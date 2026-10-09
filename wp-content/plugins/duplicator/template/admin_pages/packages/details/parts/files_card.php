<?php

/**
 * Backup details: Files card (archive engine, components, file filters)
 */

defined("ABSPATH") or die("");

use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);
$archive = $package->Archive;

$buildOptions = $package->getBuildOptions();
$engineParts  = $buildOptions !== null
    ? PackageUtils::getEngineTypeLabelParts($buildOptions->getArchiveEngine(), $buildOptions->getZipArchiveMode())
    : [
        'label' => __('Unknown', 'duplicator'),
        'mode'  => '',
    ];
$filtersOn    = $archive->FilterOn == 1;
$fileCount    = (int) $archive->FileCount;
$dirCount     = (int) $archive->DirCount;
$filterGroup  = 'admin_pages/packages/details/parts/filter_group';
$userDefined  = __('User Defined', 'duplicator');
$unreadable   = __('Unreadable', 'duplicator');
?>
<section class="dupli-backup-detail-card dupli-backup-detail-files">
    <header class="dupli-backup-detail-card-head">
        <h2 class="dupli-backup-detail-card-title"><?php esc_html_e('Files', 'duplicator'); ?></h2>
        <div class="dupli-card-head-meta">
            <span class="dupli-archive-format"><?php echo esc_html(strtoupper($archive->Format)); ?></span>
            <span class="dupli-kv-sep">|</span>
            <?php if ($package->isDBOnly()) { ?>
                <span><?php esc_html_e('DB Only', 'duplicator'); ?></span>
            <?php } else { ?>
                <span class="dupli-archive-file-count">
                    <?php echo esc_html(sprintf(_n('%s file', '%s files', $fileCount, 'duplicator'), number_format($fileCount))); ?>
                </span>
                <span class="dupli-kv-sep">|</span>
                <span class="dupli-archive-dir-count">
                    <?php echo esc_html(sprintf(_n('%s folder', '%s folders', $dirCount, 'duplicator'), number_format($dirCount))); ?>
                </span>
            <?php } ?>
        </div>
    </header>
    <div class="dupli-backup-detail-card-body">
        <div class="dupli-kv">
            <span class="dupli-kv-label"><?php esc_html_e('Engine', 'duplicator'); ?></span>
            <span class="dupli-kv-value dup-archive-engine dupli-kv-composite">
                <span><?php echo esc_html($engineParts['label']); ?></span>
                <?php if (strlen($engineParts['mode']) > 0) { ?>
                    <span class="dupli-kv-sep">|</span>
                    <span><?php echo esc_html($engineParts['mode']); ?></span>
                <?php } ?>
            </span>
        </div>
        <div class="dupli-kv">
            <span class="dupli-kv-label"><?php esc_html_e('Components', 'duplicator'); ?></span>
            <span class="dupli-kv-value dup-backup-components">
                <?php echo esc_html(BuildComponents::displayComponentsList($package->components)); ?>
            </span>
        </div>
        <div class="dupli-filters">
            <div class="dupli-kv">
                <span class="dupli-kv-label"><?php esc_html_e('Filters', 'duplicator'); ?></span>
                <span class="dupli-kv-value dup-file-filters">
                    <?php echo $filtersOn ? esc_html__('On', 'duplicator') : esc_html__('Off', 'duplicator'); ?>
                </span>
            </div>
            <?php if ($filtersOn) { ?>
                <div class="dupli-filters-sub">
                    <div class="dupli-kv">
                        <span class="dupli-kv-label">
                            <i class="far fa-folder-open fa-fw"></i>
                            <?php esc_html_e('Directories', 'duplicator'); ?>
                        </span>
                        <span class="dupli-kv-value sub-filter-data sub-filter-data-directories">
                            <?php
                            $tplMng->render($filterGroup, [
                                'title' => $userDefined,
                                'items' => $archive->FilterInfo->Dirs->Instance,
                            ]);
                            $tplMng->render($filterGroup, [
                                'title' => $unreadable,
                                'items' => $archive->FilterInfo->Dirs->Unreadable,
                            ]);
                            ?>
                        </span>
                    </div>
                    <div class="dupli-kv">
                        <span class="dupli-kv-label">
                            <i class="far fa-file fa-fw"></i>
                            <?php esc_html_e('Files', 'duplicator'); ?>
                        </span>
                        <span class="dupli-kv-value sub-filter-data sub-filter-data-files">
                            <?php
                            $tplMng->render($filterGroup, [
                                'title' => $userDefined,
                                'items' => $archive->FilterInfo->Files->Instance,
                            ]);
                            $tplMng->render($filterGroup, [
                                'title' => $unreadable,
                                'items' => $archive->FilterInfo->Files->Unreadable,
                            ]);
                            ?>
                        </span>
                    </div>
                    <div class="dupli-kv">
                        <span class="dupli-kv-label">
                            <i class="far fa-sticky-note fa-fw"></i>
                            <?php esc_html_e('Extensions', 'duplicator'); ?>
                        </span>
                        <span class="dupli-kv-value sub-filter-data sub-filter-data-extensions">
                            <?php if (count($archive->FilterExtsAll) > 0) { ?>
                                <ul class="dupli-list dupli-list-inline">
                                    <?php foreach ($archive->FilterExtsAll as $extension) { ?>
                                        <li><?php echo esc_html($extension); ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <span class="dupli-kv-empty"><?php esc_html_e('- no filters -', 'duplicator'); ?></span>
                            <?php } ?>
                        </span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
