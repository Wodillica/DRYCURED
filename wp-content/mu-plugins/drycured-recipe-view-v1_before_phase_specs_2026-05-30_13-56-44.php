<?php
/**
 * Plugin Name: Drycured Recipe View v1.0
 * Description: Produkcijski konsolidirani prikaz kanonskih drycured recepata s početnom v1.1 podrškom za HR-SL-001, HR-SL-007 i HR-SL-020. Nastao spajanjem potvrđenog HR-SL-007 pilot prikaza v0.5/v0.6.
 * Version: 1.1.0
 * Author: drycured.com
 *
 * Napomena:
 * Ova datoteka je funkcionalna konsolidacija prethodnih MU-plugin slojeva.
 * Stare datoteke se nakon uspješnog audita premještaju u _archive_recipe_view_v1_*.
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * ============================================================
 * DRYCURED RECIPE VIEW v1.0 — CONSOLIDATED FUNCTIONAL MIRROR
 * ============================================================
 *
 * Redoslijed blokova prati stabilno testirano stanje iz audita:
 * - osnovni kanonski renderer
 * - pilot HR-SL-007 renderer
 * - clarity/layout/card/safety/sensory polish
 * - v0.6 feature pack
 * - content cleanup
 * - header restore
 * - prev/next position
 * - navigation polish
 */


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-canonical-recipe-tuning.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Canonical Recipe Tuning
 * Description: Dodatna dorada javnog prikaza kanonskih recepata.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        @media (min-width: 641px) {
            .single-dry_recipe .dc-canon-table-wrap {
                overflow-x: visible !important;
            }

            .single-dry_recipe .dc-canon-table {
                min-width: 0 !important;
                width: 100% !important;
                table-layout: fixed !important;
            }

            .single-dry_recipe .dc-canon-table th,
            .single-dry_recipe .dc-canon-table td {
                white-space: normal !important;
                overflow-wrap: break-word !important;
                word-break: normal !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(1),
            .single-dry_recipe .dc-canon-table td:nth-child(1) {
                width: 36% !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(2),
            .single-dry_recipe .dc-canon-table td:nth-child(2) {
                width: 22% !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(3),
            .single-dry_recipe .dc-canon-table td:nth-child(3) {
                width: 42% !important;
            }
        }

        .single-dry_recipe .dc-canon-admin {
            display: none !important;
        }

        .single-dry_recipe .dc-canon-section p,
        .single-dry_recipe .dc-canon-list li,
        .single-dry_recipe .dc-canon-table td {
            font-size: 16px !important;
            line-height: 1.65 !important;
        }

        .single-dry_recipe .dc-canon-section {
            margin-bottom: 22px !important;
        }
    </style>
    <?php
}, 999);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-canonical-recipe-view.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Canonical Recipe View
 * Description: Sigurni override javnog prikaza kanonskih dry_recipe recepata bez diranja core plugina.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dc_canon_recipe_override_content', 999);

function dc_canon_recipe_override_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    if (!$post_id) {
        return $content;
    }

    $markdown = get_post_meta($post_id, '_dry_recipe_full_markdown', true);
    if (!$markdown) {
        return $content;
    }

    $sections = dc_canon_recipe_parse_sections($markdown);

    $code        = dc_canon_meta($post_id, '_dry_recipe_id');
    $country     = dc_canon_meta($post_id, '_dry_country', dc_canon_first_term($post_id, 'dry_country', 'Hrvatska'));
    $region      = dc_canon_meta($post_id, '_dry_region', dc_canon_first_term($post_id, 'dry_region', ''));
    $microregion = dc_canon_meta($post_id, '_dry_microregion', dc_canon_first_term($post_id, 'dry_microregion', ''));
    $type        = dc_canon_meta($post_id, '_dry_product_type', dc_canon_first_term($post_id, 'dry_product_type', ''));
    $category    = dc_canon_meta($post_id, '_dry_category', dc_canon_first_term($post_id, 'dry_product_category', ''));

    $lead = '';
    if (isset($sections[2])) {
        $lead = wp_strip_all_tags(dc_canon_recipe_render_body($sections[2]['body']));
    }

    ob_start();
    ?>
    <style>
        .single-dry_recipe .ast-container,
        .single-dry_recipe .site-content .ast-container,
        .single-dry_recipe .content-area,
        .single-dry_recipe main.site-main {
            max-width: 1280px !important;
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .single-dry_recipe .entry-header,
        .single-dry_recipe .entry-meta {
            display: none !important;
        }

        .dc-canon-recipe {
            max-width: 1160px;
            margin: 0 auto;
            padding: 36px 18px 58px;
            color: #142039;
        }

        .dc-canon-hero,
        .dc-canon-card,
        .dc-canon-section,
        .dc-canon-toc {
            background: #fffaf0;
            border: 1px solid #e2c98e;
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(25, 32, 48, .08);
        }

        .dc-canon-hero {
            padding: 30px 34px;
            margin-bottom: 18px;
        }

        .dc-canon-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .dc-canon-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: #f2dfb5;
            border: 1px solid #d5b46b;
            color: #10182d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .dc-canon-hero h1 {
            margin: 0 0 12px;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1.08;
            color: #0f1930;
        }

        .dc-canon-lead {
            max-width: 860px;
            margin: 0;
            color: #42506a;
            font-size: 17px;
            line-height: 1.65;
        }

        .dc-canon-meta-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 22px;
        }

        .dc-canon-card {
            padding: 15px 17px;
        }

        .dc-canon-card small {
            display: block;
            margin-bottom: 5px;
            color: #766843;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dc-canon-card strong {
            display: block;
            color: #142039;
            font-size: 15px;
            line-height: 1.35;
        }

        .dc-canon-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 285px;
            gap: 22px;
            align-items: start;
        }

        .dc-canon-section {
            padding: 24px 28px;
            margin-bottom: 18px;
        }

        .dc-canon-section h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            color: #111b33;
            font-size: 22px;
            line-height: 1.25;
        }

        .dc-canon-section h2 span {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #d8a63f;
            color: #10182c;
            font-size: 14px;
            font-weight: 900;
        }

        .dc-canon-section p {
            margin: 0 0 12px;
            color: #33415c;
            font-size: 16px;
            line-height: 1.7;
        }

        .dc-canon-list {
            margin: 0 0 14px;
            padding: 0;
            list-style: none;
        }

        .dc-canon-list li {
            position: relative;
            margin: 8px 0;
            padding: 10px 12px 10px 34px;
            border: 1px solid #ecd9aa;
            border-radius: 12px;
            background: #fffdf7;
            line-height: 1.55;
        }

        .dc-canon-list li::before {
            content: "•";
            position: absolute;
            left: 14px;
            top: 9px;
            color: #d8a63f;
            font-weight: 900;
        }

        .dc-canon-table-wrap {
            width: 100%;
            overflow-x: auto;
            margin: 12px 0 18px;
            border: 1px solid #e4c889;
            border-radius: 14px;
            background: #fff;
        }

        .dc-canon-table {
            width: 100%;
            min-width: 680px;
            border-collapse: collapse;
        }

        .dc-canon-table th {
            padding: 12px 14px;
            text-align: left;
            background: #d8a63f;
            color: #10182c;
            font-size: 14px;
            font-weight: 900;
        }

        .dc-canon-table td {
            padding: 12px 14px;
            border-top: 1px solid #ecd9aa;
            color: #26344f;
            font-size: 15px;
            line-height: 1.55;
            vertical-align: top;
        }

        .dc-canon-toc {
            position: sticky;
            top: 94px;
            padding: 16px;
        }

        .dc-canon-toc h3 {
            margin: 0 0 12px;
            color: #111b33;
            font-size: 15px;
        }

        .dc-canon-toc a {
            display: block;
            padding: 8px 10px;
            border-radius: 10px;
            color: #26385c;
            text-decoration: none;
            font-size: 14px;
        }

        .dc-canon-toc a:hover {
            background: #f3e3bd;
        }

        .dc-canon-admin {
            border-style: dashed;
            opacity: .96;
        }

        @media (max-width: 980px) {
            .dc-canon-layout {
                grid-template-columns: 1fr;
            }

            .dc-canon-toc {
                position: static;
            }

            .dc-canon-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .dc-canon-recipe {
                padding: 24px 12px 42px;
            }

            .dc-canon-hero,
            .dc-canon-section {
                padding: 20px 18px;
                border-radius: 14px;
            }

            .dc-canon-meta-grid {
                grid-template-columns: 1fr;
            }

            .dc-canon-table {
                min-width: 560px;
            }
        }
    </style>

    <div class="dc-canon-recipe">
        <header class="dc-canon-hero">
            <div class="dc-canon-badges">
                <span class="dc-canon-badge">RECEPT</span>
                <?php if ($code) : ?><span class="dc-canon-badge"><?php echo esc_html($code); ?></span><?php endif; ?>
                <?php if ($type) : ?><span class="dc-canon-badge"><?php echo esc_html($type); ?></span><?php endif; ?>
                <?php if ($region) : ?><span class="dc-canon-badge"><?php echo esc_html($region); ?></span><?php endif; ?>
            </div>

            <h1><?php echo esc_html(get_the_title($post_id)); ?></h1>

            <?php if ($lead) : ?>
                <p class="dc-canon-lead"><?php echo esc_html($lead); ?></p>
            <?php endif; ?>
        </header>

        <div class="dc-canon-meta-grid">
            <div class="dc-canon-card"><small>Država</small><strong><?php echo esc_html($country ?: '—'); ?></strong></div>
            <div class="dc-canon-card"><small>Regija</small><strong><?php echo esc_html($region ?: '—'); ?></strong></div>
            <div class="dc-canon-card"><small>Mikroregija</small><strong><?php echo esc_html($microregion ?: '—'); ?></strong></div>
            <div class="dc-canon-card"><small>Vrsta proizvoda</small><strong><?php echo esc_html($type ?: $category ?: '—'); ?></strong></div>
        </div>

        <div class="dc-canon-layout">
            <main class="dc-canon-main">
                <?php
                echo dc_canon_recipe_section($sections, 5, 'Mesni sastav i anatomski dijelovi');
                echo dc_canon_recipe_section($sections, 6, 'Sastojci i začini');
                echo dc_canon_recipe_section($sections, 7, 'Crijeva / omotači');
                echo dc_canon_recipe_section($sections, 8, 'Češnjak');
                echo dc_canon_recipe_section($sections, 9, 'Oprema i alati');
                echo dc_canon_recipe_section($sections, 10, 'Priprema mesa');
                echo dc_canon_recipe_section($sections, 11, 'Mljevenje / rezanje');
                echo dc_canon_recipe_section($sections, 12, 'Miješanje i odležavanje');
                echo dc_canon_recipe_section($sections, 13, 'Punjenje i vezanje');
                echo dc_canon_recipe_section($sections, 14, 'Predsušenje / početna fermentacija');
                echo dc_canon_recipe_section($sections, 15, 'Dimljenje');
                echo dc_canon_recipe_section($sections, 16, 'Sušenje i zrenje');
                echo dc_canon_recipe_section($sections, 17, 'Najčešće greške i rješenja');
                echo dc_canon_recipe_section($sections, 18, 'Posluživanje i čuvanje');
                echo dc_canon_recipe_section($sections, 3, 'Regionalni identitet');

                if (current_user_can('edit_posts')) {
                    echo '<section class="dc-canon-section dc-canon-admin"><h2><span>i</span>Urednički podaci</h2>';
                    echo dc_canon_recipe_section($sections, 1, 'Identitet recepta');
                    echo dc_canon_recipe_section($sections, 19, 'Kalkulator status');
                    echo dc_canon_recipe_section($sections, 20, 'Sljedivost izvora');
                    echo '</section>';
                }
                ?>
            </main>

            <aside>
                <nav class="dc-canon-toc" aria-label="Sadržaj recepta">
                    <h3>Sadržaj recepta</h3>
                    <a href="#dc-section-5">Mesni sastav</a>
                    <a href="#dc-section-6">Sastojci i začini</a>
                    <a href="#dc-section-7">Crijeva / omotači</a>
                    <a href="#dc-section-8">Češnjak</a>
                    <a href="#dc-section-10">Priprema</a>
                    <a href="#dc-section-15">Dimljenje</a>
                    <a href="#dc-section-16">Sušenje i zrenje</a>
                    <a href="#dc-section-17">Greške i rješenja</a>
                    <a href="#dc-section-18">Posluživanje</a>
                </nav>
            </aside>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function dc_canon_meta($post_id, $key, $fallback = '') {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $fallback;
}

function dc_canon_first_term($post_id, $tax, $fallback = '') {
    $terms = get_the_terms($post_id, $tax);
    if (!empty($terms) && !is_wp_error($terms)) {
        return $terms[0]->name;
    }
    return $fallback;
}

function dc_canon_recipe_parse_sections($markdown) {
    $lines = preg_split('/\R/u', (string) $markdown);
    $sections = [];
    $current = null;

    foreach ($lines as $line) {
        if (preg_match('/^##\s+(\d+)\.\s*(.+)$/u', $line, $m)) {
            if ($current) {
                $sections[(int) $current['num']] = $current;
            }

            $current = [
                'num' => (int) $m[1],
                'title' => trim($m[2]),
                'body' => [],
            ];
            continue;
        }

        if ($current) {
            $current['body'][] = $line;
        }
    }

    if ($current) {
        $sections[(int) $current['num']] = $current;
    }

    return $sections;
}

function dc_canon_recipe_section($sections, $num, $title = '') {
    if (empty($sections[$num])) {
        return '';
    }

    $heading = $title ?: $sections[$num]['title'];
    $body = $sections[$num]['body'];

    return '<section class="dc-canon-section" id="dc-section-' . esc_attr($num) . '">' .
        '<h2><span>' . esc_html($num) . '</span>' . esc_html($heading) . '</h2>' .
        dc_canon_recipe_render_body($body) .
        '</section>';
}

function dc_canon_recipe_render_body($body_lines) {
    $html = '';
    $table_lines = [];
    $list_open = false;

    $flush_table = function() use (&$html, &$table_lines) {
        if (!empty($table_lines)) {
            $html .= dc_canon_recipe_render_table($table_lines);
            $table_lines = [];
        }
    };

    $close_list = function() use (&$html, &$list_open) {
        if ($list_open) {
            $html .= '</ul>';
            $list_open = false;
        }
    };

    foreach ((array) $body_lines as $line) {
        $trim = trim((string) $line);

        if ($trim === '' || $trim === '---') {
            $flush_table();
            $close_list();
            continue;
        }

        if (preg_match('/^#{1,6}\s+/u', $trim)) {
            continue;
        }

        if (strpos($trim, '|') === 0) {
            $close_list();
            $table_lines[] = $trim;
            continue;
        }

        $flush_table();

        if (preg_match('/^-\s+(.+)$/u', $trim, $m)) {
            if (!$list_open) {
                $html .= '<ul class="dc-canon-list">';
                $list_open = true;
            }

            $html .= '<li>' . dc_canon_recipe_inline($m[1]) . '</li>';
            continue;
        }

        $close_list();
        $html .= '<p>' . dc_canon_recipe_inline($trim) . '</p>';
    }

    $flush_table();
    $close_list();

    return $html;
}

function dc_canon_recipe_render_table($lines) {
    $rows = [];

    foreach ($lines as $line) {
        $cells = array_map('trim', explode('|', trim($line, '|')));

        if (count($cells) < 2) {
            continue;
        }

        $separator = true;
        foreach ($cells as $cell) {
            if (!preg_match('/^:?-{3,}:?$/', $cell)) {
                $separator = false;
                break;
            }
        }

        if (!$separator) {
            $rows[] = $cells;
        }
    }

    if (count($rows) <= 1) {
        return '';
    }

    // Prvi red je zaglavlje iz Markdown tablice. U javnom prikazu ga koristimo
    // samo kao semantičku pomoć, ali ne renderiramo klasičnu HTML tablicu.
    $headers = array_shift($rows);

    $html = '<div class="dc-canon-data-list">';

    foreach ($rows as $cells) {
        $label = $cells[0] ?? '';
        $value = $cells[1] ?? '';
        $note  = $cells[2] ?? '';

        $html .= '<div class="dc-canon-data-row">';

        $html .= '<div class="dc-canon-data-main">';
        $html .= '<span class="dc-canon-data-kicker">' . esc_html($headers[0] ?? 'Stavka') . '</span>';
        $html .= '<strong>' . dc_canon_recipe_inline($label) . '</strong>';
        $html .= '</div>';

        $html .= '<div class="dc-canon-data-amount">';
        $html .= '<span class="dc-canon-data-kicker">' . esc_html($headers[1] ?? 'Količina') . '</span>';
        $html .= '<strong>' . dc_canon_recipe_inline($value) . '</strong>';
        $html .= '</div>';

        if ($note !== '') {
            $html .= '<div class="dc-canon-data-note">';
            $html .= '<span class="dc-canon-data-kicker">' . esc_html($headers[2] ?? 'Napomena') . '</span>';
            $html .= '<p>' . dc_canon_recipe_inline($note) . '</p>';
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

function dc_canon_recipe_inline($text) {
    $safe = esc_html(trim((string) $text));
    $safe = preg_replace('/\*\*(.*?)\*\*/u', '<strong>$1</strong>', $safe);
    return $safe;
}


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-pilot.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5 Pilot
 * Description: Pilot prikaz recepta kao proizvodni vodič za HR-SL-007.
 * Version: 0.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dcv5_recipe_view_pilot_content', 1200);

function dcv5_recipe_view_pilot_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);

    if (!in_array($code, dcv5_supported_recipe_codes(), true)) {
        return $content;
    }

    $recipe = dcv5_get_recipe_profile($post_id, $code);
    if (!$recipe) {
        return $content;
    }

    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    if (!$image_url) {
        $image_url = get_post_meta($post_id, '_dry_recipe_image_url', true);
    }

    $calculator_url = add_query_arg(
        ['recipe' => $recipe['code']],
        home_url('/kalkulator/')
    );

    ob_start();
    dcv5_render_recipe_schema($recipe);
    ?>
    <style>
        .single-dry_recipe .entry-header,
        .single-dry_recipe .entry-meta {
            display: none !important;
        }

        .single-dry_recipe .ast-container,
        .single-dry_recipe .site-content .ast-container,
        .single-dry_recipe .content-area,
        .single-dry_recipe main.site-main {
            max-width: 1340px !important;
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .dcv5-recipe {
            max-width: 1220px;
            margin: 0 auto;
            padding: 34px 18px 64px;
            color: #111b33;
        }

        .dcv5-hero,
        .dcv5-panel,
        .dcv5-side-panel {
            background: #fffaf0;
            border: 1px solid #e2c98e;
            border-radius: 22px;
            box-shadow: 0 14px 34px rgba(25, 32, 48, .08);
        }

        .dcv5-hero {
            padding: 30px 34px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
        }

        .dcv5-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 26px;
            align-items: center;
        }

        .dcv5-hero-media {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2c98e;
            background: #f5e7c5;
            box-shadow: 0 12px 26px rgba(25, 32, 48, .10);
        }

        .dcv5-hero-media img {
            display: block;
            width: 100%;
            height: 280px;
            object-fit: cover;
        }

        .dcv5-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .dcv5-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 15px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 900;
            border: 1px solid #d5b46b;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .dcv5-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(25, 32, 48, .12);
        }

        .dcv5-btn-primary {
            background: #111b33;
            color: #fffaf0;
            border-color: #111b33;
        }

        .dcv5-btn-secondary {
            background: #f1dfb6;
            color: #111b33;
        }

        .dcv5-hero:before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(216,166,63,.18), transparent 36%);
            pointer-events: none;
        }

        .dcv5-hero-inner {
            position: relative;
            z-index: 1;
        }

        .dcv5-kicker {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .dcv5-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            color: #10182d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .dcv5-hero h1 {
            margin: 0 0 12px;
            font-size: clamp(34px, 4.5vw, 56px);
            line-height: 1.04;
            color: #0d172d;
        }

        .dcv5-lead {
            max-width: 880px;
            margin: 0;
            color: #3e4a63;
            font-size: 17px;
            line-height: 1.7;
        }

        .dcv5-quick-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin: 18px 0 24px;
        }

        .dcv5-quick-card {
            background: #fffdf7;
            border: 1px solid #e6cf97;
            border-radius: 16px;
            padding: 13px 14px;
        }

        .dcv5-quick-card span,
        .dcv5-field-label {
            display: block;
            margin-bottom: 5px;
            color: #7d6a3c;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dcv5-quick-card strong {
            display: block;
            color: #111b33;
            font-size: 15px;
            line-height: 1.35;
        }

        .dcv5-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 22px;
            align-items: start;
        }

        .dcv5-panel {
            padding: 24px 28px;
            margin-bottom: 20px;
        }

        .dcv5-panel h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            font-size: 24px;
            line-height: 1.2;
            color: #111b33;
        }

        .dcv5-panel h2 span {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #d8a63f;
            color: #10182c;
            font-size: 14px;
            font-weight: 900;
        }

        .dcv5-section-note {
            margin: -4px 0 18px;
            color: #4a566e;
            font-size: 15px;
            line-height: 1.6;
        }

        .dcv5-composition {
            display: grid;
            grid-template-columns: 7fr 3fr;
            gap: 8px;
            margin: 12px 0 4px;
        }

        .dcv5-comp-bar {
            min-height: 46px;
            border-radius: 14px;
            padding: 12px 14px;
            background: #d8a63f;
            color: #10182c;
            font-weight: 900;
        }

        .dcv5-comp-bar:nth-child(2) {
            background: #f0d99a;
        }

        .dcv5-card-grid {
            display: grid;
            gap: 12px;
        }

        .dcv5-card-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcv5-card-grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dcv5-ingredient-card,
        .dcv5-process-card,
        .dcv5-error-card,
        .dcv5-serving-card,
        .dcv5-profile-row,
        .dcv5-climate-card {
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-ingredient-card h3,
        .dcv5-process-card h3,
        .dcv5-error-card h3,
        .dcv5-serving-card h3,
        .dcv5-climate-card h3 {
            margin: 0 0 9px;
            color: #111b33;
            font-size: 17px;
            line-height: 1.3;
        }

        .dcv5-amount-line {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
        }

        .dcv5-amount {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            background: #111b33;
            color: #fffaf0;
            font-weight: 900;
            font-size: 14px;
        }

        .dcv5-percent,
        .dcv5-rate {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            color: #111b33;
            font-weight: 800;
            font-size: 13px;
        }

        .dcv5-ingredient-card p,
        .dcv5-process-card p,
        .dcv5-error-card p,
        .dcv5-serving-card p,
        .dcv5-climate-card p {
            margin: 0;
            color: #3b4861;
            font-size: 15.5px;
            line-height: 1.65;
        }

        .dcv5-profile-row {
            display: grid;
            grid-template-columns: 125px minmax(0, 1fr) 44px;
            gap: 10px;
            align-items: center;
            margin-bottom: 9px;
        }

        .dcv5-profile-name {
            font-weight: 800;
            color: #111b33;
            font-size: 14px;
        }

        .dcv5-profile-track {
            height: 10px;
            border-radius: 999px;
            background: #efe2c3;
            overflow: hidden;
        }

        .dcv5-profile-fill {
            height: 100%;
            border-radius: 999px;
            background: #d8a63f;
        }

        .dcv5-profile-score {
            font-size: 13px;
            font-weight: 900;
            color: #111b33;
        }

        .dcv5-timeline {
            position: relative;
            display: grid;
            gap: 12px;
        }

        .dcv5-timeline-item {
            display: grid;
            grid-template-columns: 104px minmax(0, 1fr);
            gap: 14px;
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 14px;
            background: #d8a63f;
            color: #10182c;
            font-weight: 900;
            text-align: center;
            padding: 8px;
        }

        .dcv5-critical {
            margin-top: 10px;
            padding: 10px 12px;
            border-left: 4px solid #d8a63f;
            background: #fff6dd;
            border-radius: 10px;
            color: #3a455d;
            font-size: 14px;
            line-height: 1.55;
        }

        .dcv5-error-card {
            border-left: 5px solid #d8a63f;
        }

        .dcv5-error-card.danger {
            border-left-color: #9f2f2f;
        }

        .dcv5-error-card.warning {
            border-left-color: #d8a63f;
        }

        .dcv5-error-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 8px 0 10px;
        }

        .dcv5-small-pill {
            display: inline-flex;
            padding: 5px 8px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            font-size: 12px;
            font-weight: 800;
            color: #111b33;
        }

        .dcv5-side-panel {
            position: sticky;
            top: 96px;
            padding: 16px;
        }

        .dcv5-side-panel h3 {
            margin: 0 0 12px;
            color: #111b33;
            font-size: 15px;
        }

        .dcv5-side-panel a {
            display: block;
            padding: 8px 10px;
            border-radius: 10px;
            color: #26385c;
            text-decoration: none;
            font-size: 14px;
        }

        .dcv5-side-panel a:hover {
            background: #f3e3bd;
        }

        .dcv5-print-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .dcv5-check-grid,
        .dcv5-safety-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .dcv5-check-card,
        .dcv5-safety-card {
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-check-card h3,
        .dcv5-safety-card h3 {
            margin: 0 0 8px;
            font-size: 16px;
            color: #111b33;
            line-height: 1.3;
        }

        .dcv5-check-card p,
        .dcv5-safety-card p {
            margin: 0;
            color: #3b4861;
            font-size: 15px;
            line-height: 1.6;
        }

        .dcv5-safety-card.green {
            border-left: 6px solid #4b8f5a;
        }

        .dcv5-safety-card.yellow {
            border-left: 6px solid #d8a63f;
        }

        .dcv5-safety-card.red {
            border-left: 6px solid #a83a3a;
        }

        .dcv5-print-button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px 0 0;
        }

        @media print {
            header.site-header,
            footer.site-footer,
            .dcv5-side-panel,
            .dcv5-hero-media,
            .dcv5-actions,
            #profil,
            #klima {
                display: none !important;
            }

            .dcv5-recipe {
                max-width: none !important;
                padding: 0 !important;
            }

            .dcv5-layout {
                display: block !important;
            }

            .dcv5-panel,
            .dcv5-hero,
            .dcv5-quick-card {
                box-shadow: none !important;
                break-inside: avoid;
            }

            body {
                background: #fff !important;
            }
        }

        .dcv5-print-box {
            min-height: 64px;
            border: 1px dashed #d5b46b;
            border-radius: 14px;
            padding: 10px;
            background: #fffdf7;
        }

        @media (max-width: 980px) {
            .dcv5-hero-grid {
                grid-template-columns: 1fr;
            }

            .dcv5-hero-media img {
                height: 240px;
            }

            .dcv5-layout {
                grid-template-columns: 1fr;
            }

            .dcv5-side-panel {
                position: static;
            }

            .dcv5-quick-strip,
            .dcv5-card-grid.three,
            .dcv5-card-grid.two,
            .dcv5-print-strip,
            .dcv5-check-grid,
            .dcv5-safety-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .dcv5-recipe {
                padding: 24px 12px 42px;
            }

            .dcv5-hero,
            .dcv5-panel {
                padding: 20px 18px;
                border-radius: 16px;
            }

            .dcv5-quick-strip,
            .dcv5-card-grid.three,
            .dcv5-card-grid.two,
            .dcv5-print-strip,
            .dcv5-check-grid,
            .dcv5-safety-grid {
                grid-template-columns: 1fr;
            }

            .dcv5-timeline-item {
                grid-template-columns: 1fr;
            }

            .dcv5-profile-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dcv5-recipe">
        <header class="dcv5-hero" id="vrh">
            <div class="dcv5-hero-grid">
                <div class="dcv5-hero-inner">
                    <div class="dcv5-kicker">
                        <span class="dcv5-badge">DIGITALNA PUŠNICA</span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['code']); ?></span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['region']); ?></span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['type']); ?></span>
                    </div>

                    <h1><?php echo esc_html($recipe['title']); ?></h1>
                    <p class="dcv5-lead"><?php echo esc_html($recipe['lead']); ?></p>

                    <div class="dcv5-actions">
                        <a class="dcv5-btn dcv5-btn-primary" href="<?php echo esc_url($calculator_url); ?>">
                            Otvori kalkulator za ovaj recept
                        </a>
                        <a class="dcv5-btn dcv5-btn-secondary" href="#dnevnik">
                            Dnevnik šarže
                        </a>
                        <a class="dcv5-btn dcv5-btn-secondary" href="javascript:window.print()">
                            Ispiši radnu verziju
                        </a>
                    </div>
                </div>

                <?php if ($image_url) : ?>
                    <figure class="dcv5-hero-media">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($recipe['title']); ?>">
                    </figure>
                <?php endif; ?>
            </div>
        </header>

        <section class="dcv5-quick-strip" aria-label="Brzi proizvodni sažetak">
            <?php foreach ($recipe['quick'] as $item) : ?>
                <article class="dcv5-quick-card">
                    <span><?php echo esc_html($item['label']); ?></span>
                    <strong><?php echo esc_html($item['value']); ?></strong>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="dcv5-layout">
            <main>
                <section class="dcv5-panel" id="omjer">
                    <h2><span>1</span>Omjer smjese</h2>
                    <p class="dcv5-section-note">Brzi pregled odnosa glavnih sirovina u šarži od 10 kg. Ovaj omjer čuva sočnost, ali i omogućuje stabilno sušenje.</p>

                    <div class="dcv5-composition">
                        <div class="dcv5-comp-bar">70 % meso</div>
                        <div class="dcv5-comp-bar">30 % slanina</div>
                    </div>
                </section>

                <section class="dcv5-panel" id="sirovine">
                    <h2><span>2</span>Glavne sirovine</h2>
                    <p class="dcv5-section-note">Meso i slanina prikazuju se u kilogramima jer čine osnovu smjese.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['materials'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="zacini">
                    <h2><span>3</span>Začini i dodaci</h2>
                    <p class="dcv5-section-note">Začini se prikazuju u gramima, uz postotak i g/kg gdje je korisno. Time korisnik dobiva i radnu vrijednost i tehnološki omjer.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['spices'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="tekucine">
                    <h2><span>4</span>Tekućine i češnjak</h2>
                    <p class="dcv5-section-note">Tekućine se prikazuju u litrama. Češnjak se ne dodaje kao komadić, nego kao procijeđena aromatična tekućina.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['liquids'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="profil">
                    <h2><span>5</span>Profil proizvoda</h2>
                    <p class="dcv5-section-note">Senzorni profil pomaže korisniku da odmah razumije karakter proizvoda prije nego krene u izradu.</p>

                    <?php foreach ($recipe['profile'] as $item) : ?>
                        <div class="dcv5-profile-row">
                            <div class="dcv5-profile-name"><?php echo esc_html($item['name']); ?></div>
                            <div class="dcv5-profile-track"><div class="dcv5-profile-fill" style="width: <?php echo esc_attr($item['score'] * 10); ?>%"></div></div>
                            <div class="dcv5-profile-score"><?php echo esc_html($item['score']); ?>/10</div>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="dcv5-panel" id="klima">
                    <h2><span>6</span>Klimatski i tehnološki potpis</h2>
                    <p class="dcv5-section-note">Uvjeti prostora važni su koliko i začini. Dobra kobasica nastaje iz ritma hladnoće, vlage, dima i vremena.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['climate'] as $item) : ?>
                            <article class="dcv5-climate-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="kronologija">
                    <h2><span>7</span>Procesna kronologija</h2>
                    <p class="dcv5-section-note">Ovo je radni vodič od sirovine do stola. Svaka faza ima cilj i kritičnu točku kontrole.</p>

                    <div class="dcv5-timeline">
                        <?php foreach ($recipe['timeline'] as $step) : ?>
                            <article class="dcv5-timeline-item">
                                <div class="dcv5-day"><?php echo esc_html($step['day']); ?></div>
                                <div>
                                    <h3><?php echo esc_html($step['title']); ?></h3>
                                    <p><?php echo esc_html($step['text']); ?></p>
                                    <div class="dcv5-critical"><strong>Kritično:</strong> <?php echo esc_html($step['critical']); ?></div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="greske">
                    <h2><span>8</span>Anatomija greške</h2>
                    <p class="dcv5-section-note">Svaki problem mora imati konkretno rješenje. Ovo je zaštitni dio recepta.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['errors'] as $error) : ?>
                            <article class="dcv5-error-card <?php echo esc_attr($error['level']); ?>">
                                <h3><?php echo esc_html($error['problem']); ?></h3>
                                <div class="dcv5-error-meta">
                                    <span class="dcv5-small-pill"><?php echo esc_html($error['phase']); ?></span>
                                    <span class="dcv5-small-pill"><?php echo esc_html($error['severity']); ?></span>
                                </div>
                                <p><strong>Uzrok:</strong> <?php echo esc_html($error['cause']); ?></p>
                                <p><strong>Rješenje:</strong> <?php echo esc_html($error['solution']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="gotovo">
                    <h2><span>9</span>Gotovo je kad…</h2>
                    <p class="dcv5-section-note">Ovaj blok pomaže korisniku procijeniti je li proizvod tehnološki stabilan za rezanje, čuvanje i posluživanje.</p>

                    <div class="dcv5-check-grid">
                        <?php foreach ($recipe['done_when'] as $item) : ?>
                            <article class="dcv5-check-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="sigurnost">
                    <h2><span>10</span>Sigurnosni semafor</h2>
                    <p class="dcv5-section-note">Kod suhomesnatih proizvoda svaka sumnja mora imati praktičnu odluku. Bolje odbaciti rizičan proizvod nego spašavati nešto što se ne smije spašavati.</p>

                    <div class="dcv5-safety-grid">
                        <?php foreach ($recipe['safety'] as $item) : ?>
                            <article class="dcv5-safety-card <?php echo esc_attr($item['level']); ?>">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="posluzivanje">
                    <h2><span>11</span>Posluživanje i čuvanje</h2>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['serving'] as $item) : ?>
                            <article class="dcv5-serving-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="dnevnik">
                    <h2><span>12</span>Dnevnik šarže</h2>
                    <p class="dcv5-section-note">Ovaj blok je priprema za budući print i digitalni dnevnik. Za ozbiljnu proizvodnju bilješke vrijede zlata.</p>

                    <div class="dcv5-print-strip">
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Datum početka</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Masa šarže</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Kalibar crijeva</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">T/RH prostora</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Broj dimljenja</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Gubitak mase</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Datum rezanja</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Ocjena 1–10</span></div>
                    </div>
                </section>
            </main>

            <aside>
                <nav class="dcv5-side-panel" aria-label="Sadržaj recepta">
                    <h3>Sadržaj recepta</h3>
                    <a href="#omjer">Omjer smjese</a>
                    <a href="#sirovine">Glavne sirovine</a>
                    <a href="#zacini">Začini i dodaci</a>
                    <a href="#tekucine">Tekućine i češnjak</a>
                    <a href="#profil">Profil proizvoda</a>
                    <a href="#klima">Tehnološki potpis</a>
                    <a href="#kronologija">Procesna kronologija</a>
                    <a href="#greske">Anatomija greške</a>
                    <a href="#gotovo">Gotovo je kad…</a>
                    <a href="#sigurnost">Sigurnosni semafor</a>
                    <a href="#posluzivanje">Posluživanje</a>
                    <a href="#dnevnik">Dnevnik šarže</a>
                </nav>
            </aside>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

function dcv5_card($item) {
    ?>
    <article class="dcv5-ingredient-card">
        <h3><?php echo esc_html($item['name']); ?></h3>
        <div class="dcv5-amount-line">
            <span class="dcv5-amount"><?php echo esc_html($item['amount']); ?></span>
            <?php if (!empty($item['percent'])) : ?><span class="dcv5-percent"><?php echo esc_html($item['percent']); ?></span><?php endif; ?>
            <?php if (!empty($item['rate'])) : ?><span class="dcv5-rate"><?php echo esc_html($item['rate']); ?></span><?php endif; ?>
        </div>
        <p><?php echo esc_html($item['note']); ?></p>
    </article>
    <?php
}

function dcv5_render_recipe_schema($recipe) {
    $ingredients = [];

    foreach (['materials', 'spices', 'liquids'] as $group) {
        foreach ($recipe[$group] as $item) {
            $ingredients[] = $item['amount'] . ' ' . $item['name'];
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        'name' => $recipe['title'],
        'description' => $recipe['lead'],
        'recipeCategory' => 'Suhomesnati proizvod',
        'recipeCuisine' => 'Hrvatska',
        'recipeYield' => '10 kg mesne mase',
        'totalTime' => 'P60D',
        'recipeIngredient' => $ingredients,
        'recipeInstructions' => array_map(function ($step) {
            return [
                '@type' => 'HowToStep',
                'name' => $step['title'],
                'text' => $step['text'] . ' Kritično: ' . $step['critical'],
            ];
        }, $recipe['timeline']),
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}


function dcv5_supported_recipe_codes() {
    return ['HR-SL-001', 'HR-SL-007', 'HR-SL-020'];
}

function dcv5_get_recipe_profile($post_id, $code = '') {
    if (!$code) {
        $code = get_post_meta($post_id, '_dry_recipe_id', true);
    }

    if ($code === 'HR-SL-001') {
        return dcv5_slavonski_kulen_profile($post_id);
    }

    if ($code === 'HR-SL-007') {
        return dcv5_ratarske_kobasice_profile($post_id);
    }

    if ($code === 'HR-SL-020') {
        return dcv5_vinkovacka_sunka_profile($post_id);
    }

    return null;
}

function dcv5_recipe_js_profile($recipe) {
    if (!$recipe || empty($recipe['code'])) {
        return null;
    }

    $ingredients = [
        'materials' => [],
        'spices' => [],
        'liquids' => [],
    ];

    foreach (['materials', 'spices', 'liquids'] as $group) {
        if (empty($recipe[$group]) || !is_array($recipe[$group])) {
            continue;
        }

        foreach ($recipe[$group] as $item) {
            $ingredients[$group][] = [
                'name' => $item['name'] ?? '',
                'amount' => $item['amount'] ?? '',
                'percent' => $item['percent'] ?? '',
                'rate' => $item['rate'] ?? '',
            ];
        }
    }

    return [
        'code' => $recipe['code'],
        'title' => $recipe['title'],
        'baseKg' => 10,
        'type' => $recipe['type'] ?? '',
        'ingredients' => $ingredients,
    ];
}

function dcv5_slavonski_kulen_profile($post_id) {
    return [
        'code' => 'HR-SL-001',
        'title' => 'Slavonski kulen (PDO EU)',
        'region' => 'Slavonija, Baranja i Srijem',
        'type' => 'Kobasica',
        'lead' => 'Slavonski kulen je dugozreli slavonski proizvod snažnog paprikastog profila, izrađen od odabrane svinjske mišićine i tvrde slanine, punjen u širi prirodni omotač, hladno dimljen i sporo dozrijevan.',
        'quick' => [
            ['label' => 'Šarža', 'value' => '10 kg mesne sirovine'],
            ['label' => 'Trajanje', 'value' => '120–180 dana'],
            ['label' => 'Dimljenje', 'value' => 'hladni dim'],
            ['label' => 'Crijeva', 'value' => '55–80 mm'],
            ['label' => 'Gubitak mase', 'value' => '30–40 %'],
        ],
        'materials' => [
            ['name' => 'Svinjski but i/ili plećka', 'amount' => '8,000 kg', 'percent' => '80 %', 'rate' => '', 'note' => 'Čista mišićina bez tetiva i grubog veziva. Meso mora biti dobro ohlađeno i obrađeno bez nepotrebnog gnječenja.'],
            ['name' => 'Tvrda leđna slanina', 'amount' => '2,000 kg', 'percent' => '20 %', 'rate' => '', 'note' => 'Reže se ručno na kockice 8–12 mm. Ne melje se jer razmazana masnoća kvari presjek i teksturu.'],
        ],
        'spices' => [
            ['name' => 'Kuhinjska sol', 'amount' => '280 g', 'percent' => '2,80 %', 'rate' => '28 g/kg', 'note' => 'Srednja radna vrijednost za dugozreli proizvod. Sol mora biti potpuno ravnomjerno raspoređena.'],
            ['name' => 'Slatka mljevena paprika', 'amount' => '200 g', 'percent' => '2,00 %', 'rate' => '20 g/kg', 'note' => 'Nositelj boje, mirisa i slavonskog identiteta. Paprika mora biti svježa, suha i bez gorčine.'],
            ['name' => 'Ljuta mljevena paprika', 'amount' => '50 g', 'percent' => '0,50 %', 'rate' => '5 g/kg', 'note' => 'Daje toplinu i živost okusa. Količinu ne povećavati bez probne šarže.'],
            ['name' => 'Crni papar', 'amount' => '25 g', 'percent' => '0,25 %', 'rate' => '2,5 g/kg', 'note' => 'Najbolje grubo mljeven neposredno prije miješanja.'],
        ],
        'liquids' => [
            ['name' => 'Svježi češnjak za ekstrakciju', 'amount' => '50 g', 'percent' => '0,50 %', 'rate' => '5 g/kg', 'note' => 'Češnjak se zgnječi, kratko namače i procijedi. U nadjev se dodaje samo procijeđena tekućina.'],
            ['name' => 'Prokuhana i ohlađena voda', 'amount' => '0,070 L', 'percent' => '0,70 %', 'rate' => '7 ml/kg', 'note' => 'Služi samo za ekstrakciju češnjaka i lakše raspoređivanje arome.'],
        ],
        'profile' => [
            ['name' => 'Paprika', 'score' => 9],
            ['name' => 'Dim', 'score' => 7],
            ['name' => 'Ljutina', 'score' => 6],
            ['name' => 'Slanoća', 'score' => 7],
            ['name' => 'Masnoća', 'score' => 5],
            ['name' => 'Tekstura', 'score' => 8],
        ],
        'climate' => [
            ['title' => 'Izrada', 'text' => 'Sirovina mora ostati hladna, a rad miran i uredan. Kod kulena je posebno važno da se slanina ne razmaže i da paprika ravnomjerno obloži meso.'],
            ['title' => 'Odležavanje nadjeva', 'text' => 'Nadjev se odmara u hladnom prostoru kako bi se sol, paprika i miris češnjaka povezali s mesom. Miris mora ostati čist i paprikast.'],
            ['title' => 'Predsušenje', 'text' => 'Širi omotač traži strpljivo prosušivanje prije dima. Površina mora biti suha na dodir, ali ne smije postati tvrda.'],
            ['title' => 'Dimljenje', 'text' => 'Koristi se hladan, tanak i suh dim. Kulen ne treba agresivan dim, nego više blagih ciklusa s odmorom između njih.'],
            ['title' => 'Sušenje', 'text' => 'Sušenje mora biti postupno jer veliki promjer lako razvija tvrdu koru i vlažnu jezgru ako je zrak presuh ili prebrz.'],
            ['title' => 'Zrenje', 'text' => 'Dugo zrenje razvija dubinu okusa, povezanu teksturu i stabilan presjek. Presjek mora biti crven, povezan i bez šupljina.'],
        ],
        'timeline' => [
            ['day' => 'Dan 1', 'title' => 'Priprema mesa', 'text' => 'But i plećku očistiti od tetiva, grubog veziva i nepoželjnih dijelova. Slaninu držati vrlo hladnom.', 'critical' => 'Topla slanina i preduga obrada stvaraju razmazanu mast u presjeku.'],
            ['day' => 'Dan 1', 'title' => 'Mljevenje i rezanje', 'text' => 'Meso mljeti kroz rešetku 8–10 mm, a tvrdu slaninu rezati ručno na kockice 8–12 mm.', 'critical' => 'Slanina se ne melje. Kockice daju prepoznatljiv presjek kulena.'],
            ['day' => 'Dan 1', 'title' => 'Miješanje', 'text' => 'Meso, slaninu, sol, papriku, papar i procijeđenu češnjakovu tekućinu miješati dok masa ne postane ravnomjerna.', 'critical' => 'Masa se ne smije pregrijati niti pretvoriti u pastu.'],
            ['day' => 'Dan 1–5', 'title' => 'Odmor nadjeva', 'text' => 'Nadjev odmarati pokriven u hladnjaku na 2–6 °C, uz svakodnevnu provjeru mirisa.', 'critical' => 'Kiseo ili neugodan miris u ovoj fazi nije prihvatljiv.'],
            ['day' => 'Dan 5–6', 'title' => 'Punjenje', 'text' => 'Puniti u goveđa slijepa crijeva ili kate, čvrsto i bez zraka. Vidljive mjehuriće probosti čistom iglom.', 'critical' => 'Kod širokog omotača zračni džepovi su ozbiljan rizik kvarenja.'],
            ['day' => 'Dan 6–8', 'title' => 'Početna stabilizacija', 'text' => 'Kulen objesiti u hladan i prozračan prostor da se nadjev smiri, a površina pripremi za predsušenje.', 'critical' => 'Ne počinjati s dimom dok je površina mokra.'],
            ['day' => 'Dan 8–14', 'title' => 'Predsušenje', 'text' => 'Omotač se mora priljubiti uz masu i izgubiti površinsku vlagu.', 'critical' => 'Prebrzo sušenje u ovoj fazi zatvara površinu i usporava izlazak vlage iz jezgre.'],
            ['day' => 'Dan 14–44', 'title' => 'Dimljenje', 'text' => 'Dimiti hladnim dimom u blagim ciklusima, s odmorima između dimljenja.', 'critical' => 'Temperatura dima ne smije prelaziti hladni režim; prejak dim daje gorčinu.'],
            ['day' => 'Dan 44–120', 'title' => 'Sušenje', 'text' => 'Sušiti u tamnom i prozračnom prostoru uz kontrolu vlage, mirisa i tvrdoće površine.', 'critical' => 'Mekana jezgra i tvrda površina znak su prebrzog sušenja.'],
            ['day' => 'Dan 120–180', 'title' => 'Zrenje', 'text' => 'Kulen dozrijeva dok ne postigne stabilan gubitak mase, čvrst presjek i zaokružen paprikasto-dimljeni miris.', 'critical' => 'Ne rezati prerano; veliki promjer traži vrijeme.'],
            ['day' => 'Dan 180+', 'title' => 'Pakiranje i čuvanje', 'text' => 'Pakirati tek kada je kulen stabilan, površinski suh i više ne otpušta vlagu.', 'critical' => 'Prerano vakuumiranje može zarobiti vlagu i potaknuti kvarenje.'],
        ],
        'errors' => [
            ['problem' => 'Razmazana mast', 'phase' => 'Rezanje / miješanje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Slanina je bila pretopla ili je previše obrađivana.', 'solution' => 'Slaninu rezati hladnu na 8–12 mm i skratiti miješanje.'],
            ['problem' => 'Šupljine u presjeku', 'phase' => 'Punjenje', 'severity' => 'Rizik kvarenja', 'level' => 'danger', 'cause' => 'Zrak u nadjevu ili labavo punjenje.', 'solution' => 'Puniti čvrsto, probosti mjehuriće i vezati omotač bez praznih prostora.'],
            ['problem' => 'Pretvrda kora', 'phase' => 'Sušenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Preniska vlaga ili prejak protok zraka.', 'solution' => 'Povisiti relativnu vlagu, smanjiti propuh i produljiti sušenje.'],
            ['problem' => 'Kiseo miris jezgre', 'phase' => 'Zrenje', 'severity' => 'Visok rizik', 'level' => 'danger', 'cause' => 'Nepravilna fermentacija, zrak u sredini ili presporo sušenje jezgre.', 'solution' => 'Proizvod ne prikrivati začinima; procijeniti sigurnost i odbaciti ako postoji sumnja.'],
        ],
        'done_when' => [
            ['title' => 'Gubitak mase je najmanje 30 %', 'text' => 'Za dulje zrenje može ići prema 40 %, ovisno o promjeru i željenoj suhoći.'],
            ['title' => 'Presjek je povezan', 'text' => 'Meso i slanina tvore čvrstu cjelinu bez šupljina i mokre jezgre.'],
            ['title' => 'Miris je čist', 'text' => 'Paprika, dim i meso moraju biti skladni, bez kiselih, truležnih ili užeglih nota.'],
            ['title' => 'Omotač je stabilan', 'text' => 'Omotač je suh, priljubljen i bez sluzavih mjesta.'],
        ],
        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Čist paprikasto-dimljeni miris, stabilna površina, povezan presjek i postupni gubitak mase.'],
            ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Pretvrda kora, neravnomjeran presjek, presporo sušenje ili blaga ljepljivost površine. Korigirati uvjete i pratiti.'],
            ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Truležan, kiseo ili užegao miris, sluzava površina, šupljine neugodnog mirisa ili zelene/crne promjene u presjeku.'],
        ],
        'serving' => [
            ['title' => 'Rezanje', 'text' => 'Rezati tanko, najbolje nakon kratkog odmora na sobnoj temperaturi.'],
            ['title' => 'Uz što poslužiti', 'text' => 'Kruh, sir, luk, kiselo povrće i jednostavna slavonska plata.'],
            ['title' => 'Čuvanje cijelog proizvoda', 'text' => 'Čuvati na hladnom, tamnom i prozračnom mjestu, bez zatvaranja u plastiku dok proizvod otpušta vlagu.'],
            ['title' => 'Nakon rezanja', 'text' => 'Zamotati u papir ili krpu i potrošiti u razumnom roku.'],
        ],
    ];
}

function dcv5_vinkovacka_sunka_profile($post_id) {
    return [
        'code' => 'HR-SL-020',
        'title' => 'Vinkovačka šunka — suho soljena varijanta',
        'region' => 'Vinkovci i srijemski prostor',
        'type' => 'Cijeli komad',
        'lead' => 'Vinkovačka šunka — suho soljena varijanta je slavonsko-srijemski proizvod od cijelog svinjskog buta, soljen suhim postupkom, hladno dimljen i dugim zrenjem doveden do čvrste teksture i čistog dimljenog mirisa.',
        'quick' => [
            ['label' => 'Šarža', 'value' => '1 but / 9–13 kg'],
            ['label' => 'Trajanje', 'value' => '10–14 mjeseci'],
            ['label' => 'Dimljenje', 'value' => 'hladni dim'],
            ['label' => 'Omotač', 'value' => 'bez crijeva'],
            ['label' => 'Gubitak mase', 'value' => 'postupno zrenje'],
        ],
        'materials' => [
            ['name' => 'Svinjski but s kosti i kožom', 'amount' => '9–13 kg', 'percent' => '', 'rate' => '', 'note' => 'Cijeli anatomski komad. Koža i kost usporavaju sušenje i traže dulje zrenje.'],
        ],
        'spices' => [
            ['name' => 'Kuhinjska sol', 'amount' => '550–650 g', 'percent' => '', 'rate' => '50–60 g/kg', 'note' => 'Količina se vodi prema masi buta. Sol se utrljava temeljito, osobito oko kosti i debljih dijelova.'],
            ['name' => 'Crni papar', 'amount' => '35–55 g', 'percent' => '', 'rate' => '3–5 g/kg', 'note' => 'Daje blagi začinski sloj bez prekrivanja mirisa mesa i dima.'],
            ['name' => 'Slatka paprika', 'amount' => '35–45 g', 'percent' => '', 'rate' => '3–4 g/kg', 'note' => 'Koristi se kao blagi slavonski začinski naglasak.'],
            ['name' => 'Suhi češnjak ili češnjakova aroma', 'amount' => '20–35 g', 'percent' => '', 'rate' => '2–3 g/kg', 'note' => 'Koristiti odmjereno. Kod cijelih komada češnjak ne smije stvarati mokre džepove na površini.'],
        ],
        'liquids' => [
            ['name' => 'Tekućina za češnjak', 'amount' => 'nije obvezna', 'percent' => '', 'rate' => '', 'note' => 'Kod ove suho soljene varijante tekućina se ne koristi kao dio nadjeva. Ako se koristi češnjakova aroma, površina mora ostati suha.'],
            ['name' => 'Crijeva / omotači', 'amount' => 'ne koriste se', 'percent' => '', 'rate' => '', 'note' => 'Šunka je cijeli komad i ne puni se u crijevo. Zaštitu čine koža, površinsko sušenje i pravilno zrenje.'],
        ],
        'profile' => [
            ['name' => 'Paprika', 'score' => 3],
            ['name' => 'Dim', 'score' => 7],
            ['name' => 'Ljutina', 'score' => 1],
            ['name' => 'Slanoća', 'score' => 7],
            ['name' => 'Masnoća', 'score' => 5],
            ['name' => 'Tekstura', 'score' => 8],
        ],
        'climate' => [
            ['title' => 'Suho soljenje', 'text' => 'Kod cijelog buta sol mora postupno prodirati prema sredini. Posebno se pazi područje oko kosti jer je to najosjetljivija zona.'],
            ['title' => 'Stabilizacija nakon soljenja', 'text' => 'Nakon soljenja šunku treba odmoriti i prosušiti prije dima. Površina ne smije biti mokra ni ljepljiva.'],
            ['title' => 'Dimljenje', 'text' => 'Dimljenje mora biti hladno, blago i postupno. Cilj je aroma i površinska stabilizacija, ne brzo sušenje.'],
            ['title' => 'Sušenje', 'text' => 'Veliki komad traži sporo i mirno sušenje. Prebrz zrak zatvara površinu dok sredina ostaje previše vlažna.'],
            ['title' => 'Zrenje', 'text' => 'Zrenjem se razvijaju čvrsta tekstura, zaokružen miris i stabilnost proizvoda. Kod šunke vrijeme ne treba požurivati.'],
            ['title' => 'Čuvanje', 'text' => 'Gotova šunka čuva se u hladnom, tamnom i prozračnom prostoru, zaštićena od prevelike vlage i naglih promjena temperature.'],
        ],
        'timeline' => [
            ['day' => 'Dan 1', 'title' => 'Obrada buta', 'text' => 'But očistiti, oblikovati i pregledati područje oko kosti, zglobova i kože.', 'critical' => 'Ne ostavljati nagnječene ili onečišćene dijelove.'],
            ['day' => 'Dan 1–28', 'title' => 'Suho soljenje', 'text' => 'Sol i začine ravnomjerno utrljati u površinu. But držati na 2–4 °C i okretati svakih nekoliko dana.', 'critical' => 'Premalo soli oko kosti povećava rizik kvarenja jezgre.'],
            ['day' => 'Dan 28–35', 'title' => 'Stabilizacija i predsušenje', 'text' => 'Nakon soljenja šunku objesiti da se površina smiri i prosuši prije dima.', 'critical' => 'Ne dimiti mokru površinu jer dim postaje grub i neujednačen.'],
            ['day' => 'Dan 35–90', 'title' => 'Dimljenje', 'text' => 'Dimiti hladnim dimom u blagim ciklusima, uz pauze i dobro provjetravanje.', 'critical' => 'Dim ne smije biti topao, gust ni gorak.'],
            ['day' => 'Mjesec 3–6', 'title' => 'Sušenje', 'text' => 'Šunku sušiti u stabilnim uvjetima, uz kontrolu površine, mirisa i sporog gubitka vlage.', 'critical' => 'Tvrda površina i mekana unutrašnjost znak su prebrzog sušenja.'],
            ['day' => 'Mjesec 6–12', 'title' => 'Zrenje', 'text' => 'Šunka dozrijeva dok tekstura ne postane čvrsta, miris čist, a rez stabilan.', 'critical' => 'Kod sumnje na neugodan miris uz kost proizvod ne treba spašavati.'],
            ['day' => 'Nakon zrenja', 'title' => 'Pakiranje i čuvanje', 'text' => 'Pakirati tek kada je proizvod stabilan i površinski suh. Za dulje čuvanje paziti da pakiranje ne zarobi vlagu.', 'critical' => 'Ako se u pakiranju pojavi vlaga ili neugodan miris, šunku izvaditi i provjeriti prije uporabe.'],
        ],
        'errors' => [
            ['problem' => 'Kvarenje uz kost', 'phase' => 'Soljenje / zrenje', 'severity' => 'Visok rizik', 'level' => 'danger', 'cause' => 'Nedovoljno prodiranje soli ili previsoka temperatura.', 'solution' => 'Soljenje voditi hladno i dovoljno dugo; kod neugodnog mirisa uz kost proizvod ne koristiti.'],
            ['problem' => 'Pretvrda površina', 'phase' => 'Sušenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Prebrz zrak ili preniska relativna vlaga.', 'solution' => 'Smanjiti propuh, stabilizirati vlagu i produljiti sušenje.'],
            ['problem' => 'Gorak dim', 'phase' => 'Dimljenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Gust dim, vlažno drvo ili previsoka temperatura dima.', 'solution' => 'Koristiti suho tvrdo drvo i tanak hladni dim u kraćim ciklusima.'],
            ['problem' => 'Ljepljiva površina', 'phase' => 'Predsušenje', 'severity' => 'Rizik', 'level' => 'danger', 'cause' => 'Previsoka vlaga i preslab protok zraka.', 'solution' => 'Poboljšati provjetravanje i ne nastavljati dimljenje dok površina nije stabilna.'],
        ],
        'done_when' => [
            ['title' => 'Miris je čist', 'text' => 'Miris je dimljen, mesnat i zaokružen, bez truležnih ili kiselih nota, osobito uz kost.'],
            ['title' => 'Tekstura je čvrsta', 'text' => 'Rez je stabilan, bez vlažne jezgre i bez ljepljivih dijelova.'],
            ['title' => 'Površina je suha', 'text' => 'Površina nije sluzava, mokra ni neugodnog mirisa.'],
            ['title' => 'Zrenje je završeno', 'text' => 'Vrijeme, tekstura, miris i rez pokazuju da proizvod više nije sirov u sredini.'],
        ],
        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Suha površina, čist dimljeni miris, stabilan rez i čvrsta tekstura.'],
            ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Pretvrda površina, sporo sušenje, blaga ljepljivost ili neujednačen miris. Popraviti uvjete i pratiti.'],
            ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Neugodan miris uz kost, sluzava površina, truležne note, zelene/crne promjene ili vlažna jezgra neugodnog mirisa.'],
        ],
        'serving' => [
            ['title' => 'Rezanje', 'text' => 'Rezati tanko, oštrim nožem, nakon kratkog odmora komada na sobnoj temperaturi.'],
            ['title' => 'Uz što poslužiti', 'text' => 'Odgovara uz kruh, sir, mladi luk, kiselo povrće i tradicionalnu slavonsku zakusku.'],
            ['title' => 'Čuvanje cijele šunke', 'text' => 'Čuvati u hladnom, tamnom i prozračnom prostoru, bez zatvaranja dok proizvod otpušta vlagu.'],
            ['title' => 'Nakon rezanja', 'text' => 'Reznu površinu zaštititi papirom ili čistom krpom i pratiti miris.'],
        ],
    ];
}


function dcv5_ratarske_kobasice_profile($post_id) {
    return [
        'code' => 'HR-SL-007',
        'title' => 'Ratarske kobasice',
        'region' => 'Slavonija',
        'type' => 'Kobasica',
        'lead' => 'Ratarske kobasice su slavonske trajne kobasice za šaržu od 10 kg mesne mase, s jasnim omjerom mesa i masnoće, začinima u gramima, hladnim dimljenjem i postupnim sušenjem.',
        'quick' => [
            ['label' => 'Šarža', 'value' => '10 kg mesne mase'],
            ['label' => 'Trajanje', 'value' => '30–60 dana'],
            ['label' => 'Dimljenje', 'value' => 'hladni dim'],
            ['label' => 'Crijeva', 'value' => '32–42 mm'],
            ['label' => 'Gubitak mase', 'value' => '25–30 %'],
        ],
        'materials' => [
            ['name' => 'Mješovito svinjsko meso', 'amount' => '7,000 kg', 'percent' => '70 %', 'rate' => '', 'note' => 'Vrat, obresci rebara, plećka i čisti dijelovi glave. Osnova okusa, strukture i vezanja nadjeva.'],
            ['name' => 'Tvrđa svinjska slanina ili masniji obresci', 'amount' => '3,000 kg', 'percent' => '30 %', 'rate' => '', 'note' => 'Daje sočnost, mekoću presjeka i tradicionalni seoski karakter.'],
        ],
        'spices' => [
            ['name' => 'Kuhinjska sol', 'amount' => '220 g', 'percent' => '2,20 %', 'rate' => '22 g/kg', 'note' => 'Radna vrijednost za ovu recepturu. Sol mora biti ravnomjerno raspoređena po cijeloj smjesi.'],
            ['name' => 'Slatka mljevena paprika', 'amount' => '115 g', 'percent' => '1,15 %', 'rate' => '11,5 g/kg', 'note' => 'Slavonski tip paprike; mora biti svježa, mirisna i bez gorčine.'],
            ['name' => 'Ljuta mljevena paprika', 'amount' => '30 g', 'percent' => '0,30 %', 'rate' => '3 g/kg', 'note' => 'Daje umjerenu pikantnost. Ne povećavati bez probne šarže.'],
            ['name' => 'Crni papar', 'amount' => '12 g', 'percent' => '0,12 %', 'rate' => '1,2 g/kg', 'note' => 'Najbolje grubo mljeven neposredno prije rada.'],
            ['name' => 'Kim', 'amount' => '7 g', 'percent' => '0,07 %', 'rate' => '0,7 g/kg', 'note' => 'Prepoznatljiv dodatak ratarskom stilu; ne smije preuzeti okus kobasice.'],
        ],
        'liquids' => [
            ['name' => 'Svježi češnjak za ekstrakciju', 'amount' => '30 g', 'percent' => '0,30 %', 'rate' => '3 g/kg', 'note' => 'Češnjak se zgnječi, namače i procijedi. U nadjev se ne dodaju vlakna češnjaka.'],
            ['name' => 'Prokuhana i ohlađena voda', 'amount' => '0,060 L', 'percent' => '0,60 %', 'rate' => '6 ml/kg', 'note' => 'Koristi se za ekstrakciju češnjaka. U nadjev ide samo procijeđena tekućina.'],
        ],
        'profile' => [
            ['name' => 'Paprika', 'score' => 7],
            ['name' => 'Dim', 'score' => 6],
            ['name' => 'Ljutina', 'score' => 4],
            ['name' => 'Slanoća', 'score' => 6],
            ['name' => 'Masnoća', 'score' => 6],
            ['name' => 'Tekstura', 'score' => 6],
        ],
        'climate' => [
            ['title' => 'Izrada', 'text' => 'Meso i masnoća trebaju ostati hladni, idealno 0–4 °C. Hladna sirovina daje čitljiv presjek i sprječava razmazivanje masnoće. Ako se masa počne lijepiti ili mazati, rad treba prekinuti i sirovinu vratiti na hlađenje.'],
            ['title' => 'Početna fermentacija', 'text' => 'Kod ove tradicionalne kobasice fermentacija je blaga i prirodna, bez posebno vođene starter kulture. Počinje tijekom odležavanja nadjeva i nastavlja se prvih 12–24 sata nakon punjenja. Cilj je stabilizacija mirisa, boje i vezanja nadjeva, bez naglog kiseljenja.'],
            ['title' => 'Predsušenje', 'text' => 'Nakon početne stabilizacije kobasice trebaju 24–48 sati mirnog predsušenja na 8–12 °C i relativnoj vlazi oko 80–85 %. Površina mora postati suha na dodir, ali ne tvrda. Mokra površina ne prima dim pravilno.'],
            ['title' => 'Dimljenje', 'text' => 'Dimljenje je aromatska i površinska faza, a ne način brzog sušenja. Koristiti tanak hladni dim od bukve, graba ili hrasta. Temperatura dima ne bi trebala prelaziti 20–22 °C, uz pauze između ciklusa.'],
            ['title' => 'Sušenje', 'text' => 'Sušenje je faza kontroliranog gubitka vode i mase. Nakon dimljenja kobasice trebaju 10–15 °C, relativnu vlagu 70–80 % i blago strujanje zraka. Cilj je postupno smanjivanje mase bez tvrde kore.'],
            ['title' => 'Zrenje', 'text' => 'Zrenje počinje kada je proizvod površinski stabilan i kada se gubitak vlage uspori. U ovoj fazi razvijaju se aroma, boja presjeka i tekstura. Miris mora ostati čist, paprikast i dimljen, bez kiselih ili truležnih nota.'],
        ],
        'timeline' => [
            ['day' => 'Dan 1', 'title' => 'Priprema mesa', 'text' => 'Očistiti meso, ukloniti žilave dijelove i raditi s dobro ohlađenom sirovinom.', 'critical' => 'Temperatura sirovine mora ostati niska da se masnoća ne razmaže.'],
            ['day' => 'Dan 1', 'title' => 'Mljevenje', 'text' => 'Meso i masnije dijelove mljeti kroz rešetku 6–8 mm, bez ponavljanja mljevenja.', 'critical' => 'Ako se masa lijepi i maže, treba prekinuti rad i ohladiti sirovinu.'],
            ['day' => 'Dan 1', 'title' => 'Miješanje', 'text' => 'Dodati sol, papriku, papar, kim i procijeđenu češnjakovu tekućinu. Miješati 8–12 minuta.', 'critical' => 'Začini moraju biti ravnomjerni, ali nadjev se ne smije pregrijati.'],
            ['day' => 'Dan 1–2', 'title' => 'Odležavanje nadjeva', 'text' => 'Nadjev držati 12 sati na 2–6 °C, pokriven i zaštićen od isušivanja.', 'critical' => 'Miris mora ostati čist, paprikast i bez kiselih nota.'],
            ['day' => 'Dan 2', 'title' => 'Punjenje', 'text' => 'Puniti u svinjska crijeva 32–42 mm, čvrsto, ali bez pucanja.', 'critical' => 'Zračne džepove odmah probosti čistom iglom.'],
            ['day' => 'Dan 2–3', 'title' => 'Početna fermentacija / stabilizacija', 'text' => 'Nakon punjenja kobasice miruju u hladnom i prozračnom prostoru. Nadjev se stabilizira, sol i začini se dodatno povezuju s masom, a površina se priprema za predsušenje.', 'critical' => 'Fermentacija ne smije krenuti naglo. Ako se pojavi izražen kiseo miris, sluzavost ili napuhavanje crijeva, postupak treba zaustaviti i proizvod ne koristiti bez sigurne procjene.'],
            ['day' => 'Dan 3–5', 'title' => 'Predsušenje', 'text' => 'Kobasice objesiti tako da se ne dodiruju. Površina se mora prosušiti prije prvog dima.', 'critical' => 'Ne dimiti dok je površina mokra, sjajna ili ljepljiva.'],
            ['day' => 'Dan 5–15', 'title' => 'Dimljenje', 'text' => 'Dimiti hladnim dimom u 3–6 blagih ciklusa, uz pauze između dimljenja.', 'critical' => 'Prejak dim daje gorčinu i pretamnu površinu. Dim mora biti tanak, suh i mirisan.'],
            ['day' => 'Dan 15–35', 'title' => 'Sušenje', 'text' => 'Nakon dimljenja kobasice sušiti na 10–15 °C i 70–80 % relativne vlage, uz blago strujanje zraka.', 'critical' => 'Pretvrda površina i mekana sredina znače prebrzo sušenje ili prenizku vlagu.'],
            ['day' => 'Dan 35–60', 'title' => 'Zrenje', 'text' => 'U završnoj fazi proizvod se stabilizira, okus se zaokružuje, a presjek postaje povezan i mirisan.', 'critical' => 'Ako se pojavi neugodan kiseo, truležan ili užegao miris, proizvod ne treba spašavati začinima.'],
            ['day' => 'Dan 60+', 'title' => 'Pakiranje i čuvanje', 'text' => 'Gotove kobasice pakirati tek kada su stabilne, površinski suhe i mirisno čiste. Za kratko čuvanje prikladan je papir ili prozračna ambalaža, a vakuumiranje se koristi samo kada proizvod više ne otpušta vlagu.', 'critical' => 'Ne vakuumirati prerano. Ako se u pakiranju pojavi vlaga, neugodan miris ili ljepljiva površina, proizvod izvaditi, provjeriti i ne konzumirati ako postoji sumnja u kvarenje.'],
        ],
        'errors' => [
            ['problem' => 'Razmazana mast', 'phase' => 'Mljevenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Sirovina je bila pretopla ili je masnoća previše obrađivana.', 'solution' => 'Raditi s ohlađenom sirovinom, mljeti samo jednom i napraviti pauzu ako se masnoća počne mazati.'],
            ['problem' => 'Zračni džepovi', 'phase' => 'Punjenje', 'severity' => 'Rizik kvarenja', 'level' => 'danger', 'cause' => 'Labavo punjenje ili neprobušeni mjehurići zraka.', 'solution' => 'Puniti sporije, kontrolirati pritisak i svaki mjehurić odmah probosti čistom iglom.'],
            ['problem' => 'Gorka aroma dima', 'phase' => 'Dimljenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Previše dima, vlažno drvo ili premale pauze.', 'solution' => 'Koristiti suho tvrdo drvo, skratiti cikluse i produljiti pauze između dimljenja.'],
            ['problem' => 'Sluzava površina', 'phase' => 'Predsušenje / zrenje', 'severity' => 'Visok rizik', 'level' => 'danger', 'cause' => 'Previsoka vlaga bez dovoljno zraka ili loše predsušenje.', 'solution' => 'Premjestiti kobasice u prozračniji prostor, osušiti površinu i ne nastavljati dimljenje dok površina nije stabilna.'],
        ],
        'done_when' => [
            ['title' => 'Površina je stabilna', 'text' => 'Kobasica je suha na dodir, bez sluzi, bez ljepljivosti i bez mokrih mjesta oko vezova.'],
            ['title' => 'Presjek je povezan', 'text' => 'Meso i masnoća drže cjelinu, nema velikih šupljina, a sredina nije mekana ni vlažna.'],
            ['title' => 'Miris je čist', 'text' => 'Miris je paprikast, blag dimljen i mesnat, bez truležnih, kiselih ili užeglih nota.'],
            ['title' => 'Gubitak mase je postignut', 'text' => 'Ciljani gubitak za ovaj tip kobasice je približno 25–30 %, ovisno o kalibru i željenoj suhoći.'],
            ['title' => 'Kora nije pretvrda', 'text' => 'Površina ne smije biti kameno tvrda dok je sredina mekana; to je znak prebrzog sušenja.'],
            ['title' => 'Okus je zaokružen', 'text' => 'Dim, paprika, sol i masnoća trebaju biti uravnoteženi, bez gorčine i bez agresivne kiselosti.'],
        ],
        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Suha površina, čist miris, blaga bijela plemenita plijesan, ujednačena boja i postupno smanjenje mase.'],
            ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Pretvrda površina, neujednačena boja, slab protok zraka, blaga ljepljivost ili sumnja na presporo sušenje. Poboljšati uvjete i pratiti proizvod.'],
            ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Truležan, kiseo ili užegao miris, sluzava površina, napuhano crijevo, zelene/crne promjene u presjeku ili mekana sredina neugodnog mirisa.'],
        ],
        'serving' => [
            ['title' => 'Rezanje', 'text' => 'Kobasicu rezati tanko, nakon kratkog odmora na sobnoj temperaturi. Tako se aroma otvara, a presjek postaje ugodniji.'],
            ['title' => 'Uz što poslužiti', 'text' => 'Odgovara uz kruh, sir, luk, kiselo povrće, jednostavnu slavonsku zakusku i laganija crna ili svježija bijela vina.'],
            ['title' => 'Čuvanje cijelog proizvoda', 'text' => 'Čuvati na hladnom, suhom i tamnom mjestu, uz umjereno strujanje zraka. Izbjegavati zatvaranje u plastiku ako proizvod još otpušta vlagu.'],
            ['title' => 'Nakon rezanja', 'text' => 'Zamotati u papir ili prozračnu krpu i potrošiti u roku od 7–14 dana. Ako se pojavi neugodan miris ili sluzava površina, proizvod ne koristiti.'],
            ['title' => 'Pečenje', 'text' => 'Za pečenje koristiti samo svježu ili najviše 1–2 dana prosušenu kobasicu. Potpuno suha kobasica nije za pečenje.'],
            ['title' => 'Kada odbaciti', 'text' => 'Ne konzumirati ako se pojavi truležan, kiseli ili užegao miris, zelene/crne promjene u presjeku ili mekana sredina neugodnog mirisa.'],
        ],
    ];
}


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-clarity-fix.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5.1 Clarity Fix
 * Description: Poboljšava hijerarhiju količina, postotaka i omjera u v0.5 pilot prikazu.
 * Version: 0.5.1
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.5.1 — količina je glavna informacija.
         * Postotak i g/kg su sekundarni tehnološki podaci.
         */

        .single-dry_recipe .dcv5-layout {
            grid-template-columns: minmax(0, 1fr) 250px !important;
            gap: 22px !important;
        }

        .single-dry_recipe #sirovine .dcv5-card-grid.two,
        .single-dry_recipe #zacini .dcv5-card-grid.two,
        .single-dry_recipe #tekucine .dcv5-card-grid.two {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card,
        .single-dry_recipe #zacini .dcv5-ingredient-card,
        .single-dry_recipe #tekucine .dcv5-ingredient-card {
            display: grid !important;
            grid-template-columns: minmax(0, 1.15fr) 180px minmax(0, 1.45fr) !important;
            gap: 16px !important;
            align-items: center !important;
            padding: 16px 18px !important;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        .single-dry_recipe #zacini .dcv5-ingredient-card h3,
        .single-dry_recipe #tekucine .dcv5-ingredient-card h3 {
            margin: 0 !important;
            font-size: 17px !important;
            line-height: 1.35 !important;
        }

        .single-dry_recipe #sirovine .dcv5-amount-line,
        .single-dry_recipe #zacini .dcv5-amount-line,
        .single-dry_recipe #tekucine .dcv5-amount-line {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 5px !important;
            margin: 0 !important;
            justify-items: start !important;
        }

        .single-dry_recipe .dcv5-amount {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 96px !important;
            padding: 9px 13px !important;
            border-radius: 12px !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 18px !important;
            line-height: 1.1 !important;
            font-weight: 900 !important;
        }

        .single-dry_recipe .dcv5-percent,
        .single-dry_recipe .dcv5-rate {
            display: block !important;
            padding: 0 !important;
            border: 0 !important;
            background: transparent !important;
            color: #6b5c38 !important;
            font-size: 12px !important;
            line-height: 1.35 !important;
            font-weight: 800 !important;
        }

        .single-dry_recipe .dcv5-percent::before {
            content: "Udio: ";
            color: #8a733c;
            font-weight: 900;
        }

        .single-dry_recipe .dcv5-rate::before {
            content: "Omjer: ";
            color: #8a733c;
            font-weight: 900;
        }

        .single-dry_recipe #sirovine .dcv5-ingredient-card p,
        .single-dry_recipe #zacini .dcv5-ingredient-card p,
        .single-dry_recipe #tekucine .dcv5-ingredient-card p {
            margin: 0 !important;
            font-size: 15.5px !important;
            line-height: 1.62 !important;
            color: #3b4861 !important;
        }

        .single-dry_recipe .dcv5-side-panel {
            padding: 14px !important;
        }

        .single-dry_recipe .dcv5-side-panel a {
            font-size: 13px !important;
            padding: 7px 8px !important;
        }

        @media (max-width: 980px) {
            .single-dry_recipe .dcv5-layout {
                grid-template-columns: 1fr !important;
            }

            .single-dry_recipe #sirovine .dcv5-ingredient-card,
            .single-dry_recipe #zacini .dcv5-ingredient-card,
            .single-dry_recipe #tekucine .dcv5-ingredient-card {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }
        }
    </style>
    <?php
}, 1300);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-balanced-stage.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5.11 Balanced Stage
 * Description: Čisti centrirani layout bez duple pozadine i bez bježanja udesno.
 * Version: 0.5.11
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.5.11 — uravnotežen stage.
         * Puna pozadina stranice + centriran receptni sadržaj.
         */

        body.single-dry_recipe .site-content {
            background: #f8f0de !important;
        }

        body.single-dry_recipe .ast-container,
        body.single-dry_recipe .site-content .ast-container,
        body.single-dry_recipe .content-area,
        body.single-dry_recipe main.site-main,
        body.single-dry_recipe article,
        body.single-dry_recipe .entry-content {
            width: 100% !important;
            max-width: none !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
            background: transparent !important;
        }

        body.single-dry_recipe .entry-content::before,
        body.single-dry_recipe .entry-content::after,
        body.single-dry_recipe article::before,
        body.single-dry_recipe article::after {
            display: none !important;
            content: none !important;
        }

        body.single-dry_recipe .dcv5-recipe {
            box-sizing: border-box !important;
            width: min(1280px, calc(100vw - 56px)) !important;
            max-width: 1280px !important;
            margin: 0 auto !important;
            padding: 44px 0 72px !important;
            background: transparent !important;
        }

        body.single-dry_recipe .dcv5-hero,
        body.single-dry_recipe .dcv5-quick-strip,
        body.single-dry_recipe .dcv5-layout {
            width: 100% !important;
            max-width: none !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        body.single-dry_recipe .dcv5-layout {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) 250px !important;
            gap: 24px !important;
            align-items: start !important;
        }

        body.single-dry_recipe .dcv5-layout > main {
            min-width: 0 !important;
            width: 100% !important;
        }

        body.single-dry_recipe .dcv5-side-panel {
            width: 250px !important;
            max-width: 250px !important;
        }

        body.single-dry_recipe .dcv5-hero-grid {
            grid-template-columns: minmax(0, 1fr) 390px !important;
            gap: 28px !important;
        }

        body.single-dry_recipe .dcv5-hero-media img {
            height: 285px !important;
        }

        body.single-dry_recipe .dcv5-panel {
            width: 100% !important;
            max-width: none !important;
            padding: 28px 30px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-card-grid.two,
        body.single-dry_recipe #zacini .dcv5-card-grid.two,
        body.single-dry_recipe #tekucine .dcv5-card-grid.two {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card,
        body.single-dry_recipe #zacini .dcv5-ingredient-card,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card {
            display: grid !important;
            grid-template-columns: minmax(210px, 1fr) 165px minmax(260px, 1.35fr) !important;
            gap: 18px !important;
            align-items: center !important;
            width: 100% !important;
            max-width: none !important;
            padding: 18px 20px !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        body.single-dry_recipe #zacini .dcv5-ingredient-card h3,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card h3,
        body.single-dry_recipe #sirovine .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #zacini .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card .dcv5-amount-line,
        body.single-dry_recipe #sirovine .dcv5-ingredient-card p,
        body.single-dry_recipe #zacini .dcv5-ingredient-card p,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card p {
            margin: 0 !important;
        }

        @media (min-width: 1500px) {
            body.single-dry_recipe .dcv5-recipe {
                width: min(1360px, calc(100vw - 96px)) !important;
                max-width: 1360px !important;
            }

            body.single-dry_recipe .dcv5-layout {
                grid-template-columns: minmax(0, 1fr) 260px !important;
                gap: 26px !important;
            }

            body.single-dry_recipe .dcv5-side-panel {
                width: 260px !important;
                max-width: 260px !important;
            }

            body.single-dry_recipe .dcv5-hero-grid {
                grid-template-columns: minmax(0, 1fr) 420px !important;
            }

            body.single-dry_recipe .dcv5-hero-media img {
                height: 305px !important;
            }
        }

        @media (max-width: 1179px) {
            body.single-dry_recipe .dcv5-recipe {
                width: min(100%, calc(100vw - 28px)) !important;
                padding: 28px 0 52px !important;
            }

            body.single-dry_recipe .dcv5-layout {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe .dcv5-side-panel {
                width: 100% !important;
                max-width: none !important;
            }

            body.single-dry_recipe .dcv5-hero-grid {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe #sirovine .dcv5-ingredient-card,
            body.single-dry_recipe #zacini .dcv5-ingredient-card,
            body.single-dry_recipe #tekucine .dcv5-ingredient-card {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
    <?php
}, 1600);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-card-polish.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5.12 Card Polish
 * Description: Uređuje čitljivost kartica, brzi proizvodni sažetak i tekstualnu hijerarhiju v0.5 prikaza.
 * Version: 0.5.12
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.5.12 — tekst u karticama mora biti čitljiv, prozračan i hijerarhijski jasan.
         */

        body.single-dry_recipe .dcv5-panel {
            line-height: 1.65 !important;
        }

        body.single-dry_recipe .dcv5-section-note {
            max-width: 920px !important;
            margin-bottom: 22px !important;
            color: #46536b !important;
            font-size: 15.5px !important;
            line-height: 1.7 !important;
        }

        /*
         * Brzi proizvodni sažetak: šarža, trajanje, dimljenje...
         * Vrijednost mora biti glavna informacija.
         */
        body.single-dry_recipe .dcv5-quick-strip {
            display: grid !important;
            grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
            gap: 12px !important;
            margin: 18px 0 24px !important;
        }

        body.single-dry_recipe .dcv5-quick-card {
            min-height: 82px !important;
            padding: 16px 18px !important;
            border-radius: 18px !important;
            background: #fffaf0 !important;
            border: 1px solid #dfc282 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
            box-shadow: 0 8px 20px rgba(25, 32, 48, .045) !important;
        }

        body.single-dry_recipe .dcv5-quick-card span {
            margin: 0 0 7px !important;
            color: #8a733c !important;
            font-size: 11px !important;
            line-height: 1.1 !important;
            font-weight: 900 !important;
            letter-spacing: .075em !important;
            text-transform: uppercase !important;
        }

        body.single-dry_recipe .dcv5-quick-card strong {
            color: #0f1930 !important;
            font-size: 17px !important;
            line-height: 1.25 !important;
            font-weight: 900 !important;
        }

        /*
         * Kartice sastojaka: mirniji prikaz, bolji razmak i čitljiviji opis.
         */
        body.single-dry_recipe #sirovine .dcv5-ingredient-card,
        body.single-dry_recipe #zacini .dcv5-ingredient-card,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card {
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            padding: 20px 22px !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card h3,
        body.single-dry_recipe #zacini .dcv5-ingredient-card h3,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card h3 {
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe #sirovine .dcv5-ingredient-card p,
        body.single-dry_recipe #zacini .dcv5-ingredient-card p,
        body.single-dry_recipe #tekucine .dcv5-ingredient-card p {
            color: #43506a !important;
            font-size: 15.5px !important;
            line-height: 1.7 !important;
        }

        body.single-dry_recipe .dcv5-amount {
            border-radius: 14px !important;
            padding: 10px 15px !important;
            min-width: 118px !important;
            font-size: 18px !important;
            box-shadow: 0 6px 14px rgba(17, 27, 51, .16) !important;
        }

        body.single-dry_recipe .dcv5-percent,
        body.single-dry_recipe .dcv5-rate {
            color: #7b693b !important;
            font-size: 12.5px !important;
            line-height: 1.45 !important;
            font-weight: 800 !important;
        }

        /*
         * Klimatski i tehnološki potpis: tekst je bio previše zbijen.
         */
        body.single-dry_recipe #klima .dcv5-card-grid.two {
            gap: 14px !important;
        }

        body.single-dry_recipe .dcv5-climate-card {
            padding: 18px 19px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-climate-card h3 {
            margin-bottom: 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.3 !important;
        }

        body.single-dry_recipe .dcv5-climate-card p {
            color: #43506a !important;
            font-size: 15.5px !important;
            line-height: 1.72 !important;
        }

        /*
         * Procesna kronologija: bolji ritam između dana, naslova i kritične napomene.
         */
        body.single-dry_recipe .dcv5-timeline {
            gap: 16px !important;
        }

        body.single-dry_recipe .dcv5-timeline-item {
            padding: 18px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            border: 1px solid #e8d19b !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-timeline-item h3 {
            margin: 0 0 10px !important;
            color: #10182d !important;
            font-size: 21px !important;
            font-weight: 900 !important;
            line-height: 1.25 !important;
        }

        body.single-dry_recipe .dcv5-timeline-item p {
            color: #3f4c66 !important;
            font-size: 15.8px !important;
            line-height: 1.7 !important;
        }

        body.single-dry_recipe .dcv5-critical {
            margin-top: 14px !important;
            padding: 12px 14px !important;
            border-radius: 12px !important;
            color: #3f4c66 !important;
            font-size: 14.5px !important;
            line-height: 1.6 !important;
        }

        /*
         * Anatomija greške i Gotovo je kad — ravnomjerniji tekst u karticama.
         */
        body.single-dry_recipe .dcv5-error-card,
        body.single-dry_recipe .dcv5-check-card,
        body.single-dry_recipe .dcv5-safety-card,
        body.single-dry_recipe .dcv5-serving-card {
            padding: 18px 19px !important;
            border-radius: 18px !important;
            background: #fffdf7 !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv5-error-card h3,
        body.single-dry_recipe .dcv5-check-card h3,
        body.single-dry_recipe .dcv5-safety-card h3,
        body.single-dry_recipe .dcv5-serving-card h3 {
            margin-bottom: 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.32 !important;
        }

        body.single-dry_recipe .dcv5-error-card p,
        body.single-dry_recipe .dcv5-check-card p,
        body.single-dry_recipe .dcv5-safety-card p,
        body.single-dry_recipe .dcv5-serving-card p {
            color: #43506a !important;
            font-size: 15.3px !important;
            line-height: 1.68 !important;
        }

        body.single-dry_recipe .dcv5-error-card p + p {
            margin-top: 8px !important;
        }

        body.single-dry_recipe .dcv5-small-pill {
            padding: 6px 9px !important;
            font-size: 12px !important;
            line-height: 1.15 !important;
            border-radius: 999px !important;
        }

        /*
         * Responsive: brzi sažetak ne smije biti zgnječen.
         */
        @media (max-width: 980px) {
            body.single-dry_recipe .dcv5-quick-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }
        }

        @media (max-width: 640px) {
            body.single-dry_recipe .dcv5-quick-strip {
                grid-template-columns: 1fr !important;
            }

            body.single-dry_recipe .dcv5-quick-card {
                min-height: auto !important;
            }
        }
    </style>
    <?php
}, 1700);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-safety-marker.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5.14 Safety Marker
 * Description: Dodaje stvarne HTML semaforske oznake u kartice sigurnosnog semafora.
 * Version: 0.5.14
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        body.single-dry_recipe #sigurnost .dcv5-safety-card {
            position: relative !important;
            padding-bottom: 64px !important;
            overflow: visible !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-marker {
            position: absolute !important;
            left: 18px !important;
            bottom: 18px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 9px !important;
            z-index: 999 !important;
            pointer-events: none !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-dot-real {
            width: 28px !important;
            height: 28px !important;
            border-radius: 999px !important;
            border: 4px solid #fffaf0 !important;
            box-shadow: 0 5px 14px rgba(17, 27, 51, .28) !important;
            flex: 0 0 auto !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-label-real {
            font-size: 12px !important;
            line-height: 1 !important;
            font-weight: 900 !important;
            letter-spacing: .045em !important;
            text-transform: uppercase !important;
            color: #6b5c38 !important;
            background: rgba(255, 250, 240, .9) !important;
            border: 1px solid #ead6a5 !important;
            border-radius: 999px !important;
            padding: 6px 9px !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.green .dcv5-safety-dot-real {
            background: #2e9b57 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.yellow .dcv5-safety-dot-real {
            background: #f2c230 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.red .dcv5-safety-dot-real {
            background: #c93636 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.green {
            border-left: 6px solid #2e9b57 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.yellow {
            border-left: 6px solid #f2c230 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.red {
            border-left: 6px solid #c93636 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('#sigurnost .dcv5-safety-card');

            cards.forEach(function (card) {
                if (card.querySelector('.dcv5-safety-marker')) {
                    return;
                }

                let label = '';
                if (card.classList.contains('green')) {
                    label = 'normalno';
                } else if (card.classList.contains('yellow')) {
                    label = 'oprez';
                } else if (card.classList.contains('red')) {
                    label = 'odbaci';
                }

                if (!label) {
                    return;
                }

                const marker = document.createElement('div');
                marker.className = 'dcv5-safety-marker';

                const dot = document.createElement('span');
                dot.className = 'dcv5-safety-dot-real';

                const text = document.createElement('span');
                text.className = 'dcv5-safety-label-real';
                text.textContent = label;

                marker.appendChild(dot);
                marker.appendChild(text);
                card.appendChild(marker);
            });
        });
    </script>
    <?php
}, 99999);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-view-v05-sensory-polish.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe View v0.5.15 Sensory Polish
 * Description: Poboljšava prikaz senzorskog profila proizvoda u web receptu.
 * Version: 0.5.15
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.5.15 — Senzorski profil kao čitljiva karta proizvoda.
         */

        body.single-dry_recipe #profil .dcv5-sensory-summary {
            margin: 0 0 18px !important;
            padding: 15px 17px !important;
            border-radius: 16px !important;
            border: 1px solid #e4c98c !important;
            background: #fff8e8 !important;
            color: #334059 !important;
            font-size: 15.5px !important;
            line-height: 1.65 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-summary strong {
            color: #10182d !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 {
            position: relative !important;
            display: grid !important;
            grid-template-columns: 120px minmax(260px, 1fr) 72px minmax(230px, .9fr) !important;
            gap: 14px !important;
            align-items: center !important;
            padding: 15px 16px !important;
            margin-bottom: 10px !important;
            border-radius: 17px !important;
            border: 1px solid #e6cf98 !important;
            background: #fffdf7 !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 > * {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 > *:first-child {
            color: #10182d !important;
            font-size: 15.5px !important;
            font-weight: 900 !important;
            line-height: 1.25 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 .dcv5-sensory-meaning {
            display: flex !important;
            flex-direction: column !important;
            gap: 3px !important;
            padding-left: 14px !important;
            border-left: 1px solid #ead6a5 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-meaning strong {
            color: #111b33 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            font-weight: 900 !important;
            letter-spacing: .035em !important;
            text-transform: uppercase !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-meaning span {
            color: #46536b !important;
            font-size: 14px !important;
            line-height: 1.45 !important;
            font-weight: 500 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale {
            grid-column: 2 / 4 !important;
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 6px !important;
            margin-top: 6px !important;
            color: #8b7440 !important;
            font-size: 10.5px !important;
            font-weight: 800 !important;
            letter-spacing: .035em !important;
            text-transform: uppercase !important;
            opacity: .9 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(1) {
            text-align: left !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(2) {
            text-align: center !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(3) {
            text-align: right !important;
        }

        @media (max-width: 980px) {
            body.single-dry_recipe #profil .dcv5-sensory-row-v2 {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }

            body.single-dry_recipe #profil .dcv5-sensory-meaning {
                padding-left: 0 !important;
                border-left: 0 !important;
            }

            body.single-dry_recipe #profil .dcv5-sensory-scale {
                grid-column: 1 !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profile = document.querySelector('#profil');
            if (!profile) {
                return;
            }

            if (!profile.querySelector('.dcv5-sensory-summary')) {
                const note = profile.querySelector('.dcv5-section-note');
                const summary = document.createElement('div');
                summary.className = 'dcv5-sensory-summary';
                summary.innerHTML = '<strong>Senzorski potpis:</strong> paprikasto-dimljena kobasica srednje masnoće, blage do umjerene ljutine i čvrstog, ali ne presuhog presjeka.';
                if (note && note.parentNode) {
                    note.insertAdjacentElement('afterend', summary);
                } else {
                    profile.insertBefore(summary, profile.firstChild);
                }
            }

            const meanings = {
                'Paprika': {
                    level: 'izražena',
                    text: 'Paprika je vodeća aroma i nosi regionalni karakter proizvoda.'
                },
                'Dim': {
                    level: 'srednje izražen',
                    text: 'Dim je prisutan, ali ne smije prekriti meso i papriku.'
                },
                'Ljutina': {
                    level: 'blaga do umjerena',
                    text: 'Ljutina daje živost, ali ne dominira zalogajem.'
                },
                'Slanoća': {
                    level: 'uravnotežena',
                    text: 'Slanost treba čuvati proizvod bez grubog slanog dojma.'
                },
                'Masnoća': {
                    level: 'srednja',
                    text: 'Masnoća daje sočnost i mekši presjek bez masnog dojma.'
                },
                'Tekstura': {
                    level: 'kompaktna',
                    text: 'Presjek treba biti povezan, rezan čistim rubom i bez šupljina.'
                }
            };

            const candidates = Array.from(profile.querySelectorAll('div, article, li'))
                .filter(function (el) {
                    return /\b\d{1,2}\/10\b/.test(el.textContent || '') &&
                           !el.classList.contains('dcv5-sensory-row-v2') &&
                           !el.closest('.dcv5-sensory-summary');
                });

            candidates.forEach(function (row) {
                const txt = row.textContent || '';
                const key = Object.keys(meanings).find(function (name) {
                    return txt.toLowerCase().includes(name.toLowerCase());
                });

                if (!key) {
                    return;
                }

                row.classList.add('dcv5-sensory-row-v2');

                if (!row.querySelector('.dcv5-sensory-meaning')) {
                    const meaning = document.createElement('div');
                    meaning.className = 'dcv5-sensory-meaning';
                    meaning.innerHTML = '<strong>' + meanings[key].level + '</strong><span>' + meanings[key].text + '</span>';
                    row.appendChild(meaning);
                }

                if (!row.querySelector('.dcv5-sensory-scale')) {
                    const scale = document.createElement('div');
                    scale.className = 'dcv5-sensory-scale';
                    scale.innerHTML = '<span>blago</span><span>srednje</span><span>izraženo</span>';
                    row.appendChild(scale);
                }
            });
        });
    </script>
    <?php
}, 99999);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-v06-feature-pack.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe v0.6.0 Feature Pack
 * Description: Dodatni feature pack za kanonski web prikaz recepta HR-SL-007.
 * Version: 0.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dcv6_feature_pack_content', 1250);

function dcv6_feature_pack_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);

    if (!dcv5_get_recipe_profile($post_id, $code) || strpos($content, 'dcv5-recipe') === false) {
        return $content;
    }

    $content = dcv6_add_nav_links($content);
    $content = dcv6_insert_before_layout($content, dcv6_render_work_summary());
    $content = dcv6_insert_after_section($content, 'klima', dcv6_render_micro_variations());
    $content = dcv6_insert_after_section($content, 'posluzivanje', dcv6_render_related_recipes($post_id));
    $content = dcv6_insert_after_section($content, 'posluzivanje', dcv6_render_compare_block());
    $content = dcv6_insert_before_section_close($content, 'dnevnik', dcv6_render_digital_batch_diary());
    $content = dcv6_insert_before_main_close($content, dcv6_render_admin_block($post_id));
    $content .= dcv6_render_enhanced_schema($post_id);

    return $content;
}

function dcv6_add_nav_links($content) {
    $content = str_replace(
        '<h3>Sadržaj recepta</h3>',
        '<h3>Sadržaj recepta</h3><a href="#radni-sazetak">Radni sažetak</a>',
        $content
    );

    $content = str_replace(
        '<a href="#kronologija">Procesna kronologija</a>',
        '<a href="#kronologija">Procesna kronologija</a><a href="#varijacije">Varijacije</a>',
        $content
    );

    $content = str_replace(
        '<a href="#dnevnik">Dnevnik šarže</a>',
        '<a href="#povezani">Povezani recepti</a><a href="#usporedba">Usporedba</a><a href="#dnevnik">Dnevnik šarže</a>',
        $content
    );

    return $content;
}

function dcv6_insert_before_layout($content, $html) {
    if (!$html) {
        return $content;
    }

    return str_replace('<div class="dcv5-layout">', $html . "\n" . '<div class="dcv5-layout">', $content);
}

function dcv6_insert_after_section($content, $section_id, $html) {
    if (!$html) {
        return $content;
    }

    $pattern = '/(<section class="dcv5-panel" id="' . preg_quote($section_id, '/') . '">.*?<\/section>)/s';

    return preg_replace($pattern, '$1' . "\n" . $html, $content, 1);
}

function dcv6_insert_before_section_close($content, $section_id, $html) {
    if (!$html) {
        return $content;
    }

    $pattern = '/(<section class="dcv5-panel" id="' . preg_quote($section_id, '/') . '">.*?)(<\/section>)/s';

    return preg_replace($pattern, '$1' . "\n" . $html . "\n" . '$2', $content, 1);
}

function dcv6_insert_before_main_close($content, $html) {
    if (!$html) {
        return $content;
    }

    return str_replace('</main>', $html . "\n" . '</main>', $content);
}

function dcv6_render_work_summary() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-work-summary" id="radni-sazetak">
        <h2><span>✓</span>Radni sažetak prije izrade</h2>
        <p class="dcv5-section-note">
            Ovaj blok služi kao brza provjera prije rada. Ako korisnik pročita samo jedan dio prije početka,
            neka pročita ovaj.
        </p>

        <div class="dcv6-summary-grid">
            <article class="dcv6-summary-card">
                <h3>Za koga je recept</h3>
                <p>Domaća proizvodnja, mala šarža i tradicionalni slavonski stil. Recept je pisan za 10 kg mesne mase i za korisnika koji želi kontroliran, ponovljiv postupak.</p>
            </article>

            <article class="dcv6-summary-card">
                <h3>Najvažnije kontrole</h3>
                <p>Hladna sirovina, ravnomjerno miješanje, punjenje bez zraka, mirno predsušenje, blagi hladni dim i sporo sušenje bez tvrde kore.</p>
            </article>

            <article class="dcv6-summary-card">
                <h3>Ne preskači</h3>
                <p>Ne preskakati predsušenje prije dima, kontrolu mirisa, provjeru ljepljivosti površine i bilježenje gubitka mase tijekom sušenja.</p>
            </article>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_micro_variations() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-variations" id="varijacije">
        <h2><span>V</span>Mikroregionalne varijacije</h2>
        <p class="dcv5-section-note">
            Varijacije ne mijenjaju osnovnu sigurnosnu logiku recepta. One služe za razumijevanje lokalnog stila, začinskog naglaska i ritma dimljenja.
        </p>

        <div class="dcv6-card-grid-three">
            <article class="dcv6-info-card">
                <h3>Slavonska kućna varijanta</h3>
                <p>Uravnotežen odnos paprike, dima i masnoće. Naglasak je na čistom mirisu, stabilnom presjeku i umjerenom dimljenju.</p>
            </article>

            <article class="dcv6-info-card">
                <h3>Baranjski naglasak</h3>
                <p>Može imati puniji začinski dojam i izraženiju papriku, ali bez pretjerivanja s ljutinom. Sušenje mora ostati postupno.</p>
            </article>

            <article class="dcv6-info-card">
                <h3>Srijemski stil</h3>
                <p>Često ide prema nešto izraženijem dimnom karakteru i duljem čuvanju, uz isti oprez: dim mora biti tanak, suh i mirisan.</p>
            </article>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_related_recipes($current_post_id) {
    $related = [
        'HR-SL-001' => 'Slavonski kulen',
        'HR-SL-005' => 'Slavonska domaća kobasica',
        'HR-SL-006' => 'Srijemska kobasica',
        'HR-SL-030' => 'Brodska kobasica',
        'HR-SL-031' => 'Osječka kobasica',
        'HR-SL-037' => 'Vinkovačka kobasica',
        'HR-SL-020' => 'Vinkovačka šunka',
    ];

    $found = [];

    foreach ($related as $code => $fallback_title) {
        $q = new WP_Query([
            'post_type' => 'dry_recipe',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_dry_recipe_id',
                    'value' => $code,
                    'compare' => '=',
                ],
            ],
        ]);

        if ($q->have_posts()) {
            $q->the_post();
            if (get_the_ID() !== $current_post_id) {
                $found[] = [
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'code' => $code,
                ];
            }
        }
        wp_reset_postdata();
    }

    if (!$found) {
        return '';
    }

    ob_start();
    ?>
    <section class="dcv5-panel dcv6-related" id="povezani">
        <h2><span>R</span>Povezani recepti</h2>
        <p class="dcv5-section-note">
            Povezani recepti pomažu usporediti stil, začine, dimljenje i trajanje izrade unutar iste regionalne obitelji.
        </p>

        <div class="dcv6-related-list">
            <?php foreach ($found as $item) : ?>
                <a class="dcv6-related-link" href="<?php echo esc_url($item['url']); ?>">
                    <span><?php echo esc_html($item['code']); ?></span>
                    <strong><?php echo esc_html($item['title']); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_compare_block() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-compare" id="usporedba">
        <h2><span>≠</span>Usporedba recepta</h2>
        <p class="dcv5-section-note">
            Ovaj recept može se spremiti za buduću usporedbu s drugim receptima. Usporedba će koristiti omjer mesa i masnoće, začinski profil, dimljenje, trajanje, kalibar crijeva i gubitak mase.
        </p>

        <div class="dcv6-compare-box">
            <button type="button" class="dcv6-compare-button" data-recipe-code="HR-SL-007" data-recipe-title="Ratarske kobasice">
                Dodaj Ratarske kobasice u usporedbu
            </button>
            <p class="dcv6-compare-status" aria-live="polite"></p>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_digital_batch_diary() {
    ob_start();
    ?>
    <div class="dcv6-digital-diary">
        <h3>Digitalni dnevnik šarže</h3>
        <p>Unesi početnu i trenutnu masu. Sustav izračunava gubitak mase i pomaže pratiti sušenje.</p>

        <div class="dcv6-diary-grid">
            <label>
                Datum početka
                <input type="date" class="dcv6-diary-input" id="dcv6-start-date">
            </label>

            <label>
                Početna masa (kg)
                <input type="number" step="0.001" min="0" class="dcv6-diary-input" id="dcv6-start-mass" placeholder="10.000">
            </label>

            <label>
                Trenutna masa (kg)
                <input type="number" step="0.001" min="0" class="dcv6-diary-input" id="dcv6-current-mass" placeholder="7.300">
            </label>

            <label>
                Datum mjerenja
                <input type="date" class="dcv6-diary-input" id="dcv6-check-date">
            </label>
        </div>

        <div class="dcv6-diary-result" id="dcv6-diary-result">
            Gubitak mase pojavit će se nakon unosa vrijednosti.
        </div>

        <textarea class="dcv6-diary-notes" id="dcv6-diary-notes" rows="4" placeholder="Bilješke: miris, površina, boja, vlaga, dimljenje, korekcije uvjeta..."></textarea>

        <div class="dcv6-diary-actions">
            <button type="button" class="dcv6-diary-save">Spremi bilješke u pregledniku</button>
            <button type="button" class="dcv6-diary-clear">Očisti dnevnik</button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function dcv6_render_admin_block($post_id) {
    if (!current_user_can('manage_options')) {
        return '';
    }

    $code = get_post_meta($post_id, '_dry_recipe_id', true);
    $thumb_id = get_post_thumbnail_id($post_id);
    $status = get_post_status($post_id);

    ob_start();
    ?>
    <section class="dcv5-panel dcv6-admin-block" id="admin-status">
        <h2><span>A</span>Urednički status</h2>
        <p class="dcv5-section-note">
            Ovaj blok vide samo administratori. Ne prikazuje se javnim korisnicima.
        </p>

        <div class="dcv6-admin-grid">
            <div><strong>WP ID</strong><span><?php echo esc_html($post_id); ?></span></div>
            <div><strong>Recept ID</strong><span><?php echo esc_html($code); ?></span></div>
            <div><strong>Status objave</strong><span><?php echo esc_html($status); ?></span></div>
            <div><strong>Featured image</strong><span><?php echo $thumb_id ? esc_html($thumb_id) : 'nije postavljena'; ?></span></div>
            <div><strong>View standard</strong><span>Drycured Recipe View v0.6.0</span></div>
            <div><strong>Javni urednički podaci</strong><span>sakriveni</span></div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_enhanced_schema($post_id) {
    $code = get_post_meta($post_id, '_dry_recipe_id', true);
    $recipe = dcv5_get_recipe_profile($post_id, $code);

    if (!$recipe) {
        return '';
    }

    $image = get_the_post_thumbnail_url($post_id, 'large');

    $ingredients = [];
    foreach (['materials', 'spices', 'liquids'] as $group) {
        if (empty($recipe[$group]) || !is_array($recipe[$group])) {
            continue;
        }

        foreach ($recipe[$group] as $item) {
            $ingredients[] = trim(($item['amount'] ?? '') . ' ' . ($item['name'] ?? ''));
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        '@id' => get_permalink($post_id) . '#drycured-recipe-v110',
        'name' => $recipe['title'],
        'description' => $recipe['lead'],
        'image' => $image ? [$image] : [],
        'recipeYield' => $recipe['quick'][0]['value'] ?? '10 kg',
        'recipeCuisine' => 'Hrvatska',
        'recipeCategory' => 'Suhomesnati proizvod',
        'keywords' => strtolower($recipe['title']) . ', suhomesnati proizvod, hladno dimljenje, sušenje, zrenje',
        'totalTime' => 'P60D',
        'recipeIngredient' => $ingredients,
        'recipeInstructions' => array_map(function ($step) {
            return [
                '@type' => 'HowToStep',
                'name' => $step['title'] ?? '',
                'text' => trim(($step['text'] ?? '') . ' Kritično: ' . ($step['critical'] ?? '')),
            ];
        }, $recipe['timeline'] ?? []),
    ];

    return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

add_action('wp_footer', 'dcv6_feature_pack_footer', 100000);

function dcv6_feature_pack_footer() {
    ?>
    <style>
        body.single-dry_recipe .dcv6-summary-grid,
        body.single-dry_recipe .dcv6-card-grid-three {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }

        body.single-dry_recipe .dcv6-summary-card,
        body.single-dry_recipe .dcv6-info-card,
        body.single-dry_recipe .dcv6-compare-box,
        body.single-dry_recipe .dcv6-digital-diary,
        body.single-dry_recipe .dcv6-admin-block .dcv6-admin-grid > div {
            background: #fffdf7 !important;
            border: 1px solid #e6cf98 !important;
            border-radius: 18px !important;
            padding: 18px 19px !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe .dcv6-summary-card h3,
        body.single-dry_recipe .dcv6-info-card h3,
        body.single-dry_recipe .dcv6-digital-diary h3 {
            margin: 0 0 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.32 !important;
        }

        body.single-dry_recipe .dcv6-summary-card p,
        body.single-dry_recipe .dcv6-info-card p,
        body.single-dry_recipe .dcv6-digital-diary p {
            margin: 0 !important;
            color: #43506a !important;
            font-size: 15.3px !important;
            line-height: 1.68 !important;
        }

        body.single-dry_recipe .dcv6-related-list {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        body.single-dry_recipe .dcv6-related-link {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 15px 17px !important;
            border-radius: 16px !important;
            border: 1px solid #e6cf98 !important;
            background: #fffdf7 !important;
            text-decoration: none !important;
            color: #10182d !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv6-related-link span {
            display: inline-flex !important;
            min-width: 76px !important;
            justify-content: center !important;
            padding: 7px 9px !important;
            border-radius: 999px !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv6-related-link strong {
            font-size: 15.5px !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe .dcv6-compare-button,
        body.single-dry_recipe .dcv6-diary-save,
        body.single-dry_recipe .dcv6-diary-clear {
            border: 1px solid #111b33 !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            border-radius: 999px !important;
            padding: 11px 16px !important;
            font-weight: 900 !important;
            cursor: pointer !important;
        }

        body.single-dry_recipe .dcv6-diary-clear {
            background: #f1dfb6 !important;
            border-color: #d5b46b !important;
            color: #111b33 !important;
        }

        body.single-dry_recipe .dcv6-compare-status {
            margin: 12px 0 0 !important;
            color: #43506a !important;
            font-weight: 800 !important;
        }

        body.single-dry_recipe .dcv6-diary-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 12px !important;
            margin: 16px 0 !important;
        }

        body.single-dry_recipe .dcv6-diary-grid label {
            color: #10182d !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe .dcv6-diary-input,
        body.single-dry_recipe .dcv6-diary-notes {
            width: 100% !important;
            margin-top: 7px !important;
            border: 1px solid #e2c98e !important;
            border-radius: 12px !important;
            padding: 10px 11px !important;
            background: #fffaf0 !important;
            color: #10182d !important;
        }

        body.single-dry_recipe .dcv6-diary-result {
            margin: 12px 0 !important;
            padding: 14px 15px !important;
            border-radius: 14px !important;
            border: 1px solid #e2c98e !important;
            background: #fff8e8 !important;
            color: #10182d !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv6-diary-actions {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            margin-top: 12px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid strong {
            display: block !important;
            color: #8a733c !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
            margin-bottom: 5px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid span {
            color: #10182d !important;
            font-weight: 900 !important;
        }

        @media print {
            body.single-dry_recipe .site-header,
            body.single-dry_recipe .site-footer,
            body.single-dry_recipe .dcv5-side-panel,
            body.single-dry_recipe .dcv5-hero-media,
            body.single-dry_recipe .dcv5-actions,
            body.single-dry_recipe #profil,
            body.single-dry_recipe #varijacije,
            body.single-dry_recipe #povezani,
            body.single-dry_recipe #usporedba,
            body.single-dry_recipe .dcv6-admin-block,
            body.single-dry_recipe #wpadminbar,
            body.single-dry_recipe .dc-floating-compare,
            body.single-dry_recipe .dcv5-btn {
                display: none !important;
            }

            body.single-dry_recipe,
            body.single-dry_recipe .site-content {
                background: #fff !important;
            }

            body.single-dry_recipe .dcv5-recipe {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
            }

            body.single-dry_recipe .dcv5-panel,
            body.single-dry_recipe .dcv5-hero,
            body.single-dry_recipe .dcv5-quick-card {
                box-shadow: none !important;
                break-inside: avoid !important;
            }
        }

        @media (max-width: 980px) {
            body.single-dry_recipe .dcv6-summary-grid,
            body.single-dry_recipe .dcv6-card-grid-three,
            body.single-dry_recipe .dcv6-related-list,
            body.single-dry_recipe .dcv6-diary-grid,
            body.single-dry_recipe .dcv6-admin-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        (function () {
            const profile = <?php
                $dcv6_footer_post_id = get_queried_object_id();
                $dcv6_footer_code = get_post_meta($dcv6_footer_post_id, '_dry_recipe_id', true);
                $dcv6_footer_profile = function_exists('dcv5_get_recipe_profile') ? dcv5_get_recipe_profile($dcv6_footer_post_id, $dcv6_footer_code) : null;
                echo wp_json_encode(dcv5_recipe_js_profile($dcv6_footer_profile), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            ?>;

            if (!profile || !profile.code) {
                return;
            }

            window.drycuredRecipeProfile = profile;
            try {
                localStorage.setItem('drycured_recipe_profile_' + profile.code, JSON.stringify(profile));
            } catch (e) {}

            document.addEventListener('DOMContentLoaded', function () {
                const compareButton = document.querySelector('.dcv6-compare-button');
                if (compareButton) {
                    compareButton.addEventListener('click', function () {
                        let list = [];
                        try {
                            list = JSON.parse(localStorage.getItem('drycured_compare_recipes') || '[]');
                        } catch (e) {
                            list = [];
                        }

                        const item = {
                            code: compareButton.dataset.recipeCode,
                            title: compareButton.dataset.recipeTitle,
                            url: window.location.href
                        };

                        if (!list.some(x => x.code === item.code)) {
                            list.push(item);
                        }

                        localStorage.setItem('drycured_compare_recipes', JSON.stringify(list));

                        const status = document.querySelector('.dcv6-compare-status');
                        if (status) {
                            status.textContent = 'Recept je dodan u pripremu za usporedbu.';
                        }
                    });
                }

                const startMass = document.getElementById('dcv6-start-mass');
                const currentMass = document.getElementById('dcv6-current-mass');
                const result = document.getElementById('dcv6-diary-result');
                const notes = document.getElementById('dcv6-diary-notes');
                const startDate = document.getElementById('dcv6-start-date');
                const checkDate = document.getElementById('dcv6-check-date');

                function updateLoss() {
                    if (!startMass || !currentMass || !result) return;

                    const start = parseFloat(String(startMass.value).replace(',', '.'));
                    const current = parseFloat(String(currentMass.value).replace(',', '.'));

                    if (!start || !current || current > start) {
                        result.textContent = 'Gubitak mase pojavit će se nakon ispravnog unosa vrijednosti.';
                        return;
                    }

                    const lossKg = start - current;
                    const lossPct = (lossKg / start) * 100;

                    let status = 'u tijeku';
                    if (lossPct >= 25 && lossPct <= 30) {
                        status = 'ciljani raspon za ovaj recept';
                    } else if (lossPct > 30) {
                        status = 'iznad ciljanog raspona; provjeri tvrdoću i presjek';
                    }

                    result.textContent = 'Gubitak mase: ' + lossKg.toFixed(3).replace('.', ',') + ' kg · ' + lossPct.toFixed(1).replace('.', ',') + ' % · ' + status;
                }

                [startMass, currentMass].forEach(function (el) {
                    if (el) el.addEventListener('input', updateLoss);
                });

                const key = 'drycured_batch_diary_' + profile.code;

                function loadDiary() {
                    try {
                        const data = JSON.parse(localStorage.getItem(key) || '{}');
                        if (startDate && data.startDate) startDate.value = data.startDate;
                        if (checkDate && data.checkDate) checkDate.value = data.checkDate;
                        if (startMass && data.startMass) startMass.value = data.startMass;
                        if (currentMass && data.currentMass) currentMass.value = data.currentMass;
                        if (notes && data.notes) notes.value = data.notes;
                        updateLoss();
                    } catch (e) {}
                }

                function saveDiary() {
                    const data = {
                        startDate: startDate ? startDate.value : '',
                        checkDate: checkDate ? checkDate.value : '',
                        startMass: startMass ? startMass.value : '',
                        currentMass: currentMass ? currentMass.value : '',
                        notes: notes ? notes.value : ''
                    };
                    localStorage.setItem(key, JSON.stringify(data));
                    if (result) result.textContent = 'Dnevnik je spremljen u ovom pregledniku.';
                }

                function clearDiary() {
                    localStorage.removeItem(key);
                    if (startDate) startDate.value = '';
                    if (checkDate) checkDate.value = '';
                    if (startMass) startMass.value = '';
                    if (currentMass) currentMass.value = '';
                    if (notes) notes.value = '';
                    if (result) result.textContent = 'Dnevnik je očišćen.';
                }

                const saveBtn = document.querySelector('.dcv6-diary-save');
                const clearBtn = document.querySelector('.dcv6-diary-clear');

                if (saveBtn) saveBtn.addEventListener('click', saveDiary);
                if (clearBtn) clearBtn.addEventListener('click', clearDiary);

                loadDiary();

                if (new URLSearchParams(window.location.search).get('recipe') === profile.code) {
                    try {
                        localStorage.setItem('drycured_calculator_requested_recipe', profile.code);
                    } catch (e) {}
                }
            });
        })();
    </script>
    <?php
}


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-v06-content-cleanup.php
 * ============================================================ */

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

    if (!dcv5_get_recipe_profile($post_id, $code) || strpos($content, 'dcv5-recipe') === false) {
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

function dcv62_recipe_nav_registry() {
    return [
        'HR-SL-001' => [
            'title' => 'Slavonski kulen (PDO EU)',
            'slug' => 'hr-sl-001-slavonski-kulen-pdo-eu',
        ],
        'HR-SL-007' => [
            'title' => 'Ratarske kobasice',
            'slug' => 'hr-sl-007-ratarske-kobasice',
        ],
        'HR-SL-020' => [
            'title' => 'Vinkovačka šunka — suho soljena varijanta',
            'slug' => 'hr-sl-020-vinkovacka-sunka-suho-soljena-varijanta',
        ],
    ];
}

function dcv62_prev_next_recipe_nav() {
    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);
    $registry = dcv62_recipe_nav_registry();
    $codes = array_keys($registry);
    $index = array_search($code, $codes, true);

    if ($index === false) {
        return '';
    }

    $prev = $index > 0 ? $registry[$codes[$index - 1]] : null;
    $next = $index < count($codes) - 1 ? $registry[$codes[$index + 1]] : null;

    if (!$prev && !$next) {
        return '';
    }

    ob_start();
    ?>
    <nav class="dcv62-prevnext" aria-label="Navigacija između recepata">
        <?php if ($prev) : ?>
            <a class="dcv62-prevnext-link dcv62-prev" href="<?php echo esc_url(dcv62_recipe_link_by_slug($prev['slug'], $prev['slug'])); ?>">
                <span>← Prethodni recept</span>
                <strong><?php echo esc_html($prev['title']); ?></strong>
            </a>
        <?php else : ?>
            <span class="dcv62-prevnext-spacer" aria-hidden="true"></span>
        <?php endif; ?>

        <?php if ($next) : ?>
            <a class="dcv62-prevnext-link dcv62-next" href="<?php echo esc_url(dcv62_recipe_link_by_slug($next['slug'], $next['slug'])); ?>">
                <span>Sljedeći recept →</span>
                <strong><?php echo esc_html($next['title']); ?></strong>
            </a>
        <?php else : ?>
            <span class="dcv62-prevnext-spacer" aria-hidden="true"></span>
        <?php endif; ?>
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


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-v06-header-restore.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe v0.6.1 Header Restore
 * Description: Vraća standardni prikaz glavnog site zaglavlja na single dry_recipe stranicama.
 * Version: 0.6.1
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.6.1 — vraćanje glavnog zaglavlja stranice.
         * Receptni layout ostaje proširen, ali Astra header se vraća u normalan centrirani okvir.
         */

        body.single-dry_recipe .site-header .ast-container,
        body.single-dry_recipe .main-header-bar .ast-container,
        body.single-dry_recipe .ast-primary-header-bar .ast-container,
        body.single-dry_recipe .ast-above-header-bar .ast-container,
        body.single-dry_recipe .ast-below-header-bar .ast-container {
            max-width: 1240px !important;
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
            padding-left: 20px !important;
            padding-right: 20px !important;
            background: transparent !important;
        }

        body.single-dry_recipe .site-header {
            background: #ffffff !important;
        }

        body.single-dry_recipe .site-header .site-branding,
        body.single-dry_recipe .site-header .main-navigation {
            position: relative !important;
            z-index: 10 !important;
        }

        /*
         * Receptni sadržaj ostaje na sadašnjem dobrom v0.6 prikazu.
         */
        body.single-dry_recipe .site-content .ast-container,
        body.single-dry_recipe .content-area,
        body.single-dry_recipe main.site-main,
        body.single-dry_recipe article,
        body.single-dry_recipe .entry-content {
            max-width: none !important;
            width: 100% !important;
        }
    </style>
    <?php
}, 200000);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-v06-prevnext-position.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe v0.6.3 Prev/Next Position
 * Description: Premješta prethodni/sljedeći recept ispod glavnog recipe layouta i skriva defaultnu WP/Astra navigaciju.
 * Version: 0.6.3
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.6.3 — prethodni/sljedeći recept ide ispod cijelog glavnog layouta.
         */

        body.single-dry_recipe .dcv5-recipe > .dcv62-prevnext {
            width: 100% !important;
            max-width: none !important;
            margin: 28px 0 0 !important;
        }

        body.single-dry_recipe .dcv62-prevnext {
            clear: both !important;
        }

        /*
         * Sakrij defaultnu WP/Astra single post navigaciju.
         * Ne diramo našu dcv62 navigaciju.
         */
        body.single-dry_recipe .post-navigation,
        body.single-dry_recipe nav.navigation.post-navigation,
        body.single-dry_recipe .site-main > .navigation.post-navigation,
        body.single-dry_recipe .ast-single-post-navigation,
        body.single-dry_recipe .ast-post-navigation,
        body.single-dry_recipe .single-navigation {
            display: none !important;
        }

        body.single-dry_recipe .dcv62-prevnext {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 18px !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link {
            min-height: 86px !important;
            justify-content: center !important;
        }

        @media (max-width: 760px) {
            body.single-dry_recipe .dcv62-prevnext {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const recipe = document.querySelector('.dcv5-recipe');
            const layout = document.querySelector('.dcv5-layout');
            const nav = document.querySelector('.dcv62-prevnext');

            if (!recipe || !layout || !nav) {
                return;
            }

            /*
             * Ako je navigacija umetnuta unutar glavnog stupca,
             * premjesti je iza cijelog .dcv5-layout bloka.
             */
            if (layout.contains(nav)) {
                layout.insertAdjacentElement('afterend', nav);
            }

            /*
             * Sigurnosno ukloni defaultne navigacije koje tema može renderirati kasnije.
             */
            document.querySelectorAll(
                '.post-navigation, nav.navigation.post-navigation, .ast-single-post-navigation, .ast-post-navigation, .single-navigation'
            ).forEach(function (el) {
                if (!el.classList.contains('dcv62-prevnext')) {
                    el.remove();
                }
            });
        });
    </script>
    <?php
}, 300000);


/* ============================================================
 * SOURCE: wp-content/mu-plugins/drycured-recipe-v06-nav-polish.php
 * ============================================================ */

/**
 * Plugin Name: Drycured Recipe v0.6.4 Navigation Polish
 * Description: Profesionalno uređuje donju prethodni/sljedeći navigaciju i pomiče plutajući gumb usporedbe.
 * Version: 0.6.4
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        /*
         * v0.6.4 — donja navigacija mora izgledati kao završni dio recepta,
         * a ne kao još jedna velika kartica.
         */

        body.single-dry_recipe .dcv62-prevnext {
            width: 100% !important;
            max-width: 100% !important;
            margin: 30px auto 18px !important;
            padding: 0 !important;
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) !important;
            gap: 18px !important;
            clear: both !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link {
            min-height: 74px !important;
            padding: 18px 22px !important;
            border-radius: 22px !important;
            border: 1px solid #d9bc78 !important;
            background:
                linear-gradient(135deg, rgba(255,250,240,.98), rgba(248,237,210,.96)) !important;
            color: #10182d !important;
            text-decoration: none !important;
            box-shadow: 0 14px 30px rgba(25, 32, 48, .075) !important;
            display: flex !important;
            justify-content: center !important;
            position: relative !important;
            overflow: hidden !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link::before {
            content: "" !important;
            position: absolute !important;
            inset: 0 !important;
            background: radial-gradient(circle at top left, rgba(216,166,63,.18), transparent 36%) !important;
            opacity: .9 !important;
            pointer-events: none !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 18px 38px rgba(25, 32, 48, .12) !important;
            border-color: #caa65b !important;
            background:
                linear-gradient(135deg, rgba(255,247,225,1), rgba(246,232,197,1)) !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link span,
        body.single-dry_recipe .dcv62-prevnext-link strong {
            position: relative !important;
            z-index: 2 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link span {
            color: #8a733c !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: .08em !important;
            line-height: 1.1 !important;
        }

        body.single-dry_recipe .dcv62-prevnext-link strong {
            color: #10182d !important;
            font-size: 18px !important;
            line-height: 1.28 !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv62-prev {
            align-items: flex-start !important;
            text-align: left !important;
            padding-left: 54px !important;
        }

        body.single-dry_recipe .dcv62-next {
            align-items: flex-end !important;
            text-align: right !important;
            padding-right: 54px !important;
        }

        body.single-dry_recipe .dcv62-prev::after,
        body.single-dry_recipe .dcv62-next::after {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            width: 34px !important;
            height: 34px !important;
            border-radius: 999px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 18px !important;
            font-weight: 900 !important;
            box-shadow: 0 8px 18px rgba(17,27,51,.18) !important;
            z-index: 3 !important;
        }

        body.single-dry_recipe .dcv62-prev::after {
            content: "←" !important;
            left: 16px !important;
        }

        body.single-dry_recipe .dcv62-next::after {
            content: "→" !important;
            right: 16px !important;
        }

        /*
         * Admin blok ne smije vizualno prekidati javnu navigaciju.
         * Administrator ga i dalje vidi, ali kao tehnički blok ispod svega.
         */
        body.single-dry_recipe .dcv6-admin-block {
            margin-top: 34px !important;
            opacity: .92 !important;
        }

        /*
         * Plutajući gumb za usporedbu — gore u žuti dio stranice,
         * ne zalijepljen za sam donji rub.
         */
        body.single-dry_recipe .dc-floating-compare,
        body.single-dry_recipe .drycured-floating-compare,
        body.single-dry_recipe .dc-compare-floating,
        body.single-dry_recipe [class*="floating"][class*="compare"],
        body.single-dry_recipe [class*="compare"][class*="floating"] {
            bottom: 96px !important;
            right: 36px !important;
            z-index: 9999 !important;
            box-shadow: 0 14px 30px rgba(25,32,48,.16) !important;
        }

        @media (max-width: 760px) {
            body.single-dry_recipe .dcv62-prevnext {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }

            body.single-dry_recipe .dcv62-next {
                text-align: left !important;
                align-items: flex-start !important;
                padding-left: 54px !important;
                padding-right: 22px !important;
            }

            body.single-dry_recipe .dcv62-next::after {
                left: 16px !important;
                right: auto !important;
            }

            body.single-dry_recipe .dc-floating-compare,
            body.single-dry_recipe .drycured-floating-compare,
            body.single-dry_recipe .dc-compare-floating,
            body.single-dry_recipe [class*="floating"][class*="compare"],
            body.single-dry_recipe [class*="compare"][class*="floating"] {
                bottom: 82px !important;
                right: 18px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const recipe = document.querySelector('.dcv5-recipe');
            const nav = document.querySelector('.dcv62-prevnext');
            const admin = document.querySelector('.dcv6-admin-block');

            /*
             * Navigacija treba biti prije admin bloka.
             * Admin blok je tehnički sloj, a prethodni/sljedeći je javni završetak recepta.
             */
            if (recipe && nav && admin && admin.compareDocumentPosition(nav) & Node.DOCUMENT_POSITION_FOLLOWING) {
                admin.insertAdjacentElement('beforebegin', nav);
            }

            /*
             * Ako tema doda vlastiti NEXT/PREV, makni ga.
             */
            document.querySelectorAll(
                '.post-navigation, nav.navigation.post-navigation, .ast-single-post-navigation, .ast-post-navigation, .single-navigation'
            ).forEach(function (el) {
                if (!el.classList.contains('dcv62-prevnext')) {
                    el.remove();
                }
            });
        });
    </script>
    <?php
}, 400000);
