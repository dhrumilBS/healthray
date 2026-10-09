<?php
/**
 * Class WPCF7R_Action_FireScript file - handles JavaScript actions
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$is_legacy_user = get_option( \Wpcf7_Redirect::LEGACY_FIRE_SCRIPT_OPTION_FLAG, false );

		if ( 'yes' !== $is_legacy_user ) {
			return;
		}

		register_wpcf7r_actions(
			'FireScript',
			__( 'Fire JavaScript', 'wpcf7-redirect' ),
			'WPCF7R_Action_FireScript',
			2
		);
	}
);

/**
 * Class to handle JavaScript execution actions for Contact Form 7
 *
 * This class allows executing custom JavaScript code after form submission.
 */
class WPCF7R_Action_FireScript extends WPCF7R_Action {

	/**
	 * A constant defining the action slug.
	 *
	 * @var string
	 */
	const ACTION_SLUG = 'FireScript';

	/**
	 * Constructor for the fire script action
	 *
	 * @param object $post Post object representing the action settings.
	 */
	public function __construct( $post ) {
		parent::__construct( $post );
	}

	/**
	 * Get the fields relevant for this action
	 *
	 * @return array Array of field definitions for the action settings form.
	 */
	public function get_action_fields() {
		return array_merge(
			array(
				'script'           => array(
					'name'        => 'script',
					'type'        => 'textarea',
					'label'       => __( 'Paste your JavaScript here.', 'wpcf7-redirect' ),
					'sub_title'   => esc_html( __( '(Don\'t use <script> tags)', 'wpcf7-redirect' ) ),
					'placeholder' => __( 'Paste your JavaScript here', 'wpcf7-redirect' ),
					'value'       => $this->get( 'script' ),
				),
				'short-tags-usage' => array(
					'name'          => 'short-tags-usage',
					'type'          => 'notice',
					'label'         => __( 'Notice!', 'wpcf7-redirect' ),

					'sub_title'     => __( 'You can use form tags to add data from the submission.', 'wpcf7-redirect' ) . '<div>' . $this->get_formatted_mail_tags() . '</div>',
					'placeholder'   => '',
					'class'         => 'field-notice-info',
					'show_selector' => '',
				),
				'general-alert'    => array(
					'name'          => 'general-alert',
					'type'          => 'notice',
					'label'         => __( 'Warning!', 'wpcf7-redirect' ),
					'sub_title'     => __(
						'This option is for developers only - use with caution. If the plugin does not redirect after you have added scripts, it means you have a problem with your script. Either fix the script, or remove it.',
						'wpcf7-redirect'
					),
					'placeholder'   => '',
					'class'         => 'field-notice-danger',
					'show_selector' => '',
				),
			),
			parent::get_default_fields()
		);
	}

	/**
	 * Get settings page
	 */
	public function get_action_settings() {
		$this->get_settings_template( 'html-action-send-to-email.php' );
	}

	/**
	 * Handle a simple redirect rule
	 *
	 * @param WPCF7_Submission $submission The form submission object.
	 * @return string The processed JavaScript code.
	 */
	public function process( $submission ) {

		$script = $this->get( 'script' );

		$script = $this->replace_tags( $script, true );

		return $script;
	}
}
