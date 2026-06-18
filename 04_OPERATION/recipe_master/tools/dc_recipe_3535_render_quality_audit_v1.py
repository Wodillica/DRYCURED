#!/usr/bin/env python3
from pathlib import Path
import json
import re
import sys
from html import unescape
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_3535_render_quality_audit_v1.py SNAPSHOT_HTML RESULT_JSON QA_REPORT REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

snapshot_path = Path(sys.argv[1])
result_path = Path(sys.argv[2])
qa_path = Path(sys.argv[3])
review_dir = Path(sys.argv[4])
review_dir.mkdir(parents=True, exist_ok=True)

html = snapshot_path.read_text(encoding="utf-8", errors="replace")
result = json.loads(result_path.read_text(encoding="utf-8"))
qa_old = qa_path.read_text(encoding="utf-8")

text = re.sub(r"<[^>]+>", " ", html)
text = unescape(text)
text_norm = re.sub(r"\s+", " ", text).strip()
html_l = html.lower()
text_l = text_norm.lower()

def has_any(*items):
    return any(i.lower() in text_l or i.lower() in html_l for i in items)

def add(checks, key, label, ok, severity, note):
    checks.append({
        "key": key,
        "label": label,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note,
    })

checks = []

clone = result.get("clone", {})
public_fetch = result.get("public_fetch", {})
meta = result.get("meta_values_preview", {})

render_len = len(html)
plain_len = len(text_norm)

dcv_marker = has_any("dcv5", "dcv62", "dcv111", "drycured-recipe", "dc-recipe", "dc-recipe-hero")
wprm_marker = has_any("wprm", "wp-recipe-maker")
raw_markdown_marker = bool(re.search(r"(^|\n)\s*#\s+", html)) or "# " in html[:1000]
private_notice = has_any("PRIVATNI PREVIEW", "PRIVATE PREVIEW", "nije javni recept", "NOT_PUBLIC", "PUBLIC_UPDATE_FORBIDDEN")

required_content = {
    "title": has_any("Jésus de Lyon", "Jesus de Lyon"),
    "raw_materials": has_any("Glavne sirovine", "svinjska lopatica", "potrbušina", "leđna slanina"),
    "spices": has_any("Začini", "morska sol", "crni papar", "bijeli papar"),
    "liquids_garlic": has_any("Tekućine", "češnjak", "konjak"),
    "grinding": has_any("Mljevenje", "rešetka", "6–8 mm"),
    "casing": has_any("Crijeva", "svinjska crijeva", "28–32"),
    "done_when": has_any("Gotovo je kad", "gubitak", "presjek"),
    "errors": has_any("Greške", "Problem", "Rješenje", "razmazuje"),
    "blockers": has_any("Aktivne blokade", "starter kulture", "needs_confirmation"),
}

add(checks, "clone_private", "Clone je private prema prethodnom QA-u", clone.get("post_status") == "private", "BLOCKER", "Privatni clone mora ostati private.")
add(checks, "public_404", "Privatni clone nije javno dostupan", str(public_fetch.get("http_code")) == "404" and public_fetch.get("publicly_exposed") is False, "BLOCKER", "Javni fetch mora biti 404 ili bez izlaganja recepta.")
add(checks, "render_snapshot_not_empty", "Render snapshot nije prazan", render_len > 500, "BLOCKER", f"HTML duljina: {render_len}.")
add(checks, "render_has_title", "Render sadrži naslov recepta", required_content["title"], "MAJOR", "Interni render mora sadržavati naziv.")
add(checks, "render_has_private_notice", "Render jasno označava privatni status", private_notice, "MAJOR", "Privatni sadržaj ne smije izgledati kao javni recept.")
add(checks, "meta_sections_json_present", "_dry_recipe_sections postoji u result JSON-u", bool(meta.get("_dry_recipe_sections")), "MAJOR", "Meta sekcije moraju biti dostupne za renderer.")
add(checks, "meta_process_json_present", "_dry_verified_process postoji u result JSON-u", bool(meta.get("_dry_verified_process")), "MAJOR", "Procesni meta mora biti dostupan.")
add(checks, "dcv_renderer_marker", "DCV/Drycured renderer marker prisutan u renderu", dcv_marker, "INFO", "Ako ovo padne, clone je vjerojatno samo markdown/content snapshot, ne dokaz punog javnog renderera.")
add(checks, "raw_markdown_detected", "Raw markdown nije dominantan", not raw_markdown_marker, "INFO", "Ako padne, sadržaj se možda ne renderira kroz konačni kartični prikaz.")
for key, ok in required_content.items():
    add(checks, f"content_{key}", f"Sadržaj prisutan: {key}", ok, "MAJOR", "Obvezni sadržaj mora biti vidljiv u internom render snapshotu.")

failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] != "INFO"]
info_fails = [c for c in checks if c["status"] == "FAIL" and c["severity"] == "INFO"]
blocker_fails = [c for c in failures if c["severity"] == "BLOCKER"]

if blocker_fails:
    render_status = "FAIL_BLOCKER"
elif failures:
    render_status = "PASS_WITH_CONTENT_GAPS"
elif dcv_marker and not raw_markdown_marker:
    render_status = "PASS_RENDERER_LIKELY_ACTIVE"
else:
    render_status = "PASS_CONTENT_READY_RENDERER_NOT_PROVEN"

payload = {
    "generated_at": datetime.now(timezone.utc).isoformat(),
    "clone_id": result.get("clone_id", 3535),
    "source_post_id": result.get("source_post_id", 3042),
    "render_status": render_status,
    "render_html_length": render_len,
    "render_plain_length": plain_len,
    "dcv_marker_present": dcv_marker,
    "wprm_marker_present": wprm_marker,
    "raw_markdown_detected": raw_markdown_marker,
    "private_notice_present": private_notice,
    "required_content": required_content,
    "checks": checks,
    "fail_total_major_or_blocker": len(failures),
    "fail_total_info": len(info_fails),
    "blocker_fail_total": len(blocker_fails),
}

(review_dir / "3535_render_quality_audit_v1.json").write_text(
    json.dumps(payload, ensure_ascii=False, indent=2),
    encoding="utf-8"
)

csv_lines = ["key,label,status,severity,note"]
for c in checks:
    def esc(x):
        return '"' + str(x).replace('"', '""') + '"'
    csv_lines.append(",".join([esc(c["key"]), esc(c["label"]), esc(c["status"]), esc(c["severity"]), esc(c["note"])]))
(review_dir / "3535_render_quality_audit_v1.csv").write_text("\n".join(csv_lines) + "\n", encoding="utf-8")

md = []
md.append("# 3535 private clone render quality audit v1")
md.append("")
md.append(f"Status: **{render_status}**")
md.append("")
md.append("Ovaj audit ne mijenja WordPress. Analizira postojeći render snapshot privatnog clonea.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Clone ID: `3535`")
md.append(f"- Source post ID: `3042`")
md.append(f"- Render HTML length: `{render_len}`")
md.append(f"- Render plain text length: `{plain_len}`")
md.append(f"- DCV/Drycured marker present: `{str(dcv_marker).lower()}`")
md.append(f"- WPRM marker present: `{str(wprm_marker).lower()}`")
md.append(f"- Raw markdown detected: `{str(raw_markdown_marker).lower()}`")
md.append(f"- Private notice present: `{str(private_notice).lower()}`")
md.append(f"- Major/blocker fail total: `{len(failures)}`")
md.append(f"- Info fail total: `{len(info_fails)}`")
md.append(f"- Blocker fail total: `{len(blocker_fails)}`")
md.append("")
md.append("## Obvezni sadržaj")
md.append("")
md.append("| Element | Status |")
md.append("|---|---|")
for k, ok in required_content.items():
    md.append(f"| {k} | {'PASS' if ok else 'FAIL'} |")
md.append("")
md.append("## QA tablica")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    md.append(f"| {c['label'].replace('|','/')} | {c['status']} | {c['severity']} | {c['note'].replace('|','/')} |")
md.append("")
md.append("## Zaključak")
md.append("")
if render_status == "PASS_RENDERER_LIKELY_ACTIVE":
    md.append("Render snapshot izgleda kao da koristi postojeći Drycured/DCV renderer. Sljedeći korak može biti vizualna provjera privatnog prikaza u admin/preview kontekstu.")
elif render_status == "PASS_CONTENT_READY_RENDERER_NOT_PROVEN":
    md.append("Sadržaj je mapiran i prisutan, ali audit ne dokazuje da se koristi konačni Drycured kartični renderer. Sljedeći korak je odlučiti treba li dodatni admin-only preview hook ili ručna provjera u WP adminu.")
elif render_status == "PASS_WITH_CONTENT_GAPS":
    md.append("Privatni clone je siguran, ali render ima sadržajne rupe. Ne nastavljati dok se ne popune.")
else:
    md.append("Postoji blocker. Ne nastavljati.")
md.append("")
md.append("Javni WordPress update i dalje nije dopušten.")
md.append("")

(report_path := review_dir / "3535_RENDER_QUALITY_AUDIT_REPORT.md").write_text("\n".join(md), encoding="utf-8")

marker = "<!-- DC_3535_RENDER_QUALITY_AUDIT_V1 -->"
append = f"""
{marker}

## 3535 render quality audit v1

Status: **{render_status}**

- DCV/Drycured marker present: `{str(dcv_marker).lower()}`
- Raw markdown detected: `{str(raw_markdown_marker).lower()}`
- Private notice present: `{str(private_notice).lower()}`
- Major/blocker fail total: `{len(failures)}`
- Public update allowed: `false`

Report: `review/{review_dir.name}/3535_RENDER_QUALITY_AUDIT_REPORT.md`
"""

if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3535 RENDER QUALITY AUDIT COMPLETE ===")
print(f"RENDER_STATUS={render_status}")
print(f"DCV_MARKER_PRESENT={str(dcv_marker).lower()}")
print(f"RAW_MARKDOWN_DETECTED={str(raw_markdown_marker).lower()}")
print(f"PRIVATE_NOTICE_PRESENT={str(private_notice).lower()}")
print(f"FAIL_TOTAL_MAJOR_OR_BLOCKER={len(failures)}")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_fails)}")
print("PUBLIC_UPDATE_ALLOWED=false")
print(f"REPORT={report_path}")
