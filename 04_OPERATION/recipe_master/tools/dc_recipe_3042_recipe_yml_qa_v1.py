#!/usr/bin/env python3
from pathlib import Path
import json
import re
import sys
from datetime import datetime, timezone

if len(sys.argv) != 5:
    print("Usage: dc_recipe_3042_recipe_yml_qa_v1.py DOSSIER_DIR RECIPE_YML SOURCES_YML QA_REPORT", file=sys.stderr)
    sys.exit(1)

dossier_dir = Path(sys.argv[1])
recipe_path = Path(sys.argv[2])
sources_path = Path(sys.argv[3])
qa_path = Path(sys.argv[4])

review_dirs = sorted((dossier_dir / "review").glob("recipe_yml_qa_v1_*"))
review_dir = review_dirs[-1] if review_dirs else dossier_dir / "review" / "recipe_yml_qa_v1_manual"
review_dir.mkdir(parents=True, exist_ok=True)

recipe = recipe_path.read_text(encoding="utf-8")
sources = sources_path.read_text(encoding="utf-8")
qa_old = qa_path.read_text(encoding="utf-8")

def contains(marker):
    return marker in recipe

def contains_any(markers):
    return any(m in recipe for m in markers)

def find_amounts_kg():
    vals = []
    for m in re.finditer(r"amount_kg:\s*([0-9]+(?:\.[0-9]+)?)", recipe):
        vals.append(float(m.group(1)))
    return vals

def find_amounts_g():
    vals = []
    for m in re.finditer(r"amount_g:\s*([0-9]+(?:\.[0-9]+)?)", recipe):
        vals.append(float(m.group(1)))
    return vals

raw_kg = find_amounts_kg()
spice_g = find_amounts_g()
raw_sum = round(sum(raw_kg), 3)

checks = []

def add(key, label, ok, severity, note):
    checks.append({
        "key": key,
        "label": label,
        "status": "PASS" if ok else "FAIL",
        "severity": severity,
        "note": note
    })

add("public_update_false", "Javni update zabranjen", contains("public_update_allowed: false"), "BLOCKER", "Mora ostati false do završnog QA-a.")
add("draft_status", "Status radnog nacrta", contains('dossier_status: "CANON_DRAFT_V1_NOT_PUBLIC"'), "BLOCKER", "Recept mora ostati radni nacrt.")
add("not_public_verified", "Nije označen kao public verified", contains("public_verified: false"), "BLOCKER", "Ne smije biti public_verified dok izvor i QA nisu završeni.")
add("recipe_type", "Tip recepta GROUND_MEAT_OR_CASING", contains('recipe_type: "GROUND_MEAT_OR_CASING"'), "BLOCKER", "Za ovaj pilot koristi se samo model mljevenog/usitnjenog mesa u omotaču.")
add("source_status", "Status izvora upisan", contains('source_validation_status: "PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED"'), "BLOCKER", "Proizvod je potvrđen, recept još nije kanonski potvrđen.")
add("protected_status_false", "Ne tvrdi zaštićeni status", contains("protected_status_claim_allowed: false"), "BLOCKER", "Ne smije se tvrditi aktualni IGP/ZOI status.")
add("batch_10kg", "Šarža 10 kg", contains("batch_size_kg: 10"), "MAJOR", "Svi recepti u sustavu moraju biti standardizirani na 10 kg.")
add("raw_materials_present", "Sirovine u kg postoje", contains("raw_materials_kg:") and len(raw_kg) >= 3, "MAJOR", "Meso i masnoća moraju biti u kg.")
add("raw_materials_sum", "Zbroj sirovina približno 10 kg", abs(raw_sum - 10.0) <= 0.01, "MAJOR", f"Zbroj pronađenih amount_kg vrijednosti je {raw_sum} kg.")
add("spices_present", "Začini i dodaci u g postoje", contains("spices_and_additives_g:") and len(spice_g) >= 5, "MAJOR", "Začini moraju biti u g.")
add("salt_200g", "Sol 200 g / 20 g/kg", contains("amount_g: 200") and contains("g_per_kg: 20.0"), "MAJOR", "Sol je u razumnom osnovnom rasponu za suhi proizvod.")
add("starter_blocker", "Starter kultura označena za provjeru", contains("starter_culture_review_required: true") and contains("REQUIRES_TECHNICAL_REVIEW"), "BLOCKER", "Starter ne smije u javni recept bez provjere deklaracije proizvođača.")
add("liquids_present", "Tekućine upisane", contains("liquids:") and contains("konjak") and contains("destilirana voda"), "MINOR", "Tekućine su vidljive, ali voda za starter ostaje vezana uz provjeru startera.")
add("garlic_mode", "Češnjak jasno definiran", contains("garlic_liquid_details:") and contains("mode:") and contains("sušeni češnjak u prahu"), "MAJOR", "U ovom nacrtu nema procijeđene tekućine od češnjaka.")
add("grinding", "Granulacija i hladna obrada", contains("meat_plate_mm:") and contains("6–8") and contains("temperatura smjese ne smije prijeći 8 °C"), "MAJOR", "Za mljeveni proizvod mora postojati rešetka u mm i kontrola temperature.")
add("fat_handling", "Obrada masnoće opisana", contains("fat_handling:") and contains("Masnoća mora ostati hladna"), "MAJOR", "Masnoća mora ostati hladna i čvrsta.")
add("casing", "Crijeva i namakanje", contains("casing:") and contains("svinjska crijeva") and contains("30–45 minuta") and contains("25–30"), "MAJOR", "Crijeva imaju tip, promjer, tekućinu, vrijeme i temperaturu namakanja.")
add("process", "Procesni blok postoji", contains("process:") and contains("stuffing:") and contains("drying_and_aging:"), "MAJOR", "Proces mora biti strukturiran po fazama.")
add("smoking_blocker", "Dimljenje označeno kao needs_confirmation", contains("smoking_confirmation_required: true") and contains("needs_confirmation"), "BLOCKER", "Dimljenje ne smije biti javno prikazano kao obvezno bez dodatnog izvora.")
add("drying_params", "Sušenje/zrenje ima parametre", contains("temperature_c: \"10–15\"") and contains("relative_humidity_percent: \"70–80\"") and contains("target_weight_loss_percent: \"35–40\""), "MAJOR", "Sušenje/zrenje ima radne parametre.")
add("nitrite", "Nitritna sol nije navedena", contains("used: false") and contains("WP_DRAFT_NO_NITRITE_LISTED"), "MAJOR", "Ako se kasnije doda nitrit, obvezna je sigurnosna napomena.")
add("done_when", "Gotovo je kad blok postoji", contains("done_when:"), "MAJOR", "Recept mora imati kriterije gotovosti.")
add("errors_solutions", "Problemi imaju rješenja", contains("common_errors_and_solutions:") and recipe.count("problem:") >= 4 and recipe.count("solution:") >= 4, "MAJOR", "Svaki problem mora imati konkretno rješenje.")
add("serving_storage", "Posluživanje i čuvanje postoje", contains("serving_and_storage:"), "MINOR", "Treba biti vidljivo za javni prikaz.")
add("qa_blockers", "Blokade prije javnog updatea postoje", contains("qa_blockers_before_public_update:") and contains("kanonski izvor za točne količine nije potvrđen"), "BLOCKER", "Javni update mora ostati blokiran.")

failures = [c for c in checks if c["status"] == "FAIL"]
blocker_fails = [c for c in failures if c["severity"] == "BLOCKER"]

# Čak i ako su svi tehnički checkovi PASS, recept je i dalje blokiran zbog namjerno upisanih blocker polja.
known_blockers = [
    "kanonski izvor za točne količine nije potvrđen",
    "količina starter kulture zahtijeva tehničku provjeru",
    "dimljenje je označeno kao needs_confirmation",
    "javni tekst još sadrži interne tragove prema intake izvještaju",
    "potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea"
]
active_known_blockers = [b for b in known_blockers if b in recipe]

qa_status = "BLOCKED_FOR_PUBLIC_UPDATE"
private_preview_allowed = (
    len(failures) == 0
    and "starter_culture_review_required: true" in recipe
    and "smoking_confirmation_required: true" in recipe
    and "public_update_allowed: false" in recipe
)

payload = {
    "generated_at": datetime.now(timezone.utc).isoformat(),
    "post_id": 3042,
    "title": "Jésus de Lyon – debela suha kobasica",
    "qa_status": qa_status,
    "public_update_allowed": False,
    "private_preview_allowed": private_preview_allowed,
    "raw_materials_sum_kg": raw_sum,
    "check_total": len(checks),
    "pass_total": sum(1 for c in checks if c["status"] == "PASS"),
    "fail_total": len(failures),
    "blocker_fail_total": len(blocker_fails),
    "active_known_blockers": active_known_blockers,
    "checks": checks
}
(review_dir / "3042_recipe_yml_qa_v1.json").write_text(json.dumps(payload, ensure_ascii=False, indent=2), encoding="utf-8")

csv_lines = ["key,label,status,severity,note"]
for c in checks:
    def esc(x):
        return '"' + str(x).replace('"', '""') + '"'
    csv_lines.append(",".join([esc(c["key"]), esc(c["label"]), esc(c["status"]), esc(c["severity"]), esc(c["note"])]))
(review_dir / "3042_recipe_yml_qa_v1.csv").write_text("\n".join(csv_lines) + "\n", encoding="utf-8")

md = []
md.append("# 3042 Jésus de Lyon — recipe.yml internal QA v1")
md.append("")
md.append("Status: **BLOCKED_FOR_PUBLIC_UPDATE**")
md.append("")
md.append("Ovaj QA ne mijenja WordPress. Provjerava samo radni `recipe.yml` u dosjeu.")
md.append("")
md.append("## Sažetak")
md.append("")
md.append(f"- Ukupno provjera: {len(checks)}")
md.append(f"- PASS: {payload['pass_total']}")
md.append(f"- FAIL: {payload['fail_total']}")
md.append(f"- Zbroj sirovina: {raw_sum} kg")
md.append(f"- Privatni preview/adapter tehnički dopušten: `{str(private_preview_allowed).lower()}`")
md.append(f"- Javni update dopušten: `false`")
md.append("")
md.append("## Aktivne poznate blokade")
md.append("")
for b in active_known_blockers:
    md.append(f"- {b}")
md.append("")
md.append("## QA tablica")
md.append("")
md.append("| Provjera | Status | Težina | Napomena |")
md.append("|---|---|---|---|")
for c in checks:
    md.append(f"| {c['label']} | {c['status']} | {c['severity']} | {c['note']} |")
md.append("")
md.append("## Zaključak")
md.append("")
if len(failures) == 0:
    md.append("`recipe.yml` je tehnički uredan kao radni nacrt i smije ići u privatni preview/adapter, ali samo s vidljivim internim statusom `NOT_PUBLIC` u dosjeu, ne u javnom prikazu.")
else:
    md.append("`recipe.yml` ima QA padove i ne smije ići ni u privatni preview dok se ne isprave.")
md.append("")
md.append("Javni WordPress update nije dopušten jer recept još ima aktivne blokade: izvor količina, starter kultura, dimljenje i javni interni tragovi.")
md.append("")
(review_dir / "3042_RECIPE_YML_QA_V1_REPORT.md").write_text("\n".join(md), encoding="utf-8")

marker = "<!-- DC_3042_RECIPE_YML_QA_V1 -->"
append = f"""
{marker}

## Recipe.yml internal QA v1

Status: **BLOCKED_FOR_PUBLIC_UPDATE**

- Ukupno provjera: {len(checks)}
- PASS: {payload['pass_total']}
- FAIL: {payload['fail_total']}
- Zbroj sirovina: {raw_sum} kg
- Privatni preview/adapter tehnički dopušten: `{str(private_preview_allowed).lower()}`
- Javni update dopušten: `false`

### Aktivne blokade

""" + "\n".join([f"- {b}" for b in active_known_blockers]) + f"""

Report: `review/{review_dir.name}/3042_RECIPE_YML_QA_V1_REPORT.md`
"""

if marker not in qa_old:
    qa_path.write_text(qa_old.rstrip() + "\n\n" + append.strip() + "\n", encoding="utf-8")

print("=== 3042 RECIPE.YML QA COMPLETE ===")
print(f"QA_STATUS={qa_status}")
print(f"CHECK_TOTAL={len(checks)}")
print(f"PASS_TOTAL={payload['pass_total']}")
print(f"FAIL_TOTAL={payload['fail_total']}")
print(f"RAW_MATERIALS_SUM_KG={raw_sum}")
print(f"PRIVATE_PREVIEW_ALLOWED={str(private_preview_allowed).lower()}")
print("PUBLIC_UPDATE_ALLOWED=false")
print(f"REPORT={review_dir / '3042_RECIPE_YML_QA_V1_REPORT.md'}")
