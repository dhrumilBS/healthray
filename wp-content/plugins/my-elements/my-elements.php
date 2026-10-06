<?php

/**
 * Plugin Name: My Elemets
 * Version: 1.5
 * Author: My Elemets 
 * Text Domain: my-elemets  
 */

define('ML_URL', plugins_url('/', __FILE__));
define('ML_PATH', plugin_dir_path(__FILE__));
require_once plugin_dir_path(__FILE__) . '/elements/helper/slider-option.php';


// add_action('elementor/elements/categories_registered', 'ml_add_elementor_widget_categories', 5);
function ml_add_elementor_widget_categories($elements_manager)
{
	$elements_manager->add_category(
		'my-element',
		[
			'title' => 'Custom',
			'icon'  => 'fa fa-plug',
		]
	);

	$elements_manager->add_category(
		'my-element-slider',
		[
			'title' => 'my Element Slider',
			'icon'  => 'fa fa-plug',
		]
	);
}

add_action('elementor/elements/categories_registered', function ($elements_manager) {
	// Get existing categories
	$categories = $elements_manager->get_categories();

	// Insert your category at the right position
	$new_categories = [];
	foreach ($categories as $key => $category) {
		if ($key === 'layout') {
			$new_categories['my-element'] = [
				'title' => __('Custom', 'text-domain'),
				'icon'  => 'fa fa-plug',
			];
			$new_categories['my-element-slider'] = [
				'title' => __('my Element Slider', 'text-domain'),
				'icon'  => 'fa fa-plug',
			];
			$new_categories['my-element-slider2'] = [
				'title' => __('my Element Slider 2', 'text-domain'),
				'icon'  => 'fa fa-plug',
			];
		}
		$new_categories[$key] = $category;
	}

	// Re-register the categories
	foreach ($new_categories as $slug => $category) {
		$elements_manager->add_category($slug, $category);
	}
}, 5);


add_filter('wp_enqueue_scripts', 'ML_init');
// add_filter('elementor/widgets/register', 'ML_init');

function ML_init()
{
	wp_enqueue_style('my-elements', ML_URL . 'css/my-elements.css', []);
	wp_enqueue_script('my-element', ML_URL . 'js/script.js', array('jquery', 'owl.carousal'), '1.5', true );
	wp_enqueue_script('toggle-tabs', ML_URL . 'js/toggle-tabs.js', array('jquery', 'my-element'), '1.1');

	// Register style
	wp_register_style('ml-swiper-widget', ML_URL . 'css/swiper-widget.css', [], '1.0');
	wp_register_style('ml-slider-logo', ML_URL . 'css/slider-logo.css', [], '1.0');


	wp_register_style('ml-before-after-style', ML_URL . 'css/ml-before-after-style.css', [], '1.0');
	wp_register_script('ml-before-after-script', ML_URL . 'js/ml-before-after-script.js', array('jquery'), '1.1');
	
	wp_register_script('toggle-tab', ML_URL . 'js/toggle-tabs.js', array('jquery', 'my-element'), '1.1');
	
	
}

add_filter('elementor/widgets/register', 'my_elements');
function my_elements()
{
	require_once ML_PATH . 'elements/product-slider.php';
	require_once ML_PATH . 'elements/product-slider-2.php';
	require_once ML_PATH . 'elements/ehr-product-slider-3.php';
	require_once ML_PATH . 'elements/pms-product-slider.php';

	require_once ML_PATH . 'elements/swiper-grid.php';
	require_once ML_PATH . 'elements/trusted-standars.php';
	require_once ML_PATH . 'elements/slider-logo.php';

	require_once ML_PATH . 'elements/side-toggler.php';
	require_once ML_PATH . 'elements/slider-set/controls.php';

	require_once ML_PATH . 'elements/testimonial/controls.php';

	// require_once ML_PATH . 'elements/flow-widget.php';
	require_once ML_PATH . 'elements/workflow-widget.php';

	require_once ML_PATH . 'elements/healthray-tabs.php';
	require_once ML_PATH . 'elements/healthray-tabs-2.php';
	require_once ML_PATH . 'elements/doctor-reviews.php';
	require_once ML_PATH . 'elements/za-slider-client-review.php';
	require_once ML_PATH . 'elements/fancybox.php';
	require_once ML_PATH . 'elements/fancybox-list.php';
	require_once ML_PATH . 'elements/social-rating.php';

	require_once ML_PATH . 'elements/link-group.php';
	require_once ML_PATH . 'elements/link-group-2.php';

	require_once ML_PATH . 'elements/blog-faq.php';
	require_once ML_PATH . 'elements/custom-toggle.php';
	require_once ML_PATH . 'elements/alternativ.php';
	require_once ML_PATH . 'elements/service-card.php';

	require_once ML_PATH . 'elements/trust-content.php';
	require_once ML_PATH . 'elements/before-after-slider.php';
}