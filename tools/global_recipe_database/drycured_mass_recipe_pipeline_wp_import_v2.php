<?php
/**
 * DRYCURED MASS RECIPE PIPELINE v2.0 WP import bridge.
 *
 * This script is intentionally conservative. It defaults to DRY_RUN and exits
 * unless called with --execute-private after the Python DRY_RUN reports are
 * reviewed. It never publishes recipes and never changes public posts.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run through WP-CLI eval-file with WordPress loaded.\n");
    exit(1);
}

$repo_root = dirname(__DIR__, 2);
$batch_dir = $repo_root . '/server-reports/recipes/mass-pipeline-v2/batch_001';
$dry_run_table = $batch_dir . '/BATCH_001_DRY_RUN_TABLE.csv';
$taxonomy_table = $batch_dir . '/BATCH_001_TAXONOMY_MAPPING.csv';
$meta_table = $batch_dir . '/BATCH_001_META_MAPPING.csv';
$execute_table = $batch_dir . '/BATCH_001_PRIVATE_EXECUTE_TABLE.csv';
$created_posts = $batch_dir . '/BATCH_001_CREATED_POSTS.csv';
$backup_dir = $batch_dir . '/backups';
$post_type = 'dry_recipe';
$allowed_execute = in_array('--execute-private', $argv ?? array(), true);

function dc_mass_array($value) {
    return is_array($value) ? $value : array();
}

function dc_mass_csv_rows($path) {
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
        foreach (dc_mass_array($header) as $index => $column) {
            $item[$column] = isset($row[$index]) ? $row[$index] : '';
        }
        $rows[] = $item;
    }
    fclose($handle);
    return $rows;
}

function dc_mass_set_meta($post_id, $meta) {
    $written = array();
    foreach (dc_mass_array($meta) as $key => $value) {
        update_post_meta($post_id, $key, $value);
        $written[] = $key;
    }
    return implode('|', $written);
}

function dc_mass_write_csv($path, $rows, $header) {
    $handle = fopen($path, 'w');
    if (!$handle) {
        fwrite(STDERR, "Cannot write CSV: {$path}\n");
        exit(1);
    }
    fputcsv($handle, $header);
    foreach (dc_mass_array($rows) as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
}

function dc_mass_group_rows($rows, $key) {
    $grouped = array();
    foreach (dc_mass_array($rows) as $row) {
        $value = $row[$key] ?? '';
        if ($value === '') {
            continue;
        }
        if (!isset($grouped[$value])) {
            $grouped[$value] = array();
        }
        $grouped[$value][] = $row;
    }
    return $grouped;
}

if (!$allowed_execute) {
    WP_CLI::line('DRY_RUN_ONLY: pass --execute-private after human review to run private import.');
    WP_CLI::line('wordpress_write_performed: NO');
    return;
}

if (!is_readable($dry_run_table)) {
    fwrite(STDERR, "Missing DRY_RUN table: {$dry_run_table}\n");
    exit(1);
}

if (!is_dir($backup_dir) && !mkdir($backup_dir, 0775, true) && !is_dir($backup_dir)) {
    fwrite(STDERR, "Cannot create backup directory: {$backup_dir}\n");
    exit(1);
}

$timestamp = gmdate('Ymd_His');
$backup_path = $backup_dir . "/before_batch_001_private_import_{$timestamp}.sql";
WP_CLI::runcommand('db export ' . escapeshellarg($backup_path) . ' --allow-root');

$rows = dc_mass_csv_rows($dry_run_table);
$taxonomy_by_recipe = dc_mass_group_rows(dc_mass_csv_rows($taxonomy_table), 'recipe_id');
$meta_by_recipe = dc_mass_group_rows(dc_mass_csv_rows($meta_table), 'recipe_id');
$execute_rows = array();
$created_rows = array();
foreach (dc_mass_array($rows) as $row) {
    $recipe_id = $row['recipe_id'] ?? '';
    $action = $row['dry_run_action'] ?? '';
    if ($action === 'BLOCKED_REVIEW') {
        $execute_rows[] = array($recipe_id, 'SKIPPED_BLOCKED', '', '', 'blocked in DRY_RUN');
        continue;
    }
    if ($action !== 'WOULD_CREATE_PRIVATE') {
        $execute_rows[] = array($recipe_id, 'SKIPPED', '', '', 'unsupported dry_run_action');
        continue;
    }
    if (($row['public_publish_allowed'] ?? 'false') !== 'false') {
        $execute_rows[] = array($recipe_id, 'SKIPPED_BLOCKED', '', '', 'drycured public publish guard failed');
        continue;
    }
    $meta = array();
    foreach (dc_mass_array($meta_by_recipe[$recipe_id] ?? array()) as $meta_row) {
        $meta_key = $meta_row['meta_key'] ?? '';
        if ($meta_key === '') {
            continue;
        }
        $meta[$meta_key] = $meta_row['meta_value_preview'] ?? '';
    }
    if (($meta['drycured_public_publish_allowed'] ?? 'false') !== 'false') {
        $execute_rows[] = array($recipe_id, 'SKIPPED_BLOCKED', '', '', 'drycured_public_publish_allowed=false check failed');
        continue;
    }
    $post_id = wp_insert_post(array(
        'post_type' => $post_type,
        'post_status' => 'private',
        'post_title' => $row['title'] ?? '',
        'post_name' => $row['slug'] ?? '',
        'post_content' => 'Private batch draft placeholder.',
    ), true);
    if (is_wp_error($post_id)) {
        $execute_rows[] = array($recipe_id, 'ERROR', '', '', $post_id->get_error_message());
        continue;
    }
    $meta_written = dc_mass_set_meta((int) $post_id, $meta);
    $terms_set = array();
    foreach (dc_mass_array($taxonomy_by_recipe[$recipe_id] ?? array()) as $term_row) {
        $taxonomy = $term_row['taxonomy'] ?? '';
        $term_name = $term_row['term_name'] ?? '';
        if ($taxonomy === '' || $term_name === '' || $term_name === 'not_specified_in_source' || $term_name === 'needs_editorial_review') {
            continue;
        }
        wp_set_object_terms((int) $post_id, $term_name, $taxonomy, true);
        $terms_set[] = $taxonomy . ':' . $term_name;
    }
    $execute_rows[] = array($recipe_id, 'CREATED_PRIVATE', $post_id, $meta_written, implode('|', $terms_set));
    $created_rows[] = array($recipe_id, $post_id, $row['title'] ?? '', 'private', '');
}
dc_mass_write_csv($execute_table, $execute_rows, array('recipe_id', 'execute_action', 'post_id', 'meta_written', 'terms_set'));
dc_mass_write_csv($created_posts, $created_rows, array('recipe_id', 'post_id', 'post_title', 'post_status', 'notes'));
WP_CLI::line('created_private: ' . count($created_rows));
WP_CLI::line('wordpress_write_performed: YES_PRIVATE_ONLY');
