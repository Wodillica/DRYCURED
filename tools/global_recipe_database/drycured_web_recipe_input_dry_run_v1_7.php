<?php
/**
 * DRYCURED WEB RECIPE INPUT DRY-RUN v1.7
 * READ ONLY.
 * No database writes.
 * No import.
 * No publish.
 */

$input_json = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/extracted/DRYCURED_9_INTEGRATED_WEB_RECIPE_INPUT_v1_7.json';
$out_dir = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/dry_run';
@mkdir($out_dir, 0755, true);

$summary_path = $out_dir . '/DRY_RUN_SUMMARY_v1_7.txt';
$csv_path = $out_dir . '/DRY_RUN_MAPPING_v1_7.csv';

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
} elseif (array_is_list($data)) {
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
    'country',
    'region',
    'validation_status',
    'server_entry_status',
    'ready_for_import',
    'public_publish_allowed',
    'match_type',
    'matched_post_id',
    'matched_post_status',
    'matched_post_title',
    'dry_run_action',
    'notes'
]);

$total = 0;
$matched_source = 0;
$matched_slug = 0;
$would_create = 0;
$would_review_update = 0;
$ready_true = 0;
$publish_true = 0;
$blocked = 0;

foreach ($records as $i => $r) {
    if (!is_array($r)) continue;
    $total++;

    $source_id = (string) dc_pick($r, ['source_recipe_id','source_id','id','recipe_id','record_id'], '');
    $title = (string) dc_pick($r, ['title','clean_title','croatian_title','name','raw_title'], 'NEIMENOVANI RECEPT');
    $slug = (string) dc_pick($r, ['slug','post_name','clean_slug'], '');
    if ($slug === '') {
        $slug = sanitize_title($title);
    } else {
        $slug = sanitize_title($slug);
    }

    $country = (string) dc_pick($r, ['country','corrected_country','drzava','dr?ava'], '');
    $region = (string) dc_pick($r, ['region','corrected_region','regija'], '');
    $validation_status = (string) dc_pick($r, ['validation_status','public_validation_status','status'], '');
    $server_entry_status = (string) dc_pick($r, ['server_entry_status','transfer_status'], '');

    $ready_import = dc_pick($r, ['ready_for_import'], false);
    $public_publish = dc_pick($r, ['public_publish_allowed'], false);

    if (dc_bool_true($ready_import)) $ready_true++;
    if (dc_bool_true($public_publish)) $publish_true++;

    $match_type = 'none';
    $matched_id = '';
    $matched_status = '';
    $matched_title = '';

    if ($source_id !== '' && isset($source_index[$source_id])) {
        $matched_id = $source_index[$source_id];
        $match_type = 'source_recipe_id';
        $matched_source++;
    } elseif ($slug !== '' && isset($slug_index[$slug])) {
        $matched_id = $slug_index[$slug];
        $match_type = 'slug';
        $matched_slug++;
    }

    if ($matched_id !== '') {
        $mp = get_post($matched_id);
        if ($mp) {
            $matched_status = $mp->post_status;
            $matched_title = $mp->post_title;
        }
        $dry_action = 'DRY_RUN_ONLY__WOULD_REVIEW_EXISTING_PRIVATE_DRAFT_OR_META_MAPPING';
        $would_review_update++;
    } else {
        $dry_action = 'DRY_RUN_ONLY__WOULD_CREATE_PRIVATE_DRAFT_CANDIDATE';
        $would_create++;
    }

    $notes = 'No write performed.';
    if (dc_bool_true($ready_import) || dc_bool_true($public_publish)) {
        $notes = 'BLOCKED: input contains true ready/publish flag.';
        $blocked++;
        $dry_action = 'BLOCKED_IN_DRY_RUN';
    }

    fputcsv($fh, [
        $total,
        $source_id,
        $title,
        $slug,
        $country,
        $region,
        $validation_status,
        $server_entry_status,
        dc_bool_true($ready_import) ? 'true' : 'false',
        dc_bool_true($public_publish) ? 'true' : 'false',
        $match_type,
        $matched_id,
        $matched_status,
        $matched_title,
        $dry_action,
        $notes
    ]);
}
fclose($fh);

$summary = [];
$summary[] = 'DRYCURED WEB RECIPE INPUT DRY-RUN v1.7';
$summary[] = '';
$summary[] = 'MODE: READ ONLY';
$summary[] = 'NO DATABASE WRITES';
$summary[] = 'NO IMPORT';
$summary[] = 'NO PUBLISH';
$summary[] = '';
$summary[] = 'INPUT_JSON: ' . $input_json;
$summary[] = 'TOTAL_INPUT_RECORDS: ' . count($records);
$summary[] = 'TOTAL_PROCESSED_RECORDS: ' . $total;
$summary[] = 'EXISTING_DRY_RECIPE_POSTS: ' . count($existing_ids);
$summary[] = 'MATCHED_BY_SOURCE_ID: ' . $matched_source;
$summary[] = 'MATCHED_BY_SLUG: ' . $matched_slug;
$summary[] = 'WOULD_CREATE_PRIVATE_DRAFT_CANDIDATES: ' . $would_create;
$summary[] = 'WOULD_REVIEW_EXISTING_OR_MAPPING: ' . $would_review_update;
$summary[] = 'READY_FOR_IMPORT_TRUE_COUNT: ' . $ready_true;
$summary[] = 'PUBLIC_PUBLISH_ALLOWED_TRUE_COUNT: ' . $publish_true;
$summary[] = 'BLOCKED_RECORDS: ' . $blocked;
$summary[] = '';
$summary[] = 'OUTPUT_CSV: ' . $csv_path;
$summary[] = '';
$summary[] = 'SAFETY_CONFIRMATION:';
$summary[] = '- WordPress was read only.';
$summary[] = '- No posts were created.';
$summary[] = '- No posts were updated.';
$summary[] = '- No post meta was changed.';
$summary[] = '- No import was performed.';
$summary[] = '- No publish was performed.';

file_put_contents($summary_path, implode("\n", $summary) . "\n");

echo file_get_contents($summary_path);
