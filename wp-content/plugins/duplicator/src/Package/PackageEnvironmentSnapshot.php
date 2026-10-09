<?php

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Installer\Package\DescriptorPlugin;
use Duplicator\Installer\Package\DescriptorTheme;
use Duplicator\Installer\Package\DescriptorWpInfo;

/**
 * Immutable snapshot of the active WordPress environment when a Backup is created.
 */
final class PackageEnvironmentSnapshot
{
    /** @var array<int, array{name: string, version: string}> */
    private array $activeThemes = [];
    /** @var array<int, array{name: string, version: string}> */
    private array $activePlugins = [];

    /**
     * @param array<int, array{name: string, version: string}> $activeThemes  Active themes
     * @param array<int, array{name: string, version: string}> $activePlugins Active plugins
     */
    public function __construct(array $activeThemes, array $activePlugins)
    {
        $this->activeThemes  = $activeThemes;
        $this->activePlugins = $activePlugins;
    }

    /**
     * Build the snapshot from the WordPress descriptor written into the Backup.
     *
     * @param DescriptorWpInfo $wpInfo                 WordPress descriptor
     * @param ?string          $primaryThemeStylesheet Active stylesheet on a single site
     *
     * @return self
     */
    public static function fromWpInfo(DescriptorWpInfo $wpInfo, ?string $primaryThemeStylesheet = null): self
    {
        $themes = [];
        foreach ($wpInfo->themes as $theme) {
            if (!$theme instanceof DescriptorTheme || !self::isThemeActive($theme, $wpInfo, $primaryThemeStylesheet)) {
                continue;
            }

            $themes[] = [
                'name'    => strlen($theme->themeName) > 0 ? $theme->themeName : $theme->slug,
                'version' => $theme->version,
            ];
        }

        $plugins = [];
        foreach ($wpInfo->plugins as $plugin) {
            if (!$plugin instanceof DescriptorPlugin || !self::isPluginActive($plugin)) {
                continue;
            }

            $plugins[] = [
                'name'    => strlen($plugin->name) > 0 ? $plugin->name : $plugin->slug,
                'version' => $plugin->version,
            ];
        }

        self::sortByName($themes);
        self::sortByName($plugins);

        return new self($themes, $plugins);
    }

    /**
     * Active themes at Backup creation time.
     *
     * @return array<int, array{name: string, version: string}>
     */
    public function getActiveThemes(): array
    {
        return $this->activeThemes;
    }

    /**
     * Active plugins at Backup creation time.
     *
     * @return array<int, array{name: string, version: string}>
     */
    public function getActivePlugins(): array
    {
        return $this->activePlugins;
    }

    /**
     * Number of active plugins at Backup creation time.
     *
     * @return int
     */
    public function getActivePluginCount(): int
    {
        return count($this->activePlugins);
    }

    /**
     * @param DescriptorTheme  $theme                  Theme descriptor
     * @param DescriptorWpInfo $wpInfo                 WordPress descriptor
     * @param ?string          $primaryThemeStylesheet Active stylesheet on a single site
     *
     * @return bool
     */
    private static function isThemeActive(
        DescriptorTheme $theme,
        DescriptorWpInfo $wpInfo,
        ?string $primaryThemeStylesheet
    ): bool {
        if (!$wpInfo->is_multisite && $primaryThemeStylesheet !== null) {
            return $theme->stylesheet === $primaryThemeStylesheet;
        }

        return $theme->isActive === true || (is_array($theme->isActive) && count($theme->isActive) > 0);
    }

    /**
     * @param DescriptorPlugin $plugin Plugin descriptor
     *
     * @return bool
     */
    private static function isPluginActive(DescriptorPlugin $plugin): bool
    {
        return $plugin->active === true ||
            $plugin->networkActive ||
            $plugin->mustUse ||
            $plugin->dropIns ||
            (is_array($plugin->active) && count($plugin->active) > 0);
    }

    /**
     * @param array<int, array{name: string, version: string}> $items Items to sort
     *
     * @return void
     */
    private static function sortByName(array &$items): void
    {
        usort(
            $items,
            static function (array $first, array $second): int {
                return strcasecmp($first['name'], $second['name']);
            }
        );
    }
}
