<?php
if (!defined('ABSPATH')) exit;

/**
 * Drycured universal JSON importer.
 * Prima:
 * 1) JSON listu recepata: [ {...}, {...} ]
 * 2) JSON paket: { "pack_name": "...", "recipes": [ {...}, {...} ] }
 */

function drycured_import_json_file(string $file): array {
    if (!file_exists($file)) {
        throw new Exception('JSON datoteka ne postoji: ' . $file);
    }

    $raw = file_get_contents($file);
    if (!$raw) {
        throw new Exception('JSON datoteka je prazna ili se ne može pročitati.');
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        throw new Exception('Neispravan JSON: ' . json_last_error_msg());
    }

    if (isset($decoded['recipes']) && is_array($decoded['recipes'])) {
        $recipes = $decoded['recipes'];
        $pack_name = $decoded['pack_name'] ?? basename($file);
    } else {
        $recipes = $decoded;
        $pack_name = basename($file);
    }

    $out = [
        'pack' => $pack_name,
        'detected' => count($recipes),
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    foreach ($recipes as $index => $recipe) {
        if (!is_array($recipe)) {
            $out['skipped']++;
            $out['errors'][] = 'Stavka #' . $index . ' nije objekt.';
            continue;
        }

        $normalized = drycured_normalize_json_recipe($recipe, $pack_name, $index);
        $validation = drycured_validate_json_recipe($normalized);

        if (!$validation['valid']) {
            $out['skipped']++;
            $out['errors'][] = ($normalized['recipe_id'] ?? 'unknown') . ': ' . implode('; ', $validation['errors']);
            continue;
        }

        try {
            $exists = drycured_find_recipe_by_recipe_id($normalized['recipe_id']);
            drycured_import_recipe_from_array($normalized);
            $exists ? $out['updated']++ : $out['created']++;
        } catch (Exception $e) {
            $out['errors'][] = ($normalized['recipe_id'] ?? 'unknown') . ': ' . $e->getMessage();
        }
    }

    return $out;
}

function drycured_normalize_json_recipe(array $recipe, string $pack_name, int $index): array {
    $title = trim((string)($recipe['title'] ?? $recipe['name'] ?? ''));
    if (!$title) {
        $title = 'Recept ' . ($index + 1);
    }

    if (empty($recipe['recipe_id'])) {
        $recipe['recipe_id'] = drycured_make_id($title, $pack_name . '-' . $index);
    }

    $recipe['recipe_id'] = drycured_id($recipe['recipe_id']);
    $recipe['title'] = $title;

    if (function_exists('drycured_geo_normalize_recipe')) {
        $recipe = drycured_geo_normalize_recipe($recipe);
    }


    if (empty($recipe['country']) && empty($recipe['country_hr'])) {
        $recipe['country_hr'] = 'Neodređena zemlja';
    }

    if (empty($recipe['region'])) {
        $recipe['region'] = '';
    }

    if (empty($recipe['category']) && !empty($recipe['product_family'])) {
        $recipe['category'] = $recipe['product_family'];
    }

    if (empty($recipe['category'])) {
        $recipe['category'] = drycured_guess_category_from_title($title);
    }

    if (empty($recipe['product_type'])) {
        $recipe['product_type'] = $recipe['category'];
    }

    if (!isset($recipe['public_ready'])) {
        $recipe['public_ready'] = true;
    }

    if (!isset($recipe['calculator_ready'])) {
        $recipe['calculator_ready'] = !empty($recipe['batch_weight_kg']) && !empty($recipe['ingredients']);
    }

    if (empty($recipe['review_status'])) {
        $recipe['review_status'] = 'approved';
    }

    if (empty($recipe['short_description'])) {
        $recipe['short_description'] = drycured_make_short_description($recipe);
    }

    if (empty($recipe['meat_types']) || !is_array($recipe['meat_types'])) {
        $recipe['meat_types'] = drycured_guess_meat_types($recipe);
    }

    if (empty($recipe['processes']) || !is_array($recipe['processes'])) {
        $recipe['processes'] = drycured_guess_processes($recipe);
    }

    $recipe['public_html'] = '';

    return $recipe;
}

function drycured_validate_json_recipe(array $recipe): array {
    $errors = [];
    $warnings = [];

    if (empty($recipe['recipe_id'])) {
        $errors[] = 'Nedostaje recipe_id.';
    }

    if (empty($recipe['title'])) {
        $errors[] = 'Nedostaje title.';
    }

    if (empty($recipe['country']) && empty($recipe['country_hr'])) {
        $warnings[] = 'Nedostaje country/country_hr.';
    }

    if (empty($recipe['region'])) {
        $warnings[] = 'Nedostaje region.';
    }

    if (empty($recipe['category'])) {
        $warnings[] = 'Nedostaje category.';
    }

    if (!empty($recipe['batch_weight_kg']) && !is_numeric($recipe['batch_weight_kg'])) {
        $errors[] = 'batch_weight_kg mora biti broj.';
    }

    if (!empty($recipe['ingredients']) && !is_array($recipe['ingredients'])) {
        $errors[] = 'ingredients mora biti lista.';
    }

    if (!empty($recipe['meat_composition']) && !is_array($recipe['meat_composition'])) {
        $errors[] = 'meat_composition mora biti lista.';
    }

    return [
        'valid' => count($errors) === 0,
        'errors' => $errors,
        'warnings' => $warnings,
    ];
}

function drycured_guess_category_from_title(string $title): string {
    $t = mb_strtolower($title);

    if (str_contains($t, 'pršut') || str_contains($t, 'prsut')) return 'Pršuti';
    if (str_contains($t, 'pancet') || str_contains($t, 'slanina') || str_contains($t, 'špek')) return 'Pancete i slanine';
    if (str_contains($t, 'kulen')) return 'Kuleni';
    if (str_contains($t, 'salama') || str_contains($t, 'salamin')) return 'Salame';
    if (str_contains($t, 'krvavic') || str_contains($t, 'čurka') || str_contains($t, 'ćurka')) return 'Krvavice';
    if (str_contains($t, 'tlačen') || str_contains($t, 'švarg') || str_contains($t, 'prezvur')) return 'Tlačenice i švargle';
    if (str_contains($t, 'kobasic') || str_contains($t, 'pečenic')) return 'Kobasice';
    if (str_contains($t, 'ombolo') || str_contains($t, 'zarebnjak') || str_contains($t, 'vratina') || str_contains($t, 'buđola')) return 'Cijeli komadi';

    return 'Ostalo';
}

function drycured_make_short_description(array $recipe): string {
    $parts = [];

    if (!empty($recipe['product_type'])) $parts[] = $recipe['product_type'];
    if (!empty($recipe['region'])) $parts[] = $recipe['region'];
    if (!empty($recipe['meat_types']) && is_array($recipe['meat_types'])) $parts[] = implode(', ', $recipe['meat_types']);

    if ($parts) {
        return ucfirst(implode(' · ', $parts)) . '.';
    }

    return '';
}

function drycured_guess_meat_types(array $recipe): array {
    $text = mb_strtolower(json_encode($recipe, JSON_UNESCAPED_UNICODE));
    $out = [];

    $map = [
        'svinjetina' => ['svinj', 'plećka', 'plecka', 'but', 'vratina', 'slanina', 'panceta', 'ombolo'],
        'govedina' => ['gove', 'junet', 'boškarin', 'boskarin'],
        'ovčetina' => ['ovč', 'ovcet', 'janje', 'janjet'],
        'kozletina' => ['koz'],
        'konjsko meso' => ['konj'],
        'riba' => ['riba', 'ribl', 'šaran', 'saran', 'som'],
        'divljač' => ['divljač', 'divljac', 'jelen', 'srna', 'vepar'],
    ];

    foreach ($map as $label => $needles) {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                $out[] = $label;
                break;
            }
        }
    }

    return array_values(array_unique($out));
}

function drycured_guess_processes(array $recipe): array {
    $text = mb_strtolower(json_encode($recipe, JSON_UNESCAPED_UNICODE));
    $out = [];

    $map = [
        'dimljeno' => ['dimljen', 'dimiti', 'dimljenje'],
        'nedimljeno' => ['nedimljen', 'bez dimljenja'],
        'sušeno' => ['suš', 'sus'],
        'zrelo' => ['zren'],
        'fermentirano' => ['ferment'],
        'bareno' => ['baren'],
        'za pečenje' => ['pečen', 'pecen', 'roštilj', 'rostilj'],
        'za kuhanje' => ['kuhan'],
    ];

    foreach ($map as $label => $needles) {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                $out[] = $label;
                break;
            }
        }
    }

    return array_values(array_unique($out));
}
