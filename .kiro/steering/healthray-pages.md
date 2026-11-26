---
inclusion: always
---

# Healthray page template builder

Builds a coded page template for healthray.com's `stratusx-child` child theme.
The conventions below come from the pages built so far (Home, EMR, EHR, Clinic,
LIMS, Pharmacy, HIMS, IPD, OPD, Modules hub, Speciality, Solutions, the PPC
pages, ABDM). Follow them exactly; each one exists because deviating broke
something before.

## 0. Where things are

- Theme repo (git): `D:\xampp\htdocs\healthray\wp-content\themes\stratusx-child`
  (repo root `D:\xampp\htdocs\healthray`). Work directly in it; **do not commit
  unless the user asks**.
- Local WordPress copy: `http://localhost/healthray/` with database
  `wp_healthray` (`D:\xampp\mysql\bin\mysql -uroot wp_healthray`). PHP:
  `D:\xampp\php\php.exe`. Build and preview here; never write to live.
- Live database (read-only checks): `D:\xampp\htdocs\salvadore\tools\healthray\conn.php`,
  key `landing`. Multisite: the main site is prefix `wp_`; `wp_2_` .. `wp_9_`
  are country subsites (en-ng, en-za, ...). Don't pick the first `*_posts`
  table you find.
- `functions.php` has a section index at the top (search `== N.`). Section
  numbers are used below.

## 1. Pick the page family first

Every page belongs to one family. The family decides the CSS base, body class
and wiring - don't mix them.

| Family | Pages (ID) | Base | Scope |
|---|---|---|---|
| **Product landing** (common-landing) | EMR 79713, EHR 79754, Clinic 79865, LIMS 79924, Pharmacy 79928, HIMS 81124/81775, ABDM 27377, ABHA 26834, DHIS 26844, PM-JAY 26475, Our Story 31213 | `css/common-landing.css` + `css/{x}-software.css`, both inlined in `wp_head` | body `landing-page {x}-landing`, rules `.{x}-landing .foo` |
| **hx landing** | IPD 82361, OPD 82363, Modules hub 82372 | Own CSS only (no common-landing), starts with an `.{x}-landing .hx :where(...)` reset block | `<main class="hx" id="{x}-main">`, rules `.{x}-landing .hx .foo` |
| **Speciality** | 47 pages on `temp-speciality-landing.php` | ACF fields + `template-parts/speciality/` | Do NOT make a new template: add the page in admin and fill ACF |
| **PPC / ads** | `temp-ppc-{product}-{channel}.php` (12 pages) | Standalone base (no site header/footer), `css/ppc-{key}.css` + `ppc-popup.css` auto-loaded by key | body `ppc-{key}` (added automatically) |
| **Home** | `temp-home-hms.php` (82432) | `css/home-hms.css` enqueued | Don't create another |

Default for a new marketing/information page: **Product landing**. Use **hx**
only if the user's mockup is in that style. Ask if unclear.

## 2. Before writing anything

1. **Page ID and slug.** For a rebuild, read them from the database. For a new
   page, ask the user - don't invent IDs.
2. **Body class**: `{x}-landing` (e.g. `abdm-landing`). Pick from the slug and
   tell the user.
3. **Fetch live CSS and compare with local**:
   `https://healthray.com/wp-content/themes/stratusx-child/css/common-landing.css`
   (and the page family's file). If live and local differ, stop and tell the user -
   the local theme is out of sync.
4. **Yoast focus keyword, title and description** of the page (postmeta
   `_yoast_wpseo_focuskw`, `_yoast_wpseo_title`, `_yoast_wpseo_metadesc`). The
   H1 and copy should target the focus keyword.
5. **Mockup**: read the whole file before writing (800-1200 lines; read in
   chunks, skip nothing).

### Rebuilding an Elementor page in code

- Readable content: `wp_posts.post_content` (Elementor's HTML fallback). Widget
  structure: latest `_elementor_data` row. Strip tags into a heading/paragraph
  outline before planning.
- List every shortcode and HTML widget in `_elementor_data`. **Functional
  shortcodes must be kept** - e.g. `[adharAuthForm]` (ABHA card creation, ABHA
  page), `[journey_timeline]` (Our Story). Contact forms become the shared lead
  form (see section 4) unless the user says otherwise.
- `[elementor-template id="26968"]` is the "Get Mobile App" block and
  `[elementor-template id="26869"]` the "How we protect your data" + CTA form
  block. Drop or replace them only if they don't fit the page; say which.
- **Duplicate postmeta rows exist** on many live pages (2-4 identical
  `_wp_page_template` / `_elementor_edit_mode` rows). Count pages with
  `DISTINCT`/`EXISTS`, and when setting a template locally update **all** rows
  for that key. In WP admin, saving the template updates all of them.
- A page that keeps `_elementor_edit_mode = builder` but uses a template that
  never calls `the_content()` is a coded page (EHR, Become a Partner, Thank You,
  Botphonic Contact/Pricing/Multilingual). Check the template for
  `the_content()` before calling a page "Elementor"; `templates/alternative.php`
  does call it, so those pages are still Elementor.
- **Clear the old layout once the coded page is approved.** The `builder` flag
  alone is harmless (EHR keeps it), but the stored layout is not: Elementor
  still prints CSS for `_elementor_data`, and Yoast still reads `post_content`.
  On ABDM (27377, 25 Nov 2026) the leftovers added ~32 KB of inline Elementor
  CSS (41.8 KB against ~9.9 KB on EHR/EMR/IPD), three widget stylesheets
  (heading, image-box, toggle), and kept 16 old images in Yoast's page sitemap.
  EHR, EMR, Clinic, IPD and OPD have both cleared. Ask first (it deletes the old
  layout), then locally: `delete_post_meta()` for `_elementor_data` (all rows)
  and `_elementor_css`, and `wp_update_post()` with an empty `post_content`.
  Use WordPress functions, not raw SQL: the update saves a revision of the old
  content and makes Yoast refresh its sitemap. Leave `_elementor_page_settings`
  alone - the parent theme keeps header options (`themo_transparent_header`)
  there. Add the same step for live to the deploy steps.

## 3. Content rules

- **From a mockup:** keep every visible word exactly. No rewording - a client
  requirement repeated on every page.
- **Rebuild or new page where the user allows rewriting:** write for the Yoast
  focus keyword and for people, not keyword stuffing. Fix factual errors in the
  old copy and say which. Fact-check government/scheme facts (dates, who runs
  what) and leave out numbers you can't verify.
- **Healthray claims:** use only claims the site already makes: NHA approved,
  ABDM compliant, NABH-certified (link `https://nabh.co/software/healthray/`),
  HIPAA, ISO 27001, HL7/FHIR, 2,500+ hospitals, 5M+ patient records, 4.8/5 on
  Capterra & SoftwareSuggest, support in English/Hindi/Gujarati, free demo, data
  migration and training included. Any new claim (e.g. ABDM M3 certification,
  "our team handles HFR registration") goes in the action items for the user
  to confirm.
- Internal links via `home_url('/slug/')`. Link related pages (ABDM ↔ ABHA ↔
  DHIS ↔ PMJAY ↔ EHR; EMR ↔ EHR ↔ HIMS; etc.).

## 4. Template: `temp-{x}-landing.php`

- **Naming:** `temp-{x}-landing.php` (PPC: `temp-ppc-{product}-{channel}.php`).
  The old `page-*.php` names were renamed; `hr_renamed_page_templates()` maps
  old names - never create a new `page-*.php` page template.
- **Template Name:** `{X} Landing` (PPC: `PPC - {Product} {Channel}`).
- **Header comment** documents: page ID and URL, body class, CSS/JS files, the
  FAQ function, the form, and anything still to confirm.
- **No `get_header()` / `get_footer()`.** The site header/footer come from the
  Roots wrapper (`stratusx/base.php`); calling them pulls in the parent theme's
  unused fallback header/footer. Start straight with `<main>`.
- **Forms** (always via `do_shortcode`, never hand-written fields):

  | Use | Shortcode |
  |---|---|
  | Hero + final CTA on every landing page | `[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]` |
  | Speciality footer CTA | `[contact-form-7 id="3de5d32" title="CTA Form - CTA section"]` |
  | Solutions hub, hospital chains, multispeciality, PPC HIMS | `[contact-form-7 id="11eec7d" title="PPC Form"]` |
  | PPC pages | the page's own CF7 form (ask the user for the ID) |

  The old "Hero Section CTA" form (id 5803552) on Elementor pages is replaced by
  the lead form unless the user says otherwise.
- **Shared shortcodes** (use them; don't paste client/stat markup):
  - `[hr_stats]` - stats band (`columns`, `aria_label`)
  - `[hr_clients eyebrow="..." title="..."]` - client logo grid
  - `[hr_testimonials eyebrow="..." title="..." columns="4" show_ratings="yes"]`
- **Images:** `$hr_media = 'https://healthray.com/wp-content/uploads';` with
  `width`, `height`, `loading="lazy"`, `decoding="async"` and a descriptive
  `alt`. Reuse existing uploads (check sizes with `getimagesize`); list any
  placeholder images as action items.
- **Breadcrumbs** (`nav.crumbs`) as the first block on product-landing pages.
- **Accessibility:** each `<section>` has `aria-labelledby` pointing to its
  heading id; decorative SVGs get `aria-hidden="true"`; tables get a
  `<caption class="hp">`.
- **Gotcha - inline links in flex list items:** `.hero-points li`, `.arch li`,
  `.cta-points li` are `display:flex`, so a bare `<a>` inside becomes its own
  column. Wrap the text in `<span>…<a>…</a>…</span>`.

## 5. FAQ - one list for the page and the schema

```php
// functions.php, section 7 (next to healthray_ipd/opd/abdm_faq_items):
function healthray_{x}_faq_items()
{
	return array(
		array('q' => '...', 'a' => '...'),
	);
}
```

```php
// add the page to the existing hr_head_faqs filter in section 7:
add_filter('hr_head_faqs', function ($faqs) {
	if (is_page(NEW_ID)) {
		return healthray_{x}_faq_items();
	}
	// ...existing pages...
});
```

```php
// template:
$hr_{x}_faq = function_exists('healthray_{x}_faq_items') ? healthray_{x}_faq_items() : array();
// ...
<?php // one open at a time (shared name), first item open
foreach ($hr_{x}_faq as $hr_i => $hr_faq) : ?>
	<details name="{x}-faq"<?php echo 0 === $hr_i ? " open" : ""; ?>><summary><?php echo esc_html($hr_faq['q']); ?></summary>
	<div class="answer"><?php echo esc_html($hr_faq['a']); ?></div></details>
<?php endforeach; ?>
```

**Accordion behaviour (required):** every FAQ `<details>` gets the same `name` (`{x}-faq`) so only one answer is open at a time, and the first item gets `open`. Browsers do this natively; `script.js` (`initExclusiveDetails`, sitewide) closes the others in browsers without support - add no page JS for it.

`healthray_head()` (section 8) turns whatever `hr_head_faqs` returns into the
`FAQPage` node. No `_faq_graph()` helper is needed any more. The older pages
(79691, 79713, 79754, 79865 ...) still have FAQ arrays typed inside
`healthray_head()` - don't copy that pattern.

Answers are plain text: the template prints them with `esc_html()` and the
schema takes the same string, so an answer can't hold a link. Don't write "see
our X page" in an answer (ABDM's DHIS answer does); link the page from the
body instead.

## 6. CSS: diff before writing anything

1. Know what's already global: `style.css` defines the colour/size tokens
   (`--brand`, `--ink`, `--line`, `--radius`, `--font-display` ...) and the
   desktop `.hero-grid`. `common-landing.css` covers base type, buttons,
   crumbs, hero, lead-card, compliance grid, `.grid-3`/`.card`, clients grid,
   testimonials (`.t-card`, `.ratings`), `.define`/`.define-grid`,
   `.versus-table`, `.arch-grid`/`.arch`, FAQ, CTA, reveal. See
   `healthray-css-notes.md` (steering).
2. For every rule the page needs: already in a live file with the same values →
   skip. Exists with different values → tell the user before choosing. Missing →
   page file.
3. Page file `css/{x}-software.css`: only genuine gaps and page-unique sections,
   every rule scoped `.{x}-landing .foo`. Never an ID wrapper (`#hr-home` was a
   mistake).
4. **Heading case:** `style.css` sets `text-transform: capitalize` on all
   h1-h6, so headings render in Title Case. The hx family and the new home
   page reset it to sentence case (`text-transform:none`); the product-landing
   family (EMR/EHR/ABDM...) still shows Title Case. Keep the family's behaviour
   unless the user asks to change it.
5. If a rule is now needed by 2+ pages, move it into `common-landing.css` under
   a shared selector list (`.emr-landing .x, .ehr-landing .x`) - that's how the
   20 Nov consolidation was done.
6. Report: skipped as already common, added as new, conflicts found.

## 7. JS: `js/{x}-software.js`

- **Never** add reveal-on-scroll (centralized in `script.js`; a duplicate once
  shipped broken) or the "Other" dropdown handler for `your-business` /
  `speciality` (also in `script.js`).
- Only real page interactivity (video facade, tabs, sliders, popups). If none,
  create no JS file and enqueue nothing - say so.
- Validate with `node -c`.

## 8. functions.php wiring (product-landing family)

Edit `functions.php` directly; each change goes into the existing block, never
a new copy of it:

1. **Section 1** - add the ID to `HR_LANDING_PAGE_IDS` (og:type = website).
2. **Section 4** - add `is_page(NEW_ID)` to the long condition that dequeues
   Elementor-era styles/scripts (`abha-card`, `common-theme`, `owl.carousal`,
   `my-elements` ...). If the page has JS, enqueue it here:
   `wp_enqueue_script('{x}-software', get_stylesheet_directory_uri() . '/js/{x}-software.js', array(), hr_asset_version('/js/{x}-software.js'), true);`
   (`hr_asset_version()` uses the file's mtime, so no manual `?v=` bumps.)
   `abha-card` is the ABHA Card plugin (`plugins/abha-card`), not Elementor: a
   page that shows its `[adharAuthForm]` form must keep that handle. The ABHA
   page (26834) does this with an `array_diff()` right after the condition.
3. **Section 6** - add `NEW_ID => '{x}-landing'` to the `$landing_pages` map in
   the body_class filter (gives `landing-page` + `{x}-landing`).
4. **Section 7** - add `is_page(NEW_ID)` to the "Shared landing CSS" condition
   that inlines `common-landing.css`.
5. **Section 7** - a sibling block that inlines the page CSS, before
   "Modules hub CSS and ItemList":
   ```php
   // {X} page
   if (is_page(NEW_ID)) {
   	$x_css_path = get_stylesheet_directory() . '/css/{x}-software.css';
   	if (file_exists($x_css_path)) {
   		echo '<style id="{x}-software-css-inline">' . file_get_contents($x_css_path) . '</style>' . "\n";
   	}
   	// product pages only: SoftwareApplication schema (see below)
   }
   ```
6. **Schema:**
   - Product pages: a `SoftwareApplication` node in that block, like EHR/Clinic:
     `operatingSystem`, `applicationCategory` `BusinessApplication`,
     `applicationSubCategory`, `description`, `url`, `publisher` →
     `home_url('/') . '#organization'`, `offers` (price 0 INR, "Free demo and
     free trial available"), `aggregateRating` 4.8 / 5 / `ratingCount` 180 -
     ask the user to confirm the count against Capterra/SoftwareSuggest.
   - Information pages (ABDM, ABHA, DHIS, PMJAY, Our Story ...): no
     SoftwareApplication or rating. FAQPage via the filter is enough; Yoast
     already outputs WebPage + BreadcrumbList.
   - There is no longer a generic sitewide SoftwareApplication to exclude
     (checked 25 Nov 2026).
7. **FAQ** - section 5 above.

**hx family:** same steps 1-3, 5-7 but skip step 4 (no common-landing).
**PPC family:** the template name does most of it - `hr_ppc_page_key()` reads
`temp-ppc-{key}-...php`, adds body class `ppc-{key}`, inlines
`css/ppc-{key}.css` + `ppc-popup.css`, and uses the standalone base. Print the
shared header/footer with
`get_template_part('templates/ppc/header', null, array('cta' => ..., 'href' => '#demo', 'class' => ..., 'attrs' => ...))`
and `templates/ppc/footer`. WhatsApp links: `hr_ppc_wa_url('message')`. To
serve it under `/ppc/{slug}/`, add the ID to `lib/virtual-urls.php`. A PPC page
with its own popup must be excluded from the generic lead popup
(`hr_ppc_popup_args` / section 13). Set noindex in Yoast.

## 9. Verify locally before handing over

1. `php -l` the template and `functions.php`.
2. Set the template on the **local** page only (all duplicate rows):
   `UPDATE wp_postmeta SET meta_value='temp-{x}-landing.php' WHERE post_id=ID AND meta_key='_wp_page_template'`.
3. `curl http://localhost/healthray/{slug}/` → 200; check the `<body>` classes,
   the `<style id=...-css-inline>` blocks, `"@type":"FAQPage"`, the form markup,
   and no PHP notices/warnings in the HTML. For a rebuilt Elementor page, also
   compare the size of `<style id="elementor-frontend-inline-css">` with the
   EHR page; a much bigger one means the old layout is still stored (section 2).
4. Screenshots with headless Edge
   (`C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe --headless=new --hide-scrollbars --window-size=1366,9000 --screenshot=...`).
   A 9000px-tall window puts every `.reveal` element in view; anchor-scrolled
   shots come out blank. Crop with PHP GD (`imagecrop`) - Python PIL isn't
   installed. Headless Edge lays out at ~500px minimum, so 390px shots look cut
   off on the right; compare with the EHR page before calling it a bug.

   **True phone widths:** drive Chrome over the DevTools protocol from Node
   (Node 24 has a built-in `WebSocket`, so no packages are needed). Start
   `chrome.exe --headless=new --remote-debugging-port=9333 --user-data-dir=<temp dir>`,
   read the page's `webSocketDebuggerUrl` from `http://127.0.0.1:9333/json/list`,
   then send `Emulation.setDeviceMetricsOverride` (390 x 844, `mobile: true`)
   and `Page.navigate`. Scroll to the bottom 300px every 250 ms so every
   `.reveal` gets `.in` - faster scrolling leaves cards mid-fade, which looks
   like a bug. Capture with `Page.captureScreenshot`, `captureBeyondViewport:
   true` and a clip per `<section>`. Block analytics with
   `Network.setBlockedURLs` (`*googletagmanager.com*`, `*google-analytics.com*`,
   `*analytics.ahrefs.com*`, `*wati.io*`) so test loads stay out of the
   reports. The same session can check sideways overflow, broken images and
   console errors.

   Expected noise locally: a CORS error for the Mulish font from
   `healthray.com/cf-fonts`, and the site lead popup (`templates/lead-popup.php`)
   opening mid-scroll over a section. In shots where the popup is open, the
   "Skip to content" link from `js/header.js` shows too. Neither is a page bug.
5. Look at every section, fix, re-check.

## 10. Hand-over summary (in this order)

1. Page ID, URL, body class, template name - and that none were invented.
2. What changed in content (sections, facts fixed, what was dropped and why).
3. CSS diff: skipped as common / new / conflicts.
4. JS: real content or none, and why.
5. Files created/edited (repo paths), and that nothing is committed or live.
6. Action items: claims to confirm, ratingCount, placeholder images, Yoast
   title/description suggestion (title ≤ 60 chars, description ≤ 155).
7. Deploy steps: upload the new files + `functions.php`, set the template in WP
   admin, update Yoast meta in admin (direct DB edits leave Yoast's
   `wp_yoast_indexable` stale).

Never say "ready to deploy" while an action item is open.
