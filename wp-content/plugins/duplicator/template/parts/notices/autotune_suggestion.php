<?php

/**
 * Suggestion notice shown while AutoTune has never been run.
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$autoTuneUrl = $tplMng->getDataValueStringRequired('autoTuneUrl');
?>
<i class="fa-solid fa-wand-magic-sparkles dupli-notice-big-icon" aria-hidden="true"></i>
<div class="dup-sub-content">
    <h3><?php esc_html_e('Optimize Backup Settings with AutoTune', 'duplicator'); ?></h3>
    <p>
        <?php esc_html_e(
            'AutoTune has never been run on this site. It automatically tests real backups to find the most
            reliable build configuration for this server.',
            'duplicator'
        ); ?>
    </p>
    <p>
        <?php printf(
            esc_html__(
                'You can start it anytime from the %1$sAutoTune%2$s page, or dismiss this notice to hide it permanently.',
                'duplicator'
            ),
            '<a href="' . esc_url($autoTuneUrl) . '"><b>',
            '</b></a>'
        ); ?>
    </p>
</div>
