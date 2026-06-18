# qa_report.md

Status: **BLOCKED — DOSSIER_SCAFFOLD_ONLY**

Recept: **Jésus de Lyon – debela suha kobasica**

Post ID: `3042`

URL: `https://drycured.com/recepti-baza/jesus-de-lyon-debela-suha-kobasica/`

## QA-gate prije bilo kakvog javnog ažuriranja

- [ ] Izvor recepta potvrđen.
- [ ] Svi javni tekstovi su na hrvatskom.
- [ ] Recept je standardiziran na 10 kg mesa.
- [ ] Sirovine su navedene u kg.
- [ ] Začini su navedeni u g.
- [ ] Tekućine su navedene u L.
- [ ] Granulacija mesa ima rešetku u mm.
- [ ] Obrada slanine/masnoće ima rezanje u mm ili jasan opis.
- [ ] Crijeva/omotač imaju tip, promjer i namakanje.
- [ ] Češnjak je jasno označen: direktno ili procijeđena tekućina.
- [ ] Ako postoji tekućina od češnjaka, navedeni su količina češnjaka, tekućina, vrijeme, hladno/prokuhano i količina dodana u smjesu.
- [ ] Dimljenje, sušenje i zrenje imaju trajanje i parametre gdje su dostupni.
- [ ] Nitritna sol ima sigurnosnu napomenu ako se koristi.
- [ ] Svaki problem ima konkretno rješenje.
- [ ] Nema javnih internih oznaka: preview, fallback, source-lock, audit, adapter, debug.
- [ ] Ne mijenja se javni URL.
- [ ] Renderer se ne mijenja.

## Zaključak

Javni update nije dopušten. Dosje je tek otvoren i treba ručnu/kanonsku obradu.

<!-- DC_3042_SOURCE_VALIDATION_V1 -->

## Source validation v1

Status izvora: **PRODUCT_CONFIRMED_RECIPE_NOT_CANON_CONFIRMED**

Vanjski izvori potvrđuju da je Jésus de Lyon stvaran lyonški suhomesnati proizvod iz skupine suhih saucissona / suhih kobasica. Ne potvrđuju automatski sve količine i začine iz postojećeg WordPress recepta.

### Odluka

- [x] Proizvod postoji i ima vjerodostojan vanjski trag.
- [x] Tehnološka obitelj potvrđena je kao mljeveno/usitnjeno meso u omotaču.
- [ ] Kanonski recept još nije potvrđen.
- [ ] Javni update nije dopušten.
- [ ] Ne tvrditi aktualni IGP/ZOI/zaštićeni status bez dodatne potvrde.

Report: `review/source_validation_v1_2026-06-18_18-10-58/3042_SOURCE_VALIDATION_REPORT.md`

<!-- DC_3042_RECIPE_YML_DRAFT_V1 -->

## Recipe.yml draft v1

Status: **CANON_DRAFT_V1_NOT_PUBLIC**

- [x] `recipe.yml` je popunjen kao radni nacrt.
- [x] Šarža je 10 kg.
- [x] Sirovine su u kg.
- [x] Začini su u g.
- [x] Crijeva imaju namakanje.
- [x] Češnjak je označen kao sušeni češnjak u prahu, bez procijeđene tekućine.
- [x] Problemi imaju rješenja.
- [ ] Količina starter kulture nije tehnički potvrđena.
- [ ] Dimljenje nije potvrđeno kao obvezna faza.
- [ ] Javni update nije dopušten.
- [ ] Završni QA nije zatvoren.

Report: `review/recipe_yml_draft_v1_2026-06-18_18-15-54/3042_RECIPE_YML_DRAFT_V1_REPORT.md`

<!-- DC_3042_RECIPE_YML_QA_V1 -->

## Recipe.yml internal QA v1

Status: **BLOCKED_FOR_PUBLIC_UPDATE**

- Ukupno provjera: 25
- PASS: 25
- FAIL: 0
- Zbroj sirovina: 10.0 kg
- Privatni preview/adapter tehnički dopušten: `true`
- Javni update dopušten: `false`

### Aktivne blokade

- kanonski izvor za točne količine nije potvrđen
- količina starter kulture zahtijeva tehničku provjeru
- dimljenje je označeno kao needs_confirmation
- javni tekst još sadrži interne tragove prema intake izvještaju
- potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea

Report: `review/recipe_yml_qa_v1_2026-06-18_18-25-03/3042_RECIPE_YML_QA_V1_REPORT.md`

<!-- DC_3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN_V1 -->

## Private preview adapter dry-run v1

Status: **PRIVATE_PREVIEW_PAYLOAD_READY — PUBLIC_UPDATE_FORBIDDEN**

- Contract checks: 9
- Contract FAIL: 0
- Private preview payload ready: `true`
- Public update allowed: `false`

Report: `review/private_preview_adapter_dryrun_v1_2026-06-18_18-27-54/3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN_REPORT.md`
Payload: `review/private_preview_adapter_dryrun_v1_2026-06-18_18-27-54/3042_private_preview_adapter_payload.json`
HTML preview: `review/private_preview_adapter_dryrun_v1_2026-06-18_18-27-54/3042_PRIVATE_PREVIEW_ADAPTER_DRYRUN.html`

<!-- DC_3042_PRIVATE_WP_PREVIEW_PLAN_V1 -->

## Private WordPress preview plan dry-run v1

Status: **PLAN_ONLY — NO_WORDPRESS_WRITE**

- Source public post: `3042`
- Source post write allowed: `false`
- Future target: `PRIVATE_CLONE_ONLY`
- Planned meta keys: `13`
- Public update allowed: `false`

Report: `review/private_wp_preview_plan_v1_2026-06-18_18-45-14/3042_PRIVATE_WP_PREVIEW_PLAN_REPORT.md`
Meta map: `review/private_wp_preview_plan_v1_2026-06-18_18-45-14/3042_private_wp_preview_meta_map.json`
Checklist: `review/private_wp_preview_plan_v1_2026-06-18_18-45-14/3042_PRIVATE_WP_PREVIEW_EXECUTION_CHECKLIST.md`
