<?php
/**
 * Shared FAQ section for the module pages: OPD (82363), IPD (82361), Modules hub, Hospital billing (82741).
 * Styles: the .hr-faq component in css/modules-common.css. Script: js/modules-common.js (one open at a time).
 * The heading keeps each page's own look through head_class / lead_class.
 * The same items feed the FAQPage schema (hr_head_faqs filter in functions.php), so visible text and schema match.
 *
 * $args:
 *   'section_class' => classes on <section id="faq">          e.g. 'sec soft'
 *   'wrap_class'    => the page's container class               e.g. 'wrap' (OPD/IPD/hub) or 'in' (billing)
 *   'head_class'    => the page's section-heading class         e.g. 'sec-head' or 'sh'
 *   'lead_class'    => the page's lead-paragraph class          e.g. 'hx-lead' or 'lead'
 *   'title'         => heading HTML (a <span class="hl"> is allowed)
 *   'lead'          => line under the heading, HTML allowed (links)
 *   'items'         => array of array('q' => ..., 'a' => text) with optional 'html' (answer as HTML) or 'more' (HTML added after the answer)
 */
defined('ABSPATH') || exit;

$hr_faq = wp_parse_args($args, array(
	'section_class' => 'sec',
	'wrap_class'    => 'wrap',
	'head_class'    => 'sec-head',
	'lead_class'    => 'hx-lead',
	'title'         => 'Questions Before You Buy',
	'lead'          => 'Did not find yours? <a href="#demo">Ask it in the free demo</a>',
	'items'         => array(),
));
if (empty($hr_faq['items'])) {
	return;
}
?>
<section class="<?php echo esc_attr($hr_faq['section_class']); ?>" id="faq">
  <div class="<?php echo esc_attr($hr_faq['wrap_class']); ?> hr-faq">
    <div class="<?php echo esc_attr($hr_faq['head_class']); ?> hr-faq-head"><h2><?php echo wp_kses_post($hr_faq['title']); ?></h2><?php if ('' !== $hr_faq['lead']) : ?><p class="<?php echo esc_attr($hr_faq['lead_class']); ?>"><?php echo wp_kses_post($hr_faq['lead']); ?></p><?php endif; ?></div>
    <div class="hr-faq-list">
      <?php foreach (array_values($hr_faq['items']) as $hr_i => $hr_item) : ?>
      <details name="hr-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>><summary><?php echo esc_html($hr_item['q']); ?></summary><p><?php
        if (!empty($hr_item['html'])) {
            echo wp_kses_post($hr_item['html']);
        } else {
            echo esc_html($hr_item['a']);
            if (!empty($hr_item['more'])) {
                echo ' ' . wp_kses_post($hr_item['more']);
            }
        }
      ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
