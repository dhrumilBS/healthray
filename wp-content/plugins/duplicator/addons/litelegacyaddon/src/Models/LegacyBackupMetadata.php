<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteLegacyAddon\Models;

use Duplicator\Installer\Package\PComponents;

/**
 * Normalized metadata read from a legacy archive descriptor.
 */
final class LegacyBackupMetadata
{
    public string $notes      = '';
    public string $created    = '';
    public string $versionWp  = '';
    public string $versionDb  = '';
    public string $versionPhp = '';
    public string $versionOs  = '';
    public bool $databaseOnly = false;
    /** @var string[] */
    public array $components     = [];
    public int $directoryCount   = 0;
    public int $fileCount        = 0;
    public int $uncompressedSize = 0;
    /** @var array<string,scalar|array<mixed>> */
    public array $database = [];
    /** @var array<string,array{inaccurateRows:int,insertedRows:int,size:int}> */
    public array $tables = [];

    /**
     * Normalize the overlapping descriptor fields used by current package records.
     *
     * @param object $descriptor Raw decoded descriptor
     *
     * @return self
     */
    public static function fromDescriptor(object $descriptor): self
    {
        $metadata               = new self();
        $metadata->notes        = self::stringValue($descriptor, 'package_notes');
        $metadata->created      = self::stringValue($descriptor, 'created');
        $metadata->versionWp    = self::stringValue($descriptor, 'version_wp');
        $metadata->versionDb    = self::stringValue($descriptor, 'version_db');
        $metadata->versionPhp   = self::stringValue($descriptor, 'version_php');
        $metadata->versionOs    = self::stringValue($descriptor, 'version_os');
        $metadata->databaseOnly = isset($descriptor->exportOnlyDB) && (bool) $descriptor->exportOnlyDB;
        $metadata->components   = self::normalizeComponents($descriptor->components ?? []);

        if (isset($descriptor->fileInfo) && is_object($descriptor->fileInfo)) {
            $metadata->directoryCount   = self::nonNegativeInt($descriptor->fileInfo->dirCount ?? 0);
            $metadata->fileCount        = self::nonNegativeInt($descriptor->fileInfo->fileCount ?? 0);
            $metadata->uncompressedSize = self::nonNegativeInt($descriptor->fileInfo->size ?? 0);
        }

        if (isset($descriptor->dbInfo) && (is_object($descriptor->dbInfo) || is_array($descriptor->dbInfo))) {
            $metadata->database = self::normalizeDatabase((array) $descriptor->dbInfo);
            $metadata->tables   = self::normalizeTables($metadata->database['tablesList'] ?? []);
            unset($metadata->database['tablesList']);
        }

        return $metadata;
    }

    /**
     * @param object $source Descriptor object
     * @param string $key    Property name
     *
     * @return string
     */
    private static function stringValue(object $source, string $key): string
    {
        return isset($source->{$key}) && is_scalar($source->{$key}) ? (string) $source->{$key} : '';
    }

    /**
     * @param mixed $value Value to normalize
     *
     * @return int
     */
    private static function nonNegativeInt($value): int
    {
        return is_numeric($value) ? max(0, (int) $value) : 0;
    }

    /**
     * @param mixed $components Descriptor components
     *
     * @return string[]
     */
    private static function normalizeComponents($components): array
    {
        if (!is_array($components)) {
            return [];
        }

        return array_values(array_unique(array_intersect(array_map('strval', $components), PComponents::COMPONENTS)));
    }

    /**
     * @param array<string,mixed> $source Legacy database metadata
     *
     * @return array<string,scalar|array<mixed>>
     */
    private static function normalizeDatabase(array $source): array
    {
        $result = [];
        foreach (
            [
                'buildMode',
                'charSetList',
                'collationList',
                'engineList',
                'isTablesUpperCase',
                'lowerCaseTableNames',
                'isNameUpperCase',
                'name',
                'tablesBaseCount',
                'tablesFinalCount',
                'muFilteredTableCount',
                'tablesRowCount',
                'tablesSizeOnDisk',
                'tablesList',
                'dbEngine',
                'version',
                'versionComment',
                'viewCount',
                'procCount',
                'funcCount',
                'triggerList',
            ] as $key
        ) {
            if (array_key_exists($key, $source) && (is_scalar($source[$key]) || is_array($source[$key]) || is_object($source[$key]))) {
                $result[$key] = is_object($source[$key]) ? (array) $source[$key] : $source[$key];
            }
        }

        return $result;
    }

    /**
     * @param mixed $tables Legacy table list
     *
     * @return array<string,array{inaccurateRows:int,insertedRows:int,size:int}>
     */
    private static function normalizeTables($tables): array
    {
        if (!is_array($tables)) {
            return [];
        }

        $result = [];
        foreach ($tables as $name => $table) {
            if (!is_string($name) || (!is_array($table) && !is_object($table))) {
                continue;
            }
            $tableData     = (array) $table;
            $result[$name] = [
                'inaccurateRows' => self::nonNegativeInt($tableData['inaccurateRows'] ?? 0),
                'insertedRows'   => self::nonNegativeInt($tableData['insertedRows'] ?? 0),
                'size'           => self::nonNegativeInt($tableData['size'] ?? 0),
            ];
        }

        return $result;
    }
}
