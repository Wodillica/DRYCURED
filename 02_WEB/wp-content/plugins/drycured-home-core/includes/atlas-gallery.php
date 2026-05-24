<?php
defined('ABSPATH') || exit;

/**
 * Drycured Atlas Gallery v0.1.71
 * Čita /wp-content/uploads/drycured/atlas/atlas_manifest_web.csv
 * i prikazuje produkcijsku galeriju atlas karata.
 */

function drycured_home_core_atlas_gallery_manifest_path_v071() {
    return ABSPATH . 'wp-content/uploads/drycured/atlas/atlas_manifest_web.csv';
}

function drycured_home_core_atlas_gallery_base_url_v071() {
    return home_url('/wp-content/uploads/drycured/atlas/');
}

function drycured_home_core_atlas_gallery_read_manifest_v071() {
    $path = drycured_home_core_atlas_gallery_manifest_path_v071();

    if (!file_exists($path)) {
        return [];
    }

    $rows = [];
    $handle = fopen($path, 'r');

    if (!$handle) {
        return [];
    }

    $headers = fgetcsv($handle);
    if (!$headers) {
        fclose($handle);
        return [];
    }

    $headers = array_map(function($h) {
        return trim(str_replace("\xEF\xBB\xBF", '', $h));
    }, $headers);

    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) !== count($headers)) {
            continue;
        }

        $row = array_combine($headers, $data);

        if (empty($row['id']) || empty($row['webp_file']) || empty($row['thumb_file'])) {
            continue;
        }

        if (!empty($row['status']) && $row['status'] !== 'active') {
            continue;
        }

        $rows[] = $row;
    }

    fclose($handle);
    return $rows;
}

function drycured_home_core_atlas_gallery_type_label_v071($type) {
    $map = [
        'pregledna' => 'Pregledna karta',
        'drzavna' => 'Državna karta',
        'regionalna' => 'Regionalna karta',
    ];

    return $map[$type] ?? ucfirst($type);
}

function drycured_home_core_atlas_gallery_shortcode_v071() {
    $items = drycured_home_core_atlas_gallery_read_manifest_v071();

    if (!$items) {
        return '<section class="dc-atlas-gallery-v071"><p>Atlas karte još nisu učitane.</p></section>';
    }

    $base = drycured_home_core_atlas_gallery_base_url_v071();

    $featured = null;
    foreach ($items as $item) {
        if (($item['id'] ?? '') === 'atlas_eu_cijela') {
            $featured = $item;
            break;
        }
    }

    if (!$featured) {
        $featured = $items[0];
    }

    ob_start();
    ?>
    <section class="dc-atlas-gallery-v071" id="atlas-karte">
        <header class="dc-atlas-gallery-head">
            <div class="dc-atlas-gallery-kicker">Produkcijski atlas</div>
            <h2>Karte stilova Europe</h2>
            <p>
                Ovdje su objedinjene web-optimizirane karte europskih regija i država.
                Pregledne karte daju širu proizvodnu logiku, a državne i regionalne karte
                vode prema konkretnim stilovima, proizvodima i lokalnim posebnostima.
            </p>
        </header>

        <section class="dc-atlas-gallery-featured">
            <div class="dc-atlas-gallery-featured__image">
                <img src="<?php echo esc_url($base . 'web/' . $featured['webp_file']); ?>"
                     alt="<?php echo esc_attr($featured['title']); ?>"
                     loading="lazy">
            </div>
            <div class="dc-atlas-gallery-featured__text">
                <div class="dc-atlas-gallery-kicker">Glavna karta</div>
                <h3><?php echo esc_html($featured['title']); ?></h3>
                <p>
                    Početna orijentacijska karta za razumijevanje suhomesnatih stilova Europe.
                    Iz nje korisnik prelazi prema detaljnim zonama, državama i povezanim alatima.
                </p>
                <div class="dc-atlas-gallery-actions">
                    <a href="<?php echo esc_url($base . 'web/' . $featured['webp_file']); ?>" target="_blank" rel="noopener">Otvori punu kartu</a>
                    <a href="<?php echo esc_url(home_url('/kalkulator/')); ?>">Kalkulator receptura</a>
                </div>
            </div>
        </section>

        <div class="dc-atlas-gallery-filters" aria-label="Filtri atlasa">
            <button type="button" data-atlas-filter="all" class="is-active">Sve karte</button>
            <button type="button" data-atlas-filter="pregledna">Pregledne</button>
            <button type="button" data-atlas-filter="drzavna">Državne</button>
            <button type="button" data-atlas-filter="regionalna">Regionalne</button>
        </div>

        <div class="dc-atlas-gallery-grid">
            <?php foreach ($items as $item) :
                $type = $item['type'] ?? '';
                $zone = $item['zone'] ?? '';
                $title = $item['title'] ?? '';
                $web_url = $base . 'web/' . $item['webp_file'];
                $thumb_url = $base . 'thumbs/' . $item['thumb_file'];
                ?>
                <article class="dc-atlas-gallery-card" data-atlas-type="<?php echo esc_attr($type); ?>">
                    <a class="dc-atlas-gallery-card__image"
                       href="<?php echo esc_url($web_url); ?>"
                       target="_blank"
                       rel="noopener">
                        <img src="<?php echo esc_url($thumb_url); ?>"
                             alt="<?php echo esc_attr($title); ?>"
                             loading="lazy">
                    </a>

                    <div class="dc-atlas-gallery-card__body">
                        <div class="dc-atlas-gallery-card__meta">
                            <span><?php echo esc_html(drycured_home_core_atlas_gallery_type_label_v071($type)); ?></span>
                            <?php if ($zone) : ?>
                                <span><?php echo esc_html($zone); ?></span>
                            <?php endif; ?>
                        </div>

                        <h3><?php echo esc_html($title); ?></h3>

                        <div class="dc-atlas-gallery-card__actions">
                            <a href="<?php echo esc_url($web_url); ?>" target="_blank" rel="noopener">Otvori kartu</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return trim(ob_get_clean());
}
add_shortcode('drycured_atlas_gallery', 'drycured_home_core_atlas_gallery_shortcode_v071');

function drycured_home_core_atlas_gallery_styles_v071() {
    if (!is_page('atlas-stilova-europe')) {
        return;
    }
    ?>
    <style id="drycured-atlas-gallery-style-v071">
    .dc-atlas-gallery-v071 {
        max-width: 1180px;
        margin: 44px auto 84px;
        padding: 0 24px;
        color: #102033;
    }

    .dc-atlas-gallery-head {
        max-width: 920px;
        margin-bottom: 24px;
    }

    .dc-atlas-gallery-kicker {
        color: #9a7838;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .16em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .dc-atlas-gallery-head h2,
    .dc-atlas-gallery-featured__text h3 {
        margin: 0 0 10px;
        color: #102033;
        font-size: clamp(25px, 2.4vw, 34px);
        line-height: 1.16;
        font-weight: 700;
    }

    .dc-atlas-gallery-head p,
    .dc-atlas-gallery-featured__text p {
        margin: 0;
        color: #374151;
        font-size: 16.5px;
        line-height: 1.72;
    }

    .dc-atlas-gallery-featured {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr);
        gap: 18px;
        align-items: stretch;
        margin-bottom: 22px;
    }

    .dc-atlas-gallery-featured__image,
    .dc-atlas-gallery-featured__text,
    .dc-atlas-gallery-card {
        background: #fffaf0;
        border: 1px solid rgba(184,135,53,.22);
        border-radius: 16px;
        box-shadow: 0 12px 30px rgba(31,41,51,.07);
    }

    .dc-atlas-gallery-featured__image {
        padding: 14px;
        overflow: hidden;
    }

    .dc-atlas-gallery-featured__image img {
        display: block;
        width: 100%;
        height: auto;
        border-radius: 12px;
    }

    .dc-atlas-gallery-featured__text {
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .dc-atlas-gallery-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .dc-atlas-gallery-actions a,
    .dc-atlas-gallery-card__actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #9a7838;
        color: #fff !important;
        border-radius: 8px;
        padding: 10px 13px;
        text-decoration: none !important;
        font-size: 14px;
        font-weight: 700;
    }

    .dc-atlas-gallery-actions a:nth-child(2) {
        background: #102033;
    }

    .dc-atlas-gallery-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 18px 0 20px;
    }

    .dc-atlas-gallery-filters button {
        border: 1px solid rgba(154,120,56,.24);
        background: #fffaf0;
        color: #102033;
        border-radius: 999px;
        padding: 9px 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .dc-atlas-gallery-filters button.is-active {
        background: #102033;
        color: #fff;
        border-color: #102033;
    }

    .dc-atlas-gallery-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .dc-atlas-gallery-card {
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .dc-atlas-gallery-card__image {
        display: block;
        height: 190px;
        background: #fffdf8;
        overflow: hidden;
        text-decoration: none !important;
    }

    .dc-atlas-gallery-card__image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .22s ease;
    }

    .dc-atlas-gallery-card:hover img {
        transform: scale(1.025);
    }

    .dc-atlas-gallery-card__body {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .dc-atlas-gallery-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-bottom: 10px;
    }

    .dc-atlas-gallery-card__meta span {
        display: inline-flex;
        padding: 5px 8px;
        border-radius: 999px;
        background: rgba(154,120,56,.10);
        color: #8b6f47;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .dc-atlas-gallery-card h3 {
        margin: 0 0 14px;
        color: #102033;
        font-size: 19px;
        line-height: 1.22;
    }

    .dc-atlas-gallery-card__actions {
        margin-top: auto;
    }

    .dc-atlas-gallery-card.is-hidden {
        display: none !important;
    }

    @media (max-width: 1020px) {
        .dc-atlas-gallery-featured,
        .dc-atlas-gallery-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 680px) {
        .dc-atlas-gallery-v071 {
            padding: 0 16px;
        }

        .dc-atlas-gallery-featured,
        .dc-atlas-gallery-grid {
            grid-template-columns: 1fr;
        }

        .dc-atlas-gallery-card__image {
            height: 210px;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_home_core_atlas_gallery_styles_v071', 90);

function drycured_home_core_atlas_gallery_script_v071() {
    if (!is_page('atlas-stilova-europe')) {
        return;
    }
    ?>
    <script id="drycured-atlas-gallery-script-v071">
    (function () {
        function initAtlasFilters() {
            const root = document.querySelector('.dc-atlas-gallery-v071');
            if (!root) return;

            const buttons = root.querySelectorAll('[data-atlas-filter]');
            const cards = root.querySelectorAll('.dc-atlas-gallery-card');

            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const filter = btn.getAttribute('data-atlas-filter');

                    buttons.forEach(function (b) {
                        b.classList.remove('is-active');
                    });
                    btn.classList.add('is-active');

                    cards.forEach(function (card) {
                        const type = card.getAttribute('data-atlas-type');
                        const show = filter === 'all' || type === filter;
                        card.classList.toggle('is-hidden', !show);
                    });
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAtlasFilters);
        } else {
            initAtlasFilters();
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'drycured_home_core_atlas_gallery_script_v071', 90);

function drycured_home_core_atlas_gallery_insert_v071($content) {
    if (is_admin() || wp_doing_ajax()) {
        return $content;
    }

    if (!is_page('atlas-stilova-europe')) {
        return $content;
    }

    if (!in_the_loop() || !is_main_query()) {
        return $content;
    }

    if (get_option('drycured_atlas_gallery_enabled_v071', '1') !== '1') {
        return $content;
    }

    if (strpos($content, 'dc-atlas-gallery-v071') !== false) {
        return $content;
    }

    return $content . do_shortcode('[drycured_atlas_gallery]');
}
add_filter('the_content', 'drycured_home_core_atlas_gallery_insert_v071', 999999);
