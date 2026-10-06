<section class="blog-hero hero-section index">
	<div class="container">
		<div class="heading text-center">

			<?php if (is_category()) : ?>
				<h1><?php single_cat_title(); ?></h1>
				<div><?= (category_description()); ?></div>

			<?php else : ?>
				<h1>Healthray Blog</h1>
				<p>Discover expert analyses, healthcare trends, and actionable insights designed to help you stay informed and make smarter health decisions with Healthray.</p>
			<?php endif; ?>
		</div>
		<?php
		if (get_categories()) {
			get_template_part('templates/category-list');
		}
		?>
	</div>
</section>

<?php $class = (!have_posts()) ? "no-results-section" : ''; ?>

<section class="blog-container hr-blog-archive sec-padded <?= esc_attr($class); ?>">
	<?php if (! have_posts()) { ?>
		<div class="text-center">
			<?php get_template_part('template-parts/section', '404'); ?>
		</div>
	<?php } else { ?>
		<div class="container">
			<div class="hr-blog-archive__search" style="max-width: 500px; margin: 0 auto 24px;">
				<?php get_search_form(); ?>
			</div>
			<?php
			$class = is_archive() ? "th-masonry-blog hr-archives"
				: (is_search() ? "th-masonry-blog hr-search"
					: "th-masonry-blog");
			?>

			<div class="<?= esc_attr($class); ?>">
				<div class="mas-blog row">
					<?php while (have_posts()) {
						the_post();  ?>
						<div <?php post_class('mas-blog-post'); ?>>
							<div class="mas-blog-post-inner">
								<?php get_template_part('templates/content'); ?>
							</div>
						</div>
					<?php } ?>
				</div>
				<?php pagination_bar(); ?>
			</div>
		</div>
	<?php } ?>
</section>