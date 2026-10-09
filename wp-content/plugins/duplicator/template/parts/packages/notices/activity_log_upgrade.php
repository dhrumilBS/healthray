<?php

/**
 * Activity Log integration upgrade notice template
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$count          = $tplMng->getDataValueIntRequired('count');
$activityLogUrl = $tplMng->getDataValueStringRequired('activityLogUrl');
?>
<div>
    <p><b><?php esc_html_e('Activity Log Integration Update', 'duplicator'); ?></b></p>
    <p>
        <?php esc_html_e('Failed backups are no longer shown in the main backup list.', 'duplicator'); ?>
        <?php
        echo esc_html(
            sprintf(
                _n(
                    '%d failed backup has been moved to the Activity Log.',
                    '%d failed backups have been moved to the Activity Log.',
                    $count,
                    'duplicator'
                ),
                $count
            )
        );
        ?>
        <?php
        printf(
            esc_html__(
                'You can view them in the %1$sActivity Log%2$s.',
                'duplicator'
            ),
            '<a href="' . esc_url($activityLogUrl) . '">',
            '</a>'
        );
        ?>
    </p>
</div>
