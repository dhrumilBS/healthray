<?php

/**
 * Replace params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescReplace implements DescriptorInterface
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
        $params[PrmMng::PARAM_BLOGNAME] = new ParamForm(
            PrmMng::PARAM_BLOGNAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'sanitizeCallback' => function ($value): string {
                    $value = SnapUtil::sanitizeNSCharsNewline($value);
                    return htmlspecialchars_decode((empty($value) ? 'No Blog Title Set' : $value), ENT_QUOTES);
                },
            ],
            [
                'label'          => 'Site Title:',
                'status'         => function ($paramObj): string {
                    if (InstState::isRestoreBackup()) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'wrapperClasses' => [
                    'revalidate-on-change',
                    'requires-db-hide',
                ],
            ]
        );

        $params[PrmMng::PARAM_CUSTOM_SEARCH] = new ParamItem(
            PrmMng::PARAM_CUSTOM_SEARCH,
            ParamForm::TYPE_ARRAY_STRING,
            [
                'default'          => [],
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ]
        );

        $params[PrmMng::PARAM_CUSTOM_REPLACE] = new ParamItem(
            PrmMng::PARAM_CUSTOM_REPLACE,
            ParamForm::TYPE_ARRAY_STRING,
            [
                'default'          => [],
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ]
        );

        $params[PrmMng::PARAM_EMPTY_SCHEDULE_STORAGE] = new ParamForm(
            PrmMng::PARAM_EMPTY_SCHEDULE_STORAGE,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => true],
            [
                'label'         => 'Cleanup:',
                'checkboxLabel' => 'Remove schedules and storage endpoints',
            ]
        );

        $params[PrmMng::PARAM_EMAIL_REPLACE] = new ParamForm(
            PrmMng::PARAM_EMAIL_REPLACE,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Email Domains:',
                'checkboxLabel' => 'Update',
            ]
        );

        $params[PrmMng::PARAM_FULL_SEARCH] = new ParamForm(
            PrmMng::PARAM_FULL_SEARCH,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Database Search:',
                'checkboxLabel' => 'Full Search Mode',
            ]
        );

        $params[PrmMng::PARAM_POSTGUID] = new ParamForm(
            PrmMng::PARAM_POSTGUID,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Post GUID:',
                'checkboxLabel' => 'Keep Unchanged',
            ]
        );

        $params[PrmMng::PARAM_MAX_SERIALIZE_CHECK] = new ParamForm(
            PrmMng::PARAM_MAX_SERIALIZE_CHECK,
            ParamForm::TYPE_INT,
            ParamForm::FORM_TYPE_NUMBER,
            [
                'default' => \DUPX_Constants::DEFAULT_MAX_STRLEN_SERIALIZED_CHECK_IN_M,
            ],
            [
                'min'            => 0,
                'max'            => 99,
                'step'           => 1,
                'wrapperClasses' => ['small'],
                'label'          => 'Serialized obj max size:',
                'postfix'        => [
                    'type'  => 'label',
                    'label' => 'MB',
                ],
                'subNote'        => 'If the serialized object stored in the database exceeds this size, it will not be parsed for replacement.'
                . '<br><b>Too large a size in low memory installations can generate a fatal error.</b>',
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
        if ($params[PrmMng::PARAM_BLOGNAME]->getStatus() !== ParamItem::STATUS_OVERWRITE) {
            $params[PrmMng::PARAM_BLOGNAME]->setValue(\DUPX_ArchiveConfig::getInstance()->getBlognameFromSelectedSubsiteId());
        }

        $installType = $params[PrmMng::PARAM_INST_TYPE]->getValue();
        if (InstState::isRestoreBackup($installType)) {
            $params[PrmMng::PARAM_EMPTY_SCHEDULE_STORAGE]->setValue(false);
        }
    }
}
