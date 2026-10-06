# TEMPORARY read-only helper (delete after use): word-level diff of the MD body
# (markdown syntax stripped) against the PDF prose extraction, tables excluded.
import difflib
import re
import pathlib

MD = pathlib.Path(r"d:\xampp\htdocs\healthray\wp-content\themes\stratusx-child\assets\content\SmartHMIS Alternatives.md")
PROSE = pathlib.Path(r"d:\xampp\htdocs\healthray\wp-content\themes\stratusx-child\tools\alternatives\out\SmartHMIS Alternatives.prose.txt")

md = MD.read_text(encoding="utf-8")
md = md[: md.index("[image1]: <data:")]
md_lines = []
for line in md.splitlines():
    s = line.strip()
    if s.count("|") >= 8:
        # comparison table rows are diffed separately
        continue
    if re.fullmatch(r"\|[\s:\-|]+\|", s):
        continue
    md_lines.append(line)
md = "\n".join(md_lines)
md = re.sub(r"!\[\]\[image\d+\]", " ", md)
md = re.sub(r"\[([^\]]+)\]\((https?://[^)]+)\)", r"\1", md)
md = re.sub(r"\\(.)", r"\1", md)
md = md.replace("**", "").replace("*", " ")
md = re.sub(r"^\s*#+\s*", "", md, flags=re.M)
md = md.replace("|", " ")

prose = PROSE.read_text(encoding="utf-8")
pages = re.split(r"===== PAGE (\d+) =====", prose)
kept = []
for i in range(1, len(pages), 2):
    if pages[i] in ("2", "3", "4"):
        continue
    kept.append(pages[i + 1])
prose = "\n".join(kept).replace("●", " ")

def words(t):
    return t.split()

a, b = words(md), words(prose)
sm = difflib.SequenceMatcher(None, a, b, autojunk=False)
n = 0
for tag, i1, i2, j1, j2 in sm.get_opcodes():
    if tag == "equal":
        continue
    n += 1
    ctx = " ".join(a[max(0, i1 - 4):i1])
    print(f"[{tag}] ...{ctx} | MD: {' '.join(a[i1:i2])!r} | PDF: {' '.join(b[j1:j2])!r}")
print(f"\n{n} differing spans; MD words={len(a)} PDF words={len(b)}")
