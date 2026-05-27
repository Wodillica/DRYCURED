# Drycured.com — Sitemap XML fix

Datum: 2026-05-27 19:16:05
Server: swab-production

## Što je riješeno

Sitemap endpointi više nisu blokirani Nginx statičkim pravilom za `.xml`.

Ranije je `/sitemap.xml` vraćao 404 jer je Nginx static-cache regex hvatao `.xml` i radio `try_files $uri =404` prije nego što zahtjev dođe do WordPress/AIOSEO sloja.

## Promjena

Iz Nginx static-cache regexa uklonjen je `xml`.

Datoteka:

- `/etc/nginx/snippets/drycured-static-cache.conf`

## Privremeni plugin

Privremeni MU-plugin:

- `wp-content/mu-plugins/drycured-sitemap-core.php`

uklonjen je jer nakon Nginx XML fixa AIOSEO sitemap ponovno radi.

## Potvrđeno

- `https://drycured.com/sitemap.xml` vraća 200 OK
- `https://drycured.com/page-sitemap.xml` vraća 200 OK
- `https://drycured.com/wp-sitemap.xml` preusmjerava na AIOSEO `sitemap.xml`
- `robots.txt` ponovno upućuje na `https://drycured.com/sitemap.xml`
- Home, sitemap, page-sitemap i Sušenje vraćaju 200 OK

## Napomena

`https://drycured.com/drycured-sitemap.xml` može vraćati 404/empty AIOSEO odgovor i više nije relevantan jer nije oglašen u robots.txt.

## Preostali manji zadatak

U `page-sitemap.xml` pojedinačne procesne stranice i alati postoje, ali `/proces-izrade/` trenutno nije pronađen u provjeri. To treba obraditi kasnije kao zaseban SEO cleanup, bez blokiranja ovog checkpointa.

## Rollback

```bash
cp "/root/drycured_reports/drycured_sitemap_nginx_xml_fix_v002_20260527_191352/drycured-static-cache.before.20260527_191352.conf" /etc/nginx/snippets/drycured-static-cache.conf
nginx -t && systemctl reload nginx
wp rewrite flush --hard --allow-root
wp cache flush --allow-root
```
