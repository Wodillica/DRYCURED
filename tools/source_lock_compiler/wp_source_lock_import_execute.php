<?php
/**
 * Source-lock WordPress import execute, private-safe mode.
 *
 * Usage, after explicit operator approval:
 *   wp eval-file tools/source_lock_compiler/wp_source_lock_import_execute.php --path=/var/www/html --allow-root
 *
 * This script performs the first controlled source-lock import. Existing posts
 * receive source-lock meta only. Existing post_title, post_name, and post_status
 * are never changed. New posts are created only as private dry_recipe posts.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "This script must be run through WP-CLI eval-file with WordPress loaded.\n");
    exit(1);
}

$repo_root = dirname(__DIR__, 2);
$json_dir = $repo_root . '/build/source_locked_json';
$report_dir = $repo_root . '/server-reports/recipes';
$mapping_csv_path = $report_dir . '/source_lock_wp_mapping_dry_run_batch30.csv';
$import_plan_csv_path = $report_dir . '/source_lock_wp_import_dry_run_batch30.csv';
$execute_csv_path = $report_dir . '/source_lock_wp_import_execute_batch30.csv';
$post_type = 'dry_recipe';
$source_lock_meta_keys = array(
    'recipe_id',
    'source_lock_recipe_id',
    'dry_recipe_code',
    'source_lock_sha256',
    'source_lock_json_path',
    'source_lock_payload_json',
    'source_lock_imported_at',
    'source_lock_import_batch',
    'source_lock_import_mode',
);

function dc_sl_exec_array($value) {
    return is_array($value) ? $value : array();
}

function dc_sl_exec_clean_text($value) {
    return trim(wp_strip_all_tags((string) $value));
}

function dc_sl_exec_expected_slug($recipe_id) {
    return sanitize_title(strtolower((string) $recipe_id));
}

function dc_sl_exec_read_csv_assoc($path) {
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
        foreach (dc_sl_exec_array($header) as $index => $column) {
            $item[$column] = isset($row[$index]) ? $row[$index] : '';
        }
        if (!empty($item['recipe_id'])) {
            $rows[$item['recipe_id']] = $item;
        }
    }
    fclose($handle);
    return $rows;
}

function dc_sl_exec_source_lock_pass($source, &$blocked_reason) {
    $audit = isset($source['audit']) && is_array($source['audit']) ? $source['audit'] : array();
    $ingredient_status = isset($audit['ingredient_status']) ? (string) $audit['ingredient_status'] : '';
    $process_status = isset($audit['process_status']) ? (string) $audit['process_status'] : '';
    $overall_status = isset($audit['overall_status']) ? (string) $audit['overall_status'] : '';
    $contamination = dc_sl_exec_array($audit['process_contamination_errors'] ?? array());
    if ($ingredient_status !== 'PASS' || $process_status !== 'PASS' || $overall_status !== 'PASS' || count($contamination) > 0) {
        $blocked_reason = 'BLOCKED_SOURCE_LOCK_NOT_PASS';
        return false;
    }
    return true;
}

function dc_sl_exec_meta_payload($recipe_id, $source, $json_file, $mode, $raw_json) {
    $source_hash = isset($source['source']['sha256']) ? (string) $source['source']['sha256'] : '';
    return array(
        'recipe_id' => $recipe_id,
        'source_lock_recipe_id' => $recipe_id,
        'dry_recipe_code' => $recipe_id,
        'source_lock_sha256' => $source_hash,
        'source_lock_json_path' => $json_file,
        'source_lock_payload_json' => $raw_json,
        'source_lock_imported_at' => current_time('mysql', true),
        'source_lock_import_batch' => 'batch30',
        'source_lock_import_mode' => $mode,
    );
}

function dc_sl_exec_write_source_lock_meta($post_id, $meta_payload) {
    $written = array();
    foreach (dc_sl_exec_array($meta_payload) as $meta_key => $meta_value) {
        update_post_meta($post_id, $meta_key, $meta_value);
        $written[] = $meta_key;
    }
    return implode('|', $written);
}

function dc_sl_exec_plan_is_safe($mapping, $plan, $source, &$blocked_reason) {
    $mapping_action = isset($mapping['action']) ? (string) $mapping['action'] : '';
    $plan_action = isset($plan['import_dry_run_action']) ? (string) $plan['import_dry_run_action'] : '';
    $safe_match = isset($mapping['safe_match']) ? (string) $mapping['safe_match'] : '';

    if (!dc_sl_exec_source_lock_pass($source, $blocked_reason)) {
        return false;
    }
    if ($plan_action === 'WOULD_UPDATE_EXISTING') {
        if ($mapping_action !== 'UPDATE_EXISTING') {
            $blocked_reason = 'BLOCKED_MAPPING_PLAN_MISMATCH';
            return false;
        }
        if ($safe_match !== '1') {
            $blocked_reason = 'BLOCKED_UNSAFE_MAPPING';
            return false;
        }
        if (empty($plan['target_post_id'])) {
            $blocked_reason = 'BLOCKED_TARGET_POST_ID_MISSING';
            return false;
        }
    } elseif ($plan_action === 'WOULD_CREATE_NEW_PRIVATE') {
        if ($mapping_action !== 'CREATE_NEW_PRIVATE') {
            $blocked_reason = 'BLOCKED_MAPPING_PLAN_MISMATCH';
            return false;
        }
        if (!empty($plan['target_post_id'])) {
            $blocked_reason = 'BLOCKED_CREATE_HAS_TARGET_POST_ID';
            return false;
        }
    }
    return true;
}

function dc_sl_exec_preflight($json_files, $mapping_rows, $plan_rows) {
    $counts = array(
        'total' => 0,
        'would_update_existing' => 0,
        'would_create_new_private' => 0,
        'blocked' => 0,
        'skip' => 0,
        'errors' => 0,
    );
    foreach (dc_sl_exec_array($json_files) as $json_file) {
        $raw = file_get_contents($json_file);
        $source = json_decode($raw, true);
        $recipe_id = is_array($source) && !empty($source['recipe_id'])
            ? (string) $source['recipe_id']
            : basename($json_file, '.source_locked.json');
        $plan = isset($plan_rows[$recipe_id]) ? $plan_rows[$recipe_id] : null;
        $mapping = isset($mapping_rows[$recipe_id]) ? $mapping_rows[$recipe_id] : null;
        $blocked_reason = '';
        $counts['total']++;
        if (!is_array($source) || !$plan || !$mapping) {
            $counts['errors']++;
            continue;
        }
        if (!dc_sl_exec_plan_is_safe($mapping, $plan, $source, $blocked_reason)) {
            $counts['errors']++;
            continue;
        }
        $plan_action = (string) ($plan['import_dry_run_action'] ?? '');
        if ($plan_action === 'WOULD_UPDATE_EXISTING') {
            $counts['would_update_existing']++;
        } elseif ($plan_action === 'WOULD_CREATE_NEW_PRIVATE') {
            $counts['would_create_new_private']++;
        } elseif (strpos($plan_action, 'BLOCKED') === 0) {
            $counts['blocked']++;
        } elseif ($plan_action === 'SKIP') {
            $counts['skip']++;
        } else {
            $counts['errors']++;
        }
    }
    return $counts;
}

if (!is_dir($json_dir)) {
    fwrite(STDERR, "Missing source-lock JSON directory: {$json_dir}\n");
    exit(1);
}
if (!is_readable($mapping_csv_path)) {
    fwrite(STDERR, "Missing mapping CSV: {$mapping_csv_path}\n");
    exit(1);
}
if (!is_readable($import_plan_csv_path)) {
    fwrite(STDERR, "Missing import dry-run CSV: {$import_plan_csv_path}\n");
    exit(1);
}
if (!is_dir($report_dir) && !mkdir($report_dir, 0775, true) && !is_dir($report_dir)) {
    fwrite(STDERR, "Cannot create report directory: {$report_dir}\n");
    exit(1);
}

$mapping_rows = dc_sl_exec_read_csv_assoc($mapping_csv_path);
$plan_rows = dc_sl_exec_read_csv_assoc($import_plan_csv_path);
$json_files = glob($json_dir . '/*.source_locked.json');
if (!is_array($json_files)) {
    $json_files = array();
}
sort($json_files, SORT_STRING);
if (!$json_files) {
    fwrite(STDERR, "No source-lock JSON files found in: {$json_dir}\n");
    exit(1);
}

$preflight = dc_sl_exec_preflight($json_files, $mapping_rows, $plan_rows);
if (
    $preflight['total'] !== 30 ||
    $preflight['would_update_existing'] !== 16 ||
    $preflight['would_create_new_private'] !== 14 ||
    $preflight['blocked'] !== 0 ||
    $preflight['skip'] !== 0 ||
    $preflight['errors'] !== 0
) {
    fwrite(STDERR, "Preflight failed. Expected total=30, update=16, create=14, blocked=0, skip=0, errors=0.\n");
    fwrite(STDERR, "Actual: " . json_encode($preflight) . "\n");
    exit(1);
}

$handle = fopen($execute_csv_path, 'w');
if (!$handle) {
    fwrite(STDERR, "Cannot write CSV report: {$execute_csv_path}\n");
    exit(1);
}

$columns = array(
    'recipe_id',
    'source_title',
    'execute_action',
    'post_id',
    'post_title',
    'post_slug',
    'post_status',
    'meta_written',
    'blocked_reason',
    'notes',
);
fputcsv($handle, $columns);
WP_CLI::line(implode(',', $columns));

$summary = array(
    'total' => 0,
    'updated_meta_only' => 0,
    'created_private' => 0,
    'skipped_blocked' => 0,
    'skipped' => 0,
    'errors' => 0,
);

foreach (dc_sl_exec_array($json_files) as $json_file) {
    $raw = file_get_contents($json_file);
    $source = json_decode($raw, true);
    $recipe_id = is_array($source) && !empty($source['recipe_id'])
        ? (string) $source['recipe_id']
        : basename($json_file, '.source_locked.json');
    $source_title = is_array($source) ? dc_sl_exec_clean_text($source['title'] ?? '') : '';
    $expected_slug = dc_sl_exec_expected_slug($recipe_id);
    $mapping = isset($mapping_rows[$recipe_id]) ? $mapping_rows[$recipe_id] : null;
    $plan = isset($plan_rows[$recipe_id]) ? $plan_rows[$recipe_id] : null;
    $plan_action = $plan ? (string) ($plan['import_dry_run_action'] ?? '') : 'SKIP';
    $execute_action = 'SKIPPED';
    $post_id = '';
    $post_title = '';
    $post_slug = '';
    $post_status = '';
    $meta_written = '';
    $blocked_reason = '';
    $notes = '';
    $summary['total']++;

    if (!is_array($source) || !$mapping || !$plan) {
        $execute_action = 'SKIPPED';
        $blocked_reason = 'MISSING_SOURCE_MAPPING_OR_PLAN';
        $summary['errors']++;
    } elseif (!dc_sl_exec_plan_is_safe($mapping, $plan, $source, $blocked_reason)) {
        $execute_action = 'SKIPPED_BLOCKED';
        $summary['skipped_blocked']++;
    } elseif ($plan_action === 'WOULD_UPDATE_EXISTING') {
        $post_id = (string) ($plan['target_post_id'] ?? '');
        $post = $post_id !== '' ? get_post((int) $post_id) : null;
        if (!$post || $post->post_type !== $post_type) {
            $execute_action = 'SKIPPED_BLOCKED';
            $blocked_reason = 'BLOCKED_TARGET_POST_NOT_FOUND';
            $summary['skipped_blocked']++;
        } else {
            $meta_payload = dc_sl_exec_meta_payload($recipe_id, $source, $json_file, 'update_existing_private_safe', $raw);
            $meta_written = dc_sl_exec_write_source_lock_meta((int) $post->ID, $meta_payload);
            $execute_action = 'UPDATED_META_ONLY';
            $post_id = (string) $post->ID;
            $post_title = dc_sl_exec_clean_text($post->post_title);
            $post_slug = (string) $post->post_name;
            $post_status = (string) $post->post_status;
            $notes = 'existing title/slug/status preserved';
            $summary['updated_meta_only']++;
        }
    } elseif ($plan_action === 'WOULD_CREATE_NEW_PRIVATE') {
        $new_post_id = wp_insert_post(array(
            'post_type' => $post_type,
            'post_status' => 'private',
            'post_title' => $source_title,
            'post_name' => $expected_slug,
            'post_content' => 'Source-lock imported private draft placeholder.',
        ), true);
        if (is_wp_error($new_post_id)) {
            $execute_action = 'SKIPPED_BLOCKED';
            $blocked_reason = 'WP_INSERT_ERROR:' . $new_post_id->get_error_message();
            $summary['errors']++;
        } else {
            $post = get_post((int) $new_post_id);
            $meta_payload = dc_sl_exec_meta_payload($recipe_id, $source, $json_file, 'create_new_private', $raw);
            $meta_written = dc_sl_exec_write_source_lock_meta((int) $new_post_id, $meta_payload);
            $execute_action = 'CREATED_PRIVATE';
            $post_id = (string) $new_post_id;
            $post_title = $post ? dc_sl_exec_clean_text($post->post_title) : $source_title;
            $post_slug = $post ? (string) $post->post_name : $expected_slug;
            $post_status = $post ? (string) $post->post_status : 'private';
            $notes = 'new dry_recipe created private';
            $summary['created_private']++;
        }
    } elseif (strpos($plan_action, 'BLOCKED') === 0) {
        $execute_action = 'SKIPPED_BLOCKED';
        $blocked_reason = $plan_action;
        $summary['skipped_blocked']++;
    } elseif ($plan_action === 'SKIP') {
        $execute_action = 'SKIPPED';
        $summary['skipped']++;
    } else {
        $execute_action = 'SKIPPED';
        $blocked_reason = 'UNKNOWN_IMPORT_DRY_RUN_ACTION';
        $summary['errors']++;
    }

    $row = array(
        $recipe_id,
        $source_title,
        $execute_action,
        $post_id,
        $post_title,
        $post_slug,
        $post_status,
        $meta_written,
        $blocked_reason,
        $notes,
    );
    fputcsv($handle, $row);
    WP_CLI::line(implode(',', $row));
}

fclose($handle);

WP_CLI::line('');
WP_CLI::line('CSV report: ' . $execute_csv_path);
WP_CLI::line('summary:');
WP_CLI::line('total: ' . $summary['total']);
WP_CLI::line('updated_meta_only: ' . $summary['updated_meta_only']);
WP_CLI::line('created_private: ' . $summary['created_private']);
WP_CLI::line('skipped_blocked: ' . $summary['skipped_blocked']);
WP_CLI::line('skipped: ' . $summary['skipped']);
WP_CLI::line('errors: ' . $summary['errors']);
WP_CLI::line('wordpress_write_performed: yes');
