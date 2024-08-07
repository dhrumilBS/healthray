<?php global $post;  ?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>
<div class="inner-container">
	<?php include( locate_template( 'templates/page-header.php' ) ); ?>
	<?= wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); ?>
    <?= wp_kses_post($main_class_open); ?>
    <?php get_template_part('templates/content', 'page'); ?>    
    <?= wp_kses_post($main_class_close); ?>
	<?php include themo_sidebar_path(); ?>              
    <?= wp_kses_post($outer_container_close) . wp_kses_post($outer_row_close); ?>
</div><!-- /.inner-container -->