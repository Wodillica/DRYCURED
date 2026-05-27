# Drycured.com — A-group SEO meta update

Datum: 2026-05-27 19:53:59
Server: swab-production

## Što je napravljeno

Ručno su postavljeni SEO title, meta description, OG title/description i Twitter title/description za glavne A-group stranice.

## Ažurirano kroz AIOSEO bazu

Ažurirana je tablica:

- `wp_aioseo_posts`

Za sljedeće stranice:

- Home
- Proces izrade
- Recepti
- Alati
- Kalkulator sušenja
- Planer dimljenja
- Praćenje pH
- Starter kulture
- Knjiga
- Podcast
- Atlas stilova Europe
- Greške i rješenja

## Archive rute

Tri URL-a nisu obične stranice nego CPT archive rute:

- `/savjeti/` → `tip_pusnice`
- `/infografike/` → `infografika`
- `/recepti-baza/` → `dry_recipe`

Za njih je dodan uski MU-plugin:

- `wp-content/mu-plugins/drycured-archive-seo-bridge.php`

Bridge normalizira samo title, description, OG, Twitter i canonical na ta tri URL-a.

## Potvrđeno

- PHP lint: PASS
- AIOSEO DB update: PASS
- Live meta za glavne stranice: PASS
- Archive meta za `/savjeti/`, `/infografike/`, `/recepti-baza/`: PASS
- Duplicate tag count: title=1, description=1, og:title=1, og:description=1
- Health: ključne stranice i sitemap vraćaju 200 OK

## Rollback

Za archive bridge:

```bash
rm -f /var/www/html/wp-content/mu-plugins/drycured-archive-seo-bridge.php
wp cache flush --allow-root
wp transient delete --all --allow-root
```

Za AIOSEO DB rollback koristiti:

```text
server-reports/drycured/seo-agroup/agroup_aioseo_rows_backup_20260527_193708.tsv
```

## Napomena

`/recepti-baza/` nije diran kao page-level AIOSEO unos jer je archive ruta.
