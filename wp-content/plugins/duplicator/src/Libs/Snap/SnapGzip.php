<?php

/**
 *
 * @package   Duplicator
 * @copyright (c) 2026, Snap Creek LLC
 */

namespace Duplicator\Libs\Snap;

use Exception;

/**
 * Streaming gzip compress/decompress helpers with chunked/resumable support
 */
final class SnapGzip
{
    const DEFAULT_TIMEOUT           = 5;
    const DEFAULT_COMPRESSION_LEVEL = 6;
    const READ_BUFFER_SIZE          = 65536;
    const GZIP_MAGIC                = "\x1f\x8b";

    /**
     * Gzip-compress a segment of a file and (re)write it to the target
     *
     * Starts reading at $offset in $sourcePath and keeps processing until $timeout seconds of
     * wall-clock time have elapsed or EOF is reached — whichever comes first. At least one
     * read buffer is always processed so every call guarantees forward progress. When $offset
     * is 0 the target is truncated; otherwise the compressed output is appended as a new
     * gzip member, supporting chunked resumable compression across multiple invocations.
     *
     * Any I/O or gzip failure throws. Callers must treat a successful return as proof that
     * the previous chunk was fully written, so resuming with the returned source offset
     * always appends to a well-formed multi-member stream.
     *
     * @param string $sourcePath       Source file to compress
     * @param string $targetPath       Target .gz file to write
     * @param int    $offset           Byte offset in the source to resume from
     *                                 (0 for a fresh compress)
     * @param int    $timeout          Soft deadline in seconds; the loop stops after the
     *                                 timeout but only at a buffer boundary and never
     *                                 before processing at least one buffer. Pass a
     *                                 negative value to disable the timeout and run to
     *                                 completion.
     * @param int    $compressionLevel zlib level 1-9
     *
     * @return int New source offset — caller compares against filesize($sourcePath)
     *             to detect completion
     *
     * @throws Exception On I/O or gzip failure
     */
    public static function compressFile(
        string $sourcePath,
        string $targetPath,
        int $offset = 0,
        int $timeout = self::DEFAULT_TIMEOUT,
        int $compressionLevel = self::DEFAULT_COMPRESSION_LEVEL
    ): int {
        self::assertNonNegative($offset, 'offset');

        if (!is_file($sourcePath)) {
            throw new Exception("Source file does not exist: {$sourcePath}");
        }

        $sourceSize = filesize($sourcePath);
        if ($sourceSize === false) {
            throw new Exception("Can't stat source file: {$sourcePath}");
        }
        if ($offset > $sourceSize) {
            throw new Exception("Offset {$offset} exceeds source size {$sourceSize} for {$sourcePath}");
        }
        if ($offset === $sourceSize) {
            return $offset;
        }

        $source = @fopen($sourcePath, 'rb');
        if ($source === false) {
            throw new Exception("Can't open source for compression: {$sourcePath}");
        }

        if ($offset > 0 && fseek($source, $offset) !== 0) {
            fclose($source);
            throw new Exception("Can't seek source to offset {$offset}: {$sourcePath}");
        }

        $level  = self::clampLevel($compressionLevel);
        $mode   = ($offset === 0 ? 'wb' : 'ab') . $level;
        $target = @gzopen($targetPath, $mode);
        if ($target === false) {
            fclose($source);
            throw new Exception("Can't open gzip target: {$targetPath}");
        }

        $deadline  = $timeout < 0 ? INF : microtime(true) + $timeout;
        $processed = 0;
        try {
            while (!feof($source) && ($processed === 0 || microtime(true) < $deadline)) {
                $data = fread($source, self::READ_BUFFER_SIZE);
                if ($data === false) {
                    throw new Exception("Read error while compressing {$sourcePath} at offset " . ($offset + $processed));
                }
                if ($data === '') {
                    break;
                }
                // A failed write (for example a full disk) returns fewer bytes, not false
                if (gzwrite($target, $data) !== strlen($data)) {
                    throw new Exception("Write error while compressing to {$targetPath} at offset " . ($offset + $processed));
                }
                $processed += strlen($data);
            }
            // gzclose() reports success even when writing the buffered data fails
            if (!fflush($target)) {
                throw new Exception("Write error while compressing to {$targetPath} at offset " . ($offset + $processed));
            }
        } finally {
            fclose($source);
            gzclose($target);
        }

        return $offset + $processed;
    }

    /**
     * Decompress a gzipped file to a target path in a single pass
     *
     * Streams from gzopen() to fopen() until gzeof. Bounded only by PHP's max_execution_time;
     * the caller is expected to invoke this with enough runtime budget to finish the file.
     *
     * Intentionally not resumable across calls. zlib's gzseek() to a forward offset has to
     * re-decode every preceding byte (the gzip stream is not random-access), so chunked
     * resume would degrade to O(n^2) over the file size — strictly worse than running to
     * completion. If a request times out mid-decompression the caller should retry from
     * scratch rather than try to pick up where it left off.
     *
     * @param string $sourcePath Source .gz file
     * @param string $targetPath Target file to write decompressed bytes to (truncated on open)
     *
     * @return int Total decompressed size in bytes
     *
     * @throws Exception On I/O or gzip failure
     */
    public static function decompressFile(string $sourcePath, string $targetPath): int
    {
        if (!is_file($sourcePath)) {
            throw new Exception("Source file does not exist: {$sourcePath}");
        }

        $source = @gzopen($sourcePath, 'rb');
        if ($source === false) {
            throw new Exception("Can't open compressed source: {$sourcePath}");
        }

        $target = @fopen($targetPath, 'wb');
        if ($target === false) {
            gzclose($source);
            throw new Exception("Can't open decompressed target: {$targetPath}");
        }

        $written = 0;
        try {
            while (!gzeof($source)) {
                $data = gzread($source, self::READ_BUFFER_SIZE);
                if ($data === false) {
                    throw new Exception("Read error while decompressing {$sourcePath} at decoded offset {$written}");
                }
                if ($data === '') {
                    break;
                }
                if (fwrite($target, $data) === false) {
                    throw new Exception("Write error while decompressing to {$targetPath} at decoded offset {$written}");
                }
                $written += strlen($data);
            }
        } finally {
            gzclose($source);
            fclose($target);
        }

        return $written;
    }

    /**
     * Check whether a file starts with the gzip magic bytes (1f 8b)
     *
     * @param string $path File to inspect
     *
     * @return bool
     */
    public static function isGzipped(string $path): bool
    {
        if (!is_file($path) || filesize($path) < 2) {
            return false;
        }
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            return false;
        }
        $magic = fread($handle, 2);
        fclose($handle);
        return $magic === self::GZIP_MAGIC;
    }

    /**
     * @param int    $value Value to check
     * @param string $name  Name of the argument for the error message
     *
     * @return void
     *
     * @throws Exception
     */
    private static function assertNonNegative(int $value, string $name): void
    {
        if ($value < 0) {
            throw new Exception("{$name} must be non-negative, got {$value}");
        }
    }

    /**
     * @param int $level Requested zlib compression level
     *
     * @return int Clamped to 1-9
     */
    private static function clampLevel(int $level): int
    {
        if ($level < 1) {
            return 1;
        }
        if ($level > 9) {
            return 9;
        }
        return $level;
    }
}
