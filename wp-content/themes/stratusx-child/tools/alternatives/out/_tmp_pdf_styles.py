# TEMPORARY read-only helper (delete after use): reports invisible characters
# and italic/bold runs in the SmartHMIS PDF so the MD formatting can be verified.
import sys
import pdfplumber

PDF = r"d:\xampp\htdocs\healthray\wp-content\themes\stratusx-child\assets\content\SmartHMIS Alternatives.pdf"
WATCH = {0x0B: "VT", 0xA0: "NBSP", 0x200B: "ZWSP", 0xFEFF: "BOM", 0xFB01: "fi-lig", 0xFB02: "fl-lig", 0xAD: "SHY"}
mode = sys.argv[1] if len(sys.argv) > 1 else "italic"

counts = {}
fonts = {}
with pdfplumber.open(PDF) as pdf:
    for pageno, page in enumerate(pdf.pages, start=1):
        chars = page.chars
        for c in chars:
            for ch in c["text"]:
                if ord(ch) in WATCH:
                    counts[WATCH[ord(ch)]] = counts.get(WATCH[ord(ch)], 0) + 1
            fonts[c["fontname"]] = fonts.get(c["fontname"], 0) + 1
        if mode == "fonts":
            continue
        run, flag, last_top = [], None, None
        def flush():
            if run and flag:
                text = "".join(run).strip()
                if text:
                    print(f"p{pageno}: {text}")
        for c in chars:
            name = c["fontname"].lower()
            m = c.get("matrix") or (1, 0, 0, 1, 0, 0)
            is_it = ("italic" in name) or ("oblique" in name) or abs(m[2]) > 0.05
            is_bd = "bold" in name
            want = is_it if mode == "italic" else is_bd
            if want != flag:
                flush()
                run, flag = [], want
            if last_top is not None and abs(c["top"] - last_top) > 3:
                run.append(" ")
            run.append(c["text"])
            last_top = c["top"]
        flush()

print("\nINVISIBLE/LIGATURE COUNTS:", counts or "none")
if mode == "fonts":
    for k, v in sorted(fonts.items(), key=lambda kv: -kv[1]):
        print(f"  {v:6d}  {k}")
