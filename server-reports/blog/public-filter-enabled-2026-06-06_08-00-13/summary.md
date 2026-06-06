# Drycured blog — javni filter uključen

Vrijeme: 2026-06-06_08-00-13

## Status

Javni blog filter na `/vodici/` je uključen preko WordPress opcije:

`drycured_blog_core_enabled = 1`

## Javna provjera

- HTTP /vodici/: 200
- Toolbar marker: 3
- Naslov filtera “Što želite saznati?”: 1
- Detaljni filtri: 1
- Marker “pronađenih članaka”: 1

## Napomena

Prethodna provjera tražila je stari tekst “Pronađite traženu temu”, pa je javila lažnu grešku iako je filter bio prikazan.
Ispravna javna provjera koristi aktualni naslov “Što želite saznati?”.
