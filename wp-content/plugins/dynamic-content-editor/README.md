# Dynamic Content Editor Prototype 0.2.0

A modular WordPress Classic Editor framework for reusable widgets with static and dynamic WordPress/ACF values.

## Included widgets

- Dynamic Button
- Dynamic CTA Box
- Comparison Table

## Comparison Table

The Comparison Table widget is designed for layouts like the supplied reference image:

- 1 fixed label column
- 5 fixed product columns
- Product name/logo
- Pricing
- Best For
- Social Profiles
- Ease of Use rating
- Support rating
- Unlimited accordion sections
- Unlimited rows inside each section
- 5 comparison cells per row
- Cell display types: Text, Check, Cross, Stars
- Static or dynamic cell values
- CTA button at the bottom of each product column

The five product columns are fixed by design. The user controls the products and the number of accordion sections/rows.

## Installation

1. Upload `dynamic-content-editor` to `wp-content/plugins/`.
2. Activate **Dynamic Content Editor Prototype**.
3. Make sure the target CPT uses Classic Editor.
4. Open an Alternative/CPT entry.
5. Click **Dynamic Content**.
6. Select **Comparison Table**.
7. Configure the five products.
8. Add accordion sections and rows.
9. Click **Insert Widget**.
10. Save/update the post and view the frontend.

## Restrict to the Alternatives CPT

If the CPT slug is `alternatives`, add:

```php
add_filter('dce_supported_post_types', function () {
    return ['alternatives'];
});
```

This can live in your child theme or another small custom plugin.

## Storage

The editor inserts a shortcode such as:

```text
[dce_widget type="comparison_table" config="BASE64_JSON"]
```

The configuration stores the products, sections, rows, and dynamic field definitions. The final HTML is generated only on the frontend.

## Dynamic values

Dynamic fields can use:

- WordPress
- ACF
- Post Meta

The renderer recursively resolves dynamic values before calling the widget renderer.

## Adding another widget

Create a new class in:

```text
includes/widgets/
```

and register it through:

```php
add_action('dce_register_widgets', function ($registry) {
    $registry->register([
        'name' => 'my_widget',
        'label' => 'My Widget',
        'fields' => [],
        'renderer' => function ($data) {
            return '<div>' . esc_html($data['title']) . '</div>';
        },
    ]);
});
```

For a complex custom editor UI, add an editor builder identifier and extend `assets/js/editor.js`. The Comparison Table demonstrates this pattern with:

```php
'editor' => [
    'builder' => 'comparison_table',
],
```

## Notes

This version intentionally keeps the comparison table as one independently registered widget while reusing the existing registry, resolver, renderer, shortcode, and TinyMCE layers.

The next iteration can add reusable saved templates, ACF field dropdown discovery, edit/reopen support, row drag-and-drop, richer cell types, and conditional logic without changing the fundamental widget architecture.


## Comparison Table: static-only

The Comparison Table widget intentionally does **not** expose WordPress, ACF, or Post Meta dynamic sources. Product data, fixed rows, accordion section titles, row labels, and cell values are entered as static values in the widget builder.

The rest of the framework still supports dynamic values for other widgets such as the Button and CTA widgets.
