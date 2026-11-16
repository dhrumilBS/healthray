<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapIO;

/**
 * A database dump engine writes the schema file and one data file per table.
 *
 * The PHP build loop delegates schema and table writes to the engine, which
 * owns row-level recovery. The base provides the dump file handling.
 */
abstract class AbstractDumpEngine
{
    /**
     * Marks the end of the CREATEs in the schema file, which the installer
     * runs together in one chunk
     */
    const TABLE_CREATION_END_MARKER = "/***** TABLE CREATION END *****/\n";

    /**
     * Open a dump file, creating the dump folder when needed
     *
     * @param string $path dump file path
     * @param string $mode fopen mode
     *
     * @return resource
     *
     * @throws DupliException if the file can't be opened
     */
    protected function openFile(string $path, string $mode)
    {
        if (SnapIO::dirWriteCheckOrMkdir(dirname($path), 'u+rwx', true) === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not create the database export folder.\nPath: " . dirname($path),
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not create the database export folder.', 'duplicator')
            );
        }

        $handle = SnapIO::callWithPhpErrorCapture(fn () => fopen($path, $mode));
        if ($handle === false) {
            throw DupliException::fromLastError(
                "FILE OPEN ERROR: Could not open the database export file.\nPath: {$path}",
                DupliException::CODE_DB_FILE_OPEN_FAILED,
                __('Could not open the database export file.', 'duplicator')
            );
        }

        return $handle;
    }

    /**
     * The end of file marker that closes every dump file
     *
     * @return string
     */
    protected static function eofMarker(): string
    {
        return "/* " . DUPLICATOR_DB_EOF_MARKER . " */\n";
    }

    /**
     * Write the schema file: session header, the CREATE of every table, the
     * routines when requested, the table creation end marker and the end of
     * file marker.
     *
     * @param string   $path            schema file path
     * @param string[] $tables          tables whose CREATE the schema holds, in order
     * @param bool     $includeRoutines whether procedures, functions and views are written
     *
     * @return int bytes written
     */
    abstract public function writeSchema(string $path, array $tables, bool $includeRoutines): int;

    /**
     * Write the data file of a table, closed by the end of file marker.
     *
     * A deadline may stop the engine before the table is complete: the file is
     * left as it is, null is returned, and the next call for the same table
     * resumes where the engine stopped.
     *
     * @param string $table    table name
     * @param string $path     data file path
     * @param ?float $deadline microtime after which an incomplete table is left for the next call, null for none
     *
     * @return ?TableDumpResult null when the deadline interrupted the table
     */
    abstract public function writeTable(string $table, string $path, ?float $deadline): ?TableDumpResult;
}
