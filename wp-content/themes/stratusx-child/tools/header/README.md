# Header tools

Fixtures and checks for the header redesign. The redesign is presentation only —
CSS, a small script, and one filter that upgrades the menu wrapper to a `<nav>`
landmark. The navigation itself was not touched. These scripts are how that is
proven rather than asserted.

## Where the header actually lives

The header is **not** a theme template. It is an Elementor Pro Theme Builder
template, post **567** ("Healthray Theme Header"), assigned to `include/general`,
holding two widgets: the site logo and the **Max Mega Menu** widget for the
`primary_navigation` location. `base.php` never reaches its own header partial
because `elementor_theme_do_location('header')` returns true.

The navigation content lives in three places, none of them in the theme:

- menu **25** (`Healthray Main Menu`), 38 items, 6 top level
- per-item `_megamenu` post meta, where Max Mega Menu Pro "replacements" inject
  inline HTML for the product rows and ABHA cards
- three shortcodes: `[speciality_megamenu]` (28 specialty links),
  `[healthray_cta]`, `[healthray_megamenu_notice]`

`/facebook-campaign/` (page 30714) uses a **different** header template, 30745,
which contains no mega menu. Nothing here affects it.

## Files

| File | Purpose |
|---|---|
| `dump-nav-tree.php` | Menu 25 straight from the database: ids, parents, depth, order, titles, URLs, targets, descriptions, classes and the full `_megamenu` blob. |
| `dump-rendered-nav.php` | Every link as the browser sees it, parsed out of real HTML. Needed because the database dump misses everything the shortcodes render. |
| `diff-nav.php` | Compares two dumps and fails on any lost or added link, label, URL or icon. |
| `verify-assets.php` | Pre-deploy integrity gate. 46 behavioural assertions over the header CSS, JS and PHP. Run it before every upload. |
| `nav-before.json` / `rendered-before.json` | Baseline, captured before any change. These are the fixtures — keep them. |
| `.htaccess` | Denies HTTP access, matching `tools/alternatives`. The scripts also refuse to run outside the CLI SAPI. |

## Run this before uploading

```
php tools/header/verify-assets.php
```

Exit code 0 means safe. It exists because something in the local toolchain
repeatedly reformatted `css/header.css` and, twice, silently corrupted it:

- **Media-query-unaware merging.** Rules sharing a selector across different
  `@media` blocks were merged and one value kept, so `border-radius: 18px` from
  the 480px block landed in the base rule and the desktop header stopped being a
  pill.
- **Cross-`@media` deduplication.** Two byte-identical rules in different media
  queries were collapsed into one, dropping the `prefers-contrast` blur reset.
- **Hoisting a lone custom property into `:root`.** All three
  `--hrh-pill-radius` declarations were collapsed into a single
  `:root { --hrh-pill-radius: 20px }`, so the desktop pill rendered as a 20px
  rounded rectangle. Each attempt to work around this — a custom property, then
  three different selectors, then pairing each with a normal declaration — the
  formatter eventually defeated.

**This particular hazard is now gone rather than guarded against.** The header
container radius is a single device-independent `18px`, declared once in `:root`,
so there are no breakpoint values to merge, hoist or get out of step. Three
assertions became two, and the failure mode no longer exists.

The other two failure modes still apply to the rest of the file. "Same selector,
same property, different media query" describes all responsive CSS — 84 such pairs
exist here — so there is no way to restructure around a tool that does this.
Verify instead. If a check fails, restore the file rather than uploading it.

**Before deploying, close `css/header.css` in the editor or turn its CSS formatter
off.** The verifier catches the damage but only if you run it.

The `*-after.json` files are generated output, not fixtures. Write them when you
run a check and delete them afterwards; they are not committed.

## Finding dead CSS

Moved to `tools/css/` — see that directory's README. `audit-css-overlap.php`
covers all the front-end stylesheets, not just the header's.

## Running the checks

From the theme root (`wp-content/themes/stratusx-child`):

```
php tools/header/dump-nav-tree.php      > tools/header/nav-after.json
php tools/header/dump-rendered-nav.php  > tools/header/rendered-after.json
php tools/header/diff-nav.php rendered-before.json rendered-after.json
php tools/header/diff-nav.php nav-before.json nav-after.json
rm tools/header/nav-after.json tools/header/rendered-after.json
```

Worth running once on production after deploying, to confirm the live menu still
exposes every link. `dump-rendered-nav.php --url=https://healthray.com/` will
point it at the live origin.

Both diffs must print `PASS`. Exit code is 1 on any difference, so this drops
straight into a hook or CI step.

`dump-rendered-nav.php` fetches `http://localhost/healthray/` by default; pass
`--url=` for another origin. `diff-nav.php` tolerates the UTF-8 BOM that
PowerShell's `Set-Content -Encoding utf8` adds.

## The contract these checks enforce

| Metric | Value |
|---|---|
| anchors in the nav | 68 (65 with an `href`) |
| unique URLs | 53 |
| images | 38 |
| inline SVGs | 10 |
| top level items | 6 |
| menu items in the database | 38 |

Per panel: Products 9 links / 6 images, Speciality 30 / 28, ABHA 6 / 4,
Resources 14 / 0.

The three anchors without an `href` are layout wrappers — menu items 66509
("Products"), 66512 ("abha") and 67434 ("ressss") — which exist only to carry a
column span. `css/header.css` collapses them with
`a.mega-menu-link:not([href])`, which is a more reliable discriminator than
`.mega-disable-link`.

## If you change the menu

Re-baseline deliberately, never to make a failing diff go away:

```
php tools/header/dump-nav-tree.php     > tools/header/nav-before.json
php tools/header/dump-rendered-nav.php > tools/header/rendered-before.json
```

## Things that will bite you

- **Do not renumber menu items.** `css/header.css` targets 66373 (Resources),
  66508 / 66512 / 66515 (ABHA and Speciality) by ID.
- **Do not put `overflow: hidden` on the pill or on `.elementor-container`.**
  Both are ancestors of the mega panels and would clip them.
- **Do not put `backdrop-filter` on the pill below 992px.** It makes the pill a
  containing block for `position: fixed`, and the off-canvas drawer is fixed —
  it ends up anchored to the pill instead of the viewport.
- **Do not change the pill's width or height on scroll.** Max Mega Menu measures
  `.elementor-container` once and writes an inline width onto each panel; a
  narrowing pill leaves that stale and the panels spill past the viewport. A
  height change causes a layout jump, because the header is `position: sticky`
  and always occupies its space in flow.
- Panel width comes from `data-panel-width=".elementor-container"`, which now
  resolves to the pill's inner box. The `width` in `css/header.css` is only a
  fallback for when the plugin's script does not run.
