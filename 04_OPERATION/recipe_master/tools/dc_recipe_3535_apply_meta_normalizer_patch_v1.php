<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$plan_path = getenv('DC_3535_META_PLAN_JSON');
$out_dir = getenv('DC_3535_META_PATCH_OUT');
$qa_path = getenv('DC_3535_META_PATCH_QA');
$db_backup_path = getenv('DC_3535_DB_BACKUP_PATH');

if (!$plan_path || !$out_dir || !$qa_path || !$db_backup_path) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dc3535_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dc3535_json_read($path) {
    if (!is_readable($path)) {
        dc3535_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dc3535_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dc3535_basic_post($post_id) {
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
    ];
}

function dc3535_render_snapshot($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return [
            'exists' => false,
            'html_length' => 0,
            'plain_length' => 0,
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
            'has_raw_markdown' => strpos($html, '# ') !== false,
            'has_drycured_recipe' => stripos($html, 'drycured') !== false && stripos($html, 'recipe') !== false,
            'has_dc_recipe' => stripos($html, 'dc-recipe') !== false,
            'has_dcv' => stripos($html, 'dcv') !== false,
            'has_private_notice' => stripos($plain, 'privatni preview') !== false || stripos($plain, 'nije javni recept') !== false,
            'has_title' => stripos($plain, 'Jésus de Lyon') !== false || stripos($plain, 'Jesus de Lyon') !== false,
            'has_grinding' => stripos($plain, 'mljevenje') !== false || stripos($plain, 'rešetka') !== false,
            'has_casing' => stripos($plain, 'crijeva') !== false || stripos($plain, 'ovitak') !== false,
        ],
        'plain_excerpt' => mb_substr($plain, 0, 1800),
        'html' => $html,
    ];
}

$source_id = 3042;
$clone_id = 3535;

$plan = dc3535_json_read($plan_path);

if (($plan['plan_status'] ?? '') !== 'PLAN_READY_PRIVATE_CLONE_ONLY') {
    dc3535_fail("plan_status nije PLAN_READY_PRIVATE_CLONE_ONLY.");
}

if (($plan['wordpress_write_allowed_now'] ?? null) !== false) {
    dc3535_fail("plan mora imati wordpress_write_allowed_now=false.");
}

if (($plan['public_update_allowed'] ?? null) !== false) {
    dc3535_fail("plan mora imati public_update_allowed=false.");
}

if (($plan['allowed_future_target'] ?? '') !== 'PRIVATE_CLONE_3535_ONLY') {
    dc3535_fail("plan target nije PRIVATE_CLONE_3535_ONLY.");
}

if (($plan['forbidden_target'] ?? '') !== 'PUBLIC_SOURCE_3042') {
    dc3535_fail("plan forbidden target nije PUBLIC_SOURCE_3042.");
}

$patch_plan = $plan['meta_patch_plan_for_future_step'] ?? [];
if (!isset($patch_plan['_dry_recipe_id'])) {
    dc3535_fail("plan nema _dry_recipe_id patch.");
}

$recommended_id = (string)($patch_plan['_dry_recipe_id']['recommended_value'] ?? '');
if ($recommended_id === '') {
    dc3535_fail("recommended _dry_recipe_id je prazan.");
}

if (!preg_match('/^[A-Z0-9_\\-]+$/', $recommended_id)) {
    dc3535_fail("recommended _dry_recipe_id ima nedopuštene znakove: " . $recommended_id);
}

if (stripos($recommended_id, 'sk-') !== false || stripos($recommended_id, 'secret') !== false || stripos($recommended_id, 'key') !== false) {
    dc3535_fail("recommended _dry_recipe_id izgleda sumnjivo.");
}

$source = get_post($source_id);
$clone = get_post($clone_id);

if (!$source || !$clone) {
    dc3535_fail("source 3042 ili clone 3535 ne postoji.");
}

if ($source->post_type !== 'dry_recipe' || $source->post_status !== 'publish') {
    dc3535_fail("source 3042 nije očekivani publish dry_recipe.");
}

if ($clone->post_type !== 'dry_recipe' || $clone->post_status !== 'private') {
    dc3535_fail("clone 3535 nije private dry_recipe.");
}

$source_before = dc3535_basic_post($source_id);
$clone_before = dc3535_basic_post($clone_id);

$clone_before_meta = [
    '_dry_recipe_id' => get_post_meta($clone_id, '_dry_recipe_id', true),
    '_dry_recipe_public_update_allowed' => get_post_meta($clone_id, '_dry_recipe_public_update_allowed', true),
    '_dry_recipe_public_verified' => get_post_meta($clone_id, '_dry_recipe_public_verified', true),
    '_dry_recipe_preview_mode' => get_post_meta($clone_id, '_dry_recipe_preview_mode', true),
    '_dry_recipe_preview_source_post_id' => get_post_meta($clone_id, '_dry_recipe_preview_source_post_id', true),
];

if ((string)$clone_before_meta['_dry_recipe_public_update_allowed'] !== '0') {
    dc3535_fail("clone nema _dry_recipe_public_update_allowed=0 prije patcha.");
}

if ((string)$clone_before_meta['_dry_recipe_public_verified'] !== '0') {
    dc3535_fail("clone nema _dry_recipe_public_verified=0 prije patcha.");
}

if ((string)$clone_before_meta['_dry_recipe_preview_mode'] !== 'PRIVATE_CLONE_ONLY') {
    dc3535_fail("clone nema PRIVATE_CLONE_ONLY prije patcha.");
}

if ((string)$clone_before_meta['_dry_recipe_preview_source_post_id'] !== '3042') {
    dc3535_fail("clone nije vezan na source 3042 prije patcha.");
}

$write_performed = false;

if ((string)$clone_before_meta['_dry_recipe_id'] === '') {
    update_post_meta($clone_id, '_dry_recipe_id', $recommended_id);
    $write_performed = true;
} elseif ((string)$clone_before_meta['_dry_recipe_id'] !== $recommended_id) {
    dc3535_fail("clone već ima drukčiji _dry_recipe_id: " . $clone_before_meta['_dry_recipe_id']);
}

update_post_meta($clone_id, '_dry_recipe_public_update_allowed', '0');
update_post_meta($clone_id, '_dry_recipe_public_verified', '0');
update_post_meta($clone_id, '_dry_recipe_preview_mode', 'PRIVATE_CLONE_ONLY');
update_post_meta($clone_id, '_dry_recipe_preview_source_post_id', '3042');
update_post_meta($clone_id, '_dry_recipe_meta_normalizer_patch_v1', gmdate('c'));
$write_performed = true;

$source_after = dc3535_basic_post($source_id);
$clone_after = dc3535_basic_post($clone_id);

$source_unchanged = true;
foreach (['post_title', 'post_name', 'post_status', 'post_type', 'post_modified_gmt'] as $k) {
    if ((string)$source_before[$k] !== (string)$source_after[$k]) {
        $source_unchanged = false;
    }
}

if (!$source_unchanged) {
    dc3535_fail("ZAŠTITA: source 3042 se promijenio.");
}

$clone_after_meta = [
    '_dry_recipe_id' => get_post_meta($clone_id, '_dry_recipe_id', true),
    '_dry_recipe_public_update_allowed' => get_post_meta($clone_id, '_dry_recipe_public_update_allowed', true),
    '_dry_recipe_public_verified' => get_post_meta($clone_id, '_dry_recipe_public_verified', true),
    '_dry_recipe_preview_mode' => get_post_meta($clone_id, '_dry_recipe_preview_mode', true),
    '_dry_recipe_preview_source_post_id' => get_post_meta($clone_id, '_dry_recipe_preview_source_post_id', true),
    '_dry_recipe_image_url' => get_post_meta($clone_id, '_dry_recipe_image_url', true),
];

$render_after = dc3535_render_snapshot($clone_id);
file_put_contents(rtrim($out_dir, '/') . '/3535_render_after_meta_patch_snapshot.html', $render_after['html'] ?? '');

$checks = [];
function dc3535_check(&$checks, $key, $label, $ok, $severity, $note) {
    $checks[] = [
        'key' => $key,
        'label' => $label,
        'status' => $ok ? 'PASS' : 'FAIL',
        'severity' => $severity,
        'note' => $note,
    ];
}

dc3535_check($checks, 'source_unchanged', 'Source 3042 nije mijenjan', $source_unchanged, 'BLOCKER', 'Title, slug, status, type i modified_gmt moraju ostati isti.');
dc3535_check($checks, 'clone_private', 'Clone 3535 ostaje private', $clone_after['post_status'] === 'private', 'BLOCKER', 'Patch se smije primijeniti samo na private clone.');
dc3535_check($checks, 'clone_type', 'Clone 3535 ostaje dry_recipe', $clone_after['post_type'] === 'dry_recipe', 'BLOCKER', 'Post type mora ostati dry_recipe.');
dc3535_check($checks, 'dry_recipe_id_written', '_dry_recipe_id upisan', (string)$clone_after_meta['_dry_recipe_id'] === $recommended_id, 'BLOCKER', 'Ovo je glavni cilj meta-normalizacije.');
dc3535_check($checks, 'public_update_0', 'Public update ostaje 0', (string)$clone_after_meta['_dry_recipe_public_update_allowed'] === '0', 'BLOCKER', 'Privatni clone ne smije dopustiti javni update.');
dc3535_check($checks, 'public_verified_0', 'Public verified ostaje 0', (string)$clone_after_meta['_dry_recipe_public_verified'] === '0', 'BLOCKER', 'Recept nije public verified.');
dc3535_check($checks, 'preview_mode', 'Preview mode ostaje PRIVATE_CLONE_ONLY', (string)$clone_after_meta['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY', 'BLOCKER', 'Clone mora ostati jasno privatni.');
dc3535_check($checks, 'source_link', 'Clone ostaje vezan na source 3042', (string)$clone_after_meta['_dry_recipe_preview_source_post_id'] === '3042', 'BLOCKER', 'Veza na source mora ostati 3042.');
dc3535_check($checks, 'image_skipped', 'Slika nije upisana jer nema dostupne vrijednosti', (string)$clone_after_meta['_dry_recipe_image_url'] === '', 'INFO', 'Plan je preporučio SKIP_NO_VALUE.');
dc3535_check($checks, 'render_has_title', 'Render nakon patcha sadrži naslov', (bool)($render_after['markers']['has_title'] ?? false), 'MAJOR', 'Interni render mora sadržavati naziv.');
dc3535_check($checks, 'render_has_private_notice', 'Render zadržava privatnu napomenu', (bool)($render_after['markers']['has_private_notice'] ?? false), 'MAJOR', 'Privatni clone mora ostati jasno označen.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL' && $c['severity'] !== 'INFO'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$patch_status = count($blocker_failures) === 0 && count($failures) === 0 ? 'PATCH_APPLIED_PRIVATE_CLONE_ONLY' : 'PATCH_QA_FAIL';

$result = [
    'generated_at' => gmdate('c'),
    'patch_status' => $patch_status,
    'write_performed' => $write_performed,
    'public_update_allowed' => false,
    'source_post_write_allowed' => false,
    'target' => 'PRIVATE_CLONE_3535_ONLY',
    'forbidden_target' => 'PUBLIC_SOURCE_3042',
    'db_backup_path_outside_git' => $db_backup_path,
    'source_before' => $source_before,
    'source_after' => $source_after,
    'source_unchanged' => $source_unchanged,
    'clone_before' => $clone_before,
    'clone_after' => $clone_after,
    'clone_before_meta' => $clone_before_meta,
    'clone_after_meta' => $clone_after_meta,
    'recommended_dry_recipe_id' => $recommended_id,
    'render_after' => [
        'html_length' => $render_after['html_length'] ?? 0,
        'plain_length' => $render_after['plain_length'] ?? 0,
        'markers' => $render_after['markers'] ?? [],
        'plain_excerpt' => $render_after['plain_excerpt'] ?? '',
    ],
    'checks' => $checks,
    'fail_total_major_or_blocker' => count($failures),
    'blocker_fail_total' => count($blocker_failures),
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_meta_normalizer_patch_result.json',
    wp_json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_meta_normalizer_patch_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$backup_manifest = [];
$backup_manifest[] = '# Backup manifest — 3535 meta-normalizer patch v1';
$backup_manifest[] = '';
$backup_manifest[] = 'Status: **BACKUP_STORED_OUTSIDE_GIT**';
$backup_manifest[] = '';
$backup_manifest[] = '- Backup type: WordPress DB export';
$backup_manifest[] = '- Backup location: `' . $db_backup_path . '`';
$backup_manifest[] = '- Stored outside Git: `true`';
$backup_manifest[] = '- Reason: SQL backups may contain secrets and must not be committed.';
$backup_manifest[] = '';
file_put_contents(rtrim($out_dir, '/') . '/3535_META_NORMALIZER_BACKUP_MANIFEST.md', implode("\n", $backup_manifest));

$md = [];
$md[] = '# 3535 meta-normalizer patch v1';
$md[] = '';
$md[] = 'Status: **' . $patch_status . '**';
$md[] = '';
$md[] = 'Ovaj korak upisuje minimalni meta patch samo na privatni clone `3535`. Javni post `3042` nije mijenjan.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Target: `PRIVATE_CLONE_3535_ONLY`';
$md[] = '- Forbidden target: `PUBLIC_SOURCE_3042`';
$md[] = '- Source post unchanged: `' . ($source_unchanged ? 'true' : 'false') . '`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Source post write allowed: `false`';
$md[] = '- `_dry_recipe_id`: `' . $clone_after_meta['_dry_recipe_id'] . '`';
$md[] = '- `_dry_recipe_image_url`: `' . ($clone_after_meta['_dry_recipe_image_url'] === '' ? 'SKIPPED_NO_VALUE' : $clone_after_meta['_dry_recipe_image_url']) . '`';
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
$md[] = '## Zaključak';
$md[] = '';
if ($patch_status === 'PATCH_APPLIED_PRIVATE_CLONE_ONLY') {
    $md[] = 'Meta-normalizer patch je uspješno primijenjen samo na privatni clone `3535`. Sljedeći korak je read-only render QA nakon patcha.';
} else {
    $md[] = 'Patch ima QA padove. Ne nastavljati dok se ne riješe.';
}
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/3535_META_NORMALIZER_PATCH_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_3535_META_NORMALIZER_PATCH_V1 -->';
$append = $marker . "\n\n" .
"## 3535 meta-normalizer patch v1\n\n" .
"Status: **" . $patch_status . "**\n\n" .
"- Target: `PRIVATE_CLONE_3535_ONLY`\n" .
"- Forbidden target: `PUBLIC_SOURCE_3042`\n" .
"- Source unchanged: `" . ($source_unchanged ? 'true' : 'false') . "`\n" .
"- Public update allowed: `false`\n" .
"- `_dry_recipe_id`: `" . $clone_after_meta['_dry_recipe_id'] . "`\n" .
"- Report: `review/" . basename($out_dir) . "/3535_META_NORMALIZER_PATCH_REPORT.md`\n" .
"- Result JSON: `review/" . basename($out_dir) . "/3535_meta_normalizer_patch_result.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 3535 META-NORMALIZER PATCH COMPLETE ===\n";
echo "PATCH_STATUS=" . $patch_status . "\n";
echo "WRITE_PERFORMED=" . ($write_performed ? 'true' : 'false') . "\n";
echo "TARGET=PRIVATE_CLONE_3535_ONLY\n";
echo "FORBIDDEN_TARGET=PUBLIC_SOURCE_3042\n";
echo "SOURCE_UNCHANGED=" . ($source_unchanged ? 'true' : 'false') . "\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "CLONE_3535_STATUS=" . $clone_after['post_status'] . "\n";
echo "CLONE_3535_DRY_RECIPE_ID=" . $clone_after_meta['_dry_recipe_id'] . "\n";
echo "CLONE_3535_IMAGE_URL=" . ($clone_after_meta['_dry_recipe_image_url'] === '' ? 'SKIPPED_NO_VALUE' : $clone_after_meta['_dry_recipe_image_url']) . "\n";
echo "BLOCKER_FAIL_TOTAL=" . count($blocker_failures) . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_META_NORMALIZER_PATCH_REPORT.md\n";
