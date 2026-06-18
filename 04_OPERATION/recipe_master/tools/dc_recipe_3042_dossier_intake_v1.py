#!/usr/bin/env python3
import csv
import json
import re
import sys
from pathlib import Path
from html import unescape
from urllib.request import Request, urlopen
from urllib.error import URLError, HTTPError

if len(sys.argv) != 4:
    print("Usage: dc_recipe_3042_dossier_intake_v1.py RAW_JSON PUBLIC_TXT REVIEW_DIR", file=sys.stderr)
    sys.exit(1)

raw_json = Path(sys.argv[1])
public_txt = Path(sys.argv[2])
review_dir = Path(sys.argv[3])
review_dir.mkdir(parents=True, exist_ok=True)

data = json.loads(raw_json.read_text(encoding="utf-8"))
public_text = public_txt.read_text(encoding="utf-8", errors="replace")

post_id = data.get("post_id")
title = data.get("title", "")
url = data.get("url", "")
status = data.get("status", "")
post_content = data.get("post_content", "") or ""
post_excerpt = data.get("post_excerpt", "") or ""
meta = data.get("meta_redacted", {}) or {}

def norm(s):
    s = unescape(str(s or ""))
    s = s.replace("\r\n", "\n").replace("\r", "\n")
    s = re.sub(r"[ \t]+", " ", s)
    return s.strip()

def plain(s):
    s = norm(s)
    s = re.sub(r"<[^>]+>", " ", s)
    s = unescape(s)
    s = re.sub(r"\s+", " ", s).strip()
    return s

def lower_ascii(s):
    s = (s or "").lower()
    repl = {
        "š": "s", "č": "c", "ć": "c", "ž": "z", "đ": "d",
        "é": "e", "è": "e", "ê": "e", "ë": "e",
        "à": "a", "á": "a", "â": "a", "ä": "a",
        "ô": "o", "ö": "o", "ó": "o", "ò": "o",
        "û": "u", "ü": "u", "ù": "u", "ú": "u",
        "î": "i", "ï": "i", "í": "i", "ì": "i",
        "ç": "c", "œ": "oe", "æ": "ae",
    }
    for a, b in repl.items():
        s = s.replace(a, b)
    return s

def has_any(text, patterns):
    t = lower_ascii(text)
    return [p for p in patterns if p in t]

def fetch_html(url):
    if not url:
        return "", "", "empty_url"
    try:
        req = Request(url, headers={"User-Agent": "DrycuredDossierIntake/1.0 read-only"})
        with urlopen(req, timeout=8) as resp:
            status_code = str(resp.status)
            body = resp.read().decode("utf-8", errors="replace")
            return status_code, body, ""
    except HTTPError as e:
        return str(e.code), "", str(e)
    except URLError as e:
        return "", "", str(e)
    except Exception as e:
        return "", "", str(e)

status_code, html, fetch_error = fetch_html(url)
(review_dir / "3042_public_html_snapshot.html").write_text(html, encoding="utf-8", errors="replace")

all_text = "\n".join([
    title,
    post_excerpt,
    plain(post_content),
    public_text,
    plain(html),
])

html_l = lower_ascii(html)
text_l = lower_ascii(all_text)

markers = {
    "has_dcv5_recipe_marker": "dcv5" in html_l or "dcv5-recipe" in html_l,
    "has_drycured_recipe_marker": "drycured-recipe" in html_l or "dc-recipe" in html_l,
    "has_wprm_marker": "wprm" in html_l or "wp-recipe-maker" in html_l,
    "has_json_ld_recipe": '"@type":"recipe"' in html_l or '"@type": "recipe"' in html_l,
    "has_internal_preview_text": any(x in text_l for x in ["preview", "fallback", "source-lock", "audit", "adapter", "debug", "radni recept"]),
}

keyword_groups = {
    "ingredients": ["sastoj", "meso", "svinjetina", "govedina", "slanina", "mast", "sol", "papar", "češnjak", "cesnjak", "vino", "nitrit"],
    "grinding": ["mljeven", "mleven", "rešet", "reset", "šajb", "sajb", "mm", "kock", "rezan"],
    "casing": ["crijev", "crev", "omotač", "omotac", "punjenje", "puniti"],
    "process": ["miješ", "mijes", "ferment", "dim", "suš", "sus", "zren", "sazrij", "vlaga", "temperatura", "ciklus"],
    "safety": ["nitrit", "sigurn", "oprez", "odbac", "plijesan", "sluz", "kvar", "temperatura"],
    "problems": ["grešk", "gresk", "problem", "rješen", "rjesen", "uzrok"],
}

def extract_context(text, terms, max_items=30):
    text = plain(text)
    # dijeli na rečenice i kratke blokove
    parts = re.split(r"(?<=[.!?])\s+|\n+", text)
    out = []
    seen = set()
    for part in parts:
        p = part.strip()
        if len(p) < 20:
            continue
        pl = lower_ascii(p)
        if any(lower_ascii(term) in pl for term in terms):
            short = p[:500]
            if short not in seen:
                out.append(short)
                seen.add(short)
        if len(out) >= max_items:
            break
    return out

contexts = {}
for group, terms in keyword_groups.items():
    contexts[group] = extract_context(all_text, terms)

# Obvezna polja za GROUND_MEAT_OR_CASING prema našem LAW-u.
checks = [
    ("source_validation", "Izvor recepta potvrđen", False, "Dosje je scaffold-only; izvor još nije potvrđen."),
    ("batch_10kg", "Standardizacija na 10 kg mesa", bool(re.search(r"\b10\s*kg\b", text_l)), "Tražiti ili normalizirati recept na 10 kg."),
    ("raw_materials_kg", "Sirovine u kg", bool(re.search(r"\bkg\b", text_l) and has_any(all_text, ["meso", "svinjetina", "govedina", "slanina"])), "Meso i masnoća moraju biti u kg."),
    ("spices_g", "Začini u g", bool(re.search(r"\b(g|gram|grama)\b", text_l) and has_any(all_text, ["sol", "papar", "začin", "zacin"])), "Začine treba prikazati u gramima."),
    ("grinding_mm", "Granulacija/rešetka u mm", bool(re.search(r"(rešet|reset|šajb|sajb|mljeven|mleven).{0,80}\b[0-9]{1,2}\s*mm|\b[0-9]{1,2}\s*mm.{0,80}(rešet|reset|šajb|sajb|mljeven|mleven)", text_l)), "Za mljeveni proizvod mora postojati rešetka u mm."),
    ("fat_handling", "Obrada slanine/masnoće", bool(has_any(all_text, ["slanina", "masno", "mast"]) and has_any(all_text, ["kock", "rezan", "nož", "noz", "smrz", "hlad"])), "Treba opisati rezanje/hladnu obradu masnoće."),
    ("casing_details", "Crijeva/omotač i namakanje", bool(has_any(all_text, ["crijev", "crev", "omotač", "omotac"]) and has_any(all_text, ["namak", "voda", "vino", "punjen"])), "Treba navesti tip, promjer i namakanje crijeva."),
    ("garlic_mode", "Češnjak ili procijeđena tekućina od češnjaka", bool(has_any(all_text, ["češnjak", "cesnjak"])), "Ako se koristi češnjak, navesti direktno ili procijeđena tekućina s količinama."),
    ("smoking_params", "Dimljenje s parametrima", bool(has_any(all_text, ["dim"]) and re.search(r"\b[0-9]{1,2}\s*(dan|dana|sat|sata|h|min|°|c)\b", text_l)), "Ako se dimi, trebaju trajanje, temperatura dima, ciklusi/pauze."),
    ("drying_aging_params", "Sušenje/zrenje s parametrima", bool(has_any(all_text, ["suš", "sus", "zren", "sazrij"]) and re.search(r"\b[0-9]{1,3}\s*(dan|dana|tjed|mjesec|°|c|%)\b", text_l)), "Treba trajanje, temperatura i RH gdje je dostupno."),
    ("nitrite_note", "Nitritna napomena ako se koristi nitritna sol", (not has_any(all_text, ["nitrit"])) or has_any(all_text, ["vagati precizno", "ne prekoračiti", "ne prekoraciti", "ne dodavati je od oka"]), "Ako se koristi nitritna sol, treba sigurnosna napomena."),
    ("errors_solutions", "Problemi imaju konkretna rješenja", bool(has_any(all_text, ["grešk", "gresk", "problem"]) and has_any(all_text, ["rješen", "rjesen", "spriječ", "sprijec", "odbac", "poprav"])), "Svaki problem mora imati konkretno rješenje."),
    ("no_internal_public_terms", "Nema javnih internih oznaka", not markers["has_internal_preview_text"], "U javnom tekstu ne smije biti preview/fallback/source-lock/audit/adapter/debug."),
]

gap_rows = []
for key, label, ok, note in checks:
    gap_rows.append({
        "key": key,
        "label": label,
        "status": "PASS" if ok else "GAP",
        "note": note,
    })

with (review_dir / "3042_gap_matrix.csv").open("w", encoding="utf-8", newline="") as f:
    w = csv.DictWriter(f, fieldnames=["key", "label", "status", "note"])
    w.writeheader()
    w.writerows(gap_rows)

candidate = {
    "dossier_status": "INTAKE_ONLY",
    "public_update_allowed": False,
    "post_id": post_id,
    "title": title,
    "url": url,
    "recipe_type": "GROUND_MEAT_OR_CASING",
    "batch_size_kg": 10,
    "current_wp_status": status,
    "http_status": status_code,
    "http_error": fetch_error,
    "detected_markers": markers,
    "intake_contexts": contexts,
    "gap_matrix": gap_rows,
}
(review_dir / "3042_intake_candidate_data.json").write_text(json.dumps(candidate, ensure_ascii=False, indent=2), encoding="utf-8")

md = []
md.append("# 3042 Jésus de Lyon — dossier intake v1")
md.append("")
md.append("Status: **INTAKE_ONLY — NO_PUBLIC_UPDATE**")
md.append("")
md.append("Ovaj izvještaj samo čita postojeći dosje, WordPress snapshot i javni prikaz. Ne mijenja WordPress.")
md.append("")
md.append("## Osnovni podaci")
md.append("")
md.append(f"- Post ID: `{post_id}`")
md.append(f"- Naslov: **{title}**")
md.append(f"- URL: `{url}`")
md.append(f"- WordPress status: `{status}`")
md.append(f"- HTTP status javnog URL-a: `{status_code}`")
if fetch_error:
    md.append(f"- HTTP error: `{fetch_error}`")
md.append("")
md.append("## Markeri javnog prikaza")
md.append("")
for k, v in markers.items():
    md.append(f"- {k}: `{str(v).lower()}`")
md.append("")
md.append("## Gap matrica")
md.append("")
md.append("| Polje | Status | Napomena |")
md.append("|---|---|---|")
for row in gap_rows:
    md.append(f"| {row['label']} | {row['status']} | {row['note']} |")
md.append("")
for group, items in contexts.items():
    md.append(f"## Izvučeni kontekst — {group}")
    md.append("")
    if not items:
        md.append("_Nije pronađen jasan tekstualni kontekst._")
    else:
        for i, item in enumerate(items[:20], 1):
            md.append(f"{i}. {item}")
    md.append("")
md.append("## Zaključak")
md.append("")
md.append("Javni update nije dopušten. Sljedeći korak je validacija izvora i ručno/kanonsko popunjavanje `recipe.yml` na temelju potvrđenih podataka.")
md.append("")
md.append("## Izlazne datoteke")
md.append("")
md.append("- `3042_gap_matrix.csv`")
md.append("- `3042_intake_candidate_data.json`")
md.append("- `3042_public_html_snapshot.html`")
md.append("- `3042_INTAKE_REPORT.md`")
md.append("")

(review_dir / "3042_INTAKE_REPORT.md").write_text("\n".join(md), encoding="utf-8")

print("=== 3042 DOSSIER INTAKE COMPLETE ===")
print(f"POST_ID={post_id}")
print(f"TITLE={title}")
print(f"URL={url}")
print(f"HTTP_STATUS={status_code}")
print(f"DCV5_MARKER={str(markers['has_dcv5_recipe_marker']).lower()}")
print(f"WPRM_MARKER={str(markers['has_wprm_marker']).lower()}")
print(f"INTERNAL_TERMS={str(markers['has_internal_preview_text']).lower()}")
print("GAP_COUNTS:")
print("PASS=" + str(sum(1 for r in gap_rows if r["status"] == "PASS")))
print("GAP=" + str(sum(1 for r in gap_rows if r["status"] == "GAP")))
print(f"REPORT={review_dir / '3042_INTAKE_REPORT.md'}")
