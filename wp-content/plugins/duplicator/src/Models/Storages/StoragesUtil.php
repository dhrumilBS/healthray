<?php

namespace Duplicator\Models\Storages;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\Storage\UploadInfo;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\Storages\Local\DefaultLocalStorage;
use Duplicator\Models\Storages\Local\LocalStorage;
use Duplicator\Package\AbstractPackage;
use Exception;

class StoragesUtil
{
    /** @var AbstractStorageEntity[] */
    protected static $storagesForCryptUpdate = [];

    /**
     * Get default local storage, if don't exists create it
     *
     * @return DefaultLocalStorage
     */
    public static function getDefaultStorage()
    {
        static $defaultStorage = null;

        if ($defaultStorage === null) {
            if (($storages = AbstractStorageEntity::getAll()) !== false) {
                foreach ($storages as $storage) {
                    if ($storage->getSType() !== DefaultLocalStorage::getSType()) {
                        continue;
                    }
                    /** @var DefaultLocalStorage */
                    $defaultStorage = $storage;
                    break;
                }
            }

            if (is_null($defaultStorage)) {
                $defaultStorage = new DefaultLocalStorage();
                $defaultStorage->save();
            }
        }

        return $defaultStorage;
    }

    /**
     * Get default local storage id
     *
     * @return int
     */
    public static function getDefaultStorageId(): int
    {
        return self::getDefaultStorage()->getId();
    }

    /**
     * Get default new storage
     *
     * @return LocalStorage
     */
    public static function getDefaultNewStorage(): LocalStorage
    {
        return new LocalStorage();
    }

    /**
     * Get HTML for the storage connection status icon.
     * Shows a green link icon when the storage is valid and ready to use,
     * a red link-slash icon with the reason as tooltip otherwise.
     *
     * @param AbstractStorageEntity $storage Storage entity
     *
     * @return string Escaped HTML <i> element
     */
    public static function getStatusIconHtml(AbstractStorageEntity $storage): string
    {
        $errorMsg = '';
        if ($storage->isValid($errorMsg)) {
            return sprintf(
                '<i class="fas fa-link storage-status-icon storage-status-valid" title="%s"></i>',
                esc_attr__('Ready to use', 'duplicator')
            );
        }

        return sprintf(
            '<i class="fas fa-link-slash storage-status-icon storage-status-invalid" title="%s"></i>',
            esc_attr($errorMsg)
        );
    }

    /**
     * Process the Backup
     *
     * @param AbstractPackage $package     The Backup to process
     * @param UploadInfo      $upload_info The upload info
     *
     * @return void
     */
    public static function processPackage(AbstractPackage $package, UploadInfo $upload_info): void
    {
        $package->active_storage_id = $upload_info->getStorageId();
        $storage                    = $upload_info->getStorage();

        DupLog::infoTrace('** ' . strtoupper($storage->getStypeName()) . " [Name: {$storage->getName()}] [ID: {$package->active_storage_id}] **");

        if (!$upload_info->isDownloadFromRemote()) {
            $storage->copyFromDefault($package, $upload_info);
        } else {
            $storage->copyToDefault($package, $upload_info);

            if (!$upload_info->hasCompleted(true) || $package->isCancelPending()) {
                return;
            }

            $defaultStorage = StoragesUtil::getDefaultStorage();
            $defaultStorage->purgeOldPackages([$package->Archive->getFileName()]);

            $defaultLocalUploadInfo                   = new UploadInfo($defaultStorage->getId());
            $defaultLocalUploadInfo->copied_installer = true;
            $defaultLocalUploadInfo->copied_archive   = true;

            foreach ($package->upload_infos as $k => $uploadInfo) {
                if ($uploadInfo->getStorageId() == $defaultStorage->getId()) {
                    $package->upload_infos[$k] = $defaultLocalUploadInfo;
                    $package->update();
                    return;
                }
            }

            //insert at beginning
            array_unshift($package->upload_infos, $defaultLocalUploadInfo);
            $package->update();
        }
    }

    /**
     * Sort storages with default first other by id
     *
     * @param AbstractStorageEntity $a Storage a
     * @param AbstractStorageEntity $b Storage b
     *
     * @return int
     */
    public static function sortDefaultFirst(AbstractStorageEntity $a, AbstractStorageEntity $b): int
    {
        if ($a->getId() == $b->getId()) {
            return 0;
        }
        if ($a->getSType() == DefaultLocalStorage::getSType()) {
            return -1;
        }
        if ($b->getSType() == DefaultLocalStorage::getSType()) {
            return 1;
        }
        return ($a->getId() < $b->getId()) ? -1 : 1;
    }

    /**
     * Sort storages by priority, type and id
     *
     * @param AbstractStorageEntity $a Storage a
     * @param AbstractStorageEntity $b Storage b
     *
     * @return int
     */
    public static function sortByPriority(AbstractStorageEntity $a, AbstractStorageEntity $b): int
    {
        $aPriority = $a->getPriority();
        $bPriority = $b->getPriority();

        if ($aPriority == $bPriority) {
            if ($a->getSType() == $b->getSType()) {
                return $a->getId() <=> $b->getId();
            } else {
                return ($a->getSType() < $b->getSType()) ? -1 : 1;
            }
        }

        return ($aPriority < $bPriority) ? -1 : 1;
    }

    /**
     * Get local storages paths
     *
     * @return string[]
     */
    public static function getLocalStoragesPaths()
    {
        static $paths = null;
        if (!is_null($paths)) {
            return $paths;
        }

        $paths = [];
        if (($storages = AbstractStorageEntity::getAll()) !== false) {
            foreach ($storages as $storage) {
                if (!$storage->isLocal()) {
                    continue;
                }
                $paths[] = $storage->getLocationString();
            }
        }
        return $paths;
    }

    /**
     * Is local storage child path
     *
     * @param string $path Path to check
     *
     * @return bool
     */
    public static function isLocalStorageChildPath($path): bool
    {
        foreach (self::getLocalStoragesPaths() as $storagePath) {
            if (SnapIO::isChildPath($path, $storagePath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Register all storages
     *
     * @return void
     */
    public static function registerTypes(): void
    {
        // add_action first so registerCoreStorageTypes runs on the same do_action below
        add_action('duplicator_register_storage_types', [self::class, 'registerCoreStorageTypes'], 5);
        do_action('duplicator_register_storage_types');
        add_action('duplicator_before_update_crypt_setting', [self::class, 'beforeCryptUpdateSettings']);
        add_action('duplicator_after_update_crypt_setting', [self::class, 'afterCryptUpdateSettings']);
    }

    /**
     * Register core storage subtypes on the duplicator_register_storage_types hook.
     *
     * @return void
     */
    public static function registerCoreStorageTypes(): void
    {
        UnknownStorage::registerStorageSubtype();
        LocalStorage::registerStorageSubtype();
        DefaultLocalStorage::registerStorageSubtype();
    }

    /**
     * Before crypt update settings
     *
     * @return void
     */
    public static function beforeCryptUpdateSettings(): void
    {
        self::$storagesForCryptUpdate = AbstractStorageEntity::getAll();
    }

    /**
     * After crypt update settings
     *
     * @return void
     */
    public static function afterCryptUpdateSettings(): void
    {
        foreach (self::$storagesForCryptUpdate as $storage) {
            $storage->save();
        }
        self::$storagesForCryptUpdate = [];
    }

    /** @var array<class-string<AbstractStorageEntity>, AbstractStorageEntity> Per-class cache for unique storages */
    private static array $uniqueStorageCache = [];

    /**
     * Get the unique storage instance for a given class, or a new unsaved template if none exists.
     *
     * @template T of AbstractStorageEntity
     *
     * @param class-string<T> $class Storage class name
     *
     * @return T
     *
     * @throws Exception If the class is not an AbstractStorageEntity subclass or not a unique storage type.
     */
    public static function getUniqueStorage(string $class): AbstractStorageEntity
    {
        if (!is_subclass_of($class, AbstractStorageEntity::class)) {
            throw new Exception('getUniqueStorage expects an AbstractStorageEntity subclass, got: ' . $class);
        }
        if (!$class::isUnique()) {
            throw new Exception('getUniqueStorage called on non-unique storage class: ' . $class);
        }
        if (!isset(self::$uniqueStorageCache[$class])) {
            $storages                         = AbstractStorageEntity::getAllBySType($class::getSType());
            self::$uniqueStorageCache[$class] = (is_array($storages) && count($storages) > 0)
                ? $storages[0]
                : new $class();
        }
        return self::$uniqueStorageCache[$class];
    }

    /**
     * Clear the unique storage cache.
     *
     * @return void
     */
    public static function clearUniqueStorageCache(): void
    {
        self::$uniqueStorageCache = [];
    }

    /**
     * Check if a storage type should be disabled in the create type selector.
     * Combines per-type properties (unique, supported, enabled) with system state queries.
     *
     * @param class-string<AbstractStorageEntity> $class  Storage class to check
     * @param string                              $reason Reference to store the reason why it's disabled
     *
     * @return bool True if disabled, false if selectable
     *
     * @throws Exception If the class is not an AbstractStorageEntity subclass.
     */
    public static function isSelectDisabled(string $class, string &$reason = ''): bool
    {
        if (!is_subclass_of($class, AbstractStorageEntity::class)) {
            throw new Exception('isSelectDisabled expects an AbstractStorageEntity subclass, got: ' . $class);
        }

        if ($class::isUnique()) {
            $storages = AbstractStorageEntity::getAllBySType($class::getSType());
            if (count($storages) > 0) {
                $reason = sprintf(
                    __('Only one %s storage can be created at a time', 'duplicator'),
                    $class::getStypeName()
                );
                return true;
            }
        }

        if (!$class::isSupported()) {
            $reason = $class::getNotSupportedNotice();
            return true;
        }

        if (!$class::isEnabled($reason)) {
            return true;
        }

        $reason = '';
        return false;
    }

    /**
     * Check if any storage type can be created.
     * Used by create button to decide visibility.
     *
     * @return bool
     */
    public static function hasCreatableTypes(): bool
    {
        foreach (AbstractStorageEntity::getResisteredTypesByPriority() as $type) {
            $class = AbstractStorageEntity::getSTypePHPClass($type);
            if (!$class::isSelectable()) {
                continue;
            }
            $reason = '';
            if (self::isSelectDisabled($class, $reason)) {
                continue;
            }
            return true;
        }
        return false;
    }

    /**
     * Initialize all createOnInstall storages.
     * Creates any missing createOnInstall storage types.
     * Called during plugin install/upgrade, after types are registered.
     *
     * @return void
     */
    public static function initCreateOnInstallStorages(): void
    {
        foreach (AbstractStorageEntity::getResisteredTypes() as $type) {
            $class = AbstractStorageEntity::getSTypePHPClass($type);
            if (!$class::isCreateOnInstall()) {
                continue;
            }

            $storages = AbstractStorageEntity::getAllBySType($type);
            if ($storages === false) {
                // Storage table not readable (e.g. install still in progress):
                // skip the creation, the next upgrade pass will retry.
                DupLog::trace("Unable to read storages of type {$type} on install, skipping creation");
                continue;
            }
            if (count($storages) > 0) {
                continue;
            }

            $storage = AbstractStorageEntity::getNewStorageByType($type);
            if ($storage->save() === false) {
                DupLog::trace("Error saving createOnInstall storage type {$type}");
            }
        }
    }

    /**
     * Render storages global options
     *
     * @return void
     */
    public static function renderGlobalOptions(): void
    {
        foreach (AbstractStorageEntity::getResisteredTypes() as $type) {
            $class = AbstractStorageEntity::getSTypePHPClass($type);
            if (!class_exists($class)) {
                continue;
            }
            call_user_func([$class, 'renderGlobalOptions']);
        }
    }

    /**
     * Removed double default storages
     *
     * @return int[] Ids of removed storages
     */
    public static function removeDoubleDefaultStorages()
    {
        global $wpdb;

        try {
            $doubleStorageIds = [];
            $defaultStorageId = self::getDefaultStorageId();
            foreach (AbstractStorageEntity::getAll() as $storage) {
                if (!$storage->isDefault() || $storage->getId() === $defaultStorageId) {
                    continue;
                }

                $doubleStorageIds[] = $storage->getId();
            }

            if (count($doubleStorageIds) > 0) {
                $query = "DELETE FROM " . AbstractStorageEntity::getTableName() . " WHERE id IN (" . implode(',', $doubleStorageIds) . ")";
                if ($wpdb->query($query) === false) {
                    throw new Exception("Error executing query to remove double default storages");
                }
            }
        } catch (Exception $e) {
            DupLog::trace("Error removing double default storages: " . $e->getMessage());
            return [];
        }

        return $doubleStorageIds;
    }


    /**
     * Build pre-processed storage rows for the select_list template.
     *
     * @param int[] $filteredStorageIds Storage IDs to exclude from the list
     * @param int[] $selectedStorageIds Storage IDs that should be pre-checked
     * @param int[] $disabledStorageIds Storage IDs whose checkbox is disabled (e.g. backup already there)
     *
     * @return array{storageRows: list<array<string, mixed>>, hasInvalidStorage: bool}
     */
    public static function buildSelectListData(
        array $filteredStorageIds = [],
        array $selectedStorageIds = [],
        array $disabledStorageIds = []
    ): array {
        $storageList       = AbstractStorageEntity::getAll(0, 0, [self::class, 'sortByPriority']);
        $hasInvalidStorage = false;
        $storageRows       = [];

        foreach ($storageList as $storage) {
            try {
                $invalidErrorMsg = __('Invalid storage configuration', 'duplicator');
                $isValid         = $storage->isValid($invalidErrorMsg);
                if ($storage->isSupported() && !$storage->isHidden() && !$isValid) {
                    $hasInvalidStorage = true;
                }

                if (
                    !$storage->isSupported()
                    || $storage->isHidden()
                    || in_array($storage->getId(), $filteredStorageIds)
                ) {
                    continue;
                }

                $storageId  = (int) $storage->getId();
                $isDisabled = in_array($storage->getId(), $disabledStorageIds);
                $isChecked  = in_array($storage->getId(), $selectedStorageIds) && $isValid && !$isDisabled;
                $rowClasses = [
                    'package-row',
                    'storage-row',
                ];
                if (!$isValid) {
                    $rowClasses[] = 'invalid';
                }

                $storageRows[] = [
                    'storage'    => $storage,
                    'id'         => $storageId,
                    'isValid'    => $isValid,
                    'invalidMsg' => $invalidErrorMsg,
                    'rowClasses' => $rowClasses,
                    'isChecked'  => $isChecked,
                    'isDisabled' => $isDisabled,
                    'isLocal'    => $storage->isLocal(),
                ];
            } catch (Exception $e) {
                $storageRows[] = [
                    'storage'    => null,
                    'id'         => 0,
                    'isValid'    => false,
                    'invalidMsg' => $e->getMessage(),
                    'rowClasses' => [
                        'package-row',
                        'storage-row',
                        'invalid',
                    ],
                    'isChecked'  => false,
                    'isLocal'    => false,
                    'isError'    => true,
                ];
            }
        }

        return [
            'storageRows'       => $storageRows,
            'hasInvalidStorage' => $hasInvalidStorage,
        ];
    }

    /**
     * Check storages ids
     *
     * @param int[] $ids   Array of storage IDs to check
     * @param bool  $all   If true the function return true only if all storages are valid
     * @param bool  $force If true force remote storages check
     *
     * @return bool True if schedule has at least one valid storage, false otherwise
     */
    public static function hasValidStorage(array $ids, $all = false, $force = false): bool
    {
        if (count($ids) == 0) {
            return false;
        }

        // Check each assigned storage
        foreach ($ids as $id) {
            $storage  = AbstractStorageEntity::getById($id);
            $errorMsg = '';
            if ($storage !== false && $storage->isSupported() && $storage->isValid($errorMsg, $force)) {
                if ($all == false) {
                    return true;
                }
            } else {
                if ($storage instanceof AbstractStorageEntity) {
                    DupLog::trace(
                        'STORAGE CHECK FAIL ON STORAGE ' . $storage->getName() .
                        ' ID: ' . $storage->getId() .  " TYPE:" . $storage->getStypeName()
                    );
                } else {
                    DupLog::trace('STORAGE CHECK UNKNOWN STORAGE ID: ' . $id);
                }
                if ($all == true) {
                    return false;
                }
            }
        }
        return ($all);
    }
}
