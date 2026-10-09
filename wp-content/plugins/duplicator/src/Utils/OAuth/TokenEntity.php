<?php

namespace Duplicator\Utils\OAuth;

use Duplicator\Utils\Logging\DupLog;

/**
 * This class represents an OAuth2 token.
 * It should only have token data and no other logic.
 * All interaction with the token should be done through the OauthTokenService class.
 */
class TokenEntity
{
    /** @var int Minimum number of seconds before expiry to consider a token "about to expire" */
    const EXPIRY_BUFFER_MIN_SECONDS = 60;

    /** @var int The storage type identifier */
    protected $storage_type = -1;
    /** @var int The timestamp when the token was created */
    protected $created = 0;
    /** @var string */
    protected $access_token = '';
    /** @var string */
    protected $refresh_token = '';
    /** @var int */
    protected $expires_in = 0;
    /** @var string[] */
    protected $scope = [];

    /**
     * Create a new instance of TokenEntity.
     *
     * @param int                                      $storage_type The storage type identifier
     * @param array<string,scalar|list<string>>|string $token        The json token string or array containing access_token,
     *                                                               refresh_token, expires_in, token_type, scope
     */
    public function __construct($storage_type, $token = [])
    {
        $this->storage_type = $storage_type;

        if (is_string($token)) {
            $token = json_decode($token, true);
        }

        $this->updateProperties($token);
    }

    /**
     * Get access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Get refresh token.
     *
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    /**
     * Get scope.
     *
     * @return string[]
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Get created.
     *
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get expires in.
     *
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expires_in;
    }

    /**
     * Refresh the token if needed.
     *
     * @param bool $force Force the token to be refreshed ignoring the expiration time
     *
     * @return bool true if the token was refreshed, false otherwise
     */
    public function refresh($force = false): bool
    {
        if ($force || $this->isAboutToExpire()) {
            try {
                (new TokenService($this->storage_type))->refreshToken($this);
            } catch (\Exception $e) {
                DupLog::infoTrace("Could not refresh token: " . $e->getMessage());
                return false;
            }
        }

        if (!$this->isValid()) {
            DupLog::trace("Could not refresh token");
            return false;
        }
        return true;
    }

    /**
     * Check if the token is about to expire.
     * The token is considered "about to expire" if it expires in less than 60 seconds.
     *
     * @param int $in The number of seconds to consider the token about to expire, default 60
     *
     * @return bool
     */
    public function isAboutToExpire($in = self::EXPIRY_BUFFER_MIN_SECONDS): bool
    {
        return ($this->created + $this->expires_in) < time() + $in;
    }

    /**
     * Check if the token is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->access_token) && !empty($this->refresh_token) && $this->expires_in > 0;
    }

    /**
     * Check if the token has the given scope/scopes.
     *
     * @param string[]|string $scopes The scope or scopes that must be present in this token
     *
     * @return bool
     */
    public function hasScopes($scopes): bool
    {
        if (empty($scopes)) {
            return true;
        }

        if (empty($this->scope)) {
            return false;
        }

        $scopes = is_array($scopes) ? $scopes : [$scopes];

        foreach ($scopes as $scope) {
            if (!in_array($scope, $this->scope)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Update the token properties.
     *
     * @param array<string, string> $data The token data
     *
     * @return void
     */
    public function updateProperties($data): void
    {
        $this->created       = isset($data['created']) ? (int) $data['created'] : $this->created;
        $this->access_token  = $data['access_token'] ?? $this->access_token;
        $this->refresh_token = $data['refresh_token'] ?? $this->refresh_token;
        $this->expires_in    = $data['expires_in'] ?? $this->expires_in;
        $this->scope         = $data['scope'] ?? $this->scope;
        if (is_string($this->scope)) {
            $this->scope = array_values(array_filter(explode(' ', $this->scope)));
        }
    }
}
