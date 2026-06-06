<?php
/**
 * DRYCURED PRIVATE DRAFT CLEANUP v1.7.1
 *
 * Controlled update for posts created in v1.7 only.
 * Updates only private dry_recipe posts with meta drycured_web_input_version=v1.7.
 * Fixes title from web_title, content UTF-8 text, and selected renderer-friendly meta fields.
 * No publish.
 */

$execute = getenv('DRYCURED_PRIVATE_DRAFT_CLEANUP_EXECUTE') === 'YES';

$out_dir = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/private_cleanup';
@mkdir($out_dir, 0755, true);

$summary_path = $out_dir . '/PRIVATE_CLEANUP_SUMMARY_v1_7_1.txt';
$csv_path = $out_dir . '/PRIVATE_CLEANUP_RESULTS_v1_7_1.csv';

function dc_bool_true($v) {
    if ($v === true) return true;
    if (is_string($v) && strtolower(trim($v)) === 'true') return true;
    if (is_int($v) && $v === 1) return true;
    return false;
}

function dc_get($arr, $key, $default = '') {
    return (is_array($arr) && array_key_exists($key, $arr) && $arr[$key] !== null) ? $arr[$key] : $default;
}

function dc_md_list($items) {
    if (!is_array($items) || count($items) === 0) return "- nije navedeno\n";
    $out = [];
    foreach ($items as $item) {
        if (is_array($item)) {
            $parts = [];
            foreach ($item as $k => $v) {
                if (is_array($v)) continue;
                if ($v === '' || $v === null) continue;
                $parts[] = $k . ': ' . $v;
            }
            $out[] = '- ' . implode('; ', $parts);
        } else {
            $out[] = '- ' . $item;
        }
    }
    return implode("\n", $out) . "\n";
}

function dc_build_clean_content($r) {
    $title = dc_get($r, 'web_title', dc_get($r, 'raw_title', 'Neimenovani recept'));
    $source_id = dc_get($r, 'source_recipe_id');
    $country = dc_get($r, 'country');
    $region = dc_get($r, 'region');
    $product_type = dc_get($r, 'product_type');
    $process_family = dc_get($r, 'process_family');
    $integration_status = dc_get($r, 'integration_status');
    $source_comparison = dc_get($r, 'source_comparison');
    $garlic = dc_get($r, 'garlic', 'nije navedeno');
    $casing = dc_get($r, 'casing', 'nije navedeno');
    $cutting = dc_get($r, 'cutting', 'nije navedeno');
    $server_note = dc_get($r, 'server_note');

    $lines = [];
    $lines[] = '# ' . $title;
    $lines[] = '';
    $lines[] = '## Status zapisa';
    $lines[] = '';
    $lines[] = '- Status: privatni server draft';
    $lines[] = '- Spremno za javnu objavu: NE';
    $lines[] = '- Spremno za automatski import: NE';
    $lines[] = '- Potrebna zavr?na ljudska provjera: DA';
    $lines[] = '';
    $lines[] = '## Izvorni trag';
    $lines[] = '';
    $lines[] = '- Source recipe ID: ' . $source_id;
    $lines[] = '- Dr?ava: ' . $country;
    $lines[] = '- Regija: ' . $region;
    $lines[] = '- Tip proizvoda: ' . $product_type;
    $lines[] = '- Procesna obitelj: ' . $process_family;
    $lines[] = '- Integracijski status: ' . $integration_status;
    $lines[] = '';
    $lines[] = '## Usporedba izvora';
    $lines[] = '';
    $lines[] = $source_comparison !== '' ? $source_comparison : 'Nije navedeno.';
    $lines[] = '';
    $lines[] = '## Sirovine i dodaci za 10 kg';
    $lines[] = '';
    $lines[] = dc_md_list(dc_get($r, 'ingredients_10kg', []));
    $lines[] = '## Teku?ine';
    $lines[] = '';
    $lines[] = dc_md_list(dc_get($r, 'liquids', []));
    $lines[] = '## ?e?njak';
    $lines[] = '';
    $lines[] = $garlic;
    $lines[] = '';
    $lines[] = '## Crijeva / omota? / oblik';
    $lines[] = '';
    $lines[] = $casing;
    $lines[] = '';
    $lines[] = '## Mljevenje / rezanje';
    $lines[] = '';
    $lines[] = $cutting;
    $lines[] = '';
    $lines[] = '## Procesna kronologija';
    $lines[] = '';
    $lines[] = dc_md_list(dc_get($r, 'process', []));
    $lines[] = '## Izvori';
    $lines[] = '';
    $evidence = dc_get($r, 'evidence', []);
    if (is_array($evidence) && count($evidence) > 0) {
        foreach ($evidence as $ev) {
            $lines[] = '- ' . dc_get($ev, 'title') . ' ? ' . dc_get($ev, 'url') . ' (' . dc_get($ev, 'type') . ')';
        }
    } else {
        $lines[] = '- nije navedeno';
    }
    $lines[] = '';
    $lines[] = '## Napomena za server';
    $lines[] = '';
    $lines[] = $server_note !== '' ? $server_note : 'Nije navedeno.';
    $lines[] = '';
    $lines[] = '## Sigurnosna napomena';
    $lines[] = '';
    $lines[] = 'Ovaj zapis je privatni radni unos za drycured.com receptni sustav. Ne smije se javno objaviti dok Davor/ChatGPT ne zavr?e zavr?nu provjeru.';
    $lines[] = '';

    return implode("\n", $lines);
}

$posts = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => 'private',
    'numberposts' => -1,
    'meta_key' => 'drycured_web_input_version',
    'meta_value' => 'v1.7',
    'fields' => 'ids',
]);

$fh = fopen($csv_path, 'w');
fputcsv($fh, [
    'post_id',
    'old_title',
    'new_title',
    'old_slug',
    'new_slug',
    'action',
    'notes'
]);

$total = 0;
$updated = 0;
$blocked = 0;
$errors = 0;

foreach ($posts as $post_id) {
    $total++;
    $post = get_post($post_id);

    $raw_json = get_post_meta($post_id, 'drycured_input_raw_json', true);
    $r = json_decode($raw_json, true);

    if (!is_array($r)) {
        $errors++;
        fputcsv($fh, [$post_id, $post ? $post->post_title : '', '', '', '', 'ERROR', 'Cannot decode drycured_input_raw_json']);
        continue;
    }

    if (dc_bool_true(dc_get($r, 'ready_for_import', false)) || dc_bool_true(dc_get($r, 'public_publish_allowed', false))) {
        $blocked++;
        fputcsv($fh, [$post_id, $post->post_title, '', $post->post_name, '', 'BLOCKED', 'ready_for_import/public_publish_allowed true']);
        continue;
    }

    $new_title = dc_get($r, 'web_title', dc_get($r, 'raw_title', $post->post_title));
    $new_slug = sanitize_title(dc_get($r, 'slug', $post->post_name));
    $new_content = dc_build_clean_content($r);

    if (!$execute) {
        fputcsv($fh, [$post_id, $post->post_title, $new_title, $post->post_name, $new_slug, 'DRY_RUN_ONLY', 'No write performed']);
        continue;
    }

    $res = wp_update_post([
        'ID' => $post_id,
        'post_title' => wp_strip_all_tags($new_title),
        'post_name' => $new_slug,
        'post_content' => wp_slash($new_content),
        'post_status' => 'private',
    ], true);

    if (is_wp_error($res)) {
        $errors++;
        fputcsv($fh, [$post_id, $post->post_title, $new_title, $post->post_name, $new_slug, 'ERROR', $res->get_error_message()]);
        continue;
    }

    update_post_meta($post_id, 'drycured_web_title', $new_title);
    update_post_meta($post_id, 'drycured_country', dc_get($r, 'country'));
    update_post_meta($post_id, 'drycured_region', dc_get($r, 'region'));
    update_post_meta($post_id, 'drycured_product_type', dc_get($r, 'product_type'));
    update_post_meta($post_id, 'drycured_process_family', dc_get($r, 'process_family'));
    update_post_meta($post_id, 'drycured_integration_status', dc_get($r, 'integration_status'));
    update_post_meta($post_id, 'drycured_web_cleanup_version', 'v1.7.1');
    update_post_meta($post_id, 'drycured_public_publish_allowed', 'false');
    update_post_meta($post_id, 'drycured_ready_for_import', 'false');
    update_post_meta($post_id, 'drycured_requires_final_human_check', 'true');

    $updated++;
    fputcsv($fh, [$post_id, $post->post_title, $new_title, $post->post_name, $new_slug, 'UPDATED_PRIVATE_DRAFT', 'Title/content/meta cleaned safely']);
}

fclose($fh);

$summary = [];
$summary[] = 'DRYCURED PRIVATE DRAFT CLEANUP v1.7.1';
$summary[] = '';
$summary[] = $execute ? 'MODE: CONTROLLED WRITE' : 'MODE: DRY RUN ONLY';
$summary[] = 'SCOPE: private dry_recipe posts with drycured_web_input_version=v1.7 only';
$summary[] = 'NO PUBLISH';
$summary[] = '';
$summary[] = 'TOTAL_TARGET_POSTS: ' . count($posts);
$summary[] = 'TOTAL_PROCESSED: ' . $total;
$summary[] = 'UPDATED: ' . $updated;
$summary[] = 'BLOCKED: ' . $blocked;
$summary[] = 'ERRORS: ' . $errors;
$summary[] = 'OUTPUT_CSV: ' . $csv_path;
$summary[] = '';
$summary[] = 'SAFETY_CONFIRMATION:';
$summary[] = '- Only v1.7 private posts are targeted.';
$summary[] = '- Post status remains private.';
$summary[] = '- No public publish performed.';
$summary[] = '- ready_for_import remains false.';
$summary[] = '- public_publish_allowed remains false.';

file_put_contents($summary_path, implode("\n", $summary) . "\n");
echo file_get_contents($summary_path);
