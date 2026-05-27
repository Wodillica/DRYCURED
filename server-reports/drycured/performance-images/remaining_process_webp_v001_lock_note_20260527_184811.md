# Drycured.com — Remaining process images WebP switch v0.0.1

Datum: 2026-05-27 18:48:28
Server: swab-production

## Što je napravljeno

Preostalih 11 procesnih PNG slika prebačeno je na WebP prikaz.

Sušenje je već ranije prebačeno u zasebnom checkpointu.

## Promijenjeno

Kod:

- `wp-content/plugins/drycured-home-core/includes/process-rail.php`
- `wp-content/mu-plugins/drycured-process-hub.php`

Nove WebP datoteke:

- `process-01-sirovina.webp`
- `process-02-soljenje.webp`
- `process-03-rezanje.webp`
- `process-04-mljevenje.webp`
- `process-05-mijesanje.webp`
- `process-05a-odlezavanje-smjese.webp`
- `process-06-punjenje.webp`
- `process-07-fermentacija.webp`
- `process-08-dimljenje.webp`
- `process-10-zrenje.webp`
- `process-11-pakiranje.webp`

WordPress stranice ažurirane:

- Sirovina
- Soljenje
- Rezanje
- Mljevenje
- Miješanje
- Odležavanje smjese
- Punjenje
- Fermentacija
- Dimljenje
- Zrenje
- Pakiranje

## Potvrđeno

- PHP lint: PASS
- Sve ažurirane stranice: png_after=0, webp_after=1
- Javni HTML: nema procesnih PNG referenci
- Svih 12 procesnih slika sada se javno prikazuje kao WebP
- Ključne stranice vraćaju 200 OK
- Originalni PNG-ovi nisu obrisani

## Ušteda

Audit je pokazao da je cijeli procesni PNG set bio oko 27,81 MB, a WebP test set oko 2,19 MB.

## Rollback

Backup datoteke nalaze se u:

`/root/drycured_reports/switch_remaining_process_webp_v001_20260527_183527/backups`

Rollback okvir:

```bash
cp "/root/drycured_reports/switch_remaining_process_webp_v001_20260527_183527/backups/process-rail.before-remaining-webp.20260527_183527.php" /var/www/html/wp-content/plugins/drycured-home-core/includes/process-rail.php
cp "/root/drycured_reports/switch_remaining_process_webp_v001_20260527_183527/backups/drycured-process-hub.before-remaining-webp.20260527_183527.php" /var/www/html/wp-content/mu-plugins/drycured-process-hub.php

# Za stranice vratiti pojedinačne backup HTML sadržaje iz:
# /root/drycured_reports/switch_remaining_process_webp_v001_20260527_183527/backups/page-*-before-remaining-webp.20260527_183527.html

wp cache flush --allow-root
systemctl reload php8.3-fpm
```

Originalni PNG-ovi se ne brišu.
