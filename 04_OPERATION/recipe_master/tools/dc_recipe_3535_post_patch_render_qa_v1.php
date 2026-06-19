<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_3535_POST_PATCH_QA_OUT');
$qa_path = getenv('DC_3535_POST_PATCH_QA_REPORT');
$patch_result_path = getenv('DC_3535_PATCH_RESULT_JSON');
$patch_render_path = getenv('DC_3535_PATCH_RENDER_HTML');

if (!$out_dir || !$qa_path || !$patch_result_path || !$patch_render_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dcrqa_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dcrqa_json_read($path) {
    if (!is_readable($path)) {
        dcrqa_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dcrqa_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dcrqa_basic_post($post_id) {
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

function dcrqa_render($post_id) {
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

    $markers = [
        'raw_markdown' => strpos($html, '# ') !== false,
        'drycured_recipe_trace' => stripos($html, 'drycured') !== false && stripos($html, 'recipe') !== false,
        'dc_recipe_class' => stripos($html, 'dc-recipe') !== false,
        'dcv_trace' => stripos($html, 'dcv') !== false,
        'wprm_trace' => stripos($html, 'wprm') !== false,
        'private_notice' => stripos($plain, 'privatni preview') !== false || stripos($plain, 'nije javni recept') !== false || stripos($plain, 'private preview') !== false,
        'title' => stripos($plain, 'Jésus de Lyon') !== false || stripos($plain, 'Jesus de Lyon') !== false,
        'raw_materials' => stripos($plain, 'Glavne sirovine') !== false || stripos($plain, 'svinjska lopatica') !== false,
        'spices' => stripos($plain, 'Začini') !== false || stripos($plain, 'morska sol') !== false,
        'liquids_garlic' => stripos($plain, 'Tekućine') !== false || stripos($plain, 'češnjak') !== false,
        'grinding' => stripos($plain, 'Mljevenje') !== false || stripos($plain, 'rešetka') !== false || stripos($plain, '6–8') !== false,
        'casing' => stripos($plain, 'Crijeva') !== false || stripos($plain, 'svinjska crijeva') !== false || stripos($plain, '28–32') !== false,
        'done_when' => stripos($plain, 'Gotovo je kad') !== false,
        'errors_solutions' => stripos($plain, 'Greške') !== false || stripos($plain, 'Rješenje') !== false,
        'blockers' => stripos($plain, 'Aktivne blokade') !== false || stripos($plain, 'starter kulture') !== false,
        'public_update_false_text' => stripos($plain, 'javni update') !== false || stripos($plain, 'nije javni recept') !== false,
    ];

    return [
        'exists' => true,
        'html' => $html,
        'plain' => $plain,
        'html_length' => strlen($html),
        'plain_length' => strlen($plain),
        'markers' => $markers,
        'plain_excerpt' => mb_substr($plain, 0, 2200),
    ];
}

function dcrqa_http($url) {
    $res = wp_remote_get($url, [
        'timeout' => 10,
        'redirection' => 4,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'Drycured3535PostPatchRenderQA/1.0'
        ]
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'http_code' => '',
            'error' => $res->get_error_message(),
            'body_length' => 0,
            'publicly_exposed' => false,
        ];
    }

    $code = (string)wp_remote_retrieve_response_code($res);
    $body = (string)wp_remote_retrieve_body($res);
    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($body)));
    $title_exposed = stripos($plain, 'Jésus de Lyon') !== false || stripos($plain, 'Jesus de Lyon') !== false;

    return [
        'ok' => true,
        'http_code' => $code,
        'body_length' => strlen($body),
        'plain_length' => strlen($plain),
        'title_exposed' => $title_exposed,
        'publicly_exposed' => $code === '200' && $title_exposed,
    ];
}

function dcrqa_check(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

$source_id = 3042;
$clone_id = 3535;
$reference_id = 2976;

$patch_result = dcrqa_json_read($patch_result_path);
$before_after_patch_html = is_readable($patch_render_path) ? file_get_contents($patch_render_path) : '';

$source = get_post($source_id);
$clone = get_post($clone_id);
$reference = get_post($reference_id);

if (!$source || !$clone || !$reference) {
    dcrqa_fail("nedostaje jedan od postova 2976/3042/3535.");
}

$source_before_from_patch = $patch_result['source_after'] ?? null;

$source_now = dcrqa_basic_post($source_id);
$clone_now = dcrqa_basic_post($clone_id);
$reference_now = dcrqa_basic_post($reference_id);

$source_unchanged_from_patch = true;
if (is_array($source_before_from_patch)) {
    foreach (['post_title', 'post_name', 'post_status', 'post_type', 'post_modified_gmt'] as $k) {
        if (isset($source_before_from_patch[$k]) && (string)$source_before_from_patch[$k] !== (string)$source_now[$k]) {
            $source_unchanged_from_patch = false;
        }
    }
}

$clone_meta = [
    '_dry_recipe_id' => get_post_meta($clone_id, '_dry_recipe_id', true),
    '_dry_recipe_public_update_allowed' => get_post_meta($clone_id, '_dry_recipe_public_update_allowed', true),
    '_dry_recipe_public_verified' => get_post_meta($clone_id, '_dry_recipe_public_verified', true),
    '_dry_recipe_preview_mode' => get_post_meta($clone_id, '_dry_recipe_preview_mode', true),
    '_dry_recipe_preview_source_post_id' => get_post_meta($clone_id, '_dry_recipe_preview_source_post_id', true),
    '_dry_recipe_image_url' => get_post_meta($clone_id, '_dry_recipe_image_url', true),
    '_dry_recipe_full_markdown' => get_post_meta($clone_id, '_dry_recipe_full_markdown', true),
    '_dry_recipe_sections' => get_post_meta($clone_id, '_dry_recipe_sections', true),
    '_dry_verified_process' => get_post_meta($clone_id, '_dry_verified_process', true),
];

$render_3535 = dcrqa_render($clone_id);
$render_2976 = dcrqa_render($reference_id);
$render_3042 = dcrqa_render($source_id);
$http_3535 = dcrqa_http($clone_now['permalink']);

file_put_contents(rtrim($out_dir, '/') . '/3535_post_patch_render_snapshot.html', $render_3535['html']);
file_put_contents(rtrim($out_dir, '/') . '/2976_reference_render_snapshot.html', $render_2976['html']);
file_put_contents(rtrim($out_dir, '/') . '/3042_source_render_snapshot.html', $render_3042['html']);

$checks = [];

dcrqa_check($checks, 'source_unchanged', 'Source 3042 nije mijenjan nakon patcha', $source_unchanged_from_patch, 'BLOCKER', 'Source mora ostati netaknut.');
dcrqa_check($checks, 'clone_private', 'Clone 3535 je private', $clone_now['post_status'] === 'private', 'BLOCKER', 'Clone mora ostati private.');
dcrqa_check($checks, 'clone_type', 'Clone 3535 je dry_recipe', $clone_now['post_type'] === 'dry_recipe', 'BLOCKER', 'Clone mora ostati dry_recipe.');
dcrqa_check($checks, 'clone_id_meta', 'Clone ima _dry_recipe_id', $clone_meta['_dry_recipe_id'] === 'MD-JESUS_DE_LYON_DEBELA_SUHA_KOBASICA', 'BLOCKER', 'Meta-normalizer patch mora ostati prisutan.');
dcrqa_check($checks, 'public_update_0', 'Public update ostaje 0', $clone_meta['_dry_recipe_public_update_allowed'] === '0', 'BLOCKER', 'Privatni clone ne smije signalizirati javni update.');
dcrqa_check($checks, 'public_verified_0', 'Public verified ostaje 0', $clone_meta['_dry_recipe_public_verified'] === '0', 'BLOCKER', 'Privatni clone ne smije biti public verified.');
dcrqa_check($checks, 'preview_mode', 'Preview mode ostaje PRIVATE_CLONE_ONLY', $clone_meta['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY', 'BLOCKER', 'Privatni status mora biti jasan.');
dcrqa_check($checks, 'source_link', 'Clone ostaje vezan na source 3042', $clone_meta['_dry_recipe_preview_source_post_id'] === '3042', 'BLOCKER', 'Veza na source mora ostati 3042.');
dcrqa_check($checks, 'public_404', 'Privatni clone javno nije izložen', $http_3535['publicly_exposed'] === false, 'BLOCKER', 'Javni fetch ne smije prikazati recept.');
dcrqa_check($checks, 'render_has_title', 'Render sadrži naslov', $render_3535['markers']['title'] ?? false, 'MAJOR', 'Interni render mora sadržavati naslov.');
dcrqa_check($checks, 'render_has_private_notice', 'Render ima privatnu napomenu', $render_3535['markers']['private_notice'] ?? false, 'MAJOR', 'Privatni clone mora ostati jasno označen.');
dcrqa_check($checks, 'render_has_raw_materials', 'Render ima sirovine', $render_3535['markers']['raw_materials'] ?? false, 'MAJOR', 'Sirovine moraju biti vidljive.');
dcrqa_check($checks, 'render_has_spices', 'Render ima začine', $render_3535['markers']['spices'] ?? false, 'MAJOR', 'Začini moraju biti vidljivi.');
dcrqa_check($checks, 'render_has_grinding', 'Render ima mljevenje/granulaciju', $render_3535['markers']['grinding'] ?? false, 'MAJOR', 'Granulacija mora biti vidljiva.');
dcrqa_check($checks, 'render_has_casing', 'Render ima crijeva/ovitak', $render_3535['markers']['casing'] ?? false, 'MAJOR', 'Crijeva i namakanje moraju biti vidljivi.');
dcrqa_check($checks, 'render_has_errors', 'Render ima greške/rješenja', $render_3535['markers']['errors_solutions'] ?? false, 'MAJOR', 'Problemi moraju imati rješenja.');
dcrqa_check($checks, 'render_has_blockers', 'Render ima aktivne blokade', $render_3535['markers']['blockers'] ?? false, 'MAJOR', 'Blokade moraju ostati vidljive interno.');
dcrqa_check($checks, 'image_url_skipped', 'Slika je i dalje preskočena', $clone_meta['_dry_recipe_image_url'] === '', 'INFO', 'To je očekivano jer nije bilo dostupne vrijednosti.');
dcrqa_check($checks, 'raw_markdown_state_recorded', 'Raw markdown stanje zabilježeno', true, 'INFO', 'Ovaj check ne prolazi/ne pada; bilježi stanje za odluku.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && $c['severity'] !== 'INFO'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$renderer_improved = false;
if (
    ($render_3535['markers']['drycured_recipe_trace'] ?? false) ||
    ($render_3535['markers']['dc_recipe_class'] ?? false) ||
    ($render_3535['markers']['dcv_trace'] ?? false)
) {
    $renderer_improved = true;
}

if (count($blocker_failures) > 0) {
    $qa_status = 'FAIL_BLOCKER';
} elseif (count($failures) > 0) {
    $qa_status = 'FAIL_CONTENT';
} elseif ($renderer_improved && !($render_3535['markers']['raw_markdown'] ?? false)) {
    $qa_status = 'PASS_RENDERER_ACTIVATED';
} elseif ($renderer_improved) {
    $qa_status = 'PASS_RENDER_TRACE_PRESENT_RAW_MARKDOWN_STILL_VISIBLE';
} else {
    $qa_status = 'PASS_CONTENT_ONLY_RENDERER_NOT_ACTIVATED';
}

$result = [
    'generated_at' => gmdate('c'),
    'qa_status' => $qa_status,
    'public_update_allowed' => false,
    'wordpress_write_allowed' => false,
    'source_post_write_allowed' => false,
    'source_unchanged_from_patch' => $source_unchanged_from_patch,
    'source_now' => $source_now,
    'clone_now' => $clone_now,
    'reference_now' => $reference_now,
    'clone_meta' => $clone_meta,
    'http_3535' => $http_3535,
    'render_3535' => [
        'html_length' => $render_3535['html_length'],
        'plain_length' => $render_3535['plain_length'],
        'markers' => $render_3535['markers'],
        'plain_excerpt' => $render_3535['plain_excerpt'],
    ],
    'render_2976' => [
        'html_length' => $render_2976['html_length'],
        'plain_length' => $render_2976['plain_length'],
        'markers' => $render_2976['markers'],
    ],
    'render_3042' => [
        'html_length' => $render_3042['html_length'],
        'plain_length' => $render_3042['plain_length'],
        'markers' => $render_3042['markers'],
    ],
    'renderer_improved' => $renderer_improved,
    'checks' => $checks,
    'fail_total_major_or_blocker' => count($failures),
    'blocker_fail_total' => count($blocker_failures),
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_post_patch_render_qa_result.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_post_patch_render_qa_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$md = [];
$md[] = '# 3535 post-patch render QA v1';
$md[] = '';
$md[] = 'Status: **' . $qa_status . '**';
$md[] = '';
$md[] = 'Ovaj QA ne mijenja WordPress. Provjerava privatni clone `3535` nakon upisa `_dry_recipe_id`.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Source post unchanged from patch: `' . ($source_unchanged_from_patch ? 'true' : 'false') . '`';
$md[] = '- Clone status: `' . $clone_now['post_status'] . '`';
$md[] = '- Clone `_dry_recipe_id`: `' . $clone_meta['_dry_recipe_id'] . '`';
$md[] = '- Public update allowed: `false`';
$md[] = '- HTTP public exposed: `' . ($http_3535['publicly_exposed'] ? 'true' : 'false') . '`';
$md[] = '- HTTP code: `' . $http_3535['http_code'] . '`';
$md[] = '- Renderer improved: `' . ($renderer_improved ? 'true' : 'false') . '`';
$md[] = '- 3535 raw markdown: `' . (($render_3535['markers']['raw_markdown'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- 3535 Drycured/recipe trace: `' . (($render_3535['markers']['drycured_recipe_trace'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- 3535 DC recipe class: `' . (($render_3535['markers']['dc_recipe_class'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- 3535 DCV trace: `' . (($render_3535['markers']['dcv_trace'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- Major/blocker fail total: `' . count($failures) . '`';
$md[] = '- Blocker fail total: `' . count($blocker_failures) . '`';
$md[] = '';
$md[] = '## Sadržajni elementi 3535';
$md[] = '';
$md[] = '| Element | Status |';
$md[] = '|---|---|';
foreach (['title','private_notice','raw_materials','spices','liquids_garlic','grinding','casing','done_when','errors_solutions','blockers'] as $k) {
    $md[] = '| `' . $k . '` | `' . (($render_3535['markers'][$k] ?? false) ? 'PASS' : 'FAIL') . '` |';
}
$md[] = '';
$md[] = '## QA tablica';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Zaključak';
$md[] = '';
if ($qa_status === 'PASS_RENDERER_ACTIVATED') {
    $md[] = 'Upis `_dry_recipe_id` aktivirao je bolji renderer bez javnog izlaganja. Sljedeći korak može biti vizualni admin pregled.';
} elseif ($qa_status === 'PASS_RENDER_TRACE_PRESENT_RAW_MARKDOWN_STILL_VISIBLE') {
    $md[] = 'Upis `_dry_recipe_id` dodao je renderer tragove, ali raw markdown je i dalje vidljiv. Potreban je dodatni read-only pregled plugin uvjeta ili admin-only preview most.';
} elseif ($qa_status === 'PASS_CONTENT_ONLY_RENDERER_NOT_ACTIVATED') {
    $md[] = 'Sadržaj je i dalje prisutan i siguran, ali `_dry_recipe_id` sam nije aktivirao postojeći kartični renderer. Ne mijenjati javni post; sljedeći korak je plan admin-only preview mosta ili analiza dodatnih meta uvjeta.';
} else {
    $md[] = 'Postoje QA padovi. Ne nastavljati dok se ne riješe.';
}
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/3535_POST_PATCH_RENDER_QA_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_3535_POST_PATCH_RENDER_QA_V1 -->';
$append = $marker . "\n\n" .
"## 3535 post-patch render QA v1\n\n" .
"Status: **" . $qa_status . "**\n\n" .
"- Source unchanged from patch: `" . ($source_unchanged_from_patch ? 'true' : 'false') . "`\n" .
"- Clone `_dry_recipe_id`: `" . $clone_meta['_dry_recipe_id'] . "`\n" .
"- Public update allowed: `false`\n" .
"- Publicly exposed: `" . ($http_3535['publicly_exposed'] ? 'true' : 'false') . "`\n" .
"- Renderer improved: `" . ($renderer_improved ? 'true' : 'false') . "`\n" .
"- Report: `review/" . basename($out_dir) . "/3535_POST_PATCH_RENDER_QA_REPORT.md`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 3535 POST-PATCH RENDER QA COMPLETE ===\n";
echo "QA_STATUS=" . $qa_status . "\n";
echo "SOURCE_UNCHANGED_FROM_PATCH=" . ($source_unchanged_from_patch ? 'true' : 'false') . "\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "PUBLICLY_EXPOSED=" . ($http_3535['publicly_exposed'] ? 'true' : 'false') . "\n";
echo "CLONE_3535_STATUS=" . $clone_now['post_status'] . "\n";
echo "CLONE_3535_DRY_RECIPE_ID=" . $clone_meta['_dry_recipe_id'] . "\n";
echo "RENDERER_IMPROVED=" . ($renderer_improved ? 'true' : 'false') . "\n";
echo "RAW_MARKDOWN=" . (($render_3535['markers']['raw_markdown'] ?? false) ? 'true' : 'false') . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_POST_PATCH_RENDER_QA_REPORT.md\n";
