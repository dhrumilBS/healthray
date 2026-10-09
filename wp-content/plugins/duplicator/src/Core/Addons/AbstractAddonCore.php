<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 */

namespace Duplicator\Core\Addons;

/**
 * @phpstan-import-type AddonInfo from AddonsManager
 */
abstract class AbstractAddonCore
{
    /** @var static[] */
    private static $instances = [];

    /**
     *
     * @return static
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
     * Class constructor
     */
    final protected function __construct()
    {
        $this->onConstruct();
    }

    /**
     * Return this addon's manifest info.
     *
     * Info lives in `AddonsManager` — this is a typed accessor that reads
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
        $info = AddonsManager::getAddonInfo(static::getAddonPath());
        if ($info === false) {
            throw new \RuntimeException('Addon ' . static::class . ' has no valid manifest');
        }
        if ($key === null) {
            return $info;
        }
        return $info[$key] ?? null;
    }

    /**
     * On Costruct function called on child class constructor
     * Use to exect function before init hookw, at this point wordpress is not fully loaded
     *
     * @return void
     */
    protected function onConstruct(): void
    {
    }

    /**
     * Init called on worpdres hook init if addon is enabled
     *
     * @return void
     */
    abstract public function init();

    /**
     * Return addon file
     *
     * @return string
     */
    abstract public static function getAddonFile();

    /**
     * Return addon folder
     *
     * @return string
     */
    abstract public static function getAddonPath();

    /**
     * Return addon url
     *
     * @return string
     */
    public static function getAddonUrl()
    {
        $path = 'addons/' . basename(static::getAddonPath());
        return plugins_url($path, DUPLICATOR____FILE);
    }

    /**
     *
     * @return string
     */
    public function getAddonInstallerPath()
    {
        return static::getAddonPath() . '/installer/' . strtolower($this->getSlug());
    }

    /**
     * Return addon slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->getInfo('slug');
    }

    /**
     * Check if current addon is available
     *
     * @return boolean
     */
    public function canEnable()
    {
        if (version_compare(PHP_VERSION, $this->getInfo('requiresPHP'), '<')) {
            return false;
        }

        global $wp_version;
        if (version_compare($wp_version, $this->getInfo('requiresWP'), '<')) {
            return false;
        }

        if (version_compare(DUPLICATOR_VERSION, $this->getInfo('requiresDuplcator'), '<')) {
            return false;
        }

        return true;
    }

    /**
     * Check if addon has dependencies
     *
     * @return boolean
     */
    public function hasDependencies()
    {
        $avaliableAddons = AddonsManager::getInstance()->getAvailableAddons();
        return !array_diff($this->getInfo('requiresAddons'), $avaliableAddons);
    }
}
