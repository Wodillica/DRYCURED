<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$clone_id = (int) getenv('DC_3042_CLONE_ID');
$result_path = getenv('DC_3042_CLONE_RESULT');
$out_dir = getenv('DC_3042_QA_OUT');

if (!$clone_id || !$result_path || !$out_dir) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dcqa_json_file($path) {
    if (!is_readable($path)) {
        fwrite(STDERR, "FAIL: ne mogu čitati JSON: " . $path . "\n");
        exit(1);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fwrite(STDERR, "FAIL: JSON nije valjan: " . $path . "\n");
        exit(1);
    }
    return $data;
}

function dcqa_add(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

function dcqa_is_json($value) {
    if (!is_string($value) || trim($value) === '') {
        return false;
    }
    json_decode($value, true);
    return json_last_error() === JSON_ERROR_NONE;
}

$source_id = 3042;
$result = dcqa_json_file($result_path);

$clone = get_post($clone_id);
$source = get_post($source_id);

$checks = [];

dcqa_add($checks, 'clone_exists', 'Privatni clone postoji', (bool)$clone, 'BLOCKER', 'Clone ID mora postojati u WordPressu.');
dcqa_add($checks, 'source_exists', 'Javni source post postoji', (bool)$source, 'BLOCKER', 'Source post 3042 mora postojati.');

if (!$clone || !$source) {
    file_put_contents(rtrim($out_dir, '/') . '/3042_3535_readonly_qa_result.json', wp_json_encode([
        'qa_status' => 'FAIL',
        'checks' => $checks
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    echo "=== 3042 / 3535 PRIVATE CLONE READ-ONLY QA COMPLETE ===\n";
    echo "QA_STATUS=FAIL\n";
    echo "FAIL_TOTAL=1\n";
    exit(0);
}

$source_expected = $result['source_after'] ?? [];

$current_source = [
    'ID' => $source->ID,
    'post_title' => $source->post_title,
    'post_name' => $source->post_name,
    'post_status' => $source->post_status,
    'post_type' => $source->post_type,
    'post_modified_gmt' => $source->post_modified_gmt,
];

$source_unchanged_from_clone_creation = true;
foreach (['post_title', 'post_name', 'post_status', 'post_type', 'post_modified_gmt'] as $k) {
    if (array_key_exists($k, $source_expected) && (string)$source_expected[$k] !== (string)$current_source[$k]) {
        $source_unchanged_from_clone_creation = false;
    }
}

dcqa_add($checks, 'clone_private', 'Clone je private', $clone->post_status === 'private', 'BLOCKER', 'Clone ne smije biti publish.');
dcqa_add($checks, 'clone_type', 'Clone je dry_recipe', $clone->post_type === 'dry_recipe', 'BLOCKER', 'Clone mora biti dry_recipe.');
dcqa_add($checks, 'source_publish', 'Source 3042 je publish', $source->post_status === 'publish', 'BLOCKER', 'Source 3042 mora ostati javni publish recept.');
dcqa_add($checks, 'source_type', 'Source 3042 je dry_recipe', $source->post_type === 'dry_recipe', 'BLOCKER', 'Source 3042 mora ostati dry_recipe.');
dcqa_add($checks, 'source_unchanged', 'Source 3042 nije mijenjan od clone stvaranja', $source_unchanged_from_clone_creation, 'BLOCKER', 'Usporedba s result JSON-om iz trenutka clone stvaranja.');

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
    '_dry_recipe_full_markdown',
];

$meta_values = [];
foreach ($required_meta as $key) {
    $value = get_post_meta($clone_id, $key, true);
    $meta_values[$key] = $value;
    dcqa_add($checks, 'meta_' . $key, 'Meta postoji: ' . $key, $value !== '', 'MAJOR', 'Obvezni meta ključ za privatni preview clone.');
}

dcqa_add($checks, 'meta_source_id', 'Meta source ID je 3042', (string)$meta_values['_dry_recipe_preview_source_post_id'] === '3042', 'BLOCKER', 'Clone mora biti vezan na source post 3042.');
dcqa_add($checks, 'meta_public_update_0', 'Meta public update allowed je 0', (string)$meta_values['_dry_recipe_public_update_allowed'] === '0', 'BLOCKER', 'Privatni clone ne smije dopustiti javni update.');
dcqa_add($checks, 'meta_public_verified_0', 'Meta public verified je 0', (string)$meta_values['_dry_recipe_public_verified'] === '0', 'BLOCKER', 'Privatni clone ne smije biti public verified.');
dcqa_add($checks, 'meta_preview_mode', 'Meta preview mode je PRIVATE_CLONE_ONLY', (string)$meta_values['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY', 'BLOCKER', 'Meta mora jasno označiti privatni clone.');
dcqa_add($checks, 'meta_type_router', 'Meta type router je GROUND_MEAT_OR_CASING', (string)$meta_values['_dry_recipe_type_router'] === 'GROUND_MEAT_OR_CASING', 'MAJOR', 'Tipološka klasifikacija mora ostati kobasični model.');

$sections_json_ok = dcqa_is_json($meta_values['_dry_recipe_sections']);
$process_json_ok = dcqa_is_json($meta_values['_dry_verified_process']);
$blockers_json_ok = dcqa_is_json($meta_values['_dry_recipe_active_blockers']);

dcqa_add($checks, 'sections_json', '_dry_recipe_sections je valjan JSON', $sections_json_ok, 'MAJOR', 'Renderer/adapter očekuje čitljiv JSON.');
dcqa_add($checks, 'verified_process_json', '_dry_verified_process je valjan JSON', $process_json_ok, 'MAJOR', 'Proces mora biti čitljiv JSON.');
dcqa_add($checks, 'active_blockers_json', '_dry_recipe_active_blockers je valjan JSON', $blockers_json_ok, 'MAJOR', 'Blokade moraju biti čitljiv JSON.');

$full_md = (string)$meta_values['_dry_recipe_full_markdown'];
dcqa_add($checks, 'full_markdown_length', '_dry_recipe_full_markdown ima sadržaj', strlen($full_md) > 1000, 'MAJOR', 'Markdown mora sadržavati radni prikaz recepta.');
dcqa_add($checks, 'full_markdown_not_public', 'Markdown označava privatni preview', stripos($full_md, 'PRIVATNI PREVIEW') !== false || stripos($full_md, 'nije javni recept') !== false, 'MAJOR', 'Privatni sadržaj mora biti jasno označen u dosjeu/cloneu.');

$clone_url = get_permalink($clone_id);

$public_response = wp_remote_get($clone_url, [
    'timeout' => 8,
    'redirection' => 3,
    'sslverify' => false,
    'headers' => [
        'User-Agent' => 'DrycuredPrivateCloneReadOnlyQA/1.0'
    ]
]);

$http_code = '';
$public_body = '';
$public_error = '';

if (is_wp_error($public_response)) {
    $public_error = $public_response->get_error_message();
} else {
    $http_code = (string) wp_remote_retrieve_response_code($public_response);
    $public_body = (string) wp_remote_retrieve_body($public_response);
}

$title_exposed = false;
if ($public_body !== '') {
    $title_exposed = stripos($public_body, 'Jésus de Lyon') !== false || stripos($public_body, 'Jesus de Lyon') !== false;
}

$publicly_exposed = ($http_code === '200' && $title_exposed);

dcqa_add($checks, 'private_not_publicly_exposed', 'Privatni clone nije javno izložen kao recept', !$publicly_exposed, 'BLOCKER', 'Ako HTTP 200 javno prikazuje naslov recepta, private zaštita nije dobra.');

$rendered = '';
try {
    global $post;
    $old_post = $post ?? null;
    $post = $clone;
    setup_postdata($post);
    $rendered = apply_filters('the_content', $clone->post_content);
    wp_reset_postdata();
    $post = $old_post;
} catch (Throwable $e) {
    $rendered = 'RENDER_ERROR: ' . $e->getMessage();
}

file_put_contents(rtrim($out_dir, '/') . '/3535_private_clone_rendered_content_snapshot.html', $rendered);
file_put_contents(rtrim($out_dir, '/') . '/3535_public_fetch_snapshot.html', $public_body);

$render_contains_title = stripos($rendered, 'Jésus de Lyon') !== false || stripos($rendered, 'Jesus de Lyon') !== false;
dcqa_add($checks, 'render_snapshot_title', 'Render snapshot sadrži naslov', $render_contains_title, 'MINOR', 'Interni render snapshot treba imati osnovni sadržaj.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$qa_status = count($blocker_failures) === 0 && count($failures) === 0 ? 'PASS_READ_ONLY' : 'FAIL';

$result_out = [
    'qa_status' => $qa_status,
    'generated_at' => gmdate('c'),
    'source_post_id' => $source_id,
    'clone_id' => $clone_id,
    'clone_url' => $clone_url,
    'source_current' => $current_source,
    'source_expected_from_clone_creation' => $source_expected,
    'source_unchanged_from_clone_creation' => $source_unchanged_from_clone_creation,
    'clone' => [
        'ID' => $clone->ID,
        'post_title' => $clone->post_title,
        'post_name' => $clone->post_name,
        'post_status' => $clone->post_status,
        'post_type' => $clone->post_type,
        'post_modified_gmt' => $clone->post_modified_gmt,
    ],
    'public_fetch' => [
        'url' => $clone_url,
        'http_code' => $http_code,
        'error' => $public_error,
        'title_exposed' => $title_exposed,
        'publicly_exposed' => $publicly_exposed,
    ],
    'meta_values_preview' => $meta_values,
    'checks' => $checks,
    'fail_total' => count($failures),
    'blocker_fail_total' => count($blocker_failures),
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_private_clone_readonly_qa_result.json',
    wp_json_encode($result_out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_private_clone_readonly_qa_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$report = [];
$report[] = '# 3535 private clone read-only QA v1';
$report[] = '';
$report[] = 'Status: **' . $qa_status . '**';
$report[] = '';
$report[] = 'Ovaj QA ne mijenja WordPress. Provjerava privatni clone i zaštitu javnog posta 3042.';
$report[] = '';
$report[] = '## Sažetak';
$report[] = '';
$report[] = '- Source post ID: `3042`';
$report[] = '- Clone ID: `' . $clone_id . '`';
$report[] = '- Clone status: `' . $clone->post_status . '`';
$report[] = '- Clone URL: `' . $clone_url . '`';
$report[] = '- Source unchanged from clone creation: `' . ($source_unchanged_from_clone_creation ? 'true' : 'false') . '`';
$report[] = '- Public fetch HTTP code: `' . $http_code . '`';
$report[] = '- Publicly exposed: `' . ($publicly_exposed ? 'true' : 'false') . '`';
$report[] = '- Checks total: `' . count($checks) . '`';
$report[] = '- Fail total: `' . count($failures) . '`';
$report[] = '- Blocker fail total: `' . count($blocker_failures) . '`';
$report[] = '';
$report[] = '## QA tablica';
$report[] = '';
$report[] = '| Provjera | Status | Težina | Napomena |';
$report[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $report[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$report[] = '';
$report[] = '## Zaključak';
$report[] = '';
if ($qa_status === 'PASS_READ_ONLY') {
    $report[] = 'Privatni clone je tehnički ispravan za interni pregled. Javni post 3042 nije mijenjan i clone nije javno izložen kao recept.';
} else {
    $report[] = 'Privatni clone ima QA padove. Ne nastavljati dok se ne riješe.';
}
$report[] = '';
file_put_contents(rtrim($out_dir, '/') . '/3535_PRIVATE_CLONE_READONLY_QA_REPORT.md', implode("\n", $report));

echo "=== 3535 PRIVATE CLONE READ-ONLY QA COMPLETE ===\n";
echo "QA_STATUS=" . $qa_status . "\n";
echo "SOURCE_POST_ID=3042\n";
echo "SOURCE_UNCHANGED_FROM_CLONE_CREATION=" . ($source_unchanged_from_clone_creation ? 'true' : 'false') . "\n";
echo "CLONE_ID=" . $clone_id . "\n";
echo "CLONE_STATUS=" . $clone->post_status . "\n";
echo "CLONE_TYPE=" . $clone->post_type . "\n";
echo "PUBLIC_FETCH_HTTP_CODE=" . $http_code . "\n";
echo "PUBLICLY_EXPOSED=" . ($publicly_exposed ? 'true' : 'false') . "\n";
echo "FAIL_TOTAL=" . count($failures) . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_PRIVATE_CLONE_READONLY_QA_REPORT.md\n";
