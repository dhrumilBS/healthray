<?php

use Duplicator\Addons\LiteBase\Notifications\MediaCleanupScanUpsell;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$productUrl   = $tplMng->getDataValueStringRequired('productUrl');
$dismissNonce = $tplMng->getDataValueStringRequired('dismissNonce');
?>
<div id="dupli-litebase-media-cleanup-scan"
     class="dupli-dismissable"
     data-dismiss-action="<?php echo esc_attr(MediaCleanupScanUpsell::DISMISS_NONCE_KEY); ?>"
     data-dismiss-nonce="<?php echo esc_attr($dismissNonce); ?>">
    <div class="dupli-litebase-did-you-know">
        <i class="fa fa-info-circle"></i>
        <?php esc_html_e('Reduce backup size by up to 40% by removing unused media files.', 'duplicator'); ?>
        <a class="dupli-litebase-upgrade-link"
           href="<?php echo esc_url($productUrl); ?>"
           target="_blank"
           rel="noopener noreferrer">
            <?php esc_html_e('Learn about WP Media Cleanup', 'duplicator'); ?>
        </a>
        <a href="#" class="dupli-dismissable-dismiss" title="<?php esc_attr_e('Dismiss this message.', 'duplicator'); ?>">
            <i class="fa fa-times" aria-hidden="true"></i>
        </a>
    </div>
</div>
