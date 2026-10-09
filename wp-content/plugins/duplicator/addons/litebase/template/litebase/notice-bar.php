<?php

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng                   $tplMng
 */

$upgradeUrl   = $tplMng->getDataValueString('upgradeUrl');
$dismissNonce = $tplMng->getDataValueString('dismissNonce');
?>
<div class="dup-styles">
    <div id="dup-notice-bar"
         class="dupli-dismissable"
         data-dismiss-action="duplicator_notice_bar_dismiss"
         data-dismiss-nonce="<?php echo esc_attr($dismissNonce); ?>">
        <span class="dup-notice-bar-message">
            <?php
            printf(
                wp_kses(
                    _x(
                        '<strong>You\'re using Duplicator Lite.</strong> To unlock more features consider %1$supgrading to Pro%2$s',
                        '1 and 2 are opening and closing anchor tags',
                        'duplicator'
                    ),
                    ['strong' => []]
                ),
                '<a href="' . esc_url($upgradeUrl) . '" target="_blank" rel="noopener noreferrer">',
                '</a>'
            );
            ?>
            <a href="<?php echo esc_url($upgradeUrl); ?>"
               class="dup-upgrade-arrow" target="_blank" rel="noopener noreferrer">→</a>
        </span>
        <button type="button" class="dup-dismiss-button dupli-dismissable-dismiss"
                title="<?php esc_attr_e('Dismiss this message.', 'duplicator'); ?>">
        </button>
    </div>
</div>
