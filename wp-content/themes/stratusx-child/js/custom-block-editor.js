(function () {

    tinymce.create('tinymce.plugins.custom_block', {

        init: function (editor) {

            editor.addButton('custom_block', {

                text: 'Custom Block',

                icon: false,

                onclick: function () {

                    editor.insertContent(
                        '[custom_block]'
                    );

                }

            });

        },

        createControl: function (name, controlManager) {
            return null;
        }

    });

    tinymce.PluginManager.add(
        'custom_block',
        tinymce.plugins.custom_block
    );

})();