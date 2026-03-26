# DRYCURED_LOCAL_IMPORT_REPORT

Datum: 2026-03-26
Lokalna radna mapa: `E:\SWAB_V2\website\drycured_local`
Konačni status: `PASS WITH WARNING`

## 1. Payload validacija

Potvrđeno:
- SQL dump postoji: `E:\SWAB_V2\website\drycured_local\imports\live_db\drycured_live.sql`
- raspakirani `wp-content` postoji: `E:\SWAB_V2\website\drycured_local\imports\live_wp_content\wp-content`
- unutar `wp-content` postoje barem:
  - `plugins`
  - `themes`
  - `uploads`
- `drycured_wp_content.tar.gz` je ostavljen kao backup payload i nije korišten kao primarni izvor jer je raspakirani `wp-content` već postojao

## 2. Docker stack status

`docker compose ps` nakon importa:
- `wordpress`: `Up`
- `mariadb`: `Up (healthy)`
- `adminer`: `Up`

Servisi:
- WordPress: `http://localhost:8085`
- Adminer: `http://localhost:8086`

## 3. Import skripte i rezultat

Pokrenute su točno ovim redoslijedom:
1. `sync_wp_content_from_import.ps1`
2. `import_live_db_to_local.ps1`
3. `replace_local_site_urls.ps1`
4. `reset_local_wordpress_cache.ps1`

Konačni rezultat po skriptama:
- `sync_wp_content_from_import.ps1` — prošla
- `import_live_db_to_local.ps1` — prošla
- `replace_local_site_urls.ps1` — prošla
- `reset_local_wordpress_cache.ps1` — prošla

Napomena:
- prvi pokušaj importa otkrio je lokalne bugove u PowerShell skriptama (`E:` bind mount interpolacija i SQL quoting)
- skripte su popravljene unutar `drycured_local\scripts` i zatim je cijela sekvenca ponovljena istim redoslijedom do uspješnog završetka

## 4. Validacija lokalne kopije

### Frontend
- `http://localhost:8085` vraća `200`
- konačni URL ostaje `http://localhost:8085/`
- naslov stranice: `Home - drycured.com`
- homepage više nije na `install.php`
- homepage nije prazna
- sample asset URL iz `uploads` vraća `200`

### Admin
- `http://localhost:8085/wp-admin` nakon importa više nije na installeru
- nakon lokalnog DB upgrade koraka vodi na WordPress login:
  - `http://localhost:8085/wp-login.php?redirect_to=http%3A%2F%2Flocalhost%3A8085%2Fwp-admin%2F&reauth=1`
- to znači da je admin ruta živa i radi normalno za neulogiranog korisnika

### WordPress opcije nakon importa
- `home`: `http://localhost:8085`
- `siteurl`: `http://localhost:8085`
- `template`: `astra`
- `stylesheet`: `astra`

### Sadržaj
- objave postoje: `8` publish postova
- media attachment zapisi postoje: `30`
- `wp-content/uploads` se servira lokalno
- Astra ostaje aktivna
- Elementor sadržaj na homepageu se učitava i nije očito razbijen u početnoj provjeri

## 5. Redirect i sanity check

Potvrđeno:
- nema beskonačnih redirectova na live domenu
- sample homepage linkovi i asseti koriste `http://localhost:8085`
- homepage sadrži normalne lokalne URL-ove za teme, API i uploads

Napomene / warningi:
- `wordpress:cli` image tijekom `wp-cli` komandi ispisuje warning `sh: can't create /etc/nginx/conf.d/upload.conf: nonexistent directory`
  - to nije blokiralo `search-replace`, `cache flush`, `rewrite flush` ni `option get`
- attachment `guid` vrijednosti u bazi nisu mijenjane i dio njih ostaje na starijim apsolutnim URL-ovima
  - to je očekivano jer je `search-replace` namjerno rađen sa `--skip-columns=guid`
  - u frontendu su provjereni stvarni asset URL-ovi i oni se servira lokalno

## 6. Dodatni lokalni koraci za stabilizaciju nakon importa

Lokalno je dodatno odrađeno:
- `wp core update-db --allow-root`
  - razlog: nakon importa je `wp-admin` tražio DB upgrade
  - rezultat: admin ruta je nakon toga postala normalna login ruta umjesto upgrade blokera

## 7. Konačna procjena

Status: `PASS WITH WARNING`

Razlog za warning, a ne čisti `PASS`:
- import je uspješno završen i lokalna kopija radi
- ali lokalne import skripte su prvo trebale mali popravak prije uspješnog drugog izvođenja
- dodatno, `wp-cli` image daje benigni warning tijekom izvršavanja

Operativni zaključak:
- lokalna drycured kopija je podignuta i funkcionalna
- spremna je za sljedeću fazu nakon checkpointa: Home redesign u Elementoru