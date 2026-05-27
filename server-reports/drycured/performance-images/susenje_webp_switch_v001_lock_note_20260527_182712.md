# Drycured.com — Sušenje process image WebP switch v0.0.1

Datum: 2026-05-27 18:27:13
Server: swab-production

## Što je promijenjeno

Jedna procesna slika prebačena je s PNG na WebP:

- stari PNG: `wp-content/uploads/drycured/home-process/process-09-susenje.png`
- novi WebP: `wp-content/uploads/drycured/home-process/process-09-susenje.webp`

## Promijenjeno u kodu

- `wp-content/plugins/drycured-home-core/includes/process-rail.php`

Home process rail sada koristi WebP za fazu Sušenje.

## Promijenjeno u WordPress bazi

Stranica:

- ID 2875
- Naziv: Sušenje

Sadržaj stranice prebačen je s PNG reference na WebP referencu.

## Ušteda

- PNG: oko 2,5 MB
- WebP: oko 232 KB

## Potvrđeno

- Home javno prikazuje `process-09-susenje.webp`
- Stranica Sušenje javno prikazuje `process-09-susenje.webp`
- WebP vraća `Content-Type: image/webp`
- Cache header za WebP je aktivan
- Home i Sušenje vraćaju 200 OK
- Originalni PNG nije obrisan

## Rollback

```bash
cp "/root/drycured_reports/switch_susenje_webp_v001_20260527_182241/process-rail.before-susenje-webp.20260527_182241.php" /var/www/html/wp-content/plugins/drycured-home-core/includes/process-rail.php
wp post update 2875 --post_content="$(cat "/root/drycured_reports/switch_susenje_webp_v001_20260527_182241/page-2875-susenje.before-susenje-webp.20260527_182241.html")" --allow-root
wp cache flush --allow-root
systemctl reload php8.3-fpm
```

PNG se ne briše.
