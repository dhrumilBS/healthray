"""
Step 1 of the case study workflow: turn a Word (.docx) case study into
reviewable text.

Reads the document XML directly with the standard library, so headings, list
items, bold lead-ins, links, soft line breaks and tables all survive. Saving a
Google Doc as plain text loses most of that.

Usage
    python extract-docx.py "path/to/Vibrant Hospital.docx"
    python extract-docx.py "path/to/folder"              every .docx in it
    python extract-docx.py "path/to/doc.docx" --images   also save embedded pictures
    python extract-docx.py "path/to/doc.docx" --outdir out

Writes out/<name>.txt: one line per paragraph in document order, tagged with
its style, and every table cell by cell where it sits in the document.

Markers
    [TITLE] [H1] [H2] ...  paragraph style
    [P]                    normal paragraph
    [LI-b:0] [LI-n:0]      bullet / numbered list item, nesting level
    **text**               bold run
    [text](url)            hyperlink
    <BR>                   soft line break (Shift+Enter) inside a paragraph
    [IMAGE media/x.png]    embedded picture
    ROW i: a || b || c     row of a simple table (one paragraph per cell)
    [row i cell j]         any other table: one indented line per paragraph,
                           nested tables expanded underneath
    • / #                  bullet / numbered item inside a table cell

Google Docs exports carry invisible characters that collapse or split words
when read as text: vertical tab (U+000B), no-break space (U+00A0), zero-width
space (U+200B) and byte order mark (U+FEFF). They are export artifacts, not
content, so each one becomes a plain space and the header line counts them.
Soft hyphens (U+00AD) are dropped.
"""

import argparse
import collections
import pathlib
import re
import sys
import zipfile
import xml.etree.ElementTree as ET

W = "http://schemas.openxmlformats.org/wordprocessingml/2006/main"
R = "http://schemas.openxmlformats.org/officeDocument/2006/relationships"
A = "http://schemas.openxmlformats.org/drawingml/2006/main"
V = "urn:schemas-microsoft-com:vml"
PKG = "http://schemas.openxmlformats.org/package/2006/relationships"

INVISIBLE_TO_SPACE = {
    "\u000b": "VT",
    "\u00a0": "NBSP",
    "\u200b": "ZWSP",
    "\ufeff": "BOM",
}
DROPPED = {"\u00ad": "SHY"}

BREAK = "<BR>"


def w(tag):
    return f"{{{W}}}{tag}"


def is_on(el):
    """A w:b / w:i style toggle: present and not switched off."""
    if el is None:
        return False
    return el.get(w("val"), "true").lower() not in ("0", "false", "off", "none")


class Doc:
    def __init__(self, z):
        self.z = z
        self.names = set(z.namelist())
        self.rels = self._rels("word/_rels/document.xml.rels")
        self.styles = self._styles()
        self.numbering = self._numbering()
        self.counts = collections.Counter()
        self.images = []
        self.fields = []

    def xml(self, part):
        return ET.fromstring(self.z.read(part)) if part in self.names else None

    def _rels(self, part):
        rels = {}
        root = self.xml(part)
        if root is not None:
            for rel in root.findall(f"{{{PKG}}}Relationship"):
                rels[rel.get("Id")] = rel.get("Target")
        return rels

    def _styles(self):
        styles = {}
        root = self.xml("word/styles.xml")
        if root is None:
            return styles
        for st in root.findall(w("style")):
            name = st.find(w("name"))
            based = st.find(w("basedOn"))
            ppr = st.find(w("pPr"))
            rpr = st.find(w("rPr"))
            outline = ppr.find(w("outlineLvl")) if ppr is not None else None
            styles[st.get(w("styleId"))] = {
                "name": (name.get(w("val")) if name is not None else st.get(w("styleId"))) or "",
                "based": based.get(w("val")) if based is not None else None,
                "numpr": ppr.find(w("numPr")) if ppr is not None else None,
                "outline": int(outline.get(w("val"))) if outline is not None else None,
                "bold": is_on(rpr.find(w("b"))) if rpr is not None else False,
            }
        return styles

    def _numbering(self):
        formats = {}
        root = self.xml("word/numbering.xml")
        if root is None:
            return formats
        abstract = {}
        for an in root.findall(w("abstractNum")):
            levels = {}
            for lvl in an.findall(w("lvl")):
                fmt = lvl.find(w("numFmt"))
                levels[lvl.get(w("ilvl"))] = fmt.get(w("val")) if fmt is not None else "decimal"
            abstract[an.get(w("abstractNumId"))] = levels
        for num in root.findall(w("num")):
            ref = num.find(w("abstractNumId"))
            levels = dict(abstract.get(ref.get(w("val")), {})) if ref is not None else {}
            for override in num.findall(w("lvlOverride")):
                lvl = override.find(w("lvl"))
                fmt = lvl.find(w("numFmt")) if lvl is not None else None
                if fmt is not None:
                    levels[override.get(w("ilvl"))] = fmt.get(w("val"))
            formats[num.get(w("numId"))] = levels
        return formats

    def style_chain(self, sid):
        seen = set()
        while sid and sid not in seen and sid in self.styles:
            seen.add(sid)
            yield self.styles[sid]
            sid = self.styles[sid]["based"]

    def clean(self, text):
        out = []
        for ch in text or "":
            if ch in INVISIBLE_TO_SPACE:
                self.counts[INVISIBLE_TO_SPACE[ch]] += 1
                out.append(" ")
            elif ch in DROPPED:
                self.counts[DROPPED[ch]] += 1
            else:
                out.append(ch)
        return "".join(out)


def para_tag(p, doc):
    """Return (tag, list_kind) for a paragraph."""
    ppr = p.find(w("pPr"))
    sid = None
    if ppr is not None:
        ps = ppr.find(w("pStyle"))
        sid = ps.get(w("val")) if ps is not None else None

    numpr = ppr.find(w("numPr")) if ppr is not None else None
    if numpr is None:
        for st in doc.style_chain(sid):
            if st["numpr"] is not None:
                numpr = st["numpr"]
                break
    if numpr is not None:
        nid = numpr.find(w("numId"))
        lvl = numpr.find(w("ilvl"))
        nid = nid.get(w("val")) if nid is not None else None
        if nid and nid != "0":
            ilvl = lvl.get(w("val")) if lvl is not None else "0"
            fmt = doc.numbering.get(nid, {}).get(ilvl, "bullet")
            kind = "b" if fmt in ("bullet", "none") else "n"
            return f"LI-{kind}:{ilvl}", kind

    if ppr is not None:
        ol = ppr.find(w("outlineLvl"))
        if ol is not None and int(ol.get(w("val"))) < 9:
            return f"H{int(ol.get(w('val'))) + 1}", None

    for st in doc.style_chain(sid):
        name = st["name"].lower()
        if name == "title":
            return "TITLE", None
        if name == "subtitle":
            return "SUBTITLE", None
        m = re.match(r"heading\s*(\d)", name)
        if m:
            return f"H{m.group(1)}", None
        if st["outline"] is not None and st["outline"] < 9:
            return f"H{st['outline'] + 1}", None

    return "P", None


def run_bold(rpr, doc):
    if rpr is None:
        return False
    b = rpr.find(w("b"))
    if b is not None:
        return is_on(b)
    rs = rpr.find(w("rStyle"))
    if rs is not None:
        return any(st["bold"] for st in doc.style_chain(rs.get(w("val"))))
    return False


def add(segs, text, bold=False, link=None):
    if not text:
        return
    if segs and segs[-1][1] == bold and segs[-1][2] == link:
        segs[-1][0] += text
    else:
        segs.append([text, bold, link])


def images_in(el, doc):
    found = []
    for blip in el.iter(f"{{{A}}}blip"):
        rid = blip.get(f"{{{R}}}embed") or blip.get(f"{{{R}}}link")
        if rid in doc.rels:
            found.append(doc.rels[rid])
    for img in el.iter(f"{{{V}}}imagedata"):
        rid = img.get(f"{{{R}}}id")
        if rid in doc.rels:
            found.append(doc.rels[rid])
    return found


def current_field_link(doc):
    for field in reversed(doc.fields):
        if field["link"]:
            return field["link"]
    return None


def run(r, doc, segs, link):
    bold = run_bold(r.find(w("rPr")), doc)
    for child in r:
        tag = child.tag
        if tag == w("fldChar"):
            kind = child.get(w("fldCharType"))
            if kind == "begin":
                doc.fields.append({"instr": "", "phase": "instr", "link": None})
            elif kind == "separate" and doc.fields:
                field = doc.fields[-1]
                field["phase"] = "result"
                m = re.search(r'HYPERLINK\s+(?:\\l\s+)?"([^"]+)"', field["instr"])
                if m:
                    field["link"] = m.group(1)
            elif kind == "end" and doc.fields:
                doc.fields.pop()
            continue
        if doc.fields and doc.fields[-1]["phase"] == "instr":
            if tag == w("instrText"):
                doc.fields[-1]["instr"] += child.text or ""
            continue

        here = link or current_field_link(doc)
        if tag == w("t"):
            add(segs, doc.clean(child.text), bold, here)
        elif tag == w("tab"):
            add(segs, "\t", bold, here)
        elif tag in (w("br"), w("cr")):
            kind = child.get(w("type"))
            if kind == "page":
                add(segs, " [PAGE BREAK] ")
            elif kind == "column":
                add(segs, " ")
            else:
                doc.counts["soft line breaks"] += 1
                add(segs, BREAK)
        elif tag == w("noBreakHyphen"):
            add(segs, "-", bold, here)
        elif tag == w("sym"):
            add(segs, f"[SYM {child.get(w('font'))} {child.get(w('char'))}]")
        elif tag in (w("drawing"), w("pict"), w("object")):
            for img in images_in(child, doc):
                doc.images.append(img)
                add(segs, f"[IMAGE {img}]")
        elif tag in (w("footnoteReference"), w("endnoteReference")):
            add(segs, f"[NOTE {child.get(w('id'))}]")
        elif tag == w("commentReference"):
            add(segs, f"[COMMENT {child.get(w('id'))}]")


def collect(el, doc, segs, link=None):
    for child in el:
        tag = child.tag
        if tag == w("r"):
            run(child, doc, segs, link)
        elif tag == w("hyperlink"):
            url = doc.rels.get(child.get(f"{{{R}}}id"))
            if not url and child.get(w("anchor")):
                url = "#" + child.get(w("anchor"))
            collect(child, doc, segs, url or link)
        elif tag == w("fldSimple"):
            m = re.search(r'HYPERLINK\s+(?:\\l\s+)?"([^"]+)"', child.get(w("instr")) or "")
            collect(child, doc, segs, m.group(1) if m else link)
        elif tag in (w("del"), w("moveFrom"), w("pPr"), w("rPr")):
            continue
        else:
            # ins, smartTag, sdt, sdtContent, customXml, bdo, dir ...
            collect(child, doc, segs, link)


def render(segs):
    out = []
    for text, bold, link in segs:
        if bold and text.strip():
            lead = text[: len(text) - len(text.lstrip())]
            trail = text[len(text.rstrip()):]
            text = f"{lead}**{text.strip()}**{trail}"
        if link and text.strip():
            text = f"[{text}]({link})"
        out.append(text)
    return "".join(out)


def para_text(p, doc):
    segs = []
    collect(p, doc, segs)
    return render(segs)


def cell_blocks(container, doc):
    """A cell's content in order: ("p", text) for each paragraph, ("tbl", el) for nested tables."""
    blocks = []
    for child in container:
        if child.tag == w("p"):
            text = para_text(child, doc)
            _, kind = para_tag(child, doc)
            if text.strip():
                if kind == "b":
                    text = "• " + text
                elif kind == "n":
                    text = "# " + text
                blocks.append(("p", text))
        elif child.tag == w("tbl"):
            blocks.append(("tbl", child))
        elif child.tag == w("sdt"):
            content = child.find(w("sdtContent"))
            if content is not None:
                blocks += cell_blocks(content, doc)
    return blocks


def cell_note(tc):
    """Merge/span annotations for a cell."""
    notes = ""
    tcpr = tc.find(w("tcPr"))
    if tcpr is not None:
        vm = tcpr.find(w("vMerge"))
        if vm is not None and vm.get(w("val"), "continue") == "continue":
            notes += " [merged with cell above]"
        gs = tcpr.find(w("gridSpan"))
        if gs is not None and int(gs.get(w("val"))) > 1:
            notes += f" [spans {gs.get(w('val'))} cols]"
    return notes


def table_lines(tbl, doc, indent=""):
    """
    Render a table. Simple tables (one paragraph per cell, nothing nested) print
    one line per row: "ROW i: a || b". Anything else prints one block per cell,
    one line per paragraph, with nested tables expanded underneath, so a " / "
    or " || " inside the copy can never be mistaken for a cell boundary.
    """
    grid = []
    for tr in tbl.findall(w("tr")):
        grid.append([(tc, cell_blocks(tc, doc)) for tc in tr.findall(w("tc"))])

    width = max((len(r) for r in grid), default=0)
    simple = all(len(b) <= 1 and all(k == "p" for k, _ in b) for row in grid for _, b in row)
    lines = []

    if simple:
        for i, row in enumerate(grid):
            cells = [(b[0][1] if b else "") + cell_note(tc) for tc, b in row]
            lines.append(f"{indent}ROW {i}: " + " || ".join(cells))
        return lines, len(grid), width

    for i, row in enumerate(grid):
        for j, (tc, blocks) in enumerate(row):
            lines.append(f"{indent}[row {i} cell {j}]{cell_note(tc)}")
            for kind, value in blocks:
                if kind == "p":
                    lines.append(f"{indent}    {value}")
                else:
                    nested, rows, cols = table_lines(value, doc, indent + "    ")
                    lines.append(f"{indent}    NESTED TABLE ({rows} rows x {cols} cols)")
                    lines += nested
    return lines, len(grid), width


def walk(container, doc, lines, state):
    for child in container:
        tag = child.tag
        if tag == w("p"):
            text = para_text(child, doc)
            if not text.strip():
                continue
            ptag, _ = para_tag(child, doc)
            state["paragraphs"] += 1
            lines.append(f"[{ptag}] {text}")
        elif tag == w("tbl"):
            state["tables"] += 1
            n = state["tables"]
            body, rows, width = table_lines(child, doc)
            lines.append("")
            lines.append(f"----- TABLE {n} ({rows} rows x {width} cols) -----")
            lines += body
            lines.append(f"----- END TABLE {n} -----")
            lines.append("")
        elif tag == w("sdt"):
            content = child.find(w("sdtContent"))
            if content is not None:
                walk(content, doc, lines, state)


def notes(doc, part, label):
    root = doc.xml(part)
    if root is None:
        return []
    out = []
    for item in root:
        if item.get(w("type")) in ("separator", "continuationSeparator"):
            continue
        texts = [para_text(p, doc) for p in item.iter(w("p"))]
        text = " ¶ ".join(t for t in texts if t.strip())
        if text:
            author = item.get(w("author"))
            who = f" ({author})" if author else ""
            out.append(f"[{label} {item.get(w('id'))}{who}] {text}")
    return out


def extract(path, outdir, save_images):
    with zipfile.ZipFile(path) as z:
        doc = Doc(z)
        body = doc.xml("word/document.xml").find(w("body"))
        lines = []
        state = {"paragraphs": 0, "tables": 0}
        walk(body, doc, lines, state)

        extra = notes(doc, "word/comments.xml", "COMMENT") + notes(doc, "word/footnotes.xml", "NOTE") + notes(doc, "word/endnotes.xml", "NOTE")
        if extra:
            lines += ["", "===== COMMENTS AND NOTES ====="] + extra

        saved = []
        if save_images and doc.images:
            image_dir = outdir / path.stem
            image_dir.mkdir(parents=True, exist_ok=True)
            for target in dict.fromkeys(doc.images):
                part = "word/" + target.lstrip("/") if not target.startswith("word/") else target
                if part in doc.names:
                    dest = image_dir / pathlib.PurePosixPath(target).name
                    dest.write_bytes(z.read(part))
                    saved.append(dest)

    invisible = ", ".join(f"{k} x{v}" for k, v in sorted(doc.counts.items())) or "none"
    header = [
        f"===== {path.name} =====",
        f"paragraphs: {state['paragraphs']}  tables: {state['tables']}  images: {len(doc.images)}",
        f"normalised: {invisible}",
        "",
    ]

    outdir.mkdir(parents=True, exist_ok=True)
    out_path = outdir / f"{path.stem}.txt"
    out_path.write_text("\n".join(header + lines) + "\n", encoding="utf-8")
    return out_path, state, doc, saved


def main():
    parser = argparse.ArgumentParser(description="Extract a case study .docx into reviewable text.")
    parser.add_argument("path", help="A .docx file, or a folder of them")
    parser.add_argument("--outdir", default="out", help="Output directory (default: ./out next to this script)")
    parser.add_argument("--images", action="store_true", help="Also save embedded pictures to out/<name>/")
    args = parser.parse_args()

    source = pathlib.Path(args.path).expanduser().resolve()
    if source.is_dir():
        files = sorted(p for p in source.glob("*.docx") if not p.name.startswith("~$"))
    elif source.is_file():
        files = [source]
    else:
        sys.exit(f"Not found: {source}")
    if not files:
        sys.exit(f"No .docx files in {source}")

    outdir = pathlib.Path(args.outdir)
    if not outdir.is_absolute():
        outdir = pathlib.Path(__file__).parent / outdir

    for path in files:
        out_path, state, doc, saved = extract(path, outdir, args.images)
        print(f"{path.name}")
        print(f"  text   : {out_path}")
        print(f"  blocks : {state['paragraphs']} paragraphs, {state['tables']} tables, {len(doc.images)} images")
        if doc.counts:
            print("  fixed  : " + ", ".join(f"{k} x{v}" for k, v in sorted(doc.counts.items())))
        for dest in saved:
            print(f"  image  : {dest}")

    print()
    print("Next: fill content/<slug>.json from the text, then dry-run import-case-studies.php")


if __name__ == "__main__":
    main()
