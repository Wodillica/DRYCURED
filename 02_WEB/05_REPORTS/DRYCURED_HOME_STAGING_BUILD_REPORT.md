# DRYCURED_HOME_STAGING_BUILD_REPORT

Datum: 2026-03-26  
Lokalni URL: `http://localhost:8085`  
Konačni status: `PASS WITH WARNING`

## 1. Postojeća Home logika

- Trenutni aktivni front page mode: `page`
- Trenutni aktivni front page ID: `101`
- Trenutni aktivni front page naziv: `Home`
- Trenutni aktivni front page slug: `pocetna`
- Postojeća Home stranica je uređivana u Elementoru: da
- Elementor status: aktivan
- Postojeća Home meta potvrđuje Elementor builder:
  - `_elementor_edit_mode=builder`
  - `_elementor_template_type=wp-page`
  - `_elementor_version=3.27.6`
- Astra layout meta na postojećem Home pageu:
  - `site-sidebar-layout=no-sidebar`
  - `ast-title-bar-display=disabled`

Zaključak:
- nije dirana postojeća aktivna naslovnica
- nije mijenjan `page_on_front`
- staging je napravljen kao zasebna nova Elementor stranica od nule

## 2. Staging stranica

- Naziv staging stranice: `DRYCURED Home Redesign Staging`
- Način izrade: nova stranica od nule, ne duplikat
- ID staging stranice: `1458`
- Slug: `home-staging`
- URL: `http://localhost:8085/home-staging/`

Elementor / Astra meta na staging stranici:
- `_elementor_edit_mode=builder`
- `_elementor_template_type=wp-page`
- `_elementor_version=3.27.6`
- `_wp_page_template=default`
- `site-sidebar-layout=no-sidebar`
- `ast-title-bar-display=disabled`

Napomena:
- aktivni front page je i dalje `101`
- staging stranica nije postavljena kao aktivni front page

## 3. Što je stvarno složeno

Stvarno složeni blokovi u točnom redoslijedu blueprinta:

1. `BLOK 1 — HERO`
2. `BLOK 2 — ŠTO ŽELIŠ RADITI`
3. `BLOK 3 — NOVO NA SAJTU`
4. `BLOK 4 — KLJUČNE TEME`
5. `BLOK 5 — ALATI`
6. `BLOK 6 — PROBLEMI`
7. `BLOK 7 — O PROJEKTU`

## 4. Što je potpuno gotovo

- zasebna staging Home stranica postoji lokalno
- stranica je Elementor page i ne koristi klasični editor kao glavni layout sloj
- zadržan je Astra + Elementor sklad
- skelet svih 7 glavnih blokova je postavljen
- CTA i kartice su dobili početnu link logiku prema postojećim lokalnim targetima gdje oni već postoje
- postojeća aktivna naslovnica nije prepisana
- staging stranica se otvara lokalno bez fatal errora

## 5. Što je placeholder za sljedeći korak

- Hero desna kolona: privremeni vizualni placeholder
- `Novo na sajtu`: placeholder container za budući dinamički latest posts blok
- `Alati`: vizualni alatni blok bez stvarnog tool engine wiringa
- `Problemi`: kartice vode na placeholder targete dok se ne spoji problem/troubleshooting sloj
- dio kartica u `Ključne teme` koristi placeholder targete jer kanonski target još nije definiran kao postojeća lokalna stranica
- language switcher nije implementiran u ovom koraku

## 6. Validacija

Provjereno:
- staging stranica otvara se na `http://localhost:8085/home-staging/`
- frontend response: `200`
- aktivni front page ostaje `101`
- Elementor editor ruta za staging stranicu ne daje fatal error
- neautentificirani zahtjev na editor rutu normalno završava na WordPress login stranici:
  - `http://localhost:8085/wp-login.php?redirect_to=http%3A%2F%2Flocalhost%3A8085%2Fwp-admin%2Fpost.php%3Fpost%3D1458%26action%3Delementor&reauth=1`
- nema miješanja sa live Home logikom jer `page_on_front` nije mijenjan

## 7. Warning

Status je `PASS WITH WARNING`, a ne čisti `PASS`, iz dva razloga:

- staging stranica je tehnički ispravna i dostupna, ali nije odrađena puna ručna vizualna revizija kroz prijavljeni Elementor editor session
- `Novo na sajtu`, dio tema, alatni sloj i problem blok namjerno su ostavljeni kao kontrolirani placeholderi za sljedeći korak dinamičkog povezivanja

## 8. Operativni zaključak

Lokalna staging Home stranica je stabilno pripremljena za sljedeću fazu:

- dinamički latest posts blok
- alatni blok
- problem blok
- language switcher sloj
