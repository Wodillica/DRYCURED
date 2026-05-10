<?php
if (!defined('ABSPATH')) exit;

/**
 * Drycured Geo Normalizer
 * Jedan zajednički sloj za:
 * - državu
 * - regiju
 * - podregiju/mikroregiju
 * - lokalitet
 *
 * Radi prema data/drycured_geo_registry.json
 */

function drycured_geo_registry_load(): array {
    static $registry = null;

    if ($registry !== null) {
        return $registry;
    }

    $file = DRYCURED_RECIPE_CORE_PATH . 'data/drycured_geo_registry.json';

    if (!file_exists($file)) {
        $registry = [];
        return $registry;
    }

    $raw = file_get_contents($file);
    $decoded = json_decode($raw, true);

    $registry = is_array($decoded) ? $decoded : [];

    return $registry;
}

function drycured_geo_norm(string $value): string {
    $value = mb_strtolower(trim($value), 'UTF-8');

    $map = [
        'č'=>'c','ć'=>'c','š'=>'s','ž'=>'z','đ'=>'d',
        'á'=>'a','à'=>'a','ä'=>'a','â'=>'a','ã'=>'a',
        'é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
        'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i',
        'ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o','õ'=>'o',
        'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u',
        'ñ'=>'n','ç'=>'c','ß'=>'ss',
        '’'=>"'",
        '–'=>'-',
        '—'=>'-'
    ];

    $value = strtr($value, $map);
    $value = preg_replace('/[^a-z0-9]+/u', ' ', $value);
    $value = preg_replace('/\s+/', ' ', $value);

    return trim($value);
}

function drycured_geo_text_from_recipe(array $recipe): string {
    $parts = [];

    foreach ([
        'title', 'name', 'country_hr', 'country', 'region', 'microregion',
        'subregion', 'locality', 'category', 'product_type', 'short_description'
    ] as $key) {
        if (!empty($recipe[$key])) {
            $parts[] = (string)$recipe[$key];
        }
    }

    if (!empty($recipe['public_recipe']['intro'])) {
        $parts[] = (string)$recipe['public_recipe']['intro'];
    }

    if (!empty($recipe['source_file'])) {
        $parts[] = (string)$recipe['source_file'];
    }

    return drycured_geo_norm(implode(' ', $parts));
}

function drycured_geo_find_country_by_explicit_value(string $value, array $registry): ?array {
    $needle = drycured_geo_norm($value);

    if ($needle === '') {
        return null;
    }

    foreach (($registry['countries'] ?? []) as $country_key => $country) {
        $aliases = array_merge(
            [$country['label_hr'] ?? '', $country_key],
            $country['aliases'] ?? []
        );

        foreach ($aliases as $alias) {
            if ($needle === drycured_geo_norm((string)$alias)) {
                return [
                    'country_key' => $country_key,
                    'country' => $country
                ];
            }
        }
    }

    return null;
}

function drycured_geo_find_region_by_explicit_value(string $value, array $registry): ?array {
    $needle = drycured_geo_norm($value);

    if ($needle === '') {
        return null;
    }

    foreach (($registry['countries'] ?? []) as $country_key => $country) {
        foreach (($country['regions'] ?? []) as $region_key => $region) {
            $aliases = array_merge(
                [$region['label_hr'] ?? '', $region_key],
                $region['aliases'] ?? []
            );

            foreach ($aliases as $alias) {
                if ($needle === drycured_geo_norm((string)$alias)) {
                    return [
                        'country_key' => $country_key,
                        'country' => $country,
                        'region_key' => $region_key,
                        'region' => $region
                    ];
                }
            }
        }
    }

    return null;
}

function drycured_geo_detect_from_text(array $recipe, array $registry): array {
    $hay = drycured_geo_text_from_recipe($recipe);

    $best = [
        'score' => 0,
        'country_key' => '',
        'country' => null,
        'region_key' => '',
        'region' => null
    ];

    foreach (($registry['countries'] ?? []) as $country_key => $country) {
        $country_score = 0;

        $country_aliases = array_merge(
            [$country['label_hr'] ?? '', $country_key],
            $country['aliases'] ?? []
        );

        foreach ($country_aliases as $alias) {
            $a = drycured_geo_norm((string)$alias);
            if ($a !== '' && str_contains($hay, $a)) {
                $country_score += 10;
            }
        }

        foreach (($country['regions'] ?? []) as $region_key => $region) {
            $score = $country_score;

            $region_aliases = array_merge(
                [$region['label_hr'] ?? '', $region_key],
                $region['aliases'] ?? []
            );

            foreach ($region_aliases as $alias) {
                $a = drycured_geo_norm((string)$alias);
                if ($a !== '' && str_contains($hay, $a)) {
                    $score += 20;
                }
            }

            if ($score > $best['score']) {
                $best = [
                    'score' => $score,
                    'country_key' => $country_key,
                    'country' => $country,
                    'region_key' => $region_key,
                    'region' => $region
                ];
            }
        }

        // Ako država nema regije, ili je nađena samo država.
        if ($country_score > $best['score']) {
            $best = [
                'score' => $country_score,
                'country_key' => $country_key,
                'country' => $country,
                'region_key' => '',
                'region' => null
            ];
        }
    }

    return $best;
}

function drycured_geo_normalize_recipe(array $recipe): array {
    $registry = drycured_geo_registry_load();

    if (empty($registry['countries'])) {
        return $recipe;
    }

    $country_value = trim((string)($recipe['country_hr'] ?? ($recipe['country'] ?? '')));
    $region_value = trim((string)($recipe['region'] ?? ''));

    $country_match = drycured_geo_find_country_by_explicit_value($country_value, $registry);
    $region_match = drycured_geo_find_region_by_explicit_value($region_value, $registry);

    $detected = drycured_geo_detect_from_text($recipe, $registry);

    $country_key = '';
    $country = null;
    $region_key = '';
    $region = null;

    // Prioritet 1: ako je country eksplicitno država.
    if ($country_match) {
        $country_key = $country_match['country_key'];
        $country = $country_match['country'];
    }

    // Prioritet 2: ako je country zapravo regija/poddržava, npr. Engleska.
    if (!$country_match && $country_value !== '') {
        $country_as_region = drycured_geo_find_region_by_explicit_value($country_value, $registry);
        if ($country_as_region) {
            $country_key = $country_as_region['country_key'];
            $country = $country_as_region['country'];
            $region_key = $country_as_region['region_key'];
            $region = $country_as_region['region'];
        }
    }

    // Prioritet 3: eksplicitna regija.
    if ($region_match) {
        if (!$country) {
            $country_key = $region_match['country_key'];
            $country = $region_match['country'];
        }
        $region_key = $region_match['region_key'];
        $region = $region_match['region'];
    }

    // Prioritet 4: detekcija iz naslova/opisa.
    if (!$country && !empty($detected['country'])) {
        $country_key = $detected['country_key'];
        $country = $detected['country'];
    }

    if (!$region && !empty($detected['region'])) {
        $region_key = $detected['region_key'];
        $region = $detected['region'];
    }

    if (!$country) {
        $country_key = 'neodredena_zemlja';
        $country_label = 'Neodređena zemlja';
    } else {
        $country_label = $country['label_hr'] ?? $country_key;
    }

    $region_label = '';
    if ($region) {
        $region_label = $region['label_hr'] ?? $region_key;
    } elseif ($region_value !== '' && !in_array($region_value, ['Neodređena regija', 'Neodređena zemlja', 'Ostalo'], true)) {
        // Ako nije prepoznato, ali korisnik je dao neku regiju, zadrži je.
        $region_label = $region_value;
    }

    $micro = trim((string)($recipe['microregion'] ?? ''));
    $subregion = trim((string)($recipe['subregion'] ?? ''));
    $locality = trim((string)($recipe['locality'] ?? ''));

    $recipe['country_key'] = $country_key;
    $recipe['country_hr'] = $country_label;
    $recipe['country'] = $country_label;

    if ($region_label !== '') {
        $recipe['region_key'] = $region_key ?: sanitize_title(remove_accents($region_label));
        $recipe['region'] = $region_label;
    } else {
        $recipe['region_key'] = '';
        $recipe['region'] = '';
    }

    if ($subregion !== '') {
        $recipe['subregion'] = $subregion;
    }

    if ($micro !== '') {
        $recipe['microregion'] = $micro;
    }

    if ($locality !== '') {
        $recipe['locality'] = $locality;
    }

    $path_parts = array_filter([
        $country_label,
        $region_label,
        $subregion,
        $micro,
        $locality
    ]);

    $recipe['geo_path'] = implode(' / ', array_values(array_unique($path_parts)));

    return $recipe;
}
