#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Whole-cut source-safe gate for drycured.com recipe master.

Purpose:
- Prevent future scripts from using dirty legacy inputs as active render/import source for whole-cut recipes.
- Allow only the v1.1 source-safe master for CIJELI_KOMAD rendering/import workflows.
- Exit with non-zero status when an unsafe source is detected.
"""

from __future__ import annotations

import json
import re
import sys
from pathlib import Path
from typing import Any


EXPECTED_MASTER_DOCUMENT = "drycured_recipes_master_v1_1_cijeli_komadi_25_source_safe"
EXPECTED_BASENAME = "drycured_recipes_master_v1_1_cijeli_komadi_25_source_safe.json"

DIRTY_BASENAMES = {
    "drycured_recipes_master_v1_pilot_cijeli_komadi_25.json",
    "block_fill_candidates.json",
    "editorial_evidence_candidates.json",
    "drycured_recipes_clean_rebuild_v1_2.json",
}

BAD_PATTERN = re.compile(
    r"mljevenje|rešetk|resetk|šajb|sajb|nadjev|punjenje|crijeva|crijevo|omotač|"
    r"samljeti kroz rešetku|narezati na kocke 2.?3 cm|hladnim dimom ispod 25|"
    r"10.?15 °C|70.?80 %|35.?40 % početne mase",
    re.IGNORECASE,
)


def fail(message: str) -> None:
    print(f"FAIL | {message}")
    sys.exit(1)


def ok(message: str) -> None:
    print(f"OK | {message}")


def load_json(path: Path) -> Any:
    try:
        return json.loads(path.read_text(encoding="utf-8"))
    except Exception as exc:
        fail(f"JSON read/parse failed: {path} | {exc}")


def bad_count(obj: Any) -> int:
    return len(BAD_PATTERN.findall(json.dumps(obj, ensure_ascii=False)))


def main() -> None:
    if len(sys.argv) != 2:
        fail("Usage: whole_cut_source_safe_gate.py <candidate_master.json>")

    path = Path(sys.argv[1])

    if not path.exists():
        fail(f"Input file does not exist: {path}")

    if path.name in DIRTY_BASENAMES:
        fail(f"Dirty legacy source is not allowed for whole-cut render/import workflows: {path.name}")

    if path.name != EXPECTED_BASENAME:
        fail(f"Unexpected whole-cut source basename: {path.name}; expected {EXPECTED_BASENAME}")

    data = load_json(path)

    if not isinstance(data, dict):
        fail("Master root must be a JSON object")

    if data.get("master_document") != EXPECTED_MASTER_DOCUMENT:
        fail(
            "Wrong master_document: "
            + str(data.get("master_document"))
            + f"; expected {EXPECTED_MASTER_DOCUMENT}"
        )

    recipes = data.get("recipes")
    if not isinstance(recipes, list):
        fail("Master must contain recipes list")

    whole = [
        r for r in recipes
        if isinstance(r, dict)
        and r.get("identity", {}).get("product_group") == "CIJELI_KOMAD"
    ]

    source_safe_blocks = [
        r for r in whole
        if isinstance(r.get("blocks"), dict)
        and isinstance(r["blocks"].get("whole_cut_process_source_safe"), dict)
    ]

    count_bad = bad_count(data)

    print(f"TOTAL_RECIPES={len(recipes)}")
    print(f"WHOLE_CUT={len(whole)}")
    print(f"SOURCE_SAFE_BLOCKS={len(source_safe_blocks)}")
    print(f"BAD_TERMS={count_bad}")

    if len(recipes) != 25:
        fail("Expected exactly 25 recipes in whole-cut batch master")

    if len(whole) != 25:
        fail("Expected exactly 25 CIJELI_KOMAD recipes")

    if len(source_safe_blocks) != 25:
        fail("Each whole-cut recipe must contain blocks.whole_cut_process_source_safe")

    if count_bad != 0:
        fail("Bad whole-cut terms detected in source-safe master")

    ok("Whole-cut source-safe master passed")


if __name__ == "__main__":
    main()
