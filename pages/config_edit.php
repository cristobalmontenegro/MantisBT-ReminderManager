<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$t_action = gpc_get_string( 'action', 'save' );
$t_id = gpc_get_int( 'id', 0 );
$t_rules_table = plugin_table( 'rules' );

if ( $t_action == 'delete' ) {
	form_security_validate( 'plugin_ReminderManager_delete' );
	$t_query = "DELETE FROM $t_rules_table WHERE id = " . db_param();
	db_query_bound( $t_query, array( $t_id ) );
} else {
	form_security_validate( 'plugin_ReminderManager_config_edit' );
	$t_name = gpc_get_string( 'name' );
	$t_cf_id = gpc_get_int( 'custom_field_id' );
	$t_exec_time = gpc_get_string( 'execution_time' );
	$t_template_subject = gpc_get_string( 'template_subject', '' );
	$t_template = gpc_get_string( 'template_body' );
	$t_enabled = gpc_get_bool( 'enabled', false ) ? 1 : 0;
	$t_notify_reporter = gpc_get_bool( 'notify_reporter', false ) ? 1 : 0;
	$t_notify_handler = gpc_get_bool( 'notify_handler', false ) ? 1 : 0;
	$t_notify_monitors = gpc_get_bool( 'notify_monitors', false ) ? 1 : 0;

	# Validate execution_time format (HH:MM)
	if ( !preg_match( '/^(?:[01]\d|2[0-3]):[0-5]\d$/', $t_exec_time ) ) {
		trigger_error( ERROR_GENERIC, ERROR );
	}

	# Validate custom field type (must be Date)
	$t_def = custom_field_get_definition( $t_cf_id );
	if ( !$t_def || $t_def['type'] != CUSTOM_FIELD_TYPE_DATE ) {
		trigger_error( ERROR_CUSTOM_FIELD_INVALID_DEFINITION, ERROR );
	}

	if ( $t_id > 0 ) {
		$t_query = "UPDATE $t_rules_table SET 
			name = " . db_param() . ", 
			custom_field_id = " . db_param() . ", 
			execution_time = " . db_param() . ", 
			template_subject = " . db_param() . ", 
			template_body = " . db_param() . ", 
			enabled = " . db_param() . ",
			notify_reporter = " . db_param() . ",
			notify_handler = " . db_param() . ",
			notify_monitors = " . db_param() . "
			WHERE id = " . db_param();
		db_query_bound( $t_query, array( 
			$t_name, $t_cf_id, $t_exec_time, $t_template_subject, $t_template, $t_enabled, 
			$t_notify_reporter, $t_notify_handler, $t_notify_monitors, $t_id 
		) );
	} else {
		$t_query = "INSERT INTO $t_rules_table (name, custom_field_id, execution_time, template_subject, template_body, enabled, notify_reporter, notify_handler, notify_monitors) VALUES (
			" . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ", " . db_param() . ")";
		db_query_bound( $t_query, array( 
			$t_name, $t_cf_id, $t_exec_time, $t_template_subject, $t_template, $t_enabled, 
			$t_notify_reporter, $t_notify_handler, $t_notify_monitors 
		) );
	}
}

print_successful_redirect( plugin_page( 'config_page', true ) );
