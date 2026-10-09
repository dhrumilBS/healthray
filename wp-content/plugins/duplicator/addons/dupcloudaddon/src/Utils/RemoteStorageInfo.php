<?php

namespace Duplicator\Addons\DupCloudAddon\Utils;

/**
 * Class to store and provide access to remote storage information
 */
class RemoteStorageInfo
{
    /** @var bool Whether the request was successful */
    private bool $success = false;

    /** @var bool Whether the storage is authorized */
    private bool $authorized = false;

    /** @var bool Whether the storage is ready for upload */
    private bool $ready = false;

    /** @var int Total available space in bytes */
    private int $totalSpace = 0;

    /** @var int Free space in bytes */
    private int $freeSpace = 0;

    /** @var string User name associated with the storage */
    private string $userName = '';

    /** @var string User email associated with the storage */
    private string $userEmail = '';

    /** @var string Website UUID */
    private string $websiteUuid = '';

    /**
     * Constructor
     *
     * @param bool   $success     Whether the request was successful
     * @param bool   $authorized  Whether the storage is authorized
     * @param bool   $ready       Whether the storage is ready for upload
     * @param int    $totalSpace  Total available space in bytes
     * @param int    $freeSpace   Free space in bytes
     * @param string $userName    User name associated with the storage
     * @param string $userEmail   User email associated with the storage
     * @param string $websiteUuid Website UUID
     */
    public function __construct(
        bool $success = false,
        bool $authorized = false,
        bool $ready = false,
        int $totalSpace = 0,
        int $freeSpace = 0,
        string $userName = '',
        string $userEmail = '',
        string $websiteUuid = ''
    ) {
        $this->success     = $success;
        $this->authorized  = $authorized;
        $this->ready       = $ready;
        $this->totalSpace  = $totalSpace;
        $this->freeSpace   = $freeSpace;
        $this->userName    = $userName;
        $this->userEmail   = $userEmail;
        $this->websiteUuid = $websiteUuid;
    }

    /**
     * Get whether the request was successful
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get whether the storage is authorized
     *
     * @return bool
     */
    public function isAuthorized(): bool
    {
        return $this->authorized;
    }

    /**
     * Get whether the storage is ready for upload
     *
     * @return bool
     */
    public function isReady(): bool
    {
        return $this->ready;
    }

    /**
     * Get total available space in bytes
     *
     * @return int
     */
    public function getTotalSpace(): int
    {
        return $this->totalSpace;
    }

    /**
     * Get free space in bytes
     *
     * @return int
     */
    public function getFreeSpace(): int
    {
        return $this->freeSpace;
    }

    /**
     * Get user name associated with the storage
     *
     * @return string
     */
    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * Get user email associated with the storage
     *
     * @return string
     */
    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    /**
     * Get website UUID
     *
     * @return string
     */
    public function getWebsiteUuid(): string
    {
        return $this->websiteUuid;
    }
}
