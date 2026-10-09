<?php

namespace Duplicator\Controllers;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Models\TemplateEntity;
use Duplicator\Core\CapMng;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Models\FixesEntity;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageUtils;
use Duplicator\Package\Runner;
use Duplicator\Package\TemporaryPackageUtils;
use Duplicator\Libs\WpUtils\PathUtil;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Utils\Lock\FileLock;
use Duplicator\Utils\Lock\SqlLock;
use Duplicator\Views\PackageScreen;
use Exception;

/**
 * Packages page controller
 */
class PackagesPageController extends AbstractMenuPageController
{
    const L2_SLUG_PACKAGE_LIST = 'packages';

    const LIST_INNER_PAGE_LIST      = 'list';
    const LIST_INNER_PAGE_NEW_STEP1 = 'new1';
    const LIST_INNER_PAGE_NEW_STEP2 = 'new2';
    const LIST_INNER_PAGE_DETAILS   = 'detail';
    const LIST_INNER_PAGE_TRANSFER  = 'transfer';

    /*
     * action types
     */
    const ACTION_START_DOWNLOAD   = 'start_package_download';
    const ACTION_START_RESTORE    = 'start_package_restore';
    const ACTION_STOP_BUILD       = 'stop_package_build';
    const ACTION_UPDATE_TEMPLATE  = 'update_template';
    const ACTION_CREATE_FROM_TEMP = 'create-from-temp';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::PACKAGES_SUBMENU_SLUG;
        $this->pageTitle    = __('Backups', 'duplicator');
        $this->menuLabel    = __('Backups', 'duplicator');
        $this->capatibility = CapMng::CAP_BASIC;
        $this->menuPos      = 10;

        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'beforeRenderPage'], 10, 2);
        add_action('duplicator_before_render_page_' . $this->pageSlug, [$this, 'setPackagePageObject'], 10, 2);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_filter('duplicator_page_template_data_' . $this->pageSlug, [$this, 'updatePackagePageTitle']);
        add_filter('set_screen_option_package_screen_options', [PackageScreen::class, 'setScreenOptions'], 11, 3);
        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
    }

    /**
     * Set Backup page title
     *
     * @param array<string, mixed> $tplData template global data
     *
     * @return array<string, mixed>
     */
    public function updatePackagePageTitle($tplData)
    {
        $innerPage = $this->getCurrentInnerPage();
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId            = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                $tplData['pageTitle'] = $this->getPackageDetailTitle($packageId);
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                $tplData['pageTitle'] = __('New Backup', 'duplicator');
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $tplData['pageTitle'] = __('New Backup - Scan', 'duplicator');
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                $tplData['pageTitle']             =  __('Backups', 'duplicator');
                $tplData['templateSecondaryPart'] = 'admin_pages/packages/package_create_button';
                break;
        }
        return $tplData;
    }

    /**
     * Return body header template. Can be overriden by child classes for custom header.
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return string
     */
    protected function getBodyHeaderTpl($currentLevelSlugs, $innerPage)
    {
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                return 'admin_pages/packages/details/details_wpbody_header';
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
            case self::LIST_INNER_PAGE_LIST:
            default:
                return parent::getBodyHeaderTpl($currentLevelSlugs, $innerPage);
        }
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function setPackagePageObject($currentLevelSlugs, $innerPage): void
    {
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DupPackage::getById($packageId)) == false) {
                    TplMng::getInstance()->setGlobalValue('package', null);
                } else {
                    TplMng::getInstance()->setGlobalValue('package', $package);
                }
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
            case self::LIST_INNER_PAGE_LIST:
            default:
                break;
        }
    }

    /**
     * Set Backup object before render pages
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function beforeRenderPage($currentLevelSlugs, $innerPage): void
    {
        TplMng::getInstance()->setGlobalValue('blur', false);
    }

    /**
     * Capability check
     *
     * @return void
     */
    protected function capabilityCheck()
    {
        parent::capabilityCheck();

        $capOk     = true;
        $innerPage = $this->getCurrentInnerPage();
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
                break;
            case self::LIST_INNER_PAGE_TRANSFER:
                $capOk = CapMng::can(CapMng::CAP_CREATE, false);
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                $nonce = SnapUtil::sanitizeTextInput(INPUT_GET, '_wpnonce', '');
                $capOk = ($nonce !== '' && CapMng::can(CapMng::CAP_CREATE, false) && wp_verify_nonce($nonce, 'new1-package'));
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $nonce = SnapUtil::sanitizeTextInput(INPUT_GET, '_wpnonce', '');
                $capOk = ($nonce !== '' && CapMng::can(CapMng::CAP_CREATE, false) && wp_verify_nonce($nonce, 'new2-package'));
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                break;
        }

        if (!$capOk) {
            self::notPermsDie();
        }
    }

    /**
     * Return actions for current page
     *
     * @param PageAction[] $actions actions lists
     *
     * @return PageAction[]
     */
    public function pageActions($actions)
    {
        $actions[] = new PageAction(
            self::ACTION_START_DOWNLOAD,
            [
                PackagesPageActions::class,
                'startDownload',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_START_RESTORE,
            [
                PackagesPageActions::class,
                'startRestore',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_STOP_BUILD,
            [
                PackagesPageActions::class,
                'stopBuild',
            ],
            [$this->pageSlug]
        );

        $actions[] = new PageAction(
            self::ACTION_UPDATE_TEMPLATE,
            [
                $this,
                'updateTemplate',
            ],
            [$this->pageSlug],
            self::LIST_INNER_PAGE_NEW_STEP2
        );

        $actions[] = new PageAction(
            self::ACTION_CREATE_FROM_TEMP,
            [
                $this,
                'createFromTemp',
            ],
            [$this->pageSlug]
        );

        return $actions;
    }

    /**
     * Update the template
     *
     * @return array<string, mixed>
     */
    public function updateTemplate(): array
    {
        if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
            return ['errorMessage' => $blockMessage];
        }

        $templateId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'template_id', -1);
        if (
            $templateId < 0 ||
            TemplateEntity::getById($templateId) === false
        ) {
            DupLog::trace("Couldn't update manual template. Invalid template id.");
        }

        $storageIds = filter_input_array(INPUT_POST, [
            '_storage_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => []],
            ],
        ])['_storage_ids'];

        // Clear fixes and force the next runner tick
        FixesEntity::getInstance()->clear();
        Runner::resetPackageCheckTimestamp();

        DupPackage::setManualTemplateFromPost($_REQUEST);
        if (GlobalEntity::getInstance()->setManualModeStorageIds($storageIds)) {
            DupLog::trace("Manual template updated");
        } else {
            DupLog::trace("Could not update manual template.");
        }
        TemporaryPackageUtils::createTemporaryPackage($templateId, $storageIds);

        return [];
    }

    /**
     * Create From Temp
     *
     * @return array<string, mixed>
     */
    public function createFromTemp()
    {
        try {
            if (PackageUtils::isBackupCreationBlocked($blockMessage)) {
                return ['errorMessage' => $blockMessage];
            }
            TemporaryPackageUtils::createPackageFromTemporaryPackage();
            return [];
        } catch (DupliException $e) {
            return ['errorMessage' => $e->getUserMessage()];
        } catch (Exception $e) {
            return ['errorMessage' => 'Failed to create package from temporary package: ' . $e->getMessage()];
        }
    }

    /**
     * Return create Backup link
     *
     * @return string
     */
    public function getPackageBuildS1Url()
    {
        return $this->getMenuLink(
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_NEW_STEP1,
                '_wpnonce'                                  => wp_create_nonce('new1-package'),
            ]
        );
    }

    /**
     * Return create Backup link step2
     *
     * @return string
     */
    public function getPackageBuildS2Url()
    {
        return $this->getMenuLink(
            null,
            null,
            [
                ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_NEW_STEP2,
                '_wpnonce'                                  => wp_create_nonce('new2-package'),
            ]
        );
    }

    /**
     * called on admin_print_styles-[page] hook
     *
     * @return void
     */
    public function pageStyles(): void
    {
    }

    /**
     * Get Backup detail title page
     *
     * @param int<0,max> $packageId Backup id
     *
     * @return string
     */
    protected function getPackageDetailTitle($packageId = 0)
    {
        if ($packageId === 0 || ($package = DupPackage::getById($packageId)) === false) {
            return __('Backup: Not Found', 'duplicator');
        } else {
            return sprintf(__('Backup: %1$s', 'duplicator'), $package->getName());
        }
    }

    /**
     * Get Backup list title page
     *
     * @return string
     */
    protected function getPackageListTitle(): string
    {
        $postfix = '';
        switch ($this->getCurrentInnerPage()) {
            case self::LIST_INNER_PAGE_NEW_STEP1:
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $postfix = __('New', 'duplicator');
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                $postfix = __('All', 'duplicator');
                break;
        }
        return __('Backups', 'duplicator') . " » " . $postfix;
    }

    /**
     * Render page content
     *
     * @param string[] $currentLevelSlugs current menu slugs
     * @param string   $innerPage         current inner page, empty if not set
     *
     * @return void
     */
    public function renderContent($currentLevelSlugs, $innerPage): void
    {
        switch ($innerPage) {
            case self::LIST_INNER_PAGE_DETAILS:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DupPackage::getById($packageId)) == false) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/details/no_package_found',
                        ['packageId' => $packageId]
                    );
                } else {
                    TplMng::getInstance()->render('admin_pages/packages/details/detail');
                }
                break;
            case self::LIST_INNER_PAGE_TRANSFER:
                $packageId = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'id', 0);
                if ($packageId == 0 || ($package = DupPackage::getById($packageId)) == false) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/details/no_package_found',
                        ['packageId' => $packageId]
                    );
                } else {
                    TplMng::getInstance()->render('admin_pages/packages/details/transfer');
                }
                break;
            case self::LIST_INNER_PAGE_NEW_STEP1:
                // Repair stored values that became unavailable (same policy as
                // the pre-backup gate) so the wizard is not blocked by them.
                foreach (OptionsManager::getInstance()->applyCorrections() as $optionKey => $value) {
                    DupLog::trace('Backup option corrected on manual backup setup: ' . $optionKey . ' => ' . var_export($value, true));
                }
                $validation = OptionsManager::getInstance()->validateConfig();
                if (!$validation->isValid()) {
                    DupLog::traceObject('Requirements validation failures', $validation->getLogLines());
                }
                TplMng::getInstance()->render(
                    'admin_pages/packages/setup/setup_page',
                    ['validation' => $validation]
                );
                break;
            case self::LIST_INNER_PAGE_NEW_STEP2:
                $package   = TemporaryPackageUtils::getTemporaryPackage();
                $validator = $package->validateInputs();
                if (!$validator->isSuccess()) {
                    TplMng::getInstance()->render(
                        'admin_pages/packages/scan/validation_error',
                        ['validator' => $validator]
                    );
                    return;
                }

                TplMng::getInstance()->render('admin_pages/packages/scan/main', self::getScanTplData($package));
                break;
            case self::LIST_INNER_PAGE_LIST:
            default:
                TplMng::getInstance()->render(
                    'admin_pages/packages/main',
                    [
                        'isPackageRunning'       => DupPackage::isPackageRunning(),
                        'stopBuildActionKey'     => self::ACTION_STOP_BUILD,
                        'startDownloadActionKey' => self::ACTION_START_DOWNLOAD,
                        'startRestoreActionKey'  => self::ACTION_START_RESTORE,
                    ]
                );
                break;
        }
    }

    /**
     * Set global template data for scan step
     *
     * @param AbstractPackage $package The temporary package
     *
     * @return array<string, mixed> The render data
     */
    private static function getScanTplData(AbstractPackage $package): array
    {
        global $wpdb;

        // Live lock reliability tests (isolated identifiers: they never touch the process lock)
        $workingLocks = [];
        $lockErrors   = [];
        foreach ([new SqlLock(), new FileLock()] as $lockEngine) {
            if ($lockEngine->isReliable()) {
                $workingLocks[] = $lockEngine->getLockLabel();
            } else {
                $lockErrors[] = $lockEngine->getLockLabel() . ': '
                    . ($lockEngine->getLastLockError() ?? __('lock cannot be acquired', 'duplicator'));
            }
        }

        $procQuery = $wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db` = %s", $wpdb->dbname);
        $funcQuery = $wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db`  = %s", $wpdb->dbname);
        $data      = [
            'package'           => $package,
            'filteredCoreDirs'  => [],
            'filteredCoreFiles' => [],
            'prefix'            => $wpdb->prefix,
            'prefixFilter'      => $package->Database->isPrefixFilterEnabled(),
            'prefixSubFilter'   => $package->Database->prefixSubFilter,
            'procedures'        => $wpdb->get_col($procQuery, 1),
            'functions'         => $wpdb->get_col($funcQuery, 1),
            'triggers'          => $wpdb->get_col("SHOW TRIGGERS", 1),
            'workingLocks'      => $workingLocks,
            'lockErrors'        => $lockErrors,
        ];

        if (!$package->isDBOnly() && $package->Archive->FilterOn) {
            $filteredDirs             = PackageArchive::parseDirectoryFilter($package->Archive->FilterDirs, true);
            $data['filteredCoreDirs'] = array_intersect($filteredDirs, PathUtil::getWPCoreDirs());

            $filteredFiles             = PackageArchive::parseFileFilter($package->Archive->FilterFiles, true);
            $data['filteredCoreFiles'] = array_intersect($filteredFiles, PathUtil::getWPCoreFiles());
        }

        return $data;
    }

    /**
     * Build template data for the Backups list footer (totals, last backup, retention).
     *
     * @return array{lastBackupCreated:string, defaultStorageMaxPackages:int}
     */
    public static function getListFooterTplData(): array
    {
        /** @var DupPackage[] */
        $latestPackages    = DupPackage::getPackagesByStatus(
            [
                [
                    'op'     => '=',
                    'status' => AbstractPackage::STATUS_COMPLETE,
                ],
            ],
            1,
            0,
            '`created` DESC'
        );
        $lastBackupCreated = ($latestPackages !== [] && isset($latestPackages[0]))
            ? $latestPackages[0]->getCreated()
            : '';

        return [
            'lastBackupCreated'         => $lastBackupCreated,
            'defaultStorageMaxPackages' => StoragesUtil::getDefaultStorage()->getMaxPackages(),
        ];
    }

    /**
     * Get Backup detail url
     *
     * @param false|int $package_id Backup id, if false return base url without id
     *
     * @return string
     */
    public function getPackageDetailsUrl($package_id = false)
    {
        $data = [
            ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_DETAILS,
        ];
        if ($package_id !== false) {
            $data['id'] = $package_id;
        }
        return $this->getMenuLink(null, null, $data);
    }

    /**
     * Get Backup detail url
     *
     * @param false|int $package_id Backup id, if false return base url without id
     *
     * @return string
     */
    public function getPackageTransferUrl($package_id = false)
    {
        $data = [
            ControllersManager::QUERY_STRING_INNER_PAGE => self::LIST_INNER_PAGE_TRANSFER,
        ];
        if ($package_id !== false) {
            $data['id'] = $package_id;
        }
        return $this->getMenuLink(null, null, $data);
    }

    /**
     * Get Backups inner page
     *
     * @return string
     */
    public function getPackagesInnerPage()
    {
        return $this->getCurrentInnerPage();
    }
}
