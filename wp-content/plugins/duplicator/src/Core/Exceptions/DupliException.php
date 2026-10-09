<?php

declare(strict_types=1);

namespace Duplicator\Core\Exceptions;

use Duplicator\Libs\Snap\TraitFactoryExceptionOrigin;
use Exception;
use Throwable;

/**
 * Single domain exception for Duplicator. Core failure types are identified by
 * the error code (CODE_*), not by subclassing; recognized expected conditions
 * are classified as handled severity through the core code map, and addons
 * extend this class only to attach their own domain classification (severity)
 * to a failure. `message` stays raw (log + telemetry hash); `userMessage` is
 * the optional translated/formatted text for display.
 */
class DupliException extends Exception
{
    use TraitFactoryExceptionOrigin;

    // Unhandled/unexpected failure (potential plugin bug); default severity.
    const SEVERITY_ERROR = 'error';
    // Expected, recognized condition; not a plugin fault.
    const SEVERITY_HANDLED = 'handled';

    const CODE_ERROR               = 0;
    const CODE_MAX_BUILD_TIME      = 1;
    const CODE_STUCK               = 2;
    const CODE_LOCK_ACQUIRE_FAILED = 3;

    const CODE_SCAN_READ_FAILED       = 100;
    const CODE_SCAN_INVALID_REPORT    = 101;
    const CODE_SCAN_FAILED            = 102;
    const CODE_SCAN_INDEX_INVALID     = 103;
    const CODE_SCAN_WRITE_FAILED      = 104;
    const CODE_INDEX_FILE_MISSING     = 105;
    const CODE_INDEX_FILE_EMPTY       = 106;
    const CODE_SCAN_SOURCE_UNREADABLE = 107;

    const CODE_ZIP_NOT_AVAILABLE           = 109;
    const CODE_ZIP_OPEN_FAILED             = 110;
    const CODE_ZIP_CLOSE_FAILED            = 111;
    const CODE_ZIP_FILE_OVERFLOW           = 112;
    const CODE_ZIP_RETRY_EXHAUSTED         = 113;
    const CODE_ZIP_PATH_NOT_WRITABLE       = 114;
    const CODE_ARCHIVE_TARGET_ROOT_INVALID = 115;

    const CODE_SHELL_ZIP_FAILED = 120;
    /** @deprecated Use CODE_DISK_FULL */
    const CODE_SHELL_ZIP_QUOTA             = 121;
    const CODE_SHELL_ZIP_FILE_NOT_FOUND    = 122;
    const CODE_SHELL_ZIP_FILE_COUNT_FAILED = 123;
    const CODE_SHELL_ZIP_RETRY_EXHAUSTED   = 124;
    const CODE_SHELL_ZIP_ARCHIVE_CORRUPT   = 125;
    const CODE_SHELL_ZIP_NO_ARCHIVE        = 126;

    const CODE_DB_VALIDATION_FAILED             = 130;
    const CODE_DB_CREATE_QUERY_FAILED           = 131;
    const CODE_DB_FILE_OPEN_FAILED              = 132;
    const CODE_DB_FILE_TRUNCATE_FAILED          = 133;
    const CODE_DB_EMPTY_FILE                    = 134;
    const CODE_DB_RETRY_EXHAUSTED               = 135;
    const CODE_DB_PROGRESS_FILE_FAILED          = 136;
    const CODE_DB_PHP_DUMP_INTERRUPTED          = 137;
    const CODE_DB_COMPRESSION_FAILED            = 138;
    const CODE_DB_PROGRESS_SERIALIZATION_FAILED = 139;

    const CODE_MYSQLDUMP_FAILED            = 140;
    const CODE_MYSQLDUMP_FILE_WRITE_FAILED = 141;
    const CODE_MYSQLDUMP_UNAVAILABLE       = 142;
    const CODE_MYSQLDUMP_INTERRUPTED       = 143;
    const CODE_DB_TABLE_LIST_FAILED        = 144;

    const CODE_DUP_ARCHIVE_ADD_FAILED        = 150;
    const CODE_DUP_ARCHIVE_VALIDATION_FAILED = 151;
    const CODE_DUP_ARCHIVE_RETRY_EXHAUSTED   = 153;
    const CODE_DUP_ARCHIVE_TRUNCATE_FAILED   = 154;

    const CODE_INTEGRITY_DB_INCOMPLETE          = 160;
    const CODE_INTEGRITY_INSTALLER_INCOMPLETE   = 161;
    const CODE_INTEGRITY_ARCHIVE_EMPTY          = 162;
    const CODE_INTEGRITY_SCANFILE_MISSING       = 163;
    const CODE_INTEGRITY_FILE_COUNT_MISMATCH    = 164;
    const CODE_INTEGRITY_DB_TOO_SMALL           = 165;
    const CODE_INTEGRITY_DB_FILE_MISSING        = 166;
    const CODE_INTEGRITY_INSTALLER_FILE_MISSING = 167;

    const CODE_ENCRYPTION_UNAVAILABLE       = 170;
    const CODE_STORAGE_INVALID              = 171;
    const CODE_INSTALLER_BUILD_FAILED       = 172;
    const CODE_INSTALLER_ADD_FAILED         = 173;
    const CODE_INSTALLER_CONSISTENCY_FAILED = 174;
    const CODE_DISK_FULL                    = 180;
    const CODE_FILE_TOO_LARGE               = 181;

    const CODE_OPTIONS_INVALID_CONFIGURATION = 190;
    const CODE_OPTIONS_INVALID_FILTER_RESULT = 191;

    const CODE_AUTOTUNE_SESSION_SAVE_FAILED = 200;

    const CODE_STORAGE_RETRY_LIMIT     = 210;
    const CODE_STORAGE_FINALIZE_FAILED = 211;
    const CODE_STORAGE_TRANSFER_FAILED = 212;

    /** @var int[] Core codes for expected, recognized conditions classified as handled severity */
    private const HANDLED_CODES = [
        self::CODE_LOCK_ACQUIRE_FAILED,
        self::CODE_DISK_FULL,
        self::CODE_FILE_TOO_LARGE,
        self::CODE_ZIP_NOT_AVAILABLE,
        self::CODE_SCAN_SOURCE_UNREADABLE,
        self::CODE_STORAGE_INVALID,
    ];

    /**
     * @param Throwable|null $previous The originating I/O failure, for chaining
     *
     * @return self
     */
    public static function diskFull(?Throwable $previous = null): self
    {
        $exception = new self(
            'File write failed: disk quota or storage space exhausted.',
            self::CODE_DISK_FULL,
            __(
                'Your hosting account ran out of disk space or reached its storage quota.
                Free up space by removing unneeded files or old backups, or contact your
                hosting provider to increase the available space, then run the Backup again.',
                'duplicator'
            ),
            $previous
        );
        $exception->useFactoryCallerOrigin(__FILE__);

        return $exception;
    }

    /**
     * @param Throwable|null $previous The originating I/O failure, for chaining
     *
     * @return self
     */
    public static function fileTooLarge(?Throwable $previous = null): self
    {
        $exception = new self(
            'File write failed: the file reached the maximum file size allowed by the server.',
            self::CODE_FILE_TOO_LARGE,
            __(
                'A Backup file reached the maximum file size allowed by your hosting server.
                This limit is part of the hosting configuration: contact your hosting provider to raise it,
                or reduce the Backup size with filters.',
                'duplicator'
            ),
            $previous
        );
        $exception->useFactoryCallerOrigin(__FILE__);

        return $exception;
    }

    /**
     * Builds the exception enriched with the reason of the last PHP error, appended to the
     * first message line (the telemetry fingerprint). Call it right after the failed operation.
     * Keep install-specific values such as paths on the detail lines after a newline.
     *
     * @param string $message     Raw English text for log/telemetry hash, never localized
     * @param int    $code        One of the CODE_* constants
     * @param string $userMessage Optional translated/formatted message for the end user
     *
     * @return self
     */
    public static function fromLastError(string $message, int $code = self::CODE_ERROR, string $userMessage = ''): self
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

        $exception = new self($message, $code, $userMessage);
        $exception->useFactoryCallerOrigin(__FILE__);

        return $exception;
    }

    /** @var string */
    protected $userMessage = '';

    /**
     * @param string         $message     Raw English text for log/telemetry hash, never localized
     * @param int            $code        One of the CODE_* constants
     * @param string         $userMessage Optional translated/formatted message for the end user
     * @param Throwable|null $previous    Previous throwable for chaining
     */
    public function __construct(
        string $message = '',
        int $code = self::CODE_ERROR,
        string $userMessage = '',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->userMessage = $userMessage;
    }

    /**
     * Normalize any throwable into a DupliException. A DupliException is
     * returned as-is; anything else is wrapped with CODE_ERROR, keeping the
     * original as previous and inheriting its file and line so the failure
     * location is preserved.
     *
     * @param Throwable $exception The failure cause
     *
     * @return self
     */
    public static function fromThrowable(Throwable $exception): self
    {
        if ($exception instanceof self) {
            return $exception;
        }

        $wrapped       = new self($exception->getMessage(), self::CODE_ERROR, '', $exception);
        $wrapped->file = $exception->getFile();
        $wrapped->line = $exception->getLine();

        return $wrapped;
    }

    /**
     * Severity classification of the failure. Core codes for expected,
     * recognized conditions map to handled; every other failure is an
     * unhandled error. Addon exceptions extending this class override this
     * method to classify their own domain codes.
     *
     * @return string one of the SEVERITY_* constants
     */
    public function getSeverity(): string
    {
        return in_array($this->getCode(), self::HANDLED_CODES, true) ? self::SEVERITY_HANDLED : self::SEVERITY_ERROR;
    }

    /**
     * Message for display: the translated/formatted userMessage when set,
     * otherwise the raw message as a fallback.
     *
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage !== '' ? $this->userMessage : $this->getMessage();
    }

    /**
     * True when a translated/formatted message for the end user was supplied.
     * Lets callers substitute their own text instead of displaying the raw
     * message that getUserMessage() falls back to.
     *
     * @return bool
     */
    public function hasUserMessage(): bool
    {
        return $this->userMessage !== '';
    }
}
