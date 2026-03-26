# DRYCURED_LIVE_ORIGIN_DISCOVERY_REPORT

Datum: 2026-03-26
Lokalni operator: Codex na Windows računalu
SSH pristup korišten za discovery: `root@178.104.92.245` preko ključa `C:\Users\Davor\.ssh\swab_v2_actions`

## 1. Sažetak zaključka

Potvrđeni stvarni live WordPress origin za `drycured.com` je:

- WordPress root: `/var/www/html`
- Nginx vhost: `/etc/nginx/sites-available/drycured`
- Document root: `/var/www/html`
- Web server: `nginx/1.24.0` + `php8.3-fpm`
- DB naziv: `drycured`
- DB user: `drycured_user`
- DB host: `localhost`
- Prefix tablica: `wp_`
- Tema: `astra`
- `home`: `https://drycured.com`
- `siteurl`: `https://drycured.com`

## 2. Svi pronađeni WordPress rootovi

### Kandidat A
- Root: `/var/www/html`
- `wp-content` postoji: da
- `wp-content/uploads` postoji: da
- Astra aktivna tema postoji: da, `wp-content/themes/astra`
- Najnoviji uploads tragovi:
  - `2026-03-26 19:08 wp-content/uploads/elementor/css/post-101.css`
  - `2026-03-25 16:17 wp-content/uploads/wpcode/cache/docs.json`
  - `2026-03-25 16:16 wp-content/uploads/wpcode/cache/library/snippets.json`
  - `2026-03-25 10:46 wp-content/uploads/elementor/css/post-1448.css`
- `wp-cli` identity:
  - `home`: `https://drycured.com`
  - `siteurl`: `https://drycured.com`
  - `template`: `astra`
  - `stylesheet`: `astra`
- DB config iz `wp-config.php`:
  - `DB_NAME=drycured`
  - `DB_USER=drycured_user`
  - `DB_HOST=localhost`
  - `TABLE_PREFIX=wp_`
- DB sadržaj potvrda:
  - objave iz ožujka 2026 postoje i objavljene su
  - postoji stvarni set attachmenta i stranica
  - primjeri: `Osnovna podjela suhomesnatih proizvoda`, `Uvod u svijet suhomesnatih proizvoda`, `Knjiga — Preview`

### Kandidat B
- Root: `/var/www/swab-multisite`
- `wp-content` postoji: da
- `wp-content/uploads` postoji: da
- Astra aktivna tema postoji: ne
- Najnoviji uploads tragovi: nema korisnog/svježeg sadržaja u izlazu
- `wp-cli` identity:
  - `home`: `http://swabtools.com`
  - `siteurl`: `http://swabtools.com`
  - `template`: `twentytwentyfive`
  - `stylesheet`: `twentytwentyfive`
- DB config iz `wp-config.php`:
  - `DB_NAME=swab_multisite`
  - `DB_USER=swab_ms_user`
  - `DB_HOST=localhost`
  - `TABLE_PREFIX=wp_`
- DB sadržaj potvrda:
  - samo generički sadržaj: `Hello world!`, `Sample Page`, `Privacy Policy`
  - nema obilježja live drycured sajta

## 3. Web server config nalazi

### `drycured.com` vhost
Datoteka:
- `/etc/nginx/sites-available/drycured`

Bitni nalazi:
- HTTP 80 preusmjerava na `https://drycured.com$request_uri`
- HTTPS 443 koristi:
  - `server_name drycured.com www.drycured.com;`
  - `root /var/www/html;`
  - `index index.php index.html;`
  - `try_files $uri $uri/ /index.php?$args;`
  - `fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;`

### `swab.drycured.com` vhost
Datoteka:
- `/etc/nginx/sites-available/swab`

Bitni nalazi:
- `server_name swab.drycured.com;`
- reverse proxy na `http://127.0.0.1:5000`
- to nije live WordPress origin za `drycured.com`

### Apache / LiteSpeed status
- Nije pronađen aktivan Apache vhost za `drycured.com`
- Nije pronađen LiteSpeed config kao aktivni origin za `drycured.com`
- Aktivni web sloj za live domenu je Nginx

## 4. HTTP probe nalazi

Izvedene provjere na serveru:
- `curl -I http://127.0.0.1` -> `301` na `https://drycured.com/`
- `curl -H "Host: drycured.com" -I http://127.0.0.1` -> `301` na `https://drycured.com/`
- `curl -4 -k -H "Host: drycured.com" -I https://127.0.0.1` -> `200 OK`
- `curl -k -I https://127.0.0.1` bez Host headera -> `302` na `https://drycured.com/wp-signup.php?new=127.0.0.1`

Tumačenje:
- host header je bitan za točan odgovor
- HTTPS lokalni odgovor s ispravnim Host headerom vraća očekivani WordPress sadržaj
- `wp-signup.php?new=127.0.0.1` bez Host headera nije dokaz drugog origina, nego posljedica WordPress/domain-based routinga

## 5. DNS / IPv4 / IPv6 napomena

Trenutni DNS zapis lokalno očitan:
- A: `178.104.92.245`
- AAAA: `2a02:4780:3f:1897:0:16a7:f564:5`

Napomena o neskladu:
- nije pronađen poseban IPv6-only origin niti zaseban vhost za IPv6
- iz dostupnog Nginx configa nema dokaza da IPv4 i IPv6 služe različite WordPress rootove
- jedina opažena razlika u probeovima vezana je uz prisutnost/odsutnost `Host` headera, ne uz različit origin

## 6. Zašto je odabran `/var/www/html`

Odabran je zato što istovremeno zadovoljava sve kriterije:
- točno odgovara `document root` iz aktivnog Nginx vhosta za `drycured.com`
- `home/siteurl` su točno `https://drycured.com`
- aktivna tema je `astra`, što odgovara očekivanom live sajtu
- `uploads` su svježi i aktivni do `2026-03-26`
- baza sadrži stvarni drycured sadržaj, objave i medije

## 7. Točne putanje i identitet potvrđenog origina

- WordPress root: `/var/www/html`
- `wp-config.php`: `/var/www/html/wp-config.php`
- `wp-content`: `/var/www/html/wp-content`
- `wp-content/uploads`: `/var/www/html/wp-content/uploads`
- Nginx vhost: `/etc/nginx/sites-available/drycured`
- SSL cert putanja: `/etc/letsencrypt/live/drycured.com/fullchain.pem`
- SQL baza: `drycured`
- DB user: `drycured_user`
- DB host: `localhost`
- Table prefix: `wp_`