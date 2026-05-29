<?php
/**
 * Plugin Name: Drycured Process Anti-Flash
 * Description: Early anti-FOUC CSS for drycured process phase pages.
 * Version: 0.0.1
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

function drycured_process_antiflash_is_process_child_page(): bool {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return false;
    }

    $parent = get_page_by_path('proces-izrade');
    if (!$parent) {
        return false;
    }

    $post = get_post((int) get_queried_object_id());
    if (!$post) {
        return false;
    }

    return ((int) $post->post_parent === (int) $parent->ID);
}

function drycured_process_antiflash_css(): void {
    if (!drycured_process_antiflash_is_process_child_page()) {
        return;
    }
    ?>
    <style id="drycured-process-antiflash-css">
        body.page-child.parent-pageid-2864 .entry-content > * {
            display: none;
        }

        body.page-child.parent-pageid-2864 .entry-content > .dc-process-phase-page,
        body.page-child.parent-pageid-2864 .entry-content > .dcpo-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpf-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpm-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcps-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpsol-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpr-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpmi-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpun-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpdim-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpdry-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcpzrn-wrap,
        body.page-child.parent-pageid-2864 .entry-content > .dcppack-wrap {
            display: block;
        }
    </style>
    <?php
}
add_action('wp_head', 'drycured_process_antiflash_css', 1);
