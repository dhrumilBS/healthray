<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Rules;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;
use Exception;

/**
 * Archive engine option: which engine builds the Backup archive.
 */
class ArchiveEngineRule extends AbstractOptionRule
{
    const OPTION_KEY = 'archive_engine';

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
        return __('Archive Engine', 'duplicator');
    }

    /**
     * Human readable label of a single engine value, as named in the settings UI
     *
     * @param scalar $value Engine value, enum PackageArchive::BUILD_MODE_*
     *
     * @return string
     */
    public function getValueLabel($value): string
    {
        switch ($value) {
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return __('DupArchive', 'duplicator');
            case PackageArchive::BUILD_MODE_SHELL_EXEC:
                return __('Shell Zip', 'duplicator');
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return __('ZipArchive', 'duplicator');
            default:
                return parent::getValueLabel($value);
        }
    }

    /**
     * @return int[] enum PackageArchive::BUILD_MODE_*
     */
    public function getValues(): array
    {
        return [
            PackageArchive::BUILD_MODE_DUP_ARCHIVE,
            PackageArchive::BUILD_MODE_SHELL_EXEC,
            PackageArchive::BUILD_MODE_ZIP_ARCHIVE,
        ];
    }

    /**
     * Prefer DupArchive for defaults and corrections, with ZIP engines as fallbacks.
     *
     * @return int[] enum PackageArchive::BUILD_MODE_*
     */
    public function getPreference(): array
    {
        return [
            PackageArchive::BUILD_MODE_DUP_ARCHIVE,
            PackageArchive::BUILD_MODE_SHELL_EXEC,
            PackageArchive::BUILD_MODE_ZIP_ARCHIVE,
        ];
    }

    /**
     * Requirement ids needed by the given engine
     *
     * @param scalar                $value     Engine value, enum PackageArchive::BUILD_MODE_*
     * @param array<string, scalar> $depValues Values of the declared dependencies (none for this rule)
     *
     * @return string[] Requirement ids
     */
    public function requirementsFor($value, array $depValues): array
    {
        switch ($value) {
            case PackageArchive::BUILD_MODE_DUP_ARCHIVE:
                return [];
            case PackageArchive::BUILD_MODE_SHELL_EXEC:
                return [RequirementDefs::REQ_SHELL_ZIP_BINARY];
            case PackageArchive::BUILD_MODE_ZIP_ARCHIVE:
                return [RequirementDefs::REQ_ZIPARCHIVE_EXT];
            default:
                throw new DupliException(
                    "Invalid archive engine value.\nValue: " . var_export($value, true),
                    DupliException::CODE_OPTIONS_INVALID_CONFIGURATION,
                    __('The selected archive engine is invalid. Check the backup settings and try again.', 'duplicator')
                );
        }
    }

    /**
     * @return int enum PackageArchive::BUILD_MODE_*
     */
    public function getCurrentValue()
    {
        return GlobalEntity::getInstance()->getBuildMode();
    }

    /**
     * Store and persist the engine.
     *
     * @param scalar $value Engine value, enum PackageArchive::BUILD_MODE_*
     *
     * @return void
     */
    public function setValue($value): void
    {
        $global = GlobalEntity::getInstance();
        if ($global->getBuildMode() === (int) $value) {
            return;
        }
        if (!$global->setBuildMode((int) $value)) {
            throw new Exception('Unable to save the archive engine.');
        }
    }
}
