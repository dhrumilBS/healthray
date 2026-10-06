<?php
add_filter('hr_footer_type', fn() => 'landing');
$total_published = wp_count_posts('alternatives')->publish;
$has_results = have_posts();
?>
<section class="alt-archive-hero">
	<div class="container">
		<div class="alt-archive-hero__inner">
			<span class="alt-archive-hero__eyebrow">Comparison Guides</span>
			<h1 class="alt-archive-hero__title"><?php post_type_archive_title(); ?></h1>
			<p class="alt-archive-hero__lead">Compare Healthray against the hospital &amp; healthcare software your team already knows feature by feature, backed by real G2 reviews.</p>

			<div class="alt-archive-hero__search">
				<?php get_search_form(); ?>
			</div>

			<?php if ($total_published > 0): ?>
				<ul class="alt-archive-hero__stats">
					<li><?= hr_alt_icon('yes'); ?> 	<?= (int) $total_published; ?>+ in-depth comparisons</li>
					<li><?= hr_alt_icon('yes'); ?> Updated for 2026</li>
					<li><?= hr_alt_icon('yes'); ?> Backed by real G2 reviews</li>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="alt-archive-container<?= $has_results ? '' : ' no-results-section'; ?>">
	<div class="container">
		<?php if (!$has_results): ?>
			<div class="text-center">
				<?php get_template_part('template-parts/section', '404'); ?>
			</div>
		<?php else: ?>
			<div class="alt-archive-grid">
				<?php
				while (have_posts()):
					the_post();
					$post_id = get_the_ID();
					$subject_name = get_field('subject_name', $post_id);

					$profiles = get_field('competitor_profiles', $post_id) ?: array();
					$profile_count = count($profiles);
					$healthray = $profiles[0] ?? null;
					$rating_value = $healthray['rating_value'] ?? null;
					$rating_source = $healthray['rating_source'] ?? 'G2';

					$archive_tag = '';
					if ($subject_name) {
						$archive_tag = ($profile_count > 1 ? $profile_count . ' Alternatives to ' : 'Alternative to ') . $subject_name;
					}
					?>
					<article <?php post_class('alt-archive-card'); ?>>
						<a href="<?= esc_url(get_permalink()); ?>" class="alt-archive-card__image">
							<?php if (has_post_thumbnail()): ?>
								<?php the_post_thumbnail('medium_large', array('class' => 'alt-archive-card__img', 'alt' => get_the_title())); ?>
							<?php else: ?>
								<div class="alt-archive-card__img-fallback">
									<?= wp_get_attachment_image(27662, 'medium', false, array('class' => 'alt-archive-card__img-fallback-logo')); ?>
								</div>
							<?php endif; ?>
							<?php if ($archive_tag): ?>
								<span class="alt-archive-card__tag"><?= esc_html($archive_tag); ?></span>
							<?php endif; ?>
						</a>
						<div class="alt-archive-card__body">
							<?php if ($rating_value): ?>
								<div class="alt-archive-card__rating">
									<?= hr_alt_render_stars((float) $rating_value); ?>
									<span><?= esc_html($rating_value); ?>/5 on <?= esc_html($rating_source); ?></span>
								</div>
							<?php endif; ?>
							<h2 class="alt-archive-card__title">
								<a href="<?= esc_url(get_permalink()); ?>"><?= get_the_title(); ?></a>
							</h2>
							<a href="<?= esc_url(get_permalink()); ?>" class="alt-archive-card__link">
								View Comparison
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M5 12h14"></path>
									<path d="m12 5 7 7-7 7"></path>
								</svg>
							</a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php pagination_bar(); ?>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</div>
</section>

<?php if ($has_results): ?>
	<section class="alt-archive-cta text-center">
		<div class="container narrow">
			<div class="alt-mid-cta__box">
				<p>Ready to See Healthray in Action?</p>
				<p>Skip the research book a free walkthrough tailored to your hospital&rsquo;s workflows.</p>
				<a href="https://calendly.com/healthray/healthray-technologies-lead-meeting" class="alt-hero__cta-primary" target="_blank" rel="noopener noreferrer">
					Book a Free Demo
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
				</a>
			</div>
		</div>
	</section>
<?php endif; ?>