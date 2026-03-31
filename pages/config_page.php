<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

layout_page_header( plugin_lang_get( 'title' ) );
layout_page_begin();

print_manage_menu();

$t_rules_table = plugin_table( 'rules' );
$t_query = "SELECT * FROM $t_rules_table ORDER BY name ASC";
$t_result = db_query_bound( $t_query );

$t_rule_id = gpc_get_int( 'id', 0 );
$t_edit_rule = array(
	'id' => 0,
	'name' => '',
	'custom_field_id' => 0,
	'template_body' => '',
	'execution_time' => '09:00',
	'template_subject' => '',
	'enabled' => 1,
	'notify_reporter' => 1,
	'notify_handler' => 1,
	'notify_monitors' => 1
);

if ( $t_rule_id > 0 ) {
	$t_query_edit = "SELECT * FROM $t_rules_table WHERE id = " . db_param();
	$t_result_edit = db_query_bound( $t_query_edit, array( $t_rule_id ) );
	if ( db_num_rows( $t_result_edit ) > 0 ) {
		$t_edit_rule = db_fetch_array( $t_result_edit );
	}
}
?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>

    <div class="widget-box widget-color-blue2">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-info-circle"></i>
                <?php echo plugin_lang_get( 'cron_instructions' ) ?>
            </h4>
        </div>
        <div class="widget-body">
            <div class="widget-main">
                <p><?php echo plugin_lang_get( 'cron_hint' ) ?></p>
                <div class="well well-sm">
                    <strong>CLI (Recomendado):</strong><br />
                    <code><?php echo sprintf( plugin_lang_get( 'cron_command' ), dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'process.php' ) ?></code>
                    <br /><br />
                    <strong>HTTP (Web):</strong><br />
                    <code><?php echo string_display_line( config_get( 'path' ) ) . 'plugins/ReminderManager/scripts/process.php?token=' . plugin_config_get( 'cron_token' ) ?></code>
                </div>
            </div>
        </div>
    </div>

    <div class="space-10"></div>

    <div class="widget-box widget-color-blue2">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-list"></i>
                <?php echo plugin_lang_get( 'rules_list' ) ?>
            </h4>
        </div>

        <div class="widget-body">
            <div class="widget-main no-padding">
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-striped">
                        <thead>
                            <tr class="row-category">
                                <th><?php echo plugin_lang_get( 'name' ) ?></th>
                                <th><?php echo plugin_lang_get( 'custom_field' ) ?></th>
                                <th><?php echo plugin_lang_get( 'execution_time' ) ?></th>
                                <th class="center"><?php echo plugin_lang_get( 'enabled' ) ?></th>
                                <th class="center"><?php echo plugin_lang_get( 'edit_rule' ) ?></th>
                                <th class="center"><?php echo plugin_lang_get( 'delete' ) ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ( $row = db_fetch_array( $t_result ) ) { ?>
                                <tr>
                                    <td><?php echo string_display_line( $row['name'] ) ?></td>
                                    <td><?php echo string_display_line( custom_field_get_field( $row['custom_field_id'], 'name' ) ) ?></td>
                                    <td><?php echo string_display_line( $row['execution_time'] ) ?></td>
                                    <td class="center"><?php echo $row['enabled'] ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>' ?></td>
                                    <td class="center">
                                        <a class="btn btn-xs btn-primary btn-white btn-round" href="<?php echo plugin_page( 'config_page&id=' . $row['id'] ) ?>">
                                            <i class="fa fa-pencil"></i> <?php echo plugin_lang_get( 'edit_rule' ) ?>
                                        </a>
                                    </td>
                                    <td class="center">
                                        <a class="btn btn-xs btn-danger btn-white btn-round" href="<?php echo plugin_page( 'config_edit&action=delete&id=' . $row['id'] ) . form_security_param( 'plugin_ReminderManager_delete' ) ?>">
                                            <i class="fa fa-trash"></i> <?php echo plugin_lang_get( 'delete' ) ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="space-20"></div>

    <div class="widget-box widget-color-blue2">
        <div class="widget-header widget-header-small">
            <h4 class="widget-title lighter">
                <i class="ace-icon fa fa-edit"></i>
                <?php echo ( $t_rule_id > 0 ) ? plugin_lang_get( 'edit_rule' ) : plugin_lang_get( 'add_rule' ) ?>
            </h4>
        </div>

        <div class="widget-body">
            <form action="<?php echo plugin_page( 'config_edit' ) ?>" method="post">
                <div class="widget-main no-padding">
                    <?php echo form_security_field( 'plugin_ReminderManager_config_edit' ) ?>
                    <input type="hidden" name="id" value="<?php echo $t_edit_rule['id'] ?>" />
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-condensed table-striped">
                            <tr>
                                <td class="category" width="25%"><?php echo plugin_lang_get( 'name' ) ?></td>
                                <td><input type="text" name="name" size="50" class="input-sm" value="<?php echo string_attribute( $t_edit_rule['name'] ) ?>" required /></td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'custom_field' ) ?></td>
                                <td>
                                    <select name="custom_field_id" class="input-sm">
                                        <?php
                                        $t_cf_ids = custom_field_get_ids();
                                        foreach ( $t_cf_ids as $t_id ) {
                                            $t_def = custom_field_get_definition( $t_id );
                                            if ( $t_def['type'] == CUSTOM_FIELD_TYPE_DATE ) {
                                                $t_selected = ( $t_id == $t_edit_rule['custom_field_id'] ) ? 'selected' : '';
                                                echo '<option value="' . $t_id . '" ' . $t_selected . '>' . string_display_line( $t_def['name'] ) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'execution_time' ) ?></td>
                                <td><input type="text" name="execution_time" size="10" placeholder="09:00" class="input-sm" value="<?php echo string_attribute( $t_edit_rule['execution_time'] ) ?>" required /></td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'template_subject' ) ?></td>
                                <td><input type="text" name="template_subject" size="100" class="input-sm" value="<?php echo string_attribute( $t_edit_rule['template_subject'] ) ?>" placeholder="Recordatorio Caso [[id]] - [[summary]]" /></td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'template' ) ?></td>
                                <td>
                                    <textarea name="template_body" class="form-control" rows="8" required><?php echo string_textarea( $t_edit_rule['template_body'] ) ?></textarea>
                                    <span class="help-block"><?php echo plugin_lang_get( 'placeholder_hint' ) ?> | [[link]] (<?php echo plugin_lang_get( 'placeholder_link' ) ?>)</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'notify_reporter' ) ?> / <?php echo plugin_lang_get( 'notify_handler' ) ?> / <?php echo plugin_lang_get( 'notify_monitors' ) ?></td>
                                <td>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="notify_reporter" class="ace" <?php echo $t_edit_rule['notify_reporter'] ? 'checked' : '' ?> />
                                        <span class="lbl"> <?php echo plugin_lang_get( 'notify_reporter' ) ?></span>
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="notify_handler" class="ace" <?php echo $t_edit_rule['notify_handler'] ? 'checked' : '' ?> />
                                        <span class="lbl"> <?php echo plugin_lang_get( 'notify_handler' ) ?></span>
                                    </label>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="notify_monitors" class="ace" <?php echo $t_edit_rule['notify_monitors'] ? 'checked' : '' ?> />
                                        <span class="lbl"> <?php echo plugin_lang_get( 'notify_monitors' ) ?></span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="category"><?php echo plugin_lang_get( 'enabled' ) ?></td>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enabled" class="ace" <?php echo $t_edit_rule['enabled'] ? 'checked' : '' ?> />
                                        <span class="lbl"></span>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="widget-toolbox padding-8 clearfix">
                    <input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo plugin_lang_get( 'save' ) ?>" />
                </div>
            </form>
        </div>
    </div>
</div>

<?php
layout_page_end();
