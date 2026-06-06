<?php
/**
 * DRYCURED WEB RECIPE PRIVATE DRAFT IMPORT v1.7
 * Controlled write script.
 * Creates only private dry_recipe posts.
 * No publish.
 * No update of existing posts.
 */

$execute = getenv('DRYCURED_PRIVATE_DRAFT_EXECUTE') === 'YES';

$input_json = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/extracted/DRYCURED_9_INTEGRATED_WEB_RECIPE_INPUT_v1_7.json';
$out_dir = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/private_import';
@mkdir($out_dir, 0755, true);

$summary_path = $out_dir . '/PRIVATE_IMPORT_SUMMARY_v1_7.txt';
$csv_path = $out_dir . '/PRIVATE_IMPORT_RESULTS_v1_7.csv';

function dc_pick($arr, $keys, $default = '') {
    foreach ($keys as $k) {
        if (is_array($arr) && array_key_exists($k, $arr) && $arr[$k] !== null && $arr[$k] !== '') {
            return $arr[$k];
        }
    }
    return $default;
}

function dc_bool_true($v) {
    if ($v === true) return true;
    if (is_string($v) && strtolower(trim($v)) === 'true') return true;
    if (is_int($v) && $v === 1) return true;
    return false;
}

function dc_to_text($value, $indent = 0) {
    $pad = str_repeat('  ', $indent);

    if (is_array($value)) {
        $is_list = array_keys($value) === range(0, count($value) - 1);
        $out = [];
        foreach ($value as $k => $v) {
            if ($is_list) {
                $out[] = $pad . '- ' . dc_to_text($v, $indent + 1);
            } else {
                $out[] = $pad . '- ' . $k . ': ' . dc_to_text($v, $indent + 1);
            }
        }
        return implode("\n", $out);
    }

    if (is_bool($value)) return $value ? 'true' : 'false';
    if ($value === null) return 'null';

    return trim((string) $value);
}

function dc_build_content($record) {
    $title = dc_pick($record, ['title','clean_title','croatian_title','name','raw_title'], 'Neimenovani recept');
    $source_id = dc_pick($record, ['source_recipe_id','source_id','id','recipe_id','record_id'], '');
    $country = dc_pick($record, ['country','corrected_country','drzava','dr?ava'], '');
    $region = dc_pick($record, ['region','corrected_region','regija'], '');
    $validation_status = dc_pick($record, ['validation_status','public_validation_status','status'], '');
    $server_entry_status = dc_pick($record, ['server_entry_status','transfer_status'], '');

    $lines = [];
    $lines[] = '# ' . $title;
    $lines[] = '';
    $lines[] = '## Status zapisa';
    $lines[] = '';
    $lines[] = '- Status: PRIVATNI SERVER DRAFT';
    $lines[] = '- Spremno za javnu objavu: NE';
    $lines[] = '- Spremno za automatski import: NE';
    $lines[] = '- Potrebna zavr?na ljudska provjera: DA';
    $lines[] = '';
    $lines[] = '## Izvorni trag';
    $lines[] = '';
    $lines[] = '- Source recipe ID: ' . $source_id;
    $lines[] = '- Dr?ava: ' . $country;
    $lines[] = '- Regija: ' . $region;
    $lines[] = '- Validation status: ' . $validation_status;
    $lines[] = '- Server entry status: ' . $server_entry_status;
    $lines[] = '';
    $lines[] = '## Podaci za receptni prikaz';
    $lines[] = '';
    $lines[] = dc_to_text($record);
    $lines[] = '';
    $lines[] = '## Sigurnosna napomena';
    $lines[] = '';
    $lines[] = 'Ovaj zapis je privatni radni unos za drycured.com receptni sustav. Ne smije se javno objaviti dok Davor/ChatGPT ne zavr?e zavr?nu provjeru.';
    $lines[] = '';

    return implode("\n", $lines);
}

if (!$execute) {
    $msg = [];
    $msg[] = 'DRYCURED PRIVATE DRAFT IMPORT v1.7';
    $msg[] = '';
    $msg[] = 'EXECUTE FLAG NOT SET.';
    $msg[] = 'No writes performed.';
    $msg[] = '';
    $msg[] = 'To execute, run with DRYCURED_PRIVATE_DRAFT_EXECUTE=YES.';
    file_put_contents($summary_path, implode("\n", $msg) . "\n");
    echo file_get_contents($summary_path);
    return;
}

if (!file_exists($input_json)) {
    file_put_contents($summary_path, "ERROR: Input JSON not found: $input_json\n");
    echo file_get_contents($summary_path);
    return;
}

$raw = file_get_contents($input_json);
$data = json_decode($raw, true);

if ($data === null) {
    file_put_contents($summary_path, "ERROR: JSON decode failed: " . json_last_error_msg() . "\n");
    echo file_get_contents($summary_path);
    return;
}

if (isset($data['recipes']) && is_array($data['recipes'])) {
    $records = $data['recipes'];
} elseif (isset($data['items']) && is_array($data['items'])) {
    $records = $data['items'];
} elseif (isset($data['records']) && is_array($data['records'])) {
    $records = $data['records'];
} elseif (isset($data['data']) && is_array($data['data'])) {
    $records = $data['data'];
} elseif (is_array($data) && array_keys($data) === range(0, count($data) - 1)) {
    $records = $data;
} else {
    $records = [$data];
}

$existing_ids = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => 'any',
    'numberposts' => -1,
    'fields' => 'ids',
]);

$slug_index = [];
$source_index = [];

$source_meta_keys = [
    'source_recipe_id',
    '_source_recipe_id',
    'drycured_source_recipe_id',
    '_drycured_source_recipe_id',
    'drycured_recipe_source_id',
    '_drycured_recipe_source_id',
    'recipe_source_id',
    '_recipe_source_id',
];

foreach ($existing_ids as $pid) {
    $post = get_post($pid);
    if ($post && !empty($post->post_name)) {
        $slug_index[$post->post_name] = $pid;
    }

    foreach ($source_meta_keys as $mk) {
        $mv = get_post_meta($pid, $mk, true);
        if (is_string($mv) && trim($mv) !== '') {
            $source_index[trim($mv)] = $pid;
        }
    }
}

$fh = fopen($csv_path, 'w');
fputcsv($fh, [
    'input_no',
    'source_recipe_id',
    'title',
    'slug',
    'created_post_id',
    'post_status',
    'action',
    'notes'
]);

$total = 0;
$created = 0;
$skipped_existing = 0;
$blocked = 0;
$errors = 0;
$created_ids = [];

foreach ($records as $r) {
    if (!is_array($r)) continue;
    $total++;

    $source_id = (string) dc_pick($r, ['source_recipe_id','source_id','id','recipe_id','record_id'], '');
    $title = (string) dc_pick($r, ['title','clean_title','croatian_title','name','raw_title'], 'NEIMENOVANI RECEPT');
    $slug = (string) dc_pick($r, ['slug','post_name','clean_slug'], '');
    $slug = $slug === '' ? sanitize_title($title) : sanitize_title($slug);

    $ready_import = dc_pick($r, ['ready_for_import'], false);
    $public_publish = dc_pick($r, ['public_publish_allowed'], false);

    if (dc_bool_true($ready_import) || dc_bool_true($public_publish)) {
        $blocked++;
        fputcsv($fh, [$total, $source_id, $title, $slug, '', '', 'BLOCKED', 'ready_for_import or public_publish_allowed was true']);
        continue;
    }

    if ($source_id !== '' && isset($source_index[$source_id])) {
        $skipped_existing++;
        fputcsv($fh, [$total, $source_id, $title, $slug, $source_index[$source_id], '', 'SKIPPED_EXISTING_SOURCE_ID', 'Existing post found by source ID']);
        continue;
    }

    if ($slug !== '' && isset($slug_index[$slug])) {
        $skipped_existing++;
        fputcsv($fh, [$total, $source_id, $title, $slug, $slug_index[$slug], '', 'SKIPPED_EXISTING_SLUG', 'Existing post found by slug']);
        continue;
    }

    $content = dc_build_content($r);

    $post_id = wp_insert_post([
        'post_type' => 'dry_recipe',
        'post_status' => 'private',
        'post_title' => wp_strip_all_tags($title),
        'post_name' => $slug,
        'post_content' => wp_slash($content),
        'post_excerpt' => 'Privatni drycured receptni draft v1.7. Nije za javnu objavu.',
    ], true);

    if (is_wp_error($post_id)) {
        $errors++;
        fputcsv($fh, [$total, $source_id, $title, $slug, '', '', 'ERROR', $post_id->get_error_message()]);
        continue;
    }

    update_post_meta($post_id, 'drycured_source_recipe_id', $source_id);
    update_post_meta($post_id, 'drycured_web_input_version', 'v1.7');
    update_post_meta($post_id, 'drycured_private_draft_batch', 'web_recipe_input_v1_7');
    update_post_meta($post_id, 'drycured_ready_for_import', 'false');
    update_post_meta($post_id, 'drycured_public_publish_allowed', 'false');
    update_post_meta($post_id, 'drycured_requires_final_human_check', 'true');
    update_post_meta($post_id, 'drycured_input_raw_json', wp_json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    update_post_meta($post_id, 'drycured_import_note', 'Created as private draft only. No public publish allowed.');

    $created++;
    $created_ids[] = $post_id;

    fputcsv($fh, [$total, $source_id, $title, $slug, $post_id, 'private', 'CREATED_PRIVATE_DRAFT', 'Created safely as private dry_recipe']);
}

fclose($fh);

$summary = [];
$summary[] = 'DRYCURED WEB RECIPE PRIVATE DRAFT IMPORT v1.7';
$summary[] = '';
$summary[] = 'MODE: CONTROLLED WRITE';
$summary[] = 'POST TYPE: dry_recipe';
$summary[] = 'POST STATUS: private only';
$summary[] = 'NO PUBLISH';
$summary[] = '';
$summary[] = 'INPUT_JSON: ' . $input_json;
$summary[] = 'TOTAL_INPUT_RECORDS: ' . count($records);
$summary[] = 'TOTAL_PROCESSED_RECORDS: ' . $total;
$summary[] = 'CREATED_PRIVATE_DRAFTS: ' . $created;
$summary[] = 'SKIPPED_EXISTING: ' . $skipped_existing;
$summary[] = 'BLOCKED_RECORDS: ' . $blocked;
$summary[] = 'ERRORS: ' . $errors;
$summary[] = 'CREATED_POST_IDS: ' . implode(',', $created_ids);
$summary[] = '';
$summary[] = 'OUTPUT_CSV: ' . $csv_path;
$summary[] = '';
$summary[] = 'SAFETY_CONFIRMATION:';
$summary[] = '- Posts were created only as private.';
$summary[] = '- No posts were published.';
$summary[] = '- Existing posts were not updated.';
$summary[] = '- Existing post meta was not changed.';
$summary[] = '- Manual review is still required.';

file_put_contents($summary_path, implode("\n", $summary) . "\n");
echo file_get_contents($summary_path);
