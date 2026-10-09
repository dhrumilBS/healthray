<?php

namespace  Duplicator\Installer\Core\Deploy\Database;

use Duplicator\Installer\Core\Params\Models\SiteOwrMap;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Utils\Log\Log;
use DUPX_ArchiveConfig;
use DUPX_DB;
use DUPX_DB_Functions;
use DUPX_DBInstall;
use Duplicator\Installer\Core\InstState;
use DUPX_NOTICE_ITEM;
use DUPX_NOTICE_MANAGER;

/**
 * Class with db cleanup functions
 */
class DbCleanup
{
    /**
     * Cleanup extra entities (views, procs, funcs)
     *
     * @return void
     */
    public static function cleanupExtra(): void
    {
        if (InstState::isRestoreBackup() ||  InstState::isAddSiteOnMultisite()) {
            return;
        }

        Log::info("CLEANUP EXTRA");
        $paramsManager = PrmMng::getInstance();

        if (!$paramsManager->getValue(PrmMng::PARAM_DB_VIEW_CREATION)) {
            self::dropViews();
            Log::info("\t- VIEWS DROPPED");
        } else {
            Log::info("\t- SKIP DROP VIEWS");
        }

        if (!$paramsManager->getValue(PrmMng::PARAM_DB_PROC_CREATION)) {
            self::dropProcs();
            Log::info("\t- PROCS DROPPED");
        } else {
            Log::info("\t- SKIP DROP PROCS");
        }

        if (!$paramsManager->getValue(PrmMng::PARAM_DB_FUNC_CREATION)) {
            self::dropFuncs();
            Log::info("\t- FUNCS DROPPED");
        } else {
            Log::info("\t- SKIP DROP FUNCS");
        }
    }

    /**
     * Cleanup packages
     *
     * @return void
     */
    public static function cleanupPackages(): void
    {
        if (InstState::isAddSiteOnMultisite()) {
            return;
        }

        if (InstState::isRestoreBackup()) {
            Log::info("REMOVE CURRENT PACKAGE IN BACKUP");
            self::deletePackageInBackup();
        } else {
            Log::info("EMPTY PACKAGES TABLE");
            self::emptyDuplicatorPackages();
        }

        do_action('duplicator_installer_after_cleanup_packages');
    }

    /**
     * Cleanup activity logs
     *
     * @return void
     */
    public static function cleanupActivityLogs(): void
    {
        if (InstState::isRestoreBackup()) {
            return;
        }

        $dbh               = DUPX_DB_Functions::getInstance()->dbConnection();
        $activityLogsTable = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getActivityLogsTableName());
        if (DUPX_DB::tableExists($dbh, $activityLogsTable)) {
            $count = DUPX_DB::chunksDelete($dbh, $activityLogsTable, '1 = 1');
            Log::info(sprintf('DATABASE ACTIVITY LOGS DELETED [ROWS:%6d]', $count));
        }
    }

    /**
     * Cleanup options tables (remove transientes ..)
     *
     * @return int return number of items deleted
     */
    public static function cleanupOptions()
    {
        if (InstState::isRestoreBackup()) {
            return 0;
        }

        $dbh = DUPX_DB_Functions::getInstance()->dbConnection();

        $archiveConfig     = DUPX_ArchiveConfig::getInstance();
        $optionsTableList  = [];
        $deleteOptionConds = [];

        if (InstState::isAddSiteOnMultisite()) {
            /** @var SiteOwrMap[] $overwriteMapping */
            $overwriteMapping = PrmMng::getInstance()->getValue(PrmMng::PARAM_SUBSITE_OVERWRITE_MAPPING);
            foreach ($overwriteMapping as $map) {
                $targetInfo         = $map->getTargetSiteInfo();
                $optionsTableList[] = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getOptionsTableName($targetInfo['blog_prefix']));
            }
        } else {
            $optionsTableList[]  = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getOptionsTableName());
            $deleteOptionConds[] = '`option_name` = "dupli_opt_plugin_data_stats"';
            $deleteOptionConds[] = '`option_name` = "dupli_opt_unique_id"';
        }

        $deleteOptionConds[] = '`option_name` LIKE "\_transient%"';
        $deleteOptionConds[] = '`option_name` LIKE "\_site\_transient%"';

        $opts_delete = [];
        foreach ($archiveConfig->opts_delete as $value) {
            $opts_delete[] = '"' . mysqli_real_escape_string($dbh, $value) . '"';
        }
        if (count($opts_delete) > 0) {
            $deleteOptionConds[] = '`option_name` IN (' . implode(',', $opts_delete) . ')';
        }

        $count = 0;
        foreach ($optionsTableList as $optionsTable) {
            $log = "CLEAN OPTIONS [" . $optionsTable . "]";
            foreach ($deleteOptionConds as $cond) {
                $log .= "\n\t" . $cond;
            }
            Log::info($log);
            $count += DUPX_DB::chunksDelete($dbh, $optionsTable, implode(' OR ', $deleteOptionConds));
            Log::info(sprintf('DATABASE OPTIONS DELETED [ROWS:%6d]', $count));
        }
        return $count;
    }

    /**
     * Delete current package in backup
     *
     * @return void
     */
    protected static function deletePackageInBackup()
    {
        $dbh       = DUPX_DB_Functions::getInstance()->dbConnection();
        $packageId = DUPX_ArchiveConfig::getInstance()->packInfo->packageId;
        Log::info("CLEANUP CURRENT PACKAGE STATUS ID " . $packageId);

        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        if (!$overwriteData['packagesTableExists']) {
            // Clean current package only if is extracted from backup
            $packagesTable = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getPackagesTableName());
            DUPX_DB::mysqli_query($dbh, 'DELETE FROM `' . $packagesTable . '` WHERE `id` = ' . (int) $packageId);
        }
    }

    /**
     * Empty duplicator packages table
     *
     * @return int return number of packages deleted
     */
    protected static function emptyDuplicatorPackages()
    {
        Log::info("CLEAN PACKAGES");
        $dbh           = DUPX_DB_Functions::getInstance()->dbConnection();
        $packagesTable = mysqli_real_escape_string($dbh, DUPX_DB_Functions::getPackagesTableName());
        if (DUPX_DB::tableExists($dbh, $packagesTable)) {
            $count = DUPX_DB::chunksDelete($dbh, $packagesTable, '1 = 1');
            Log::info(sprintf('DATABASE PACKAGE DELETED [ROWS:%6d]', $count));
            return $count;
        }
        Log::info('DATABASE PACKAGES TABLE MISSING');
        return 0;
    }


    /**
     * Drop db procedures
     *
     * @return void
     */
    public static function dropProcs(): void
    {
        $dbh    = DUPX_DB_Functions::getInstance()->dbConnection();
        $dbName = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME);

        $sql      = "SHOW PROCEDURE STATUS WHERE db='{$dbName}'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($dbh, $sql))) {
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'PROCEDURE CLEAN ERROR: ' . mysqli_error($dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of PROCEDURES from database "%s".', $dbName),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ]);

            Log::info("PROCEDURE CLEAN ERROR: Could not get list of PROCEDURES to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $proc_name = $row[1];
            $sql       = "DROP PROCEDURE IF EXISTS `" . mysqli_real_escape_string($dbh, $dbName) . "`.`" . mysqli_real_escape_string($dbh, $proc_name) . "`";
            if (!DUPX_DB::mysqli_query($dbh, $sql)) {
                $err = mysqli_error($dbh);
                $nManager->addNextStepNotice([
                    'shortMsg'    => 'PROCEDURE CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove PROCEDURE "%s" from database "%s".<br/>', $proc_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-proc-fail-msg');

                $nManager->addFinalReportNotice([
                    'shortMsg'    => 'PROCEDURE CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove PROCEDURE "%s" from database "%s".', $proc_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ]);

                Log::info("PROCEDURE CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, DUPX_DBInstall::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice([
            'shortMsg'    => 'PROCEDURE CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(
                'PROCEDURE CLEAN FAILURE. ' .
                    'Please remove all procedures from this database and try the installation again. ' .
                    'If no procedures show in the database, then Drop the database and re-create it.<br/>' .
                    'ERROR MESSAGE: %s <br/><br/>',
                mysqli_error($dbh)
            ),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
        ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-proc-fail-msg');
    }

    /**
     * Drop db functions
     *
     * @return void
     */
    public static function dropFuncs(): void
    {
        $dbh    = DUPX_DB_Functions::getInstance()->dbConnection();
        $dbName = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME);

        $sql      = "SHOW FUNCTION STATUS WHERE db='{$dbName}'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($dbh, $sql))) {
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'FUNCTION CLEAN ERROR: ' . mysqli_error($dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of FUNCTIONS from database "%s".', $dbName),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ]);

            Log::info("FUNCTION CLEAN ERROR: Could not get list of FUNCTIONS to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $func_name = $row[1];
            $sql       = "DROP FUNCTION IF EXISTS `" . mysqli_real_escape_string($dbh, $dbName) . "`.`" . mysqli_real_escape_string($dbh, $func_name) . "`";
            if (!DUPX_DB::mysqli_query($dbh, $sql)) {
                $err = mysqli_error($dbh);
                $nManager->addNextStepNotice([
                    'shortMsg'    => 'FUNCTION CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove FUNCTION "%s" from database "%s".<br/>', $func_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-func-fail-msg');

                $nManager->addFinalReportNotice([
                    'shortMsg'    => 'FUNCTION CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove FUNCTION "%s" from database "%s".', $func_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ]);

                Log::info("FUNCTION CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, DUPX_DBInstall::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice([
            'shortMsg'    => 'FUNCTION CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(
                'FUNCTION CLEAN FAILURE. ' .
                    'Please remove all functions from this database and try the installation again. ' .
                    'If no functions show in the database, then Drop the database and re-create it.<br/>' .
                    'ERROR MESSAGE: %s <br/><br/>',
                mysqli_error($dbh)
            ),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
        ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-func-fail-msg');
    }

    /**
     * Drop db views
     *
     * @return void
     */
    public static function dropViews(): void
    {
        $dbh    = DUPX_DB_Functions::getInstance()->dbConnection();
        $dbName = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_NAME);

        $sql      = "SHOW FULL TABLES WHERE Table_Type = 'VIEW'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($dbh, $sql))) {
            $nManager->addFinalReportNotice([
                'shortMsg'    => 'VIEW CLEAN ERROR: ' . mysqli_error($dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of VIEWS from database "%s"', $dbName),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ]);

            Log::info("VIEW CLEAN ERROR: Could not get list of VIEWS to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $view_name = $row[0];
            $sql       = "DROP VIEW `" . mysqli_real_escape_string($dbh, $dbName) . "`.`" . mysqli_real_escape_string($dbh, $view_name) . "`";
            if (!DUPX_DB::mysqli_query($dbh, $sql)) {
                $err = mysqli_error($dbh);

                $nManager->addNextStepNotice([
                    'shortMsg'    => 'VIEW CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove VIEW "%s" from database "%s".<br/>', $view_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-view-fail-msg');

                $nManager->addFinalReportNotice([
                    'shortMsg'    => 'VIEW CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove VIEW "%s" from database "%s"', $view_name, $dbName),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ]);

                Log::info("VIEW CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, DUPX_DBInstall::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice([
            'shortMsg'    => 'VIEW CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(
                'VIEW CLEAN FAILURE. ' .
                    'Please remove all views from this database and try the installation again. ' .
                    'If no views show in the database, then Drop the database and re-create it.<br/>' .
                    'ERROR MESSAGE: %s <br/><br/>',
                mysqli_error($dbh)
            ),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
        ], DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-view-fail-msg');
    }
}
