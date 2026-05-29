<?php
/**
 * Plugin Name: Drycured Process Soljenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/soljenje/ s mostom prema kalkulatoru soljenja.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpsol_enabled(): bool {
    return (bool) get_option('drycured_process_soljenje_enabled', 1);
}

function dcpsol_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/soljenje';
}

function dcpsol_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpsol_calculator_url(): string {
    $manual = trim((string) get_option('drycured_salt_calculator_url', ''));

    if ($manual !== '') {
        return esc_url($manual);
    }

    $candidates = [
        'kalkulator-soljenja',
        'alati/kalkulator-soljenja',
        'kalkulatori/kalkulator-soljenja',
        'soljenje-kalkulator',
        'kalkulatori/soljenje',
        'alati/soljenje',
        'alati',
    ];

    foreach ($candidates as $path) {
        $page = get_page_by_path($path);
        if ($page && $page->post_status === 'publish') {
            return esc_url(get_permalink($page));
        }
    }

    return esc_url(home_url('/alati/'));
}

function dcpsol_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/soljenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpsol_render(): string {
    if (!dcpsol_enabled()) {
        return '';
    }

    $hero_img = dcpsol_image_url('soljenje-hero-v01.jpg');
    $calc_url = dcpsol_calculator_url();

    ob_start();
    ?>
    <main id="dcpsol-soljenje" class="dcpsol-wrap" aria-label="Soljenje mesa u proizvodnji suhomesnatih proizvoda">

        <section class="dcpsol-hero">
            <div class="dcpsol-hero-copy">
                <span class="dcpsol-eyebrow">Faza 03 — soljenje</span>
                <h1>Soljenje</h1>
                <p class="dcpsol-lead">
                    Soljenje nije samo dodavanje soli. To je faza u kojoj se postavlja temelj sigurnosti,
                    okusa, vezanja vode, boje i kasnijeg sušenja. Premalo soli stvara rizik, previše soli
                    uništava proizvod — zato se sol ne dodaje od oka.
                </p>

                <div class="dcpsol-actions">
                    <a href="#dcpsol-simulator">Otvori procjenu soljenja</a>
                    <a href="<?php echo esc_url($calc_url); ?>">Otvori kalkulator soljenja</a>
                </div>

                <div class="dcpsol-mini">
                    <div><span>cilj</span><strong>točna doza soli</strong></div>
                    <div><span>rizik</span><strong>premalo ili previše</strong></div>
                    <div><span>alat</span><strong>kalkulator soljenja</strong></div>
                </div>
            </div>

            <div class="dcpsol-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Soljenje mesa za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpsol-visual-overlay">
                    <span>kontrola doze</span>
                    <h2>Soljenje se planira računanjem, a ne rukom koja “otprilike zna”.</h2>
                </div>

                <div class="dcpsol-hero-points">
                    <div><span>sol</span><strong>precizno</strong></div>
                    <div><span>meso</span><strong>hladno</strong></div>
                    <div><span>zapis</span><strong>obavezan</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpsol-simulator" class="dcpsol-simulator">
            <div class="dcpsol-head">
                <span class="dcpsol-eyebrow">Edukativna procjena</span>
                <h2>Procjena rizika soljenja</h2>
                <p>
                    Ovaj alat objašnjava zašto su masa mesa, debljina komada, ujednačenost rezanja,
                    način soljenja i temperatura važni. Za stvarnu količinu soli koristi kalkulator soljenja.
                </p>
            </div>

            <div class="dcpsol-sim-shell">
                <div class="dcpsol-controls">
                    <h3>Postavi uvjete soljenja</h3>

                    <div class="dcpsol-control">
                        <label>Masa mesa <b id="dcpsol-mass-val">10 kg</b></label>
                        <input id="dcpsol-mass" type="range" min="1" max="50" value="10" step="1">
                    </div>

                    <div class="dcpsol-control">
                        <label>Veličina komada <b id="dcpsol-size-val">srednji</b></label>
                        <input id="dcpsol-size" type="range" min="1" max="4" value="2" step="1">
                    </div>

                    <div class="dcpsol-control">
                        <label>Ujednačenost komada <b id="dcpsol-uniform-val">dobra</b></label>
                        <input id="dcpsol-uniform" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpsol-control">
                        <label>Temperatura tijekom soljenja <b id="dcpsol-temp-val">4 °C</b></label>
                        <input id="dcpsol-temp" type="range" min="0" max="14" value="4" step="1">
                    </div>

                    <div class="dcpsol-control">
                        <label>Preciznost vaganja <b id="dcpsol-scale-val">visoka</b></label>
                        <input id="dcpsol-scale" type="range" min="1" max="4" value="4" step="1">
                    </div>

                    <div class="dcpsol-note">
                        Ova procjena ne računa recepturu. Za količine soli, nitritne soli, salamure ili pacanja
                        koristi kalkulator soljenja.
                    </div>
                </div>

                <div class="dcpsol-output">
                    <div class="dcpsol-status">
                        <span>procjena</span>
                        <h3 id="dcpsol-status-title">Dobri uvjeti za soljenje</h3>
                        <p id="dcpsol-status-text">
                            Komadi su dovoljno ujednačeni, temperatura je pod kontrolom i soljenje se može voditi precizno.
                        </p>
                    </div>

                    <div class="dcpsol-risk-bars">
                        <div class="dcpsol-risk">
                            <label>Neravnomjerno prodiranje soli <span id="dcpsol-uneven-num">0/100</span></label>
                            <i><b id="dcpsol-uneven"></b></i>
                        </div>

                        <div class="dcpsol-risk">
                            <label>Rizik premale doze <span id="dcpsol-low-num">0/100</span></label>
                            <i><b id="dcpsol-low"></b></i>
                        </div>

                        <div class="dcpsol-risk">
                            <label>Rizik preslanosti <span id="dcpsol-high-num">0/100</span></label>
                            <i><b id="dcpsol-high"></b></i>
                        </div>

                        <div class="dcpsol-risk">
                            <label>Rizik toplog procesa <span id="dcpsol-warm-num">0/100</span></label>
                            <i><b id="dcpsol-warm"></b></i>
                        </div>
                    </div>

                    <div class="dcpsol-advice" id="dcpsol-advice">
                        Vodi bilješku šarže i stvarne količine izračunaj kalkulatorom.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpsol-calculator-bridge">
            <div>
                <span class="dcpsol-eyebrow">Glavni alat</span>
                <h2>Za količine koristi kalkulator soljenja</h2>
                <p>
                    Ova stranica objašnjava logiku i rizike soljenja. Točne količine soli, nitritne soli,
                    salamure ili paca treba računati u namjenskom alatu, prema masi mesa i tipu proizvoda.
                </p>
            </div>

            <div class="dcpsol-calc-actions">
                <a href="<?php echo esc_url($calc_url); ?>">Otvori kalkulator soljenja</a>
                <small>Ako poveznica ne vodi na točan alat, URL se može postaviti WP opcijom <code>drycured_salt_calculator_url</code>.</small>
            </div>
        </section>

        <section class="dcpsol-principles">
            <div class="dcpsol-head">
                <span class="dcpsol-eyebrow">Što sol radi</span>
                <h2>Sol utječe na sigurnost, vodu i strukturu</h2>
            </div>

            <div class="dcpsol-card-grid">
                <article>
                    <b>01</b>
                    <h3>Sigurnost</h3>
                    <p>Sol smanjuje dostupnu vodu i pomaže usmjeriti proizvod prema stabilnijem procesu.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Tekstura</h3>
                    <p>Sol pomaže razvoju vezivnosti smjese, ali samo ako se kasnije pravilno miješa.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Okus</h3>
                    <p>Premalo soli daje nestabilan proizvod, a previše soli potiskuje aromu i čini proizvod tvrdim.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Sušenje</h3>
                    <p>Soljenje utječe na kretanje vode i kasniju brzinu sušenja i zrenja.</p>
                </article>
            </div>
        </section>

        <section class="dcpsol-process">
            <div class="dcpsol-head">
                <span class="dcpsol-eyebrow">Praktični redoslijed</span>
                <h2>Kako voditi soljenje bez nagađanja</h2>
            </div>

            <div class="dcpsol-step-grid">
                <article>
                    <em>01</em>
                    <h3>Izvaži meso</h3>
                    <p>Količina soli uvijek se računa prema stvarnoj masi mesa, ne prema procjeni od oka.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Izračunaj količine</h3>
                    <p>Koristi kalkulator soljenja za sol, nitritnu sol, pac ili salamuru prema tipu proizvoda.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Ravnomjerno rasporedi</h3>
                    <p>Sol mora doći do svih dijelova sirovine. Neujednačeni komadi otežavaju ravnomjernost.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Bilježi šaržu</h3>
                    <p>Zapiši masu, količinu soli, vrijeme, temperaturu i opažanja za ponovljivost.</p>
                </article>
            </div>
        </section>

        <section id="dcpsol-checklist" class="dcpsol-checklist">
            <div class="dcpsol-head">
                <span class="dcpsol-eyebrow">Kontrolna lista</span>
                <h2>Soljenje ne smije ostati u glavi</h2>
                <p>
                    Kontrolna lista vodi kroz pripremu, primjenu i završnu provjeru. Neoznačene stavke daju rješenje.
                </p>
            </div>

            <div class="dcpsol-check-shell">
                <div class="dcpsol-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije soljenja</button>
                    <button type="button" data-tab="during">Tijekom soljenja</button>
                    <button type="button" data-tab="after">Nakon soljenja</button>
                </div>

                <div class="dcpsol-check-panel">
                    <div class="dcpsol-progress">
                        <div>
                            <strong id="dcpsol-check-title">Prije soljenja</strong>
                            <span id="dcpsol-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpsol-check-bar"></b></i>
                    </div>

                    <div id="dcpsol-check-list" class="dcpsol-check-list"></div>

                    <div class="dcpsol-solutions">
                        <div class="dcpsol-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpsol-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpsol-solutions-list" class="dcpsol-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpsol-problems">
            <div class="dcpsol-head">
                <span class="dcpsol-eyebrow">Problem → rješenje</span>
                <h2>Greške u soljenju koje kasnije izgledaju kao problem sušenja ili okusa</h2>
            </div>

            <div class="dcpsol-problem-grid">
                <article>
                    <h3>Proizvod je preslan</h3>
                    <p><strong>Uzrok:</strong> sol dodana od oka, krivo vaganje ili predugo soljenje za manji komad.</p>
                    <p><strong>Rješenje:</strong> koristiti kalkulator, točnu vagu i zapisivati stvarnu masu mesa.</p>
                </article>

                <article>
                    <h3>Proizvod je nesigurno ili slabo soljen</h3>
                    <p><strong>Uzrok:</strong> premala količina soli, loš raspored ili preveliki komadi.</p>
                    <p><strong>Rješenje:</strong> provjeriti izračun, ravnomjerno rasporediti sol i ujednačiti komade.</p>
                </article>

                <article>
                    <h3>Neujednačen okus</h3>
                    <p><strong>Uzrok:</strong> sol nije jednako raspoređena ili komadi nisu iste veličine.</p>
                    <p><strong>Rješenje:</strong> bolje miješanje/okretanje, manja serija i jasna dimenzija reza.</p>
                </article>

                <article>
                    <h3>Loša ponovljivost</h3>
                    <p><strong>Uzrok:</strong> nema bilješke šarže, ne zna se točna masa ni količina soli.</p>
                    <p><strong>Rješenje:</strong> svaku šaržu zapisati: masa, sol, nitritna sol, vrijeme, temperatura i rezultat.</p>
                </article>
            </div>
        </section>

        <section class="dcpsol-next">
            <div>
                <span class="dcpsol-eyebrow">Sljedeća faza</span>
                <h2>Nakon soljenja proces ide prema mljevenju ili daljnjoj obradi</h2>
                <p>
                    Kod kobasica se nakon pravilne pripreme i soljenja ide prema mljevenju i miješanju.
                    Ako je soljenje krivo, sljedeće faze samo nose grešku dalje.
                </p>
            </div>

            <div class="dcpsol-next-actions">
                <a href="<?php echo esc_url(dcpsol_page_url('proces-izrade/mljevenje')); ?>">Otvori fazu Mljevenje</a>
                <a href="<?php echo esc_url(dcpsol_page_url('proces-izrade')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_soljenje', 'dcpsol_render');

function dcpsol_append_to_page($content) {
    static $added = false;

    if ($added || !dcpsol_enabled()) {
        return $content;
    }

    if (!dcpsol_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpsol-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_soljenje]');
}
add_filter('the_content', 'dcpsol_append_to_page', 35);

function dcpsol_assets() {
    if (!dcpsol_is_page() || !dcpsol_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpsol-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpsol-wrap * { box-sizing: border-box; }

        .dcpsol-eyebrow {
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

        .dcpsol-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcpsol-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpsol-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpsol-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpsol-hero,
        .dcpsol-simulator,
        .dcpsol-calculator-bridge,
        .dcpsol-principles,
        .dcpsol-process,
        .dcpsol-checklist,
        .dcpsol-problems,
        .dcpsol-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpsol-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpsol-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpsol-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcpsol-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpsol-actions a,
        .dcpsol-next-actions a,
        .dcpsol-calc-actions a {
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

        .dcpsol-actions a:nth-child(2),
        .dcpsol-calc-actions a {
            background: #f1d889;
            color: #101722 !important;
        }

        .dcpsol-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpsol-mini,
        .dcpsol-hero-points,
        .dcpsol-card-grid,
        .dcpsol-step-grid,
        .dcpsol-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpsol-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpsol-mini div,
        .dcpsol-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpsol-mini span,
        .dcpsol-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpsol-mini strong,
        .dcpsol-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpsol-hero-visual {
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

        .dcpsol-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpsol-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpsol-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcpsol-visual-overlay span {
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

        .dcpsol-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcpsol-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpsol-simulator,
        .dcpsol-calculator-bridge,
        .dcpsol-principles,
        .dcpsol-process,
        .dcpsol-checklist,
        .dcpsol-problems,
        .dcpsol-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpsol-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpsol-sim-shell,
        .dcpsol-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpsol-controls,
        .dcpsol-output,
        .dcpsol-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpsol-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpsol-control {
            margin-bottom: 18px;
        }

        .dcpsol-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpsol-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpsol-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpsol-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpsol-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpsol-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpsol-status h3,
        .dcpsol-status p {
            color: #fff;
        }

        .dcpsol-status p { opacity: .75; }

        .dcpsol-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpsol-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpsol-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpsol-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpsol-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpsol-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpsol-calculator-bridge,
        .dcpsol-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 310px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpsol-calculator-bridge h2,
        .dcpsol-calculator-bridge p,
        .dcpsol-next h2,
        .dcpsol-next p {
            color: #fff;
        }

        .dcpsol-calculator-bridge p,
        .dcpsol-next p {
            opacity: .78;
        }

        .dcpsol-calc-actions,
        .dcpsol-next-actions {
            display: grid;
            gap: 12px;
        }

        .dcpsol-calc-actions small {
            display: block;
            color: rgba(255,255,255,.62);
            font-size: 12px;
            line-height: 1.45;
        }

        .dcpsol-calc-actions code {
            color: #f1d889;
        }

        .dcpsol-card-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpsol-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpsol-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpsol-card-grid article,
        .dcpsol-step-grid article,
        .dcpsol-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
        }

        .dcpsol-card-grid b,
        .dcpsol-step-grid em {
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

        .dcpsol-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpsol-tabs button {
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

        .dcpsol-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpsol-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpsol-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpsol-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpsol-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpsol-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpsol-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpsol-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpsol-check-item {
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

        .dcpsol-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcpsol-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpsol-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpsol-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpsol-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpsol-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpsol-solutions-head span {
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

        .dcpsol-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpsol-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpsol-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpsol-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpsol-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpsol-solution-card p + p {
            margin-top: 8px;
        }

        .dcpsol-solution-card strong {
            color: #76551e;
        }

        @media (max-width: 1000px) {
            .dcpsol-hero,
            .dcpsol-sim-shell,
            .dcpsol-check-shell,
            .dcpsol-calculator-bridge,
            .dcpsol-next {
                grid-template-columns: 1fr;
            }

            .dcpsol-card-grid,
            .dcpsol-step-grid,
            .dcpsol-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcpsol-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpsol-hero,
            .dcpsol-hero-copy,
            .dcpsol-hero-visual,
            .dcpsol-simulator,
            .dcpsol-calculator-bridge,
            .dcpsol-principles,
            .dcpsol-process,
            .dcpsol-checklist,
            .dcpsol-problems,
            .dcpsol-next {
                border-radius: 24px;
            }

            .dcpsol-mini,
            .dcpsol-hero-points,
            .dcpsol-card-grid,
            .dcpsol-step-grid,
            .dcpsol-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcpsol-hero-visual {
                min-height: 540px;
            }

            .dcpsol-progress > div,
            .dcpsol-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpsol-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpsol-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpsol-wrap')) {
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

            function labelSize(v) {
                return v === 1 ? 'sitni' : v === 2 ? 'srednji' : v === 3 ? 'veliki' : 'vrlo veliki';
            }

            function labelUniform(v) {
                return v === 4 ? 'odlična' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'loša';
            }

            function labelScale(v) {
                return v === 4 ? 'visoka' : v === 3 ? 'dobra' : v === 2 ? 'slaba' : 'kritična';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpsol-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const mass = n('dcpsol-mass');
                const size = n('dcpsol-size');
                const uniform = n('dcpsol-uniform');
                const temp = n('dcpsol-temp');
                const scale = n('dcpsol-scale');

                document.getElementById('dcpsol-mass-val').textContent = mass.toFixed(0) + ' kg';
                document.getElementById('dcpsol-size-val').textContent = labelSize(size);
                document.getElementById('dcpsol-uniform-val').textContent = labelUniform(uniform);
                document.getElementById('dcpsol-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpsol-scale-val').textContent = labelScale(scale);

                let uneven = 10 + (4 - uniform) * 18 + (size - 1) * 12 + Math.max(0, mass - 20) * 0.8;
                let low = 10 + (4 - scale) * 22 + Math.max(0, mass - 10) * 0.5;
                let high = 8 + (4 - scale) * 18 + (size === 1 ? 10 : 0);
                let warm = 8 + Math.max(0, temp - 5) * 9;

                uneven = clamp(uneven, 0, 100);
                low = clamp(low, 0, 100);
                high = clamp(high, 0, 100);
                warm = clamp(warm, 0, 100);

                setBar('dcpsol-uneven', 'dcpsol-uneven-num', uneven, true);
                setBar('dcpsol-low', 'dcpsol-low-num', low, true);
                setBar('dcpsol-high', 'dcpsol-high-num', high, true);
                setBar('dcpsol-warm', 'dcpsol-warm-num', warm, true);

                let title = 'Dobri uvjeti za soljenje';
                let text = 'Komadi su dovoljno ujednačeni, temperatura je pod kontrolom i soljenje se može voditi precizno.';
                let advice = 'Vodi bilješku šarže i stvarne količine izračunaj kalkulatorom.';

                if (low > 65) {
                    title = 'Rizik premale ili netočne doze';
                    text = 'Slaba preciznost vaganja i veća masa šarže povećavaju mogućnost pogreške.';
                    advice = 'Ne nastavljaj od oka. Otvori kalkulator soljenja i izračunaj količine prema stvarnoj masi mesa.';
                } else if (high > 65) {
                    title = 'Rizik preslanosti';
                    text = 'Netočno vaganje i sitni komadi mogu dovesti do prejakog soljenja u odnosu na očekivani stil.';
                    advice = 'Provjeri recept, vagu i izračun u kalkulatoru prije dodavanja soli.';
                } else if (uneven > 65) {
                    title = 'Rizik neravnomjernog soljenja';
                    text = 'Veliki ili neujednačeni komadi ne primaju sol jednakom brzinom.';
                    advice = 'Ujednači komade i osiguraj ravnomjeran kontakt soli sa sirovinom.';
                } else if (warm > 65) {
                    title = 'Soljenje se vodi pretoplo';
                    text = 'Toplina povećava rizik i remeti hladni tok obrade.';
                    advice = 'Vrati sirovinu u hladan režim i nastavi tek kad je temperatura pod kontrolom.';
                }

                document.getElementById('dcpsol-status-title').textContent = title;
                document.getElementById('dcpsol-status-text').textContent = text;
                document.getElementById('dcpsol-advice').textContent = advice;
            }

            ['dcpsol-mass', 'dcpsol-size', 'dcpsol-uniform', 'dcpsol-temp', 'dcpsol-scale'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije soljenja',
                    items: [
                        ['Meso je točno izvagano', 'Količina soli računa se prema stvarnoj masi.'],
                        ['Komadi su ujednačeni', 'Slični komadi ravnomjernije primaju sol.'],
                        ['Sirovina je hladna', 'Soljenje ne smije početi s toplom sirovinom.'],
                        ['Vaga je precizna', 'Mala pogreška na vagi postaje velika greška u šarži.'],
                        ['Recept je jasan', 'Zna se koristi li se suho soljenje, pac ili salamura.'],
                        ['Kalkulator je spreman', 'Količine se računaju prije dodavanja soli.']
                    ]
                },
                during: {
                    title: 'Tijekom soljenja',
                    items: [
                        ['Sol se dodaje ravnomjerno', 'Ne smije završiti u grudama ili samo na jednom dijelu.'],
                        ['Smjesa ili komadi se okreću', 'Kontakt soli mora biti što ujednačeniji.'],
                        ['Temperatura ostaje niska', 'Toplina narušava sigurnost i kontrolu procesa.'],
                        ['Količine su zapisane', 'Zapiši stvarno dodanu sol i sve korekcije.'],
                        ['Nema dodavanja od oka', 'Svaka promjena mora biti izračunata i zapisana.'],
                        ['Sirovina je pokrivena i zaštićena', 'Nema izlaganja nečistoći ili nepotrebnom sušenju površine.']
                    ]
                },
                after: {
                    title: 'Nakon soljenja',
                    items: [
                        ['Bilješka šarže je dopunjena', 'Zapisana je masa, sol, vrijeme i temperatura.'],
                        ['Komadi su pravilno raspoređeni', 'Ne smiju biti zbijeni tako da dio ostane bez kontakta.'],
                        ['Vrijeme soljenja je definirano', 'Ne smije ovisiti o sjećanju ili procjeni.'],
                        ['Sljedeća faza je jasna', 'Zna se ide li proces prema mljevenju, miješanju ili odležavanju.'],
                        ['Nema sumnje u dozu', 'Ako postoji sumnja, provjerava se kalkulator i zapis.'],
                        ['Proces ostaje hladan', 'Do sljedeće faze sirovina ostaje u kontroliranim uvjetima.']
                    ]
                }
            };

            const solutions = {
                'Meso je točno izvagano': ['Bez stvarne mase nema točne doze soli.', 'Izvaži meso ponovno i tek tada koristi kalkulator soljenja.'],
                'Komadi su ujednačeni': ['Različiti komadi ne primaju sol jednako.', 'Ujednači rez ili odvoji veće komade u poseban tok.'],
                'Sirovina je hladna': ['Topla sirovina povećava rizik i remeti proces.', 'Vrati je na hladno prije soljenja.'],
                'Vaga je precizna': ['Neprecizna vaga kvari cijeli izračun.', 'Koristi vagu prikladne preciznosti i provjeri taru.'],
                'Recept je jasan': ['Različiti postupci traže različite količine i vrijeme.', 'Odredi suho soljenje, pac ili salamuru prije početka.'],
                'Kalkulator je spreman': ['Bez izračuna lako nastane premalo ili previše soli.', 'Otvori kalkulator soljenja i izračunaj količine prije dodavanja.'],

                'Sol se dodaje ravnomjerno': ['Grude soli stvaraju neujednačen okus i djelovanje.', 'Rasporedi sol postupno i ravnomjerno po cijeloj masi.'],
                'Smjesa ili komadi se okreću': ['Kontakt soli mora biti ravnomjeran.', 'Okreni komade ili bolje promiješaj prema tipu postupka.'],
                'Temperatura ostaje niska': ['Toplina stvara rizik i kvari kontrolu.', 'Vrati sirovinu u hladan režim.'],
                'Količine su zapisane': ['Bez zapisa nema ponovljivosti.', 'Odmah upiši stvarno dodane količine.'],
                'Nema dodavanja od oka': ['Dodavanje od oka je najkraći put do preslanog ili nesigurnog proizvoda.', 'Svaku promjenu ponovno izračunaj.'],
                'Sirovina je pokrivena i zaštićena': ['Nezaštićena sirovina upija nečistoće i gubi kontrolu površine.', 'Pokrij posude i drži ih u urednom hladnom prostoru.'],

                'Bilješka šarže je dopunjena': ['Bez završne bilješke ne znaš što ponoviti.', 'Upiši masu, sol, vrijeme, temperaturu i opažanja.'],
                'Komadi su pravilno raspoređeni': ['Zbijeni komadi ne primaju sol jednako.', 'Rasporedi ih tako da sol ima ravnomjeran kontakt.'],
                'Vrijeme soljenja je definirano': ['Nejasno vrijeme vodi u neujednačen rezultat.', 'Postavi vrijeme i podsjetnik.'],
                'Sljedeća faza je jasna': ['Neplaniran tok stvara čekanje i greške.', 'Odredi ide li proizvod prema mljevenju, miješanju ili odležavanju.'],
                'Nema sumnje u dozu': ['Ako nisi siguran, ne nastavljaj.', 'Provjeri kalkulator, zapis i stvarno dodanu količinu.'],
                'Proces ostaje hladan': ['Toplo čekanje poništava dobru pripremu.', 'Drži sirovinu u kontroliranim uvjetima do sljedeće faze.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpsol-check-list');
                document.getElementById('dcpsol-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_soljenje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpsol-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_soljenje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpsol-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpsol-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpsol-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpsol-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpsol-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpsol-solutions-title');
                const solList = document.getElementById('dcpsol-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpsol-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi prema planu i drži se izračuna iz kalkulatora.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpsol-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpsol-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpsol-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcpsol_assets', 120);
