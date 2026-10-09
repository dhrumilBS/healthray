<?php

/**
 * Validation params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescValidation implements DescriptorInterface
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
        $params[PrmMng::PARAM_VALIDATION_LEVEL] = new ParamItem(
            PrmMng::PARAM_VALIDATION_LEVEL,
            ParamItem::TYPE_INT,
            [
                'default'      => \DUPX_Validation_abstract_item::LV_FAIL,
                'acceptValues' => [
                    \DUPX_Validation_abstract_item::LV_FAIL,
                    \DUPX_Validation_abstract_item::LV_HARD_WARNING,
                    \DUPX_Validation_abstract_item::LV_SOFT_WARNING,
                    \DUPX_Validation_abstract_item::LV_GOOD,
                    \DUPX_Validation_abstract_item::LV_PASS,
                ],
            ]
        );

        $params[PrmMng::PARAM_VALIDATION_ACTION_ON_START] = new ParamItem(
            PrmMng::PARAM_VALIDATION_ACTION_ON_START,
            ParamForm::TYPE_STRING,
            [
                'default'      => \DUPX_Validation_manager::ACTION_ON_START_NORMAL,
                'acceptValues' => [
                    \DUPX_Validation_manager::ACTION_ON_START_NORMAL,
                    \DUPX_Validation_manager::ACTION_ON_START_AUTO,
                ],
            ]
        );

        $params[PrmMng::PARAM_VALIDATION_SHOW_ALL] = new ParamForm(
            PrmMng::PARAM_VALIDATION_SHOW_ALL,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_SWITCH,
            ['default' => false],
            [
                'label'          => 'Show all',
                'wrapperClasses' => 'align-right',
            ]
        );

        $params[PrmMng::PARAM_ACCEPT_TERM_COND] = new ParamForm(
            PrmMng::PARAM_ACCEPT_TERM_COND,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Accept term and conditions',
                'renderLabel'   => false,
                'checkboxLabel' => 'I have read and accept all <a href="#" onclick="DUPX.viewTerms()" >terms &amp; notices</a>*',
                'subNote'       => '<div class="required-txt">* required to continue</div>',
                'attr'          => ['onclick' => 'DUPX.acceptWarning();'],
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
