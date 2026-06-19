#!/usr/bin/env python3
from pathlib import Path
from datetime import datetime, timezone
import csv
import hashlib
import json
import os
import sys

if len(sys.argv) < 5:
    print("Usage: dc_recipe_system_file_inventory_v1.py scan REPO WEBROOT REPORT_DIR", file=sys.stderr)
    print("   or: dc_recipe_system_file_inventory_v1.py report REPO WEBROOT REPORT_DIR", file=sys.stderr)
    sys.exit(1)

mode = sys.argv[1]
repo = Path(sys.argv[2])
webroot = Path(sys.argv[3])
report_dir = Path(sys.argv[4])

master = repo / "04_OPERATION" / "recipe_master"
tools_dir = master / "tools"
reports_dir = master / "reports"
dossiers_dir = master / "dossiers"

def sha256_file(path: Path, max_bytes=None):
    h = hashlib.sha256()
    total = 0
    with path.open("rb") as f:
        while True:
            chunk = f.read(1024 * 1024)
            if not chunk:
                break
            if max_bytes is not None and total + len(chunk) > max_bytes:
                chunk = chunk[: max_bytes - total]
            h.update(chunk)
            total += len(chunk)
            if max_bytes is not None and total >= max_bytes:
                break
    return h.hexdigest()

def rel(path: Path):
    try:
        return str(path.relative_to(repo))
    except Exception:
        return str(path)

def safe_read(path: Path, limit=300000):
    try:
        with path.open("rb") as f:
            data = f.read(limit)
        return data.decode("utf-8", errors="ignore")
    except Exception:
        return ""

def classify_file(path: Path):
    r = rel(path)
    name = path.name.lower()
    ext = path.suffix.lower()
    low = r.lower()

    if "/_legacy" in low or "/legacy" in low or "/archive" in low:
        return "LEGACY_OR_ARCHIVE"
    if ext in [".php", ".py", ".sh", ".bash"]:
        if "drycured-recipe-view" in name or "recipe-view" in name:
            return "RENDERER_UI_PROTECTED_SCRIPT"
        return "SCRIPT"
    if ext in [".md", ".markdown"]:
        if "/dossiers/" in low:
            return "DOSSIER_MARKDOWN"
        if "/reports/" in low:
            return "REPORT_MARKDOWN"
        if any(x in low for x in ["recipe", "recept", "canon", "source", "batch"]):
            return "RECIPE_SOURCE_CANDIDATE_MD"
        return "MARKDOWN_UNKNOWN"
    if ext in [".json", ".yml", ".yaml", ".csv"]:
        if "/dossiers/" in low:
            return "DOSSIER_DATA"
        if "/reports/" in low:
            return "REPORT_DATA"
        if any(x in low for x in ["recipe", "recept", "source", "batch", "canon", "clean", "rebuild"]):
            return "RECIPE_SOURCE_CANDIDATE_DATA"
        return "DATA_UNKNOWN"
    if ext in [".sql", ".dump", ".gz", ".zip", ".tar"]:
        return "BACKUP_OR_ARCHIVE_CHECK"
    return "OTHER"

WRITE_PATTERNS = [
    "wp_insert_post",
    "wp_update_post",
    "update_post_meta",
    "delete_post_meta",
    "wp_delete_post",
    "wp_trash_post",
    "set_post_thumbnail",
    "wp_set_post_terms",
    "wp_set_object_terms",
    "wp db import",
    "mysql ",
    "insert into wp_",
    "update wp_",
    "delete from wp_",
]

READ_PATTERNS = [
    "get_post(",
    "get_posts(",
    "get_post_meta",
    "wp_remote_get",
    "wp post list",
    "wp eval-file",
    "wp db export",
]

def classify_script(path: Path):
    text = safe_read(path, limit=500000)
    low = text.lower()
    r = rel(path)
    name = path.name.lower()

    write_hits = [p for p in WRITE_PATTERNS if p in low]
    read_hits = [p for p in READ_PATTERNS if p in low]

    if "drycured-recipe-view" in name or "recipe-view" in name:
        category = "RENDERER_UI_PROTECTED"
    elif any(x in write_hits for x in ["wp_insert_post", "wp_update_post", "update_post_meta", "delete_post_meta", "wp_delete_post", "wp_trash_post", "set_post_thumbnail", "wp_set_post_terms", "wp_set_object_terms"]):
        category = "WP_WRITE_CAPABLE"
    elif any(x in write_hits for x in ["wp db import", "mysql ", "insert into wp_", "update wp_", "delete from wp_"]):
        category = "DB_WRITE_RISK"
    elif read_hits:
        category = "READ_ONLY_OR_AUDIT_LIKELY"
    else:
        category = "SCRIPT_UNKNOWN_REVIEW"

    if "/reports/" in r.lower() or "/dossiers/" in r.lower():
        location_class = "GENERATED_WORK_ARTIFACT"
    elif "/tools/" in r.lower():
        location_class = "TOOL"
    elif "/wp-content/mu-plugins/" in r.lower():
        location_class = "MU_PLUGIN_OR_RENDERER"
    else:
        location_class = "OTHER_SCRIPT_LOCATION"

    return {
        "path": r,
        "category": category,
        "location_class": location_class,
        "write_hits": "; ".join(write_hits),
        "read_hits": "; ".join(read_hits),
        "size_bytes": path.stat().st_size,
        "modified_utc": datetime.fromtimestamp(path.stat().st_mtime, timezone.utc).isoformat(),
        "sha256": sha256_file(path) if path.stat().st_size < 5_000_000 else sha256_file(path, max_bytes=5_000_000) + "_partial5MB",
    }

def scan_files():
    report_dir.mkdir(parents=True, exist_ok=True)

    roots = [
        master,
        repo / "wp-content" / "mu-plugins",
        repo / "wp-content" / "plugins",
    ]

    live_protected = [
        webroot / "wp-content" / "mu-plugins" / "drycured-recipe-view-v1.php",
        webroot / "wp-content" / "mu-plugins" / "drycured-granulation-display-core-safe-v11.php",
    ]

    skip_dirs = {".git", "node_modules", "vendor", "__pycache__", ".cache"}

    files = []
    scripts = []
    protected_ui = []

    for root in roots:
        if not root.exists():
            continue
        for dirpath, dirnames, filenames in os.walk(root):
            dirnames[:] = [d for d in dirnames if d not in skip_dirs]
            dpath = Path(dirpath)

            for fn in filenames:
                path = dpath / fn
                try:
                    st = path.stat()
                except Exception:
                    continue

                ext = path.suffix.lower()
                category = classify_file(path)

                row = {
                    "path": rel(path),
                    "name": path.name,
                    "extension": ext,
                    "category": category,
                    "size_bytes": st.st_size,
                    "modified_utc": datetime.fromtimestamp(st.st_mtime, timezone.utc).isoformat(),
                    "sha256": sha256_file(path) if st.st_size < 2_000_000 and ext in [".md", ".json", ".yml", ".yaml", ".csv", ".php", ".py", ".sh"] else "",
                }
                files.append(row)

                if ext in [".php", ".py", ".sh", ".bash"]:
                    scripts.append(classify_script(path))

    for path in live_protected:
        if path.exists():
            st = path.stat()
            protected_ui.append({
                "path": str(path),
                "exists": "yes",
                "size_bytes": st.st_size,
                "modified_utc": datetime.fromtimestamp(st.st_mtime, timezone.utc).isoformat(),
                "sha256": sha256_file(path) if st.st_size < 5_000_000 else sha256_file(path, max_bytes=5_000_000) + "_partial5MB",
                "protection_status": "DO_NOT_MODIFY_IN_INVENTORY",
            })
        else:
            protected_ui.append({
                "path": str(path),
                "exists": "no",
                "size_bytes": "",
                "modified_utc": "",
                "sha256": "",
                "protection_status": "EXPECTED_OR_OPTIONAL_FILE_NOT_FOUND",
            })

    files.sort(key=lambda x: x["path"])
    scripts.sort(key=lambda x: x["path"])

    file_csv = report_dir / "file_inventory.csv"
    with file_csv.open("w", encoding="utf-8", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=["path", "name", "extension", "category", "size_bytes", "modified_utc", "sha256"])
        writer.writeheader()
        writer.writerows(files)

    script_csv = report_dir / "script_inventory.csv"
    with script_csv.open("w", encoding="utf-8", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=["path", "category", "location_class", "write_hits", "read_hits", "size_bytes", "modified_utc", "sha256"])
        writer.writeheader()
        writer.writerows(scripts)

    protected_csv = report_dir / "protected_ui_inventory.csv"
    with protected_csv.open("w", encoding="utf-8", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=["path", "exists", "size_bytes", "modified_utc", "sha256", "protection_status"])
        writer.writeheader()
        writer.writerows(protected_ui)

    category_counts = {}
    for row in files:
        category_counts[row["category"]] = category_counts.get(row["category"], 0) + 1

    script_counts = {}
    for row in scripts:
        script_counts[row["category"]] = script_counts.get(row["category"], 0) + 1

    summary = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "mode": "READ_ONLY_FILE_INVENTORY",
        "ui_protected": True,
        "wordpress_write_allowed": False,
        "repo": str(repo),
        "webroot": str(webroot),
        "master_dir": str(master),
        "file_total": len(files),
        "script_total": len(scripts),
        "category_counts": category_counts,
        "script_category_counts": script_counts,
        "protected_ui": protected_ui,
        "outputs": {
            "file_inventory_csv": str(file_csv),
            "script_inventory_csv": str(script_csv),
            "protected_ui_inventory_csv": str(protected_csv),
        }
    }

    (report_dir / "file_inventory_summary.json").write_text(json.dumps(summary, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

    print("=== FILE INVENTORY COMPLETE ===")
    print(f"FILE_TOTAL={len(files)}")
    print(f"SCRIPT_TOTAL={len(scripts)}")
    for k, v in sorted(category_counts.items()):
        print(f"FILE_CATEGORY_{k}={v}")
    for k, v in sorted(script_counts.items()):
        print(f"SCRIPT_CATEGORY_{k}={v}")
    print(f"FILE_INVENTORY={file_csv}")
    print(f"SCRIPT_INVENTORY={script_csv}")
    print(f"PROTECTED_UI_INVENTORY={protected_csv}")

def compile_report():
    file_summary_path = report_dir / "file_inventory_summary.json"
    wp_summary_path = report_dir / "wp_recipe_inventory_summary.json"

    if not file_summary_path.exists():
        raise SystemExit("FAIL: file_inventory_summary.json ne postoji.")
    if not wp_summary_path.exists():
        raise SystemExit("FAIL: wp_recipe_inventory_summary.json ne postoji.")

    file_summary = json.loads(file_summary_path.read_text(encoding="utf-8"))
    wp_summary = json.loads(wp_summary_path.read_text(encoding="utf-8"))

    script_csv = report_dir / "script_inventory.csv"
    script_rows = []
    if script_csv.exists():
        with script_csv.open("r", encoding="utf-8") as f:
            reader = csv.DictReader(f)
            script_rows = list(reader)

    wp_write_scripts = [r for r in script_rows if r["category"] == "WP_WRITE_CAPABLE"]
    db_write_scripts = [r for r in script_rows if r["category"] == "DB_WRITE_RISK"]
    renderer_scripts = [r for r in script_rows if r["category"] == "RENDERER_UI_PROTECTED"]
    unknown_scripts = [r for r in script_rows if r["category"] == "SCRIPT_UNKNOWN_REVIEW"]

    report = []
    report.append("# Drycured recipe system inventory v1")
    report.append("")
    report.append("Status: **READ_ONLY_INVENTORY_CREATED**")
    report.append("")
    report.append("Ova inventura ne mijenja WordPress, ne mijenja javne recepte i ne mijenja sučelje prikaza recepata.")
    report.append("")
    report.append("## Zaštita sučelja")
    report.append("")
    report.append("- Sučelje prikaza recepata ostaje netaknuto.")
    report.append("- Renderer/MU-plugin datoteke su u ovoj inventuri samo pročitane i hashirane.")
    report.append("- Obustava prikaza recepata, ako kasnije bude potrebna, mora biti zaseban kontrolirani korak, ne dio inventure.")
    report.append("")
    report.append("### Zaštićene UI datoteke")
    report.append("")
    report.append("| Putanja | Postoji | Veličina | SHA256 | Status |")
    report.append("|---|---|---:|---|---|")
    for item in file_summary.get("protected_ui", []):
        report.append(f"| `{item['path']}` | {item['exists']} | {item['size_bytes']} | `{item['sha256']}` | {item['protection_status']} |")

    report.append("")
    report.append("## Sažetak datoteka")
    report.append("")
    report.append(f"- Ukupno inventariziranih datoteka: `{file_summary['file_total']}`")
    report.append(f"- Ukupno inventariziranih skripti: `{file_summary['script_total']}`")
    report.append("")
    report.append("| Kategorija datoteka | Broj |")
    report.append("|---|---:|")
    for k, v in sorted(file_summary.get("category_counts", {}).items()):
        report.append(f"| {k} | {v} |")

    report.append("")
    report.append("## Sažetak skripti")
    report.append("")
    report.append("| Kategorija skripti | Broj |")
    report.append("|---|---:|")
    for k, v in sorted(file_summary.get("script_category_counts", {}).items()):
        report.append(f"| {k} | {v} |")

    report.append("")
    report.append("### Skripte koje mogu pisati u WordPress")
    report.append("")
    if wp_write_scripts:
        report.append("| Putanja | Write hitovi | Lokacija |")
        report.append("|---|---|---|")
        for row in wp_write_scripts[:80]:
            report.append(f"| `{row['path']}` | {row['write_hits']} | {row['location_class']} |")
        if len(wp_write_scripts) > 80:
            report.append(f"| ... | prikazano 80 od {len(wp_write_scripts)} | ... |")
    else:
        report.append("Nema detektiranih WP write skripti u inventariziranom opsegu.")

    report.append("")
    report.append("### Skripte s DB write rizikom")
    report.append("")
    if db_write_scripts:
        report.append("| Putanja | Hitovi | Lokacija |")
        report.append("|---|---|---|")
        for row in db_write_scripts[:80]:
            report.append(f"| `{row['path']}` | {row['write_hits']} | {row['location_class']} |")
    else:
        report.append("Nema detektiranih DB write rizika u inventariziranom opsegu.")

    report.append("")
    report.append("### Renderer/UI skripte pod zaštitom")
    report.append("")
    if renderer_scripts:
        report.append("| Putanja | Lokacija | SHA256 |")
        report.append("|---|---|---|")
        for row in renderer_scripts:
            report.append(f"| `{row['path']}` | {row['location_class']} | `{row['sha256']}` |")
    else:
        report.append("Nije pronađena renderer/UI skripta u skeniranom repo opsegu; pogledati `protected_ui_inventory.csv` za live datoteke.")

    report.append("")
    report.append("## WordPress dry_recipe inventura")
    report.append("")
    report.append(f"- Ukupno dry_recipe postova: `{wp_summary['total_dry_recipe_posts']}`")
    report.append(f"- Privatni preview cloneovi: `{wp_summary['preview_clone_total']}`")
    report.append(f"- Javni publish recepti: `{wp_summary['status_counts'].get('publish', 0)}`")
    report.append(f"- Private recepti: `{wp_summary['status_counts'].get('private', 0)}`")
    report.append(f"- Draft recepti: `{wp_summary['status_counts'].get('draft', 0)}`")
    report.append(f"- Bez `_dry_recipe_sections`: `{wp_summary['missing_sections_total']}`")
    report.append(f"- Bez `_dry_verified_process`: `{wp_summary['missing_verified_process_total']}`")
    report.append(f"- Bez `_dry_recipe_full_markdown`: `{wp_summary['missing_full_markdown_total']}`")
    report.append("")
    report.append("| Status | Broj |")
    report.append("|---|---:|")
    for k, v in sorted(wp_summary.get("status_counts", {}).items()):
        report.append(f"| {k} | {v} |")

    report.append("")
    report.append("## Procjena kaosa / preklapanja")
    report.append("")
    report.append("- Postoji više slojeva podataka: reporti, dosjei, JSON/YML/MD i skripte.")
    report.append("- Pojedinačne skripte po receptu treba tretirati kao radne pokušaje, ne kao trajni proizvodni sustav.")
    report.append("- Glavni smjer treba biti: jedan standardni MD format → jedan parser → jedan QA izvještaj → jedan preview generator.")
    report.append("- Sve WP write-capable skripte treba staviti u karantenu dok se ne zaključi novi master workflow.")
    report.append("")
    report.append("## Preporučena privremena klasifikacija")
    report.append("")
    report.append("| Oznaka | Značenje | Radnja |")
    report.append("|---|---|---|")
    report.append("| ACTIVE / KEEP | Renderer i nužni read-only alati | Ne dirati bez dogovora |")
    report.append("| REVIEW | Skripte i dokumenti koji mogu biti korisni | Ručno pregledati prije uporabe |")
    report.append("| LEGACY / ARCHIVE | Stari pokušaji, batch reporti, privremeni dosjei | Premjestiti u `_legacy_archive` tek nakon potvrde |")
    report.append("| DANGEROUS / DO NOT RUN | Skripte s WP/DB write funkcijama | Ne pokretati dok nisu auditirane |")
    report.append("| UNKNOWN | Nejasni dokumenti i skripte | Ne koristiti u produkciji |")
    report.append("")
    report.append("## Output datoteke")
    report.append("")
    report.append("- `file_inventory.csv`")
    report.append("- `script_inventory.csv`")
    report.append("- `protected_ui_inventory.csv`")
    report.append("- `wp_recipe_posts_inventory.csv`")
    report.append("- `wp_recipe_meta_inventory.csv`")
    report.append("- `wp_recipe_inventory_summary.json`")
    report.append("- `file_inventory_summary.json`")
    report.append("")
    report.append("## Sljedeći korak")
    report.append("")
    report.append("Ne raditi više pojedinačne recepte. Sljedeći korak je pregledati ovaj inventarni izvještaj i zaključati: jednu izvornu mapu MD recepata, jedan kanonski MD format, jedan parser i jedan preview workflow.")
    report.append("")

    report_path = report_dir / "RECIPE_SYSTEM_INVENTORY_REPORT_v1.md"
    report_path.write_text("\n".join(report), encoding="utf-8")

    print("=== FINAL REPORT CREATED ===")
    print(f"REPORT={report_path}")
    print(f"WP_WRITE_SCRIPT_TOTAL={len(wp_write_scripts)}")
    print(f"DB_WRITE_RISK_SCRIPT_TOTAL={len(db_write_scripts)}")
    print(f"UNKNOWN_SCRIPT_TOTAL={len(unknown_scripts)}")
    print(f"RENDERER_UI_PROTECTED_SCRIPT_TOTAL={len(renderer_scripts)}")

if mode == "scan":
    scan_files()
elif mode == "report":
    compile_report()
else:
    raise SystemExit(f"FAIL: nepoznat mode: {mode}")
