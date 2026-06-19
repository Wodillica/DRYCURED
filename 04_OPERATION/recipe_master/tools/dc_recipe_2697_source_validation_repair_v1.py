#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 4:
    print("Usage: dc_recipe_2697_source_validation_repair_v1.py DOSSIER_DIR SOURCE_DIR REPAIR_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
source_dir = Path(sys.argv[2])
repair_dir = Path(sys.argv[3])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"

report_path = source_dir / "2697_SOURCE_VALIDATION_REPORT.md"
json_path = source_dir / "2697_source_validation_v1.json"
checks_path = source_dir / "2697_source_validation_checks.csv"
sources_snapshot_path = source_dir / "2697_sources_snapshot.yml"

repair_report_path = repair_dir / "2697_SOURCE_VALIDATION_REPAIR_REPORT.md"
repair_json_path = repair_dir / "2697_source_validation_repair_v1.json"

now = datetime.now(timezone.utc).isoformat()

public_sources = [
    {
        "id": "SRC-2697-001",
        "type": "expert_article",
        "authority": "Agroklub / stručni opis regionalnih kobasica, citiran Mastanjević",
        "url": "https://www.agroklub.com/prehrambena-industrija/kobasica-gdje-je-glavna-paprika-a-gdje-cesnjak-i-biber/72662/",
        "reliability": "high_for_technology_medium_for_exact_formula",
        "supports": [
            "Slavonija i Baranja imaju karakterističnu slavonsku odnosno baranjsku kobasicu",
            "sirovine uključuju plećku, vrat, potrbušinu i prsa",
            "u kobasicama je više masnog tkiva nego kod kulena i kulenove seke",
            "začinski profil uključuje slatku i ljutu papriku, češnjak i sol",
            "punjenje se radi u svinjska tanka crijeva",
            "paprika 1-2 % na masu mesa i slanine",
            "sol 1,8-2 %",
            "češnjak 0,2-0,3 %",
            "dimljenje svaki drugi dan, pet do šest dimova",
            "nakon dimljenja još oko 30 dana dozrijevanja, ovisno o klimatskim uvjetima"
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
    "Naziv javnog posta `Baranjska Ljuta Slavonska Kobasica` treba urednički uskladiti; kanonski naziv za radni dosje je `Baranjska kobasica – ljuta varijanta`.",
    "Izvori potvrđuju proizvod i radnu recepturu, ali ne daju službeni zaštićeni disciplinar kao kod IGP proizvoda.",
    "Prije javnog updatea treba izraditi `recipe.yml` na 10 kg ukupne smjese prema Drycured standardu.",
    "Treba jasno riješiti skaliranje jer javni recept daje 10 kg mesa + 1 kg tvrde slanine, dok Drycured standard traži 10 kg ukupne mesne smjese.",
    "Treba definirati češnjak: pastozni češnjak, macerat ili procijeđena tekućina.",
    "Treba definirati crijeva: tanka svinjska crijeva 35-40 cm, namakanje, voda, temperatura, vrijeme i neprokuhavanje.",
    "Treba definirati faze dimljenja i zrenja s radnim parametrima."
]

validation = {
    "generated_at": now,
    "post_id": 2697,
    "title": "Baranjska Ljuta Slavonska Kobasica",
    "validation_status": "CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED",
    "canonical_project_status": "CONFIRMED_RECIPE",
    "recipe_type_router": "GROUND_MEAT_OR_CASING",
    "recommended_canonical_title_hr": "Baranjska kobasica – ljuta varijanta",
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "recipe_yml_allowed_next": True,
    "private_clone_allowed_if_needed": True,
    "official_product_confirmed": True,
    "public_recipe_framework_confirmed": True,
    "exact_wp_recipe_quantities_confirmed": False,
    "title_review_required": True,
    "why_not_public_ready_yet": remaining_blockers,
    "sources": public_sources,
    "source_locked_facts": {
        "product_type": "suha/dimljena kobasica u tankom svinjskom crijevu",
        "region": "Baranja / Slavonija, Hrvatska",
        "protected_status": "no_confirmed_eu_protected_status_for_this_recipe",
        "batch_basis_required_by_project": "10 kg ukupne mesne smjese",
        "identity": [
            "paprika je glavni regionalni aromatski i vizualni potpis",
            "ljuta varijanta pojačava udio ljute paprike",
            "češnjak i biber/papar su važni pomoćni začini",
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
    "next_step": "RECIPE_YML_DRAFT_FROM_PUBLIC_SOURCES_AND_EXISTING_WP_CONTENT",
    "repair_note": "Corrected Markdown/report artifacts after an unquoted shell here-doc allowed Bash to interpret Markdown backticks and braces.",
    "guardrails": [
        "No WordPress write in source validation.",
        "No public update.",
        "Do not overwrite source post 2697.",
        "Use Baranja/Slavonia source facts, not foreign sausage templates.",
        "Normalize to 10 kg total meat/fat mixture for Drycured.",
        "Keep public status blocked until recipe.yml, internal QA and private preview pass."
    ]
}

json_path.write_text(json.dumps(validation, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks = [
    {
        "key": "product_identity_confirmed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Baranjska/slavonska kobasica identity is supported by public sources."
    },
    {
        "key": "recipe_framework_confirmed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Public recipe source gives formula and process."
    },
    {
        "key": "type_ground_casing",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Recipe belongs to GROUND_MEAT_OR_CASING."
    },
    {
        "key": "public_update_allowed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Public update remains false."
    },
    {
        "key": "title_review_required",
        "status": "PASS",
        "severity": "MAJOR",
        "note": "Current title should be editorially normalized before public update."
    },
    {
        "key": "drycured_10kg_scaling_required",
        "status": "PASS",
        "severity": "MAJOR",
        "note": "Source formula is 11 kg meat/fat and must be scaled to 10 kg total mixture."
    },
    {
        "key": "exact_wp_quantities_confirmed",
        "status": "FAIL",
        "severity": "MAJOR",
        "note": "Existing WP quantities still need recipe.yml mapping and QA."
    }
]

with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
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
sources_lines.append("exact_wp_recipe_quantities_confirmed: false")
sources_lines.append("sources:")
for s in public_sources:
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
md.append("Ovaj korak ne mijenja WordPress. Potvrđuje da recept ima javne izvore dovoljne za radni `recipe.yml`, ali ne dopušta javni update.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Baranjska kobasica je potvrđena kao stvarni regionalni hrvatski proizvod iz skupine mljevenog mesa u ovitku. Javni izvori potvrđuju tehnološki okvir, začinski profil i jedan konkretan receptni zapis. Budući da nema službeni IGP/PGI disciplinar za ovaj konkretni recept, javni update ostaje blokiran dok se ne izradi `recipe.yml`, provede internal QA i napravi privatni preview.")
md.append("")
md.append("## Statusi")
md.append("")
md.append("- Canonical project status: `CONFIRMED_RECIPE`")
md.append("- Recipe type router: `GROUND_MEAT_OR_CASING`")
md.append("- Recommended title: `Baranjska kobasica – ljuta varijanta`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append("- Recipe.yml next allowed: `true`")
md.append("- Title review required: `true`")
md.append("- Exact WP quantities confirmed: `false`")
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
md.append("## Drycured odluka prije `recipe.yml`")
md.append("")
md.append("Drycured standard traži 10 kg ukupne mesne smjese. Budući da javni recept ima 10 kg mesa + 1 kg slanine, omjer treba skalirati faktorom `10/11 = 0,90909` ili urednički oblikovati kao 10 kg ukupne smjese uz očuvanje omjera mesa i tvrde slanine.")
md.append("")
md.append("## Otvorene blokade prije javnog updatea")
md.append("")
for b in remaining_blockers:
    md.append(f"- {b}")
md.append("")
md.append("## Izvori")
md.append("")
for s in public_sources:
    md.append(f"- `{s['id']}` — {s['authority']} — {s['reliability']}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Izraditi `recipe.yml` na 10 kg ukupne smjese, uz jasno definiranje rešetke 6 mm, tvrdog masnog tkiva, tankih svinjskih crijeva, češnjaka i ciklusa dimljenja/zrenja. Javni update ostaje zabranjen.")
md.append("")
md.append("## Repair napomena")
md.append("")
md.append("Ova verzija ispravlja dokumentacijski problem iz prethodnog generiranja izvještaja, gdje je shell pogrešno interpretirao Markdown backtickove. WordPress nije mijenjan.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def replace_or_append_marked(path: Path, marker: str, block: str) -> None:
    text = path.read_text(encoding="utf-8")
    block = block.strip() + "\n"
    start = text.find(marker)
    if start == -1:
        path.write_text(text.rstrip() + "\n\n" + block, encoding="utf-8")
        return
    next_marker = text.find("<!-- DC_", start + len(marker))
    if next_marker == -1:
        new_text = text[:start].rstrip() + "\n\n" + block
    else:
        new_text = text[:start].rstrip() + "\n\n" + block + "\n" + text[next_marker:].lstrip()
    path.write_text(new_text, encoding="utf-8")

qa_block = f"""
<!-- DC_2697_SOURCE_VALIDATION_V1 -->

## 2697 Baranjska Ljuta Slavonska Kobasica source validation v1

Status: **CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

- Canonical project status: `CONFIRMED_RECIPE`
- Recipe type router: `GROUND_MEAT_OR_CASING`
- Recommended title: `Baranjska kobasica – ljuta varijanta`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Recipe.yml next allowed: `true`
- Title review required: `true`
- Exact WP quantities confirmed: `false`
- Repair status: `CORRECTED_SOURCE_VALIDATION_ARTIFACTS`
- Report: `review/{source_dir.name}/2697_SOURCE_VALIDATION_REPORT.md`
- JSON: `review/{source_dir.name}/2697_source_validation_v1.json`
"""
replace_or_append_marked(qa_path, "<!-- DC_2697_SOURCE_VALIDATION_V1 -->", qa_block)

readme_text = readme_path.read_text(encoding="utf-8")
readme_marker = "<!-- DC_2697_SOURCE_VALIDATION_REPAIR_V1 -->"
readme_block = f"""
{readme_marker}

## 2697 source validation repair v1

Status: **CORRECTED_SOURCE_VALIDATION_ARTIFACTS**

Ispravljeni su dokumentacijski artefakti source validation koraka za `2697 — Baranjska Ljuta Slavonska Kobasica`. Validacijski status ostaje `CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED`, a javni update ostaje zabranjen dok se ne izradi i QA-provjeri `recipe.yml`.
"""
if readme_marker not in readme_text:
    readme_path.write_text(readme_text.rstrip() + "\n\n" + readme_block.strip() + "\n", encoding="utf-8")

repair = {
    "generated_at": now,
    "repair_status": "CORRECTED_SOURCE_VALIDATION_ARTIFACTS",
    "wordpress_write_allowed": False,
    "public_update_allowed": False,
    "source_post_write_allowed": False,
    "corrected_files": [
        str(report_path),
        str(json_path),
        str(checks_path),
        str(sources_snapshot_path),
        str(sources_path),
        str(qa_path),
        str(readme_path)
    ],
    "cause": "Previous script used an unquoted shell here-doc section, so Markdown backticks and braces were interpreted by Bash.",
    "next_step": "RECIPE_YML_DRAFT_FROM_PUBLIC_SOURCES_AND_EXISTING_WP_CONTENT"
}
repair_json_path.write_text(json.dumps(repair, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

repair_md = []
repair_md.append("# 2697 source validation repair v1")
repair_md.append("")
repair_md.append("Status: **CORRECTED_SOURCE_VALIDATION_ARTIFACTS**")
repair_md.append("")
repair_md.append("Ovaj repair ne mijenja WordPress. Ispravlja samo dokumentacijske artefakte nastale zbog shell quoting problema u prethodnoj skripti.")
repair_md.append("")
repair_md.append("## Ispravljeno")
repair_md.append("")
repair_md.append("- statusi u `2697_SOURCE_VALIDATION_REPORT.md`")
repair_md.append("- ID-jevi izvora u izvještaju")
repair_md.append("- tekst `recipe.yml` u izvještaju")
repair_md.append("- faktor skaliranja `10/11 = 0,90909`")
repair_md.append("- `sources.yml`")
repair_md.append("- `2697_source_validation_v1.json`")
repair_md.append("- `qa_report.md` blok za source validation")
repair_md.append("")
repair_md.append("## Sigurnosna odluka")
repair_md.append("")
repair_md.append("- WordPress write allowed: `false`")
repair_md.append("- Public update allowed: `false`")
repair_md.append("- Source post write allowed: `false`")
repair_md.append("- Sljedeći korak: `recipe.yml` draft")
repair_md.append("")
repair_report_path.write_text("\n".join(repair_md), encoding="utf-8")

print("=== 2697 SOURCE VALIDATION REPAIR COMPLETE ===")
print("REPAIR_STATUS=CORRECTED_SOURCE_VALIDATION_ARTIFACTS")
print("VALIDATION_STATUS=CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
print("CANONICAL_PROJECT_STATUS=CONFIRMED_RECIPE")
print("RECIPE_TYPE_ROUTER=GROUND_MEAT_OR_CASING")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print("RECIPE_YML_NEXT_ALLOWED=true")
print(f"REPORT={report_path}")
print(f"REPAIR_REPORT={repair_report_path}")
