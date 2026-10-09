<?php

use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Package\SettingsUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$secureOn   = $tplMng->getDataValueInt('secureOn', ArchiveDescriptor::SECURE_MODE_NONE);
$securePass = $tplMng->getDataValueString('securePass');

$unavaliableMessage = '';
$encryptAvaliable   = SettingsUtils::isArchiveEncryptionAvailable($unavaliableMessage);

?>
<div class="archive-setup-tab dupli-security-section" >
    <p class="dupli-section-desc">
        <b><?php esc_html_e('Caution:', 'duplicator'); ?></b>
        <?php esc_html_e(
            'Passwords are case-sensitive and cannot be recovered. Without the password,
            the data inside an encrypted Backup cannot be restored, so store the password
            in a safe place, such as a password manager.',
            'duplicator'
        ); ?>
    </p>

    <div class="dup-form-item margin-bottom-1">
        <label class="lbl-larger" >
            <?php esc_html_e('Mode', 'duplicator') ?>:&nbsp;
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e('Security', 'duplicator'); ?>"
                data-tooltip="<?php $tplMng->renderEscAttr('admin_pages/packages/setup/security-tooltip-content'); ?>">
            </i>
        </label>
        <div class="input">
            <div class="secure-on-input-wrapper dupli-security-mode-group">
                <input
                    type="radio"
                    name="secure-on"
                    class="dupli-security-mode-radio"
                    id="secure-on-none"
                    onclick="DupliJs.EnableInstallerPassword(true)"
                    required
                    value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_NONE; ?>"
                    <?php checked($secureOn, ArchiveDescriptor::SECURE_MODE_NONE); ?>
                    data-parsley-multiple="secure-on-mltiple-error"
                    data-parsley-errors-container="#secure-on-parsely-error"
                >
                <label for="secure-on-none" class="button hollow secondary small"
                    data-tooltip-title="<?php esc_attr_e('No protection', 'duplicator'); ?>"
                    data-tooltip="<?php esc_attr_e(
                        'Backup archives are not encrypted and anyone can run the installer:
                        whoever gets the files can read their contents and restore the site. Keep them in a safe place!',
                        'duplicator'
                    ); ?>">
                    <i class="fa-solid fa-lock-open warning-color"></i>
                    <?php esc_html_e('No protection', 'duplicator') ?>
                </label>
                <input
                    type="radio"
                    name="secure-on"
                    class="dupli-security-mode-radio"
                    id="secure-on-inst-pwd"
                    value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_INST_PWD; ?>"
                    <?php checked($secureOn, ArchiveDescriptor::SECURE_MODE_INST_PWD); ?>
                    onclick="DupliJs.EnableInstallerPassword(true)"
                    data-parsley-multiple="secure-on-mltiple-error"
                >
                <label for="secure-on-inst-pwd" class="button hollow secondary small"
                    data-tooltip-title="<?php esc_attr_e('Installer password', 'duplicator'); ?>"
                    data-tooltip="<?php esc_attr_e(
                        'The Backup archive is not encrypted, but running the installer requires a password.
                        File contents remain readable by anyone who gets them.',
                        'duplicator'
                    ); ?>">
                    <i class="fa-solid fa-lock warning-color"></i>
                    <?php esc_html_e('Installer password', 'duplicator') ?>
                </label>
                <input
                    type="radio"
                    name="secure-on"
                    class="dupli-security-mode-radio"
                    id="secure-on-arc-encrypt"
                    value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT; ?>"
                    <?php echo ($encryptAvaliable ? checked($secureOn, ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT, false) : ''); ?>
                    onclick="DupliJs.EnableInstallerPassword(true)"
                    <?php disabled(!$encryptAvaliable); ?>
                    data-parsley-multiple="secure-on-mltiple-error"
                >
                <label for="secure-on-arc-encrypt" class="button hollow secondary small"
                    data-tooltip-title="<?php esc_attr_e('Archive encryption', 'duplicator'); ?>"
                    data-tooltip="<?php esc_attr_e(
                        'The Backup archive is encrypted with your password: the most secure option,
                        with the highest level of protection for your data.',
                        'duplicator'
                    ); ?>">
                    <i class="fa-solid fa-lock success-color"></i>
                    <?php esc_html_e('Archive encryption', 'duplicator') ?>
                </label>
            </div>
            <div id="secure-on-parsely-error"></div>
        </div>
    </div>
    <div class="dup-form-item">
        <label class="lbl-larger" >
            <?php esc_html_e('Password', 'duplicator') ?>:
        </label>
        <div class="input">
            <span class="dup-password-toggle width-xlarge">
                <input
                    id="secure-pass"
                    type="password"
                    name="secure-pass"
                    required="required"
                    size="50"
                    maxlength="150"
                    value="<?php echo esc_attr($securePass); ?>"
                >
                <button type="button" >
                    <i class="fas fa-eye fa-sm"></i>
                </button>
            </span>
        </div>
    </div>

    <?php if (!$encryptAvaliable) { ?>
        <div class="dup-form-item">
            <span class="title">
                &nbsp;
            </span>
            <span class="input dup-tabs-opts-notice">
                <i class="fas fa-exclamation-triangle fa-xs alert-color"></i>
                <?php
                    echo esc_html__("The security mode 'Archive encryption' option above is currently disabled on this server.", 'duplicator') . ' '
                        . wp_kses_post($unavaliableMessage);
                ?>
            </span>
        </div>
    <?php } ?>

</div>

<script>
    (function($) {
        DupliJs.EnableInstallerPassword = function (focusPassword) {
            let $button = $('#secure-btn');
            let secureOnVal = $('.secure-on-input-wrapper input:checked').val();
            let $lockWrapper = $('#dupli-install-secure-lock-icon');
            let $lockIcon = $lockWrapper.find('i');

            // The lock is always visible: it swaps icon, color and tooltip with the selected mode
            $lockWrapper.show();
            $lockIcon.removeClass('fa-lock fa-lock-open primary-color success-color warning-color');

            switch (parseInt(secureOnVal, 10)) {
                case <?php echo (int) ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT; ?>:
                    $lockIcon.addClass('fa-lock success-color');
                    $lockWrapper.attr('data-tooltip', <?php echo json_encode(__('Archive encryption enabled', 'duplicator')); ?>);
                    break;
                case <?php echo (int) ArchiveDescriptor::SECURE_MODE_INST_PWD; ?>:
                    $lockIcon.addClass('fa-lock warning-color');
                    $lockWrapper.attr('data-tooltip', <?php echo json_encode(__('Installer password protection', 'duplicator')); ?>);
                    break;
                default:
                    $lockIcon.addClass('fa-lock-open warning-color');
                    $lockWrapper.attr('data-tooltip', <?php echo json_encode(__('No backup protection', 'duplicator')); ?>);
                    break;
            }

            if ($lockWrapper.length && $lockWrapper[0]._tippy) {
                DuplicatorTooltip.updateElementContent($lockWrapper, $lockWrapper.attr('data-tooltip'));
            }

            if (secureOnVal == <?php echo json_encode(ArchiveDescriptor::SECURE_MODE_NONE); ?>) {
                $('#secure-pass').removeAttr('required');
                $('#secure-pass').attr('readonly', true);
                $button.prop('disabled', true);
            } else {
                $('#secure-pass').attr('readonly', false);
                $('#secure-pass').attr('required', 'true');
                if (focusPassword) {
                    $('#secure-pass').focus();
                }
                $button.prop('disabled', false);
            }
        };
    })(jQuery);

    jQuery(function($) {
        $('#secure-on-none').parsley().on('field:error', function() {
            jQuery('html,body').animate({scrollTop: jQuery(".dupli-security-section").offset().top - 30}, 'slow');
        });
    });
</script>
