<?php
/**
 * Plugin Name: Drycured Podcast Card Shortcode
 * Description: Shortcode za prikaz podcast kartice na home stranici.
 * Version: 1.0.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dc_get_podcast_episode_page() {
    $page = get_page_by_path('podcast/osnove-dobrog-suhomesnatog-proizvoda');

    if (!$page) {
        $page = get_page_by_path('osnove-dobrog-suhomesnatog-proizvoda');
    }

    return $page;
}

function dc_podcast_ep01_card_shortcode() {
    $page = dc_get_podcast_episode_page();

    if (!$page) {
        return '<p>Podcast epizoda nije pronađena.</p>';
    }

    $url = get_permalink($page);
    $title = get_the_title($page);

    $excerpt = get_post_meta($page->ID, '_dc_podcast_excerpt', true);
    if (!$excerpt) {
        $excerpt = 'Prva epizoda Drycured podcasta govori o temelju svake dobre šarže: mesu, soli, redoslijedu procesa i strpljenju koje se ne može preskočiti.';
    }

    $image = get_the_post_thumbnail_url($page->ID, 'large');

    ob_start();
    ?>
    <article class="dc-podcast-home-card">
        <?php if ($image): ?>
            <a class="dc-podcast-home-image-link" href="<?php echo esc_url($url); ?>">
                <img class="dc-podcast-home-image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
            </a>
        <?php endif; ?>

        <div class="dc-podcast-home-label">Novi podcast</div>

        <h3 class="dc-podcast-home-title">
            <a href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($title); ?>
            </a>
        </h3>

        <p class="dc-podcast-home-excerpt">
            <?php echo esc_html($excerpt); ?>
        </p>

        <a class="dc-podcast-home-link" href="<?php echo esc_url($url); ?>">
            Slušaj epizodu →
        </a>
    </article>
    <?php
    return ob_get_clean();
}
add_shortcode('drycured_podcast_ep01_card', 'dc_podcast_ep01_card_shortcode');

add_action('wp_head', function () {
    ?>
    <style id="drycured-podcast-card-shortcode-style">
        .dc-podcast-home-card {
            width: 100%;
        }

        .dc-podcast-home-image-link {
            display: block;
            width: 100%;
            margin-bottom: 18px;
            overflow: hidden;
            text-decoration: none !important;
        }

        .dc-podcast-home-image {
            display: block;
            width: 100%;
            height: 210px;
            object-fit: cover;
            object-position: center center;
        }

        .dc-podcast-home-label {
            margin-bottom: 8px;
            font-size: 11px;
            line-height: 1.2;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #d39a3a;
            font-weight: 800;
        }

        .dc-podcast-home-title {
            margin: 0 0 10px 0;
            font-size: 18px !important;
            line-height: 1.3 !important;
            font-weight: 600 !important;
        }

        .dc-podcast-home-title a {
            color: #1f2530 !important;
            text-decoration: none !important;
        }

        .dc-podcast-home-title a:hover {
            color: #d39a3a !important;
            text-decoration: underline !important;
        }

        .dc-podcast-home-excerpt {
            margin: 0 0 14px 0;
            font-size: 14px !important;
            line-height: 1.55 !important;
            color: #555f68 !important;
        }

        .dc-podcast-home-link {
            display: inline-flex;
            align-items: center;
            font-size: 12px !important;
            font-weight: 800 !important;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #d39a3a !important;
            text-decoration: none !important;
        }

        .dc-podcast-home-link:hover {
            text-decoration: underline !important;
        }
    </style>
    <?php
}, 10030);
