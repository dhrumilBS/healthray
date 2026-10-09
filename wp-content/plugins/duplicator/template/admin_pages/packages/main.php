<?php



defined('ABSPATH') || exit;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Views\PackageListTable;

/**
 * Variables
 *
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 */

$blur               = $tplMng->getDataValueBool('blur');
$stopBuildActionKey = $tplMng->getDataValueStringRequired('stopBuildActionKey');

// Filter out failed backups (status < 0) from the main backup list
$statusConditions = [
    [
        'op'     => '>=',
        'status' => 0,
    ],
];

$totalElements = PackageUtils::getNumPackages([DupPackage::getType()], $statusConditions);
$statusActive  = DupPackage::isPackageRunning();
$activePackage = DupPackage::getNextActive();
$isTransfer    = $activePackage === null ? false : $activePackage->getStatus() == AbstractPackage::STATUS_STORAGE_PROCESSING;

$pager       = new PackageListTable();
$perPage     = $pager->get_per_page();
$currentPage = $statusActive && !$isTransfer ? 1 : $pager->get_pagenum();
$offset      = ($currentPage - 1) * $perPage;

$global = GlobalEntity::getInstance();


do_action('duplicator_before_packages_table_action');
?>
<form
    id     = "form-duplicator"
    method = "post"
    class  = "<?php echo esc_attr($blur ? 'dup-mock-blur' : ''); ?>" >
    <?php $tplMng->getAction($stopBuildActionKey)->getActionNonceFileds(); ?>
    <input type="hidden" id="stop-backup-id" name="stop-backup-id" />
    <?php $tplMng->render('admin_pages/packages/toolbar'); ?>

    <table class="widefat dup-table-list dup-packtbl striped" aria-label="Backup List">
        <?php
        $tplMng->render(
            'admin_pages/packages/packages_table_head',
            ['totalElements' => $totalElements]
        );

        if ($totalElements == 0) {
            $tplMng->render('admin_pages/packages/no_elements_row');
        } else {
            DupPackage::dbSelectByStatusCallback(
                function (DupPackage $package): void {
                    if (PackageUtils::hasNoStorageCopy($package)) {
                        // Persist the recalculated storage flags so the list query and the cleanup cron exclude it.
                        // The row still counts toward this page's total once: accepted, it is a rare one-off.
                        $package->save(false);
                        return;
                    }
                    TplMng::getInstance()->render(
                        'admin_pages/packages/package_row',
                        ['package' => $package]
                    );
                },
                $statusConditions,
                $perPage,
                $offset,
                '`id` DESC',
                [
                    PackageUtils::DEFAULT_BACKUP_TYPE,
                ]
            );
        }
        $footerTplData = PackagesPageController::getListFooterTplData();
        $tplMng->render(
            'admin_pages/packages/packages_table_foot',
            [
                'totalElements'             => $totalElements,
                'lastBackupCreated'         => $footerTplData['lastBackupCreated'],
                'defaultStorageMaxPackages' => $footerTplData['defaultStorageMaxPackages'],
            ]
        ); ?>
    </table>
</form>

<?php if ($totalElements > $perPage) { ?>
    <form id="form-duplicator-nav" method="post">
        <div class="dup-paged-nav tablenav">
            <?php if ($statusActive > 0) { ?>
                <div id="dupli-paged-progress" style="padding-right: 10px">
                    <i class="fas fa-circle-notch fa-spin fa-lg fa-fw"></i>
                    <i><?php esc_html_e('Paging disabled during build...', 'duplicator'); ?></i>
                </div>
            <?php } else { ?>
                <div id="dupli-paged-buttons">
                    <?php $pager->display_pagination($totalElements, $perPage); ?>
                </div>
            <?php } ?>
        </div>
    </form>
<?php } else { ?>
    <div style="float:right; padding:10px 5px">
        <?php echo esc_html(sprintf(_n('%s item', '%s items', $totalElements, 'duplicator'), $totalElements)); ?>
    </div>
    <?php
}

$tplMng->render(
    'admin_pages/packages/packages_scripts',
    [
        'perPage'          => $perPage,
        'offset'           => $offset,
        'currentPage'      => $currentPage,
        'stattiBackupType' => DupPackage::getType(),
    ]
);
