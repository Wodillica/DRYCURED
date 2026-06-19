# 2697 Baranjska Ljuta Slavonska Kobasica quick intake v1

Status: **INTAKE_COMPLETE**

Ovaj korak ne mijenja WordPress. Radi početni read-only intake prvog kandidata iz strict hrvatskog reda čekanja.

## Sažetak

- Post ID: `2697`
- Title: `Baranjska Ljuta Slavonska Kobasica`
- Status: `publish`
- Type: `dry_recipe`
- Expected recipe type: `GROUND_MEAT_OR_CASING`
- URL: `https://drycured.com/recepti-baza/baranjska-ljuta-slavonska-kobasica/`
- HTTP code: `200`
- WordPress write allowed: `false`
- Public update allowed: `false`
- Major/blocker fail total: `0`
- Blocker fail total: `0`

## Meta stanje

- `_dry_recipe_id`: `present`
- `_dry_recipe_full_markdown`: `present`
- `_dry_recipe_image_url`: `missing`
- `_dry_recipe_sections`: `missing`
- `_dry_verified_process`: `missing`

## QA provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| Post 2697 postoji | PASS | BLOCKER | WP zapis mora postojati. |
| Post je dry_recipe | PASS | BLOCKER | Očekuje se dry_recipe. |
| Post je publish | PASS | MAJOR | Strict queue ga je odabrao kao javni zapis. |
| Naziv odgovara Baranjskoj kobasici | PASS | BLOCKER | Provjera identiteta recepta. |
| Javni HTTP je 200 | PASS | MAJOR | Javni zapis mora biti dohvatljiv. |
| Signal mljevenog proizvoda u ovitku | PASS | BLOCKER | Kobasica ide u GROUND_MEAT_OR_CASING. |
| Signal Baranja/Slavonija | PASS | MAJOR | Mora imati hrvatski regionalni signal. |
| Javni prikaz nema interne oznake | PASS | MAJOR | Javni recept ne smije imati interne QA/preview oznake. |
| Meta postoji: _dry_recipe_id | PASS | MAJOR | Pregled postojećeg meta stanja; ne znači da je javno spremno. |
| Meta postoji: _dry_recipe_full_markdown | PASS | INFO | Pregled postojećeg meta stanja; ne znači da je javno spremno. |
| Meta postoji: _dry_recipe_image_url | FAIL | INFO | Pregled postojećeg meta stanja; ne znači da je javno spremno. |
| Meta postoji: _dry_recipe_sections | FAIL | INFO | Pregled postojećeg meta stanja; ne znači da je javno spremno. |
| Meta postoji: _dry_verified_process | FAIL | INFO | Pregled postojećeg meta stanja; ne znači da je javno spremno. |

## Zaključak

Sljedeći korak je source validation za `2697 — Baranjska Ljuta Slavonska Kobasica`. Javni update nije dopušten u intake koraku.
