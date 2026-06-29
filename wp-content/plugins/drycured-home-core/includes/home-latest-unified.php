<?php
defined('ABSPATH') || exit;

/**
 * Home — jedinstveni blok "Najnoviji sadržaj".
 * Verzija v0.1.47:
 * - sve tri kartice imaju isti markup
 * - podcast slika ostaje cover
 * - greška dana i infografika dana idu kao background contain u zaobljenom nosaču
 */

function drycured_home_core_latest_abs_url($path) {
    if (!$path) {
        return '';
    }

    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }

    return home_url($path);
}

function drycured_home_core_latest_unified_cards() {
    $cards = [];

    $error = function_exists('drycured_home_core_current_error_item')
        ? drycured_home_core_current_error_item()
        : null;

    if (!$error && function_exists('drycured_home_core_error_day_items')) {
        $items = drycured_home_core_error_day_items();
        if ($items) {
            $start = strtotime('2026-05-21 00:00:00');
            $now = current_time('timestamp');
            $day = max(0, floor(($now - $start) / DAY_IN_SECONDS));
            $error = $items[$day % count($items)];
        }
    }

    if ($error && !empty($error['slug'])) {
        $inf = function_exists('drycured_home_core_get_error_infographic')
            ? drycured_home_core_get_error_infographic($error['slug'])
            : null;

        $cards[] = [
            'type' => 'error',
            'kicker' => 'Greška dana',
            'title' => $error['title'],
            'excerpt' => $error['symptom'],
            'meta' => 'Povezana faza: ' . ($error['phase'] ?? ''),
            'url' => home_url('/greske/' . $error['slug'] . '/'),
            'image' => $inf['thumb'] ?? '',
            'button' => 'Otvori opis greške →',
            'fit' => 'contain',
        ];
    }

    $cards[] = [
        'type' => 'podcast',
        'kicker' => 'Novi podcast',
        'title' => 'EP05: Kontrola, sigurnost i dnevnik šarže',
        'excerpt' => 'Peta epizoda Drycured podcasta govori o sigurnosti procesa, praćenju kritičnih točaka i vođenju dnevnika šarže u domaćoj proizvodnji suhomesnatih proizvoda.',
        'meta' => '',
        'url' => home_url('/podcast/kontrola-sigurnost-i-dnevnik-sarze/'),
        'image' => home_url('/wp-content/uploads/2026/05/drycured_podcast_ep05-1024x683.png'),
        'button' => 'Slušaj epizodu →',
        'fit' => 'cover',
    ];

    $info_card = [
        'type' => 'infographic',
        'kicker' => 'Infografika dana',
        'title' => 'Vrste masnog tkiva',
        'excerpt' => 'Pregled različitih vrsta masnog tkiva i njihove uloge u okusu, teksturi, stabilnosti i izgledu suhomesnatih proizvoda.',
        'meta' => '',
        'url' => home_url('/infografike/vrste-masnog-tkiva/'),
        'image' => home_url('/wp-content/uploads/2026/05/aad7f827-0396-41e9-9387-4a4867370fd9-768x512.png'),
        'button' => 'Pogledaj infografiku →',
        'fit' => 'contain',
    ];

    if (function_exists('dc_infografike_items')) {
        $items = dc_infografike_items();
        if ($items) {
            $item = $items[intval(current_time('z')) % count($items)];
            $info_card = [
                'type' => 'infographic',
                'kicker' => 'Infografika dana',
                'title' => $item['title'] ?? 'Infografika dana',
                'excerpt' => $item['desc'] ?? '',
                'meta' => '',
                'url' => home_url('/infografike/' . ($item['slug'] ?? '') . '/'),
                'image' => drycured_home_core_latest_abs_url($item['thumb'] ?? ($item['image'] ?? '')),
                'button' => 'Pogledaj infografiku →',
                'fit' => 'contain',
            ];
        }
    }

    $cards[] = $info_card;

    return $cards;
}

function drycured_home_core_latest_unified_render() {
    if (!is_front_page()) {
        return;
    }

    if (get_option('drycured_home_core_latest_unified_enabled', '0') !== '1') {
        return;
    }

    $cards = drycured_home_core_latest_unified_cards();
    if (!$cards) {
        return;
    }

    $json = wp_json_encode($cards, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    ?>
    <style id="drycured-home-latest-unified-v047">
    body.home .elementor-element-34e7873 .elementor-posts-container.dc-latest-unified-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 30px !important;
        align-items: stretch !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card {
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

    body.home .elementor-element-34e7873 .dc-latest-card__media {
        display: block !important;
        width: 100% !important;
        height: 185px !important;
        min-height: 185px !important;
        max-height: 185px !important;
        border-radius: 10px !important;
        overflow: hidden !important;
        background-color: #fffdf8 !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        border: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        clip-path: inset(0 round 10px) !important;
        transform: translateZ(0) !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__media--cover {
        background-size: cover !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__media--contain {
        background-size: contain !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__media img {
        display: none !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__body {
        display: flex !important;
        flex-direction: column !important;
        flex: 1 1 auto !important;
        padding-top: 18px !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__kicker {
        color: #9a7838 !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: .16em !important;
        text-transform: uppercase !important;
        margin: 0 0 10px !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__title {
        margin: 0 0 10px !important;
        font-size: 22px !important;
        line-height: 1.25 !important;
        font-weight: 700 !important;
        color: #102033 !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__title a {
        color: #102033 !important;
        text-decoration: none !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card--infographic .dc-latest-card__title {
        display: none !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__text {
        margin: 0 0 14px !important;
        font-size: 15px !important;
        line-height: 1.62 !important;
        color: #374151 !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__meta {
        margin: 0 0 14px !important;
        font-size: 13.5px !important;
        color: #6b5b3a !important;
    }

    body.home .elementor-element-34e7873 .dc-latest-card__btn {
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

    body.home .elementor-element-34e7873 .dc-latest-card__btn:hover {
        background: #80622e !important;
        color: #fff !important;
    }

    @media (max-width: 980px) {
        body.home .elementor-element-34e7873 .elementor-posts-container.dc-latest-unified-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 767px) {
        body.home .elementor-element-34e7873 .elementor-posts-container.dc-latest-unified-grid {
            grid-template-columns: 1fr !important;
        }

        body.home .elementor-element-34e7873 .dc-latest-card {
            min-height: 0 !important;
        }

        body.home .elementor-element-34e7873 .dc-latest-card__media {
            height: 185px !important;
            min-height: 185px !important;
            max-height: 185px !important;
        }
    }
    </style>

    <script id="drycured-home-latest-unified-script-v047">
    (function () {
        const cards = <?php echo $json; ?>;

        function esc(value) {
            const div = document.createElement('div');
            div.textContent = value || '';
            return div.innerHTML;
        }

        function safeBg(value) {
            return String(value || '').replace(/"/g, '%22');
        }

        function cardHtml(card) {
            const type = esc(card.type || 'standard');
            const fit = card.fit === 'cover' ? 'cover' : 'contain';
            const mediaClass = 'dc-latest-card__media dc-latest-card__media--' + fit;

            const image = card.image
                ? `<a class="${mediaClass}" href="${esc(card.url)}" data-bg="${esc(card.image)}" aria-label="${esc(card.title)}"><img src="${esc(card.image)}" alt="${esc(card.title)}" loading="lazy"></a>`
                : '';

            const meta = card.meta
                ? `<div class="dc-latest-card__meta">${esc(card.meta)}</div>`
                : '';

            return `
                <article class="elementor-post elementor-grid-item dc-latest-card dc-latest-card--${type}">
                    ${image}
                    <div class="dc-latest-card__body">
                        <div class="dc-latest-card__kicker">${esc(card.kicker)}</div>
                        <h3 class="dc-latest-card__title"><a href="${esc(card.url)}">${esc(card.title)}</a></h3>
                        <p class="dc-latest-card__text">${esc(card.excerpt)}</p>
                        ${meta}
                        <a class="dc-latest-card__btn" href="${esc(card.url)}">${esc(card.button)}</a>
                    </div>
                </article>
            `;
        }

        function applyBackgrounds(root) {
            root.querySelectorAll('.dc-latest-card__media[data-bg]').forEach(function (el) {
                const bg = el.getAttribute('data-bg');
                if (bg) {
                    el.style.backgroundImage = 'url("' + safeBg(bg) + '")';
                }
            });
        }

        function applyUnifiedLatestCards() {
            const grid = document.querySelector('body.home .elementor-element-34e7873 .elementor-posts-container');
            if (!grid || !cards || !cards.length) {
                return false;
            }

            grid.classList.add('dc-latest-unified-grid');
            grid.innerHTML = cards.map(cardHtml).join('');
            grid.setAttribute('data-dc-latest-unified', 'v047');
            applyBackgrounds(grid);

            return true;
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyUnifiedLatestCards);
        } else {
            applyUnifiedLatestCards();
        }

        window.addEventListener('load', function () {
            [50, 250, 700, 1200, 2200, 3500, 5000].forEach(function (delay) {
                setTimeout(applyUnifiedLatestCards, delay);
            });
        });
    })();
    </script>
    <?php
}

add_action('wp_footer', 'drycured_home_core_latest_unified_render', 1000050);
