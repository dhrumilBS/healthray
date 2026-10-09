<?php

use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$fileRemoved   = $tplMng->getDataValueArrayRequired('installerCleanupFiles');
$removeError   = $tplMng->getDataValueBool('installerCleanupError');
$purgeCaches   = $tplMng->getDataValueArray('installerCleanupPurge');
$manualNotices = $tplMng->getDataValueArray('installerManualNotices');
?>
<div class="dupli-diagnostic-action-installer">
    <p>
        <b><?php esc_html_e('Installation cleanup ran!', 'duplicator'); ?></b>
    </p>
    <?php
    if (count($fileRemoved) === 0) {
        ?>
        <p>
            <b><?php esc_html_e('No Duplicator files were found on this WordPress Site.', 'duplicator'); ?></b>
        </p> <?php
    } else {
        foreach ($fileRemoved as $path => $success) {
            if ($success) {
                ?><div class="success">
                    <i class="fa fa-check"></i> <?php esc_html_e("Removed", 'duplicator'); ?> - <?php echo esc_html($path); ?>
                </div><?php
            } else {
                ?><div class="failed">
                    <i class='fa fa-exclamation-triangle'></i> <?php esc_html_e("Found", 'duplicator'); ?> - <?php echo esc_html($path); ?>
                </div>
                <?php
            }
        }
    }

    foreach ($purgeCaches as $message) {
        ?><div class="success">
            <i class="fa fa-check"></i> <?php echo wp_kses($message, KsesHelper::GEN_TAGS); ?>
        </div>
    <?php } ?>

    <div class="dupli-manual-purge-notice">
        <p class="dupli-manual-purge-notice__title">
            <i class="fa fa-exclamation-triangle"></i>&nbsp;
            <b><?php esc_html_e('Notice broken images or old-domain links after the install?', 'duplicator'); ?></b>
        </p>
        <div>
            <?php
            esc_html_e(
                'If, after the installation, you see broken images, missing styles, or links pointing to the old domain,
                it is likely that a third-party plugin or theme is caching assets that still reference the old URL.',
                'duplicator'
            );
            ?><br>
            <?php
            esc_html_e(
                'Open the tool responsible for that cache and trigger its cache regeneration from its own settings page.',
                'duplicator'
            );
            ?>
        </div>
        <?php if (count($manualNotices) > 0) { ?>
            <div class="margin-top-1" >
                    <?php
                    esc_html_e(
                        'The following tools have been detected on this site and may keep their own cache.
                        If something looks wrong, reset their cache from their own settings page.',
                        'duplicator'
                    );
                    ?><br>
                <b>
                    <?php
                    esc_html_e(
                        'Check each tool\'s official documentation for the exact steps:',
                        'duplicator'
                    );
                    ?>
                </b>
            </div>
            <ul class="dupli-manual-purge-notice__list">
                <?php foreach ($manualNotices as $notice) { ?>
                    <li>
                        <b><?php echo esc_html($notice['name']); ?>:</b>
                        <?php echo esc_html($notice['description']); ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>
    <?php

    if ($removeError) {
        ?>
        <p>
        <?php esc_html_e('Some of the installer files did not get removed, ', 'duplicator'); ?>
            <span class="link-style" onclick="DupliJs.Tools.removeInstallerFiles();">
        <?php esc_html_e('please retry the installer cleanup process', 'duplicator'); ?>
            </span><br>
        <?php esc_html_e(' If this process continues please see the previous FAQ link.', 'duplicator'); ?>
        </p>
        <?php
    }
    ?>
    <div style="font-style: italic; max-width:900px; padding:10px 0 25px 0;">
        <p>
            <b><i class="fa fa-shield-alt"></i> <?php esc_html_e('Security Notes', 'duplicator'); ?>:</b>
            <?php
            esc_html_e(
                'If the installer files do not successfully get removed with this action, 
                then they WILL need to be removed manually through your host\'s control panel or FTP. 
                Please remove all installer files to avoid any security issues on this site.',
                'duplicator'
            );
            ?><br>
            <?php
            printf(
                esc_html_x(
                    'For more details please visit the FAQ link %1$sWhich files need to be removed after an install?%2$s',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'duplicator'
                ),
                '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'which-files-need-to-be-removed-after-an-install') . '" target="_blank">',
                '</a>'
            );
            ?>
        </p>
        <p>
            <b><i class="fa fa-thumbs-up"></i> <?php esc_html_e('Help Support Duplicator', 'duplicator'); ?>:</b>
            <?php
            esc_html_e(
                'The Duplicator team has worked many years to make moving a WordPress site a much easier process. ',
                'duplicator'
            );
            ?>
            <br>
            <?php
            printf(
                esc_html_x(
                    'Show your support with a %1$s5 star review%2$s! We would be thrilled if you could!',
                    '%1$s and %2$s represents the opening and closing HTML tags for an anchor or link',
                    'duplicator'
                ),
                '<a href="https://wordpress.org/support/plugin/duplicator/reviews/?filter=5" target="_blank">',
                '</a>'
            );
            ?>
        </p>
    </div>
</div>