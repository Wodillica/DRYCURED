<?php
/**
 * DRYCURED RENDERER META MAPPING v1.7.2
 *
 * Maps private v1.7 dry_recipe drafts to the canonical renderer meta model.
 * Updates only private dry_recipe posts with drycured_web_input_version=v1.7.
 * No publish.
 */

$execute = getenv('DRYCURED_RENDERER_MAPPING_EXECUTE') === 'YES';

$out_dir = '/root/DRYCURED_GITHUB/server-reports/recipes/web-recipe-input/v1_7/renderer_mapping';
@mkdir($out_dir, 0755, true);

$summary_path = $out_dir . '/RENDERER_MAPPING_SUMMARY_v1_7_2.txt';
$csv_path = $out_dir . '/RENDERER_MAPPING_RESULTS_v1_7_2.csv';

function dc_get($arr, $key, $default = '') {
    return (is_array($arr) && array_key_exists($key, $arr) && $arr[$key] !== null && $arr[$key] !== '') ? $arr[$key] : $default;
}

function dc_bool_true($v) {
    if ($v === true) return true;
    if (is_string($v) && strtolower(trim($v)) === 'true') return true;
    if (is_int($v) && $v === 1) return true;
    return false;
}

function dc_clean($v) {
    if (is_array($v)) return '';
    if ($v === null) return '';
    return trim((string) $v);
}

function dc_table_ingredients($items) {
    if (!is_array($items) || count($items) === 0) return "not_specified_in_source\n";

    $out = [];
    $out[] = '| Sastojak | Koli?ina za 10 kg (kg) | g/kg | Napomena |';
    $out[] = '|---|---:|---:|---|';

    foreach ($items as $it) {
        if (!is_array($it)) continue;
        $name = dc_clean(dc_get($it, 'name', ''));
        $kg = dc_clean(dc_get($it, 'kg', ''));
        $gkg = dc_clean(dc_get($it, 'g_per_kg', ''));
        $note = dc_clean(dc_get($it, 'note', ''));
        if ($name === '') continue;
        $out[] = '| ' . $name . ' | ' . $kg . ' | ' . $gkg . ' | ' . $note . ' |';
    }

    return implode("\n", $out);
}

function dc_table_liquids($items) {
    if (!is_array($items) || count($items) === 0) return "- Nije navedeno\n";

    $out = [];
    $out[] = '| Teku?ina | L | ml/kg | Napomena |';
    $out[] = '|---|---:|---:|---|';

    foreach ($items as $it) {
        if (!is_array($it)) continue;
        $name = dc_clean(dc_get($it, 'name', ''));
        $liters = dc_clean(dc_get($it, 'liters', ''));
        $mlkg = dc_clean(dc_get($it, 'ml_per_kg', ''));
        $note = dc_clean(dc_get($it, 'note', ''));
        if ($name === '') continue;
        $out[] = '| ' . $name . ' | ' . $liters . ' | ' . $mlkg . ' | ' . $note . ' |';
    }

    return implode("\n", $out);
}

function dc_list_process($items) {
    if (!is_array($items) || count($items) === 0) return "- Nije navedeno\n";
    $out = [];
    foreach ($items as $it) {
        $txt = dc_clean($it);
        if ($txt !== '') $out[] = '- ' . $txt;
    }
    return implode("\n", $out);
}

function dc_filter_process($items, $needles, $fallback) {
    if (!is_array($items)) return $fallback;
    $out = [];
    foreach ($items as $it) {
        $txt = dc_clean($it);
        $low = mb_strtolower($txt, 'UTF-8');
        foreach ($needles as $n) {
            if (mb_strpos($low, $n) !== false) {
                $out[] = '- ' . $txt;
                break;
            }
        }
    }
    return count($out) ? implode("\n", $out) : $fallback;
}

function dc_evidence_table($items, $source_id) {
    $out = [];
    $out[] = '| Izvor | URL | Uloga |';
    $out[] = '|---|---|---|';

    if (is_array($items) && count($items) > 0) {
        foreach ($items as $ev) {
            if (!is_array($ev)) continue;
            $title = dc_clean(dc_get($ev, 'title'));
            $url = dc_clean(dc_get($ev, 'url'));
            $type = dc_clean(dc_get($ev, 'type'));
            if ($title === '') continue;
            $out[] = '| ' . $title . ' | ' . $url . ' | ' . $type . ' |';
        }
    }

    $out[] = '| v1.7 source record | ' . $source_id . ' | Interni source ID i ulazni JSON |';

    return implode("\n", $out);
}

function dc_build_sections($r) {
    $title = dc_get($r, 'web_title', dc_get($r, 'raw_title', 'Neimenovani recept'));
    $raw_title = dc_get($r, 'raw_title', '');
    $source_id = dc_get($r, 'source_recipe_id', '');
    $country = dc_get($r, 'country', '');
    $region = dc_get($r, 'region', '');
    $product_type = dc_get($r, 'product_type', '');
    $process_family = dc_get($r, 'process_family', '');
    $integration_status = dc_get($r, 'integration_status', '');
    $source_comparison = dc_get($r, 'source_comparison', '');
    $garlic = dc_get($r, 'garlic', 'nije navedeno');
    $casing = dc_get($r, 'casing', 'nije navedeno');
    $cutting = dc_get($r, 'cutting', 'nije navedeno');
    $server_note = dc_get($r, 'server_note', '');
    $ingredients = dc_get($r, 'ingredients_10kg', []);
    $liquids = dc_get($r, 'liquids', []);
    $process = dc_get($r, 'process', []);
    $evidence = dc_get($r, 'evidence', []);

    $category = 'privatni radni receptni zapis';
    if (mb_stripos($integration_status, 'PROCESS_CONFIRMED', 0, 'UTF-8') !== false) {
        $category = 'procesno potvr?en privatni draft';
    } elseif (mb_stripos($integration_status, 'CATEGORY_CONFIRMED', 0, 'UTF-8') !== false) {
        $category = 'kategorijski potvr?en privatni draft';
    }

    $sections = [];

    $sections['identity'] =
"## 1. Identitet recepta
- Kanonski naziv: {$title}
- Izvorni / RAW naziv: {$raw_title}
- Dr?ava: {$country}
- Regija: {$region}
- Mikroregija: {$region}
- Tip proizvoda: {$product_type}
- Kategorija: {$category}
- Status: privatni web draft v1.7.2 ? nije za javnu objavu
- Source recipe ID: {$source_id}
- Napomena: zapis je pripremljen za privatni renderer-test i zavr?nu ljudsku provjeru.";

    $sections['short_description'] =
"## 2. Kratki opis
{$title} je privatni radni zapis u drycured.com receptnom sustavu. U javni prikaz smije i?i tek nakon zavr?ne provjere izvora, sastojaka, tehnolo?kih parametara i pravnog statusa naziva.";

    $sections['regional_identity'] =
"## 3. Regionalni identitet
- Dr?ava: {$country}
- Regija / kontekst: {$region}
- Procesna obitelj: {$process_family}

{$source_comparison}";

    $sections['batch'] =
"## 4. Standardna ?ar?a
- Osnovna ?ar?a: 10,00 kg osnovne sirovine gdje je izvor dopu?tao prera?un.
- Status prera?una: privatni radni prera?un; nije javno odobreno.
- Napomena: kod cijelih komada masa se vodi prema komadu, a ne prema kobasi?arskom modelu.";

    $sections['meat_composition'] =
"## 5. Mesni sastav i anatomski dijelovi
" . dc_table_ingredients($ingredients);

    $sections['ingredients'] =
"## 6. Sastojci i za?ini
" . dc_table_ingredients($ingredients) . "

### Teku?ine
" . dc_table_liquids($liquids);

    $sections['casings'] =
"## 7. Crijeva / omota?i
{$casing}";

    $sections['garlic'] =
"## 8. ?e?njak
{$garlic}";

    $sections['tools'] =
"## 9. Oprema i alati
- Vaga, no?evi, posude, termometar i higrometar.
- Za kobasice: mlin za meso, punilica, crijeva/omota?i i igla za probadanje.
- Za cijele komade: posuda za soljenje, re?etka za ocje?ivanje, kuka/konop, prostor za su?enje i zrenje.";

    $sections['meat_preparation'] =
"## 10. Priprema mesa
Sirovinu dr?ati hladnom, raditi ?isto i odvojiti sve sumnjive dijelove. Kod cijelih komada posebno kontrolirati miris uz kost, a kod kobasica temperaturu mesa i masno?e.";

    $sections['grinding_cutting'] =
"## 11. Mljevenje / rezanje
{$cutting}";

    $sections['mixing_resting'] =
"## 12. Mije?anje i odle?avanje
" . dc_filter_process($process, ['mije', 'odle?', 'odlez', 'salamu', 'soljen', 'salamuren'], "- Nije posebno navedeno; ostaje za zavr?nu tehnolo?ku provjeru.");

    $sections['stuffing_tying'] =
"## 13. Punjenje i vezanje
" . dc_filter_process($process, ['pun', 'crijev', 'vez', 'oblik'], "- Nije primjenjivo ili nije posebno navedeno.");

    $sections['predrying'] =
"## 14. Predsu?enje / po?etna fermentacija
" . dc_filter_process($process, ['predsu', 'ferment', 'umr', 'su?enje povr?ine', 'povr?in'], "- Nije posebno navedeno; provjeriti prema tipu proizvoda.");

    $sections['smoking'] =
"## 15. Dimljenje
" . dc_filter_process($process, ['dim', 'smok', 'rauch'], "- Dimljenje nije navedeno ili nije obavezno za ovaj zapis.");

    $sections['drying_aging'] =
"## 16. Su?enje i zrenje
" . dc_filter_process($process, ['su?', 'sus', 'zren', 'sazr', 'aging', 'dry'], "- Su?enje/zrenje nije dovoljno razra?eno; potrebna zavr?na provjera.");

    $sections['mistakes_solutions'] =
"## 17. Naj?e??e gre?ke i rje?enja
| Problem | Mogu?i uzrok | Konkretno rje?enje |
|---|---|---|
| Nejasan javni izvor za exact naziv | RAW naziv nije potpuno potvr?en | Koristiti samo potvr?eni kategorijski naziv dok se ne prona?e bolji javni izvor |
| Prebrzo su?enje povr?ine | Prejak protok zraka ili preniska vlaga | Smanjiti propuh, stabilizirati vlagu i produljiti ravnomjerno zrenje |
| Nejasna koli?ina soli/nitrita | Javni izvor ne daje receptnu specifikaciju | Ne objavljivati koli?inu javno dok se ne potvrdi pouzdanim izvorom |
| Nejasna crijeva ili omota? | Izvor navodi samo kategoriju proizvoda | Ostaviti kao NEEDS_TECH_REVIEW ili dopuniti nakon dodatnog izvora |
| Neodgovaraju?i naziv jela umjesto proizvoda | RAW zapis uklju?uje prilog ili serviranje | U naslovu zadr?ati samo stvarni proizvod, a prilog premjestiti u poslu?ivanje ili izbaciti |";

    $sections['serving_storage'] =
"## 18. Poslu?ivanje i ?uvanje
- Poslu?ivanje: tanko rezano kod cijelih komada; kod kobasica prema tipu proizvoda.
- ?uvanje: hladno, suho i tamno; nakon rezanja za?tititi reznu povr?inu.
- Javna objava nije dopu?tena dok se ne zavr?i zavr?na provjera.";

    $sections['calculator_status'] =
"## 19. Kalkulator status
- Spremno za kalkulator: NE
- Razlog: privatni radni draft; koli?ine i tehnolo?ki parametri nisu javno odobreni.
- Sljede?i korak: ru?na provjera sastojaka, soli/nitrita, crijeva, procesa i sigurnosnog statusa.";

    $sections['traceability'] =
"## 20. Sljedivost izvora
" . dc_evidence_table($evidence, $source_id) . "

### Napomena za server
{$server_note}";

    return $sections;
}

function dc_full_markdown($sections) {
    return implode("\n\n", array_values($sections)) . "\n";
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
fputcsv($fh, ['post_id','title','source_recipe_id','action','notes']);

$total = 0;
$updated = 0;
$blocked = 0;
$errors = 0;

foreach ($posts as $post_id) {
    $total++;
    $post = get_post($post_id);
    $raw = get_post_meta($post_id, 'drycured_input_raw_json', true);
    $r = json_decode($raw, true);

    if (!is_array($r)) {
        $errors++;
        fputcsv($fh, [$post_id, $post ? $post->post_title : '', '', 'ERROR', 'Cannot decode drycured_input_raw_json']);
        continue;
    }

    if (dc_bool_true(dc_get($r, 'ready_for_import', false)) || dc_bool_true(dc_get($r, 'public_publish_allowed', false))) {
        $blocked++;
        fputcsv($fh, [$post_id, $post->post_title, dc_get($r, 'source_recipe_id'), 'BLOCKED', 'Unsafe ready/publish flag']);
        continue;
    }

    $sections = dc_build_sections($r);
    $full = dc_full_markdown($sections);
    $source_id = dc_get($r, 'source_recipe_id', 'UNKNOWN');
    $title = dc_get($r, 'web_title', dc_get($r, 'raw_title', $post->post_title));
    $country = dc_get($r, 'country');
    $region = dc_get($r, 'region');
    $ptype = dc_get($r, 'product_type');
    $integration = dc_get($r, 'integration_status');

    if (!$execute) {
        fputcsv($fh, [$post_id, $title, $source_id, 'DRY_RUN_ONLY', 'Would map canonical renderer meta']);
        continue;
    }

    wp_update_post([
        'ID' => $post_id,
        'post_status' => 'private',
        'post_content' => wp_slash($full),
    ]);

    update_post_meta($post_id, '_dry_recipe_full_markdown', $full);
    update_post_meta($post_id, '_dry_recipe_sections', wp_json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    update_post_meta($post_id, '_dry_recipe_id', $source_id);
    update_post_meta($post_id, 'dry_recipe_code', $source_id);
    update_post_meta($post_id, 'recipe_id', $source_id);
    update_post_meta($post_id, '_dry_country', $country);
    update_post_meta($post_id, '_dry_region', $region);
    update_post_meta($post_id, '_dry_microregion', $region);
    update_post_meta($post_id, '_dry_product_type', $ptype);
    update_post_meta($post_id, '_dry_category', 'privatni web draft');
    update_post_meta($post_id, '_dry_source', 'DRYCURED_9_INTEGRATED_WEB_RECIPE_INPUT_v1_7');
    update_post_meta($post_id, '_dry_public_ready', 'no');
    update_post_meta($post_id, '_dry_calculator_ready', 'no');
    update_post_meta($post_id, '_dry_canonical_status', 'private_web_draft_v1_7_2');
    update_post_meta($post_id, '_dry_canonical_batch', 'DRYCURED_9_INTEGRATED_WEB_RECIPE_INPUT_v1_7');
    update_post_meta($post_id, '_dry_recipe_import_version', 'v1.7.2');
    update_post_meta($post_id, '_dry_recipe_source_audit_status', $integration);
    update_post_meta($post_id, '_dry_recipe_source_audit_reason', 'Imported as private draft only; final human review required before public use.');
    update_post_meta($post_id, 'drycured_renderer_mapping_version', 'v1.7.2');
    update_post_meta($post_id, 'drycured_ready_for_import', 'false');
    update_post_meta($post_id, 'drycured_public_publish_allowed', 'false');
    update_post_meta($post_id, 'drycured_requires_final_human_check', 'true');

    $updated++;
    fputcsv($fh, [$post_id, $title, $source_id, 'UPDATED_RENDERER_META_PRIVATE', 'Canonical renderer meta mapped; post remains private']);
}

fclose($fh);

$summary = [];
$summary[] = 'DRYCURED RENDERER META MAPPING v1.7.2';
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
$summary[] = '- _dry_public_ready is set to no.';
$summary[] = '- _dry_calculator_ready is set to no.';
$summary[] = '- ready_for_import remains false.';
$summary[] = '- public_publish_allowed remains false.';

file_put_contents($summary_path, implode("\n", $summary) . "\n");
echo file_get_contents($summary_path);
