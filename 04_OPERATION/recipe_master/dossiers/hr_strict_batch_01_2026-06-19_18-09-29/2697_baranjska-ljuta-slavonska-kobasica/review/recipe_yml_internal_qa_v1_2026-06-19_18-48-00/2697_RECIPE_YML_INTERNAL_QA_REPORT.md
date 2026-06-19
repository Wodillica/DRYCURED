# 2697 Baranjska kobasica – ljuta varijanta recipe.yml internal QA v1

Status: **RECIPE_YML_QA_PASS_READY_FOR_SECTIONS**

Ovaj korak ne mijenja WordPress. Provjerava je li `recipe.yml` dovoljno čist za generiranje strukturiranih sekcija i procesnog zapisa.

## Sažetak

- Post ID: `2697`
- Recipe code: `HR-BR-2697-BARANJSKA-LJUTA-KOBASICA`
- WordPress write allowed: `false`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Raw material total: `10.0 kg`
- Recipe type router: `GROUND_MEAT_OR_CASING`
- Major fail total: `0`
- Blocker fail total: `0`
- Ready for sections: `true`

## Kritične provjere

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| schema_version | PASS | BLOCKER | Schema mora biti drycured_recipe_yml_v1. |
| post_id | PASS | BLOCKER | Post ID mora biti 2697. |
| recipe_code | PASS | BLOCKER | Recipe code mora biti stabilan. |
| title_hr | PASS | MAJOR | Radni naslov mora biti urednički normaliziran. |
| title_review_required | PASS | MAJOR | Mora ostati označeno da je prije javne objave potrebna recenzija naslova. |
| country_region | PASS | BLOCKER | Recept mora biti hrvatski, regija Baranja/Slavonija. |
| recipe_type | PASS | BLOCKER | Baranjska kobasica mora biti GROUND_MEAT_OR_CASING. |
| canonical_confirmed | PASS | BLOCKER | Source validation mora biti CONFIRMED_RECIPE. |
| draft_status | PASS | BLOCKER | Draft status mora biti spreman za internal QA. |
| public_update_false | PASS | BLOCKER | Javni update mora biti false. |
| public_publish_false | PASS | BLOCKER | Javna objava mora biti false. |
| source_post_write_false | PASS | BLOCKER | Source post se ne smije mijenjati. |
| sources_present | PASS | BLOCKER | Moraju biti prisutna sva tri zaključana izvora. |
| sources_yml_contains_sources | PASS | BLOCKER | sources.yml mora sadržavati sve izvore. |
| no_protected_status_claim | PASS | MAJOR | Ne smije se tvrditi EU zaštićeni status. |
| raw_material_sum_10kg | PASS | BLOCKER | Ukupno sirovina mora biti 10 kg; dobiveno 10.0. |
| raw_material_two_groups | PASS | MAJOR | Radna formula treba jasno razlikovati meso i tvrdu slaninu. |
| meat_kg_scaled | PASS | BLOCKER | Meso mora biti skalirano na oko 9,09 kg. |
| fat_kg_scaled | PASS | BLOCKER | Tvrda slanina mora biti skalirana na oko 0,91 kg. |
| fat_handling_summary | PASS | MAJOR | Mora biti opisana obrada tvrde slanine/masnoće. |
| salt_190g | PASS | BLOCKER | Sol mora biti 190 g / 10 kg u ovom draftu. |
| salt_percent | PASS | BLOCKER | Sol mora biti 1,9 %. |
| sugar_25g | PASS | MAJOR | Šećer mora biti 25 g / 10 kg. |
| pepper_45g | PASS | MAJOR | Papar/biber mora biti 45 g / 10 kg. |
| sweet_paprika_120g | PASS | MAJOR | Slatka paprika mora biti 120 g / 10 kg. |
| hot_paprika_70g | PASS | MAJOR | Ljuta paprika mora biti 70 g / 10 kg. |
| paprika_total_190g | PASS | BLOCKER | Ukupna paprika mora biti 190 g / 10 kg. |
| paprika_percent | PASS | BLOCKER | Ukupna paprika mora biti 1,9 %. |
| garlic_28g | PASS | MAJOR | Pastozni češnjak mora biti 28 g / 10 kg. |
| garlic_percent | PASS | MAJOR | Češnjak mora biti 0,28 %. |
| nitrite_not_used | PASS | MAJOR | Nitritna sol nije uključena u bazni draft. |
| garlic_mode_paste | PASS | MAJOR | Češnjak mora biti pasta / fino zgnječeni češnjak. |
| garlic_paste_used | PASS | MAJOR | Mora biti označeno da se koristi pastozni češnjak. |
| garlic_liquid_false | PASS | MAJOR | Ne koristi se tekućina od češnjaka. |
| garlic_no_soaking | PASS | MAJOR | Ako nema tekućine od češnjaka, to mora biti eksplicitno navedeno. |
| pre_cut_present | PASS | MAJOR | Dimenzija rezanja prije mljevenja mora biti 20-30 mm. |
| grinding_6mm | PASS | BLOCKER | Mljevenje mora biti 6 mm. |
| meat_temperature | PASS | MAJOR | Temperatura mesa mora biti 0-4 °C. |
| mix_temp_max | PASS | MAJOR | Maksimalna temperatura smjese mora biti 8 °C. |
| fat_handling_detail | PASS | MAJOR | Mora biti detaljno opisana obrada tvrde slanine. |
| texture_goal | PASS | MAJOR | Mora biti cilj nerazmazane masnoće. |
| casing_type | PASS | BLOCKER | Ovitak mora biti tanka svinjska crijeva. |
| casing_length | PASS | MAJOR | Dužina komada mora biti 35-40 cm prema izvoru. |
| casing_caliber_guidance | PASS | MAJOR | Mora postojati radna smjernica kalibra. |
| soaking_required | PASS | MAJOR | Namakanje mora biti definirano. |
| soaking_liquid | PASS | MAJOR | Tekućina za namakanje mora biti pitka voda. |
| soaking_temperature | PASS | MAJOR | Temperatura namakanja mora biti 20-25 °C. |
| soaking_time | PASS | MAJOR | Vrijeme namakanja mora biti 30-45 min. |
| casing_not_boiled | PASS | MAJOR | Crijeva se ne prokuhavaju. |
| casing_rinsing | PASS | MAJOR | Mora biti navedeno ispiranje. |
| process_step_count | PASS | BLOCKER | Proces mora imati najmanje 10 faza. |
| process_has_odabir | PASS | MAJOR | Proces mora sadržavati fazu: odabir. |
| process_has_rezanje | PASS | MAJOR | Proces mora sadržavati fazu: rezanje. |
| process_has_mljevenje | PASS | MAJOR | Proces mora sadržavati fazu: mljevenje. |
| process_has_miješanje | PASS | MAJOR | Proces mora sadržavati fazu: miješanje. |
| process_has_crijeva | PASS | MAJOR | Proces mora sadržavati fazu: crijeva. |
| process_has_punjenje | PASS | MAJOR | Proces mora sadržavati fazu: punjenje. |
| process_has_odmor | PASS | MAJOR | Proces mora sadržavati fazu: odmor. |
| process_has_dimljenje | PASS | MAJOR | Proces mora sadržavati fazu: dimljenje. |
| process_has_zrenje | PASS | MAJOR | Proces mora sadržavati fazu: zrenje. |
| process_has_završna | PASS | MAJOR | Proces mora sadržavati fazu: završna. |
| smoking_3_4_by_6h | PASS | BLOCKER | Dimljenje mora imati 3-4 dima po oko 6 h. |
| smoking_alt_5_6 | PASS | MAJOR | Treba biti zabilježena i stručna usporedba 5-6 dimova svaki drugi dan. |
| maturation_25_30 | PASS | BLOCKER | Zrenje mora imati 25-30 dana. |
| thin_blue_smoke | PASS | MAJOR | Dimljenje mora spominjati tanak plavi dim. |
| done_when_count | PASS | MAJOR | Mora biti dovoljno kriterija gotovosti. |
| problem_solution_count | PASS | MAJOR | Mora biti najmanje 7 problema s rješenjima. |
| problem_solution_1 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_2 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_3 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_4 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_5 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_6 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| problem_solution_7 | PASS | MAJOR | Svaki problem mora imati problem, uzrok i konkretno rješenje. |
| not_for_frying | PASS | MAJOR | Mora pisati da se ne tretira kao kobasica za pečenje. |
| discard_suspicious | PASS | MAJOR | Sumnjiv proizvod se ne kuša i ne poslužuje. |
| active_blockers_present | PASS | MAJOR | Mora biti jasno što još blokira javni update. |
| recipe_yml_no_tabs | PASS | MAJOR | recipe.yml ne smije imati tab znakove. |
| recipe_yml_no_public_true | PASS | BLOCKER | recipe.yml ne smije imati public_update_allowed true. |

## Zaključak

`recipe.yml` je prošao internal QA. Sljedeći korak je generirati `_dry_recipe_sections` i `_dry_verified_process` za privatni preview, bez javnog WordPress updatea.
