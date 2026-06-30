# DRYCURED RECIPE RENDER MODEL — mljeveni trajni proizvodi u omotaču v1

Datum zaključavanja: 2026-06-30  
Referentni vizual: HR-IS-001 Istarska kobasica  
Pilot provjera: IT-CA-001 Soppressata Calabrese / Kalabrijska soppressata

## Zaključak

Za kobasice, salame, kulen, kulenovu seku, sudžuk i sve mljevene trajne proizvode u omotaču prihvatljiv je samo isti vizualni model kao kod Istarske kobasice.

Recept mora imati:

- kanonski `recipe_id`
- isti ID u `_dry_recipe_id`, `dry_recipe_code`, `recipe_id`, `source_lock_recipe_id`
- registraciju ID-a u `aaa-drycured-registry-01B.php`
- strukturirani `_dry_verified_process`
- noindex dok urednik ne potvrdi prikaz i sadržaj

Bez registry zapisa `dcv5_get_recipe_profile()` ne vraća profil i recept pada u stari fallback prikaz.

## Obavezno za javni recept

- sirovine u kg na 10 kg šarže
- začini u g, g/kg i %
- rešetka za mljevenje u mm
- obrada tvrde slanine ili masnoće
- crijevo/omotač: vrsta, promjer, namakanje, punjenje, izbijanje zraka, vezanje
- dimljenje: tip, ciklusi, trajanje, temperatura, pauze
- sušenje i zrenje: T, RH, trajanje, cilj gubitka mase
- gotovost: presjek, miris, tekstura, površina, gubitak mase
- problemi s konkretnim rješenjima

## Zabranjeno u javnom prikazu

- markdown fallback
- “Sadržaj recepta” sa starim MD tekstom
- “Nedostaje strukturirani mesni sastav”
- “Nedostaju strukturirani začini”
- “provjeriti”
- “recept ne smije u javnu objavu”
- riba/morski proizvodi kod mesnih recepata
- pogrešna tvrdnja da se crijeva ne koriste
- interni audit, source-lock, preview i radne napomene

## Status pilota 2060

Vizualni put je potvrđen nakon dodavanja `IT-CA-001` u registry.

Sadržaj 2060 još nije gotov jer treba dopuniti:
- javni prikaz sirovina
- javni prikaz začina
- promjer i obradu crijeva
- dimljenje i trajanje ciklusa
- završni QA

2060 ostaje `IN_REVIEW_VISUAL_CHECK` i noindex.
