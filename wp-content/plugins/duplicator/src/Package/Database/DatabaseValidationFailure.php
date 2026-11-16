<?php

declare(strict_types=1);

namespace Duplicator\Package\Database;

use Duplicator\Core\Exceptions\DupliException;

/**
 * Describes the structural checks that failed for a completed database export.
 */
final class DatabaseValidationFailure
{
    private const MAX_REPORTED_NAMES = 20;

    private bool $eofMarkerMissing;

    /** @var string[] */
    private array $mismatchedFiles;

    /**
     * @param bool     $eofMarkerMissing Whether a dump file is missing its end marker
     * @param string[] $mismatchedFiles  Dump file names missing or whose size differs from the tracked one
     */
    public function __construct(bool $eofMarkerMissing, array $mismatchedFiles)
    {
        $this->eofMarkerMissing = $eofMarkerMissing;
        $this->mismatchedFiles  = array_values($mismatchedFiles);
    }

    /**
     * Whether at least one structural check failed.
     *
     * @return bool
     */
    public function hasFailures(): bool
    {
        return $this->eofMarkerMissing || $this->mismatchedFiles !== [];
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
        if ($this->mismatchedFiles !== []) {
            $checks[]   = 'dump_file_size_mismatch=' . count($this->mismatchedFiles);
            $fileReport = $this->getBoundedReport($this->mismatchedFiles);
            $fileList   = implode(', ', $fileReport['names']);
            if ($fileReport['omitted'] > 0) {
                $fileList .= sprintf(', and %d more', $fileReport['omitted']);
            }
            $lines[] = 'Dump files with an unexpected size: ' . $fileList;
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
            $reasons[] = __('A database export file is missing its end marker.', 'duplicator');
        }

        if ($this->mismatchedFiles !== []) {
            $fileReport = $this->getBoundedReport($this->mismatchedFiles);
            $fileList   = implode(', ', array_map('wp_strip_all_tags', $fileReport['names']));
            if ($fileReport['omitted'] > 0) {
                $fileList = sprintf(
                    /* translators: 1: comma-separated file names, 2: number of additional files */
                    __('%1$s, and %2$d more', 'duplicator'),
                    $fileList,
                    $fileReport['omitted']
                );
            }
            $reasons[] = sprintf(
                /* translators: %s: comma-separated dump file names */
                __('The size of these database export files differs from the bytes written: %s.', 'duplicator'),
                $fileList
            );
        }

        return implode(' ', $reasons);
    }

    /**
     * Bound the names included in persisted diagnostics.
     *
     * @param string[] $names Names to report
     *
     * @return array{names:string[],omitted:int}
     */
    private function getBoundedReport(array $names): array
    {
        $reported = array_slice($names, 0, self::MAX_REPORTED_NAMES);

        return [
            'names'   => $reported,
            'omitted' => count($names) - count($reported),
        ];
    }
}
