<?php

/**
 * Engines params descriptions
 */

namespace Duplicator\Installer\Core\Params\Descriptors;

use DUPX_Extraction;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Params\Items\ParamItem;
use Duplicator\Installer\Core\Params\Items\ParamForm;
use Duplicator\Installer\Core\Params\Items\ParamOption;
use Duplicator\Installer\Package\PComponents;
use Duplicator\Installer\Utils\Log\Log;
use DUPX_ArchiveConfig;
use DUPX_DBInstall;

/**
 * class where all parameters are initialized. Used by the param manager
 */
final class ParamDescEngines implements DescriptorInterface
{
    const AUTO_SKIP_PATH_REPLACE_LIST = [
        '',
        '/html',
    ];

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

        $statusRemoveActions = ($archiveConfig->isDBOnly() ? ParamOption::OPT_DISABLED : ParamOption::OPT_ENABLED);

        $params[PrmMng::PARAM_ARCHIVE_ACTION] = new ParamForm(
            PrmMng::PARAM_ARCHIVE_ACTION,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => DUPX_Extraction::ACTION_DO_NOTHING,
                'acceptValues' => [
                    DUPX_Extraction::ACTION_DO_NOTHING,
                    DUPX_Extraction::ACTION_REMOVE_WP_FILES,
                    DUPX_Extraction::ACTION_REMOVE_ALL_FILES,
                    DUPX_Extraction::ACTION_REMOVE_UPLOADS,
                ],
            ],
            [
                'label'          => 'Archive Action:',
                'status'         => function ($paramObj): string {
                    if (InstState::isAddSiteOnMultisite()) {
                        return ParamForm::STATUS_SKIP;
                    } elseif (InstState::isRecoveryMode()) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options'        => [
                    new ParamOption(DUPX_Extraction::ACTION_DO_NOTHING, 'Extract files over current files'),
                    new ParamOption(DUPX_Extraction::ACTION_REMOVE_WP_FILES, 'Remove WP core and content and extract', $statusRemoveActions),
                    new ParamOption(DUPX_Extraction::ACTION_REMOVE_ALL_FILES, 'Remove all files except addon sites and extract', $statusRemoveActions),
                    new ParamOption(DUPX_Extraction::ACTION_REMOVE_UPLOADS, 'Empty only uploads folder', $statusRemoveActions),
                ],
                'wrapperClasses' => ['revalidate-on-change'],
                'subNote'        => fn($param) => dupxTplRender('parts/params/archive-action-notes', [
                    'currentAction' => $param->getValue(),
                ], false),
            ]
        );

        $params[PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES] = new ParamForm(
            PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => DUPX_Extraction::FILTER_NONE,
                'acceptValues' => [
                    DUPX_Extraction::FILTER_NONE,
                    DUPX_Extraction::FILTER_SKIP_WP_CORE,
                    DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES,
                    DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES,
                ],
            ],
            [
                'label'          => 'Skip Files:',
                'status'         => function ($paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'options'        => [
                    new ParamOption(DUPX_Extraction::FILTER_NONE, 'Extract all files'),
                    new ParamOption(DUPX_Extraction::FILTER_SKIP_WP_CORE, 'Skip extraction of WP core files'),
                    new ParamOption(
                        DUPX_Extraction::FILTER_SKIP_CORE_PLUG_THEMES,
                        'Skip extraction of WP core files and plugins/themes existing on the host'
                    ),
                    new ParamOption(DUPX_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES, 'Extract only media files and new plugins and themes'),
                ],
                'wrapperClasses' => ['revalidate-on-change'],
                'subNote'        => dupxTplRender('parts/params/extract-skip-notes', [
                    'currentSkipMode' => DUPX_Extraction::FILTER_NONE,
                ], false),
            ]
        );

        $engineOptions = self::getArchiveEngineOptions();

        $params[PrmMng::PARAM_ARCHIVE_ENGINE] = new ParamForm(
            PrmMng::PARAM_ARCHIVE_ENGINE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'          => $engineOptions['default'],
                'acceptValues'     => $engineOptions['acceptValues'],
                'sanitizeCallback' => function ($value) {
                    if (
                        PrmMng::getInstance()->getValue(
                            PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES
                        ) !== DUPX_Extraction::FILTER_NONE && $value === DUPX_Extraction::ENGINE_ZIP_SHELL
                    ) {
                        return DUPX_Extraction::ENGINE_ZIP_CHUNK;
                    }
                    return $value;
                },
            ],
            [
                'label'   => 'Extraction Mode:',
                'options' => $engineOptions['options'],
                'size'    => 0,
                'subNote' => $engineOptions['subNote'],
                'attr'    => ['onchange' => 'DUPX.onSafeModeSwitch();'],
            ]
        );

        $params[PrmMng::PARAM_ZIP_THROTTLING] = new ParamForm(
            PrmMng::PARAM_ZIP_THROTTLING,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            ['default' => false],
            [
                'label'         => 'Server Throttling:',
                'checkboxLabel' => 'Enable archive extraction throttling',
                'status'        => function (): string {
                    if (
                        PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE) === DUPX_Extraction::ENGINE_ZIP
                        || PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE) === DUPX_Extraction::ENGINE_ZIP_CHUNK
                    ) {
                        return ParamForm::STATUS_ENABLED;
                    } else {
                        return ParamForm::STATUS_DISABLED;
                    }
                },
            ]
        );

        $params[PrmMng::PARAM_DB_ACTION] = new ParamForm(
            PrmMng::PARAM_DB_ACTION,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => DUPX_DBInstall::DBACTION_EMPTY,
                'acceptValues' => [
                    DUPX_DBInstall::DBACTION_CREATE,
                    DUPX_DBInstall::DBACTION_EMPTY,
                    DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES,
                    DUPX_DBInstall::DBACTION_RENAME,
                    DUPX_DBInstall::DBACTION_MANUAL,
                    DUPX_DBInstall::DBACTION_ONLY_CONNECT,
                    DUPX_DBInstall::DBACTION_DO_NOTHING,
                ],
            ],
            [
                'label'          => 'Action:',
                'status'         => function ($paramObj): string {
                    if (
                        InstState::isRestoreBackup() ||
                        InstState::isAddSiteOnMultisite()
                    ) {
                        return ParamForm::STATUS_INFO_ONLY;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
                'wrapperClasses' => ['revalidate-on-change'],
                'options'        => [
                    new ParamOption(
                        DUPX_DBInstall::DBACTION_CREATE,
                        'Create New Database',
                        function (): string {
                            if (InstState::getInstance()->getMode() === InstState::MODE_STD_INSTALL) {
                                return ParamOption::OPT_ENABLED;
                            } else {
                                return ParamOption::OPT_DISABLED;
                            }
                        }
                    ),
                    new ParamOption(DUPX_DBInstall::DBACTION_EMPTY, 'Empty Database'),
                    new ParamOption(DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES, 'Overwrite Existing Tables'),
                    new ParamOption(DUPX_DBInstall::DBACTION_MANUAL, 'Skip Database Extraction'),
                    new ParamOption(DUPX_DBInstall::DBACTION_RENAME, 'Backup and Rename Existing Tables'),
                    new ParamOption(DUPX_DBInstall::DBACTION_DO_NOTHING, 'Only Extract Files'),
                    new ParamOption(DUPX_DBInstall::DBACTION_ONLY_CONNECT, 'Do Nothing (Advanced)', ParamOption::OPT_HIDDEN),
                ],
                'subNote'        => dupxTplRender('parts/params/db-action-notes', [], false),
            ]
        );

        $params[PrmMng::PARAM_DB_ENGINE] = new ParamForm(
            PrmMng::PARAM_DB_ENGINE,
            ParamForm::TYPE_STRING,
            ParamForm::FORM_TYPE_SELECT,
            [
                'default'      => DUPX_DBInstall::ENGINE_CHUNK,
                'acceptValues' => [
                    DUPX_DBInstall::ENGINE_CHUNK,
                    DUPX_DBInstall::ENGINE_NORMAL,
                ],
            ],
            [
                'label'   => 'Processing:',
                'size'    => 0,
                'options' => [
                    new ParamOption(DUPX_DBInstall::ENGINE_CHUNK, 'Chunking mode'),
                    new ParamOption(DUPX_DBInstall::ENGINE_NORMAL, 'Single step'),
                ],
            ]
        );

        $params[PrmMng::PARAM_DB_CHUNK] = new ParamItem(
            PrmMng::PARAM_DB_CHUNK,
            ParamForm::TYPE_BOOL,
            [
                'default' => ($params[PrmMng::PARAM_DB_ENGINE]->getValue() === DUPX_DBInstall::ENGINE_CHUNK),
            ]
        );

        $params[PrmMng::PARAM_REPLACE_ENGINE] = new ParamItem(
            PrmMng::PARAM_REPLACE_ENGINE,
            ParamForm::TYPE_INT,
            [
                'default'      => \DUPX_S3_Funcs::MODE_CHUNK,
                'acceptValues' => [
                    \DUPX_S3_Funcs::MODE_NORMAL,
                    \DUPX_S3_Funcs::MODE_CHUNK,
                    \DUPX_S3_Funcs::MODE_SKIP,
                ],
            ]
        );

        $oldHomePath                             = DUPX_ArchiveConfig::getInstance()->getRealValue('archivePaths')->home;
        $params[PrmMng::PARAM_SKIP_PATH_REPLACE] = new ParamForm(
            PrmMng::PARAM_SKIP_PATH_REPLACE,
            ParamForm::TYPE_BOOL,
            ParamForm::FORM_TYPE_CHECKBOX,
            [
                'default' => in_array($oldHomePath, self::AUTO_SKIP_PATH_REPLACE_LIST),
            ],
            [
                'label'         => 'Skip path replace:',
                'checkboxLabel' => 'Skips the replacement of the source path',
                'status'        => function (ParamForm $paramObj): string {
                    $sourcePath = PrmMng::getInstance()->getValue(PrmMng::PARAM_PATH_OLD);
                    if (strlen($sourcePath) == 0) {
                        return ParamForm::STATUS_DISABLED;
                    } else {
                        return ParamForm::STATUS_ENABLED;
                    }
                },
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
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        if (
            $params[PrmMng::PARAM_ARCHIVE_ACTION]->getStatus() !== ParamItem::STATUS_OVERWRITE &&
            InstState::isRestoreBackup($params[PrmMng::PARAM_INST_TYPE]->getValue()) &&
            PComponents::isFullFilesComponents($archiveConfig->components) &&
            $archiveConfig->mu_is_filtered === false
        ) {
            Log::info('IS RESTORE BACKUP WITH FULL FILES COMPONENTS: SET ARCHIVE ACTION TO REMOVE WP FILES');
            $params[PrmMng::PARAM_ARCHIVE_ACTION]->setValue(DUPX_Extraction::ACTION_REMOVE_WP_FILES);
        }

        if (
            $params[PrmMng::PARAM_DB_ACTION]->getStatus() !== ParamItem::STATUS_OVERWRITE &&
            InstState::isRestoreBackup($params[PrmMng::PARAM_INST_TYPE]->getValue()) &&
            $archiveConfig->dbInfo->tablesBaseCount != $archiveConfig->dbInfo->tablesFinalCount &&
            $archiveConfig->mu_is_filtered === false
        ) {
            Log::info('IS RESTORE BACKUP WITH PARTIAL DB: SET DB ACTION TO REMOVE ONLY TABLES');
            $params[PrmMng::PARAM_DB_ACTION]->setValue(DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES);
        }

        if (
            InstState::dbDoNothing() ||
            InstState::isRestoreBackup($params[PrmMng::PARAM_INST_TYPE]->getValue())
        ) {
            $default = \DUPX_S3_Funcs::MODE_SKIP;
        } elseif ($params[PrmMng::PARAM_DB_ENGINE]->getValue() === DUPX_DBInstall::ENGINE_CHUNK) {
            $default = \DUPX_S3_Funcs::MODE_CHUNK;
        } else {
            $default = \DUPX_S3_Funcs::MODE_NORMAL;
        }
        $params[PrmMng::PARAM_REPLACE_ENGINE]->setValue($default);

        if (
            ($params[PrmMng::PARAM_ARCHIVE_ENGINE]->getValue() === DUPX_Extraction::ENGINE_ZIP
                || $params[PrmMng::PARAM_ARCHIVE_ENGINE]->getValue() === DUPX_Extraction::ENGINE_ZIP_CHUNK)
            && \DUPX_Custom_Host_Manager::getInstance()->isHosting(\DUPX_Custom_Host_Manager::HOST_SITEGROUND)
        ) {
            $params[PrmMng::PARAM_ZIP_THROTTLING]->setValue(true);
        }
    }

    /**
     * Get db chunk engine value
     *
     * @return boolean
     */
    public static function getDbChunkFromParams(): bool
    {
        return PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_ENGINE) === DUPX_DBInstall::ENGINE_CHUNK;
    }

    /**
     * Get replace engine mode
     *
     * @return integer
     */
    public static function getReplaceEngineModeFromParams(): int
    {
        $paramsManager = PrmMng::getInstance();
        if (InstState::dbDoNothing() || InstState::isRestoreBackup()) {
            return \DUPX_S3_Funcs::MODE_SKIP;
        } elseif ($paramsManager->getValue(PrmMng::PARAM_DB_ENGINE) === DUPX_DBInstall::ENGINE_CHUNK) {
            return \DUPX_S3_Funcs::MODE_CHUNK;
        } else {
            return \DUPX_S3_Funcs::MODE_NORMAL;
        }
    }


    /**
     * Get archive engine options
     *
     * @return array<string, mixed>
     */
    private static function getArchiveEngineOptions(): array
    {
        $archiveConfig = \DUPX_ArchiveConfig::getInstance();

        $acceptValues = [];
        $subNote      = null;
        if (($manualEnable = \DUPX_Conf_Utils::isManualExtractFilePresent()) === true) {
            $acceptValues[] = DUPX_Extraction::ENGINE_MANUAL;
        } else {
            $subNote = <<<SUBNOTEHTML
* Option enabled when archive has been pre-extracted
<a href="https://duplicator.com/knowledge-base/how-to-handle-various-install-scenarios" target="_blank">[more info]</a>               
SUBNOTEHTML;
        }
        if (($zipEnable = ($archiveConfig->isZipArchive() && \DUPX_Conf_Utils::archiveExists() && \DUPX_Conf_Utils::isPhpZipAvailable())) === true) {
            $acceptValues[] = DUPX_Extraction::ENGINE_ZIP;
            $acceptValues[] = DUPX_Extraction::ENGINE_ZIP_CHUNK;
        }
        if (($shellZipEnable = ($archiveConfig->isZipArchive() && \DUPX_Conf_Utils::archiveExists() && \DUPX_Conf_Utils::isShellZipAvailable())) === true) {
            $acceptValues[] = DUPX_Extraction::ENGINE_ZIP_SHELL;
        }
        if (($dupEnable = (!$archiveConfig->isZipArchive() && \DUPX_Conf_Utils::archiveExists())) === true) {
            $acceptValues[] = DUPX_Extraction::ENGINE_DUP;
        }

        $options   = [];
        $options[] = new ParamOption(
            DUPX_Extraction::ENGINE_MANUAL,
            'Manual Archive Extraction',
            $manualEnable ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED
        );

        if ($archiveConfig->isZipArchive()) {
            //ZIP-ARCHIVE
            $options[] = new ParamOption(
                DUPX_Extraction::ENGINE_ZIP,
                'PHP ZipArchive',
                $zipEnable ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED
            );

            $options[] = new ParamOption(
                DUPX_Extraction::ENGINE_ZIP_CHUNK,
                'PHP ZipArchive Chunking',
                $zipEnable ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED
            );

            $options[] = new ParamOption(
                DUPX_Extraction::ENGINE_ZIP_SHELL,
                'Shell Exec Unzip',
                function (): string {
                    $archiveConfig = \DUPX_ArchiveConfig::getInstance();
                    $pathsMapping  = $archiveConfig->getPathsMapping();
                    if (PrmMng::getInstance()->getValue(PrmMng::PARAM_ARCHIVE_ENGINE_SKIP_WP_FILES) !== DUPX_Extraction::FILTER_NONE) {
                        return ParamOption::OPT_DISABLED;
                    }
                    if (is_array($pathsMapping) && count($pathsMapping) > 1) {
                        return ParamOption::OPT_DISABLED;
                    }
                    if ($archiveConfig->isZipArchive() && \DUPX_Conf_Utils::archiveExists() && \DUPX_Conf_Utils::isShellZipAvailable()) {
                        return ParamOption::OPT_ENABLED;
                    }
                    return ParamOption::OPT_DISABLED;
                }
            );
        } else {
            // DUPARCHIVE
            $options[] = new ParamOption(
                DUPX_Extraction::ENGINE_DUP,
                'DupArchive',
                $dupEnable ? ParamOption::OPT_ENABLED : ParamOption::OPT_DISABLED
            );
        }

        if ($manualEnable) {
            $default = DUPX_Extraction::ENGINE_MANUAL;
        } elseif ($zipEnable) {
            $default = DUPX_Extraction::ENGINE_ZIP_CHUNK;
        } elseif ($shellZipEnable) {
            $default = DUPX_Extraction::ENGINE_ZIP_SHELL;
        } elseif ($dupEnable) {
            $default = DUPX_Extraction::ENGINE_DUP;
        } else {
            $default = null;
        }

        return [
            'options'      => $options,
            'acceptValues' => $acceptValues,
            'default'      => $default,
            'subNote'      => $subNote,
        ];
    }
}
