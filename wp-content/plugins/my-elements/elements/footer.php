<?php

namespace Elementor;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use Elementor\Icons_Manager;

class Ml_Widget_Footer_1 extends Widget_Base
{

	public function get_name()
	{
		return 'footer-1';
	}

	public function get_title()
	{
		return esc_html__('Footer', 'elementor');
	}

	public function get_keywords()
	{
		return ['footer, logo'];
	}

	public function get_categories()
	{
		return ['my-element'];
	}

	protected function register_controls()
	{
		$this->start_controls_section('section', ['label' => __('Content', 'my-elements'),]);
		$this->add_control('tagline', ['label' => 'Tagline']);
	}


	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attributes('tagline', [
			'class' => 'tagline'
		]);
		$this->add_inline_editing_attributes('tagline', 'advance');



?>
<div class="hr-footer">
	<?php if ($settings['tagline']) ?><div <?= $this->get_render_attribute_string('tagline') ?>> <?= $settings['tagline'] ?> </div>
	<ul class="hr-contact">
			<li> <a href="<?= get_field('talk_to_team', 'option')['url']; ?>" target="_blank"><i class="fa-regular fa-phone"></i><?= get_field('talk_to_team', 'option')['title']; ?> </a> </li>
			<li> <a href="<?= get_field('customer_support_email', 'option')['url']; ?>" target="_blank"> <i class="fa-regular fa-envelope"></i> <?= get_field('customer_support_email', 'option')['title']; ?></a> </li>
			<li> <a href="<?= get_field('company_address', 'option')['url']; ?>" target="_blank"> <i class="fa-regular fa-location-dot"></i> <?= get_field('company_address', 'option')['title']; ?></a> </li>
		</ul>

		<ul class="hr-social">
			<li> <a href="https://www.facebook.com/Healthraytechnologies/" target="_blank"> <i class="fab fa-facebook-f"></i> </a> </li>
			<li> <a href="https://www.instagram.com/healthraytechnologies/" target="_blank"><i class="fab fa-instagram"></i> </a> </li>
			<li> <a href="https://in.linkedin.com/company/healthraytechnologies" target="_blank"> <i class="fab fa-linkedin-in"></i> </a> </li>
			<li> <a href="https://twitter.com/healthray_" class="twitter-x-footer" target="_blank"> <i class="fab fa-x-twitter"></i> </a> </li>
		</ul>
</div>
<?php }
}

Plugin::instance()->widgets_manager->register(new Ml_Widget_Footer_1());
