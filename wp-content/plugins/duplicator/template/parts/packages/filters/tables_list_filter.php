<?php

/**
 * Duplicator Backup row in table Backups list
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$dbFilterOn         = $tplMng->getDataValueBool('dbFilterOn');
$dbPrefixFilter     = $tplMng->getDataValueBool('dbPrefixFilter');
$dbPrefixSubFilter  = $tplMng->getDataValueBool('dbPrefixSubFilter');
$dbPrefix           = $tplMng->getDataValueString('dbPrefix');
$isMultisite        = $tplMng->getDataValueBool('isMultisite');
$tableItems         = $tplMng->getDataValueArray('tableItems');
$prefixFilterForced = $tplMng->getDataValueBool('prefixFilterForced');

$toolTipPrefixFilterContent = sprintf(
    __(
        'By enabling this option all tables that do not start with the prefix <b>"%s"</b> are excluded from the Backup.',
        'duplicator'
    ),
    esc_html($dbPrefix)
) . ' ' .
    __(
        'This option is useful for multiple WordPress installs in the same database or if several applications are installed in the same database.',
        'duplicator'
    );

$toolTipSubsiteFilterContent =
    __('Enabling this option excludes all tables associated with deleted sites from the Backup.', 'duplicator') . '<br><br>' .
    __(
        'When deleting a site in a multisite, WordPress deletes the tables of items 
        related to the core, however it is not assumed that the tables of third party plugins are removed.',
        'duplicator'
    ) . ' ' .
    __('With a multisite with a large number of deleted sites the database may be full of unused tables.', 'duplicator') . ' ' .
    __('With this option only the tables of currently existing sites will be included in the backup.', 'duplicator');

$toolTipTablesFilters = __(
    "Checked tables will be <b>excluded</b> from the database script.
    Excluding certain tables can cause your site or plugins to not work correctly after install!",
    'duplicator'
) . '<br><br>' .
    __(
        "Use caution when excluding tables! It is highly recommended to not exclude WordPress core tables in red with an *,
    unless you know the impact.",
        'duplicator'
    );
?>
<label class="lbl-larger">
    <?php esc_html_e('Database Filters:', 'duplicator'); ?>
    <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
        title="<?php esc_attr_e('Database filters allow you to exclude tables from the backup.', 'duplicator'); ?>"
        aria-expanded="false"></i>
</label>
<div class="margin-bottom-1">
    <label>
        <input
            type="checkbox"
            id="dbfilter-on"
            name="dbfilter-on" <?php checked($dbFilterOn); ?>
            class="margin-0">&nbsp;<?php esc_html_e('Enable', 'duplicator'); ?>
    </label>
</div>

<div class="db-filter-section">
    <label class="lbl-larger">
        <?php esc_html_e("Table Prefixes:", 'duplicator') ?>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            title="<?php echo esc_attr($toolTipPrefixFilterContent); ?>"
            aria-expanded="false"></i>
    </label>
    <div class="margin-bottom-1">
        <label>
            <input
                type="checkbox"
                id="db-prefix-filter"
                name="db-prefix-filter"
                class="margin-0"
                <?php checked($dbPrefixFilter); ?>
                <?php disabled(!$dbFilterOn || $prefixFilterForced); ?>
                <?php
                // data-force-checked guards the JS in ToggleDBFiltersRedIcon below and in
                // the TemplateAddon selector against re-enabling/unchecking a forced filter
                echo $prefixFilterForced ? 'data-force-checked="1"' : '';
                ?>
                data-prefix-value="<?php echo esc_attr($dbPrefix); ?>" />
            <?php
            esc_html_e('Filter tables without current WordPress prefix', 'duplicator');
            echo $dbPrefix !== '' ?  '&nbsp;<i>(' . esc_html($dbPrefix) . ')</i>&nbsp;' : '';
            if ($prefixFilterForced) {
                echo '<i>&mdash;&nbsp;' . esc_html__('always enabled on this site', 'duplicator') . '</i>';
            }
            ?>
        </label>
    </div>


    <?php if ($isMultisite) { ?>
        <label class="lbl-larger">
            <?php esc_html_e("Subsites:", 'duplicator') ?>
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e("Multisite-Subsite Filters", 'duplicator'); ?>"
                data-tooltip="<?php echo esc_attr($toolTipSubsiteFilterContent); ?>">
            </i>
        </label>
        <div class="margin-bottom-1">
            <label>
                <input
                    type="checkbox"
                    id="db-prefix-sub-filter"
                    name="db-prefix-sub-filter"
                    class="margin-0"
                    <?php checked($dbPrefixSubFilter); ?>
                    <?php disabled(!$dbFilterOn); ?>>
                <?php esc_html_e("Filter/Hide Tables of Deleted Multisite-Subsites", 'duplicator') ?>
            </label>
        </div>
    <?php } ?>

    <label class="lbl-larger">
        <?php esc_html_e("Exclude Tables:", 'duplicator') ?>
        <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
            data-tooltip="<?php echo esc_attr($toolTipTablesFilters); ?>">
        </i>
    </label>
    <div id="dup-db-filter-items">
        <div class="dup-db-filter-buttons">
            <span id="dbnone" class="link-style gray dup-db-filter-none">
                <i class="fa-regular fa-square-minus fa-lg" title="<?php esc_html_e('Unfilter All Tables', 'duplicator'); ?>"></i>
            </span>&nbsp;
            <span id="dball" class="link-style gray dup-db-filter-all">
                <i class="fa-regular fa-square-check fa-lg" title="<?php esc_html_e('Filter All Tables', 'duplicator'); ?>"></i>
            </span>
        </div>
        <div id="dup-db-tables-exclude-wrapper">
            <div id="dup-db-tables-exclude">
                <input type="hidden" id="dup-db-tables-lists" name="dbtables-list" value="">
                <?php foreach ($tableItems as $tableItem) { ?>
                    <label
                        class="<?php echo esc_attr($tableItem['rowClasses']); ?>"
                        title="<?php echo esc_attr($tableItem['name'] . $tableItem['coreNote']); ?>">
                        <span
                            class="<?php echo esc_attr($tableItem['cboxClasses']); ?>"
                            aria-checked="<?php echo $tableItem['checked'] ? "true" : "false"; ?>"
                            role="checkbox"
                            data-value="<?php echo esc_attr($tableItem['name']); ?>">
                        </span>
                        &nbsp;<span><?php echo esc_html($tableItem['name'] . $tableItem['coreNote']); ?></span>
                    </label>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
    (function($) {
        /* METHOD: Toggle Database table filter red icon */
        DupliJs.Pack.ToggleDBFiltersRedIcon = function() {
            if (
                $("#dbfilter-on").is(':checked')
            ) {
                $('#dup-archive-filter-db-icon').removeClass('no-display');
                if (!$('#db-prefix-filter').data('force-checked')) {
                    $('#db-prefix-filter').prop('disabled', false);
                }
                $('#db-prefix-sub-filter').prop('disabled', false);
            } else {
                $('#dup-archive-filter-db-icon').addClass('no-display');
                $('#db-prefix-filter').prop('disabled', true);
                $('#db-prefix-sub-filter').prop('disabled', true);
            }
        }

        DupliJs.Pack.ToggleDBFilters = function() {
            var filterItems = $('#dup-db-filter-items');

            if (
                $("#dbfilter-on").is(':checked')
            ) {
                $('.db-filter-section').removeClass('no-display');
                filterItems.removeClass('disabled');
                $('#dup-db-filter-items-no-filters').hide();
            } else {
                $('.db-filter-section').addClass('no-display');
                filterItems.addClass('disabled');
                $('#dup-db-filter-items-no-filters').show();
            }

            DupliJs.Pack.ToggleDBFiltersRedIcon();
        };

        DupliJs.Pack.FillExcludeTablesList = function() {
            let values = $("#dup-db-tables-exclude .dup-pseudo-checkbox.checked")
                .map(function() {
                    return this.getAttribute('data-value');
                })
                .get()
                .join();

            $('#dup-db-tables-lists').val(values);
        };

        DupliJs.Pack.ToggleNoPrefixTables = function(removeCheckOnEnable = true) {
            let checkNode = $('#db-prefix-filter');
            let display = !checkNode.is(":checked");

            $("#dup-db-tables-exclude .no-prefix-table").each(function() {
                let checkBox = $(this).find(".dup-pseudo-checkbox").first();
                if (display) {
                    checkBox.removeClass('disabled');
                    if (removeCheckOnEnable) {
                        checkBox.removeClass("checked");
                    }
                } else {
                    checkBox
                        .addClass('disabled')
                        .addClass("checked");
                }
            });

            DupliJs.Pack.ToggleDBFiltersRedIcon();
        }

        DupliJs.Pack.ToggleNoSubsiteExistsTables = function(removeCheckOnEnable = true) {
            let checkNode = $('#db-prefix-sub-filter');
            let display = !checkNode.is(":checked");

            $("#dup-db-tables-exclude .no-subsite-exists").each(function() {
                let checkBox = $(this).find(".dup-pseudo-checkbox").first();
                if (display) {
                    checkBox.removeClass('disabled');
                    if (removeCheckOnEnable) {
                        checkBox.removeClass("checked");
                    }
                } else {
                    checkBox
                        .addClass('disabled')
                        .addClass("checked");
                }
            });

            DupliJs.Pack.ToggleDBFiltersRedIcon();
        }
    })(jQuery);

    jQuery(document).ready(function($) {
        let tablesToExclude = $("#dup-db-tables-exclude");

        $('.dup-db-filter-none').click(function() {
            tablesToExclude.find(".dup-pseudo-checkbox.checked").removeClass("checked");
        });

        $('.dup-db-filter-all').click(function() {
            tablesToExclude.find(".dup-pseudo-checkbox:not(.checked)").addClass("checked");
        });

        $('#db-prefix-sub-filter').change(DupliJs.Pack.ToggleNoSubsiteExistsTables);
        $('#db-prefix-filter').change(DupliJs.Pack.ToggleNoPrefixTables);
        $('#dbfilter-on').change(DupliJs.Pack.ToggleDBFilters);
        DupliJs.Pack.ToggleDBFilters();
    });
</script>