<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_2697_INTAKE_OUT');
$qa_path = getenv('DC_2697_INTAKE_QA');
$readme_path = getenv('DC_2697_INTAKE_README');
$wplog_path = getenv('DC_2697_INTAKE_WPLOG');

if (!$out_dir || !$qa_path || !$readme_path || !$wplog_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc2697_post_basic($post_id) {
    $p = get_post($post_id);
    if (!$p) return null;

    return [
        'ID' => (int)$p->ID,
        'post_title' => $p->post_title,
        'post_name' => $p->post_name,
        'post_status' => $p->post_status,
        'post_type' => $p->post_type,
        'post_modified_gmt' => $p->post_modified_gmt,
        'permalink' => get_permalink($post_id),
        'content_length' => strlen($p->post_content),
        'content_excerpt' => mb_substr(trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($p->post_content))), 0, 2400),
    ];
}

function dc2697_meta_inventory($post_id) {
    $raw = get_post_meta($post_id);
    $meta = [];

    foreach ($raw as $k => $vals) {
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
                'preview' => is_string($v) ? mb_substr($v, 0, 500) : '',
            ];
        }
    }

    ksort($meta);
    return $meta;
}

function dc2697_render_snapshot($post_id) {
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
        'html_length' => strlen($html),
        'plain_length' => strlen($plain),
        'plain_excerpt' => mb_substr($plain, 0, 2600),
        'markers' => [
            'has_title' => stripos($plain, 'Baranjska') !== false || stripos($plain, 'kobasica') !== false,
            'has_kobasica' => stripos($plain, 'kobasica') !== false,
            'has_slavonska_baranja' => stripos($plain, 'Baranj') !== false || stripos($plain, 'Slavon') !== false,
            'has_ingredients' => stripos($plain, 'sastoj') !== false || stripos($plain, 'sirovin') !== false,
            'has_grinding' => stripos($plain, 'mljeven') !== false || stripos($plain, 'rešet') !== false || stripos($plain, 'granul') !== false,
            'has_casing' => stripos($plain, 'crijev') !== false || stripos($plain, 'ovit') !== false,
            'has_process' => stripos($plain, 'proces') !== false || stripos($plain, 'sušen') !== false || stripos($plain, 'zren') !== false || stripos($plain, 'dim') !== false,
            'has_internal_terms' => stripos($plain, 'PRIVATE_PREVIEW') !== false || stripos($plain, 'source-lock') !== false || stripos($plain, 'QA') !== false,
            'has_drycured_trace' => stripos($html, 'drycured') !== false || stripos($html, 'dc-recipe') !== false || stripos($html, 'dcv') !== false,
        ],
    ];
}

function dc2697_http_fetch($url) {
    $res = wp_remote_get($url, [
        'timeout' => 12,
        'redirection' => 5,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Drycured2697QuickIntake/1.0'
        ],
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'http_code' => '',
            'error' => $res->get_error_message(),
            'body_length' => 0,
            'plain_length' => 0,
            'plain_excerpt' => '',
            'markers' => [],
            'html' => '',
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
        'plain_excerpt' => mb_substr($plain, 0, 2600),
        'markers' => [
            'has_title' => stripos($plain, 'Baranjska') !== false || stripos($plain, 'kobasica') !== false,
            'has_kobasica' => stripos($plain, 'kobasica') !== false,
            'has_slavonska_baranja' => stripos($plain, 'Baranj') !== false || stripos($plain, 'Slavon') !== false,
            'has_ingredients' => stripos($plain, 'sastoj') !== false || stripos($plain, 'sirovin') !== false,
            'has_grinding' => stripos($plain, 'mljeven') !== false || stripos($plain, 'rešet') !== false || stripos($plain, 'granul') !== false,
            'has_casing' => stripos($plain, 'crijev') !== false || stripos($plain, 'ovit') !== false,
            'has_process' => stripos($plain, 'proces') !== false || stripos($plain, 'sušen') !== false || stripos($plain, 'zren') !== false || stripos($plain, 'dim') !== false,
            'has_internal_terms' => stripos($plain, 'PRIVATE_PREVIEW') !== false || stripos($plain, 'source-lock') !== false || stripos($plain, 'QA') !== false,
        ],
        'html' => $body,
    ];
}

function dc2697_check(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

$post_id = 2697;
$post = get_post($post_id);
if (!$post) {
    fwrite(STDERR, "FAIL: post 2697 ne postoji.\n");
    exit(1);
}

$basic = dc2697_post_basic($post_id);
$meta = dc2697_meta_inventory($post_id);
$render = dc2697_render_snapshot($post_id);
$http = dc2697_http_fetch($basic['permalink']);

file_put_contents(rtrim($out_dir, '/') . '/2697_public_html_snapshot.html', $http['html']);
file_put_contents(rtrim($out_dir, '/') . '/2697_render_snapshot.html', $render['html']);

$required_meta_keys = [
    '_dry_recipe_id',
    '_dry_recipe_full_markdown',
    '_dry_recipe_image_url',
    '_dry_recipe_sections',
    '_dry_verified_process',
];

$checks = [];

dc2697_check($checks, 'post_exists', 'Post 2697 postoji', true, 'BLOCKER', 'WP zapis mora postojati.');
dc2697_check($checks, 'post_type', 'Post je dry_recipe', $post->post_type === 'dry_recipe', 'BLOCKER', 'Očekuje se dry_recipe.');
dc2697_check($checks, 'post_publish', 'Post je publish', $post->post_status === 'publish', 'MAJOR', 'Strict queue ga je odabrao kao javni zapis.');
dc2697_check($checks, 'title_identity', 'Naziv odgovara Baranjskoj kobasici', stripos($post->post_title, 'Baranj') !== false && stripos($post->post_title, 'Kobas') !== false, 'BLOCKER', 'Provjera identiteta recepta.');
dc2697_check($checks, 'http_200', 'Javni HTTP je 200', $http['http_code'] === '200', 'MAJOR', 'Javni zapis mora biti dohvatljiv.');
dc2697_check($checks, 'type_ground_signal', 'Signal mljevenog proizvoda u ovitku', ($http['markers']['has_kobasica'] ?? false) || ($render['markers']['has_kobasica'] ?? false), 'BLOCKER', 'Kobasica ide u GROUND_MEAT_OR_CASING.');
dc2697_check($checks, 'hr_region_signal', 'Signal Baranja/Slavonija', ($http['markers']['has_slavonska_baranja'] ?? false) || ($render['markers']['has_slavonska_baranja'] ?? false), 'MAJOR', 'Mora imati hrvatski regionalni signal.');
dc2697_check($checks, 'no_public_internal_terms', 'Javni prikaz nema interne oznake', !($http['markers']['has_internal_terms'] ?? false), 'MAJOR', 'Javni recept ne smije imati interne QA/preview oznake.');

foreach ($required_meta_keys as $key) {
    $exists = array_key_exists($key, $meta) && ($meta[$key]['length'] > 0);
    dc2697_check(
        $checks,
        'meta_' . $key,
        'Meta postoji: ' . $key,
        $exists,
        $key === '_dry_recipe_id' ? 'MAJOR' : 'INFO',
        'Pregled postojećeg meta stanja; ne znači da je javno spremno.'
    );
}

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && $c['severity'] !== 'INFO'));
$blockers = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

if (count($blockers) > 0) {
    $status = 'INTAKE_BLOCKED';
} elseif (count($failures) > 0) {
    $status = 'INTAKE_COMPLETE_WITH_GAPS';
} else {
    $status = 'INTAKE_COMPLETE';
}

$result = [
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_QUICK_INTAKE',
    'post_id' => $post_id,
    'intake_status' => $status,
    'wordpress_write_allowed' => false,
    'public_update_allowed' => false,
    'expected_type' => 'GROUND_MEAT_OR_CASING',
    'post' => $basic,
    'meta' => $meta,
    'http' => [
        'http_code' => $http['http_code'],
        'ok' => $http['ok'],
        'error' => $http['error'],
        'body_length' => $http['body_length'],
        'plain_length' => $http['plain_length'],
        'markers' => $http['markers'],
        'plain_excerpt' => $http['plain_excerpt'],
    ],
    'render' => [
        'html_length' => $render['html_length'],
        'plain_length' => $render['plain_length'],
        'markers' => $render['markers'],
        'plain_excerpt' => $render['plain_excerpt'],
    ],
    'checks' => $checks,
    'fail_total_major_or_blocker' => count($failures),
    'blocker_fail_total' => count($blockers),
    'recommended_next' => [
        'source_validation',
        'recipe_yml_draft_or_reconstruction',
        'internal_qa',
        'preview_payload',
        'private_clone'
    ],
];

file_put_contents(
    rtrim($out_dir, '/') . '/2697_quick_intake_v1.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/2697_quick_intake_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$meta_csv = fopen(rtrim($out_dir, '/') . '/2697_meta_inventory.csv', 'w');
fputcsv($meta_csv, ['meta_key', 'length', 'json_valid', 'preview']);
foreach ($meta as $k => $m) {
    fputcsv($meta_csv, [$k, $m['length'], $m['json_valid'] ? 'YES' : 'NO', $m['preview']]);
}
fclose($meta_csv);

$md = [];
$md[] = '# 2697 Baranjska Ljuta Slavonska Kobasica quick intake v1';
$md[] = '';
$md[] = 'Status: **' . $status . '**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Radi početni read-only intake prvog kandidata iz strict hrvatskog reda čekanja.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Post ID: `2697`';
$md[] = '- Title: `' . str_replace('`', "'", $post->post_title) . '`';
$md[] = '- Status: `' . $post->post_status . '`';
$md[] = '- Type: `' . $post->post_type . '`';
$md[] = '- Expected recipe type: `GROUND_MEAT_OR_CASING`';
$md[] = '- URL: `' . $basic['permalink'] . '`';
$md[] = '- HTTP code: `' . $http['http_code'] . '`';
$md[] = '- WordPress write allowed: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Major/blocker fail total: `' . count($failures) . '`';
$md[] = '- Blocker fail total: `' . count($blockers) . '`';
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
$md[] = 'Sljedeći korak je source validation za `2697 — Baranjska Ljuta Slavonska Kobasica`. Javni update nije dopušten u intake koraku.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/2697_QUICK_INTAKE_REPORT.md', implode("\n", $md));

function dc2697_append_once($path, $marker, $block) {
    $old = file_get_contents($path);
    if (strpos($old, $marker) === false) {
        file_put_contents($path, rtrim($old) . "\n\n" . trim($block) . "\n");
    }
}

$qa_block = "
<!-- DC_2697_QUICK_INTAKE_V1 -->

## 2697 Baranjska Ljuta Slavonska Kobasica quick intake v1

Status: **{$status}**

- Post ID: `2697`
- Expected type: `GROUND_MEAT_OR_CASING`
- WordPress write allowed: `false`
- Public update allowed: `false`
- HTTP code: `{$http['http_code']}`
- Major/blocker fail total: `" . count($failures) . "`
- Blocker fail total: `" . count($blockers) . "`
- Report: `review/" . basename($out_dir) . "/2697_QUICK_INTAKE_REPORT.md`
- JSON: `review/" . basename($out_dir) . "/2697_quick_intake_v1.json`
";

dc2697_append_once($qa_path, '<!-- DC_2697_QUICK_INTAKE_V1 -->', $qa_block);

$readme_block = "
<!-- DC_2697_QUICK_INTAKE_V1 -->

## 2697 quick intake v1

Status: **{$status}**

Početni read-only intake za `2697 — Baranjska Ljuta Slavonska Kobasica` je izrađen. Javni update nije dopušten.
";
dc2697_append_once($readme_path, '<!-- DC_2697_QUICK_INTAKE_V1 -->', $readme_block);

$wplog_block = "
<!-- DC_2697_QUICK_INTAKE_V1 -->

## 2697 quick intake v1

- WordPress write allowed: `false`
- Public update allowed: `false`
- HTTP code: `{$http['http_code']}`
- Status: `{$status}`
";
dc2697_append_once($wplog_path, '<!-- DC_2697_QUICK_INTAKE_V1 -->', $wplog_block);

echo "=== 2697 QUICK INTAKE COMPLETE ===\n";
echo "INTAKE_STATUS=" . $status . "\n";
echo "POST_ID=2697\n";
echo "TITLE=" . $post->post_title . "\n";
echo "POST_STATUS=" . $post->post_status . "\n";
echo "POST_TYPE=" . $post->post_type . "\n";
echo "EXPECTED_TYPE=GROUND_MEAT_OR_CASING\n";
echo "HTTP_CODE=" . $http['http_code'] . "\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "MAJOR_FAIL_TOTAL=" . count($failures) . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blockers) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/2697_QUICK_INTAKE_REPORT.md\n";
