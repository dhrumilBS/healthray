<?php

namespace Duplicator\Utils\UsageStatistics;

use Duplicator\Models\GlobalEntity;
use Duplicator\Installer\Core\InstState;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Libs\WpUtils\WpDbUtils;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Exception;

class StatsUtil
{
    /**
     * Web server family detected from the request signature.
     *
     * @return string One of the SnapServer::WEBSERVER_* constants
     */
    public static function getServerFamily(): string
    {
        return SnapServer::parseServerSoftware(SnapServer::getServerSoftware())['family'];
    }

    /**
     * Web server family with its short version (`apache/2.4`), or the bare
     * family when no version is exposed.
     *
     * @return string
     */
    public static function getServerLabel(): string
    {
        $parsed = SnapServer::parseServerSoftware(SnapServer::getServerSoftware());
        return $parsed['version'] === '' ? $parsed['family'] : $parsed['family'] . '/' . $parsed['version'];
    }

    /**
     * Get db mode
     *
     * @return string
     */
    public static function getDbBuildMode(): string
    {
        switch (WpDbUtils::getBuildMode()) {
            case WpDbUtils::BUILD_MODE_MYSQLDUMP:
                return 'mysqldump';
            case WpDbUtils::BUILD_MODE_PHP_MULTI_THREAD:
                return 'php-multi';
            case WpDbUtils::BUILD_MODE_PHP_SINGLE_THREAD:
                return 'php-single';
            default:
                throw new Exception('Unknown db build mode');
        }
    }

    /**
     * Get archive mode
     *
     * @return string
     */
    public static function getArchiveBuildMode(): string
    {
        $global = GlobalEntity::getInstance();
        switch ($global->getBuildMode()) {
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                if ($global->getZipArchiveMode() == PackageArchive::ZIP_MODE_MULTI_THREAD) {
                    return 'zip-multi';
                } else {
                    return 'zip-single';
                }
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return 'dup';
            default:
                return 'shellzip';
        }
    }



    /**
     * Get install type
     *
     * @param int $type Install type
     *
     * @return string
     */
    public static function getInstallType($type): string
    {
        switch ($type) {
            case InstState::TYPE_SINGLE:
                return 'single';
            case InstState::TYPE_STANDALONE:
                return 'standalone';
            case InstState::TYPE_MSUBDOMAIN:
                return 'msubdomain';
            case InstState::TYPE_MSUBFOLDER:
                return 'msubfolder';
            case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
                return 'single_on_subdomain';
            case InstState::TYPE_SINGLE_ON_SUBFOLDER:
                return 'single_on_subfolder';
            case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
                return 'subsite_on_subdomain';
            case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                return 'subsite_on_subfolder';
            case InstState::TYPE_RBACKUP_SINGLE:
                return 'rbackup_single';
            case InstState::TYPE_RBACKUP_MSUBDOMAIN:
                return 'rbackup_msubdomain';
            case InstState::TYPE_RBACKUP_MSUBFOLDER:
                return 'rbackup_msubfolder';
            case InstState::TYPE_RECOVERY_SINGLE:
                return 'recovery_single';
            case InstState::TYPE_RECOVERY_MSUBDOMAIN:
                return 'recovery_msubdomain';
            case InstState::TYPE_RECOVERY_MSUBFOLDER:
                return 'recovery_msubfolder';
            default:
                return 'not_set';
        }
    }

    /**
     * Get stats components
     *
     * @param string[] $components Components
     *
     * @return string
     */
    public static function getStatsComponents($components): string
    {
        $result = [];
        foreach ($components as $component) {
            switch ($component) {
                case BuildComponents::COMP_DB:
                    $result[] = 'db';
                    break;
                case BuildComponents::COMP_CORE:
                    $result[] = 'core';
                    break;
                case BuildComponents::COMP_PLUGINS:
                    $result[] = 'plugins';
                    break;
                case BuildComponents::COMP_PLUGINS_ACTIVE:
                    $result[] = 'plugins_active';
                    break;
                case BuildComponents::COMP_THEMES:
                    $result[] = 'themes';
                    break;
                case BuildComponents::COMP_THEMES_ACTIVE:
                    $result[] = 'themes_active';
                    break;
                case BuildComponents::COMP_UPLOADS:
                    $result[] = 'uploads';
                    break;
                case BuildComponents::COMP_OTHER:
                    $result[] = 'other';
                    break;
            }
        }
        return implode(',', $result);
    }

    /**
     * Get am family plugins
     *
     * @return string
     */
    public static function getAmFamily(): string
    {
        $plugins = [
            'dup-lite' => 'duplicator/duplicator.php',
            'dup-pro'  => 'duplicator-pro/duplicator-pro.php',
        ];

        $result = [];
        foreach ($plugins as $name => $file) {
            if (SnapWP::isPluginInstalled($file)) {
                $result[] = $name;
            }
        }

        return implode(',', $result);
    }

    /**
     * Get logic modes
     *
     * @param string[] $modes Logic modes
     *
     * @return string
     */
    public static function getLogicModes($modes): string
    {
        $result = [];
        foreach ($modes as $mode) {
            switch ($mode) {
                case InstState::LOGIC_MODE_IMPORT:
                    $result[] = 'IMPORT';
                    break;
                case InstState::LOGIC_MODE_RECOVERY:
                    $result[] = 'RECOVERY';
                    break;
                case InstState::LOGIC_MODE_CLASSIC:
                    $result[] = 'CLASSIC';
                    break;
                case InstState::LOGIC_MODE_OVERWRITE:
                    $result[] = 'OVERWRITE';
                    break;
                case InstState::LOGIC_MODE_BRIDGE:
                    $result[] = 'BRIDGE';
                    break;
                case InstState::LOGIC_MODE_RESTORE_BACKUP:
                    $result[] = 'RESTORE';
                    break;
            }
        }
        return implode(',', $result);
    }

    /**
     * Get template
     *
     * @param string $template Template
     *
     * @return string
     */
    public static function getTemplate($template): string
    {
        switch ($template) {
            case 'base':
                return 'CLASSIC_BASE';
            case 'import-base':
                return 'IMPORT_BASE';
            case 'import-advanced':
                return 'IMPORT_ADV';
            case 'recovery':
                return 'RECOVERY';
            case 'default':
            default:
                return 'CLASSIC_ADV';
        }
    }

    /**
     * Sanitize fields with rule string
     * [nullable][type][|max:number]
     * - ?string|max:25
     * - int
     *
     * @param array<string, mixed>  $data  Data
     * @param array<string, string> $rules Rules
     *
     * @return array<string, mixed>
     */
    public static function sanitizeFields($data, $rules)
    {
        foreach ($data as $key => $val) {
            if (!isset($rules[$key])) {
                continue;
            }

            $matches = null;
            if (preg_match('/(\??)(int|float|bool|string)(?:\|max:(\d+))?/', $rules[$key], $matches) !== 1) {
                throw new Exception("Invalid sanitize rule: {$rules[$key]}");
            }

            $nullable = $matches[1] === '?';
            $type     = $matches[2];
            $max      = isset($matches[3]) ? (int) $matches[3] : PHP_INT_MAX;

            if ($nullable && $val === null) {
                continue;
            }

            switch ($type) {
                case 'int':
                    $data[$key] = (int) $val;
                    break;
                case 'float':
                    $data[$key] = (float) $val;
                    break;
                case 'bool':
                    $data[$key] = (bool) $val;
                    break;
                case 'string':
                    $data[$key] = substr((string) $val, 0, $max);
                    break;
                default:
                    throw new Exception("Unknown sanitize rule: {$rules[$key]}");
            }
        }

        return $data;
    }
}
