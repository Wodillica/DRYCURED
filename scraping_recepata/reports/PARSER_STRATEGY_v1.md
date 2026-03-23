# PARSER STRATEGY v1

## Cilj

Definirati stabilan redoslijed ekstrakcije recepata s weba tako da sustav prvo koristi najstrukturiraniji izvor, a tek onda prelazi na fallback metode.

## Strategija po slojevima

### Sloj 1 — Strukturirani podaci
Prvo provjeriti postoji li:
- schema.org Recipe
- JSON-LD
- Microdata
- RDFa

Ako postoji jasan Recipe objekt, to je primarni izvor za naslov, sastojke, korake, vrijeme i kategorije.

### Sloj 2 — recipe-scrapers
Ako strukturirani markup nije potpun ili nije prisutan:
- pokušati ekstrakciju preko recipe-scrapers biblioteke
- bilježiti je li ekstrakcija potpuna ili djelomična

### Sloj 3 — Trafilatura
Ako prva dva sloja ne daju dobar rezultat:
- izvući glavni tekst stranice
- očistiti šum navigacije, footera i oglasa
- pokušati prepoznati blokove sastojaka i koraka

### Sloj 4 — Vlastiti parser
Ako prethodni slojevi nisu dovoljni:
- analizirati naslov
- tražiti listu sastojaka
- tražiti numerirane ili imperativne korake
- prepoznati procesne izraze: soljenje, dimljenje, fermentacija, zrenje, sušenje

## Bilježenje parsera
Za svaki obrađeni zapis treba zabilježiti:
- parser_used
- parser_fallback_level
- extraction_quality
- extraction_notes

## Preporučene oznake kvalitete
- structured_full
- structured_partial
- scraper_full
- scraper_partial
- trafilatura_partial
- custom_partial
- failed

## Posebna pravila za drycured projekt

### 1. Tehnološki tekst nije isto što i recept
Ako stranica opisuje samo proces bez količina i bez sastojaka, zapis ide u technical_reference ili suspect_incomplete, ovisno o kvaliteti sadržaja.

### 2. Češnjak, crijeva i procesni detalji
Ako parser prepozna:
- vrstu crijeva
- namakanje crijeva
- način primjene češnjaka
- faze dodavanja začina
te informacije treba posebno označiti jer su važne za kasniju normalizaciju.

### 3. Regionalni naziv
Ako postoji lokalni naziv proizvoda, čuvati:
- original_name
- normalized_name
- country_guess
- region_guess

## Minimalni izlaz parsera
Svaki parser treba pokušati vratiti barem:
- raw_title
- raw_ingredients
- raw_instructions
- source_url
- source_domain
- language_detected
- parser_used
- confidence_score

## Napomena

Parser strategija mora ostati hijerarhijska. Ne preskakati strukturirani sloj i ne kretati odmah na grubi tekstualni parser osim ako je to nužno.