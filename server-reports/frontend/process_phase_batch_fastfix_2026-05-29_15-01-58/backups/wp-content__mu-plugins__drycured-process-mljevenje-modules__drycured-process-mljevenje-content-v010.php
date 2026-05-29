<?php
/**
 * Plugin Name: Drycured Process Mljevenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/mljevenje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpm_enabled(): bool {
    return (bool) get_option('drycured_process_mljevenje_enabled', 1);
}

function dcpm_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/mljevenje';
}

function dcpm_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpm_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/mljevenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpm_render(): string {
    if (!dcpm_enabled()) {
        return '';
    }

    $hero_img = dcpm_image_url('mljevenje-hero-v01.jpg');
    $visual_1 = dcpm_image_url('mljevenje-rizik-razmazivanja-v01.jpg');
    $visual_2 = dcpm_image_url('mljevenje-resetke-v01.jpg');

    ob_start();
    ?>
    <main id="dcpm-mljevenje" class="dcpm-wrap" aria-label="Mljevenje mesa u proizvodnji suhomesnatih proizvoda">

        <section class="dcpm-hero">
            <div class="dcpm-hero-copy">
                <span class="dcpm-eyebrow">Faza 04 — mljevenje</span>
                <h1>Mljevenje</h1>
                <p class="dcpm-lead">
                    Mljevenje nije samo usitnjavanje mesa. To je trenutak u kojem se odlučuje hoće li proizvod
                    imati čist presjek, pravilnu granulaciju i čvrstu strukturu — ili razmazanu mast, kašastu smjesu
                    i probleme koji će se vidjeti tek mnogo kasnije.
                </p>

                <div class="dcpm-actions">
                    <a href="#dcpm-simulator">Otvori simulator rizika</a>
                    <a href="<?php echo esc_url(dcpm_page_url('proces-izrade/mijesanje')); ?>">Sljedeća faza: Miješanje</a>
                </div>

                <div class="dcpm-mini">
                    <div><span>cilj</span><strong>čista granulacija</strong></div>
                    <div><span>rizik</span><strong>razmazana mast</strong></div>
                    <div><span>kontrola</span><strong>hladnoća + oštrina</strong></div>
                </div>
            </div>

            <div class="dcpm-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Mljevenje mesa za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>
                <div class="dcpm-visual-overlay">
                    <span>kritična faza</span>
                    <h2>Toplina i tup nož kvare strukturu prije nego majstor primijeti problem.</h2>
                </div>
                <div class="dcpm-hero-points">
                    <div><span>meso</span><strong>0–4 °C</strong></div>
                    <div><span>mlin</span><strong>ohlađen</strong></div>
                    <div><span>rešetka</span><strong>prema proizvodu</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpm-simulator" class="dcpm-simulator">
            <div class="dcpm-head">
                <span class="dcpm-eyebrow">Aktivni alat</span>
                <h2>Simulator rizika razmazivanja masti</h2>
                <p>
                    Pomakni klizače i vidi kako temperatura, oštrina noža, rešetka, brzina rada i broj prolaza
                    utječu na strukturu smjese. Ovo je edukativni model, ali jako dobro pokazuje zašto se mljevenje
                    mora voditi hladno i precizno.
                </p>
            </div>

            <div class="dcpm-sim-shell">
                <div class="dcpm-controls">
                    <h3>Postavi uvjete mljevenja</h3>

                    <div class="dcpm-control">
                        <label>Temperatura mesa <b id="dcpm-temp-val">2 °C</b></label>
                        <input id="dcpm-temp" type="range" min="-2" max="12" value="2" step="1">
                    </div>

                    <div class="dcpm-control">
                        <label>Oštrina noža <b id="dcpm-knife-val">85 %</b></label>
                        <input id="dcpm-knife" type="range" min="20" max="100" value="85" step="5">
                    </div>

                    <div class="dcpm-control">
                        <label>Rešetka <b id="dcpm-plate-val">6 mm</b></label>
                        <input id="dcpm-plate" type="range" min="2" max="12" value="6" step="1">
                    </div>

                    <div class="dcpm-control">
                        <label>Brzina rada <b id="dcpm-speed-val">srednja</b></label>
                        <input id="dcpm-speed" type="range" min="1" max="3" value="2" step="1">
                    </div>

                    <div class="dcpm-control">
                        <label>Broj prolaza <b id="dcpm-pass-val">1</b></label>
                        <input id="dcpm-pass" type="range" min="1" max="3" value="1" step="1">
                    </div>

                    <div class="dcpm-note">
                        Ako se mast jednom razmaže, kasnije je više ne možeš vratiti u jasnu granulaciju.
                        Dim, sušenje i zrenje ne popravljaju loše mljevenje.
                    </div>
                </div>

                <div class="dcpm-output">
                    <div class="dcpm-status">
                        <span>procjena</span>
                        <h3 id="dcpm-status-title">Dobri uvjeti za mljevenje</h3>
                        <p id="dcpm-status-text">
                            Smjesa je dovoljno hladna, nož je oštar i rizik razmazivanja masti je nizak.
                        </p>
                    </div>

                    <div class="dcpm-risk-bars">
                        <div class="dcpm-risk">
                            <label>Razmazana mast <span id="dcpm-smear-num">0/100</span></label>
                            <i><b id="dcpm-smear"></b></i>
                        </div>

                        <div class="dcpm-risk">
                            <label>Kašasta struktura <span id="dcpm-paste-num">0/100</span></label>
                            <i><b id="dcpm-paste"></b></i>
                        </div>

                        <div class="dcpm-risk">
                            <label>Neujednačena granulacija <span id="dcpm-uneven-num">0/100</span></label>
                            <i><b id="dcpm-uneven"></b></i>
                        </div>

                        <div class="dcpm-risk">
                            <label>Zagrijavanje smjese <span id="dcpm-warm-num">0/100</span></label>
                            <i><b id="dcpm-warm"></b></i>
                        </div>
                    </div>

                    <div class="dcpm-advice" id="dcpm-advice">
                        Nastavi raditi hladno i u kraćim serijama. Mlin ne smije gurati smjesu kao pastu.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpm-plate-guide">
            <div class="dcpm-head">
                <span class="dcpm-eyebrow">Odabir rešetke</span>
                <h2>Rešetka se bira prema proizvodu, ne prema navici</h2>
                <p>
                    Promjer rešetke određuje granulaciju, presjek i osjećaj u ustima. Previše fina rešetka može
                    uništiti karakter trajne kobasice, a pregruba rešetka može otežati vezanje smjese.
                </p>
            </div>

            <div class="dcpm-plate-grid">
                <article>
                    <b>2–3 mm</b>
                    <h3>Paštete i fine smjese</h3>
                    <p>Koristi se za vrlo fine proizvode, ali nije prvi izbor za trajne kobasice.</p>
                </article>

                <article>
                    <b>3–5 mm</b>
                    <h3>Polutrajni proizvodi</h3>
                    <p>Daje finiju teksturu, ali traži oprez da mast ne postane razmazana.</p>
                </article>

                <article>
                    <b>6–8 mm</b>
                    <h3>Trajne kobasice</h3>
                    <p>Najčešći raspon za uredan presjek i dobru teksturu kod mnogih trajnih kobasica.</p>
                </article>

                <article>
                    <b>8–10 mm</b>
                    <h3>Kulen i grublji stilovi</h3>
                    <p>Čuva krupniju strukturu i prepoznatljiv presjek proizvoda koji traže grublju granulaciju.</p>
                </article>
            </div>
        </section>

        <section class="dcpm-process">
            <div class="dcpm-head">
                <span class="dcpm-eyebrow">Praktični redoslijed</span>
                <h2>Kako mljeti bez skrivene štete</h2>
            </div>

            <div class="dcpm-step-grid">
                <article>
                    <em>01</em>
                    <h3>Ohladi meso i mast</h3>
                    <p>Meso mora biti hladno i čvrsto. Mast ne smije biti mekana jer tada lako prelazi u razmazanu pastu.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Provjeri nož i rešetku</h3>
                    <p>Tup nož i loš kontakt s rešetkom trgaju meso umjesto da ga čisto režu.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Radi u kraćim serijama</h3>
                    <p>Dug rad bez pauze grije smjesu i metalne dijelove mlina.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Smjesu odmah vrati na hladno</h3>
                    <p>Nakon mljevenja smjesa ne smije čekati na toplom. Sljedeća faza je miješanje, ali pod kontrolom temperature.</p>
                </article>
            </div>
        </section>

        <section class="dcpm-visual-row">
            <div class="dcpm-image-card <?php echo $visual_1 ? 'has-image' : 'no-image'; ?>">
                <?php if ($visual_1): ?>
                    <img src="<?php echo esc_url($visual_1); ?>" alt="Usporedba pravilno mljevene strukture i razmazane masti" loading="lazy" decoding="async">
                <?php endif; ?>
                <div>
                    <span class="dcpm-eyebrow">Vizual 01</span>
                    <h3>Pravilno mljevenje nasuprot razmazanoj masti</h3>
                    <p>Ovdje ćemo umetnuti sliku koju ćemo generirati: uredna granulacija, jasno vidljiva mast i primjer loše, kašaste strukture.</p>
                </div>
            </div>

            <div class="dcpm-image-card <?php echo $visual_2 ? 'has-image' : 'no-image'; ?>">
                <?php if ($visual_2): ?>
                    <img src="<?php echo esc_url($visual_2); ?>" alt="Odabir rešetke za mljevenje prema tipu proizvoda" loading="lazy" decoding="async">
                <?php endif; ?>
                <div>
                    <span class="dcpm-eyebrow">Vizual 02</span>
                    <h3>Rešetke i granulacija</h3>
                    <p>Ovdje ćemo umetnuti sliku s prikazom različitih rešetki i teksture: 3 mm, 6 mm, 8 mm i 10 mm.</p>
                </div>
            </div>
        </section>

        <section id="dcpm-checklist" class="dcpm-checklist">
            <div class="dcpm-head">
                <span class="dcpm-eyebrow">Kontrolna lista</span>
                <h2>Mljevenje se provjerava prije nego što bude kasno</h2>
                <p>
                    Ova lista vodi korisnika kroz ono što treba provjeriti prije, tijekom i nakon mljevenja.
                    Neoznačene stavke odmah prikazuju rješenja.
                </p>
            </div>

            <div class="dcpm-check-shell">
                <div class="dcpm-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije mljevenja</button>
                    <button type="button" data-tab="during">Tijekom mljevenja</button>
                    <button type="button" data-tab="after">Nakon mljevenja</button>
                </div>

                <div class="dcpm-check-panel">
                    <div class="dcpm-progress">
                        <div>
                            <strong id="dcpm-check-title">Prije mljevenja</strong>
                            <span id="dcpm-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpm-check-bar"></b></i>
                    </div>

                    <div id="dcpm-check-list" class="dcpm-check-list"></div>

                    <div class="dcpm-solutions">
                        <div class="dcpm-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpm-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpm-solutions-list" class="dcpm-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpm-problems">
            <div class="dcpm-head">
                <span class="dcpm-eyebrow">Problem → rješenje</span>
                <h2>Greške u mljevenju koje se kasnije skupo plaćaju</h2>
            </div>

            <div class="dcpm-problem-grid">
                <article>
                    <h3>Razmazana mast u presjeku</h3>
                    <p><strong>Uzrok:</strong> topla mast, tup nož, sporo guranje kroz mlin ili previše prolaza.</p>
                    <p><strong>Rješenje:</strong> ohladiti smjesu, naoštriti nož, provjeriti rešetku i raditi u kraćim serijama.</p>
                </article>

                <article>
                    <h3>Kašasta smjesa</h3>
                    <p><strong>Uzrok:</strong> previše fino mljevenje, pregrijavanje ili neodgovarajuća rešetka.</p>
                    <p><strong>Rješenje:</strong> koristiti veću rešetku za trajne proizvode i smjesu vratiti na hladno prije nastavka.</p>
                </article>

                <article>
                    <h3>Neujednačena granulacija</h3>
                    <p><strong>Uzrok:</strong> loše narezani komadi, različite temperature mesa i masti ili zagušen mlin.</p>
                    <p><strong>Rješenje:</strong> rezati komade ujednačeno, odvojiti pretople dijelove i ne prepunjavati mlin.</p>
                </article>

                <article>
                    <h3>Začepljena rešetka</h3>
                    <p><strong>Uzrok:</strong> žilice, opne, tup nož ili preslabo očišćeno meso.</p>
                    <p><strong>Rješenje:</strong> obrezati opne i žile, očistiti rešetku i provjeriti kontakt noža i rešetke.</p>
                </article>
            </div>
        </section>

        <section class="dcpm-next">
            <div>
                <span class="dcpm-eyebrow">Sljedeća faza</span>
                <h2>Mljevenje priprema teren za miješanje</h2>
                <p>
                    Ako je mljevenje loše, miješanje više ne može vratiti jasnu strukturu. Sljedeća faza zato mora
                    objasniti vezivnost smjese, ekstrakciju proteina, redoslijed dodavanja soli, začina i tekućina.
                </p>
            </div>

            <div class="dcpm-next-actions">
                <a href="<?php echo esc_url(dcpm_page_url('proces-izrade/mijesanje')); ?>">Otvori fazu Miješanje</a>
                <a href="<?php echo esc_url(dcpm_page_url('proces-izrade')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_mljevenje', 'dcpm_render');

function dcpm_append_to_page($content) {
    static $added = false;

    if ($added || !dcpm_enabled()) {
        return $content;
    }

    if (!dcpm_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpm-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_mljevenje]');
}
add_filter('the_content', 'dcpm_append_to_page', 35);

function dcpm_assets() {
    if (!dcpm_is_page() || !dcpm_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpm-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpm-wrap * {
            box-sizing: border-box;
        }

        .dcpm-eyebrow {
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

        .dcpm-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcpm-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpm-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpm-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpm-hero,
        .dcpm-simulator,
        .dcpm-plate-guide,
        .dcpm-process,
        .dcpm-checklist,
        .dcpm-problems,
        .dcpm-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpm-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpm-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpm-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcpm-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpm-actions a,
        .dcpm-next-actions a {
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

        .dcpm-actions a:nth-child(2),
        .dcpm-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpm-mini {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpm-mini div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpm-mini span,
        .dcpm-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpm-mini strong,
        .dcpm-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpm-hero-visual {
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

        .dcpm-hero-visual img,
        .dcpm-image-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpm-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpm-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcpm-visual-overlay span {
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

        .dcpm-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcpm-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpm-hero-points div {
            padding: 14px;
            border-radius: 18px;
            background: rgba(255,255,255,.86);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,.28);
            box-shadow: 0 18px 38px rgba(16,23,34,.16);
        }

        .dcpm-simulator,
        .dcpm-plate-guide,
        .dcpm-process,
        .dcpm-checklist,
        .dcpm-problems,
        .dcpm-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpm-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpm-sim-shell,
        .dcpm-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpm-controls,
        .dcpm-output,
        .dcpm-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpm-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpm-controls h3 {
            font-size: 23px;
        }

        .dcpm-control {
            margin-bottom: 18px;
        }

        .dcpm-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpm-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpm-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpm-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpm-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpm-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpm-status h3,
        .dcpm-status p {
            color: #fff;
        }

        .dcpm-status p {
            opacity: .75;
        }

        .dcpm-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpm-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpm-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpm-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpm-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpm-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpm-plate-grid,
        .dcpm-step-grid,
        .dcpm-problem-grid,
        .dcpm-visual-row {
            display: grid;
            gap: 16px;
        }

        .dcpm-plate-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpm-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpm-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpm-plate-grid article,
        .dcpm-step-grid article,
        .dcpm-problem-grid article,
        .dcpm-image-card {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .dcpm-plate-grid article:hover,
        .dcpm-step-grid article:hover,
        .dcpm-problem-grid article:hover,
        .dcpm-image-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 24px 54px rgba(16,23,34,.13);
        }

        .dcpm-plate-grid b,
        .dcpm-step-grid em {
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

        .dcpm-visual-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 34px;
        }

        .dcpm-image-card {
            position: relative;
            min-height: 360px;
            overflow: hidden;
            display: flex;
            align-items: flex-end;
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.24), transparent 34%),
                #101722;
        }

        .dcpm-image-card::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.82));
        }

        .dcpm-image-card > div {
            position: relative;
            z-index: 2;
            color: #fff;
        }

        .dcpm-image-card h3,
        .dcpm-image-card p {
            color: #fff;
        }

        .dcpm-image-card p {
            opacity: .78;
        }

        .dcpm-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpm-tabs button {
            width: 100%;
            text-align: left;
            border: 1px solid rgba(16,23,34,.10);
            border-radius: 18px;
            padding: 15px 16px;
            background: #fff;
            color: var(--ink);
            font-weight: 950;
            cursor: pointer;
            transition: transform .16s ease, background .16s ease;
        }

        .dcpm-tabs button:hover {
            transform: translateY(-2px);
        }

        .dcpm-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpm-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpm-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpm-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpm-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpm-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpm-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpm-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpm-check-item {
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

        .dcpm-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
            cursor: pointer;
        }

        .dcpm-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpm-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpm-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpm-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpm-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpm-solutions-head span {
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

        .dcpm-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpm-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpm-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpm-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpm-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpm-solution-card p + p {
            margin-top: 8px;
        }

        .dcpm-solution-card strong {
            color: #76551e;
        }

        .dcpm-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpm-next h2,
        .dcpm-next p {
            color: #fff;
        }

        .dcpm-next p {
            opacity: .78;
        }

        .dcpm-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1000px) {
            .dcpm-hero,
            .dcpm-sim-shell,
            .dcpm-check-shell,
            .dcpm-next {
                grid-template-columns: 1fr;
            }

            .dcpm-plate-grid,
            .dcpm-step-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dcpm-problem-grid,
            .dcpm-visual-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 680px) {
            .dcpm-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpm-hero,
            .dcpm-hero-copy,
            .dcpm-hero-visual,
            .dcpm-simulator,
            .dcpm-plate-guide,
            .dcpm-process,
            .dcpm-checklist,
            .dcpm-problems,
            .dcpm-next {
                border-radius: 24px;
            }

            .dcpm-wrap h1 {
                font-size: clamp(42px, 13vw, 62px);
            }

            .dcpm-mini,
            .dcpm-hero-points,
            .dcpm-plate-grid,
            .dcpm-step-grid {
                grid-template-columns: 1fr;
            }

            .dcpm-hero-visual {
                min-height: 540px;
            }

            .dcpm-check-panel,
            .dcpm-controls,
            .dcpm-output {
                border-radius: 24px;
            }

            .dcpm-progress > div,
            .dcpm-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpm-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpm-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpm-wrap')) {
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

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpm-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcpm-temp');
                const knife = n('dcpm-knife');
                const plate = n('dcpm-plate');
                const speed = n('dcpm-speed');
                const passes = n('dcpm-pass');

                document.getElementById('dcpm-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpm-knife-val').textContent = knife.toFixed(0) + ' %';
                document.getElementById('dcpm-plate-val').textContent = plate.toFixed(0) + ' mm';
                document.getElementById('dcpm-pass-val').textContent = passes.toFixed(0);

                const speedLabel = speed === 1 ? 'spora' : (speed === 3 ? 'brza' : 'srednja');
                document.getElementById('dcpm-speed-val').textContent = speedLabel;

                let smear = 18 + Math.max(0, temp - 3) * 8 + Math.max(0, 70 - knife) * 0.75 + (passes - 1) * 18 + (speed === 3 ? 12 : 0);
                let paste = 15 + Math.max(0, temp - 4) * 6 + Math.max(0, 5 - plate) * 11 + (passes - 1) * 16 + Math.max(0, 60 - knife) * 0.45;
                let uneven = 18 + Math.abs(plate - 6) * 3 + Math.max(0, 55 - knife) * 0.85 + (speed === 3 ? 10 : 0);
                let warm = 12 + Math.max(0, temp - 2) * 7 + (speed === 3 ? 18 : 0) + (passes - 1) * 16;

                smear = clamp(smear, 0, 100);
                paste = clamp(paste, 0, 100);
                uneven = clamp(uneven, 0, 100);
                warm = clamp(warm, 0, 100);

                setBar('dcpm-smear', 'dcpm-smear-num', smear, true);
                setBar('dcpm-paste', 'dcpm-paste-num', paste, true);
                setBar('dcpm-uneven', 'dcpm-uneven-num', uneven, true);
                setBar('dcpm-warm', 'dcpm-warm-num', warm, true);

                let title = 'Dobri uvjeti za mljevenje';
                let text = 'Smjesa je dovoljno hladna, nož je oštar i rizik razmazivanja masti je nizak.';
                let advice = 'Nastavi raditi hladno i u kraćim serijama. Mlin ne smije gurati smjesu kao pastu.';

                if (smear > 65) {
                    title = 'Visok rizik razmazivanja masti';
                    text = 'Mast se može pretvoriti u film koji kasnije kvari presjek, sušenje i teksturu.';
                    advice = 'Zaustavi rad, vrati smjesu na hlađenje, provjeri nož i rešetku te nastavi u kraćim serijama.';
                } else if (paste > 65) {
                    title = 'Rizik kašaste strukture';
                    text = 'Smjesa gubi jasnu granulaciju i može kasnije dati zbijen ili neugodan presjek.';
                    advice = 'Koristi veću rešetku, smanji broj prolaza i pazi da meso ne ulazi u mlin pretoplo.';
                } else if (uneven > 65) {
                    title = 'Rizik neujednačene granulacije';
                    text = 'Presjek može biti nepravilan, s krupnim i sitnim dijelovima koji se ne ponašaju jednako.';
                    advice = 'Ujednači veličinu narezanih komada, očisti rešetku i provjeri odgovara li rešetka tipu proizvoda.';
                } else if (warm > 65) {
                    title = 'Smjesa se previše zagrijava';
                    text = 'Toplina je tihi neprijatelj mljevenja. Problem se često vidi tek kasnije.';
                    advice = 'Radi s ohlađenim metalnim dijelovima, kraćim serijama i vrati smjesu na hlađenje čim omekša.';
                }

                document.getElementById('dcpm-status-title').textContent = title;
                document.getElementById('dcpm-status-text').textContent = text;
                document.getElementById('dcpm-advice').textContent = advice;
            }

            ['dcpm-temp', 'dcpm-knife', 'dcpm-plate', 'dcpm-speed', 'dcpm-pass'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije mljevenja',
                    items: [
                        ['Meso i mast su dobro ohlađeni', 'Mast mora biti čvrsta, a meso hladno na dodir.'],
                        ['Komadi su ujednačeno narezani', 'Preveliki i nepravilni komadi opterećuju mlin.'],
                        ['Nož je oštar', 'Tup nož trga meso i gura mast kroz rešetku.'],
                        ['Rešetka je čista i ravna', 'Začepljena ili oštećena rešetka kvari rez.'],
                        ['Metalni dijelovi su ohlađeni', 'Topli mlin ubrzava omekšavanje masti.'],
                        ['Odabrana je pravilna rešetka', 'Rešetka mora odgovarati tipu proizvoda.']
                    ]
                },
                during: {
                    title: 'Tijekom mljevenja',
                    items: [
                        ['Mlin reže, a ne gnječi', 'Smjesa mora izlaziti jasno, ne kao pasta.'],
                        ['Smjesa se ne zagrijava', 'Ako mast omekšava, treba stati i hladiti.'],
                        ['Protok je ravnomjeran', 'Zastajkivanje često znači opne, žile ili tup nož.'],
                        ['Rešetka se ne začepljuje', 'Začepljenje povećava pritisak i razmazivanje.'],
                        ['Ne radi se previše prolaza', 'Svaki prolaz povećava toplinu i mehaničko oštećenje.'],
                        ['Serije su kratke', 'Bolje više kratkih hladnih serija nego jedna duga topla.']
                    ]
                },
                after: {
                    title: 'Nakon mljevenja',
                    items: [
                        ['Granulacija je čista', 'Meso i mast trebaju biti jasno vidljivi.'],
                        ['Nema masnog filma', 'Masni sjaj i film znak su razmazivanja.'],
                        ['Smjesa je vraćena na hladno', 'Ne smije čekati na toplom prije miješanja.'],
                        ['Mlin je očišćen', 'Ostaci mesa i masti brzo postaju higijenski problem.'],
                        ['Zabilježena je rešetka', 'Upiši promjer rešetke i opažanja.'],
                        ['Spremno je za miješanje', 'Miješanje se nastavlja samo ako je struktura uredna.']
                    ]
                }
            };

            const solutions = {
                'Meso i mast su dobro ohlađeni': ['Topla mast se razmazuje i kvari presjek.', 'Vrati meso i mast na hlađenje. Ako je mast mekana, ne nastavljaj mljevenje.'],
                'Komadi su ujednačeno narezani': ['Neujednačeni komadi stvaraju nepravilan pritisak u mlinu.', 'Nareži komade ujednačeno, približno 2–3 cm za siguran i ravnomjeran ulaz u mlin.'],
                'Nož je oštar': ['Tup nož gnječi, a ne reže.', 'Naoštri ili zamijeni nož i provjeri prianjanje na rešetku.'],
                'Rešetka je čista i ravna': ['Začepljena rešetka povećava trenje i toplinu.', 'Očisti rešetku, ukloni opne i provjeri je li površina ravna.'],
                'Metalni dijelovi su ohlađeni': ['Topli metal brzo omekšava mast.', 'Ohladi puž, nož i rešetku prije rada.'],
                'Odabrana je pravilna rešetka': ['Pogrešna rešetka mijenja teksturu cijelog proizvoda.', 'Za trajne kobasice kreni od 6–8 mm, za grublje proizvode 8–10 mm, za fine proizvode manje.'],

                'Mlin reže, a ne gnječi': ['Gnječenje je znak tupog noža, loše rešetke ili pretoplog mesa.', 'Zaustavi rad, provjeri nož, rešetku i temperaturu smjese.'],
                'Smjesa se ne zagrijava': ['Zagrijavanje je najčešći put prema razmazanoj masti.', 'Radi kraće, hladi smjesu i ne sili mlin.'],
                'Protok je ravnomjeran': ['Zastajkivanje stvara pritisak i nepravilan rez.', 'Očisti žile/opne, ne prepunjavaj mlin i provjeri nož.'],
                'Rešetka se ne začepljuje': ['Začepljenje stvara trenje i gura mast.', 'Očisti rešetku i obreži meso prije nastavka.'],
                'Ne radi se previše prolaza': ['Svaki prolaz dodatno oštećuje strukturu.', 'Planiraj granulaciju unaprijed i koristi što manje prolaza.'],
                'Serije su kratke': ['Duga serija grije smjesu i mlin.', 'Radi u manjim količinama i vraćaj smjesu na hladno.'],

                'Granulacija je čista': ['Ako granulacija nije čista, problem je već nastao.', 'Zaustavi proces, procijeni može li smjesa ići u proizvod ili je treba preusmjeriti u drugi tip obrade.'],
                'Nema masnog filma': ['Masni film pokazuje da je mast razmazana.', 'U sljedećoj šarži radi hladnije, s oštrijim nožem i manje prolaza.'],
                'Smjesa je vraćena na hladno': ['Toplo čekanje poništava dobar rad.', 'Smjesu odmah vrati u hladnjak do miješanja.'],
                'Mlin je očišćen': ['Ostaci mesa i masti su higijenski rizik.', 'Rastavi, operi, osuši i po potrebi dezinficiraj dijelove.'],
                'Zabilježena je rešetka': ['Bez bilješki ne znaš što je dalo dobar presjek.', 'Upiši promjer rešetke, temperaturu i opažanja.'],
                'Spremno je za miješanje': ['Ako je struktura loša, miješanje neće vratiti izgubljenu granulaciju.', 'Prije miješanja procijeni temperaturu, izgled i masnoću smjese.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpm-check-list');
                document.getElementById('dcpm-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_mljevenje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpm-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_mljevenje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpm-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpm-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpm-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpm-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpm-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpm-solutions-title');
                const solList = document.getElementById('dcpm-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpm-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi s mjerenjem, hladnim režimom i bilješkama šarže.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpm-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpm-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpm-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcpm_assets', 120);
