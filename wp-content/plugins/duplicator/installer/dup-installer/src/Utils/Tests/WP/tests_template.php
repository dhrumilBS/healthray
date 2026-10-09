<?php

namespace Duplicator\Installer\Utils\Tests\WP;

use Duplicator\Installer\Utils\Autoloader;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Installer\Utils\Tests\MessageCustomizer;
use DUPX_NOTICE_ITEM;
use DUPX_NOTICE_MANAGER;
use Exception;

// phpcs:disable
die(); // [REMOVE LINE BY SCRIPT] don't remove/change this *********************************

if (!defined('DUPXABSPATH')) { // @phpstan-ignore-line
    define('DUPXABSPATH', __DIR__);
}

if (!defined('DUPX_INIT')) {
    define('DUPX_INIT', '$_$_DUPX_INIT_$_$');
}
// phpcs:enable

require_once(DUPX_INIT . '/src/Utils/Autoloader.php');
Autoloader::register();

require_once(DUPX_INIT . '/classes/utilities/class.u.notices.manager.php');
$GLOBALS["NOTICES_FILE_PATH"] = '$_$_NOTICES_FILE_PATH_$_$';

$GLOBALS["TEST_SCRIPT"] = SnapUtil::sanitizeDefaultInput(INPUT_GET, 'dupli_test_script_name');
ob_start();
TestsErrorHandler::register();
TestsErrorHandler::setShutdownCallabck(function ($errors): void {

    $nManager   = DUPX_NOTICE_MANAGER::getInstance();
    $scriptName = basename($GLOBALS["TEST_SCRIPT"]);

    if (!file_exists($GLOBALS["TEST_SCRIPT"])) {
        $longMessage = "- The file " . $GLOBALS["TEST_SCRIPT"] . " doesn't exist.\n";
        $data        = [
            'shortMsg'    => "Some files required for the final tests were not found.",
            'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
            'longMsg'     => $longMessage,
            'sections'    => 'general',
        ];

        $nManager->addBothNextAndFinalReportNotice($data, DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'test_file_not_found');

        if ($nManager->saveNotices()) {
            echo json_encode(true);
        } else {
            echo json_encode(false);
        }

        return;
    }

    $scriptNameId = str_replace(['.', '-', '#'], '_', $scriptName);
    $firstFatal   = true;
    $firstNotice  = true;

    switch ($scriptName) {
        case 'index.php':
            $shortMessageFatal  = 'Fatal error on WordPress front-end tests!';
            $shortMessageNotice = 'Warnings or notices on WordPress front-end tests!';
            $fatalErrorLevel    = DUPX_NOTICE_ITEM::CRITICAL;
            break;
        case 'wp-login.php':
            $shortMessageFatal  = 'Fatal error on WordPress login tests!';
            $shortMessageNotice = 'Warnings or notices on WordPress backend tests!';
            $fatalErrorLevel    = DUPX_NOTICE_ITEM::FATAL;
            break;
        default:
            $shortMessageFatal  = 'Fatal error on php script ' . $scriptName;
            $shortMessageNotice = 'Warnings or notices on php script ' . $scriptName;
            $fatalErrorLevel    = DUPX_NOTICE_ITEM::CRITICAL;
            break;
    }

    foreach ($errors as $error) {
        $addBeforeNotice = false;
        switch ($error['error_cat']) {
            case TestsErrorHandler::ERR_TYPE_ERROR:
                $noticeId     = 'wptest_fatal_error_' . $scriptNameId;
                $errorLevel   = $fatalErrorLevel;
                $shortMessage = $shortMessageFatal;
                if ($firstFatal) {
                    $addBeforeNotice = true;
                    $firstFatal      = false;
                }

                break;
            case TestsErrorHandler::ERR_TYPE_NOTICE:
            case TestsErrorHandler::ERR_TYPE_DEPRECATED:
            case TestsErrorHandler::ERR_TYPE_WARNING:
            default:
                $noticeId     = 'wptest_notice_' . $scriptNameId;
                $errorLevel   = DUPX_NOTICE_ITEM::NOTICE;
                $shortMessage = $shortMessageNotice;
                if ($firstNotice) {
                    $addBeforeNotice = true;
                    $firstNotice     = false;
                }
                break;
        }

        $longMessage  = $addBeforeNotice ? 'SCRIPT FILE TEST: ' . $GLOBALS["TEST_SCRIPT"] . "\n\n" : '';
        $longMessage .= TestsErrorHandler::errorToString($error) . "\n-----\n\n";
        $longMessage .= "For solutions to these issues see the online FAQs \nhttps://duplicator.com/knowledge-base \n\n";

        MessageCustomizer::applyAllNoticeCustomizations($shortMessage, $longMessage, $noticeId);
        $data = [
            'shortMsg'    => $shortMessage,
            'level'       => $errorLevel,
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
            'longMsg'     => $longMessage,
            'sections'    => 'general',
        ];
        if ($errorLevel == DUPX_NOTICE_ITEM::FATAL) {
            $nManager->addBothNextAndFinalReportNotice($data, DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, $noticeId);
        } else {
            $nManager->addFinalReportNotice($data, DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, $noticeId);
        }
    }

    if ($nManager->saveNotices()) {
        echo json_encode(true);
    } else {
        echo json_encode(false);
    }
});

$_SERVER['REQUEST_URI'] = '/';
if (file_exists($GLOBALS["TEST_SCRIPT"])) {
    require_once($GLOBALS["TEST_SCRIPT"]);
} else {
    throw new Exception('test script file ' . $GLOBALS["TEST_SCRIPT"] . ' doesn\'t exist');
}
