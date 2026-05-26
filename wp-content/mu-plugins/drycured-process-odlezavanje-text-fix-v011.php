<?php
/**
 * Plugin Name: Drycured Process Odlezavanje Text Fix v0.1.1
 * Description: Popravlja lom naslova i tekstualni balans na stranici Odležavanje smjese.
 * Version: 0.1.1
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpodltxt_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/odlezavanje',
        'proces-izrade/odlezavanje-smjese',
    ], true);
}

function dcpodltxt_assets() {
    if (!dcpodltxt_is_page()) {
        return;
    }
    ?>
    <style>
        /*
         * Odležavanje — korekcija hero teksta.
         * Cilj: naslov se ne smije lomiti kao "Odležavanj e".
         */

        .dcpo-wrap .dcpo-hero {
            grid-template-columns: minmax(430px, .92fr) minmax(520px, 1.08fr) !important;
        }

        .dcpo-wrap .dcpo-hero-copy {
            min-width: 0 !important;
        }

        .dcpo-wrap h1 {
            max-width: 100% !important;
            font-size: clamp(46px, 5.1vw, 72px) !important;
            line-height: .98 !important;
            letter-spacing: -.055em !important;
            word-break: keep-all !important;
            overflow-wrap: normal !important;
            hyphens: none !important;
        }

        .dcpo-wrap .dcpo-lead {
            max-width: 560px !important;
            font-size: clamp(17px, 1.65vw, 20px) !important;
            line-height: 1.62 !important;
        }

        .dcpo-wrap .dcpo-visual-overlay h2 {
            max-width: 560px !important;
            font-size: clamp(28px, 2.7vw, 40px) !important;
            line-height: 1.08 !important;
            letter-spacing: -.035em !important;
        }

        @media (max-width: 1100px) {
            .dcpo-wrap .dcpo-hero {
                grid-template-columns: 1fr !important;
            }

            .dcpo-wrap h1 {
                font-size: clamp(44px, 9vw, 68px) !important;
            }
        }

        @media (max-width: 680px) {
            .dcpo-wrap h1 {
                font-size: clamp(40px, 13vw, 58px) !important;
                letter-spacing: -.045em !important;
            }

            .dcpo-wrap .dcpo-lead {
                font-size: 17px !important;
            }

            .dcpo-wrap .dcpo-visual-overlay h2 {
                font-size: clamp(24px, 8vw, 34px) !important;
            }
        }
    </style>

    <script id="drycured-odlezavanje-text-fix-v011">
        document.addEventListener('DOMContentLoaded', function () {
            const wrap = document.querySelector('.dcpo-wrap');
            if (!wrap) return;

            const h1 = wrap.querySelector('h1');
            if (h1 && h1.textContent.trim() === 'Odležavanje') {
                h1.textContent = 'Odležavanje smjese';
            }
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpodltxt_assets', 180);
