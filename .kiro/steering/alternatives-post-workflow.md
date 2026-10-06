---
inclusion: always
---

# Creating an Alternatives post from a PDF

When the user supplies a PDF (or DOCX/Doc export) of Alternatives content and
asks for a post, follow this pipeline. Do not hand-write one-off scripts.

Tools live in `wp-content/themes/stratusx-child/tools/alternatives/`.
Full guide is that directory's `README.md`.

## Process

1. **Extract.** `python extract-pdf.py "<path to pdf>"` from the tool directory.
   Read both `out/<name>.prose.txt` and `out/<name>.tables.txt`. The prose file
   alone is not enough; the comparison table only comes out of the table file.

2. **Fill a content JSON** in `content/<slug>.json`. Copy the shape from
   `content/ezovion-alternatives.json`. Transcribe verbatim: do not reword,
   summarise, expand or fix the source. Preserve the document's own apostrophes
   (it mixes straight and curly), en dashes and `·` separators. Convert only the
   `fi`/`fl` ligature glyphs, which are font artifacts.

   **Scan for invisible characters before transcribing.** Google Docs exports
   soft line breaks as `U+000B` (vertical tab), which is invisible in most
   editors and collapses two words together when read as text: a cell reading
   `Not<VT>confirmed` looks like "Notconfirmed" but means "Not confirmed". Treat
   `U+000B` as a space, and treat `U+00A0`, `U+200B` and `U+FEFF` the same way —
   they are export artifacts, not content. Inside a review or Best For box the
   vertical tab is the separator between author, stars, quote, label and the
   Rating line, so use it to split fields rather than transcribing it.

3. **Dry run.** `php import-alternatives.php --file=content/<slug>.json`.
   Fix every error. Report the warnings to the user rather than silently
   accepting them.

4. **Apply.** Add `--apply`. Then report the post ID, edit URL, and preview URL.

5. **Verify.** Load the rendered page and confirm it returns 200 with no PHP
   errors and that the row/profile counts match the document.

## Mapping rules

- `competitors` sets column order for the whole table. Healthray is implicit and
  always column 0, so never list it there. Every `values` array is Healthray
  first, then competitors in that exact order.
- Table cells: `yes`/`no` become icons, `yes: caption` becomes an icon plus
  caption, anything else is plain text, empty is a dash.
- Row `type`: `rating` for a 0-5 number, `yesno` when a row mixes ticks and
  text, `text` otherwise.
- "What Stands Out" bullets map to `stands_out` (the `key_modules` repeater).
- Each platform's closing "Our Experience" paragraph maps to `our_experience`.
- Profile names carry no leading number; the template adds it.
- Never put "Verified ... Review" in content. The template builds that line
  itself: "Verified {rating_source} Review" when the box names a reviewer, and a
  plain "Verified Review" when it does not.
- Omit a key to leave that field untouched. Omit `url` to keep an existing
  review link, `"url": ""` to clear it.

## Pros/cons review boxes

A review box needs only its text. Reviewer name, star rating and link are each
optional, and the box still renders without them.

Source documents often put the writer's own observation in the box where a
reviewer quote normally sits — no name, no stars, no link. Transcribe the text
into `pros_review.quote` / `cons_review.quote` and leave the rest out:

- **No name in the source** → omit `author`. The template's label line becomes
  "Verified Review" on its own, so do not set `label` and do not invent a
  reviewer.
- **No link in the source** → omit `url`. The label renders as plain text
  instead of an `<a>`. Never go looking for a URL to fill it.
- **No stars in the source** → omit `rating`. The stars are skipped rather than
  defaulting to 5, which would invent a score.

Set `label` explicitly only when the source's own wording differs from what the
fallback produces — an app store review under a profile whose `rating_source` is
a review site, for example. Write it in title case: "Verified App Review".

## Hard rules

- New posts are drafts. Never publish, and never change an existing post's
  status, unless the user explicitly asks.
- Never write SEO/Yoast meta or the featured image. The user does those.
- Never invent content that is not in the source: no review URLs, no ratings,
  no filler for empty fields.
- Every section a source document normally contains now has a field; see the
  mapping table in the tool's README.md. If a document contains something not on
  that list, say so and ask rather than forcing it into a field where it does not
  belong.
- If the source contradicts what is already stored (changed reviewer names,
  changed ratings), flag the conflict instead of leaving stale data silently
  paired with new content.
