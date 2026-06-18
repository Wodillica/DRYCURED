# 3535 private clone read-only QA v1

Status: **PASS_READ_ONLY**

Ovaj QA ne mijenja WordPress. Provjerava privatni clone i zaštitu javnog posta 3042.

## Sažetak

- Source post ID: `3042`
- Clone ID: `3535`
- Clone status: `private`
- Clone URL: `https://drycured.com/?post_type=dry_recipe&p=3535`
- Source unchanged from clone creation: `true`
- Public fetch HTTP code: `404`
- Publicly exposed: `false`
- Checks total: `32`
- Fail total: `0`
- Blocker fail total: `0`

## QA tablica

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| Privatni clone postoji | PASS | BLOCKER | Clone ID mora postojati u WordPressu. |
| Javni source post postoji | PASS | BLOCKER | Source post 3042 mora postojati. |
| Clone je private | PASS | BLOCKER | Clone ne smije biti publish. |
| Clone je dry_recipe | PASS | BLOCKER | Clone mora biti dry_recipe. |
| Source 3042 je publish | PASS | BLOCKER | Source 3042 mora ostati javni publish recept. |
| Source 3042 je dry_recipe | PASS | BLOCKER | Source 3042 mora ostati dry_recipe. |
| Source 3042 nije mijenjan od clone stvaranja | PASS | BLOCKER | Usporedba s result JSON-om iz trenutka clone stvaranja. |
| Meta postoji: _dry_recipe_preview_mode | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_preview_source_post_id | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_public_update_allowed | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_dossier_status | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_public_verified | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_source_validation_status | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_type_router | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_adapter_payload_version | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_dossier_path | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_active_blockers | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_sections | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_verified_process | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta postoji: _dry_recipe_full_markdown | PASS | MAJOR | Obvezni meta ključ za privatni preview clone. |
| Meta source ID je 3042 | PASS | BLOCKER | Clone mora biti vezan na source post 3042. |
| Meta public update allowed je 0 | PASS | BLOCKER | Privatni clone ne smije dopustiti javni update. |
| Meta public verified je 0 | PASS | BLOCKER | Privatni clone ne smije biti public verified. |
| Meta preview mode je PRIVATE_CLONE_ONLY | PASS | BLOCKER | Meta mora jasno označiti privatni clone. |
| Meta type router je GROUND_MEAT_OR_CASING | PASS | MAJOR | Tipološka klasifikacija mora ostati kobasični model. |
| _dry_recipe_sections je valjan JSON | PASS | MAJOR | Renderer/adapter očekuje čitljiv JSON. |
| _dry_verified_process je valjan JSON | PASS | MAJOR | Proces mora biti čitljiv JSON. |
| _dry_recipe_active_blockers je valjan JSON | PASS | MAJOR | Blokade moraju biti čitljiv JSON. |
| _dry_recipe_full_markdown ima sadržaj | PASS | MAJOR | Markdown mora sadržavati radni prikaz recepta. |
| Markdown označava privatni preview | PASS | MAJOR | Privatni sadržaj mora biti jasno označen u dosjeu/cloneu. |
| Privatni clone nije javno izložen kao recept | PASS | BLOCKER | Ako HTTP 200 javno prikazuje naslov recepta, private zaštita nije dobra. |
| Render snapshot sadrži naslov | PASS | MINOR | Interni render snapshot treba imati osnovni sadržaj. |

## Zaključak

Privatni clone je tehnički ispravan za interni pregled. Javni post 3042 nije mijenjan i clone nije javno izložen kao recept.
