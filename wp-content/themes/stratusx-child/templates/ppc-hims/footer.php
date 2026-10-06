<?php
/**
 * Page footer (.foot): copyright, location, privacy link.
 *
 * Shared by the "hims" PPC templates (temp-ppc-hims-*.php): included with plain include,
 * so it uses their variables ($ppc_assets, $ppc_hospitals, $ppc_icon_*) from templates/ppc-hims/setup.php.
 */

defined('ABSPATH') || exit;
?>
<footer class="foot">
  <div class="wrap">
    <span>© <?php echo esc_html(wp_date('Y')); ?> Healthray Technologies Pvt. Ltd.<span class="foot-sep"> · </span><span
        class="foot-loc">Surat, Gujarat · Since 2019</span></span>
    <a href="https://healthray.com/privacy-policy/" rel="nofollow">Privacy policy</a>
  </div>
</footer>
