#!/usr/bin/env python3
import json, re, csv
from pathlib import Path

RAW = Path("/tmp/contract_normalizer_v2_export.json")

FORBIDDEN = [
    "prema receptu",
    "hladna masa",
    "fallback",
    "source-lock",
    "preview",
    "privatni radni recept",
    "fotografija će biti dodana",
]

def try_json(v):
    if not isinstance(v, str):
        return v
    s = v.strip()
    if not s or s[0] not in "{[":
        return v
    try:
        return json.loads(s)
    except Exception:
        return v

def flatten(obj, prefix=""):
    out = []
    if isinstance(obj, dict):
        for k, v in obj.items():
            out.extend(flatten(v, f"{prefix}/{k}" if prefix else str(k)))
    elif isinstance(obj, list):
        for i, v in enumerate(obj):
            out.extend(flatten(v, f"{prefix}[{i}]"))
    else:
        out.append((prefix, obj))
    return out

def as_text(x):
    if isinstance(x, (dict, list)):
        return json.dumps(x, ensure_ascii=False)
    return "" if x is None else str(x)

def collect_text(meta):
    parts = []
    for k in ["_dry_verified_process", "_dry_recipe_sections", "_dry_recipe_full_markdown", "_dry_recipe_data", "_dry_recipe_json", "_dry_dcv5_data"]:
        if k in meta and meta[k]:
            parts.append(as_text(try_json(meta[k])))
    return "\n".join(parts)

def forbidden_terms(text):
    low = text.lower()
    return [f for f in FORBIDDEN if f.lower() in low]

def extract_json_groups(meta):
    vp = try_json(meta.get("_dry_verified_process", ""))
    sections = try_json(meta.get("_dry_recipe_sections", ""))

    groups = {
        "verified_process": vp if isinstance(vp, dict) else {},
        "sections": sections if isinstance(sections, dict) else {},
    }
    return groups

def find_paths(groups, keywords):
    hits = []
    for root_name, obj in groups.items():
        for path, value in flatten(obj):
            txt = as_text(value)
            combined = f"{path} {txt}".lower()
            if any(k.lower() in combined for k in keywords):
                hits.append({
                    "source": root_name,
                    "path": path,
                    "value": txt[:800]
                })
    return hits

def extract_contract(row):
    meta = row["meta"]
    groups = extract_json_groups(meta)
    text = collect_text(meta)

    materials = find_paths(groups, ["raw_materials", "materials", "sirovine", "meso", "slanina", "but", "plećka", "vrat"])
    spices = find_paths(groups, ["spices", "začini", "zacini", "sol", "paprika", "papar", "šećer", "secer", "kim"])
    liquids = find_paths(groups, ["liquids", "tekućine", "tekucine", "voda", "vino", "ml/kg", "amount_l"])
    garlic = find_paths(groups, ["češnjak", "cesnjak", "bijeli luk", "garlic"])
    granulation = find_paths(groups, ["mljevenje", "granulacija", "rešetka", "resetka", "mm", "grinder"])
    casing = find_paths(groups, ["crijeva", "omotač", "omotac", "casing", "diameter", "punjenje"])
    process = find_paths(groups, ["timeline", "process", "faza", "priprema", "miješanje", "punjenje", "dimljenje", "sušenje", "zrenje"])
    errors = find_paths(groups, ["errors", "greške", "greske", "problem", "solution", "rješenje", "rjesenje"])
    safety = find_paths(groups, ["safety", "sigurn", "green", "yellow", "red", "oprez", "odbaci"])

    contract = {
        "contract_version": "dry_recipe_contract_v1.0_field_extracted_v2",
        "source_wp": {
            "post_id": row["ID"],
            "title": row["title"],
            "slug": row["slug"],
            "status": row["status"],
            "url": row["url"],
        },
        "identity": {
            "canonical_name": row["title"].replace("Privatno: ", "").strip(),
            "batch_size_kg": 10,
            "public_status": "IN_REVIEW",
            "qa_status": "CONTRACT_V2_READ_ONLY",
        },
        "materials": materials[:30],
        "spices": spices[:30],
        "liquids": liquids[:30],
        "garlic": garlic[:30],
        "granulation": granulation[:30],
        "casing": casing[:30],
        "process": process[:50],
        "errors": errors[:30],
        "safety": safety[:30],
        "qa": {
            "forbidden_terms": forbidden_terms(text),
            "counts": {
                "materials": len(materials),
                "spices": len(spices),
                "liquids": len(liquids),
                "garlic": len(garlic),
                "granulation": len(granulation),
                "casing": len(casing),
                "process": len(process),
                "errors": len(errors),
                "safety": len(safety),
            }
        }
    }

    critical = ["materials", "spices", "granulation", "casing", "process"]
    missing = [g for g in critical if len(contract[g]) == 0]

    if not missing and len(errors) > 0 and len(safety) > 0 and not contract["qa"]["forbidden_terms"]:
        status = "RENDER_ADAPTER_CANDIDATE"
    elif not missing:
        status = "FIELD_EXTRACTED_NEEDS_QA"
    else:
        status = "FIELD_EXTRACTION_INCOMPLETE"

    contract["qa"]["v2_status"] = status
    contract["qa"]["missing_critical_groups"] = missing

    return contract

def write_csv(path, rows):
    keys = list(rows[0].keys()) if rows else ["empty"]
    with path.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=keys)
        w.writeheader()
        w.writerows(rows)

def main():
    import argparse
    ap = argparse.ArgumentParser()
    ap.add_argument("--outdir", required=True)
    args = ap.parse_args()

    outdir = Path(args.outdir)
    cdir = outdir / "contracts_v2"
    cdir.mkdir(parents=True, exist_ok=True)

    rows = json.loads(RAW.read_text(encoding="utf-8"))
    summary = []

    for row in rows:
        c = extract_contract(row)
        pid = c["source_wp"]["post_id"]
        slug = c["source_wp"]["slug"]
        p = cdir / f"{pid}_{slug}_contract_v2.json"
        p.write_text(json.dumps(c, ensure_ascii=False, indent=2), encoding="utf-8")

        counts = c["qa"]["counts"]
        summary.append({
            "ID": pid,
            "title": c["source_wp"]["title"],
            "status": c["source_wp"]["status"],
            "v2_status": c["qa"]["v2_status"],
            "missing_critical_groups": ";".join(c["qa"]["missing_critical_groups"]),
            "forbidden_terms": ";".join(c["qa"]["forbidden_terms"]),
            "materials": counts["materials"],
            "spices": counts["spices"],
            "liquids": counts["liquids"],
            "garlic": counts["garlic"],
            "granulation": counts["granulation"],
            "casing": counts["casing"],
            "process": counts["process"],
            "errors": counts["errors"],
            "safety": counts["safety"],
            "contract_file": str(p),
        })

    write_csv(outdir / "contract_normalizer_v2_summary.csv", summary)

    counts = {}
    for r in summary:
        counts[r["v2_status"]] = counts.get(r["v2_status"], 0) + 1

    with (outdir / "SAZETAK_CONTRACT_NORMALIZER_v2.md").open("w", encoding="utf-8") as f:
        f.write("# Contract normalizer v2 — field extractor\n\n")
        f.write("Read-only alat. Ne mijenja WordPress. Izvlači stvarne field-path vrijednosti iz `_dry_verified_process` i `_dry_recipe_sections`.\n\n")
        f.write(f"- Obrađeno: {len(summary)}\n")
        for k, v in counts.items():
            f.write(f"- {k}: {v}\n")
        f.write("\n## Sljedeći korak\n\n")
        f.write("Ako postoji barem nekoliko `RENDER_ADAPTER_CANDIDATE` ili `FIELD_EXTRACTED_NEEDS_QA`, može se raditi renderer adapter pilot samo za jedan post.\n")

    print(f"TOTAL={len(summary)}")
    for k, v in counts.items():
        print(f"{k}={v}")

if __name__ == "__main__":
    main()
