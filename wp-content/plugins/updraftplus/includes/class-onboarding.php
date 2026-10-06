<?php
// phpcs:disable PHPCompatibility.Keywords.NewKeywords.t_useFound, PHPCompatibility.LanguageConstructs.NewLanguageConstructs.t_ns_separatorFound, PHPCompatibility.InitialValue.NewConstantScalarExpressions.constFound -- Namespaces and use statements are intentionally used; code is guarded to run only on supported PHP versions.

if (!defined('ABSPATH')) die('No direct access.');

updraft_try_include_file('vendor/team-updraft/lib-onboarding-wizard/autoload.php');

use Updraftplus\Updraftplus\Wizard\Onboarding\Onboarding;

class UpdraftPlus_Onboarding {

	/**
	 * Plugin display name.
	 *
	 * @var string
	 */
	const PLUGIN_NAME = 'UpdraftPlus';

	/**
	 * Plugin slug and identifier.
	 *
	 * @var string
	 */
	const PLUGIN_SLUG = 'updraftplus';

	/**
	 * Mailing list ID for free users.
	 *
	 * Used to identify subscribers who are using the free version of the product.
	 */
	const MAILING_LIST_FREE_ID = 128;

	/**
	 * Mailing list ID for premium users.
	 *
	 * Used to identify subscribers who have an active premium license.
	 */
	const MAILING_LIST_PREMIUM_ID = 129;

	/**
	 * URL to the free plugin support forum.
	 *
	 * @var string
	 */
	const FREE_SUPPORT_URL = 'https://wordpress.org/support/plugin/updraftplus/';

	/**
	 * Path to the onboarding logo image.
	 *
	 * @var string
	 */
	const LOGO_PATH = UPDRAFTPLUS_URL.'/images/ud-logo.png';

	/**
	 * Path to the plugin languages directory.
	 *
	 * @var string
	 */
	const LANGUAGES_DIR = UPDRAFTPLUS_DIR.'languages';

	/**
	 * Flag to determine whether the current version is premium.
	 *
	 * @var bool
	 */
	private $is_premium;

	/**
	 * Onboarding object
	 *
	 * @var object
	 */
	private static $onboarding;

	/**
	 * Remote storages with OAuth
	 *
	 * @var array
	 */
	private $oauth_remote_storage_methods = array('dropbox', 'googlecloud', 'googledrive', 'onedrive', 'pcloud');

	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action('init', array($this, 'init'), 12);
		if (Updraftplus\Updraftplus\Wizard\Onboarding\Onboarding::is_onboarding_active(self::PLUGIN_SLUG, self::PLUGIN_SLUG)) add_action('updraftplus_remote_storage_connection_status_changed', array($this, 'connection_status_changed'), 10, 2);
	}

	/**
	 * Initializes the onboarding functionality and configures onboarding settings.
	 *
	 * @return void
	 */
	public function init() {
		global $updraftplus, $pagenow;

		if (!$this->is_onboarding_rest_ajax() && !$this->is_onboarding_rest_request() && !Updraftplus\Updraftplus\Wizard\Onboarding\Onboarding::is_onboarding_active(self::PLUGIN_SLUG, self::PLUGIN_SLUG)) return;

		if (!$this->is_onboarding_rest_ajax() && !$this->is_onboarding_rest_request() && (UpdraftPlus_Options::admin_page() != $pagenow || empty($_REQUEST['page']) || 'updraftplus' != $_REQUEST['page'])) return;

		if (!UpdraftPlus_Options::user_can_manage()) return;

		self::$onboarding = new Updraftplus\Updraftplus\Wizard\Onboarding\Onboarding();

		$this->is_premium = (int) $updraftplus->have_addons > 20 ? true : false;

		add_filter('updraftplus_onboarding_steps', array($this, 'get_onboarding_steps'));
		add_action('updraftplus_admin_enqueue_scripts', array($this, 'onboarding_script'));
		add_action('updraftplus_onboarding_update_options', array($this, 'update_step_settings'));
		add_action('updraftplus_skipped_onboarding', array($this, 'remove_unused_remote_storage_options'));
		add_action('updraftplus_completed_onboarding', array($this, 'remove_unused_remote_storage_options'));
		add_action('update_site_option_updraftplus_onboarding_position', array($this, 'should_save_remote_storage_settings_during_onboarding'), 10, 2);

		$position = get_site_option('updraftplus_onboarding_position');
		if (!$this->is_onboarding_rest_ajax() && !$this->is_onboarding_rest_request() && is_array($position) && isset($position['step_id']) && 'remote_storage_setup' == $position['step_id']) {
			UpdraftPlus_Options::delete_updraft_option('updraftplus_save_remote_storage_settings_onboarding');
		}

		self::$onboarding->is_pro = $this->is_premium;
		self::$onboarding->logo_path = self::LOGO_PATH;
		self::$onboarding->plugin_name = self::PLUGIN_NAME;
		self::$onboarding->prefix = self::PLUGIN_SLUG;
		self::$onboarding->mailing_list = self::PLUGIN_SLUG;
		self::$onboarding->mailing_list  = array($this->is_premium ? self::MAILING_LIST_PREMIUM_ID : self::MAILING_LIST_FREE_ID);
		self::$onboarding->mailing_list_endpoint = $updraftplus->get_url('newsletter');
		self::$onboarding->privacy_statement_url = $updraftplus->get_url('privacy');
		self::$onboarding->caller_slug = self::PLUGIN_SLUG;
		self::$onboarding->udmupdater_slug = self::PLUGIN_SLUG;
		self::$onboarding->permission_callback = array('UpdraftPlus_Options', 'user_can_manage');
		self::$onboarding->support_url = $this->is_premium ? $updraftplus->get_url('premium_support') : self::FREE_SUPPORT_URL;
		self::$onboarding->documentation_url = $updraftplus->get_url('documentation');
		self::$onboarding->upgrade_url = $updraftplus->get_url('premium');
		self::$onboarding->page_prefix = self::PLUGIN_SLUG;
		self::$onboarding->version = $updraftplus->version;
		self::$onboarding->languages_dir = self::LANGUAGES_DIR;
		self::$onboarding->text_domain = self::PLUGIN_SLUG;
		self::$onboarding->reload_settings_page_on_finish = true;

		if ($this->is_premium) self::$onboarding->plugin_name .= ' '.__('Premium', 'updraftplus');

		self::$onboarding->init();
	}

	/**
	 * Apply onboarding inputs to backup and remote storage settings.
	 *
	 * @param array $settings Settings data
	 *
	 * @return void
	 */
	public function update_step_settings($settings) {
		global $updraftplus;

		if (!UpdraftPlus_Options::user_can_manage()) return;
		$settings = array_column($settings, 'value', 'id');// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.array_columnFound -- array_column is intentionally used and code is executed only on supported PHP versions.

		// Save backup settings
		UpdraftPlus_Options::update_updraft_option('updraft_interval_database', $settings['backup_frequency']);
		// Every hour is not available for file
		$backup_frequency = 'everyhour' == $settings['backup_frequency'] ? 'every2hours' : $settings['backup_frequency'];
		UpdraftPlus_Options::update_updraft_option('updraft_interval', $backup_frequency);

		UpdraftPlus_Options::update_updraft_option('updraft_retain_db', $settings['keep_last_backups']);
		UpdraftPlus_Options::update_updraft_option('updraft_retain', $settings['keep_last_backups']);

		// Save remote storage data
		if (!empty($settings['selected_destinations'])) {
			add_filter('updraftplus_update_option', array($updraftplus, 'storage_options_filter'), 10, 2);
			if (!is_array($settings['selected_destinations'])) $settings['selected_destinations'] = array($settings['selected_destinations']);
			
			foreach ($settings['selected_destinations'] as $method) {
				if (!array_key_exists($method, $updraftplus->backup_methods)) continue;
				$prefix = $method.'_';
				if ('email' == $method) {
					$option = isset($settings["{$prefix}email_address"]) ? array($settings["{$prefix}email_address"]) : array();
				} else {
					$storage = UpdraftPlus_Storage_Methods_Interface::get_storage_objects_and_ids(array($method));
					$storage_object = reset($storage);
					$instance_id = key($storage_object['instance_settings']);

					$allowed_keys = array_keys($storage_object['object']->get_input_option_mappings());
					$posted_option = array();
					foreach ($settings as $key => $value) {
						if (0 === strpos($key, $prefix)) $posted_option[str_replace($prefix, '', $key)] = $value;
					}
					$posted_option = array_intersect_key($posted_option, array_flip($allowed_keys));

					$instance_settings = array_merge(reset($storage_object['instance_settings']), $posted_option);
					$option = UpdraftPlus_Options::get_updraft_option('updraft_'.$method);
					$option['settings'][$instance_id] = $instance_settings;
				}

				UpdraftPlus_Options::update_updraft_option('updraft_'.$method, $option);
			}
			remove_filter('updraftplus_update_option', array($updraftplus, 'storage_options_filter'), 10, 2);
			UpdraftPlus_Options::update_updraft_option('updraftplus_save_remote_storage_settings_onboarding', true);
			UpdraftPlus_Options::update_updraft_option('updraft_service', $updraftplus->get_canonical_service_list($settings['selected_destinations']));
		}
	}

	/**
	 * Updates the stored connection status for a remote storage provider.
	 *
	 * The connection status is stored in the
	 * `updraftplus_remote_storage_connection_status` option, keyed by the
	 * remote storage object ID.
	 *
	 * @param bool   $status         Whether the remote storage is connected.
	 *                               True indicates connected, false indicates not connected.
	 * @param object $storage_object Remote storage object instance. Must implement
	 *                               the get_id() method.
	 *
	 * @return void
	 */
	public function connection_status_changed($status, $storage_object) {
		$storage_object_id = $storage_object->get_id();
		$option = UpdraftPlus_Options::get_updraft_option('updraftplus_onboarding_remote_storage_status', array());

		$option[$storage_object_id] = $status ? 1 : 0; // 1 for connected, 0 for not connected
		UpdraftPlus_Options::update_updraft_option('updraftplus_onboarding_remote_storage_status', $option);
	}

	/**
	 * Deletes the temporary onboarding flag once the remote storage setup step has been completed.
	 *
	 * This callback is triggered during the onboarding flow and removes the
	 * `updraftplus_save_remote_storage_settings_onboarding` option when the
	 * current step is `remote_storage_setup`.
	 *
	 * @param mixed $option Unused. Included for compatibility with the calling hook.
	 * @param array $value  Data associated with the current onboarding step.
	 *
	 * @return void
	 */
	public function should_save_remote_storage_settings_during_onboarding($option, $value) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused parameter is present because the method is called from action.
		if (isset($_REQUEST['action']) && 'updraftplus_onboarding_rest_api_fallback' === $_REQUEST['action']) {
			// AJAX request
			$data = json_decode(file_get_contents('php://input'), true);
			$target = isset($data['path']) ? $data['path'] : '';
		} else {
			// REST request
			$target = UpdraftPlus_Manipulation_Functions::fetch_superglobal('server', 'REQUEST_URI', '', true);
		}

		if (false === strpos($target, 'update_position') || 'remote_storage_setup' !== $value['step_id']) return;
		UpdraftPlus_Options::delete_updraft_option('updraftplus_save_remote_storage_settings_onboarding');
	}

	/**
	 * Remove settings for remote storage providers that are no longer enabled.
	 *
	 * Compares the list of available remote storage methods against the
	 * currently configured services and deletes any stored options belonging
	 * to providers that are not in use.
	 *
	 * @return void
	 */
	public function remove_unused_remote_storage_options() {
		if (!UpdraftPlus_Options::user_can_manage()) return;

		global $updraftplus;

		if (!UpdraftPlus_Options::get_updraft_option('updraftplus_save_remote_storage_settings_onboarding', false)) UpdraftPlus_Options::delete_updraft_option('updraft_service');

		$diff = array_diff(array_keys($updraftplus->backup_methods), $updraftplus->get_canonical_service_list());
		if ($diff) {
			foreach ($diff as $backup_method) {
				UpdraftPlus_Options::delete_updraft_option('updraft_'.$backup_method);
			}
		}

		UpdraftPlus_Options::delete_updraft_option('updraftplus_onboarding_remote_storage_status');
		UpdraftPlus_Options::delete_updraft_option('updraftplus_save_remote_storage_settings_onboarding');
	}

	/**
	 * Enqueues the UpdraftPlus onboarding JavaScript file.
	 *
	 * @return void
	 */
	public function onboarding_script() {
		global $updraftplus;
		$enqueue_version = $updraftplus->use_unminified_scripts() ? $updraftplus->version.'.'.time() : $updraftplus->version;
		$min_or_not = $updraftplus->get_updraftplus_file_version();

		wp_register_script('updraftplus-onboarding', esc_url(UPDRAFTPLUS_URL.'/includes/updraft-onboarding'.$min_or_not.'.js'), array(), $enqueue_version, array('in_footer' => true));
		wp_localize_script('updraftplus-onboarding', 'updraftplus_onboarding', array(
			'email_cannot_empty' => esc_html__('Email field cannot be empty.', 'updraftplus'),
			'password_cannot_empty' => esc_html__('Password field cannot be empty.', 'updraftplus'),
			'email_not_valid' => esc_html__('Please enter a valid email address.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'testing_remote_storage' => esc_html__('Testing %s connection...', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'oauth_pre_connection' => esc_html__('Initiating %s connection, please follow the prompts.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'connected' => esc_html__('%s connection established successfully.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'not_connected' => esc_html__('Could not connect to %s, please check your details and try again.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'connecting' => esc_html__('Connecting to %s', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'refreshing' => esc_html__('Refreshing %s quota', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'cannot_refresh' => esc_html__('Could not refresh %s quota, please try again.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'connection_error' => esc_html__('Error connecting to %s.', 'updraftplus'),
			/* translators: %s: Remote storage name */
			'refresh_error' => esc_html__('Error refreshing %s quota.', 'updraftplus'),
			'remote_storages' => $updraftplus->backup_methods
		));
		wp_enqueue_script('updraftplus-onboarding');
	}

	/**
	 * Builds and returns the full list of remote storage configuration fields used during onboarding.
	 *
	 * This method prepares destination options, handles premium-only storage providers,
	 * retrieves provider-specific field groups, and appends connection or OAuth buttons
	 * depending on the provider type. Returned fields are formatted for use in the
	 * onboarding UI.
	 *
	 * @global object $updraftplus UpdraftPlus main plugin instance containing backup methods.
	 *
	 * @return array List of formatted remote storage configuration fields.
	 */
	private function get_remote_storage_fields() {
		global $updraftplus;
		
		// Keep UpdraftVault at the top of the list. Since $updraftplus->backup_methods is not ordered alphabetically, temporarily remove the UpdraftVault entry, sort the remaining methods, then prepend it back afterwards.
		$backup_methods = $updraftplus->backup_methods;
		unset($backup_methods['updraftvault']);
		asort($backup_methods, SORT_STRING | SORT_FLAG_CASE);// phpcs:ignore PHPCompatibility.Constants.NewConstants.sort_flag_caseFound -- SORT_FLAG_CASE is intentionally used and code is executed only on supported PHP versions.
		$backup_methods = array_merge(array('updraftvault' => $updraftplus->backup_methods['updraftvault']), $backup_methods);

		$options = $premium_options = array();
		foreach ($backup_methods as $method => $method_friendly_name) {
			$option = array(
				'value' => $method,
				'icon' => UPDRAFTPLUS_URL.'/images/onboarding/'.$method.'.png',
				'label' => $method_friendly_name,
				'is_group' => true,
			);

			if (!class_exists('UpdraftPlus_BackupModule_'.$method) || is_subclass_of('UpdraftPlus_BackupModule_'.$method, 'UpdraftPlus_BackupModule_AddonNotYetPresent')) {
				$option['is_premium'] = true;
				$premium_options[] = $option;
				continue;
			}

			$options[] = $option;
		}

		if (!empty($premium_options)) $options = array_merge($options, $premium_options);

		$service = $updraftplus->get_canonical_service_list();

		$select_destination_field = array(
			'id' => 'selected_destinations',
			'group_id' => 'backup_destinations',
			'type' => class_exists('UpdraftPlus_Addon_MoreStorage') ? 'multi_select' : 'dropdown',
			'label' => __('Select destinations', 'updraftplus'),
			'placeholder' => __('Add backup destinations...', 'updraftplus'),
			'options' => $options,
			'tooltip' => array(
				'text' => class_exists('UpdraftPlus_Addon_MoreStorage') ? __('Choose one or more cloud storage providers for your backups.', 'updraftplus') : __('Choose one cloud storage provider for your backups.', 'updraftplus'),
				'icon' => 'info',
			),
		);

		if (!empty($service)) $select_destination_field['value'] = !class_exists('UpdraftPlus_Addon_MoreStorage') && is_array($service) ? $service[0] : $service;

		$remote_storage_fields = array($select_destination_field);

		// This line was added because the onboarding wizard library always requires a step to exist, even though a step is not necessarily needed when running in the REST or AJAX context.
		if ((defined('REST_REQUEST') && REST_REQUEST) || wp_doing_ajax()) return $remote_storage_fields;

		if (!is_array($service) && !empty($service)) $service = array($service);

		foreach ($updraftplus->backup_methods as $method => $method_friendly_name) {
			$storage = UpdraftPlus_Storage_Methods_Interface::get_storage_object($method);
			if (!$storage) continue;

			$fields_by_method = $storage->transform_template_properties_to_fields_structure();
			$storage_option = in_array($method, $service) ? $storage->get_options() : array();
			if (is_array($storage_option) && method_exists($storage, 'transform_options_for_template')) $storage_option = $storage->transform_options_for_template($storage_option);
			$connection_status = UpdraftPlus_Options::get_updraft_option('updraftplus_onboarding_remote_storage_status', array());

			$hidden_field = array(
				'id' => 'completed',
				'type' => 'hidden',
				'default' => false,
			);

			if (in_array($method, $service) && isset($connection_status[$method]) && 1 == $connection_status[$method]) {
				$hidden_field['default'] = $hidden_field['value'] = $hidden_field['edited'] = true;
			}

			$fields_by_method[] = $hidden_field;

			if (in_array($method, $this->oauth_remote_storage_methods)) {
				$fields_by_method[] = array(
					'id' => 'oauth_button',
					'type' => 'button',
					/* translators: %s: Remote storage name */
					'label' => sprintf(__('Sign in with %s', 'updraftplus'), $method_friendly_name),
					'externalAction' => 'oauth',
					'actionType' => 'connection_test',
				);
			} else {
				if ('updraftvault' === $method) {
					$action = 'connectUpdraftVault';
					$label = __('Connect to UpdraftVault', 'updraftplus');
					$extra = array(
						'visible_if' => array(
							'field'  => 'updraftvault_completed',
							'equals' => false,
						)
					);
				} else {
					$action = 'testConnection';
					$label = __('Test connection', 'updraftplus');
					$extra = array();
				}

				if ('email' !== $method) {
					$button = array(
						'id' => 'test_button',
						'type' => 'button',
						'actionType' => 'connection_test',
						'label' => $label,
						'externalAction' => $action,
					);

					$button = array_merge($button, $extra);

					if ('updraftvault' === $method) {
						array_splice($fields_by_method, 2, 0, array($button));
						$fields_by_method[] = array(
							'id' => 'email_display',
							'type' => 'hidden',
							'value' => isset($storage_option['email']) ? $storage_option['email'] : ''
						);
						$fields_by_method[] = array(
							'id' => 'quota_display',
							'type' => 'hidden',
							'value' => preg_replace('/ - .*$/', '', get_transient('updraftvault_quota_text'))
						);
					} else {
						$fields_by_method[] = $button;
					}
				}
			}

			foreach ($fields_by_method as $key => $field) {
				if (isset($storage_option[$field['id']])) $field['value'] = $storage_option[$field['id']];
				$field['id'] = $method.'_'.$field['id'];
				$field['group_id'] = $method;
				$field = apply_filters('updraftplus_remote_storage_input_field', $field, $method);
				$fields_by_method[$key] = $field;
			}

			$remote_storage_fields = array_merge($remote_storage_fields, $fields_by_method);
		}

		return $remote_storage_fields;
	}

	/**
	 * Generates and returns the list of remote storage groups for the onboarding interface.
	 *
	 * Each group represents a storage provider section used to configure backup destinations.
	 * The method includes a main "Backup destination(s)" group, followed by one group per
	 * supported remote storage method defined in UpdraftPlus.
	 *
	 * @global object $updraftplus UpdraftPlus main plugin instance containing backup methods.
	 *
	 * @return array List of remote storage groups with titles, IDs, and display settings.
	 */
	private function get_remote_storage_groups() {
		global $updraftplus;

		$groups = array();

		$groups[] = array(
			'title' => __('Backup destination(s)', 'updraftplus'),
			'id' => 'backup_destinations',
			'showConfirmButton' => false,
			'resetOnOpen' => false,
		);

		$service = $updraftplus->get_canonical_service_list();
		$connection_status = UpdraftPlus_Options::get_updraft_option('updraftplus_onboarding_remote_storage_status', array());

		foreach ($updraftplus->backup_methods as $method => $method_friendly_name) {
			$group = array(
				/* translators: %s: Remote storage name */
				'title' => sprintf(__('Connect to %s', 'updraftplus'), $method_friendly_name),
				'id' => $method,
				'hidden' => true,
				'showConfirmButton' => false,
				'controllerFieldId' => 'selected_destinations',
				'resetOnOpen' => false,
			);

			if ('updraftvault' !== $method && in_array($method, $service) && isset($connection_status[$method])) {
				if (1 == $connection_status[$method]) {
					$group['alert'] = array(
						'responseCode' => 'success',
						/* translators: %s: Remote storage name */
						'responseMessage' => sprintf(__('%s connection established successfully.', 'updraftplus'), $method_friendly_name)
					);
				} elseif (0 == $connection_status[$method]) {
					$group['alert'] = array(
						'responseCode' => 'danger',
						/* translators: %s: Remote storage name */
						'responseMessage' => sprintf(__('Could not connect to %s, please check your details and try again.', 'updraftplus'), $method_friendly_name),
					);
				}
			}

			$groups[] = $group;
		}

		return $groups;
	}

	/**
	 * Retrieves the list of backup frequency options available for scheduling backups.
	 *
	 * Includes a manual option followed by all cron-based schedules returned from
	 * UpdraftPlus' cron schedule list.
	 *
	 * @return array List of backup frequency options with values and labels.
	 */
	private function get_backup_schedule_options() {
		$cron_schedules = updraftplus_list_cron_schedules();
		$backup_frequency_options = array();

		$backup_frequency_options[] = array(
			'value' => 'manual',
			'label' => __('Manual', 'updraftplus')
		);

		foreach ($cron_schedules as $key => $value) {
			$backup_frequency_options[] = array(
				'value' => $key,
				'label' => $value['display']
			);
		}

		return $backup_frequency_options;
	}

	/**
	 * Get the introductory onboarding step configuration.
	 *
	 * @return array The configuration array used to render the intro step of the onboarding flow
	 */
	private function get_intro_step() {
		return array(
			'id' => 'intro',
			'type' => 'intro',
			'title' => __('Let\'s get started', 'updraftplus'),
			'subtitle' => __('In a few moments, we\'ll run you through everything you need to backup, restore, or migrate your site.', 'updraftplus'),
			'button' => array(
				'id' => 'start',
				'label' => __('Start', 'updraftplus'),
				'icon' => 'magic-wand'
			),
			'note' => __('Quick setup', 'updraftplus')."   \u{2022}   ".__('No tech skills needed', 'updraftplus'),// phpcs:ignore PHPCompatibility.TextStrings.NewUnicodeEscapeSequence.Found -- Unicode codepoint escape sequences is intentionally used and code is executed only on supported PHP versions.
		);
	}

	/**
	 * Get the license onboarding step configuration.
	 *
	 * @return array The configuration array used to render the license step of the onboarding flow
	 */
	private function get_license_step() {
		return array(
			'id' => 'license',
			'type' => 'license',
			'icon' => 'license',
			'title' => __('Connect and activate your license', 'updraftplus'),
			'title_conditional' => array(
				'licenseActivated' => __('Your license has been activated.', 'updraftplus'),
				'isUpdating' => __('Activating your premium license...', 'updraftplus'),
				'responseSuccess' => __('Your license has been activated.', 'updraftplus'),
				'responseFail' => __('License validation failed', 'updraftplus'),
			),
			'subtitle' => __('Please enter your TeamUpdraft credentials to start using premium features.', 'updraftplus'),
			'subtitle_conditional' => array(
				'licenseActivated' => '',
				'isUpdating' => '',
				'responseSuccess' => '',
				'responseFail' => __('Please check your details and try again.', 'updraftplus'),
			),
			'fields' => array(
				array(
					'id' => 'registration_email',
					'type' => 'email',
					'label' => __('Email', 'updraftplus'),
				),
				array(
					'id' => 'registration_password',
					'type' => 'password',
					'label' => __('Password', 'updraftplus'),
				),
			),
			'button' => array(
				'id' => 'activate',
				'label' => __('Confirm and activate', 'updraftplus'),
				'icon' => 'EastRoundedIcon',
			)
		);
	}

	/**
	 * Get the backup settings onboarding step configuration.
	 *
	 * @return array The configuration array used to render the backup settings of the onboarding flow
	 */
	private function get_backup_settings_step() {
		return array(
			'id' => 'backup_settings',
			'icon' => 'backup',
			'type' => 'settings',
			'title' => __('Automate your backups', 'updraftplus'),
			'subtitle' => __('Automatically backup your site\'s files and database on a regular schedule.', 'updraftplus').' '.__('You can change these anytime.', 'updraftplus'),
			'fields' => array(
				array(
					'id' => 'backup_frequency',
					'type' => 'dropdown',
					'label' => __('Backup schedule', 'updraftplus'),
					'options' => $this->get_backup_schedule_options(),
					'default' => 'manual',
					'tooltip' => array(
						'text' => __('Choose how often your website backups should be performed.', 'updraftplus'),
						'icon' => 'info',
					),
					'value' => UpdraftPlus_Options::get_updraft_option('updraft_interval_database', 'manual')
				),
				array(
					'id' => 'keep_last_backups',
					'type' => 'number',
					'label' => __('Number of backups to keep', 'updraftplus'),
					'default' => 2,
					'min' => 1,
					'tooltip' => array(
						'text' => __('How many most-recent backups to keep.', 'updraftplus').' '.__('When the limit is reached, the oldest backup is deleted automatically', 'updraftplus'),
						'icon' => 'info',
					),
					'value' => (int) UpdraftPlus_Options::get_updraft_option('updraft_retain_db', 2)
				),
			),
			'button' => array(
				'id' => 'save_backup_settings',
				'label' => __('Save and continue', 'updraftplus'),
				'icon' => 'EastRoundedIcon',
			),
		);
	}

	/**
	 * Get the remote storage onboarding step configuration.
	 *
	 * @return array The configuration array used to render the remote storage of the onboarding flow
	 */
	private function get_remote_storage_step() {
		$subtitle = __('Backups are stored locally by default.', 'updraftplus').' ';

		if (class_exists('UpdraftPlus_Addon_MoreStorage')) {
			$subtitle .= __('As a premium customer, you can store backups in multiple locations for added protection.', 'updraftplus').' '.__('Choose where they should be saved.', 'updraftplus');
		} else {
			$subtitle .= __('Add remote storage for an offsite copy.', 'updraftplus').' '.__('You can change this anytime.', 'updraftplus').' ('.__('Premium users can send backups to multiple locations', 'updraftplus').').';
		}

		return array(
			'id' => 'remote_storage_setup',
			'icon' => 'cloud-upload',
			'type' => 'settings',
			'title' => __('Set up your remote storage', 'updraftplus'),
			'subtitle' => $subtitle,
			'groups' => $this->get_remote_storage_groups(),
			'fields' => $this->get_remote_storage_fields(),
			'button' => array(
				'id' => 'save_remote_storage',
				'label' => __('Save and continue', 'updraftplus'),
				'icon' => 'EastRoundedIcon',
			),
			'skip_step' => array(
				'icon' => 'info',
				'tooltip' => array(
					'text' => __('You can configure remote storage later from the plugin settings.', 'updraftplus')
				),
			)
		);
	}

	/**
	 * Get the newsletter onboarding step configuration.
	 *
	 * @return array The configuration array used to render the newsletter of the onboarding flow
	 */
	private function get_email_step() {
		return array(
			'id' => 'email',
			'type' => 'email',
			'icon' => 'mail',
			'title' => __('Stay in the loop', 'updraftplus'),
			'subtitle' => __('Join our newsletter for backup, restore and migrate tips and best practices.', 'updraftplus').' '.__('Delivered straight to your inbox.', 'updraftplus'),
			'fields' => array(
				array(
					'id' => 'email_reports_mailinglist',
					'key' => 'email_reports_mailinglist',
					'type' => 'email',
					'label' => __('Email', 'updraftplus'),
					'default' => '',
				),
				array(
					'id' => 'tips_tricks_mailinglist',
					'key' => 'tips_tricks_mailinglist',
					'type' => 'checkbox',
					'label' => __('I agree to receive emails with tips, updates and marketing content.', 'updraftplus').' '.__('I understand I can unsubscribe at any time.', 'updraftplus'),
					'default' => false,
					'show_privacy_link' => true,
				),
			),
			'button' => array(
				'id' => 'save',
				'label' => __('Save and continue', 'updraftplus'),
				'icon' => 'EastRoundedIcon',
			),
		);
	}

	/**
	 * Get the plugin installer onboarding step configuration.
	 *
	 * @return array The configuration array used to render the plugin installer of the onboarding flow
	 */
	private function get_plugins_step() {
		return array(
			'id' => 'plugins',
			'type' => 'plugins',
			'icon' => 'plugin',
			'first_run_only' => false,
			'title' => __('Recommended for your setup', 'updraftplus'),
			'title_conditional' => array(
				'all_installed' => __('Best-practice plugins enabled', 'updraftplus'),
			),
			'subtitle' => __('We\'ve carefully handpicked these plugins to match your website\'s setup, so everything works just the way it should.', 'updraftplus'),
			'subtitle_conditional' => array(
				'all_installed' => __('Wow, your site already meets all our plugin recommendations, let\'s move on.', 'updraftplus'),
			),
			'fields' => array(
				array(
					'id' => 'plugins',
					'type' => 'plugins',
				),
			),
			'button' => array(
				'id' => 'save',
				'label' => __('Install and continue', 'updraftplus'),
				'icon' => 'EastRoundedIcon',
			),
		);
	}

	/**
	 * Get the upgrade plugin onboarding step configuration.
	 *
	 * @return array The configuration array used to render the upgrade plugin of the onboarding flow
	 */
	private function get_upgrade_step() {
		return array(
			'id' => 'upgrade',
			'icon' => 'bolt',
			'title' => __('Upgrade to premium', 'updraftplus'),
			'subtitle' => __('Unlock advanced backup, restore, and migration features for maximum control and peace of mind.', 'updraftplus'),
			'bullets' => $this->get_premium_bullets(),
			'enable_premium_btn' => true,
		);
	}

	/**
	 * Get the complete onboarding step configuration.
	 *
	 * @return array The configuration array used to render the complete of the onboarding flow
	 */
	private function get_completed_step() {
		return array(
			'id' => 'completed',
			'type' => 'completed',
			'icon' => 'CheckRoundedIcon',
			'title' => __('You\'re all set', 'updraftplus'),
			'title_conditional' => array(
				'isInstalling' => __('Almost done, finalizing...', 'updraftplus'),
			),
			'subtitle' => __('UpdraftPlus is ready to protect your site.', 'updraftplus'),
			'bullets' => $this->is_premium ? $this->get_premium_bullets() : array(),
			'button' => array(
				'id' => 'finish',
				'label' => __('Go to settings', 'updraftplus'),
			),
		);
	}

	/**
	 * List of premium features.
	 *
	 * @var array
	 */
	private function get_premium_bullets() {
		return array(
			array(
				__('Incremental backups', 'updraftplus'),
			),
			array(
				__('Data anonymization', 'updraftplus'),
			),
			array(
				__('Multi-destination storage', 'updraftplus'),
			),
			array(
				__('Site-to-site migration', 'updraftplus'),
			),
			array(
				__('Priority support and more', 'updraftplus'),
			),
		);
	}

	/**
	 * Retrieves the full list of onboarding steps used during the setup process.
	 *
	 * This method initializes and returns the sequence of steps that guide users
	 * through the onboarding workflow.
	 *
	 * @return array List of onboarding steps.
	 */
	public function get_onboarding_steps() {
		global $updraftplus_addons2;

		$steps = array();
		$steps[] = $this->get_intro_step();

		if ($updraftplus_addons2 && true !== $updraftplus_addons2->connection_status()) $steps[] = $this->get_license_step();

		$steps[] = $this->get_backup_settings_step();
		$steps[] = $this->get_remote_storage_step();
		$steps[] = $this->get_email_step();
		if ((!is_multisite() || is_network_admin()) && current_user_can('install_plugins') && (!defined('DISALLOW_FILE_MODS') || !DISALLOW_FILE_MODS)) $steps[] = $this->get_plugins_step();

		if (!$this->is_premium) $steps[] = $this->get_upgrade_step();

		$steps[] = $this->get_completed_step();

		return $steps;
	}

	/**
	 * Check whether or not an admin AJAX action comes from the onboarding wizard request
	 *
	 * @return bool True if onboarding rest ajax request, false otherwise
	 */
	private function is_onboarding_rest_ajax() {
		global $pagenow;
		// This was added to prevent issues during plugin installations performed through admin-ajax.php. When onboarding is completed and the completion flag is set, the rest_api_fallback is no longer registered. This check ensures that the rest_api_fallback remains available for AJAX requests when needed.
		return ((defined('DOING_AJAX') && DOING_AJAX) || 'admin-ajax.php' === $pagenow) && isset($_REQUEST['action']) && 'updraftplus_onboarding_rest_api_fallback' === $_REQUEST['action'];
	}

	/**
	 * Checks if the current request is an onboarding REST API request.
	 *
	 * This function verifies whether the current action is 'init' and whether the request URL
	 * matches the onboarding endpoint pattern for UpdraftPlus REST API versioning.
	 *
	 * @return bool True if the current request is an onboarding REST request, false otherwise.
	 */
	private function is_onboarding_rest_request() {
		if (!doing_action('init')) return false;
		$path = (string) filter_input(INPUT_SERVER, 'REQUEST_URI');
		if (empty($path)) $path = UpdraftPlus_Manipulation_Functions::fetch_superglobal('server', 'REQUEST_URI', '');
		$path = '' !== $path ? parse_url($path, PHP_URL_PATH) : '/';
		$query_string = (string) filter_input(INPUT_SERVER, 'QUERY_STRING');
		if ('' === $query_string) $query_string = UpdraftPlus_Manipulation_Functions::fetch_superglobal('server', 'QUERY_STRING', '');
		$path = 1 === UpdraftPlus_Manipulation_Functions::is_url_encoded($path) ? rawurldecode($path) : urldecode($path);
		$query_string = 1 === UpdraftPlus_Manipulation_Functions::is_url_encoded($query_string) ? rawurldecode($query_string) : urldecode($query_string);
		return (bool) preg_match('/updraftplus\/v[1-9]+\/onboarding/i', $path) || (bool) preg_match('/rest_route=\/updraftplus\/v[1-9]+\/onboarding/i', $query_string);
	}
}
