<?php 
/*
check contact form 7 plugin is active.
*/
if(class_exists('WPCF7')){
	require NB_CPF_PATH. 'includes/settings.php';
	require NB_CPF_PATH. 'includes/country-text.php';
	require NB_CPF_PATH. 'includes/phone-text.php';
	require NB_CPF_PATH. 'includes/include-js-css.php';
}