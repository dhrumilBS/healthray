<?php

/**
 * Settings page controller
 */

namespace Duplicator\Controllers;

use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Utils\ActivityLog\SettingsChangeTracker;
use Duplicator\Models\ActivityLog\LogEventSettingsChange;
use Duplicator\Core\CapMng;
use Duplicator\Core\Constants;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;
use Duplicator\Core\Controllers\PageAction;
use Duplicator\Core\Controllers\SubMenuItem;
use Duplicator\Core\Options\OptionsManager;
use Duplicator\Core\Options\Rules\ArchiveEngineRule;
use Duplicator\Core\Options\Rules\CompressionRule;
use Duplicator\Core\Options\Rules\DbDumpEngineRule;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Shell\ShellZipUtils;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\ClientSideKick;
use Duplicator\Utils\AsyncSetupActions;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\TraceLogMng;
use Duplicator\Utils\Settings\MigrateSettings;
use Duplicator\Utils\Settings\ServerThrottle;
use Duplicator\Utils\UsageStatistics\StatsBootstrap;
use Exception;

class SettingsPageController extends AbstractMenuPageController
{
    const NONCE_ACTION = 'dupli-settings-package';

    /**
     * tabs menu
     */
    const L2_SLUG_GENERAL         = 'general';
    const L2_SLUG_GENERAL_MIGRATE = 'migrate';
    const L2_SLUG_PACKAGE         = 'package';
    const L2_SLUG_STORAGE         = 'storage';
    /*
     * action types
     */
    const ACTION_GENERAL_SAVE          = 'save';
    const ACTION_GENERAL_TRACE         = 'trace';
    const ACTION_PACKAGE_ADVANCED_SAVE = 'pack-adv-save';
    const ACTION_PACKAGE_BASIC_SAVE    = 'pack-basic-save';
    const ACTION_RESET_SETTINGS        = 'reset-settings';
    const ACTION_SAVE_STORAGE          = 'save-storage';
    const ACTION_SAVE_STORAGE_SSL      = 'save-storage-ssl';
    const ACTION_SAVE_STORAGE_OPTIONS  = 'save-storage-options';
    const ACTION_IMPORT_SETTINGS       = 'import-settings';

    /**
     * Class constructor
     */
    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::SETTINGS_SUBMENU_SLUG;
        $this->pageTitle    = __('Settings', 'duplicator');
        $this->menuLabel    = __('Settings', 'duplicator');
        $this->capatibility = CapMng::CAP_SETTINGS;
        $this->menuPos      = 60;

        add_filter('duplicator_sub_menu_items_' . $this->pageSlug, [$this, 'getBasicSubMenus']);
        add_filter('duplicator_sub_level_default_tab_' . $this->pageSlug, [$this, 'getSubMenuDefaults'], 10, 2);
        add_action('duplicator_render_page_content_' . $this->pageSlug, [$this, 'renderContent'], 10, 2);
        add_filter('duplicator_page_actions_' . $this->pageSlug, [$this, 'pageActions']);
    }

    /**
     * Return sub menus for current page
     *
     * @param SubMenuItem[] $subMenus sub menus list
     *
     * @return SubMenuItem[]
     */
    public function getBasicSubMenus($subMenus)
    {
        $subMenus[] = new SubMenuItem(self::L2_SLUG_GENERAL, __('General', 'duplicator'), '', true, 10);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_PACKAGE, __('Backups', 'duplicator'), '', true, 20);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_STORAGE, __('Storage', 'duplicator'), '', true, 50);
        $subMenus[] = new SubMenuItem(self::L2_SLUG_GENERAL_MIGRATE, __('Import/Export Settings', 'duplicator'), '', true, 70);

        return $subMenus;
    }

    /**
     * Return slug default for parent menu slug
     *
     * @param string $slug   current default
     * @param string $parent parent for default
     *
     * @return string default slug
     */
    public function getSubMenuDefaults($slug, $parent)
    {
        switch ($parent) {
            case '':
                return self::L2_SLUG_GENERAL;
            default:
                return $slug;
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
            self::ACTION_GENERAL_SAVE,
            [
                $this,
                'saveGeneral',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_GENERAL,
            ]
        );
        $actions[] = new PageAction(
            self::ACTION_GENERAL_TRACE,
            [
                $this,
                'traceGeneral',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_GENERAL,
            ]
        );
        $actions[] = new PageAction(
            self::ACTION_PACKAGE_BASIC_SAVE,
            [
                $this,
                'savePackage',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_PACKAGE,
            ]
        );
        $actions[] = new PageAction(
            self::ACTION_RESET_SETTINGS,
            [
                $this,
                'resetSettings',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_GENERAL,
            ]
        );
        $actions[] = new PageAction(
            self::ACTION_SAVE_STORAGE,
            [
                $this,
                'saveStorageGeneral',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_STORAGE,
            ]
        );
        $actions[] = new PageAction(
            self::ACTION_IMPORT_SETTINGS,
            [
                $this,
                'importSettings',
            ],
            [
                $this->pageSlug,
                self::L2_SLUG_GENERAL_MIGRATE,
            ]
        );
        return $actions;
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
        switch ($currentLevelSlugs[1]) {
            case self::L2_SLUG_GENERAL:
                TplMng::getInstance()->render('admin_pages/settings/general/general');
                break;
            case self::L2_SLUG_GENERAL_MIGRATE:
                TplMng::getInstance()->render('admin_pages/settings/migrate_settings/migrate_page');
                break;
            case self::L2_SLUG_PACKAGE:
                TplMng::getInstance()->render(
                    'admin_pages/settings/backup/backup_settings',
                    array_merge(self::getAdvancedSettingsData(), self::getOptionsAvailabilityData())
                );
                break;
            case self::L2_SLUG_STORAGE:
                TplMng::getInstance()->render('admin_pages/settings/storage/storage_settings');
                break;
        }
    }

    /**
     * Prepare template data for the advanced backup settings section
     *
     * @return array<string, mixed>
     */
    private static function getAdvancedSettingsData(): array
    {
        $dGlobal      = DynamicGlobalEntity::getInstance();
        $detectedAuth = SnapServer::detectBasicAuthCredentials();
        $authMode     = $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY);

        $savedAuthUser = $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY);
        $savedAuthPass = $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY);

        $detectedAuthUser = $detectedAuth !== null ? $detectedAuth['user'] : '';

        // In custom mode warn when the server reports different credentials than the stored ones
        $showMismatchWarning = $authMode === 'custom'
            && $detectedAuth !== null
            && $savedAuthUser !== ''
            && $detectedAuth['user'] !== $savedAuthUser;

        $isShellZipAvailable = (ShellZipUtils::getShellExecZipPath() != null);
        $shellZipData        = ['hasShellZip' => $isShellZipAvailable];
        if (!$isShellZipAvailable) {
            $scanPaths        = PackageArchive::getScanPaths();
            $shellZipProblems = ShellZipUtils::getShellExecZipProblems();

            $shellZipData['multiScanPath'] = count($scanPaths) > 1;
            $shellZipData['problems']      = $shellZipProblems;
            $shellZipData['isWindows']     = SnapServer::isWindows();
        }

        return [
            'basicAuthMode'       => $authMode,
            'showMismatchWarning' => $showMismatchWarning,
            'detectedAuthUser'    => $detectedAuthUser,
            'savedAuthUser'       => $savedAuthUser,
            'savedAuthPass'       => $savedAuthPass,
            'shellZipData'        => $shellZipData,
        ];
    }

    /**
     * Availability matrix data for the gated backup options: the dependency
     * matrix the generic JS uses to update the children (disabled state and
     * warning icons) when a parent value changes. The matrix entry adds the
     * DOM field names to the manager data: reasons are stripped to plain text
     * for the tooltips. The static decorations are rendered by the templates
     * through OptionsUIHelper.
     *
     * @return array<string, mixed>
     */
    private static function getOptionsAvailabilityData(): array
    {
        $manager = OptionsManager::getInstance();

        $compressionMatrix = $manager->availabilityMatrix(CompressionRule::OPTION_KEY);
        foreach ($compressionMatrix['entries'] as $comboKey => $outcomes) {
            foreach ($outcomes as $valueKey => $outcome) {
                $compressionMatrix['entries'][$comboKey][$valueKey]['reasons'] = array_map('wp_strip_all_tags', $outcome['reasons']);
            }
        }

        return [
            'availabilityMatrix' => [
                CompressionRule::OPTION_KEY => array_merge($compressionMatrix, [
                    'field'        => GlobalEntity::ARCHIVE_COMPRESSION_KEY,
                    'parentFields' => [ArchiveEngineRule::OPTION_KEY => GlobalEntity::ARCHIVE_BUILD_MODE_KEY],
                ]),
            ],
        ];
    }

    /**
     * Save general settings
     *
     * @return array<string, mixed>
     */
    public function saveGeneral(): array
    {
        $result        = ['saveSuccess' => false];
        $changesTraker = new SettingsChangeTracker();
        $global        = GlobalEntity::getInstance();

        // Track uninstall settings changes
        $newUninstallSettings     = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'uninstall_settings');
        $currentUninstallSettings = StaticGlobal::getUninstallSettingsOption();
        $changesTraker->addChange(
            'uninstall_settings',
            $currentUninstallSettings,
            $newUninstallSettings,
            $newUninstallSettings ? 'enabled' : 'disabled'
        );
        StaticGlobal::setUninstallSettingsOption($newUninstallSettings);

        // Track uninstall packages changes
        $newUninstallPackages     = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'uninstall_packages');
        $currentUninstallPackages = StaticGlobal::getUninstallPackageOption();
        $changesTraker->addChange(
            'uninstall_packages',
            $currentUninstallPackages,
            $newUninstallPackages,
            $newUninstallPackages ? 'enabled' : 'disabled'
        );
        StaticGlobal::setUninstallPackageOption($newUninstallPackages);

        // Track crypt option changes (persisted after saves succeed to keep hooks consistent)
        $newCryptOption      = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'crypt');
        $currentCryptOption  = StaticGlobal::getCryptOption();
        $cryptSettingChanged = ($newCryptOption !== $currentCryptOption);
        $changesTraker->addChange(
            'crypt_option',
            $currentCryptOption,
            $newCryptOption,
            $newCryptOption ? 'enabled' : 'disabled'
        );

        // Track third party JS/CSS unhook changes
        $newUnhookJs = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_unhook_third_party_js');
        $changesTraker->addChange(
            GlobalEntity::UNHOOK_THIRD_PARTY_JS_KEY,
            $global->shouldUnhookThirdPartyJs(),
            $newUnhookJs,
            $newUnhookJs ? 'enabled' : 'disabled'
        );
        $global->setUnhookThirdPartyJs($newUnhookJs, false);

        $newUnhookCss = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_unhook_third_party_css');
        $changesTraker->addChange(
            GlobalEntity::UNHOOK_THIRD_PARTY_CSS_KEY,
            $global->shouldUnhookThirdPartyCss(),
            $newUnhookCss,
            $newUnhookCss ? 'enabled' : 'disabled'
        );
        $global->setUnhookThirdPartyCss($newUnhookCss);

        // Track trace log mode changes
        $newLoggingMode = SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, '_logging_mode');
        if (!empty($newLoggingMode)) {
            // Determine current logging mode before changes
            $currentLoggingMode = StaticGlobal::getTraceLogEnabledOption() ? 'on' : 'off';

            $loggingModeOptions = [
                'off' => __('Off', 'duplicator'),
                'on'  => __('On', 'duplicator'),
            ];
            $changesTraker->addChange(
                'logging_mode',
                $currentLoggingMode,
                $newLoggingMode,
                'optionChanged',
                $loggingModeOptions
            );
        }

        $this->updateLoggingModeOptions();

        // Track email summary frequency changes
        $newEmailFrequency = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, '_email_summary_frequency');
        $changesTraker->addChange(
            GlobalEntity::EMAIL_SUMMARY_FREQUENCY_KEY,
            $global->getEmailSummaryFrequency(),
            $newEmailFrequency,
            'frequencyChanged'
        );
        $global->setEmailSummaryFrequency($newEmailFrequency);
        // Track email recipients changes
        $emailRecipients = filter_input(INPUT_POST, '_email_summary_recipients', FILTER_SANITIZE_EMAIL, [
            'flags'   => FILTER_REQUIRE_ARRAY,
            'options' => [
                'default' => [],
            ],
        ]);
        if ($emailRecipients !== []) {
            $emailRecipients = array_map('sanitize_email', $emailRecipients);
        }



        $changesTraker->addChange(
            GlobalEntity::EMAIL_SUMMARY_RECIPIENTS_KEY,
            $global->getEmailSummaryRecipients(),
            $emailRecipients,
            'emailListChanged'
        );
        $global->setEmailSummaryRecipients($emailRecipients);

        // Track usage tracking changes (only if not hardcoded disabled)
        if (!DUPLICATOR_USTATS_DISALLOW) { // @phpstan-ignore-line
            $newUsageTracking = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'usage_tracking');
            $changesTraker->addChange(
                'usage_tracking',
                StatsBootstrap::isTrackingAllowed(),
                $newUsageTracking,
                $newUsageTracking ? 'enabled' : 'disabled'
            );
            StatsBootstrap::setTrackingAllowed($newUsageTracking);
        }

        // Track AM notices changes
        $newAmNotices = !SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dup_am_notices');
        $changesTraker->addChange(
            'am_notices',
            $global->isAmNoticesEnabled(),
            $newAmNotices,
            $newAmNotices ? 'enabled' : 'disabled'
        );
        $global->setAmNotices($newAmNotices);

        // Track trace log max size changes
        $newMaxSizeMB = SnapUtil::sanitizeIntInput(INPUT_POST, 'trace_max_size', TraceLogMng::DEFAULT_MAX_TOTAL_SIZE / MB_IN_BYTES);
        $changesTraker->addChange(
            'trace_max_size',
            TraceLogMng::getInstance()->getMaxTotalSize() / MB_IN_BYTES,
            $newMaxSizeMB,
            'sizeChanged',
            [
                'fromUnit' => 'MB',
                'toUnit'   => 'MB',
            ]
        );
        TraceLogMng::getInstance()->setMaxTotalSize($newMaxSizeMB * MB_IN_BYTES);

        if (($result['saveSuccess'] = $global->save()) == false) {
            $result['errorMessage'] = __('Can\'t update general settings', 'duplicator');
            return $result;
        } else {
            $result['successMessage'] = __("General settings updated.", 'duplicator');
        }

        // Save activity log retention setting in DynamicGlobalEntity
        if ($result['saveSuccess']) {
            $dGlobal                     = DynamicGlobalEntity::getInstance();
            $activityLogRetentionMonths  = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'activity_log_retention_months', 0);
            $activityLogRetentionSeconds = $activityLogRetentionMonths * MONTH_IN_SECONDS;

                    // Track activity log retention changes
            $changesTraker->addChange(
                'activity_log_retention',
                $dGlobal->getValInt('activity_log_retention'),
                $activityLogRetentionSeconds,
                'timeChanged',
                [
                    'fromUnit' => 'sec',
                    'toUnit'   => 'month',
                ]
            );
            $dGlobal->setValInt('activity_log_retention', $activityLogRetentionSeconds);

            if (($result['saveSuccess'] = $dGlobal->save()) == false) {
                $result['errorMessage'] = __('Can\'t update activity log retention settings', 'duplicator');
            } else {
                $changesTraker->createLog(LogEventSettingsChange::SUB_TYPE_GENERAL);
            }
        }

        if ($cryptSettingChanged && $result['saveSuccess']) {
            do_action('duplicator_before_update_crypt_setting');
            StaticGlobal::setCryptOption($newCryptOption);
            do_action('duplicator_after_update_crypt_setting');
        }

        return $result;
    }

    /**
     * Save storage general settings
     *
     * @return array<string, mixed>
     */
    public function saveStorageGeneral(): array
    {
        $result        = ['saveSuccess' => false];
        $changesTraker = new SettingsChangeTracker();
        $global        = GlobalEntity::getInstance();

        // Track storage htaccess setting changes
        $newHtaccessOff = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_storage_htaccess_off');
        $changesTraker->addChange(
            GlobalEntity::STORAGE_HTACCESS_OFF_KEY,
            $global->isStorageHtaccessOff(),
            $newHtaccessOff,
            $newHtaccessOff ? 'enabled' : 'disabled'
        );
        $global->setStorageHtaccessOff($newHtaccessOff, false);

        // Track SSL server certificates setting
        $newSslServerCerts = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, GlobalEntity::SSL_USE_SERVER_CERTS_KEY);
        $changesTraker->addChange(
            GlobalEntity::SSL_USE_SERVER_CERTS_KEY,
            $global->isUsingServerCerts(),
            $newSslServerCerts,
            $newSslServerCerts ? 'enabled' : 'disabled'
        );
        $global->setSslUseServerCerts($newSslServerCerts, false);

        // Track SSL verify disable setting
        $newSslDisableVerify = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, GlobalEntity::SSL_DISABLE_VERIFY_KEY);
        $changesTraker->addChange(
            GlobalEntity::SSL_DISABLE_VERIFY_KEY,
            !$global->isSslVerifyEnabled(),
            $newSslDisableVerify,
            $newSslDisableVerify ? 'enabled' : 'disabled'
        );
        $global->setSslDisableVerify($newSslDisableVerify, false);

        // Track IPv4 only setting
        $newIpv4Only = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, GlobalEntity::IPV4_ONLY_KEY);
        $changesTraker->addChange(
            GlobalEntity::IPV4_ONLY_KEY,
            $global->isIpv4Only(),
            $newIpv4Only,
            $newIpv4Only ? 'enabled' : 'disabled'
        );
        $global->setIpv4Only($newIpv4Only, false);

        // Track purge backup records setting
        $newPurgeRecords           = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, GlobalEntity::PURGE_BACKUP_RECORDS_KEY, 0);
        $purgeBackupRecordsOptions = [
            0 => __('When the backup archive is removed from all storages', 'duplicator'),
            1 => __('When maximum is reached for Default Local Storage', 'duplicator'),
            2 => __('Never', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::PURGE_BACKUP_RECORDS_KEY,
            $global->getPurgeBackupRecords(),
            $newPurgeRecords,
            'optionChanged',
            $purgeBackupRecordsOptions
        );
        if (($result['saveSuccess'] = $global->setPurgeBackupRecords($newPurgeRecords)) == false) {
            $result['errorMessage'] = __('Can\'t update storage settings.', 'duplicator');
        } else {
            $result['successMessage'] = __('Storage settings updated.', 'duplicator');
        }

        if ($result['saveSuccess']) {
            do_action('duplicator_update_global_storage_settings');
            $changesTraker->createLog(LogEventSettingsChange::SUB_TYPE_STORAGE);
        }

        return $result;
    }

    /**
     * Migrate settings
     *
     * @return array<string, mixed>
     */
    public function importSettings(): array
    {
        $inputData = filter_input_array(INPUT_POST, [
            'import-opts' => [
                'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => [
                    'default' => [],
                ],
            ],
        ]);

        if (empty($inputData['import-opts'])) {
            return ['errorMessage' => __('No import options selected.', 'duplicator')];
        }

        if (!isset($_FILES['import-file']['tmp_name'])) {
            return ['errorMessage' => __('No file uploaded.', 'duplicator')];
        }

        $filePath = SnapUtil::sanitizeNSCharsNewlineTabs($_FILES["import-file"]["tmp_name"]);
        try {
            if (MigrateSettings::import($filePath, $inputData['import-opts']) == false) {
                return ['errorMessage' => __('Couldn\'t import settings.', 'duplicator')];
            }
        } catch (Exception $ex) {
            return ['errorMessage' => sprintf(__('Couldn\'t import settings. Error: %s', 'duplicator'), $ex->getMessage())];
        }

        // Log the settings import action
        LogEventSettingsChange::create(
            LogEventSettingsChange::SUB_TYPE_IMPORT_EXPORT,
            [
                'changes'     => [], // Import doesn't track individual changes
                'action_type' => 'settings_import',
            ]
        );

        return ['successMessage' => __('Settings imported.', 'duplicator')];
    }

    /**
     * Reset all user settings and redirects to the settings page
     *
     * @return array<string, mixed>
     */
    public function resetSettings(): array
    {
        $result = ['saveSuccess' => false];

        $global = GlobalEntity::getInstance();

        // Capture before values for logging (before reset)
        $beforeValues = ['all_standard_settings' => 'existing_values'];

        if ($global->resetUserSettings() && $global->save()) {
            $result['successMessage'] = __('Settings reset to defaults successfully', 'duplicator');
            $result['saveSuccess']    = true;

            // Log the settings reset action
            LogEventSettingsChange::create(
                LogEventSettingsChange::SUB_TYPE_GENERAL,
                [
                    'changes'     => [], // Reset doesn't track individual changes
                    'action_type' => 'settings_reset',
                ]
            );
        } else {
            $result['errorMessage'] = __('Failed to reset settings.', 'duplicator');
            $result['saveSuccess']  = false;
        }

        TraceLogMng::getInstance()->setMaxTotalSize(TraceLogMng::DEFAULT_MAX_TOTAL_SIZE);
        return $result;
    }

    /**
     * Update trace mode
     *
     * @return array<string, mixed>
     */
    public function traceGeneral(): array
    {
        $result = ['saveSuccess' => false];

        switch (SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, '_logging_mode')) {
            case 'off':
                $this->updateLoggingModeOptions();
                $result = [
                    'saveSuccess'    => true,
                    'successMessage' => __("Trace settings have been turned off.", 'duplicator'),
                ];
                break;
            case 'on':
                $this->updateLoggingModeOptions();
                $result = [
                    'saveSuccess'    => true,
                    'successMessage' => __("Trace settings have been turned on.", 'duplicator'),
                ];
                break;
            default:
                $result = [
                    'saveSuccess'  => false,
                    'errorMessage' => __("Trace mode not valid.", 'duplicator'),
                ];
                break;
        }

        return $result;
    }

    /**
     * Upate loggin modes options
     *
     * @return void
     */
    protected function updateLoggingModeOptions()
    {
        switch (SnapUtil::sanitizeStrictInput(SnapUtil::INPUT_REQUEST, '_logging_mode')) {
            case 'off':
                StaticGlobal::setTraceLogEnabledOption(false);
                break;
            case 'on':
                if (StaticGlobal::getTraceLogEnabledOption() == false) {
                    DupLog::deleteTraceLog();
                }
                StaticGlobal::setTraceLogEnabledOption(true);
                break;
            default:
                break;
        }
    }

    /**
     * Resolve the installer name mode from a submitted value.
     *
     * Falls back to the secure hashed mode when the value is missing or
     * unrecognized, so a malformed request cannot downgrade to the predictable
     * simple name; an explicit simple selection is still honored.
     *
     * @param string $submittedMode Raw installer_name_mode input value
     *
     * @return string One of the GlobalEntity::INSTALLER_NAME_MODE_* constants
     */
    private static function resolveInstallerNameMode(string $submittedMode): string
    {
        return $submittedMode === GlobalEntity::INSTALLER_NAME_MODE_SIMPLE
            ? GlobalEntity::INSTALLER_NAME_MODE_SIMPLE
            : GlobalEntity::INSTALLER_NAME_MODE_WITH_HASH;
    }

    /**
     * Save Backup basic settings
     *
     * @return array<string, mixed>
     */
    public function savePackage(): array
    {
        $result          = ['saveSuccess' => false];
        $changesTraker   = new SettingsChangeTracker();
        $global          = GlobalEntity::getInstance();
        $dGlobal         = DynamicGlobalEntity::getInstance();
        $packageBuild    = Constants::DEFAULT_MAX_PACKAGE_RUNTIME_IN_MIN;
        $defaultTransfer = Constants::DEFAULT_MAX_PACKAGE_TRANSFER_TIME_IN_MIN;

        // Track database mode settings before they change
        $optionsMng    = OptionsManager::getInstance();
        $newDbMode     = SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_dbmode');
        $currentDbMode = $global->isMysqldumpEnabled() ? 'mysql' : 'php';
        $dbModeOptions = [
            'mysql' => $optionsMng->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_MYSQLDUMP),
            'php'   => $optionsMng->getValueLabel(DbDumpEngineRule::OPTION_KEY, DbDumpEngineRule::VALUE_PHP),
        ];
        $changesTraker->addChange(
            'package_dbmode',
            $currentDbMode,
            $newDbMode,
            'optionChanged',
            $dbModeOptions
        );

        $newPhpDumpMode     = filter_input(INPUT_POST, '_phpdump_mode', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
        $phpDumpModeOptions = [
            0 => __('Multi-Threaded', 'duplicator'),
            1 => __('Single-Threaded', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::PACKAGE_PHPDUMP_MODE_KEY,
            $global->getPhpDumpMode(),
            $newPhpDumpMode,
            'optionChanged',
            $phpDumpModeOptions
        );

        $newMysqldumpPath = SnapUtil::sanitizeDefaultInput(INPUT_POST, '_package_mysqldump_path');
        $changesTraker->addChange(
            GlobalEntity::PACKAGE_MYSQLDUMP_PATH_KEY,
            $global->getMysqldumpPath(),
            $newMysqldumpPath,
            'fieldChanged',
            [
                'truncate' => true,
                'max'      => 80,
            ]
        );

        $newMysqldumpQryLimit     = filter_input(
            INPUT_POST,
            '_package_mysqldump_qrylimit',
            FILTER_VALIDATE_INT,
            ['options' => ['default' => Constants::DEFAULT_MYSQL_DUMP_CHUNK_SIZE]]
        );
        $mysqldumpQryLimitOptions = Constants::MYSQL_DUMP_CHUNK_SIZES;
        $changesTraker->addChange(
            GlobalEntity::PACKAGE_MYSQLDUMP_QRYLIMIT_KEY,
            $global->getMysqldumpQueryLimit(),
            $newMysqldumpQryLimit,
            'optionChanged',
            $mysqldumpQryLimitOptions
        );

        // Track archive mode settings before they change
        $newArchiveBuildMode     = filter_input(INPUT_POST, GlobalEntity::ARCHIVE_BUILD_MODE_KEY, FILTER_VALIDATE_INT);
        $archiveBuildModeOptions = [];
        foreach ($optionsMng->getOptionValues(ArchiveEngineRule::OPTION_KEY) as $engineValue) {
            $archiveBuildModeOptions[(int) $engineValue] = $optionsMng->getValueLabel(ArchiveEngineRule::OPTION_KEY, $engineValue);
        }
        $changesTraker->addChange(
            GlobalEntity::ARCHIVE_BUILD_MODE_KEY,
            $global->getBuildMode(),
            $newArchiveBuildMode,
            'optionChanged',
            $archiveBuildModeOptions
        );

        $newZipArchiveMode     = filter_input(INPUT_POST, GlobalEntity::ZIPARCHIVE_MODE_KEY, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
        $zipArchiveModeOptions = [
            0 => __('Multi-Threaded', 'duplicator'),
            1 => __('Single-Threaded', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::ZIPARCHIVE_MODE_KEY,
            $global->getZipArchiveMode(),
            $newZipArchiveMode,
            'optionChanged',
            $zipArchiveModeOptions
        );

        $newArchiveCompression = filter_input(INPUT_POST, GlobalEntity::ARCHIVE_COMPRESSION_KEY, FILTER_VALIDATE_BOOLEAN);
        $changesTraker->addChange(
            GlobalEntity::ARCHIVE_COMPRESSION_KEY,
            $global->isArchiveCompressionEnabled(),
            $newArchiveCompression,
            $newArchiveCompression ? 'enabled' : 'disabled'
        );

        $newZipValidation = filter_input(INPUT_POST, GlobalEntity::ZIPARCHIVE_VALIDATION_KEY, FILTER_VALIDATE_BOOLEAN);
        $changesTraker->addChange(
            GlobalEntity::ZIPARCHIVE_VALIDATION_KEY,
            $global->isZipArchiveValidationEnabled(),
            $newZipValidation,
            $newZipValidation ? 'enabled' : 'disabled'
        );

        $newZipChunkSize = filter_input(
            INPUT_POST,
            GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            FILTER_VALIDATE_INT,
            ['options' => ['default' => GlobalEntity::getDefaultZipArchiveChunkSize()]]
        );
        $changesTraker->addChange(
            GlobalEntity::ZIPARCHIVE_CHUNK_SIZE_IN_MB_KEY,
            $global->getZipArchiveChunkSize(),
            $newZipChunkSize,
            'sizeChanged',
            [
                'fromUnit' => 'MB',
                'toUnit'   => 'MB',
            ]
        );

        $rejectedOptions = array_merge($global->setDbMode(), $global->setArchiveMode());

        // Track max package runtime changes
        $newPackageRuntime = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY, $packageBuild);
        $changesTraker->addChange(
            GlobalEntity::MAX_PACKAGE_RUNTIME_IN_MIN_KEY,
            $global->getMaxPackageRuntime(),
            $newPackageRuntime,
            'timeChanged',
            [
                'fromUnit' => 'min',
                'toUnit'   => 'min',
            ]
        );
        $global->setMaxPackageRuntime($newPackageRuntime, false);

        // Track server load reduction changes
        $newServerLoad     = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, GlobalEntity::SERVER_LOAD_REDUCTION_KEY, 0);
        $serverLoadOptions = [
            0 => __('Off', 'duplicator'),
            1 => __('Low', 'duplicator'),
            2 => __('Medium', 'duplicator'),
            3 => __('High', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::SERVER_LOAD_REDUCTION_KEY,
            $global->getServerLoadReduction(),
            $newServerLoad,
            'optionChanged',
            $serverLoadOptions
        );
        $global->setServerLoadReduction($newServerLoad, false);

        // Track max transfer time changes
        $newTransferTime = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY, $defaultTransfer);
        $changesTraker->addChange(
            GlobalEntity::MAX_PACKAGE_TRANSFER_TIME_IN_MIN_KEY,
            $global->getMaxPackageTransferTime(),
            $newTransferTime,
            'timeChanged',
            [
                'fromUnit' => 'min',
                'toUnit'   => 'min',
            ]
        );
        $global->setMaxPackageTransferTime($newTransferTime, false);

        // Handle installer name mode
        $newInstallerMode         = SnapUtil::sanitizeDefaultInput(INPUT_POST, GlobalEntity::INSTALLER_NAME_MODE_KEY);
        $installerNameMode        = self::resolveInstallerNameMode($newInstallerMode);
        $installerNameModeOptions = [
            'simple'    => __('Standard (installer.php)', 'duplicator'),
            'with_hash' => __('Hashed (more secure)', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::INSTALLER_NAME_MODE_KEY,
            $global->getInstallerNameMode(),
            $installerNameMode,
            'optionChanged',
            $installerNameModeOptions
        );
        $global->setInstallerNameMode($installerNameMode, false);

        $newKickoffOverride = SnapUtil::sanitizeTextInput(INPUT_POST, 'override_kickoff', 'auto');
        if (!in_array($newKickoffOverride, ['auto', 'server', 'client'])) {
            $newKickoffOverride = 'auto';
        }
        $dGlobal->setValString(ClientSideKick::KICKOFF_OVERRIDE_KEY, $newKickoffOverride, false);

        $newAjaxProtocol = SnapUtil::sanitizeTextInput(INPUT_POST, 'override_ajax_protocol', 'auto');
        if (!in_array($newAjaxProtocol, ['auto', 'http', 'https', 'custom'])) {
            $newAjaxProtocol = 'auto';
        }
        $dGlobal->setValString(ClientSideKick::AJAX_PROTOCOL_OVERRIDE_KEY, $newAjaxProtocol, false);

        $newAjaxUrlOverride = SnapUtil::sanitizeTextInput(INPUT_POST, 'override_ajax_url', '');
        $dGlobal->setValString(ClientSideKick::AJAX_URL_OVERRIDE_KEY, $newAjaxUrlOverride, false);

        $newHomepathAsAbspath = SnapUtil::sanitizeBoolInput(INPUT_POST, GlobalEntity::HOMEPATH_AS_ABSPATH_KEY, false);
        $changesTraker->addChange(
            GlobalEntity::HOMEPATH_AS_ABSPATH_KEY,
            $global->isHomePathAsAbsolute(),
            $newHomepathAsAbspath,
            $newHomepathAsAbspath ? 'enabled' : 'disabled'
        );
        $global->setHomePathAsAbsolute($newHomepathAsAbspath, false);

        $newSkipArchiveScan = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, '_skip_archive_scan', false);
        $changesTraker->addChange(
            GlobalEntity::SKIP_ARCHIVE_SCAN_KEY,
            $global->isArchiveScanSkipped(),
            $newSkipArchiveScan,
            $newSkipArchiveScan ? 'enabled' : 'disabled'
        );
        $global->setSkipArchiveScan($newSkipArchiveScan, false);

        // Basic auth mode ('auto': credentials synced by the server detection, 'custom': user-entered)
        $newBasicAuthMode = SnapUtil::sanitizeTextInput(INPUT_POST, DynamicGlobalEntity::BASIC_AUTH_MODE_KEY, 'auto');
        if (!in_array($newBasicAuthMode, ['auto', 'custom'])) {
            $newBasicAuthMode = 'auto';
        }
        $changesTraker->addChange(
            DynamicGlobalEntity::BASIC_AUTH_MODE_KEY,
            $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY),
            $newBasicAuthMode,
            'optionChanged',
            [
                'auto'   => __('Auto', 'duplicator'),
                'custom' => __('Custom', 'duplicator'),
            ]
        );

        if ($newBasicAuthMode === 'custom') {
            $basicAuthUser = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'basic_auth_user', '');

            $basicAuthPassword = SnapUtil::sanitizeTextInput(SnapUtil::INPUT_REQUEST, 'basic_auth_password', '');
            $basicAuthPassword = stripslashes(SnapUtil::sanitizeNSCharsNewlineTrim($basicAuthPassword));

            // Track basic auth user changes
            $changesTraker->addChange(
                'basic_auth_user',
                $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY),
                $basicAuthUser,
                'optionChanged'
            );

            // Track basic auth password changes (show only that it changed, not the actual password)
            $changesTraker->addChange(
                'basic_auth_password',
                false,
                $dGlobal->getValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY) !== $basicAuthPassword,
                'passwordChanged'
            );
        }

        // CLEANUP - Track cleanup settings before they change
        $newCleanupMode     = filter_input(
            INPUT_POST,
            GlobalEntity::CLEANUP_MODE_KEY,
            FILTER_VALIDATE_INT,
            ['options' => ['default' => GlobalEntity::CLEANUP_MODE_OFF]]
        );
        $cleanupModeOptions = [
            GlobalEntity::CLEANUP_MODE_OFF  => __('Off', 'duplicator'),
            GlobalEntity::CLEANUP_MODE_MAIL => __('Email notification', 'duplicator'),
            GlobalEntity::CLEANUP_MODE_AUTO => __('Automatic cleanup', 'duplicator'),
        ];
        $changesTraker->addChange(
            GlobalEntity::CLEANUP_MODE_KEY,
            $global->getCleanupMode(),
            $newCleanupMode,
            'optionChanged',
            $cleanupModeOptions
        );

        $newCleanupEmail = filter_input(
            INPUT_POST,
            GlobalEntity::CLEANUP_EMAIL_KEY,
            FILTER_VALIDATE_EMAIL,
            ['options' => ['default' => '']]
        );
        $newCleanupEmail = $newCleanupEmail === '' ? get_option('admin_email') : $newCleanupEmail;
        $changesTraker->addChange(
            GlobalEntity::CLEANUP_EMAIL_KEY,
            $global->getCleanupEmail(),
            $newCleanupEmail,
            'fieldChanged'
        );

        $newAutoCleanupHours = filter_input(
            INPUT_POST,
            GlobalEntity::AUTO_CLEANUP_HOURS_KEY,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'default'   => 24,
                    'min_range' => 1,
                ],
            ]
        );
        $changesTraker->addChange(
            GlobalEntity::AUTO_CLEANUP_HOURS_KEY,
            $global->getAutoCleanupHours(),
            $newAutoCleanupHours,
            'timeChanged',
            [
                'fromUnit' => 'hour',
                'toUnit'   => 'hour',
            ]
        );

        // Log accumulated changes BEFORE attempting to save
        $changesTraker->createLog(LogEventSettingsChange::SUB_TYPE_BACKUP);

        $global->setCleanupFields();

        if (($result['saveSuccess'] = $global->save()) == false) {
            $result['errorMessage'] = __('Can\'t Save Backup Settings', 'duplicator');
            return $result;
        } else {
            $result['successMessage'] = __("Backup Settings Saved.", 'duplicator');
        }

        $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_MODE_KEY, $newBasicAuthMode);
        if ($newBasicAuthMode === 'custom') {
            // Both empty means basic auth not set. In auto mode the credentials
            // are refreshed by the detection run below.
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY, $basicAuthUser);
            $dGlobal->setValString(DynamicGlobalEntity::BASIC_AUTH_PASSWORD_KEY, $basicAuthPassword);
        }

        $dGlobal->save();

        $detectionResult = AsyncSetupActions::runDetection(true);
        $lockResult      = $detectionResult['lockResult'];

        $overrideLabels  = [
            'auto'   => __('Auto', 'duplicator'),
            'server' => __('Disabled', 'duplicator'),
            'client' => __('Enabled', 'duplicator'),
            'http'   => __('HTTP', 'duplicator'),
            'https'  => __('HTTPS', 'duplicator'),
            'custom' => __('Custom', 'duplicator'),
        ];
        $dGlobalFresh    = DynamicGlobalEntity::getInstance();
        $loopbackPassed  = $detectionResult['loopbackPass'];
        $clientSideKick  = ClientSideKick::isClientSideKickoffMode();
        $kickoffMismatch = $newKickoffOverride === 'server' && !$loopbackPassed;

        $lockMismatch = !$lockResult['sqlReliable'] && !$lockResult['fileReliable'];

        $result['successMessage'] = TplMng::getInstance()->render(
            'admin_pages/settings/backup/detection_result_message',
            [
                'kickSettingLabel'      => $overrideLabels[$newKickoffOverride],
                'ajaxSettingLabel'      => $overrideLabels[$newAjaxProtocol],
                'basicAuthSettingLabel' => $overrideLabels[$newBasicAuthMode],
                'ajaxUrl'               => ClientSideKick::getBackendAjaxUrl(),
                'sqlLockPassed'         => $lockResult['sqlReliable'],
                'fileLockPassed'        => $lockResult['fileReliable'],
                'clientSideKickoff'     => $clientSideKick,
                'basicAuthConfigured'   => $dGlobalFresh->getBasicAuthHeader() !== null,
                'basicAuthUser'         => $dGlobalFresh->getValString(DynamicGlobalEntity::BASIC_AUTH_USER_KEY),
                'kickoffOverride'       => $newKickoffOverride,
                'kickoffMismatch'       => $kickoffMismatch,
                'lockMismatch'          => $lockMismatch,
            ],
            false
        );

        if (count($rejectedOptions) > 0) {
            $result['errorMessage'] = TplMng::getInstance()->render(
                'parts/settings/adjusted_options_notice',
                ['failures' => $rejectedOptions],
                false
            );
        }

        return $result;
    }

    /**
     * Mysql dump message
     *
     * @param bool   $mysqlDumpFound Found
     * @param string $mysqlDumpPath  mysqldump path
     *
     * @return void
     */
    public static function getMySQLDumpMessage($mysqlDumpFound = false, $mysqlDumpPath = ''): void
    {
        ?>
        <?php if ($mysqlDumpFound) :
            ?>
            <span class="dup-feature-found success-color">
                <?php echo esc_html($mysqlDumpPath) ?> &nbsp;
                <small>
                    <i class="fa fa-check-circle"></i>&nbsp;<i><?php esc_html_e("Successfully Found", 'duplicator'); ?></i>
                </small>
            </span>
            <?php
        else :
            ?>
            <span class="dup-feature-notfound alert-color">
                <i class="fa fa-exclamation-triangle fa-sm" aria-hidden="true"></i>
                <?php
                self::getMySqlDumpPathProblems($mysqlDumpPath, !empty($mysqlDumpPath));
                ?>
            </span>
            <?php
        endif;
    }

    /**
     * Return purge orphan Backups action URL
     *
     * @param bool $on true turn on, false turn off
     *
     * @return string
     */
    public function getTraceActionUrl($on)
    {
        $action = $this->getActionByKey(self::ACTION_GENERAL_TRACE);
        return $this->getMenuLink(
            self::L2_SLUG_GENERAL,
            null,
            [
                'action'        => $action->getKey(),
                '_wpnonce'      => $action->getNonce(),
                '_logging_mode' => ($on ? 'on' : 'off'),
            ]
        );
    }

    /**
     * Display mysql dump path problems
     *
     * @param string $path      mysqldump path
     * @param bool   $is_custom is custom path
     *
     * @return void
     */
    public static function getMySqlDumpPathProblems($path = '', $is_custom = false): void
    {
        $available = WpDbUtils::getMySqlDumpPath();
        $default   = false;
        if ($available) {
            if ($is_custom) {
                if (!Shell::isExecutable($path)) {
                    printf(
                        esc_html_x(
                            'The mysqldump program at custom path exists but is not executable. Please check file permission
                            to resolve this problem. Please check this %1$sFAQ page%2$s for possible solution.',
                            '%1$s and %2$s are html anchor tags or link',
                            'duplicator'
                        ),
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-dependency-checks') . '" target="_blank">',
                        '</a>'
                    );
                } else {
                    $default = true;
                }
            } else {
                if (!Shell::isExecutable($available)) {
                    printf(
                        esc_html_x(
                            'The mysqldump program at its default location exists but is not executable.
                            Please check file permission to resolve this problem. Please check this %1$sFAQ page%2$s for possible solution.',
                            '%1$s and %2$s are html anchor tags or link',
                            'duplicator'
                        ),
                        '<a href="' . esc_url(DUPLICATOR_DUPLICATOR_DOCS_URL . 'how-to-resolve-dependency-checks') . '" target="_blank">',
                        '</a>'
                    );
                } else {
                    $default = true;
                }
            }
        } else {
            if ($is_custom) {
                printf(
                    esc_html_x(
                        'The mysqldump program was not found at its custom path location.
                        Please check is there some typo mistake or mysqldump program exists on that location.
                        Also you can leave custom path empty to force automatic settings. If the problem persists
                        contact your server admin for the correct path. For a list of approved providers that support mysqldump %1$sclick here%2$s.',
                        '%1$s and %2$s are html anchor tags or links',
                        'duplicator'
                    ),
                    '<a href="' . esc_url(DUPLICATOR_BLOG_URL . 'best-wordpress-hosting/') . '" target="_blank">',
                    '</a>'
                );
            } else {
                esc_html_e(
                    'The mysqldump program was not found at its default location.
                    To use mysqldump, ask your host to install it or for a custom mysqldump path.',
                    'duplicator'
                );
            }
        }

        if ($default) {
            printf(
                esc_html_x(
                    'The mysqldump program was not found at its default location or the custom path below.
                    Please enter a valid path where mysqldump can run. If the problem persists contact your
                    server admin for the correct path. For a list of approved providers that support mysqldump %1$sclick here%2$s.',
                    '%1$s and %2$s are html anchor tags or links',
                    'duplicator'
                ),
                '<a href="' . esc_url(DUPLICATOR_BLOG_URL . 'best-wordpress-hosting/') . '" target="_blank">',
                '</a>'
            );
        }
    }
}
