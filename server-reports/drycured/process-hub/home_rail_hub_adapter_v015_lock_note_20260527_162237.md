# Drycured.com — Home Process Rail Hub Adapter v0.1.5 Lock Note

Datum: 2026-05-27 16:22:38
Server: swab-production

## Što je dodano

Dodan je isključeni adapter:

`wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php`

## Svrha

Adapter uspoređuje postojeći home process rail s Process Hub registrom.

## Važno

Adapter v0.1.5 ne mijenja javni prikaz.

Opcija ostaje isključena:

`drycured_home_process_rail_use_hub=0`

## Potvrđeno

- PHP lint: PASS
- adapter funkcije: PASS
- admin funkcije: PASS
- hub_item_count=12
- compare_ok=yes
- missing prazno
- public marker count=0
- dc-process-rail marker count=5
- svih 12 procesnih URL-ova prisutno je na home stranici

## Ne dira

- Elementor sadržaj
- home process rail render
- meni
- alate
- procesne stranice
- Process Hub registar

## Rollback

```bash
rm -f /var/www/html/wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php
wp option delete drycured_home_process_rail_use_hub --allow-root
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeći korak smije biti samo dodatni audit ili testni admin-only prikaz.
Javno uključivanje adaptera ne raditi bez zasebnog odobrenja i novog rollback plana.
