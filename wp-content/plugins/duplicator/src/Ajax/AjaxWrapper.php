<?php

namespace Duplicator\Ajax;

use Duplicator\Utils\Logging\DupLog;
use Duplicator\Core\CapMng;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapNet;
use Duplicator\Libs\Snap\SnapUtil;
use Duplicator\Utils\Logging\ErrorHandler;
use Exception;
use Throwable;

class AjaxWrapper
{
    /**
     * This function wrap a callback and return always a json well formatted output.
     *
     * check nonce and capability if passed and return a json with this format
     * [
     *      success : bool
     *      data : [
     *          funcData : mixed    // callback return data
     *          message : string    // a message for jvascript func (for example an exception message)
     *          output : string     // all normal output wrapped between ob_start and ob_get_clean
     *                              // if $errorUnespectedOutput is true and output isn't empty the json return an error
     *      ]
     * ]
     *
     * @param callable        $callback              callback function
     * @param string          $nonceaction           nonce action
     * @param string          $nonce                 nonce string
     * @param string|string[] $capabilities          if capability is an empty array don't verify capability
     * @param bool            $errorUnespectedOutput if true thorw exception with unespected optput
     *
     * @return never
     */
    public static function json(
        $callback,
        $nonceaction,
        $nonce,
        $capabilities = [],
        $errorUnespectedOutput = true
    ): void {
        $error = false;

        $result = [
            'funcData' => null,
            'output'   => '',
            'message'  => '',
        ];

        ob_start();
        try {
            ErrorHandler::init();
            $nonce = SnapUtil::sanitizeNSCharsNewline($nonce);
            if (!wp_verify_nonce($nonce, $nonceaction)) {
                DupLog::trace('Security issue');
                throw new Exception('Security issue');
            }

            if ($capabilities !== []) {
                if (is_scalar($capabilities)) {
                    $capabilities = [$capabilities];
                }

                foreach ($capabilities as $cap) {
                    CapMng::can($cap);
                }
            }

            // execute ajax function
            $result['funcData'] = call_user_func($callback);
            DupLog::trace("AJAX FUCION [" . SnapUtil::getCallbackName($callback) . "] RESULT: " . substr(wp_json_encode($result), 0, 100));
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Error executing ajax callback');
            $error             = true;
            $result['message'] = $e->getMessage();
        } finally {
            $result['output'] = ob_get_clean();
            if ($errorUnespectedOutput && !empty($result['output'])) {
                DupLog::trace('Unexpected output: ' . substr($result['output'], 0, 250));
                $error = true;
            }
        }


        if ($error) {
            wp_send_json_error($result);
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * This function wrap a callback and start a chunked file download.
     * The callback must return a file path.
     *
     * @param callable        $callback              Callback function that return a file path for download or false on error
     * @param string          $nonceaction           nonce action
     * @param string          $nonce                 nonce string
     * @param string|string[] $capabilities          if capability is an empty string don't verify capability
     * @param bool            $errorUnespectedOutput if true thorw exception with unespected optput
     *
     * @return never
     */
    public static function fileDownload(
        $callback,
        $nonceaction,
        $nonce,
        $capabilities = [],
        $errorUnespectedOutput = true
    ): void {
        ob_start();
        try {
            ErrorHandler::init();
            $nonce = SnapUtil::sanitizeNSCharsNewline($nonce);
            if (!wp_verify_nonce($nonce, $nonceaction)) {
                DupLog::trace('Security issue');
                throw new Exception('Security issue');
            }

            if ($capabilities !== []) {
                if (is_scalar($capabilities)) {
                    $capabilities = [$capabilities];
                }

                foreach ($capabilities as $cap) {
                    CapMng::can($cap);
                }
            }

            // execute ajax function
            if (($fileInfo = call_user_func($callback)) === false) {
                throw new Exception('Error generating file');
            }

            if (!@file_exists($fileInfo['path'])) {
                throw new Exception('File ' . $fileInfo['path'] . ' not found');
            }

            $result['output'] = ob_get_clean();
            if ($errorUnespectedOutput && !empty($result['output'])) {
                throw new Exception('Unexpected output');
            }

            SnapNet::serveFileForDownload(
                $fileInfo['path'],
                $fileInfo['name'],
                DUPLICATOR_BUFFER_DOWNLOAD_SIZE
            );
        } catch (Throwable $e) {
            DupLog::traceException($e, 'Error executing ajax callback file download');
            SnapNet::serverError500();
        } finally {
            ob_end_clean();
        }
    }
}
