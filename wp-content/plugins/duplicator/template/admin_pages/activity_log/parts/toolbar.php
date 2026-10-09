<?php

/**
 * Activity Log toolbar template (filters and bulk actions)
 */

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$page           = $tplMng->getDataValueIntRequired('page');
$totalItems     = $tplMng->getDataValueIntRequired('totalItems');
$totalPages     = $tplMng->getDataValueIntRequired('totalPages');
$filters        = $tplMng->getDataValueArrayRequired('filters');
$severityLevels = $tplMng->getDataValueArrayRequired('severityLevels');

$currentUrl   = self_admin_url('admin.php?page=' . $_REQUEST['page']);
$showAllTitle =  __(
    'When enabled, shows a detailed view with all sub-events expanded. When disabled, shows a compact view with only main events.',
    'duplicator'
);
?>
<div class="tablenav top">
    <form method="get" class="dup-activity-log-filters float-left" action="<?php echo esc_url($currentUrl); ?>">
        <div class="dup-toolbar">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>">
            <input type="checkbox"
                id="filter_show_all"
                name="filter_show_all"
                value="1"
                title="<?php echo esc_attr($showAllTitle); ?>"
                <?php checked(!empty($filters['show_all'])); ?>>
            <label for="filter_show_all">
                <?php esc_html_e('Detailed list', 'duplicator'); ?>
            </label>

            <div class="separator"></div>

            <input type="date"
                id="filter_date_from"
                name="filter_date_from"
                title="<?php esc_attr_e('Filter by date from', 'duplicator'); ?>"
                value="<?php echo esc_attr($filters['date_from']); ?>">

            <span> - </span>

            <input type="date"
                id="filter_date_to"
                name="filter_date_to"
                title="<?php esc_attr_e('Filter by date to', 'duplicator'); ?>"
                value="<?php echo esc_attr($filters['date_to']); ?>">

            <div class="separator"></div>

            <select id="filter_severity"
                name="filter_severity"
                title="<?php esc_attr_e('Filter by severity level', 'duplicator'); ?>">
                <option value="-1" <?php selected($filters['severity'], -1); ?>>
                    <?php esc_html_e('All Severities', 'duplicator'); ?>
                </option>
                <?php foreach ($severityLevels as $severityValue => $severityLabel) : ?>
                    <option value="<?php echo esc_attr($severityValue); ?>" <?php selected($filters['severity'], $severityValue); ?>>
                        <?php echo esc_html($severityLabel); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="separator"></div>

            <input type="submit"
                name="filter_action"
                class="button primary small"
                value="<?php esc_attr_e('Filter', 'duplicator'); ?>">

            <a href="<?php echo esc_url($currentUrl); ?>"
                class="button secondary small hollow">
                <?php esc_html_e('Reset', 'duplicator'); ?>
            </a>
        </div>
    </form>

    <?php if ($totalPages > 1) : ?>
        <div class="tablenav-pages">
            <span class="displaying-num">
                <?php echo esc_html(sprintf(__('%d items', 'duplicator'), $totalItems)); ?>
            </span>

            <span class="pagination-links">
                <?php if ($page > 1) : ?>
                    <a class="first-page button" href="<?php echo esc_url(add_query_arg('paged', 1)); ?>">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    <a class="prev-page button" href="<?php echo esc_url(add_query_arg('paged', $page - 1)); ?>">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>
                <?php endif; ?>

                <span class="paging-input">
                    <?php echo esc_html(sprintf(__('%1$d of %2$d', 'duplicator'), $page, $totalPages)); ?>
                </span>

                <?php if ($page < $totalPages) : ?>
                    <a class="next-page button" href="<?php echo esc_url(add_query_arg('paged', $page + 1)); ?>">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>
                    <a class="last-page button" href="<?php echo esc_url(add_query_arg('paged', $totalPages)); ?>">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                <?php endif; ?>
            </span>
        </div>
    <?php endif; ?>

    <br class="clear">
</div>
