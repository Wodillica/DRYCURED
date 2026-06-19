# 2697 Baranjska kobasica – ljuta varijanta recipe.yml draft v1

Status: **RECIPE_YML_DRAFT_READY_INTERNAL_QA_REQUIRED**

Ovaj korak ne mijenja WordPress. Izrađuje radni `recipe.yml` za prvi hrvatski recept iz strict reda čekanja.

## Sažetak

- Post ID: `2697`
- Recipe code: `HR-BR-2697-BARANJSKA-LJUTA-KOBASICA`
- Radni naslov: `Baranjska kobasica – ljuta varijanta`
- Originalni WP naslov: `Baranjska Ljuta Slavonska Kobasica`
- Batch: `10 kg ukupne mesne smjese`
- Type router: `GROUND_MEAT_OR_CASING`
- Public update allowed: `false`
- Public publish allowed: `false`
- Raw material total: `10.0 kg`
- Blocker fail total: `0`

## Sirovine za 10 kg

| Sirovina | kg | Napomena |
|---|---:|---|
| svinjsko meso za kobasice: plećka, vrat, potrbušina/prsa bez kožica i žilavih dijelova | 9.09 | scaled_from_public_recipe_and_supported_by_expert_description |
| tvrda leđna slanina ili čvrsta bijela slanina, dobro ohlađena | 0.91 | scaled_from_public_recipe |

## Začini za 10 kg

| Sastojak | g | g/kg | Napomena |
|---|---:|---:|---|
| sol | 190 | 19.0 | unutar stručnog raspona 1,8-2 % |
| šećer | 25 | 2.5 | blisko skaliranom javnom receptu; pomaže početku fermentacije i zaokružuje ljutinu |
| crni papar / biber, mljeven ili grublje lomljen | 45 | 4.5 | blisko skaliranom javnom receptu |
| slatka mljevena paprika | 120 | 12.0 | radno uravnoteženje unutar ukupnog papričnog profila |
| ljuta mljevena paprika | 70 | 7.0 | ljuta varijanta; ukupna paprika 190 g / 10 kg |
| pastozni češnjak | 28 | 2.8 | usklađeno sa stručnim rasponom 0,2-0,3 % češnjaka |

## Izvorni recept skaliran na 10 kg — referenca

| Element | Vrijednost |
|---|---:|
| scale_factor | 0.90909 |
| source_total_meat_fat_kg | 11 |
| drycured_target_total_kg | 10 |
| svinjsko_meso_kg_scaled | 9.091 |
| tvrda_slanina_kg_scaled | 0.909 |
| sol_g_scaled | 181.8 |
| secer_g_scaled | 27.3 |
| biber_papar_g_scaled | 45.5 |
| slatka_paprika_g_scaled | 136.4 |
| ljuta_paprika_g_scaled | 90.9 |
| pastozni_cesnjak_g_scaled | 45.5 |

## Tehnološke odluke

- Mljevenje: `6 mm`.
- Češnjak: `28 g pastoznog češnjaka`, bez tekućine od češnjaka.
- Crijeva: tanka svinjska crijeva; namakanje `30-45 min` u pitkoj vodi `20-25 °C`, bez prokuhavanja.
- Dimljenje: `3-4 dima po oko 6 h tijekom tjedan dana`; dopuštena radna usporedba je `5-6 laganih dimova svaki drugi dan`.
- Zrenje: `25-30 dana`, hladno i prozračno.
- Nitritna sol: nije uključena u bazni draft.

## QA provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| raw_material_sum_10kg | PASS | BLOCKER | Ukupno sirovina: 10.0 kg. |
| meat_fat_ratio_scaled | PASS | BLOCKER | Omjer mesa i tvrde slanine skaliran je s 11 kg na 10 kg. |
| salt_range | PASS | BLOCKER | Sol mora biti u radnom rasponu 1,8-2,0 %. |
| paprika_range | PASS | MAJOR | Ukupna paprika u ovom radnom draftu drži se do 2 %. |
| garlic_range | PASS | MAJOR | Češnjak je u rasponu 0,2-0,3 %. |
| grinding_6mm | PASS | BLOCKER | Mljevenje mora biti 6 mm prema javnom receptnom zapisu. |
| casing_soaking_complete | PASS | MAJOR | Crijeva imaju namakanje, temperaturu, vrijeme i neprokuhavanje. |
| garlic_policy_complete | PASS | MAJOR | Češnjak je jasno definiran kao pasta, bez tekućine od češnjaka. |
| process_has_smoking | PASS | BLOCKER | Proces mora imati dimljenje. |
| process_has_maturation | PASS | BLOCKER | Proces mora imati sušenje/zrenje. |
| problem_solution_count | PASS | MAJOR | Problemi imaju konkretna rješenja. |
| public_update_blocked | PASS | BLOCKER | Javni update ostaje blokiran. |
| source_validation_confirmed | PASS | BLOCKER | Source validation mora biti CONFIRMED_RECIPE. |

## Otvoreno prije javnog updatea

- internal QA recipe.yml još nije napravljen
- javni WordPress update nije dopušten
- treba generirati _dry_recipe_sections i _dry_verified_process
- treba napraviti privatni preview clone prije bilo kakvog javnog updatea
- naslov treba uredničku normalizaciju prije javne objave

## Sljedeći korak

Pokrenuti internal QA za `recipe.yml`, zatim generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview. Javni update ostaje zabranjen.
