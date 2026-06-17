#!/usr/bin/env python3
import json, re, csv
from pathlib import Path

def val(items, path_suffix):
    for it in items:
        if it.get("path","").endswith(path_suffix):
            return str(it.get("value","")).strip()
    return ""

def values_contains(items, needle):
    n = needle.lower()
    return [it for it in items if n in (it.get("path","") + " " + it.get("value","")).lower()]

def main():
    import argparse
    ap = argparse.ArgumentParser()
    ap.add_argument("--contract", required=True)
    ap.add_argument("--outdir", required=True)
    args = ap.parse_args()

    c = json.loads(Path(args.contract).read_text(encoding="utf-8"))
    outdir = Path(args.outdir)
    outdir.mkdir(parents=True, exist_ok=True)

    materials = c.get("materials", [])
    spices = c.get("spices", [])
    garlic = c.get("garlic", [])
    gran = c.get("granulation", [])
    casing = c.get("casing", [])
    process = c.get("process", [])
    errors = c.get("errors", [])
    safety = c.get("safety", [])

    adapter = {
        "post_id": c["source_wp"]["post_id"],
        "title": c["source_wp"]["title"],
        "display_lock": "EXISTING_DISPLAY_ONLY",
        "quick_summary": {
            "batch": "10 kg",
            "type": "Kobasica",
            "casing": val(casing, "casing/diameter_mm") or "NEEDS_VALUE",
        },
        "materials_block": {
            "lean_meat_total_kg": val(materials, "raw_materials/krto_meso_kg_total"),
            "fat_total_kg": val(materials, "raw_materials/slanina_kg_total"),
            "details": [
                {"part": val(materials, "raw_materials/krto_meso_detalji[0]/dio"), "kg": val(materials, "raw_materials/krto_meso_detalji[0]/kg")},
                {"part": val(materials, "raw_materials/krto_meso_detalji[1]/dio"), "kg": val(materials, "raw_materials/krto_meso_detalji[1]/kg")},
                {"part": val(materials, "raw_materials/krto_meso_detalji[2]/dio"), "kg": val(materials, "raw_materials/krto_meso_detalji[2]/kg")},
                {"part": val(materials, "raw_materials/krto_meso_detalji[3]/dio"), "kg": val(materials, "raw_materials/krto_meso_detalji[3]/kg")},
                {"part": val(materials, "raw_materials/slanina_detalji[0]/dio"), "kg": val(materials, "raw_materials/slanina_detalji[0]/kg")},
            ],
        },
        "spices_block": {
            "items_detected": [x for x in spices[:12]],
        },
        "garlic_block": {
            "mode": "procijeđena tekućina od češnjaka" if any("procijeđena" in it.get("value","").lower() for it in garlic) else "NEEDS_CLASSIFICATION",
            "evidence": garlic[:5],
        },
        "granulation_block": {
            "meat_plate_mm": val(gran, "mljevenje/krto_meso_resetka_mm"),
            "fat_processing": val(gran, "mljevenje/slanina_rezanje"),
            "fat_condition": val(gran, "mljevenje/slanina_preduvjet"),
        },
        "casing_block": {
            "type": val(casing, "casing/type"),
            "anatomy": val(casing, "casing/anatomy"),
            "diameter_mm": val(casing, "casing/diameter_mm"),
            "length_cm": val(casing, "casing/length_cm"),
            "soaking_text": next((it.get("value","") for it in casing if "Namakanje" in it.get("value","") or "namakanje" in it.get("value","")), ""),
        },
        "process_blocks": {
            "stuffing": values_contains(process, "punjenje")[:5],
            "smoking": values_contains(process, "dim")[:5],
            "drying_aging": values_contains(process, "sušenje")[:5] + values_contains(process, "zrenje")[:5],
        },
        "errors_block": errors[:3],
        "safety_block": safety[:8],
    }

    forbidden = ["prema receptu", "hladna masa", "fallback", "preview", "source-lock"]
    full = json.dumps(adapter, ensure_ascii=False).lower()
    forbidden_hits = [f for f in forbidden if f in full]

    required = {
        "lean_meat_total_kg": bool(adapter["materials_block"]["lean_meat_total_kg"]),
        "fat_total_kg": bool(adapter["materials_block"]["fat_total_kg"]),
        "meat_plate_mm": bool(adapter["granulation_block"]["meat_plate_mm"]),
        "fat_processing": bool(adapter["granulation_block"]["fat_processing"]),
        "casing_type": bool(adapter["casing_block"]["type"]),
        "casing_diameter": bool(adapter["casing_block"]["diameter_mm"]),
        "stuffing_process": bool(adapter["process_blocks"]["stuffing"]),
        "smoking_process": bool(adapter["process_blocks"]["smoking"]),
        "drying_aging_process": bool(adapter["process_blocks"]["drying_aging"]),
        "errors": bool(adapter["errors_block"]),
    }

    bad = sum(1 for v in required.values() if not v) + len(forbidden_hits)

    adapter["qa"] = {
        "bad": bad,
        "required_checks": required,
        "forbidden_hits": forbidden_hits,
        "pilot_status": "ADAPTER_PAYLOAD_READY" if bad == 0 else "ADAPTER_PAYLOAD_NEEDS_FIX"
    }

    (outdir / "2976_data_adapter_payload_dryrun.json").write_text(json.dumps(adapter, ensure_ascii=False, indent=2), encoding="utf-8")

    with (outdir / "2976_data_adapter_summary.csv").open("w", encoding="utf-8", newline="") as f:
        w = csv.writer(f)
        w.writerow(["field", "status"])
        for k, v in required.items():
            w.writerow([k, "PASS" if v else "FAIL"])
        w.writerow(["forbidden_hits", ";".join(forbidden_hits)])
        w.writerow(["BAD", bad])
        w.writerow(["pilot_status", adapter["qa"]["pilot_status"]])

    md = []
    md.append("# Data adapter dry-run v1 — 2976\n\n")
    md.append("Ovaj dry-run ne mijenja WordPress i ne mijenja dogovoreni prikaz. Provjerava može li contract napuniti postojeće blokove.\n\n")
    md.append(f"- Post ID: {adapter['post_id']}\n")
    md.append(f"- Naziv: {adapter['title']}\n")
    md.append(f"- Status: {adapter['qa']['pilot_status']}\n")
    md.append(f"- BAD: {bad}\n\n")
    md.append("## Kontrole\n\n")
    for k, v in required.items():
        md.append(f"- {k}: {'PASS' if v else 'FAIL'}\n")
    md.append(f"- forbidden_hits: {', '.join(forbidden_hits) if forbidden_hits else 'nema'}\n\n")
    md.append("## Zaključak\n\n")
    if bad == 0:
        md.append("Payload je spreman za pilot povezivanje s postojećim prikazom bez promjene dizajna.\n")
    else:
        md.append("Prije povezivanja treba popraviti mapiranje polja u adapteru.\n")
    (outdir / "SAZETAK_DATA_ADAPTER_DRYRUN_v1.md").write_text("".join(md), encoding="utf-8")

    print("BAD=", bad)
    print("STATUS=", adapter["qa"]["pilot_status"])

if __name__ == "__main__":
    main()
