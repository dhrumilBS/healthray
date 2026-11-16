<?php

declare(strict_types=1);

namespace Duplicator\Core\Exceptions;

use Throwable;

/**
 * Thrown by a storage adapter when a transfer can't succeed on a later try: the transfer fails without retry
 */
class StorageTransferAbortException extends DupliException
{
    /**
     * @param string         $message     Raw English text for log/telemetry hash, never localized
     * @param string         $userMessage Optional translated message for the end user
     * @param Throwable|null $previous    The failure that caused the abort
     */
    public function __construct(string $message, string $userMessage = '', ?Throwable $previous = null)
    {
        parent::__construct($message, self::CODE_STORAGE_TRANSFER_ABORTED, $userMessage, $previous);
    }
}
