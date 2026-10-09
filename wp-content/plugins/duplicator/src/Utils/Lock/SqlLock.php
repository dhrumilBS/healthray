<?php

declare(strict_types=1);

namespace Duplicator\Utils\Lock;

use Duplicator\Utils\Logging\DupLog;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * MySQL named-lock engine.
 */
class SqlLock extends AbstractLockEngine
{
    private string $lockName;

    /**
     * @param string|null $lockName MySQL named-lock identifier, or null for the site default
     */
    public function __construct(?string $lockName = null)
    {
        if ($lockName === '') {
            throw new InvalidArgumentException('SQL lock name cannot be empty');
        }

        $this->lockName = $lockName ?? 'dupli_lock_' . self::getSiteHash();
    }

    /**
     * @return bool True when the lock is acquired
     */
    public function lock(): bool
    {
        global $wpdb;

        if ($this->isLocked()) {
            return true;
        }

        try {
            $query  = $wpdb->prepare('SELECT GET_LOCK(%s, 0)', $this->getIdentifier());
            $result = $wpdb->get_var($query);

            if ($wpdb->last_error !== '') {
                throw new RuntimeException($wpdb->last_error);
            }

            if ($result === null) {
                throw new RuntimeException('database returned NULL');
            }

            $result = (int) $result;
            if ($result === 1) {
                $this->setAcquired();
                return true;
            }

            if ($result === 0) {
                $this->setBusy();
                return false;
            }

            throw new RuntimeException("database returned unexpected result {$result}");
        } catch (Throwable $e) {
            $this->setError("Cannot acquire SQL lock: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * @return bool True when released or not held
     */
    public function unlock(): bool
    {
        global $wpdb;

        if (!$this->isLocked()) {
            return true;
        }

        try {
            $query  = $wpdb->prepare('SELECT RELEASE_LOCK(%s)', $this->getIdentifier());
            $result = $wpdb->get_var($query);

            if ($wpdb->last_error !== '') {
                throw new RuntimeException($wpdb->last_error);
            }

            if ((int) $result !== 1) {
                throw new RuntimeException('lock is no longer owned by this connection');
            }

            $this->setReleased();
            return true;
        } catch (Throwable $e) {
            $this->setError("Cannot release SQL lock: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * @return bool True when SQL named locks are reliable
     */
    public function isReliable(): bool
    {
        global $wpdb;

        $testLock = new self($this->getTestIdentifier());
        $reliable = false;

        try {
            if (!$testLock->lock()) {
                throw new RuntimeException($testLock->getLastLockError() ?? 'cannot acquire test lock');
            }

            $query  = $wpdb->prepare(
                'SELECT IS_USED_LOCK(%s) = CONNECTION_ID()',
                $testLock->getIdentifier()
            );
            $result = $wpdb->get_var($query);

            if ($wpdb->last_error !== '') {
                throw new RuntimeException($wpdb->last_error);
            }

            if ($result === null || (int) $result !== 1) {
                throw new RuntimeException('test lock is not held by the current connection');
            }

            $reliable = true;
        } catch (Throwable $e) {
            $this->setError("SQL lock reliability test failed: {$e->getMessage()}");
        } finally {
            if (!$testLock->unlock()) {
                DupLog::trace($testLock->getLastLockError() ?? 'Cannot release SQL test lock');
            }
        }

        return $reliable;
    }

    /**
     * @return string Lock type
     */
    public function getLockType(): string
    {
        return 'sql';
    }

    /**
     * @return string Lock label
     */
    public function getLockLabel(): string
    {
        return 'SQL';
    }

    /**
     * @return string MySQL named-lock identifier
     */
    protected function getIdentifier(): string
    {
        return $this->lockName;
    }

    /**
     * @return string MySQL named-lock identifier used for tests, unique per
     *                call so concurrent reliability tests never collide
     */
    protected function getTestIdentifier(): string
    {
        return $this->getIdentifier() . '_ts' . bin2hex(random_bytes(2));
    }
}
