<?php

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$feature      = $tplMng->getDataValueStringRequired('feature');
$upgradeUrl   = $tplMng->getDataValueStringRequired('upgradeUrl');
$dismissNonce = $tplMng->getDataValueStringRequired('dismissNonce');
?>
<div id="dupli-packages-bottom-bar"
     class="dupli-dismissable"
     data-dismiss-action="duplicator_packages_bottom_bar_dismiss"
     data-dismiss-nonce="<?php echo esc_attr($dismissNonce); ?>">
    <i class="fa fa-info-circle dupli-packages-bottom-bar-icon" aria-hidden="true"></i>
    <div class="dupli-packages-bottom-bar-feature">
        <p><strong><?php esc_html_e('Upgrade to Pro to Unlock...', 'duplicator'); ?></strong></p>
        <p><?php echo esc_html($feature); ?></p>
    </div>
    <a href="<?php echo esc_url($upgradeUrl); ?>"
       class="button hollow margin-bottom-0 dupli-litebase-upgrade-btn"
       target="_blank" rel="noopener noreferrer">
        <?php esc_html_e('Upgrade Now & Save!', 'duplicator'); ?>
    </a>
    <a href="#" class="dupli-dismissable-dismiss" title="<?php esc_attr_e('Dismiss this message.', 'duplicator'); ?>">
        <i class="fa fa-times" aria-hidden="true"></i>
    </a>
</div>
