<?php
/**
 * Plugin ReminderManager
 * 
 * Este plugin permite automatizar el envío de notificaciones por correo electrónico
 * basándose en la fecha configurada en cualquier campo personalizado de tipo fecha.
 * 
 * @package ReminderManager
 * @version 2.0.1
 * @author Cristobal Montenegro
 * @link github.com/cristobalmontenegro
 */

require_once( config_get( 'class_path' ) . 'MantisPlugin.class.php' );

class ReminderManagerPlugin extends MantisPlugin {
	/**
	 * Registro de metadatos básicos del plugin.
	 */
	function register() {
		$this->name = 'Reminder Manager';
		$this->description = 'Envía recordatorios de correo basados en campos personalizados de fecha.';
		$this->page = 'config_page';
		$this->version = '2.1.0';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);
		$this->author = 'Cristobal Montenegro';
		$this->contact = 'https://github.com/cristobalmontenegro';
		$this->url = 'https://github.com/cristobalmontenegro';
	}

	/**
	 * Configuración por defecto.
	 */
	function config() {
		return array(
			'cron_token' => 'SECRET_TOKEN_REPLACE_ME_9f8e7d6c5b4a3928172635445566778899aabbccddeeff',
		);
	}

	/**
	 * Definición del esquema de base de datos.
	 * Crea la tabla de reglas necesaria para almacenar la configuración de las notificaciones.
	 */
	function schema() {
		return array(
			array( 'CreateTableSQL', array( plugin_table( 'rules' ), "
				id                 I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				name               C(255)  NOTNULL,
				custom_field_id    I       NOTNULL,
				template_body      X       NOTNULL,
				execution_time     C(5)    NOTNULL DEFAULT '09:00',
				enabled            L       NOTNULL DEFAULT '1',
				last_run           T       DEFAULT NULL
			" ) ),
			# Versión 2.1.0: Nuevos campos de personalización y tabla de bitácora
			array( 'AddColumnSQL', array( plugin_table( 'rules' ), "
				template_subject   C(255)  DEFAULT '',
				notify_reporter    L       NOTNULL DEFAULT '1',
				notify_handler     L       NOTNULL DEFAULT '1',
				notify_monitors    L       NOTNULL DEFAULT '1'
			" ) ),
			array( 'CreateTableSQL', array( plugin_table( 'logs' ), "
				id                 I       NOTNULL UNSIGNED AUTOINCREMENT PRIMARY,
				rule_id            I       NOTNULL,
				bug_id             I       NOTNULL,
				recipients         C(255)  NOTNULL,
				subject            C(255)  NOTNULL,
				timestamp          T       
			" ) ),
		);
	}
}
