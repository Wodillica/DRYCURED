<?php
/**
 * Plugin Name: Drycured Process Rezanje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/rezanje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpr_enabled(): bool {
    return (bool) get_option('drycured_process_rezanje_enabled', 1);
}

function dcpr_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/rezanje';
}

function dcpr_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpr_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/rezanje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpr_render(): string {
    if (!dcpr_enabled()) {
        return '';
    }

    $hero_img = dcpr_image_url('rezanje-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcpr-rezanje" class="dcpr-wrap" aria-label="Rezanje mesa za suhomesnate proizvode">

        <section class="dcpr-hero">
            <div class="dcpr-hero-copy">
                <span class="dcpr-eyebrow">Faza 02 — rezanje</span>
                <h1>Rezanje</h1>
                <p class="dcpr-lead">
                    Rezanje nije samo priprema mesa za daljnji rad. Pravilna veličina komada, hladnoća,
                    oštar nož i uklanjanje opni odlučuju hoće li soljenje biti ravnomjerno, mljevenje čisto,
                    a kasniji presjek stabilan.
                </p>

                <div class="dcpr-actions">
                    <a href="#dcpr-simulator">Otvori simulator rezanja</a>
                    <a href="<?php echo esc_url(dcpr_page_url('proces-izrade/soljenje')); ?>">Sljedeća faza: Soljenje</a>
                </div>

                <div class="dcpr-mini">
                    <div><span>cilj</span><strong>ujednačeni komadi</strong></div>
                    <div><span>rizik</span><strong>neravnomjerno soljenje</strong></div>
                    <div><span>kontrola</span><strong>hladnoća + oštar nož</strong></div>
                </div>
            </div>

            <div class="dcpr-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Rezanje mesa za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpr-visual-overlay">
                    <span>priprema strukture</span>
                    <h2>Neujednačen rez kasnije postaje neujednačeno soljenje, sušenje i tekstura.</h2>
                </div>

                <div class="dcpr-hero-points">
                    <div><span>nož</span><strong>oštar</strong></div>
                    <div><span>komadi</span><strong>ujednačeni</strong></div>
                    <div><span>rad</span><strong>hladan</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpr-simulator" class="dcpr-simulator">
            <div class="dcpr-head">
                <span class="dcpr-eyebrow">Aktivni alat</span>
                <h2>Simulator rizika lošeg rezanja</h2>
                <p>
                    Pomakni klizače i vidi kako temperatura mesa, oštrina noža, ujednačenost komada,
                    količina opni i vrijeme izvan hladnog režima utječu na daljnji proces.
                </p>
            </div>

            <div class="dcpr-sim-shell">
                <div class="dcpr-controls">
                    <h3>Postavi uvjete rezanja</h3>

                    <div class="dcpr-control">
                        <label>Temperatura mesa <b id="dcpr-temp-val">3 °C</b></label>
                        <input id="dcpr-temp" type="range" min="0" max="12" value="3" step="1">
                    </div>

                    <div class="dcpr-control">
                        <label>Oštrina noža <b id="dcpr-knife-val">85 %</b></label>
                        <input id="dcpr-knife" type="range" min="20" max="100" value="85" step="5">
                    </div>

                    <div class="dcpr-control">
                        <label>Ujednačenost komada <b id="dcpr-uniform-val">dobra</b></label>
                        <input id="dcpr-uniform" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpr-control">
                        <label>Opne i žile <b id="dcpr-trim-val">malo</b></label>
                        <input id="dcpr-trim" type="range" min="1" max="4" value="1" step="1">
                    </div>

                    <div class="dcpr-control">
                        <label>Vrijeme izvan hladnog <b id="dcpr-time-val">10 min</b></label>
                        <input id="dcpr-time" type="range" min="0" max="60" value="10" step="5">
                    </div>

                    <div class="dcpr-note">
                        Rezanje treba biti brzo, hladno i plansko. Dugo natezanje mesa na toplom stolu
                        često napravi više štete nego što se vidi u tom trenutku.
                    </div>
                </div>

                <div class="dcpr-output">
                    <div class="dcpr-status">
                        <span>procjena</span>
                        <h3 id="dcpr-status-title">Dobri uvjeti za rezanje</h3>
                        <p id="dcpr-status-text">
                            Meso je hladno, nož je oštar i komadi se mogu rezati ujednačeno.
                        </p>
                    </div>

                    <div class="dcpr-risk-bars">
                        <div class="dcpr-risk">
                            <label>Neravnomjerno soljenje <span id="dcpr-salt-num">0/100</span></label>
                            <i><b id="dcpr-salt"></b></i>
                        </div>

                        <div class="dcpr-risk">
                            <label>Oštećenje strukture <span id="dcpr-structure-num">0/100</span></label>
                            <i><b id="dcpr-structure"></b></i>
                        </div>

                        <div class="dcpr-risk">
                            <label>Zagrijavanje mesa <span id="dcpr-warm-num">0/100</span></label>
                            <i><b id="dcpr-warm"></b></i>
                        </div>

                        <div class="dcpr-risk">
                            <label>Problem u mljevenju <span id="dcpr-grind-num">0/100</span></label>
                            <i><b id="dcpr-grind"></b></i>
                        </div>
                    </div>

                    <div class="dcpr-advice" id="dcpr-advice">
                        Nastavi rezati u manjim serijama i vraćaj pripremljene komade na hladno.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpr-guide">
            <div class="dcpr-head">
                <span class="dcpr-eyebrow">Veličina reza</span>
                <h2>Dimenzija reza ovisi o sljedećem procesu</h2>
                <p>
                    Nije isto reže li se meso za veće komade, suhi pac, mljevenje ili sitniju smjesu.
                    Rez mora pomoći sljedećoj fazi, a ne stvarati dodatni problem.
                </p>
            </div>

            <div class="dcpr-card-grid">
                <article>
                    <b>2–3 cm</b>
                    <h3>Priprema za mljevenje</h3>
                    <p>Ujednačene kocke lakše ulaze u mlin i manje opterećuju nož, rešetku i puž.</p>
                </article>

                <article>
                    <b>trake</b>
                    <h3>Priprema za soljenje</h3>
                    <p>Kod većih komada rez mora omogućiti ravnomjerniji dodir soli s površinom mesa.</p>
                </article>

                <article>
                    <b>veći komadi</b>
                    <h3>Sušenje i zrenje</h3>
                    <p>Veći komadi traže pažljivije soljenje i dulji, mirniji tijek procesa.</p>
                </article>

                <article>
                    <b>čisti rub</b>
                    <h3>Manje oštećenja</h3>
                    <p>Oštar nož daje čist rez; tup nož gnječi vlakna i nepotrebno zagrijava površinu.</p>
                </article>
            </div>
        </section>

        <section class="dcpr-process">
            <div class="dcpr-head">
                <span class="dcpr-eyebrow">Praktični redoslijed</span>
                <h2>Kako rezati bez stvaranja skrivenih grešaka</h2>
            </div>

            <div class="dcpr-step-grid">
                <article>
                    <em>01</em>
                    <h3>Ohladi sirovinu</h3>
                    <p>Hladno meso se reže čišće, manje se gnječi i kasnije se lakše kontrolira u mljevenju.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Obreži opne i žile</h3>
                    <p>Ono što sada ne ukloniš kasnije može začepiti rešetku, smetati vezanju i kvariti teksturu.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Reži prema namjeni</h3>
                    <p>Komadi za mljevenje, soljenje ili veće proizvode nemaju istu idealnu dimenziju.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Vrati na hladno</h3>
                    <p>Narezano meso ne smije čekati na toplom. Sljedeća faza treba početi s hladnom sirovinom.</p>
                </article>
            </div>
        </section>

        <section id="dcpr-checklist" class="dcpr-checklist">
            <div class="dcpr-head">
                <span class="dcpr-eyebrow">Kontrolna lista</span>
                <h2>Rezanje priprema sve što slijedi</h2>
                <p>
                    Ova lista vodi korisnika kroz rezanje. Neoznačene stavke odmah daju objašnjenje i rješenje.
                </p>
            </div>

            <div class="dcpr-check-shell">
                <div class="dcpr-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije rezanja</button>
                    <button type="button" data-tab="during">Tijekom rezanja</button>
                    <button type="button" data-tab="after">Nakon rezanja</button>
                </div>

                <div class="dcpr-check-panel">
                    <div class="dcpr-progress">
                        <div>
                            <strong id="dcpr-check-title">Prije rezanja</strong>
                            <span id="dcpr-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpr-check-bar"></b></i>
                    </div>

                    <div id="dcpr-check-list" class="dcpr-check-list"></div>

                    <div class="dcpr-solutions">
                        <div class="dcpr-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpr-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpr-solutions-list" class="dcpr-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpr-problems">
            <div class="dcpr-head">
                <span class="dcpr-eyebrow">Problem → rješenje</span>
                <h2>Greške u rezanju koje kasnije izgledaju kao problem soljenja ili mljevenja</h2>
            </div>

            <div class="dcpr-problem-grid">
                <article>
                    <h3>Neujednačeni komadi</h3>
                    <p><strong>Uzrok:</strong> rezanje bez plana, preveliki komadi ili žurba.</p>
                    <p><strong>Rješenje:</strong> odrediti dimenziju prije rezanja i odvojiti komade prema namjeni.</p>
                </article>

                <article>
                    <h3>Zgnječena površina mesa</h3>
                    <p><strong>Uzrok:</strong> tup nož, toplo meso ili prejako pritiskanje.</p>
                    <p><strong>Rješenje:</strong> naoštriti nož, ohladiti meso i rezati čistim potezom.</p>
                </article>

                <article>
                    <h3>Opne i žile u smjesi</h3>
                    <p><strong>Uzrok:</strong> preskočeno obrezivanje prije mljevenja.</p>
                    <p><strong>Rješenje:</strong> ukloniti opne i žile prije daljnje obrade, osobito prije mljevenja.</p>
                </article>

                <article>
                    <h3>Meso predugo stoji na toplom</h3>
                    <p><strong>Uzrok:</strong> prevelika serija na stolu ili loša organizacija rada.</p>
                    <p><strong>Rješenje:</strong> raditi u manjim serijama i narezano meso odmah vraćati na hladno.</p>
                </article>
            </div>
        </section>

        <section class="dcpr-next">
            <div>
                <span class="dcpr-eyebrow">Sljedeća faza</span>
                <h2>Ujednačen rez vodi u ujednačeno soljenje</h2>
                <p>
                    Ako su komadi različiti, soljenje ne može djelovati jednako na sve dijelove. Zato je rezanje
                    stvarna priprema za kontrolirano soljenje, a ne samo “usputni” posao nožem.
                </p>
            </div>

            <div class="dcpr-next-actions">
                <a href="<?php echo esc_url(dcpr_page_url('proces-izrade/soljenje')); ?>">Otvori fazu Soljenje</a>
                <a href="<?php echo esc_url(dcpr_page_url('proces-izrade')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_rezanje', 'dcpr_render');

function dcpr_append_to_page($content) {
    static $added = false;

    if ($added || !dcpr_enabled()) {
        return $content;
    }

    if (!dcpr_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpr-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_rezanje]');
}
add_filter('the_content', 'dcpr_append_to_page', 35);

function dcpr_assets() {
    if (!dcpr_is_page() || !dcpr_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpr-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpr-wrap * { box-sizing: border-box; }

        .dcpr-eyebrow {
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

        .dcpr-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcpr-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpr-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpr-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpr-hero,
        .dcpr-simulator,
        .dcpr-guide,
        .dcpr-process,
        .dcpr-checklist,
        .dcpr-problems,
        .dcpr-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpr-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpr-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpr-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcpr-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpr-actions a,
        .dcpr-next-actions a {
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

        .dcpr-actions a:nth-child(2),
        .dcpr-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpr-mini,
        .dcpr-hero-points,
        .dcpr-card-grid,
        .dcpr-step-grid,
        .dcpr-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpr-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpr-mini div,
        .dcpr-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpr-mini span,
        .dcpr-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpr-mini strong,
        .dcpr-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpr-hero-visual {
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

        .dcpr-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpr-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpr-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcpr-visual-overlay span {
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

        .dcpr-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcpr-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpr-simulator,
        .dcpr-guide,
        .dcpr-process,
        .dcpr-checklist,
        .dcpr-problems,
        .dcpr-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpr-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpr-sim-shell,
        .dcpr-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpr-controls,
        .dcpr-output,
        .dcpr-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpr-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpr-control {
            margin-bottom: 18px;
        }

        .dcpr-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpr-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpr-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpr-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpr-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpr-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpr-status h3,
        .dcpr-status p {
            color: #fff;
        }

        .dcpr-status p { opacity: .75; }

        .dcpr-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpr-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpr-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpr-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpr-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpr-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpr-card-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpr-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpr-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpr-card-grid article,
        .dcpr-step-grid article,
        .dcpr-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dcpr-card-grid article:hover,
        .dcpr-step-grid article:hover,
        .dcpr-problem-grid article:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 54px rgba(16,23,34,.13);
        }

        .dcpr-card-grid b,
        .dcpr-step-grid em {
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

        .dcpr-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpr-tabs button {
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

        .dcpr-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpr-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpr-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpr-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpr-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpr-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpr-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpr-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpr-check-item {
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

        .dcpr-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcpr-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpr-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpr-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpr-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpr-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpr-solutions-head span {
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

        .dcpr-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpr-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpr-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpr-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpr-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpr-solution-card p + p {
            margin-top: 8px;
        }

        .dcpr-solution-card strong {
            color: #76551e;
        }

        .dcpr-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpr-next h2,
        .dcpr-next p {
            color: #fff;
        }

        .dcpr-next p {
            opacity: .78;
        }

        .dcpr-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1000px) {
            .dcpr-hero,
            .dcpr-sim-shell,
            .dcpr-check-shell,
            .dcpr-next {
                grid-template-columns: 1fr;
            }

            .dcpr-card-grid,
            .dcpr-step-grid,
            .dcpr-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcpr-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpr-hero,
            .dcpr-hero-copy,
            .dcpr-hero-visual,
            .dcpr-simulator,
            .dcpr-guide,
            .dcpr-process,
            .dcpr-checklist,
            .dcpr-problems,
            .dcpr-next {
                border-radius: 24px;
            }

            .dcpr-mini,
            .dcpr-hero-points,
            .dcpr-card-grid,
            .dcpr-step-grid,
            .dcpr-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcpr-hero-visual {
                min-height: 540px;
            }

            .dcpr-progress > div,
            .dcpr-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpr-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpr-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpr-wrap')) {
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

            function labelUniform(v) {
                return v === 4 ? 'odlična' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'loša';
            }

            function labelTrim(v) {
                return v === 1 ? 'malo' : v === 2 ? 'srednje' : v === 3 ? 'mnogo' : 'kritično';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpr-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcpr-temp');
                const knife = n('dcpr-knife');
                const uniform = n('dcpr-uniform');
                const trim = n('dcpr-trim');
                const time = n('dcpr-time');

                document.getElementById('dcpr-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpr-knife-val').textContent = knife.toFixed(0) + ' %';
                document.getElementById('dcpr-uniform-val').textContent = labelUniform(uniform);
                document.getElementById('dcpr-trim-val').textContent = labelTrim(trim);
                document.getElementById('dcpr-time-val').textContent = time.toFixed(0) + ' min';

                let salt = 12 + (4 - uniform) * 18 + Math.max(0, time - 25) * 1.2;
                let structure = 12 + Math.max(0, 70 - knife) * 0.8 + Math.max(0, temp - 5) * 5 + (trim - 1) * 10;
                let warm = 8 + Math.max(0, temp - 3) * 8 + Math.max(0, time - 15) * 1.4;
                let grind = 12 + (trim - 1) * 18 + Math.max(0, 65 - knife) * 0.6 + (4 - uniform) * 10;

                salt = clamp(salt, 0, 100);
                structure = clamp(structure, 0, 100);
                warm = clamp(warm, 0, 100);
                grind = clamp(grind, 0, 100);

                setBar('dcpr-salt', 'dcpr-salt-num', salt, true);
                setBar('dcpr-structure', 'dcpr-structure-num', structure, true);
                setBar('dcpr-warm', 'dcpr-warm-num', warm, true);
                setBar('dcpr-grind', 'dcpr-grind-num', grind, true);

                let title = 'Dobri uvjeti za rezanje';
                let text = 'Meso je hladno, nož je oštar i komadi se mogu rezati ujednačeno.';
                let advice = 'Nastavi rezati u manjim serijama i vraćaj pripremljene komade na hladno.';

                if (warm > 65) {
                    title = 'Meso se predugo grije';
                    text = 'Dugo stajanje izvan hladnog režima povećava rizik u svim sljedećim fazama.';
                    advice = 'Smanji količinu mesa na stolu, radi u manjim serijama i vraćaj narezane komade na hladno.';
                } else if (structure > 65) {
                    title = 'Rizik oštećenja strukture';
                    text = 'Tup nož, toplo meso ili opne mogu gnječiti površinu umjesto da je čisto režu.';
                    advice = 'Naoštri nož, ohladi meso i ukloni opne prije nastavka.';
                } else if (salt > 65) {
                    title = 'Rizik neravnomjernog soljenja';
                    text = 'Neujednačeni komadi kasnije ne primaju sol jednako.';
                    advice = 'Ujednači dimenzije reza prema namjeni i odvoji veće komade od manjih.';
                } else if (grind > 65) {
                    title = 'Kasniji problem u mljevenju';
                    text = 'Opne, žile i loš rez mogu kasnije začepljivati mlin i kvariti granulaciju.';
                    advice = 'Obreži problematične dijelove prije nego sirovina dođe do mlina.';
                }

                document.getElementById('dcpr-status-title').textContent = title;
                document.getElementById('dcpr-status-text').textContent = text;
                document.getElementById('dcpr-advice').textContent = advice;
            }

            ['dcpr-temp', 'dcpr-knife', 'dcpr-uniform', 'dcpr-trim', 'dcpr-time'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije rezanja',
                    items: [
                        ['Meso je hladno', 'Sirovina nije omekšala na sobnoj temperaturi.'],
                        ['Nož je oštar', 'Tup nož gnječi i trga površinu mesa.'],
                        ['Daska je čista i stabilna', 'Nestabilna podloga usporava rad i povećava rizik.'],
                        ['Opne i žile su vidljive za obrezivanje', 'Ne smiju završiti u mljevenju ako se mogu ukloniti ranije.'],
                        ['Dimenzija reza je određena', 'Zna se reže li se za soljenje, mljevenje ili veće komade.'],
                        ['Radi se u manjim serijama', 'Na stolu ne smije biti više mesa nego što se može brzo obraditi.']
                    ]
                },
                during: {
                    title: 'Tijekom rezanja',
                    items: [
                        ['Komadi su ujednačeni', 'Slični komadi kasnije se ravnomjernije sole i obrađuju.'],
                        ['Meso se ne gnječi', 'Rez mora biti čist, bez nepotrebnog pritiska.'],
                        ['Opne i žile se uklanjaju odmah', 'Ne ostavljaju se za kasnije ako se sada mogu očistiti.'],
                        ['Narezano meso se vraća na hladno', 'Ne skuplja se velika hrpa na toplom stolu.'],
                        ['Mast ostaje čvrsta', 'Ako mast omekšava, prekini i ohladi sirovinu.'],
                        ['Radni tok ostaje uredan', 'Čista površina i jasne posude smanjuju mogućnost zabune.']
                    ]
                },
                after: {
                    title: 'Nakon rezanja',
                    items: [
                        ['Komadi su odvojeni po namjeni', 'Za soljenje, mljevenje i veće komade ne vrijedi isti tok.'],
                        ['Nema sumnjivih dijelova u šarži', 'Oštećeni ili upitni dijelovi nisu vraćeni u masu.'],
                        ['Meso je vraćeno u hladan režim', 'Sljedeća faza ne počinje s toplom sirovinom.'],
                        ['Omjer mesa i masti je provjeren', 'Recept se ne oslanja na dojam nego na stvaran omjer.'],
                        ['Bilješka šarže je dopunjena', 'Zapisana je priprema, masa i eventualni problemi.'],
                        ['Spremno je za soljenje', 'Rez mora omogućiti ravnomjerno soljenje u sljedećoj fazi.']
                    ]
                }
            };

            const solutions = {
                'Meso je hladno': ['Toplo meso se lakše gnječi i brže ulazi u rizičan režim.', 'Vrati meso na hlađenje i nastavi u manjim serijama.'],
                'Nož je oštar': ['Tup nož ne reže, nego trga vlakna.', 'Naoštri ili zamijeni nož prije nastavka.'],
                'Daska je čista i stabilna': ['Loša podloga usporava rad i povećava rizik kontaminacije.', 'Očisti i učvrsti radnu površinu prije rezanja.'],
                'Opne i žile su vidljive za obrezivanje': ['Ako ih preskočiš, kasnije kvare mljevenje i teksturu.', 'Odvoji ih prije rezanja u konačnu dimenziju.'],
                'Dimenzija reza je određena': ['Bez plana nastaju neujednačeni komadi.', 'Odredi dimenziju prema sljedećem procesu.'],
                'Radi se u manjim serijama': ['Velike serije stoje predugo na toplom.', 'Izvadi samo količinu koju možeš brzo obraditi.'],

                'Komadi su ujednačeni': ['Neujednačeni komadi ne primaju sol i toplinu procesa jednako.', 'Ponovno odvoji prevelike komade i ujednači rez.'],
                'Meso se ne gnječi': ['Gnječenje oštećuje površinu i strukturu.', 'Naoštri nož i smanji pritisak.'],
                'Opne i žile se uklanjaju odmah': ['Kasnije ih je teže ukloniti, a mogu začepiti mlin.', 'Očisti ih dok su vidljive i dostupne.'],
                'Narezano meso se vraća na hladno': ['Toplo čekanje kvari dobar rad.', 'Odmah vraćaj posude u hladnjak.'],
                'Mast ostaje čvrsta': ['Mekana mast kasnije vodi prema razmazivanju.', 'Prekini, ohladi i nastavi tek kad je mast čvrsta.'],
                'Radni tok ostaje uredan': ['Nered stvara greške i usporava hladan proces.', 'Koristi označene posude i drži površinu čistom.'],

                'Komadi su odvojeni po namjeni': ['Različite namjene traže različit tok.', 'Odvoji meso za soljenje, mljevenje i veće komade.'],
                'Nema sumnjivih dijelova u šarži': ['Sumnjivi dio može pokvariti cijelu šaržu.', 'Ne vraćaj problematične dijelove u masu.'],
                'Meso je vraćeno u hladan režim': ['Sljedeća faza ne smije početi s toplom sirovinom.', 'Vrati meso na hladno do soljenja ili mljevenja.'],
                'Omjer mesa i masti je provjeren': ['Pogrešan omjer mijenja teksturu i sušenje.', 'Izvaži i uskladi omjer prije nastavka.'],
                'Bilješka šarže je dopunjena': ['Bez bilješki nema ponovljivosti.', 'Upiši masu, dimenziju reza i opažanja.'],
                'Spremno je za soljenje': ['Ako rez nije ujednačen, soljenje neće biti ujednačeno.', 'Ujednači komade prije prelaska na soljenje.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpr-check-list');
                document.getElementById('dcpr-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_rezanje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpr-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_rezanje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpr-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpr-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpr-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpr-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpr-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpr-solutions-title');
                const solList = document.getElementById('dcpr-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpr-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi hladno, uredno i prema planiranoj dimenziji reza.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpr-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpr-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpr-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcpr_assets', 120);
