<?php
/**
 * Plugin Name: Drycured Process Odlezavanje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/odlezavanje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpodl_enabled(): bool {
    return (bool) get_option('drycured_process_odlezavanje_enabled', 1);
}

function dcpodl_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/odlezavanje',
        'proces-izrade/odlezavanje-smjese',
    ], true);
}

function dcpodl_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpodl_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/odlezavanje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpodl_render(): string {
    if (!dcpodl_enabled()) {
        return '';
    }

    $hero_img = dcpodl_image_url('odlezavanje-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcpo-odlezavanje" class="dcpo-wrap" aria-label="Odležavanje smjese za suhomesnate proizvode">

        <section class="dcpo-hero">
            <div class="dcpo-hero-copy">
                <span class="dcpo-eyebrow">Faza 06 — odležavanje</span>
                <h1>Odležavanje</h1>
                <p class="dcpo-lead">
                    Odležavanje smjese nije pasivno čekanje. To je kratka, kontrolirana faza u kojoj se smjesa
                    smiruje, sol i začini ravnomjernije raspoređuju, a vezivnost postaje stabilnija prije punjenja.
                    Ako se odležavanje vodi pretoplo ili predugo, ono više ne pomaže nego otvara prostor problemima.
                </p>

                <div class="dcpo-actions">
                    <a href="#dcpo-simulator">Otvori procjenu odležavanja</a>
                    <a href="<?php echo esc_url(dcpodl_page_url('proces-izrade/punjenje')); ?>">Sljedeća faza: Punjenje</a>
                </div>

                <div class="dcpo-mini">
                    <div><span>cilj</span><strong>smirena smjesa</strong></div>
                    <div><span>rizik</span><strong>toplina i čekanje</strong></div>
                    <div><span>kontrola</span><strong>vrijeme + hladnoća</strong></div>
                </div>
            </div>

            <div class="dcpo-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Odležavanje smjese za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpo-visual-overlay">
                    <span>kratka stabilizacija</span>
                    <h2>Dobro odležavanje smjesu smiruje; loše odležavanje samo joj daje vremena da se pokvari.</h2>
                </div>

                <div class="dcpo-hero-points">
                    <div><span>temperatura</span><strong>hladno</strong></div>
                    <div><span>vrijeme</span><strong>kontrolirano</strong></div>
                    <div><span>posuda</span><strong>zaštićena</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpo-simulator" class="dcpo-simulator">
            <div class="dcpo-head">
                <span class="dcpo-eyebrow">Aktivni alat</span>
                <h2>Procjena rizika odležavanja smjese</h2>
                <p>
                    Ovaj edukativni alat pokazuje kako temperatura, trajanje, vezivnost smjese, pokrivenost posude
                    i plan nastavka utječu na sigurnost i kvalitetu. Odležavanje mora imati svrhu; čekanje bez plana
                    nije tehnologija, nego lutrija.
                </p>
            </div>

            <div class="dcpo-sim-shell">
                <div class="dcpo-controls">
                    <h3>Postavi uvjete odležavanja</h3>

                    <div class="dcpo-control">
                        <label>Temperatura smjese <b id="dcpo-temp-val">3 °C</b></label>
                        <input id="dcpo-temp" type="range" min="0" max="12" value="3" step="1">
                    </div>

                    <div class="dcpo-control">
                        <label>Trajanje odležavanja <b id="dcpo-time-val">6 h</b></label>
                        <input id="dcpo-time" type="range" min="0" max="48" value="6" step="1">
                    </div>

                    <div class="dcpo-control">
                        <label>Vezivnost smjese <b id="dcpo-bind-val">dobra</b></label>
                        <input id="dcpo-bind" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpo-control">
                        <label>Pokrivenost posude <b id="dcpo-cover-val">dobra</b></label>
                        <input id="dcpo-cover" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpo-control">
                        <label>Spremnost za punjenje <b id="dcpo-ready-val">spremno</b></label>
                        <input id="dcpo-ready" type="range" min="1" max="4" value="4" step="1">
                    </div>

                    <div class="dcpo-note">
                        Kratko odležavanje može pomoći smjesi, ali samo ako je smjesa hladna, pokrivena i ako je sljedeća
                        faza već pripremljena. Nije cilj zaboraviti posudu u hladnjaku.
                    </div>
                </div>

                <div class="dcpo-output">
                    <div class="dcpo-status">
                        <span>procjena</span>
                        <h3 id="dcpo-status-title">Dobri uvjeti za odležavanje</h3>
                        <p id="dcpo-status-text">
                            Smjesa je hladna, posuda je zaštićena i odležavanje može pomoći stabilizaciji prije punjenja.
                        </p>
                    </div>

                    <div class="dcpo-risk-bars">
                        <div class="dcpo-risk">
                            <label>Rizik toplog čekanja <span id="dcpo-warm-num">0/100</span></label>
                            <i><b id="dcpo-warm"></b></i>
                        </div>

                        <div class="dcpo-risk">
                            <label>Rizik površinskog isušivanja <span id="dcpo-dry-num">0/100</span></label>
                            <i><b id="dcpo-dry"></b></i>
                        </div>

                        <div class="dcpo-risk">
                            <label>Rizik slabe vezivnosti <span id="dcpo-bindrisk-num">0/100</span></label>
                            <i><b id="dcpo-bindrisk"></b></i>
                        </div>

                        <div class="dcpo-risk">
                            <label>Rizik zastoja procesa <span id="dcpo-delay-num">0/100</span></label>
                            <i><b id="dcpo-delay"></b></i>
                        </div>
                    </div>

                    <div class="dcpo-advice" id="dcpo-advice">
                        Nastavi odležavanje hladno i planirano; punjenje pripremi prije isteka vremena.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpo-principles">
            <div class="dcpo-head">
                <span class="dcpo-eyebrow">Što se događa</span>
                <h2>Odležavanje pomaže samo kada je pod kontrolom</h2>
            </div>

            <div class="dcpo-card-grid">
                <article>
                    <b>01</b>
                    <h3>Ujednačavanje smjese</h3>
                    <p>Sol, začini i vlaga imaju vremena ravnomjernije se rasporediti kroz smjesu.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Smirivanje strukture</h3>
                    <p>Nakon miješanja smjesa se može stabilizirati prije punjenja, ali samo dok ostaje hladna.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Zaštita površine</h3>
                    <p>Posuda mora biti pokrivena jer površinsko isušivanje kasnije otežava punjenje i vezanje.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Plan nastavka</h3>
                    <p>Odležavanje završava punjenjem. Ako punjenje nije spremno, smjesa nepotrebno čeka.</p>
                </article>
            </div>
        </section>

        <section class="dcpo-process">
            <div class="dcpo-head">
                <span class="dcpo-eyebrow">Praktični redoslijed</span>
                <h2>Kako odležavanje voditi bez gubitka kontrole</h2>
            </div>

            <div class="dcpo-step-grid">
                <article>
                    <em>01</em>
                    <h3>Procijeni smjesu</h3>
                    <p>Smjesa mora biti hladna, povezana i bez slobodne tekućine prije nego ide na odležavanje.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Zaštiti posudu</h3>
                    <p>Pokrij smjesu tako da ne upija mirise i ne isušuje površinu.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Odredi vrijeme</h3>
                    <p>Vrijeme mora biti zapisano i ograničeno. Ne smije ovisiti o sjećanju.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Pripremi punjenje</h3>
                    <p>Prije završetka odležavanja pripremi punilicu, crijeva i hladni radni tok.</p>
                </article>
            </div>
        </section>

        <section id="dcpo-checklist" class="dcpo-checklist">
            <div class="dcpo-head">
                <span class="dcpo-eyebrow">Kontrolna lista</span>
                <h2>Odležavanje smjese mora imati kraj</h2>
                <p>
                    Kontrolna lista vodi kroz početak, tijek i završetak odležavanja. Ako neka stavka nije u redu,
                    odmah se prikazuje što treba napraviti.
                </p>
            </div>

            <div class="dcpo-check-shell">
                <div class="dcpo-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije odležavanja</button>
                    <button type="button" data-tab="during">Tijekom odležavanja</button>
                    <button type="button" data-tab="after">Prije punjenja</button>
                </div>

                <div class="dcpo-check-panel">
                    <div class="dcpo-progress">
                        <div>
                            <strong id="dcpo-check-title">Prije odležavanja</strong>
                            <span id="dcpo-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpo-check-bar"></b></i>
                    </div>

                    <div id="dcpo-check-list" class="dcpo-check-list"></div>

                    <div class="dcpo-solutions">
                        <div class="dcpo-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpo-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpo-solutions-list" class="dcpo-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpo-problems">
            <div class="dcpo-head">
                <span class="dcpo-eyebrow">Problem → rješenje</span>
                <h2>Greške u odležavanju koje kasnije izgledaju kao problem punjenja</h2>
            </div>

            <div class="dcpo-problem-grid">
                <article>
                    <h3>Smjesa je postala topla</h3>
                    <p><strong>Uzrok:</strong> predugo čekanje izvan hladnog režima ili loša organizacija rada.</p>
                    <p><strong>Rješenje:</strong> vratiti smjesu na hladno i ne nastaviti punjenje dok nije stabilna.</p>
                </article>

                <article>
                    <h3>Površina smjese se isušila</h3>
                    <p><strong>Uzrok:</strong> nepokrivena posuda ili prejak protok zraka u hladnjaku.</p>
                    <p><strong>Rješenje:</strong> smjesu pravilno pokriti i prije punjenja ukloniti ili razraditi problematičan površinski sloj.</p>
                </article>

                <article>
                    <h3>Smjesa je izgubila vezivnost</h3>
                    <p><strong>Uzrok:</strong> loše miješanje, previše tekućine ili predugo/pretplo odležavanje.</p>
                    <p><strong>Rješenje:</strong> procijeniti može li se smjesa kratko hladno doraditi prije punjenja.</p>
                </article>

                <article>
                    <h3>Punjenje nije spremno</h3>
                    <p><strong>Uzrok:</strong> odležavanje je završilo, ali punilica, crijeva ili radni prostor nisu pripremljeni.</p>
                    <p><strong>Rješenje:</strong> prije završetka odležavanja pripremiti opremu, crijeva i hladan radni tok.</p>
                </article>
            </div>
        </section>

        <section class="dcpo-next">
            <div>
                <span class="dcpo-eyebrow">Sljedeća faza</span>
                <h2>Nakon odležavanja dolazi punjenje</h2>
                <p>
                    Smjesa na punjenje mora doći hladna, povezana i bez zračnih džepova. Ako se odležavanje pretvori
                    u neplanirano čekanje, punjenje samo prenosi problem u crijevo.
                </p>
            </div>

            <div class="dcpo-next-actions">
                <a href="<?php echo esc_url(dcpodl_page_url('proces-izrade/punjenje')); ?>">Otvori fazu Punjenje</a>
                <a href="<?php echo esc_url(dcpodl_page_url('proces-izrade/mijesanje')); ?>">Vrati se na Miješanje</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_odlezavanje', 'dcpodl_render');

function dcpodl_append_to_page($content) {
    static $added = false;

    if ($added || !dcpodl_enabled()) {
        return $content;
    }

    if (!dcpodl_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpo-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_odlezavanje]');
}
add_filter('the_content', 'dcpodl_append_to_page', 35);

function dcpodl_assets() {
    if (!dcpodl_is_page() || !dcpodl_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpo-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpo-wrap * { box-sizing: border-box; }

        .dcpo-eyebrow {
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

        .dcpo-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcpo-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpo-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpo-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpo-hero,
        .dcpo-simulator,
        .dcpo-principles,
        .dcpo-process,
        .dcpo-checklist,
        .dcpo-problems,
        .dcpo-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpo-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpo-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpo-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcpo-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpo-actions a,
        .dcpo-next-actions a {
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

        .dcpo-actions a:nth-child(2),
        .dcpo-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpo-mini,
        .dcpo-hero-points,
        .dcpo-card-grid,
        .dcpo-step-grid,
        .dcpo-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpo-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpo-mini div,
        .dcpo-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpo-mini span,
        .dcpo-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpo-mini strong,
        .dcpo-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpo-hero-visual {
            position: relative;
            min-height: 560px;
            overflow: hidden;
            border-radius: 34px;
            background:
                radial-gradient(circle at 35% 20%, rgba(241,216,137,.24), transparent 30%),
                radial-gradient(circle at 75% 75%, rgba(120,211,255,.14), transparent 32%),
                #101722;
            box-shadow: 0 30px 70px rgba(16,23,34,.24);
        }

        .dcpo-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpo-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpo-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcpo-visual-overlay span {
            display: inline-flex;
            margin-bottom: 14px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            color: #f1d889;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
        }

        .dcpo-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcpo-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpo-simulator,
        .dcpo-principles,
        .dcpo-process,
        .dcpo-checklist,
        .dcpo-problems,
        .dcpo-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpo-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpo-sim-shell,
        .dcpo-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpo-controls,
        .dcpo-output,
        .dcpo-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpo-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpo-control {
            margin-bottom: 18px;
        }

        .dcpo-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpo-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpo-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpo-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpo-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpo-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpo-status h3,
        .dcpo-status p {
            color: #fff;
        }

        .dcpo-status p { opacity: .75; }

        .dcpo-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpo-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpo-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpo-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpo-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpo-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpo-card-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpo-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpo-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpo-card-grid article,
        .dcpo-step-grid article,
        .dcpo-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
        }

        .dcpo-card-grid b,
        .dcpo-step-grid em {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 52px;
            height: 30px;
            margin-bottom: 14px;
            border-radius: 999px;
            background: #101722;
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            font-style: normal;
        }

        .dcpo-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpo-tabs button {
            width: 100%;
            text-align: left;
            border: 1px solid rgba(16,23,34,.10);
            border-radius: 18px;
            padding: 15px 16px;
            background: #fff;
            color: var(--ink);
            font-weight: 950;
            cursor: pointer;
        }

        .dcpo-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpo-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpo-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpo-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpo-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpo-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpo-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpo-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpo-check-item {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr);
            gap: 12px;
            align-items: start;
            padding: 14px;
            border-radius: 18px;
            background: rgba(16,23,34,.035);
            border: 1px solid rgba(16,23,34,.07);
            cursor: pointer;
        }

        .dcpo-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcpo-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpo-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpo-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpo-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpo-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpo-solutions-head span {
            display: inline-flex;
            width: max-content;
            min-height: 28px;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(182,138,58,.14);
            color: #76551e;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .10em;
            text-transform: uppercase;
        }

        .dcpo-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpo-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpo-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpo-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpo-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpo-solution-card p + p {
            margin-top: 8px;
        }

        .dcpo-solution-card strong {
            color: #76551e;
        }

        .dcpo-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpo-next h2,
        .dcpo-next p {
            color: #fff;
        }

        .dcpo-next p { opacity: .78; }

        .dcpo-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1000px) {
            .dcpo-hero,
            .dcpo-sim-shell,
            .dcpo-check-shell,
            .dcpo-next {
                grid-template-columns: 1fr;
            }

            .dcpo-card-grid,
            .dcpo-step-grid,
            .dcpo-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcpo-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpo-hero,
            .dcpo-hero-copy,
            .dcpo-hero-visual,
            .dcpo-simulator,
            .dcpo-principles,
            .dcpo-process,
            .dcpo-checklist,
            .dcpo-problems,
            .dcpo-next {
                border-radius: 24px;
            }

            .dcpo-mini,
            .dcpo-hero-points,
            .dcpo-card-grid,
            .dcpo-step-grid,
            .dcpo-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcpo-hero-visual {
                min-height: 540px;
            }

            .dcpo-progress > div,
            .dcpo-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpo-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpo-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpo-wrap')) {
                            child.style.display = 'none';
                        }
                    });
                }
            }

            function clamp(v, min, max) {
                return Math.max(min, Math.min(max, v));
            }

            function n(id) {
                return parseFloat(document.getElementById(id).value);
            }

            function labelQuality(v) {
                return v === 4 ? 'odlična' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'kritična';
            }

            function labelCover(v) {
                return v === 4 ? 'odlična' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'otvoreno';
            }

            function labelReady(v) {
                return v === 4 ? 'spremno' : v === 3 ? 'uglavnom' : v === 2 ? 'kasni' : 'nije spremno';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpo-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcpo-temp');
                const time = n('dcpo-time');
                const bind = n('dcpo-bind');
                const cover = n('dcpo-cover');
                const ready = n('dcpo-ready');

                document.getElementById('dcpo-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpo-time-val').textContent = time.toFixed(0) + ' h';
                document.getElementById('dcpo-bind-val').textContent = labelQuality(bind);
                document.getElementById('dcpo-cover-val').textContent = labelCover(cover);
                document.getElementById('dcpo-ready-val').textContent = labelReady(ready);

                let warm = 10 + Math.max(0, temp - 4) * 9 + Math.max(0, time - 18) * 1.3;
                let dry = 10 + (4 - cover) * 20 + Math.max(0, time - 12) * 1.2;
                let bindRisk = 12 + (4 - bind) * 22 + Math.max(0, temp - 6) * 5 + Math.max(0, time - 24) * 1.4;
                let delay = 8 + (4 - ready) * 18 + Math.max(0, time - 12) * 1.2;

                warm = clamp(warm, 0, 100);
                dry = clamp(dry, 0, 100);
                bindRisk = clamp(bindRisk, 0, 100);
                delay = clamp(delay, 0, 100);

                setBar('dcpo-warm', 'dcpo-warm-num', warm, true);
                setBar('dcpo-dry', 'dcpo-dry-num', dry, true);
                setBar('dcpo-bindrisk', 'dcpo-bindrisk-num', bindRisk, true);
                setBar('dcpo-delay', 'dcpo-delay-num', delay, true);

                let title = 'Dobri uvjeti za odležavanje';
                let text = 'Smjesa je hladna, posuda je zaštićena i odležavanje može pomoći stabilizaciji prije punjenja.';
                let advice = 'Nastavi odležavanje hladno i planirano; punjenje pripremi prije isteka vremena.';

                if (warm > 65) {
                    title = 'Rizik toplog čekanja';
                    text = 'Odležavanje se pretvara u rizično čekanje jer temperatura ili trajanje izlaze iz sigurnog okvira.';
                    advice = 'Vrati smjesu na hladno, skrati čekanje i ne puni dok smjesa nije stabilna.';
                } else if (dry > 65) {
                    title = 'Rizik površinskog isušivanja';
                    text = 'Otvorena ili slabo pokrivena posuda može isušiti površinu smjese.';
                    advice = 'Pokrij posudu i spriječi strujanje zraka po površini smjese.';
                } else if (bindRisk > 65) {
                    title = 'Rizik slabe vezivnosti';
                    text = 'Smjesa nije dovoljno povezana ili gubi stabilnost tijekom čekanja.';
                    advice = 'Procijeni smjesu prije punjenja; po potrebi kratko doradi hladno, bez zagrijavanja.';
                } else if (delay > 65) {
                    title = 'Rizik zastoja procesa';
                    text = 'Punjenje nije spremno, pa smjesa može nepotrebno čekati.';
                    advice = 'Pripremi punilicu, crijeva i radni prostor prije završetka odležavanja.';
                }

                document.getElementById('dcpo-status-title').textContent = title;
                document.getElementById('dcpo-status-text').textContent = text;
                document.getElementById('dcpo-advice').textContent = advice;
            }

            ['dcpo-temp', 'dcpo-time', 'dcpo-bind', 'dcpo-cover', 'dcpo-ready'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije odležavanja',
                    items: [
                        ['Smjesa je hladna', 'Smjesa ne smije biti topla ili maziva.'],
                        ['Smjesa je povezana', 'Mora držati oblik i ne smije imati slobodnu tekućinu.'],
                        ['Posuda je čista', 'Odležavanje ne smije unositi novi higijenski rizik.'],
                        ['Posuda se može pokriti', 'Površina smjese mora biti zaštićena.'],
                        ['Vrijeme je određeno', 'Odležavanje mora imati početak i kraj.'],
                        ['Punjenje je planirano', 'Sljedeća faza ne smije biti improvizacija.']
                    ]
                },
                during: {
                    title: 'Tijekom odležavanja',
                    items: [
                        ['Temperatura je stabilna', 'Smjesa ostaje u hladnom režimu.'],
                        ['Posuda je pokrivena', 'Nema isušivanja površine ni upijanja mirisa.'],
                        ['Vrijeme se prati', 'Ne oslanja se na sjećanje.'],
                        ['Nema slobodne tekućine', 'Tekućina se ne odvaja na dnu posude.'],
                        ['Smjesa ne mijenja miris', 'Svaka sumnjiva promjena je znak za oprez.'],
                        ['Punjenje se priprema na vrijeme', 'Oprema i crijeva ne čekaju zadnji trenutak.']
                    ]
                },
                after: {
                    title: 'Prije punjenja',
                    items: [
                        ['Smjesa je i dalje hladna', 'Punjenje ne počinje s toplom smjesom.'],
                        ['Površina nije isušena', 'Nema kore ili osušenog sloja koji kvari punjenje.'],
                        ['Smjesa drži oblik', 'Vezivnost je očuvana.'],
                        ['Nema zračnih džepova', 'Smjesa se može sabiti prije punjenja.'],
                        ['Bilješka šarže je dopunjena', 'Upisano je trajanje, temperatura i opažanja.'],
                        ['Punilica i crijeva su spremni', 'Sljedeća faza može krenuti bez čekanja.']
                    ]
                }
            };

            const solutions = {
                'Smjesa je hladna': ['Topla smjesa brzo gubi strukturu.', 'Vrati smjesu na hlađenje i ne počinji odležavanje dok nije stabilna.'],
                'Smjesa je povezana': ['Rastresita smjesa kasnije može stvarati šupljine.', 'Kratko doradi miješanje hladno prije odležavanja.'],
                'Posuda je čista': ['Prljava posuda kvari dobar proces.', 'Operi, osuši i pripremi čistu posudu.'],
                'Posuda se može pokriti': ['Nepokrivena smjesa se isušuje i upija mirise.', 'Pripremi poklopac ili prikladnu zaštitu.'],
                'Vrijeme je određeno': ['Bez vremena odležavanje postaje zaboravljeno čekanje.', 'Zapiši početak i planirani kraj.'],
                'Punjenje je planirano': ['Ako punjenje nije spremno, smjesa nepotrebno čeka.', 'Pripremi plan punjenja prije početka odležavanja.'],

                'Temperatura je stabilna': ['Toplina povećava rizik i slabi strukturu.', 'Provjeri hladnjak i smanji trajanje čekanja.'],
                'Posuda je pokrivena': ['Otvorena površina se isušuje.', 'Pokrij posudu bez odgađanja.'],
                'Vrijeme se prati': ['Predugo odležavanje stvara nove probleme.', 'Postavi podsjetnik i zapiši vrijeme.'],
                'Nema slobodne tekućine': ['Odvojena tekućina pokazuje problem vezivnosti ili dodavanja tekućine.', 'Prije punjenja procijeni smjesu i po potrebi kratko doradi hladno.'],
                'Smjesa ne mijenja miris': ['Promjena mirisa je znak za oprez.', 'Ako je miris sumnjiv, proces se zaustavlja.'],
                'Punjenje se priprema na vrijeme': ['Kašnjenje nakon odležavanja povećava rizik.', 'Pripremi punilicu i crijeva prije završetka odležavanja.'],

                'Smjesa je i dalje hladna': ['Topla smjesa se loše puni i lakše razmazuje mast.', 'Vrati smjesu na hladno prije punjenja.'],
                'Površina nije isušena': ['Kora ili suhi sloj stvaraju neravnomjernost u punjenju.', 'Ukloni ili razradi problematičan sloj prema stanju smjese.'],
                'Smjesa drži oblik': ['Slaba vezivnost vodi prema šupljinama.', 'Ne puni dok smjesa nije dovoljno povezana.'],
                'Nema zračnih džepova': ['Zrak u smjesi postaje šupljina u proizvodu.', 'Sabij smjesu prije punjenja i puni bez prekida.'],
                'Bilješka šarže je dopunjena': ['Bez zapisa nema ponovljivosti.', 'Upiši vrijeme, temperaturu i opažanja.'],
                'Punilica i crijeva su spremni': ['Čekanje nakon odležavanja kvari hladan tok.', 'Pripremi sve prije vađenja smjese iz hladnog režima.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpo-check-list');
                document.getElementById('dcpo-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_odlezavanje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpo-check-item ${checked ? 'is-checked' : ''}">
                            <input type="checkbox" data-index="${index}" ${checked ? 'checked' : ''}>
                            <span>
                                <strong>${item[0]}</strong>
                                <span>${item[1]}</span>
                            </span>
                        </label>
                    `;
                }).join('');

                list.querySelectorAll('input[type="checkbox"]').forEach(function (box) {
                    box.addEventListener('change', function () {
                        const k = 'drycured_odlezavanje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpo-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpo-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpo-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpo-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpo-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpo-solutions-title');
                const solList = document.getElementById('dcpo-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpo-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi prema punjenju bez nepotrebnog čekanja.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpo-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpo-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpo-tabs button').forEach(function (b) {
                        b.classList.remove('is-active');
                    });
                    button.classList.add('is-active');
                    renderChecklist(button.getAttribute('data-tab'));
                });
            });

            renderChecklist(activeTab);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpodl_assets', 120);
