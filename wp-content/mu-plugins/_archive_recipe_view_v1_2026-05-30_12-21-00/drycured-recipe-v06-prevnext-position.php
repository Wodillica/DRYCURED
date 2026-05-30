<?php
/**
 * Plugin Name: Drycured Recipe v0.6.3 Prev/Next Position
 * Description: Premješta prethodni/sljedeći recept ispod glavnog recipe layouta i skriva defaultnu WP/Astra navigaciju.
 * Version: 0.6.3
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
         * v0.6.3 — prethodni/sljedeći recept ide ispod cijelog glavnog layouta.
         */

        body.single-dry_recipe .dcv5-recipe > .dcv62-prevnext {
            width: 100% !important;
            max-width: none !important;
            margin: 28px 0 0 !important;
        }

        body.single-dry_recipe .dcv62-prevnext {
            clear: both !important;
        }

        /*
         * Sakrij defaultnu WP/Astra single post navigaciju.
         * Ne diramo našu dcv62 navigaciju.
         */
        body.single-dry_recipe .post-navigation,
        body.single-dry_recipe nav.navigation.post-navigation,
        body.single-dry_recipe .site-main > .navigation.post-navigation,
        body.single-dry_recipe .ast-single-post-navigation,
        body.single-dry_recipe .ast-post-navigation,
        body.single-dry_recipe .single-navigation {
            display: none !important;
        }

        body.single-dry_recipe .dcv62-prevnext {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 18px !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link {
            min-height: 86px !important;
            justify-content: center !important;
        }

        @media (max-width: 760px) {
            body.single-dry_recipe .dcv62-prevnext {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const recipe = document.querySelector('.dcv5-recipe');
            const layout = document.querySelector('.dcv5-layout');
            const nav = document.querySelector('.dcv62-prevnext');

            if (!recipe || !layout || !nav) {
                return;
            }

            /*
             * Ako je navigacija umetnuta unutar glavnog stupca,
             * premjesti je iza cijelog .dcv5-layout bloka.
             */
            if (layout.contains(nav)) {
                layout.insertAdjacentElement('afterend', nav);
            }

            /*
             * Sigurnosno ukloni defaultne navigacije koje tema može renderirati kasnije.
             */
            document.querySelectorAll(
                '.post-navigation, nav.navigation.post-navigation, .ast-single-post-navigation, .ast-post-navigation, .single-navigation'
            ).forEach(function (el) {
                if (!el.classList.contains('dcv62-prevnext')) {
                    el.remove();
                }
            });
        });
    </script>
    <?php
}, 300000);
