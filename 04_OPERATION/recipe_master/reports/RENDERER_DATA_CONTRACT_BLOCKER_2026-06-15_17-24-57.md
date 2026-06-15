# Renderer / data-contract blocker — drycured recepti

## Status

Pokušaj ručnog uređivanja preview recepta `3524 — Slavonska domaća kobasica` zaustavljen je prije javne objave.

Javni recept `2976 — Slavonska domaća kobasica` nije mijenjan.

## Zaključak

Problem nije pojedinačni recept nego trenutni renderer/data-layer.

Tijekom testa potvrđeno je da se dio javnog prikaza ne puni iz ažuriranih meta-podataka preview recepta, nego iz hardkodiranih ili fallback procesnih vrijednosti u aktivnom rendereru `drycured-recipe-view-v1.php`.

Primjeri problema:
- blok `Punjenje` ostaje na generičkoj vrijednosti `Crijeva: prema receptu`
- dio procesne kronologije dolazi iz fallback profila
- receptni meta-podaci mogu biti ispravljeni, ali javni prikaz i dalje ostaje netočan
- ručno krpanje jednog recepta ne skalira na 400+ recepata

## Odluka

Zaustavlja se uređivanje recept po recept dok se ne napravi stabilan recipe data-contract i renderer koji čita samo definirana strukturirana polja.

## Sljedeći obvezni korak

Izraditi `DRYCURED_RECIPE_DATA_CONTRACT_v1.0` i povezati renderer tako da svi recepti koriste isti podatkovni sloj:

- identitet recepta
- sirovine u kg
- začini u g i g/kg
- tekućine u L/ml
- češnjak: direktno / macerat / procijeđena tekućina
- granulacija: meso, slanina, rezanje, rešetka u mm
- crijeva: tip, kalibar, namakanje, tekućina, temperatura, vrijeme, prokuhavanje
- procesne faze
- dimljenje
- sušenje
- zrenje
- greške i rješenja
- sigurnosni semafor
- status izvora i QA status

## Zabranjeno do popravka

- masovno javno mijenjanje recepata
- oslanjanje na fallback tekst `prema receptu`
- ručno krpanje HTML-a pojedinačnih recepata
- javna objava bez QA prolaza renderiranog HTML-a
