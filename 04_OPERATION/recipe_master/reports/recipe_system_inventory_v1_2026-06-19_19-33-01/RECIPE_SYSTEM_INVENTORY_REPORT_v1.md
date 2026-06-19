# Drycured recipe system inventory v1

Status: **READ_ONLY_INVENTORY_CREATED**

Ova inventura ne mijenja WordPress, ne mijenja javne recepte i ne mijenja sučelje prikaza recepata.

## Zaštita sučelja

- Sučelje prikaza recepata ostaje netaknuto.
- Renderer/MU-plugin datoteke su u ovoj inventuri samo pročitane i hashirane.
- Obustava prikaza recepata, ako kasnije bude potrebna, mora biti zaseban kontrolirani korak, ne dio inventure.

### Zaštićene UI datoteke

| Putanja | Postoji | Veličina | SHA256 | Status |
|---|---|---:|---|---|
| `/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php` | yes | 282701 | `238b2f0bd79c77df5f0ecaa5482fea1e284009c2ff5a799440c75577d575e901` | DO_NOT_MODIFY_IN_INVENTORY |
| `/var/www/html/wp-content/mu-plugins/drycured-granulation-display-core-safe-v11.php` | no |  | `` | EXPECTED_OR_OPTIONAL_FILE_NOT_FOUND |

## Sažetak datoteka

- Ukupno inventariziranih datoteka: `684`
- Ukupno inventariziranih skripti: `121`

| Kategorija datoteka | Broj |
|---|---:|
| DOSSIER_DATA | 90 |
| DOSSIER_MARKDOWN | 56 |
| OTHER | 187 |
| RECIPE_SOURCE_CANDIDATE_DATA | 45 |
| RECIPE_SOURCE_CANDIDATE_MD | 16 |
| RENDERER_UI_PROTECTED_SCRIPT | 39 |
| REPORT_DATA | 132 |
| REPORT_MARKDOWN | 37 |
| SCRIPT | 82 |

## Sažetak skripti

| Kategorija skripti | Broj |
|---|---:|
| READ_ONLY_OR_AUDIT_LIKELY | 22 |
| RENDERER_UI_PROTECTED | 39 |
| SCRIPT_UNKNOWN_REVIEW | 54 |
| WP_WRITE_CAPABLE | 6 |

### Skripte koje mogu pisati u WordPress

| Putanja | Write hitovi | Lokacija |
|---|---|---|
| `04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/backup_tool_before_comment_cleanup.php` | wp_insert_post; wp_update_post; update_post_meta | GENERATED_WORK_ARTIFACT |
| `04_OPERATION/recipe_master/tools/dc_recipe_1982_create_private_clone_v1.php` | wp_insert_post; update_post_meta | TOOL |
| `04_OPERATION/recipe_master/tools/dc_recipe_2697_create_private_clone_v1.php` | wp_insert_post; update_post_meta | TOOL |
| `04_OPERATION/recipe_master/tools/dc_recipe_3042_create_private_preview_clone_v1.php` | wp_insert_post; update_post_meta | TOOL |
| `04_OPERATION/recipe_master/tools/dc_recipe_3535_apply_meta_normalizer_patch_v1.php` | update_post_meta | TOOL |
| `04_OPERATION/recipe_master/tools/dc_recipe_system_file_inventory_v1.py` | wp_insert_post; wp_update_post; update_post_meta; delete_post_meta; wp_delete_post; wp_trash_post; set_post_thumbnail; wp_set_post_terms; wp_set_object_terms; wp db import; mysql ; insert into wp_; update wp_; delete from wp_ | TOOL |

### Skripte s DB write rizikom

Nema detektiranih DB write rizika u inventariziranom opsegu.

### Renderer/UI skripte pod zaštitom

| Putanja | Lokacija | SHA256 |
|---|---|---|
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_data_adapter_preview_2026-06-17_16-10-01.php` | OTHER_SCRIPT_LOCATION | `37d4ad76c5f49c6c218c8090bdf92ad9d75ff3d9f1b485d068a75079b30fd7a0` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv40_early_return_fix_2026-06-17_16-15-27.php` | OTHER_SCRIPT_LOCATION | `a959aea34cad507a8955fd383c1481a9f120df5af2cada284347366ee33d09b9` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv40_public_2976_2026-06-17_16-26-43.php` | OTHER_SCRIPT_LOCATION | `4f2cae19d40c1ae63cc30b54fb9f9a69dd749ac2006dada29bf15360c4754b88` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv40_quick_fix_2026-06-17_16-23-15.php` | OTHER_SCRIPT_LOCATION | `195c40cfb2df55177f1b183c518836a257a5f4216de325737003d18f0d02c5fc` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv40_sensor_profile_fix_2026-06-17_16-40-03.php` | OTHER_SCRIPT_LOCATION | `63738b60a092456e4335dbff2d95d2524faff351ac192fcbb1a8847365906c71` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv41_nitrite_safety_2026-06-17_16-56-19.php` | OTHER_SCRIPT_LOCATION | `dc78bbcf8de158293b787f282aabb65b1c79e4e1a5a07ab7ae876668b95d7542` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv42_phase_timeframes_2026-06-18_17-02-21.php` | OTHER_SCRIPT_LOCATION | `423ff9ce5acf9768ce52204a5e17b71bfd634ac15325365b326e254b0cd37bb0` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv43_visible_timeline_2026-06-18_17-11-01.php` | OTHER_SCRIPT_LOCATION | `399d3dd44e37585ee3547d2eae7cae53d9d119b3a92774632ebeb26fd7bea881` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_dcv44_smoking_cycle_duration_2026-06-18_17-18-00.php` | OTHER_SCRIPT_LOCATION | `a500d728e0cbfced0204de87edadbc417afbb9f12dfe258e9b9a32ba03dcaad9` |
| `04_OPERATION/recipe_master/backups/drycured-recipe-view-v1_before_nitrite_note_display_fix_2026-06-18_16-33-18.php` | OTHER_SCRIPT_LOCATION | `669325f9fa98774318aecc220592e40d6e89a1a062dcf2c1ec205964676d3f1e` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-canonical-recipe-view.php` | OTHER_SCRIPT_LOCATION | `550100a6b2829350868408e112b25eb6e63060549899f58bb76f54f519bd9663` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-balanced-stage.php` | OTHER_SCRIPT_LOCATION | `07cb399cd358c2dfdb677cf522d07eca83e24661c72734e817cf65036ef97908` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-card-polish.php` | OTHER_SCRIPT_LOCATION | `5a199daf28fb4570e73bfb9d0f72bd76a25d5960a88bd653f0f2c82e5c17c493` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-clarity-fix.php` | OTHER_SCRIPT_LOCATION | `609e3a0ef266d26c000f98ec0a514ddd1df5ffbbeb604e7839a1c23b0912a463` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-pilot.php` | OTHER_SCRIPT_LOCATION | `1731f2df68a7b6fcc5d09b73cd6c2ec0887b7da8ec189bd9da454063331e7e79` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-safety-marker.php` | OTHER_SCRIPT_LOCATION | `29d90e8f3441df2be7df44981bac97553d7b2846732b8db6cda60af9dde0e0a3` |
| `wp-content/mu-plugins/_archive_recipe_view_v1_2026-05-30_12-21-00/drycured-recipe-view-v05-sensory-polish.php` | OTHER_SCRIPT_LOCATION | `a9a4ac9687e148ce0ed1d53947c62a909266043df4d68714eb747874575b4641` |
| `wp-content/mu-plugins/drycured-recipe-view-v1.php` | OTHER_SCRIPT_LOCATION | `238b2f0bd79c77df5f0ecaa5482fea1e284009c2ff5a799440c75577d575e901` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_batch01_v1_2_2026-05-30_14-15-34.php` | OTHER_SCRIPT_LOCATION | `ece33f5317769e80e1022913d7c439ea7d63e082cd303da519f917f8701d953d` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_cure_model_lock_2026-05-30_17-51-16.php` | OTHER_SCRIPT_LOCATION | `cd8386c4244f39de4645140ef533ff8382621cf9465df884c967f267b55c7ff9` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_final_profile_override_2026-05-30_16-39-19.php` | OTHER_SCRIPT_LOCATION | `b23afec2dfda8702f21b85636a0371626ddc00d53a3e5c2735ccc951e8edec29` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_formula_only_0200L_2026-05-30_17-15-10.php` | OTHER_SCRIPT_LOCATION | `f93562f80e784fb147984fba261c9c814763c8deeab63ae1074980da26a2db28` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_garlic_default_0200L_2026-05-30_17-09-22.php` | OTHER_SCRIPT_LOCATION | `f93562f80e784fb147984fba261c9c814763c8deeab63ae1074980da26a2db28` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_garlic_default_0200L_safe_2026-05-30_17-13-08.php` | OTHER_SCRIPT_LOCATION | `f93562f80e784fb147984fba261c9c814763c8deeab63ae1074980da26a2db28` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_global_garlic_liquid_fix_2026-05-30_16-46-07.php` | OTHER_SCRIPT_LOCATION | `0883e861747eebfd414ee63fc2f8a117e2e917ad28b05ca8ea6dc0d5374ff6cb` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_global_quantities_2026-05-30_16-16-29.php` | OTHER_SCRIPT_LOCATION | `de76a76d34ce82e217761bb0983f29e5c6204c929f2bb716a321ba7d3a877c27` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_hr_sl_006_rum_runtime_fix_2026-05-30_16-35-33.php` | OTHER_SCRIPT_LOCATION | `33a232fa1952d1442734ffec805ab3646b23bce72f1960b7ea2d3d0087ed9313` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_hrsl001_late_source_render_2026-05-31_07-18-34.php` | OTHER_SCRIPT_LOCATION | `69d0a46228d7a017d8f43ecfaa6f5eec6952ec6d3d69a13f59e18729c2cfcccb` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_hrsl001_process_omotac_fix_2026-05-31_07-27-54.php` | OTHER_SCRIPT_LOCATION | `d23f1681775b2dfe172da8c9a807b106b9ae6d2ea449b646d6cd20c80853b457` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_hrsl001_process_omotac_fix_2026-05-31_07-36-09.php` | OTHER_SCRIPT_LOCATION | `a127f626fbc8da7a27af7c722376bb4171fe8cce2b97357ce7a20421c08e195f` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_ingredient_sanity_layer_2026-05-30_17-29-08.php` | OTHER_SCRIPT_LOCATION | `3450562cfaad12e171d7dcf0e9aeccb3888b404648085a9c6b36019fe8035910` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_no_liquid_fix_2026-05-30_16-27-08.php` | OTHER_SCRIPT_LOCATION | `f6bb781a13ecd5d8dc0c0ce16e0d9bb0a6c52c2f2e5c71c66a3dcf41399b562b` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_phase_specs_2026-05-30_13-56-44.php` | OTHER_SCRIPT_LOCATION | `44198a9c704eeee506206336d12f9941df7d6f7fd212b73b3b3feaa7382fb95b` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_quantity_parser_2026-05-30_16-13-26.php` | OTHER_SCRIPT_LOCATION | `86aa5e4db9cc5b468300f697655da8bd841e1ac5b53e78bba30b19beffa234c9` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_source_lock_hr_sl_001_2026-05-31_07-05-53.php` | OTHER_SCRIPT_LOCATION | `b539924b0c0b3a648c6a4a0136b33240262dd18659464f55fc8a439dc85e20ab` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_v1_1_2026-05-30_13-50-06.php` | OTHER_SCRIPT_LOCATION | `b05c44cf95f65bb1e3f61fcae175bf18ee60a95e1ff3b0a9a841cd5589c6d5ad` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_visual_lock_v1_2_2026-05-30_14-33-06.php` | OTHER_SCRIPT_LOCATION | `ece33f5317769e80e1022913d7c439ea7d63e082cd303da519f917f8701d953d` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_whole_piece_cure_model_2026-05-30_17-36-16.php` | OTHER_SCRIPT_LOCATION | `ac3dc79319744c9be316d0871cd1c15a05a44a3779328a8fe0dccc115b8b7e40` |
| `wp-content/mu-plugins/drycured-recipe-view-v1_before_whole_piece_late_cleanup_2026-05-30_17-40-05.php` | OTHER_SCRIPT_LOCATION | `01b5a4a1fa9746f6dc31fe28a0032ac22fd7a463ebe466baac731da1b71b3f7d` |

## WordPress dry_recipe inventura

- Ukupno dry_recipe postova: `940`
- Privatni preview cloneovi: `12`
- Javni publish recepti: `412`
- Private recepti: `148`
- Draft recepti: `288`
- Bez `_dry_recipe_sections`: `837`
- Bez `_dry_verified_process`: `913`
- Bez `_dry_recipe_full_markdown`: `393`

| Status | Broj |
|---|---:|
| draft | 288 |
| pending | 92 |
| private | 148 |
| publish | 412 |

## Procjena kaosa / preklapanja

- Postoji više slojeva podataka: reporti, dosjei, JSON/YML/MD i skripte.
- Pojedinačne skripte po receptu treba tretirati kao radne pokušaje, ne kao trajni proizvodni sustav.
- Glavni smjer treba biti: jedan standardni MD format → jedan parser → jedan QA izvještaj → jedan preview generator.
- Sve WP write-capable skripte treba staviti u karantenu dok se ne zaključi novi master workflow.

## Preporučena privremena klasifikacija

| Oznaka | Značenje | Radnja |
|---|---|---|
| ACTIVE / KEEP | Renderer i nužni read-only alati | Ne dirati bez dogovora |
| REVIEW | Skripte i dokumenti koji mogu biti korisni | Ručno pregledati prije uporabe |
| LEGACY / ARCHIVE | Stari pokušaji, batch reporti, privremeni dosjei | Premjestiti u `_legacy_archive` tek nakon potvrde |
| DANGEROUS / DO NOT RUN | Skripte s WP/DB write funkcijama | Ne pokretati dok nisu auditirane |
| UNKNOWN | Nejasni dokumenti i skripte | Ne koristiti u produkciji |

## Output datoteke

- `file_inventory.csv`
- `script_inventory.csv`
- `protected_ui_inventory.csv`
- `wp_recipe_posts_inventory.csv`
- `wp_recipe_meta_inventory.csv`
- `wp_recipe_inventory_summary.json`
- `file_inventory_summary.json`

## Sljedeći korak

Ne raditi više pojedinačne recepte. Sljedeći korak je pregledati ovaj inventarni izvještaj i zaključati: jednu izvornu mapu MD recepata, jedan kanonski MD format, jedan parser i jedan preview workflow.
