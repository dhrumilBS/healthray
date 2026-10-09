<?php

declare(strict_types=1);

namespace Duplicator\Utils\Lock;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Utils\Logging\DupLog;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Non-blocking filesystem lock engine.
 */
class FileLock extends AbstractLockEngine
{
    private string $lockFilePath;

    /** @var false|resource */
    private $handle = false;

    /**
     * @param string|null $lockFilePath Lock file path, or null for the site default
     */
    public function __construct(?string $lockFilePath = null)
    {
        if ($lockFilePath === '') {
            throw new InvalidArgumentException('Lock file path cannot be empty');
        }

        $this->lockFilePath = $lockFilePath ?? DUPLICATOR_SSDIR_PATH . '/building_lock_' . self::getSiteHash() . '.txt';
    }

    /**
     * @return bool True when the lock is acquired
     */
    public function lock(): bool
    {
        if ($this->isLocked()) {
            return true;
        }

        try {
            $lockDir = dirname($this->getIdentifier());
            if (!is_dir($lockDir) && !wp_mkdir_p($lockDir)) {
                throw new RuntimeException("cannot create lock directory {$lockDir}");
            }

            $this->handle = SnapIO::fopen($this->getIdentifier(), 'c+', false);
            if ($this->handle === false) {
                if ($this->isWindowsOpenContention()) {
                    $this->setBusy();
                    return false;
                }

                $error = error_get_last();
                throw new RuntimeException(
                    'cannot open file: ' . ($error['message'] ?? 'unknown error')
                );
            }

            $wouldBlock = 0;
            if (!flock($this->handle, LOCK_EX | LOCK_NB, $wouldBlock)) {
                if ($wouldBlock === 1) {
                    $this->setBusy();
                    return false;
                }

                throw new RuntimeException('flock failed');
            }

            $this->setAcquired();
            return true;
        } catch (Throwable $e) {
            $this->setError("Cannot acquire file lock {$this->getIdentifier()}: {$e->getMessage()}");
            return false;
        } finally {
            if (!$this->isLocked() && is_resource($this->handle)) {
                fclose($this->handle);
                $this->handle = false;
            }
        }
    }

    /**
     * @return bool True when released or not held
     */
    public function unlock(): bool
    {
        if ($this->handle === false) {
            return true;
        }

        try {
            if (!flock($this->handle, LOCK_UN)) {
                throw new RuntimeException('flock unlock failed');
            }

            $this->setReleased();
            return true;
        } catch (Throwable $e) {
            $this->setError("Cannot release file lock {$this->getIdentifier()}: {$e->getMessage()}");
            return false;
        } finally {
            if (is_resource($this->handle)) {
                fclose($this->handle);
            }
            $this->handle = false;
        }
    }

    /**
     * @return bool True when file locks provide mutual exclusion
     */
    public function isReliable(): bool
    {
        $testIdentifier = $this->getTestIdentifier();
        $primaryLock    = new self($testIdentifier);
        $probeLock      = new self($testIdentifier);
        $reliable       = false;

        try {
            $directory = dirname($testIdentifier);
            if (!is_dir($directory) && !wp_mkdir_p($directory)) {
                throw new RuntimeException("cannot create lock directory {$directory}");
            }

            if (!$primaryLock->lock()) {
                throw new RuntimeException($primaryLock->getLastLockError() ?? 'cannot acquire primary test lock');
            }

            if ($probeLock->lock()) {
                throw new RuntimeException('both test instances acquired the same exclusive lock');
            }

            if ($probeLock->getStatus() !== self::STATUS_BUSY) {
                throw new RuntimeException($probeLock->getLastLockError() ?? 'probe test lock failed unexpectedly');
            }

            $reliable = true;
        } catch (Throwable $e) {
            $this->setError("File lock reliability test failed: {$e->getMessage()}");
        } finally {
            if (!$probeLock->unlock()) {
                DupLog::trace($probeLock->getLastLockError() ?? 'Cannot release file probe lock');
            }

            if (!$primaryLock->unlock()) {
                DupLog::trace($primaryLock->getLastLockError() ?? 'Cannot release primary file test lock');
            }

            SnapIO::unlink($testIdentifier);
        }

        return $reliable;
    }

    /**
     * @return string Lock type
     */
    public function getLockType(): string
    {
        return 'file';
    }

    /**
     * @return string Lock label
     */
    public function getLockLabel(): string
    {
        return 'File';
    }

    /**
     * @return bool True when Windows denied a second handle to an accessible lock file
     */
    private function isWindowsOpenContention(): bool
    {
        $path = $this->getIdentifier();

        return SnapServer::isWindows()
            && is_file($path)
            && is_readable($path)
            && is_writable($path);
    }

    /**
     * @return string Lock file path
     */
    protected function getIdentifier(): string
    {
        return $this->lockFilePath;
    }

    /**
     * @return string Lock file path used for tests, unique per call so
     *                concurrent reliability tests never collide
     */
    protected function getTestIdentifier(): string
    {
        return (string) preg_replace('~(?<=[^/\\.])(?=\.[^/\\.]+$)|$~', '_ts' . bin2hex(random_bytes(2)) . '_', $this->getIdentifier(), 1);
    }
}
