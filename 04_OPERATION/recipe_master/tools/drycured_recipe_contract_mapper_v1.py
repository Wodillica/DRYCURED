#!/usr/bin/env python3
import csv
import json
import re
import subprocess
from concurrent.futures import ThreadPoolExecutor, as_completed
from pathlib import Path
from collections import Counter

RAW = Path("/tmp/dry_recipe_contract_mapper_raw_v1.json")

FORBIDDEN = [
    "prema receptu",
    "hladna masa",
    "crijeva prema receptu",
    "rešetka prema receptu",
    "omotač prema receptu",
    "trajanje prema receptu",
    "fotografija će biti dodana",
    "fallback",
    "source-lock",
    "privatni radni recept",
    "radni recept",
]

REQUIRED_CONTRACT_GROUPS = [
    "identity",
    "materials",
    "spices",
    "liquids",
    "garlic",
    "granulation",
    "casing",
    "process",
    "smoking",
    "drying",
    "maturation",
    "errors",
    "safety",
    "qa",
]

def norm(s: str) -> str:
    return (s or "").lower()

def has_text(v: str) -> bool:
    return bool(v and str(v).strip())

def count_forbidden(text: str) -> int:
    low = norm(text)
    return sum(low.count(f.lower()) for f in FORBIDDEN)

def forbidden_terms(text: str) -> str:
    low = norm(text)
    hits = [f for f in FORBIDDEN if f.lower() in low]
    return "; ".join(hits)

def classify(row):
    sm = row.get("selected_meta", {})
    full_md = sm.get("_dry_recipe_full_markdown", "") or ""
    sections = sm.get("_dry_recipe_sections", "") or ""
    vp = sm.get("_dry_verified_process", "") or ""
    recipe_data = sm.get("_dry_recipe_data", "") or ""
    json_data = sm.get("_dry_recipe_json", "") or sm.get("_dry_dcv5_data", "") or ""

    combined = "\n".join([full_md, sections, vp, recipe_data, json_data])

    has_full_md = has_text(full_md)
    has_sections = has_text(sections)
    has_vp = has_text(vp)
    has_recipe_data = has_text(recipe_data)
    has_any_json = has_text(json_data)

    has_casing = any(x in norm(combined) for x in ["crijeva", "omotač", "omotac", "casing"])
    has_granulation = any(x in norm(combined) for x in ["mljevenje", "rešetka", "resetka", "granulacija", " mm"])
    has_garlic = any(x in norm(combined) for x in ["češnjak", "cesnjak", "bijeli luk"])
    has_process = any(x in norm(combined) for x in ["punjenje", "dimljenje", "sušenje", "zrenje", "proces"])

    forbidden_count = count_forbidden(combined)
    forbidden = forbidden_terms(combined)

    if has_vp and has_sections and has_full_md:
        readiness = "CONTRACT_BUILD_CANDIDATE_HIGH"
    elif has_vp or has_sections:
        readiness = "CONTRACT_BUILD_CANDIDATE_MEDIUM"
    elif has_full_md:
        readiness = "CONTRACT_BUILD_CANDIDATE_MARKDOWN_ONLY"
    else:
        readiness = "LEGACY_NEEDS_SOURCE_OR_STRUCTURING"

    if row["post_status"] == "publish" and forbidden_count > 0:
        public_risk = "PUBLIC_FALLBACK_RISK"
    elif row["post_status"] == "publish":
        public_risk = "PUBLIC_NO_META_FALLBACK_FOUND"
    else:
        public_risk = "NOT_PUBLIC_OR_PREVIEW"

    if has_granulation and has_casing and has_process:
        contract_gap = "LOW_TO_MEDIUM"
    elif has_process:
        contract_gap = "MEDIUM"
    else:
        contract_gap = "HIGH"

    return {
        "ID": row["ID"],
        "post_title": row["post_title"],
        "post_name": row["post_name"],
        "post_status": row["post_status"],
        "permalink": row["permalink"],
        "meta_key_count": len(row.get("meta_keys", [])),
        "has_full_markdown": int(has_full_md),
        "has_sections": int(has_sections),
        "has_verified_process": int(has_vp),
        "has_recipe_data": int(has_recipe_data),
        "has_any_json_data": int(has_any_json),
        "has_casing_terms": int(has_casing),
        "has_granulation_terms": int(has_granulation),
        "has_garlic_terms": int(has_garlic),
        "has_process_terms": int(has_process),
        "forbidden_meta_count": forbidden_count,
        "forbidden_meta_terms": forbidden,
        "contract_readiness": readiness,
        "contract_gap": contract_gap,
        "public_risk": public_risk,
    }

def curl_html(row):
    url = row.get("permalink", "")
    if row.get("post_status") != "publish" or not url:
        return None

    try:
        p = subprocess.run(
            ["curl", "-k", "-L", "--max-time", "8", "-s", "-w", "\nHTTP_CODE:%{http_code}", url],
            stdout=subprocess.PIPE,
            stderr=subprocess.DEVNULL,
            text=True,
            timeout=12,
        )
        out = p.stdout or ""
        if "\nHTTP_CODE:" in out:
            html, code = out.rsplit("\nHTTP_CODE:", 1)
        else:
            html, code = out, "000"
        return {
            "ID": row["ID"],
            "post_title": row["post_title"],
            "post_name": row["post_name"],
            "url": url,
            "http_code": code.strip(),
            "html_forbidden_count": count_forbidden(html),
            "html_forbidden_terms": forbidden_terms(html),
            "has_granulation_mm_html": int(bool(re.search(r"\b\d{1,2}\s*[–-]?\s*\d{0,2}\s*mm\b", html, flags=re.I))),
            "has_casing_html": int(any(x in norm(html) for x in ["crijeva", "omotač", "omotac"])),
            "has_garlic_html": int(any(x in norm(html) for x in ["češnjak", "cesnjak", "bijeli luk"])),
        }
    except Exception as e:
        return {
            "ID": row["ID"],
            "post_title": row["post_title"],
            "post_name": row["post_name"],
            "url": url,
            "http_code": "ERR",
            "html_forbidden_count": -1,
            "html_forbidden_terms": str(e)[:120],
            "has_granulation_mm_html": 0,
            "has_casing_html": 0,
            "has_garlic_html": 0,
        }

def write_csv(path, rows):
    rows = list(rows)
    if rows:
        keys = list(rows[0].keys())
    else:
        keys = ["empty"]
    with path.open("w", encoding="utf-8", newline="") as f:
        w = csv.DictWriter(f, fieldnames=keys)
        w.writeheader()
        w.writerows(rows)

def main():
    import argparse
    ap = argparse.ArgumentParser()
    ap.add_argument("--outdir", required=True)
    ap.add_argument("--html", action="store_true")
    ap.add_argument("--workers", type=int, default=6)
    args = ap.parse_args()

    outdir = Path(args.outdir)
    outdir.mkdir(parents=True, exist_ok=True)

    data = json.loads(RAW.read_text(encoding="utf-8"))
    mapped = [classify(r) for r in data]

    write_csv(outdir / "recipe_contract_mapper_v1.csv", mapped)

    readiness = Counter(r["contract_readiness"] for r in mapped)
    status_counts = Counter(r["post_status"] for r in mapped)
    public_risk = Counter(r["public_risk"] for r in mapped)
    gap_counts = Counter(r["contract_gap"] for r in mapped)

    write_csv(outdir / "contract_readiness_counts.csv", [{"status": k, "count": v} for k, v in readiness.items()])
    write_csv(outdir / "post_status_counts.csv", [{"status": k, "count": v} for k, v in status_counts.items()])
    write_csv(outdir / "public_risk_counts.csv", [{"status": k, "count": v} for k, v in public_risk.items()])
    write_csv(outdir / "contract_gap_counts.csv", [{"status": k, "count": v} for k, v in gap_counts.items()])

    public_risks = [r for r in mapped if r["public_risk"] == "PUBLIC_FALLBACK_RISK"]
    write_csv(outdir / "public_meta_fallback_risks.csv", public_risks)

    candidates = [r for r in mapped if r["contract_readiness"] in ("CONTRACT_BUILD_CANDIDATE_HIGH", "CONTRACT_BUILD_CANDIDATE_MEDIUM")]
    candidates = sorted(candidates, key=lambda r: (r["contract_gap"], -r["has_verified_process"], r["ID"]))
    write_csv(outdir / "first_contract_batch_candidates.csv", candidates[:50])

    html_rows = []
    if args.html:
        publish_rows = [r for r in data if r.get("post_status") == "publish"]
        with ThreadPoolExecutor(max_workers=args.workers) as ex:
            futs = [ex.submit(curl_html, r) for r in publish_rows]
            for fut in as_completed(futs):
                res = fut.result()
                if res:
                    html_rows.append(res)
        html_rows = sorted(html_rows, key=lambda r: int(r["ID"]))
        write_csv(outdir / "html_qa_published.csv", html_rows)
        write_csv(outdir / "public_html_fallback_risks.csv", [r for r in html_rows if r["html_forbidden_count"] > 0])

    summary = outdir / "SAZETAK_RECIPE_CONTRACT_MAPPER_v1.md"
    with summary.open("w", encoding="utf-8") as f:
        f.write("# Read-only recipe contract mapper v1\n\n")
        f.write("Ovaj izvještaj ne mijenja WordPress. Služi za procjenu spremnosti recepata za `DRYCURED_RECIPE_DATA_CONTRACT_v1.0`.\n\n")
        f.write("## Sažetak\n\n")
        f.write(f"- Ukupno dry_recipe zapisa: {len(mapped)}\n")
        for k, v in status_counts.items():
            f.write(f"- Post status {k}: {v}\n")
        f.write("\n## Contract readiness\n\n")
        for k, v in readiness.items():
            f.write(f"- {k}: {v}\n")
        f.write("\n## Public risk\n\n")
        for k, v in public_risk.items():
            f.write(f"- {k}: {v}\n")
        f.write("\n## Contract gap\n\n")
        for k, v in gap_counts.items():
            f.write(f"- {k}: {v}\n")
        if args.html:
            f.write("\n## HTML QA published\n\n")
            f.write(f"- HTML provjerenih publish URL-ova: {len(html_rows)}\n")
            f.write(f"- Publish HTML s fallback rizikom: {sum(1 for r in html_rows if r['html_forbidden_count'] > 0)}\n")
        f.write("\n## Izlazi\n\n")
        f.write("- recipe_contract_mapper_v1.csv\n")
        f.write("- first_contract_batch_candidates.csv\n")
        f.write("- public_meta_fallback_risks.csv\n")
        f.write("- html_qa_published.csv\n")
        f.write("- public_html_fallback_risks.csv\n")

    print(f"TOTAL={len(mapped)}")
    print("READINESS=" + "; ".join(f"{k}:{v}" for k, v in readiness.items()))
    print("PUBLIC_RISK=" + "; ".join(f"{k}:{v}" for k, v in public_risk.items()))
    if args.html:
        print(f"HTML_CHECKED={len(html_rows)}")
        print(f"HTML_FALLBACK_RISKS={sum(1 for r in html_rows if r['html_forbidden_count'] > 0)}")

if __name__ == "__main__":
    main()
