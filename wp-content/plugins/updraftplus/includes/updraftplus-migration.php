<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('UpdraftPlus_Login')) require_once('updraftplus-login.php');

class UpdraftPlus_Migration extends UpdraftPlus_Login {

	/**
	 * Pulls the appropriate message for the given code and translate it before
	 * returning it to the caller
	 *
	 * @internal
	 * @param string $code The code of the message to pull
	 * @return string The translated message
	 */
	protected function translate_message($code) {
		switch ($code) {
			case 'generic':
			default:
				return __('An error has occurred while processing your request.', 'updraftplus').' '.__('The server might be busy or you have lost your connection to the internet at the time of the request.', 'updraftplus').' '.__('Please try again later.', 'updraftplus');
		}
	}

	/**
	 * This function will check the passed in response from the remote call and check for various errors and return the parsed response
	 *
	 * @param array|WP_Error $response The response from the remote call
	 * @return array The parsed response
	 */
	private function parse_response($response) {
		
		if (is_wp_error($response)) {
			$response = array(
				'status' => 'error',
				'code' => $response->get_error_code(),
				'message' => $response->get_error_message(),
				'raw_response' => serialize($response),
			);
		} else {
			if (isset($response['status'])) {
				if ('error' === $response['status']) {
					$response = array(
						'status' => 'error',
						'code' => isset($response['code']) ? $response['code'] : -1,
						'message' => isset($response['message']) ? $response['message'] : $this->translate_message('generic'),
						'response' => $response,
					);
				}
			} else {
				$response = array(
					'status' => 'error',
					'message' => $this->translate_message('generic'),
					'raw_response' => serialize($response),
				);
			}
		}

		return $response;
	}

	/**
	 * Executes login or registration process. Connects and sends request to the teamupdraft.com
	 * and returns the response coming from the server
	 *
	 * @internal
	 * @param array   $data     The submitted form data
	 * @param boolean $register Indicates whether the current call is for a registration process or not. Defaults to false. Currently will always be false.
	 * @return array The response from the request
	 */
	protected function login_or_register($data, $register = false) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Unused parameter is required by abstract method signature.

		$response = $this->send_remote_request($data, 'updraftplus_migration_login');

		return $this->parse_response($response);
	}

	/**
	 * Sends a migration create request to the migration API.
	 *
	 * @internal
	 * @param array  $data         Data to send to the migration API.
	 * @param string $migrate_type The type of migration ('import' or 'export') to provide
	 *                             context for the API request. Defaults to an empty string.
	 * @return array The response from the request.
	 */
	public function process_migration($data, $migrate_type = 'export') {

		$response = $this->send_remote_request($data, 'updraftplus_migration_start');
		$response['migration_in_progress_html'] = $this->render_migration_notice($migrate_type);

		return $this->parse_response($response);
	}

	/**
	 * Starts an Extendify-based migration via the migration API.
	 *
	 * This method is used when a migration is initiated from Extendify.
	 * It forwards the prepared request data (including the temporary
	 * Extendify migration token) to the migration service and returns
	 * the parsed API response.
	 *
	 * @internal
	 * @param array $data Migration request parameters.
	 * @return array The response from the request.
	 */
	public function process_extendify_migration($data) {
		$response = $this->send_remote_request($data, 'updraftplus_migration_extendify_start');
		$response['migration_in_progress_html'] = $this->render_migration_notice('import');

		return $this->parse_response($response);
	}

	/**
	 * Polls the migration job status from the migration API.
	 *
	 * @internal
	 * @param array $data Contains 'identifier' for the job and any additional data.
	 * @return array Migration status response.
	 */
	public function process_migration_status($data) {

		if (!empty($data['extendify_access_token'])) {
			$response = $this->send_remote_request($data, 'updraftplus_migration_extendify_status');
		} else {
			$response = $this->send_remote_request($data, 'updraftplus_migration_status');
		}

		$status_response = $this->parse_response($response);

		if (empty($status_response['data'])) {
			return $status_response;
		}

		$status = isset($status_response['data']['status']) ? $status_response['data']['status'] : '';

		if (empty($status)) {
			return $status_response;
		}

		$migrate_type = isset($data['migrate_type']) ? $data['migrate_type'] : '';
		
		$status_response['data']['status_message'] = $this->get_readable_migration_status($status, $migrate_type);

		if (false !== strpos($status, 'FAILED')) {
			return $status_response;
		}

		$status_response['data']['overall_progress'] = $this->calculate_overall_migration_progress($status_response['data']);

		if (empty($migrate_type)) {
			return $status_response;
		}

		/**
		 * For "Send site" (export) migrations, display the last logged UpdraftPlus
		 * backup stage message while the source-site backup is running.
		 *
		 * The migration API status values during this phase are technical and not
		 * user-friendly (e.g. generic state transitions), whereas the UpdraftPlus
		 * backup process already produces clear, human-readable progress messages.
		 *
		 * To improve the user experience, we surface the latest UpdraftPlus backup
		 * stage message instead of the raw migration status, so users can clearly
		 * see what the backup is doing while the export is in progress.
		 */
		if ('SOURCE_SITE_MIGRATION_STARTED' === $status && !empty($status_response['data']['migration_progress_bar_text'])) {
			$status_response['data']['status_message'] = $status_response['data']['migration_progress_bar_text'];
		}

		return $status_response;
	}

	/**
	 * Calculate the overall migration progress percentage shown to the user.
	 *
	 * The migration process consists of multiple phases that do not all provide
	 * numeric progress values from the Migration API. In particular, the initial
	 * setup steps (plugin installation, authentication, disk checks, key generation)
	 * complete before the actual backup and transfer begins.
	 *
	 * To avoid a poor user experience where the progress bar jumps suddenly from
	 * 0% to a high value, we map these setup steps to a fixed percentage range.
	 * This provides a smoother and more predictable progress indication.
	 *
	 * Once setup is complete, progress is calculated using weighted values for
	 * the migration (backup + transfer) and restore phases.
	 *
	 * Progress weighting:
	 * - Setup phase:    15%  (status-based mapping)
	 * - Migration phase: 75% (reported migration_progress)
	 * - Restore phase:   10% (reported restore_progress)
	 *
	 * This approach ensures:
	 * - Early visual feedback during setup
	 * - Accurate progress during data transfer
	 * - Fast completion during restore, which typically completes quickly
	 *
	 * @param array $status_data Migration status response data from the API.
	 *
	 * @return int Overall progress percentage (0–100).
	 */
	private function calculate_overall_migration_progress($status_data) {

		/**
		 * Setup phase progress mapping.
		 *
		 * These statuses represent preparatory steps before the actual migration
		 * begins. Each step advances the progress bar incrementally up to 15%.
		 */
		$setup_status_progress_map = array(
			'SOURCE_SITE_LOGIN_COMPLETE' => 2,
			'SOURCE_SITE_MULTISITE_DETECTION_COMPLETE' => 3,
			'DESTINATION_SITE_LOGIN_COMPLETE' => 4,
			'DESTINATION_SITE_PLUGIN_INSTALLATION_COMPLETE' => 5,
			'DESTINATION_SITE_ACTIVATION_COMPLETE' => 6,
			'DESTINATION_SITE_DISK_SPACE_CHECK_COMPLETE' => 7,
			'DESTINATION_SITE_INSTALL_UDP_COMPLETE' => 9,
			'DESTINATION_SITE_MIGRATION_KEY_GENERATION_COMPLETE' => 10,
			'SOURCE_SITE_PLUGIN_INSTALLATION_COMPLETE' => 11,
			'SOURCE_SITE_ACTIVATION_COMPLETE' => 12,
			'SOURCE_SITE_DISK_SPACE_CHECK_COMPLETE' => 13,
			'SOURCE_SITE_INSTALL_UDP_COMPLETE' => 15,
		);

		if (isset($setup_status_progress_map[$status_data['status']])) {
			return $setup_status_progress_map[$status_data['status']];
		}

		$migration_progress = isset($status_data['migration_progress']) ? (int) $status_data['migration_progress'] : 0;
		$restore_progress = isset($status_data['restore_progress']) ? (int) $status_data['restore_progress'] : 0;

		if ($restore_progress > 0) {
			return (int) min(
				100,
				round(90 + ($restore_progress * 0.1))
			);
		}

		if ($migration_progress >= 0) {
			return (int) min(
				90,
				round(15 + ($migration_progress * 0.75))
			);
		}
	}

	/**
	 * Convert migration API status codes into user-friendly, translatable messages.
	 *
	 * @param string $status       Raw status code returned by the migration API.
	 * @param string $migrate_type Type of migration ('import' or 'export').
	 * @return string Human-readable status message.
	 */
	private function get_readable_migration_status($status, $migrate_type) {

		/*
		 * Resolve the two labels used throughout all messages.
		 *
		 * import > source = remote site,  destination = current site
		 * export > source = current site, destination = remote site
		 *
		 * Every message string is built once using these two variables, so
		 * there is a single source of truth and no post-translation str_replace().
		 */
		if ('export' === $migrate_type) {
			$src  = __('current site', 'updraftplus');
			$dest = __('remote site',  'updraftplus');
		} else {
			// 'import' and any unrecognised value
			$src  = __('remote site',  'updraftplus');
			$dest = __('current site', 'updraftplus');
		}

		$migration_status_messages = array(
			// -------------------------------------------------
			// Source site: connection & preparation
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_LOGIN_COMPLETE'               => sprintf(__('Connected to the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_MULTISITE_DETECTION_COMPLETE' => sprintf(__('Checking %1$s configuration…', 'updraftplus'), $src),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_DISK_SPACE_CHECK_COMPLETE'    => sprintf(__('Checking disk space on the %1$s…', 'updraftplus'), $src),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_PLUGIN_INSTALLATION_COMPLETE' => sprintf(__('Preparing the %1$s for migration…', 'updraftplus'), $src),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_ACTIVATION_COMPLETE'          => sprintf(__('Activating UpdraftPlus on the %1$s…', 'updraftplus'), $src),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_INSTALL_UDP_COMPLETE'         => sprintf(__('UpdraftPlus installed on the %1$s.', 'updraftplus'), $src),

			// -------------------------------------------------
			// Destination site: connection & preparation
			// -------------------------------------------------

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_LOGIN_COMPLETE'                    => sprintf(__('Connected to the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_DISK_SPACE_CHECK_COMPLETE'         => sprintf(__('Checking disk space on the %1$s…', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_PLUGIN_INSTALLATION_COMPLETE'      => sprintf(__('Preparing the %1$s for migration…', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_ACTIVATION_COMPLETE'               => sprintf(__('Activating UpdraftPlus on the %1$s…', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_INSTALL_UDP_COMPLETE'              => sprintf(__('UpdraftPlus installed on the %1$s.', 'updraftplus'), $dest),

			'DESTINATION_SITE_MIGRATION_KEY_GENERATION_COMPLETE' => __('Preparing secure migration connection…', 'updraftplus'),

			// -------------------------------------------------
			// Migration execution
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_MIGRATION_STARTED'       => sprintf(__('Creating a backup of the %1$s…', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_MIGRATION_COMPLETE' => sprintf(__('Backup transferred to the %1$s.', 'updraftplus'), $dest),

			// -------------------------------------------------
			// Restore on destination
			// -------------------------------------------------

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_RESTORE_IN_PROGRESS' => sprintf(__('Restoring the site on the %1$s server…', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'DESTINATION_SITE_RESTORE_COMPLETE'    => sprintf(__('Site restored on the %1$s server.', 'updraftplus'), $dest),

			// -------------------------------------------------
			// Cleanup & finalization
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'SOURCE_SITE_CLEANUP_COMPLETE' => sprintf(__('Cleaning up temporary files on the %1$s…', 'updraftplus'), $src),

			// -------------------------------------------------
			// Success
			// -------------------------------------------------
			'SUCCESS' => __('Migration completed successfully.', 'updraftplus'),

			// -------------------------------------------------
			// Failures & errors
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_RESTORE_FAILED'               => sprintf(__('Restoration on the %1$s failed.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_RESTORE_FAILED'          => sprintf(__('Restoration on the %1$s failed.', 'updraftplus'), $dest),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_RESTORE'                 => sprintf(__('Restoration on the %1$s failed.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_DISK_SPACE_CHECK_FAILED'      => sprintf(__('Disk space check failed on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_DISK_SPACE_CHECK_FAILED' => sprintf(__('Disk space check failed on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UDP_INSTALL_FAILED'           => sprintf(__('UpdraftPlus installation failed on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UDP_INSTALL_FAILED'      => sprintf(__('UpdraftPlus installation failed on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_ACTIVATE_PLUGIN'      => sprintf(__('Unable to activate the UpdraftPlus plugin on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_ACTIVATE_PLUGIN' => sprintf(__('Unable to activate the UpdraftPlus plugin on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_ADD_SITE_IN_UPDRAFTPLUS'      => sprintf(__('Unable to add the %1$s to UpdraftPlus.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_ADD_SITE_IN_UPDRAFTPLUS' => sprintf(__('Unable to add the %1$s to UpdraftPlus.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site"), %2$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_PING_REMOTE_SITE_IN_UPDRAFTPLUS'      => sprintf(__('The %1$s could not get a response from the %2$s, so a direct site-to-site migration is not possible right now.', 'updraftplus'), $src, $dest) . ' ' .
				__('If sending directly from site to site does not work for you, there are three other methods you can try instead.', 'updraftplus') . ' ' .
				sprintf(
					/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
					__('%1$sRead the step-by-step guide, including screenshots.%2$s', 'updraftplus'),
					'<a href="https://teamupdraft.com/documentation/updraftplus/topics/migration/faqs/how-to-migrate-a-wordpress-site-with-updraftplus/" target="_blank" rel="noopener">',
					'</a>'
				),
			/* translators: %1$s: destination site label (e.g. "current site" or "remote site"), %2$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_PING_REMOTE_SITE_IN_UPDRAFTPLUS' => sprintf(__('The %1$s could not get a response from the %2$s, so a direct site-to-site migration is not possible right now.', 'updraftplus'), $dest, $src) . ' ' .
				__('If sending directly from site to site does not work for you, there are three other methods you can try instead.', 'updraftplus') . ' ' .
				sprintf(
					/* translators: %1$s: opening anchor tag, %2$s: closing anchor tag */
					__('%1$sRead the step-by-step guide, including screenshots.%2$s', 'updraftplus'),
					'<a href="https://teamupdraft.com/documentation/updraftplus/topics/migration/faqs/how-to-migrate-a-wordpress-site-with-updraftplus/" target="_blank" rel="noopener">',
					'</a>'
				),
			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_START_MIGRATION_IN_UPDRAFTPLUS'      => sprintf(__('Unable to start migration in UpdraftPlus on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_START_MIGRATION_IN_UPDRAFTPLUS' => sprintf(__('Unable to start migration in UpdraftPlus on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_CLEANUP_FAILED'      => sprintf(__('Cleanup of temporary files failed on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_CLEANUP_FAILED' => sprintf(__('Cleanup of temporary files failed on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_RETRIEVE_MIGRATION_INFO'      => sprintf(__('Unable to retrieve migration information on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_RETRIEVE_MIGRATION_INFO' => sprintf(__('Unable to retrieve migration information on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_GENERATE_MIGRATION_KEY'      => sprintf(__('Unable to generate migration key on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_GENERATE_MIGRATION_KEY' => sprintf(__('Unable to generate migration key on the %1$s.', 'updraftplus'), $dest),

			// -------------------------------------------------
			// Credential & login failures
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_CREDENTIALS_INVALID' => sprintf(__('The %1$s credentials are invalid.', 'updraftplus'), $src) . ' ' .
				/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
				sprintf(__('If you are sure the username and password are correct, the %1$s may be using two-factor authentication (2FA) or a CAPTCHA that is preventing login.', 'updraftplus'), $src) . ' ' .
				__('Please temporarily disable these and try again.', 'updraftplus'),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_CREDENTIALS_INVALID' => sprintf(__('The %1$s credentials are invalid.', 'updraftplus'), $dest) . ' ' .
				/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
				sprintf(__('If you are sure the username and password are correct, the %1$s may be using two-factor authentication (2FA) or a CAPTCHA that is preventing login.', 'updraftplus'), $dest) . ' ' .
				__('Please temporarily disable these and try again.', 'updraftplus'),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_LOGIN_URL_INVALID' => sprintf(__('The %1$s login URL is invalid.', 'updraftplus'), $src) . ' ' .
				__('If the site uses a custom login URL, please enter the correct one in the Advanced options.', 'updraftplus'),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_LOGIN_URL_INVALID' => sprintf(__('The %1$s login URL is invalid.', 'updraftplus'), $dest) . ' ' .
				__('If the site uses a custom login URL, please enter the correct one in the Advanced options.', 'updraftplus'),

			// -------------------------------------------------
			// Security / nonce errors
			// -------------------------------------------------

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_EXTRACT_NONCE_FOR_UPLOADING_PLUGIN'      => sprintf(__('Unable to extract security token (nonce) for uploading plugin on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_EXTRACT_NONCE_FOR_UPLOADING_PLUGIN' => sprintf(__('Unable to extract security token (nonce) for uploading plugin on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site"), %2$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_MULTISITE_TO_SINGLE_SITE_MIGRATION_NOT_SUPPORTED' => sprintf(__('The %1$s is a WordPress Multisite network, but the %2$s is a single site.', 'updraftplus'), $src, $dest) . ' ' .
				__('Migrating from a Multisite network to a single site is not supported.', 'updraftplus') . ' ' .
				__('Please make sure both sites are the same type - either both single sites or both Multisite networks.', 'updraftplus'),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site"), %2$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_SINGLE_SITE_TO_MULTISITE_MIGRATION_NOT_SUPPORTED' => sprintf(__('The %1$s is a single WordPress site, but the %2$s is a Multisite network.', 'updraftplus'), $src, $dest) . ' ' .
				__('Migrating from a single site to a Multisite network is not supported.', 'updraftplus') . ' ' .
				__('Please make sure both sites are the same type - either both single sites or both Multisite networks.', 'updraftplus'),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_RETRIEVE_BACKUP_NONCE'      => sprintf(__('Unable to retrieve backup security token (nonce) on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_RETRIEVE_BACKUP_NONCE' => sprintf(__('Unable to retrieve backup security token (nonce) on the %1$s.', 'updraftplus'), $dest),

			/* translators: %1$s: source site label (e.g. "remote site" or "current site") */
			'FAILED_SOURCE_SITE_UNABLE_TO_RETRIEVE_UDP_NONCE'      => sprintf(__('Unable to retrieve UpdraftPlus security token (nonce) on the %1$s.', 'updraftplus'), $src),

			/* translators: %1$s: destination site label (e.g. "current site" or "remote site") */
			'FAILED_DESTINATION_SITE_UNABLE_TO_RETRIEVE_UDP_NONCE' => sprintf(__('Unable to retrieve UpdraftPlus security token (nonce) on the %1$s.', 'updraftplus'), $dest),
		);

		/**
		 * Allow overriding or extending migration status labels.
		 *
		 * @param array  $migration_status_messages
		 * @param string $status
		 * @param string $migrate_type
		 */
		$migration_status_messages = apply_filters('updraftplus_migration_status_labels', $migration_status_messages, $status, $migrate_type);

		return isset($migration_status_messages[$status]) ? $migration_status_messages[$status] : $status;
	}

	/**
	 * Validate a site login flow for migration (2FA / CAPTCHA detection).
	 *
	 * @internal
	 * @param string $site_url             Site URL to validate.
	 * @param string $has_custom_login_url Whether the site has a custom login URL.
	 * @return array The response from the request.
	 */
	public function process_migration_site_validation_url($site_url, $has_custom_login_url) {
		$data = array(
			'url' => esc_url_raw($site_url),
			'has_custom_login_url' => $has_custom_login_url,
		);
		$response = $this->send_remote_request($data, 'updraftplus_migration_validate_site');

		return $this->parse_response($response);
	}

	/**
	 * Validate a site login flow for extendify migration (2FA / CAPTCHA detection).
	 *
	 * @internal
	 * @param string $site_url             Site URL to validate.
	 * @param string $access_token         Extendify access token.
	 * @param string $has_custom_login_url Whether the site has a custom login URL.
	 * @return array The response from the request.
	 */
	public function process_migration_extendify_site_validation_url($site_url, $access_token, $has_custom_login_url) {
		$data = array(
			'url' => esc_url_raw($site_url),
			'extendify_access_token' => $access_token,
			'has_custom_login_url' => $has_custom_login_url,
		);
		$response = $this->send_remote_request($data, 'updraftplus_migration_extendify_validate_site');
		return $this->parse_response($response);
	}

	/**
	 * Render the "migration in progress" notice.
	 *
	 * @param string $migrate_type Type of migration. Accepts 'import' or 'export'.
	 * @return string
	 */
	private function render_migration_notice($migrate_type) {

		if ('import' === $migrate_type) {
			$status_line = __('Once it\'s complete, you may be asked to update the WordPress database or to log in again.', 'updraftplus');
			$login_note  = __('Note: if you\'re asked to log in, use the credentials from the imported site, not this site\'s original login details.', 'updraftplus');
		} else {
			$status_line = __('Once it\'s complete, the destination site may ask you to update the WordPress database or to log in again.', 'updraftplus');
			$login_note  = __('Note: if you\'re asked to log in to the destination site, use this site\'s credentials, not the destination site\'s original login details.', 'updraftplus');
		}

		ob_start();
		?>
		<h3><?php esc_html_e('Migration in progress', 'updraftplus'); ?></h3>

		<div class="notice notice-info">
			<p><?php esc_html_e('The migration is now in progress — updates will appear below shortly.', 'updraftplus'); ?></p>
			<p><?php echo esc_html($status_line); ?></p>
		</div>

		<div class="notice notice-warning">
			<p>
				<span class="dashicons dashicons-warning" aria-hidden="true"></span>
				<?php echo esc_html($login_note); ?>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}
}
