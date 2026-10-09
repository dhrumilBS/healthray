<?php

namespace Duplicator\Utils\Lock;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Throwable;

/**
 * Lock utility
 */
class LockUtil
{
    /** @var AbstractLockEngine[] Lock engines used by the current process */
    protected static array $processLocks = [];

    /** @var bool True after the acquisition trace has been written for the current request */
    private static bool $processLocksTraced = false;

    /**
     * Run both lock reliability tests and return the test details.
     *
     * Diagnostic only: locking acquires every available engine in parallel,
     * the results are surfaced in the settings/tools UI. Reliability checks
     * use isolated test identifiers and do not acquire the process lock.
     *
     * @return array{sqlReliable: bool, fileReliable: bool, sqlError: string, fileError: string}
     */
    public static function redetectLockMode(): array
    {
        $result = [
            'sqlReliable'  => false,
            'fileReliable' => false,
            'sqlError'     => '',
            'fileError'    => '',
        ];

        foreach (self::createLockEngines() as $lock) {
            $reliable = $lock->isReliable();
            if (!$reliable) {
                self::logLockError($lock);
            }

            switch ($lock->getLockType()) {
                case 'sql':
                    $result['sqlReliable'] = $reliable;
                    $result['sqlError']    = $reliable ? '' : ($lock->getLastLockError() ?? 'Unknown SQL lock error');
                    break;
                case 'file':
                    $result['fileReliable'] = $reliable;
                    $result['fileError']    = $reliable ? '' : ($lock->getLastLockError() ?? 'Unknown file lock error');
                    break;
            }
        }

        return $result;
    }

    /**
     * Create the lock engines, filterable for testing and host customization.
     *
     * @return AbstractLockEngine[] Lock engines in acquisition order
     */
    private static function createLockEngines(): array
    {
        $engines = apply_filters('duplicator_lock_engines', [
            new SqlLock(),
            new FileLock(),
        ]);

        return array_values(array_filter(
            is_array($engines) ? $engines : [],
            fn($engine): bool => $engine instanceof AbstractLockEngine
        ));
    }

    /**
     * Lock process, failing closed when no engine is available.
     *
     * Acquires every available process-lock engine without waiting.
     * Used by the build entry points, which surface the failure to the user.
     *
     * @throws DupliException When every lock engine is unavailable
     *
     * @return bool true if lock acquired, false when another process holds a lock
     */
    public static function lockProcessOrThrow(): bool
    {
        if (self::hasAcquiredProcessLock()) {
            return true;
        }

        $errors = [];

        try {
            foreach (self::getProcessLocks() as $lock) {
                if ($lock->getStatus() === AbstractLockEngine::STATUS_ERROR) {
                    $errors[] = $lock->getLastLockError()
                        ?? get_class($lock) . ' is unavailable';
                    continue;
                }

                if ($lock->lock()) {
                    continue;
                }

                if ($lock->getStatus() === AbstractLockEngine::STATUS_BUSY) {
                    self::releaseProcessLocks();
                    return false;
                }

                $errors[] = $lock->getLastLockError()
                    ?? get_class($lock) . ' returned an unexpected lock status';
            }

            if (self::hasAcquiredProcessLock()) {
                self::traceAcquiredProcessLocks();
                return true;
            }

            throw self::createAcquireException($errors);
        } catch (Throwable $e) {
            self::releaseProcessLocks();

            if ($e instanceof DupliException) {
                throw $e;
            }

            throw self::createAcquireException([$e->getMessage()], $e);
        }
    }

    /**
     * Lock process
     *
     * Boolean variant for the worker path: engine failures are intercepted
     * at the build entry points, so here any failure only means "not acquired".
     *
     * @return bool true if lock acquired
     */
    public static function lockProcess(): bool
    {
        try {
            return self::lockProcessOrThrow();
        } catch (DupliException $e) {
            DupLog::traceError('Process lock unavailable: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Return process-lock engines in acquisition order.
     *
     * Every engine is always used: the reliability detection is informational
     * only, an unreliable engine simply fails to acquire at runtime.
     *
     * @return AbstractLockEngine[] Lock engines
     */
    private static function getProcessLocks(): array
    {
        if (self::$processLocks !== []) {
            return self::$processLocks;
        }

        self::$processLocks = self::createLockEngines();

        return self::$processLocks;
    }

    /**
     * Unlock process
     *
     * Idempotent: no-op returning true when the current request does not hold the lock.
     *
     * @return bool true if lock released or not held
     */
    public static function unlockProcess(): bool
    {
        return self::releaseProcessLocks();
    }

    /**
     * Return the current process-lock state for diagnostics.
     *
     * @return array{acquired: array<string, string>, errors: array<string, string>}
     */
    public static function getProcessLockInfo(): array
    {
        $result = [
            'acquired' => [],
            'errors'   => [],
        ];

        foreach (self::$processLocks as $lock) {
            if ($lock->isLocked()) {
                $result['acquired'][$lock->getLockType()] = $lock->getLockLabel();
            } elseif ($lock->getStatus() === AbstractLockEngine::STATUS_ERROR) {
                $result['errors'][$lock->getLockLabel()] = $lock->getLastLockError()
                    ?? $lock->getLockLabel() . ' lock is unavailable';
            }
        }

        return $result;
    }

    /**
     * @return bool True when at least one process lock is held
     *
     * @phpstan-impure
     */
    private static function hasAcquiredProcessLock(): bool
    {
        foreach (self::$processLocks as $lock) {
            if ($lock->isLocked()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Trace the process locks acquired by the current request.
     *
     * @return void
     */
    private static function traceAcquiredProcessLocks(): void
    {
        if (self::$processLocksTraced) {
            return;
        }

        $labels = [];

        foreach (self::$processLocks as $lock) {
            if ($lock->isLocked()) {
                $labels[$lock->getLockType()] = $lock->getLockLabel();
            } elseif ($lock->getStatus() === AbstractLockEngine::STATUS_ERROR) {
                self::logLockError($lock);
            }
        }

        DupLog::trace('Process locks acquired: ' . implode(', ', $labels));
        self::$processLocksTraced = true;
    }

    /**
     * Release every acquired process lock in reverse order.
     *
     * @return bool True when every acquired lock was released
     */
    private static function releaseProcessLocks(): bool
    {
        $locks    = array_reverse(self::$processLocks);
        $released = true;

        foreach ($locks as $lock) {
            if (!$lock->isLocked()) {
                continue;
            }

            try {
                if (!$lock->unlock()) {
                    self::logLockError($lock);
                    $released = false;
                }
            } catch (Throwable $e) {
                DupLog::trace('Cannot release process lock: ' . $e->getMessage());
                $released = false;
            }
        }

        return $released;
    }

    /**
     * Create the fail-closed exception used when no lock engine is available.
     *
     * @param string[]       $errors   Lock engine errors
     * @param Throwable|null $previous Previous throwable
     *
     * @return DupliException
     */
    private static function createAcquireException(array $errors, ?Throwable $previous = null): DupliException
    {
        return new DupliException(
            'Cannot acquire process lock: ' . implode('; ', $errors),
            DupliException::CODE_LOCK_ACQUIRE_FAILED,
            __(
                'The backup cannot start because no process-locking method is available.
                Check the backup log for details or contact your hosting provider.',
                'duplicator'
            ),
            $previous
        );
    }

    /**
     * Log an operational lock error when present.
     *
     * @param AbstractLockEngine $lock Lock engine
     *
     * @return void
     */
    private static function logLockError(AbstractLockEngine $lock): void
    {
        $error = $lock->getLastLockError();
        if ($error !== null) {
            DupLog::trace($error);
        }
    }
}
