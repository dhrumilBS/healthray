<?php

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

/** @var string[] $proFeatures */
$proFeatures   = $tplMng->getDataValueArray('proFeatures');
$upgradeNowUrl = $tplMng->getDataValueString('upgradeNowUrl');
?>
<div class="dupli-litebase-welcome-upgrade-box">
    <div class="dupli-litebase-welcome-block">
        <h2><?php esc_html_e('Upgrade to PRO', 'duplicator'); ?></h2>
        <ul>
            <?php foreach ($proFeatures as $feature) : ?>
                <li>
                    <span class="dashicons dashicons-yes"></span>
                    <?php echo esc_html($feature); ?>
                </li>
            <?php endforeach; ?>
        </ul>
        <div class="dupli-litebase-welcome-upgrade-box-action">
            <a href="<?php echo esc_url($upgradeNowUrl); ?>"
               rel="noopener noreferrer"
               target="_blank"
               class="button large margin-bottom-0 dupli-litebase-welcome-upgrade-button">
                <?php esc_html_e('Upgrade Now', 'duplicator'); ?>
            </a>
        </div>
    </div>
</div>
