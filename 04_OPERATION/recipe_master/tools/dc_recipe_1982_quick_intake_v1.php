<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_1982_INTAKE_OUT');
$qa_path = getenv('DC_1982_INTAKE_QA');

if (!$out_dir || !$qa_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc1982_meta($post_id, $key) {
    $v = get_post_meta($post_id, $key, true);
    return is_string($v) ? $v : '';
}

function dc1982_public_fetch($url) {
    $res = wp_remote_get($url, [
        'timeout' => 12,
        'redirection' => 5,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Drycured1982QuickIntake/1.0'
        ],
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'http_code' => '',
            'error' => $res->get_error_message(),
            'body_length' => 0,
            'plain_length' => 0,
            'markers' => [],
            'plain_excerpt' => '',
        ];
    }

    $code = (string)wp_remote_retrieve_response_code($res);
    $body = (string)wp_remote_retrieve_body($res);
    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($body)));

    return [
        'ok' => true,
        'http_code' => $code,
        'error' => '',
        'body_length' => strlen($body),
        'plain_length' => strlen($plain),
        'markers' => [
            'has_title_finocchiona' => stripos($plain, 'Finocchiona') !== false,
            'has_toscana' => stripos($plain, 'Toscana') !== false || stripos($plain, 'Toskana') !== false,
            'has_raw_markdown' => strpos($body, '# ') !== false,
            'has_drycured_recipe_trace' => stripos($body, 'drycured') !== false && stripos($body, 'recipe') !== false,
            'has_internal_preview_trace' => stripos($plain, 'PRIVATE_PREVIEW') !== false || stripos($plain, 'source-lock') !== false || stripos($plain, 'QA') !== false,
            'has_ingredients' => stripos($plain, 'sastoj') !== false || stripos($plain, 'sirovin') !== false,
            'has_grinding' => stripos($plain, 'mljeven') !== false || stripos($plain, 'rešet') !== false || stripos($plain, 'granul') !== false,
            'has_casing' => stripos($plain, 'crijev') !== false || stripos($plain, 'ovit') !== false,
            'has_process' => stripos($plain, 'proces') !== false || stripos($plain, 'sušen') !== false || stripos($plain, 'zren') !== false,
        ],
        'plain_excerpt' => mb_substr($plain, 0, 2200),
        'html_snapshot' => $body,
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
        'html_length' => strlen($html),
        'plain_length' => strlen($plain),
        'markers' => [
            'has_title_finocchiona' => stripos($plain, 'Finocchiona') !== false,
            'has_raw_markdown' => strpos($html, '# ') !== false,
            'has_drycured_recipe_trace' => stripos($html, 'drycured') !== false && stripos($html, 'recipe') !== false,
            'has_ingredients' => stripos($plain, 'sastoj') !== false || stripos($plain, 'sirovin') !== false,
            'has_grinding' => stripos($plain, 'mljeven') !== false || stripos($plain, 'rešet') !== false || stripos($plain, 'granul') !== false,
            'has_casing' => stripos($plain, 'crijev') !== false || stripos($plain, 'ovit') !== false,
            'has_process' => stripos($plain, 'proces') !== false || stripos($plain, 'sušen') !== false || stripos($plain, 'zren') !== false,
        ],
        'plain_excerpt' => mb_substr($plain, 0, 2200),
        'html' => $html,
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

$post_id = 1982;
$p = get_post($post_id);

if (!$p) {
    fwrite(STDERR, "FAIL: post 1982 ne postoji.\n");
    exit(1);
}

$permalink = get_permalink($post_id);

$all_meta_raw = get_post_meta($post_id);
$meta = [];
foreach ($all_meta_raw as $k => $vals) {
    if (
        strpos($k, '_dry_') === 0 ||
        strpos($k, 'dry_') === 0 ||
        strpos($k, '_wprm') === 0 ||
        strpos($k, 'wprm') === 0 ||
        strpos($k, '_recipe') === 0
    ) {
        $v = is_array($vals) && isset($vals[0]) ? $vals[0] : '';
        $meta[$k] = [
            'length' => is_string($v) ? strlen($v) : 0,
            'json_valid' => is_string($v) && $v !== '' && json_decode($v, true) !== null,
            'preview' => is_string($v) ? mb_substr($v, 0, 420) : '',
        ];
    }
}
ksort($meta);

$render = dc1982_render_snapshot($post_id);
$http = dc1982_public_fetch($permalink);

file_put_contents(rtrim($out_dir, '/') . '/1982_public_html_snapshot.html', $http['html_snapshot'] ?? '');
file_put_contents(rtrim($out_dir, '/') . '/1982_render_snapshot.html', $render['html'] ?? '');

$post_snapshot = [
    'ID' => (int)$p->ID,
    'post_title' => $p->post_title,
    'post_name' => $p->post_name,
    'post_status' => $p->post_status,
    'post_type' => $p->post_type,
    'post_modified_gmt' => $p->post_modified_gmt,
    'permalink' => $permalink,
    'content_length' => strlen($p->post_content),
    'content_excerpt' => mb_substr(trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($p->post_content))), 0, 2200),
];

$required_meta_keys = [
    '_dry_recipe_id',
    '_dry_recipe_full_markdown',
    '_dry_recipe_image_url',
    '_dry_recipe_sections',
    '_dry_verified_process',
];

$checks = [];

dc1982_check($checks, 'post_exists', 'Post 1982 postoji', true, 'BLOCKER', 'WP zapis mora postojati.');
dc1982_check($checks, 'post_type', 'Post je dry_recipe', $p->post_type === 'dry_recipe', 'BLOCKER', 'Očekuje se dry_recipe.');
dc1982_check($checks, 'post_publish_or_private', 'Post je publish ili private', in_array($p->post_status, ['publish', 'private', 'draft'], true), 'MAJOR', 'Status mora biti poznat za daljnji workflow.');
dc1982_check($checks, 'title_finocchiona', 'Naziv sadrži Finocchiona', stripos($p->post_title, 'Finocchiona') !== false, 'MAJOR', 'Provjera identiteta recepta.');
dc1982_check($checks, 'public_http_200_if_publish', 'Javni HTTP je 200 ako je publish', $p->post_status !== 'publish' || ($http['http_code'] === '200'), 'MAJOR', 'Ako je publish, stranica mora biti dohvatljiva.');
dc1982_check($checks, 'render_has_title', 'Render sadrži naziv', $render['markers']['has_title_finocchiona'] ?? false, 'MAJOR', 'Interni render mora imati naziv.');
dc1982_check($checks, 'http_has_title', 'HTTP sadrži naziv', $p->post_status !== 'publish' || ($http['markers']['has_title_finocchiona'] ?? false), 'MAJOR', 'Javni prikaz mora imati naziv ako je publish.');
dc1982_check($checks, 'likely_ground_or_casing', 'Vjerojatno mljeveni/omotač tip', (($render['markers']['has_grinding'] ?? false) || ($http['markers']['has_grinding'] ?? false) || ($render['markers']['has_casing'] ?? false) || ($http['markers']['has_casing'] ?? false)), 'MAJOR', 'Za 01B očekuje se GROUND_MEAT_OR_CASING, ali treba potvrditi.');
dc1982_check($checks, 'no_private_preview_trace_public', 'Javni prikaz nema privatne/QA tragove', !($http['markers']['has_internal_preview_trace'] ?? false), 'MAJOR', 'Javni recept ne smije imati interne oznake.');

foreach ($required_meta_keys as $key) {
    dc1982_check(
        $checks,
        'meta_' . $key,
        'Meta postoji: ' . $key,
        array_key_exists($key, $meta) && ($meta[$key]['length'] > 0),
        $key === '_dry_recipe_id' ? 'MAJOR' : 'INFO',
        'Pregled postojećeg meta stanja; ne znači automatski da je javno spremno.'
    );
}

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && $c['severity'] !== 'INFO'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

if (count($blocker_failures) > 0) {
    $intake_status = 'INTAKE_BLOCKED';
} elseif (count($failures) > 0) {
    $intake_status = 'INTAKE_COMPLETE_WITH_GAPS';
} else {
    $intake_status = 'INTAKE_COMPLETE';
}

$result = [
    'generated_at' => gmdate('c'),
    'post_id' => $post_id,
    'intake_status' => $intake_status,
    'mode' => 'READ_ONLY_QUICK_INTAKE',
    'wordpress_write_allowed' => false,
    'public_update_allowed' => false,
    'post' => $post_snapshot,
    'meta' => $meta,
    'render' => [
        'html_length' => $render['html_length'],
        'plain_length' => $render['plain_length'],
        'markers' => $render['markers'],
        'plain_excerpt' => $render['plain_excerpt'],
    ],
    'http' => [
        'http_code' => $http['http_code'],
        'ok' => $http['ok'],
        'error' => $http['error'],
        'body_length' => $http['body_length'],
        'plain_length' => $http['plain_length'],
        'markers' => $http['markers'],
        'plain_excerpt' => $http['plain_excerpt'],
    ],
    'checks' => $checks,
    'fail_total_major_or_blocker' => count($failures),
    'blocker_fail_total' => count($blocker_failures),
    'recommended_next' => [
        'source_validation',
        'recipe_yml_draft_or_recovery',
        'internal_qa',
        'private_clone_if_not_public_ready',
    ],
];

file_put_contents(
    rtrim($out_dir, '/') . '/1982_quick_intake_v1.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/1982_quick_intake_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$meta_csv = fopen(rtrim($out_dir, '/') . '/1982_meta_inventory.csv', 'w');
fputcsv($meta_csv, ['meta_key', 'length', 'json_valid', 'preview']);
foreach ($meta as $k => $m) {
    fputcsv($meta_csv, [$k, $m['length'], $m['json_valid'] ? 'YES' : 'NO', $m['preview']]);
}
fclose($meta_csv);

$md = [];
$md[] = '# 1982 Finocchiona Toscana quick intake v1';
$md[] = '';
$md[] = 'Status: **' . $intake_status . '**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Radi početni read-only intake za sljedeći recept iz 01B skupine.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Post ID: `1982`';
$md[] = '- Title: `' . str_replace('`', "'", $p->post_title) . '`';
$md[] = '- Status: `' . $p->post_status . '`';
$md[] = '- Type: `' . $p->post_type . '`';
$md[] = '- URL: `' . $permalink . '`';
$md[] = '- HTTP code: `' . $http['http_code'] . '`';
$md[] = '- WordPress write allowed: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Major/blocker fail total: `' . count($failures) . '`';
$md[] = '- Blocker fail total: `' . count($blocker_failures) . '`';
$md[] = '';
$md[] = '## Meta stanje';
$md[] = '';
foreach ($required_meta_keys as $key) {
    $exists = array_key_exists($key, $meta) && $meta[$key]['length'] > 0;
    $md[] = '- `' . $key . '`: `' . ($exists ? 'present' : 'missing') . '`';
}
$md[] = '';
$md[] = '## QA provjere';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Zaključak';
$md[] = '';
$md[] = 'Sljedeći korak je source validation za `1982 — Finocchiona Toscana`. Javni update nije dopušten u ovom intake koraku.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/1982_QUICK_INTAKE_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_1982_QUICK_INTAKE_V1 -->';
$append = $marker . "\n\n" .
"## 1982 Finocchiona Toscana quick intake v1\n\n" .
"Status: **" . $intake_status . "**\n\n" .
"- Post ID: `1982`\n" .
"- WordPress write allowed: `false`\n" .
"- Public update allowed: `false`\n" .
"- HTTP code: `" . $http['http_code'] . "`\n" .
"- Report: `review/" . basename($out_dir) . "/1982_QUICK_INTAKE_REPORT.md`\n" .
"- JSON: `review/" . basename($out_dir) . "/1982_quick_intake_v1.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 1982 QUICK INTAKE COMPLETE ===\n";
echo "INTAKE_STATUS=" . $intake_status . "\n";
echo "POST_ID=1982\n";
echo "TITLE=" . $p->post_title . "\n";
echo "POST_STATUS=" . $p->post_status . "\n";
echo "POST_TYPE=" . $p->post_type . "\n";
echo "HTTP_CODE=" . $http['http_code'] . "\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/1982_QUICK_INTAKE_REPORT.md\n";
