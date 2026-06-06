<?php
/**
 * Plugin Name: Drycured Service Page Typography v1
 * Description: Tipografska hijerarhija za servisne i pravne stranice drycured.com.
 * Version: 1.0.0
 * Author: Drycured.com
 */

defined('ABSPATH') || exit;

if (!function_exists('drycured_service_page_typography_is_target')) {
    function drycured_service_page_typography_is_target(): bool {
        if (is_admin() || wp_doing_ajax() || !is_page()) {
            return false;
        }

        $slugs = [
            'o-projektu',
            'kontakt',
            'sitemap',
            'prijavi-gresku',
            'sigurnosna-napomena',
            'uvjeti-koristenja',
            'politika-privatnosti',
            'politika-kolacica',
        ];

        return is_page($slugs);
    }
}

if (!function_exists('drycured_service_page_typography_css')) {
    function drycured_service_page_typography_css(): void {
        if (!drycured_service_page_typography_is_target()) {
            return;
        }
        ?>
        <style id="drycured-service-page-typography-v1">
            /*
             * Drycured service/legal page typography
             * Cilj: glavni naslov stranice ostaje najjači,
             * a H2/H3 u sadržaju postaju podnaslovi prikladne veličine.
             */

            body.page .entry-title,
            body.page h1.entry-title,
            body.page .ast-single-post .entry-title {
                font-size: clamp(32px, 3.2vw, 46px);
                line-height: 1.12;
                letter-spacing: -0.015em;
                margin-bottom: 26px;
            }

            body.page .entry-content h2,
            body.page .site-content .entry-content h2 {
                font-size: clamp(24px, 2.1vw, 30px);
                line-height: 1.22;
                letter-spacing: -0.01em;
                margin-top: 34px;
                margin-bottom: 14px;
            }

            body.page .entry-content h3,
            body.page .site-content .entry-content h3 {
                font-size: clamp(19px, 1.55vw, 23px);
                line-height: 1.28;
                margin-top: 26px;
                margin-bottom: 10px;
            }

            body.page .entry-content p,
            body.page .entry-content li {
                font-size: 16px;
                line-height: 1.72;
            }

            body.page .entry-content ul,
            body.page .entry-content ol {
                margin-top: 10px;
                margin-bottom: 18px;
            }

            body.page .entry-content code {
                font-size: .92em;
                background: rgba(122, 59, 22, .08);
                padding: 1px 5px;
                border-radius: 5px;
            }

            @media (max-width: 640px) {
                body.page .entry-title,
                body.page h1.entry-title,
                body.page .ast-single-post .entry-title {
                    font-size: 32px;
                    line-height: 1.16;
                    margin-bottom: 20px;
                }

                body.page .entry-content h2,
                body.page .site-content .entry-content h2 {
                    font-size: 24px;
                    margin-top: 28px;
                    margin-bottom: 10px;
                }

                body.page .entry-content h3,
                body.page .site-content .entry-content h3 {
                    font-size: 20px;
                    margin-top: 22px;
                }
            }
        </style>
        <?php
    }
}

add_action('wp_head', 'drycured_service_page_typography_css', 40);
