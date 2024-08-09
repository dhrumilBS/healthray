<?php

/**
 * Plugin Name: My Elemets
 * Version: 1.2  
 * Author: My Elemets 
 * Text Domain: my-elemets  
 */

define('ML_URL', plugins_url('/', __FILE__));
define('ML_PATH', plugin_dir_path(__FILE__));

add_action('elementor/elements/categories_registered', 'ml_add_elementor_widget_categories', 1);
function ml_add_elementor_widget_categories($elements_manager)
{
	$elements_manager->add_category(
		'my-element',
		[
			'title' => 'Custom',
			'icon' => 'font',
		]
	);
}

add_action('wp_enqueue_scripts', 'ML_init');
function ML_init()
{
	wp_enqueue_style('owl.carousal', ML_URL . 'css/owl.carousel.min.css', [], rand());
	wp_enqueue_style('tabs', ML_URL . 'css/tabs.css', [], rand());

	wp_enqueue_script('owl.carousal', ML_URL . 'js/owl.carousel.min.js', array('jquery'), rand());
	wp_enqueue_script('my-element-js', ML_URL . 'js/script.js', array('jquery'), rand());

	wp_enqueue_style('ml-swiper', ML_URL.'css/swiper-bundle.min.css', [], rand());
	wp_enqueue_script('ml-swiper', ML_URL.'js/swiper-bundle.min.js', array(), false, true);

	wp_enqueue_style('custom-elementor-widgets', plugins_url('/assets/css/style.css', __FILE__));
	wp_enqueue_script('custom-elementor-widgets', plugins_url('/assets/js/script.js', __FILE__), array('jquery'), false, true);
}

add_filter('elementor/widgets/register', 'my_elements');
function my_elements()
{
	require_once ML_PATH . 'elements/doctor-reviews.php';
	require_once ML_PATH . 'elements/fancybox-list.php';
	require_once ML_PATH . 'elements/social-rating.php';
	require_once ML_PATH . 'elements/topbar.php';
	require_once ML_PATH . 'elements/footer.php';
	require_once ML_PATH . 'elements/blog-faq.php';
	require_once ML_PATH . 'elements/link-group.php';
	require_once ML_PATH . 'elements/link-group-2.php';
	require_once ML_PATH . 'elements/testimonial/controls.php';
	require_once ML_PATH . 'elements/healthray-tabs/controls.php';
	require_once ML_PATH . 'elements/custom-toggle.php';
	require_once ML_PATH . 'elements/product-slider.php';
	require_once ML_PATH . 'elements/product-slider-2.php';
	require_once ML_PATH . 'elements/ehr-product-slider-3.php';
	require_once ML_PATH . 'elements/pms-product-slider.php';
	require_once ML_PATH . 'elements/swiper-grid.php';
	require_once ML_PATH . 'elements/alternativ.php';

}