# Renderer activation deep inspection v1

Status: **READ_ONLY_DEEP_INSPECTION_COMPLETE**

Ovaj korak ne mijenja WordPress. Cilj je razlikovati interni `apply_filters` snapshot od stvarnog javnog HTTP prikaza i pronaći vjerojatne uvjete aktivacije postojećeg renderera.

## Sažetak po postu

| Post | Status | HTTP | Filtered raw markdown | HTTP raw markdown | HTTP Drycured/recipe trag |
|---|---|---:|---|---|---|
| `2976` REFERENCE_PUBLIC_SLAVONSKA_DOMACA_KOBASICA | `publish` | `200` | `true` | `true` | `true` |
| `3042` SOURCE_PUBLIC_JESUS_DE_LYON | `publish` | `200` | `true` | `true` | `true` |
| `3535` PRIVATE_CLONE_JESUS_DE_LYON | `private` | `404` | `true` | `false` | `true` |

## Dijagnoza

- I interni i javni HTTP 2976 pokazuju raw markdown marker; moguće je da renderer koristi Markdown kao izvorni format ili da marker test nije dovoljan.
- Javni HTTP 2976 ima Drycured/recipe/DCV tragove; treba usporediti koji uvjet izostaje na 3535.
- Clone 3535 nema _dry_recipe_id; plugin na više mjesta referencira _dry_recipe_id i moguće je da je to minimalni trigger za kanonski renderer.
- Clone 3535 nema _dry_recipe_image_url; to možda nije blocker, ali plugin ga referencira za hero/sliku.
- Clone 3535 ima _dry_recipe_full_markdown, ali nema identifikacijski meta ključ _dry_recipe_id; sljedeći test treba biti meta-normalizer plan, ne promjena renderera.

## Plugin linije — najvažniji tragovi

- Repo plugin exists: `true`
- Live plugin exists: `true`
- Repo relevant lines: `391`
- Live relevant lines: `391`

- line `130`: `add_filter('the_content', 'dc_canon_recipe_override_content', 999);`
- line `142`: `$markdown = get_post_meta($post_id, '_dry_recipe_full_markdown', true);`
- line `149`: `$code        = dc_canon_meta($post_id, '_dry_recipe_id');`
- line `706`: `add_filter('the_content', 'dcv5_recipe_view_pilot_content', 1200);`
- line `714`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `741`: `$image_url = get_post_meta($post_id, '_dry_recipe_image_url', true);`
- line `1657`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `2925`: `add_filter('the_content', 'dcv6_feature_pack_content', 1250);`
- line `2933`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `3090`: `'key' => '_dry_recipe_id',`
- line `3204`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `3230`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `3491`: `$dcv6_footer_code = get_post_meta($dcv6_footer_post_id, '_dry_recipe_id', true);`
- line `3640`: `add_filter('the_content', 'dcv62_recipe_content_cleanup', 1400);`
- line `3648`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `3721`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `5111`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `5165`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `5267`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `5318`: `add_filter('the_content', 'dcv16_whole_piece_cure_content_cleanup', 1510);`
- line `6089`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `6151`: `add_filter('the_content', 'dcv17_whole_piece_late_content_cleanup', 9999);`
- line `6180`: `$markdown = $post_id ? get_post_meta($post_id, '_dry_recipe_full_markdown', true) : '';`
- line `6227`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `6347`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `6431`: `add_filter('the_content', 'dcv21_hrsl001_late_source_render_cleanup', 30000);`
- line `6443`: `$code = get_post_meta($post_id, '_dry_recipe_id', true);`
- line `6478`: `add_filter('the_content', 'dcv22_hrsl001_process_omotac_fix', 40000);`
- line `6495`: `foreach (['_dry_recipe_id', 'dry_recipe_id', '_recipe_id'] as $key) {`
- line `6671`: `$code = (string)get_post_meta($post_id, '_dry_recipe_id', true);`
- line `6700`: `add_filter('the_content', 'dcv31_hrsl001_final_public_cleanup', 50000);`

## Izlazne datoteke

- `renderer_activation_deep_inspection_v1.json`
- `plugin_relevant_lines_repo.json`
- `plugin_relevant_lines_live.json`
- `plugin_line_contexts.json`
- `2976_post_snapshot.json`
- `3042_post_snapshot.json`
- `3535_post_snapshot.json`

## Zaključak

Sljedeći korak treba biti minimalni **meta-normalizer plan** za privatni clone: ne mijenjati renderer, nego provjeriti koji meta ključevi nedostaju za aktivaciju postojećeg prikaza, osobito `_dry_recipe_id` i eventualno `_dry_recipe_image_url`.

Javni WordPress update i dalje nije dopušten.
