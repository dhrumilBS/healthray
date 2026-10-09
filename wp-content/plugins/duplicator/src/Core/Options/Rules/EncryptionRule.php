<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Rules;

use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Package\Archive\PackageArchive;

/**
 * Archive encryption option. The needed requirement depends on the engine:
 * ZipArchive needs native zip encryption support (Libzip 1.2+), DupArchive
 * needs the PHP OpenSSL module, shell zip encrypts through the zip binary.
 *
 * Encryption has no global stored value: it is chosen per Backup/template
 * (installer secure mode + password). The rule takes part in the system for
 * availability evaluations only, so the current value is always the terminal
 * "off" and setValue() has nothing to persist.
 */
class EncryptionRule extends AbstractOptionRule
{
    const OPTION_KEY = 'archive_encryption';

    /**
     * Unique option key
     *
     * @return string
     */
    public function getKey(): string
    {
        return self::OPTION_KEY;
    }

    /**
     * Human readable option label, as named in the settings UI
     *
     * @return string
     */
    public function getLabel(): string
    {
        return __('Archive Encryption', 'duplicator');
    }

    /**
     * Human readable label of a single encryption value, as named in the settings UI
     *
     * @param scalar $value Encryption enabled flag
     *
     * @return string
     */
    public function getValueLabel($value): string
    {
        return $value ? __('Enabled', 'duplicator') : __('Disabled', 'duplicator');
    }

    /**
     * All the values the option can assume
     *
     * @return bool[]
     */
    public function getValues(): array
    {
        return [
            false,
            true,
        ];
    }

    /**
     * Encryption is opt-in: off is the default and the always-available fallback
     *
     * @return bool[]
     */
    public function getPreference(): array
    {
        return [
            false,
            true,
        ];
    }

    /**
     * Encryption depends on the selected archive engine
     *
     * @return string[]
     */
    public function getDependsOn(): array
    {
        return [ArchiveEngineRule::OPTION_KEY];
    }

    /**
     * Requirement ids needed by the given encryption value
     *
     * @param scalar                $value     Encryption enabled flag
     * @param array<string, scalar> $depValues Values of the declared dependencies (the archive engine)
     *
     * @return string[] Requirement ids
     */
    public function requirementsFor($value, array $depValues): array
    {
        if (!$value) {
            return [];
        }

        switch ($depValues[ArchiveEngineRule::OPTION_KEY]) {
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return [RequirementDefs::REQ_ZIPARCHIVE_ENCRYPTION];
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return [RequirementDefs::REQ_OPENSSL];
            default:
                return [];
        }
    }

    /**
     * Encryption has no global stored value (it is a per-Backup choice),
     * so the current value is always the terminal "off"
     *
     * @return bool
     */
    public function getCurrentValue()
    {
        return false;
    }

    /**
     * No global storage to persist: encryption is enabled per Backup/template
     *
     * @param scalar $value Encryption enabled flag
     *
     * @return void
     */
    public function setValue($value): void
    {
    }
}
