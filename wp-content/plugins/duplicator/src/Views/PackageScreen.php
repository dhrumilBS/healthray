<?php

namespace Duplicator\Views;

use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Models\TemplateEntity;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Create\BuildComponents;
use WP_Screen;

class PackageScreen extends ScreenBase
{
    /**
     * Display sections shared by the flags cell tooltip and the Status column legend, in display order
     */
    const FLAGS_SECTION_ORIGIN  = 'origin';
    const FLAGS_SECTION_SHAPE   = 'shape';
    const FLAGS_SECTION_STORAGE = 'storage';
    const FLAGS_SECTION_OTHER   = 'other';
    const FLAGS_SECTIONS_ORDER  = [
        self::FLAGS_SECTION_ORIGIN,
        self::FLAGS_SECTION_SHAPE,
        self::FLAGS_SECTION_STORAGE,
        self::FLAGS_SECTION_OTHER,
    ];

    /**
     * Class contructor
     *
     * @param string $page Page
     *
     * @return void
     */
    public function __construct($page)
    {
        add_action('load-' . $page, [$this, 'init']);
        add_filter('screen_settings', [$this, 'showOptions'], 10, 2);
    }

    /**
     * Init Backup screen
     *
     * @return void
     */
    public function init(): void
    {
        add_action('admin_head', [self::class, 'displayColsCss']);
    }

    /**
     * Display columns css
     *
     * @return void
     */
    public static function displayColsCss(): void
    {
        $uiOpts = UserUIOptions::getInstance();

        $showNote    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_NOTE);
        $showSize    = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_SIZE);
        $showCreated = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_CREATED);
        $showAge     = $uiOpts->get(UserUIOptions::VAL_SHOW_COL_AGE);
        ?>
        <style>
            <?php if (!$showNote) { ?>
                .dup-packtbl .dup-note-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showSize) { ?>
                .dup-packtbl .dup-size-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showCreated) { ?>
                .dup-packtbl .dup-created-column {
                    display: none;
                }
            <?php } ?>

            <?php if (!$showAge) { ?>
                .dup-packtbl .dup-age-column {
                    display: none;
                }
            <?php } ?>
        </style>
        <?php
    }

    /**
     * Packages List: Screen Options Tab
     *
     * @param string    $screen_settings Screen settings
     * @param WP_Screen $args            Screen args
     *
     * @return string
     */
    public function showOptions($screen_settings, WP_Screen $args)
    {


        // Only display on Backups screen and not build screens
        if (
            !PackagesPageController::getInstance()->isCurrentPage() ||
            PackagesPageController::getCurrentInnerPage(PackagesPageController::LIST_INNER_PAGE_LIST) !== PackagesPageController::LIST_INNER_PAGE_LIST
        ) {
            return $screen_settings;
        }

        return TplMng::getInstance()->render('admin_pages/packages/screen_options', [], false);
    }

    /**
     * Set duplicator screen option
     *
     * @param mixed  $screen_option The value to save instead of the option value. Default false (to skip saving the current option).
     * @param string $option        The option name.
     * @param int    $value         The option value.
     *
     * @return bool
     */
    public static function setScreenOptions($screen_option, $option, $value): bool
    {
        $uiOpts = UserUIOptions::getInstance();

        $perPage = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'dupli-per-page', 10);
        $perPage = max(10, $perPage); // Minimum 10 entries per page

        $uiOpts->set(UserUIOptions::VAL_PACKAGES_PER_PAGE, $perPage);

        $dateFormat = SnapUtil::sanitizeIntInput(SnapUtil::INPUT_REQUEST, 'dupli-created-format', 1);
        $uiOpts->set(UserUIOptions::VAL_CREATED_DATE_FORMAT, $dateFormat);

        $showNote = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dupli-note-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_NOTE, $showNote);

        $showSize = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dupli-size-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_SIZE, $showSize);

        $showCreated = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dupli-created-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_CREATED, $showCreated);

        $showAge = SnapUtil::sanitizeBoolInput(SnapUtil::INPUT_REQUEST, 'dupli-age-hide');
        $uiOpts->set(UserUIOptions::VAL_SHOW_COL_AGE, $showAge);

        $uiOpts->save();

        // Returning false from the filter will skip saving the current option
        return false;
    }

    /**
     * Build the list of icons rendered in the package flags cell, in display order: origin,
     * shape, storage, then the remaining markers; addon icons are appended by the
     * `duplicator_package_flags_icons` action. The origin, shape and storage icons share the
     * aggregated "Backup details" tooltip, the others carry their own.
     * An empty tooltipTitle renders a headerless tooltip; an empty onclick means not clickable.
     *
     * @param AbstractPackage $package The package
     *
     * @return array<int, array{icon: string, class: string, tooltip: string, tooltipTitle: string, onclick: string}>
     */
    public static function getFlagsCellIcons(AbstractPackage $package): array
    {
        $detailsTooltip = TplMng::getInstance()->render(
            'admin_pages/packages/row_parts/flags_tooltip',
            ['tooltipSections' => self::getFlagsTooltipSections($package)],
            false
        );
        $detailsTitle   = esc_attr__('Backup details', 'duplicator');

        $icons   = [];
        $icons[] = [
            'icon'         => self::getOriginGroup($package)['icon'],
            'class'        => '',
            'tooltip'      => $detailsTooltip,
            'tooltipTitle' => $detailsTitle,
            'onclick'      => '',
        ];
        $icons[] = [
            'icon'         => self::getShapeIcon($package),
            'class'        => '',
            'tooltip'      => $detailsTooltip,
            'tooltipTitle' => $detailsTitle,
            'onclick'      => '',
        ];

        $storageClick = sprintf(
            "DupliJs.Pack.ShowRemote(%d, '%s');",
            $package->getId(),
            esc_js($package->getNameHash())
        );
        if ($package->hasFlag(AbstractPackage::FLAG_HAVE_LOCAL)) {
            $icons[] = [
                'icon'         => 'fa-solid fa-hard-drive',
                'class'        => 'cursor-pointer',
                'tooltip'      => $detailsTooltip,
                'tooltipTitle' => $detailsTitle,
                'onclick'      => $storageClick,
            ];
        }
        if ($package->hasFlag(AbstractPackage::FLAG_HAVE_REMOTE)) {
            $icons[] = [
                'icon'         => 'fa-solid fa-cloud',
                'class'        => 'cursor-pointer remote-storage-flag',
                'tooltip'      => $detailsTooltip,
                'tooltipTitle' => $detailsTitle,
                'onclick'      => $storageClick,
            ];
        }
        $icons[] = self::getSecurityLockIcon($package);
        if ($package->hasFlag(AbstractPackage::FLAG_CREATED_AFTER_RESTORE)) {
            $icons[] = [
                'icon'         => 'fa-solid fa-clock-rotate-left maroon',
                'class'        => '',
                'tooltip'      => esc_attr__('This Backup is created after the Last Restored Backup', 'duplicator'),
                'tooltipTitle' => '',
                'onclick'      => '',
            ];
        }
        if (!$package->isDeployable()) {
            $icons[] = [
                'icon'         => 'fa-solid fa-ban',
                'class'        => '',
                'tooltip'      => esc_attr__(
                    'Automatic Restore is unavailable. Download the Backup to run its installer manually.',
                    'duplicator'
                ),
                'tooltipTitle' => '',
                'onclick'      => '',
            ];
        }
        if ($package->hasBuildWarnings()) {
            $icons[] = [
                'icon'         => 'fa-solid fa-triangle-exclamation warning-color',
                'class'        => '',
                'tooltip'      => TplMng::getInstance()->render(
                    'admin_pages/packages/row_parts/build_warnings_tooltip',
                    ['warnings' => $package->getBuildWarningsDisplayList()],
                    false
                ),
                'tooltipTitle' => esc_attr__('Backup created with warnings', 'duplicator'),
                'onclick'      => '',
            ];
        }

        return $icons;
    }

    /**
     * Build the always-visible security lock icon entry of the flags cell, reflecting the
     * installer security mode of the package.
     *
     * @param AbstractPackage $package The package
     *
     * @return array{icon: string, class: string, tooltip: string, tooltipTitle: string, onclick: string}
     */
    protected static function getSecurityLockIcon(AbstractPackage $package): array
    {
        return self::getSecurityLockIconByMode((int) $package->Installer->OptsSecureOn);
    }

    /**
     * Build the security lock icon entry for the given installer security mode.
     * Shared by the Backups flags cell and the template/schedule flags cells.
     *
     * @param int $secureMode One of the ArchiveDescriptor::SECURE_MODE_* values
     *
     * @return array{icon: string, class: string, tooltip: string, tooltipTitle: string, onclick: string}
     */
    public static function getSecurityLockIconByMode(int $secureMode): array
    {
        switch ($secureMode) {
            case ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT:
                $icon    = 'fa-solid fa-lock success-color';
                $tooltip = esc_attr__('Archive encryption enabled', 'duplicator');
                break;
            case ArchiveDescriptor::SECURE_MODE_INST_PWD:
                $icon    = 'fa-solid fa-lock warning-color';
                $tooltip = esc_attr__('Installer password protection', 'duplicator');
                break;
            case ArchiveDescriptor::SECURE_MODE_NONE:
            default:
                $icon    = 'fa-solid fa-lock-open warning-color';
                $tooltip = esc_attr__('No backup protection', 'duplicator');
                break;
        }

        return [
            'icon'         => $icon,
            'class'        => '',
            'tooltip'      => $tooltip,
            'tooltipTitle' => esc_attr__('Security', 'duplicator'),
            'onclick'      => '',
        ];
    }

    /**
     * Build the flags cell icons of a backup template: the shape icon derived from the
     * template components and the security lock. Shared by the Templates and Schedules
     * lists so their flags match the icons of the Backups list.
     *
     * @param TemplateEntity $template The template
     *
     * @return array<string, array{icon: string, class: string, tooltip: string, tooltipTitle: string, onclick: string}> Entries keyed by
     *                                                                                                                   'shape' and 'security'
     */
    public static function getTemplateFlagsIcons(TemplateEntity $template): array
    {
        $shapeFlag = BuildComponents::getShapeFlag($template->components);

        return [
            'shape'    => [
                'icon'         => self::getShapeIconByFlag($shapeFlag),
                'class'        => '',
                'tooltip'      => self::getShapeLabelByFlag($shapeFlag),
                'tooltipTitle' => '',
                'onclick'      => '',
            ],
            'security' => self::getSecurityLockIconByMode((int) $template->installer_opts_secure_on),
        ];
    }

    /**
     * Build the legend entries of the template flags icons: the four shapes and the three
     * security lock states. Shared by the Templates and Schedules flags column legends;
     * each list applies its own filter for addon entries.
     *
     * @return array<int, array{icon: string, label: string, section: string}>
     */
    public static function getTemplateFlagsLegendEntries(): array
    {
        $entries = [];
        foreach (
            [
                AbstractPackage::FLAG_FULL_BACKUP,
                AbstractPackage::FLAG_DB_ONLY,
                AbstractPackage::FLAG_MEDIA_ONLY,
                AbstractPackage::FLAG_CUSTOM_COMPONENTS,
            ] as $shapeFlag
        ) {
            $entries[] = [
                'icon'    => '<i class="' . esc_attr(self::getShapeIconByFlag($shapeFlag)) . '"></i>',
                'label'   => self::getShapeLabelByFlag($shapeFlag),
                'section' => self::FLAGS_SECTION_SHAPE,
            ];
        }

        foreach (
            [
                ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT,
                ArchiveDescriptor::SECURE_MODE_INST_PWD,
                ArchiveDescriptor::SECURE_MODE_NONE,
            ] as $secureMode
        ) {
            $lockEntry = self::getSecurityLockIconByMode($secureMode);
            $entries[] = [
                'icon'    => '<i class="' . esc_attr($lockEntry['icon']) . '"></i>',
                'label'   => $lockEntry['tooltip'],
                'section' => self::FLAGS_SECTION_OTHER,
            ];
        }

        return $entries;
    }

    /**
     * Resolve the origin group of a package: the icon and label of the trigger that created it.
     *
     * Core resolves its own execution types; addons resolve the types they own through the
     * `duplicator_package_origin_group` filter. A type nobody resolves (e.g. owned by a disabled
     * addon, or a migration that left only execution metadata behind) gets a generic entry built
     * from that metadata, so every package always has exactly one origin.
     *
     * @param AbstractPackage $package The package
     *
     * @return array{icon: string, label: string, details: string, section: string}
     */
    public static function getOriginGroup(AbstractPackage $package): array
    {
        $origin = null;
        switch ($package->getExecutionType()) {
            case AbstractPackage::EXECUTION_TYPE_MANUAL:
                $origin = self::makeGroup('fa-solid fa-hand', esc_html__('Manual Backup', 'duplicator'));
                break;
            case AbstractPackage::EXECUTION_TYPE_AUTOTUNE:
                $origin = self::makeGroup(
                    'fa-solid fa-wand-magic-sparkles',
                    esc_html__('Created by an AutoTune session', 'duplicator')
                );
                break;
            case AbstractPackage::EXECUTION_TYPE_API:
                $origin = self::makeGroup(
                    'fa-solid fa-plug',
                    esc_html(sprintf(
                        /* translators: %s: name of the plugin that requested the backup */
                        __('Requested by %s', 'duplicator'),
                        $package->getExecutionSource() ?? __('another plugin', 'duplicator')
                    ))
                );
                break;
        }

        /**
         * Filter the origin group of a package. Addons return the group for the execution
         * types they own and pass the incoming value through otherwise.
         *
         * @param ?array{icon: string, label: string, details: string} $origin  Null when core doesn't own the type
         * @param AbstractPackage                                        $package The package
         */
        $origin = apply_filters('duplicator_package_origin_group', $origin, $package);

        if (!is_array($origin)) {
            $origin = self::makeGroup(
                'fa-solid fa-circle-play',
                esc_html(sprintf(
                    /* translators: %s: name of the component that created the backup, or its execution type identifier */
                    __('Triggered by %s', 'duplicator'),
                    $package->getExecutionSource() ?? $package->getExecutionType()
                ))
            );
        }

        // The creation reason belongs to every origin, whoever resolved it
        $details = (string) ($origin['details'] ?? '');
        $reason  = $package->getExecutionReason();
        if ($reason !== null) {
            $details .= '<span class="dup-package-flags-tooltip-note">' . esc_html($reason) . '</span>';
        }

        return [
            'icon'    => (string) ($origin['icon'] ?? ''),
            'label'   => (string) ($origin['label'] ?? ''),
            'details' => $details,
            'section' => self::FLAGS_SECTION_ORIGIN,
        ];
    }

    /**
     * Build the aggregated tooltip groups for the package flags cell.
     *
     * Each group has:
     *   - icon:    FontAwesome class string (e.g. "fa-solid fa-hard-drive"); the template
     *             wraps it in <i class="...">
     *   - label:   translated short description; may contain safe HTML (e.g. a link)
     *   - details: optional HTML rendered as a nested list under the group label
     *   - section: one of the FLAGS_SECTION_* keys; missing or unknown falls into FLAGS_SECTION_OTHER
     *
     * The list is filterable via `duplicator_package_flags_tooltip_groups`.
     *
     * @param AbstractPackage $package The package
     *
     * @return array<int, array{icon: string, label: string, details: string, section?: string}>
     */
    public static function getFlagsTooltipGroups(AbstractPackage $package): array
    {
        $tplMng = TplMng::getInstance();
        $groups = [self::getOriginGroup($package)];

        $shapeDetails = '';
        if (count($package->components) > 1) {
            $componentRows = array_map(
                fn($component): array => [
                    'icon_html' => '',
                    'text'      => BuildComponents::getLabel($component),
                    'url'       => '',
                ],
                $package->components
            );
            $shapeDetails  = $tplMng->render(
                'admin_pages/packages/row_parts/flags_tooltip_sublist',
                ['rows' => $componentRows],
                false
            );
        }
        $groups[] = self::makeGroup(
            self::getShapeIcon($package),
            self::getShapeLabel($package),
            $shapeDetails,
            self::FLAGS_SECTION_SHAPE
        );
        $groups[] = self::makeGroup(
            'fa-solid fa-file-zipper',
            $package->hasFlag(AbstractPackage::FLAG_ZIP_ARCHIVE)
                ? esc_html__('ZIP archive', 'duplicator')
                : esc_html__('DupArchive format', 'duplicator'),
            '',
            self::FLAGS_SECTION_SHAPE
        );

        $storageRows = [];
        foreach ($package->getValidStorages() as $storage) {
            $iconHtml = $storage::getStypeIcon(false);
            if ($storage::isLocal()) {
                $iconHtml = str_replace(
                    'class="dup-storage-icon"',
                    'class="dup-storage-icon dup-storage-icon-invert"',
                    $iconHtml
                );
            }
            $storageRows[] = [
                'icon_html' => $iconHtml,
                'text'      => $storage->getName(),
                'url'       => StoragePageController::getEditUrl($storage),
            ];
        }
        if ($storageRows !== []) {
            $groups[] = self::makeGroup(
                'fa-solid fa-hard-drive',
                esc_html__('Storages', 'duplicator'),
                $tplMng->render(
                    'admin_pages/packages/row_parts/flags_tooltip_sublist',
                    ['rows' => $storageRows],
                    false
                ),
                self::FLAGS_SECTION_STORAGE
            );
        }

        if ($package->hasFlag(AbstractPackage::FLAG_CREATED_AFTER_RESTORE)) {
            $groups[] = self::makeGroup(
                'fa-solid fa-clock-rotate-left maroon',
                esc_html__('Created after the last restored backup', 'duplicator')
            );
        }

        if (!$package->isDeployable()) {
            $groups[] = self::makeGroup(
                'fa-solid fa-ban',
                esc_html__('Automatic Restore unavailable; manual installer required', 'duplicator')
            );
        }

        if ($package->hasBuildWarnings()) {
            $groups[] = self::makeGroup(
                'fa-solid fa-triangle-exclamation warning-color',
                esc_html__('Created with warnings', 'duplicator'),
                $tplMng->render(
                    'admin_pages/packages/row_parts/build_warnings_tooltip',
                    ['warnings' => $package->getBuildWarningsDisplayList()],
                    false
                )
            );
        }

        /**
         * Filter aggregated tooltip groups for the package flags cell.
         *
         * @param array<int, array{icon: string, label: string, details: string, section?: string}> $groups
         * @param AbstractPackage                                                                    $package
         */
        return apply_filters('duplicator_package_flags_tooltip_groups', $groups, $package);
    }

    /**
     * The aggregated tooltip groups split into their display sections.
     *
     * @param AbstractPackage $package The package
     *
     * @return array<int, array<int, array{icon: string, label: string, details: string, section?: string}>>
     */
    public static function getFlagsTooltipSections(AbstractPackage $package): array
    {
        return self::splitFlagsSections(self::getFlagsTooltipGroups($package));
    }

    /**
     * Split flag entries (tooltip groups or legend entries) into their display sections, in
     * FLAGS_SECTIONS_ORDER. Entries without a known section fall into FLAGS_SECTION_OTHER and
     * empty sections are dropped, so the result is a list of non-empty lists.
     *
     * @param array<int, array<string, mixed>> $entries Entries carrying an optional `section` key
     *
     * @return array<int, array<int, array<string, mixed>>>
     */
    public static function splitFlagsSections(array $entries): array
    {
        $sections = array_fill_keys(self::FLAGS_SECTIONS_ORDER, []);
        foreach ($entries as $entry) {
            $section = $entry['section'] ?? self::FLAGS_SECTION_OTHER;
            if (!is_string($section) || !isset($sections[$section])) {
                $section = self::FLAGS_SECTION_OTHER;
            }
            $sections[$section][] = $entry;
        }

        return array_values(array_filter($sections));
    }

    /**
     * Build a tooltip group entry.
     *
     * @param string $icon    FontAwesome class string
     * @param string $label   Escaped label
     * @param string $details Optional HTML details block
     * @param string $section One of the FLAGS_SECTION_* keys
     *
     * @return array{icon: string, label: string, details: string, section: string}
     */
    private static function makeGroup(
        string $icon,
        string $label,
        string $details = '',
        string $section = self::FLAGS_SECTION_OTHER
    ): array {
        return [
            'icon'    => $icon,
            'label'   => $label,
            'details' => $details,
            'section' => $section,
        ];
    }

    /**
     * Return the FontAwesome class string matching the package shape flag.
     *
     * @param AbstractPackage $package The package
     *
     * @return string
     */
    private static function getShapeIcon(AbstractPackage $package): string
    {
        return self::getShapeIconByFlag(self::getPackageShapeFlag($package));
    }

    /**
     * Return the shape flag set on the package, custom for any legacy package missing one.
     *
     * @param AbstractPackage $package The package
     *
     * @return string One of the AbstractPackage shape flags
     */
    private static function getPackageShapeFlag(AbstractPackage $package): string
    {
        foreach (
            [
                AbstractPackage::FLAG_FULL_BACKUP,
                AbstractPackage::FLAG_DB_ONLY,
                AbstractPackage::FLAG_MEDIA_ONLY,
            ] as $shapeFlag
        ) {
            if ($package->hasFlag($shapeFlag)) {
                return $shapeFlag;
            }
        }
        return AbstractPackage::FLAG_CUSTOM_COMPONENTS;
    }

    /**
     * Return the icon classes matching a shape flag.
     *
     * @param string $shapeFlag One of the AbstractPackage shape flags
     *
     * @return string
     */
    public static function getShapeIconByFlag(string $shapeFlag): string
    {
        switch ($shapeFlag) {
            case AbstractPackage::FLAG_FULL_BACKUP:
                return 'fa-solid fa-square-check';
            case AbstractPackage::FLAG_DB_ONLY:
                return 'fa-solid fa-database';
            case AbstractPackage::FLAG_MEDIA_ONLY:
                return 'fa-solid fa-images';
            default:
                return 'fa-solid fa-puzzle-piece';
        }
    }

    /**
     * Return the translated label matching the package shape flag.
     *
     * @param AbstractPackage $package The package
     *
     * @return string
     */
    private static function getShapeLabel(AbstractPackage $package): string
    {
        return self::getShapeLabelByFlag(self::getPackageShapeFlag($package));
    }

    /**
     * Return the translated label matching a shape flag.
     *
     * @param string $shapeFlag One of the AbstractPackage shape flags
     *
     * @return string
     */
    public static function getShapeLabelByFlag(string $shapeFlag): string
    {
        switch ($shapeFlag) {
            case AbstractPackage::FLAG_FULL_BACKUP:
                return esc_html__('Full Backup', 'duplicator');
            case AbstractPackage::FLAG_DB_ONLY:
                return esc_html__('Database Only Backup', 'duplicator');
            case AbstractPackage::FLAG_MEDIA_ONLY:
                return esc_html__('Media Only Backup', 'duplicator');
            default:
                return esc_html__('Custom Components Backup', 'duplicator');
        }
    }
}
