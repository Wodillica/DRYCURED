# Drycured.com — Process Hub v0.1.0 Lock Note

Datum: 2026-05-27 16:01:41
Server: swab-production

## Što je dodano

Dodan je read-only MU-plugin:

`wp-content/mu-plugins/drycured-process-hub.php`

## Svrha

Plugin služi kao centralni registar procesnih faza drycured.com sustava.

Registar sadrži:

- redoslijed procesa
- naziv procesa
- URL procesa
- hero sliku
- povezani alat, ako postoji
- prethodnu i sljedeću fazu
- status procesa
- kratki opis

## Važno

Ova verzija ne mijenja frontend.

Ne dira:

- postojeće procesne stranice
- meni
- alate
- shortcodeove postojećih alata
- `the_content`
- CSS
- JavaScript
- postojeći HTML izlaz

## Provjere

- PHP lint: PASS
- WP function check: PASS
- HTTP check ključnih stranica: PASS
- public debug output: nije javno vidljiv

## Rollback

```bash
rm -f /var/www/html/wp-content/mu-plugins/drycured-process-hub.php
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeće se smije raditi samo oprezno:

1. admin-only debug pregled registra
2. usporedba registra s postojećim home process rail prikazom
3. tek kasnije plan povezivanja home vodiča s registrom

Ne smije se odmah mijenjati postojeći home vodič.
