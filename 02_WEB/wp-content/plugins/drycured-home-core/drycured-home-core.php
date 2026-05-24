<?php
/**
 * Plugin Name: Drycured Home Core
 * Description: Vlastiti drycured.com home blokovi i interaktivni edukativni moduli.
 * Version: 0.1.96
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

define('DRYCURED_HOME_CORE_VERSION', '0.1.96');
define('DRYCURED_HOME_CORE_URL', plugin_dir_url(__FILE__));
define('DRYCURED_HOME_CORE_PATH', plugin_dir_path(__FILE__));

function drycured_home_core_enqueue_assets() {
    wp_enqueue_style(
        'drycured-home-core',
        DRYCURED_HOME_CORE_URL . 'assets/css/drycured-home-core.css',
        [],
        DRYCURED_HOME_CORE_VERSION
    );
}

function drycured_home_core_process_path_shortcode($atts = []) {
    drycured_home_core_enqueue_assets();

    $phases = [
        [
            'num' => '01',
            'title' => 'Sirovina',
            'text' => 'Odabir mesa određuje sve što slijedi: boju, teksturu, stabilnost i konačan okus proizvoda.',
            'risk' => 'Loša sirovina ne može se spasiti začinima ni dimom.',
            'action' => 'Birati svježe, pravilno ohlađeno meso stabilne boje, mirisa i strukture.'
        ],
        [
            'num' => '02',
            'title' => 'Soljenje',
            'text' => 'Sol pokreće konzerviranje, veže vodu i oblikuje osnovu sigurnosti proizvoda.',
            'risk' => 'Premalo soli smanjuje sigurnost, previše soli narušava okus.',
            'action' => 'Koristiti provjerene omjere i voditi zapis šarže.'
        ],
        [
            'num' => '03',
            'title' => 'Mljevenje i rezanje',
            'text' => 'Veličina čestica odlučuje o presjeku, zagrizu i vezivanju mase.',
            'risk' => 'Toplo meso i tupa oprema razmazuju mast.',
            'action' => 'Raditi hladno, oštrim noževima i pravilnom rešetkom.'
        ],
        [
            'num' => '04',
            'title' => 'Miješanje',
            'text' => 'Miješanjem se izvlače proteini koji povezuju meso, mast i začine u stabilnu masu.',
            'risk' => 'Premalo miješanja daje rastresitu kobasicu, previše miješanja grije masu.',
            'action' => 'Miješati kontrolirano, kratko i hladno, uz praćenje teksture.'
        ],
        [
            'num' => '05',
            'title' => 'Punjenje',
            'text' => 'Pravilno punjenje daje ravnomjeran oblik, dobar presjek i manje zračnih džepova.',
            'risk' => 'Zrak u nadjevu stvara šupljine i mjesta za kvarenje.',
            'action' => 'Puniti ravnomjerno, bez naglih prekida i pažljivo izbosti zarobljeni zrak.'
        ],
        [
            'num' => '06',
            'title' => 'Predsušenje',
            'text' => 'Kratko mirovanje nakon punjenja stabilizira površinu i priprema proizvod za daljnji proces.',
            'risk' => 'Prebrz prijelaz u dim ili sušenje može zatvoriti površinu.',
            'action' => 'Omogućiti miran početak procesa uz umjerenu vlagu i lagano strujanje zraka.'
        ],
        [
            'num' => '07',
            'title' => 'Fermentacija',
            'text' => 'Fermentacija razvija kiselost, aromu i mikrobiološku stabilnost kod trajnih kobasica.',
            'risk' => 'Previsoka temperatura i loša kontrola mogu dati kiseo miris jezgre.',
            'action' => 'Pratiti temperaturu, vlagu, vrijeme i po potrebi pH vrijednost.'
        ],
        [
            'num' => '08',
            'title' => 'Dimljenje',
            'text' => 'Dim daje aromu, boju i dodatnu zaštitu, ali samo ako je blag, čist i kontroliran.',
            'risk' => 'Prejak dim daje gorčinu i tamnu površinu.',
            'action' => 'Koristiti suh, čist dim i izbjegavati smolasto drvo.'
        ],
        [
            'num' => '09',
            'title' => 'Sušenje',
            'text' => 'Sušenje postupno uklanja vodu i stvara stabilnost proizvoda.',
            'risk' => 'Prebrzo sušenje stvara tvrdu koru i mekanu jezgru.',
            'action' => 'Uskladiti vlagu, temperaturu i strujanje zraka.'
        ],
        [
            'num' => '10',
            'title' => 'Zrenje',
            'text' => 'Zrenje razvija dubinu okusa, mirisa i teksture koju ne može zamijeniti brzina.',
            'risk' => 'Nestabilna mikroklima daje neujednačen proizvod.',
            'action' => 'Voditi proizvod polako, uz redovitu kontrolu mase, mirisa i površine.'
        ],
        [
            'num' => '11',
            'title' => 'Pakiranje',
            'text' => 'Pakiranje zatvara ciklus i čuva ono što su meso, sol, dim i vrijeme stvorili.',
            'risk' => 'Prerano zatvaranje može zarobiti višak vlage.',
            'action' => 'Pakirati tek kada je proizvod stabilan, suh i senzorski uredan.'
        ],
    ];

    ob_start();
    ?>
    <section class="dc-home-process" aria-labelledby="dc-home-process-title">
        <div class="dc-home-process__inner">
            <div class="dc-home-process__eyebrow">Digitalna pušnica</div>
            <h2 id="dc-home-process-title">Put od mesa do zrelog proizvoda</h2>
            <p class="dc-home-process__lead">
                Svaki dobar suhomesnati proizvod nastaje kroz niz povezanih koraka. Kada jedan korak zakaže,
                posljedice se vide kasnije — u presjeku, mirisu, teksturi ili sigurnosti proizvoda.
            </p>

            <div class="dc-home-process__grid">
                <?php foreach ($phases as $phase): ?>
                    <article class="dc-process-card">
                        <div class="dc-process-card__num"><?php echo esc_html($phase['num']); ?></div>
                        <h3><?php echo esc_html($phase['title']); ?></h3>
                        <p><?php echo esc_html($phase['text']); ?></p>
                        <details>
                            <summary>Najčešći rizik i rješenje</summary>
                            <div class="dc-process-card__details">
                                <strong>Rizik:</strong> <?php echo esc_html($phase['risk']); ?><br>
                                <strong>Rješenje:</strong> <?php echo esc_html($phase['action']); ?>
                            </div>
                        </details>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="dc-home-process__footer">
                <a href="<?php echo esc_url(home_url('/alati/')); ?>">Otvori alate Digitalne pušnice</a>
                <a href="<?php echo esc_url(home_url('/recepti/')); ?>">Pregledaj recepture</a>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_home_process_path', 'drycured_home_core_process_path_shortcode');

require_once DRYCURED_HOME_CORE_PATH . 'includes/process-rail.php';

/**
 * Drycured Home Core — sigurno umetanje procesnog vodiča na početnu stranicu.
 * Ne mijenja Elementor JSON ni post_content. Može se ugasiti opcijom:
 * wp option update drycured_home_core_front_rail_enabled 0 --allow-root
 */
function drycured_home_core_front_rail_inject($content) {
    if (is_admin() || wp_doing_ajax() || is_feed()) {
        return $content;
    }

    if (!is_singular('page') || !is_main_query() || !in_the_loop()) {
        return $content;
    }

    $front_id = (int) get_option('page_on_front');
    if (!$front_id || get_the_ID() !== $front_id) {
        return $content;
    }

    $enabled = get_option('drycured_home_core_front_rail_enabled', '0');
    if ($enabled !== '1') {
        return $content;
    }

    if (strpos($content, 'dc-process-rail') !== false || strpos($content, '[drycured_home_process_rail') !== false) {
        return $content;
    }

    return do_shortcode('[drycured_home_process_rail]') . $content;
}
add_filter('the_content', 'drycured_home_core_front_rail_inject', 4);

/**
 * Drycured Home Core v0.1.17
 * Stabilniji prikaz procesnog vodiča na Home stranici preko Astra hookova.
 * Ne mijenja Elementor JSON ni sadržaj stranice.
 */

/* Stari the_content inject gasimo da ne bi bilo dupliranja. */
remove_filter('the_content', 'drycured_home_core_front_rail_inject', 4);

function drycured_home_core_front_rail_should_render() {
    if (is_admin() || wp_doing_ajax() || is_feed()) {
        return false;
    }

    $enabled = get_option('drycured_home_core_front_rail_enabled', '0');
    if ($enabled !== '1') {
        return false;
    }

    $front_id = (int) get_option('page_on_front');
    if (!$front_id) {
        return false;
    }

    return is_front_page() || ((int) get_queried_object_id() === $front_id);
}

function drycured_home_core_front_rail_render_once() {
    static $printed = false;

    if ($printed) {
        return;
    }

    if (!drycured_home_core_front_rail_should_render()) {
        return;
    }

    $printed = true;

    echo "\n" . '<div class="dc-home-rail-hook-wrap">' . "\n";
    echo do_shortcode('[drycured_home_process_rail]');
    echo "\n" . '</div>' . "\n";
}

/*
 * Astra hookovi:
 * - astra_content_before: najčešće idealno mjesto ispod headera.
 * - astra_primary_content_top: fallback ako prvi hook ne sjedne u aktivnom layoutu.
 * Static $printed sprječava duplo prikazivanje.
 */
add_action('astra_content_before', 'drycured_home_core_front_rail_render_once', 8);
add_action('astra_primary_content_top', 'drycured_home_core_front_rail_render_once', 8);


/**
 * Učitaj CSS plugina i na stranicama procesa izrade.
 * Bez ovoga CSS za članke faza ne dolazi do preglednika.
 */
function drycured_home_core_enqueue_on_process_pages() {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return;
    }

    $parent = get_page_by_path('proces-izrade');
    if (!$parent) {
        return;
    }

    $current_id = (int) get_queried_object_id();
    $post = get_post($current_id);

    if (!$post) {
        return;
    }

    if ((int) $post->ID === (int) $parent->ID || (int) $post->post_parent === (int) $parent->ID) {
        drycured_home_core_enqueue_assets();
    }
}
add_action('wp_enqueue_scripts', 'drycured_home_core_enqueue_on_process_pages', 30);

/**
 * Učitaj CSS plugina na stranicama grešaka.
 */
function drycured_home_core_enqueue_on_error_pages() {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return;
    }

    $parent = get_page_by_path('greske');
    if (!$parent) {
        return;
    }

    $current_id = (int) get_queried_object_id();
    $post = get_post($current_id);

    if (!$post) {
        return;
    }

    if ((int) $post->ID === (int) $parent->ID || (int) $post->post_parent === (int) $parent->ID) {
        drycured_home_core_enqueue_assets();
    }
}
add_action('wp_enqueue_scripts', 'drycured_home_core_enqueue_on_error_pages', 30);

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/error-infographics.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/error-infographics.php';
}
if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/error-day.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/error-day.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/home-error-card.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/home-error-card.php';
}



if (!function_exists('drycured_home_core_home_latest_inline_polish_v040')) {
    function drycured_home_core_home_latest_inline_polish_v040() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-inline-polish-v040">
body.home .elementor-element-34e7873 .elementor-posts-container {
    align-items: stretch !important;
}

body.home .elementor-element-34e7873 article.elementor-post,
body.home .elementor-element-34e7873 .dc-runtime-podcast-card-host {
    background: #fffaf0 !important;
    border: 1px solid rgba(184,135,53,.24) !important;
    border-radius: 16px !important;
    padding: 16px !important;
    box-shadow: 0 12px 30px rgba(31,41,51,.07) !important;
    overflow: hidden !important;
    min-height: 430px !important;
    display: flex !important;
    flex-direction: column !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__image,
body.home .elementor-element-34e7873 .dc-runtime-podcast-image-link,
body.home .elementor-element-34e7873 .dc-infoday-image-link {
    display: block !important;
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    overflow: hidden !important;
    border-radius: 10px !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__image img,
body.home .elementor-element-34e7873 .dc-runtime-podcast-image {
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    object-fit: cover !important;
    object-position: center !important;
    border-radius: 10px !important;
    display: block !important;
}

body.home .elementor-element-34e7873 .dc-infoday-image-link img {
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    object-fit: contain !important;
    object-position: center !important;
    border-radius: 10px !important;
    display: block !important;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
}

body.home .elementor-element-34e7873 .dc-infoday-card,
body.home .elementor-element-34e7873 .dc-runtime-podcast-card {
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
    height: 100% !important;
    display: flex !important;
    flex-direction: column !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__body {
    padding-top: 18px !important;
    display: flex !important;
    flex-direction: column !important;
    flex: 1 1 auto !important;
}

body.home .elementor-element-34e7873 .dc-infoday-kicker,
body.home .elementor-element-34e7873 .dc-home-error-card__kicker,
body.home .elementor-element-34e7873 .dc-runtime-podcast-label {
    color: #9a7838 !important;
    font-size: 12px !important;
    font-weight: 800 !important;
    letter-spacing: .16em !important;
    text-transform: uppercase !important;
    margin: 18px 0 10px !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__title,
body.home .elementor-element-34e7873 .dc-runtime-podcast-title,
body.home .elementor-element-34e7873 .dc-infoday-card h3 {
    margin: 0 0 10px !important;
    font-size: 22px !important;
    line-height: 1.25 !important;
    font-weight: 700 !important;
    color: #102033 !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__title a,
body.home .elementor-element-34e7873 .dc-runtime-podcast-title a {
    color: #102033 !important;
    text-decoration: none !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__text,
body.home .elementor-element-34e7873 .dc-runtime-podcast-excerpt,
body.home .elementor-element-34e7873 .dc-infoday-card p {
    margin: 0 0 14px !important;
    font-size: 15px !important;
    line-height: 1.62 !important;
    color: #374151 !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__meta {
    margin: 0 0 14px !important;
    font-size: 13.5px !important;
    color: #6b5b3a !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__btn,
body.home .elementor-element-34e7873 .dc-infoday-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: fit-content !important;
    margin-top: auto !important;
    background: #9a7838 !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    text-decoration: none !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    box-shadow: none !important;
    opacity: 1 !important;
}

body.home .elementor-element-34e7873 .dc-home-error-card__btn:hover,
body.home .elementor-element-34e7873 .dc-infoday-btn:hover {
    background: #80622e !important;
    color: #fff !important;
}

body.home .elementor-element-34e7873 .dc-runtime-podcast-link {
    margin-top: auto !important;
    background: transparent !important;
    color: #c5903f !important;
    padding: 0 !important;
    border: 0 !important;
    box-shadow: none !important;
    text-decoration: none !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    letter-spacing: .08em !important;
    text-transform: uppercase !important;
}

@media (max-width: 767px) {
    body.home .elementor-element-34e7873 article.elementor-post,
    body.home .elementor-element-34e7873 .dc-runtime-podcast-card-host {
        min-height: 0 !important;
    }

    body.home .elementor-element-34e7873 .dc-home-error-card__image,
    body.home .elementor-element-34e7873 .dc-runtime-podcast-image-link,
    body.home .elementor-element-34e7873 .dc-infoday-image-link,
    body.home .elementor-element-34e7873 .dc-home-error-card__image img,
    body.home .elementor-element-34e7873 .dc-runtime-podcast-image,
    body.home .elementor-element-34e7873 .dc-infoday-image-link img {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
    }
}
</style>
HTML;
    }

    add_action('wp_head', 'drycured_home_core_home_latest_inline_polish_v040', 9999);
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/home-latest-unified.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/home-latest-unified.php';
}



if (!function_exists('drycured_home_core_latest_infographic_cleanup_v042')) {
    function drycured_home_core_latest_infographic_cleanup_v042() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-infographic-cleanup-v042">
/* Infografika dana: uklanja dodatni naslov-gumb, ostaje samo donji CTA */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__title {
    display: none !important;
}

/* Siguran nosač slike infografike: bez zlatnog unutarnjeg okvira i bez rezanja */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    padding: 10px !important;
    box-sizing: border-box !important;
    background: #fffdf8 !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 10px !important;
    overflow: hidden !important;
}

/* Sama infografika: nikad cover, nikad rezanje, nikad razvlačenje */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
    width: 100% !important;
    height: 100% !important;
    min-height: 0 !important;
    max-height: none !important;
    object-fit: contain !important;
    object-position: center center !important;
    display: block !important;
    border: 0 !important;
    box-shadow: none !important;
    border-radius: 6px !important;
    background: transparent !important;
}

/* Donji gumb infografike ostaje jedini zlatni gumb */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: fit-content !important;
    margin-top: auto !important;
    background: #9a7838 !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    text-decoration: none !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    box-shadow: none !important;
    opacity: 1 !important;
}

/* Na mobitelu neka infografika ostane cijela */
@media (max-width: 767px) {
    body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
        padding: 8px !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
        height: auto !important;
        width: 100% !important;
    }
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_infographic_cleanup_v042', 1000000);
}



if (!function_exists('drycured_home_core_latest_image_fit_v043')) {
    function drycured_home_core_latest_image_fit_v043() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-image-fit-v043">
/* Sve tri kartice imaju isti prostor za sliku kao podcast kartica */
body.home .elementor-element-34e7873 .dc-latest-card__media {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    overflow: hidden !important;
    border-radius: 10px !important;
    background: #fffdf8 !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Osnovna veličina svake slike */
body.home .elementor-element-34e7873 .dc-latest-card__image {
    display: block !important;
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    border-radius: 10px !important;
    border: 0 !important;
    box-shadow: none !important;
    background: transparent !important;
}

/* Podcast je fotografija/banner: smije popuniti prostor */
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__image {
    object-fit: cover !important;
    object-position: center center !important;
}

/* Greška dana i Infografika dana su tekstualne infografike: nikad crop, nikad rezanje */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
    object-fit: contain !important;
    object-position: center center !important;
}

/* Da tekstualne infografike ne izgledaju kao da su pobjegle u kut */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    padding: 6px !important;
    box-sizing: border-box !important;
}

/* Kod infografike dana ostaje samo donji gumb */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__title {
    display: none !important;
}

/* Donji gumb infografike mora biti normalno vidljiv */
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__btn,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__btn {
    opacity: 1 !important;
    visibility: visible !important;
    background: #9a7838 !important;
    color: #fff !important;
}

/* Na mobitelu bez rezanja */
@media (max-width: 767px) {
    body.home .elementor-element-34e7873 .dc-latest-card__media,
    body.home .elementor-element-34e7873 .dc-latest-card__image {
        height: auto !important;
        min-height: 0 !important;
        max-height: none !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
    body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
        width: 100% !important;
        height: auto !important;
    }
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_image_fit_v043', 1000002);
}



if (!function_exists('drycured_home_core_latest_final_polish_v044')) {
    function drycured_home_core_latest_final_polish_v044() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-final-polish-v044">
/* Slika / nosač slike: sve tri kartice isti radijus kao podcast */
body.home .elementor-element-34e7873 .dc-latest-card__media {
    border-radius: 10px !important;
    overflow: hidden !important;
    background: #fffdf8 !important;
}

/* Lijeva i desna kartica: tekstualne infografike ostaju cijele, ali u zaobljenom nosaču */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    border-radius: 10px !important;
    overflow: hidden !important;
    background: #fffdf8 !important;
    padding: 6px !important;
    box-sizing: border-box !important;
}

/* Sve slike imaju isti zaobljeni rub */
body.home .elementor-element-34e7873 .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__image {
    border-radius: 10px !important;
    overflow: hidden !important;
    display: block !important;
}

/* Podcast ostaje crop/cover jer je fotografija */
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__image {
    object-fit: cover !important;
}

/* Greška i infografika ostaju contain jer imaju tekst */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
    object-fit: contain !important;
}

/* Svi CTA gumbi u tri kartice isti — uključujući srednju podcast karticu */
body.home .elementor-element-34e7873 .dc-latest-card__btn,
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__btn,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__btn,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: fit-content !important;
    margin-top: auto !important;
    background: #9a7838 !important;
    color: #fff !important;
    border: 0 !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    text-decoration: none !important;
    font-size: 13.5px !important;
    font-weight: 700 !important;
    line-height: 1.2 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    box-shadow: none !important;
    opacity: 1 !important;
    visibility: visible !important;
}

body.home .elementor-element-34e7873 .dc-latest-card__btn:hover,
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__btn:hover,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__btn:hover,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__btn:hover {
    background: #80622e !important;
    color: #fff !important;
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_final_polish_v044', 1000004);
}



if (!function_exists('drycured_home_core_latest_rounded_infographics_v045')) {
    function drycured_home_core_latest_rounded_infographics_v045() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-rounded-infographics-v045">
/* Završno zaobljenje stvarnog prikaza infografika u lijevoj i desnoj kartici */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    border-radius: 10px !important;
    overflow: hidden !important;
    background: #fffdf8 !important;
    padding: 0 !important;
    box-sizing: border-box !important;
    clip-path: inset(0 round 10px) !important;
    transform: translateZ(0) !important;
}

/* Sama bitmapa dobiva isti rez kao podcast slika */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
    border-radius: 10px !important;
    overflow: hidden !important;
    clip-path: inset(0 round 10px) !important;
    -webkit-mask-image: -webkit-radial-gradient(white, black) !important;
    transform: translateZ(0) !important;
    object-fit: contain !important;
    object-position: center center !important;
    background: #fffdf8 !important;
}

/* Podcast ostaje referenca za izgled */
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__image {
    border-radius: 10px !important;
    overflow: hidden !important;
    clip-path: inset(0 round 10px) !important;
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_rounded_infographics_v045', 1000005);
}



if (!function_exists('drycured_home_core_latest_infographic_title_safe_v048')) {
    function drycured_home_core_latest_infographic_title_safe_v048() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-infographic-title-safe-v048">
/* Podcast ostaje referenca — bez promjene */
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__media {
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    background-size: cover !important;
    border-radius: 10px !important;
    overflow: hidden !important;
}

/* Infografike: zadržati isti prostor slike, ali smanjiti pozadinsku sliku za nekoliko px da naslov ne ulazi u zaobljeni rub */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    background-color: #fffdf8 !important;
    background-repeat: no-repeat !important;
    background-position: center center !important;
    background-size: auto calc(100% - 16px) !important;
    clip-path: inset(0 round 10px) !important;
}

/* Osiguranje: obični img ostaje sakriven jer slika dolazi kao background */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media img,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media img {
    display: none !important;
}

/* Ako neka infografika ima širi format, neka ne dodiruje rubove */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media[data-bg],
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media[data-bg] {
    background-origin: content-box !important;
}

/* Donji CTA ostaje normalan */
body.home .elementor-element-34e7873 .dc-latest-card__btn {
    background: #9a7838 !important;
    color: #fff !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_infographic_title_safe_v048', 1000060);
}



if (!function_exists('drycured_home_core_restore_latest_images_v049')) {
    function drycured_home_core_restore_latest_images_v049() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-restore-latest-images-v049">
/* Poništava problematični background-prikaz za lijevu i desnu infografiku */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 185px !important;
    min-height: 185px !important;
    max-height: 185px !important;
    padding: 8px !important;
    box-sizing: border-box !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    background-color: #fffdf8 !important;
    background-image: none !important;
    border: 0 !important;
    box-shadow: none !important;
    clip-path: inset(0 round 10px) !important;
}

/* Vraća stvarnu sliku i prikazuje je cijelu bez rezanja teksta */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media img,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media img,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image {
    display: block !important;
    position: static !important;
    inset: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    width: 100% !important;
    height: 100% !important;
    min-height: 0 !important;
    max-height: none !important;
    object-fit: contain !important;
    object-position: center center !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    background: transparent !important;
    box-shadow: none !important;
    border: 0 !important;
}

/* Podcast ostaje kakav jest — on je referenca */
body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__media {
    padding: 0 !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    background-image: none !important;
}

body.home .elementor-element-34e7873 .dc-latest-card--podcast .dc-latest-card__image {
    display: block !important;
    width: 100% !important;
    height: 185px !important;
    object-fit: cover !important;
    object-position: center center !important;
    border-radius: 10px !important;
}

/* CTA gumbi ostaju ujednačeni */
body.home .elementor-element-34e7873 .dc-latest-card__btn {
    background: #9a7838 !important;
    color: #fff !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_restore_latest_images_v049', 1000100);
}



if (!function_exists('drycured_home_core_latest_infographic_corners_v050')) {
    function drycured_home_core_latest_infographic_corners_v050() {
        if (!is_front_page()) {
            return;
        }

        echo <<<'HTML'
<style id="drycured-home-latest-infographic-corners-v050">
/* Ne diramo veličinu prikaza — samo zaobljenje lijeve i desne infografike */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    border-radius: 10px !important;
    overflow: hidden !important;
    background: #fffdf8 !important;
    box-shadow: none !important;
}

/* Stvarna slika infografike dobiva isti vizualni rez kao podcast slika */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__image,
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media img,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media img {
    border-radius: 10px !important;
    clip-path: inset(0 round 10px) !important;
    overflow: hidden !important;
    -webkit-mask-image: -webkit-radial-gradient(white, black) !important;
    transform: translateZ(0) !important;
}

/* Vrlo tanak rub samo da se zaobljenje infografike vidi na svijetloj podlozi */
body.home .elementor-element-34e7873 .dc-latest-card--error .dc-latest-card__media,
body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__media {
    border: 1px solid rgba(154,120,56,.14) !important;
}
</style>
HTML;
    }

    add_action('wp_footer', 'drycured_home_core_latest_infographic_corners_v050', 1000200);
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/home-link-router.php')) {
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/home-link-router.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/home-link-router.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/atlas-europe.php')) {
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/process-title-fix.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/process-title-fix.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/atlas-europe.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/atlas-europe.php';
}
if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/tools-hub.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/tools-hub.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/tools-page-override.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/tools-page-override.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/start-here.php')) {
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/start-here.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/start-here.php';
}
if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/atlas-gallery.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/atlas-gallery.php';
}

if (file_exists(DRYCURED_HOME_CORE_PATH . 'includes/atlas-interactive-map.php')) {
    require_once DRYCURED_HOME_CORE_PATH . 'includes/atlas-interactive-map.php';
}

