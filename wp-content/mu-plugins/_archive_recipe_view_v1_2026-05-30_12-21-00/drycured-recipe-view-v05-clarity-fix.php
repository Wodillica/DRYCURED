<?php
/**
 * Plugin Name: Drycured Recipe View v0.5.1 Clarity Fix
 * Description: Poboljšava hijerarhiju količina, postotaka i omjera u v0.5 pilot prikazu.
 * Version: 0.5.1
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
         * v0.5.1 — količina je glavna informacija.
         * Postotak i g/kg su sekundarni tehnološki podaci.
         */

        .single-dry_recipe .dcv5-layout {
            grid-template-columns: minmax(0, 1fr) 250px !important;
            gap: 22px !important;
        }

        .single-dry_recipe #sirovine .dcv5-card-grid.two,
        .single-dry_recipe #zacini .dcv5-card-grid.two,
        .single-dry_recipe #tekucine .dcv5-card-grid.two {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card,
        .single-dry_recipe #zacini .dcv5-ingredient-card,
        .single-dry_recipe #tekucine .dcv5-ingredient-card {
            display: grid !important;
            grid-template-columns: minmax(0, 1.15fr) 180px minmax(0, 1.45fr) !important;
            gap: 16px !important;
            align-items: center !important;
            padding: 16px 18px !important;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        .single-dry_recipe #zacini .dcv5-ingredient-card h3,
        .single-dry_recipe #tekucine .dcv5-ingredient-card h3 {
            margin: 0 !important;
            font-size: 17px !important;
            line-height: 1.35 !important;
        }

        .single-dry_recipe #sirovine .dcv5-amount-line,
        .single-dry_recipe #zacini .dcv5-amount-line,
        .single-dry_recipe #tekucine .dcv5-amount-line {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 5px !important;
            margin: 0 !important;
            justify-items: start !important;
        }

        .single-dry_recipe .dcv5-amount {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 96px !important;
            padding: 9px 13px !important;
            border-radius: 12px !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 18px !important;
            line-height: 1.1 !important;
            font-weight: 900 !important;
        }

        .single-dry_recipe .dcv5-percent,
        .single-dry_recipe .dcv5-rate {
            display: block !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
            color: #6b5c38 !important;
            font-size: 12px !important;
            line-height: 1.35 !important;
            font-weight: 800 !important;
        }

        .single-dry_recipe .dcv5-percent::before {
            content: "Udio: ";
            color: #8a733c;
            font-weight: 900;
        }

        .single-dry_recipe .dcv5-rate::before {
            content: "Omjer: ";
            color: #8a733c;
            font-weight: 900;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card p,
        .single-dry_recipe #zacini .dcv5-ingredient-card p,
        .single-dry_recipe #tekucine .dcv5-ingredient-card p {
            margin: 0 !important;
            font-size: 15.5px !important;
            line-height: 1.62 !important;
            color: #3b4861 !important;
        }

        .single-dry_recipe .dcv5-side-panel {
            padding: 14px !important;
        }

        .single-dry_recipe .dcv5-side-panel a {
            font-size: 13px !important;
            padding: 7px 8px !important;
        }

        @media (max-width: 980px) {
            .single-dry_recipe .dcv5-layout {
                grid-template-columns: 1fr !important;
            }

            .single-dry_recipe #sirovine .dcv5-ingredient-card,
            .single-dry_recipe #zacini .dcv5-ingredient-card,
            .single-dry_recipe #tekucine .dcv5-ingredient-card {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }
        }
    </style>
    <?php
}, 1300);
