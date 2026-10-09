/**
 * Handle special cases for survey initialization.
 */
function configureSurveyTabListener() {
	/**
	 * When the page is `page=wpcf7`, trigger the survey only the user is in our own tab `Actions`.
	 */
	if ( ! document.querySelector('#redirect-panel-tab') ) {
		return;
	}
	
	window.dispatchEvent(new CustomEvent('themeisle:survey:trigger:cancel'));
	document
		.querySelector( '#contact-form-editor-tabs li#redirect-panel-tab' )
		.addEventListener( 'click', () => {
			window.tsdk_formbricks?.init({});
		} );
}
window.addEventListener('themeisle:survey:loaded', configureSurveyTabListener);
