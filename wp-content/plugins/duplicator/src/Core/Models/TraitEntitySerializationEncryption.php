<?php

namespace Duplicator\Core\Models;

use Duplicator\Libs\Snap\SnapLog;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Crypt\CryptBlowfish;
use Exception;
use Throwable;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

/**
 * Trait for standardized entity serialization with encryption support.
 *
 * Provides encryption/decryption infrastructure for entity properties.
 * Classes using this trait must declare their own $encryptedProperties array
 * to specify which properties need encryption.
 *
 * Example:
 * protected static array $encryptedProperties = ['password', 'apiKey'];
 *
 * Re-entrancy: code on the decrypt/unserialize path must use primitive logging
 * only (SnapLog::phpErr / error_log) and must not resolve singletons or fire
 * hooks, otherwise singleton entities can recurse during load.
 */
trait TraitEntitySerializationEncryption
{
    /** @var string[]   */
    protected array $decryptPropsErrors = [];

    /**
     * Encrypt properties declared in static::$encryptedProperties
     *
     * @param array<string,mixed> $data Data array to encrypt
     *
     * @return array<string,mixed> Data array with encrypted values and __encrypted list
     */
    protected function encryptSerializedProperties(array $data): array
    {
        $this->assertEncryptedPropertiesDefined();

        // Always add __encrypted marker to distinguish new format from legacy
        $encrypted = [];

        // Check if encryption is globally enabled
        if (!StaticGlobal::getCryptOption()) {
            $data['__encrypted'] = $encrypted;
            return $data;
        }

        foreach (static::$encryptedProperties as $property) {
            if (!isset($data[$property])) {
                continue;
            }

            $value = $data[$property];

            $value = JsonSerialize::serialize($value);
            if ($value === false) {
                continue; // Skip if JSON serialization fails
            }

            $encryptedValue = CryptBlowfish::encryptIfAvaiable($value, null, true);

            // Only mark as encrypted if encryption actually changed the value
            if (strlen($encryptedValue) > 0) {
                $data[$property] = $encryptedValue;
                $encrypted[]     = $property;
            }
        }

        // Always add __encrypted array (even if empty) to mark as new format
        $data['__encrypted'] = $encrypted;
        // Reset errors on new encryption
        $this->decryptPropsErrors = [];
        return $data;
    }


    /**
     * Return true if there is a decryption error
     *
     * @return bool
     */
    protected function isDecryptError(): bool
    {
        return count($this->decryptPropsErrors) > 0;
    }

    /**
     * Decrypt properties from serialized data.
     *
     * On failure a property is set to null; __unserialize() applies defaults for null values.
     *
     * @param array<string,mixed> $data Serialized data array
     *
     * @return array<string,mixed> Data array with decrypted properties
     */
    protected function decryptSerializedProperties(array $data): array
    {
        $this->decryptPropsErrors = [];
        $this->assertEncryptedPropertiesDefined();

        if (isset($data['__encrypted'])) {
            $data = $this->decryptTrackedProperties($data);
        } else {
            $data = $this->legacyDecryptProperties($data);
            foreach (static::$encryptedProperties as $property) {
                if (array_key_exists($property, $data) && $data[$property] === null) {
                    $this->decryptPropsErrors[] = $property;
                }
            }
        }

        return $data;
    }

    /**
     * Decrypt the properties named in the `__encrypted` marker.
     *
     * @param array<string,mixed> $data Serialized data carrying the `__encrypted` marker
     *
     * @return array<string,mixed>
     */
    private function decryptTrackedProperties(array $data): array
    {
        $encryptedList = $data['__encrypted'];
        unset($data['__encrypted']);

        foreach ($encryptedList as $property) {
            if (isset($data[$property])) {
                $data[$property] = $this->decryptStoredValue($property, $data[$property]);
            }
        }

        return $data;
    }

    /**
     * Decrypt one stored value, or null if it cannot be read.
     *
     * Primitive logging only — see the re-entrancy note in the trait docblock.
     *
     * @param string $property Property name, used in the error message
     * @param mixed  $value    Encrypted stored value
     *
     * @return mixed
     */
    private function decryptStoredValue(string $property, $value)
    {
        try {
            $decrypted = CryptBlowfish::decryptIfAvaiable($value, null, true);
            if (strlen($decrypted) === 0) {
                throw new Exception('Decrypt property failed');
            }

            $unserialized = JsonSerialize::unserialize($decrypted);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid json Error:' . json_last_error());
            }

            return $unserialized;
        } catch (Throwable $e) {
            SnapLog::phpErr('decrypt of a stored entity property failed, value reset');
            $this->decryptPropsErrors[] = $property;
            return null;
        }
    }

    /**
     * Entity override point for legacy format decryption.
     *
     * @param array<string,mixed> $data Serialized data array in legacy format
     *
     * @return array<string,mixed> Data array with legacy format decrypted
     */
    protected function legacyDecryptProperties(array $data): array
    {
        // Default implementation: no legacy encrypted properties
        return $data;
    }

    /**
     * Assert that $encryptedProperties is defined in the class using this trait.
     *
     * @return void
     *
     * @throws Exception If $encryptedProperties is not defined
     */
    private function assertEncryptedPropertiesDefined(): void
    {
        if (
            !property_exists(static::class, 'encryptedProperties') ||
            !is_array(static::$encryptedProperties) // @phpstan-ignore function.alreadyNarrowedType
        ) {
            throw new Exception(
                'Class ' . static::class . ' must define protected static array $encryptedProperties'
            );
        }
    }
}
