# 3535 meta-normalizer plan v1

Status: **PLAN_READY_PRIVATE_CLONE_ONLY**

Ovaj korak ne mijenja WordPress. Planira minimalni meta-normalizer za privatni clone `3535`, bez promjene renderera i bez diranja javnog posta `3042`.

## Sažetak

- WordPress write allowed now: `false`
- Public update allowed: `false`
- Future allowed target: `PRIVATE_CLONE_3535_ONLY`
- Forbidden target: `PUBLIC_SOURCE_3042`
- Clone 3535 has `_dry_recipe_id`: `false`
- Clone 3535 has `_dry_recipe_image_url`: `false`

## Planirani meta patch za budući korak

| Meta key | Action | Source | Napomena |
|---|---|---|---|
| `_dry_recipe_id` | `ADD_TO_PRIVATE_CLONE_ONLY` | `COPY_FROM_SOURCE_3042__dry_recipe_id` | Renderer i cleanup funkcije na više mjesta čitaju _dry_recipe_id. Bez tog ključa clone se ne može pouzdano ponašati kao recipe renderer kandidat. |
| `_dry_recipe_image_url` | `SKIP_NO_VALUE` | `NO_IMAGE_AVAILABLE` | Plugin referencira _dry_recipe_image_url za hero/sliku. Nije blocker za podatke, ali je važan za vizualni preview. |
| `_dry_recipe_public_update_allowed` | `KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY` | `SAFETY_GUARD` | Privatni clone nikada ne smije signalizirati javni update. |
| `_dry_recipe_public_verified` | `KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY` | `SAFETY_GUARD` | Recept nije public verified jer su aktivne blokade izvora, startera i dimljenja. |
| `_dry_recipe_preview_mode` | `KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY` | `SAFETY_GUARD` | Clone mora ostati jasno označen kao privatni preview. |

## QA provjere plana

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| Source 3042 je publish | PASS | BLOCKER | Source ostaje javni referentni zapis, ali se ne smije mijenjati. |
| Clone 3535 je private | PASS | BLOCKER | Normalizer se smije planirati samo za privatni clone. |
| Clone 3535 nema _dry_recipe_id | PASS | MAJOR | To potvrđuje razlog za meta-normalizer. |
| Postoji source code ili fallback | PASS | MAJOR | Plan mora imati vrijednost za _dry_recipe_id. |
| Plan ne dopušta javni update | PASS | BLOCKER | Ovaj plan ne smije pisati u javni 3042. |
| Plan je read-only | PASS | BLOCKER | Ovaj alat ne smije upisivati meta podatke. |

## Sigurnosna odluka

Sljedeći korak smije biti samo mali meta patch na privatnom cloneu `3535`, i to tek uz zaštitu: `post_status=private`, source `3042` read-only, public update `false`, renderer unchanged.

Javni WordPress update i dalje nije dopušten.
