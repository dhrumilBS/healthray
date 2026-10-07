# Alternatives post workflow

Turns a Google Docs PDF into an Alternatives post, as a draft, with the content
placed in the right ACF fields and nothing overwritten that shouldn't be.

Two commands. Everything in between is reviewable.

```
PDF  ──►  extract-pdf.py  ──►  text + tables  ──►  content JSON  ──►  import-alternatives.php  ──►  draft post
                                                   (Kiro fills this)      (validates, then writes)
```

## Files

| Path | What it is |
|---|---|
| `extract-pdf.py` | Reads the PDF, rebuilds readable lines, pulls the comparison table out cell by cell |
| `import-alternatives.php` | Validates a content JSON file and writes it to a post. Dry run unless you pass `--apply` |
| `content/` | One JSON file per post. `ezovion-alternatives.json` is a working example exported from the live post |
| `out/` | Extractor output. Scratch space, safe to delete |

## Step 1 — Drop the PDF in and extract it

Put the PDF anywhere. Then:

```powershell
cd d:\xampp\htdocs\healthray\wp-content\themes\stratusx-child\tools\alternatives
python extract-pdf.py "..\..\assets\Ezovion Alternatives.pdf"
```

You get two files in `out/`:

- `<name>.prose.txt` — all the body copy, page by page
- `<name>.tables.txt` — the comparison table, one cell per `||` field

Both are needed. Google Docs writes one word per PDF instruction, so the prose
file is a reconstruction, and the table only comes out reliably from the cell
extraction.

## Step 2 — Fill the content JSON

Copy `content/ezovion-alternatives.json` to a new name and replace the content.
This is the step Kiro does for you: hand it the PDF path and it will read the
extraction output and write the JSON.

Shape, with the parts that matter:

```jsonc
{
  "post": {
    "id": null,                    // null creates a new draft; a number updates that post
    "title": "8 Foo Alternatives for Hospitals in 2026",
    "slug": "foo-alternatives",
    "status": "draft"
  },
  "fields": {
    "subject_name": "Foo",         // the platform this article is about

    // Arrays become <p> blocks. A single string is used as raw HTML instead.
    "intro_content": ["First paragraph.", "Second paragraph."],

    // The heading is rendered inside the content block, matching existing posts.
    "methodology_heading": "How We Analyse and Select Foo Alternatives",
    "methodology_content": ["..."],

    // Column order for the whole comparison table. Healthray is implicit and
    // always the first column, so do not list it here.
    "competitors": ["MocDoc", "SoftClinic GenX"],

    "glance_rows": [
      // "values" is Healthray first, then one per competitor, in the order above.
      { "label": "Best for",  "type": "text",   "values": ["Hospitals", "Clinics", "Hospitals"] },
      { "label": "ABDM",      "type": "yesno",  "values": ["yes", "no", "no"] },
      { "label": "Rating",    "type": "rating", "values": ["5", "4", "4"] }
    ],

    "comparison_categories": [
      {
        "name": "CORE FEATURE COVERAGE",
        "features": [
          { "name": "OPD/IPD Management", "values": ["yes", "yes", "no"] },
          { "name": "Go-live time",       "values": ["1-3 weeks", "Not stated", "no"] }
        ]
      }
    ],

    "profiles": [
      {
        "name": "Healthray",
        "description": "One paragraph.",
        "stands_out": [{ "title": "Broad Coverage", "text": "..." }],
        "best_for": "...",
        "rating": "4.8",
        "rating_source": "G2",
        "pros": [{ "title": "Lead-in", "text": "..." }],
        "pros_review": { "author": "A. Name", "rating": 5, "quote": "...", "url": "https://...", "label": "Verified G2 Review" },
        "cons": [{ "title": "Lead-in", "text": "..." }],
        "cons_review": { "author": "B. Name", "rating": 4, "quote": "..." },
        "our_experience_heading": "Overall Verdict",   // optional, defaults to "Our Experience"
        "our_experience": "Closing verdict paragraph for this platform."
        // or replace the five section keys above with:
        // "content": "<div class=\"alt-profile__description\">…</div>…"
      }
    ],

    "faqs": [{ "question": "...", "answer": "..." }]
  }
}
```

### The rules worth memorising

**Column order is everything.** `competitors` sets the order for the entire
table. Every `values` array is Healthray first, then the competitors in that
same order. Get this wrong and values land under the wrong logo. The importer
counts them for you and refuses to write on a mismatch.

**Cell values.** `yes` and `no` render as tick and cross icons. `yes: some note`
renders a tick with a caption. Anything else renders as plain text, which is how
you get cells like `Not stated` or `1-3 weeks`. An empty string renders a dash.

**Row types.** `text` for plain values, `rating` for a 0–5 star number, `yesno`
when a row mixes ticks and text, which is the common case.

**Omit a key to leave that field alone.** Only keys present in the JSON are
written. This is how SEO-adjacent and image fields stay safe on an update.

**Review URLs, screenshots and the Healthray profile `video` follow the same
rule.** Leave `url` out to keep
whatever is stored. Set `"url": ""` to clear it. Set a value to replace it.

**The review label is its own field.** Each review block ends with a
verification line. Set it per review with `pros_review.label` /
`cons_review.label` when the source needs its own wording — "Verified App
Review", "Verified Healthray Review". Leave it out and the template builds it:
`Verified {rating_source} Review` when the box names a reviewer, so a source of
`Capterra` produces "Verified Capterra Review" on its own, and a plain
"Verified Review" when it does not. Either way, don't write that line into the
prose.

**A review box needs only its text.** `author`, `rating` and `url` are each
optional, which is what lets an unattributed editorial note live in the box where
a reviewer quote normally sits. Omit `author` and the name is skipped and the
label falls back to "Verified Review". Omit `rating` and the stars are skipped
rather than defaulting to 5. Omit `url` and the label renders as plain text
instead of a link. Don't fill any of them from outside the document.

**Skip the numbering in profile names.** The template prints `1.`, `2.` and so
on. Name the profile `Epic`, not `5. Epic`.

**Prose fields accept HTML, but keep transcribing plain text.** Every
profile's `description`, `best_for`, `stands_out[].text`, `pros[].text`,
`cons[].text`, both review `quote`s, `our_experience`, the FAQ answers and the
two CTA `text` lines are TinyMCE editors in the admin. Plain strings are still
the right thing to write here — the template wraps them for you — and inline
`<strong>`, `<em>` and `<a>` are available if the source document has them.
Don't reach for headings, lists or block markup in the short ones: they render
inside a bullet or a quote, where a block would break the design.

**A profile write-up is one editor in the admin.** `description`, `stands_out`,
`best_for`, `our_experience_heading` and `our_experience` are section keys in the
JSON only: the importer composes them into the single "Profile Content" editor,
in that order. Keep using them — they are what a normal source document looks
like, and they save you writing markup.

When a document does something the sections don't cover — an extra subheading,
two bullet lists, a section in a different order — pass `content` instead and
write the markup yourself. It replaces all five section keys, and the importer
refuses to write if you supply both. The classes are load-bearing:

```html
<div class="alt-profile__description"><p>Prose under the heading.</p></div>

<h4 class="alt-profile__subheading">What Stands Out</h4>

<ul class="alt-profile__modules">
  <li><strong>Lead-in</strong>: point</li>
</ul>

<div class="alt-profile__best-for">
  <h4 class="alt-profile__subheading">Best For</h4>
  <p>...</p>
</div>

<div class="alt-profile__experience">
  <h4 class="alt-profile__subheading">Our Experience</h4>
  <p>...</p>
</div>
```

Three blocks are generated from the other fields rather than written as markup.
Put each token on its own line where the document has it:

| Token | Renders |
|---|---|
| `[alt-screenshot]` | `profiles[].screenshot`, or on the Healthray profile its video when one is attached |
| `[alt-rating]` | the stars from `rating` + `rating_source` |
| `[alt-pros-cons]` | the Pros/Cons grid from `pros`, `cons` and both reviews |

Leave a token out and its block still renders — screenshot above the content,
rating and pros/cons after it — so nothing is silently dropped. Exporting a post
always writes `content`, never the section keys, because the editor is what the
post actually stores.

## Step 3 — Dry run, then apply

Always dry run first. This writes nothing:

```powershell
& "d:\xampp\php\php.exe" import-alternatives.php --file=content/foo-alternatives.json
```

You get a summary (site, acting user, target post, columns, row counts, profile
count) plus any errors and warnings. Errors block the write. Warnings are things
to eyeball.

When it's clean:

```powershell
& "d:\xampp\php\php.exe" import-alternatives.php --file=content/foo-alternatives.json --apply
```

It prints the post ID, the edit URL and the preview URL.

### Which post gets written

`post.id` in the file only means anything on the site that produced the file, so
there are four ways to pick the target:

| Flag | Behaviour |
|---|---|
| *(none)* | Use `post.id`, or create a draft when it is `null`. Errors if the ID is missing or is not an alternatives post. |
| `--id=N` | Write to post N and ignore `post.id`. |
| `--by-slug` | Look the post up by `post.slug` on this site. Errors when there is no match or more than one, so it never creates by accident. |
| `--new` | Always create a new draft. |

`--by-slug` is the one to reach for when the same file is used on more than one
site, because slugs match across environments and IDs do not.

### Acting user

The importer runs as the first administrator unless you pass
`--user=<id|login|email>`. This is not cosmetic: with no current user WordPress
finds no `unfiltered_html` capability, puts `post_title` through kses, and stores
`&` as `&amp;`, which then shows up literally in the admin post list.

## What the importer will never do

- Write Yoast or any SEO meta
- Set or change the featured image
- Publish anything. New posts are created as `draft`, and an existing post's
  status is left as-is unless you explicitly pass `--status=`
- Replace a competitor logo, a profile screenshot or the Healthray profile video
- Write anything at all if validation fails, even with `--apply`

Every update is backed up to `.kiro/backups/alt-<id>-<timestamp>.json` before a
single field is touched.

## Step 4 — Review locally, then move the post to live

`--export` turns any existing post back into this JSON schema, so the file and
the post stay interchangeable. That is the review loop: edit in WordPress, export,
diff the JSON, or edit the JSON, dry run, apply, export again to confirm.

### 4a. On local — export a portable copy

```powershell
& "d:\xampp\php\php.exe" import-alternatives.php --export=81314 --portable --out=content/miracle-his-alternatives.json
```

`--portable` blanks `post.id` and sets the status to `draft`. Without it the file
carries the local post ID, which points at a different post (or nothing) on live.
Keep one portable file per post in `content/` and there is nothing to hand-edit
before a transfer.

### 4b. Copy the one file to live

Only the JSON goes across, e.g. to `wp-content/themes/stratusx-child/tools/alternatives/content/`
on the live server. Nothing else about the transfer touches the database.

### 4c. On live — dry run, then apply

First time, when the post does not exist there yet. `post.id` is null, so this
creates a draft:

```bash
php import-alternatives.php --file=content/miracle-his-alternatives.json
php import-alternatives.php --file=content/miracle-his-alternatives.json --apply
```

Every time after that, target the post that is already there by slug:

```bash
php import-alternatives.php --file=content/miracle-his-alternatives.json --by-slug
php import-alternatives.php --file=content/miracle-his-alternatives.json --by-slug --apply
```

> **A full export overwrites, including with blanks.** "Omit a key to leave that
> field alone" is the rule, but an export never omits a key: empty fields come
> across as `""`. So pushing an exported file over a post that someone has since
> edited on live will blank whatever they added. When the destination post is the
> one being edited, either export from *there* first and merge, or cut the file
> down to the keys you actually mean to change.

Read the header before you let it write. It prints the site URL, the acting user
and the resolved target, which is how you catch pointing at the wrong install:

```
Site         : https://healthray.com
Acting as    : someadmin (#30)
Mode         : UPDATE post 92310 (target from --by-slug)
Action       : DRY RUN (nothing will be written)
```

### 4d. Finish by hand on live

The importer deliberately leaves these alone, so they are the checklist after an
apply:

1. Featured image.
2. Competitor logos, one per row of the Competitors repeater.
3. Profile screenshots, and the Healthray profile video if the post has one.
4. Yoast title and meta description.
5. Publish, when you are ready. The importer never will.

The `images` block in the exported file lists what was attached on the source
site, by filename, to work from.

### What does not travel

**Images.** Attachment IDs are per-site, so an ID from one install points at
something else, or nothing, on another. The importer never writes an image, which
is what makes a cross-site import safe. An export records what *was* attached in
an `images` block at the top level of the file:

```jsonc
"images": {
  "featured_image":      { "id": 81275, "file": "2026/08/dashboard.webp", "url": "..." },
  "competitor_logos":    { "MocDoc": { "id": 81269, "file": "2026/08/Mocdoc.webp", "url": "..." } },
  "profile_screenshots": { "Healthray": null },
  "profile_videos":      { "Healthray": { "id": 81708, "file": "2026/10/healthray-walkthrough.mp4", "url": "..." } }
}
```

That block is reference material only. The importer reads `post` and `fields` and
ignores everything else, so nothing in it is ever written.

**SEO meta.** Never exported, never imported. Yours to set on each site.

**Post status.** A `--portable` export always says `draft`. Publishing is a
decision you make on the destination.

## Useful extras

Force a status when you deliberately want to: `--status=draft`.

## Document section → field map

Every section a source document normally contains has a home. If a document has
something not on this list, the importer will not invent a place for it; ask.

| Document section | Field |
|---|---|
| H1 | `post.title` |
| Opening paragraphs | `intro_content` |
| "Why Are Hospitals Looking for …" | `why_look_title` + `why_look_content` |
| "How We Analyse and Select …" | `methodology_heading` + `methodology_content` |
| "Compare the Options at a Glance" heading | `comparison_title` |
| The paragraph between that heading and the table | `comparison_intro` |
| Table header row | `competitors` |
| Table rows above the first grey band | `glance_rows` |
| Grey band + the rows beneath it | one `comparison_categories` entry |
| Heading above the numbered write-ups | `profiles_heading` |
| Numbered platform heading | `profiles[].name` (drop the number) |
| Paragraph under the heading | `profiles[].description` |
| "What Stands Out" bullets | `profiles[].stands_out` |
| "Best For" | `profiles[].best_for` |
| "Star Ratings: 4.8/5 by G2" | `profiles[].rating` + `rating_source` |
| "Pros" bullets | `profiles[].pros` |
| Reviewer block under Pros | `profiles[].pros_review` |
| "Cons" bullets | `profiles[].cons` |
| Reviewer block under Cons | `profiles[].cons_review` |
| "Our Experience" / "Overall Verdict" heading | `profiles[].our_experience_heading` (omit for "Our Experience") |
| The paragraph under it | `profiles[].our_experience` |
| A profile section the document invented | `profiles[].content` (see below) |
| "How to Choose the Right …" | `how_to_choose_title` + `how_to_choose_content` |
| "Final Verdict" | `final_verdict_title` + `final_verdict_content` |
| Closing CTA heading / text / button | `mid_cta_heading` / `mid_cta_text` / `mid_cta_button_text` |
| "FAQs" | `faqs` |

Not taken from the document, because you own them: featured image, competitor
logos, profile screenshots, the Healthray profile video, Yoast title and meta
description. The importer never writes any of these.

Also not from the document: review URLs. Source PDFs quote reviewers without
linking them. Add the links by hand, or supply them in the JSON if you have them.

## Requirements

Python 3 with `pdfplumber`:

```powershell
python -m pip install pdfplumber
```

PHP is used through XAMPP's binary at `d:\xampp\php\php.exe`. The importer
refuses to run over HTTP and this directory is blocked by `.htaccess`.
