<?php

declare(strict_types=1);

namespace Duplicator\Package\Database;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Libs\Snap\SnapString;

/**
 * Describes the structural checks that failed for a completed database export.
 */
final class DatabaseValidationFailure
{
    private const MAX_REPORTED_TABLES = 20;

    private bool $eofMarkerMissing;

    /** @var string[] */
    private array $missingCreateTables;

    private ?int $expectedSize;
    private ?int $actualSize;

    /**
     * @param bool     $eofMarkerMissing    Whether the SQL end marker is missing
     * @param string[] $missingCreateTables Tables without a CREATE statement
     * @param int|null $expectedSize        Byte count tracked during export
     * @param int|null $actualSize          SQL file size, null when unavailable
     */
    public function __construct(
        bool $eofMarkerMissing,
        array $missingCreateTables,
        ?int $expectedSize,
        ?int $actualSize
    ) {
        $this->eofMarkerMissing    = $eofMarkerMissing;
        $this->missingCreateTables = array_values($missingCreateTables);
        $this->expectedSize        = $expectedSize;
        $this->actualSize          = $actualSize;
    }

    /**
     * Whether at least one structural check failed.
     *
     * @return bool
     */
    public function hasFailures(): bool
    {
        return $this->eofMarkerMissing ||
            $this->missingCreateTables !== [] ||
            $this->expectedSize === null ||
            $this->actualSize === null ||
            $this->expectedSize !== $this->actualSize;
    }

    /**
     * Build the domain exception carrying stable telemetry text and user details.
     *
     * @return DupliException
     */
    public function toException(): DupliException
    {
        return new DupliException(
            $this->getLogMessage(),
            DupliException::CODE_DB_VALIDATION_FAILED,
            $this->getUserMessage()
        );
    }

    /**
     * Raw English diagnostics for logs and telemetry.
     *
     * @return string
     */
    public function getLogMessage(): string
    {
        $lines  = ['Database export validation failed.'];
        $checks = [];

        if ($this->eofMarkerMissing) {
            $checks[] = 'eof_marker_missing';
        }
        if ($this->missingCreateTables !== []) {
            $checks[]    = 'create_query_missing=' . count($this->missingCreateTables);
            $tableReport = $this->getMissingCreateTablesReport();
            $tableList   = implode(', ', $tableReport['tables']);
            if ($tableReport['omitted'] > 0) {
                $tableList .= sprintf(', and %d more', $tableReport['omitted']);
            }
            $lines[] = 'Tables without CREATE output: ' . $tableList;
        }
        if ($this->expectedSize === null || $this->actualSize === null || $this->expectedSize !== $this->actualSize) {
            $checks[] = 'file_size_mismatch';
            $lines[]  = sprintf(
                'Tracked bytes: %s; SQL file bytes: %s; delta: %s.',
                $this->expectedSize === null ? 'unavailable' : (string) $this->expectedSize,
                $this->actualSize === null ? 'unavailable' : (string) $this->actualSize,
                $this->getSignedDelta(false)
            );
        }

        array_splice($lines, 1, 0, ['Failed checks: ' . implode(', ', $checks)]);
        return implode("\n", $lines);
    }

    /**
     * Localized failure reason for the administrator.
     *
     * @return string
     */
    public function getUserMessage(): string
    {
        $reasons = [];

        if ($this->eofMarkerMissing) {
            $reasons[] = __('The SQL file is missing its end marker.', 'duplicator');
        }

        if ($this->missingCreateTables !== []) {
            $tableReport = $this->getMissingCreateTablesReport();
            $tableList   = implode(', ', array_map('wp_strip_all_tags', $tableReport['tables']));
            if ($tableReport['omitted'] > 0) {
                $tableList = sprintf(
                    /* translators: 1: comma-separated database table names, 2: number of additional tables */
                    __('%1$s, and %2$d more', 'duplicator'),
                    $tableList,
                    $tableReport['omitted']
                );
            }
            $reasons[] = sprintf(
                /* translators: %s: comma-separated database table names */
                __('The export is missing CREATE TABLE output for: %s.', 'duplicator'),
                $tableList
            );
        }

        if ($this->expectedSize === null || $this->actualSize === null) {
            $reasons[] = __('The SQL file size could not be compared with the byte count tracked during export.', 'duplicator');
        } elseif ($this->expectedSize !== $this->actualSize) {
            $reasons[] = sprintf(
                /* translators: 1: expected size, 2: actual size, 3: signed size difference */
                __('The SQL file size differs from the byte count tracked during export: expected %1$s, actual %2$s, delta %3$s.', 'duplicator'),
                SnapString::byteSize($this->expectedSize),
                SnapString::byteSize($this->actualSize),
                $this->getSignedDelta(true)
            );
        }

        return implode(' ', $reasons);
    }

    /**
     * Bound the table names included in persisted diagnostics.
     *
     * @return array{tables:string[],omitted:int}
     */
    private function getMissingCreateTablesReport(): array
    {
        $tables = array_slice($this->missingCreateTables, 0, self::MAX_REPORTED_TABLES);

        return [
            'tables'  => $tables,
            'omitted' => count($this->missingCreateTables) - count($tables),
        ];
    }

    /**
     * Signed difference between the SQL file and tracked byte count.
     *
     * @param bool $humanReadable Whether to format the absolute value as a byte size
     *
     * @return string
     */
    private function getSignedDelta(bool $humanReadable): string
    {
        if ($this->expectedSize === null || $this->actualSize === null) {
            return 'unavailable';
        }

        $delta = $this->actualSize - $this->expectedSize;
        $value = $humanReadable ? SnapString::byteSize(abs($delta)) : (string) abs($delta);
        return ($delta >= 0 ? '+' : '-') . $value;
    }
}
