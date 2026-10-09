<?php

namespace Duplicator\Libs\WpUtils;

use Duplicator\Models\GlobalEntity;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapWP;

class WpArchiveUtils
{
    /**
     * get the main target root path to make archive
     *
     * @return string
     */
    public static function getTargetRootPath(): string
    {
        static $targetRootPath = null;
        if (is_null($targetRootPath)) {
            $paths = self::getArchiveListPaths();
            unset($paths['wpconfig']);
            $targetRootPath = SnapIO::trailingslashit(SnapIO::getCommonPath($paths));
        }
        return $targetRootPath;
    }

    /**
     * Get original wordpress URLs
     *
     * @param null|string $urlKey if set will only return the url identified by that key
     *
     * @return array<string,string>|string return empty string if key doesn't exist
     */
    public static function getOriginalUrls(?string $urlKey = null)
    {
        static $origUrls = null;
        if (is_null($origUrls)) {
            $restoreMultisite = false;
            if (is_multisite() && get_main_site_id() !== get_current_blog_id()) {
                $restoreMultisite = true;
                restore_current_blog();
                switch_to_blog(get_main_site_id());
            }

            $updDirs = wp_upload_dir(null, false, true);
            if (($wpConfigDir = SnapWP::getWPConfigPath()) !== false) {
                $wpConfigDir = dirname($wpConfigDir);
            }

            if (GlobalEntity::getInstance()->isHomePathAsAbsolute()) {
                $homeUrl = site_url();
            } else {
                $homeUrl   = home_url();
                $homeParse = SnapURL::parseUrl(home_url());
                $absParse  = SnapURL::parseUrl(site_url());
                if ($homeParse['host'] === $absParse['host'] && SnapIO::isChildPath($homeParse['path'], $absParse['path'], false, false)) {
                    $homeParse['path'] = $absParse['path'];
                    $homeUrl           = SnapURL::buildUrl($homeParse);
                }
            }

            $origUrls = [
                'home'      => $homeUrl,
                'abs'       => site_url(),
                'login'     => wp_login_url(),
                'wpcontent' => content_url(),
                'uploads'   => $updDirs['baseurl'],
                'plugins'   => plugins_url(),
                'muplugins' => WPMU_PLUGIN_URL, // @phpstan-ignore phpstanWP.wpConstant.fetch
                'themes'    => get_theme_root_uri(),
            ];
            if ($restoreMultisite) {
                restore_current_blog();
            }
        }

        if ($urlKey === null) {
            return $origUrls;
        }

        if (isset($origUrls[$urlKey])) {
            return $origUrls[$urlKey];
        } else {
            return '';
        }
    }

    /**
     * return the wordpress original dir paths
     *
     * @param string|null $pathKey path key
     *
     * @return array<string,string>|string return empty string if key doesn't exist
     */
    public static function getOriginalPaths(?string $pathKey = null)
    {
        return SnapWP::getWpPaths($pathKey, GlobalEntity::getInstance()->isHomePathAsAbsolute());
    }

    /**
     * Return the wordpress original dir paths.
     *
     * @param string|null $pathKey path key
     *
     * @return array<string,string>|string return empty string if key doesn't exist
     */
    public static function getArchiveListPaths(?string $pathKey = null)
    {
        return SnapWP::getNormalizedWpPaths($pathKey, GlobalEntity::getInstance()->isHomePathAsAbsolute());
    }

    /**
     *
     * @param string $path path to check
     *
     * @return bool return true if path is a path of current wordpress installation
     */
    public static function isCurrentWordpressInstallPath(string $path): bool
    {
        static $currentWpPaths = null;

        if (is_null($currentWpPaths)) {
            $currentWpPaths = array_merge(self::getOriginalPaths(), self::getArchiveListPaths());
            $currentWpPaths = array_map('trailingslashit', $currentWpPaths);
            $currentWpPaths = array_values(array_unique($currentWpPaths));
        }
        return in_array(trailingslashit($path), $currentWpPaths);
    }

    /**
     * Check if the homepath and abspath are equivalent
     *
     * @return bool
     */
    public static function isAbspathHomepathEquivalent(): bool
    {
        static $isEquivalent = null;
        if (is_null($isEquivalent)) {
            $absPath      = SnapIO::safePathUntrailingslashit(ABSPATH, true);
            $homePath     = SnapIO::safePathUntrailingslashit(get_home_path(), true);
            $isEquivalent = (strcmp($homePath, $absPath) === 0);
        }
        return $isEquivalent;
    }
}
