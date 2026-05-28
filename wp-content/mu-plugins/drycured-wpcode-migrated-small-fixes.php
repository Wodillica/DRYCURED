<?php
/**
 * Plugin Name: Drycured WPCode Migrated Small Fixes
 * Description: Controlled MU-plugin replacement for small low-risk WPCode snippets migrated out of WPCode.
 * Version: 0.0.1
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

/**
 * Migrated from WPCode snippet:
 * ID: 1454
 * Title: Limit Elementor Posts Excerpt Length
 *
 * Original behavior:
 * - limit excerpt length to 30 words
 * - use ellipsis as excerpt more text
 */
add_filter('excerpt_length', function ($length) {
    return 30;
}, 999);

add_filter('excerpt_more', function ($more) {
    return '&hellip;';
});
