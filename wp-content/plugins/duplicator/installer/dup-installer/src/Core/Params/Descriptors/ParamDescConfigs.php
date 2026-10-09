<?php

/**
 * Configs(htaccess, wp-config ...) params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use DUPX_Template;
use DUPX_WPConfig;
use Exception;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescConfigs implements DescriptorInterface
{
    /**
     * Init params
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function init(&$params): void
    {
        $params[PrmMng::PARAM_INST_TYPE] = new ParamForm(
            PrmMng::PARAM_INST_TYPE,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'      => InstState::TYPE_NOT_SET,
                'acceptValues' => [
                    self::class,
                    'getInstallTypesAcceptValues',
                ],
            ],
            [
                'status'         => ParamForm::STATUS_ENABLED,
                'label'          => 'Install Type:',
                'wrapperClasses' => [
                    'group-block',
                    'revalidate-on-change',
                ],
                'options'        => self::getInstallTypeOptions(),
            ]
        );

        $params[PrmMng::PARAM_WP_CONFIG] = new ParamForm(
            PrmMng::PARAM_WP_CONFIG,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => 'modify',
                'acceptValues' => [
                    'modify',
                    'nothing',
                    'new',
                ],
            ],
            [
                'label'          => 'WordPress:',
                'classes'        => ['requires-db-disable'],
                'wrapperClasses' => 'medium',
                'status'         => function (ParamItem $paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options'        => [
                    new ParamOption('nothing', 'Do nothing'),
                    new ParamOption(
                        'modify',
                        'Modify original',
                        fn(ParamOption $opt): string => DUPX_WPConfig::isSourceWpConfigValid() ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED
                    ),
                    new ParamOption('new', 'Create new from wp-config sample'),
                ],
                'subNote'        => 'wp-config.php',
            ]
        );

        $params[PrmMng::PARAM_HTACCESS_CONFIG] = new ParamForm(
            PrmMng::PARAM_HTACCESS_CONFIG,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => 'new',
                'acceptValues' => [
                    'new',
                    'original',
                    'nothing',
                ],
            ],
            [
                'label'          => 'Apache:',
                'classes'        => ['requires-db-disable'],
                'wrapperClasses' => 'medium',
                'status'         => function (ParamItem $paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options'        => [
                    new ParamOption('nothing', 'Do nothing'),
                    new ParamOption('original', 'Retain original from Archive.zip/daf'),
                    new ParamOption('new', 'Create new'),
                ],
                'subNote'        => '.htaccess',
            ]
        );

        $params[PrmMng::PARAM_OTHER_CONFIG] = new ParamForm(
            PrmMng::PARAM_OTHER_CONFIG,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => 'new',
                'acceptValues' => [
                    'new',
                    'original',
                    'nothing',
                ],
            ],
            [
                'label'          => 'General:',
                'classes'        => ['requires-db-disable'],
                'wrapperClasses' => 'medium',
                'status'         => function (ParamItem $paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options'        => [
                    new ParamOption('nothing', 'Do nothing'),
                    new ParamOption('original', 'Retain original from Archive.zip/daf'),
                    new ParamOption('new', 'Reset'),
                ],
                'subNote'        => 'includes: php.ini, user.ini, web.config',
            ]
        );
    }

    /**
     * Update params after overwrite logic
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function updateParamsAfterOverwrite($params): void
    {
        $installType = $params[PrmMng::PARAM_INST_TYPE]->getValue();
        if ($installType == InstState::TYPE_NOT_SET) {
            $acceptValues = $params[PrmMng::PARAM_INST_TYPE]->getAcceptValues();
            $params[PrmMng::PARAM_INST_TYPE]->setValue(self::getInstTypeByPriority($acceptValues));
        }

        $installType = $params[PrmMng::PARAM_INST_TYPE]->getValue();
        if (InstState::isRestoreBackup($installType)) {
            if (\DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
                $params[PrmMng::PARAM_WP_CONFIG]->setValue('nothing');
                $params[PrmMng::PARAM_HTACCESS_CONFIG]->setValue('nothing');
                $params[PrmMng::PARAM_OTHER_CONFIG]->setValue('nothing');
            } else {
                $params[PrmMng::PARAM_WP_CONFIG]->setValue('modify');
                $params[PrmMng::PARAM_HTACCESS_CONFIG]->setValue('original');
                $params[PrmMng::PARAM_OTHER_CONFIG]->setValue('original');
            }
        }

        if (!DUPX_WPConfig::isSourceWpConfigValid()) {
            if ($params[PrmMng::PARAM_WP_CONFIG]->getValue() === 'modify') {
                $params[PrmMng::PARAM_WP_CONFIG]->setValue('new');
            }
        }
    }

    /**
     * Return default install type from install types enabled
     *
     * @param int[] $acceptValues install types enabled
     *
     * @return int
     */
    protected static function getInstTypeByPriority($acceptValues): int
    {
        $defaultPriority = [
            InstState::TYPE_RECOVERY_MSUBDOMAIN,
            InstState::TYPE_RECOVERY_MSUBFOLDER,
            InstState::TYPE_RECOVERY_SINGLE,
            InstState::TYPE_RBACKUP_MSUBDOMAIN,
            InstState::TYPE_RBACKUP_MSUBFOLDER,
            InstState::TYPE_RBACKUP_SINGLE,
            InstState::TYPE_SINGLE_ON_SUBDOMAIN,
            InstState::TYPE_SINGLE_ON_SUBFOLDER,
            InstState::TYPE_SUBSITE_ON_SUBDOMAIN,
            InstState::TYPE_SUBSITE_ON_SUBFOLDER,
            InstState::TYPE_MSUBDOMAIN,
            InstState::TYPE_MSUBFOLDER,
            InstState::TYPE_SINGLE,
            InstState::TYPE_STANDALONE,
        ];

        foreach ($defaultPriority as $current) {
            if (in_array($current, $acceptValues)) {
                return $current;
            }
        }

        throw new Exception('No default value found on proprity list');
    }

    /**
     *
     * @return ParamOption[]
     */
    protected static function getInstallTypeOptions(): array
    {
        $result = [];

        $option = new ParamOption(InstState::TYPE_RBACKUP_SINGLE, 'Restore single site', [self::class, 'typeOptionsVisibility']);
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_RBACKUP_MSUBDOMAIN,
            '<b>Restore</b> multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_RBACKUP_MSUBFOLDER,
            '<b>Restore</b> multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_SINGLE,
            '<b>Full</b> install single site',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_MSUBDOMAIN,
            '<b>Full</b> install multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_MSUBFOLDER,
            '<b>Full</b> install multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_STANDALONE,
            '<b>Convert</b> network subsite to standalone site',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_SINGLE_ON_SUBDOMAIN,
            '<b>Import</b> single site into multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_SINGLE_ON_SUBFOLDER,
            '<b>Import</b> single site into multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_SUBSITE_ON_SUBDOMAIN,
            '<b>Import</b> subsite into multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_SUBSITE_ON_SUBFOLDER,
            '<b>Import</b> subsite into multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_RECOVERY_SINGLE,
            '<b>Restore Backup</b> single site',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_RECOVERY_MSUBDOMAIN,
            '<b>Restore Backup</b> multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        $option = new ParamOption(
            InstState::TYPE_RECOVERY_MSUBFOLDER,
            '<b>Restore Backup</b> multisite network',
            [
                self::class,
                'typeOptionsVisibility',
            ]
        );
        $option->setNote([self::class, 'getInstallTypesNotes']);
        $result[] = $option;

        return $result;
    }

    /**
     * Return option type status
     *
     * @param ParamOption $option install type option
     *
     * @return string option status
     */
    public static function typeOptionsVisibility(ParamOption $option): string
    {
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();
        $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        $isOwrMode     = PrmMng::getInstance()->getValue(PrmMng::PARAM_INSTALLER_MODE) === InstState::MODE_OVR_INSTALL;

        switch ($option->value) {
            case InstState::TYPE_SINGLE:
                if ($archiveConfig->mu_mode != 0) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_MSUBDOMAIN:
                if ($archiveConfig->mu_mode != 1) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_MSUBFOLDER:
                if ($archiveConfig->mu_mode != 2) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_STANDALONE:
                if ($archiveConfig->mu_mode == 0) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
                if (!$isOwrMode || $archiveConfig->mu_mode > 0 || !$overwriteData['isMultisite'] || !$overwriteData['subdomain']) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_SINGLE_ON_SUBFOLDER:
                if (!$isOwrMode || $archiveConfig->mu_mode > 0 || !$overwriteData['isMultisite'] || $overwriteData['subdomain']) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
                if (!$isOwrMode || $archiveConfig->mu_mode == 0 || !$overwriteData['isMultisite'] || !$overwriteData['subdomain']) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                if (!$isOwrMode || $archiveConfig->mu_mode == 0 || !$overwriteData['isMultisite'] || $overwriteData['subdomain']) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_RBACKUP_SINGLE:
                if (
                    $archiveConfig->mu_mode != 0 ||
                    !InstState::isInstallerCreatedInThisLocation()
                ) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_RBACKUP_MSUBDOMAIN:
                if (
                    $archiveConfig->mu_mode != 1 ||
                    !InstState::isInstallerCreatedInThisLocation()
                ) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_RBACKUP_MSUBFOLDER:
                if (
                    $archiveConfig->mu_mode != 2 ||
                    !InstState::isInstallerCreatedInThisLocation()
                ) {
                    return ParamOption::OPT_HIDDEN;
                }
                break;
            case InstState::TYPE_RECOVERY_SINGLE:
            case InstState::TYPE_RECOVERY_MSUBDOMAIN:
            case InstState::TYPE_RECOVERY_MSUBFOLDER:
                return ParamOption::OPT_HIDDEN;
            case InstState::TYPE_NOT_SET:
            default:
                throw new Exception('Install type not valid ' . $option->value);
        }

        $acceptValues = self::getInstallTypesAcceptValues();
        return in_array($option->value, $acceptValues) ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED;
    }

    /**
     *
     * @return int[]
     */
    public static function getInstallTypesAcceptValues(): array
    {
        $acceptValues  = [];
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();

        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_TEMPLATE) === DUPX_Template::TEMPLATE_RECOVERY) {
            switch ($archiveConfig->mu_mode) {
                case 0:
                    $acceptValues[] = InstState::TYPE_RECOVERY_SINGLE;
                    break;
                case 1:
                    $acceptValues[] = InstState::TYPE_RECOVERY_MSUBDOMAIN;
                    break;
                case 2:
                    $acceptValues[] = InstState::TYPE_RECOVERY_MSUBFOLDER;
                    break;
            }
            return $acceptValues;
        }

        $overwriteData  = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
        $isManaged      = \DUPX_Custom_Host_Manager::getInstance()->isManaged();
        $isSameLocation = InstState::isInstallerCreatedInThisLocation();

        switch ($archiveConfig->mu_mode) {
            case 0:
                $acceptValues[] = InstState::TYPE_SINGLE;
                if ($isSameLocation) {
                    $acceptValues[] = InstState::TYPE_RBACKUP_SINGLE;
                }
                break;
            case 1:
                if (!$isManaged && !$archiveConfig->isPartialNetwork()) {
                    $acceptValues[] = InstState::TYPE_MSUBDOMAIN;
                    if ($isSameLocation) {
                        $acceptValues[] = InstState::TYPE_RBACKUP_MSUBDOMAIN;
                    }
                }
                break;
            case 2:
                if (!$isManaged && !$archiveConfig->isPartialNetwork()) {
                    $acceptValues[] = InstState::TYPE_MSUBFOLDER;
                    if ($isSameLocation) {
                        $acceptValues[] = InstState::TYPE_RBACKUP_MSUBFOLDER;
                    }
                }
                break;
        }

        return apply_filters(
            'duplicator_installer_install_type_accepted_values',
            $acceptValues,
            $archiveConfig,
            $overwriteData
        );
    }

    /**
     * Return install type option note
     *
     * @param ParamOption $option install type option
     *
     * @return string
     */
    public static function getInstallTypesNotes(ParamOption $option): string
    {
        switch ($option->value) {
            case InstState::TYPE_SINGLE:
            case InstState::TYPE_MSUBDOMAIN:
            case InstState::TYPE_MSUBFOLDER:
                $note = '';
                break;
            case InstState::TYPE_STANDALONE:
                $note = '';
                break;
            case InstState::TYPE_SINGLE_ON_SUBDOMAIN:
            case InstState::TYPE_SINGLE_ON_SUBFOLDER:
            case InstState::TYPE_SUBSITE_ON_SUBDOMAIN:
            case InstState::TYPE_SUBSITE_ON_SUBFOLDER:
                $notes         = [];
                $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
                if (!InstState::isImportFromBackendMode()) {
                    $notes[] = 'This functionality is active only in the Drag&Drop import.';
                } elseif (
                    !isset($overwriteData['wpVersion']) ||
                    version_compare($overwriteData['wpVersion'], InstState::SUBSITE_IMPORT_WP_MIN_VERSION, '<')
                ) {
                    $notes[] = 'WordPress ' . InstState::SUBSITE_IMPORT_WP_MIN_VERSION .
                        '+ is required on current multisite to enabled this function.';
                }
                $note = implode('<br>', $notes);
                break;
            case InstState::TYPE_RBACKUP_SINGLE:
            case InstState::TYPE_RBACKUP_MSUBDOMAIN:
            case InstState::TYPE_RBACKUP_MSUBFOLDER:
            case InstState::TYPE_RECOVERY_SINGLE:
            case InstState::TYPE_RECOVERY_MSUBDOMAIN:
            case InstState::TYPE_RECOVERY_MSUBFOLDER:
                $note = '';
                break;
            case InstState::TYPE_NOT_SET:
            default:
                throw new Exception('Install type not valid ' . $option->value);
        }

        /** @var string $note Generic filter — addons can add notes to install types (e.g. license upgrade text) */
        return (string) apply_filters('duplicator_installer_install_type_note', $note, $option);
    }
}
