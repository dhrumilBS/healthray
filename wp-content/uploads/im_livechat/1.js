
            
                window.addEventListener('load', function () {
                    odoo.define('im_livechat.loaderData', function() {
                        return {
                            isAvailable: true,
                            serverUrl: "http://139.59.75.74:8069",
                            options: {"header_background_color": "#875A7B", "button_background_color": "#875A7B", "title_color": "#FFFFFF", "button_text_color": "#FFFFFF", "button_text": "Have a Question? Chat with us.", "input_placeholder": false, "default_message": "Hello, how may I help you?", "channel_name": "Healthray.com", "channel_id": 1, "current_partner_id": 4, "default_username": "Visitor"},
                        };
                    });
                });