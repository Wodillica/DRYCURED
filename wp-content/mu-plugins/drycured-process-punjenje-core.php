<?php
/**
 * Plugin Name: Drycured Process Punjenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/punjenje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpunv010_enabled(): bool {
    return (bool) get_option('drycured_process_punjenje_enabled', 1);
}

function dcpunv010_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/punjenje',
    ], true);
}

function dcpunv010_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpunv010_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/punjenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;

    if (file_exists($path)) {
        return content_url($rel);
    }

    return '';
}

function dcpunv010_render(): string {
    if (!dcpunv010_enabled()) {
        return '';
    }

    $hero_img = dcpunv010_image_url('punjenje-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcpun-punjenje" class="dcpun-wrap" aria-label="Punjenje kobasica i salama">

        <section class="dcpun-hero">
            <div class="dcpun-hero-copy">
                <span class="dcpun-eyebrow">Faza 07 — punjenje</span>
                <h1>Punjenje</h1>
                <p class="dcpun-lead">
                    Punjenje je trenutak kada smjesa postaje proizvod. Ovdje se više ne smije improvizirati:
                    smjesa mora biti hladna i vezana, crijeva pravilno pripremljena, pritisak ravnomjeran,
                    a zrak izbačen prije nego ostane zarobljen u kobasici ili salami.
                </p>

                <div class="dcpun-actions">
                    <a href="#dcpun-simulator">Otvori simulator punjenja</a>
                    <a href="<?php echo esc_url(dcpunv010_page_url('proces-izrade/fermentacija')); ?>">Sljedeća faza: Fermentacija</a>
                </div>

                <div class="dcpun-mini">
                    <div><span>cilj</span><strong>puno bez zraka</strong></div>
                    <div><span>rizik</span><strong>pucanje i šupljine</strong></div>
                    <div><span>kontrola</span><strong>pritisak + crijevo</strong></div>
                </div>
            </div>

            <div class="dcpun-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Punjenje smjese u crijeva za kobasice i salame" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpun-visual-overlay">
                    <span>kritična faza</span>
                    <h2>Punjenje ne popravlja smjesu — ono samo pokazuje koliko je prethodni rad bio uredan.</h2>
                </div>

                <div class="dcpun-hero-points">
                    <div><span>smjesa</span><strong>hladna</strong></div>
                    <div><span>crijevo</span><strong>elastično</strong></div>
                    <div><span>zrak</span><strong>izbačen</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpun-simulator" class="dcpun-simulator">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Aktivni alat</span>
                <h2>Simulator rizika punjenja</h2>
                <p>
                    Ovaj alat pokazuje kako vezivnost smjese, temperatura, priprema crijeva, pritisak punjenja
                    i uklanjanje zraka utječu na pucanje, šupljine, neujednačen proizvod i kasnije kvarenje presjeka.
                </p>
            </div>

            <div class="dcpun-sim-shell">
                <div class="dcpun-controls">
                    <h3>Postavi uvjete punjenja</h3>

                    <div class="dcpun-control">
                        <label>Temperatura smjese <b id="dcpun-temp-val">3 °C</b></label>
                        <input id="dcpun-temp" type="range" min="0" max="14" value="3" step="1">
                    </div>

                    <div class="dcpun-control">
                        <label>Vezivnost smjese <b id="dcpun-bind-val">dobra</b></label>
                        <input id="dcpun-bind" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpun-control">
                        <label>Priprema crijeva <b id="dcpun-casing-val">dobra</b></label>
                        <input id="dcpun-casing" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpun-control">
                        <label>Pritisak punjenja <b id="dcpun-pressure-val">umjeren</b></label>
                        <input id="dcpun-pressure" type="range" min="1" max="5" value="3" step="1">
                    </div>

                    <div class="dcpun-control">
                        <label>Uklanjanje zraka <b id="dcpun-air-val">dobro</b></label>
                        <input id="dcpun-air" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpun-note">
                        Crijeva prije punjenja moraju biti pravilno namočena i isprana. Suho, kruto ili slabo
                        pripremljeno crijevo puca lakše nego loše obećanje u izbornoj kampanji.
                    </div>
                </div>

                <div class="dcpun-output">
                    <div class="dcpun-status">
                        <span>procjena</span>
                        <h3 id="dcpun-status-title">Dobri uvjeti za punjenje</h3>
                        <p id="dcpun-status-text">
                            Smjesa je hladna, crijevo je pripremljeno i punjenje se može voditi mirno i ravnomjerno.
                        </p>
                    </div>

                    <div class="dcpun-risk-bars">
                        <div class="dcpun-risk">
                            <label>Rizik pucanja crijeva <span id="dcpun-burst-num">0/100</span></label>
                            <i><b id="dcpun-burst"></b></i>
                        </div>

                        <div class="dcpun-risk">
                            <label>Rizik zračnih džepova <span id="dcpun-voids-num">0/100</span></label>
                            <i><b id="dcpun-voids"></b></i>
                        </div>

                        <div class="dcpun-risk">
                            <label>Rizik lošeg presjeka <span id="dcpun-slice-num">0/100</span></label>
                            <i><b id="dcpun-slice"></b></i>
                        </div>

                        <div class="dcpun-risk">
                            <label>Rizik neravnomjernog sušenja <span id="dcpun-dry-num">0/100</span></label>
                            <i><b id="dcpun-dry"></b></i>
                        </div>
                    </div>

                    <div class="dcpun-advice" id="dcpun-advice">
                        Puni ravnomjerno, bez prekida i bez pretjeranog zatezanja crijeva.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpun-casing">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Crijeva i priprema</span>
                <h2>Crijevo mora biti spremno prije nego smjesa izađe iz hladnog režima</h2>
                <p>
                    Punjenje počinje prije prvog okretaja punilice. Crijeva treba namočiti, isprati,
                    provjeriti elastičnost i promjer, a tek tada puniti. Ako se crijevo priprema u hodu,
                    smjesa stoji, grije se i gubi ono što smo čuvali u prethodnim fazama.
                </p>
            </div>

            <div class="dcpun-card-grid">
                <article>
                    <b>01</b>
                    <h3>Namakanje</h3>
                    <p>Prirodna crijeva namakati prema vrsti i stanju. Moraju omekšati, ali ne smiju se raspadati.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Ispiranje</h3>
                    <p>Unutrašnjost crijeva treba isprati kako bi se uklonio višak soli i provjerila prohodnost.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Promjer</h3>
                    <p>Promjer crijeva bira se prema proizvodu. Tanka kobasica i veća salama ne traže isti režim.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Elastičnost</h3>
                    <p>Crijevo mora pratiti smjesu bez pucanja i bez stvaranja praznih, mlitavih dijelova.</p>
                </article>
            </div>
        </section>

        <section class="dcpun-process">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Praktični redoslijed</span>
                <h2>Kako puniti bez zraka, pucanja i praznih mjesta</h2>
            </div>

            <div class="dcpun-step-grid">
                <article>
                    <em>01</em>
                    <h3>Pripremi crijeva</h3>
                    <p>Namakanje, ispiranje i provjera prohodnosti moraju biti gotovi prije vađenja smjese.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Napuni cilindar bez zraka</h3>
                    <p>Smjesu sabij u punilicu tako da se ne stvaraju zračni džepovi već prije početka punjenja.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Vodi pritisak</h3>
                    <p>Pritisak mora biti dovoljan da nema praznina, ali ne toliko jak da puca crijevo.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Zatvori i označi</h3>
                    <p>Vezanje, oblikovanje i označavanje šarže treba napraviti odmah, uredno i bez čekanja.</p>
                </article>
            </div>
        </section>

        <section class="dcpun-pressure">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Pritisak i punjenost</span>
                <h2>Prelabavo i pretvrdo punjenje stvaraju različite probleme</h2>
            </div>

            <div class="dcpun-compare-grid">
                <article>
                    <span>Prelabavo punjenje</span>
                    <h3>Šupljine i nepravilan oblik</h3>
                    <p>U crijevu ostaje zrak, proizvod se neravnomjerno suši i kasnije može imati loš presjek.</p>
                    <strong>Rješenje: puni ravnomjernije, sabij smjesu prije punjenja i izbaci zrak.</strong>
                </article>

                <article>
                    <span>Pretvrdo punjenje</span>
                    <h3>Pucanje crijeva</h3>
                    <p>Prevelik pritisak, slabo namočeno crijevo ili pregruba smjesa mogu otvoriti crijevo.</p>
                    <strong>Rješenje: smanji pritisak, provjeri crijevo i prilagodi brzinu punjenja.</strong>
                </article>

                <article>
                    <span>Ujednačeno punjenje</span>
                    <h3>Stabilan oblik i presjek</h3>
                    <p>Proizvod je čvrst, ali ne napet do pucanja. Nema praznih mjesta ni zračnih džepova.</p>
                    <strong>Rješenje: održavaj jednak ritam i lagano kontroliraj izlaz smjese.</strong>
                </article>
            </div>
        </section>

        <section id="dcpun-checklist" class="dcpun-checklist">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Kontrolna lista</span>
                <h2>Punjenje mora biti mirno, hladno i bez prekida</h2>
                <p>
                    Kontrolna lista vodi kroz pripremu, samo punjenje i završnu provjeru. Neoznačene stavke odmah
                    prikazuju konkretna rješenja, jer u ovoj fazi “vidjet ćemo kasnije” obično znači — kasnije ćemo žaliti.
                </p>
            </div>

            <div class="dcpun-check-shell">
                <div class="dcpun-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije punjenja</button>
                    <button type="button" data-tab="during">Tijekom punjenja</button>
                    <button type="button" data-tab="after">Nakon punjenja</button>
                </div>

                <div class="dcpun-check-panel">
                    <div class="dcpun-progress">
                        <div>
                            <strong id="dcpun-check-title">Prije punjenja</strong>
                            <span id="dcpun-check-count">0/7 označeno</span>
                        </div>
                        <i><b id="dcpun-check-bar"></b></i>
                    </div>

                    <div id="dcpun-check-list" class="dcpun-check-list"></div>

                    <div class="dcpun-solutions">
                        <div class="dcpun-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpun-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpun-solutions-list" class="dcpun-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpun-problems">
            <div class="dcpun-head">
                <span class="dcpun-eyebrow">Problem → rješenje</span>
                <h2>Greške u punjenju koje kasnije izgledaju kao problem fermentacije ili sušenja</h2>
            </div>

            <div class="dcpun-problem-grid">
                <article>
                    <h3>Zračni džepovi u kobasici</h3>
                    <p><strong>Uzrok:</strong> smjesa nije sabijena u cilindru, punjenje je prekidano ili se crijevo vodilo prelabavo.</p>
                    <p><strong>Rješenje:</strong> sabiti smjesu prije punjenja, puniti stalnim ritmom i po potrebi pažljivo izbosti zračne džepove.</p>
                </article>

                <article>
                    <h3>Crijevo puca</h3>
                    <p><strong>Uzrok:</strong> presuh omotač, prevelik pritisak, pogrešan promjer ili neujednačena smjesa.</p>
                    <p><strong>Rješenje:</strong> bolje namočiti crijevo, smanjiti pritisak, provjeriti promjer i ne forsirati punjenje.</p>
                </article>

                <article>
                    <h3>Smjesa izlazi neravnomjerno</h3>
                    <p><strong>Uzrok:</strong> zrak u cilindru, preslaba vezivnost smjese ili začepljenje nastavka.</p>
                    <p><strong>Rješenje:</strong> zaustaviti, ispustiti zrak, očistiti nastavak i provjeriti temperaturu smjese.</p>
                </article>

                <article>
                    <h3>Proizvod je premekan i mlitav</h3>
                    <p><strong>Uzrok:</strong> prelabavo punjenje ili smjesa koja nije dovoljno povezana.</p>
                    <p><strong>Rješenje:</strong> provjeriti vezivnost, puniti čvršće i ujednačenije, ali bez pretjeranog napinjanja crijeva.</p>
                </article>

                <article>
                    <h3>Površina je masna ili ljepljiva</h3>
                    <p><strong>Uzrok:</strong> smjesa se zagrijala, mast se počela razmazivati ili je prethodno mljevenje bilo loše.</p>
                    <p><strong>Rješenje:</strong> zaustaviti rad i vratiti smjesu na hlađenje prije nastavka.</p>
                </article>

                <article>
                    <h3>Loše vezanje krajeva</h3>
                    <p><strong>Uzrok:</strong> previše zraka na kraju, neujednačena punjenost ili nepripremljen konop/klipse.</p>
                    <p><strong>Rješenje:</strong> krajeve zatvoriti odmah, izbaciti zrak i označiti šaržu prije prelaska na fermentaciju.</p>
                </article>
            </div>
        </section>

        <section class="dcpun-next">
            <div>
                <span class="dcpun-eyebrow">Sljedeća faza</span>
                <h2>Nakon punjenja počinje oblikovanje procesa u crijevu</h2>
                <p>
                    Punjeni proizvod ide prema fermentaciji, dimljenju ili sušenju ovisno o tipu proizvoda.
                    Ako je punjenje loše, sljedeće faze samo nasljeđuju zrak, šupljine i neujednačenost.
                </p>
            </div>

            <div class="dcpun-next-actions">
                <a href="<?php echo esc_url(dcpunv010_page_url('proces-izrade/fermentacija')); ?>">Otvori fazu Fermentacija</a>
                <a href="<?php echo esc_url(dcpunv010_page_url('proces-izrade/odlezavanje-smjese')); ?>">Vrati se na Odležavanje</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_punjenje', 'dcpunv010_render');

function dcpunv010_append_to_page($content) {
    static $added = false;

    if ($added || !dcpunv010_enabled()) {
        return $content;
    }

    if (!dcpunv010_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpun-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_punjenje]');
}
add_filter('the_content', 'dcpunv010_append_to_page', 35);

function dcpunv010_assets() {
    if (!dcpunv010_is_page() || !dcpunv010_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpun-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpun-wrap * { box-sizing: border-box; }

        .dcpun-eyebrow {
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

        .dcpun-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.2vw, 82px);
            line-height: .96;
            letter-spacing: -.06em;
            word-break: keep-all;
            hyphens: none;
        }

        .dcpun-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpun-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpun-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpun-hero,
        .dcpun-simulator,
        .dcpun-casing,
        .dcpun-process,
        .dcpun-pressure,
        .dcpun-checklist,
        .dcpun-problems,
        .dcpun-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpun-hero {
            display: grid;
            grid-template-columns: minmax(430px, .92fr) minmax(520px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpun-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpun-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 1.75vw, 21px) !important;
            line-height: 1.58 !important;
        }

        .dcpun-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpun-actions a,
        .dcpun-next-actions a {
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

        .dcpun-actions a:nth-child(2),
        .dcpun-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpun-mini,
        .dcpun-hero-points,
        .dcpun-card-grid,
        .dcpun-step-grid,
        .dcpun-problem-grid,
        .dcpun-compare-grid {
            display: grid;
            gap: 16px;
        }

        .dcpun-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpun-mini div,
        .dcpun-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpun-mini span,
        .dcpun-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpun-mini strong,
        .dcpun-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpun-hero-visual {
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

        .dcpun-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpun-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpun-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 560px;
            color: #fff;
        }

        .dcpun-visual-overlay span {
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

        .dcpun-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 2.8vw, 40px);
            line-height: 1.06;
            letter-spacing: -.035em;
        }

        .dcpun-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpun-simulator,
        .dcpun-casing,
        .dcpun-process,
        .dcpun-pressure,
        .dcpun-checklist,
        .dcpun-problems,
        .dcpun-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpun-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpun-sim-shell,
        .dcpun-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpun-controls,
        .dcpun-output,
        .dcpun-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpun-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpun-control {
            margin-bottom: 18px;
        }

        .dcpun-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpun-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpun-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpun-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpun-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpun-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpun-status h3,
        .dcpun-status p {
            color: #fff;
        }

        .dcpun-status p { opacity: .75; }

        .dcpun-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpun-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpun-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpun-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpun-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpun-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpun-card-grid,
        .dcpun-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpun-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpun-compare-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dcpun-card-grid article,
        .dcpun-step-grid article,
        .dcpun-problem-grid article,
        .dcpun-compare-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
        }

        .dcpun-card-grid b,
        .dcpun-step-grid em {
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

        .dcpun-compare-grid article span {
            display: inline-flex;
            margin-bottom: 12px;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(182,138,58,.14);
            color: #76551e;
            font-size: 11px;
            font-weight: 950;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpun-compare-grid strong {
            display: block;
            margin-top: 12px;
            color: #101722;
            font-size: 14px;
            line-height: 1.5;
        }

        .dcpun-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpun-tabs button {
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

        .dcpun-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpun-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpun-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpun-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpun-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpun-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpun-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpun-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpun-check-item {
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

        .dcpun-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcpun-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpun-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpun-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpun-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpun-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpun-solutions-head span {
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

        .dcpun-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpun-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpun-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpun-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpun-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpun-solution-card p + p {
            margin-top: 8px;
        }

        .dcpun-solution-card strong {
            color: #76551e;
        }

        .dcpun-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpun-next h2,
        .dcpun-next p {
            color: #fff;
        }

        .dcpun-next p { opacity: .78; }

        .dcpun-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1100px) {
            .dcpun-hero,
            .dcpun-sim-shell,
            .dcpun-check-shell,
            .dcpun-next {
                grid-template-columns: 1fr;
            }

            .dcpun-card-grid,
            .dcpun-step-grid,
            .dcpun-problem-grid,
            .dcpun-compare-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dcpun-wrap h1 {
                font-size: clamp(44px, 9vw, 68px);
            }
        }

        @media (max-width: 680px) {
            .dcpun-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpun-hero,
            .dcpun-hero-copy,
            .dcpun-hero-visual,
            .dcpun-simulator,
            .dcpun-casing,
            .dcpun-process,
            .dcpun-pressure,
            .dcpun-checklist,
            .dcpun-problems,
            .dcpun-next {
                border-radius: 24px;
            }

            .dcpun-mini,
            .dcpun-hero-points,
            .dcpun-card-grid,
            .dcpun-step-grid,
            .dcpun-problem-grid,
            .dcpun-compare-grid {
                grid-template-columns: 1fr;
            }

            .dcpun-hero-visual {
                min-height: 540px;
            }

            .dcpun-progress > div,
            .dcpun-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpun-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpun-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpun-wrap')) {
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

            function labelPressure(v) {
                return v === 1 ? 'preslab' : v === 2 ? 'slab' : v === 3 ? 'umjeren' : v === 4 ? 'jak' : 'prejak';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpun-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcpun-temp');
                const bind = n('dcpun-bind');
                const casing = n('dcpun-casing');
                const pressure = n('dcpun-pressure');
                const air = n('dcpun-air');

                document.getElementById('dcpun-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpun-bind-val').textContent = labelQuality(bind);
                document.getElementById('dcpun-casing-val').textContent = labelQuality(casing);
                document.getElementById('dcpun-pressure-val').textContent = labelPressure(pressure);
                document.getElementById('dcpun-air-val').textContent = labelQuality(air);

                let burst = 8 + (4 - casing) * 20 + Math.max(0, pressure - 3) * 22 + Math.max(0, temp - 7) * 5;
                let voids = 10 + (4 - air) * 22 + Math.max(0, 3 - pressure) * 18 + (4 - bind) * 10;
                let slice = 12 + (4 - bind) * 18 + Math.max(0, temp - 5) * 7 + (4 - air) * 10;
                let dry = 10 + (4 - air) * 16 + Math.abs(pressure - 3) * 10 + (4 - casing) * 8;

                burst = clamp(burst, 0, 100);
                voids = clamp(voids, 0, 100);
                slice = clamp(slice, 0, 100);
                dry = clamp(dry, 0, 100);

                setBar('dcpun-burst', 'dcpun-burst-num', burst, true);
                setBar('dcpun-voids', 'dcpun-voids-num', voids, true);
                setBar('dcpun-slice', 'dcpun-slice-num', slice, true);
                setBar('dcpun-dry', 'dcpun-dry-num', dry, true);

                let title = 'Dobri uvjeti za punjenje';
                let text = 'Smjesa je hladna, crijevo je pripremljeno i punjenje se može voditi mirno i ravnomjerno.';
                let advice = 'Puni ravnomjerno, bez prekida i bez pretjeranog zatezanja crijeva.';

                if (burst > 65) {
                    title = 'Rizik pucanja crijeva';
                    text = 'Crijevo nije dovoljno pripremljeno ili je pritisak previsok.';
                    advice = 'Smanji pritisak, provjeri namakanje i prohodnost crijeva te ne forsiraj izlaz smjese.';
                } else if (voids > 65) {
                    title = 'Rizik zračnih džepova';
                    text = 'Zrak može ostati u cilindru, smjesi ili crijevu i kasnije stvoriti šupljine.';
                    advice = 'Sabij smjesu u punilici, puni stalnim ritmom i pažljivo izbaci zrak prije zatvaranja.';
                } else if (slice > 65) {
                    title = 'Rizik lošeg presjeka';
                    text = 'Slaba vezivnost ili topla smjesa mogu dati rasipanje i neujednačen presjek.';
                    advice = 'Vrati smjesu na hladno i ne puni dok ne drži oblik i ne izgleda povezano.';
                } else if (dry > 65) {
                    title = 'Rizik neravnomjernog sušenja';
                    text = 'Prelabavo ili pretvrdo punjenje može kasnije promijeniti tijek sušenja.';
                    advice = 'Ujednači pritisak punjenja i provjeri da proizvod nema praznine ni prenapete dijelove.';
                }

                document.getElementById('dcpun-status-title').textContent = title;
                document.getElementById('dcpun-status-text').textContent = text;
                document.getElementById('dcpun-advice').textContent = advice;
            }

            ['dcpun-temp', 'dcpun-bind', 'dcpun-casing', 'dcpun-pressure', 'dcpun-air'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije punjenja',
                    items: [
                        ['Smjesa je hladna', 'Smjesa nije topla, maziva ni razvodnjena.'],
                        ['Smjesa je povezana', 'Drži oblik i nema slobodne tekućine.'],
                        ['Crijeva su namočena', 'Crijevo je elastično i spremno za punjenje.'],
                        ['Crijeva su isprana', 'Višak soli i nečistoće su uklonjeni.'],
                        ['Punilica je čista', 'Cilindar, nastavak i klip su pripremljeni.'],
                        ['Nastavak odgovara crijevu', 'Promjer nastavka mora odgovarati crijevu i proizvodu.'],
                        ['Konop, klipse ili vezanje su spremni', 'Zatvaranje krajeva ne smije se tražiti kad je proizvod već napunjen.']
                    ]
                },
                during: {
                    title: 'Tijekom punjenja',
                    items: [
                        ['Smjesa ulazi bez zraka', 'Cilindar je napunjen sabijeno i bez velikih džepova.'],
                        ['Pritisak je ravnomjeran', 'Nema naglih prekida, trzaja ni pretjeranog napinjanja.'],
                        ['Crijevo se ne prepunjava', 'Proizvod je čvrst, ali ne napet do pucanja.'],
                        ['Nema praznih mjesta', 'Crijevo nije mlitavo i nema vidljivih rupa.'],
                        ['Temperatura ostaje niska', 'Smjesa se ne grije tijekom punjenja.'],
                        ['Zrak se uklanja odmah', 'Vidljivi džepovi zraka rješavaju se prije nastavka.'],
                        ['Ritam rada je miran', 'Punjenje se ne prekida nepotrebno.']
                    ]
                },
                after: {
                    title: 'Nakon punjenja',
                    items: [
                        ['Krajevi su dobro zatvoreni', 'Nema curenja smjese ni otvorenih završetaka.'],
                        ['Proizvodi su ujednačeni', 'Dužina, punjenost i oblik su što ujednačeniji.'],
                        ['Zračni džepovi su provjereni', 'Vidljivi zrak je uklonjen prije sljedeće faze.'],
                        ['Šarža je označena', 'Zapisani su datum, recept i faza procesa.'],
                        ['Proizvod ide dalje bez čekanja', 'Ne ostaje nepotrebno na toplom.'],
                        ['Površina crijeva je uredna', 'Nema pucanja, curenja ili masnog filma.'],
                        ['Sljedeća faza je pripremljena', 'Fermentacija, dimljenje ili sušenje počinju prema planu.']
                    ]
                }
            };

            const solutions = {
                'Smjesa je hladna': ['Topla smjesa se loše puni i razmazuje mast.', 'Vrati smjesu na hlađenje prije punjenja.'],
                'Smjesa je povezana': ['Slaba vezivnost vodi prema šupljinama i rasipanju presjeka.', 'Kratko doradi smjesu hladno ili provjeri uzrok prije punjenja.'],
                'Crijeva su namočena': ['Suho crijevo puca i loše prima smjesu.', 'Namoči crijeva dok ne postanu elastična.'],
                'Crijeva su isprana': ['Višak soli i nečistoće kvare rad i okus.', 'Isperi unutrašnjost i provjeri prohodnost.'],
                'Punilica je čista': ['Nečista oprema poništava dobar proces.', 'Operi i pripremi cilindar, klip i nastavak.'],
                'Nastavak odgovara crijevu': ['Pogrešan nastavak povećava pucanje i neravnomjerno punjenje.', 'Uzmi nastavak koji odgovara promjeru crijeva.'],
                'Konop, klipse ili vezanje su spremni': ['Kašnjenje nakon punjenja stvara nered i zagrijavanje.', 'Pripremi sve za zatvaranje prije početka.'],

                'Smjesa ulazi bez zraka': ['Zrak u cilindru postaje šupljina u proizvodu.', 'Sabij smjesu i izbaci zrak prije nastavka.'],
                'Pritisak je ravnomjeran': ['Trzanje stvara prazna mjesta i pucanje.', 'Vodi punilicu mirno i stalnim ritmom.'],
                'Crijevo se ne prepunjava': ['Pretvrdo punjenje puca u punjenju ili kasnije.', 'Smanji pritisak i pusti crijevo da se puni bez pretjeranog zatezanja.'],
                'Nema praznih mjesta': ['Praznine znače zrak i neravnomjerno sušenje.', 'Puni čvršće i ravnomjernije, ali bez prenapinjanja.'],
                'Temperatura ostaje niska': ['Toplina kvari strukturu smjese.', 'Radi u manjim serijama i vraćaj smjesu na hladno.'],
                'Zrak se uklanja odmah': ['Zračni džepovi kasnije se ne popravljaju sami.', 'Pažljivo istisni ili izbodi vidljive džepove.'],
                'Ritam rada je miran': ['Prekidi stvaraju zrak i neujednačenost.', 'Pripremi sve prije početka i puni bez nepotrebnog stajanja.'],

                'Krajevi su dobro zatvoreni': ['Loše zatvaranje pušta smjesu i zrak.', 'Ponovno zatvori kraj i provjeri napetost.'],
                'Proizvodi su ujednačeni': ['Neujednačeni komadi različito fermentiraju i suše se.', 'Ujednači dužinu i punjenost koliko je moguće.'],
                'Zračni džepovi su provjereni': ['Zrak stvara šupljine i slabe točke.', 'Pregledaj proizvod i ukloni vidljive džepove.'],
                'Šarža je označena': ['Bez oznake nema kontrole ni ponovljivosti.', 'Označi datum, recept i šaržu.'],
                'Proizvod ide dalje bez čekanja': ['Čekanje na toplom povećava rizik.', 'Odmah nastavi prema planiranoj fazi.'],
                'Površina crijeva je uredna': ['Pucanje, curenje ili masni film znak su problema.', 'Odvoji problematične komade i provjeri uzrok.'],
                'Sljedeća faza je pripremljena': ['Punjenje ne smije završiti u zastoju.', 'Pripremi fermentaciju, dimljenje ili sušenje prije kraja punjenja.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpun-check-list');
                document.getElementById('dcpun-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_punjenje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpun-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_punjenje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpun-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpun-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpun-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpun-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpun-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpun-solutions-title');
                const solList = document.getElementById('dcpun-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpun-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi prema sljedećoj fazi bez nepotrebnog čekanja.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpun-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpun-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpun-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcpunv010_assets', 120);
