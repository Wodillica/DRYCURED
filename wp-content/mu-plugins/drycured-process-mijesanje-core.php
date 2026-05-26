<?php
/**
 * Plugin Name: Drycured Process Mijesanje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/mijesanje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpmi_enabled(): bool {
    return (bool) get_option('drycured_process_mijesanje_enabled', 1);
}

function dcpmi_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/mijesanje';
}

function dcpmi_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpmi_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/mijesanje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpmi_render(): string {
    if (!dcpmi_enabled()) {
        return '';
    }

    $hero_img = dcpmi_image_url('mijesanje-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcpmi-mijesanje" class="dcpmi-wrap" aria-label="Miješanje smjese za suhomesnate proizvode">

        <section class="dcpmi-hero">
            <div class="dcpmi-hero-copy">
                <span class="dcpmi-eyebrow">Faza 05 — miješanje</span>
                <h1>Miješanje</h1>
                <p class="dcpmi-lead">
                    Miješanje nije obično premještanje mesa i začina. To je faza u kojoj se smjesa veže,
                    sol počinje izvlačiti bjelančevine, začini se ravnomjerno raspoređuju, a struktura postaje
                    spremna za punjenje. Ako se ovdje pogriješi, šupljine i rasipanje presjeka često se vide tek kasnije.
                </p>

                <div class="dcpmi-actions">
                    <a href="#dcpmi-simulator">Otvori simulator vezivnosti</a>
                    <a href="<?php echo esc_url(dcpmi_page_url('proces-izrade/odlezavanje')); ?>">Sljedeća faza: Odležavanje</a>
                </div>

                <div class="dcpmi-mini">
                    <div><span>cilj</span><strong>vezana smjesa</strong></div>
                    <div><span>rizik</span><strong>šupljine i rasipanje</strong></div>
                    <div><span>kontrola</span><strong>hladnoća + redoslijed</strong></div>
                </div>
            </div>

            <div class="dcpmi-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Miješanje smjese za suhomesnate proizvode" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpmi-visual-overlay">
                    <span>vezivnost smjese</span>
                    <h2>Dobro miješanje gradi strukturu koju punjenje i sušenje samo nastavljaju.</h2>
                </div>

                <div class="dcpmi-hero-points">
                    <div><span>smjesa</span><strong>hladna</strong></div>
                    <div><span>sol</span><strong>ravnomjerno</strong></div>
                    <div><span>tekućina</span><strong>postupno</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpmi-simulator" class="dcpmi-simulator">
            <div class="dcpmi-head">
                <span class="dcpmi-eyebrow">Aktivni alat</span>
                <h2>Simulator vezivnosti smjese</h2>
                <p>
                    Pomakni klizače i vidi kako temperatura, vrijeme miješanja, stanje mljevenja,
                    redoslijed dodavanja i tekućina utječu na vezivnost smjese. Ovo je edukativni model:
                    ne mijenja recept, nego pokazuje zašto redoslijed i hladnoća nisu ukras nego pravilo.
                </p>
            </div>

            <div class="dcpmi-sim-shell">
                <div class="dcpmi-controls">
                    <h3>Postavi uvjete miješanja</h3>

                    <div class="dcpmi-control">
                        <label>Temperatura smjese <b id="dcpmi-temp-val">3 °C</b></label>
                        <input id="dcpmi-temp" type="range" min="0" max="12" value="3" step="1">
                    </div>

                    <div class="dcpmi-control">
                        <label>Vrijeme miješanja <b id="dcpmi-time-val">6 min</b></label>
                        <input id="dcpmi-time" type="range" min="1" max="16" value="6" step="1">
                    </div>

                    <div class="dcpmi-control">
                        <label>Stanje mljevene smjese <b id="dcpmi-grind-val">dobro</b></label>
                        <input id="dcpmi-grind" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpmi-control">
                        <label>Redoslijed dodavanja <b id="dcpmi-order-val">pravilan</b></label>
                        <input id="dcpmi-order" type="range" min="1" max="4" value="4" step="1">
                    </div>

                    <div class="dcpmi-control">
                        <label>Dodavanje tekućine <b id="dcpmi-liquid-val">postupno</b></label>
                        <input id="dcpmi-liquid" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpmi-note">
                        Ako se koristi tekućina od češnjaka, vino ili voda, dodaje se odmjereno i postupno.
                        Previše tekućine odjednom može stvoriti neravnomjernu smjesu i kasnije šupljine.
                    </div>
                </div>

                <div class="dcpmi-output">
                    <div class="dcpmi-status">
                        <span>procjena</span>
                        <h3 id="dcpmi-status-title">Dobri uvjeti za vezanje smjese</h3>
                        <p id="dcpmi-status-text">
                            Smjesa je hladna, redoslijed je pravilan i miješanje može razviti dobru vezivnost.
                        </p>
                    </div>

                    <div class="dcpmi-risk-bars">
                        <div class="dcpmi-risk">
                            <label>Slaba vezivnost <span id="dcpmi-bind-num">0/100</span></label>
                            <i><b id="dcpmi-bind"></b></i>
                        </div>

                        <div class="dcpmi-risk">
                            <label>Razmazivanje masti <span id="dcpmi-smear-num">0/100</span></label>
                            <i><b id="dcpmi-smear"></b></i>
                        </div>

                        <div class="dcpmi-risk">
                            <label>Šupljine u proizvodu <span id="dcpmi-voids-num">0/100</span></label>
                            <i><b id="dcpmi-voids"></b></i>
                        </div>

                        <div class="dcpmi-risk">
                            <label>Neravnomjerni začini <span id="dcpmi-spice-num">0/100</span></label>
                            <i><b id="dcpmi-spice"></b></i>
                        </div>
                    </div>

                    <div class="dcpmi-advice" id="dcpmi-advice">
                        Nastavi miješati hladno i ravnomjerno dok smjesa ne postane povezana, ali ne topla i maziva.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpmi-order-guide">
            <div class="dcpmi-head">
                <span class="dcpmi-eyebrow">Redoslijed dodavanja</span>
                <h2>Smjesa se gradi redom, ne bacanjem svega odjednom</h2>
                <p>
                    Kod miješanja je redoslijed važan jer sol, začini i tekućine nemaju istu ulogu.
                    Ako se sve doda odjednom i bez kontrole, rezultat može biti neravnomjerna aroma,
                    slaba vezivnost i tekućina koja se ne rasporedi pravilno.
                </p>
            </div>

            <div class="dcpmi-card-grid">
                <article>
                    <b>01</b>
                    <h3>Hladna mljevena smjesa</h3>
                    <p>Smjesa mora ostati hladna. Toplina ubrzava razmazivanje masti i otežava kontrolu teksture.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Sol i osnovni sastojci</h3>
                    <p>Sol mora biti ravnomjerno raspoređena jer sudjeluje u razvoju vezivnosti smjese.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Začini</h3>
                    <p>Suhi začini se dodaju tako da ne ostaju u džepovima, nego se rasporede po cijeloj masi.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Tekućina ako se koristi</h3>
                    <p>Tekućina se dodaje postupno, u tankom mlazu, dok smjesa još može ravnomjerno primati vlagu.</p>
                </article>
            </div>
        </section>

        <section class="dcpmi-binding">
            <div class="dcpmi-head">
                <span class="dcpmi-eyebrow">Znakovi dobre smjese</span>
                <h2>Što znači da je smjesa “uhvatila”?</h2>
            </div>

            <div class="dcpmi-step-grid">
                <article>
                    <em>01</em>
                    <h3>Smjesa se povezuje</h3>
                    <p>Ne raspada se na suhe dijelove, nego počinje držati oblik kao jedinstvena masa.</p>
                </article>

                <article>
                    <em>02</em>
                    <h3>Površina je lagano ljepljiva</h3>
                    <p>To je znak razvoja vezivnosti, ali ne smije postati topla, masna i razmazana.</p>
                </article>

                <article>
                    <em>03</em>
                    <h3>Nema slobodne tekućine</h3>
                    <p>Tekućina ne smije ostati na dnu posude ili u džepovima smjese.</p>
                </article>

                <article>
                    <em>04</em>
                    <h3>Začini su ravnomjerni</h3>
                    <p>Boja, miris i struktura trebaju izgledati jednoliko, bez vidljivih nakupina začina.</p>
                </article>
            </div>
        </section>

        <section id="dcpmi-checklist" class="dcpmi-checklist">
            <div class="dcpmi-head">
                <span class="dcpmi-eyebrow">Kontrolna lista</span>
                <h2>Miješanje mora završiti prije nego smjesa postane topla</h2>
                <p>
                    Kontrolna lista vodi kroz pripremu, miješanje i završnu procjenu. Neoznačene stavke
                    odmah prikazuju konkretna rješenja.
                </p>
            </div>

            <div class="dcpmi-check-shell">
                <div class="dcpmi-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije miješanja</button>
                    <button type="button" data-tab="during">Tijekom miješanja</button>
                    <button type="button" data-tab="after">Nakon miješanja</button>
                </div>

                <div class="dcpmi-check-panel">
                    <div class="dcpmi-progress">
                        <div>
                            <strong id="dcpmi-check-title">Prije miješanja</strong>
                            <span id="dcpmi-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpmi-check-bar"></b></i>
                    </div>

                    <div id="dcpmi-check-list" class="dcpmi-check-list"></div>

                    <div class="dcpmi-solutions">
                        <div class="dcpmi-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpmi-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpmi-solutions-list" class="dcpmi-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpmi-problems">
            <div class="dcpmi-head">
                <span class="dcpmi-eyebrow">Problem → rješenje</span>
                <h2>Greške u miješanju koje se kasnije vide kao šupljine, rasipanje ili loš presjek</h2>
            </div>

            <div class="dcpmi-problem-grid">
                <article>
                    <h3>Smjesa se ne veže</h3>
                    <p><strong>Uzrok:</strong> premalo miješanja, loš redoslijed dodavanja ili smjesa bez pravilnog kontakta soli i mesa.</p>
                    <p><strong>Rješenje:</strong> miješati hladno i postupno dok smjesa ne postane povezana i lagano ljepljiva.</p>
                </article>

                <article>
                    <h3>Smjesa postaje masna i razmazana</h3>
                    <p><strong>Uzrok:</strong> previsoka temperatura, predugo miješanje ili već loše mljevenje.</p>
                    <p><strong>Rješenje:</strong> zaustaviti rad, vratiti smjesu na hlađenje i ne forsirati toplu masu.</p>
                </article>

                <article>
                    <h3>Začini nisu ravnomjerni</h3>
                    <p><strong>Uzrok:</strong> dodavanje u grudama ili preslabo miješanje.</p>
                    <p><strong>Rješenje:</strong> začine rasporediti postupno i provjeriti jednolikost boje i mirisa smjese.</p>
                </article>

                <article>
                    <h3>Tekućina ostaje u džepovima</h3>
                    <p><strong>Uzrok:</strong> prebrzo dodavanje vode, vina ili tekućine od češnjaka.</p>
                    <p><strong>Rješenje:</strong> dodavati u tankom mlazu i stati ako smjesa više ne prima tekućinu pravilno.</p>
                </article>
            </div>
        </section>

        <section class="dcpmi-next">
            <div>
                <span class="dcpmi-eyebrow">Sljedeća faza</span>
                <h2>Nakon miješanja smjesa ide na odležavanje ili punjenje</h2>
                <p>
                    Ako se koristi kratko odležavanje smjese, ono pomaže ujednačavanju okusa i vezivnosti.
                    Ako se ide izravno na punjenje, smjesa mora biti hladna, povezana i bez zračnih džepova.
                </p>
            </div>

            <div class="dcpmi-next-actions">
                <a href="<?php echo esc_url(dcpmi_page_url('proces-izrade/odlezavanje')); ?>">Otvori fazu Odležavanje</a>
                <a href="<?php echo esc_url(dcpmi_page_url('proces-izrade/punjenje')); ?>">Otvori fazu Punjenje</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_mijesanje', 'dcpmi_render');

function dcpmi_append_to_page($content) {
    static $added = false;

    if ($added || !dcpmi_enabled()) {
        return $content;
    }

    if (!dcpmi_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpmi-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_mijesanje]');
}
add_filter('the_content', 'dcpmi_append_to_page', 35);

function dcpmi_assets() {
    if (!dcpmi_is_page() || !dcpmi_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpmi-wrap {
            --ink: #101722;
            --muted: #59636f;
            --gold: #b68a3a;
            --gold2: #f1d889;
            max-width: 1220px;
            margin: 46px auto 90px;
            padding: 0 22px;
            color: var(--ink);
        }

        .dcpmi-wrap * { box-sizing: border-box; }

        .dcpmi-eyebrow {
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

        .dcpmi-wrap h1 {
            margin: 18px 0 16px;
            color: var(--ink);
            font-size: clamp(48px, 6.4vw, 86px);
            line-height: .94;
            letter-spacing: -.065em;
        }

        .dcpmi-wrap h2 {
            margin: 14px 0 12px;
            color: var(--ink);
            font-size: clamp(30px, 4.2vw, 54px);
            line-height: 1.03;
            letter-spacing: -.045em;
        }

        .dcpmi-wrap h3 {
            margin: 0 0 10px;
            color: var(--ink);
            font-size: 21px;
            line-height: 1.16;
        }

        .dcpmi-wrap p {
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .dcpmi-hero,
        .dcpmi-simulator,
        .dcpmi-order-guide,
        .dcpmi-binding,
        .dcpmi-checklist,
        .dcpmi-problems,
        .dcpmi-next {
            border-radius: 34px;
            background: rgba(255,255,255,.70);
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 24px 60px rgba(16,23,34,.08);
        }

        .dcpmi-hero {
            display: grid;
            grid-template-columns: minmax(0, .92fr) minmax(420px, 1.08fr);
            gap: 28px;
            align-items: stretch;
            margin-bottom: 34px;
        }

        .dcpmi-hero-copy {
            padding: clamp(32px, 4.5vw, 58px);
            border-radius: 34px;
            background:
                radial-gradient(circle at 10% 10%, rgba(241,216,137,.28), transparent 32%),
                linear-gradient(135deg, rgba(255,255,255,.86), rgba(255,255,255,.54));
        }

        .dcpmi-lead {
            margin: 0;
            color: #2f3943 !important;
            font-size: clamp(18px, 2vw, 22px) !important;
            line-height: 1.58 !important;
        }

        .dcpmi-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dcpmi-actions a,
        .dcpmi-next-actions a {
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

        .dcpmi-actions a:nth-child(2),
        .dcpmi-next-actions a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
            border: 1px solid rgba(16,23,34,.10);
        }

        .dcpmi-mini,
        .dcpmi-hero-points,
        .dcpmi-card-grid,
        .dcpmi-step-grid,
        .dcpmi-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpmi-mini {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 30px;
        }

        .dcpmi-mini div,
        .dcpmi-hero-points div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpmi-mini span,
        .dcpmi-hero-points span {
            display: block;
            margin-bottom: 4px;
            color: #76551e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .dcpmi-mini strong,
        .dcpmi-hero-points strong {
            display: block;
            color: var(--ink);
            font-size: 17px;
            line-height: 1.2;
        }

        .dcpmi-hero-visual {
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

        .dcpmi-hero-visual img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dcpmi-hero-visual::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(180deg, rgba(16,23,34,.05), rgba(16,23,34,.55)),
                radial-gradient(circle at top right, rgba(241,216,137,.18), transparent 38%);
            pointer-events: none;
        }

        .dcpmi-visual-overlay {
            position: absolute;
            z-index: 2;
            left: 28px;
            right: 28px;
            top: 28px;
            max-width: 470px;
            color: #fff;
        }

        .dcpmi-visual-overlay span {
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

        .dcpmi-visual-overlay h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(26px, 3vw, 42px);
            line-height: 1.04;
        }

        .dcpmi-hero-points {
            position: absolute;
            z-index: 2;
            left: 24px;
            right: 24px;
            bottom: 24px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpmi-simulator,
        .dcpmi-order-guide,
        .dcpmi-binding,
        .dcpmi-checklist,
        .dcpmi-problems,
        .dcpmi-next {
            margin-top: 34px;
            padding: clamp(28px, 4vw, 46px);
        }

        .dcpmi-head {
            max-width: 900px;
            margin-bottom: 24px;
        }

        .dcpmi-sim-shell,
        .dcpmi-check-shell {
            display: grid;
            grid-template-columns: 330px minmax(0, 1fr);
            gap: 20px;
        }

        .dcpmi-controls,
        .dcpmi-output,
        .dcpmi-check-panel {
            border-radius: 28px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
            padding: 24px;
        }

        .dcpmi-output {
            background:
                radial-gradient(circle at top right, rgba(241,216,137,.14), transparent 32%),
                #101722;
            color: #fff;
        }

        .dcpmi-control {
            margin-bottom: 18px;
        }

        .dcpmi-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 7px;
            color: var(--ink);
            font-size: 14px;
            font-weight: 900;
        }

        .dcpmi-control label b {
            color: #76551e;
            font-variant-numeric: tabular-nums;
        }

        .dcpmi-control input[type="range"] {
            width: 100%;
            accent-color: #b68a3a;
        }

        .dcpmi-note {
            padding: 14px;
            border-radius: 18px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.18);
            color: #59636f;
            font-size: 13px;
            line-height: 1.5;
        }

        .dcpmi-status {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
        }

        .dcpmi-status span {
            display: inline-flex;
            margin-bottom: 10px;
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpmi-status h3,
        .dcpmi-status p {
            color: #fff;
        }

        .dcpmi-status p { opacity: .75; }

        .dcpmi-risk-bars {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dcpmi-risk label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 6px;
            color: rgba(255,255,255,.75);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .dcpmi-risk i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpmi-risk b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpmi-risk.is-warning b {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpmi-advice {
            margin-top: 18px;
            padding: 16px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.13);
            color: rgba(255,255,255,.78);
            font-size: 14px;
            line-height: 1.6;
        }

        .dcpmi-card-grid,
        .dcpmi-step-grid,
        .dcpmi-problem-grid {
            display: grid;
            gap: 16px;
        }

        .dcpmi-card-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpmi-step-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dcpmi-problem-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcpmi-card-grid article,
        .dcpmi-step-grid article,
        .dcpmi-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
            box-shadow: 0 18px 42px rgba(16,23,34,.06);
        }

        .dcpmi-card-grid b,
        .dcpmi-step-grid em {
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

        .dcpmi-tabs {
            display: grid;
            gap: 10px;
            align-content: start;
        }

        .dcpmi-tabs button {
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

        .dcpmi-tabs button.is-active {
            background: #101722;
            color: #fff;
        }

        .dcpmi-progress {
            margin-bottom: 20px;
            padding: 18px;
            border-radius: 22px;
            background: #101722;
            color: #fff;
        }

        .dcpmi-progress > div {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: baseline;
            margin-bottom: 13px;
        }

        .dcpmi-progress strong {
            color: #fff;
            font-size: 19px;
        }

        .dcpmi-progress span {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .dcpmi-progress i {
            display: block;
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
            overflow: hidden;
        }

        .dcpmi-progress b {
            display: block;
            width: 0%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpmi-check-list {
            display: grid;
            gap: 10px;
        }

        .dcpmi-check-item {
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

        .dcpmi-check-item input {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #b68a3a;
        }

        .dcpmi-check-item strong {
            display: block;
            margin-bottom: 3px;
            color: var(--ink);
            font-size: 15.5px;
            line-height: 1.25;
        }

        .dcpmi-check-item span span {
            display: block;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.48;
        }

        .dcpmi-check-item.is-checked {
            background: rgba(182,138,58,.12);
            border-color: rgba(182,138,58,.22);
        }

        .dcpmi-solutions {
            margin-top: 18px;
            padding: 18px;
            border-radius: 22px;
            background: #fffaf0;
            border: 1px solid rgba(182,138,58,.20);
        }

        .dcpmi-solutions-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 14px;
        }

        .dcpmi-solutions-head span {
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

        .dcpmi-solutions-head strong {
            color: #101722;
            font-size: 15px;
            text-align: right;
        }

        .dcpmi-solutions-list {
            display: grid;
            gap: 10px;
        }

        .dcpmi-solution-card {
            padding: 15px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(16,23,34,.08);
        }

        .dcpmi-solution-card h4 {
            margin: 0 0 7px;
            color: var(--ink);
            font-size: 15.5px;
        }

        .dcpmi-solution-card p {
            margin: 0;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .dcpmi-solution-card p + p {
            margin-top: 8px;
        }

        .dcpmi-solution-card strong {
            color: #76551e;
        }

        .dcpmi-next {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 290px;
            gap: 22px;
            align-items: center;
            background:
                radial-gradient(circle at top right, rgba(120,211,255,.12), transparent 34%),
                #101722;
            color: #fff;
        }

        .dcpmi-next h2,
        .dcpmi-next p {
            color: #fff;
        }

        .dcpmi-next p { opacity: .78; }

        .dcpmi-next-actions {
            display: grid;
            gap: 12px;
        }

        @media (max-width: 1000px) {
            .dcpmi-hero,
            .dcpmi-sim-shell,
            .dcpmi-check-shell,
            .dcpmi-next {
                grid-template-columns: 1fr;
            }

            .dcpmi-card-grid,
            .dcpmi-step-grid,
            .dcpmi-problem-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .dcpmi-wrap {
                padding: 0 14px;
                margin-top: 32px;
            }

            .dcpmi-hero,
            .dcpmi-hero-copy,
            .dcpmi-hero-visual,
            .dcpmi-simulator,
            .dcpmi-order-guide,
            .dcpmi-binding,
            .dcpmi-checklist,
            .dcpmi-problems,
            .dcpmi-next {
                border-radius: 24px;
            }

            .dcpmi-mini,
            .dcpmi-hero-points,
            .dcpmi-card-grid,
            .dcpmi-step-grid,
            .dcpmi-problem-grid {
                grid-template-columns: 1fr;
            }

            .dcpmi-hero-visual {
                min-height: 540px;
            }

            .dcpmi-progress > div,
            .dcpmi-solutions-head {
                flex-direction: column;
                gap: 6px;
            }

            .dcpmi-solutions-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpmi-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpmi-wrap')) {
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

            function labelGrind(v) {
                return v === 4 ? 'odlično' : v === 3 ? 'dobro' : v === 2 ? 'upitno' : 'loše';
            }

            function labelOrder(v) {
                return v === 4 ? 'pravilan' : v === 3 ? 'uglavnom dobar' : v === 2 ? 'neuredan' : 'sve odjednom';
            }

            function labelLiquid(v) {
                return v === 4 ? 'nema' : v === 3 ? 'postupno' : v === 2 ? 'prebrzo' : 'odjednom';
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';
                el.closest('.dcpmi-risk').classList.toggle('is-warning', warning && val > 60);
            }

            function updateSimulator() {
                const temp = n('dcpmi-temp');
                const time = n('dcpmi-time');
                const grind = n('dcpmi-grind');
                const order = n('dcpmi-order');
                const liquid = n('dcpmi-liquid');

                document.getElementById('dcpmi-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpmi-time-val').textContent = time.toFixed(0) + ' min';
                document.getElementById('dcpmi-grind-val').textContent = labelGrind(grind);
                document.getElementById('dcpmi-order-val').textContent = labelOrder(order);
                document.getElementById('dcpmi-liquid-val').textContent = labelLiquid(liquid);

                let bind = 45 - time * 3 - (order - 1) * 7 - (grind - 1) * 6 + Math.max(0, temp - 6) * 7;
                let smear = 8 + Math.max(0, temp - 4) * 9 + Math.max(0, time - 10) * 5 + Math.max(0, 3 - grind) * 12;
                let voids = 12 + Math.max(0, 7 - time) * 7 + Math.max(0, 4 - order) * 13 + Math.max(0, 3 - liquid) * 10;
                let spice = 10 + Math.max(0, 4 - order) * 18 + Math.max(0, 6 - time) * 6;

                bind = clamp(bind, 0, 100);
                smear = clamp(smear, 0, 100);
                voids = clamp(voids, 0, 100);
                spice = clamp(spice, 0, 100);

                setBar('dcpmi-bind', 'dcpmi-bind-num', bind, true);
                setBar('dcpmi-smear', 'dcpmi-smear-num', smear, true);
                setBar('dcpmi-voids', 'dcpmi-voids-num', voids, true);
                setBar('dcpmi-spice', 'dcpmi-spice-num', spice, true);

                let title = 'Dobri uvjeti za vezanje smjese';
                let text = 'Smjesa je hladna, redoslijed je pravilan i miješanje može razviti dobru vezivnost.';
                let advice = 'Nastavi miješati hladno i ravnomjerno dok smjesa ne postane povezana, ali ne topla i maziva.';

                if (smear > 65) {
                    title = 'Rizik razmazivanja masti';
                    text = 'Smjesa se previše grije ili je već došla iz mljevenja s lošom strukturom.';
                    advice = 'Zaustavi rad, vrati smjesu na hlađenje i nastavi tek kad je opet hladna i čvrsta.';
                } else if (bind > 65) {
                    title = 'Slaba vezivnost smjese';
                    text = 'Smjesa vjerojatno neće dobro držati oblik i može se kasnije rasipati u presjeku.';
                    advice = 'Provjeri redoslijed, sol i vrijeme miješanja. Smjesa mora postati povezana i lagano ljepljiva.';
                } else if (voids > 65) {
                    title = 'Rizik šupljina u proizvodu';
                    text = 'Loša vezivnost ili prebrzo dodana tekućina mogu stvoriti džepove i neravnomjerno punjenje.';
                    advice = 'Tekućinu dodaj postupno, smjesu poveži prije punjenja i izbjegni zračne džepove.';
                } else if (spice > 65) {
                    title = 'Začini nisu ravnomjerno raspoređeni';
                    text = 'Nepravilan redoslijed ili premalo miješanja može ostaviti džepove začina.';
                    advice = 'Dodaj začine postupno i miješaj do jednolikog mirisa, boje i rasporeda.';
                }

                document.getElementById('dcpmi-status-title').textContent = title;
                document.getElementById('dcpmi-status-text').textContent = text;
                document.getElementById('dcpmi-advice').textContent = advice;
            }

            ['dcpmi-temp', 'dcpmi-time', 'dcpmi-grind', 'dcpmi-order', 'dcpmi-liquid'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', updateSimulator);
            });

            updateSimulator();

            const checklistData = {
                before: {
                    title: 'Prije miješanja',
                    items: [
                        ['Smjesa je hladna', 'Mljevena smjesa nije omekšala ni postala maziva.'],
                        ['Sol i začini su izvagani', 'Količine su pripremljene prije početka miješanja.'],
                        ['Tekućina je odmjerena ako se koristi', 'Voda, vino ili tekućina od češnjaka ne dodaju se od oka.'],
                        ['Posuda je dovoljno velika', 'Smjesa se mora moći ravnomjerno okretati.'],
                        ['Redoslijed dodavanja je jasan', 'Ne dodaje se sve odjednom bez plana.'],
                        ['Sljedeća faza je pripremljena', 'Punilica, crijeva ili odležavanje ne smiju čekati u kaosu.']
                    ]
                },
                during: {
                    title: 'Tijekom miješanja',
                    items: [
                        ['Smjesa se povezuje', 'Postaje jedinstvena masa, a ne rastresiti dijelovi.'],
                        ['Temperatura ostaje niska', 'Ako smjesa omekša, treba stati i hladiti.'],
                        ['Začini su ravnomjerni', 'Nema vidljivih nakupina začina.'],
                        ['Tekućina se dodaje postupno', 'Smjesa mora primati tekućinu bez džepova i lokvi.'],
                        ['Mast se ne razmazuje', 'Nema masnog filma po posudi ili površini smjese.'],
                        ['Ne miješa se predugo', 'Cilj je vezivnost, ne zagrijavanje i razbijanje strukture.']
                    ]
                },
                after: {
                    title: 'Nakon miješanja',
                    items: [
                        ['Smjesa drži oblik', 'Povezana je i spremna za punjenje ili kratko odležavanje.'],
                        ['Nema slobodne tekućine', 'Na dnu posude nema odvojene vode, vina ili soka.'],
                        ['Nema zračnih džepova', 'Smjesa se prije punjenja može sabiti bez praznina.'],
                        ['Bilješka šarže je dopunjena', 'Zapisano je vrijeme miješanja, temperatura i opažanja.'],
                        ['Smjesa je vraćena na hladno', 'Ne čeka na toplom prije punjenja.'],
                        ['Sljedeći korak je jasan', 'Ide li se na odležavanje ili odmah na punjenje.']
                    ]
                }
            };

            const solutions = {
                'Smjesa je hladna': ['Topla smjesa brzo razmazuje mast.', 'Vrati smjesu na hlađenje i nastavi tek kad je opet čvrsta.'],
                'Sol i začini su izvagani': ['Nepripremljene količine vode do neravnomjernog rada.', 'Izvaži sve prije početka i ne dodaj od oka.'],
                'Tekućina je odmjerena ako se koristi': ['Previše tekućine kvari vezanje i može stvoriti džepove.', 'Odmjeri tekućinu i dodaj je postupno.'],
                'Posuda je dovoljno velika': ['Premala posuda ne dopušta ravnomjerno miješanje.', 'Koristi veću posudu ili miješaj u manjim serijama.'],
                'Redoslijed dodavanja je jasan': ['Sve odjednom često daje neravnomjernu smjesu.', 'Prvo pripremi hladnu smjesu, zatim sol i začine, a tekućinu dodaj postupno.'],
                'Sljedeća faza je pripremljena': ['Čekanje grije smjesu.', 'Pripremi punilicu, crijeva ili posudu za odležavanje prije kraja miješanja.'],

                'Smjesa se povezuje': ['Ako se ne povezuje, kasnije može nastati rasipanje presjeka.', 'Nastavi kratko miješati hladno dok ne postane povezana i lagano ljepljiva.'],
                'Temperatura ostaje niska': ['Toplina je glavni neprijatelj strukture.', 'Prekini i ohladi smjesu prije nastavka.'],
                'Začini su ravnomjerni': ['Džepovi začina daju neujednačen okus.', 'Miješaj do jednolikog rasporeda boje i mirisa.'],
                'Tekućina se dodaje postupno': ['Tekućina odjednom stvara džepove.', 'Dodaj u tankom mlazu i stani ako se odvaja.'],
                'Mast se ne razmazuje': ['Masni film znači da struktura popušta.', 'Ohladi smjesu i skrati rad.'],
                'Ne miješa se predugo': ['Predugo miješanje grije i oštećuje smjesu.', 'Stani kad je smjesa povezana, ne čekaj da postane topla i maziva.'],

                'Smjesa drži oblik': ['Ako ne drži oblik, punjenje će biti rizično.', 'Kratko nastavi miješati hladno ili provjeri uzrok slabe vezivnosti.'],
                'Nema slobodne tekućine': ['Slobodna tekućina znači da smjesa nije pravilno primila dodatak.', 'Ne puni dok se problem ne riješi; sljedeći put dodavati tekućinu sporije.'],
                'Nema zračnih džepova': ['Zrak kasnije daje šupljine i loš presjek.', 'Sabij smjesu prije punjenja i puni bez prekida.'],
                'Bilješka šarže je dopunjena': ['Bez bilješki nema ponovljivosti.', 'Upiši vrijeme, temperaturu i opažanja.'],
                'Smjesa je vraćena na hladno': ['Toplo čekanje poništava dobru strukturu.', 'Vrati smjesu u hladnjak do punjenja.'],
                'Sljedeći korak je jasan': ['Neplaniran nastavak stvara čekanje.', 'Odluči ide li smjesa na odležavanje ili odmah na punjenje.']
            };

            let activeTab = 'before';

            function renderChecklist(tab) {
                activeTab = tab;
                const cfg = checklistData[tab];
                const list = document.getElementById('dcpmi-check-list');
                document.getElementById('dcpmi-check-title').textContent = cfg.title;

                list.innerHTML = cfg.items.map(function (item, index) {
                    const k = 'drycured_mijesanje_check_' + tab + '_' + index;
                    const checked = localStorage.getItem(k) === '1';
                    return `
                        <label class="dcpmi-check-item ${checked ? 'is-checked' : ''}">
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
                        const k = 'drycured_mijesanje_check_' + activeTab + '_' + box.getAttribute('data-index');
                        localStorage.setItem(k, box.checked ? '1' : '0');
                        box.closest('.dcpmi-check-item').classList.toggle('is-checked', box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist() {
                const boxes = Array.from(document.querySelectorAll('.dcpmi-check-item input'));
                const checked = boxes.filter(function (b) { return b.checked; }).length;
                const total = boxes.length || 1;
                const pct = Math.round((checked / total) * 100);

                document.getElementById('dcpmi-check-count').textContent = checked + '/' + total + ' označeno';
                document.getElementById('dcpmi-check-bar').style.width = pct + '%';

                const unchecked = boxes.map(function (box) {
                    if (box.checked) return null;
                    const title = box.closest('.dcpmi-check-item').querySelector('strong').textContent.trim();
                    return title;
                }).filter(Boolean);

                const solTitle = document.getElementById('dcpmi-solutions-title');
                const solList = document.getElementById('dcpmi-solutions-list');

                if (!unchecked.length) {
                    solTitle.textContent = 'Sve stavke su označene';
                    solList.innerHTML = '<div class="dcpmi-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi hladno i bez nepotrebnog čekanja prije sljedeće faze.</p></div>';
                    return;
                }

                solTitle.textContent = unchecked.length + ' stavki traži pažnju';
                solList.innerHTML = unchecked.map(function (title) {
                    const s = solutions[title] || ['Stavka traži provjeru.', 'Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `
                        <article class="dcpmi-solution-card">
                            <h4>${title}</h4>
                            <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                            <p><strong>Što napraviti:</strong> ${s[1]}</p>
                        </article>
                    `;
                }).join('');
            }

            document.querySelectorAll('.dcpmi-tabs button').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.querySelectorAll('.dcpmi-tabs button').forEach(function (b) {
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
add_action('wp_head', 'dcpmi_assets', 120);
