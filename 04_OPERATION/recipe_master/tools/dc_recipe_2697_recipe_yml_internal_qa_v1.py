#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_2697_recipe_yml_internal_qa_v1.py DOSSIER_DIR DRAFT_JSON REVIEW_DIR RECIPE_YML", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
draft_json = Path(sys.argv[2])
review_dir = Path(sys.argv[3])
recipe_yml_path = Path(sys.argv[4])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"

recipe = json.loads(draft_json.read_text(encoding="utf-8"))
recipe_yml_text = recipe_yml_path.read_text(encoding="utf-8")
sources_text = sources_path.read_text(encoding="utf-8")

now = datetime.now(timezone.utc).isoformat()
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

def has_text(text, terms):
    low = str(text).lower()
    return all(str(t).lower() in low for t in terms)

# Osnovna struktura
check("schema_version", get(["schema_version"]) == "drycured_recipe_yml_v1", "BLOCKER", "Schema mora biti drycured_recipe_yml_v1.")
check("post_id", get(["post_id"]) == 2697, "BLOCKER", "Post ID mora biti 2697.")
check("recipe_code", get(["recipe_code"]) == "HR-BR-2697-BARANJSKA-LJUTA-KOBASICA", "BLOCKER", "Recipe code mora biti stabilan.")
check("title_hr", get(["title_hr"]) == "Baranjska kobasica – ljuta varijanta", "MAJOR", "Radni naslov mora biti urednički normaliziran.")
check("title_review_required", get(["title_review_required"]) is True, "MAJOR", "Mora ostati označeno da je prije javne objave potrebna recenzija naslova.")
check("country_region", get(["country"]) == "Hrvatska" and "Baranja" in get(["region"], ""), "BLOCKER", "Recept mora biti hrvatski, regija Baranja/Slavonija.")
check("recipe_type", get(["recipe_type_router"]) == "GROUND_MEAT_OR_CASING", "BLOCKER", "Baranjska kobasica mora biti GROUND_MEAT_OR_CASING.")
check("canonical_confirmed", get(["canonical_project_status"]) == "CONFIRMED_RECIPE", "BLOCKER", "Source validation mora biti CONFIRMED_RECIPE.")
check("draft_status", get(["draft_status"]) == "RECIPE_YML_DRAFT_READY_INTERNAL_QA_REQUIRED", "BLOCKER", "Draft status mora biti spreman za internal QA.")

# Sigurnosne zastavice
check("public_update_false", get(["public_update_allowed"]) is False, "BLOCKER", "Javni update mora biti false.")
check("public_publish_false", get(["public_publish_allowed"]) is False, "BLOCKER", "Javna objava mora biti false.")
check("source_post_write_false", get(["source_post_write_allowed"]) is False, "BLOCKER", "Source post se ne smije mijenjati.")

# Izvori
primary_sources = get(["source_policy", "primary_sources"], [])
check("sources_present", all(s in primary_sources for s in ["SRC-2697-001", "SRC-2697-002", "SRC-2697-003"]), "BLOCKER", "Moraju biti prisutna sva tri zaključana izvora.")
check("sources_yml_contains_sources", all(s in sources_text for s in ["SRC-2697-001", "SRC-2697-002", "SRC-2697-003"]), "BLOCKER", "sources.yml mora sadržavati sve izvore.")
check("no_protected_status_claim", get(["protected_status"]) is None, "MAJOR", "Ne smije se tvrditi EU zaštićeni status.")

# Sirovine 10 kg
raw_items = get(["raw_materials", "working_formula_10kg"], [])
raw_total = round(sum(float(i.get("kg", 0)) for i in raw_items), 3) if isinstance(raw_items, list) else 0
check("raw_material_sum_10kg", abs(raw_total - 10.0) < 0.001, "BLOCKER", f"Ukupno sirovina mora biti 10 kg; dobiveno {raw_total}.")
check("raw_material_two_groups", isinstance(raw_items, list) and len(raw_items) == 2, "MAJOR", "Radna formula treba jasno razlikovati meso i tvrdu slaninu.")
if isinstance(raw_items, list) and len(raw_items) >= 2:
    check("meat_kg_scaled", abs(float(raw_items[0].get("kg", 0)) - 9.09) < 0.02, "BLOCKER", "Meso mora biti skalirano na oko 9,09 kg.")
    check("fat_kg_scaled", abs(float(raw_items[1].get("kg", 0)) - 0.91) < 0.02, "BLOCKER", "Tvrda slanina mora biti skalirana na oko 0,91 kg.")
check("fat_handling_summary", len(str(get(["raw_materials", "fat_handling_summary"], ""))) > 80, "MAJOR", "Mora biti opisana obrada tvrde slanine/masnoće.")

# Začini
ingredients = get(["ingredients_10kg", "working_formula"], [])
by_name = {i.get("name"): i for i in ingredients if isinstance(i, dict)}

def ing_val(name):
    return by_name.get(name, {}).get("chosen_g")

salt = ing_val("sol")
sugar = ing_val("šećer")
pepper = ing_val("crni papar / biber, mljeven ili grublje lomljen")
sweet_paprika = ing_val("slatka mljevena paprika")
hot_paprika = ing_val("ljuta mljevena paprika")
garlic = ing_val("pastozni češnjak")

check("salt_190g", salt == 190, "BLOCKER", "Sol mora biti 190 g / 10 kg u ovom draftu.")
check("salt_percent", abs(float(get(["ingredients_10kg", "salt_percent"], 0)) - 1.9) < 0.001, "BLOCKER", "Sol mora biti 1,9 %.")
check("sugar_25g", sugar == 25, "MAJOR", "Šećer mora biti 25 g / 10 kg.")
check("pepper_45g", pepper == 45, "MAJOR", "Papar/biber mora biti 45 g / 10 kg.")
check("sweet_paprika_120g", sweet_paprika == 120, "MAJOR", "Slatka paprika mora biti 120 g / 10 kg.")
check("hot_paprika_70g", hot_paprika == 70, "MAJOR", "Ljuta paprika mora biti 70 g / 10 kg.")
check("paprika_total_190g", get(["ingredients_10kg", "total_paprika_g"]) == 190, "BLOCKER", "Ukupna paprika mora biti 190 g / 10 kg.")
check("paprika_percent", abs(float(get(["ingredients_10kg", "paprika_percent"], 0)) - 1.9) < 0.001, "BLOCKER", "Ukupna paprika mora biti 1,9 %.")
check("garlic_28g", garlic == 28, "MAJOR", "Pastozni češnjak mora biti 28 g / 10 kg.")
check("garlic_percent", abs(float(get(["ingredients_10kg", "garlic_percent"], 0)) - 0.28) < 0.001, "MAJOR", "Češnjak mora biti 0,28 %.")
check("nitrite_not_used", get(["ingredients_10kg", "nitrite_nitrate_policy", "used"]) is False, "MAJOR", "Nitritna sol nije uključena u bazni draft.")

# Češnjak
check("garlic_mode_paste", get(["garlic_policy", "mode"]) == "direct_garlic_paste", "MAJOR", "Češnjak mora biti pasta / fino zgnječeni češnjak.")
check("garlic_paste_used", get(["garlic_policy", "paste_garlic_used"]) is True, "MAJOR", "Mora biti označeno da se koristi pastozni češnjak.")
check("garlic_liquid_false", get(["garlic_policy", "garlic_liquid_used"]) is False, "MAJOR", "Ne koristi se tekućina od češnjaka.")
check("garlic_no_soaking", get(["garlic_policy", "soaking_liquid"]) == "none" and get(["garlic_policy", "soaking_time_minutes"]) == 0 and get(["garlic_policy", "boiled"]) is False, "MAJOR", "Ako nema tekućine od češnjaka, to mora biti eksplicitno navedeno.")

# Mljevenje
check("pre_cut_present", get(["grinding_and_fat_handling", "pre_cut_size_mm"]) == "20-30", "MAJOR", "Dimenzija rezanja prije mljevenja mora biti 20-30 mm.")
check("grinding_6mm", get(["grinding_and_fat_handling", "chosen_plate_mm"]) == 6, "BLOCKER", "Mljevenje mora biti 6 mm.")
check("meat_temperature", get(["grinding_and_fat_handling", "meat_temperature_c"]) == "0-4", "MAJOR", "Temperatura mesa mora biti 0-4 °C.")
check("mix_temp_max", get(["grinding_and_fat_handling", "mix_temperature_max_c"]) == 8, "MAJOR", "Maksimalna temperatura smjese mora biti 8 °C.")
check("fat_handling_detail", len(str(get(["grinding_and_fat_handling", "fat_handling"], ""))) > 100, "MAJOR", "Mora biti detaljno opisana obrada tvrde slanine.")
check("texture_goal", "nerazmazana" in str(get(["grinding_and_fat_handling", "texture_goal"], "")).lower(), "MAJOR", "Mora biti cilj nerazmazane masnoće.")

# Crijeva
soak = get(["casing_and_filling", "soaking"], {})
check("casing_type", get(["casing_and_filling", "casing_type"]) == "tanka svinjska crijeva", "BLOCKER", "Ovitak mora biti tanka svinjska crijeva.")
check("casing_length", get(["casing_and_filling", "source_length_guidance_cm"]) == "35-40", "MAJOR", "Dužina komada mora biti 35-40 cm prema izvoru.")
check("casing_caliber_guidance", "28-34" in str(get(["casing_and_filling", "working_caliber_guidance"], "")), "MAJOR", "Mora postojati radna smjernica kalibra.")
check("soaking_required", soak.get("required") is True, "MAJOR", "Namakanje mora biti definirano.")
check("soaking_liquid", soak.get("liquid") == "pitka voda", "MAJOR", "Tekućina za namakanje mora biti pitka voda.")
check("soaking_temperature", soak.get("temperature_c") == "20-25", "MAJOR", "Temperatura namakanja mora biti 20-25 °C.")
check("soaking_time", soak.get("time_minutes") == "30-45", "MAJOR", "Vrijeme namakanja mora biti 30-45 min.")
check("casing_not_boiled", soak.get("boiled") is False, "MAJOR", "Crijeva se ne prokuhavaju.")
check("casing_rinsing", "isprati" in str(soak.get("rinsing", "")).lower(), "MAJOR", "Mora biti navedeno ispiranje.")

# Proces
process = get(["process"], [])
process_text = json.dumps(process, ensure_ascii=False).lower()
check("process_step_count", isinstance(process, list) and len(process) >= 10, "BLOCKER", "Proces mora imati najmanje 10 faza.")
for word in ["odabir", "rezanje", "mljevenje", "miješanje", "crijeva", "punjenje", "odmor", "dimljenje", "zrenje", "završna"]:
    check("process_has_" + word, word in process_text, "MAJOR", f"Proces mora sadržavati fazu: {word}.")
check("smoking_3_4_by_6h", "3-4 dima" in process_text and "6 h" in process_text, "BLOCKER", "Dimljenje mora imati 3-4 dima po oko 6 h.")
check("smoking_alt_5_6", "5-6" in process_text and "svaki drugi dan" in process_text, "MAJOR", "Treba biti zabilježena i stručna usporedba 5-6 dimova svaki drugi dan.")
check("maturation_25_30", "25-30" in process_text, "BLOCKER", "Zrenje mora imati 25-30 dana.")
check("thin_blue_smoke", "tanak plavi dim" in process_text, "MAJOR", "Dimljenje mora spominjati tanak plavi dim.")

# Gotovost, greške, čuvanje
done_when = get(["done_when"], [])
problems = get(["problems_and_solutions"], [])
check("done_when_count", isinstance(done_when, list) and len(done_when) >= 6, "MAJOR", "Mora biti dovoljno kriterija gotovosti.")
check("problem_solution_count", isinstance(problems, list) and len(problems) >= 7, "MAJOR", "Mora biti najmanje 7 problema s rješenjima.")
for idx, item in enumerate(problems):
    check(
        f"problem_solution_{idx+1}",
        bool(item.get("problem")) and bool(item.get("likely_cause")) and bool(item.get("solution")),
        "MAJOR",
        "Svaki problem mora imati problem, uzrok i konkretno rješenje."
    )

serving = json.dumps(get(["serving_and_storage"], {}), ensure_ascii=False).lower()
check("not_for_frying", "ne tretirati kao kobasicu za pečenje" in serving, "MAJOR", "Mora pisati da se ne tretira kao kobasica za pečenje.")
check("discard_suspicious", "ne kuša" in serving and "ne poslužuje" in serving, "MAJOR", "Sumnjiv proizvod se ne kuša i ne poslužuje.")

# Aktivne blokade i tekstualna provjera
active_blockers = get(["active_blockers"], [])
check("active_blockers_present", isinstance(active_blockers, list) and len(active_blockers) >= 5, "MAJOR", "Mora biti jasno što još blokira javni update.")
check("recipe_yml_no_tabs", "\t" not in recipe_yml_text, "MAJOR", "recipe.yml ne smije imati tab znakove.")
check("recipe_yml_no_public_true", "public_update_allowed\": true" not in recipe_yml_text.lower() and "public_update_allowed: true" not in recipe_yml_text.lower(), "BLOCKER", "recipe.yml ne smije imati public_update_allowed true.")

major_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in major_failures if c["severity"] == "BLOCKER"]

qa_status = "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS" if not major_failures else "RECIPE_YML_QA_FAIL"

result = {
    "generated_at": now,
    "post_id": 2697,
    "recipe_code": get(["recipe_code"]),
    "qa_status": qa_status,
    "wordpress_write_allowed": False,
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "ready_for_sections": qa_status == "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS",
    "ready_for_private_clone": False,
    "raw_material_total_kg": raw_total,
    "recipe_type_router": get(["recipe_type_router"]),
    "checks": checks,
    "major_fail_total": len(major_failures),
    "blocker_fail_total": len(blocker_failures),
    "next_step": "GENERATE_DRY_RECIPE_SECTIONS_AND_VERIFIED_PROCESS" if qa_status == "RECIPE_YML_QA_PASS_READY_FOR_SECTIONS" else "REPAIR_RECIPE_YML"
}

json_path = review_dir / "2697_recipe_yml_internal_qa_v1.json"
json_path.write_text(json.dumps(result, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks_path = review_dir / "2697_recipe_yml_internal_qa_checks.csv"
with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

report_path = review_dir / "2697_RECIPE_YML_INTERNAL_QA_REPORT.md"
md = []
md.append("# 2697 Baranjska kobasica – ljuta varijanta recipe.yml internal QA v1")
md.append("")
md.append(f"Status: **{qa_status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Provjerava je li `recipe.yml` dovoljno čist za generiranje strukturiranih sekcija i procesnog zapisa.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `2697`")
md.append(f"- Recipe code: `{get(['recipe_code'])}`")
md.append("- WordPress write allowed: `false`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append(f"- Raw material total: `{raw_total} kg`")
md.append(f"- Recipe type router: `{get(['recipe_type_router'])}`")
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
    md.append("`recipe.yml` je prošao internal QA. Sljedeći korak je generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview, bez javnog WordPress updatea.")
else:
    md.append("`recipe.yml` nije prošao internal QA. Prije nastavka treba napraviti repair.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def append_once(path: Path, marker: str, block: str):
    old = path.read_text(encoding="utf-8")
    if marker not in old:
        path.write_text(old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

qa_block = f"""
<!-- DC_2697_RECIPE_YML_INTERNAL_QA_V1 -->

## 2697 Baranjska kobasica – ljuta varijanta recipe.yml internal QA v1

Status: **{qa_status}**

- WordPress write allowed: `false`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Raw material total: `{raw_total} kg`
- Major fail total: `{len(major_failures)}`
- Blocker fail total: `{len(blocker_failures)}`
- Ready for sections: `{'true' if result['ready_for_sections'] else 'false'}`
- Report: `review/{review_dir.name}/2697_RECIPE_YML_INTERNAL_QA_REPORT.md`
- JSON: `review/{review_dir.name}/2697_recipe_yml_internal_qa_v1.json`
"""
append_once(qa_path, "<!-- DC_2697_RECIPE_YML_INTERNAL_QA_V1 -->", qa_block)

readme_block = f"""
<!-- DC_2697_RECIPE_YML_INTERNAL_QA_V1 -->

## 2697 recipe.yml internal QA v1

Status: **{qa_status}**

`recipe.yml` za Baranjsku kobasicu – ljutu varijantu provjeren je internim QA-om. Javni update ostaje blokiran.
"""
append_once(readme_path, "<!-- DC_2697_RECIPE_YML_INTERNAL_QA_V1 -->", readme_block)

print("=== 2697 RECIPE.YML INTERNAL QA COMPLETE ===")
print(f"QA_STATUS={qa_status}")
print("POST_ID=2697")
print(f"RECIPE_CODE={get(['recipe_code'])}")
print(f"RAW_MATERIAL_TOTAL_KG={raw_total}")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print(f"MAJOR_FAIL_TOTAL={len(major_failures)}")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"READY_FOR_SECTIONS={'true' if result['ready_for_sections'] else 'false'}")
print(f"REPORT={report_path}")
