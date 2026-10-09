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
 * Mock body markup mirrors the real Pro Templates (Tools > Templates) panel so
 * the blurred silhouette behind the upgrade popup looks identical. Content is
 * static and not localized: it is rendered inside .dup-mock-blur and is not
 * read.
 */

$rows = $tplMng->getDataValueArrayRequired('rows');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <h2>Templates</h2>
    <p>Create Backup Templates with Preset Configurations.</p>
    <hr>

    <div class="dup-toolbar">
        <label for="bulk_action" class="screen-reader-text">Select bulk action</label>
        <select id="bulk_action" class="small">
            <option value="-1" selected>Bulk Actions</option>
            <option value="delete">Delete</option>
        </select>
        <input type="button" class="button hollow secondary small" value="Apply">
        <span class="separator"></span>
        <span class="button primary small margin-0">Add New</span>
    </div>

    <table class="widefat dupli-template-list-tbl dup-table-list valign-top">
        <thead>
            <tr>
                <th class="col-check"><input type="checkbox" disabled></th>
                <th class="col-name">Name</th>
                <th class="col-flags">Flags&nbsp;<i class="fa-solid fa-circle-info"></i></th>
                <th class="col-empty"></th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?>
            <?php foreach ($rows as $row) : ?>
                <?php $i++; ?>
                <tr class="package-row <?php echo ($i % 2) ? 'alternate' : ''; ?>">
                    <td class="col-check">
                        <input type="checkbox" disabled>
                    </td>
                    <td class="col-name">
                        <span class="name"><?php echo wp_kses_post($row['name']); ?></span>
                        <div class="sub-menu">
                            <span class="link-style">Edit</span> |
                            <span class="link-style">Copy</span>
                            <?php if (! $row['isDefault']) : ?>
                                | <span class="link-style">Delete</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="col-flags">
                        <div class="dup-package-flags">
                            <span class="icon-wrapper"><i class="<?php echo esc_attr($row['shapeIcon']); ?>"></i></span>
                            <span class="icon-wrapper"><i class="fa-solid fa-lock-open warning-color"></i></span>
                            <?php if ($row['recoverable']) : ?>
                                <span class="icon-wrapper"><i class="fa-solid fa-house-fire dupli-recovery-flag-icon"></i></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>&nbsp;</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="8" style="text-align:right; font-size:12px">
                    Total: <?php echo count($rows); ?>
                </th>
            </tr>
        </tfoot>
    </table>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Easily customize your backups with templates!', 'duplicator'),
    'warningText' => __('Backup Templates are not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [
        __(
            'If you install the same theme, plugins or content on all your WordPress sites then Duplicator can save you a lot of time.',
            'duplicator'
        ),
        __(
            'Instead of manually configuring the same themes and plugins over and over, 
            just configure one site and bundle it into a Duplicator Backup. 
            Install the Backup to create a pre-configured site on as many locations as you want!',
            'duplicator'
        ),
    ],
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Templates'),
]);
