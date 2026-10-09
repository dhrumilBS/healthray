<?php

/**
 * Database params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Descriptors\ParamsDescriptors;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Installer\Core\Params\Items\ParamFormTables;
use Duplicator\Installer\Core\Params\Items\ParamFormPass;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Installer\Core\InstState;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescDatabase implements DescriptorInterface
{
    const INVALID_EMPTY                     = 'can\'t be empty';
    const EMPTY_COLLATION_LABEL             = ' --- DEFAULT --- ';
    const DEFAULT_CHARSET_POSTFIX           = ' (Default)';
    const DEFAULT_COLLATE_POSTFIX           = ' (Default)';
    const SPLIT_CREATE_MAX_VALUE_TO_DEFAULT = 200;

    /**
     * Init params
     *
     * @param ParamItem[]|ParamForm[] $params params list
     *
     * @return void
     */
    public static function init(&$params): void
    {
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();

        $params[PrmMng::PARAM_DB_DISPLAY_OVERWIRE_WARNING] = new ParamItem(
            PrmMng::PARAM_DB_DISPLAY_OVERWIRE_WARNING,
            ParamItem::TYPE_BOOL,
            ['default' => true]
        );

        $params[PrmMng::PARAM_DB_ONLY_PREFIXED_TABLES] = new ParamItem(
            PrmMng::PARAM_DB_ONLY_PREFIXED_TABLES,
            ParamItem::TYPE_BOOL,
            ['default' => false]
        );

        $params[PrmMng::PARAM_DB_VIEW_MODE] = new ParamForm(
            PrmMng::PARAM_DB_VIEW_MODE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_BGROUP,
            [
                'default'      => 'basic',
                'acceptValues' => [
                    'basic',
                    'cpnl',
                ],
            ],
            [
                'label'                 => 'Database view mode',
                'renderLabel'           => false,
                'options'               => [
                    new ParamOption('basic', 'Default'),
                    new ParamOption('cpnl', 'CPanel'),
                ],
                'wrapperClasses'        => [
                    'revalidate-on-change',
                    'align-right',
                    'requires-db-hide',
                ],
                'inputContainerClasses' => ['small'],
            ]
        );

        $params[PrmMng::PARAM_DB_HOST] = new ParamForm(
            PrmMng::PARAM_DB_HOST,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'persistence'      => true,
                'default'          => 'localhost',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfBasic',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'Host:',
                'wrapperClasses' => [
                    'revalidate-on-change',
                    'requires-db-hide',
                ],
                'attr'           => [
                    'required'    => 'required',
                    'placeholder' => 'localhost',
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_NAME] = new ParamForm(
            PrmMng::PARAM_DB_NAME,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'persistence'      => true,
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfBasic',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'Database:',
                'wrapperClasses' => [
                    'revalidate-on-change',
                    'requires-db-hide',
                ],
                'attr'           => [
                    'required'    => 'required',
                    'placeholder' => 'new or existing database name',
                ],
                'subNote'        => dupxTplRender('parts/params/db-name-notes', [], false),
            ]
        );

        $params[PrmMng::PARAM_DB_USER] = new ParamForm(
            PrmMng::PARAM_DB_USER,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'persistence'      => true,
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
                'validateCallback' => [
                    self::class,
                    'validateNoEmptyIfBasic',
                ],
                'invalidMessage'   => self::INVALID_EMPTY,
            ],
            [
                'label'          => 'User:',
                'wrapperClasses' => [
                    'revalidate-on-change',
                    'requires-db-hide',
                ],
                'attr'           => [
                    'placeholder'  => 'valid database username',
                    // Can be written field wise
                    // Ref. https://developer.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                    'autocomplete' => "off",
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_PASS] = new ParamFormPass(
            PrmMng::PARAM_DB_PASS,
            ParamFormPass::TYPE_STRING,
            ParamFormPass::FORM_TYPE_PWD_TOGGLE,
            [
                'persistence'      => true,
                'default'          => '',
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewline',
                ],
            ],
            [
                'label'          => 'Password:',
                'wrapperClasses' => [
                    'revalidate-on-change',
                    'requires-db-hide',
                ],
                'attr'           => [
                    'placeholder'  => 'valid database user password',
                    // Can be written field wise
                    // Ref. https://devBasicBasiceloper.mozilla.org/en-US/docs/Web/Security/Securing_your_site/Turning_off_form_autocompletion
                    'autocomplete' => "off",
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_FLAG] = new ParamItem(
            PrmMng::PARAM_DB_FLAG,
            ParamForm::TYPE_INT,
            [
                'default'      => \DUPX_DB::DB_CONNECTION_FLAG_NOT_SET,
                'acceptValues' => function (ParamItem $param): array {
                    $result = [
                        \DUPX_DB::MYSQLI_CLIENT_NO_FLAGS,
                        MYSQLI_CLIENT_SSL,
                    ];
                    if (defined("MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT")) {
                        // phpcs:ignore PHPCompatibility.Constants.NewConstants.mysqli_client_ssl_dont_verify_server_certFound
                        $result[] = MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
                    }
                    return $result;
                },
            ]
        );

        $params[PrmMng::PARAM_DB_CHARSET] = new ParamForm(
            PrmMng::PARAM_DB_CHARSET,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'          => $archiveConfig->getWpConfigDefineValue('DB_CHARSET', ''),
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateRegex'    => ParamForm::VALIDATE_REGEX_AZ_NUMBER_SEP_EMPTY,
            ],
            [
                'label'   => 'Charset:',
                'status'  => function (ParamForm $param): string {
                    if (InstState::isRestoreBackup()) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options' => [
                    self::class,
                    'getCharsetSelectOptions',
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_COLLATE] = new ParamForm(
            PrmMng::PARAM_DB_COLLATE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'          => $archiveConfig->getWpConfigDefineValue('DB_COLLATE', ''),
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateRegex'    => ParamForm::VALIDATE_REGEX_AZ_NUMBER_SEP_EMPTY,
            ],
            [
                'label'   => 'Collation:',
                'status'  => function (): string {
                    if (InstState::isRestoreBackup()) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options' => [
                    self::class,
                    'getCollationSelectOptions',
                ],
            ]
        );

        $tablePrefixWarning = "Changing this setting alters the database table prefix by renaming all tables and references to them.\n"
            . "Change it only if you're sure you know what you're doing!";

        $params[PrmMng::PARAM_DB_TABLE_PREFIX] = new ParamForm(
            PrmMng::PARAM_DB_TABLE_PREFIX,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => \DUPX_ArchiveConfig::getInstance()->wp_tableprefix,
                'sanitizeCallback' => [
                    SnapUtil::class,
                    'sanitizeNSCharsNewlineTrim',
                ],
                'validateRegex'    => ParamForm::VALIDATE_REGEX_AZ_NUMBER_SEP,
            ],
            [
                'status'         => function (): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_READONLY;
                    }
                },
                'label'          => 'Table Prefix:',
                'wrapperClasses' => ['revalidate-on-change'],
                'postfix'        => [
                    'type'      => 'button',
                    'label'     => 'edit',
                    'btnAction' => 'DUPX.editActivate(this, ' . SnapJson::jsonEncode($tablePrefixWarning) . ');',
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_VIEW_CREATION] = new ParamForm(
            PrmMng::PARAM_DB_VIEW_CREATION,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => true],
            [
                'label'         => 'Objects:',
                'checkboxLabel' => 'Enable View Creation',
            ]
        );

        $params[PrmMng::PARAM_DB_PROC_CREATION] = new ParamForm(
            PrmMng::PARAM_DB_PROC_CREATION,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => true],
            [
                'label'         => ' ',
                'checkboxLabel' => 'Enable Stored Procedure Creation',
            ]
        );

        $params[PrmMng::PARAM_DB_FUNC_CREATION] = new ParamForm(
            PrmMng::PARAM_DB_FUNC_CREATION,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => true],
            [
                'label'         => ' ',
                'checkboxLabel' => 'Enable Function Creation',
            ]
        );

        $params[PrmMng::PARAM_DB_REMOVE_DEFINER] = new ParamForm(
            PrmMng::PARAM_DB_REMOVE_DEFINER,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => ' ',
                'checkboxLabel' => 'Remove security DEFINER declarations',
            ]
        );

        $numTables                              = count((array) \DUPX_ArchiveConfig::getInstance()->dbInfo->tablesList);
        $params[PrmMng::PARAM_DB_SPLIT_CREATES] = new ParamForm(
            PrmMng::PARAM_DB_SPLIT_CREATES,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            [
                'default' => ($numTables <= self::SPLIT_CREATE_MAX_VALUE_TO_DEFAULT),
            ],
            [
                'label'         => 'Create:',
                'checkboxLabel' => 'Run all CREATE SQL statements at once',
            ]
        );

        $newObj = new ParamForm(
            PrmMng::PARAM_DB_MYSQL_MODE_OPTS,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_TEXT,
            [
                'default'          => '',
                'validateRegex'    => '/^[A-Za-z0-9_\-,]*$/', // db options with , and can be empty
                'sanitizeCallback' => function ($value): string {
                    $value = SnapUtil::sanitizeNSCharsNewlineTrim($value);
                    return str_replace(' ', '', $value);
                },
            ],
            [
                'label'          => ' ', // for aligment at PARAM_DB_MYSQL_MODE
                'wrapperClasses' => 'no-display',
                'subNote'        => 'Separate additional ' . \DUPX_View_Funcs::helpLink('step2', 'sql modes', false) . ' with commas &amp; no spaces.<br>'
                . 'Example: <i>NO_ENGINE_SUBSTITUTION,NO_ZERO_IN_DATE,...</i>.</small>',
            ]
        );
        $params[PrmMng::PARAM_DB_MYSQL_MODE_OPTS] = $newObj;
        $modeOptsWrapper                          = $newObj->getFormWrapperId();

        $params[PrmMng::PARAM_DB_MYSQL_MODE] = new ParamForm(
            PrmMng::PARAM_DB_MYSQL_MODE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_RADIO,
            [
                'default'      => 'DEFAULT',
                'acceptValues' => [
                    'DEFAULT',
                    'DISABLE',
                    'CUSTOM',
                ],
            ],
            [
                'label'   => 'Mode:',
                'options' => [
                    new ParamOption('DEFAULT', 'Default', ParamOption::OPT_ENABLED, [
                        'onchange' => "if ($(this).is(':checked')) { "
                    . "jQuery('#" . $modeOptsWrapper . "').addClass('no-display');"
                    . "}",
                    ]),
                    new ParamOption('DISABLE', 'Disable', ParamOption::OPT_ENABLED, [
                        'onchange' => "if ($(this).is(':checked')) { "
                    . "jQuery('#" . $modeOptsWrapper . "').addClass('no-display');"
                    . "}",
                    ]),
                    new ParamOption('CUSTOM', 'Custom', ParamOption::OPT_ENABLED, [
                        'onchange' => "if ($(this).is(':checked')) { "
                    . "jQuery('#" . $modeOptsWrapper . "').removeClass('no-display');"
                    . "}",
                    ]),
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_TABLES] = new ParamFormTables(
            PrmMng::PARAM_DB_TABLES,
            ParamFormTables::TYPE_ARRAY_TABLES,
            ParamFormTables::FORM_TYPE_TABLES_SELECT,
            [// ITEM ATTRIBUTES
                'default' => [],
            ],
            [// FORM ATTRIBUTES
                'label'       => 'Tables',
                'renderLabel' => false,
                'status'      => function (ParamForm $paramObj): string {
                    if (InstState::isRestoreBackup()) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
            ]
        );
    }

    /**
     * Validate function for database params
     *
     * @param mixed     $value    input value
     * @param ParamItem $paramObj current param object
     *
     * @return boolean
     */
    public static function validateNoEmptyIfBasic($value, ParamItem $paramObj)
    {
        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_VIEW_MODE) !== 'basic') {
            return true;
        }

        return ParamsDescriptors::validateNotEmpty($value, $paramObj);
    }

    /**
     * Get charset options list
     *
     * @return ParamOption[]
     */
    public static function getCharsetSelectOptions(): array
    {
        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_VALIDATION_LEVEL) < \DUPX_Validation_manager::MIN_LEVEL_VALID) {
            return [];
        }

        $data       = \DUPX_DB_Functions::getInstance()->getCharsetAndCollationData();
        $charsetDef = \DUPX_DB_Functions::getInstance()->getDefaultCharset();

        $options = [];

        foreach ($data as $charset => $charsetInfo) {
            $label     = $charset . ($charset == $charsetDef ? self::DEFAULT_CHARSET_POSTFIX : '');
            $options[] = new ParamOption($charset, $label, ParamOption::OPT_ENABLED, [
                'data-collations'        => json_encode($charsetInfo['collations']),
                'data-collation-default' => $charsetInfo['defCollation'],
            ]);
        }

        return $options;
    }

    /**
     * Get collation options list
     *
     * @return ParamOption[]
     */
    public static function getCollationSelectOptions(): array
    {
        $options = [new ParamOption('', self::EMPTY_COLLATION_LABEL)];

        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_VALIDATION_LEVEL) < \DUPX_Validation_manager::MIN_LEVEL_VALID) {
            return $options;
        }

        $data           = \DUPX_DB_Functions::getInstance()->getCharsetAndCollationData();
        $currentCharset = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_CHARSET);

        if (!isset($data[$currentCharset])) {
            return $options;
        }

        $defaultCollation = \DUPX_DB_Functions::getInstance()->getDefaultCollateOfCharset($currentCharset);
        // if charset exists update default
        $options = [new ParamOption('', self::EMPTY_COLLATION_LABEL . ' [' . $defaultCollation . ']')];

        foreach ($data[$currentCharset]['collations'] as $collation) {
            $label     = $collation . ($collation == $data[$currentCharset]['defCollation'] ? self::DEFAULT_COLLATE_POSTFIX : '');
            $options[] = new ParamOption($collation, $label);
        }

        return $options;
    }

    /**
     * Update Charset and collate param by database settings
     *
     * @return void
     */
    public static function updateCharsetAndCollateByDatabaseSettings(): void
    {
        if (InstState::dbDoNothing()) {
            return;
        }

        $paramsManager = PrmMng::getInstance();
        $data          = \DUPX_DB_Functions::getInstance()->getCharsetAndCollationData();
        $charsetDef    = \DUPX_DB_Functions::getInstance()->getDefaultCharset();

        $currentCharset = $paramsManager->getValue(PrmMng::PARAM_DB_CHARSET);
        $currentCollate = $paramsManager->getValue(PrmMng::PARAM_DB_COLLATE);

        if (!array_key_exists($currentCharset, $data)) {
            $paramsManager->setValue(PrmMng::PARAM_DB_CHARSET, $charsetDef);
            $paramsManager->setValue(PrmMng::PARAM_DB_COLLATE, '');
            Log::info('DEFAULT DB_CHARSET [' . $currentCharset . '] isn\'t valid, update DB_CHARSET to ' . $charsetDef . ' and DB_COLLATE set empty');
        } elseif (strlen($currentCollate) > 0 && !in_array($currentCollate, $data[$currentCharset]['collations'])) {
            $paramsManager->setValue(PrmMng::PARAM_DB_COLLATE, '');
            Log::info('DEFAULT DB_COLLATE [' . $currentCollate . '] isn\'t valid, DB_COLLATE set empty');
        }
        $paramsManager->save();
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
        $params[PrmMng::PARAM_DB_TABLES]->setValue(\DUPX_DB_Tables::getInstance()->getDefaultParamValue());
    }
}
