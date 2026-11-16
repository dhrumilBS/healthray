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
 * Mock body markup mirrors the real Pro Incremental Backups page with an
 * active snapshot chain, so the blurred silhouette behind the upgrade popup
 * looks identical. Content is static and not localized: it is rendered
 * inside .dup-mock-blur and is not read.
 */

$rows = $tplMng->getDataValueArrayRequired('rows');
?>
<div class="dup-mock-blur" aria-hidden="true">
    <div class="dupli-inc-page">
        <div class="dupli-inc-grid">
            <div class="dupli-inc-card">
                <div class="dupli-inc-card-head">
                    <div class="dupli-inc-card-title-wrap">
                        <h2 class="dupli-inc-card-title">Backup Chain</h2>
                        <span class="dupli-inc-status-pill dupli-inc-status-pill-active">Active</span>
                    </div>
                    <div class="dupli-inc-head-meta">
                        <span class="dupli-inc-connected">
                            <span class="dupli-inc-dot"></span>
                            Cloud storage connected
                            <a>Manage</a>
                        </span>
                        <span class="dupli-inc-divider"></span>
                        <a class="dupli-inc-dashboard-link">
                            Cloud Dashboard
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>

                <div class="dupli-inc-stats">
                    <div class="dupli-inc-stat">
                        <div class="dupli-inc-stat-label">Snapshots in chain</div>
                        <div class="dupli-inc-stat-value"><?php echo count($rows); ?></div>
                    </div>
                    <div class="dupli-inc-stat">
                        <div class="dupli-inc-stat-label">Baseline created</div>
                        <div class="dupli-inc-stat-value">January 12, 2027</div>
                    </div>
                    <div class="dupli-inc-stat">
                        <div class="dupli-inc-stat-label">Last snapshot</div>
                        <div class="dupli-inc-stat-value">47 mins ago</div>
                    </div>
                    <div class="dupli-inc-stat">
                        <div class="dupli-inc-stat-label">Cloud storage used</div>
                        <div class="dupli-inc-stat-value">
                            4.25GB
                            <span class="dupli-inc-stat-sub">of 25GB</span>
                        </div>
                        <div class="dupli-inc-meter dupli-inc-meter-thin">
                            <span class="dupli-inc-meter-fill dupli-inc-meter-fill-success dupli-inc-meter-sixth"></span>
                        </div>
                    </div>
                </div>

                <div class="dupli-inc-card-section">
                    <div class="dupli-inc-schedule-form">
                        <div class="dupli-inc-schedule-row">
                            <div class="dupli-inc-schedule-text">
                                <strong>Incremental backups enabled</strong>
                                <label>— back up changes every</label>
                                <select class="dupli-inc-hours-select margin-bottom-0" tabindex="-1">
                                    <option selected="selected">3</option>
                                </select>
                                <span>hours</span>
                                <label>— keep the last</label>
                                <input type="number" class="dupli-inc-max-backups-input margin-bottom-0" value="10" tabindex="-1">
                                <span>backups</span>
                            </div>
                            <span class="button primary margin-bottom-0 dupli-inc-schedule-activate">Deactivate</span>
                        </div>
                        <div class="dupli-inc-schedule-meta">
                            <span>Next run <strong>in 2 hrs 13 mins</strong></span>
                            <span>Coverage <strong>1 day and 6 hours</strong></span>
                            <span>Shorter intervals keep each snapshot small</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dupli-inc-card dupli-inc-changes">
                <div class="dupli-inc-card-head dupli-inc-card-head-stacked">
                    <div class="dupli-inc-changes-title">
                        DB Changes since last snapshot
                        <i class="fa-solid fa-info-circle fa-sm dark-gray-color"></i>
                    </div>
                    <div class="dupli-inc-changes-checked">Checked 6 mins ago</div>
                </div>
                <div class="dupli-inc-card-section">
                    <div class="dupli-inc-changes-rows">
                        <div class="dupli-inc-changes-row">
                            <span class="dupli-inc-changes-key">Database</span>
                            <span class="dupli-inc-changes-val">Changed — full copy included</span>
                        </div>
                        <div class="dupli-inc-changes-row">
                            <span class="dupli-inc-changes-key">Tables with changes</span>
                            <span class="dupli-inc-changes-val">3</span>
                        </div>
                        <div class="dupli-inc-changes-row">
                            <span class="dupli-inc-changes-key">Rows added/changed</span>
                            <span class="dupli-inc-changes-val">1,284</span>
                        </div>
                    </div>

                    <hr class="dupli-inc-hr">

                    <div class="dupli-inc-changes-actions">
                        <span class="button hollow secondary small margin-bottom-0">Check Again</span>
                        <span class="button clear secondary small margin-bottom-0">View change list</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="margin-top-2">
            <div class="dupli-inc-snapshots-head">
                <h3 class="title margin-bottom-0">Snapshots</h3>
                <div class="dupli-inc-snapshots-actions margin-top-1">
                    <span class="dupli-inc-snapshot-filter">
                        <label>Show:</label>
                        <select class="small margin-bottom-0" tabindex="-1">
                            <option selected="selected">All (<?php echo count($rows); ?>)</option>
                        </select>
                    </span>
                    <span class="button hollow secondary small margin-bottom-0">Back Up Changes Now</span>
                    <span class="button hollow small margin-bottom-0 dupli-inc-btn-danger">Reset Backup Chain…</span>
                </div>
            </div>

            <table class="widefat dup-table-list dup-packtbl striped dupli-inc-snapshots-table">
                <thead>
                    <tr>
                        <th class="dup-check-column"></th>
                        <th class="dup-note-column">Changes captured</th>
                        <th class="dup-storages-column">Storages</th>
                        <th class="dup-flags-column">Type&nbsp;<i class="fa-solid fa-circle-info"></i></th>
                        <th class="dup-size-column">Size&nbsp;<i class="fa-solid fa-circle-info"></i></th>
                        <th class="dup-age-column">Age</th>
                        <th class="dupli-inc-local-column">Local&nbsp;<i class="fa-solid fa-circle-info"></i></th>
                        <th class="dup-download-column"></th>
                        <th class="dup-restore-column"></th>
                        <th class="dup-details-column">
                            <span class="link-style"><i class="fa-solid fa-plus"></i></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr class="dup-row dup-row-complete">
                            <td class="dup-check-column dup-cell-chk"></td>
                            <td class="dup-note-column">
                                <span class="link-style"><?php echo esc_html($row['changes']); ?></span>
                            </td>
                            <td class="dup-storages-column"></td>
                            <td class="dup-flags-column"><?php echo esc_html($row['type']); ?></td>
                            <td class="dup-size-column"><?php echo esc_html($row['size']); ?></td>
                            <td class="dup-age-column"><?php echo esc_html($row['age']); ?></td>
                            <td class="dupli-inc-local-column">
                                <?php if ($row['local'] !== '') : ?>
                                    <i class="fa-solid fa-check success-color"></i>
                                    <?php echo esc_html($row['local']); ?>
                                <?php else : ?>
                                    &ndash;
                                <?php endif; ?>
                            </td>
                            <td class="dup-cell-btns dup-download-column">
                                <span class="full-cell-button link-style">
                                    <i class="fas fa-download fa-fw"></i> Download
                                </span>
                            </td>
                            <td class="dup-cell-btns dup-restore-column">
                                <span class="full-cell-button link-style">
                                    <i class="fas fa-undo-alt"></i> Restore
                                </span>
                            </td>
                            <td class="dup-cell-btns dup-cell-toggle-btn dup-details-column">
                                <span class="full-cell-button link-style">
                                    <i class="fa-solid fa-plus"></i>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10">
                            <div class="dup-pack-status-info">
                                <span class="float-right">Total: <?php echo count($rows); ?> snapshots</span>
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div class="float-right padding-half"><?php echo count($rows); ?> items</div>
        </div>
    </div>
</div>
<?php
TplMng::getInstance()->render('litebase/mocks/static-popup', [
    'title'       => __('Go back in time. Get your whole site back.', 'duplicator'),
    'warningText' => __('Incremental Backups are not available in Duplicator Lite!', 'duplicator'),
    'paragraphs'  => [
        __(
            'Incremental Backups copy your full site once, then save <strong>only what changed</strong>. Each backup is
            small and fast, so you can back up far more often.',
            'duplicator'
        ),
        sprintf(
            /* translators: %1$s is the opening anchor tag to Duplicator Cloud, %2$s its closing tag. */
            __(
                'Backup lives in <strong>%1$sDuplicator Cloud%2$s</strong>, off your server,
                they are safe even if your site is hacked or your host fails. When something goes wrong, just pick a backup
                and restore your entire site, just as it was.',
                'duplicator'
            ),
            '<a href="' . esc_url('https://cloud.duplicator.com/') . '" target="_blank" rel="noopener noreferrer">',
            '</a>'
        ),
        sprintf(
            /* translators: %1$s is the opening anchor tag to the YouTube video, %2$s its closing tag. */
            __('%1$sWatch the video (1:07)%2$s', 'duplicator'),
            '<a href="' . esc_url('https://www.youtube.com/watch?v=2E02mcnntj8') . '" target="_blank" rel="noopener noreferrer">'
                . '<i class="fa-solid fa-circle-play" aria-hidden="true"></i> ',
            '</a>'
        ),
    ],
    'ctaText'     => __('Unlock Incremental Backups with Pro', 'duplicator'),
    'upgradeUrl'  => LiteBaseLinks::getUpgradeUrl('blurred-mocks', 'Incremental'),
]);
