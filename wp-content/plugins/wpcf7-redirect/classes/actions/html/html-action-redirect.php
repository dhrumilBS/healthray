<?php
/**
 * HTML action redirect
 *
 * @package Redirection_For_Contact_Form_7
 */

defined( 'ABSPATH' ) || exit;

/**
 * HTML itterator to display redirect actions fields.
 */
foreach ( $this->get_action_fields() as $field ) {
	$this->render_field( $field, $prefix );
}
