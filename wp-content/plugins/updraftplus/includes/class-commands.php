<?php

if (!defined('ABSPATH')) die('No direct access allowed');

/*
	- A container for all the remote commands implemented. Commands map exactly onto method names (and hence this class should not implement anything else, beyond the constructor, and private methods)
	- Return format is either to return data (boolean, string, array), or an WP_Error object
	Commands are not allowed to begin with an underscore. So, any private methods can be prefixed with an underscore.
	TODO: Many of these just verify input, and then call back into a relevant method in UpdraftPlus_Admin. Once all commands have been ported over to go via this class, those methods in UpdraftPlus_Admin can generally be folded into the relevant method in here, and removed from UpdraftPlus_Admin. (Since this class is intended to become the official way of performing actions). As a bonus, we then won't need so much _load_ud(_admin) boilerplate.
*/

if (class_exists('UpdraftPlus_Commands')) return;

class UpdraftPlus_Commands {

	private $_uc_helper;

	/**
	 * Constructor
	 *
	 * @param Class $uc_helper The 'helper' needs to provide the method _updraftplus_background_operation_started
	 */
	public function __construct($uc_helper) {
		$this->_uc_helper = $uc_helper;
	}

	/**
	 * Get the Advanced Tools HTMl and return to Central
	 *
	 * @param  string $options Options for advanced settings
	 * @return string
	 */
	public function get_advanced_settings($options) {
		// load global updraftplus and admin
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.

		$html = $updraftplus_admin->settings_advanced_tools(true, array('options' => $options));
		
		return $html;
	}

	public function get_download_status($items) {
		// load global updraftplus and admin
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		if (!is_array($items)) $items = array();

		return $updraftplus_admin->get_download_statuses($items);
	
	}
	
	/**
	 * Begin a download process
	 *
	 * @param Array $downloader_params - download parameters (findex, type, timestamp, stage)
	 *
	 * @return Array - as from UpdraftPlus_Admin::do_updraft_download_backup() (with 'request' key added, with value $downloader_params)
	 */
	public function downloader($downloader_params) {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$findex = $downloader_params['findex'];
		$type = $downloader_params['type'];
		$timestamp = $downloader_params['timestamp'];
		// Valid stages: 2='spool the data'|'delete'='delete local copy'|anything else='make sure it is present'
		$stage = empty($downloader_params['stage']) ? false : $downloader_params['stage'];
	
		// This may, or may not, return, depending upon whether the files are already downloaded
		// The response is usually an array with key 'result', and values deleted|downloaded|needs_download|download_failed
		$response = $updraftplus_admin->do_updraft_download_backup($findex, $type, $timestamp, $stage, array($this->_uc_helper, '_updraftplus_background_operation_started'));
	
		if (is_array($response)) {
			$response['request'] = $downloader_params;
		}
	
		return $response;
	}
	
	public function delete_downloaded($set_info) {
		$set_info['stage'] = 'delete';
		return $this->downloader($set_info);
	}
	
	/**
	 * Get backup progress (as HTML) for a particular backup
	 *
	 * @param Array $params - should have a key 'job_id' with corresponding value
	 *
	 * @return String - the HTML
	 */
	public function backup_progress($params) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$request = array(
			'thisjobonly' => $params['job_id']
		);
		$activejobs_list = $updraftplus_admin->get_activejobs_list($request);
		
		return $activejobs_list;
	
	}
	
	/**
	 * Perform a backup
	 *
	 * @param Array $params
	 *
	 * @uses die()
	 *
	 * @return WP_Error - or, may die()
	 */
	public function backupnow($params) {
		
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!empty($params['updraftplus_clone_backup'])) {
			add_filter('updraft_backupnow_options', array($updraftplus, 'updraftplus_clone_backup_options'), 10, 2);
			add_filter('updraftplus_initial_jobdata', array($updraftplus, 'updraftplus_clone_backup_jobdata'), 10, 3);
		}

		if (!empty($params['db_anon_all']) || !empty($params['db_anon_non_staff']) || !empty($params['db_anon_wc_orders'])) {
			if (!class_exists('UpdraftPlus_Anonymisation_Functions')) updraft_try_include_file('addons/anonymisation.php', 'include_once');

			add_filter('updraft_backupnow_options', 'UpdraftPlus_Anonymisation_Functions::updraftplus_backup_anonymisation_options', 10, 2);
			add_filter('updraftplus_initial_jobdata', 'UpdraftPlus_Anonymisation_Functions::updraftplus_backup_anonymisation_jobdata', 10, 2);
		}

		$background_operation_started_method_name = empty($params['background_operation_started_method_name']) ? '_updraftplus_background_operation_started' : $params['background_operation_started_method_name'];
		$updraftplus_admin->request_backupnow($params, array($this->_uc_helper, $background_operation_started_method_name));
		
		// Control returns when the backup finished; but, the browser connection should have been closed before
		die;
	}
	
	/**
	 * Mark a backup as "do not delete"
	 *
	 * @param array $params this is an array of parameters sent via ajax it can include the following:
	 * backup_key - Integer - backup timestamp
	 * always_keep - Boolean - "Always keep" value
	 * @return array which contains rawbackup html
	 */
	public function always_keep_this_backup($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		$backup_key = $params['backup_key'];
		$backup_history = UpdraftPlus_Backup_History::get_history();
		if (empty($params['always_keep'])) {
			unset($backup_history[$backup_key]['always_keep']);
		} else {
			$backup_history[$backup_key]['always_keep'] = true;
		}
		UpdraftPlus_Backup_History::save_history($backup_history);
		$nonce = $backup_history[$backup_key]['nonce'];
		$rawbackup = $updraftplus_admin->raw_backup_info($backup_history, $backup_key, $nonce);
		return array(
			'rawbackup' => html_entity_decode($rawbackup),
		);
	}

	/**
	 * Function to retrieve raw backup history given a timestamp and nonce
	 *
	 * @param Array $data - Data parameter; keys: timestamp, nonce
	 *
	 * @return Array - An array that contains the raw backup history
	 */
	public function rawbackup_history($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		$history = UpdraftPlus_Backup_History::get_history();

		$rawbackup = $updraftplus_admin->raw_backup_info($history, $data['timestamp'], $data['nonce'], null);

		return array(
			'rawbackup' => html_entity_decode($rawbackup),
		);
	}

	/**
	 * Function to retrieve list of existing backups with all of their data.
	 *
	 * @return Array - Array of existing backup data.
	 */
	public function get_existing_backups_data() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === $this->_load_ud()) return new WP_Error('no_updraftplus');
		$history = UpdraftPlus_Backup_History::get_history();

		return array(
			'history' => $history,
			'download_data' => $updraftplus_admin->get_download_buttons_data($history),
		);
	}
	
	private function _load_ud() {
		global $updraftplus;
		return is_a($updraftplus, 'UpdraftPlus') ? $updraftplus : false;
	}
	
	private function _load_ud_admin() {
		if (!defined('UPDRAFTPLUS_DIR') || !is_file(UPDRAFTPLUS_DIR.'/admin.php')) return false;
		updraft_try_include_file('admin.php', 'include_once');
		global $updraftplus_admin;
		return $updraftplus_admin;
	}
	
	public function get_log($job_id = '') {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
	
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if ('' != $job_id && !preg_match("/^[0-9a-f]{12}$/", $job_id)) return new WP_Error('updraftplus_permission_invalid_jobid');
		
		return $updraftplus_admin->fetch_log($job_id);
	
	}
	
	public function activejobs_delete($job_id) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->activejobs_delete((string) $job_id);

	}
	
	public function deleteset($what) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = $updraftplus_admin->delete_set($what);
	
		$get_history_opts = isset($what['get_history_opts']) ? $what['get_history_opts'] : array();
	
		$backup_history = UpdraftPlus_Backup_History::get_history();
	
		$results['history'] = $updraftplus_admin->settings_downloading_and_restoring($backup_history, true, $get_history_opts);

		$results['backupnow_file_entities'] = apply_filters('updraftplus_backupnow_file_entities', array());
		$results['modal_afterfileoptions'] = apply_filters('updraft_backupnow_modal_afterfileoptions', '', '');
		
		$results['count_backups'] = count($backup_history);

		return $results;
	
	}
	
	/**
	 * Slightly misnamed - this doesn't always rescan, but it does always return the history status (possibly after a rescan)
	 *
	 * @param  Array|String $data - with keys 'operation' and 'debug'; or, if a string (backwards compatibility), just the value of the 'operation' key (with debug assumed as 0)
	 *
	 * @return Array - returns an array of history statuses
	 */
	public function rescan($data) {

		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$backup_count = 0;

		if (is_array($data)) {
			$operation = empty($data['operation']) ? '' : $data['operation'];
			$debug = !empty($data['debug']);
			$backup_count = empty($data['backup_count']) ? 0 : $data['backup_count'];
		} else {
			$operation = $data;
			$debug = false;
		}
	
		$remotescan = ('remotescan' == $operation);
		$rescan = ($remotescan || 'rescan' == $operation);
		
		$history_status = $updraftplus_admin->get_history_status($rescan, $remotescan, $debug, $backup_count);
		$history_status['backupnow_file_entities'] = apply_filters('updraftplus_backupnow_file_entities', array());
		$history_status['modal_afterfileoptions'] = apply_filters('updraft_backupnow_modal_afterfileoptions', '', '');

		return $history_status;
		
	}
	
	public function get_settings($options) {
		global $updraftplus;
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		ob_start();
		$updraftplus_admin->settings_formcontents($options);
		$output = ob_get_contents();
		ob_end_clean();
		
		$remote_storage_options_and_templates = UpdraftPlus_Storage_Methods_Interface::get_remote_storage_options_and_templates();
		
		$is_premium = false;
		if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/udaddons')) $is_premium = true;

		return array(
			'settings' => $output,
			'remote_storage_options' => $remote_storage_options_and_templates['options'],
			'remote_storage_templates' => $remote_storage_options_and_templates['templates'],
			'remote_storage_partial_templates' => $remote_storage_options_and_templates['partial_templates'],
			'meta' => apply_filters('updraftplus_get_settings_meta', array()),
			'updraftplus_version' => $updraftplus->version,
			'backup_methods' => $updraftplus->backup_methods,
			'active_instances' => $updraftplus_admin->get_active_remote_storages(),
			'is_premium' => $is_premium,
		);
		
	}
	
	/**
	 * Run a credentials test
	 *
	 * @param Array $test_data - test configuration
	 *
	 * @return WP_Error|Array - test results (keys: results, (optional)data), or an error
	 */
	public function test_storage_settings($test_data) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = $updraftplus_admin->do_credentials_test($test_data, true);
	
		return $results;
	
	}
	
	/**
	 * Perform a connection test on a database
	 *
	 * @param Array $info - test parameters
	 *
	 * @return Array - test results
	 */
	public function extradb_testconnection($info) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
	
		$results = apply_filters('updraft_extradb_testconnection_go', array(), $info);
	
		return $results;
	
	}
	
	/**
	 * This method will make a call to the methods responsible for recounting the quota in the UpdraftVault account
	 *
	 * @param  array $params - an array of parameters such as a instance_id
	 * @return string - the result of the call
	 */
	public function vault_recountquota($params = array()) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$instance_id = empty($params['instance_id']) ? '' : $params['instance_id'];

		$vault = $updraftplus_admin->get_updraftvault($instance_id);

		$return_data_only = UpdraftPlus_Manipulation_Functions::fetch_superglobal('request', 'return_data_only', false);

		return $vault->ajax_vault_recountquota(false, $return_data_only);
	}
	
	/**
	 * This method will make a call to the methods responsible for creating a connection to UpdraftVault
	 *
	 * @param  array $credentials - an array of parameters such as the user credentials and instance_id
	 * @return string - the result of the call
	 */
	public function vault_connect($credentials) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$instance_id = empty($credentials['instance_id']) ? '' : $credentials['instance_id'];

		$return_data_only = isset($credentials['return_data_only']) && $credentials['return_data_only'] ? true : false;

		return $updraftplus_admin->get_updraftvault($instance_id)->ajax_vault_connect(false, $credentials, $return_data_only);
	}
	
	/**
	 * This method will make a call to the methods responsible for removing a connection to UpdraftVault
	 *
	 * @param array $params - an array of parameters such as a instance_id
	 * @return string - the result of the call
	 */
	public function vault_disconnect($params = array()) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$echo_results = empty($params['immediate_echo']) ? false : true;

		$instance_id = empty($params['instance_id']) ? '' : $params['instance_id'];
		
		$results = (array) $updraftplus_admin->get_updraftvault($instance_id)->ajax_vault_disconnect($echo_results);

		return $results;
	
	}
	
	/**
	 * A handler method to call the UpdraftPlus admin save settings method. It will check if the settings passed to it are in the format of a string if so it converts it to an array otherwise just pass the array
	 *
	 * @param  String/Array $settings Settings to be saved to UpdraftPlus either in the form of a string ready to be converted to an array or already an array ready to be passed to the save settings function in UpdraftPlus.
	 * @return Array An Array response to be sent back
	 */
	public function save_settings($settings) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!empty($settings)) {

			if (is_string($settings)) {
				parse_str($settings, $settings_as_array);
			} elseif (is_array($settings)) {
				$settings_as_array = $settings;
			} else {
				return new WP_Error('invalid_settings');
			}
		}
		
		$results = $updraftplus_admin->save_settings($settings_as_array);

		return $results;
	
	}
	
	public function s3_newuser($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$results = apply_filters('updraft_s3_newuser_go', array(), $data);
		
		return $results;
	}
	
	public function cloudfiles_newuser($data) {
	
		global $updraftplus_addon_cloudfilesenhanced;
		if (!is_a($updraftplus_addon_cloudfilesenhanced, 'UpdraftPlus_Addon_CloudFilesEnhanced')) {
			// translators: %s: The name of the missing add-on.
			$data = array('e' => 1, 'm' => sprintf(__('%s add-on not found', 'updraftplus'), 'Rackspace Cloud Files'));
		} else {
			$data = $updraftplus_addon_cloudfilesenhanced->create_api_user($data);
		}
		
		if (0 === $data['e']) {
			return $data;
		} else {
			return new WP_Error('error', '', $data);
		}
	}
	
	/**
	 * Get an HTML fragment
	 *
	 * @param String|Array $fragment - what fragment to fetch. If an array, the fragment identifier is in 'fragment' (and 'data' is associated data)
	 *
	 * @return Array|WP_Error
	 */
	public function get_fragment($fragment) {
	
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (is_array($fragment)) {
			$data = $fragment['data'];
			$fragment = $fragment['fragment'];
		}
		
		$error = false;
		
		switch ($fragment) {
		
			case 'last_backup_html':
					$output = $updraftplus_admin->last_backup_html();
				break;
			
			case 's3_new_api_user_form':
				ob_start();
				do_action('updraft_s3_print_new_api_user_form', false);
				$output = ob_get_contents();
				ob_end_clean();
				break;
				
			case 'cloudfiles_new_api_user_form':
				global $updraftplus_addon_cloudfilesenhanced;
				if (!is_a($updraftplus_addon_cloudfilesenhanced, 'UpdraftPlus_Addon_CloudFilesEnhanced')) {
						$error = true;
						$output = 'cloudfiles_addon_not_found';
				} else {
					$output = array(
						'accounts' => $updraftplus_addon_cloudfilesenhanced->account_options(),
						'regions' => $updraftplus_addon_cloudfilesenhanced->region_options(),
					);
				}
				break;
			
			case 'backupnow_modal_contents':
				$updraft_dir = $updraftplus->backups_dir_location();
				if (!UpdraftPlus_Filesystem_Functions::really_is_writable($updraft_dir)) {
					$output = array('error' => true, 'html' => __("The 'Backup Now' button is disabled as your backup directory is not writable (go to the 'Settings' tab and find the relevant option).", 'updraftplus'));
				} else {
					$output = array('html' => $updraftplus_admin->backupnow_modal_contents(), 'backupnow_file_entities' => apply_filters('updraftplus_backupnow_file_entities', array()), 'incremental_installed' => apply_filters('updraftplus_incremental_addon_installed', false));
				}
				break;
			
			case 'panel_download_and_restore':
				$backup_history = UpdraftPlus_Backup_History::get_history();
				$output = $updraftplus_admin->settings_downloading_and_restoring($backup_history, true, $data);
				break;
			
			case 'disk_usage':
				$output = UpdraftPlus_Filesystem_Functions::get_disk_space_used($data);
				break;
			default:
				// We just return a code - translation is done on the other side
				$output = 'ud_get_fragment_could_not_return';
				$error = true;
				break;
		}
		
		if (!$error) {
			return array(
				'output' => $output,
			);
		} else {
			return new WP_Error('get_fragment_error', '', $output);
		}
		
	}
	
	/**
	 * This gets the http_get function from admin to grab information on a url
	 *
	 * @param  string $uri URL to be used
	 * @return array returns response from specific URL
	 */
	public function http_get($uri) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (empty($uri)) {
			return new WP_Error('error', '', 'no_uri');
		}
		
		$response = $updraftplus_admin->http_get($uri, false);
		$response_decode = json_decode($response);

		if (isset($response_decode->e)) {
		  return new WP_Error('error', '', htmlspecialchars($response_decode->e));
		}

		return array('status' => $response_decode->code, 'response' => $response_decode->html_response);
	}

	/**
	 * This gets the http_get function from admin to grab cURL information on a url
	 *
	 * @param  string $uri URL to be used
	 * @return array
	 */
	public function http_get_curl($uri) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		if (empty($uri)) {
			return new WP_Error('error', '', 'no_uri');
		}
		
		if (!function_exists('curl_exec')) {
			return new WP_Error('error', '', 'no_curl');
		}
		
		$response_encode = $updraftplus_admin->http_get($uri, true);
		$response_decode = json_decode($response_encode);

		$response = 'Curl Info: ' . $response_decode->verb
					.'Response: ' . $response_decode->response;

		if (false === $response_decode->response) {
			return new WP_Error('error', '', array(
				'error' => htmlspecialchars($response_decode->e),
				"status" => $response_decode->status,
				"log" => htmlspecialchars($response_decode->verb)
			));
		}
		
		return array(
			'response'=> htmlspecialchars(substr($response, 0, 2048)),
			'status'=> $response_decode->status,
			'log'=> htmlspecialchars($response_decode->verb)
		);
	}

	/**
	 * Display raw backup and file list
	 *
	 * @return string
	 */
	public function show_raw_backup_and_file_list() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		/*
			Need to remove the pre tags as the modal assumes a <pre> is for a new box.
			This cause issues specifically with fetch log events. Do this by passing true
			to the method show_raw_backups
		 */
		
		$response = $updraftplus_admin->show_raw_backups(true);

		return $response['html'];
	}

	public function reset_site_id() {
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		delete_site_option('updraftplus-addons_siteid');
		return $updraftplus->siteid();
	}

	public function search_replace($query) {

		if (!class_exists('UpdraftPlus_Addons_Migrator')) {
			return new WP_Error('error', '', 'no_class_found');
		}
		
		global $updraftplus_addons_migrator;
		
		if (!is_a($updraftplus_addons_migrator, 'UpdraftPlus_Addons_Migrator')) {
			return new WP_Error('error', 'no_object_found');
		}

		$_POST = $query;
		
		ob_start();

		do_action('updraftplus_adminaction_searchreplace', $query);
		
		$response = array('log' => ob_get_clean());
		
		return $response;
	}

	/**
	 * Change lock settings for UpdraftPlus admin access
	 *
	 * @param array $data Lock settings data
	 * @return string|WP_Error Success message or error
	 */
	public function change_lock_settings($data) {
		global $updraftplus_addon_lockadmin;
		
		if (!class_exists('UpdraftPlus_Addon_LockAdmin')) {
			return new WP_Error('error', '', 'no_class_found');
		}
		
		if (!is_a($updraftplus_addon_lockadmin, "UpdraftPlus_Addon_LockAdmin")) {
			return new WP_Error('error', '', 'no_object_found');
		}

		$session_length = empty($data["session_length"]) ? '' : $data["session_length"];
		$password 		= empty($data["password"]) ? '' : $data["password"];
		$old_password 	= empty($data["old_password"]) ? '' : $data["old_password"];
		$support_url 	= $data["support_url"];
		
		$user = wp_get_current_user();
		if (0 == $user->ID) {
			return new WP_Error('no_user_found');
		}
		
		$options = $updraftplus_addon_lockadmin->return_opts();

		if ($old_password == $options['password']) {
			
			$options['password'] = (string) $password;
			$options['support_url'] = (string) $support_url;
			$options['session_length'] = (int) $session_length;
			UpdraftPlus_Options::update_updraft_option('updraft_adminlocking', $options);
						
			return "lock_changed";
		} else {
			return new WP_Error('error', '', 'wrong_old_password');
		}
	}

	/**
	 * Get lock admin settings data
	 *
	 * @return array Lock settings information
	 */
	private function get_lock_settings_data() {
		if (!UpdraftPlus_Options::user_can_manage()) {
			return new WP_Error('updraftplus_permission_denied');
		}
		
		global $updraftplus_addon_lockadmin;
		if (is_a($updraftplus_addon_lockadmin, "UpdraftPlus_Addon_LockAdmin")) {
			$options = $updraftplus_addon_lockadmin->return_opts();
			return array(
				'has_lock_admin' => true,
				'current_password' => $options['password'],
				'session_length' => $options['session_length'],
				'support_url' => $options['support_url'],
				'session_length_options' => $updraftplus_addon_lockadmin->get_session_length_options()
			);
		}
		
		return array('has_lock_admin' => false);
	}

	public function delete_key($key_id) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = $updraftcentral_main->delete_key($key_id);
		return $response;
		
	}
	
	public function create_key($data) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = call_user_func(array($updraftcentral_main, 'create_key'), $data);
		
		return $response;
	}
	
	public function fetch_log($data) {
		global $updraftcentral_main;

		if (!is_a($updraftcentral_main, 'UpdraftCentral_Main')) {
			return new WP_Error('error', '', 'UpdraftCentral_Main object not found');
		}
		
		$response = call_user_func(array($updraftcentral_main, 'get_log'), $data);
		return $response;
	}

	/**
	 * A handler method to call the UpdraftPlus admin auth_remote_method
	 *
	 * @param Array - $data It consists of below key elements:
	 *                $remote_method - Remote storage service
	 *                $instance_id - Remote storage instance id
	 * @return Array An Array response to be sent back
	 */
	public function auth_remote_method($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$response = $updraftplus_admin->auth_remote_method($data);
		return $response;
	}

	/**
	 * A handler method to call the UpdraftPlus admin deauth_remote_method
	 *
	 * @param Array - $data It consists of below key elements:
	 *                $remote_method - Remote storage service
	 *                $instance_id - Remote storage instance id
	 * @return Array An Array response to be sent back
	 */
	public function deauth_remote_method($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		$response = $updraftplus_admin->deauth_remote_method($data);
		return $response;
	}

	/**
	 * A handler method to call the relevant remote storage manual authentication methods and return the authentication result
	 *
	 * @param array $data - an array of authentication data, normally includes the state and auth code
	 *
	 * @return array - an array response to be sent back to the frontend
	 */
	public function manual_remote_storage_authentication($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.

		$response = array(
			'result' => 'success'
		);

		$method = $data['method'];

		$enabled_services = UpdraftPlus_Storage_Methods_Interface::get_enabled_storage_objects_and_ids(array($method));
		
		if (empty($enabled_services[$method]['object']) || empty($enabled_services[$method]['instance_settings']) || !$enabled_services[$method]['object']->supports_feature('manual_authentication')) {
			$response['result'] = 'error';
			$response['data'] = __('Manual authentication is not available for this remote storage method', 'updraftplus') . '(' . $method . ')';
			return $response;
		}

		$backup_obj = $enabled_services[$method]['object'];

		$auth_data = json_decode(base64_decode($data['auth_data']), true);
		$instance_id = '';

		$state = isset($auth_data['state']) ? urldecode($auth_data['state']) : '';
		$code = isset($auth_data['code']) ? urldecode($auth_data['code']) : '';

		if (empty($code) && isset($auth_data['access_token']) && isset($auth_data['user_id'])) {
			// If there is no code, but the access_token and user_id is set then this is for Google Drive so create a code array using these values
			$access_token = urldecode($auth_data['access_token']);
			$user_id = urldecode($auth_data['user_id']);
			$code = array(
				'access_token' => $access_token,
				'user_id' => $user_id
			);
		} elseif (empty($code) && isset($auth_data['token'])) {
			// If there is no code, but a token is set then this is for OneDrive so assign token to code
			$encoded_token = stripslashes($auth_data['token']);
			$token = json_decode($encoded_token);
			$code = $token;
		}

		if (empty($state) || empty($code)) {
			$response['result'] = 'error';
			$response['data'] = __('Missing authentication data:', 'updraftplus') . " ({$state}) ({$code})";
			return $response;
		}

		if (false !== strpos($state, ':')) {
			$parts = explode(':', $state);
			$instance_id = $parts[1];
		}

		if (empty($instance_id)) {
			$response['result'] = 'error';
			$response['data'] = __('Missing instance id:', 'updraftplus') . " ($state)";
			return $response;
		}

		if (isset($enabled_services[$method]['instance_settings'][$instance_id])) {
			$opts = $enabled_services[$method]['instance_settings'][$instance_id];
			$backup_obj->set_options($opts, false, $instance_id);
		}

		$result = $backup_obj->complete_authentication($state, $code, true);
		
		$response['data'] = $result;

		return $response;
	}
	
	/**
	 * A handler method to call the UpdraftPlus admin wipe settings method
	 *
	 * @return Array An Array response to be sent back
	 */
	public function wipe_settings() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		// pass false to this method so that it does not remove the UpdraftCentral key
		$response = $updraftplus_admin->wipe_settings(false);

		return $response;
	}

	/**
	 * Retrieves backup information (next scheduled backups, last backup jobs and last log message)
	 * for UpdraftCentral consumption
	 *
	 * @return Array An array containing the results of the backup information retrieval
	 */
	public function get_backup_info() {
		try {
			
			// load global updraftplus admin
			if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

			ob_start();
			$updraftplus_admin->next_scheduled_backups_output();
			$next_scheduled_backups = ob_get_clean();

			$response = array(
				'next_scheduled_backups' => $next_scheduled_backups,
				'last_backup_job' => $updraftplus_admin->last_backup_html(),
				'last_log_message' => UpdraftPlus_Options::get_updraft_lastmessage()
			);

			$updraft_last_backup = UpdraftPlus_Options::get_updraft_option('updraft_last_backup', false);
			$backup_history = UpdraftPlus_Backup_History::get_history();
			
			if (false !== $updraft_last_backup && !empty($backup_history)) {
				$backup_nonce = $updraft_last_backup['backup_nonce'];

				$response['backup_nonce'] = $backup_nonce;
				$response['log'] = $this->get_log($backup_nonce);
			}

		} catch (Exception $e) {
			$response = array('error' => true, 'message' => $e->getMessage());
		}

		return $response;
	}

	/**
	 * This method will check the connection status to UpdraftPlus.com using the submitted credentials and return the result of that check.
	 *
	 * @param  array $data - an array that contains the users UpdraftPlus.com credentials
	 *
	 * @return array       - an array with the result of the connection status
	 */
	public function updraftplus_com_login_submit($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		
		global $updraftplus_addons2, $updraftplus;

		$options = $updraftplus_addons2->get_option(UDADDONS2_SLUG.'_options');
		$new_options = $data['data'];
		
		// Check if we can make a connection if we can then we don't want to reset the options in the case where the user has removed their password from the form
		$result = !empty($options['email']) ? $updraftplus_addons2->connection_status() : false;
		
		if (true !== $result) {
			// We failed to make a connection so try the new options
			$updraftplus_addons2->update_option(UDADDONS2_SLUG.'_options', $new_options);
			$result = $updraftplus_addons2->connection_status();
		}

		if (true !== $result) {
			if (is_wp_error($result)) {
				$connection_errors = array();
				foreach ($result->get_error_messages() as $msg) {
					$connection_errors[] = $msg;
				}
			} else {
				if (!empty($options['email']) && !empty($options['password'])) $connection_errors = array(__('An unknown error occurred when trying to connect to UpdraftPlus.Com', 'updraftplus'));
			}
			$result = false;
		}
		if ($result && isset($new_options['auto_update'])) {
			$updraftplus->set_automatic_updates($new_options['auto_update']);
		}
		if ($result) {
			return array(
				'success' => true
			);
		} else {
			// There was an error reset the options so that we don't get unwanted notices on the dashboard.
			$updraftplus_addons2->update_option(UDADDONS2_SLUG.'_options', array('email' => '', 'password' => ''));

			return array(
				'error' => true,
				'message' => $connection_errors
			);
		}
	}

	/**
	 * This method will create UI for a list of local backups that can be remote sent and return it
	 *
	 * @return array      - an array with the results from the backup search
	 */
	public function get_backup_list() {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');

		$response = array(
			'result' => 'success'
		);

		$output = '';
		$backup_history = UpdraftPlus_Backup_History::get_history();
		
		foreach ($backup_history as $key => $backup) {
			$backup_complete = $updraftplus_admin->check_backup_is_complete($backup, false, false, true);
			$remote_sent = !empty($backup['service']) && ((is_array($backup['service']) && in_array('remotesend', $backup['service'])) || 'remotesend' === $backup['service']);
			if (!$backup_complete || $remote_sent) unset($backup_history[$key]);
		}

		if (!empty($backup_history)) {

			$output .= '<p class="updraftplus-option updraftplus-option-inline updraft-remote_send">';
			$output .= ' <span class="updraftplus-option-label">'.__('Backup:', 'updraftplus').'</span> ';
			$output .= '<select id="updraftplus_remote_send_backup_options" name="updraftplus_remote_send_backup_options">';

			foreach ($backup_history as $key => $backup) {
				$total_size = round($updraftplus->get_total_backup_size($backup) / 1073741824, 1);
				$pretty_date = get_date_from_gmt(gmdate('Y-m-d H:i:s', (int) $key), 'M d, Y G:i');
				$label = isset($backup['label']) ? ' ' . $backup['label'] : '';
				$output .= '<option value="'.$key. '" data-nonce="'.$backup['nonce'].'" data-timestamp="'.$key.'" data-size="'.$total_size.'">' . $pretty_date . $label . '</option>';
			}
			$output .= '</select>';
			$output .= ' <button class="button button-primary" onclick="updraft_migrate_go_existing_backup()">'.__('Send', 'updraftplus').'</button>';
			$output .= '</p>';
		} else {
			$output .= '<p>'.__('You have no local backups to send.', 'updraftplus').'</p>';
		}

		$response['data'] = $output;

		return $response;
	}
	
	/**
	 * This function will add some needed filters in order to be able to send a local backup to remote storage it will then boot the backup process.
	 *
	 * @param array $data - data sent from the front end, it includes the backup timestamp and nonce
	 *
	 * @return array      - the response to be sent back to the front end
	 */
	public function upload_local_backup($data) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		add_filter('updraftplus_initial_jobdata', array($updraftplus_admin, 'upload_local_backup_jobdata'), 10, 3);
		add_filter('updraftplus_get_backup_file_basename_from_time', array($updraftplus_admin, 'upload_local_backup_name'), 10, 3);
		
		$background_operation_started_method_name = empty($data['background_operation_started_method_name']) ? '_updraftplus_background_operation_started' : $data['background_operation_started_method_name'];

		$msg = array(
			'nonce' => $data['use_nonce'],
			'm' => apply_filters('updraftplus_backupnow_start_message', '<strong>'.__('Start backup', 'updraftplus').':</strong> '.htmlspecialchars(__('OK.', 'updraftplus').' '.__('You should soon see activity in the "Last log message" field below.', 'updraftplus')), $data['use_nonce'])
		);

		$close_connection_callable = array($this->_uc_helper, $background_operation_started_method_name);

		if (is_callable($close_connection_callable)) {
			call_user_func($close_connection_callable, $msg);
		} else {
			$updraftplus->close_browser_connection(json_encode($msg));
		}

		do_action('updraft_backupnow_backup_all', apply_filters('updraft_backupnow_options', $data, array()));

		// Control returns when the backup finished; but, the browser connection should have been closed before
		die;
	}

	/**
	 * Pre-check before sending request and delegates login request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftcentral_login($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->get_updraftcentral_cloud()->ajax_process_login($params);
	}

	/**
	 * Pre-check before sending request and delegates registration request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftcentral_registration($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		return $updraftplus_admin->get_updraftcentral_cloud()->ajax_process_registration($params);
	}

	/**
	 * Pre-check before sending request and delegates login request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return string - the result of the call
	 */
	public function process_updraftplus_clone_login($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (!defined('UPDRAFTPLUS_DO_NOT_USE_IPINFO') || !UPDRAFTPLUS_DO_NOT_USE_IPINFO) {
			// Try to get this website's hosting region code (from the server's outbound IP via IPInfo), so we can find the closest clone region.
			$response = wp_remote_get('https://ipinfo.io/json', array(
				'timeout' => 3,
				// The API always returns 429 rate limit unless this header is passed
				'headers' => array(
					'Referer' => network_site_url()
				)
			));
			
			if (200 === wp_remote_retrieve_response_code($response)) {
				$body = wp_remote_retrieve_body($response);
				$response = json_decode($body, true);
				if (isset($response['country'])) $params['form_data']['country_code'] = $response['country'];
			}
		}
		
		$response = $updraftplus->get_updraftplus_clone()->ajax_process_login($params, false);
		
		if (isset($response['status']) && 'authenticated' == $response['status']) {
			UpdraftPlus::load_checkout_embed();

			global $updraftplus_checkout_embed;
			$checkout_url = $updraftplus->get_url('buy_clone_tokens');
			if (is_a($updraftplus_checkout_embed, 'Updraft_Checkout_Embed')) {
				$checkout_url = $updraftplus_checkout_embed->get_product('updraftplus-clone-tokens', UpdraftPlus_Options::admin_page_url().'?page=updraftplus&tab=migrate');
			}

			$tokens = isset($response['tokens']) ? $response['tokens'] : 0;
			$content = '<div class="updraftclone-main-row">';
			$content .= '<div class="updraftclone-tokens">';
			$content .= '<p>' . __("Available temporary clone tokens:", "updraftplus") . ' <span class="tokens-number">' . esc_html($tokens) . '</span></p>';
			$content .= '<p><a href="'.esc_attr($checkout_url).'">'.__('You can buy more temporary clone tokens here.', 'updraftplus').'</a></p>';
			$content .= '</div>';
			
			if (0 != $response['tokens']) {
				$is_vps_tester = !empty($response['is_vps_tester']);
				$supported_wp_versions = isset($response['supported_wp_versions']) ? $response['supported_wp_versions'] : array();
				$supported_packages = isset($response['supported_packages']) ? $response['supported_packages'] : array();
				$supported_packages_label = isset($response['supported_packages_label']) ? $response['supported_packages_label'] : array();
				$supported_regions = isset($response['supported_regions']) ? $response['supported_regions'] : array();
				$nearest_region = isset($response['nearest_region']) ? $response['nearest_region'] : '';
				$content .= '<div class="updraftclone_action_box">';
				$content .= $updraftplus_admin->updraftplus_clone_ui_widget($is_vps_tester, $supported_wp_versions, $supported_packages, $supported_regions, $nearest_region, $supported_packages_label);
				$content .= '<p class="updraftplus_clone_status"></p>';
				$content .= '<button id="updraft_migrate_createclone" class="button button-primary button-hero" data-clone_id="'.$response['clone_info']['id'].'" data-secret_token="'.$response['clone_info']['secret_token'].'">'. __('Create clone', 'updraftplus') . '</button>';
				$content .= '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span><br>';
				$content .= '<div id="ud_downloadstatus3"></div>';
				$content .= '</div>';
			}
			$content .= '</div>'; // end .updraftclone-main-row

			$content .= isset($response['clone_list']) ? '<div class="clone-list"><h3>'.__('Current clones', 'updraftplus').' - <a target="_blank" href="https://teamupdraft.com/my-account/clones/">'.__('manage', 'updraftplus').'</a></h3>'.$response['clone_list'].'</div>' : '';

			$response['html'] = $content;
		}

		return $response;
	}

	/**
	 * This function sends the request to create the clone
	 *
	 * @param array $params - The submitted data
	 * @return string - the result of the call
	 */
	public function process_updraftplus_clone_create($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$response = $updraftplus->get_updraftplus_clone()->ajax_process_clone($params);
		
		if (!isset($response['status']) && 'success' != $response['status']) return $response;

		$content = '';
		$url = '';
		
		if (isset($response['data'])) {
			$tokens = isset($response['data']['tokens']) ? $response['data']['tokens'] : 0;
			$url = isset($response['data']['url']) ? $response['data']['url'] : '';
			
			if (isset($response['data']['secret_token'])) {
				$response['secret_token'] = $response['data']['secret_token'];
				unset($response['data']['secret_token']);
			}

			$content .= '<div class="updraftclone-main-row">';

			$content .= '<div class="updraftclone-tokens">';
			$content .= '<p>' . __("Available temporary clone tokens:", "updraftplus") . ' <span class="tokens-number">' . esc_html($tokens) . '</span></p>';
			$content .= '</div>';

			$content .= '<div class="updraftclone_action_box">';
			
			$content .= $updraftplus_admin->updraftplus_clone_info($url);

			$content .= '</div>';

			$content .= '</div>'; // end .updraftclone-main-row
		}
		if (isset($params['form_data']['install_info']['wp_only'])) {
			$content .= '<p id="updraft_clone_progress">' . __('No backup will be started.', 'updraftplus').' '.__('The creation of your clone should now begin, and your WordPress username and password will be displayed below when ready.', 'updraftplus') . ' ' . __('N.B. You will be charged one token once the clone is ready.', 'updraftplus').' '.__('If the clone fails to boot, then the token will be released within an hour.', 'updraftplus') . '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span></p>';
		} else {
			$content .= '<p id="updraft_clone_progress">' . __('The creation of your data for creating the clone should now begin.', 'updraftplus') . ' ' . __('N.B. You will be charged one token once the clone is ready.', 'updraftplus').' '.__('If the clone fails to boot, then the token will be released within an hour.', 'updraftplus') . '<span class="updraftplus_spinner spinner">' . __('Processing', 'updraftplus') . '...</span></p>';
			$content .= '<div id="updraft_clone_activejobsrow" style="display:none;"></div>';
		}

		$response['html'] = $content;
		$response['url'] = $url;
		$response['key'] = '';

		return $response;
	}

	/**
	 * This function will get the clone network and credential info
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array|WP_Error - the response array that includes the credential info or a WP_Error
	 */
	public function process_updraftplus_clone_poll($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$response = $updraftplus->get_updraftplus_clone()->clone_info_poll($params);

		return $response;
	}

	/**
	 * This function will get the clone network info HTML for the passed in clone URL
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array        - the response array that includes the network HTML
	 */
	public function get_clone_network_info($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');
		
		$url = empty($params['clone_url']) ? '' : $params['clone_url'];

		$response = array();

		$response['html'] = $updraftplus_admin->updraftplus_clone_info($url);

		return $response;
	}

	/**
	 * This function will get the restore resume notice
	 *
	 * @param array $params - the parameters for the call
	 *
	 * @return array|WP_Error - the response array that includes the restore resume notice
	 */
	public function get_restore_resume_notice($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused variable is for future use.
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$job_id = empty($params['job_id']) ? '' : $params['job_id'];

		$response = array(
			'status' => 'success',
		);

		if (empty($job_id)) return new WP_Error('missing_parameter', 'Missing parameters.');

		$html = $updraftplus_admin->get_restore_resume_notice($job_id);

		if (is_wp_error($html)) return $html;

		$response['html'] = $html;

		return $response;
	}

	/**
	 * This function will add updraft_dismiss_admin_warning_litespeed option to hide litespeed admin warning after dismissed
	 *
	 * @return array - an empty array
	 */
	public function dismiss_admin_warning_litespeed() {
		UpdraftPlus_Options::update_updraft_option('updraft_dismiss_admin_warning_litespeed', true);
		return array();
	}
	
	/**
	 * This function will add updraft_dismiss_admin_warning_pclzip option to hide pclzip admin warning after dismissed
	 *
	 * @return array - an empty array
	 */
	public function dismiss_admin_warning_pclzip() {
		UpdraftPlus_Options::update_updraft_option('updraft_dismiss_admin_warning_pclzip', true);
		return array();
	}

	/**
	 * This function is for importing settings via RPC
	 *
	 * @param  Array $settings - The settings data to be imported
	 * @return Array An Array response to be sent back
	 */
	public function import_settings($settings) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (empty($settings) || !is_array($settings)) {
			return new WP_Error('invalid_settings', 'Invalid settings data provided');
		}

		try {
			$result = $updraftplus_admin->import_settings($settings, true);

			if (is_array($result)) {
				if (isset($result['saved']) && !$result['saved'] && !empty($result['error_message'])) {
					return new WP_Error('import_failed', $result['error_message']);
				}
				
				return $result;
			} else {
				return new WP_Error('unexpected_response', 'Unexpected response format from import_settings');
			}
		} catch (Exception $e) {
			return new WP_Error('import_failed', $e->getMessage());
		}
	}

	/**
	 * This function is for updating site information
	 *
	 * @param array $params Parameters containing site info to update
	 * @return array|WP_Error Response or error
	 */
	public function update_site_info($params) {
		if (false === $this->_load_ud_admin()) return new WP_Error('no_updraftplus');

		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		// Update site title
		if (isset($params['site_title'])) {
			$site_title = sanitize_text_field($params['site_title']);
			update_option('blogname', $site_title);
		}

		// Update tagline
		if (isset($params['tagline'])) {
			$tagline = sanitize_text_field($params['tagline']);
			update_option('blogdescription', $tagline);
		}

		// Update admin email
		if (isset($params['admin_email'])) {
			$admin_email = sanitize_email($params['admin_email']);
			
			// Validate email format
			if (!is_email($admin_email)) {
				return new WP_Error('invalid_email', __('Invalid email address format', 'updraftplus'));
			}
			
			update_option('admin_email', $admin_email);
		}

		return array(
			'message' => __('Site information updated successfully', 'updraftplus')
		);
	}

	/**
	 * Get advanced tools data in a structured format
	 *
	 * @param array $param The form data.
	 *
	 * @return array|WP_Error Structured data or error
	 */
	public function get_structured_data($param) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin())) return new WP_Error('no_updraftplus');

		$tool_type = $param['tool_type'];

		// Site Info
		if ('site-information' === $tool_type) {
			$site_info = $updraftplus_admin->get_site_info_data();
			$site_info['site_title'] = get_bloginfo('name');
			$site_info['tagline'] = get_bloginfo('description');
			$site_info['admin_email'] = get_bloginfo('admin_email');

			return array('site_info' => $site_info);
		}

		// Lock Settings
		if ('lock-settings' === $tool_type) {
			$lock_settings = $this->get_lock_settings_data();
			if (is_wp_error($lock_settings)) {
				$lock_settings = array('has_premium' => false);
			}

			return array('lock_settings' => $lock_settings);
		}

		// Directory Sizes
		if ('site-size' === $tool_type) {
			$site_size = array();
			if (false !== ($updraftplus = $this->_load_ud())) {
				$backupable_entities = $updraftplus->get_backupable_file_entities(true, true);
				foreach ($backupable_entities as $entity => $info) {
					$size = UpdraftPlus_Filesystem_Functions::get_disk_space_used($entity, 'numeric');

					$size                 = is_numeric($size) ? $size : 0;
					$site_size[$entity] = array(
						'size'           => $size,
						'size_formatted' => size_format($size)
					);
				}
			}

			return array('site_size' => $site_size);
		}

		// Connection Keys
		if ('updraftcentral' === $tool_type) {
			updraft_try_include_file('central/bootstrap.php', 'include_once');
			$keys = array();
			global $updraftcentral_main;
			if (is_a($updraftcentral_main, 'UpdraftCentral_Main') && method_exists($updraftcentral_main, 'get_connection_keys_data')) {
				$keys = $updraftcentral_main->get_connection_keys_data();
			}

			return array('keys' => $keys);
		}

		// Database Size Information
		if ('db-size' === $tool_type) {
			updraft_try_include_file('includes/class-wpadmin-commands.php', 'include_once');

			$db_size = array('size' => '0 B');
			if (class_exists('UpdraftPlus_WPAdmin_Commands')) {
				$wpadmin_commands = new UpdraftPlus_WPAdmin_Commands($this->_uc_helper);
				$db_size_result   = $wpadmin_commands->db_size(true);
				if (!is_wp_error($db_size_result)) {
					$db_size = $db_size_result;
				}
			}

			return array('db_size' => $db_size);
		}

		return new WP_Error('invalid_tool_type', __('Invalid tool type', 'updraftplus'));
	}

	/**
	 * Pre-check before sending request and delegates login request to the appropriate service
	 *
	 * @param array $params - The submitted form data
	 * @return array - the result of the call
	 */
	public function process_updraftplus_migration_login($params) {
		if (false === ($updraftplus_admin = $this->_load_ud_admin()) || false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (class_exists('WC_Geolocation')) {
			// Prefer WooCommerce's geolocation - no external ipinfo.io call needed
			$ip_response = WC_Geolocation::geolocate_ip();
			if (!empty($ip_response['country'])) {
				$params['form_data']['country_code'] = $ip_response['country'];
			}
		} elseif (!defined('UPDRAFTPLUS_DO_NOT_USE_IPINFO') || !UPDRAFTPLUS_DO_NOT_USE_IPINFO) {
			// Try to get this website's hosting region code (from the server's outbound IP via IPInfo), so we can find the closest clone region.
			$ip_response = wp_remote_get('https://ipinfo.io/json', array(
				'timeout' => 3,
				// The API always returns 429 rate limit unless this header is passed
				'headers' => array(
					'Referer' => network_site_url()
				)
			));

			if (200 === wp_remote_retrieve_response_code($ip_response)) {
				$body = wp_remote_retrieve_body($ip_response);
				$ip_data = json_decode($body, true);
				if (isset($ip_data['country'])) $params['form_data']['country_code'] = $ip_data['country'];
			}
		}

		$response = $updraftplus->get_updraftplus_migration()->ajax_process_login($params, false);

		// Only continue if authentication succeeded
		if (empty($response['status']) || 'authenticated' !== $response['status']) {
			// A '2FA_checked' response is not an error: the JS will ask for the TFA code and resubmit.
			if (empty($response['tfa_enabled'])) {
				$response['message'] = $this->get_migration_login_error_message(isset($response['code']) ? $response['code'] : '');
			}
			return $response;
		}

		// Validate token data
		if (empty($response['token_data']['migration_id']) || empty($response['token_data']['user_id']) || empty($response['token_data']['secret_token'])) {
			$response['message'] = wp_kses($response['message'], array(
				'strong' => array(),
				'a' => array('href' => array()),
			));
			return $response;
		}

		$content = '<div class="updraftclone-main-row">';
		$content .= '<div class="updraftclone_action_box">';
		$content .= $updraftplus_admin->updraftplus_simple_migration_fields_ui($response['token_data']);
		$content .= '<div class="updraftplus-migration-processing-section"></div>';
		$content .= '<div class="updraft_migration_progress_container"></div>';
		$content .= '<p class="updraftplus-migration-process-notice">' . esc_html__('Important: Migration time depends on the size of the site.', 'updraftplus') . ' ' . esc_html__('Larger sites may take longer to complete.', 'updraftplus') . '</p>';
		$content .= '</div>';
		$content .= '</div>';

		$response['html'] = $content;

		return $response;
	}

	/**
	 * Starts the migration process by preparing request parameters and
	 * passing them to the migration API (TeamUpdraft migration service).
	 *
	 * Handles both:
	 * - Import (remote > this site)
	 * - Export (this site > remote)
	 *
	 * @param array $params Data submitted from the JS migration form.
	 * @return array|WP_Error API response or WP_Error on failure.
	 */
	public function process_updraftplus_migration_start($params) {
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		if (empty($params['form_data']) || !is_array($params['form_data'])) {
			return new WP_Error('invalid_params', __('Invalid or missing migration request data.', 'updraftplus'));
		}

		$migrate_type = isset($params['form_data']['migrate_type']) ? sanitize_text_field($params['form_data']['migrate_type']) : '';
		$remote_site_url = !empty($params['form_data']['site_url']) ? trailingslashit(esc_url_raw($params['form_data']['site_url'])) : '';
		$remote_email = isset($params['form_data']['admin_email']) ? sanitize_text_field($params['form_data']['admin_email']) : '';
		$remote_pass = isset($params['form_data']['admin_password']) ? $params['form_data']['admin_password'] : '';
		$migration_id = isset($params['form_data']['migration_id']) ? sanitize_text_field($params['form_data']['migration_id']) : '';
		$user_id = isset($params['form_data']['user_id']) ? sanitize_text_field($params['form_data']['user_id']) : '';
		$secret_token = isset($params['form_data']['secret_token']) ? sanitize_text_field($params['form_data']['secret_token']) : '';
		$current_site_login_url = !empty($params['form_data']['current_site_login_url']) ? trailingslashit(esc_url_raw($params['form_data']['current_site_login_url'])) : '';
		$remote_site_login_url = !empty($params['form_data']['remote_site_login_url']) ? trailingslashit(esc_url_raw($params['form_data']['remote_site_login_url'])) : '';

		$current_user = wp_get_current_user();
		$current_url = network_site_url();

		if (UpdraftPlus_Manipulation_Functions::normalise_url($current_url) == UpdraftPlus_Manipulation_Functions::normalise_url($remote_site_url)) {
			return array(
				'message' => __('The remote site cannot be the same as the current site.', 'updraftplus') . ' ' . __('Please check the site URL and try again.', 'updraftplus'),
			);
		}

		$temp_password = $this->generate_migration_temp_password();

		$current_site_url_validation = $current_url;
		$remote_site_url_validation = $remote_site_url;

		$current_site_has_custom_login_url = 'no';
		$remote_site_has_custom_login_url = 'no';

		$param_data = array(
			'migration_id' => $migration_id,
			'user_id' => $user_id,
			'secret_token' => $secret_token,
			'strategy' => 'p2p', // Possible value: p2p (Peer to Peer (via migrator addon)) or s3 (S3(via AWS S3 bucket))
		);

		if ('import' === $migrate_type) {
			// Remote > Local migration.
			$param_data['source_site_url'] = $remote_site_url;
			$param_data['source_site_username'] = $remote_email;
			$param_data['source_site_password'] = $remote_pass;

			$param_data['destination_site_url'] = $current_url;
			$param_data['destination_site_username'] = $current_user->user_login;
			$param_data['destination_site_password'] = $temp_password;

			if (!empty($current_site_login_url)) {
				$param_data['destination_site_custom_login_url'] = $current_site_login_url;
				$current_site_url_validation = $current_site_login_url;
				$current_site_has_custom_login_url = 'yes';
			}

			if (!empty($remote_site_login_url)) {
				$param_data['source_site_custom_login_url'] = $remote_site_login_url;
				$remote_site_url_validation = $remote_site_login_url;
				$remote_site_has_custom_login_url = 'yes';
			}
		} else {
			// Local > Remote migration.
			$param_data['destination_site_url'] = $remote_site_url;
			$param_data['destination_site_username'] = $remote_email;
			$param_data['destination_site_password'] = $remote_pass;

			$param_data['source_site_url'] = $current_url;
			$param_data['source_site_username'] = $current_user->user_login;
			$param_data['source_site_password'] = $temp_password;

			if (!empty($current_site_login_url)) {
				$param_data['source_site_custom_login_url'] = $current_site_login_url;
				$current_site_url_validation = $current_site_login_url;
				$current_site_has_custom_login_url = 'yes';
			}

			if (!empty($remote_site_login_url)) {
				$param_data['destination_site_custom_login_url'] = $remote_site_login_url;
				$remote_site_url_validation = $remote_site_login_url;
				$remote_site_has_custom_login_url = 'yes';
			}
		}

		// Extendify migration flow
		if (!empty($params['form_data']['is_extendify_migration_active'])) {
			// These params are part of the shared migration payload. They are ignored for Extendify-based migrations.
			unset($_POST['migration_id'], $_POST['user_id'], $_POST['secret_token']);

			$param_data['extendify_access_token'] = apply_filters('updraftplus_get_migration_access_token', null);

			// Validate current before starting migration against 2FA/ Captcha issue for login.
			$current_site_validation_response = $updraftplus->get_updraftplus_migration()->process_migration_extendify_site_validation_url($current_site_url_validation, $param_data['extendify_access_token'], $current_site_has_custom_login_url);
			if ('success' !== $current_site_validation_response['status']) {
				$validation_error_message = $this->get_site_validation_error_message($current_site_validation_response['response'], 'current site');
				$current_site_validation_response['message'] = ('' !== $validation_error_message) ? $validation_error_message : $current_site_validation_response['message'];
				return $current_site_validation_response;
			}

			// Validate remote site before starting migration against 2FA/ Captcha issue for login.
			$remote_site_validation_response = $updraftplus->get_updraftplus_migration()->process_migration_extendify_site_validation_url($remote_site_url_validation, $param_data['extendify_access_token'], $remote_site_has_custom_login_url);
			if ('success' !== $remote_site_validation_response['status']) {
				$validation_error_message = $this->get_site_validation_error_message($remote_site_validation_response['response'], 'remote site');
				$remote_site_validation_response['message'] = ('' !== $validation_error_message) ? $validation_error_message : $remote_site_validation_response['message'];
				return $remote_site_validation_response;
			}

			return $updraftplus->get_updraftplus_migration()->process_extendify_migration($param_data);
		} else {
			// Validate current before starting migration against 2FA/ Captcha issue for login.
			$current_site_validation_response = $updraftplus->get_updraftplus_migration()->process_migration_site_validation_url($current_site_url_validation, $current_site_has_custom_login_url);
			if ('success' !== $current_site_validation_response['status']) {
				$validation_error_message = $this->get_site_validation_error_message($current_site_validation_response['response'], 'current site');
				$current_site_validation_response['message'] = ('' !== $validation_error_message) ? $validation_error_message : $current_site_validation_response['message'];
				return $current_site_validation_response;
			}

			// Validate remote site before starting migration against 2FA/ Captcha issue for login.
			$remote_site_validation_response = $updraftplus->get_updraftplus_migration()->process_migration_site_validation_url($remote_site_url_validation, $remote_site_has_custom_login_url);
			if ('success' !== $remote_site_validation_response['status']) {
				$validation_error_message = $this->get_site_validation_error_message($remote_site_validation_response['response'], 'remote site');
				$remote_site_validation_response['message'] = ('' !== $validation_error_message) ? $validation_error_message : $remote_site_validation_response['message'];
				return $remote_site_validation_response;
			}
		}

		$response = $updraftplus->get_updraftplus_migration()->process_migration($param_data, $migrate_type);

		return $response;
	}

	/**
	 * Retrieves the migration job status from the migration API.
	 *
	 * @param array $params Must include 'identifier' (migration job ID).
	 * @return array|WP_Error API response or WP_Error on failure.
	 */
	public function process_updraftplus_migration_status($params) {
		if (false === ($updraftplus = $this->_load_ud())) return new WP_Error('no_updraftplus');
		if (!UpdraftPlus_Options::user_can_manage()) return new WP_Error('updraftplus_permission_denied');

		$response = $updraftplus->get_updraftplus_migration()->process_migration_status($params);

		return $response;
	}

	/**
	 * Generates and stores a temporary password used exclusively for migration authentication.
	 *
	 * WordPress does NOT allow us to retrieve the real password of the current logged-in user.
	 * But the migration service requires valid credentials for the "source site" in order to
	 * log in and perform a migration. To solve this, we create a temporary password with a
	 * known prefix, valid for 5 minutes.
	 *
	 * During migration, the external migration service uses this temporary password to log in.
	 * The login attempt is validated inside the 'authenticate' filter, which checks:
	 * - the temporary password matches the stored one
	 * - it has the correct prefix `updraftplustemppwd-`
	 * - it has not expired (older than 5 minutes)
	 *
	 * @return string The generated temporary migration password.
	 */
	private function generate_migration_temp_password() {
		$temp_password = 'updraftplustemppwd-' . wp_generate_password();

		$temp_password_data = array(
			'password_hash' => wp_hash_password($temp_password),
			'created' => time(),
			'user_id' => get_current_user_id(),
		);

		update_site_option('updraftplus_migration_temp_password', $temp_password_data, false);

		return $temp_password;
	}

	/**
	 * Convert migration site validation API errors into user-friendly messages.
	 *
	 * @param array  $error_response Parsed API error response.
	 * @param string $site_label     Site context: 'current site' or 'remote site'.
	 * @return string Human-readable error message.
	 */
	private function get_site_validation_error_message($error_response, $site_label = 'current site') {

		if (empty($error_response['data']['type'])) {
			return '';
		}

		switch ($error_response['data']['type']) {

			case 'https://errors.updraftplus.com/api/site-validations/login-page-not-found':
				return sprintf(
					/* translators: %s: site label */
					__('The %s login URL is invalid.', 'updraftplus') . ' ' .
					__('If the site uses a custom login URL, please enter the correct one in the Advanced options.', 'updraftplus'),
					$site_label
				);

			case 'https://errors.updraftplus.com/api/site-validations/two-factor-authentication-detected':
				return sprintf(
					/* translators: %s: site label */
					__('Two-factor authentication was detected on the login page of the %s.', 'updraftplus') .' ' .
					__('Please temporarily disable it, complete the migration, and then re-enable it.', 'updraftplus'),
					$site_label
				);

			case 'https://errors.updraftplus.com/api/site-validations/captcha-detected':
				return sprintf(
					/* translators: %s: site label */
					__('A CAPTCHA or security challenge was detected on the login page of the %s.', 'updraftplus') . ' ' .
					__('Please temporarily disable it so the migration can proceed.', 'updraftplus'),
					$site_label
				);

			default:
				return '';
		}
	}

	/**
	 * Map a login error code from the migration API to our own plain-text message.
	 *
	 * The API relays WordPress core's WP_Error message, which contains HTML and the
	 * submitted email. We return our own text instead so nothing untrusted reaches
	 * the frontend, which renders this with .text().
	 *
	 * @param string $code - The error code returned by the login API.
	 * @return string - A translated, plain-text message.
	 */
	private function get_migration_login_error_message($code) {
		$messages = array(
			'empty_username' => __('Please enter your username.', 'updraftplus'),
			'empty_email' => __('Please enter your email address.', 'updraftplus'),
			'empty_password' => __('Please enter your password.', 'updraftplus'),
			'invalid_email' => __('No account was found for that email address.', 'updraftplus'),
			'incorrect_password' => __('The password you entered is incorrect.', 'updraftplus'),
			'no_an_email_address' => __('Please enter an email address.', 'updraftplus'),
			'email_not_registered' => __('There is no account with this email address at teamupdraft.com.', 'updraftplus') . ' ' . __('Please check your email address and try again.', 'updraftplus'),
			'invalidcombo' => __('There is no account with that username or email address.', 'updraftplus')
		);

		return isset($messages[$code]) ? $messages[$code] : __('Login failed.', 'updraftplus') . ' ' . __('Please try again.', 'updraftplus');
	}
}
