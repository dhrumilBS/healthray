(function () {
    tinymce.create('tinymce.plugins.dce_dynamic_content', {
        init: function (editor) {
            editor.addButton('dce_dynamic_content', {
                text: 'Dynamic Content',
                icon: false,
                onclick: function () {
                    window.DCE_OpenModal(editor);
                }
            });
        },
        createControl: function () {
            return null;
        }
    });

    tinymce.PluginManager.add(
        'dce_dynamic_content',
        tinymce.plugins.dce_dynamic_content
    );
})();
