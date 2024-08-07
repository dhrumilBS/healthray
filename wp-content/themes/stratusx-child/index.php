<?php global $post; ?>
<section class="blog-hero hero">
	<div class="container">
		<div class="page-title">
			<?php
			if(is_author()){
				$author = get_queried_object(); 
			?> 
			<div class="profile-picture"> <?= get_avatar(get_the_author_meta('ID')); ?> </div>
			<h1><?= $author->display_name . '\'s Blogs'; ?></h1>
			<p class="description"><?= get_the_author_description(); ?></p>
			<?php } else { ?>
			<h1 class='entry-title header-default azsfdc'><?= wp_kses_post(roots_title());?> </h1>
			<?php } ?>
		</div> 
		<?php
		$categories = get_categories();
		if ($categories) {
		?>
		<div class="main-sidebar">
			<?php get_template_part('templates/category-list'); ?>
		</div>
		<?php } ?>
		<div class="sidebar-form">
			<?php echo get_search_form(); ?>
		</div>
	</div>
</section>
<div class="inner-container blog-container">
	<div class="container">
		<?php
		if (is_archive()) $class = "th-masonry-blog hr-archives";
		elseif (is_search()) $class = "th-masonry-blog hr-search";
		else $class = "th-masonry-blog"; 
		?>
		<div class="main" role="main">
			<section class="<?= $class; ?>">				 
				<div class="mas-blog row">
					<?php
					while (have_posts()) { 
						the_post();  ?>
					<div <?= post_class('mas-blog-post col-sm-6 col-md-4'); ?>>
						<?php get_template_part('templates/content'); ?>
					</div>
					<?php  	} ?>
				</div>
				<?php if ($wp_query->max_num_pages > 1) : ?>
				<nav class="post-nav">
					<?= pagination_bar($wp_query)?>
				</nav>
				<?php endif; ?>
			</section>
		</div><!-- /.main -->
	</div><!-- /.container -->
</div><!-- /.inner-container -->

