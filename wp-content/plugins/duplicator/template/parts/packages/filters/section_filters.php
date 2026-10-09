<?php

use Duplicator\Models\GlobalEntity;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Views\UI\UiViewState;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$isTemplateEdit = $tplMng->getDataValueBool('isTemplateEdit');
$template       = $tplMng->getDataValueObjRequired('template', TemplateEntity::class);

$archive_format = (GlobalEntity::getInstance()->getBuildMode() == PackageArchive::BUILD_MODE_DUP_ARCHIVE ? 'daf' : 'zip');

/**
 * Filter the Backup box sections, rendered sequentially inside the box.
 *
 * Each section entry: ['label' => string, 'class' => string, 'callback' => callable]
 * The callback renders the section content below its title.
 *
 * @var array<string, array{label: string, class: string, callback: callable}> $filterSections
 */
$filterSections = apply_filters('duplicator_package_setup_filter_sections', [
    'filters' => [
        'label'    => '',
        'class'    => 'filter-files-tab',
        'callback' => function () use ($tplMng): void {
            $tplMng->render('parts/packages/filters/section_filters_subtab_filters_files');
        },
    ],
]);
?>
<div class="dup-box dup-archive-filters-wrapper">
    <div class="dup-box-title">
        <i class="far fa-file-archive fa-sm"></i> <?php esc_html_e('Backup Filters', 'duplicator') ?>
        <?php if (!$isTemplateEdit) { ?>
            <sup class="dup-box-title-badge">
                <?php echo esc_html($archive_format); ?>
            </sup>
        <?php } ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text">
                <?php esc_html_e('Toggle panel:', 'duplicator') ?> <?php esc_html_e('Backup Filters', 'duplicator') ?>
            </span>
        </button>
    </div>

    <div id="dup-pack-archive-panel" class="dup-box-panel <?php echo (UiViewState::getValue('dup-pack-archive-panel') ? '' : 'no-display'); ?>">
        <?php foreach ($filterSections as $section) { ?>
            <div class="dupli-package-setup-section <?php echo esc_attr($section['class']); ?>">
                <?php if ($section['label'] !== '') { ?>
                    <div class="dup-package-hdr-1">
                        <?php echo esc_html($section['label']); ?>
                    </div>
                <?php } ?>
                <?php call_user_func($section['callback']); ?>
            </div>
        <?php } ?>
    </div>
</div>

<div class="duplicator-error-container"></div>
