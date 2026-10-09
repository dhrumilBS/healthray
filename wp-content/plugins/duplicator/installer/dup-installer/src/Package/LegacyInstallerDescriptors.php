<?php

namespace Duplicator\Installer\Package;

/**
 * This class is used to manage old installer files previously version 4.5.20
 */
class LegacyInstallerDescriptors extends InstallerDescriptors
{
    /**
     * Init descriptors mapping
     *
     * @return void
     */
    protected function initMapping()
    {
        $this->mapping = [
            self::TYPE_ORIG_FILES            => 'orig_files__' . $this->hash,
            self::TYPE_ARCHIVE_CONFIG        => 'dup-archive__' . $this->hash . '.txt',
            self::TYPE_SCAN                  => 'dup-scan__' . $this->hash . '.json',
            self::TYPE_FILE_LIST             => 'dup-scanned-files__' . $this->hash . '.txt',
            self::TYPE_DIR_LIST              => 'dup-scanned-dirs__' . $this->hash . '.txt',
            self::TYPE_MANUAL_EXTRACT        => 'dup-manual-extract__' . $this->hash,
            self::TYPE_DB_DUMP               => 'dup-database__' . $this->hash . '.sql',
            self::TYPE_DB_DUMP_GZ            => 'dup-database__' . $this->hash . '.sql.gz',
            self::TYPE_INST_CHUNK_DATA       => 'dup-installer-chunk__' . $this->hash . '.json',
            self::TYPE_INST_NOTICES          => 'dup-installer-notices__' . $this->hash . '.json',
            self::TYPE_INST_DB_DATA          => 'dup-installer-dbinstall__' . $this->hash . '.json',
            self::TYPE_INST_DB_SEEK_TELL_LOG => 'dup-database-seek-tell-log__' . $this->hash . '.txt',
            self::TYPE_INST_EXTRACTION_DATA  => 'dup-installer-extraction__' . $this->hash . '.json',
            self::TYPE_INST_S3_DATA          => 'dup-installer-s3data__' . $this->hash . '.json',
            self::TYPE_INST_PHP_ERROR_LOG    => 'php_error__' . $this->hash . '.log',
            self::TYPE_PARAMS                => 'dup-params__' . $this->hash . '.json',
        ];
    }

    /**
     * Returns the folder in which all descriptors are stored
     *
     * @return string
     */
    public function getDescriptorsFolder(): string
    {
        return '';
    }
}
