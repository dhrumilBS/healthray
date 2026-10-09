<?php

namespace Duplicator\Utils\Settings;

interface ModelMigrateSettingsInterface
{
    /**
     * To export data
     *
     * @return array<string, mixed>
     */
    public function settingsExport(): array;

    /**
     * Update object properties from import data
     *
     * @param array<string, mixed> $data        data to import
     * @param string               $dataVersion version of data
     * @param array<string, mixed> $extraData   extra data, useful form id mapping etc.
     *
     * @return bool True if success, otherwise false
     */
    public function settingsImport(array $data, string $dataVersion, array $extraData = []): bool;
}
