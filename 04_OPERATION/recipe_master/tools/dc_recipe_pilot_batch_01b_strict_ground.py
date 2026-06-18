#!/usr/bin/env python3
import csv
import json
import re
import sys
from pathlib import Path

if len(sys.argv) != 3:
    print("Usage: dc_recipe_pilot_batch_01b_strict_ground.py SOURCE_CSV OUT_DIR", file=sys.stderr)
    sys.exit(1)

src = Path(sys.argv[1])
out_dir = Path(sys.argv[2])
out_dir.mkdir(parents=True, exist_ok=True)

with src.open("r", encoding="utf-8", newline="") as f:
    rows = list(csv.DictReader(f))

def norm(s):
    s = (s or "").lower()
    repl = {
        "š": "s", "č": "c", "ć": "c", "ž": "z", "đ": "d",
        "ä": "a", "ö": "o", "ü": "u", "ß": "ss",
        "á": "a", "à": "a", "â": "a", "é": "e", "è": "e", "ê": "e",
        "í": "i", "ì": "i", "ó": "o", "ò": "o", "ú": "u", "ù": "u",
        "ñ": "n", "ø": "o", "æ": "ae", "œ": "oe",
    }
    for a, b in repl.items():
        s = s.replace(a, b)
    s = re.sub(r"&#8211;|&ndash;|–|—", " ", s)
    s = re.sub(r"[^a-z0-9]+", " ", s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

GROUND_STRONG = [
    "kobasica", "kobasice", "salama", "salame", "saucisson",
    "finocchiona", "nduja", "jesus de lyon", "salame di felino",
    "kulen", "kulin", "sudzuk", "sudžuk", "mettwurst", "landjager",
    "chorizo", "soppressata", "soppressa",
]

WHOLE_EXCLUDE = [
    "filet", "file", "fillet", "but", "vrat", "slanina", "panceta",
    "pancetta", "lardo", "guanciale", "coppa", "capocollo", "bresaola",
    "pastourma", "pastourmas", "pastirma", "pastrama", "basturma",
    "apohti", "apohtin", "suhomesnato govedje meso", "suseno meso",
    "suho meso", "ovcetina", "govedji file", "svinjski file",
]

THERMAL_EXCLUDE = [
    "krvavica", "black pudding", "white pudding", "bratwurst",
    "jetrenjaca", "pasteta", "tlacenica", "svargla", "mortadella",
]

strict = []
excluded = []

for r in rows:
    title = r.get("title", "")
    t = norm(title)

    ground_hits = [x for x in GROUND_STRONG if x in t]
    whole_hits = [x for x in WHOLE_EXCLUDE if x in t]
    thermal_hits = [x for x in THERMAL_EXCLUDE if x in t]

    reason = []
    decision = "EXCLUDE"

    if whole_hits:
        reason.append("WHOLE_CUT_SIGNAL:" + ",".join(whole_hits))
    if thermal_hits:
        reason.append("THERMAL_SIGNAL:" + ",".join(thermal_hits))
    if ground_hits:
        reason.append("GROUND_SIGNAL:" + ",".join(ground_hits))

    if ground_hits and not whole_hits and not thermal_hits:
        decision = "STRICT_GROUND_ACCEPT"

    out = dict(r)
    out["strict_decision"] = decision
    out["strict_reason"] = ";".join(reason)

    if decision == "STRICT_GROUND_ACCEPT":
        strict.append(out)
    else:
        excluded.append(out)

headers = list(rows[0].keys()) + ["strict_decision", "strict_reason"] if rows else []

def write_csv(path, data):
    with Path(path).open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=headers)
        w.writeheader()
        for r in data:
            w.writerow({h: r.get(h, "") for h in headers})

write_csv(out_dir / "pilot_01b_strict_ground_accept.csv", strict)
write_csv(out_dir / "pilot_01b_excluded_for_type_review.csv", excluded)

summary = {
    "source_csv": str(src),
    "input_total": len(rows),
    "strict_ground_accept": len(strict),
    "excluded_for_type_review": len(excluded),
    "accepted": strict,
    "excluded": excluded,
}

(out_dir / "pilot_01b_strict_ground_summary.json").write_text(
    json.dumps(summary, ensure_ascii=False, indent=2),
    encoding="utf-8"
)

def table(data):
    lines = []
    lines.append("| Post ID | Naslov | Tip | Gate | URL | Odluka | Razlog |")
    lines.append("|---:|---|---|---|---|---|---|")
    for r in data:
        lines.append(
            f"| {r.get('post_id','')} | {(r.get('title','') or '').replace('|','/')} | "
            f"{r.get('v1_1_final_type','')} | {r.get('v1_1_public_update_gate','')} | "
            f"{r.get('url','')} | {r.get('strict_decision','')} | "
            f"{(r.get('strict_reason','') or '').replace('|','/')} |"
        )
    return lines

md = []
md.append("# DRYCURED PILOT_BATCH_01B_STRICT_GROUND")
md.append("")
md.append("Ovaj dokument ne mijenja WordPress. Služi za strogo čišćenje prvog pilot batcha.")
md.append("")
md.append("## Pravilo")
md.append("")
md.append("U prvi pilot smiju ući samo proizvodi koji su stvarno mljeveno/usitnjeno meso u omotaču. Komadi mesa, fileti, pastirme, pastrame, basturme, pancete, vratovi, slanine i riba se isključuju i šalju u vlastite tehnološke modele.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Izvorni redovi: {len(rows)}")
md.append(f"- Prihvaćeno kao STRICT_GROUND: {len(strict)}")
md.append(f"- Isključeno za type review: {len(excluded)}")
md.append("")
md.append("## Prihvaćeni kandidati za prvi pojedinačni dosje")
md.append("")
md.extend(table(strict))
md.append("")
md.append("## Isključeni kandidati — prebaciti u WHOLE_CUT/THERMAL review")
md.append("")
md.extend(table(excluded))
md.append("")
md.append("## Sljedeći korak")
md.append("")
md.append("Za prihvaćene kandidate otvoriti pojedinačne dosjee: sources.yml, recipe.yml, qa_report.md i wordpress_import_log.md. Javni update se i dalje ne radi.")
md.append("")

(out_dir / "PILOT_BATCH_01B_STRICT_GROUND.md").write_text("\n".join(md), encoding="utf-8")

print("=== PILOT BATCH 01B STRICT COMPLETE ===")
print(f"INPUT_TOTAL={len(rows)}")
print(f"STRICT_GROUND_ACCEPT={len(strict)}")
print(f"EXCLUDED_FOR_TYPE_REVIEW={len(excluded)}")
print("ACCEPTED_IDS=" + ",".join(r.get("post_id", "") for r in strict))
print("EXCLUDED_IDS=" + ",".join(r.get("post_id", "") for r in excluded))
print(f"WORKLIST={out_dir / 'PILOT_BATCH_01B_STRICT_GROUND.md'}")
