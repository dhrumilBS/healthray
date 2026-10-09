<?php
/**
 * Extension management class for Contact Form 7 Redirection plugin.
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WPCF7R_Extensions
 */
class WPCF7R_Extensions {

	/**
	 * Constructor for the class.
	 *
	 * Initializes the available extensions and sets them up for display.
	 */
	public function __construct() {
		$this->available_extensions = wpcf7_get_extensions();
		$this->set_available_extensions();
	}

	/**
	 * Set available extensions
	 */
	private function set_available_extensions() {
		foreach ( $this->available_extensions as $extension ) {
			$this->extensions[] = new WPCF7R_Extension( $extension );
		}
	}

	/**
	 * Initialize extensions table tab
	 */
	public function init() {
		$this->display();
	}
	/**
	 * View available extensions
	 *
	 * @return array An array of extension objects.
	 */
	public function get_extensions() {
		return $this->extensions;
	}

	/**
	 * Display the extensions list
	 *
	 * @return void
	 */
	public function display() {
		include WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'extensions.php';
	}
}
