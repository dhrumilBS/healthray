<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteBase\Utils\ExtraPlugins;

use Duplicator\Libs\Snap\SnapString;
use Exception;

class ExtraItem
{
    const STATUS_NOT_INSTALLED = 0;
    const STATUS_INSTALLED     = 1;
    const STATUS_ACTIVE        = 2;

    const URL_TYPE_GENERIC = 0;
    const URL_TYPE_ZIP     = 1;

    /** @var string */
    protected string $name = '';

    /** @var string */
    protected string $slug = '';

    /** @var string */
    protected string $icon = '';

    /** @var string */
    protected string $desc = '';

    /** @var string */
    protected string $url = '';

    /** @var string|false */
    protected $wpOrgURL = false;

    /** @var ?ExtraItem */
    protected ?ExtraItem $pro = null;

    /** @var ?string[] */
    private static ?array $installedSlugs = null;

    /**
     * @param string       $name     Plugin name
     * @param string       $slug     Plugin basename (folder/file.php)
     * @param string       $icon     URL to plugin icon
     * @param string       $desc     Plugin description
     * @param string       $url      Install URL (ZIP) or generic landing page
     * @param string|false $wpOrgURL Optional wp.org page URL
     */
    public function __construct(string $name, string $slug, string $icon, string $desc, string $url, $wpOrgURL = false)
    {
        $this->name     = $name;
        $this->slug     = $slug;
        $this->icon     = $icon;
        $this->desc     = $desc;
        $this->url      = $url;
        $this->wpOrgURL = $wpOrgURL;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getDesc(): string
    {
        return $this->desc;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string|false
     */
    public function getWpOrgURL()
    {
        return $this->wpOrgURL;
    }

    /**
     * @return ?ExtraItem
     */
    public function getPro(): ?ExtraItem
    {
        return $this->pro;
    }

    /**
     * Set Pro variant of this plugin.
     *
     * @param string       $name     Pro plugin name
     * @param string       $slug     Pro plugin basename
     * @param string       $icon     URL to plugin icon
     * @param string       $desc     Pro plugin description
     * @param string       $url      Pro plugin landing page URL
     * @param string|false $wpOrgURL Optional wp.org page URL
     *
     * @return void
     */
    public function setPro(string $name, string $slug, string $icon, string $desc, string $url, $wpOrgURL = false): void
    {
        $this->pro = new self($name, $slug, $icon, $desc, $url, $wpOrgURL);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isInstalled() && is_plugin_active($this->slug);
    }

    /**
     * @return bool
     */
    public function isInstalled(): bool
    {
        if (self::$installedSlugs === null) {
            if (!function_exists('get_plugins')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            self::$installedSlugs = array_keys(get_plugins());
        }
        return in_array($this->slug, self::$installedSlugs, true);
    }

    /**
     * If the Lite version is already active and a Pro variant exists, show Pro instead.
     *
     * @return bool
     */
    public function skipLite(): bool
    {
        return $this->pro !== null && $this->isActive();
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        if ($this->isActive()) {
            return self::STATUS_ACTIVE;
        }
        if ($this->isInstalled()) {
            return self::STATUS_INSTALLED;
        }
        return self::STATUS_NOT_INSTALLED;
    }

    /**
     * @return string
     */
    public function getStatusText(): string
    {
        switch ($this->getStatus()) {
            case self::STATUS_ACTIVE:
                return __('Active', 'duplicator');
            case self::STATUS_INSTALLED:
                return __('Inactive', 'duplicator');
            case self::STATUS_NOT_INSTALLED:
            default:
                return __('Not Installed', 'duplicator');
        }
    }

    /**
     * @return int
     */
    public function getURLType(): int
    {
        return SnapString::endsWith($this->url, '.zip') ? self::URL_TYPE_ZIP : self::URL_TYPE_GENERIC;
    }

    /**
     * Install the plugin from its ZIP URL.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function install(): bool
    {
        if ($this->isInstalled()) {
            return true;
        }

        if (!SnapString::endsWith($this->url, '.zip')) {
            throw new Exception('Invalid plugin url for installation');
        }

        if (!current_user_can('install_plugins')) {
            throw new Exception('User does not have permission to install plugins');
        }

        if (!class_exists('Plugin_Upgrader')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        }
        wp_cache_flush();

        $upgrader = new \Plugin_Upgrader(new \Automatic_Upgrader_Skin());
        if (!$upgrader->install($this->url)) {
            throw new Exception('Failed to install plugin');
        }

        self::$installedSlugs = null;

        return true;
    }

    /**
     * Activate the plugin.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function activate(): bool
    {
        if ($this->isActive()) {
            return true;
        }

        if (!current_user_can('activate_plugins')) {
            throw new Exception('User does not have permission to activate plugins');
        }

        if (activate_plugin($this->slug) !== null) {
            throw new Exception('Failed to activate plugin');
        }

        return true;
    }
}
