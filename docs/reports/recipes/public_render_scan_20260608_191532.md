# Drycured public recipe render scan — 2026-06-08 19:15:32

## Sažetak

Read-only scanner javnih `dry_recipe` recepata izvršen je nakon uspješnog MD → V5 bridge pilota za recept 3042 — Jésus de Lyon.

## Rezultat

- TOTAL_PUBLISH: 412
- HTTP_BAD: 2
- V5_RENDER: 14
- MD_BRIDGE_ACTIVE_RENDER: 1
- PUBLIC_GHOST_NO_RECIPE_RENDER: 11
- PROFILE_NOT_RENDERED: 1
- MD_BRIDGE_CANDIDATE: 383
- JSON_BROKEN: 10
- BAD_PARALLEL_RENDER: 0
- DUPLICATE_CODE: 4
- OTHER: 0

## Zaključak

MD → V5 bridge model je potvrđen na pilotu 3042, ali ne smije se širiti masovno bez dodatnog QA-a. Prioritet rada nakon ovog audita:

1. riješiti HTTP 500 javne recepte
2. riješiti PROFILE_NOT_RENDERED zapis
3. riješiti PUBLIC_GHOST_NO_RECIPE_RENDER zapise
4. tek zatim širiti MD → V5 bridge po malim kontroliranim batchovima
5. posebno pratiti JSON_BROKEN i DUPLICATE_CODE zapise

## Puni lokalni izvještaj na serveru

Puni CSV/JSON izvještaji ostaju lokalno jer je `server-reports/recipes` ignoriran u Gitu:

`/root/DRYCURED_GITHUB/server-reports/recipes/public_render_scan_20260608_191532/`

Datoteke:

- public_recipe_render_scan.csv
- public_recipe_render_scan.json
- public_recipe_render_scan_summary.txt

## Kritični prvi zapisi

HTTP 500:

- 3064 — Proshuta Shqiptare – albanska pršuta
- 3068 — Vorarlberger Mostbröckle – vorarlberška dimljena govedina

PROFILE_NOT_RENDERED:

- 2101 — Vinkovački Slavonski Kulen – Stara obiteljska receptura

PUBLIC_GHOST_NO_RECIPE_RENDER:

- 3233 — Češnjovke 1.2021 – domaća varijanta
- 3234 — Kranjska 1.2021 – domaća varijanta
- 3236 — Pečena salamurena slanina s toplim dimom
- 3308 — Istarska kobasica
- 3309 — Rovinjska kobasica
- 3310 — Pazinska kobasica
- 3311 — Lička kobasica
- 3312 — Sinjska kobasica
- 3313 — Korčulanska kobasica
- 3314 — Hvarska prstena kobasica
- 3315 — Vrgorački kulen
