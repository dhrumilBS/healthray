<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.itpathsolutions.com/
 * @since      1.0.0
 *
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cf7_To_Any_Api
 * @subpackage Cf7_To_Any_Api/admin
 * @author     IT Path Solution <info@itpathsolutions.com>
 */
class Cf7_To_Any_Api_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_footer', array( $this, '_cf7_api_deactivation_feedback_popup' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_To_Any_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_To_Any_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cf7-to-any-api-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cf7_To_Any_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cf7_To_Any_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$data = array(
	        'cf7_to_any_api_site_url' => site_url(),
	        'cf7_to_any_api_ajax_url' => admin_url('admin-ajax.php'),
	    );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-to-any-api-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'cf7_to_any_api_ajax_object', $data);

	}

	/**
	 * Check Plugin Dependencies
	 *
	 * @since    1.0.0
	 */
	public function cf7_to_any_api_verify_dependencies(){
		if(is_multisite()){
			if(!is_plugin_active_for_network('contact-form-7/wp-contact-form-7.php') && !is_plugin_active('contact-form-7/wp-contact-form-7.php')){

	         	echo '<div class="notice notice-warning is-dismissible"><p>'.esc_html__( 'Contact form 7 API integrations requires CONTACT FORM 7 Plugin to be installed and active', 'contact-form-to-any-api' ).'</p></div>';
			}
		}else{
			if(!is_plugin_active('contact-form-7/wp-contact-form-7.php')){	         	
	         	echo '<div class="notice notice-warning is-dismissible">
			         <p>' . esc_html__( 'Contact form 7 API integrations requires CONTACT FORM 7 Plugin to be installed and active', 'contact-form-to-any-api' ) . '</p>
			      </div>';
    		}
    	}
    	
    	if ( is_plugin_active( CF7_TO_ANY_API_PLUGIN_BASENAME )) { 
			$screen = get_current_screen();
			if( $screen->post_type == 'cf7_to_any_api'){
		    	echo '<div class="cf7anyapi-notice-bar">
				<span>You’re using Contact Form to Any API. To unlock more features, consider<a href="'.CF7_CURL_DOMAIN.'/pricing/" target="_blank"> Upgrading to Pro</a></span>
				<button type="button" class="cf7anyapi-close-btn"> <svg width="12" height="12" viewBox="0 0 12 12"> <line x1="1" y1="1" x2="11" y2="11" stroke="currentColor" stroke-width="2"/> <line x1="11" y1="1" x2="1" y2="11" stroke="currentColor" stroke-width="2"/> </svg> </button>
				</div>';
			}
	 	}

	}

	/**
	 * Register the Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_custom_post_type(){
		$supports = array(
			'title', 
		);
		$labels = array(
			'name' => _x('All API Integrations', 'plural', 'contact-form-to-any-api'),
			'singular_name' => _x('All API Integrations', 'singular', 'contact-form-to-any-api'),
			'menu_name' => _x('CF7 to Any API', 'admin menu', 'contact-form-to-any-api'),
			'name_admin_bar' => _x('CF7 to Any API', 'admin bar', 'contact-form-to-any-api'),
			'add_new' => _x('Add New Integration', 'add new', 'contact-form-to-any-api'),
			'add_new_item' => __('Add New Integration', 'contact-form-to-any-api'),
			'new_item' => __('New API Integrations', 'contact-form-to-any-api'),
			'edit_item' => __('Edit API Integrations', 'contact-form-to-any-api'),
			'view_item' => __('View API Integrations', 'contact-form-to-any-api'),
			'all_items' => __('All API Integrations', 'contact-form-to-any-api'),
			'not_found' => __('No API Integrations found.', 'contact-form-to-any-api'),
			'register_meta_box_cb' => 'aps_metabox',
		);
		$args = array(
			'supports' => $supports,
			'labels' => $labels,
			'hierarchical' => false,
			'public' => false,  
			'publicly_queryable' => false, 
			'show_ui' => true,  
			'exclude_from_search' => true,
			'show_in_nav_menus' => false, 
			'has_archive' => false,  
			'rewrite' => false,  
			'menu_icon' => 'dashicons-smiley',
			'menu_position' => 31,
		);
		register_post_type('cf7_to_any_api', $args);		
	}

	/**
	 * Register the Submenu for the CF7 to Any API
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_register_submenu(){
		global $submenu;
		$cf7anyapi_setting_options = Cf7_To_Any_Api::setting_get_options();

		if($cf7anyapi_setting_options['cf7_to_api_log_hide'] != true){
		    add_submenu_page(
		        'edit.php?post_type=cf7_to_any_api',
		        __('API Logs', 'contact-form-to-any-api'),
		        __('API Logs', 'contact-form-to-any-api'),
		        'manage_options',
		        'cf7anyapi_logs',
		        array(&$this,'cf7anyapi_submenu_callback')
		    );
		}

		if($cf7anyapi_setting_options['cf7_to_api_entry_hide'] != true){
			add_submenu_page(
		        'edit.php?post_type=cf7_to_any_api',
		        __('Form Entries', 'contact-form-to-any-api'),
		        __('Form Entries', 'contact-form-to-any-api'),
		        'manage_options',
		        'cf7anyapi_entries',
		        array(&$this,'cf7anyapi_entries_callback')
		    );
		}

	    add_submenu_page(
	        'edit.php?post_type=cf7_to_any_api',
	        __('Settings', 'contact-form-to-any-api'),
	        __('Settings', 'contact-form-to-any-api'),
	        'manage_options',
	        'cf7anyapi_setting',
	        array(&$this,'cf7anyapi_hook_settings')
	    );

	    add_submenu_page(
	        'edit.php?post_type=cf7_to_any_api',
	        __('Documentation', 'contact-form-to-any-api'),
	        __('Documentation', 'contact-form-to-any-api'),
	        'manage_options',
	        'cf7anyapi_docs',
	        array(&$this,'cf7anyapi_submenu_docs_callback')
	    );

	    add_submenu_page(
			'edit.php?post_type=cf7_to_any_api',
			__('Upgrade to Pro', 'contact-form-to-any-api'),
			__('Upgrade to Pro', 'contact-form-to-any-api'),
			'manage_options',
			'cf7anyapi_upgrade_to_pro',
			''  // no callback
		);	 

	    $parent = 'edit.php?post_type=cf7_to_any_api';
	    if (!isset($submenu[$parent])) return;
	    foreach ($submenu[$parent] as $key => $item) {
	        if ($item[2] === 'cf7anyapi_upgrade_to_pro') {
	            $submenu[$parent][$key][2] = CF7_CURL_DOMAIN . '/pricing/';
	        }
	    }
	}

	/**
	 * Register Submenu Callback Function
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_submenu_callback(){
		$myListTable = new cf7anyapi_List_Table();
	  	echo '<div class="wrap"><h2>' . esc_html__( 'Contact Form API Submission Logs', 'contact-form-to-any-api' ) . '</h2>';
			echo wp_kses_post( wp_nonce_field('cf_to_any_api_log_del_nonce','cf_to_any_api_log_del_nonce' ) );
	  		echo '<div id="cf7anyapi-log-popup"><span class="close-popup">X</span><div class="cf7anyapi-log-content"><pre></pre></div></div>';
	  		$myListTable->prepare_items();
	  		$myListTable->display(); 
	  	echo '</div>';
	}

	/**
	 * Registered Entries Fields
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_entries_callback(){
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-entries.php';
	 }

	 /**
	 * Registered Settings Fields option
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_hook_settings() {
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-setting.php';
	}

	/**
	 * CF7 API Documentation tab
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_submenu_docs_callback(){
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-display-docs.php';
	}

	/**
	 * Register the Custom Meta Boxes
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_metabox(){
	    add_meta_box(
	        'cf7anyapi-setting',
	        __( 'CF7 API Integration Settings', 'contact-form-to-any-api' ),
	        array($this,'cf7anyapi_settings'),
	        'cf7_to_any_api'
	    );
	}
	
	/**
	 * Update the Metaboxes value on Post Save
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_update_settings($cf7_to_any_api_id,$cf7_to_any_api){
		if($cf7_to_any_api->post_type == 'cf7_to_any_api'){
			$status = 'false';
			if(isset($_POST['cf7_to_any_api_cpt_nonce']) && wp_verify_nonce(sanitize_text_field( wp_unslash( $_POST['cf7_to_any_api_cpt_nonce'] ) ), 'cf7_to_any_api_cpt_nonce')){
				if (isset($_POST['cf7anyapi_selected_form'])) {
					$options['cf7anyapi_selected_form'] = (int)sanitize_text_field( wp_unslash( $_POST['cf7anyapi_selected_form']) );
				}
				if (isset($_POST['cf7anyapi_base_url'])) {
					$options['cf7anyapi_base_url'] = esc_url_raw(wp_unslash($_POST['cf7anyapi_base_url']));
				}
				if (isset($_POST['cf7anyapi_input_type'])) {
					$options['cf7anyapi_input_type'] = sanitize_text_field( wp_unslash( $_POST['cf7anyapi_input_type'] ) );
				}
				if (isset($_POST['cf7anyapi_method'])) {
					$options['cf7anyapi_method'] = sanitize_text_field( wp_unslash( $_POST['cf7anyapi_method']) );
				}
				if (isset($_POST['cf7anyapi_form_field'])) {
					$options['cf7anyapi_form_field'] = self::Cf7_To_Any_Api_sanitize_array($_POST['cf7anyapi_form_field']);
				}
				if (isset($_POST['cf7anyapi_header_request'])) {
					$options['cf7anyapi_header_request'] = sanitize_textarea_field(wp_unslash($_POST['cf7anyapi_header_request']));
				}
				
				/**
			     * CONDITIONAL LOGIC (INLINE)
			     */
				$options['cf7anyapi_enable_condition'] = isset($_POST['cf7anyapi_enable_condition']) ? '1' : '0';
				if ($options['cf7anyapi_enable_condition'] === '1') {
					if (isset($_POST['cf7anyapi_conditions']) && is_array($_POST['cf7anyapi_conditions'])) {
					    $field    = sanitize_text_field(wp_unslash($_POST['cf7anyapi_conditions']['field'] ?? ''));
					    $operator = sanitize_text_field(wp_unslash($_POST['cf7anyapi_conditions']['operator'] ?? '')); 
					    $value    = sanitize_text_field(wp_unslash($_POST['cf7anyapi_conditions']['value'] ?? ''));
				        $options['cf7anyapi_conditions'] = array(
				            'field'    => $field,
				            'operator' => $operator,
				            'value'    => $value,
				        );
					}
				}
				foreach($options as $options_key => $options_value){
					$response = update_post_meta( $cf7_to_any_api_id, $options_key, $options_value );
    			}
				if($response){
					$status = 'true';
				}
			}
		}
	}

	/**
	* Add custom action links on the Plugins page for "Contact Form to Any API".
	* Adds:
	* - "Get Contact Form to Any API Pro" (external link)
	* - "Settings" (link to plugin settings page)
	* - "Documentation" (link to plugin documentation page)
	*/
	public function cf7anyapi_add_settings_link($links,$file){
		if($file === 'contact-form-to-any-api/cf7-to-any-api.php' && current_user_can('manage_options')){
			$pro_link     = CF7_CURL_DOMAIN.'/pricing/';
			$settings_url = admin_url( 'edit.php?post_type=cf7_to_any_api' );
			$docs_url     = admin_url( 'edit.php?post_type=cf7_to_any_api&page=cf7anyapi_docs' );
			
			$pro_link_html = sprintf(
				'<a href="%s" target="_blank" style="font-weight:700; color:#1da867;">%s</a>',
				esc_url( $pro_link ),
				esc_html__( 'Get Contact Form to Any API Pro', 'contact-form-to-any-api' )
			);

			$settings_link = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $settings_url ),
				esc_html__( 'Settings', 'contact-form-to-any-api' )
			);

			$docs_link = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $docs_url ),
				esc_html__( 'Documentation', 'contact-form-to-any-api' )
			);

			// Insert Pro link at the beginning (before "Deactivate")
			array_unshift( $links, $pro_link_html );
			$links[] = $settings_link;
			$links[] = $docs_link;
		}
		return $links;
	}
	
	/**
	 * Filters the columns displayed in the custom post type list table.
	 *
	 * This function customizes the columns shown in the admin list view
	 * for the custom post type related to the "Contact Form to Any API" plugin.
	 */
	public function cf7_to_any_api_filter_posts_columns($columns){
		$columns = array(
			'cb' => $columns['cb'],
			'title' => __('Title', 'contact-form-to-any-api'),
			'cf7form' => __('Form Name','contact-form-to-any-api'),
			'cf7api_status' => __('Status','contact-form-to-any-api'),			
			'date' => __('Date','contact-form-to-any-api'),
		);
		return $columns;
	}

	public function cf7_to_any_api_sortable_columns($columns){
		$columns['cf7form'] = 'cf7anyapi_selected_form';
  		return $columns;
	}

	/**
	 * Delete all log in a one click
	 *
	 * @since    1.0.0
	 */
	public static function cf7_to_any_api_bulk_log_delete_function(){
		if (!current_user_can('manage_options')) {
	        wp_send_json_error( __( 'Unauthorized', 'contact-form-to-any-api' ) );
	    }
	    if ( empty( $_POST['cf_to_any_api_log_del_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cf_to_any_api_log_del_nonce'] ) ), 'cf_to_any_api_log_del_nonce'
	        )) {
	        wp_send_json_error( __( 'Invalid nonce', 'contact-form-to-any-api' ) );
	    }
	    if ( empty( $_POST['cf_to_any_api_log_ids'] ) || ! is_array( $_POST['cf_to_any_api_log_ids'] ) ) {
	        wp_send_json_error( __( 'No logs selected', 'contact-form-to-any-api' ) );
	    }
	    
	    $log_ids = array_map( 'absint', $_POST['cf_to_any_api_log_ids'] );
    	$log_ids = array_filter( $log_ids );
    	if ( empty( $log_ids ) ) {
	        wp_send_json_error( __( 'Invalid log IDs', 'contact-form-to-any-api' ) );
	    }

	    global $wpdb;
	    $table_name   = $wpdb->prefix . 'cf7anyapi_logs';
	    $placeholders = implode( ',', array_fill( 0, count( $log_ids ), '%d' ) );

	    $deleted = $wpdb->query(
	        $wpdb->prepare(
	            "DELETE FROM {$table_name} WHERE id IN ($placeholders)",
	            ...$log_ids
	        )
	    );

	    if ( false !== $deleted ) {
	        wp_send_json_success(
	            sprintf(
	                __( 'Deleted %d log(s).', 'contact-form-to-any-api' ),
	                $deleted
	            )
	        );
	    }
	    wp_send_json_error( __( 'Deletion failed', 'contact-form-to-any-api' ) );
		exit();
	}

	

	/**
	 * Registered Metaboxes Fields
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_settings() {
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-display.php';
	}

	/**
	 * On Metabox Form Change Show that form fields
	 *
	 * @since    1.0.0
	 */
	public static function cf7_to_any_api_get_form_field_function(){
		if(empty((int)sanitize_text_field(wp_unslash($_POST['form_id'])))){
			echo wp_json_encode(__( 'No Fields Found for Selected Form.', 'contact-form-to-any-api' ));
			exit();
		}
		$html = '';
		$form_ID     = (int)sanitize_text_field(wp_unslash($_POST['form_id']));
		$post_id     = (int)sanitize_text_field(wp_unslash($_POST['post_id']));
		$ContactForm = WPCF7_ContactForm::get_instance($form_ID);
		$form_fields = $ContactForm->scan_form_tags();

		$post_form_id = get_post_meta($post_id,'cf7anyapi_selected_form',true);
		$post_form_field = get_post_meta($post_id,'cf7anyapi_form_field',true);
		
		if(!empty($post_form_field) && $post_form_id == $form_ID){
			foreach($form_fields as $form_fields_key => $form_fields_value){
				if($form_fields_value->basetype != 'submit'){
					$html .= '<div class="cf7anyapi_field">';
						$html .= '<label for="cf7anyapi_'.$form_fields_value->raw_name.'">'.$form_fields_value->name.'</label>';
						$html .= '<input type="text" id="cf7anyapi_'.$form_fields_value->raw_name.'" name="cf7anyapi_form_field['.$form_fields_value->name.']" value="'.$post_form_field[$form_fields_value->raw_name].'" data-basetype="'.$form_fields_value->basetype.'" placeholder="'. __( 'Enter your API mapping key', 'contact-form-to-any-api' ). '">'; 
					$html .= '</div>';
				}
			}
		}
		else{
			foreach($form_fields as $form_fields_key => $form_fields_value){
				if($form_fields_value->basetype != 'submit'){
					$html .= '<div class="cf7anyapi_field">';
						$html .= '<label for="cf7anyapi_'.$form_fields_value->raw_name.'">'.$form_fields_value->name.'</label>';
						$html .= '<input type="text" id="cf7anyapi_'.$form_fields_value->raw_name.'" name="cf7anyapi_form_field['.$form_fields_value->name.']" data-basetype="'.$form_fields_value->basetype.'" placeholder="'. __( 'Enter your API mapping key', 'contact-form-to-any-api' ). '">'; 
					$html .= '</div>';
				}
			}
		}
		$html .= '<div class="update_pro_wrapper"><small class="update_pro_features"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.61 122.88"><title>upload</title><path d="M23.28,94.67H23a50.6,50.6,0,0,0,88.87-33.1,5.36,5.36,0,0,1,10.71,0A61.3,61.3,0,0,1,17.54,104.48v12.35a5.36,5.36,0,0,1-10.72,0V89.31A5.36,5.36,0,0,1,12.18,84h3.91a50.57,50.57,0,0,0,7.19,10.71Zm38-72.91A39.68,39.68,0,1,1,21.62,61.44,39.68,39.68,0,0,1,61.31,21.76ZM55.1,83.41H67.55A4.48,4.48,0,0,0,72,78.93V63.45h7.91A3.72,3.72,0,0,0,83.09,62c1.66-2.49-.6-5-2.17-6.68-4.47-4.89-14.57-13.76-16.77-16.35a3.64,3.64,0,0,0-5.71,0C56.17,41.59,45.52,51,41.28,55.75,39.81,57.4,38,59.66,39.52,62a3.76,3.76,0,0,0,3.17,1.49h7.93V78.93a4.49,4.49,0,0,0,4.48,4.48Zm51.5-78a5.36,5.36,0,1,1,10.71,0V33.14A5.36,5.36,0,0,1,112,38.49h-5.65A50.42,50.42,0,0,0,99,27.78h0a51,51,0,0,0-6.48-6.07l0,0L91.62,21l-.1-.07-.11-.08-.21-.16L91,20.61l0,0-.22-.16-.42-.3L90.13,20A50.51,50.51,0,0,0,25.6,25.73c-.31.31-.62.62-.92.94l-.35.37-.06.07-.35.37A50.45,50.45,0,0,0,10.71,61.57,5.36,5.36,0,1,1,0,61.57,61.31,61.31,0,0,1,91.07,8,61.83,61.83,0,0,1,106.6,20.27V5.36Z"/></svg>' . esc_html__( 'Multi-dimension support and OAuth 2.0 are available in the  ', 'contact-form-to-any-api' ) . ' <a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/' ) . '" target="_blank" style="color:#1da867; font-weight:bold;">' . esc_html__( 'Pro features', 'contact-form-to-any-api' ) . '.</a></small></div>
		<div class="update_pro_wrapper"><small class="update_pro_features"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 122.61 122.88"><title>upload</title><path d="M23.28,94.67H23a50.6,50.6,0,0,0,88.87-33.1,5.36,5.36,0,0,1,10.71,0A61.3,61.3,0,0,1,17.54,104.48v12.35a5.36,5.36,0,0,1-10.72,0V89.31A5.36,5.36,0,0,1,12.18,84h3.91a50.57,50.57,0,0,0,7.19,10.71Zm38-72.91A39.68,39.68,0,1,1,21.62,61.44,39.68,39.68,0,0,1,61.31,21.76ZM55.1,83.41H67.55A4.48,4.48,0,0,0,72,78.93V63.45h7.91A3.72,3.72,0,0,0,83.09,62c1.66-2.49-.6-5-2.17-6.68-4.47-4.89-14.57-13.76-16.77-16.35a3.64,3.64,0,0,0-5.71,0C56.17,41.59,45.52,51,41.28,55.75,39.81,57.4,38,59.66,39.52,62a3.76,3.76,0,0,0,3.17,1.49h7.93V78.93a4.49,4.49,0,0,0,4.48,4.48Zm51.5-78a5.36,5.36,0,1,1,10.71,0V33.14A5.36,5.36,0,0,1,112,38.49h-5.65A50.42,50.42,0,0,0,99,27.78h0a51,51,0,0,0-6.48-6.07l0,0L91.62,21l-.1-.07-.11-.08-.21-.16L91,20.61l0,0-.22-.16-.42-.3L90.13,20A50.51,50.51,0,0,0,25.6,25.73c-.31.31-.62.62-.92.94l-.35.37-.06.07-.35.37A50.45,50.45,0,0,0,10.71,61.57,5.36,5.36,0,1,1,0,61.57,61.31,61.31,0,0,1,91.07,8,61.83,61.83,0,0,1,106.6,20.27V5.36Z"/></svg>' . esc_html__( 'Passing fields as integers and uploading multiple files are ', 'contact-form-to-any-api' ) . ' <a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/' ) . '" target="_blank" style="color:#1da867; font-weight:bold;">' . esc_html__( 'Pro features', 'contact-form-to-any-api' ) . '.</a></small></div>';

		echo wp_json_encode($html);
		exit();
	}

	/*
	* Create New Table
	*/
	public function cf7toanyapi_add_new_table(){
		
		global $wpdb;
		$table = $wpdb->prefix.'cf7anyapi_entry_id';
		if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s%s'", $wpdb->prefix, 'cf7anyapi_entry_id')) != $wpdb->prefix . 'cf7anyapi_entry_id'){
	        $charset_collate = $wpdb->get_charset_collate();
	        $wpdb->query($wpdb->prepare("CREATE TABLE IF NOT EXISTS %i ( id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,Created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) $charset_collate;", $table));
	    }

	    if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s%s'", $wpdb->prefix, 'cf7anyapi_entries')) != $wpdb->prefix . 'cf7anyapi_entries'){
	    	$charset_collate = $wpdb->get_charset_collate();
	        $table_name2 = $wpdb->prefix.'cf7anyapi_entries';
	        $wpdb->query($wpdb->prepare("CREATE TABLE IF NOT EXISTS %i ( id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT, form_id int(11) , data_id int(11), field_name varchar(255), field_value varchar(255), date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (data_id) REFERENCES %i (id) )$charset_collate;", $table_name2,$table));
	    }

	    // Log Table Add New Field "Status" Column
	    $table_logs = $wpdb->prefix . 'cf7anyapi_logs';
        if (empty($wpdb->get_var("SHOW COLUMNS FROM `$table_logs` LIKE 'status'"))) {
            $wpdb->query("ALTER TABLE $table_logs ADD `status` VARCHAR(50) NULL AFTER `log`");
        }
	}
	
	/**
	 * Sanitize Array Value
	 *
	 * @since     1.0.0
	 * @return    string
	 */
	public static function Cf7_To_Any_Api_sanitize_array($array){
		$sanitize_array = array();
		foreach($array as $key => $value) {
			$sanitize_array[sanitize_text_field($key)] = sanitize_text_field($value);
		}
		return $sanitize_array;
	}

	/**
	 * On Form Submit Selected Form Data send to API
	 *
	 * @since    1.0.0
	 */
	public static function cf7_to_any_api_send_data_to_api($WPCF7_ContactForm){
		global $wpdb;
		$form_title = $WPCF7_ContactForm->title();
		$cf7_uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'cf7-to-any-api-uploads';
		if(isset($_POST['_wpcf7'])){
			$form_id = sanitize_text_field((int)wp_unslash($_POST['_wpcf7']));
		}else{
			$form_id = 0;
		}
		if (! is_dir($cf7_uploads_dir)) {
			wp_mkdir_p( $cf7_uploads_dir );
		}
		$submission = WPCF7_Submission::get_instance();
		$posted_data = $submission->get_posted_data();
		$cf7files = $submission->uploaded_files();
		if( !empty($cf7files) && get_option('cf7_to_api_entry_hide') != true ) {
			foreach ($cf7files as $key => $cf7file) {
				if(!empty($cf7file)){
					$ext = pathinfo($cf7file[0], PATHINFO_EXTENSION);
					$f_name = pathinfo($cf7file[0], PATHINFO_FILENAME );
					$fileName = 'cf7-'.$form_id.'-'.time().'.'.$ext;
					copy($cf7file[0], $cf7_uploads_dir.'/'.$fileName);
					$posted_data[$key] = '<a href="'.wp_upload_dir()['baseurl'] . '/cf7-to-any-api-uploads/'.$fileName.'" target="_blank">'.$fileName.'</a>';
			    }
			}
		}
		$post_id = $submission->get_meta('container_post_id');
		$posted_data['submitted_from'] = $post_id;
		// $posted_data['submit_time'] = date('Y-m-d H:i:s');
		$posted_data['submit_time'] = wp_date('Y-m-d H:i:s');
		if(isset($_SERVER['REMOTE_ADDR'])){
			$posted_data['User_IP'] = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
		}
		self::cf7anyapi_save_form_submit_data($form_id,$posted_data);

		//Image set base64 encode
		if( !empty($cf7files)){
			foreach ($cf7files as $key => $cf7file) {
				if(!empty($cf7file)){
				   $posted_data[$key] = base64_encode(file_get_contents($cf7file[0]));
				}
			}   
		}

		$args = array(
			'post_type' => 'cf7_to_any_api',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
		        'relation' => 'AND',
		        array(
		            'key' => 'cf7anyapi_selected_form',
		            'value' => $form_id,
		            'compare' => '=',
		        ),
		    ),
		);
		
		$the_query = new WP_Query($args);
		if($the_query->have_posts()){
		    
		    while($the_query->have_posts()){
		        $the_query->the_post();
		        $api_post_array = array();
		        
		        $cf7anyapi_form_field = array_filter((array)get_post_meta(get_the_ID(),'cf7anyapi_form_field',true));
		        $cf7anyapi_base_url = get_post_meta(get_the_ID(),'cf7anyapi_base_url',true);

		        $cf7anyapi_basic_auth = get_post_meta(get_the_ID(),'cf7anyapi_basic_auth',true);
		        $cf7anyapi_bearer_auth = get_post_meta(get_the_ID(),'cf7anyapi_bearer_auth',true);

		        $cf7anyapi_input_type = get_post_meta(get_the_ID(),'cf7anyapi_input_type',true);
				$cf7anyapi_method = get_post_meta(get_the_ID(),'cf7anyapi_method',true);

				$header_request 		= get_post_meta(get_the_ID(),'cf7anyapi_header_request' ,true);
				$cf7anyapi_header_request = apply_filters( 'cf7anyapi_header_request', $header_request, get_the_ID(), $form_id);
		        foreach($cf7anyapi_form_field as $key => $value){
		        	$api_post_array[$value] = (is_array($posted_data[$key]) ? implode(',', self::Cf7_To_Any_Api_sanitize_array($posted_data[$key])) : sanitize_text_field($posted_data[$key]));
		        }
		        
		        try {
					$all_data = array();
					foreach ($posted_data as $key => $value) {
						$all_data[$key] = (is_array($posted_data[$key]) ? implode(',', self::Cf7_To_Any_Api_sanitize_array($posted_data[$key])) : sanitize_text_field($posted_data[$key]));
					}
					$api_post_array["all_data"] = json_encode($all_data);
				} catch (Exception $e) {
					//echo 'Message: ' .$e->getMessage();
				}

		        // Handle CF7 special tags dynamically
			    $pre_defined_tags = [
				    '_user_ip'          => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
				    '_submission_source_url' => esc_url(get_permalink($post_id)),
				    '_date'             => date_i18n(get_option('date_format')),
				    '_time'             => date_i18n(get_option('time_format')),
				    '_submitted_date_time' => date_i18n(get_option('date_format')). ' ' .date_i18n(get_option('time_format')),
				    '_site_url'         => home_url(),
				    '_post_id'          => $post_id,
				    '_post_slug'        => basename(get_permalink($post_id)),
				    '_post_title'       => get_the_title($post_id),
				    '_form_id'			=> $form_id,
				    '_form_name'		=> $form_title,
				    '_http_referer'     => isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '',
				    '_browser_info'     => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : '',
				    '_server_name'      => isset($_SERVER['SERVER_NAME']) ? sanitize_text_field($_SERVER['SERVER_NAME']) : '',
				];
			    // Predefined tags mapping
				foreach ( $cf7anyapi_form_field as $key => $value ) {
				    if ( isset( $pre_defined_tags[$key] ) ) {
				        $api_post_array[$value] = $pre_defined_tags[$key];
				    }
				}
		        self::cf7anyapi_send_lead($api_post_array, $cf7anyapi_base_url, $cf7anyapi_input_type, $cf7anyapi_method, $form_id, get_the_ID(), $cf7anyapi_basic_auth, $cf7anyapi_bearer_auth,$cf7anyapi_header_request, $posted_data);
		    }
		}
		wp_reset_postdata();
	}

	public static function cf7anyapi_save_form_submit_data($form_id,$posted_data){
		if(get_option('cf7_to_api_entry_hide') != true ){
			global $wpdb;
			$table = $wpdb->prefix.'cf7anyapi_entry_id';
			$table2 = $wpdb->prefix.'cf7anyapi_entries';

			$wpdb->insert($table,array('Created' => date("Y-m-d H:i:s")));
			$data_id = (int)$wpdb->insert_id;

			foreach($posted_data as $field => $value){
				
				$posted_value = (is_array($value) ? implode(',',$value) : $value);
				if (is_string($posted_value) && strlen($posted_value) > 255) {
	    			$posted_value = substr($posted_value, 0, 255);
				}
				$wpdb->insert(
					$table2,
					array(
						'form_id' => $form_id,
						'data_id' => $data_id,
						'field_name' => $field,
						'field_value' => $posted_value
					),
					array('%s')
				);
			}
		}
	}

	/**
	 * Child Fuction of specific form data send to the API
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_send_lead($data, $url, $input_type, $method, $form_id, $post_id, $basic_auth = '', $bearer_auth = '',$header_request = '', $posted_data = ''){
		
        $enable_condition = get_post_meta($post_id, 'cf7anyapi_enable_condition', true);
		$condition_value  = get_post_meta($post_id, 'cf7anyapi_conditions', true);
		if ( $enable_condition === '1' ) {
		    if ( ! self::cf7_to_any_api_check_condition($posted_data, $condition_value) ) {
		        return; // stop execution
		    }
		}

		$cf7api_status = get_post_meta( $post_id, '_cf7api_status', true );
		if( $cf7api_status != 'disabled'){
			
			global $wp_version;
			

			if($method == 'GET' && ($input_type == 'params' || $input_type == 'json')){
				$args = array(
					'timeout'     => 30,
					'redirection' => 5,
					'httpversion' => '1.0',
					'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
					'blocking'    => true,
					'headers'     => array(),
					'cookies'     => array(),
					'body'        => null,
					'compress'    => false,
					'decompress'  => true,
					'sslverify'   => true,
					'stream'      => false,
					'filename'    => null
				);

				// Append data as URL query params
				$data_string = http_build_query($data);
        		$url = stripos($url,'?') !== false ? $url.'&'.$data_string : $url.'?'.$data_string;
				$response = wp_remote_get( $url, $args );
				$result = wp_remote_retrieve_body($response);
				$status_code = wp_remote_retrieve_response_code($response);
	      		self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result, $posted_data, $status_code);
			}
			else{
				$args = array(
					'timeout'     => 30,
					'redirection' => 5,
					'httpversion' => '1.0',
					'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
					'blocking'    => true,
					'headers'     => array(),
					'cookies'     => array(),
					'body'        => $data,
					'compress'    => false,
					'decompress'  => true,
					'sslverify'   => true,
					'stream'      => false,
					'filename'    => null
				);

				if(isset($basic_auth) && $basic_auth !== ''){
	        		$args['headers']['Authorization'] = 'Basic ' . base64_encode( $basic_auth );
	      		}
	      
	      		if(isset($bearer_auth) && $bearer_auth !== ''){
	    			$args['headers']['Authorization'] = 'Bearer ' . $bearer_auth;
	      		}

	      		if(isset($header_request) && $header_request !== ''){
	      			$args['headers'] = $header_request;
	      		}
				
				if($input_type == "json"){
					if(!isset($header_request) && $header_request === ''){
	        			$args['headers']['Content-Type'] = 'application/json';
	        		}
	        		$json = self::Cf7_To_Any_Api_parse_json($data);
	        	
	        		if(is_wp_error($json)){
	          			return $json;
	        		}  
	        		else{
	          			$args['body'] = $json;
	    			}
	      		}

	      		$result = wp_remote_post($url, $args);
	      		$result_body = wp_remote_retrieve_body($result);
	      		$status_code = wp_remote_retrieve_response_code($result);
	      		if(!empty($result_body)){
					$result = $result_body;
				}
	      		self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result, $posted_data, $status_code);
			}
		}
	}

	/**
	 * Form Data convert into JSON formate
	 *
	 * @since    1.0.0
	 */
	public static function Cf7_To_Any_Api_parse_json($string){
		return json_encode($string, JSON_UNESCAPED_UNICODE);
  	}

  	/**
	 * API response store into Database
	 *
	 * @since    1.0.0
	 */
  	public static function Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $response, $posted_data, $status_code){
  		if(get_option('cf7_to_api_log_hide') != true ){
	  		global $wpdb;
	  		$table = $wpdb->prefix.'cf7anyapi_logs';

	  		// Base64 image get only 10 characters
	  		if(isset($posted_data)){
	  			foreach($posted_data as $key => $arr){
	  				$sanitized_key = sanitize_text_field($key);
					if(strstr($sanitized_key, 'file-')){
						$posted_data[$sanitized_key] = mb_substr(sanitize_text_field($arr), 0, 10).'...';
				    } else {
	                	// Sanitize other input fields
		                $posted_data[$sanitized_key] = is_array($arr) ? array_map('sanitize_text_field', $arr) : sanitize_text_field($arr);
		            }
				}
	  		}
	  		
	  		$form_data = json_encode($posted_data, JSON_UNESCAPED_UNICODE);
	  		if (gettype($response) != 'string') {
				$response = json_encode($response, JSON_UNESCAPED_UNICODE);
			}else {
		        $response = sanitize_text_field($response);
		    }

	  		$data = array(
	  			'form_id' => $form_id,
	  			'post_id' => $post_id,
	  			'form_data' => $form_data,
	  			'status' => $status_code,
	  			'log' => $response,
	  		);

	  		$wpdb->insert($table,$data);
	  	}
  	}

  	public static function delete_cf7_records(){

  		if ( !empty($_POST['id']) && ( isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'],'cf_to_any_api_entrie_del_nonce') )  ) {
		    global $wpdb;
		    $record_id = isset($_POST['id']) ? $_POST['id'] : array();
			$placeholders = implode(',', array_fill(0, count($record_id), '%d'));

		    $table_entries = $wpdb->prefix.'cf7anyapi_entries';
		    $table = $wpdb->prefix.'cf7anyapi_entry_id';

		    $result_entries = $wpdb->query($wpdb->prepare("DELETE FROM $table_entries WHERE data_id IN ($placeholders)", $record_id));
		    $result_id = $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE id IN ($placeholders)", $record_id));
		    if ($record_id !== false) {
		        echo json_encode(array('status' => 1, 'Message' => 'Success'));
		    }else {
	        	echo json_encode(array('status' => -1, 'Message' => 'Failed'));
	    	}
		}else {
	        echo json_encode(array('status' => -1, 'Message' => 'Invalid'));
	    }
	    exit();
	}

  	public function _cf7_api_deactivation_feedback_popup(){
		$screen = get_current_screen();
		if ($screen->base === 'plugins') {
			if ( is_file( dirname(__FILE__) . '/partials/cf7-to-any-api-feedback.php') ) {
				include dirname(__FILE__).'/partials/cf7-to-any-api-feedback.php';
            }
        }
	}

	public function cf7anyapi_add_dashboard_widget() {
	    wp_add_dashboard_widget(
	        'cf7anyapi_dashboard_widget',       // Widget slug
	        __('Contact Form 7 to Any API Statistics', 'contact-form-to-any-api'), // Widget title
	        array(&$this,'cf7anyapi_render_dashboard_widget') // Callback function to display content
	    );

		// Reorder the widget to appear at the top
		global $wp_meta_boxes;

		// Move your widget to the top of the 'normal' dashboard area
		$widget = $wp_meta_boxes['dashboard']['normal']['core']['cf7anyapi_dashboard_widget'];
		unset($wp_meta_boxes['dashboard']['normal']['core']['cf7anyapi_dashboard_widget']);
	
		// Reinsert it at the top
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge(
			array('cf7anyapi_dashboard_widget' => $widget),
			$wp_meta_boxes['dashboard']['normal']['core']
		);
	}

	// Render the widget content
	public function cf7anyapi_render_dashboard_widget() {
	    $cf7_api_count = $this->get_cf7_api_post_count();

	    echo '<ul>';
	    if ($cf7_api_count > 0) {
	        echo '<li><span class="dashicons dashicons-rest-api"></span> <strong>' . esc_html(__('Number of API Connections:', 'contact-form-to-any-api')) . '</strong> ' . esc_html($cf7_api_count) . '</li>';
	    } else {	        
	        echo '<li><span class="dashicons dashicons-rest-api"></span> <a href="' . esc_url(admin_url('post-new.php?post_type=cf7_to_any_api')) . '"><strong>' . esc_html(__('Add New API Connection', 'contact-form-to-any-api')) . '</strong></a></li>';
	    }
	    echo '<li><span class="dashicons dashicons-book-alt"></span> <a href="' . esc_url(admin_url('edit.php?post_type=cf7_to_any_api&page=cf7anyapi_docs')) . '"><strong>' .esc_html(__('API Documentation', 'contact-form-to-any-api')) . '</strong></a></li>';
	    echo '<li><span class="dashicons dashicons-clipboard"></span> <a href="' . esc_url(admin_url('edit.php?post_type=cf7_to_any_api&page=cf7anyapi_logs')) . '"><strong>' . esc_html(__('API Logs', 'contact-form-to-any-api')) . '</strong></a></li>';
	    echo '</ul>';
	    echo '<hr>';
	    echo '<h3><strong>' . esc_html(__('Contact Form 7 to Any API PRO', 'contact-form-to-any-api')) . '</strong></h3>';
	    echo '<ul>';
	    echo '<li><span class="dashicons dashicons-saved"></span> ' . esc_html(__('200+ API Supports', 'contact-form-to-any-api')) . '</li>';
	    echo '<li><span class="dashicons dashicons-saved"></span> ' . esc_html(__('Support Multi Level or Any Format of JSON', 'contact-form-to-any-api')) . '</li>';
	    echo '<li><span class="dashicons dashicons-saved"></span> ' . esc_html(__('Supports Multiple Files Upload BASE64', 'contact-form-to-any-api')) . '</li>';
	    echo '<li><span class="dashicons dashicons-saved"></span> ' . esc_html(__('Priority Support (support@contactformtoapi.com)', 'contact-form-to-any-api')) . '</li>';
	    echo '<li><span class="dashicons dashicons-saved"></span> ' . esc_html(__('OAuth 2.0 Customization From our Expert Developer Team', 'contact-form-to-any-api')) . '</li>';
	    echo '</ul>';
	    echo '<ul>';
	    echo '<li><a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/' ) . '" class="button button-primary"><strong>' . esc_html(__('Buy now', 'contact-form-to-any-api')) . '</strong></a> ';
	    echo '<a href="' . esc_url( CF7_CURL_DOMAIN . '/#contact_us' ) . '" class="button button-secondary"><strong>' . esc_html( __('Need Help with Plugin Integration', 'contact-form-to-any-api') ) . 
     '</strong></a>';
	    echo '</ul>';
	}

	// Function to fetch the count of published posts for the cf7_to_any_api CPT
	public function get_cf7_api_post_count() {
	    $args = array(
	        'post_type'      => 'cf7_to_any_api',
	        'post_status'    => 'publish',
	        'posts_per_page' => -1,
	        'fields'         => 'ids'
	    );

	    $query = new WP_Query($args);
	    $count = $query->found_posts;
	    return $count;
	}	

	// Function to add additional links in plugin meta area of plugin listing page
	public function cf7anyapi_add_plugin_links($links, $file) {
		
		if ($file == 'contact-form-to-any-api/cf7-to-any-api.php') {
			$new_links = array(
			    '<a href="' . esc_url( CF7_CURL_DOMAIN . '/#contact_us' ) . '" target="_blank">' . esc_html__( 'Support', 'contact-form-to-any-api' ) . '</a>',
			    '<a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/' ) . '" target="_blank">' . esc_html__( 'Upgrade To PRO', 'contact-form-to-any-api' ) . '</a>',
			    '<a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/#oauth' ) . '" target="_blank">' . esc_html__( 'OAuth 2.0 Customization', 'contact-form-to-any-api' ) . '</a>',
			    '<a href="' . esc_url( CF7_CURL_DOMAIN . '/pricing/#crm' ) . '" target="_blank">' . esc_html__( 'Supported CRM/API', 'contact-form-to-any-api' ) . '</a>',
			    '<a href="' . esc_url( 'https://wordpress.org/plugins/connect-wpform-to-any-api/' ) . '" target="_blank">' . esc_html__( 'Connect WPForm to Any API', 'contact-form-to-any-api' ) . '</a>'
			);
			// Merge new links with existing ones
			$links = array_merge($links, $new_links);
		}
		return $links;
	}

	/**
	* Retrieve the all current Setting data
	*
	*/
    public function cf7_to_any_api_update_settings(){

    	if ( ! current_user_can( 'manage_options' ) ) {
	        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'contact-form-to-any-api' ) );
	    }

		check_admin_referer( -1 , 'save_cf7_to_any_api_update_settings' );

	    // Define the settings you expect
	    $fields = [
	        'cf7_to_api_before_mail_sent',
	        'cf7_to_api_log_hide',
	        'cf7_to_api_entry_hide',
	    ];

	    foreach ( $fields as $field ) {
	        $value = isset($_POST[$field]) ? absint( sanitize_text_field( $_POST[$field] ) ) : 0;
	        update_option( $field, $value );
	    }
	    
	    if ( isset( $_POST['_wp_http_referer'] ) ) {
	        wp_redirect( esc_url_raw( $_POST['_wp_http_referer'] ) . '&update-status=true' );
	        exit;
	    }
	}

	/**
	* Add new columns for the enable and disable API
	*
	*/
    public function cf7_to_any_api_posts_columns($columns){
    	$columns['cf7api_status'] = __( 'Status', 'contact-form-to-any-api' );
    	return $columns;
    }
    /**
	 * Handles custom columns for the 'cf7_to_any_api' custom post type.
	 * 
	 * An Enable/Disable toggle switch in the "cf7api_status" column.
	 * The associated Contact Form 7 form link in the "cf7form" column.
	 * */
    public function cf7_to_any_api_posts_custom_column ($column, $post_id ){

    	switch ( $column ) {
    		case 'cf7api_status':
	            $status = get_post_meta( $post_id, '_cf7api_status', true );
		        $status = $status == 'disabled' ? 'disabled' : '';
		        echo '<label class="cf7api-status-switch">
	                <input type="checkbox" class="cf7api-toggle" data-post-id="' . esc_attr( $post_id ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'cf7api_toggle_status_' . $post_id ) ) . '" ' . checked( $status, '', false ) . '>
	                <span class="cf7api-slider"></span>
	                <img src="' . esc_url( plugin_dir_url( __FILE__ ) . 'images/loading_icon.gif' ) . '" alt="loading_icon" style="display:none">
	              </label>';
            	break;
 			case 'cf7form':
 				$form_name = get_post_meta($post_id,'cf7anyapi_selected_form',true);
		    	if($form_name){
					echo '<a href="' . esc_url( site_url() . '/wp-admin/admin.php?page=wpcf7&post=' . $form_name . '&action=edit' ) . '" target="_blank">' . esc_html( get_the_title( $form_name ) ) . '</a>';
				}
				break;
    	 }
    }

    /**
	 * AJAX callback to toggle the _cf7api_status meta.
	 * - If enabled: meta is deleted (empty) so default logic treats it as enabled.
	 * - If disabled: meta is set to 'disabled'.
	 */
    function cf7_to_any_api_toggle_status_callback() {

	    if ( ! isset( $_POST['post_id'], $_POST['nonce'] ) ) {
	        wp_send_json_error( __( 'Invalid request.', 'contact-form-to-any-api' ) );
	    }
	    $post_id = intval( $_POST['post_id'] );

	    if ( ! wp_verify_nonce( $_POST['nonce'], 'cf7api_toggle_status_' . $post_id ) ) {
	        wp_send_json_error( __( 'Security check failed.', 'contact-form-to-any-api' ) );
	    }

	    if ( ! current_user_can( 'edit_post', $post_id ) ) {
	        wp_send_json_error( __( 'You are not allowed to do this.', 'contact-form-to-any-api' ) );
	    }
	    $is_checked = isset( $_POST['is_checked'] ) && $_POST['is_checked'] == 'true';
	    if ( $is_checked ) {
	        update_post_meta( $post_id, '_cf7api_status', '' );
	        $status = 'active';
	    } else {
	        update_post_meta( $post_id, '_cf7api_status', 'disabled' );
	        $status = 'disabled';
	    }
	    wp_send_json_success( [ 'status' => $status ] );
	}

	/*
	* Check conditional logic before sending CF7 lead to API.
	*/
	private static function cf7_to_any_api_check_condition( $posted_data, $condition ) {

	    if ( empty($condition) || !is_array($condition) ) {
	        return true;
	    }

	    $field    = $condition['field']    ?? '';
	    $operator = $condition['operator'] ?? '';
	    $value    = $condition['value']    ?? '';

	    if ( $field === '' || ! in_array($operator, ['equals', 'not_equals'], true) ) {
	        return true;
	    }
	    if ( !isset($posted_data[$field]) ) {
	        return false;
	    }		
		$posted_raw = $posted_data[ $field ];
		if ( is_array( $posted_raw ) ) {
	        if ( empty( $posted_raw ) ) {
	            $posted_value = '';
	        } else {
	            $posted_value = implode(',', array_map( 'trim', array_map( 'strval', $posted_raw ) ) );
	        }
	    } else {
	        $posted_value = trim( (string) $posted_raw );
	    }
	    $value	= trim($value);

	    if ( $operator === 'equals' ) {
	        return $posted_value === $value;
	    }

	    if ( $operator === 'not_equals' ) {
	        return $posted_value !== $value;
	    }

	    return true; // fallback
	}

}