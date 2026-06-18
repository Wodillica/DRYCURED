#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone
from html import escape

if len(sys.argv) != 5:
    print("Usage: dc_recipe_3042_private_wp_preview_plan_v1.py DOSSIER_DIR RECIPE_YML PAYLOAD_JSON QA_REPORT", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
recipe_path = Path(sys.argv[2])
payload_path = Path(sys.argv[3])
qa_path = Path(sys.argv[4])

review_dirs = sorted((dossier_dir / "review").glob("private_wp_preview_plan_v1_*"))
review_dir = review_dirs[-1] if review_dirs else dossier_dir / "review" / "private_wp_preview_plan_v1_manual"
review_dir.mkdir(parents=True, exist_ok=True)

recipe_text = recipe_path.read_text(encoding="utf-8")
qa_old = qa_path.read_text(encoding="utf-8")
payload = json.loads(payload_path.read_text(encoding="utf-8"))

source_post_id = 3042
title = payload.get("title", "Jésus de Lyon – debela suha kobasica")
now = datetime.now(timezone.utc).isoformat()

def must(cond, msg):
    if not cond:
        print("FAIL: " + msg, file=sys.stderr)
        sys.exit(1)

must(payload.get("public_update_allowed") is False, "payload mora imati public_update_allowed=false")
must(payload.get("adapter_mode") == "DRY_RUN_OFFLINE_ONLY", "payload mora biti offline dry-run")
must("public_update_allowed: false" in recipe_text, "recipe.yml mora imati public_update_allowed=false")
must("CANON_DRAFT_V1_NOT_PUBLIC" in recipe_text, "recipe.yml mora biti CANON_DRAFT_V1_NOT_PUBLIC")

raw = payload.get("raw_materials_kg", [])
spices = payload.get("spices_and_additives_g", [])
liquids = payload.get("liquids", [])
errors = payload.get("common_errors_and_solutions", [])
done_when = payload.get("done_when", [])
blockers = payload.get("active_blockers", [])

sections = {
    "hero": payload.get("hero", {}),
    "quick_summary": payload.get("quick_summary", {}),
    "raw_materials_kg": raw,
    "spices_and_additives_g": spices,
    "liquids": liquids,
    "garlic": payload.get("garlic", {}),
    "grinding": payload.get("grinding", {}),
    "casing": payload.get("casing", {}),
    "nitrite_salt": payload.get("nitrite_salt", {}),
    "sensory_profile": payload.get("sensory_profile", {}),
    "done_when": done_when,
    "common_errors_and_solutions": errors,
    "serving_and_storage": payload.get("serving_and_storage", {}),
    "active_blockers": blockers,
}

verified_process = {
    "recipe_type": "GROUND_MEAT_OR_CASING",
    "status": "PRIVATE_PREVIEW_ONLY",
    "public_update_allowed": False,
    "source_post_id": source_post_id,
    "source_recipe_yml": str(recipe_path),
    "phases": [
        {
            "key": "preparation",
            "title": "Priprema sirovine",
            "summary": "Meso narezati na kocke 2–3 cm i držati vrlo hladnim.",
            "status": "draft_from_recipe_yml"
        },
        {
            "key": "grinding",
            "title": "Mljevenje",
            "summary": "Meso i masnoću obraditi kroz rešetku 6–8 mm; temperatura smjese ne smije prijeći 8 °C.",
            "status": "draft_from_recipe_yml"
        },
        {
            "key": "stuffing",
            "title": "Punjenje",
            "summary": "Puniti u svinjska crijeva 28–32 mm, prethodno namočena 30–45 minuta u mlakoj vodi.",
            "status": "draft_from_recipe_yml"
        },
        {
            "key": "drying_aging",
            "title": "Sušenje i zrenje",
            "summary": "Radna smjernica: 10–15 °C, 70–80 % RH, do gubitka 35–40 % mase.",
            "status": "draft_guideline"
        },
        {
            "key": "smoking",
            "title": "Dimljenje",
            "summary": "Nije potvrđeno kao obvezna faza; ostaje needs_confirmation.",
            "status": "blocked_needs_confirmation"
        }
    ],
    "blockers": blockers
}

markdown = []
markdown.append(f"# {title}")
markdown.append("")
markdown.append("> PRIVATNI PREVIEW — DOSJE. Ovo nije javni recept i ne smije se objaviti dok su blokade aktivne.")
markdown.append("")
markdown.append("## Brzi sažetak")
markdown.append("")
markdown.append("- Tip: mljeveno/usitnjeno meso u prirodnom ovitku")
markdown.append("- Šarža: 10 kg")
markdown.append("- Status: CANON_DRAFT_V1_NOT_PUBLIC")
markdown.append("- Javni update: zabranjen")
markdown.append("")
markdown.append("## Glavne sirovine")
markdown.append("")
for r in raw:
    markdown.append(f"- {r.get('item')}: {r.get('amount_kg')} kg")
markdown.append("")
markdown.append("## Začini i dodaci")
markdown.append("")
for r in spices:
    amount = r.get("amount_g", "")
    gkg = r.get("g_per_kg", "")
    markdown.append(f"- {r.get('item')}: {amount} g ({gkg} g/kg)")
markdown.append("")
markdown.append("## Tekućine i češnjak")
markdown.append("")
for r in liquids:
    markdown.append(f"- {r.get('item')}: {r.get('amount_ml', r.get('amount_l', ''))} ml")
markdown.append(f"- Češnjak: {payload.get('garlic', {}).get('mode', '')}")
markdown.append("")
markdown.append("## Mljevenje i obrada masnoće")
markdown.append("")
markdown.append(f"- Rešetka: {payload.get('grinding', {}).get('meat_plate_mm', '')} mm")
markdown.append(f"- Obrada masnoće: {payload.get('grinding', {}).get('fat_handling', '')}")
markdown.append(f"- Temperaturna kontrola: {payload.get('grinding', {}).get('temperature_control', '')}")
markdown.append("")
markdown.append("## Crijeva")
markdown.append("")
casing = payload.get("casing", {})
markdown.append(f"- Tip: {casing.get('type', '')}")
markdown.append(f"- Promjer: {casing.get('diameter_mm', '')} mm")
markdown.append(f"- Namakanje: {casing.get('soaking_time', '')}, {casing.get('soaking_liquid', '')}, {casing.get('soaking_temperature_c', '')} °C")
markdown.append(f"- Prokuhavanje: {casing.get('boiled_or_cold_liquid', '')}")
markdown.append("")
markdown.append("## Gotovo je kad")
markdown.append("")
for item in done_when:
    markdown.append(f"- {item}")
markdown.append("")
markdown.append("## Greške i rješenja")
markdown.append("")
for e in errors:
    markdown.append(f"- Problem: {e.get('problem', '')}")
    markdown.append(f"  - Uzrok: {e.get('cause', '')}")
    markdown.append(f"  - Rješenje: {e.get('solution', '')}")
markdown.append("")
markdown.append("## Aktivne blokade")
markdown.append("")
for b in blockers:
    markdown.append(f"- {b}")

full_markdown = "\n".join(markdown) + "\n"

meta_map = {
    "_dry_recipe_preview_mode": "PRIVATE_CLONE_ONLY",
    "_dry_recipe_preview_source_post_id": str(source_post_id),
    "_dry_recipe_public_update_allowed": "0",
    "_dry_recipe_dossier_status": "CANON_DRAFT_V1_NOT_PUBLIC",
    "_dry_recipe_public_verified": "0",
    "_dry_recipe_source_validation_status": "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED",
    "_dry_recipe_type_router": "GROUND_MEAT_OR_CASING",
    "_dry_recipe_adapter_payload_version": "3042_private_preview_adapter_dryrun_v1",
    "_dry_recipe_dossier_path": str(dossier_dir),
    "_dry_recipe_active_blockers": json.dumps(blockers, ensure_ascii=False),
    "_dry_recipe_sections": json.dumps(sections, ensure_ascii=False),
    "_dry_verified_process": json.dumps(verified_process, ensure_ascii=False),
    "_dry_recipe_full_markdown": full_markdown,
}

plan = {
    "plan_status": "DRY_RUN_ONLY_NO_WORDPRESS_WRITE",
    "generated_at": now,
    "source_public_post": {
        "post_id": source_post_id,
        "title": title,
        "write_allowed": False,
        "allowed_action": "READ_ONLY_REFERENCE"
    },
    "future_private_clone": {
        "create_allowed_in_future_step": True,
        "required_post_type": "dry_recipe",
        "required_post_status": "private",
        "title": f"PRIVATE PREVIEW — {title}",
        "slug_policy": "private-preview-3042-jesus-de-lyon-dossier-only",
        "public_indexing_allowed": False,
        "source_post_update_allowed": False
    },
    "meta_write_scope_for_future_step": {
        "allowed_target": "FUTURE_PRIVATE_CLONE_ONLY",
        "forbidden_target": "PUBLIC_POST_3042",
        "source_post_meta_write_allowed": False,
        "clone_meta_write_allowed_after_explicit_approval": True
    },
    "would_set_meta_on_future_private_clone_only": meta_map,
    "would_not_set_on_public_post_3042": list(meta_map.keys()),
    "safety_guards": [
        "Ne smije se pisati u javni post 3042.",
        "Ne smije se mijenjati javni title, slug, status ni URL.",
        "Ne smije se mijenjati renderer.",
        "Meta vrijednosti iz ovog plana smiju se koristiti samo na budućem privatnom cloneu.",
        "Privatni clone mora imati post_status=private.",
        "Javni update ostaje zabranjen dok se ne zatvore izvor količina, starter kultura, dimljenje i interni javni tragovi."
    ]
}

plan_json = review_dir / "3042_private_wp_preview_plan_v1.json"
meta_json = review_dir / "3042_private_wp_preview_meta_map.json"
markdown_path = review_dir / "3042_private_wp_preview_full_markdown.md"
plan_json.write_text(json.dumps(plan, ensure_ascii=False, indent=2), encoding="utf-8")
meta_json.write_text(json.dumps(meta_map, ensure_ascii=False, indent=2), encoding="utf-8")
markdown_path.write_text(full_markdown, encoding="utf-8")

csv_path = review_dir / "3042_private_wp_preview_meta_map.csv"
with csv_path.open("w", encoding="utf-8", newline="") as f:
    w = csv.writer(f)
    w.writerow(["meta_key", "target", "write_allowed_now", "future_write_scope", "value_preview"])
    for k, v in meta_map.items():
        preview = v if isinstance(v, str) else json.dumps(v, ensure_ascii=False)
        if len(preview) > 180:
            preview = preview[:180] + "..."
        w.writerow([k, "FUTURE_PRIVATE_CLONE_ONLY", "false", "after_explicit_approval_only", preview])

html_path = review_dir / "3042_PRIVATE_WP_PREVIEW_PLAN.html"
rows = []
for k, v in meta_map.items():
    pv = v if isinstance(v, str) else json.dumps(v, ensure_ascii=False)
    if len(pv) > 320:
        pv = pv[:320] + "..."
    rows.append(f"<tr><td>{escape(k)}</td><td>FUTURE_PRIVATE_CLONE_ONLY</td><td>false</td><td><code>{escape(pv)}</code></td></tr>")

html = f"""<!doctype html>
<html lang="hr">
<head>
<meta charset="utf-8">
<title>3042 Private WP Preview Plan</title>
<style>
body {{ font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin: 32px; background:#f7f2ea; color:#1f2933; }}
main {{ max-width: 1200px; margin:auto; background:#fffaf2; border-radius:18px; padding:28px; }}
h1,h2 {{ color:#4a2414; }}
.warn {{ background:#fde2e2; padding:14px 16px; border-radius:12px; margin:14px 0; }}
.ok {{ background:#e7f6ec; padding:14px 16px; border-radius:12px; margin:14px 0; }}
table {{ width:100%; border-collapse:collapse; }}
th,td {{ border-bottom:1px solid #eadfce; padding:8px; vertical-align:top; text-align:left; }}
code {{ white-space:pre-wrap; }}
</style>
</head>
<body>
<main>
<h1>3042 Private WordPress Preview Plan</h1>
<div class="warn"><strong>DRY-RUN ONLY.</strong> Ne upisuje se u WordPress. Javni post 3042 ne smije se mijenjati.</div>
<div class="ok">Meta mapa je dopuštena samo za budući privatni clone nakon posebnog odobrenja.</div>
<h2>Meta mapa za budući privatni clone</h2>
<table>
<thead><tr><th>Meta key</th><th>Target</th><th>Write now</th><th>Value preview</th></tr></thead>
<tbody>
{''.join(rows)}
</tbody>
</table>
</main>
</body>
</html>
"""
html_path.write_text(html, encoding="utf-8")

checklist = []
checklist.append("# 3042 private WordPress preview — execution checklist")
checklist.append("")
checklist.append("Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**")
checklist.append("")
checklist.append("## Zabranjeno")
checklist.append("")
checklist.append("- [x] Ne mijenjati javni post 3042.")
checklist.append("- [x] Ne mijenjati javni title.")
checklist.append("- [x] Ne mijenjati javni slug.")
checklist.append("- [x] Ne mijenjati javni status.")
checklist.append("- [x] Ne mijenjati javni URL.")
checklist.append("- [x] Ne mijenjati renderer.")
checklist.append("")
checklist.append("## Dopušteno tek u idućem koraku uz posebno odobrenje")
checklist.append("")
checklist.append("- [ ] Stvoriti privatni clone s post_status=private.")
checklist.append("- [ ] Meta vrijednosti iz `3042_private_wp_preview_meta_map.json` upisati samo na privatni clone.")
checklist.append("- [ ] Provjeriti privatni URL/preview.")
checklist.append("- [ ] Ako preview nije dobar, obrisati ili arhivirati privatni clone, ne dirati javni post.")
checklist.append("")
checklist.append("## Blokade prije javne objave")
checklist.append("")
for b in blockers:
    checklist.append(f"- [ ] {b}")
checklist_path = review_dir / "3042_PRIVATE_WP_PREVIEW_EXECUTION_CHECKLIST.md"
checklist_path.write_text("\n".join(checklist) + "\n", encoding="utf-8")

report = []
report.append("# 3042 Jésus de Lyon — private WordPress preview plan dry-run v1")
report.append("")
report.append("Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**")
report.append("")
report.append("Ovaj korak ne mijenja WordPress. Izrađuje samo plan za budući privatni clone.")
report.append("")
report.append("## Sažetak")
report.append("")
report.append("- Source public post: `3042`")
report.append("- Source post write allowed: `false`")
report.append("- Future target: `PRIVATE_CLONE_ONLY`")
report.append("- Meta keys planned: `{}`".format(len(meta_map)))
report.append("- Public update allowed: `false`")
report.append("")
report.append("## Meta keys")
report.append("")
for k in meta_map.keys():
    report.append(f"- `{k}` → samo budući privatni clone")
report.append("")
report.append("## Sigurnosna pravila")
report.append("")
for item in plan["safety_guards"]:
    report.append(f"- {item}")
report.append("")
report.append("## Izlazne datoteke")
report.append("")
report.append("- `3042_private_wp_preview_plan_v1.json`")
report.append("- `3042_private_wp_preview_meta_map.json`")
report.append("- `3042_private_wp_preview_meta_map.csv`")
report.append("- `3042_private_wp_preview_full_markdown.md`")
report.append("- `3042_PRIVATE_WP_PREVIEW_PLAN.html`")
report.append("- `3042_PRIVATE_WP_PREVIEW_EXECUTION_CHECKLIST.md`")
report.append("")
report.append("## Zaključak")
report.append("")
report.append("Plan je spreman za pregled. Sljedeći korak, ako se odobri, smije napraviti samo privatni clone i upisivati samo u taj privatni clone.")
report_path = review_dir / "3042_PRIVATE_WP_PREVIEW_PLAN_REPORT.md"
report_path.write_text("\n".join(report) + "\n", encoding="utf-8")

marker = "<!-- DC_3042_PRIVATE_WP_PREVIEW_PLAN_V1 -->"
append = f"""
{marker}

## Private WordPress preview plan dry-run v1

Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**

- Source public post: `3042`
- Source post write allowed: `false`
- Future target: `PRIVATE_CLONE_ONLY`
- Planned meta keys: `{len(meta_map)}`
- Public update allowed: `false`

Report: `review/{review_dir.name}/3042_PRIVATE_WP_PREVIEW_PLAN_REPORT.md`
Meta map: `review/{review_dir.name}/3042_private_wp_preview_meta_map.json`
Checklist: `review/{review_dir.name}/3042_PRIVATE_WP_PREVIEW_EXECUTION_CHECKLIST.md`
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3042 PRIVATE WP PREVIEW PLAN COMPLETE ===")
print("PLAN_STATUS=PLAN_ONLY_NO_WORDPRESS_WRITE")
print("SOURCE_POST_ID=3042")
print("SOURCE_POST_WRITE_ALLOWED=false")
print("FUTURE_TARGET=PRIVATE_CLONE_ONLY")
print(f"PLANNED_META_KEYS={len(meta_map)}")
print("PUBLIC_UPDATE_ALLOWED=false")
print(f"PLAN={plan_json}")
print(f"META_MAP={meta_json}")
print(f"REPORT={report_path}")
