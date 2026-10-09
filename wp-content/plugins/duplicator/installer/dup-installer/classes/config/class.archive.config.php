<?php

/**
 * Class used to control values about the package meta data
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Package\PComponents;
use Duplicator\Installer\Core\Deploy\ServerConfigs;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Models\SiteOwrMap;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Installer\Package\DescriptorSubsite;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpConfig\WPConfigTransformer;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * singleton class
 */
class DUPX_ArchiveConfig extends ArchiveDescriptor
{
    const NOTICE_ID_PARAM_EMPTY = 'param_empty_to_validate';


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
     * Singleton class constructor
     */
    protected function __construct()
    {
        $config_filepath = DUPX_Package::getPackageArchivePath();
        if (!file_exists($config_filepath)) {
            throw new Exception("Archive file $config_filepath doesn't exist");
        }

        if (($file_contents = file_get_contents($config_filepath)) === false) {
            throw new Exception("Can\'t read Archive file $config_filepath");
        }

        JsonSerialize::unserializeToObj($file_contents, $this);
    }

    /**
     * Get installer display name, optionally appending a postfix.
     *
     * @param string $postfix optional postfix appended after a space
     *
     * @return string
     */
    public function getInstallerName(string $postfix = ''): string
    {
        $name = isset($this->header['name']) && $this->header['name'] !== ''
            ? (string) $this->header['name']
            : 'Duplicator';
        return $postfix === '' ? $name : $name . ' ' . $postfix;
    }

    /**
     * Returns true if it's a DB only insallation
     *
     * @return bool
     */
    public function isDBOnly(): bool
    {
        return PComponents::isDBOnly($this->components);
    }

    /**
     * Returns true if DB excluded from the Package
     *
     * @return bool
     */
    public function isDBExcluded(): bool
    {
        return PComponents::isDBExcluded($this->components);
    }

    /**
     *
     * @return bool
     */
    public function isZipArchive(): bool
    {
        $extension = strtolower(pathinfo($this->package_name, PATHINFO_EXTENSION));
        return ($extension == 'zip');
    }

    /**
     * Check if define value exists
     *
     * @param string $define define name
     *
     * @return bool
     */
    public function defineValueExists($define): bool
    {
        return isset($this->wpInfo->configs->defines->{$define});
    }

    /**
     * Get user list
     *
     * @return array<int,string> Return array of user id => user login
     */
    public function getUsersLists(): array
    {
        $result = [];
        foreach ($this->wpInfo->adminUsers as $user) {
            $result[$user->ID] = $user->user_login;
        }
        return $result;
    }

    /**
     * Get define value from archive or default value if don't exists
     *
     * @param string                             $define  define name
     * @param array{inWpConfig:bool,value:mixed} $default default value
     *
     * @return array{inWpConfig:bool,value:mixed}
     */
    public function getDefineArrayValue(
        $define,
        $default = [
            'value'      => false,
            'inWpConfig' => false,
        ]
    ) {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return (array) $defines->{$define};
        } else {
            return $default;
        }
    }

    /**
     * return define value from archive or default value if don't exists
     *
     * @param string $define  define name
     * @param mixed  $default default value
     *
     * @return mixed
     */
    public function getDefineValue($define, $default = false)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return $defines->{$define}->value;
        } else {
            return $default;
        }
    }

    /**
     * return define value from archive or default value if don't exists in wp-config
     *
     * @param string $define  define name
     * @param mixed  $default default value
     *
     * @return mixed
     */
    public function getWpConfigDefineValue($define, $default = false)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define}) && $defines->{$define}->inWpConfig) {
            return $defines->{$define}->value;
        } else {
            return $default;
        }
    }

    /**
     * return true if define exists in wp-config
     *
     * @param string $define define name
     *
     * @return bool
     */
    public function inWpConfigDefine($define)
    {
        $defines = $this->wpInfo->configs->defines;
        if (isset($defines->{$define})) {
            return $defines->{$define}->inWpConfig;
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $key key
     *
     * @return boolean
     */
    public function realValueExists($key): bool
    {
        return isset($this->wpInfo->configs->realValues->{$key});
    }

    /**
     * return read value from archive if exists of default if don't exists
     *
     * @param string $key     key
     * @param mixed  $default default value
     *
     * @return mixed
     */
    public function getRealValue($key, $default = false)
    {
        $values = $this->wpInfo->configs->realValues;
        if (isset($values->{$key})) {
            return $values->{$key};
        } else {
            return $default;
        }
    }

    /**
     * Get blogname from selected subsite id
     * If subsite id is not selected return default blogname
     *
     * @return string
     */
    public function getBlognameFromSelectedSubsiteId()
    {
        $subsiteId = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_ID);
        $blogname  = $this->blogname;
        if ($subsiteId > 0) {
            foreach ($this->subsites as $subsite) {
                if ($subsiteId == $subsite->id) {
                    $blogname = $subsite->blogname;
                    break;
                }
            }
        }
        return $blogname;
    }

    /**
     * Return package life
     *
     * @param string $type can be hours,human,timestamp
     *
     * @return int package life in hours, timestamp
     */
    public function getPackageLife($type = 'timestamp'): int
    {
        $created = strtotime($this->created);
        $current = strtotime(gmdate("Y-m-d H:i:s"));
        $delta   = (int) ($current - $created);

        switch ($type) {
            case 'hours':
                return (int) max(0, floor($delta / 60 / 60));
            case 'timestamp':
            default:
                return $delta;
        }
    }

    /**
     * Total items count in archive
     *
     * @return int
     */
    public function totalArchiveItemsCount()
    {
        return $this->fileInfo->dirCount + $this->fileInfo->fileCount;
    }

    /**
     * Return true if source site is multisite
     *
     * @return bool
     */
    public function isNetwork(): bool
    {
        return $this->mu_mode > 0;
    }

    /**
     * Return true if source site is subdomain multisite
     *
     * @return bool
     */
    public function isSubdomain(): bool
    {
        return $this->mu_mode == 1;
    }

    /**
     * Return true if source site is partial multisite
     *
     * @return bool
     */
    public function isPartialNetwork(): bool
    {
        $hasNotImportableSubsite = SnapUtil::inArrayExtended($this->subsites, fn($subsite): bool => count($subsite->filteredTables) > 0);
        return ($this->mu_mode != 0 && count($this->subsites) > 0 && $this->mu_is_filtered) || ($hasNotImportableSubsite && InstState::isImportFromBackendMode());
    }

    /**
     * Set the Engines and Params in case the DB excluded
     *
     * @return void
     */
    public function setEnginesDBExcluded(): void
    {
        if (!DUPX_ArchiveConfig::getInstance()->isDBExcluded()) {
            return;
        }

        $prmMng = PrmMng::getInstance();
        $prmMng->setValue(PrmMng::PARAM_DB_ACTION, \DUPX_DBInstall::DBACTION_DO_NOTHING);
        $prmMng->setValue(PrmMng::PARAM_REPLACE_ENGINE, DUPX_S3_Funcs::MODE_SKIP);
        $prmMng->setValue(PrmMng::PARAM_CPNL_CAN_SELECTED, false);
        $prmMng->setValue(PrmMng::PARAM_WP_CONFIG, ServerConfigs::ACTION_WPCONF_NOTHING);
        $prmMng->setValue(PrmMng::PARAM_HTACCESS_CONFIG, 'nothing');
        $prmMng->setValue(PrmMng::PARAM_OTHER_CONFIG, 'nothing');
        $prmMng->setFormStatus(PrmMng::PARAM_WP_CONFIG, ParamForm::STATUS_INFO_ONLY);
        $prmMng->setFormStatus(PrmMng::PARAM_HTACCESS_CONFIG, ParamForm::STATUS_INFO_ONLY);
        $prmMng->setFormStatus(PrmMng::PARAM_OTHER_CONFIG, ParamForm::STATUS_INFO_ONLY);
        $prmMng->setFormStatus(PrmMng::PARAM_DB_ACTION, ParamForm::STATUS_INFO_ONLY);
        $prmMng->setFormStatus(PrmMng::PARAM_DB_HOST, ParamForm::STATUS_SKIP);
        $prmMng->setFormStatus(PrmMng::PARAM_DB_USER, ParamForm::STATUS_SKIP);
        $prmMng->setFormStatus(PrmMng::PARAM_DB_NAME, ParamForm::STATUS_SKIP);
        $prmMng->setFormStatus(PrmMng::PARAM_DB_PASS, ParamForm::STATUS_SKIP);

        $prmMng->save();
    }

    /**
     * Set new paths and url params by main new
     *
     * @return void
     */
    public function setNewPathsAndUrlParamsByMainNew(): void
    {
        self::manageEmptyPathAndUrl(PrmMng::PARAM_PATH_WP_CORE_NEW, PrmMng::PARAM_SITE_URL);
        self::manageEmptyPathAndUrl(PrmMng::PARAM_PATH_CONTENT_NEW, PrmMng::PARAM_URL_CONTENT_NEW);
        self::manageEmptyPathAndUrl(PrmMng::PARAM_PATH_UPLOADS_NEW, PrmMng::PARAM_URL_UPLOADS_NEW);
        self::manageEmptyPathAndUrl(PrmMng::PARAM_PATH_PLUGINS_NEW, PrmMng::PARAM_URL_PLUGINS_NEW);
        self::manageEmptyPathAndUrl(PrmMng::PARAM_PATH_MUPLUGINS_NEW, PrmMng::PARAM_URL_MUPLUGINS_NEW);

        $paramsManager = PrmMng::getInstance();
        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();
        $noticeManager->addNextStepNotice([
            'shortMsg'    => '',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => '<span class="green">If desired, you can change the default values in "Advanced install" &gt; "Other options"</span>.',
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
        ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND_IF_EXISTS, self::NOTICE_ID_PARAM_EMPTY);

        $paramsManager->save();
        $noticeManager->saveNotices();
    }

    /**
     * Manage empty path and url
     *
     * @param string $pathKey path key
     * @param string $urlKey  url key
     *
     * @return void
     */
    protected static function manageEmptyPathAndUrl($pathKey, $urlKey)
    {
        $paramsManager = PrmMng::getInstance();
        $validPath     = (strlen($paramsManager->getValue($pathKey)) > 0);
        $validUrl      = (strlen($paramsManager->getValue($urlKey)) > 0);

        if ($validPath && $validUrl) {
            return;
        }

        $paramsManager->setValue($pathKey, self::getDefaultPathUrlValueFromParamKey($pathKey));
        $paramsManager->setValue($urlKey, self::getDefaultPathUrlValueFromParamKey($urlKey));

        $noticeManager = DUPX_NOTICE_MANAGER::getInstance();
        $msg           = '<b>' . $paramsManager->getLabel($pathKey) . ' and/or ' . $paramsManager->getLabel($urlKey) . '</b> can\'t be generated automatically so they are set to their default value.' . "<br>\n";
        $msg          .= $paramsManager->getLabel($pathKey) . ': ' . $paramsManager->getValue($pathKey) . "<br>\n";
        $msg          .= $paramsManager->getLabel($urlKey) . ': ' . $paramsManager->getValue($urlKey) . "<br>\n";

        $noticeManager->addNextStepNotice([
            'shortMsg'    => 'URLs and/or PATHs set automatically to their default value.',
            'level'       => DUPX_NOTICE_ITEM::NOTICE,
            'longMsg'     => $msg . "<br>\n",
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
        ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, self::NOTICE_ID_PARAM_EMPTY);
    }

    /**
     * Get the default value for a path or url param
     *
     * @param string $paramKey param key
     *
     * @return string
     */
    public static function getDefaultPathUrlValueFromParamKey($paramKey)
    {
        $paramsManager = PrmMng::getInstance();
        switch ($paramKey) {
            case PrmMng::PARAM_SITE_URL:
                return $paramsManager->getValue(PrmMng::PARAM_URL_NEW);
            case PrmMng::PARAM_URL_CONTENT_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_URL_NEW) . '/wp-content';
            case PrmMng::PARAM_URL_UPLOADS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_URL_CONTENT_NEW) . '/uploads';
            case PrmMng::PARAM_URL_PLUGINS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_URL_CONTENT_NEW) . '/plugins';
            case PrmMng::PARAM_URL_MUPLUGINS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_URL_CONTENT_NEW) . '/mu-plugins';
            case PrmMng::PARAM_PATH_WP_CORE_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_PATH_NEW);
            case PrmMng::PARAM_PATH_CONTENT_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_PATH_NEW) . '/wp-content';
            case PrmMng::PARAM_PATH_UPLOADS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW) . '/uploads';
            case PrmMng::PARAM_PATH_PLUGINS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW) . '/plugins';
            case PrmMng::PARAM_PATH_MUPLUGINS_NEW:
                return $paramsManager->getValue(PrmMng::PARAM_PATH_CONTENT_NEW) . '/mu-plugins';
            default:
                throw new Exception('Invalid URL or PATH param');
        }
    }

    /**
     * Get new sub string from old main string and new main string
     *
     * @param string $oldMain old main string
     * @param string $newMain new main string
     * @param string $subOld  old sub string
     *
     * @return boolean|string  return false if cant generate new sub string
     */
    public static function getNewSubString($oldMain, $newMain, $subOld)
    {
        if (($relativePath = SnapIO::getRelativePath($subOld, $oldMain)) === false) {
            return false;
        }
        return $newMain . '/' . $relativePath;
    }

    /**
     * Get new sub url from old main url and new main url
     *
     * @param string $oldMain old main url
     * @param string $newMain new main url
     * @param string $subOld  old sub url
     *
     * @return boolean|string  return false if cant generate new sub string
     */
    public static function getNewSubUrl($oldMain, $newMain, $subOld)
    {

        $parsedOldMain = SnapURL::parseUrl($oldMain);
        $parsedNewMain = SnapURL::parseUrl($newMain);
        $parsedSubOld  = SnapURL::parseUrl($subOld);

        $parsedSubNew           = $parsedSubOld;
        $parsedSubNew['scheme'] = $parsedNewMain['scheme'];
        $parsedSubNew['port']   = $parsedNewMain['port'];

        if ($parsedOldMain['host'] !== $parsedSubOld['host']) {
            return false;
        }
        $parsedSubNew['host'] = $parsedNewMain['host'];

        if (($newPath = self::getNewSubString($parsedOldMain['path'], $parsedNewMain['path'], $parsedSubOld['path'])) === false) {
            return false;
        }
        $parsedSubNew['path'] = $newPath;
        return SnapURL::buildUrl($parsedSubNew);
    }

    /**
     * Returns case insensitive duplicate tables from source site
     *
     * @return array<string[]>
     */
    public function getDuplicateTableNames(): array
    {
        $allTables = array_keys($this->dbInfo->tablesList);

        return SnapString::getCaseInsesitiveDuplicates($allTables);
    }

    /**
     * Returns list of redundant duplicates
     *
     * @return string[]
     */
    public function getRedundantDuplicateTableNames(): array
    {
        $duplicateTables = $this->getDuplicateTableNames();
        $prefix          = DUPX_ArchiveConfig::getInstance()->wp_tableprefix;
        $redundantTables = [];

        foreach ($duplicateTables as $tables) {
            $redundantTables = array_merge($redundantTables, SnapDB::getRedundantDuplicateTables($prefix, $tables));
        }

        return $redundantTables;
    }

    /**
     * Check if tables are case sensitive
     *
     * @return bool
     */
    public function isTablesCaseSensitive()
    {
        return $this->dbInfo->isTablesUpperCase;
    }

    /**
     * Check if table prefix is changed
     *
     * @return bool
     */
    public function isTablePrefixChanged(): bool
    {
        return $this->wp_tableprefix != PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
    }

    /**
     * Get table name with new prefix
     *
     * @param string $table table name
     *
     * @return string
     */
    public function getTableWithNewPrefix($table): string
    {
        $search  = '/^' . preg_quote($this->wp_tableprefix, '/') . '(.*)/';
        $replace = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX) . '$1';
        return (string) preg_replace($search, $replace, $table, 1);
    }

    /**
     * Get subsite prefix by subsite id
     *
     * @param int $subsiteId subsite id
     *
     * @return boolean|string return false if don't exists subsiteid
     */
    public function getSubsitePrefixByParam($subsiteId)
    {
        if (($subSiteObj = $this->getSubsiteObjById($subsiteId)) === false) {
            return false;
        }

        if (!$this->isTablePrefixChanged()) {
            return $subSiteObj->blog_prefix;
        } else {
            $search  = '/^' . preg_quote($this->wp_tableprefix, '/') . '(.*)/';
            $replace = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX) . '$1';
            return preg_replace($search, $replace, $subSiteObj->blog_prefix, 1);
        }
    }

    /**
     * Get main site index
     *
     * @return int
     */
    public function getMainSiteIndex()
    {
        static $mainSubsiteIndex = null;
        if (is_null($mainSubsiteIndex)) {
            $mainSubsiteIndex = -1;
            if (!empty($this->subsites)) {
                foreach ($this->subsites as $index => $subsite) {
                    if ($subsite->id === $this->main_site_id) {
                        $mainSubsiteIndex = $index;
                        break;
                    }
                }
                if ($mainSubsiteIndex == -1) {
                    $mainSubsiteIndex = 0;
                }
            }
        }
        return $mainSubsiteIndex;
    }

    /**
     * Return main site info object
     *
     * @return DescriptorSubsite
     */
    public function getMainSiteInfo()
    {
        return $this->subsites[$this->getMainSiteIndex()];
    }

    /**
     * Return subsites ids
     *
     * @return int[]
     */
    public function getSubsitesIds()
    {
        static $subsitesIds = null;
        if (is_null($subsitesIds)) {
            $subsitesIds = [];
            foreach ($this->subsites as $subsite) {
                $subsitesIds[] = $subsite->id;
            }
        }

        return $subsitesIds;
    }

    /**
     * Get subsite object info by id
     *
     * @param int $id subsite id
     *
     * @return false|DescriptorSubsite refurn false if id dont exists
     */
    public function getSubsiteObjById($id)
    {
        /** @var (false|DescriptorSubsite)[] */
        static $indexCache = [];

        if (!isset($indexCache[$id])) {
            foreach ($this->subsites as $subsite) {
                if ($subsite->id == $id) {
                    $indexCache[$id] = $subsite;
                    break;
                }
            }
            if (!isset($indexCache[$id])) {
                $indexCache[$id] = false;
            }
        }

        return $indexCache[$id];
    }

    /**
     * Get site old url scheme
     *
     * @return string
     */
    public function getOldUrlScheme()
    {
        static $oldScheme = null;
        if (is_null($oldScheme)) {
            $siteUrl   = $this->getRealValue('siteUrl');
            $oldScheme = parse_url($siteUrl, PHP_URL_SCHEME);
            if ($oldScheme === false) {
                $oldScheme = 'http';
            }
        }
        return $oldScheme;
    }

    /**
     * subsite object from archive
     *
     * @param DescriptorSubsite $subsite subsite object
     *
     * @return string
     */
    public function getUrlFromSubsiteObj(DescriptorSubsite $subsite): string
    {
        return $this->getOldUrlScheme() . '://' . $subsite->domain . $subsite->path;
    }

    /**
     * @param DescriptorSubsite $subsite subsite object
     *
     * @return string the uploads url with the subsite specific url (e.g. example.com/subsite/wp-content/uploads)
     */
    public function getUploadsUrlFromSubsiteObj(DescriptorSubsite $subsite)
    {
        if ($subsite->id == $this->getMainSiteIndex()) {
            return PrmMng::getInstance()->getValue(PrmMng::PARAM_URL_UPLOADS_OLD);
        }

        $subsiteOldUrl  = rtrim($this->getUrlFromSubsiteObj($subsite), '/');
        $mainOldUrlHost = parse_url(PrmMng::getInstance()->getValue(PrmMng::PARAM_URL_OLD), PHP_URL_HOST);

        return preg_replace(
            "/(https?:\/\/(?:www\.)?" . preg_quote($mainOldUrlHost, '/') . ")(.*)/",
            $subsiteOldUrl . "$2",
            PrmMng::getInstance()->getValue(PrmMng::PARAM_URL_UPLOADS_OLD)
        );
    }

    /**
     * Get old subsits urls array id => url
     *
     * @return string[]
     */
    public function getOldUrlsArrayIdVal(): array
    {
        if (empty($this->subsites)) {
            return [];
        }

        $result = [];

        foreach ($this->subsites as $subsite) {
            $result[$subsite->id] = rtrim($this->getUrlFromSubsiteObj($subsite), '/');
        }
        return $result;
    }

    /**
     * get relative path in archive of wordpress main paths
     *
     * @param string $pathKey (abs,home,plugins ...)
     *
     * @return string
     */
    public function getRelativePathsInArchive($pathKey = null)
    {
        static $realtviePaths = null;

        if (is_null($realtviePaths)) {
            $realtviePaths = (array) $this->getRealValue('archivePaths');
            foreach ($realtviePaths as $key => $path) {
                $realtviePaths[$key] = SnapIO::getRelativePath($path, $this->wpInfo->targetRoot);
            }
        }

        if (!empty($pathKey)) {
            if (array_key_exists($pathKey, $realtviePaths)) {
                return $realtviePaths[$pathKey];
            } else {
                throw new Exception('Invalid path key ' . $pathKey);
            }
        } else {
            return $realtviePaths;
        }
    }

    /**
     *
     * @param string          $path     path to check
     * @param string|string[] $pathKeys path keys to check (abs,home,plugins ...)
     *
     * @return boolean
     */
    public function isChildOfArchivePath($path, $pathKeys = []): bool
    {
        if (is_scalar($pathKeys)) {
            $pathKeys = [$pathKeys];
        }

        $mainPaths = $this->getRelativePathsInArchive();
        foreach ($pathKeys as $key) {
            if (!isset($mainPaths[$key])) {
                continue;
            }

            if (strlen($mainPaths[$key]) == 0) {
                return true;
            }

            if (strpos($path, (string) $mainPaths[$key]) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @return string
     */
    public function getRelativeMuPlugins()
    {
        static $relativePath = null;
        if (is_null($relativePath)) {
            $relativePath = SnapIO::getRelativePath(
                PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_MUPLUGINS_NEW),
                PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_NEW)
            );
        }
        return $relativePath;
    }

    /**
     * return the mapping paths from relative path of archive zip and target folder
     * if exist only one entry return the target folter string
     *
     * @param bool $reset if true recalculater path mappintg
     *
     * @return string|array<string,string>
     */
    public function getPathsMapping($reset = false)
    {
        static $pathsMapping = null;

        if (is_null($pathsMapping) || $reset) {
            $prmMng       = PrmMng::getInstance();
            $pathsMapping = [];

            $targetRootPath = $this->wpInfo->targetRoot;
            $paths          = (array) $this->getRealValue('archivePaths');

            foreach ($paths as $key => $path) {
                if (($relativePath = SnapIO::getRelativePath($path, $targetRootPath)) !== false) {
                    $paths[$key] = $relativePath;
                }
            }
            $pathsMapping[$paths['home']] = $prmMng->getValue(PrmMng::PARAM_PATH_NEW);
            if ($paths['home'] !== $paths['abs']) {
                $pathsMapping[$paths['abs']] = $prmMng->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW);
            } elseif ($prmMng->getValue(PrmMng::PARAM_PATH_NEW) != $prmMng->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW)) {
                // In case ABSPATH and HOME PATH are the same in the source site and different in the destination site is necessary
                // to map only the core files in a different way. site is necessary to change the path of the core files of wordpress
                $rootCoreList = SnapWP::getWpCoreFilesListInFolder();
                $absNew       = $prmMng->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW);
                foreach ($rootCoreList['dirs'] as $dir) {
                    $pathsMapping[$paths['abs'] . $dir] = $absNew . '/' . $dir;
                }
                foreach ($rootCoreList['files'] as $file) {
                    if ($file == 'index.php') {
                        continue;
                    }
                    $pathsMapping[$paths['abs'] . $file] = $absNew . '/' . $file;
                }
            }
            $pathsMapping[$paths['wpcontent']] = $prmMng->getValue(PrmMng::PARAM_PATH_CONTENT_NEW);
            $pathsMapping[$paths['plugins']]   = $prmMng->getValue(PrmMng::PARAM_PATH_PLUGINS_NEW);
            $pathsMapping[$paths['muplugins']] = $prmMng->getValue(PrmMng::PARAM_PATH_MUPLUGINS_NEW);

            switch (InstState::getInstType()) {
                case InstState::TYPE_SINGLE:
                case InstState::TYPE_MSUBDOMAIN:
                case InstState::TYPE_MSUBFOLDER:
                case InstState::TYPE_RBACKUP_SINGLE:
                case InstState::TYPE_RBACKUP_MSUBDOMAIN:
                case InstState::TYPE_RBACKUP_MSUBFOLDER:
                case InstState::TYPE_RECOVERY_SINGLE:
                case InstState::TYPE_RECOVERY_MSUBDOMAIN:
                case InstState::TYPE_RECOVERY_MSUBFOLDER:
                    $pathsMapping[$paths['uploads']] = $prmMng->getValue(PrmMng::PARAM_PATH_UPLOADS_NEW);
                    break;
                case InstState::TYPE_STANDALONE:
                    $pathsMapping[$paths['uploads']] = $prmMng->getValue(PrmMng::PARAM_PATH_UPLOADS_NEW);
                    if (($subsiteId = $prmMng->getValue(PrmMng::PARAM_SUBSITE_ID)) > 1) {
                        $subSiteObj                            = $this->getSubsiteObjById($subsiteId);
                        $pathsMapping[$subSiteObj->uploadPath] = $prmMng->getValue(PrmMng::PARAM_PATH_UPLOADS_NEW);
                    }
                    break;
                case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
                case InstState::TYPE_SINGLE_ON_SUBFOLDER:
                case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
                case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                    /** @var SiteOwrMap[] $overwriteMapping */
                    $overwriteMapping = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_MAPPING);

                    foreach ($overwriteMapping as $map) {
                        if ($map->getTargetId() < 1) {
                            // if it is 0 the site has not been created yet therefore it is skipped
                            continue;
                        }
                        $sourceInfo                              = $map->getSourceSiteInfo();
                        $targetInfo                              = $map->getTargetSiteInfo();
                        $pathsMapping[$sourceInfo['uploadPath']] = $targetInfo['fullUploadPath'];
                    }
                    break;
                case InstState::TYPE_NOT_SET:
                    throw new Exception('Cannot change setup with current installation type [' . InstState::getInstType() . ']');
                default:
                    throw new Exception('Unknown mode');
            }

            // remove all empty values for safe,
            // This should never happen, but if it does, there is a risk that the installer will remove all the files in the server root.
            $pathsMapping = array_filter($pathsMapping, fn($value): bool => strlen($value) > 0);

            $pathsMapping = SnapIO::sortBySubfoldersCount($pathsMapping, true, false, true);

            $unsetKeys = [];
            foreach (array_reverse($pathsMapping) as $oldPathA => $newPathA) {
                foreach ($pathsMapping as $oldPathB => $newPathB) {
                    if ($oldPathA == $oldPathB) {
                        continue;
                    }

                    if (
                        ($relativePathOld = SnapIO::getRelativePath($oldPathA, $oldPathB)) === false ||
                        ($relativePathNew = SnapIO::getRelativePath($newPathA, $newPathB)) === false
                    ) {
                        continue;
                    }

                    if ($relativePathOld == $relativePathNew) {
                        $unsetKeys[] = $oldPathA;
                        break;
                    }
                }
            }
            foreach (array_unique($unsetKeys) as $unsetKey) {
                unset($pathsMapping[$unsetKey]);
            }

            $tempArray    = $pathsMapping;
            $pathsMapping = [];
            foreach ($tempArray as $key => $val) {
                $pathsMapping['/' . $key] = $val;
            }

            switch (count($pathsMapping)) {
                case 0:
                    throw new Exception('Paths archive mapping is inconsistent');
                case 1:
                    $pathsMapping = reset($pathsMapping);
                    break;
                default:
            }

            Log::info("--------------------------------------");
            Log::info('PATHS MAPPING : ' . Log::v2str($pathsMapping));
            Log::info("--------------------------------------");
        }
        return $pathsMapping;
    }

    /**
     * get absolute target path from archive relative path
     *
     * @param string $pathInArchive relative path in archive
     *
     * @return string
     *
     * @throws ErrorException If the entry path contains a traversal segment
     */
    public function destFileFromArchiveName($pathInArchive): string
    {
        if (SnapIO::hasTraversalSegment((string) $pathInArchive)) {
            throw new ErrorException('Illegal archive entry (path traversal): ' . $pathInArchive);
        }

        $pathsMapping = $this->getPathsMapping();

        if (is_string($pathsMapping)) {
            return $pathsMapping . '/' . ltrim($pathInArchive, '\\/');
        }

        if (strlen($pathInArchive) === 0) {
            $pathInArchive = '/';
        } elseif ($pathInArchive[0] != '/') {
            $pathInArchive = '/' . $pathInArchive;
        }

        foreach ($pathsMapping as $archiveMainPath => $newMainPath) {
            if (($relative = SnapIO::getRelativePath($pathInArchive, $archiveMainPath)) !== false) {
                return $newMainPath . (strlen($relative) ? ('/' . $relative) : '');
            }
        }

        // if don't find corrispondance in mapping get the path new as default (this should never happen)
        return PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_NEW) . '/' . ltrim($pathInArchive, '\\/');
    }

    /**
     *
     * @return string[]
     */
    public function invalidCharsets(): array
    {
        return array_diff($this->dbInfo->charSetList, DUPX_DB_Functions::getInstance()->getCharsetsList());
    }

    /**
     *
     * @return string[]
     */
    public function invalidCollations(): array
    {
        return array_diff($this->dbInfo->collationList, DUPX_DB_Functions::getInstance()->getCollationsList());
    }

    /**
     *
     * @return string[] list of MySQL engines in source site not supported by the current database
     */
    public function invalidEngines(): array
    {
        return array_diff($this->dbInfo->engineList, DUPX_DB_Functions::getInstance()->getSupportedEngineList());
    }

    /**
     * Update Wp config by param
     *
     * @param WPConfigTransformer $confTrans wp config transformer
     * @param string              $defineKey define key
     * @param string              $paramKey  param key
     *
     * @return void
     */
    public static function updateWpConfigByParam(WPConfigTransformer $confTrans, $defineKey, $paramKey): void
    {
        $paramsManager = PrmMng::getInstance();
        $wpConfVal     = $paramsManager->getValue($paramKey);
        self::updateWpConfigByValue($confTrans, $defineKey, $wpConfVal);
    }

    /**
     * Update wp-config
     *
     * @param WPConfigTransformer                   $confTrans   wp config transformer
     * @param string                                $defineKey   wp config key
     * @param array{inWpConfig: bool, value: mixed} $wpConfVal   wp config value
     * @param mixed                                 $customValue if is not null custom value overwrite value
     *
     * @return void
     */
    public static function updateWpConfigByValue(WPConfigTransformer $confTrans, $defineKey, $wpConfVal, $customValue = null): void
    {
        if ($wpConfVal['inWpConfig']) {
            $stringVal = '';
            if ($customValue !== null) {
                $stringVal = $customValue;
                $updParam  = [
                    'raw'       => true,
                    'normalize' => true,
                ];
            } else {
                switch (gettype($wpConfVal['value'])) {
                    case "boolean":
                        $stringVal = $wpConfVal['value'] ? 'true' : 'false';
                        $updParam  = [
                            'raw'       => true,
                            'normalize' => true,
                        ];
                        break;
                    case "integer":
                    case "double":
                        $stringVal = (string) $wpConfVal['value'];
                        $updParam  = [
                            'raw'       => true,
                            'normalize' => true,
                        ];
                        break;
                    case "string":
                        $stringVal = $wpConfVal['value'];
                        $updParam  = [
                            'raw'       => false,
                            'normalize' => true,
                        ];
                        break;
                    case "NULL":
                        $stringVal = 'null';
                        $updParam  = [
                            'raw'       => true,
                            'normalize' => true,
                        ];
                        break;
                    case "array":
                    case "object":
                    case "resource":
                    case "resource (closed)":
                    case "unknown type":
                    default:
                        $stringVal = '';
                        $updParam  = [
                            'raw'       => true,
                            'normalize' => true,
                        ];
                        break;
                }
            }

            Log::info('WP CONFIG UPDATE ' . $defineKey . ' ' . Log::v2str($stringVal));
            $confTrans->update('constant', $defineKey, $stringVal, $updParam);
        } else {
            if ($confTrans->exists('constant', $defineKey)) {
                Log::info('WP CONFIG REMOVE ' . $defineKey);
                $confTrans->remove('constant', $defineKey);
            }
        }
    }
}
