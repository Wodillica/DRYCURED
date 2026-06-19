<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_HR_QUEUE_STRICT_OUT');
if (!$out_dir) {
    fwrite(STDERR, "FAIL: nedostaje DC_HR_QUEUE_STRICT_OUT.\n");
    exit(1);
}
if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc_l($s) {
    $s = (string)$s;
    return function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
}

function dc_has($text, $terms) {
    $t = dc_l($text);
    foreach ($terms as $term) {
        if ($term !== '' && strpos($t, dc_l($term)) !== false) return true;
    }
    return false;
}

function dc_hits($text, $terms) {
    $t = dc_l($text);
    $hits = [];
    foreach ($terms as $term) {
        if ($term !== '' && strpos($t, dc_l($term)) !== false) $hits[] = $term;
    }
    return array_values(array_unique($hits));
}

function dc_type_strict($title, $slug, $recipe_id, $content_hint) {
    $t = dc_l($title . ' ' . $slug . ' ' . $recipe_id);
    $wide = dc_l($title . ' ' . $slug . ' ' . $recipe_id . ' ' . $content_hint);

    $thermal_strong = [
        'barena', 'bareni', 'bareno', 'kuhana', 'kuhani', 'kuhano',
        'pečena', 'pečeni', 'pečeno', 'topli dim', 'toplim dimom',
        'krvavica', 'tlačenica', 'švargl', 'prezvuršt', 'čvarci', 'mast'
    ];

    $ground_title = [
        'kobasica', 'kobasice', 'kulen', 'kulenova seka', 'salama',
        'salame', 'češnjovka', 'češnjovke', 'sudžuk'
    ];

    $whole_title = [
        'pršut', 'šunka', 'panceta', 'slanina', 'sušeni vrat',
        'vrat', 'buđola', 'but', 'plećka', 'rebra', 'pečenica', 'filet'
    ];

    $fish_strong = [
        'riba', 'tuna', 'srdela', 'inćun', 'losos', 'bakalar',
        'oslić', 'morska riba', 'dimljena riba'
    ];

    if (dc_has($t, $fish_strong)) return 'FISH_OR_SEAFOOD';

    if (dc_has($t, $ground_title)) {
        if (dc_has($t, $thermal_strong)) return 'THERMAL_PROCESSED';
        return 'GROUND_MEAT_OR_CASING';
    }

    if (dc_has($t, $whole_title)) {
        if (dc_has($t, $thermal_strong)) return 'THERMAL_PROCESSED';
        return 'WHOLE_CUT';
    }

    if (dc_has($t, $thermal_strong)) return 'THERMAL_PROCESSED';

    if (dc_has($wide, ['mljeven', 'rešetka', 'crijeva', 'punjenje'])) return 'GROUND_MEAT_OR_CASING';
    if (dc_has($wide, ['suho soljenje', 'salamura', 'pac', 'komad mesa'])) return 'WHOLE_CUT';

    return 'NEEDS_CLASSIFICATION';
}

function dc_meta_hint($post_id) {
    $keys = ['_dry_recipe_id', '_dry_recipe_full_markdown', '_dry_recipe_type_router', '_dry_recipe_source_validation_status'];
    $parts = [];
    foreach ($keys as $k) {
        $v = get_post_meta($post_id, $k, true);
        if (is_string($v) && $v !== '') $parts[] = mb_substr($v, 0, 1200);
    }
    return implode(' ', $parts);
}

$strong_hr_regions = [
    'slavonsk', 'baranjsk', 'srijem', 'srijemsk', 'sremsk',
    'dalmatinsk', 'istarsk', 'ličk', 'lika', 'vrgoračk',
    'sinjsk', 'korčulansk', 'hvarsk', 'rovinjsk', 'pazinsk',
    'senjsk', 'crikveničk', 'krčk', 'drnišk', 'vinkovačk',
    'miholjačk', 'baranjska', 'samoborsk', 'zagorsk', 'međimursk',
    'podravsk', 'posavsk', 'moslavačk', 'turopoljsk'
];

$strong_hr_products = [
    'slavonski kulen', 'slavonska kobasica', 'kulenova seka',
    'vrgorački kulen', 'dalmatinski pršut', 'istarski pršut',
    'drniški pršut', 'krčki pršut', 'istarska kobasica',
    'rovinjska kobasica', 'pazinska kobasica', 'lička kobasica',
    'sinjska kobasica', 'dalmatinska kobasica', 'hvarska prstena',
    'češnjovka', 'češnjovke'
];

$foreign_exclude_title = [
    'balmoș', 'balmos', 'pfälzer', 'pfalzer', 'tolminska',
    'gorenjska', 'savinjski', 'savinjska', 'crnogorska',
    'durmitorska', 'paio alentejano', 'alentejanska', 'ossenworst',
    'domašnja kovbasa', 'ковбаса', 'wiltshire', 'beer-cured',
    'spekeskinke', 'norska', 'norška', 'pancetta arrotolata',
    'nduja', 'finocchiona', 'salame di felino', 'jesus de lyon',
    'suxhuk', 'babić kobasica', 'babic', 'kranjska'
];

$noise_exclude = [
    'etnografska studija', 'studija', 'enciklopedija', 'neptunov dar',
    'starohrvatskimore', 'more-kopno', 'salama maritima'
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
$excluded_foreign = 0;
$excluded_noise = 0;
$excluded_weak = 0;

foreach ($posts as $p) {
    $post_id = (int)$p->ID;
    $title = (string)$p->post_title;
    $slug = (string)$p->post_name;
    $recipe_id = get_post_meta($post_id, '_dry_recipe_id', true);
    $recipe_id = is_string($recipe_id) ? $recipe_id : '';

    $preview_mode = get_post_meta($post_id, '_dry_recipe_preview_mode', true);
    $preview_source = get_post_meta($post_id, '_dry_recipe_preview_source_post_id', true);
    if ($preview_mode === 'PRIVATE_CLONE_ONLY' || $preview_source !== '' || dc_has($title, ['preview —', 'preview -', 'privatno:', 'private preview'])) {
        $excluded_preview++;
        continue;
    }

    $id_title_slug = $recipe_id . ' ' . $title . ' ' . $slug;
    if (dc_has($id_title_slug, $foreign_exclude_title)) {
        $excluded_foreign++;
        continue;
    }
    if (dc_has($id_title_slug, $noise_exclude)) {
        $excluded_noise++;
        continue;
    }

    $has_hr_id = strpos($recipe_id, 'HR-') === 0;
    $region_hits = dc_hits($id_title_slug, $strong_hr_regions);
    $product_hits = dc_hits($id_title_slug, $strong_hr_products);

    $score = 0;
    if ($has_hr_id) $score += 20;
    $score += count($region_hits) * 8;
    $score += count($product_hits) * 10;

    $is_hr_strict = $has_hr_id || $score >= 8;

    if (!$is_hr_strict) {
        $excluded_weak++;
        continue;
    }

    $content_hint = dc_meta_hint($post_id) . ' ' . mb_substr((string)$p->post_content, 0, 2000);
    $type = dc_type_strict($title, $slug, $recipe_id, $content_hint);

    $sections = get_post_meta($post_id, '_dry_recipe_sections', true);
    $process = get_post_meta($post_id, '_dry_verified_process', true);
    $full_md = get_post_meta($post_id, '_dry_recipe_full_markdown', true);
    $image = get_post_meta($post_id, '_dry_recipe_image_url', true);
    $source_status = get_post_meta($post_id, '_dry_recipe_source_validation_status', true);

    $has_sections = is_string($sections) && strlen($sections) > 200;
    $has_process = is_string($process) && strlen($process) > 200;
    $has_full_md = is_string($full_md) && strlen($full_md) > 500;
    $has_image = is_string($image) && trim($image) !== '';
    $has_source = is_string($source_status) && trim($source_status) !== '';

    $needs = 0;
    if (!$has_sections) $needs += 3;
    if (!$has_process) $needs += 3;
    if (!$has_full_md) $needs += 2;
    if (!$has_image) $needs += 1;
    if (!$has_source) $needs += 2;

    $reference_like = false;
    $ltitle = dc_l($title);
    if (
        (strpos($ltitle, 'slavonska domaća kobasica') !== false ||
         strpos($ltitle, 'slavonska kobasica') !== false ||
         strpos($ltitle, 'slavonski kulen') !== false)
        && $has_sections && $has_process
    ) {
        $reference_like = true;
    }

    if ($reference_like) {
        $priority = 'REFERENCE_OR_ALREADY_STRUCTURED';
    } elseif ($p->post_status === 'publish' && $needs >= 5) {
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
        'recipe_id' => $recipe_id,
        'strict_hr_score' => $score,
        'type_guess' => $type,
        'priority' => $priority,
        'needs_work_score' => $needs,
        'has_sections' => $has_sections ? 'yes' : 'no',
        'has_verified_process' => $has_process ? 'yes' : 'no',
        'has_full_markdown' => $has_full_md ? 'yes' : 'no',
        'has_image' => $has_image ? 'yes' : 'no',
        'source_status' => is_string($source_status) ? $source_status : '',
        'region_hits' => implode('; ', $region_hits),
        'product_hits' => implode('; ', $product_hits),
    ];
}

usort($rows, function($a, $b) {
    $rank = [
        'HIGH_PUBLIC_NEEDS_STRUCTURE' => 1,
        'PUBLIC_REVIEW' => 2,
        'PRIVATE_REVIEW' => 3,
        'DRAFT_REVIEW' => 4,
        'REFERENCE_OR_ALREADY_STRUCTURED' => 9,
    ];
    $ra = $rank[$a['priority']] ?? 99;
    $rb = $rank[$b['priority']] ?? 99;
    if ($ra !== $rb) return $ra <=> $rb;

    $type_rank = [
        'GROUND_MEAT_OR_CASING' => 1,
        'WHOLE_CUT' => 2,
        'THERMAL_PROCESSED' => 3,
        'FISH_OR_SEAFOOD' => 4,
        'NEEDS_CLASSIFICATION' => 5,
    ];
    $ta = $type_rank[$a['type_guess']] ?? 99;
    $tb = $type_rank[$b['type_guess']] ?? 99;
    if ($ta !== $tb) return $ta <=> $tb;

    if ((int)$a['strict_hr_score'] !== (int)$b['strict_hr_score']) return (int)$b['strict_hr_score'] <=> (int)$a['strict_hr_score'];
    if ((int)$a['needs_work_score'] !== (int)$b['needs_work_score']) return (int)$b['needs_work_score'] <=> (int)$a['needs_work_score'];
    return (int)$a['post_id'] <=> (int)$b['post_id'];
});

$fields = [
    'post_id','title','slug','status','url','recipe_id','strict_hr_score',
    'type_guess','priority','needs_work_score','has_sections','has_verified_process',
    'has_full_markdown','has_image','source_status','region_hits','product_hits'
];

$csv_path = rtrim($out_dir, '/') . '/hr_recipe_queue_strict_v1_1.csv';
$json_path = rtrim($out_dir, '/') . '/hr_recipe_queue_strict_v1_1.json';
$report_path = rtrim($out_dir, '/') . '/HR_RECIPE_QUEUE_STRICT_REPORT_v1_1.md';
$top_path = rtrim($out_dir, '/') . '/HR_RECIPE_QUEUE_STRICT_TOP30_v1_1.md';

$fp = fopen($csv_path, 'w');
fputcsv($fp, $fields);
foreach ($rows as $r) fputcsv($fp, array_map(fn($f) => $r[$f] ?? '', $fields));
fclose($fp);

$priority_counts = [];
$type_counts = [];
foreach ($rows as $r) {
    $priority_counts[$r['priority']] = ($priority_counts[$r['priority']] ?? 0) + 1;
    $type_counts[$r['type_guess']] = ($type_counts[$r['type_guess']] ?? 0) + 1;
}

file_put_contents($json_path, wp_json_encode([
    'generated_at' => gmdate('c'),
    'mode' => 'READ_ONLY_HR_QUEUE_STRICT_V1_1',
    'wordpress_write_allowed' => false,
    'public_update_allowed' => false,
    'total_dry_recipe_posts_seen' => count($posts),
    'excluded_preview_clones' => $excluded_preview,
    'excluded_foreign_by_title_slug' => $excluded_foreign,
    'excluded_noise_by_title_slug' => $excluded_noise,
    'excluded_weak_hr_signal' => $excluded_weak,
    'hr_candidates_total' => count($rows),
    'priority_counts' => $priority_counts,
    'type_counts' => $type_counts,
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$md = [];
$md[] = '# Croatian recipe queue STRICT v1.1';
$md[] = '';
$md[] = 'Status: **READ_ONLY_STRICT_QUEUE_CREATED**';
$md[] = '';
$md[] = 'Ovaj izvještaj ispravlja preširoki HR queue v1. Ne mijenja WordPress.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- WordPress write allowed: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Pregledani dry_recipe postovi: `' . count($posts) . '`';
$md[] = '- Isključeni privatni preview cloneovi: `' . $excluded_preview . '`';
$md[] = '- Isključeni strani recepti po title/slug: `' . $excluded_foreign . '`';
$md[] = '- Isključeni noise zapisi po title/slug: `' . $excluded_noise . '`';
$md[] = '- Isključeni zbog slabog HR signala: `' . $excluded_weak . '`';
$md[] = '- Strogi hrvatski kandidati: `' . count($rows) . '`';
$md[] = '';
$md[] = '## Prioriteti';
$md[] = '';
$md[] = '| Prioritet | Broj |';
$md[] = '|---|---:|';
foreach ($priority_counts as $k => $v) $md[] = '| ' . $k . ' | ' . $v . ' |';
$md[] = '';
$md[] = '## Tipovi';
$md[] = '';
$md[] = '| Tip | Broj |';
$md[] = '|---|---:|';
foreach ($type_counts as $k => $v) $md[] = '| ' . $k . ' | ' . $v . ' |';
$md[] = '';
$md[] = '## TOP 30';
$md[] = '';
$md[] = '| # | ID | Naziv | Status | Tip | Prioritet | HR score | Nedostaje | URL |';
$md[] = '|---:|---:|---|---|---|---|---:|---:|---|';
$i = 1;
foreach (array_slice($rows, 0, 30) as $r) {
    $md[] = '| ' . $i . ' | ' . $r['post_id'] . ' | ' . str_replace('|','/',$r['title']) . ' | ' . $r['status'] . ' | ' . $r['type_guess'] . ' | ' . $r['priority'] . ' | ' . $r['strict_hr_score'] . ' | ' . $r['needs_work_score'] . ' | ' . $r['url'] . ' |';
    $i++;
}
$md[] = '';
$md[] = '## Sljedeći korak';
$md[] = '';
$md[] = 'Odabrati prvi stvarni hrvatski recept iz TOP liste. Ako je prvi kandidat očito stvaran, pokrenuti quick intake po skraćenom workflowu.';
$md[] = '';
file_put_contents($report_path, implode("\n", $md));

$top = [];
$top[] = '# Croatian recipe queue STRICT TOP 30 v1.1';
$top[] = '';
$top[] = '| # | ID | Naziv | Status | Tip | Prioritet | HR score | Nedostaje |';
$top[] = '|---:|---:|---|---|---|---|---:|---:|';
$i = 1;
foreach (array_slice($rows, 0, 30) as $r) {
    $top[] = '| ' . $i . ' | ' . $r['post_id'] . ' | ' . str_replace('|','/',$r['title']) . ' | ' . $r['status'] . ' | ' . $r['type_guess'] . ' | ' . $r['priority'] . ' | ' . $r['strict_hr_score'] . ' | ' . $r['needs_work_score'] . ' |';
    $i++;
}
$top[] = '';
file_put_contents($top_path, implode("\n", $top));

echo "=== HR STRICT QUEUE COMPLETE ===\n";
echo "WORDPRESS_WRITE_ALLOWED=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "TOTAL_DRY_RECIPE_POSTS_SEEN=" . count($posts) . "\n";
echo "EXCLUDED_PREVIEW_CLONES=" . $excluded_preview . "\n";
echo "EXCLUDED_FOREIGN_BY_TITLE_SLUG=" . $excluded_foreign . "\n";
echo "EXCLUDED_NOISE_BY_TITLE_SLUG=" . $excluded_noise . "\n";
echo "EXCLUDED_WEAK_HR_SIGNAL=" . $excluded_weak . "\n";
echo "HR_STRICT_CANDIDATES_TOTAL=" . count($rows) . "\n";
foreach ($priority_counts as $k => $v) echo "PRIORITY_" . $k . "=" . $v . "\n";
foreach ($type_counts as $k => $v) echo "TYPE_" . $k . "=" . $v . "\n";
if (!empty($rows)) {
    $f = $rows[0];
    echo "FIRST_CANDIDATE_ID=" . $f['post_id'] . "\n";
    echo "FIRST_CANDIDATE_TITLE=" . $f['title'] . "\n";
    echo "FIRST_CANDIDATE_TYPE=" . $f['type_guess'] . "\n";
    echo "FIRST_CANDIDATE_PRIORITY=" . $f['priority'] . "\n";
}
echo "REPORT=" . $report_path . "\n";
echo "CSV=" . $csv_path . "\n";
echo "JSON=" . $json_path . "\n";
