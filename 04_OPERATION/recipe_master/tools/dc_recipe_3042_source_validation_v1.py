#!/usr/bin/env python3
from pathlib import Path
import json
import sys
from datetime import datetime, timezone

if len(sys.argv) != 4:
    print("Usage: dc_recipe_3042_source_validation_v1.py DOSSIER_DIR SOURCES_YML QA_REPORT", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
sources_path = Path(sys.argv[2])
qa_path = Path(sys.argv[3])

review_dirs = sorted((dossier_dir / "review").glob("source_validation_v1_*"))
review_dir = review_dirs[-1] if review_dirs else dossier_dir / "review" / "source_validation_v1_manual"
review_dir.mkdir(parents=True, exist_ok=True)

now = datetime.now(timezone.utc).isoformat()

sources = [
    {
        "id": "SRC-3042-001",
        "title": "BOCCRF / DGCCRF — rosette et jésus de Lyon, cahier des charges label rouge",
        "url": "https://www.economie.gouv.fr/files/files/directions_services/dgccrf/boccrf/99_19/a0190013.htm",
        "language": "fr",
        "source_type": "official_regulatory_archive",
        "reliability": "high",
        "supports": [
            "Nazivi rosette de Lyon i jésus de Lyon postoje u službenom francuskom regulatornom kontekstu.",
            "Dokument govori o zaštiti naziva i tradicije proizvoda od pojednostavljenih recepata.",
            "Koristan je za potvrdu proizvoda i regionalnog identiteta, ali ne daje potpun kućni recept za naš prikaz."
        ],
        "limits": [
            "Ne smije se doslovno prepisivati.",
            "Ne potvrđuje automatski sve količine iz postojećeg WP recepta.",
            "Ne smije se iz njega tvrditi aktualni IGP/ZOI status bez dodatne provjere."
        ],
        "decision": "PRODUCT_CONFIRMED_SOURCE"
    },
    {
        "id": "SRC-3042-002",
        "title": "BOCCRF / DGCCRF — indication géographique, rosette et jésus de Lyon, opis proizvoda",
        "url": "https://www.economie.gouv.fr/files/files/directions_services/dgccrf/boccrf/99_19/a0190012.htm",
        "language": "fr",
        "source_type": "official_regulatory_archive",
        "reliability": "high",
        "supports": [
            "Rosette i jésus de Lyon opisani su kao suhomesnati proizvodi iz obitelji velikih komada.",
            "Smjesa se temelji na nemasnom svinjskom mesu ili mesu krmače i tvrdom svinjskom masnom tkivu.",
            "Nadijevanje ide u prirodni ovitak/crijevo; kod jésusa se oblik crijeva razlikuje od rosette."
        ],
        "limits": [
            "Daje tehnološki opis, ne cjelovit recept za 10 kg.",
            "Ne potvrđuje začine iz našeg WP zapisa."
        ],
        "decision": "PRODUCT_AND_TECHNOLOGY_CONFIRMED_SOURCE"
    },
    {
        "id": "SRC-3042-003",
        "title": "INAO — IGP Rosette de Lyon ou Jésus de Lyon, povijesni status postupka",
        "url": "https://extranet.inao.gouv.fr/fichier/CNIGPLRSTG-2010-229-IGP-RosetteDeLyonOuJesusDeLyon.pdf",
        "language": "fr",
        "source_type": "official_institutional_pdf",
        "reliability": "high",
        "supports": [
            "Potvrđuje da su za naziv Rosette de Lyon / Jésus de Lyon postojali regulatorni postupci.",
            "Važan je za oprez oko statusa zaštite."
        ],
        "limits": [
            "Dokument navodi poništenje ranijih statusa 2008.; zato se u javnom receptu ne smije tvrditi aktualni IGP/ZOI status bez nove potvrde.",
            "Ne daje kućni recept."
        ],
        "decision": "STATUS_CAUTION_SOURCE"
    },
    {
        "id": "SRC-3042-004",
        "title": "Maison Sibilia — Jésu de Lyon, lokalni proizvođački opis",
        "url": "https://www.charcuterie-sibilia.com/saucissons-secs/84-jesu-de-lyon.html",
        "language": "fr",
        "source_type": "producer_product_description",
        "reliability": "medium",
        "supports": [
            "Potvrđuje da se Jésu/Jésus de Lyon prodaje kao suhi lyonški saucisson i lokalna specijalnost.",
            "Koristan je za senzorski i identitetski opis proizvoda."
        ],
        "limits": [
            "Proizvođački opis nije dovoljan za kanonski kućni recept.",
            "Ne smije se prepisivati reklamni tekst."
        ],
        "decision": "PRODUCT_MARKET_CONFIRMATION"
    },
    {
        "id": "SRC-3042-005",
        "title": "Le Blog Saucisson — Recette du Jésus",
        "url": "https://www.leblogsaucisson.fr/recette-jesus/",
        "language": "fr",
        "source_type": "public_recipe_blog",
        "reliability": "low_to_medium",
        "supports": [
            "Daje javni receptni obrazac za jésus kao suhi saucisson.",
            "Može pomoći kao pomoćni receptni signal za sastav i postupak."
        ],
        "limits": [
            "Blog nije visoko pouzdan izvor.",
            "Ne koristiti samostalno za javni kanon.",
            "Ne prepisivati doslovno."
        ],
        "decision": "SUPPORTING_RECIPE_SIGNAL_ONLY"
    }
]

source_validation = {
    "post_id": 3042,
    "title": "Jésus de Lyon – debela suha kobasica",
    "validation_status": "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED",
    "public_update_allowed": False,
    "generated_at": now,
    "summary": {
        "product_exists": True,
        "technology_family": "GROUND_MEAT_OR_CASING / dry saucisson",
        "region_identity": "Lyon, Francuska",
        "recipe_quantities_confirmed": False,
        "protected_status_public_claim_allowed": False,
        "reason": "Službeni izvori potvrđuju proizvod i tehnološku obitelj, ali ne potvrđuju sve količine i začine iz postojećeg WordPress recepta."
    },
    "sources": sources,
    "canonical_decisions": [
        "U javnom receptu smije se navesti da je Jésus de Lyon lyonški tip suhog saucissona / suhe kobasice.",
        "Ne smije se tvrditi aktualni IGP/ZOI/zaštićeni status bez dodatne potvrde.",
        "Postojeći WP recept ne smije se označiti kao PUBLIC_VERIFIED dok se recipe.yml ne izradi iz potvrđenih izvora.",
        "Količine začina iz WP sadržaja tretirati kao radni nacrt, ne kao potvrđeni kanon.",
        "Javni update nije dopušten u ovom koraku."
    ]
}

(review_dir / "3042_source_validation_v1.json").write_text(
    json.dumps(source_validation, ensure_ascii=False, indent=2),
    encoding="utf-8"
)

md = []
md.append("# 3042 Jésus de Lyon — source validation v1")
md.append("")
md.append("Status: **PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED**")
md.append("")
md.append("Ovaj dokument ne mijenja WordPress. Služi samo za izvorni dosje prije izrade kanonskog `recipe.yml`.")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Vanjski izvori potvrđuju da je Jésus de Lyon stvaran lyonški suhomesnati proizvod iz skupine suhih saucissona / suhih kobasica. Međutim, izvori ne potvrđuju automatski sve količine i začine iz postojećeg WordPress recepta.")
md.append("")
md.append("Zato je dopušteno nastaviti s kanonskom obradom, ali status ostaje: **proizvod potvrđen, recept još nije kanonski potvrđen**.")
md.append("")
md.append("## Javni status koji NE smijemo tvrditi")
md.append("")
md.append("- Ne tvrditi aktualni IGP/ZOI/zaštićeni status bez dodatne potvrde.")
md.append("- Ne tvrditi da postojeći WP recept doslovno odgovara službenom cahier des charges.")
md.append("- Ne prepisivati izvorne tekstove.")
md.append("")
md.append("## Izvori")
md.append("")
md.append("| ID | Pouzdanost | Vrsta | Što potvrđuje | Ograničenje |")
md.append("|---|---|---|---|---|")
for s in sources:
    md.append(
        "| {id} | {rel} | {typ} | {sup} | {lim} |".format(
            id=s["id"],
            rel=s["reliability"],
            typ=s["source_type"],
            sup="; ".join(s["supports"]).replace("|", "/"),
            lim="; ".join(s["limits"]).replace("|", "/")
        )
    )
md.append("")
md.append("## Kanonske odluke za daljnji rad")
md.append("")
for d in source_validation["canonical_decisions"]:
    md.append(f"- {d}")
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Izraditi `recipe.yml` kao kanonski radni nacrt na 10 kg, uz jasno označavanje koji su podaci potvrđeni službenim izvorom, a koji su tehnološka radna smjernica. Javni update i dalje nije dopušten.")
md.append("")

(review_dir / "3042_SOURCE_VALIDATION_REPORT.md").write_text("\n".join(md), encoding="utf-8")

# Update sources.yml as repo working dossier, keeping it simple and explicit.
sources_yml = []
sources_yml.append('# sources.yml')
sources_yml.append('dossier_status: "SOURCE_VALIDATION_V1"')
sources_yml.append('public_update_allowed: false')
sources_yml.append('post_id: 3042')
sources_yml.append('title: "Jésus de Lyon – debela suha kobasica"')
sources_yml.append('url: "https://drycured.com/recepti-baza/jesus-de-lyon-debela-suha-kobasica/"')
sources_yml.append('wordpress_status: "publish"')
sources_yml.append('recipe_type: "GROUND_MEAT_OR_CASING"')
sources_yml.append('source_validation_status: "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED"')
sources_yml.append('product_confirmed: true')
sources_yml.append('canonical_recipe_confirmed: false')
sources_yml.append('protected_status_claim_allowed: false')
sources_yml.append('notes:')
sources_yml.append('  - "Službeni izvori potvrđuju proizvod i tehnološku obitelj, ali ne potvrđuju sve količine iz postojećeg WP recepta."')
sources_yml.append('  - "Ne tvrditi aktualni IGP/ZOI/zaštićeni status bez dodatne potvrde."')
sources_yml.append('  - "Javni update nije dopušten dok recipe.yml i qa_report.md nisu završeni."')
sources_yml.append('source_candidates:')
for s in sources:
    sources_yml.append(f'  - id: "{s["id"]}"')
    sources_yml.append(f'    title: "{s["title"]}"')
    sources_yml.append(f'    url: "{s["url"]}"')
    sources_yml.append(f'    language: "{s["language"]}"')
    sources_yml.append(f'    source_type: "{s["source_type"]}"')
    sources_yml.append(f'    reliability: "{s["reliability"]}"')
    sources_yml.append(f'    decision: "{s["decision"]}"')
    sources_yml.append('    supports:')
    for item in s["supports"]:
        sources_yml.append(f'      - "{item}"')
    sources_yml.append('    limits:')
    for item in s["limits"]:
        sources_yml.append(f'      - "{item}"')
sources_yml.append('wp_snapshot: "raw_wp_snapshot.json"')
sources_yml.append('public_text_snapshot: "public_text_snapshot.txt"')
sources_path.write_text("\n".join(sources_yml) + "\n", encoding="utf-8")

# Update QA report by appending source validation section; avoid duplicate append marker.
qa_text = qa_path.read_text(encoding="utf-8")
marker = "<!-- DC_3042_SOURCE_VALIDATION_V1 -->"
append = f"""
{marker}

## Source validation v1

Status izvora: **PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED**

Vanjski izvori potvrđuju da je Jésus de Lyon stvaran lyonški suhomesnati proizvod iz skupine suhih saucissona / suhih kobasica. Ne potvrđuju automatski sve količine i začine iz postojećeg WordPress recepta.

### Odluka

- [x] Proizvod postoji i ima vjerodostojan vanjski trag.
- [x] Tehnološka obitelj potvrđena je kao mljeveno/usitnjeno meso u omotaču.
- [ ] Kanonski recept još nije potvrđen.
- [ ] Javni update nije dopušten.
- [ ] Ne tvrditi aktualni IGP/ZOI/zaštićeni status bez dodatne potvrde.

Report: `review/{review_dir.name}/3042_SOURCE_VALIDATION_REPORT.md`
"""
if marker not in qa_text:
    qa_path.write_text(qa_text.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3042 SOURCE VALIDATION COMPLETE ===")
print("STATUS=PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED")
print("PRODUCT_CONFIRMED=true")
print("CANONICAL_RECIPE_CONFIRMED=false")
print("PUBLIC_UPDATE_ALLOWED=false")
print(f"SOURCES_UPDATED={sources_path}")
print(f"QA_UPDATED={qa_path}")
print(f"REPORT={review_dir / '3042_SOURCE_VALIDATION_REPORT.md'}")
