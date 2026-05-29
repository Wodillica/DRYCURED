<?php
/**
 * Plugin Name: Drycured Process Susenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/susenje/ s poveznicom prema Kalkulatoru sušenja.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpdryv010_enabled(): bool {
    return (bool) get_option('drycured_process_susenje_enabled', 1);
}

function dcpdryv010_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/susenje',
    ], true);
}

function dcpdryv010_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }

    return home_url('/' . trim($path, '/') . '/');
}

function dcpdryv010_tool_url(): string {
    $url = trim((string) get_option('drycured_drying_calculator_url', ''));

    if ($url !== '') {
        return esc_url($url);
    }

    return esc_url(home_url('/alati/'));
}

function dcpdryv010_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/susenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;

    if (file_exists($path)) {
        return content_url($rel);
    }

    return '';
}

function dcpdryv010_render(): string {
    if (!dcpdryv010_enabled()) {
        return '';
    }

    $hero_img = dcpdryv010_image_url('susenje-hero-v01.jpg');
    $tool_url = dcpdryv010_tool_url();

    ob_start();
    ?>
    <main id="dcpdry-susenje" class="dcpdry-wrap" aria-label="Sušenje suhomesnatih proizvoda">

        <section class="dcpdry-hero">
            <div class="dcpdry-hero-copy">
                <span class="dcpdry-eyebrow">Faza 10 — sušenje</span>
                <h1>Sušenje</h1>
                <p class="dcpdry-lead">
                    Sušenje nije samo gubitak vode. To je kontrolirano smanjivanje mase uz očuvanje pravilne površine,
                    mirisa, boje i unutarnje strukture. Ako proizvod prebrzo izgubi vlagu na površini, nastaje kora;
                    ako su uvjeti preslabi, jezgra ostaje vlažna i proces se usporava.
                </p>

                <div class="dcpdry-actions">
                    <a href="#dcpdry-simulator">Otvori simulator sušenja</a>
                    <a href="<?php echo esc_url($tool_url); ?>">Otvori Kalkulator sušenja</a>
                </div>

                <div class="dcpdry-mini">
                    <div><span>cilj</span><strong>ravnomjeran gubitak mase</strong></div>
                    <div><span>rizik</span><strong>kora i vlažna jezgra</strong></div>
                    <div><span>alat</span><strong>Kalkulator sušenja</strong></div>
                </div>
            </div>

            <div class="dcpdry-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Sušenje suhomesnatih proizvoda" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpdry-visual-overlay">
                    <span>kontrola vlage</span>
                    <h2>Dobar proizvod se ne suši na brzinu — on pravilno otpušta vlagu.</h2>
                </div>

                <div class="dcpdry-hero-points">
                    <div><span>vlaga</span><strong>postupno</strong></div>
                    <div><span>zrak</span><strong>mirno kruži</strong></div>
                    <div><span>masa</span><strong>mjeri se</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpdry-simulator" class="dcpdry-simulator">
            <div class="dcpdry-head">
                <span class="dcpdry-eyebrow">Edukativna procjena</span>
                <h2>Simulator rizika sušenja</h2>
                <p>
                    Ovaj simulator objašnjava kako temperatura, relativna vlaga, strujanje zraka, promjer proizvoda
                    i gubitak mase utječu na koru, vlažnu jezgru i zastoj sušenja. Za praktičan izračun koristi
                    Kalkulator sušenja.
                </p>
            </div>

            <div class="dcpdry-sim-shell">
                <div class="dcpdry-controls">
                    <h3>Postavi uvjete sušenja</h3>

                    <div class="dcpdry-control">
                        <label>Temperatura prostora <b id="dcpdry-temp-val">14 °C</b></label>
                        <input id="dcpdry-temp" type="range" min="6" max="24" value="14" step="1">
                    </div>

                    <div class="dcpdry-control">
                        <label>Relativna vlaga <b id="dcpdry-rh-val">78 %</b></label>
                        <input id="dcpdry-rh" type="range" min="50" max="92" value="78" step="1">
                    </div>

                    <div class="dcpdry-control">
                        <label>Strujanje zraka <b id="dcpdry-air-val">umjereno</b></label>
                        <input id="dcpdry-air" type="range" min="1" max="5" value="3" step="1">
                    </div>

                    <div class="dcpdry-control">
                        <label>Promjer proizvoda <b id="dcpdry-dia-val">45 mm</b></label>
                        <input id="dcpdry-dia" type="range" min="25" max="90" value="45" step="5">
                    </div>

                    <div class="dcpdry-control">
                        <label>Dosadašnji gubitak mase <b id="dcpdry-loss-val">18 %</b></label>
                        <input id="dcpdry-loss" type="range" min="0" max="45" value="18" step="1">
                    </div>

                    <div class="dcpdry-note">
                        Kalkulator sušenja koristi se za stvarno praćenje mase. Ovdje korisnik uči zašto ista vlaga,
                        temperatura i zrak ne znače isto za tanku kobasicu i debelu salamu.
                    </div>
                </div>

                <div class="dcpdry-output">
                    <div class="dcpdry-status">
                        <span>procjena</span>
                        <h3 id="dcpdry-status-title">Uvjeti sušenja su uravnoteženi</h3>
                        <p id="dcpdry-status-text">
                            Površina i jezgra imaju dobar odnos. Nastavi mjeriti masu i pratiti miris, boju i tvrdoću.
                        </p>
                    </div>

                    <div class="dcpdry-risk-bars">
                        <div class="dcpdry-risk">
                            <label>Rizik površinske kore <span id="dcpdry-case-num">0/100</span></label>
                            <i><b id="dcpdry-case"></b></i>
                        </div>

                        <div class="dcpdry-risk">
                            <label>Rizik vlažne jezgre <span id="dcpdry-core-num">0/100</span></label>
                            <i><b id="dcpdry-core"></b></i>
                        </div>

                        <div class="dcpdry-risk">
                            <label>Rizik zastoja sušenja <span id="dcpdry-stall-num">0/100</span></label>
                            <i><b id="dcpdry-stall"></b></i>
                        </div>

                        <div class="dcpdry-risk">
                            <label>Rizik presušivanja <span id="dcpdry-over-num">0/100</span></label>
                            <i><b id="dcpdry-over"></b></i>
                        </div>
                    </div>

                    <div class="dcpdry-advice" id="dcpdry-advice">
                        Mjeri masu u pravilnim razmacima i ne ubrzavaj proces samo zato što izvana izgleda suho.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpdry-tool-bridge">
            <div>
                <span class="dcpdry-eyebrow">Glavni alat</span>
                <h2>Za stvarno praćenje koristi Kalkulator sušenja</h2>
                <p>
                    Ova stranica objašnjava logiku sušenja i najčešće greške. Kalkulator sušenja služi za praktično
                    praćenje gubitka mase, procjenu tempa sušenja i odluku kada proizvod može prijeći prema zrenju
                    ili završnom skladištenju.
                </p>
            </div>

            <div class="dcpdry-tool-actions">
                <a href="<?php echo esc_url($tool_url); ?>">Otvori Kalkulator sušenja</a>
                <small>Sušenje se ne procjenjuje samo okom. Masa, miris, tvrdoća i presjek moraju govoriti istu priču.</small>
            </div>
        </section>

        <section class="dcpdry-principles">
            <div class="dcpdry-head">
                <span class="dcpdry-eyebrow">Što se događa</span>
                <h2>Voda mora izlaziti iz proizvoda, ali ne smije pobjeći samo s površine</h2>
            </div>

            <div class="dcpdry-card-grid">
                <article>
                    <b>01</b>
                    <h3>Površina</h3>
                    <p>Površina mora otpuštati vlagu, ali ne smije se prebrzo zatvoriti i stvoriti tvrdu koru.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Jezgra</h3>
                    <p>Unutrašnjost proizvoda mora pratiti površinu. Vlažna jezgra kasnije daje loš presjek i miris.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Zrak</h3>
                    <p>Zrak mora kružiti mirno. Prejak zrak suši površinu, preslab zrak usporava cijeli proces.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Masa</h3>
                    <p>Gubitak mase je najpraktičniji znak tijeka sušenja, ali uvijek se tumači zajedno s izgledom i mirisom.</p>
                </article>
            </div>
        </section>

        <section id="dcpdry-checklist" class="dcpdry-checklist">
            <div class="dcpdry-head">
                <span class="dcpdry-eyebrow">Kontrolna lista</span>
                <h2>Sušenje se vodi mjerenjem, a ne nagađanjem</h2>
                <p>
                    Kontrolna lista vodi kroz početak, tijek i završetak sušenja. Ako stavka nije u redu,
                    korisnik odmah dobiva konkretno rješenje.
                </p>
            </div>

            <div class="dcpdry-check-shell">
                <div class="dcpdry-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije sušenja</button>
                    <button type="button" data-tab="during">Tijekom sušenja</button>
                    <button type="button" data-tab="after">Završna procjena</button>
                </div>

                <div class="dcpdry-check-panel">
                    <div class="dcpdry-progress">
                        <div>
                            <strong id="dcpdry-check-title">Prije sušenja</strong>
                            <span id="dcpdry-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpdry-check-bar"></b></i>
                    </div>

                    <div id="dcpdry-check-list" class="dcpdry-check-list"></div>

                    <div class="dcpdry-solutions">
                        <div class="dcpdry-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpdry-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpdry-solutions-list" class="dcpdry-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpdry-problems">
            <div class="dcpdry-head">
                <span class="dcpdry-eyebrow">Problem → rješenje</span>
                <h2>Greške u sušenju koje se najčešće otkriju prekasno</h2>
            </div>

            <div class="dcpdry-problem-grid">
                <article>
                    <h3>Tvrda površinska kora</h3>
                    <p><strong>Uzrok:</strong> prejak zrak, preniska vlaga ili previsoka temperatura na početku sušenja.</p>
                    <p><strong>Rješenje:</strong> smanjiti strujanje zraka, stabilizirati vlagu i ne forsirati brzo sušenje.</p>
                </article>

                <article>
                    <h3>Vlažna jezgra</h3>
                    <p><strong>Uzrok:</strong> površina se prerano zatvorila ili je proizvod prevelik za zadane uvjete.</p>
                    <p><strong>Rješenje:</strong> usporiti površinsko sušenje i pratiti masu kroz dulji, mirniji režim.</p>
                </article>

                <article>
                    <h3>Zastoj sušenja</h3>
                    <p><strong>Uzrok:</strong> previsoka vlaga, preslaba cirkulacija ili prenatrpan prostor.</p>
                    <p><strong>Rješenje:</strong> razmaknuti proizvode, provjeriti izmjenu zraka i voditi dnevnik mase.</p>
                </article>

                <article>
                    <h3>Presušen proizvod</h3>
                    <p><strong>Uzrok:</strong> predugo sušenje, previsoka temperatura ili zaboravljen gubitak mase.</p>
                    <p><strong>Rješenje:</strong> koristiti kalkulator, zapisivati mjerenja i zaustaviti proces prije pretjeranog gubitka mase.</p>
                </article>
            </div>
        </section>

        <section class="dcpdry-next">
            <div>
                <span class="dcpdry-eyebrow">Sljedeća faza</span>
                <h2>Nakon sušenja dolazi zrenje ili završno skladištenje</h2>
                <p>
                    Kada proizvod izgubi ciljanu masu i ima ujednačen presjek, sušenje prelazi u zrenje ili završnu stabilizaciju.
                    Nije cilj samo osušiti proizvod, nego dobiti siguran, aromatičan i uravnotežen rezultat.
                </p>
            </div>

            <div class="dcpdry-next-actions">
                <a href="<?php echo esc_url(dcpdryv010_page_url('proces-izrade/zrenje')); ?>">Otvori fazu Zrenje</a>
                <a href="<?php echo esc_url(dcpdryv010_page_url('proces-izrade/dimljenje')); ?>">Vrati se na Dimljenje</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_susenje', 'dcpdryv010_render');

function dcpdryv010_append_to_page($content) {
    static $added = false;

    if ($added || !dcpdryv010_enabled()) {
        return $content;
    }

    if (!dcpdryv010_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpdry-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_susenje]');
}
add_filter('the_content', 'dcpdryv010_append_to_page', 35);

function dcpdryv010_assets() {
    if (!dcpdryv010_is_page() || !dcpdryv010_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpdry-wrap{--ink:#101722;--muted:#59636f;--gold:#b68a3a;--gold2:#f1d889;max-width:1220px;margin:46px auto 90px;padding:0 22px;color:var(--ink)}
        .dcpdry-wrap *{box-sizing:border-box}
        .dcpdry-eyebrow{display:inline-flex;width:max-content;min-height:30px;align-items:center;padding:7px 12px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpdry-wrap h1{margin:18px 0 16px;color:var(--ink);font-size:clamp(48px,6.2vw,82px);line-height:.96;letter-spacing:-.06em;word-break:keep-all;hyphens:none}
        .dcpdry-wrap h2{margin:14px 0 12px;color:var(--ink);font-size:clamp(30px,4.2vw,54px);line-height:1.03;letter-spacing:-.045em}
        .dcpdry-wrap h3{margin:0 0 10px;color:var(--ink);font-size:21px;line-height:1.16}
        .dcpdry-wrap p{color:var(--muted);font-size:16px;line-height:1.7}
        .dcpdry-hero,.dcpdry-simulator,.dcpdry-tool-bridge,.dcpdry-principles,.dcpdry-checklist,.dcpdry-problems,.dcpdry-next{border-radius:34px;background:rgba(255,255,255,.70);border:1px solid rgba(16,23,34,.08);box-shadow:0 24px 60px rgba(16,23,34,.08)}
        .dcpdry-hero{display:grid;grid-template-columns:minmax(430px,.92fr) minmax(520px,1.08fr);gap:28px;align-items:stretch;margin-bottom:34px}
        .dcpdry-hero-copy{padding:clamp(32px,4.5vw,58px);border-radius:34px;background:radial-gradient(circle at 10% 10%,rgba(241,216,137,.28),transparent 32%),linear-gradient(135deg,rgba(255,255,255,.86),rgba(255,255,255,.54))}
        .dcpdry-lead{margin:0;color:#2f3943!important;font-size:clamp(18px,1.75vw,21px)!important;line-height:1.58!important}
        .dcpdry-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}
        .dcpdry-actions a,.dcpdry-next-actions a,.dcpdry-tool-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border-radius:999px;background:#101722;color:#fff!important;text-decoration:none!important;font-weight:900;box-shadow:0 16px 34px rgba(16,23,34,.14)}
        .dcpdry-actions a:nth-child(2),.dcpdry-tool-actions a{background:#f1d889;color:#101722!important}
        .dcpdry-next-actions a:nth-child(2){background:#fff;color:#101722!important;border:1px solid rgba(16,23,34,.10)}
        .dcpdry-mini,.dcpdry-hero-points,.dcpdry-card-grid,.dcpdry-problem-grid{display:grid;gap:16px}
        .dcpdry-mini{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:30px}
        .dcpdry-mini div,.dcpdry-hero-points div{padding:16px;border-radius:20px;background:rgba(255,255,255,.72);border:1px solid rgba(16,23,34,.08)}
        .dcpdry-mini span,.dcpdry-hero-points span{display:block;margin-bottom:4px;color:#76551e;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}
        .dcpdry-mini strong,.dcpdry-hero-points strong{display:block;color:var(--ink);font-size:17px;line-height:1.2}
        .dcpdry-hero-visual{position:relative;min-height:560px;overflow:hidden;border-radius:34px;background:radial-gradient(circle at 35% 20%,rgba(241,216,137,.24),transparent 30%),radial-gradient(circle at 75% 75%,rgba(120,211,255,.14),transparent 32%),#101722;box-shadow:0 30px 70px rgba(16,23,34,.24)}
        .dcpdry-hero-visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .dcpdry-hero-visual::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(16,23,34,.05),rgba(16,23,34,.58)),radial-gradient(circle at top right,rgba(241,216,137,.18),transparent 38%);pointer-events:none}
        .dcpdry-visual-overlay{position:absolute;z-index:2;left:28px;right:28px;top:28px;max-width:560px;color:#fff}
        .dcpdry-visual-overlay span{display:inline-flex;margin-bottom:14px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.14);color:#f1d889;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;backdrop-filter:blur(10px)}
        .dcpdry-visual-overlay h2{margin:0;color:#fff;font-size:clamp(26px,2.8vw,40px);line-height:1.06;letter-spacing:-.035em}
        .dcpdry-hero-points{position:absolute;z-index:2;left:24px;right:24px;bottom:24px;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
        .dcpdry-simulator,.dcpdry-tool-bridge,.dcpdry-principles,.dcpdry-checklist,.dcpdry-problems,.dcpdry-next{margin-top:34px;padding:clamp(28px,4vw,46px)}
        .dcpdry-head{max-width:900px;margin-bottom:24px}
        .dcpdry-sim-shell,.dcpdry-check-shell{display:grid;grid-template-columns:330px minmax(0,1fr);gap:20px}
        .dcpdry-controls,.dcpdry-output,.dcpdry-check-panel{border-radius:28px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06);padding:24px}
        .dcpdry-output{background:radial-gradient(circle at top right,rgba(241,216,137,.14),transparent 32%),#101722;color:#fff}
        .dcpdry-control{margin-bottom:18px}
        .dcpdry-control label{display:flex;justify-content:space-between;gap:12px;margin-bottom:7px;color:var(--ink);font-size:14px;font-weight:900}
        .dcpdry-control label b{color:#76551e;font-variant-numeric:tabular-nums}
        .dcpdry-control input[type="range"]{width:100%;accent-color:#b68a3a}
        .dcpdry-note{padding:14px;border-radius:18px;background:#fffaf0;border:1px solid rgba(182,138,58,.18);color:#59636f;font-size:13px;line-height:1.5}
        .dcpdry-status{padding:18px;border-radius:22px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13)}
        .dcpdry-status span{display:inline-flex;margin-bottom:10px;color:#f1d889;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpdry-status h3,.dcpdry-status p{color:#fff}.dcpdry-status p{opacity:.75}
        .dcpdry-risk-bars{display:grid;gap:12px;margin-top:18px}
        .dcpdry-risk label{display:flex;justify-content:space-between;gap:12px;margin-bottom:6px;color:rgba(255,255,255,.75);font-size:12px;font-weight:900;text-transform:uppercase}
        .dcpdry-risk i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden}
        .dcpdry-risk b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff);transition:width .2s ease}
        .dcpdry-risk.is-warning b{background:linear-gradient(90deg,#f1d889,#ff9a76)}
        .dcpdry-advice{margin-top:18px;padding:16px;border-radius:20px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.78);font-size:14px;line-height:1.6}
        .dcpdry-tool-bridge,.dcpdry-next{display:grid;grid-template-columns:minmax(0,1fr) 310px;gap:22px;align-items:center;background:radial-gradient(circle at top right,rgba(120,211,255,.12),transparent 34%),#101722;color:#fff}
        .dcpdry-tool-bridge h2,.dcpdry-tool-bridge p,.dcpdry-next h2,.dcpdry-next p{color:#fff}
        .dcpdry-tool-bridge p,.dcpdry-next p{opacity:.78}
        .dcpdry-tool-actions,.dcpdry-next-actions{display:grid;gap:12px}
        .dcpdry-tool-actions small{color:rgba(255,255,255,.68);font-size:12px;line-height:1.45}
        .dcpdry-card-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
        .dcpdry-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .dcpdry-card-grid article,.dcpdry-problem-grid article{padding:22px;border-radius:24px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06)}
        .dcpdry-card-grid b{display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:30px;margin-bottom:14px;border-radius:999px;background:#101722;color:#fff;font-size:12px;font-weight:900}
        .dcpdry-tabs{display:grid;gap:10px;align-content:start}
        .dcpdry-tabs button{width:100%;text-align:left;border:1px solid rgba(16,23,34,.10);border-radius:18px;padding:15px 16px;background:#fff;color:var(--ink);font-weight:950;cursor:pointer}
        .dcpdry-tabs button.is-active{background:#101722;color:#fff}
        .dcpdry-progress{margin-bottom:20px;padding:18px;border-radius:22px;background:#101722;color:#fff}
        .dcpdry-progress>div{display:flex;justify-content:space-between;gap:14px;align-items:baseline;margin-bottom:13px}
        .dcpdry-progress strong{color:#fff;font-size:19px}
        .dcpdry-progress span{color:rgba(255,255,255,.72);font-size:12px;font-weight:900;white-space:nowrap}
        .dcpdry-progress i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.14);overflow:hidden}
        .dcpdry-progress b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff)}
        .dcpdry-check-list{display:grid;gap:10px}
        .dcpdry-check-item{display:grid;grid-template-columns:28px minmax(0,1fr);gap:12px;align-items:start;padding:14px;border-radius:18px;background:rgba(16,23,34,.035);border:1px solid rgba(16,23,34,.07);cursor:pointer}
        .dcpdry-check-item input{width:20px;height:20px;margin-top:2px;accent-color:#b68a3a}
        .dcpdry-check-item strong{display:block;margin-bottom:3px;color:var(--ink);font-size:15.5px;line-height:1.25}
        .dcpdry-check-item span span{display:block;color:var(--muted);font-size:13.5px;line-height:1.48}
        .dcpdry-check-item.is-checked{background:rgba(182,138,58,.12);border-color:rgba(182,138,58,.22)}
        .dcpdry-solutions{margin-top:18px;padding:18px;border-radius:22px;background:#fffaf0;border:1px solid rgba(182,138,58,.20)}
        .dcpdry-solutions-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:14px}
        .dcpdry-solutions-head span{display:inline-flex;width:max-content;min-height:28px;align-items:center;padding:6px 10px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:11px;font-weight:950;letter-spacing:.10em;text-transform:uppercase}
        .dcpdry-solutions-head strong{color:#101722;font-size:15px;text-align:right}
        .dcpdry-solutions-list{display:grid;gap:10px}
        .dcpdry-solution-card{padding:15px;border-radius:18px;background:#fff;border:1px solid rgba(16,23,34,.08)}
        .dcpdry-solution-card h4{margin:0 0 7px;color:var(--ink);font-size:15.5px}
        .dcpdry-solution-card p{margin:0;color:var(--muted);font-size:13.5px;line-height:1.55}
        .dcpdry-solution-card p+p{margin-top:8px}.dcpdry-solution-card strong{color:#76551e}
        @media(max-width:1100px){.dcpdry-hero,.dcpdry-sim-shell,.dcpdry-check-shell,.dcpdry-tool-bridge,.dcpdry-next{grid-template-columns:1fr}.dcpdry-card-grid,.dcpdry-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dcpdry-wrap h1{font-size:clamp(44px,9vw,68px)}}
        @media(max-width:680px){.dcpdry-wrap{padding:0 14px;margin-top:32px}.dcpdry-hero,.dcpdry-hero-copy,.dcpdry-hero-visual,.dcpdry-simulator,.dcpdry-tool-bridge,.dcpdry-principles,.dcpdry-checklist,.dcpdry-problems,.dcpdry-next{border-radius:24px}.dcpdry-mini,.dcpdry-hero-points,.dcpdry-card-grid,.dcpdry-problem-grid{grid-template-columns:1fr}.dcpdry-hero-visual{min-height:540px}.dcpdry-progress>div,.dcpdry-solutions-head{flex-direction:column;gap:6px}.dcpdry-solutions-head strong{text-align:left}}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpdry-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpdry-wrap')) child.style.display = 'none';
                    });
                }
            }

            function clamp(v,min,max){return Math.max(min,Math.min(max,v));}
            function n(id){return parseFloat(document.getElementById(id).value);}
            function labelAir(v){return v===1?'slabo':v===2?'blago':v===3?'umjereno':v===4?'jako':'prejako';}
            function setBar(id,numId,value,warning){
                const val=clamp(Math.round(value),0,100);
                const el=document.getElementById(id);
                el.style.width=val+'%';
                document.getElementById(numId).textContent=val+'/100';
                el.closest('.dcpdry-risk').classList.toggle('is-warning',warning&&val>60);
            }

            function updateSimulator(){
                const temp=n('dcpdry-temp'), rh=n('dcpdry-rh'), air=n('dcpdry-air'), dia=n('dcpdry-dia'), loss=n('dcpdry-loss');

                document.getElementById('dcpdry-temp-val').textContent=temp.toFixed(0)+' °C';
                document.getElementById('dcpdry-rh-val').textContent=rh.toFixed(0)+' %';
                document.getElementById('dcpdry-air-val').textContent=labelAir(air);
                document.getElementById('dcpdry-dia-val').textContent=dia.toFixed(0)+' mm';
                document.getElementById('dcpdry-loss-val').textContent=loss.toFixed(0)+' %';

                let caseRisk=10+Math.max(0,temp-16)*5+Math.max(0,70-rh)*2.1+Math.max(0,air-3)*18+Math.max(0,loss-28)*2;
                let core=12+Math.max(0,dia-50)*1.2+Math.max(0,loss-18)*1.2+Math.max(0,70-rh)*1.1;
                let stall=10+Math.max(0,rh-84)*3+Math.max(0,2-air)*20+Math.max(0,12-temp)*3;
                let over=8+Math.max(0,loss-35)*4+Math.max(0,temp-18)*4+Math.max(0,65-rh)*1.2;

                caseRisk=clamp(caseRisk,0,100);core=clamp(core,0,100);stall=clamp(stall,0,100);over=clamp(over,0,100);

                setBar('dcpdry-case','dcpdry-case-num',caseRisk,true);
                setBar('dcpdry-core','dcpdry-core-num',core,true);
                setBar('dcpdry-stall','dcpdry-stall-num',stall,true);
                setBar('dcpdry-over','dcpdry-over-num',over,true);

                let title='Uvjeti sušenja su uravnoteženi';
                let text='Površina i jezgra imaju dobar odnos. Nastavi mjeriti masu i pratiti miris, boju i tvrdoću.';
                let advice='Mjeri masu u pravilnim razmacima i ne ubrzavaj proces samo zato što izvana izgleda suho.';

                if(caseRisk>65){
                    title='Rizik površinske kore';
                    text='Površina se može prebrzo zatvoriti, dok unutrašnjost ostaje vlažna.';
                    advice='Smanji strujanje zraka, povisi relativnu vlagu u sigurnom okviru i uspori početno sušenje.';
                } else if(core>65){
                    title='Rizik vlažne jezgre';
                    text='Promjer i tempo gubitka mase upućuju na mogućnost da jezgra kasni za površinom.';
                    advice='Produži mirno sušenje i ne zaključuj proces samo po tvrdoj površini.';
                } else if(stall>65){
                    title='Rizik zastoja sušenja';
                    text='Previsoka vlaga ili preslaba cirkulacija mogu zaustaviti otpuštanje vlage.';
                    advice='Provjeri razmak proizvoda, izmjenu zraka i počni redovito mjeriti masu.';
                } else if(over>65){
                    title='Rizik presušivanja';
                    text='Gubitak mase ili uvjeti upućuju da proizvod može postati pretvrd i suh.';
                    advice='Provjeri ciljanu masu u Kalkulatoru sušenja i razmotri prelazak na zrenje ili skladištenje.';
                }

                document.getElementById('dcpdry-status-title').textContent=title;
                document.getElementById('dcpdry-status-text').textContent=text;
                document.getElementById('dcpdry-advice').textContent=advice;
            }

            ['dcpdry-temp','dcpdry-rh','dcpdry-air','dcpdry-dia','dcpdry-loss'].forEach(function(id){
                const el=document.getElementById(id);
                if(el) el.addEventListener('input',updateSimulator);
            });

            updateSimulator();

            const checklistData={
                before:{title:'Prije sušenja',items:[
                    ['Proizvodi imaju razmak','Zrak mora kružiti oko svakog komada.'],
                    ['Početna masa je zapisana','Bez početne mase nema kontrole gubitka.'],
                    ['Temperatura je stabilna','Prostor ne smije imati nagle skokove.'],
                    ['Vlaga je u razumnom okviru','Presuho stvara koru, prevlažno usporava sušenje.'],
                    ['Površina nije mokra','Mokra površina otežava pravilno sušenje.'],
                    ['Kalkulator sušenja je spreman','Mjerenja se upisuju redovito, ne po sjećanju.']
                ]},
                during:{title:'Tijekom sušenja',items:[
                    ['Masa se redovito mjeri','Tempo gubitka mase mora biti poznat.'],
                    ['Površina nije pretvrda','Tvrda površina uz mekanu jezgru je znak problema.'],
                    ['Miris je čist','Kiseli ili ustajali miris traži provjeru uvjeta.'],
                    ['Nema kondenzacije','Kondenzacija znači problem vlage i cirkulacije.'],
                    ['Zrak nije prejak','Prejak zrak brzo suši površinu.'],
                    ['Proizvodi se provjeravaju pojedinačno','Deblji i tanji komadi ne suše se istim tempom.']
                ]},
                after:{title:'Završna procjena',items:[
                    ['Ciljani gubitak mase je postignut','Masa je uspoređena s ciljem.'],
                    ['Presjek je ujednačen','Nema tvrde kore i vlažne jezgre.'],
                    ['Miris je ugodan','Nema kiselog, ustajalog ili nečistog mirisa.'],
                    ['Tekstura odgovara proizvodu','Proizvod nije ni premekan ni pretvrd.'],
                    ['Bilješke su dopunjene','Upisani su uvjeti, trajanje i rezultat.'],
                    ['Sljedeća faza je određena','Proizvod ide na zrenje ili završno skladištenje.']
                ]}
            };

            const solutions={
                'Proizvodi imaju razmak':['Bez razmaka se vlaga i zrak ne raspoređuju ravnomjerno.','Razmakni proizvode i provjeri cirkulaciju.'],
                'Početna masa je zapisana':['Bez početne mase ne znaš stvarni gubitak.','Izvagaj svaki komad ili reprezentativnu šaržu i upiši podatak.'],
                'Temperatura je stabilna':['Skokovi temperature mijenjaju brzinu sušenja.','Stabiliziraj prostor prije nastavka.'],
                'Vlaga je u razumnom okviru':['Presuho zatvara površinu, prevlažno usporava proces.','Prilagodi ventilaciju i kontrolu vlage.'],
                'Površina nije mokra':['Mokra površina otežava ravnomjerno sušenje.','Pričekaj da se površina smiri prije jačeg protoka zraka.'],
                'Kalkulator sušenja je spreman':['Bez mjerenja sušenje postaje pogađanje.','Otvori Kalkulator sušenja i vodi zapise.'],
                'Masa se redovito mjeri':['Bez ritma mjerenja promaši se pravi trenutak.','Postavi stalni raspored vaganja.'],
                'Površina nije pretvrda':['Tvrda kora skriva vlažnu jezgru.','Smanji zrak i uspori sušenje.'],
                'Miris je čist':['Neugodan miris može biti znak zastoja ili previsoke vlage.','Provjeri vlagu, cirkulaciju i razmak proizvoda.'],
                'Nema kondenzacije':['Kondenzacija znači da vlaga ne izlazi pravilno.','Poboljšaj izmjenu zraka i stabiliziraj temperaturu.'],
                'Zrak nije prejak':['Prejak zrak napada površinu.','Smanji ventilaciju ili udalji proizvod od direktnog strujanja.'],
                'Proizvodi se provjeravaju pojedinačno':['Različiti promjeri suše se različito.','Vodi odvojene bilješke za deblje i tanje proizvode.'],
                'Ciljani gubitak mase je postignut':['Bez cilja lako presušiš ili prerano zaustaviš proces.','Provjeri cilj u Kalkulatoru sušenja.'],
                'Presjek je ujednačen':['Neujednačen presjek znači da proces nije završen kako treba.','Procijeni treba li mirnije zrenje ili dulje sušenje.'],
                'Miris je ugodan':['Miris je važan sigurnosni signal.','Ako je miris sumnjiv, proizvod odvoji i provjeri uzrok.'],
                'Tekstura odgovara proizvodu':['Pretvrda ili premekana tekstura pokazuje promašen tempo.','Usporedi masu, promjer i uvjete sušenja.'],
                'Bilješke su dopunjene':['Bez bilješki nema ponavljanja dobrog rezultata.','Upiši uvjete, trajanje, masu i opažanja.'],
                'Sljedeća faza je određena':['Nakon sušenja ne smije biti lutanja.','Odredi ide li proizvod na zrenje ili skladištenje.']
            };

            let activeTab='before';

            function renderChecklist(tab){
                activeTab=tab;
                const cfg=checklistData[tab];
                const list=document.getElementById('dcpdry-check-list');
                document.getElementById('dcpdry-check-title').textContent=cfg.title;

                list.innerHTML=cfg.items.map(function(item,index){
                    const k='drycured_susenje_check_'+tab+'_'+index;
                    const checked=localStorage.getItem(k)==='1';
                    return `<label class="dcpdry-check-item ${checked?'is-checked':''}">
                        <input type="checkbox" data-index="${index}" ${checked?'checked':''}>
                        <span><strong>${item[0]}</strong><span>${item[1]}</span></span>
                    </label>`;
                }).join('');

                list.querySelectorAll('input[type="checkbox"]').forEach(function(box){
                    box.addEventListener('change',function(){
                        const k='drycured_susenje_check_'+activeTab+'_'+box.getAttribute('data-index');
                        localStorage.setItem(k,box.checked?'1':'0');
                        box.closest('.dcpdry-check-item').classList.toggle('is-checked',box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist(){
                const boxes=Array.from(document.querySelectorAll('.dcpdry-check-item input'));
                const checked=boxes.filter(function(b){return b.checked;}).length;
                const total=boxes.length||1;
                const pct=Math.round((checked/total)*100);

                document.getElementById('dcpdry-check-count').textContent=checked+'/'+total+' označeno';
                document.getElementById('dcpdry-check-bar').style.width=pct+'%';

                const unchecked=boxes.map(function(box){
                    if(box.checked) return null;
                    return box.closest('.dcpdry-check-item').querySelector('strong').textContent.trim();
                }).filter(Boolean);

                const solTitle=document.getElementById('dcpdry-solutions-title');
                const solList=document.getElementById('dcpdry-solutions-list');

                if(!unchecked.length){
                    solTitle.textContent='Sve stavke su označene';
                    solList.innerHTML='<div class="dcpdry-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi s mjerenjem mase i kontrolom uvjeta.</p></div>';
                    return;
                }

                solTitle.textContent=unchecked.length+' stavki traži pažnju';
                solList.innerHTML=unchecked.map(function(title){
                    const s=solutions[title]||['Stavka traži provjeru.','Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `<article class="dcpdry-solution-card">
                        <h4>${title}</h4>
                        <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                        <p><strong>Što napraviti:</strong> ${s[1]}</p>
                    </article>`;
                }).join('');
            }

            document.querySelectorAll('.dcpdry-tabs button').forEach(function(button){
                button.addEventListener('click',function(){
                    document.querySelectorAll('.dcpdry-tabs button').forEach(function(b){b.classList.remove('is-active');});
                    button.classList.add('is-active');
                    renderChecklist(button.getAttribute('data-tab'));
                });
            });

            renderChecklist(activeTab);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpdryv010_assets', 120);
