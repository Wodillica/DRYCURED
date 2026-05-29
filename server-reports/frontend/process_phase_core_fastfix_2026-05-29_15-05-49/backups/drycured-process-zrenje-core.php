<?php
/**
 * Plugin Name: Drycured Process Zrenje Core
 * Description: Moderni edukativni sloj za stranicu /proces-izrade/zrenje/.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpzrnv010_enabled(): bool {
    return (bool) get_option('drycured_process_zrenje_enabled', 1);
}

function dcpzrnv010_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return in_array($path, ['proces-izrade/zrenje'], true);
}

function dcpzrnv010_page_url(string $path): string {
    $page = get_page_by_path($path);
    if ($page && $page->post_status === 'publish') {
        return get_permalink($page);
    }
    return home_url('/' . trim($path, '/') . '/');
}

function dcpzrnv010_image_url(string $filename): string {
    $rel = '/uploads/drycured/procesi/zrenje/' . ltrim($filename, '/');
    $path = WP_CONTENT_DIR . $rel;
    if (file_exists($path)) {
        return content_url($rel);
    }
    return '';
}

function dcpzrnv010_render(): string {
    if (!dcpzrnv010_enabled()) {
        return '';
    }

    $hero_img = dcpzrnv010_image_url('zrenje-hero-v02.jpg');

    ob_start();
    ?>
    <main id="dcpzrn-zrenje" class="dcpzrn-wrap" aria-label="Zrenje suhomesnatih proizvoda">

        <section class="dcpzrn-hero">
            <div class="dcpzrn-hero-copy">
                <span class="dcpzrn-eyebrow">Faza 11 — zrenje</span>
                <h1>Zrenje</h1>
                <p class="dcpzrn-lead">
                    Zrenje nije produženo sušenje. To je mirna završna faza u kojoj se vlaga, sol, aroma i tekstura
                    izjednačavaju kroz proizvod. Dobar proizvod u zrenju postaje zaokruženiji; loše vođen proizvod
                    može razviti presuhu površinu, vlažnu jezgru, neugodan miris ili nepoželjnu plijesan.
                </p>

                <div class="dcpzrn-actions">
                    <a href="#dcpzrn-simulator">Otvori simulator zrenja</a>
                    <a href="<?php echo esc_url(dcpzrnv010_page_url('proces-izrade/pakiranje')); ?>">Sljedeća faza: Pakiranje</a>
                </div>

                <div class="dcpzrn-mini">
                    <div><span>cilj</span><strong>aroma i stabilnost</strong></div>
                    <div><span>rizik</span><strong>plijesan i presušivanje</strong></div>
                    <div><span>kontrola</span><strong>miris + masa + površina</strong></div>
                </div>
            </div>

            <div class="dcpzrn-hero-visual <?php echo $hero_img ? 'has-image' : 'no-image'; ?>">
                <?php if ($hero_img): ?>
                    <img src="<?php echo esc_url($hero_img); ?>" alt="Zrenje suhomesnatih proizvoda" loading="lazy" decoding="async">
                <?php endif; ?>

                <div class="dcpzrn-visual-overlay">
                    <span>mirna završna faza</span>
                    <h2>Zrenje ne žuri. Ono dovršava ono što su sol, dim, sušenje i vrijeme započeli.</h2>
                </div>

                <div class="dcpzrn-hero-points">
                    <div><span>aroma</span><strong>zaokružuje se</strong></div>
                    <div><span>vlaga</span><strong>izjednačuje se</strong></div>
                    <div><span>površina</span><strong>prati se</strong></div>
                </div>
            </div>
        </section>

        <section id="dcpzrn-simulator" class="dcpzrn-simulator">
            <div class="dcpzrn-head">
                <span class="dcpzrn-eyebrow">Edukativna procjena</span>
                <h2>Simulator rizika zrenja</h2>
                <p>
                    Zrenje traži mirnije uvjete od agresivnog sušenja. Ovdje se prati temperatura, vlaga, strujanje zraka,
                    trajanje, stanje površine i gubitak mase. Cilj nije dodatno “ubiti” proizvod suhoćom, nego ga stabilizirati.
                </p>
            </div>

            <div class="dcpzrn-sim-shell">
                <div class="dcpzrn-controls">
                    <h3>Postavi uvjete zrenja</h3>

                    <div class="dcpzrn-control">
                        <label>Temperatura prostora <b id="dcpzrn-temp-val">12 °C</b></label>
                        <input id="dcpzrn-temp" type="range" min="6" max="22" value="12" step="1">
                    </div>

                    <div class="dcpzrn-control">
                        <label>Relativna vlaga <b id="dcpzrn-rh-val">76 %</b></label>
                        <input id="dcpzrn-rh" type="range" min="55" max="90" value="76" step="1">
                    </div>

                    <div class="dcpzrn-control">
                        <label>Strujanje zraka <b id="dcpzrn-air-val">blago</b></label>
                        <input id="dcpzrn-air" type="range" min="1" max="5" value="2" step="1">
                    </div>

                    <div class="dcpzrn-control">
                        <label>Trajanje zrenja <b id="dcpzrn-time-val">4 tjedna</b></label>
                        <input id="dcpzrn-time" type="range" min="1" max="16" value="4" step="1">
                    </div>

                    <div class="dcpzrn-control">
                        <label>Stanje površine <b id="dcpzrn-surface-val">uredna</b></label>
                        <input id="dcpzrn-surface" type="range" min="1" max="4" value="3" step="1">
                    </div>

                    <div class="dcpzrn-note">
                        Zrenje nije mjesto za popravljanje velikih grešaka. Ako je presjek već neravnomjeran, površina pretvrda
                        ili miris sumnjiv, prvo treba razumjeti uzrok, a ne samo “pustiti još malo”.
                    </div>
                </div>

                <div class="dcpzrn-output">
                    <div class="dcpzrn-status">
                        <span>procjena</span>
                        <h3 id="dcpzrn-status-title">Uvjeti zrenja su uravnoteženi</h3>
                        <p id="dcpzrn-status-text">
                            Uvjeti su mirni i pogodni za izjednačavanje arome, vlage i teksture.
                        </p>
                    </div>

                    <div class="dcpzrn-risk-bars">
                        <div class="dcpzrn-risk">
                            <label>Rizik presušivanja <span id="dcpzrn-over-num">0/100</span></label>
                            <i><b id="dcpzrn-over"></b></i>
                        </div>

                        <div class="dcpzrn-risk">
                            <label>Rizik neželjene plijesni <span id="dcpzrn-mold-num">0/100</span></label>
                            <i><b id="dcpzrn-mold"></b></i>
                        </div>

                        <div class="dcpzrn-risk">
                            <label>Rizik ustajalog mirisa <span id="dcpzrn-odor-num">0/100</span></label>
                            <i><b id="dcpzrn-odor"></b></i>
                        </div>

                        <div class="dcpzrn-risk">
                            <label>Rizik neravnomjernog presjeka <span id="dcpzrn-slice-num">0/100</span></label>
                            <i><b id="dcpzrn-slice"></b></i>
                        </div>
                    </div>

                    <div class="dcpzrn-advice" id="dcpzrn-advice">
                        Nastavi mirno zrenje, prati miris, površinu, masu i presjek probnog komada.
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpzrn-principles">
            <div class="dcpzrn-head">
                <span class="dcpzrn-eyebrow">Što se događa</span>
                <h2>Zrenje je faza izjednačavanja, a ne forsiranja</h2>
            </div>

            <div class="dcpzrn-card-grid">
                <article>
                    <b>01</b>
                    <h3>Aroma</h3>
                    <p>Arome se zaokružuju i smiruju. Prejak miris dima, soli ili kiseline postupno se uravnotežuje.</p>
                </article>

                <article>
                    <b>02</b>
                    <h3>Tekstura</h3>
                    <p>Unutrašnjost i površina postaju skladnije. Proizvod ne smije biti tvrd izvana, a mekan iznutra.</p>
                </article>

                <article>
                    <b>03</b>
                    <h3>Vlaga</h3>
                    <p>Vlaga se izjednačava kroz proizvod. Zrenje ne smije biti nastavak agresivnog isušivanja.</p>
                </article>

                <article>
                    <b>04</b>
                    <h3>Površina</h3>
                    <p>Površina se prati zbog plijesni, ljepljivosti, presušivanja i mirisa. Oko majstora tu je bolji senzor od ekrana.</p>
                </article>
            </div>
        </section>

        <section id="dcpzrn-checklist" class="dcpzrn-checklist">
            <div class="dcpzrn-head">
                <span class="dcpzrn-eyebrow">Kontrolna lista</span>
                <h2>Zrenje se prati mirno, ali redovito</h2>
                <p>
                    Kontrolna lista vodi kroz početak, tijek i završnu procjenu zrenja. Ako neka stavka nije u redu,
                    odmah se prikazuje konkretno rješenje.
                </p>
            </div>

            <div class="dcpzrn-check-shell">
                <div class="dcpzrn-tabs">
                    <button type="button" class="is-active" data-tab="before">Prije zrenja</button>
                    <button type="button" data-tab="during">Tijekom zrenja</button>
                    <button type="button" data-tab="after">Završna procjena</button>
                </div>

                <div class="dcpzrn-check-panel">
                    <div class="dcpzrn-progress">
                        <div>
                            <strong id="dcpzrn-check-title">Prije zrenja</strong>
                            <span id="dcpzrn-check-count">0/6 označeno</span>
                        </div>
                        <i><b id="dcpzrn-check-bar"></b></i>
                    </div>

                    <div id="dcpzrn-check-list" class="dcpzrn-check-list"></div>

                    <div class="dcpzrn-solutions">
                        <div class="dcpzrn-solutions-head">
                            <span>Što ako nije u redu?</span>
                            <strong id="dcpzrn-solutions-title">Rješenja za neoznačene stavke</strong>
                        </div>
                        <div id="dcpzrn-solutions-list" class="dcpzrn-solutions-list"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="dcpzrn-problems">
            <div class="dcpzrn-head">
                <span class="dcpzrn-eyebrow">Problem → rješenje</span>
                <h2>Greške u zrenju koje kvare završni dojam proizvoda</h2>
            </div>

            <div class="dcpzrn-problem-grid">
                <article>
                    <h3>Presuha i tvrda površina</h3>
                    <p><strong>Uzrok:</strong> preniska vlaga, prejako strujanje zraka ili predugo zrenje u suhim uvjetima.</p>
                    <p><strong>Rješenje:</strong> smanjiti strujanje, stabilizirati vlagu i više se ne oslanjati samo na izgled površine.</p>
                </article>

                <article>
                    <h3>Neželjena plijesan</h3>
                    <p><strong>Uzrok:</strong> previsoka vlaga, slaba izmjena zraka, prljav prostor ili kontakt proizvoda.</p>
                    <p><strong>Rješenje:</strong> odvojiti problematične komade, provjeriti higijenu, vlagu i razmak proizvoda.</p>
                </article>

                <article>
                    <h3>Ustajao ili težak miris</h3>
                    <p><strong>Uzrok:</strong> slab protok zraka, previsoka vlaga ili proizvod koji nije pravilno prošao ranije faze.</p>
                    <p><strong>Rješenje:</strong> prozračiti prostor, provjeriti vlagu i ne nastavljati slijepo ako je miris sumnjiv.</p>
                </article>

                <article>
                    <h3>Neravnomjeran presjek</h3>
                    <p><strong>Uzrok:</strong> površina i jezgra nisu se pravilno izjednačile ili je ranije nastala površinska kora.</p>
                    <p><strong>Rješenje:</strong> produžiti mirnije zrenje samo ako je miris uredan; za sljedeću šaržu usporiti sušenje.</p>
                </article>
            </div>
        </section>

        <section class="dcpzrn-next">
            <div>
                <span class="dcpzrn-eyebrow">Sljedeća faza</span>
                <h2>Nakon zrenja dolazi pakiranje i čuvanje</h2>
                <p>
                    Zreo proizvod mora imati stabilan miris, ujednačen presjek i teksturu primjerenu svojoj vrsti.
                    Pakiranje ne smije sakriti problem; ono samo čuva rezultat koji je već postignut.
                </p>
            </div>

            <div class="dcpzrn-next-actions">
                <a href="<?php echo esc_url(dcpzrnv010_page_url('proces-izrade/pakiranje')); ?>">Otvori fazu Pakiranje</a>
                <a href="<?php echo esc_url(dcpzrnv010_page_url('proces-izrade/susenje')); ?>">Vrati se na Sušenje</a>
            </div>
        </section>

    </main>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_process_zrenje', 'dcpzrnv010_render');

function dcpzrnv010_append_to_page($content) {
    static $added = false;

    if ($added || !dcpzrnv010_enabled()) {
        return $content;
    }

    if (!dcpzrnv010_is_page() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (strpos($content, 'dcpzrn-wrap') !== false) {
        return $content;
    }

    $added = true;
    return $content . do_shortcode('[drycured_process_zrenje]');
}
add_filter('the_content', 'dcpzrnv010_append_to_page', 35);

function dcpzrnv010_assets() {
    if (!dcpzrnv010_is_page() || !dcpzrnv010_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpzrn-wrap{--ink:#101722;--muted:#59636f;--gold:#b68a3a;--gold2:#f1d889;max-width:1220px;margin:46px auto 90px;padding:0 22px;color:var(--ink)}
        .dcpzrn-wrap *{box-sizing:border-box}
        .dcpzrn-eyebrow{display:inline-flex;width:max-content;min-height:30px;align-items:center;padding:7px 12px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:12px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpzrn-wrap h1{margin:18px 0 16px;color:var(--ink);font-size:clamp(48px,6.2vw,82px);line-height:.96;letter-spacing:-.06em;word-break:keep-all;hyphens:none}
        .dcpzrn-wrap h2{margin:14px 0 12px;color:var(--ink);font-size:clamp(30px,4.2vw,54px);line-height:1.03;letter-spacing:-.045em}
        .dcpzrn-wrap h3{margin:0 0 10px;color:var(--ink);font-size:21px;line-height:1.16}
        .dcpzrn-wrap p{color:var(--muted);font-size:16px;line-height:1.7}
        .dcpzrn-hero,.dcpzrn-simulator,.dcpzrn-principles,.dcpzrn-checklist,.dcpzrn-problems,.dcpzrn-next{border-radius:34px;background:rgba(255,255,255,.70);border:1px solid rgba(16,23,34,.08);box-shadow:0 24px 60px rgba(16,23,34,.08)}
        .dcpzrn-hero{display:grid;grid-template-columns:minmax(430px,.92fr) minmax(520px,1.08fr);gap:28px;align-items:stretch;margin-bottom:34px}
        .dcpzrn-hero-copy{padding:clamp(32px,4.5vw,58px);border-radius:34px;background:radial-gradient(circle at 10% 10%,rgba(241,216,137,.28),transparent 32%),linear-gradient(135deg,rgba(255,255,255,.86),rgba(255,255,255,.54))}
        .dcpzrn-lead{margin:0;color:#2f3943!important;font-size:clamp(18px,1.75vw,21px)!important;line-height:1.58!important}
        .dcpzrn-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:28px}
        .dcpzrn-actions a,.dcpzrn-next-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:46px;padding:12px 18px;border-radius:999px;background:#101722;color:#fff!important;text-decoration:none!important;font-weight:900;box-shadow:0 16px 34px rgba(16,23,34,.14)}
        .dcpzrn-actions a:nth-child(2),.dcpzrn-next-actions a:nth-child(2){background:#fff;color:#101722!important;border:1px solid rgba(16,23,34,.10)}
        .dcpzrn-mini,.dcpzrn-hero-points,.dcpzrn-card-grid,.dcpzrn-problem-grid{display:grid;gap:16px}
        .dcpzrn-mini{grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:30px}
        .dcpzrn-mini div,.dcpzrn-hero-points div{padding:16px;border-radius:20px;background:rgba(255,255,255,.72);border:1px solid rgba(16,23,34,.08)}
        .dcpzrn-mini span,.dcpzrn-hero-points span{display:block;margin-bottom:4px;color:#76551e;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase}
        .dcpzrn-mini strong,.dcpzrn-hero-points strong{display:block;color:var(--ink);font-size:17px;line-height:1.2}
        .dcpzrn-hero-visual{position:relative;min-height:560px;overflow:hidden;border-radius:34px;background:radial-gradient(circle at 35% 20%,rgba(241,216,137,.24),transparent 30%),radial-gradient(circle at 75% 75%,rgba(120,211,255,.14),transparent 32%),#101722;box-shadow:0 30px 70px rgba(16,23,34,.24)}
        .dcpzrn-hero-visual img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
        .dcpzrn-hero-visual::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(16,23,34,.05),rgba(16,23,34,.58)),radial-gradient(circle at top right,rgba(241,216,137,.18),transparent 38%);pointer-events:none}
        .dcpzrn-visual-overlay{position:absolute;z-index:2;left:28px;right:28px;top:28px;max-width:560px;color:#fff}
        .dcpzrn-visual-overlay span{display:inline-flex;margin-bottom:14px;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.14);color:#f1d889;font-size:12px;font-weight:900;letter-spacing:.1em;text-transform:uppercase;backdrop-filter:blur(10px)}
        .dcpzrn-visual-overlay h2{margin:0;color:#fff;font-size:clamp(26px,2.8vw,40px);line-height:1.06;letter-spacing:-.035em}
        .dcpzrn-hero-points{position:absolute;z-index:2;left:24px;right:24px;bottom:24px;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
        .dcpzrn-simulator,.dcpzrn-principles,.dcpzrn-checklist,.dcpzrn-problems,.dcpzrn-next{margin-top:34px;padding:clamp(28px,4vw,46px)}
        .dcpzrn-head{max-width:900px;margin-bottom:24px}
        .dcpzrn-sim-shell,.dcpzrn-check-shell{display:grid;grid-template-columns:330px minmax(0,1fr);gap:20px}
        .dcpzrn-controls,.dcpzrn-output,.dcpzrn-check-panel{border-radius:28px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06);padding:24px}
        .dcpzrn-output{background:radial-gradient(circle at top right,rgba(241,216,137,.14),transparent 32%),#101722;color:#fff}
        .dcpzrn-control{margin-bottom:18px}
        .dcpzrn-control label{display:flex;justify-content:space-between;gap:12px;margin-bottom:7px;color:var(--ink);font-size:14px;font-weight:900}
        .dcpzrn-control label b{color:#76551e;font-variant-numeric:tabular-nums}
        .dcpzrn-control input[type="range"]{width:100%;accent-color:#b68a3a}
        .dcpzrn-note{padding:14px;border-radius:18px;background:#fffaf0;border:1px solid rgba(182,138,58,.18);color:#59636f;font-size:13px;line-height:1.5}
        .dcpzrn-status{padding:18px;border-radius:22px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13)}
        .dcpzrn-status span{display:inline-flex;margin-bottom:10px;color:#f1d889;font-size:11px;font-weight:900;letter-spacing:.12em;text-transform:uppercase}
        .dcpzrn-status h3,.dcpzrn-status p{color:#fff}.dcpzrn-status p{opacity:.75}
        .dcpzrn-risk-bars{display:grid;gap:12px;margin-top:18px}
        .dcpzrn-risk label{display:flex;justify-content:space-between;gap:12px;margin-bottom:6px;color:rgba(255,255,255,.75);font-size:12px;font-weight:900;text-transform:uppercase}
        .dcpzrn-risk i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.12);overflow:hidden}
        .dcpzrn-risk b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff);transition:width .2s ease}
        .dcpzrn-risk.is-warning b{background:linear-gradient(90deg,#f1d889,#ff9a76)}
        .dcpzrn-advice{margin-top:18px;padding:16px;border-radius:20px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.13);color:rgba(255,255,255,.78);font-size:14px;line-height:1.6}
        .dcpzrn-card-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
        .dcpzrn-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .dcpzrn-card-grid article,.dcpzrn-problem-grid article{padding:22px;border-radius:24px;background:#fff;border:1px solid rgba(16,23,34,.08);box-shadow:0 18px 42px rgba(16,23,34,.06)}
        .dcpzrn-card-grid b{display:inline-flex;align-items:center;justify-content:center;min-width:52px;height:30px;margin-bottom:14px;border-radius:999px;background:#101722;color:#fff;font-size:12px;font-weight:900}
        .dcpzrn-tabs{display:grid;gap:10px;align-content:start}
        .dcpzrn-tabs button{width:100%;text-align:left;border:1px solid rgba(16,23,34,.10);border-radius:18px;padding:15px 16px;background:#fff;color:var(--ink);font-weight:950;cursor:pointer}
        .dcpzrn-tabs button.is-active{background:#101722;color:#fff}
        .dcpzrn-progress{margin-bottom:20px;padding:18px;border-radius:22px;background:#101722;color:#fff}
        .dcpzrn-progress>div{display:flex;justify-content:space-between;gap:14px;align-items:baseline;margin-bottom:13px}
        .dcpzrn-progress strong{color:#fff;font-size:19px}
        .dcpzrn-progress span{color:rgba(255,255,255,.72);font-size:12px;font-weight:900;white-space:nowrap}
        .dcpzrn-progress i{display:block;height:10px;border-radius:999px;background:rgba(255,255,255,.14);overflow:hidden}
        .dcpzrn-progress b{display:block;width:0%;height:100%;border-radius:999px;background:linear-gradient(90deg,#f1d889,#78d3ff)}
        .dcpzrn-check-list{display:grid;gap:10px}
        .dcpzrn-check-item{display:grid;grid-template-columns:28px minmax(0,1fr);gap:12px;align-items:start;padding:14px;border-radius:18px;background:rgba(16,23,34,.035);border:1px solid rgba(16,23,34,.07);cursor:pointer}
        .dcpzrn-check-item input{width:20px;height:20px;margin-top:2px;accent-color:#b68a3a}
        .dcpzrn-check-item strong{display:block;margin-bottom:3px;color:var(--ink);font-size:15.5px;line-height:1.25}
        .dcpzrn-check-item span span{display:block;color:var(--muted);font-size:13.5px;line-height:1.48}
        .dcpzrn-check-item.is-checked{background:rgba(182,138,58,.12);border-color:rgba(182,138,58,.22)}
        .dcpzrn-solutions{margin-top:18px;padding:18px;border-radius:22px;background:#fffaf0;border:1px solid rgba(182,138,58,.20)}
        .dcpzrn-solutions-head{display:flex;justify-content:space-between;gap:14px;align-items:flex-start;margin-bottom:14px}
        .dcpzrn-solutions-head span{display:inline-flex;width:max-content;min-height:28px;align-items:center;padding:6px 10px;border-radius:999px;background:rgba(182,138,58,.14);color:#76551e;font-size:11px;font-weight:950;letter-spacing:.10em;text-transform:uppercase}
        .dcpzrn-solutions-head strong{color:#101722;font-size:15px;text-align:right}
        .dcpzrn-solutions-list{display:grid;gap:10px}
        .dcpzrn-solution-card{padding:15px;border-radius:18px;background:#fff;border:1px solid rgba(16,23,34,.08)}
        .dcpzrn-solution-card h4{margin:0 0 7px;color:var(--ink);font-size:15.5px}
        .dcpzrn-solution-card p{margin:0;color:var(--muted);font-size:13.5px;line-height:1.55}
        .dcpzrn-solution-card p+p{margin-top:8px}.dcpzrn-solution-card strong{color:#76551e}
        .dcpzrn-next{display:grid;grid-template-columns:minmax(0,1fr) 290px;gap:22px;align-items:center;background:radial-gradient(circle at top right,rgba(120,211,255,.12),transparent 34%),#101722;color:#fff}
        .dcpzrn-next h2,.dcpzrn-next p{color:#fff}.dcpzrn-next p{opacity:.78}.dcpzrn-next-actions{display:grid;gap:12px}
        @media(max-width:1100px){.dcpzrn-hero,.dcpzrn-sim-shell,.dcpzrn-check-shell,.dcpzrn-next{grid-template-columns:1fr}.dcpzrn-card-grid,.dcpzrn-problem-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.dcpzrn-wrap h1{font-size:clamp(44px,9vw,68px)}}
        @media(max-width:680px){.dcpzrn-wrap{padding:0 14px;margin-top:32px}.dcpzrn-hero,.dcpzrn-hero-copy,.dcpzrn-hero-visual,.dcpzrn-simulator,.dcpzrn-principles,.dcpzrn-checklist,.dcpzrn-problems,.dcpzrn-next{border-radius:24px}.dcpzrn-mini,.dcpzrn-hero-points,.dcpzrn-card-grid,.dcpzrn-problem-grid{grid-template-columns:1fr}.dcpzrn-hero-visual{min-height:540px}.dcpzrn-progress>div,.dcpzrn-solutions-head{flex-direction:column;gap:6px}.dcpzrn-solutions-head strong{text-align:left}}
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const block = document.querySelector('.dcpzrn-wrap');
            if (block) {
                const entry = block.closest('.entry-content') || document.querySelector('.entry-content');
                if (entry) {
                    Array.from(entry.children).forEach(function (child) {
                        if (child === block) return;
                        if (!child.classList.contains('dcpzrn-wrap')) child.style.display = 'none';
                    });
                }
            }

            function clamp(v,min,max){return Math.max(min,Math.min(max,v));}
            function n(id){return parseFloat(document.getElementById(id).value);}
            function labelAir(v){return v===1?'vrlo slabo':v===2?'blago':v===3?'umjereno':v===4?'jako':'prejako';}
            function labelSurface(v){return v===4?'odlična':v===3?'uredna':v===2?'upitna':'problematična';}
            function setBar(id,numId,value,warning){
                const val=clamp(Math.round(value),0,100);
                const el=document.getElementById(id);
                el.style.width=val+'%';
                document.getElementById(numId).textContent=val+'/100';
                el.closest('.dcpzrn-risk').classList.toggle('is-warning',warning&&val>60);
            }

            function updateSimulator(){
                const temp=n('dcpzrn-temp'), rh=n('dcpzrn-rh'), air=n('dcpzrn-air'), time=n('dcpzrn-time'), surface=n('dcpzrn-surface');

                document.getElementById('dcpzrn-temp-val').textContent=temp.toFixed(0)+' °C';
                document.getElementById('dcpzrn-rh-val').textContent=rh.toFixed(0)+' %';
                document.getElementById('dcpzrn-air-val').textContent=labelAir(air);
                document.getElementById('dcpzrn-time-val').textContent=time.toFixed(0)+' tjedna';
                document.getElementById('dcpzrn-surface-val').textContent=labelSurface(surface);

                let over=10+Math.max(0,70-rh)*2.2+Math.max(0,air-3)*18+Math.max(0,time-10)*2.5+Math.max(0,temp-16)*3;
                let mold=10+Math.max(0,rh-82)*3.2+Math.max(0,2-air)*15+(4-surface)*13+Math.max(0,temp-15)*2;
                let odor=10+Math.max(0,rh-84)*3+Math.max(0,2-air)*18+(4-surface)*11;
                let slice=12+(4-surface)*14+Math.max(0,70-rh)*1.3+Math.max(0,rh-84)*1.4+Math.max(0,time-12)*2;

                over=clamp(over,0,100);mold=clamp(mold,0,100);odor=clamp(odor,0,100);slice=clamp(slice,0,100);

                setBar('dcpzrn-over','dcpzrn-over-num',over,true);
                setBar('dcpzrn-mold','dcpzrn-mold-num',mold,true);
                setBar('dcpzrn-odor','dcpzrn-odor-num',odor,true);
                setBar('dcpzrn-slice','dcpzrn-slice-num',slice,true);

                let title='Uvjeti zrenja su uravnoteženi';
                let text='Uvjeti su mirni i pogodni za izjednačavanje arome, vlage i teksture.';
                let advice='Nastavi mirno zrenje, prati miris, površinu, masu i presjek probnog komada.';

                if(over>65){
                    title='Rizik presušivanja';
                    text='Zrak ili preniska vlaga mogu dodatno stvrdnuti površinu i osušiti proizvod više nego što treba.';
                    advice='Smanji strujanje zraka, stabiliziraj vlagu i provjeri je li proizvod već spreman za pakiranje.';
                } else if(mold>65){
                    title='Rizik neželjene plijesni';
                    text='Previsoka vlaga, slaba cirkulacija ili problematična površina mogu pogodovati neželjenoj plijesni.';
                    advice='Odvoji problematične komade, provjeri higijenu prostora, vlagu i razmak između proizvoda.';
                } else if(odor>65){
                    title='Rizik ustajalog mirisa';
                    text='Slaba izmjena zraka i previsoka vlaga mogu dati težak, zatvoren miris.';
                    advice='Blago prozrači prostor i provjeri ima li proizvoda s neurednom površinom ili sumnjivim mirisom.';
                } else if(slice>65){
                    title='Rizik neravnomjernog presjeka';
                    text='Površina i jezgra možda se ne izjednačavaju kako treba.';
                    advice='Ako je miris uredan, nastavi mirno zrenje; za sljedeću šaržu uspori ranije sušenje.';
                }

                document.getElementById('dcpzrn-status-title').textContent=title;
                document.getElementById('dcpzrn-status-text').textContent=text;
                document.getElementById('dcpzrn-advice').textContent=advice;
            }

            ['dcpzrn-temp','dcpzrn-rh','dcpzrn-air','dcpzrn-time','dcpzrn-surface'].forEach(function(id){
                const el=document.getElementById(id);
                if(el) el.addEventListener('input',updateSimulator);
            });

            updateSimulator();

            const checklistData={
                before:{title:'Prije zrenja',items:[
                    ['Sušenje je završeno kontrolirano','Proizvod nije samo tvrd izvana, nego ujednačenije osušen.'],
                    ['Miris je uredan','Nema kiselog, ustajalog ili nečistog mirisa.'],
                    ['Površina je stabilna','Nema ljepljivosti, curenja ni problematične plijesni.'],
                    ['Prostor je čist','Zrenje ne smije unositi novi higijenski rizik.'],
                    ['Proizvodi imaju razmak','Zrak mora blago prolaziti oko svakog komada.'],
                    ['Bilješke su prenesene','Masa, trajanje sušenja i opažanja ostaju uz šaržu.']
                ]},
                during:{title:'Tijekom zrenja',items:[
                    ['Miris se redovito provjerava','Miris mora biti čist, ugodan i svojstven proizvodu.'],
                    ['Površina se prati','Svaka plijesan nije ista; problematična se mora odmah prepoznati.'],
                    ['Vlaga je stabilna','Previsoka vlaga potiče probleme, preniska dodatno suši proizvod.'],
                    ['Strujanje je blago','Zrenje traži miran zrak, ne propuh.'],
                    ['Proizvodi se ne dodiruju','Kontakt proizvoda stvara vlažne točke i plijesan.'],
                    ['Bilježe se promjene','Promjena mirisa, boje, mase i teksture mora ostati zapisana.']
                ]},
                after:{title:'Završna procjena',items:[
                    ['Presjek je ujednačen','Nema izražene kore ni vlažne jezgre.'],
                    ['Tekstura odgovara proizvodu','Proizvod nije gumast, premekan ni pretvrd.'],
                    ['Miris je zaokružen','Nema oštrih, ustajalih ili nečistih nota.'],
                    ['Površina je uredna','Nema nepoželjne plijesni ni ljepljivosti.'],
                    ['Šarža je dokumentirana','Zapisani su trajanje, uvjeti i završna procjena.'],
                    ['Pakiranje je pripremljeno','Proizvod ne čeka nepotrebno nakon završne procjene.']
                ]}
            };

            const solutions={
                'Sušenje je završeno kontrolirano':['Ako je površina samo tvrda, zrenje neće čarobno popraviti jezgru.','Provjeri masu, presjek probnog komada i znakove površinske kore.'],
                'Miris je uredan':['Sumnjiv miris se u zrenju može samo pogoršati.','Odvoji problematičan komad i provjeri uzrok prije nastavka.'],
                'Površina je stabilna':['Ljepljivost ili neuredna plijesan znače rizik.','Stabiliziraj uvjete i odvoji sumnjive komade.'],
                'Prostor je čist':['Prljav prostor prenosi problem na gotov proizvod.','Očisti prostor i ukloni izvore kontaminacije.'],
                'Proizvodi imaju razmak':['Dodir stvara vlažne točke.','Razmakni proizvode i omogući blagu cirkulaciju.'],
                'Bilješke su prenesene':['Bez bilješki ne znaš što zrenje treba dovršiti.','Prenesi masu, trajanje i opažanja iz sušenja.'],
                'Miris se redovito provjerava':['Miris prvi javlja da nešto nije u redu.','Provjeravaj šaržu redovito i ne ignoriraj promjenu.'],
                'Površina se prati':['Nepoželjna plijesan brzo se širi.','Odvoji komade i provjeri vlagu, zrak i higijenu.'],
                'Vlaga je stabilna':['Nestabilna vlaga stvara i plijesan i presušivanje.','Ujednači uvjete prije nastavka.'],
                'Strujanje je blago':['Propuh dodatno suši površinu.','Smanji direktno strujanje zraka.'],
                'Proizvodi se ne dodiruju':['Mjesta dodira ostaju vlažna.','Razmakni proizvode i provjeri mjesta kontakta.'],
                'Bilježe se promjene':['Bez zapisa se greška ponavlja.','Upiši promjene mirisa, površine, mase i teksture.'],
                'Presjek je ujednačen':['Neujednačen presjek znači da proces još nije skladan.','Procijeni miris i po potrebi nastavi mirnije zrenje.'],
                'Tekstura odgovara proizvodu':['Tekstura govori je li proizvod zaista spreman.','Usporedi teksturu s ciljem proizvoda.'],
                'Miris je zaokružen':['Oštar ili nečist miris ne treba pakirati.','Odvoji proizvod i provjeri uzrok.'],
                'Površina je uredna':['Površina mora biti stabilna prije pakiranja.','Riješi plijesan, ljepljivost ili presušivanje prije pakiranja.'],
                'Šarža je dokumentirana':['Bez dokumentacije nema ponavljanja uspjeha.','Upiši završne uvjete i procjenu.'],
                'Pakiranje je pripremljeno':['Gotov proizvod ne treba nepotrebno čekati.','Pripremi ambalažu i uvjete čuvanja prije završne procjene.']
            };

            let activeTab='before';

            function renderChecklist(tab){
                activeTab=tab;
                const cfg=checklistData[tab];
                const list=document.getElementById('dcpzrn-check-list');
                document.getElementById('dcpzrn-check-title').textContent=cfg.title;

                list.innerHTML=cfg.items.map(function(item,index){
                    const k='drycured_zrenje_check_'+tab+'_'+index;
                    const checked=localStorage.getItem(k)==='1';
                    return `<label class="dcpzrn-check-item ${checked?'is-checked':''}">
                        <input type="checkbox" data-index="${index}" ${checked?'checked':''}>
                        <span><strong>${item[0]}</strong><span>${item[1]}</span></span>
                    </label>`;
                }).join('');

                list.querySelectorAll('input[type="checkbox"]').forEach(function(box){
                    box.addEventListener('change',function(){
                        const k='drycured_zrenje_check_'+activeTab+'_'+box.getAttribute('data-index');
                        localStorage.setItem(k,box.checked?'1':'0');
                        box.closest('.dcpzrn-check-item').classList.toggle('is-checked',box.checked);
                        updateChecklist();
                    });
                });

                updateChecklist();
            }

            function updateChecklist(){
                const boxes=Array.from(document.querySelectorAll('.dcpzrn-check-item input'));
                const checked=boxes.filter(function(b){return b.checked;}).length;
                const total=boxes.length||1;
                const pct=Math.round((checked/total)*100);

                document.getElementById('dcpzrn-check-count').textContent=checked+'/'+total+' označeno';
                document.getElementById('dcpzrn-check-bar').style.width=pct+'%';

                const unchecked=boxes.map(function(box){
                    if(box.checked) return null;
                    return box.closest('.dcpzrn-check-item').querySelector('strong').textContent.trim();
                }).filter(Boolean);

                const solTitle=document.getElementById('dcpzrn-solutions-title');
                const solList=document.getElementById('dcpzrn-solutions-list');

                if(!unchecked.length){
                    solTitle.textContent='Sve stavke su označene';
                    solList.innerHTML='<div class="dcpzrn-solution-card"><p>Sve stavke u ovoj fazi su označene. Nastavi mirno, bez nepotrebnog forsiranja procesa.</p></div>';
                    return;
                }

                solTitle.textContent=unchecked.length+' stavki traži pažnju';
                solList.innerHTML=unchecked.map(function(title){
                    const s=solutions[title]||['Stavka traži provjeru.','Zaustavi proces i provjeri uvjete prije nastavka.'];
                    return `<article class="dcpzrn-solution-card">
                        <h4>${title}</h4>
                        <p><strong>Zašto je važno:</strong> ${s[0]}</p>
                        <p><strong>Što napraviti:</strong> ${s[1]}</p>
                    </article>`;
                }).join('');
            }

            document.querySelectorAll('.dcpzrn-tabs button').forEach(function(button){
                button.addEventListener('click',function(){
                    document.querySelectorAll('.dcpzrn-tabs button').forEach(function(b){b.classList.remove('is-active');});
                    button.classList.add('is-active');
                    renderChecklist(button.getAttribute('data-tab'));
                });
            });

            renderChecklist(activeTab);
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpzrnv010_assets', 120);
