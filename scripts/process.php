<?php
# ReminderManager - Cron Processing Script
# This script should be executed via system cron (e.g., every 5-15 minutes)

# MantisBT initialization
# Assuming this script is in plugins/ReminderManager/scripts/process.php
$t_core_path = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'core.php';

# Fallback for development structure in this workspace
if ( !file_exists( $t_core_path ) ) {
    $t_core_path = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . DIRECTORY_SEPARATOR . 'mantis' . DIRECTORY_SEPARATOR . 'core.php';
}

if ( !file_exists( $t_core_path ) ) {
	header( 'HTTP/1.1 500 Internal Server Error' );
	die( "MantisBT core.php not found. Please check plugin installation paths.\n" );
}

require_once( $t_core_path );

# Ensure we are running from CLI or authorized with a valid token
if ( php_sapi_name() !== 'cli' ) {
	$t_token = plugin_config_get( 'cron_token', 'NOT_SET', false, 'ReminderManager' );
	$t_web_token = gpc_get_string( 'token', '' );

	if ( $t_web_token === '' || $t_web_token !== $t_token ) {
		header( 'HTTP/1.1 403 Forbidden' );
		die( "Direct access denied. Invalid or missing security token.\n" );
	}
}

$t_rules_table = plugin_table( 'rules', 'ReminderManager' );

$t_now = time();
$t_current_time = date( 'H:i', $t_now );
$t_current_date = date( 'Y-m-d', $t_now );

$t_query = "SELECT * FROM $t_rules_table WHERE enabled = 1";
$t_result = db_query_bound( $t_query );

while ( $row = db_fetch_array( $t_result ) ) {
	# Check if it's time to run (only if current time >= scheduled time)
	if ( $t_current_time < $row['execution_time'] ) continue;
	
	# Check if already run today
	if ( !empty( $row['last_run'] ) && date( 'Y-m-d', strtotime( $row['last_run'] ) ) == $t_current_date ) continue;

	$t_cf_id = $row['custom_field_id'];

	# Corrección: Nombre exacto de la tabla de Mantis sin que db_get_table la duplique
	$t_cf_table = db_get_table( 'custom_field_string' );
	
	$t_query_bugs = "SELECT bug_id, value FROM $t_cf_table WHERE field_id = " . db_param() . " AND value <> ''";
	$t_result_bugs = db_query_bound( $t_query_bugs, array( $t_cf_id ) );

	while ( $t_bug_row = db_fetch_array( $t_result_bugs ) ) {
		$t_bug_id = $t_bug_row['bug_id'];
		$t_cf_value = $t_bug_row['value'];

		# Mantis stores Date type as Unix timestamp string
		if ( is_numeric( $t_cf_value ) && $t_cf_value <= $t_now ) {
			process_reminder( $t_bug_id, $row );
		}
	}

	# Update last_run to today
	$t_query_update = "UPDATE $t_rules_table SET last_run = " . db_param() . " WHERE id = " . db_param();
	db_query_bound( $t_query_update, array( date( 'Y-m-d H:i:s', $t_now ), $row['id'] ) );
}

function process_reminder( $p_bug_id, $p_rule_row ) {
	if ( !bug_exists( $p_bug_id ) ) return;
	
	$t_bug = bug_get( $p_bug_id );
	$t_cf_id = $p_rule_row['custom_field_id'];
	$t_template = $p_rule_row['template_body'];
	$t_template_subject = $p_rule_row['template_subject'];

	# Support for [[link]] placeholder
	$t_case_url = string_display_line( config_get( 'path' ) ) . 'view.php?id=' . $p_bug_id;

	# Pre-compilar placeholders comunes
	$t_placeholders = array(
		'[[id]]' => $p_bug_id,
		'[[summary]]' => $t_bug->summary,
		'[[status]]' => get_enum_element( 'status', $t_bug->status ),
		'[[handler]]' => ( $t_bug->handler_id > 0 ) ? user_get_name( $t_bug->handler_id ) : 'N/A',
		'[[reporter]]' => user_get_name( $t_bug->reporter_id ),
		'[[link]]' => $t_case_url
	);

	# Custom fields placeholders [[custom_field_X]]
	if ( preg_match_all( '/\[\[custom_field_(\d+)\]\]/i', $t_template . $t_template_subject, $matches ) ) {
		foreach ( array_unique($matches[1]) as $t_cf_target_id ) {
			$t_val = custom_field_get_value( $t_cf_target_id, $p_bug_id );
			$t_placeholders['[[custom_field_' . $t_cf_target_id . ']]'] = $t_val;
		}
	}

	# Aplicar reemplazos al asunto y al cuerpo
	$t_search = array_keys( $t_placeholders );
	$t_replace = array_values( $t_placeholders );

	# Corrección: str_ireplace hace que los placeholders sean insensibles a mayúsculas/minúsculas
	$t_message = str_ireplace( $t_search, $t_replace, $t_template );
	
	if ( empty( $t_template_subject ) ) {
		$t_subject = "[Recordatorio] Caso $p_bug_id: " . $t_bug->summary;
	} else {
		$t_subject = str_ireplace( $t_search, $t_replace, $t_template_subject );
	}

	# Recolección de destinatarios basada en la configuración de la regla
	$t_emails = array();
	
	if ( $p_rule_row['notify_reporter'] ) {
		$t_emails[] = user_get_email( $t_bug->reporter_id );
	}
	
	if ( $p_rule_row['notify_handler'] && $t_bug->handler_id > 0 ) {
		$t_emails[] = user_get_email( $t_bug->handler_id );
	}
	
	if ( $p_rule_row['notify_monitors'] ) {
		$t_monitors = bug_get_monitors( $p_bug_id );
		foreach ( $t_monitors as $t_user_id ) {
			$t_emails[] = user_get_email( $t_user_id );
		}
	}

	# Limpiar lista de correos
	$t_emails = array_unique( array_filter( $t_emails ) );

	if ( count( $t_emails ) > 0 ) {
		foreach ( $t_emails as $t_to ) {
			# Corrección: Encolar correo en lugar de enviarlo de golpe
			email_store( $t_to, $t_subject, $t_message );
		}

		# Registrar en la bitácora (logs)
		$t_logs_table = plugin_table( 'logs', 'ReminderManager' );
		$t_query_log = "INSERT INTO $t_logs_table ( rule_id, bug_id, recipients, subject, timestamp ) VALUES (
			" . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_now() . " )";
		db_query_bound( $t_query_log, array( 
			$p_rule_row['id'], 
			$p_bug_id, 
			implode( ', ', $t_emails ), 
			$t_subject 
		) );
	}

	# ACCIÓN CRÍTICA: Limpiar el valor del campo personalizado tras la notificación
	custom_field_set_value( $t_cf_id, $p_bug_id, '', false );
}

# CORRECCIÓN CRÍTICA: Vaciar la cola de correos de Mantis para procesos en segundo plano
if ( function_exists( 'email_send_all' ) ) {
	email_send_all();
}
