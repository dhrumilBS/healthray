<?php

namespace Duplicator\Installer\Package;

class ArchiveDescriptor
{
    const SECURE_MODE_NONE        = 0;
    const SECURE_MODE_INST_PWD    = 1;
    const SECURE_MODE_ARC_ENCRYPT = 2;

    const GEN_FILE_REGEX_PATTERN   = '/^(.+_[a-z0-9]{7,}_[0-9]{14})_.+\.(?:zip|daf|php|bak)$/';
    const ARCHIVE_REGEX_PATTERN    = '/^(.+_[a-z0-9]{7,}_[0-9]{14})_archive\.(?:zip|daf)$/';
    const INSTALLER_REGEX_PATTERN  = '/^(?:.+_[a-z0-9]{7,}_[0-9]{14}_)?installer(?:-backup)?\.(php|php\.bak)$/';
    const NAME_PARTS_REGEX_PATTERN = '/^(.+)_([a-z0-9]{7,})_([0-9]{14})_.+\.(?:zip|daf|php|bak)$/';

    /** @var string */
    public $dup_type = '';
    /** @var string */
    public $created = '';
    /** @var string */
    public $version_dup = '';
    /** @var string */
    public $version_wp = '';
    /** @var string */
    public $version_db = '';
    /** @var string */
    public $version_php = '';
    /** @var string */
    public $version_os = '';
    /** @var string */
    public $blogname = '';
    /** @var bool */
    public $exportOnlyDB = false;
    /** @var int<0,2> */
    public $secure_on = self::SECURE_MODE_NONE;
    /** @var string */
    public $secure_pass = '';
    /** @var ?string */
    public $dbhost;
    /** @var ?string */
    public $dbname;
    /** @var ?string */
    public $dbuser;
    /** @var ?string */
    public $cpnl_host;
    /** @var ?string */
    public $cpnl_user;
    /** @var ?string */
    public $cpnl_pass;
    /** @var ?string */
    public $cpnl_enable;
    /** @var string */
    public $wp_tableprefix = '';
    /** @var int<0, 2> */
    public $mu_mode = 0;
    /** @var int<0, 2> */
    public $mu_generation = 0;
    /** @var string[] */
    public $mu_siteadmins = [];
    /** @var DescriptorSubsite[] */
    public $subsites = [];
    /** @var int */
    public $main_site_id = 1;
    /** @var bool */
    public $mu_is_filtered = false;
    /** @var int */
    public $license_type = 0;
    /** @var ?DescriptorDBInfo */
    public $dbInfo;
    /** @var ?DescriptorPackageInfo */
    public $packInfo;
    /** @var ?DescriptorFileInfo*/
    public $fileInfo;
    /** @var ?DescriptorWpInfo */
    public $wpInfo;
    /** @var int<-1,max> */
    public $defaultStorageId = -1;
    /** @var string[] */
    public $components = [];
    /** @var string[] Plugin basenames to force-activate after restoration. */
    public array $forceActivatePlugins = [];
    /** @var string[] */
    public $opts_delete = [];
    /** @var array<string, mixed> */
    public $header = [];
    /** @var array<string, mixed> */
    public $overwriteInstallerParams = [];
    /**
      * @var string
      *
      * @deprecated This option is no longer taken into account
      */
    public $installer_base_name = '';
    /** @var string */
    public $installer_backup_name = '';
    /** @var string */
    public $package_name = '';
    /** @var string */
    public $package_hash = '';
    /** @var string */
    public $package_notes = '';

    /**
     * Check if the file path is have a valid archive file name
     *
     * @param string $filePath File path, accept full path or just file name
     *
     * @return bool
     */
    public static function isArchiveFile($filePath)
    {
        if (empty($filePath)) {
            return false;
        }
        $filePath = basename($filePath);
        return preg_match(self::GEN_FILE_REGEX_PATTERN, $filePath) === 1;
    }

    /**
     * Get archive name parts, name, hash, date, package hash or false if name is invalid
     *
     * @param string $filePath Archive or installer name path, accept full path or just file name
     *
     * @return array{name:string,hash:string,date:string,packageHash:string}|false
     */
    public static function getArchiveNameParts($filePath)
    {
        if (empty($filePath)) {
            return false;
        }
        $filePath = basename($filePath);
        $matches  = [];
        if (preg_match(self::NAME_PARTS_REGEX_PATTERN, $filePath, $matches)) {
            return [
                'name'        => $matches[1],
                'hash'        => $matches[2],
                'date'        => $matches[3],
                'packageHash' => substr($matches[2], 0, 7) . '-' . substr($matches[3], -8),
            ];
        }
        return false;
    }
}
