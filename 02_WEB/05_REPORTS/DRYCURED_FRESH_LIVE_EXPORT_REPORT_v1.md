# DRYCURED_FRESH_LIVE_EXPORT_REPORT_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Datum: 2026-03-31  
Jezik rada: hrvatski

---

## 1. Svrha

Ovaj dokument bilježi svježi live export izrađen za obnovu lokalne sadržajne kopije unutar `E:\SWAB_V2\website\drycured_local`.

---

## 2. Potvrđeni live origin

- WordPress root: `/var/www/html`
- `wp-content` root: `/var/www/html/wp-content`
- Nginx vhost: `/etc/nginx/sites-available/drycured`
- baza: `drycured`
- DB user: `drycured_user`
- tablični prefiks: `wp_`
- `home`: `https://drycured.com`
- `siteurl`: `https://drycured.com`
- `page_on_front`: `101`
- page `101` title: `Home`

---

## 3. Što je izvezeno

Izvezen je minimalni recovery set potreban za vjernu lokalnu obnovu:

- puni SQL dump relevantne live baze
- puni `wp-content` payload u jednoj arhivi

Lokalno sada postoje:

- `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live_fresh_2026-03-31.sql`
- `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\drycured_wp_content_fresh_2026-03-31.tar.gz`

---

## 4. Validacija exporta

Provjereno je sljedeće:

- SQL dump postoji i nije prazan
- SQL dump ima valjan MySQL dump header
- SQL dump sadrži drycured-specifične tragove uključujući `https://drycured.com`
- `wp-content` arhiva postoji i nije prazna
- `tar -tzf` prolazi bez očitog oštećenja arhive
- unutar arhive postoji `wp-content/uploads`
- unutar arhive postoje dated upload putanje (`2024`, `2025`, `2026`)

Zaključak:

> Svježi export set sada postoji lokalno i dovoljan je za sljedeći korak: retry live-content importa u bootable lokalni WordPress runtime.

---

## 5. Što je još warning, a nije blocker

- ovaj korak nije još radio stvarni lokalni import
- lokalna instanca još nije validirana kao puna live-content kopija u ovom koraku
- zato status ostaje `PASS WITH WARNING`, a ne puni `PASS`

---

## 6. Konačni status

Fresh live export je uspješno izrađen i lokalno spremljen.

Konačni status:

- `PASS WITH WARNING`

