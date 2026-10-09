<?php

namespace Duplicator\Utils\OAuth;

use Duplicator\Utils\ExpireOptions;
use Duplicator\Utils\OAuth\TokenEntity;

/**
 * This class is responsible for handling communication with the OAuth2 servers.
 */
class TokenService
{
    const BACKOFF_KEY = 'oauth_backoff_';

    /** @var int Storage type identifier */
    protected int $storage_type;

    /**
     * Create a new instance of the service.
     *
     * @param int $storage_type Storage type identifier
     */
    public function __construct($storage_type)
    {
        $this->storage_type = (int) $storage_type;
    }

    /**
     * Get a list of servers capable of handling the OAuth2 requests.
     *
     * @return string[]
     */
    private static function getServerCandidates(): array
    {
        return [
            DUPLICATOR_PRIMARY_OAUTH_SERVER,
            DUPLICATOR_SECONDARY_OAUTH_SERVER,
        ];
    }

    /**
     * Get the redirect uri for the current provider.
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        $candidates = self::getServerCandidates();
        return sprintf('%s/oauth/%s/connect', $candidates[0], $this->storage_type);
    }

    /**
     * Refresh the token from the server.
     *
     * Tries each server candidate in turn and stops on the first success.
     * Servers currently in backoff are skipped.
     *
     * @param TokenEntity $token The token entity to be refreshed.
     *
     * @return void
     * @throws \Exception
     */
    public function refreshToken(TokenEntity $token): void
    {
        $lastError = null;
        foreach (self::getServerCandidates() as $server) {
            if (self::shouldBackOff($server)) {
                continue;
            }

            try {
                $this->refreshTokenOnServer($server, $token);
                return;
            } catch (\Exception $e) {
                $lastError = $e;
            }
        }

        throw $lastError ?? new \Exception('No server is available to refresh token, please try again later.');
    }

    /**
     * Refresh the token against a specific server.
     *
     * @param string      $server Server URL.
     * @param TokenEntity $token  The token entity to be refreshed.
     *
     * @return void
     * @throws \Exception
     */
    private function refreshTokenOnServer(string $server, TokenEntity $token): void
    {
        $url      = sprintf('%s/oauth/%s/refresh', $server, $this->storage_type);
        $response = wp_remote_post($url, [
            'timeout' => 15,
            'body'    => [
                'refresh_token' => $token->getRefreshToken(),
            ],
        ]);
        self::maybeBackoffNextTime($server, $response);

        if (is_wp_error($response)) {
            throw new \Exception($response->get_error_message());
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            throw new \Exception('Could not retrieve token refresh response body');
        }

        $data = json_decode($body, true);

        if (isset($data['error'])) {
            $message = "Failed to refresh token with error: {$data['error']}";
            if (isset($data['error_description'])) {
                $message .= " - {$data['error_description']}";
            }
            throw new \Exception($message);
        }

        $token->updateProperties($data);
    }

    /**
     * Record a backoff for the next time if needed.
     *
     * @param string                         $server   Server URL.
     * @param array<string, mixed>|\WP_Error $response The response from the server.
     *
     * @return void
     */
    private static function maybeBackoffNextTime(string $server, $response): void
    {
        $code = (int) wp_remote_retrieve_response_code($response);

        // If the response code is 0, 429 or between 500 and 599 we should backoff.
        if ($code === 0 || $code === 429 || ($code >= 500 && $code <= 599)) {
            ExpireOptions::set(self::BACKOFF_KEY . $server, true, MINUTE_IN_SECONDS);
        }
    }

    /**
     * Check if we have to stop trying to connect to the server.
     *
     * @param string $server Server URL.
     *
     * @return bool
     */
    private static function shouldBackOff($server): bool
    {
        return (bool) ExpireOptions::get(self::BACKOFF_KEY . $server, false);
    }
}
