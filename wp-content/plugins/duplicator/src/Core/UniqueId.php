<?php

/**
 * Website identifier manager
 *
 * This class manages a unique identifier for the website that is used for:
 * - Duplicator Cloud authentication and connection
 * - Usage statistics tracking
 * - Website identification across migrations and restorations
 *
 * The identifier is a 44-character string that remains persistent across
 * WordPress updates but can be carried over during site migrations. It is
 * derived deterministically from stable site material so that a site whose
 * options are wiped (image rebuild, template restore) reproduces the same
 * identifier instead of registering as a new site; generation falls back to
 * a random string when no usable AUTH_KEY is available.
 */

declare(strict_types=1);

namespace Duplicator\Core;

/**
 * Website Identifier Manager
 *
 * Manages the unique identifier for this WordPress installation.
 */
final class UniqueId
{
    /**
     * WordPress option key for storing the website identifier
     */
    const OPTION_KEY = 'dupli_opt_unique_id';

    /**
     * Characters allowed in the identifier
     */
    const IDENTIFIER_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_-.,;=+&';

    /**
     * Length of the generated identifier
     */
    const IDENTIFIER_LENGTH = 44;

    /**
     * WordPress default placeholder value for undefined salts in wp-config.php
     */
    const AUTH_KEY_PLACEHOLDER = 'put your unique phrase here';

    /**
     * Singleton instance
     *
     * @var ?self
     */
    private static $instance = null;

    /**
     * The website identifier
     *
     * @var string
     */
    private $identifier = '';

    /**
     * Get singleton instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Class constructor
     *
     * Loads or generates the website identifier.
     * If the identifier doesn't exist in the database, a new one is generated.
     * Migration from old location is handled by UpgradeFunctions::migrateWebsiteIdentifier()
     *
     * Always reads/writes on the network's main site, not the current request's blog.
     */
    private function __construct()
    {
        $switched = is_multisite() && !is_main_site() && switch_to_blog(get_main_site_id());

        $identifier = get_option(self::OPTION_KEY, false);

        if ($identifier !== false && strlen($identifier) > 0) {
            $this->identifier = $identifier;
        } else {
            $this->identifier = self::generateIdentifier();
            $this->save();
        }

        if ($switched) {
            restore_current_blog();
        }
    }

    /**
     * Get the website identifier
     *
     * @return string The unique website identifier
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Get a short hex hash derived from the website identifier
     *
     * NOTE: This is NOT a unique identifier. Collisions are rare but possible,
     * especially with shorter lengths. Do not use where absolute uniqueness is
     * required (e.g. authentication, deduplication). Suitable for best-effort
     * disambiguation like lock names or file suffixes.
     *
     * @param int $length Number of hex characters (1-32)
     *
     * @return string Hex hash of the requested length
     */
    public function getShortId(int $length = 8): string
    {
        return substr(md5($this->identifier), 0, max(1, min($length, 32)));
    }

    /**
     * Update the website identifier from migration data
     *
     * This method is called during site migration/restoration to update
     * the identifier from the source site.
     *
     * @param string $identifier The identifier from the source site
     *
     * @return bool True if the identifier was updated, false otherwise
     */
    public function updateFromMigration(string $identifier): bool
    {
        if (strlen($identifier) === 0) {
            return false;
        }

        if ($identifier === $this->identifier) {
            return true;
        }

        $this->identifier = $identifier;
        return $this->save();
    }

    /**
     * Save the identifier to WordPress options
     *
     * Always writes on the network's main site, not the current request's blog.
     *
     * @return bool True if saved successfully, false otherwise
     */
    private function save(): bool
    {
        $switched = is_multisite() && !is_main_site() && switch_to_blog(get_main_site_id());
        $result   = update_option(self::OPTION_KEY, $this->identifier, true);
        if ($switched) {
            restore_current_blog();
        }

        return $result;
    }

    /**
     * Generate a new identifier
     *
     * Derives the identifier deterministically from stable site material, so
     * that regenerations on the same site (options wiped by an image rebuild,
     * a template restore or a plugin reset) reproduce the same identifier.
     * Falls back to a random identifier when AUTH_KEY is unusable.
     *
     * @return string The generated identifier
     */
    protected static function generateIdentifier(): string
    {
        $authKey = defined('AUTH_KEY') ? (string) constant('AUTH_KEY') : '';
        if ($authKey === '' || $authKey === self::AUTH_KEY_PLACEHOLDER) {
            return self::generateRandomIdentifier();
        }

        return self::deriveIdentifier(
            $authKey,
            (string) get_site_url(is_multisite() ? get_main_site_id() : null),
            (string) ABSPATH,
            PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION
        );
    }

    /**
     * Derive a deterministic identifier from site material
     *
     * Same material always yields the same identifier. One-way hash: nothing
     * is recoverable from the identifier.
     *
     * @param string $authKey    AUTH_KEY salt from wp-config.php
     * @param string $siteUrl    Network main site URL
     * @param string $path       WordPress root path
     * @param string $phpVersion PHP major.minor version; patch and distro suffixes would churn the identifier
     *
     * @return string The derived identifier
     */
    public static function deriveIdentifier(string $authKey, string $siteUrl, string $path, string $phpVersion): string
    {
        $hash      = hash('sha512', $authKey . '|' . $siteUrl . '|' . $path . '|' . $phpVersion, true);
        $charsSize = strlen(self::IDENTIFIER_CHARS);
        $result    = '';

        for ($i = 0; $i < self::IDENTIFIER_LENGTH; $i++) {
            $result .= self::IDENTIFIER_CHARS[ord($hash[$i]) % $charsSize];
        }

        return $result;
    }

    /**
     * Generate a new random identifier using the allowed character set
     *
     * @return string The generated identifier
     */
    protected static function generateRandomIdentifier(): string
    {
        $maxRand = strlen(self::IDENTIFIER_CHARS) - 1;
        $result  = '';

        for ($i = 0; $i < self::IDENTIFIER_LENGTH; $i++) {
            $result .= substr(self::IDENTIFIER_CHARS, wp_rand(0, $maxRand), 1);
        }

        return $result;
    }
}
