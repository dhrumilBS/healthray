<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Package\PackageUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$lastBackupString = $tplMng->getDataValueStringRequired('lastBackupString');
$tooltipTitle     = esc_attr__('Backup creation', 'duplicator');
$tooltipContent   = esc_attr__(
    'This will create a new Backup. If a Backup is currently running then this button will be disabled.',
    'duplicator'
);
$disableCreate    = PackageUtils::isBackupCreationBlocked();
?>
<div class="dup-section-package-create dup-flex-content">
    <span>
        <?php esc_html_e('Last backup:', 'duplicator'); ?>
        <span class="dup-last-backup-info">
            <?php
            echo wp_kses(
                $lastBackupString,
                [
                    'b'    => [],
                    'span' => [
                        'class' => [],
                    ],
                ]
            );
            ?>
        </span>
    </span>
    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
        <span
            class="dup-new-package-wrapper"
            data-tooltip-title="<?php echo esc_attr($tooltipTitle); ?>"
            data-tooltip="<?php echo esc_attr($tooltipContent); ?>">
            <a
                id="dupli-create-new"
                class="button button-primary <?php echo $disableCreate ? 'disabled' : ''; ?>"
                href="<?php echo esc_url(PackagesPageController::getInstance()->getPackageBuildS1Url()); ?>">
                <?php esc_html_e('Create New', 'duplicator'); ?>
            </a>
        </span>
    <?php } ?>
</div>