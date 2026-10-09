<?php

namespace Duplicator\Models\Storages;

use Duplicator\Utils\OAuth\TokenEntity;

/**
 * Adds OAuth2 token handling to a storage entity.
 *
 * Handles token caching, refresh-on-expiry, and exclusion from serialization
 * so the using storage only needs to implement how token data is read from
 * and written back to its storage-specific config.
 */
abstract class AbstractOAuthStorage extends AbstractStorageEntity implements StorageAuthInterface
{
    /** @var ?TokenEntity In-memory cached token. Excluded from serialization (see __serialize). */
    protected $token = null;

    /**
     * Return the token data used to instantiate the TokenEntity.
     *
     * @return array<string, mixed>
     */
    abstract protected function getTokenConfig(): array;

    /**
     * Persist the refreshed token data back to the storage config.
     *
     * @param TokenEntity $token The refreshed token entity
     *
     * @return void
     */
    abstract protected function setTokenConfig(TokenEntity $token): void;

    /**
     * Get the OAuth token, refreshing and persisting it if it is about to expire.
     *
     * @return TokenEntity
     */
    public function getToken(): TokenEntity
    {
        if ($this->token !== null) {
            return $this->token;
        }

        $expiryBufferMultiplier = 5;
        $this->token            = new TokenEntity(static::getSType(), $this->getTokenConfig());
        $expiryBufferIn         = max(
            TokenEntity::EXPIRY_BUFFER_MIN_SECONDS,
            $expiryBufferMultiplier * (int) ($this->getUploadChunkTimeout() / SECONDS_IN_MICROSECONDS)
        );

        if (
            $this->token->isValid() &&
            $this->token->isAboutToExpire($expiryBufferIn) &&
            $this->token->refresh(true)
        ) {
            $this->setTokenConfig($this->token);
            $this->save();
        }

        return $this->token;
    }

    /**
     * Exclude the in-memory token from serialization so it is never persisted to the database.
     *
     * @return array<string, mixed>
     */
    public function __serialize(): array
    {
        $data = parent::__serialize();
        unset($data['token']);
        return $data;
    }
}
