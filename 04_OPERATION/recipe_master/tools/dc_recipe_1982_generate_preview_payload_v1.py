#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_1982_generate_preview_payload_v1.py DOSSIER_DIR DRAFT_JSON INTERNAL_QA_JSON REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
draft_json = Path(sys.argv[2])
internal_qa_json = Path(sys.argv[3])
review_dir = Path(sys.argv[4])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"

recipe = json.loads(draft_json.read_text(encoding="utf-8"))
internal_qa = json.loads(internal_qa_json.read_text(encoding="utf-8"))

now = datetime.now(timezone.utc).isoformat()

if internal_qa.get("qa_status") != "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS":
    raise SystemExit("FAIL: internal QA nije spreman za sections.")

if internal_qa.get("ready_for_sections") is not True:
    raise SystemExit("FAIL: ready_for_sections nije true.")

def md_table(rows, headers):
    out = []
    out.append("| " + " | ".join(headers) + " |")
    out.append("|" + "|".join(["---"] * len(headers)) + "|")
    for row in rows:
        out.append("| " + " | ".join(str(row.get(h, "")) for h in headers) + " |")
    return "\n".join(out)

title = recipe["title_hr"]
code = recipe["recipe_code"]
batch = recipe["batch_size_kg"]

raw_rows = []
for item in recipe["raw_materials"]["working_formula_10kg"]:
    raw_rows.append({
        "Sirovina": item["name"],
        "kg": item["kg"],
        "Status": item["source_status"]
    })

mandatory_rows = []
for item in recipe["ingredients_10kg"]["mandatory_with_chosen_values"]:
    mandatory_rows.append({
        "Sastojak": item["name"],
        "Količina": f"{item['chosen_g']} g",
        "g/kg": item["g_per_kg"],
        "Raspon": item["official_range_g_per_10kg"] + " g / 10 kg"
    })

optional_rows = [
    {"Sastojak": "crno ili toskansko vino", "Količina": "0,08 L", "Status": "opcionalno, ispod maksimuma 0,1 L / 10 kg"},
    {"Sastojak": "dekstroza ili saharoza", "Količina": "30 g", "Status": "opcionalno, ispod maksimuma 100 g / 10 kg"},
    {"Sastojak": "starter kulture", "Količina": "ne koristi se u baznom draftu", "Status": "dopušteno disciplinarom, ali traži tehnološku recenziju"},
    {"Sastojak": "nitriti/nitrati", "Količina": "ne koristi se u baznom draftu", "Status": "dopušteno disciplinarom, ali traži sigurnosnu recenziju ako se uključi"}
]

process_rows = []
for step in recipe["process"]:
    process_rows.append({
        "Korak": step["step"],
        "Faza": step["name"],
        "Parametri": step["parameters"],
        "Kontrola": step["critical_control"]
    })

problems_rows = []
for item in recipe["problems_and_solutions"]:
    problems_rows.append({
        "Problem": item["problem"],
        "Uzrok": item["likely_cause"],
        "Rješenje": item["solution"]
    })

sections = [
    {
        "id": "hero",
        "title": "Finocchiona Toscana IGP",
        "type": "hero",
        "content": {
            "country": recipe["country"],
            "region": recipe["region"],
            "protected_status": recipe["protected_status"],
            "batch_size_kg": batch,
            "recipe_type": recipe["recipe_type_router"],
            "public_update_allowed": False
        }
    },
    {
        "id": "quick_summary",
        "title": "Brzi proizvodni sažetak",
        "type": "summary_cards",
        "content": [
            "Toskanska suha fermentirana kobasica s izraženom aromom komorača.",
            "Šarža je standardizirana na 10 kg.",
            "Mljevenje: 6 mm, unutar službenog raspona 4,5–8 mm.",
            "Češnjak: suhi češnjak izravno, bez tekućine od češnjaka.",
            "Sušenje: 12–25 °C.",
            "Zrenje: 11–18 °C i 65–90 % RH."
        ]
    },
    {
        "id": "raw_materials",
        "title": "Glavne sirovine za 10 kg",
        "type": "table",
        "content": raw_rows
    },
    {
        "id": "mandatory_ingredients",
        "title": "Obvezni začini i službeni rasponi",
        "type": "table",
        "content": mandatory_rows
    },
    {
        "id": "optional_ingredients",
        "title": "Opcionalni dodaci i sigurnosne odluke",
        "type": "table",
        "content": optional_rows
    },
    {
        "id": "garlic_liquids",
        "title": "Češnjak i tekućine",
        "type": "text",
        "content": (
            "U ovoj radnoj verziji koristi se 8 g suhog češnjaka na 10 kg smjese. "
            "Ne koristi se svježi češnjak, ne koristi se macerat i ne dodaje se tekućina od češnjaka. "
            "Vino se dodaje odvojeno u količini 0,08 L na 10 kg, postupno u tankom mlazu tijekom miješanja."
        )
    },
    {
        "id": "grinding",
        "title": "Mljevenje, rezanje i obrada masnoće",
        "type": "text",
        "content": (
            "Sirovina se reže na komade 20–30 mm i melje na rešetku 6 mm. "
            "Službeni raspon mljevenja je 4,5–8 mm. Meso i čvršća masnoća moraju biti dobro ohlađeni; "
            "smjesa ne smije prijeći 8 °C. Mekana i maziva masnoća se uklanja. "
            "Ako se mast počne razmazivati, rad se prekida, sirovina i oprema se hlade, a mljevenje se nastavlja tek nakon stabilizacije."
        )
    },
    {
        "id": "casing",
        "title": "Crijeva, namakanje i punjenje",
        "type": "text",
        "content": (
            "Koristi se prirodni ili collato ovitak. Za kućni radni preview koristi se veći prirodni ovitak, "
            "ovisno o željenoj težini komada. Ovitak se namače 30–45 minuta u pitkoj vodi temperature 20–25 °C, "
            "ne prokuhava se i ispire se izvana i iznutra prije punjenja. Puniti čvrsto, ali bez pucanja ovitka; "
            "zračne džepove istisnuti i po potrebi probosti sterilnom iglom."
        )
    },
    {
        "id": "process",
        "title": "Procesna kronologija",
        "type": "process",
        "content": process_rows
    },
    {
        "id": "done_when",
        "title": "Gotovo je kad…",
        "type": "checklist",
        "content": recipe["done_when"]
    },
    {
        "id": "problems_solutions",
        "title": "Najčešće greške i konkretna rješenja",
        "type": "table",
        "content": problems_rows
    },
    {
        "id": "serving_storage",
        "title": "Posluživanje i čuvanje",
        "type": "text",
        "content": (
            recipe["serving_and_storage"]["serving"] + " " +
            recipe["serving_and_storage"]["storage"] + " " +
            recipe["serving_and_storage"]["public_note"]
        )
    },
    {
        "id": "internal_status",
        "title": "Interni status prije javnog updatea",
        "type": "internal",
        "content": {
            "public_update_allowed": False,
            "public_publish_allowed": False,
            "active_blockers": recipe["active_blockers"]
        }
    }
]

verified_process = {
    "schema_version": "drycured_verified_process_v1",
    "generated_at": now,
    "post_id": 1982,
    "recipe_code": code,
    "recipe_type_router": recipe["recipe_type_router"],
    "public_update_allowed": False,
    "source_post_write_allowed": False,
    "phases": recipe["process"],
    "critical_controls": [
        "Sirovina mora biti svježa i nesmrznuta.",
        "Masa tijekom mljevenja i miješanja ne smije prijeći 8 °C.",
        "Mljevenje mora ostati unutar 4,5–8 mm; radni izbor je 6 mm.",
        "Ovitak se namače u pitkoj vodi 20–25 °C tijekom 30–45 minuta i ne prokuhava se.",
        "Sušenje mora ostati u rasponu 12–25 °C.",
        "Zrenje mora ostati u rasponu 11–18 °C i 65–90 % RH.",
        "Ako se pojave neugodan miris, sluzavost, napuhavanje ili sumnjiva plijesan, proizvod se ne kuša i ne poslužuje."
    ],
    "done_when": recipe["done_when"],
    "problems_and_solutions": recipe["problems_and_solutions"]
}

full_md = []
full_md.append(f"# {title}")
full_md.append("")
full_md.append("**Status:** privatni radni preview; javni update nije dopušten.")
full_md.append("")
full_md.append("## Brzi proizvodni sažetak")
full_md.append("")
full_md.append(f"Finocchiona Toscana IGP je toskanska suha fermentirana kobasica u ovitku, standardizirana ovdje na {batch:g} kg. Glavni aromatski potpis čine komorač, papar i blaga nota češnjaka. Bazni draft ne koristi nitrite/nitrate ni starter kulture.")
full_md.append("")
full_md.append("## Glavne sirovine za 10 kg")
full_md.append("")
full_md.append(md_table(raw_rows, ["Sirovina", "kg", "Status"]))
full_md.append("")
full_md.append("## Obvezni začini")
full_md.append("")
full_md.append(md_table(mandatory_rows, ["Sastojak", "Količina", "g/kg", "Raspon"]))
full_md.append("")
full_md.append("## Opcionalni dodaci")
full_md.append("")
full_md.append(md_table(optional_rows, ["Sastojak", "Količina", "Status"]))
full_md.append("")
full_md.append("## Češnjak i tekućine")
full_md.append("")
full_md.append(sections[5]["content"])
full_md.append("")
full_md.append("## Mljevenje i obrada masnoće")
full_md.append("")
full_md.append(sections[6]["content"])
full_md.append("")
full_md.append("## Crijeva i punjenje")
full_md.append("")
full_md.append(sections[7]["content"])
full_md.append("")
full_md.append("## Procesna kronologija")
full_md.append("")
for step in recipe["process"]:
    full_md.append(f"### {step['step']}. {step['name']}")
    full_md.append("")
    full_md.append(f"- Parametri: {step['parameters']}")
    full_md.append(f"- Radnja: {step['action']}")
    full_md.append(f"- Kritična kontrola: {step['critical_control']}")
    full_md.append("")
full_md.append("## Gotovo je kad…")
full_md.append("")
for item in recipe["done_when"]:
    full_md.append(f"- {item}")
full_md.append("")
full_md.append("## Greške i rješenja")
full_md.append("")
for item in recipe["problems_and_solutions"]:
    full_md.append(f"### {item['problem']}")
    full_md.append("")
    full_md.append(f"- Uzrok: {item['likely_cause']}")
    full_md.append(f"- Rješenje: {item['solution']}")
    full_md.append("")
full_md.append("## Posluživanje i čuvanje")
full_md.append("")
full_md.append(sections[11]["content"])
full_md.append("")
full_md.append("## Interno prije javnog updatea")
full_md.append("")
for item in recipe["active_blockers"]:
    full_md.append(f"- {item}")
full_md.append("")

full_markdown = "\n".join(full_md)

payload = {
    "schema_version": "drycured_private_preview_payload_v1",
    "generated_at": now,
    "post_id": 1982,
    "recipe_code": code,
    "title": title,
    "public_update_allowed": False,
    "source_post_write_allowed": False,
    "meta_to_apply_to_private_clone_only": {
        "_dry_recipe_preview_mode": "PRIVATE_CLONE_ONLY",
        "_dry_recipe_preview_source_post_id": "1982",
        "_dry_recipe_public_update_allowed": "0",
        "_dry_recipe_public_verified": "0",
        "_dry_recipe_id": code,
        "_dry_recipe_sections": sections,
        "_dry_verified_process": verified_process,
        "_dry_recipe_full_markdown": full_markdown
    }
}

checks = []

def add_check(key, ok, severity, note):
    checks.append({
        "key": key,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

add_check("sections_count", len(sections) >= 10, "BLOCKER", f"Broj sekcija: {len(sections)}.")
add_check("has_process_section", any(s["id"] == "process" for s in sections), "BLOCKER", "Mora postojati procesna sekcija.")
add_check("has_grinding_section", any(s["id"] == "grinding" for s in sections), "MAJOR", "Mora postojati sekcija mljevenja.")
add_check("has_casing_section", any(s["id"] == "casing" for s in sections), "MAJOR", "Mora postojati sekcija crijeva.")
add_check("has_garlic_liquids_section", any(s["id"] == "garlic_liquids" for s in sections), "MAJOR", "Mora postojati sekcija češnjaka i tekućina.")
add_check("has_problem_solutions", len(recipe["problems_and_solutions"]) >= 5, "MAJOR", "Problemi i rješenja moraju biti prisutni.")
add_check("verified_process_phases", len(verified_process["phases"]) >= 8, "BLOCKER", "Verified process mora imati sve faze.")
add_check("full_markdown_length", len(full_markdown) > 4000, "MAJOR", f"Full markdown duljina: {len(full_markdown)}.")
add_check("public_update_false", payload["public_update_allowed"] is False, "BLOCKER", "Javni update mora ostati false.")
add_check("source_post_write_false", payload["source_post_write_allowed"] is False, "BLOCKER", "Source post write mora ostati false.")

failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in failures if c["severity"] == "BLOCKER"]

status = "PREVIEW_PAYLOAD_READY_FOR_PRIVATE_CLONE" if not blocker_failures and not failures else "PREVIEW_PAYLOAD_BLOCKED"

sections_path = review_dir / "1982_dry_recipe_sections.json"
verified_path = review_dir / "1982_dry_verified_process.json"
full_md_path = review_dir / "1982_dry_recipe_full_markdown.md"
payload_path = review_dir / "1982_private_preview_payload_v1.json"
checks_path = review_dir / "1982_preview_payload_checks.csv"
report_path = review_dir / "1982_PREVIEW_PAYLOAD_REPORT.md"

sections_path.write_text(json.dumps(sections, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
verified_path.write_text(json.dumps(verified_process, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
full_md_path.write_text(full_markdown + "\n", encoding="utf-8")
payload_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

md = []
md.append("# 1982 Finocchiona Toscana preview payload v1")
md.append("")
md.append(f"Status: **{status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Generira strukturirane payload datoteke za budući privatni clone.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `1982`")
md.append(f"- Recipe code: `{code}`")
md.append("- Public update allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append(f"- Sections count: `{len(sections)}`")
md.append(f"- Verified process phases: `{len(verified_process['phases'])}`")
md.append(f"- Full markdown length: `{len(full_markdown)}`")
md.append(f"- Blocker fail total: `{len(blocker_failures)}`")
md.append("")
md.append("## Output datoteke")
md.append("")
md.append(f"- `{sections_path.name}`")
md.append(f"- `{verified_path.name}`")
md.append(f"- `{full_md_path.name}`")
md.append(f"- `{payload_path.name}`")
md.append("")
md.append("## QA provjere")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    md.append(f"| {c['key']} | {c['status']} | {c['severity']} | {c['note']} |")
md.append("")
md.append("## Sljedeći korak")
md.append("")
if status == "PREVIEW_PAYLOAD_READY_FOR_PRIVATE_CLONE":
    md.append("Payload je spreman za planiranje i izradu privatnog clonea za `1982`, uz DB backup izvan Git repozitorija i bez promjene javnog source posta.")
else:
    md.append("Payload nije spreman. Potreban je repair prije privatnog clonea.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

qa_old = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_1982_PREVIEW_PAYLOAD_V1 -->"
block = f"""
{marker}

## 1982 Finocchiona Toscana preview payload v1

Status: **{status}**

- WordPress write allowed: `false`
- Public update allowed: `false`
- Source post write allowed: `false`
- Sections count: `{len(sections)}`
- Verified process phases: `{len(verified_process['phases'])}`
- Full markdown length: `{len(full_markdown)}`
- Blocker fail total: `{len(blocker_failures)}`
- Report: `review/{review_dir.name}/1982_PREVIEW_PAYLOAD_REPORT.md`
- Payload: `review/{review_dir.name}/1982_private_preview_payload_v1.json`
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

readme_old = readme_path.read_text(encoding="utf-8")
readme_marker = "<!-- DC_1982_PREVIEW_PAYLOAD_V1 -->"
readme_block = f"""
{readme_marker}

## 1982 preview payload v1

Status: **{status}**

Generirane su strukturirane payload datoteke `_dry_recipe_sections`, `_dry_verified_process` i `_dry_recipe_full_markdown` za budući privatni clone. WordPress nije mijenjan.
"""
if readme_marker not in readme_old:
    readme_path.write_text(readme_old.rstrip() + "\n\n" + readme_block.strip() + "\n", encoding="utf-8")

print("=== 1982 PREVIEW PAYLOAD COMPLETE ===")
print(f"PAYLOAD_STATUS={status}")
print("POST_ID=1982")
print(f"RECIPE_CODE={code}")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print(f"SECTIONS_COUNT={len(sections)}")
print(f"VERIFIED_PROCESS_PHASES={len(verified_process['phases'])}")
print(f"FULL_MARKDOWN_LENGTH={len(full_markdown)}")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"REPORT={report_path}")
print(f"PAYLOAD={payload_path}")
