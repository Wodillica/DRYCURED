<?php
if (!defined('ABSPATH')) exit;

/**
 * Drycured dynamic calculator registry.
 *
 * Cilj:
 * dry_recipe baza je jedina baza recepata.
 * Kalkulator pri učitavanju prima recepte iz dry_recipe baze i dodaje ih u svoj #dc-json.
 */

add_filter('the_content', 'drycured_merge_recipes_into_calculator_json', 30);

function drycured_merge_recipes_into_calculator_json($content) {
    if (!is_page('kalkulator')) {
        return $content;
    }

    if (strpos($content, 'id="dc-json"') === false) {
        return $content;
    }

    return preg_replace_callback(
        '#<script id="dc-json" type="application/json">(.*?)</script>#s',
        function ($matches) {
            $raw = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $data = json_decode($raw, true);

            if (!is_array($data)) {
                return $matches[0];
            }

            if (empty($data['products']) || !is_array($data['products'])) {
                $data['products'] = [];
            }

            if (empty($data['_region_map']) || !is_array($data['_region_map'])) {
                $data['_region_map'] = [];
            }

            $dynamic_products = drycured_build_dynamic_calculator_products();

            foreach ($dynamic_products as $key => $product) {
                $country_group = $product['country'] ?? 'Neodređena zemlja';

                if (!isset($data['products'][$key])) {
                    $data['products'][$key] = $product;
                }

                if (!isset($data['_region_map'][$country_group])) {
                    $data['_region_map'][$country_group] = [];
                }

                if (!in_array($key, $data['_region_map'][$country_group], true)) {
                    $data['_region_map'][$country_group][] = $key;
                }
            }

            $json = wp_json_encode($data, JSON_UNESCAPED_UNICODE);

            return '<script id="dc-json" type="application/json">' . $json . '</script>';
        },
        $content,
        1
    );
}

function drycured_build_dynamic_calculator_products(): array {
    $posts = get_posts([
        'post_type'      => 'dry_recipe',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'     => '_dry_calculator_ready',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
    ]);

    $products = [];

    foreach ($posts as $post_id) {
        $product = drycured_build_calculator_product_from_recipe($post_id);

        if (!$product || empty($product['_key'])) {
            continue;
        }

        $key = $product['_key'];
        unset($product['_key']);

        $products[$key] = $product;
    }

    return $products;
}

function drycured_build_calculator_product_from_recipe(int $post_id): ?array {
    $raw = get_post_meta($post_id, '_dry_recipe_data', true);
    $recipe = $raw ? json_decode($raw, true) : [];

    if (!is_array($recipe) || empty($recipe['recipe_id'])) {
        return null;
    }

    $calculator = $recipe['calculator'] ?? [];

    $key = $calculator['calculator_key'] ?? '';
    if (!$key) {
        $key = sanitize_title(remove_accents($recipe['title'] ?? $recipe['recipe_id']));
        $key = str_replace('-', '_', $key);
    }

    $key = preg_replace('/[^a-z0-9_]/', '_', strtolower($key));

    $title = $recipe['title'] ?? get_the_title($post_id);
    $country = drycured_dynamic_country_label($recipe, $post_id);
    $geo_path = drycured_dynamic_geo_path($recipe, $post_id);
    $category = drycured_guess_calculator_category($recipe);
    $description = $recipe['public_recipe']['intro'] ?? ($recipe['short_description'] ?? get_the_excerpt($post_id));

    $batch_kg = floatval($recipe['calculator_data']['batch_weight_kg'] ?? ($recipe['batch_weight_kg'] ?? 10));
    if ($batch_kg <= 0) {
        $batch_kg = 10;
    }

    return [
        '_key'        => $key,
        'name'        => $title,
        'category'    => $category,
        'country'     => $country,
        'region'      => $country,
        'geo_path'    => $geo_path,
        'verified'    => false,
        'description' => $description,
        'composition' => drycured_build_calculator_composition($recipe, $batch_kg, $category),
        'ingredients' => drycured_build_calculator_ingredients($recipe, $batch_kg),
        'process'     => drycured_build_calculator_process($recipe),
        '_source'     => 'dry_recipe',
        '_recipe_id'  => $recipe['recipe_id'],
    ];
}

function drycured_dynamic_country_label(array $recipe, int $post_id): string {
    $country = trim((string)($recipe['country_hr'] ?? ($recipe['country'] ?? '')));

    if ($country === '') {
        $country = drycured_dynamic_first_term($post_id, 'dry_country');
    }

    if ($country === '') {
        $region = trim((string)($recipe['region'] ?? ''));

        if (str_contains($region, '—')) {
            $country = trim(explode('—', $region)[0]);
        } elseif (str_contains($region, ' - ')) {
            $country = trim(explode(' - ', $region)[0]);
        }
    }

    $country = drycured_dynamic_clean_geo_label($country);

    if ($country === '' || $country === 'Ostalo' || $country === 'Korisnički recept') {
        return 'Neodređena zemlja';
    }

    return $country;
}

function drycured_dynamic_geo_path(array $recipe, int $post_id): string {
    $region = trim((string)($recipe['region'] ?? ''));
    $micro = trim((string)($recipe['microregion'] ?? ''));
    $locality = trim((string)($recipe['locality'] ?? ''));

    if ($region === '') {
        $region = drycured_dynamic_first_term($post_id, 'dry_region');
    }

    if ($micro === '') {
        $micro = drycured_dynamic_first_term($post_id, 'dry_microregion');
    }

    if (str_contains($region, '—')) {
        $parts = array_map('trim', explode('—', $region));
        if (count($parts) > 1) {
            $region = end($parts);
        }
    }

    if (str_contains($region, ' - ')) {
        $parts = array_map('trim', explode(' - ', $region));
        if (count($parts) > 1) {
            $region = end($parts);
        }
    }

    $bad = [
        '',
        'Ostalo',
        'Neodređena regija',
        'Neodređena zemlja',
        'Korisnički recept',
        'Šira regija',
        'Opći stil',
        'Opći kućni stil'
    ];

    if (in_array($region, $bad, true)) {
        $region = '';
    }

    $parts = array_filter([$region, $micro, $locality]);
    $parts = array_values(array_unique($parts));

    return $parts ? implode(' / ', $parts) : '';
}

function drycured_dynamic_clean_geo_label(string $value): string {
    $value = trim($value);

    $map = [
        'Croatia' => 'Hrvatska',
        'Hrvatska' => 'Hrvatska',
        'Italy' => 'Italija',
        'Italia' => 'Italija',
        'Italija' => 'Italija',
        'Switzerland' => 'Švicarska',
        'Svicarska' => 'Švicarska',
        'Švicarska' => 'Švicarska',
        'Poland' => 'Poljska',
        'Poljska' => 'Poljska',
        'Romania' => 'Rumunjska',
        'Rumunjska' => 'Rumunjska',
        'Russia' => 'Rusija',
        'Rusija' => 'Rusija',
        'France' => 'Francuska',
        'Francuska' => 'Francuska',
        'Germany' => 'Njemačka',
        'Njemačka' => 'Njemačka',
        'Austria' => 'Austrija',
        'Austrija' => 'Austrija',
        'Spain' => 'Španjolska',
        'Španjolska' => 'Španjolska',
        'Slovenia' => 'Slovenija',
        'Slovenija' => 'Slovenija',
        'Bosnia and Herzegovina' => 'Bosna i Hercegovina',
        'Bosna i Hercegovina' => 'Bosna i Hercegovina',
        'Serbia' => 'Srbija',
        'Srbija' => 'Srbija',
        'Montenegro' => 'Crna Gora',
        'Crna Gora' => 'Crna Gora',
        'Hungary' => 'Mađarska',
        'Mađarska' => 'Mađarska',
        'Czechia' => 'Češka',
        'Češka' => 'Češka',
        'Slovakia' => 'Slovačka',
        'Slovačka' => 'Slovačka',
        'Portugal' => 'Portugal',
        'Greece' => 'Grčka',
        'Grčka' => 'Grčka'
    ];

    return $map[$value] ?? $value;
}

function drycured_dynamic_first_term(int $post_id, string $taxonomy): string {
    $terms = get_the_terms($post_id, $taxonomy);

    if (!$terms || is_wp_error($terms)) {
        return '';
    }

    return $terms[0]->name;
}

function drycured_guess_calculator_category(array $recipe): string {
    $text = mb_strtolower(json_encode($recipe, JSON_UNESCAPED_UNICODE));

    if (
        str_contains($text, 'pršut') ||
        str_contains($text, 'prsut') ||
        str_contains($text, 'pancet') ||
        str_contains($text, 'slanina') ||
        str_contains($text, 'ombolo') ||
        str_contains($text, 'zarebnjak') ||
        str_contains($text, 'vratina') ||
        str_contains($text, 'udić') ||
        str_contains($text, 'udic') ||
        str_contains($text, 'kaštradina') ||
        str_contains($text, 'kastradina')
    ) {
        return 'cijeli_komad';
    }

    return 'mljevena';
}

function drycured_build_calculator_composition(array $recipe, float $batch_kg, string $category): array {
    $rows = $recipe['public_recipe']['meat_composition'] ?? ($recipe['meat_composition'] ?? []);

    if ($category === 'cijeli_komad') {
        return [
            'meso_komad' => [
                'pct'  => 100,
                'note' => $rows[0]['name'] ?? 'Cijeli komad mesa',
            ],
        ];
    }

    $lean_kg = 0.0;
    $fat_kg = 0.0;
    $lean_notes = [];
    $fat_notes = [];

    foreach ($rows as $row) {
        if (!is_array($row)) continue;

        $name = $row['name'] ?? ($row['dio_mesa'] ?? '');
        $amount = drycured_extract_amount_kg_from_row($row);

        if ($amount <= 0) continue;

        $name_l = mb_strtolower($name);

        if (
            str_contains($name_l, 'slanina') ||
            str_contains($name_l, 'špek') ||
            str_contains($name_l, 'spek') ||
            str_contains($name_l, 'mast') ||
            str_contains($name_l, 'masno')
        ) {
            $fat_kg += $amount;
            $fat_notes[] = $name;
        } else {
            $lean_kg += $amount;
            $lean_notes[] = $name;
        }
    }

    if (($lean_kg + $fat_kg) <= 0) {
        return [
            'meso_misicno' => [
                'pct'   => 70,
                'range' => [60, 80],
                'note'  => 'Procjena prema tipu proizvoda',
            ],
            'meso_masno' => [
                'pct'   => 30,
                'range' => [20, 40],
                'note'  => 'Procjena prema tipu proizvoda',
            ],
        ];
    }

    $total = $lean_kg + $fat_kg;
    $lean_pct = round(($lean_kg / $total) * 100, 1);
    $fat_pct = round(($fat_kg / $total) * 100, 1);

    return [
        'meso_misicno' => [
            'pct'   => $lean_pct,
            'range' => [max(0, $lean_pct - 10), min(100, $lean_pct + 10)],
            'note'  => implode(', ', array_filter($lean_notes)),
        ],
        'meso_masno' => [
            'pct'   => $fat_pct,
            'range' => [max(0, $fat_pct - 10), min(100, $fat_pct + 10)],
            'note'  => implode(', ', array_filter($fat_notes)),
        ],
    ];
}

function drycured_build_calculator_ingredients(array $recipe, float $batch_kg): array {
    $items = $recipe['calculator_data']['ingredients']
        ?? ($recipe['ingredients'] ?? ($recipe['public_recipe']['ingredients'] ?? []));

    $out = [];

    foreach ($items as $item) {
        if (!is_array($item)) continue;

        $name = $item['name'] ?? ($item['sastojak'] ?? '');
        if (!$name) continue;

        $key = drycured_ingredient_key($name);

        $pct = null;
        $range = null;
        $unit = '%';

        if (isset($item['amount_g'])) {
            $pct = (floatval($item['amount_g']) / 1000) / $batch_kg * 100;
        } elseif (isset($item['amount_l'])) {
            $pct = floatval($item['amount_l']) / $batch_kg * 100;
        } elseif (isset($item['amount_g_min']) && isset($item['amount_g_max'])) {
            $min = (floatval($item['amount_g_min']) / 1000) / $batch_kg * 100;
            $max = (floatval($item['amount_g_max']) / 1000) / $batch_kg * 100;
            $pct = round(($min + $max) / 2, 3);
            $range = [round($min, 3), round($max, 3)];
        } elseif (isset($item['amount_l_min']) && isset($item['amount_l_max'])) {
            $min = floatval($item['amount_l_min']) / $batch_kg * 100;
            $max = floatval($item['amount_l_max']) / $batch_kg * 100;
            $pct = round(($min + $max) / 2, 3);
            $range = [round($min, 3), round($max, 3)];
        }

        if ($pct === null) {
            continue;
        }

        if ($range === null) {
            $range = [round($pct * 0.8, 3), round($pct * 1.2, 3)];
        }

        $out[$key] = [
            'pct'   => round($pct, 3),
            'range' => $range,
            'unit'  => $unit,
            'note'  => $item['note'] ?? $name,
        ];
    }

    return $out;
}

function drycured_build_calculator_process(array $recipe): array {
    $phases = $recipe['public_recipe']['process_phases'] ?? [];
    $process = [];

    foreach ($phases as $phase) {
        if (empty($phase['title'])) continue;

        $title_l = mb_strtolower($phase['title']);
        $text = $phase['what'] ?? ($phase['details'] ?? '');

        if (str_contains($title_l, 'mljeven') || str_contains($title_l, 'rezanje')) {
            $process['rezanje'] = $text;
        }

        if (str_contains($title_l, 'crijev') || str_contains($title_l, 'punjenje')) {
            $process['crijevo'] = $text;
        }

        if (str_contains($title_l, 'dim')) {
            $process['dimljenje'] = $text;
        }

        if (str_contains($title_l, 'sušen') || str_contains($title_l, 'zren')) {
            $process['zrenje_dani'] = $text;
        }
    }

    return $process;
}

function drycured_extract_amount_kg_from_row(array $row): float {
    if (isset($row['amount_kg'])) {
        return floatval($row['amount_kg']);
    }

    if (!empty($row['amount'])) {
        $amount = str_replace(',', '.', $row['amount']);
        if (preg_match('/([0-9]+(?:\.[0-9]+)?)\s*kg/i', $amount, $m)) {
            return floatval($m[1]);
        }
    }

    return 0.0;
}

function drycured_ingredient_key(string $name): string {
    $name_l = mb_strtolower($name);

    $map = [
        'sol'              => ['sol', 'morska sol'],
        'papar'            => ['papar', 'biber'],
        'cesnjak'          => ['češnjak', 'cesnjak', 'bijeli luk', 'beli luk', 'češnjakova'],
        'paprika_slatka'   => ['slatka paprika'],
        'paprika_ljuta'    => ['ljuta paprika', 'čili', 'chili'],
        'vino_belo'        => ['bijelo vino', 'belo vino', 'malvazija', 'žlahtina', 'zlahtina'],
        'lovor'            => ['lovor'],
        'ruzmarin'         => ['ružmarin', 'ruzmarin'],
        'secer'            => ['šećer', 'secer'],
        'majoran'          => ['mažuran', 'majoran'],
        'kim'              => ['kim'],
    ];

    foreach ($map as $key => $needles) {
        foreach ($needles as $needle) {
            if (str_contains($name_l, $needle)) {
                return $key;
            }
        }
    }

    $key = sanitize_title(remove_accents($name));
    $key = str_replace('-', '_', $key);

    return $key ?: 'sastojak';
}
