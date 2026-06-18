<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$plan_path = getenv('DC_3042_PLAN_JSON');
$meta_path = getenv('DC_3042_META_JSON');
$markdown_path = getenv('DC_3042_FULL_MARKDOWN');
$out_dir = getenv('DC_3042_CLONE_OUT');

if (!$plan_path || !$meta_path || !$markdown_path || !$out_dir) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc3042_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dc3042_json_read($path) {
    if (!is_readable($path)) {
        dc3042_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dc3042_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dc3042_slug($text) {
    $text = html_entity_decode((string)$text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $map = [
        'š'=>'s','Š'=>'S','č'=>'c','Č'=>'C','ć'=>'c','Ć'=>'C','ž'=>'z','Ž'=>'Z','đ'=>'d','Đ'=>'D',
        'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','à'=>'a','á'=>'a','â'=>'a','ä'=>'a',
        'ô'=>'o','ö'=>'o','ó'=>'o','ò'=>'o','û'=>'u','ü'=>'u','ù'=>'u','ú'=>'u',
        'î'=>'i','ï'=>'i','í'=>'i','ì'=>'i','ç'=>'c','œ'=>'oe','æ'=>'ae'
    ];
    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'private-preview';
}

$plan = dc3042_json_read($plan_path);
$meta_map = dc3042_json_read($meta_path);
$full_markdown = file_get_contents($markdown_path);

if (!is_string($full_markdown) || trim($full_markdown) === '') {
    dc3042_fail("full markdown je prazan.");
}

$source_id = 3042;
$source = get_post($source_id);
if (!$source) {
    dc3042_fail("source post 3042 ne postoji.");
}

$source_before = [
    'ID' => $source->ID,
    'post_title' => $source->post_title,
    'post_name' => $source->post_name,
    'post_status' => $source->post_status,
    'post_type' => $source->post_type,
    'post_modified_gmt' => $source->post_modified_gmt,
];

if ($source->post_type !== 'dry_recipe') {
    dc3042_fail("source post 3042 nije dry_recipe.");
}

if ($source->post_status !== 'publish') {
    dc3042_fail("source post 3042 nije publish; zaštita prekida postupak.");
}

if (($plan['plan_status'] ?? '') !== 'DRY_RUN_ONLY_NO_WORDPRESS_WRITE') {
    dc3042_fail("plan_status nije očekivan.");
}

if (($plan['source_public_post']['write_allowed'] ?? null) !== false) {
    dc3042_fail("plan ne štiti source post od upisa.");
}

if (($plan['future_private_clone']['required_post_status'] ?? '') !== 'private') {
    dc3042_fail("plan ne zahtijeva private status.");
}

if (($plan['meta_write_scope_for_future_step']['allowed_target'] ?? '') !== 'FUTURE_PRIVATE_CLONE_ONLY') {
    dc3042_fail("plan target nije FUTURE_PRIVATE_CLONE_ONLY.");
}

if (($plan['meta_write_scope_for_future_step']['source_post_meta_write_allowed'] ?? null) !== false) {
    dc3042_fail("plan dopušta meta write na source post; prekid.");
}

$required_meta = [
    '_dry_recipe_preview_mode',
    '_dry_recipe_preview_source_post_id',
    '_dry_recipe_public_update_allowed',
    '_dry_recipe_dossier_status',
    '_dry_recipe_public_verified',
    '_dry_recipe_source_validation_status',
    '_dry_recipe_type_router',
    '_dry_recipe_adapter_payload_version',
    '_dry_recipe_dossier_path',
    '_dry_recipe_active_blockers',
    '_dry_recipe_sections',
    '_dry_verified_process',
    '_dry_recipe_full_markdown'
];

foreach ($required_meta as $k) {
    if (!array_key_exists($k, $meta_map)) {
        dc3042_fail("meta mapa nema obvezni key: " . $k);
    }
}

if ((string)$meta_map['_dry_recipe_preview_source_post_id'] !== '3042') {
    dc3042_fail("meta mapa ne pokazuje na source post 3042.");
}

if ((string)$meta_map['_dry_recipe_public_update_allowed'] !== '0') {
    dc3042_fail("meta mapa mora imati _dry_recipe_public_update_allowed=0.");
}

if ((string)$meta_map['_dry_recipe_public_verified'] !== '0') {
    dc3042_fail("meta mapa mora imati _dry_recipe_public_verified=0.");
}

$existing = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => 'private',
    'numberposts' => 5,
    'meta_query' => [
        [
            'key' => '_dry_recipe_preview_source_post_id',
            'value' => '3042',
            'compare' => '='
        ],
        [
            'key' => '_dry_recipe_adapter_payload_version',
            'value' => '3042_private_preview_adapter_dryrun_v1',
            'compare' => '='
        ],
    ],
    'orderby' => 'ID',
    'order' => 'DESC',
]);

$mode = 'CREATED_NEW_PRIVATE_CLONE';
$clone_id = 0;
$clone_title = 'PRIVATE PREVIEW — ' . ($plan['future_private_clone']['title'] ?? 'Jésus de Lyon – debela suha kobasica');
$clone_title = preg_replace('/^PRIVATE PREVIEW — PRIVATE PREVIEW — /u', 'PRIVATE PREVIEW — ', $clone_title);
$clone_slug = 'private-preview-3042-jesus-de-lyon-dossier-only-' . gmdate('Ymd-His');

if (!empty($existing)) {
    $mode = 'EXISTING_PRIVATE_CLONE_FOUND_NO_WRITE';
    $clone_id = (int)$existing[0]->ID;
} else {
    $clone_id = wp_insert_post([
        'post_type' => 'dry_recipe',
        'post_status' => 'private',
        'post_title' => $clone_title,
        'post_name' => $clone_slug,
        'post_content' => $full_markdown,
        'post_excerpt' => 'Privatni preview dosjea 3042. Nije javni recept.',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    ], true);

    if (is_wp_error($clone_id)) {
        dc3042_fail("wp_insert_post nije uspio: " . $clone_id->get_error_message());
    }

    $clone_id = (int)$clone_id;

    if ($clone_id === $source_id) {
        dc3042_fail("ZAŠTITA: clone_id je jednak source_id.");
    }

    foreach ($meta_map as $key => $value) {
        if ($clone_id === $source_id) {
            dc3042_fail("ZAŠTITA: pokušaj meta upisa u source post.");
        }
        update_post_meta($clone_id, $key, is_array($value) ? wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$value);
    }

    update_post_meta($clone_id, '_dry_recipe_private_clone_created_at_gmt', gmdate('c'));
    update_post_meta($clone_id, '_dry_recipe_private_clone_guard', 'SOURCE_3042_NOT_MODIFIED');
}

$clone = get_post($clone_id);
if (!$clone) {
    dc3042_fail("clone post ne postoji nakon postupka.");
}

if ($clone->post_status !== 'private') {
    dc3042_fail("clone nije private.");
}

if ($clone->post_type !== 'dry_recipe') {
    dc3042_fail("clone nije dry_recipe.");
}

$source_after = get_post($source_id);
$source_after_data = [
    'ID' => $source_after->ID,
    'post_title' => $source_after->post_title,
    'post_name' => $source_after->post_name,
    'post_status' => $source_after->post_status,
    'post_type' => $source_after->post_type,
    'post_modified_gmt' => $source_after->post_modified_gmt,
];

$source_unchanged = (
    $source_before['post_title'] === $source_after_data['post_title'] &&
    $source_before['post_name'] === $source_after_data['post_name'] &&
    $source_before['post_status'] === $source_after_data['post_status'] &&
    $source_before['post_type'] === $source_after_data['post_type'] &&
    $source_before['post_modified_gmt'] === $source_after_data['post_modified_gmt']
);

if (!$source_unchanged) {
    dc3042_fail("ZAŠTITA: source post 3042 se promijenio. Prekid.");
}

$meta_check = [];
foreach ($required_meta as $k) {
    $meta_check[$k] = get_post_meta($clone_id, $k, true);
}

$result = [
    'mode' => $mode,
    'source_post_unchanged' => $source_unchanged,
    'source_before' => $source_before,
    'source_after' => $source_after_data,
    'clone' => [
        'ID' => $clone_id,
        'post_title' => $clone->post_title,
        'post_name' => $clone->post_name,
        'post_status' => $clone->post_status,
        'post_type' => $clone->post_type,
        'permalink' => get_permalink($clone_id),
        'edit_link' => get_edit_post_link($clone_id, 'raw'),
    ],
    'meta_keys_written_or_confirmed' => array_keys($meta_check),
    'required_meta_check' => $meta_check,
    'public_update_allowed' => false,
    'source_write_allowed' => false,
];

file_put_contents(
    rtrim($out_dir, '/') . '/3042_private_preview_clone_result.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$report = [];
$report[] = '# 3042 private preview clone v1';
$report[] = '';
$report[] = 'Status: **' . $mode . '**';
$report[] = '';
$report[] = 'Ovaj korak smije stvoriti ili pronaći samo privatni clone. Javni post 3042 ne smije biti mijenjan.';
$report[] = '';
$report[] = '## Rezultat';
$report[] = '';
$report[] = '- Source post unchanged: `' . ($source_unchanged ? 'true' : 'false') . '`';
$report[] = '- Source post write allowed: `false`';
$report[] = '- Public update allowed: `false`';
$report[] = '- Clone ID: `' . $clone_id . '`';
$report[] = '- Clone status: `' . $clone->post_status . '`';
$report[] = '- Clone type: `' . $clone->post_type . '`';
$report[] = '- Clone permalink: `' . get_permalink($clone_id) . '`';
$report[] = '- Clone edit link: `' . get_edit_post_link($clone_id, 'raw') . '`';
$report[] = '';
$report[] = '## Meta keys';
$report[] = '';
foreach (array_keys($meta_check) as $k) {
    $report[] = '- `' . $k . '`';
}
$report[] = '';
$report[] = '## Zaključak';
$report[] = '';
$report[] = 'Privatni clone je spreman za pregled. Javni post 3042 nije mijenjan.';
file_put_contents(rtrim($out_dir, '/') . '/3042_PRIVATE_PREVIEW_CLONE_REPORT.md', implode("\n", $report) . "\n");

echo "=== 3042 PRIVATE PREVIEW CLONE COMPLETE ===\n";
echo "MODE=" . $mode . "\n";
echo "SOURCE_POST_UNCHANGED=" . ($source_unchanged ? 'true' : 'false') . "\n";
echo "SOURCE_POST_WRITE_ALLOWED=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "CLONE_ID=" . $clone_id . "\n";
echo "CLONE_STATUS=" . $clone->post_status . "\n";
echo "CLONE_URL=" . get_permalink($clone_id) . "\n";
echo "CLONE_EDIT_LINK=" . get_edit_post_link($clone_id, 'raw') . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3042_PRIVATE_PREVIEW_CLONE_REPORT.md\n";
