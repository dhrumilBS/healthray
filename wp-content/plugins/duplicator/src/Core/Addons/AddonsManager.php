<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 */

namespace Duplicator\Core\Addons;

use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\DupLog;
use Exception;
use RuntimeException;
use Throwable;

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
 *     requiresAddons: string[],
 *     mainFile: string,
 *     required: bool,
 *     forceDisabled: bool,
 *     enabled: bool
 * }
 */
final class AddonsManager
{
    const MANIFEST_FILE = 'addon.json';

    /** @var ?self */
    private static $instance;
    /** @var AbstractAddonCore[] */
    private array $addons;
    /** @var AbstractAddonCore[] */
    private $enabledAddons = [];
    /**
     * Cache of decoded manifests, keyed by the addon folder basename.
     * Folder basenames are unique across scan paths.
     * A `false` entry marks a folder whose manifest is missing, unreadable,
     * or not valid JSON — such folders are ignored as addons.
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
     * Class constructor
     */
    private function __construct()
    {
        $this->addons = self::instantiateAddons(self::discoverAll(), self::getDisabledSlugs());
    }

    /**
     * Check
     *
     * @return object
     */
    public static function check(): object
    {
        static $check = null;

        if (is_null($check)) {
            $checkString = pack("H*", \Duplicator\Core\Bootstrap::getAddsHash());
            $check       = json_decode($checkString);

            if (!is_object($check)) {
                throw new Exception('Invalid hash');
            }
        }
        return $check;
    }

    /**
     *
     * @return void
     */
    public function initializeAddons(): void
    {
        $check = self::check();
        if (
            !is_array($check->r) ||
            !is_array($check->fd) ||
            !isset($check->v) ||
            $check->v !== DUPLICATOR_VERSION
        ) {
            throw new \Exception('Initialization error');
        }

        $disabled = self::getDisabledSlugs();

        foreach ($this->addons as $addon) {
            if (!in_array($addon->getSlug(), $disabled) && $addon->canEnable() && $addon->hasDependencies()) {
                $this->enabledAddons[$addon->getSlug()] = $addon;
                $addon->init();
            }
        }

        do_action('duplicator_addons_loaded');
    }

    /**
     * Reads the persistent status map if present; otherwise get the default.
     *
     * @return string[]
     */
    public static function getDisabledSlugs(): array
    {
        $check  = self::check();
        $status = self::getStatus();

        $disabled = [];
        if ($status === null) {
            /** @var string[] $disabled */
            $disabled = $check->fd;
        } else {
            foreach ($status as $slug => $enabled) {
                if (!$enabled) {
                    $disabled[] = $slug;
                }
            }
        }

        return array_values(array_diff($disabled, $check->r));
    }

    /**
     * @return array<string,bool>|null Null if missing or stale (fingerprint mismatch).
     */
    public static function getStatus(): ?array
    {
        $status = StaticGlobal::getAddonsStatus();
        if ($status === null) {
            return null;
        }
        if (StaticGlobal::getAddonsStatusFingerprint() !== self::currentFingerprint()) {
            return null;
        }
        return $status;
    }

    /**
     * @param array<string,bool> $status Map slug => enabled
     *
     * @return bool
     */
    public static function saveStatus(array $status): bool
    {
        return StaticGlobal::setAddonsStatus($status, self::currentFingerprint());
    }

    /**
     * Identifies the addon configuration the status was written for.
     * If this changes, any persisted status is considered stale and ignored.
     *
     * @return string
     */
    private static function currentFingerprint(): string
    {
        return DUPLICATOR_VERSION . '|' . \Duplicator\Core\Bootstrap::getAddsHash();
    }

    /**
     * Rebuild the status map from the filesystem and hash defaults, and persist it.
     * Hooked to `duplicator_upgrade`.
     *
     * @return void
     */
    public static function resetStatus(): void
    {
        self::saveStatus(self::buildDefaultStatus());
    }

    /**
     * Default status: required = true, hash-force-disabled = false, others = true.
     *
     * @return array<string,bool>
     */
    public static function buildDefaultStatus(): array
    {
        $check    = self::check();
        $required = array_flip($check->r);
        $fd       = array_flip($check->fd);

        $status = [];
        foreach (array_keys(self::discoverAll()) as $slug) {
            if (isset($required[$slug])) {
                $status[$slug] = true;
            } elseif (isset($fd[$slug])) {
                $status[$slug] = false;
            } else {
                $status[$slug] = true;
            }
        }
        return $status;
    }

    /**
     * Scan all addon paths and return the discovered addon folders.
     *
     * @return array<string,string> slug => addon folder absolute path
     */
    private static function discoverAll(): array
    {
        $result = [];
        foreach (self::getAddonsPath() as $path) {
            $result = array_merge($result, self::discoverFolder($path));
        }
        return $result;
    }

    /**
     * Return metadata for every discovered addon (no instantiation).
     *
     * @return array<string,AddonInfo> slug => info
     */
    public static function getAddonsListInfo(): array
    {
        $result = [];
        foreach (self::discoverAll() as $slug => $folder) {
            $info = self::getAddonInfo($folder);
            if ($info === false) {
                // discoverAll() filters invalid manifests, so this is unreachable.
                continue;
            }
            $result[$slug] = $info;
        }
        return $result;
    }

    /**
     * Read an addon manifest and compute the full info record (including
     * `mainFile`, `required`, `forceDisabled`, `enabled`). The result is
     * cached by the addon folder basename — folder basenames are unique
     * across scan paths, so the basename is a safe cache key even when two
     * scan paths shadow each other.
     *
     * Returns `false` when the manifest is missing, unreadable, not valid
     * JSON, or does not declare a non-empty `slug`. Invalid results are
     * cached too, so a broken addon folder is inspected only once per request.
     *
     * @param string $folder Absolute path to the addon folder (containing addon.json)
     *
     * @return AddonInfo|false
     */
    public static function getAddonInfo(string $folder)
    {
        $cacheKey = basename($folder);
        if (!array_key_exists($cacheKey, self::$infoCache)) {
            try {
                self::$infoCache[$cacheKey] = self::loadAddonInfo($folder);
            } catch (Throwable $e) {
                DupLog::trace('Invalid addon manifest in ' . $folder . ': ' . $e->getMessage());
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
            throw new RuntimeException(self::MANIFEST_FILE . ' not found');
        }
        $raw = json_decode((string) file_get_contents($manifestFile), true);
        if (!is_array($raw)) {
            throw new RuntimeException(self::MANIFEST_FILE . ' is not valid JSON');
        }
        if (!isset($raw['slug']) || !is_string($raw['slug']) || $raw['slug'] === '') {
            throw new RuntimeException(self::MANIFEST_FILE . ' is missing or has empty "slug"');
        }

        $data = array_merge(self::getDefaultManifestValues(), $raw);
        if ($data['name'] === '') {
            $data['name'] = $data['slug'];
        }

        $check                 = self::check();
        $slug                  = $data['slug'];
        $data['mainFile']      = $folder . '/' . $slug . '.php';
        $data['required']      = in_array($slug, $check->r, true);
        $data['forceDisabled'] = in_array($slug, $check->fd, true);
        $data['enabled']       = $data['required'] || !in_array($slug, self::getDisabledSlugs(), true);

        return $data;
    }

    /**
     * Default manifest field values. Used both as the fallback when a field is
     * missing from addon.json and as the schema of known fields.
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

    /**
     *
     * @return boolean
     */
    public function isAddonsReady(): bool
    {
        return (count(array_diff(self::check()->r, array_keys($this->enabledAddons))) === 0);
    }

    /**
     * Get list of availables addons
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
     * @return AbstractAddonCore[]
     */
    public function getEnabledAddons()
    {
        return $this->enabledAddons;
    }

    /**
     * Returns true if the addon is enabled
     *
     * @param string $slug addon slug
     *
     * @return boolean
     */
    public function isAddonEnabled($slug): bool
    {
        return isset($this->enabledAddons[$slug]);
    }

    /**
     * return addons folder list
     *
     * @return string[]
     */
    public static function getAddonsPath(): array
    {
        // Sort order is importat
        return [
            DUPLICATOR_SSDIR_PATH_ADDONS,
            DUPLICATOR____PATH . '/addons',
        ];
    }

    /**
     * Scan a single addon path, looking for `{dir}/addon.json` manifests.
     *
     * A folder is considered a valid addon when its manifest exists, parses as
     * JSON, declares a non-empty `slug`, and the corresponding `{slug}.php`
     * main file is present in the same directory.
     *
     * @param string $path Directory to scan
     *
     * @return array<string,string> slug => addon folder absolute path
     */
    private static function discoverFolder(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $result = [];
        foreach (glob(trailingslashit($path) . '*/' . self::MANIFEST_FILE) as $manifestFile) {
            $folder = dirname($manifestFile);
            $info   = self::getAddonInfo($folder);
            if ($info === false) {
                DupLog::trace('Addon manifest ' . $manifestFile . ' is invalid or missing "slug"');
                continue;
            }
            $slug = $info['slug'];
            if (!is_file($folder . '/' . $slug . '.php')) {
                DupLog::trace('Addon manifest ' . $manifestFile . ' declares slug "' . $slug . '" but ' . $slug . '.php is missing');
                continue;
            }
            $result[$slug] = $folder;
        }
        return $result;
    }

    /**
     * Instantiate addon main classes from a discovered list, skipping disabled slugs.
     *
     * @param array<string,string> $discovered slug => addon folder absolute path
     * @param string[]             $disabled   Slugs to skip
     *
     * @return AbstractAddonCore[]
     */
    private static function instantiateAddons(array $discovered, array $disabled): array
    {
        $addons = [];
        foreach ($discovered as $slug => $folder) {
            if (in_array($slug, $disabled)) {
                continue;
            }
            $class = '\\Duplicator\\Addons\\' . $slug . '\\' . $slug;
            try {
                if (!is_subclass_of($class, AbstractAddonCore::class)) {
                    DupLog::trace(
                        'Addon folder ' . $folder . ' references class ' . $class .
                            ' which does not extend AbstractAddonCore'
                    );
                    continue;
                }
            } catch (\Exception $e) {
                DupLog::trace(
                    'Addon ' . $slug . ' main class load failed, Exception: ' . $e->getMessage()
                );
                continue;
            } catch (\Error $e) {
                DupLog::trace(
                    'Addon ' . $slug . ' main class load error, Exception: ' . $e->getMessage()
                );
                continue;
            }
            $addon                     = $class::getInstance();
            $addons[$addon->getSlug()] = $addon;
        }
        return $addons;
    }
}
