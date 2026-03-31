# DRYCURED_LIVE_CONTENT_BLOCKERS_v1

Status: blockers mapa v1  
Projekt: drycured.com

---

## 1. Preostali blokeri

### B1 — nema live SQL dumpa

Prioritet: P1

Točan problem:

- nije pronađen lokalni `drycured_live.sql`
- nije pronađen drugi SQL dump live sadržaja

Posljedica:

- nema stvarne baze stranica, objava, menija, Home/page `101`, Elementor meta zapisa ni attachment relacija

Najmanji sljedeći korak:

- pribaviti novi live SQL export ili drugi valjani sadržajni DB artefakt

### B2 — uploads/media payload je prazan

Prioritet: P1

Točan problem:

- `imports/live_wp_content/wp-content/uploads` nema stvarne datoteke

Posljedica:

- nema lokalne media kopije
- nema vjerne vizualne rekonstrukcije live sadržaja

Najmanji sljedeći korak:

- ponovno izvesti ili pronaći stvarni uploads/media export

### B3 — nema lokalnog page `101` sadržajnog artefakta

Prioritet: P1

Točan problem:

- Home/page `101` nije prisutan u lokalnoj bazi
- nema lokalnog Elementor content zapisa za tu stranicu

Posljedica:

- ne postoji stvarni lokalni Home sadržaj

Najmanji sljedeći korak:

- DB import koji sadrži page `101`

### B4 — postojeći `wp-content` import je djelomičan

Prioritet: P2

Točan problem:

- theme/plugin payload iz starog importa nije bio dovoljno cjelovit za direktan runtime

Posljedica:

- filesystem sam po sebi nije dovoljan za live-copy obnovu

Najmanji sljedeći korak:

- koristiti ga samo kao pomoćni sadržajni sloj nakon što stigne stvarna baza

---

## 2. Što više nije blocker

- lokalni WordPress runtime
- `localhost:8085`
- `wp-admin`
- lokalna MariaDB baza kao runtime sloj
- Elementor baseline plugin

---

## 3. Zaključak

Glavni problem više nije infrastruktura nego nedostatak stvarnih live-content source-of-truth artefakata. Bez njih nema vjerne lokalne kopije i nema smisla prelaziti na stvarnu Home interaction copy izvedbu.
