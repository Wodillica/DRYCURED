<?php
/**
 * Plugin Name: Drycured Enciklopedija znanja
 * Description: Dnevna rotacija članaka za rubriku Enciklopedija znanja na drycured.com.
 * Version: 1.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function drycured_ez_get_daily_post() {
    $category = get_category_by_slug('enciklopedija-znanja');
    if (!$category) {
        return null;
    }

    $query = new WP_Query(array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'cat'            => $category->term_id,
        'orderby'        => 'date',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ));

    if (empty($query->posts)) {
        return null;
    }

    $day_index = intval(current_time('z'));
    $post_ids = array_values($query->posts);
    $post_id = $post_ids[$day_index % count($post_ids)];

    return get_post($post_id);
}

function drycured_ez_get_image_url($post_id, $atts) {
    if (!empty($atts['image_url'])) {
        return esc_url($atts['image_url']);
    }

    if (!empty($atts['image_id'])) {
        $img = wp_get_attachment_image_url(intval($atts['image_id']), 'large');
        if ($img) {
            return esc_url($img);
        }
    }

    if (has_post_thumbnail($post_id)) {
        $img = get_the_post_thumbnail_url($post_id, 'large');
        if ($img) {
            return esc_url($img);
        }
    }

    $fallback_id = intval(get_option('drycured_ez_fallback_image_id', 0));
    if ($fallback_id > 0) {
        $img = wp_get_attachment_image_url($fallback_id, 'large');
        if ($img) {
            return esc_url($img);
        }
    }

    $fallback_url = get_option('drycured_ez_fallback_image_url', '');
    if (!empty($fallback_url)) {
        return esc_url($fallback_url);
    }

    return '';
}

function drycured_ez_daily_shortcode($atts) {
    $atts = shortcode_atts(array(
        'image_url' => '',
        'image_id'  => '',
    ), $atts, 'drycured_enciklopedija_dana');

    $post = drycured_ez_get_daily_post();

    if (!$post) {
        return '<div class="drycured-ez-card"><p>Rubrika Enciklopedija znanja uskoro se puni novim člancima.</p></div>';
    }

    $excerpt = get_post_meta($post->ID, '_drycured_card_excerpt', true);
    if (!$excerpt) {
        $excerpt = has_excerpt($post->ID) ? get_the_excerpt($post) : wp_trim_words(wp_strip_all_tags($post->post_content), 28);
    }

    $image_url = drycured_ez_get_image_url($post->ID, $atts);

    ob_start();
    ?>
    <article class="drycured-ez-card">
        <?php if ($image_url): ?>
            <a class="drycured-ez-image-link" href="<?php echo esc_url(get_permalink($post)); ?>">
                <img class="drycured-ez-image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title($post)); ?>" loading="lazy">
            </a>
        <?php endif; ?>

        <div class="drycured-ez-body">
            <div class="drycured-ez-label">Enciklopedija znanja</div>
            <h3 class="drycured-ez-title">
                <a href="<?php echo esc_url(get_permalink($post)); ?>">
                    <?php echo esc_html(get_the_title($post)); ?>
                </a>
            </h3>
            <p class="drycured-ez-excerpt"><?php echo esc_html($excerpt); ?></p>
            <a class="drycured-ez-link" href="<?php echo esc_url(get_permalink($post)); ?>">Saznaj više →</a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}
add_shortcode('drycured_enciklopedija_dana', 'drycured_ez_daily_shortcode');

function drycured_ez_inline_styles() {
    ?>
    <style>
    .drycured-ez-card {
        box-sizing: border-box;
        width: 100%;
        overflow: hidden;
        border-radius: 6px;
        background: transparent;
        color: #2f2a24;
    }

    .drycured-ez-image-link {
        display: block;
        width: 100%;
        margin-bottom: 22px;
        text-decoration: none;
    }

    .drycured-ez-image {
        display: block;
        width: 100%;
        height: 245px;
        object-fit: cover;
        border-radius: 4px;
    }

    .drycured-ez-body {
        padding: 0;
    }

    .drycured-ez-label {
        display: none;
    }

    .drycured-ez-title {
        margin: 0 0 12px 0;
        font-size: 21px;
        line-height: 1.3;
        font-weight: 500;
    }

    .drycured-ez-title a {
        color: #1f2530;
        text-decoration: none;
    }

    .drycured-ez-title a:hover {
        color: #d39a3a;
        text-decoration: underline;
    }

    .drycured-ez-excerpt {
        margin: 0 0 18px 0;
        font-size: 15px;
        line-height: 1.65;
        color: #545b64;
    }

    .drycured-ez-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #d39a3a;
        text-decoration: none;
    }

    .drycured-ez-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 767px) {
        .drycured-ez-image {
            height: 210px;
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'drycured_ez_inline_styles');

/**
 * Front page injector — preuzima postojeći Elementor okvir "Enciklopedija znanja"
 * i puni ga dnevnim člankom iz kategorije Enciklopedija znanja.
 */
if (!function_exists('drycured_ez_home_injector')) {
    function drycured_ez_home_injector() {
        if (!is_front_page() && !is_home()) {
            return;
        }

        if (!function_exists('drycured_ez_get_daily_post')) {
            return;
        }

        $post = drycured_ez_get_daily_post();
        if (!$post) {
            return;
        }

        $excerpt = get_post_meta($post->ID, '_drycured_card_excerpt', true);
        if (!$excerpt) {
            $excerpt = has_excerpt($post->ID)
                ? get_the_excerpt($post)
                : wp_trim_words(wp_strip_all_tags($post->post_content), 28);
        }

        $image_url = '';
        if (function_exists('drycured_ez_get_image_url')) {
            $image_url = drycured_ez_get_image_url($post->ID, array(
                'image_url' => '',
                'image_id' => '',
            ));
        }

        $data = array(
            'title'   => get_the_title($post),
            'url'     => get_permalink($post),
            'excerpt' => $excerpt,
            'image'   => $image_url,
        );
        ?>
        <script id="drycured-ez-home-injector">
        (function () {
            const data = <?php echo wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

            function norm(text) {
                return (text || '').replace(/\s+/g, ' ').trim().toLowerCase();
            }

            function esc(text) {
                const d = document.createElement('div');
                d.textContent = text || '';
                return d.innerHTML;
            }

            function findTarget() {
                const headings = document.querySelectorAll('h1,h2,h3,h4,h5,h6,.elementor-heading-title');
                for (const el of headings) {
                    if (norm(el.textContent) === 'enciklopedija znanja') {
                        return el;
                    }
                }

                const all = document.querySelectorAll('p,div,span');
                for (const el of all) {
                    if ((el.textContent || '').includes('Baza znanja o svakom koraku')) {
                        return el;
                    }
                }

                return null;
            }

            function findCardHost(el) {
                let node = el;
                let best = null;

                while (node && node !== document.body) {
                    const text = node.innerText || '';
                    const hasEnc = text.includes('Enciklopedija znanja') || text.includes('Baza znanja o svakom koraku');
                    const hasOtherCards = text.includes('Recepture') || text.includes('Atlas stilova Europe');

                    if (hasEnc && !hasOtherCards && (node.querySelector('img') || /saznaj više/i.test(text))) {
                        best = node;
                    }

                    node = node.parentElement;
                }

                return best ||
                    el.closest('.elementor-widget-wrap') ||
                    el.closest('.elementor-column') ||
                    el.closest('.e-con') ||
                    el.parentElement;
            }

            const target = findTarget();
            if (!target) {
                return;
            }

            const host = findCardHost(target);
            if (!host) {
                return;
            }

            const existingImg = host.querySelector('img');
            const image = data.image || (existingImg ? existingImg.src : '');

            host.classList.add('drycured-ez-forced-host');

            host.innerHTML = `
                <article class="drycured-ez-card drycured-ez-card--forced">
                    ${image ? `
                        <a class="drycured-ez-image-link" href="${esc(data.url)}">
                            <img class="drycured-ez-image" src="${esc(image)}" alt="${esc(data.title)}" loading="lazy">
                        </a>
                    ` : ''}
                    <div class="drycured-ez-body">
                        <h3 class="drycured-ez-title">
                            <a href="${esc(data.url)}">${esc(data.title)}</a>
                        </h3>
                        <p class="drycured-ez-excerpt">${esc(data.excerpt)}</p>
                        <a class="drycured-ez-link" href="${esc(data.url)}">Saznaj više →</a>
                    </div>
                </article>
            `;
        })();
        </script>
        <?php
    }

    add_action('wp_footer', 'drycured_ez_home_injector', 9999);
}

/**
 * Korekcija veličine slike u home bloku Enciklopedija znanja.
 * Slika mora imati isti vizualni ritam kao susjedne kartice.
 */
if (!function_exists('drycured_ez_home_image_size_fix')) {
    function drycured_ez_home_image_size_fix() {
        if (!is_front_page() && !is_home()) {
            return;
        }
        ?>
        <style id="drycured-ez-home-image-size-fix">
            .drycured-ez-forced-host .drycured-ez-card--forced {
                width: 100% !important;
                max-width: 100% !important;
            }

            .drycured-ez-forced-host .drycured-ez-image-link {
                display: block !important;
                width: 100% !important;
                margin-bottom: 22px !important;
                overflow: hidden !important;
            }

            .drycured-ez-forced-host .drycured-ez-image,
            .drycured-ez-card--forced .drycured-ez-image {
                display: block !important;
                width: 100% !important;
                height: 312px !important;
                max-height: 312px !important;
                object-fit: cover !important;
                object-position: center center !important;
                border-radius: 4px !important;
            }

            @media (max-width: 767px) {
                .drycured-ez-forced-host .drycured-ez-image,
                .drycured-ez-card--forced .drycured-ez-image {
                    height: 220px !important;
                    max-height: 220px !important;
                }
            }
        </style>
        <?php
    }

    add_action('wp_head', 'drycured_ez_home_image_size_fix', 10000);
}

/**
 * Finalna korekcija visine slike u bloku Enciklopedija znanja.
 */
if (!function_exists('drycured_ez_home_image_size_fix_v2')) {
    function drycured_ez_home_image_size_fix_v2() {
        if (!is_front_page() && !is_home()) {
            return;
        }
        ?>
        <style id="drycured-ez-home-image-size-fix-v2">
            .drycured-ez-forced-host .drycured-ez-image,
            .drycured-ez-card--forced .drycured-ez-image {
                width: 100% !important;
                height: 380px !important;
                max-height: 380px !important;
                object-fit: cover !important;
                object-position: center center !important;
            }
        </style>
        <?php
    }

    add_action('wp_head', 'drycured_ez_home_image_size_fix_v2', 10001);
}

/**
 * Stil javnog prikaza članaka Enciklopedije znanja.
 * Smanjuje prevelike naslove samo na uvezenim EZ člancima.
 */
if (!function_exists('drycured_ez_single_body_class')) {
    function drycured_ez_single_body_class($classes) {
        if (is_single()) {
            $post_id = get_queried_object_id();
            if ($post_id && get_post_meta($post_id, '_drycured_ez_id', true)) {
                $classes[] = 'drycured-ez-single';
            }
        }
        return $classes;
    }
    add_filter('body_class', 'drycured_ez_single_body_class');
}

if (!function_exists('drycured_ez_single_article_styles')) {
    function drycured_ez_single_article_styles() {
        if (!is_single()) {
            return;
        }

        $post_id = get_queried_object_id();
        if (!$post_id || !get_post_meta($post_id, '_drycured_ez_id', true)) {
            return;
        }
        ?>
        <style id="drycured-ez-single-article-styles">
            body.drycured-ez-single .entry-content h2,
            body.drycured-ez-single .post-content h2,
            body.drycured-ez-single article h2 {
                font-size: 26px !important;
                line-height: 1.28 !important;
                letter-spacing: 0.01em !important;
                margin-top: 34px !important;
                margin-bottom: 14px !important;
                font-weight: 600 !important;
            }

            body.drycured-ez-single .entry-content h3,
            body.drycured-ez-single .post-content h3,
            body.drycured-ez-single article h3 {
                font-size: 20px !important;
                line-height: 1.35 !important;
                margin-top: 24px !important;
                margin-bottom: 10px !important;
                font-weight: 600 !important;
            }

            body.drycured-ez-single .entry-content p,
            body.drycured-ez-single .post-content p,
            body.drycured-ez-single article p {
                font-size: 17px !important;
                line-height: 1.75 !important;
            }

            body.drycured-ez-single .entry-content table,
            body.drycured-ez-single .post-content table,
            body.drycured-ez-single article table {
                width: 100% !important;
                margin: 18px 0 28px 0 !important;
                border-collapse: collapse !important;
                font-size: 15px !important;
            }

            body.drycured-ez-single .entry-content th,
            body.drycured-ez-single .post-content th,
            body.drycured-ez-single article th {
                font-size: 15px !important;
                font-weight: 600 !important;
                padding: 10px 12px !important;
            }

            body.drycured-ez-single .entry-content td,
            body.drycured-ez-single .post-content td,
            body.drycured-ez-single article td {
                font-size: 15px !important;
                line-height: 1.55 !important;
                padding: 10px 12px !important;
            }
        </style>
        <?php
    }
    add_action('wp_head', 'drycured_ez_single_article_styles', 10020);
}
