<?php
/**
 * Plugin Name: Drycured Blog Editorial Redirects
 * Description: Trajna preusmjerenja nastala uredničkim čišćenjem bloga.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

const DC_BLOG_EDITORIAL_REDIRECTS_OPTION =
    'drycured_blog_editorial_redirects_v1';

function dc_blog_editorial_normalize_path(string $path): string
{
    $path = rawurldecode($path);
    $path = '/' . ltrim($path, '/');

    return trailingslashit($path);
}

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }

    $request_uri = (string) ($_SERVER['REQUEST_URI'] ?? '');

    $request_path = (string) wp_parse_url(
        $request_uri,
        PHP_URL_PATH
    );

    $request_path =
        dc_blog_editorial_normalize_path($request_path);

    $redirects = get_option(
        DC_BLOG_EDITORIAL_REDIRECTS_OPTION,
        []
    );

    if (!is_array($redirects)) {
        return;
    }

    $target = $redirects[$request_path] ?? '';

    if (!is_string($target) || $target === '') {
        return;
    }

    nocache_headers();

    wp_safe_redirect(
        $target,
        301,
        'Drycured Blog Editorial Redirects'
    );

    exit;
}, -50);
