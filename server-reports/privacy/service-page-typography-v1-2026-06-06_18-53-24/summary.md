# Drycured Service Page Typography v1

Vrijeme: 2026-06-06_18-53-24

## Problem

Na servisnim i pravnim stranicama podnaslovi u sadržaju bili su vizualno veći od glavnog naslova stranice.

## Rješenje

Dodan je MU-plugin `drycured-service-page-typography-v1.php`, koji samo na servisnim i pravnim stranicama podešava hijerarhiju:

- glavni naslov stranice ostaje najveći,
- H2 se smanjuje na prikladan podnaslov,
- H3 je manji od H2,
- tekst dobiva mirniji razmak i bolju čitljivost.

## Obuhvaćene stranice

- /politika-kolacica/
- /politika-privatnosti/
- /o-projektu/
- /kontakt/
- /prijavi-gresku/
- /sigurnosna-napomena/
- /uvjeti-koristenja/
- /sitemap/

## QA

- sve stranice: 200
- typography CSS marker: PASS
- svaka stranica ima točno 1 H1

## Napomena

Blog, recepti, početna stranica i ostali sadržaji nisu dirani.
