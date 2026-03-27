# DRYCURED_HOME_TOOLS_BLOCK_REPORT

Datum: 2026-03-27  
Projekt: drycured.com  
Okruženje: lokalni Docker staging  
Staging stranica: ID 1458 / `http://localhost:8085/home-staging/`

---

## 1. Sažetak

Placeholder sekcija `Alati` na staging Home stranici pretvorena je u stvarni alatni ulazni blok s tri kartice.

Implementacija je napravljena isključivo na lokalnoj kopiji, bez promjene aktivnog front pagea i bez uvođenja novih pluginova.

---

## 2. Implementirane alatne kartice

Ugrađene kartice:

- `Kalkulator recepata`
- `Kalkulator soli`
- `Procesni helper`

---

## 3. Target logika

Stvarni target:

- `Kalkulator recepata` vodi na `http://localhost:8085/recepti/`

Status tog targeta:

- to je postojeća lokalna stranica
- radi i vraća HTTP 200
- trenutno služi kao kontrolirani privremeni target dok stvarni recipe calculator frontend nije vraćen

Placeholder / future targets:

- `Kalkulator soli` = `Uskoro`
- `Procesni helper` = `Uskoro`

Obje future kartice su namjerno prikazane kao planirani moduli, ali bez lažnog live linka.

---

## 4. Tehnička izvedba

Blok je tehnički izveden kroz:

- lokalni shortcode u Astra temi
- Elementor shortcode widget unutar sekcije `Alati` na staging stranici

Lokalne datoteke implementacije:

- `E:\SWAB_V2\website\drycured_local\wordpress\wp-content\themes\astra\inc\drycured-local-tools-block.php`
- `E:\SWAB_V2\website\drycured_local\wordpress\wp-content\themes\astra\functions.php`
- `E:\SWAB_V2\website\drycured_local\scripts\apply_tools_block_to_home_staging.php`

Napomena:

- pokušano je napraviti zaseban lokalni staging target za kalkulator recepata
- WordPress u ovoj importanoj kopiji vraćao je DB insert grešku za novu page kreaciju
- zato je usvojen manji rizik: postojeći `Recepti` URL kao kontrolirani privremeni target

---

## 5. Observe nalaz koji je utjecao na odluku

Potvrđeno stanje alatnog sloja:

- `WP Recipe Maker` plugin je aktivan
- nema nijednog `wprm_recipe` entiteta
- nije pronađen postojeći javni kalkulator recepata kao zasebna stranica, shortcode ili gotov frontend alat
- `Recepti` stranica postoji, ali trenutno je sadržajni listing, ne stvarni kalkulator

---

## 6. Validacija

Potvrđeno nakon implementacije:

- `http://localhost:8085/home-staging/` vraća HTTP 200
- sekcija `Alati` više nije placeholder
- novi uvod sekcije se prikazuje na frontend renderu
- kartice `Kalkulator recepata`, `Kalkulator soli` i `Procesni helper` se prikazuju
- `Kalkulator recepata` vodi na stvarni postojeći URL `http://localhost:8085/recepti/`
- `Kalkulator soli` i `Procesni helper` imaju kontrolirani `Uskoro` status
- layout se ne raspada na desktopu
- `wp-admin` i dalje normalno vodi na login ekran
- nema fatal errora

---

## 7. Refactor napomena prije live faze

Da, potreban je kasniji refactor prije live faze.

Razlog:

- lokalni tools shortcode sada je u parent Astra temi
- to je prihvatljivo za lokalni staging, ali nije stabilan dugoročni produkcijski sloj

Prije live faze kod treba preseliti u:

- child theme
ili
- stabilniji projektni custom modul

---

## 8. Konačni status

`PASS WITH WARNING`

Warning:

- stvarni recipe calculator frontend trenutno nije pronađen u importanoj lokalnoj kopiji
- glavni alatni CTA zato ide na postojeći `Recepti` URL kao kontrolirani privremeni target
- implementacija shortcoda je lokalna i nalazi se u parent Astra temi, pa traži refactor prije live prijenosa
