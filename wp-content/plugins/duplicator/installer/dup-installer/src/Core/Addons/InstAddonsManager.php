<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 */

namespace Duplicator\Installer\Core\Addons;

use Duplicator\Installer\Core\Hooks\HooksMng;
use Duplicator\Installer\Utils\Log\Log;

/**
 * @phpstan-type AddonInfo array{
 *     slug: string,
 *     name: string,
 *     version: string,
 *     description: string,
 *     author: string,
 *     authorURI: string,
 *     addonURI: string,
 *     requiresWP: string,
 *     requiresPHP: string,
 *     requiresDuplcator: string,
 *     requiresAddons: string[]
 * }
 */
final class InstAddonsManager
{
    const MANIFEST_FILE = 'addon.json';

    /** @var ?self */
    private static $instance;
    /** @var InstAbstractAddonCore[] */
    private array $addons;
    /** @var string[] */
    private $enabledAddons = [];
    /**
     * Cache of decoded manifests, keyed by the addon folder basename.
     *
     * @var array<string, AddonInfo|false>
     */
    private static array $infoCache = [];

    /**
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
     * Inizialize addons
     */
    private function __construct()
    {
        $this->addons = self::getAddonListFromFolder();
    }

    /**
     * inizialize all abaiblae addons
     *
     * @return void
     */
    public function initializeAddons(): void
    {
        foreach ($this->addons as $addon) {
            if ($addon->canEnable() && $addon->hasDependencies()) {
                $this->enabledAddons[] = $addon->getSlug();
                $addon->init();
                Log::info('ADDON ' . $addon->getAddonFile() . ' ENABLED', Log::LV_DETAILED);
            } else {
                Log::info('CAN\'T ENABLE ADDON ' . $addon->getSlug());
            }
        }
        HooksMng::getInstance()->doAction('duplicator_addons_loaded');
    }

    /**
     *
     * @return string[]
     */
    public function getAvailableAddons(): array
    {
        $result = [];
        foreach ($this->addons as $addon) {
            $result[] = $addon->getSlug();
        }

        return $result;
    }

    /**
     *
     * @return string[] List of enabled addon slugs
     */
    public function getEnabledAddons()
    {
        return $this->enabledAddons;
    }

    /**
     * return addons folder
     *
     * @return string
     */
    public static function getAddonsPath(): string
    {
        return DUPX_INIT . '/addons';
    }

    /**
     * Scan the addons path for `{dir}/addon.json` manifests and instantiate
     * each addon main class.
     *
     * @return InstAbstractAddonCore[]
     */
    private static function getAddonListFromFolder(): array
    {
        $checkDir = self::getAddonsPath();
        if (!is_dir($checkDir)) {
            return [];
        }

        $addonList = [];
        foreach (glob(rtrim($checkDir, '/') . '/*/' . self::MANIFEST_FILE) as $manifestFile) {
            $folder = dirname($manifestFile);
            $info   = self::getAddonInfo($folder);
            if ($info === false) {
                Log::info('Addon manifest ' . $manifestFile . ' is invalid or missing "slug"');
                continue;
            }
            $slug          = $info['slug'];
            $addonMainFile = $folder . '/' . $slug . '.php';
            if (!is_file($addonMainFile)) {
                Log::info('Addon manifest ' . $manifestFile . ' references missing main file ' . $slug . '.php');
                continue;
            }

            $addonMainClass = '\\Duplicator\\Installer\\Addons\\' . $slug . '\\' . $slug;
            try {
                if (!is_subclass_of($addonMainClass, InstAbstractAddonCore::class)) {
                    continue;
                }
            } catch (\Exception $e) {
                Log::info('Addon file ' . $addonMainFile . ' exists but not countain addon main core class, Exception: ' . $e->getMessage());
                continue;
            } catch (\Error $e) {
                Log::info('Addon file ' . $addonMainFile . ' exists but generate an error, Exception: ' . $e->getMessage());
                continue;
            }

            $addonObj                        = $addonMainClass::getInstance();
            $addonList[$addonObj->getSlug()] = $addonObj;
        }

        return $addonList;
    }

    /**
     * Read and cache an addon manifest, keyed by the addon folder basename
     * (unique across the installer's single addons path).
     *
     * Returns `false` when the manifest is missing, unreadable, not valid
     * JSON, or does not declare a non-empty `slug`. Invalid results are
     * cached too, so a broken addon folder is inspected only once per request.
     *
     * @param string $folder Absolute path to the addon folder
     *
     * @return AddonInfo|false
     */
    public static function getAddonInfo(string $folder)
    {
        $cacheKey = basename($folder);
        if (!array_key_exists($cacheKey, self::$infoCache)) {
            try {
                self::$infoCache[$cacheKey] = self::loadAddonInfo($folder);
            } catch (\Throwable $e) {
                Log::info('Invalid addon manifest in ' . $folder . ': ' . $e->getMessage());
                self::$infoCache[$cacheKey] = false;
            }
        }
        return self::$infoCache[$cacheKey];
    }

    /**
     * Read and normalize a single manifest. Throws on any invalid state —
     * callers store the outcome in the cache.
     *
     * @param string $folder Absolute path to the addon folder
     *
     * @return AddonInfo
     *
     * @throws \RuntimeException If the manifest is missing, unreadable, not valid JSON,
     *                           or does not declare a non-empty `slug`.
     */
    private static function loadAddonInfo(string $folder): array
    {
        $manifestFile = $folder . '/' . self::MANIFEST_FILE;
        if (!is_file($manifestFile)) {
            throw new \RuntimeException(self::MANIFEST_FILE . ' not found');
        }
        $raw = json_decode((string) file_get_contents($manifestFile), true);
        if (!is_array($raw)) {
            throw new \RuntimeException(self::MANIFEST_FILE . ' is not valid JSON');
        }
        if (!isset($raw['slug']) || !is_string($raw['slug']) || $raw['slug'] === '') {
            throw new \RuntimeException(self::MANIFEST_FILE . ' is missing or has empty "slug"');
        }

        $data = array_merge(self::getDefaultManifestValues(), $raw);
        if ($data['name'] === '') {
            $data['name'] = $data['slug'];
        }

        return $data;
    }

    /**
     * Default manifest field values.
     *
     * @return AddonInfo
     */
    private static function getDefaultManifestValues(): array
    {
        static $defaults = null;
        if (is_null($defaults)) {
            $defaults = [
                'slug'              => '',
                'name'              => '',
                'addonURI'          => '',
                'version'           => '0',
                'description'       => '',
                'author'            => '',
                'authorURI'         => '',
                'requiresWP'        => '5.3',
                'requiresPHP'       => '7.4',
                'requiresDuplcator' => '4.5.20',
                'requiresAddons'    => [],
            ];
        }
        return $defaults;
    }
}
