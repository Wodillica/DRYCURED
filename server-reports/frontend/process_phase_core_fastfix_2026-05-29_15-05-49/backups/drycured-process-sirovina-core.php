<?php
/**
 * Plugin Name: Drycured Process Sirovina Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/sirovina/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcps_enabled(): bool {
    return (bool) get_option('drycured_process_sirovina_enabled', 1);
}

function dcps_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/sirovina';
}

function dcps_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcps_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/sirovina/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcps_render(): string {
    if (!dcps_enabled()) {
        return '';
    }

    $hero_img = dcps_image_url('sirovina-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcps-sirovina" class="dcps-wrap" aria-label="Sirovina u proizvodnji suhomesnatih proizvoda">

        <section class="dcps-hero">
            <div class="dcps-hero-copy">
                <span class="dcps-eyebrow">Faza 01 — sirovina</span>
                <h1>Sirovina</h1>
                <p class="dcps-lead">
                    Dobar proizvod ne počinje u pušnici, nego na stolu za pripremu. Kvaliteta mesa, masnoće,
                    temperatura, svježina i pravilno obrezivanje odlučuju hoće li kasnije soljenje, rezanje,
                    mljevenje i zrenje imati dobar temelj.
                </p>

                <div class="dcps-actions">
                    <a href="#dcps-procjena">Otvori procjenu sirovine</a>
                    <a href="<?php echo esc_url(dcps_page_url('proces-izrade/rezanje')); ?>">Sljedeća faza: Rezanje</a>
                </div>

                <div class="dcps-mini">
                    <div><span>cilj</span><strong>čista i hladna sirovina</strong></div>
                    <div><span>rizik</span><strong>loš temelj procesa</strong></div>
                    <div><span>kontrola</span><strong>miris, boja, masnoća</strong></div>
                </div>
            </div>

            <div class="dcps-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Priprema sirovine za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcps-visual-overlay">
                    <span>temelj proizvoda</span>
                    <h2>Greška u sirovini kasnije se ne popravlja začinom, dimom ni strpljenjem.</h2>
                </div>

                <div class="dcps-hero-points">
                    <div><span>meso</span><strong>svježe</strong></div>
                    <div><span>mast</span><strong>čvrsta</strong></div>
                    <div><span>obrada</span><strong>hladna</strong></div>
                </div>
            </div>
        </section>

        <section id="dcps-procjena" class="dcps-simulator">
            <div class="dcps-head">
                <span class="dcps-eyebrow">Aktivni alat</span>
                <h2>Procjena rizika sirovine</h2>
                <p>
                    Ovaj edukativni alat pomaže razumjeti zašto temperatura, svježina, udio masnoće,
                    opne i higijena radnog toka utječu na sigurnost, teksturu i kasniji presjek proizvoda.
                </p>
            </div>

            <div class="dcps-sim-shell">
                <div class="dcps-controls">
                    <h3>Postavi stanje sirovine</h3>

                    <div class="dcps-control">
                        <label>Temperatura mesa <b id="dcps-temp-val">3 °C</b></label>
                        <input id="dcps-temp" type="range" min="0" max="12" value="3" step="1">
                    </div>

                    <div class="dcps-control">
                        <label>Svježina i miris <b id="dcps-fresh-val">odlično</b></label>
                        <input id="dcps-fresh" type="range" min="1" max="4" value="4" step="1">
                    </div>

                    <div class="dcps-control">
                        <label>Opne, žile i oštećeni dijelovi <b id="dcps-trim-val">malo</b></label>
                        <input id="dcps-trim" type="range" min="1" max="4" value="1" step="1">
                    </div>

                    <div class="dcps-control">
                        <label>Udio masnoće <b id="dcps-fat-val">25 %</b></label>
                        <input id="dcps-fat" type="range" min="5" max="50" value="25" step="5">
                    </div>

                    <div class="dcps-control">
                        <label>Higijena radnog toka <b id="dcps-hygiene-val">visoka</b></label>
                        <input id="dcps-hygiene" type="range" min="1" max="4" value="4" step="1">
                    </div>

                    <div class="dcps-note">
                        Ako sirovina već na početku pokazuje sumnjiv miris, sluzavost ili lošu boju,
                        daljnji proces ne smije biti pokušaj spašavanja.
                    </div>
                </div>

                <div class="dcps-output">
                    <div class="dcps-status">
                        <span>procjena</span>
                        <h3 id="dcps-status-title">Dobar temelj za proces</h3>
                        <p id="dcps-status-text">
                            Sirovina je hladna, svježa i dovoljno uredna za nastavak prema rezanju.
                        </p>
                    </div>

                    <div class="dcps-risk-bars">
                        <div class="dcps-risk">
                            <label>Sigurnosni rizik <span id="dcps-safety-num">0/100</span></label>
                            <i><b id="dcps-safety"></b></i>
                        </div>

                        <div class="dcps-risk">
                            <label>Rizik loše teksture <span id="dcps-texture-num">0/100</span></label>
                            <i><b id="dcps-texture"></b></i>
                        </div>

                        <div class="dcps-risk">
                            <label>Rizik razmazivanja masti <span id="dcps-smear-num">0/100</span></label>
                            <i><b id="dcps-smear"></b></i>
                        </div>

                        <div class="dcps-risk">
                            <label>Rizik neujednačenog sušenja <span id="dcps-dry-num">0/100</span></label>
                            <i><b id="dcps-dry"></b></i>
                        </div>
                    </div>

                    <div class="dcps-advice" id="dcps-advice">
                        Nastavi s hladnim rezanjem i uklanjanjem opni, žila i oštećenih dijelova.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcps-principles">
            <div class="dcps-head">
                <span class="dcps-eyebrow">Što se provjerava</span>
                <h2>Tri stvari odlučuju prije prvog reza</h2>
            </div>

            <div class="dcps-card-grid">
                <article>
                    <b>01</b>
                    <h3>Svježina i miris</h3>
                    <p>Meso mora imati čist miris i prirodnu boju. Sumnjiv miris, sluzavost ili neobična površina nisu detalj nego upozorenje.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Masnoća i struktura</h3>
                    <p>Mast treba biti čvrsta i prikladna za tip proizvoda. Premekana mast kasnije lako prelazi u razmazanu strukturu.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Hladan radni tok</h3>
                    <p>Temperatura sirovine mora ostati pod kontrolom. Toplina na početku stvara probleme u mljevenju, miješanju i punjenju.</p>
                </article>
            </div>
        </section>

        <section class="dcps-process">
            <div class="dcps-head">
                <span class="dcps-eyebrow">Praktični redoslijed</span>
                <h2>Kako pripremiti sirovinu bez skrivenih grešaka</h2>
            </div>

            <div class="dcps-step-grid">
                <article>
                    <em>01</em>
                    <h3>Pregledaj meso</h3>
                    <p>Provjeri miris, boju, površinu i temperaturu. Sumnjive dijelove ne pokušavaj popravljati začinima.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Odvoji oštećene dijelove</h3>
                    <p>Ukloni krvave, zgnječene, sluzave ili oštećene dijelove koji mogu pokvariti cijelu šaržu.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Uredi mast</h3>
                    <p>Mast mora biti čvrsta, čista i primjerena proizvodu. Loša mast često je uzrok lošeg presjeka.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Vrati na hladno</h3>
                    <p>Sirovina ne smije čekati na toplom. Sljedeći korak je rezanje, ali pod hladnim režimom.</p>
                </article>
            </div>
        </section>

        <section id="dcps-checklist" class="dcps-checklist">
            <div class="dcps-head">
                <span class="dcps-eyebrow">Kontrolna lista</span>
                <h2>Sirovina se ne prima na povjerenje</h2>
                <p>
                    Ova lista pomaže da prije nastavka procesa provjeriš osnovne uvjete.
                    Neoznačene stavke odmah prikazuju konkretno rješenje.
                </p>
            </div>

            <div class="dcps-check-shell">
                <div class="dcps-tabs">
                    <button type="button" class="is-active" data-tab="prijem">Prijem sirovine</button>
                    <button type="button" data-tab="obrada">Priprema</button>
                    <button type="button" data-tab="nastavak">Prije rezanja</button>
                </div>

                <div class="dcps-check-panel">
                    <div class="dcps-progress">
                        <div>
                            <strong id="dcps-check-title">Prijem sirovine</strong>
                            <span id="dcps-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcps-check-bar"></b></i>
                    </div>

                    <div id="dcps-check-list" class="dcps-check-list"></div>

                    <div class="dcps-solutions">
                        <div class="dcps-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcps-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcps-solutions-list" class="dcps-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcps-problems">
            <div class="dcps-head">
                <span class="dcps-eyebrow">Problem → rješenje</span>
                <h2>Greške u sirovini koje kasnije vode cijeli proces u krivom smjeru</h2>
            </div>

            <div class="dcps-problem-grid">
                <article>
                    <h3>Sumnjiv miris ili sluzava površina</h3>
                    <p><strong>Uzrok:</strong> loša svježina, loše čuvanje ili prekid hladnog lanca.</p>
                    <p><strong>Rješenje:</strong> ne koristiti takvu sirovinu za suhomesnate proizvode. Ako postoji sumnja u ispravnost, šarža se zaustavlja.</p>
                </article>

                <article>
                    <h3>Premekana mast</h3>
                    <p><strong>Uzrok:</strong> previsoka temperatura ili neprikladna masnoća.</p>
                    <p><strong>Rješenje:</strong> vratiti mast na hlađenje i koristiti samo čvrstu masnoću primjerenu proizvodu.</p>
                </article>

                <article>
                    <h3>Previše opni i žila</h3>
                    <p><strong>Uzrok:</strong> nedovoljno obrezivanje mesa prije daljnje obrade.</p>
                    <p><strong>Rješenje:</strong> očistiti meso prije rezanja i mljevenja jer opne kasnije začepljuju rešetku i kvare teksturu.</p>
                </article>

                <article>
                    <h3>Neujednačen odnos mesa i masti</h3>
                    <p><strong>Uzrok:</strong> loše planiranje sirovine za tip proizvoda.</p>
                    <p><strong>Rješenje:</strong> prije rezanja odvojiti i odmjeriti mesni i masni dio prema receptu.</p>
                </article>
            </div>
        </section>

        <section class="dcps-next">
            <div>
                <span class="dcps-eyebrow">Sljedeća faza</span>
                <h2>Uredna sirovina ide na rezanje</h2>
                <p>
                    Rezanje priprema komade za soljenje, mljevenje ili daljnju obradu. Ako je sirovina loše
                    odabrana, rezanje samo uredno rasporedi problem po cijeloj šarži.
                </p>
            </div>

            <div class="dcps-next-actions">
                <a href="<?php echo esc_url(dcps_page_url('proces-izrade/rezanje')); ?>">Otvori fazu Rezanje</a>
                <a href="<?php echo esc_url(dcps_page_url('proces-izrade')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_sirovina', 'dcps_render');

function dcps_append_to_page($content) {
    static $added = false;

    if ($added || !dcps_enabled()) {
        return $content;
    }

    if (!dcps_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcps-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_sirovina]');
}
add_filter('the_content', 'dcps_append_to_page', 35);

function dcps_assets() {
    if (!dcps_is_page() || !dcps_enabled()) {
        return;
    }
    ?>
    <style>
        .dcps-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcps-wrap * { box-sizing: border-box; }

        .dcps-eyebrow {
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

        .dcps-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcps-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcps-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcps-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcps-hero,
        .dcps-simulator,
        .dcps-principles,
        .dcps-process,
        .dcps-checklist,
        .dcps-problems,
        .dcps-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcps-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcps-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcps-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcps-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcps-actions a,
        .dcps-next-actions a {
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

        .dcps-actions a:nth-child(2),
        .dcps-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcps-mini,
        .dcps-hero-points,
        .dcps-card-grid,
        .dcps-step-grid,
        .dcps-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcps-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcps-mini div,
        .dcps-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcps-mini span,
        .dcps-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcps-mini strong,
        .dcps-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcps-hero-visual {
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

        .dcps-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcps-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcps-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcps-visual-overlay span {
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

        .dcps-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcps-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcps-simulator,
        .dcps-principles,
        .dcps-process,
        .dcps-checklist,
        .dcps-problems,
        .dcps-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcps-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcps-sim-shell,
        .dcps-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcps-controls,
        .dcps-output,
        .dcps-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcps-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcps-control {
            margin-bottom: 18px;
        }

        .dcps-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcps-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcps-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcps-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcps-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcps-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcps-status h3,
        .dcps-status p {
            color: #fff;
        }

        .dcps-status p { opacity: .75; }

        .dcps-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcps-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcps-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcps-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcps-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcps-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcps-card-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dcps-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcps-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcps-card-grid article,
        .dcps-step-grid article,
        .dcps-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dcps-card-grid article:hover,
        .dcps-step-grid article:hover,
        .dcps-problem-grid article:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 54px rgba(16,23,34,.13);
        }

        .dcps-card-grid b,
        .dcps-step-grid em {
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

        .dcps-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcps-tabs button {
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

        .dcps-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcps-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcps-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcps-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcps-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcps-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcps-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcps-check-list {
            display: grid;
            gap: 10px;
        }

        .dcps-check-item {
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

        .dcps-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcps-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcps-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcps-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcps-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcps-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcps-solutions-head span {
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

        .dcps-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcps-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcps-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcps-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcps-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcps-solution-card p + p {
            margin-top: 8px;
        }

        .dcps-solution-card strong {
            color: #76551e;
        }

        .dcps-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcps-next h2,
        .dcps-next p {
            color: #fff;
        }

        .dcps-next p {
            opacity: .78;
        }

        .dcps-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1000px) {
            .dcps-hero,
            .dcps-sim-shell,
            .dcps-check-shell,
            .dcps-next {
                grid-template-columns: 1fr;
            }

            .dcps-card-grid,
            .dcps-step-grid,
            .dcps-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcps-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcps-hero,
            .dcps-hero-copy,
            .dcps-hero-visual,
            .dcps-simulator,
            .dcps-principles,
            .dcps-process,
            .dcps-checklist,
            .dcps-problems,
            .dcps-next {
                border-radius: 24px;
            }

            .dcps-mini,
            .dcps-hero-points,
            .dcps-card-grid,
            .dcps-step-grid,
            .dcps-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcps-hero-visual {
                min-height: 540px;
            }

            .dcps-progress > div,
            .dcps-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcps-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcps-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcps-wrap')) {
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

            function labelFresh(v) {
                return v === 4 ? 'odlično' : v === 3 ? 'dobro' : v === 2 ? 'upitno' : 'loše';
            }

            function labelTrim(v) {
                return v === 1 ? 'malo' : v === 2 ? 'srednje' : v === 3 ? 'mnogo' : 'kritično';
            }

            function labelHygiene(v) {
                return v === 4 ? 'visoka' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'kritična';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcps-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcps-temp');
                const fresh = n('dcps-fresh');
                const trim = n('dcps-trim');
                const fat = n('dcps-fat');
                const hygiene = n('dcps-hygiene');

                document.getElementById('dcps-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcps-fresh-val').textContent = labelFresh(fresh);
                document.getElementById('dcps-trim-val').textContent = labelTrim(trim);
                document.getElementById('dcps-fat-val').textContent = fat.toFixed(0) + ' %';
                document.getElementById('dcps-hygiene-val').textContent = labelHygiene(hygiene);

                let safety = 12 + Math.max(0, temp - 4) * 7 + (4 - fresh) * 22 + (4 - hygiene) * 18;
                let texture = 14 + Math.abs(fat - 25) * 1.4 + (trim - 1) * 14 + Math.max(0, temp - 5) * 4;
                let smear = 10 + Math.max(0, temp - 3) * 8 + Math.max(0, fat - 30) * 1.5;
                let dry = 14 + Math.abs(fat - 25) * 1.2 + (trim - 1) * 10 + Math.max(0, 2 - fresh) * 15;

                safety = clamp(safety, 0, 100);
                texture = clamp(texture, 0, 100);
                smear = clamp(smear, 0, 100);
                dry = clamp(dry, 0, 100);

                setBar('dcps-safety', 'dcps-safety-num', safety, true);
                setBar('dcps-texture', 'dcps-texture-num', texture, true);
                setBar('dcps-smear', 'dcps-smear-num', smear, true);
                setBar('dcps-dry', 'dcps-dry-num', dry, true);

                let title = 'Dobar temelj za proces';
                let text = 'Sirovina je hladna, svježa i dovoljno uredna za nastavak prema rezanju.';
                let advice = 'Nastavi s hladnim rezanjem i uklanjanjem opni, žila i oštećenih dijelova.';

                if (safety > 65) {
                    title = 'Visok sigurnosni rizik';
                    text = 'Sirovina ne pokazuje dovoljno siguran temelj za nastavak procesa.';
                    advice = 'Zaustavi proces. Ako postoji sumnja u zdravstvenu ispravnost, sirovina se ne koristi za suhomesnate proizvode.';
                } else if (smear > 65) {
                    title = 'Rizik razmazivanja masti';
                    text = 'Temperatura ili masnoća mogu kasnije pokvariti mljevenje i presjek.';
                    advice = 'Vrati sirovinu na hlađenje i nastavi tek kad je mast čvrsta i hladna.';
                } else if (texture > 65) {
                    title = 'Rizik loše teksture';
                    text = 'Opne, žile ili nepravilan odnos mesa i masti mogu kasnije stvoriti loš presjek.';
                    advice = 'Obreži problematične dijelove i odmjeri mesni i masni dio prije nastavka.';
                } else if (dry > 65) {
                    title = 'Rizik neujednačenog sušenja';
                    text = 'Neujednačena sirovina kasnije može dati neravnomjerno sušenje i nestabilnu teksturu.';
                    advice = 'Ujednači komade i odnos mesa/masti prije rezanja i daljnje obrade.';
                }

                document.getElementById('dcps-status-title').textContent = title;
                document.getElementById('dcps-status-text').textContent = text;
                document.getElementById('dcps-advice').textContent = advice;
            }

            ['dcps-temp', 'dcps-fresh', 'dcps-trim', 'dcps-fat', 'dcps-hygiene'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                prijem: {
                    title: 'Prijem sirovine',
                    items: [
                        ['Miris je čist', 'Nema neugodnog, trulog, kiselog ili neobično sladunjavog mirisa.'],
                        ['Boja je prirodna', 'Nema neobičnog sivila, zelenila, ljepljive površine ili sumnjivih promjena.'],
                        ['Meso je hladno', 'Sirovina nije dugo stajala na sobnoj temperaturi.'],
                        ['Mast je čvrsta', 'Mast nije mekana, maziva ni uljasta.'],
                        ['Nema sluzave površine', 'Površina ne smije biti ljepljiva ili sluzava.'],
                        ['Sirovina je odvojena po namjeni', 'Mesni i masni dijelovi jasno su razdvojeni prije obrade.']
                    ]
                },
                obrada: {
                    title: 'Priprema sirovine',
                    items: [
                        ['Uklonjene su opne i žile', 'Opne i žile kasnije kvare rezanje, mljevenje i teksturu.'],
                        ['Oštećeni dijelovi su odvojeni', 'Zgnječeni, krvavi ili sumnjivi dijelovi ne idu u smjesu.'],
                        ['Masnoća je odabrana prema proizvodu', 'Nije svaka masnoća dobra za svaki tip proizvoda.'],
                        ['Radna površina je čista', 'Priprema sirovine mora se raditi na urednoj i hladnoj površini.'],
                        ['Komadi su pripremljeni za rezanje', 'Sirovina je spremna za ujednačeno rezanje, bez nepotrebnog zadržavanja.'],
                        ['Bilješka šarže je otvorena', 'Upisuje se vrsta mesa, masa, stanje sirovine i opažanja.']
                    ]
                },
                nastavak: {
                    title: 'Prije rezanja',
                    items: [
                        ['Sirovina je vraćena na hladno', 'Ne smije čekati na toplom prije rezanja.'],
                        ['Odnos mesa i masti je planiran', 'Recept mora imati jasan mesni i masni sastav.'],
                        ['Sumnjivi dijelovi nisu korišteni', 'Ne pokušava se spasiti nešto što je već upitno.'],
                        ['Noževi i daske su spremni', 'Alat je čist, oštar i prikladan za hladan rad.'],
                        ['Sljedeća faza je jasna', 'Zna se ide li sirovina na rezanje za komade, soljenje ili mljevenje.'],
                        ['Radni tok je brz i hladan', 'Nema nepotrebnog zadržavanja na sobnoj temperaturi.']
                    ]
                }
            };

            const solutions = {
                'Miris je čist': ['Miris je prvi ozbiljan alarm.', 'Ako je miris sumnjiv, ne nastavljaj proces. Takvu sirovinu ne treba spašavati začinima, dimom ili sušenjem.'],
                'Boja je prirodna': ['Neobična boja može značiti oksidaciju, loše čuvanje ili kvarenje.', 'Odvoji problematične dijelove i ne koristi sirovinu ako promjena izgleda ozbiljno.'],
                'Meso je hladno': ['Toplo meso ubrzava mikrobiološki rizik i kvari kasniju obradu.', 'Vrati meso na hlađenje i nastavi tek kad je temperatura pod kontrolom.'],
                'Mast je čvrsta': ['Mekana mast kasnije se lako razmazuje.', 'Ohladi masnoću prije rezanja ili mljevenja.'],
                'Nema sluzave površine': ['Sluzavost je znak za oprez.', 'Zaustavi proces i ne koristi sumnjivu sirovinu za suhomesnate proizvode.'],
                'Sirovina je odvojena po namjeni': ['Bez odvajanja nema kontrole recepta.', 'Razdvoji mesni i masni dio prije vaganja i rezanja.'],

                'Uklonjene su opne i žile': ['Opne i žile kasnije začepljuju rešetku i kvare teksturu.', 'Obreži ih prije rezanja i mljevenja.'],
                'Oštećeni dijelovi su odvojeni': ['Oštećeni dijelovi mogu pokvariti cijelu smjesu.', 'Izreži ih i ne vraćaj u šaržu.'],
                'Masnoća je odabrana prema proizvodu': ['Masnoća određuje presjek, sočnost i stabilnost.', 'Odaberi čvrstu masnoću primjerenu proizvodu.'],
                'Radna površina je čista': ['Prljava površina kvari dobru sirovinu.', 'Operi, osuši i pripremi površinu prije nastavka.'],
                'Komadi su pripremljeni za rezanje': ['Neuredna priprema usporava rezanje i grije sirovinu.', 'Pripremi komade za brz, hladan i uredan rad.'],
                'Bilješka šarže je otvorena': ['Bez bilješki nema ponovljivosti.', 'Upiši masu, vrstu mesa, stanje sirovine i početna opažanja.'],

                'Sirovina je vraćena na hladno': ['Toplo čekanje poništava dobru pripremu.', 'Vrati sirovinu u hladnjak do trenutka rezanja.'],
                'Odnos mesa i masti je planiran': ['Loš omjer mijenja teksturu i sušenje.', 'Prije rezanja odmjeri mesni i masni dio prema receptu.'],
                'Sumnjivi dijelovi nisu korišteni': ['Jedan loš dio može pokvariti šaržu.', 'Ne ubacuj sumnjive dijelove u smjesu.'],
                'Noževi i daske su spremni': ['Tup ili prljav alat usporava rad i povećava rizik.', 'Pripremi čist alat i oštar nož prije početka.'],
                'Sljedeća faza je jasna': ['Neplaniran rad produžuje vrijeme na toplom.', 'Prije rezanja odluči ide li meso na komade, soljenje ili mljevenje.'],
                'Radni tok je brz i hladan': ['Sporo čekanje na toplom stvara probleme koji se ne vide odmah.', 'Radi u manjim serijama i vraćaj sirovinu na hladno.']
            };

            let activeTab = 'prijem';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcps-check-list');
                document.getElementById('dcps-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_sirovina_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcps-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_sirovina_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcps-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcps-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcps-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcps-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcps-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcps-solutions-title');
                const solList = document.getElementById('dcps-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcps-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi hladno, uredno i uz bilježenje šarže.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcps-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcps-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcps-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcps_assets', 120);
