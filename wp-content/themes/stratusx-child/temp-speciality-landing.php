<?php
/**
 * Template Name: Speciality Landing
 * Template Post Type: page
 *
 * Every "EMR software for {speciality}" page: the hero, then the page's
 * "Sections" rows in order, each rendered by
 * template-parts/speciality/{layout}.php.
 *
 * Body class:  landing-page speciality-landing
 * CSS:         css/speciality-landing.css, inlined. Only the container (.wrap)
 *              and section spacing rules come from css/common-landing.css,
 *              which is not loaded here; the rest is scoped to .sp-main
 * JS:          js/speciality-landing.js (testimonial slider)
 * Fields:      lib/acf-speciality.php - shared content under Pages > Speciality Settings
 * Schema:      FAQPage from the FAQ section, via healthray_head()
 *
 * No get_header()/get_footer(): base.php renders the site header and footer.
 */

$hr_sp_id = get_queried_object_id();
?>
<main class="sp-main">
	<?php
	get_template_part('template-parts/speciality/hero', null, hr_sp_hero($hr_sp_id));

	$hr_sp_feature_index = 0;
	foreach (hr_sp_sections($hr_sp_id) as $hr_sp_row) {
		$hr_sp_layout = (string) ($hr_sp_row['acf_fc_layout'] ?? '');
		if ('feature_row' === $hr_sp_layout) {
			$hr_sp_row['index'] = $hr_sp_feature_index++;
		}
		get_template_part('template-parts/speciality/' . str_replace('_', '-', sanitize_key($hr_sp_layout)), null, $hr_sp_row);
	}
	?>
</main>
