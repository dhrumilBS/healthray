<?php 
add_action('widgets_init', 'hr_widgets_init');
function hr_widgets_init()
{
		for ($i = 1; $i <= 5; $i++) {
		register_sidebar(array(
			'name'          => sprintf(esc_html__('Lab Footer Column %1$s', 'stratus'), $i),
			'id'            => "lab-footer-$i",
			'before_widget' => '<section class="widget lab-widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
	}
	
	for ($i = 1; $i <= 5; $i++) {
		register_sidebar(array(
			'name'          => sprintf(esc_html__('Pharma Footer Column %1$s', 'stratus'), $i),
			'id'            => "pharma-footer-$i",
			'before_widget' => '<section class="widget lab-widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
	}
	
	register_sidebar(array(
			'name'          => esc_html__('Header Review', 'stratus'),
			'id'            => "header-review-$i",
			'before_widget' => '<section class="widget header-review-widget %1$s %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
		));
		
}