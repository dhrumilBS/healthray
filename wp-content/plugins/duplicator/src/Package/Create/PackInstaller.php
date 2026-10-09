<?php

namespace Duplicator\Package\Create;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Models\GlobalEntity;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Installer\Package\DescriptorFileInfo;
use Duplicator\Installer\Package\DescriptorPackageInfo;
use Duplicator\Installer\Package\DescriptorPlugin;
use Duplicator\Installer\Package\DescriptorTheme;
use Duplicator\Installer\Package\DescriptorWpInfo;
use Duplicator\Installer\Package\InstallerDescriptors;
use Duplicator\Libs\DupArchive\DupArchiveEngine;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Shell\Shell;
use Duplicator\Libs\Snap\SnapCode;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapOrigFileManager;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpConfig\WPConfigTransformer;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Create\BuildProgress;
use Duplicator\Libs\Scan\ScanIterator;
use Duplicator\Libs\Scan\ScanNodeInfo;
use Duplicator\Libs\Shell\ShellZipUtils;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\WpUtils\WpUtilsMultisite;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\PackageEnvironmentSnapshot;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Utils\ZipArchiveExtended;
use Duplicator\Utils\ZipVerifier;
use Exception;
use stdClass;
use Throwable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use VendorDuplicator\Amk\JsonSerialize\AbstractJsonSerializable;

/**
 * Classes for building the Backup installer extra files
 */
class PackInstaller extends AbstractJsonSerializable
{
    const INSTALLER_SERVER_EXTENSION                      = '.php.bak';
    const DEFAULT_INSTALLER_FILE_NAME_WITHOUT_HASH        = 'installer.php';
    const DEFAULT_INSTALLER_BACKUP_FILE_NAME_WITHOUT_HASH = 'installer-backup.php';
    const CONFIG_ORIG_FILE_FOLDER_PREFIX                  = 'source_site_';
    const CONFIG_ORIG_FILE_USERINI_ID                     = 'userini';
    const CONFIG_ORIG_FILE_HTACCESS_ID                    = 'htaccess';
    const CONFIG_ORIG_FILE_WPCONFIG_ID                    = 'wpconfig';
    const CONFIG_ORIG_FILE_PHPINI_ID                      = 'phpini';
    const CONFIG_ORIG_FILE_WEBCONFIG_ID                   = 'webconfig';

    protected ?string $File;
    /** @var int<0,max> */
    public $Size = 0;
    //SETUP
    /** @var int ENUM ArchiveDescriptor::SECURE_MODE_* */
    public $OptsSecureOn = ArchiveDescriptor::SECURE_MODE_NONE;
    /** @var string */
    public $passowrd = '';
    /** @var string */
    public $OptsSecurePass = ''; // Old installer password managed before 4.5.3,
    /** @var bool */
    public $OptsSkipScan = false;
    //BASIC
    /** @var string */
    public $OptsDBHost = '';
    /** @var string */
    public $OptsDBName = '';
    /** @var string */
    public $OptsDBUser = '';
    //CPANEL
    /** @var string */
    public $OptsCPNLHost = '';
    /** @var string */
    public $OptsCPNLUser = '';
    /** @var string */
    public $OptsCPNLPass = '';
    /** @var bool */
    public $OptsCPNLEnable = false;
    /** @var bool */
    public $OptsCPNLConnect = false;
    //CPANEL DB
    //1 = Create New, 2 = Connect Remove
    /** @var string */
    public $OptsCPNLDBAction = 'create';
    /** @var string */
    public $OptsCPNLDBHost = '';
    /** @var string */
    public $OptsCPNLDBName = '';
    /** @var string */
    public $OptsCPNLDBUser = '';

    /** @var SnapOrigFileManager */
    protected $origFileManger;
    protected \Duplicator\Package\AbstractPackage $Package;
    /** @var int<0,max> */
    public $numFilesAdded = 0;
    /** @var int<0,max> */
    public $numDirsAdded = 0;
    /** @var ?WPConfigTransformer */
    private $configTransformer;

    /**
     * CLass constructor
     *
     * @param AbstractPackage $package Backup
     */
    public function __construct(AbstractPackage $package)
    {
        $this->Package = $package;
        $this->File    = $package->getNameHash() . '_' . self::DEFAULT_INSTALLER_FILE_NAME_WITHOUT_HASH;
        $this->loadInit();
    }

    /**
     * Init after load
     *
     * @return void
     */
    protected function loadInit()
    {
        $this->origFileManger = new SnapOrigFileManager(
            WpArchiveUtils::getArchiveListPaths('home'),
            DUPLICATOR_SSDIR_PATH_TMP,
            $this->getPrimaryInternalHash()
        );

        if (($wpConfigPath = SnapWP::getWPConfigPath()) !== false) {
            $this->configTransformer = new WPConfigTransformer($wpConfigPath);
        }
    }

    /**
     * Get internal hash by installer file name
     *
     * @return string
     */
    protected function getPrimaryInternalHash(): string
    {
        if (($archiveInfo = ArchiveDescriptor::getArchiveNameParts($this->File)) === false) {
            throw new DupliException("Can't get archive info from filename: {$this->File}");
        }
        return $archiveInfo['packageHash'];
    }

    /**
     * Return serialize data for json encode
     *
     * @return array<string,mixed>
     */
    public function __serialize() // phpcs:ignore PHPCompatibility.FunctionNameRestrictions.NewMagicMethods.__serializeFound
    {
        $data = get_object_vars($this);
        foreach (['origFileManger', 'Package', 'configTransformer'] as $removeProp) {
            unset($data[$removeProp]);
        }
        $data['OptsSecurePass'] = ''; // empty old password
        $data['passowrd']       = CryptBlowfish::encryptIfAvaiable($data['passowrd'], null, true);

        return $data;
    }

    /**
     * Called after json decode
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->loadInit();

        if (strlen($this->OptsSecurePass) > 0) {
            $this->passowrd = base64_decode($this->OptsSecurePass);
        } elseif (strlen($this->passowrd) > 0) {
            $this->passowrd = CryptBlowfish::decryptIfAvaiable($this->passowrd, null, true);
        }

        $this->OptsSecurePass = '';
    }

    /**
     * Returns real and normalized path to the saved installer file at default backup location
     *
     * @return string
     */
    public function getSafeFilePath()
    {
        return SnapIO::safePath(DUPLICATOR_SSDIR_PATH . "/" . $this->getInstallerLocalName());
    }

    /**
     * Return local fil name
     *
     * @return string
     */
    public function getInstallerLocalName()
    {
        return pathinfo($this->File, PATHINFO_FILENAME) . self::INSTALLER_SERVER_EXTENSION;
    }

    /**
     * Get the installer file name
     *
     * @return string
     */
    public function getInstallerName()
    {
        return $this->File;
    }

    /**
     * Get the download name based on installer name mode setting
     *
     * @return string
     */
    public function getDownloadName()
    {
        $global = GlobalEntity::getInstance();

        switch ($global->getInstallerNameMode()) {
            case GlobalEntity::INSTALLER_NAME_MODE_SIMPLE:
                return self::DEFAULT_INSTALLER_FILE_NAME_WITHOUT_HASH;
            case GlobalEntity::INSTALLER_NAME_MODE_WITH_HASH:
            default:
                $info = pathinfo($this->getInstallerName());
                return $info['basename'];
        }
    }

    /**
     * Return true if a installer security system is enabled
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->OptsSecureOn != ArchiveDescriptor::SECURE_MODE_NONE;
    }

    /**
     * Build
     *
     * @param BuildProgress $build_progress Build progress
     *
     * @return void
     */
    public function build(BuildProgress $build_progress): void
    {
        DupLog::trace("building installer");
        $this->createEnhancedInstaller();
        $this->createArchiveConfigFile();
        $this->addExtraFiles();

        $build_progress->installer_built = true;
    }

    /**
     * Create installer.php file
     *
     * @return void
     */
    private function createEnhancedInstaller(): void
    {
        $archive_filepath   = SnapIO::safePath("{$this->Package->StorePath}/{$this->Package->Archive->getFileName()}");
        $installer_filepath = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/" . $this->getInstallerLocalName();
        $template_filepath  = DUPLICATOR____PATH . '/installer/installer.tpl';
        $header             = <<<HEADER
<?php
/* ------------------------------ NOTICE ----------------------------------

If you're seeing this text when browsing to the installer, it means your
web server is not set up properly.

Please contact your host and ask them to enable "PHP" processing on your
account.
----------------------------- NOTICE --------------------------------- */
HEADER;
        $installer_contents = $header . SnapCode::getSrcClassCode($template_filepath, false, true) . "\n/* " . DUPLICATOR_INSTALLER_EOF_MARKER . " */";

        $dupExpanderCoder  = '';
        $bootPath          = DUPLICATOR____PATH . '/installer/dup-installer/src/Bootstrap/';
        $dupExpanderCoder .= SnapCode::getSrcClassCode($bootPath . 'BootstrapRunner.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode(DUPLICATOR____PATH . '/src/Libs/Shell/Shell.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode(DUPLICATOR____PATH . '/src/Libs/Shell/ShellOutput.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode($bootPath . 'BootstrapUtils.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode($bootPath . 'BootstrapView.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode($bootPath . 'LogHandler.php') . "\n";
        $dupExpanderCoder .= SnapCode::getSrcClassCode(DUPLICATOR____PATH . '/installer/dup-installer/src/Utils/SecureCsrf.php') . "\n";

        if ($this->Package->requireBuildOptions()->getArchiveEngine() == PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
            $dupLib            = DUPLICATOR____PATH . '/src/Libs/DupArchive/';
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'DupArchive.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'DupArchiveExpandBasicEngine.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Headers/AbstractDupArchiveHeader.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Headers/DupArchiveDirectoryHeader.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Headers/DupArchiveFileHeader.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Headers/DupArchiveGlobHeader.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Headers/DupArchiveHeader.php') . "\n";
            $dupExpanderCoder .= SnapCode::getSrcClassCode($dupLib . 'Info/DupArchiveExpanderInfo.php') . "\n";
        }

        $search_array           = [
            '#@@DUP_INSTALLER_CLASSES_EXPANDER@@#',
            '@@ARCHIVE@@',
            '@@VERSION@@',
            '@@ARCHIVE_SIZE@@',
            '@@PACKAGE_HASH@@',
            '@@SECONDARY_PACKAGE_HASH@@',
        ];
        $package_hash           = $this->Package->getPrimaryInternalHash();
        $secondary_package_hash = $this->Package->getSecondaryInternalHash();
        $replace_array          = [
            $dupExpanderCoder,
            (string) $this->Package->Archive->getFileName(),
            DUPLICATOR_VERSION,
            (string) SnapIO::filesize($archive_filepath),
            $package_hash,
            $secondary_package_hash,
        ];

        $installer_contents = str_replace($search_array, $replace_array, $installer_contents);
        $expectedLen        = strlen($installer_contents);

        error_clear_last();
        $written = file_put_contents($installer_filepath, $installer_contents);

        if ($written === false || $written < $expectedLen) {
            $error  = error_get_last();
            $reason = $error !== null ? $error['message'] : 'unknown';
            DupLog::error("Installer file write failed", "Path: {$installer_filepath}, reason: {$reason}");
            self::throwWriteException('installer file', $reason);
        }

        $this->Size = SnapIO::filesize($installer_filepath);
    }

    /**
     * Create archive.txt file
     *
     * @return void
     */
    protected function createArchiveConfigFile(): void
    {
        global $wpdb;

        // Build the descriptor on the network main site so current-blog reads (blogname)
        // reflect the network, not the subsite a scheduled build ran on.
        $switched = is_multisite() && !is_main_site() && switch_to_blog(get_main_site_id());
        try {
            $global                  = GlobalEntity::getInstance();
            $archive_config_filepath = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/{$this->Package->getNameHash()}_archive.txt";
            $ac                      = new ArchiveDescriptor();
            $extension               = strtolower($this->Package->Archive->Format);

            //READ-ONLY: COMPARE VALUES
            $ac->created                 = $this->Package->getCreated();
            $ac->version_dup             = DUPLICATOR_VERSION;
            $ac->version_wp              = $this->Package->VersionWP;
            $ac->version_db              = $this->Package->VersionDB;
            $ac->version_php             = $this->Package->VersionPHP;
            $ac->version_os              = $this->Package->VersionOS;
            $ac->dbInfo                  = $this->Package->Database->info->cloneToArchiveDbInfo();
            $ac->packInfo                = new DescriptorPackageInfo();
            $ac->packInfo->packageId     = $this->Package->getId();
            $ac->packInfo->packageName   = $this->Package->getName();
            $ac->packInfo->packageHash   = $this->Package->getPrimaryInternalHash();
            $ac->packInfo->secondaryHash = $this->Package->getSecondaryInternalHash();
            $ac->fileInfo                = new DescriptorFileInfo();
            $ac->fileInfo->fileCount     = $this->Package->Archive->FileCount;
            $ac->fileInfo->dirCount      = $this->Package->Archive->DirCount;
            $ac->fileInfo->size          = $this->Package->Archive->Size;
            $ac->wpInfo                  = $this->getWpInfo();
            $this->Package->setEnvironmentSnapshot(
                PackageEnvironmentSnapshot::fromWpInfo(
                    $ac->wpInfo,
                    $ac->wpInfo->is_multisite ? null : get_stylesheet()
                )
            );

            //READ-ONLY: GENERAL
            $ac->installer_backup_name = $this->getInstallerBackupName();
            $ac->package_name          = "{$this->Package->getNameHash()}_archive.{$extension}";
            $ac->package_hash          = $this->Package->getPrimaryInternalHash();
            $ac->package_notes         = $this->Package->notes;
            $ac->opts_delete           = DUPLICATOR_OPTS_DELETE;
            $ac->blogname              = sanitize_text_field(get_option('blogname'));
            $ac->defaultStorageId      = StoragesUtil::getDefaultStorageId();
            $ac->exportOnlyDB          = $this->Package->isDBOnly();
            $ac->components            = $this->Package->components;
            $ac->dup_type              = strtolower(DUPLICATOR____TYPE);
            $ac->forceActivatePlugins  = $this->getForceActivatePlugins();

            //PRE-FILLED: GENERAL
            $ac->secure_on   = $this->OptsSecureOn;
            $ac->secure_pass = $ac->secure_on ? Security::passwordHash($this->passowrd) : '';

            $ac->mu_mode        = SnapWP::getMode();
            $ac->wp_tableprefix = $wpdb->base_prefix;
            $ac->mu_generation  = SnapWP::getMuGeneration();
            $ac->mu_is_filtered = !empty($this->Package->Multisite->FilterSites);
            $ac->mu_siteadmins  = array_values(get_super_admins());
            $filteredTables     = ($this->Package->Database->FilterOn ? explode(',', $this->Package->Database->FilterTables) : []);
            $ac->subsites       = WpUtilsMultisite::getSubsites(
                $this->Package->Multisite->FilterSites,
                $filteredTables,
                $this->Package->Archive->FilterInfo->Dirs->Instance
            );
            $ac->main_site_id   = get_main_site_id();
            $ac->header         = [
                'name'      => DUPLICATOR____NAME,
                'isDefault' => true,
                'logo'      => '',
                'enabled'   => false,
                'style'     => [],
            ];

            //LICENSING
            $ac->license_type = 0;

            // OVERWRITE PARAMS
            $ac->overwriteInstallerParams = apply_filters('duplicator_overwrite_params_data', $this->getPrefillParams());

            $ac   = apply_filters('duplicator_archive_config', $ac, $this->Package);
            $json = JsonSerialize::serialize($ac, JSON_PRETTY_PRINT);

            $expectedLen = strlen($json);
            error_clear_last();
            $written = file_put_contents($archive_config_filepath, $json);

            if ($written === false || $written < $expectedLen) {
                $error  = error_get_last();
                $reason = $error !== null ? $error['message'] : 'unknown';
                DupLog::error("Archive config write failed", "Path: {$archive_config_filepath}, reason: {$reason}");
                self::throwWriteException('archive config file', $reason);
            }
        } finally {
            if ($switched) {
                restore_current_blog();
            }
        }
    }

    /**
     * Resolve the list of plugin basenames to force-activate after restoration.
     *
     * @return string[]
     */
    private function getForceActivatePlugins(): array
    {
        $default = [plugin_basename(DUPLICATOR____FILE)];
        /**
         * Filters the list of plugin basenames to force-activate after restoration.
         *
         * @param string[] $plugins Plugin basenames.
         */
        $filtered = apply_filters('duplicator_force_activate_plugins', $default);

        if (!is_array($filtered)) {
            return $default;
        }
        return array_values(array_filter($filtered, 'is_string'));
    }

    /**
     * Get prefill installer params
     *
     * @return array<string,array{formStatus?:string,value:mixed}>
     */
    private function getPrefillParams(): array
    {
        $result = [];
        if (strlen($this->OptsDBHost) > 0) {
            $result['dbhost'] = ['value' => $this->OptsDBHost];
        }

        if (strlen($this->OptsDBName) > 0) {
            $result['dbname'] = ['value' => $this->OptsDBName];
        }

        if (strlen($this->OptsDBUser) > 0) {
            $result['dbuser'] = ['value' => $this->OptsDBUser];
        }

        if (filter_var($this->OptsCPNLEnable, FILTER_VALIDATE_BOOLEAN)) {
            $result['view_mode'] = ['value' => 'cpnl'];
        }

        if (strlen($this->OptsCPNLDBAction) > 0) {
            $result['cpnl-dbaction'] = ['value' => $this->OptsCPNLDBAction];
        }

        if (strlen($this->OptsCPNLHost) > 0) {
            $result['cpnl-host'] = ['value' => $this->OptsCPNLHost];
        }

        if (strlen($this->OptsCPNLUser) > 0) {
            $result['cpnl-user'] = ['value' => $this->OptsCPNLUser];
        }

        if (strlen($this->OptsCPNLPass) > 0) {
            $result['cpnl-pass'] = ['value' => $this->OptsCPNLPass];
        }

        if (strlen($this->OptsCPNLDBHost) > 0) {
            $result['cpnl-dbhost'] = ['value' => $this->OptsCPNLDBHost];
        }

        if (strlen($this->OptsCPNLDBName) > 0) {
            $result['cpnl-dbname-txt'] = ['value' => $this->OptsCPNLDBName];
        }

        if (strlen($this->OptsCPNLDBUser) > 0) {
            $result['cpnl-dbuser-txt'] = ['value' => $this->OptsCPNLDBUser];
        }

        return $result;
    }

    /**
     * Return list of extra files to add to archive
     *
     * @param bool $checkExists Check if file exists
     *
     * @return array<array{sourcePath:string,archivePath:string,id:string,isArtifact:bool}>
     */
    protected function getExtraFilesLists($checkExists = true): array
    {
        $result = $this->getDefaultExtraFilesLists($checkExists);

        /** @var array<int, array{sourcePath: string, archivePath: string, id: string, isArtifact: bool}> */
        return apply_filters('duplicator_installer_extra_files', $result, $this->Package);
    }

    /**
     * Get the complete unfiltered list of extra installer files
     *
     * @param bool $checkExists Check if file exists
     *
     * @return array<array{sourcePath:string,archivePath:string,id:string,isArtifact:bool}>
     */
    protected function getDefaultExtraFilesLists($checkExists = true): array
    {
        $dscMng = $this->Package->getDescriptorMng();
        $result = [];

        $result[] = [
            'sourcePath'  => SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/" . $this->getInstallerLocalName(),
            'archivePath' => $this->getInstallerBackupName(),
            'id'          => 'installer-backup',
            'isArtifact'  => true,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/installer/dup-installer',
            'archivePath' => 'dup-installer',
            'id'          => 'dup-installer',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Snap',
            'archivePath' => 'dup-installer/libs/Snap',
            'id'          => 'lib-snap',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Shell',
            'archivePath' => 'dup-installer/libs/Shell',
            'id'          => 'lib-shell',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Chunking',
            'archivePath' => 'dup-installer/libs/Chunking',
            'id'          => 'lib-chunking',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/DupArchive',
            'archivePath' => 'dup-installer/libs/DupArchive',
            'id'          => 'lib-duparchive',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Binary',
            'archivePath' => 'dup-installer/libs/Binary',
            'id'          => 'lib-binary',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Index',
            'archivePath' => 'dup-installer/libs/Index',
            'id'          => 'lib-index',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Scan',
            'archivePath' => 'dup-installer/libs/Scan',
            'id'          => 'lib-scan',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/WpConfig',
            'archivePath' => 'dup-installer/libs/WpConfig',
            'id'          => 'lib-wpconfig',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/src/Libs/Certificates',
            'archivePath' => 'dup-installer/libs/Certificates',
            'id'          => 'lib-certificates',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/vendor-prefixed/andreamk/jsonserialize',
            'archivePath' => 'dup-installer/vendor-prefixed/andreamk/jsonserialize',
            'id'          => 'vendor-jsonserialize',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/vendor-prefixed/rmccue/requests',
            'archivePath' => 'dup-installer/vendor-prefixed/rmccue/requests',
            'id'          => 'vendor-requests',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => DUPLICATOR____PATH . '/assets/css/font-awesome',
            'archivePath' => 'dup-installer/assets/font-awesome',
            'id'          => 'asset-font-awesome',
            'isArtifact'  => false,
        ];

        $result[] = [
            'sourcePath'  => $this->origFileManger->getMainFolder(),
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_ORIG_FILES),
            'id'          => 'original-files',
            'isArtifact'  => true,
        ];

        $result[] = [
            'sourcePath'  => SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/{$this->Package->getNameHash()}_archive.txt",
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_ARCHIVE_CONFIG),
            'id'          => 'archive-descriptor',
            'isArtifact'  => true,
        ];

        $result[] = [
            'sourcePath'  => SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/{$this->Package->getNameHash()}_scan.json",
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_SCAN),
            'id'          => 'scan-file',
            'isArtifact'  => true,
        ];

        $result[] = [
            'sourcePath'  => SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . '/' . $this->Package->getIndexFileName(),
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_INDEX),
            'id'          => 'index-file',
            'isArtifact'  => true,
        ];

        $result[] = [
            'sourcePath'  => $this->getManualExtractFilePath(),
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_MANUAL_EXTRACT),
            'id'          => 'manual-extract',
            'isArtifact'  => true,
        ];

        foreach (\Duplicator\Core\Addons\AddonsManager::getInstance()->getEnabledAddons() as $addon) {
            if (!is_readable($addon->getAddonInstallerPath())) {
                continue;
            }

            $installerArchivePath = 'dup-installer/addons/' . basename($addon->getAddonInstallerPath());

            $result[] = [
                'sourcePath'  => $addon->getAddonInstallerPath(),
                'archivePath' => $installerArchivePath,
                'id'          => 'addon-' . $addon->getSlug(),
                'isArtifact'  => false,
            ];

            $result[] = [
                'sourcePath'  => $addon::getAddonPath() . '/' . \Duplicator\Core\Addons\AddonsManager::MANIFEST_FILE,
                'archivePath' => $installerArchivePath . '/' . \Duplicator\Core\Addons\AddonsManager::MANIFEST_FILE,
                'id'          => 'addon-manifest-' . $addon->getSlug(),
                'isArtifact'  => false,
            ];
        }

        $result[] = [
            'sourcePath'  => $this->Package->Database->getCompressedStorePath(),
            'archivePath' => 'dup-installer/' . $dscMng->getName(InstallerDescriptors::TYPE_DB_DUMP_GZ),
            'id'          => 'db-dump-gz',
            'isArtifact'  => true,
        ];

        foreach ($result as $index => $item) {
            $result[$index]['sourcePath'] = SnapIO::safePath($item['sourcePath']);
        }

        if ($checkExists) {
            foreach ($result as $item) {
                if (!is_readable($item['sourcePath'])) {
                    throw new DupliException(
                        'INSTALLER FILES: "' . $item['id'] . '" doesn\'t exist ' . $item['sourcePath'],
                        DupliException::CODE_INSTALLER_ADD_FAILED,
                        __('An installer file to add to the archive is missing or not readable. Check the backup log for details.', 'duplicator')
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Get wpInfo object
     *
     * @return DescriptorWpInfo
     */
    private function getWpInfo(): DescriptorWpInfo
    {
        $wpInfo               = new DescriptorWpInfo();
        $wpInfo->version      = $this->Package->VersionWP;
        $wpInfo->is_multisite = is_multisite();
        $wpInfo->network_id   = function_exists('get_current_network_id') ? get_current_network_id() : 1;

        $wpInfo->targetRoot  = WpArchiveUtils::getTargetRootPath();
        $wpInfo->targetPaths = PackageArchive::getScanPaths();
        $wpInfo->adminUsers  = SnapWP::getAdminUserLists();

        $pluginFiltes = (
            in_array(BuildComponents::COMP_PLUGINS_ACTIVE, $this->Package->components) ?
            SnapWP::PLUGIN_INFO_ACTIVE :
            SnapWP::PLUGIN_INFO_ALL
        );
        if (!in_array(BuildComponents::COMP_PLUGINS, $this->Package->components)) {
            $pathFilters = true;
        } else {
            $pathFilters = $this->Package->Archive->FilterDirsAll;
        }
        $pluginsData = SnapWP::getPluginsInfo($pluginFiltes, $pathFilters);
        foreach ($pluginsData as $pluginData) {
            $wpInfo->plugins[$pluginData['slug']] = new DescriptorPlugin($pluginData);
        }
        $themesData = SnapWP::getThemesInfo();
        foreach ($themesData as $themeData) {
            $wpInfo->themes[$themeData['slug']] = new DescriptorTheme($themeData);
        }

        $this->addDefineIfExists($wpInfo->configs->defines, 'ABSPATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DB_CHARSET');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DB_COLLATE');
        $this->addDefineIfExists(
            $wpInfo->configs->defines,
            'MYSQL_CLIENT_FLAGS',
            [
                SnapDB::class,
                'getMysqlConnectFlagsFromMaskVal',
            ]
        );
        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTH_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SECURE_AUTH_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'LOGGED_IN_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'NONCE_KEY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTH_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SECURE_AUTH_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'LOGGED_IN_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'NONCE_SALT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_SITEURL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HOME');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CONTENT_URL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_PLUGIN_URL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'PLUGINDIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'UPLOADS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTOSAVE_INTERVAL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_POST_REVISIONS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'COOKIE_DOMAIN');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ALLOW_MULTISITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'ALLOW_MULTISITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'MULTISITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DOMAIN_CURRENT_SITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'PATH_CURRENT_SITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SITE_ID_CURRENT_SITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'BLOG_ID_CURRENT_SITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SUBDOMAIN_INSTALL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'VHOST');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SUNRISE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'NOBLOGREDIRECT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SCRIPT_DEBUG');
        $this->addDefineIfExists($wpInfo->configs->defines, 'CONCATENATE_SCRIPTS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG_LOG');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_DEBUG_DISPLAY');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_MEMORY_LIMIT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_MAX_MEMORY_LIMIT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CACHE');

        // wp super cache define
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPCACHEHOME');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_TEMP_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_TABLE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'CUSTOM_USER_META_TABLE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPLANG');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_LANG_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SAVEQUERIES');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_CHMOD_FILE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FS_METHOD');
        /**
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_BASE');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_CONTENT_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PLUGIN_DIR');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PUBKEY');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PRIKEY');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_USER');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_PASS');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_HOST');
          $this->addDefineIfExists($wpInfo->configs->defines, 'FTP_SSL');
         * */
        $this->addDefineIfExists($wpInfo->configs->defines, 'ALTERNATE_WP_CRON');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DISABLE_WP_CRON');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_CRON_LOCK_TIMEOUT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'COOKIEPATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'SITECOOKIEPATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'ADMIN_COOKIE_PATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'PLUGINS_COOKIE_PATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'TEMPLATEPATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'STYLESHEETPATH');
        $this->addDefineIfExists($wpInfo->configs->defines, 'EMPTY_TRASH_DAYS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ALLOW_REPAIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DO_NOT_UPGRADE_GLOBAL_TABLES');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DISALLOW_FILE_EDIT');
        $this->addDefineIfExists($wpInfo->configs->defines, 'DISALLOW_FILE_MODS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'FORCE_SSL_ADMIN');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_HTTP_BLOCK_EXTERNAL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_ACCESSIBLE_HOSTS');
        $this->addDefineIfExists($wpInfo->configs->defines, 'AUTOMATIC_UPDATER_DISABLED');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WP_AUTO_UPDATE_CORE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'IMAGE_EDIT_OVERWRITE');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_DIR');
        $this->addDefineIfExists($wpInfo->configs->defines, 'WPMU_PLUGIN_URL');
        $this->addDefineIfExists($wpInfo->configs->defines, 'MUPLUGINDIR');

        $originalUrls                            = WpArchiveUtils::getOriginalUrls();
        $wpInfo->configs->realValues->siteUrl    = $originalUrls['abs'];
        $wpInfo->configs->realValues->homeUrl    = $originalUrls['home'];
        $wpInfo->configs->realValues->loginUrl   = $originalUrls['login'];
        $wpInfo->configs->realValues->contentUrl = $originalUrls['wpcontent'];
        $wpInfo->configs->realValues->uploadBaseUrl = $originalUrls['uploads'];
        $wpInfo->configs->realValues->pluginsUrl    = $originalUrls['plugins'];
        $wpInfo->configs->realValues->mupluginsUrl  = $originalUrls['muplugins'];
        $wpInfo->configs->realValues->themesUrl     = $originalUrls['themes'];
        $wpInfo->configs->realValues->originalPaths = [];
        $originalpaths                              = WpArchiveUtils::getOriginalPaths();
        foreach ($originalpaths as $key => $val) {
            $originalpaths[$key] = untrailingslashit($val);
        }
        $wpInfo->configs->realValues->originalPaths = (object) $originalpaths;
        $wpInfo->configs->realValues->archivePaths  = (object) array_merge(
            $originalpaths,
            WpArchiveUtils::getArchiveListPaths()
        );
        return $wpInfo;
    }

    /**
     * Check if $define is defined and add a prop to $obj
     *
     * @param object        $obj               object to add prop
     * @param string        $define            constant name
     * @param null|callable $transformCallback if it is different from null the function is applied to the value
     *
     * @return boolean return true if define is added of false
     */
    private function addDefineIfExists($obj, string $define, $transformCallback = null): bool
    {
        if (!defined($define)) {
            return false;
        }

        $obj->{$define} = new stdClass();

        if (is_callable($transformCallback)) {
            $obj->{$define}->value = call_user_func($transformCallback, constant($define));
        } else {
            if ($transformCallback !== null) {
                throw new DupliException('transformCallback isn\'t callable');
            }
            $obj->{$define}->value = constant($define);
        }

        if (!is_null($this->configTransformer)) {
            $obj->{$define}->inWpConfig = $this->configTransformer->exists('constant', $define);
        } else {
            $obj->{$define}->inWpConfig = false;
        }

        return true;
    }

    /**
     * Get archive full path
     *
     * @return string
     */
    public function getArchiveFullPath()
    {
        return SnapIO::safePath($this->Package->StorePath) . '/' . $this->Package->Archive->getFileName();
    }

    /**
     * Add installer extra files to the archive.
     *
     * @return void
     */
    private function addExtraFiles(): void
    {
        $archive_filepath = SnapIO::safePath("{$this->Package->StorePath}/{$this->Package->Archive->getFileName()}");

        $this->initConfigFiles();
        $this->createManualExtractCheckFile();
        $this->addExtraFilesToIndex();

        try {
            $archiveEngine = $this->Package->requireBuildOptions()->getArchiveEngine();
            DupLog::trace("Add extra files: Current build mode = " . $archiveEngine);
            if ($archiveEngine == PackageArchive::BUILD_MODE_ZIP_ARCHIVE) {
                $this->zipArchiveAddExtra();
            } elseif ($archiveEngine == PackageArchive::BUILD_MODE_SHELL_EXEC) {
                $this->shellZipAddExtra();
            } elseif ($archiveEngine == PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
                $this->dupArchiveAddExtra();
            } else {
                throw new DupliException(
                    'Unknown build mode: ' . $archiveEngine,
                    DupliException::CODE_INSTALLER_ADD_FAILED,
                    __('The backup failed while adding the installer files to the archive. Check the backup log for details.', 'duplicator')
                );
            }
        } finally {
            try {
                $archive_config_filepath = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . "/{$this->Package->getNameHash()}_archive.txt";
                unlink($archive_config_filepath);
                $this->origFileManger->deleteMainFolder();
                $this->deleteManualExtractCheckFile();
            } catch (Throwable $e) {
                DupLog::infoTrace("Error clean temp installer file, but continue. Message: " . $e->getMessage());
            }
        }

        $this->Package->Archive->Size = SnapIO::filesize($archive_filepath);
    }

    /**
     * Check if index need include installer files
     *
     * @return bool
     */
    protected static function isIndexIncludeInstallerFiles(): bool
    {
        return DUPLICATOR_INDEX_INCLUDE_INSTALLER_FILES;
    }

    /**
     * Add extra files to index
     *
     * @return void
     */
    public function addExtraFilesToIndex(): void
    {
        if (static::isIndexIncludeInstallerFiles() === false) {
            return;
        }

        $defaultIndexPath = $this->Package->Archive->defaultIndexPath();
        $indexManager     = $this->Package->Archive->getIndexManager();
        if ($indexManager->getPath() !== $defaultIndexPath) {
            $indexManager = new FileIndexManager($defaultIndexPath);
        }

        $extraFiles = $this->getDefaultExtraFilesLists();
        usort($extraFiles, fn($a, $b): int => strcmp($a['archivePath'], $b['archivePath']));

        foreach ($extraFiles as $extraItem) {
            $sourcePath  = $extraItem['sourcePath'];
            $archivePath = $extraItem['archivePath'];
            $listType    = $extraItem['isArtifact']
                ? FileIndexManager::LIST_TYPE_DESCRIPTORS
                : FileIndexManager::LIST_TYPE_INSTALLER;

            FileIndexManager::setRootPathMap($sourcePath, $archivePath);
            if (!is_dir($sourcePath)) {
                $indexManager->add($listType, new ScanNodeInfo($sourcePath));
                continue;
            }

            $archivePath = SnapIO::trailingslashit($archivePath);
            $iterator    = new ScanIterator($sourcePath, [], ScanIterator::SORT_ASC);
            foreach ($iterator as $node) {
                if ($node->isDir()) {
                    continue;
                }

                $indexManager->add($listType, $node);
            }
        }
        // Reset the static root path map so it can't leak into later index writes in the same request
        FileIndexManager::setRootPathMap();

        $indexManager->save();
    }

    /**
     * Get installer backup name
     *
     * @return string
     */
    public function getInstallerBackupName()
    {
        return $this->Package->getNameHash() . '_' . self::DEFAULT_INSTALLER_BACKUP_FILE_NAME_WITHOUT_HASH;
    }

    /**
     * Add extra files in duparchive
     *
     * @return void
     */
    private function dupArchiveAddExtra(): void
    {
        $logger = new \Duplicator\Package\Create\DupArchive\Logger();
        DupArchiveEngine::init($logger, null);

        $archivePath   = $this->getArchiveFullPath();
        $extraPoistion = SnapIO::filesize($archivePath);
        if ($extraPoistion <= 0) {
            throw new DupliException(
                "Cannot add extra files: archive is missing or empty at $archivePath",
                DupliException::CODE_INSTALLER_ADD_FAILED,
                __('The backup archive is missing or empty, the installer files cannot be added. Check the backup log for details.', 'duplicator')
            );
        }

        $password = $this->Package->Archive->getArchivePassword();

        foreach ($this->getExtraFilesLists() as $extraItem) {
            if (is_dir($extraItem['sourcePath'])) {
                $result               = DupArchiveEngine::addDirectoryToArchiveST(
                    $archivePath,
                    $extraItem['sourcePath'],
                    $extraItem['archivePath'],
                    $password,
                    true
                );
                $this->numFilesAdded += $result->numFilesAdded;
                $this->numDirsAdded  += $result->numDirsAdded;
            } else {
                DupArchiveEngine::addRelativeFileToArchiveST(
                    $archivePath,
                    $extraItem['sourcePath'],
                    $extraItem['archivePath'],
                    $password
                );
                $this->numFilesAdded++;
            }
        }

        // store extra files position
        $src  = json_encode([DupArchiveEngine::EXTRA_FILES_POS_KEY => $extraPoistion]);
        $src .= str_repeat("\0", DupArchiveEngine::INDEX_FILE_SIZE - strlen($src));
        DupArchiveEngine::replaceFileContent(
            $archivePath,
            $src,
            DupArchiveEngine::INDEX_FILE_NAME,
            $password,
            0,
            3000
        );
    }

    /**
     * Add extra files in zip archive
     *
     * @return void
     */
    private function zipArchiveAddExtra(): void
    {
        $zipArchive = new ZipArchiveExtended($this->getArchiveFullPath());
        $zipArchive->setCompressed($this->Package->requireBuildOptions()->isCompressionEnabled());
        if ($this->Package->Archive->isArchiveEncrypt()) {
            $zipArchive->setEncrypt(true, $this->Package->Archive->getArchivePassword());
        }

        if ($zipArchive->open() !== true) {
            throw new DupliException(
                "Couldn't open zip archive to add installer files.",
                DupliException::CODE_INSTALLER_ADD_FAILED,
                __('Could not open the archive file to add the installer files.', 'duplicator')
            );
        }

        DupLog::trace("Successfully opened zip");

        foreach ($this->getExtraFilesLists() as $extraItem) {
            if (is_dir($extraItem['sourcePath'])) {
                $zipArchive->addDir($extraItem['sourcePath'], $extraItem['archivePath']);
            } else {
                $saveUncompressed = $extraItem['id'] === 'db-dump-gz';
                $zipArchive->addFile($extraItem['sourcePath'], $extraItem['archivePath'], $saveUncompressed);
            }
        }

        if ($zipArchive->close() === false) {
            throw new DupliException(
                "Couldn't close zip archive after adding installer files.",
                DupliException::CODE_INSTALLER_ADD_FAILED,
                __('Could not finalize the archive file after adding the installer files.', 'duplicator')
            );
        }

        DupLog::trace('After ziparchive close when adding installer');

        $this->installerFilesArchiveCheck();
    }

    /**
     * Verify the installer files are present in the built archive.
     *
     * Single check for both zip engines (PHP ZipArchive and shell zip), backed
     * by the layered ZipVerifier probes. Confirmed missing entries and archive
     * corruption fail the build; an archive that no probe can inspect
     * completes with a build warning.
     *
     * @return void
     */
    private function installerFilesArchiveCheck(): void
    {
        $filesToValidate = $this->getInstallerPathsForIntegrityCheck();
        DupLog::infoTrace('CHECK FILES ' . SnapLog::v2str($filesToValidate));

        $result = ZipVerifier::verifyEntriesPresence($this->getArchiveFullPath(), $filesToValidate);

        switch ($result['verdict']) {
            case ZipVerifier::VERDICT_OK:
                DupLog::info(__('ARCHIVE CONSISTENCY TEST: PASS', 'duplicator'));
                return;
            case ZipVerifier::VERDICT_UNKNOWN:
                DupLog::infoTrace("Installer files presence not verifiable ({$result['detail']}), continuing with a build warning");
                $this->Package->addBuildWarning(
                    AbstractPackage::WARNING_INSTALLER_FILES_UNVERIFIED,
                    __(
                        'It was not possible to double-check that the installer files are inside the archive.
                        No action is needed; to be extra safe, you can test the Backup with a restore.',
                        'duplicator'
                    )
                );
                return;
            case ZipVerifier::VERDICT_MISSING:
                DupLog::info(__('ARCHIVE CONSISTENCY TEST: FAIL', 'duplicator'));
                foreach ($result['missing'] as $path) {
                    DupLog::infoTrace("Couldn't find {$path} in archive");
                }
                throw new DupliException(
                    'Zip for Backup ' . $this->Package->getId() . " didn't pass consistency test, missing entries: " . count($result['missing']),
                    DupliException::CODE_INSTALLER_CONSISTENCY_FAILED,
                    __('One or more installer files were not found in the archive. Check the backup log for details.', 'duplicator')
                );
            default:
                throw new DupliException(
                    'Installer files check failed, archive is corrupted: ' . $result['detail'],
                    DupliException::CODE_INSTALLER_CONSISTENCY_FAILED,
                    __("Archive doesn't pass consistency check.", 'duplicator')
                );
        }
    }

    /**
     * Add extra files in shell zip
     *
     * @return void
     */
    private function shellZipAddExtra(): void
    {
        $tmpExtraFolder = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP) . '/extras/';

        if (file_exists($tmpExtraFolder)) {
            if (SnapIO::rrmdir($tmpExtraFolder) === false) {
                throw new DupliException(
                    "Error deleting $tmpExtraFolder",
                    DupliException::CODE_INSTALLER_ADD_FAILED,
                    __('Could not clean the temporary folder for the installer files.', 'duplicator')
                );
            }
        }
        if (!wp_mkdir_p($tmpExtraFolder)) {
            throw new DupliException(
                'Error creating extras directory',
                DupliException::CODE_INSTALLER_ADD_FAILED,
                __('Could not create the temporary folder for the installer files.', 'duplicator')
            );
        }
        SnapIO::createSilenceIndex($tmpExtraFolder);

        foreach ($this->getExtraFilesLists() as $extraItem) {
            $destPath = $tmpExtraFolder . $extraItem['archivePath'];

            if (!wp_mkdir_p(dirname($destPath))) {
                throw new DupliException(
                    "Error creating extras directory, Couldn't create " . dirname($destPath),
                    DupliException::CODE_INSTALLER_ADD_FAILED,
                    __('Could not create the temporary folder for the installer files.', 'duplicator')
                );
            }

            if (!SnapIO::rcopy($extraItem['sourcePath'], $destPath)) {
                throw DupliException::fromLastError(
                    "Error copying an installer file to the temporary folder.\n" .
                    'From ' . $extraItem['sourcePath'] . ' to ' . $destPath,
                    DupliException::CODE_INSTALLER_ADD_FAILED,
                    __('Could not copy an installer file to the temporary folder.', 'duplicator')
                );
            }
        }

        //-- STAGE 1 ADD
        $params = Shell::getCompressionParam($this->Package->requireBuildOptions()->isCompressionEnabled());
        if (strlen($this->Package->Archive->getArchivePassword()) > 0) {
            $params .= ' --password ' . escapeshellarg($this->Package->Archive->getArchivePassword());
        }
        $params       .= ' -g -rq';
        $paramsPostfix = ' -x "index.php"';

        $command  = 'cd ' . escapeshellarg(SnapIO::safePath($tmpExtraFolder));
        $command .= ' && ' . escapeshellcmd(ShellZipUtils::getShellExecZipPath()) . ' ' . $params . ' ';
        $command .= escapeshellarg($this->getArchiveFullPath()) . ' .[!.]* *' . $paramsPostfix;

        $loggableCommand = str_replace(
            ' --password ' . escapeshellarg($this->Package->Archive->getArchivePassword()),
            ' --password [REDACTED]',
            $command
        );
        DupLog::infoTrace('EXECUTING SHELL COMMAND');

        DupLog::infoTrace("SHELL COMMAND: $loggableCommand");
        $shellOutput = Shell::runCommandBuffered($command);
        $exitCode    = $shellOutput->getCode();
        if ($exitCode != 0) {
            // Any non-zero exit code is a failure even with no command output, same
            // as the main shell zip build. The raw output goes to the private log
            // only, so the exception message stays stable for telemetry.
            // zip prints the fatal error last: keep the tail.
            $output = trim($shellOutput->getOutputAsString(-20));
            DupLog::info("SHELL ZIP INSTALLER ADD FAILED: exit code {$exitCode}, output: " . ($output === '' ? '[no output]' : $output));

            if (SnapIO::isDiskFullError($output)) {
                throw DupliException::diskFull();
            }

            throw new DupliException(
                sprintf(
                    'Shell zip command failed adding installer files, exit code %d (%s).',
                    $exitCode,
                    SnapString::spellDigits($exitCode)
                ),
                DupliException::CODE_INSTALLER_ADD_FAILED,
                __('The shell zip command failed while adding the installer files to the archive.', 'duplicator')
            );
        }

        $this->installerFilesArchiveCheck();

        if (!SnapIO::rrmdir($tmpExtraFolder)) {
            DupLog::trace("Couldn't recursively delete {$tmpExtraFolder}");
        }
    }

    /**
     * Creates the original_files_ folder in the tmp directory where all config files are saved
     * to be later added to the archives
     *
     * @return void
     */
    public function initConfigFiles(): void
    {
        $this->origFileManger->init();
        $configFilePaths = $this->getConfigFilePaths();
        foreach ($configFilePaths as $identifier => $path) {
            if ($path !== false) {
                try {
                    $this->origFileManger->addEntry($identifier, $path, SnapOrigFileManager::MODE_COPY, self::CONFIG_ORIG_FILE_FOLDER_PREFIX . $identifier);
                } catch (Exception $ex) {
                    DupLog::infoTrace("Error while handling config files: " . $ex->getMessage());
                }
            }
        }

        //Clean sensitive information from wp-config.php file.
        self::cleanTempWPConfArkFilePath($this->origFileManger->getEntryStoredPath(self::CONFIG_ORIG_FILE_WPCONFIG_ID));
    }

    /**
     * Gets config files path
     *
     * @return string[] array of config files in identifier => path format
     */
    public function getConfigFilePaths()
    {
        $home        = WpArchiveUtils::getArchiveListPaths('home');
        $configFiles = [
            self::CONFIG_ORIG_FILE_USERINI_ID   => $home . '/.user.ini',
            self::CONFIG_ORIG_FILE_PHPINI_ID    => $home . '/php.ini',
            self::CONFIG_ORIG_FILE_WEBCONFIG_ID => $home . '/web.config',
            self::CONFIG_ORIG_FILE_HTACCESS_ID  => $home . '/.htaccess',
            self::CONFIG_ORIG_FILE_WPCONFIG_ID  => SnapWP::getWPConfigPath(),
        ];
        foreach ($configFiles as $identifier => $path) {
            if (!file_exists($path)) {
                unset($configFiles[$identifier]);
            }
        }

        return $configFiles;
    }

    /**
     * Get path list for integrity check
     *
     * @return string[]
     */
    public function getInstallerPathsForIntegrityCheck()
    {
        /** @var bool Whether to include static installer paths (e.g. dup-installer/ structure files) */
        $includeStaticPaths = (bool) apply_filters('duplicator_installer_include_static_paths', true, $this->Package);

        $filesToValidate = [];
        if ($includeStaticPaths) {
            $filesToValidate = [
                'dup-installer/api/class.api.php',
                'dup-installer/assets/index.php',
                'dup-installer/classes/index.php',
                'dup-installer/ctrls/index.php',
                'dup-installer/src/Utils/Autoloader.php',
                'dup-installer/templates/default/page-help.php',
                'dup-installer/main.installer.php',
            ];
        }

        foreach ($this->getExtraFilesLists() as $extraItem) {
            if (is_file($extraItem['sourcePath'])) {
                $filesToValidate[] = $extraItem['archivePath'];
            } else {
                if (file_exists(trailingslashit($extraItem['sourcePath']) . 'index.php')) {
                    $filesToValidate[] = ltrim(trailingslashit($extraItem['archivePath']), '\\/') . 'index.php';
                } else {
                    // SKIP CHECK
                }
            }
        }

        return array_unique($filesToValidate);
    }

    /**
     * Create manual extract check file
     *
     * @return bool
     */
    private function createManualExtractCheckFile(): bool
    {
        $file_path = $this->getManualExtractFilePath();
        return SnapIO::filePutContents($file_path, '');
    }

    /**
     * Get manual extract check file path
     *
     * @return string
     */
    private function getManualExtractFilePath(): string
    {
        $tmp = SnapIO::safePath(DUPLICATOR_SSDIR_PATH_TMP);
        return $tmp . '/dup-manual-extract__' . $this->Package->getPrimaryInternalHash();
    }

    /**
     * Delete manual extract check file
     *
     * @return void
     */
    private function deleteManualExtractCheckFile(): void
    {
        SnapIO::rm($this->getManualExtractFilePath());
    }

    /**
     * Clear out sensitive database connection information
     *
     * @param string $temp_conf_ark_file_path Temp config file path
     *
     * @return void
     */
    private static function cleanTempWPConfArkFilePath($temp_conf_ark_file_path): void
    {
        try {
            if (function_exists('token_get_all')) {
                $transformer = new WPConfigTransformer($temp_conf_ark_file_path);
                $constants   = [
                    'DB_NAME',
                    'DB_USER',
                    'DB_PASSWORD',
                    'DB_HOST',
                ];
                foreach ($constants as $constant) {
                    if ($transformer->exists('constant', $constant)) {
                        $transformer->update('constant', $constant, '');
                    }
                }
            }
        } catch (Throwable $e) {
            DupLog::infoTrace("Can\'t inizialize wp-config transformer Message: " . $e->getMessage());
        }
    }

    /**
     * @param string $fileLabel Short label for the file that failed
     * @param string $reason    The error message from error_get_last()
     *
     * @return never
     */
    private static function throwWriteException(string $fileLabel, string $reason): void
    {
        if (SnapIO::isDiskFullError($reason)) {
            throw DupliException::diskFull();
        }

        throw new DupliException(
            "Installer file creation failed: couldn't write the " . $fileLabel . '.',
            DupliException::CODE_INSTALLER_BUILD_FAILED,
            __(
                'The backup failed while writing an installer file. Check the backup log for details.',
                'duplicator'
            )
        );
    }
}
