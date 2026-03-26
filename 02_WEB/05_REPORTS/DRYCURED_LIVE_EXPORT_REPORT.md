# DRYCURED_LIVE_EXPORT_REPORT

Datum: 2026-03-26
Status: export napravljen iz potvrđenog live origina i payload kopiran u lokalne import mape

## 1. Odabrani live origin

- WordPress root: `/var/www/html`
- Domena: `https://drycured.com`
- DB: `drycured`
- DB user: `drycured_user`
- DB host: `localhost`
- Table prefix: `wp_`

## 2. Server-side export artefakti

Na serveru su izrađeni:
- SQL dump: `/tmp/drycured_live.sql`
- `wp-content` arhiva: `/tmp/drycured_wp_content.tar.gz`

Korištene naredbe:
- `mysqldump --default-character-set=utf8mb4 --single-transaction --quick --skip-lock-tables --no-tablespaces -h localhost -u drycured_user -p'***' drycured > /tmp/drycured_live.sql`
- `cd /var/www/html && tar -czf /tmp/drycured_wp_content.tar.gz wp-content`

## 3. Validacija exporta na serveru

Potvrđeno:
- `/tmp/drycured_live.sql` postoji
- `/tmp/drycured_wp_content.tar.gz` postoji
- `tar -tzf /tmp/drycured_wp_content.tar.gz | head -n 50` vraća očekivanu strukturu `wp-content/`

Veličine na serveru pri validaciji:
- SQL dump: `23M`
- `wp-content` arhiva: `1.9G`

Napomena:
- prvi pokušaj `mysqldump` prijavio je tablespace privilege upozorenje
- završni dump je napravljen ponovno s `--no-tablespaces` i završen je uredno

## 4. Lokalni payload pripremljen za import

Artefakti su kopirani u:
- `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live.sql`
- `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\drycured_wp_content.tar.gz`

`wp-content` je dodatno raspakiran u:
- `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\wp-content`

Lokalno potvrđene veličine:
- SQL dump: `24044297` bytes
- `wp-content` arhiva: `2024387431` bytes

Lokalno potvrđena struktura raspakiranog `wp-content`:
- `plugins`
- `themes`
- `uploads`
- `languages`
- `litespeed`
- dodatni backup/cache direktoriji iz live sajta

## 5. Je li export spreman za lokalni import?

Da.

Payload je spreman za lokalni import workflow jer postoje i SQL dump i puna kopija `wp-content` na očekivanim putanjama koje koristi lokalni `drycured_local` setup.

## 6. Sljedeći logični korak nakon ovog exporta

Bez diranja livea, lokalni import u `drycured_local` može ići ovim redom:
- `sync_wp_content_from_import.ps1`
- `import_live_db_to_local.ps1`
- `replace_local_site_urls.ps1`
- `reset_local_wordpress_cache.ps1`