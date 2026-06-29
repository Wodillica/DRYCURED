<?php
/**
 * Plugin Name: Drycured Infografike
 * Description: Katalog infografika i blok Infografika dana za drycured.com.
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dc_infografike_items() {
    $base = '/wp-content/uploads/2026/05/';

    return [
        [
            'id' => 'i01',
            'slug' => 'tehnoloska-namjena-dijelova-mesa',
            'title' => 'Tehnološka namjena dijelova mesa',
            'desc' => 'Prikaz osnovnih dijelova mesa i njihove praktične namjene u proizvodnji suhomesnatih proizvoda.',
            'image' => $base . '0fefe5cc-5f38-418a-b511-08ed0d1466b4.png',
            'thumb' => $base . '0fefe5cc-5f38-418a-b511-08ed0d1466b4-768x512.png',
        ],
        [
            'id' => 'i02',
            'slug' => 'vodnost-mesa-kapacitet-vezanja-vode',
            'title' => 'Vodnost mesa – kapacitet vezanja vode',
            'desc' => 'Infografika objašnjava kako sposobnost mesa da zadržava vodu utječe na teksturu, sočnost, prinos i kvalitetu gotovog proizvoda.',
            'image' => $base . '2ea1abfc-a50d-481d-b75d-bf728aa26b42.png',
            'thumb' => $base . '2ea1abfc-a50d-481d-b75d-bf728aa26b42-768x512.png',
        ],
        [
            'id' => 'i03',
            'slug' => 'misici-po-bojama-but',
            'title' => 'Mišići po bojama – but',
            'desc' => 'Pregled tamnijih i svjetlijih mišića buta te njihove primjene u sušenju, zrenju, kobasicama i drugim proizvodima.',
            'image' => $base . '2f2fd242-b630-4628-9e56-1169bf2d5610.png',
            'thumb' => $base . '2f2fd242-b630-4628-9e56-1169bf2d5610-768x512.png',
        ],
        [
            'id' => 'i04',
            'slug' => 'smjer-rezanja-vlakana',
            'title' => 'Smjer rezanja vlakana',
            'desc' => 'Infografika prikazuje zašto rezanje poprečno na vlakna daje mekšu teksturu, bolji zagriz i pravilniji presjek proizvoda.',
            'image' => $base . '5724b20d-c324-4e64-8e88-9d5cab7fe473.png',
            'thumb' => $base . '5724b20d-c324-4e64-8e88-9d5cab7fe473-768x512.png',
        ],
        [
            'id' => 'i05',
            'slug' => 'ph-semafor-mesa',
            'title' => 'pH semafor mesa',
            'desc' => 'Praktični prikaz pH vrijednosti mesa i njihovog značenja za svježinu, sigurnost, vezanje vode i početak prerade.',
            'image' => $base . '8f309bbc-a8b5-46f6-94cc-64b82c154781.png',
            'thumb' => $base . '8f309bbc-a8b5-46f6-94cc-64b82c154781-768x512.png',
        ],
        [
            'id' => 'i06',
            'slug' => 'vrste-masnog-tkiva',
            'title' => 'Vrste masnog tkiva',
            'desc' => 'Pregled različitih vrsta masnog tkiva i njihove uloge u okusu, teksturi, stabilnosti i izgledu suhomesnatih proizvoda.',
            'image' => $base . 'aad7f827-0396-41e9-9387-4a4867370fd9.png',
            'thumb' => $base . 'aad7f827-0396-41e9-9387-4a4867370fd9-768x512.png',
        ],
        [
            'id' => 'i07',
            'slug' => 'kvaliteta-mesa-u-preradi',
            'title' => 'Kvaliteta mesa u preradi',
            'desc' => 'Infografika sažima ključne pokazatelje kvalitete mesa koji utječu na stabilnost, teksturu i sigurnost proizvoda.',
            'image' => $base . 'ad071f8b-a00a-419d-bed7-4707f4d2031d.png',
            'thumb' => $base . 'ad071f8b-a00a-419d-bed7-4707f4d2031d-768x512.png',
        ],
        [
            'id' => 'i08',
            'slug' => 'vrste-vode-u-mesu',
            'title' => 'Vrste vode u mesu',
            'desc' => 'Prikaz slobodne, vezane i strukturalne vode u mesu te njihova utjecaja na soljenje, sušenje, smrzavanje i kvalitetu proizvoda.',
            'image' => $base . 'b04e640e-52f4-49d6-a83f-eb8cb42e4e32.png',
            'thumb' => $base . 'b04e640e-52f4-49d6-a83f-eb8cb42e4e32-768x512.png',
        ],
        [
            'id' => 'i09',
            'slug' => 'struktura-mesa-i-preradbena-svojstva',
            'title' => 'Struktura mesa i preradbena svojstva',
            'desc' => 'Sažeti prikaz odnosa između strukture mesa, vode, proteina, masnoće i tehnološke stabilnosti tijekom prerade.',
            'image' => $base . 'bffeb832-a4a2-4497-a393-e1c4d9bf4ef9.png',
            'thumb' => $base . 'bffeb832-a4a2-4497-a393-e1c4d9bf4ef9-768x512.png',
        ],
        [
            'id' => 'i10',
            'slug' => 'vanjska-pokrovna-i-unutarnja-mast',
            'title' => 'Vanjska pokrovna i unutarnja mast',
            'desc' => 'Usporedba vanjske pokrovne masti i unutarnje masti s naglaskom na teksturu, stabilnost, namjenu i preradbenu vrijednost.',
            'image' => $base . 'cd1428ef-8da0-47f5-b040-d3c5b9cdda10.png',
            'thumb' => $base . 'cd1428ef-8da0-47f5-b040-d3c5b9cdda10-768x512.png',
        ],
        [
            'id' => 'i11',
            'slug' => 'anatomska-podjela-svinjskog-trupa',
            'title' => 'Anatomska podjela svinjskog trupa',
            'desc' => 'Pregled osnovnih anatomskih dijelova svinjskog trupa i njihove namjene u proizvodnji mesa i suhomesnatih proizvoda.',
            'image' => $base . 'ChatGPT-Image-5.-svi-2026.-21_35_15.png',
            'thumb' => $base . 'ChatGPT-Image-5.-svi-2026.-21_35_15-768x512.png',
        ],
        [
            'id' => 'i12',
            'slug' => 'postmortalne-promjene-i-ph-mesa',
            'title' => 'Postmortalne promjene i pH mesa',
            'desc' => 'Infografika prikazuje promjene u mesu nakon klanja, pad pH vrijednosti i praktičnu važnost pravilnog odležavanja.',
            'image' => $base . 'd3fbbc55-1446-4cb5-b923-77917e8bab6b.png',
            'thumb' => $base . 'd3fbbc55-1446-4cb5-b923-77917e8bab6b-768x512.png',
        ],
    ];
}

function dc_infografike_url($path) {
    return esc_url(home_url($path));
}

function dc_infografike_find($slug) {
    foreach (dc_infografike_items() as $item) {
        if ($item['slug'] === $slug) {
            return $item;
        }
    }
    return null;
}


function dc_infografike_print_seo_head($item = null) {
    if (function_exists('add_filter')) {
        add_filter('aioseo_disable', '__return_true', 999);
        add_filter('aioseo_disable_title_rewrites', '__return_true', 999);
    }

    remove_action('wp_head', '_wp_render_title_tag', 1);

    if (is_array($item)) {
        $title = $item['title'] . ' — drycured.com';
        $desc = $item['desc'];
        $url = home_url('/infografike/' . $item['slug'] . '/');
        $image = home_url($item['thumb'] ?: $item['image']);
        $type = 'article';
    } else {
        $title = 'Infografike — drycured.com';
        $desc = 'Katalog edukativnih infografika o mesu, soljenju, sušenju, zrenju i tradicionalnoj izradi suhomesnatih proizvoda.';
        $url = home_url('/infografike/');
        $image = home_url('/wp-content/uploads/2025/03/cropped-www.drycured.com_-1.png');
        $type = 'website';
    }

    echo "\n" . '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
}

add_shortcode('dc_infografika_dana', function () {
    $items = dc_infografike_items();
    if (!$items) {
        return '';
    }

    $start = strtotime('2026-05-05 00:00:00');
    $now = current_time('timestamp');
    $day = max(0, floor(($now - $start) / DAY_IN_SECONDS));
    $item = $items[$day % count($items)];

    $single_url = home_url('/infografike/' . $item['slug'] . '/');

    ob_start();
    ?>
    <div class="dc-infoday-card">
        <div class="dc-infoday-kicker">Infografika dana</div>
        <a class="dc-infoday-image-link" href="<?php echo esc_url($single_url); ?>">
            <img src="<?php echo dc_infografike_url($item['thumb']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
        </a>
        <h3><?php echo esc_html($item['title']); ?></h3>
        <p><?php echo esc_html($item['desc']); ?></p>
        <a class="dc-infoday-btn" href="<?php echo esc_url($single_url); ?>">Pogledaj infografiku →</a>
    </div>
    <?php
    return ob_get_clean();
});

add_action('wp_head', function () {
    ?>
    <style id="dc-infografike-css">
        .dc-infoday-card{
            background:#fff8e9;
            border:1px solid rgba(184,135,53,.34);
            border-radius:16px;
            padding:8px 18px 12px;
            box-shadow:0 8px 24px rgba(0,0,0,.07);
            text-align:left;
        }
        .dc-infoday-kicker{
            color:#b88735;
            font-size:13px;
            font-weight:700;
            letter-spacing:.08em;
            text-transform:uppercase;
            margin-bottom:10px;
        }
        .dc-infoday-card img{
            width:100%;
            height:auto;
            display:block;
            border-radius:10px;
            background:#efe6d4;
            margin-bottom:13px;
        }
        .dc-infoday-card h3{
            margin:0 0 8px;
            font-size:20px;
            line-height:1.25;
            color:#1f2933;
        }
        .dc-infoday-card p{
            margin:0 0 14px;
            font-size:15px;
            line-height:1.55;
            color:#4b5563;
        }
        .dc-infoday-btn{
            display:inline-block;
            padding:9px 13px;
            border-radius:8px;
            background:#d9a441;
            color:#fff !important;
            text-decoration:none !important;
            font-size:13px;
            font-weight:700;
        }
        .dc-infoday-btn:hover{
            filter:brightness(.96);
        }

        .dc-info-page{
            background:#f7f2e6;
            color:#1f2933;
            font-family:Arial, sans-serif;
            min-height:auto;
            padding:8px 18px 12px;
        }
        .dc-info-wrap{
            max-width:1640px;
            margin:0 auto;
        }
        .dc-info-top{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            margin-bottom:6px;
        }
        .dc-info-top h1{
            margin:0;
            font-size:16px;
            line-height:1.2;
            letter-spacing:.04em;
        }
        .dc-info-back,
        .dc-info-original{
            display:inline-block;
            padding:9px 13px;
            border-radius:8px;
            background:#d9a441;
            color:#fff !important;
            text-decoration:none !important;
            font-size:13px;
            font-weight:700;
        }
        .dc-info-panel{
            background:#fffaf0;
            border:1px solid rgba(180,140,70,.34);
            border-radius:18px;
            padding:5px;
            box-shadow:0 10px 30px rgba(0,0,0,.10);
        }
        .dc-info-figure{
            display:grid;
            grid-template-columns:minmax(0, 1fr) 255px;
            gap:10px;
            align-items:center;
        }
        .dc-info-figure img{
            display:block;
            width:auto;
            height:auto;
            max-width:100%;
            max-height:calc(100vh - 165px);
            margin:0 auto;
            object-fit:contain;
            border-radius:10px;
            background:#efe6d4;
        }
        .dc-info-caption{
            margin:0;
            padding:14px;
            text-align:left;
            color:#4b5563;
            font-size:15px;
            line-height:1.48;
            background:#fff7e8;
            border:1px solid rgba(180,140,70,.32);
            border-radius:14px;
            box-shadow:0 6px 18px rgba(0,0,0,.06);
        }
        .dc-info-caption strong{
            display:block;
            margin-bottom:8px;
            color:#1f2933;
            font-size:16px;
            line-height:1.25;
        }
        .dc-info-caption .dc-info-original{
            margin-top:12px;
        }

        .dc-info-archive-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:22px;
        }
        .dc-info-archive-card{
            background:#fffaf0;
            border:1px solid rgba(180,140,70,.34);
            border-radius:16px;
            padding:16px;
            box-shadow:0 8px 24px rgba(0,0,0,.07);
        }
        .dc-info-archive-card img{
            width:100%;
            height:auto;
            display:block;
            border-radius:10px;
            margin-bottom:12px;
        }
        .dc-info-archive-card h2{
            margin:0 0 8px;
            font-size:19px;
            line-height:1.25;
        }
        .dc-info-archive-card p{
            margin:0 0 12px;
            color:#4b5563;
            font-size:14px;
            line-height:1.5;
        }

        @media (max-width:900px){
            .dc-info-figure{
                display:block;
            }
            .dc-info-figure img{
                width:100%;
                max-height:none;
            }
            .dc-info-caption{
                margin-top:12px;
            }
            .dc-info-top{
                align-items:flex-start;
                flex-direction:column;
            }
        }
    </style>
    <?php
});

add_action('init', function () {
    add_rewrite_rule('^infografike/?$', 'index.php?dc_infografike=archive', 'top');
    add_rewrite_rule('^infografike/([^/]+)/?$', 'index.php?dc_infografike=$matches[1]', 'top');
});

add_filter('query_vars', function ($vars) {
    $vars[] = 'dc_infografike';
    return $vars;
});

add_action('template_redirect', function () {
    $q = get_query_var('dc_infografike');
    if (!$q) {
        return;
    }

    if ($q === 'archive') {
        dc_infografike_render_archive();
        exit;
    }

    $item = dc_infografike_find($q);
    if (!$item) {
        status_header(404);
        dc_infografike_render_404();
        exit;
    }

    dc_infografike_render_single($item);
    exit;
});

function dc_infografike_render_archive() {
    $items = dc_infografike_items();

    ?><!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Infografike — drycured.com</title>
        <?php dc_infografike_print_seo_head(); ?>
        <?php wp_head(); ?>
    </head>
    <body>
    <main class="dc-info-page">
        <div class="dc-info-wrap">
            <div class="dc-info-top">
                <h1>Infografike — drycured.com</h1>
                <a class="dc-info-back" href="<?php echo esc_url(home_url('/')); ?>">← Povratak na početnu</a>
            </div>

            <div class="dc-info-archive-grid">
                <?php foreach ($items as $item): ?>
                    <article class="dc-info-archive-card">
                        <a href="<?php echo esc_url(home_url('/infografike/' . $item['slug'] . '/')); ?>">
                            <img src="<?php echo dc_infografike_url($item['thumb']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                        </a>
                        <h2><?php echo esc_html($item['title']); ?></h2>
                        <p><?php echo esc_html($item['desc']); ?></p>
                        <a class="dc-infoday-btn" href="<?php echo esc_url(home_url('/infografike/' . $item['slug'] . '/')); ?>">Pogledaj infografiku →</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <?php wp_footer(); ?>
    </body>
    </html><?php
}

function dc_infografike_render_single($item) {
    ?><!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html($item['title']); ?> — drycured.com</title>
        <?php dc_infografike_print_seo_head($item); ?>
        <?php wp_head(); ?>
    </head>
    <body>
    <main class="dc-info-page">
        <div class="dc-info-wrap">
            <div class="dc-info-top">
                <h1><?php echo esc_html($item['title']); ?></h1>
                <a class="dc-info-back" href="<?php echo esc_url(home_url('/infografike/')); ?>">← Povratak na katalog</a>
            </div>

            <div class="dc-info-panel">
                <div class="dc-info-figure">
                    <img src="<?php echo dc_infografike_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                    <aside class="dc-info-caption">
                        <strong><?php echo esc_html($item['title']); ?></strong>
                        <?php echo esc_html($item['desc']); ?>
                        <br>
                        <a class="dc-info-original" href="<?php echo dc_infografike_url($item['image']); ?>" target="_blank" rel="noopener">Otvori original</a>
                    </aside>
                </div>
            </div>
        </div>
    </main>
    <?php wp_footer(); ?>
    </body>
    </html><?php
}

function dc_infografike_render_404() {
    ?><!doctype html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Infografika nije pronađena — drycured.com</title>
        <?php wp_head(); ?>
    </head>
    <body>
    <main class="dc-info-page">
        <div class="dc-info-wrap">
            <div class="dc-info-top">
                <h1>Infografika nije pronađena</h1>
                <a class="dc-info-back" href="<?php echo esc_url(home_url('/infografike/')); ?>">← Povratak na katalog</a>
            </div>
        </div>
    </main>
    <?php wp_footer(); ?>
    </body>
    </html><?php
}


/**
 * Home auto-slot v2:
 * Preciznije zamjenjuje namjensku placeholder karticu "Infografika dana"
 * blokom "Infografika dana".
 */
add_action('wp_footer', function () {
    if (!is_front_page() && !is_page(10)) {
        return;
    }

    $html = do_shortcode('[dc_infografika_dana]');
    if (!$html) {
        return;
    }
    ?>
    <script id="dc-home-infoday-auto-slot-v2">
    (function () {
        const infodayHtml = <?php echo wp_json_encode($html); ?>;
        const targetTitle = 'Infografika dana';

        function normalizeText(value) {
            return (value || '').replace(/\s+/g, ' ').trim();
        }

        function uniqueElements(elements) {
            return Array.from(new Set(elements));
        }

        function findCandidateCardFromTitle(node) {
            let el = node;

            for (let i = 0; i < 10 && el; i++, el = el.parentElement) {
                const text = normalizeText(el.textContent);
                const rect = el.getBoundingClientRect();

                if (!text.includes(targetTitle)) continue;
                if (!text.includes('Read More') && !text.includes('SAZNAJ VIŠE') && !text.includes('Saznaj više')) continue;

                if (rect.width < 180 || rect.width > 560) continue;
                if (rect.height < 90 || rect.height > 700) continue;

                return el;
            }

            return null;
        }

        function findMiddleArticle() {
            const titleNodes = Array.from(document.querySelectorAll('a, h1, h2, h3, h4, h5, .elementor-heading-title'))
                .filter(function (el) {
                    return normalizeText(el.textContent).includes(targetTitle);
                });

            let candidates = uniqueElements(
                titleNodes
                    .map(findCandidateCardFromTitle)
                    .filter(Boolean)
            );

            if (!candidates.length) {
                return null;
            }

            const pageCenter = window.innerWidth / 2;

            candidates.sort(function (a, b) {
                const ar = a.getBoundingClientRect();
                const br = b.getBoundingClientRect();

                const ac = ar.left + ar.width / 2;
                const bc = br.left + br.width / 2;

                const aHasImage = a.querySelector('img') ? 1000 : 0;
                const bHasImage = b.querySelector('img') ? 1000 : 0;

                return (Math.abs(ac - pageCenter) + aHasImage) - (Math.abs(bc - pageCenter) + bHasImage);
            });

            return candidates[0];
        }

        function applyInfodaySlot() {
            if (document.querySelector('.dc-home-infoday-replaced')) {
                return;
            }

            const target = findMiddleArticle();
            if (!target) {
                return;
            }

            target.classList.add('dc-home-infoday-replaced');
            target.innerHTML = infodayHtml;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyInfodaySlot);
        } else {
            applyInfodaySlot();
        }

        setTimeout(applyInfodaySlot, 400);
        setTimeout(applyInfodaySlot, 1000);
        setTimeout(applyInfodaySlot, 1800);
    })();
    </script>
    <?php
}, 120);

/**
 * Route fallback:
 * Sigurno hvata /infografike/ i /infografike/naziv-infografike/
 * čak i ako WordPress rewrite pravila nisu osvježena.
 */
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $path = parse_url($request_uri, PHP_URL_PATH);
    $path = trim(rawurldecode($path), '/');

    if ($path === 'infografike') {
        dc_infografike_render_archive();
        exit;
    }

    if (preg_match('#^infografike/([^/]+)/?$#', $path, $matches)) {
        $slug = sanitize_title($matches[1]);
        $item = dc_infografike_find($slug);

        if (!$item) {
            status_header(404);
            dc_infografike_render_404();
            exit;
        }

        dc_infografike_render_single($item);
        exit;
    }
}, 0);
