<?php

namespace Duplicator\Installer\Package;

class InstallerDescriptors
{
    const FIRST_VERSION_NEW_ARCHIVE_FILES = '4.5.20';
    const DESCRIPTORS_FOLDER_PREFIX       = 'dup_descriptors_';

    const TYPE_ORIG_FILES            = 'orig_files';
    const TYPE_ARCHIVE_CONFIG        = 'archive_config';
    const TYPE_SCAN                  = 'scan';
    const TYPE_INDEX                 = 'file_index';
    const TYPE_FILE_LIST             = 'file_list';
    const TYPE_DIR_LIST              = 'dir_list';
    const TYPE_MANUAL_EXTRACT        = 'manual_extract';
    const TYPE_DB_DUMP               = 'db_dump';
    const TYPE_DB_DUMP_GZ            = 'db_dump_gz';
    const TYPE_PARAMS                = 'params';
    const TYPE_INST_CHUNK_DATA       = 'inst_chunk_data';
    const TYPE_INST_NOTICES          = 'inst_notices';
    const TYPE_INST_DB_DATA          = 'inst_db_data';
    const TYPE_INST_DB_SEEK_TELL_LOG = 'inst_db_seek_log';
    const TYPE_INST_EXTRACTION_DATA  = 'inst_extraction_data';
    const TYPE_INST_DB_GUNZIP_DATA   = 'inst_db_gunzip_data';
    const TYPE_INST_S3_DATA          = 'inst_s3_data';
    const TYPE_INST_PHP_ERROR_LOG    = 'inst_php_error_log';

    /** @var string The backup hash */
    protected $hash = '';

    /** @var string The backup date in YmdHis format */
    protected $date = '';

    /** @var array<string,string> Names mapping*/
    protected $mapping = [];

    /**
     * Constructor
     *
     * @param string $hash The backup hash
     * @param string $date The backup date in YmdHis format
     */
    public function __construct($hash, $date)
    {
        if (empty($hash)) {
            throw new \Exception('The hash is required');
        }

        if (empty($date)) {
            throw new \Exception('The date is required');
        }

        $this->hash = $hash;
        $this->date = $date;
        $this->initMapping();
    }

    /**
     * Returns the folder in which all descriptors are stored
     *
     * @return string
     */
    public function getDescriptorsFolder(): string
    {
        return self::DESCRIPTORS_FOLDER_PREFIX . $this->hash . '/';
    }

    /**
     * Init descriptors mapping
     *
     * @return void
     */
    protected function initMapping()
    {
        $this->mapping = [
            self::TYPE_ORIG_FILES            => 'orig_files',
            self::TYPE_ARCHIVE_CONFIG        => 'archive.txt',
            self::TYPE_SCAN                  => 'scan.json',
            self::TYPE_FILE_LIST             => 'scanned-files.txt',
            self::TYPE_DIR_LIST              => 'scanned-dirs.txt',
            self::TYPE_INDEX                 => 'index.txt',
            self::TYPE_MANUAL_EXTRACT        => 'manual-extract',
            self::TYPE_DB_DUMP               => 'db_dumps/' . $this->date . '-dump.sql',
            self::TYPE_DB_DUMP_GZ            => 'db_dumps/' . $this->date . '-dump.sql.gz',
            self::TYPE_INST_CHUNK_DATA       => 'installer-chunk-data.json',
            self::TYPE_INST_NOTICES          => 'installer-notices.json',
            self::TYPE_INST_DB_DATA          => 'installer-db-data.json',
            self::TYPE_INST_DB_SEEK_TELL_LOG => 'installer-db-seek-tell-log.txt',
            self::TYPE_INST_EXTRACTION_DATA  => 'installer-extraction.json',
            self::TYPE_INST_DB_GUNZIP_DATA   => 'installer-db-gunzip.json',
            self::TYPE_INST_S3_DATA          => 'installer-s3data.json',
            self::TYPE_INST_PHP_ERROR_LOG    => 'php_error.log',
            self::TYPE_PARAMS                => 'params.json',
        ];
    }



    /**
     * Get the file name of the descriptor file with hash
     *
     * @param string $descriptorType The archive file type
     *
     * @return string
     */
    public function getName($descriptorType): string
    {
        if (!isset($this->mapping[$descriptorType])) {
            throw new \Exception('Invalid descriptor type ' . $descriptorType);
        }

        return $this->getDescriptorsFolder() . $this->mapping[$descriptorType];
    }

    /**
     * Get the file name of the descriptor file without hash or date
     *
     * @param string $descriptorType The archive file type
     *
     * @return string
     */
    public function getGenericName($descriptorType): string
    {
        if (!isset($this->mapping[$descriptorType])) {
            throw new \Exception('Invalid descriptor type ' . $descriptorType);
        }

        return str_replace(
            [
                $this->hash,
                $this->date,
            ],
            [
                '[HASH]',
                '[DATE]',
            ],
            $this->getDescriptorsFolder() . $this->mapping[$descriptorType]
        );
    }

    /**
     * Get the file name of the descriptor file with hash
     *
     * @param string $descriptorType The archive file type
     * @param string $hash           (Optional) The hash of the archive
     *
     * @return string
     */
    public function getOldName($descriptorType, $hash = ''): string
    {
        if (empty($hash)) {
            $hash = $this->hash;
        }

        return self::getHashedOldName($descriptorType, $hash);
    }

    /**
     * Get the location of the old archive files
     *
     * @param string $descriptorType The descriptor type
     * @param string $hash           The hash of the archive
     *
     * @return string
     */
    protected static function getHashedOldName($descriptorType, $hash): string
    {
        $mapping = [
            self::TYPE_ORIG_FILES            => 'orig_files__' . $hash,
            self::TYPE_ARCHIVE_CONFIG        => 'dup-archive__' . $hash . '.txt',
            self::TYPE_SCAN                  => 'dup-scan__' . $hash . '.json',
            self::TYPE_FILE_LIST             => 'dup-scanned-files__' . $hash . '.txt',
            self::TYPE_DIR_LIST              => 'dup-scanned-dirs__' . $hash . '.txt',
            self::TYPE_MANUAL_EXTRACT        => 'dup-manual-extract__' . $hash,
            self::TYPE_DB_DUMP               => 'dup-database__' . $hash . '.sql',
            self::TYPE_DB_DUMP_GZ            => 'dup-database__' . $hash . '.sql.gz',
            self::TYPE_INST_CHUNK_DATA       => 'dup-installer-chunk__' . $hash . '.json',
            self::TYPE_INST_NOTICES          => 'dup-installer-notices__' . $hash . '.json',
            self::TYPE_INST_DB_DATA          => 'dup-installer-dbinstall__' . $hash . '.json',
            self::TYPE_INST_DB_SEEK_TELL_LOG => 'dup-database-seek-tell-log__' . $hash . '.txt',
            self::TYPE_INST_EXTRACTION_DATA  => 'dup-installer-extraction__' . $hash . '.json',
            self::TYPE_INST_S3_DATA          => 'dup-installer-s3data__' . $hash . '.json',
            self::TYPE_INST_PHP_ERROR_LOG    => 'php_error__' . $hash . '.log',
            self::TYPE_PARAMS                => 'dup-params__' . $hash . '.json',
        ];

        if (!isset($mapping[$descriptorType])) {
            throw new \Exception('Invalid descriptor type');
        }

        return $mapping[$descriptorType];
    }
}
