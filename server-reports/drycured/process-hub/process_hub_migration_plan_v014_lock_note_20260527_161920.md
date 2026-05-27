# Drycured.com — Process Hub / Home Rail Migration Plan v0.1.4 Lock Note

Datum: 2026-05-27 16:19:20
Server: swab-production

## Što je napravljeno

Izrađen je migracijski plan za buduće povezivanje postojećeg home process raila s Process Hub registrom.

## Važno

Ovaj korak nije mijenjao produkciju.

Nije dirano:

- home process rail
- Process Hub plugin
- procesne stranice
- alatne stranice
- meni
- frontend prikaz
- Elementor sadržaj

## Zaključak plana

Home vodič se ne spaja odmah na Process Hub.

Prvo se smije izraditi samo isključeni adapter:

`drycured-home-process-rail-hub-adapter.php`

Adapter prema zadanim postavkama mora biti isključen:

`drycured_home_process_rail_use_hub=0`

## Rollback budućeg adaptera

Ako se adapter bude radio, rollback mora biti:

```bash
wp option update drycured_home_process_rail_use_hub 0 --allow-root
rm -f wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Tek nakon ovog Git checkpointa smije se izraditi isključeni adapter, bez promjene javnog prikaza.
