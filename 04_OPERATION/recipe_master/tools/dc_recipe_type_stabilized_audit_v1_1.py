#!/usr/bin/env python3
import csv
import json
import re
import sys
from pathlib import Path
from collections import Counter, defaultdict

if len(sys.argv) != 4:
    print("Usage: dc_recipe_type_stabilized_audit_v1_1.py AUDIT_CSV CALIB_CSV OUT_DIR", file=sys.stderr)
    sys.exit(1)

audit_csv = Path(sys.argv[1])
calib_csv = Path(sys.argv[2])
out_dir = Path(sys.argv[3])
out_dir.mkdir(parents=True, exist_ok=True)

def read_csv(path):
    with path.open("r", encoding="utf-8", newline="") as f:
        return list(csv.DictReader(f))

audit_rows = read_csv(audit_csv)
calib_rows = read_csv(calib_csv)

calib_by_id = {r.get("post_id"): r for r in calib_rows}

def normalize(s):
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
    s = re.sub(r"&#8211;|&ndash;|–|—", "-", s)
    s = re.sub(r"[^a-z0-9]+", " ", s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

def has_phrase(t, phrase):
    return phrase in t

def has_word(t, word):
    return re.search(rf"(^|\\s){re.escape(word)}($|\\s)", t) is not None

def any_phrase(t, phrases):
    return [p for p in phrases if has_phrase(t, p)]

def any_word(t, words):
    return [w for w in words if has_word(t, w)]

NOISE = [
    "kompleksna zbirka", "tradicionalni recepti", "recepti za",
    "etnografska studija", "tradicijskih", "testni recept",
    "davorova kobasa", "ujedinjeno kraljevstvo", "francuska",
    "grcka", "ceska", "italija", "njemacka",
]

FISH = [
    "riba", "riblji", "morski", "pastrva", "losos", "bakalar",
    "tuna", "skusa", "sardina", "incun", "brancin", "orada",
    "oslic", "savusiika", "kylmasavulohi", "graavilohi", "gravlax",
    "gravet laks", "lasišos", "lasisos",
]

THERMAL_STRONG = [
    "krvavica", "black pudding", "white pudding", "drisheen",
    "kaszanka", "morcela", "verikakk", "blodpølse", "blodpolse",
    "jetrenjaca", "dzigernjaca", "leberwurst", "leverworst",
    "leverpostej", "pate", "pasteta", "tlacenica", "svargla",
    "brawn", "head cheese", "hladetina", "pihtije", "mortadella",
    "mortadela", "hrenovka", "virsla", "virsle", "zampone",
    "cotechino", "haslet", "slatur",
]

THERMAL_CONTEXT = [
    "bratwurst", "kobasica za pecenje", "kobasica za pečenje",
    "pecena svinjetina", "kuhana sunka", "kuvana sunka",
    "boiled ham", "barena", "kuhana", "kuvana",
]

WHOLE_STRONG = [
    "prosciutto", "prsut", "pršut", "san daniele", "parma",
    "speck", "schinken", "rohschinken", "sunka", "šunka",
    "pancetta", "panceta", "slanina", "lardo", "guanciale",
    "coppa", "capocollo", "capicola", "vratina", "bresaola",
    "lonza", "lountza", "posyrti", "apohtin", "pastirma",
    "pasterma", "pastrma", "stelja", "lomo", "jambon",
    "pinnekjott", "fenalar", "baleron",
]

WHOLE_CONTEXT = [
    "suho meso", "suseno meso", "sušeno meso", "dimljeni janjeti but",
    "suseni but", "sušeni but", "sobov but", "svinjska rebra",
    "susena rebra", "sušena rebra", "zacinjeni vrat", "začinjeni vrat",
    "suseni vrat", "sušeni vrat", "dimljena sunka", "dimljena šunka",
]

WHOLE_WORDS = [
    "vrat", "but", "rebra", "kare",
]

GROUND_STRONG = [
    "kobasica", "kobasice", "salama", "salame", "kulen", "kulin",
    "sudzuk", "sudžuk", "saucisson", "mettwurst", "landjager",
    "nduja", "finocchiona", "salame di felino",
]

def title_proposal(title, current):
    t = normalize(title)

    reasons = []
    final = current
    severity = "KEEP"

    noise_hits = any_phrase(t, NOISE)
    fish_hits = any_phrase(t, FISH)
    thermal_hits = any_phrase(t, THERMAL_STRONG) + any_phrase(t, THERMAL_CONTEXT)
    whole_hits = any_phrase(t, WHOLE_STRONG) + any_phrase(t, WHOLE_CONTEXT) + any_word(t, WHOLE_WORDS)
    ground_hits = any_phrase(t, GROUND_STRONG)

    # Lažni signal: "but" u "butifarra" nije but.
    if "butifarra" in t:
        whole_hits = [h for h in whole_hits if h != "but"]

    # "pečenica" u hrvatskom sustavu često znači sušeni komad; ne smije sama prebaciti u thermal.
    if "suhi kare pecenica" in t or "susena svinjska pecenica" in t or "sušena svinjska pečenica" in title.lower():
        thermal_hits = [h for h in thermal_hits if h not in ["pecenica", "pecena"]]
        if "pecenica" not in whole_hits:
            whole_hits.append("pecenica_as_whole_cut_context")

    if noise_hits:
        final = "NEEDS_CLASSIFICATION"
        severity = "BLOCK"
        reasons.append("NOISE_OR_COLLECTION:" + ",".join(noise_hits))
        return final, severity, reasons

    if fish_hits:
        final = "FISH_OR_SEAFOOD"
        severity = "RECLASSIFY" if current != final else "KEEP"
        reasons.append("FISH_TITLE:" + ",".join(fish_hits))
        return final, severity, reasons

    # Termički proizvodi imaju prednost nad kobasičnim signalom ako su krvavice, jetrene, tlačenice, paštete, mortadele.
    if thermal_hits:
        final = "THERMAL_PROCESSED"
        severity = "RECLASSIFY" if current != final else "KEEP"
        reasons.append("THERMAL_TITLE:" + ",".join(thermal_hits))
        return final, severity, reasons

    # Komadi mesa imaju prednost nad generičkom riječi "kobasica" samo kad je signal stvarno jak.
    if whole_hits:
        final = "WHOLE_CUT"
        severity = "RECLASSIFY" if current != final else "KEEP"
        reasons.append("WHOLE_CUT_TITLE:" + ",".join(whole_hits))
        return final, severity, reasons

    if ground_hits and current == "NEEDS_CLASSIFICATION":
        final = "GROUND_MEAT_OR_CASING"
        severity = "RECLASSIFY"
        reasons.append("GROUND_TITLE:" + ",".join(ground_hits))
        return final, severity, reasons

    return final, severity, reasons

def truthy(row, key):
    return str(row.get(key, "")).strip() == "1"

final_rows = []
counts = Counter()
status_counts = Counter()
severity_counts = Counter()
gate_counts = Counter()
changed = []
manual_review = []

for r in audit_rows:
    post_id = r.get("post_id")
    calib = calib_by_id.get(post_id, {})
    current = r.get("detected_type", "NEEDS_CLASSIFICATION")
    title = r.get("title", "")

    final_type, severity, reasons = title_proposal(title, current)

    # Ako nema naslovnog signala, ali je kalibracija već nešto predložila, prihvati samo ako nije low-confidence rubni slučaj.
    calib_type = calib.get("calibration_proposed_type", "")
    calib_reason = calib.get("calibration_reason", "")
    confidence = r.get("confidence", "")

    if final_type == current and calib_type and calib_type != current:
        if confidence in ["high", "medium"] and "TITLE_LOOKS_LIKE_COLLECTION_OR_TEST" in calib_reason:
            final_type = calib_type
            severity = "BLOCK"
            reasons.append("CALIBRATION_NOISE:" + calib_reason)
        elif confidence in ["high", "medium"] and (
            "TITLE_FISH_SIGNAL" in calib_reason or
            "TITLE_THERMAL_SIGNAL" in calib_reason or
            "TITLE_WHOLE_CUT_SIGNAL" in calib_reason
        ):
            # Prihvati samo ako nema očitog false-positive butifarra/but.
            if not ("butifarra" in normalize(title) and "but" in calib_reason):
                final_type = calib_type
                severity = "RECLASSIFY"
                reasons.append("CALIBRATION_ACCEPTED:" + calib_reason)

    block_reasons = [x for x in (r.get("block_reasons", "") or "").split(";") if x]

    # Tipološke blokade po finalnom tipu.
    if final_type == "NEEDS_CLASSIFICATION":
        if "NEEDS_CLASSIFICATION" not in block_reasons:
            block_reasons.append("NEEDS_CLASSIFICATION")

    if final_type == "GROUND_MEAT_OR_CASING":
        if not truthy(r, "has_granulation"):
            block_reasons.append("GROUND_MISSING_GRANULATION")
        if not truthy(r, "has_fat_handling"):
            block_reasons.append("GROUND_MISSING_FAT_HANDLING")
        if not truthy(r, "has_casing"):
            block_reasons.append("GROUND_MISSING_CASING")

    if final_type == "WHOLE_CUT":
        if not truthy(r, "has_brine_or_cure"):
            block_reasons.append("WHOLE_CUT_MISSING_CURE_OR_BRINE")

    if final_type == "THERMAL_PROCESSED":
        if not truthy(r, "has_thermal_process"):
            block_reasons.append("THERMAL_MISSING_PROCESS")
        if not truthy(r, "has_thermal_params"):
            block_reasons.append("THERMAL_MISSING_TEMP_OR_DURATION")

    if final_type == "FISH_OR_SEAFOOD":
        if not truthy(r, "has_cold_chain"):
            block_reasons.append("FISH_MISSING_COLD_CHAIN")
        if not truthy(r, "has_brine_or_cure"):
            block_reasons.append("FISH_MISSING_SALTING_OR_BRINE")

    if truthy(r, "has_nitrite") and not truthy(r, "has_nitrite_note"):
        block_reasons.append("NITRITE_WITHOUT_SAFETY_NOTE")

    if truthy(r, "has_fallback_internal"):
        block_reasons.append("PUBLIC_OR_META_INTERNAL_TEXT_HIT")

    if (truthy(r, "has_smoking") or truthy(r, "has_drying") or truthy(r, "has_aging")) and not truthy(r, "has_phase_time_or_params"):
        block_reasons.append("PHASE_MISSING_TIME_OR_PARAMS")

    if truthy(r, "has_problem_signal") and not truthy(r, "has_solution_signal"):
        block_reasons.append("PROBLEM_WITHOUT_SOLUTION_SIGNAL")

    # Dedup uz očuvanje redoslijeda.
    seen = set()
    clean_block_reasons = []
    for b in block_reasons:
        if b and b not in seen:
            clean_block_reasons.append(b)
            seen.add(b)

    final_gate = "PASS" if not clean_block_reasons else "FAIL"

    if current != final_type:
        changed.append(r)

    if severity in ["RECLASSIFY", "BLOCK"] or confidence in ["low", "none"] or final_gate == "FAIL":
        manual_review.append(r)

    out = dict(r)
    out["v1_1_final_type"] = final_type
    out["v1_1_type_source"] = severity
    out["v1_1_type_reason"] = ";".join(reasons)
    out["v1_1_public_update_gate"] = final_gate
    out["v1_1_block_reasons"] = ";".join(clean_block_reasons)

    # Radni prioritet: ne znači objavu, nego redoslijed pregleda.
    if out.get("post_status") == "publish" and final_gate == "FAIL":
        out["v1_1_work_priority"] = "A_PUBLIC_BLOCKED"
    elif out.get("post_status") == "publish" and final_gate == "PASS":
        out["v1_1_work_priority"] = "B_PUBLIC_REVIEW_READY"
    elif final_gate == "FAIL":
        out["v1_1_work_priority"] = "C_NONPUBLIC_BLOCKED"
    else:
        out["v1_1_work_priority"] = "D_NONPUBLIC_REVIEW_READY"

    final_rows.append(out)
    counts[final_type] += 1
    status_counts[out.get("post_status", "")] += 1
    severity_counts[severity] += 1
    gate_counts[final_gate] += 1

out_csv = out_dir / "recipe_type_stabilized_audit_v1_1.csv"
out_json = out_dir / "recipe_type_stabilized_audit_v1_1.json"
out_md = out_dir / "recipe_type_stabilized_audit_v1_1_summary.md"

headers = list(final_rows[0].keys()) if final_rows else []
with out_csv.open("w", encoding="utf-8", newline="") as f:
    w = csv.DictWriter(f, fieldnames=headers)
    w.writeheader()
    w.writerows(final_rows)

payload = {
    "source_audit_csv": str(audit_csv),
    "source_calibration_csv": str(calib_csv),
    "total_rows": len(final_rows),
    "final_type_counts": dict(counts),
    "post_status_counts": dict(status_counts),
    "type_source_counts": dict(severity_counts),
    "gate_counts": dict(gate_counts),
    "changed_from_v1_total": sum(1 for r in final_rows if r.get("detected_type") != r.get("v1_1_final_type")),
    "public_fail_total": sum(1 for r in final_rows if r.get("post_status") == "publish" and r.get("v1_1_public_update_gate") == "FAIL"),
    "public_pass_total": sum(1 for r in final_rows if r.get("post_status") == "publish" and r.get("v1_1_public_update_gate") == "PASS"),
    "rows": final_rows,
}
out_json.write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

def table(rows, limit=60):
    lines = []
    lines.append("| Post ID | Status | V1 tip | V1.1 tip | Gate | Prioritet | Naslov | Razlog |")
    lines.append("|---:|---|---|---|---|---|---|---|")
    for r in rows[:limit]:
        lines.append(
            f"| {r.get('post_id','')} | {r.get('post_status','')} | {r.get('detected_type','')} | "
            f"{r.get('v1_1_final_type','')} | {r.get('v1_1_public_update_gate','')} | "
            f"{r.get('v1_1_work_priority','')} | {(r.get('title','') or '').replace('|','/')} | "
            f"{(r.get('v1_1_type_reason','') or r.get('v1_1_block_reasons','')).replace('|','/')} |"
        )
    return lines

public_blocked = [r for r in final_rows if r.get("post_status") == "publish" and r.get("v1_1_public_update_gate") == "FAIL"]
public_ready = [r for r in final_rows if r.get("post_status") == "publish" and r.get("v1_1_public_update_gate") == "PASS"]
changed_rows = [r for r in final_rows if r.get("detected_type") != r.get("v1_1_final_type")]

md = []
md.append("# DRYCURED Recipe Type Router — stabilized audit v1.1")
md.append("")
md.append("Ovaj izvještaj je read-only. Ne mijenja WordPress i služi kao stabilizirana radna lista prije pojedinačne obrade recepata.")
md.append("")
md.append("## Izvori")
md.append("")
md.append(f"- Audit v1 CSV: `{audit_csv}`")
md.append(f"- Calibration v1.1 CSV: `{calib_csv}`")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Ukupno recepata: {len(final_rows)}")
md.append(f"- Promijenjeno u odnosu na audit v1: {len(changed_rows)}")
md.append(f"- Javno objavljeni PASS kandidati: {len(public_ready)}")
md.append(f"- Javno objavljeni FAIL/blokirani: {len(public_blocked)}")
md.append("")
md.append("## Finalni broj po tipu")
md.append("")
for k, v in sorted(counts.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Gate rezultat")
md.append("")
for k, v in sorted(gate_counts.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Type source rezultat")
md.append("")
for k, v in sorted(severity_counts.items()):
    md.append(f"- {k}: {v}")
md.append("")
md.append("## Prvih 60 promjena tipa u odnosu na v1")
md.append("")
md.extend(table(changed_rows, 60))
md.append("")
md.append("## Prvih 60 javno blokiranih")
md.append("")
md.extend(table(public_blocked, 60))
md.append("")
md.append("## Prvih 60 javnih PASS kandidata")
md.append("")
md.extend(table(public_ready, 60))
md.append("")
md.append("## Sljedeća urednička odluka")
md.append("")
md.append("Ne raditi javni update. Sljedeći korak je odabrati mali pilot batch po jednom tehnološkom tipu i za svaki recept otvoriti pojedinačni izvorni dosje, recipe.yml i QA izvještaj.")
md.append("")

out_md.write_text("\n".join(md), encoding="utf-8")

print("=== STABILIZED AUDIT COMPLETE ===")
print(f"TOTAL={len(final_rows)}")
print(f"CHANGED_FROM_V1={len(changed_rows)}")
print(f"PUBLIC_PASS={len(public_ready)}")
print(f"PUBLIC_FAIL={len(public_blocked)}")
print("FINAL_TYPE_COUNTS:")
for k, v in sorted(counts.items()):
    print(f"{k}={v}")
print("GATE_COUNTS:")
for k, v in sorted(gate_counts.items()):
    print(f"{k}={v}")
print("TYPE_SOURCE_COUNTS:")
for k, v in sorted(severity_counts.items()):
    print(f"{k}={v}")
print(f"CSV={out_csv}")
print(f"JSON={out_json}")
print(f"SUMMARY={out_md}")
