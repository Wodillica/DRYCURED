#!/usr/bin/env python3
import json, re, csv
from pathlib import Path

RAW = Path("/tmp/contract_normalizer_candidates.json")

FORBIDDEN_PUBLIC = [
    "prema receptu",
    "hladna masa",
    "fallback",
    "source-lock",
    "preview",
    "privatni radni recept",
    "fotografija će biti dodana",
]

def try_json(s):
    if not isinstance(s, str):
        return {}
    t = s.strip()
    if not t or t[0] not in "{[":
        return {}
    try:
        return json.loads(t)
    except Exception:
        return {}

def text_has(s, pattern):
    return re.search(pattern, s or "", flags=re.I | re.U) is not None

def find_forbidden(text):
    low = (text or "").lower()
    return [x for x in FORBIDDEN_PUBLIC if x.lower() in low]

def build_contract(row):
    meta = row.get("meta", {})
    md = meta.get("_dry_recipe_full_markdown", "") or ""
    sections_raw = meta.get("_dry_recipe_sections", "") or ""
    vp_raw = meta.get("_dry_verified_process", "") or ""

    sections = try_json(sections_raw)
    vp = try_json(vp_raw)

    combined = "\n".join([md, sections_raw, vp_raw])

    contract = {
        "contract_version": "dry_recipe_contract_v1.0",
        "source_wp": {
            "post_id": row["ID"],
            "title": row["post_title"],
            "slug": row["post_name"],
            "status": row["post_status"],
            "permalink": row["permalink"],
        },
        "identity": {
            "canonical_name": row["post_title"].replace("Privatno: ", "").strip(),
            "batch_size_kg": vp.get("batch_size_kg", 10) if isinstance(vp, dict) else 10,
            "public_status": "IN_REVIEW",
            "qa_status": "CONTRACT_DRAFT_READ_ONLY",
        },
        "materials": {
            "status": "detected" if text_has(combined, r"kg|meso|slanina|but|plećka|vrat") else "missing",
            "raw_source_available": bool(text_has(combined, r"kg|meso|slanina|but|plećka|vrat")),
        },
        "spices": {
            "status": "detected" if text_has(combined, r"sol|paprika|papar|šećer|secer|kim|češnjak|cesnjak") else "missing",
        },
        "liquids": {
            "status": "detected" if text_has(combined, r"voda|vino|tekućina|tekucina|ml/kg|litra|0,\d+\s*L") else "not_detected",
        },
        "garlic": {
            "status": "detected" if text_has(combined, r"češnjak|cesnjak|bijeli luk") else "not_detected",
            "needs_classification": bool(text_has(combined, r"češnjak|cesnjak|bijeli luk")),
        },
        "granulation": {
            "status": "detected" if text_has(combined, r"\b\d{1,2}\s*(–|-)?\s*\d{0,2}\s*mm\b|rešetka|resetka|mljevenje") else "missing_or_needs_review",
            "has_mm": bool(text_has(combined, r"\b\d{1,2}\s*(–|-)?\s*\d{0,2}\s*mm\b")),
        },
        "casing": {
            "status": "detected" if text_has(combined, r"crijev|omotač|omotac|kate|kulenovo crijevo") else "missing_or_not_applicable",
            "has_diameter_mm": bool(text_has(combined, r"Ø?\s*\d{2}\s*(–|-)\s*\d{2}\s*mm|\d{2}\s*mm")),
            "needs_soaking_details": bool(text_has(combined, r"crijev|omotač|omotac")),
        },
        "process": {
            "status": "detected" if text_has(combined, r"priprema|mljevenje|miješanje|punjenje|dimljenje|sušenje|zrenje") else "missing",
        },
        "smoking": {
            "status": "detected" if text_has(combined, r"dim|dimljenje|pušnica|pusnica") else "not_used_or_missing",
        },
        "drying": {
            "status": "detected" if text_has(combined, r"sušenje|susenje|sušiti|susiti") else "missing",
        },
        "maturation": {
            "status": "detected" if text_has(combined, r"zrenje|dozrijev|matur") else "missing",
        },
        "errors": {
            "status": "detected" if text_has(combined, r"problem|greška|greska|rješenje|rjesenje") else "missing",
        },
        "safety": {
            "status": "detected" if text_has(combined, r"sigurn|rizik|oprez|odbaci|ne konzumirati") else "missing",
        },
        "qa": {
            "forbidden_public_terms": find_forbidden(combined),
            "source_mentions_allowed_internal_only": True,
            "public_ready": False,
        },
        "raw_presence": {
            "has_full_markdown": bool(md.strip()),
            "has_sections": bool(sections_raw.strip()),
            "has_verified_process": bool(vp_raw.strip()),
        },
    }

    missing = []
    for key in ["materials", "spices", "granulation", "casing", "process", "smoking", "drying", "maturation", "errors", "safety"]:
        st = contract[key]["status"]
        if "missing" in st or "needs_review" in st:
            missing.append(key)

    if contract["qa"]["forbidden_public_terms"]:
        missing.append("forbidden_public_terms")

    contract["qa"]["missing_or_review_groups"] = sorted(set(missing))
    contract["qa"]["contract_build_status"] = "PASS_DRAFT" if len(missing) <= 3 else "NEEDS_REVIEW"

    return contract

def main():
    import argparse
    ap = argparse.ArgumentParser()
    ap.add_argument("--outdir", required=True)
    args = ap.parse_args()

    outdir = Path(args.outdir)
    contracts_dir = outdir / "contracts"
    contracts_dir.mkdir(parents=True, exist_ok=True)

    rows = json.loads(RAW.read_text(encoding="utf-8"))
    summary_rows = []

    for row in rows:
        c = build_contract(row)
        pid = c["source_wp"]["post_id"]
        slug = c["source_wp"]["slug"]
        path = contracts_dir / f"{pid}_{slug}_contract_v1.json"
        path.write_text(json.dumps(c, ensure_ascii=False, indent=2), encoding="utf-8")

        summary_rows.append({
            "ID": pid,
            "title": c["source_wp"]["title"],
            "slug": slug,
            "status": c["source_wp"]["status"],
            "contract_build_status": c["qa"]["contract_build_status"],
            "missing_or_review_groups": ";".join(c["qa"]["missing_or_review_groups"]),
            "forbidden_terms": ";".join(c["qa"]["forbidden_public_terms"]),
            "has_granulation_mm": int(c["granulation"]["has_mm"]),
            "has_casing_diameter": int(c["casing"]["has_diameter_mm"]),
            "has_verified_process": int(c["raw_presence"]["has_verified_process"]),
            "contract_file": str(path),
        })

    with (outdir / "contract_normalizer_summary.csv").open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=list(summary_rows[0].keys()))
        w.writeheader()
        w.writerows(summary_rows)

    pass_count = sum(1 for r in summary_rows if r["contract_build_status"] == "PASS_DRAFT")
    review_count = len(summary_rows) - pass_count

    with (outdir / "SAZETAK_CONTRACT_NORMALIZER_v1.md").open("w", encoding="utf-8") as f:
        f.write("# Contract normalizer v1 — read-only pilot\n\n")
        f.write("Ovaj korak ne mijenja WordPress. Generira draft contract JSON datoteke za prve kandidate.\n\n")
        f.write(f"- Kandidata obrađeno: {len(summary_rows)}\n")
        f.write(f"- PASS_DRAFT: {pass_count}\n")
        f.write(f"- NEEDS_REVIEW: {review_count}\n\n")
        f.write("## Sljedeći korak\n\n")
        f.write("Ako su contract draftovi smisleni, radi se renderer adapter koji čita contract umjesto fallback vrijednosti.\n")

    print(f"TOTAL={len(summary_rows)}")
    print(f"PASS_DRAFT={pass_count}")
    print(f"NEEDS_REVIEW={review_count}")

if __name__ == "__main__":
    main()
