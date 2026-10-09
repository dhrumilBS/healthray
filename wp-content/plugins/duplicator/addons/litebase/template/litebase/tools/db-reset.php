<?php

use Duplicator\Addons\LiteBase\LiteBase;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$plugin = $tplMng->getDataValueObj('plugin', ExtraItem::class);
?>
<div id="dupli-litebase-db-reset">
    <h1 class="dupli-litebase-db-reset-title">
        <?php esc_html_e('Clean & Reset WordPress Database', 'duplicator'); ?>
    </h1>

    <?php if ($plugin !== null && current_user_can('install_plugins')) : ?>
        <div class="dupli-litebase-db-reset-card">
            <?php TplMng::getInstance()->render(
                'litebase/about-us/about-info/extra-plugin-item',
                ['plugin' => $plugin]
            ); ?>
        </div>
    <?php endif; ?>

    <p class="dupli-litebase-db-reset-headline">
        <strong><?php esc_html_e('The Simplest Database Reset Solution', 'duplicator'); ?></strong>
    </p>

    <div class="dupli-litebase-db-reset-details">
        <div class="dupli-litebase-db-reset-screenshot">
            <img
                src="<?php echo esc_url(LiteBase::getAddonUrl() . '/assets/img/tools/db-reset-plugin.png'); ?>"
                alt="<?php esc_attr_e('Database Reset Pro Screenshot', 'duplicator'); ?>"
            >
        </div>
        <ul class="dupli-litebase-db-reset-features">
            <li>
                <i class="fa fa-caret-right"></i>
                <?php esc_html_e('One-Click Operation – No complex settings or configurations', 'duplicator'); ?>
            </li>
            <li>
                <i class="fa fa-caret-right"></i>
                <?php esc_html_e('Clear Visual Interface – Know exactly what will happen before you click', 'duplicator'); ?>
            </li>
            <li>
                <i class="fa fa-caret-right"></i>
                <?php esc_html_e('Instant Reset – Complete database reset in seconds, not minutes', 'duplicator'); ?>
            </li>
            <li>
                <i class="fa fa-caret-right"></i>
                <?php esc_html_e('No Learning Curve – If you can click a button, you can use this plugin', 'duplicator'); ?>
            </li>
        </ul>
    </div>
</div>
