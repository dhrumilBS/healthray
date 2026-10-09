<?php

use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\BuildRequirements;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

ob_start();
SnapUtil::phpinfo();
$serverinfo = preg_replace('/.*<body>(.*?)<\/body>.*/s', '$1', ob_get_clean());

?>
<div class="dup-tool-server-info">
    <h2>
        <?php esc_html_e('Server Settings', 'duplicator'); ?>
    </h2>
    <hr>
    <?php
    $tplMng->render(
        'parts/tools/server_settings_table',
        [
            'serverSettings' => BuildRequirements::getServerSettingsData(),
        ]
    );
    ?>
    <hr>
    <div id='dupli-phpinfo'>
        <?php echo $serverinfo; ?>
    </div>
</div>