window.DupliJs = window.DupliJs || {};

jQuery(function () {
    var data = {
        action: 'duplicator_process_worker',
    }

    DupliJs.kickme = function () {
        jQuery.ajax({
            async: true,
            type: "POST",
            url: dupli_gateway.ajaxurl,
            timeout: 10000000,
            data: data,
            success: function (respData) {
                if ('ok' != respData) {
                    try {
                        var data = DupliJs.parseJSON(respData);
                    } catch (err) {
                        console.error(err);
                        console.error('JSON parse failed for response data: ' + respData);
                        return false;
                    }
                }
            },
            error: function (data) {
                console.error(data);
            }
        });
    }

    DupliJs.kickme();
    window.setInterval(DupliJs.kickme, dupli_gateway.client_call_frequency);
});
