<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_RENDER_INSPECTION_OUT');
$qa_path = getenv('DC_RENDER_INSPECTION_QA');

if (!$out_dir || !$qa_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc_inspect_read($path) {
    return is_readable($path) ? file_get_contents($path) : '';
}

function dc_inspect_meta($post_id) {
    $all = get_post_meta($post_id);
    $out = [];
    foreach ($all as $k => $vals) {
        if (strpos($k, '_dry_') === 0 || strpos($k, 'dry_') === 0 || strpos($k, '_wprm') === 0 || strpos($k, 'wprm') === 0) {
            $v = is_array($vals) && isset($vals[0]) ? $vals[0] : '';
            $out[$k] = [
                'length' => is_string($v) ? strlen($v) : 0,
                'json_valid' => is_string($v) && strlen($v) > 0 && json_decode($v, true) !== null,
                'preview' => is_string($v) ? mb_substr($v, 0, 280) : '',
            ];
        }
    }
    ksort($out);
    return $out;
}

function dc_render_snapshot($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return [
            'exists' => false,
            'html_length' => 0,
            'plain_length' => 0,
            'markers' => [],
            'excerpt' => '',
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
        'dcv5' => stripos($html, 'dcv5') !== false,
        'dcv' => stripos($html, 'dcv') !== false,
        'drycured_recipe' => stripos($html, 'drycured') !== false && stripos($html, 'recipe') !== false,
        'dc_recipe' => stripos($html, 'dc-recipe') !== false,
        'wprm' => stripos($html, 'wprm') !== false,
        'raw_markdown_h1' => strpos($html, '# ') !== false,
        'private_notice' => stripos($html, 'PRIVATNI PREVIEW') !== false || stripos($html, 'nije javni recept') !== false,
        'granulation' => stripos($html, 'granulacija') !== false || stripos($html, 'rešetka') !== false,
        'timeline' => stripos($html, 'kronologija') !== false || stripos($html, 'proces') !== false,
    ];

    return [
        'exists' => true,
        'post_status' => $p->post_status,
        'post_type' => $p->post_type,
        'post_title' => $p->post_title,
        'post_name' => $p->post_name,
        'html_length' => strlen($html),
        'plain_length' => strlen($plain),
        'markers' => $markers,
        'excerpt' => mb_substr($plain, 0, 1400),
    ];
}

function dc_file_scan($path) {
    $txt = dc_inspect_read($path);
    if ($txt === '') {
        return [
            'path' => $path,
            'exists' => false,
        ];
    }

    $patterns = [
        'add_filter_the_content' => '/add_filter\s*\(\s*[\'"]the_content[\'"]/',
        'dry_recipe_full_markdown' => '/_dry_recipe_full_markdown/',
        'dry_recipe_sections' => '/_dry_recipe_sections/',
        'dry_verified_process' => '/_dry_verified_process/',
        'dry_recipe_data' => '/_dry_recipe_data/',
        'post_status_private' => '/post_status|private/',
        'is_singular' => '/is_singular/',
        'dry_recipe_post_type' => '/dry_recipe/',
        'dcv_marker' => '/dcv[0-9_\\-]*/i',
        'shortcode' => '/add_shortcode/',
    ];

    $found = [];
    foreach ($patterns as $name => $rx) {
        preg_match_all($rx, $txt, $m);
        $found[$name] = count($m[0]);
    }

    $lines = preg_split('/\R/', $txt);
    $interesting = [];
    foreach ($lines as $i => $line) {
        if (
            stripos($line, 'the_content') !== false ||
            stripos($line, '_dry_recipe') !== false ||
            stripos($line, '_dry_verified_process') !== false ||
            stripos($line, 'dry_recipe') !== false ||
            stripos($line, 'dcv') !== false ||
            stripos($line, 'function ') !== false && stripos($line, 'recipe') !== false
        ) {
            $interesting[] = [
                'line' => $i + 1,
                'text' => mb_substr(trim($line), 0, 240),
            ];
        }
    }

    return [
        'path' => $path,
        'exists' => true,
        'bytes' => strlen($txt),
        'pattern_counts' => $found,
        'interesting_lines' => array_slice($interesting, 0, 240),
    ];
}

$repo_plugin = '/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php';
$live_plugin = '/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php';

$reference_ids = [
    2976 => 'REFERENCE_PUBLIC_SLAVONSKA_DOMACA_KOBASICA',
    3042 => 'SOURCE_PUBLIC_JESUS_DE_LYON',
    3535 => 'PRIVATE_CLONE_JESUS_DE_LYON',
];

$posts = [];
foreach ($reference_ids as $id => $label) {
    $p = get_post($id);
    $posts[$id] = [
        'label' => $label,
        'exists' => (bool)$p,
        'post' => $p ? [
            'ID' => $p->ID,
            'post_title' => $p->post_title,
            'post_name' => $p->post_name,
            'post_status' => $p->post_status,
            'post_type' => $p->post_type,
            'post_modified_gmt' => $p->post_modified_gmt,
            'content_length' => strlen($p->post_content),
        ] : null,
        'meta' => $p ? dc_inspect_meta($id) : [],
        'render' => $p ? dc_render_snapshot($id) : [],
    ];
}

$repo_scan = dc_file_scan($repo_plugin);
$live_scan = dc_file_scan($live_plugin);

$all_meta_keys = [];
foreach ($posts as $id => $data) {
    foreach (array_keys($data['meta']) as $k) {
        $all_meta_keys[$k] = true;
    }
}
$all_meta_keys = array_keys($all_meta_keys);
sort($all_meta_keys);

$meta_matrix = [];
foreach ($all_meta_keys as $key) {
    $row = ['meta_key' => $key];
    foreach ($posts as $id => $data) {
        $row[(string)$id] = array_key_exists($key, $data['meta']) ? 'YES' : 'NO';
    }
    $meta_matrix[] = $row;
}

$renderer_likely_requires = [];
foreach ([$repo_scan, $live_scan] as $scan) {
    if (!$scan['exists']) continue;
    foreach ($scan['interesting_lines'] as $line) {
        $txt = $line['text'];
        if (strpos($txt, '_dry_recipe') !== false || strpos($txt, '_dry_verified_process') !== false) {
            $renderer_likely_requires[] = $scan['path'] . ':' . $line['line'] . ': ' . $txt;
        }
    }
}

$diagnosis = [];
$clone_markers = $posts[3535]['render']['markers'] ?? [];
$ref_markers = $posts[2976]['render']['markers'] ?? [];
$clone_meta = $posts[3535]['meta'] ?? [];
$ref_meta = $posts[2976]['meta'] ?? [];

if (($clone_markers['raw_markdown_h1'] ?? false) === true) {
    $diagnosis[] = 'Clone 3535 renderira raw markdown/post_content, ne dokazani kartični renderer.';
}
if (($clone_markers['dcv'] ?? false) === false && ($ref_markers['dcv'] ?? false) === true) {
    $diagnosis[] = 'Referentni 2976 ima DCV marker, a clone 3535 nema; treba pronaći točan renderer trigger.';
}
if (!array_key_exists('_dry_recipe_data', $clone_meta) && array_key_exists('_dry_recipe_data', $ref_meta)) {
    $diagnosis[] = 'Clone 3535 nema _dry_recipe_data, a referentni recept ga možda ima; provjeriti koristi li renderer taj ključ.';
}
if (array_key_exists('_dry_recipe_sections', $clone_meta) && !($clone_markers['dcv'] ?? false)) {
    $diagnosis[] = 'Clone ima _dry_recipe_sections, ali renderer se ne aktivira; vjerojatno treba dodatni trigger, status, shortcode, template uvjet ili drugačiji meta format.';
}
if (empty($diagnosis)) {
    $diagnosis[] = 'Nema očite razlike iz osnovne inspekcije; treba ručno pregledati plugin linije.';
}

$out = [
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_RENDERER_CONTRACT_INSPECTION',
    'public_update_allowed' => false,
    'wordpress_write_allowed' => false,
    'posts' => $posts,
    'meta_matrix' => $meta_matrix,
    'repo_plugin_scan' => $repo_scan,
    'live_plugin_scan' => $live_scan,
    'renderer_likely_requires' => array_values(array_unique($renderer_likely_requires)),
    'diagnosis' => $diagnosis,
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_renderer_contract_inspection_v1.json',
    wp_json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_renderer_meta_matrix.csv', 'w');
fputcsv($csv, ['meta_key', '2976_reference', '3042_source', '3535_clone']);
foreach ($meta_matrix as $r) {
    fputcsv($csv, [$r['meta_key'], $r['2976'] ?? 'NO', $r['3042'] ?? 'NO', $r['3535'] ?? 'NO']);
}
fclose($csv);

$md = [];
$md[] = '# 3535 renderer contract inspection v1';
$md[] = '';
$md[] = 'Status: **READ_ONLY_INSPECTION_COMPLETE**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Uspoređuje referentni javni recept `2976`, javni source `3042` i privatni clone `3535`.';
$md[] = '';
$md[] = '## Sažetak rendera';
$md[] = '';
$md[] = '| Post | Status | Type | HTML length | DCV marker | WPRM marker | Raw markdown |';
$md[] = '|---|---|---|---:|---|---|---|';
foreach ($posts as $id => $data) {
    $r = $data['render'];
    $m = $r['markers'] ?? [];
    $md[] = '| `' . $id . '` ' . $data['label'] .
        ' | `' . ($data['post']['post_status'] ?? 'NA') .
        '` | `' . ($data['post']['post_type'] ?? 'NA') .
        '` | `' . ($r['html_length'] ?? 0) .
        '` | `' . (($m['dcv'] ?? false) ? 'true' : 'false') .
        '` | `' . (($m['wprm'] ?? false) ? 'true' : 'false') .
        '` | `' . (($m['raw_markdown_h1'] ?? false) ? 'true' : 'false') . '` |';
}
$md[] = '';
$md[] = '## Dijagnoza';
$md[] = '';
foreach ($diagnosis as $d) {
    $md[] = '- ' . $d;
}
$md[] = '';
$md[] = '## Plugin scan';
$md[] = '';
$md[] = '- Repo plugin exists: `' . (($repo_scan['exists'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- Live plugin exists: `' . (($live_scan['exists'] ?? false) ? 'true' : 'false') . '`';
$md[] = '- Repo plugin bytes: `' . ($repo_scan['bytes'] ?? 0) . '`';
$md[] = '- Live plugin bytes: `' . ($live_scan['bytes'] ?? 0) . '`';
$md[] = '';
$md[] = '## Mogući renderer triggeri / meta reference';
$md[] = '';
if (empty($renderer_likely_requires)) {
    $md[] = '- Nema pronađenih meta referenci u osnovnom skenu.';
} else {
    foreach (array_slice(array_values(array_unique($renderer_likely_requires)), 0, 80) as $line) {
        $md[] = '- `' . str_replace('`', "'", $line) . '`';
    }
}
$md[] = '';
$md[] = '## Meta matrix';
$md[] = '';
$md[] = 'Detaljna meta matrix spremljena je u `3535_renderer_meta_matrix.csv`.';
$md[] = '';
$md[] = '## Zaključak';
$md[] = '';
$md[] = 'Privatni clone je siguran, ali treba utvrditi točan uvjet aktivacije postojećeg Drycured/DCV renderera. Sljedeći korak ne smije mijenjati dizajn, nego samo predložiti minimalni admin-only preview most ili potrebni meta normalizer za privatni clone.';
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/3535_RENDERER_CONTRACT_INSPECTION_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_3535_RENDERER_CONTRACT_INSPECTION_V1 -->';
$append = $marker . "\n\n" .
"## 3535 renderer contract inspection v1\n\n" .
"Status: **READ_ONLY_INSPECTION_COMPLETE**\n\n" .
"- Public update allowed: `false`\n" .
"- WordPress write allowed: `false`\n" .
"- Report: `review/" . basename($out_dir) . "/3535_RENDERER_CONTRACT_INSPECTION_REPORT.md`\n" .
"- JSON: `review/" . basename($out_dir) . "/3535_renderer_contract_inspection_v1.json`\n" .
"- Meta matrix: `review/" . basename($out_dir) . "/3535_renderer_meta_matrix.csv`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 3535 RENDERER CONTRACT INSPECTION COMPLETE ===\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "REPO_PLUGIN_EXISTS=" . (($repo_scan['exists'] ?? false) ? 'true' : 'false') . "\n";
echo "LIVE_PLUGIN_EXISTS=" . (($live_scan['exists'] ?? false) ? 'true' : 'false') . "\n";
echo "POST_2976_DCV_MARKER=" . (($posts[2976]['render']['markers']['dcv'] ?? false) ? 'true' : 'false') . "\n";
echo "POST_3535_DCV_MARKER=" . (($posts[3535]['render']['markers']['dcv'] ?? false) ? 'true' : 'false') . "\n";
echo "POST_3535_RAW_MARKDOWN=" . (($posts[3535]['render']['markers']['raw_markdown_h1'] ?? false) ? 'true' : 'false') . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_RENDERER_CONTRACT_INSPECTION_REPORT.md\n";
