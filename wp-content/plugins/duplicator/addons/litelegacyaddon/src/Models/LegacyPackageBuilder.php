<?php

declare(strict_types=1);

namespace Duplicator\Addons\LiteLegacyAddon\Models;

use DateTimeImmutable;
use DateTimeZone;
use Duplicator\Installer\Package\ArchiveDescriptor;
use Duplicator\Installer\Package\DescriptorDBTableInfo;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\AbstractPackage;
use Duplicator\Package\Archive\PackageArchive;
use Duplicator\Package\Create\BuildComponents;
use Duplicator\Package\Create\PackInstaller;
use Duplicator\Package\Database\DatabaseInfo;
use Duplicator\Package\Database\DatabasePkg;
use Duplicator\Package\DupPackage;
use Duplicator\Package\PackageExecutionInfo;
use Exception;

/**
 * Builds standard package records for moved legacy Lite archives.
 */
final class LegacyPackageBuilder extends DupPackage
{
    const EXECUTION_SOURCE = 'Duplicator Lite';
    const EXECUTION_REASON = 'Migrated from standalone Duplicator Lite';

    /**
     * Build a complete minimal record from a generated archive filename.
     *
     * @param string $archivePath Current archive path
     *
     * @return self
     * @throws Exception When the archive filename or size is invalid
     */
    public static function fromArchive(string $archivePath): self
    {
        $parts = ArchiveDescriptor::getArchiveNameParts($archivePath);
        if ($parts === false) {
            throw new Exception('Invalid legacy archive filename: ' . basename($archivePath));
        }

        $created = DateTimeImmutable::createFromFormat(
            '!' . AbstractPackage::PACKAGE_HASH_DATE_FORMAT,
            $parts['date'],
            new DateTimeZone('UTC')
        );
        $size    = SnapIO::filesize($archivePath);
        if ($created === false || $size < 0) {
            throw new Exception('Invalid legacy archive metadata: ' . basename($archivePath));
        }

        $storageId = StoragesUtil::getDefaultStorageId();
        $package   = new self(
            [$storageId],
            null,
            new PackageExecutionInfo(
                AbstractPackage::EXECUTION_TYPE_MANUAL,
                self::EXECUTION_SOURCE,
                self::EXECUTION_REASON
            )
        );

        $package->name       = $parts['name'];
        $package->hash       = $parts['hash'] . '_' . $parts['date'];
        $package->created    = $created->format('Y-m-d H:i:s');
        $package->status     = AbstractPackage::STATUS_COMPLETE;
        $package->VersionWP  = '';
        $package->VersionDB  = '';
        $package->VersionPHP = '';
        $package->VersionOS  = '';
        $package->notes      = '';
        $package->components = [];
        $package->StorePath  = DUPLICATOR_SSDIR_PATH;
        $package->StoreURL   = DUPLICATOR_SSDIR_URL . '/';

        $package->Archive = new PackageArchive($package);
        $package->Archive->setFormatFromEngine(
            strtolower(pathinfo($archivePath, PATHINFO_EXTENSION)) === 'daf'
                ? PackageArchive::BUILD_MODE_DUP_ARCHIVE
                : PackageArchive::BUILD_MODE_ZIP_ARCHIVE
        );
        $package->Archive->Size      = $size;
        $package->Database           = new DatabasePkg($package);
        $package->Database->info     = new DatabaseInfo();
        $package->Database->Comments = '';
        $package->Installer          = new PackInstaller($package);
        $package->active_storage_id  = $storageId;

        foreach ($package->upload_infos as $uploadInfo) {
            $uploadInfo->copied_archive   = true;
            $uploadInfo->copied_installer = true;
            $uploadInfo->progress         = 100;
        }

        $package->addFlag(AbstractPackage::FLAG_NON_DEPLOYABLE);

        return $package;
    }

    /**
     * Enrich the minimal record with normalized descriptor metadata.
     *
     * @param LegacyBackupMetadata $metadata Descriptor metadata
     *
     * @return void
     */
    public function enrich(LegacyBackupMetadata $metadata): void
    {
        $this->notes      = $metadata->notes;
        $this->VersionWP  = $metadata->versionWp;
        $this->VersionDB  = $metadata->versionDb;
        $this->VersionPHP = $metadata->versionPhp;
        $this->VersionOS  = $metadata->versionOs;

        if ($metadata->components !== []) {
            $this->components = $metadata->components;
        } elseif ($metadata->databaseOnly) {
            $this->components = [BuildComponents::COMP_DB];
        }

        $this->Archive->ExportOnlyDB = $metadata->databaseOnly;
        $this->Archive->DirCount     = $metadata->directoryCount;
        $this->Archive->FileCount    = $metadata->fileCount;
        $this->Archive->file_count   = $metadata->fileCount;
        $this->Archive->scanSize     = $metadata->uncompressedSize;

        $this->applyDatabaseMetadata($metadata);

        $descriptorCreated = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $metadata->created,
            new DateTimeZone('UTC')
        );
        if ($descriptorCreated !== false) {
            $this->created = $descriptorCreated->format('Y-m-d H:i:s');
        }
    }

    /**
     * @param LegacyBackupMetadata $metadata Descriptor metadata
     *
     * @return void
     */
    private function applyDatabaseMetadata(LegacyBackupMetadata $metadata): void
    {
        $info = new DatabaseInfo();
        foreach ($metadata->database as $property => $value) {
            if (property_exists($info, $property)) {
                $info->{$property} = $value;
            }
        }
        foreach ($metadata->tables as $name => $table) {
            $info->tablesList[$name] = new DescriptorDBTableInfo(
                $table['inaccurateRows'],
                $table['size'],
                $table['insertedRows']
            );
        }

        $this->Database->info = $info;
        $this->Database->Size = (int) ($metadata->database['tablesSizeOnDisk'] ?? 0);
    }
}
