<?php

declare(strict_types=1);

namespace Duplicator\Core\Options\Rules;

use Duplicator\Core\Options\Requirements\RequirementDefs;
use Duplicator\Models\GlobalEntity;
use Duplicator\Package\Archive\PackageArchive;

/**
 * Archive compression option. Only DupArchive needs the PHP zlib extension to
 * compress: the zip engines carry their own compression support.
 */
class CompressionRule extends AbstractOptionRule
{
    const OPTION_KEY = GlobalEntity::ARCHIVE_COMPRESSION_KEY;

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
        return __('Archive Compression', 'duplicator');
    }

    /**
     * Human readable label of a single compression value, as named in the settings UI
     *
     * @param scalar $value Compression enabled flag
     *
     * @return string
     */
    public function getValueLabel($value): string
    {
        return $value ? __('On', 'duplicator') : __('Off', 'duplicator');
    }

    /**
     * All the values the option can assume
     *
     * @return bool[]
     */
    public function getValues(): array
    {
        return [
            true,
            false,
        ];
    }

    /**
     * Compression on is the preferred default, off is the always-available fallback
     *
     * @return bool[]
     */
    public function getPreference(): array
    {
        return [
            true,
            false,
        ];
    }

    /**
     * Compression depends on the selected archive engine
     *
     * @return string[]
     */
    public function getDependsOn(): array
    {
        return [ArchiveEngineRule::OPTION_KEY];
    }

    /**
     * Requirement ids needed by the given compression value
     *
     * @param scalar                $value     Compression enabled flag
     * @param array<string, scalar> $depValues Values of the declared dependencies (the archive engine)
     *
     * @return string[] Requirement ids
     */
    public function requirementsFor($value, array $depValues): array
    {
        if (!$value) {
            return [];
        }

        if ($depValues[ArchiveEngineRule::OPTION_KEY] === PackageArchive::BUILD_MODE_DUP_ARCHIVE) {
            return [RequirementDefs::REQ_ZLIB];
        }

        return [];
    }

    /**
     * The currently stored compression flag
     *
     * @return bool
     */
    public function getCurrentValue()
    {
        return GlobalEntity::getInstance()->isArchiveCompressionEnabled();
    }

    /**
     * Store and persist the compression flag
     *
     * @param scalar $value Compression enabled flag
     *
     * @return void
     */
    public function setValue($value): void
    {
        $global = GlobalEntity::getInstance();
        if ($global->isArchiveCompressionEnabled() === (bool) $value) {
            return;
        }
        $global->setArchiveCompression((bool) $value);
    }
}
