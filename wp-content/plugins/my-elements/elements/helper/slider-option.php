<?php

namespace Elementor;

if (! defined('ABSPATH')) exit;

class ML_Slider_Controls
{
	public function get_slider_btn_controls($obj)
	{
		$obj->start_controls_section(
			'section_control',
			[
				'label' => __('Slider Control', 'healthray'),
			]
		);

		$obj->add_control(
			'desk_num',
			[
				'label' => __('Desktop number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('3', 'healthray'),
			]
		);
		$obj->add_control(
			'lap_num',
			[
				'label' => __('Laptop number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('3', 'healthray'),

			]
		);
		$obj->add_control(
			'tab_num',
			[
				'label' => __('Tablet number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('2', 'healthray'),
			]
		);
		$obj->add_control(
			'mob_num',
			[
				'label' => __('Mobile number', 'healthray'),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('1', 'healthray'),
				'separator' => 'after',

			]
		);
		$obj->add_control(
			'autoplay',
			[
				'label'      => __('Autoplay', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'false',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$obj->add_control(
			'loop',
			[
				'label'      => __('Loop', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'false',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$obj->add_control(
			'nav_arrow',
			[
				'label'      => __('Navigation Arrow', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$obj->add_control(
			'dots',
			[
				'label'      => __('Dots', 'healthray'),
				'type'       => Controls_Manager::SELECT,
				'default'    => 'true',
				'options'    => [
					'true'       => __('True', 'healthray'),
					'false'          => __('False', 'healthray'),

				],

			]
		);
		$obj->add_responsive_control(
			'margin',
			[
				'label' => __('Space', 'elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],


			]
		);
		$obj->end_controls_section();
	}
}