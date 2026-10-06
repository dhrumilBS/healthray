"""
Step 1 of the Alternatives post workflow: turn a Google Docs PDF export into
reviewable text.

Google Docs PDFs emit one word per text-showing operation, so a naive text dump
is unusable. This script rebuilds visual lines from word coordinates and pulls
the comparison table out cell by cell.

Usage
    python extract-pdf.py "path/to/Ezovion Alternatives.pdf"
    python extract-pdf.py "path/to/doc.pdf" --outdir out

Writes two files next to each other in the output directory:
    <name>.prose.txt   page-by-page text, one visual line per line
    <name>.tables.txt  every detected table, one cell per "||" separated field

Requires: pdfplumber  (pip install pdfplumber)
"""

import argparse
import pathlib
import sys

try:
    import pdfplumber
except ImportError:
    sys.exit("pdfplumber is not installed. Run: python -m pip install pdfplumber")


# Google Docs substitutes typographic ligature glyphs for these letter pairs.
# They are font artifacts, not characters the author typed, so map them back.
# Curly quotes and dashes are deliberately left alone: those are real content.
LIGATURES = {
    "\ufb00": "ff",
    "\ufb01": "fi",
    "\ufb02": "fl",
    "\ufb03": "ffi",
    "\ufb04": "ffl",
    "\ufb05": "st",
    "\ufb06": "st",
}

# Words whose baselines fall within this many points are treated as one line.
LINE_TOLERANCE = 3.0


def clean(text):
    if text is None:
        return ""
    for bad, good in LIGATURES.items():
        text = text.replace(bad, good)
    return text.replace("\u00a0", " ")


def extract(pdf_path, outdir):
    prose = []
    tables = []

    with pdfplumber.open(pdf_path) as pdf:
        for pageno, page in enumerate(pdf.pages, start=1):
            prose.append(f"\n===== PAGE {pageno} =====\n")

            words = page.extract_words(use_text_flow=True, keep_blank_chars=False)
            lines = {}
            for word in words:
                lines.setdefault(round(word["top"] / LINE_TOLERANCE), []).append(word)

            for key in sorted(lines):
                row = sorted(lines[key], key=lambda w: w["x0"])
                line = " ".join(clean(w["text"]) for w in row).strip()
                if line:
                    prose.append(line + "\n")

            for tableno, table in enumerate(page.extract_tables(), start=1):
                tables.append(f"\n===== PAGE {pageno} TABLE {tableno} =====\n")
                for rowno, row in enumerate(table):
                    cells = [clean(c).replace("\n", " ").strip() for c in row]
                    tables.append(f"ROW {rowno}: " + " || ".join(cells) + "\n")

    outdir.mkdir(parents=True, exist_ok=True)
    stem = pdf_path.stem
    prose_path = outdir / f"{stem}.prose.txt"
    table_path = outdir / f"{stem}.tables.txt"

    prose_path.write_text("".join(prose), encoding="utf-8")
    table_path.write_text("".join(tables), encoding="utf-8")

    return prose_path, table_path, len("".join(prose)), len("".join(tables))


def main():
    parser = argparse.ArgumentParser(description="Extract text and tables from an Alternatives content PDF.")
    parser.add_argument("pdf", help="Path to the PDF")
    parser.add_argument("--outdir", default="out", help="Output directory (default: ./out)")
    args = parser.parse_args()

    pdf_path = pathlib.Path(args.pdf).expanduser().resolve()
    if not pdf_path.is_file():
        sys.exit(f"Not found: {pdf_path}")

    outdir = pathlib.Path(args.outdir)
    if not outdir.is_absolute():
        outdir = pathlib.Path(__file__).parent / outdir

    prose_path, table_path, prose_len, table_len = extract(pdf_path, outdir)

    print(f"source : {pdf_path}")
    print(f"prose  : {prose_path}  ({prose_len} chars)")
    print(f"tables : {table_path}  ({table_len} chars)")
    print()
    print("Next: fill a content JSON file (see README.md) and run import-alternatives.php")


if __name__ == "__main__":
    main()
