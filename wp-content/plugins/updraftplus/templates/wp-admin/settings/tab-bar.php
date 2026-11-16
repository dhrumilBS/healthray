<?php
if (!defined('ABSPATH')) die('No direct access allowed');
?>

<h2 class="nav-tab-wrapper">
<?php
foreach ($main_tabs as $updraftplus_tab_slug => $updraftplus_tab_label) {
	$updraftplus_tab_slug_as_attr = sanitize_title($updraftplus_tab_slug);
?>
	<a class="updraftplus-nav-tab <?php if ($tabflag == $updraftplus_tab_slug) echo 'nav-tab-active'; ?>" id="updraft-navtab-<?php echo esc_attr($updraftplus_tab_slug_as_attr);?>" href="<?php echo esc_url(UpdraftPlus::get_current_clean_url());?>#updraft-navtab-<?php echo esc_attr($updraftplus_tab_slug_as_attr);?>-content" ><?php echo esc_html($updraftplus_tab_label);?></a>
<?php
}
?>
</h2>
