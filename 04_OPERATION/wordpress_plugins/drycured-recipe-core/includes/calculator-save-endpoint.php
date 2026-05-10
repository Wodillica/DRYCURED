<?php
if (!defined('ABSPATH')) exit;

/**
 * REST endpoint: spremanje novog recepta iz kalkulatora u jedinstvenu bazu dry_recipe.
 */

add_action('rest_api_init', function () {
    register_rest_route('drycured/v1', '/calculator-save', [
        'methods'  => 'POST',
        'callback' => 'drycured_calculator_save_recipe',
        'permission_callback' => '__return_true',
    ]);
});

function drycured_calculator_save_recipe(WP_REST_Request $request) {
    $payload = $request->get_json_params();

    if (!is_array($payload)) {
        return new WP_Error('invalid_payload', 'Neispravan format recepta.', ['status' => 400]);
    }

    $recipe = drycured_normalize_calculator_recipe_payload($payload);
    $review = drycured_auto_review_calculator_recipe($recipe);

    $recipe['auto_review'] = $review;
    $recipe['source'] = 'calculator_user_input';

    if ($review['status'] === 'approved') {
        $recipe['public_ready'] = true;
        $recipe['review_status'] = 'approved';
    } else {
        $recipe['public_ready'] = false;
        $recipe['review_status'] = 'needs_review';
    }

    try {
        $post_id = drycured_import_recipe_from_array($recipe);
    } catch (Exception $e) {
        return new WP_Error('save_failed', $e->getMessage(), ['status' => 500]);
    }

    return rest_ensure_response([
        'success' => true,
        'post_id' => $post_id,
        'recipe_id' => $recipe['recipe_id'],
        'status' => $recipe['review_status'],
        'auto_review' => $review,
        'message' => $review['status'] === 'approved'
            ? 'Recept je spremljen i odobren.'
            : 'Recept je spremljen, ali treba dopunu prije javne objave.',
    ]);
}

function drycured_normalize_calculator_recipe_payload(array $payload): array {
    $title = sanitize_text_field($payload['title'] ?? $payload['name'] ?? 'Novi Drycured recept');

    $recipe_id = sanitize_text_field($payload['recipe_id'] ?? '');
    if (!$recipe_id) {
        $recipe_id = drycured_make_id($title, 'calculator-' . time());
    }

    $country = sanitize_text_field($payload['country_hr'] ?? $payload['country'] ?? 'Hrvatska');
    $region = sanitize_text_field($payload['region'] ?? 'Neodređena regija');
    $microregion = sanitize_text_field($payload['microregion'] ?? '');
    $category = sanitize_text_field($payload['category'] ?? 'Kobasice');
    $product_type = sanitize_text_field($payload['product_type'] ?? $category);

    $batch_weight_kg = floatval($payload['batch_weight_kg'] ?? 10);
    if ($batch_weight_kg <= 0) {
        $batch_weight_kg = 10;
    }

    $meat_composition = drycured_normalize_meat_rows($payload['meat_composition'] ?? []);
    $ingredients = drycured_normalize_ingredient_rows($payload['ingredients'] ?? []);

    $process_phases = $payload['process_phases'] ?? ($payload['preparation'] ?? []);
    if (!is_array($process_phases)) {
        $process_phases = [];
    }

    $mistakes = $payload['mistakes'] ?? ($payload['problems'] ?? []);
    if (!is_array($mistakes)) {
        $mistakes = [];
    }

    $short_description = sanitize_text_field(
        $payload['short_description']
        ?? 'Korisnički kreiran recept spremljen iz Drycured kalkulatora.'
    );

    $recipe = [
        'recipe_id' => $recipe_id,
        'title' => $title,
        'country_hr' => $country,
        'region' => $region,
        'microregion' => $microregion,
        'category' => $category,
        'product_type' => $product_type,
        'meat_types' => $payload['meat_types'] ?? drycured_guess_meat_types($payload),
        'processes' => $payload['processes'] ?? drycured_guess_processes($payload),
        'batch_weight_kg' => $batch_weight_kg,
        'short_description' => $short_description,
        'calculator_ready' => true,
        'calculator' => [
            'enabled' => true,
            'calculator_key' => sanitize_title(remove_accents($title)),
            'calculator_mode' => 'sastojci',
        ],
        'public_recipe' => [
            'intro' => $payload['intro'] ?? $short_description,
            'quick_facts' => [
                'Osnovna šarža' => drycured_format_number_hr($batch_weight_kg) . ' kg',
                'Regija' => $region,
                'Vrsta' => $category,
                'Meso' => is_array($payload['meat_types'] ?? null) ? implode(', ', $payload['meat_types']) : '',
                'Postupak' => is_array($payload['processes'] ?? null) ? implode(', ', $payload['processes']) : '',
                'Kalkulator' => 'dostupan',
            ],
            'meat_composition' => $meat_composition,
            'ingredients' => $ingredients,
            'process_phases' => $process_phases,
            'mistakes' => $mistakes,
        ],
        'calculator_data' => [
            'batch_weight_kg' => $batch_weight_kg,
            'ingredients' => $ingredients,
            'meat_composition' => $meat_composition,
        ],
    ];

    return $recipe;
}

function drycured_normalize_meat_rows($rows): array {
    if (!is_array($rows)) return [];

    $out = [];

    foreach ($rows as $row) {
        if (!is_array($row)) continue;

        $name = sanitize_text_field($row['name'] ?? $row['part'] ?? $row['dio_mesa'] ?? '');
        if (!$name) continue;

        $item = [
            'name' => $name,
            'why' => sanitize_text_field($row['why'] ?? $row['role'] ?? $row['uloga'] ?? ''),
        ];

        if (isset($row['amount_kg'])) {
            $item['amount_kg'] = floatval($row['amount_kg']);
            $item['amount'] = drycured_format_number_hr($item['amount_kg']) . ' kg';
        } elseif (!empty($row['amount'])) {
            $item['amount'] = sanitize_text_field($row['amount']);
        }

        $out[] = $item;
    }

    return $out;
}

function drycured_normalize_ingredient_rows($rows): array {
    if (!is_array($rows)) return [];

    $out = [];

    foreach ($rows as $row) {
        if (!is_array($row)) continue;

        $name = sanitize_text_field($row['name'] ?? $row['sastojak'] ?? '');
        if (!$name) continue;

        $item = [
            'name' => $name,
            'note' => sanitize_text_field($row['note'] ?? $row['napomena'] ?? ''),
        ];

        if (isset($row['amount_g'])) {
            $item['amount_g'] = floatval($row['amount_g']);
            $item['amount'] = drycured_format_number_hr($item['amount_g']) . ' g';
        } elseif (isset($row['amount_l'])) {
            $item['amount_l'] = floatval($row['amount_l']);
            $item['amount'] = drycured_format_number_hr($item['amount_l']) . ' L';
        } elseif (!empty($row['amount'])) {
            $item['amount'] = sanitize_text_field($row['amount']);
        }

        $out[] = $item;
    }

    return $out;
}

function drycured_auto_review_calculator_recipe(array $recipe): array {
    $errors = [];
    $warnings = [];

    if (empty($recipe['title'])) {
        $errors[] = 'Nedostaje naziv recepta.';
    }

    if (empty($recipe['region'])) {
        $warnings[] = 'Nedostaje regija.';
    }

    if (empty($recipe['public_recipe']['meat_composition'])) {
        $errors[] = 'Nedostaje mesni sastav.';
    }

    if (empty($recipe['public_recipe']['ingredients'])) {
        $errors[] = 'Nedostaju sastojci.';
    }

    if (empty($recipe['public_recipe']['process_phases'])) {
        $warnings[] = 'Nedostaje detaljan postupak po fazama.';
    }

    $salt_found = false;
    foreach ($recipe['public_recipe']['ingredients'] as $ingredient) {
        $name = mb_strtolower($ingredient['name'] ?? '');
        if (str_contains($name, 'sol')) {
            $salt_found = true;
            break;
        }
    }

    if (!$salt_found) {
        $warnings[] = 'Nije pronađena sol u sastojcima.';
    }

    $status = empty($errors) ? 'approved' : 'needs_review';

    return [
        'status' => $status,
        'errors' => $errors,
        'warnings' => $warnings,
        'score' => $status === 'approved' ? 90 : 55,
    ];
}

function drycured_format_number_hr($value): string {
    if (!is_numeric($value)) return (string)$value;

    $value = floatval($value);

    if (floor($value) == $value) {
        return (string)intval($value);
    }

    return rtrim(rtrim(number_format($value, 3, ',', ''), '0'), ',');
}
