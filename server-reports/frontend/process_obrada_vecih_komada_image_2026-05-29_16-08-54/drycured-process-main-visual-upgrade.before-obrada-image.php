<?php
/**
 * Plugin Name: Drycured Process Main Visual Upgrade
 * Description: Visual modernization layer for the main /proces-izrade/ page: hero, images, card polish and related starter culture block.
 * Version: 0.0.1
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

function dcpmvu_is_process_main_page(): bool {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return false;
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade';
}

function dcpmvu_assets(): void {
    if (!dcpmvu_is_process_main_page()) {
        return;
    }

    $base = home_url('/wp-content/uploads/drycured/home-process/');
    ?>
    <style id="drycured-process-main-visual-upgrade-css">
        body.page .dcpo-wrap {
            max-width: 1180px !important;
            margin: 0 auto !important;
            padding: 0 22px 44px !important;
        }

        body.page .dcpo-wrap > h1,
        body.page .dcpo-wrap > .dcpo-kicker {
            display: none !important;
        }

        .dcpmvu-hero {
            margin: 24px auto 34px;
            padding: 42px;
            border-radius: 34px;
            background:
                radial-gradient(circle at 82% 18%, rgba(202,164,111,.28), transparent 34%),
                linear-gradient(135deg, rgba(255,255,255,.94), rgba(248,244,235,.98));
            border: 1px solid rgba(139,111,71,.16);
            box-shadow: 0 18px 52px rgba(60,40,20,.10);
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
            gap: 34px;
            align-items: center;
            box-sizing: border-box;
        }

        .dcpmvu-eyebrow {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 16px;
            padding: 9px 16px;
            border-radius: 999px;
            background: rgba(139,111,71,.12);
            color: #8B6F47;
            font-size: 12px;
            font-weight: 850;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .dcpmvu-hero h1 {
            margin: 0 0 16px;
            color: #101722;
            font-size: clamp(38px, 5vw, 68px);
            line-height: 1.02;
            letter-spacing: -.045em;
        }

        .dcpmvu-hero p {
            margin: 0;
            max-width: 720px;
            color: #4e5a68;
            font-size: 17px;
            line-height: 1.78;
        }

        .dcpmvu-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .dcpmvu-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 11px 18px;
            border-radius: 999px;
            font-weight: 850;
            font-size: 14px;
            text-decoration: none !important;
        }

        .dcpmvu-btn--dark {
            background: #101722;
            color: #fff !important;
            box-shadow: 0 12px 28px rgba(16,23,34,.18);
        }

        .dcpmvu-btn--light {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(139,111,71,.18);
        }

        .dcpmvu-hero-visual {
            position: relative;
            min-height: 390px;
            border-radius: 30px;
            overflow: hidden;
            background: #101722;
            box-shadow: 0 22px 50px rgba(16,23,34,.24);
        }

        .dcpmvu-hero-main-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: .88;
            transform: scale(1.02);
        }

        .dcpmvu-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.08), rgba(16,23,34,.78)),
                radial-gradient(circle at 20% 15%, rgba(255,255,255,.20), transparent 32%);
        }

        .dcpmvu-mini-strip {
            position: absolute;
            left: 22px;
            right: 22px;
            bottom: 22px;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .dcpmvu-mini-strip span {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 72px;
            padding: 12px;
            border-radius: 18px;
            background: rgba(255,255,255,.88);
            color: #101722;
            font-size: 12px;
            font-weight: 850;
            line-height: 1.15;
            backdrop-filter: blur(8px);
        }

        .dcpmvu-mini-strip small {
            color: #8B6F47;
            font-size: 10px;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpo-wrap .dcpo-grid,
        .dcpo-wrap [class*="grid"] {
            gap: 18px !important;
        }

        .dcpo-wrap .dcpo-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px !important;
            border: 1px solid rgba(139,111,71,.14) !important;
            background: rgba(255,255,255,.92) !important;
            box-shadow: 0 14px 34px rgba(60,40,20,.08) !important;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dcpo-wrap .dcpo-card:hover {
            transform: translateY(-3px);
            border-color: rgba(139,111,71,.26) !important;
            box-shadow: 0 18px 44px rgba(60,40,20,.13) !important;
        }

        .dcpmvu-card-image {
            display: block;
            width: calc(100% + 2px);
            height: 138px;
            margin: -1px -1px 16px;
            overflow: hidden;
            background: #101722;
        }

        .dcpmvu-card-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.01);
        }

        .dcpo-wrap .dcpo-card h3 {
            margin-top: 8px !important;
        }

        .dcpo-wrap .dcpo-card .dcpo-btn {
            margin-top: 12px !important;
            background: #8B6F47 !important;
            color: #fff !important;
            border-radius: 999px !important;
            font-weight: 850 !important;
            box-shadow: 0 8px 18px rgba(139,111,71,.18);
        }

        .dcpo-wrap .dcpo-card .dcpo-btn:hover {
            background: #765c39 !important;
            color: #fff !important;
        }

        .dcpmvu-related-badge {
            display: inline-flex;
            margin-left: 8px;
            padding: 5px 9px;
            border-radius: 999px;
            background: rgba(139,111,71,.12);
            color: #8B6F47;
            font-size: 10px;
            font-weight: 850;
            letter-spacing: .10em;
            text-transform: uppercase;
            vertical-align: middle;
        }

        .dcpo-wrap .dcpo-related,
        .dcpo-wrap section:has(a[href*="starter-kulture"]) {
            border-radius: 30px !important;
            background:
                radial-gradient(circle at 90% 20%, rgba(202,164,111,.18), transparent 28%),
                linear-gradient(135deg, rgba(255,255,255,.92), rgba(248,244,235,.98)) !important;
            border: 1px solid rgba(139,111,71,.16) !important;
            box-shadow: 0 16px 42px rgba(60,40,20,.08) !important;
        }

        .dcpmvu-bottom-note {
            margin: 34px auto 0;
            padding: 34px;
            border-radius: 30px;
            background: #101722;
            color: #fff;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 24px;
            align-items: center;
            box-shadow: 0 18px 48px rgba(16,23,34,.16);
        }

        .dcpmvu-bottom-note h2 {
            margin: 0 0 10px;
            color: #ffffff !important;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.08;
            letter-spacing: -.025em;
        }

        .dcpmvu-bottom-note p {
            margin: 0;
            max-width: 760px;
            color: rgba(255,255,255,.84) !important;
            line-height: 1.7;
        }

        .dcpmvu-bottom-note .dcpmvu-eyebrow,
        .dcpmvu-bottom-note span {
            color: rgba(255,255,255,.72);
        }


        /* HARD POLISH: force readable dark CTA and hide leftover phase numbers */
        body.page-id-2864 .dcpmvu-bottom-note,
        body.page-id-2864 .dcpo-wrap .dcpmvu-bottom-note {
            background: #101722 !important;
            color: #ffffff !important;
        }

        body.page-id-2864 .dcpmvu-bottom-note h2,
        body.page-id-2864 .dcpo-wrap .dcpmvu-bottom-note h2 {
            color: #ffffff !important;
            opacity: 1 !important;
            filter: none !important;
            text-shadow: none !important;
        }

        body.page-id-2864 .dcpmvu-bottom-note p,
        body.page-id-2864 .dcpo-wrap .dcpmvu-bottom-note p {
            color: rgba(255,255,255,.86) !important;
            opacity: 1 !important;
        }

        body.page-id-2864 .dcpo-card > span:first-child,
        body.page-id-2864 .dcpo-card > small:first-child,
        body.page-id-2864 .dcpo-card > b:first-child,
        body.page-id-2864 .dcpo-card > strong:first-child,
        body.page-id-2864 .dcpo-card > em:first-child,
        body.page-id-2864 .dcpo-card [class*="number"],
        body.page-id-2864 .dcpo-card [class*="num"],
        body.page-id-2864 .dcpo-card [class*="step"],
        body.page-id-2864 .dcpo-card [class*="phase"] {
            display: none !important;
        }


        @media (max-width: 920px) {
            .dcpmvu-hero {
                grid-template-columns: 1fr;
                padding: 30px 22px;
            }

            .dcpmvu-hero-visual {
                min-height: 330px;
            }

            .dcpmvu-mini-strip {
                grid-template-columns: repeat(2, 1fr);
            }

            .dcpmvu-bottom-note {
                grid-template-columns: 1fr;
                padding: 28px 22px;
            }
        }

        @media (max-width: 620px) {
            body.page .dcpo-wrap {
                padding: 0 14px 36px !important;
            }

            .dcpmvu-hero {
                margin-top: 16px;
                border-radius: 26px;
            }

            .dcpmvu-hero-visual {
                min-height: 300px;
            }

            .dcpmvu-mini-strip {
                left: 14px;
                right: 14px;
                bottom: 14px;
                gap: 8px;
            }

            .dcpmvu-mini-strip span {
                min-height: 64px;
                padding: 10px;
            }

            .dcpmvu-card-image {
                height: 160px;
            }
        }
    </style>

    <script id="drycured-process-main-visual-upgrade-js">
        document.addEventListener('DOMContentLoaded', function () {
            const wrap = document.querySelector('.dcpo-wrap');
            if (!wrap || wrap.dataset.dcpmvuReady === '1') return;
            wrap.dataset.dcpmvuReady = '1';

            const base = <?php echo wp_json_encode($base); ?>;

            const phaseImages = {
                'sirovina': 'process-01-sirovina.webp',
                'soljenje': 'process-02-soljenje.webp',
                'rezanje': 'process-03-rezanje.webp',
                'mljevenje': 'process-04-mljevenje.webp',
                'mijesanje': 'process-05-mijesanje.webp',
                'odlezavanje-smjese': 'process-05a-odlezavanje-smjese.webp',
                'punjenje': 'process-06-punjenje.webp',
                'fermentacija': 'process-07-fermentacija.webp',
                'dimljenje': 'process-08-dimljenje.webp',
                'susenje': 'process-09-susenje.webp',
                'zrenje': 'process-10-zrenje.webp',
                'pakiranje': 'process-11-pakiranje.webp'
            };

            const hero = document.createElement('section');
            hero.className = 'dcpmvu-hero';
            hero.setAttribute('aria-label', 'Vizualni pregled procesa izrade');
            hero.innerHTML = `
                <div>
                    <span class="dcpmvu-eyebrow">Procesni vodič</span>
                    <h1>Od sirovine do zrelog proizvoda</h1>
                    <p>Proces izrade suhomesnatih proizvoda nije niz slučajnih koraka, nego lanac odluka. Svaka faza ima svoju ulogu: čuva sigurnost, oblikuje teksturu, razvija aromu i vodi proizvod prema stabilnom završetku.</p>
                    <div class="dcpmvu-hero-actions">
                        <a class="dcpmvu-btn dcpmvu-btn--dark" href="#dcpmvu-process-grid">Pregledaj faze</a>
                        <a class="dcpmvu-btn dcpmvu-btn--light" href="/proces-izrade/fermentacija/">Fermentacija i starter kulture</a>
                    </div>
                </div>
                <div class="dcpmvu-hero-visual">
                    <img class="dcpmvu-hero-main-img" src="${base}process-07-fermentacija.webp" alt="Proces izrade suhomesnatih proizvoda u kontroliranoj mikroklimi" loading="eager" decoding="async">
                    <div class="dcpmvu-mini-strip" aria-hidden="true">
                        <span><small>01</small>Sirovina</span>
                        <span><small>07</small>Fermentacija</span>
                        <span><small>09</small>Sušenje</span>
                        <span><small>11</small>Pakiranje</span>
                    </div>
                </div>
            `;

            wrap.prepend(hero);

            const cards = Array.from(wrap.querySelectorAll('.dcpo-card'));

            function dcpmvuRemovePhaseNumbers(card) {
                const numberPattern = /^(0?[1-9]|1[0-2]|05a|5a)$/i;

                Array.from(card.querySelectorAll('span, small, b, strong, em, div, p')).forEach(function(el) {
                    if (el.closest('.dcpmvu-card-image')) return;
                    if (el.classList.contains('dcpmvu-related-badge')) return;
                    if (el.querySelector('a, img, button')) return;

                    const txt = (el.textContent || '').trim();

                    if (!numberPattern.test(txt)) return;

                    // Ukloni čiste brojčane oznake faze gdje god se pojave u kartici.
                    el.remove();
                });

                // Dodatni osigurač: ako je broj ostao kao prvi tekstualni čvor u kartici.
                Array.from(card.childNodes).forEach(function(node) {
                    if (node.nodeType !== Node.TEXT_NODE) return;
                    const txt = (node.textContent || '').trim();
                    if (numberPattern.test(txt)) {
                        node.remove();
                    }
                });
            }

            cards.forEach(function(card) {
                dcpmvuRemovePhaseNumbers(card);
                if (card.querySelector('.dcpmvu-card-image')) return;

                const link = card.querySelector('a[href*="/proces-izrade/"]');
                if (!link) return;

                const href = link.getAttribute('href') || '';
                const slug = Object.keys(phaseImages).find(function(key) {
                    return href.includes('/' + key + '/');
                });

                if (!slug) return;

                const image = document.createElement('a');
                image.className = 'dcpmvu-card-image';
                image.href = href;
                image.setAttribute('aria-hidden', 'true');
                image.tabIndex = -1;
                image.innerHTML = `<img src="${base}${phaseImages[slug]}" alt="" loading="lazy" decoding="async">`;

                card.prepend(image);

                if (slug === 'fermentacija' && !card.querySelector('.dcpmvu-related-badge')) {
                    const h3 = card.querySelector('h3');
                    if (h3) {
                        const badge = document.createElement('span');
                        badge.className = 'dcpmvu-related-badge';
                        badge.textContent = 'Starter kulture';
                        h3.appendChild(badge);
                    }
                }
            });

            const gridAnchorTarget = wrap.querySelector('.dcpo-grid, [class*="grid"]');
            if (gridAnchorTarget && !document.getElementById('dcpmvu-process-grid')) {
                gridAnchorTarget.id = 'dcpmvu-process-grid';
            }


            function dcpmvuHardPolish() {
                // 1) Force readable bottom CTA colors.
                const bottom = wrap.querySelector('.dcpmvu-bottom-note');
                if (bottom) {
                    bottom.style.setProperty('background', '#101722', 'important');
                    bottom.style.setProperty('color', '#ffffff', 'important');

                    bottom.querySelectorAll('h1,h2,h3').forEach(function(el) {
                        el.style.setProperty('color', '#ffffff', 'important');
                        el.style.setProperty('opacity', '1', 'important');
                    });

                    bottom.querySelectorAll('p').forEach(function(el) {
                        el.style.setProperty('color', 'rgba(255,255,255,.86)', 'important');
                        el.style.setProperty('opacity', '1', 'important');
                    });
                }

                // 2) Remove leftover pure phase-number badges from all process cards.
                const numberPattern = /^(0?[1-9]|1[0-2]|05a|5a)$/i;

                Array.from(wrap.querySelectorAll('.dcpo-card')).forEach(function(card) {
                    Array.from(card.querySelectorAll('span, small, b, strong, em, div, p')).forEach(function(el) {
                        if (el.closest('.dcpmvu-card-image')) return;
                        if (el.classList.contains('dcpmvu-related-badge')) return;
                        if (el.querySelector('a, img, button, h1, h2, h3')) return;

                        const txt = (el.textContent || '').trim();
                        if (numberPattern.test(txt)) {
                            el.style.setProperty('display', 'none', 'important');
                            el.setAttribute('data-dcpmvu-number-hidden', '1');
                        }
                    });
                });
            }

            dcpmvuHardPolish();
            setTimeout(dcpmvuHardPolish, 150);
            setTimeout(dcpmvuHardPolish, 600);


            if (!wrap.querySelector('.dcpmvu-bottom-note')) {
                const note = document.createElement('section');
                note.className = 'dcpmvu-bottom-note';
                note.innerHTML = `
                    <div>
                        <h2>Svaka faza ima svoj rizik i svoje rješenje</h2>
                        <p>Dobro vođen proces nije stvar sreće. Ako se pojavi problem, važno je znati u kojoj je fazi nastao i koju mjeru treba odmah primijeniti.</p>
                    </div>
                    <a class="dcpmvu-btn dcpmvu-btn--light" href="/savjeti/">Otvori savjete</a>
                `;
                wrap.appendChild(note);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'dcpmvu_assets', 9999);
