<?php
/**
 * Shared setup for the "hims" PPC templates (temp-ppc-hims-*.php): asset path, lead form
 * (hero card + popup), hospital logo list and small icons. Included at the top of each template.
 */

defined('ABSPATH') || exit;

$ppc_assets = get_stylesheet_directory_uri() . '/assets/ppc-hims';

/*
 * Lead form: the same stepper form in the hero card and in the popup that every
 * "Book a free demo" button opens (instead of the site-wide popup form).
 */
$ppc_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';
// Popup heading/text: set $ppc_popup = array('title' => ..., 'text' => ...) in the template before including this file.
$ppc_popup = array_merge(
	array(
		'title' => 'Book a free demo',
		'text'  => "A free 30-minute demo, set up for your hospital's size and departments.",
	),
	isset($ppc_popup) ? (array) $ppc_popup : array()
);
add_filter('hr_ppc_popup_args', function () use ($ppc_form, $ppc_popup) {
	return array(
		'form'  => $ppc_form,
		'title' => $ppc_popup['title'],
		'text'  => $ppc_popup['text'],
	);
});
$ppc_hospitals = array(
	array( 'name' => 'HZB Arogyam Multispeciality Hospital', 'place' => 'Hazaribagh, Jharkhand', 'beds' => '150 beds', 'logo' => $ppc_assets . '/hzb-arogyam-multispeciality-hospital.webp' ),
	array( 'name' => 'Vibrant Multispecialty Hospital', 'place' => 'Vapi', 'beds' => '120 beds', 'logo' => $ppc_assets . '/vibrant-hospital.webp' ),
	array( 'name' => 'Shraddha Arogya Mandir', 'place' => 'Vapi', 'beds' => '120+ beds', 'logo' => 'https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp' ),
	array( 'name' => 'Sri Manakula Vinayagar Medical College', 'place' => 'Puducherry', 'beds' => '1,100 beds', 'college' => true, 'logo' => $ppc_assets . '/shri-manakula-vinayak-medical-college.webp' ),
	array( 'name' => 'Parivar Super Speciality Hospital', 'place' => 'Madhya Pradesh', 'beds' => '140 beds', 'logo' => $ppc_assets . '/parivar-super-speciality-hospital.webp' ),
	array( 'name' => 'Jeevan Rekha Hospital', 'place' => 'West Bengal', 'beds' => '120 beds', 'logo' => $ppc_assets . '/jeevan-rekha-hospital.webp' ),
	array( 'name' => 'Mamta Medical College & Hospital', 'place' => 'Siwan, Bihar', 'beds' => '670 beds', 'college' => true, 'logo' => $ppc_assets . '/mamta-medical-college-hospital.webp' ),
	array( 'name' => 'Budha Baba Multispecialty Hospital', 'place' => 'Cuttack, Odisha', 'beds' => '130 beds', 'logo' => $ppc_assets . '/budha-baba-multispeciality-hospital.webp' ),
	array( 'name' => 'Heritage Medical College', 'place' => 'Varanasi', 'beds' => '1,000 beds', 'college' => true, 'logo' => $ppc_assets . '/heritage-medical-college.webp' ),
	array( 'name' => 'PIMS Multi-Superspeciality Hospital', 'place' => 'Udaipur', 'beds' => '120 beds', 'logo' => $ppc_assets . '/pims-multi-superspeciality-hospital.webp' ),
	array( 'name' => 'JJ Plus Hospitals', 'place' => '', 'beds' => '135 beds', 'logo' => $ppc_assets . '/jj-plus-hospitals.webp' ),
	array( 'name' => 'Jabalpur Hospital & Research Centre', 'place' => 'Jabalpur, MP', 'beds' => '350 beds', 'logo' => $ppc_assets . '/jabalpur-hospital-research-center.webp' ),
	array( 'name' => 'Prabh Aasra Charitable Hospital', 'place' => 'Punjab', 'beds' => '120 beds', 'logo' => 'https://healthray.com/wp-content/uploads/2025/08/Prabh-Aasra-Unified-Family.webp' ),
	array( 'name' => 'Heritage Hospital', 'place' => 'Noida', 'beds' => '100 beds', 'logo' => $ppc_assets . '/heritage-hospital.webp' ),
	array( 'name' => 'Universal Hospital', 'place' => 'Surat', 'beds' => '250 beds', 'logo' => $ppc_assets . '/universal-hospital.webp' ),
	array( 'name' => 'Jupiter Hospital Group', 'place' => 'Vadodara', 'beds' => '100 beds', 'logo' => $ppc_assets . '/jupiter-hospital-group.webp' ),
);

// Small inline icons (static markup, printed as is).
$ppc_icon_bed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>';
$ppc_icon_cap = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c3 2 9 2 12 0v-5"/></svg>';