<?php

declare(strict_types=1);

namespace Duplicator\Utils;

use Duplicator\Libs\Shell\Shell;
use Duplicator\Utils\Logging\DupLog;
use ZipArchive;

/**
 * Read-only verification of zip archives: entry count, entries presence and
 * structural corruption checks.
 *
 * Layered probes: PHP zip extension first (reliable result and corruption
 * verdict), shell tools (zipinfo/unzip) as fallback, end-of-central-directory
 * record as the last structural resort. Returns pure verdicts — mapping to
 * build failures or warnings belongs to the callers.
 */
class ZipVerifier
{
    /* Probe succeeded: count resolved / all entries present */
    const VERDICT_OK = 1;
    /* Probes could not inspect the archive; no corruption evidence */
    const VERDICT_UNKNOWN = 2;
    /* Entries probe worked and one or more entries are absent */
    const VERDICT_MISSING = 3;
    /* Definitive corruption evidence (invalid/inconsistent/truncated archive) */
    const VERDICT_CORRUPT = 4;

    /**
     * Resolve the archive entry count.
     *
     * @param string $zipPath Path of the zip file
     *
     * @return array{verdict: int, count: ?int, detail: string} VERDICT_OK with the
     *                                                           count, VERDICT_UNKNOWN or VERDICT_CORRUPT with count null
     */
    public static function verifyEntryCount(string $zipPath): array
    {
        if (ZipArchiveExtended::isPhpZipAvailable()) {
            $zip    = new ZipArchive();
            $result = $zip->open($zipPath);
            if ($result === true) {
                $count = $zip->numFiles;
                $zip->close();
                return [
                    'verdict' => self::VERDICT_OK,
                    'count'   => $count,
                    'detail'  => 'php zip extension',
                ];
            }
            if (self::isCorruptionOpenError($result)) {
                return [
                    'verdict' => self::VERDICT_CORRUPT,
                    'count'   => null,
                    'detail'  => "ZipArchive open error code {$result}",
                ];
            }
            DupLog::infoTrace("ZipVerifier: ZipArchive count probe inconclusive (open error {$result}), falling back to shell tools");
        }

        if (($count = self::shellEntryCount($zipPath)) !== null) {
            return [
                'verdict' => self::VERDICT_OK,
                'count'   => $count,
                'detail'  => 'shell tools',
            ];
        }

        if (self::hasEndOfCentralDirectory($zipPath)) {
            return [
                'verdict' => self::VERDICT_UNKNOWN,
                'count'   => null,
                'detail'  => 'archive finalized, count not determinable',
            ];
        }

        return [
            'verdict' => self::VERDICT_CORRUPT,
            'count'   => null,
            'detail'  => 'end of central directory record not found',
        ];
    }

    /**
     * Verify that the given entry paths exist in the archive.
     *
     * When no probe can inspect the archive — or the shell listing returns
     * nothing at all, which is indistinguishable from a broken probe — the
     * verdict is VERDICT_UNKNOWN rather than VERDICT_MISSING.
     *
     * @param string   $zipPath Path of the zip file
     * @param string[] $paths   Exact entry paths expected in the archive
     *
     * @return array{verdict: int, missing: string[], detail: string}
     */
    public static function verifyEntriesPresence(string $zipPath, array $paths): array
    {
        if (count($paths) === 0) {
            return [
                'verdict' => self::VERDICT_OK,
                'missing' => [],
                'detail'  => 'nothing to check',
            ];
        }

        if (ZipArchiveExtended::isPhpZipAvailable()) {
            $zip    = new ZipArchive();
            $result = $zip->open($zipPath, ZipArchive::CHECKCONS);
            if ($result === true) {
                $missing = [];
                foreach ($paths as $path) {
                    if ($zip->locateName($path) === false) {
                        $missing[] = $path;
                    }
                }
                $zip->close();
                return [
                    'verdict' => count($missing) === 0 ? self::VERDICT_OK : self::VERDICT_MISSING,
                    'missing' => $missing,
                    'detail'  => 'php zip extension',
                ];
            }
            if (self::isCorruptionOpenError($result)) {
                return [
                    'verdict' => self::VERDICT_CORRUPT,
                    'missing' => [],
                    'detail'  => "ZipArchive open error code {$result}",
                ];
            }
            DupLog::infoTrace("ZipVerifier: ZipArchive presence probe inconclusive (open error {$result}), falling back to shell tools");
        }

        if (Shell::getExeFilepath('unzip') === null) {
            return [
                'verdict' => self::VERDICT_UNKNOWN,
                'missing' => [],
                'detail'  => 'no probe available',
            ];
        }

        // List only the expected entries: no grep/wc pipeline (a pipe hides the
        // real exit code and unescaped paths would break the grep pattern) and
        // no full-archive listing in memory.
        $command = 'unzip -Z1 ' . escapeshellarg($zipPath);
        foreach ($paths as $path) {
            $command .= ' ' . escapeshellarg($path);
        }

        $output = Shell::runCommandBuffered($command)->getOutputAsString();
        $listed = array_intersect($paths, array_map('trim', explode("\n", $output)));

        if (count($listed) === 0) {
            return [
                'verdict' => self::VERDICT_UNKNOWN,
                'missing' => [],
                'detail'  => 'shell listing gave no usable output',
            ];
        }

        $missing = array_values(array_diff($paths, $listed));
        return [
            'verdict' => count($missing) === 0 ? self::VERDICT_OK : self::VERDICT_MISSING,
            'missing' => $missing,
            'detail'  => 'shell tools',
        ];
    }

    /**
     * True when the ZipArchive open error code is definitive corruption
     * evidence, as opposed to an environment problem (permissions, memory).
     *
     * @param int|bool $openResult Result of ZipArchive::open() when not true
     *
     * @return bool
     */
    protected static function isCorruptionOpenError($openResult): bool
    {
        return in_array($openResult, [ZipArchive::ER_NOZIP, ZipArchive::ER_INCONS, ZipArchive::ER_CRC], true);
    }

    /**
     * Get the archive entry count from the available shell tools.
     *
     * @param string $zipPath Path of the zip file
     *
     * @return ?int Entry count, null if no tool produced a usable count
     */
    protected static function shellEntryCount(string $zipPath): ?int
    {
        $probes = [];
        if (Shell::getExeFilepath('zipinfo') !== null) {
            $probes['zipinfo'] = 'zipinfo -t ' . escapeshellarg($zipPath);
        }
        if (Shell::getExeFilepath('unzip') !== null) {
            $probes['unzip'] = 'unzip -l ' . escapeshellarg($zipPath);
        }

        foreach ($probes as $tool => $command) {
            $shellOutput = Shell::runCommandBuffered($command);
            $count       = self::parseFileCountFromToolOutput($shellOutput->getOutputAsString());
            if ($count !== null) {
                DupLog::trace("ZipVerifier: entry count resolved by {$tool}: {$count}");
                return $count;
            }
            DupLog::infoTrace("ZipVerifier: count probe [{$tool}] gave no usable count, exit code {$shellOutput->getCode()}");
        }

        return null;
    }

    /**
     * Parse the entry count from zipinfo/unzip output.
     *
     * Both tools summarize the archive with a "<count> files" token; the last
     * occurrence is used so unzip's per-file listing can't shadow the summary.
     * Digit group separators are tolerated.
     *
     * @param string $output Raw tool output
     *
     * @return ?int Parsed count, null when no usable count is present
     */
    public static function parseFileCountFromToolOutput(string $output): ?int
    {
        if (!preg_match_all('/\b(\d[\d.,\']*)\s+files?\b/i', $output, $matches)) {
            return null;
        }

        $raw = (string) end($matches[1]);
        return (int) str_replace([',', '.', "'"], '', $raw);
    }

    /**
     * Check whether the file ends with a zip end-of-central-directory record.
     *
     * A zip archive is finalized by writing the central directory and its end
     * record at the tail; a missing record means the file was truncated or is
     * not a zip archive. The record can be followed only by the archive
     * comment, so it must appear within the last comment-length + record-size
     * bytes of the file.
     *
     * @param string $zipPath Path of the zip file
     *
     * @return bool
     */
    public static function hasEndOfCentralDirectory(string $zipPath): bool
    {
        $eocdMinSize    = 22;
        $maxCommentSize = 65535;

        $fileSize = @filesize($zipPath);
        if ($fileSize === false || $fileSize < $eocdMinSize) {
            return false;
        }

        if (($handle = @fopen($zipPath, 'rb')) === false) {
            return false;
        }

        try {
            $readLen = (int) min($fileSize, $eocdMinSize + $maxCommentSize);
            if (fseek($handle, -$readLen, SEEK_END) !== 0) {
                return false;
            }
            $tail = fread($handle, $readLen);
        } finally {
            fclose($handle);
        }

        return is_string($tail) && strpos($tail, "PK\x05\x06") !== false;
    }
}
