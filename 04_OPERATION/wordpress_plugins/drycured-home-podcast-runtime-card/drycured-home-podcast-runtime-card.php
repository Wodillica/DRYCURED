<?php
/**
 * Plugin Name: Drycured Home Podcast Runtime Card
 * Description: Tjedna rotacija podcast kartice na home stranici; zamjenjuje samo staru karticu recepta.
 * Version: 2.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dc_get_weekly_podcast_episode_data() {
    $parent = get_page_by_path('podcast');

    if (!$parent) {
        return null;
    }

    $episodes = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'post_parent'    => $parent->ID,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ));

    if (empty($episodes)) {
        return null;
    }

    /*
     * Rotacija od nedjelje 10.05.2026.
     * Jedna epizoda vrijedi sedam dana.
     */
    $start = strtotime('2026-05-10 00:00:00');
    $now = current_time('timestamp');
    $week_index = max(0, floor(($now - $start) / WEEK_IN_SECONDS));

    $episode = $episodes[$week_index % count($episodes)];

    $excerpt = get_post_meta($episode->ID, '_dc_podcast_excerpt', true);
    if (!$excerpt) {
        $excerpt = wp_trim_words(wp_strip_all_tags($episode->post_content), 28);
    }

    $image = get_the_post_thumbnail_url($episode->ID, 'large');

    return array(
        'title'   => get_the_title($episode),
        'url'     => get_permalink($episode),
        'excerpt' => $excerpt,
        'image'   => $image ? $image : '',
    );
}

add_action('wp_footer', function () {
    $data = dc_get_weekly_podcast_episode_data();

    if (!$data) {
        return;
    }
    ?>
    <script id="drycured-home-podcast-runtime-card">
    (function () {
        const data = <?php echo wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        const oldTitle = 'Recept za domaću kobasicu bez slike';
        const oldSlug = 'recept-za-domacu-kobasicu-bez-slike';

        function norm(t) {
            return (t || '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/\s+/g, ' ')
                .trim()
                .toLowerCase();
        }

        function esc(t) {
            const d = document.createElement('div');
            d.textContent = t || '';
            return d.innerHTML;
        }

        function buildCard() {
            return `
                <article class="dc-runtime-podcast-card">
                    ${data.image ? `
                    <a class="dc-runtime-podcast-image-link" href="${esc(data.url)}">
                        <img class="dc-runtime-podcast-image" src="${esc(data.image)}" alt="${esc(data.title)}" loading="lazy">
                    </a>` : ''}

                    <div class="dc-runtime-podcast-label">Novi podcast</div>

                    <h3 class="dc-runtime-podcast-title">
                        <a href="${esc(data.url)}">${esc(data.title)}</a>
                    </h3>

                    <p class="dc-runtime-podcast-excerpt">${esc(data.excerpt)}</p>

                    <a class="dc-runtime-podcast-link" href="${esc(data.url)}">Slušaj epizodu →</a>
                </article>
            `;
        }

        function renameHeading() {
            document.querySelectorAll('h1,h2,h3,h4,.elementor-heading-title').forEach(function (h) {
                if (norm(h.textContent) === 'najnoviji clanci') {
                    h.textContent = 'Najnoviji sadržaj';
                }
            });
        }

        function findOldRecipeLink() {
            const needle = norm(oldTitle);

            const links = Array.from(document.querySelectorAll('a'));
            for (const a of links) {
                const txt = norm(a.textContent || '');
                const href = norm(a.getAttribute('href') || '');

                if (txt.includes(needle) || href.includes(oldSlug)) {
                    return a;
                }
            }

            return null;
        }

        function findSmallestSafeCardRoot(startNode) {
            let node = startNode;

            for (let i = 0; i < 12 && node && node !== document.body; i++) {
                const text = norm(node.innerText || '');
                const html = node.innerHTML || '';

                const hasOldRecipe =
                    text.includes(norm(oldTitle)) ||
                    html.includes(oldSlug);

                const hasCardSignals =
                    node.querySelector('img') ||
                    text.includes('read more') ||
                    text.includes('saznaj vise') ||
                    text.includes('nema komentara');

                const protectedBlock =
                    text.includes('kalkulator novosti') ||
                    text.includes('infografika dana') ||
                    text.includes('vrste masnog tkiva') ||
                    text.includes('recepture') ||
                    text.includes('atlas stilova europe') ||
                    text.includes('najnoviji sadrzaj') ||
                    text.includes('najnoviji clanci');

                /*
                 * Vraćamo PRVI dovoljno velik, ali još siguran element.
                 * Ne penjemo se do cijele sekcije.
                 */
                if (hasOldRecipe && hasCardSignals && !protectedBlock) {
                    return node;
                }

                node = node.parentElement;
            }

            return null;
        }

        function replaceOldRecipeCard() {
            const oldLink = findOldRecipeLink();
            if (!oldLink) {
                return false;
            }

            const root = findSmallestSafeCardRoot(oldLink);
            if (!root) {
                return false;
            }

            root.innerHTML = buildCard();
            root.classList.add('dc-runtime-podcast-card-host');
            return true;
        }

        function refreshExistingPodcastCard() {
            const existing = document.querySelector('.dc-runtime-podcast-card');
            if (existing && existing.parentElement) {
                existing.parentElement.innerHTML = buildCard();
                return true;
            }

            return false;
        }

        function run() {
            renameHeading();

            if (refreshExistingPodcastCard()) {
                return;
            }

            replaceOldRecipeCard();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', run);
        } else {
            run();
        }

        window.addEventListener('load', run);

        let tries = 0;
        const timer = setInterval(function () {
            tries++;
            run();
            if (tries > 16 || document.querySelector('.dc-runtime-podcast-card')) {
                clearInterval(timer);
            }
        }, 500);
    })();
    </script>
    <?php
}, 999999);

add_action('wp_head', function () {
    ?>
    <style id="drycured-home-podcast-runtime-card-style">
        .dc-runtime-podcast-card {
            width: 100%;
        }

        .dc-runtime-podcast-image-link {
            display: block;
            width: 100%;
            margin-bottom: 18px;
            overflow: hidden;
            text-decoration: none !important;
            border-radius: 6px !important;
        }

        .dc-runtime-podcast-image {
            display: block;
            width: 100%;
            height: 210px;
            object-fit: cover;
            object-position: center center;
            border-radius: 6px !important;
        }

        .dc-runtime-podcast-label {
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.2;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #d39a3a;
            font-weight: 800;
        }

        .dc-runtime-podcast-title {
            margin: 0 0 10px 0;
            font-size: 18px !important;
            line-height: 1.3 !important;
            font-weight: 600 !important;
        }

        .dc-runtime-podcast-title a {
            color: #1f2530 !important;
            text-decoration: none !important;
        }

        .dc-runtime-podcast-title a:hover {
            color: #d39a3a !important;
            text-decoration: underline !important;
        }

        .dc-runtime-podcast-excerpt {
            margin: 0 0 14px 0;
            font-size: 14px !important;
            line-height: 1.55 !important;
            color: #555f68 !important;
        }

        .dc-runtime-podcast-link {
            display: inline-flex;
            align-items: center;
            font-size: 12px !important;
            font-weight: 800 !important;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #d39a3a !important;
            text-decoration: none !important;
        }

        .dc-runtime-podcast-link:hover {
            text-decoration: underline !important;
        }
    </style>
    <?php
}, 999999);
