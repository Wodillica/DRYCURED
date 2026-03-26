# DRYCURED_LOCAL_BASELINE_CHECKPOINT

Naziv baseline točke: `DRYCURED_LOCAL_BASELINE_PRE_HOME_REDESIGN`
Datum: 2026-03-26
Status baseline: aktivan i zapisan nakon uspješnog lokalnog importa

## 1. Identitet baselinea

Ovo je `pre-home-redesign baseline` za lokalni staging primjerak drycured sajta prije bilo kakvih Elementor Home izmjena.

Lokalni URL:
- `http://localhost:8085`

Lokalna radna mapa:
- `E:\SWAB_V2\website\drycured_local`

Bitne putanje:
- WordPress datoteke: `E:\SWAB_V2\website\drycured_local\wordpress`
- lokalna baza kroz Docker servis: `drycured_local_db`
- import SQL payload: `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live.sql`
- import `wp-content` payload: `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\wp-content`

## 2. Stanje sustava u baseline točki

- Docker stack radi
- WordPress frontend radi na `http://localhost:8085`
- admin ruta radi i vodi na login ekran za neulogiranog korisnika
- `home` i `siteurl` su lokalizirani na `http://localhost:8085`
- aktivna tema: `astra`
- objave postoje
- mediji postoje i lokalno se serviraju
- import status: uspješan

## 3. Checkpoint artefakti

Lokalni DB backup checkpoint:
- `E:\SWAB_V2\website\drycured_local\backups\DRYCURED_LOCAL_BASELINE_PRE_HOME_REDESIGN.sql`

Veličina checkpoint SQL-a:
- `23397590` bytes

Napomena:
- backup je napravljen nakon završene import i URL replace sekvence, dakle predstavlja lokalni baseline prije Home redizajna

## 4. Spremnost za sljedeću fazu

Sustav je spreman za sljedeću fazu:
- Home redesign u Elementoru

Uvjetno upozorenje:
- prije većih vizualnih izmjena preporučljivo je zadržati ovaj baseline dump kao rollback točku
- nema potrebe dirati live server za sljedeću fazu jer lokalna kopija sada radi odvojeno