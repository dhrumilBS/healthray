<?php

/**
 * Database gunzip phase
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

namespace Duplicator\Installer\Core\Deploy\Database;

use DUPX_Constants;
use DUPX_Package;
use DUPX_U;
use DUPX_Validation_manager;
use Duplicator\Installer\Utils\InstDescMng;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapGzip;
use Duplicator\Libs\Snap\SnapJson;
use Exception;
use VendorDuplicator\Amk\JsonSerialize\AbstractJsonSerializable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Decompresses every .sql.gz dump under the package dump directory and unlinks each
 * .gz once the corresponding .sql is on disk.
 *
 * Each call processes at least one file when the directory is non-empty so progress is
 * always made; the deadline is checked between files only. Within a single .sql.gz,
 * decompression must run to completion in one call: zlib can't seek a gzip stream
 * without re-decoding from the start, so chunked resume of an individual file would
 * degrade to O(n^2) over its size. Counts are persisted across requests so progress
 * can be displayed to the user.
 */
final class DbGunzip extends AbstractJsonSerializable
{
    /** @var float Microtime when the phase started */
    public $gunzipStart = 0.0;
    /** @var float Microtime when the current chunk started */
    public $chunkStart = 0.0;
    /** @var int Total .sql.gz files seen on first run, used for percent display */
    public $totalFiles = 0;
    /** @var int Number of .sql.gz files already decompressed */
    public $processedFiles = 0;
    /** @var bool True once every .sql.gz has been decompressed and unlinked */
    public $done = false;

    /** @var ?self */
    protected static $instance;

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        if (!DUPX_Validation_manager::isValidated()) {
            throw new Exception('Installer isn\'t validated');
        }
        $this->initData();
    }

    /**
     * Load existing state from disk, or initialize it from the current dump directory.
     *
     * @return void
     */
    private function initData(): void
    {
        if (file_exists(self::dataFilePath())) {
            Log::info('LOAD DB GUNZIP DATA FROM JSON', Log::LV_DETAILED);
            if (($json = @file_get_contents(self::dataFilePath())) === false) {
                throw new Exception('Can\'t load gunzip state file');
            }
            JsonSerialize::unserializeToObj($json, $this);
        } else {
            Log::info('INIT DB GUNZIP DATA', Log::LV_DETAILED);
            $this->gunzipStart    = DUPX_U::getMicrotime();
            $this->totalFiles     = count(DUPX_Package::getSqlDumpGzPaths());
            $this->processedFiles = 0;
            $this->done           = ($this->totalFiles === 0);
            $this->saveData();
        }
        $this->chunkStart = DUPX_U::getMicrotime();
    }

    /**
     * Persist current state to disk.
     *
     * @return bool
     */
    public function saveData(): bool
    {
        if (($json = SnapJson::jsonEncodePPrint($this)) === false) {
            Log::info('Can\'t encode gunzip data');
            return false;
        }

        if (@file_put_contents(self::dataFilePath(), $json) === false) {
            Log::info('Can\'t save gunzip state file');
            return false;
        }

        return true;
    }

    /**
     * Delete the persistent state file so the next run starts fresh.
     *
     * @return void
     */
    public static function resetData(): void
    {
        if (file_exists(self::dataFilePath())) {
            if (@unlink(self::dataFilePath()) === false) {
                throw new Exception('Can\'t delete gunzip state file');
            }
        }
    }

    /**
     * Process a chunk of .sql.gz files, honoring the soft deadline between files.
     *
     * @param int $timeout Soft deadline in seconds; pass a negative value to run uncapped
     *
     * @return void
     *
     * @throws Exception On any I/O or gzip failure
     */
    public function runChunk(int $timeout): void
    {
        if ($this->done) {
            return;
        }

        $gzFiles = DUPX_Package::getSqlDumpGzPaths();
        if (count($gzFiles) === 0) {
            $this->done = true;
            $this->saveData();
            return;
        }

        $deadline = $timeout < 0 ? INF : DUPX_U::getMicrotime() + $timeout;
        foreach ($gzFiles as $gzPath) {
            self::decompressOne($gzPath);
            $this->processedFiles++;

            if (DUPX_U::getMicrotime() > $deadline) {
                break;
            }
        }

        if ($this->processedFiles >= $this->totalFiles) {
            $this->done = true;
        }

        $this->saveData();
    }

    /**
     * Build the AJAX progress payload describing the current state.
     *
     * @return array{pass:int,perc:string,processedFiles:string,title:string}
     */
    public function getResult(): array
    {
        $result =  [
            'pass'           => 1,
            'perc'           => '100%',
            'processedFiles' => 'No compressed database dump found',
            'title'          => 'Decompressing Database',
        ];

        if ($this->totalFiles === 0) {
            return $result;
        }

        $result['processedFiles'] = 'Files processed: ' . number_format($this->processedFiles) . ' of ' . number_format($this->totalFiles);
        $result['perc']           = min(100, round(($this->processedFiles * 100) / $this->totalFiles)) . '%';

        if ($this->done) {
            $deltaTime = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->gunzipStart);
            Log::info("DB GUNZIP COMPLETE - RUNTIME: {$deltaTime}");
        } else {
            $result['pass'] = -1;
            $deltaTime      = DUPX_U::elapsedTime(DUPX_U::getMicrotime(), $this->chunkStart);
            Log::info("DB GUNZIP CHUNK COMPLETE - RUNTIME: {$deltaTime}");
        }

        return $result;
    }

    /**
     * Run one chunk and return the AJAX-ready result.
     *
     * @return array{pass:int,perc:string,processedFiles:string,title:string}
     */
    public function process(): array
    {
        $this->runChunk(DUPX_Constants::CHUNK_EXTRACTION_TIMEOUT_TIME_ZIP);
        return $this->getResult();
    }

    /**
     * Decompress a single .sql.gz to its sibling .sql, validate, and unlink the source.
     *
     * @param string $gzPath Absolute path to a .sql.gz file
     *
     * @return void
     *
     * @throws Exception If the source isn't a valid gzip stream or the output is empty
     */
    private static function decompressOne(string $gzPath): void
    {
        $sqlPath = substr($gzPath, 0, -3);

        SnapGzip::decompressFile($gzPath, $sqlPath);

        clearstatcache(true, $sqlPath);
        if (!is_file($sqlPath) || filesize($sqlPath) <= 0) {
            throw new Exception("Decompressed SQL dump is empty: {$sqlPath}");
        }

        if (@unlink($gzPath) === false) {
            Log::info("[WARN] Decompressed {$sqlPath} but couldn't remove source {$gzPath}");
        }

        Log::info('DB GUNZIP DONE: ' . basename($sqlPath));
    }

    /**
     * Path to the persistent state file.
     *
     * @return string
     */
    private static function dataFilePath(): string
    {
        static $path = null;
        if (is_null($path)) {
            $path = \DUPX_INIT . '/' . InstDescMng::getInstance()->getName(InstDescMng::TYPE_INST_DB_GUNZIP_DATA);
        }
        return $path;
    }
}
