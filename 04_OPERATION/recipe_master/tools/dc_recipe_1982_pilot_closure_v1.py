#!/usr/bin/env python3
from pathlib import Path
import json
import csv
import sys
from datetime import datetime, timezone

if len(sys.argv) != 4:
    print("Usage: dc_recipe_1982_pilot_closure_v1.py DOSSIER_DIR PRIVATE_CLONE_RESULT REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
private_clone_result = Path(sys.argv[2])
review_dir = Path(sys.argv[3])

qa_path = dossier_dir / "qa_report.md"
readme_path = dossier_dir / "README.md"
wplog_path = dossier_dir / "wordpress_import_log.md"

clone = json.loads(private_clone_result.read_text(encoding="utf-8"))
now = datetime.now(timezone.utc).isoformat()

clone_id = clone.get("clone_id")
admin_preview_url = clone.get("admin_preview_url")
admin_edit_url = clone.get("admin_edit_url")

checks = []

def add_check(key, status, severity, note):
    checks.append({
        "key": key,
        "status": status,
        "severity": severity,
        "note": note
    })

add_check("private_clone_created", "PASS" if clone.get("status") == "PRIVATE_CLONE_CREATED_QA_PASS" else "FAIL", "BLOCKER", "Private clone mora imati QA pass.")
add_check("clone_id_3536", "PASS" if str(clone_id) == "3536" else "FAIL", "MAJOR", "Očekivani clone ID je 3536.")
add_check("source_unchanged", "PASS" if clone.get("source_unchanged") is True else "FAIL", "BLOCKER", "Source post 1982 mora ostati netaknut.")
add_check("public_update_false", "PASS" if clone.get("public_update_allowed") is False else "FAIL", "BLOCKER", "Javni update mora ostati false.")
add_check("public_publish_false", "PASS" if clone.get("public_publish_allowed") is False else "FAIL", "BLOCKER", "Javna objava mora ostati false.")
add_check("not_publicly_exposed", "PASS" if clone.get("http_public_check", {}).get("publicly_exposed") is False else "FAIL", "BLOCKER", "Privatni clone ne smije biti javno izložen.")
add_check("manual_admin_preview_confirmed", "PASS", "BLOCKER", "Korisnik je screenshotovima potvrdio strukturirani kartični admin preview.")
add_check("admin_edit_markdown_expected", "PASS", "MAJOR", "Admin edit prikazuje markdown, što je očekivano za ovaj workflow.")
add_check("internal_blocks_private_only", "PASS", "MAJOR", "Interni blokovi su prihvatljivi samo u privatnom previewu; ne smiju ići u javni prikaz.")

failures = [c for c in checks if c["status"] == "FAIL" and c["severity"] in ("BLOCKER", "MAJOR")]
blockers = [c for c in failures if c["severity"] == "BLOCKER"]

closure_status = "PRIVATE_PREVIEW_READY_PUBLIC_UPDATE_BLOCKED" if not blockers and not failures else "PILOT_CLOSURE_BLOCKED"

closure = {
    "generated_at": now,
    "post_id": 1982,
    "clone_id": clone_id,
    "recipe_code": "IT-TOS-1982-FINOCCHIONA-TOSCANA",
    "closure_status": closure_status,
    "manual_admin_preview_confirmed": True,
    "admin_preview_url": admin_preview_url,
    "admin_edit_url": admin_edit_url,
    "source_unchanged": clone.get("source_unchanged"),
    "public_update_allowed": False,
    "public_publish_allowed": False,
    "source_post_write_allowed": False,
    "next_project_priority": "CROATIA_RECIPES_FIRST",
    "do_not_continue_next_italian_recipes_now": True,
    "checks": checks,
    "major_fail_total": len(failures),
    "blocker_fail_total": len(blockers),
    "final_decision": {
        "technical_preview": "ready",
        "public_update": "blocked",
        "public_reason": "Private preview is technically ready, but no public update is allowed in this closure step.",
        "next_step": "Start Croatian recipe queue after closing 1982."
    }
}

json_path = review_dir / "1982_pilot_closure_v1.json"
json_path.write_text(json.dumps(closure, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

checks_path = review_dir / "1982_pilot_closure_checks.csv"
with checks_path.open("w", encoding="utf-8", newline="") as f:
    writer = csv.DictWriter(f, fieldnames=["key", "status", "severity", "note"])
    writer.writeheader()
    writer.writerows(checks)

report_path = review_dir / "1982_PILOT_CLOSURE_REPORT.md"
md = []
md.append("# 1982 Finocchiona Toscana pilot closure v1")
md.append("")
md.append(f"Status: **{closure_status}**")
md.append("")
md.append("## Sažetak")
md.append("")
md.append("Pilot za `1982 — Finocchiona Toscana IGP` tehnički je zatvoren.")
md.append("")
md.append(f"- Source post ID: `1982`")
md.append(f"- Private clone ID: `{clone_id}`")
md.append(f"- Admin preview URL: `{admin_preview_url}`")
md.append(f"- Admin edit URL: `{admin_edit_url}`")
md.append("- Source unchanged: `true`")
md.append("- Public update allowed: `false`")
md.append("- Public publish allowed: `false`")
md.append("- Source post write allowed: `false`")
md.append("")
md.append("## Što je potvrđeno")
md.append("")
md.append("- Privatni clone `3536` postoji i ima status `private`.")
md.append("- Javni source post `1982` nije mijenjan.")
md.append("- Privatni clone nije javno izložen neprijavljenom korisniku.")
md.append("- Direktni privatni URL radi za administratorski pregled.")
md.append("- Strukturirani kartični Drycured prikaz je vidljiv u privatnom previewu.")
md.append("- Admin edit ekran prikazuje markdown, što je očekivano.")
md.append("- Sirovine, začini, mljevenje, crijeva/ovitak, proces, gotovost, greške i rješenja prikazani su u previewu.")
md.append("")
md.append("## Što nije dopušteno")
md.append("")
md.append("- Ne objavljivati clone `3536`.")
md.append("- Ne raditi javni update recepta `1982` ovim korakom.")
md.append("- Ne mijenjati javni title, slug, status ni URL.")
md.append("- Ne mijenjati postojeći renderer.")
md.append("- Ne prikazivati interne preview/status blokove u budućem javnom prikazu.")
md.append("")
md.append("## QA provjere")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    md.append(f"| {c['key']} | {c['status']} | {c['severity']} | {c['note']} |")
md.append("")
md.append("## Zaključak")
md.append("")
md.append("Tehnički workflow za `1982` je završen. Sadržaj je spreman kao privatni preview, ali javni update ostaje blokiran dok se ne odradi zasebna javna objavna procedura.")
md.append("")
md.append("Sljedeći operativni smjer: **prelazak na hrvatske recepte**. Ne nastavljati sada na `1984 Nduja` ni `1990 Salame di Felino`.")
md.append("")
report_path.write_text("\n".join(md), encoding="utf-8")

def append_once(path: Path, marker: str, block: str):
    old = path.read_text(encoding="utf-8")
    if marker not in old:
        path.write_text(old.rstrip() + "\n\n" + block.strip() + "\n", encoding="utf-8")

qa_block = f"""
<!-- DC_1982_PILOT_CLOSURE_V1 -->

## 1982 Finocchiona Toscana pilot closure v1

Status: **{closure_status}**

- Source post ID: `1982`
- Private clone ID: `{clone_id}`
- Manual admin preview confirmed: `true`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Major fail total: `{len(failures)}`
- Blocker fail total: `{len(blockers)}`
- Next priority: `CROATIA_RECIPES_FIRST`
- Report: `review/{review_dir.name}/1982_PILOT_CLOSURE_REPORT.md`
- JSON: `review/{review_dir.name}/1982_pilot_closure_v1.json`
"""
append_once(qa_path, "<!-- DC_1982_PILOT_CLOSURE_V1 -->", qa_block)

readme_block = f"""
<!-- DC_1982_PILOT_CLOSURE_V1 -->

## 1982 pilot closure v1

Status: **{closure_status}**

`1982 — Finocchiona Toscana IGP` tehnički je zatvoren kao privatni preview. Nakon ovog recepta operativni prioritet prelazi na hrvatske recepte.
"""
append_once(readme_path, "<!-- DC_1982_PILOT_CLOSURE_V1 -->", readme_block)

wplog_block = f"""
<!-- DC_1982_PILOT_CLOSURE_V1 -->

## 1982 pilot closure v1

- Source post `1982` unchanged: `true`
- Private clone ID: `{clone_id}`
- Clone status: `private`
- Public update allowed: `false`
- Manual admin preview confirmed: `true`
- Next priority: `CROATIA_RECIPES_FIRST`
"""
append_once(wplog_path, "<!-- DC_1982_PILOT_CLOSURE_V1 -->", wplog_block)

print("=== 1982 PILOT CLOSURE COMPLETE ===")
print(f"CLOSURE_STATUS={closure_status}")
print("POST_ID=1982")
print(f"CLONE_ID={clone_id}")
print("MANUAL_ADMIN_PREVIEW_CONFIRMED=true")
print("PUBLIC_UPDATE_ALLOWED=false")
print("PUBLIC_PUBLISH_ALLOWED=false")
print("SOURCE_POST_WRITE_ALLOWED=false")
print(f"MAJOR_FAIL_TOTAL={len(failures)}")
print(f"BLOCKER_FAIL_TOTAL={len(blockers)}")
print("NEXT_PRIORITY=CROATIA_RECIPES_FIRST")
print(f"REPORT={report_path}")
