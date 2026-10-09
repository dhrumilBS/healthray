<?php
/**
 * HTML-action-send-to-email file.
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send to mail action html fields
 */
foreach ( $this->get_action_fields() as $field ) {
	$this->render_field( $field, $prefix );
}
