<section class="blog-hero hero-section">
	<div class="container">
		<div class="heading text-center">

			<?php if (is_category()) : ?>
				<h1><?php single_cat_title(); ?></h1>
				<div><?= (category_description()); ?></div>

			<?php elseif (is_tag()) : ?>
				<h1>Tag: <?php single_tag_title(); ?></h1>
				<p>Explore articles tagged under <strong><?php single_tag_title(); ?></strong> for deeper insights and expert takes.</p>

			<?php elseif (is_author()) : ?>
				<h1>Posts by <?php the_author(); ?></h1>
				<p>Read the latest posts and perspectives by <strong><?php the_author(); ?></strong>.</p>

			<?php elseif (is_post_type_archive()) : ?>
				<h1><?php post_type_archive_title(); ?></h1>
				<p>Browse through our collection of <strong><?php post_type_archive_title(); ?></strong> posts.</p>

			<?php elseif (is_author()) : ?>
				<h1>Posts by <?php the_author(); ?></h1>
				<p>Read the latest posts and perspectives by <strong><?php the_author(); ?></strong>.</p>

			<?php elseif (is_date()) : ?>
				<h1>
					<?php
					if (is_day()) {
						echo 'Daily Archives: ' . get_the_date();
					} elseif (is_month()) {
						echo 'Monthly Archives: ' . get_the_date('F Y');
					} elseif (is_year()) {
						echo 'Yearly Archives: ' . get_the_date('Y');
					}
					?>
				</h1>
				<p>Browse posts from <?php echo get_the_date(); ?> and stay updated with the latest insights.</p>

			<?php else : ?>
				<h1>Healthray Blog</h1>
				<p>Discover expert analyses, healthcare trends, and actionable insights designed to help you stay informed and make smarter health decisions with Healthray.</p>
			<?php endif; ?>
		</div>

		<?php
		if (get_categories() && is_home()) {
			get_template_part('templates/category-list');
		}
		?>
	</div>
</section>