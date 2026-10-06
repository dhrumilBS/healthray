<?php

function register_handl_utm_grabber_gdpr_variables() {
	register_setting( 'handl-utm-grabber-gdpr-variables-group', 'handl_gdpr_plugins' );
}
add_action( 'admin_init', 'register_handl_utm_grabber_gdpr_variables' );


function add_handl_gdpr_variables_to_tabs($tabs){
	array_push($tabs, array( 'gdpr-variables' => __( 'GDPR', 'handlutmgrabber' ) ) );
	return $tabs;
}
add_filter('filter_admin_tabs','add_handl_gdpr_variables_to_tabs', 10, 1);


function getHandLGDPRPlugins(){
	return get_option( 'handl_gdpr_plugins' ) ? get_option( 'handl_gdpr_plugins' ) : array();
}

function getHandLGDPRContent(){
	global $handl_fields_disabled;
	$GDPRParams = getHandLGDPRPlugins();
    $activeGDPRPlugins = getAllGDPRPlugins();
	?>
	<form method='post' action='options.php'>
		<?php settings_fields( 'handl-utm-grabber-gdpr-variables-group' ); ?>
		<?php do_settings_sections( 'handl-utm-grabber-gdpr-variables-group' ); ?>
		<?php do_action('maybe_dispay_license_error_notice'); ?>
		<h2>What is this?</h2>
		<p class="description">If you are using any GDPR plugin, you will be able to allow/disallow such plugins to intercept HandL UTM Grabber</p>
		<table class='form-table'>
            <?php
                foreach ($activeGDPRPlugins as $plugin_dir => $activePlugin):
                    $checkbox_checked = isset($GDPRParams[$plugin_dir])  && $GDPRParams[$plugin_dir] ? 'checked="checked"' : '';
                    ?>
            <tr>
                <th scope='row'><?php print $activePlugin['Name'];?></th>
                <td>
                    <fieldset>
                        <legend class='screen-reader-text'>
                            <span><?php print $activePlugin['Name'];?></span>
                        </legend>
                        <label for='handl_gdpr'>
                            <input name='handl_gdpr_plugins[<?php print $plugin_dir; ?>]' value="1" type='checkbox' <?php print $checkbox_checked;?>  <?php print $handl_fields_disabled;?> />
                        </label>
                        <p class="description">Check the box if you want HandL UTM Grabber to wait for user consent.</p>
                    </fieldset>
                </td>
            </tr>
            <?php endforeach;?>
		</table>

		<?php submit_button(null, 'primary', 'submit', true, $handl_fields_disabled); ?>
	</form>
	<?php
}
add_filter( 'get_admin_tab_content_gdpr-variables', 'getHandLGDPRContent', 10 );


function getAllGDPRPlugins(){
    $plugins = [];
    $plugins = apply_filters('handl_gdpr_add_plugin_support',$plugins);

    $res_plugins = [];
    foreach ($plugins as $plugin){
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR."/".$plugin);
	    $res_plugins[$plugin] = $plugin_data;
    }
    return $res_plugins;
}

function getHandLGDPRPluginStatus($plugin){
	$GDPRParams = getHandLGDPRPlugins();
//    print "<pre>";
//    print_r($GDPRParams[$plugin]);
	return isset($GDPRParams[$plugin])  && $GDPRParams[$plugin] ? true : false;
}
