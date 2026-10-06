<?php
/**
 * Blog / taxonomy archive.
 *
 * Card markup lives in templates/content.php (shared with index.php and
 * page-blogs.php). Existing class names are kept as-is so the styling in
 * css/custom.css keeps applying to every published post.
 */
?>
<?php get_template_part('template-parts/section', 'archive-hero'); ?>

<?php $class = (!have_posts()) ? 'no-results-section' : ''; ?>
<section class="inner-container blog-container hr-blog-archive sec-padded <?= esc_attr($class); ?>">
	<?php if (! have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">
			<div class="hr-blog-archive__search" style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>

			<section class="th-masonry-blog">
				<div class="mas-blog row">
					<?php
					while (have_posts()) {
						the_post();
					?>
						<div <?php post_class('mas-blog-post'); ?>>
							<div class="mas-blog-post-inner">
								<?php get_template_part('templates/content'); ?>
							</div>
						</div>
					<?php } ?>
				</div>

				<?php pagination_bar(); ?>
				<?php wp_reset_postdata(); ?>
			</section>
		</div>
	<?php } ?>
</section>
