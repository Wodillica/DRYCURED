# DRYCURED_LOCAL_WP_RUNTIME_BLOCKERS_v1

Status: blockers mapa v1  
Projekt: drycured.com

---

## 1. Preostali blokeri

### B1 — nema live SQL importa

Prioritet: P1

Stanje:

- lokalna baza sada postoji i radi
- ali nije live-content kopija

Točan nedostatak:

- nije pronađen lokalni live SQL dump
- nema WordPress tablica i sadržaja iz live sajta osim novog recovery baselinea

Najmanji sljedeći korak:

- osigurati novi export live baze ili drugi valjani lokalni DB artefakt

### B2 — originalni local theme/plugin payload bio je nepotpun

Prioritet: P1

Stanje:

- originalni `astra`, `elementor` i `elementor-pro` payload nisu bili upotrebljivi

Točan nedostatak:

- nedostajali su ključni runtime fajlovi

Najmanji sljedeći korak:

- po potrebi kasnije usporediti backup payload s radnim baselineom
- `elementor-pro` vratiti samo ako postoji cjelovit lokalni paket

### B3 — nema stvarnog media/attachment statea iz live baze

Prioritet: P2

Stanje:

- runtime radi
- ali media attachmenti nisu povezani kroz bazu

Najmanji sljedeći korak:

- tek nakon live DB importa provjeriti attachment zapise i media library stanje

### B4 — Home interaction copy još nije građena

Prioritet: P1

Stanje:

- recovery je vratio baseline
- ali interaction copy build još nije izveden

Najmanji sljedeći korak:

- actual local home interaction copy build v1
- ali tek na ovom novom bootable baselineu

---

## 2. Što više nije blocker

- `localhost:8085` više nije blocker
- `wp-admin` više nije blocker
- Docker runtime više nije blocker
- lokalni `wp-config.php` više nije blocker
- Elementor baseline više nije blocker

---

## 3. Zaključak

Glavni preostali problem više nije infrastruktura nego vjernost lokalne kopije u odnosu na live sadržaj. To je bitno jer sljedeći korak može krenuti, ali samo kao:

- interaction copy build na bootable baselineu

a ne kao potpuna rekonstrukcija live Home sadržaja.
