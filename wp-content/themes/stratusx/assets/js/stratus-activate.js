"use strict";
/**
 * Stratus Activate Custom JS Functions
 *
 * @author     Themovation <themovation@gmail.com>
 * @copyright  2014 Themovation INC.
 * @license    http://themeforest.net/licenses/regular
 * @version    1.1
 */

/*
 # On Window Load
 */

//======================================================================
// On Window Load - executes when complete page is fully loaded, including all frames, objects and images
//======================================================================
 jQuery( window ).load( function( $ ) {
	 "use strict";

	 // Showing Purchase Information with Input field
	 jQuery( '#purchase-code-notices' ).click( function( e ) {
	 	jQuery( '.purchase-code-wrapper' ).show();
	 	e.preventDefault();
	 } );

	 // Envato Form Validator
	 jQuery( '#th-stratus-envato' ).submit( function( e ) {
 		jQuery( '#th-stratus-envato .notice-msg' ).hide();
	 	if ( jQuery('#th-stratus-envato input[ name="envato_token" ]' ).val() == "" ) {
	 		jQuery( '#th-stratus-envato .error-msg' ).show();
			e.preventDefault();
		} else {
	 		jQuery( '#th-stratus-envato .error-msg' ).hide();
		}
	 } );

	 // Purchase Form Validator
	 jQuery( '#th-stratus-activate' ).submit( function( e ) {
 		jQuery( '#th-stratus-activate .notice-msg' ).hide();
	 	if ( jQuery('#th-stratus-activate input[ name="purchase_code_0" ]' ).val() == "" ) {
	 		jQuery( '#th-stratus-activate .error-msg' ).show();
			e.preventDefault();
		} else {
	 		jQuery( '#th-stratus-activate .error-msg' ).hide();
		}
	 } );

	 // Open Modal Popup
	 jQuery( '.th-modal-link' ).click( function( e ) {
	 	e.preventDefault();
	 	var id = jQuery( this ).attr( 'attr-popup' );
	 	if ( id && jQuery( '#' + id + '.th-modal' ).length > 0 ) {
	 		jQuery( '#' + id + '.th-modal' ).show();
	 	}
	 } );

	 // Close Modal Popup
	 jQuery('.th-modal .th-close').click( function(e) {
	 	e.preventDefault();
		jQuery(this).closest( '.th-modal' ).hide();
	 } );

} );
