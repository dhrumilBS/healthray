<?php global $post;  ?>
<?php include( locate_template( 'templates/page-layout.php' ) ); ?>

<div class="inner-container">
	<?php 
	// Page Header Template
	include( locate_template( 'templates/page-header.php' ) );
	
	echo wp_kses_post($outer_container_open) . wp_kses_post($outer_row_open); // Outer Tag Open
	
	echo wp_kses_post($main_class_open); /* OPEN MAIN CLASS */
	get_template_part('templates/content', 'page');	
	echo wp_kses_post($main_class_close); /* CLOSE MAIN CLASS */

	echo wp_kses_post($outer_container_close) . wp_kses_post($outer_row_close); // Outer Tag Close
	?>
</div><!-- /.inner-container -->