<?php

declare(strict_types=1);

namespace Duplicator\Package\Database\Dump;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapIO;

/**
 * Names and paths of the files of a database dump.
 *
 * A dump is made of one schema file plus one data file per table, in the order
 * the tables are given. Every data file name carries the checksum of the raw
 * table name, so a table always keeps the same name and adding or removing a
 * table never renames another table's file. The execution order travels in the
 * archive descriptor, not in the names.
 */
class DumpFileMap
{
    /**
     * Name of the file holding the header, the CREATEs and the routines
     */
    const SCHEMA_FILE_NAME = 'schema.sql';
    /**
     * Extension appended to a dump file once it is gzipped
     */
    const COMPRESSED_EXTENSION = '.gz';

    private string $localDir;
    private string $archiveDir;
    /** @var array<string,string> data file name by table name, in dump order */
    private array $dataFiles = [];

    /**
     * Class constructor
     *
     * @param string   $localDir   dir where the dump files are built
     * @param string   $archiveDir dir of the dump files inside the archive
     * @param string[] $tables     tables of the dump, in execution order
     *
     * @throws DupliException if two tables resolve to the same data file name
     */
    public function __construct(string $localDir, string $archiveDir, array $tables)
    {
        $this->localDir   = SnapIO::untrailingslashit(SnapIO::safePath($localDir));
        $this->archiveDir = SnapIO::untrailingslashit($archiveDir);

        $usedNames = [];
        foreach ($tables as $table) {
            $name = self::dataFileName($table);
            if (isset($usedNames[$name])) {
                // Defensive: the digest makes a collision practically impossible, but a
                // silent one would overwrite a table's data file with another table's
                throw new DupliException(
                    'Two tables of the dump resolve to the same data file name.' .
                        "\nTable: {$table}\nFile: {$name}",
                    DupliException::CODE_DB_VALIDATION_FAILED,
                    __('The database export could not assign a distinct file to every table.', 'duplicator')
                );
            }
            $usedNames[$name]        = true;
            $this->dataFiles[$table] = $name;
        }
    }

    /**
     * Dir where the dump files are built, without a trailing separator
     *
     * @return string
     */
    public function getLocalDir(): string
    {
        return $this->localDir;
    }

    /**
     * Dir of the dump files inside the archive, without a trailing separator
     *
     * @return string
     */
    public function getArchiveDir(): string
    {
        return $this->archiveDir;
    }

    /**
     * Path of the schema file
     *
     * @return string
     */
    public function getSchemaFile(): string
    {
        return $this->localDir . '/' . self::SCHEMA_FILE_NAME;
    }

    /**
     * Path of the data file of the given table
     *
     * @param string $table table name
     *
     * @return string
     *
     * @throws DupliException if the table isn't part of the dump
     */
    public function getTableFile(string $table): string
    {
        if (!isset($this->dataFiles[$table])) {
            throw new DupliException(
                'The table is not part of the database dump file map.' . "\nTable: {$table}",
                DupliException::CODE_ERROR,
                __('The database export could not resolve the file of one of its tables.', 'duplicator')
            );
        }

        return $this->localDir . '/' . $this->dataFiles[$table];
    }

    /**
     * Archive relative path of the gzipped data file of a table, dumped by this Backup or not
     *
     * The file name depends on the table alone, so the path is known for a table
     * whose data file another Backup holds.
     *
     * @param string $table table name
     *
     * @return string
     */
    public function getTableArchivePath(string $table): string
    {
        return $this->archiveDir . '/' . self::dataFileName($table) . self::COMPRESSED_EXTENSION;
    }

    /**
     * Names of every dump file, schema first and data files in execution order
     *
     * @return string[]
     */
    public function getFileNames(): array
    {
        return array_merge([self::SCHEMA_FILE_NAME], array_values($this->dataFiles));
    }

    /**
     * Paths of every dump file, in the order of getFileNames()
     *
     * @return string[]
     */
    public function getFiles(): array
    {
        return array_map(fn(string $name): string => $this->localDir . '/' . $name, $this->getFileNames());
    }

    /**
     * Paths of the gzipped dump files, in the order of getFileNames()
     *
     * @return string[]
     */
    public function getCompressedFiles(): array
    {
        return array_map(fn(string $file): string => $file . self::COMPRESSED_EXTENSION, $this->getFiles());
    }

    /**
     * Archive relative paths of the gzipped dump files, in the order of getFileNames()
     *
     * @return string[]
     */
    public function getCompressedArchivePaths(): array
    {
        return array_map(fn(string $name): string => $this->archiveDir . '/' . $name . self::COMPRESSED_EXTENSION, $this->getFileNames());
    }

    /**
     * Name of the data file of a table
     *
     * The digest disambiguates tables whose sanitized names coincide; its
     * truncation is long enough that a collision between two table names of the
     * same database is practically impossible.
     *
     * @param string $table table name
     *
     * @return string
     */
    private static function dataFileName(string $table): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '_', $table) . '-h' . substr(hash('sha256', $table), 0, 16) . '.sql';
    }
}
