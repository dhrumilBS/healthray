<?php

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\PathUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Core\Exceptions\DupliException;

/**
 * Evaluates whether a package or template represents a full backup.
 *
 * A "full backup" includes all required components (DB, Core, Plugins, Themes, Uploads)
 * with no filtered WordPress core directories, database tables, or multisite subsites.
 *
 * This is a core package characteristic used by multiple features:
 * - Recovery points (must also be local)
 * - Staging sites (must also be local and version-compatible)
 * - Cloud recovery (full backup only)
 *
 * @phpstan-type IneligibilityReasons array{
 *     missing_components?: string[],
 *     db_only?: bool,
 *     filtered_wp_dirs?: string[],
 *     filtered_db_tables?: string[],
 *     filtered_subsites?: int[]
 * }
 */
class FullBackupStatus
{
    const TYPE_PACKAGE  = 'PACKAGE';
    const TYPE_TEMPLATE = 'TEMPLATE';

    const COMPONENTS_REQUIRED = [
        BuildComponents::COMP_DB,
        BuildComponents::COMP_CORE,
        BuildComponents::COMP_PLUGINS,
        BuildComponents::COMP_THEMES,
        BuildComponents::COMP_UPLOADS,
    ];

    /** @var AbstractPackage|TemplateEntity */
    protected $object;
    protected string $objectType = '';
    /** @var ?array{dbonly:bool,filterDirs:string[],filterTables:string[],filterSubsites:int[],components:string[]} */
    protected $filteredData;
    /** @var false|TemplateEntity */
    private $activeTemplate = false;

    /**
     * Class constructor
     *
     * @param AbstractPackage|TemplateEntity $object entity object
     */
    public function __construct($object)
    {
        if (is_a($object, AbstractPackage::class)) {
            $this->objectType = self::TYPE_PACKAGE;
        } elseif ($object instanceof TemplateEntity) {
            $this->objectType     = self::TYPE_TEMPLATE;
            $this->activeTemplate = $object;
        } else {
            throw new DupliException('Object must be AbstractPackage or TemplateEntity');
        }
        $this->object = $object;

        // Init filtered data
        $this->getFilteredData();
    }

    /**
     * Get the literal type name based on the object being evaluated
     *
     * @return string Returns the object type literal
     */
    public function getType(): string
    {
        return $this->objectType;
    }

    /**
     * Return the underlying object
     *
     * @return DupPackage|TemplateEntity|object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get the type label based on the object being evaluated
     *
     * @return string Returns the object type by name PACKAGE | TEMPLATE
     */
    public function getTypeLabel(): string
    {
        switch ($this->objectType) {
            case self::TYPE_PACKAGE:
                return self::TYPE_PACKAGE;
            case self::TYPE_TEMPLATE:
                return self::TYPE_TEMPLATE;
        }

        return '';
    }

    /**
     * Return true if current object is a full backup.
     *
     * A full backup has all required components and no filtered WordPress core
     * directories, database tables, or multisite subsites.
     *
     * When $reasons is passed, it is populated with the structured ineligibility data
     * for each failing condition (keys present only when the condition fails).
     *
     * @param IneligibilityReasons $reasons Populated by reference with ineligibility data
     *
     * @return bool
     */
    public function isFullBackup(array &$reasons = []): bool
    {
        $isFull = true;

        if (!$this->hasRequiredComponents()) {
            $isFull                        = false;
            $reasons['missing_components'] = array_values(
                array_diff(self::COMPONENTS_REQUIRED, $this->filteredData['components'])
            );
        }

        if (!$this->isWordPressCoreComplete()) {
            $isFull                      = false;
            $reasons['db_only']          = $this->filteredData['dbonly'];
            $reasons['filtered_wp_dirs'] = $this->filteredData['dbonly']
                ? PathUtil::getWPCoreDirs()
                : $this->filteredData['filterDirs'];
        }

        if (!$this->isDatabaseComplete()) {
            $isFull                        = false;
            $reasons['filtered_db_tables'] = $this->filteredData['filterTables'];
        }

        if (!$this->isMultisiteComplete()) {
            $isFull                       = false;
            $reasons['filtered_subsites'] = $this->filteredData['filterSubsites'];
        }

        return $isFull;
    }

    /**
     * Convert ineligibility reasons array into human-readable messages.
     *
     * @param IneligibilityReasons $reasons Reasons from isFullBackup()
     *
     * @return string[]
     */
    public static function reasonsToMessages(array $reasons): array
    {
        $messages = [];

        if (!empty($reasons['db_only'])) {
            $messages[] = __('Database-only backups cannot be used.', 'duplicator');
        }

        if (!empty($reasons['missing_components'])) {
            $messages[] = sprintf(
                __('Backup is missing required components: %s', 'duplicator'),
                implode(', ', $reasons['missing_components'])
            );
        }

        if (!empty($reasons['filtered_wp_dirs'])) {
            $messages[] = __('Backup has filtered WordPress core directories.', 'duplicator');
        }

        if (!empty($reasons['filtered_db_tables'])) {
            $messages[] = __('Backup has filtered database tables.', 'duplicator');
        }

        if (!empty($reasons['filtered_subsites'])) {
            $messages[] = __('Backup has filtered multisite subsites.', 'duplicator');
        }

        return $messages;
    }

    /**
     * Returns true if backup has all required components
     *
     * @return bool
     */
    public function hasRequiredComponents(): bool
    {
        return array_intersect(
            self::COMPONENTS_REQUIRED,
            $this->filteredData['components']
        ) === self::COMPONENTS_REQUIRED;
    }

    /**
     * Returns true if package has a specific component
     *
     * @param string $component component name
     *
     * @return bool
     */
    public function hasComponent($component): bool
    {
        return in_array($component, $this->filteredData['components']);
    }

    /**
     * Returns true if no WordPress core directories are filtered
     *
     * @return bool
     */
    public function isWordPressCoreComplete(): bool
    {
        return (
            $this->filteredData['dbonly'] == false &&
            count($this->filteredData['filterDirs']) == 0
        );
    }

    /**
     * Returns true if no database tables with WordPress prefix are filtered
     *
     * @return bool
     */
    public function isDatabaseComplete(): bool
    {
        return (count($this->filteredData['filterTables']) == 0);
    }

    /**
     * Returns true if no multisite subsites are filtered
     *
     * @return bool
     */
    public function isMultisiteComplete(): bool
    {
        return (count($this->filteredData['filterSubsites']) == 0);
    }

    /**
     * Return filtered data from entity
     *
     * @return array{dbonly:bool,filterDirs:string[],filterTables:string[],filterSubsites:int[],components:string[]}
     */
    public function getFilteredData()
    {
        if ($this->filteredData === null) {
            $dbOnly        = false;
            $components    = [];
            $filterDirs    = [];
            $filterTables  = [];
            $filterSusites = [];


            switch (get_class($this->object)) {
                case DupPackage::class:
                    $dbOnly     = $this->object->isDBOnly();
                    $components = $this->object->components;

                    if (filter_var($this->object->Archive->FilterOn, FILTER_VALIDATE_BOOLEAN) && strlen($this->object->Archive->FilterDirs) > 0) {
                        $filterDirs = explode(';', $this->object->Archive->FilterDirs);
                        $filterDirs = array_intersect($filterDirs, PathUtil::getWPCoreDirs());
                    }

                    if (
                        filter_var($this->object->Database->FilterOn, FILTER_VALIDATE_BOOLEAN) &&
                        strlen($this->object->Database->FilterTables) > 0
                    ) {
                        $filterTables = SnapWP::getTablesWithPrefix(explode(',', $this->object->Database->FilterTables));
                    }

                    $filterSusites = $this->object->Multisite->FilterSites;
                    break;
                case TemplateEntity::class:
                default:
                    if ($this->activeTemplate === false) {
                        break;
                    }
                    $dbOnly     = BuildComponents::isDBOnly($this->activeTemplate->components);
                    $components = $this->activeTemplate->components;

                    if (
                        filter_var(
                            $this->activeTemplate->archive_filter_on,
                            FILTER_VALIDATE_BOOLEAN
                        ) &&
                        strlen($this->activeTemplate->archive_filter_dirs) > 0
                    ) {
                        $filterDirs = explode(';', $this->activeTemplate->archive_filter_dirs);
                        $filterDirs = array_intersect($filterDirs, PathUtil::getWPCoreDirs());
                    }

                    if (
                        filter_var($this->activeTemplate->database_filter_on, FILTER_VALIDATE_BOOLEAN) &&
                        strlen($this->activeTemplate->database_filter_tables) > 0
                    ) {
                        $filterTables = SnapWP::getTablesWithPrefix(explode(',', $this->activeTemplate->database_filter_tables));
                    }

                    $filterSusites = $this->activeTemplate->getExtraData('filter_sites', []);
                    break;
            }

            $this->filteredData = [
                'dbonly'         => $dbOnly,
                'filterDirs'     => $filterDirs,
                'filterTables'   => $filterTables,
                'filterSubsites' => $filterSusites,
                'components'     => $components,
            ];
        }

        return $this->filteredData;
    }
}
