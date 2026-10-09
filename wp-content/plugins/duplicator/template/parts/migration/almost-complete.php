<?php

use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Controllers\ControllersManager;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$safeMsg           = $tplMng->getDataValueString('safeMsg');
$isRestoreMode     = $tplMng->getDataValueBool('isRestoreMode');
$bottomMessageHtml = $tplMng->getDataValueString('bottomMessageHtml');
$url               = $ctrlMng->getMenuLink(ControllersManager::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_GENERAL);

?>
<div class="notice notice-success dupli-admin-notice dupli-admin-notice dup-migration-pass-wrapper" >
    <p>
        <b><?php
        if ($isRestoreMode) {
            esc_html_e('Restore Backup Almost Complete!', 'duplicator');
        } else {
            esc_html_e('Migration Almost Complete!', 'duplicator');
        }
        ?></b>
    </p>
    <p>
        <?php
        printf(
            /* translators: %s: plugin name */
            esc_html__(
                'Reserved %s installation files have been detected in the root directory.
            Please delete these installation files to avoid security issues.',
                'duplicator'
            ),
            esc_html(DUPLICATOR____NAME)
        );
        ?>
        <br/>
        <?php esc_html_e('Go to: Tools > General > Data Cleanup and click the "Delete Installation Files" button', 'duplicator'); ?><br>
        <a id="dupli-notice-action-general-site-page" href="<?php echo esc_url($url); ?>">
            <?php esc_html_e('Take me there now!', 'duplicator'); ?>
        </a>
    </p>
    <?php if (strlen($safeMsg) > 0) { ?>
        <div class="notice-safemode">
            <?php echo esc_html($safeMsg); ?>
        </div>
    <?php } ?>
    <p class="sub-note">
        <i><?php
            esc_html_e(
                'If an archive.zip/daf file was intentionally added to the root directory
                to perform an overwrite install of this site then you can ignore this message.',
                'duplicator'
            );
            ?>
        </i>
    </p>

    <?php echo $bottomMessageHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
