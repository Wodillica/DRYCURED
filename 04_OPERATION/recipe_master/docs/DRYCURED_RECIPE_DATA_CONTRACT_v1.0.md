# DRYCURED_RECIPE_DATA_CONTRACT_v1.0

## Svrha

Ovaj dokument definira jedinstveni podatkovni ugovor za sve recepte na drycured.com.

Cilj je spriječiti da renderer prikazuje generičke ili hardkodirane vrijednosti poput:

- prema receptu
- hladna masa
- crijeva prema receptu
- rešetka prema receptu
- generička češnjakova tekućina
- generičko punjenje bez stvarnog kalibra crijeva

Renderer smije prikazivati javni recept samo iz strukturiranog, provjerenog podatkovnog sloja.

## Glavno pravilo

Jedan recept mora imati jedan kanonski strukturirani zapis.

Javni prikaz ne smije izmišljati podatke. Ako podatak nije potvrđen, prikazuje se kao urednički nedostatan i recept ne smije dobiti status `PUBLIC_VERIFIED`.

## Zabranjeni javni fallbackovi

U javnom prikazu nisu dopuštene vrijednosti:

- `prema receptu`
- `hladna masa`
- `crijeva prema receptu`
- `rešetka prema receptu`
- `omotač prema receptu`
- `trajanje prema receptu`
- `češnjak prema receptu`
- `fotografija će biti dodana`
- `preview`
- `fallback`
- `source-lock`
- `audit`
- `radni zapis`
- `privatni radni recept`

Ako renderer nema podatak, ne smije ga zamijeniti generičkim tekstom. Mora označiti da podatak nedostaje u QA sloju, a ne u javnom prikazu.

## Minimalna struktura recepta

Svaki recept mora imati sljedeće grupe podataka.

### 1. Identitet

Obavezna polja:

- `recipe_id`
- `canonical_name`
- `country`
- `region`
- `microregion`
- `product_type`
- `technology_group`
- `batch_size_kg`
- `public_status`
- `qa_status`

### 2. Sirovine

Za svaku sirovinu:

- `name`
- `amount_kg`
- `percent`
- `cut_or_anatomical_part`
- `note`

Meso i masnoća uvijek se prikazuju u kilogramima za šaržu od 10 kg, osim kod cijelih komada gdje se jasno navodi masa komada.

### 3. Začini

Za svaki začin:

- `name`
- `amount_g`
- `g_per_kg`
- `percent`
- `note`

Začini se ne smiju prikazivati samo opisno ako je izvor dao količine.

### 4. Tekućine

Za svaku tekućinu:

- `name`
- `amount_l`
- `ml_per_kg`
- `temperature`
- `purpose`
- `note`

Ako nema tekućine, prikaz mora jasno reći da se tekućina ne koristi.

### 5. Češnjak

Češnjak mora imati jedan od statusa:

- `direct`
- `macerated_liquid`
- `strained_liquid`
- `not_used`
- `unknown_needs_review`

Obavezna polja ako se koristi:

- `garlic_amount_g`
- `liquid_type`
- `liquid_amount_l`
- `soaking_time_min`
- `boiled`
- `cooled`
- `strained`
- `added_to_mix_amount_l`
- `public_note`

Ako izvor kaže samo “češnjak u vodi”, ali ne daje količinu vode ili procjeđivanje, renderer ne smije automatski prikazati procijeđenu češnjakovu tekućinu.

### 6. Granulacija

Obavezna polja za kobasice i salame:

- `meat_grinder_plate_mm`
- `fat_grinder_plate_mm`
- `fat_cut_size_mm`
- `fat_hand_cut`
- `temperature_requirement`
- `control_note`
- `source_status`

Ako izvor ne navodi rešetku u mm, recept ne smije dobiti status `PUBLIC_VERIFIED` dok se taj podatak ne potvrdi ili dok se ne označi kao urednički nedostatan.

### 7. Crijeva / omotači

Obavezna polja za punjene proizvode:

- `casing_type`
- `diameter_mm`
- `soaking_required`
- `soaking_time_min`
- `soaking_liquid`
- `soaking_temperature_c`
- `water_changes`
- `boiled`
- `rinsing`
- `public_note`

Ako receptni izvor potvrđuje samo tip crijeva, a ne promjer, promjer se može prikazati samo ako je označen kao standardni radni kalibar za taj tip proizvoda. U javnom prikazu ne spominje se ime izvora.

### 8. Procesne faze

Svaka faza mora imati:

- `phase_id`
- `title`
- `day_or_period`
- `description`
- `duration`
- `temperature`
- `relative_humidity`
- `airflow_or_smoke`
- `goal`
- `critical_control`
- `problem`
- `solution`

Svaki problem mora imati konkretno rješenje.

### 9. Dimljenje

Ako se dimljenje koristi:

- `smoke_type`
- `wood_type`
- `cycles`
- `duration`
- `temperature`
- `pause_between_cycles`
- `critical_control`

Ako se dimljenje ne koristi, javni prikaz mora jasno reći da se ne koristi.

### 10. Sušenje i zrenje

Obavezna polja:

- `drying_duration`
- `drying_temperature`
- `drying_relative_humidity`
- `maturation_duration`
- `maturation_temperature`
- `mass_loss_target`
- `done_when`
- `critical_control`

### 11. Greške i rješenja

Svaka greška mora imati:

- `problem`
- `phase`
- `cause`
- `risk_level`
- `solution`
- `prevention`

### 12. Sigurnosni semafor

Obavezno:

- `green`
- `yellow`
- `red`

Svaka razina mora biti praktična i razumljiva korisniku.

### 13. Javni prikaz

Javni prikaz smije sadržavati samo:

- provjerene podatke
- urednički preoblikovan tekst
- praktične kontrole
- konkretna rješenja
- tehnološke parametre

Javni prikaz ne smije sadržavati:

- imena izvora
- interne statuse
- QA oznake
- debug tragove
- “preview” tekst
- “source-lock” tekst
- placeholder vrijednosti

## Statusi recepta

Dopušteni statusi:

- `LEGACY_PUBLIC_UNVERIFIED`
- `IN_REVIEW`
- `SOURCE_CONFIRMED`
- `CANON_DRAFT`
- `CANON_READY_PRIVATE`
- `PUBLIC_VERIFIED`
- `NEEDS_SOURCE`
- `REJECT_OR_ARCHIVE`

Javni recept može dobiti `PUBLIC_VERIFIED` samo ako nema zabranjenih fallbackova i ako HTML QA prođe.

## HTML QA prije javne objave

Prije javne objave mora proći QA nad stvarno renderiranim HTML-om.

Obavezne provjere:

- nema `prema receptu`
- nema `hladna masa`
- nema `fallback`
- nema `preview`
- nema `source-lock`
- granulacija prikazuje mm
- crijeva prikazuju tip i kalibar gdje je primjenjivo
- namakanje crijeva je prikazano gdje se koriste crijeva
- češnjak je klasificiran
- svaka faza ima cilj i kritičnu kontrolu
- svaka greška ima rješenje

## Renderer pravilo

Renderer ne smije sam stvarati tehnološke podatke.

Renderer smije:

- prikazati podatke iz data-contracta
- sakriti blok koji nema dovoljno podataka
- označiti recept kao neprovjeren u uredničkom sloju

Renderer ne smije:

- popuniti `prema receptu`
- popuniti staru granulaciju iz fallbacka
- popuniti crijeva iz generičkog profila
- spajati podatke iz drugog recepta
- prikazati interni izvor korisniku

## Zaključak

Ovaj data-contract je obvezan prije daljnje batch obrade recepata.

Dok renderer ne čita ovaj ugovor, ne smije se nastaviti javno uređivanje 400+ recepata.
