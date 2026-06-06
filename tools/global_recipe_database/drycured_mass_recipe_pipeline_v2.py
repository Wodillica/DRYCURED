#!/usr/bin/env python3
"""DRYCURED MASS RECIPE PIPELINE v2.0.2.

DRY_RUN prepares batch reports only. It does not connect to WordPress and does
not perform writes. EXECUTE_PRIVATE is intentionally delegated to the WP-CLI PHP
script after human review of the dry-run reports.
"""

from __future__ import annotations

import argparse
import csv
import json
import re
import subprocess
import sys
import unicodedata
from dataclasses import dataclass
from pathlib import Path
from typing import Any


PIPELINE_VERSION = "mass_pipeline_v2_0_2"
FALSE_VALUES = {"", "ne", "no", "false", "0", "not_specified_in_source"}
BLOCKING_TRIAGE_PREFIXES = ("OUT_OF_SCOPE", "REJECTED", "FANTASY")
REVIEW_VALUES = {"not_specified_in_source", "needs_editorial_review", "needs_external_validation", "category_only_record", "blocked", ""}
SERVER_AWARE_REPORT_ROOT = "server-reports/recipes/mass-pipeline-v2/batch_001_server_aware_dry_run"
LOCAL_SERVER_AWARE_REPORT_ROOT = "_local_mass_pipeline_reports/batch_001_server_aware_dry_run"
SERVER_LIVE_REPORT_ROOT = "server-reports/recipes/mass-pipeline-v2/batch_001_server_live_dry_run"
CORE_REQUIRED_TAXONOMIES = [
    "dry_country",
    "dry_region",
    "dry_product_category",
    "dry_meat_type",
    "dry_process_type",
]
OPTIONAL_TAXONOMIES = [
    "dry_microregion",
    "dry_product_type",
    "dry_preparation_method",
    "dry_difficulty",
    "dry_recipe_status",
]
TAXONOMIES = [
    "dry_country",
    "dry_region",
    "dry_product_category",
    "dry_meat_type",
    "dry_process_type",
    "dry_microregion",
    "dry_product_type",
    "dry_preparation_method",
    "dry_difficulty",
    "dry_recipe_status",
]
HARD_BLOCK_IDS = {
    "MD-STAROHRVATSKIMORE_KOPNO_SALAMET_SALAMA_MARITIMA",
    "MD-KVARNERSKI_DIVOVSKI_SALAMET",
    "MD-ISTARSKI_MORSKI_PRSUT_NEPTUNOV_DAR",
    "MD-ETNOGRAFSKA_STUDIJA_KULINARSKOG_NASLIJEDA",
}
HARD_BLOCK_STATUS_TOKENS = {
    "REJECTED_OR_FANTASY",
    "REJECTED",
    "FANTASY_SOURCE",
    "NOT_RECIPE",
    "DOCUMENT_ONLY",
    "NON_RECIPE_ARTICLE",
}
CATEGORY_ONLY_TOKENS = {
    "CATEGORY_ONLY_RECORD",
    "PRIVATE_REFERENCE_ONLY",
    "NOT_FOR_RECIPE_RENDERER",
    "DOCUMENT_ONLY",
    "NON_RECIPE_ARTICLE",
}
WHOLE_PIECE_CATEGORY_ALIASES = {
    "Suho meso": ["Cijeli komad", "Cijeli komadi", "Cijelorezni i suhomesnati proizvodi"],
}


@dataclass
class BatchResult:
    input_records: int
    selected_for_batch: int
    would_create_private: int
    would_update_private: int
    blocked: int
    needs_review: int
    taxonomy_terms_existing: int
    taxonomy_terms_to_create: int
    missing_required_fields: int
    readiness_score_min: int
    readiness_score_max: int
    optional_taxonomies_empty: int = 0
    hard_blocked_count: int = 0
    category_only_count: int = 0


def project_root_from(start: Path) -> Path:
    if start.name == "DRYCURED_GITHUB":
        return start
    candidate = start / "DRYCURED_GITHUB"
    if candidate.exists():
        return candidate
    return start


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8-sig", errors="replace")


def load_config(root: Path) -> dict[str, Any]:
    path = root / "tools" / "global_recipe_database" / "drycured_mass_recipe_pipeline_config_v2.json"
    return json.loads(read_text(path))


def resolve_path(root: Path, configured: str) -> Path:
    path = Path(configured)
    if path.is_absolute():
        return path
    return root / path


def read_csv_rows(path: Path) -> list[dict[str, str]]:
    if not path.exists():
        return []
    with path.open("r", encoding="utf-8-sig", newline="") as handle:
        return [dict(row) for row in csv.DictReader(handle)]


def write_csv(path: Path, rows: list[dict[str, Any]], fieldnames: list[str]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    with path.open("w", encoding="utf-8", newline="") as handle:
        writer = csv.DictWriter(handle, fieldnames=fieldnames, extrasaction="ignore")
        writer.writeheader()
        for row in rows:
            writer.writerow({key: row.get(key, "") for key in fieldnames})


def repair_mojibake(text: str) -> str:
    replacements = {
        "Ä‡": "ć", "ÄĆ": "Ć", "ÄŤ": "č", "ÄŚ": "Č", "Ä‘": "đ", "Ä": "Đ",
        "Ĺˇ": "š", "Ĺ ": "Š", "Ĺľ": "ž", "Ĺ˝": "Ž", "Ă«": "ë", "Ă«": "ë",
        "Ă¤": "ä", "Ă©": "é", "Ă‰": "É", "Ă¨": "è", "Ă‚": "Â", "Ă´": "ô",
        "Ă¶": "ö", "Ă¼": "ü", "Ăś": "ö", "Ĺ": "Š",
    }
    repaired = str(text)
    for bad, good in replacements.items():
        repaired = repaired.replace(bad, good)
    return repaired


def slugify(text: str) -> str:
    text = repair_mojibake(text)
    text = text.translate(str.maketrans({"đ": "d", "Đ": "D", "č": "c", "Č": "C", "ć": "c", "Ć": "C", "š": "s", "Š": "S", "ž": "z", "Ž": "Z"}))
    normalized = unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode("ascii")
    slug = re.sub(r"[^a-zA-Z0-9]+", "-", normalized.lower()).strip("-")
    return slug or "not-specified"


def normalize_key(text: str) -> str:
    return slugify(text).replace("-", "")


def default_term_seed() -> dict[str, list[dict[str, Any]]]:
    seed = {
        "dry_country": ["Hrvatska", "Albanija", "Austrija", "Belgija", "Njemačka", "Švicarska", "Francuska", "Italija", "Španjolska", "Srbija", "Bosna i Hercegovina", "Crna Gora"],
        "dry_region": ["Slavonija", "Baranja", "Dalmacija", "Tirol", "Istra", "Lika", "Westfalen", "Bavarska", "Koruška"],
        "dry_product_category": ["Kobasica", "Salama", "Slanina", "Šunka", "Pršut", "Cijeli komad", "Suho meso", "Pašteta"],
        "dry_meat_type": ["Svinjetina", "Govedina", "Janjetina", "Divljač", "Perad", "Miješano meso"],
        "dry_process_type": ["Sušeno", "Dimljeno", "Fermentirano", "Soljeno", "Kuhano", "Bareno"],
        "dry_microregion": ["not_specified_in_source"],
        "dry_product_type": ["Kobasica", "Salama", "Slanina", "Šunka", "Pršut", "Suho meso"],
        "dry_preparation_method": ["Sušenje", "Dimljenje", "Fermentacija", "Soljenje", "Kuhanje"],
        "dry_difficulty": ["Početno", "Srednje", "Napredno"],
        "dry_recipe_status": ["Private batch draft", "Needs editorial review", "Needs external validation"],
    }
    return {
        taxonomy: [
            {"term_id": index + 1, "name": name, "slug": slugify(name), "count": 0, "source": "local_fallback_seed"}
            for index, name in enumerate(names)
        ]
        for taxonomy, names in seed.items()
    }


def wp_term_list(taxonomy: str, wp_path: str | None = None) -> list[dict[str, Any]]:
    command = ["wp", "term", "list", taxonomy, "--fields=term_id,name,slug,count", "--format=json", "--allow-root"]
    if wp_path:
        command.append(f"--path={wp_path}")
    try:
        completed = subprocess.run(command, check=False, capture_output=True, text=True, timeout=20)
    except (OSError, subprocess.SubprocessError):
        return []
    if completed.returncode != 0 or not completed.stdout.strip():
        return []
    try:
        parsed = json.loads(completed.stdout)
    except json.JSONDecodeError:
        return []
    return parsed if isinstance(parsed, list) else []


def load_wp_terms(require_live: bool = False, wp_path: str | None = None) -> tuple[dict[str, list[dict[str, Any]]], str, list[str]]:
    terms: dict[str, list[dict[str, Any]]] = {}
    used_wp = False
    optional_empty: list[str] = []
    for taxonomy in TAXONOMIES:
        rows = wp_term_list(taxonomy, wp_path)
        if rows:
            used_wp = True
            terms[taxonomy] = [{**row, "source": "wp_term_list"} for row in rows]
        elif require_live and taxonomy in CORE_REQUIRED_TAXONOMIES:
            raise SystemExit(f"SERVER_LIVE_TERM_AUDIT_FAILED: no live WP terms returned for {taxonomy}")
        elif require_live and taxonomy in OPTIONAL_TAXONOMIES:
            terms[taxonomy] = []
            optional_empty.append(taxonomy)
    if require_live and not used_wp:
        raise SystemExit("SERVER_LIVE_TERM_AUDIT_FAILED: WP-CLI returned no live terms.")
    fallback = default_term_seed()
    for taxonomy in TAXONOMIES:
        if taxonomy not in terms:
            terms[taxonomy] = fallback.get(taxonomy, [])
    return terms, "wp_term_list" if used_wp else "local_fallback_seed", optional_empty


def existing_term_lookup(wp_terms: dict[str, list[dict[str, Any]]]) -> dict[str, dict[str, dict[str, Any]]]:
    lookup: dict[str, dict[str, dict[str, Any]]] = {}
    for taxonomy, rows in wp_terms.items():
        lookup[taxonomy] = {}
        for row in rows:
            name = str(row.get("name") or "")
            slug = str(row.get("slug") or slugify(name))
            lookup[taxonomy][slugify(name)] = row
            lookup[taxonomy][slug] = row
            lookup[taxonomy][normalize_key(name)] = row
    return lookup


def resolve_existing_term(taxonomy: str, term_name: str, lookup: dict[str, dict[str, dict[str, Any]]]) -> dict[str, Any] | None:
    if term_name in REVIEW_VALUES:
        return None
    keys = [slugify(term_name), normalize_key(term_name)]
    for key in keys:
        if key in lookup.get(taxonomy, {}):
            return lookup[taxonomy][key]
    return None


def resolve_existing_term_with_alias(taxonomy: str, term_name: str, lookup: dict[str, dict[str, dict[str, Any]]]) -> tuple[dict[str, Any] | None, str, str]:
    existing = resolve_existing_term(taxonomy, term_name, lookup)
    if existing:
        return existing, term_name, "existing WP term"
    if taxonomy == "dry_product_category":
        for alias in WHOLE_PIECE_CATEGORY_ALIASES.get(term_name, []):
            alias_existing = resolve_existing_term(taxonomy, alias, lookup)
            if alias_existing:
                alias_name = str(alias_existing.get("name") or alias)
                note = f"mapped_from={term_name}; existing broader whole-piece category used; no new Suho meso term"
                return alias_existing, alias_name, note
    return None, term_name, "review before creating clean term"


def clean_term(value: str, fallback: str = "not_specified_in_source") -> str:
    text = repair_mojibake(value or "").strip()
    if text.lower() in FALSE_VALUES:
        return fallback
    text = re.split(r"[;\n\r]", text, maxsplit=1)[0].strip()
    return text[:80]


def title_contains(text: str, *needles: str) -> bool:
    lowered = unicodedata.normalize("NFKD", text).encode("ascii", "ignore").decode("ascii").lower()
    return any(needle in lowered for needle in needles)


def classify_category(product_type: str, title: str, markdown: str) -> str:
    combined = f"{product_type} {title} {markdown}"
    if title_contains(combined, "kobasica", "suxhuk", "wurstel", "sausage"):
        return "Kobasica"
    if title_contains(combined, "salama", "salami"):
        return "Salama"
    if title_contains(combined, "slanina", "speck", "bacon"):
        return "Slanina"
    if title_contains(combined, "sunka", "prsut", "proshuta", "ham"):
        return "Šunka"
    if title_contains(combined, "sir", "cheese", "kackavall"):
        return "needs_editorial_review"
    if title_contains(combined, "meso", "janjetina", "goved"):
        return "Suho meso"
    return "needs_editorial_review"


def classify_meat(title: str, markdown: str) -> str:
    combined = f"{title} {markdown}"
    if title_contains(combined, "svinj", "pork", "schwein", "speck", "slanina", "prsut", "sunka"):
        return "Svinjetina"
    if title_contains(combined, "goved", "beef"):
        return "Govedina"
    if title_contains(combined, "janjet", "lamb", "ovc"):
        return "Janjetina"
    if title_contains(combined, "sir", "cheese"):
        return "needs_editorial_review"
    return "not_specified_in_source"


def classify_process(markdown: str) -> str:
    terms = []
    if title_contains(markdown, "dim", "smoke", "rauch"):
        terms.append("Dimljeno")
    if title_contains(markdown, "sus", "suh", "dry", "zren", "sazrij"):
        terms.append("Sušeno")
    if title_contains(markdown, "ferment"):
        terms.append("Fermentirano")
    if title_contains(markdown, "sol", "salt"):
        terms.append("Soljeno")
    if title_contains(markdown, "kuh", "bar", "cook"):
        terms.append("Kuhano")
    return "|".join(dict.fromkeys(terms)) if terms else "not_specified_in_source"


def has_yes(value: str) -> bool:
    return str(value or "").strip().lower() in {"da", "yes", "true", "1"}


def hard_block_reason(source_id: str, triage: str, safety: str, title: str, category: str) -> str:
    combined = f"{source_id} {triage} {safety} {title} {category}".upper()
    if source_id in HARD_BLOCK_IDS:
        return "hard_block_id_minimal_v2_0_2"
    if "HIGH_RISK_SPECIAL_PROCESS" in combined and "SPECIAL_TRADITIONAL_PROCESS_RECORD" not in combined:
        return "high_risk_special_process_without_special_traditional_process_record"
    for token in HARD_BLOCK_STATUS_TOKENS:
        if token in combined:
            return token.lower()
    if any(triage.upper().startswith(prefix) for prefix in BLOCKING_TRIAGE_PREFIXES):
        return "blocking_triage_prefix"
    if "REJECTED_OR_FANTASY" in combined:
        return "triage_rejected_or_fantasy"
    if category == "needs_editorial_review" and title_contains(title, "sir", "cheese", "kackavall"):
        return "non_meat_or_out_of_scope_food_record"
    return ""


def is_category_only_record(source_id: str, triage: str, safety: str, title: str, category: str) -> bool:
    combined = f"{source_id} {triage} {safety} {title} {category}".upper()
    document_markers = {"STUDIJA", "NASLIJEDA", "NASLJEDA", "TRADICIJSKI_ZAPIS", "KATEGORIJSKI_ZAPIS"}
    if any(token in combined for token in document_markers):
        return True
    if "REJECTED_OR_FANTASY" in combined:
        return False
    return any(token in combined for token in CATEGORY_ONLY_TOKENS)


def is_blocked_record(triage: str, safety: str, title: str, category: str) -> bool:
    return bool(hard_block_reason("", triage, safety, title, category))


def derive_candidate(row: dict[str, str], config: dict[str, Any], root: Path) -> dict[str, Any]:
    excerpt_dir = resolve_path(root, config["source_paths"]["source_excerpt_dir"])
    number = int(row.get("record_number") or row.get("num") or 0)
    excerpt = load_excerpt(excerpt_dir, number)
    source_id = row.get("source_id") or excerpt.get("recipe_id") or f"RECIPE-{number:03d}"
    title = repair_mojibake(row.get("title") or excerpt.get("title") or source_id)
    markdown = str(excerpt.get("full_markdown") or "")
    country = clean_term(row.get("transfer_proposed_country") or row.get("country_from_source") or row.get("country") or "")
    region = clean_term(row.get("region_from_source") or row.get("transfer_region") or "")
    product_type = clean_term(row.get("product_type_from_source") or row.get("type") or "")
    category = classify_category(product_type, title, markdown)
    meat_type = classify_meat(title, markdown)
    process_type = classify_process(markdown)
    triage = row.get("transfer_triage_status") or ""
    safety = row.get("transfer_safety_status") or ""
    has_ingredients = has_yes(row.get("has_ingredients") or "") or bool(excerpt.get("public_recipe", {}).get("ingredients"))
    has_process = has_yes(row.get("has_process") or "") or bool(markdown and title_contains(markdown, "postupak", "korak", "sus", "dim", "sol"))
    hard_reason = hard_block_reason(source_id, triage, safety, title, category)
    category_only = is_category_only_record(source_id, triage, safety, title, category)
    blocked = bool(hard_reason) or category_only
    missing_fields = []
    missing_map = {
        "dry_country": country,
        "dry_region": region,
        "dry_product_category": category,
        "dry_meat_type": meat_type,
        "dry_process_type": process_type,
    }
    for key, value in missing_map.items():
        if value in REVIEW_VALUES:
            missing_fields.append(key)
    if not has_ingredients:
        missing_fields.append("ingredients")
    if not has_process:
        missing_fields.append("process")
    score = 0
    if category not in REVIEW_VALUES:
        score += 20
    if country not in REVIEW_VALUES:
        score += 15
    if region not in REVIEW_VALUES:
        score += 10
    if meat_type not in REVIEW_VALUES:
        score += 15
    if process_type not in REVIEW_VALUES:
        score += 15
    if has_ingredients:
        score += 10
    else:
        score -= 30
    if has_process:
        score += 10
    else:
        score -= 30
    if not blocked:
        score += 20
    else:
        score -= 100
    if hard_reason:
        score -= 500
    if category_only:
        score -= 250
    if "NEEDS_EXTERNAL_VALIDATION" in triage or "not_attached" in (row.get("transfer_public_source_hint") or ""):
        score -= 20
    audit_reason_parts = [part for part in [triage, safety, row.get("notes") or row.get("transfer_notes")] if part]
    audit_reason = "; ".join(audit_reason_parts) or "needs_editorial_review"
    suggested_fix = "none"
    status = "ready_for_private_dry_run"
    if missing_fields:
        status = "needs_editorial_review"
        if category not in REVIEW_VALUES or meat_type not in REVIEW_VALUES or process_type not in REVIEW_VALUES:
            suggested_fix = "derived_from_confirmed_category"
        elif "NEEDS_EXTERNAL_VALIDATION" in triage:
            suggested_fix = "needs_external_validation"
        else:
            suggested_fix = "needs_editorial_review"
    if blocked:
        status = "blocked"
        suggested_fix = "blocked"
    if category_only:
        status = "category_only_record"
        suggested_fix = "private_reference_only_not_for_recipe_renderer"
    return {
        **row,
        "_record_number": number,
        "_excerpt": excerpt,
        "_source_id": source_id,
        "_title": title,
        "_markdown": markdown,
        "_country": country,
        "_region": region,
        "_product_type": product_type,
        "_category": category,
        "_meat_type": meat_type,
        "_process_type": process_type,
        "_triage": triage,
        "_safety": safety,
        "_audit_reason": audit_reason,
        "_has_ingredients": has_ingredients,
        "_has_process": has_process,
        "_blocked": blocked,
        "_hard_blocked": bool(hard_reason),
        "_hard_block_reason": hard_reason,
        "_category_only": category_only,
        "_category_only_status": "category_only_record|private_reference_only|not_for_recipe_renderer" if category_only else "",
        "_missing_fields": missing_fields,
        "_readiness_score": score,
        "_suggested_fix": suggested_fix,
        "_status": status,
    }


def select_server_aware_batch(records: list[dict[str, str]], config: dict[str, Any], root: Path, batch_number: int) -> list[dict[str, Any]]:
    candidates = [derive_candidate(row, config, root) for row in records]
    candidates.sort(key=lambda row: (int(row["_readiness_score"]), -len(row["_missing_fields"]), -int(row["_record_number"])), reverse=True)
    batch_size = int(config.get("batch_size", 50))
    start = (batch_number - 1) * batch_size
    eligible = [row for row in candidates if not row["_blocked"] and not row["_hard_blocked"] and not row["_category_only"]]
    selected = eligible[start : start + batch_size]
    if len(selected) < batch_size:
        selected.extend([row for row in candidates if row["_blocked"] and not row["_hard_blocked"] and not row["_category_only"]][: batch_size - len(selected)])
    return selected


def load_excerpt(source_excerpt_dir: Path, number: int) -> dict[str, Any]:
    path = source_excerpt_dir / f"R{number:03d}_source_excerpt.json"
    if not path.exists():
        return {}
    try:
        return json.loads(read_text(path))
    except json.JSONDecodeError:
        return {}


def merge_records(index_rows: list[dict[str, str]], transfer_rows: list[dict[str, str]]) -> list[dict[str, str]]:
    transfer_by_source = {row.get("source_id", ""): row for row in transfer_rows}
    merged = []
    for row in index_rows:
        source_id = row.get("source_id", "")
        merged_row = dict(row)
        if source_id in transfer_by_source:
            for key, value in transfer_by_source[source_id].items():
                merged_row[f"transfer_{key}"] = value
        merged.append(merged_row)
    return merged


def build_batch_rows(records: list[dict[str, str]], config: dict[str, Any], root: Path, batch_number: int) -> tuple[list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]]]:
    batch_size = int(config.get("batch_size", 50))
    start = (batch_number - 1) * batch_size
    selected = records[start : start + batch_size]
    excerpt_dir = resolve_path(root, config["source_paths"]["source_excerpt_dir"])
    hard_defaults = config.get("hard_safety_defaults", {})
    table_rows: list[dict[str, Any]] = []
    taxonomy_rows: list[dict[str, Any]] = []
    meta_rows: list[dict[str, Any]] = []
    review_rows: list[dict[str, Any]] = []
    blocked_rows: list[dict[str, Any]] = []

    for offset, row in enumerate(selected, start=1):
        number = int(row.get("record_number") or row.get("num") or (start + offset))
        excerpt = load_excerpt(excerpt_dir, number)
        source_id = row.get("source_id") or excerpt.get("recipe_id") or f"BATCH001-{number:03d}"
        title = repair_mojibake(row.get("title") or excerpt.get("title") or source_id)
        markdown = str(excerpt.get("full_markdown") or "")
        country = clean_term(row.get("transfer_proposed_country") or row.get("country_from_source") or row.get("country") or "")
        region = clean_term(row.get("region_from_source") or row.get("transfer_region") or "")
        product_type = clean_term(row.get("product_type_from_source") or row.get("type") or "")
        category = classify_category(product_type, title, markdown)
        meat_type = classify_meat(title, markdown)
        process_type = classify_process(markdown)
        triage = row.get("transfer_triage_status") or ""
        safety = row.get("transfer_safety_status") or ""
        audit_status = "REVIEW" if triage or safety else "REVIEW"
        audit_reason_parts = [part for part in [triage, safety, row.get("notes") or row.get("transfer_notes")] if part]
        audit_reason = "; ".join(audit_reason_parts) or "needs_editorial_review"
        missing_fields = []
        for key, value in {
            "dry_country": country,
            "dry_region": region,
            "dry_product_category": category,
            "dry_meat_type": meat_type,
            "dry_process_type": process_type,
        }.items():
            if value in {"not_specified_in_source", "needs_editorial_review", ""}:
                missing_fields.append(key)
        blocked = any(triage.startswith(prefix) for prefix in BLOCKING_TRIAGE_PREFIXES) or category == "needs_editorial_review" and title_contains(title, "sir", "cheese", "kackavall")
        action = "BLOCKED_REVIEW" if blocked else "WOULD_CREATE_PRIVATE"
        needs_review = bool(missing_fields or audit_status != "PASS")

        recipe_code = source_id
        slug = slugify(f"{number:04d}-{title}")
        sections = {
            "full_markdown": markdown,
            "source_excerpt_file": f"R{number:03d}_source_excerpt.json",
        }
        meta_values = {
            "_dry_recipe_id": recipe_code,
            "dry_recipe_code": recipe_code,
            "recipe_id": recipe_code,
            "_dry_recipe_full_markdown": markdown,
            "_dry_recipe_sections": json.dumps(sections, ensure_ascii=False),
            "_dry_country": country,
            "_dry_region": region,
            "_dry_microregion": "not_specified_in_source",
            "_dry_product_type": product_type,
            "_dry_category": category,
            "_dry_source": str(excerpt.get("source_file") or "09_ALL_485_RAW_MASTER_DOCUMENT"),
            "_dry_recipe_source_audit_status": audit_status,
            "_dry_recipe_source_audit_reason": audit_reason,
            **hard_defaults,
        }

        table_row = {
            "batch": f"batch_{batch_number:03d}",
            "record_number": number,
            "recipe_id": recipe_code,
            "title": title,
            "slug": slug,
            "dry_run_action": action,
            "would_post_status": "private" if not blocked else "",
            "would_update_private": "no",
            "needs_review": "yes" if needs_review else "no",
            "blocked_reason": "out_of_scope_or_non_standard_review" if blocked else "",
            "missing_required_fields": "|".join(missing_fields),
            "source_audit_status": audit_status,
            "source_audit_reason": audit_reason,
            "public_publish_allowed": "false",
        }
        table_rows.append(table_row)
        if blocked:
            blocked_rows.append(table_row)
        if needs_review:
            review_rows.append(table_row)

        taxonomy_values = {
            "dry_country": country,
            "dry_region": region,
            "dry_product_category": category,
            "dry_meat_type": meat_type,
            "dry_process_type": process_type,
        }
        for taxonomy, term_value in taxonomy_values.items():
            terms = [item.strip() for item in str(term_value).split("|") if item.strip()] if taxonomy == "dry_process_type" else [term_value]
            for term in terms:
                taxonomy_rows.append({
                    "recipe_id": recipe_code,
                    "taxonomy": taxonomy,
                    "term_name": term,
                    "term_slug": slugify(term),
                    "term_status": "needs_wp_term_audit",
                    "notes": "Use existing slug if term already exists; create clean term only if absent.",
                })
        for meta_key, meta_value in meta_values.items():
            meta_rows.append({
                "recipe_id": recipe_code,
                "meta_key": meta_key,
                "meta_value_preview": str(meta_value).replace("\n", "\\n")[:500],
                "write_mode": "dry_run_only",
            })
    return table_rows, taxonomy_rows, meta_rows, review_rows, blocked_rows


def write_reports(root: Path, config: dict[str, Any], batch_number: int, records: list[dict[str, str]], table_rows: list[dict[str, Any]], taxonomy_rows: list[dict[str, Any]], meta_rows: list[dict[str, Any]], review_rows: list[dict[str, Any]], blocked_rows: list[dict[str, Any]], report_root_override: str | None = None) -> BatchResult:
    report_root = report_root_override or config.get("report_root", "server-reports/recipes/mass-pipeline-v2")
    report_dir = root / report_root / f"batch_{batch_number:03d}"
    report_dir.mkdir(parents=True, exist_ok=True)
    would_create = sum(1 for row in table_rows if row["dry_run_action"] == "WOULD_CREATE_PRIVATE")
    result = BatchResult(
        input_records=len(records),
        selected_for_batch=len(table_rows),
        would_create_private=would_create,
        would_update_private=0,
        blocked=len(blocked_rows),
        needs_review=len(review_rows),
        taxonomy_terms_existing=0,
        taxonomy_terms_to_create=len({(row["taxonomy"], row["term_slug"]) for row in taxonomy_rows if row["term_name"] not in {"not_specified_in_source", "needs_editorial_review", ""}}),
        missing_required_fields=sum(1 for row in table_rows if row["missing_required_fields"]),
    )

    table_fields = ["batch", "record_number", "recipe_id", "title", "slug", "dry_run_action", "would_post_status", "would_update_private", "needs_review", "blocked_reason", "missing_required_fields", "source_audit_status", "source_audit_reason", "public_publish_allowed"]
    write_csv(report_dir / "BATCH_001_DRY_RUN_TABLE.csv", table_rows, table_fields)
    write_csv(report_dir / "BATCH_001_TAXONOMY_MAPPING.csv", taxonomy_rows, ["recipe_id", "taxonomy", "term_name", "term_slug", "term_status", "notes"])
    write_csv(report_dir / "BATCH_001_META_MAPPING.csv", meta_rows, ["recipe_id", "meta_key", "meta_value_preview", "write_mode"])
    write_csv(report_dir / "BATCH_001_NEEDS_REVIEW.csv", review_rows, table_fields)
    write_csv(report_dir / "BATCH_001_REJECTED_OR_BLOCKED.csv", blocked_rows, table_fields)
    write_csv(report_dir / "BATCH_001_CREATED_POSTS.csv", [], ["recipe_id", "post_id", "post_title", "post_status", "notes"])
    write_csv(report_dir / "BATCH_001_PRIVATE_EXECUTE_TABLE.csv", [], ["recipe_id", "execute_action", "post_id", "meta_written", "terms_set", "notes"])

    summary = [
        "# DRYCURED MASS RECIPE PIPELINE v2.0 — BATCH 001 DRY_RUN",
        "",
        f"input_records: {result.input_records}",
        f"selected_for_batch: {result.selected_for_batch}",
        f"would_create_private: {result.would_create_private}",
        f"would_update_private: {result.would_update_private}",
        f"blocked: {result.blocked}",
        f"needs_review: {result.needs_review}",
        f"taxonomy_terms_existing: {result.taxonomy_terms_existing}",
        f"taxonomy_terms_to_create: {result.taxonomy_terms_to_create}",
        f"missing_required_fields: {result.missing_required_fields}",
        "public_publish_attempts: 0",
        "wordpress_write_performed: NO",
        "renderer_changed: NO",
        "shortcode_changed: NO",
        "",
        "Notes:",
        "- DRY_RUN only; no WordPress writes were attempted.",
        "- Taxonomy term status is needs_wp_term_audit until WP term list audit is run on the server.",
        "- All processed records keep private safety defaults and final human check flags.",
    ]
    (report_dir / "BATCH_001_DRY_RUN_SUMMARY.md").write_text("\n".join(summary) + "\n", encoding="utf-8")
    (report_dir / "BATCH_001_PRIVATE_EXECUTE_SUMMARY.md").write_text(
        "# BATCH 001 PRIVATE EXECUTE SUMMARY\n\nNOT_RUN. Execute private import is blocked until Davor/ChatGPT reviews DRY_RUN reports.\n",
        encoding="utf-8",
    )
    safety = [
        "DRYCURED MASS RECIPE PIPELINE v2.0 — BATCH 001 SAFETY CHECK",
        "mode=DRY_RUN",
        "wordpress_write_performed=NO",
        "public_publish_attempts=0",
        "new_posts_required_status=private",
        "_dry_public_ready=no",
        "_dry_archive_ready=no",
        "_dry_calculator_ready=no",
        "drycured_public_publish_allowed=false",
        "drycured_requires_final_human_check=true",
        "renderer_changed=NO",
        "shortcode_changed=NO",
        "execute_private_status=NOT_RUN",
    ]
    (report_dir / "BATCH_001_SAFETY_CHECK.txt").write_text("\n".join(safety) + "\n", encoding="utf-8")
    return result


def build_server_aware_rows(selected: list[dict[str, Any]], config: dict[str, Any], wp_terms: dict[str, list[dict[str, Any]]]) -> tuple[list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]], list[dict[str, Any]]]:
    lookup = existing_term_lookup(wp_terms)
    hard_defaults = config.get("hard_safety_defaults", {})
    table_rows: list[dict[str, Any]] = []
    readiness_rows: list[dict[str, Any]] = []
    taxonomy_rows: list[dict[str, Any]] = []
    terms_review: dict[tuple[str, str], dict[str, Any]] = {}
    missing_rows: list[dict[str, Any]] = []
    blocked_rows: list[dict[str, Any]] = []
    import_plan_rows: list[dict[str, Any]] = []

    for candidate in selected:
        recipe_code = candidate["_source_id"]
        title = candidate["_title"]
        number = candidate["_record_number"]
        markdown = candidate["_markdown"]
        excerpt = candidate["_excerpt"]
        slug = slugify(f"{number:04d}-{title}")
        missing_fields = list(candidate["_missing_fields"])
        blocked = bool(candidate["_blocked"])
        action = "BLOCKED_REVIEW" if blocked else "WOULD_CREATE_PRIVATE"
        needs_review = bool(missing_fields or candidate["_status"] != "ready_for_private_dry_run")
        audit_status = "REVIEW" if needs_review else "PASS"
        blocked_reason = candidate.get("_hard_block_reason") or ("category_only_record" if candidate.get("_category_only") else "blocked_by_triage_or_out_of_scope")
        table_row = {
            "batch": "batch_001_server_aware_dry_run",
            "record_number": number,
            "recipe_id": recipe_code,
            "title": title,
            "slug": slug,
            "readiness_score": candidate["_readiness_score"],
            "dry_run_action": action,
            "would_post_status": "private" if not blocked else "",
            "would_update_private": "no",
            "needs_review": "yes" if needs_review else "no",
            "blocked_reason": blocked_reason if blocked else "",
            "missing_required_fields": "|".join(missing_fields),
            "source_audit_status": audit_status,
            "source_audit_reason": candidate["_audit_reason"],
            "public_publish_allowed": "false",
        }
        table_rows.append(table_row)
        readiness_rows.append({
            "record_number": number,
            "recipe_id": recipe_code,
            "title": title,
            "readiness_score": candidate["_readiness_score"],
            "has_country": "yes" if candidate["_country"] not in REVIEW_VALUES else "no",
            "has_region": "yes" if candidate["_region"] not in REVIEW_VALUES else "no",
            "has_product_category": "yes" if candidate["_category"] not in REVIEW_VALUES else "no",
            "has_meat_type": "yes" if candidate["_meat_type"] not in REVIEW_VALUES else "no",
            "has_process_type": "yes" if candidate["_process_type"] not in REVIEW_VALUES else "no",
            "has_ingredients": "yes" if candidate["_has_ingredients"] else "no",
            "has_process": "yes" if candidate["_has_process"] else "no",
            "blocked": "yes" if blocked else "no",
            "triage_status": candidate["_triage"],
            "safety_status": candidate["_safety"],
        })
        if missing_fields:
            missing_rows.append({
                "source_recipe_id": recipe_code,
                "title": title,
                "missing_country": "yes" if "dry_country" in missing_fields else "no",
                "missing_region": "yes" if "dry_region" in missing_fields else "no",
                "missing_product_category": "yes" if "dry_product_category" in missing_fields else "no",
                "missing_meat_type": "yes" if "dry_meat_type" in missing_fields else "no",
                "missing_process_type": "yes" if "dry_process_type" in missing_fields else "no",
                "missing_ingredients": "yes" if "ingredients" in missing_fields else "no",
                "missing_process": "yes" if "process" in missing_fields else "no",
                "suggested_fix": candidate["_suggested_fix"],
                "status": candidate["_status"],
            })
        if blocked:
            blocked_rows.append({
                "source_recipe_id": recipe_code,
                "title": title,
                "reason": candidate["_audit_reason"],
                "blocked_category": blocked_reason,
                "can_be_category_only_record": "yes" if candidate["_category"] not in REVIEW_VALUES else "no",
                "should_remain_rejected": "yes",
            })

        taxonomy_values = {
            "dry_country": candidate["_country"],
            "dry_region": candidate["_region"],
            "dry_product_category": candidate["_category"],
            "dry_meat_type": candidate["_meat_type"],
            "dry_process_type": candidate["_process_type"],
        }
        for taxonomy, term_value in taxonomy_values.items():
            terms = [item.strip() for item in str(term_value).split("|") if item.strip()] if taxonomy == "dry_process_type" else [term_value]
            for term in terms:
                existing, report_term, existing_note = resolve_existing_term_with_alias(taxonomy, term, lookup)
                combined_or_unclean = "/" in str(term) or "\\" in str(term)
                status = "existing" if existing else ("not_mapped_review_value" if term in REVIEW_VALUES or combined_or_unclean else "to_create_review")
                term_slug = str(existing.get("slug")) if existing else slugify(term)
                taxonomy_rows.append({
                    "recipe_id": recipe_code,
                    "taxonomy": taxonomy,
                    "term_name": report_term,
                    "term_slug": term_slug,
                    "term_status": status,
                    "existing_term_id": existing.get("term_id", "") if existing else "",
                    "notes": existing_note if existing else ("combined value needs editorial split before term creation" if combined_or_unclean else existing_note),
                })
                if status == "to_create_review":
                    key = (taxonomy, term_slug)
                    if key not in terms_review:
                        terms_review[key] = {
                            "taxonomy": taxonomy,
                            "proposed_name": term,
                            "proposed_slug": term_slug,
                            "reason_existing_term_not_enough": "no matching existing term name/slug in WP term audit",
                            "recipe_count": 0,
                        }
                    terms_review[key]["recipe_count"] += 1

        sections = {"full_markdown": markdown, "source_excerpt_file": f"R{number:03d}_source_excerpt.json"}
        meta_values = {
            "_dry_recipe_id": recipe_code,
            "dry_recipe_code": recipe_code,
            "recipe_id": recipe_code,
            "_dry_recipe_full_markdown": markdown,
            "_dry_recipe_sections": json.dumps(sections, ensure_ascii=False),
            "_dry_country": candidate["_country"],
            "_dry_region": candidate["_region"],
            "_dry_microregion": "not_specified_in_source",
            "_dry_product_type": candidate["_product_type"],
            "_dry_category": candidate["_category"],
            "_dry_source": str(excerpt.get("source_file") or "09_ALL_485_RAW_MASTER_DOCUMENT"),
            "_dry_recipe_source_audit_status": audit_status,
            "_dry_recipe_source_audit_reason": candidate["_audit_reason"],
            **hard_defaults,
        }
        import_plan_rows.append({
            "recipe_id": recipe_code,
            "source_title": title,
            "planned_action": action,
            "post_type": "dry_recipe",
            "post_status": "private" if not blocked else "",
            "expected_slug": slug,
            "meta_keys_count": len(meta_values),
            "taxonomy_rows_count": sum(1 for row in taxonomy_rows if row["recipe_id"] == recipe_code),
            "wordpress_write_performed": "NO",
            "notes": "DRY_RUN only; EXECUTE_PRIVATE not run",
        })
    return table_rows, readiness_rows, taxonomy_rows, list(terms_review.values()), missing_rows, blocked_rows, import_plan_rows


def write_wp_term_audit_reports(report_dir: Path, wp_terms: dict[str, list[dict[str, Any]]], term_source: str, optional_empty: list[str] | None = None) -> None:
    optional_empty = optional_empty or []
    summary_lines = ["# WP Term Audit — Read Only", "", f"source: {term_source}", f"optional_taxonomies_empty: {len(optional_empty)}", ""]
    for taxonomy in TAXONOMIES:
        rows = wp_terms.get(taxonomy, [])
        if taxonomy in {"dry_country", "dry_region", "dry_product_category", "dry_meat_type", "dry_process_type"}:
            write_csv(report_dir / f"WP_TERM_AUDIT_{taxonomy}.csv", rows, ["term_id", "name", "slug", "count", "source"])
        suffix = " optional_taxonomy_empty" if taxonomy in optional_empty else ""
        summary_lines.append(f"- {taxonomy}: {len(rows)} terms{suffix}")
    (report_dir / "WP_TERM_AUDIT_ALL_SUMMARY.md").write_text("\n".join(summary_lines) + "\n", encoding="utf-8")


def candidate_review_row(candidate: dict[str, Any]) -> dict[str, Any]:
    return {
        "source_recipe_id": candidate["_source_id"],
        "record_number": candidate["_record_number"],
        "title": candidate["_title"],
        "triage_status": candidate["_triage"],
        "safety_status": candidate["_safety"],
        "category": candidate["_category"],
        "reason": candidate.get("_hard_block_reason") or candidate.get("_category_only_status") or candidate["_audit_reason"],
        "status": candidate["_status"],
        "readiness_score": candidate["_readiness_score"],
    }


def write_wp_import_script_audit(root: Path, report_dir: Path) -> None:
    script = root / "tools" / "global_recipe_database" / "drycured_mass_recipe_pipeline_wp_import_v2.php"
    text = read_text(script) if script.exists() else ""
    checks = {
        "wp_insert_post_exists": "YES" if "wp_insert_post" in text else "NO",
        "wp_update_post_exists": "YES" if "wp_update_post" in text else "NO",
        "update_post_meta_exists": "YES" if "update_post_meta" in text else "NO",
        "wp_set_object_terms_exists": "YES" if "wp_set_object_terms" in text else "NO",
        "post_status_publish_assignment_exists": "YES" if re.search(r"post_status['\"]?\s*=>\s*['\"]publish['\"]", text) else "NO",
        "new_posts_private_assignment_exists": "YES" if re.search(r"post_status['\"]?\s*=>\s*['\"]private['\"]", text) else "NO",
        "publish_prohibition_text_exists": "YES" if "never publishes" in text or "publish is forbidden" in text else "NO",
        "drycured_public_publish_allowed_false_check_exists": "YES" if "drycured_public_publish_allowed" in text and "false" in text else "NO",
    }
    lines = [
        "# BATCH 001 WP Import Script Audit",
        "",
        f"script: {script}",
        "",
    ]
    lines.extend(f"{key}: {value}" for key, value in checks.items())
    lines.extend([
        "",
        "Conclusion:",
        "- wp_insert_post is allowed only for private post creation in the execute bridge.",
        "- update_post_meta is allowed only for source-lock/mass-pipeline private metadata.",
        "- wp_set_object_terms is allowed only after reviewed DRY_RUN terms.",
        "- No public publish status assignment may be present.",
    ])
    (report_dir / "BATCH_001_WP_IMPORT_SCRIPT_AUDIT.md").write_text("\n".join(lines) + "\n", encoding="utf-8")


def write_clean_v202_reports(root: Path, report_dir: Path, hard_blocked_candidates: list[dict[str, Any]], category_only_candidates: list[dict[str, Any]], import_plan_rows: list[dict[str, Any]], terms_review: list[dict[str, Any]]) -> None:
    review_fields = ["source_recipe_id", "record_number", "title", "triage_status", "safety_status", "category", "reason", "status", "readiness_score"]
    write_csv(report_dir / "BATCH_001_HARD_BLOCKED_RECORDS.csv", [candidate_review_row(row) for row in hard_blocked_candidates], review_fields)
    write_csv(report_dir / "BATCH_001_CATEGORY_ONLY_RECORDS.csv", [candidate_review_row(row) for row in category_only_candidates], review_fields)
    clean_plan = [
        row for row in import_plan_rows
        if row.get("planned_action") == "WOULD_CREATE_PRIVATE"
        and row.get("recipe_id") not in HARD_BLOCK_IDS
    ]
    write_csv(report_dir / "BATCH_001_CLEAN_PRIVATE_IMPORT_PLAN.csv", clean_plan, ["recipe_id", "source_title", "planned_action", "post_type", "post_status", "expected_slug", "meta_keys_count", "taxonomy_rows_count", "wordpress_write_performed", "notes"])
    final_terms = []
    for row in terms_review:
        if row.get("taxonomy") == "dry_product_category" and slugify(str(row.get("proposed_name") or "")) == "suho-meso":
            final_terms.append({
                **row,
                "final_decision": "review_required",
                "existing_terms_considered": "Cijeli komad|Cijeli komadi|Cijelorezni i suhomesnati proizvodi",
                "final_reason": "Create Suho meso only if no existing whole-piece term is present in live WP audit.",
            })
        else:
            final_terms.append({
                **row,
                "final_decision": "review_required",
                "existing_terms_considered": "",
                "final_reason": row.get("reason_existing_term_not_enough", ""),
            })
    write_csv(report_dir / "BATCH_001_TERMS_TO_CREATE_FINAL_REVIEW.csv", final_terms, ["taxonomy", "proposed_name", "proposed_slug", "reason_existing_term_not_enough", "recipe_count", "final_decision", "existing_terms_considered", "final_reason"])
    write_wp_import_script_audit(root, report_dir)


def write_server_aware_reports(root: Path, config: dict[str, Any], records: list[dict[str, str]], selected: list[dict[str, Any]], wp_terms: dict[str, list[dict[str, Any]]], term_source: str, report_roots: list[str], legacy_blocked_candidates: list[dict[str, Any]] | None = None, report_variant: str = "SERVER_AWARE", optional_empty: list[str] | None = None, hard_blocked_candidates: list[dict[str, Any]] | None = None, category_only_candidates: list[dict[str, Any]] | None = None) -> BatchResult:
    table_rows, readiness_rows, taxonomy_rows, terms_review, missing_rows, blocked_rows, import_plan_rows = build_server_aware_rows(selected, config, wp_terms)
    hard_blocked_candidates = hard_blocked_candidates or []
    category_only_candidates = category_only_candidates or []
    existing_count = sum(1 for row in taxonomy_rows if row["term_status"] == "existing")
    to_create_count = len(terms_review)
    scores = [int(row["readiness_score"]) for row in readiness_rows] or [0]
    blocked_report_rows = list(blocked_rows)
    if not blocked_report_rows and legacy_blocked_candidates:
        for candidate in legacy_blocked_candidates:
            blocked_report_rows.append({
                "source_recipe_id": candidate["_source_id"],
                "title": candidate["_title"],
                "reason": candidate["_audit_reason"],
                "blocked_category": "legacy_first50_out_of_scope_or_high_risk",
                "can_be_category_only_record": "yes" if candidate["_category"] not in REVIEW_VALUES else "no",
                "should_remain_rejected": "yes",
            })
    result = BatchResult(
        input_records=len(records),
        selected_for_batch=len(table_rows),
        would_create_private=sum(1 for row in table_rows if row["dry_run_action"] == "WOULD_CREATE_PRIVATE"),
        would_update_private=0,
        blocked=len(blocked_rows),
        needs_review=sum(1 for row in table_rows if row["needs_review"] == "yes"),
        taxonomy_terms_existing=existing_count,
        taxonomy_terms_to_create=to_create_count,
        missing_required_fields=len(missing_rows),
        readiness_score_min=min(scores),
        readiness_score_max=max(scores),
        optional_taxonomies_empty=len(optional_empty or []),
        hard_blocked_count=len(hard_blocked_candidates),
        category_only_count=len(category_only_candidates),
    )
    table_fields = ["batch", "record_number", "recipe_id", "title", "slug", "readiness_score", "dry_run_action", "would_post_status", "would_update_private", "needs_review", "blocked_reason", "missing_required_fields", "source_audit_status", "source_audit_reason", "public_publish_allowed"]
    readiness_fields = ["record_number", "recipe_id", "title", "readiness_score", "has_country", "has_region", "has_product_category", "has_meat_type", "has_process_type", "has_ingredients", "has_process", "blocked", "triage_status", "safety_status"]
    missing_fields = ["source_recipe_id", "title", "missing_country", "missing_region", "missing_product_category", "missing_meat_type", "missing_process_type", "missing_ingredients", "missing_process", "suggested_fix", "status"]
    blocked_fields = ["source_recipe_id", "title", "reason", "blocked_category", "can_be_category_only_record", "should_remain_rejected"]
    for report_root in report_roots:
        report_dir = root / report_root
        report_dir.mkdir(parents=True, exist_ok=True)
        write_wp_term_audit_reports(report_dir, wp_terms, term_source, optional_empty)
        write_clean_v202_reports(root, report_dir, hard_blocked_candidates, category_only_candidates, import_plan_rows, terms_review)
        live = report_variant == "SERVER_LIVE"
        table_name = "BATCH_001_SERVER_LIVE_DRY_RUN_TABLE.csv" if live else "BATCH_001_SERVER_AWARE_DRY_RUN_TABLE.csv"
        readiness_name = "BATCH_001_SERVER_LIVE_READINESS_SCORE_TABLE.csv" if live else "BATCH_001_READINESS_SCORE_TABLE.csv"
        taxonomy_name = "BATCH_001_SERVER_LIVE_TAXONOMY_MAPPING.csv" if live else "BATCH_001_TAXONOMY_MAPPING_SERVER_AWARE.csv"
        terms_name = "BATCH_001_SERVER_LIVE_TERMS_TO_CREATE_REVIEW.csv" if live else "BATCH_001_TERMS_TO_CREATE_REVIEW.csv"
        missing_name = "BATCH_001_SERVER_LIVE_MISSING_FIELDS_DETAILS.csv" if live else "BATCH_001_MISSING_FIELDS_DETAILS.csv"
        blocked_name = "BATCH_001_SERVER_LIVE_BLOCKED_DETAILS.csv" if live else "BATCH_001_BLOCKED_DETAILS.csv"
        import_plan_name = "BATCH_001_SERVER_LIVE_PRIVATE_IMPORT_PLAN.csv" if live else "BATCH_001_PRIVATE_IMPORT_PLAN.csv"
        summary_name = "BATCH_001_SERVER_LIVE_DRY_RUN_SUMMARY.md" if live else "BATCH_001_SERVER_AWARE_DRY_RUN_SUMMARY.md"
        safety_name = "BATCH_001_SERVER_LIVE_SAFETY_CHECK.txt" if live else "BATCH_001_SAFETY_CHECK.txt"
        blocked_md_name = "BATCH_001_SERVER_LIVE_BLOCKED_DETAILS.md" if live else "BATCH_001_BLOCKED_DETAILS.md"
        write_csv(report_dir / table_name, table_rows, table_fields)
        write_csv(report_dir / readiness_name, readiness_rows, readiness_fields)
        write_csv(report_dir / taxonomy_name, taxonomy_rows, ["recipe_id", "taxonomy", "term_name", "term_slug", "term_status", "existing_term_id", "notes"])
        write_csv(report_dir / terms_name, terms_review, ["taxonomy", "proposed_name", "proposed_slug", "reason_existing_term_not_enough", "recipe_count"])
        write_csv(report_dir / missing_name, missing_rows, missing_fields)
        write_csv(report_dir / blocked_name, blocked_report_rows, blocked_fields)
        write_csv(report_dir / import_plan_name, import_plan_rows, ["recipe_id", "source_title", "planned_action", "post_type", "post_status", "expected_slug", "meta_keys_count", "taxonomy_rows_count", "wordpress_write_performed", "notes"])
        (report_dir / "BATCH_001_BLOCKED_DETAILS.md").write_text(
            "# BATCH 001 Blocked Details\n\n" + ("\n".join(f"- {row['source_recipe_id']}: {row['title']} — {row['reason']}" for row in blocked_report_rows) if blocked_report_rows else "No blocked records in selected server-aware batch.") + "\n",
            encoding="utf-8",
        )
        if blocked_md_name != "BATCH_001_BLOCKED_DETAILS.md":
            (report_dir / blocked_md_name).write_text(
                "# BATCH 001 Server Live Blocked Details\n\n" + ("\n".join(f"- {row['source_recipe_id']}: {row['title']} — {row['reason']}" for row in blocked_report_rows) if blocked_report_rows else "No blocked records in selected server live batch.") + "\n",
                encoding="utf-8",
            )
        summary = [
            "# DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 CLEAN SERVER LIVE DRY_RUN" if live else "# DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 CLEAN SERVER-AWARE DRY_RUN",
            "",
            f"input_records: {result.input_records}",
            f"selected_for_batch: {result.selected_for_batch}",
            f"would_create_private: {result.would_create_private}",
            f"would_update_private: {result.would_update_private}",
            f"blocked: {result.blocked}",
            f"hard_blocked_count: {result.hard_blocked_count}",
            f"category_only_count: {result.category_only_count}",
            f"needs_review: {result.needs_review}",
            f"taxonomy_terms_existing: {result.taxonomy_terms_existing}",
            f"taxonomy_terms_to_create: {result.taxonomy_terms_to_create}",
            f"missing_required_fields: {result.missing_required_fields}",
            f"readiness_score_min: {result.readiness_score_min}",
            f"readiness_score_max: {result.readiness_score_max}",
            f"optional_taxonomies_empty: {result.optional_taxonomies_empty}",
            f"used_live_wp_terms: {'YES' if term_source == 'wp_term_list' else 'NO'}",
            f"used_fallback_terms: {'NO' if term_source == 'wp_term_list' else 'YES'}",
            "public_publish_attempts: 0",
            "wordpress_write_performed: NO",
            "renderer_changed: NO",
            "shortcode_changed: NO",
            "EXECUTE_PRIVATE_RUN: NO",
            "",
            "Notes:",
            "- Server-aware term audit is read-only.",
            f"- Term audit source: {term_source}.",
            "- Batch selection is top 50 by readiness score after hard-block and category-only exclusion.",
            "- Hard-blocked and category-only records are reported separately and excluded from clean private import plan.",
        ]
        (report_dir / summary_name).write_text("\n".join(summary) + "\n", encoding="utf-8")
        safety = [
            "DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 SERVER LIVE SAFETY CHECK" if live else "DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 SERVER-AWARE SAFETY CHECK",
            "mode=SERVER_LIVE_DRY_RUN" if live else "mode=SERVER_AWARE_DRY_RUN",
            "wordpress_write_performed=NO",
            "public_publish_attempts=0",
            "new_posts_required_status=private",
            "_dry_public_ready=no",
            "_dry_archive_ready=no",
            "_dry_calculator_ready=no",
            "drycured_public_publish_allowed=false",
            "drycured_requires_final_human_check=true",
            "renderer_changed=NO",
            "shortcode_changed=NO",
            "EXECUTE_PRIVATE_RUN=NO",
        ]
        (report_dir / safety_name).write_text("\n".join(safety) + "\n", encoding="utf-8")
    return result


def parse_args(argv: list[str]) -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="DRYCURED mass recipe pipeline v2.0.2")
    parser.add_argument("--mode", choices=["DRY_RUN", "EXECUTE_PRIVATE"], default="DRY_RUN")
    parser.add_argument("--batch", default="001")
    parser.add_argument("--root", default=None)
    parser.add_argument("--report-root", default=None, help="Override report root relative to project root for local dry-run verification.")
    parser.add_argument("--server-aware", action="store_true", help="Run v2.0.2 server-aware dry-run with WP term audit and readiness scoring.")
    parser.add_argument("--server-live", action="store_true", help="Run v2.0.2 server-live dry-run. Requires live WP-CLI terms and never falls back.")
    parser.add_argument("--wp-path", default="/var/www/html", help="WordPress path for WP-CLI term audit in --server-live mode.")
    return parser.parse_args(argv)


def main(argv: list[str]) -> int:
    args = parse_args(argv)
    if args.mode != "DRY_RUN":
        raise SystemExit("EXECUTE_PRIVATE is handled by drycured_mass_recipe_pipeline_wp_import_v2.php after human review.")
    root = Path(args.root).resolve() if args.root else project_root_from(Path.cwd()).resolve()
    config = load_config(root)
    index_rows = read_csv_rows(resolve_path(root, config["source_paths"]["all_485_index_csv"]))
    transfer_rows = read_csv_rows(resolve_path(root, config["source_paths"]["transfer_table_csv"]))
    records = merge_records(index_rows, transfer_rows)
    batch_number = int(args.batch)
    if args.server_aware or args.server_live:
        wp_terms, term_source, optional_empty = load_wp_terms(require_live=bool(args.server_live), wp_path=args.wp_path if args.server_live else None)
        all_candidates = [derive_candidate(row, config, root) for row in records]
        selected = select_server_aware_batch(records, config, root, batch_number)
        legacy_first50 = [derive_candidate(row, config, root) for row in records[: int(config.get("batch_size", 50))]]
        legacy_blocked = [row for row in legacy_first50 if row["_blocked"]]
        hard_blocked = [row for row in all_candidates if row["_hard_blocked"]]
        category_only = [row for row in all_candidates if row["_category_only"]]
        report_roots = [SERVER_LIVE_REPORT_ROOT] if args.server_live else [SERVER_AWARE_REPORT_ROOT, LOCAL_SERVER_AWARE_REPORT_ROOT]
        if args.report_root:
            report_roots = [args.report_root]
        result = write_server_aware_reports(root, config, records, selected, wp_terms, term_source, report_roots, legacy_blocked, "SERVER_LIVE" if args.server_live else "SERVER_AWARE", optional_empty, hard_blocked, category_only)
        print("DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 CLEAN SERVER LIVE DRY_RUN" if args.server_live else "DRYCURED MASS RECIPE PIPELINE v2.0.2 — BATCH 001 CLEAN SERVER-AWARE DRY_RUN")
        print(f"input_records: {result.input_records}")
        print(f"selected_for_batch: {result.selected_for_batch}")
        print(f"would_create_private: {result.would_create_private}")
        print(f"would_update_private: {result.would_update_private}")
        print(f"blocked: {result.blocked}")
        print(f"hard_blocked_count: {result.hard_blocked_count}")
        print(f"category_only_count: {result.category_only_count}")
        print(f"needs_review: {result.needs_review}")
        print(f"taxonomy_terms_existing: {result.taxonomy_terms_existing}")
        print(f"taxonomy_terms_to_create: {result.taxonomy_terms_to_create}")
        print(f"missing_required_fields: {result.missing_required_fields}")
        print(f"readiness_score_min: {result.readiness_score_min}")
        print(f"readiness_score_max: {result.readiness_score_max}")
        print(f"optional_taxonomies_empty: {result.optional_taxonomies_empty}")
        print(f"used_live_wp_terms: {'YES' if term_source == 'wp_term_list' else 'NO'}")
        print(f"used_fallback_terms: {'NO' if term_source == 'wp_term_list' else 'YES'}")
        print("public_publish_attempts: 0")
        print("wordpress_write_performed: NO")
        print("renderer_changed: NO")
        print("shortcode_changed: NO")
        print("EXECUTE_PRIVATE_RUN: NO")
        return 0
    table_rows, taxonomy_rows, meta_rows, review_rows, blocked_rows = build_batch_rows(records, config, root, batch_number)
    result = write_reports(root, config, batch_number, records, table_rows, taxonomy_rows, meta_rows, review_rows, blocked_rows, args.report_root)
    print("DRYCURED MASS RECIPE PIPELINE v2.0 — BATCH 001 DRY_RUN")
    print(f"input_records: {result.input_records}")
    print(f"selected_for_batch: {result.selected_for_batch}")
    print(f"would_create_private: {result.would_create_private}")
    print(f"would_update_private: {result.would_update_private}")
    print(f"blocked: {result.blocked}")
    print(f"needs_review: {result.needs_review}")
    print(f"taxonomy_terms_existing: {result.taxonomy_terms_existing}")
    print(f"taxonomy_terms_to_create: {result.taxonomy_terms_to_create}")
    print(f"missing_required_fields: {result.missing_required_fields}")
    print("public_publish_attempts: 0")
    print("wordpress_write_performed: NO")
    print("renderer_changed: NO")
    print("shortcode_changed: NO")
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))
