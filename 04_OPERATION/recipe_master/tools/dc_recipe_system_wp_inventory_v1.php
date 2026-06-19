<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_RECIPE_SYSTEM_INVENTORY_OUT');
if (!$out_dir) {
    fwrite(STDERR, "FAIL: nedostaje DC_RECIPE_SYSTEM_INVENTORY_OUT.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

$posts = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => ['publish', 'private', 'draft', 'pending', 'future'],
    'numberposts' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);

$post_csv = rtrim($out_dir, '/') . '/wp_recipe_posts_inventory.csv';
$meta_csv = rtrim($out_dir, '/') . '/wp_recipe_meta_inventory.csv';
$summary_json = rtrim($out_dir, '/') . '/wp_recipe_inventory_summary.json';

$post_fp = fopen($post_csv, 'w');
fputcsv($post_fp, [
    'ID',
    'post_title',
    'post_name',
    'post_status',
    'post_type',
    'permalink',
    'modified_gmt',
    'is_preview_clone',
    'preview_source_post_id',
    'recipe_id',
    'has_full_markdown',
    'has_sections',
    'has_verified_process',
    'has_image_url',
    'full_markdown_len',
    'sections_len',
    'verified_process_len',
    'image_url_len'
]);

$meta_fp = fopen($meta_csv, 'w');
fputcsv($meta_fp, [
    'ID',
    'post_title',
    'post_status',
    'meta_key',
    'meta_length',
    'json_valid',
    'preview'
]);

$status_counts = [];
$preview_clone_total = 0;
$missing_sections_total = 0;
$missing_verified_process_total = 0;
$missing_full_markdown_total = 0;
$missing_image_total = 0;

$meta_keys = [
    '_dry_recipe_id',
    '_dry_recipe_full_markdown',
    '_dry_recipe_sections',
    '_dry_verified_process',
    '_dry_recipe_image_url',
    '_dry_recipe_preview_mode',
    '_dry_recipe_preview_source_post_id',
    '_dry_recipe_public_update_allowed',
    '_dry_recipe_public_verified',
    '_dry_recipe_private_clone_status',
];

foreach ($posts as $p) {
    $id = (int)$p->ID;

    $status_counts[$p->post_status] = ($status_counts[$p->post_status] ?? 0) + 1;

    $recipe_id = get_post_meta($id, '_dry_recipe_id', true);
    $full_md = get_post_meta($id, '_dry_recipe_full_markdown', true);
    $sections = get_post_meta($id, '_dry_recipe_sections', true);
    $process = get_post_meta($id, '_dry_verified_process', true);
    $image = get_post_meta($id, '_dry_recipe_image_url', true);
    $preview_mode = get_post_meta($id, '_dry_recipe_preview_mode', true);
    $preview_source = get_post_meta($id, '_dry_recipe_preview_source_post_id', true);

    $is_preview = ($preview_mode === 'PRIVATE_CLONE_ONLY') || ($preview_source !== '') || stripos($p->post_title, 'PREVIEW') !== false || stripos($p->post_title, 'Privatno: PREVIEW') !== false;

    if ($is_preview) {
        $preview_clone_total++;
    }

    $has_full = is_string($full_md) && strlen($full_md) > 500;
    $has_sections = is_string($sections) && strlen($sections) > 200;
    $has_process = is_string($process) && strlen($process) > 200;
    $has_image = is_string($image) && trim($image) !== '';

    if (!$has_full) {
        $missing_full_markdown_total++;
    }
    if (!$has_sections) {
        $missing_sections_total++;
    }
    if (!$has_process) {
        $missing_verified_process_total++;
    }
    if (!$has_image) {
        $missing_image_total++;
    }

    fputcsv($post_fp, [
        $id,
        $p->post_title,
        $p->post_name,
        $p->post_status,
        $p->post_type,
        get_permalink($id),
        $p->post_modified_gmt,
        $is_preview ? 'yes' : 'no',
        is_string($preview_source) ? $preview_source : '',
        is_string($recipe_id) ? $recipe_id : '',
        $has_full ? 'yes' : 'no',
        $has_sections ? 'yes' : 'no',
        $has_process ? 'yes' : 'no',
        $has_image ? 'yes' : 'no',
        is_string($full_md) ? strlen($full_md) : 0,
        is_string($sections) ? strlen($sections) : 0,
        is_string($process) ? strlen($process) : 0,
        is_string($image) ? strlen($image) : 0,
    ]);

    foreach ($meta_keys as $key) {
        $v = get_post_meta($id, $key, true);
        $len = is_string($v) ? strlen($v) : 0;
        $json_valid = false;
        if (is_string($v) && $v !== '') {
            json_decode($v, true);
            $json_valid = json_last_error() === JSON_ERROR_NONE;
        }

        fputcsv($meta_fp, [
            $id,
            $p->post_title,
            $p->post_status,
            $key,
            $len,
            $json_valid ? 'yes' : 'no',
            is_string($v) ? mb_substr(preg_replace('/\s+/', ' ', $v), 0, 300) : '',
        ]);
    }
}

fclose($post_fp);
fclose($meta_fp);

$summary = [
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_WP_RECIPE_INVENTORY',
    'wordpress_write_allowed' => false,
    'public_recipe_update_allowed' => false,
    'total_dry_recipe_posts' => count($posts),
    'status_counts' => $status_counts,
    'preview_clone_total' => $preview_clone_total,
    'missing_sections_total' => $missing_sections_total,
    'missing_verified_process_total' => $missing_verified_process_total,
    'missing_full_markdown_total' => $missing_full_markdown_total,
    'missing_image_total' => $missing_image_total,
    'outputs' => [
        'wp_recipe_posts_inventory_csv' => $post_csv,
        'wp_recipe_meta_inventory_csv' => $meta_csv,
        'wp_recipe_inventory_summary_json' => $summary_json,
    ],
];

file_put_contents($summary_json, wp_json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "=== WP RECIPE INVENTORY COMPLETE ===\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "PUBLIC_RECIPE_UPDATE_ALLOWED=false\n";
echo "TOTAL_DRY_RECIPE_POSTS=" . count($posts) . "\n";
echo "PREVIEW_CLONE_TOTAL={$preview_clone_total}\n";
echo "MISSING_SECTIONS_TOTAL={$missing_sections_total}\n";
echo "MISSING_VERIFIED_PROCESS_TOTAL={$missing_verified_process_total}\n";
echo "MISSING_FULL_MARKDOWN_TOTAL={$missing_full_markdown_total}\n";
echo "MISSING_IMAGE_TOTAL={$missing_image_total}\n";
foreach ($status_counts as $status => $count) {
    echo "STATUS_" . strtoupper($status) . "={$count}\n";
}
echo "POST_CSV={$post_csv}\n";
echo "META_CSV={$meta_csv}\n";
echo "SUMMARY_JSON={$summary_json}\n";
