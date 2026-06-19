#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 3:
    print("Usage: dc_recipe_1982_recipe_yml_draft_v1.py DOSSIER_DIR REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
review_dir = Path(sys.argv[2])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"
recipe_yml_path = dossier_dir / "recipe.yml"

now = datetime.now(timezone.utc).isoformat()

def yq(s):
    if isinstance(s, bool):
        return "true" if s else "false"
    if isinstance(s, (int, float)):
        return str(s)
    if s is None:
        return "null"
    text = str(s)
    escaped = text.replace("\\", "\\\\").replace('"', '\\"')
    return f'"{escaped}"'

def dump_yaml(obj, indent=0):
    sp = "  " * indent
    out = []
    if isinstance(obj, dict):
        for k, v in obj.items():
            if isinstance(v, (dict, list)):
                out.append(f"{sp}{k}:")
                out.extend(dump_yaml(v, indent + 1))
            else:
                out.append(f"{sp}{k}: {yq(v)}")
    elif isinstance(obj, list):
        for item in obj:
            if isinstance(item, (dict, list)):
                out.append(f"{sp}-")
                out.extend(dump_yaml(item, indent + 1))
            else:
                out.append(f"{sp}- {yq(item)}")
    else:
        out.append(f"{sp}{yq(obj)}")
    return out

official_sources = [
    {
        "id": "SRC-1982-005",
        "type": "official_consolidated_specification_2024",
        "authority": "MASAF / consolidated Finocchiona IGP specification after ordinary modification",
        "url": "https://www.masaf.gov.it/flex/cm/pages/ServeAttachment.php/L/IT/D/1%252F7%252F2%252FD.372a511f04565ec8a7ed/P/BLOB%3AID%3D18907/E/pdf?mode=download",
        "reliability": "high",
        "primary_for_recipe_yml": True,
        "supports": [
            "current consolidated production specification",
            "PGI product identity",
            "allowed cuts",
            "mandatory ingredient ranges",
            "optional additives, wine and starter cultures",
            "grinding range 4.5-8 mm",
            "casing and tying requirements",
            "drying and ageing parameters",
            "minimum drying/ageing durations by stuffed weight",
            "updated aw maximum 0.945"
        ]
    },
    {
        "id": "SRC-1982-003",
        "type": "consortium_specification_page",
        "authority": "Consorzio di tutela della Finocchiona IGP",
        "url": "https://www.finocchionaigp.it/en/specification/",
        "reliability": "high",
        "primary_for_recipe_yml": False,
        "supports": [
            "consortium confirms specification controls origin, recipe, processing and ripening"
        ]
    }
]

recipe = {
    "schema_version": "drycured_recipe_yml_v1",
    "generated_at": now,
    "post_id": 1982,
    "recipe_code": "IT-TOS-1982-FINOCCHIONA-TOSCANA",
    "title_hr": "Finocchiona Toscana IGP",
    "title_original": "Finocchiona Toscana",
    "dossier_status": "CANON_DRAFT_V1_PUBLIC_UPDATE_BLOCKED",
    "canonical_project_status": "CONFIRMED_RECIPE",
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "recipe_type_router": "GROUND_MEAT_OR_CASING",
    "country": "Italija",
    "region": "Toskana",
    "protected_status": "IGP / PGI",
    "batch_size_kg": 10.0,
    "source_policy": {
        "primary_source": "SRC-1982-005",
        "basis": "Službeni konsolidirani disciplinar iz 2024. koristi se kao primarni izvor za radni recipe.yml.",
        "exact_wp_quantities_confirmed": False,
        "working_formula_note": "Sastav mesa je Drycured radna formulacija unutar dopuštenih rezova iz disciplinara; nije tvrdnja da je to jedina službena formulacija.",
        "public_block_reason": "Prije javnog updatea treba proći internal QA, strukturirane sekcije, procesni zapis i privatni pregled."
    },
    "identity": {
        "product_description_hr": "Toskanska suha fermentirana kobasica prepoznatljiva po aromi komorača, mekom do blago mrvičastom prerezu i srednje krupnoj strukturi u kojoj se masni i mesni dijelovi ne odvajaju oštrim granicama.",
        "official_sensory_profile": {
            "aroma": "izražen komorač, lagana nota češnjaka",
            "taste": "svjež, ugodan, nikad kiseo",
            "slice": "od čvrste do mekše, može se blago mrviti",
            "grain": "srednje krupna struktura; masno i nemasno meso povezani bez oštrih granica"
        },
        "chemical_targets_from_spec": {
            "protein_min_percent": 20,
            "fat_max_percent": 35,
            "ph_range": "5.0-6.0",
            "aw_max": 0.945,
            "salt_max_percent": 6
        }
    },
    "raw_materials": {
        "source_locked_allowed_cuts": [
            "otkoštena i očišćena svinjska lopatica",
            "obresci pršuta",
            "traculo / stražnji dio prema lokalnoj talijanskoj terminologiji",
            "gole bez žlijezda",
            "nemasni dio pancete i podbradka",
            "meso coppe / vrata",
            "panceta",
            "pancettone"
        ],
        "not_allowed": [
            "smrznuto meso",
            "mekano i mazivo masno tkivo",
            "veće vezivne opne i grubo vezivno tkivo"
        ],
        "working_formula_10kg": [
            {"name": "svinjska lopatica, očišćena", "kg": 4.0, "source_status": "allowed_cut_working_formula"},
            {"name": "obresci pršuta / nemasni butni obresci", "kg": 2.0, "source_status": "allowed_cut_working_formula"},
            {"name": "nemasni dio pancete i podbradka", "kg": 1.5, "source_status": "allowed_cut_working_formula"},
            {"name": "meso coppe / svinjski vrat", "kg": 1.0, "source_status": "allowed_cut_working_formula"},
            {"name": "panceta / pancettone s čvršćom masnoćom", "kg": 1.5, "source_status": "allowed_cut_working_formula"}
        ],
        "total_kg": 10.0,
        "fat_control_note": "Cilj je ostati ispod 35 % ukupne masti u gotovom proizvodu. Meku masnoću i veće vezivno tkivo ukloniti prije mljevenja."
    },
    "ingredients_10kg": {
        "mandatory_with_chosen_values": [
            {"name": "sol", "chosen_g": 280, "official_range_g_per_10kg": "250-350", "g_per_kg": 28.0},
            {"name": "mljeveni crni papar", "chosen_g": 8, "official_range_g_per_10kg": "5-10", "g_per_kg": 0.8},
            {"name": "lomljeni papar / papar u zrnu", "chosen_g": 25, "official_range_g_per_10kg": "15-40", "g_per_kg": 2.5},
            {"name": "suhi češnjak", "chosen_g": 8, "official_range_g_per_10kg": "5-10", "g_per_kg": 0.8},
            {"name": "sjeme komorača ili cvijet komorača", "chosen_g": 35, "official_range_g_per_10kg": "20-50", "g_per_kg": 3.5}
        ],
        "optional_used_in_working_formula": [
            {"name": "crno ili toskansko vino", "chosen_l": 0.08, "official_max_l_per_10kg": 0.1, "note": "opcionalno; koristi se u maloj količini samo za aromu i lakše raspoređivanje začina"},
            {"name": "dekstroza ili saharoza", "chosen_g": 30, "official_max_g_per_10kg": 100, "note": "opcionalno; radna pomoć fermentaciji, ne smije prikrivati lošu sirovinu"}
        ],
        "optional_not_used_in_base_draft": [
            {"name": "starter kulture", "status": "allowed_by_spec_but_not_used_in_base_draft", "note": "Ako se koriste, doza mora biti prema proizvođaču i mora proći tehnološku recenziju."},
            {"name": "nitriti / nitrati", "status": "allowed_by_spec_but_not_used_in_base_draft", "note": "Ako se koriste, potrebna je zasebna sigurnosna formulacija, precizno vaganje i zabrana kombiniranja nitritnih mješavina bez upute."},
            {"name": "natrijev askorbat", "status": "not_used_in_base_draft", "official_max_g_per_10kg": 15}
        ]
    },
    "garlic_policy": {
        "mode": "direct_dried_garlic",
        "fresh_garlic_used": False,
        "garlic_liquid_used": False,
        "soaking_liquid": "none",
        "soaking_time_minutes": 0,
        "boiled": False,
        "amount_used_g": 8,
        "note": "U ovoj radnoj verziji koristi se suhi češnjak unutar službenog raspona. Ne koristi se tekućina od češnjaka."
    },
    "grinding_and_fat_handling": {
        "official_grinding_range_mm": "4.5-8",
        "chosen_plate_mm": 6,
        "pre_cut_size_mm": "20-30",
        "meat_temperature_target_c": "0-4",
        "mix_temperature_max_c": 8,
        "fat_handling": "Ukloniti meku masnoću; koristiti čvršću pancetu/pancettone dobro ohlađenu. Meso i masnoća se melju zajedno na 6 mm kako bi presjek ostao srednje krupan i povezan.",
        "critical_control": "Ako se masnoća razmazuje, odmah zaustaviti mljevenje, ohladiti sirovinu i nastavak raditi tek kada je masa ponovno čvrsta."
    },
    "casing_and_filling": {
        "official_casing": "budello naturale o collato",
        "working_casing_guidance": "prirodni ovitak većeg kalibra za komade 0,5-2 kg; radna smjernica 55-80 mm, ovisno o dostupnosti i željenoj težini komada",
        "soaking": {
            "required": True,
            "liquid": "pitka voda",
            "temperature_c": "20-25",
            "time_minutes": "30-45",
            "boiled": False,
            "rinsing": "isperi izvana i iznutra prije punjenja",
            "vinegar_or_additives": "ne dodavati osim ako dobavljač ovitaka izričito traži"
        },
        "filling": {
            "firmness": "čvrsto, ali bez pucanja ovitka",
            "air_pockets": "zračne džepove istisnuti i po potrebi probosti sterilnom iglom",
            "tying": "vezati špagom ili mrežom od prirodnih materijala; ne koristiti metalne/plastične klipse za standardni cijeli proizvod"
        }
    },
    "process": [
        {"step": 1, "name": "Odabir i hlađenje sirovine", "parameters": "0-4 °C", "action": "Koristiti svježe, nesmrznuto svinjsko meso dopuštenih rezova. Ukloniti veće opne, žlijezde i meku masnoću.", "critical_control": "Meso ne smije mirisati kiselo, ljepljivo ili ustajalo."},
        {"step": 2, "name": "Rezanje", "parameters": "20-30 mm komadi", "action": "Rezati na komade prikladne za mljevenje i ponovno ohladiti.", "critical_control": "Ako se masnoća lijepi za nož, masa nije dovoljno hladna."},
        {"step": 3, "name": "Mljevenje", "parameters": "rešetka 6 mm; dopušteni službeni raspon 4,5-8 mm", "action": "Meso i čvršću masnoću samljeti srednje krupno.", "critical_control": "Presjek mora ostati srednje krupan, bez razmazane masnoće."},
        {"step": 4, "name": "Miješanje", "parameters": "masa najviše 8 °C", "action": "Sol, papar, suhi češnjak, komorač i šećer ravnomjerno umiješati; vino dodati postupno u tankom mlazu.", "critical_control": "Miješati do povezivanja, ali ne toliko dugo da se masa zagrije."},
        {"step": 5, "name": "Punjenje i vezanje", "parameters": "prirodni ili collato ovitak; komad najmanje 0,5 kg", "action": "Napuniti pripremljeni ovitak, istisnuti zrak, vezati špagom.", "critical_control": "Masa mora ispuniti ovitak bez šupljina."},
        {"step": 6, "name": "Sušenje / asciugamento", "parameters": "12-25 °C prema disciplinaru", "action": "Nakon punjenja proizvod držati u uvjetima koji omogućuju početnu dehidraciju.", "critical_control": "Ne dopustiti prebrzo stvaranje suhe kore; po potrebi smanjiti propuh i povisiti vlagu."},
        {"step": 7, "name": "Zrenje / stagionatura", "parameters": "11-18 °C; 65-90 % RH", "action": "Zriti do stabilnog presjeka, izražene arome komorača i sigurnih analitičkih ciljeva.", "critical_control": "Za komad 1-6 kg ukupno sušenje i zrenje ne smije biti kraće od 21 dan; za 0,5-1 kg najmanje 15 dana; za 6-25 kg najmanje 45 dana."},
        {"step": 8, "name": "Završna provjera", "parameters": "pH 5,0-6,0; aw ≤ 0,945; mast ≤ 35 %", "action": "Prije javne objave/konzumacije potvrditi sigurnosne i senzorske kriterije.", "critical_control": "Ako je okus kiseo, miris neugodan, ovitak sluzav ili presjek nepravilan, proizvod ne ide u konzumaciju."}
    ],
    "done_when": [
        "prošao je minimalni službeni rok prema težini pri punjenju",
        "miris je ugodan, s jasnim komoračem i blagim češnjakom",
        "okus je svjež i ugodan, nikad kiseo",
        "presjek je srednje krupan, mekan do blago mrvičast, bez razmazane masti",
        "nema sluzi, napuhavanja, truležnog mirisa ni neobičnih plijesni",
        "ciljani laboratorijski kriteriji su pH 5,0-6,0 i aw ≤ 0,945"
    ],
    "problems_and_solutions": [
        {"problem": "Razmazana mast u presjeku", "likely_cause": "sirovina ili nož/rešetka su bili pretopli; korištena je mekana masnoća", "solution": "zaustaviti rad, ohladiti meso i opremu, ukloniti mekanu masnoću i nastaviti tek kada masa ponovno bude čvrsta"},
        {"problem": "Suha kora i mekana jezgra", "likely_cause": "prejak propuh ili preniska vlaga u sušenju", "solution": "smanjiti strujanje zraka, povisiti relativnu vlagu unutar sigurnog raspona i produžiti zrenje"},
        {"problem": "Kiseo okus", "likely_cause": "prebrza ili nekontrolirana fermentacija, previše šećera ili previsoka temperatura", "solution": "ne nuditi proizvod; za sljedeću šaržu smanjiti šećer, voditi temperaturu i koristiti provjereni starter samo uz točnu dozu"},
        {"problem": "Neobične plijesni ili sluzav ovitak", "likely_cause": "loša higijena, previsoka vlaga ili slab protok zraka", "solution": "ako je miris neugodan ili sluzavost prodire u ovitak, proizvod odbaciti; prostor očistiti, osušiti i stabilizirati RH"},
        {"problem": "Napuhavanje ili šupljine", "likely_cause": "zrak u punjenju ili mikrobiološki kvar", "solution": "male zračne džepove odmah probosti sterilnom iglom; ako se pojavi napuhavanje uz neugodan miris, proizvod odbaciti"},
        {"problem": "Žućenje masti i užegao miris", "likely_cause": "oksidacija zbog loše masnoće, dugog kontakta s kisikom ili lošeg skladištenja", "solution": "ne koristiti za posluživanje; u sljedećoj šarži koristiti svježiju čvrstu masnoću i smanjiti izlaganje zraku"}
    ],
    "serving_and_storage": {
        "serving": "Rezati tanko kao suhu salamu. Ne tretirati kao kobasicu za pečenje.",
        "storage": "Čuvati u hladnom i prozračnom prostoru ili vakuumirati nakon završnog zrenja, uz kontrolu mirisa i površine.",
        "public_note": "Ako postoji ikakva sumnja u zdravstvenu ispravnost, proizvod se ne kuša i ne poslužuje."
    },
    "active_blockers": [
        "Internal QA recipe.yml još nije napravljen.",
        "Javni WordPress update nije dopušten.",
        "Potrebno je generirati _dry_recipe_sections i _dry_verified_process prije privatnog clone workflowa.",
        "Ako se kasnije uključe nitriti/nitrati ili starter kulture, potrebna je zasebna tehnološka i sigurnosna recenzija."
    ],
    "sources": official_sources
}

# QA checks before writing
failures = []

total_kg = round(sum(float(x["kg"]) for x in recipe["raw_materials"]["working_formula_10kg"]), 3)
if total_kg != 10.0:
    failures.append(f"raw_material_sum_not_10kg:{total_kg}")

chosen = {x["name"]: x["chosen_g"] for x in recipe["ingredients_10kg"]["mandatory_with_chosen_values"]}
ranges = {
    "sol": (250, 350),
    "mljeveni crni papar": (5, 10),
    "lomljeni papar / papar u zrnu": (15, 40),
    "suhi češnjak": (5, 10),
    "sjeme komorača ili cvijet komorača": (20, 50),
}
for name, (lo, hi) in ranges.items():
    val = chosen.get(name)
    if val is None or not (lo <= val <= hi):
        failures.append(f"mandatory_range_fail:{name}:{val}:{lo}-{hi}")

if not (4.5 <= recipe["grinding_and_fat_handling"]["chosen_plate_mm"] <= 8):
    failures.append("grinding_mm_out_of_range")

if recipe["garlic_policy"]["garlic_liquid_used"] is not False:
    failures.append("garlic_liquid_should_be_false")

if recipe["casing_and_filling"]["soaking"]["boiled"] is not False:
    failures.append("casing_boiled_should_be_false")

if recipe["public_update_allowed"] is not False:
    failures.append("public_update_must_be_false")

if recipe["recipe_type_router"] != "GROUND_MEAT_OR_CASING":
    failures.append("wrong_recipe_type_router")

required_process_names = ["Mljevenje", "Punjenje", "Sušenje", "Zrenje"]
process_names = " | ".join(step["name"] for step in recipe["process"])
for req in required_process_names:
    if req.lower() not in process_names.lower():
        failures.append(f"missing_process:{req}")

if len(recipe["problems_and_solutions"]) < 5:
    failures.append("not_enough_problem_solution_items")

recipe_yml_text = "\n".join(dump_yaml(recipe)) + "\n"
recipe_yml_path.write_text(recipe_yml_text, encoding="utf-8")

json_path = review_dir / "1982_recipe_yml_draft_v1.json"
json_path.write_text(json.dumps(recipe, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks = []
def add_check(key, ok, severity, note):
    checks.append({
        "key": key,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

add_check("raw_material_sum_10kg", total_kg == 10.0, "BLOCKER", f"Ukupno sirovina: {total_kg} kg.")
add_check("salt_in_range", 250 <= chosen["sol"] <= 350, "BLOCKER", "Sol je unutar službenog raspona.")
add_check("ground_pepper_in_range", 5 <= chosen["mljeveni crni papar"] <= 10, "BLOCKER", "Mljeveni papar je unutar službenog raspona.")
add_check("cracked_pepper_in_range", 15 <= chosen["lomljeni papar / papar u zrnu"] <= 40, "BLOCKER", "Lomljeni papar je unutar službenog raspona.")
add_check("garlic_in_range", 5 <= chosen["suhi češnjak"] <= 10, "BLOCKER", "Suhi češnjak je unutar službenog raspona.")
add_check("fennel_in_range", 20 <= chosen["sjeme komorača ili cvijet komorača"] <= 50, "BLOCKER", "Komorač je unutar službenog raspona.")
add_check("wine_under_max", recipe["ingredients_10kg"]["optional_used_in_working_formula"][0]["chosen_l"] <= 0.1, "MAJOR", "Vino je ispod službenog maksimuma.")
add_check("sugar_under_max", recipe["ingredients_10kg"]["optional_used_in_working_formula"][1]["chosen_g"] <= 100, "MAJOR", "Šećer je ispod službenog maksimuma.")
add_check("grinding_range_ok", 4.5 <= recipe["grinding_and_fat_handling"]["chosen_plate_mm"] <= 8, "BLOCKER", "Odabrana rešetka je unutar službenog raspona 4,5-8 mm.")
add_check("casing_soaking_complete", recipe["casing_and_filling"]["soaking"]["required"] and recipe["casing_and_filling"]["soaking"]["time_minutes"] and recipe["casing_and_filling"]["soaking"]["temperature_c"], "MAJOR", "Crijeva imaju definirano namakanje.")
add_check("garlic_policy_complete", recipe["garlic_policy"]["mode"] == "direct_dried_garlic" and not recipe["garlic_policy"]["garlic_liquid_used"], "MAJOR", "Češnjak je jasno definiran; nema tekućine od češnjaka.")
add_check("problem_solution_complete", len(recipe["problems_and_solutions"]) >= 5, "MAJOR", "Problemi imaju konkretna rješenja.")
add_check("public_update_blocked", recipe["public_update_allowed"] is False, "BLOCKER", "Javni update ostaje blokiran.")
add_check("source_2024_primary", recipe["source_policy"]["primary_source"] == "SRC-1982-005", "MAJOR", "Primarni izvor za draft je konsolidirani 2024. dokument.")

qa_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in qa_failures if c["severity"] == "BLOCKER"]

draft_status = "RECIPE_YML_DRAFT_READY_INTERNAL_QA_REQUIRED" if not blocker_failures else "RECIPE_YML_DRAFT_BLOCKED"

checks_path = review_dir / "1982_recipe_yml_draft_checks.csv"
with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

# Update sources.yml with current 2024 primary source
sources_lines = []
sources_lines.append("post_id: 1982")
sources_lines.append("title: FINOCCHIONA TOSCANA")
sources_lines.append("validation_status: CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
sources_lines.append("canonical_project_status: CONFIRMED_RECIPE")
sources_lines.append("primary_source_for_recipe_yml: SRC-1982-005")
sources_lines.append("public_update_allowed: false")
sources_lines.append("public_publish_allowed: false")
sources_lines.append("source_post_write_allowed: false")
sources_lines.append("official_product_confirmed: true")
sources_lines.append("official_recipe_framework_confirmed: true")
sources_lines.append("exact_wp_recipe_quantities_confirmed: false")
sources_lines.append("sources:")
for s in official_sources:
    sources_lines.append(f"  - id: {s['id']}")
    sources_lines.append(f"    type: {s['type']}")
    sources_lines.append(f"    authority: {s['authority']}")
    sources_lines.append(f"    url: {s['url']}")
    sources_lines.append(f"    reliability: {s['reliability']}")
    sources_lines.append(f"    primary_for_recipe_yml: {'true' if s['primary_for_recipe_yml'] else 'false'}")
    sources_lines.append("    supports:")
    for item in s["supports"]:
        sources_lines.append(f"      - {item}")
sources_lines.append("recipe_yml_draft:")
sources_lines.append(f"  status: {draft_status}")
sources_lines.append("  file: recipe.yml")
sources_lines.append("  public_update_allowed: false")
sources_path.write_text("\n".join(sources_lines) + "\n", encoding="utf-8")

report_path = review_dir / "1982_RECIPE_YML_DRAFT_REPORT.md"
md = []
md.append("# 1982 Finocchiona Toscana recipe.yml draft v1")
md.append("")
md.append(f"Status: **{draft_status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Izrađuje radni `recipe.yml` za Finocchiona Toscana na temelju konsolidiranog službenog disciplinara iz 2024. i Drycured standarda.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `1982`")
md.append("- Recipe code: `IT-TOS-1982-FINOCCHIONA-TOSCANA`")
md.append("- Batch: `10 kg`")
md.append("- Type router: `GROUND_MEAT_OR_CASING`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Primary source: `SRC-1982-005`")
md.append("- Raw material total: `" + str(total_kg) + " kg`")
md.append("- Blocker fail total: `" + str(len(blocker_failures)) + "`")
md.append("")
md.append("## Radna formulacija za 10 kg")
md.append("")
md.append("| Sirovina | kg | Status |")
md.append("|---|---:|---|")
for item in recipe["raw_materials"]["working_formula_10kg"]:
    md.append(f"| {item['name']} | {item['kg']} | {item['source_status']} |")
md.append("")
md.append("## Začini i dodaci")
md.append("")
md.append("| Sastojak | Količina | Službeni raspon / status |")
md.append("|---|---:|---|")
for item in recipe["ingredients_10kg"]["mandatory_with_chosen_values"]:
    md.append(f"| {item['name']} | {item['chosen_g']} g | {item['official_range_g_per_10kg']} g / 10 kg |")
md.append("| crno/toskansko vino | 0,08 L | opcionalno, do 0,1 L / 10 kg |")
md.append("| dekstroza ili saharoza | 30 g | opcionalno, do 100 g / 10 kg |")
md.append("")
md.append("## Tehnološke odluke")
md.append("")
md.append("- Mljevenje: `6 mm`, unutar službenog raspona `4,5-8 mm`.")
md.append("- Češnjak: koristi se suhi češnjak izravno; ne koristi se tekućina od češnjaka.")
md.append("- Crijeva: prirodni ili collato ovitak; za radni kućni preview navedena je smjernica većeg kalibra, uz namakanje 30-45 min u pitkoj vodi 20-25 °C, bez prokuhavanja.")
md.append("- Nitriti/nitrati i starter kulture nisu uključeni u bazni draft; ako se kasnije uključe, traže posebnu tehnološku i sigurnosnu recenziju.")
md.append("- Sušenje: 12-25 °C prema disciplinaru.")
md.append("- Zrenje: 11-18 °C i 65-90 % RH.")
md.append("- Minimalno trajanje: 15 / 21 / 45 dana prema težini pri punjenju.")
md.append("")
md.append("## QA provjere")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    md.append(f"| {c['key']} | {c['status']} | {c['severity']} | {c['note']} |")
md.append("")
md.append("## Otvoreno prije javnog updatea")
md.append("")
for b in recipe["active_blockers"]:
    md.append(f"- {b}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Pokrenuti internal QA za `recipe.yml`, zatim generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview. Javni WordPress update i dalje nije dopušten.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

qa_old = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_1982_RECIPE_YML_DRAFT_V1 -->"
block = f"""
{marker}

## 1982 Finocchiona Toscana recipe.yml draft v1

Status: **{draft_status}**

- Recipe file: `recipe.yml`
- Recipe code: `IT-TOS-1982-FINOCCHIONA-TOSCANA`
- Batch: `10 kg`
- Primary source: `SRC-1982-005`
- Type router: `GROUND_MEAT_OR_CASING`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Blocker fail total: `{len(blocker_failures)}`
- Report: `review/{review_dir.name}/1982_RECIPE_YML_DRAFT_REPORT.md`
- JSON: `review/{review_dir.name}/1982_recipe_yml_draft_v1.json`
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

readme_old = readme_path.read_text(encoding="utf-8")
readme_marker = "<!-- DC_1982_RECIPE_YML_DRAFT_V1 -->"
readme_block = f"""
{readme_marker}

## 1982 recipe.yml draft v1

Status: **{draft_status}**

Izrađen je radni `recipe.yml` za `Finocchiona Toscana IGP`, standardiziran na 10 kg, s obveznim rasponima začina prema službenom konsolidiranom disciplinaru iz 2024. Javni update ostaje blokiran do internal QA i privatnog preview koraka.
"""
if readme_marker not in readme_old:
    readme_path.write_text(readme_old.rstrip() + "\n\n" + readme_block.strip() + "\n", encoding="utf-8")

print("=== 1982 RECIPE.YML DRAFT COMPLETE ===")
print(f"DRAFT_STATUS={draft_status}")
print("POST_ID=1982")
print("RECIPE_CODE=IT-TOS-1982-FINOCCHIONA-TOSCANA")
print("BATCH_SIZE_KG=10")
print(f"RAW_MATERIAL_TOTAL_KG={total_kg}")
print("PRIMARY_SOURCE=SRC-1982-005")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"REPORT={report_path}")
print(f"RECIPE_YML={recipe_yml_path}")
