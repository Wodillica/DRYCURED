# DCV5 DATA CONTRACT — drycured.com recipe master

Status: v0.1 read-only draft  
Created UTC: 2026-06-09T16:56:32.652475+00:00  
Scope: postojeći javni Drycured/DCV5 prikaz recepata i buduća master baza recepata.

## 1. Temeljna odluka

Ne razvija se novi prikaz recepta i ne razvija se novi renderer.

Postojeći DCV5 prikaz ostaje referentni vizualni i funkcionalni standard. Master baza mora proizvoditi podatke koje postojeći DCV5 renderer može ispravno prikazati.

## 2. Referentni ispravni recepti

### HR-SL-005 — Slavonska domaća kobasica

- WP post ID: 2976
- URL slug: `hr-sl-005-slavonska-domaca-kobasica`
- Status: publish
- `_dry_recipe_data`: prazno
- `_dry_recipe_full_markdown`: popunjeno
- `_dry_recipe_sections`: popunjeno
- `_dry_verified_process`: popunjeno

### HR-SL-010 — Slavonska kobasica (ZOI EU 2023)

- WP post ID: 2981
- URL slug: `hr-sl-010-slavonska-kobasica-zoi-eu-2023`
- Status: publish
- `_dry_recipe_data`: prazno
- `_dry_recipe_full_markdown`: popunjeno
- `_dry_recipe_sections`: popunjeno
- `_dry_verified_process`: popunjeno

## 3. Aktivni slojevi prikaza

### 3.1 Glavni DCV5 renderer

Aktivna live datoteka:

`/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php`

Glavne funkcije koje treba tretirati kao postojeći renderer contract:

- `dc_canon_recipe_override_content`
- `dcv5_recipe_view_pilot_content`
- `dcv5_card`
- `dcv5_render_recipe_schema`
- `dcv5_supported_recipe_codes`
- `dcv5_get_recipe_profile`
- `dcv5_recipe_js_profile`

Glavni HTML marker prikaza:

- `dcv5-recipe`

Važni pomoćni HTML/CSS markeri:

- `dcv5-hero`
- `dcv5-timeline`
- `dcv5-panel`
- `dcv5-ingredient-card`
- `dcv5-safety-card`
- `dcv5-profile-row`
- `dcv5-serving-card`
- `dcv5-print-box`

### 3.2 Adapteri koji proširuju DCV5 profil

Aktivne ili relevantne live datoteke:

- `/var/www/html/wp-content/mu-plugins/drycured-verified-process-adapter.php`
- `/var/www/html/wp-content/mu-plugins/drycured-md-v5-bridge-pilot.php`
- `/var/www/html/wp-content/mu-plugins/drycured-batch001-profile-bridge.php`
- `/var/www/html/wp-content/mu-plugins/drycured-batch001-public-profile-bridge.php`
- `/var/www/html/wp-content/mu-plugins/drycured-product-gate-v1-1-clean-render.php`
- `/var/www/html/wp-content/mu-plugins/drycured-batch001-force-dcv5-template.php`

Ovi slojevi se ne smiju prepisivati naslijepo. Treba ih tretirati kao adaptere preko filtera i kao most između meta podataka i DCV5 profila.

## 4. Meta polja koja čine stvarni podatkovni izvor

### 4.1 `_dry_recipe_data`

Kod referentnih ispravnih recepata ovo polje je prazno. Ne smije se pretpostaviti da ono upravlja ispravnim DCV5 prikazom.

### 4.2 `_dry_recipe_full_markdown`

Sadrži puni markdown izvor recepta. Koristi se kao jedan od ulaza za most prema DCV5 profilu i/ili za fallback/kanonski tekst.

### 4.3 `_dry_recipe_sections`

Sadrži strukturirane sekcije izvedene iz markdowna. Koristi se kao bolji ulaz za blokove od sirovog markdowna.

### 4.4 `_dry_verified_process`

Sadrži ručno ili programski potvrđene procesne podatke. Za buduću master bazu ovo je najvažniji most prema sigurnom prikazu procesa, faza, kontrola i tehnoloških detalja.

## 5. Minimalni profil koji master baza mora proizvesti

Master baza mora biti sposobna proizvesti DCV5 profil koji postojeći renderer može prikazati bez novog layouta.

Minimalne kategorije profila:

- `identity`
- `hero`
- `summary`
- `timeline`
- `composition`
- `ingredients`
- `liquids_and_garlic`
- `product_profile`
- `climate_technology_signature`
- `errors_and_solutions`
- `done_when`
- `safety`
- `serving_storage`
- `batch_log`

Nazivi u master bazi mogu biti dodatno standardizirani, ali adapter mora mapirati te podatke u format koji `dcv5_get_recipe_profile()` i `dcv5_recipe_view_pilot_content()` već očekuju.

## 6. Posebni contract za cijele komade

Kod `product_group = CIJELI_KOMAD` isti DCV5 kartični sustav ostaje, ali sadržajni blokovi moraju biti drukčiji od kobasičnog modela.

### Prikazivati

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

### Ne prikazivati

- mljevenje
- granulaciju
- rešetku ili šajbu
- crijeva
- nadjev
- miješanje nadjeva
- punjenje

Iznimka je dopuštena samo ako izvor izričito i tehnološki opravdano traži omotač ili poseban postupak.

## 7. Dokazni materijali uz ovaj contract

Glavni read-only map report:

`04_OPERATION/recipe_master/dcv5_contract_evidence/dcv5_renderer_map_latest.txt`

Izvadci funkcija i aktivnih adaptera:

`04_OPERATION/recipe_master/dcv5_contract_evidence/source_extracts/`

Hook i meta mapa:

`04_OPERATION/recipe_master/dcv5_contract_evidence/source_extracts/dcv5_hook_and_meta_map.txt`

## 8. Pravilo za daljnji rad

Prije bilo kakvog upisa u master ili live prikaz:

1. Provjeriti postoji li odgovarajući DCV5 profil.
2. Provjeriti da adapter ne uvodi kobasične blokove kod cijelih komada.
3. Provjeriti da `_dry_recipe_data` nije pogrešno tretiran kao glavni izvor.
4. Provjeriti da se javni HTML i dalje prikazuje kroz `dcv5-recipe`.
5. Proći HTTP 200 test na referentnim receptima HR-SL-005 i HR-SL-010.

## 9. Status

Ovo je prvi data-contract nacrt. Ne aktivira produkcijsko čitanje master baze. Sljedeći korak je usporedba izvadaka funkcija i izrada adapter-specifikacije:

`MASTER_TO_DCV5_ADAPTER_SPEC.md`
