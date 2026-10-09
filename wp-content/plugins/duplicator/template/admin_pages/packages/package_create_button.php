<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Package\PackageUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

if ($tplMng->getDataValueBoolRequired('blur')) {
    return;
}

$autotuneUrl = ControllersManager::getMenuLink(ControllersManager::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_AUTOTUNE);

$disableCreate = PackageUtils::isBackupCreationBlocked();

if (CapMng::can(CapMng::CAP_CREATE, false)) {
    $tipContent = __(
        'Create a new backup. If a backup is currently running then this button will be disabled.',
        'duplicator'
    );
    ?>
    <span
        class="dup-new-package-wrapper"
        data-tooltip="<?php echo esc_attr($tipContent); ?>">
        <a
            href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>"
            id="dupli-create-new"
            class="button primary tiny margin-bottom-0 <?php echo $disableCreate ? 'disabled' : ''; ?>">
            <?php esc_html_e('Add New', 'duplicator'); ?>
        </a>
    </span>
    <?php
}
?>
<div class="dupli-backups-header-actions">
    <?php do_action('duplicator_backups_page_header_after'); ?>
    <?php if (CapMng::can(CapMng::CAP_SETTINGS, false)) { ?>
        <span>
            <a
                href="<?php echo esc_url($autotuneUrl); ?>"
                id="dupli-backups-autotune-link"
                class="button secondary hollow tiny margin-bottom-0">
                <i class="fa-solid fa-wand-magic-sparkles"></i>&nbsp;
                <?php esc_html_e('AutoTune', 'duplicator'); ?>
            </a>
        </span>
    <?php } ?>
</div>
