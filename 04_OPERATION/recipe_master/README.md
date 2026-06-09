# Drycured Recipe Master

Ova mapa sadrži početak jedinstvene master baze recepata za drycured.com.

Cilj master baze nije zamijeniti WordPress postove kao javne URL-ove, nego postati strukturirani izvor podataka iz kojeg plugin može ispravno prikazivati svaki recept prema skupini proizvoda.

## Pilot v1

Trenutni pilot:

- `drycured_recipes_master_v1_pilot_cijeli_komadi_25.json`
- skupina: `CIJELI_KOMAD`
- broj recepata: 25
- status: pilot, nije još live renderer
- izvor: `server-reports/recipes/mass-pipeline-v2/source_input/drycured_recipes_clean_rebuild_v1_2.json`
- batch izvoz: `server-reports/recipes/edit_batch01_cijeli_komadi_25_20260609_154333/`

## Pravilo prikaza za CIJELI_KOMAD

Plugin za ovu skupinu ne smije prikazivati:

- mljevenje
- granulaciju
- crijeva
- nadjev
- miješanje nadjeva
- punjenje

Plugin smije prikazivati samo blokove koji pripadaju cijelim komadima:

- identitet proizvoda
- država / regija
- osnovna šarža 10 kg
- anatomski komad
- suhi pac / salamura / marinada
- sol i začini
- utrljavanje / potapanje / premazivanje
- trajanje i temperatura paca
- okretanje / prelaganje / prešanje ako postoji
- dimljenje ako postoji
- sušenje
- zrenje
- gubitak mase / znakovi gotovosti
- greške i rješenja
- sigurnosni semafor
- posluživanje i čuvanje
- dnevnik šarže

## Važno

Ovaj pilot još nije konačno urednički popunjen. On postavlja stabilnu strukturu, povezuje WP postove sa stable_recipe_id vrijednostima i definira render_policy. Sljedeći korak je uredničko punjenje blokova.
