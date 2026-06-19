#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_1982_recipe_yml_internal_qa_v1.py DOSSIER_DIR DRAFT_JSON REVIEW_DIR RECIPE_YML", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
draft_json = Path(sys.argv[2])
review_dir = Path(sys.argv[3])
recipe_yml_path = Path(sys.argv[4])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"

now = datetime.now(timezone.utc).isoformat()

recipe = json.loads(draft_json.read_text(encoding="utf-8"))
recipe_yml_text = recipe_yml_path.read_text(encoding="utf-8")
sources_text = sources_path.read_text(encoding="utf-8")

checks = []

def check(key, ok, severity, note):
    checks.append({
        "key": key,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

def get(path, default=None):
    cur = recipe
    for p in path:
        if isinstance(cur, dict) and p in cur:
            cur = cur[p]
        else:
            return default
    return cur

def contains_any(text, items):
    low = text.lower()
    return any(str(x).lower() in low for x in items)

# Osnovni statusi
check("schema_version", get(["schema_version"]) == "drycured_recipe_yml_v1", "BLOCKER", "Schema mora biti drycured_recipe_yml_v1.")
check("post_id", get(["post_id"]) == 1982, "BLOCKER", "Post ID mora biti 1982.")
check("recipe_code", get(["recipe_code"]) == "IT-TOS-1982-FINOCCHIONA-TOSCANA", "BLOCKER", "Recipe code mora biti stabilan.")
check("recipe_type", get(["recipe_type_router"]) == "GROUND_MEAT_OR_CASING", "BLOCKER", "Finocchiona je mljeveni proizvod u ovitku.")
check("public_update_false", get(["public_update_allowed"]) is False, "BLOCKER", "Javni update mora biti false.")
check("public_publish_false", get(["public_publish_allowed"]) is False, "BLOCKER", "Javna objava mora biti false u ovoj fazi.")
check("source_post_write_false", get(["source_post_write_allowed"]) is False, "BLOCKER", "Source post se ne smije mijenjati.")
check("canonical_confirmed", get(["canonical_project_status"]) == "CONFIRMED_RECIPE", "MAJOR", "Source validation je potvrdio službeni receptni okvir.")
check("primary_source", get(["source_policy", "primary_source"]) == "SRC-1982-005", "BLOCKER", "Primarni izvor mora biti konsolidirani 2024. disciplinar.")
check("sources_contains_primary", "SRC-1982-005" in sources_text, "BLOCKER", "sources.yml mora sadržavati primarni izvor SRC-1982-005.")

# 10 kg i sirovine
raw_items = get(["raw_materials", "working_formula_10kg"], [])
raw_total = round(sum(float(i.get("kg", 0)) for i in raw_items), 3) if isinstance(raw_items, list) else 0
check("raw_total_10kg", raw_total == 10.0, "BLOCKER", f"Ukupna sirovina mora biti 10 kg; dobiveno {raw_total}.")
check("raw_items_count", isinstance(raw_items, list) and len(raw_items) >= 4, "MAJOR", "Radna formulacija mora imati razrađene sirovinske skupine.")
check("allowed_cuts_present", len(get(["raw_materials", "source_locked_allowed_cuts"], [])) >= 5, "MAJOR", "Mora postojati popis dopuštenih rezova iz disciplinara.")
check("not_allowed_present", len(get(["raw_materials", "not_allowed"], [])) >= 3, "MAJOR", "Mora biti navedeno što nije dopušteno.")

# Začini i rasponi
mandatory = get(["ingredients_10kg", "mandatory_with_chosen_values"], [])
mandatory_by_name = {i.get("name"): i for i in mandatory if isinstance(i, dict)}
expected_ranges = {
    "sol": (250, 350),
    "mljeveni crni papar": (5, 10),
    "lomljeni papar / papar u zrnu": (15, 40),
    "suhi češnjak": (5, 10),
    "sjeme komorača ili cvijet komorača": (20, 50),
}
for name, (lo, hi) in expected_ranges.items():
    item = mandatory_by_name.get(name)
    val = item.get("chosen_g") if item else None
    check(
        "mandatory_range_" + name.replace(" ", "_").replace("/", "_"),
        isinstance(val, (int, float)) and lo <= float(val) <= hi,
        "BLOCKER",
        f"{name}: {val}; dopušteno {lo}-{hi} g / 10 kg."
    )
    if item:
        g_per_kg = item.get("g_per_kg")
        expected = round(float(val) / 10.0, 3)
        check(
            "g_per_kg_" + name.replace(" ", "_").replace("/", "_"),
            isinstance(g_per_kg, (int, float)) and abs(float(g_per_kg) - expected) < 0.001,
            "MAJOR",
            f"{name}: g/kg mora odgovarati 10 kg šarži."
        )

optional_used = get(["ingredients_10kg", "optional_used_in_working_formula"], [])
wine = next((i for i in optional_used if "vino" in i.get("name", "").lower()), {})
sugar = next((i for i in optional_used if "dekstroza" in i.get("name", "").lower() or "saharoza" in i.get("name", "").lower()), {})
check("wine_max", float(wine.get("chosen_l", 999)) <= 0.1, "MAJOR", "Vino mora biti ≤ 0,1 L / 10 kg.")
check("sugar_max", float(sugar.get("chosen_g", 999)) <= 100, "MAJOR", "Šećer mora biti ≤ 100 g / 10 kg.")

optional_not = get(["ingredients_10kg", "optional_not_used_in_base_draft"], [])
optional_text = json.dumps(optional_not, ensure_ascii=False).lower()
check("starter_policy", "starter" in optional_text and "not_used" in optional_text, "MAJOR", "Starter kultura mora biti jasno označena kao nedodana u bazni draft.")
check("nitrite_policy", "nitriti" in optional_text or "nitrati" in optional_text, "MAJOR", "Nitriti/nitrati moraju imati sigurnosnu politiku ako se spominju.")

# Češnjak
check("garlic_mode", get(["garlic_policy", "mode"]) == "direct_dried_garlic", "MAJOR", "Češnjak mora biti jasno definiran kao suhi direktni češnjak.")
check("garlic_liquid_false", get(["garlic_policy", "garlic_liquid_used"]) is False, "MAJOR", "Ne smije ostati nejasno koristi li se tekućina od češnjaka.")
check("garlic_soaking_defined", get(["garlic_policy", "soaking_liquid"]) == "none" and get(["garlic_policy", "soaking_time_minutes"]) == 0 and get(["garlic_policy", "boiled"]) is False, "MAJOR", "Ako nema tekućine od češnjaka, to mora biti eksplicitno navedeno.")

# Mljevenje i mast
check("grinding_range_official", get(["grinding_and_fat_handling", "official_grinding_range_mm"]) == "4.5-8", "BLOCKER", "Službeni raspon mljevenja mora biti 4,5-8 mm.")
plate = get(["grinding_and_fat_handling", "chosen_plate_mm"])
check("chosen_plate_ok", isinstance(plate, (int, float)) and 4.5 <= float(plate) <= 8, "BLOCKER", "Odabrana rešetka mora biti unutar službenog raspona.")
check("pre_cut_present", bool(get(["grinding_and_fat_handling", "pre_cut_size_mm"])), "MAJOR", "Mora biti navedena dimenzija rezanja prije mljevenja.")
check("fat_handling_present", len(str(get(["grinding_and_fat_handling", "fat_handling"], ""))) > 80, "MAJOR", "Mora biti opisana obrada masnoće.")
check("mix_temp_present", get(["grinding_and_fat_handling", "mix_temperature_max_c"]) == 8, "MAJOR", "Mora biti naveden maksimum temperature smjese.")

# Crijeva
soak = get(["casing_and_filling", "soaking"], {})
check("casing_official_present", bool(get(["casing_and_filling", "official_casing"])), "MAJOR", "Mora biti navedena službena vrsta ovitka.")
check("casing_guidance_present", bool(get(["casing_and_filling", "working_casing_guidance"])), "MAJOR", "Mora biti navedena radna smjernica kalibra/ovitka.")
check("casing_soaking_required", soak.get("required") is True, "MAJOR", "Namakanje crijeva mora biti definirano.")
check("casing_soaking_liquid", soak.get("liquid") == "pitka voda", "MAJOR", "Mora biti navedena tekućina za namakanje.")
check("casing_soaking_temp", bool(soak.get("temperature_c")), "MAJOR", "Mora biti navedena temperatura namakanja.")
check("casing_soaking_time", bool(soak.get("time_minutes")), "MAJOR", "Mora biti navedeno vrijeme namakanja.")
check("casing_not_boiled", soak.get("boiled") is False, "MAJOR", "Mora biti jasno da se crijeva ne prokuhavaju.")
check("casing_rinsing", bool(soak.get("rinsing")), "MAJOR", "Mora biti navedeno ispiranje.")

# Proces
process = get(["process"], [])
process_text = json.dumps(process, ensure_ascii=False).lower()
required_process_words = [
    "odabir",
    "rezanje",
    "mljevenje",
    "miješanje",
    "punjenje",
    "sušenje",
    "zrenje",
    "završna provjera"
]
for word in required_process_words:
    check("process_" + word.replace(" ", "_"), word in process_text, "MAJOR", f"Proces mora imati fazu: {word}.")
check("drying_params", "12-25" in process_text or "12–25" in process_text, "BLOCKER", "Sušenje mora imati parametar 12-25 °C.")
check("ageing_params", ("11-18" in process_text or "11–18" in process_text) and ("65-90" in process_text or "65–90" in process_text), "BLOCKER", "Zrenje mora imati 11-18 °C i 65-90 % RH.")
check("minimum_duration", "15" in process_text and "21" in process_text and "45" in process_text, "MAJOR", "Minimalna trajanja 15/21/45 dana moraju biti navedena.")

# Done when i problemi
done_when = get(["done_when"], [])
problems = get(["problems_and_solutions"], [])
check("done_when_count", isinstance(done_when, list) and len(done_when) >= 5, "MAJOR", "Mora biti dovoljno kriterija gotovosti.")
check("problem_solution_count", isinstance(problems, list) and len(problems) >= 5, "MAJOR", "Mora biti najmanje 5 problema s rješenjima.")
for idx, item in enumerate(problems):
    check(
        f"problem_solution_{idx+1}",
        bool(item.get("problem")) and bool(item.get("likely_cause")) and bool(item.get("solution")),
        "MAJOR",
        "Svaki problem mora imati problem, uzrok i konkretno rješenje."
    )

# Tekstualna sigurnost i interni status
active_blockers = get(["active_blockers"], [])
check("active_blockers_present", isinstance(active_blockers, list) and len(active_blockers) >= 3, "MAJOR", "Mora biti jasno što još blokira javni update.")
check("serving_not_for_frying", contains_any(json.dumps(get(["serving_and_storage"], {}), ensure_ascii=False), ["ne tretirati kao kobasicu za pečenje"]), "MAJOR", "Posluživanje mora jasno reći da se ne tretira kao kobasica za pečenje.")
check("safety_discard_note", contains_any(json.dumps(get(["serving_and_storage"], {}), ensure_ascii=False), ["sumnja", "ne kuša", "ne poslužuje"]), "MAJOR", "Mora postojati sigurnosna napomena za sumnjive proizvode.")

# YAML tekst sanity
check("recipe_yml_contains_no_tabs", "\t" not in recipe_yml_text, "MAJOR", "YAML ne smije imati tab znakove.")
check("recipe_yml_contains_10kg", "batch_size_kg: 10.0" in recipe_yml_text or "batch_size_kg: 10" in recipe_yml_text, "MAJOR", "recipe.yml mora sadržavati 10 kg šaržu.")
check("recipe_yml_contains_no_public_true", "public_update_allowed: true" not in recipe_yml_text.lower(), "BLOCKER", "recipe.yml ne smije imati public_update_allowed true.")

major_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] == "BLOCKER"]

qa_status = "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS" if not major_failures else "RECIPE_YML_QA_FAIL"

result = {
    "generated_at": now,
    "post_id": 1982,
    "recipe_code": get(["recipe_code"]),
    "qa_status": qa_status,
    "wordpress_write_allowed": False,
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "ready_for_sections": qa_status == "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS",
    "ready_for_private_clone": False,
    "raw_material_total_kg": raw_total,
    "primary_source": get(["source_policy", "primary_source"]),
    "checks": checks,
    "major_fail_total": len(major_failures),
    "blocker_fail_total": len(blocker_failures),
    "next_step": "GENERATE_DRY_RECIPE_SECTIONS_AND_VERIFIED_PROCESS" if qa_status == "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS" else "REPAIR_RECIPE_YML"
}

json_path = review_dir / "1982_recipe_yml_internal_qa_v1.json"
json_path.write_text(json.dumps(result, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks_path = review_dir / "1982_recipe_yml_internal_qa_checks.csv"
with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

report_path = review_dir / "1982_RECIPE_YML_INTERNAL_QA_REPORT.md"
md = []
md.append("# 1982 Finocchiona Toscana recipe.yml internal QA v1")
md.append("")
md.append(f"Status: **{qa_status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Provjerava je li `recipe.yml` dovoljno čist za generiranje strukturiranih sekcija i procesnog zapisa.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `1982`")
md.append(f"- Recipe code: `{get(['recipe_code'])}`")
md.append("- WordPress write allowed: `false`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append(f"- Raw material total: `{raw_total} kg`")
md.append(f"- Primary source: `{get(['source_policy', 'primary_source'])}`")
md.append(f"- Major fail total: `{len(major_failures)}`")
md.append(f"- Blocker fail total: `{len(blocker_failures)}`")
md.append(f"- Ready for sections: `{'true' if result['ready_for_sections'] else 'false'}`")
md.append("")
md.append("## Kritične provjere")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    if c["severity"] in ("BLOCKER", "MAJOR"):
        md.append(f"| {c['key']} | {c['status']} | {c['severity']} | {c['note']} |")
md.append("")
md.append("## Zaključak")
md.append("")
if qa_status == "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS":
    md.append("`recipe.yml` je prošao internal QA. Sljedeći korak je generirati `_dry_recipe_sections` i `_dry_verified_process` iz ovog zapisa, bez javnog WordPress updatea.")
else:
    md.append("`recipe.yml` nije prošao internal QA. Prije nastavka treba napraviti repair.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

qa_old = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_1982_RECIPE_YML_INTERNAL_QA_V1 -->"
block = f"""
{marker}

## 1982 Finocchiona Toscana recipe.yml internal QA v1

Status: **{qa_status}**

- WordPress write allowed: `false`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Raw material total: `{raw_total} kg`
- Major fail total: `{len(major_failures)}`
- Blocker fail total: `{len(blocker_failures)}`
- Ready for sections: `{'true' if result['ready_for_sections'] else 'false'}`
- Report: `review/{review_dir.name}/1982_RECIPE_YML_INTERNAL_QA_REPORT.md`
- JSON: `review/{review_dir.name}/1982_recipe_yml_internal_qa_v1.json`
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

readme_old = readme_path.read_text(encoding="utf-8")
readme_marker = "<!-- DC_1982_RECIPE_YML_INTERNAL_QA_V1 -->"
readme_block = f"""
{readme_marker}

## 1982 recipe.yml internal QA v1

Status: **{qa_status}**

`recipe.yml` za Finocchiona Toscana provjeren je internim QA-om. Javni update ostaje blokiran. Sljedeći korak je generiranje strukturiranih sekcija i procesnog zapisa za privatni preview.
"""
if readme_marker not in readme_old:
    readme_path.write_text(readme_old.rstrip() + "\n\n" + readme_block.strip() + "\n", encoding="utf-8")

print("=== 1982 RECIPE.YML INTERNAL QA COMPLETE ===")
print(f"QA_STATUS={qa_status}")
print("POST_ID=1982")
print(f"RECIPE_CODE={get(['recipe_code'])}")
print(f"RAW_MATERIAL_TOTAL_KG={raw_total}")
print(f"PRIMARY_SOURCE={get(['source_policy', 'primary_source'])}")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print(f"MAJOR_FAIL_TOTAL={len(major_failures)}")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"READY_FOR_SECTIONS={'true' if result['ready_for_sections'] else 'false'}")
print(f"REPORT={report_path}")
