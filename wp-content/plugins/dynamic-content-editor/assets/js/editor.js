(function ($) {
    'use strict';

    var widgets = window.DCE_WIDGETS || {};

    window.DCE_OpenModal = function (editor) {
        var names = Object.keys(widgets);

        if (!names.length) {
            alert('No Dynamic Content widgets are registered.');
            return;
        }

        var modal = $('<div class="dce-modal-overlay"></div>');
        var box = $('<div class="dce-modal"></div>');
        var header = $('<div class="dce-modal__header"></div>');
        var body = $('<div class="dce-modal__body"></div>');
        var footer = $('<div class="dce-modal__footer"></div>');

        header.append('<h2>Insert Dynamic Content</h2>');

        var close = $('<button type="button" class="dce-modal__close" aria-label="Close">&times;</button>');
        header.append(close);

        var typeSelect = $('<select class="dce-control dce-widget-type"></select>');
        names.forEach(function (name) {
            typeSelect.append($('<option>').val(name).text(widgets[name].label));
        });

        body.append(
            $('<label class="dce-label">Widget Type</label>'),
            typeSelect
        );

        var fieldsWrap = $('<div class="dce-fields"></div>');
        body.append(fieldsWrap);

        var cancel = $('<button type="button" class="button">Cancel</button>');
        var insert = $('<button type="button" class="button button-primary">Insert Widget</button>');
        footer.append(cancel, insert);

        box.append(header, body, footer);
        modal.append(box);
        $('body').append(modal);

        function closeModal() {
            modal.remove();
        }

        function sourceOptions(allowed) {
            var sources = [{ value: 'static', label: 'Static Value' }];

            if (Array.isArray(allowed)) {
                if (allowed.indexOf('wp') !== -1) {
                    sources.push({ value: 'wp', label: 'WordPress' });
                }
                if (allowed.indexOf('acf') !== -1) {
                    sources.push({ value: 'acf', label: 'ACF Field' });
                }
                if (allowed.indexOf('meta') !== -1) {
                    sources.push({ value: 'meta', label: 'Post Meta' });
                }
            }

            return sources;
        }

        function wpOptions(fieldType) {
            if (fieldType === 'url') {
                return [['permalink', 'Current Post URL']];
            }

            return [
                ['post_title', 'Post Title'],
                ['post_excerpt', 'Post Excerpt'],
                ['author_name', 'Author Name'],
                ['date', 'Publish Date']
            ];
        }

        function createStaticControl(name, field) {
            var control;
            var value = field.default || '';

            if (field.type === 'textarea') {
                control = $('<textarea class="dce-control dce-static-value"></textarea>').val(value);
            } else if (field.type === 'select') {
                control = $('<select class="dce-control dce-static-value"></select>');

                Object.keys(field.options || {}).forEach(function (key) {
                    control.append($('<option>').val(key).text(field.options[key]));
                });

                control.val(value);
            } else {
                control = $('<input class="dce-control dce-static-value">')
                    .attr('type', field.type === 'url' ? 'url' : 'text')
                    .val(value);
            }

            control.attr('data-field', name);
            return control;
        }

        function createField(name, field) {
            var wrapper = $('<div class="dce-field"></div>');
            var label = $('<label class="dce-label"></label>').text(field.label || name);
            wrapper.append(label);

            var isDynamic = Array.isArray(field.dynamic) && field.dynamic.length;

            if (!isDynamic) {
                return wrapper.append(createStaticControl(name, field));
            }

            var source = $('<select class="dce-control dce-source"></select>');
            sourceOptions(field.dynamic).forEach(function (item) {
                source.append($('<option>').val(item.value).text(item.label));
            });

            var valueWrap = $('<div class="dce-value-wrap"></div>');

            function renderValue() {
                valueWrap.empty();

                if (source.val() === 'static') {
                    valueWrap.append(createStaticControl(name, field));
                    return;
                }

                if (source.val() === 'wp') {
                    var wpSelect = $('<select class="dce-control dce-dynamic-key"></select>');
                    wpOptions(field.type).forEach(function (item) {
                        wpSelect.append($('<option>').val(item[0]).text(item[1]));
                    });
                    valueWrap.append(wpSelect);
                    return;
                }

                var input = $('<input type="text" class="dce-control dce-dynamic-key">');

                if (source.val() === 'acf') {
                    input.attr('placeholder', 'ACF field name, e.g. button_url');
                } else {
                    input.attr('placeholder', 'Post meta key');
                }

                valueWrap.append(input);
            }

            source.on('change', renderValue);

            wrapper.append(source, valueWrap);
            renderValue();

            return wrapper;
        }

        function renderGenericFields(widget) {
            fieldsWrap.empty();

            Object.keys(widget.fields || {}).forEach(function (name) {
                fieldsWrap.append(createField(name, widget.fields[name]));
            });
        }

        function dynamicValue(source, key, value) {
            return {
                source: source,
                key: source === 'static' ? '' : key,
                value: source === 'static' ? value : ''
            };
        }

        function fieldInput(label, type, defaultValue) {
            var wrap = $('<div class="dce-builder-field"></div>');
            wrap.append($('<label class="dce-label"></label>').text(label));

            var input = $('<input class="dce-control">')
                .attr('type', type || 'text')
                .val(defaultValue || '');

            wrap.append(input);

            return {
                wrap: wrap,
                input: input
            };
        }

        function staticBuilderField(label, type, defaultValue, placeholder) {
            var wrap = $('<div class="dce-builder-field"></div>');
            wrap.append($('<label class="dce-label"></label>').text(label));

            var input = $('<input class="dce-control dce-builder-value">')
                .attr('type', type || 'text')
                .val(defaultValue || '');

            if (placeholder) {
                input.attr('placeholder', placeholder);
            }

            wrap.append(input);

            return {
                wrap: wrap,
                input: input
            };
        }

        function createProduct(index) {
            var card = $('<div class="dce-builder-product"></div>');
            card.append('<h3>Product ' + (index + 1) + '</h3>');

            // Comparison table values are intentionally static.
            // Dynamic WordPress/ACF/Post Meta sources are not exposed here.
            var name = staticBuilderField('Product Name', 'text', '', 'e.g. SocialPilot');
            var logo = staticBuilderField('Logo URL', 'url', '', 'https://example.com/logo.svg');
            var pricing = staticBuilderField('Pricing', 'text', '', 'e.g. Starts at $25/month');
            var bestFor = staticBuilderField('Best For', 'text', '', 'e.g. Small Businesses');
            var profiles = staticBuilderField('Social Profiles', 'text', '', 'e.g. 7');
            var ease = staticBuilderField('Ease of Use (0-5)', 'number', '0', '0-5');
            var support = staticBuilderField('Support (0-5)', 'number', '0', '0-5');
            var ctaText = staticBuilderField('CTA Text', 'text', 'Try for Free', 'e.g. Try for Free');
            var ctaUrl = staticBuilderField('CTA URL', 'url', '', 'https://example.com/');

            [name, logo, pricing, bestFor, profiles, ease, support, ctaText, ctaUrl].forEach(function (item) {
                card.append(item.wrap);
            });

            return {
                card: card,
                read: function () {
                    return {
                        name: name.input.val(),
                        logo: logo.input.val(),
                        pricing: pricing.input.val(),
                        best_for: bestFor.input.val(),
                        social_profiles: profiles.input.val(),
                        ease: ease.input.val(),
                        support: support.input.val(),
                        cta_text: ctaText.input.val(),
                        cta_url: ctaUrl.input.val()
                    };
                }
            };
        }

        function createCell() {
            var wrap = $('<div class="dce-cell-builder"></div>');

            var type = $('<select class="dce-control"></select>')
                .append('<option value="text">Text</option>')
                .append('<option value="check">Check ✓</option>')
                .append('<option value="cross">Cross ×</option>')
                .append('<option value="stars">Stars</option>');

            var value = $('<input class="dce-control" type="text" placeholder="Cell value">');

            type.on('change', function () {
                if (type.val() === 'check' || type.val() === 'cross') {
                    value.hide();
                } else {
                    value.show();
                }
            });

            wrap.append(
                $('<label class="dce-small-label">Display</label>'),
                type,
                $('<label class="dce-small-label">Value</label>'),
                value
            );

            return {
                wrap: wrap,
                read: function () {
                    var t = type.val();

                    return {
                        type: t,
                        value: (t === 'check' || t === 'cross') ? '' : value.val()
                    };
                }
            };
        }

        function createRow() {
            var row = $('<div class="dce-builder-row"></div>');
            var label = staticBuilderField('Row Label', 'text', '', 'e.g. Bulk Scheduling');
            row.append(label.wrap);

            var cells = [];
            for (var i = 0; i < 5; i++) {
                var cell = createCell();
                cells.push(cell);
                row.append(cell.wrap);
            }

            var remove = $('<button type="button" class="button-link-delete">Remove Row</button>');
            row.append(remove);

            remove.on('click', function () {
                row.remove();
            });

            return {
                row: row,
                read: function () {
                    return {
                        label: label.input.val(),
                        cells: cells.map(function (cell) {
                            return cell.read();
                        })
                    };
                }
            };
        }

        function createSection() {
            var section = $('<div class="dce-builder-section"></div>');
            var header = $('<div class="dce-builder-section-header"></div>');
            var title = fieldInput('Section Title', 'text', '');
            var open = $('<label class="dce-inline-check"><input type="checkbox"> Open by default</label>');
            var remove = $('<button type="button" class="button-link-delete">Remove Section</button>');

            header.append(title.wrap, open, remove);

            var rowsWrap = $('<div class="dce-builder-rows"></div>');
            var addRow = $('<button type="button" class="button">+ Add Row</button>');

            section.append(header, rowsWrap, addRow);

            var rows = [];

            function addNewRow() {
                var row = createRow();
                rows.push(row);
                rowsWrap.append(row.row);
            }

            addRow.on('click', addNewRow);

            remove.on('click', function () {
                section.remove();
            });

            addNewRow();

            return {
                section: section,
                read: function () {
                    return {
                        title: title.input.val(),
                        open: open.find('input').is(':checked'),
                        rows: rows.filter(function (row) {
                            return $.contains(document, row.row[0]);
                        }).map(function (row) {
                            return row.read();
                        })
                    };
                }
            };
        }

        function renderComparisonBuilder() {
            fieldsWrap.empty();

            var intro = $('<div class="dce-builder-intro"></div>').html(
                '<strong>Comparison Table</strong><p>Five product columns are fixed. All comparison values are static. Add as many accordion sections and comparison rows as you need.</p>'
            );
            fieldsWrap.append(intro);

            var productsTitle = $('<h3 class="dce-builder-heading">Products — 5 fixed columns</h3>');
            fieldsWrap.append(productsTitle);

            var productsGrid = $('<div class="dce-products-grid"></div>');
            var products = [];

            for (var i = 0; i < 5; i++) {
                var product = createProduct(i);
                products.push(product);
                productsGrid.append(product.card);
            }

            fieldsWrap.append(productsGrid);

            var sectionsTitle = $('<div class="dce-builder-section-title"><h3>Accordion Sections</h3></div>');
            var sectionsWrap = $('<div class="dce-builder-sections"></div>');
            var addSection = $('<button type="button" class="button button-secondary">+ Add Accordion Section</button>');

            fieldsWrap.append(sectionsTitle, sectionsWrap, addSection);

            var sections = [];

            function addNewSection() {
                var section = createSection();
                sections.push(section);
                sectionsWrap.append(section.section);
            }

            addSection.on('click', addNewSection);

            addNewSection();

            insert.off('click').on('click', function () {
                var data = {
                    products: products.map(function (product) {
                        return product.read();
                    }),
                    sections: sections.filter(function (section) {
                        return $.contains(document, section.section[0]);
                    }).map(function (section) {
                        return section.read();
                    })
                };

                var encoded = btoa(unescape(encodeURIComponent(JSON.stringify(data))));

                editor.insertContent(
                    '[dce_widget type="comparison_table" config="' + encoded + '"]'
                );

                closeModal();
            });
        }

        function renderSelectedWidget() {
            var widget = widgets[typeSelect.val()];

            insert.off('click');

            if (widget.editor && widget.editor.builder === 'comparison_table') {
                renderComparisonBuilder();
                return;
            }

            renderGenericFields(widget);

            insert.on('click', function () {
                var config = {};
                var valid = true;

                Object.keys(widget.fields || {}).forEach(function (name) {
                    var field = widget.fields[name];
                    var fieldWrap = fieldsWrap.children('.dce-field').eq(
                        Object.keys(widget.fields).indexOf(name)
                    );

                    var sourceControl = fieldWrap.find('.dce-source').first();
                    var source = sourceControl.length ? sourceControl.val() : 'static';
                    var value = source === 'static'
                        ? fieldWrap.find('.dce-static-value').first().val() || ''
                        : fieldWrap.find('.dce-dynamic-key').first().val() || '';

                    if (field.required && !value) {
                        valid = false;
                        fieldWrap.addClass('dce-field--error');
                    } else {
                        fieldWrap.removeClass('dce-field--error');
                    }

                    config[name] = {
                        source: source,
                        value: source === 'static' ? value : '',
                        key: source === 'static' ? '' : value
                    };
                });

                if (!valid) {
                    alert('Please fill in the required fields.');
                    return;
                }

                var encoded = btoa(unescape(encodeURIComponent(JSON.stringify(config))));

                editor.insertContent(
                    '[dce_widget type="' + typeSelect.val() + '" config="' + encoded + '"]'
                );

                closeModal();
            });
        }

        typeSelect.on('change', renderSelectedWidget);
        close.on('click', closeModal);
        cancel.on('click', closeModal);

        renderSelectedWidget();
    };

})(jQuery);
