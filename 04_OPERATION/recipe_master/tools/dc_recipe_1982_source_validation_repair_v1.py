#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 4:
    print("Usage: dc_recipe_1982_source_validation_repair_v1.py DOSSIER_DIR SOURCE_DIR REPAIR_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
source_dir = Path(sys.argv[2])
repair_dir = Path(sys.argv[3])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
sources_path = dossier_dir / "sources.yml"

report_path = source_dir / "1982_SOURCE_VALIDATION_REPORT.md"
json_path = source_dir / "1982_source_validation_v1.json"
checks_path = source_dir / "1982_source_validation_checks.csv"
sources_snapshot_path = source_dir / "1982_sources_snapshot.yml"

repair_report_path = repair_dir / "1982_SOURCE_VALIDATION_REPAIR_REPORT.md"
repair_json_path = repair_dir / "1982_source_validation_repair_v1.json"

now = datetime.now(timezone.utc).isoformat()

official_sources = [
    {
        "id": "SRC-1982-001",
        "type": "official_specification",
        "authority": "Ministero delle politiche agricole alimentari e forestali / Finocchiona IGP disciplinary specification",
        "url": "https://www.masaf.gov.it/flex/files/0/7/c/D.b4191a7a18c0f1afe6e9/Finocchiona___pubblicazione_GU.pdf",
        "reliability": "high",
        "supports": [
            "PGI product identity",
            "raw material categories",
            "allowed meat cuts",
            "mandatory ingredients and ranges per 100 kg mixture",
            "optional additives, wine and starter cultures",
            "drying and ageing conditions",
            "minimum drying and ageing durations by stuffed weight"
        ]
    },
    {
        "id": "SRC-1982-002",
        "type": "official_eu_registration",
        "authority": "European Union / Commission Implementing Regulation (EU) 2015/629",
        "url": "https://www.legislation.gov.uk/eur/2015/629/data.xht?view=snippet&wrap=true",
        "reliability": "high",
        "supports": [
            "Finocchiona name registered as PGI",
            "product class meat products"
        ]
    },
    {
        "id": "SRC-1982-003",
        "type": "consortium_specification_page",
        "authority": "Consorzio di tutela della Finocchiona IGP",
        "url": "https://www.finocchionaigp.it/en/specification/",
        "reliability": "high",
        "supports": [
            "PGI specification governs raw material origin, production recipe, processing and ripening",
            "control body and consortium oversight"
        ]
    },
    {
        "id": "SRC-1982-004",
        "type": "regional_product_sheet",
        "authority": "Regione Toscana product information",
        "url": "https://prodtrad.regione.toscana.it/LIB_DOPIGP/Prodotto.php?ID=38",
        "reliability": "medium_high",
        "supports": [
            "traditional Tuscan identity",
            "historical and sensory description"
        ]
    }
]

remaining_blockers = [
    "Postojeći WP recept nema još zaključan recipe.yml s provjerom svih obveznih polja.",
    "Nedostaju _dry_recipe_sections i _dry_verified_process za strukturirani radni prikaz.",
    "Potrebno je uskladiti recept na 10 kg prema službenim rasponima iz disciplinara.",
    "Ako se koriste nitriti/nitrati ili starter kulture, moraju biti jasno označeni kao opcionalni/dopušteni i tehnološki objašnjeni.",
    "Crijeva, granulacija i proces moraju biti popunjeni u Drycured standardu prije javnog updatea."
]

validation = {
    "generated_at": now,
    "post_id": 1982,
    "title": "FINOCCHIONA TOSCANA",
    "validation_status": "CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED",
    "canonical_project_status": "CONFIRMED_RECIPE",
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "recipe_yml_allowed_next": True,
    "private_clone_allowed_if_needed": True,
    "official_product_confirmed": True,
    "official_recipe_framework_confirmed": True,
    "exact_wp_recipe_quantities_confirmed": False,
    "why_not_public_ready_yet": remaining_blockers,
    "official_sources": official_sources,
    "source_locked_facts": {
        "product_type": "suha fermentirana/sušena kobasica u omotaču",
        "region": "Toskana / Italija",
        "protected_status": "IGP/PGI",
        "batch_basis": "10 kg",
        "mandatory_ingredients_from_spec_scaled_to_10kg": {
            "salt_g_range": "250-350 g",
            "ground_pepper_g_range": "5-10 g",
            "pepper_grain_or_broken_g_range": "15-40 g",
            "garlic_or_equivalent_dehydrated_g_range": "5-10 g",
            "fennel_seed_or_flower_g_range": "20-50 g"
        },
        "optional_from_spec_scaled_to_10kg": {
            "wine_max_l": "0.1 L",
            "sugars_max_g": "100 g",
            "sodium_ascorbate_max_g": "15 g",
            "starter_cultures": "allowed by specification",
            "nitrites_nitrates": "allowed by specification, but Drycured public recipe requires explicit safety note if used"
        },
        "process_parameters_from_spec": {
            "drying_temperature_c": "12-25 °C",
            "ageing_temperature_c": "11-18 °C",
            "ageing_relative_humidity": "65-90 %",
            "minimum_total_drying_ageing": [
                "0.5-1 kg stuffed weight: at least 15 days",
                "1-6 kg stuffed weight: at least 21 days",
                "6-25 kg stuffed weight: at least 45 days"
            ]
        }
    },
    "next_step": "RECIPE_YML_DRAFT_FROM_OFFICIAL_SPEC_AND_EXISTING_WP_CONTENT",
    "repair_note": "Corrected Markdown/report artifacts after a shell here-doc quoting error in the previous validation script.",
    "guardrails": [
        "No WordPress write in source validation.",
        "No public update.",
        "Do not overwrite source post 1982.",
        "Use official specification ranges when drafting recipe.yml.",
        "Keep public status blocked until recipe.yml and internal QA pass."
    ]
}

json_path.write_text(json.dumps(validation, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks = [
    {
        "key": "official_product_confirmed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Finocchiona is confirmed by official PGI/specification sources."
    },
    {
        "key": "official_recipe_framework_confirmed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Specification confirms ingredient and process framework."
    },
    {
        "key": "exact_wp_quantities_confirmed",
        "status": "FAIL",
        "severity": "MAJOR",
        "note": "Existing WP quantities still need canonical recipe.yml mapping."
    },
    {
        "key": "public_update_allowed",
        "status": "PASS",
        "severity": "BLOCKER",
        "note": "Public update remains false."
    },
    {
        "key": "recipe_yml_next_allowed",
        "status": "PASS",
        "severity": "MAJOR",
        "note": "Recipe.yml draft is the correct next step."
    }
]

with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

sources_lines = []
sources_lines.append("post_id: 1982")
sources_lines.append("title: FINOCCHIONA TOSCANA")
sources_lines.append("validation_status: CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
sources_lines.append("canonical_project_status: CONFIRMED_RECIPE")
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
    sources_lines.append("    supports:")
    for item in s["supports"]:
        sources_lines.append(f"      - {item}")
sources_lines.append("remaining_blockers:")
for b in remaining_blockers:
    sources_lines.append(f"  - {b}")
sources_lines.append("next_step: RECIPE_YML_DRAFT_FROM_OFFICIAL_SPEC_AND_EXISTING_WP_CONTENT")

sources_text = "\n".join(sources_lines) + "\n"
sources_path.write_text(sources_text, encoding="utf-8")
sources_snapshot_path.write_text(sources_text, encoding="utf-8")

md = []
md.append("# 1982 Finocchiona Toscana source validation v1")
md.append("")
md.append("Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Potvrđuje izvorni status recepta i definira smjer za `recipe.yml`.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Finocchiona Toscana je službeno potvrđen IGP/PGI proizvod s dostupnim službenim disciplinarom. Disciplinar daje dovoljno jak okvir za izradu Drycured radnog `recipe.yml` zapisa, ali postojeći WP recept se ne smije javno ažurirati dok se ne napravi i QA-provjeri strukturirani `recipe.yml`.")
md.append("")
md.append("## Statusi")
md.append("")
md.append("- Canonical project status: `CONFIRMED_RECIPE`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append("- Recipe.yml next allowed: `true`")
md.append("- Exact WP quantities confirmed: `false`")
md.append("")
md.append("## Službeno zaključane činjenice za 10 kg")
md.append("")
md.append("| Element | Raspon / status |")
md.append("|---|---|")
md.append("| Sol | 250–350 g / 10 kg |")
md.append("| Mljeveni papar | 5–10 g / 10 kg |")
md.append("| Papar u zrnu/lomljeni | 15–40 g / 10 kg |")
md.append("| Češnjak ili ekvivalent suhog češnjaka | 5–10 g / 10 kg |")
md.append("| Sjeme/cvijet komorača | 20–50 g / 10 kg |")
md.append("| Vino | do 0,1 L / 10 kg, opcionalno |")
md.append("| Šećeri | do 100 g / 10 kg, opcionalno |")
md.append("| Starter kulture | dopuštene, ali zahtijevaju tehnološku napomenu |")
md.append("| Nitriti/nitrati | dopušteni u specifikaciji, ali u Drycured receptu traže sigurnosnu napomenu ako se koriste |")
md.append("| Sušenje | 12–25 °C |")
md.append("| Zrenje | 11–18 °C, 65–90 % RH |")
md.append("| Minimalno trajanje | 15 / 21 / 45 dana prema težini pri punjenju |")
md.append("")
md.append("## Otvorene blokade prije javnog updatea")
md.append("")
for b in remaining_blockers:
    md.append(f"- {b}")
md.append("")
md.append("## Izvori")
md.append("")
for s in official_sources:
    md.append(f"- `{s['id']}` — {s['authority']} — {s['reliability']}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Izraditi `recipe.yml` iz službenog disciplinara i postojećeg WP sadržaja. Javni update ostaje zabranjen.")
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

qa_block = """
<!-- DC_1982_SOURCE_VALIDATION_V1 -->

## 1982 Finocchiona Toscana source validation v1

Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

- Canonical project status: `CONFIRMED_RECIPE`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Recipe.yml next allowed: `true`
- Exact WP quantities confirmed: `false`
- Repair status: `CORRECTED_SOURCE_VALIDATION_ARTIFACTS`
- Report: `review/{source_dir_name}/1982_SOURCE_VALIDATION_REPORT.md`
- JSON: `review/{source_dir_name}/1982_source_validation_v1.json`
""".replace("{source_dir_name}", source_dir.name)

replace_or_append_marked(qa_path, "<!-- DC_1982_SOURCE_VALIDATION_V1 -->", qa_block)

readme_text = readme_path.read_text(encoding="utf-8")
readme_marker = "<!-- DC_1982_SOURCE_VALIDATION_REPAIR_V1 -->"
readme_block = f"""
{readme_marker}

## 1982 source validation repair v1

Status: **CORRECTED_SOURCE_VALIDATION_ARTIFACTS**

Ispravljeni su dokumentacijski artefakti source validation koraka za `1982 — Finocchiona Toscana`. Validacijski status ostaje `CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED`, a javni update ostaje zabranjen dok se ne izradi i QA-provjeri `recipe.yml`.
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
    "cause": "Previous script used an unquoted shell here-doc section, so Markdown backticks were interpreted by Bash.",
    "next_step": "RECIPE_YML_DRAFT_FROM_OFFICIAL_SPEC_AND_EXISTING_WP_CONTENT"
}
repair_json_path.write_text(json.dumps(repair, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

repair_md = []
repair_md.append("# 1982 source validation repair v1")
repair_md.append("")
repair_md.append("Status: **CORRECTED_SOURCE_VALIDATION_ARTIFACTS**")
repair_md.append("")
repair_md.append("Ovaj repair ne mijenja WordPress. Ispravlja samo dokumentacijske artefakte nastale zbog shell quoting problema u prethodnoj skripti.")
repair_md.append("")
repair_md.append("## Ispravljeno")
repair_md.append("")
repair_md.append("- statusi u `1982_SOURCE_VALIDATION_REPORT.md`")
repair_md.append("- ID-jevi izvora u izvještaju")
repair_md.append("- tekst `recipe.yml` u izvještaju")
repair_md.append("- `sources.yml`")
repair_md.append("- `1982_source_validation_v1.json`")
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

print("=== 1982 SOURCE VALIDATION REPAIR COMPLETE ===")
print("REPAIR_STATUS=CORRECTED_SOURCE_VALIDATION_ARTIFACTS")
print("VALIDATION_STATUS=CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
print("CANONICAL_PROJECT_STATUS=CONFIRMED_RECIPE")
print("WORDPRESS_WRITE_ALLOWED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print("RECIPE_YML_NEXT_ALLOWED=true")
print(f"REPORT={report_path}")
print(f"REPAIR_REPORT={repair_report_path}")
