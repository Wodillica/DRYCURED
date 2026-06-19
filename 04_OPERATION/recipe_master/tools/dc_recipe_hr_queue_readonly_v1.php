<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_HR_QUEUE_OUT');
$repo = getenv('DC_HR_QUEUE_REPO');

if (!$out_dir || !$repo) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc_hr_lower($s) {
    $s = (string)$s;
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($s, 'UTF-8');
    }
    return strtolower($s);
}

function dc_hr_contains_any($haystack, $needles) {
    $h = dc_hr_lower($haystack);
    foreach ($needles as $n) {
        if ($n !== '' && strpos($h, dc_hr_lower($n)) !== false) {
            return true;
        }
    }
    return false;
}

function dc_hr_count_hits($haystack, $needles) {
    $h = dc_hr_lower($haystack);
    $hits = [];
    foreach ($needles as $n) {
        if ($n !== '' && strpos($h, dc_hr_lower($n)) !== false) {
            $hits[] = $n;
        }
    }
    return $hits;
}

function dc_hr_meta_text($post_id) {
    $keys = [
        '_dry_recipe_id',
        '_dry_recipe_full_markdown',
        '_dry_recipe_sections',
        '_dry_verified_process',
        '_dry_recipe_type_router',
        '_dry_recipe_source_validation_status',
        '_wprm_recipe',
        'wprm_recipe'
    ];

    $parts = [];
    foreach ($keys as $k) {
        $v = get_post_meta($post_id, $k, true);
        if (is_string($v) && $v !== '') {
            $parts[] = $k . ': ' . mb_substr($v, 0, 3000);
        }
    }
    return implode("\n", $parts);
}

function dc_hr_classify_type($text) {
    $t = dc_hr_lower($text);

    $fish = ['riba', 'tuna', 'srdela', 'inćun', 'losos', 'bakalar', 'oslić', 'dimljena riba', 'morski'];
    $thermal = ['barenje', 'barena', 'kuhana', 'kuhanje', 'parenje', 'parena', 'pečenje', 'pečena', 'čvarci', 'krvavica', 'tlačenica', 'švargl', 'prezvuršt'];
    $ground = ['kobasica', 'kobasice', 'salama', 'kulen', 'kulenova', 'sudžuk', 'češnjovka', 'ćevap', 'mljeven', 'rešetka', 'crijeva', 'punjenje'];
    $whole = ['pršut', 'šunka', 'panceta', 'slanina', 'vrat', 'buđola', 'but', 'plećka', 'rebra', 'pečenica', 'filet', 'lopatica u komadu'];

    if (dc_hr_contains_any($t, $fish)) {
        return 'FISH_OR_SEAFOOD';
    }
    if (dc_hr_contains_any($t, $thermal)) {
        return 'THERMAL_PROCESSED';
    }
    if (dc_hr_contains_any($t, $ground)) {
        return 'GROUND_MEAT_OR_CASING';
    }
    if (dc_hr_contains_any($t, $whole)) {
        return 'WHOLE_CUT';
    }
    return 'NEEDS_CLASSIFICATION';
}

function dc_hr_existing_dossier_hint($repo, $post_id, $slug) {
    $paths = [];
    $base = rtrim($repo, '/') . '/04_OPERATION/recipe_master/dossiers';
    if (!is_dir($base)) {
        return [];
    }

    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $needle_id = (string)$post_id;
    $needle_slug = dc_hr_lower((string)$slug);

    foreach ($it as $file) {
        if (!$file->isDir()) {
            continue;
        }

        $name = dc_hr_lower($file->getFilename());
        $path = $file->getPathname();

        if (strpos($name, $needle_id) !== false || ($needle_slug !== '' && strpos($name, $needle_slug) !== false)) {
            $paths[] = str_replace(rtrim($repo, '/') . '/', '', $path);
            if (count($paths) >= 5) {
                break;
            }
        }
    }

    return $paths;
}

$country_terms = [
    'hrvatska', 'hrvatski', 'hrvatsko', 'croatia', 'croatian', 'hr-'
];

$region_terms = [
    'slavonija', 'slavonski', 'slavonska', 'baranja', 'srijem', 'srem',
    'dalmacija', 'dalmatinski', 'dalmatinska', 'istra', 'istarski', 'istarska',
    'lika', 'lički', 'lička', 'kvarner', 'primorje', 'gorski kotar',
    'zagorje', 'zagorski', 'međimurje', 'međimurski', 'podravina', 'podravski',
    'posavina', 'banija', 'banovina', 'kordun', 'moslavina', 'prigorje',
    'turopolje', 'zagreb', 'samobor', 'sinj', 'sinjska', 'vrgorac', 'vrgorački',
    'korčula', 'korčulanska', 'hvar', 'hvarska', 'rovinj', 'rovinjska',
    'pazin', 'pazinska', 'krk', 'dubrovnik', 'šibenik', 'poljica'
];

$product_terms = [
    'slavonski kulen', 'slavonska kobasica', 'kulenova seka', 'kulen',
    'domaća kobasica', 'češnjovka', 'istarska kobasica', 'rovinjska kobasica',
    'pazinska kobasica', 'lička kobasica', 'sinjska kobasica', 'dalmatinska kobasica',
    'hvarska prstena', 'vrgorački kulen', 'dalmatinski pršut', 'istarski pršut',
    'drniški pršut', 'krčki pršut', 'panceta', 'buđola', 'sušeni vrat',
    'slanina', 'pečenica', 'krvavica', 'tlačenica'
];

$exclude_terms = [
    'preview —', 'preview -', 'privatno:', 'private preview', 'test recept'
];

$posts = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => ['publish', 'private', 'draft'],
    'numberposts' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
]);

$rows = [];
$excluded_preview = 0;

foreach ($posts as $p) {
    $post_id = (int)$p->ID;

    $preview_mode = get_post_meta($post_id, '_dry_recipe_preview_mode', true);
    $preview_source = get_post_meta($post_id, '_dry_recipe_preview_source_post_id', true);

    $title = (string)$p->post_title;
    $slug = (string)$p->post_name;
    $content = (string)$p->post_content;
    $meta_text = dc_hr_meta_text($post_id);
    $all = $title . "\n" . $slug . "\n" . $content . "\n" . $meta_text;

    if ($preview_mode === 'PRIVATE_CLONE_ONLY' || $preview_source !== '' || dc_hr_contains_any($title, $exclude_terms)) {
        $excluded_preview++;
        continue;
    }

    $country_hits = dc_hr_count_hits($all, $country_terms);
    $region_hits = dc_hr_count_hits($all, $region_terms);
    $product_hits = dc_hr_count_hits($all, $product_terms);

    $recipe_id = get_post_meta($post_id, '_dry_recipe_id', true);
    $has_hr_code = is_string($recipe_id) && strpos($recipe_id, 'HR-') === 0;

    $score = 0;
    if ($has_hr_code) {
        $score += 12;
    }
    $score += count($country_hits) * 5;
    $score += count($region_hits) * 3;
    $score += count($product_hits) * 4;

    $is_hr = $score >= 6 || $has_hr_code;

    if (!$is_hr) {
        continue;
    }

    $sections = get_post_meta($post_id, '_dry_recipe_sections', true);
    $process = get_post_meta($post_id, '_dry_verified_process', true);
    $full_md = get_post_meta($post_id, '_dry_recipe_full_markdown', true);
    $image = get_post_meta($post_id, '_dry_recipe_image_url', true);

    $has_sections = is_string($sections) && strlen($sections) > 200;
    $has_process = is_string($process) && strlen($process) > 200;
    $has_full_md = is_string($full_md) && strlen($full_md) > 500;
    $has_image = is_string($image) && trim($image) !== '';

    $type = dc_hr_classify_type($all);

    $source_status = get_post_meta($post_id, '_dry_recipe_source_validation_status', true);
    $dossier_hints = dc_hr_existing_dossier_hint($repo, $post_id, $slug);

    $needs_work_score = 0;
    if (!$has_sections) $needs_work_score += 3;
    if (!$has_process) $needs_work_score += 3;
    if (!$has_full_md) $needs_work_score += 2;
    if (!$has_image) $needs_work_score += 1;
    if ($source_status === '') $needs_work_score += 2;

    $reference_like = false;
    $ltitle = dc_hr_lower($title);
    if (
        (strpos($ltitle, 'slavonska domaća kobasica') !== false || strpos($ltitle, 'slavonska kobasica') !== false || strpos($ltitle, 'slavonski kulen') !== false)
        && $has_sections && $has_process
    ) {
        $reference_like = true;
    }

    if ($reference_like) {
        $priority = 'REFERENCE_OR_ALREADY_STRUCTURED';
    } elseif ($p->post_status === 'publish' && $needs_work_score >= 5) {
        $priority = 'HIGH_PUBLIC_NEEDS_STRUCTURE';
    } elseif ($p->post_status === 'publish') {
        $priority = 'PUBLIC_REVIEW';
    } elseif ($p->post_status === 'private') {
        $priority = 'PRIVATE_REVIEW';
    } else {
        $priority = 'DRAFT_REVIEW';
    }

    $rows[] = [
        'post_id' => $post_id,
        'title' => $title,
        'slug' => $slug,
        'status' => $p->post_status,
        'url' => get_permalink($post_id),
        'recipe_id' => is_string($recipe_id) ? $recipe_id : '',
        'hr_score' => $score,
        'type_guess' => $type,
        'priority' => $priority,
        'needs_work_score' => $needs_work_score,
        'has_sections' => $has_sections ? 'yes' : 'no',
        'has_verified_process' => $has_process ? 'yes' : 'no',
        'has_full_markdown' => $has_full_md ? 'yes' : 'no',
        'has_image' => $has_image ? 'yes' : 'no',
        'source_status' => is_string($source_status) ? $source_status : '',
        'country_hits' => implode('; ', array_slice(array_unique($country_hits), 0, 12)),
        'region_hits' => implode('; ', array_slice(array_unique($region_hits), 0, 12)),
        'product_hits' => implode('; ', array_slice(array_unique($product_hits), 0, 12)),
        'dossier_hints' => implode('; ', $dossier_hints),
    ];
}

usort($rows, function($a, $b) {
    $priority_rank = [
        'HIGH_PUBLIC_NEEDS_STRUCTURE' => 1,
        'PUBLIC_REVIEW' => 2,
        'PRIVATE_REVIEW' => 3,
        'DRAFT_REVIEW' => 4,
        'REFERENCE_OR_ALREADY_STRUCTURED' => 9,
    ];

    $ra = $priority_rank[$a['priority']] ?? 99;
    $rb = $priority_rank[$b['priority']] ?? 99;

    if ($ra !== $rb) return $ra <=> $rb;
    if ((int)$a['needs_work_score'] !== (int)$b['needs_work_score']) return (int)$b['needs_work_score'] <=> (int)$a['needs_work_score'];
    if ((int)$a['hr_score'] !== (int)$b['hr_score']) return (int)$b['hr_score'] <=> (int)$a['hr_score'];
    return (int)$a['post_id'] <=> (int)$b['post_id'];
});

$csv_path = rtrim($out_dir, '/') . '/hr_recipe_queue_v1.csv';
$json_path = rtrim($out_dir, '/') . '/hr_recipe_queue_v1.json';
$md_path = rtrim($out_dir, '/') . '/HR_RECIPE_QUEUE_REPORT_v1.md';
$top_path = rtrim($out_dir, '/') . '/HR_RECIPE_QUEUE_TOP30_v1.md';

$fields = [
    'post_id', 'title', 'slug', 'status', 'url', 'recipe_id', 'hr_score', 'type_guess',
    'priority', 'needs_work_score', 'has_sections', 'has_verified_process', 'has_full_markdown',
    'has_image', 'source_status', 'country_hits', 'region_hits', 'product_hits', 'dossier_hints'
];

$fp = fopen($csv_path, 'w');
fputcsv($fp, $fields);
foreach ($rows as $r) {
    fputcsv($fp, array_map(fn($f) => $r[$f] ?? '', $fields));
}
fclose($fp);

file_put_contents($json_path, wp_json_encode([
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_HR_QUEUE',
    'wordpress_write_allowed' => false,
    'public_update_allowed' => false,
    'total_dry_recipe_posts_seen' => count($posts),
    'excluded_preview_clones' => $excluded_preview,
    'hr_candidates_total' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$priority_counts = [];
$type_counts = [];
foreach ($rows as $r) {
    $priority_counts[$r['priority']] = ($priority_counts[$r['priority']] ?? 0) + 1;
    $type_counts[$r['type_guess']] = ($type_counts[$r['type_guess']] ?? 0) + 1;
}

$md = [];
$md[] = '# Croatian recipe queue v1';
$md[] = '';
$md[] = 'Status: **READ_ONLY_QUEUE_CREATED**';
$md[] = '';
$md[] = 'Ovaj izvještaj ne mijenja WordPress. Služi za odabir hrvatskih recepata nakon zatvaranja `1982 — Finocchiona Toscana`.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- WordPress write allowed: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Pregledani dry_recipe postovi: `' . count($posts) . '`';
$md[] = '- Isključeni privatni preview cloneovi: `' . $excluded_preview . '`';
$md[] = '- Hrvatski kandidati: `' . count($rows) . '`';
$md[] = '';
$md[] = '## Prioriteti';
$md[] = '';
$md[] = '| Prioritet | Broj |';
$md[] = '|---|---:|';
foreach ($priority_counts as $k => $v) {
    $md[] = '| ' . $k . ' | ' . $v . ' |';
}
$md[] = '';
$md[] = '## Tipovi recepata';
$md[] = '';
$md[] = '| Tip | Broj |';
$md[] = '|---|---:|';
foreach ($type_counts as $k => $v) {
    $md[] = '| ' . $k . ' | ' . $v . ' |';
}
$md[] = '';
$md[] = '## Prvih 30 kandidata';
$md[] = '';
$md[] = '| # | ID | Naziv | Status | Tip | Prioritet | Nedostaje | URL |';
$md[] = '|---:|---:|---|---|---|---|---:|---|';
$i = 1;
foreach (array_slice($rows, 0, 30) as $r) {
    $md[] = '| ' . $i . ' | ' . $r['post_id'] . ' | ' . str_replace('|', '/', $r['title']) . ' | ' . $r['status'] . ' | ' . $r['type_guess'] . ' | ' . $r['priority'] . ' | ' . $r['needs_work_score'] . ' | ' . $r['url'] . ' |';
    $i++;
}
$md[] = '';
$md[] = '## Sljedeći korak';
$md[] = '';
$md[] = 'Odabrati prvi hrvatski kandidat iz reda `HIGH_PUBLIC_NEEDS_STRUCTURE` ili `PUBLIC_REVIEW`, zatim primijeniti isti workflow: quick intake → source validation → recipe.yml → internal QA → preview payload → private clone → manual admin preview → closure.';
$md[] = '';
file_put_contents($md_path, implode("\n", $md));

$top = [];
$top[] = '# Croatian recipe queue TOP 30 v1';
$top[] = '';
$top[] = '| # | ID | Naziv | Status | Tip | Prioritet | Score | Nedostaje |';
$top[] = '|---:|---:|---|---|---|---|---:|---:|';
$i = 1;
foreach (array_slice($rows, 0, 30) as $r) {
    $top[] = '| ' . $i . ' | ' . $r['post_id'] . ' | ' . str_replace('|', '/', $r['title']) . ' | ' . $r['status'] . ' | ' . $r['type_guess'] . ' | ' . $r['priority'] . ' | ' . $r['hr_score'] . ' | ' . $r['needs_work_score'] . ' |';
    $i++;
}
$top[] = '';
file_put_contents($top_path, implode("\n", $top));

echo "=== HR QUEUE COMPLETE ===\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "TOTAL_DRY_RECIPE_POSTS_SEEN=" . count($posts) . "\n";
echo "EXCLUDED_PREVIEW_CLONES=" . $excluded_preview . "\n";
echo "HR_CANDIDATES_TOTAL=" . count($rows) . "\n";

foreach ($priority_counts as $k => $v) {
    echo "PRIORITY_" . $k . "=" . $v . "\n";
}

foreach ($type_counts as $k => $v) {
    echo "TYPE_" . $k . "=" . $v . "\n";
}

if (!empty($rows)) {
    $first = $rows[0];
    echo "FIRST_CANDIDATE_ID=" . $first['post_id'] . "\n";
    echo "FIRST_CANDIDATE_TITLE=" . $first['title'] . "\n";
    echo "FIRST_CANDIDATE_TYPE=" . $first['type_guess'] . "\n";
    echo "FIRST_CANDIDATE_PRIORITY=" . $first['priority'] . "\n";
}

echo "REPORT=" . $md_path . "\n";
echo "CSV=" . $csv_path . "\n";
echo "JSON=" . $json_path . "\n";
