<?php
/**
 * Plugin Name: Drycured Recipe Overrides (data-driven, no-code-per-recipe)
 * Description: Cita _dry_recipe_overrides JSON meta po postu i override-a
 *              materials/spices/casings/timeline u DCV5 profilu. Skalabilno
 *              rjesenje za stotine recepata bez PHP izmjena po receptu.
 * Version: 1.0
 */
if (!defined('ABSPATH')) exit;

add_filter('dcv12_apply_final_profile_overrides', 'drycured_apply_data_overrides', 99999, 2);
// Fallback hook ako filter ne postoji vec kao chain - koristimo dcv5_recipe_profile s niskim prioritetom da ide NAKON svega
add_filter('dcv5_recipe_profile', 'drycured_apply_data_overrides_late', 99999, 3);

function drycured_get_recipe_overrides_by_code($code) {
    global $wpdb;
    $post_id = $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_dry_recipe_id' AND meta_value=%s LIMIT 1",
        $code
    ));
    if (!$post_id) return null;
    $raw = get_post_meta($post_id, '_dry_recipe_overrides', true);
    if (!$raw) return null;
    $data = json_decode($raw, true);
    return is_array($data) ? $data : null;
}

function drycured_apply_data_overrides($profile, $code) {
    if (!is_array($profile)) return $profile;
    $ov = drycured_get_recipe_overrides_by_code($code);
    if (!$ov) return $profile;
    return drycured_merge_recipe_overrides($profile, $ov);
}

function drycured_apply_data_overrides_late($profile, $post_id, $code) {
    if (!is_array($profile)) return $profile;
    $ov = drycured_get_recipe_overrides_by_code($code);
    if (!$ov) return $profile;
    return drycured_merge_recipe_overrides($profile, $ov);
}

function drycured_merge_recipe_overrides($profile, $ov) {
    if (!empty($ov['materials']) && is_array($ov['materials'])) {
        $profile['materials'] = $ov['materials'];
    }
    if (!empty($ov['spices']) && is_array($ov['spices'])) {
        $profile['spices'] = $ov['spices'];
    }
    if (!empty($ov['liquids']) && is_array($ov['liquids'])) {
        $profile['liquids'] = $ov['liquids'];
    }
    if (!empty($ov['casing_note'])) {
        $profile['quick'] = $profile['quick'] ?? [];
        foreach ($profile['quick'] as &$q) {
            if (($q['label'] ?? '') === 'Crijeva' || ($q['label'] ?? '') === 'Omotač') {
                $q['value'] = $ov['casing_note'];
            }
        }
        unset($q);
    }
    if (!empty($ov['timeline']) && is_array($ov['timeline'])) {
        $profile['timeline'] = $ov['timeline'];
    }
    if (!empty($ov['grinding_note'])) {
        $profile['_grinding_note'] = $ov['grinding_note'];
    }
    if (!empty($ov['profile']) && is_array($ov['profile'])) {
        $profile['profile'] = $ov['profile'];
    }
    if (!empty($ov['climate']) && is_array($ov['climate'])) {
        $profile['climate'] = $ov['climate'];
    }
    // quick_overrides: ['Dimljenje' => 'bez dimljenja', 'Trajanje' => '60–90 dana', ...]
    if (!empty($ov['quick_overrides']) && is_array($ov['quick_overrides'])) {
        $profile['quick'] = $profile['quick'] ?? [];
        foreach ($profile['quick'] as &$q) {
            $label = $q['label'] ?? '';
            if (isset($ov['quick_overrides'][$label])) {
                $q['value'] = $ov['quick_overrides'][$label];
            }
        }
        unset($q);
    }
    return $profile;
}

/*
 * Override dcv6 micro-variations section after it's injected at priority 1250.
 * Reads 'variations' key from _dry_recipe_overrides:
 *   [{"title":"...", "text":"..."}, ...]
 * Replaces the hardcoded Slavonska/Baranjska/Srijemska cards with recipe-specific content.
 */
add_filter('the_content', 'drycured_override_micro_variations', 1260);

function drycured_override_micro_variations($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }
    if (strpos($content, 'dcv6-variations') === false) {
        return $content;
    }

    $post_id = get_the_ID();
    $raw = get_post_meta($post_id, '_dry_recipe_overrides', true);
    if (!$raw) return $content;
    $ov = json_decode($raw, true);
    if (!is_array($ov) || empty($ov['variations']) || !is_array($ov['variations'])) {
        return $content;
    }

    $cards_html = '';
    foreach ($ov['variations'] as $v) {
        $cards_html .= '<article class="dcv6-info-card">' .
            '<h3>' . esc_html($v['title'] ?? '') . '</h3>' .
            '<p>' . esc_html($v['text'] ?? '') . '</p>' .
            '</article>';
    }

    $new_section = '<section class="dcv5-panel dcv6-variations" id="varijacije">' .
        '<h2><span>V</span>Mikroregionalne varijacije</h2>' .
        '<p class="dcv5-section-note">Varijacije ne mijenjaju osnovnu sigurnosnu logiku recepta. One služe za razumijevanje lokalnog stila, začinskog naglaska i ritma pripreme.</p>' .
        '<div class="dcv6-card-grid-three">' . $cards_html . '</div>' .
        '</section>';

    $content = preg_replace(
        '/<section[^>]+dcv6-variations[^>]*>.*?<\/section>/su',
        $new_section,
        $content,
        1
    );

    return $content;
}
