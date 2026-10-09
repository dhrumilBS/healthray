<?php

/**
 * Backup details: WordPress Environment card (WordPress version, active theme, active plugins)
 */

defined("ABSPATH") or die("");

use Duplicator\Package\DupPackage;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package  = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$snapshot = $package->getEnvironmentSnapshot();
$themes   = $snapshot !== null ? $snapshot->getActiveThemes() : [];
$plugins  = $snapshot !== null ? $snapshot->getActivePlugins() : [];

$notAvailableLabel = __('N/A', 'duplicator');
$unknownVersion    = __('n/a', 'duplicator');
?>
<section class="dupli-backup-detail-card dupli-backup-detail-environment">
    <header class="dupli-backup-detail-card-head">
        <h2 class="dupli-backup-detail-card-title"><?php esc_html_e('WordPress Environment', 'duplicator'); ?></h2>
    </header>
    <div class="dupli-backup-detail-card-body">
        <div class="dupli-kv">
            <span class="dupli-kv-label"><?php esc_html_e('WordPress', 'duplicator'); ?></span>
            <span class="dupli-kv-value">
                <?php if (strlen($package->VersionWP) > 0) { ?>
                    <?php echo esc_html($package->VersionWP); ?>
                <?php } else { ?>
                    <span class="dupli-kv-empty"><?php echo esc_html($notAvailableLabel); ?></span>
                <?php } ?>
            </span>
        </div>
        <div class="dupli-kv">
            <span class="dupli-kv-label"><?php echo esc_html(_n('Theme', 'Themes', max(1, count($themes)), 'duplicator')); ?></span>
            <span class="dupli-kv-value">
                <?php if ($snapshot === null) { ?>
                    <span class="dupli-kv-empty"><?php echo esc_html($notAvailableLabel); ?></span>
                <?php } else { ?>
                    <?php foreach ($themes as $index => $theme) { ?>
                        <?php if ($index > 0) { ?>
                            <span>, </span>
                        <?php } ?>
                        <span class="dupli-kv-composite">
                            <span><?php echo esc_html($theme['name']); ?></span>
                            <span class="dupli-kv-sep">|</span>
                            <?php if (strlen($theme['version']) > 0) { ?>
                                <span><?php echo esc_html($theme['version']); ?></span>
                            <?php } else { ?>
                                <span class="dupli-list-unknown"><?php echo esc_html($unknownVersion); ?></span>
                            <?php } ?>
                        </span>
                    <?php } ?>
                <?php } ?>
            </span>
        </div>
        <div class="dupli-kv">
            <span class="dupli-kv-label">
                <?php esc_html_e('Plugins', 'duplicator'); ?>
                <?php if ($snapshot !== null) { ?>
                    <span class="dupli-kv-count">(<?php echo (int) $snapshot->getActivePluginCount(); ?>)</span>
                <?php } ?>
            </span>
            <span class="dupli-kv-value">
                <?php if ($snapshot === null) { ?>
                    <span class="dupli-kv-empty"><?php echo esc_html($notAvailableLabel); ?></span>
                <?php } elseif (count($plugins) === 0) { ?>
                    <span class="dupli-kv-empty"><?php esc_html_e('- no active plugins -', 'duplicator'); ?></span>
                <?php } else { ?>
                    <ul class="dupli-list dupli-list-plugins">
                        <?php foreach ($plugins as $plugin) { ?>
                            <li>
                                <span class="dupli-list-name"><?php echo esc_html($plugin['name']); ?></span>
                                <span class="dupli-list-meta">
                                    <?php if (strlen($plugin['version']) > 0) { ?>
                                        <?php echo esc_html($plugin['version']); ?>
                                    <?php } else { ?>
                                        <span class="dupli-list-unknown"><?php echo esc_html($unknownVersion); ?></span>
                                    <?php } ?>
                                </span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </span>
        </div>
    </div>
</section>
