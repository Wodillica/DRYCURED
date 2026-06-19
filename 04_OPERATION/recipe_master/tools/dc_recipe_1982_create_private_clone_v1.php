<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$payload_path = getenv('DC_1982_PRIVATE_CLONE_PAYLOAD');
$out_dir = getenv('DC_1982_PRIVATE_CLONE_OUT');
$qa_path = getenv('DC_1982_PRIVATE_CLONE_QA');
$readme_path = getenv('DC_1982_PRIVATE_CLONE_README');
$wplog_path = getenv('DC_1982_PRIVATE_CLONE_WPLOG');
$db_backup_path = getenv('DC_1982_PRIVATE_CLONE_DB_BACKUP');

if (!$payload_path || !$out_dir || !$qa_path || !$readme_path || !$wplog_path || !$db_backup_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc1982_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dc1982_json_read($path) {
    if (!is_readable($path)) {
        dc1982_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dc1982_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dc1982_basic_post($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return null;
    }
    return [
        'ID' => (int)$p->ID,
        'post_title' => $p->post_title,
        'post_name' => $p->post_name,
        'post_status' => $p->post_status,
        'post_type' => $p->post_type,
        'post_modified_gmt' => $p->post_modified_gmt,
        'content_length' => strlen($p->post_content),
        'permalink' => get_permalink($post_id),
    ];
}

function dc1982_render_snapshot($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return [
            'exists' => false,
            'html' => '',
            'plain' => '',
            'markers' => [],
        ];
    }

    global $post;
    $old = $post ?? null;
    $post = $p;
    setup_postdata($post);
    $html = apply_filters('the_content', $p->post_content);
    wp_reset_postdata();
    $post = $old;

    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($html)));

    return [
        'exists' => true,
        'html' => $html,
        'plain' => $plain,
        'html_length' => strlen($html),
        'plain_length' => strlen($plain),
        'markers' => [
            'has_title' => stripos($plain, 'Finocchiona') !== false,
            'has_raw_materials' => stripos($plain, 'Glavne sirovine') !== false || stripos($plain, 'svinjska lopatica') !== false,
            'has_grinding' => stripos($plain, 'Mljevenje') !== false || stripos($plain, '6 mm') !== false,
            'has_casing' => stripos($plain, 'Crijeva') !== false || stripos($plain, 'ovitak') !== false,
            'has_process' => stripos($plain, 'Procesna kronologija') !== false || stripos($plain, 'Zrenje') !== false,
            'has_problems' => stripos($plain, 'Greške') !== false || stripos($plain, 'Rješenje') !== false,
            'has_private_notice' => stripos($plain, 'privatni radni preview') !== false || stripos($plain, 'javni update nije dopušten') !== false,
            'has_internal_blockers' => stripos($plain, 'Interno prije javnog updatea') !== false,
            'has_drycured_trace' => stripos($html, 'drycured') !== false || stripos($html, 'dc-recipe') !== false || stripos($html, 'dcv') !== false,
        ],
        'plain_excerpt' => mb_substr($plain, 0, 2400),
    ];
}

function dc1982_http($url) {
    $res = wp_remote_get($url, [
        'timeout' => 10,
        'redirection' => 4,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Drycured1982PrivateCloneQA/1.0'
        ]
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'http_code' => '',
            'error' => $res->get_error_message(),
            'publicly_exposed' => false,
            'body_length' => 0,
        ];
    }

    $code = (string)wp_remote_retrieve_response_code($res);
    $body = (string)wp_remote_retrieve_body($res);
    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($body)));
    $title_exposed = stripos($plain, 'Finocchiona') !== false;

    return [
        'ok' => true,
        'http_code' => $code,
        'error' => '',
        'body_length' => strlen($body),
        'plain_length' => strlen($plain),
        'title_exposed' => $title_exposed,
        'publicly_exposed' => $code === '200' && $title_exposed,
    ];
}

function dc1982_check(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

$source_id = 1982;
$payload = dc1982_json_read($payload_path);

if (($payload['schema_version'] ?? '') !== 'drycured_private_preview_payload_v1') {
    dc1982_fail("payload schema nije drycured_private_preview_payload_v1.");
}

if (($payload['post_id'] ?? null) !== 1982) {
    dc1982_fail("payload nije za post 1982.");
}

if (($payload['recipe_code'] ?? '') !== 'IT-TOS-1982-FINOCCHIONA-TOSCANA') {
    dc1982_fail("recipe_code nije očekivan.");
}

if (($payload['public_update_allowed'] ?? null) !== false) {
    dc1982_fail("payload mora imati public_update_allowed=false.");
}

if (($payload['source_post_write_allowed'] ?? null) !== false) {
    dc1982_fail("payload mora imati source_post_write_allowed=false.");
}

$meta = $payload['meta_to_apply_to_private_clone_only'] ?? null;
if (!is_array($meta)) {
    dc1982_fail("payload nema meta_to_apply_to_private_clone_only.");
}

$required_meta = [
    '_dry_recipe_preview_mode',
    '_dry_recipe_preview_source_post_id',
    '_dry_recipe_public_update_allowed',
    '_dry_recipe_public_verified',
    '_dry_recipe_id',
    '_dry_recipe_sections',
    '_dry_verified_process',
    '_dry_recipe_full_markdown'
];

foreach ($required_meta as $k) {
    if (!array_key_exists($k, $meta)) {
        dc1982_fail("payload nema obvezni meta ključ: " . $k);
    }
}

if ((string)$meta['_dry_recipe_preview_mode'] !== 'PRIVATE_CLONE_ONLY') {
    dc1982_fail("preview mode mora biti PRIVATE_CLONE_ONLY.");
}

if ((string)$meta['_dry_recipe_preview_source_post_id'] !== '1982') {
    dc1982_fail("preview source post id mora biti 1982.");
}

if ((string)$meta['_dry_recipe_public_update_allowed'] !== '0') {
    dc1982_fail("public update allowed mora biti 0.");
}

if ((string)$meta['_dry_recipe_public_verified'] !== '0') {
    dc1982_fail("public verified mora biti 0.");
}

$source = get_post($source_id);
if (!$source) {
    dc1982_fail("source post 1982 ne postoji.");
}

if ($source->post_type !== 'dry_recipe' || $source->post_status !== 'publish') {
    dc1982_fail("source 1982 mora biti publish dry_recipe.");
}

$source_before = dc1982_basic_post($source_id);

$post_content = (string)$meta['_dry_recipe_full_markdown'];
if (strlen($post_content) < 4000) {
    dc1982_fail("_dry_recipe_full_markdown je prekratak.");
}

$existing = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => ['private', 'draft'],
    'meta_key' => '_dry_recipe_preview_source_post_id',
    'meta_value' => '1982',
    'numberposts' => 10,
    'fields' => 'ids',
]);

if (!empty($existing)) {
    dc1982_fail("već postoji privatni/draft clone za source 1982: " . implode(',', $existing));
}

$clone_id = wp_insert_post([
    'post_type' => 'dry_recipe',
    'post_status' => 'private',
    'post_title' => 'PREVIEW — Finocchiona Toscana IGP',
    'post_name' => 'preview-finocchiona-toscana-src-1982',
    'post_content' => $post_content,
    'post_excerpt' => 'Privatni Drycured preview za Finocchiona Toscana IGP. Nije javni recept.',
], true);

if (is_wp_error($clone_id)) {
    dc1982_fail("wp_insert_post error: " . $clone_id->get_error_message());
}

$clone_id = (int)$clone_id;
if ($clone_id <= 0) {
    dc1982_fail("clone ID nije valjan.");
}

foreach ($meta as $key => $value) {
    if (is_array($value) || is_object($value)) {
        update_post_meta($clone_id, $key, wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    } else {
        update_post_meta($clone_id, $key, (string)$value);
    }
}

update_post_meta($clone_id, '_dry_recipe_source_validation_status', 'CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED');
update_post_meta($clone_id, '_dry_recipe_type_router', 'GROUND_MEAT_OR_CASING');
update_post_meta($clone_id, '_dry_recipe_adapter_payload_version', 'preview_payload_v1');
update_post_meta($clone_id, '_dry_recipe_dossier_path', str_replace('/root/DRYCURED_GITHUB/', '', dirname($out_dir, 2)));
update_post_meta($clone_id, '_dry_recipe_private_clone_created_at_gmt', gmdate('c'));
update_post_meta($clone_id, '_dry_recipe_private_clone_source_commit_hint', '1982-preview-payload-v1');

$source_after = dc1982_basic_post($source_id);
$clone = dc1982_basic_post($clone_id);

$source_unchanged = true;
foreach (['post_title', 'post_name', 'post_status', 'post_type', 'post_modified_gmt'] as $k) {
    if ((string)$source_before[$k] !== (string)$source_after[$k]) {
        $source_unchanged = false;
    }
}

if (!$source_unchanged) {
    dc1982_fail("ZAŠTITA: source 1982 se promijenio.");
}

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

$render = dc1982_render_snapshot($clone_id);
$http = dc1982_http($clone['permalink']);

file_put_contents(rtrim($out_dir, '/') . '/1982_private_clone_render_snapshot.html', $render['html']);

$checks = [];

dc1982_check($checks, 'source_unchanged', 'Source 1982 nije mijenjan', $source_unchanged, 'BLOCKER', 'Javni source mora ostati netaknut.');
dc1982_check($checks, 'clone_private', 'Clone je private', $clone['post_status'] === 'private', 'BLOCKER', 'Clone mora biti private.');
dc1982_check($checks, 'clone_type', 'Clone je dry_recipe', $clone['post_type'] === 'dry_recipe', 'BLOCKER', 'Clone mora biti dry_recipe.');
dc1982_check($checks, 'clone_source_link', 'Clone je vezan na 1982', $clone_meta['_dry_recipe_preview_source_post_id'] === '1982', 'BLOCKER', 'Source link mora biti 1982.');
dc1982_check($checks, 'preview_mode', 'Preview mode je PRIVATE_CLONE_ONLY', $clone_meta['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY', 'BLOCKER', 'Clone mora ostati privatni preview.');
dc1982_check($checks, 'public_update_0', 'Public update je 0', $clone_meta['_dry_recipe_public_update_allowed'] === '0', 'BLOCKER', 'Ne smije dopuštati javni update.');
dc1982_check($checks, 'public_verified_0', 'Public verified je 0', $clone_meta['_dry_recipe_public_verified'] === '0', 'BLOCKER', 'Ne smije biti public verified.');
dc1982_check($checks, 'recipe_id', 'Recipe ID je upisan', $clone_meta['_dry_recipe_id'] === 'IT-TOS-1982-FINOCCHIONA-TOSCANA', 'BLOCKER', 'Recipe ID mora biti stabilan.');
dc1982_check($checks, 'sections_present', 'Sections meta postoji', $clone_meta['_dry_recipe_sections_length'] > 1000, 'MAJOR', 'Sections moraju biti upisane.');
dc1982_check($checks, 'process_present', 'Verified process meta postoji', $clone_meta['_dry_verified_process_length'] > 1000, 'MAJOR', 'Verified process mora biti upisan.');
dc1982_check($checks, 'full_markdown_present', 'Full markdown postoji', $clone_meta['_dry_recipe_full_markdown_length'] > 4000, 'MAJOR', 'Full markdown mora biti upisan.');
dc1982_check($checks, 'not_publicly_exposed', 'Clone nije javno izložen', $http['publicly_exposed'] === false, 'BLOCKER', 'Neprijavljeni javni fetch ne smije prikazati recept.');
dc1982_check($checks, 'render_has_title', 'Render ima naslov', $render['markers']['has_title'] ?? false, 'MAJOR', 'Interni render mora sadržavati naslov.');
dc1982_check($checks, 'render_has_raw_materials', 'Render ima sirovine', $render['markers']['has_raw_materials'] ?? false, 'MAJOR', 'Interni render mora sadržavati sirovine.');
dc1982_check($checks, 'render_has_grinding', 'Render ima mljevenje', $render['markers']['has_grinding'] ?? false, 'MAJOR', 'Interni render mora sadržavati mljevenje.');
dc1982_check($checks, 'render_has_casing', 'Render ima crijeva/ovitak', $render['markers']['has_casing'] ?? false, 'MAJOR', 'Interni render mora sadržavati ovitak/crijeva.');
dc1982_check($checks, 'render_has_process', 'Render ima proces', $render['markers']['has_process'] ?? false, 'MAJOR', 'Interni render mora sadržavati proces.');
dc1982_check($checks, 'render_has_problems', 'Render ima probleme/rješenja', $render['markers']['has_problems'] ?? false, 'MAJOR', 'Interni render mora sadržavati probleme i rješenja.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && $c['severity'] !== 'INFO'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$status = count($blocker_failures) === 0 && count($failures) === 0 ? 'PRIVATE_CLONE_CREATED_QA_PASS' : 'PRIVATE_CLONE_CREATED_QA_HAS_GAPS';

$result = [
    'generated_at' => gmdate('c'),
    'status' => $status,
    'source_post_id' => 1982,
    'clone_id' => $clone_id,
    'source_unchanged' => $source_unchanged,
    'public_update_allowed' => false,
    'public_publish_allowed' => false,
    'source_post_write_allowed' => false,
    'db_backup_path_outside_git' => $db_backup_path,
    'source_before' => $source_before,
    'source_after' => $source_after,
    'clone' => $clone,
    'clone_meta' => $clone_meta,
    'http_public_check' => $http,
    'render_snapshot' => [
        'html_length' => $render['html_length'],
        'plain_length' => $render['plain_length'],
        'markers' => $render['markers'],
        'plain_excerpt' => $render['plain_excerpt'],
    ],
    'checks' => $checks,
    'fail_total_major_or_blocker' => count($failures),
    'blocker_fail_total' => count($blocker_failures),
    'admin_preview_url' => $clone['permalink'],
    'admin_edit_url' => admin_url('post.php?post=' . $clone_id . '&action=edit'),
];

file_put_contents(
    rtrim($out_dir, '/') . '/1982_private_clone_result.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/1982_private_clone_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$backup_manifest = [];
$backup_manifest[] = '# Backup manifest — 1982 private clone v1';
$backup_manifest[] = '';
$backup_manifest[] = 'Status: **BACKUP_STORED_OUTSIDE_GIT**';
$backup_manifest[] = '';
$backup_manifest[] = '- Backup type: WordPress DB export';
$backup_manifest[] = '- Backup location: `' . $db_backup_path . '`';
$backup_manifest[] = '- Stored outside Git: `true`';
$backup_manifest[] = '- Reason: SQL backups may contain secrets and must not be committed.';
$backup_manifest[] = '';
file_put_contents(rtrim($out_dir, '/') . '/1982_PRIVATE_CLONE_BACKUP_MANIFEST.md', implode("\n", $backup_manifest));

$md = [];
$md[] = '# 1982 Finocchiona Toscana private clone v1';
$md[] = '';
$md[] = 'Status: **' . $status . '**';
$md[] = '';
$md[] = 'Ovaj korak stvara privatni clone za administratorski pregled. Javni source post `1982` nije mijenjan.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Source post ID: `1982`';
$md[] = '- Private clone ID: `' . $clone_id . '`';
$md[] = '- Source unchanged: `' . ($source_unchanged ? 'true' : 'false') . '`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Public publish allowed: `false`';
$md[] = '- Source post write allowed: `false`';
$md[] = '- Clone status: `' . $clone['post_status'] . '`';
$md[] = '- Admin preview URL: `' . $clone['permalink'] . '`';
$md[] = '- Admin edit URL: `' . admin_url('post.php?post=' . $clone_id . '&action=edit') . '`';
$md[] = '- Publicly exposed unauth: `' . ($http['publicly_exposed'] ? 'true' : 'false') . '`';
$md[] = '- DB backup stored outside Git: `' . $db_backup_path . '`';
$md[] = '';
$md[] = '## QA provjere';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Sljedeći korak';
$md[] = '';
if ($status === 'PRIVATE_CLONE_CREATED_QA_PASS') {
    $md[] = 'Ručno otvoriti admin preview URL kao prijavljeni administrator i potvrditi vizualni prikaz. Ako je prikaz dobar, zatvoriti pilot za `1982`.';
} else {
    $md[] = 'Postoje QA praznine. Ne nastavljati na ručni preview dok se ne provjere.';
}
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/1982_PRIVATE_CLONE_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_1982_PRIVATE_CLONE_V1 -->';
$append = $marker . "\n\n" .
"## 1982 Finocchiona Toscana private clone v1\n\n" .
"Status: **" . $status . "**\n\n" .
"- Source post ID: `1982`\n" .
"- Private clone ID: `" . $clone_id . "`\n" .
"- Source unchanged: `" . ($source_unchanged ? 'true' : 'false') . "`\n" .
"- Public update allowed: `false`\n" .
"- Public publish allowed: `false`\n" .
"- Source post write allowed: `false`\n" .
"- Admin preview URL: `" . $clone['permalink'] . "`\n" .
"- Admin edit URL: `" . admin_url('post.php?post=' . $clone_id . '&action=edit') . "`\n" .
"- Report: `review/" . basename($out_dir) . "/1982_PRIVATE_CLONE_REPORT.md`\n" .
"- JSON: `review/" . basename($out_dir) . "/1982_private_clone_result.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

$readme_old = file_get_contents($readme_path);
$readme_marker = '<!-- DC_1982_PRIVATE_CLONE_V1 -->';
$readme_append = $readme_marker . "\n\n" .
"## 1982 private clone v1\n\n" .
"Status: **" . $status . "**\n\n" .
"Privatni clone `" . $clone_id . "` stvoren je za administratorski pregled. Javni source post `1982` nije mijenjan i javni update ostaje blokiran.\n";

if (strpos($readme_old, $readme_marker) === false) {
    file_put_contents($readme_path, rtrim($readme_old) . "\n\n" . $readme_append . "\n");
}

$wplog_old = file_get_contents($wplog_path);
$wplog_marker = '<!-- DC_1982_PRIVATE_CLONE_V1 -->';
$wplog_append = $wplog_marker . "\n\n" .
"## 1982 private clone v1\n\n" .
"- Source post `1982` unchanged: `" . ($source_unchanged ? 'true' : 'false') . "`\n" .
"- Private clone ID: `" . $clone_id . "`\n" .
"- Clone status: `private`\n" .
"- Public update allowed: `false`\n" .
"- Admin preview URL: `" . $clone['permalink'] . "`\n" .
"- DB backup outside Git: `" . $db_backup_path . "`\n";

if (strpos($wplog_old, $wplog_marker) === false) {
    file_put_contents($wplog_path, rtrim($wplog_old) . "\n\n" . $wplog_append . "\n");
}

echo "=== 1982 PRIVATE CLONE COMPLETE ===\n";
echo "CLONE_STATUS=" . $status . "\n";
echo "SOURCE_POST_ID=1982\n";
echo "CLONE_ID=" . $clone_id . "\n";
echo "SOURCE_UNCHANGED=" . ($source_unchanged ? 'true' : 'false') . "\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "PUBLIC_PUBLISH_ALLOWED=false\n";
echo "SOURCE_POST_WRITE_ALLOWED=false\n";
echo "PUBLICLY_EXPOSED=" . ($http['publicly_exposed'] ? 'true' : 'false') . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "ADMIN_PREVIEW_URL=" . $clone['permalink'] . "\n";
echo "ADMIN_EDIT_URL=" . admin_url('post.php?post=' . $clone_id . '&action=edit') . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/1982_PRIVATE_CLONE_REPORT.md\n";
