#!/usr/bin/env python3
from pathlib import Path
import json
import sys
from datetime import datetime, timezone

if len(sys.argv) != 6:
    print("Usage: dc_recipe_1982_source_validation_v1.py QUICK_JSON DOSSIER_DIR QA README SOURCES", file=sys.stderr)
    sys.exit(1)

quick_json = Path(sys.argv[1])
dossier_dir = Path(sys.argv[2])
qa_path = Path(sys.argv[3])
readme_path = Path(sys.argv[4])
sources_path = Path(sys.argv[5])

review_dir = Path(__file__).resolve()
quick = json.loads(quick_json.read_text(encoding="utf-8"))

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
            "minimum drying/ageing durations by stuffed weight"
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
    "why_not_public_ready_yet": [
        "Postojeći WP recept nema još zaključan recipe.yml s provjerom svih obveznih polja.",
        "Nedostaju _dry_recipe_sections i _dry_verified_process za strukturirani radni prikaz.",
        "Potrebno je uskladiti recept na 10 kg prema službenim rasponima iz disciplinara.",
        "Ako se koriste nitriti/nitrati ili starter kulture, moraju biti jasno označeni kao opcionalni/dopušteni i tehnološki objašnjeni.",
        "Crijeva, granulacija i proces moraju biti popunjeni u Drycured standardu prije javnog updatea."
    ],
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
    "next_step": "RECIPE_YML_DRAFT_FROM_OFFICIAL_SPEC_AND_EXISTING_WP_CONTENT",
    "guardrails": [
        "No WordPress write in source validation.",
        "No public update.",
        "Do not overwrite source post 1982.",
        "Use official specification ranges when drafting recipe.yml.",
        "Keep public status blocked until recipe.yml and internal QA pass."
    ]
}

review_out = dossier_dir / "review"
# actual review dir is injected by bash as cwd output path through environment-like file placement below
# This script writes to the latest source_validation_v1_* directory discovered from output path argument replacement in bash.
out_dir = Path("/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/dossiers/pilot_batch_01b_strict_ground_2026-06-18_18-04-36/1982_finocchiona-toscana/review/source_validation_v1_2026-06-19_17-08-56")
out_dir.mkdir(parents=True, exist_ok=True)

report_path = out_dir / "1982_SOURCE_VALIDATION_REPORT.md"
json_path = out_dir / "1982_source_validation_v1.json"
sources_snapshot_path = out_dir / "1982_sources_snapshot.yml"
checks_path = out_dir / "1982_source_validation_checks.csv"

json_path.write_text(json.dumps(validation, ensure_ascii=False, indent=2), encoding="utf-8")

checks = [
    ("official_product_confirmed", "PASS", "BLOCKER", "Finocchiona is confirmed by official PGI/specification sources."),
    ("official_recipe_framework_confirmed", "PASS", "BLOCKER", "Specification confirms ingredient and process framework."),
    ("exact_wp_quantities_confirmed", "FAIL", "MAJOR", "Existing WP quantities still need canonical recipe.yml mapping."),
    ("public_update_allowed", "PASS", "BLOCKER", "Public update remains false."),
    ("recipe_yml_next_allowed", "PASS", "MAJOR", "Recipe.yml draft is the correct next step."),
]

checks_path.write_text(
    "key,status,severity,note\n" + "\n".join([",".join([f'"{x}"' for x in row]) for row in checks]) + "\n",
    encoding="utf-8"
)

sources_lines = []
sources_lines.append("post_id: 1982")
sources_lines.append("title: FINOCCHIONA TOSCANA")
sources_lines.append("validation_status: CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
sources_lines.append("canonical_project_status: CONFIRMED_RECIPE")
sources_lines.append("public_update_allowed: false")
sources_lines.append("public_publish_allowed: false")
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
for b in validation["why_not_public_ready_yet"]:
    sources_lines.append(f"  - {b}")
sources_path.write_text("\n".join(sources_lines) + "\n", encoding="utf-8")
sources_snapshot_path.write_text("\n".join(sources_lines) + "\n", encoding="utf-8")

md = []
md.append("# 1982 Finocchiona Toscana source validation v1")
md.append("")
md.append("Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**")
md.append("")
md.append("Ovaj korak ne mijenja WordPress. Potvrđuje izvorni status recepta i definira smjer za recipe.yml.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Finocchiona Toscana je službeno potvrđen IGP/PGI proizvod s dostupnim službenim disciplinarom. Disciplinar daje dovoljno jak okvir za izradu Drycured radnog recipe.yml zapisa, ali postojeći WP recept se ne smije javno ažurirati dok se ne napravi i QA-provjeri strukturirani recipe.yml.")
md.append("")
md.append("## Statusi")
md.append("")
md.append("- Canonical project status: ")
md.append("- Public update allowed: ")
md.append("- Public publish allowed: ")
md.append("- Source post write allowed: ")
md.append("- Recipe.yml next allowed: ")
md.append("- Exact WP quantities confirmed: ")
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
for b in validation["why_not_public_ready_yet"]:
    md.append(f"- {b}")
md.append("")
md.append("## Izvori")
md.append("")
for s in official_sources:
    md.append(f"-  — {s['authority']} — {s['reliability']}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Izraditi  iz službenog disciplinara i postojećeg WP sadržaja. Javni update ostaje zabranjen.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

qa_old = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_1982_SOURCE_VALIDATION_V1 -->"
append = f"""
{marker}

## 1982 Finocchiona Toscana source validation v1

Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

- Canonical project status: 
- Public update allowed: 
- Public publish allowed: 
- Source post write allowed: 
- Recipe.yml next allowed: 
- Exact WP quantities confirmed: 
- Report: 
- JSON: 
"""
if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

readme_old = readme_path.read_text(encoding="utf-8")
if "1982 source validation v1" not in readme_old:
    readme_path.write_text(readme_old.rstrip() + """

## 1982 source validation v1

Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

Finocchiona Toscana ima službeni IGP/PGI disciplinar i može ići u izradu strukturiranog . Javni update ostaje blokiran dok se ne završi recipe.yml i QA.
""" + "\n", encoding="utf-8")

print("=== 1982 SOURCE VALIDATION COMPLETE ===")
print("VALIDATION_STATUS=CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED")
print("CANONICAL_PROJECT_STATUS=CONFIRMED_RECIPE")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print("RECIPE_YML_NEXT_ALLOWED=true")
print("EXACT_WP_QUANTITIES_CONFIRMED=false")
print(f"REPORT={report_path}")
