<?php
/**
 * Plugin Name: Drycured Recipe v0.6.2 Content Cleanup
 * Description: Čisti redoslijed i javni prikaz recepta HR-SL-007: proces gore, bez kartica usporedbe/povezanih recepata, dnevnik samo interaktivno.
 * Version: 0.6.2
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dcv62_recipe_content_cleanup', 1400);

function dcv62_recipe_content_cleanup($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);

    if ($code !== 'HR-SL-007' || strpos($content, 'dcv5-recipe') === false) {
        return $content;
    }

    // 1) Ukloni karticu usporedbe i povezane recepte iz glavnog sadržaja.
    $content = preg_replace('/\s*<section class="dcv5-panel dcv6-compare" id="usporedba">.*?<\/section>/s', '', $content);
    $content = preg_replace('/\s*<section class="dcv5-panel dcv6-related" id="povezani">.*?<\/section>/s', '', $content);

    // 2) Premjesti procesnu kronologiju gore, prije omjera smjese.
    if (preg_match('/(<section class="dcv5-panel" id="kronologija">.*?<\/section>)/s', $content, $match)) {
        $kronologija = $match[1];
        $content = str_replace($kronologija, '', $content);
        $content = str_replace(
            '<section class="dcv5-panel" id="omjer">',
            $kronologija . "\n" . '<section class="dcv5-panel" id="omjer">',
            $content
        );
    }

    // 3) Ukloni linkove iz bočne navigacije.
    $content = str_replace('<a href="#povezani">Povezani recepti</a>', '', $content);
    $content = str_replace('<a href="#usporedba">Usporedba</a>', '', $content);

    // 4) Dodaj prethodni/sljedeći recept nakon dnevnika šarže.
    $prevnext = dcv62_prev_next_recipe_nav();

    if ($prevnext && strpos($content, 'dcv62-prevnext') === false) {
        $content = preg_replace(
            '/(<section class="dcv5-panel" id="dnevnik">.*?<\/section>)/s',
            '$1' . "\n" . $prevnext,
            $content,
            1
        );
    }

    return $content;
}

function dcv62_recipe_link_by_slug($slug, $fallback) {
    $post = get_page_by_path($slug, OBJECT, 'dry_recipe');

    if ($post && $post->post_status === 'publish') {
        return get_permalink($post);
    }

    return home_url('/recepti-baza/' . trim($fallback, '/') . '/');
}

function dcv62_prev_next_recipe_nav() {
    $prev_url = dcv62_recipe_link_by_slug(
        'hr-sl-001-slavonski-kulen-pdo-eu',
        'hr-sl-001-slavonski-kulen-pdo-eu'
    );

    $next_url = dcv62_recipe_link_by_slug(
        'hr-sl-020-vinkovacka-sunka-suho-soljena-varijanta',
        'hr-sl-020-vinkovacka-sunka-suho-soljena-varijanta'
    );

    ob_start();
    ?>
    <nav class="dcv62-prevnext" aria-label="Navigacija između recepata">
        <a class="dcv62-prevnext-link dcv62-prev" href="<?php echo esc_url($prev_url); ?>">
            <span>← Prethodni recept</span>
            <strong>Slavonski kulen (PDO EU)</strong>
        </a>

        <a class="dcv62-prevnext-link dcv62-next" href="<?php echo esc_url($next_url); ?>">
            <span>Sljedeći recept →</span>
            <strong>Vinkovačka šunka — suho soljena varijanta</strong>
        </a>
    </nav>
    <?php
    return ob_get_clean();
}

add_action('wp_footer', 'dcv62_recipe_content_cleanup_css_js', 250000);

function dcv62_recipe_content_cleanup_css_js() {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.6.2 — završno čišćenje strukture.
         */

        body.single-dry_recipe #usporedba,
        body.single-dry_recipe #povezani,
        body.single-dry_recipe .dcv5-side-panel a[href="#usporedba"],
        body.single-dry_recipe .dcv5-side-panel a[href="#povezani"] {
            display: none !important;
        }

        /*
         * Dnevnik šarže: ostaje samo interaktivni dio.
         */
        body.single-dry_recipe #dnevnik > .dcv5-section-note,
        body.single-dry_recipe #dnevnik > .dcv5-print-strip {
            display: none !important;
        }

        body.single-dry_recipe #dnevnik .dcv6-digital-diary {
            margin-top: 0 !important;
        }

        /*
         * Procesna kronologija je sada važniji gornji blok.
         */
        body.single-dry_recipe #kronologija {
            border-width: 2px !important;
            box-shadow: 0 14px 30px rgba(25, 32, 48, .07) !important;
        }

        body.single-dry_recipe #kronologija .dcv5-timeline-item {
            background: #fffdf8 !important;
        }

        /*
         * Donja navigacija prethodni/sljedeći recept.
         * Nije kartica “povezani recepti”, nego diskretan navigacijski završetak.
         */
        body.single-dry_recipe .dcv62-prevnext {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 18px !important;
            margin: 22px 0 0 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link {
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            padding: 18px 20px !important;
            border-radius: 18px !important;
            border: 1px solid #dfc282 !important;
            background: #fffaf0 !important;
            color: #10182d !important;
            text-decoration: none !important;
            box-shadow: 0 8px 20px rgba(25, 32, 48, .045) !important;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 12px 26px rgba(25, 32, 48, .08) !important;
            background: #fff7e4 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link span {
            color: #8a733c !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: .055em !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link strong {
            color: #10182d !important;
            font-size: 17px !important;
            line-height: 1.35 !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv62-next {
            text-align: right !important;
            align-items: flex-end !important;
        }

        body.single-dry_recipe .dcv62-prev {
            text-align: left !important;
            align-items: flex-start !important;
        }

        @media (max-width: 760px) {
            body.single-dry_recipe .dcv62-prevnext {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe .dcv62-next {
                text-align: left !important;
                align-items: flex-start !important;
            }
        }

        @media print {
            body.single-dry_recipe .dcv62-prevnext {
                display: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // JS sigurnosni sloj ako je neki raniji filter već renderirao blokove.
            const compare = document.querySelector('#usporedba');
            if (compare) compare.remove();

            const related = document.querySelector('#povezani');
            if (related) related.remove();

            document.querySelectorAll('.dcv5-side-panel a[href="#usporedba"], .dcv5-side-panel a[href="#povezani"]').forEach(function (link) {
                link.remove();
            });

            const kronologija = document.querySelector('#kronologija');
            const omjer = document.querySelector('#omjer');

            if (kronologija && omjer && kronologija.compareDocumentPosition(omjer) & Node.DOCUMENT_POSITION_PRECEDING) {
                omjer.parentNode.insertBefore(kronologija, omjer);
            }

            const diary = document.querySelector('#dnevnik');
            if (diary) {
                const staticNote = diary.querySelector(':scope > .dcv5-section-note');
                const printStrip = diary.querySelector(':scope > .dcv5-print-strip');

                if (staticNote) staticNote.remove();
                if (printStrip) printStrip.remove();
            }

            // Prebroji vidljive glavne sekcije nakon promjene redoslijeda.
            const order = [
                'kronologija',
                'omjer',
                'sirovine',
                'zacini',
                'tekucine',
                'profil',
                'klima',
                'varijacije',
                'greske',
                'gotovo',
                'sigurnost',
                'posluzivanje',
                'dnevnik'
            ];

            let number = 1;
            order.forEach(function (id) {
                const section = document.getElementById(id);
                if (!section || section.offsetParent === null) return;

                const badge = section.querySelector('h2 span');
                if (badge) {
                    badge.textContent = String(number);
                    number++;
                }
            });
        });
    </script>
    <?php
}
