<?php

/**
 * controller step 0
 *
 * Standard: PSR-2
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Deploy\Database\DbGunzip;
use Duplicator\Installer\Core\Deploy\Plugins\PluginsManager;
use Duplicator\Installer\Core\InstState;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Installer\Core\Security;
use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Utils\SecureCsrf;
use Duplicator\Installer\Utils\Tests\WP\TestsExecuter;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Libs\Snap\SnapString;
use Duplicator\Libs\Snap\SnapUtil;

/**
 * Ajax controller
 */
final class DUPX_Ctrl_ajax
{
    const DEBUG_AJAX_CALL_SLEEP            = 0;
    const PREVENT_BRUTE_FORCE_ATTACK_SLEEP = 1;
    const AJAX_NAME                        = 'ajax_request';
    const ACTION_NAME                      = 'ajax_action';
    const TOKEN_NAME                       = 'ajax_csrf_token';
    // ACCEPTED ACTIONS
    const ACTION_INITPASS_CHECK         = 'initpass';
    const ACTION_PROCEED_CONFIRM_DIALOG = 'proceed_confirm_dialog';
    const ACTION_VALIDATE               = 'validate';
    const ACTION_SET_PARAMS_S1          = 'sparam_s1';
    const ACTION_SET_PARAMS_S2          = 'sparam_s2';
    const ACTION_SET_PARAMS_S3          = 'sparam_s3';
    const ACTION_DB_GUNZIP              = 'dbgunzip';
    const ACTION_EXTRACTION             = 'extract';
    const ACTION_DBINSTALL              = 'dbinstall';
    const ACTION_WEBSITE_UPDATE         = 'webupdate';
    const ACTION_PWD_CHECK              = 'pwdcheck';
    const ACTION_FINAL_TESTS_PREPARE    = 'finalpre';
    const ACTION_FINAL_TESTS_AFTER      = 'finalafter';
    const ACTION_SET_AUTO_CLEAN_FILES   = 'autoclean';

    /**
     * Ajax actions
     *
     * @return string[]
     */
    public static function ajaxActions()
    {
        static $actions = null;
        if (is_null($actions)) {
            $actions = [
                self::ACTION_PROCEED_CONFIRM_DIALOG,
                self::ACTION_VALIDATE,
                self::ACTION_SET_PARAMS_S1,
                self::ACTION_SET_PARAMS_S2,
                self::ACTION_SET_PARAMS_S3,
                self::ACTION_DB_GUNZIP,
                self::ACTION_EXTRACTION,
                self::ACTION_DBINSTALL,
                self::ACTION_WEBSITE_UPDATE,
                self::ACTION_PWD_CHECK,
                self::ACTION_FINAL_TESTS_PREPARE,
                self::ACTION_FINAL_TESTS_AFTER,
                self::ACTION_SET_AUTO_CLEAN_FILES,
            ];
        }
        return $actions;
    }

    /**
     * Ajax controller
     *
     * @return void
     */
    public static function controller(): void
    {
        $action = null;
        if (self::isAjax($action) === false) {
            return;
        }

        ob_start();

        Log::info("\n" . '-------------------------' . "\n" . 'AJAX ACTION [' . $action . "] START");
        Log::infoObject('POST DATA: ', $_POST, Log::LV_DEBUG);

        $jsonResult = [
            'success'      => true,
            'message'      => '',
            "errorContent" => [
                'pre'  => '',
                'html' => '',
            ],
            'trace'        => '',
            'actionData'   => null,
        ];

        Log::setThrowExceptionOnError(true);

        try {
            DUPX_Template::getInstance()->setTemplate(PrmMng::getInstance()->getValue(PrmMng::PARAM_TEMPLATE));
            $jsonResult['actionData'] = self::actions($action);
        } catch (Exception $e) {
            Log::logException($e);

            if (SnapString::isHTML($e->getMessage())) {
                $message = $e->getMessage();
            } else {
                $message = DUPX_U::esc_html($e->getMessage());
            }

            $jsonResult = [
                'success'      => false,
                'message'      => $message,
                "errorContent" => [
                    'pre'  => Log::getLogException($e),
                    'html' => '',
                ],
            ];
        }

        $invalidOutput = SnapUtil::obCleanAll();
        ob_end_clean();
        if (strlen($invalidOutput) > 0) {
            Log::info('INVALID AJAX OUTPUT:' . "\n" . $invalidOutput . "\n---------------------------------");
        }

        if ($jsonResult['success']) {
            Log::info('AJAX ACTION [' . $action . '] SUCCESS');
        } else {
            Log::info('AJAX ACTION [' . $action . '] FAIL, MESSAGE: ' . $jsonResult['message']);
        }

        Log::info('-------------------------' . "\n");

        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        echo SnapJson::jsonEncode($jsonResult);
        Log::close();
        // if is ajax always die;
        die();
    }

    /**
     * ajax actions
     *
     * @param string $action action name
     *
     * @return mixed
     */
    protected static function actions(?string $action)
    {
        $actionData = null;

        self::debugAjaxCallSleep();

        switch ($action) {
            case self::ACTION_PWD_CHECK:
                $actionData = Security::getInstance()->securityCheck();
                break;
            case self::ACTION_PROCEED_CONFIRM_DIALOG:
                $vData = DUPX_Validation_database_service::getInstance();
                if (!InstState::dbDoNothing() && !$vData->getDbConnection()) {
                    throw new Exception('Connection DB data isn\'t valid');
                }

                $actionData = dupxTplRender(
                    'pages-parts/step1/proceed-confirm-dialog',
                    [
                        'tableCount' => InstState::dbDoNothing() ? 0 : $vData->getDBActionAffectedTablesCount(),
                    ],
                    false
                );
                break;
            case self::ACTION_VALIDATE:
                DUPX_Extraction::resetData();
                DbGunzip::resetData();
                $actionData = DUPX_Validation_manager::getInstance()->getValidateData();
                if ($actionData['mainLevel'] <= DUPX_Validation_abstract_item::LV_FAIL) {
                    sleep(self::PREVENT_BRUTE_FORCE_ATTACK_SLEEP);
                } else {
                    DUPX_Ctrl_Params::setParamsAfterValidation();
                }
                $actionData['nextStepMessagesHtml'] = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);

                break;
            case self::ACTION_SET_PARAMS_S1:
                $valid = DUPX_Ctrl_Params::setParamsStep1();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $actionData     = [
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices,
                ];
                break;
            case self::ACTION_SET_PARAMS_S2:
                $valid = DUPX_Ctrl_Params::setParamsStep2();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $actionData     = [
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices,
                ];
                break;
            case self::ACTION_SET_PARAMS_S3:
                $valid = DUPX_Ctrl_Params::setParamsStep3();
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $actionData     = [
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices,
                ];
                break;
            case self::ACTION_DB_GUNZIP:
                $actionData = DbGunzip::getInstance()->process();
                break;
            case self::ACTION_EXTRACTION:
                $extractor = DUPX_Extraction::getInstance();
                $extractor->runExtraction();
                $actionData = $extractor->finishExtraction();
                break;
            case self::ACTION_DBINSTALL:
                $dbInstall  = DUPX_DBInstall::getInstance();
                $actionData = $dbInstall->deploy();
                PluginsManager::getInstance()->preViewChecks();
                break;
            case self::ACTION_WEBSITE_UPDATE:
                $actionData = DUPX_S3_Funcs::getInstance()->updateWebsite();
                break;
            case self::ACTION_FINAL_TESTS_PREPARE:
                $actionData = InstState::dbDoNothing() || TestsExecuter::preTestPrepare();
                break;
            case self::ACTION_FINAL_TESTS_AFTER:
                $actionData = InstState::dbDoNothing() || TestsExecuter::afterTestClean();
                break;
            case self::ACTION_SET_AUTO_CLEAN_FILES:
                if (
                    (!InstState::dbDoNothing() || InstState::isImportFromBackendMode()) &&
                    DUPX_Ctrl_Params::setParamAutoClean()
                ) {
                    $valid = DUPX_S3_Funcs::getInstance()->duplicatorMigrationInfoSet();
                } else {
                    $valid = false;
                }
                DUPX_NOTICE_MANAGER::getInstance()->nextStepLog(false);
                $nexStepNotices = DUPX_NOTICE_MANAGER::getInstance()->nextStepMessages(true, false);
                $actionData     = [
                    'isValid'              => $valid,
                    'nextStepMessagesHtml' => $nexStepNotices,
                ];
                break;
            default:
                throw new Exception('Invalid ajax action');
        }
        return $actionData;
    }

    /**
     * Check if current call is ajax
     *
     * @param string $action if is ajax $action is set with action string
     *
     * @return bool true if is ajax
     */
    public static function isAjax(&$action = null)
    {
        static $isAjaxAction = null;
        if (is_null($isAjaxAction)) {
            $isAjaxAction = [
                'isAjax' => false,
                'action' => false,
            ];

            $argsInput = SnapUtil::filterInputRequestArray([
                PrmMng::PARAM_CTRL_ACTION => [
                    'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => ['default' => ''],
                ],
                self::ACTION_NAME         => [
                    'filter'  => FILTER_SANITIZE_SPECIAL_CHARS,
                    'flags'   => FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_HIGH,
                    'options' => ['default' => false],
                ],
            ]);

            if ($argsInput[PrmMng::PARAM_CTRL_ACTION] !== 'ajax' || $argsInput[self::ACTION_NAME] === false) {
                $isAjaxAction['isAjax'] = false;
            } else {
                if (($isAjaxAction['isAjax'] = in_array($argsInput[self::ACTION_NAME], self::ajaxActions()))) {
                    $isAjaxAction['action'] = $argsInput[self::ACTION_NAME];
                }
            }
        }

        if ($isAjaxAction['isAjax']) {
            $action = $isAjaxAction['action'];
        }
        return $isAjaxAction['isAjax'];
    }

    /**
     * Get token key by action
     *
     * @param string $action action name
     *
     * @return string
     */
    public static function getTokenKeyByAction($action): string
    {
        return self::ACTION_NAME . $action;
    }

    /**
     * Get token from input
     *
     * @return string
     */
    public static function getTokenFromInput()
    {
        return SnapUtil::sanitizeDefaultInput(INPUT_POST, self::TOKEN_NAME, false);
    }

    /**
     * Get token by action
     *
     * @param string $action action name
     *
     * @return string
     */
    public static function generateToken($action)
    {
        return SecureCsrf::generate(self::getTokenKeyByAction($action));
    }

    /**
     * debugAjaxCallSleep
     *
     * @return void
     */
    protected static function debugAjaxCallSleep(): void
    {
        if (self::DEBUG_AJAX_CALL_SLEEP > 0) { // @phpstan-ignore-line
            sleep(self::DEBUG_AJAX_CALL_SLEEP);
        }
    }
}
