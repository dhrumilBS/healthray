<?php

/**
 * Plugins params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamFormPlugins;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Installer\Core\InstState;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescPlugins implements DescriptorInterface
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
        $params[PrmMng::PARAM_PLUGINS] = new ParamFormPlugins(
            PrmMng::PARAM_PLUGINS,
            ParamFormPlugins::TYPE_ARRAY_STRING,
            ParamFormPlugins::FORM_TYPE_PLUGINS_SELECT,
            [
                'default'          => [],
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'       => 'Plugins',
                'renderLabel' => false,
                'status'      => function ($paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
            ]
        );

        $params[PrmMng::PARAM_IGNORE_PLUGINS] = new ParamItem(
            PrmMng::PARAM_IGNORE_PLUGINS,
            ParamItem::TYPE_ARRAY_STRING,
            [
                'default'          => [],
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ]
        );

        $params[PrmMng::PARAM_FORCE_DIABLE_PLUGINS] = new ParamItem(
            PrmMng::PARAM_FORCE_DIABLE_PLUGINS,
            ParamItem::TYPE_ARRAY_STRING,
            [
                'default'          => [],
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
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
