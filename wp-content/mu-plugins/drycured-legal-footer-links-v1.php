<?php
/**
 * Plugin Name: Drycured Legal Footer Links v1
 * Description: Diskretne pravne poveznice u footeru: Politika privatnosti, Politika kolačića, Postavke kolačića.
 * Version: 1.0.0
 * Author: Drycured.com
 */

defined('ABSPATH') || exit;

if (!function_exists('drycured_legal_footer_links_render')) {
    function drycured_legal_footer_links_render(): void {
        if (is_admin() || wp_doing_ajax()) {
            return;
        }

        $privacy_url = esc_url(home_url('/politika-privatnosti/'));
        $cookies_url = esc_url(home_url('/politika-kolacica/'));
        ?>
        <style id="drycured-legal-footer-links-css">
            .dc-legal-footer {
                width: 100%;
                box-sizing: border-box;
                padding: 16px 18px 22px;
                background: #fff8ef;
                border-top: 1px solid rgba(92, 58, 28, .12);
                color: #5b3a25;
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                font-size: 13px;
                line-height: 1.45;
                text-align: center;
            }
            .dc-legal-footer-inner {
                max-width: 1180px;
                margin: 0 auto;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            .dc-legal-footer a,
            .dc-legal-footer button {
                color: #6d3a18;
                text-decoration: underline;
                text-underline-offset: 3px;
                background: transparent;
                border: 0;
                padding: 0;
                cursor: pointer;
                font: inherit;
            }
            .dc-legal-footer-sep {
                color: rgba(91, 58, 37, .55);
            }
            @media (max-width: 640px) {
                .dc-legal-footer {
                    padding-bottom: 18px;
                    font-size: 12px;
                }
                .dc-legal-footer-inner {
                    gap: 7px;
                }
            }
        </style>

        <div class="dc-legal-footer" id="dc-legal-footer" role="contentinfo" aria-label="Pravne informacije">
            <div class="dc-legal-footer-inner">
                <a href="<?php echo $privacy_url; ?>">Politika privatnosti</a>
                <span class="dc-legal-footer-sep">·</span>
                <a href="<?php echo $cookies_url; ?>">Politika kolačića</a>
                <span class="dc-legal-footer-sep">·</span>
                <button type="button" class="dc-legal-cookie-settings" id="dc-legal-cookie-settings">
                    Postavke kolačića
                </button>
            </div>
        </div>

        <script id="drycured-legal-footer-links-js">
        (function () {
            function openCookieSettings(ev) {
                if (ev) ev.preventDefault();

                var reopen = document.getElementById('dc-cookie-reopen');
                var settingsButton = document.querySelector('[data-dc-cookie-action="settings"]');

                if (reopen) {
                    reopen.click();
                    return;
                }

                if (settingsButton) {
                    settingsButton.click();
                    return;
                }

                window.location.href = '<?php echo $cookies_url; ?>';
            }

            document.addEventListener('click', function (ev) {
                var btn = ev.target.closest('#dc-legal-cookie-settings');
                if (!btn) return;
                openCookieSettings(ev);
            });
        })();
        </script>
        <?php
    }
}

add_action('wp_footer', 'drycured_legal_footer_links_render', 1001);
