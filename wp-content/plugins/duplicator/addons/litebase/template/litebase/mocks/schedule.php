<?php

use Duplicator\Addons\LiteBase\Utils\LiteBaseLinks;
use Duplicator\Core\Views\TplMng;

defined('ABSPATH') || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng $tplMng
 *
 * Mock body markup mirrors the real Pro Schedule page so the blurred
 * silhouette behind the upgrade popup looks identical. Content is static
 * and not localized: it is rendered inside .dup-mock-blur and is not read.
 */

$rows = $tplMng->getDataValueArrayRequired('rows');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <div class="dup-toolbar">
        <label for="bulk_action" class="screen-reader-text">Select bulk action</label>
        <select id="bulk_action" class="small">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="2">Activate</option>
            <option value="3">Deactivate</option>
            <option value="1">Delete</option>
        </select>
        <input type="button" class="button hollow secondary small action" value="Apply">
        <span class="separator"></span>
        <span class="button hollow secondary small">
            <i class="fas fa-sliders-h fa-fw" aria-hidden="true"></i>
        </span>
        <span class="button hollow secondary small">
            <i class="far fa-clone" aria-hidden="true"></i>
        </span>
    </div>

    <table class="widefat storage-tbl dup-table-list valign-top schedule-tbl">
        <thead>
            <tr>
                <th style="width:10px;"><input type="checkbox"></th>
                <th style="width:275px;">Name</th>
                <th class="dup-col-flags">Flags&nbsp;<i class="fa-solid fa-circle-info"></i></th>
                <th>Storage</th>
                <th>Runs Next</th>
                <th>Last Ran</th>
                <th>Active</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $index => $row) : ?>
                <tr class="schedule-row<?php echo ($index % 2 === 0) ? ' alternate' : ''; ?>">
                    <td><input type="checkbox" class="item-chk"></td>
                    <td>
                        <a href="#" class="name" onclick="return false;"><?php echo esc_html($row['name']); ?></a>
                    </td>
                    <td class="dup-col-flags">
                        <div class="dup-package-flags">
                            <span class="icon-wrapper"><i class="fa-solid fa-square-check"></i></span>
                            <span class="icon-wrapper"><i class="fa-solid fa-hard-drive"></i></span>
                            <span class="icon-wrapper"><i class="fa-solid fa-cloud"></i></span>
                            <span class="icon-wrapper"><i class="fa-solid fa-lock-open warning-color"></i></span>
                            <span class="icon-wrapper"><i class="fa-solid fa-house-fire dupli-recovery-flag-icon"></i></span>
                        </div>
                    </td>
                    <td><?php echo wp_kses($row['storage'], ['br' => []]); ?></td>
                    <td><?php echo esc_html($row['next']); ?></td>
                    <td><?php echo esc_html($row['last']); ?></td>
                    <td><b><span class="green">Yes</span></b></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" style="text-align:right; white-space: nowrap; font-size:12px">
                    Total: 12 | Active: 12 | Time: 00:00:01
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Automate your workflow with scheduled backups!', 'duplicator'),
    'warningText' => __('Duplicator Lite does not support scheduled backups!', 'duplicator'),
    'paragraphs'  => [
        __(
            'Scheduled Backups provide peace of mind and ensure that critical data can be quickly and easily 
            restored in the event of a disaster or loss. Duplicator Pro supports Hourly, Daily, Weekly and 
            Monthly scheduled backups.',
            'duplicator'
        ),
        __(
            'Supported Cloud Storage: Google Drive, Dropbox, Microsoft OneDrive, Amazon S3 
            (or any compatible S3 service), and FTP/SFTP Storage.',
            'duplicator'
        ),
    ],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Schedules'),
]);
