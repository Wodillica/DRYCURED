<?php
/**
 * Plugin Name: Drycured Legal Footer Links v1
 * Description: Servisni footer drycured.com: projekt, pomoć i pravila.
 * Version: 2.0.2
 * Author: Drycured.com
 */

defined('ABSPATH') || exit;

if (!function_exists('drycured_footer_v2_page_url')) {
    function drycured_footer_v2_page_url(string $slug, string $fallback = ''): string {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if ($page) {
            return get_permalink($page->ID);
        }

        return $fallback ? home_url($fallback) : home_url('/' . trim($slug, '/') . '/');
    }
}

if (!function_exists('drycured_legal_footer_links_render')) {
    function drycured_legal_footer_links_render(): void {
        if (is_admin() || wp_doing_ajax()) {
            return;
        }

        $columns = [
            'Drycured' => [
                ['O projektu', drycured_footer_v2_page_url('o-projektu')],
                ['Kontakt', drycured_footer_v2_page_url('kontakt')],
                ['Sitemap', drycured_footer_v2_page_url('sitemap')],
            ],
            'Pomoć' => [
                ['Prijavi grešku', drycured_footer_v2_page_url('prijavi-gresku')],
                ['Sigurnosna napomena', drycured_footer_v2_page_url('sigurnosna-napomena')],
            ],
            'Pravila' => [
                ['Politika privatnosti', drycured_footer_v2_page_url('politika-privatnosti')],
                ['Politika kolačića', drycured_footer_v2_page_url('politika-kolacica')],
                ['Uvjeti korištenja', drycured_footer_v2_page_url('uvjeti-koristenja')],
            ],
        ];
        ?>
        <style id="drycured-legal-footer-links-css">
            .dc-legal-footer {
                width: 100%;
                box-sizing: border-box;
                padding: 26px 18px 26px;
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
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }
            .dc-legal-footer-title {
                margin: 0 0 8px;
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
            @media (max-width: 760px) {
                .dc-legal-footer-inner {
                    grid-template-columns: 1fr;
                    text-align: center;
                    gap: 16px;
                }
                .dc-legal-footer-list {
                    justify-items: center;
                }
            }
        </style>

        <div class="dc-legal-footer dc-service-footer-v2" id="dc-legal-footer" role="contentinfo" aria-label="Servisne i pravne informacije" data-dc-footer-version="2.0.2">
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
        </div>
        <?php
    }
}

add_action('wp_footer', 'drycured_legal_footer_links_render', 1001);
