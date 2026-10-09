<?php

/**
 * Controller params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescController implements DescriptorInterface
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
        $params[PrmMng::PARAM_FINAL_REPORT_DATA] = new ParamItem(
            PrmMng::PARAM_FINAL_REPORT_DATA,
            ParamItem::TYPE_ARRAY_MIXED,
            [
                'default' => [
                    'extraction' => [
                        'table_count' => 0,
                        'table_rows'  => 0,
                        'query_errs'  => 0,
                    ],
                    'replace'    => [
                        'scan_tables' => 0,
                        'scan_rows'   => 0,
                        'scan_cells'  => 0,
                        'updt_tables' => 0,
                        'updt_rows'   => 0,
                        'updt_cells'  => 0,
                        'errsql'      => 0,
                        'errser'      => 0,
                        'errkey'      => 0,
                        'errsql_sum'  => 0,
                        'errser_sum'  => 0,
                        'errkey_sum'  => 0,
                        'err_all'     => 0,
                        'warn_all'    => 0,
                        'warnlist'    => [],
                    ],
                ],
            ]
        );

        $params[PrmMng::PARAM_INSTALLER_MODE] = new ParamItem(
            PrmMng::PARAM_INSTALLER_MODE,
            ParamItem::TYPE_INT,
            [
                'default'      => InstState::MODE_UNKNOWN,
                'acceptValues' => [
                    InstState::MODE_UNKNOWN,
                    InstState::MODE_STD_INSTALL,
                    InstState::MODE_OVR_INSTALL,
                ],
            ]
        );

        $params[PrmMng::PARAM_OVERWRITE_SITE_DATA] = new ParamItem(
            PrmMng::PARAM_OVERWRITE_SITE_DATA,
            ParamItem::TYPE_ARRAY_MIXED,
            [
                'default' => InstState::overwriteDataDefault(),
            ]
        );


        $params[PrmMng::PARAM_DEBUG] = new ParamItem(
            PrmMng::PARAM_DEBUG,
            ParamItem::TYPE_BOOL,
            [
                'persistence' => true,
                'default'     => false,
            ]
        );

        $params[PrmMng::PARAM_DEBUG_PARAMS] = new ParamItem(
            PrmMng::PARAM_DEBUG_PARAMS,
            ParamItem::TYPE_BOOL,
            [
                'persistence' => true,
                'default'     => false,
            ]
        );

        $params[PrmMng::PARAM_CTRL_ACTION] = new ParamItem(
            PrmMng::PARAM_CTRL_ACTION,
            ParamForm::TYPE_STRING,
            [
                'persistence'  => false,
                'default'      => '',
                'acceptValues' => [
                    '',
                    'ajax',
                    'secure',
                    'ctrl-step1',
                    'ctrl-step2',
                    'ctrl-step3',
                    'ctrl-step4',
                    'help',
                ],
            ]
        );

        $params[PrmMng::PARAM_STEP_ACTION] = new ParamItem(
            PrmMng::PARAM_STEP_ACTION,
            ParamForm::TYPE_STRING,
            [
                'persistence'  => false,
                'default'      => '',
                'acceptValues' => [
                    '',
                    \DUPX_CTRL::ACTION_STEP_INIZIALIZED,
                    \DUPX_CTRL::ACTION_STEP_ON_VALIDATE,
                    \DUPX_CTRL::ACTION_STEP_SET_TEMPLATE,
                ],
            ]
        );

        $params[Security::CTRL_TOKEN] = new ParamItem(
            Security::CTRL_TOKEN,
            ParamForm::TYPE_STRING,
            [
                'persistence'      => false,
                'default'          => null,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ]
        );

        $params[PrmMng::PARAM_ROUTER_ACTION] = new ParamItem(
            PrmMng::PARAM_ROUTER_ACTION,
            ParamItem::TYPE_STRING,
            [
                'persistence'  => false,
                'default'      => 'router',
                'acceptValues' => ['router'],
            ]
        );

        $params[PrmMng::PARAM_TEMPLATE] = new ParamItem(
            PrmMng::PARAM_TEMPLATE,
            ParamForm::TYPE_STRING,
            [
                'default'      => \DUPX_Template::TEMPLATE_BASE,
                'acceptValues' => [
                    \DUPX_Template::TEMPLATE_BASE,
                    \DUPX_Template::TEMPLATE_ADVANCED,
                    \DUPX_Template::TEMPLATE_IMPORT_BASE,
                    \DUPX_Template::TEMPLATE_IMPORT_ADVANCED,
                    \DUPX_Template::TEMPLATE_RECOVERY,
                ],
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
