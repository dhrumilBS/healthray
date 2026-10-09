<?php

/**
 * Backup details: Database card (dump engine, database name and size, table filters)
 */

defined("ABSPATH") or die("");

use Duplicator\Libs\Snap\SnapString;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package  = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$database = $package->Database;

$dbExcluded    = BuildComponents::isDBExcluded($package->components);
$buildModeInfo = PackageUtils::getDbBuildModeLabelParts(PackageUtils::getPackageDbBuildMode($package));
$filtersOn     = $database->FilterOn == 1;
$filterTables  = strlen($database->FilterTables) > 0 ? explode(',', $database->FilterTables) : [];
?>
<section class="dupli-backup-detail-card dupli-backup-detail-database">
    <header class="dupli-backup-detail-card-head">
        <h2 class="dupli-backup-detail-card-title"><?php esc_html_e('Database', 'duplicator'); ?></h2>
        <?php if (!$dbExcluded) { ?>
            <div class="dupli-card-head-meta">
                <?php
                printf(
                    esc_html_x('%1$s of %2$s tables', 'Example: 7 of 10 tables', 'duplicator'),
                    '<span class="dupli-db-tables-count">' . (int) $database->info->tablesFinalCount . '</span>',
                    '<span class="dupli-db-tables-total">' . (int) $database->info->tablesBaseCount . '</span>'
                );
                ?>
            </div>
        <?php } ?>
    </header>
    <div class="dupli-backup-detail-card-body">
        <?php if ($dbExcluded) { ?>
            <div class="dupli-empty-state">
                <i class="fas fa-database fa-2x"></i>
                <p><?php esc_html_e('The Database was excluded from the Backup.', 'duplicator'); ?></p>
            </div>
        <?php } else { ?>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Engine', 'duplicator'); ?></span>
                <span class="dupli-kv-value dupli-db-build-mode dupli-kv-composite">
                    <span><?php echo esc_html($buildModeInfo['label']); ?></span>
                    <?php if (strlen($buildModeInfo['mode']) > 0) { ?>
                        <span class="dupli-kv-sep">|</span>
                        <span><?php echo esc_html($buildModeInfo['mode']); ?></span>
                    <?php } ?>
                </span>
            </div>
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Database', 'duplicator'); ?></span>
                <span class="dupli-kv-value dupli-kv-composite">
                    <strong class="dup-db-name"><?php echo esc_html($database->info->name); ?></strong>
                    <span class="dupli-kv-sep">|</span>
                    <span class="dup-db-size"><?php echo esc_html(SnapString::byteSize($database->info->tablesSizeOnDisk)); ?></span>
                </span>
            </div>
            <div class="dupli-filters">
                <div class="dupli-kv">
                    <span class="dupli-kv-label"><?php esc_html_e('Filters', 'duplicator'); ?></span>
                    <span class="dupli-kv-value dup-db-filters">
                        <?php echo $filtersOn ? esc_html__('On', 'duplicator') : esc_html__('Off', 'duplicator'); ?>
                    </span>
                </div>
                <?php if ($filtersOn) { ?>
                    <div class="dupli-filters-sub">
                        <div class="dupli-kv">
                            <span class="dupli-kv-label">
                                <i class="fas fa-table fa-fw"></i>
                                <?php esc_html_e('Tables', 'duplicator'); ?>
                            </span>
                            <span class="dupli-kv-value sub-filter-data sub-filter-data-tables">
                                <?php
                                $tplMng->render('admin_pages/packages/details/parts/filter_group', [
                                    'title' => __('User Defined', 'duplicator'),
                                    'items' => array_map('trim', $filterTables),
                                ]);
                                ?>
                            </span>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</section>
