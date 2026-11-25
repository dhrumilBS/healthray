<?php

/**
 * Common header of every PPC page (temp-ppc-*.php): the logo (not a link: no exits from an ads page), an optional
 * small label, and one button that opens the page's own demo form.
 *
 * get_template_part('templates/ppc/header', null, array(
 *   'cta'       => 'Book a free demo',          // button text
 *   'cta_short' => '',                          // shorter text on small phones (optional)
 *   'href'      => '#demo',
 *   'class'     => '',                          // hook class the page script listens to, e.g. 'hr-cta-btn', 'js-start'
 *   'attrs'     => array('data-lab-pop' => ''), // hook attributes, e.g. data-cta
 *   'label'     => '',                          // small label beside the logo, e.g. 'Pharmacy'
 * ));
 *
 * Styles: css/ppc-common.css, printed once by hr_ppc_common_css().
 */

defined('ABSPATH') || exit;

$hr_hd = wp_parse_args(
	isset($args) && is_array($args) ? $args : array(),
	array(
		'cta'       => 'Book a free demo',
		'cta_short' => '',
		'href'      => '#demo',
		'class'     => '',
		'attrs'     => array(),
		'label'     => '',
	)
);

hr_ppc_common_css();

$hr_hd_attrs = '';
foreach ((array) $hr_hd['attrs'] as $hr_hd_k => $hr_hd_v) {
	$hr_hd_attrs .= ' ' . esc_attr($hr_hd_k) . ('' === (string) $hr_hd_v ? '' : '="' . esc_attr($hr_hd_v) . '"');
}
?>
<header class="top ppc-hd">
	<div class="ppc-hd-in">
		<div class="ppc-hd-logo"><img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray" width="150" height="40"><?php if ('' !== $hr_hd['label']) : ?><span class="ppc-hd-sub"><?php echo esc_html($hr_hd['label']); ?></span><?php endif; ?></div>
		<a class="ppc-hd-btn<?php echo '' !== $hr_hd['class'] ? ' ' . esc_attr($hr_hd['class']) : ''; ?>" href="<?php echo esc_attr($hr_hd['href']); ?>"<?php echo $hr_hd_attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above ?>><?php if ('' !== $hr_hd['cta_short']) : ?><span class="ppc-hd-l"><?php echo esc_html($hr_hd['cta']); ?></span><span class="ppc-hd-s"><?php echo esc_html($hr_hd['cta_short']); ?></span><?php else : echo esc_html($hr_hd['cta']); endif; ?></a>
	</div>
</header>
