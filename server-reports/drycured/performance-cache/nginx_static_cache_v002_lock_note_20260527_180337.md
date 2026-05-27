# Drycured.com — Nginx Static Cache + Gzip v0.0.2

Datum: 2026-05-27 18:03:38
Server: swab-production

## Što je napravljeno

Dodana je aktivna Nginx konfiguracija za statičke datoteke drycured.com:

- gzip za tekstualne statičke resurse
- 30 dana browser cache za CSS, JS, slike, SVG, fontove i slične statičke datoteke

## Promijenjene server datoteke

- /etc/nginx/sites-enabled/drycured
- /etc/nginx/sites-available/drycured
- /etc/nginx/snippets/drycured-gzip-types.conf
- /etc/nginx/snippets/drycured-static-cache.conf

## Potvrđeno

- nginx -t: PASS
- nginx reload: PASS
- HTML i dalje vraća 200 OK
- CSS dobiva Content-Encoding: gzip
- JS dobiva Content-Encoding: gzip
- CSS/JS/slike dobivaju Cache-Control: max-age=2592000
- slike dobivaju Expires header
- PHP/HTML stranice nisu stavljene u statički cache

## Važno

Ovaj zahvat ne mijenja WordPress sadržaj, Elementor, alate ni procesne stranice.

## Rollback

Backup je u server report mapi:

/root/drycured_reports/nginx_static_cache_fix_active_v002_20260527_180052

Rollback:

```bash
cp "/root/drycured_reports/nginx_static_cache_fix_active_v002_20260527_180052/drycured.sites-enabled.before.20260527_180052.conf" /etc/nginx/sites-enabled/drycured
cp "/root/drycured_reports/nginx_static_cache_fix_active_v002_20260527_180052/drycured.sites-available.before.20260527_180052.conf" /etc/nginx/sites-available/drycured
rm -f /etc/nginx/snippets/drycured-gzip-types.conf
rm -f /etc/nginx/snippets/drycured-static-cache.conf
nginx -t && systemctl reload nginx
```

## Sljedeći dopušteni korak

Nakon ovog checkpointa može se planirati:

1. optimizacija velikih slika,
2. SEO meta opisi za alatne stranice,
3. asset unload audit za home stranicu.
