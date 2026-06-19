<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$payload_path = getenv('DC_2697_PAYLOAD_JSON');
$out_dir = getenv('DC_2697_PRIVATE_CLONE_OUT');
$qa_path = getenv('DC_2697_QA');
$readme_path = getenv('DC_2697_README');
$wplog_path = getenv('DC_2697_WPLOG');

if (!$payload_path || !$out_dir || !$qa_path || !$readme_path || !$wplog_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_file($payload_path)) {
    fwrite(STDERR, "FAIL: payload ne postoji: $payload_path\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc2697_hash_source_state($post_id) {
    $p = get_post($post_id);
    if (!$p) return null;

    $keys = [
        '_dry_recipe_id',
        '_dry_recipe_full_markdown',
        '_dry_recipe_sections',
        '_dry_verified_process',
        '_dry_recipe_image_url'
    ];

    $meta = [];
    foreach ($keys as $k) {
        $v = get_post_meta($post_id, $k, true);
        $meta[$k] = is_string($v) ? hash('sha256', $v) : '';
    }

    return [
        'ID' => (int)$p->ID,
        'post_title' => $p->post_title,
        'post_name' => $p->post_name,
        'post_status' => $p->post_status,
        'post_type' => $p->post_type,
        'post_content_hash' => hash('sha256', (string)$p->post_content),
        'meta_hashes' => $meta,
    ];
}

function dc2697_append_once($path, $marker, $block) {
    $old = file_get_contents($path);
    if (strpos($old, $marker) === false) {
        file_put_contents($path, rtrim($old) . "\n\n" . trim($block) . "\n");
    }
}

function dc2697_public_fetch($url) {
    $res = wp_remote_get($url, [
        'timeout' => 12,
        'redirection' => 5,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Drycured2697PrivateCloneQA/1.0'
        ],
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'http_code' => '',
            'error' => $res->get_error_message(),
            'body_length' => 0,
            'plain_excerpt' => '',
            'publicly_exposed' => false,
        ];
    }

    $code = (int)wp_remote_retrieve_response_code($res);
    $body = (string)wp_remote_retrieve_body($res);
    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($body)));

    $exposed = false;
    if ($code === 200 && (
        stripos($plain, 'Baranjska kobasica') !== false ||
        stripos($plain, 'HR-BR-2697-BARANJSKA-LJUTA-KOBASICA') !== false ||
        stripos($plain, '9,09 kg') !== false ||
        stripos($plain, '3–4 dima') !== false ||
        stripos($plain, '3-4 dima') !== false
    )) {
        $exposed = true;
    }

    return [
        'ok' => true,
        'http_code' => $code,
        'error' => '',
        'body_length' => strlen($body),
        'plain_excerpt' => mb_substr($plain, 0, 1200),
        'publicly_exposed' => $exposed,
    ];
}

$payload = json_decode(file_get_contents($payload_path), true);
if (!is_array($payload)) {
    fwrite(STDERR, "FAIL: payload nije validan JSON.\n");
    exit(1);
}

$source_post_id = 2697;
$source_before = dc2697_hash_source_state($source_post_id);

if (!$source_before) {
    fwrite(STDERR, "FAIL: source post 2697 ne postoji.\n");
    exit(1);
}

if (($payload['post_id'] ?? null) !== 2697) {
    fwrite(STDERR, "FAIL: payload post_id nije 2697.\n");
    exit(1);
}

if (($payload['public_update_allowed'] ?? true) !== false) {
    fwrite(STDERR, "FAIL: payload public_update_allowed nije false.\n");
    exit(1);
}

if (($payload['source_post_write_allowed'] ?? true) !== false) {
    fwrite(STDERR, "FAIL: payload source_post_write_allowed nije false.\n");
    exit(1);
}

$meta = $payload['meta_to_apply_to_private_clone_only'] ?? [];
if (!is_array($meta) || empty($meta)) {
    fwrite(STDERR, "FAIL: payload nema meta_to_apply_to_private_clone_only.\n");
    exit(1);
}

$clone_title = 'PREVIEW — ' . ($payload['title_hr'] ?? 'Baranjska kobasica – ljuta varijanta');
$clone_content = $meta['_dry_recipe_full_markdown'] ?? '';
if (!is_string($clone_content) || strlen($clone_content) < 1000) {
    fwrite(STDERR, "FAIL: full markdown u payloadu je prekratak.\n");
    exit(1);
}

$clone_id = wp_insert_post([
    'post_type' => 'dry_recipe',
    'post_status' => 'private',
    'post_title' => $clone_title,
    'post_name' => 'preview-2697-baranjska-kobasica-ljuta-varijanta-' . gmdate('Ymd-His'),
    'post_content' => $clone_content,
    'post_excerpt' => 'Privatni radni preview za Baranjsku kobasicu – ljutu varijantu. Javni update nije dopušten.',
], true);

if (is_wp_error($clone_id)) {
    fwrite(STDERR, "FAIL: wp_insert_post error: " . $clone_id->get_error_message() . "\n");
    exit(1);
}

$clone_id = (int)$clone_id;

foreach ($meta as $k => $v) {
    update_post_meta($clone_id, $k, is_scalar($v) ? (string)$v : wp_json_encode($v));
}

update_post_meta($clone_id, '_dry_recipe_preview_created_at', gmdate('c'));
update_post_meta($clone_id, '_dry_recipe_preview_label', '2697 Baranjska kobasica – ljuta varijanta');
update_post_meta($clone_id, '_dry_recipe_public_update_allowed', '0');
update_post_meta($clone_id, '_dry_recipe_public_verified', '0');
update_post_meta($clone_id, '_dry_recipe_source_post_unchanged_required', '1');
update_post_meta($clone_id, '_dry_recipe_private_clone_status', 'PRIVATE_CLONE_CREATED_QA_PENDING');

$source_after = dc2697_hash_source_state($source_post_id);
$source_unchanged = ($source_before === $source_after);

$admin_preview_url = site_url('/?post_type=dry_recipe&p=' . $clone_id);
$admin_edit_url = admin_url('post.php?post=' . $clone_id . '&action=edit');

$public_fetch = dc2697_public_fetch($admin_preview_url);

$clone = get_post($clone_id);
$clone_meta = [
    '_dry_recipe_preview_mode' => get_post_meta($clone_id, '_dry_recipe_preview_mode', true),
    '_dry_recipe_preview_source_post_id' => get_post_meta($clone_id, '_dry_recipe_preview_source_post_id', true),
    '_dry_recipe_public_update_allowed' => get_post_meta($clone_id, '_dry_recipe_public_update_allowed', true),
    '_dry_recipe_public_verified' => get_post_meta($clone_id, '_dry_recipe_public_verified', true),
    '_dry_recipe_id' => get_post_meta($clone_id, '_dry_recipe_id', true),
    '_dry_recipe_sections_length' => strlen((string)get_post_meta($clone_id, '_dry_recipe_sections', true)),
    '_dry_verified_process_length' => strlen((string)get_post_meta($clone_id, '_dry_verified_process', true)),
    '_dry_recipe_full_markdown_length' => strlen((string)get_post_meta($clone_id, '_dry_recipe_full_markdown', true)),
];

$checks = [];

$checks[] = ['key' => 'clone_created', 'status' => $clone ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Private clone mora biti kreiran.'];
$checks[] = ['key' => 'clone_private', 'status' => ($clone && $clone->post_status === 'private') ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Clone mora biti private.'];
$checks[] = ['key' => 'clone_type', 'status' => ($clone && $clone->post_type === 'dry_recipe') ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Clone mora biti dry_recipe.'];
$checks[] = ['key' => 'source_unchanged', 'status' => $source_unchanged ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Source post 2697 ne smije biti promijenjen.'];
$checks[] = ['key' => 'preview_mode', 'status' => $clone_meta['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY' ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Preview mode mora biti PRIVATE_CLONE_ONLY.'];
$checks[] = ['key' => 'source_link', 'status' => $clone_meta['_dry_recipe_preview_source_post_id'] === '2697' ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Clone mora biti vezan na source 2697.'];
$checks[] = ['key' => 'public_update_zero', 'status' => $clone_meta['_dry_recipe_public_update_allowed'] === '0' ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Public update mora biti 0.'];
$checks[] = ['key' => 'public_verified_zero', 'status' => $clone_meta['_dry_recipe_public_verified'] === '0' ? 'PASS' : 'FAIL', 'severity' => 'MAJOR', 'note' => 'Public verified mora biti 0.'];
$checks[] = ['key' => 'recipe_id_present', 'status' => $clone_meta['_dry_recipe_id'] === 'HR-BR-2697-BARANJSKA-LJUTA-KOBASICA' ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Recipe ID mora biti upisan.'];
$checks[] = ['key' => 'sections_present', 'status' => $clone_meta['_dry_recipe_sections_length'] > 1000 ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Sections meta mora biti prisutan.'];
$checks[] = ['key' => 'verified_process_present', 'status' => $clone_meta['_dry_verified_process_length'] > 1000 ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Verified process meta mora biti prisutan.'];
$checks[] = ['key' => 'full_markdown_present', 'status' => $clone_meta['_dry_recipe_full_markdown_length'] > 5000 ? 'PASS' : 'FAIL', 'severity' => 'MAJOR', 'note' => 'Full markdown meta mora biti prisutan.'];
$checks[] = ['key' => 'not_publicly_exposed', 'status' => $public_fetch['publicly_exposed'] === false ? 'PASS' : 'FAIL', 'severity' => 'BLOCKER', 'note' => 'Privatni clone ne smije biti javno izložen neprijavljenom korisniku.'];

$major_failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && in_array($c['severity'], ['BLOCKER', 'MAJOR'], true)));
$blocker_failures = array_values(array_filter($major_failures, fn($c) => $c['severity'] === 'BLOCKER'));

$status = count($major_failures) === 0 ? 'PRIVATE_CLONE_CREATED_QA_PASS' : 'PRIVATE_CLONE_CREATED_QA_FAIL';

update_post_meta($clone_id, '_dry_recipe_private_clone_status', $status);

$result = [
    'generated_at' => gmdate('c'),
    'status' => $status,
    'source_post_id' => $source_post_id,
    'clone_id' => $clone_id,
    'clone_status' => $clone ? $clone->post_status : '',
    'clone_type' => $clone ? $clone->post_type : '',
    'admin_preview_url' => $admin_preview_url,
    'admin_edit_url' => $admin_edit_url,
    'source_unchanged' => $source_unchanged,
    'public_update_allowed' => false,
    'public_publish_allowed' => false,
    'source_post_write_allowed' => false,
    'public_fetch' => $public_fetch,
    'clone_meta' => $clone_meta,
    'checks' => $checks,
    'major_fail_total' => count($major_failures),
    'blocker_fail_total' => count($blocker_failures),
    'manual_preview_required' => true,
    'manual_preview_instruction' => 'Otvoriti admin_preview_url dok je korisnik prijavljen kao administrator.',
];

$result_path = rtrim($out_dir, '/') . '/2697_private_clone_result.json';
file_put_contents($result_path, wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$checks_path = rtrim($out_dir, '/') . '/2697_private_clone_checks.csv';
$fp = fopen($checks_path, 'w');
fputcsv($fp, ['key', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($fp, [$c['key'], $c['status'], $c['severity'], $c['note']]);
}
fclose($fp);

$report_path = rtrim($out_dir, '/') . '/2697_PRIVATE_CLONE_REPORT.md';
$md = [];
$md[] = '# 2697 Baranjska kobasica – ljuta varijanta private clone v1';
$md[] = '';
$md[] = 'Status: **' . $status . '**';
$md[] = '';
$md[] = 'Ovaj korak stvara samo privatni preview clone. Javni recept `2697` nije mijenjan.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Source post ID: `2697`';
$md[] = '- Clone ID: `' . $clone_id . '`';
$md[] = '- Clone status: `' . ($clone ? $clone->post_status : '') . '`';
$md[] = '- Clone type: `' . ($clone ? $clone->post_type : '') . '`';
$md[] = '- Admin preview URL: `' . $admin_preview_url . '`';
$md[] = '- Admin edit URL: `' . $admin_edit_url . '`';
$md[] = '- Source unchanged: `' . ($source_unchanged ? 'true' : 'false') . '`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Public publish allowed: `false`';
$md[] = '- Source post write allowed: `false`';
$md[] = '- Publicly exposed: `' . ($public_fetch['publicly_exposed'] ? 'true' : 'false') . '`';
$md[] = '- Blocker fail total: `' . count($blocker_failures) . '`';
$md[] = '';
$md[] = '## QA provjere';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . $c['key'] . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Ručni pregled';
$md[] = '';
$md[] = 'Korisnik treba otvoriti admin preview URL dok je prijavljen kao administrator:';
$md[] = '';
$md[] = '`' . $admin_preview_url . '`';
$md[] = '';
$md[] = 'Očekuje se strukturirani Drycured kartični prikaz. Ako se vidi samo sirovi Markdown ili interni blokovi na javno neprikladan način, treba napraviti preview repair.';
$md[] = '';
file_put_contents($report_path, implode("\n", $md));

$qa_block = "
<!-- DC_2697_PRIVATE_CLONE_V1 -->

## 2697 private clone v1

Status: **{$status}**

- Source post ID: `2697`
- Clone ID: `{$clone_id}`
- Admin preview URL: `{$admin_preview_url}`
- Admin edit URL: `{$admin_edit_url}`
- Source unchanged: `" . ($source_unchanged ? "true" : "false") . "`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Publicly exposed: `" . ($public_fetch['publicly_exposed'] ? "true" : "false") . "`
- Blocker fail total: `" . count($blocker_failures) . "`
- Report: `review/" . basename($out_dir) . "/2697_PRIVATE_CLONE_REPORT.md`
- JSON: `review/" . basename($out_dir) . "/2697_private_clone_result.json`
";
dc2697_append_once($qa_path, '<!-- DC_2697_PRIVATE_CLONE_V1 -->', $qa_block);

$readme_block = "
<!-- DC_2697_PRIVATE_CLONE_V1 -->

## 2697 private clone v1

Status: **{$status}**

Privatni clone za pregled kartičnog prikaza je izrađen.

Admin preview URL: `{$admin_preview_url}`

Javni post `2697` nije mijenjan.
";
dc2697_append_once($readme_path, '<!-- DC_2697_PRIVATE_CLONE_V1 -->', $readme_block);

$wplog_block = "
<!-- DC_2697_PRIVATE_CLONE_V1 -->

## 2697 private clone v1

- Source post ID: `2697`
- Clone ID: `{$clone_id}`
- Clone status: `private`
- Source unchanged: `" . ($source_unchanged ? "true" : "false") . "`
- Public update allowed: `false`
- Admin preview URL: `{$admin_preview_url}`
";
dc2697_append_once($wplog_path, '<!-- DC_2697_PRIVATE_CLONE_V1 -->', $wplog_block);

echo "=== 2697 PRIVATE CLONE COMPLETE ===\n";
echo "CLONE_STATUS={$status}\n";
echo "SOURCE_POST_ID=2697\n";
echo "CLONE_ID={$clone_id}\n";
echo "SOURCE_UNCHANGED=" . ($source_unchanged ? "true" : "false") . "\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "PUBLIC_PUBLISH_ALLOWED=false\n";
echo "SOURCE_POST_WRITE_ALLOWED=false\n";
echo "PUBLICLY_EXPOSED=" . ($public_fetch['publicly_exposed'] ? "true" : "false") . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "ADMIN_PREVIEW_URL={$admin_preview_url}\n";
echo "ADMIN_EDIT_URL={$admin_edit_url}\n";
echo "REPORT={$report_path}\n";
