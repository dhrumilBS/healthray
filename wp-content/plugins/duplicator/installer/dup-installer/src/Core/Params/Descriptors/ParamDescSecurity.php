<?php

/**
 * Generic params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamFormPass;
use DUPX_ArchiveConfig;
use Duplicator\Installer\Core\InstState;
use Duplicator\Libs\Snap\SnapUtil;
use DUPX_View_Funcs;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescSecurity implements DescriptorInterface
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
        $params[PrmMng::PARAM_SECURE_PASS] = new ParamFormPass(
            PrmMng::PARAM_SECURE_PASS,
            ParamFormPass::TYPE_STRING,
            ParamFormPass::FORM_TYPE_PWD_TOGGLE,
            [
                'persistence'      => false,
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'          => 'Password:',
                'status'         => function (ParamForm $param): string {
                    if (Security::getInstance()->getSecurityType() == Security::SECURITY_PASSWORD) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
                'wrapperClasses' => 'margin-bottom-2',
                'attr'           => [
                    'placeholder' => (DUPX_ArchiveConfig::getInstance()->secure_on ? '' : 'Password not enabled'),
                ],
            ]
        );

        $params[PrmMng::PARAM_SECURE_ARCHIVE_HASH] = new ParamForm(
            PrmMng::PARAM_SECURE_ARCHIVE_HASH,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'persistence'      => false,
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
            ],
            [
                'label'          => 'Backup File Name:',
                'status'         => function (ParamForm $param): string {
                    if (!InstState::isOverwrite()) {
                        return ParamForm::STATUS_SKIP;
                    } elseif (Security::getInstance()->getSecurityType() == Security::SECURITY_ARCHIVE) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
                'wrapperClasses' => 'margin-bottom-4',
                'attr'           => ['placeholder' => 'example: [full-unique-name]_archive.zip'],
                'subNote'        => DUPX_View_Funcs::helpLink('secure', 'How to get archive file name?', false),
            ]
        );

        $params[PrmMng::PARAM_SECURE_OK] = new ParamItem(
            PrmMng::PARAM_SECURE_OK,
            ParamForm::TYPE_BOOL,
            ['default' => false]
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
