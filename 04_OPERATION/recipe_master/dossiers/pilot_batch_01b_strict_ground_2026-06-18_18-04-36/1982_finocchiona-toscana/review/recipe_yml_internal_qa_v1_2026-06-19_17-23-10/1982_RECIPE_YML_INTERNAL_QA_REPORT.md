# 1982 Finocchiona Toscana recipe.yml internal QA v1

Status: **RECIPE_YML_QA_PASS_READY_FOR_SECTIONS**

Ovaj korak ne mijenja WordPress. Provjerava je li `recipe.yml` dovoljno čist za generiranje strukturiranih sekcija i procesnog zapisa.

## Sažetak

- Post ID: `1982`
- Recipe code: `IT-TOS-1982-FINOCCHIONA-TOSCANA`
- WordPress write allowed: `false`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Raw material total: `10.0 kg`
- Primary source: `SRC-1982-005`
- Major fail total: `0`
- Blocker fail total: `0`
- Ready for sections: `true`

## Kritične provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| schema_version | PASS | BLOCKER | Schema mora biti drycured_recipe_yml_v1. |
| post_id | PASS | BLOCKER | Post ID mora biti 1982. |
| recipe_code | PASS | BLOCKER | Recipe code mora biti stabilan. |
| recipe_type | PASS | BLOCKER | Finocchiona je mljeveni proizvod u ovitku. |
| public_update_false | PASS | BLOCKER | Javni update mora biti false. |
| public_publish_false | PASS | BLOCKER | Javna objava mora biti false u ovoj fazi. |
| source_post_write_false | PASS | BLOCKER | Source post se ne smije mijenjati. |
| canonical_confirmed | PASS | MAJOR | Source validation je potvrdio službeni receptni okvir. |
| primary_source | PASS | BLOCKER | Primarni izvor mora biti konsolidirani 2024. disciplinar. |
| sources_contains_primary | PASS | BLOCKER | sources.yml mora sadržavati primarni izvor SRC-1982-005. |
| raw_total_10kg | PASS | BLOCKER | Ukupna sirovina mora biti 10 kg; dobiveno 10.0. |
| raw_items_count | PASS | MAJOR | Radna formulacija mora imati razrađene sirovinske skupine. |
| allowed_cuts_present | PASS | MAJOR | Mora postojati popis dopuštenih rezova iz disciplinara. |
| not_allowed_present | PASS | MAJOR | Mora biti navedeno što nije dopušteno. |
| mandatory_range_sol | PASS | BLOCKER | sol: 280; dopušteno 250-350 g / 10 kg. |
| g_per_kg_sol | PASS | MAJOR | sol: g/kg mora odgovarati 10 kg šarži. |
| mandatory_range_mljeveni_crni_papar | PASS | BLOCKER | mljeveni crni papar: 8; dopušteno 5-10 g / 10 kg. |
| g_per_kg_mljeveni_crni_papar | PASS | MAJOR | mljeveni crni papar: g/kg mora odgovarati 10 kg šarži. |
| mandatory_range_lomljeni_papar___papar_u_zrnu | PASS | BLOCKER | lomljeni papar / papar u zrnu: 25; dopušteno 15-40 g / 10 kg. |
| g_per_kg_lomljeni_papar___papar_u_zrnu | PASS | MAJOR | lomljeni papar / papar u zrnu: g/kg mora odgovarati 10 kg šarži. |
| mandatory_range_suhi_češnjak | PASS | BLOCKER | suhi češnjak: 8; dopušteno 5-10 g / 10 kg. |
| g_per_kg_suhi_češnjak | PASS | MAJOR | suhi češnjak: g/kg mora odgovarati 10 kg šarži. |
| mandatory_range_sjeme_komorača_ili_cvijet_komorača | PASS | BLOCKER | sjeme komorača ili cvijet komorača: 35; dopušteno 20-50 g / 10 kg. |
| g_per_kg_sjeme_komorača_ili_cvijet_komorača | PASS | MAJOR | sjeme komorača ili cvijet komorača: g/kg mora odgovarati 10 kg šarži. |
| wine_max | PASS | MAJOR | Vino mora biti ≤ 0,1 L / 10 kg. |
| sugar_max | PASS | MAJOR | Šećer mora biti ≤ 100 g / 10 kg. |
| starter_policy | PASS | MAJOR | Starter kultura mora biti jasno označena kao nedodana u bazni draft. |
| nitrite_policy | PASS | MAJOR | Nitriti/nitrati moraju imati sigurnosnu politiku ako se spominju. |
| garlic_mode | PASS | MAJOR | Češnjak mora biti jasno definiran kao suhi direktni češnjak. |
| garlic_liquid_false | PASS | MAJOR | Ne smije ostati nejasno koristi li se tekućina od češnjaka. |
| garlic_soaking_defined | PASS | MAJOR | Ako nema tekućine od češnjaka, to mora biti eksplicitno navedeno. |
| grinding_range_official | PASS | BLOCKER | Službeni raspon mljevenja mora biti 4,5-8 mm. |
| chosen_plate_ok | PASS | BLOCKER | Odabrana rešetka mora biti unutar službenog raspona. |
| pre_cut_present | PASS | MAJOR | Mora biti navedena dimenzija rezanja prije mljevenja. |
| fat_handling_present | PASS | MAJOR | Mora biti opisana obrada masnoće. |
| mix_temp_present | PASS | MAJOR | Mora biti naveden maksimum temperature smjese. |
| casing_official_present | PASS | MAJOR | Mora biti navedena službena vrsta ovitka. |
| casing_guidance_present | PASS | MAJOR | Mora biti navedena radna smjernica kalibra/ovitka. |
| casing_soaking_required | PASS | MAJOR | Namakanje crijeva mora biti definirano. |
| casing_soaking_liquid | PASS | MAJOR | Mora biti navedena tekućina za namakanje. |
| casing_soaking_temp | PASS | MAJOR | Mora biti navedena temperatura namakanja. |
| casing_soaking_time | PASS | MAJOR | Mora biti navedeno vrijeme namakanja. |
| casing_not_boiled | PASS | MAJOR | Mora biti jasno da se crijeva ne prokuhavaju. |
| casing_rinsing | PASS | MAJOR | Mora biti navedeno ispiranje. |
| process_odabir | PASS | MAJOR | Proces mora imati fazu: odabir. |
| process_rezanje | PASS | MAJOR | Proces mora imati fazu: rezanje. |
| process_mljevenje | PASS | MAJOR | Proces mora imati fazu: mljevenje. |
| process_miješanje | PASS | MAJOR | Proces mora imati fazu: miješanje. |
| process_punjenje | PASS | MAJOR | Proces mora imati fazu: punjenje. |
| process_sušenje | PASS | MAJOR | Proces mora imati fazu: sušenje. |
| process_zrenje | PASS | MAJOR | Proces mora imati fazu: zrenje. |
| process_završna_provjera | PASS | MAJOR | Proces mora imati fazu: završna provjera. |
| drying_params | PASS | BLOCKER | Sušenje mora imati parametar 12-25 °C. |
| ageing_params | PASS | BLOCKER | Zrenje mora imati 11-18 °C i 65-90 % RH. |
| minimum_duration | PASS | MAJOR | Minimalna trajanja 15/21/45 dana moraju biti navedena. |
| done_when_count | PASS | MAJOR | Mora biti dovoljno kriterija gotovosti. |
| problem_solution_count | PASS | MAJOR | Mora biti najmanje 5 problema s rješenjima. |
| problem_solution_1 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_2 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_3 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_4 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_5 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_6 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| active_blockers_present | PASS | MAJOR | Mora biti jasno što još blokira javni update. |
| serving_not_for_frying | PASS | MAJOR | Posluživanje mora jasno reći da se ne tretira kao kobasica za pečenje. |
| safety_discard_note | PASS | MAJOR | Mora postojati sigurnosna napomena za sumnjive proizvode. |
| recipe_yml_contains_no_tabs | PASS | MAJOR | YAML ne smije imati tab znakove. |
| recipe_yml_contains_10kg | PASS | MAJOR | recipe.yml mora sadržavati 10 kg šaržu. |
| recipe_yml_contains_no_public_true | PASS | BLOCKER | recipe.yml ne smije imati public_update_allowed true. |

## Zaključak

`recipe.yml` je prošao internal QA. Sljedeći korak je generirati `_dry_recipe_sections` i `_dry_verified_process` iz ovog zapisa, bez javnog WordPress updatea.
