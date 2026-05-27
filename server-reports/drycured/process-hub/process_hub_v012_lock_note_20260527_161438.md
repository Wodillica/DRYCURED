# Drycured.com — Process Hub v0.1.2 Lock Note

Datum: 2026-05-27 16:14:38
Server: swab-production

## Što je dodano

Ažuriran je read-only Process Hub:

`wp-content/mu-plugins/drycured-process-hub.php`

Dodana je administratorska stranica:

`Alati -> Drycured Process Hub`

## Svrha

Administrator može pregledati centralni registar procesa iz WordPress admina.

## Važno

Ova verzija ne mijenja javni frontend.

Ne dira:

- postojeće procesne stranice
- home vodič
- meni
- alate
- postojeće shortcodeove alata
- `the_content`
- javni HTML izlaz

## Provjere

- PHP lint: PASS
- Process Hub funkcije: PASS
- admin menu funkcija: PASS
- admin page funkcija: PASS
- process_count=12
- public debug output: nije javno vidljiv
- ključne HTTP provjere: PASS

## Rollback

```bash
cp "/root/drycured_reports/process_hub_v012_20260527_161327/drycured-process-hub.before-v012.20260527_161327.php" /var/www/html/wp-content/mu-plugins/drycured-process-hub.php
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeće raditi samo kao audit:

1. usporedba admin prikaza s home process railom
2. provjera redoslijeda home vodiča
3. tek kasnije plan povezivanja home vodiča s Process Hub registrom
