<?php
/**
 * Shared "How We Protect Your Data" section (Speciality Settings > Data protection).
 *
 * @package stratusx-child
 */
?>
<section class="sp-dp">
	<div class="wrap sp-dp__inner">
		<div class="sp-dp__text">
			<h2><?php echo hr_sp_inline(hr_sp_setting('dp_heading')); ?></h2>
			<?php echo wp_kses_post(hr_sp_setting('dp_text')); ?>
			<ul class="sp-dp__list">
				<?php foreach (hr_sp_lines(hr_sp_setting('dp_points')) as $hr_point) : ?>
					<li><?php echo esc_html($hr_point); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="sp-dp__media">
			<?php echo hr_sp_image(hr_sp_setting('dp_image'), 'full', array('alt' => 'Healthray certifications: HIPAA, GDPR, SOC 2, CMMI Level 5, ISO')); ?>
		</div>
	</div>
</section>
