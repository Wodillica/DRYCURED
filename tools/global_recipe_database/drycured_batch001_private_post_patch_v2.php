<?php
/**
 * DRYCURED Batch 001 private post patch v2.0.2.
 *
 * Default mode is DRY_RUN. EXECUTE requires:
 * DRYCURED_BATCH001_PATCH_EXECUTE=YES wp eval-file tools/global_recipe_database/drycured_batch001_private_post_patch_v2.php --path=/var/www/html --allow-root
 *
 * This script only patches existing private dry_recipe posts. It never creates
 * posts, never publishes, and refuses to touch public posts.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "Run through WP-CLI eval-file with WordPress loaded.\n");
    exit(1);
}

$repo_root = dirname(__DIR__, 2);
$batch_dir = $repo_root . '/server-reports/recipes/mass-pipeline-v2/batch_001';
$source_input_dir = $repo_root . '/server-reports/recipes/mass-pipeline-v2/source_input';
$source_excerpt_dir = $source_input_dir . '/source_excerpts';
$report_dir = $repo_root . '/server-reports/recipes/mass-pipeline-v2/batch_001_private_patch';

$private_plan_csv = $batch_dir . '/BATCH_001_PRIVATE_IMPORT_PLAN.csv';
$dry_run_csv = $batch_dir . '/BATCH_001_DRY_RUN_TABLE.csv';
$taxonomy_csv = $batch_dir . '/BATCH_001_TAXONOMY_MAPPING.csv';
$clean_rebuild_json = $source_input_dir . '/drycured_recipes_clean_rebuild_v1_2.json';

$execute = getenv('DRYCURED_BATCH001_PATCH_EXECUTE') === 'YES';
$mode = $execute ? 'EXECUTE' : 'DRY_RUN';
$post_type = 'dry_recipe';
$pipeline_version = 'v2.0.2';
$batch_name = 'batch_001';
$allowed_taxonomies = array('dry_country', 'dry_region', 'dry_product_category', 'dry_meat_type', 'dry_process_type');

function dc_b001_array($value) {
    return is_array($value) ? $value : array();
}

function dc_b001_csv_rows($path) {
    if (!is_readable($path)) {
        return array();
    }
    $handle = fopen($path, 'r');
    if (!$handle) {
        return array();
    }
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        return array();
    }
    $rows = array();
    while (($row = fgetcsv($handle)) !== false) {
        $item = array();
        foreach (dc_b001_array($header) as $index => $column) {
            $item[$column] = isset($row[$index]) ? $row[$index] : '';
        }
        $rows[] = $item;
    }
    fclose($handle);
    return $rows;
}

function dc_b001_write_csv($path, $rows, $header) {
    $dir = dirname($path);
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        fwrite(STDERR, "Cannot create report directory: {$dir}\n");
        exit(1);
    }
    $handle = fopen($path, 'w');
    if (!$handle) {
        fwrite(STDERR, "Cannot write CSV: {$path}\n");
        exit(1);
    }
    fputcsv($handle, $header);
    foreach (dc_b001_array($rows) as $row) {
        $line = array();
        foreach ($header as $column) {
            $line[] = $row[$column] ?? '';
        }
        fputcsv($handle, $line);
    }
    fclose($handle);
}

function dc_b001_group_by($rows, $key) {
    $grouped = array();
    foreach (dc_b001_array($rows) as $row) {
        $value = $row[$key] ?? '';
        if ($value === '') {
            continue;
        }
        if (!isset($grouped[$value])) {
            $grouped[$value] = array();
        }
        $grouped[$value][] = $row;
    }
    return $grouped;
}

function dc_b001_index_by($rows, $key) {
    $indexed = array();
    foreach (dc_b001_array($rows) as $row) {
        $value = $row[$key] ?? '';
        if ($value !== '') {
            $indexed[$value] = $row;
        }
    }
    return $indexed;
}

function dc_b001_slugify($text) {
    $text = remove_accents((string) $text);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim((string) $text, '-');
    return $text !== '' ? $text : 'not-specified';
}

function dc_b001_load_json_file($path) {
    if (!is_readable($path)) {
        return null;
    }
    $content = file_get_contents($path);
    if ($content === false || $content === '') {
        return null;
    }
    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : null;
}

function dc_b001_clean_rebuild_index($path) {
    $decoded = dc_b001_load_json_file($path);
    $indexed = array();
    foreach (dc_b001_array($decoded) as $row) {
        $recipe_id = $row['recipe_id'] ?? ($row['id'] ?? '');
        if ($recipe_id !== '') {
            $indexed[$recipe_id] = $row;
        }
    }
    return $indexed;
}

function dc_b001_source_excerpt($source_excerpt_dir, $record_number) {
    $number = (int) $record_number;
    if ($number <= 0) {
        return array();
    }
    $path = $source_excerpt_dir . '/R' . str_pad((string) $number, 3, '0', STR_PAD_LEFT) . '_source_excerpt.json';
    $decoded = dc_b001_load_json_file($path);
    return is_array($decoded) ? array($path, $decoded) : array($path, array());
}

function dc_b001_markdown_from_source($recipe_id, $record_number, $source_excerpt_dir, $clean_index) {
    list($excerpt_path, $excerpt) = dc_b001_source_excerpt($source_excerpt_dir, $record_number);
    $clean = $clean_index[$recipe_id] ?? array();
    $markdown = $excerpt['full_markdown'] ?? ($clean['full_markdown'] ?? '');
    $source_file = $excerpt['source_file'] ?? ($clean['source_file'] ?? '');
    $raw = $excerpt ? $excerpt : $clean;
    return array(
        'markdown' => (string) $markdown,
        'source_file' => (string) $source_file,
        'source_excerpt_path' => $excerpt_path,
        'raw_json' => $raw,
    );
}


function dc_b001_flatten_terms_text($terms_by_taxonomy) {
    $parts = array();
    foreach (dc_b001_array($terms_by_taxonomy) as $taxonomy => $terms) {
        $parts[] = (string) $taxonomy;
        foreach (dc_b001_array($terms) as $term) {
            $parts[] = (string) $term;
        }
    }
    return implode(' ', $parts);
}

function dc_b001_detect_whole_cut_candidate($title, $expected_slug, $terms_by_taxonomy, $markdown) {
    $haystack = (string) $title . ' ' . (string) $expected_slug . ' ' . dc_b001_flatten_terms_text($terms_by_taxonomy);
    if (preg_match('/cijeli\s*komad|whole[\s_-]*cut|vrat|file|but|rebra|lomo|coppa|capocollo|basturma|pastourma|pastourmas|pastrama|bündner|bundner|lountza|apohti|hangikj|elenski|elena\s+filet|vyrezka|hateclett/iu', $haystack)) {
        return true;
    }

    $md = (string) $markdown;
    if (preg_match('/cijeli\s+komad(?![[:alpha:]])|komad\s+bez\s+kosti|svinjske\s+vratine|svinjskog\s+filea|goveđi\s+but|govedi\s+but|janjeti\s+but|sušeni\s+vrat|suseni\s+vrat/iu', $md)) {
        return true;
    }

    return false;
}

function dc_b001_source_gate_errors($recipe_id, $title, $expected_slug, $source, $terms_by_taxonomy) {
    $errors = array();
    $markdown = (string) ($source['markdown'] ?? '');
    $source_file = (string) ($source['source_file'] ?? '');
    $raw_json = $source['raw_json'] ?? array();

    if ($markdown === '') {
        $errors[] = 'source_markdown_empty';
    }

    if (strlen($markdown) < 1000) {
        $errors[] = 'source_markdown_too_short';
    }

    if (!preg_match('/sastojci|sirovine|meso|sol/iu', $markdown)) {
        $errors[] = 'source_missing_ingredients_marker';
    }

    if (!preg_match('/postupak|priprema|proces|sušenje|susenje|zrenje|dimljenje|soljenje/iu', $markdown)) {
        $errors[] = 'source_missing_process_marker';
    }

    $bad_false_product_patterns = array(
        'false_seafood_marker' => '/riba\/morski\s+proizvodi|morski\s+proizvodi|neptunov\s+dar|planinsko-morski|trogirski\s+zephyr|makarski\s+jugo/iu',
        'generic_placeholder_marker' => '/mesna\s+sirovina\s+prema\s+receptu|začini\s+prema\s+receptu|zacini\s+prema\s+receptu|postupak\s+prema\s+receptu/iu',
        'internal_note_marker' => '/privatni\s+radni\s+recept|masovni\s+unos|treba\s+ga\s+završno\s+uskladiti|treba\s+ga\s+zavrsno\s+uskladiti/iu',
    );

    foreach ($bad_false_product_patterns as $code => $pattern) {
        if (preg_match($pattern, $markdown) || preg_match($pattern, $source_file)) {
            $errors[] = $code;
        }
    }

    $is_whole_cut = dc_b001_detect_whole_cut_candidate($title, $expected_slug, $terms_by_taxonomy, $markdown);
    if ($is_whole_cut) {
        if (preg_match('/mljevenje|samljeti|nadjev|miješanje\s+nadjeva|mijesanje\s+nadjeva|punjenje|napuniti.{0,80}crijev|crijeva|crijevo|omotač|omotac/iu', $markdown)) {
            $errors[] = 'whole_cut_contains_sausage_process_or_casing';
        }
    }

    if (!is_array($raw_json) || !$raw_json) {
        $errors[] = 'source_raw_json_missing';
    }

    return array_values(array_unique($errors));
}

function dc_b001_unique_posts_by_id($posts) {
    $unique = array();
    foreach (dc_b001_array($posts) as $post) {
        if (!isset($post->ID)) {
            continue;
        }
        $unique[(int) $post->ID] = $post;
    }
    return array_values($unique);
}

function dc_b001_find_posts_by_meta($meta_key, $meta_value, $post_type) {
    if ((string) $meta_value === '') {
        return array();
    }

    return dc_b001_unique_posts_by_id(get_posts(array(
        'post_type' => $post_type,
        'post_status' => array('private', 'publish', 'draft', 'pending', 'future'),
        'meta_key' => $meta_key,
        'meta_value' => $meta_value,
        'numberposts' => -1,
        'suppress_filters' => false,
    )));
}

function dc_b001_find_posts_by_slug($slug, $post_type) {
    if ((string) $slug === '') {
        return array();
    }

    return dc_b001_unique_posts_by_id(get_posts(array(
        'name' => $slug,
        'post_type' => $post_type,
        'post_status' => array('private', 'publish', 'draft', 'pending', 'future'),
        'numberposts' => -1,
        'suppress_filters' => false,
    )));
}



function dc_b001_web_structure_regression_error($before_text, $after_text) {
    $before = (string) $before_text;
    $after = (string) $after_text;

    $before_has_drycured_structure = preg_match('/Radni sažetak|Radni sazetak|Gotovo je kad|Najčešće greške|Najcesce greske|Sigurnosne smjernice/iu', $before);
    $after_has_drycured_structure = preg_match('/Radni sažetak|Radni sazetak|Gotovo je kad|Najčešće greške|Najcesce greske|Sigurnosne smjernice/iu', $after);

    if ($before_has_drycured_structure && !$after_has_drycured_structure) {
        return 'web_structure_regression_refused';
    }

    return '';
}

function dc_b001_content_regression_error($before_bytes, $after_bytes) {
    $before = (int) $before_bytes;
    $after = (int) $after_bytes;

    if ($before >= 1000 && $after > 0 && $after < ($before * 0.80) && ($before - $after) >= 500) {
        return 'content_regression_refused';
    }

    return '';
}

function dc_b001_find_existing_post_for_recipe($recipe_id, $slug, $post_type) {
    $meta_keys = array('_dry_recipe_id', 'recipe_id', 'dry_recipe_code', 'drycured_source_recipe_id');
    $ambiguous_meta_posts = array();

    foreach ($meta_keys as $meta_key) {
        $posts = dc_b001_find_posts_by_meta($meta_key, $recipe_id, $post_type);
        if (count($posts) === 1) {
            return $posts;
        }
        if (count($posts) > 1) {
            foreach ($posts as $post) {
                $ambiguous_meta_posts[(int) $post->ID] = $post;
            }
        }
    }

    $slug_posts = dc_b001_find_posts_by_slug($slug, $post_type);
    if (count($slug_posts) === 1) {
        return $slug_posts;
    }

    if ($ambiguous_meta_posts) {
        return array_values($ambiguous_meta_posts);
    }

    return $slug_posts;
}

function dc_b001_taxonomy_terms_for_recipe($recipe_id, $taxonomy_rows, $allowed_taxonomies) {
    $terms_by_taxonomy = array();
    foreach (dc_b001_array($taxonomy_rows[$recipe_id] ?? array()) as $row) {
        $taxonomy = $row['taxonomy'] ?? '';
        $term = trim((string) ($row['term_name'] ?? ''));
        if (!in_array($taxonomy, $allowed_taxonomies, true)) {
            continue;
        }
        if ($term === '' || $term === 'not_specified_in_source' || $term === 'needs_editorial_review') {
            continue;
        }
        if (!isset($terms_by_taxonomy[$taxonomy])) {
            $terms_by_taxonomy[$taxonomy] = array();
        }
        $terms_by_taxonomy[$taxonomy][] = $term;
    }
    foreach ($terms_by_taxonomy as $taxonomy => $terms) {
        $terms_by_taxonomy[$taxonomy] = array_values(array_unique($terms));
    }
    return $terms_by_taxonomy;
}

function dc_b001_first_term($terms_by_taxonomy, $taxonomy, $fallback = 'not_specified_in_source') {
    $terms = $terms_by_taxonomy[$taxonomy] ?? array();
    return $terms ? (string) $terms[0] : $fallback;
}

function dc_b001_terms_exist($terms_by_taxonomy) {
    $missing = array();
    foreach ($terms_by_taxonomy as $taxonomy => $terms) {
        foreach ($terms as $term) {
            if (!term_exists($term, $taxonomy)) {
                $missing[] = $taxonomy . ':' . $term;
            }
        }
    }
    return $missing;
}

function dc_b001_meta_payload($recipe_id, $dry_row, $source, $terms_by_taxonomy, $pipeline_version, $batch_name) {
    $markdown = (string) ($source['markdown'] ?? '');
    $sections = array(
        'full_markdown' => $markdown,
        'source_excerpt_file' => basename((string) ($source['source_excerpt_path'] ?? '')),
        'source_file' => (string) ($source['source_file'] ?? ''),
        'raw_json' => $source['raw_json'] ?? array(),
    );
    return array(
        '_dry_recipe_id' => $recipe_id,
        'dry_recipe_code' => $recipe_id,
        'recipe_id' => $recipe_id,
        '_dry_recipe_full_markdown' => $markdown,
        '_dry_recipe_sections' => wp_json_encode($sections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        '_dry_country' => dc_b001_first_term($terms_by_taxonomy, 'dry_country'),
        '_dry_region' => dc_b001_first_term($terms_by_taxonomy, 'dry_region'),
        '_dry_microregion' => 'not_specified_in_source',
        '_dry_product_type' => dc_b001_first_term($terms_by_taxonomy, 'dry_product_category'),
        '_dry_category' => dc_b001_first_term($terms_by_taxonomy, 'dry_product_category'),
        '_dry_source' => (string) ($source['source_file'] ?? ''),
        'drycured_input_raw_json' => wp_json_encode($source['raw_json'] ?? array(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'drycured_mass_pipeline_version' => $pipeline_version,
        'drycured_mass_pipeline_batch' => $batch_name,
        '_dry_public_ready' => 'no',
        '_dry_archive_ready' => 'no',
        '_dry_calculator_ready' => 'no',
        'drycured_public_publish_allowed' => 'false',
        'drycured_requires_final_human_check' => 'true',
    );
}

function dc_b001_private_count_by_meta($status, $version) {
    $query = new WP_Query(array(
        'post_type' => 'dry_recipe',
        'post_status' => $status,
        'meta_key' => 'drycured_mass_pipeline_version',
        'meta_value' => $version,
        'fields' => 'ids',
        'posts_per_page' => -1,
        'no_found_rows' => false,
    ));
    return (int) $query->post_count;
}

$plan_rows = dc_b001_csv_rows($private_plan_csv);
$dry_rows = dc_b001_csv_rows($dry_run_csv);
$taxonomy_rows = dc_b001_group_by(dc_b001_csv_rows($taxonomy_csv), 'recipe_id');
$dry_by_recipe = dc_b001_index_by($dry_rows, 'recipe_id');
$clean_index = dc_b001_clean_rebuild_index($clean_rebuild_json);

if ($execute && !$plan_rows) {
    fwrite(STDERR, "EXECUTE refused: missing BATCH_001_PRIVATE_IMPORT_PLAN.csv\n");
    exit(1);
}

if (!$plan_rows) {
    $plan_rows = array();
    foreach ($dry_rows as $row) {
        $plan_rows[] = array(
            'recipe_id' => $row['recipe_id'] ?? '',
            'source_title' => $row['title'] ?? '',
            'expected_slug' => $row['slug'] ?? '',
            'planned_action' => $row['dry_run_action'] ?? '',
        );
    }
}


$preflight_error_rows = array();

foreach (dc_b001_array($plan_rows) as $plan) {
    $recipe_id = $plan['recipe_id'] ?? '';
    $dry_row = $dry_by_recipe[$recipe_id] ?? array();
    $title = $plan['source_title'] ?? ($dry_row['title'] ?? '');
    $expected_slug = $plan['expected_slug'] ?? ($dry_row['slug'] ?? '');
    $record_number = $dry_row['record_number'] ?? '';
    $planned_action = $plan['planned_action'] ?? ($dry_row['dry_run_action'] ?? '');

    if ($recipe_id === '' || $expected_slug === '') {
        $preflight_error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'missing_recipe_id_or_expected_slug', 'post_id' => '', 'notes' => '');
        continue;
    }

    if ($planned_action !== '' && $planned_action !== 'WOULD_CREATE_PRIVATE') {
        continue;
    }

    $posts = dc_b001_find_existing_post_for_recipe($recipe_id, $expected_slug, $post_type);
    if (count($posts) !== 1) {
        $preflight_error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'preflight_post_match_count_not_one', 'post_id' => '', 'notes' => 'candidate_count=' . count($posts));
        continue;
    }

    $post = $posts[0];
    $post_id = (int) $post->ID;

    if ($post->post_status !== 'private') {
        $preflight_error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'preflight_refused_non_private_post', 'post_id' => $post_id, 'notes' => 'status=' . $post->post_status);
        continue;
    }

    $source = dc_b001_markdown_from_source($recipe_id, $record_number, $source_excerpt_dir, $clean_index);
    $terms_by_taxonomy = dc_b001_taxonomy_terms_for_recipe($recipe_id, $taxonomy_rows, $allowed_taxonomies);
    $source_gate_errors = dc_b001_source_gate_errors($recipe_id, $title, $expected_slug, $source, $terms_by_taxonomy);

    if ($source_gate_errors) {
        $preflight_error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'preflight_source_gate_failed', 'post_id' => $post_id, 'notes' => implode('|', $source_gate_errors));
        continue;
    }

    $before_content_text = (string) $post->post_content;
    $after_content_text = (string) ($source['markdown'] ?? '');
    $before_content_bytes = strlen($before_content_text);
    $after_content_bytes = strlen($after_content_text);

    $content_regression_error = dc_b001_content_regression_error($before_content_bytes, $after_content_bytes);
    if ($content_regression_error !== '') {
        $preflight_error_rows[] = array(
            'recipe_id' => $recipe_id,
            'expected_slug' => $expected_slug,
            'error' => 'preflight_content_regression_refused',
            'post_id' => $post_id,
            'notes' => 'before_content_bytes=' . $before_content_bytes . '|after_content_bytes=' . $after_content_bytes
        );
        continue;
    }

    $web_structure_error = dc_b001_web_structure_regression_error($before_content_text, $after_content_text);
    if ($web_structure_error !== '') {
        $preflight_error_rows[] = array(
            'recipe_id' => $recipe_id,
            'expected_slug' => $expected_slug,
            'error' => 'preflight_web_structure_regression_refused',
            'post_id' => $post_id,
            'notes' => 'existing_drycured_structure_would_be_replaced_by_raw_source'
        );
        continue;
    }
}

$preflight_error_header = array('recipe_id', 'expected_slug', 'error', 'post_id', 'notes');
dc_b001_write_csv($report_dir . '/BATCH_001_PATCH_PREFLIGHT_ERRORS.csv', $preflight_error_rows, $preflight_error_header);

if ($execute && $preflight_error_rows) {
    fwrite(STDERR, "EXECUTE refused: preflight source gate failed for " . count($preflight_error_rows) . " row(s). See BATCH_001_PATCH_PREFLIGHT_ERRORS.csv\n");
    exit(1);
}


$dry_report_rows = array();
$execute_report_rows = array();
$error_rows = array();
$patched = 0;
$would_patch = 0;
$errors = 0;
$skipped = 0;
$public_refusals = 0;

foreach (dc_b001_array($plan_rows) as $plan) {
    $recipe_id = $plan['recipe_id'] ?? '';
    $dry_row = $dry_by_recipe[$recipe_id] ?? array();
    $title = $plan['source_title'] ?? ($dry_row['title'] ?? '');
    $expected_slug = $plan['expected_slug'] ?? ($dry_row['slug'] ?? '');
    $record_number = $dry_row['record_number'] ?? '';
    $planned_action = $plan['planned_action'] ?? ($dry_row['dry_run_action'] ?? '');
    if ($recipe_id === '' || $expected_slug === '') {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'missing_recipe_id_or_expected_slug', 'post_id' => '', 'notes' => '');
        continue;
    }
    if ($planned_action !== '' && $planned_action !== 'WOULD_CREATE_PRIVATE') {
        $skipped++;
        continue;
    }

    $posts = dc_b001_find_existing_post_for_recipe($recipe_id, $expected_slug, $post_type);
    if (count($posts) !== 1) {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'post_match_count_not_one', 'post_id' => '', 'notes' => 'candidate_count=' . count($posts));
        continue;
    }
    $post = $posts[0];
    $post_id = (int) $post->ID;
    if ($post->post_status !== 'private') {
        $errors++;
        if ($post->post_status === 'publish') {
            $public_refusals++;
        }
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'refused_non_private_post', 'post_id' => $post_id, 'notes' => 'status=' . $post->post_status);
        continue;
    }

    $source = dc_b001_markdown_from_source($recipe_id, $record_number, $source_excerpt_dir, $clean_index);
    $markdown = (string) ($source['markdown'] ?? '');
    if (strlen($markdown) < 1000) {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'source_markdown_too_short', 'post_id' => $post_id, 'notes' => 'bytes=' . strlen($markdown));
        continue;
    }

    $terms_by_taxonomy = dc_b001_taxonomy_terms_for_recipe($recipe_id, $taxonomy_rows, $allowed_taxonomies);
    $missing_terms = dc_b001_terms_exist($terms_by_taxonomy);
    if ($missing_terms) {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'taxonomy_term_missing', 'post_id' => $post_id, 'notes' => implode('|', $missing_terms));
        continue;
    }

    $source_gate_errors = dc_b001_source_gate_errors($recipe_id, $title, $expected_slug, $source, $terms_by_taxonomy);
    if ($source_gate_errors) {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'source_gate_failed', 'post_id' => $post_id, 'notes' => implode('|', $source_gate_errors));
        continue;
    }

    $meta = dc_b001_meta_payload($recipe_id, $dry_row, $source, $terms_by_taxonomy, $pipeline_version, $batch_name);
    $before_content_bytes = strlen((string) $post->post_content);
    $current_country_terms = wp_get_object_terms($post_id, 'dry_country', array('fields' => 'names'));
    $before_country = is_wp_error($current_country_terms) ? '' : implode('|', dc_b001_array($current_country_terms));
    $target_country = dc_b001_first_term($terms_by_taxonomy, 'dry_country');

    if ($before_country !== '' && $target_country !== '' && $before_country !== $target_country) {
        $errors++;
        $error_rows[] = array(
            'recipe_id' => $recipe_id,
            'expected_slug' => $expected_slug,
            'error' => 'taxonomy_country_mismatch_refused',
            'post_id' => $post_id,
            'notes' => 'before_country=' . $before_country . '|target_country=' . $target_country
        );
        continue;
    }

    $content_regression_error = dc_b001_content_regression_error($before_content_bytes, strlen($markdown));
    if ($content_regression_error !== '') {
        $errors++;
        $error_rows[] = array(
            'recipe_id' => $recipe_id,
            'expected_slug' => $expected_slug,
            'error' => 'content_regression_refused',
            'post_id' => $post_id,
            'notes' => 'before_content_bytes=' . $before_content_bytes . '|after_content_bytes=' . strlen($markdown)
        );
        continue;
    }

    $web_structure_error = dc_b001_web_structure_regression_error((string) $post->post_content, $markdown);
    if ($web_structure_error !== '') {
        $errors++;
        $error_rows[] = array(
            'recipe_id' => $recipe_id,
            'expected_slug' => $expected_slug,
            'error' => 'web_structure_regression_refused',
            'post_id' => $post_id,
            'notes' => 'existing_drycured_structure_would_be_replaced_by_raw_source'
        );
        continue;
    }

    $row_report = array(
        'recipe_id' => $recipe_id,
        'post_id' => $post_id,
        'title' => $title,
        'expected_slug' => $expected_slug,
        'current_status' => $post->post_status,
        'before_content_bytes' => $before_content_bytes,
        'after_content_bytes' => strlen($markdown),
        'before_country_terms' => $before_country,
        'target_country_terms' => $target_country,
        'meta_keys_to_update' => implode('|', array_keys($meta)),
        'taxonomies_to_replace' => implode('|', array_keys($terms_by_taxonomy)),
        'execute_mode' => $mode,
        'result' => $execute ? 'PENDING_EXECUTE' : 'WOULD_PATCH_EXISTING_PRIVATE',
        'notes' => '',
    );
    $dry_report_rows[] = $row_report;
    $would_patch++;

    if (!$execute) {
        continue;
    }

    $update_result = wp_update_post(array(
        'ID' => $post_id,
        'post_content' => $markdown,
    ), true);
    if (is_wp_error($update_result)) {
        $errors++;
        $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'wp_update_post_failed', 'post_id' => $post_id, 'notes' => $update_result->get_error_message());
        continue;
    }
    foreach ($meta as $key => $value) {
        update_post_meta($post_id, $key, $value);
    }
    foreach ($terms_by_taxonomy as $taxonomy => $terms) {
        $term_result = wp_set_object_terms($post_id, $terms, $taxonomy, false);
        if (is_wp_error($term_result)) {
            $errors++;
            $error_rows[] = array('recipe_id' => $recipe_id, 'expected_slug' => $expected_slug, 'error' => 'wp_set_object_terms_failed', 'post_id' => $post_id, 'notes' => $taxonomy . ':' . $term_result->get_error_message());
            continue 2;
        }
    }
    $patched++;
    $row_report['result'] = 'PATCHED_EXISTING_PRIVATE';
    $execute_report_rows[] = $row_report;
}

$report_header = array('recipe_id', 'post_id', 'title', 'expected_slug', 'current_status', 'before_content_bytes', 'after_content_bytes', 'before_country_terms', 'target_country_terms', 'meta_keys_to_update', 'taxonomies_to_replace', 'execute_mode', 'result', 'notes');
$error_header = array('recipe_id', 'expected_slug', 'error', 'post_id', 'notes');
dc_b001_write_csv($report_dir . '/BATCH_001_PATCH_DRY_RUN.csv', $dry_report_rows, $report_header);
dc_b001_write_csv($report_dir . '/BATCH_001_PATCH_EXECUTE.csv', $execute_report_rows, $report_header);
dc_b001_write_csv($report_dir . '/BATCH_001_PATCH_ERRORS.csv', $error_rows, $error_header);

$private_v202_count = dc_b001_private_count_by_meta('private', $pipeline_version);
$publish_v202_count = dc_b001_private_count_by_meta('publish', $pipeline_version);
$post_3029 = get_post(3029);
$post_3029_markdown = $post_3029 ? (string) get_post_meta(3029, '_dry_recipe_full_markdown', true) : '';
$post_3029_country = $post_3029 ? wp_get_object_terms(3029, 'dry_country', array('fields' => 'names')) : array();
$post_3029_country_text = is_wp_error($post_3029_country) ? 'WP_ERROR' : implode('|', dc_b001_array($post_3029_country));

$summary = array(
    '# BATCH 001 Private Post Patch v2.0.2',
    '',
    'mode: ' . $mode,
    'plan_rows: ' . count($plan_rows),
    'would_patch_existing_private: ' . $would_patch,
    'patched_existing_private: ' . $patched,
    'skipped: ' . $skipped,
    'errors: ' . $errors,
    'public_refusals: ' . $public_refusals,
    'private_posts_with_drycured_mass_pipeline_version_v2_0_2: ' . $private_v202_count,
    'publish_posts_with_drycured_mass_pipeline_version_v2_0_2: ' . $publish_v202_count,
    'post_3029_status: ' . ($post_3029 ? $post_3029->post_status : 'missing'),
    'post_3029_content_bytes: ' . ($post_3029 ? strlen((string) $post_3029->post_content) : 0),
    'post_3029_full_markdown_bytes: ' . strlen($post_3029_markdown),
    'post_3029_dry_country_terms: ' . $post_3029_country_text,
    '',
    'Safety:',
    '- no post creation',
    '- existing post_status is not changed',
    '- public posts are refused',
    '- post_title and post_name are not changed',
    '- execute requires DRYCURED_BATCH001_PATCH_EXECUTE=YES',
);
file_put_contents($report_dir . '/BATCH_001_PATCH_SUMMARY.md', implode("\n", $summary) . "\n");

$safety = array(
    'DRYCURED_BATCH001_PRIVATE_POST_PATCH_V2',
    'mode=' . $mode,
    'wordpress_write_performed=' . ($execute ? 'YES_PRIVATE_PATCH_ONLY' : 'NO'),
    'post_creation_allowed=NO',
    'public_publish_attempts=0',
    'post_status_change_allowed=NO',
    'public_post_touch_allowed=NO',
    'renderer_changed=NO',
    'shortcode_changed=NO',
    'css_changed=NO',
);
file_put_contents($report_dir . '/BATCH_001_PATCH_SAFETY_CHECK.txt', implode("\n", $safety) . "\n");

WP_CLI::line('DRYCURED Batch 001 private patch v2.0.2');
WP_CLI::line('mode: ' . $mode);
WP_CLI::line('would_patch_existing_private: ' . $would_patch);
WP_CLI::line('patched_existing_private: ' . $patched);
WP_CLI::line('errors: ' . $errors);
WP_CLI::line('public_refusals: ' . $public_refusals);
WP_CLI::line('private_posts_with_v2.0.2: ' . $private_v202_count);
WP_CLI::line('publish_posts_with_v2.0.2: ' . $publish_v202_count);
WP_CLI::line('wordpress_write_performed: ' . ($execute ? 'YES_PRIVATE_PATCH_ONLY' : 'NO'));
