<?php
/**
 * Plugin Name: Drycured Recipe View v0.5.12 Card Polish
 * Description: Uređuje čitljivost kartica, brzi proizvodni sažetak i tekstualnu hijerarhiju v0.5 prikaza.
 * Version: 0.5.12
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
         * v0.5.12 — tekst u karticama mora biti čitljiv, prozračan i hijerarhijski jasan.
         */

        body.single-dry_recipe .dcv5-panel {
            line-height: 1.65 !important;
        }

        body.single-dry_recipe .dcv5-section-note {
            max-width: 920px !important;
            margin-bottom: 22px !important;
            color: #46536b !important;
            font-size: 15.5px !important;
            line-height: 1.7 !important;
        }

        /*
         * Brzi proizvodni sažetak: šarža, trajanje, dimljenje...
         * Vrijednost mora biti glavna informacija.
         */
        body.single-dry_recipe .dcv5-quick-strip {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 12px !important;
            margin: 18px 0 24px !important;
        }

        body.single-dry_recipe .dcv5-quick-card {
            min-height: 82px !important;
            padding: 16px 18px !important;
            border-radius: 18px !important;
            background: #fffaf0 !important;
            border: 1px solid #dfc282 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            box-shadow: 0 8px 20px rgba(25, 32, 48, .045) !important;
        }

        body.single-dry_recipe .dcv5-quick-card span {
            margin: 0 0 7px !important;
            color: #8a733c !important;
            font-size: 11px !important;
            line-height: 1.1 !important;
            font-weight: 900 !important;
            letter-spacing: .075em !important;
            text-transform: uppercase !important;
        }

        body.single-dry_recipe .dcv5-quick-card strong {
            color: #0f1930 !important;
            font-size: 17px !important;
            line-height: 1.25 !important;
            font-weight: 900 !important;
        }

        /*
         * Kartice sastojaka: mirniji prikaz, bolji razmak i čitljiviji opis.
         */
        body.single-dry_recipe #sirovine .dcv5-ingredient-card,
        body.single-dry_recipe #zacini .dcv5-ingredient-card,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card {
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            padding: 20px 22px !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        body.single-dry_recipe #zacini .dcv5-ingredient-card h3,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card h3 {
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card p,
        body.single-dry_recipe #zacini .dcv5-ingredient-card p,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card p {
            color: #43506a !important;
            font-size: 15.5px !important;
            line-height: 1.7 !important;
        }

        body.single-dry_recipe .dcv5-amount {
            border-radius: 14px !important;
            padding: 10px 15px !important;
            min-width: 118px !important;
            font-size: 18px !important;
            box-shadow: 0 6px 14px rgba(17, 27, 51, .16) !important;
        }

        body.single-dry_recipe .dcv5-percent,
        body.single-dry_recipe .dcv5-rate {
            color: #7b693b !important;
            font-size: 12.5px !important;
            line-height: 1.45 !important;
            font-weight: 800 !important;
        }

        /*
         * Klimatski i tehnološki potpis: tekst je bio previše zbijen.
         */
        body.single-dry_recipe #klima .dcv5-card-grid.two {
            gap: 14px !important;
        }

        body.single-dry_recipe .dcv5-climate-card {
            padding: 18px 19px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-climate-card h3 {
            margin-bottom: 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.3 !important;
        }

        body.single-dry_recipe .dcv5-climate-card p {
            color: #43506a !important;
            font-size: 15.5px !important;
            line-height: 1.72 !important;
        }

        /*
         * Procesna kronologija: bolji ritam između dana, naslova i kritične napomene.
         */
        body.single-dry_recipe .dcv5-timeline {
            gap: 16px !important;
        }

        body.single-dry_recipe .dcv5-timeline-item {
            padding: 18px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-timeline-item h3 {
            margin: 0 0 10px !important;
            color: #10182d !important;
            font-size: 21px !important;
            font-weight: 900 !important;
            line-height: 1.25 !important;
        }

        body.single-dry_recipe .dcv5-timeline-item p {
            color: #3f4c66 !important;
            font-size: 15.8px !important;
            line-height: 1.7 !important;
        }

        body.single-dry_recipe .dcv5-critical {
            margin-top: 14px !important;
            padding: 12px 14px !important;
            border-radius: 12px !important;
            color: #3f4c66 !important;
            font-size: 14.5px !important;
            line-height: 1.6 !important;
        }

        /*
         * Anatomija greške i Gotovo je kad — ravnomjerniji tekst u karticama.
         */
        body.single-dry_recipe .dcv5-error-card,
        body.single-dry_recipe .dcv5-check-card,
        body.single-dry_recipe .dcv5-safety-card,
        body.single-dry_recipe .dcv5-serving-card {
            padding: 18px 19px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-error-card h3,
        body.single-dry_recipe .dcv5-check-card h3,
        body.single-dry_recipe .dcv5-safety-card h3,
        body.single-dry_recipe .dcv5-serving-card h3 {
            margin-bottom: 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.32 !important;
        }

        body.single-dry_recipe .dcv5-error-card p,
        body.single-dry_recipe .dcv5-check-card p,
        body.single-dry_recipe .dcv5-safety-card p,
        body.single-dry_recipe .dcv5-serving-card p {
            color: #43506a !important;
            font-size: 15.3px !important;
            line-height: 1.68 !important;
        }

        body.single-dry_recipe .dcv5-error-card p + p {
            margin-top: 8px !important;
        }

        body.single-dry_recipe .dcv5-small-pill {
            padding: 6px 9px !important;
            font-size: 12px !important;
            line-height: 1.15 !important;
            border-radius: 999px !important;
        }

        /*
         * Responsive: brzi sažetak ne smije biti zgnječen.
         */
        @media (max-width: 980px) {
            body.single-dry_recipe .dcv5-quick-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            body.single-dry_recipe .dcv5-quick-strip {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe .dcv5-quick-card {
                min-height: auto !important;
            }
        }
    </style>
    <?php
}, 1700);
