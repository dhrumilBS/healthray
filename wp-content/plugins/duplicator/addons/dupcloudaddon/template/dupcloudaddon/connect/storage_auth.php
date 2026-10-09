<?php

/**
 * Template for Duplicator Cloud Connect Step 1
 */

use Duplicator\Addons\DupCloudAddon\Models\DupCloudStorage;
use Duplicator\Addons\DupCloudAddon\Utils\DupCloudClient;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Views\UI\UiDialog;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */
$storage  = $tplMng->getDataValueObjRequired('storage', DupCloudStorage::class);
$errorMsg = '';

if (!$storage->isAuthorized()) : ?>
    <div class='dupcloud-authorization-state' id="dupcloud-state-unauthorized">
        <div class="margin-bottom-1">
            <p><strong><?php esc_html_e('New to Duplicator Cloud?', 'duplicator'); ?></strong></p>
            <a
                id="dupli-dupcloud-learn-more"
                class="button primary margin-bottom-0"
                href="<?php echo esc_url(DupCloudClient::getLandingUrl()); ?>"
                target="_blank"
                rel="noopener noreferrer"
            >
                <?php esc_html_e('Explore plans and features', 'duplicator'); ?>
            </a>
        </div>
        <p><?php esc_html_e('Already have Duplicator Cloud? Connect your storage below.', 'duplicator'); ?></p>
        <?php
        /**
         * Action to inject additional connect buttons before the token connection.
         *
         * @param DupCloudStorage $storage The cloud storage instance
         */
        do_action('duplicator_dupcloud_connect_buttons', $storage);
        ?>
        <button
            id="dupli-dupcloud-connect-btn"
            type="button"
            class="button secondary hollow margin-bottom-0"
            onclick="DupliJs.Storage.DupCloud.ShowTokenInput();">
            <i class="fa fa-plug"></i> <?php esc_html_e('Connect Duplicator Cloud Token', 'duplicator'); ?>
        </button>

        <div id="dupli-dupcloud-token-area" style="display:none;">
            <div class="storage-auth-step">
                <p>
                    <b><?php esc_html_e('Step 1:', 'duplicator'); ?></b>&nbsp;
                    <?php esc_html_e('Get your authentication token from Duplicator.com', 'duplicator'); ?>
                </p>
                <button
                    type="button"
                    class="button secondary hollow margin-bottom-0"
                    onclick="window.open('<?php echo esc_js(DupCloudClient::getManageLicenseStorageUrl()); ?>', '_blank');">
                    <i class="fa fa-external-link"></i> <?php esc_html_e('Get Connection Token', 'duplicator'); ?>
                </button>
            </div>

            <div class="storage-auth-step">
                <p>
                    <b><?php esc_html_e('Step 2:', 'duplicator'); ?></b>&nbsp;
                    <?php esc_html_e('Paste your authentication token below:', 'duplicator'); ?>
                </p>
                <input id="dupcloud-compound-token" name="dupcloud-compound-token" style="width: 500px" type="text">
            </div>

            <div class="storage-auth-step">
                <button
                    id="dupcloud-finalize-setup"
                    type="button"
                    class="button secondary margin-bottom-0">
                    <i class="fa fa-check-square"></i> <?php esc_html_e('Finalize Setup', 'duplicator'); ?>
                </button>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class='dupcloud-authorization-state' id="dupcloud-state-authorized" style="margin-top:-10px">
        <?php if (strlen($storage->getUserName()) > 0) : ?>
            <h3>
                <?php esc_html_e('Duplicator Cloud Account', 'duplicator'); ?><br />
                <i class="dupli-edit-info">
                    <?php esc_html_e('Duplicator has been authorized to access this user\'s Duplicator Cloud account', 'duplicator'); ?>
                </i>
            </h3>
            <?php if (!$storage->isValid($errorMsg, true)) : ?>
                <div class="alert-color margin-bottom-1">
                    <p><b><?php esc_html_e('The storage is currently not in a valid state.', 'duplicator'); ?></b></p>
                    <p><b><?php esc_html_e('Error:', 'duplicator'); ?></b> <?php echo esc_html($errorMsg); ?></p>
                </div>
            <?php endif; ?>
            <div id="dupcloud-account-info">
                <label><?php esc_html_e('Name', 'duplicator'); ?>:</label>
                <?php echo esc_html($storage->getUserName()); ?><br />

                <label><?php esc_html_e('Email', 'duplicator'); ?>:</label> <?php echo esc_html($storage->getUserEmail()); ?><br />
                <label><?php esc_html_e('Space', 'duplicator'); ?>:</label>
            <?php if ($storage->getFreeSpace() > 0) : ?>
                <?php printf(
                    '%1$s of %2$s is is used',
                    esc_html(SnapString::byteSize($storage->getUsedSpace())),
                    esc_html(SnapString::byteSize($storage->getTotalSpace()))
                ); ?>
            <?php else : ?>
                <b class="alert-color">
                    <?php printf(
                        '%1$s of %2$s is used',
                        esc_html(SnapString::byteSize($storage->getUsedSpace())),
                        esc_html(SnapString::byteSize($storage->getTotalSpace()))
                    ); ?>
                </b>
                <br />
                <br />
                <b class="alert-color"><?php esc_html_e('Warning! Storage is full and cannot be used.', 'duplicator'); ?></b>
            <?php endif ?>
            </div><br />
        <?php else : ?>
            <div><?php esc_html_e('Error retrieving user information.', 'duplicator'); ?></div>
        <?php endif ?>
        <a href="<?php echo esc_url($storage->getBackupsUrl()); ?>" target="_blank"
            id="dup-dupcloud-manage-website"
            class="button margin-right-1 button-primary"
            target="_blank"
        >
            <?php esc_html_e('Manage Backups', 'duplicator'); ?>
        </a>
        <button
            id="dup-dupcloud-cancel-authorization"
            type="button"
            class="button gray hollow">
            <?php esc_html_e('Cancel Authorization', 'duplicator'); ?>
        </button><br />
        <i class="dupli-edit-info">
            <?php
            esc_html_e(
                'Disassociation of storage provider will require re-authorization.',
                'duplicator'
            ); ?>
        </i>
    </div>
<?php endif;


$alertConnStatus          = new UiDialog();
$alertConnStatus->title   = __('Duplicator Cloud Authorization Error', 'duplicator');
$alertConnStatus->message = ''; // javascript inserted message
$alertConnStatus->initAlert();
?>
<script>
    jQuery(document).ready(function($) {
        DupliJs.Storage.DupCloud = DupliJs.Storage.DupCloud || {};

        DupliJs.Storage.DupCloud.ShowTokenInput = function() {
            $('#dupli-dupcloud-connect-btn-area').hide();
            $('#dupli-dupcloud-token-area').show();
        }

        $('#dup-dupcloud-manage-website').click(function(e) {
            e.stopPropagation();
            window.open('<?php echo esc_js(DupCloudClient::manageWebsitesUrl()); ?>', '_blank');
            return false;
        });

        $('#dup-dupcloud-cancel-authorization').click(function(e) {
            e.stopPropagation();
            DupliJs.Storage.RevokeAuth(<?php echo (int) $storage->getId(); ?>);
            return false;
        });

        $('#dupcloud-finalize-setup').click(function(event) {
            event.stopPropagation();

            var compoundToken = $('#dupcloud-compound-token').val().trim();

            if (compoundToken.length > 0) {
                // Validate token format (should contain a dot)
                if (compoundToken.indexOf('.') === -1) {
                    <?php $alertConnStatus->showAlert(); ?>
                    let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " +
                        "<?php esc_html_e('Invalid token format. Please ensure you copied the complete authentication token.', 'duplicator'); ?>";
                    <?php $alertConnStatus->updateMessage("alertMsg"); ?>
                    return false;
                }

                DupliJs.Storage.PrepareForSubmit();

                DupliJs.Storage.Authorize(
                    <?php echo (int) $storage->getId(); ?>,
                    <?php echo (int) $storage->getSType(); ?>, {
                        'name': $('#name').val(),
                        'notes': $('#notes').val(),
                        'access_token': compoundToken
                    }
                );
            } else {
                <?php $alertConnStatus->showAlert(); ?>
                let alertMsg = "<i class='fas fa-exclamation-triangle'></i> " +
                    "<?php esc_html_e('Please paste your authentication token!', 'duplicator'); ?>";
                <?php $alertConnStatus->updateMessage("alertMsg"); ?>
            }

            return false;
        });
    });
</script>
