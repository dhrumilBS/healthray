<?php

namespace Duplicator\Package\Create;

use Duplicator\Package\AbstractPackage;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Installer\Package\PComponents;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Core\Exceptions\DupliException;

class BuildComponents extends PComponents
{
    const COMP_ACTION_ALL = 'all';
    const COMP_ACTION_DB  = 'database';

    const COMPONENTS_ACTIONS = [
        self::COMP_ACTION_ALL,
        self::COMP_ACTION_DB,
    ];

    /** @var string[] */
    protected $components = [];
    /** @var array{dirs:string[],files:string[]}  */
    protected $filters = [
        'dirs'  => [],
        'files' => [],
    ];

    /** @var string Reason emitted by the most recent directory scan failure */
    protected $scanDirectoryError = '';

    /**
     * Class contructor
     *
     * @param string[] $components The components to build
     */
    public function __construct($components)
    {
        $this->components = (array) $components;
        if (in_array(self::COMP_PLUGINS_ACTIVE, $this->components)) {
            $this->components = array_diff($this->components, [self::COMP_PLUGINS]);
        }

        if (in_array(self::COMP_THEMES_ACTIVE, $this->components)) {
            $this->components = array_diff($this->components, [self::COMP_THEMES]);
        }
        $this->components = array_values($this->components);

        DupLog::trace("Components: " . implode(', ', $this->components));
        $this->generateFilters();
    }

    /**
     * Generate filter listh paths
     *
     * @return void
     */
    public function generateFilters(): void
    {
        $this->filters = [
            'dirs'  => [],
            'files' => [],
        ];

        if (!in_array(self::COMP_CORE, $this->components)) {
            $filters                = $this->getCoreComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        if (!in_array(self::COMP_DB, $this->components)) {
            $filters                = $this->getDBComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        if (in_array(self::COMP_PLUGINS_ACTIVE, $this->components)) {
            $filters                = $this->getInactivePluginsComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        } elseif (!in_array(self::COMP_PLUGINS, $this->components)) {
            $filters                = $this->getPluginsComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        if (in_array(self::COMP_THEMES_ACTIVE, $this->components)) {
            $filters                = $this->getInactiveThemesComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        } elseif (!in_array(self::COMP_THEMES, $this->components)) {
            $filters                = $this->getThemesComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        if (!in_array(self::COMP_UPLOADS, $this->components)) {
            $filters                = $this->getMediaComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        if (!in_array(self::COMP_OTHER, $this->components)) {
            $filters                = $this->getOtherComponentPaths();
            $this->filters['dirs']  = array_merge($this->filters['dirs'], $filters['dirs']);
            $this->filters['files'] = array_merge($this->filters['files'], $filters['files']);
        }

        $this->filters['dirs']  = array_unique($this->filters['dirs']);
        $this->filters['files'] = array_unique($this->filters['files']);
    }

    /**
     * Returns a list of files path to filter
     *
     * @return string[]
     */
    public function getFiltersFiles()
    {
        return $this->filters['files'];
    }

    /**
     * Returns a list of dirs path to filter
     *
     * @return string[]
     */
    public function getFiltersDirs()
    {
        return $this->filters['dirs'];
    }

    /**
     * Returns core directories and files of the WP install
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getCoreComponentPaths(): array
    {
        $paths         = [
            'dirs'  => [],
            'files' => [],
        ];
        $absRootPaths  = SnapWP::getWpCoreFilesListInFolder();
        $paths['dirs'] = array_map(fn($item): string => WpArchiveUtils::getArchiveListPaths('abs') . '/' . $item, $absRootPaths['dirs']);

        $paths['files'] = array_map(fn($item): string => WpArchiveUtils::getArchiveListPaths('abs') . '/' . $item, $absRootPaths['files']);

        foreach (SnapWP::getWPContentCoreDirs() as $dir) {
            $paths['dirs'][] = WpArchiveUtils::getArchiveListPaths('wpcontent') . '/' . $dir;
        }
        return $paths;
    }

    /**
     * Returns database directories and files of the WP install
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getDBComponentPaths(): array
    {
        return [
            'dirs'  => [],
            'files' => [],
        ];
    }


    /**
     * Returns directories and file list of the Plugin component which includes all non-core files and folders
     * in the wp-content folder and the plugins and mu-plugins folders which can be outside of wp-content
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getPluginsComponentPaths(): array
    {
        $paths         = [
            'dirs'  => [
                WpArchiveUtils::getArchiveListPaths('plugins'),
                WpArchiveUtils::getArchiveListPaths('muplugins'),
            ],
            'files' => [],
        ];
        $excludePaths  = [
            WpArchiveUtils::getArchiveListPaths('themes'),
            WpArchiveUtils::getArchiveListPaths('uploads'),
        ];
        $wpContentPath = WpArchiveUtils::getArchiveListPaths('wpcontent');
        if (($wpContentItems = $this->scanDirectory($wpContentPath)) === false) {
            throw $this->createDirectoryScanException(
                $wpContentPath,
                'SCAN ERROR: Could not read the wp-content folder contents.'
            );
        }
        foreach ($wpContentItems as $basename) {
            $tmpPath = $wpContentPath . '/' . $basename;
            if (
                $basename === '.'
                || $basename === '..'
                || in_array($tmpPath, $excludePaths)
                || in_array($basename, SnapWP::getWPContentCoreDirs())
            ) {
                continue;
            }

            if (is_dir($tmpPath)) {
                $paths['dirs'][] = $tmpPath;
            } else {
                $paths['files'][] = $tmpPath;
            }
        }

        return $paths;
    }

    /**
     * Directory and file list path of inactive plugins
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getInactivePluginsComponentPaths(): array
    {
        $paths = [
            'dirs'  => [],
            'files' => [],
        ];

        foreach (SnapWP::getPluginsInfo(SnapWP::PLUGIN_INFO_INACTIVE) as $slug => $info) {
            if (SnapString::contains($slug, '/')) {
                $paths['dirs'][] = WpArchiveUtils::getArchiveListPaths('plugins') . '/' . dirname($slug);
            } else {
                $paths['files'][] = WpArchiveUtils::getArchiveListPaths('plugins') . '/' . $slug;
            }
        }
        DupLog::traceObject("Inactive plugins:", $paths);

        return $paths;
    }

    /**
     * Directory and file list path of themes
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getThemesComponentPaths(): array
    {
        return [
            'dirs'  => [WpArchiveUtils::getArchiveListPaths('themes')],
            'files' => [],
        ];
    }

    /**
     * Directory and file list path of inactive themes
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getInactiveThemesComponentPaths(): array
    {
        $inactiveThemes = [
            'dirs'  => [],
            'files' => [],
        ];
        foreach (SnapWP::getThemesInfo() as $info) {
            if ($info['isActive'] === true || (is_array($info['isActive']) && !empty($info['isActive']))) {
                continue;
            }

            $inactiveThemes['dirs'][] = WpArchiveUtils::getArchiveListPaths('themes') . '/' . $info['slug'];
        }

        return $inactiveThemes;
    }

    /**
     * Returns the paths related to the media Backup component
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getMediaComponentPaths(): array
    {
        return [
            'dirs'  => [WpArchiveUtils::getArchiveListPaths('uploads')],
            'files' => [],
        ];
    }

    /**
     * Returns file and directory and file path lists of the "Other" package component
     *
     * @return array{dirs: string[], files: string[]}
     */
    private function getOtherComponentPaths(): array
    {
        $paths = [
            'dirs'  => [],
            'files' => [],
        ];

        $absPath   = WpArchiveUtils::getArchiveListPaths('abs');
        $corePaths = SnapWP::getWpCoreFilesListInFolder();
        if (($absItems = $this->scanDirectory($absPath)) === false) {
            throw $this->createDirectoryScanException(
                $absPath,
                'SCAN ERROR: Could not read the WordPress root folder contents.'
            );
        }
        foreach ($absItems as $basename) {
            $tmpPath = $absPath . '/' . $basename;
            if (
                $basename === '.'
                || $basename === '..'
                || in_array($basename, $corePaths['dirs'])
                || in_array($basename, $corePaths['files'])
                || $tmpPath === WpArchiveUtils::getArchiveListPaths('wpcontent')
            ) {
                continue;
            }

            if (is_dir($tmpPath)) {
                $paths['dirs'][] = $tmpPath;
            } else {
                $paths['files'][] = $tmpPath;
            }
        }

        return $paths;
    }

    /**
     * Read a component directory.
     *
     * @param string $path Directory path
     *
     * @return string[]|false
     */
    protected function scanDirectory(string $path)
    {
        $result                   = SnapIO::callWithPhpErrorCapture(fn() => scandir($path));
        $error                    = error_get_last();
        $this->scanDirectoryError = is_array($error) ? (string) $error['message'] : '';
        error_clear_last();

        return $result;
    }

    /**
     * Create the failure matching a directory scan result.
     *
     * @param string $path       Directory that could not be listed
     * @param string $rawMessage Stable raw failure message
     *
     * @return DupliException
     */
    private function createDirectoryScanException(string $path, string $rawMessage): DupliException
    {
        $reason            = $this->scanDirectoryError;
        $openBasedirDenied = stripos($reason, 'open_basedir restriction in effect') !== false;
        $permissionDenied  = !$openBasedirDenied && is_dir($path) && (
            !is_readable($path)
            || stripos($reason, 'permission denied') !== false
            || stripos($reason, 'access is denied') !== false
        );

        if ($reason !== '') {
            $rawMessage .= ' Reason: ' . $reason;
        }

        if (!$openBasedirDenied && !$permissionDenied) {
            return new DupliException(
                $rawMessage,
                DupliException::CODE_SCAN_FAILED,
                sprintf(__('Could not read the contents of the folder "%1$s" during the backup scan.', 'duplicator'), $path)
            );
        }

        return new DupliException(
            $rawMessage,
            DupliException::CODE_SCAN_SOURCE_UNREADABLE,
            sprintf(
                __(
                    'Duplicator does not need write access to the source folder "%1$s", but PHP must be able to read and list it
                    and traverse its parent folders. Check those permissions with your hosting provider. If only scheduled Backups
                    fail, ask the host to compare the PHP execution environments used by scheduled and manual Backups.',
                    'duplicator'
                ),
                $path
            )
        );
    }

    /**
     * Get label by compoentent
     *
     * @param string $component The component
     *
     * @return string
     */
    public static function getLabel($component): string
    {
        switch ($component) {
            case self::COMP_DB:
                return __('Database', 'duplicator');
            case self::COMP_CORE:
                return __('Core', 'duplicator');
            case self::COMP_PLUGINS:
                return __('Plugins', 'duplicator');
            case self::COMP_PLUGINS_ACTIVE:
                return __('Only Active Plugins', 'duplicator');
            case self::COMP_THEMES:
                return __('Themes', 'duplicator');
            case self::COMP_THEMES_ACTIVE:
                return __('Only Active Themes', 'duplicator');
            case self::COMP_UPLOADS:
                return __('Media', 'duplicator');
            case self::COMP_OTHER:
                return __('Other', 'duplicator');
            default:
                throw new DupliException('Invalid component: ' . $component);
        }
    }

    /**
     * Get label by compoentent action
     *
     * @param string $componentAction The component action
     * @param bool   $icon            If true, the label will include an icon
     *
     * @return string
     */
    public static function getActionLabel($componentAction, $icon = false): string
    {
        $result = '';
        if ($icon) {
            switch ($componentAction) {
                case self::COMP_ACTION_ALL:
                    $result .= '<i class="fa-solid fa-square-check"></i>';
                    break;
                case self::COMP_ACTION_DB:
                    $result .= '<i class="fa-solid fa-database"></i>';
                    break;
                default:
                    $result .= (string) apply_filters('duplicator_component_action_icon', '', $componentAction);
                    break;
            }
            if ($result !== '') {
                $result .= ' ';
            }
        }

        switch ($componentAction) {
            case self::COMP_ACTION_ALL:
                $result .= __('Full Site', 'duplicator');
                break;
            case self::COMP_ACTION_DB:
                $result .= __('Database Only', 'duplicator');
                break;
            default:
                $result .= (string) apply_filters('duplicator_component_action_label', '', $componentAction);
                break;
        }

        return $result;
    }

    /**
     * Get label by compoentent action
     *
     * @param string[] $components The active components
     *
     * @return string Enum of the component action (COMP_ACTION_*)
     */
    public static function getActionFromComponents($components): string
    {
        if (count($components) === 1 && $components[0] === self::COMP_DB) {
            return self::COMP_ACTION_DB;
        }

        if (array_diff(self::COMPONENTS_DEFAULT, $components) === []) {
            return self::COMP_ACTION_ALL;
        }

        return (string) apply_filters('duplicator_component_action_from_components', '', $components);
    }

    /**
     * Get the components a component action selects
     *
     * @param string $action Enum of the component action (COMP_ACTION_*)
     *
     * @return string[] The active components
     */
    public static function getComponentsFromAction(string $action): array
    {
        return ($action === self::COMP_ACTION_DB) ? [self::COMP_DB] : self::COMPONENTS_DEFAULT;
    }

    /**
     * Return the single AbstractPackage::FLAG_* constant that describes the shape of a package
     * with the given component set. Exactly one shape per package.
     *
     * Logic is self-contained and does not depend on addons, so the shape flag is computed
     * consistently even when ProBase is disabled.
     *
     * @param string[] $components active components
     *
     * @return string one of AbstractPackage::FLAG_FULL_BACKUP, FLAG_DB_ONLY, FLAG_MEDIA_ONLY, FLAG_CUSTOM_COMPONENTS
     */
    public static function getShapeFlag(array $components): string
    {
        if (self::isDBOnly($components)) {
            return AbstractPackage::FLAG_DB_ONLY;
        }
        if (self::isMediaOnly($components)) {
            return AbstractPackage::FLAG_MEDIA_ONLY;
        }
        if (array_diff(self::COMPONENTS_DEFAULT, $components) === []) {
            return AbstractPackage::FLAG_FULL_BACKUP;
        }
        return AbstractPackage::FLAG_CUSTOM_COMPONENTS;
    }

    /**
     * Get preset actions for the UI, filterable by addons.
     *
     * Core defines Full Site and Database Only. Addons add additional presets (e.g. Media Only, Custom).
     *
     * @return array<array{value: string, disabled: bool}>
     */
    public static function getPresetActions(): array
    {
        $actions = [];
        foreach (self::COMPONENTS_ACTIONS as $action) {
            $actions[] = [
                'value'    => $action,
                'disabled' => false,
            ];
        }

        return apply_filters('duplicator_component_preset_actions', $actions);
    }

    /**
     * Returns the Backup components from the input array
     *
     * @param string[] $input the input array $_POST, $_GET, etc.
     *
     * @return string[]
     */
    public static function getFromInput($input): array
    {
        $components = [];
        foreach (self::COMPONENTS as $component) {
            if (isset($input[$component])) {
                $components[] = $component;
            }
        }

        return apply_filters('duplicator_package_components_from_input', $components, $input);
    }
}
