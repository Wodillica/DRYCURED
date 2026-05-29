<?php
/**
 * Plugin Name: Drycured Process Fermentacija Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/fermentacija/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpf_enabled(): bool {
    return (bool) get_option('drycured_process_fermentacija_enabled', 1);
}

function dcpf_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/fermentacija';
}

function dcpf_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpf_render(): string {
    if (!dcpf_enabled()) {
        return '';
    }

    ob_start();
    ?>
    <section id="dcpf-fermentacija" class="dcpf-wrap" aria-label="Fermentacija u proizvodnji trajnih kobasica i salama">

        <section class="dcpf-hero-panel">
            <div class="dcpf-copy">
                <span class="dcpf-eyebrow">Faza 07 — fermentacija</span>
                <h2>Fermentacija nije čekanje, nego vođenje procesa</h2>
                <p>
                    Fermentacija je faza u kojoj se trajne kobasice i salame počinju mikrobiološki stabilizirati.
                    U njoj se razvijaju kiselost, boja, aroma i sigurnost proizvoda. Ako se ova faza vodi naslijepo,
                    kasnije sušenje i zrenje često samo prikriju problem — ne riješe ga.
                </p>
                <p>
                    U kućnoj proizvodnji najvažnije je razumjeti četiri stvari: temperaturu, relativnu vlagu,
                    vrijeme i smjer pada pH vrijednosti. Starter kultura može pomoći, ali ne zamjenjuje higijenu,
                    dobru sirovinu i pravilnu mikroklimu.
                </p>
            </div>

            <div class="dcpf-dashboard" aria-label="Tipični parametri fermentacije">
                <div class="dcpf-dashboard-head">
                    <span>Tipični raspon</span>
                    <strong>trajna kobasica</strong>
                </div>

                <div class="dcpf-meter">
                    <label>Temperatura <strong>18–24 °C</strong></label>
                    <i style="--w:74%"></i>
                    <small>početno ponekad 24–26 °C za brži start blagih stilova</small>
                </div>

                <div class="dcpf-meter">
                    <label>Relativna vlaga <strong>90–95 %</strong></label>
                    <i style="--w:86%"></i>
                    <small>pomaže da površina ne zatvori proizvod prerano</small>
                </div>

                <div class="dcpf-meter">
                    <label>Vrijeme <strong>24–72 h</strong></label>
                    <i style="--w:62%"></i>
                    <small>ovisno o šećeru, kulturi, promjeru i temperaturi</small>
                </div>

                <div class="dcpf-meter">
                    <label>Ciljani pH <strong>4,9–5,3</strong></label>
                    <i style="--w:68%"></i>
                    <small>blaži stilovi traže strožu kontrolu ostalih faktora</small>
                </div>
            </div>
        </section>

        <section class="dcpf-section">
            <div class="dcpf-head">
                <span class="dcpf-eyebrow">Što se događa</span>
                <h2>Mikroflora počinje oblikovati proizvod</h2>
                <p>
                    Fermentacija nije jedan mikroorganizam i jedna reakcija. To je suradnja više skupina
                    mikroorganizama, uvjeta prostora i sastava nadjeva.
                </p>
            </div>

            <div class="dcpf-micro-grid">
                <article>
                    <b>LAB</b>
                    <h3>Bakterije mliječne kiseline</h3>
                    <p>Snižavaju pH, stvaraju mliječnu kiselinu i usmjeravaju sigurniji početak fermentacije.</p>
                </article>

                <article>
                    <b>BOJA</b>
                    <h3>Korisne koke</h3>
                    <p>Pomažu stabilizaciji boje i razvoju arome, osobito kod kontrolirane fermentacije.</p>
                </article>

                <article>
                    <b>POVRŠINA</b>
                    <h3>Plemenite plijesni</h3>
                    <p>Kod određenih salama pomažu zaštiti površine, kontroli sušenja i razvoju zrele arome.</p>
                </article>
            </div>
        </section>

        <section class="dcpf-flow">
            <div class="dcpf-head">
                <span class="dcpf-eyebrow">Praktični redoslijed</span>
                <h2>Kako voditi fermentaciju bez nagađanja</h2>
            </div>

            <div class="dcpf-flow-grid">
                <article>
                    <em>01</em>
                    <h3>Pripremi proizvod za start</h3>
                    <p>Omoti trebaju biti uredno napunjeni, bez zračnih džepova i bez dodirivanja među komadima.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Postavi mikroklimu</h3>
                    <p>Temperatura i vlaga moraju odgovarati tipu proizvoda, promjeru i kulturi ako se koristi.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Prati ponašanje površine</h3>
                    <p>Površina ne smije prebrzo otvrdnuti. Ako se zatvori prerano, jezgra kasni.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Prebaci u sušenje tek kad je faza stabilna</h3>
                    <p>Fermentacija mora imati smislen završetak prije jačeg sušenja i zrenja.</p>
                </article>
            </div>
        </section>

        <section class="dcpf-problems">
            <div class="dcpf-head">
                <span class="dcpf-eyebrow">Problem → rješenje</span>
                <h2>Najčešće greške u fermentaciji</h2>
            </div>

            <div class="dcpf-problem-grid">
                <article>
                    <h3>Spor pad pH</h3>
                    <p><strong>Uzrok:</strong> preniska temperatura, premalo dostupnog šećera, slaba ili pogrešno čuvana kultura.</p>
                    <p><strong>Rješenje:</strong> provjeriti temperaturu, doziranje, rok kulture i dostupnost fermentabilnog šećera.</p>
                </article>

                <article>
                    <h3>Prebrzo zakiseljavanje</h3>
                    <p><strong>Uzrok:</strong> previše dekstroze, previsoka temperatura ili prebrza kultura.</p>
                    <p><strong>Rješenje:</strong> sniziti temperaturu, smanjiti brzi šećer u sljedećoj šarži i birati blaži režim.</p>
                </article>

                <article>
                    <h3>Tvrda površina, mekana jezgra</h3>
                    <p><strong>Uzrok:</strong> preniska vlaga ili prejako strujanje zraka prije nego je proizvod stabiliziran.</p>
                    <p><strong>Rješenje:</strong> povećati RH, smanjiti strujanje i usporiti prijelaz prema sušenju.</p>
                </article>

                <article>
                    <h3>Šupljine i spužvasta tekstura</h3>
                    <p><strong>Uzrok:</strong> zrak u nadjevu, loše punjenje, plinotvorna mikroflora ili slab higijenski temelj.</p>
                    <p><strong>Rješenje:</strong> bolje izmiješati i puniti, izbosti zračne džepove i pojačati higijenu procesa.</p>
                </article>
            </div>
        </section>

        <section class="dcpf-starter-bridge">
            <div>
                <span class="dcpf-eyebrow">Povezana napredna tema</span>
                <h2>Starter kulture kao pomoć u kontroli</h2>
                <p>
                    Starter kulture mogu pomoći da fermentacija bude predvidljivija, osobito u kućnim uvjetima,
                    kod većeg promjera proizvoda ili kod proizvođača koji još nemaju stabilnu mikrofloru prostora.
                    Ne treba ih promatrati kao zamjenu za tradiciju, nego kao alat za sigurnije vođenje procesa.
                </p>
            </div>

            <div class="dcpf-actions">
                <a href="<?php echo esc_url(dcpf_page_url('starter-kulture')); ?>">Otvori stranicu Starter kulture</a>
                <a href="<?php echo esc_url(dcpf_page_url('proces-izrade')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

        <section class="dcpf-nav">
            <a href="<?php echo esc_url(dcpf_page_url('proces-izrade/punjenje')); ?>">← Prethodna faza: Punjenje</a>
            <a href="<?php echo esc_url(dcpf_page_url('proces-izrade/dimljenje')); ?>">Sljedeća faza: Dimljenje →</a>
        </section>

    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_fermentacija', 'dcpf_render');

function dcpf_append_to_page($content) {
    static $added = false;

    if ($added || !dcpf_enabled()) {
        return $content;
    }

    if (!dcpf_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpf-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_fermentacija]');
}
add_filter('the_content', 'dcpf_append_to_page', 35);

function dcpf_assets() {
    if (!dcpf_is_page() || !dcpf_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpf-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 40px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpf-wrap * {
            box-sizing: border-box;
        }

        .dcpf-eyebrow {
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

        .dcpf-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpf-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpf-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpf-hero-panel,
        .dcpf-section,
        .dcpf-flow,
        .dcpf-problems,
        .dcpf-starter-bridge,
        .dcpf-nav {
            border-radius: 32px;
            background: rgba(255,255,255,.68);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpf-hero-panel {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 430px;
            gap: 26px;
            align-items: stretch;
            padding: clamp(28px, 4vw, 46px);
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.22), transparent 34%),
                rgba(255,255,255,.68);
        }

        .dcpf-copy {
            max-width: 760px;
        }

        .dcpf-dashboard {
            min-height: 420px;
            padding: 26px;
            border-radius: 30px;
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 32%),
                #101722;
            color: #fff;
            box-shadow: 0 30px 70px rgba(16,23,34,.26);
        }

        .dcpf-dashboard-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            margin-bottom: 26px;
        }

        .dcpf-dashboard-head span {
            color: rgba(255,255,255,.70);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpf-dashboard-head strong {
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(241,216,137,.16);
            color: #f1d889;
            font-size: 12px;
        }

        .dcpf-meter {
            margin-bottom: 20px;
        }

        .dcpf-meter label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: rgba(255,255,255,.86);
            font-weight: 900;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .dcpf-meter label strong {
            color: #f1d889;
            font-variant-numeric: tabular-nums;
        }

        .dcpf-meter i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpf-meter i::before {
            content: "";
            display: block;
            width: var(--w);
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpf-meter small {
            display: block;
            margin-top: 7px;
            color: rgba(255,255,255,.62);
            font-size: 12px;
            line-height: 1.45;
        }

        .dcpf-section,
        .dcpf-flow,
        .dcpf-problems,
        .dcpf-starter-bridge,
        .dcpf-nav {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpf-head {
            max-width: 880px;
            margin-bottom: 24px;
        }

        .dcpf-micro-grid,
        .dcpf-flow-grid,
        .dcpf-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpf-micro-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dcpf-flow-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpf-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpf-micro-grid article,
        .dcpf-flow-grid article,
        .dcpf-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dcpf-micro-grid article:hover,
        .dcpf-flow-grid article:hover,
        .dcpf-problem-grid article:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 54px rgba(16,23,34,.13);
        }

        .dcpf-micro-grid b,
        .dcpf-flow-grid em {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 46px;
            height: 30px;
            margin-bottom: 14px;
            border-radius: 999px;
            background: #101722;
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            font-style: normal;
        }

        .dcpf-starter-bridge {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 310px;
            gap: 24px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.22), transparent 34%),
                rgba(255,255,255,.72);
        }

        .dcpf-actions {
            display: grid;
            gap: 12px;
        }

        .dcpf-actions a,
        .dcpf-nav a {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            background: #101722;
            color: #fff !important;
            text-decoration: none !important;
            font-weight: 900;
        }

        .dcpf-actions a:nth-child(2),
        .dcpf-nav a:first-child {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpf-nav {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        @media (max-width: 1000px) {
            .dcpf-hero-panel,
            .dcpf-starter-bridge {
                grid-template-columns: 1fr;
            }

            .dcpf-micro-grid,
            .dcpf-flow-grid,
            .dcpf-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcpf-wrap {
                padding: 0 14px;
            }

            .dcpf-hero-panel,
            .dcpf-section,
            .dcpf-flow,
            .dcpf-problems,
            .dcpf-starter-bridge,
            .dcpf-nav {
                border-radius: 24px;
            }

            .dcpf-micro-grid,
            .dcpf-flow-grid,
            .dcpf-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcpf-dashboard {
                min-height: auto;
                border-radius: 24px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'dcpf_assets', 120);
