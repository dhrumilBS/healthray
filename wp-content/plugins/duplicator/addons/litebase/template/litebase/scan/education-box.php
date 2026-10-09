<?php

use Duplicator\Addons\LiteBase\Notifications\EmailSubscribeForm;
use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng $tplMng
 */

$subscribed     = $tplMng->getDataValueBool('subscribed');
$email          = $tplMng->getDataValueString('email');
$subscribeNonce = $tplMng->getDataValueStringRequired('subscribeNonce');

$features   = [
    __('Scheduled Backups', 'duplicator'),
    __('Recovery Points', 'duplicator'),
    __('Secure File Encryption', 'duplicator'),
    __('Server to Server Import', 'duplicator'),
    __('File & Database Table Filters', 'duplicator'),
    __('Cloud Storage - Google Drive', 'duplicator'),
    __('Cloud Storage - Amazon S3', 'duplicator'),
    __('Cloud Storage - DropBox', 'duplicator'),
    __('Cloud Storage - OneDrive', 'duplicator'),
    __('Cloud Storage - FTP/SFTP', 'duplicator'),
    __('Drag & Drop Installs', 'duplicator'),
    __('Larger Site Support', 'duplicator'),
    __('Multisite Network Support', 'duplicator'),
    __('Email Alerts', 'duplicator'),
    __('Advanced Backup Permissions', 'duplicator'),
];
$feature    = $features[array_rand($features)];
$upgradeUrl = LiteBaseLinks::getUpgradeUrl('scan_did-you-know', $feature);
?>
<div class="dupli-litebase-scan-education-box">
    <div class="dupli-litebase-did-you-know">
        <i class="fas fa-info-circle"></i>
        <?php
        printf(
            /* translators: %s: Pro feature name */
            esc_html__('Did you know Duplicator Pro has: %s?', 'duplicator'),
            esc_html($feature)
        );
        ?>
        <a class="dupli-litebase-upgrade-link"
           href="<?php echo esc_url($upgradeUrl); ?>"
           target="_blank"
           rel="noopener noreferrer">
            <?php esc_html_e('Upgrade To Pro', 'duplicator'); ?>
        </a>
    </div>
    <?php if (!$subscribed) : ?>
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
            <div class="dupli-litebase-subscribe-desc">
                <small><?php esc_html_e('Get tips and product updates straight to your inbox.', 'duplicator'); ?></small>
            </div>
        </div>
    <?php endif; ?>
</div>
