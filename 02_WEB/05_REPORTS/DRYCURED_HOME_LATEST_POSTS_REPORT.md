# DRYCURED_HOME_LATEST_POSTS_REPORT

Datum: 2026-03-27  
Projekt: drycured.com  
Okruženje: lokalni Docker staging  
Staging stranica: ID 1458 / `http://localhost:8085/home-staging/`

---

## 1. Sažetak

Placeholder blok "Novo na sajtu" na staging Home stranici pretvoren je u stvarni dinamički latest posts blok.

Implementacija je napravljena isključivo na lokalnoj kopiji, bez promjene aktivnog front pagea i bez uvođenja novih pluginova.

---

## 2. Odabrana metoda

Odabrana je varijanta:

- lokalni shortcode u postojećoj Astra temi
- Elementor shortcode widget unutar postojećeg bloka "Novo na sajtu"

Razlog:

- stabilno radi bez novih pluginova
- uklapa se u postojeći Elementor skelet
- najmanji je operativni rizik u odnosu na ručno slaganje Pro loop strukture kroz JSON

---

## 3. Gdje je implementirano

Lokalna implementacija napravljena je u:

- `E:\SWAB_V2\website\drycured_local\wordpress\wp-content\themes\astra\inc\drycured-local-latest-posts.php`
- `E:\SWAB_V2\website\drycured_local\wordpress\wp-content\themes\astra\functions.php`
- `E:\SWAB_V2\website\drycured_local\scripts\apply_latest_posts_to_home_staging.php`

Na staging Elementor stranici:

- stranica ID `1458`
- naziv `DRYCURED Home Redesign Staging`
- blok `Novo na sajtu`

---

## 4. Funkcionalni rezultat

Blok trenutno prikazuje:

- 3 najnovije objave
- naslov
- datum
- kratki excerpt
- link `Otvori objavu`

Featured images:

- ne prikazuju se
- to je svjesna odluka jer najnovije objave trenutno nemaju istaknute slike i tekstualni grid bolje čuva ritam i stabilnost bloka

---

## 5. Validacija

Potvrđeno nakon implementacije:

- `http://localhost:8085/home-staging/` vraća HTTP 200
- staging URL se normalno otvara
- blok više nije placeholder
- blok sadrži 3 dinamičke objave
- nema praznih kartica
- `http://localhost:8085/wp-admin/` i dalje normalno vodi na login ekran
- `home` = `http://localhost:8085`
- `siteurl` = `http://localhost:8085`
- aktivna tema = `astra`
- aktivni front page nije mijenjan

---

## 6. Napomene

Za konačan frontend refresh bilo je potrebno:

- flushati WordPress transiente i cache
- osvježiti Elementor frontend cache
- bumpati `post_modified` na staging stranici da frontend render definitivno povuče novi sadržaj

Benigna napomena iz lokalnog CLI okruženja:

- `wordpress:cli` i dalje ispisuje warning oko `/etc/nginx/conf.d/upload.conf`
- warning nije blokirao implementaciju ni validaciju

---

## 7. Konačni status

`PASS WITH WARNING`

Warning:

- implementacija je lokalno funkcionalna i stabilna, ali koristi lokalni theme-level shortcode fallback umjesto čistog native Elementor loop widgeta
- za produkcijski prijenos treba zadržati isti dizajn, ali kod smjestiti u održiviji sloj nakon odobrenja staging smjera
