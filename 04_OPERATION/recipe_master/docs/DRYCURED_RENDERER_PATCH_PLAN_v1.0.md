# DRYCURED_RENDERER_PATCH_PLAN_v1.0

## Cilj

Popraviti renderer recepata tako da javni prikaz više ne ovisi o hardkodiranim fallback vrijednostima.

## Trenutni problem

Audit renderera pokazao je:

- 231 pogodak vezan uz fallback/meta/procesne prikaze
- 90 funkcija u rendereru
- 11 mjesta s `prema receptu`
- 5 mjesta s `hladna masa`
- 13 mjesta s `Punjenje`
- 47 mjesta vezanih uz `Crijeva / Omotač`
- 12 mjesta vezanih uz granulaciju
- 58 mjesta vezanih uz češnjak
- 30 default/fallback mjesta

To znači da ručno uređivanje jednog recepta ne rješava sustavni problem.

## Patch faze

### Faza 1 — read-only mapper

Napraviti skriptu koja za svaki `dry_recipe` post izvuče:

- ID
- title
- slug
- status
- postojeće `_dry_*` meta ključeve
- koji renderer/profil ga prikazuje
- ima li `_dry_verified_process`
- ima li `_dry_recipe_sections`
- ima li `_dry_recipe_full_markdown`
- ima li fallback riječi u renderiranom HTML-u

Bez ikakvih izmjena u bazi.

### Faza 2 — data-contract normalizer

Napraviti funkciju koja iz postojećih meta podataka sastavlja jedan standardni objekt:

`dry_recipe_contract_v1`

Polja:

- identity
- materials
- spices
- liquids
- garlic
- granulation
- casing
- process
- smoking
- drying
- maturation
- errors
- safety
- qa

### Faza 3 — renderer contract adapter

Renderer prvo pokušava čitati `dry_recipe_contract_v1`.

Ako ugovor postoji i QA je `PASS`, renderer prikazuje taj ugovor.

Ako ugovor ne postoji, recept ostaje u starom prikazu, ali ne dobiva `PUBLIC_VERIFIED`.

### Faza 4 — fallback zabrana

U javnom prikazu zabraniti:

- `prema receptu`
- `hladna masa`
- `Crijeva: prema receptu`
- `Rešetka: prema receptu`
- `Omotač: prema receptu`

Ako vrijednost nedostaje, blok se ne prikazuje ili recept ostaje u neprovjerenom statusu.

### Faza 5 — prvi pilot

Ponovno obraditi samo:

`2976 — Slavonska domaća kobasica`

Cilj pilota:

- stvoriti `dry_recipe_contract_v1`
- prikazati ispravnu granulaciju
- prikazati crijeva i namakanje
- ukloniti javno spominjanje izvora
- potvrditi HTML QA
- tek tada planirati javni update

### Faza 6 — batch pristup

Nakon pilota:

- batch 10
- batch 30
- batch 50

Bez ručnog HTML krpanja.

## Zabranjeno

Ne smije se:

- patchati svaki recept zasebno
- dodavati privremene override pluginove bez data-contracta
- javno objaviti recept koji ima fallback vrijednosti
- mijenjati postojeće URL-ove bez SEO plana
- dirati ZOI/EU recept 2981 dok se ne odvoji od domaće varijante

## Git pravilo

Svaki korak ide u zaseban commit:

1. data-contract dokument
2. read-only audit skripta
3. contract normalizer
4. renderer adapter
5. pilot contract za jedan recept
6. public update tek nakon QA
