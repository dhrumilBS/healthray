<?php

declare(strict_types=1);

namespace Duplicator\Libs\Snap;

use Exception;
use Throwable;

/**
 * Library-level exception with dedicated codes for conditions that callers
 * must recognize without parsing messages.
 *
 * Generic call-site codes and dedicated Snap condition codes use separate
 * code spaces.
 */
class SnapException extends Exception
{
    use TraitFactoryExceptionOrigin;

    /**
     * Write failed because disk space or quota is exhausted
     */
    const CODE_DISK_FULL = 1000;

    /**
     * Write failed because the file reached the maximum size allowed by the system
     */
    const CODE_FILE_TOO_LARGE = 1001;

    /**
     * Builds the exception for a failed I/O operation, enriched with the
     * reason of the last PHP error. Call it immediately after the failed
     * operation so the reported reason belongs to it; the captured error is
     * cleared so it cannot leak into a later failure.
     *
     * The reason is appended to the first message line (the telemetry
     * fingerprint), while any detail lines after a newline are preserved
     * as-is. When the reason indicates disk-full or quota exhaustion the
     * exception code becomes CODE_DISK_FULL (replacing the passed code), and
     * CODE_FILE_TOO_LARGE when it reports the maximum file size, so callers and
     * the failure boundary can recognize the condition without parsing messages.
     *
     * @param string $message Base error message
     * @param int    $code    Optional exception code
     *
     * @return self
     */
    public static function fromLastError(string $message, int $code = 0): self
    {
        $error  = error_get_last();
        $reason = $error === null ? '' : $error['message'];
        error_clear_last();

        if ($reason !== '') {
            $eolPos = strcspn($message, "\r\n");
            if ($eolPos < strlen($message)) {
                $message = substr($message, 0, $eolPos) . ' Reason: ' . $reason . substr($message, $eolPos);
            } else {
                $message .= ' Reason: ' . $reason;
            }
        }

        if (SnapIO::isDiskFullError($reason)) {
            $code = self::CODE_DISK_FULL;
        } elseif (stripos($reason, 'File too large') !== false || preg_match('/errno=27\b/', $reason) === 1) {
            // EFBIG: the errno is matched too because the description can be localized
            $code = self::CODE_FILE_TOO_LARGE;
        }

        $exception = new self($message, $code);
        $exception->useFactoryCallerOrigin(__FILE__);

        return $exception;
    }

    /**
     * Returns the condition code marked by the first SnapException in the chain,
     * or null when no exception in the chain marks a condition. Wrapping catches
     * must chain the original exception for this to see through them.
     *
     * @param Throwable $exception The exception chain to inspect
     *
     * @return int|null
     */
    public static function conditionInChain(Throwable $exception): ?int
    {
        for ($current = $exception; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof self && in_array($current->getCode(), [self::CODE_DISK_FULL, self::CODE_FILE_TOO_LARGE], true)) {
                return $current->getCode();
            }
        }

        return null;
    }
}
