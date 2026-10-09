<?php

use Duplicator\Addons\LiteBase\Utils\ExtraPlugins\ExtraItem;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$plugin = $tplMng->getDataValueObjRequired('plugin', ExtraItem::class);
?>
<div id="dupli-litebase-ai-wpvibe">
    <?php TplMng::getInstance()->render(
        'litebase/about-us/about-info/extra-plugin-item',
        ['plugin' => $plugin]
    ); ?>
</div>
