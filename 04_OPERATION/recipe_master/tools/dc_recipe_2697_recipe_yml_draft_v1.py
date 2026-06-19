#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_2697_recipe_yml_draft_v1.py DOSSIER_DIR SOURCE_JSON REVIEW_DIR RECIPE_YML", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
source_json = Path(sys.argv[2])
review_dir = Path(sys.argv[3])
recipe_yml_path = Path(sys.argv[4])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"

source_validation = json.loads(source_json.read_text(encoding="utf-8"))
now = datetime.now(timezone.utc).isoformat()

# Drycured standard: 10 kg ukupne mesne smjese.
# Izvorni javni recept: 10 kg mesa + 1 kg tvrde slanine = 11 kg.
# Omjer se skalira na 10 kg: faktor 10/11 = 0,90909.
scale_factor = 10 / 11

source_scaled_reference = {
    "scale_factor": round(scale_factor, 5),
    "source_total_meat_fat_kg": 11,
    "drycured_target_total_kg": 10,
    "svinjsko_meso_kg_scaled": round(10 * scale_factor, 3),
    "tvrda_slanina_kg_scaled": round(1 * scale_factor, 3),
    "sol_g_scaled": round(200 * scale_factor, 1),
    "secer_g_scaled": round(30 * scale_factor, 1),
    "biber_papar_g_scaled": round(50 * scale_factor, 1),
    "slatka_paprika_g_scaled": round(150 * scale_factor, 1),
    "ljuta_paprika_g_scaled": round(100 * scale_factor, 1),
    "pastozni_cesnjak_g_scaled": round(50 * scale_factor, 1)
}

recipe = {
    "schema_version": "drycured_recipe_yml_v1",
    "generated_at": now,
    "post_id": 2697,
    "based_on_source_validation": str(source_json),
    "recipe_code": "HR-BR-2697-BARANJSKA-LJUTA-KOBASICA",
    "title_hr": "Baranjska kobasica – ljuta varijanta",
    "original_wp_title": "Baranjska Ljuta Slavonska Kobasica",
    "title_review_required": True,
    "country": "Hrvatska",
    "region": "Baranja / Slavonija",
    "protected_status": None,
    "canonical_project_status": "CONFIRMED_RECIPE",
    "recipe_status": "CANON_DRAFT_V1_PUBLIC_UPDATE_BLOCKED",
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "recipe_type_router": "GROUND_MEAT_OR_CASING",
    "batch_size_kg": 10.0,
    "batch_basis": "10 kg ukupne mesne smjese, uključujući meso i tvrdu slaninu",
    "identity": {
        "short_description": "Baranjska ljuta kobasica je dimljena i sušena kobasica u tankom svinjskom crijevu, s naglašenom paprikom, češnjakom i paprom.",
        "regional_signature": [
            "paprika kao glavni aromatski i vizualni potpis",
            "ljuta paprika kao obilježje ljute varijante",
            "češnjak i papar/biber kao pomoćni začinski nositelji",
            "dimljenje i zrenje u slavonsko-baranjskom stilu"
        ],
        "not_public_claims": [
            "ne tvrditi EU zaštićeni status",
            "ne tvrditi da je ovo jedina izvorna receptura",
            "ne prikazivati kao službeni disciplinar"
        ]
    },
    "source_policy": {
        "primary_sources": ["SRC-2697-001", "SRC-2697-002", "SRC-2697-003"],
        "formula_basis": "radna Drycured formulacija iz javnih izvora, skalirana i tehnološki uravnotežena na 10 kg ukupne smjese",
        "source_scaled_reference": source_scaled_reference,
        "public_update_allowed": False
    },
    "raw_materials": {
        "working_formula_10kg": [
            {
                "name": "svinjsko meso za kobasice: plećka, vrat, potrbušina/prsa bez kožica i žilavih dijelova",
                "kg": 9.09,
                "source_status": "scaled_from_public_recipe_and_supported_by_expert_description"
            },
            {
                "name": "tvrda leđna slanina ili čvrsta bijela slanina, dobro ohlađena",
                "kg": 0.91,
                "source_status": "scaled_from_public_recipe"
            }
        ],
        "raw_material_total_kg": 10.0,
        "fat_handling_summary": "Koristi se samo čvrsta, hladna slanina. Mekana, maziva ili topla masnoća se uklanja jer razmazuje presjek i zatvara teksturu kobasice.",
        "source_note": "Javni recept daje 10 kg mesa + 1 kg tvrde slanine. Drycured radna verzija zadržava omjer, ali ga skalira na 10 kg ukupne smjese."
    },
    "ingredients_10kg": {
        "working_formula": [
            {
                "name": "sol",
                "chosen_g": 190,
                "g_per_kg": 19.0,
                "source_relation": "unutar stručnog raspona 1,8-2 %"
            },
            {
                "name": "šećer",
                "chosen_g": 25,
                "g_per_kg": 2.5,
                "source_relation": "blisko skaliranom javnom receptu; pomaže početku fermentacije i zaokružuje ljutinu"
            },
            {
                "name": "crni papar / biber, mljeven ili grublje lomljen",
                "chosen_g": 45,
                "g_per_kg": 4.5,
                "source_relation": "blisko skaliranom javnom receptu"
            },
            {
                "name": "slatka mljevena paprika",
                "chosen_g": 120,
                "g_per_kg": 12.0,
                "source_relation": "radno uravnoteženje unutar ukupnog papričnog profila"
            },
            {
                "name": "ljuta mljevena paprika",
                "chosen_g": 70,
                "g_per_kg": 7.0,
                "source_relation": "ljuta varijanta; ukupna paprika 190 g / 10 kg"
            },
            {
                "name": "pastozni češnjak",
                "chosen_g": 28,
                "g_per_kg": 2.8,
                "source_relation": "usklađeno sa stručnim rasponom 0,2-0,3 % češnjaka"
            }
        ],
        "total_paprika_g": 190,
        "paprika_percent": 1.9,
        "salt_percent": 1.9,
        "garlic_percent": 0.28,
        "nitrite_nitrate_policy": {
            "used": False,
            "note": "Nitritna sol, nitriti i nitrati nisu uključeni u ovaj bazni draft. Ako se kasnije uključe, recept mora dobiti zasebnu sigurnosnu napomenu i precizno doziranje."
        }
    },
    "garlic_policy": {
        "mode": "direct_garlic_paste",
        "paste_garlic_used": True,
        "garlic_liquid_used": False,
        "chosen_g_per_10kg": 28,
        "preparation": "Češnjak se koristi kao pasta ili vrlo fino zgnječeni češnjak. Dodaje se izravno u smjesu tijekom miješanja.",
        "soaking_liquid": "none",
        "soaking_time_minutes": 0,
        "boiled": False,
        "cooled": False,
        "note": "Ne koristi se tekućina od češnjaka. Ako se u budućoj varijanti koristi procijeđena tekućina, mora se posebno navesti voda, količina, vrijeme, prokuhavanje/hlađenje i procjeđivanje."
    },
    "grinding_and_fat_handling": {
        "pre_cut_size_mm": "20-30",
        "chosen_plate_mm": 6,
        "meat_temperature_c": "0-4",
        "mix_temperature_max_c": 8,
        "fat_handling": "Tvrdu slaninu držati vrlo hladnom, po potrebi kratko pothladiti. Ako se mast lijepi za nož ili puž mašine, rad se prekida i sirovina se ponovno hladi.",
        "texture_goal": "srednje fina baranjska kobasičarska granulacija; paprika ravnomjerno boji masu, a mast ostaje vidljiva i nerazmazana"
    },
    "casing_and_filling": {
        "casing_type": "tanka svinjska crijeva",
        "source_length_guidance_cm": "35-40",
        "working_caliber_guidance": "tanka svinjska crijeva za domaću kobasicu; kalibar ovisi o dobavljaču, obično oko 28-34 mm",
        "soaking": {
            "required": True,
            "liquid": "pitka voda",
            "temperature_c": "20-25",
            "time_minutes": "30-45",
            "boiled": False,
            "rinsing": "isprati izvana i iznutra prije punjenja",
            "additives": "bez octa, soli ili drugih dodataka osim ako dobavljač crijeva izričito traži"
        },
        "filling": "Puniti čvrsto, ali ne pretvrdo. Oblikovati komade 35-40 cm, istisnuti zračne džepove i po potrebi probosti sterilnom iglom."
    },
    "process": [
        {
            "step": 1,
            "name": "Odabir, čišćenje i hlađenje sirovine",
            "parameters": "meso i slanina 0-4 °C",
            "action": "Odstraniti žile, hrskavice, krvave dijelove, mekanu mast i kožice. Pripremiti 9,09 kg mesa i 0,91 kg tvrde slanine.",
            "critical_control": "Sirovina mora biti hladna, svježa i bez neugodnog mirisa."
        },
        {
            "step": 2,
            "name": "Rezanje i kratko odležavanje",
            "parameters": "komadi 20-30 mm; 12-24 h na 0-4 °C",
            "action": "Meso i slaninu narezati, posoliti dijelom soli ili držati pripremljeno za mljevenje prema radnoj praksi.",
            "critical_control": "Ne ostavljati sirovinu na sobnoj temperaturi; posude držati pokrivene i čiste."
        },
        {
            "step": 3,
            "name": "Mljevenje",
            "parameters": "rešetka 6 mm; masa najviše 8 °C",
            "action": "Meso i tvrdu slaninu samljeti kroz rešetku 6 mm.",
            "critical_control": "Ako se mast razmazuje, zaustaviti mljevenje, ohladiti sirovinu i opremu pa nastaviti."
        },
        {
            "step": 4,
            "name": "Miješanje začina",
            "parameters": "190 g soli, 25 g šećera, 45 g papra, 120 g slatke paprike, 70 g ljute paprike, 28 g pastoznog češnjaka",
            "action": "Začine ravnomjerno rasporediti i miješati dok smjesa ne postane povezana, ali ne predugo da se mast ne razmaže.",
            "critical_control": "Paprika mora ravnomjerno obojiti smjesu; masa mora ostati hladna."
        },
        {
            "step": 5,
            "name": "Priprema crijeva",
            "parameters": "pitka voda 20-25 °C; 30-45 min; bez prokuhavanja",
            "action": "Tanka svinjska crijeva namočiti, isprati izvana i iznutra i ocijediti prije punjenja.",
            "critical_control": "Crijeva ne prokuhavati; odbaciti oštećene ili neugodno mirisne dijelove."
        },
        {
            "step": 6,
            "name": "Punjenje i vezanje",
            "parameters": "komadi 35-40 cm",
            "action": "Puniti u tanka svinjska crijeva, oblikovati kobasice, vezati i ukloniti zračne džepove.",
            "critical_control": "Punjenje mora biti čvrsto bez velikih zračnih šupljina i bez pucanja crijeva."
        },
        {
            "step": 7,
            "name": "Odmor nakon punjenja",
            "parameters": "oko 24 h; hladno i prozračno",
            "action": "Kobasice objesiti da se površina osuši prije dimljenja.",
            "critical_control": "Površina mora biti suha na dodir; ne dimiti mokru kobasicu."
        },
        {
            "step": 8,
            "name": "Hladno dimljenje",
            "parameters": "3-4 dima po oko 6 h tijekom tjedan dana; alternativno 5-6 laganih dimova svaki drugi dan",
            "action": "Dimiti hladnim dimom, s pauzama između dimova. Koristiti čist, tanak plavi dim.",
            "critical_control": "Dim ne smije biti vruć, gust ni čađav. Ako se pojavi gorčina ili vlažna ljepljiva površina, produžiti provjetravanje i smanjiti intenzitet dima."
        },
        {
            "step": 9,
            "name": "Sušenje i zrenje",
            "parameters": "25-30 dana; hladno, prozračno, bez izravnog propuha",
            "action": "Nakon dimljenja kobasice dozrijevaju dok ne postanu stabilne, povezane i prikladne za rezanje.",
            "critical_control": "Ako se rub prebrzo suši, smanjiti propuh i povisiti vlagu prostora. Ako je površina sluzava ili miris neugodan, proizvod ne kušati."
        },
        {
            "step": 10,
            "name": "Završna provjera i čuvanje",
            "parameters": "nakon 25-30 dana ili kada je presjek stabilan",
            "action": "Provjeriti miris, presjek, boju paprike, čvrstoću i odsutnost kvarenja.",
            "critical_control": "Sumnjiv proizvod ne kušati i ne posluživati."
        }
    ],
    "done_when": [
        "kobasica je čvrsta, ali ne kameno tvrda",
        "presjek je jednoliko crvenkast od paprike",
        "mast je vidljiva i nerazmazana",
        "miris je ugodan, dimljen, papričan i blago češnjakast",
        "nema sluzi, ljepljive površine, napuhavanja ni truležnog mirisa",
        "nakon rezanja kriška drži oblik i ne raspada se",
        "zrenje je trajalo najmanje 25-30 dana u primjerenim uvjetima"
    ],
    "problems_and_solutions": [
        {
            "problem": "Razmazana mast u presjeku",
            "likely_cause": "Slanina ili meso bili su pretopli tijekom mljevenja ili miješanja.",
            "solution": "Zaustaviti rad, ohladiti sirovinu i dijelove mašine, nastaviti tek kada je masa ponovno hladna. Sljedeći put koristiti tvrđu slaninu."
        },
        {
            "problem": "Gorka, čađava površina",
            "likely_cause": "Dim je bio pregust, vruć ili je izgaralo vlažno/nečisto drvo.",
            "solution": "Prekinuti dimljenje, provjetravati kobasice i nastaviti samo tankim hladnim dimom. Ne koristiti smolasto ili vlažno drvo."
        },
        {
            "problem": "Presuh rub i mekana jezgra",
            "likely_cause": "Prejak propuh ili preniska vlaga u prostoru.",
            "solution": "Smanjiti propuh, stabilizirati vlagu i produžiti zrenje. Ne ubrzavati sušenje jakom ventilacijom."
        },
        {
            "problem": "Preoštra ljutina bez zaokruženog okusa",
            "likely_cause": "Previše ljute paprike ili prekratko zrenje.",
            "solution": "Ostaviti dulje zrenje ako nema znakova kvarenja. Sljedeći put smanjiti ljutu papriku i dio zamijeniti slatkom paprikom."
        },
        {
            "problem": "Kiseli miris ili neugodan okus",
            "likely_cause": "Previše šećera, previsoka temperatura, loša higijena ili nepravilan tijek fermentacije.",
            "solution": "Sumnjiv proizvod ne kušati i ne posluživati. Sljedeći put smanjiti temperaturu obrade i bolje kontrolirati higijenu."
        },
        {
            "problem": "Sluzava površina ili neobične plijesni",
            "likely_cause": "Previsoka vlaga, slab protok zraka ili kontaminacija prostora.",
            "solution": "Ako je miris neugodan ili sluz prodire u ovitak, proizvod odbaciti. Prostor očistiti, osušiti i provjetriti prije nove šarže."
        },
        {
            "problem": "Zračne šupljine",
            "likely_cause": "Nedovoljno čvrsto punjenje ili zrak u punilici.",
            "solution": "Manje džepove probosti sterilnom iglom odmah nakon punjenja. Kod velikih šupljina povećava se rizik kvarenja i proizvod treba pažljivo nadzirati."
        }
    ],
    "serving_and_storage": {
        "serving": "Rezati tanko i posluživati kao suhomesnatu kobasicu. Ne tretirati kao kobasicu za pečenje.",
        "storage": "Čuvati na hladnom, suhom i prozračnom mjestu. Nakon narezivanja zaštititi presjek od isušivanja.",
        "public_note": "Ako postoji sumnja u miris, boju, sluzavost ili napuhavanje, proizvod se ne kuša i ne poslužuje."
    },
    "active_blockers": [
        "internal QA recipe.yml još nije napravljen",
        "javni WordPress update nije dopušten",
        "treba generirati _dry_recipe_sections i _dry_verified_process",
        "treba napraviti privatni preview clone prije bilo kakvog javnog updatea",
        "naslov treba uredničku normalizaciju prije javne objave"
    ]
}

checks = []

def check(key, ok, severity, note):
    checks.append({
        "key": key,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

raw_total = round(sum(float(i["kg"]) for i in recipe["raw_materials"]["working_formula_10kg"]), 2)
ingredients = {i["name"]: i for i in recipe["ingredients_10kg"]["working_formula"]}

salt_g = ingredients["sol"]["chosen_g"]
paprika_total = recipe["ingredients_10kg"]["total_paprika_g"]
garlic_g = ingredients["pastozni češnjak"]["chosen_g"]
plate = recipe["grinding_and_fat_handling"]["chosen_plate_mm"]
process_names = " ".join(step["name"].lower() for step in recipe["process"])

check("raw_material_sum_10kg", raw_total == 10.0, "BLOCKER", f"Ukupno sirovina: {raw_total} kg.")
check("meat_fat_ratio_scaled", abs(recipe["raw_materials"]["working_formula_10kg"][0]["kg"] - 9.09) < 0.01 and abs(recipe["raw_materials"]["working_formula_10kg"][1]["kg"] - 0.91) < 0.01, "BLOCKER", "Omjer mesa i tvrde slanine skaliran je s 11 kg na 10 kg.")
check("salt_range", 180 <= salt_g <= 200, "BLOCKER", "Sol mora biti u radnom rasponu 1,8-2,0 %.")
check("paprika_range", 100 <= paprika_total <= 200, "MAJOR", "Ukupna paprika u ovom radnom draftu drži se do 2 %.")
check("garlic_range", 20 <= garlic_g <= 30, "MAJOR", "Češnjak je u rasponu 0,2-0,3 %.")
check("grinding_6mm", plate == 6, "BLOCKER", "Mljevenje mora biti 6 mm prema javnom receptnom zapisu.")
check("casing_soaking_complete", recipe["casing_and_filling"]["soaking"]["required"] is True and recipe["casing_and_filling"]["soaking"]["boiled"] is False, "MAJOR", "Crijeva imaju namakanje, temperaturu, vrijeme i neprokuhavanje.")
check("garlic_policy_complete", recipe["garlic_policy"]["garlic_liquid_used"] is False and recipe["garlic_policy"]["paste_garlic_used"] is True, "MAJOR", "Češnjak je jasno definiran kao pasta, bez tekućine od češnjaka.")
check("process_has_smoking", "dimljenje" in process_names, "BLOCKER", "Proces mora imati dimljenje.")
check("process_has_maturation", "zrenje" in process_names or "sušenje" in process_names, "BLOCKER", "Proces mora imati sušenje/zrenje.")
check("problem_solution_count", len(recipe["problems_and_solutions"]) >= 6, "MAJOR", "Problemi imaju konkretna rješenja.")
check("public_update_blocked", recipe["public_update_allowed"] is False, "BLOCKER", "Javni update ostaje blokiran.")
check("source_validation_confirmed", source_validation.get("canonical_project_status") == "CONFIRMED_RECIPE", "BLOCKER", "Source validation mora biti CONFIRMED_RECIPE.")

major_failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blocker_failures = [c for c in major_failures if c["severity"] == "BLOCKER"]

draft_status = "RECIPE_YML_DRAFT_READY_INTERNAL_QA_REQUIRED" if not blocker_failures and not major_failures else "RECIPE_YML_DRAFT_BLOCKED"

recipe["draft_status"] = draft_status
recipe["draft_checks"] = checks

recipe_yml_path.write_text(json.dumps(recipe, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

draft_json_path = review_dir / "2697_recipe_yml_draft_v1.json"
draft_json_path.write_text(json.dumps(recipe, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks_path = review_dir / "2697_recipe_yml_draft_checks.csv"
with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

report_path = review_dir / "2697_RECIPE_YML_DRAFT_REPORT.md"
md = []
md.append("# 2697 Baranjska kobasica – ljuta varijanta recipe.yml draft v1")
md.append("")
md.append(f"Status: **{draft_status}**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Izrađuje radni `recipe.yml` za prvi hrvatski recept iz strict reda čekanja.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("- Post ID: `2697`")
md.append("- Recipe code: `HR-BR-2697-BARANJSKA-LJUTA-KOBASICA`")
md.append("- Radni naslov: `Baranjska kobasica – ljuta varijanta`")
md.append("- Originalni WP naslov: `Baranjska Ljuta Slavonska Kobasica`")
md.append("- Batch: `10 kg ukupne mesne smjese`")
md.append("- Type router: `GROUND_MEAT_OR_CASING`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append(f"- Raw material total: `{raw_total} kg`")
md.append("- Blocker fail total: `" + str(len(blocker_failures)) + "`")
md.append("")
md.append("## Sirovine za 10 kg")
md.append("")
md.append("| Sirovina | kg | Napomena |")
md.append("|---|---:|---|")
for item in recipe["raw_materials"]["working_formula_10kg"]:
    md.append(f"| {item['name']} | {item['kg']} | {item['source_status']} |")
md.append("")
md.append("## Začini za 10 kg")
md.append("")
md.append("| Sastojak | g | g/kg | Napomena |")
md.append("|---|---:|---:|---|")
for item in recipe["ingredients_10kg"]["working_formula"]:
    md.append(f"| {item['name']} | {item['chosen_g']} | {item['g_per_kg']} | {item['source_relation']} |")
md.append("")
md.append("## Izvorni recept skaliran na 10 kg — referenca")
md.append("")
md.append("| Element | Vrijednost |")
md.append("|---|---:|")
for k, v in source_scaled_reference.items():
    md.append(f"| {k} | {v} |")
md.append("")
md.append("## Tehnološke odluke")
md.append("")
md.append("- Mljevenje: `6 mm`.")
md.append("- Češnjak: `28 g pastoznog češnjaka`, bez tekućine od češnjaka.")
md.append("- Crijeva: tanka svinjska crijeva; namakanje `30-45 min` u pitkoj vodi `20-25 °C`, bez prokuhavanja.")
md.append("- Dimljenje: `3-4 dima po oko 6 h tijekom tjedan dana`; dopuštena radna usporedba je `5-6 laganih dimova svaki drugi dan`.")
md.append("- Zrenje: `25-30 dana`, hladno i prozračno.")
md.append("- Nitritna sol: nije uključena u bazni draft.")
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
for item in recipe["active_blockers"]:
    md.append(f"- {item}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Pokrenuti internal QA za `recipe.yml`, zatim generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview. Javni update ostaje zabranjen.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def append_once(path: Path, marker: str, block: str):
    old = path.read_text(encoding="utf-8")
    if marker not in old:
        path.write_text(old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

qa_block = f"""
<!-- DC_2697_RECIPE_YML_DRAFT_V1 -->

## 2697 Baranjska kobasica – ljuta varijanta recipe.yml draft v1

Status: **{draft_status}**

- Recipe code: `HR-BR-2697-BARANJSKA-LJUTA-KOBASICA`
- Batch: `10 kg ukupne mesne smjese`
- Raw material total: `{raw_total} kg`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Blocker fail total: `{len(blocker_failures)}`
- Report: `review/{review_dir.name}/2697_RECIPE_YML_DRAFT_REPORT.md`
- JSON: `review/{review_dir.name}/2697_recipe_yml_draft_v1.json`
"""
append_once(qa_path, "<!-- DC_2697_RECIPE_YML_DRAFT_V1 -->", qa_block)

readme_block = f"""
<!-- DC_2697_RECIPE_YML_DRAFT_V1 -->

## 2697 recipe.yml draft v1

Status: **{draft_status}**

Izrađen je radni `recipe.yml` za `Baranjska kobasica – ljuta varijanta`, standardiziran na 10 kg ukupne mesne smjese. Javni update ostaje zabranjen.
"""
append_once(readme_path, "<!-- DC_2697_RECIPE_YML_DRAFT_V1 -->", readme_block)

print("=== 2697 RECIPE.YML DRAFT COMPLETE ===")
print(f"DRAFT_STATUS={draft_status}")
print("POST_ID=2697")
print("RECIPE_CODE=HR-BR-2697-BARANJSKA-LJUTA-KOBASICA")
print("BATCH_SIZE_KG=10")
print(f"RAW_MATERIAL_TOTAL_KG={raw_total}")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print(f"BLOCKER_FAIL_TOTAL={len(blocker_failures)}")
print(f"REPORT={report_path}")
print(f"RECIPE_YML={recipe_yml_path}")
