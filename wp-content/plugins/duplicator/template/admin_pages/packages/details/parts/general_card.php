<?php

/**
 * Backup details: General card (identity, runtime, type, security, storages, warnings, notes)
 */

defined("ABSPATH") or die("");

use Duplicator\Controllers\ActivityLogPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\CapMng;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Views\KsesHelper;
use Duplicator\Views\PackageScreen;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);

switch ($package->Installer->OptsSecureOn) {
    case ArchiveDescriptor::SECURE_MODE_NONE:
        $securityLabel     = __('No protection', 'duplicator');
        $securityIconClass = 'fa-lock-open dupli-kv-icon-warn';
        break;
    case ArchiveDescriptor::SECURE_MODE_INST_PWD:
        $securityLabel     = __('Installer password', 'duplicator');
        $securityIconClass = 'fa-lock dupli-kv-icon-warn';
        break;
    case ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT:
        $securityLabel     = __('Archive encryption', 'duplicator');
        $securityIconClass = 'fa-lock dupli-kv-icon-ok';
        break;
    default:
        throw new Exception('Invalid secure mode');
}

$logDownloadURL  = $package->getLocalPackageFileURL(AbstractPackage::FILE_TYPE_LOG);
$logFileExists   = file_exists($package->getSafeLogFilepath());
$canExport       = CapMng::can(CapMng::CAP_EXPORT, false) && $package->getStatus() != AbstractPackage::STATUS_ERROR;
$activityLogId   = $package->getMainActivityLogId();
$showActivityLog = $activityLogId > 0 && CapMng::can(CapMng::CAP_BASIC, false);

$tooltopCreatedContent = __(
    'Backup date and time expressed in UTC (Coordinated Universal Time).
    The displayed date corresponds to the server\'s international time, independent of local time zones.',
    'duplicator'
);
$unknownLabel          = __('- unknown -', 'duplicator');
$notAvailableLabel     = __('N/A', 'duplicator');
$toggleDetailsLabel    = __('Show details', 'duplicator');

// Total and per-phase durations from the package state times
$runtimeSeconds = $package->getPhaseDuration(AbstractPackage::PHASE_RUNTIME);
if ($runtimeSeconds >= 0) {
    $runtimeLabel = PackageUtils::getDurationLabel($runtimeSeconds);
} else {
    // Legacy Backups predate the per-status timers and only carry the build runtime string
    $runtimeLabel = $package->Runtime;
}
$phases         = [
    __('Scan', 'duplicator')     => PackageUtils::getDurationLabel($package->getPhaseDuration(AbstractPackage::PHASE_SCAN)),
    __('Database', 'duplicator') => PackageUtils::getDurationLabel($package->getPhaseDuration(AbstractPackage::PHASE_DATABASE)),
    __('Files', 'duplicator')    => PackageUtils::getDurationLabel($package->getPhaseDuration(AbstractPackage::PHASE_FILES)),
    __('Transfer', 'duplicator') => PackageUtils::getDurationLabel($package->getPhaseDuration(AbstractPackage::PHASE_TRANSFER)),
];
$scanPhaseLabel = array_key_first($phases);

$origin   = PackageScreen::getOriginGroup($package);
$storages = $package->getValidStorages();
$warnings = $package->hasBuildWarnings() ? $package->getBuildWarningsDisplayList() : [];
?>
<section class="dupli-backup-detail-card dupli-backup-detail-general">
    <header class="dupli-backup-detail-card-head">
        <h2 class="dupli-backup-detail-card-title"><?php esc_html_e('General', 'duplicator'); ?></h2>
        <div class="dupli-detail-actions">
            <?php if ($canExport) { ?>
                <?php $tplMng->render(
                    'admin_pages/packages/row_parts/download_buttons',
                    [
                        'menuClass'   => 'dupli-detail-dnload',
                        'buttonClass' => 'button hollow secondary small',
                        'showCaret'   => true,
                    ]
                ); ?>
            <?php } ?>
            <?php if ($logFileExists) { ?>
                <a class="button hollow secondary small dup-link-build-log" href="<?php echo esc_url($logDownloadURL); ?>" target="file_results">
                    <i class="fas fa-file-lines fa-sm"></i> <?php esc_html_e('Build Log', 'duplicator'); ?>
                </a>
            <?php } ?>
            <?php if ($showActivityLog) { ?>
                <a
                    class="button hollow secondary small dupli-link-activity-log"
                    href="<?php echo esc_url(ActivityLogPageController::getOpenLogUrl($activityLogId)); ?>">
                    <i class="fas fa-clock-rotate-left fa-sm"></i> <?php esc_html_e('Activity Log', 'duplicator'); ?>
                </a>
            <?php } ?>
            <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                <button class="button hollow alert small dupli-delete-backup" onclick="DupliJs.Pack.ConfirmDeleteCurrent();return false;">
                    <i class="fas fa-trash-alt fa-sm"></i> <?php esc_html_e('Delete', 'duplicator'); ?>
                </button>
            <?php } ?>
        </div>
    </header>
    <div id="dup-package-dtl-general-panel" class="dupli-backup-detail-card-body">
        <div class="dupli-general-grid">
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Name', 'duplicator'); ?></span>
                <span class="dupli-kv-value">
                    <?php echo esc_html($package->getName()); ?>
                    <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                        <button
                            type="button"
                            class="dupli-toggle-btn dupli-toggle-name"
                            aria-expanded="false"
                            aria-label="<?php echo esc_attr($toggleDetailsLabel); ?>">
                            <i class="fa-solid fa-chevron-down fa-xs"></i>
                        </button>
                        <div class="dup-link-data dup-link-data-name">
                            <b><?php esc_html_e('ID', 'duplicator'); ?>:</b> <?php echo absint($package->getId()); ?><br />
                            <b><?php esc_html_e('Hash', 'duplicator'); ?>:</b> <?php echo esc_html($package->getHash()); ?><br />
                            <b><?php esc_html_e('Archive', 'duplicator'); ?>:</b> <?php echo esc_html($package->getArchiveFilename()); ?><br />
                        </div>
                    <?php } ?>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label">
                    <?php esc_html_e('Created', 'duplicator'); ?>
                    <i
                        class="fa-solid fa-circle-info fa-fw"
                        data-tooltip-title="<?php esc_attr_e('Backup Date/Time', 'duplicator'); ?>"
                        data-tooltip="<?php echo esc_attr($tooltopCreatedContent); ?>"></i>
                </span>
                <span class="dupli-kv-value">
                    <?php if (strlen($package->getCreated()) > 0) { ?>
                        <?php echo esc_html(get_date_from_gmt($package->getCreated())); ?>
                        <button
                            type="button"
                            class="dupli-toggle-btn dupli-toggle-created"
                            aria-expanded="false"
                            aria-label="<?php echo esc_attr($toggleDetailsLabel); ?>">
                            <i class="fa-solid fa-chevron-down fa-xs"></i>
                        </button>
                        <div class="dup-link-data dup-link-data-created">
                            <b><?php esc_html_e('Age', 'duplicator'); ?>:</b>
                            <?php
                            printf(
                                /* translators: %s: human readable time interval, e.g. "3 days" */
                                esc_html__('%s ago', 'duplicator'),
                                esc_html($package->getPackageLife('human'))
                            );
                            ?>
                        </div>
                    <?php } else { ?>
                        <span class="dupli-kv-empty"><?php esc_html_e('- not set in this version -', 'duplicator'); ?></span>
                    <?php } ?>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Duplicator', 'duplicator'); ?></span>
                <span class="dupli-kv-value">
                    <?php echo esc_html($package->getVersion()); ?>
                    <button
                        type="button"
                        class="dupli-toggle-btn dupli-toggle-versions"
                        aria-expanded="false"
                        aria-label="<?php echo esc_attr($toggleDetailsLabel); ?>">
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div class="dup-link-data dup-link-data-versions">
                        <b><?php esc_html_e('WordPress', 'duplicator'); ?>:</b>
                        <?php echo esc_html(strlen($package->VersionWP) > 0 ? $package->VersionWP : $unknownLabel); ?><br />
                        <b><?php esc_html_e('PHP', 'duplicator'); ?>:</b>
                        <?php echo esc_html(strlen($package->VersionPHP) > 0 ? $package->VersionPHP : $unknownLabel); ?><br />
                        <b><?php esc_html_e('OS', 'duplicator'); ?>:</b>
                        <?php echo esc_html(strlen($package->VersionOS) > 0 ? $package->VersionOS : $unknownLabel); ?><br />
                        <b><?php esc_html_e('Mysql', 'duplicator'); ?>:</b>
                        <span class="dupli-kv-composite">
                            <span><?php echo esc_html(strlen($package->VersionDB) > 0 ? $package->VersionDB : $unknownLabel); ?></span>
                            <span class="dupli-kv-sep">|</span>
                            <span><?php echo esc_html(strlen($package->Database->Comments) > 0 ? $package->Database->Comments : $unknownLabel); ?></span>
                        </span><br />
                    </div>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Runtime', 'duplicator'); ?></span>
                <span class="dupli-kv-value">
                    <?php if (strlen($runtimeLabel) > 0) { ?>
                        <?php echo esc_html($runtimeLabel); ?>
                    <?php } else { ?>
                        <span class="dupli-kv-empty"><?php esc_html_e('error running', 'duplicator'); ?></span>
                    <?php } ?>
                    <button
                        type="button"
                        class="dupli-toggle-btn dupli-toggle-runtime"
                        aria-expanded="false"
                        aria-label="<?php esc_attr_e('Show phase timings', 'duplicator'); ?>">
                        <i class="fa-solid fa-chevron-down fa-xs"></i>
                    </button>
                    <div class="dup-link-data dup-link-data-runtime">
                        <?php foreach ($phases as $phaseLabel => $phaseDuration) { ?>
                            <b><?php echo esc_html($phaseLabel); ?>:</b>
                            <?php echo esc_html($phaseDuration); ?>
                            <?php if ($phaseLabel === $scanPhaseLabel) { ?>
                                <em class="dupli-kv-empty"><?php esc_html_e('(before build, not counted)', 'duplicator'); ?></em>
                            <?php } ?>
                            <br />
                        <?php } ?>
                    </div>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Type', 'duplicator'); ?></span>
                <span class="dupli-kv-value">
                    <i class="<?php echo esc_attr($origin['icon']); ?> fa-fw dupli-kv-icon"></i>
                    <?php echo wp_kses($origin['label'], KsesHelper::RICH_TAGS); ?>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Security', 'duplicator'); ?></span>
                <span class="dupli-kv-value dup-installer-security">
                    <i class="fa-solid <?php echo esc_attr($securityIconClass); ?> fa-fw dupli-kv-icon"></i>
                    <?php echo esc_html($securityLabel); ?>
                </span>
            </div>
        </div>
        <div class="dupli-kv dupli-general-storages">
            <span class="dupli-kv-label">
                <?php esc_html_e('Storages', 'duplicator'); ?>
                <span class="dupli-kv-count">(<?php echo count($storages); ?>)</span>
            </span>
            <span class="dupli-kv-value">
                <?php if (count($storages) === 0) { ?>
                    <span class="dupli-kv-empty"><?php esc_html_e('- No storage locations associated with this Backup -', 'duplicator'); ?></span>
                <?php } else { ?>
                    <ul class="dupli-list dupli-list-storages">
                        <?php foreach ($storages as $storage) { ?>
                            <li>
                                <span class="dupli-list-name">
                                    <?php echo wp_kses($storage->getSTypeIcon(), KsesHelper::getStorageIconAllowedTags()); ?>
                                    <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                                        <a href="<?php echo esc_url(StoragePageController::getEditUrl($storage)); ?>" target="_blank">
                                            <?php echo esc_html($storage->getName()); ?>
                                        </a>
                                    <?php } else { ?>
                                        <?php echo esc_html($storage->getName()); ?>
                                    <?php } ?>
                                </span>
                                <span class="dupli-list-meta"><?php echo wp_kses_post($storage->getLocationHtml()); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </span>
        </div>
        <?php
        // Shared upsell surface: sanitized with the storages-footer allow-list like every consumer of this filter
        $extraStoragesFooter = (string) apply_filters('duplicator_storages_table_footer_content', '');
        if ($extraStoragesFooter !== '') { ?>
            <div class="dupli-kv dupli-general-storages-footer">
                <?php echo wp_kses($extraStoragesFooter, KsesHelper::getFooterAllowedTags()); ?>
            </div>
        <?php } ?>
        <?php if (count($warnings) > 0) { ?>
            <div class="dupli-kv dupli-general-warnings">
                <span class="dupli-kv-label">
                    <i class="fa-solid fa-triangle-exclamation fa-fw warning-color"></i>
                    <?php esc_html_e('Warnings', 'duplicator'); ?>
                    <span class="dupli-kv-count">(<?php echo count($warnings); ?>)</span>
                </span>
                <span class="dupli-kv-value">
                    <ul class="dupli-list dupli-list-warning">
                        <?php foreach ($warnings as $warning) { ?>
                            <li>
                                <b><?php echo esc_html($warning['label']); ?>:</b>
                                <?php echo esc_html($warning['message']); ?>
                            </li>
                        <?php } ?>
                    </ul>
                </span>
            </div>
        <?php } ?>
        <div class="dupli-kv dupli-general-notes">
            <span class="dupli-kv-label"><?php esc_html_e('Notes', 'duplicator'); ?></span>
            <span class="dupli-kv-value">
                <?php if (strlen($package->notes) > 0) { ?>
                    <?php echo esc_html($package->notes); ?>
                <?php } else { ?>
                    <span class="dupli-kv-empty"><?php esc_html_e('- no notes -', 'duplicator'); ?></span>
                <?php } ?>
            </span>
        </div>
    </div>
</section>
