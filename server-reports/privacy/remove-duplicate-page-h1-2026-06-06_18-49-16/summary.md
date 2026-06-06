# Drycured remove duplicate page H1 v1

Vrijeme: 2026-06-06_18-49-16

## Problem

Servisne i pravne stranice imale su dvostruki naslov:

1. naslov stranice koji automatski prikazuje tema,
2. dodatni `<h1>` umetnut u sadržaj stranice.

## Rješenje

Iz sadržaja servisnih i pravnih stranica uklonjen je `<h1>`.

Tema i dalje prikazuje gornji naslov stranice.

## Obuhvaćene stranice

- /o-projektu/
- /kontakt/
- /sitemap/
- /prijavi-gresku/
- /sigurnosna-napomena/
- /uvjeti-koristenja/
- /politika-privatnosti/
- /politika-kolacica/

## QA

- sve stranice: 200
- sadržaj stranica: 0 H1 u post_content
- javni HTML: najviše 1 H1 po stranici

## Backup

`/root/drycured-privacy-backups/remove-duplicate-page-h1-2026-06-06_18-49-16`
