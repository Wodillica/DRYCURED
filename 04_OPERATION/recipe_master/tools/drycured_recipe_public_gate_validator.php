<?php
/**
 * Drycured Recipe Public Gate Validator
 *
 * Read-only validator.
 * Run from WordPress root:
 *   POST_IDS=2060 wp eval-file /root/DRYCURED_GITHUB/04_OPERATION/recipe_master/tools/drycured_recipe_public_gate_validator.php --allow-root
 *
 * Purpose:
 * - block public release when renderer shows fallback/internal/generic text
 * - verify recipe has canonical ID, registry profile and quantity profile
 * - verify noindex / public flags before manual approval
 */

$post_ids_raw = getenv('POST_IDS') ?: '2060';
$post_ids = array_values(array_filter(array_map('intval', preg_split('/\s*,\s*/', $post_ids_raw))));

$forbidden_public_phrases = [
    'prema receptu',
    'treba dopuniti',
    'Postupak treba dopuniti',
    'Nedostaje strukturirani mesni sastav',
    'Nedostaju strukturirani začini',
    'PROVJERITI',
    'provjera prikaza recepta u tijeku',
    'recept ne smije u javnu objavu',
    'source-lock',
    'preview',
    'audit',
    '3–6 blagih ciklusa',
    '3-6 blagih ciklusa',
    'bez potvrđenog izvora',
    'nije poznato trajanje ciklusa',
];

echo "DRYCURED_RECIPE_PUBLIC_GATE_VALIDATOR\n";
echo "POST_IDS=" . implode(',', $post_ids) . "\n\n";

$total_fail = 0;

foreach ($post_ids as $post_id) {
    $post = get_post($post_id);

    echo "==============================\n";
    echo "POST_ID=$post_id\n";

    if (!$post || $post->post_type !== 'dry_recipe') {
        echo "FAIL post_missing_or_not_dry_recipe\n";
        $total_fail++;
        continue;
    }

    $url = get_permalink($post_id);
    $code = get_post_meta($post_id, '_dry_recipe_id', true);
    $validation = get_post_meta($post_id, '_dry_recipe_validation_status', true);
    $public_allowed = get_post_meta($post_id, '_dry_public_update_allowed', true);
    $publish_allowed = get_post_meta($post_id, 'drycured_public_publish_allowed', true);
    $human_check = get_post_meta($post_id, 'drycured_requires_final_human_check', true);

    echo "TITLE=" . get_the_title($post_id) . "\n";
    echo "URL=$url\n";
    echo "CODE=$code\n";
    echo "VALIDATION=$validation\n";
    echo "PUBLIC_ALLOWED=$public_allowed\n";
    echo "PUBLISH_ALLOWED=$publish_allowed\n";
    echo "HUMAN_CHECK=$human_check\n";

    $fail = 0;

    if (!$code) {
        echo "FAIL missing__dry_recipe_id\n";
        $fail++;
    }

    if (function_exists('dcv5_supported_recipe_codes')) {
        $codes = dcv5_supported_recipe_codes();
        if (!in_array($code, $codes, true)) {
            echo "FAIL code_not_registered_in_dcv5_supported_codes\n";
            $fail++;
        } else {
            echo "PASS code_registered\n";
        }
    } else {
        echo "FAIL dcv5_supported_recipe_codes_missing\n";
        $fail++;
    }

    if (function_exists('dcv5_get_recipe_profile')) {
        $profile = dcv5_get_recipe_profile($post_id, $code);
        if (is_array($profile)) {
            echo "PASS dcv5_profile_array\n";
        } else {
            echo "FAIL dcv5_profile_not_array\n";
            $fail++;
        }
    } else {
        echo "FAIL dcv5_get_recipe_profile_missing\n";
        $fail++;
    }

    if (function_exists('dcv12_quantity_profile_for_code')) {
        $q = dcv12_quantity_profile_for_code($code);
        if (is_array($q)) {
            echo "PASS quantity_profile_array\n";
            echo "MATERIALS=" . count($q['materials'] ?? []) . "\n";
            echo "SPICES=" . count($q['spices'] ?? []) . "\n";
            echo "LIQUIDS=" . count($q['liquids'] ?? []) . "\n";
        } else {
            echo "FAIL quantity_profile_missing\n";
            $fail++;
        }
    } else {
        echo "FAIL dcv12_quantity_profile_for_code_missing\n";
        $fail++;
    }

    global $wpdb;
    $aioseo = $wpdb->get_row($wpdb->prepare(
        "SELECT robots_default, robots_noindex, robots_nofollow FROM {$wpdb->prefix}aioseo_posts WHERE post_id=%d ORDER BY id ASC LIMIT 1",
        $post_id
    ), ARRAY_A);

    if ($aioseo && (int)$aioseo['robots_noindex'] === 1) {
        echo "PASS aioseo_noindex\n";
    } else {
        echo "FAIL aioseo_noindex_not_set\n";
        $fail++;
    }

    $fetch_url = add_query_arg('dcv_gate', time(), $url);
    $response = wp_remote_get($fetch_url, ['timeout' => 20, 'redirection' => 3]);

    if (is_wp_error($response)) {
        echo "FAIL html_fetch_error=" . $response->get_error_message() . "\n";
        $fail++;
    } else {
        $html = wp_remote_retrieve_body($response);
        $text = html_entity_decode(wp_strip_all_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);

        foreach ($forbidden_public_phrases as $phrase) {
            if (mb_stripos($text, $phrase, 0, 'UTF-8') !== false) {
                echo "FAIL public_forbidden_phrase=" . $phrase . "\n";
                $fail++;
            }
        }

        if ($fail === 0) {
            echo "PASS no_forbidden_public_phrases\n";
        }
    }

    if ($fail === 0) {
        echo "RESULT=PASS\n";
    } else {
        echo "RESULT=FAIL count=$fail\n";
        $total_fail += $fail;
    }

    echo "\n";
}

echo "==============================\n";
echo "TOTAL_FAIL=$total_fail\n";

if ($total_fail > 0) {
    exit(1);
}
