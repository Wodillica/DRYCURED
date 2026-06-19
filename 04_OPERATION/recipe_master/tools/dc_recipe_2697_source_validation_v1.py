#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 6:
    print("Usage: dc_recipe_2697_source_validation_v1.py QUICK_JSON DOSSIER_DIR QA README SOURCES", file=sys.stderr)
    sys.exit(1)

quick_json = Path(sys.argv[1])
dossier_dir = Path(sys.argv[2])
qa_path = Path(sys.argv[3])
readme_path = Path(sys.argv[4])
sources_path = Path(sys.argv[5])

out_dir = Path(__file__)  # placeholder only
quick = json.loads(quick_json.read_text(encoding="utf-8"))

now = datetime.now(timezone.utc).isoformat()

official_sources = [
    {
        "id": "SRC-2697-001",
        "type": "expert_article",
        "authority": "Agroklub / stručni opis regionalnih kobasica, citiran Mastanjević",
        "url": "https://www.agroklub.com/prehrambena-industrija/kobasica-gdje-je-glavna-paprika-a-gdje-cesnjak-i-biber/72662/",
        "reliability": "high_for_technology_medium_for_exact_formula",
        "supports": [
            "Slavonija i Baranja imaju karakterističnu slavonsku odnosno baranjsku kobasicu",
            "sirovine: nekoliko kategorija svinjskog mesa, plećka bez podlaktice, vrat, potrbušina i prsa",
            "više masnog tkiva nego kod kulena i kulenove seke",
            "začini: slatka i ljuta paprika, češnjak i sol",
            "punjenje u svinjska tanka crijeva",
            "paprika 1-2 % na masu mesa i slanine",
            "sol 1,8-2 %",
            "češnjak 0,2-0,3 %",
            "dimljenje svaki drugi dan, pet do šest dimova",
            "nakon dimljenja još oko 30 dana dozrijevanja, ovisno o klimi"
        ]
    },
    {
        "id": "SRC-2697-002",
        "type": "public_broadcast_article",
        "authority": "HRT / izjava predsjednika udruge proizvođača baranjskog kulena",
        "url": "https://magazin.hrt.hr/price-iz-hrvatske/odrzano-deveto-izdanje-s-klobasicom-u-europu--10670309",
        "reliability": "medium_high_for_identity",
        "supports": [
            "baranjska kobasica prepoznaje se po paprici, češnjaku i biberu",
            "regionalna usporedba s istarskom i slavonskom kobasicom"
        ]
    },
    {
        "id": "SRC-2697-003",
        "type": "public_recipe_archive",
        "authority": "Recepisi / javno objavljen recept za Baranjsku kobasicu",
        "url": "https://recepisi.wordpress.com/recepti-za-kobasice/",
        "reliability": "medium_for_exact_formula_low_for_official_status",
        "supports": [
            "konkretan recept za Baranjsku kobasicu",
            "10 kg svinjskog mesa + 1 kg tvrde slanine",
            "200 g soli, 30 g šećera, 50 g bibera, 150 g slatke paprike, 100 g ljute paprike, 50 g pastoznog češnjaka",
            "odležavanje mesa 24 sata",
            "mljevenje kroz rešetku 6 mm",
            "punjenje u tanka svinjska crijeva 35-40 cm",
            "dimljenje tjedan dana s 3-4 dima po 6 sati",
            "sazrijevanje 25-30 dana"
        ]
    }
]

remaining_blockers = [
    "Naziv javnog posta `Baranjska Ljuta Slavonska Kobasica` treba urednički uskladiti; kanonski naziv vjerojatno treba biti `Baranjska ljuta kobasica` ili `Baranjska kobasica – ljuta varijanta`.",
    "Izvori potvrđuju proizvod i radnu recepturu, ali ne daju službeni zaštićeni disciplinar kao kod IGP proizvoda.",
    "Prije javnog updatea treba izraditi `recipe.yml` na 10 kg smjese prema Drycured standardu.",
    "Treba jasno odlučiti računa li se šarža kao 10 kg ukupne mesne smjese ili izvornih 10 kg mesa + 1 kg slanine; Drycured standard mora prikazati 10 kg ukupne smjese.",
    "Treba definirati češnjak: koristi li se pastozni češnjak, macerat ili procijeđena tekućina.",
    "Treba definirati crijeva: tanka svinjska crijeva 35-40 cm, namakanje, voda, temperatura, vrijeme i neprokuhavanje.",
    "Treba definirati faze dimljenja i zrenja s radnim parametrima."
]

validation = {
    "generated_at": now,
    "post_id": 2697,
    "title": "Baranjska Ljuta Slavonska Kobasica",
    "validation_status": "CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED",
    "canonical_project_status": "CONFIRMED_RECIPE",
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "recipe_yml_allowed_next": True,
    "private_clone_allowed_if_needed": True,
    "official_product_confirmed": True,
    "public_recipe_framework_confirmed": True,
    "exact_wp_recipe_quantities_confirmed": False,
    "title_review_required": True,
    "recommended_canonical_title_hr": "Baranjska kobasica – ljuta varijanta",
    "recipe_type_router": "GROUND_MEAT_OR_CASING",
    "why_not_public_ready_yet": remaining_blockers,
    "sources": official_sources,
    "source_locked_facts": {
        "product_type": "suha/dimljena kobasica u tankom svinjskom crijevu",
        "region": "Baranja / Slavonija, Hrvatska",
        "protected_status": "no_confirmed_eu_protected_status_for_this_recipe",
        "batch_basis_required_by_project": "10 kg ukupne mesne smjese",
        "identity": [
            "paprika je glavni regionalni aromatski i vizualni potpis",
            "ljuta varijanta pojačava udio ljute paprike",
            "češanj/češnjak i biber/papar su važni pomoćni začini",
            "proizvod pripada skupini mljevenog mesa u omotaču"
        ],
        "source_formula_from_public_recipe_before_drycured_scaling": {
            "svinjsko_meso_kg": 10,
            "tvrda_slanina_kg": 1,
            "sol_g": 200,
            "secer_g": 30,
            "biber_papar_g": 50,
            "slatka_paprika_g": 150,
            "ljuta_paprika_g": 100,
            "pastozni_cesnjak_g": 50,
            "total_meat_fat_kg": 11
        },
        "drycured_scaling_note": "Ako se zadrži omjer iz javnog recepta, za 10 kg ukupne smjese faktor je 10/11 = 0,90909.",
        "source_process_facts": {
            "meat_resting": "24 sata prije mljevenja",
            "grinding_plate_mm": 6,
            "casing": "tanka svinjska crijeva, dužina 35-40 cm",
            "after_filling_rest": "24 sata",
            "smoking": "tjedan dana, 3-4 dima po 6 sati",
            "maturation": "25-30 dana",
            "storage": "suhe promajne prostorije"
        },
        "expert_technology_ranges": {
            "paprika_percent": "1-2 % na masu mesa i slanine",
            "salt_percent": "1,8-2 %",
            "garlic_percent": "0,2-0,3 %",
            "smoking_pattern": "svaki drugi dan, pet do šest dimova",
            "maturation_after_smoking": "oko 30 dana, ovisno o klimatskim uvjetima"
        }
    },
    "quick_intake_input": {
        "path": str(quick_json),
        "intake_status": quick.get("intake_status"),
        "post_status": quick.get("post", {}).get("post_status"),
        "http_code": quick.get("http", {}).get("http_code"),
        "missing_structured_meta": [
            "_dry_recipe_image_url",
            "_dry_recipe_sections",
            "_dry_verified_process"
        ]
    },
    "next_step": "RECIPE_YML_DRAFT_FROM_PUBLIC_SOURCES_AND_EXISTING_WP_CONTENT",
    "guardrails": [
        "No WordPress write in source validation.",
        "No public update.",
        "Do not overwrite source post 2697.",
        "Use Baranja/Slavonia source facts, not foreign sausage templates.",
        "Normalize to 10 kg total meat/fat mixture for Drycured.",
        "Keep public status blocked until recipe.yml, internal QA and private preview pass."
    ]
}
out_dir = Path("/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/dossiers/hr_strict_batch_01_2026-06-19_18-09-29/2697_baranjska-ljuta-slavonska-kobasica/review/source_validation_v1_2026-06-19_18-29-44")
out_dir.mkdir(parents=True, exist_ok=True)

report_path = out_dir / "2697_SOURCE_VALIDATION_REPORT.md"
json_path = out_dir / "2697_source_validation_v1.json"
sources_snapshot_path = out_dir / "2697_sources_snapshot.yml"
checks_path = out_dir / "2697_source_validation_checks.csv"

json_path.write_text(json.dumps(validation, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks = [
    ("product_identity_confirmed", "PASS", "BLOCKER", "Baranjska/slavonska kobasica identity is supported by public sources."),
    ("recipe_framework_confirmed", "PASS", "BLOCKER", "Public recipe source gives formula and process."),
    ("type_ground_casing", "PASS", "BLOCKER", "Recipe belongs to GROUND_MEAT_OR_CASING."),
    ("public_update_allowed", "PASS", "BLOCKER", "Public update remains false."),
    ("title_review_required", "PASS", "MAJOR", "Current title should be editorially normalized before public update."),
    ("drycured_10kg_scaling_required", "PASS", "MAJOR", "Source formula is 11 kg meat/fat and must be scaled to 10 kg total mixture."),
    ("exact_wp_quantities_confirmed", "FAIL", "MAJOR", "Existing WP quantities still need recipe.yml mapping and QA."),
]

with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.writer(f)
    writer.writerow(["key", "status", "severity", "note"])
    writer.writerows(checks)

sources_lines = []
sources_lines.append("post_id: 2697")
sources_lines.append("title: Baranjska Ljuta Slavonska Kobasica")
sources_lines.append("validation_status: CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
sources_lines.append("canonical_project_status: CONFIRMED_RECIPE")
sources_lines.append("recommended_canonical_title_hr: Baranjska kobasica – ljuta varijanta")
sources_lines.append("recipe_type_router: GROUND_MEAT_OR_CASING")
sources_lines.append("public_update_allowed: false")
sources_lines.append("public_publish_allowed: false")
sources_lines.append("source_post_write_allowed: false")
sources_lines.append("recipe_yml_allowed_next: true")
sources_lines.append("title_review_required: true")
sources_lines.append("sources:")
for s in official_sources:
    sources_lines.append(f"  - id: {s['id']}")
    sources_lines.append(f"    type: {s['type']}")
    sources_lines.append(f"    authority: {s['authority']}")
    sources_lines.append(f"    url: {s['url']}")
    sources_lines.append(f"    reliability: {s['reliability']}")
    sources_lines.append("    supports:")
    for item in s["supports"]:
        sources_lines.append(f"      - {item}")
sources_lines.append("remaining_blockers:")
for b in remaining_blockers:
    sources_lines.append(f"  - {b}")
sources_lines.append("next_step: RECIPE_YML_DRAFT_FROM_PUBLIC_SOURCES_AND_EXISTING_WP_CONTENT")

sources_text = "\n".join(sources_lines) + "\n"
sources_path.write_text(sources_text, encoding="utf-8")
sources_snapshot_path.write_text(sources_text, encoding="utf-8")

md = []
md.append("# 2697 Baranjska Ljuta Slavonska Kobasica source validation v1")
md.append("")
md.append("Status: **CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Potvrđuje da recept ima javne izvore dovoljne za radni , ali ne dopušta javni update.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Baranjska kobasica je potvrđena kao stvarni regionalni hrvatski proizvod iz skupine mljevenog mesa u ovitku. Javni izvori potvrđuju tehnološki okvir, začinski profil i jedan konkretan receptni zapis. Budući da nema službeni IGP/PGI disciplinar za ovaj konkretni recept, javni update ostaje blokiran dok se ne izradi , provede internal QA i napravi privatni preview.")
md.append("")
md.append("## Statusi")
md.append("")
md.append("- Canonical project status: ")
md.append("- Recipe type router: ")
md.append("- Recommended title: ")
md.append("- Public update allowed: ")
md.append("- Public publish allowed: ")
md.append("- Source post write allowed: ")
md.append("- Recipe.yml next allowed: ")
md.append("- Title review required: ")
md.append("- Exact WP quantities confirmed: ")
md.append("")
md.append("## Izvorno zaključane činjenice")
md.append("")
md.append("| Element | Izvorno potvrđeno |")
md.append("|---|---|")
md.append("| Tip proizvoda | mljevena suha/dimljena kobasica u tankom svinjskom crijevu |")
md.append("| Regija | Baranja / Slavonija |")
md.append("| Glavni začinski potpis | slatka i ljuta paprika, češnjak, papar/biber, sol |")
md.append("| Mljevenje | rešetka 6 mm u javnom receptnom zapisu |")
md.append("| Crijeva | tanka svinjska crijeva, 35–40 cm |")
md.append("| Dimljenje | 3–4 dima po 6 sati tijekom tjedan dana u receptnom zapisu; stručni opis navodi 5–6 dimova svaki drugi dan |")
md.append("| Zrenje | 25–30 dana ili oko 30 dana prema javnim izvorima |")
md.append("")
md.append("## Izvorna formula prije Drycured skaliranja")
md.append("")
md.append("| Sastojak | Izvorna količina |")
md.append("|---|---:|")
md.append("| svinjsko meso | 10 kg |")
md.append("| tvrda slanina | 1 kg |")
md.append("| sol | 200 g |")
md.append("| šećer | 30 g |")
md.append("| biber/papar | 50 g |")
md.append("| slatka paprika | 150 g |")
md.append("| ljuta paprika | 100 g |")
md.append("| pastozni češnjak | 50 g |")
md.append("")
md.append("## Drycured odluka prije ")
md.append("")
md.append("Drycured standard traži 10 kg ukupne mesne smjese. Budući da javni recept ima 10 kg mesa + 1 kg slanine, omjer treba skalirati faktorom  ili urednički oblikovati kao 10 kg ukupne smjese uz očuvanje omjera mesa i tvrde slanine.")
md.append("")
md.append("## Otvorene blokade prije javnog updatea")
md.append("")
for b in remaining_blockers:
    md.append(f"- {b}")
md.append("")
md.append("## Izvori")
md.append("")
for s in official_sources:
    md.append(f"-  — {s['authority']} — {s['reliability']}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Izraditi  na 10 kg ukupne smjese, uz jasno definiranje rešetke 6 mm, tvrdog masnog tkiva, tankih svinjskih crijeva, češnjaka i ciklusa dimljenja/zrenja. Javni update ostaje zabranjen.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def append_once(path: Path, marker: str, block: str):
    old = path.read_text(encoding="utf-8")
    if marker not in old:
        path.write_text(old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

qa_block = f"""
<!-- DC_2697_SOURCE_VALIDATION_V1 -->

## 2697 Baranjska Ljuta Slavonska Kobasica source validation v1

Status: **CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

- Canonical project status: 
- Recipe type router: 
- Recommended title: 
- Public update allowed: 
- Public publish allowed: 
- Source post write allowed: 
- Recipe.yml next allowed: 
- Title review required: 
- Exact WP quantities confirmed: 
- Report: 
- JSON: 
"""
append_once(qa_path, "<!-- DC_2697_SOURCE_VALIDATION_V1 -->", qa_block)

readme_block = f"""
<!-- DC_2697_SOURCE_VALIDATION_V1 -->

## 2697 source validation v1

Status: **CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

Recept je potvrđen kao stvarna Baranjska kobasica / ljuta varijanta, ali javni update ostaje blokiran dok se ne izradi , internal QA i privatni preview.
"""
append_once(readme_path, "<!-- DC_2697_SOURCE_VALIDATION_V1 -->", readme_block)

print("=== 2697 SOURCE VALIDATION COMPLETE ===")
print("VALIDATION_STATUS=CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
print("CANONICAL_PROJECT_STATUS=CONFIRMED_RECIPE")
print("RECIPE_TYPE_ROUTER=GROUND_MEAT_OR_CASING")
print("RECOMMENDED_TITLE=Baranjska kobasica – ljuta varijanta")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print("RECIPE_YML_NEXT_ALLOWED=true")
print("TITLE_REVIEW_REQUIRED=true")
print("EXACT_WP_QUANTITIES_CONFIRMED=false")
print(f"REPORT={report_path}")
