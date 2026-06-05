<?php
/**
 * Plugin Name: Drycured Blog Canonical Redirects
 * Description: Precizna trajna preusmjerenja uklonjenih duplikata blog članaka.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

add_action('template_redirect', static function (): void {
    if (
        is_admin() ||
        wp_doing_ajax() ||
        (defined('REST_REQUEST') && REST_REQUEST)
    ) {
        return;
    }

    $request_path = wp_parse_url(
        isset($_SERVER['REQUEST_URI'])
            ? wp_unslash($_SERVER['REQUEST_URI'])
            : '',
        PHP_URL_PATH
    );

    if (!is_string($request_path) || $request_path === '') {
        return;
    }

    $request_path = untrailingslashit(rawurldecode($request_path));

    $redirects = [
        '/2026/03/28/tajna-savrsene-domace-pancete' => 1476,
        '/2026/03/28/domaci-kulen-iz-slavonije'      => 1466,
    ];

    if (!isset($redirects[$request_path])) {
        return;
    }

    $target = get_permalink($redirects[$request_path]);

    if (!$target) {
        return;
    }

    wp_safe_redirect(
        $target,
        301,
        'Drycured Blog Canonical Redirects'
    );

    exit;
}, 1);
