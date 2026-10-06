<?php
/**
 * Logo strip: hospitals that use Healthray, with bed badges ($ppc_hospitals in setup.php).
 *
 * Shared by the "hims" PPC templates (temp-ppc-hims-*.php): included with plain include,
 * so it uses their variables ($ppc_assets, $ppc_hospitals, $ppc_icon_*) from templates/ppc-hims/setup.php.
 */

defined('ABSPATH') || exit;
?>
  <!-- 2. HOSPITALS + SIZE: "a hospital like mine uses this". Bed badge on each tile (confirmed numbers only). -->
  <section class="logos" aria-label="Hospitals using Healthray">
    <div class="wrap">
      <p class="lg-label">Hospitals that moved to Healthray</p>
      <div class="marquee">
        <div class="marquee-track">
          <?php // The list is printed twice for a seamless loop; the copy is hidden from screen readers. ?>
          <?php foreach (array(false, true) as $ppc_copy) : ?>
          <ul<?php echo $ppc_copy ? ' aria-hidden="true"' : ''; ?>>
            <?php foreach ($ppc_hospitals as $ppc_h) :
              $ppc_alt = $ppc_h['name'] . ($ppc_h['place'] ? ', ' . $ppc_h['place'] : ''); ?>
            <li>
              <span class="lg-tile<?php echo $ppc_h['logo'] ? '' : ' noimg'; ?><?php echo empty($ppc_h['zoom']) ? '' : ' lg-zoom'; ?>">
                <?php if ($ppc_h['logo']) : ?>
                <img src="<?php echo esc_url($ppc_h['logo']); ?>" alt="<?php echo $ppc_copy ? '' : esc_attr($ppc_alt); ?>" loading="lazy">
                <?php endif; ?>
                <span class="lg-name"><b><?php echo esc_html($ppc_h['name']); ?></b><?php if ($ppc_h['place']) : ?><small><?php echo esc_html($ppc_h['place']); ?></small><?php endif; ?></span>
              </span>
              <?php if ($ppc_h['beds']) : ?>
              <?php if (! empty($ppc_h['college'])) : ?>
              <span class="lg-badge mc"><?php echo $ppc_icon_cap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Medical college · <?php echo esc_html($ppc_h['beds']); ?></span>
              <?php else : ?>
              <span class="lg-badge"><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html($ppc_h['beds']); ?></span>
              <?php endif; ?>
              <?php endif; ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>
