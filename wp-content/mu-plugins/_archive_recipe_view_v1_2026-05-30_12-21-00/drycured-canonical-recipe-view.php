<?php
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
