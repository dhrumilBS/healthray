<?php

declare(strict_types=1);

namespace Duplicator\Utils\Lock;

use Duplicator\Core\UniqueId;

/**
 * Base contract and state management for a lock backend.
 */
abstract class AbstractLockEngine
{
    public const STATUS_IDLE     = 10;
    public const STATUS_ACQUIRED = 20;
    public const STATUS_BUSY     = 30;
    public const STATUS_ERROR    = 40;

    private int $status = self::STATUS_IDLE;

    private ?string $lastLockError = null;

    /**
     * Attempt to acquire the lock without waiting.
     *
     * A false result is classified as busy or error by getStatus().
     *
     * @return bool True when the lock is acquired
     */
    abstract public function lock(): bool;

    /**
     * Release the lock when held by this engine.
     *
     * @return bool True when released or not held, false on an operational error
     */
    abstract public function unlock(): bool;

    /**
     * Test whether the lock backend works correctly.
     *
     * @return bool True when the backend is reliable
     */
    abstract public function isReliable(): bool;

    /**
     * Return the stable lock engine type.
     *
     * @return string Lock type
     */
    abstract public function getLockType(): string;

    /**
     * Return the human-readable lock engine label.
     *
     * @return string Lock label
     */
    abstract public function getLockLabel(): string;

    /**
     * Return the lock resource identifier.
     *
     * @return string Lock identifier
     */
    abstract protected function getIdentifier(): string;

    /**
     * Return an isolated identifier for reliability tests.
     *
     * @return string Test lock identifier, unique per call so concurrent
     *                reliability tests never collide
     */
    abstract protected function getTestIdentifier(): string;

    /**
     * Return the identifier of the current WordPress installation.
     *
     * @return string Site identifier
     */
    final protected static function getSiteHash(): string
    {
        static $hash = null;

        if ($hash === null) {
            $hash = UniqueId::getInstance()->getShortId();
        }

        return $hash;
    }

    /**
     * @return int Current lock status
     */
    final public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return bool True when this engine holds the lock
     */
    final public function isLocked(): bool
    {
        return $this->status === self::STATUS_ACQUIRED;
    }

    /**
     * @return string|null Error from the last lock operation
     */
    final public function getLastLockError(): ?string
    {
        return $this->lastLockError;
    }

    /**
     * Mark the lock as acquired.
     *
     * @return void
     */
    final protected function setAcquired(): void
    {
        $this->status        = self::STATUS_ACQUIRED;
        $this->lastLockError = null;
    }

    /**
     * Mark the lock as held by another process.
     *
     * @return void
     */
    final protected function setBusy(): void
    {
        $this->status        = self::STATUS_BUSY;
        $this->lastLockError = null;
    }

    /**
     * Mark the lock backend as unavailable.
     *
     * @param string $error Error description
     *
     * @return void
     */
    final protected function setError(string $error): void
    {
        $this->status        = self::STATUS_ERROR;
        $this->lastLockError = $error;
    }

    /**
     * Mark the lock as released.
     *
     * @return void
     */
    final protected function setReleased(): void
    {
        $this->status        = self::STATUS_IDLE;
        $this->lastLockError = null;
    }
}
