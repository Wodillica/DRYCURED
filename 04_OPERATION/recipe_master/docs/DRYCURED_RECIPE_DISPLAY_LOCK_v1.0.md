# DRYCURED_RECIPE_DISPLAY_LOCK_v1.0

## Status

Ovaj dokument zaključava pravilo prikaza web recepata na drycured.com.

## Glavno pravilo

Sadržajni i dizajnerski prikaz recepata već je dogovoren i ne smije se samostalno mijenjati.

Promjena prikaza može se dogoditi samo nakon zajedničkog dogovora i izričitog odobrenja.

## Što se ne smije mijenjati bez dogovora

- redoslijed blokova
- vizualni stil kartica
- širine, razmaci, boje, tipografija i hijerarhija prikaza
- sadržajni model javnog recepta
- postojeći dogovoreni raspored: hero, radni sažetak, procesna kronologija, sastojci, granulacija, crijeva, češnjak, greške i rješenja, sigurnost, dnevnik šarže i navigacija
- URL struktura i javni statusi recepata

## Dopušteno bez promjene dizajna

Dopušteno je razvijati podatkovni sloj koji puni postojeći prikaz:

- data-contract
- normalizer
- field extractor
- QA provjere
- data adapter za postojeći renderer
- uklanjanje netočnih fallback vrijednosti
- sprječavanje prikaza netočnih ili nepotpunih podataka

## Važno razlikovanje

Data-contract i adapter nisu novi renderer.

Njihova zadaća je isključivo dovesti točne podatke u postojeći dogovoreni prikaz.

## Zabranjeno

Zabranjeno je:

- uvoditi novi dizajn recepta
- mijenjati postojeći dogovoreni prikaz bez odobrenja
- prikazivati generičke vrijednosti kao javne podatke
- ručno krpati svaki recept HTML zamjenama
- masovno mijenjati javne recepte prije pilot QA prolaza

## Zaključak

Prvo se popravlja podatkovni sloj i povezivanje s postojećim prikazom. Dizajn i sadržajni raspored ostaju zaključani.
