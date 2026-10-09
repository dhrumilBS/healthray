<?php

use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$email          = $tplMng->getDataValueString('email');
$subscribeNonce = $tplMng->getDataValueStringRequired('subscribeNonce');
?>
<i class="fa-solid fa-envelope-open-text dupli-litebase-first-backup-banner-icon"></i>
<div class="dup-sub-content">
    <h3><?php esc_html_e('Nice work, your backup is ready!', 'duplicator'); ?></h3>
    <div
        class="dupli-litebase-subscribe-form"
        data-subscribe-action="<?php echo esc_attr(EmailSubscribeForm::SUBSCRIBE_NONCE_KEY); ?>"
        data-subscribe-nonce="<?php echo esc_attr($subscribeNonce); ?>"
    >
        <div class="dupli-litebase-subscribe-input-area">
            <input
                type="email"
                class="dupli-litebase-subscribe-email"
                placeholder="<?php esc_attr_e('Email Address', 'duplicator'); ?>"
                value="<?php echo esc_attr($email); ?>"
            >
            <button
                type="button"
                class="button dupli-litebase-subscribe-button"
            >
                <?php esc_html_e('Subscribe', 'duplicator'); ?>
            </button>
        </div>
    </div>
    <p>
        <small><?php esc_html_e('Subscribe to get tips and product updates straight to your inbox.', 'duplicator'); ?></small>
    </p>
</div>
