<?php

defined("ABSPATH") or die("");

use Duplicator\Package\PackageEnvironmentSnapshot;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$snapshot = $tplMng->getDataValueObjRequired('environmentSnapshot', PackageEnvironmentSnapshot::class);
$themes   = $snapshot->getActiveThemes();
$plugins  = $snapshot->getActivePlugins();
?>
<div class="dupli-backup-environment-details">
    <div class="dupli-kv dupli-environment-theme">
        <span class="dupli-kv-label">
            <?php echo esc_html(_n('Active Theme', 'Active Themes', count($themes), 'duplicator')); ?>
        </span>
        <span class="dupli-kv-value">
            <?php foreach ($themes as $theme) { ?>
                <span class="dupli-environment-theme-item">
                    <strong><?php echo esc_html($theme['name']); ?></strong>
                    <span class="dupli-environment-version">
                        <?php echo strlen($theme['version']) > 0 ? esc_html($theme['version']) : esc_html__('Unknown', 'duplicator'); ?>
                    </span>
                </span>
            <?php } ?>
        </span>
    </div>

    <div class="dupli-kv dupli-environment-plugins">
        <span class="dupli-kv-label">
            <?php esc_html_e('Active Plugins', 'duplicator'); ?>
            <span class="dupli-kv-count">(<?php echo count($plugins); ?>)</span>
        </span>
    </div>
    <ul class="dupli-list dupli-list-plugins">
        <?php foreach ($plugins as $plugin) { ?>
            <li>
                <span class="dupli-list-name"><?php echo esc_html($plugin['name']); ?></span>
                <span class="dupli-list-meta <?php echo strlen($plugin['version']) > 0 ? '' : 'dupli-list-unknown'; ?>">
                    <?php echo strlen($plugin['version']) > 0 ? esc_html($plugin['version']) : esc_html__('Unknown', 'duplicator'); ?>
                </span>
            </li>
        <?php } ?>
    </ul>
</div>
