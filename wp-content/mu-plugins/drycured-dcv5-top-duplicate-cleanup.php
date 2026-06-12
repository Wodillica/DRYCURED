<?php
/**
 * Plugin Name: Drycured DCV5 Top Duplicate Cleanup
 * Description: Removes legacy top blocks that appear before the real DCV5 dry_recipe layout.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', 'dcdtc_start_buffer', 0);

function dcdtc_start_buffer() {
    if (is_admin() || !is_singular('dry_recipe')) {
        return;
    }

    ob_start('dcdtc_cleanup_html');
}

function dcdtc_find_first_dcv5_pos($html) {
    if (preg_match('/<div\b[^>]*class=["\'][^"\']*\bdcv5-recipe\b[^"\']*["\'][^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
        return (int) $m[0][1];
    }

    return false;
}

function dcdtc_remove_balanced_tag_by_class($html, $tag, $class) {
    $tag = preg_quote($tag, '/');
    $class = preg_quote($class, '/');

    $start_re = '/<' . $tag . '\b[^>]*class=["\'][^"\']*\b' . $class . '\b[^"\']*["\'][^>]*>/i';
    $tag_re = '/<\/?' . $tag . '\b[^>]*>/i';

    while (preg_match($start_re, $html, $m, PREG_OFFSET_CAPTURE)) {
        $start = (int) $m[0][1];
        $pos = $start + strlen($m[0][0]);
        $depth = 1;
        $end = null;

        while (preg_match($tag_re, $html, $tm, PREG_OFFSET_CAPTURE, $pos)) {
            $tag_text = $tm[0][0];
            $tag_pos = (int) $tm[0][1];
            $tag_len = strlen($tag_text);

            if (preg_match('/^<\//', $tag_text)) {
                $depth--;
                if ($depth === 0) {
                    $end = $tag_pos + $tag_len;
                    break;
                }
            } else {
                if (!preg_match('/\/\s*>$/', $tag_text)) {
                    $depth++;
                }
            }

            $pos = $tag_pos + $tag_len;
        }

        if ($end === null || $end <= $start) {
            break;
        }

        $html = substr($html, 0, $start) . substr($html, $end);
    }

    return $html;
}

function dcdtc_cleanup_html($html) {
    if (stripos($html, 'dcv5-recipe') === false) {
        return $html;
    }

    $dcv5_pos = dcdtc_find_first_dcv5_pos($html);
    if ($dcv5_pos === false) {
        return $html;
    }

    $before = substr($html, 0, $dcv5_pos);
    $after = substr($html, $dcv5_pos);

    $before = dcdtc_remove_balanced_tag_by_class($before, 'section', 'dcv31-hrsl001-source-lock-panel');
    $before = dcdtc_remove_balanced_tag_by_class($before, 'div', 'drycured-granulation-display-core');

    return $before . $after;
}
