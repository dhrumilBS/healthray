<section class="blog-hero hero-section">
	<div class="container">
		<div class="heading text-center">
			<h1> Healthray Blog </h1>
			<p>Discover expert analyses, healthcare trends, and actionable insights designed to help you stay informed and make smarter health decisions with Healthray.</p>
		</div>
		<?php
		if (get_categories()) {
			get_template_part('templates/category-list');
		} ?>

	</div>
</section>

<section class="sec-padded blog-container hr-blog-archive">
	<div class="container">
		<div class="sidebar-form hr-blog-archive__search">
			<?php echo get_search_form(); ?>
		</div>

		<?php
		$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

		$args = array(
			'post_type' => array('post'),
			'post_status' => array('publish'),
			'orderby' => 'date',
			'order' => 'DESC',
			'paged' => $paged
		);
		$widget_wp_query = new \WP_Query($args); ?>
		<section class="th-masonry-blog">
			<div class="mas-blog row">
				<?php
				while ($widget_wp_query->have_posts()) {
					$widget_wp_query->the_post();
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

	</div><!-- /.container -->
</section><!-- /.inner-container -->