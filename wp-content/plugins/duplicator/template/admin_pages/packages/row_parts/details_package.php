<?php

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\CapMng;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var Duplicator\Core\Views\TplMng $tplMng
 */

$package = $tplMng->getDataValueObjRequired('package', DupPackage::class);

$pack_dbonly         = $package->isDBOnly();
$pack_format         = strtolower($package->Archive->Format);
$packageDetailsURL   = PackagesPageController::getInstance()->getPackageDetailsURL($package->getId());
$txt_DBOnly          = __('DB Only', 'duplicator');
$archive_exists      = ($package->getLocalPackageFilePath(AbstractPackage::FILE_TYPE_ARCHIVE) != false);
$environmentSnapshot = $package->getEnvironmentSnapshot();
$environmentDialogId = 'dupli-backup-environment-' . $package->getId();

?>
<td colspan="11">
    <div class="dup-package-row-details-wrapper">
        <div class="dupli-row-ovr-hdr">
            <h3 class="font-bold">
                <i class="fas fa-archive"></i>
                <?php esc_html_e('Backup Overview', 'duplicator'); ?>
                <?php if ($package->hasBuildWarnings()) { ?>
                    <span class="icon-wrapper"
                          data-tooltip-title="<?php esc_attr_e('Backup created with warnings', 'duplicator'); ?>"
                          data-tooltip="<?php echo esc_attr($tplMng->render(
                              'admin_pages/packages/row_parts/build_warnings_tooltip',
                              ['warnings' => $package->getBuildWarningsDisplayList()],
                              false
                          )); ?>">
                        <i class="fa-solid fa-triangle-exclamation warning-color"></i>
                    </span>
                <?php } ?>
            </h3>
            <div class="dupli-row-ovr-hdr-actions">
                <a
                    aria-label="<?php esc_attr_e("Go to Backup details screen", 'duplicator') ?>"
                    class="button hollow secondary small dup-details"
                    href="<?php echo esc_url($packageDetailsURL); ?>">
                    <i class="fas fa-search"></i> <?php esc_html_e("View Details", 'duplicator') ?>
                </a>
                <?php if (CapMng::can(CapMng::CAP_STORAGE, false)) { ?>
                    <?php if ($archive_exists) : ?>
                        <button type="button" class="button hollow secondary small dup-transfer"
                            aria-label="<?php esc_attr_e('Go to Backup transfer screen', 'duplicator') ?>"
                            onclick="DupliJs.Pack.OpenPackTransfer(<?php echo (int) $package->getId(); ?>); return false;">
                            <i class="fa fa-exchange-alt fa-fw"></i> <?php esc_html_e("Transfer Backup", 'duplicator') ?>
                        </button>
                    <?php else : ?>
                        <span title="<?php esc_attr_e('Transfer Backups requires the use of built-in default storage!', 'duplicator') ?>">
                            <button type="button" class="button hollow secondary small dup-transfer disabled">
                                <i class="fa fa-exchange-alt fa-fw"></i> <?php esc_html_e("Transfer Backup", 'duplicator') ?>
                            </button>
                        </span>
                    <?php endif; ?>
                    <button type="button" class="button hollow secondary small dupli-row-storages-btn"
                        aria-label="<?php esc_attr_e('Show the storage locations of this Backup', 'duplicator') ?>"
                        onclick="DupliJs.Pack.ShowRemote(<?php echo (int) $package->getId(); ?>, '<?php echo esc_js($package->getNameHash()); ?>');">
                        <i class="fas fa-server"></i> <?php esc_html_e('Storages', 'duplicator'); ?>
                    </button>
                <?php } ?>

                <?php do_action('duplicator_package_detail_actions', $package); ?>

                <?php if (CapMng::can(CapMng::CAP_CREATE, false)) { ?>
                    <button type="button" class="button hollow alert small dupli-delete-backup"
                        aria-label="<?php esc_attr_e('Delete Backup', 'duplicator') ?>"
                        onclick="DupliJs.Pack.ConfirmDeleteRow(<?php echo (int) $package->getId(); ?>); return false;">
                        <i class="fas fa-trash-alt fa-sm"></i> <?php esc_html_e('Delete', 'duplicator'); ?>
                    </button>
                <?php } ?>
            </div>
        </div>

        <div class="dupli-row-ovr-grid">
            <section class="dupli-row-ovr-section">
                <h4 class="dupli-row-ovr-title"><?php esc_html_e('Environment', 'duplicator'); ?></h4>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('WordPress', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value"><?php echo esc_html($package->VersionWP); ?></span>
                </div>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('Duplicator', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value"><?php echo esc_html($package->getVersion()); ?></span>
                </div>
                <?php if ($environmentSnapshot !== null) { ?>
                    <div class="dupli-row-kv">
                        <span class="dupli-row-kv-label">
                            <?php echo esc_html(_n('Active Theme', 'Active Themes', count($environmentSnapshot->getActiveThemes()), 'duplicator')); ?>
                        </span>
                        <span class="dupli-row-kv-value">
                            <?php
                            $activeThemes = $environmentSnapshot->getActiveThemes();
                            echo isset($activeThemes[0])
                                ? esc_html($activeThemes[0]['name'] . ' ' . $activeThemes[0]['version'])
                                : esc_html__('Unknown', 'duplicator');
                            ?>
                        </span>
                    </div>
                    <div class="dupli-row-kv">
                        <span class="dupli-row-kv-label"><?php esc_html_e('Active Plugins', 'duplicator'); ?></span>
                        <span class="dupli-row-kv-value">
                            <button
                                type="button"
                                class="link-style dupli-backup-environment-open"
                                data-dialog-id="<?php echo esc_attr($environmentDialogId); ?>">
                                <i class="fas fa-plug fa-sm"></i>
                                <?php echo (int) $environmentSnapshot->getActivePluginCount(); ?>
                                <span class="screen-reader-text"><?php esc_html_e('View active theme and plugins', 'duplicator'); ?></span>
                            </button>
                        </span>
                    </div>
                <?php } ?>
            </section>

            <section class="dupli-row-ovr-section">
                <h4 class="dupli-row-ovr-title"><?php esc_html_e('Archive', 'duplicator'); ?></h4>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('Format', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value"><?php echo esc_html(strtoupper($pack_format)); ?></span>
                </div>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('Files', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value">
                        <?php echo ($pack_dbonly)
                            ? '<i>' . esc_html($txt_DBOnly) . '</i>'
                            : esc_html(number_format($package->Archive->FileCount)); ?>
                    </span>
                </div>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('Folders', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value">
                        <?php echo ($pack_dbonly)
                            ? '<i>' . esc_html($txt_DBOnly) . '</i>'
                            : esc_html(number_format($package->Archive->DirCount)); ?>
                    </span>
                </div>
                <div class="dupli-row-kv">
                    <span class="dupli-row-kv-label"><?php esc_html_e('Tables', 'duplicator'); ?></span>
                    <span class="dupli-row-kv-value">
                        <?php
                        printf(
                            esc_html_x(
                                '%1$d of %2$d',
                                'Example: 7 of 10',
                                'duplicator'
                            ),
                            (int) $package->Database->info->tablesFinalCount,
                            (int) $package->Database->info->tablesBaseCount
                        );
                        ?>
                    </span>
                </div>
            </section>

            <?php if (CapMng::can(CapMng::CAP_EXPORT, false)) { ?>
                <section class="dupli-row-ovr-section dupli-row-ovr-section-res">
                    <h4 class="dupli-row-ovr-title"><?php esc_html_e('Install Resources', 'duplicator'); ?></h4>
                    <?php $tplMng->render('admin_pages/packages/row_parts/details_download_block'); ?>
                </section>
            <?php } ?>
        </div>

        <?php if ($environmentSnapshot !== null) { ?>
            <div id="<?php echo esc_attr($environmentDialogId); ?>" class="no-display">
                <?php
                $tplMng->render(
                    'admin_pages/packages/row_parts/environment_details',
                    ['environmentSnapshot' => $environmentSnapshot]
                );
                ?>
            </div>
        <?php } ?>
    </div>
</td>
