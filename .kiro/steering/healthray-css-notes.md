---
inclusion: always
---

# CSS scoping notes and known pitfalls

Snapshot as of 25 Nov 2026. **Re-fetch the live files before trusting it** -
this says what to look for, not the current state.

## Where styles come from

| File | Loaded | Holds |
|---|---|---|
| `style.css` | every page | tokens on `:root` (`--brand` #2563EB, `--brand-tint`, `--brand-dark`, `--brand-deep`, `--brand-glow`, `--ink`, `--ink-soft`, `--ink-rgb`, `--line`, `--radius` 14px, `--font-display` Mulish, `--hr-hero-gradient`, `--hr-hero-*`, `--amber`), global `h1-h6 { text-transform: capitalize }`, desktop `.hero-grid` (1.25fr 1fr), sitewide `.footer-popup` form |
| `common-landing.css` | inlined on product-landing pages only (condition in functions.php section 7) | everything under `.landing-page` listed below |
| `{x}-software.css` | inlined per page | page-only rules, scoped `.{x}-landing` |
| `script.js` | every page | reveal-on-scroll (`.reveal` → `.reveal.in`), "Other" dropdown handlers |

## Present in common-landing.css (scoped `.landing-page`)

Base type and `.wrap` (max 1288px), `.eyebrow`, `section` padding,
`.section-head(.center)`, `.btn` / `.btn-primary` / `.btn-ghost`, `.crumbs`,
`.hero` + `h1 .pulse-word` + `.ecg-path` animation, `.hero-sub`,
`.hero-points`, `.hero-proof`, `.lead-card`, mobile hero re-ordering (title →
sub → form → points → proof, eyebrow and crumbs hidden under 767px),
`.compliance` / `.comp-grid` / `.comp-item`, `.clients-grid` / `.client` /
`.clients-note`, `.hp` (visually hidden), `.strip` / `.badge`, `.grid-3` /
`.card` / `.card .icon`, `.faq-list` (details/summary/answer),
`.cta` / `.cta-grid` / `.cta-points` / `.ecg-bg`, `.reveal`, reduced-motion
rules, `.t-card` / `.t-who` / `.ratings` / `.rating`, `.define` /
`.define-grid`, `.versus-table` / `.versus-cta`, `.arch-grid` / `.arch`.

Shared multi-page block at the end: `.t-grid` (emr/ehr/clinic/hims), the
animated `.steps-track` / `.steps-grid` (emr/ehr), `.outcomes` band
(clinic/hims).

Information-page block after it (ABDM, ABHA, DHIS, PM-JAY, Our Story), written
as `:is(.abdm-landing, .abha-landing, ...) .foo` so specificity stays two
classes: `ol.num-steps` (four numbered step cards with a CSS counter),
`.table-scroll` (wrapper that scrolls 3+ column tables sideways on phones,
min-width 640px), `.related-grid` (`a.card` links with a `.more` line and
arrow), link colours inside `.card p`, `.arch li` and `.define p`, and the
`.define-img` height cap for portrait images (abdm/dhis/pmjay). A new page that
needs one of these joins its `:is()` list instead of copying the rule.

## Not in common (put in the page file if needed)

- Media zigzag rows (`.media-rows`, `.media-row`, `.float-chip`) - only in
  `ehr-software.css`. If a second page needs them, move them to common.
- Step-track dot positions and step card styling - per page.

## Pitfalls

- **ID wrappers:** the old home page used `#hr-home`; every later page uses
  the body class. Never add a new ID wrapper.
- **Compliance badge sizes:** `emr-software.css` overrides `.comp-item img`
  (64px, min-height 170px) while other pages use common's 52px. Ask before
  "fixing" either way.
- **Flex list items:** `.hero-points li`, `.arch li`, `.cta-points li` are
  flex rows - wrap text that contains a link in a `<span>`.
- **Image frame in `.define-grid`:** common gives `.define-grid img` a 1px
  `var(--line)` border and a 16px radius. Around an image that isn't a
  rectangle (ABDM's arch-shaped illustration) it shows as a faint frame; reset
  it in the page file with `border: 0`.
- **Title Case headings** come from `style.css`. The hx family resets with
  `.{x}-landing .hx :where(h1,h2,h3,h4){ text-transform:none }`.
- **hx family reset block:** IPD/OPD/Modules start their CSS with a
  `:where(...)` reset under `.{x}-landing .hx` to undo theme defaults
  (fonts, list margins, blockquote, table borders, buttons), excluding
  `.wpcf7 *` so the form keeps its styles. Copy that block for a new hx page.
- **Speciality pages** do not load common-landing; they scope to `.sp-main`.
- **PPC pages** run on the standalone base: no site header/footer and no
  common-landing; shared header/footer CSS is `css/ppc-common.css`, printed
  once by `hr_ppc_common_css()`.
