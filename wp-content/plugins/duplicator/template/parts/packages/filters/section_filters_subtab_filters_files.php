<?php

use Duplicator\Models\TemplateEntity;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$isTemplateEdit = $tplMng->getDataValueBool('isTemplateEdit');
$template       = $tplMng->getDataValueObjRequired('template', TemplateEntity::class);

$componentsParams = [
    'archiveFilterOn'         => $template->archive_filter_on,
    'archiveFilterDirs'       => $template->archive_filter_dirs,
    'archiveFilterFiles'      => $template->archive_filter_files,
    'archiveFilterExtensions' => $template->archive_filter_exts,
    'components'              => $template->components,
];
?>
<div class="filter-files-tab-content">
    <?php $tplMng->render('parts/packages/filters/package_components', $componentsParams); ?>
    <?php $tplMng->render('parts/packages/filters/section_filters_subtab_filters_db'); ?>
</div>