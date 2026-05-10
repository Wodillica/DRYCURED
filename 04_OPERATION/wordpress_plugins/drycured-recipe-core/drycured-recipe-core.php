<?php
/**
 * Plugin Name: Drycured Recipe Core
 * Description: Moderna receptna baza: atlas, lista, kartice, ÄŤisti prikaz recepta, MD uvoz i priprema za kalkulator.
 * Version: 0.2.23
 * Author: drycured.com
 */
if (!defined('ABSPATH')) exit;

define('DRYCURED_RECIPE_CORE_VERSION','0.2.23');
define('DRYCURED_RECIPE_CORE_PATH', plugin_dir_path(__FILE__));
define('DRYCURED_RECIPE_CORE_URL', plugin_dir_url(__FILE__));

require_once DRYCURED_RECIPE_CORE_PATH.'includes/helpers.php';
require_once DRYCURED_RECIPE_CORE_PATH . 'includes/geo-normalizer.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/post-types.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/taxonomies.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/meta.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/importer.php';
require_once DRYCURED_RECIPE_CORE_PATH . 'includes/json-importer.php';
require_once DRYCURED_RECIPE_CORE_PATH . 'includes/calculator-dynamic-registry.php';
require_once DRYCURED_RECIPE_CORE_PATH . 'includes/calculator-save-endpoint.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/rest-api.php';
require_once DRYCURED_RECIPE_CORE_PATH.'includes/cli.php';
require_once DRYCURED_RECIPE_CORE_PATH.'admin/import-page.php';
require_once DRYCURED_RECIPE_CORE_PATH.'public/shortcodes.php';
require_once DRYCURED_RECIPE_CORE_PATH.'public/single-template.php';

add_action('init','drycured_register_recipe_post_types');
add_action('init','drycured_register_recipe_taxonomies');
add_action('init','drycured_register_recipe_meta');
add_action('rest_api_init','drycured_register_recipe_rest_routes');
add_action('admin_menu','drycured_register_recipe_admin_pages');
add_action('wp_enqueue_scripts','drycured_recipe_enqueue_assets');

register_activation_hook(__FILE__, function(){ drycured_register_recipe_post_types(); drycured_register_recipe_taxonomies(); flush_rewrite_rules(); });
register_deactivation_hook(__FILE__, function(){ flush_rewrite_rules(); });

function drycured_recipe_enqueue_assets(){
    wp_register_style('drycured-recipes', DRYCURED_RECIPE_CORE_URL.'assets/css/drycured-recipes.css', [], DRYCURED_RECIPE_CORE_VERSION);
    wp_register_script('drycured-recipes', DRYCURED_RECIPE_CORE_URL.'assets/js/drycured-recipes.js', [], DRYCURED_RECIPE_CORE_VERSION, true);
    wp_localize_script('drycured-recipes','DrycuredRecipes',['restUrl'=>esc_url_raw(rest_url('drycured/v1'))]);
}

/**
 * Drycured calculator bridge â€” uÄŤitava se samo na stranici Kalkulator.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('kalkulator')) {
        return;
    }

    wp_enqueue_script(
        'drycured-calculator-bridge',
        DRYCURED_RECIPE_CORE_URL . 'assets/js/calculator-bridge.js',
        [],
        DRYCURED_RECIPE_CORE_VERSION,
        true
    );
}, 30);

/**
 * Drycured calculator hierarchy â€” drĹľava â†’ regija â†’ proizvodi.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('kalkulator')) {
        return;
    }

    wp_enqueue_script(
        'drycured-calculator-hierarchy',
        DRYCURED_RECIPE_CORE_URL . 'assets/js/calculator-hierarchy.js',
        [],
        DRYCURED_RECIPE_CORE_VERSION,
        true
    );
}, 40);

/**
 * Drycured calculator region folders â€” drĹľava â†’ regija â†’ proizvodi.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('kalkulator')) {
        return;
    }

    wp_enqueue_script(
        'drycured-calculator-country-region-folders',
        DRYCURED_RECIPE_CORE_URL . 'assets/js/calculator-country-region-folders.js',
        [],
        DRYCURED_RECIPE_CORE_VERSION,
        true
    );
}, 45);

/**
 * Drycured kalkulator â€” prisilno uÄŤitavanje stilova i hijerarhijskog JS-a.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('kalkulator')) {
        return;
    }

    wp_enqueue_style(
        'drycured-recipes',
        DRYCURED_RECIPE_CORE_URL . 'assets/css/drycured-recipes.css',
        [],
        DRYCURED_RECIPE_CORE_VERSION
    );

    wp_enqueue_script(
        'drycured-calculator-country-region-folders',
        DRYCURED_RECIPE_CORE_URL . 'assets/js/calculator-country-region-folders.js',
        [],
        DRYCURED_RECIPE_CORE_VERSION,
        true
    );
}, 99);

/**
 * Drycured kalkulator â€” normalizacija prikaza regija.
 */
add_action('wp_enqueue_scripts', function () {
    if (!is_page('kalkulator')) {
        return;
    }

    wp_enqueue_script(
        'drycured-calculator-region-label-normalizer',
        DRYCURED_RECIPE_CORE_URL . 'assets/js/calculator-region-label-normalizer.js',
        [],
        DRYCURED_RECIPE_CORE_VERSION,
        true
    );
}, 60);
