<?php
if (!defined('ABSPATH')) exit;

/**
 * Drycured importer v0.2.2
 * Strogi uvoz:
 * - uvozi samo cjeline koje imaju **ID recepta:**
 * - ne uvozi YAML/Web metapodatke kao recepte
 * - ne uvozi podnaslove tipa Mesni sastav, Začini, Postupak...
 */

function drycured_import_recipe_from_array(array $recipe): int {
    $recipe_id = drycured_id($recipe['recipe_id'] ?? '');

    if (!$recipe_id) {
        throw new Exception('Nedostaje recipe_id.');
    }

    $recipe['recipe_id'] = $recipe_id;
    $title = sanitize_text_field($recipe['title'] ?? $recipe['name'] ?? $recipe_id);

    $existing = drycured_find_recipe_by_recipe_id($recipe_id);

    $post_data = [
        'post_type'    => 'dry_recipe',
        'post_title'   => $title,
        'post_status'  => !empty($recipe['public_ready']) ? 'publish' : 'draft',
        'post_content' => wp_kses_post($recipe['public_html'] ?? ''),
        'post_excerpt' => sanitize_text_field($recipe['short_description'] ?? ''),
    ];

    if ($existing) {
        $post_data['ID'] = $existing;
        $post_id = wp_update_post($post_data, true);
    } else {
        $post_id = wp_insert_post($post_data, true);
    }

    if (is_wp_error($post_id)) {
        throw new Exception($post_id->get_error_message());
    }

    update_post_meta($post_id, '_dry_recipe_id', $recipe_id);
    update_post_meta(
        $post_id,
        '_dry_recipe_data',
        wp_json_encode($recipe, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
    update_post_meta($post_id, '_dry_calculator_ready', !empty($recipe['calculator_ready']));
    update_post_meta($post_id, '_dry_public_ready', !empty($recipe['public_ready']));

    drycured_assign_tax($post_id, 'dry_country', [$recipe['country_hr'] ?? $recipe['country'] ?? 'Hrvatska']);
    drycured_assign_tax($post_id, 'dry_region', [$recipe['region'] ?? '']);
    drycured_assign_tax($post_id, 'dry_microregion', [$recipe['microregion'] ?? '']);
    drycured_assign_tax($post_id, 'dry_product_category', [$recipe['category'] ?? $recipe['product_family'] ?? '']);
    drycured_assign_tax($post_id, 'dry_product_type', [$recipe['product_type'] ?? '']);
    drycured_assign_tax($post_id, 'dry_meat_type', $recipe['meat_types'] ?? []);
    drycured_assign_tax($post_id, 'dry_process_type', $recipe['processes'] ?? []);
    drycured_assign_tax($post_id, 'dry_preparation_method', $recipe['processes'] ?? []);
    drycured_assign_tax($post_id, 'dry_recipe_status', [$recipe['review_status'] ?? 'approved']);

    return intval($post_id);
}

function drycured_import_md_text(string $text, string $source = 'md-import'): array {
    $text = preg_replace('/<!--\s*INTERNAL_SOURCE_BLOCK.*?-->/s', '', $text);

    $recipes = drycured_extract_recipe_sections($text, $source);

    $out = [
        'detected' => count($recipes),
        'created'  => 0,
        'updated'  => 0,
        'errors'   => [],
    ];

    foreach ($recipes as $recipe) {
        try {
            $exists = drycured_find_recipe_by_recipe_id($recipe['recipe_id']);
            drycured_import_recipe_from_array($recipe);

            if ($exists) {
                $out['updated']++;
            } else {
                $out['created']++;
            }
        } catch (Exception $e) {
            $out['errors'][] = ($recipe['recipe_id'] ?? 'unknown') . ': ' . $e->getMessage();
        }
    }

    return $out;
}

function drycured_extract_recipe_sections(string $text, string $source): array {
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $headings = [];

    foreach ($lines as $i => $line) {
        if (preg_match('/^(#{1,6})\s+(.+?)\s*$/u', $line, $m)) {
            $headings[] = [
                'line'  => $i,
                'level' => strlen($m[1]),
                'title' => trim($m[2]),
            ];
        }
    }

    $recipes = [];
    $seen_ids = [];

    foreach ($lines as $i => $line) {
        if (preg_match('/\*\*ID recepta:\*\*\s*([A-Za-z0-9\-_]+)/u', $line, $m)) {
            $recipe_id = $m[1];
        } elseif (preg_match('/^\s*recipe_id:\s*([A-Za-z0-9\-_]+)/u', $line, $m)) {
            $recipe_id = $m[1];
        } else {
            continue;
        }

        if (isset($seen_ids[$recipe_id])) {
            continue;
        }

        $heading = drycured_find_recipe_heading_before_line($headings, $i);

        if (!$heading) {
            continue;
        }

        $title = drycured_clean_public_title($heading['title']);

        if (drycured_title_is_blocked($title)) {
            continue;
        }

        $start = $heading['line'] + 1;
        $end = count($lines);

        foreach ($headings as $h) {
            if ($h['line'] <= $heading['line']) {
                continue;
            }

            if ($h['level'] <= $heading['level']) {
                $end = $h['line'];
                break;
            }
        }

        $body = implode("\n", array_slice($lines, $start, max(0, $end - $start)));

        $recipes[] = drycured_parse_recipe($title, $body, $source, $recipe_id);
        $seen_ids[$recipe_id] = true;
    }

    return $recipes;
}

function drycured_find_recipe_heading_before_line(array $headings, int $line_number): ?array {
    for ($i = count($headings) - 1; $i >= 0; $i--) {
        $h = $headings[$i];

        if ($h['line'] >= $line_number) {
            continue;
        }

        $title = drycured_clean_public_title($h['title']);

        if (drycured_title_is_blocked($title)) {
            continue;
        }

        return $h;
    }

    return null;
}

function drycured_title_is_blocked(string $title): bool {
    $title_l = mb_strtolower(remove_accents(trim($title)));

    $title_l = preg_replace('/^\d+(\.\d+)*[\.\)]?\s*/u', '', $title_l);

    $blocked_titles = [
        'tom 2',
        'pravila ove obrade',
        'regionalna karta',
        'napomena o opsegu',
        'metodologija obrade',
        'novi receptni zapisi',
        'stanje korpusa',
        'qa provjera',
        'deduplikacijska bilješka',
        'deduplikacijska biljeska',
        'mesni sastav',
        'mesni sastav i anatomski dijelovi',
        'standardizirano na 10 kg',
        'sastojci',
        'sastojci i zacini',
        'sastojci i začini',
        'zacini',
        'začini',
        'zacini i dodaci',
        'začini i dodaci',
        'suhi pac',
        'mokri pac',
        'crijeva',
        'crijeva i omotaci',
        'crijeva i omotači',
        'postupak',
        'priprema',
        'tehnoloski postupak',
        'tehnološki postupak',
        'najcesce greske i rjesenja',
        'najčešće greške i rješenja',
        'greske i rjesenja',
        'greške i rješenja',
        'web blok',
        'web metapodaci',
        'yaml blok',
        'yaml metapodaci',
        'json metapodaci',
        'kalkulator',
        'kalkulator podaci',
        'kalkulatorski podaci',
        'kalkulatorski profil',
        'kalkulatorski metapodaci',
        'filteri',
        'web verzija',
        'kratki opis',
        'zakljucak',
        'zaključak',
    ];

    foreach ($blocked_titles as $blocked) {
        if ($title_l === $blocked || str_starts_with($title_l, $blocked)) {
            return true;
        }
    }

    if (function_exists('drycured_technical_title') && drycured_technical_title($title)) {
        return true;
    }

    return false;
}

function drycured_parse_recipe(string $title, string $body, string $source, string $recipe_id): array {
    $recipe = [
        'recipe_id'        => $recipe_id,
        'title'            => $title,
        'source_file'      => $source,
        'public_ready'     => true,
        'review_status'    => 'approved',
        'calculator_ready' => true,
    ];

    $field_patterns = [
        'category' => '/\*\*Kategorija:\*\*\s*(.+)$/mi',
        'region'   => '/\*\*(?:Regija\/stil|Regija):\*\*\s*(.+)$/mi',
        'status'   => '/\*\*Status:\*\*\s*(.+)$/mi',
    ];

    foreach ($field_patterns as $key => $pattern) {
        if (preg_match($pattern, $body, $m)) {
            $recipe[$key] = trim($m[1]);
        }
    }

    $recipe['short_description'] = drycured_extract_short_description($body);

    $recipe['meat_composition'] = drycured_table_after_heading($body, [
        'Mesni sastav',
        'Mesni sastav i anatomski dijelovi',
        'Sirovina',
    ]);

    $recipe['ingredients'] = drycured_table_after_heading($body, [
        'Sastojci',
        'Sastojci i začini',
        'Začini',
        'Začini i dodaci',
        'Suhi pac',
        'Mokri pac',
    ]);

    $recipe['problems'] = drycured_table_after_heading($body, [
        'Najčešće greške i rješenja',
        'Greške i rješenja',
    ]);

    $recipe['procedure_steps'] = drycured_steps_after_heading($body, [
        'Postupak',
        'Priprema',
        'Tehnološki postupak',
    ]);

    drycured_infer_terms($recipe);

    $recipe['public_html'] = '';

    return $recipe;
}

function drycured_clean_public_title(string $title): string {
    $title = trim($title);
    $title = preg_replace('/^\d+(\.\d+)*[\.\)]?\s*/u', '', $title);
    return trim($title);
}

function drycured_extract_short_description(string $body): string {
    if (preg_match('/\*\*Kratki opis:\*\*\s*(.+)$/mi', $body, $m)) {
        return drycured_excerpt($m[1], 220);
    }

    if (preg_match('/###\s+Regionalni.*?\n(.+?)(?:\n\n|$)/si', $body, $m)) {
        return drycured_excerpt($m[1], 220);
    }

    return '';
}

function drycured_table_after_heading(string $body, array $headings): array {
    foreach ($headings as $heading) {
        $pattern = '/#{2,6}\s*' . preg_quote($heading, '/') . '.*?\n(.*?)(?=\n#{2,6}\s+|\z)/si';
        if (preg_match($pattern, $body, $m)) {
            $table = drycured_parse_first_markdown_table($m[1]);
            if ($table) {
                return $table;
            }
        }
    }

    return [];
}

function drycured_parse_first_markdown_table(string $text): array {
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $table = [];

    foreach ($lines as $line) {
        if (strpos($line, '|') !== false) {
            if (preg_match('/^\s*\|?\s*[-:| —–]+\s*\|?\s*$/u', $line)) {
                continue;
            }

            $cells = array_map('trim', explode('|', trim($line, " \t|")));

            if (count($cells) >= 2) {
                $table[] = $cells;
            }
        } elseif (!empty($table)) {
            break;
        }
    }

    if (count($table) < 2) {
        return [];
    }

    $headers = array_shift($table);
    $rows = [];

    foreach ($table as $row) {
        $item = [];

        foreach ($headers as $i => $header) {
            $key = sanitize_key(remove_accents($header));
            $item[$key ?: 'col_' . $i] = $row[$i] ?? '';
        }

        $rows[] = $item;
    }

    return $rows;
}

function drycured_steps_after_heading(string $body, array $headings): array {
    foreach ($headings as $heading) {
        $pattern = '/#{2,6}\s*' . preg_quote($heading, '/') . '.*?\n(.*?)(?=\n#{2,6}\s+|\z)/si';
        if (preg_match($pattern, $body, $m)) {
            preg_match_all('/^\s*\d+\.\s+(.+)$/m', $m[1], $steps);
            if (!empty($steps[1])) {
                return array_map('trim', $steps[1]);
            }
        }
    }

    return [];
}

function drycured_infer_terms(array &$recipe): void {
    $text = mb_strtolower(json_encode($recipe, JSON_UNESCAPED_UNICODE));

    $meat_map = [
        'svinjetina'    => ['svinj', 'vratina', 'plećka', 'plecka', 'but', 'slanina', 'panceta'],
        'govedina'      => ['gove', 'junet'],
        'ovčetina'      => ['ovč', 'ovcet', 'janje', 'janjet'],
        'kozletina'     => ['koz'],
        'konjsko meso'  => ['konj'],
        'riba'          => ['ribl', 'šaran', 'saran', 'som', 'riba'],
        'divljač'       => ['divljač', 'divljac', 'jelen', 'srna', 'vepar'],
    ];

    $recipe['meat_types'] = $recipe['meat_types'] ?? [];

    foreach ($meat_map as $label => $needles) {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                $recipe['meat_types'][] = $label;
                break;
            }
        }
    }

    $recipe['meat_types'] = array_values(array_unique($recipe['meat_types']));

    $process_map = [
        'dimljeno'       => ['dim'],
        'nedimljeno'    => ['nedim'],
        'sušeno'         => ['suš', 'sus'],
        'zrelo'          => ['zren'],
        'fermentirano'  => ['ferment'],
        'bareno'         => ['baren'],
        'za pečenje'     => ['pečen', 'pecen', 'roštilj', 'rostilj'],
        'za kuhanje'     => ['kuhan'],
    ];

    $recipe['processes'] = $recipe['processes'] ?? [];

    foreach ($process_map as $label => $needles) {
        foreach ($needles as $needle) {
            if (str_contains($text, $needle)) {
                $recipe['processes'][] = $label;
                break;
            }
        }
    }

    $recipe['processes'] = array_values(array_unique($recipe['processes']));
}
