<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Libs\Snap\SnapWP;
use Duplicator\Utils\Logging\DupLog;
use wpdb;

/**
 * Dump engine reading the tables through PHP.
 *
 * The rows are written by TableDumpWriter and the position inside the table
 * being written lives in a TableDumpCheckpoint, so a table stopped on the
 * deadline or by a killed worker resumes where it left off.
 */
final class PhpDumpEngine extends AbstractDumpEngine
{
    /** @var wpdb WordPress database handle, used for the schema queries */
    private $wpdb;
    private TableDumpCheckpoint $checkpoint;
    private TableDumpWriter $writer;
    private string $modeLabel;

    /**
     * Class constructor
     *
     * @param wpdb                $wpdb       WordPress database handle, used for the schema queries
     * @param TableDumpCheckpoint $checkpoint position inside the table being written, the same instance the writer holds
     * @param TableDumpWriter     $writer     writer appending the rows of a table to its data file
     * @param string              $modeLabel  build mode label written in the schema header
     */
    public function __construct($wpdb, TableDumpCheckpoint $checkpoint, TableDumpWriter $writer, string $modeLabel)
    {
        $this->wpdb       = $wpdb;
        $this->checkpoint = $checkpoint;
        $this->writer     = $writer;
        $this->modeLabel  = $modeLabel;
    }

    /**
     * Write the schema file in one pass
     *
     * @param string   $path            schema file path
     * @param string[] $tables          tables whose CREATE the schema holds, in order
     * @param bool     $includeRoutines whether procedures, functions and views are written
     *
     * @return int bytes written
     *
     * @throws DupliException if the structure of a table can't be read
     */
    public function writeSchema(string $path, array $tables, bool $includeRoutines): int
    {
        // Written in one pass: truncating makes a retry after an interrupted write idempotent
        $handle = $this->openFile($path, 'w');

        try {
            // NO_AUTO_VALUE_ON_ZERO avoids 'Invalid default value' errors on older MySQL versions
            $header  = "/* DUPLICATOR-PRO ({$this->modeLabel}) MYSQL SCRIPT CREATED ON : " . date("Y-m-d H:i:s") . " */\n\n";
            $header .= "/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n";
            $header .= "/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n";
            $header .= "/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;\n\n";
            SnapIO::fwrite($handle, $header);

            foreach ($tables as $table) {
                $create      = $this->getCreateTableRow($table);
                $createQuery = preg_replace('/^(\s*CREATE\s+TABLE\s+(?!IF NOT EXISTS))(`.+?`)/m', '$1IF NOT EXISTS $2', $create[1]);
                SnapIO::fwrite($handle, "{$createQuery};\n\n");
                DupLog::trace("DATABASE CREATE TABLE: " . $table . " OK");
            }

            if ($includeRoutines) {
                $this->writeRoutines($handle);
            }

            SnapIO::fwrite($handle, self::TABLE_CREATION_END_MARKER);
            SnapIO::fwrite($handle, self::eofMarker());
            $size = (int) fstat($handle)['size'];
        } finally {
            SnapIO::fclose($handle);
        }

        return $size;
    }

    /**
     * Write the data file of a table, resuming from the checkpoint when it is positioned on it
     *
     * @param string $table    table name
     * @param string $path     data file path
     * @param ?float $deadline microtime after which an incomplete table is left for the next call, null for none
     *
     * @return ?TableDumpResult null when the deadline interrupted the table
     */
    public function writeTable(string $table, string $path, ?float $deadline): ?TableDumpResult
    {
        if ($this->checkpoint->startTable($table)) {
            // Only the file of the table being written can be misaligned by a killed worker
            $this->truncateToCheckpoint($path);
            $handle = $this->openFile($path, 'a');
        } else {
            $handle = $this->openFile($path, 'w');
        }

        DupLog::trace(
            "DB DUMP TABLE {$table}: from row " . $this->checkpoint->getRows() .
            " offset " . SnapLog::v2str($this->checkpoint->getLastIndexOffset())
        );

        try {
            if (!$this->writer->writeTable($table, $handle, $deadline)) {
                // Persist the exact yield position: the batched row saves alone would
                // re-export the rows written since the last batch on every resume
                fflush($handle);
                $this->checkpoint->flush();
                return null;
            }
            $eofBytes = SnapIO::fwrite($handle, self::eofMarker());
        } finally {
            SnapIO::fclose($handle);
        }

        // The marker bytes never enter the checkpoint: a worker killed before the position
        // is cleared resumes by truncating the marker away and writing it again
        $result = new TableDumpResult($this->checkpoint->getBytes() + $eofBytes, $this->checkpoint->getChecksum(), $this->checkpoint->getRows());
        $this->checkpoint->finishTable();

        return $result;
    }

    /**
     * Write procedures, functions and views
     *
     * @param resource $handle open schema file handle
     *
     * @return void
     */
    private function writeRoutines($handle): void
    {
        $query      = $this->wpdb->prepare("SHOW PROCEDURE STATUS WHERE `Db` = %s", DB_NAME);
        $procedures = $this->wpdb->get_col($query, 1);
        foreach ($procedures as $procedure) {
            $create = $this->wpdb->get_row("SHOW CREATE PROCEDURE `{$procedure}`", ARRAY_N);
            SnapIO::fwrite($handle, "DELIMITER ;;\n{$create[2]} ;;\nDELIMITER ;\n\n");
        }

        $query     = $this->wpdb->prepare("SHOW FUNCTION STATUS WHERE `Db` = %s", DB_NAME);
        $functions = $this->wpdb->get_col($query, 1);
        foreach ($functions as $function) {
            $create = $this->wpdb->get_row("SHOW CREATE FUNCTION `{$function}`", ARRAY_N);
            SnapIO::fwrite($handle, "DELIMITER ;;\n{$create[2]} ;;\nDELIMITER ;\n\n");
        }

        $views = $this->wpdb->get_col("SHOW FULL TABLES WHERE Table_Type = 'VIEW'");
        foreach ($views as $view) {
            $create = $this->wpdb->get_row("SHOW CREATE VIEW `{$view}`", ARRAY_N);
            SnapIO::fwrite($handle, "{$create[1]};\n\n");
        }
    }

    /**
     * Truncate the data file of the current table to the bytes the checkpoint recorded
     *
     * @param string $path data file path
     *
     * @return void
     *
     * @throws DupliException if the file can't be truncated
     */
    private function truncateToCheckpoint(string $path): void
    {
        $size = $this->checkpoint->getBytes();
        clearstatcache();
        $currentSize = file_exists($path) ? filesize($path) : 0;
        if ($currentSize === $size) {
            return;
        }

        DupLog::infoTrace("[CHUNK RECOVERY] DB SQL dump misaligned, trying to realign " . basename($path) . " ({$currentSize} -> {$size} bytes)");
        $handle = $this->openFile($path, 'r+');
        $result = ftruncate($handle, $size);
        SnapIO::fclose($handle);

        if (!$result) {
            DupLog::infoTrace("[CHUNK RECOVERY] DB SQL FAILED, Could not truncate the sql file.");
            throw new DupliException(
                "FILE TRUNCATE ERROR: Could not truncate to file size " . $size,
                DupliException::CODE_DB_FILE_TRUNCATE_FAILED,
                __('Could not realign the database export file during chunk recovery.', 'duplicator')
            );
        }
        DupLog::infoTrace("[CHUNK RECOVERY] DB SQL dump realigned, resuming export");
    }

    /**
     * Fetches the SHOW CREATE TABLE row for a table, retrying once after a
     * connection check. When the query still fails it throws with a distinct
     * raw message per condition (existing table that cannot be read, vanished
     * core table, vanished table) so telemetry can separate them.
     *
     * @param string $table table name
     *
     * @return string[] SHOW CREATE TABLE numeric row
     *
     * @throws DupliException when the query fails after the retry
     */
    private function getCreateTableRow(string $table): array
    {
        $wpdb                = $this->wpdb;
        $connectionAvailable = true;
        for ($attempt = 0; $attempt < 2; $attempt++) {
            if ($attempt > 0) {
                DupLog::infoTrace("DB: SHOW CREATE TABLE failed for the table {$table}, retrying once. " . $wpdb->last_error);
                if (!$wpdb->check_connection(false)) {
                    $connectionAvailable = false;
                    break;
                }
            }
            if (($create = $wpdb->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N)) !== null) {
                return $create;
            }
        }

        DupLog::infoTrace("DB ERROR: Could not get the CREATE query for the table {$table}. " . $wpdb->last_error);
        $tableExists = $connectionAvailable
            ? $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table)))
            : null;
        if (!$connectionAvailable || $wpdb->last_error !== '' || $tableExists !== null) {
            throw new DupliException(
                'DB ERROR: Could not get the CREATE query for a table.',
                DupliException::CODE_DB_CREATE_QUERY_FAILED,
                sprintf(
                    __('Could not read the structure of the database table "%1$s" during export.', 'duplicator'),
                    $table
                )
            );
        }

        if (SnapWP::getTableInfoByName($table, $wpdb->prefix)['isCore']) {
            throw new DupliException(
                'DB ERROR: A WordPress core table was removed during the export.',
                DupliException::CODE_DB_CREATE_QUERY_FAILED,
                sprintf(
                    __(
                        'The WordPress core table "%1$s" was removed while the Backup was running,
                        so the Backup cannot continue.',
                        'duplicator'
                    ),
                    $table
                )
            );
        }

        throw new DupliException(
            'DB ERROR: A table was removed during the export.',
            DupliException::CODE_DB_CREATE_QUERY_FAILED,
            sprintf(
                __(
                    'The database table "%1$s" was removed by another process while the Backup was running.
                    If it is a temporary or cache table, exclude it from the Backup or create the Backup
                    when the site is less active.',
                    'duplicator'
                ),
                $table
            )
        );
    }
}
