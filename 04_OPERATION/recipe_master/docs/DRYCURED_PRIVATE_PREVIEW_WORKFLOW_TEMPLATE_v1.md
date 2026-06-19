# Drycured private preview workflow template v1

Status: **LOCKED_FROM_3042_3535_PILOT**

Ovaj dokument zaključava skraćeni tehnički workflow nastao iz pilota `3042 / 3535`.

## Kada se koristi

Koristi se za recepte koji nisu spremni za javnu objavu, ali trebaju siguran interni kartični pregled u postojećem Drycured prikazu.

## Stroge zabrane

- Ne mijenjati javni source post.
- Ne mijenjati javni title, slug, status ni URL.
- Ne mijenjati postojeći renderer.
- Ne objavljivati privatni clone.
- Ne dodavati bridge plugin ako direktni privatni URL radi za admin pregled.
- Ne spremati SQL backup u Git.
- Ne označavati recept kao public verified dok postoje sadržajne blokade.

## Skraćeni koraci

### 1. Dossier intake

Provjeriti postoji li dossier i osnovni source post.

Minimalno zabilježiti:

- source post ID
- title
- status
- URL
- tip recepta
- postojeće meta podatke
- javne/interne tragove
- status izvora

### 2. Source validation

Odlučiti status:

- `CONFIRMED_RECIPE`
- `CONFIRMED_PRODUCT_RECIPE_NOT_PUBLIC`
- `CANDIDATE_NEEDS_SOURCE`
- `DUPLICATE_MERGE`
- `REJECT_FALSE_OR_UNSUPPORTED`
- `EDITORIAL_RECONSTRUCTION_ALLOWED`
- `NOT_RECIPE`

Ako recept nije potvrđen iz javnog/kanonskog izvora, ne smije u javnu objavu.

### 3. recipe.yml draft

Izraditi radni `recipe.yml`.

Obvezno:

- 10 kg šarža
- glavne sirovine u kg
- začini u g i g/kg
- tekućine u L/ml
- granulacija u mm
- crijeva: tip, kalibar, namakanje, voda/tekućina, temperatura, vrijeme, prokuhano/ne
- češnjak: izravno, macerat, procijeđena tekućina ili nema
- procesne faze
- greške i konkretna rješenja
- aktivne blokade
- public update false

### 4. Internal QA

QA mora potvrditi:

- nema public update
- aktivne blokade su vidljive interno
- JSON/meta struktura je čitljiva
- nema javnog objavljivanja
- nema miješanja tipova recepta

### 5. Private clone

Smije se stvoriti samo private clone.

Obvezno:

- DB backup izvan Git repozitorija
- source post read-only
- clone post_status=private
- clone meta `_dry_recipe_preview_mode=PRIVATE_CLONE_ONLY`
- clone meta `_dry_recipe_preview_source_post_id=<source_id>`
- clone meta `_dry_recipe_public_update_allowed=0`
- clone meta `_dry_recipe_public_verified=0`
- clone meta `_dry_recipe_full_markdown`
- clone meta `_dry_recipe_sections`
- clone meta `_dry_verified_process`
- clone meta `_dry_recipe_id` ako je potreban za interni prikaz

### 6. Read-only QA clonea

Provjeriti:

- clone je private
- javni unauth URL nije izložen
- source nije mijenjan
- meta je valjana
- sadržaj je vidljiv
- public update je false

### 7. Manual admin preview

Ispravni admin pregled je direktni privatni URL dok je korisnik prijavljen:

`https://drycured.com/?post_type=dry_recipe&p=<clone_id>`

`preview=true` se ne tretira kao obvezan jer može vraćati 404.

### 8. Closure

Na kraju svaki pilot mora dobiti closure status:

- `PRIVATE_PREVIEW_READY_PUBLIC_UPDATE_BLOCKED`
- `PRIVATE_PREVIEW_READY_CONTENT_REVIEW_REQUIRED`
- `PUBLIC_READY_AFTER_QA`
- `REJECTED_OR_NEEDS_SOURCE`

## Zaključak

Za sljedeće recepte ne ponavljati puni istražni workflow. Koristiti ovaj skraćeni model:

source validation → recipe.yml → internal QA → private clone → read-only QA → manual admin preview → closure.
