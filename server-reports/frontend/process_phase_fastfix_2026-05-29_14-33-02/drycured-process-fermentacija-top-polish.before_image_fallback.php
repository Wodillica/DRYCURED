<?php
/**
 * Plugin Name: Drycured Process Fermentacija Top Polish
 * Description: Modernizira gornji uvodni dio stranice /proces-izrade/fermentacija/.
 * Version: 0.1.1
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpftp_enabled(): bool {
    return (bool) get_option('drycured_process_fermentacija_top_polish_enabled', 1);
}

function dcpftp_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/fermentacija';
}

function dcpftp_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpftp_assets() {
    if (!dcpftp_is_page() || !dcpftp_enabled()) {
        return;
    }

    $starter_url = dcpftp_page_url('starter-kulture');
    $procesi_url = dcpftp_page_url('proces-izrade');
    ?>
    <style>
        .dcpftp-hidden {
            display: none !important;
        }

        .dcpftp-top {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 36px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpftp-top * {
            box-sizing: border-box;
        }

        .dcpftp-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
        }

        .dcpftp-copy,
        .dcpftp-image-panel,
        .dcpftp-overview {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpftp-copy {
            padding: clamp(32px, 4.5vw, 58px);
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpftp-eyebrow {
            display: inline-flex;
            width: max-content;
            min-height: 30px;
            align-items: center;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(182,138,58,.14);
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpftp-copy h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(44px, 6vw, 78px);
            line-height: .96;
            letter-spacing: -.06em;
        }

        .dcpftp-lead {
            margin: 0;
            color: #2f3943;
            font-size: clamp(18px, 2vw, 22px);
            line-height: 1.58;
        }

        .dcpftp-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpftp-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 12px 18px;
            border-radius: 999px;
            background: #101722;
            color: #fff !important;
            text-decoration: none !important;
            font-weight: 900;
            box-shadow: 0 16px 34px rgba(16,23,34,.14);
        }

        .dcpftp-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpftp-mini {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpftp-mini div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpftp-mini span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpftp-mini strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpftp-image-panel {
            position: relative;
            min-height: 560px;
            overflow: hidden;
            background: #101722;
        }

        .dcpftp-image-panel img {
            position: absolute;
            inset: 0;
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center center;
            transform: scale(1.02);
        }

        .dcpftp-image-panel::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.48)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpftp-image-badge {
            position: absolute;
            z-index: 2;
            left: 24px;
            top: 24px;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 10px 13px;
            border-radius: 999px;
            background: rgba(255,255,255,.84);
            backdrop-filter: blur(14px);
            color: #101722;
            font-size: 12px;
            font-weight: 950;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dcpftp-image-badge i {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #b68a3a;
            box-shadow: 0 0 0 5px rgba(182,138,58,.16);
        }

        .dcpftp-params {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpftp-param {
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,.28);
            box-shadow: 0 18px 38px rgba(16,23,34,.16);
        }

        .dcpftp-param span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .dcpftp-param strong {
            color: #101722;
            font-size: 17px;
            font-variant-numeric: tabular-nums;
        }

        .dcpftp-overview {
            margin-top: 28px;
            padding: clamp(26px, 4vw, 40px);
            display: grid;
            grid-template-columns: .78fr 1.22fr;
            gap: 26px;
            align-items: start;
        }

        .dcpftp-overview h2 {
            margin: 14px 0 0;
            color: var(--ink);
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.04;
            letter-spacing: -.045em;
        }

        .dcpftp-overview-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .dcpftp-overview-grid article {
            padding: 18px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpftp-overview-grid b {
            display: inline-flex;
            min-width: 42px;
            height: 28px;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            border-radius: 999px;
            background: #101722;
            color: #fff;
            font-size: 12px;
        }

        .dcpftp-overview-grid h3 {
            margin: 0 0 8px;
            color: var(--ink);
            font-size: 18px;
            line-height: 1.2;
        }

        .dcpftp-overview-grid p {
            margin: 0;
            color: var(--muted);
            font-size: 14.5px;
            line-height: 1.6;
        }

        @media (max-width: 1000px) {
            .dcpftp-hero,
            .dcpftp-overview {
                grid-template-columns: 1fr;
            }

            .dcpftp-image-panel {
                min-height: 460px;
            }
        }

        @media (max-width: 680px) {
            .dcpftp-top {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpftp-copy,
            .dcpftp-image-panel,
            .dcpftp-overview {
                border-radius: 24px;
            }

            .dcpftp-copy {
                padding: 28px;
            }

            .dcpftp-copy h1 {
                font-size: clamp(42px, 13vw, 58px);
            }

            .dcpftp-mini,
            .dcpftp-params,
            .dcpftp-overview-grid {
                grid-template-columns: 1fr;
            }

            .dcpftp-image-panel {
                min-height: 560px;
            }

            .dcpftp-params {
                left: 16px;
                right: 16px;
                bottom: 16px;
            }

            .dcpftp-image-badge {
                left: 16px;
                top: 16px;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modern = document.querySelector('.dcpf-wrap');
            if (!modern || document.querySelector('.dcpftp-top')) {
                return;
            }

            const entry = modern.closest('.entry-content') || document.querySelector('.entry-content');
            if (!entry) {
                return;
            }

            const children = Array.from(entry.children);
            const before = [];

            for (const child of children) {
                if (child === modern) break;
                before.push(child);
            }

            const img = before.map(el => el.querySelector ? el.querySelector('img') : null).find(Boolean);
            const imgSrc = img ? (img.currentSrc || img.src || img.getAttribute('data-src') || '') : '';

            before.forEach(function (el) {
                el.classList.add('dcpftp-hidden');
            });

            const section = document.createElement('section');
            section.className = 'dcpftp-top';
            section.innerHTML = `
                <div class="dcpftp-hero">
                    <div class="dcpftp-copy">
                        <span class="dcpftp-eyebrow">Faza 07 — fermentacija</span>
                        <h1>Fermentacija</h1>
                        <p class="dcpftp-lead">
                            Mikroklima, mikroflora i vrijeme ovdje počinju oblikovati kiselost,
                            boju, aromu i stabilnost trajnih kobasica i salama.
                        </p>

                        <div class="dcpftp-actions">
                            <a href="#dcpf-fermentacija">Otvori praktični vodič</a>
                            <a href="<?php echo esc_url($starter_url); ?>">Starter kulture</a>
                        </div>

                        <div class="dcpftp-mini">
                            <div><span>cilj</span><strong>sigurniji početak</strong></div>
                            <div><span>rizik</span><strong>prebrzo sušenje</strong></div>
                            <div><span>kontrola</span><strong>temperatura + RH</strong></div>
                        </div>
                    </div>

                    <div class="dcpftp-image-panel">
                        ${imgSrc ? `<img src="${imgSrc}" alt="Fermentacija trajnih kobasica u kontroliranoj mikroklimi">` : ``}
                        <div class="dcpftp-image-badge"><i></i> aktivna faza</div>

                        <div class="dcpftp-params">
                            <div class="dcpftp-param"><span>temp.</span><strong>18–24 °C</strong></div>
                            <div class="dcpftp-param"><span>vlaga</span><strong>90–95 %</strong></div>
                            <div class="dcpftp-param"><span>vrijeme</span><strong>24–72 h</strong></div>
                            <div class="dcpftp-param"><span>pH</span><strong>4,9–5,3</strong></div>
                        </div>
                    </div>
                </div>

                <div class="dcpftp-overview">
                    <div>
                        <span class="dcpftp-eyebrow">Brzi pregled</span>
                        <h2>Što treba razumjeti prije nastavka?</h2>
                    </div>

                    <div class="dcpftp-overview-grid">
                        <article>
                            <b>01</b>
                            <h3>Fermentacija vodi smjer</h3>
                            <p>Ona nije samo mirovanje proizvoda, nego početak mikrobiološke stabilizacije.</p>
                        </article>
                        <article>
                            <b>02</b>
                            <h3>Površina ne smije pobjeći</h3>
                            <p>Ako se površina prerano osuši, jezgra kasni i kasnije zrenje postaje problem.</p>
                        </article>
                        <article>
                            <b>03</b>
                            <h3>Starter nije čarolija</h3>
                            <p>Kultura pomaže samo ako su sirovina, higijena i mikroklima pod kontrolom.</p>
                        </article>
                        <article>
                            <b>04</b>
                            <h3>Bilješke su alat</h3>
                            <p>Temperatura, vlaga, trajanje i rezultat moraju se zapisivati ako želiš ponovljivost.</p>
                        </article>
                    </div>
                </div>
            `;

            modern.insertAdjacentElement('beforebegin', section);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpftp_assets', 160);
