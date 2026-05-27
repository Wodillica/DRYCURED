# Drycured.com — MU-plugins Safe Cleanup Lock Note

Datum: 2026-05-27 15:38:13
Server: swab-production

## Što je napravljeno

Iz produkcijskog wp-content/mu-plugins root direktorija premješteni su samo:

- root _archive_* direktoriji
- root *.disabled* datoteke

Nisu dirani:

- aktivni root .php MU-pluginovi
- modules/ direktoriji
- procesni core pluginovi
- starter culture modules
- home process order datoteke

## Produkcijski backup

/root/drycured_reports/mu_plugins_safe_cleanup_20260527_153345/mu-plugins-before-cleanup_20260527_153345.tar.gz

## Produkcijska arhiva premještenih datoteka

/root/drycured_archives/mu_plugins_cleanup_20260527_153345

## Provjere nakon cleanup-a

- PHP lint aktivnih root MU-pluginova: PASS
- HTTP provjera ključnih stranica: PASS
- Root _archive_* direktoriji: uklonjeni iz mu-plugins root-a
- Root *.disabled* datoteke: uklonjene iz mu-plugins root-a

## Rollback

tar -xzf "/root/drycured_reports/mu_plugins_safe_cleanup_20260527_153345/mu-plugins-before-cleanup_20260527_153345.tar.gz" -C /var/www/html
wp cache flush --allow-root

## Pravilo za nastavak

Ne čistiti modules/ foldere i ne uklanjati stare datoteke iz produkcije bez prethodnog audita.
