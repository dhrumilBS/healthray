<?php 
/**
 * plugin settings class
 */

 class NB_CPF_settings {

    /**
     * plugin class costruct 
    */

    public function __construct(){
        
        add_action('admin_menu', array($this, 'nb_cpf_setting_menu'));
        /**
		 * register our tb_sub_box_settings_init to the admin_init action hook
		*/
		add_action( 'admin_init',  array($this, 'nb_cpf_settings_options'));
    }

    public function nb_cpf_setting_menu(){

        add_submenu_page( 'wpcf7', __('Country and Phone field settings', 'nb-cpf'), 
    __('CPF Settings', 'nb-cpf'), 'manage_options', 'cpf-settings', array( $this, 'cpf_setting_tabs' ) );

    }

    public function cpf_setting_tabs(){
        // check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
        }
    ?>
        <div class="wrap full-width-layout np_cpf_settings_page">
		    <h2><?php _e('Country and phone field settings', 'nb-cpf'); ?></h2>
			<?php settings_errors(); ?>
            <?php $this->nb_cpf_settings_admin_tabs(); ?>
            <?php if ( isset ( $_GET['tab'] ) && esc_attr($_GET['tab']) == 'help' ) : ?>
            <h2><?php _e('Field settings', 'nb-cpf') ?></h2>
            <p>
                <strong><?php _e( 'Default Country', 'nb-cpf' ); ?>:</strong>
                <?php _e( 'Enter country 2 character ISO code. Default country will selected default country in dropdown.' ) ?>
            </p>
            <p>
                <strong><?php _e( 'Include Countries', 'nb-cpf' ); ?>:</strong>
                <?php _e( 'Enter Country 2 character ISO code. Only selected counries will display in dropdown. Kindly enter comma separted countries code.' ) ?>
            </p>
            <p>
                <strong><?php _e( 'Exclude  Countries', 'nb-cpf' ); ?>:</strong>
                <?php _e( 'Enter Country 2 character ISO code. Countries will excluded from the dropdown. Kindly enter comma separted countries code.') ?>
            <p>
                <strong><?php _e( 'Preferred Countries', 'nb-cpf' ); ?>:</strong>
                <?php _e( 'Enter Country 2 character ISO code. Countries will display at top the dropdown. Kindly enter comma separted countries code.' ) ?>
            </p>


            <?php else: ?>
                <form method="post" action="options.php">
				<?php 
					settings_fields( 'nb_cpf_settings' );
					do_settings_sections( 'nb_cpf_settings' );
					// output save settings button
					submit_button( __('Save Settings', 'nb-cpf') ); 
				?>
			</form>
            <?php endif; ?>
        </div>
    <?php
    }

    private function nb_cpf_settings_admin_tabs() {
		$tabs = array( 
		
		'country'   => __('Country Field Settings', 'nb-cpf'), 
		'phone' => __('Phone Field Settings', 'nb-cpf'),
		//'ip_api' => __('IP API Settings', 'nb-cpf'),
		'help' => __('Documentation', 'nb-cpf'),
		);
		$links = array();
		if ( isset ( $_GET['tab'] ) ) :
			$current = esc_attr($_GET['tab']);
		else:
			$current = 'country';
		endif;
		foreach( $tabs as $tab => $name ) :
			if ( $tab == $current ) :
				$links[] = '<a class="nav-tab nav-tab-active" href="?page=cpf-settings&tab='.$tab.'">'.$name.'</a>';
			else :
				$links[] = '<a class="nav-tab" href="?page=cpf-settings&tab='.$tab.'">'.$name.'</a>';
			endif;
		endforeach;
		echo '<div id="icon-themes" class="icon32"><br></div>';
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $links as $link )
			echo $link;
		echo '</h2>';
    }
	
	public function nb_cpf_field_sanitize($input){
		// for enhanced security, create a new empty array
		$valid_input = array();
		foreach($input as $key => $input_val){
			
			// if it's not set, default to null!
			if($key == 'ip_api_key'){
				$input[$key] = sanitize_text_field(strip_tags(trim($input_val))); 
				// need to add slashes still before sending to the database
				$valid_input[$key] = addslashes($input[$key]);
			}
			elseif ($key == 'phone_nationalMode' || $key == 'phone_auto_select' || $key == 'country_auto_select') {
				// Our checkbox value is either 0 or 1
				$valid_input[$key] = ( $input[$key] == 1 ? 1 : 0 );
				
			} else {
				$reg_exp = '/^([a-zA-Z]{2}+,)+$/';
				//$input[$key] = sanitize_text_field(strip_tags(trim($input_val))); 
				// register error
				if($input[$key] != '' && ( ! preg_match($reg_exp,$input[$key].',') )) {
					
					add_settings_error(
						'nb_cpf_settings_error', // setting title
						'nb_cpf_text_field_error', // error ID
						__('Countries code must two character length and comma separated.
						Wrong Value: '.esc_attr($input[$key]),'nb-cpf'), // error message
						'error' // type of message
					);
					//return false;
				} else {
					$input[$key] = sanitize_text_field(strip_tags(trim($input_val))); 
					// need to add slashes still before sending to the database
					$valid_input[$key] = addslashes($input[$key]);
				}
			}
		}
		//print_r($valid_input); exit;
		return $valid_input;
	}
    
    public function nb_cpf_settings_options(){
        
		// register a new setting for "nb_cpf_settings" page
		$args = array(
			'sanitize_callback' => array( $this, 'nb_cpf_field_sanitize' ),
		);
		register_setting('nb_cpf_settings', 'nb_cpf_options', $args );
		
		if ( isset ( $_GET['tab'] ) ) :
			$current_tab = esc_attr($_GET['tab']);
		else:
			$current_tab = 'country';
        endif;
        
        if($current_tab == 'country'){

            add_settings_section('nb_cpf_country_section', __( 'Country Options Settings.', 'nb-cpf' ), array($this , 'nb_cpf_country_section_cb'), 'nb_cpf_settings');
			
			add_settings_field('defaultCountry', 
				__( 'Default Country', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_country_section',
				['label_for' => 'defaultCountry', 
				'class' => 'regular-text', 
				'wporg_custom_data' => 'custom',]
			);
            
            add_settings_field('onlyCountries', 
				__( 'Include Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_country_section',
				['label_for' => 'onlyCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
            );

            add_settings_field('excludeCountries', 
				__( 'Exclude Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_country_section',
				['label_for' => 'excludeCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
			);
            
            add_settings_field('preferredCountries', 
				__( 'Preferred Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_country_section',
				['label_for' => 'preferredCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
			);
			
			add_settings_field('country_auto_select', 
				__( 'Enable Auto Country Select', 'nb-cpf' ), 
				array($this , 'nb_cpf_checkbox_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_country_section',
				['label_for' => 'country_auto_select', 
				'class' => 'checkbox-button', 
				'description' => __( 'After enable this check box. The country dropdown will selected base on your computer IP Address. If default country not selected.', 'nb-cpf' ),
				'wporg_custom_data' => 'custom',]
			);
        }
        if($current_tab == 'phone'){

            add_settings_section('nb_cpf_phone_section', __( 'Phone Field Settings.', 'nb-cpf' ), array($this , 'nb_cpf_phone_section_cb'), 'nb_cpf_settings');
			
			add_settings_field('phone_defaultCountry', 
				__( 'Default Country', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_defaultCountry', 
				'class' => 'regular-text', 
				'wporg_custom_data' => 'custom',]
			);
            
            add_settings_field('phone_onlyCountries', 
				__( 'Include Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_onlyCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
            );

            add_settings_field('phone_excludeCountries', 
				__( 'Exclude Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_excludeCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
			);
            
            add_settings_field('phone_preferredCountries', 
				__( 'Preferred Countries', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_preferredCountries', 
				'class' => 'large-text', 
				'wporg_custom_data' => 'custom',]
			);
			
			add_settings_field('phone_nationalMode', 
				__( 'Disable International dial codes', 'nb-cpf' ), 
				array($this , 'nb_cpf_checkbox_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_nationalMode', 
				'class' => 'checkbox-button', 
				'description' => __( 'Disable phone number dial code. After checkbox enable. Dial code will not visible', 'nb-cpf' ),
				'wporg_custom_data' => 'custom',]
			);
			
			add_settings_field('phone_auto_select', 
				__( 'Enable Auto Dial Code Select', 'nb-cpf' ), 
				array($this , 'nb_cpf_checkbox_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_phone_section',
				['label_for' => 'phone_auto_select', 
				'class' => 'checkbox-button', 
				'description' => __( 'After enable this check box. The phone number dial code will selected base on your computer IP Address. If default country not selected.', 'nb-cpf' ),
				'wporg_custom_data' => 'custom',]
			);
			
        }
		if($current_tab == 'ip_api'){

			add_settings_section('nb_cpf_api_section', __( 'IP API Settings.', 'nb-cpf' ), array($this , 'nb_cpf_api_section'), 'nb_cpf_settings');

			add_settings_field('ip_api_key', 
				__( 'Add you own API Key', 'nb-cpf' ), 
				array($this , 'nb_cpf_text_fields_cb'), 
				'nb_cpf_settings', 
				'nb_cpf_api_section',
				['label_for' => 'ip_api_key', 
				'class' => 'large-text', 
				'description' => __( 'This is API key for IP auto-country detection. Please register to get free API. (<a href="https://ipgeolocation.io/" target="_blank">https://ipgeolocation.io/</a>) ', 'nb-cpf' ),
				'wporg_custom_data' => 'custom',]
			);
			

		}
    }
	
	public function nb_cpf_checkbox_fields_cb($args){ $options = get_option( 'nb_cpf_options' );
	   ?>
			<input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="nb_cpf_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="checkbox"  class="<?php echo esc_attr( $args['class'] ); ?>" value="1" <?php isset( $options[ $args['label_for'] ] ) ? checked(1, esc_attr($options[ $args['label_for'] ]), true) : ''; ?>/> <label>Yes</label>
			
			<br/>
			<p><?php echo $args['description'] ?></p>
	   <?php
	}

    public function nb_cpf_text_fields_cb($args){
        $options = get_option( 'nb_cpf_options' );
	?>
		<input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="nb_cpf_options[<?php echo esc_attr( $args['label_for'] ); ?>]" type="text"  class="<?php echo esc_attr( $args['class'] ); ?>" value="<?php echo isset( $options[ $args['label_for'] ] ) ? esc_attr($options[ $args['label_for'] ]) : ''; ?>" />
		<br/>
		<p><?php echo isset($args['description']) ? $args['description'] : '' ?></p>
	<?php 
    }

    public function nb_cpf_country_section_cb( $args ) {
		$options = get_option( 'nb_cpf_options' );
	?>
		<input type="hidden" name="nb_cpf_options[phone_defaultCountry]" value="<?php echo isset( $options['phone_defaultCountry'] ) ? esc_attr($options['phone_defaultCountry']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_onlyCountries]" value="<?php echo isset( $options['phone_onlyCountries'] ) ? esc_attr($options['phone_onlyCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_excludeCountries]" value="<?php echo isset( $options['phone_excludeCountries'] ) ? esc_attr($options['phone_excludeCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_preferredCountries]" value="<?php echo isset( $options['phone_preferredCountries'] ) ? esc_attr($options['phone_preferredCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_nationalMode]" value="<?php echo isset( $options['phone_nationalMode'] ) ? esc_attr($options['phone_nationalMode']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_auto_select]" value="<?php echo isset( $options['phone_auto_select'] ) ? esc_attr($options['phone_auto_select']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[ip_api_key]" value="<?php echo isset( $options['ip_api_key'] ) ? esc_attr($options['ip_api_key']) : ''; ?>" />
		
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'Country dropdown field settings.', 'nb-cpf' ); ?>
		</p>
		
	<?php
	}

    public function nb_cpf_phone_section_cb( $args ){
        $options = get_option( 'nb_cpf_options' );
	?>
		<input type="hidden" name="nb_cpf_options[defaultCountry]" value="<?php echo isset( $options['defaultCountry'] ) ? esc_attr($options['defaultCountry']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[onlyCountries]" value="<?php echo isset( $options['onlyCountries'] ) ? esc_attr($options['onlyCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[excludeCountries]" value="<?php echo isset( $options['excludeCountries'] ) ? esc_attr($options['excludeCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[preferredCountries]" value="<?php echo isset( $options['preferredCountries'] ) ? esc_attr($options['preferredCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[country_auto_select]" value="<?php echo isset( $options['country_auto_select'] ) ? esc_attr($options['country_auto_select']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[ip_api_key]" value="<?php echo isset( $options['ip_api_key'] ) ? esc_attr($options['ip_api_key']) : ''; ?>" />
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'Phone field dropdown settings.', 'nb-cpf' ); ?>
		</p>
		
	<?php
    }

	public function nb_cpf_api_section( $args ){
        $options = get_option( 'nb_cpf_options' );
	?>
		<input type="hidden" name="nb_cpf_options[phone_defaultCountry]" value="<?php echo isset( $options['phone_defaultCountry'] ) ? esc_attr($options['phone_defaultCountry']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_onlyCountries]" value="<?php echo isset( $options['phone_onlyCountries'] ) ? esc_attr($options['phone_onlyCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_excludeCountries]" value="<?php echo isset( $options['phone_excludeCountries'] ) ? esc_attr($options['phone_excludeCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_preferredCountries]" value="<?php echo isset( $options['phone_preferredCountries'] ) ? esc_attr($options['phone_preferredCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_nationalMode]" value="<?php echo isset( $options['phone_nationalMode'] ) ? esc_attr($options['phone_nationalMode']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[phone_auto_select]" value="<?php echo isset( $options['phone_auto_select'] ) ? esc_attr($options['phone_auto_select']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[defaultCountry]" value="<?php echo isset( $options['defaultCountry'] ) ? esc_attr($options['defaultCountry']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[onlyCountries]" value="<?php echo isset( $options['onlyCountries'] ) ? esc_attr($options['onlyCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[excludeCountries]" value="<?php echo isset( $options['excludeCountries'] ) ? esc_attr($options['excludeCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[preferredCountries]" value="<?php echo isset( $options['preferredCountries'] ) ? esc_attr($options['preferredCountries']) : ''; ?>" />
		<input type="hidden" name="nb_cpf_options[country_auto_select]" value="<?php echo isset( $options['country_auto_select'] ) ? esc_attr($options['country_auto_select']) : ''; ?>" />
		
		<p id="<?php echo esc_attr( $args['id'] ); ?>">
			<?php esc_html_e( 'IP API settings.', 'nb-cpf' ); ?>
		</p>
		
	<?php
    }

}

new NB_CPF_settings;