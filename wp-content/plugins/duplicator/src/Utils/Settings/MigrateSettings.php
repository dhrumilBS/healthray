<?php

declare(strict_types=1);

namespace Duplicator\Utils\Settings;

use Duplicator\Models\GlobalEntity;
use Duplicator\Models\StaticGlobal;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Models\TemplateEntity;
use Duplicator\Models\DynamicGlobalEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\Local\DefaultLocalStorage;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Utils\Crypt\CryptCustom;
use Exception;
use VendorDuplicator\Amk\JsonSerialize\JsonSerialize;

class MigrateSettings
{
    /** @var string Encryption key for settings export files (obfuscation only, not security) */
    public const EXPORT_ENCRYPTION_KEY = 'test';

    /**
     * Create settings export file
     *
     * @param string $message message to display to user
     *
     * @return false|string false if error, otherwise the export file path
     */
    public static function export(&$message = '')
    {
        $exportData = [
            'version'          => DUPLICATOR_VERSION,
            'settings'         => GlobalEntity::getInstance()->settingsExport(),
            'dynamic_settings' => DynamicGlobalEntity::getInstance()->settingsExport(),
            'static_settings'  => StaticGlobal::settingsExport(),
            'templates'        => [],
            'storages'         => [],
        ];

        if (($templates = TemplateEntity::getAllWithoutManualMode()) === false) {
            $templates = [];
        }
        foreach ($templates as $template) {
            $exportData['templates'][] = $template->settingsExport();
        }

        if (($storages = AbstractStorageEntity::getAll()) === false) {
            $storages = [];
        }
        foreach ($storages as $storage) {
            $exportData['storages'][] = $storage->settingsExport();
        }

        $exportData = apply_filters('duplicator_settings_export_data', $exportData);

        $jsonData = JsonSerialize::serialize(
            $exportData,
            JsonSerialize::JSON_SKIP_CLASS_NAME | JSON_PRETTY_PRINT
        );

        if ($jsonData === false) {
            // Isolate the problem area:
            $test                  = JsonSerialize::serialize($exportData['templates']);
            $test_templates        = ($test === false ? '*Fail' : 'Pass');
            $test                  = JsonSerialize::serialize($exportData['storages']);
            $test_storages         = ($test === false ? '*Fail' : 'Pass');
            $test                  = JsonSerialize::serialize($exportData['settings']);
            $test_settings         = ($test === false ? '*Fail' : 'Pass');
            $test                  = JsonSerialize::serialize($exportData['dynamic_settings']);
            $test_dynamic_settings = ($test === false ? '*Fail' : 'Pass');
            $test                  = JsonSerialize::serialize($exportData['static_settings']);
            $test_static_settings  = ($test === false ? '*Fail' : 'Pass');

            $exc_msg    = 'Isn\'t possible serialize json data';
            $div        = "******************************************";
            $pluginName = DUPLICATOR____NAME;
            $message    = <<<ERR
******************************************
{$pluginName} - EXPORT SETTINGS ERROR
******************************************
Error encoding json data for export status

Templates		 = {$test_templates}
Storage			 = {$test_storages}
Settings		 = {$test_settings}
Dynamic Settings = {$test_dynamic_settings}
Static Settings	 = {$test_static_settings}

RECOMMENDATION:
Check the data in the failed areas above to make sure the data is correct.  If the data looks correct consider re-saving the data in
that respective area.  If the problem persists consider removing the items one by one to isolate the setting that is causing the issue.

ERROR DETAILS:\n$exc_msg
ERR;
            DupLog::traceObject('There was an error encoding json data for export', $exportData);
            return false;
        }

        $encryptedData  = CryptCustom::encrypt($jsonData, self::EXPORT_ENCRYPTION_KEY);
        $exportFilepath = DUPLICATOR_SSDIR_PATH_TMP . '/dupli-export-' . date("Ymdhs") . '-' . uniqid() . '.dup';

        if (file_put_contents($exportFilepath, $encryptedData) === false) {
            DupLog::trace("Error writing export to {$exportFilepath}");
            return false;
        }

        $message = __("Export data file has been created!<br/>", 'duplicator');
        return $exportFilepath;
    }

    /**
     * Creates and export file of current settings and then
     * imports all the new settings from an existing import file
     *
     * @param string   $filename The name of the import file to import
     * @param string[] $opts     The options to import templates, schedules, storage, etc.
     * @param string   $message  message to display to user
     *
     *  @return bool true if success, otherwise false
     */
    public static function import($filename, array $opts, &$message = ''): bool
    {
        DupLog::trace('Start Import data options: ' . implode(',', $opts));

        // Generate backup of current settings
        $backupSettings = self::export();

        $filepath = $filename;
        if (!file_exists($filepath)) {
            throw new Exception("File {$filepath} does not exist");
        }

        $encrypted_data = file_get_contents($filepath);
        if ($encrypted_data === false) {
            throw new Exception("Error reading {$filepath}");
        }

        $json_data   = CryptCustom::decrypt($encrypted_data, self::EXPORT_ENCRYPTION_KEY);
        $import_data = JsonSerialize::unserialize($json_data);
        if (!is_array($import_data)) {
            throw new Exception('Problem decoding JSON data');
        }

        DupLog::traceObject('Import data', $import_data);

        $version = ($import_data['version'] ?? '0.0.0');

        /** @var string[] $opts Let addons enforce dependencies between import options */
        $opts = apply_filters('duplicator_settings_import_opts', $opts);
        DupLog::trace('Import data effective options: ' . implode(',', $opts));

        if (in_array('settings', $opts)) {
            self::importSettings($import_data, $version);
        }

        $template_map = in_array('templates', $opts) ? self::importTemplates($import_data, $version) : [];

        $storage_map = in_array('storages', $opts) ? self::importStorages($import_data, $version) : [];

        do_action('duplicator_settings_import_data', $import_data, $version, $storage_map, $template_map, $opts);

        $message  = __("All data has been successfully imported and updated! <br/>", 'duplicator');
        $message .= sprintf(__('Backup data file has been created here %s', 'duplicator'), $backupSettings) . '<br/>';

        return true;
    }

    /**
     * Import settings
     *
     * @param array<string,mixed> $import_data data to import
     * @param string              $version     version of data
     *
     * @return bool true if success, otherwise false
     */
    private static function importSettings(array $import_data, string $version): bool
    {
        if (!isset($import_data['settings'])) {
            return true;
        }
        DupLog::trace('Import data settings');

        $global = GlobalEntity::getInstance();
        $global->settingsImport($import_data['settings'], $version);

        if (isset($import_data['dynamic_settings'])) {
            $dGlobal = DynamicGlobalEntity::getInstance();
            $dGlobal->settingsImport($import_data['dynamic_settings'], $version);
            $dGlobal->save();
        }

        if (isset($import_data['static_settings'])) {
            StaticGlobal::settingsImport($import_data['static_settings']);
        }

        return $global->save();
    }

    /**
     * Import templates
     *
     * @param array<string,mixed> $import_data data to import
     * @param string              $version     version of data
     *
     * @return int[] return map from old ids and new
     */
    private static function importTemplates(array $import_data, string $version): array
    {
        $map = [];

        if (!isset($import_data['templates']) || !is_array($import_data['templates'])) {
            return $map;
        }

        foreach ($import_data['templates'] as $data) {
            $template = new TemplateEntity();
            $template->settingsImport($data, $version);

            if ($template->is_default) {
                // Don't save default template
                continue;
            }

            if ($template->save() === false) {
                DupLog::traceObject('Error saving template so skip', $template);
                continue;
            }
            $map[$data['id']] = $template->getId();
        }
        return $map;
    }

    /**
     * Import storages
     *
     * @param array<string,mixed> $import_data data to import
     * @param string              $version     version of data
     *
     * @return int[] return map from old ids and new
     */
    private static function importStorages(array $import_data, string $version): array
    {
        $map = [
            DefaultLocalStorage::OLD_VIRTUAL_STORAGE_ID => StoragesUtil::getDefaultStorageId(),
        ];

        if (!isset($import_data['storages']) || !is_array($import_data['storages'])) {
            return $map;
        }

        foreach ($import_data['storages'] as $data) {
            $class = AbstractStorageEntity::getSTypePHPClass($data);
            /** @var AbstractStorageEntity */
            $storage = new $class();
            $storage->settingsImport($data, $version);

            if ($storage->isDefault()) {
                // Don't create new default storage, update existing
                $storage = StoragesUtil::getDefaultStorage();
                $storage->settingsImport($data, $version);
            } elseif ($class::isUnique()) {
                // Check if unique storage of this type already exists
                $existingStorages = AbstractStorageEntity::getAllBySType($class::getSType());
                if (is_array($existingStorages) && count($existingStorages) > 0) {
                    // Use existing unique storage instead of creating new
                    $storage = $existingStorages[0];
                    $storage->settingsImport($data, $version);
                }
            }

            if ($storage->save() === false) {
                DupLog::traceObject('Error saving storage so skip', $storage);
                continue;
            }
            $map[$data['id']] = $storage->getId();
        }
        return $map;
    }
}
