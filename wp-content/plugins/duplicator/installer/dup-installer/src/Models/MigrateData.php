<?php

namespace Duplicator\Installer\Models;

use Duplicator\Installer\Core\InstState;

class MigrateData
{
    /** @var string */
    public $plugin = 'dup-pro';
    /** @var string */
    public $installerVersion = '';
    /** @var int */
    public $installType = InstState::TYPE_NOT_SET;
    /** @var string */
    public $installTime = '';
    /** @var string[] */
    public $logicModes = [];
    /** @var string */
    public $template = '';
    /** @var bool */
    public $restoreBackupMode = false;
    /** @var bool */
    public $recoveryMode = false;
    /** @var string */
    public $archivePath = '';
    /** @var int */
    public $packageId = 0;
    /** @var string */
    public $packageHash = '';
    /** @var string */
    public $installerPath = '';
    /** @var string */
    public $installerBootLog = '';
    /** @var string */
    public $installerLog = '';
    /** @var string */
    public $dupInstallerPath = '';
    /** @var string */
    public $origFileFolderPath = '';
    /** @var bool */
    public $safeMode = false;
    /** @var bool */
    public $cleanInstallerFiles = false;
    /** @var int */
    public $licenseType = -1;
    /** @var string */
    public $phpVersion = '';
    /** @var string */
    public $archiveType = '';
    /** @var float */
    public $siteSize = 0.0;
    /** @var int */
    public $siteNumFiles = 0;
    /** @var float */
    public $siteDbSize = 0.0;
    /** @var int */
    public $siteDBNumTables = 0;
    /** @var string[] */
    public $components = [];
    /** @var string */
    public $ustatIdentifier = '';
    /** @var array<string, mixed> Extra data added by addons */
    public $extra = [];
}
