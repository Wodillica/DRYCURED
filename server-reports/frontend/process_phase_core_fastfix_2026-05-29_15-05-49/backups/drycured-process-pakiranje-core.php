<?php
/**
 * Plugin Name: Drycured Process Pakiranje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/pakiranje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcppackv010_enabled(): bool {
    return (bool) get_option('drycured_process_pakiranje_enabled', 1);
}

function dcppackv010_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    return in_array($path, [
        'proces-izrade/pakiranje',
        'proces-izrade/pakiranje-i-cuvanje',
        'proces-izrade/pakiranje-i-čuvanje',
        'proces-izrade/pakiranje-cuvanje',
        'proces-izrade/pakiranje-čuvanje',
    ], true);
}

function dcppackv010_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcppackv010_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/pakiranje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;

    if (file_exists($path)) {
        return content_url($rel);
    }

    return '';
}

function dcppackv010_render(): string {
    if (!dcppackv010_enabled()) {
        return '';
    }

    $hero_img = dcppackv010_image_url('pakiranje-hero-v01.jpg');

    ob_start();
    ?>
    <main id="dcppack-pakiranje" class="dcppack-wrap" aria-label="Pakiranje i čuvanje suhomesnatih proizvoda">

        <section class="dcppack-hero">
            <div class="dcppack-hero-copy">
                <span class="dcppack-eyebrow">Faza 12 — pakiranje</span>
                <h1>Pakiranje</h1>
                <p class="dcppack-lead">
                    Pakiranje ne smije sakriti problem; ono mora sačuvati dobar proizvod. Nakon sušenja i zrenja cilj je
                    zaštititi aromu, usporiti neželjeno isušivanje, spriječiti kontaminaciju i omogućiti sigurno čuvanje.
                    Krivo pakiranje može pokvariti ono što su tjedni rada već izgradili.
                </p>

                <div class="dcppack-actions">
                    <a href="#dcppack-simulator">Otvori simulator pakiranja</a>
                    <a href="<?php echo esc_url(dcppackv010_page_url('proces-izrade/zrenje')); ?>">Vrati se na Zrenje</a>
                </div>

                <div class="dcppack-mini">
                    <div><span>cilj</span><strong>očuvati kvalitetu</strong></div>
                    <div><span>rizik</span><strong>vlaga, plijesan, oksidacija</strong></div>
                    <div><span>kontrola</span><strong>čistoća + temperatura</strong></div>
                </div>
            </div>

            <div class="dcppack-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Pakiranje suhomesnatih proizvoda" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcppack-visual-overlay">
                    <span>završna zaštita</span>
                    <h2>Dobro pakiranje čuva proizvod; loše pakiranje zatvara problem u vrećicu.</h2>
                </div>

                <div class="dcppack-hero-points">
                    <div><span>površina</span><strong>suha i čista</strong></div>
                    <div><span>ambalaža</span><strong>prikladna</strong></div>
                    <div><span>čuvanje</span><strong>kontrolirano</strong></div>
                </div>
            </div>
        </section>

        <section id="dcppack-simulator" class="dcppack-simulator">
            <div class="dcppack-head">
                <span class="dcppack-eyebrow">Edukativna procjena</span>
                <h2>Simulator rizika pakiranja</h2>
                <p>
                    Pakiranje ovisi o stanju proizvoda, vlazi površine, tipu ambalaže, temperaturi čuvanja i trajanju skladištenja.
                    Isti proizvod ne ponaša se jednako u papiru, vakuumu, posudi ili slobodnom visećem čuvanju.
                </p>
            </div>

            <div class="dcppack-sim-shell">
                <div class="dcppack-controls">
                    <h3>Postavi uvjete pakiranja</h3>

                    <div class="dcppack-control">
                        <label>Suhoća površine <b id="dcppack-surface-val">dobra</b></label>
                        <input id="dcppack-surface" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcppack-control">
                        <label>Čistoća rada <b id="dcppack-clean-val">dobra</b></label>
                        <input id="dcppack-clean" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcppack-control">
                        <label>Tip pakiranja <b id="dcppack-type-val">papir/omot</b></label>
                        <input id="dcppack-type" type="range" min="1" max="4" value="2" step="1">
                    </div>

                    <div class="dcppack-control">
                        <label>Temperatura čuvanja <b id="dcppack-temp-val">8 °C</b></label>
                        <input id="dcppack-temp" type="range" min="0" max="22" value="8" step="1">
                    </div>

                    <div class="dcppack-control">
                        <label>Planirano čuvanje <b id="dcppack-time-val">14 dana</b></label>
                        <input id="dcppack-time" type="range" min="1" max="90" value="14" step="1">
                    </div>

                    <div class="dcppack-note">
                        Vakuum nije čarobna zaštita. Ako je proizvod premokar, nečist ili nestabilan, vakuum samo stvara
                        zatvoren prostor u kojem se problem lakše sakrije — a poslije nas dočeka kao loš vic.
                    </div>
                </div>

                <div class="dcppack-output">
                    <div class="dcppack-status">
                        <span>procjena</span>
                        <h3 id="dcppack-status-title">Uvjeti pakiranja su dobri</h3>
                        <p id="dcppack-status-text">
                            Proizvod je stabilan, površina je uredna i pakiranje može čuvati postignutu kvalitetu.
                        </p>
                    </div>

                    <div class="dcppack-risk-bars">
                        <div class="dcppack-risk">
                            <label>Rizik kondenzacije <span id="dcppack-cond-num">0/100</span></label>
                            <i><b id="dcppack-cond"></b></i>
                        </div>

                        <div class="dcppack-risk">
                            <label>Rizik plijesni <span id="dcppack-mold-num">0/100</span></label>
                            <i><b id="dcppack-mold"></b></i>
                        </div>

                        <div class="dcppack-risk">
                            <label>Rizik oksidacije <span id="dcppack-oxid-num">0/100</span></label>
                            <i><b id="dcppack-oxid"></b></i>
                        </div>

                        <div class="dcppack-risk">
                            <label>Rizik gubitka arome <span id="dcppack-aroma-num">0/100</span></label>
                            <i><b id="dcppack-aroma"></b></i>
                        </div>
                    </div>

                    <div class="dcppack-advice" id="dcppack-advice">
                        Pakiraj samo stabilan proizvod, označi šaržu i čuvaj ga u prikladnim uvjetima.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcppack-principles">
            <div class="dcppack-head">
                <span class="dcppack-eyebrow">Što dobro pakiranje mora napraviti</span>
                <h2>Ambalaža čuva rezultat, ali ne liječi greške</h2>
            </div>

            <div class="dcppack-card-grid">
                <article>
                    <b>01</b>
                    <h3>Zaštita od prljanja</h3>
                    <p>Gotov proizvod mora biti zaštićen od dodira, prašine, nečistih površina i nepotrebnog rukovanja.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Kontrola vlage</h3>
                    <p>Pakiranje ne smije zarobiti vlagu na površini niti proizvod ostaviti da se prebrzo dodatno suši.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Očuvanje arome</h3>
                    <p>Aroma se čuva pravilnim omotom, hladnim čuvanjem i izbjegavanjem dugog izlaganja zraku.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Sljedivost</h3>
                    <p>Svaki proizvod treba imati osnovnu oznaku: naziv, datum, šaržu i bilješku o načinu čuvanja.</p>
                </article>
            </div>
        </section>

        <section class="dcppack-types">
            <div class="dcppack-head">
                <span class="dcppack-eyebrow">Odabir ambalaže</span>
                <h2>Pakiranje se bira prema proizvodu, ne prema navici</h2>
            </div>

            <div class="dcppack-compare-grid">
                <article>
                    <span>Papir / prozračni omot</span>
                    <h3>Dobro za kratko čuvanje</h3>
                    <p>Omogućuje disanje proizvoda, ali ne štiti jako dugo od dodatnog isušivanja i mirisa iz okoline.</p>
                    <strong>Rješenje: koristiti za kraće čuvanje i redovitu potrošnju.</strong>
                </article>

                <article>
                    <span>Vakuum</span>
                    <h3>Dobro samo za stabilan proizvod</h3>
                    <p>Usporava oksidaciju i gubitak arome, ali nije za proizvod s vlažnom površinom ili sumnjivim mirisom.</p>
                    <strong>Rješenje: vakuumirati samo suh, stabilan i uredan proizvod.</strong>
                </article>

                <article>
                    <span>Posuda / hladno čuvanje</span>
                    <h3>Dobro za kontroliranu potrošnju</h3>
                    <p>Praktično za rezane komade, ali traži čistu posudu i kontrolu kondenzacije.</p>
                    <strong>Rješenje: koristiti čistu posudu i ne zatvarati topao ili vlažan proizvod.</strong>
                </article>
            </div>
        </section>

        <section id="dcppack-checklist" class="dcppack-checklist">
            <div class="dcppack-head">
                <span class="dcppack-eyebrow">Kontrolna lista</span>
                <h2>Pakiranje počinje provjerom proizvoda</h2>
                <p>
                    Ako proizvod nije stabilan, pakiranje neće pomoći. Kontrolna lista vodi kroz provjeru prije pakiranja,
                    samo pakiranje i čuvanje nakon pakiranja.
                </p>
            </div>

            <div class="dcppack-check-shell">
                <div class="dcppack-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije pakiranja</button>
                    <button type="button" data-tab="during">Tijekom pakiranja</button>
                    <button type="button" data-tab="after">Nakon pakiranja</button>
                </div>

                <div class="dcppack-check-panel">
                    <div class="dcppack-progress">
                        <div>
                            <strong id="dcppack-check-title">Prije pakiranja</strong>
                            <span id="dcppack-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcppack-check-bar"></b></i>
                    </div>

                    <div id="dcppack-check-list" class="dcppack-check-list"></div>

                    <div class="dcppack-solutions">
                        <div class="dcppack-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcppack-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcppack-solutions-list" class="dcppack-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcppack-problems">
            <div class="dcppack-head">
                <span class="dcppack-eyebrow">Problem → rješenje</span>
                <h2>Greške u pakiranju koje kvare gotov proizvod</h2>
            </div>

            <div class="dcppack-problem-grid">
                <article>
                    <h3>Kondenzacija u pakiranju</h3>
                    <p><strong>Uzrok:</strong> proizvod je pakiran pretopao, površina je bila vlažna ili je došlo do promjene temperature.</p>
                    <p><strong>Rješenje:</strong> proizvod stabilizirati prije pakiranja, pakirati suh i čuvati bez naglih temperaturnih promjena.</p>
                </article>

                <article>
                    <h3>Plijesan nakon pakiranja</h3>
                    <p><strong>Uzrok:</strong> nečista površina, previsoka vlaga ili proizvod koji nije bio spreman za zatvaranje.</p>
                    <p><strong>Rješenje:</strong> odvojiti proizvod, provjeriti miris i površinu te ne pakirati problematične komade zajedno s ispravnima.</p>
                </article>

                <article>
                    <h3>Gubitak mirisa i arome</h3>
                    <p><strong>Uzrok:</strong> predugo izlaganje zraku, loš omot ili čuvanje uz namirnice jakog mirisa.</p>
                    <p><strong>Rješenje:</strong> koristiti prikladan omot, hladno čuvanje i jasno odvojiti proizvod od jakih mirisa.</p>
                </article>

                <article>
                    <h3>Presušivanje nakon pakiranja</h3>
                    <p><strong>Uzrok:</strong> prozračno pakiranje za predugo čuvanje ili presuhi prostor.</p>
                    <p><strong>Rješenje:</strong> odabrati ambalažu prema planiranom trajanju čuvanja i redovito provjeravati rezane komade.</p>
                </article>
            </div>
        </section>

        <section class="dcppack-next">
            <div>
                <span class="dcppack-eyebrow">Završetak procesa</span>
                <h2>Pakiranje zatvara proizvodni ciklus</h2>
                <p>
                    Nakon pakiranja najvažnije je pravilno čuvanje, jasna oznaka šarže i praćenje stanja proizvoda.
                    Dobar završetak nije spektakl — to je urednost. Stara škola, ali radi.
                </p>
            </div>

            <div class="dcppack-next-actions">
                <a href="<?php echo esc_url(dcppackv010_page_url('proces-izrade/zrenje')); ?>">Vrati se na Zrenje</a>
                <a href="<?php echo esc_url(home_url('/proces-izrade/')); ?>">Pogledaj sve procese</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_pakiranje', 'dcppackv010_render');

function dcppackv010_append_to_page($content) {
    static $added = false;

    if ($added || !dcppackv010_enabled()) {
        return $content;
    }

    if (!dcppackv010_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcppack-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_pakiranje]');
}
add_filter('the_content', 'dcppackv010_append_to_page', 35);

function dcppackv010_assets() {
    if (!dcppackv010_is_page() || !dcppackv010_enabled()) {
        return;
    }
    ?>
    <style>
        .dcppack-wrap{--ink:#101722;--muted:#59636f;--gold:#b68a3a;--gold2:#f1d889;max-width:1220px;margin:46px auto 90px;padding:0 22px;color:var(--ink)}
        .dcppack-wrap *{box-sizing:border-box}
        .dcppack-eyebrow{display:inline-flex;width:max-content;min-height:30px;align-items:center;padding:7px 12px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcppack-wrap h1{margin:18px 0 16px;color:var(--ink);font-size:clamp(48px,6.2vw,82px);line-height:.96;letter-spacing:-.06em;word-break:keep-all;hyphens:none}
        .dcppack-wrap h2{margin:14px 0 12px;color:var(--ink);font-size:clamp(30px,4.2vw,54px);line-height:1.03;letter-spacing:-.045em}
        .dcppack-wrap h3{margin:0 0 10px;color:var(--ink);font-size:21px;line-height:1.16}
        .dcppack-wrap p{color:var(--muted);font-size:16px;line-height:1.7}
        .dcppack-hero,.dcppack-simulator,.dcppack-principles,.dcppack-types,.dcppack-checklist,.dcppack-problems,.dcppack-next{border-radius:34px;background:rgba(255,255,255,.70);border:1px solid rgba(16,23,34,.08);box-shadow:0 24px 60px rgba(16,23,34,.08)}
        .dcppack-hero{display:grid;grid-template-columns:minmax(430px,.92fr) minmax(520px,1.08fr);gap:28px;align-items:stretch;margin-bottom:34px}
        .dcppack-hero-copy{padding:clamp(32px,4.5vw,58px);border-radius:34px;background:radial-gradient(circle at 10% 10%,rgba(241,216,137,.28),transparent 32%),linear-gradient(135deg,rgba(255,255,255,.86),rgba(255,255,255,.54))}
        .dcppack-lead{margin:0;color:#2f3943!important;font-size:clamp(18px,1.75vw,21px)!important;line-height:1.58!important}
        .dcppack-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}
        .dcppack-actions a,.dcppack-next-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border-radius:999px;background:#101722;color:#fff!important;text-decoration:none!important;font-weight:900;box-shadow:0 16px 34px rgba(16,23,34,.14)}
        .dcppack-actions a:nth-child(2),.dcppack-next-actions a:nth-child(2){background:#fff;color:#101722!important;border:1px solid rgba(16,23,34,.10)}
        .dcppack-mini,.dcppack-hero-points,.dcppack-card-grid,.dcppack-problem-grid,.dcppack-compare-grid{display:grid;gap:16px}
        .dcppack-mini{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:30px}
        .dcppack-mini div,.dcppack-hero-points div{padding:16px;border-radius:20px;background:rgba(255,255,255,.72);border:1px solid rgba(16,23,34,.08)}
        .dcppack-mini span,.dcppack-hero-points span{display:block;margin-bottom:4px;color:#76551e;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}
        .dcppack-mini strong,.dcppack-hero-points strong{display:block;color:var(--ink);font-size:17px;line-height:1.2}
        .dcppack-hero-visual{position:relative;min-height:560px;overflow:hidden;border-radius:34px;background:radial-gradient(circle at 35% 20%,rgba(241,216,137,.24),transparent 30%),radial-gradient(circle at 75% 75%,rgba(120,211,255,.14),transparent 32%),#101722;box-shadow:0 30px 70px rgba(16,23,34,.24)}
        .dcppack-hero-visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .dcppack-hero-visual::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(16,23,34,.05),rgba(16,23,34,.58)),radial-gradient(circle at top right,rgba(241,216,137,.18),transparent 38%);pointer-events:none}
        .dcppack-visual-overlay{position:absolute;z-index:2;left:28px;right:28px;top:28px;max-width:560px;color:#fff}
        .dcppack-visual-overlay span{display:inline-flex;margin-bottom:14px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.14);color:#f1d889;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;backdrop-filter:blur(10px)}
        .dcppack-visual-overlay h2{margin:0;color:#fff;font-size:clamp(26px,2.8vw,40px);line-height:1.06;letter-spacing:-.035em}
        .dcppack-hero-points{position:absolute;z-index:2;left:24px;right:24px;bottom:24px;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
        .dcppack-simulator,.dcppack-principles,.dcppack-types,.dcppack-checklist,.dcppack-problems,.dcppack-next{margin-top:34px;padding:clamp(28px,4vw,46px)}
        .dcppack-head{max-width:900px;margin-bottom:24px}
        .dcppack-sim-shell,.dcppack-check-shell{display:grid;grid-template-columns:330px minmax(0,1fr);gap:20px}
        .dcppack-controls,.dcppack-output,.dcppack-check-panel{border-radius:28px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06);padding:24px}
        .dcppack-output{background:radial-gradient(circle at top right,rgba(241,216,137,.14),transparent 32%),#101722;color:#fff}
        .dcppack-control{margin-bottom:18px}
        .dcppack-control label{display:flex;justify-content:space-between;gap:12px;margin-bottom:7px;color:var(--ink);font-size:14px;font-weight:900}
        .dcppack-control label b{color:#76551e;font-variant-numeric:tabular-nums}
        .dcppack-control input[type="range"]{width:100%;accent-color:#b68a3a}
        .dcppack-note{padding:14px;border-radius:18px;background:#fffaf0;border:1px solid rgba(182,138,58,.18);color:#59636f;font-size:13px;line-height:1.5}
        .dcppack-status{padding:18px;border-radius:22px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13)}
        .dcppack-status span{display:inline-flex;margin-bottom:10px;color:#f1d889;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcppack-status h3,.dcppack-status p{color:#fff}.dcppack-status p{opacity:.75}
        .dcppack-risk-bars{display:grid;gap:12px;margin-top:18px}
        .dcppack-risk label{display:flex;justify-content:space-between;gap:12px;margin-bottom:6px;color:rgba(255,255,255,.75);font-size:12px;font-weight:900;text-transform:uppercase}
        .dcppack-risk i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden}
        .dcppack-risk b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff);transition:width .2s ease}
        .dcppack-risk.is-warning b{background:linear-gradient(90deg,#f1d889,#ff9a76)}
        .dcppack-advice{margin-top:18px;padding:16px;border-radius:20px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.78);font-size:14px;line-height:1.6}
        .dcppack-card-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
        .dcppack-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .dcppack-compare-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
        .dcppack-card-grid article,.dcppack-problem-grid article,.dcppack-compare-grid article{padding:22px;border-radius:24px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06)}
        .dcppack-card-grid b{display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:30px;margin-bottom:14px;border-radius:999px;background:#101722;color:#fff;font-size:12px;font-weight:900}
        .dcppack-compare-grid article span{display:inline-flex;margin-bottom:12px;padding:7px 10px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:11px;font-weight:950;letter-spacing:.1em;text-transform:uppercase}
        .dcppack-compare-grid strong{display:block;margin-top:12px;color:#101722;font-size:14px;line-height:1.5}
        .dcppack-tabs{display:grid;gap:10px;align-content:start}
        .dcppack-tabs button{width:100%;text-align:left;border:1px solid rgba(16,23,34,.10);border-radius:18px;padding:15px 16px;background:#fff;color:var(--ink);font-weight:950;cursor:pointer}
        .dcppack-tabs button.is-active{background:#101722;color:#fff}
        .dcppack-progress{margin-bottom:20px;padding:18px;border-radius:22px;background:#101722;color:#fff}
        .dcppack-progress>div{display:flex;justify-content:space-between;gap:14px;align-items:baseline;margin-bottom:13px}
        .dcppack-progress strong{color:#fff;font-size:19px}
        .dcppack-progress span{color:rgba(255,255,255,.72);font-size:12px;font-weight:900;white-space:nowrap}
        .dcppack-progress i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.14);overflow:hidden}
        .dcppack-progress b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff)}
        .dcppack-check-list{display:grid;gap:10px}
        .dcppack-check-item{display:grid;grid-template-columns:28px minmax(0,1fr);gap:12px;align-items:start;padding:14px;border-radius:18px;background:rgba(16,23,34,.035);border:1px solid rgba(16,23,34,.07);cursor:pointer}
        .dcppack-check-item input{width:20px;height:20px;margin-top:2px;accent-color:#b68a3a}
        .dcppack-check-item strong{display:block;margin-bottom:3px;color:var(--ink);font-size:15.5px;line-height:1.25}
        .dcppack-check-item span span{display:block;color:var(--muted);font-size:13.5px;line-height:1.48}
        .dcppack-check-item.is-checked{background:rgba(182,138,58,.12);border-color:rgba(182,138,58,.22)}
        .dcppack-solutions{margin-top:18px;padding:18px;border-radius:22px;background:#fffaf0;border:1px solid rgba(182,138,58,.20)}
        .dcppack-solutions-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:14px}
        .dcppack-solutions-head span{display:inline-flex;width:max-content;min-height:28px;align-items:center;padding:6px 10px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:11px;font-weight:950;letter-spacing:.10em;text-transform:uppercase}
        .dcppack-solutions-head strong{color:#101722;font-size:15px;text-align:right}
        .dcppack-solutions-list{display:grid;gap:10px}
        .dcppack-solution-card{padding:15px;border-radius:18px;background:#fff;border:1px solid rgba(16,23,34,.08)}
        .dcppack-solution-card h4{margin:0 0 7px;color:var(--ink);font-size:15.5px}
        .dcppack-solution-card p{margin:0;color:var(--muted);font-size:13.5px;line-height:1.55}
        .dcppack-solution-card p+p{margin-top:8px}.dcppack-solution-card strong{color:#76551e}
        .dcppack-next{display:grid;grid-template-columns:minmax(0,1fr) 290px;gap:22px;align-items:center;background:radial-gradient(circle at top right,rgba(120,211,255,.12),transparent 34%),#101722;color:#fff}
        .dcppack-next h2,.dcppack-next p{color:#fff}.dcppack-next p{opacity:.78}.dcppack-next-actions{display:grid;gap:12px}
        @media(max-width:1100px){.dcppack-hero,.dcppack-sim-shell,.dcppack-check-shell,.dcppack-next{grid-template-columns:1fr}.dcppack-card-grid,.dcppack-problem-grid,.dcppack-compare-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dcppack-wrap h1{font-size:clamp(44px,9vw,68px)}}
        @media(max-width:680px){.dcppack-wrap{padding:0 14px;margin-top:32px}.dcppack-hero,.dcppack-hero-copy,.dcppack-hero-visual,.dcppack-simulator,.dcppack-principles,.dcppack-types,.dcppack-checklist,.dcppack-problems,.dcppack-next{border-radius:24px}.dcppack-mini,.dcppack-hero-points,.dcppack-card-grid,.dcppack-problem-grid,.dcppack-compare-grid{grid-template-columns:1fr}.dcppack-hero-visual{min-height:540px}.dcppack-progress>div,.dcppack-solutions-head{flex-direction:column;gap:6px}.dcppack-solutions-head strong{text-align:left}}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcppack-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcppack-wrap')) child.style.display = 'none';
                    });
                }
            }

            function clamp(v,min,max){return Math.max(min,Math.min(max,v));}
            function n(id){return parseFloat(document.getElementById(id).value);}
            function labelQuality(v){return v===4?'odlična':v===3?'dobra':v===2?'upitna':'loša';}
            function labelType(v){return v===1?'otvoreno':v===2?'papir/omot':v===3?'posuda':v===4?'vakuum':'omot';}
            function setBar(id,numId,value,warning){
                const val=clamp(Math.round(value),0,100);
                const el=document.getElementById(id);
                el.style.width=val+'%';
                document.getElementById(numId).textContent=val+'/100';
                el.closest('.dcppack-risk').classList.toggle('is-warning',warning&&val>60);
            }

            function updateSimulator(){
                const surface=n('dcppack-surface'), clean=n('dcppack-clean'), type=n('dcppack-type'), temp=n('dcppack-temp'), time=n('dcppack-time');

                document.getElementById('dcppack-surface-val').textContent=labelQuality(surface);
                document.getElementById('dcppack-clean-val').textContent=labelQuality(clean);
                document.getElementById('dcppack-type-val').textContent=labelType(type);
                document.getElementById('dcppack-temp-val').textContent=temp.toFixed(0)+' °C';
                document.getElementById('dcppack-time-val').textContent=time.toFixed(0)+' dana';

                let cond=10+(4-surface)*18+Math.max(0,temp-12)*4+(type===4?18:0)+Math.max(0,time-30)*0.7;
                let mold=10+(4-clean)*18+(4-surface)*15+Math.max(0,temp-10)*3+(type===4?12:0)+Math.max(0,time-21)*0.8;
                let oxid=12+(type===1?30:0)+(type===2?18:0)+Math.max(0,time-30)*0.8+Math.max(0,temp-14)*2;
                let aroma=10+(type===1?22:0)+(type===2?12:0)+Math.max(0,temp-12)*2+Math.max(0,time-45)*0.8;

                cond=clamp(cond,0,100);mold=clamp(mold,0,100);oxid=clamp(oxid,0,100);aroma=clamp(aroma,0,100);

                setBar('dcppack-cond','dcppack-cond-num',cond,true);
                setBar('dcppack-mold','dcppack-mold-num',mold,true);
                setBar('dcppack-oxid','dcppack-oxid-num',oxid,true);
                setBar('dcppack-aroma','dcppack-aroma-num',aroma,true);

                let title='Uvjeti pakiranja su dobri';
                let text='Proizvod je stabilan, površina je uredna i pakiranje može čuvati postignutu kvalitetu.';
                let advice='Pakiraj samo stabilan proizvod, označi šaržu i čuvaj ga u prikladnim uvjetima.';

                if(cond>65){
                    title='Rizik kondenzacije';
                    text='Površina ili temperatura nisu idealni za zatvoreno pakiranje.';
                    advice='Stabiliziraj proizvod prije pakiranja i ne zatvaraj ga ako je topao ili vlažan.';
                } else if(mold>65){
                    title='Rizik plijesni nakon pakiranja';
                    text='Nečistoća, vlažna površina ili previsoka temperatura povećavaju rizik.';
                    advice='Odvoji sumnjive komade, provjeri površinu i ne pakiraj problematičan proizvod zajedno s urednim.';
                } else if(oxid>65){
                    title='Rizik oksidacije';
                    text='Previše zraka i dugo čuvanje mogu oslabiti boju, miris i okus.';
                    advice='Za dulje čuvanje koristi prikladnije zatvaranje i nižu stabilnu temperaturu.';
                } else if(aroma>65){
                    title='Rizik gubitka arome';
                    text='Ambalaža ili uvjeti čuvanja mogu ubrzati slabljenje mirisa i okusa.';
                    advice='Odaberi ambalažu prema trajanju čuvanja i izbjegavaj okolinu jakih mirisa.';
                }

                document.getElementById('dcppack-status-title').textContent=title;
                document.getElementById('dcppack-status-text').textContent=text;
                document.getElementById('dcppack-advice').textContent=advice;
            }

            ['dcppack-surface','dcppack-clean','dcppack-type','dcppack-temp','dcppack-time'].forEach(function(id){
                const el=document.getElementById(id);
                if(el) el.addEventListener('input',updateSimulator);
            });

            updateSimulator();

            const checklistData={
                before:{title:'Prije pakiranja',items:[
                    ['Proizvod je stabilan','Nema sumnjivog mirisa, ljepljivosti ni vlažne površine.'],
                    ['Površina je čista','Nema nepoželjne plijesni, prljavštine ni ostataka.'],
                    ['Ambalaža je pripremljena','Omot, vrećice ili posude su čiste i prikladne.'],
                    ['Ruke i alat su čisti','Gotov proizvod ne smije se kontaminirati pri zadnjem koraku.'],
                    ['Šarža je poznata','Zna se naziv, datum, recept i status proizvoda.'],
                    ['Uvjeti čuvanja su spremni','Hladnjak, prostor ili polica ne smiju biti improvizacija.']
                ]},
                during:{title:'Tijekom pakiranja',items:[
                    ['Proizvod se ne grije','Ne stoji nepotrebno na toplom tijekom pakiranja.'],
                    ['Ne zatvara se vlažna površina','Ako ima vlage, proizvod treba stabilizirati prije zatvaranja.'],
                    ['Pakiranje nije pregrubo','Proizvod se ne gnječi, ne savija i ne oštećuje.'],
                    ['Ambalaža odgovara trajanju čuvanja','Kratko i dugo čuvanje ne traže isto pakiranje.'],
                    ['Oznaka se stavlja odmah','Oznaka se ne piše poslije po sjećanju.'],
                    ['Problematični komadi se odvajaju','Sumnjiv proizvod ne ide u isto pakiranje s dobrim.']
                ]},
                after:{title:'Nakon pakiranja',items:[
                    ['Pakiranje je zatvoreno uredno','Nema pukotina, curenja, zraka gdje ga ne treba ili lošeg spoja.'],
                    ['Oznaka je čitljiva','Naziv, datum i šarža su jasni.'],
                    ['Nema kondenzacije','Unutar pakiranja nema kapljica i orošavanja.'],
                    ['Temperatura čuvanja je prikladna','Proizvod se čuva u uvjetima primjerenim tipu pakiranja.'],
                    ['Rezani komadi se prate češće','Rezani proizvod je osjetljiviji od cijelog komada.'],
                    ['Bilješka završetka je upisana','Zabilježen je način pakiranja i plan čuvanja.']
                ]}
            };

            const solutions={
                'Proizvod je stabilan':['Nestabilan proizvod se pakiranjem ne popravlja.','Odvoji proizvod i provjeri miris, površinu i presjek prije pakiranja.'],
                'Površina je čista':['Nečista površina stvara rizik u zatvorenoj ambalaži.','Očisti ili odvoji problematičan komad prema stanju proizvoda.'],
                'Ambalaža je pripremljena':['Traženje ambalaže usred rada grije i izlaže proizvod.','Pripremi sve prije početka.'],
                'Ruke i alat su čisti':['Zadnji dodir može pokvariti sve prethodno.','Operi ruke, alat i radnu površinu.'],
                'Šarža je poznata':['Bez oznake nema sljedivosti.','Pripremi podatke prije pakiranja.'],
                'Uvjeti čuvanja su spremni':['Gotov proizvod ne smije čekati bez plana.','Pripremi hladan i čist prostor čuvanja.'],
                'Proizvod se ne grije':['Toplina stvara kondenzaciju i ubrzava kvarenje.','Radi u manjim serijama i vrati proizvod na hladno.'],
                'Ne zatvara se vlažna površina':['Vlaga u pakiranju vodi prema plijesni i lošem mirisu.','Pričekaj stabilizaciju površine.'],
                'Pakiranje nije pregrubo':['Oštećen proizvod brže gubi kvalitetu.','Rukuj mirno i bez gnječenja.'],
                'Ambalaža odgovara trajanju čuvanja':['Pogrešan omot daje presušivanje ili kondenzaciju.','Odaberi ambalažu prema planu potrošnje.'],
                'Oznaka se stavlja odmah':['Naknadno označavanje vodi do pogrešaka.','Označi odmah nakon pakiranja.'],
                'Problematični komadi se odvajaju':['Jedan loš komad može ugroziti cijelo pakiranje.','Odvoji ga i procijeni zasebno.'],
                'Pakiranje je zatvoreno uredno':['Loš spoj pušta zrak ili vlagu.','Ponovno zatvori ili prepakiraj.'],
                'Oznaka je čitljiva':['Nečitka oznaka nema vrijednost.','Prepiši oznaku jasno.'],
                'Nema kondenzacije':['Kondenzacija znači temperaturni ili površinski problem.','Otvori, stabiliziraj proizvod i ponovno pakiraj samo ako je uredan.'],
                'Temperatura čuvanja je prikladna':['Pogrešna temperatura ubrzava kvarenje ili isušivanje.','Premjesti proizvod u prikladan režim čuvanja.'],
                'Rezani komadi se prate češće':['Rezani komadi imaju veću izloženu površinu.','Provjeravaj ih češće i troši prije cijelih komada.'],
                'Bilješka završetka je upisana':['Bez završne bilješke gubi se kontrola procesa.','Upiši način pakiranja, datum i uvjete čuvanja.']
            };

            let activeTab='before';

            function renderChecklist(tab){
                activeTab=tab;
                const cfg=checklistData[tab];
                const list=document.getElementById('dcppack-check-list');
                document.getElementById('dcppack-check-title').textContent=cfg.title;

                list.innerHTML=cfg.items.map(function(item,index){
                    const k='drycured_pakiranje_check_'+tab+'_'+index;
                    const checked=localStorage.getItem(k)==='1';
                    return `<label class="dcppack-check-item ${checked?'is-checked':''}">
                        <input type="checkbox" data-index="${index}" ${checked?'checked':''}>
                        <span><strong>${item[0]}</strong><span>${item[1]}</span></span>
                    </label>`;
                }).join('');

                list.querySelectorAll('input[type="checkbox"]').forEach(function(box){
                    box.addEventListener('change',function(){
                        const k='drycured_pakiranje_check_'+activeTab+'_'+box.getAttribute('data-index');
                        localStorage.setItem(k,box.checked?'1':'0');
                        box.closest('.dcppack-check-item').classList.toggle('is-checked',box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist(){
                const boxes=Array.from(document.querySelectorAll('.dcppack-check-item input'));
                const checked=boxes.filter(function(b){return b.checked;}).length;
                const total=boxes.length||1;
                const pct=Math.round((checked/total)*100);

                document.getElementById('dcppack-check-count').textContent=checked+'/'+total+' označeno';
                document.getElementById('dcppack-check-bar').style.width=pct+'%';

                const unchecked=boxes.map(function(box){
                    if(box.checked) return null;
                    return box.closest('.dcppack-check-item').querySelector('strong').textContent.trim();
                }).filter(Boolean);

                const solTitle=document.getElementById('dcppack-solutions-title');
                const solList=document.getElementById('dcppack-solutions-list');

                if(!unchecked.length){
                    solTitle.textContent='Sve stavke su označene';
                    solList.innerHTML='<div class="dcppack-solution-card"><p>Sve stavke u ovoj fazi su označene. Proizvod je spreman za čuvanje prema odabranom načinu pakiranja.</p></div>';
                    return;
                }

                solTitle.textContent=unchecked.length+' stavki traži pažnju';
                solList.innerHTML=unchecked.map(function(title){
                    const s=solutions[title]||['Stavka traži provjeru.','Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `<article class="dcppack-solution-card">
                        <h4>${title}</h4>
                        <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                        <p><strong>Što napraviti:</strong> ${s[1]}</p>
                    </article>`;
                }).join('');
            }

            document.querySelectorAll('.dcppack-tabs button').forEach(function(button){
                button.addEventListener('click',function(){
                    document.querySelectorAll('.dcppack-tabs button').forEach(function(b){b.classList.remove('is-active');});
                    button.classList.add('is-active');
                    renderChecklist(button.getAttribute('data-tab'));
                });
            });

            renderChecklist(activeTab);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcppackv010_assets', 120);
