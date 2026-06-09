# START HERE — DCV5 recipe master / drycured.com

Ovaj dokument je trajna početna točka za nastavak rada na receptnoj master bazi i postojećem Drycured/DCV5 prikazu recepata.

## Ključna odluka

- Ne razvijamo novi prikaz recepta.
- Ne razvijamo novi renderer.
- Ne mijenjamo postojeći dizajn koji već ispravno radi.
- Master baza služi kao podatkovni sloj za postojeći Drycured/DCV5 prikaz.

## Referentni ispravni javni prikazi

### HR-SL-005 — Slavonska domaća kobasica

- URL: `/recepti-baza/hr-sl-005-slavonska-domaca-kobasica/`
- WP post ID: 2976
- Status: publish
- Potvrđen kao ispravan referentni prikaz.

### HR-SL-010 — Slavonska kobasica (ZOI EU 2023)

- URL: `/recepti-baza/hr-sl-010-slavonska-kobasica-zoi-eu-2023/`
- WP post ID: 2981
- Status: publish
- Potvrđen kao drugi ispravan referentni prikaz, uz male dopuštene varijacije.

## Važno tehničko opažanje

Kod referentnih recepata `_dry_recipe_data` je prazan ili nije glavni izvor prikaza.

Prikaz se oslanja na postojeći DCV5 sustav, osobito na:

- `_dry_recipe_full_markdown`
- `_dry_recipe_sections`
- `_dry_verified_process`

HTML prikaz koristi postojeće klase:

- `dcv5-recipe`
- `dcv5-hero`
- `dcv5-timeline`
- `dcv5-panel`
- `dcv5-ingredient-card`
- `dcv5-safety-card`
- `dcv5-profile-row`
- `dcv5-serving-card`
- `dcv5-print-box`

## Pravilo za master bazu

Master baza nije novi layout. Ona mora proizvoditi podatke koje postojeći DCV5 renderer može ispravno prikazati.

Kod kobasica, salama i kulena postojeći prikaz uključuje: hero, radni sažetak, procesnu kronologiju, omjer smjese, glavne sirovine, začine, tekućine i češnjak, profil proizvoda, tehnološki potpis, anatomiju greške, Gotovo je kad, sigurnosni semafor, posluživanje i dnevnik šarže.

Kod cijelih komada isti vizualni sustav ostaje, ali blokovi moraju odgovarati tehnologiji cijelog komada.

## Za cijele komade prikazivati

- identitet proizvoda
- država, regija i mikroregija
- osnovna šarža 10 kg sirovine
- anatomski komad
- suhi pac, mokri pac, salamura ili marinada
- sol i začini
- utrljavanje, potapanje ili premazivanje
- trajanje i temperatura paca
- okretanje, prelaganje ili prešanje ako postoji
- ispiranje, brisanje ili prosušivanje ako postoji
- dimljenje ako postoji
- sušenje
- zrenje
- gubitak mase i znakovi gotovosti
- greške i rješenja
- sigurnosni semafor
- posluživanje i čuvanje
- dnevnik šarže

## Za cijele komade ne prikazivati

- mljevenje
- granulaciju
- rešetku ili šajbu
- crijeva
- nadjev
- miješanje nadjeva
- punjenje

Iznimka je dopuštena samo ako izvor izričito i tehnološki opravdano traži omotač ili poseban postupak.

## Trenutni Git status rada

Zadnji važni commitovi:

- `a825178 Add block fill candidates for whole-cut recipe master pilot`
- `f6496c9 Archive evidence matrix for whole-cut recipe master pilot`
- `8bc78ab Add recipe master pilot for whole-cut products`
- `4e6c090 Guard legacy recipe renderer for MD-only HTTP 500 posts`
- `99e4bdc Remove invalid MD bridge call from recipe JS profile`

## Važne datoteke

- `04_OPERATION/recipe_master/drycured_recipes_master_v1_pilot_cijeli_komadi_25.json`
- `04_OPERATION/recipe_master/drycured_recipes_master_v1_pilot_cijeli_komadi_25_summary.txt`
- `04_OPERATION/recipe_master/evidence/batch01_cijeli_komadi_25/editorial_evidence_matrix.csv`
- `04_OPERATION/recipe_master/evidence/batch01_cijeli_komadi_25/editorial_evidence_candidates.json`
- `04_OPERATION/recipe_master/fill_candidates/batch01_cijeli_komadi_25/block_fill_candidates.json`
- `server-reports/recipes/mass-pipeline-v2/source_input/drycured_recipes_clean_rebuild_v1_2.json`
- `scraping_recepata/schema/recipe_schema_web_v1_1.json`

## Pilot batch — cijeli komadi

U master pilot uspješno je povezano 25 javnih recepata skupine `CIJELI_KOMAD`.

Validacija:

- `RECORD_COUNT=25`
- `MISSING_MATCHES=0`
- `DUPLICATE_WARNINGS=0`
- `JSON_OK`
- `GROUPS={CIJELI_KOMAD:25}`
- `DUPLICATE_STABLE_IDS=0`
- `DUPLICATE_WP_IDS=0`
- `MASTER_PILOT_VALIDATION_OK`

## Što NE raditi dalje

- Ne nastavljati s izmišljanjem novog `editorial_draft` modela.
- Ne raditi novi renderer.
- Ne mijenjati javni izgled recepata.
- Ne dirati live recepte.
- Ne upisivati ništa u master dok ne postoji mapiran DCV5 data contract.

## Sljedeći ispravan korak

Mapirati aktivni DCV5 renderer i izraditi:

`04_OPERATION/recipe_master/DCV5_DATA_CONTRACT.md`

Treba dokumentirati koja datoteka generira postojeći DCV5 prikaz, koje funkcije čitaju `_dry_recipe_full_markdown`, `_dry_recipe_sections` i `_dry_verified_process`, te koji podatkovni format master baza mora proizvesti.

## Naredba za početak novog razgovora

```bash
cat /root/DRYCURED_GITHUB/04_OPERATION/recipe_master/00_START_HERE_DCV5_RECIPE_MASTER.md
```
