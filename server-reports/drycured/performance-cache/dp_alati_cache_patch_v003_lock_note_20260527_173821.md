# Drycured.com — Digitalna Pušnica Alati Cache Header Patch v0.0.3

Datum: 2026-05-27 17:38:22
Server: swab-production

## Što je promijenjeno

Patchan je plugin:

`wp-content/plugins/digitalna-pusnica-alati/digitalna-pusnica-alati.php`

## Problem

Plugin je slao:

`Cache-Control: no-cache, no-store, must-revalidate`

na sve javne WordPress stranice.

## Rješenje

No-cache header je ograničen na alatne stranice i stranice koje sadrže alatne shortcodeove.

## Potvrđeno

Nakon patcha:

- Home više nema prisilni no-cache header.
- Sušenje više nema prisilni no-cache header.
- Fermentacija više nema prisilni no-cache header.
- Kalkulator sušenja i Planer dimljenja i dalje smiju imati no-cache jer su interaktivni alati.

## Ne dirati

Untracked stavka `?? main` nije dodavana u commit.

## Rollback

```bash
cp "/root/drycured_reports/patch_dp_alati_cache_v003_20260527_172828/digitalna-pusnica-alati.before-cache-patch.20260527_172828.php" /var/www/html/wp-content/plugins/digitalna-pusnica-alati/digitalna-pusnica-alati.php
wp cache flush --allow-root
systemctl reload php8.3-fpm
```
