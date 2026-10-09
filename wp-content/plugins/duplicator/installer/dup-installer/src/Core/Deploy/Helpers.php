<?php

namespace Duplicator\Installer\Core\Deploy;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Package\DescriptorTheme;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapUtil;
use DUPX_DB;
use DUPX_DB_Functions;
use Exception;
use mysqli;

class Helpers
{
    /**
     * Load WordPress dependencies
     *
     * @return bool $loaded
     *
     * @throws Exception
     */
    public static function loadWP()
    {
        static $loaded = null;
        if (is_null($loaded)) {
            $wpRootDir = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_WP_CORE_NEW);
            require_once($wpRootDir . '/wp-load.php');
            if (!class_exists('WP_Privacy_Policy_Content')) {
                require_once($wpRootDir . '/wp-admin/includes/misc.php');
            }
            if (!function_exists('request_filesystem_credentials')) {
                require_once($wpRootDir . '/wp-admin/includes/file.php');
            }
            if (!function_exists('get_plugins')) {
                require_once $wpRootDir . '/wp-admin/includes/plugin.php';
            }
            if (!function_exists('delete_theme')) {
                require_once $wpRootDir . '/wp-admin/includes/theme.php';
            }
            $GLOBALS['wpdb']->show_errors(false);
            $loaded = true;
        }
        return $loaded;
    }

    /**
     * Check if Theme is enabled
     *
     * @param DescriptorTheme $theme Theme object
     *
     * @return boolean
     *
     * @throws Exception
     */
    public static function isThemeEnable(DescriptorTheme $theme): bool
    {
        switch (InstState::getInstType()) {
            case InstState::TYPE_SINGLE:
            case InstState::TYPE_RBACKUP_SINGLE:
            case InstState::TYPE_RECOVERY_SINGLE:
                if ($theme->isActive) {
                    return true;
                }
                break;
            case InstState::TYPE_MSUBDOMAIN:
            case InstState::TYPE_MSUBFOLDER:
            case InstState::TYPE_RBACKUP_MSUBDOMAIN:
            case InstState::TYPE_RBACKUP_MSUBFOLDER:
            case InstState::TYPE_RECOVERY_MSUBDOMAIN:
            case InstState::TYPE_RECOVERY_MSUBFOLDER:
                if (count($theme->isActive) > 0) {
                    return true;
                }
                break;
            case InstState::TYPE_STANDALONE:
                if (in_array(PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_ID), $theme->isActive)) {
                    return true;
                }
                break;
            case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
            case InstState::TYPE_SINGLE_ON_SUBFOLDER:
            case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
            case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                return true;
            case InstState::TYPE_NOT_SET:
            default:
                throw new Exception('Invalid installer type');
        }

        return false;
    }

    /**
     * Check if a parent theme has a child theme enabled
     *
     * @param DescriptorTheme   $parentTheme Parent Theme Object
     * @param DescriptorTheme[] $themes      Themes List
     *
     * @return boolean
     * @throws Exception
     */
    public static function haveChildEnable(DescriptorTheme $parentTheme, &$themes): bool
    {
        foreach ($themes as $theme) {
            if ($theme->parentTheme === $parentTheme->slug) {
                if (Helpers::isThemeEnable($theme)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param mysqli $dbh Database connection
     *
     * @return int[]
     */
    public static function getSuperAdminsUserIds(\mysqli $dbh): array
    {
        $result = [];

        if (InstState::isNewSiteIsMultisite()) {
            $paramsManager   = PrmMng::getInstance();
            $basePrefix      = $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
            $usersTableName  = "{$basePrefix}users";
            $superAdminsList = self::getSuperAdminUsernames($dbh, $basePrefix);

            if (!empty($superAdminsList)) {
                $sql = "SELECT ID FROM {$usersTableName} WHERE user_login IN ('" . implode("','", $superAdminsList) . "')";

                $queryResult = DUPX_DB::queryToArray($dbh, $sql);
                foreach ($queryResult as $superAdminsResult) {
                    $result[] = $superAdminsResult[0];
                }
            }
        }

        return $result;
    }

    /**
     * Get Super Admin Users names
     *
     * @param mysqli $dbh        Database connection
     * @param string $basePrefix WordPress Tables Prefix
     *
     * @return string[]
     *
     * @throws Exception
     */
    public static function getSuperAdminUsernames(\mysqli $dbh, string $basePrefix): array
    {
        $result            = [];
        $siteMetaTableName = "{$basePrefix}sitemeta";

        if (InstState::isNewSiteIsMultisite() && DUPX_DB_Functions::getInstance()->tablesExist($siteMetaTableName)) {
            $sql                = "SELECT meta_value FROM {$siteMetaTableName} WHERE meta_key = 'site_admins'";
            $superAdminsResults = DUPX_DB::queryToArray($dbh, $sql);

            if (isset($superAdminsResults[0][0])) {
                if (!SnapUtil::safeUnserialize($superAdminsResults[0][0], $result) || !is_array($result)) {
                    throw new Exception('Invalid site_admins value in ' . $siteMetaTableName . ': expected a serialized array');
                }
                Log::info('SUPER ADMIN USERS: ' . print_r($result, true));
            }
        }

        return $result;
    }
}
