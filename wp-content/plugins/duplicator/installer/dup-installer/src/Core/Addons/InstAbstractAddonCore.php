<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 */

namespace Duplicator\Installer\Core\Addons;

/**
 * @phpstan-import-type AddonInfo from InstAddonsManager
 */
abstract class InstAbstractAddonCore
{
    /**
     * Addons instances
     *
     * @var self[]
     */
    private static $instances = [];

    /**
     * Get curent addon instance
     *
     * @return self
     */
    public static function getInstance()
    {
        $class = static::class;
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    /**
     * Constructor
     */
    final protected function __construct()
    {
    }

    /**
     * Return this addon's manifest info.
     *
     * Info lives in `InstAddonsManager` — this is a typed accessor that reads
     * straight from the cached entry, so there is no local copy to stay in
     * sync with.
     *
     * @param string|null $key Single manifest field to return, or null for the full record
     *
     * @return ($key is null ? AddonInfo : mixed)
     *
     * @throws \RuntimeException If the addon manifest is missing or invalid
     *                           (should never happen: a discovered & instantiated addon must have a valid manifest)
     */
    public function getInfo(?string $key = null)
    {
        $info = InstAddonsManager::getAddonInfo(static::getAddonPath());
        if ($info === false) {
            throw new \RuntimeException('Addon ' . static::class . ' has no valid manifest');
        }
        if ($key === null) {
            return $info;
        }
        return $info[$key] ?? null;
    }

    /**
     * Function called on addon init only if is available
     *
     * @return void
     */
    abstract public function init();

    /**
     * Get main addon file path
     *
     * @return string
     */
    public static function getAddonFile()
    {
        // To prevent the warning about static abstract functions that appears in PHP 5.4/5.6 I use this trick.
        throw new \Exception('this function have to overwritte on child class');
    }

    /**
     * Get main addon folder
     *
     * @return string
     */
    public static function getAddonPath()
    {
        // To prevent the warning about static abstract functions that appears in PHP 5.4/5.6 I use this trick.
        throw new \Exception('this function have to overwritte on child class');
    }

    /**
     * Get slug of current addon
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->getInfo('slug');
    }

    /**
     * True if current addon is available
     *
     * @return boolean
     */
    public function canEnable()
    {
        if (version_compare(PHP_VERSION, $this->getInfo('requiresPHP'), '<')) {
            return false;
        }

        if (version_compare(DUPX_VERSION, $this->getInfo('requiresDuplcator'), '<')) {
            return false;
        }

        return true;
    }

    /**
     * True if addon has dependencies
     *
     * @return boolean
     */
    public function hasDependencies()
    {
        $avaliableAddons = InstAddonsManager::getInstance()->getAvailableAddons();
        return !array_diff($this->getInfo('requiresAddons'), $avaliableAddons);
    }
}
