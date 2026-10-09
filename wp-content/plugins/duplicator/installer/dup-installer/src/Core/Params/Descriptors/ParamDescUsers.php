<?php

/**
 * Users params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Installer\Core\Params\Items\ParamFormUsersReset;
use DUPX_DBInstall;
use Duplicator\Installer\Core\InstState;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescUsers implements DescriptorInterface
{
    const USER_MODE_OVERWRITE    = 'overwrite';
    const USER_MODE_IMPORT_USERS = 'import_users';
    /**
     * Init params
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function init(&$params): void
    {
        $params[PrmMng::PARAM_USERS_MODE] = new ParamForm(
            PrmMng::PARAM_USERS_MODE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'          => self::USER_MODE_OVERWRITE,
                'sanitizeCallback' => function ($value) {
                    if (
                        InstState::getInstance()->getMode() !== InstState::MODE_OVR_INSTALL ||
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        // if is restore backup user mode must be overwrite
                        return ParamDescUsers::USER_MODE_OVERWRITE;
                    }

                    $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
                    if ($overwriteData['isMultisite']) {
                        return ParamDescUsers::USER_MODE_OVERWRITE;
                    }

                    // disable keep users for some db actions
                    switch (PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_ACTION)) {
                        case DUPX_DBInstall::DBACTION_CREATE:
                        case DUPX_DBInstall::DBACTION_MANUAL:
                        case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
                            return ParamDescUsers::USER_MODE_OVERWRITE;
                        case DUPX_DBInstall::DBACTION_EMPTY:
                        case DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES:
                        case DUPX_DBInstall::DBACTION_RENAME:
                            return $value;
                    }
                },
                'acceptValues'     => [
                    self::USER_MODE_OVERWRITE,
                    self::USER_MODE_IMPORT_USERS,
                ],
            ],
            [
                'status'         => function (): string {
                    // Hide user mode instandalone migration for now
                    return ParamForm::STATUS_SKIP;
                    if (InstState::getInstance()->getMode() !== InstState::MODE_OVR_INSTALL) { // @phpstan-ignore-line
                        return ParamForm::STATUS_DISABLED;
                    }

                    $overwriteData = PrmMng::getInstance()->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

                    if (
                        $overwriteData['isMultisite'] ||
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite() ||
                        InstState::isNewSiteIsMultisite()
                    ) {
                        return ParamForm::STATUS_DISABLED;
                    }
                    return ParamForm::STATUS_ENABLED;
                },
                'label'          => 'Users:',
                'options'        => function ($item): array {
                    $result   = [];
                    $result[] = new ParamOption(ParamDescUsers::USER_MODE_OVERWRITE, 'Overwrite');
                    $result[] = new ParamOption(ParamDescUsers::USER_MODE_IMPORT_USERS, 'Merge');
                    return $result;
                },
                'inlineHelp'     => dupxTplRender('parts/params/inline_helps/user_mode', [], false),
                'wrapperClasses' => ['revalidate-on-change'],
            ]
        );

        $params[PrmMng::PARAM_ADD_SUBSITE_USER_MODE] = new ParamForm(
            PrmMng::PARAM_ADD_SUBSITE_USER_MODE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'      => ParamDescUsers::USER_MODE_IMPORT_USERS,
                'acceptValues' => [ParamDescUsers::USER_MODE_IMPORT_USERS],
            ],
            [
                'status'          => fn(): string => ParamForm::STATUS_SKIP,
                'label'           => 'Users:',
                'options'         => function ($item): array {
                    $result   = [];
                    $result[] = new ParamOption(ParamDescUsers::USER_MODE_IMPORT_USERS, 'Import');
                    return $result;
                },
                'inlineHelpTitle' => 'User Mode',
                'inlineHelp'      => dupxTplRender('parts/params/inline_helps/subsite_user_mode', [], false),
                'wrapperClasses'  => ['revalidate-on-change'],
            ]
        );

        $params[PrmMng::PARAM_USERS_PWD_RESET] = new ParamFormUsersReset(
            PrmMng::PARAM_USERS_PWD_RESET,
            ParamFormUsersReset::TYPE_ARRAY_STRING,
            ParamFormUsersReset::FORM_TYPE_USERS_PWD_RESET,
            [ // ITEM ATTRIBUTES
                'default'          => array_map(fn($value): string => '', \DUPX_ArchiveConfig::getInstance()->getUsersLists()),
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    if (strlen($value) > 0 && strlen($value) <  \DUPX_Constants::MIN_NEW_PASSWORD_LEN) {
                        $paramObj->setInvalidMessage('New password must have ' . \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' or more characters');
                        return false;
                    }

                    return true;
                },
            ],
            [ // FORM ATTRIBUTES
                'status'  => function ($paramObj): string {
                    if (ParamDescUsers::getUsersMode() != ParamDescUsers::USER_MODE_OVERWRITE) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'label'   => 'Existing user reset password:',
                'classes' => 'strength-pwd-check',
                'attr'    => [
                    'title'       => \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' characters minimum',
                    'placeholder' => "Reset user password",
                ],
            ]
        );
    }

    /**
     * Return import users mode
     *
     * @return string
     */
    public static function getUsersMode()
    {
        $paramsManager = PrmMng::getInstance();

        if (InstState::isAddSiteOnMultisite()) {
            return $paramsManager->getValue(PrmMng::PARAM_ADD_SUBSITE_USER_MODE);
        } else {
            return $paramsManager->getValue(PrmMng::PARAM_USERS_MODE);
        }
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
