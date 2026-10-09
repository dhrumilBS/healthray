<?php

use Duplicator\Utils\Help\Help;
use Duplicator\Libs\Snap\SnapJson;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$helpPageUrl = SnapJson::jsonEncode(Help::getHelpPageUrl());
?>
<div id="dup-meta-screen" class="dup-styles"></div>
<div class="dup-header dup-styles">
    <img src="<?php echo esc_url(DUPLICATOR_PLUGIN_URL . 'assets/img/duplicator-header-logo.svg'); ?>" alt="Duplicator Logo">
    <a class="dup-global-help">
        <i class="fa fa-question-circle  fa-sm"></i> <?php esc_html_e('Help', 'duplicator'); ?>
    </a>
</div>
<script>
    jQuery(document).ready(function($) {
        $('.dup-global-help').click(function() {
            if (DupliJs.Help.isDataLoaded()) {
                DupliJs.Help.Display();
            } else {
                DupliJs.Help.Load('<?php echo esc_url_raw($helpPageUrl); ?>');
            }
        });
    });
</script>