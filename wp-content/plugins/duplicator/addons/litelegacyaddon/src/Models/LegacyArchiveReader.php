<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteLegacyAddon\Models;

use Duplicator\Package\AbstractPackageDeployer;

/**
 * Read-only adapter for descriptor metadata embedded in legacy archives.
 */
final class LegacyArchiveReader extends AbstractPackageDeployer
{
    /**
     * Return normalized metadata when the embedded descriptor is readable.
     *
     * @return ?LegacyBackupMetadata
     */
    public function readMetadata(): ?LegacyBackupMetadata
    {
        if (!$this->isValid() || !is_object($this->info)) {
            return null;
        }

        return LegacyBackupMetadata::fromDescriptor($this->info);
    }

    /**
     * This reader never prepares an installer.
     *
     * @param array<string, array{value:mixed,formStatus?:string}> $baseParams Unused base parameters
     *
     * @return array<string, array{value:mixed,formStatus?:string}>
     */
    protected function getOverwriteParamsExtended(array $baseParams): array
    {
        return [];
    }
}
