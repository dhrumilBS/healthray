<?php

namespace Duplicator\Models;

use Closure;
use Duplicator\Core\Models\AbstractEntity;
use Duplicator\Core\Models\TraitEntitySerializationEncryption;
use Duplicator\Core\Models\TraitGenericModelSingleton;
use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Duplicator\Utils\Settings\ModelMigrateSettingsInterface;
use Duplicator\Views\AdminNotices;
use Exception;
use ReflectionClass;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Dynamic Global Entity values
 */
class DynamicGlobalEntity extends AbstractEntity implements ModelMigrateSettingsInterface
{
    use TraitGenericModelSingleton;
    use TraitEntitySerializationEncryption;

    const PACKAGE_CHECK_TS_KEY = 'package_check_ts';

    /** @var string Basic auth mode key ('auto': credentials kept in sync by the server detection, 'custom': user-entered) */
    const BASIC_AUTH_MODE_KEY = 'override_basic_auth';
    /** @var string Basic auth effective username key */
    const BASIC_AUTH_USER_KEY = 'basic_auth_user';
    /** @var string Basic auth effective password key */
    const BASIC_AUTH_PASSWORD_KEY = 'basic_auth_password';

    /**
     * Fallback values by getter type, used when a key has no registered
     * default and the caller passes none. Not an error: it is the same
     * behavior the getters signature defaults provided before the registry.
     *
     * @var array<string,scalar|mixed[]>
     */
    private const TYPE_DEFAULTS = [
        'int'    => 0,
        'string' => '',
        'bool'   => false,
        'float'  => 0.0,
        'array'  => [],
    ];

    /**
     * Runtime defaults registry, populated via registerDefaults(): the
     * entity is agnostic about the keys it stores — core keys are
     * registered by CoreSettingsDefaults in the bootstrap, addon keys by
     * each addon, storage keys by the storage classes.
     *
     * Defaults are resolved lazily at read time and never persisted, so a
     * site that never saved a key picks up a changed default on plugin
     * update. Keys without a registered default fall back to the
     * TYPE_DEFAULTS value of the getter type.
     *
     * @var array<string,scalar|mixed[]|Closure>
     */
    private static array $registeredDefaults = [];

    /**
     * Properties to encrypt during serialization
     *
     * @var string[]
     */
    protected static array $encryptedProperties = ['data'];

    /** @var array<string,scalar|mixed[]|null> Entity data */
    protected array $data = [];

    /**
     * Class constructor
     */
    protected function __construct()
    {
    }

    /**
     * Serialize the entity
     *
     * @return array<string,mixed>
     */
    public function __serialize(): array
    {
        $data = JsonSerialize::serializeToData(
            $this,
            JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME
        );

        return $this->encryptSerializedProperties($data);
    }

    /**
     * Unserialize the entity
     *
     * @param array<string,mixed> $data Data to unserialize
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $data = $this->decryptSerializedProperties($data);

        if ($this->isDecryptError()) {
            AdminNotices::enableNotice(AdminNotices::ENCRYPTED_RESET_NOTICE);
            $this->saveOnShutdown();
        }

        if (!is_array($data['data'])) {
            SnapLog::phpErr('dynamic global data was invalid and was reset');
            // In case of error, set the default value
            $data['data'] = [];
        }

        foreach ($data as $pName => $val) {
            if (!property_exists($this, $pName)) {
                continue;
            }
            $this->$pName = $val;
        }
    }

    /**
     * Handle legacy format decryption (dataIsEncrypted marker)
     *
     * @param array<string,mixed> $data Serialized data in legacy format
     *
     * @return array<string,mixed> Decrypted data
     */
    protected function legacyDecryptProperties(array $data): array
    {
        if (isset($data['dataIsEncrypted']) && $data['dataIsEncrypted']) {
            $decrypted    = CryptBlowfish::decryptIfAvaiable($data['data'], null, true);
            $data['data'] = json_decode($decrypted, true);
        }
        unset($data['dataIsEncrypted']);

        return $data;
    }

    /**
     * Register default values for dynamic keys.
     *
     * Must be called before any code that can read the keys (upgrade
     * functions, cron, WP-CLI): addons call it in their bootstrap init.
     * A Closure default is resolved lazily at read time (dynamic defaults
     * are written as `fn() => ...`); only Closure instances are accepted as
     * lazy defaults, a callable string would be indistinguishable from a
     * plain string value.
     *
     * Re-registering the same key and value is a no-op; Closure defaults are
     * idempotent only when the same Closure instance is reused. A different
     * value throws so defaults cannot be silently overwritten.
     *
     * @param array<string,scalar|mixed[]|Closure> $map key => default map
     *
     * @return void
     */
    public static function registerDefaults(array $map): void
    {
        foreach ($map as $key => $default) {
            if (!is_string($key) || strlen($key) == 0) {
                throw new Exception('Invalid defaults registry key');
            }
            self::validateDefaultValue($key, $default, true);
            $current = self::$registeredDefaults[$key] ?? null;
            if ($current !== null && $current !== $default) {
                throw new Exception('Default for key "' . $key . '" is already registered with a different value');
            }
            self::$registeredDefaults[$key] = $default;
        }
    }

    /**
     * Check if a key has a registered default
     *
     * @param string $key Option name
     *
     * @return bool
     */
    public static function hasRegisteredDefault(string $key): bool
    {
        return array_key_exists($key, self::$registeredDefaults);
    }

    /**
     * Resolve the registered default of a key
     *
     * @param string $key Option name
     *
     * @return scalar|mixed[]|null Null when the key has no registered default
     */
    protected static function getRegisteredDefault(string $key)
    {
        if (!array_key_exists($key, self::$registeredDefaults)) {
            return null;
        }

        $default = self::$registeredDefaults[$key];
        if (!($default instanceof Closure)) {
            return $default;
        }

        $value = $default();
        self::validateDefaultValue($key, $value, false);
        return $value;
    }

    /**
     * Validate a registry default or a resolved lazy default.
     *
     * @param string $key          Registry key
     * @param mixed  $value        Default value
     * @param bool   $allowClosure Whether a lazy default is accepted
     *
     * @return void
     */
    private static function validateDefaultValue(string $key, $value, bool $allowClosure): void
    {
        if (is_scalar($value)) {
            return;
        }
        if (is_array($value) && self::isJsonSafeArray($value)) {
            return;
        }
        if ($allowClosure && $value instanceof Closure) {
            return;
        }

        $allowed = ($allowClosure ? 'scalar, JSON-serializable array or Closure' : 'scalar or JSON-serializable array');
        throw new Exception('Invalid default for key "' . $key . '", only ' . $allowed . ' values are allowed');
    }

    /**
     * Resolve the value of a key: stored value first, then the registered
     * default, last the TYPE_DEFAULTS value of the getter type.
     *
     * @param string $key  Option name
     * @param string $type TYPE_DEFAULTS key of the calling getter
     *
     * @return scalar|mixed[]|null
     */
    private function resolveValue(string $key, string $type)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        if (self::hasRegisteredDefault($key)) {
            return self::getRegisteredDefault($key);
        }

        return self::TYPE_DEFAULTS[$type];
    }

    /**
     * Get a value as integer
     *
     * @param string $key Option name
     *
     * @return int
     */
    public function getValInt(string $key): int
    {
        return (int) $this->resolveValue($key, 'int');
    }

    /**
     * Get a value as string
     *
     * @param string $key Option name
     *
     * @return string
     */
    public function getValString(string $key): string
    {
        return (string) $this->resolveValue($key, 'string');
    }

    /**
     * Get a value as boolean
     *
     * @param string $key Option name
     *
     * @return bool
     */
    public function getValBool(string $key): bool
    {
        return (bool) $this->resolveValue($key, 'bool');
    }

    /**
     * Get a value as float
     *
     * @param string $key Option name
     *
     * @return float
     */
    public function getValFloat(string $key): float
    {
        return (float) $this->resolveValue($key, 'float');
    }

    /**
     * Get a value as array
     *
     * @param string $key Option name
     *
     * @return mixed[] The resolved value if it is an array, the array type default otherwise
     */
    public function getValArray(string $key): array
    {
        $value = $this->resolveValue($key, 'array');
        return (is_array($value) ? $value : []);
    }

    /**
     * Get HTTP Basic Authentication header value if configured
     *
     * The stored credentials are always the effective ones regardless of the
     * mode ('auto' keeps them in sync via the server detection, 'custom' stores
     * the user-entered values): both empty means basic auth is not set.
     *
     * @return string|null The Authorization header value (e.g., "Basic base64..."), or null if not set
     */
    public function getBasicAuthHeader(): ?string
    {
        $user     = $this->getValString(self::BASIC_AUTH_USER_KEY);
        $password = $this->getValString(self::BASIC_AUTH_PASSWORD_KEY);

        if ($user === '' && $password === '') {
            return null;
        }

        return 'Basic ' . base64_encode($user . ':' . $password);
    }

    /**
     * Set option value.
     *
     * Internal only: settings are written through the typed setters.
     * Objects never enter the key-value store: the accepted value shape is
     * JSON-serializable data (scalars, scalar arrays, nested associative
     * arrays). Object-valued settings are persisted as plain arrays and the
     * domain accessor owns the hydration.
     *
     * @param string              $key   Option name
     * @param scalar|mixed[]|null $value Option value
     * @param bool                $save  Save on DB
     *
     * @return bool
     */
    protected function setVal(string $key, $value = null, bool $save = false): bool
    {
        if (strlen($key) == 0) {
            throw new Exception('Invalid key');
        }
        if (
            !is_scalar($value) &&
            $value !== null &&
            !(is_array($value) && self::isJsonSafeArray($value))
        ) {
            throw new Exception('Invalid value, only scalar, null or JSON-serializable array values are allowed');
        }
        $this->data[$key] = $value;
        return ($save ? $this->save() : true);
    }

    /**
     * Check that an array contains only JSON-serializable data
     * (scalars, nulls and nested arrays of the same shape)
     *
     * @param mixed[] $value Array to check
     *
     * @return bool
     */
    protected static function isJsonSafeArray(array $value): bool
    {
        return json_encode($value) !== false && self::containsOnlyJsonValues($value);
    }

    /**
     * @param mixed[] $value Array to check
     *
     * @return bool
     */
    private static function containsOnlyJsonValues(array $value): bool
    {
        foreach ($value as $item) {
            if (is_array($item)) {
                if (!self::containsOnlyJsonValues($item)) {
                    return false;
                }
            } elseif (!is_scalar($item) && $item !== null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Set an integer value
     *
     * @param string $key   Option name
     * @param int    $value Option value
     * @param bool   $save  If true the entity is saved
     *
     * @return bool True on success
     */
    public function setValInt(string $key, int $value = 0, bool $save = false): bool
    {
        return $this->setVal($key, $value, $save);
    }

    /**
     * Set a string value
     *
     * @param string $key   Option name
     * @param string $value Option value
     * @param bool   $save  If true the entity is saved
     *
     * @return bool True on success
     */
    public function setValString(string $key, string $value = '', bool $save = false): bool
    {
        return $this->setVal($key, $value, $save);
    }

    /**
     * Set a boolean value
     *
     * @param string $key   Option name
     * @param bool   $value Option value
     * @param bool   $save  If true the entity is saved
     *
     * @return bool True on success
     */
    public function setValBool(string $key, bool $value = false, bool $save = false): bool
    {
        return $this->setVal($key, $value, $save);
    }

    /**
     * Set a float value
     *
     * @param string $key   Option name
     * @param float  $value Option value
     * @param bool   $save  If true the entity is saved
     *
     * @return bool True on success
     */
    public function setValFloat(string $key, float $value = 0.0, bool $save = false): bool
    {
        return $this->setVal($key, $value, $save);
    }

    /**
     * Set an array value
     *
     * @param string  $key   Option name
     * @param mixed[] $value Option value, JSON-serializable data only
     * @param bool    $save  If true the entity is saved
     *
     * @return bool True on success
     */
    public function setValArray(string $key, array $value = [], bool $save = false): bool
    {
        return $this->setVal($key, $value, $save);
    }

    /**
     * Value exists
     *
     * @param string $key Option name
     *
     * @return bool
     */
    public function valExists(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Delete option value
     *
     * @param string $key  Option name
     * @param bool   $save Save on DB
     *
     * @return bool
     */
    public function removeVal(string $key, bool $save = false): bool
    {
        if (!isset($this->data[$key])) {
            return true;
        }

        unset($this->data[$key]);
        return ($save ? $this->save() : true);
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return 'Dynamic_Entity';
    }

    /**
     * Get reset data to skip on user settings reset
     *
     * @return array<string>
     */
    public static function getResetDataToSkip(): array
    {
        return apply_filters('duplicator_dynamic_data_skip_reset', [self::PACKAGE_CHECK_TS_KEY]);
    }

    /**
     * Reset user settings
     *
     * @return bool True if success, otherwise false
     */
    public function resetUserSettings(): bool
    {
        $skipResetData = self::getResetDataToSkip();
        foreach ($this->data as $key => $value) {
            if (in_array($key, $skipResetData)) {
                continue;
            }
            $this->removeVal($key);
        }
        return $this->save();
    }

    /**
     * Get data to skip on export
     *
     * @return array<string>
     */
    public function getSkipDataExport(): array
    {
        return apply_filters('duplicator_dynamic_skip_data_export', [self::PACKAGE_CHECK_TS_KEY]);
    }

    /**
     * To export data
     *
     * @return array<string, mixed>
     */
    public function settingsExport(): array
    {
        $data           = JsonSerialize::serializeToData($this, JsonSerialize::JSON_SKIP_MAGIC_METHODS |  JsonSerialize::JSON_SKIP_CLASS_NAME);
        $skipDataExport = $this->getSkipDataExport();

        foreach ($data['data'] as $key => $value) {
            if (in_array($key, $skipDataExport)) {
                unset($data['data'][$key]);
            }
        }

        return $data;
    }

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport(array $data, string $dataVersion, array $extraData = []): bool
    {
        $skipProps      = [
            'id',
            'data',
        ];
        $skipDataExport = $this->getSkipDataExport();

        $reflect = new ReflectionClass(self::class);
        $props   = $reflect->getProperties();

        foreach ($props as $prop) {
            if (in_array($prop->getName(), $skipProps)) {
                continue;
            }
            if (!isset($data[$prop->getName()])) {
                continue;
            }
            if (PHP_VERSION_ID < 80100) {
                $prop->setAccessible(true);
            }
            $prop->setValue($this, $data[$prop->getName()]);
        }

        foreach ($data['data'] as $key => $value) {
            if (in_array($key, $skipDataExport)) {
                continue;
            }
            $this->data[$key] = $value;
        }

        return true;
    }
}
