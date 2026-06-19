# 2697 Baranjska kobasica – ljuta varijanta preview payload v1

Status: **PREVIEW_PAYLOAD_READY_FOR_PRIVATE_CLONE**

Ovaj korak ne mijenja WordPress. Generira strukturirane payload datoteke za budući privatni clone.

## Sažetak

- Post ID: `2697`
- Recipe code: `HR-BR-2697-BARANJSKA-LJUTA-KOBASICA`
- Public update allowed: `false`
- Source post write allowed: `false`
- Sections count: `13`
- Verified process phases: `10`
- Full markdown length: `8015`
- Blocker fail total: `0`

## Output datoteke

- `2697_dry_recipe_sections.json`
- `2697_dry_verified_process.json`
- `2697_dry_recipe_full_markdown.md`
- `2697_private_preview_payload_v1.json`

## QA provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| sections_count | PASS | BLOCKER | Broj sekcija: 13. |
| has_process_section | PASS | BLOCKER | Mora postojati procesna sekcija. |
| has_raw_materials_section | PASS | MAJOR | Mora postojati sekcija sirovina. |
| has_spices_section | PASS | MAJOR | Mora postojati sekcija začina. |
| has_grinding_section | PASS | MAJOR | Mora postojati sekcija mljevenja. |
| has_casing_section | PASS | MAJOR | Mora postojati sekcija crijeva. |
| has_garlic_liquids_section | PASS | MAJOR | Mora postojati sekcija češnjaka i tekućina. |
| has_problem_solutions | PASS | MAJOR | Problemi i rješenja moraju biti prisutni. |
| verified_process_phases | PASS | BLOCKER | Verified process ima 10 faza. |
| full_markdown_length | PASS | MAJOR | Full markdown duljina: 8015. |
| full_markdown_has_6mm | PASS | BLOCKER | Full markdown mora sadržavati 6 mm. |
| full_markdown_has_casing_soak | PASS | MAJOR | Full markdown mora sadržavati namakanje crijeva. |
| full_markdown_has_smoking | PASS | BLOCKER | Full markdown mora sadržavati cikluse dimljenja. |
| public_update_false | PASS | BLOCKER | Javni update mora ostati false. |
| source_post_write_false | PASS | BLOCKER | Source post write mora ostati false. |

## Sljedeći korak

Payload je spreman za izradu privatnog clonea za `2697`, uz DB backup izvan Git repozitorija i bez promjene javnog source posta.
