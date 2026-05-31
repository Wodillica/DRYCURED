<?php
/**
 * Source-lock to WordPress dry_recipe mapping dry-run.
 *
 * Usage:
 *   wp eval-file tools/source_lock_compiler/wp_source_lock_mapping_dry_run.php --path=/var/www/html --allow-root
 *
 * This script only reads WordPress posts/meta and source-lock JSON files. It
 * writes a CSV report under server-reports/recipes and does not update posts,
 * post_status, post meta, or renderer code.
 */

if (!defined('ABSPATH')) {
    fwrite(STDERR, "This script must be run through WP-CLI eval-file with WordPress loaded.\n");
    exit(1);
}

$repo_root = dirname(__DIR__, 2);
$json_dir = $repo_root . '/build/source_locked_json';
$report_dir = $repo_root . '/server-reports/recipes';
$csv_path = $report_dir . '/source_lock_wp_mapping_dry_run_batch30.csv';
$post_type = 'dry_recipe';
$meta_keys = array('recipe_id', 'source_lock_recipe_id', 'dry_recipe_code');

function slwp_normalize_text($value) {
    return trim(wp_strip_all_tags((string) $value));
}

function normalize_title($title) {
    $normalized = slwp_normalize_text($title);
    $normalized = preg_replace('/\s*\([^)]*\)\s*$/u', '', $normalized);
    $normalized = preg_replace('/\s+/u', ' ', trim($normalized));
    if (function_exists('remove_accents')) {
        $normalized = remove_accents($normalized);
    } else {
        $normalized = strtr($normalized, array(
            'Č' => 'C',
            'Ć' => 'C',
            'Đ' => 'D',
            'Š' => 'S',
            'Ž' => 'Z',
            'č' => 'c',
            'ć' => 'c',
            'đ' => 'd',
            'š' => 's',
            'ž' => 'z',
        ));
    }
    if (function_exists('mb_strtolower')) {
        $normalized = mb_strtolower($normalized, 'UTF-8');
    } else {
        $normalized = strtolower($normalized);
    }
    return trim($normalized);
}

function title_compatible($source_title, $current_title) {
    $source = slwp_normalize_text($source_title);
    $current = slwp_normalize_text($current_title);
    if ($source === '' || $current === '') {
        return false;
    }
    if ($source === $current) {
        return true;
    }
    $normalized_source = normalize_title($source);
    $normalized_current = normalize_title($current);
    if ($normalized_source === '' || $normalized_current === '') {
        return false;
    }
    if ($normalized_source === $normalized_current) {
        return true;
    }
    if (strpos($normalized_current, $normalized_source) !== false || strpos($normalized_source, $normalized_current) !== false) {
        return true;
    }
    return false;
}

function slwp_expected_slug($recipe_id) {
    return sanitize_title(strtolower((string) $recipe_id));
}

function slwp_post_row($post) {
    return array(
        'id' => (int) $post->ID,
        'title' => slwp_normalize_text($post->post_title),
        'slug' => (string) $post->post_name,
        'status' => (string) $post->post_status,
    );
}

function slwp_candidate_ids($candidates) {
    return array_map(
        static function ($candidate) {
            return $candidate['id'];
        },
        $candidates
    );
}

function slwp_candidate_note($label, $candidates) {
    if (!$candidates) {
        return '';
    }
    return $label . '=' . implode('|', slwp_candidate_ids($candidates));
}

function slwp_select_mapping($recipe_id, $source_title, $posts, $meta_keys) {
    $expected_slug = slwp_expected_slug($recipe_id);
    $meta_candidates = array();
    $exact_title_candidates = array();
    $normalized_title_candidates = array();
    $slug_safe_candidates = array();
    $rejected_candidates = array();

    foreach ($posts as $post) {
        $row = slwp_post_row($post);
        $row['title_compatible'] = title_compatible($source_title, $row['title']) ? 1 : 0;
        foreach ($meta_keys as $meta_key) {
            $meta_value = get_post_meta($post->ID, $meta_key, true);
            if (trim((string) $meta_value) === (string) $recipe_id) {
                $row['match_meta_key'] = $meta_key;
                $meta_candidates[$post->ID] = $row;
                break;
            }
        }
        if ($source_title !== '' && $row['title'] === $source_title) {
            $exact_title_candidates[$post->ID] = $row;
        } elseif ($source_title !== '' && normalize_title($row['title']) === normalize_title($source_title)) {
            $normalized_title_candidates[$post->ID] = $row;
        }
        if ($expected_slug !== '' && strpos(strtolower($row['slug']), $expected_slug) === 0) {
            if ($row['title_compatible']) {
                $slug_safe_candidates[$post->ID] = $row;
            } else {
                $rejected_candidates[] = 'REJECTED_SLUG_PREFIX_TITLE_MISMATCH: ID=' . $row['id'] . ', title=' . $row['title'] . ', slug=' . $row['slug'];
            }
        }
    }

    $steps = array(
        'meta_recipe_id' => array_values($meta_candidates),
        'exact_title' => array_values($exact_title_candidates),
        'normalized_title' => array_values($normalized_title_candidates),
        'slug_prefix_title_compatible' => array_values($slug_safe_candidates),
    );

    $notes = array();
    foreach ($steps as $label => $candidates) {
        $note = slwp_candidate_note($label, $candidates);
        if ($note !== '') {
            $notes[] = $note;
        }
    }
    $notes = array_merge($notes, $rejected_candidates);

    foreach ($steps as $method => $candidates) {
        if (count($candidates) === 1) {
            $target = $candidates[0];
            $special_notes = slwp_special_notes($recipe_id, $target, $steps);
            if ($special_notes) {
                $notes = array_merge($notes, $special_notes);
            }
            $guard_post_id = slwp_guard_main_post_id($recipe_id);
            if ($guard_post_id && (int) $target['id'] !== $guard_post_id) {
                $notes[] = 'MAIN_POST_GUARD_REQUIRES_REVIEW_' . $guard_post_id;
                return array(
                    'action' => 'AMBIGUOUS_REVIEW',
                    'target' => null,
                    'match_method' => $method,
                    'candidate_count' => count($candidates) + 1,
                    'safe_match' => 0,
                    'title_compatible' => 0,
                    'rejected_candidates' => implode(' | ', $rejected_candidates),
                    'notes' => implode('; ', array_values(array_unique(array_filter($notes)))),
                );
            }
            return array(
                'action' => 'UPDATE_EXISTING',
                'target' => $target,
                'match_method' => $method,
                'candidate_count' => 1,
                'safe_match' => 1,
                'title_compatible' => (int) ($method === 'meta_recipe_id' || !empty($target['title_compatible'])),
                'rejected_candidates' => implode(' | ', $rejected_candidates),
                'notes' => implode('; ', array_values(array_unique(array_filter($notes)))),
            );
        }
        if (count($candidates) > 1) {
            return array(
                'action' => 'AMBIGUOUS_REVIEW',
                'target' => null,
                'match_method' => $method,
                'candidate_count' => count($candidates),
                'safe_match' => 0,
                'title_compatible' => 0,
                'rejected_candidates' => implode(' | ', $rejected_candidates),
                'notes' => implode('; ', array_values(array_unique(array_filter($notes)))),
            );
        }
    }

    $notes = array_merge($notes, slwp_missing_notes($recipe_id));
    return array(
        'action' => 'CREATE_NEW_PRIVATE',
        'target' => null,
        'match_method' => 'none',
        'candidate_count' => 0,
        'safe_match' => 0,
        'title_compatible' => 0,
        'rejected_candidates' => implode(' | ', $rejected_candidates),
        'notes' => implode('; ', array_values(array_unique(array_filter($notes)))),
    );
}

function slwp_guard_main_post_id($recipe_id) {
    $guard_ids = array(
        'HR-SL-001' => 2972,
        'HR-SL-009' => 2980,
    );
    if (!isset($guard_ids[$recipe_id])) {
        return null;
    }
    $post = get_post($guard_ids[$recipe_id]);
    if (!$post || $post->post_type !== 'dry_recipe') {
        return null;
    }
    return (int) $post->ID;
}

function slwp_special_notes($recipe_id, $target, $steps) {
    $notes = array();
    if ($recipe_id === 'HR-SL-001' && (int) $target['id'] !== 2972) {
        $all_ids = array();
        foreach ($steps as $candidates) {
            $all_ids = array_merge($all_ids, slwp_candidate_ids($candidates));
        }
        if (in_array(2972, $all_ids, true)) {
            $notes[] = 'CHECK_MAIN_POST_ID_2972_PRESENT_NOT_SELECTED';
        } else {
            $notes[] = 'CHECK_HR_SL_001_MAIN_POST_ID_2972_NOT_FOUND_IN_CANDIDATES';
        }
    }
    if ($recipe_id === 'HR-SL-009' && (int) $target['id'] !== 2980) {
        $all_ids = array();
        foreach ($steps as $candidates) {
            $all_ids = array_merge($all_ids, slwp_candidate_ids($candidates));
        }
        if (in_array(2980, $all_ids, true)) {
            $notes[] = 'CHECK_MAIN_POST_ID_2980_PRESENT_NOT_SELECTED';
        } else {
            $notes[] = 'CHECK_HR_SL_009_MAIN_POST_ID_2980_NOT_FOUND_IN_CANDIDATES';
        }
    }
    if (in_array($recipe_id, array('HR-SL-022', 'HR-SL-023'), true)) {
        $notes[] = 'SUSPICIOUS_OLD_SLUG_REVIEW_BEFORE_UPDATE';
    }
    return $notes;
}

function slwp_missing_notes($recipe_id) {
    if (in_array($recipe_id, array('HR-SL-022', 'HR-SL-023'), true)) {
        return array('SUSPICIOUS_OLD_SLUG_REVIEW_BEFORE_CREATE');
    }
    return array();
}

if (!is_dir($json_dir)) {
    fwrite(STDERR, "Missing source-lock JSON directory: {$json_dir}\n");
    exit(1);
}

$json_files = glob($json_dir . '/*.source_locked.json');
sort($json_files, SORT_STRING);
if (!$json_files) {
    fwrite(STDERR, "No source-lock JSON files found in: {$json_dir}\n");
    exit(1);
}

$posts = get_posts(array(
    'post_type' => $post_type,
    'post_status' => 'any',
    'numberposts' => -1,
    'orderby' => 'ID',
    'order' => 'ASC',
));

if (!is_dir($report_dir) && !mkdir($report_dir, 0775, true) && !is_dir($report_dir)) {
    fwrite(STDERR, "Cannot create report directory: {$report_dir}\n");
    exit(1);
}

$handle = fopen($csv_path, 'w');
if (!$handle) {
    fwrite(STDERR, "Cannot write CSV report: {$csv_path}\n");
    exit(1);
}

$columns = array(
    'recipe_id',
    'source_title',
    'action',
    'target_post_id',
    'current_title',
    'current_slug',
    'current_status',
    'match_method',
    'candidate_count',
    'safe_match',
    'title_compatible',
    'rejected_candidates',
    'notes',
);
fputcsv($handle, $columns);

$summary = array(
    'total' => 0,
    'UPDATE_EXISTING' => 0,
    'CREATE_NEW_PRIVATE' => 0,
    'AMBIGUOUS_REVIEW' => 0,
    'SKIP' => 0,
    'rejected_slug_prefix_title_mismatch' => 0,
);

WP_CLI::line('recipe_id,source_title,expected_slug,current_wp_match_status,target_post_id,current_status,current_slug,action,match_method,candidate_count,safe_match,title_compatible,rejected_candidates,notes');

foreach ($json_files as $json_file) {
    $raw = file_get_contents($json_file);
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $recipe_id = basename($json_file, '.source_locked.json');
        $row = array($recipe_id, '', 'SKIP', '', '', '', '', 'invalid_json', 0, 0, 0, '', 'INVALID_JSON');
        fputcsv($handle, $row);
        $summary['total']++;
        $summary['SKIP']++;
        continue;
    }

    $recipe_id = (string) ($data['recipe_id'] ?? basename($json_file, '.source_locked.json'));
    $source_title = slwp_normalize_text($data['title'] ?? ($data['metadata']['expected_title'] ?? ''));
    $mapping = slwp_select_mapping($recipe_id, $source_title, $posts, $meta_keys);
    $target = $mapping['target'];
    $target_id = $target ? (string) $target['id'] : '';
    $current_title = $target ? $target['title'] : '';
    $current_slug = $target ? $target['slug'] : '';
    $current_status = $target ? $target['status'] : '';

    $csv_row = array(
        $recipe_id,
        $source_title,
        $mapping['action'],
        $target_id,
        $current_title,
        $current_slug,
        $current_status,
        $mapping['match_method'],
        $mapping['candidate_count'],
        $mapping['safe_match'],
        $mapping['title_compatible'],
        $mapping['rejected_candidates'],
        $mapping['notes'],
    );
    fputcsv($handle, $csv_row);

    $summary['total']++;
    $summary[$mapping['action']]++;
    $summary['rejected_slug_prefix_title_mismatch'] += substr_count($mapping['rejected_candidates'], 'REJECTED_SLUG_PREFIX_TITLE_MISMATCH');

    WP_CLI::line(implode(',', array(
        $recipe_id,
        $source_title,
        slwp_expected_slug($recipe_id),
        $mapping['action'],
        $target_id,
        $current_status,
        $current_slug,
        $mapping['action'],
        $mapping['match_method'],
        $mapping['candidate_count'],
        $mapping['safe_match'],
        $mapping['title_compatible'],
        $mapping['rejected_candidates'],
        $mapping['notes'],
    )));
}

fclose($handle);

WP_CLI::line('');
WP_CLI::line('CSV report: ' . $csv_path);
WP_CLI::line('summary:');
WP_CLI::line('total: ' . $summary['total']);
WP_CLI::line('update_existing: ' . $summary['UPDATE_EXISTING']);
WP_CLI::line('create_new_private: ' . $summary['CREATE_NEW_PRIVATE']);
WP_CLI::line('ambiguous_review: ' . $summary['AMBIGUOUS_REVIEW']);
WP_CLI::line('skip: ' . $summary['SKIP']);
WP_CLI::line('rejected_slug_prefix_title_mismatch: ' . $summary['rejected_slug_prefix_title_mismatch']);
WP_CLI::line('wordpress_update_allowed: no');
