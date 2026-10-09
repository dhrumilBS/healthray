<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapJson;

?>
<script>
    DUPX.startAjaxDbGunzip = function(isTheFirstCall, successCallback) {
        if (isTheFirstCall) {
            DUPX.pageComponents.resetTopMessages().showProgress({
                'title': 'Decompressing Database',
                'perc': '0%',
                'secondary': 'Preparing database dump',
                'bottomText': '<i>Keep this window open during the decompression process.</i><br/>' +
                    '<i>This can take several minutes for large dumps.</i>'
            });
        }

        let gunzipAction = <?php echo SnapJson::jsonEncode(DUPX_Ctrl_ajax::ACTION_DB_GUNZIP); ?>;
        let gunzipToken = <?php echo SnapJson::jsonEncode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_DB_GUNZIP)); ?>;

        let retryAttempt = 0;

        DUPX.StandarJsonAjaxWrapper(
            gunzipAction,
            gunzipToken, {},
            function(data, textStatus, jqXHR) {
                let progressUpdate = {
                    'perc': data.actionData.perc,
                    'secondary': data.actionData.processedFiles,
                    'notice': ''
                };
                if (typeof data.actionData.title === 'string') {
                    progressUpdate.title = data.actionData.title;
                }
                DUPX.progress.update(progressUpdate);

                switch (data.actionData.pass) {
                    case 1:
                        if (typeof successCallback === "function") {
                            successCallback(data);
                        }
                        break;
                    case -1:
                        DUPX.startAjaxDbGunzip(false, successCallback);
                        break;
                    default:
                        const result = {
                            'success': false,
                            'message': 'Unknown pass value :' + data.actionData.pass,
                            'errorContent': {
                                'pre': '',
                                'html': '',
                                'iframe': ''
                            },
                            'actionData': null
                        };
                        DUPX.ajaxErrorDisplayHideError(result, textStatus, jqXHR);
                        return false;
                }

                return true;
            },
            function(result, textStatus, jqXHR) {
                let status = "<b>Server Code:</b> " + jqXHR.status + "<br/>";
                status += "<b>Status:</b> " + jqXHR.statusText + "<br/>";
                status += "<b>Response:</b> " + jqXHR.responseText + "<hr/>";
                result.errorContent.html += status;
                DUPX.ajaxErrorDisplayHideError(result, textStatus, jqXHR);
            }, {
                retryOnFailure: true,
                numberOfAttempts: 2,
                delayRetryOnFailure: 5000,
                callbackOnRetry: function(data, textStatus, jqXHR, options) {
                    retryAttempt++;

                    DUPX.progress.update({
                        'notice': 'Decompression failed: ' + data.message + ',<br>' +
                            'wait ' + (options.delayRetryOnFailure / 1000) + ' seconds and retry.<br>' +
                            '<b>' + DUPX.stringifyNumber(retryAttempt) + ' attempt</b>'
                    });
                    console.log('Callback on retry', data);
                }
            }
        );
    };
</script>
