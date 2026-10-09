<?php

/**
 * Trait for package scan operations
 */

declare(strict_types=1);

namespace Duplicator\Package;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Index\FileIndexManager;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapOpenBasedir;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Libs\WpUtils\WpArchiveUtils;
use Duplicator\Libs\WpUtils\WpUtilsMultisite;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Database\DatabasePkg;
use Duplicator\Utils\Logging\DupLog;
use Throwable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Trait TraitPackageScan
 *
 * Handles package scanning operations including scan report generation
 * and retrieval of scan data from JSON files.
 *
 * @phpstan-require-extends AbstractPackage
 *
 * @property string         $ScanFile   Scan file name
 * @property PackageArchive $Archive    Package archive object
 * @property DatabasePkg    $Database   Database package object
 * @property PackMultisite  $Multisite  Multisite configuration
 * @property string[]       $components Build components flags
 */
trait TraitPackageScan
{
    /**
     * Generates a scan report
     *
     * @return array<string,mixed> of scan results
     * @throws Throwable Any scan-report failure is propagated unchanged to the caller
     */
    public function createScanReport(): array
    {
        global $wpdb;
        $report = [];
        DupLog::trace('Scanning');
        $global = GlobalEntity::getInstance();
        do_action('duplicator_before_scan_report', $this);

        //Set tree filters
        $this->Archive->setTreeFilters();

        //Load scan data necessary for report
        $db                        = $this->Database->getScanData();
        $timerStart                = microtime(true);
        $this->ScanFile            = "{$this->getNameHash()}_scan.json";
        $report['RPT']['ScanTime'] = "0";
        $report['RPT']['ScanFile'] = $this->ScanFile;
        //FILES
        $scanPath          = DUPLICATOR_SSDIR_PATH_TMP . "/{$this->ScanFile}";
        $dirCount          = $this->Archive->DirCount;
        $fileCount         = $this->Archive->FileCount;
        $fullCount         = $dirCount + $fileCount;
        $unreadable        = array_merge($this->Archive->FilterInfo->Files->Unreadable, $this->Archive->FilterInfo->Dirs->Unreadable);
        $scanArchiveEngine = ($buildOptions = $this->getBuildOptions()) !== null ?
            $buildOptions->getArchiveEngine() : $global->getBuildMode();
        $site_warning_size = $scanArchiveEngine === PackageArchive::BUILD_MODE_ZIP_ARCHIVE ?
            DUPLICATOR_SCAN_SITE_ZIP_ARCHIVE_WARNING_SIZE : DUPLICATOR_SCAN_SITE_WARNING_SIZE;
        $filteredTables    = ($this->Database->FilterOn ? explode(',', $this->Database->FilterTables) : []);

        // On multisite, skip getSubsites here — MultisiteScanController recalculates
        // with actual FilterSites via the duplicator_scan_report filter.
        if (is_multisite()) {
            $subsites              = [];
            $hasImportableSites    = false;
            $hasNotImportableSites = false;
        } else {
            $subsites              = WpUtilsMultisite::getSubsites([], $filteredTables);
            $hasImportableSites    = SnapUtil::inArrayExtended($subsites, fn($subsite): bool => count($subsite->filteredTables) === 0);
            $hasNotImportableSites = SnapUtil::inArrayExtended($subsites, fn($subsite): bool => count($subsite->filteredTables) > 0);
        }

        $hasFilteredSiteTables = $this->Database->info->tablesBaseCount !== $this->Database->info->tablesFinalCount;
        $pathsOutOpenbaseDir   = array_filter($this->Archive->FilterInfo->Dirs->Unknown, fn(string $path): bool => !SnapOpenBasedir::isPathValid($path));

        // Check if the user has the privileges to show the CREATE FUNCTION and CREATE PROCEDURE statements
        $privileges_to_show_create_func = true;
        $query                          = $wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db` = %s", $wpdb->dbname);
        $procedures                     = $wpdb->get_col($query, 1);
        if (count($procedures)) {
            $create                         = $wpdb->get_row("SHOW CREATE PROCEDURE `" . $procedures[0] . "`", ARRAY_N);
            $privileges_to_show_create_func = isset($create[2]);
        }

        $query     = $wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db` = %s", $wpdb->dbname);
        $functions = $wpdb->get_col($query, 1);
        if (count($functions)) {
            $create                         = $wpdb->get_row("SHOW CREATE FUNCTION `" . $functions[0] . "`", ARRAY_N);
            $privileges_to_show_create_func = $privileges_to_show_create_func && isset($create[2]);
        }
        $privileges_to_show_create_func = apply_filters('duplicator_privileges_to_show_create_func', $privileges_to_show_create_func);

        //Add info to report to
        $report = [
            'Status' => 1,
            'ARC'    => [
                'Size'                => SnapString::byteSize($this->Archive->Size),
                'DirCount'            => number_format($dirCount),
                'FileCount'           => number_format($fileCount),
                'FullCount'           => number_format($fullCount),
                'USize'               => $this->Archive->Size,
                'UDirCount'           => $dirCount,
                'UFileCount'          => $fileCount,
                'UFullCount'          => $fullCount,
                'UnreadableDirCount'  => $this->Archive->FilterInfo->Dirs->getUnreadableCount(),
                'UnreadableFileCount' => $this->Archive->FilterInfo->Files->getUnreadableCount(),
                'FilterDirsAll'       => $this->Archive->FilterDirsAll,
                'FilterFilesAll'      => $this->Archive->FilterFilesAll,
                'FilterExtsAll'       => $this->Archive->FilterExtsAll,
                'FilteredCoreDirs'    => $this->Archive->filterWpCoreFoldersList(),
                'RecursiveLinks'      => $this->Archive->RecursiveLinks,
                'UnreadableItems'     => $unreadable,
                'PathsOutOpenbaseDir' => $pathsOutOpenbaseDir,
                'Subsites'            => $subsites,
                'Status'              => [
                    'Size'                   => $this->Archive->Size <= $site_warning_size && $this->Archive->Size >= 0,
                    'Big'                    => count($this->Archive->FilterInfo->Files->Size) <= 0,
                    'AddonSites'             => count($this->Archive->FilterInfo->Dirs->AddonSites) <= 0,
                    'UnreadableItems'        => empty($this->Archive->RecursiveLinks) && empty($unreadable) && empty($pathsOutOpenbaseDir),
                    'showCreateFuncStatus'   => $privileges_to_show_create_func,
                    'showCreateFunc'         => $privileges_to_show_create_func,
                    'HasImportableSites'     => $hasImportableSites,
                    'HasNotImportableSites'  => $hasNotImportableSites,
                    'HasFilteredCoreFolders' => $this->Archive->hasWpCoreFolderFiltered(),
                    'HasFilteredSiteTables'  => $hasFilteredSiteTables,
                    'IsDBOnly'               => $this->isDBOnly(),
                    'PackageIsNotImportable' => !(
                        (!$hasFilteredSiteTables || $hasImportableSites) &&
                        !$hasNotImportableSites
                    ),
                ],
            ],
            'DB'     => [
                'Status'         => $db['Status'],
                'SizeInBytes'    => $db['Size'],
                'Size'           => SnapString::byteSize($db['Size']),
                'Rows'           => number_format($db['Rows']),
                'TableCount'     => $db['TableCount'],
                'TableList'      => $db['TableList'],
                'FilteredTables' => ($this->Database->FilterOn ? explode(',', $this->Database->FilterTables) : []),
                'DBExcluded'     => BuildComponents::isDBExcluded($this->components),
            ],
            'SRV'    => BuildRequirements::getChecks($this)['SRV'],
            'RPT'    => [
                'ScanCreated' => @date("Y-m-d H:i:s"),
                'ScanTime'    => SnapString::formattedElapsedTime(microtime(true), $timerStart),
                'ScanPath'    => $scanPath,
                'ScanFile'    => $this->ScanFile,
            ],
        ];

        /** @var array<string,mixed> $report Generic filter to modify scan report data before JSON serialization */
        $report = apply_filters('duplicator_scan_report', $report, $this);

        if (($json = JsonSerialize::serialize($report, JSON_PRETTY_PRINT | JsonSerialize::JSON_SKIP_CLASS_NAME)) === false) {
            throw new DupliException(
                'Problem encoding scan report json',
                DupliException::CODE_SCAN_WRITE_FAILED,
                __('Could not encode the scan report. Check the backup log for details.', 'duplicator')
            );
        }

        if (SnapIO::atomicWrite([$scanPath => $json]) === false) {
            throw new DupliException(
                'Problem writing scan file',
                DupliException::CODE_SCAN_WRITE_FAILED,
                __('Could not write the scan report file. Check the backup log for details.', 'duplicator')
            );
        }

        //Safe to clear at this point only JSON
        //report stores the full directory and file lists
        $this->Archive->Dirs  = [];
        $this->Archive->Files = [];
        /**
         * don't save filter info in report scan json.
         */
        $report['ARC']['FilterInfo'] = $this->Archive->FilterInfo;
        DupLog::trace("TOTAL SCAN TIME = " . SnapString::formattedElapsedTime(microtime(true), $timerStart));

        do_action('duplicator_after_scan_report', $this, $report);
        return $report;
    }

    /**
     * Write the scan section header to the Backup creation log.
     *
     * Idempotent: writes the header only once per build, so it can be called
     * at scan start (scheduled builds) or right before the scan results (manual builds).
     *
     * @return void
     */
    public function logScanSectionHeader(): void
    {
        if ($this->build_progress->scan_header_logged) {
            return;
        }

        $info  = "\n********************************************************************************\n";
        $info .= "SCAN:\n";
        $info .= "********************************************************************************\n";
        $info .= str_pad('SCAN START:', AbstractPackage::SCAN_LOG_PAD) . @date("Y-m-d H:i:s");
        DupLog::infoTrace($info);

        $this->build_progress->scan_header_logged = true;
    }

    /**
     * Write the scan results to the Backup creation log.
     *
     * Idempotent: writes the results only once per build, so it can be called
     * both after scan validation (scheduled builds) and at build start (manual builds).
     *
     * @return void
     */
    public function logScanReport(): void
    {
        if ($this->build_progress->scan_logged) {
            return;
        }

        $scanPath = DUPLICATOR_SSDIR_PATH_TMP . "/{$this->getNameHash()}_scan.json";
        if (
            ($json = SnapIO::safeFileGetContents($scanPath)) === false ||
            ($report = json_decode($json)) === null ||
            !isset($report->ARC->DirCount)
        ) {
            DupLog::trace("Can't read scan file {$scanPath}, skipping scan log section");
            return;
        }

        $this->logScanSectionHeader();

        $unreadableCount = (int) $report->ARC->UnreadableDirCount + (int) $report->ARC->UnreadableFileCount;
        $tablesTotal     = $this->Database->info->tablesBaseCount;
        $tablesCreate    = $this->Database->info->tablesFinalCount;
        $tablesFiltered  = $tablesTotal - $tablesCreate;
        $pad             = AbstractPackage::SCAN_LOG_PAD;

        $info  = "\n" . str_pad('SCAN COMPLETE:', $pad) . @date("Y-m-d H:i:s") . "\n";
        $info .= str_pad('TABLES:', $pad) . "total: {$tablesTotal} | filtered: {$tablesFiltered} | create: {$tablesCreate}\n";
        $info .= str_pad('DIRS:', $pad) . "{$report->ARC->DirCount} | FILES: {$report->ARC->FileCount} | TOTAL: {$report->ARC->FullCount}\n";
        $info .= str_pad('SIZE:', $pad) . "{$report->ARC->Size}\n";
        $info .= str_pad('UNREADABLE ITEMS:', $pad) . "{$unreadableCount}\n";
        if (($scanTime = $this->getStateDuration(AbstractPackage::STATUS_SCANNING)) >= 0) {
            $info .= str_pad('SCAN TIME:', $pad) . SnapString::formattedElapsedTime($scanTime, 0) . "\n";
        }
        $info .= "PACKAGE COMPONENTS:\n\t" . BuildComponents::displayComponentsList($this->components, ",\n\t");
        DupLog::infoTrace($info);

        $this->build_progress->scan_logged = true;
    }

    /**
     * Adds file and dirs lists to scan report.
     *
     * @param string $json_path    string The path to the json file
     * @param bool   $includeLists Include the file and dir lists in the report
     *
     * @return mixed The scan report
     */
    public function getScanReportFromJson($json_path, $includeLists = false)
    {
        if (!file_exists($json_path)) {
            throw new DupliException(
                "Can't find scan file: $json_path",
                DupliException::CODE_SCAN_READ_FAILED,
                __('A temporary file needed by the build was missing.', 'duplicator')
            );
        }

        $json_contents = file_get_contents($json_path);

        $report = json_decode($json_contents);
        if ($report === null) {
            throw new DupliException(
                "Couldn't decode scan file.",
                DupliException::CODE_SCAN_READ_FAILED,
                __('A temporary file needed by the build could not be read.', 'duplicator')
            );
        }

        if (
            !isset($report->ARC->DirCount, $report->ARC->FileCount, $report->ARC->FullCount) ||
            $report->ARC->DirCount === '' || $report->ARC->FileCount === '' || $report->ARC->FullCount === ''
        ) {
            throw new DupliException('Invalid Scan Report Detected', DupliException::CODE_SCAN_INVALID_REPORT);
        }

        if ($includeLists) {
            $targetRootPath     = WpArchiveUtils::getTargetRootPath();
            $indexManager       = $this->Archive->getIndexManager();
            $report->ARC->Dirs  = $indexManager->getPathArray(FileIndexManager::LIST_TYPE_DIRS, $targetRootPath);
            $report->ARC->Files = $indexManager->getPathArray(FileIndexManager::LIST_TYPE_FILES, $targetRootPath);
        }

        return $report;
    }
}
