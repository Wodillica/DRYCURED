#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_2697_generate_preview_payload_v1.py DOSSIER_DIR RECIPE_YML INTERNAL_QA_JSON REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
recipe_yml = Path(sys.argv[2])
internal_qa_json = Path(sys.argv[3])
review_dir = Path(sys.argv[4])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"

recipe = json.loads(recipe_yml.read_text(encoding="utf-8"))
internal_qa = json.loads(internal_qa_json.read_text(encoding="utf-8"))
now = datetime.now(timezone.utc).isoformat()

if internal_qa.get("qa_status") != "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS":
    raise SystemExit("FAIL: internal QA nije PASS_READY_FOR_SECTIONS.")

if internal_qa.get("ready_for_sections") is not True:
    raise SystemExit("FAIL: ready_for_sections nije true.")

def md_table(headers, rows):
    out = []
    out.append("| " + " | ".join(headers) + " |")
    out.append("|" + "|".join(["---" for _ in headers]) + "|")
    for row in rows:
        out.append("| " + " | ".join(str(x).replace("|", "/") for x in row) + " |")
    return "\n".join(out)

title = recipe["title_hr"]
recipe_code = recipe["recipe_code"]

raw_rows = []
for item in recipe["raw_materials"]["working_formula_10kg"]:
    raw_rows.append([item["name"], item["kg"], item.get("source_status", "")])

ing_rows = []
for item in recipe["ingredients_10kg"]["working_formula"]:
    ing_rows.append([item["name"], item["chosen_g"], item["g_per_kg"], item.get("source_relation", "")])

process_rows = []
for step in recipe["process"]:
    process_rows.append([
        step["step"],
        step["name"],
        step["parameters"],
        step["action"],
        step["critical_control"]
    ])

problem_rows = []
for item in recipe["problems_and_solutions"]:
    problem_rows.append([
        item["problem"],
        item["likely_cause"],
        item["solution"]
    ])

sections = [
    {
        "id": "hero",
        "title": title,
        "type": "hero",
        "content": {
            "recipe_code": recipe_code,
            "country": recipe["country"],
            "region": recipe["region"],
            "recipe_type": recipe["recipe_type_router"],
            "batch_size_kg": recipe["batch_size_kg"],
            "public_update_allowed": False,
            "private_preview_notice": "Privatni radni preview. Javni update nije dopušten."
        }
    },
    {
        "id": "production_summary",
        "title": "Brzi proizvodni sažetak",
        "type": "summary_cards",
        "content": {
            "batch": "10 kg ukupne mesne smjese",
            "raw_materials": "9,09 kg svinjskog mesa + 0,91 kg tvrde slanine",
            "grinding": "rešetka 6 mm; smjesa najviše 8 °C",
            "casing": "tanka svinjska crijeva, komadi 35–40 cm",
            "smoking": "3–4 dima po oko 6 h tijekom tjedan dana",
            "maturation": "25–30 dana",
            "nitrite": "bazni draft bez nitritne soli"
        }
    },
    {
        "id": "process_timeline",
        "title": "Procesna kronologija",
        "type": "process_timeline",
        "content": recipe["process"]
    },
    {
        "id": "raw_materials",
        "title": "Sirovine za 10 kg",
        "type": "ingredients_table",
        "content": {
            "batch_basis": recipe["batch_basis"],
            "items": recipe["raw_materials"]["working_formula_10kg"],
            "raw_material_total_kg": recipe["raw_materials"]["raw_material_total_kg"],
            "fat_handling_summary": recipe["raw_materials"]["fat_handling_summary"]
        }
    },
    {
        "id": "spices",
        "title": "Začini i dodatci za 10 kg",
        "type": "spices_table",
        "content": {
            "items": recipe["ingredients_10kg"]["working_formula"],
            "total_paprika_g": recipe["ingredients_10kg"]["total_paprika_g"],
            "paprika_percent": recipe["ingredients_10kg"]["paprika_percent"],
            "salt_percent": recipe["ingredients_10kg"]["salt_percent"],
            "garlic_percent": recipe["ingredients_10kg"]["garlic_percent"],
            "nitrite_nitrate_policy": recipe["ingredients_10kg"]["nitrite_nitrate_policy"]
        }
    },
    {
        "id": "grinding",
        "title": "Mljevenje i obrada tvrde slanine",
        "type": "technical_box",
        "content": recipe["grinding_and_fat_handling"]
    },
    {
        "id": "garlic_liquids",
        "title": "Češnjak i tekućine",
        "type": "technical_box",
        "content": recipe["garlic_policy"]
    },
    {
        "id": "casing",
        "title": "Crijeva, namakanje i punjenje",
        "type": "technical_box",
        "content": recipe["casing_and_filling"]
    },
    {
        "id": "identity",
        "title": "Regionalni identitet",
        "type": "text",
        "content": recipe["identity"]
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
        "type": "problem_solution_table",
        "content": recipe["problems_and_solutions"]
    },
    {
        "id": "serving_storage",
        "title": "Posluživanje i čuvanje",
        "type": "text",
        "content": recipe["serving_and_storage"]
    },
    {
        "id": "editorial_status",
        "title": "Urednički status",
        "type": "private_status",
        "content": {
            "recipe_status": recipe["recipe_status"],
            "public_update_allowed": False,
            "public_publish_allowed": False,
            "source_post_write_allowed": False,
            "active_blockers": recipe["active_blockers"],
            "title_review_required": recipe["title_review_required"]
        }
    }
]

verified_process = {
    "schema_version": "drycured_verified_process_v1",
    "generated_at": now,
    "post_id": 2697,
    "recipe_code": recipe_code,
    "recipe_type_router": "GROUND_MEAT_OR_CASING",
    "public_update_allowed": False,
    "source_post_write_allowed": False,
    "phases": recipe["process"],
    "critical_controls": [
        "Sirovina i smjesa moraju ostati hladne.",
        "Mljevenje se radi na rešetku 6 mm.",
        "Mast mora ostati nerazmazana.",
        "Crijeva se namaču 30–45 min u pitkoj vodi 20–25 °C i ne prokuhavaju.",
        "Dimljenje ide tankim hladnim dimom, bez čađavog ili vrućeg dima.",
        "Zrenje traje 25–30 dana u hladnom i prozračnom prostoru.",
        "Sumnjiv proizvod se ne kuša i ne poslužuje."
    ],
    "done_when": recipe["done_when"],
    "problems_and_solutions": recipe["problems_and_solutions"]
}

full_md = []
full_md.append(f"# {title}")
full_md.append("")
full_md.append("**Privatni radni preview. Javni update nije dopušten.**")
full_md.append("")
full_md.append("## Brzi proizvodni sažetak")
full_md.append("")
full_md.append("- Šarža: **10 kg ukupne mesne smjese**")
full_md.append("- Sirovine: **9,09 kg svinjskog mesa** i **0,91 kg tvrde slanine**")
full_md.append("- Mljevenje: **rešetka 6 mm**")
full_md.append("- Češnjak: **28 g pastoznog češnjaka**, bez tekućine od češnjaka")
full_md.append("- Crijeva: **tanka svinjska crijeva**, komadi **35–40 cm**")
full_md.append("- Dimljenje: **3–4 dima po oko 6 h tijekom tjedan dana**")
full_md.append("- Zrenje: **25–30 dana**")
full_md.append("")
full_md.append("## Sirovine za 10 kg")
full_md.append("")
full_md.append(md_table(["Sirovina", "kg", "Napomena"], raw_rows))
full_md.append("")
full_md.append("## Začini za 10 kg")
full_md.append("")
full_md.append(md_table(["Sastojak", "g", "g/kg", "Napomena"], ing_rows))
full_md.append("")
full_md.append("## Mljevenje i obrada tvrde slanine")
full_md.append("")
g = recipe["grinding_and_fat_handling"]
full_md.append(f"Meso i slanina režu se na komade **{g['pre_cut_size_mm']} mm** i melju na rešetku **{g['chosen_plate_mm']} mm**. Meso treba biti na **{g['meat_temperature_c']} °C**, a smjesa tijekom rada ne smije prijeći **{g['mix_temperature_max_c']} °C**.")
full_md.append("")
full_md.append(g["fat_handling"])
full_md.append("")
full_md.append(f"Cilj teksture: {g['texture_goal']}.")
full_md.append("")
full_md.append("## Češnjak i tekućine")
full_md.append("")
garlic = recipe["garlic_policy"]
full_md.append(f"Koristi se **{garlic['chosen_g_per_10kg']} g pastoznog češnjaka** na 10 kg smjese. Češnjak se dodaje izravno u smjesu.")
full_md.append("")
full_md.append("Tekućina od češnjaka se u ovom receptu **ne koristi**. Nema namakanja, nema prokuhavanja i nema hlađenja češnjakove tekućine.")
full_md.append("")
full_md.append("## Crijeva, namakanje i punjenje")
full_md.append("")
casing = recipe["casing_and_filling"]
soak = casing["soaking"]
full_md.append(f"Koriste se **{casing['casing_type']}**. Radna smjernica kalibra: {casing['working_caliber_guidance']}. Komadi se oblikuju na **{casing['source_length_guidance_cm']} cm**.")
full_md.append("")
full_md.append(f"Crijeva se namaču u **{soak['liquid']}** temperature **{soak['temperature_c']} °C** kroz **{soak['time_minutes']} minuta**. Crijeva se **ne prokuhavaju**. Prije punjenja treba ih {soak['rinsing']}.")
full_md.append("")
full_md.append(casing["filling"])
full_md.append("")
full_md.append("## Procesna kronologija")
full_md.append("")
full_md.append(md_table(["Korak", "Faza", "Parametri", "Radnja", "Kritična kontrola"], process_rows))
full_md.append("")
full_md.append("## Gotovo je kad…")
full_md.append("")
for item in recipe["done_when"]:
    full_md.append(f"- {item}")
full_md.append("")
full_md.append("## Najčešće greške i konkretna rješenja")
full_md.append("")
full_md.append(md_table(["Problem", "Vjerojatan uzrok", "Rješenje"], problem_rows))
full_md.append("")
full_md.append("## Posluživanje i čuvanje")
full_md.append("")
full_md.append(recipe["serving_and_storage"]["serving"])
full_md.append("")
full_md.append(recipe["serving_and_storage"]["storage"])
full_md.append("")
full_md.append(recipe["serving_and_storage"]["public_note"])
full_md.append("")
full_md.append("## Urednički status")
full_md.append("")
full_md.append("Ovaj zapis je privatni preview. Javni update nije dopušten dok se ne potvrdi vizualni prikaz, naslov i javni objavni postupak.")
full_md.append("")
for blocker in recipe["active_blockers"]:
    full_md.append(f"- {blocker}")
full_md.append("")

full_markdown_text = "\n".join(full_md)

payload = {
    "schema_version": "drycured_private_preview_payload_v1",
    "generated_at": now,
    "post_id": 2697,
    "recipe_code": recipe_code,
    "title_hr": title,
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "private_preview_allowed": True,
    "sections_count": len(sections),
    "verified_process_phases": len(verified_process["phases"]),
    "full_markdown_length": len(full_markdown_text),
    "meta_to_apply_to_private_clone_only": {
        "_dry_recipe_preview_mode": "PRIVATE_CLONE_ONLY",
        "_dry_recipe_preview_source_post_id": "2697",
        "_dry_recipe_public_update_allowed": "0",
        "_dry_recipe_public_verified": "0",
        "_dry_recipe_id": recipe_code,
        "_dry_recipe_sections": json.dumps(sections, ensure_ascii=False, indent=2),
        "_dry_verified_process": json.dumps(verified_process, ensure_ascii=False, indent=2),
        "_dry_recipe_full_markdown": full_markdown_text
    }
}

sections_path = review_dir / "2697_dry_recipe_sections.json"
process_path = review_dir / "2697_dry_verified_process.json"
full_md_path = review_dir / "2697_dry_recipe_full_markdown.md"
payload_path = review_dir / "2697_private_preview_payload_v1.json"
checks_path = review_dir / "2697_preview_payload_checks.csv"
report_path = review_dir / "2697_PREVIEW_PAYLOAD_REPORT.md"

sections_path.write_text(json.dumps(sections, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
process_path.write_text(json.dumps(verified_process, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")
full_md_path.write_text(full_markdown_text, encoding="utf-8")
payload_path.write_text(json.dumps(payload, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks = []

def check(key, ok, severity, note):
    checks.append({
        "key": key,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

check("sections_count", len(sections) >= 13, "BLOCKER", f"Broj sekcija: {len(sections)}.")
check("has_process_section", any(s["id"] == "process_timeline" for s in sections), "BLOCKER", "Mora postojati procesna sekcija.")
check("has_raw_materials_section", any(s["id"] == "raw_materials" for s in sections), "MAJOR", "Mora postojati sekcija sirovina.")
check("has_spices_section", any(s["id"] == "spices" for s in sections), "MAJOR", "Mora postojati sekcija začina.")
check("has_grinding_section", any(s["id"] == "grinding" for s in sections), "MAJOR", "Mora postojati sekcija mljevenja.")
check("has_casing_section", any(s["id"] == "casing" for s in sections), "MAJOR", "Mora postojati sekcija crijeva.")
check("has_garlic_liquids_section", any(s["id"] == "garlic_liquids" for s in sections), "MAJOR", "Mora postojati sekcija češnjaka i tekućina.")
check("has_problem_solutions", any(s["id"] == "problems_solutions" for s in sections), "MAJOR", "Problemi i rješenja moraju biti prisutni.")
check("verified_process_phases", len(verified_process["phases"]) >= 10, "BLOCKER", f"Verified process ima {len(verified_process['phases'])} faza.")
check("full_markdown_length", len(full_markdown_text) > 6500, "MAJOR", f"Full markdown duljina: {len(full_markdown_text)}.")
check("full_markdown_has_6mm", "6 mm" in full_markdown_text, "BLOCKER", "Full markdown mora sadržavati 6 mm.")
check("full_markdown_has_casing_soak", "30-45" in full_markdown_text and "20-25" in full_markdown_text, "MAJOR", "Full markdown mora sadržavati namakanje crijeva.")
check("full_markdown_has_smoking", "3–4 dima" in full_markdown_text or "3-4 dima" in full_markdown_text, "BLOCKER", "Full markdown mora sadržavati cikluse dimljenja.")
check("public_update_false", payload["public_update_allowed"] is False, "BLOCKER", "Javni update mora ostati false.")
check("source_post_write_false", payload["source_post_write_allowed"] is False, "BLOCKER", "Source post write mora ostati false.")

major_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in major_failures if c["severity"] == "BLOCKER"]

payload_status = "PREVIEW_PAYLOAD_READY_FOR_PRIVATE_CLONE" if not major_failures else "PREVIEW_PAYLOAD_BLOCKED"

with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

md = []
md.append("# 2697 Baranjska kobasica – ljuta varijanta preview payload v1")
md.append("")
md.append(f"Status: **{payload_status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Generira strukturirane payload datoteke za budući privatni clone.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `2697`")
md.append(f"- Recipe code: `{recipe_code}`")
md.append("- Public update allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append(f"- Sections count: `{len(sections)}`")
md.append(f"- Verified process phases: `{len(verified_process['phases'])}`")
md.append(f"- Full markdown length: `{len(full_markdown_text)}`")
md.append(f"- Blocker fail total: `{len(blocker_failures)}`")
md.append("")
md.append("## Output datoteke")
md.append("")
md.append("- `2697_dry_recipe_sections.json`")
md.append("- `2697_dry_verified_process.json`")
md.append("- `2697_dry_recipe_full_markdown.md`")
md.append("- `2697_private_preview_payload_v1.json`")
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
if payload_status == "PREVIEW_PAYLOAD_READY_FOR_PRIVATE_CLONE":
    md.append("Payload je spreman za izradu privatnog clonea za `2697`, uz DB backup izvan Git repozitorija i bez promjene javnog source posta.")
else:
    md.append("Payload nije spreman. Potrebno je napraviti repair prije privatnog clonea.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def append_once(path: Path, marker: str, block: str):
    old = path.read_text(encoding="utf-8")
    if marker not in old:
        path.write_text(old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

qa_block = f"""
<!-- DC_2697_PREVIEW_PAYLOAD_V1 -->

## 2697 Baranjska kobasica – ljuta varijanta preview payload v1

Status: **{payload_status}**

- Recipe code: `{recipe_code}`
- Public update allowed: `false`
- Source post write allowed: `false`
- Sections count: `{len(sections)}`
- Verified process phases: `{len(verified_process['phases'])}`
- Full markdown length: `{len(full_markdown_text)}`
- Blocker fail total: `{len(blocker_failures)}`
- Report: `review/{review_dir.name}/2697_PREVIEW_PAYLOAD_REPORT.md`
- Payload: `review/{review_dir.name}/2697_private_preview_payload_v1.json`
"""
append_once(qa_path, "<!-- DC_2697_PREVIEW_PAYLOAD_V1 -->", qa_block)

readme_block = f"""
<!-- DC_2697_PREVIEW_PAYLOAD_V1 -->

## 2697 preview payload v1

Status: **{payload_status}**

Generirani su `_dry_recipe_sections`, `_dry_verified_process` i `_dry_recipe_full_markdown` za budući privatni clone. Javni update ostaje zabranjen.
"""
append_once(readme_path, "<!-- DC_2697_PREVIEW_PAYLOAD_V1 -->", readme_block)

print("=== 2697 PREVIEW PAYLOAD COMPLETE ===")
print(f"PAYLOAD_STATUS={payload_status}")
print("POST_ID=2697")
print(f"RECIPE_CODE={recipe_code}")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print(f"SECTIONS_COUNT={len(sections)}")
print(f"VERIFIED_PROCESS_PHASES={len(verified_process['phases'])}")
print(f"FULL_MARKDOWN_LENGTH={len(full_markdown_text)}")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"REPORT={report_path}")
print(f"PAYLOAD={payload_path}")
