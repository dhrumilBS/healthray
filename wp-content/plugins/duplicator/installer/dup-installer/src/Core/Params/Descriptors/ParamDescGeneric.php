<?php

/**
 * Generic params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Installer\Core\Params\Items\ParamFormPass;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapServer;
use Duplicator\Libs\Snap\SnapUtil;
use DUPX_ArchiveConfig;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescGeneric implements DescriptorInterface
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
        $newObj = new ParamForm(
            PrmMng::PARAM_FILE_PERMS_VALUE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '644',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/', // octal + ugo rwx,
            ],
            [
                'label'          => 'File permissions',
                'renderLabel'    => false,
                'status'         => SnapServer::isWindows() ? ParamForm::STATUS_SKIP : ParamForm::STATUS_ENABLED,
                'wrapperClasses' => ['display-inline-block'],
            ]
        );

        $params[PrmMng::PARAM_FILE_PERMS_VALUE] = $newObj;
        $permItemId                             = $newObj->getFormItemId();
        $params[PrmMng::PARAM_SET_FILE_PERMS]   = new ParamForm(
            PrmMng::PARAM_SET_FILE_PERMS,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_SWITCH,
            [
                'default' => !SnapServer::isWindows(),
            ],
            [
                'status'         => SnapServer::isWindows() ? ParamForm::STATUS_SKIP : ParamForm::STATUS_ENABLED,
                'label'          => 'File permissions:',
                'checkboxLabel'  => 'All files',
                'wrapperClasses' => ['display-inline-block'],
                'attr'           => [
                    'onclick' => "jQuery('#" . $permItemId . "').prop('disabled', !jQuery(this).is(':checked'));",
                ],
            ]
        );

        $newObj = new ParamForm(
            PrmMng::PARAM_DIR_PERMS_VALUE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '755',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateRegex'    => '/^[ugorwx,\s\+\-0-7]+$/', // octal + ugo rwx
            ],
            [
                'label'          => 'Folder permissions',
                'renderLabel'    => false,
                'status'         => SnapServer::isWindows() ? ParamForm::STATUS_SKIP : ParamForm::STATUS_ENABLED,
                'wrapperClasses' => ['display-inline-block'],
            ]
        );

        $params[PrmMng::PARAM_DIR_PERMS_VALUE] = $newObj;
        $permItemId                            = $newObj->getFormItemId();
        $params[PrmMng::PARAM_SET_DIR_PERMS]   = new ParamForm(
            PrmMng::PARAM_SET_DIR_PERMS,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_SWITCH,
            [
                'default' => !SnapServer::isWindows(),
            ],
            [
                'status'         => SnapServer::isWindows() ? ParamForm::STATUS_SKIP : ParamForm::STATUS_ENABLED,
                'label'          => 'Dir permissions:',
                'checkboxLabel'  => 'All Directories',
                'wrapperClasses' => ['display-inline-block'],
                'attr'           => [
                    'onclick' => "jQuery('#" . $permItemId . "').prop('disabled', !jQuery(this).is(':checked'));",
                ],
            ]
        );

        $params[PrmMng::PARAM_SAFE_MODE] = new ParamForm(
            PrmMng::PARAM_SAFE_MODE,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => 0,
                'acceptValues' => [
                    0,
                    1,
                    2,
                ],
            ],
            [
                'label'   => 'Safe Mode:',
                'status'  => function (ParamItem $paramObj): string {
                    if (InstState::isRestoreBackup()) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options' => [
                    new ParamOption(0, 'Disabled'),
                    new ParamOption(1, 'Enabled'),
                ],
                'attr'    => ['onchange' => 'DUPX.onSafeModeSwitch();'],
            ]
        );

        $params[PrmMng::PARAM_FILE_TIME] = new ParamForm(
            PrmMng::PARAM_FILE_TIME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'      => 'current',
                'acceptValues' => [
                    'current',
                    'original',
                ],
            ],
            [
                'label'   => 'File Times:',
                'status'  => ParamForm::STATUS_ENABLED,
                'options' => [
                    new ParamOption('current', 'Current', ParamOption::OPT_ENABLED, ['title' => 'Set the files current date time to now']),
                    new ParamOption('original', 'Original', ParamOption::OPT_ENABLED, ['title' => 'Keep the files date time the same']),
                ],
                'subNote' => 'This option is not supported for extraction mode Shell Exec Unzip',
            ]
        );

        $params[PrmMng::PARAM_LOGGING] = new ParamForm(
            PrmMng::PARAM_LOGGING,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'      => Log::LV_DEFAULT,
                'acceptValues' => [
                    Log::LV_DEFAULT,
                    Log::LV_DETAILED,
                    Log::LV_DEBUG,
                    Log::LV_HARD_DEBUG,
                ],
            ],
            [
                'label'   => 'Logging:',
                'options' => [
                    new ParamOption(Log::LV_DEFAULT, 'Light'),
                    new ParamOption(Log::LV_DETAILED, 'Detailed'),
                    new ParamOption(Log::LV_DEBUG, 'Debug'),
                    // enabled only with overwrite params
                    new ParamOption(Log::LV_HARD_DEBUG, 'Hard debug', ParamOption::OPT_HIDDEN),
                ],
            ]
        );

        $params[PrmMng::PARAM_REMOVE_RENDUNDANT] = new ParamForm(
            PrmMng::PARAM_REMOVE_RENDUNDANT,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'          => 'Cleanup:',
                'checkboxLabel'  => 'Remove disabled plugins/themes',
                'wrapperClasses' => ['requires-db-hide'],
                'status'         => function (ParamItem $paramObj): string {
                    if (InstState::isRestoreBackup() || InstState::isAddSiteOnMultisite()) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
            ]
        );

        $params[PrmMng::PARAM_REMOVE_USERS_WITHOUT_PERMISSIONS] = new ParamForm(
            PrmMng::PARAM_REMOVE_USERS_WITHOUT_PERMISSIONS,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'          => ' ',
                'checkboxLabel'  => 'Remove users without permissions',
                'wrapperClasses' => ['requires-db-hide'],
            ]
        );

        $params[PrmMng::PARAM_RECOVERY_LINK] = new ParamItem(
            PrmMng::PARAM_RECOVERY_LINK,
            ParamFormPass::TYPE_STRING,
            ['default' => '']
        );

        $params[PrmMng::PARAM_FROM_SITE_IMPORT_INFO] = new ParamItem(
            PrmMng::PARAM_FROM_SITE_IMPORT_INFO,
            ParamFormPass::TYPE_ARRAY_MIXED,
            [
                'default' => [],
            ]
        );

        $params[PrmMng::PARAM_AUTO_CLEAN_INSTALLER_FILES] = new ParamForm(
            PrmMng::PARAM_AUTO_CLEAN_INSTALLER_FILES,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => true],
            [
                'label'         => 'CLean installation files',
                'renderLabel'   => false,
                'checkboxLabel' => 'Auto delete installer files after login to secure site (recommended!)',
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
    }
}
