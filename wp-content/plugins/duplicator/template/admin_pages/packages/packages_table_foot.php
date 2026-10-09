<?php

/**
 * Duplicator Backup row in table Backups list
 */

use Duplicator\Views\KsesHelper;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$totalElements      = $tplMng->getDataValueIntRequired('totalElements');
$latestCreated      = $tplMng->getDataValueString('lastBackupCreated');
$maxDefaultPackages = $tplMng->getDataValueInt('defaultStorageMaxPackages');
$retentionTooltip   = esc_attr__(
    'Max number of backups kept for the default storage. Edit in Storage > Default > Max Backups.',
    'duplicator'
);
?>
<tfoot>
    <?php
    $extraFooterContent = (string) apply_filters('duplicator_packages_table_footer_content', '');
    if ($extraFooterContent !== '') :
        ?>
        <tr class="dup-table-footer-extra">
            <td colspan="11"><?php echo wp_kses($extraFooterContent, KsesHelper::getFooterAllowedTags()); ?></td>
        </tr>
    <?php endif; ?>
    <tr>
        <th colspan="11">
            <div class="dup-pack-status-info">
                <span class="float-left">
                    <?php
                    printf(
                        /* translators: %d: total number of backups */
                        esc_html(_n('Total: %d backup', 'Total: %d backups', $totalElements, 'duplicator')),
                        (int) $totalElements
                    );
                    echo ' | ';
                    if ($maxDefaultPackages === 0) {
                        esc_html_e('Default storage: unlimited', 'duplicator');
                    } else {
                        printf(
                            /* translators: %d: max number of backups kept by the default storage */
                            esc_html__('Default storage: keep last %d', 'duplicator'),
                            (int) $maxDefaultPackages
                        );
                    }
                    ?>
                    <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                       data-tooltip-title="<?php esc_attr_e('Default storage retention', 'duplicator'); ?>"
                       data-tooltip="<?php echo esc_attr($retentionTooltip); ?>"></i>
                </span>
                <?php if ($latestCreated !== '') :
                    $timestamp = strtotime($latestCreated);
                    if ($timestamp > 0) :
                        ?>
                        <span class="float-right">
                            <?php
                            printf(
                                /* translators: %s: human-readable time ago (e.g. "5 minutes") */
                                esc_html__('Last backup: %s ago', 'duplicator'),
                                esc_html(human_time_diff($timestamp))
                            );
                            ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </th>
    </tr>
</tfoot>