# Drycured.com — Process Hub v0.1.1 Lock Note

Datum: 2026-05-27 16:11:39
Server: swab-production

## Što je napravljeno

Ažuriran je read-only Process Hub registar:

`wp-content/mu-plugins/drycured-process-hub.php`

## Promjena u odnosu na v0.1.0

- Fermentacija sada ima upisanu postojeću hero sliku.
- Audit je prebačen s krhkog TSV čitanja na stabilniji JSON audit.
- Potvrđena je prev/next logika svih 12 procesa.
- Potvrđeno je da sve slike i povezani alati vraćaju 200 OK.

## Važno

Ova verzija i dalje ne mijenja frontend.

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
- registry logic: PASS
- process URL checks: PASS
- image URL checks: PASS
- tool URL checks: PASS
- public debug output: nije javno vidljiv

## Rollback

```bash
cp "/root/drycured_reports/process_hub_v011_20260527_161036/drycured-process-hub.before-v011.20260527_161036.php" /var/www/html/wp-content/mu-plugins/drycured-process-hub.php
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeće se smije raditi samo kao audit ili admin-only prikaz:

1. admin-only debug pregled registra
2. usporedba Process Hub registra s home process rail prikazom
3. tek kasnije plan povezivanja home vodiča s registrom
