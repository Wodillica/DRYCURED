#!/usr/bin/env python3
import csv
import json
import re
import sys
from pathlib import Path
from collections import Counter, defaultdict

if len(sys.argv) != 3:
    print("Usage: dc_recipe_type_router_calibration_v1_1.py INPUT_CSV OUT_DIR", file=sys.stderr)
    sys.exit(1)

csv_path = Path(sys.argv[1])
out_dir = Path(sys.argv[2])
out_dir.mkdir(parents=True, exist_ok=True)

rows = []
with csv_path.open("r", encoding="utf-8", newline="") as f:
    reader = csv.DictReader(f)
    for row in reader:
        rows.append(row)

def norm(s: str) -> str:
    s = (s or "").lower()
    replacements = {
        "š": "s", "č": "c", "ć": "c", "ž": "z", "đ": "d",
        "ä": "a", "ö": "o", "ü": "u", "ß": "ss",
        "á": "a", "à": "a", "â": "a", "é": "e", "è": "e",
        "ê": "e", "í": "i", "ì": "i", "ó": "o", "ò": "o",
        "ú": "u", "ù": "u", "ñ": "n", "ø": "o", "æ": "ae",
        "œ": "oe",
    }
    for a, b in replacements.items():
        s = s.replace(a, b)
    s = re.sub(r"\s+", " ", s).strip()
    return s

WHOLE_TITLE = [
    "prosciutto", "prsut", "pršut", "san daniele", "parma",
    "speck", "schinken", "rohschinken", "sunka", "šunka",
    "pancetta", "panceta", "slanina", "lardo", "guanciale",
    "coppa", "capocollo", "capicola", "vratina", "vrat",
    "bresaola", "lonza", "lountza", "posyrti", "apohtin",
    "pastirma", "pastrma", "pastrami", "but", "plecka", "plećka",
    "rebra", "kare", "udić", "udic", "stelja", "suseno meso",
    "suho meso", "dimljeni janjeti but", "hangikjot", "hangikjöt",
]

THERMAL_TITLE = [
    "krvavica", "black pudding", "white pudding", "drisheen",
    "kaszanka", "morcela", "jetrenjaca", "jetrenjača",
    "dzigernjaca", "džigernjača", "liver", "leverpostej",
    "pate", "pašteta", "pasteta", "tlačenica", "tlacenica",
    "svargla", "švargla", "brawn", "head cheese", "hladetina",
    "pihtije", "mortadella", "mortadela", "hrenovka", "virsla",
    "viršle", "zampone", "cotechino", "barena", "kuhana",
    "kuvana", "pecena", "pečena", "pečenica", "pecenica",
    "bratwurst", "kobasica za pecenje", "kobasica za pečenje",
]

FISH_TITLE = [
    "riba", "riblji", "morski", "pastrva", "losos", "bakalar",
    "tuna", "skusa", "skuša", "sardina", "incun", "inćun",
    "brancin", "orada", "oslic", "oslić", "dimljena riba",
    "soljena riba", "susena riba", "sušena riba",
]

NOISE_TITLE = [
    "kompleksna zbirka", "tradicionalni recepti", "recepti za",
    "etnografska studija", "tradicijskih", "testni recept",
    "davorova kobasa", "ujedinjeno kraljevstvo", "francuska",
    "grcka", "grčka", "ceska", "češka",
]

def hits(title_norm, words):
    return [w for w in words if w in title_norm]

calibrated = []
counter_current = Counter()
counter_proposed = Counter()
issues = Counter()

for r in rows:
    title = r.get("title", "")
    t = norm(title)
    current = r.get("detected_type", "")
    proposed = current
    reason = []
    severity = "INFO"

    h_noise = hits(t, NOISE_TITLE)
    h_fish = hits(t, FISH_TITLE)
    h_thermal = hits(t, THERMAL_TITLE)
    h_whole = hits(t, WHOLE_TITLE)

    if h_noise:
        proposed = "NEEDS_CLASSIFICATION"
        reason.append("TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:" + ",".join(h_noise))
        severity = "BLOCK"

    elif h_fish and current != "FISH_OR_SEAFOOD":
        proposed = "FISH_OR_SEAFOOD"
        reason.append("TITLE_FISH_SIGNAL:" + ",".join(h_fish))
        severity = "RECLASSIFY"

    elif h_thermal and current != "THERMAL_PROCESSED":
        proposed = "THERMAL_PROCESSED"
        reason.append("TITLE_THERMAL_SIGNAL:" + ",".join(h_thermal))
        severity = "RECLASSIFY"

    elif h_whole and current != "WHOLE_CUT":
        proposed = "WHOLE_CUT"
        reason.append("TITLE_WHOLE_CUT_SIGNAL:" + ",".join(h_whole))
        severity = "RECLASSIFY"

    if current == "GROUND_MEAT_OR_CASING" and h_whole:
        issues["ground_probably_whole_cut"] += 1

    if current == "GROUND_MEAT_OR_CASING" and h_thermal:
        issues["ground_probably_thermal"] += 1

    if current != proposed:
        issues["changed_proposal"] += 1

    counter_current[current] += 1
    counter_proposed[proposed] += 1

    out = dict(r)
    out["calibration_proposed_type"] = proposed
    out["calibration_severity"] = severity
    out["calibration_reason"] = ";".join(reason)
    calibrated.append(out)

out_csv = out_dir / "recipe_type_router_calibration_v1_1.csv"
out_json = out_dir / "recipe_type_router_calibration_v1_1.json"
out_md = out_dir / "recipe_type_router_calibration_v1_1_summary.md"

headers = list(calibrated[0].keys()) if calibrated else []
with out_csv.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=headers)
    writer.writeheader()
    writer.writerows(calibrated)

changed = [r for r in calibrated if r["detected_type"] != r["calibration_proposed_type"]]
reclassify = [r for r in changed if r["calibration_severity"] == "RECLASSIFY"]
blocked = [r for r in changed if r["calibration_severity"] == "BLOCK"]

payload = {
    "source_csv": str(csv_path),
    "total_rows": len(rows),
    "current_type_counts": dict(counter_current),
    "proposed_type_counts": dict(counter_proposed),
    "issue_counts": dict(issues),
    "changed_total": len(changed),
    "reclassify_total": len(reclassify),
    "block_total": len(blocked),
    "changed_rows": changed,
}
out_json.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

md = []
md.append("# DRYCURED Recipe Type Router — calibration v1.1")
md.append("")
md.append("Ovaj izvještaj ne mijenja WordPress. Služi samo za kalibraciju klasifikatora prije bilo kakvog batch uređivanja.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Izvorni CSV: `{csv_path}`")
md.append(f"- Ukupno redova: {len(rows)}")
md.append(f"- Prijedloga promjene tipa: {len(changed)}")
md.append(f"- Reclassify prijedloga: {len(reclassify)}")
md.append(f"- Blokada zbog kolekcije/testa/nerecepta: {len(blocked)}")
md.append("")
md.append("## Trenutni broj po tipu")
md.append("")
for k, v in sorted(counter_current.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Predloženi broj po tipu nakon kalibracije")
md.append("")
for k, v in sorted(counter_proposed.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Detektirani problemi")
md.append("")
for k, v in sorted(issues.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Prvih 80 prijedloga promjene")
md.append("")
md.append("| Post ID | Status | Trenutni tip | Predloženi tip | Naslov | Razlog |")
md.append("|---:|---|---|---|---|---|")
for r in changed[:80]:
    md.append(
        f"| {r.get('post_id','')} | {r.get('post_status','')} | "
        f"{r.get('detected_type','')} | {r.get('calibration_proposed_type','')} | "
        f"{(r.get('title','') or '').replace('|','/')} | "
        f"{(r.get('calibration_reason','') or '').replace('|','/')} |"
    )
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Ako se potvrdi da su prijedlozi kalibracije logični, sljedeći korak je izrada audit alata v1.1 s jačim pravilima prioriteta za WHOLE_CUT, THERMAL_PROCESSED i FISH_OR_SEAFOOD.")
md.append("Tek nakon toga smije se odabrati prvi mali batch za ručnu/kanonsku obradu.")
md.append("")

out_md.write_text("\n".join(md), encoding="utf-8")

print("=== CALIBRATION COMPLETE ===")
print(f"TOTAL={len(rows)}")
print(f"CHANGED_TOTAL={len(changed)}")
print(f"RECLASSIFY_TOTAL={len(reclassify)}")
print(f"BLOCK_TOTAL={len(blocked)}")
print("CURRENT_TYPE_COUNTS:")
for k, v in sorted(counter_current.items()):
    print(f"{k}={v}")
print("PROPOSED_TYPE_COUNTS:")
for k, v in sorted(counter_proposed.items()):
    print(f"{k}={v}")
print("ISSUE_COUNTS:")
for k, v in sorted(issues.items()):
    print(f"{k}={v}")
print(f"CSV={out_csv}")
print(f"JSON={out_json}")
print(f"SUMMARY={out_md}")
