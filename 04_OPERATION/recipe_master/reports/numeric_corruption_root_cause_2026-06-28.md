# Numeric Corruption Audit — Root Cause (2026-06-28)

## Pattern
22 published MD-* recipes contain repeating-decimal artifacts:
- 8,889 kg / 11,111 kg  (= 8/9 × 10 and 10/9 × 10)
- 6,667 kg              (= 2/3 × 10)
- 3,333 kg              (= 1/3 × 10)
- 8,333 / 11,667 kg     (= 5/6 × 10 and 7/6 × 10)
- 13,333 kg             (= 40/3)

## Root Cause
Batch import pipeline `drycured_mass_recipe_pipeline_v2.py` generated recipes
targeting a 10 kg batch. When translating original ingredient proportions, a
scaling division by 9 (or by 3, or by 1.5) was applied instead of correct
proportional scaling, producing floating-point remainders embedded in ingredient
quantities in `public_content_md`.

These values were frozen in:
  `server-reports/recipes/public-master-v1-import/2026-06-03_18-38-52/
   drycured_public_master_v1_0/DRYCURED_ALL_489_PUBLIC_READY_IMPORT_SOURCE_LOCK_v1_0.json`

The corrupted `_dry_recipe_full_markdown` meta was imported directly from this JSON.

## Action Taken (2026-06-28)
- 15 posts (Category A — EU PDO/PGI or well-documented): kept published,
  flagged for Claude(chat) web search to retrieve correct canonical quantities.
- 7 posts (Category B — regional/niche, no source): set to draft with
  flag NEMA_IZVORA.

## Do NOT auto-correct numeric values. Source must be verified first.
