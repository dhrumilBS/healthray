# CSS tools

Four command-line scripts for keeping the stylesheets honest. All CLI-only, all
denied over HTTP by the `.htaccess` here. Run them from the theme root
(`wp-content/themes/stratusx-child`).

| Script | What it does |
|---|---|
| `audit-colours.php` | Inventory: every literal colour left in the CSS, every custom property each file defines, and every `var()` with nothing behind it. |
| `audit-css-overlap.php` | Finds dead CSS: the same selector, in the same media context, with the same property set twice. |
| `apply-palette.php` | Rewrites literal colours to the central palette from an explicit mapping table. |
| `dedupe-tokens.php` | Removes duplicate and colliding custom properties from the per-page stylesheets. |

## The colour system

`style.css` holds the palette in one `:root` block and everything else references
it. Two rules decide what belongs there:

1. A colour earns a token when it is used in more than one file, or several times
   within one file. A one-off accent stays a literal — a token used once is only a
   rename, and it hides the value from whoever reads the rule.
2. Two different values never share a token.

`#fff` and `#000` stay literal in most places. `--hr-surface` means "a card or
panel background"; using it for the label on a navy button would be a lie, and it
would break the day a surface stops being white. White as *ink on a dark fill* is
an absolute, not a palette entry.

### The one file that duplicates on purpose

`css/admin.css` loads inside the block editor's iframe, which cannot see the
front-end `:root`, so it restates the colours it needs. It is excluded from
`apply-palette.php` and from the unresolved-reference check. When a palette
colour that the editor preview shows changes, change it in both places.

## After moving colours between files

```
php tools/css/audit-colours.php --unresolved
```

Must print `none`, or only the five known exceptions below. A `var()` with no
fallback and no definition makes the whole declaration invalid, and the property
silently falls back to its inherited value rather than erroring — which is why
this check exists. It is how `--hr-bg-soft` in `css/alternatives.css` and
`--hr-border` in `style.css` were caught: both referenced tokens that only
`css/single.css` defined, and `css/single.css` does not load on every page that
used them.

Known and expected:

| Reference | Why |
|---|---|
| `--alt-grid-cols` | Set inline by `single-alternatives.php` per post. |
| `--icon-box-bg` | Set inline by `templates/template-lab-state*.php` from an ACF field. |
| `--color`, `--rotate-x`, `--rotate-y` | Pre-existing. `.feature-card::after` and `.parallax-container` reference them but nothing in the theme sets them, and neither selector appears in the rendered HTML of any page checked. Probably dead rules; left alone because proving that needs a content audit, not a CSS one. |

## Verifying a palette change did not change the design

The colour work was done against a full-page computed-style snapshot rather than
by eye. `PROPS` in the snapshot script covers `color`, `backgroundColor`, all four
border colours, `outlineColor`, `boxShadow`, `textDecorationColor`, `fill`,
`stroke`, `backgroundImage`, `caretColor`, `columnRuleColor` and
`textEmphasisColor`, for every element on the page, keyed by a stable DOM path.

Capture before, make the change, capture after, diff. 18 routes at 1440px and 6
at 390px was enough to catch the one real mistake in the pass: `--sky` in
`css/pricing.css` was `#EEF4FF`, one digit off `--brand-tint`'s `#EFF4FF`, and
mapping one onto the other moved 42 elements. It now has its own token,
`--hr-sky`.

Two things a colour snapshot will not catch, so check them separately:

- **Geometry.** `tools/header/verify-assets.php` exists because the local editor's
  CSS formatter twice hoisted `--hrh-pill-radius` into `:root` and kept only one
  of its three breakpoint values, turning the desktop header pill into a 20px
  rounded rectangle. Radius is not a colour; the snapshot passed clean while the
  header was visibly wrong.
- **The block editor.** `css/admin.css` only renders inside wp-admin.

## Re-running the codemods

Both are idempotent — a second run reports `0 substitutions` / no edits. They are
kept rather than deleted because the mapping tables are the record of which
literal became which token, and because the next person to add a stylesheet can
run `apply-palette.php` on it instead of matching hex values by hand.

`apply-palette.php` never touches a custom property *definition*: any declaration
whose property name starts with `--` is left alone, so `:root` blocks stay a
hand-reviewed decision. That guard is the whole correctness argument for the
script, and it was wrong once — the first version looked at the nearest non-space
character before the property name, which for `<comment> --hr-navy: #1b2374` is
the `/` closing the comment rather than a `{` or `;`. Six palette definitions
became `--hr-navy: var(--hr-navy)`, which is circular and therefore invalid, and
several hundred rules fell back to inherited colours. It is a state machine now.
