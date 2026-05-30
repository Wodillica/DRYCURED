<?php
/**
 * Plugin Name: Drycured Recipe v0.6.4 Navigation Polish
 * Description: Profesionalno uređuje donju prethodni/sljedeći navigaciju i pomiče plutajući gumb usporedbe.
 * Version: 0.6.4
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
         * v0.6.4 — donja navigacija mora izgledati kao završni dio recepta,
         * a ne kao još jedna velika kartica.
         */

        body.single-dry_recipe .dcv62-prevnext {
            width: 100% !important;
            max-width: 100% !important;
            margin: 30px auto 18px !important;
            padding: 0 !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 18px !important;
            clear: both !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link {
            min-height: 74px !important;
            padding: 18px 22px !important;
            border-radius: 22px !important;
            border: 1px solid #d9bc78 !important;
            background:
                linear-gradient(135deg, rgba(255,250,240,.98), rgba(248,237,210,.96)) !important;
            color: #10182d !important;
            text-decoration: none !important;
            box-shadow: 0 14px 30px rgba(25, 32, 48, .075) !important;
            display: flex !important;
            justify-content: center !important;
            position: relative !important;
            overflow: hidden !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link::before {
            content: "" !important;
            position: absolute !important;
            inset: 0 !important;
            background: radial-gradient(circle at top left, rgba(216,166,63,.18), transparent 36%) !important;
            opacity: .9 !important;
            pointer-events: none !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 18px 38px rgba(25, 32, 48, .12) !important;
            border-color: #caa65b !important;
            background:
                linear-gradient(135deg, rgba(255,247,225,1), rgba(246,232,197,1)) !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link span,
        body.single-dry_recipe .dcv62-prevnext-link strong {
            position: relative !important;
            z-index: 2 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link span {
            color: #8a733c !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: .08em !important;
            line-height: 1.1 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link strong {
            color: #10182d !important;
            font-size: 18px !important;
            line-height: 1.28 !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv62-prev {
            align-items: flex-start !important;
            text-align: left !important;
            padding-left: 54px !important;
        }

        body.single-dry_recipe .dcv62-next {
            align-items: flex-end !important;
            text-align: right !important;
            padding-right: 54px !important;
        }

        body.single-dry_recipe .dcv62-prev::after,
        body.single-dry_recipe .dcv62-next::after {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 34px !important;
            height: 34px !important;
            border-radius: 999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 18px !important;
            font-weight: 900 !important;
            box-shadow: 0 8px 18px rgba(17,27,51,.18) !important;
            z-index: 3 !important;
        }

        body.single-dry_recipe .dcv62-prev::after {
            content: "←" !important;
            left: 16px !important;
        }

        body.single-dry_recipe .dcv62-next::after {
            content: "→" !important;
            right: 16px !important;
        }

        /*
         * Admin blok ne smije vizualno prekidati javnu navigaciju.
         * Administrator ga i dalje vidi, ali kao tehnički blok ispod svega.
         */
        body.single-dry_recipe .dcv6-admin-block {
            margin-top: 34px !important;
            opacity: .92 !important;
        }

        /*
         * Plutajući gumb za usporedbu — gore u žuti dio stranice,
         * ne zalijepljen za sam donji rub.
         */
        body.single-dry_recipe .dc-floating-compare,
        body.single-dry_recipe .drycured-floating-compare,
        body.single-dry_recipe .dc-compare-floating,
        body.single-dry_recipe [class*="floating"][class*="compare"],
        body.single-dry_recipe [class*="compare"][class*="floating"] {
            bottom: 96px !important;
            right: 36px !important;
            z-index: 9999 !important;
            box-shadow: 0 14px 30px rgba(25,32,48,.16) !important;
        }

        @media (max-width: 760px) {
            body.single-dry_recipe .dcv62-prevnext {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            body.single-dry_recipe .dcv62-next {
                text-align: left !important;
                align-items: flex-start !important;
                padding-left: 54px !important;
                padding-right: 22px !important;
            }

            body.single-dry_recipe .dcv62-next::after {
                left: 16px !important;
                right: auto !important;
            }

            body.single-dry_recipe .dc-floating-compare,
            body.single-dry_recipe .drycured-floating-compare,
            body.single-dry_recipe .dc-compare-floating,
            body.single-dry_recipe [class*="floating"][class*="compare"],
            body.single-dry_recipe [class*="compare"][class*="floating"] {
                bottom: 82px !important;
                right: 18px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const recipe = document.querySelector('.dcv5-recipe');
            const nav = document.querySelector('.dcv62-prevnext');
            const admin = document.querySelector('.dcv6-admin-block');

            /*
             * Navigacija treba biti prije admin bloka.
             * Admin blok je tehnički sloj, a prethodni/sljedeći je javni završetak recepta.
             */
            if (recipe && nav && admin && admin.compareDocumentPosition(nav) & Node.DOCUMENT_POSITION_FOLLOWING) {
                admin.insertAdjacentElement('beforebegin', nav);
            }

            /*
             * Ako tema doda vlastiti NEXT/PREV, makni ga.
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
}, 400000);
