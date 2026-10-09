<?php

namespace Duplicator\Models\Storages;

interface StorageAuthInterface
{
    /**
     * Is authorized
     *
     * @return bool
     */
    public function isAuthorized(): bool;

    /**
     * Authorized from HTTP request
     *
     * @param string $message Message
     *
     * @return bool True if authorized, false if failed
     */
    public function authorizeFromRequest(?string &$message = ''): bool;

    /**
     * Revokes authorization
     *
     * @param string $message Message
     *
     * @return bool True if revoked, false if failed
     */
    public function revokeAuthorization(?string &$message = ''): bool;
}
