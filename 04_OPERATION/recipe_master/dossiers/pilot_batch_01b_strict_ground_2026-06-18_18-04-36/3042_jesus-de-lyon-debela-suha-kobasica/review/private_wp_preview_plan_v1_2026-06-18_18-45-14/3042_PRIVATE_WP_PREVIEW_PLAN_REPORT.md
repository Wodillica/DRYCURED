# 3042 Jésus de Lyon — private WordPress preview plan dry-run v1

Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**

Ovaj korak ne mijenja WordPress. Izrađuje samo plan za budući privatni clone.

## Sažetak

- Source public post: `3042`
- Source post write allowed: `false`
- Future target: `PRIVATE_CLONE_ONLY`
- Meta keys planned: `13`
- Public update allowed: `false`

## Meta keys

- `_dry_recipe_preview_mode` → samo budući privatni clone
- `_dry_recipe_preview_source_post_id` → samo budući privatni clone
- `_dry_recipe_public_update_allowed` → samo budući privatni clone
- `_dry_recipe_dossier_status` → samo budući privatni clone
- `_dry_recipe_public_verified` → samo budući privatni clone
- `_dry_recipe_source_validation_status` → samo budući privatni clone
- `_dry_recipe_type_router` → samo budući privatni clone
- `_dry_recipe_adapter_payload_version` → samo budući privatni clone
- `_dry_recipe_dossier_path` → samo budući privatni clone
- `_dry_recipe_active_blockers` → samo budući privatni clone
- `_dry_recipe_sections` → samo budući privatni clone
- `_dry_verified_process` → samo budući privatni clone
- `_dry_recipe_full_markdown` → samo budući privatni clone

## Sigurnosna pravila

- Ne smije se pisati u javni post 3042.
- Ne smije se mijenjati javni title, slug, status ni URL.
- Ne smije se mijenjati renderer.
- Meta vrijednosti iz ovog plana smiju se koristiti samo na budućem privatnom cloneu.
- Privatni clone mora imati post_status=private.
- Javni update ostaje zabranjen dok se ne zatvore izvor količina, starter kultura, dimljenje i interni javni tragovi.

## Izlazne datoteke

- `3042_private_wp_preview_plan_v1.json`
- `3042_private_wp_preview_meta_map.json`
- `3042_private_wp_preview_meta_map.csv`
- `3042_private_wp_preview_full_markdown.md`
- `3042_PRIVATE_WP_PREVIEW_PLAN.html`
- `3042_PRIVATE_WP_PREVIEW_EXECUTION_CHECKLIST.md`

## Zaključak

Plan je spreman za pregled. Sljedeći korak, ako se odobri, smije napraviti samo privatni clone i upisivati samo u taj privatni clone.
