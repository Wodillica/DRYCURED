# 1982 Finocchiona Toscana recipe.yml draft v1

Status: **RECIPE_YML_DRAFT_READY_INTERNAL_QA_REQUIRED**

Ovaj korak ne mijenja WordPress. Izrađuje radni `recipe.yml` za Finocchiona Toscana na temelju konsolidiranog službenog disciplinara iz 2024. i Drycured standarda.

## Sažetak

- Post ID: `1982`
- Recipe code: `IT-TOS-1982-FINOCCHIONA-TOSCANA`
- Batch: `10 kg`
- Type router: `GROUND_MEAT_OR_CASING`
- Public update allowed: `false`
- Public publish allowed: `false`
- Primary source: `SRC-1982-005`
- Raw material total: `10.0 kg`
- Blocker fail total: `0`

## Radna formulacija za 10 kg

| Sirovina | kg | Status |
|---|---:|---|
| svinjska lopatica, očišćena | 4.0 | allowed_cut_working_formula |
| obresci pršuta / nemasni butni obresci | 2.0 | allowed_cut_working_formula |
| nemasni dio pancete i podbradka | 1.5 | allowed_cut_working_formula |
| meso coppe / svinjski vrat | 1.0 | allowed_cut_working_formula |
| panceta / pancettone s čvršćom masnoćom | 1.5 | allowed_cut_working_formula |

## Začini i dodaci

| Sastojak | Količina | Službeni raspon / status |
|---|---:|---|
| sol | 280 g | 250-350 g / 10 kg |
| mljeveni crni papar | 8 g | 5-10 g / 10 kg |
| lomljeni papar / papar u zrnu | 25 g | 15-40 g / 10 kg |
| suhi češnjak | 8 g | 5-10 g / 10 kg |
| sjeme komorača ili cvijet komorača | 35 g | 20-50 g / 10 kg |
| crno/toskansko vino | 0,08 L | opcionalno, do 0,1 L / 10 kg |
| dekstroza ili saharoza | 30 g | opcionalno, do 100 g / 10 kg |

## Tehnološke odluke

- Mljevenje: `6 mm`, unutar službenog raspona `4,5-8 mm`.
- Češnjak: koristi se suhi češnjak izravno; ne koristi se tekućina od češnjaka.
- Crijeva: prirodni ili collato ovitak; za radni kućni preview navedena je smjernica većeg kalibra, uz namakanje 30-45 min u pitkoj vodi 20-25 °C, bez prokuhavanja.
- Nitriti/nitrati i starter kulture nisu uključeni u bazni draft; ako se kasnije uključe, traže posebnu tehnološku i sigurnosnu recenziju.
- Sušenje: 12-25 °C prema disciplinaru.
- Zrenje: 11-18 °C i 65-90 % RH.
- Minimalno trajanje: 15 / 21 / 45 dana prema težini pri punjenju.

## QA provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| raw_material_sum_10kg | PASS | BLOCKER | Ukupno sirovina: 10.0 kg. |
| salt_in_range | PASS | BLOCKER | Sol je unutar službenog raspona. |
| ground_pepper_in_range | PASS | BLOCKER | Mljeveni papar je unutar službenog raspona. |
| cracked_pepper_in_range | PASS | BLOCKER | Lomljeni papar je unutar službenog raspona. |
| garlic_in_range | PASS | BLOCKER | Suhi češnjak je unutar službenog raspona. |
| fennel_in_range | PASS | BLOCKER | Komorač je unutar službenog raspona. |
| wine_under_max | PASS | MAJOR | Vino je ispod službenog maksimuma. |
| sugar_under_max | PASS | MAJOR | Šećer je ispod službenog maksimuma. |
| grinding_range_ok | PASS | BLOCKER | Odabrana rešetka je unutar službenog raspona 4,5-8 mm. |
| casing_soaking_complete | PASS | MAJOR | Crijeva imaju definirano namakanje. |
| garlic_policy_complete | PASS | MAJOR | Češnjak je jasno definiran; nema tekućine od češnjaka. |
| problem_solution_complete | PASS | MAJOR | Problemi imaju konkretna rješenja. |
| public_update_blocked | PASS | BLOCKER | Javni update ostaje blokiran. |
| source_2024_primary | PASS | MAJOR | Primarni izvor za draft je konsolidirani 2024. dokument. |

## Otvoreno prije javnog updatea

- Internal QA recipe.yml još nije napravljen.
- Javni WordPress update nije dopušten.
- Potrebno je generirati _dry_recipe_sections i _dry_verified_process prije privatnog clone workflowa.
- Ako se kasnije uključe nitriti/nitrati ili starter kulture, potrebna je zasebna tehnološka i sigurnosna recenzija.

## Sljedeći korak

Pokrenuti internal QA za `recipe.yml`, zatim generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview. Javni WordPress update i dalje nije dopušten.
