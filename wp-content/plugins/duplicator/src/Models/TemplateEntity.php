<?php

namespace Duplicator\Models;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\Models\AbstractEntity;
use Duplicator\Core\Models\TraitEntitySerializationEncryption;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;
use Duplicator\Core\Models\TraitGenericModelList;
use Duplicator\Core\Models\UpdateFromInputInterface;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\NameFormat;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Duplicator\Libs\WpUtils\PathUtil;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Utils\Settings\ModelMigrateSettingsInterface;
use Exception;
use ReflectionClass;

class TemplateEntity extends AbstractEntity implements UpdateFromInputInterface, ModelMigrateSettingsInterface
{
    use TraitGenericModelList {
        getAll as traitGetAll;
    }
    use TraitEntitySerializationEncryption;

    /**
     * Encrypted properties for template entities
     *
     * @var string[]
     */
    protected static array $encryptedProperties = ['installerPassowrd'];

    /** @var string */
    public $name = '';
    /** @var string  */
    public $package_name_format = NameFormat::DEFAULT_FORMAT;
    /** @var string */
    public $notes = '';
    //ARCHIVE:Files
    /** @var bool */
    public $archive_export_onlydb = false;
    /** @var bool */
    public $archive_filter_on = false;
    /** @var string */
    public $archive_filter_dirs = '';
    /** @var string */
    public $archive_filter_exts = '';
    /** @var string */
    public $archive_filter_files = '';
    /** @var bool */
    public $archive_filter_names = false;
    /** @var string[] */
    public $components = [];
    //ARCHIVE:Database
    /** @var bool */
    public $database_filter_on = false;  // Enable Table Filters
    /** @var bool */
    public $databasePrefixFilter = false;  // If true exclude tables without prefix
    /** @var bool */
    public $databasePrefixSubFilter = false;  // If true exclude unexisting subsite id tables
    /** @var string */
    public $database_filter_tables = ''; // List of filtered tables
    /** @var string */
    public $database_compatibility_modes = ''; // Older style sql compatibility
    //INSTALLER
    //Setup
    /** @var int */
    public $installer_opts_secure_on = 0;  // Enable Password Protection
    /** @var string */
    public $installerPassowrd = ''; // Password Protection password
    /** @var bool */
    public $installer_opts_skip_scan = false;  // Skip Scanner
    //Basic DB
    /** @var string */
    public $installer_opts_db_host = '';   // MySQL Server Host
    /** @var string */
    public $installer_opts_db_name = '';   // Database
    /** @var string */
    public $installer_opts_db_user = '';   // User
    //cPanel Login
    /** @var bool */
    public $installer_opts_cpnl_enable = false;
    /** @var string */
    public $installer_opts_cpnl_host = '';
    /** @var string */
    public $installer_opts_cpnl_user = '';
    /** @var string */
    public $installer_opts_cpnl_pass = '';
    //cPanel DB
    /** @var string */
    public $installer_opts_cpnl_db_action = 'create';
    /** @var string */
    public $installer_opts_cpnl_db_host = '';
    /** @var string */
    public $installer_opts_cpnl_db_name = '';
    /** @var string */
    public $installer_opts_cpnl_db_user = '';
    /** @var array<string, mixed> Addon-managed extra data */
    public $extraData = [];
    /** @var bool */
    public $is_default = false;
    /** @var bool */
    public $is_manual = false;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->name       = __('New Template', 'duplicator');
        $this->components = BuildComponents::COMPONENTS_DEFAULT;
    }

    /**
     * Entity type
     *
     * @return string
     */
    public static function getType(): string
    {
        return 'Package_Template_Entity';
    }

    /**
     * Get addon extra data value
     *
     * @param string $key     data key
     * @param mixed  $default default value if key not set
     *
     * @return mixed
     */
    public function getExtraData(string $key, $default = null)
    {
        return $this->extraData[$key] ?? $default;
    }

    /**
     * Set addon extra data value
     *
     * @param string $key   data key
     * @param mixed  $value value to set
     *
     * @return void
     */
    public function setExtraData(string $key, $value): void
    {
        $this->extraData[$key] = $value;
    }

    /**
     * Serialize the template entity
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        $data = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);

        // Encrypt properties using trait
        $data = $this->encryptSerializedProperties($data);

        return $data;
    }

    /**
     * Unserialize the template entity
     *
     * @param array<string,mixed> $data Serialized data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        // Decrypt properties
        $data = $this->decryptSerializedProperties($data);

        // Type conversions for backward compatibility
        if (isset($data['database_compatibility_modes']) && is_array($data['database_compatibility_modes'])) {
            // for old version compatibility
            $data['database_compatibility_modes'] = implode(',', $data['database_compatibility_modes']);
        }

        if (isset($data['archive_filter_dirs'])) {
            $data['archive_filter_dirs'] = (string) $data['archive_filter_dirs'];
        }
        if (isset($data['archive_filter_files'])) {
            $data['archive_filter_files'] = (string) $data['archive_filter_files'];
        }
        if (isset($data['archive_filter_exts'])) {
            $data['archive_filter_exts'] = (string) $data['archive_filter_exts'];
        }
        if (isset($data['archive_filter_on'])) {
            $data['archive_filter_on'] = filter_var($data['archive_filter_on'], FILTER_VALIDATE_BOOLEAN);
        }
        if (isset($data['database_filter_on'])) {
            $data['database_filter_on'] = filter_var($data['database_filter_on'], FILTER_VALIDATE_BOOLEAN);
        }
        // Assign properties
        foreach ($data as $pName => $val) {
            if (!property_exists($this, $pName)) {
                continue;
            }
            $this->$pName = $val;
        }
    }

    /**
     * Legacy decryption for installer password
     *
     * @param array<string,mixed> $data Serialized data
     *
     * @return array<string,mixed> Data with legacy format decrypted
     */
    protected function legacyDecryptProperties(array $data): array
    {
        // Decrypt installerPassowrd
        if (isset($data['installerPassowrd'])) {
            $data['installerPassowrd'] = CryptBlowfish::decryptIfAvaiable($data['installerPassowrd'], null, true);
        }

        return $data;
    }

    /**
     * To export data
     *
     * @return array<string,mixed>
     */
    public function settingsExport(): array
    {
        return JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
    }

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport(array $data, string $dataVersion, array $extraData = []): bool
    {
        $skipProps = ['id'];

        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (!isset($data[$prop->getName()])) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($this, $data[$prop->getName()]);
        }

        if (!isset($data['components'])) {
            // Allow import of older templsates that did not have Backup components
            if ($this->archive_export_onlydb) {
                $this->components = [BuildComponents::COMP_DB];
            } else {
                $this->components = BuildComponents::COMPONENTS_DEFAULT;
            }
        }

        return true;
    }

    /**
     * Create default template
     *
     * @return void
     */
    public static function createDefault(): void
    {
        if (self::getDefaultTemplate() == null) {
            $template = new self();

            $template->name       = __('Default', 'duplicator');
            $template->notes      = __('The default template.', 'duplicator');
            $template->is_default = true;

            $template->save();
            DupLog::trace('Created default template');
        } else {
            // Update it
            DupLog::trace('Default template already exists so not creating');
        }
    }

    /**
     * Set data from query input
     *
     * @param int $type One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV, SnapUtil::INPUT_REQUEST
     *
     * @return bool true on success or false on failure
     */
    public function setFromInput(int $type): bool
    {
        $input = SnapUtil::getInputFromType($type);
        $this->setFromArrayKey(
            $input,
            function ($key, $val) {
                if (is_string($val)) {
                    $val = stripslashes($val);
                }
                return (is_scalar($val) ? SnapUtil::sanitizeNSChars($val) : $val);
            }
        );
        $this->components = BuildComponents::getFromInput($input);

        $this->database_filter_tables = isset($input['dbtables-list']) ? SnapUtil::sanitizeNSCharsNewlineTrim($input['dbtables-list']) : '';

        if (isset($input['filter-paths'])) {
            $filterPaths                = SnapUtil::sanitizeNSChars($input['filter-paths']);
            $this->archive_filter_dirs  = PackageArchive::parseDirectoryFilter($filterPaths);
            $this->archive_filter_files = PackageArchive::parseFileFilter($filterPaths);
        } else {
            $this->archive_filter_dirs  = '';
            $this->archive_filter_files = '';
        }

        if (isset($input['filter-exts'])) {
            $post_filter_exts          = SnapUtil::sanitizeNSCharsNewlineTrim($input['filter-exts']);
            $this->archive_filter_exts = PackageArchive::parseExtensionFilter($post_filter_exts);
        } else {
            $this->archive_filter_exts = '';
        }

        //Archive
        $this->archive_filter_on       = isset($input['filter-on']);
        $this->database_filter_on      = isset($input['dbfilter-on']);
        $this->databasePrefixFilter    = isset($input['db-prefix-filter']);
        $this->databasePrefixSubFilter = isset($input['db-prefix-sub-filter']);
        $this->archive_filter_names    = isset($input['archive_filter_names']);

        //Installer
        $this->installer_opts_secure_on = filter_input(INPUT_POST, 'secure-on', FILTER_VALIDATE_INT);
        switch ($this->installer_opts_secure_on) {
            case ArchiveDescriptor::SECURE_MODE_NONE:
            case ArchiveDescriptor::SECURE_MODE_INST_PWD:
            case ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT:
                break;
            default:
                throw new Exception(__('Select valid secure mode', 'duplicator'));
        }
        $this->installer_opts_skip_scan   = isset($input['_installer_opts_skip_scan']);
        $this->installer_opts_cpnl_enable = isset($input['installer_opts_cpnl_enable']);

        $this->installerPassowrd = SnapUtil::sanitizeNSCharsNewline(stripslashes($input['secure-pass']));
        $this->notes             = SnapUtil::sanitizeNSCharsNewlineTrim(stripslashes($input['package-notes']));

        do_action('duplicator_template_after_set_from_post', $this, $input);

        return true;
    }

    /**
     * Copy template from id
     *
     * @param int<0, max> $templateId template id
     *
     * @return void
     */
    public function copyFromSourceId(int $templateId): void
    {
        if (($source = self::getById($templateId)) === false) {
            throw new Exception('Can\'t get tempalte id' . $templateId);
        }

        $skipProps = [
            'id',
            'is_manual',
            'is_default',
        ];

        if ($this->getId() > 0) {
            $skipProps[] = 'name';
        }

        $reflect = new ReflectionClass($this);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($this, $prop->getValue($source));
        }

        $source_template_name = $source->is_manual ? __("Active Build Settings", 'duplicator') : $source->name;
        if ($this->getId() < 0) {
            $this->name = sprintf(__('%1$s - Copy', 'duplicator'), $source_template_name);
        }
    }

    /**
     * Gets a list of core WordPress folders that have been filtered
     *
     * @return string[] Returns and array of folders paths
     */
    public function getWordPressCoreFilteredFoldersList(): array
    {
        return array_intersect(explode(';', $this->archive_filter_dirs), PathUtil::getWPCoreDirs());
    }

    /**
     * Is any of the WordPress core folders in the folder filter list
     *
     * @return bool Returns true if a WordPress core path is being filtered
     */
    public function isWordPressCoreFolderFiltered(): bool
    {
        return count($this->getWordPressCoreFilteredFoldersList()) > 0;
    }

    /**
     * Get all entities of current type
     *
     * @param int<0, max>                          $page           current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max>                          $pageSize       page size, 0 return all entities
     * @param callable                             $sortCallback   sort function on items result
     * @param callable                             $filterCallback filter on items result
     * @param array{'col': string, 'mode': string} $orderby        query ordder by
     *
     * @return static[]|false return entities list of false on failure
     */
    public static function getAll(
        $page = 0,
        $pageSize = 0,
        $sortCallback = null,
        $filterCallback = null,
        $orderby = [
            'col'  => 'id',
            'mode' => 'ASC',
        ]
    ) {
        if (is_null($sortCallback)) {
            $sortCallback = function (self $a, self $b): int {
                if ($a->is_default) {
                    return -1;
                } elseif ($b->is_default) {
                    return 1;
                } else {
                    return strcasecmp($a->name, $b->name);
                }
            };
        }
        return self::traitGetAll($page, $pageSize, $sortCallback, $filterCallback, $orderby);
    }

    /**
     * Return list template json encoded data for javascript
     *
     * @param bool          $echo      echo data or return
     * @param static[]|null $templates pre-fetched templates to avoid duplicate query, null to fetch
     *
     * @return string
     */
    public static function getTemplatesFrontendListData(bool $echo = true, ?array $templates = null): string
    {
        if ($templates === null) {
            $templates = self::getAll();
        }
        $result = JsonSerialize::serialize($templates, JsonSerialize::JSON_SKIP_MAGIC_METHODS | JsonSerialize::JSON_SKIP_CLASS_NAME);

        if ($echo) {
            echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            return '';
        } else {
            return $result;
        }
    }

    /**
     * Get all entities of current type
     *
     * @param int<0, max> $page     current page, if $pageSize is 0 o 1 $pase is the offset
     * @param int<0, max> $pageSize page size, 0 return all entities
     *
     * @return static[]|false return entities list of false on failure
     */
    public static function getAllWithoutManualMode(
        $page = 0,
        $pageSize = 0
    ) {
        $filterManualCallback = (fn(self $obj): bool => $obj->is_manual === false);
        return self::getAll($page, $pageSize, null, $filterManualCallback);
    }

    /**
     * Get default template if exists
     *
     * @return ?self
     */
    public static function getDefaultTemplate()
    {
        $templates = self::getAll();

        foreach ($templates as $template) {
            if ($template->is_default) {
                return $template;
            }
        }
        return null;
    }

    /**
     * Return manual template
     *
     * @return self
     */
    public static function getManualTemplate(): self
    {
        static $manualTemplate = null;

        if ($manualTemplate === null) {
            $templates = self::getAll();

            foreach ($templates as $template) {
                if ($template->is_manual) {
                    $manualTemplate = $template;
                    break;
                }
            }

            if ($manualTemplate === null) {
                $manualTemplate = self::createManual();
            }
        }

        return $manualTemplate;
    }

    /**
     * Create manual mode template
     *
     * @return self
     */
    protected static function createManual(): self
    {
        $template = new self();

        $template->name      = __('[Manual Mode]', 'duplicator');
        $template->notes     = '';
        $template->is_manual = true;

        $template->save();
        DupLog::trace('Created manual mode template');
        return $template;
    }
}
