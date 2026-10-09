<?php

namespace Duplicator\Libs\WpUtils;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Installer\Package\DescriptorSubsite;
use Duplicator\Libs\Snap\SnapDB;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapURL;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Package\Archive\PackageArchive;
use stdClass;

class WpUtilsMultisite
{
    /**
     * Returns the subsite info list
     *
     * @param int[]    $filteredSites  List of sites to filter
     * @param string[] $filteredTables List of tables to filter
     * @param string[] $filteredPaths  List of paths to filter
     *
     * @return DescriptorSubsite[]
     */
    public static function getSubsites(array $filteredSites = [], array $filteredTables = [], array $filteredPaths = []): array
    {
        if (!is_multisite()) {
            return [self::getSubsiteInfo(1, $filteredTables, $filteredPaths)];
        }

        $site_array = [];

        DupLog::trace("NETWORK SITES");

        foreach (SnapWP::getSitesIds() as $siteId) {
            if (in_array($siteId, $filteredSites)) {
                continue;
            }
            if (($siteInfo = self::getSubsiteInfo($siteId, $filteredTables, $filteredPaths)) == false) {
                continue;
            }
            array_push($site_array, $siteInfo);
            DupLog::trace(
                "Multisite subsite detected. ID={$siteInfo->id} Domain={$siteInfo->domain} Path={$siteInfo->path} Blogname={$siteInfo->blogname}"
            );
        }

        return $site_array;
    }

    /**
     * Returns the subsite info by id
     *
     * @param int $subsiteId subsite id
     *
     * @return false|DescriptorSubsite false on failure
     */
    public static function getSubsiteInfoById(int $subsiteId)
    {
        if (!is_multisite()) {
            $subsiteId = 1;
        }
        return self::getSubsiteInfo($subsiteId);
    }

    /**
     * Get subsite info, if is not multisite, it will return the main site info
     *
     * @param int      $siteId         subsite id
     * @param string[] $filteredTables Filtered tables
     * @param string[] $filteredPaths  Filtered paths
     *
     * @return false|DescriptorSubsite false on failure
     */
    public static function getSubsiteInfo(int $siteId = 1, array $filteredTables = [], array $filteredPaths = [])
    {
        if (is_multisite()) {
            if (($siteDetails = get_blog_details($siteId)) == false) {
                return false;
            }
        } else {
            $siteId                = 1;
            $siteDetails           = new stdClass();
            $home                  = WpArchiveUtils::getOriginalUrls('home');
            $parsedHome            = SnapURL::parseUrl($home);
            $siteDetails->domain   = $parsedHome['host'];
            $siteDetails->path     = trailingslashit($parsedHome['path']);
            $siteDetails->blogname = sanitize_text_field(get_option('blogname'));
        }

        $subsiteID             = $siteId;
        $siteInfo              = new DescriptorSubsite();
        $siteInfo->id          = $subsiteID;
        $siteInfo->domain      = $siteDetails->domain;
        $siteInfo->path        = $siteDetails->path;
        $siteInfo->blogname    = $siteDetails->blogname;
        $siteInfo->blog_prefix = $GLOBALS['wpdb']->get_blog_prefix($subsiteID);
        if (count($filteredTables) > 0) {
            $siteInfo->filteredTables = array_values(array_intersect(self::getSubsiteTables($subsiteID), $filteredTables));
        } else {
            $siteInfo->filteredTables = [];
        }
        $siteInfo->adminUsers  = SnapWP::getAdminUserLists($siteInfo->id);
        $siteInfo->fullHomeUrl = get_home_url($siteId);
        $siteInfo->fullSiteUrl = get_site_url($siteId);

        if ($siteId > 1) {
            switch_to_blog($siteId);
        }

        $uploadData                   = wp_upload_dir();
        $uploadPath                   = $uploadData['basedir'];
        $siteInfo->uploadPath         = SnapIO::getRelativePath($uploadPath, WpArchiveUtils::getTargetRootPath(), true);
        $siteInfo->fullUploadPath     = untrailingslashit($uploadPath);
        $siteInfo->fullUploadSafePath = SnapIO::safePathUntrailingslashit($uploadPath);
        $siteInfo->fullUploadUrl      = $uploadData['baseurl'];
        if (count($filteredPaths)) {
            $globalDirFilters        = PackageArchive::getDefaultGlobalDirFilter();
            $siteInfo->filteredPaths = array_values(array_filter($filteredPaths, function ($path) use ($uploadPath, $subsiteID, $globalDirFilters): bool {
                if (
                    ($relativeUpload = SnapIO::getRelativePath($path, $uploadPath)) === false ||
                    in_array($path, $globalDirFilters)
                ) {
                    return false;
                }

                if ($subsiteID > 1) {
                    return true;
                } else {
                    // no check on blogs.dir because in wp-content/blogs.dir not in upload folder
                    return strpos($relativeUpload, 'sites') !== 0;
                }
            }));
        } else {
            $siteInfo->filteredPaths = [];
        }

        if ($siteId > 1) {
            restore_current_blog();
        }
        return $siteInfo;
    }

    /**
     * @param int $subsiteID subsite id
     *
     * @return string[] List of tables belonging to subsite
     */
    public static function getSubsiteTables(int $subsiteID): array
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $basePrefix    = $wpdb->base_prefix;
        $subsitePrefix = $wpdb->get_blog_prefix($subsiteID);

        $sharedTables = array_map(fn($table): string => $basePrefix . $table, SnapWP::getSharedTables());

        $multisiteOnlyTables = array_map(fn($table): string => $basePrefix . $table, SnapWP::getMultisiteTables());

        $sql        = "";
        $dbnameSafe = esc_sql(DB_NAME);

        if ($subsiteID != 1) {
            $regex      = '^' . SnapDB::quoteRegex($subsitePrefix);
            $regexpSafe = esc_sql($regex);

            $sharedTablesSafe = "'" . implode(
                "', '",
                esc_sql($sharedTables)
            ) . "'";
            $sql              = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbnameSafe' AND ";
            $sql             .= "(TABLE_NAME REGEXP '$regexpSafe' OR TABLE_NAME IN ($sharedTablesSafe))";
        } else {
            $regexMain        = '^' . SnapDB::quoteRegex($basePrefix);
            $regexpMainSafe   = esc_sql($regexMain);
            $regexNotSub      = '^' . SnapDB::quoteRegex($basePrefix) . '[0-9]+_';
            $regexpNotSubSafe = esc_sql($regexNotSub);

            $multisiteOnlyTablesSafe = "'" . implode(
                "', '",
                esc_sql($multisiteOnlyTables)
            ) . "'";
            $sql                     = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbnameSafe' AND ";
            $sql                    .= "TABLE_NAME REGEXP '$regexpMainSafe' AND ";
            $sql                    .= "TABLE_NAME NOT REGEXP '$regexpNotSubSafe' AND ";
            $sql                    .= "TABLE_NAME NOT IN ($multisiteOnlyTablesSafe)";
        }
        return $wpdb->get_col($sql);
    }
}
