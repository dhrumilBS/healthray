<?php

/**
 * CPanel params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Descriptors\ParamsDescriptors;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamFormPass;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescCPanel implements DescriptorInterface
{
    const INVALID_EMPTY = 'can\'t be empty';

    /**
     * Init params
     *
     * @param (ParamItem|ParamForm)[] $params params list
     *
     * @return void
     */
    public static function init(&$params): void
    {
        $params[PrmMng::PARAM_CPNL_CAN_SELECTED] = new ParamItem(
            PrmMng::PARAM_CPNL_CAN_SELECTED,
            ParamItem::TYPE_BOOL,
            ['default' => true]
        );

        $params[PrmMng::PARAM_CPNL_HOST] = new ParamForm(
            PrmMng::PARAM_CPNL_HOST,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => "https://" . parse_url(DUPX_ROOT_URL, PHP_URL_HOST) . ":2083",
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfCpanel',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'CPanel host:',
                'wrapperClasses' => ['revalidate-on-change'],
                'attr'           => [
                    'required'    => 'true',
                    'placeholder' => 'cPanel url',
                ],
                'subNote'        => '<span id="cpnl-host-warn">'
                . 'Caution: The cPanel host name and URL in the browser address bar do not match, '
                . 'in rare cases this may be intentional.'
                . 'Please be sure this is the correct server to avoid data loss.</span>',
                'postfix'        => [
                    'type'      => 'button',
                    'label'     => 'get',
                    'btnAction' => 'DUPX.getcPanelURL(this);',
                ],
            ]
        );

        $params[PrmMng::PARAM_CPNL_USER] = new ParamForm(
            PrmMng::PARAM_CPNL_USER,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfCpanel',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'CPanel username:',
                'wrapperClasses' => ['revalidate-on-change'],
                'attr'           => [
                    'required'             => 'required',
                    'data-parsley-pattern' => '/^[\w.-~]+$/',
                    'placeholder'          => 'cPanel username',
                ],
            ]
        );

        $params[PrmMng::PARAM_CPNL_PASS] = new ParamFormPass(
            PrmMng::PARAM_CPNL_PASS,
            ParamFormPass::TYPE_STRING,
            ParamFormPass::FORM_TYPE_PWD_TOGGLE,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfCpanel',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'CPanel password:',
                'wrapperClasses' => ['revalidate-on-change'],
                'attr'           => [
                    'required'    => 'true',
                    'placeholder' => 'cPanel password',
                ],
            ]
        );

        /** @var ParamForm */
        $paramFormObj = $params[PrmMng::PARAM_DB_ACTION];
        $paramFormObj = $paramFormObj->getCopyWithNewName(
            PrmMng::PARAM_CPNL_DB_ACTION,
            [],
            [
                'status' => ParamForm::STATUS_DISABLED,
            ]
        );
        // force create database enable for cpanel
        $paramFormObj->setOptionStatus(0, ParamOption::OPT_ENABLED);
        $params[PrmMng::PARAM_CPNL_DB_ACTION] = $paramFormObj;


        $params[PrmMng::PARAM_CPNL_PREFIX] = new ParamForm(
            PrmMng::PARAM_CPNL_PREFIX,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_HIDDEN,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'       => 'Control panel prefix',
                'renderLabel' => false,
            ]
        );


        /** @var ParamForm */
        $paramFormObj = $params[PrmMng::PARAM_DB_HOST];
        $paramFormObj = $paramFormObj->getCopyWithNewName(
            PrmMng::PARAM_CPNL_DB_HOST,
            [
                'default'          => 'localhost',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfCpanel',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'status' => ParamForm::STATUS_DISABLED,
                'attr'   => ['required' => 'true'],
            ]
        );

        $params[PrmMng::PARAM_CPNL_DB_HOST] = $paramFormObj;

        $params[PrmMng::PARAM_CPNL_DB_NAME_SEL] = new ParamForm(
            PrmMng::PARAM_CPNL_DB_NAME_SEL,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'          => 'Database:',
                'wrapperClasses' => ['revalidate-on-change'],
                'status'         => ParamForm::STATUS_DISABLED,
                'attr'           => [
                    'required'             => 'true',
                    'data-parsley-pattern' => '^((?!-- Select Database --).)*$',
                ],
                'subNote'        => '<span class="s2-warning-emptydb">'
                . 'Warning: The selected "Action" above will remove <u>all data</u> from this database!'
                . '</span>',
            ]
        );

        /** @var ParamForm */
        $paramFormObj = $params[PrmMng::PARAM_DB_NAME];
        $paramFormObj = $paramFormObj->getCopyWithNewName(
            PrmMng::PARAM_CPNL_DB_NAME_TXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    $paramManager = PrmMng::getInstance();
                    if (
                        $paramManager->getValue(PrmMng::PARAM_DB_VIEW_MODE) === 'cpnl' &&
                        $paramManager->getValue(PrmMng::PARAM_CPNL_DB_ACTION) === \DUPX_DBInstall::DBACTION_CREATE
                    ) {
                        return ParamsDescriptors::validateNotEmpty($value, $paramObj);
                    }

                    return true;
                },
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'   => 'Database:',
                'status'  => ParamForm::STATUS_DISABLED,
                'attr'    => [
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[\w.-~]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbname-txt-error',
                ],
                'subNote' => '<span id="cpnl-dbname-txt-error"></span>',
                'prefix'  => [
                    'type'  => 'label',
                    'label' => '',
                    'id'    => 'cpnl-prefix-dbname',
                ],
            ]
        );

        $params[PrmMng::PARAM_CPNL_DB_NAME_TXT] = $paramFormObj;

        $params[PrmMng::PARAM_CPNL_DB_USER_SEL] = new ParamForm(
            PrmMng::PARAM_CPNL_DB_USER_SEL,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'          => 'User:',
                'wrapperClasses' => ['revalidate-on-change'],
                'status'         => ParamForm::STATUS_DISABLED,
                'attr'           => [
                    'required'             => 'true',
                    'data-parsley-pattern' => '^((?!-- Select User --).)*$',
                ],
            ]
        );

        /** @var ParamForm */
        $paramFormObj = $params[PrmMng::PARAM_DB_USER];
        $paramFormObj = $paramFormObj->getCopyWithNewName(
            PrmMng::PARAM_CPNL_DB_USER_TXT,
            [
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => function ($value, ParamItem $paramObj): bool {
                    $paramManager = PrmMng::getInstance();
                    if (
                        $paramManager->getValue(PrmMng::PARAM_DB_VIEW_MODE) === 'cpnl' &&
                        $paramManager->getValue(PrmMng::PARAM_CPNL_DB_USER_CHK) === true
                    ) {
                        return ParamsDescriptors::validateNotEmpty($value, $paramObj);
                    }

                    return true;
                },
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'   => 'User:',
                'status'  => ParamForm::STATUS_DISABLED,
                'attr'    => [
                    'required'                      => 'true',
                    'data-parsley-pattern'          => '/^[a-zA-Z0-9-_]+$/',
                    'data-parsley-errors-container' => '#cpnl-dbuser-txt-error',
                    'data-parsley-cpnluser'         => "16",
                ],
                'subNote' => '<span id="cpnl-dbuser-txt-error"></span>',
                'prefix'  => [
                    'type'  => 'label',
                    'label' => '',
                    'id'    => 'cpnl-prefix-dbuser',
                ],
            ]
        );

        $params[PrmMng::PARAM_CPNL_DB_USER_TXT] = $paramFormObj;

        $params[PrmMng::PARAM_CPNL_DB_USER_CHK] = new ParamForm(
            PrmMng::PARAM_CPNL_DB_USER_CHK,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'          => ' ',
                'wrapperClasses' => ['revalidate-on-change'],
                'checkboxLabel'  => 'Create New Database User',
            ]
        );

        /** @var ParamForm */
        $paramFormObj = $params[PrmMng::PARAM_DB_PASS];
        $paramFormObj = $paramFormObj->getCopyWithNewName(
            PrmMng::PARAM_CPNL_DB_PASS,
            [],
            [
                'status' => ParamForm::STATUS_DISABLED,
                'attr'   => ['required' => 'true'],
            ]
        );

        $params[PrmMng::PARAM_CPNL_DB_PASS] = $paramFormObj;

        $params[PrmMng::PARAM_CPNL_IGNORE_PREFIX] = new ParamForm(
            PrmMng::PARAM_CPNL_IGNORE_PREFIX,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'          => 'CPanel Prefix',
                'wrapperClasses' => ['revalidate-on-change'],
                'checkboxLabel'  => 'Ignore',
                'attr'           => ['onclick' => 'DUPX.cpnlPrefixIgnore();'],
            ]
        );
    }

    /**
     * Validate function for cpanle params
     *
     * @param mixed     $value    input value
     * @param ParamItem $paramObj current param object
     *
     * @return boolean
     */
    public static function validateNoEmptyIfCpanel($value, ParamItem $paramObj)
    {
        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_VIEW_MODE) !== 'cpnl') {
            return true;
        }
        return ParamsDescriptors::validateNotEmpty($value, $paramObj);
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
