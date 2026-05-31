#!/usr/bin/env python3
"""Drycured source-lock compiler v1.9E.

Pilot scope: 10 HR-SL recipes from TOM2_HR_SOURCE_LOCK_MASTER.md.

The compiler uses only the strict primary source declared in
source_recipes/hr/source_priority_manifest.yml. It never reads or writes
WordPress, changes post_status, publishes recipes, or treats legacy quantity
profiles as authority.
"""

from __future__ import annotations

import argparse
import copy
import csv
import hashlib
import json
import re
import sys
import unicodedata
from dataclasses import dataclass
from datetime import datetime, timezone
from pathlib import Path
from difflib import SequenceMatcher
from typing import Any


SCHEMA_VERSION = "drycured.source_locked_recipe.v1"
DEFAULT_PRIMARY_SOURCE = "TOM2_HR_SOURCE_LOCK_MASTER.md"

PILOT10_IDS = [
    "HR-SL-001",
    "HR-SL-002",
    "HR-SL-005",
    "HR-SL-009",
    "HR-SL-014",
    "HR-SL-012",
    "HR-SL-024",
    "HR-SL-017",
    "HR-SL-018",
    "HR-SL-019",
]

LEGACY_CONFLICT_VALUES = [
    "8,00 kg",
    "2,00 kg",
    "Crni papar mljeveni",
    "goveđa slijepa / kate",
    "55–80 mm",
]

HR_SL_001_FORBIDDEN_OUTPUT_VALUES = [
    "Svinjski but i/ili plećka",
    "70 % meso",
    "30 % slanina",
    "8,000 kg",
    "2,000 kg",
    "8,00 kg",
    "2,00 kg",
    "55–80 mm",
    "55–60 mm",
    "širi prirodni omotač",
    "siri prirodni omotac",
    "crni papar",
    "Crni papar",
    "Crni papar mljeveni",
    "goveđa slijepa / kate",
]

UNIVERSAL_FORBIDDEN_OUTPUT_VALUES = [
    "PROVJERITI",
    "prema receptu",
    "šajba za punjenje",
    "crijeva / omotači",
]

TABLE_HEADER_FIRST_CELLS = {
    "komponenta",
    "sastojak",
    "sirovina",
    "naziv",
    "materijal",
    "dio",
    "kolicina",
    "napomena",
}

EQUIPMENT_TERMS = [
    "kanab",
    "vezivo",
    "vezivanje",
    "igla",
    "spaga",
    "konop",
    "oprema",
    "sajba",
    "resetka",
    "punilica",
    "kuka",
    "noz",
    "kalup",
    "presa",
    "mlin",
    "mreza",
]

CASING_TERMS = [
    "crijevo",
    "crijeva",
    "omotac",
    "omotaci",
    "kate",
    "slijepo crijevo",
    "debela crijeva",
    "svinjska debela crijeva",
    "caecum",
    "mjehur",
    "zeludac",
]

SPICE_TERMS = [
    "sol",
    "paprika",
    "papar",
    "crni papar",
    "majoram",
    "mazuran",
    "majoran",
    "secer",
    "askorbinska kiselina",
    "piment",
    "kumin",
    "korijander",
    "lovor",
    "zacin",
]

LIQUID_TERMS = [
    "juha od kuhanja",
    "mesna juha",
    "temeljac",
    "voda od kuhanja",
    "voda",
    "vino",
    "rum",
    "rakija",
    "salamura",
    "tekucina",
    "tekuci",
    "cijeđeni sok",
    "cijedeni sok",
    "procijedena tekucina",
    "prokuhana i ohladena voda",
]

GARLIC_TERMS = ["bijeli luk", "cesnjak"]
GARLIC_LIQUID_HINTS = ["tekuci", "tekucina", "cijedeni sok", "cijeđeni sok", "procijedena tekucina", "ekstrakcija"]
MATERIAL_TERMS = [
    "svinjska plecka",
    "plecka",
    "vrat",
    "but",
    "govedina",
    "krv",
    "kuhana glava",
    "glava",
    "koza",
    "kozica",
    "jetra",
    "plucka",
    "pluca",
    "slanina",
    "mast",
    "meso",
    "proso",
    "riza",
]

HR_SL_001_REQUIRED_INGREDIENT_TOKENS = [
    "6,00 kg",
    "2,50 kg",
    "1,00 kg",
    "0,50 kg",
    "260–280 g",
    "80–120 g",
    "120–160 g",
    "50–70 g",
    "svinjsko slijepo crijevo",
    "80–120 mm",
    "konopljeni kanab",
]

HR_SL_001_ITEM_SPECS = [
    ("materials", "Svinjski but I. kategorije (bez kosti, bez kože)", r"6,00\s*kg", None),
    ("materials", "Svinjska plećka II. kategorije (bez podlaktice)", r"2,50\s*kg", None),
    ("materials", "Svinjski vrat III. kategorije", r"1,00\s*kg", None),
    ("materials", "Tvrda leđna slanina", r"0,50\s*kg", None),
    ("spices", "Sol (kuhinjska)", r"260\s*[-–]\s*280\s*g", None),
    ("spices", "Ljuta mljevena paprika", r"80\s*[-–]\s*120\s*g", None),
    ("spices", "Slatka mljevena paprika", r"120\s*[-–]\s*160\s*g", None),
    ("liquids", "Bijeli luk (svježi, cijeđeni sok)", r"50\s*[-–]\s*70\s*g", None),
    (
        "casings",
        "Svinjsko slijepo crijevo (caecum / kate)",
        r"1\s*[-–]\s*2\s*kom",
        "Min. promjer 80–120 mm; isključivo prirodni omotač",
    ),
    ("equipment", "Konopljeni kanab", r"vezivanje|konopljeni\s+kanab", None),
]

PROCESS_PHASES = [
    ("odabir sirovine", ["odabir sirovine", "sirovina", "pasmina", "svinja"]),
    ("rezanje / mljevenje", ["rezanje", "mljevenje", "mljeti", "rezati"]),
    ("miješanje", ["miješanje", "mijesanje", "izmiješati", "izmijesati"]),
    ("odmor nadjeva", ["odmor nadjeva", "odmor", "nadjev"]),
    ("priprema omotača", ["priprema omotača", "priprema omotaca", "priprema kate", "omotač", "omotac", "crijevo"]),
    ("punjenje", ["punjenje", "puniti"]),
    ("dimljenje", ["dimljenje", "dimiti", "dim"]),
    ("sušenje / zrenje", ["sušenje", "susenje", "zrenje", "dozrijevanje"]),
    ("čuvanje / posluživanje", ["čuvanje", "cuvanje", "posluživanje", "posluzivanje", "skladištenje"]),
]

WHOLE_CUT_HINTS = ["šunka", "slanina", "svinjski vrat"]
COOKED_HINTS = ["jetrena", "krvavica", "tlačenica", "švargl", "baren", "kuhan"]


@dataclass(frozen=True)
class SourceDocument:
    path: Path
    text: str
    sha256: str


def normalize_for_search(value: str) -> str:
    table = str.maketrans(
        {
            "—": "-",
            "–": "-",
            "‑": "-",
            "≤": "<=",
            "š": "s",
            "Š": "s",
            "đ": "d",
            "Đ": "d",
            "č": "c",
            "Č": "c",
            "ć": "c",
            "Ć": "c",
            "ž": "z",
            "Ž": "z",
        }
    )
    translated = value.translate(table)
    decomposed = unicodedata.normalize("NFKD", translated)
    ascii_folded = "".join(ch for ch in decomposed if not unicodedata.combining(ch))
    return re.sub(r"\s+", " ", ascii_folded).strip()


def contains_token(haystack: str | None, needle: str) -> bool:
    return bool(haystack) and normalize_for_search(needle).lower() in normalize_for_search(haystack).lower()


def read_text_file(path: Path) -> str:
    data = path.read_bytes()
    for encoding in ("utf-8-sig", "utf-8", "cp1250", "latin-1"):
        try:
            return data.decode(encoding)
        except UnicodeDecodeError:
            continue
    return data.decode("utf-8", errors="replace")


def sha256_file(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1024 * 1024), b""):
            digest.update(chunk)
    return digest.hexdigest()


def project_root_from(start: Path) -> Path:
    linux_root = Path("/root/DRYCURED_GITHUB")
    if linux_root.exists():
        return linux_root
    if start.name == "DRYCURED_GITHUB":
        return start
    return start / "DRYCURED_GITHUB"


def ensure_dirs(root: Path) -> None:
    for path in [
        root / "tools" / "source_lock_compiler",
        root / "source_recipes" / "hr",
        root / "build" / "source_locked_json",
        root / "build" / "source_lock_audit",
    ]:
        path.mkdir(parents=True, exist_ok=True)


def parse_simple_yml(path: Path) -> dict[str, Any]:
    if not path.exists():
        return {}
    result: dict[str, Any] = {}
    current_key: str | None = None
    for raw_line in read_text_file(path).splitlines():
        line = raw_line.rstrip()
        if not line.strip() or line.lstrip().startswith("#"):
            continue
        if line.startswith("  - ") and current_key:
            result.setdefault(current_key, []).append(line[4:].strip().strip('"'))
            continue
        if ":" in line and not line.startswith(" "):
            key, value = line.split(":", 1)
            key = key.strip()
            value = value.strip()
            if value:
                result[key] = value.strip('"')
                current_key = None
            else:
                result[key] = []
                current_key = key
    return result


def parse_recipe_id_map(path: Path) -> dict[str, dict[str, Any]]:
    if not path.exists():
        return {}
    result: dict[str, dict[str, Any]] = {}
    current_recipe: str | None = None
    current_list: str | None = None
    for raw_line in read_text_file(path).splitlines():
        line = raw_line.rstrip()
        if not line.strip():
            continue
        if not line.startswith(" ") and line.endswith(":"):
            current_recipe = line[:-1].strip()
            result[current_recipe] = {}
            current_list = None
            continue
        if not current_recipe:
            continue
        stripped = line.strip()
        if stripped.startswith("- ") and current_list:
            result[current_recipe].setdefault(current_list, []).append(stripped[2:].strip().strip('"'))
            continue
        if ":" in stripped:
            key, value = stripped.split(":", 1)
            key = key.strip()
            value = value.strip()
            if value:
                parsed: Any = value.strip('"')
                if parsed.isdigit():
                    parsed = int(parsed)
                elif parsed.lower() == "true":
                    parsed = True
                elif parsed.lower() == "false":
                    parsed = False
                result[current_recipe][key] = parsed
                current_list = None
            else:
                result[current_recipe][key] = []
                current_list = key
    return result


def yaml_scalar(value: Any) -> str:
    if isinstance(value, bool):
        return "true" if value else "false"
    if isinstance(value, int):
        return str(value)
    text = str(value).replace('"', '\\"')
    return f'"{text}"'


def write_recipe_id_map(path: Path, recipe_maps: dict[str, dict[str, Any]]) -> None:
    lines: list[str] = []
    for recipe_id, recipe_map in recipe_maps.items():
        lines.append(f"{recipe_id}:")
        ordered_keys = ["title", "source_heading", "heading_status", "authority_locked", "title_aliases", "recipe_number", "zone"]
        for key in ordered_keys:
            if key not in recipe_map:
                continue
            value = recipe_map[key]
            if isinstance(value, list):
                lines.append(f"  {key}:")
                for item in value:
                    lines.append(f"    - {yaml_scalar(item)}")
            else:
                lines.append(f"  {key}: {yaml_scalar(value)}")
        for key, value in recipe_map.items():
            if key in ordered_keys:
                continue
            if isinstance(value, list):
                lines.append(f"  {key}:")
                for item in value:
                    lines.append(f"    - {yaml_scalar(item)}")
            else:
                lines.append(f"  {key}: {yaml_scalar(value)}")
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def load_primary_source(root: Path, manifest: dict[str, Any], warnings: list[str]) -> SourceDocument | None:
    primary_name = str(manifest.get("primary_source") or DEFAULT_PRIMARY_SOURCE)
    primary_path = root / "source_recipes" / "hr" / primary_name
    if not primary_path.exists():
        warnings.append("Authoritative source file TOM2_HR_SOURCE_LOCK_MASTER.md is missing.")
        return None
    if primary_path.suffix.lower() not in {".md", ".txt"}:
        warnings.append(f"Unsupported strict primary source type: {primary_path.suffix}")
        return None
    return SourceDocument(primary_path, read_text_file(primary_path), sha256_file(primary_path))


def recipe_title(recipe_id: str, recipe_map: dict[str, Any]) -> str:
    if recipe_map.get("title"):
        return str(recipe_map["title"])
    aliases = recipe_map.get("title_aliases") or []
    if aliases:
        return str(aliases[0])
    heading = str(recipe_map.get("source_heading") or "")
    return re.sub(r"^#{2,3}\s+\d+\.\s*", "", heading).strip() or recipe_id


def base_recipe(recipe_id: str, recipe_map: dict[str, Any], source_heading: str | None = None) -> dict[str, Any]:
    return {
        "schema_version": SCHEMA_VERSION,
        "recipe_id": recipe_id,
        "title": recipe_title(recipe_id, recipe_map),
        "source_status": "review",
        "batch": {"amount": 10, "unit": "kg", "basis": "source"},
        "metadata": {
            "authority_block_heading": source_heading,
            "zone": recipe_map.get("zone"),
            "recipe_number": recipe_map.get("recipe_number"),
            "title_aliases": recipe_map.get("title_aliases", []),
            "inherited_from": recipe_map.get("inherits_from"),
        },
        "materials": [],
        "spices": [],
        "liquids": [],
        "casings": [],
        "equipment": [],
        "process": [],
        "critical_controls": [],
        "storage": [],
        "source": {
            "file": None,
            "sha256": None,
            "extracted_at": datetime.now(timezone.utc).isoformat(),
        },
        "audit": {
            "status": "FAIL",
            "ingredient_status": "FAIL",
            "process_status": "FAIL",
            "overall_status": "FAIL",
            "warnings": [],
            "missing_fields": [],
            "ingredient_missing_fields": [],
            "process_missing_fields": [],
            "process_fail_fields": [],
            "process_mapping_errors": [],
            "process_contamination_errors": [],
            "not_specified_in_source": [],
            "skipped_table_header_rows": [],
            "moved_to_casings": [],
            "moved_to_equipment": [],
            "reclassified_to_materials": [],
            "reclassified_to_spices": [],
            "reclassified_to_liquids": [],
            "reclassified_to_casings": [],
            "reclassified_to_equipment": [],
            "reclassified_to_garlic_or_liquids": [],
            "quantity_from_column": [],
            "note_dimensions_detected": [],
            "suspicious_quantity_unit": [],
            "status_reason": [],
            "empty_quantity_by_group": {
                "materials": [],
                "spices": [],
                "liquids": [],
                "casings": [],
                "equipment": [],
            },
            "missing_required_values": [],
            "forbidden_old_values_found": [],
            "legacy_conflict_block_found_outside_authority": "NO",
            "inherited_from": recipe_map.get("inherits_from"),
            "inherited_fields": [],
            "override_fields": [],
        },
    }


def is_recipe_heading(line: str) -> bool:
    return bool(re.match(r"^#{2,3}\s+\d+\.\s+", line.strip()))


def is_recipe_discovery_heading(line: str) -> bool:
    stripped = line.strip()
    if not re.match(r"^#{2,3}\s+\d+\.\s+", stripped):
        return False
    lowered = normalize_for_search(stripped).lower()
    excluded = ["sastojci", "proces izrade", "zrenje", "cuvanje", "napomene", "oprema"]
    return not any(term in lowered for term in excluded)


def heading_title(heading: str) -> str:
    return re.sub(r"^#{2,3}\s+\d+\.\s*", "", heading).strip()


def heading_number(heading: str) -> int | None:
    match = re.match(r"^#{2,3}\s+(\d+)\.\s+", heading.strip())
    return int(match.group(1)) if match else None


def discover_recipe_headings(source_text: str) -> list[dict[str, Any]]:
    headings: list[dict[str, Any]] = []
    for line_no, line in enumerate(source_text.splitlines(), start=1):
        stripped = line.strip()
        if is_recipe_discovery_heading(stripped):
            headings.append(
                {
                    "line": line_no,
                    "heading": stripped,
                    "title": heading_title(stripped),
                    "number": heading_number(stripped),
                }
            )
    return headings


def token_set(text: str) -> set[str]:
    normalized = normalize_for_search(text).lower()
    return {token for token in re.split(r"[^a-z0-9]+", normalized) if token}


def score_heading(recipe_map: dict[str, Any], candidate: dict[str, Any]) -> tuple[float, str]:
    aliases = [str(item) for item in recipe_map.get("title_aliases", [])]
    recipe_number = recipe_map.get("recipe_number")
    heading = str(candidate["heading"])
    title = str(candidate["title"])
    normalized_heading = normalize_for_search(heading).lower()
    normalized_title = normalize_for_search(title).lower()

    best_score = 0.0
    best_reason = "no_safe_match"
    for alias in aliases:
        normalized_alias = normalize_for_search(alias).lower()
        if normalized_alias == normalized_title or normalized_alias == normalized_heading:
            best_score = max(best_score, 1.0)
            best_reason = "exact_alias"
            continue
        if normalized_alias in normalized_heading:
            score = 0.94
            if score > best_score:
                best_score = score
                best_reason = "normalized_alias"
        alias_tokens = token_set(alias)
        heading_tokens = token_set(title)
        if alias_tokens and heading_tokens:
            overlap = len(alias_tokens & heading_tokens) / len(alias_tokens | heading_tokens)
            ratio = SequenceMatcher(None, normalized_alias, normalized_title).ratio()
            score = max(overlap, ratio * 0.92)
            if score > best_score:
                best_score = score
                best_reason = "partial_match"

    if recipe_number and candidate.get("number") == int(recipe_number):
        if best_score >= 0.65:
            best_score = min(1.0, best_score + 0.12)
        else:
            best_score = 0.72
            best_reason = "recipe_number"
    return best_score, best_reason


def discover_for_pilot(root: Path, recipe_ids: list[str]) -> dict[str, Any]:
    ensure_dirs(root)
    manifest = parse_simple_yml(root / "source_recipes" / "hr" / "source_priority_manifest.yml")
    recipe_map_path = root / "tools" / "source_lock_compiler" / "recipe_id_map.yml"
    recipe_maps = parse_recipe_id_map(recipe_map_path)
    warnings: list[str] = []
    source = load_primary_source(root, manifest, warnings)
    candidates_path = root / "build" / "source_lock_audit" / "pilot10_heading_candidates.txt"
    candidates_path.parent.mkdir(parents=True, exist_ok=True)

    if source is None:
        lines = ["SOURCE: missing", *warnings]
        selected: dict[str, str] = {}
        for recipe_id in recipe_ids:
            recipe_map = recipe_maps.get(recipe_id, {})
            locked = bool(recipe_map.get("authority_locked")) or str(recipe_map.get("heading_status", "")).upper() == "LOCKED"
            locked_heading = str(recipe_map.get("source_heading") or "")
            if locked:
                selected[recipe_id] = locked_heading
            lines.extend(
                [
                    "",
                    f"RECIPE: {recipe_id} | {recipe_title(recipe_id, recipe_map)}",
                    "CANDIDATES:",
                    "SELECTED: LOCKED" if locked else "SELECTED:",
                    f"heading={locked_heading}" if locked else "REVIEW_HEADING",
                    "REASON:",
                    "authority_locked; no_source" if locked else "no_source",
                ]
            )
        candidates_path.write_text("\n".join(lines) + "\n", encoding="utf-8")
        return {"headings_count": 0, "selected": selected, "candidates_path": candidates_path}

    headings = discover_recipe_headings(source.text)
    lines = [f"SOURCE: {source.path}", f"RECIPE_HEADINGS_FOUND: {len(headings)}"]
    selected: dict[str, str] = {}
    for recipe_id in recipe_ids:
        recipe_map = recipe_maps.get(recipe_id, {})
        locked = str(recipe_map.get("authority_locked", "")).lower() == "true" or str(recipe_map.get("heading_status", "")).upper() == "LOCKED"
        if locked:
            locked_heading = str(recipe_map.get("source_heading") or "")
            selected[recipe_id] = locked_heading
            lines.extend(
                [
                    "",
                    f"RECIPE: {recipe_id} | {recipe_title(recipe_id, recipe_map)}",
                    "CANDIDATES:",
                    "SELECTED: LOCKED",
                    f"heading={locked_heading}",
                    "REASON:",
                    "authority_locked",
                ]
            )
            continue
        scored = []
        for candidate in headings:
            score, reason = score_heading(recipe_map, candidate)
            if score > 0:
                scored.append({**candidate, "score": score, "reason": reason})
        scored.sort(key=lambda item: item["score"], reverse=True)
        top = scored[0] if scored else None
        runner_up = scored[1] if len(scored) > 1 else None
        high_candidates = [item for item in scored if item["score"] >= 0.90]
        safe = bool(top and top["score"] >= 0.90 and len(high_candidates) == 1)
        reason = str(top["reason"]) if top else "no_safe_match"
        lines.extend(["", f"RECIPE: {recipe_id} | {recipe_title(recipe_id, recipe_map)}", "CANDIDATES:"])
        for item in scored[:8]:
            lines.append(f"line={item['line']} | score={item['score']:.2f} | heading={item['heading']}")
        lines.append("SELECTED:")
        if safe:
            selected_heading = str(top["heading"])
            selected[recipe_id] = selected_heading
            recipe_map["source_heading"] = selected_heading
            recipe_map.pop("heading_status", None)
            lines.append(f"line={top['line']} | heading={selected_heading}")
            lines.extend(["REASON:", reason])
        else:
            recipe_map["heading_status"] = "REVIEW"
            lines.append("REVIEW_HEADING")
            lines.extend(["REASON:", "no_safe_match" if top is None else f"{reason}; ambiguous_or_score_below_threshold"])
        if recipe_map:
            recipe_maps[recipe_id] = recipe_map

    candidates_path.write_text("\n".join(lines) + "\n", encoding="utf-8")
    write_recipe_id_map(recipe_map_path, recipe_maps)
    return {"headings_count": len(headings), "selected": selected, "candidates_path": candidates_path}


def find_heading_by_map(text: str, recipe_map: dict[str, Any]) -> str | None:
    declared = recipe_map.get("source_heading")
    if declared and any(normalize_for_search(line.strip()) == normalize_for_search(str(declared)) for line in text.splitlines()):
        return str(declared)
    aliases = [str(item) for item in recipe_map.get("title_aliases", [])]
    number = recipe_map.get("recipe_number")
    for line in text.splitlines():
        stripped = line.strip()
        if not is_recipe_heading(stripped):
            continue
        if number and not re.match(rf"^#{{2,3}}\s+{int(number)}\.\s+", stripped):
            continue
        if aliases and any(contains_token(stripped, alias) for alias in aliases):
            return stripped
    for line in text.splitlines():
        stripped = line.strip()
        if is_recipe_heading(stripped) and aliases and any(contains_token(stripped, alias) for alias in aliases):
            return stripped
    return str(declared) if declared else None


def extract_authority_block(text: str, source_heading: str) -> tuple[str | None, str | None]:
    lines = text.splitlines()
    start = None
    for index, line in enumerate(lines):
        if normalize_for_search(line.strip()) == normalize_for_search(source_heading):
            start = index
            break
    if start is None:
        return None, None
    end = len(lines)
    for index in range(start + 1, len(lines)):
        if is_recipe_heading(lines[index]):
            end = index
            break
    return lines[start].strip(), "\n".join(lines[start:end]).strip()


def is_table_separator_cells(cells: list[str]) -> bool:
    return bool(cells) and all(re.fullmatch(r":?-{2,}:?", cell.strip()) for cell in cells)


def is_table_header_cells(cells: list[str]) -> bool:
    if not cells:
        return False
    first = normalize_for_search(cells[0]).lower().strip(" :")
    return first in TABLE_HEADER_FIRST_CELLS


def markdown_rows(block: str, audit: dict[str, Any] | None = None) -> list[list[str]]:
    rows: list[list[str]] = []
    for raw_line in block.splitlines():
        line = raw_line.strip()
        if not line.startswith("|") or not line.endswith("|"):
            continue
        cells = [cell.strip() for cell in line.strip("|").split("|")]
        if not cells or is_table_separator_cells(cells):
            continue
        if is_table_header_cells(cells):
            if audit is not None:
                audit.setdefault("skipped_table_header_rows", []).append(" | ".join(cells))
            continue
        rows.append(cells)
    return rows


def section_text(block: str, heading_text: str) -> str:
    lines = block.splitlines()
    start = None
    for index, line in enumerate(lines):
        stripped = line.strip()
        if stripped.startswith("#") and contains_token(stripped, heading_text):
            start = index + 1
            break
    if start is None:
        return ""
    end = len(lines)
    for index in range(start, len(lines)):
        stripped = lines[index].strip()
        if stripped.startswith("### ") and not contains_token(stripped, heading_text):
            end = index
            break
        if is_recipe_heading(stripped):
            end = index
            break
    return "\n".join(lines[start:end]).strip()


def normalize_range(value: str) -> str:
    return re.sub(r"\s*[-–]\s*", "–", value).strip()


def quantity_from_text(text: str) -> str | None:
    patterns = [
        r"\d+(?:,\d+)?\s*[-–]\s*\d+(?:,\d+)?\s*(?:kg|g|kom|L|l|ml|mm|cm|%)",
        r"\d+(?:,\d+)?\s*(?:kg|g|kom|L|l|ml|mm|cm|%)",
        r"vezivanje",
    ]
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            return normalize_range(match.group(0))
    return None


def note_dimension_from_text(text: str | None) -> str | None:
    if not text:
        return None
    match = re.search(r"\d+(?:,\d+)?\s*[-–]\s*\d+(?:,\d+)?\s*mm|\d+(?:,\d+)?\s*mm", text, re.IGNORECASE)
    return normalize_range(match.group(0)) if match else None


def quantity_from_column(cells: list[str], audit: dict[str, Any], source_line: str) -> str | None:
    if len(cells) >= 2:
        quantity_cell = cells[1].strip()
        if quantity_cell:
            quantity = normalize_range(quantity_cell)
            audit.setdefault("quantity_from_column", []).append(source_line)
            return quantity
        return None
    return quantity_from_text(source_line)


def has_term(text: str, term: str) -> bool:
    normalized_text = normalize_for_search(text).lower()
    normalized_term = normalize_for_search(term).lower()
    if " " in normalized_term:
        return normalized_term in normalized_text
    return bool(re.search(rf"(?<![a-z0-9]){re.escape(normalized_term)}(?![a-z0-9])", normalized_text))


def has_any_term(text: str, terms: list[str]) -> bool:
    return any(has_term(text, term) for term in terms)


def quantity_has_mass_or_volume(quantity: str | None) -> bool:
    return bool(quantity and re.search(r"\b(?:kg|g|l|L|ml)\b", quantity))


def classify_item(name: str, current_section: str, title: str, note: str | None = None, quantity: str | None = None) -> str:
    name_text = normalize_for_search(name).lower()
    name_note_text = normalize_for_search(f"{name} {note or ''}").lower()
    text = normalize_for_search(f"{current_section} {name} {note or ''}").lower()
    title_norm = normalize_for_search(title).lower()
    if has_term(name_text, "krv") and quantity_has_mass_or_volume(quantity):
        return "materials"
    if has_any_term(name_note_text, CASING_TERMS):
        return "casings"
    if has_any_term(name_note_text, GARLIC_TERMS) and has_any_term(name_note_text, GARLIC_LIQUID_HINTS):
        return "liquids"
    if has_any_term(name_note_text, LIQUID_TERMS):
        if any(normalize_for_search(hint).lower() in title_norm for hint in WHOLE_CUT_HINTS) and "sok" not in name_note_text and "voda" not in name_note_text:
            return "equipment"
        return "liquids"
    if has_any_term(name_note_text, GARLIC_TERMS) and any(normalize_for_search(hint).lower() in title_norm for hint in WHOLE_CUT_HINTS):
        return "spices"
    if has_any_term(name_text, EQUIPMENT_TERMS) or has_any_term(text, ["oprema"]):
        return "equipment"
    if has_any_term(name_note_text, SPICE_TERMS) or "cesnjak u prahu" in name_note_text:
        return "spices"
    if has_any_term(name_note_text, GARLIC_TERMS):
        return "liquids"
    if has_any_term(name_note_text, MATERIAL_TERMS):
        return "materials"
    return "materials"


def add_item(recipe: dict[str, Any], bucket: str, name: str, quantity: str | None, note: str | None, source_line: str) -> dict[str, Any]:
    item: dict[str, Any] = {"name": name, "quantity": quantity, "source_line": source_line}
    if note:
        item["note"] = normalize_range(note)
    recipe[bucket].append(item)
    return item


def add_classified_item(recipe: dict[str, Any], bucket: str, name: str, quantity: str | None, note: str | None, source_line: str) -> None:
    audit = recipe["audit"]
    if bucket == "casings":
        audit.setdefault("moved_to_casings", []).append(name)
        audit.setdefault("reclassified_to_casings", []).append(name)
    elif bucket == "equipment":
        audit.setdefault("moved_to_equipment", []).append(name)
        audit.setdefault("reclassified_to_equipment", []).append(name)
    elif bucket == "materials":
        audit.setdefault("reclassified_to_materials", []).append(name)
    elif bucket == "spices":
        audit.setdefault("reclassified_to_spices", []).append(name)
    elif bucket == "liquids":
        audit.setdefault("reclassified_to_liquids", []).append(name)
        normalized = normalize_for_search(f"{name} {note or ''}").lower()
        if any(term in normalized for term in GARLIC_TERMS):
            audit.setdefault("reclassified_to_garlic_or_liquids", []).append(name)
    if quantity in (None, ""):
        audit.setdefault("empty_quantity_by_group", {}).setdefault(bucket, []).append(name)
        if bucket in {"casings", "equipment"}:
            audit.setdefault("not_specified_in_source", []).append(f"{bucket}.{name}.quantity")
    if quantity and contains_token(quantity, "mm") and bucket in {"materials", "spices", "liquids"}:
        audit.setdefault("suspicious_quantity_unit", []).append(f"{bucket}.{name}:{quantity}")
    dimension = note_dimension_from_text(note)
    if dimension:
        audit.setdefault("note_dimensions_detected", []).append(f"{name}:{dimension}")
    item = add_item(recipe, bucket, name, quantity, note, source_line)
    if dimension:
        if bucket == "equipment":
            item["grind_plate_mm"] = dimension
        else:
            item["cut_size"] = dimension


def find_hr001_item(block: str, label: str, quantity_pattern: str) -> tuple[str | None, str | None, str | None]:
    label_key = normalize_for_search(label).lower().split("(")[0].strip()
    quantity_re = re.compile(quantity_pattern, re.IGNORECASE)
    for row in markdown_rows(block):
        row_text = " | ".join(row)
        if label_key in normalize_for_search(row_text).lower() and quantity_re.search(row_text):
            quantity = normalize_range(quantity_re.search(row_text).group(0))  # type: ignore[union-attr]
            note = next((cell for cell in row if contains_token(cell, "80–120 mm")), None)
            return row_text, quantity, note
    for raw_line in block.splitlines():
        line = raw_line.strip(" \t-*|")
        if label_key in normalize_for_search(line).lower() and quantity_re.search(line):
            quantity = normalize_range(quantity_re.search(line).group(0))  # type: ignore[union-attr]
            return line, quantity, line if contains_token(line, "80–120 mm") else None
    return None, None, None


def extract_hr001_items(block: str, recipe: dict[str, Any]) -> None:
    warnings = recipe["audit"]["warnings"]
    ingredient_missing = recipe["audit"]["ingredient_missing_fields"]
    ingredient_section = section_text(block, "Sastojci") or block
    for bucket, name, quantity_pattern, required_note in HR_SL_001_ITEM_SPECS:
        line, quantity, row_note = find_hr001_item(ingredient_section, name, quantity_pattern)
        if line is None:
            ingredient_missing.append(f"{bucket}.{name}")
            warnings.append(f"Missing source evidence in authority ingredient table for {name}")
            continue
        note = row_note if row_note else (required_note if required_note and contains_token(ingredient_section, "80–120 mm") else None)
        if required_note and not note:
            ingredient_missing.append(f"{bucket}.{name}.note")
        if bucket == "equipment":
            quantity = "vezivanje" if contains_token(line, "vezivanje") or contains_token(ingredient_section, "vezivanje") else quantity
        add_item(recipe, bucket, name, quantity, note, line)


def extract_generic_items(block: str, recipe: dict[str, Any]) -> None:
    ingredient_section = section_text(block, "Sastojci") or block
    current_section = "materials"
    found = 0
    for raw_line in ingredient_section.splitlines():
        line = raw_line.strip()
        if not line:
            continue
        if line.startswith("#"):
            current_section = line.strip("# ").strip()
            continue
        if line.startswith("|") and line.endswith("|"):
            continue
        if line.startswith("-") or line.startswith("*"):
            text = line.strip("-* ").strip()
            parts = [part.strip() for part in text.split("|")]
            name = parts[0] if parts else text
            if is_table_header_cells([name]):
                recipe["audit"].setdefault("skipped_table_header_rows", []).append(text)
                continue
            quantity = quantity_from_column(parts, recipe["audit"], text) if len(parts) > 1 else quantity_from_text(text)
            note = " | ".join(parts[2:]) if len(parts) > 2 else None
            bucket = classify_item(name, current_section, recipe["title"], note, quantity)
            add_classified_item(recipe, bucket, name, quantity, note, text)
            found += 1
    for row in markdown_rows(ingredient_section, recipe["audit"]):
        row_text = " | ".join(row)
        if len(row) < 2:
            continue
        name = row[0]
        if is_table_header_cells([name]):
            recipe["audit"].setdefault("skipped_table_header_rows", []).append(row_text)
            continue
        quantity = quantity_from_column(row, recipe["audit"], row_text)
        note = " | ".join(row[2:]) if len(row) > 2 else None
        bucket = classify_item(name, current_section, recipe["title"], note, quantity)
        add_classified_item(recipe, bucket, name, quantity, note, row_text)
        found += 1
    if found == 0:
        recipe["audit"]["ingredient_missing_fields"].append("ingredients")
        recipe["audit"]["warnings"].append("No parseable ingredient rows found in authority block")


def extract_items(block: str, recipe: dict[str, Any]) -> None:
    if recipe["recipe_id"] == "HR-SL-001":
        extract_hr001_items(block, recipe)
    else:
        extract_generic_items(block, recipe)


def evidence_lines(block: str, terms: list[str]) -> str | None:
    lines = []
    for raw_line in block.splitlines():
        line = raw_line.strip(" \t-*|")
        if line and any(contains_token(line, term) for term in terms):
            lines.append(line)
    return "\n".join(lines).strip() or None


def override_phase(process: list[dict[str, Any]], title: str, evidence: str, duration: str | None = None) -> None:
    for phase in process:
        if phase.get("title") == title:
            phase["action"] = evidence
            phase["source_evidence"] = evidence
            if duration:
                phase["duration"] = duration
            phase["critical_control"] = evidence
            return
    process.append(
        {
            "title": title,
            "duration": duration or extract_duration(evidence),
            "temperature": extract_temperature(evidence),
            "humidity": extract_humidity(evidence),
            "action": evidence,
            "critical_control": evidence,
            "source_evidence": evidence,
        }
    )


def set_phase(process: list[dict[str, Any]], title: str, action: str, duration: str | None = None, source_evidence: str | None = None) -> None:
    evidence = source_evidence or action
    for phase in process:
        if phase.get("title") == title:
            phase.update(
                {
                    "duration": duration,
                    "temperature": extract_temperature(action),
                    "humidity": extract_humidity(action),
                    "action": action,
                    "critical_control": action,
                    "source_evidence": evidence,
                }
            )
            return
    process.append(
        {
            "title": title,
            "duration": duration,
            "temperature": extract_temperature(action),
            "humidity": extract_humidity(action),
            "action": action,
            "critical_control": action,
            "source_evidence": evidence,
        }
    )


def apply_hr002_inheritance(recipe: dict[str, Any], parent: dict[str, Any], authority_block: str) -> None:
    recipe["materials"] = copy.deepcopy(parent.get("materials", []))
    recipe["spices"] = copy.deepcopy(parent.get("spices", []))
    recipe["liquids"] = copy.deepcopy(parent.get("liquids", []))
    recipe["process"] = copy.deepcopy(parent.get("process", []))
    recipe["metadata"]["inherited_from"] = "HR-SL-001"
    recipe["audit"]["inherited_from"] = "HR-SL-001"
    recipe["audit"]["inherited_fields"] = ["materials", "spices", "liquids"]
    recipe["audit"]["override_fields"] = ["casings", "process.priprema omotača", "process.punjenje", "process.zrenje", "process.čuvanje", "storage", "product_weight"]
    recipe["audit"].setdefault("status_reason", []).append("HR-SL-002 inherits base filling formula from locked HR-SL-001 and applies source overrides")

    casing_evidence = evidence_lines(authority_block, ["rektum", "ravno stražnje crijevo", "ravno straznje crijevo", "50–60 mm", "50-60 mm"])
    if casing_evidence:
        add_item(
            recipe,
            "casings",
            "Svinjsko ravno stražnje crijevo / rektum",
            None,
            "kalibar ~50–60 mm",
            casing_evidence,
        )
    else:
        recipe["audit"]["ingredient_missing_fields"].append("casings.rektum")

    prep_action = "Izvor navodi da se Kulenova seka puni u svinjsko ravno stražnje crijevo / rektum, kalibar približno 50–60 mm. Poseban režim namakanja ili pripreme rektuma nije naveden u izvoru."
    set_phase(recipe["process"], "priprema omotača", prep_action, None)
    recipe["audit"]["not_specified_in_source"].extend(
        [
            "process.priprema_omotača.soaking",
            "process.priprema_omotača.temperature",
            "process.priprema_omotača.duration",
        ]
    )

    recipe["product_weight"] = "300–500 g gotovog" if contains_token(authority_block, "300") and contains_token(authority_block, "500") else None
    if recipe["product_weight"] is None:
        recipe["audit"]["not_specified_in_source"].append("product_weight")

    punjenje_action = "Rektum puniti kompaktno. Vezati na 20–25 cm. Kulenova seka se ne veže konopljenim kanabom kao kulen, nego obično špagom."
    set_phase(recipe["process"], "punjenje", punjenje_action, None)

    zrenje_action = "Zrenje traje 75–90 dana od punjenja, kraće od kulena zbog manjeg promjera. Fermentacija: početni pH 5,5–5,7, finalni pH 5,0–5,4."
    set_phase(recipe["process"], "sušenje / zrenje", zrenje_action, "75–90 dana")

    if contains_token(authority_block, "do 12 mj") or contains_token(authority_block, "12 mj"):
        recipe["storage"] = ["do 12 mjeseci"]
        cuvanje_action = "Čuvanje: do 12 mjeseci. Kulenova seka nije jeftina inačica kulena, nego poseban proizvod s mekšom teksturom i kraćim zrenjem."
        set_phase(recipe["process"], "čuvanje / posluživanje", cuvanje_action, "12 mjeseci")
    else:
        recipe["audit"]["not_specified_in_source"].append("storage")
    if contains_token(authority_block, "nije jeftina"):
        recipe.setdefault("notes", []).append("nije jeftina inačica kulena")


def paragraph_chunks(text: str) -> list[str]:
    chunks = [chunk.strip() for chunk in re.split(r"\n\s*\n+", text) if chunk.strip()]
    return chunks or [line.strip() for line in text.splitlines() if line.strip()]


def extract_duration(chunk: str | None) -> str | None:
    if not chunk:
        return None
    match = re.search(r"\b\d+(?:\s*[-–]\s*\d+)?\s*(?:min|h|sati|dan(?:a)?|tjed(?:na|ana)?|mjesec(?:i|a)?|mj\.)\b", chunk, re.IGNORECASE)
    return normalize_range(match.group(0)) if match else None


def extract_temperature(chunk: str | None) -> str | None:
    if not chunk:
        return None
    match = re.search(r"(?:<=|≤)?\s*\d+(?:\s*[-–]\s*\d+)?\s*(?:°C|℃|stup(?:anj|njeva)?)", chunk, re.IGNORECASE)
    return normalize_range(match.group(0)) if match else None


def extract_humidity(chunk: str | None) -> str | None:
    if not chunk:
        return None
    match = re.search(r"\b\d+(?:\s*[-–]\s*\d+)?\s*%\s*(?:RH|relativn\w*\s+vlažnosti|vlage|vlažnost)?", chunk, re.IGNORECASE)
    return normalize_range(match.group(0)) if match else None


def extract_day_window(chunk: str | None) -> str | None:
    if not chunk:
        return None
    match = re.search(r"Dan\s+\d+\s*[-–]\s*\d+", chunk, re.IGNORECASE)
    return normalize_range(match.group(0)) if match else None


def numbered_phase_block(text: str, markers: list[str]) -> str | None:
    lines = text.splitlines()
    start = None
    for index, line in enumerate(lines):
        if any(contains_token(line, marker) for marker in markers):
            start = index
            break
    if start is None:
        return None
    end = len(lines)
    for index in range(start + 1, len(lines)):
        stripped = lines[index].strip()
        if re.match(r"^\*\*\d+\.\s+", stripped):
            end = index
            break
        if stripped.startswith("### ") and not contains_token(stripped, "Zrenje i čuvanje"):
            end = index
            break
    return "\n".join(lines[start:end]).strip()


def focused_lines(text: str, include_terms: list[str]) -> str:
    selected = []
    for raw_line in text.splitlines():
        line = raw_line.strip()
        if line and any(contains_token(line, term) for term in include_terms):
            selected.append(line)
    return "\n".join(selected).strip() or text.strip()


def zrenje_evidence(block: str) -> str | None:
    zrenje = section_text(block, "Zrenje i čuvanje") or section_text(block, "Zrenje i cuvanje")
    if not zrenje:
        return None
    lines = []
    for raw_line in zrenje.splitlines():
        line = raw_line.strip()
        if not line:
            continue
        if contains_token(line, "Čuvanje:") or contains_token(line, "Cuvanje:"):
            break
        if any(contains_token(line, term) for term in ["Minimalno zrenje", "Optimum", "gubitak mase", "30 %", "12–16°C", "12-16°C", "zrenje"]):
            lines.append(line)
    return "\n".join(lines).strip() or zrenje.strip()


def cuvanje_evidence(block: str) -> str | None:
    zrenje = section_text(block, "Zrenje i čuvanje") or section_text(block, "Zrenje i cuvanje")
    if not zrenje:
        return None
    lines = []
    capture = False
    for raw_line in zrenje.splitlines():
        line = raw_line.strip()
        if not line:
            continue
        if any(contains_token(line, term) for term in ["Čuvanje:", "Cuvanje:", "Rez:", "mozaik", "duboko crvena"]):
            capture = True
        if capture:
            lines.append(line)
    return "\n".join(lines).strip() or None


def generic_phase_evidence(block: str, title: str, keywords: list[str]) -> str | None:
    candidates = paragraph_chunks(section_text(block, "Proces") or block)
    for chunk in candidates:
        normalized = normalize_for_search(chunk).lower()
        if any(normalize_for_search(keyword).lower() in normalized for keyword in keywords):
            return chunk
    return None


def phase_evidence(block: str, title: str, keywords: list[str], recipe_id: str) -> str | None:
    if recipe_id == "HR-SL-001":
        if title == "odabir sirovine":
            return numbered_phase_block(block, ["**1. Odabir sirovine", "1. Odabir sirovine"])
        if title == "rezanje / mljevenje":
            return numbered_phase_block(block, ["**2. Rezanje / mljevenje", "2. Rezanje / mljevenje"])
        if title in {"miješanje", "odmor nadjeva"}:
            return numbered_phase_block(block, ["**3. Miješanje i odmor mase", "3. Miješanje i odmor mase", "3. Mijesanje i odmor mase"])
        if title == "priprema omotača":
            return numbered_phase_block(block, ["**4. Priprema kate", "4. Priprema kate"])
        if title == "punjenje":
            return numbered_phase_block(block, ["**5. Punjenje i vezivanje", "5. Punjenje i vezivanje"])
        if title == "dimljenje":
            return numbered_phase_block(block, ["**6. Dimljenje", "6. Dimljenje"])
        if title == "sušenje / zrenje":
            return zrenje_evidence(block)
        if title == "čuvanje / posluživanje":
            return cuvanje_evidence(block)
    return generic_phase_evidence(block, title, keywords)


def phase_action(title: str, evidence: str | None, recipe_id: str) -> str | None:
    if not evidence:
        return None
    if recipe_id == "HR-SL-001":
        if title == "miješanje":
            return focused_lines(evidence, ["dodavati postepeno", "miješati", "30–45 min", "30-45 min", "ujednačeno crvena", "bijelih mrlja"])
        if title == "odmor nadjeva":
            return focused_lines(evidence, ["odmor", "2–4°C", "2-4°C", "min. 24 h", "proteina", "vezivanje"])
        if title == "sušenje / zrenje":
            return focused_lines(evidence, ["Minimalno zrenje", "Optimum", "gubitak mase", "30 %", "12–16°C", "12-16°C"])
        if title == "čuvanje / posluživanje":
            return focused_lines(evidence, ["Čuvanje", "Cuvanje", "18 mj", "12–16°C", "12-16°C", "Rez", "3–5 mm", "3-5 mm", "mozaik", "duboko crvena"])
    return evidence


def extract_phase_duration(title: str, evidence: str | None, recipe_id: str) -> str | None:
    if not evidence:
        return None
    if recipe_id == "HR-SL-001":
        if title == "miješanje":
            match = re.search(r"\b30\s*[-–]\s*45\s*min\b", evidence, re.IGNORECASE)
            return normalize_range(match.group(0)) if match else None
        if title == "odmor nadjeva":
            if re.search(r"min\.\s*24\s*h\b", evidence, re.IGNORECASE) or re.search(r"\b24\s*h\b", evidence, re.IGNORECASE):
                return "24 h"
            return None
    return extract_duration(evidence)


def critical_control_for(title: str, evidence: str | None) -> str | None:
    if not evidence:
        return None
    terms_by_title = {
        "odabir sirovine": ["1. studeni", "31. ožujka", "140 kg", "12–20", "≤4°C"],
        "rezanje / mljevenje": ["≤2°C", "≤4°C", "6–12 mm", "8 mm", "12 mm"],
        "miješanje": ["30–45 min", "bez bijelih mrlja"],
        "odmor nadjeva": ["2–4°C", "min. 24 h"],
        "priprema omotača": ["30 g soli/L", "min. 12 h"],
        "punjenje": ["bez mjehurića", "10–15 puta", "konopljenim kanabom"],
        "dimljenje": ["max 25°C", "15–25 ciklusa", "3–4 tjedna"],
        "sušenje / zrenje": ["150 dana", "6–9 mj", "30 %", "12–16°C"],
        "čuvanje / posluživanje": ["18 mj", "12–16°C", "3–5 mm", "duboko crvena"],
    }
    return focused_lines(evidence, terms_by_title.get(title, [])) or None


def add_cooked_phase_if_needed(block: str, process: list[dict[str, Any]], recipe: dict[str, Any]) -> None:
    title_norm = normalize_for_search(recipe["title"]).lower()
    if not any(hint in title_norm for hint in COOKED_HINTS):
        return
    if any(contains_token(str(phase.get("source_evidence")), "baren") or contains_token(str(phase.get("source_evidence")), "kuhan") for phase in process):
        return
    cooked = generic_phase_evidence(block, "barenje", ["barenje", "bariti", "kuhanje", "kuhati"])
    if cooked:
        process.append(
            {
                "title": "barenje / kuhanje",
                "duration": extract_duration(cooked),
                "temperature": extract_temperature(cooked),
                "humidity": extract_humidity(cooked),
                "action": cooked,
                "critical_control": focused_lines(cooked, ["temperatura", "bariti", "kuhati", "ohladiti"]),
                "source_evidence": cooked,
            }
        )
    else:
        recipe["audit"]["process_missing_fields"].append("process.barenje / kuhanje")
        recipe["audit"]["warnings"].append("Cooked/bareni product has no parseable barenje/kuhanje process evidence")


def normalize_hr014_process_text(text: str | None) -> str | None:
    if text is None:
        return None
    return text.replace("měseci", "mjeseci").replace("măseci", "mjeseci")


def hr014_storage_block(block: str) -> str:
    return normalize_hr014_process_text(section_text(block, "Zrenje i čuvanje") or section_text(block, "Zrenje i cuvanje") or "") or ""


def hr014_storage_cuvanje_text(block: str) -> str | None:
    storage = hr014_storage_block(block)
    if not storage:
        return None
    lines: list[str] = []
    capture = False
    for raw_line in storage.splitlines():
        line = raw_line.strip(" \t-*")
        if not line:
            continue
        if contains_token(line, "Čuvanje:") or contains_token(line, "Cuvanje:") or contains_token(line, "Napomena:"):
            capture = True
        if capture:
            lines.append(line)
    return "\n".join(lines).strip() or None


def hr014_storage_zrenje_duration(block: str) -> str | None:
    storage = hr014_storage_block(block)
    for raw_line in storage.splitlines():
        line = raw_line.strip(" \t-*")
        if contains_token(line, "Zrenje:"):
            return line.split(":", 1)[1].strip() if ":" in line else line
    return None


def strip_hr014_storage_from_zrenje(evidence: str) -> str:
    evidence = normalize_hr014_process_text(evidence) or ""
    cut_patterns = ["### Zrenje i čuvanje", "### Zrenje i cuvanje", "Čuvanje:", "Cuvanje:", "Napomena:"]
    cut_at: int | None = None
    for pattern in cut_patterns:
        index = normalize_for_search(evidence).lower().find(normalize_for_search(pattern).lower())
        if index >= 0:
            cut_at = index if cut_at is None else min(cut_at, index)
    return evidence[:cut_at].strip() if cut_at is not None else evidence.strip()


def extract_hr014_process(block: str, recipe: dict[str, Any]) -> None:
    phase_specs = [
        ("priprema", ["**1. Priprema", "1. Priprema"], ["0–2 °C", "0-2 °C", "1–2 cm", "1-2 cm"]),
        ("suho soljenje", ["**2. Suho soljenje", "2. Suho soljenje"], ["2–4 °C", "2-4 °C", "2–3 dana", "2-3 dana"]),
        ("pranje i sušenje", ["**3. Pranje i sušenje", "3. Pranje i sušenje"], ["3 dana", "hladnom vodom"]),
        ("dimljenje", ["**4. Dimljenje", "4. Dimljenje"], ["max 18 °C", "8–12 ciklusa", "8-12 ciklusa"]),
        ("zrenje", ["**5. Zrenje", "5. Zrenje"], ["8–14 °C", "8-14 °C", "70–75 %", "70-75 %", "20–25 %", "20-25 %"]),
        ("čuvanje", ["**6. Čuvanje", "6. Čuvanje", "**6. Cuvanje", "6. Cuvanje"], ["do 12 mjeseci", "do 12 mj", "dimljenje je obavezno"]),
    ]
    process = []
    for title, markers, control_terms in phase_specs:
        evidence = numbered_phase_block(block, markers)
        if evidence is None and title == "čuvanje":
            evidence = hr014_storage_cuvanje_text(block)
        if evidence is None:
            recipe["audit"]["process_missing_fields"].append(f"process.{title}")
            recipe["audit"]["warnings"].append(f"Missing HR-SL-014 process evidence: {title}")
            continue
        evidence = normalize_hr014_process_text(evidence) or evidence
        secondary_duration = None
        if title == "zrenje":
            secondary_duration = hr014_storage_zrenje_duration(block)
            evidence = strip_hr014_storage_from_zrenje(evidence)
        duration = extract_day_window(evidence) or extract_duration(evidence)
        if title == "pranje i sušenje" and contains_token(evidence, "3 dana"):
            duration = "3 dana"
        phase = {
            "title": title,
            "duration": duration,
            "temperature": extract_temperature(evidence),
            "humidity": extract_humidity(evidence),
            "action": evidence,
            "critical_control": focused_lines(evidence, control_terms),
            "source_evidence": evidence,
        }
        if secondary_duration:
            phase["secondary_duration"] = secondary_duration
            phase["note"] = f"Storage block states zrenje: {secondary_duration}"
        process.append(phase)
    recipe["process"] = process


def split_hr014_zrenje_cuvanje(recipe: dict[str, Any]) -> None:
    zrenje_phase = next((phase for phase in recipe["process"] if phase.get("title") == "zrenje"), None)
    cuvanje_phase = next((phase for phase in recipe["process"] if contains_token(str(phase.get("title")), "čuvanje") or contains_token(str(phase.get("title")), "cuvanje")), None)
    if not zrenje_phase:
        return
    evidence = str(zrenje_phase.get("source_evidence") or "")
    if not contains_token(evidence, "Čuvanje:") and not contains_token(evidence, "Cuvanje:"):
        return
    lines = evidence.splitlines()
    zrenje_lines: list[str] = []
    cuvanje_lines: list[str] = []
    in_cuvanje = False
    for line in lines:
        if contains_token(line, "Čuvanje:") or contains_token(line, "Cuvanje:") or contains_token(line, "Napomena:"):
            in_cuvanje = True
        if in_cuvanje:
            cuvanje_lines.append(line)
        else:
            zrenje_lines.append(line)
    zrenje_text = "\n".join(zrenje_lines).strip()
    cuvanje_text = "\n".join(cuvanje_lines).strip()
    if zrenje_text:
        zrenje_phase["action"] = zrenje_text
        zrenje_phase["source_evidence"] = zrenje_text
        zrenje_phase["critical_control"] = focused_lines(zrenje_text, ["8–14 °C", "8-14 °C", "70–75 %", "70-75 %", "20–25 %", "20-25 %", "3–6 mjeseci", "3-6 mjeseci"])
        zrenje_phase["duration"] = extract_day_window(zrenje_text) or extract_duration(zrenje_text)
        zrenje_phase["temperature"] = extract_temperature(zrenje_text)
        zrenje_phase["humidity"] = extract_humidity(zrenje_text)
    if cuvanje_text:
        recipe["audit"]["process_missing_fields"] = [item for item in recipe["audit"].get("process_missing_fields", []) if item != "process.čuvanje"]
        if cuvanje_phase:
            cuvanje_phase["action"] = cuvanje_text
            cuvanje_phase["source_evidence"] = cuvanje_text
            cuvanje_phase["critical_control"] = focused_lines(cuvanje_text, ["do 12 mjeseci", "do 12 mj", "dimljenje je obavezno"])
            cuvanje_phase["duration"] = extract_duration(cuvanje_text)
            cuvanje_phase["temperature"] = extract_temperature(cuvanje_text)
            cuvanje_phase["humidity"] = extract_humidity(cuvanje_text)
        else:
            recipe["process"].append(
                {
                    "title": "čuvanje",
                    "duration": extract_duration(cuvanje_text),
                    "temperature": extract_temperature(cuvanje_text),
                    "humidity": extract_humidity(cuvanje_text),
                    "action": cuvanje_text,
                    "critical_control": focused_lines(cuvanje_text, ["do 12 mjeseci", "do 12 mj", "dimljenje je obavezno"]),
                    "source_evidence": cuvanje_text,
                }
            )


def extract_process(block: str, recipe: dict[str, Any]) -> None:
    if recipe["recipe_id"] == "HR-SL-014":
        extract_hr014_process(block, recipe)
        split_hr014_zrenje_cuvanje(recipe)
        return

    warnings = recipe["audit"]["warnings"]
    process_missing = recipe["audit"]["process_missing_fields"]
    process_fail = recipe["audit"]["process_fail_fields"]
    mapping_errors = recipe["audit"]["process_mapping_errors"]
    not_specified = recipe["audit"]["not_specified_in_source"]
    process: list[dict[str, Any]] = []
    recipe_id = recipe["recipe_id"]

    for title, keywords in PROCESS_PHASES:
        evidence = phase_evidence(block, title, keywords, recipe_id)
        action = phase_action(title, evidence, recipe_id)
        if evidence is None and recipe_id != "HR-SL-001":
            continue
        phase = {
            "title": title,
            "duration": extract_phase_duration(title, evidence, recipe_id),
            "temperature": extract_temperature(evidence),
            "humidity": extract_humidity(evidence),
            "action": action,
            "critical_control": critical_control_for(title, evidence),
            "source_evidence": evidence,
        }
        if phase["action"] is None:
            process_fail.append(f"process.{title}.action")
            warnings.append(f"Missing process action text from authority block: {title}")
        if phase["source_evidence"] is None:
            process_fail.append(f"process.{title}.source_evidence")
            warnings.append(f"Missing process source evidence from authority block: {title}")
        if title == "miješanje" and phase["duration"] == "24 h":
            error = "process.miješanje.duration_from_odmor"
            process_fail.append(error)
            mapping_errors.append(error)
            warnings.append("Wrong process duration: miješanje used 24 h from odmor nadjeva")
        for field in ("duration", "temperature", "humidity"):
            if phase[field] is None:
                not_specified.append(f"process.{title}.{field}")
        combined = "\n".join(str(phase.get(key) or "") for key in ["action", "source_evidence"])
        if title == "sušenje / zrenje" and any(contains_token(combined, bad) for bad in ["Priprema kate", "slijepo crijevo", "30 g soli/L", "min. 12 h"]):
            error = "process.sušenje / zrenje.mapped_to_priprema_kate"
            process_fail.append(error)
            mapping_errors.append(error)
            warnings.append("Wrong process mapping: sušenje / zrenje uses Priprema kate evidence")
        if title == "priprema omotača" and not any(contains_token(combined, required) for required in ["Priprema kate", "slijepo crijevo", "salamura 30 g soli/L", "30 g soli/L", "crijevo", "mjehur", "omotač", "debelo crijevo"]):
            process_missing.append("process.priprema omotača.casing_evidence")
        process.append(phase)

    add_cooked_phase_if_needed(block, process, recipe)
    if not process:
        process_missing.append("process")
        warnings.append("No parseable process phases found in authority block")
    recipe["process"] = process


def extract_controls_and_storage(block: str, recipe: dict[str, Any]) -> None:
    controls = []
    storage = []
    for raw_line in block.splitlines():
        line = raw_line.strip(" \t-*|")
        if not line:
            continue
        normalized = normalize_for_search(line).lower()
        if any(key in normalized for key in ["max 10", "bez nitrata", "samo sok", "80-120 mm", "prirodni omotac", "kanab", "baren", "kuhan"]):
            controls.append(line)
        if any(key in normalized for key in ["zrenje", "cuvanje", "čuvanje", "posluzivanje", "posluživanje", "skladistenje"]):
            storage.append(line)
    recipe["critical_controls"] = controls
    recipe["storage"] = storage


def legacy_conflict_outside_authority(source_text: str, authority_block: str | None) -> bool:
    outside = source_text
    if authority_block:
        outside = source_text.replace(authority_block, "")
    return any(contains_token(outside, value) for value in LEGACY_CONFLICT_VALUES)


def required_tokens_for(recipe_id: str) -> list[str]:
    return HR_SL_001_REQUIRED_INGREDIENT_TOKENS if recipe_id == "HR-SL-001" else []


def forbidden_values_for(recipe: dict[str, Any], serialized: str) -> list[str]:
    recipe_id = recipe["recipe_id"]
    if recipe_id == "HR-SL-001":
        return [value for value in HR_SL_001_FORBIDDEN_OUTPUT_VALUES if contains_token(serialized, value)]

    critical_serialized = json.dumps(
        {key: recipe.get(key, []) for key in ["materials", "spices", "liquids"]},
        ensure_ascii=False,
        sort_keys=True,
    )
    forbidden = [value for value in UNIVERSAL_FORBIDDEN_OUTPUT_VALUES if contains_token(critical_serialized, value)]
    empty_by_group = recipe["audit"].setdefault("empty_quantity_by_group", {})
    for bucket in ("materials", "spices", "liquids"):
        for item in recipe.get(bucket, []):
            if item.get("quantity") in (None, ""):
                empty_by_group.setdefault(bucket, []).append(item.get("name"))
                forbidden.append(f"empty_quantity:{bucket}.{item.get('name')}")
    return list(dict.fromkeys(forbidden))


def validate_process_contamination(recipe: dict[str, Any]) -> list[str]:
    errors: list[str] = []
    if not recipe.get("process"):
        return errors
    recipe_heading = str(recipe.get("metadata", {}).get("authority_block_heading") or "")
    strict_recipe_ids = {"HR-SL-002", "HR-SL-014"}
    numbered_heading_re = re.compile(r"(?m)^\s*#{2,3}\s+\d+\.\s+")
    phase_heading_re = re.compile(r"\*\*\s*(\d+)\.\s*([^*\n]+)")
    expected_heading_numbers_by_title = {
        "odabir sirovine": {1},
        "rezanje / mljevenje": {2},
        "mijesanje": {3},
        "odmor nadjeva": {3},
        "priprema omotaca": {4},
        "punjenje": {5},
        "dimljenje": {4, 6},
        "susenje / zrenje": {5},
        "zrenje": {5},
        "cuvanje / posluzivanje": {6},
        "priprema": {1},
        "suho soljenje": {2},
        "pranje i susenje": {3},
        "cuvanje": {6},
    }
    for phase in recipe.get("process", []):
        title = str(phase.get("title") or "")
        combined = "\n".join(str(phase.get(key) or "") for key in ["action", "source_evidence"])
        if not combined.strip():
            continue
        if any(contains_token(combined, bad) for bad in ["### Sastojci", "| Komponenta |", "| Količina |"]):
            errors.append(f"process.{title}.contains_ingredient_table")
        if numbered_heading_re.search(combined) and not (recipe_heading and contains_token(combined, recipe_heading)):
            errors.append(f"process.{title}.contains_other_recipe_heading")
        if recipe_heading and contains_token(combined, recipe_heading):
            errors.append(f"process.{title}.contains_recipe_heading")
        if recipe.get("recipe_id") in strict_recipe_ids:
            expected_numbers = expected_heading_numbers_by_title.get(normalize_for_search(title).lower())
            for match in phase_heading_re.finditer(combined):
                number = int(match.group(1))
                if expected_numbers is not None and number not in expected_numbers:
                    errors.append(f"process.{title}.contains_other_numbered_phase_heading:{number}")
        if recipe.get("recipe_id") == "HR-SL-014" and normalize_for_search(title).lower() == "zrenje":
            for bad in ["Čuvanje:", "Cuvanje:", "Napomena:", "dimljenje je obavezno", "### Zrenje i čuvanje", "### Zrenje i cuvanje"]:
                if contains_token(combined, bad):
                    errors.append(f"process.zrenje.storage_block_contamination:{bad}")

    if recipe.get("recipe_id") == "HR-SL-002":
        by_title = {str(phase.get("title") or ""): "\n".join(str(phase.get(key) or "") for key in ["action", "source_evidence"]) for phase in recipe.get("process", [])}
        full_process_text = "\n".join(by_title.values())
        for bad in ["Priprema kate", "slijepo crijevo", "kate od teških svinja", "30 g soli/L", "do 18 mj."]:
            if contains_token(full_process_text, bad):
                errors.append(f"process.HR-SL-002.inherited_forbidden_trace:{bad}")
        prep = by_title.get("priprema omotača", "")
        if any(contains_token(prep, bad) for bad in ["Priprema kate", "slijepo crijevo", "kate od teških svinja"]):
            errors.append("process.priprema omotača.inherited_kate_evidence")
        storage = by_title.get("čuvanje / posluživanje", "")
        if contains_token(storage, "18 mj"):
            errors.append("process.čuvanje / posluživanje.inherited_18_mj")
        if not contains_token(storage, "12 mj"):
            errors.append("process.čuvanje / posluživanje.missing_12_mj")
        zrenje = by_title.get("sušenje / zrenje", "")
        if not contains_token(zrenje, "75–90 dana") and not contains_token(zrenje, "75-90 dana"):
            errors.append("process.sušenje / zrenje.missing_75_90_dana")
    return list(dict.fromkeys(errors))


def audit_recipe(recipe: dict[str, Any]) -> None:
    warnings = list(dict.fromkeys(recipe["audit"]["warnings"]))
    ingredient_missing = list(dict.fromkeys(recipe["audit"]["ingredient_missing_fields"]))
    process_missing = list(dict.fromkeys(recipe["audit"]["process_missing_fields"]))
    process_fail = list(dict.fromkeys(recipe["audit"]["process_fail_fields"]))
    mapping_errors = list(dict.fromkeys(recipe["audit"]["process_mapping_errors"]))
    contamination_errors = list(dict.fromkeys(recipe["audit"].get("process_contamination_errors", []) + validate_process_contamination(recipe)))
    not_specified = list(dict.fromkeys(recipe["audit"]["not_specified_in_source"]))
    skipped_headers = list(dict.fromkeys(recipe["audit"].get("skipped_table_header_rows", [])))
    moved_to_casings = list(dict.fromkeys(recipe["audit"].get("moved_to_casings", [])))
    moved_to_equipment = list(dict.fromkeys(recipe["audit"].get("moved_to_equipment", [])))
    reclassified_to_materials = list(dict.fromkeys(recipe["audit"].get("reclassified_to_materials", [])))
    reclassified_to_spices = list(dict.fromkeys(recipe["audit"].get("reclassified_to_spices", [])))
    reclassified_to_liquids = list(dict.fromkeys(recipe["audit"].get("reclassified_to_liquids", [])))
    reclassified_to_casings = list(dict.fromkeys(recipe["audit"].get("reclassified_to_casings", [])))
    reclassified_to_equipment = list(dict.fromkeys(recipe["audit"].get("reclassified_to_equipment", [])))
    reclassified_to_garlic_or_liquids = list(dict.fromkeys(recipe["audit"].get("reclassified_to_garlic_or_liquids", [])))
    quantity_from_column_rows = list(dict.fromkeys(recipe["audit"].get("quantity_from_column", [])))
    note_dimensions_detected = list(dict.fromkeys(recipe["audit"].get("note_dimensions_detected", [])))
    suspicious_quantity_unit = list(dict.fromkeys(recipe["audit"].get("suspicious_quantity_unit", [])))

    ingredient_serialized = json.dumps(
        {key: recipe[key] for key in ["materials", "spices", "liquids", "casings", "equipment"]},
        ensure_ascii=False,
        sort_keys=True,
    )
    missing_required = []
    for token in required_tokens_for(recipe["recipe_id"]):
        if not contains_token(ingredient_serialized, token):
            missing_required.append(token)
            ingredient_missing.append(f"required_token:{token}")
            warnings.append(f"Required ingredient token not present in source-locked JSON: {token}")

    full_serialized = json.dumps(recipe, ensure_ascii=False, sort_keys=True)
    forbidden = forbidden_values_for(recipe, full_serialized)

    no_ingredients = not any(recipe[key] for key in ["materials", "spices", "liquids", "casings", "equipment"])
    if no_ingredients:
        ingredient_missing.append("ingredients")

    title_norm = normalize_for_search(recipe["title"]).lower()
    if any(normalize_for_search(hint).lower() in title_norm for hint in WHOLE_CUT_HINTS) and recipe["liquids"]:
        warnings.append("Whole-cut recipe has liquids; review whether they are true ingredients or process aids")
        recipe["audit"].setdefault("status_reason", []).append("NOTE: whole-cut liquids/aromatics retained for source JSON; renderer must avoid kobasičarski liquid block")

    if suspicious_quantity_unit:
        ingredient_missing.extend(f"suspicious_quantity_unit:{item}" for item in suspicious_quantity_unit)
        warnings.append("Suspicious quantity unit found in critical ingredient group")

    status_reason: list[str] = []
    if forbidden:
        status_reason.append("FAIL: forbidden values in critical ingredient groups")
    if suspicious_quantity_unit:
        status_reason.append("FAIL: suspicious quantity unit in critical ingredient group")
    if recipe["recipe_id"] != "HR-SL-001" and (not recipe["materials"] or "source.authority_block" in ingredient_missing or "ingredients" in ingredient_missing):
        status_reason.append("FAIL: authority block/ingredient table missing or materials_count=0")

    if forbidden or suspicious_quantity_unit:
        ingredient_status = "FAIL"
    elif recipe["recipe_id"] != "HR-SL-001" and (not recipe["materials"] or "source.authority_block" in ingredient_missing or "ingredients" in ingredient_missing):
        ingredient_status = "FAIL"
    elif ingredient_missing:
        status_reason.append("REVIEW: ingredient missing fields require review")
        ingredient_status = "REVIEW"
    else:
        status_reason.append("PASS: ingredients parsed with no critical forbidden, missing, or suspicious quantity units")
        ingredient_status = "PASS"
    if recipe["recipe_id"] == "HR-SL-001":
        expected_titles = [title for title, _keywords in PROCESS_PHASES]
        actual_titles = [phase.get("title") for phase in recipe.get("process", [])]
        if actual_titles != expected_titles:
            process_fail.append("process.phase_set")
            warnings.append("Process phase set/order does not match required HR-SL-001 phases")

    process_status = "FAIL" if process_fail or mapping_errors or contamination_errors else ("REVIEW" if process_missing else "PASS")
    if forbidden or ingredient_status == "FAIL" or process_status == "FAIL" or contamination_errors:
        overall_status = "FAIL"
    elif ingredient_status == "REVIEW" or process_status == "REVIEW":
        overall_status = "REVIEW"
    else:
        overall_status = "PASS"

    missing_fields = list(dict.fromkeys(ingredient_missing + process_fail + process_missing))
    recipe["audit"].update(
        {
            "status": overall_status,
            "ingredient_status": ingredient_status,
            "process_status": process_status,
            "overall_status": overall_status,
            "warnings": warnings,
            "missing_fields": missing_fields,
            "ingredient_missing_fields": ingredient_missing,
            "process_missing_fields": process_missing,
            "process_fail_fields": process_fail,
            "process_mapping_errors": mapping_errors,
            "process_contamination_errors": contamination_errors,
            "not_specified_in_source": not_specified,
            "skipped_table_header_rows": skipped_headers,
            "moved_to_casings": moved_to_casings,
            "moved_to_equipment": moved_to_equipment,
            "reclassified_to_materials": reclassified_to_materials,
            "reclassified_to_spices": reclassified_to_spices,
            "reclassified_to_liquids": reclassified_to_liquids,
            "reclassified_to_casings": reclassified_to_casings,
            "reclassified_to_equipment": reclassified_to_equipment,
            "reclassified_to_garlic_or_liquids": reclassified_to_garlic_or_liquids,
            "quantity_from_column": quantity_from_column_rows,
            "note_dimensions_detected": note_dimensions_detected,
            "suspicious_quantity_unit": suspicious_quantity_unit,
            "status_reason": list(dict.fromkeys(recipe["audit"].get("status_reason", []) + status_reason)),
            "empty_quantity_by_group": {
                group: list(dict.fromkeys(values))
                for group, values in recipe["audit"].get("empty_quantity_by_group", {}).items()
            },
            "missing_required_values": missing_required,
            "forbidden_old_values_found": forbidden,
        }
    )
    recipe["source_status"] = "locked" if ingredient_status == "PASS" and process_status == "PASS" else "review"


def fail_recipe(recipe_id: str, recipe_map: dict[str, Any], message: str, source_heading: str | None = None) -> dict[str, Any]:
    recipe = base_recipe(recipe_id, recipe_map, source_heading)
    recipe["audit"]["warnings"].append(message)
    recipe["audit"]["ingredient_missing_fields"].append("source.file")
    recipe["audit"]["process_missing_fields"].append("source.file")
    audit_recipe(recipe)
    recipe["audit"]["ingredient_status"] = "FAIL"
    recipe["audit"]["process_status"] = "FAIL"
    recipe["audit"]["overall_status"] = "FAIL"
    recipe["audit"]["status"] = "FAIL"
    return recipe


def build_recipe(root: Path, recipe_id: str, recipe_maps: dict[str, dict[str, Any]], source: SourceDocument | None, manifest: dict[str, Any], load_warnings: list[str]) -> dict[str, Any]:
    recipe_map = recipe_maps.get(recipe_id, {})
    if not recipe_map:
        return fail_recipe(recipe_id, {}, f"Recipe ID {recipe_id} is missing from recipe_id_map.yml", None)
    source_heading = recipe_map.get("source_heading")
    if recipe_map.get("heading_status") == "REVIEW":
        recipe = fail_recipe(recipe_id, recipe_map, "Recipe heading requires review; no safe authority heading selected.", str(source_heading) if source_heading else None)
        recipe["audit"]["overall_status"] = "FAIL"
        recipe["audit"]["status"] = "FAIL"
        return recipe
    if source is None:
        recipe = fail_recipe(recipe_id, recipe_map, "Authoritative source file TOM2_HR_SOURCE_LOCK_MASTER.md is missing.", str(source_heading) if source_heading else None)
        recipe["audit"]["warnings"] = list(dict.fromkeys(load_warnings + recipe["audit"]["warnings"]))
        recipe["metadata"]["authority_mode"] = manifest.get("authority_mode", "strict")
        return recipe

    resolved_heading = find_heading_by_map(source.text, recipe_map)
    heading, authority_block = extract_authority_block(source.text, resolved_heading) if resolved_heading else (None, None)
    recipe = base_recipe(recipe_id, recipe_map, heading or resolved_heading)
    recipe["source"]["file"] = str(source.path)
    recipe["source"]["sha256"] = source.sha256
    recipe["metadata"]["authority_mode"] = manifest.get("authority_mode", "strict")
    recipe["audit"]["legacy_conflict_block_found_outside_authority"] = "YES" if legacy_conflict_outside_authority(source.text, authority_block) else "NO"

    if authority_block is None:
        recipe["audit"]["warnings"].append(f"Authority block heading not found: {source_heading}")
        recipe["audit"]["ingredient_missing_fields"].append("source.authority_block")
        recipe["audit"]["process_missing_fields"].append("source.authority_block")
        audit_recipe(recipe)
        recipe["audit"]["overall_status"] = "FAIL"
        recipe["audit"]["status"] = "FAIL"
        return recipe

    if recipe_id == "HR-SL-002" and recipe_map.get("inherits_from") == "HR-SL-001":
        parent = build_recipe(root, "HR-SL-001", recipe_maps, source, manifest, load_warnings)
        if parent["audit"]["overall_status"] == "FAIL":
            recipe["audit"]["warnings"].append("Cannot inherit HR-SL-002 because HR-SL-001 parent did not compile cleanly")
            recipe["audit"]["ingredient_missing_fields"].append("inheritance.HR-SL-001")
            recipe["audit"]["process_missing_fields"].append("inheritance.HR-SL-001")
        else:
            apply_hr002_inheritance(recipe, parent, authority_block)
        extract_controls_and_storage(authority_block, recipe)
        audit_recipe(recipe)
        return recipe

    extract_items(authority_block, recipe)
    extract_process(authority_block, recipe)
    extract_controls_and_storage(authority_block, recipe)
    audit_recipe(recipe)
    return recipe


def write_json(path: Path, data: dict[str, Any]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(data, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def write_audit(path: Path, recipe: dict[str, Any]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    audit = recipe["audit"]
    lines = [
        f"Recipe: {recipe['recipe_id']} {recipe['title']}",
        f"Source file: {recipe['source']['file']}",
        f"Source hash: {recipe['source']['sha256']}",
        f"Authority block heading: {recipe['metadata'].get('authority_block_heading')}",
        f"ingredient_status: {audit['ingredient_status']}",
        f"process_status: {audit['process_status']}",
        f"overall_status: {audit['overall_status']}",
        f"inherited_from: {audit.get('inherited_from')}",
        f"inherited_fields: {', '.join(audit.get('inherited_fields', []))}",
        f"override_fields: {', '.join(audit.get('override_fields', []))}",
        f"LEGACY_CONFLICT_BLOCK_FOUND_OUTSIDE_AUTHORITY={audit.get('legacy_conflict_block_found_outside_authority', 'NO')}",
        f"Warnings: {len(audit['warnings'])}",
        f"Missing fields: {len(audit['missing_fields'])}",
        f"Process contamination errors: {len(audit.get('process_contamination_errors', []))}",
        f"Not specified in source: {len(audit['not_specified_in_source'])}",
        f"Missing required values: {len(audit['missing_required_values'])}",
        f"Forbidden old values: {len(audit['forbidden_old_values_found'])}",
        f"Skipped table header rows: {len(audit.get('skipped_table_header_rows', []))}",
        f"Moved to casings: {len(audit.get('moved_to_casings', []))}",
        f"Moved to equipment: {len(audit.get('moved_to_equipment', []))}",
        f"Reclassified to materials: {len(audit.get('reclassified_to_materials', []))}",
        f"Reclassified to spices: {len(audit.get('reclassified_to_spices', []))}",
        f"Reclassified to liquids: {len(audit.get('reclassified_to_liquids', []))}",
        f"Reclassified to casings: {len(audit.get('reclassified_to_casings', []))}",
        f"Reclassified to equipment: {len(audit.get('reclassified_to_equipment', []))}",
        f"Reclassified to garlic/liquids: {len(audit.get('reclassified_to_garlic_or_liquids', []))}",
        f"Quantity from column: {len(audit.get('quantity_from_column', []))}",
        f"Note dimensions detected: {len(audit.get('note_dimensions_detected', []))}",
        f"Suspicious quantity unit: {len(audit.get('suspicious_quantity_unit', []))}",
        "",
        "Warnings:",
    ]
    lines.extend(f"- {item}" for item in audit["warnings"])
    lines.extend(["", "Ingredient missing fields:"])
    lines.extend(f"- {item}" for item in audit["ingredient_missing_fields"])
    lines.extend(["", "Process fail fields:"])
    lines.extend(f"- {item}" for item in audit["process_fail_fields"])
    lines.extend(["", "Process mapping errors:"])
    lines.extend(f"- {item}" for item in audit["process_mapping_errors"])
    lines.extend(["", "Process contamination errors:"])
    lines.extend(f"- {item}" for item in audit.get("process_contamination_errors", []))
    lines.extend(["", "Process missing fields:"])
    lines.extend(f"- {item}" for item in audit["process_missing_fields"])
    lines.extend(["", "Not specified in source:"])
    lines.extend(f"- {item}" for item in audit["not_specified_in_source"])
    lines.extend(["", "Missing required values:"])
    lines.extend(f"- {item}" for item in audit["missing_required_values"])
    lines.extend(["", "Forbidden old values found:"])
    lines.extend(f"- {item}" for item in audit["forbidden_old_values_found"])
    lines.extend(["", "Skipped table header rows:"])
    lines.extend(f"- {item}" for item in audit.get("skipped_table_header_rows", []))
    lines.extend(["", "Moved to casings:"])
    lines.extend(f"- {item}" for item in audit.get("moved_to_casings", []))
    lines.extend(["", "Moved to equipment:"])
    lines.extend(f"- {item}" for item in audit.get("moved_to_equipment", []))
    lines.extend(["", "Reclassified to materials:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_materials", []))
    lines.extend(["", "Reclassified to spices:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_spices", []))
    lines.extend(["", "Reclassified to liquids:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_liquids", []))
    lines.extend(["", "Reclassified to casings:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_casings", []))
    lines.extend(["", "Reclassified to equipment:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_equipment", []))
    lines.extend(["", "Reclassified to garlic/liquids:"])
    lines.extend(f"- {item}" for item in audit.get("reclassified_to_garlic_or_liquids", []))
    lines.extend(["", "Quantity from column:"])
    lines.extend(f"- {item}" for item in audit.get("quantity_from_column", []))
    lines.extend(["", "Note dimensions detected:"])
    lines.extend(f"- {item}" for item in audit.get("note_dimensions_detected", []))
    lines.extend(["", "Suspicious quantity unit:"])
    lines.extend(f"- {item}" for item in audit.get("suspicious_quantity_unit", []))
    lines.extend(["", "Status reason:"])
    lines.extend(f"- {item}" for item in audit.get("status_reason", []))
    lines.extend(["", "Empty quantity by group:"])
    for group, values in audit.get("empty_quantity_by_group", {}).items():
        lines.append(f"{group}: {len(values)}")
        lines.extend(f"- {item}" for item in values)
    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def manifest_row(recipe: dict[str, Any], json_path: Path) -> dict[str, Any]:
    audit = recipe["audit"]
    return {
        "recipe_id": recipe["recipe_id"],
        "title": recipe["title"],
        "source_file": recipe["source"]["file"],
        "source_hash": recipe["source"]["sha256"],
        "json_file": str(json_path),
        "ingredient_status": audit["ingredient_status"],
        "process_status": audit["process_status"],
        "overall_status": audit["overall_status"],
        "warnings_count": len(audit["warnings"]),
        "missing_fields_count": len(audit["missing_fields"]),
        "forbidden_old_values_count": len(audit["forbidden_old_values_found"]),
    }


def write_manifest(path: Path, rows: list[dict[str, Any]]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    fieldnames = [
        "recipe_id",
        "title",
        "source_file",
        "source_hash",
        "json_file",
        "ingredient_status",
        "process_status",
        "overall_status",
        "warnings_count",
        "missing_fields_count",
        "forbidden_old_values_count",
    ]
    with path.open("w", newline="", encoding="utf-8") as handle:
        writer = csv.DictWriter(handle, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(rows)


def print_report(recipes: list[dict[str, Any]]) -> None:
    columns = [
        "recipe_id",
        "title",
        "ingredient_status",
        "process_status",
        "overall_status",
        "materials_count",
        "spices_count",
        "liquids_count",
        "casings_count",
        "equipment_count",
        "process_count",
        "forbidden_count",
        "missing_required_count",
    ]
    print(",".join(columns))
    for recipe in recipes:
        audit = recipe["audit"]
        row = {
            "recipe_id": recipe["recipe_id"],
            "title": recipe["title"],
            "ingredient_status": audit["ingredient_status"],
            "process_status": audit["process_status"],
            "overall_status": audit["overall_status"],
            "materials_count": len(recipe["materials"]),
            "spices_count": len(recipe["spices"]),
            "liquids_count": len(recipe["liquids"]),
            "casings_count": len(recipe["casings"]),
            "equipment_count": len(recipe["equipment"]),
            "process_count": len(recipe["process"]),
            "forbidden_count": len(audit["forbidden_old_values_found"]),
            "missing_required_count": len(audit["missing_required_values"]),
        }
        print(",".join(str(row[column]) for column in columns))


def parse_args(argv: list[str]) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Compile source-locked Drycured recipe JSON.")
    parser.add_argument("--recipe", help="Recipe ID to compile.")
    parser.add_argument("--pilot10", action="store_true", help="Compile the 10-recipe source-lock pilot.")
    parser.add_argument("--discover-headings", action="store_true", help="Discover safe authority headings in the strict TOM2 master before compiling.")
    parser.add_argument("--root", default=None, help="Project root. Defaults to /root/DRYCURED_GITHUB if present, otherwise ./DRYCURED_GITHUB.")
    parser.add_argument("--dry-run", action="store_true", help="Do not touch WordPress. Build JSON and audit artifacts only.")
    return parser.parse_args(argv)


def compile_recipes(root: Path, recipe_ids: list[str]) -> list[dict[str, Any]]:
    ensure_dirs(root)
    manifest = parse_simple_yml(root / "source_recipes" / "hr" / "source_priority_manifest.yml")
    recipe_maps = parse_recipe_id_map(root / "tools" / "source_lock_compiler" / "recipe_id_map.yml")
    load_warnings: list[str] = []
    source = load_primary_source(root, manifest, load_warnings)

    recipes = []
    manifest_rows = []
    for recipe_id in recipe_ids:
        recipe = build_recipe(root, recipe_id, recipe_maps, source, manifest, load_warnings)
        json_path = root / "build" / "source_locked_json" / f"{recipe_id}.source_locked.json"
        audit_path = root / "build" / "source_lock_audit" / f"{recipe_id}.source_lock_audit.txt"
        write_json(json_path, recipe)
        write_audit(audit_path, recipe)
        recipes.append(recipe)
        manifest_rows.append(manifest_row(recipe, json_path))
    write_manifest(root / "build" / "source_lock_audit" / "source_lock_manifest.csv", manifest_rows)
    return recipes


def main(argv: list[str]) -> int:
    args = parse_args(argv)
    if not args.pilot10 and not args.recipe:
        raise SystemExit("Pass --recipe RECIPE_ID or --pilot10")
    root = Path(args.root).resolve() if args.root else project_root_from(Path.cwd()).resolve()
    recipe_ids = PILOT10_IDS if args.pilot10 else [str(args.recipe)]

    discovery = None
    if args.discover_headings:
        discovery = discover_for_pilot(root, recipe_ids)

    recipes = compile_recipes(root, recipe_ids)
    print("DRYCURED SOURCE LOCK COMPILER v1.9E")
    print(f"dry_run: {bool(args.dry_run)}")
    print(f"project_root: {root}")
    if discovery:
        print(f"recipe_headings_found: {discovery['headings_count']}")
        for recipe_id in recipe_ids:
            selected = discovery["selected"].get(recipe_id, "REVIEW_HEADING")
            print(f"heading_selection: {recipe_id} | {selected}")
        print(f"heading_candidates_path: {discovery['candidates_path']}")
    print_report(recipes)
    if any(recipe["audit"]["overall_status"] != "PASS" for recipe in recipes):
        print("wordpress_update_allowed: no")
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))
