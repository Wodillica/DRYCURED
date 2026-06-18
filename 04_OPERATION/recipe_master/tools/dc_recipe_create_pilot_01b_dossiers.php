<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_DOSSIER_OUT');
$ids_raw = getenv('DC_DOSSIER_IDS');

if (!$out_dir || !$ids_raw) {
    fwrite(STDERR, "FAIL: DC_DOSSIER_OUT ili DC_DOSSIER_IDS nije postavljen.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc_dossier_slug($text) {
    $text = html_entity_decode((string)$text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $map = [
        'š'=>'s','Š'=>'S','č'=>'c','Č'=>'C','ć'=>'c','Ć'=>'C','ž'=>'z','Ž'=>'Z','đ'=>'d','Đ'=>'D',
        'ä'=>'a','ö'=>'o','ü'=>'u','ß'=>'ss','á'=>'a','à'=>'a','â'=>'a','é'=>'e','è'=>'e','ê'=>'e',
        'í'=>'i','ì'=>'i','ó'=>'o','ò'=>'o','ú'=>'u','ù'=>'u','ñ'=>'n','ø'=>'o','æ'=>'ae','œ'=>'oe'
    ];
    $text = strtr($text, $map);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'recipe';
}

function dc_dossier_yaml_string($s) {
    $s = (string)$s;
    $s = str_replace(["\r\n", "\r"], "\n", $s);
    $s = str_replace('"', '\"', $s);
    return '"' . $s . '"';
}

function dc_dossier_redact_meta($key, $value) {
    $k = strtolower((string)$key);
    if (preg_match('/token|secret|password|passwd|cookie|nonce|auth|api[_-]?key|private[_-]?key|salt/i', $k)) {
        return '[REDACTED_META_VALUE]';
    }
    return $value;
}

function dc_dossier_public_text($url) {
    if (!$url) {
        return [
            'status' => '',
            'error' => 'empty_url',
            'text' => '',
        ];
    }

    $response = wp_remote_get($url, [
        'timeout' => 5,
        'redirection' => 2,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'DrycuredDossierScaffold/1.0 read-only',
        ],
    ]);

    if (is_wp_error($response)) {
        return [
            'status' => '',
            'error' => $response->get_error_message(),
            'text' => '',
        ];
    }

    $status = (string) wp_remote_retrieve_response_code($response);
    $html = (string) wp_remote_retrieve_body($response);
    $text = wp_strip_all_tags($html);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = preg_replace('/\s+/u', ' ', $text);
    $text = trim($text);

    if (function_exists('mb_substr')) {
        $text = mb_substr($text, 0, 20000, 'UTF-8');
    } else {
        $text = substr($text, 0, 20000);
    }

    return [
        'status' => $status,
        'error' => '',
        'text' => $text,
    ];
}

$ids = array_filter(array_map('trim', explode(',', $ids_raw)));
$created = [];

foreach ($ids as $id_raw) {
    $post_id = (int)$id_raw;
    $post = get_post($post_id);

    if (!$post) {
        fwrite(STDERR, "FAIL: post ne postoji: $post_id\n");
        exit(1);
    }

    $title = get_the_title($post_id);
    $slug = dc_dossier_slug($title);
    $recipe_dir = rtrim($out_dir, '/') . '/' . $post_id . '_' . $slug;

    if (!is_dir($recipe_dir)) {
        mkdir($recipe_dir, 0775, true);
    }

    $url = get_permalink($post_id);
    $status = get_post_status($post_id);
    $modified = get_post_modified_time('c', true, $post_id);
    $meta = get_post_meta($post_id);

    $safe_meta = [];
    foreach ($meta as $key => $values) {
        $safe_meta[$key] = [];
        foreach ((array)$values as $v) {
            if (is_scalar($v)) {
                $safe_meta[$key][] = dc_dossier_redact_meta($key, (string)$v);
            } else {
                $safe_meta[$key][] = dc_dossier_redact_meta($key, wp_json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            }
        }
    }

    $public = dc_dossier_public_text($url);

    $snapshot = [
        'post_id' => $post_id,
        'title' => $title,
        'post_name' => $post->post_name,
        'url' => $url,
        'status' => $status,
        'post_type' => $post->post_type,
        'modified_gmt' => $modified,
        'post_excerpt' => $post->post_excerpt,
        'post_content' => $post->post_content,
        'meta_redacted' => $safe_meta,
        'public_fetch_status' => $public['status'],
        'public_fetch_error' => $public['error'],
    ];

    file_put_contents(
        $recipe_dir . '/raw_wp_snapshot.json',
        wp_json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );

    file_put_contents($recipe_dir . '/public_text_snapshot.txt', $public['text']);

    $sources = [];
    $sources[] = '# sources.yml';
    $sources[] = 'dossier_status: "SCAFFOLD_ONLY"';
    $sources[] = 'public_update_allowed: false';
    $sources[] = 'post_id: ' . $post_id;
    $sources[] = 'title: ' . dc_dossier_yaml_string($title);
    $sources[] = 'url: ' . dc_dossier_yaml_string($url);
    $sources[] = 'wordpress_status: ' . dc_dossier_yaml_string($status);
    $sources[] = 'recipe_type: "GROUND_MEAT_OR_CASING"';
    $sources[] = 'source_validation_status: "NEEDS_SOURCE_VALIDATION"';
    $sources[] = 'notes:';
    $sources[] = '  - "Ovaj dosje je otvoren iz strict pilot batcha 01B."';
    $sources[] = '  - "Javni update nije dopušten dok recipe.yml i qa_report.md nisu završeni."';
    $sources[] = '  - "Potrebno je potvrditi izvor: službena objava, knjiga, stručni izvor ili drugi digitalni/tiskani trag."';
    $sources[] = 'wp_snapshot: "raw_wp_snapshot.json"';
    $sources[] = 'public_text_snapshot: "public_text_snapshot.txt"';
    $sources[] = 'source_candidates: []';
    file_put_contents($recipe_dir . '/sources.yml', implode("\n", $sources) . "\n");

    $recipe = [];
    $recipe[] = '# recipe.yml';
    $recipe[] = 'dossier_status: "SCAFFOLD_ONLY"';
    $recipe[] = 'public_update_allowed: false';
    $recipe[] = 'post_id: ' . $post_id;
    $recipe[] = 'title: ' . dc_dossier_yaml_string($title);
    $recipe[] = 'url: ' . dc_dossier_yaml_string($url);
    $recipe[] = 'recipe_type: "GROUND_MEAT_OR_CASING"';
    $recipe[] = 'reference_model: "HR-SL-005 Slavonska domaća kobasica — samo dizajnerski/sadržajni model za mljevene proizvode u omotaču"';
    $recipe[] = 'batch_size_kg: 10';
    $recipe[] = 'language: "hr"';
    $recipe[] = 'canonical_recipe_ready: false';
    $recipe[] = 'fields:';
    $recipe[] = '  product_identity: null';
    $recipe[] = '  region_country: null';
    $recipe[] = '  eu_status: null';
    $recipe[] = '  product_type: "mljeveno/usitnjeno meso u omotaču"';
    $recipe[] = '  raw_materials_kg: []';
    $recipe[] = '  spices_g: []';
    $recipe[] = '  liquids_l: []';
    $recipe[] = '  garlic_mode: null';
    $recipe[] = '  garlic_liquid_details:';
    $recipe[] = '    garlic_amount_g: null';
    $recipe[] = '    liquid_type: null';
    $recipe[] = '    liquid_amount_l: null';
    $recipe[] = '    steeping_time: null';
    $recipe[] = '    boiled_or_cold: null';
    $recipe[] = '    strained_amount_added_l: null';
    $recipe[] = '  grinding:';
    $recipe[] = '    meat_plate_mm: null';
    $recipe[] = '    fat_cut_mm: null';
    $recipe[] = '    fat_handling: null';
    $recipe[] = '    temperature_control: null';
    $recipe[] = '  casing:';
    $recipe[] = '    type: null';
    $recipe[] = '    diameter_mm: null';
    $recipe[] = '    soaking_liquid: null';
    $recipe[] = '    soaking_time: null';
    $recipe[] = '    boiled_or_cold_liquid: null';
    $recipe[] = '  process:';
    $recipe[] = '    mixing: null';
    $recipe[] = '    stuffing: null';
    $recipe[] = '    fermentation: null';
    $recipe[] = '    smoking: null';
    $recipe[] = '    drying: null';
    $recipe[] = '    aging: null';
    $recipe[] = '  nitrite_salt:';
    $recipe[] = '    used: null';
    $recipe[] = '    safety_note_required: null';
    $recipe[] = '  sensory_profile: null';
    $recipe[] = '  common_errors_and_solutions: []';
    $recipe[] = '  done_when: []';
    file_put_contents($recipe_dir . '/recipe.yml', implode("\n", $recipe) . "\n");

    $qa = [];
    $qa[] = '# qa_report.md';
    $qa[] = '';
    $qa[] = 'Status: **BLOCKED — DOSSIER_SCAFFOLD_ONLY**';
    $qa[] = '';
    $qa[] = 'Recept: **' . $title . '**';
    $qa[] = '';
    $qa[] = 'Post ID: `' . $post_id . '`';
    $qa[] = '';
    $qa[] = 'URL: `' . $url . '`';
    $qa[] = '';
    $qa[] = '## QA-gate prije bilo kakvog javnog ažuriranja';
    $qa[] = '';
    $qa[] = '- [ ] Izvor recepta potvrđen.';
    $qa[] = '- [ ] Svi javni tekstovi su na hrvatskom.';
    $qa[] = '- [ ] Recept je standardiziran na 10 kg mesa.';
    $qa[] = '- [ ] Sirovine su navedene u kg.';
    $qa[] = '- [ ] Začini su navedeni u g.';
    $qa[] = '- [ ] Tekućine su navedene u L.';
    $qa[] = '- [ ] Granulacija mesa ima rešetku u mm.';
    $qa[] = '- [ ] Obrada slanine/masnoće ima rezanje u mm ili jasan opis.';
    $qa[] = '- [ ] Crijeva/omotač imaju tip, promjer i namakanje.';
    $qa[] = '- [ ] Češnjak je jasno označen: direktno ili procijeđena tekućina.';
    $qa[] = '- [ ] Ako postoji tekućina od češnjaka, navedeni su količina češnjaka, tekućina, vrijeme, hladno/prokuhano i količina dodana u smjesu.';
    $qa[] = '- [ ] Dimljenje, sušenje i zrenje imaju trajanje i parametre gdje su dostupni.';
    $qa[] = '- [ ] Nitritna sol ima sigurnosnu napomenu ako se koristi.';
    $qa[] = '- [ ] Svaki problem ima konkretno rješenje.';
    $qa[] = '- [ ] Nema javnih internih oznaka: preview, fallback, source-lock, audit, adapter, debug.';
    $qa[] = '- [ ] Ne mijenja se javni URL.';
    $qa[] = '- [ ] Renderer se ne mijenja.';
    $qa[] = '';
    $qa[] = '## Zaključak';
    $qa[] = '';
    $qa[] = 'Javni update nije dopušten. Dosje je tek otvoren i treba ručnu/kanonsku obradu.';
    file_put_contents($recipe_dir . '/qa_report.md', implode("\n", $qa) . "\n");

    $log = [];
    $log[] = '# wordpress_import_log.md';
    $log[] = '';
    $log[] = 'Status: **NO_IMPORT_PERFORMED**';
    $log[] = '';
    $log[] = '- Post ID: `' . $post_id . '`';
    $log[] = '- URL: `' . $url . '`';
    $log[] = '- Ovaj korak nije mijenjao WordPress.';
    $log[] = '- Nije mijenjan title.';
    $log[] = '- Nije mijenjan slug.';
    $log[] = '- Nije mijenjan status.';
    $log[] = '- Nije mijenjan renderer.';
    $log[] = '- Nije rađen javni update.';
    $log[] = '';
    $log[] = 'Sljedeći dopušteni korak: ručno/kanonsko popunjavanje `recipe.yml` i QA provjera.';
    file_put_contents($recipe_dir . '/wordpress_import_log.md', implode("\n", $log) . "\n");

    $readme = [];
    $readme[] = '# ' . $title;
    $readme[] = '';
    $readme[] = '- Post ID: `' . $post_id . '`';
    $readme[] = '- URL: `' . $url . '`';
    $readme[] = '- Tip: `GROUND_MEAT_OR_CASING`';
    $readme[] = '- Status dosjea: `SCAFFOLD_ONLY`';
    $readme[] = '- Javni update: `false`';
    $readme[] = '';
    $readme[] = '## Datoteke';
    $readme[] = '';
    $readme[] = '- `sources.yml` — izvorni tragovi i status validacije.';
    $readme[] = '- `recipe.yml` — kanonski podatkovni kostur recepta.';
    $readme[] = '- `qa_report.md` — QA-gate prije javnog ažuriranja.';
    $readme[] = '- `wordpress_import_log.md` — potvrda da WordPress nije mijenjan.';
    $readme[] = '- `raw_wp_snapshot.json` — read-only WordPress snapshot.';
    $readme[] = '- `public_text_snapshot.txt` — javni tekstualni snapshot za pregled.';
    file_put_contents($recipe_dir . '/README.md', implode("\n", $readme) . "\n");

    $created[] = [
        'post_id' => $post_id,
        'title' => $title,
        'url' => $url,
        'dir' => $recipe_dir,
        'public_fetch_status' => $public['status'],
        'public_fetch_error' => $public['error'],
    ];
}

$index_md = [];
$index_md[] = '# PILOT 01B — individual recipe dossiers';
$index_md[] = '';
$index_md[] = 'Status: **SCAFFOLD_ONLY**';
$index_md[] = '';
$index_md[] = 'Ovaj korak nije mijenjao WordPress. Otvoreni su pojedinačni dosjei za strict ground pilot kandidate.';
$index_md[] = '';
$index_md[] = '| Post ID | Recept | URL | Dosje | HTTP status |';
$index_md[] = '|---:|---|---|---|---|';
foreach ($created as $c) {
    $index_md[] = '| ' . $c['post_id'] . ' | ' . str_replace('|', '/', $c['title']) . ' | ' . $c['url'] . ' | `' . $c['dir'] . '` | ' . $c['public_fetch_status'] . ' |';
}
$index_md[] = '';
$index_md[] = '## Sljedeći korak';
$index_md[] = '';
$index_md[] = 'Popuniti jedan dosje do kraja, počevši od `sources.yml`, zatim `recipe.yml`, pa `qa_report.md`. Tek nakon toga planirati kontrolirani WordPress update za jedan recept.';
file_put_contents(rtrim($out_dir, '/') . '/DOSSIERS_INDEX.md', implode("\n", $index_md) . "\n");

file_put_contents(
    rtrim($out_dir, '/') . '/dossiers_created.json',
    wp_json_encode($created, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

echo "=== DOSSIERS CREATED ===\n";
echo "COUNT=" . count($created) . "\n";
foreach ($created as $c) {
    echo "POST_ID=" . $c['post_id'] . " TITLE=" . $c['title'] . " HTTP=" . $c['public_fetch_status'] . "\n";
}
echo "INDEX=" . rtrim($out_dir, '/') . "/DOSSIERS_INDEX.md\n";
