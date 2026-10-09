<?php

namespace Duplicator\Package\Restore;

use Duplicator\Package\AbstractPackage;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Models\Storages\StoragesUtil;
use Duplicator\Package\AbstractPackageDeployer;
use Duplicator\Libs\WpUtils\WpArchiveUtils;

class BackupPackage extends AbstractPackageDeployer
{
    protected AbstractPackage $package;

    /**
     * Used for restor backup button
     *
     * @param string          $path    Archiv path
     * @param AbstractPackage $package Recovery Backup
     */
    public function __construct($path, AbstractPackage $package)
    {
        $this->package    = $package;
        $this->archivePwd = $this->package->Archive->getArchivePassword();
        parent::__construct($path);
    }

    /**
     * This function extract archive info backup and read it, After initializing the information deletes the file.
     *
     * @return bool true on success, or false on failure
     */
    public function loadInfo(): bool
    {
        return $this->loadInfoFromArchive();
    }

    /**
     * Return recovery-specific overwrite params
     *
     * @param array<string, array{value: mixed, formStatus?: string}> $baseParams Base params
     *
     * @return array<string, array{value: mixed, formStatus?: string}>
     */
    protected function getOverwriteParamsExtended(array $baseParams): array
    {
        $updDirs = wp_upload_dir();
        $result  = [
            PrmMng::PARAM_TEMPLATE           => ['value' => 'recovery'],
            PrmMng::PARAM_RECOVERY_LINK      => ['value' => ''],
            PrmMng::PARAM_SITE_URL           => [
                'value'      => site_url(),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_PATH_WP_CORE_NEW   => [
                'value'      => WpArchiveUtils::getOriginalPaths('abs'),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_URL_CONTENT_NEW    => [
                'value'      => content_url(),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_PATH_CONTENT_NEW   => [
                'value'      => WpArchiveUtils::getOriginalPaths('wpcontent'),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_URL_UPLOADS_NEW    => [
                'value'      => $updDirs['baseurl'],
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_PATH_UPLOADS_NEW   => [
                'value'      => WpArchiveUtils::getOriginalPaths('uploads'),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_URL_PLUGINS_NEW    => [
                'value'      => plugins_url(),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_PATH_PLUGINS_NEW   => [
                'value'      => WpArchiveUtils::getOriginalPaths('plugins'),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_URL_MUPLUGINS_NEW  => [
                'value'      => WpArchiveUtils::getOriginalUrls('muplugins'),
                'formStatus' => 'st_infoonly',
            ],
            PrmMng::PARAM_PATH_MUPLUGINS_NEW => [
                'value'      => WpArchiveUtils::getOriginalPaths('muplugins'),
                'formStatus' => 'st_infoonly',
            ],
        ];

        $overwriteData = $baseParams[PrmMng::PARAM_OVERWRITE_SITE_DATA] ?? ['value' => []];
        foreach (StoragesUtil::getLocalStoragesPaths() as $path) {
            $overwriteData['value']['removeFilters']['dirs'][] = $path;
        }
        $result[PrmMng::PARAM_OVERWRITE_SITE_DATA] = $overwriteData;

        return $result;
    }
}
