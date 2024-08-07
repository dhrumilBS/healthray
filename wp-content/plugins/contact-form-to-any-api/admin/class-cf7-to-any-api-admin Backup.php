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
	        'site_url' => site_url(),
	        'ajax_url' => admin_url('admin-ajax.php'),
	    );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cf7-to-any-api-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'ajax_object', $data);

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
	}

	/**
	 * Register the Custom Post Type
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_custom_post_type(){
		$supports = array(
			'title', // Custom Post Type Title
		);
		$labels = array(
			'name' => _x('CF7 to API', 'plural', 'contact-form-to-any-api'),
			'singular_name' => _x('cf7 to api', 'singular', 'contact-form-to-any-api'),
			'menu_name' => _x('CF7 to Any API', 'admin menu', 'contact-form-to-any-api'),
			'name_admin_bar' => _x('CF7 to Any API', 'admin bar', 'contact-form-to-any-api'),
			'add_new' => _x('Add New CF7 API', 'add new', 'contact-form-to-any-api'),
			'add_new_item' => __('Add New CF7 API', 'contact-form-to-any-api'),
			'new_item' => __('New CF7 API', 'contact-form-to-any-api'),
			'edit_item' => __('Edit CF7 API', 'contact-form-to-any-api'),
			'view_item' => __('View CF7 API', 'contact-form-to-any-api'),
			'all_items' => __('All CF7 API', 'contact-form-to-any-api'),
			'not_found' => __('No CF7 API found.', 'contact-form-to-any-api'),
			'register_meta_box_cb' => 'aps_metabox',
		);
		$args = array(
			'supports' => $supports,
			'labels' => $labels,
			'hierarchical' => false,
			'public' => false,  // it's not public, it shouldn't have it's own permalink, and so on
			'publicly_queryable' => false,  // you should be able to query it
			'show_ui' => true,  // you should be able to edit it in wp-admin
			'exclude_from_search' => true,  // you should exclude it from search results
			'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
			'has_archive' => false,  // it shouldn't have archive page
			'rewrite' => false,  // it shouldn't have rewrite rules
			'menu_icon'           => 'dashicons-smiley',
		);
		register_post_type('cf7_to_any_api', $args);
		flush_rewrite_rules(); 
	}

	/**
	 * Register the Custom Meta Boxes
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_metabox(){
	    add_meta_box(
	        'cf7anyapi-setting',
	        __( 'Contact Form 7 Any Api Setting', 'contact-form-to-any-api' ),
	        array($this,'cf7anyapi_settings'),
	        'cf7_to_any_api'
	    );
	}
	
	public function cf7anyapi_add_settings_link($links,$file){
		if($file === 'contact-form-to-any-api/cf7-to-any-api.php' && current_user_can('manage_options')){
			$url = admin_url('edit.php?post_type=cf7_to_any_api');
			$documentation = admin_url('edit.php?post_type=cf7_to_any_api&page=cf7anyapi_docs');
			$links = (array) $links;
			$links[] = sprintf('<a href="%s">%s</a>', $url, __('Settings','contact-form-to-any-api'));
			$links[] = sprintf('<a href="%s">%s</a>', $documentation, __('Documentation','contact-form-to-any-api'));
		}
		return $links;
	}
	
	public function cf7_to_any_api_filter_posts_columns($columns){
		$columns = array(
			'cb' => $columns['cb'],
			'title' => __('Title'),
			'cf7form' => __('Form Name','contact-form-to-any-api'),
			'date' => __('Date','contact-form-to-any-api'),
		);
		return $columns;
	}

	public function cf7_to_any_api_table_content($column_name,$post_id){
		if($column_name == 'cf7form'){
	    	$form_name = get_post_meta($post_id,'cf7anyapi_selected_form',true);
	    	if($form_name){
	    		echo '<a href="' . esc_url( site_url() . '/wp-admin/admin.php?page=wpcf7&post=' . $form_name . '&action=edit' ) . '" target="_blank">' . esc_html( get_the_title( $form_name ) ) . '</a>';

	    	}
	      	
	    }
	}

	public function cf7_to_any_api_sortable_columns($columns){
		$columns['cf7form'] = 'cf7anyapi_selected_form';
  		return $columns;
	}

	/**
	 * Register the Submenu
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_register_submenu(){
	    add_submenu_page(
	        'edit.php?post_type=cf7_to_any_api',
	        __('Logs', 'contact-form-to-any-api'),
	        __('Logs', 'contact-form-to-any-api'),
	        'manage_options',
	        'cf7anyapi_logs',
	        array(&$this,'cf7anyapi_submenu_callback')
	    );

		add_submenu_page(
	        'edit.php?post_type=cf7_to_any_api',
	        __('Entries', 'contact-form-to-any-api'),
	        __('Entries', 'contact-form-to-any-api'),
	        'manage_options',
	        'cf7anyapi_entries',
	        array(&$this,'cf7anyapi_entries_callback')
	    );

	    add_submenu_page(
	        'edit.php?post_type=cf7_to_any_api',
	        __('Documentation', 'contact-form-to-any-api'),
	        __('Documentation', 'contact-form-to-any-api'),
	        'manage_options',
	        'cf7anyapi_docs',
	        array(&$this,'cf7anyapi_submenu_docs_callback')
	    );
	}

	/**
	 * Register Submenu Callback Function
	 *
	 * @since    1.0.0
	 */
	public function cf7anyapi_submenu_callback(){
		$myListTable = new cf7anyapi_List_Table();
	  	echo '<div class="wrap"><h2>' . __( 'CF7 To Any API Log Data', 'contact-form-to-any-api' ) . '</h2>';
    	  	echo wp_nonce_field('cf_to_any_api_log_del_nonce','cf_to_any_api_log_del_nonce' );
	  		echo '<div class="cf7anyapi_log_button">';
	  			echo '<a href="javascript:void(0);" class="cf7anyapi_bulk_log_delete">'.__( 'Delete All Log', 'contact-form-to-any-api' ).'</a>';
	  		echo '</div>';
	  	$myListTable->prepare_items();
	  	$myListTable->display(); 
	  	echo '</div>';
	}

	/**
	 * Delete all log in a one click
	 *
	 * @since    1.0.0
	 */
	public static function cf7_to_any_api_bulk_log_delete_function(){
		if(isset($_POST['cf_to_any_api_log_del_nonce']) && wp_verify_nonce($_POST['cf_to_any_api_log_del_nonce'],'cf_to_any_api_log_del_nonce') )
		{
			global $wpdb;
			$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'cf7anyapi_logs');
		}
		exit();
	}

	public function cf7anyapi_submenu_docs_callback(){
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-display-docs.php';
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
	 * Registered Entries Fields
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_entries_callback(){
		include dirname(__FILE__).'/partials/cf7-to-any-api-admin-entries.php';
	 }

	/**
	 * Update the Metaboxes value on Post Save
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_update_settings($cf7_to_any_api_id,$cf7_to_any_api){
		if($cf7_to_any_api->post_type == 'cf7_to_any_api'){
			$status = 'false';
			if(isset($_POST['cf7_to_any_api_cpt_nonce']) && wp_verify_nonce($_POST['cf7_to_any_api_cpt_nonce'], 'cf7_to_any_api_cpt_nonce')){

				$options['cf7anyapi_selected_form'] = (int)stripslashes($_POST['cf7anyapi_selected_form']);
				$options['cf7anyapi_base_url'] = esc_url_raw($_POST['cf7anyapi_base_url']);
				if(isset($_POST['cf7anyapi_basic_auth'])){
					$options['cf7anyapi_basic_auth'] = sanitize_text_field($_POST['cf7anyapi_basic_auth']);
				}
				if(isset($_POST['cf7anyapi_bearer_auth'])){
					$options['cf7anyapi_bearer_auth'] = sanitize_text_field($_POST['cf7anyapi_bearer_auth']);
				}
				$options['cf7anyapi_input_type'] = sanitize_text_field($_POST['cf7anyapi_input_type']);
				$options['cf7anyapi_method'] = sanitize_text_field($_POST['cf7anyapi_method']);
				$options['cf7anyapi_form_field'] = self::Cf7_To_Any_Api_sanitize_array($_POST['cf7anyapi_form_field']);
				$options['cf7anyapi_header_request'] = sanitize_textarea_field($_POST['cf7anyapi_header_request']);

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
	 * On Metabox Form Change Show that form fields
	 *
	 * @since    1.0.0
	 */
	public static function cf7_to_any_api_get_form_field_function(){
		if(empty((int)stripslashes($_POST['form_id']))){
			echo json_encode('No Fields Found for Selected Form.');
			exit();
		}
		$html = '';
		$form_ID     = (int)stripslashes($_POST['form_id']);
		$post_id     = (int)stripslashes($_POST['post_id']);
		$ContactForm = WPCF7_ContactForm::get_instance($form_ID);
		$form_fields = $ContactForm->scan_form_tags();

		$post_form_id = get_post_meta($post_id,'cf7anyapi_selected_form',true);
		$post_form_field = get_post_meta($post_id,'cf7anyapi_form_field',true);
		
		if(!empty($post_form_field) && $post_form_id == $form_ID){
			foreach($form_fields as $form_fields_key => $form_fields_value){
				if($form_fields_value->basetype != 'submit'){
					$html .= '<div class="cf7anyapi_field">';
						$html .= '<label for="cf7anyapi_'.$form_fields_value->raw_name.'">'.$form_fields_value->name.'</label>';
						$html .= '<input type="text" id="cf7anyapi_'.$form_fields_value->raw_name.'" name="cf7anyapi_form_field['.$form_fields_value->name.']" value="'.$post_form_field[$form_fields_value->raw_name].'" data-basetype="'.$form_fields_value->basetype.'" placeholder="'. __( 'Enter your API side mapping key', 'contact-form-to-any-api' ). '">'; 
					$html .= '</div>';
				}
			}
		}
		else{
			foreach($form_fields as $form_fields_key => $form_fields_value){
				if($form_fields_value->basetype != 'submit'){
					$html .= '<div class="cf7anyapi_field">';
						$html .= '<label for="cf7anyapi_'.$form_fields_value->raw_name.'">'.$form_fields_value->name.'</label>';
						$html .= '<input type="text" id="cf7anyapi_'.$form_fields_value->raw_name.'" name="cf7anyapi_form_field['.$form_fields_value->name.']" data-basetype="'.$form_fields_value->basetype.'" placeholder="'. __( 'Enter your API side mapping key', 'contact-form-to-any-api' ). '">'; 
					$html .= '</div>';
				}
			}
		}
		echo json_encode($html);
		exit();
	}

	public function cf7toanyapi_add_new_table(){
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		global $wpdb;

		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."cf7anyapi_logs'") == $wpdb->prefix."cf7anyapi_logs"){
			$sql = "SELECT COUNT(*) AS a FROM information_schema.COLUMNS WHERE TABLE_NAME = '". $wpdb->prefix."cf7anyapi_logs' AND COLUMN_NAME =  'form_data'";
			$query_data = $wpdb->get_results($sql);
			if($query_data[0]->a == 0){
				$sql2 = 'ALTER TABLE `'.$wpdb->prefix.'cf7anyapi_logs` ADD `form_data` LONGTEXT NOT NULL AFTER `post_id`';
				$wpdb->query($sql2);
			}else{
				$data_type_query = "SHOW COLUMNS FROM `".$wpdb->prefix."cf7anyapi_logs` LIKE 'form_data'";
				$form_data_type = $wpdb->get_row($data_type_query);
				if ($form_data_type && $form_data_type->Type === 'text') {
					$formdata_datatype = "ALTER TABLE `".$wpdb->prefix."cf7anyapi_logs` MODIFY `form_data` LONGTEXT NOT NULL";
					$wpdb->query($formdata_datatype);
				}
			}
		}

		$table = $wpdb->prefix.'cf7anyapi_entry_id';
		if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s%s'", $wpdb->prefix, 'cf7anyapi_entry_id')) != $wpdb->prefix . 'cf7anyapi_entry_id'){
	        $charset_collate = $wpdb->get_charset_collate();
	        $qry = "CREATE TABLE IF NOT EXISTS " . $table . " (
	            id int(11)  PRIMARY KEY NOT NULL AUTO_INCREMENT,
	            Created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
	        )$charset_collate;";
	        dbDelta($qry);
	    }

	    if($wpdb->get_var(sprintf("SHOW TABLES LIKE '%s%s'", $wpdb->prefix, 'cf7anyapi_entries')) != $wpdb->prefix . 'cf7anyapi_entries'){
	    	$charset_collate = $wpdb->get_charset_collate();
	        $table_name2 = $wpdb->prefix.'cf7anyapi_entries';
	        $query = "CREATE TABLE IF NOT EXISTS " . $table_name2 . " (
	            id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	            form_id int(11) ,
	            data_id int(11),
	            field_name varchar(255),
	            field_value varchar(255),
	            date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	            FOREIGN KEY (data_id) REFERENCES ".$table."(id)
	        )$charset_collate;";
	        dbDelta($query);
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
		$cf7_uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'cf7-to-any-api-uploads';
		$form_id = (int)stripslashes($_POST['_wpcf7']);
		if (! is_dir($cf7_uploads_dir)) {
			wp_mkdir_p( $cf7_uploads_dir );
		}
		$submission = WPCF7_Submission::get_instance();
		$posted_data = $submission->get_posted_data();
		$cf7files = $submission->uploaded_files();
		if( !empty($cf7files)){
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
		$posted_data['submit_time'] = date('Y-m-d H:i:s');
		$posted_data['User_IP'] = $_SERVER['REMOTE_ADDR'];		
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
		        
		        $cf7anyapi_form_field = array_filter(get_post_meta(get_the_ID(),'cf7anyapi_form_field',true));
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
		        
		        self::cf7anyapi_send_lead($api_post_array, $cf7anyapi_base_url, $cf7anyapi_input_type, $cf7anyapi_method, $form_id, get_the_ID(), $cf7anyapi_basic_auth, $cf7anyapi_bearer_auth,$cf7anyapi_header_request, $posted_data);
		    }
		}
		wp_reset_postdata();
	}

	public static function cf7anyapi_save_form_submit_data($form_id,$posted_data){
		global $wpdb;
		$table = $wpdb->prefix.'cf7anyapi_entry_id';
		$table2 = $wpdb->prefix.'cf7anyapi_entries';

		$wpdb->insert($table,array('Created' => date("Y-m-d H:i:s")));
		$data_id = (int)$wpdb->insert_id;

		foreach($posted_data as $field => $value){
			$posted_value = (is_array($value) ? implode(',',$value) : $value);
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

	/**
	 * Child Fuction of specific form data send to the API
	 *
	 * @since    1.0.0
	 */
	public static function cf7anyapi_send_lead($data, $url, $input_type, $method, $form_id, $post_id, $basic_auth = '', $bearer_auth = '',$header_request = '', $posted_data = ''){
		
		global $wp_version;

		if($method == 'GET' && ($input_type == 'params' || $input_type == 'json')){
			$args = array(
				'timeout'     => 5,
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

			if($input_type == 'params'){
				$data_string = http_build_query($data);

        		$url = stripos($url,'?') !== false ? $url.'&'.$data_string : $url.'?'.$data_string;
			}
			else{
				$args['headers']['Content-Type'] = 'application/json';
        		$json = self::Cf7_To_Any_Api_parse_json($data);

        		if(is_wp_error($json)){
          			return $json;
        		}
        		else{
          			$args['body'] = $json;
        		}
			}
			
			$result = wp_remote_retrieve_body(wp_remote_get($url, $args));
      		self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result, $posted_data);
		}
		else{
			$args = array(
				'timeout'     => 5,
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
			if(!empty($result_body)){
				$result = $result_body;
			}
      		self::Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $result, $posted_data);
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
  	public static function Cf7_To_Any_Api_save_response_in_log($post_id, $form_id, $response, $posted_data){
  		global $wpdb;
  		$table = $wpdb->prefix.'cf7anyapi_logs';

  		// Base64 image get only 10 characters
  		if(isset($posted_data)){
  			foreach($posted_data as $key => $arr){
				if(strstr($key, 'file-')){
					$posted_data[$key] = mb_substr($arr, 0, 10).'...';
			    }
			}
  		}
  		
  		$form_data = json_encode($posted_data, JSON_UNESCAPED_UNICODE);
  		if (gettype($response) != 'string') {
			$response = json_encode($response, JSON_UNESCAPED_UNICODE);
		}
  		$data = array(
  			'form_id' => $form_id,
  			'post_id' => $post_id,
  			'form_data' => $form_data,
  			'log' => $response,
  		);

  		$wpdb->insert($table,$data);
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
}