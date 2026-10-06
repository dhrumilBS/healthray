<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Ml_Widget_Topbar extends Widget_Base
{
	public function get_name()
	{
		return 'ml-topbar';
	}

	public function get_title()
	{
		return __('Topbar', 'my-elements');
	}

	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$topbar = new \Elementor\Repeater();
		$topbar->add_control(
			'icon_name',
			[
				'label' => __('Icon Name', 'my-elements'),
				'type' => Controls_Manager::TEXT,
			],
		);

	}
	protected function render()
	{ 
?>
<div class="top-nav">
	<div class="container">
		<div class="row col-md-12">
			<div class="top-nav-icon-blocks top-nav-text">
				<div class="icon-block"><a target="_blank" href="mailto:contact@healthray.com"><i class="fa fa-envelope-open-o"></i><span>Contact Us</span></a></div>
				<div class="icon-block"><a href="tel:+919714874435"><i class="fa fa-mobile"></i><span>+91 97148 74435</span></a></div>
			</div>
			<div class="top-nav-icon-blocks">
				<div class="icon-block">
					<a target="_blank" href="https://www.facebook.com/Healthray.official/" class="customize-unpreviewable"><i class="fa fa-facebook"></i></a>
				</div>
				<div class="icon-block">
					<a target="_blank" href="https://www.youtube.com/@HealthrayS" class="customize-unpreviewable"><i class="fa fa-youtube"></i></a>
				</div>
				<div class="icon-block">
					<a target="_blank" href="https://twitter.com/healthray_" class="customize-unpreviewable"><i class="fa fa-twitter"></i></a>
				</div>
				<div class="icon-block">
					<a target="_blank" href="https://in.linkedin.com/company/healthrayapp" class="customize-unpreviewable"><i class="fa fa-linkedin"></i></a>
				</div>
				<div class="icon-block">
					<a target="_blank" href="https://www.instagram.com/healthray.official/" class="customize-unpreviewable"><i class="fa fa-instagram"></i></a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php    }
} 
Plugin::instance()->widgets_manager->register(new Ml_Widget_Topbar());
