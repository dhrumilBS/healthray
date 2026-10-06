---
inclusion: always
---

# Custom post types

Custom post types on this site are config-driven. Never write a bare
`register_post_type()` or `register_taxonomy()` call in the theme.

- `wp-content/themes/stratusx-child/lib/cpt/definitions.php` — the only file to edit
  when adding, changing or removing a post type or taxonomy
- `wp-content/themes/stratusx-child/lib/cpt/class-hr-cpt-registry.php` — the engine
- `wp-content/themes/stratusx-child/lib/cpt.php` — loader, wired into `functions.php`

## Adding a post type

Add one entry to `definitions.php`. Only `singular` is really needed; the registry
generates the full label set, applies defaults (public, `show_in_rest`,
`has_archive`, supports title/editor/thumbnail/revisions) and flushes rewrite
rules on the next page load. Do not tell the user to re-save permalinks.

Use `args` to override any `register_post_type()` argument, `labels` to override
individual generated labels, `taxonomies` to declare taxonomies inline.

Constraints: post type keys max 20 characters, taxonomy keys max 32.

## Per-post-type assets

Declare CSS/JS under `assets` in the definition with a `context` of `single`, `archive` or `both`. Do not add another `is_singular()` to the
`wp_enqueue_scripts` callback in `functions.php`.

## Templates

Core template naming already works: `archive-{slug}.php` and `single-{slug}.php` in the child theme root. No extra wiring, no `template_include` filter.

## Related conventions

- ACF field groups are registered in PHP via `acf_add_local_field_group()` in `lib/`, one file per post type (see `lib/acf-alternatives.php`)
- Omitting a taxonomy `slug` is sometimes deliberate, so the taxonomy key stays the permalink base. Check before adding one to an existing taxonomy.
