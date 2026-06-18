<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_META_PLAN_OUT');
$qa_path = getenv('DC_META_PLAN_QA');
$deep_json_path = getenv('DC_META_PLAN_DEEP_JSON');

if (!$out_dir || !$qa_path || !$deep_json_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dcmp_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dcmp_json_read($path) {
    if (!is_readable($path)) {
        dcmp_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dcmp_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dcmp_meta_value($post_id, $key) {
    $v = get_post_meta($post_id, $key, true);
    return is_string($v) ? $v : '';
}

function dcmp_post_basic($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return null;
    }
    return [
        'ID' => $p->ID,
        'title' => $p->post_title,
        'name' => $p->post_name,
        'status' => $p->post_status,
        'type' => $p->post_type,
        'permalink' => get_permalink($post_id),
    ];
}

$source_id = 3042;
$clone_id = 3535;
$reference_id = 2976;

$source = get_post($source_id);
$clone = get_post($clone_id);
$reference = get_post($reference_id);

if (!$source || !$clone || !$reference) {
    dcmp_fail("nedostaje jedan od postova 2976/3042/3535.");
}

if ($source->post_status !== 'publish') {
    dcmp_fail("source 3042 nije publish.");
}
if ($clone->post_status !== 'private') {
    dcmp_fail("clone 3535 nije private.");
}
if ($clone->post_type !== 'dry_recipe' || $source->post_type !== 'dry_recipe') {
    dcmp_fail("source ili clone nije dry_recipe.");
}

$deep = dcmp_json_read($deep_json_path);

$keys_to_compare = [
    '_dry_recipe_id',
    'dry_recipe_id',
    '_recipe_id',
    '_dry_recipe_image_url',
    '_dry_recipe_full_markdown',
    '_dry_recipe_sections',
    '_dry_verified_process',
    '_dry_recipe_public_update_allowed',
    '_dry_recipe_public_verified',
    '_dry_recipe_preview_source_post_id',
    '_dry_recipe_preview_mode',
    '_dry_recipe_source_validation_status',
    '_dry_recipe_type_router',
];

$meta = [];
foreach ([$reference_id, $source_id, $clone_id] as $id) {
    $meta[$id] = [];
    foreach ($keys_to_compare as $key) {
        $v = dcmp_meta_value($id, $key);
        $meta[$id][$key] = [
            'exists' => $v !== '',
            'length' => strlen($v),
            'value' => $v,
            'preview' => mb_substr($v, 0, 360),
            'json_valid' => $v !== '' && json_decode($v, true) !== null,
        ];
    }
}

$source_code = dcmp_meta_value($source_id, '_dry_recipe_id');
$source_code_alt = dcmp_meta_value($source_id, 'dry_recipe_id');
$source_code_alt2 = dcmp_meta_value($source_id, '_recipe_id');

$chosen_source_code = $source_code ?: ($source_code_alt ?: $source_code_alt2);
if ($chosen_source_code === '') {
    $chosen_source_code = 'PREVIEW-3042-JESUS-DE-LYON';
}

$source_image = dcmp_meta_value($source_id, '_dry_recipe_image_url');
$reference_image = dcmp_meta_value($reference_id, '_dry_recipe_image_url');

$clone_has_code = dcmp_meta_value($clone_id, '_dry_recipe_id') !== '';
$clone_has_image = dcmp_meta_value($clone_id, '_dry_recipe_image_url') !== '';

$meta_patch_plan = [];
$meta_patch_plan['_dry_recipe_id'] = [
    'action' => $clone_has_code ? 'KEEP_EXISTING' : 'ADD_TO_PRIVATE_CLONE_ONLY',
    'recommended_value' => $chosen_source_code,
    'source' => $source_code !== '' ? 'COPY_FROM_SOURCE_3042__dry_recipe_id' : 'PREVIEW_FALLBACK_GENERATED',
    'reason' => 'Renderer i cleanup funkcije na više mjesta čitaju _dry_recipe_id. Bez tog ključa clone se ne može pouzdano ponašati kao recipe renderer kandidat.',
    'risk' => 'Ako se kopira isti kod kao javni source, ne smije se koristiti za javno mapiranje ni globalne query operacije. Primjena smije biti samo na private cloneu.',
];

if ($source_image !== '') {
    $image_value = $source_image;
    $image_source = 'COPY_FROM_SOURCE_3042__dry_recipe_image_url';
} elseif ($reference_image !== '') {
    $image_value = $reference_image;
    $image_source = 'TEMP_COPY_FROM_REFERENCE_2976_ONLY_IF_ACCEPTED';
} else {
    $image_value = '';
    $image_source = 'NO_IMAGE_AVAILABLE';
}

$meta_patch_plan['_dry_recipe_image_url'] = [
    'action' => $clone_has_image ? 'KEEP_EXISTING' : ($image_value !== '' ? 'ADD_TO_PRIVATE_CLONE_ONLY' : 'SKIP_NO_VALUE'),
    'recommended_value' => $image_value,
    'source' => $image_source,
    'reason' => 'Plugin referencira _dry_recipe_image_url za hero/sliku. Nije blocker za podatke, ali je važan za vizualni preview.',
    'risk' => 'Ne koristiti pogrešnu javnu sliku kao konačnu. Ako se koristi privremena slika, mora biti označena kao preview-only.',
];

$meta_patch_plan['_dry_recipe_public_update_allowed'] = [
    'action' => 'KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY',
    'recommended_value' => '0',
    'source' => 'SAFETY_GUARD',
    'reason' => 'Privatni clone nikada ne smije signalizirati javni update.',
    'risk' => 'Bez ovog guarda može doći do pogrešnog javnog update toka.',
];

$meta_patch_plan['_dry_recipe_public_verified'] = [
    'action' => 'KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY',
    'recommended_value' => '0',
    'source' => 'SAFETY_GUARD',
    'reason' => 'Recept nije public verified jer su aktivne blokade izvora, startera i dimljenja.',
    'risk' => 'Ako se označi verified, javni sustav može pogrešno tretirati recept kao završen.',
];

$meta_patch_plan['_dry_recipe_preview_mode'] = [
    'action' => 'KEEP_OR_ENFORCE_PRIVATE_CLONE_ONLY',
    'recommended_value' => 'PRIVATE_CLONE_ONLY',
    'source' => 'SAFETY_GUARD',
    'reason' => 'Clone mora ostati jasno označen kao privatni preview.',
    'risk' => 'Bez oznake može se zamijeniti s javnim zapisom.',
];

$checks = [];
function dcmp_check(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

dcmp_check($checks, 'source_publish', 'Source 3042 je publish', $source->post_status === 'publish', 'BLOCKER', 'Source ostaje javni referentni zapis, ali se ne smije mijenjati.');
dcmp_check($checks, 'clone_private', 'Clone 3535 je private', $clone->post_status === 'private', 'BLOCKER', 'Normalizer se smije planirati samo za privatni clone.');
dcmp_check($checks, 'clone_missing_id_confirmed', 'Clone 3535 nema _dry_recipe_id', !$clone_has_code, 'MAJOR', 'To potvrđuje razlog za meta-normalizer.');
dcmp_check($checks, 'source_code_available_or_fallback', 'Postoji source code ili fallback', $chosen_source_code !== '', 'MAJOR', 'Plan mora imati vrijednost za _dry_recipe_id.');
dcmp_check($checks, 'public_update_remains_false', 'Plan ne dopušta javni update', true, 'BLOCKER', 'Ovaj plan ne smije pisati u javni 3042.');
dcmp_check($checks, 'no_wp_write_now', 'Plan je read-only', true, 'BLOCKER', 'Ovaj alat ne smije upisivati meta podatke.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$plan_status = count($blocker_failures) === 0 ? 'PLAN_READY_PRIVATE_CLONE_ONLY' : 'PLAN_BLOCKED';

$plan = [
    'generated_at' => gmdate('c'),
    'plan_status' => $plan_status,
    'mode' => 'READ_ONLY_META_NORMALIZER_PLAN',
    'wordpress_write_allowed_now' => false,
    'public_update_allowed' => false,
    'source_post_write_allowed' => false,
    'allowed_future_target' => 'PRIVATE_CLONE_3535_ONLY',
    'forbidden_target' => 'PUBLIC_SOURCE_3042',
    'posts' => [
        'reference_2976' => dcmp_post_basic($reference_id),
        'source_3042' => dcmp_post_basic($source_id),
        'clone_3535' => dcmp_post_basic($clone_id),
    ],
    'meta_comparison' => $meta,
    'meta_patch_plan_for_future_step' => $meta_patch_plan,
    'deep_inspection_input' => [
        'path' => $deep_json_path,
        'post_3535_has_dry_recipe_id_from_deep' => false,
        'post_3535_has_image_url_from_deep' => false,
    ],
    'safety_rules' => [
        'Ne pisati u javni post 3042.',
        'Ne mijenjati title, slug, status ni URL javnog posta.',
        'Ne mijenjati renderer.',
        'Meta patch smije se primijeniti samo na post 3535 ako je post_status=private.',
        'Ako 3535 prestane biti private, patch mora stati.',
        'Ako _dry_recipe_public_update_allowed nije 0, patch mora stati.',
        'Ovaj plan nije javna verifikacija recepta.'
    ],
    'checks' => $checks,
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_meta_normalizer_plan_v1.json',
    wp_json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_meta_normalizer_patch_plan.csv', 'w');
fputcsv($csv, ['meta_key', 'action', 'recommended_value', 'source', 'reason', 'risk']);
foreach ($meta_patch_plan as $key => $row) {
    fputcsv($csv, [
        $key,
        $row['action'],
        $row['recommended_value'],
        $row['source'],
        $row['reason'],
        $row['risk'],
    ]);
}
fclose($csv);

$matrix = fopen(rtrim($out_dir, '/') . '/3535_meta_normalizer_meta_comparison.csv', 'w');
fputcsv($matrix, ['meta_key', '2976_exists', '2976_length', '3042_exists', '3042_length', '3535_exists', '3535_length']);
foreach ($keys_to_compare as $key) {
    fputcsv($matrix, [
        $key,
        $meta[$reference_id][$key]['exists'] ? 'YES' : 'NO',
        $meta[$reference_id][$key]['length'],
        $meta[$source_id][$key]['exists'] ? 'YES' : 'NO',
        $meta[$source_id][$key]['length'],
        $meta[$clone_id][$key]['exists'] ? 'YES' : 'NO',
        $meta[$clone_id][$key]['length'],
    ]);
}
fclose($matrix);

$md = [];
$md[] = '# 3535 meta-normalizer plan v1';
$md[] = '';
$md[] = 'Status: **' . $plan_status . '**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Planira minimalni meta-normalizer za privatni clone `3535`, bez promjene renderera i bez diranja javnog posta `3042`.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- WordPress write allowed now: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Future allowed target: `PRIVATE_CLONE_3535_ONLY`';
$md[] = '- Forbidden target: `PUBLIC_SOURCE_3042`';
$md[] = '- Clone 3535 has `_dry_recipe_id`: `' . ($clone_has_code ? 'true' : 'false') . '`';
$md[] = '- Clone 3535 has `_dry_recipe_image_url`: `' . ($clone_has_image ? 'true' : 'false') . '`';
$md[] = '';
$md[] = '## Planirani meta patch za budući korak';
$md[] = '';
$md[] = '| Meta key | Action | Source | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($meta_patch_plan as $key => $row) {
    $md[] = '| `' . $key . '` | `' . $row['action'] . '` | `' . $row['source'] . '` | ' . str_replace('|', '/', $row['reason']) . ' |';
}
$md[] = '';
$md[] = '## QA provjere plana';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Sigurnosna odluka';
$md[] = '';
$md[] = 'Sljedeći korak smije biti samo mali meta patch na privatnom cloneu `3535`, i to tek uz zaštitu: `post_status=private`, source `3042` read-only, public update `false`, renderer unchanged.';
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/3535_META_NORMALIZER_PLAN_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_3535_META_NORMALIZER_PLAN_V1 -->';
$append = $marker . "\n\n" .
"## 3535 meta-normalizer plan v1\n\n" .
"Status: **" . $plan_status . "**\n\n" .
"- WordPress write allowed now: `false`\n" .
"- Public update allowed: `false`\n" .
"- Future target: `PRIVATE_CLONE_3535_ONLY`\n" .
"- Forbidden target: `PUBLIC_SOURCE_3042`\n" .
"- Report: `review/" . basename($out_dir) . "/3535_META_NORMALIZER_PLAN_REPORT.md`\n" .
"- Plan JSON: `review/" . basename($out_dir) . "/3535_meta_normalizer_plan_v1.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 3535 META-NORMALIZER PLAN COMPLETE ===\n";
echo "PLAN_STATUS=" . $plan_status . "\n";
echo "WORDPRESS_WRITE_ALLOWED_NOW=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "FUTURE_TARGET=PRIVATE_CLONE_3535_ONLY\n";
echo "FORBIDDEN_TARGET=PUBLIC_SOURCE_3042\n";
echo "CLONE_3535_HAS_DRY_RECIPE_ID=" . ($clone_has_code ? 'true' : 'false') . "\n";
echo "CLONE_3535_HAS_IMAGE_URL=" . ($clone_has_image ? 'true' : 'false') . "\n";
echo "RECOMMENDED_DRY_RECIPE_ID=" . $meta_patch_plan['_dry_recipe_id']['recommended_value'] . "\n";
echo "RECOMMENDED_IMAGE_SOURCE=" . $meta_patch_plan['_dry_recipe_image_url']['source'] . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_META_NORMALIZER_PLAN_REPORT.md\n";
