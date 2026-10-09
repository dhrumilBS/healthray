<?php

/**
 * New admin params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamFormPass;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescNewAdmin implements DescriptorInterface
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
        $params[PrmMng::PARAM_WP_ADMIN_CREATE_NEW] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_CREATE_NEW,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_SWITCH,
            ['default' => false],
            [
                'label'         => 'Create New User:',
                'status'        => function ($paramObj): string {
                    if (ParamDescUsers::getUsersMode() != ParamDescUsers::USER_MODE_OVERWRITE) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'checkboxLabel' => '',
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_NAME] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_NAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    if (!PrmMng::getInstance()->getValue(PrmMng::PARAM_WP_ADMIN_CREATE_NEW)) {
                        return true;
                    }

                    if (strlen($value) < 4) {
                        $paramObj->setInvalidMessage('Must have 4 or more characters');
                        return false;
                    }

                    return true;
                },
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'Username:',
                'classes' => 'new-admin-field',
                'attr'    => [
                    'title'       => '4 characters minimum',
                    'placeholder' => "(4 or more characters)",
                ],
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_PASSWORD] = new ParamFormPass(
            PrmMng::PARAM_WP_ADMIN_PASSWORD,
            ParamFormPass::TYPE_STRING,
            ParamFormPass::FORM_TYPE_PWD_TOGGLE,
            [
                'default'          => \DUPX_ArchiveConfig::getInstance()->cpnl_pass,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    if (!PrmMng::getInstance()->getValue(PrmMng::PARAM_WP_ADMIN_CREATE_NEW)) {
                        return true;
                    }

                    if (strlen($value) <  \DUPX_Constants::MIN_NEW_PASSWORD_LEN) {
                        $paramObj->setInvalidMessage('Must have ' . \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' or more characters');
                        return false;
                    }

                    return true;
                },
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'Password:',
                'classes' => [
                    'strength-pwd-check',
                    'new-admin-field',
                ],
                'attr'    => [
                    'placeholder' => '(' . \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' or more characters)',
                    'title'       => \DUPX_Constants::MIN_NEW_PASSWORD_LEN . ' characters minimum',
                ],
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_MAIL] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_MAIL,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    if (!PrmMng::getInstance()->getValue(PrmMng::PARAM_WP_ADMIN_CREATE_NEW)) {
                        return true;
                    }

                    if (strlen($value) < 4) {
                        $paramObj->setInvalidMessage('Email name must have 4 or more characters');
                        return false;
                    }

                    if (filter_var($value, FILTER_VALIDATE_EMAIL) == false) {
                        // The notice manager renders this message as HTML
                        $paramObj->setInvalidMessage('Email "' . esc_html((string) $value) . '"  isn\'t valid');
                        return false;
                    }

                    return true;
                },
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'Email:',
                'classes' => 'new-admin-field',
                'attr'    => [
                    'title'       => '4 characters minimum',
                    'placeholder' => "(4 or more characters)",
                ],
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_NICKNAME] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_NICKNAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'Nickname:',
                'classes' => 'new-admin-field',
                'attr'    => [
                    'title'       => 'if username is empty',
                    'placeholder' => "(if username is empty)",
                ],
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_FIRST_NAME] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_FIRST_NAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'First Name:',
                'classes' => 'new-admin-field',
                'attr'    => [
                    'title'       => 'optional',
                    'placeholder' => "(optional)",
                ],
            ]
        );

        $params[PrmMng::PARAM_WP_ADMIN_LAST_NAME] = new ParamForm(
            PrmMng::PARAM_WP_ADMIN_LAST_NAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
            ],
            [
                'status'  => [
                    self::class,
                    'getStatuOfNewAdminParams',
                ],
                'label'   => 'Last Name:',
                'classes' => 'new-admin-field',
                'attr'    => [
                    'title'       => 'optional',
                    'placeholder' => "(optional)",
                ],
            ]
        );
    }

    /**
     *
     * @return string
     */
    public static function getStatuOfNewAdminParams(): string
    {
        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_WP_ADMIN_CREATE_NEW)) {
            return ParamForm::STATUS_ENABLED;
        } else {
            return ParamForm::STATUS_DISABLED;
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
