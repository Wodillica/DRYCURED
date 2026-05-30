<?php
/**
 * Plugin Name: Drycured Recipe v0.6.1 Header Restore
 * Description: Vraća standardni prikaz glavnog site zaglavlja na single dry_recipe stranicama.
 * Version: 0.6.1
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.6.1 — vraćanje glavnog zaglavlja stranice.
         * Receptni layout ostaje proširen, ali Astra header se vraća u normalan centrirani okvir.
         */

        body.single-dry_recipe .site-header .ast-container,
        body.single-dry_recipe .main-header-bar .ast-container,
        body.single-dry_recipe .ast-primary-header-bar .ast-container,
        body.single-dry_recipe .ast-above-header-bar .ast-container,
        body.single-dry_recipe .ast-below-header-bar .ast-container {
            max-width: 1240px !important;
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            background: transparent !important;
        }

        body.single-dry_recipe .site-header {
            background: #ffffff !important;
        }

        body.single-dry_recipe .site-header .site-branding,
        body.single-dry_recipe .site-header .main-navigation {
            position: relative !important;
            z-index: 10 !important;
        }

        /*
         * Receptni sadržaj ostaje na sadašnjem dobrom v0.6 prikazu.
         */
        body.single-dry_recipe .site-content .ast-container,
        body.single-dry_recipe .content-area,
        body.single-dry_recipe main.site-main,
        body.single-dry_recipe article,
        body.single-dry_recipe .entry-content {
            max-width: none !important;
            width: 100% !important;
        }
    </style>
    <?php
}, 200000);
