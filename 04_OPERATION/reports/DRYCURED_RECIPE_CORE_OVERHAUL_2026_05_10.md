# DRYCURED — recipe core overhaul 2026-05-10

## Sažetak

U glavni DRYCURED GitHub repo dodan je aktualni live export plugina:

04_OPERATION/wordpress_plugins/drycured-recipe-core

## Promjene

- 89 recepata u bazi
  - 4 Slavonija ZOZP
  - 10 cijelorezni
  - 61 HR komplet
  - 14 EU
- Cascade filter: regije filtriraju po odabranoj državi
- PHP server-side logika za regionalne filtere
- Popravljeni taxonomy termini s ispravnim nazivima
- Uklonjeni lažni numeric taxonomy termini
- JS wp_enqueue_script dodan u shortcodes.php
- Dupli naslovi skriveni na /recepti/ i /podcast/
- Custom SVG select arrows
- REST endpoint: /drycured/v1/filters/regions?country=slug
- Import pipeline koristi wp_set_object_terms
- 135 pokvarenih bulk-importa prebačeno je u draft status
- Verzija plugina usklađena na 0.2.23

## Status

Ovo je arhivski zapis live stanja plugina na serveru nakon današnjeg rada.
