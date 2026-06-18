#!/usr/bin/env python3
from pathlib import Path
import json
import re
import sys
from html import escape
from datetime import datetime, timezone

if len(sys.argv) != 4:
    print("Usage: dc_recipe_3042_private_preview_adapter_dryrun_v1.py DOSSIER_DIR RECIPE_YML QA_REPORT", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
recipe_path = Path(sys.argv[2])
qa_path = Path(sys.argv[3])

review_dirs = sorted((dossier_dir / "review").glob("private_preview_adapter_dryrun_v1_*"))
review_dir = review_dirs[-1] if review_dirs else dossier_dir / "review" / "private_preview_adapter_dryrun_v1_manual"
review_dir.mkdir(parents=True, exist_ok=True)

text = recipe_path.read_text(encoding="utf-8")
qa_old = qa_path.read_text(encoding="utf-8")

def strip_quotes(v):
    v = str(v).strip()
    if (v.startswith('"') and v.endswith('"')) or (v.startswith("'") and v.endswith("'")):
        return v[1:-1]
    return v

def top_value(key, default=None):
    m = re.search(rf"^{re.escape(key)}:\s*(.+)$", text, flags=re.M)
    if not m:
        return default
    return strip_quotes(m.group(1).strip())

def section(name):
    pattern = rf"^{re.escape(name)}:\n(.*?)(?=^[A-Za-z0-9_]+:|\Z)"
    m = re.search(pattern, text, flags=re.M | re.S)
    return m.group(1) if m else ""

def parse_list_objects(section_text):
    items = []
    current = None
    for raw in section_text.splitlines():
        line = raw.rstrip()
        if re.match(r"^\s*-\s+item:\s*", line):
            if current:
                items.append(current)
            current = {}
            current["item"] = strip_quotes(re.sub(r"^\s*-\s+item:\s*", "", line).strip())
            continue
        if current is not None:
            m = re.match(r"^\s{4,}([A-Za-z0-9_]+):\s*(.*)$", line)
            if m:
                k, v = m.group(1), strip_quotes(m.group(2).strip())
                current[k] = v
    if current:
        items.append(current)
    return items

def parse_dash_list(section_text):
    out = []
    for raw in section_text.splitlines():
        line = raw.strip()
        if line.startswith("- "):
            out.append(strip_quotes(line[2:].strip()))
    return out

def parse_errors(section_text):
    errors = []
    blocks = re.split(r"\n\s*-\s+problem:\s*", section_text)
    for b in blocks[1:]:
        problem = b.splitlines()[0].strip().strip('"')
        obj = {"problem": problem}
        for key in ["cause", "solution", "risk"]:
            m = re.search(rf"^\s+{key}:\s*(.+)$", b, flags=re.M)
            if m:
                obj[key] = strip_quotes(m.group(1).strip())
        errors.append(obj)
    return errors

def scalar_from_section(sec, key, default=None):
    m = re.search(rf"^\s+{re.escape(key)}:\s*(.+)$", sec, flags=re.M)
    return strip_quotes(m.group(1).strip()) if m else default

raw_materials = parse_list_objects(section("raw_materials_kg"))
spices = parse_list_objects(section("spices_and_additives_g"))
liquids = parse_list_objects(section("liquids"))
done_when = parse_dash_list(section("done_when"))
errors = parse_errors(section("common_errors_and_solutions"))
blockers = parse_dash_list(section("qa_blockers_before_public_update"))

identity_sec = section("product_identity")
batch_sec = section("batch")
grinding_sec = section("grinding")
casing_sec = section("casing")
garlic_sec = section("garlic_liquid_details")
nitrite_sec = section("nitrite_salt")
sensory_sec = section("sensory_profile")
storage_sec = section("serving_and_storage")

payload = {
    "adapter_mode": "DRY_RUN_OFFLINE_ONLY",
    "generated_at": datetime.now(timezone.utc).isoformat(),
    "public_update_allowed": False,
    "wordpress_write_allowed": False,
    "post_id": 3042,
    "source_recipe_yml": str(recipe_path),
    "status": top_value("dossier_status"),
    "recipe_type": top_value("recipe_type"),
    "title": top_value("title"),
    "url": top_value("url"),
    "batch_size_kg": top_value("batch_size_kg"),
    "hero": {
        "title": top_value("title"),
        "region_country": scalar_from_section(identity_sec, "region_country"),
        "product_family": scalar_from_section(identity_sec, "product_family"),
        "product_type": scalar_from_section(identity_sec, "product_type"),
        "status_note": scalar_from_section(identity_sec, "public_status_note")
    },
    "quick_summary": {
        "batch": "10 kg",
        "type": "mljeveno/usitnjeno meso u prirodnom ovitku",
        "duration": "oko 45–60 dana",
        "public_status": "NOT_PUBLIC_DOSSIER_ONLY"
    },
    "raw_materials_kg": raw_materials,
    "spices_and_additives_g": spices,
    "liquids": liquids,
    "garlic": {
        "used": scalar_from_section(garlic_sec, "used"),
        "mode": scalar_from_section(garlic_sec, "mode"),
        "garlic_amount_g": scalar_from_section(garlic_sec, "garlic_amount_g"),
        "note": scalar_from_section(garlic_sec, "note")
    },
    "grinding": {
        "pre_cut": scalar_from_section(grinding_sec, "pre_cut"),
        "meat_plate_mm": scalar_from_section(grinding_sec, "meat_plate_mm"),
        "fat_handling": scalar_from_section(grinding_sec, "fat_handling"),
        "temperature_control": scalar_from_section(grinding_sec, "temperature_control"),
        "qa_note": scalar_from_section(grinding_sec, "qa_note")
    },
    "casing": {
        "type": scalar_from_section(casing_sec, "type"),
        "diameter_mm": scalar_from_section(casing_sec, "diameter_mm"),
        "soaking_liquid": scalar_from_section(casing_sec, "soaking_liquid"),
        "soaking_time": scalar_from_section(casing_sec, "soaking_time"),
        "soaking_temperature_c": scalar_from_section(casing_sec, "soaking_temperature_c"),
        "boiled_or_cold_liquid": scalar_from_section(casing_sec, "boiled_or_cold_liquid")
    },
    "nitrite_salt": {
        "used": scalar_from_section(nitrite_sec, "used"),
        "note": scalar_from_section(nitrite_sec, "note")
    },
    "sensory_profile": {
        "texture": scalar_from_section(sensory_sec, "texture"),
        "cut_surface": scalar_from_section(sensory_sec, "cut_surface"),
        "aroma": scalar_from_section(sensory_sec, "aroma")
    },
    "done_when": done_when,
    "common_errors_and_solutions": errors,
    "serving_and_storage": {
        "serving": scalar_from_section(storage_sec, "serving"),
        "pairing": scalar_from_section(storage_sec, "pairing"),
        "storage": scalar_from_section(storage_sec, "storage"),
        "safety_discard": scalar_from_section(storage_sec, "safety_discard")
    },
    "active_blockers": blockers,
    "adapter_decision": {
        "private_preview_payload_ready": True,
        "public_update_allowed": False,
        "reason": "Recipe.yml je QA PASS kao radni nacrt, ali javne blokade ostaju aktivne."
    }
}

payload_path = review_dir / "3042_private_preview_adapter_payload.json"
payload_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

required_payload_fields = [
    ("hero.title", payload["hero"]["title"]),
    ("raw_materials_kg", raw_materials),
    ("spices_and_additives_g", spices),
    ("garlic.mode", payload["garlic"]["mode"]),
    ("grinding.meat_plate_mm", payload["grinding"]["meat_plate_mm"]),
    ("casing.type", payload["casing"]["type"]),
    ("done_when", done_when),
    ("common_errors_and_solutions", errors),
    ("active_blockers", blockers),
]

contract_rows = []
for key, val in required_payload_fields:
    ok = bool(val)
    contract_rows.append({
        "field": key,
        "status": "PASS" if ok else "FAIL",
        "note": "Polje mapirano iz recipe.yml." if ok else "Polje nije pronađeno ili je prazno."
    })

contract_path = review_dir / "3042_private_preview_adapter_contract.csv"
contract_path.write_text(
    "field,status,note\n" + "\n".join(
        f'"{r["field"]}","{r["status"]}","{r["note"]}"' for r in contract_rows
    ) + "\n",
    encoding="utf-8"
)

def html_list(items):
    return "<ul>" + "".join(f"<li>{escape(str(x))}</li>" for x in items) + "</ul>"

def html_table(rows, cols):
    out = ["<table><thead><tr>"]
    for c in cols:
        out.append(f"<th>{escape(c)}</th>")
    out.append("</tr></thead><tbody>")
    for r in rows:
        out.append("<tr>")
        for c in cols:
            out.append(f"<td>{escape(str(r.get(c, '')))}</td>")
        out.append("</tr>")
    out.append("</tbody></table>")
    return "".join(out)

preview_html = f"""<!doctype html>
<html lang="hr">
<head>
<meta charset="utf-8">
<title>PRIVATE PREVIEW — 3042 Jésus de Lyon</title>
<style>
body {{ font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin: 32px; line-height: 1.5; color: #1f2933; background: #f7f2ea; }}
main {{ max-width: 1100px; margin: 0 auto; background: #fffaf2; padding: 28px; border-radius: 18px; }}
h1, h2 {{ color: #4a2414; }}
.warning {{ background: #fff3cd; padding: 14px 16px; border-radius: 12px; margin: 16px 0; }}
.blocker {{ background: #fde2e2; padding: 14px 16px; border-radius: 12px; margin: 16px 0; }}
.card {{ background: #ffffff; padding: 16px; border-radius: 14px; margin: 14px 0; box-shadow: 0 1px 5px rgba(0,0,0,.06); }}
table {{ width: 100%; border-collapse: collapse; margin: 12px 0; }}
th, td {{ border-bottom: 1px solid #eadfce; text-align: left; padding: 8px; vertical-align: top; }}
small {{ color: #667085; }}
</style>
</head>
<body>
<main>
<h1>{escape(payload["title"])}</h1>
<div class="warning"><strong>PRIVATE PREVIEW / DOSSIER ONLY.</strong> Ovo nije javni recept i ne smije se objaviti dok su blokade aktivne.</div>

<section class="card">
<h2>Brzi sažetak</h2>
<p><strong>Regija:</strong> {escape(str(payload["hero"]["region_country"]))}<br>
<strong>Tip:</strong> {escape(str(payload["hero"]["product_type"]))}<br>
<strong>Šarža:</strong> 10 kg<br>
<strong>Status:</strong> NOT_PUBLIC_DOSSIER_ONLY</p>
</section>

<section class="card">
<h2>Glavne sirovine</h2>
{html_table(raw_materials, ["item", "amount_kg", "percent", "source_status"])}
</section>

<section class="card">
<h2>Začini i dodaci</h2>
{html_table(spices, ["item", "amount_g", "g_per_kg", "source_status"])}
</section>

<section class="card">
<h2>Tekućine i češnjak</h2>
{html_table(liquids, ["item", "amount_l", "amount_ml", "source_status"])}
<p><strong>Češnjak:</strong> {escape(str(payload["garlic"]["mode"]))}</p>
</section>

<section class="card">
<h2>Mljevenje i masnoća</h2>
<p><strong>Rezanje:</strong> {escape(str(payload["grinding"]["pre_cut"]))}<br>
<strong>Rešetka:</strong> {escape(str(payload["grinding"]["meat_plate_mm"]))} mm<br>
<strong>Masnoća:</strong> {escape(str(payload["grinding"]["fat_handling"]))}<br>
<strong>Temperatura:</strong> {escape(str(payload["grinding"]["temperature_control"]))}</p>
</section>

<section class="card">
<h2>Crijeva / ovitak</h2>
<p><strong>Tip:</strong> {escape(str(payload["casing"]["type"]))}<br>
<strong>Promjer:</strong> {escape(str(payload["casing"]["diameter_mm"]))} mm<br>
<strong>Namakanje:</strong> {escape(str(payload["casing"]["soaking_time"]))}, {escape(str(payload["casing"]["soaking_liquid"]))}, {escape(str(payload["casing"]["soaking_temperature_c"]))} °C<br>
<strong>Prokuhavanje:</strong> {escape(str(payload["casing"]["boiled_or_cold_liquid"]))}</p>
</section>

<section class="card">
<h2>Gotovo je kad…</h2>
{html_list(done_when)}
</section>

<section class="card">
<h2>Greške i rješenja</h2>
{html_table(errors, ["problem", "cause", "solution", "risk"])}
</section>

<section class="blocker">
<h2>Aktivne blokade</h2>
{html_list(blockers)}
</section>

<p><small>Generated offline from recipe.yml. WordPress nije mijenjan.</small></p>
</main>
</body>
</html>
"""

html_path = review_dir / "3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN.html"
html_path.write_text(preview_html, encoding="utf-8")

fail_total = sum(1 for r in contract_rows if r["status"] == "FAIL")
private_preview_ready = fail_total == 0 and payload["public_update_allowed"] is False

report = []
report.append("# 3042 Jésus de Lyon — private preview adapter dry-run v1")
report.append("")
report.append("Status: **PRIVATE_PREVIEW_PAYLOAD_READY — PUBLIC_UPDATE_FORBIDDEN**")
report.append("")
report.append("Ovaj korak ne mijenja WordPress. Iz `recipe.yml` je izrađen offline adapter payload i lokalni HTML preview u dosjeu.")
report.append("")
report.append("## Sažetak")
report.append("")
report.append(f"- Contract checks: {len(contract_rows)}")
report.append(f"- Contract FAIL: {fail_total}")
report.append(f"- Private preview payload ready: `{str(private_preview_ready).lower()}`")
report.append("- Public update allowed: `false`")
report.append("")
report.append("## Contract check")
report.append("")
report.append("| Polje | Status | Napomena |")
report.append("|---|---|---|")
for r in contract_rows:
    report.append(f"| {r['field']} | {r['status']} | {r['note']} |")
report.append("")
report.append("## Izlazne datoteke")
report.append("")
report.append(f"- `{payload_path.name}`")
report.append(f"- `{contract_path.name}`")
report.append(f"- `{html_path.name}`")
report.append("")
report.append("## Aktivne blokade")
report.append("")
for b in blockers:
    report.append(f"- {b}")
report.append("")
report.append("## Zaključak")
report.append("")
report.append("Recept smije ići samo u privatni/offline preview za provjeru mapiranja podataka. Javni WordPress update nije dopušten.")
report.append("")
report_path = review_dir / "3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN_REPORT.md"
report_path.write_text("\n".join(report), encoding="utf-8")

marker = "<!-- DC_3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN_V1 -->"
append = f"""
{marker}

## Private preview adapter dry-run v1

Status: **PRIVATE_PREVIEW_PAYLOAD_READY — PUBLIC_UPDATE_FORBIDDEN**

- Contract checks: {len(contract_rows)}
- Contract FAIL: {fail_total}
- Private preview payload ready: `{str(private_preview_ready).lower()}`
- Public update allowed: `false`

Report: `review/{review_dir.name}/3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN_REPORT.md`
Payload: `review/{review_dir.name}/3042_private_preview_adapter_payload.json`
HTML preview: `review/{review_dir.name}/3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN.html`
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3042 PRIVATE PREVIEW ADAPTER DRY-RUN COMPLETE ===")
print(f"CONTRACT_CHECKS={len(contract_rows)}")
print(f"CONTRACT_FAIL={fail_total}")
print(f"PRIVATE_PREVIEW_PAYLOAD_READY={str(private_preview_ready).lower()}")
print("PUBLIC_UPDATE_ALLOWED=false")
print(f"PAYLOAD={payload_path}")
print(f"HTML_PREVIEW={html_path}")
print(f"REPORT={report_path}")
