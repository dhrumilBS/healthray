<?php

namespace Duplicator\Installer\Core;

use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\Descriptors\ParamDescConfigs;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Models\MigrateData;
use Duplicator\Installer\Utils\InstallerOrigFileMng;
use Duplicator\Libs\Snap\SnapIO;
use DUPX_ArchiveConfig;
use DUPX_DB;
use DUPX_DB_Functions;
use DUPX_DBInstall;
use DUPX_NOTICE_ITEM;
use DUPX_NOTICE_MANAGER;
use DUPX_Template;
use DUPX_WPConfig;
use Error;
use Exception;

class InstState
{
    /**
     * modes
     */
    const MODE_UNKNOWN     = -1;
    const MODE_STD_INSTALL = 0;
    const MODE_OVR_INSTALL = 1;

    const LOGIC_MODE_IMPORT         = 'IMPORT';
    const LOGIC_MODE_RECOVERY       = 'RECOVERY';
    const LOGIC_MODE_CLASSIC        = 'CLASSIC';
    const LOGIC_MODE_OVERWRITE      = 'OVERWRITE';
    const LOGIC_MODE_BRIDGE         = 'BRIDGE';
    const LOGIC_MODE_RESTORE_BACKUP = 'RESTORE_BACKUP';

    /**
     * install types
     */
    const TYPE_NOT_SET              = -2;
    const TYPE_SINGLE               = -1;
    const TYPE_STANDALONE           = 0;
    const TYPE_MSUBDOMAIN           = 2;
    const TYPE_MSUBFOLDER           = 3;
    const TYPE_SINGLE_ON_SUBDOMAIN  = 4;
    const TYPE_SINGLE_ON_SUBFOLDER  = 5;
    const TYPE_SUBSITE_ON_SUBDOMAIN = 6;
    const TYPE_SUBSITE_ON_SUBFOLDER = 7;
    const TYPE_RBACKUP_SINGLE       = 8;
    const TYPE_RBACKUP_MSUBDOMAIN   = 9;
    const TYPE_RBACKUP_MSUBFOLDER   = 10;
    const TYPE_RECOVERY_SINGLE      = 11;
    const TYPE_RECOVERY_MSUBDOMAIN  = 12;
    const TYPE_RECOVERY_MSUBFOLDER  = 13;

    const SUBSITE_IMPORT_WP_MIN_VERSION = '4.6';

    /** @var int */
    protected $mode = self::MODE_UNKNOWN;
    /** @var string */
    protected $ovr_wp_content_dir = '';
    /** @var ?self */
    private static $instance;

    /**
     * Get instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
    }

    /**
     * return installer mode
     *
     * @return int
     */
    public function getMode()
    {
        return PrmMng::getInstance()->getValue(PrmMng::PARAM_INSTALLER_MODE);
    }

    /**
     * check current installer mode
     *
     * @param bool $onlyIfUnknown check se state only if is unknow state
     * @param bool $saveParams    if true update params
     *
     * @return boolean
     */
    public function checkState($onlyIfUnknown = true, $saveParams = true)
    {
        $paramsManager = PrmMng::getInstance();

        if ($onlyIfUnknown && $paramsManager->getValue(PrmMng::PARAM_INSTALLER_MODE) !== self::MODE_UNKNOWN) {
            return true;
        }
        $isOverwrite   = false;
        $overwriteData = false;
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        try {
            if (self::isImportFromBackendMode() || self::isRecoveryMode()) {
                $overwriteData = $this->getOverwriteDataFromParams();
            } else {
                $overwriteData = $this->getOverwriteDataFromWpConfig();
            }

            if (!empty($overwriteData)) {
                if (!DUPX_DB::testConnection($overwriteData['dbhost'], $overwriteData['dbuser'], $overwriteData['dbpass'], $overwriteData['dbname'])) {
                    throw new Exception('wp-config.php exists but database data connection isn\'t valid. Continuing with standard install');
                }

                $isOverwrite = true;

                if (self::isClassicInstall()) {
                    //Add additional overwrite data for standard installs
                    $overwriteData['adminUsers'] = $this->getAdminUsersOnOverwriteDatabase($overwriteData);
                    $overwriteData['wpVersion']  = $this->getWordPressVersionOverwrite();
                    $this->updateOverwriteDataFromDb($overwriteData);
                }
            }
        } catch (Exception | Error $e) {
            Log::logException($e);
            $longMsg = "Exception message: " . $e->getMessage() . "\n\n";
            $nManager->addNextStepNotice([
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
            ]);
            $nManager->saveNotices();
        }


        if ($isOverwrite) {
            $paramsManager->setValue(PrmMng::PARAM_INSTALLER_MODE, self::MODE_OVR_INSTALL);
            $paramsManager->setValue(PrmMng::PARAM_OVERWRITE_SITE_DATA, $overwriteData);
        } else {
            $paramsManager->setValue(PrmMng::PARAM_INSTALLER_MODE, self::MODE_STD_INSTALL);
        }

        if ($saveParams) {
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * Install type to string
     *
     * @param ?int $type install type Enum InstState::TYPE_*, if null get current install type
     *
     * @return string
     */
    public static function installTypeToString($type = null): string
    {
        if (is_null($type)) {
            $type = self::getInstType();
        }
        switch ($type) {
            case self::TYPE_MSUBDOMAIN:
                return 'multisite subdomain';
            case self::TYPE_MSUBFOLDER:
                return 'multisite subfolder';
            case self::TYPE_STANDALONE:
                return 'standalone subsite';
            case self::TYPE_SUBSITE_ON_SUBDOMAIN:
                return 'subsite on subdomain multisite';
            case self::TYPE_SUBSITE_ON_SUBFOLDER:
                return 'subsite on subfolder multisite';
            case self::TYPE_SINGLE:
                return 'single site';
            case self::TYPE_SINGLE_ON_SUBDOMAIN:
                return 'single site on subdomain multisite';
            case self::TYPE_SINGLE_ON_SUBFOLDER:
                return 'single site on subfolder multisite';
            case self::TYPE_RBACKUP_SINGLE:
                return 'restore single site';
            case self::TYPE_RBACKUP_MSUBDOMAIN:
                return 'restore subdomain multisite';
            case self::TYPE_RBACKUP_MSUBFOLDER:
                return 'restore subfolder multisite';
            case self::TYPE_RECOVERY_SINGLE:
                return 'restore single site';
            case self::TYPE_RECOVERY_MSUBDOMAIN:
                return 'restore subdomain multisite';
            case self::TYPE_RECOVERY_MSUBFOLDER:
                return 'restore subfolder multisite';
            case self::TYPE_NOT_SET:
                return 'NOT SET';
            default:
                throw new Exception('Invalid installer mode');
        }
    }

    /**
     * Overwrite data default values
     *
     * @return array<string, mixed>
     */
    public static function overwriteDataDefault(): array
    {
        return [
            'dupVersion'          => '0',
            'wpVersion'           => '0',
            'dbhost'              => '',
            'dbname'              => '',
            'dbuser'              => '',
            'dbpass'              => '',
            'table_prefix'        => '',
            'restUrl'             => '',
            'restNonce'           => '',
            'restAuthUser'        => '',
            'restAuthPassword'    => '',
            'ustatIdentifier'     => '',
            'isMultisite'         => false,
            'subdomain'           => false,
            'subsites'            => [],
            'nextSubsiteIdAI'     => -1,
            'adminUsers'          => [],
            'paths'               => [],
            'urls'                => [],
            'dupLicense'          => 0,
            'loggedUser'          => [
                'ID'         => -1,
                'user_login' => '',
            ],
            'packagesTableExists' => false,
            'removeFilters'       => [
                'dirs'  => [],
                'files' => [],
            ],
        ];
    }

    /**
     * Get overwrite data from params
     *
     * @return false|array<string, mixed>
     */
    protected function getOverwriteDataFromParams()
    {
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        if (empty($overwriteData)) {
            return false;
        }

        if (!isset($overwriteData['dbhost']) || !isset($overwriteData['dbname']) || !isset($overwriteData['dbuser']) || !isset($overwriteData['dbpass'])) {
            return false;
        }

        return array_merge(self::overwriteDataDefault(), $overwriteData);
    }

    /**
     * Get overwrite data from wp-config.php
     *
     * @return false|array<string, mixed>
     */
    protected function getOverwriteDataFromWpConfig()
    {
        if (($wpConfigPath = ServerConfigs::getWpConfigLocalStoredPath()) === false) {
            $wpConfigPath = DUPX_WPConfig::getWpConfigPath();
            if (!file_exists($wpConfigPath)) {
                $wpConfigPath = DUPX_WPConfig::getWpConfigDeafultPath();
            }
        }

        $overwriteData = false;

        Log::info('CHECK STATE INSTALLER WP CONFIG PATH: ' . Log::v2str($wpConfigPath), Log::LV_DETAILED);

        if (!file_exists($wpConfigPath)) {
            return $overwriteData;
        }

        $nManager = DUPX_NOTICE_MANAGER::getInstance();
        try {
            if (DUPX_WPConfig::getLocalConfigTransformer() === false) {
                throw new Exception('wp-config.php exist but isn\'t valid. continue on standard install');
            }

            $overwriteData = array_merge(
                self::overwriteDataDefault(),
                [
                    'dbhost'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_HOST'),
                    'dbname'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_NAME'),
                    'dbuser'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_USER'),
                    'dbpass'       => DUPX_WPConfig::getValueFromLocalWpConfig('DB_PASSWORD'),
                    'table_prefix' => DUPX_WPConfig::getValueFromLocalWpConfig('table_prefix', 'variable'),
                ]
            );

            if (DUPX_WPConfig::getValueFromLocalWpConfig('MULTISITE', 'constant', false)) {
                $overwriteData['isMultisite'] = true;
                $overwriteData['subdomain']   = DUPX_WPConfig::getValueFromLocalWpConfig('SUBDOMAIN_INSTALL', 'constant', false);
            }
        } catch (Exception | Error $e) {
            $overwriteData = false;
            Log::logException($e);
            $longMsg = "Exception message: " . $e->getMessage() . "\n\n";
            $nManager->addNextStepNotice([
                'shortMsg'    => 'wp-config.php exists but isn\'t valid. Continue on standard install.',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
            ]);
            $nManager->saveNotices();
        }

        return $overwriteData;
    }

    /**
     * Check if is bridge install
     *
     * @return bool
     */
    public static function isBridgeInstall(): bool
    {
        return defined('DUPLICATOR_MU_PLUGIN_VERSION');
    }

    /**
     * Check if is recovery mode
     *
     * @param ?int $type install type Enum InstState::TYPE_*, if null get current install type
     *
     * @return bool
     */
    public static function isRecoveryMode($type = null): bool
    {
        return self::isInstType(
            [
                self::TYPE_RECOVERY_SINGLE,
                self::TYPE_RECOVERY_MSUBDOMAIN,
                self::TYPE_RECOVERY_MSUBFOLDER,
            ],
            $type
        );
    }

    /**
     * Check if is restore backup
     *
     * @param ?int $type install type Enum InstState::TYPE_*, if null get current install type
     *
     * @return bool
     */
    public static function isRestoreBackup($type = null): bool
    {
        return self::isInstType(
            [
                self::TYPE_RBACKUP_SINGLE,
                self::TYPE_RBACKUP_MSUBDOMAIN,
                self::TYPE_RBACKUP_MSUBFOLDER,
                self::TYPE_RECOVERY_SINGLE,
                self::TYPE_RECOVERY_MSUBDOMAIN,
                self::TYPE_RECOVERY_MSUBFOLDER,
            ],
            $type
        );
    }

    /**
     * Check if is import from backend mode
     *
     * @return bool
     */
    public static function isImportFromBackendMode(): bool
    {
        $template = PrmMng::getInstance()->getValue(PrmMng::PARAM_TEMPLATE);
        return ($template === DUPX_Template::TEMPLATE_IMPORT_BASE ||
            $template === DUPX_Template::TEMPLATE_IMPORT_ADVANCED);
    }

    /**
     * Check if is classic install (non import from backend)
     *
     * @return bool
     */
    public static function isClassicInstall(): bool
    {
        return (!self::isImportFromBackendMode() && !self::isRecoveryMode());
    }


    /**
     * Return true if new target site is multisite
     *
     * @return bool
     */
    public static function isNewSiteIsMultisite(): bool
    {
        return self::isInstType(
            [
                InstState::TYPE_MSUBDOMAIN,
                InstState::TYPE_MSUBFOLDER,
                InstState::TYPE_RBACKUP_MSUBDOMAIN,
                InstState::TYPE_RBACKUP_MSUBFOLDER,
                InstState::TYPE_RECOVERY_MSUBDOMAIN,
                InstState::TYPE_RECOVERY_MSUBFOLDER,
            ]
        );
    }

    /**
     * Check is install type is add site on multisite
     *
     * @param ?int $type install type Enum InstState::TYPE_*, if null get current install type
     *
     * @return bool
     */
    public static function isAddSiteOnMultisite($type = null): bool
    {
        return self::isInstType(
            [
                self::TYPE_SINGLE_ON_SUBDOMAIN,
                self::TYPE_SINGLE_ON_SUBFOLDER,
                self::TYPE_SUBSITE_ON_SUBDOMAIN,
                self::TYPE_SUBSITE_ON_SUBFOLDER,
            ],
            $type
        );
    }

    /**
     * Check if is multisite install
     *
     * @param ?int $type install type Enum InstState::TYPE_*, if null get current install type
     *
     * @return bool
     */
    public static function isMultisiteInstall($type = null): bool
    {
        return self::isInstType(
            [
                self::TYPE_MSUBDOMAIN,
                self::TYPE_MSUBFOLDER,
            ],
            $type
        );
    }

    /**
     * Check if install type is available
     *
     * @param int|int[] $type install type Enum InstState::TYPE_*
     *
     * @return bool
     */
    public static function instTypeAvailable($type): bool
    {
        $acceptList      = ParamDescConfigs::getInstallTypesAcceptValues();
        $typesToCheck    = is_array($type) ? $type : [$type];
        $typesAvaliables = array_intersect($acceptList, $typesToCheck);
        return (count($typesAvaliables) > 0);
    }

    /**
     * Return true if add site on multisite install is available
     *
     * @return bool
     */
    public static function isAddSiteOnMultisiteAvailable(): bool
    {
        return self::instTypeAvailable(
            [
                self::TYPE_SINGLE_ON_SUBDOMAIN,
                self::TYPE_SINGLE_ON_SUBFOLDER,
                self::TYPE_SUBSITE_ON_SUBDOMAIN,
                self::TYPE_SUBSITE_ON_SUBFOLDER,
            ]
        );
    }

    /**
     * Return true if multisite install is available
     *
     * @return bool
     */
    public static function isMultisiteInstallAvailable(): bool
    {
        return self::instTypeAvailable(
            [
                self::TYPE_MSUBDOMAIN,
                self::TYPE_MSUBFOLDER,
            ]
        );
    }

    /**
     * This function in case of an error returns an empty array but never generates exceptions
     *
     * @param array<string, mixed> $overwriteData Overwrite data
     *
     * @return array<string, mixed>
     */
    protected function getAdminUsersOnOverwriteDatabase($overwriteData)
    {
        $adminUsers = [];
        $dbFuncs    = null;
        try {
            $dbFuncs = DUPX_DB_Functions::getInstance();

            if (!$dbFuncs->dbConnection($overwriteData)) {
                throw new Exception('GET USERS ON CURRENT DATABASE FAILED. Can\'t connect');
            }

            $usersTables = [
                $dbFuncs->getUserTableName($overwriteData['table_prefix']),
                $dbFuncs->getUserMetaTableName($overwriteData['table_prefix']),
            ];

            if (!$dbFuncs->tablesExist($usersTables)) {
                throw new Exception(
                    'GET USERS ON CURRENT DATABASE FAILED. Users tables doesn\'t exist, ' .
                        'continue with orverwrite installation but with option keep users disabled' . "\n"
                );
            }

            if (($adminUsers = $dbFuncs->getAdminUsers($overwriteData['table_prefix'])) === false) {
                $adminUsers = [];
                throw new Exception('GET USERS ON CURRENT DATABASE FAILED. OVERWRITE DB USERS NOT FOUND');
            }
        } catch (Exception | Error $e) {
            Log::logException($e, Log::LV_DEFAULT, 'GET ADMIN USER EXECPTION BUT CONTINUE');
        } finally {
            if ($dbFuncs instanceof DUPX_DB_Functions) {
                $dbFuncs->closeDbConnection();
            }
        }

        return $adminUsers;
    }

    /**
     * Returns the WP version from the ./wp-includes/version.php file if it exists, otherwise '0'
     *
     * @return string WP version
     */
    protected function getWordPressVersionOverwrite(): string
    {
        $wp_version = '0';
        try {
            $versionFilePath = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW) . "/wp-includes/version.php";
            if (!file_exists($versionFilePath) || !is_readable($versionFilePath)) {
                Log::info("WordPress Version file does not exist or is not readable at path: {$versionFilePath}");
                return $wp_version;
            }

            include($versionFilePath);
            return $wp_version;
        } catch (Exception $e) {
            Log::logException($e, Log::LV_DEFAULT, 'EXCEPTION GETTING WordPress VERSION, BUT CONTINUE');
        } catch (Error $e) {
            Log::logException($e, Log::LV_DEFAULT, 'ERROR GETTING WordPress VERSION, BUT CONTINUE');
        }

        return $wp_version;
    }

    /**
     * Populate overwrite data with info read from the target DB (Duplicator version, ustat id, packages table presence)
     *
     * @param array<string, mixed> $overwriteData Overwrite data
     *
     * @return bool True on success, false on failure
     */
    protected function updateOverwriteDataFromDb(&$overwriteData): bool
    {
        $dbFuncs = null;
        try {
            $dbFuncs = DUPX_DB_Functions::getInstance();

            if (!$dbFuncs->dbConnection($overwriteData)) {
                throw new Exception('OVERWRITE DATA ERROR: Can\'t connect');
            }

            $optionsTable = DUPX_DB_Functions::getOptionsTableName($overwriteData['table_prefix']);

            if (!$dbFuncs->tablesExist($optionsTable)) {
                throw new Exception("OVERWRITE DATA ERROR: Options tables doesn't exist.\n");
            }

            $duplicatorVersion = $dbFuncs->getDuplicatorVersion($overwriteData['table_prefix']);

            $overwriteData['dupVersion']          = (empty($duplicatorVersion) ? '0' : $duplicatorVersion);
            $overwriteData['ustatIdentifier']     = $dbFuncs->getUniqueId($overwriteData['table_prefix']);
            $overwriteData['packagesTableExists'] = $dbFuncs->tablesExist($dbFuncs->getPackagesTableName($overwriteData['table_prefix']));
        } catch (Exception $e) {
            Log::logException($e, Log::LV_DEFAULT, 'GET DUPLICATOR VERSION EXECPTION BUT CONTINUE');
            return false;
        } catch (Error $e) {
            Log::logException($e, Log::LV_DEFAULT, 'GET DUPLICATOR VERSION ERROR BUT CONTINUE');
            return false;
        } finally {
            if ($dbFuncs instanceof DUPX_DB_Functions) {
                $dbFuncs->closeDbConnection();
            }
        }
        return true;
    }

    /**
     * getHtmlModeHeader
     *
     * @return string
     */
    public function getHtmlModeHeader(): string
    {
        $additional_info  = '<span class="requires-no-db"> - No database actions';
        $additional_info .= DUPX_ArchiveConfig::getInstance()->isDBExcluded() ? ' (Database Excluded)' : '';
        $additional_info .= '</span>';

        $additional_info .= (DUPX_ArchiveConfig::getInstance()->isDBOnly()) ? ' - Database Only' : '';
        $additional_info .= ($GLOBALS['DUPX_ENFORCE_PHP_INI']) ? '<i style="color:red"><br/>*PHP ini enforced*</i>' : '';

        switch ($this->getMode()) {
            case self::MODE_OVR_INSTALL:
                $label = 'Overwrite Install';
                $class = 'dupx-overwrite mode_overwrite';
                break;
            case self::MODE_STD_INSTALL:
                $label = 'Standard Install';
                $class = 'dupx-overwrite mode_standard';
                break;
            case self::MODE_UNKNOWN:
            default:
                $label = 'Custom Install';
                $class = 'mode_unknown';
                break;
        }

        if (strlen($additional_info)) {
            return '<span class="' . $class . '">' . $label . ' ' . $additional_info . '</span>';
        } else {
            return "<span class=\"{$class}\">{$label}</span>";
        }
    }

    /**
     * reset current mode
     *
     * @param boolean $saveParams save params
     *
     * @return boolean
     */
    public function resetState($saveParams = true)
    {
        $paramsManager = PrmMng::getInstance();
        $paramsManager->setValue(PrmMng::PARAM_INSTALLER_MODE, self::MODE_UNKNOWN);
        if ($saveParams) {
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * Save current installer state
     *
     * @return bool
     */
    public function save()
    {
        return PrmMng::getInstance()->save();
    }

    /**
     * Return stru if is overwrite install
     *
     * @return boolean
     */
    public static function isOverwrite(): bool
    {
        return (PrmMng::getInstance()->getValue(PrmMng::PARAM_INSTALLER_MODE) === self::MODE_OVR_INSTALL);
    }

    /**
     * Returns true if the DB action is set to do nothing
     *
     * @return bool
     */
    public static function dbDoNothing(): bool
    {
        return PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_ACTION) === DUPX_DBInstall::DBACTION_DO_NOTHING;
    }

    /**
     * Returns true if the installer is running in staging mode
     *
     * @return bool
     */
    public static function isStagingMode(): bool
    {
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        return !empty($overwriteData['stagingMode']);
    }

    /**
     * this function returns true if both the URL and path old and new path are identical
     *
     * @return bool
     */
    public static function isInstallerCreatedInThisLocation(): bool
    {
        $paramsManager = PrmMng::getInstance();

        $urlNew  = null;
        $pathNew = null;

        if (InstState::isImportFromBackendMode()) {
            $overwriteData = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
            if (isset($overwriteData['urls']['home']) && isset($overwriteData['paths']['home'])) {
                $urlNew  = $overwriteData['urls']['home'];
                $pathNew = $overwriteData['paths']['home'];
            }
        }

        if (is_null($urlNew) || is_null($pathNew)) {
            $pathNew = $paramsManager->getValue(PrmMng::PARAM_PATH_NEW);
            $urlNew  = $paramsManager->getValue(PrmMng::PARAM_URL_NEW);
        }

        return self::urlAndPathAreSameOfArchive($urlNew, $pathNew);
    }

    /**
     * Check if the url and path are the same of the archive
     *
     * @param string $urlNew  new url
     * @param string $pathNew new path
     *
     * @return bool
     */
    public static function urlAndPathAreSameOfArchive($urlNew, $pathNew): bool
    {
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();
        $urlOld        = rtrim($archiveConfig->getRealValue('homeUrl'), '/');
        $paths         = $archiveConfig->getRealValue('archivePaths');
        $pathOld       = $paths->home;
        $paths         = $archiveConfig->getRealValue('originalPaths');
        $pathOldOrig   = $paths->home;

        $urlNew      = SnapIO::untrailingslashit($urlNew);
        $urlOld      = SnapIO::untrailingslashit($urlOld);
        $pathNew     = SnapIO::untrailingslashit($pathNew);
        $pathOld     = SnapIO::untrailingslashit($pathOld);
        $pathOldOrig = SnapIO::untrailingslashit($pathOldOrig);

        return (($pathNew === $pathOld || $pathNew === $pathOldOrig) && $urlNew === $urlOld);
    }

    /**
     * Get migration data to store in wp-options
     *
     * @return MigrateData
     */
    public static function getMigrationData(): MigrateData
    {
        $sec           = Security::getInstance();
        $paramsManager = PrmMng::getInstance();

        $result        = new MigrateData();
        $ac            = DUPX_ArchiveConfig::getInstance();
        $overwriteData = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

        $result->installerVersion    = DUPX_VERSION;
        $result->installType         = $paramsManager->getValue(PrmMng::PARAM_INST_TYPE);
        $result->installTime         = gmdate("Y-m-d H:i:s");
        $result->logicModes          = self::getLogicModes();
        $result->template            = PrmMng::getInstance()->getValue(PrmMng::PARAM_TEMPLATE);
        $result->restoreBackupMode   = self::isRestoreBackup();
        $result->recoveryMode        = self::isRecoveryMode();
        $result->archivePath         = $sec->getArchivePath();
        $result->packageId           = is_numeric($ac->packInfo->packageId) ? (int) $ac->packInfo->packageId : 0;
        $result->packageHash         = $ac->packInfo->packageHash;
        $result->installerPath       = $sec->getBootFilePath();
        $result->installerBootLog    = $sec->getBootLogFile();
        $result->installerLog        = Log::getLogFilePath();
        $result->dupInstallerPath    = DUPX_INIT;
        $result->origFileFolderPath  = InstallerOrigFileMng::getInstance()->getMainFolder();
        $result->safeMode            = $paramsManager->getValue(PrmMng::PARAM_SAFE_MODE);
        $result->cleanInstallerFiles = $paramsManager->getValue(PrmMng::PARAM_AUTO_CLEAN_INSTALLER_FILES);
        $result->licenseType         = $ac->license_type;
        $result->phpVersion          = $ac->version_php;
        $result->archiveType         = $ac->isZipArchive() ? 'zip' : 'dup';
        $result->siteSize            = $ac->fileInfo->size;
        $result->siteNumFiles        = ($ac->fileInfo->dirCount + $ac->fileInfo->fileCount);
        $result->siteDbSize          = $ac->dbInfo->tablesSizeOnDisk;
        $result->siteDBNumTables     = $ac->dbInfo->tablesFinalCount;
        $result->components          = $ac->components;
        $result->ustatIdentifier     = $overwriteData['ustatIdentifier'];

        /**
         * Filter to allow addons to add extra data to migration data.
         *
         * @param array<string, mixed> $extra         Extra data array
         * @param array<string, mixed> $overwriteData Overwrite site data from params
         */
        $result->extra = apply_filters('duplicator_installer_migrate_data_extra', [], $overwriteData);

        return $result;
    }

    /**
     * Get admin login url
     *
     * @return string
     */
    public static function getAdminLogin()
    {
        $paramsManager = PrmMng::getInstance();
        if (self::isAddSiteOnMultisite()) {
            $overwriteData = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
            $loginUrl      = $overwriteData['urls']['login'];
        } else {
            $oldUrl        = $paramsManager->getValue(PrmMng::PARAM_SITE_URL_OLD);
            $newUrl        = $paramsManager->getValue(PrmMng::PARAM_SITE_URL);
            $archiveConfig = DUPX_ArchiveConfig::getInstance();
            $loginUrl      = DUPX_ArchiveConfig::getNewSubUrl($oldUrl, $newUrl, $archiveConfig->getRealValue("loginUrl"));
        }
        return $loginUrl;
    }

    /**
     * Get install type
     *
     * @return int install type Enum self::TYPE_*
     */
    public static function getInstType()
    {
        return PrmMng::getInstance()->getValue(PrmMng::PARAM_INST_TYPE);
    }

    /**
     * @param int|int[] $type        list of types to check
     * @param int       $typeToCheck if is null get param install time or check this
     *
     * @return bool
     */
    public static function isInstType($type, $typeToCheck = null): bool
    {
        $currentType = $typeToCheck ?? self::getInstType();
        if (is_array($type)) {
            return in_array($currentType, $type);
        } else {
            return $currentType === $type;
        }
    }

    /**
     * Get install logic modes
     *
     * @return string[]
     */
    public static function getLogicModes(): array
    {
        $modes = [];
        if (self::isImportFromBackendMode()) {
            $modes[] = self::LOGIC_MODE_IMPORT;
        }
        if (self::isRecoveryMode()) {
            $modes[] = self::LOGIC_MODE_RECOVERY;
        }
        if (self::isClassicInstall()) {
            $modes[] = self::LOGIC_MODE_CLASSIC;
        }
        if (self::isOverwrite()) {
            $modes[] = self::LOGIC_MODE_OVERWRITE;
        }
        if (self::isBridgeInstall()) {
            $modes[] = self::LOGIC_MODE_BRIDGE;
        }
        if (self::isRestoreBackup()) {
            $modes[] = self::LOGIC_MODE_RESTORE_BACKUP;
        }
        return $modes;
    }
}
