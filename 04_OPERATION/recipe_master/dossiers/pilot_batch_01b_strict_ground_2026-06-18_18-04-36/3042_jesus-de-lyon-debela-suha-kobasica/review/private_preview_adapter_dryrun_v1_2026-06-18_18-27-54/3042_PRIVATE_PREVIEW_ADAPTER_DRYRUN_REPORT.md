# 3042 Jésus de Lyon — private preview adapter dry-run v1

Status: **PRIVATE_PREVIEW_PAYLOAD_READY — PUBLIC_UPDATE_FORBIDDEN**

Ovaj korak ne mijenja WordPress. Iz `recipe.yml` je izrađen offline adapter payload i lokalni HTML preview u dosjeu.

## Sažetak

- Contract checks: 9
- Contract FAIL: 0
- Private preview payload ready: `true`
- Public update allowed: `false`

## Contract check

| Polje | Status | Napomena |
|---|---|---|
| hero.title | PASS | Polje mapirano iz recipe.yml. |
| raw_materials_kg | PASS | Polje mapirano iz recipe.yml. |
| spices_and_additives_g | PASS | Polje mapirano iz recipe.yml. |
| garlic.mode | PASS | Polje mapirano iz recipe.yml. |
| grinding.meat_plate_mm | PASS | Polje mapirano iz recipe.yml. |
| casing.type | PASS | Polje mapirano iz recipe.yml. |
| done_when | PASS | Polje mapirano iz recipe.yml. |
| common_errors_and_solutions | PASS | Polje mapirano iz recipe.yml. |
| active_blockers | PASS | Polje mapirano iz recipe.yml. |

## Izlazne datoteke

- `3042_private_preview_adapter_payload.json`
- `3042_private_preview_adapter_contract.csv`
- `3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN.html`

## Aktivne blokade

- kanonski izvor za točne količine nije potvrđen
- količina starter kulture zahtijeva tehničku provjeru
- dimljenje je označeno kao needs_confirmation
- javni tekst još sadrži interne tragove prema intake izvještaju
- potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea

## Zaključak

Recept smije ići samo u privatni/offline preview za provjeru mapiranja podataka. Javni WordPress update nije dopušten.
