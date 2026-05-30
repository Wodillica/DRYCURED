<?php
/**
 * Plugin Name: Drycured Recipe View v0.5.11 Balanced Stage
 * Description: Čisti centrirani layout bez duple pozadine i bez bježanja udesno.
 * Version: 0.5.11
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
         * v0.5.11 — uravnotežen stage.
         * Puna pozadina stranice + centriran receptni sadržaj.
         */

        body.single-dry_recipe .site-content {
            background: #f8f0de !important;
        }

        body.single-dry_recipe .ast-container,
        body.single-dry_recipe .site-content .ast-container,
        body.single-dry_recipe .content-area,
        body.single-dry_recipe main.site-main,
        body.single-dry_recipe article,
        body.single-dry_recipe .entry-content {
            width: 100% !important;
            max-width: none !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            background: transparent !important;
        }

        body.single-dry_recipe .entry-content::before,
        body.single-dry_recipe .entry-content::after,
        body.single-dry_recipe article::before,
        body.single-dry_recipe article::after {
            display: none !important;
            content: none !important;
        }

        body.single-dry_recipe .dcv5-recipe {
            box-sizing: border-box !important;
            width: min(1280px, calc(100vw - 56px)) !important;
            max-width: 1280px !important;
            margin: 0 auto !important;
            padding: 44px 0 72px !important;
            background: transparent !important;
        }

        body.single-dry_recipe .dcv5-hero,
        body.single-dry_recipe .dcv5-quick-strip,
        body.single-dry_recipe .dcv5-layout {
            width: 100% !important;
            max-width: none !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        body.single-dry_recipe .dcv5-layout {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 250px !important;
            gap: 24px !important;
            align-items: start !important;
        }

        body.single-dry_recipe .dcv5-layout > main {
            min-width: 0 !important;
            width: 100% !important;
        }

        body.single-dry_recipe .dcv5-side-panel {
            width: 250px !important;
            max-width: 250px !important;
        }

        body.single-dry_recipe .dcv5-hero-grid {
            grid-template-columns: minmax(0, 1fr) 390px !important;
            gap: 28px !important;
        }

        body.single-dry_recipe .dcv5-hero-media img {
            height: 285px !important;
        }

        body.single-dry_recipe .dcv5-panel {
            width: 100% !important;
            max-width: none !important;
            padding: 28px 30px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-card-grid.two,
        body.single-dry_recipe #zacini .dcv5-card-grid.two,
        body.single-dry_recipe #tekucine .dcv5-card-grid.two {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card,
        body.single-dry_recipe #zacini .dcv5-ingredient-card,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card {
            display: grid !important;
            grid-template-columns: minmax(210px, 1fr) 165px minmax(260px, 1.35fr) !important;
            gap: 18px !important;
            align-items: center !important;
            width: 100% !important;
            max-width: none !important;
            padding: 18px 20px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        body.single-dry_recipe #zacini .dcv5-ingredient-card h3,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card h3,
        body.single-dry_recipe #sirovine .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #zacini .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #sirovine .dcv5-ingredient-card p,
        body.single-dry_recipe #zacini .dcv5-ingredient-card p,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card p {
            margin: 0 !important;
        }

        @media (min-width: 1500px) {
            body.single-dry_recipe .dcv5-recipe {
                width: min(1360px, calc(100vw - 96px)) !important;
                max-width: 1360px !important;
            }

            body.single-dry_recipe .dcv5-layout {
                grid-template-columns: minmax(0, 1fr) 260px !important;
                gap: 26px !important;
            }

            body.single-dry_recipe .dcv5-side-panel {
                width: 260px !important;
                max-width: 260px !important;
            }

            body.single-dry_recipe .dcv5-hero-grid {
                grid-template-columns: minmax(0, 1fr) 420px !important;
            }

            body.single-dry_recipe .dcv5-hero-media img {
                height: 305px !important;
            }
        }

        @media (max-width: 1179px) {
            body.single-dry_recipe .dcv5-recipe {
                width: min(100%, calc(100vw - 28px)) !important;
                padding: 28px 0 52px !important;
            }

            body.single-dry_recipe .dcv5-layout {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe .dcv5-side-panel {
                width: 100% !important;
                max-width: none !important;
            }

            body.single-dry_recipe .dcv5-hero-grid {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe #sirovine .dcv5-ingredient-card,
            body.single-dry_recipe #zacini .dcv5-ingredient-card,
            body.single-dry_recipe #tekucine .dcv5-ingredient-card {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    <?php
}, 1600);
