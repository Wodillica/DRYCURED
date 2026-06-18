<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_RENDER_DEEP_OUT');
$qa_path = getenv('DC_RENDER_DEEP_QA');

if (!$out_dir || !$qa_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc_deep_bool($v) {
    return $v ? 'true' : 'false';
}

function dc_deep_file($path) {
    return is_readable($path) ? file_get_contents($path) : '';
}

function dc_deep_context_lines($path, $center, $radius = 28) {
    $txt = dc_deep_file($path);
    if ($txt === '') return [];
    $lines = preg_split('/\R/', $txt);
    $start = max(1, $center - $radius);
    $end = min(count($lines), $center + $radius);
    $out = [];
    for ($i = $start; $i <= $end; $i++) {
        $out[] = [
            'line' => $i,
            'text' => $lines[$i - 1],
        ];
    }
    return $out;
}

function dc_deep_find_lines($path, $patterns) {
    $txt = dc_deep_file($path);
    if ($txt === '') return [];
    $lines = preg_split('/\R/', $txt);
    $out = [];
    foreach ($lines as $i => $line) {
        foreach ($patterns as $name => $rx) {
            if (preg_match($rx, $line)) {
                $out[] = [
                    'pattern' => $name,
                    'line' => $i + 1,
                    'text' => trim($line),
                ];
                break;
            }
        }
    }
    return $out;
}

function dc_deep_post_snapshot($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return ['exists' => false];
    }

    $all_meta = get_post_meta($post_id);
    $meta = [];
    foreach ($all_meta as $k => $vals) {
        if (
            strpos($k, '_dry_') === 0 ||
            strpos($k, 'dry_') === 0 ||
            strpos($k, '_wprm') === 0 ||
            strpos($k, 'wprm') === 0
        ) {
            $v = is_array($vals) && isset($vals[0]) ? $vals[0] : '';
            $meta[$k] = [
                'length' => is_string($v) ? strlen($v) : 0,
                'is_json' => is_string($v) && json_decode($v, true) !== null,
                'preview' => is_string($v) ? mb_substr($v, 0, 400) : '',
            ];
        }
    }
    ksort($meta);

    global $post;
    $old = $post ?? null;
    $post = $p;
    setup_postdata($post);
    $filtered = apply_filters('the_content', $p->post_content);
    wp_reset_postdata();
    $post = $old;

    return [
        'exists' => true,
        'ID' => $p->ID,
        'title' => $p->post_title,
        'name' => $p->post_name,
        'status' => $p->post_status,
        'type' => $p->post_type,
        'permalink' => get_permalink($post_id),
        'content_length' => strlen($p->post_content),
        'filtered_length' => strlen($filtered),
        'filtered_has_raw_markdown' => strpos($filtered, '# ') !== false,
        'filtered_excerpt' => mb_substr(trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($filtered))), 0, 1600),
        'meta' => $meta,
    ];
}

function dc_deep_http_snapshot($url) {
    $res = wp_remote_get($url, [
        'timeout' => 12,
        'redirection' => 5,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'DrycuredRendererDeepInspection/1.0'
        ]
    ]);

    if (is_wp_error($res)) {
        return [
            'ok' => false,
            'error' => $res->get_error_message(),
            'http_code' => '',
            'body_length' => 0,
            'markers' => [],
            'excerpt' => '',
        ];
    }

    $code = (string) wp_remote_retrieve_response_code($res);
    $body = (string) wp_remote_retrieve_body($res);
    $plain = trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($body)));

    $markers = [
        'body_has_dcv' => stripos($body, 'dcv') !== false,
        'body_has_drycured_recipe' => stripos($body, 'drycured') !== false && stripos($body, 'recipe') !== false,
        'body_has_dc_recipe_class' => stripos($body, 'dc-recipe') !== false,
        'body_has_recipe_hero' => stripos($body, 'hero') !== false && stripos($body, 'recipe') !== false,
        'body_has_wprm' => stripos($body, 'wprm') !== false,
        'body_has_raw_markdown' => strpos($body, '# ') !== false,
        'body_has_granulation' => stripos($plain, 'granulacija') !== false || stripos($plain, 'rešetka') !== false,
        'body_has_kronologija' => stripos($plain, 'kronologija') !== false,
        'body_has_sigurnosni_semafor' => stripos($plain, 'sigurnosni semafor') !== false,
        'body_has_dnevnik_sarze' => stripos($plain, 'dnevnik šarže') !== false || stripos($plain, 'dnevnik sarže') !== false,
        'body_has_private_notice' => stripos($plain, 'privatni preview') !== false || stripos($plain, 'nije javni recept') !== false,
    ];

    $needle_excerpts = [];
    foreach (['Slavonska domaća kobasica', 'Jésus de Lyon', 'Mljevenje', 'Obavezna granulacija', 'Sigurnosni semafor', 'Dnevnik šarže'] as $needle) {
        $pos = mb_stripos($plain, $needle);
        if ($pos !== false) {
            $needle_excerpts[$needle] = mb_substr($plain, max(0, $pos - 240), 700);
        }
    }

    return [
        'ok' => true,
        'http_code' => $code,
        'body_length' => strlen($body),
        'plain_length' => strlen($plain),
        'markers' => $markers,
        'excerpt' => mb_substr($plain, 0, 1800),
        'needle_excerpts' => $needle_excerpts,
    ];
}

$repo_plugin = '/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php';
$live_plugin = '/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php';

$post_ids = [
    2976 => 'REFERENCE_PUBLIC_SLAVONSKA_DOMACA_KOBASICA',
    3042 => 'SOURCE_PUBLIC_JESUS_DE_LYON',
    3535 => 'PRIVATE_CLONE_JESUS_DE_LYON',
];

$posts = [];
foreach ($post_ids as $id => $label) {
    $snap = dc_deep_post_snapshot($id);
    $snap['label'] = $label;
    if (!empty($snap['permalink']) && $snap['status'] !== 'private') {
        $snap['http'] = dc_deep_http_snapshot($snap['permalink']);
    } elseif (!empty($snap['permalink'])) {
        $snap['http'] = dc_deep_http_snapshot($snap['permalink']);
    } else {
        $snap['http'] = null;
    }
    $posts[$id] = $snap;

    file_put_contents(
        rtrim($out_dir, '/') . '/' . $id . '_post_snapshot.json',
        wp_json_encode($snap, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
}

$patterns = [
    'add_filter_the_content' => '/add_filter\s*\(\s*[\'"]the_content[\'"]/',
    'function_recipe' => '/function\s+[a-zA-Z0-9_]*recipe/i',
    'get_post_meta_dry' => '/get_post_meta\s*\([^;]*_dry_/i',
    'dry_recipe_full_markdown' => '/_dry_recipe_full_markdown/',
    'dry_recipe_id' => '/_dry_recipe_id/',
    'dry_recipe_image_url' => '/_dry_recipe_image_url/',
    'dry_recipe_sections' => '/_dry_recipe_sections/',
    'dry_verified_process' => '/_dry_verified_process/',
    'is_singular' => '/is_singular/',
    'post_type_dry_recipe' => '/dry_recipe/',
    'is_admin' => '/is_admin/',
    'current_user_can' => '/current_user_can/',
];

$repo_lines = dc_deep_find_lines($repo_plugin, $patterns);
$live_lines = dc_deep_find_lines($live_plugin, $patterns);

$line_contexts = [
    'repo_120_180' => dc_deep_context_lines($repo_plugin, 150, 36),
    'repo_680_770' => dc_deep_context_lines($repo_plugin, 725, 55),
    'live_120_180' => dc_deep_context_lines($live_plugin, 150, 36),
    'live_680_770' => dc_deep_context_lines($live_plugin, 725, 55),
];

file_put_contents(
    rtrim($out_dir, '/') . '/plugin_relevant_lines_repo.json',
    wp_json_encode($repo_lines, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);
file_put_contents(
    rtrim($out_dir, '/') . '/plugin_relevant_lines_live.json',
    wp_json_encode($live_lines, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);
file_put_contents(
    rtrim($out_dir, '/') . '/plugin_line_contexts.json',
    wp_json_encode($line_contexts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$diagnosis = [];

$http2976 = $posts[2976]['http']['markers'] ?? [];
$filtered2976_raw = $posts[2976]['filtered_has_raw_markdown'] ?? false;
$http2976_raw = $http2976['body_has_raw_markdown'] ?? false;
$http2976_dry = ($http2976['body_has_drycured_recipe'] ?? false) || ($http2976['body_has_dc_recipe_class'] ?? false) || ($http2976['body_has_dcv'] ?? false);

if ($filtered2976_raw && !$http2976_raw) {
    $diagnosis[] = 'Interni apply_filters za 2976 pokazuje markdown, ali javni HTTP ne pokazuje raw markdown; renderer/template se vjerojatno aktivira u punom front-end kontekstu.';
}
if ($filtered2976_raw && $http2976_raw) {
    $diagnosis[] = 'I interni i javni HTTP 2976 pokazuju raw markdown marker; moguće je da renderer koristi Markdown kao izvorni format ili da marker test nije dovoljan.';
}
if ($http2976_dry) {
    $diagnosis[] = 'Javni HTTP 2976 ima Drycured/recipe/DCV tragove; treba usporediti koji uvjet izostaje na 3535.';
}
if (!isset($posts[3535]['meta']['_dry_recipe_id'])) {
    $diagnosis[] = 'Clone 3535 nema _dry_recipe_id; plugin na više mjesta referencira _dry_recipe_id i moguće je da je to minimalni trigger za kanonski renderer.';
}
if (!isset($posts[3535]['meta']['_dry_recipe_image_url'])) {
    $diagnosis[] = 'Clone 3535 nema _dry_recipe_image_url; to možda nije blocker, ali plugin ga referencira za hero/sliku.';
}
if (isset($posts[3535]['meta']['_dry_recipe_full_markdown']) && !isset($posts[3535]['meta']['_dry_recipe_id'])) {
    $diagnosis[] = 'Clone 3535 ima _dry_recipe_full_markdown, ali nema identifikacijski meta ključ _dry_recipe_id; sljedeći test treba biti meta-normalizer plan, ne promjena renderera.';
}

$out = [
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_RENDERER_ACTIVATION_DEEP_INSPECTION',
    'public_update_allowed' => false,
    'wordpress_write_allowed' => false,
    'posts' => $posts,
    'repo_plugin_exists' => is_readable($repo_plugin),
    'live_plugin_exists' => is_readable($live_plugin),
    'repo_plugin_bytes' => is_readable($repo_plugin) ? filesize($repo_plugin) : 0,
    'live_plugin_bytes' => is_readable($live_plugin) ? filesize($live_plugin) : 0,
    'plugin_relevant_lines_repo_count' => count($repo_lines),
    'plugin_relevant_lines_live_count' => count($live_lines),
    'diagnosis' => $diagnosis,
];

file_put_contents(
    rtrim($out_dir, '/') . '/renderer_activation_deep_inspection_v1.json',
    wp_json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$md = [];
$md[] = '# Renderer activation deep inspection v1';
$md[] = '';
$md[] = 'Status: **READ_ONLY_DEEP_INSPECTION_COMPLETE**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Cilj je razlikovati interni `apply_filters` snapshot od stvarnog javnog HTTP prikaza i pronaći vjerojatne uvjete aktivacije postojećeg renderera.';
$md[] = '';
$md[] = '## Sažetak po postu';
$md[] = '';
$md[] = '| Post | Status | HTTP | Filtered raw markdown | HTTP raw markdown | HTTP Drycured/recipe trag |';
$md[] = '|---|---|---:|---|---|---|';
foreach ($posts as $id => $p) {
    $hm = $p['http']['markers'] ?? [];
    $http_code = $p['http']['http_code'] ?? '';
    $http_dry = (($hm['body_has_drycured_recipe'] ?? false) || ($hm['body_has_dc_recipe_class'] ?? false) || ($hm['body_has_dcv'] ?? false));
    $md[] = '| `' . $id . '` ' . $p['label'] .
        ' | `' . ($p['status'] ?? 'NA') .
        '` | `' . $http_code .
        '` | `' . dc_deep_bool($p['filtered_has_raw_markdown'] ?? false) .
        '` | `' . dc_deep_bool($hm['body_has_raw_markdown'] ?? false) .
        '` | `' . dc_deep_bool($http_dry) . '` |';
}
$md[] = '';
$md[] = '## Dijagnoza';
$md[] = '';
foreach ($diagnosis as $d) {
    $md[] = '- ' . $d;
}
$md[] = '';
$md[] = '## Plugin linije — najvažniji tragovi';
$md[] = '';
$md[] = '- Repo plugin exists: `' . dc_deep_bool(is_readable($repo_plugin)) . '`';
$md[] = '- Live plugin exists: `' . dc_deep_bool(is_readable($live_plugin)) . '`';
$md[] = '- Repo relevant lines: `' . count($repo_lines) . '`';
$md[] = '- Live relevant lines: `' . count($live_lines) . '`';
$md[] = '';
$important = [];
foreach ($live_lines as $l) {
    if (
        strpos($l['text'], '_dry_recipe_full_markdown') !== false ||
        strpos($l['text'], '_dry_recipe_id') !== false ||
        strpos($l['text'], '_dry_recipe_image_url') !== false ||
        strpos($l['text'], 'the_content') !== false
    ) {
        $important[] = $l;
    }
}
foreach (array_slice($important, 0, 60) as $l) {
    $md[] = '- line `' . $l['line'] . '`: `' . str_replace('`', "'", mb_substr($l['text'], 0, 220)) . '`';
}
$md[] = '';
$md[] = '## Izlazne datoteke';
$md[] = '';
$md[] = '- `renderer_activation_deep_inspection_v1.json`';
$md[] = '- `plugin_relevant_lines_repo.json`';
$md[] = '- `plugin_relevant_lines_live.json`';
$md[] = '- `plugin_line_contexts.json`';
$md[] = '- `2976_post_snapshot.json`';
$md[] = '- `3042_post_snapshot.json`';
$md[] = '- `3535_post_snapshot.json`';
$md[] = '';
$md[] = '## Zaključak';
$md[] = '';
$md[] = 'Sljedeći korak treba biti minimalni **meta-normalizer plan** za privatni clone: ne mijenjati renderer, nego provjeriti koji meta ključevi nedostaju za aktivaciju postojećeg prikaza, osobito `_dry_recipe_id` i eventualno `_dry_recipe_image_url`.';
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/RENDERER_ACTIVATION_DEEP_INSPECTION_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_RENDERER_ACTIVATION_DEEP_INSPECTION_V1 -->';
$append = $marker . "\n\n" .
"## Renderer activation deep inspection v1\n\n" .
"Status: **READ_ONLY_DEEP_INSPECTION_COMPLETE**\n\n" .
"- Public update allowed: `false`\n" .
"- WordPress write allowed: `false`\n" .
"- Report: `review/" . basename($out_dir) . "/RENDERER_ACTIVATION_DEEP_INSPECTION_REPORT.md`\n" .
"- JSON: `review/" . basename($out_dir) . "/renderer_activation_deep_inspection_v1.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== RENDERER ACTIVATION DEEP INSPECTION COMPLETE ===\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "POST_2976_HTTP_CODE=" . ($posts[2976]['http']['http_code'] ?? '') . "\n";
echo "POST_3042_HTTP_CODE=" . ($posts[3042]['http']['http_code'] ?? '') . "\n";
echo "POST_3535_HTTP_CODE=" . ($posts[3535]['http']['http_code'] ?? '') . "\n";
echo "POST_2976_HTTP_RAW_MARKDOWN=" . dc_deep_bool($posts[2976]['http']['markers']['body_has_raw_markdown'] ?? false) . "\n";
echo "POST_3535_HAS_DRY_RECIPE_ID=" . dc_deep_bool(isset($posts[3535]['meta']['_dry_recipe_id'])) . "\n";
echo "POST_3535_HAS_IMAGE_URL=" . dc_deep_bool(isset($posts[3535]['meta']['_dry_recipe_image_url'])) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/RENDERER_ACTIVATION_DEEP_INSPECTION_REPORT.md\n";
