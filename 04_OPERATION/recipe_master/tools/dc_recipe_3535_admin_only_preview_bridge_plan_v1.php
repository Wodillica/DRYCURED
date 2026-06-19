<?php
if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_3535_ADMIN_BRIDGE_PLAN_OUT');
$qa_path = getenv('DC_3535_ADMIN_BRIDGE_PLAN_QA');
$post_patch_json = getenv('DC_3535_POST_PATCH_JSON');

if (!$out_dir || !$qa_path || !$post_patch_json) {
    fwrite(STDERR, "FAIL: nedostaju env varijable.\n");
    exit(1);
}

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

function dcap_fail($msg) {
    fwrite(STDERR, "FAIL: " . $msg . "\n");
    exit(1);
}

function dcap_json_read($path) {
    if (!is_readable($path)) {
        dcap_fail("ne mogu čitati JSON: " . $path);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        dcap_fail("JSON nije valjan: " . $path);
    }
    return $data;
}

function dcap_basic_post($post_id) {
    $p = get_post($post_id);
    if (!$p) {
        return null;
    }
    return [
        'ID' => (int)$p->ID,
        'title' => $p->post_title,
        'slug' => $p->post_name,
        'status' => $p->post_status,
        'type' => $p->post_type,
        'modified_gmt' => $p->post_modified_gmt,
        'permalink' => get_permalink($post_id),
        'admin_edit_url' => admin_url('post.php?post=' . (int)$post_id . '&action=edit'),
        'logged_in_front_preview_url' => get_permalink($post_id) . (strpos(get_permalink($post_id), '?') === false ? '?' : '&') . 'preview=true',
    ];
}

function dcap_meta($post_id, $key) {
    $v = get_post_meta($post_id, $key, true);
    return is_string($v) ? $v : '';
}

function dcap_file_scan($path) {
    $txt = is_readable($path) ? file_get_contents($path) : '';
    if ($txt === '') {
        return [
            'exists' => false,
            'path' => $path,
        ];
    }

    $patterns = [
        'the_content_filters' => '/add_filter\s*\(\s*[\'"]the_content[\'"]/',
        'is_singular' => '/is_singular\s*\(/',
        'is_admin' => '/is_admin\s*\(/',
        'current_user_can' => '/current_user_can\s*\(/',
        'dry_recipe_id' => '/_dry_recipe_id/',
        'dry_recipe_full_markdown' => '/_dry_recipe_full_markdown/',
        'private_status' => '/private/',
        'get_post_status' => '/get_post_status\s*\(/',
        'dry_recipe_post_type' => '/dry_recipe/',
    ];

    $counts = [];
    $lines = preg_split('/\R/', $txt);
    $interesting = [];

    foreach ($patterns as $name => $rx) {
        preg_match_all($rx, $txt, $m);
        $counts[$name] = count($m[0]);
    }

    foreach ($lines as $i => $line) {
        foreach ($patterns as $name => $rx) {
            if (preg_match($rx, $line)) {
                $interesting[] = [
                    'pattern' => $name,
                    'line' => $i + 1,
                    'text' => trim(mb_substr($line, 0, 260)),
                ];
                break;
            }
        }
    }

    return [
        'exists' => true,
        'path' => $path,
        'bytes' => strlen($txt),
        'pattern_counts' => $counts,
        'interesting_lines' => array_slice($interesting, 0, 260),
    ];
}

function dcap_check(&$checks, $key, $label, $ok, $severity, $note) {
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

$post_patch = dcap_json_read($post_patch_json);

$source = get_post($source_id);
$clone = get_post($clone_id);
$reference = get_post($reference_id);

if (!$source || !$clone || !$reference) {
    dcap_fail("nedostaje jedan od postova 2976/3042/3535.");
}

$source_now = dcap_basic_post($source_id);
$clone_now = dcap_basic_post($clone_id);
$reference_now = dcap_basic_post($reference_id);

$clone_meta = [
    '_dry_recipe_id' => dcap_meta($clone_id, '_dry_recipe_id'),
    '_dry_recipe_preview_mode' => dcap_meta($clone_id, '_dry_recipe_preview_mode'),
    '_dry_recipe_preview_source_post_id' => dcap_meta($clone_id, '_dry_recipe_preview_source_post_id'),
    '_dry_recipe_public_update_allowed' => dcap_meta($clone_id, '_dry_recipe_public_update_allowed'),
    '_dry_recipe_public_verified' => dcap_meta($clone_id, '_dry_recipe_public_verified'),
    '_dry_recipe_source_validation_status' => dcap_meta($clone_id, '_dry_recipe_source_validation_status'),
    '_dry_recipe_type_router' => dcap_meta($clone_id, '_dry_recipe_type_router'),
    '_dry_recipe_full_markdown' => dcap_meta($clone_id, '_dry_recipe_full_markdown'),
    '_dry_recipe_sections' => dcap_meta($clone_id, '_dry_recipe_sections'),
    '_dry_verified_process' => dcap_meta($clone_id, '_dry_verified_process'),
];

$repo_plugin = '/root/DRYCURED_GITHUB/wp-content/mu-plugins/drycured-recipe-view-v1.php';
$live_plugin = '/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php';

$repo_scan = dcap_file_scan($repo_plugin);
$live_scan = dcap_file_scan($live_plugin);

$checks = [];

dcap_check($checks, 'clone_private', 'Clone 3535 je private', $clone_now['status'] === 'private', 'BLOCKER', 'Admin-only preview bridge smije raditi samo na privatnom cloneu.');
dcap_check($checks, 'clone_type', 'Clone 3535 je dry_recipe', $clone_now['type'] === 'dry_recipe', 'BLOCKER', 'Bridge ne smije raditi na drugim tipovima zapisa.');
dcap_check($checks, 'source_publish', 'Source 3042 je publish', $source_now['status'] === 'publish', 'BLOCKER', 'Source se ne smije dirati.');
dcap_check($checks, 'clone_preview_mode', 'Clone ima PRIVATE_CLONE_ONLY', $clone_meta['_dry_recipe_preview_mode'] === 'PRIVATE_CLONE_ONLY', 'BLOCKER', 'Bez ove oznake bridge ne smije raditi.');
dcap_check($checks, 'clone_source_link', 'Clone je vezan na source 3042', $clone_meta['_dry_recipe_preview_source_post_id'] === '3042', 'BLOCKER', 'Mora biti jasno vezan na source.');
dcap_check($checks, 'public_update_false', 'Public update je 0', $clone_meta['_dry_recipe_public_update_allowed'] === '0', 'BLOCKER', 'Bridge ne smije otvarati javni update tok.');
dcap_check($checks, 'public_verified_false', 'Public verified je 0', $clone_meta['_dry_recipe_public_verified'] === '0', 'BLOCKER', 'Recept nije verificiran za javnu objavu.');
dcap_check($checks, 'has_recipe_id', 'Clone ima _dry_recipe_id', $clone_meta['_dry_recipe_id'] === 'MD-JESUS_DE_LYON_DEBELA_SUHA_KOBASICA', 'MAJOR', 'Meta normalizer je uspješno odradio minimalni ID.');
dcap_check($checks, 'has_full_markdown', 'Clone ima _dry_recipe_full_markdown', strlen($clone_meta['_dry_recipe_full_markdown']) > 1000, 'MAJOR', 'Bridge mora imati izvor sadržaja.');
dcap_check($checks, 'has_sections_json', 'Clone ima _dry_recipe_sections', strlen($clone_meta['_dry_recipe_sections']) > 100, 'MAJOR', 'Bridge može koristiti strukturirane sekcije.');
dcap_check($checks, 'has_process_json', 'Clone ima _dry_verified_process', strlen($clone_meta['_dry_verified_process']) > 100, 'MAJOR', 'Bridge može koristiti procesne podatke.');
dcap_check($checks, 'renderer_not_activated', 'Post-patch renderer nije aktiviran', ($post_patch['renderer_improved'] ?? true) === false, 'MAJOR', 'Potvrđuje potrebu za bridge planom.');
dcap_check($checks, 'repo_plugin_exists', 'Repo renderer plugin postoji', $repo_scan['exists'] ?? false, 'MAJOR', 'Ne smijemo raditi novi dizajn bez pregleda postojećeg plugin sloja.');
dcap_check($checks, 'live_plugin_exists', 'Live renderer plugin postoji', $live_scan['exists'] ?? false, 'MAJOR', 'Live i repo renderer moraju biti dostupni.');

$failures = array_values(array_filter($checks, fn($c) => $c['status'] === 'FAIL'));
$blocker_failures = array_values(array_filter($failures, fn($c) => $c['severity'] === 'BLOCKER'));

$plan_status = count($blocker_failures) === 0 ? 'PLAN_READY_ADMIN_ONLY_PREVIEW_BRIDGE' : 'PLAN_BLOCKED';

$manual_preview_urls = [
    'admin_edit_url' => $clone_now['admin_edit_url'],
    'logged_in_front_preview_url' => $clone_now['logged_in_front_preview_url'],
    'public_unauth_url_expected_404' => $clone_now['permalink'],
];

$recommended_bridge = [
    'name' => 'drycured-private-preview-bridge',
    'file' => 'wp-content/mu-plugins/drycured-private-preview-bridge.php',
    'status' => 'PLAN_ONLY_NOT_CREATED',
    'principle' => 'Admin-only preview bridge, not public renderer replacement.',
    'activation_scope' => [
        'only_when_is_admin_or_logged_in_admin_preview' => true,
        'required_capability' => 'manage_options',
        'required_post_id' => 3535,
        'required_post_type' => 'dry_recipe',
        'required_post_status' => 'private',
        'required_meta_preview_mode' => 'PRIVATE_CLONE_ONLY',
        'required_meta_public_update_allowed' => '0',
        'required_meta_public_verified' => '0',
        'required_meta_source_post_id' => '3042',
    ],
    'forbidden_actions' => [
        'No public post 3042 writes',
        'No public title/slug/status changes',
        'No global renderer replacement',
        'No public route without admin capability',
        'No SEO indexable preview page',
        'No use of source-lock/debug labels in public output',
    ],
    'implementation_options' => [
        [
            'id' => 'A',
            'name' => 'Use existing logged-in WP preview first',
            'risk' => 'lowest',
            'description' => 'Prvo ručno otvoriti logged-in front preview URL kao administrator. Ako se u stvarnom browser kontekstu aktivira prikaz, bridge nije potreban.',
        ],
        [
            'id' => 'B',
            'name' => 'Admin-only preview page in wp-admin',
            'risk' => 'low',
            'description' => 'Dodati wp-admin stranicu koja za post 3535 prikazuje strukturirani preview iz postojećih meta podataka. Nije javni renderer i nije indeksabilno.',
        ],
        [
            'id' => 'C',
            'name' => 'Temporary private preview front route with nonce',
            'risk' => 'medium',
            'description' => 'Front route dostupan samo logged-in adminu, s nonceom i noindex headerima. Koristiti samo ako admin page nije dovoljan.',
        ],
    ],
    'recommended_next' => 'A_THEN_B_IF_NEEDED',
];

$plan = [
    'generated_at' => gmdate('c'),
    'plan_status' => $plan_status,
    'mode' => 'READ_ONLY_ADMIN_ONLY_PREVIEW_BRIDGE_PLAN',
    'wordpress_write_allowed_now' => false,
    'public_update_allowed' => false,
    'source_post_write_allowed' => false,
    'renderer_change_allowed' => false,
    'posts' => [
        'reference_2976' => $reference_now,
        'source_3042' => $source_now,
        'clone_3535' => $clone_now,
    ],
    'clone_meta' => [
        '_dry_recipe_id' => $clone_meta['_dry_recipe_id'],
        '_dry_recipe_preview_mode' => $clone_meta['_dry_recipe_preview_mode'],
        '_dry_recipe_preview_source_post_id' => $clone_meta['_dry_recipe_preview_source_post_id'],
        '_dry_recipe_public_update_allowed' => $clone_meta['_dry_recipe_public_update_allowed'],
        '_dry_recipe_public_verified' => $clone_meta['_dry_recipe_public_verified'],
        '_dry_recipe_source_validation_status' => $clone_meta['_dry_recipe_source_validation_status'],
        '_dry_recipe_type_router' => $clone_meta['_dry_recipe_type_router'],
        '_dry_recipe_full_markdown_length' => strlen($clone_meta['_dry_recipe_full_markdown']),
        '_dry_recipe_sections_length' => strlen($clone_meta['_dry_recipe_sections']),
        '_dry_verified_process_length' => strlen($clone_meta['_dry_verified_process']),
    ],
    'post_patch_input' => [
        'path' => $post_patch_json,
        'qa_status' => $post_patch['qa_status'] ?? '',
        'renderer_improved' => $post_patch['renderer_improved'] ?? null,
        'public_update_allowed' => $post_patch['public_update_allowed'] ?? null,
    ],
    'manual_preview_urls' => $manual_preview_urls,
    'repo_plugin_scan_summary' => [
        'exists' => $repo_scan['exists'] ?? false,
        'bytes' => $repo_scan['bytes'] ?? 0,
        'pattern_counts' => $repo_scan['pattern_counts'] ?? [],
    ],
    'live_plugin_scan_summary' => [
        'exists' => $live_scan['exists'] ?? false,
        'bytes' => $live_scan['bytes'] ?? 0,
        'pattern_counts' => $live_scan['pattern_counts'] ?? [],
    ],
    'recommended_bridge' => $recommended_bridge,
    'checks' => $checks,
];

file_put_contents(
    rtrim($out_dir, '/') . '/3535_admin_only_preview_bridge_plan_v1.json',
    wp_json_encode($plan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

file_put_contents(
    rtrim($out_dir, '/') . '/3535_admin_only_preview_bridge_plugin_scan_repo.json',
    wp_json_encode($repo_scan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

file_put_contents(
    rtrim($out_dir, '/') . '/3535_admin_only_preview_bridge_plugin_scan_live.json',
    wp_json_encode($live_scan, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

$csv = fopen(rtrim($out_dir, '/') . '/3535_admin_only_preview_bridge_checks.csv', 'w');
fputcsv($csv, ['key', 'label', 'status', 'severity', 'note']);
foreach ($checks as $c) {
    fputcsv($csv, [$c['key'], $c['label'], $c['status'], $c['severity'], $c['note']]);
}
fclose($csv);

$md = [];
$md[] = '# 3535 admin-only preview bridge plan v1';
$md[] = '';
$md[] = 'Status: **' . $plan_status . '**';
$md[] = '';
$md[] = 'Ovaj korak ne mijenja WordPress. Planira kako pregledati privatni clone `3535` bez javnog izlaganja i bez promjene postojećeg Drycured prikaza.';
$md[] = '';
$md[] = '## Zaključak prethodnog QA';
$md[] = '';
$md[] = '- `3535` je private i nije javno izložen.';
$md[] = '- `_dry_recipe_id` je upisan.';
$md[] = '- `_dry_recipe_id` sam nije aktivirao kartični renderer.';
$md[] = '- Sadržaj je prisutan, ali ostaje raw markdown u internom snapshotu.';
$md[] = '- Javni update ostaje zabranjen.';
$md[] = '';
$md[] = '## Sigurnosne granice';
$md[] = '';
$md[] = '- WordPress write allowed now: `false`';
$md[] = '- Public update allowed: `false`';
$md[] = '- Source post write allowed: `false`';
$md[] = '- Renderer change allowed: `false`';
$md[] = '- Allowed target for future bridge: private clone `3535` only';
$md[] = '- Forbidden target: public source `3042`';
$md[] = '';
$md[] = '## Manualni admin preview linkovi';
$md[] = '';
$md[] = '- Admin edit URL: `' . $manual_preview_urls['admin_edit_url'] . '`';
$md[] = '- Logged-in front preview URL: `' . $manual_preview_urls['logged_in_front_preview_url'] . '`';
$md[] = '- Public unauth URL expected 404: `' . $manual_preview_urls['public_unauth_url_expected_404'] . '`';
$md[] = '';
$md[] = '## Preporučeni smjer';
$md[] = '';
$md[] = '1. Prvo otvoriti `logged_in_front_preview_url` kao prijavljeni administrator i vizualno provjeriti aktivira li stvarni front-end kontekst bolji prikaz.';
$md[] = '2. Ako i dalje prikazuje raw markdown, napraviti mali MU-plugin **admin-only preview bridge**.';
$md[] = '3. Bridge mora raditi samo u admin/logged-in kontekstu, samo za `post_status=private`, samo uz `PRIVATE_CLONE_ONLY`, samo za post `3535`, i mora slati `noindex` / bez javnog SEO izlaganja.';
$md[] = '4. Bridge ne smije mijenjati javni renderer ni javni recept `3042`.';
$md[] = '';
$md[] = '## Moguće implementacije';
$md[] = '';
foreach ($recommended_bridge['implementation_options'] as $opt) {
    $md[] = '### Opcija ' . $opt['id'] . ' — ' . $opt['name'];
    $md[] = '';
    $md[] = '- Rizik: `' . $opt['risk'] . '`';
    $md[] = '- Opis: ' . $opt['description'];
    $md[] = '';
}
$md[] = '## QA provjere plana';
$md[] = '';
$md[] = '| Provjera | Status | Težina | Napomena |';
$md[] = '|---|---|---|---|';
foreach ($checks as $c) {
    $md[] = '| ' . str_replace('|', '/', $c['label']) . ' | ' . $c['status'] . ' | ' . $c['severity'] . ' | ' . str_replace('|', '/', $c['note']) . ' |';
}
$md[] = '';
$md[] = '## Odluka';
$md[] = '';
$md[] = 'Ne dodavati više meta ključeva naslijepo. Sljedeći praktični korak je ručna admin preview provjera; ako ne uspije, onda planirano izraditi admin-only preview bridge kao odvojeni MU-plugin s vrlo uskim guardovima.';
$md[] = '';
$md[] = 'Javni WordPress update i dalje nije dopušten.';
$md[] = '';

file_put_contents(rtrim($out_dir, '/') . '/3535_ADMIN_ONLY_PREVIEW_BRIDGE_PLAN_REPORT.md', implode("\n", $md));

$qa_old = file_get_contents($qa_path);
$marker = '<!-- DC_3535_ADMIN_ONLY_PREVIEW_BRIDGE_PLAN_V1 -->';
$append = $marker . "\n\n" .
"## 3535 admin-only preview bridge plan v1\n\n" .
"Status: **" . $plan_status . "**\n\n" .
"- WordPress write allowed now: `false`\n" .
"- Public update allowed: `false`\n" .
"- Source post write allowed: `false`\n" .
"- Renderer change allowed: `false`\n" .
"- Recommended next: `" . $recommended_bridge['recommended_next'] . "`\n" .
"- Report: `review/" . basename($out_dir) . "/3535_ADMIN_ONLY_PREVIEW_BRIDGE_PLAN_REPORT.md`\n" .
"- Plan JSON: `review/" . basename($out_dir) . "/3535_admin_only_preview_bridge_plan_v1.json`\n";

if (strpos($qa_old, $marker) === false) {
    file_put_contents($qa_path, rtrim($qa_old) . "\n\n" . $append . "\n");
}

echo "=== 3535 ADMIN-ONLY PREVIEW BRIDGE PLAN COMPLETE ===\n";
echo "PLAN_STATUS=" . $plan_status . "\n";
echo "WORDPRESS_WRITE_ALLOWED_NOW=false\n";
echo "PUBLIC_UPDATE_ALLOWED=false\n";
echo "SOURCE_POST_WRITE_ALLOWED=false\n";
echo "RENDERER_CHANGE_ALLOWED=false\n";
echo "RECOMMENDED_NEXT=" . $recommended_bridge['recommended_next'] . "\n";
echo "ADMIN_EDIT_URL=" . $manual_preview_urls['admin_edit_url'] . "\n";
echo "LOGGED_IN_FRONT_PREVIEW_URL=" . $manual_preview_urls['logged_in_front_preview_url'] . "\n";
echo "PUBLIC_UNAUTH_URL_EXPECTED_404=" . $manual_preview_urls['public_unauth_url_expected_404'] . "\n";
echo "REPORT=" . rtrim($out_dir, '/') . "/3535_ADMIN_ONLY_PREVIEW_BRIDGE_PLAN_REPORT.md\n";
