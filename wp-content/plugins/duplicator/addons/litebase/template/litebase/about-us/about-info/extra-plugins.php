<?php

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraPluginsMng;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

if (!current_user_can('install_plugins')) {
    return;
}
?>
<div id="dupli-litebase-about-addons">
    <div id="dupli-litebase-about-addons-list">
        <div class="list">
            <?php
            ExtraPluginsMng::getInstance()->foreachCallback(function (ExtraItem $plugin): void {
                $resolved = $plugin->skipLite() && $plugin->getPro() !== null ? $plugin->getPro() : $plugin;
                TplMng::getInstance()->render(
                    'litebase/about-us/about-info/extra-plugin-item',
                    ['plugin' => $resolved]
                );
            });
            ?>
        </div>
    </div>
</div>
