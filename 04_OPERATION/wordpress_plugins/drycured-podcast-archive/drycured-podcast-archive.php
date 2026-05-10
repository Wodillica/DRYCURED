<?php
/**
 * Plugin Name: Drycured Podcast Archive
 * Description: Moderna glavna podcast stranica za drycured.com.
 * Version: 1.0.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dc_podcast_get_episodes() {
    $parent = get_page_by_path('podcast');

    if (!$parent) {
        return array();
    }

    $episodes = get_posts(array(
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'post_parent'    => $parent->ID,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ));

    usort($episodes, function ($a, $b) {
        $ea = get_post_meta($a->ID, '_dc_podcast_episode', true);
        $eb = get_post_meta($b->ID, '_dc_podcast_episode', true);

        $na = intval(preg_replace('/\D+/', '', $ea));
        $nb = intval(preg_replace('/\D+/', '', $eb));

        if ($na === $nb) {
            return strtotime($a->post_date) <=> strtotime($b->post_date);
        }

        return $na <=> $nb;
    });

    return $episodes;
}

function dc_podcast_episode_number($post_id) {
    $ep = get_post_meta($post_id, '_dc_podcast_episode', true);

    if (!$ep) {
        return 'EP';
    }

    return esc_html($ep);
}

function dc_podcast_excerpt($post_id) {
    $excerpt = get_post_meta($post_id, '_dc_podcast_excerpt', true);

    if (!$excerpt) {
        $excerpt = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $post_id)), 32);
    }

    return $excerpt;
}

function dc_podcast_archive_shortcode() {
    $episodes = dc_podcast_get_episodes();

    if (empty($episodes)) {
        return '<p>Podcast epizode uskoro će biti dostupne.</p>';
    }

    $latest = end($episodes);
    reset($episodes);

    $latest_image = get_the_post_thumbnail_url($latest->ID, 'large');
    $latest_excerpt = dc_podcast_excerpt($latest->ID);

    ob_start();
    ?>
    <div class="dc-podcast-archive-shell">

        <section class="dc-podcast-archive-hero">
            <div class="dc-podcast-archive-hero-content">
                <div class="dc-podcast-kicker">DRYCURED.COM · AUDIO SERIJAL</div>
                <h1>Drycured podcast</h1>
                <p>
                    Razgovori o suhomesnatim proizvodima, tradiciji, tehnologiji, sigurnosti i praksi.
                    Znanje iz enciklopedije pretvaramo u slušljiv, topao i praktičan format.
                </p>

                <div class="dc-podcast-hero-actions">
                    <a href="<?php echo esc_url(get_permalink($latest)); ?>" class="dc-podcast-primary-button">
                        Slušaj najnoviju epizodu
                    </a>
                    <a href="/enciklopedija-znanja/" class="dc-podcast-secondary-button">
                        Enciklopedija znanja
                    </a>
                </div>
            </div>

            <div class="dc-podcast-archive-featured">
                <?php if ($latest_image): ?>
                    <a href="<?php echo esc_url(get_permalink($latest)); ?>" class="dc-podcast-featured-image-link">
                        <img src="<?php echo esc_url($latest_image); ?>" alt="<?php echo esc_attr(get_the_title($latest)); ?>" loading="lazy">
                    </a>
                <?php endif; ?>

                <div class="dc-podcast-featured-body">
                    <span><?php echo dc_podcast_episode_number($latest->ID); ?> · najnovija epizoda</span>
                    <h2>
                        <a href="<?php echo esc_url(get_permalink($latest)); ?>">
                            <?php echo esc_html(get_the_title($latest)); ?>
                        </a>
                    </h2>
                    <p><?php echo esc_html($latest_excerpt); ?></p>
                </div>
            </div>
        </section>

        <section class="dc-podcast-archive-intro">
            <div>
                <h2>Što slušatelj dobiva?</h2>
                <p>
                    Svaka epizoda obrađuje jednu praktičnu temu: od mesa, soli i crijeva, preko fermentacije i dimljenja,
                    do grešaka, kontrole kvalitete i sigurnosti hrane.
                </p>
            </div>

            <div class="dc-podcast-archive-pillbox">
                <span>Tradicija</span>
                <span>Znanost</span>
                <span>Praksa</span>
                <span>Strpljenje</span>
            </div>
        </section>

        <section class="dc-podcast-episodes-section">
            <div class="dc-podcast-section-head">
                <span>Popis epizoda</span>
                <h2>Slušaj po redu</h2>
            </div>

            <div class="dc-podcast-episodes-grid">
                <?php foreach ($episodes as $episode): ?>
                    <?php
                    $image = get_the_post_thumbnail_url($episode->ID, 'large');
                    $excerpt = dc_podcast_excerpt($episode->ID);
                    $audio_id = get_post_meta($episode->ID, '_dc_podcast_audio_id', true);
                    $has_audio = !empty($audio_id);
                    ?>
                    <article class="dc-podcast-episode-card">
                        <?php if ($image): ?>
                            <a class="dc-podcast-episode-image-link" href="<?php echo esc_url(get_permalink($episode)); ?>">
                                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(get_the_title($episode)); ?>" loading="lazy">
                            </a>
                        <?php else: ?>
                            <a class="dc-podcast-episode-image-link dc-podcast-no-image" href="<?php echo esc_url(get_permalink($episode)); ?>">
                                <span><?php echo dc_podcast_episode_number($episode->ID); ?></span>
                            </a>
                        <?php endif; ?>

                        <div class="dc-podcast-episode-body">
                            <div class="dc-podcast-episode-meta">
                                <span><?php echo dc_podcast_episode_number($episode->ID); ?></span>
                                <?php if ($has_audio): ?>
                                    <em>Audio dostupan</em>
                                <?php endif; ?>
                            </div>

                            <h3>
                                <a href="<?php echo esc_url(get_permalink($episode)); ?>">
                                    <?php echo esc_html(get_the_title($episode)); ?>
                                </a>
                            </h3>

                            <p><?php echo esc_html($excerpt); ?></p>

                            <a class="dc-podcast-card-link" href="<?php echo esc_url(get_permalink($episode)); ?>">
                                Slušaj epizodu →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dc-podcast-archive-note">
            <h2>Ritam objave</h2>
            <p>
                Podcast epizode se pripremaju kao tjedni audio nastavci. Home kartica prikazuje odabranu epizodu,
                a glavna podcast stranica služi kao trajna arhiva serijala.
            </p>
        </section>

    </div>
    <?php

    return ob_get_clean();
}

add_shortcode('drycured_podcast_archive', 'dc_podcast_archive_shortcode');

add_action('wp_head', function () {
    ?>
    <style id="drycured-podcast-archive-style">
        .dc-podcast-archive-shell {
            width: min(1180px, calc(100vw - 40px));
            margin-left: 50%;
            transform: translateX(-50%);
            padding: 46px 0 82px;
            color: #1f2530;
        }

        .dc-podcast-archive-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
            gap: 28px;
            align-items: stretch;
            padding: 38px;
            border-radius: 30px;
            background:
                radial-gradient(circle at 86% 10%, rgba(211,154,58,.18), transparent 30%),
                linear-gradient(135deg, #1f2530 0%, #2e2b27 48%, #8b6f47 100%);
            color: #fffaf0;
            box-shadow: 0 24px 64px rgba(31,37,48,.18);
        }

        .dc-podcast-kicker {
            margin-bottom: 12px;
            font-size: 12px;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: #f2c879;
            font-weight: 800;
        }

        .dc-podcast-archive-hero h1 {
            margin: 0 0 14px;
            font-size: clamp(34px, 4vw, 56px) !important;
            line-height: 1.05 !important;
            letter-spacing: -0.035em !important;
            color: #fffaf0 !important;
            font-weight: 750 !important;
        }

        .dc-podcast-archive-hero p {
            max-width: 680px;
            margin: 0;
            font-size: 18px !important;
            line-height: 1.72 !important;
            color: rgba(255,250,240,.88) !important;
        }

        .dc-podcast-hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .dc-podcast-primary-button,
        .dc-podcast-secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 0 18px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            text-decoration: none !important;
        }

        .dc-podcast-primary-button {
            background: #d39a3a;
            color: #1f2530 !important;
        }

        .dc-podcast-secondary-button {
            border: 1px solid rgba(255,255,255,.28);
            color: #fffaf0 !important;
            background: rgba(255,255,255,.08);
        }

        .dc-podcast-archive-featured {
            overflow: hidden;
            border-radius: 24px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(10px);
        }

        .dc-podcast-featured-image-link {
            display: block;
            overflow: hidden;
        }

        .dc-podcast-featured-image-link img {
            display: block;
            width: 100%;
            height: 260px;
            object-fit: cover;
        }

        .dc-podcast-featured-body {
            padding: 22px 24px 24px;
        }

        .dc-podcast-featured-body span,
        .dc-podcast-section-head span {
            display: block;
            margin-bottom: 8px;
            font-size: 11px;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #d39a3a;
            font-weight: 800;
        }

        .dc-podcast-featured-body h2 {
            margin: 0 0 10px;
            font-size: 24px !important;
            line-height: 1.25 !important;
            color: #fffaf0 !important;
        }

        .dc-podcast-featured-body h2 a {
            color: #fffaf0 !important;
            text-decoration: none !important;
        }

        .dc-podcast-featured-body p {
            font-size: 15px !important;
            line-height: 1.62 !important;
            color: rgba(255,250,240,.82) !important;
        }

        .dc-podcast-archive-intro {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 22px;
            margin: 26px 0;
            padding: 26px 30px;
            border-radius: 24px;
            background: #fffaf0;
            border: 1px solid rgba(139,111,71,.16);
            box-shadow: 0 12px 30px rgba(31,37,48,.06);
        }

        .dc-podcast-archive-intro h2,
        .dc-podcast-section-head h2,
        .dc-podcast-archive-note h2 {
            margin: 0 0 10px;
            font-size: 26px !important;
            line-height: 1.25 !important;
            font-weight: 650 !important;
            color: #1f2530 !important;
        }

        .dc-podcast-archive-intro p,
        .dc-podcast-archive-note p {
            margin: 0;
            font-size: 16px !important;
            line-height: 1.72 !important;
            color: #56616d !important;
        }

        .dc-podcast-archive-pillbox {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-content: center;
            justify-content: flex-end;
        }

        .dc-podcast-archive-pillbox span {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 0 14px;
            border-radius: 999px;
            background: rgba(211,154,58,.12);
            color: #8b6f47;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .dc-podcast-section-head {
            margin: 40px 0 18px;
        }

        .dc-podcast-episodes-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 22px;
        }

        .dc-podcast-episode-card {
            overflow: hidden;
            border-radius: 24px;
            background: #fffaf0;
            border: 1px solid rgba(139,111,71,.16);
            box-shadow: 0 14px 34px rgba(31,37,48,.07);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .dc-podcast-episode-card:hover {
            transform: translateY(-3px);
            border-color: rgba(211,154,58,.45);
            box-shadow: 0 20px 42px rgba(31,37,48,.11);
        }

        .dc-podcast-episode-image-link {
            display: block;
            width: 100%;
            height: 190px;
            overflow: hidden;
            background: #1f2530;
            text-decoration: none !important;
        }

        .dc-podcast-episode-image-link img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .dc-podcast-no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                radial-gradient(circle at 80% 20%, rgba(211,154,58,.22), transparent 34%),
                linear-gradient(135deg, #1f2530, #3b3024);
        }

        .dc-podcast-no-image span {
            color: #d39a3a;
            font-size: 42px;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .dc-podcast-episode-body {
            padding: 22px 22px 24px;
        }

        .dc-podcast-episode-meta {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            font-weight: 800;
            color: #d39a3a;
        }

        .dc-podcast-episode-meta em {
            font-style: normal;
            color: #8b6f47;
        }

        .dc-podcast-episode-card h3 {
            margin: 0 0 10px;
            font-size: 20px !important;
            line-height: 1.32 !important;
            font-weight: 650 !important;
            color: #1f2530 !important;
        }

        .dc-podcast-episode-card h3 a {
            color: #1f2530 !important;
            text-decoration: none !important;
        }

        .dc-podcast-episode-card h3 a:hover {
            color: #d39a3a !important;
            text-decoration: underline !important;
        }

        .dc-podcast-episode-card p {
            margin: 0 0 16px;
            font-size: 15px !important;
            line-height: 1.62 !important;
            color: #56616d !important;
        }

        .dc-podcast-card-link {
            display: inline-flex;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
            color: #d39a3a !important;
            text-decoration: none !important;
        }

        .dc-podcast-card-link:hover {
            text-decoration: underline !important;
        }

        .dc-podcast-archive-note {
            margin-top: 30px;
            padding: 26px 30px;
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(139,111,71,.14);
            box-shadow: 0 10px 26px rgba(31,37,48,.05);
        }

        @media (max-width: 1024px) {
            .dc-podcast-archive-hero,
            .dc-podcast-archive-intro {
                grid-template-columns: 1fr;
            }

            .dc-podcast-episodes-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dc-podcast-archive-pillbox {
                justify-content: flex-start;
            }
        }

        @media (max-width: 700px) {
            .dc-podcast-archive-shell {
                width: min(100%, calc(100vw - 28px));
                padding-top: 26px;
            }

            .dc-podcast-archive-hero {
                padding: 26px;
                border-radius: 24px;
            }

            .dc-podcast-episodes-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}, 10030);
