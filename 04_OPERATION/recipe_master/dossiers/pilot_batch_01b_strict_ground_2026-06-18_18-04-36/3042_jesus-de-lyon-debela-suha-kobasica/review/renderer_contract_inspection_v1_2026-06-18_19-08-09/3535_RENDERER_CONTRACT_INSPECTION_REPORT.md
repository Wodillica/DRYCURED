# 3535 renderer contract inspection v1

Status: **READ_ONLY_INSPECTION_COMPLETE**

Ovaj korak ne mijenja WordPress. Uspoređuje referentni javni recept `2976`, javni source `3042` i privatni clone `3535`.

## Sažetak rendera

| Post | Status | Type | HTML length | DCV marker | WPRM marker | Raw markdown |
|---|---|---|---:|---|---|---|
| `2976` REFERENCE_PUBLIC_SLAVONSKA_DOMACA_KOBASICA | `publish` | `dry_recipe` | `6689` | `false` | `false` | `true` |
| `3042` SOURCE_PUBLIC_JESUS_DE_LYON | `publish` | `dry_recipe` | `4677` | `false` | `false` | `true` |
| `3535` PRIVATE_CLONE_JESUS_DE_LYON | `private` | `dry_recipe` | `3277` | `false` | `false` | `true` |

## Dijagnoza

- Clone 3535 renderira raw markdown/post_content, ne dokazani kartični renderer.
- Clone ima _dry_recipe_sections, ali renderer se ne aktivira; vjerojatno treba dodatni trigger, status, shortcode, template uvjet ili drugačiji meta format.

## Plugin scan

- Repo plugin exists: `true`
- Live plugin exists: `true`
- Repo plugin bytes: `282701`
- Live plugin bytes: `282701`

## Mogući renderer triggeri / meta reference

- `/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php:142: $markdown = get_post_meta($post_id, '_dry_recipe_full_markdown', true);`
- `/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php:149: $code        = dc_canon_meta($post_id, '_dry_recipe_id');`
- `/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php:714: $code = get_post_meta($post_id, '_dry_recipe_id', true);`
- `/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php:741: $image_url = get_post_meta($post_id, '_dry_recipe_image_url', true);`
- `/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php:142: $markdown = get_post_meta($post_id, '_dry_recipe_full_markdown', true);`
- `/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php:149: $code        = dc_canon_meta($post_id, '_dry_recipe_id');`
- `/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php:714: $code = get_post_meta($post_id, '_dry_recipe_id', true);`
- `/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php:741: $image_url = get_post_meta($post_id, '_dry_recipe_image_url', true);`

## Meta matrix

Detaljna meta matrix spremljena je u `3535_renderer_meta_matrix.csv`.

## Zaključak

Privatni clone je siguran, ali treba utvrditi točan uvjet aktivacije postojećeg Drycured/DCV renderera. Sljedeći korak ne smije mijenjati dizajn, nego samo predložiti minimalni admin-only preview most ili potrebni meta normalizer za privatni clone.

Javni WordPress update i dalje nije dopušten.
