<?php

namespace Duplicator\Package;

use Duplicator\Core\Exceptions\DupliException;
use Duplicator\Utils\Logging\DupLog;
use Duplicator\Package\DupPackage;
use Duplicator\Models\TemplateEntity;
use Duplicator\Models\Storages\AbstractStorageEntity;
use Duplicator\Models\Storages\StoragesUtil;
use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use ReflectionObject;

class TemporaryPackageUtils
{
    /**
     * Set package as temporary using reflection
     *
     * @param AbstractPackage $package Package to set as temporary
     *
     * @return void
     */
    protected static function setTemporaryFlag(AbstractPackage $package): void
    {
        $reflect       = new ReflectionObject($package);
        $flagsProperty = $reflect->getProperty('flags');
        if (PHP_VERSION_ID < 80100) {
            $flagsProperty->setAccessible(true);
        }
        $flags = $flagsProperty->getValue($package);
        if (!in_array(AbstractPackage::FLAG_TEMPORARY, $flags)) {
            $flags[] = AbstractPackage::FLAG_TEMPORARY;
            $flagsProperty->setValue($package, $flags);
        }
    }

    /**
     * Remove temporary flag using reflection
     *
     * @param AbstractPackage $package Package to remove temporary flag from
     *
     * @return void
     */
    protected static function removeTemporaryFlag(AbstractPackage $package): void
    {
        $reflect       = new ReflectionObject($package);
        $flagsProperty = $reflect->getProperty('flags');
        if (PHP_VERSION_ID < 80100) {
            $flagsProperty->setAccessible(true);
        }
        $flags = $flagsProperty->getValue($package);
        $flags = array_values(array_diff($flags, [AbstractPackage::FLAG_TEMPORARY]));
        $flagsProperty->setValue($package, $flags);
    }

    /**
     * Create temporary package from class name
     *
     * @param int   $templateId Template ID, if -1 use manual template
     * @param int[] $storageIds Array of storage IDs
     *
     * @return DupPackage
     */
    public static function createTemporaryPackage($templateId = -1, $storageIds = []): DupPackage
    {
        if ($templateId === -1) {
            $template = TemplateEntity::getManualTemplate();
        } else {
            if (($template = TemplateEntity::getById($templateId)) === null) {
                throw new InvalidArgumentException("Template with id {$templateId} not found");
            }
        }

        if (count($storageIds) === 0) {
            $storageIds = [StoragesUtil::getDefaultStorageId()];
        }

        foreach ($storageIds as $storageId) {
            $storage = AbstractStorageEntity::getById($storageId);
            if ($storage === false) {
                throw new InvalidArgumentException("Storage with id {$storageId} not found");
            }
        }

        self::deleteTemporaryPackage();
        $package = new DupPackage($storageIds, $template);
        self::setTemporaryFlag($package);

        if ($package->save() === false) {
            throw new RuntimeException('Failed to save temporary package');
        }
        DupLog::trace('CREATE TEMPORARY PACKAGE: Saved temporary package; id: ' . $package->getID());

        return $package;
    }

    /**
     *  Remove all temporary packages
     *
     * @return void
     */
    public static function deleteTemporaryPackage(): void
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        $table = AbstractPackage::getTableName();
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM `{$table}` WHERE FIND_IN_SET(%s, flags)",
                AbstractPackage::FLAG_TEMPORARY
            )
        );
    }

    /**
     * Returns the stored temporary package, or null if none exists.
     *
     * @return ?DupPackage
     */
    public static function findTemporaryPackage(): ?DupPackage
    {
        $where = 'FIND_IN_SET(\'' . AbstractPackage::FLAG_TEMPORARY . '\', `flags`)';
        $ids   = DupPackage::dbSelect(
            $where,
            1,
            0,
            '',
            'ids',
            [
                DupPackage::getType(),
            ],
            true
        );

        if (count($ids) == 1 && ($package = DupPackage::getById($ids[0])) !== false) {
            return $package;
        }

        return null;
    }

    /**
     * Gets the temporary package. The temporary package is defined as the first package with FLAG_TEMPORARY flag.
     *
     * @return DupPackage Return temporary package
     */
    public static function getTemporaryPackage(): DupPackage
    {
        $package = self::findTemporaryPackage();

        if ($package === null) {
            DupLog::trace('GET TEMPORARY PACKAGE: Creating new temporary package because no package was found');
            $package = new DupPackage();
            self::setTemporaryFlag($package);
        }

        return $package;
    }

    /**
     * Create a new package from the temporary package.
     *
     * The temporary package is consumed: it is deleted after the clone is saved,
     * so a second submission (double click, stale browser tab) cannot create
     * another package sharing the same name/hash and colliding on the same
     * build artifact files.
     *
     * @return DupPackage
     */
    public static function createPackageFromTemporaryPackage(): DupPackage
    {
        $tmpPackage = self::findTemporaryPackage();
        if ($tmpPackage === null) {
            throw new DupliException(
                'No temporary package found, the build confirm may have already consumed it',
                DupliException::CODE_ERROR,
                __(
                    'No backup ready to start was found. The backup may have already been started, check the backups list.',
                    'duplicator'
                )
            );
        }
        $package = clone $tmpPackage;

        // Set ID using reflection
        $reflection = new ReflectionClass($package);
        $idProperty = $reflection->getProperty('ID');
        if (PHP_VERSION_ID < 80100) {
            $idProperty->setAccessible(true);
        }
        $idProperty->setValue($package, -1);

        // The clone inherits the scan-time timer: restart it so the time spent
        // reviewing the scan results is not counted by the stuck watchdog
        $package->restartStateTimer($package->getStatus());

        self::removeTemporaryFlag($package);
        if (!$package->save()) {
            throw new RuntimeException('Failed to save new package from temporary package');
        }
        self::deleteTemporaryPackage();
        return $package;
    }
}
