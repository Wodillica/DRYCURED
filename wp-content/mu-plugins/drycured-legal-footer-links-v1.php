<?php
/**
 * Plugin Name: Drycured Legal Footer Links v1
 * Description: Servisni footer drycured.com: sadržaj, projekt, pomoć i pravila.
 * Version: 3.0.0
 * Author: Drycured.com
 */

defined('ABSPATH') || exit;

if (!function_exists('drycured_footer_v3_page_url')) {
    function drycured_footer_v3_page_url(string $slug, string $fallback = ''): string {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page) {
            return get_permalink($page->ID);
        }

        return $fallback ? home_url($fallback) : home_url('/' . trim($slug, '/') . '/');
    }
}

if (!function_exists('drycured_service_footer_v3_render')) {
    function drycured_service_footer_v3_render(): void {
        if (is_admin() || wp_doing_ajax()) {
            return;
        }

        $year = date('Y');

        $columns = [
            'Sadržaj' => [
                ['Recepti', home_url('/recepti-baza/')],
                ['Blog i vodiči', home_url('/vodici/')],
                ['Knjiga', home_url('/knjiga/')],
                ['Kalkulator', home_url('/kalkulator/')],
                ['Podcast', home_url('/drycured-podcast/')],
            ],
            'Drycured' => [
                ['O projektu', drycured_footer_v3_page_url('o-projektu')],
                ['Kontakt', drycured_footer_v3_page_url('kontakt')],
                ['Sitemap', drycured_footer_v3_page_url('sitemap')],
            ],
            'Pomoć' => [
                ['Prijavi grešku', drycured_footer_v3_page_url('prijavi-gresku')],
                ['Sigurnosna napomena', drycured_footer_v3_page_url('sigurnosna-napomena')],
            ],
            'Pravila' => [
                ['Politika privatnosti', drycured_footer_v3_page_url('politika-privatnosti')],
                ['Politika kolačića', drycured_footer_v3_page_url('politika-kolacica')],
                ['Uvjeti korištenja', drycured_footer_v3_page_url('uvjeti-koristenja')],
            ],
        ];
        ?>
        <style id="drycured-legal-footer-links-css">
            .ast-footer-copyright,
            .site-footer .ast-footer-copyright {
                display: none !important;
            }

            .dc-legal-footer {
                width: 100%;
                box-sizing: border-box;
                padding: 24px 18px 0;
                background: #fff8ef;
                border-top: 1px solid rgba(92, 58, 28, .12);
                color: #5b3a25;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                font-size: 13px;
                line-height: 1.45;
            }

            .dc-legal-footer-inner {
                max-width: 1180px;
                margin: 0 auto;
                display: grid;
                grid-template-columns: 1.25fr 1fr 1fr 1fr;
                gap: 28px;
                align-items: start;
            }

            .dc-legal-footer-title {
                margin: 0 0 9px;
                font-weight: 800;
                letter-spacing: .04em;
                text-transform: uppercase;
                font-size: 12px;
                color: #2d2118;
            }

            .dc-legal-footer-list {
                list-style: none;
                margin: 0;
                padding: 0;
                display: grid;
                gap: 5px;
            }

            .dc-legal-footer a {
                color: #6d3a18;
                text-decoration: underline;
                text-underline-offset: 3px;
            }

            .dc-legal-footer-bottom {
                max-width: 1180px;
                margin: 20px auto 0;
                padding: 14px 0 18px;
                border-top: 1px solid rgba(92, 58, 28, .10);
                text-align: center;
                color: rgba(91, 58, 37, .78);
                font-size: 12px;
            }

            @media (max-width: 900px) {
                .dc-legal-footer-inner {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                    gap: 22px;
                }
            }

            @media (max-width: 640px) {
                .dc-legal-footer {
                    padding: 22px 14px 0;
                }

                .dc-legal-footer-inner {
                    grid-template-columns: 1fr;
                    text-align: center;
                    gap: 17px;
                }

                .dc-legal-footer-list {
                    justify-items: center;
                }
            }
        </style>

        <div class="dc-legal-footer dc-service-footer-v3" id="dc-legal-footer" role="contentinfo" aria-label="Servisne i pravne informacije" data-dc-footer-version="3.0.0">
            <div class="dc-legal-footer-inner">
                <?php foreach ($columns as $title => $links) : ?>
                    <section class="dc-legal-footer-col">
                        <p class="dc-legal-footer-title"><?php echo esc_html($title); ?></p>
                        <ul class="dc-legal-footer-list">
                            <?php foreach ($links as $link) : ?>
                                <li><a href="<?php echo esc_url($link[1]); ?>"><?php echo esc_html($link[0]); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endforeach; ?>
            </div>

            <div class="dc-legal-footer-bottom">
                Copyright © <?php echo esc_html($year); ?> drycured.com
            </div>
        </div>

        <script id="drycured-service-footer-v3-js">
        (function () {
            function hideOldThemeCopyright() {
                var footer = document.getElementById('dc-legal-footer');
                var nodes = document.querySelectorAll('.ast-footer-copyright, .site-footer *');

                nodes.forEach(function (node) {
                    if (footer && footer.contains(node)) return;

                    var text = (node.textContent || '').replace(/\s+/g, ' ').trim();
                    if (/Copyright\s*©\s*20\d{2}\s*drycured\.com/i.test(text)) {
                        node.style.display = 'none';
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hideOldThemeCopyright);
            } else {
                hideOldThemeCopyright();
            }
        })();
        </script>
        <?php
    }
}

add_action('wp_footer', 'drycured_service_footer_v3_render', 1001);
