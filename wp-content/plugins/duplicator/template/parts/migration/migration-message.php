<?php



defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

if (!$tplMng->getDataValueBool('isMigrationSuccessNotice')) {
    $tplMng->render('parts/migration/tool-cleanup-installer-files');
    return;
}

$safeMsg              = $tplMng->getDataValueString('safeMsg');
$cleanupReport        = $tplMng->getDataValueArray('cleanupReport');
$isRestoreMode        = $tplMng->getDataValueBool('isRestoreMode');
$storedMigrationLists = $tplMng->getDataValueArray('storedMigrationLists');
$bottomMessageHtml    = $tplMng->getDataValueString('bottomMessageHtml');

?>
<div class="notice notice-success dupli-admin-notice dupli-admin-notice dup-migration-pass-wrapper">
    <div class="dup-migration-pass-title">
        <i class="fa fa-check-circle"></i> <?php
        if ($isRestoreMode) {
            esc_html_e('This site has been successfully restored!', 'duplicator');
        } else {
            esc_html_e('This site has been successfully migrated!', 'duplicator');
        }
        ?>
    </div>
    <p>
        <?php
        printf(
            esc_html__(
                'The following installation files are stored in the folder %s',
                'duplicator'
            ),
            '<b>' . esc_html(DUPLICATOR_SSDIR_PATH_INSTALLER) . '</b>'
        );
        ?>
    </p>
    <ul class="dup-stored-minstallation-files">
        <?php foreach ($storedMigrationLists as $label) { ?>
            <li>
                <?php echo esc_html($label); ?>
            </li>
        <?php } ?>
    </ul>

    <?php
    if ($tplMng->getDataValueBool('isInstallerCleanup')) {
        $tplMng->render('parts/migration/clean-installation-files');
    } else {
        if (count($cleanupReport['instFile']) > 0) { ?>
            <p>
                <?php esc_html_e('Security actions:', 'duplicator'); ?>
            </p>
            <ul class="dup-stored-minstallation-files">
                <?php
                foreach ($cleanupReport['instFile'] as $html) { ?>
                    <li>
                        <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
        <p>
            <b><?php esc_html_e('Final step:', 'duplicator'); ?></b><br>
            <span id="dupli-notice-action-remove-installer-files" class="link-style" onclick="DupliJs.Tools.removeInstallerFiles();">
                <?php esc_html_e('Remove Installation Files Now!', 'duplicator'); ?>
            </span>
        </p>
        <?php if (strlen($safeMsg) > 0) { ?>
            <div class="notice-safemode">
                <?php echo esc_html($safeMsg); ?>
            </div>
        <?php } ?>

        <p class="sub-note">
            <i><?php
                esc_html_e(
                    'Note: This message will be removed after all installer files are removed.
                    Installer files must be removed to maintain a secure site.
                    Click the link above to remove all installer files and complete the migration.',
                    'duplicator'
                );
                ?><br>
                <i class="fas fa-info-circle"></i>
                <?php
                esc_html_e(
                    'If an archive.zip/daf file was intentionally added to the root directory to perform an overwrite install
                    of this site then you can ignore this message.',
                    'duplicator'
                )
                ?>
            </i>
        </p>
        <?php
    }

    echo $bottomMessageHtml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
</div>
