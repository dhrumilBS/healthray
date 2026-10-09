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
 * Mock body markup mirrors the real Pro Staging page so the blurred
 * silhouette behind the upgrade popup looks identical. Content is static
 * and not localized: it is rendered inside .dup-mock-blur and is not read.
 */

$rows = $tplMng->getDataValueArrayRequired('rows');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <div class="dup-toolbar">
        <label for="dupli-staging-bulk-actions" class="screen-reader-text">Select bulk action</label>
        <select id="dupli-staging-bulk-actions" class="small">
            <option value="-1" selected="selected">Bulk Actions</option>
            <option value="delete">Delete</option>
        </select>
        <input type="button" class="button hollow secondary small" value="Apply">
    </div>

    <table class="widefat dup-table-list dup-packtbl striped">
        <thead>
            <tr>
                <th class="dup-check-column dupli-check-column-narrow">
                    <input type="checkbox">
                </th>
                <th class="dup-name-column">Name</th>
                <th>Status</th>
                <th>Source Backup</th>
                <th>Details</th>
                <th>Created</th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row) : ?>
                <tr class="staging-row">
                    <td class="dup-check-column dup-cell-chk">
                        <label>
                            <input type="checkbox">
                        </label>
                    </td>
                    <td class="dup-name-column">
                        <strong><?php echo esc_html($row['name']); ?></strong>
                    </td>
                    <td>
                        <i class="fas fa-clock" aria-hidden="true"></i>&nbsp;Install Pending
                    </td>
                    <td>
                        <span class="dupli-backup-deleted">
                            <?php echo esc_html($row['source']); ?>
                        </span>
                    </td>
                    <td class="dupli-versions-column">
                        <span>
                            <i class="fab fa-wordpress fa-fw" aria-hidden="true"></i>
                            <?php echo esc_html($row['wp']); ?>
                        </span>
                        <br>
                        <span>
                            <i class="fas fa-clone fa-fw" aria-hidden="true"></i>
                            <?php echo esc_html($row['dup']); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html($row['created']); ?></td>
                    <td class="dup-cell-btns dupli-open-admin-column">
                        <span class="full-cell-button link-style">
                            <i class="fas fa-play-circle fa-fw" aria-hidden="true"></i> Finish Setup
                        </span>
                    </td>
                    <td class="dup-cell-btns dupli-delete-column">
                        <span class="full-cell-button link-style">
                            <i class="fas fa-trash-alt fa-fw" aria-hidden="true"></i> Delete
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" class="dupli-staging-table-footer">
                    Total: <?php echo count($rows); ?>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Create staging sites to safely test changes!', 'duplicator'),
    'warningText' => __('Staging sites are not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [
        __(
            'Staging sites let you create a full copy of your WordPress site to safely test updates, new plugins,
            theme changes and other modifications without affecting your live site.',
            'duplicator'
        ),
        __(
            'Duplicator Pro creates an isolated staging site from a backup so you can freely test changes 
            without any risk to your live site.',
            'duplicator'
        ),
    ],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Staging'),
]);
