<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules Breeze class.
 *
 * Compatibility since: 1.1.0
 *
 * @class Cookie_Notice_Modules_Breeze
 */
class Cookie_Notice_Modules_Breeze {

	private $file_pattern = '(.*)/js/hu-options.js(.*)';

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		// actions
		add_action( 'plugins_loaded', [ $this, 'load_module' ], 11 );
	}

	/**
	 * Add compatibility to Breeze plugin.
	 *
	 * @return void
	 */
	public function load_module() {
		// check caching status
		$cache_active = (int) Breeze_Options_Reader::get_option_value( 'breeze-active' );

		// update 2.4.17
		if ( version_compare( Cookie_Notice()->db_version, '2.4.16', '<=' ) ) {
			if ( $cache_active === 1 ) {
				// clear cache
				$this->delete_cache();
			}

			// save options file
			$this->write_cc_options_file();
		}

		add_action( 'pre_update_option_breeze_file_settings', [ $this, 'update_breeze_file_settings' ], 10, 3 );

		if ( $cache_active === 1 ) {
			add_action( 'cn_configuration_updated', [ $this, 'delete_cache' ] );

			// is js minification active?
			if ( (int) Breeze_Options_Reader::get_option_value( 'breeze-minify-js' ) === 1 ) {
				// actions
				add_action( 'cn_configuration_updated', [ $this, 'save_cc_options' ], 10, 2 );
				add_action( 'cn_configuration_updated', [ $this, 'exclude_options_file' ] );

				// filters
				add_filter( 'cn_cookie_compliance_output', [ $this, 'update_cc_output' ] );
			}
		}
	}

	/**
	 * Update Breeze file settings.
	 *
	 * @param array $new_options
	 * @param array $old_options
	 * @param string $option
	 * @return void
	 */
	public function update_breeze_file_settings( $new_options, $old_options, $option ) {
		// is js minification active?
		if ( (int) $new_options['breeze-minify-js'] === 1 ) {
			// did not find pattern? add it
			if ( ! in_array( $this->file_pattern, $new_options['breeze-exclude-js'], true ) )
				$new_options['breeze-exclude-js'][] = $this->file_pattern;
		} else {
			// find pattern
			$key = array_search( $this->file_pattern, $new_options['breeze-exclude-js'], true );

			// found pattern?
			if ( $key !== false ) {
				// remove pattern
				unset( $new_options['breeze-exclude-js'][$key] );

				// reindex array
				if ( count( $new_options['breeze-exclude-js'] ) > 0 )
					$new_options['breeze-exclude-js'] = array_values( $new_options['breeze-exclude-js'] );
			}
		}

		return $new_options;
	}

	/**
	 * Save Cookie Compliance options.
	 *
	 * @param string $type
	 * @param array $options
	 * @return void
	 */
	public function save_cc_options( $type, $options ) {
		// allow for saving settings only
		if ( $type !== 'settings' )
			return;

		// get main instance
		$cn = Cookie_Notice();

		// update options to be fresh for get_cc_options
		$cn->options['general'] = $cn->multi_array_merge( $cn->options['general'], $options );

		// save file
		$this->write_cc_options_file();
	}

	/**
	 * Write Cookie Compliance options directly into file.
	 *
	 * @return void
	 */
	private function write_cc_options_file() {
		// open file
		$handle = fopen( COOKIE_NOTICE_PATH . 'js/hu-options.js', 'w' );

		// ready?
		if ( $handle !== false ) {
			// set transient with time
			set_transient( 'cn_breeze_js_expiration', time(), DAY_IN_SECONDS );

			// get options
			$options = Cookie_Notice()->frontend->get_cc_options();

			// save options
			fwrite( $handle, 'var huOptions = ' . wp_json_encode( $options ) . ';' );

			// close file
			fclose( $handle );
		}
	}

	/**
	 * Add external options JavaScript file to exclude array.
	 *
	 * @param string $type
	 * @return void
	 */
	public function exclude_options_file( $type ) {
		// allow for saving settings only
		if ( $type !== 'settings' )
			return;

		// get breeze file options
		$file_options = breeze_get_option( 'file_settings' );

		// did not find pattern?
		if ( ! in_array( $this->file_pattern, $file_options['breeze-exclude-js'], true ) ) {
			// add pattern
			$file_options['breeze-exclude-js'][] = $this->file_pattern;

			// update breeze file options
			breeze_update_option( 'file_settings', $file_options, true );
		}
	}

	/**
	 * Update Cookie Compliance output.
	 *
	 * @return string
	 */
	public function update_cc_output( $output ) {
		// get transient time
		$time = (int) get_transient( 'cn_breeze_js_expiration' );

		if ( $time === 0 )
			$time = time();

		// replace inline javascript with external file
		$output = preg_replace( '/<script(.*)var huOptions(.*)<\/script>/', '<script type="text/javascript" src="' . esc_url( COOKIE_NOTICE_URL . '/js/hu-options.js?' . $time ) . '"></script>', $output );

		return $output;
	}

	/**
	 * Delete all cache files.
	 *
	 * @return void
	 */
	public function delete_cache() {
		do_action( 'breeze_clear_all_cache' );
	}
}

new Cookie_Notice_Modules_Breeze();