<?php
/**
 * Walkthrough video player: muted loop on screen, play/pause + fullscreen (js/ppc-hims.js, section 3).
 *
 * Shared by the "hims" PPC templates (temp-ppc-hims-*.php): included with plain include,
 * so it uses their variables ($ppc_assets, $ppc_hospitals, $ppc_icon_*) from templates/ppc-hims/setup.php.
 */

defined('ABSPATH') || exit;
?>
      <div class="player" id="player">
        <?php // Autoplays muted and loops on screen, pauses off screen; play/pause and fullscreen buttons (js/ppc-hims.js, section 3). ?>
        <div class="player-screen">
          <img class="player-poster" loading="lazy" decoding="async"
            src="<?php echo esc_url($ppc_assets . '/walkthrough-poster.jpg'); ?>" width="1600" height="900"
            alt="Healthray dashboard: OPD visits, IPD admissions, bed occupancy, collections and today's patients">
          <video id="walkVideo" muted loop playsinline preload="none" disablepictureinpicture disableremoteplayback
            controlslist="nodownload noremoteplayback" width="1600" height="900" aria-hidden="true">
            <source src="https://healthray.com/wp-content/uploads/2026/10/healthray-walkthrough.mp4" type="video/mp4">
          </video>
        </div>
        <button type="button" class="vt" id="walkBtn" aria-label="Play the walkthrough">
          <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" />
          </svg>
          <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="6" y="5" width="4" height="14" rx="1" />
            <rect x="14" y="5" width="4" height="14" rx="1" />
          </svg>
        </button>
        <?php // Hidden by js/ppc-hims.js when the browser has no fullscreen support. ?>
        <button type="button" class="vt vt-fs" id="walkFs" aria-label="Watch the walkthrough full screen">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M4 9V5a1 1 0 0 1 1-1h4M15 4h4a1 1 0 0 1 1 1v4M20 15v4a1 1 0 0 1-1 1h-4M9 20H5a1 1 0 0 1-1-1v-4" />
          </svg>
        </button>
      </div>
