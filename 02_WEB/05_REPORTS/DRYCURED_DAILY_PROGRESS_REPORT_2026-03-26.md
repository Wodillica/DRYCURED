# DRYCURED_DAILY_PROGRESS_REPORT_2026-03-26

Status dana: PASS WITH WARNING
Projekt: drycured.com
Datum: 2026-03-26

## 1. Što je danas napravljeno

Danas su odrađene četiri glavne operativne cjeline:

- forenzički discovery stvarnog live WordPress origina
- export potvrđenog live payloada
- lokalni import i validacija Docker kopije sajta
- izrada zasebne staging Home stranice kao Elementor skeleta za redesign

## 2. Potvrđeni live origin

Potvrđeni stvarni live origin je:

- WordPress root: `/var/www/html`
- Nginx vhost: `/etc/nginx/sites-available/drycured`
- document root: `/var/www/html`
- DB: `drycured`
- DB user: `drycured_user`
- prefix: `wp_`
- aktivna tema: `astra`
- `home/siteurl`: `https://drycured.com`

Napomena:
- pronađen je i drugi WordPress root na `/var/www/swab-multisite`, ali je isključen kao live origin jer odgovara `swabtools.com` i nema stvarni drycured sadržaj

## 3. Lokalni import i validacija

Live payload je prebačen u lokalni `drycured_local` i importan u Docker stack.

Potvrđeno nakon importa:

- frontend radi na `http://localhost:8085`
- `wp-admin` više nije na installeru nego normalno vodi na login ekran
- `home`: `http://localhost:8085`
- `siteurl`: `http://localhost:8085`
- aktivna tema: `astra`
- objave postoje
- mediji postoje i serviraju se lokalno
- nema beskonačnih redirectova na live domenu

## 4. Baseline checkpoint

Napravljen je lokalni baseline checkpoint prije Home redizajna:

- naziv checkpoint logike: `DRYCURED_LOCAL_BASELINE_PRE_HOME_REDESIGN`
- checkpoint dump: `E:\SWAB_V2\website\drycured_local\backups\DRYCURED_LOCAL_BASELINE_PRE_HOME_REDESIGN.sql`

Ova točka služi kao rollback osnova prije daljnjih Home izmjena.

## 5. Staging Home stranica

Kreirana je zasebna staging Home stranica bez mijenjanja aktivnog front pagea.

- naziv: `DRYCURED Home Redesign Staging`
- ID: `1458`
- slug: `home-staging`
- URL: `http://localhost:8085/home-staging/`

Staging stranica je složena kao Elementor skelet sa svih 7 traženih blokova, dok su latest posts, alatni i problem wiring ostavljeni kao kontrolirani placeholderi za sljedeći korak.

## 6. Današnji status

Konačni status dana: `PASS WITH WARNING`

## 7. Warningi

- lokalne import skripte su prvo tražile mali popravak PowerShell interpolacije i SQL quotinga prije uspješnog drugog izvođenja
- `wordpress:cli` image tijekom lokalnih `wp-cli` poziva ispisuje benigni warning vezan uz `/etc/nginx/conf.d/upload.conf`, ali nije blokirao rad
- staging Home skelet je stabilan, ali latest posts, alatni blok i problem blok još nisu dinamički spojeni

## 8. Sljedeći korak sutra

Sljedeći logični koraci su:

- latest posts blok
- alatni blok
- problem blok
- language switcher sloj