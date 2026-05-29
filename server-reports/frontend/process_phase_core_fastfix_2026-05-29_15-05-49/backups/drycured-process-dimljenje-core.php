<?php
/**
 * Plugin Name: Drycured Process Dimljenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/dimljenje/ s mostom prema Planeru dimljenja.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpdimv010_enabled(): bool {
    return (bool) get_option('drycured_process_dimljenje_enabled', 1);
}

function dcpdimv010_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/dimljenje',
    ], true);
}

function dcpdimv010_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpdimv010_tool_url(): string {
    $manual = trim((string) get_option('drycured_smoking_planner_url', ''));
    if ($manual !== '') {
        return esc_url($manual);
    }
    return esc_url(home_url('/alati/?alat=planer-dimljenja#planer-dimljenja'));
}

function dcpdimv010_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/dimljenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;

    if (file_exists($path)) {
        return content_url($rel);
    }

    return '';
}

function dcpdimv010_render(): string {
    if (!dcpdimv010_enabled()) {
        return '';
    }

    $hero_img = dcpdimv010_image_url('dimljenje-hero-v01.jpg');
    $tool_url = dcpdimv010_tool_url();

    ob_start();
    ?>
    <main id="dcpdim-dimljenje" class="dcpdim-wrap" aria-label="Dimljenje suhomesnatih proizvoda">

        <section class="dcpdim-hero">
            <div class="dcpdim-hero-copy">
                <span class="dcpdim-eyebrow">Faza 09 — dimljenje</span>
                <h1>Dimljenje</h1>
                <p class="dcpdim-lead">
                    Dimljenje nije bojenje proizvoda dimom. To je kontrolirana faza u kojoj se usklađuju temperatura,
                    vlažnost, jačina dima, trajanje i odmori između ciklusa. Dobar dim gradi aromu i površinu;
                    loš dim stvara gorčinu, kiselkast miris, tamnu koru i neravnomjerno sušenje.
                </p>

                <div class="dcpdim-actions">
                    <a href="#dcpdim-simulator">Otvori simulator dimljenja</a>
                    <a href="<?php echo esc_url($tool_url); ?>">Otvori Planer dimljenja</a>
                </div>

                <div class="dcpdim-mini">
                    <div><span>cilj</span><strong>čist dim i aroma</strong></div>
                    <div><span>rizik</span><strong>gorčina i kora</strong></div>
                    <div><span>alat</span><strong>Planer dimljenja</strong></div>
                </div>
            </div>

            <div class="dcpdim-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Dimljenje suhomesnatih proizvoda u pušnici" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpdim-visual-overlay">
                    <span>kontrolirani dim</span>
                    <h2>Dim treba voditi proizvod, a ne prekriti sve što je prije toga bilo dobro napravljeno.</h2>
                </div>

                <div class="dcpdim-hero-points">
                    <div><span>dim</span><strong>tanak i čist</strong></div>
                    <div><span>temperatura</span><strong>stabilna</strong></div>
                    <div><span>odmor</span><strong>obavezan</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpdim-simulator" class="dcpdim-simulator">
            <div class="dcpdim-head">
                <span class="dcpdim-eyebrow">Edukativna procjena</span>
                <h2>Simulator rizika dimljenja</h2>
                <p>
                    Ovaj alat ne zamjenjuje Planer dimljenja. On objašnjava što se događa kada se temperatura,
                    vlažnost, jačina dima, trajanje i odmor između ciklusa pomaknu iz sigurnog i kvalitetnog okvira.
                </p>
            </div>

            <div class="dcpdim-sim-shell">
                <div class="dcpdim-controls">
                    <h3>Postavi uvjete dimljenja</h3>

                    <div class="dcpdim-control">
                        <label>Temperatura pušnice <b id="dcpdim-temp-val">18 °C</b></label>
                        <input id="dcpdim-temp" type="range" min="8" max="45" value="18" step="1">
                    </div>

                    <div class="dcpdim-control">
                        <label>Relativna vlaga <b id="dcpdim-rh-val">78 %</b></label>
                        <input id="dcpdim-rh" type="range" min="45" max="95" value="78" step="1">
                    </div>

                    <div class="dcpdim-control">
                        <label>Jačina dima <b id="dcpdim-smoke-val">umjerena</b></label>
                        <input id="dcpdim-smoke" type="range" min="1" max="5" value="3" step="1">
                    </div>

                    <div class="dcpdim-control">
                        <label>Trajanje ciklusa <b id="dcpdim-time-val">6 h</b></label>
                        <input id="dcpdim-time" type="range" min="1" max="24" value="6" step="1">
                    </div>

                    <div class="dcpdim-control">
                        <label>Odmor/prozračivanje <b id="dcpdim-rest-val">dobro</b></label>
                        <input id="dcpdim-rest" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpdim-note">
                        Za konkretan raspored ciklusa, trajanja, odmora i tip proizvoda koristi Planer dimljenja.
                        Ova procjena služi da korisnik razumije zašto “više dima” nije automatski bolje.
                    </div>
                </div>

                <div class="dcpdim-output">
                    <div class="dcpdim-status">
                        <span>procjena</span>
                        <h3 id="dcpdim-status-title">Dobri uvjeti za blago dimljenje</h3>
                        <p id="dcpdim-status-text">
                            Temperatura, vlaga i dim su u mirnom odnosu. Proces se može voditi kontrolirano.
                        </p>
                    </div>

                    <div class="dcpdim-risk-bars">
                        <div class="dcpdim-risk">
                            <label>Rizik kiselog/gorkog dima <span id="dcpdim-bitter-num">0/100</span></label>
                            <i><b id="dcpdim-bitter"></b></i>
                        </div>

                        <div class="dcpdim-risk">
                            <label>Rizik površinske kore <span id="dcpdim-case-num">0/100</span></label>
                            <i><b id="dcpdim-case"></b></i>
                        </div>

                        <div class="dcpdim-risk">
                            <label>Rizik preslabog dima <span id="dcpdim-weak-num">0/100</span></label>
                            <i><b id="dcpdim-weak"></b></i>
                        </div>

                        <div class="dcpdim-risk">
                            <label>Rizik zastoja sušenja <span id="dcpdim-stall-num">0/100</span></label>
                            <i><b id="dcpdim-stall"></b></i>
                        </div>
                    </div>

                    <div class="dcpdim-advice" id="dcpdim-advice">
                        Za stvarni raspored ciklusa otvori Planer dimljenja i prilagodi proces tipu proizvoda.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpdim-tool-bridge">
            <div>
                <span class="dcpdim-eyebrow">Glavni alat</span>
                <h2>Za raspored ciklusa koristi Planer dimljenja</h2>
                <p>
                    Ova stranica objašnjava logiku dimljenja i najčešće greške. Planer dimljenja služi za praktičnu
                    organizaciju ciklusa: kada dimiti, koliko odmarati, kada prozračiti i kako ne pretjerati s dimom.
                </p>
            </div>

            <div class="dcpdim-tool-actions">
                <a href="<?php echo esc_url($tool_url); ?>">Otvori Planer dimljenja</a>
                <small>Dimljenje je proces u ciklusima. Jedan dugi dim često napravi više štete nego nekoliko mirnih, kontroliranih ciklusa.</small>
            </div>
        </section>

        <section class="dcpdim-principles">
            <div class="dcpdim-head">
                <span class="dcpdim-eyebrow">Što dobar dim radi</span>
                <h2>Dim mora dodati aromu, boju i zaštitu — bez gušenja proizvoda</h2>
            </div>

            <div class="dcpdim-card-grid">
                <article>
                    <b>01</b>
                    <h3>Aroma</h3>
                    <p>Blag i čist dim daje ugodnu aromu. Težak, vlažan ili prljav dim daje gorčinu i neugodan miris.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Boja</h3>
                    <p>Boja se razvija postupno. Pretjerano dimljenje ne znači bolji proizvod, nego često masku preko greške.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Površina</h3>
                    <p>Površina mora primati dim, ali ne smije se prerano zatvoriti i stvoriti tvrdu koru.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Ritam</h3>
                    <p>Dimljenje traži cikluse i odmor. Odmor omogućuje izjednačavanje površine, vlage i mirisa.</p>
                </article>
            </div>
        </section>

        <section id="dcpdim-checklist" class="dcpdim-checklist">
            <div class="dcpdim-head">
                <span class="dcpdim-eyebrow">Kontrolna lista</span>
                <h2>Dimljenje se vodi promatranjem, a ne tvrdoglavošću</h2>
                <p>
                    Kontrolna lista vodi kroz pripremu, dimljenje i odmor. Ako neka stavka nije u redu, prikazuje se
                    konkretno rješenje.
                </p>
            </div>

            <div class="dcpdim-check-shell">
                <div class="dcpdim-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije dimljenja</button>
                    <button type="button" data-tab="during">Tijekom dimljenja</button>
                    <button type="button" data-tab="after">Nakon ciklusa</button>
                </div>

                <div class="dcpdim-check-panel">
                    <div class="dcpdim-progress">
                        <div>
                            <strong id="dcpdim-check-title">Prije dimljenja</strong>
                            <span id="dcpdim-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpdim-check-bar"></b></i>
                    </div>

                    <div id="dcpdim-check-list" class="dcpdim-check-list"></div>

                    <div class="dcpdim-solutions">
                        <div class="dcpdim-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpdim-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpdim-solutions-list" class="dcpdim-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpdim-problems">
            <div class="dcpdim-head">
                <span class="dcpdim-eyebrow">Problem → rješenje</span>
                <h2>Greške u dimljenju koje kasnije izgledaju kao problem sušenja ili zrenja</h2>
            </div>

            <div class="dcpdim-problem-grid">
                <article>
                    <h3>Gorak ili kiseo miris dima</h3>
                    <p><strong>Uzrok:</strong> prejak, vlažan ili prljav dim; loše izgaranje; premalo prozračivanja.</p>
                    <p><strong>Rješenje:</strong> smanjiti dim, osigurati bolji dotok zraka i ubaciti odmor/prozračivanje.</p>
                </article>

                <article>
                    <h3>Pretamna površina</h3>
                    <p><strong>Uzrok:</strong> predugi ciklus, prejak dim ili previsoka temperatura.</p>
                    <p><strong>Rješenje:</strong> skratiti cikluse, smanjiti jačinu dima i nastaviti blaže.</p>
                </article>

                <article>
                    <h3>Tvrda površinska kora</h3>
                    <p><strong>Uzrok:</strong> preniska vlaga, pretopla pušnica ili prejako strujanje zraka.</p>
                    <p><strong>Rješenje:</strong> stabilizirati vlagu, smanjiti temperaturu i ne forsirati brzo sušenje.</p>
                </article>

                <article>
                    <h3>Dim se ne prima ravnomjerno</h3>
                    <p><strong>Uzrok:</strong> proizvodi su preblizu, pušnica nejednako vuče ili je površina previše mokra.</p>
                    <p><strong>Rješenje:</strong> razmaknuti proizvode, provjeriti cirkulaciju i pričekati da površina bude primjereno prosušena.</p>
                </article>
            </div>
        </section>

        <section class="dcpdim-next">
            <div>
                <span class="dcpdim-eyebrow">Sljedeća faza</span>
                <h2>Nakon dimljenja proizvod mora nastaviti mirno sušenje</h2>
                <p>
                    Dimljenje ne završava posao. Nakon dima treba kontrolirati površinu, miris, boju i prijelaz prema sušenju.
                    Prejak dim i loša površina kasnije stvaraju tvrdu koru i sporije otpuštanje vlage.
                </p>
            </div>

            <div class="dcpdim-next-actions">
                <a href="<?php echo esc_url(dcpdimv010_page_url('proces-izrade/susenje')); ?>">Otvori fazu Sušenje</a>
                <a href="<?php echo esc_url(dcpdimv010_page_url('proces-izrade/fermentacija')); ?>">Vrati se na Fermentaciju</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_dimljenje', 'dcpdimv010_render');

function dcpdimv010_append_to_page($content) {
    static $added = false;

    if ($added || !dcpdimv010_enabled()) {
        return $content;
    }

    if (!dcpdimv010_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpdim-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_dimljenje]');
}
add_filter('the_content', 'dcpdimv010_append_to_page', 35);

function dcpdimv010_assets() {
    if (!dcpdimv010_is_page() || !dcpdimv010_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpdim-wrap{--ink:#101722;--muted:#59636f;--gold:#b68a3a;--gold2:#f1d889;max-width:1220px;margin:46px auto 90px;padding:0 22px;color:var(--ink)}
        .dcpdim-wrap *{box-sizing:border-box}
        .dcpdim-eyebrow{display:inline-flex;width:max-content;min-height:30px;align-items:center;padding:7px 12px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpdim-wrap h1{margin:18px 0 16px;color:var(--ink);font-size:clamp(48px,6.2vw,82px);line-height:.96;letter-spacing:-.06em;word-break:keep-all;hyphens:none}
        .dcpdim-wrap h2{margin:14px 0 12px;color:var(--ink);font-size:clamp(30px,4.2vw,54px);line-height:1.03;letter-spacing:-.045em}
        .dcpdim-wrap h3{margin:0 0 10px;color:var(--ink);font-size:21px;line-height:1.16}
        .dcpdim-wrap p{color:var(--muted);font-size:16px;line-height:1.7}

        .dcpdim-hero,.dcpdim-simulator,.dcpdim-tool-bridge,.dcpdim-principles,.dcpdim-checklist,.dcpdim-problems,.dcpdim-next{border-radius:34px;background:rgba(255,255,255,.70);border:1px solid rgba(16,23,34,.08);box-shadow:0 24px 60px rgba(16,23,34,.08)}
        .dcpdim-hero{display:grid;grid-template-columns:minmax(430px,.92fr) minmax(520px,1.08fr);gap:28px;align-items:stretch;margin-bottom:34px}
        .dcpdim-hero-copy{padding:clamp(32px,4.5vw,58px);border-radius:34px;background:radial-gradient(circle at 10% 10%,rgba(241,216,137,.28),transparent 32%),linear-gradient(135deg,rgba(255,255,255,.86),rgba(255,255,255,.54))}
        .dcpdim-lead{margin:0;color:#2f3943!important;font-size:clamp(18px,1.75vw,21px)!important;line-height:1.58!important}
        .dcpdim-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}
        .dcpdim-actions a,.dcpdim-next-actions a,.dcpdim-tool-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border-radius:999px;background:#101722;color:#fff!important;text-decoration:none!important;font-weight:900;box-shadow:0 16px 34px rgba(16,23,34,.14)}
        .dcpdim-actions a:nth-child(2),.dcpdim-tool-actions a{background:#f1d889;color:#101722!important}
        .dcpdim-next-actions a:nth-child(2){background:#fff;color:#101722!important;border:1px solid rgba(16,23,34,.10)}
        .dcpdim-mini,.dcpdim-hero-points,.dcpdim-card-grid,.dcpdim-problem-grid{display:grid;gap:16px}
        .dcpdim-mini{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:30px}
        .dcpdim-mini div,.dcpdim-hero-points div{padding:16px;border-radius:20px;background:rgba(255,255,255,.72);border:1px solid rgba(16,23,34,.08)}
        .dcpdim-mini span,.dcpdim-hero-points span{display:block;margin-bottom:4px;color:#76551e;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}
        .dcpdim-mini strong,.dcpdim-hero-points strong{display:block;color:var(--ink);font-size:17px;line-height:1.2}

        .dcpdim-hero-visual{position:relative;min-height:560px;overflow:hidden;border-radius:34px;background:radial-gradient(circle at 35% 20%,rgba(241,216,137,.24),transparent 30%),radial-gradient(circle at 75% 75%,rgba(120,211,255,.14),transparent 32%),#101722;box-shadow:0 30px 70px rgba(16,23,34,.24)}
        .dcpdim-hero-visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .dcpdim-hero-visual::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(16,23,34,.05),rgba(16,23,34,.58)),radial-gradient(circle at top right,rgba(241,216,137,.18),transparent 38%);pointer-events:none}
        .dcpdim-visual-overlay{position:absolute;z-index:2;left:28px;right:28px;top:28px;max-width:560px;color:#fff}
        .dcpdim-visual-overlay span{display:inline-flex;margin-bottom:14px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.14);color:#f1d889;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;backdrop-filter:blur(10px)}
        .dcpdim-visual-overlay h2{margin:0;color:#fff;font-size:clamp(26px,2.8vw,40px);line-height:1.06;letter-spacing:-.035em}
        .dcpdim-hero-points{position:absolute;z-index:2;left:24px;right:24px;bottom:24px;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}

        .dcpdim-simulator,.dcpdim-tool-bridge,.dcpdim-principles,.dcpdim-checklist,.dcpdim-problems,.dcpdim-next{margin-top:34px;padding:clamp(28px,4vw,46px)}
        .dcpdim-head{max-width:900px;margin-bottom:24px}
        .dcpdim-sim-shell,.dcpdim-check-shell{display:grid;grid-template-columns:330px minmax(0,1fr);gap:20px}
        .dcpdim-controls,.dcpdim-output,.dcpdim-check-panel{border-radius:28px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06);padding:24px}
        .dcpdim-output{background:radial-gradient(circle at top right,rgba(241,216,137,.14),transparent 32%),#101722;color:#fff}
        .dcpdim-control{margin-bottom:18px}
        .dcpdim-control label{display:flex;justify-content:space-between;gap:12px;margin-bottom:7px;color:var(--ink);font-size:14px;font-weight:900}
        .dcpdim-control label b{color:#76551e;font-variant-numeric:tabular-nums}
        .dcpdim-control input[type="range"]{width:100%;accent-color:#b68a3a}
        .dcpdim-note{padding:14px;border-radius:18px;background:#fffaf0;border:1px solid rgba(182,138,58,.18);color:#59636f;font-size:13px;line-height:1.5}
        .dcpdim-status{padding:18px;border-radius:22px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13)}
        .dcpdim-status span{display:inline-flex;margin-bottom:10px;color:#f1d889;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpdim-status h3,.dcpdim-status p{color:#fff}.dcpdim-status p{opacity:.75}
        .dcpdim-risk-bars{display:grid;gap:12px;margin-top:18px}
        .dcpdim-risk label{display:flex;justify-content:space-between;gap:12px;margin-bottom:6px;color:rgba(255,255,255,.75);font-size:12px;font-weight:900;text-transform:uppercase}
        .dcpdim-risk i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden}
        .dcpdim-risk b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff);transition:width .2s ease}
        .dcpdim-risk.is-warning b{background:linear-gradient(90deg,#f1d889,#ff9a76)}
        .dcpdim-advice{margin-top:18px;padding:16px;border-radius:20px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.78);font-size:14px;line-height:1.6}

        .dcpdim-tool-bridge,.dcpdim-next{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:22px;align-items:center;background:radial-gradient(circle at top right,rgba(120,211,255,.12),transparent 34%),#101722;color:#fff}
        .dcpdim-tool-bridge h2,.dcpdim-tool-bridge p,.dcpdim-next h2,.dcpdim-next p{color:#fff}
        .dcpdim-tool-bridge p,.dcpdim-next p{opacity:.78}
        .dcpdim-tool-actions,.dcpdim-next-actions{display:grid;gap:12px}
        .dcpdim-tool-actions small{color:rgba(255,255,255,.68);font-size:12px;line-height:1.45}

        .dcpdim-card-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
        .dcpdim-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .dcpdim-card-grid article,.dcpdim-problem-grid article{padding:22px;border-radius:24px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06)}
        .dcpdim-card-grid b{display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:30px;margin-bottom:14px;border-radius:999px;background:#101722;color:#fff;font-size:12px;font-weight:900}

        .dcpdim-tabs{display:grid;gap:10px;align-content:start}
        .dcpdim-tabs button{width:100%;text-align:left;border:1px solid rgba(16,23,34,.10);border-radius:18px;padding:15px 16px;background:#fff;color:var(--ink);font-weight:950;cursor:pointer}
        .dcpdim-tabs button.is-active{background:#101722;color:#fff}
        .dcpdim-progress{margin-bottom:20px;padding:18px;border-radius:22px;background:#101722;color:#fff}
        .dcpdim-progress>div{display:flex;justify-content:space-between;gap:14px;align-items:baseline;margin-bottom:13px}
        .dcpdim-progress strong{color:#fff;font-size:19px}
        .dcpdim-progress span{color:rgba(255,255,255,.72);font-size:12px;font-weight:900;white-space:nowrap}
        .dcpdim-progress i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.14);overflow:hidden}
        .dcpdim-progress b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff)}
        .dcpdim-check-list{display:grid;gap:10px}
        .dcpdim-check-item{display:grid;grid-template-columns:28px minmax(0,1fr);gap:12px;align-items:start;padding:14px;border-radius:18px;background:rgba(16,23,34,.035);border:1px solid rgba(16,23,34,.07);cursor:pointer}
        .dcpdim-check-item input{width:20px;height:20px;margin-top:2px;accent-color:#b68a3a}
        .dcpdim-check-item strong{display:block;margin-bottom:3px;color:var(--ink);font-size:15.5px;line-height:1.25}
        .dcpdim-check-item span span{display:block;color:var(--muted);font-size:13.5px;line-height:1.48}
        .dcpdim-check-item.is-checked{background:rgba(182,138,58,.12);border-color:rgba(182,138,58,.22)}
        .dcpdim-solutions{margin-top:18px;padding:18px;border-radius:22px;background:#fffaf0;border:1px solid rgba(182,138,58,.20)}
        .dcpdim-solutions-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:14px}
        .dcpdim-solutions-head span{display:inline-flex;width:max-content;min-height:28px;align-items:center;padding:6px 10px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:11px;font-weight:950;letter-spacing:.10em;text-transform:uppercase}
        .dcpdim-solutions-head strong{color:#101722;font-size:15px;text-align:right}
        .dcpdim-solutions-list{display:grid;gap:10px}
        .dcpdim-solution-card{padding:15px;border-radius:18px;background:#fff;border:1px solid rgba(16,23,34,.08)}
        .dcpdim-solution-card h4{margin:0 0 7px;color:var(--ink);font-size:15.5px}
        .dcpdim-solution-card p{margin:0;color:var(--muted);font-size:13.5px;line-height:1.55}
        .dcpdim-solution-card p+p{margin-top:8px}.dcpdim-solution-card strong{color:#76551e}

        @media(max-width:1100px){
            .dcpdim-hero,.dcpdim-sim-shell,.dcpdim-check-shell,.dcpdim-tool-bridge,.dcpdim-next{grid-template-columns:1fr}
            .dcpdim-card-grid,.dcpdim-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
            .dcpdim-wrap h1{font-size:clamp(44px,9vw,68px)}
        }
        @media(max-width:680px){
            .dcpdim-wrap{padding:0 14px;margin-top:32px}
            .dcpdim-hero,.dcpdim-hero-copy,.dcpdim-hero-visual,.dcpdim-simulator,.dcpdim-tool-bridge,.dcpdim-principles,.dcpdim-checklist,.dcpdim-problems,.dcpdim-next{border-radius:24px}
            .dcpdim-mini,.dcpdim-hero-points,.dcpdim-card-grid,.dcpdim-problem-grid{grid-template-columns:1fr}
            .dcpdim-hero-visual{min-height:540px}
            .dcpdim-progress>div,.dcpdim-solutions-head{flex-direction:column;gap:6px}
            .dcpdim-solutions-head strong{text-align:left}
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpdim-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpdim-wrap')) child.style.display = 'none';
                    });
                }
            }

            function clamp(v,min,max){return Math.max(min,Math.min(max,v));}
            function n(id){return parseFloat(document.getElementById(id).value);}
            function labelSmoke(v){return v===1?'vrlo blag':v===2?'blag':v===3?'umjeren':v===4?'jak':'prejak';}
            function labelRest(v){return v===4?'odlično':v===3?'dobro':v===2?'slabo':'nema';}
            function setBar(id,numId,value,warning){
                const val=clamp(Math.round(value),0,100);
                const el=document.getElementById(id);
                el.style.width=val+'%';
                document.getElementById(numId).textContent=val+'/100';
                el.closest('.dcpdim-risk').classList.toggle('is-warning',warning&&val>60);
            }

            function updateSimulator(){
                const temp=n('dcpdim-temp'), rh=n('dcpdim-rh'), smoke=n('dcpdim-smoke'), time=n('dcpdim-time'), rest=n('dcpdim-rest');

                document.getElementById('dcpdim-temp-val').textContent=temp.toFixed(0)+' °C';
                document.getElementById('dcpdim-rh-val').textContent=rh.toFixed(0)+' %';
                document.getElementById('dcpdim-smoke-val').textContent=labelSmoke(smoke);
                document.getElementById('dcpdim-time-val').textContent=time.toFixed(0)+' h';
                document.getElementById('dcpdim-rest-val').textContent=labelRest(rest);

                let bitter=8+Math.max(0,smoke-3)*22+Math.max(0,time-8)*3+(4-rest)*12+Math.max(0,rh-88)*2;
                let caseRisk=10+Math.max(0,temp-24)*3+Math.max(0,65-rh)*1.6+Math.max(0,time-10)*3+Math.max(0,smoke-3)*10;
                let weak=10+Math.max(0,3-smoke)*22+Math.max(0,4-time)*8;
                let stall=10+Math.max(0,rh-86)*2+Math.max(0,18-temp)*1.5+(4-rest)*7;

                bitter=clamp(bitter,0,100);caseRisk=clamp(caseRisk,0,100);weak=clamp(weak,0,100);stall=clamp(stall,0,100);

                setBar('dcpdim-bitter','dcpdim-bitter-num',bitter,true);
                setBar('dcpdim-case','dcpdim-case-num',caseRisk,true);
                setBar('dcpdim-weak','dcpdim-weak-num',weak,true);
                setBar('dcpdim-stall','dcpdim-stall-num',stall,true);

                let title='Dobri uvjeti za blago dimljenje';
                let text='Temperatura, vlaga i dim su u mirnom odnosu. Proces se može voditi kontrolirano.';
                let advice='Za stvarni raspored ciklusa otvori Planer dimljenja i prilagodi proces tipu proizvoda.';

                if(bitter>65){
                    title='Rizik gorkog ili kiselog dima';
                    text='Dim je prejak, ciklus je predug ili nema dovoljno odmora i prozračivanja.';
                    advice='Smanji jačinu dima, skrati ciklus i ubaci odmor/prozračivanje.';
                } else if(caseRisk>65){
                    title='Rizik tvrde površinske kore';
                    text='Toplina, niska vlaga ili predug ciklus mogu prerano zatvoriti površinu.';
                    advice='Smanji temperaturu, stabiliziraj vlagu i ne forsiraj brzo sušenje kroz dim.';
                } else if(weak>65){
                    title='Dimljenje je preslabo';
                    text='Aroma i boja se možda neće razviti dovoljno za planirani stil proizvoda.';
                    advice='Produži blagi ciklus ili povećaj dim postupno, ne naglo.';
                } else if(stall>65){
                    title='Rizik zastoja i teškog vlažnog dima';
                    text='Previsoka vlaga i slab odmor mogu usporiti proces i dati težak miris.';
                    advice='Uvedi prozračivanje i provjeri vuču pušnice.';
                }

                document.getElementById('dcpdim-status-title').textContent=title;
                document.getElementById('dcpdim-status-text').textContent=text;
                document.getElementById('dcpdim-advice').textContent=advice;
            }

            ['dcpdim-temp','dcpdim-rh','dcpdim-smoke','dcpdim-time','dcpdim-rest'].forEach(function(id){
                const el=document.getElementById(id);
                if(el) el.addEventListener('input',updateSimulator);
            });

            updateSimulator();

            const checklistData={
                before:{title:'Prije dimljenja',items:[
                    ['Površina proizvoda je spremna','Proizvod ne smije biti mokar kao da je upravo izašao iz vode.'],
                    ['Pušnica je čista','Stari katran, čađa i prljav dim kvare aromu.'],
                    ['Drvo je prikladno','Ne koristi se mokro, smolasto ili nepoznato drvo.'],
                    ['Temperatura je stabilna','Pušnica ne smije divljati prije ulaska proizvoda.'],
                    ['Proizvodi imaju razmak','Dim i zrak moraju prolaziti oko svakog komada.'],
                    ['Plan ciklusa je određen','Zna se koliko traje dim i kada ide odmor/prozračivanje.']
                ]},
                during:{title:'Tijekom dimljenja',items:[
                    ['Dim je tanak i čist','Dim ne smije biti gust, težak i zagušljiv.'],
                    ['Temperatura ne skače','Nagli skokovi mijenjaju površinu i sušenje.'],
                    ['Vlaga nije ekstremna','Ni presuho ni prevlažno nije dobro.'],
                    ['Boja se razvija postupno','Pretamna boja prerano je znak pretjerivanja.'],
                    ['Miris je ugodan','Kiseli, oštar ili gorak miris traži korekciju.'],
                    ['Odmori se poštuju','Dimljenje u ciklusima je sigurnije od forsiranja.']
                ]},
                after:{title:'Nakon ciklusa',items:[
                    ['Proizvod se prozračuje','Nakon dima proizvod mora odmoriti.'],
                    ['Površina nije ljepljiva','Ljepljiva površina može značiti previše vlage ili težak dim.'],
                    ['Boja je ravnomjerna','Tamne mrlje ili blijedi dijelovi traže provjeru rasporeda.'],
                    ['Nema gorkog mirisa','Gorčina znači da je dim bio pretežak ili ciklus predug.'],
                    ['Bilješka ciklusa je zapisana','Zapiši trajanje, temperaturu, vlagu, drvo i opažanja.'],
                    ['Sljedeća faza je jasna','Proizvod ide prema sušenju ili novom kontroliranom ciklusu.']
                ]}
            };

            const solutions={
                'Površina proizvoda je spremna':['Mokra površina prima dim neravnomjerno i može dati težak miris.','Pričekaj da se površina prosuši prije dimljenja.'],
                'Pušnica je čista':['Prljava pušnica daje gorčinu i neugodan miris.','Očisti naslage čađe i katrana prije ciklusa.'],
                'Drvo je prikladno':['Loše drvo kvari cijeli proizvod.','Koristi suho, čisto i provjereno drvo za dimljenje.'],
                'Temperatura je stabilna':['Nestabilna temperatura stvara neujednačen proces.','Stabiliziraj pušnicu prije ulaska proizvoda.'],
                'Proizvodi imaju razmak':['Bez razmaka dim ne prolazi ravnomjerno.','Razmakni proizvode i provjeri cirkulaciju.'],
                'Plan ciklusa je određen':['Bez plana lako se pretjera s dimom.','Otvori Planer dimljenja i postavi cikluse.'],

                'Dim je tanak i čist':['Gust dim često daje gorčinu.','Smanji izvor dima i osiguraj bolju vuču.'],
                'Temperatura ne skače':['Skok temperature zatvara površinu i mijenja sušenje.','Smanji loženje i stabiliziraj pušnicu.'],
                'Vlaga nije ekstremna':['Presuho stvara koru, prevlažno daje težak dim.','Prilagodi ventilaciju i ritam ciklusa.'],
                'Boja se razvija postupno':['Prebrza tamna boja znak je pretjerivanja.','Skrati ciklus i uvedi odmor.'],
                'Miris je ugodan':['Oštar ili kiseo miris znak je lošeg dima.','Prozrači, smanji dim i provjeri drvo.'],
                'Odmori se poštuju':['Bez odmora proizvod se guši dimom.','Uvedi pauzu između ciklusa.'],

                'Proizvod se prozračuje':['Bez prozračivanja miris ostaje težak.','Pusti proizvod da odmori u kontroliranim uvjetima.'],
                'Površina nije ljepljiva':['Ljepljivost može značiti previše vlage ili težak dim.','Produži prozračivanje i smanji idući dimni ciklus.'],
                'Boja je ravnomjerna':['Neravnomjerna boja upućuje na loš raspored ili cirkulaciju.','Promijeni položaj proizvoda i provjeri protok zraka.'],
                'Nema gorkog mirisa':['Gorčina ostaje i kasnije se teško popravlja.','Prekini dim, prozrači i nastavi blaže ako je potrebno.'],
                'Bilješka ciklusa je zapisana':['Bez zapisa nema ponovljivosti.','Upiši trajanje, temperaturu, vlagu, drvo i rezultat.'],
                'Sljedeća faza je jasna':['Nakon dima ne smije biti lutanja.','Odredi ide li proizvod na sušenje ili novi blagi ciklus.']
            };

            let activeTab='before';

            function renderChecklist(tab){
                activeTab=tab;
                const cfg=checklistData[tab];
                const list=document.getElementById('dcpdim-check-list');
                document.getElementById('dcpdim-check-title').textContent=cfg.title;

                list.innerHTML=cfg.items.map(function(item,index){
                    const k='drycured_dimljenje_check_'+tab+'_'+index;
                    const checked=localStorage.getItem(k)==='1';
                    return `<label class="dcpdim-check-item ${checked?'is-checked':''}">
                        <input type="checkbox" data-index="${index}" ${checked?'checked':''}>
                        <span><strong>${item[0]}</strong><span>${item[1]}</span></span>
                    </label>`;
                }).join('');

                list.querySelectorAll('input[type="checkbox"]').forEach(function(box){
                    box.addEventListener('change',function(){
                        const k='drycured_dimljenje_check_'+activeTab+'_'+box.getAttribute('data-index');
                        localStorage.setItem(k,box.checked?'1':'0');
                        box.closest('.dcpdim-check-item').classList.toggle('is-checked',box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist(){
                const boxes=Array.from(document.querySelectorAll('.dcpdim-check-item input'));
                const checked=boxes.filter(function(b){return b.checked;}).length;
                const total=boxes.length||1;
                const pct=Math.round((checked/total)*100);

                document.getElementById('dcpdim-check-count').textContent=checked+'/'+total+' označeno';
                document.getElementById('dcpdim-check-bar').style.width=pct+'%';

                const unchecked=boxes.map(function(box){
                    if(box.checked) return null;
                    return box.closest('.dcpdim-check-item').querySelector('strong').textContent.trim();
                }).filter(Boolean);

                const solTitle=document.getElementById('dcpdim-solutions-title');
                const solList=document.getElementById('dcpdim-solutions-list');

                if(!unchecked.length){
                    solTitle.textContent='Sve stavke su označene';
                    solList.innerHTML='<div class="dcpdim-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi prema planu i bilježi svaki ciklus dimljenja.</p></div>';
                    return;
                }

                solTitle.textContent=unchecked.length+' stavki traži pažnju';
                solList.innerHTML=unchecked.map(function(title){
                    const s=solutions[title]||['Stavka traži provjeru.','Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `<article class="dcpdim-solution-card">
                        <h4>${title}</h4>
                        <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                        <p><strong>Što napraviti:</strong> ${s[1]}</p>
                    </article>`;
                }).join('');
            }

            document.querySelectorAll('.dcpdim-tabs button').forEach(function(button){
                button.addEventListener('click',function(){
                    document.querySelectorAll('.dcpdim-tabs button').forEach(function(b){b.classList.remove('is-active');});
                    button.classList.add('is-active');
                    renderChecklist(button.getAttribute('data-tab'));
                });
            });

            renderChecklist(activeTab);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpdimv010_assets', 120);
