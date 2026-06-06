<?php
/**
 * Plugin Name: Drycured Cookie Privacy v1
 * Description: Mali Drycured cookie/privacy sloj: nužni kolačići, funkcionalni prijevod, postavke kolačića.
 * Version: 1.0.0
 * Author: Drycured.com
 */

defined('ABSPATH') || exit;

if (!function_exists('drycured_cookie_privacy_enabled')) {
    function drycured_cookie_privacy_enabled(): bool {
        return !is_admin() && !wp_doing_ajax();
    }
}

if (!function_exists('drycured_cookie_privacy_render')) {
    function drycured_cookie_privacy_render(): void {
        if (!drycured_cookie_privacy_enabled()) {
            return;
        }

        $policy_url = esc_url(home_url('/politika-kolacica/'));
        $privacy_url = esc_url(home_url('/politika-privatnosti/'));
        ?>
        <style id="drycured-cookie-privacy-css">
            .dc-cookie-bar,
            .dc-cookie-panel,
            .dc-cookie-reopen {
                font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                color: #2d2118;
                box-sizing: border-box;
            }
            .dc-cookie-bar {
                position: fixed;
                left: 18px;
                right: 18px;
                bottom: 18px;
                z-index: 99998;
                display: none;
                max-width: 980px;
                margin: 0 auto;
                padding: 16px 18px;
                background: #fff8ef;
                border: 1px solid rgba(92, 58, 28, .18);
                border-radius: 18px;
                box-shadow: 0 18px 55px rgba(30, 20, 10, .18);
            }
            .dc-cookie-bar.is-visible { display: block; }
            .dc-cookie-title {
                font-weight: 800;
                margin: 0 0 6px;
                font-size: 16px;
            }
            .dc-cookie-text {
                margin: 0;
                font-size: 14px;
                line-height: 1.45;
            }
            .dc-cookie-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 12px;
            }
            .dc-cookie-btn {
                border: 0;
                border-radius: 999px;
                padding: 9px 14px;
                font-weight: 700;
                cursor: pointer;
                font-size: 13px;
            }
            .dc-cookie-btn.primary { background: #7a3b16; color: #fff; }
            .dc-cookie-btn.secondary { background: #ead9c6; color: #2d2118; }
            .dc-cookie-btn.ghost { background: transparent; color: #5b371f; text-decoration: underline; }
            .dc-cookie-links {
                margin-top: 8px;
                font-size: 12px;
            }
            .dc-cookie-links a { color: #6d3a18; text-decoration: underline; }
            .dc-cookie-reopen {
                position: fixed;
                left: 14px;
                bottom: 12px;
                z-index: 99997;
                display: none;
                border: 1px solid rgba(92, 58, 28, .20);
                background: rgba(255, 248, 239, .96);
                color: #5b371f;
                border-radius: 999px;
                padding: 7px 11px;
                font-size: 12px;
                cursor: pointer;
                box-shadow: 0 8px 22px rgba(30, 20, 10, .12);
            }
            .dc-cookie-reopen.is-visible { display: inline-flex; }
            .dc-cookie-overlay {
                position: fixed;
                inset: 0;
                display: none;
                z-index: 99999;
                background: rgba(20, 12, 6, .35);
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .dc-cookie-overlay.is-visible { display: flex; }
            .dc-cookie-panel {
                width: min(720px, 100%);
                max-height: 90vh;
                overflow: auto;
                background: #fffaf2;
                border-radius: 22px;
                padding: 22px;
                box-shadow: 0 22px 70px rgba(20, 12, 6, .30);
            }
            .dc-cookie-panel h2 {
                margin: 0 0 10px;
                font-size: 22px;
            }
            .dc-cookie-option {
                border-top: 1px solid rgba(92, 58, 28, .16);
                padding: 14px 0;
            }
            .dc-cookie-option strong { display: block; margin-bottom: 4px; }
            .dc-cookie-option p {
                margin: 0;
                font-size: 14px;
                line-height: 1.45;
            }
            .dc-cookie-option label {
                display: flex;
                gap: 10px;
                align-items: flex-start;
                cursor: pointer;
            }
            .dc-cookie-option input { margin-top: 3px; }
            @media (max-width: 640px) {
                .dc-cookie-bar { left: 10px; right: 10px; bottom: 10px; border-radius: 16px; }
                .dc-cookie-actions { flex-direction: column; }
                .dc-cookie-btn { width: 100%; }
            }
        </style>

        <div class="dc-cookie-bar" id="dc-cookie-bar" role="region" aria-label="Obavijest o kolačićima">
            <p class="dc-cookie-title">Kolačići na drycured.com</p>
            <p class="dc-cookie-text">
                Koristimo nužne kolačiće za rad stranice. Funkcionalne kolačiće koristimo za dodatne mogućnosti,
                primjerice pamćenje odabranog jezika prijevoda. Analitičke i marketinške kolačiće trenutačno ne koristimo.
            </p>
            <div class="dc-cookie-actions">
                <button type="button" class="dc-cookie-btn primary" data-dc-cookie-action="functional">Prihvati funkcionalne</button>
                <button type="button" class="dc-cookie-btn secondary" data-dc-cookie-action="necessary">Samo nužni</button>
                <button type="button" class="dc-cookie-btn ghost" data-dc-cookie-action="settings">Postavke</button>
            </div>
            <div class="dc-cookie-links">
                <a href="<?php echo $policy_url; ?>">Politika kolačića</a>
                ·
                <a href="<?php echo $privacy_url; ?>">Politika privatnosti</a>
            </div>
        </div>

        <button type="button" class="dc-cookie-reopen" id="dc-cookie-reopen">Postavke kolačića</button>

        <div class="dc-cookie-overlay" id="dc-cookie-overlay" aria-hidden="true">
            <div class="dc-cookie-panel" role="dialog" aria-modal="true" aria-labelledby="dc-cookie-panel-title">
                <h2 id="dc-cookie-panel-title">Postavke kolačića</h2>
                <div class="dc-cookie-option">
                    <label>
                        <input type="checkbox" checked disabled>
                        <span>
                            <strong>Nužni kolačići</strong>
                            <p>Uvijek aktivni. Potrebni su za sigurnost, prijavu korisnika i osnovni rad WordPress stranice.</p>
                        </span>
                    </label>
                </div>
                <div class="dc-cookie-option">
                    <label>
                        <input type="checkbox" id="dc-cookie-functional">
                        <span>
                            <strong>Funkcionalni prijevod</strong>
                            <p>Omogućuje dodatne funkcije poput pamćenja odabranog jezika prijevoda.</p>
                        </span>
                    </label>
                </div>
                <div class="dc-cookie-option">
                    <label>
                        <input type="checkbox" disabled>
                        <span>
                            <strong>Analitika</strong>
                            <p>Trenutno se ne koristi.</p>
                        </span>
                    </label>
                </div>
                <div class="dc-cookie-option">
                    <label>
                        <input type="checkbox" disabled>
                        <span>
                            <strong>Marketing</strong>
                            <p>Trenutno se ne koristi.</p>
                        </span>
                    </label>
                </div>
                <div class="dc-cookie-actions">
                    <button type="button" class="dc-cookie-btn primary" data-dc-cookie-action="save-settings">Spremi postavke</button>
                    <button type="button" class="dc-cookie-btn secondary" data-dc-cookie-action="necessary">Samo nužni</button>
                    <button type="button" class="dc-cookie-btn ghost" data-dc-cookie-action="close">Zatvori</button>
                </div>
            </div>
        </div>

        <script id="drycured-cookie-privacy-js">
        (function () {
            var key = 'dc_cookie_consent_v1';
            var cookieName = 'dc_cookie_consent';
            var bar = document.getElementById('dc-cookie-bar');
            var reopen = document.getElementById('dc-cookie-reopen');
            var overlay = document.getElementById('dc-cookie-overlay');
            var functionalBox = document.getElementById('dc-cookie-functional');

            function getChoice() {
                try { return JSON.parse(localStorage.getItem(key) || 'null'); }
                catch (e) { return null; }
            }

            function setCookie(value) {
                document.cookie = cookieName + '=' + encodeURIComponent(value) + '; Max-Age=' + (180 * 24 * 60 * 60) + '; Path=/; SameSite=Lax';
            }

            function saveChoice(functional) {
                var payload = {
                    necessary: true,
                    functional: !!functional,
                    analytics: false,
                    marketing: false,
                    savedAt: new Date().toISOString()
                };

                localStorage.setItem(key, JSON.stringify(payload));
                setCookie(functional ? 'functional' : 'necessary');

                document.documentElement.setAttribute('data-dc-cookie-consent', functional ? 'functional' : 'necessary');

                if (functional) {
                    document.documentElement.setAttribute('data-dc-functional-cookies', 'accepted');
                    window.dispatchEvent(new CustomEvent('drycured:functionalCookiesAccepted', { detail: payload }));
                } else {
                    document.documentElement.setAttribute('data-dc-functional-cookies', 'declined');
                    window.dispatchEvent(new CustomEvent('drycured:functionalCookiesDeclined', { detail: payload }));
                }

                hideBar();
                closeSettings();
                showReopen();
            }

            function showBar() { if (bar) bar.classList.add('is-visible'); }
            function hideBar() { if (bar) bar.classList.remove('is-visible'); }
            function showReopen() { if (reopen) reopen.classList.add('is-visible'); }
            function hideReopen() { if (reopen) reopen.classList.remove('is-visible'); }

            function openSettings() {
                var choice = getChoice();
                if (functionalBox) functionalBox.checked = !!(choice && choice.functional);
                if (overlay) {
                    overlay.classList.add('is-visible');
                    overlay.setAttribute('aria-hidden', 'false');
                }
            }

            function closeSettings() {
                if (overlay) {
                    overlay.classList.remove('is-visible');
                    overlay.setAttribute('aria-hidden', 'true');
                }
            }

            function init() {
                var choice = getChoice();

                if (!choice) {
                    showBar();
                    hideReopen();
                } else {
                    document.documentElement.setAttribute('data-dc-cookie-consent', choice.functional ? 'functional' : 'necessary');
                    document.documentElement.setAttribute('data-dc-functional-cookies', choice.functional ? 'accepted' : 'declined');
                    hideBar();
                    showReopen();
                }

                document.addEventListener('click', function (ev) {
                    var btn = ev.target.closest('[data-dc-cookie-action]');
                    if (!btn) return;

                    var action = btn.getAttribute('data-dc-cookie-action');

                    if (action === 'functional') saveChoice(true);
                    if (action === 'necessary') saveChoice(false);
                    if (action === 'settings') openSettings();
                    if (action === 'close') closeSettings();
                    if (action === 'save-settings') saveChoice(functionalBox && functionalBox.checked);
                });

                if (reopen) {
                    reopen.addEventListener('click', openSettings);
                }

                if (overlay) {
                    overlay.addEventListener('click', function (ev) {
                        if (ev.target === overlay) closeSettings();
                    });
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
        </script>
        <?php
    }
}

add_action('wp_footer', 'drycured_cookie_privacy_render', 999);
