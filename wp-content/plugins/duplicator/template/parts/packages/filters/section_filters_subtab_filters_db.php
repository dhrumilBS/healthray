<?php

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Database\DatabasePkg;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */
$isTemplateEdit = $tplMng->getDataValueBool('isTemplateEdit');
$template       = $tplMng->getDataValueObjRequired('template', TemplateEntity::class);

$tableList          = explode(',', $template->database_filter_tables);
$prefixFilterForced = DatabasePkg::isPrefixFilterForced();
$dbPrefixFilter     = $template->databasePrefixFilter || $prefixFilterForced;
$dbPrefixSubFilter  = $template->databasePrefixSubFilter;

global $wpdb;
$dbPrefix       = $wpdb->prefix;
$subsiteIds     = SnapWP::getSitesIds();
$tableItems     = [];
$tableListError = '';

try {
    $databaseTables = WpDbUtils::getTablesList();
} catch (DupliException $exception) {
    $databaseTables = [];
    $tableListError = $exception->getUserMessage();
}

foreach ($databaseTables as $table) {
    $info        = SnapWP::getTableInfoByName($table, $dbPrefix);
    $classes     = ['table-item'];
    $coreNote    = '';
    $cboxClasses = ['dup-pseudo-checkbox'];
    $checked     = in_array($table, $tableList);

    if ($info['isCore']) {
        $classes[] = 'core-table';
        $coreNote  = '*';
        if ($info['subsiteId'] > 0) {
            $classes[] = ' subcore-table-' . ($info['subsiteId'] % 2);
        }
    }

    if ($info['subsiteId'] > 1 && !in_array($info['subsiteId'], $subsiteIds)) {
        $classes[] = 'no-subsite-exists';
        if ($dbPrefixSubFilter) {
            $cboxClasses[] = 'disabled';
            $checked       = true;
        }
    }

    if ($info['havePrefix'] == false) {
        $classes[] = 'no-prefix-table';
        if ($dbPrefixFilter) {
            $cboxClasses[] = 'disabled';
            $checked       = true;
        }
    }

    if ($checked) {
        $cboxClasses[] = 'checked';
    }

    $tableItems[] = [
        'name'        => $table,
        'coreNote'    => $coreNote,
        'rowClasses'  => implode(' ', $classes),
        'cboxClasses' => implode(' ', $cboxClasses),
        'checked'     => $checked,
    ];
}

$tableListFilterParams = [
    'dbFilterOn'         => $template->database_filter_on,
    'dbPrefixFilter'     => $dbPrefixFilter,
    'dbPrefixSubFilter'  => $dbPrefixSubFilter,
    'dbPrefix'           => $dbPrefix,
    'isMultisite'        => is_multisite(),
    'tableItems'         => $tableItems,
    'prefixFilterForced' => $prefixFilterForced,
];

?>
<div class="filter-db-tab-content">
    <hr>
    <div class="db-only-message margin-bottom-1">
        <label class="lbl-larger">
            <?php esc_html_e("Database Only", 'duplicator') ?>
        </label>
        <div class="input">
            <?php
            esc_html_e(
                'This advanced option excludes all files from the archive. 
                Only the database and a copy of the installer.php will be included in the Backup file.',
                'duplicator'
            );
            ?><br>
            <?php
            esc_html_e(
                'The option can be used for backing up and moving only the database.',
                'duplicator'
            );
            ?><br>
            <?php
            printf(
                esc_html_x(
                    'When installing a database only backup please visit the %1$sdatabase only quick start%2$s',
                    '%1$s and %2$s are opening and closing anchor tags',
                    'duplicator'
                ),
                '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'database-install') . '" target="_blank">',
                '</a>'
            );
            ?>
        </div>
    </div>
    <div class="margin-bottom-1">
        <?php if ($tableListError !== '') : ?>
            <div class="notice notice-error inline">
                <p><?php echo esc_html($tableListError); ?></p>
            </div>
        <?php else : ?>
            <?php $tplMng->render('parts/packages/filters/tables_list_filter', $tableListFilterParams); ?>
        <?php endif; ?>
    </div>
    <?php $tplMng->render('parts/packages/filters/mysqldump_compatibility_mode'); ?>
</div>