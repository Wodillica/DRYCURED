<?php
/**
 * Source-lock WordPress import dry-run.
 *
 * Usage:
 *   wp eval-file tools/source_lock_compiler/wp_source_lock_import_dry_run.php --path=/var/www/html --allow-root
 *
 * This script reads source-lock JSON plus the safe mapping CSV and prints what
 * an importer would do. It only reads WordPress posts/meta and writes a CSV
 * report; it does not write WordPress data or touch renderer code.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "This script must be run through WP-CLI eval-file with WordPress loaded.\n");
    exit(1);
}

$repo_root = dirname(__DIR__, 2);
$json_dir = $repo_root . '/build/source_locked_json';
$report_dir = $repo_root . '/server-reports/recipes';
$mapping_csv_path = $report_dir . '/source_lock_wp_mapping_dry_run_batch30.csv';
$import_csv_path = $report_dir . '/source_lock_wp_import_dry_run_batch30.csv';
$post_type = 'dry_recipe';
$meta_keys = array('recipe_id', 'source_lock_recipe_id', 'dry_recipe_code');

function sl_import_clean_text($value) {
    return trim(wp_strip_all_tags((string) $value));
}

function dc_sl_array($value) {
    return is_array($value) ? $value : array();
}

function sl_import_expected_slug($recipe_id) {
    return sanitize_title(strtolower((string) $recipe_id));
}

function sl_import_read_csv_assoc($path) {
    if (!is_readable($path)) {
        return array();
    }
    $handle = fopen($path, 'r');
    if (!$handle) {
        return array();
    }
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        return array();
    }
    $rows = array();
    while (($row = fgetcsv($handle)) !== false) {
        $item = array();
        foreach (dc_sl_array($header) as $index => $column) {
            $item[$column] = isset($row[$index]) ? $row[$index] : '';
        }
        if (!empty($item['recipe_id'])) {
            $rows[$item['recipe_id']] = $item;
        }
    }
    fclose($handle);
    return $rows;
}

function sl_import_source_lock_pass($source, &$blocked_reason) {
    $audit = isset($source['audit']) && is_array($source['audit']) ? $source['audit'] : array();
    $ingredient_status = isset($audit['ingredient_status']) ? (string) $audit['ingredient_status'] : '';
    $process_status = isset($audit['process_status']) ? (string) $audit['process_status'] : '';
    $overall_status = isset($audit['overall_status']) ? (string) $audit['overall_status'] : '';
    $contamination = dc_sl_array($audit['process_contamination_errors'] ?? array());
    if ($ingredient_status !== 'PASS' || $process_status !== 'PASS' || $overall_status !== 'PASS' || count($contamination) > 0) {
        $blocked_reason = 'BLOCKED_SOURCE_LOCK_NOT_PASS';
        return false;
    }
    return true;
}

function sl_import_meta_plan($post_id, $recipe_id, $source, $json_file) {
    global $meta_keys;
    $changes = array();
    foreach (dc_sl_array($meta_keys) as $meta_key) {
        $current = get_post_meta($post_id, $meta_key, true);
        if ((string) $current !== (string) $recipe_id) {
            $changes[] = $meta_key . '=' . $recipe_id;
        }
    }
    $source_hash = isset($source['source']['sha256']) ? (string) $source['source']['sha256'] : '';
    if ($source_hash !== '') {
        $current_hash = get_post_meta($post_id, 'source_lock_sha256', true);
        if ((string) $current_hash !== $source_hash) {
            $changes[] = 'source_lock_sha256=' . $source_hash;
        }
    }
    $current_json_path = get_post_meta($post_id, 'source_lock_json_path', true);
    if ((string) $current_json_path !== (string) $json_file) {
        $changes[] = 'source_lock_json_path=' . $json_file;
    }
    return implode('|', $changes);
}

function sl_import_create_meta_plan($recipe_id, $source, $json_file) {
    $changes = array(
        'recipe_id=' . $recipe_id,
        'source_lock_recipe_id=' . $recipe_id,
        'dry_recipe_code=' . $recipe_id,
        'source_lock_json_path=' . $json_file,
    );
    $source_hash = isset($source['source']['sha256']) ? (string) $source['source']['sha256'] : '';
    if ($source_hash !== '') {
        $changes[] = 'source_lock_sha256=' . $source_hash;
    }
    return implode('|', $changes);
}

function sl_import_report_diff($current_value, $expected_value) {
    return (string) $current_value === (string) $expected_value ? 'no' : 'yes';
}

function sl_import_mapping_safe($mapping) {
    $action = isset($mapping['action']) ? (string) $mapping['action'] : '';
    $safe_match = isset($mapping['safe_match']) ? (string) $mapping['safe_match'] : '';
    if ($action === 'UPDATE_EXISTING' && $safe_match !== '1') {
        return false;
    }
    return true;
}

if (!is_dir($json_dir)) {
    fwrite(STDERR, "Missing source-lock JSON directory: {$json_dir}\n");
    exit(1);
}

if (!is_readable($mapping_csv_path)) {
    fwrite(STDERR, "Missing mapping CSV: {$mapping_csv_path}\n");
    exit(1);
}

if (!is_dir($report_dir) && !mkdir($report_dir, 0775, true) && !is_dir($report_dir)) {
    fwrite(STDERR, "Cannot create report directory: {$report_dir}\n");
    exit(1);
}

$mapping_rows = sl_import_read_csv_assoc($mapping_csv_path);
$json_files = glob($json_dir . '/*.source_locked.json');
if (!is_array($json_files)) {
    $json_files = array();
}
sort($json_files, SORT_STRING);
if (!$json_files) {
    fwrite(STDERR, "No source-lock JSON files found in: {$json_dir}\n");
    exit(1);
}

$handle = fopen($import_csv_path, 'w');
if (!$handle) {
    fwrite(STDERR, "Cannot write CSV report: {$import_csv_path}\n");
    exit(1);
}

$columns = array(
    'recipe_id',
    'source_title',
    'mapping_action',
    'import_dry_run_action',
    'target_post_id',
    'current_title',
    'current_slug',
    'current_status',
    'expected_slug',
    'fields_to_update',
    'meta_to_update',
    'title_diff',
    'slug_diff',
    'title_policy',
    'slug_policy',
    'blocked_reason',
    'notes',
);
fputcsv($handle, $columns);

$summary = array(
    'total' => 0,
    'would_update_existing' => 0,
    'would_create_new_private' => 0,
    'blocked' => 0,
    'skip' => 0,
);

WP_CLI::line(implode(',', $columns));

foreach (dc_sl_array($json_files) as $json_file) {
    $raw = file_get_contents($json_file);
    $source = json_decode($raw, true);
    $recipe_id = is_array($source) && !empty($source['recipe_id'])
        ? (string) $source['recipe_id']
        : basename($json_file, '.source_locked.json');
    $source_title = is_array($source) ? sl_import_clean_text($source['title'] ?? '') : '';
    $expected_slug = sl_import_expected_slug($recipe_id);
    $mapping = isset($mapping_rows[$recipe_id]) ? $mapping_rows[$recipe_id] : null;
    $mapping_action = $mapping ? (string) ($mapping['action'] ?? '') : 'SKIP';
    $import_action = 'SKIP';
    $target_post_id = '';
    $current_title = '';
    $current_slug = '';
    $current_status = '';
    $fields_to_update = '';
    $meta_to_update = '';
    $title_diff = 'no';
    $slug_diff = 'no';
    $title_policy = '';
    $slug_policy = '';
    $blocked_reason = '';
    $notes = '';

    $summary['total']++;

    if (!is_array($source)) {
        $import_action = 'SKIP';
        $blocked_reason = 'INVALID_SOURCE_JSON';
        $summary['skip']++;
    } elseif (!$mapping) {
        $import_action = 'SKIP';
        $blocked_reason = 'MAPPING_ROW_MISSING';
        $summary['skip']++;
    } elseif (!sl_import_source_lock_pass($source, $blocked_reason)) {
        $import_action = 'BLOCKED_SOURCE_LOCK_NOT_PASS';
        $summary['blocked']++;
    } elseif (!sl_import_mapping_safe($mapping)) {
        $import_action = 'BLOCKED_UNSAFE_MAPPING';
        $blocked_reason = 'BLOCKED_UNSAFE_MAPPING';
        $summary['blocked']++;
    } elseif ($mapping_action === 'UPDATE_EXISTING') {
        $target_post_id = (string) ($mapping['target_post_id'] ?? '');
        $post = $target_post_id !== '' ? get_post((int) $target_post_id) : null;
        if (!$post || $post->post_type !== $post_type) {
            $import_action = 'BLOCKED_TARGET_POST_NOT_FOUND';
            $blocked_reason = 'BLOCKED_TARGET_POST_NOT_FOUND';
            $summary['blocked']++;
        } else {
            $current_title = sl_import_clean_text($post->post_title);
            $current_slug = (string) $post->post_name;
            $current_status = (string) $post->post_status;
            $title_diff = sl_import_report_diff($current_title, $source_title);
            $slug_diff = sl_import_report_diff($current_slug, $expected_slug);
            $title_policy = 'report_only_keep_existing';
            $slug_policy = 'report_only_keep_existing';
            $review_notes = array('status_change: none');
            if ($title_diff === 'yes') {
                $review_notes[] = 'TITLE_DIFF_REVIEW_ONLY';
            }
            if ($slug_diff === 'yes') {
                $review_notes[] = 'SLUG_DIFF_REVIEW_ONLY';
            }
            $fields_to_update = '';
            $meta_to_update = sl_import_meta_plan((int) $post->ID, $recipe_id, $source, $json_file);
            $import_action = 'WOULD_UPDATE_EXISTING';
            $notes = implode('; ', $review_notes);
            $summary['would_update_existing']++;
        }
    } elseif ($mapping_action === 'CREATE_NEW_PRIVATE') {
        $import_action = 'WOULD_CREATE_NEW_PRIVATE';
        $fields_to_update = 'new_post_title|new_post_name|post_type|post_status';
        $meta_to_update = sl_import_create_meta_plan($recipe_id, $source, $json_file);
        $title_policy = 'create_from_source_title';
        $slug_policy = 'create_from_expected_slug';
        $notes = 'post_type=dry_recipe; post_status=private; new_post_title=' . $source_title . '; new_slug=' . $expected_slug;
        $summary['would_create_new_private']++;
    } elseif ($mapping_action === 'AMBIGUOUS_REVIEW') {
        $import_action = 'BLOCKED_AMBIGUOUS_REVIEW';
        $blocked_reason = 'BLOCKED_AMBIGUOUS_REVIEW';
        $notes = 'manual mapping review required';
        $summary['blocked']++;
    } elseif ($mapping_action === 'SKIP') {
        $import_action = 'SKIP';
        $summary['skip']++;
    } else {
        $import_action = 'SKIP';
        $blocked_reason = 'UNKNOWN_MAPPING_ACTION';
        $summary['skip']++;
    }

    $row = array(
        $recipe_id,
        $source_title,
        $mapping_action,
        $import_action,
        $target_post_id,
        $current_title,
        $current_slug,
        $current_status,
        $expected_slug,
        $fields_to_update,
        $meta_to_update,
        $title_diff,
        $slug_diff,
        $title_policy,
        $slug_policy,
        $blocked_reason,
        $notes,
    );
    fputcsv($handle, $row);
    WP_CLI::line(implode(',', $row));
}

fclose($handle);

WP_CLI::line('');
WP_CLI::line('CSV report: ' . $import_csv_path);
WP_CLI::line('summary:');
WP_CLI::line('total: ' . $summary['total']);
WP_CLI::line('would_update_existing: ' . $summary['would_update_existing']);
WP_CLI::line('would_create_new_private: ' . $summary['would_create_new_private']);
WP_CLI::line('blocked: ' . $summary['blocked']);
WP_CLI::line('skip: ' . $summary['skip']);
WP_CLI::line('wordpress_write_allowed: no');
