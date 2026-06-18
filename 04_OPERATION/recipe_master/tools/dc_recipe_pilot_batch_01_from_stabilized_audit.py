#!/usr/bin/env python3
import csv
import json
import sys
from pathlib import Path
from collections import Counter, defaultdict

if len(sys.argv) != 3:
    print("Usage: dc_recipe_pilot_batch_01_from_stabilized_audit.py STABILIZED_CSV OUT_DIR", file=sys.stderr)
    sys.exit(1)

csv_path = Path(sys.argv[1])
out_dir = Path(sys.argv[2])
out_dir.mkdir(parents=True, exist_ok=True)

with csv_path.open("r", encoding="utf-8", newline="") as f:
    rows = list(csv.DictReader(f))

def clean_title(t):
    return (t or "").replace("|", "/").strip()

def is_public(row):
    return row.get("post_status") == "publish"

def gate(row):
    return row.get("v1_1_public_update_gate", "")

def typ(row):
    return row.get("v1_1_final_type", "")

def priority(row):
    return row.get("v1_1_work_priority", "")

def block_reasons(row):
    return row.get("v1_1_block_reasons", "") or row.get("block_reasons", "")

def sort_key(row):
    try:
        pid = int(row.get("post_id") or 0)
    except ValueError:
        pid = 0
    # prvo public, zatim PASS, zatim niži ID
    return (
        0 if is_public(row) else 1,
        0 if gate(row) == "PASS" else 1,
        pid
    )

public_pass = [r for r in rows if is_public(r) and gate(r) == "PASS"]
public_fail = [r for r in rows if is_public(r) and gate(r) == "FAIL"]
all_pass = [r for r in rows if gate(r) == "PASS"]

by_type_public_pass = defaultdict(list)
by_type_public_fail = defaultdict(list)
by_type_all_pass = defaultdict(list)

for r in public_pass:
    by_type_public_pass[typ(r)].append(r)

for r in public_fail:
    by_type_public_fail[typ(r)].append(r)

for r in all_pass:
    by_type_all_pass[typ(r)].append(r)

for d in [by_type_public_pass, by_type_public_fail, by_type_all_pass]:
    for k in d:
        d[k] = sorted(d[k], key=sort_key)

# Pilot logika:
# 1) prvo referentni tip GROUND, jer imamo potvrđen model HR-SL-005
# 2) zatim WHOLE_CUT kao zaseban model, ali bez miješanja u isti renderer
# 3) THERMAL i FISH samo kao pregled, ne za prvi javni update
pilot_ground = by_type_public_pass.get("GROUND_MEAT_OR_CASING", [])[:10]
pilot_whole = by_type_public_pass.get("WHOLE_CUT", [])[:10]
pilot_thermal = by_type_public_pass.get("THERMAL_PROCESSED", [])[:10]
pilot_fish = by_type_public_pass.get("FISH_OR_SEAFOOD", [])[:10]

# Predloženi prvi radni batch: samo GROUND public PASS, jer imamo referentni model.
pilot_01_recommended = pilot_ground[:10]

summary = {
    "source_csv": str(csv_path),
    "total_rows": len(rows),
    "public_pass_total": len(public_pass),
    "public_fail_total": len(public_fail),
    "all_pass_total": len(all_pass),
    "public_pass_by_type": {k: len(v) for k, v in sorted(by_type_public_pass.items())},
    "public_fail_by_type": {k: len(v) for k, v in sorted(by_type_public_fail.items())},
    "all_pass_by_type": {k: len(v) for k, v in sorted(by_type_all_pass.items())},
    "pilot_01_recommended_count": len(pilot_01_recommended),
}

def write_csv(path, subset):
    headers = [
        "post_id", "title", "url", "post_status",
        "detected_type", "v1_1_final_type",
        "v1_1_public_update_gate", "v1_1_work_priority",
        "v1_1_type_reason", "v1_1_block_reasons",
        "has_granulation", "has_fat_handling", "has_casing",
        "has_brine_or_cure", "has_thermal_process", "has_thermal_params",
        "has_cold_chain", "has_smoking", "has_drying", "has_aging",
        "has_nitrite", "has_nitrite_note", "has_fallback_internal",
    ]
    with Path(path).open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=headers)
        w.writeheader()
        for r in subset:
            w.writerow({h: r.get(h, "") for h in headers})

write_csv(out_dir / "pilot_01_recommended_ground_public_pass.csv", pilot_01_recommended)
write_csv(out_dir / "public_pass_all_types.csv", public_pass)
write_csv(out_dir / "public_fail_blocked.csv", public_fail)
write_csv(out_dir / "public_pass_ground.csv", pilot_ground)
write_csv(out_dir / "public_pass_whole_cut.csv", pilot_whole)
write_csv(out_dir / "public_pass_thermal.csv", pilot_thermal)
write_csv(out_dir / "public_pass_fish.csv", pilot_fish)

(out_dir / "pilot_batch_01_summary.json").write_text(
    json.dumps({
        "summary": summary,
        "pilot_01_recommended": pilot_01_recommended,
        "pilot_ground": pilot_ground,
        "pilot_whole": pilot_whole,
        "pilot_thermal": pilot_thermal,
        "pilot_fish": pilot_fish,
    }, ensure_ascii=False, indent=2),
    encoding="utf-8"
)

def md_table(subset, limit=20):
    lines = []
    lines.append("| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |")
    lines.append("|---:|---|---|---|---|---|---|")
    for r in subset[:limit]:
        lines.append(
            f"| {r.get('post_id','')} | {r.get('v1_1_final_type','')} | "
            f"{r.get('v1_1_public_update_gate','')} | {r.get('v1_1_work_priority','')} | "
            f"{clean_title(r.get('title',''))} | {r.get('url','')} | "
            f"{(r.get('v1_1_type_reason','') or block_reasons(r)).replace('|','/')} |"
        )
    return lines

md = []
md.append("# DRYCURED PILOT_BATCH_01 — radna lista iz stabiliziranog audita v1.1")
md.append("")
md.append("Ovaj dokument ne mijenja WordPress. Služi samo za izbor prvog malog batcha za pojedinačnu obradu.")
md.append("")
md.append("## Izvor")
md.append("")
md.append(f"- Stabilized audit CSV: `{csv_path}`")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Ukupno redova: {summary['total_rows']}")
md.append(f"- Javno objavljeni PASS kandidati: {summary['public_pass_total']}")
md.append(f"- Javno objavljeni FAIL/blokirani: {summary['public_fail_total']}")
md.append(f"- Svi PASS kandidati, uključujući private/draft/pending: {summary['all_pass_total']}")
md.append("")
md.append("## Javni PASS po tipu")
md.append("")
for k, v in sorted(summary["public_pass_by_type"].items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Javni FAIL po tipu")
md.append("")
for k, v in sorted(summary["public_fail_by_type"].items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Preporuka")
md.append("")
md.append("Prvi pilot batch treba biti samo `GROUND_MEAT_OR_CASING`, jer za taj tip već postoji potvrđeni referentni javni model: Slavonska domaća kobasica HR-SL-005.")
md.append("")
md.append("Ne raditi javni update izravno iz ove liste. Za svaki recept treba otvoriti pojedinačni izvorni dosje, recipe.yml i QA izvještaj.")
md.append("")
md.append("## PILOT_01_RECOMMENDED — mljeveno/usitnjeno meso u omotaču")
md.append("")
md.extend(md_table(pilot_01_recommended, 20))
md.append("")
md.append("## Javni PASS — WHOLE_CUT kandidati za kasniji zaseban model")
md.append("")
md.extend(md_table(pilot_whole, 20))
md.append("")
md.append("## Javni PASS — THERMAL_PROCESSED kandidati za kasniji zaseban model")
md.append("")
md.extend(md_table(pilot_thermal, 20))
md.append("")
md.append("## Javni PASS — FISH_OR_SEAFOOD kandidati za kasniji zaseban model")
md.append("")
md.extend(md_table(pilot_fish, 20))
md.append("")
md.append("## Prvih 40 javno blokiranih")
md.append("")
md.extend(md_table(public_fail, 40))
md.append("")
md.append("## Izlazne datoteke")
md.append("")
md.append("- `pilot_01_recommended_ground_public_pass.csv`")
md.append("- `public_pass_all_types.csv`")
md.append("- `public_fail_blocked.csv`")
md.append("- `public_pass_ground.csv`")
md.append("- `public_pass_whole_cut.csv`")
md.append("- `public_pass_thermal.csv`")
md.append("- `public_pass_fish.csv`")
md.append("- `pilot_batch_01_summary.json`")
md.append("")

(out_dir / "PILOT_BATCH_01_WORKLIST.md").write_text("\n".join(md), encoding="utf-8")

print("=== PILOT BATCH 01 WORKLIST COMPLETE ===")
print(f"TOTAL={summary['total_rows']}")
print(f"PUBLIC_PASS_TOTAL={summary['public_pass_total']}")
print(f"PUBLIC_FAIL_TOTAL={summary['public_fail_total']}")
print(f"ALL_PASS_TOTAL={summary['all_pass_total']}")
print("PUBLIC_PASS_BY_TYPE:")
for k, v in sorted(summary["public_pass_by_type"].items()):
    print(f"{k}={v}")
print("PUBLIC_FAIL_BY_TYPE:")
for k, v in sorted(summary["public_fail_by_type"].items()):
    print(f"{k}={v}")
print(f"PILOT_01_RECOMMENDED_COUNT={len(pilot_01_recommended)}")
print(f"WORKLIST={out_dir / 'PILOT_BATCH_01_WORKLIST.md'}")
