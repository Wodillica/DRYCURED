<?php
/**
 * Plugin Name: Drycured Recipe MD Fallback Renderer
 * Description: Public fallback renderer for dry_recipe posts without a valid DCV5 profile.
 * Version: 0.3.2
 * Author: drycured.com
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', 'dcmdfr_maybe_render', 1);

function dcmdfr_public_enabled() {
    return (string)get_option('drycured_md_fallback_public_enabled', '0') === '1';
}

function dcmdfr_has_valid_preview_token() {
    $expected = (string)get_option('drycured_md_fallback_preview_token', '');
    $provided = isset($_GET['dc_md_fallback_preview']) ? (string)wp_unslash($_GET['dc_md_fallback_preview']) : '';
    return $expected !== '' && $provided !== '' && hash_equals($expected, $provided);
}

function dcmdfr_clean_display_title($title) {
    $title = trim((string)$title);
    $title = preg_replace('/^\s*\?+\s*/u', '', $title);
    $title = preg_replace('/^\s*(hrvatski|talijanski|španjolski|spanjolski|austrijski|nizozemski|grčki|grcki)\s+tradicionalni\s+recept\s*[-–:]\s*/iu', '', $title);
    $title = preg_replace('/^\s*(tradicionalni\s+recepti|tradicionalni\s+recept)\s*[-–:]\s*/iu', '', $title);
    $title = preg_replace('/\s+/u', ' ', $title);
    return trim($title);
}

function dcmdfr_clean_public_text_line($line) {
    $line = trim((string)$line);
    $line = preg_replace('/^\s*\?+\s*/u', '', $line);
    $line = preg_replace('/privatni radni recept|preview|draft|radna verzija|urednička provjera/iu', '', $line);
    return trim($line);
}

function dcmdfr_recipe_code($post_id) {
    foreach (['_dry_recipe_id','dry_recipe_id','_recipe_id','recipe_id','dry_recipe_code','drycured_source_recipe_id'] as $key) {
        $value = trim((string)get_post_meta($post_id, $key, true));
        if ($value !== '') return $value;
    }
    return '';
}

function dcmdfr_has_valid_dcv5_profile($post_id, $code) {
    if (!function_exists('dcv5_get_recipe_profile')) return false;
    $profile = dcv5_get_recipe_profile($post_id, $code);
    if (!is_array($profile) || empty($profile)) return false;
    $title = trim((string)($profile['title'] ?? $profile['name'] ?? ''));
    $profile_code = trim((string)($profile['code'] ?? $profile['recipe_code'] ?? ''));
    return $title !== '' || $profile_code !== '';
}

function dcmdfr_source_markdown($post_id) {
    $full = trim((string)get_post_meta($post_id, '_dry_recipe_full_markdown', true));
    if ($full !== '') return $full;
    $post = get_post($post_id);
    return $post ? (string)$post->post_content : '';
}

function dcmdfr_terms_line($post_id, $taxonomy) {
    $terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'names']);
    if (is_wp_error($terms) || empty($terms)) return '';
    return implode(' · ', array_map('sanitize_text_field', $terms));
}

function dcmdfr_detect_context($title, $category, $process) {
    $all = mb_strtolower($title . ' ' . $category . ' ' . $process, 'UTF-8');

    $is_fish = preg_match('/riba|losos|laks|lax|siika|šaran|saran|pastrv|koryushka|rakfisk|gravlax|graavilohi|lašiš|lasis|kylmäsavulohi|røkelaks|dimljeni siika/iu', $all);
    $is_cheese = preg_match('/sir|peynir|vacherin|fribourgeois|burduf|sirnate|sir-kobasica/iu', $all);
    $is_whole_cut = preg_match('/šunka|sunka|rohschinken|prosciutto|pršut|prsut|jamón|jamon|jambon|pancet|slanina|lardo|bresaola|lonz|coppa|guanciale|vrat|but|rebra|stelja|chaps|suitsink|spegeskinke/iu', $all);
    $is_fat = preg_match('/lardo|slanina|panceta|guanciale|leđna mast|ledna mast|bacon/iu', $all);
    $is_sausage = preg_match('/kobas|salama|salame|salami|saucisson|wurst|chorizo|loukaniko|kulen|mettwurst|salsiccia|carnati|cârnați|nduja|andouille|landjäger|landjager/iu', $all);

    $family = 'meat';
    if ($is_fish) $family = 'fish';
    elseif ($is_cheese && !$is_sausage && !$is_whole_cut) $family = 'cheese';
    elseif ($is_sausage) $family = 'sausage';
    elseif ($is_fat) $family = 'fat_or_bacon';
    elseif ($is_whole_cut) $family = 'whole_cut';

    $type_label = 'suhomesnati proizvod';
    $raw_material = 'meso';

    if ($family === 'fish') {
        $type_label = 'riblji proizvod za soljenje, mariniranje ili dimljenje';
        $raw_material = 'riba';
    } elseif ($family === 'cheese') {
        $type_label = 'sirni ili punjeni proizvod za zasebnu provjeru';
        $raw_material = 'sir ili punjeno meso';
    } elseif ($family === 'sausage') {
        $type_label = 'suha kobasica ili salama';
        $raw_material = 'svinjetina ili drugo meso prema receptu';
    } elseif ($family === 'fat_or_bacon') {
        $type_label = 'sušena slanina, panceta ili masno tkivo';
        $raw_material = 'svinjetina / masno tkivo';
    } elseif ($family === 'whole_cut') {
        $type_label = 'sušeni ili dimljeni cijeli komad mesa';
        if (preg_match('/šunka|sunka|rohschinken|prosciutto|pršut|prsut|jamón|jamon|jambon|suitsink|spegeskinke/iu', $all)) {
            $type_label = 'sirova, sušena ili dimljena šunka';
        }
        $raw_material = 'cijeli komad mesa';
    }

    return [
        'family' => $family,
        'is_fish' => (bool)$is_fish,
        'is_cheese' => (bool)$is_cheese,
        'is_whole_cut' => (bool)$is_whole_cut,
        'is_sausage' => (bool)$is_sausage,
        'type_label' => $type_label,
        'raw_material' => $raw_material,
    ];
}

function dcmdfr_clean_meta_category($category, $ctx) {
    $category = trim((string)$category);

    if ($category === '' || preg_match('/sušeni zreli sir|suseni zreli sir/iu', $category)) {
        return $ctx['type_label'];
    }

    if (!$ctx['is_fish']) {
        $category = preg_replace('/\s*riba\/morski proizvodi\s*/iu', '', $category);
    }

    if (!$ctx['is_cheese'] && preg_match('/sir/iu', $category)) {
        return $ctx['type_label'];
    }

    return trim($category, " \t\n\r\0\x0B·|,");
}

function dcmdfr_clean_meta_process($process) {
    $process = trim((string)$process);
    $process = preg_replace('/\s*\|\s*/u', ' · ', $process);
    $process = preg_replace('/\s+/u', ' ', $process);
    return $process;
}

function dcmdfr_sanitize_line_for_context($line, $ctx) {
    $line = dcmdfr_clean_public_text_line($line);
    if ($line === '') return '';

    if (preg_match('/^\s*Tip proizvoda\s*:/iu', $line)) {
        return 'Tip proizvoda: ' . $ctx['type_label'] . '.';
    }

    if (preg_match('/^\s*Glavna sirovina\s*:/iu', $line)) {
        return 'Glavna sirovina: ' . $ctx['raw_material'] . '.';
    }

    if (preg_match('/^\s*Crijeva\/omotač\s*:/iu', $line) || preg_match('/^\s*Crijeva\/omotac\s*:/iu', $line)) {
        if ($ctx['family'] === 'whole_cut' || $ctx['family'] === 'fat_or_bacon') {
            return 'Omotač: ne koristi se; proizvod se obrađuje kao cijeli komad mesa.';
        }
        if ($ctx['family'] === 'fish') {
            return 'Omotač: ne koristi se; proizvod se soli, marinira ili dimi kao file ili komad.';
        }
        if ($ctx['family'] === 'sausage') {
            return 'Crijeva/omotač: koristiti prirodno ili odgovarajuće jestivo crijevo te puniti bez zračnih džepova.';
        }
    }

    if (!$ctx['is_fish']) {
        $line = preg_replace('/svinjetina\/svinjski proizvodi,\s*riba\/morski proizvodi/iu', $ctx['raw_material'], $line);
        $line = preg_replace('/,\s*riba\/morski proizvodi/iu', '', $line);
        $line = preg_replace('/riba\/morski proizvodi,\s*/iu', '', $line);
        $line = preg_replace('/riba\/morski proizvodi/iu', $ctx['raw_material'], $line);
        $line = preg_replace('/\briba\b|morski proizvodi/iu', $ctx['raw_material'], $line);
    }

    if (!$ctx['is_cheese']) {
        $line = preg_replace('/sušeni zreli sir|suseni zreli sir/iu', $ctx['type_label'], $line);
        $line = preg_replace('/sirni proizvod|sirnate kobasice/iu', $ctx['type_label'], $line);
    }

    if (($ctx['family'] === 'whole_cut' || $ctx['family'] === 'fat_or_bacon') && preg_match('/puniti bez zračnih džepova|puniti bez zracnih dzepova/iu', $line)) {
        $line = 'Oblikovanje: komad mora biti ravnomjerno nasoljen, pravilno okretan i sušen bez zatvaranja površine.';
    }

    if ($ctx['family'] === 'sausage') {
        $line = preg_replace('/crijeva\/omotač:\s*ne koriste se[^.]*\./iu', 'Crijeva/omotač: koristiti prirodno ili odgovarajuće jestivo crijevo te puniti bez zračnih džepova.', $line);
        $line = preg_replace('/crijeva\/omotac:\s*ne koriste se[^.]*\./iu', 'Crijeva/omotač: koristiti prirodno ili odgovarajuće jestivo crijevo te puniti bez zračnih džepova.', $line);
    }

    $line = preg_replace('/\s+,/u', ',', $line);
    $line = preg_replace('/,\s*\./u', '.', $line);
    $line = preg_replace('/\s{2,}/u', ' ', $line);

    return trim($line);
}

function dcmdfr_inline_format($text) {
    $text = esc_html($text);
    $text = preg_replace('/\*\*(.*?)\*\*/u', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/u', '<em>$1</em>', $text);
    return $text;
}

function dcmdfr_markdown_to_html($markdown, $display_title = '', $ctx = []) {
    $markdown = str_replace(["\r\n", "\r"], "\n", trim((string)$markdown));
    $lines = explode("\n", $markdown);

    $html = '';
    $in_ul = false;
    $in_ol = false;
    $section_open = false;
    $first_heading_skipped = false;

    $close_lists = function() use (&$html, &$in_ul, &$in_ol) {
        if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
        if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
    };

    foreach ($lines as $line) {
        $trim = dcmdfr_sanitize_line_for_context($line, $ctx);
        if ($trim === '') {
            $close_lists();
            continue;
        }

        if (preg_match('/^(#{1,4})\s+(.+)$/u', $trim, $m)) {
            $heading_text = dcmdfr_clean_display_title($m[2]);
            $heading_text = dcmdfr_sanitize_line_for_context($heading_text, $ctx);

            if (!$first_heading_skipped && $display_title !== '' && mb_strtolower($heading_text, 'UTF-8') === mb_strtolower($display_title, 'UTF-8')) {
                $first_heading_skipped = true;
                continue;
            }

            $first_heading_skipped = true;
            $close_lists();

            if ($section_open) $html .= "</section>\n";
            $section_open = true;

            $level = min(4, max(2, strlen($m[1]) + 1));
            $html .= '<section class="dc-md-section"><h' . $level . '>' . dcmdfr_inline_format($heading_text) . '</h' . $level . ">\n";
            continue;
        }

        if (preg_match('/^(Radni sažetak|Sastojci|Sastav|Postupak|Crijeva|Omotač|Omotac|Češnjak|Gotovo je kad|Najčešći problemi|Čuvanje|Posluživanje)(.*)$/iu', $trim, $m)) {
            $close_lists();

            if ($section_open) $html .= "</section>\n";
            $section_open = true;

            $heading = trim($m[1] . ($m[2] ?? ''));
            $heading = dcmdfr_sanitize_line_for_context($heading, $ctx);
            $html .= '<section class="dc-md-section"><h2>' . dcmdfr_inline_format($heading) . "</h2>\n";
            continue;
        }

        if (!$section_open) {
            $html .= '<section class="dc-md-section dc-md-intro">' . "\n";
            $section_open = true;
        }

        if (preg_match('/^[-*•]\s+(.+)$/u', $trim, $m)) {
            $item = dcmdfr_sanitize_line_for_context($m[1], $ctx);
            if ($item === '') continue;

            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            if (!$in_ul) { $html .= "<ul>\n"; $in_ul = true; }
            $html .= '<li>' . dcmdfr_inline_format($item) . "</li>\n";
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/u', $trim, $m)) {
            $item = dcmdfr_sanitize_line_for_context($m[1], $ctx);
            if ($item === '') continue;

            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if (!$in_ol) { $html .= "<ol>\n"; $in_ol = true; }
            $html .= '<li>' . dcmdfr_inline_format($item) . "</li>\n";
            continue;
        }

        $close_lists();
        $html .= '<p>' . dcmdfr_inline_format($trim) . "</p>\n";
    }

    $close_lists();
    if ($section_open) $html .= "</section>\n";

    return $html;
}

function dcmdfr_clean_full_fallback_html($html, $ctx = []) {
    if (stripos($html, 'dc-md-fallback-recipe') === false) {
        return $html;
    }

    $html = preg_replace('/\?{2,}\s*/u', '', $html);

    if (empty($ctx['is_fish'])) {
        $html = preg_replace('/svinjetina\/svinjski proizvodi,\s*riba\/morski proizvodi/iu', $ctx['raw_material'] ?? 'meso', $html);
        $html = preg_replace('/,\s*riba\/morski proizvodi/iu', '', $html);
        $html = preg_replace('/riba\/morski proizvodi,\s*/iu', '', $html);
        $html = preg_replace('/riba\/morski proizvodi/iu', $ctx['raw_material'] ?? 'meso', $html);
    }

    if (empty($ctx['is_cheese'])) {
        $html = preg_replace('/sušeni zreli sir|suseni zreli sir/iu', $ctx['type_label'] ?? 'suhomesnati proizvod', $html);
    }

    $html = preg_replace('/\s{2,}/u', ' ', $html);

    return $html;
}

function dcmdfr_render_page($post_id, $markdown, $code, $mode) {
    $title = dcmdfr_clean_display_title(get_the_title($post_id));
    $country = dcmdfr_terms_line($post_id, 'dry_country');
    $category_raw = dcmdfr_terms_line($post_id, 'dry_product_category');
    $process_raw = dcmdfr_terms_line($post_id, 'dry_process_type');

    $ctx = dcmdfr_detect_context($title, $category_raw, $process_raw);

    $category = dcmdfr_clean_meta_category($category_raw, $ctx);
    $process = dcmdfr_clean_meta_process($process_raw);

    $html_body = dcmdfr_markdown_to_html($markdown, $title, $ctx);

    ob_start();
    ?>
    <style>
        body.single-dry_recipe .site-content { background:#f3ead7 !important; }

        .dc-md-fallback-recipe {
            width:min(1120px, calc(100vw - 36px));
            margin:34px auto 78px;
            color:#111b33;
            font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        }

        .dc-md-fallback-banner {
            margin:0 0 16px;
            padding:10px 14px;
            border:1px solid #d8a63f;
            border-radius:14px;
            background:#fff8e3;
            color:#4b3512;
            font-weight:800;
            font-size:13px;
        }

        .dc-md-hero,
        .dc-md-meta,
        .dc-md-body {
            background:#fffaf0;
            border:1px solid #e1bd6b;
            border-radius:24px;
            box-shadow:0 14px 34px rgba(25,32,48,.08);
        }

        .dc-md-hero {
            padding:32px 34px;
            margin-bottom:18px;
        }

        .dc-md-kicker {
            margin:0 0 12px;
            font-size:13px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.08em;
            color:#8a6320;
        }

        .dc-md-hero h1 {
            margin:0;
            font-size:clamp(34px,4vw,54px);
            line-height:1.05;
            color:#07142d;
            letter-spacing:-.03em;
        }

        .dc-md-meta {
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:12px;
            padding:14px;
            margin-bottom:18px;
        }

        .dc-md-meta div {
            padding:14px 16px;
            border-radius:16px;
            background:#fffdf7;
            border:1px solid #ecd9aa;
            min-height:64px;
        }

        .dc-md-meta span {
            display:block;
            margin-bottom:6px;
            font-size:11px;
            font-weight:900;
            text-transform:uppercase;
            letter-spacing:.07em;
            color:#8a6320;
        }

        .dc-md-meta strong {
            display:block;
            font-size:16px;
            line-height:1.35;
            color:#07142d;
        }

        .dc-md-body { padding:26px; }

        .dc-md-section {
            background:#fffdf7;
            border:1px solid #ecd9aa;
            border-radius:20px;
            padding:22px 24px;
            margin:0 0 18px;
        }

        .dc-md-section h2,
        .dc-md-section h3,
        .dc-md-section h4 {
            margin:0 0 14px;
            color:#07142d;
            line-height:1.22;
        }

        .dc-md-section h2 { font-size:24px; }
        .dc-md-section h3 { font-size:21px; }
        .dc-md-section h4 { font-size:18px; }

        .dc-md-section p,
        .dc-md-section li {
            font-size:17px;
            line-height:1.72;
            color:#15264a;
        }

        .dc-md-section p { margin:0 0 13px; }

        .dc-md-section ul,
        .dc-md-section ol {
            margin:0 0 14px 1.15rem;
            padding:0;
        }

        .dc-md-section li { margin:6px 0; }
        .dc-md-section strong { color:#07142d; }

        @media (max-width:780px) {
            .dc-md-fallback-recipe {
                width:min(100%, calc(100vw - 22px));
                margin-top:18px;
            }
            .dc-md-hero,
            .dc-md-body,
            .dc-md-section {
                padding:20px 17px;
            }
            .dc-md-meta { grid-template-columns:1fr; }
            .dc-md-hero h1 { font-size:34px; }
        }
    </style>

    <article class="dc-md-fallback-recipe" id="dc-md-fallback-recipe-<?php echo esc_attr($post_id); ?>">
        <?php if ($mode === 'preview') : ?>
            <div class="dc-md-fallback-banner">PREVIEW FALLBACK PRIKAZ — vidljivo samo s privatnim tokenom.</div>
        <?php endif; ?>

        <header class="dc-md-hero">
            <p class="dc-md-kicker">Drycured recept</p>
            <h1><?php echo esc_html($title); ?></h1>
        </header>

        <section class="dc-md-meta" aria-label="Osnovni podaci recepta">
            <div><span>Šifra</span><strong><?php echo esc_html($code ?: 'nije navedeno'); ?></strong></div>
            <div><span>Zemlja</span><strong><?php echo esc_html($country ?: 'nije navedeno'); ?></strong></div>
            <div><span>Kategorija</span><strong><?php echo esc_html($category ?: $ctx['type_label']); ?></strong></div>
            <div><span>Proces</span><strong><?php echo esc_html($process ?: 'prema tipu proizvoda'); ?></strong></div>
        </section>

        <main class="dc-md-body">
            <?php echo $html_body; ?>
        </main>
    </article>
    <?php
    return [ob_get_clean(), $ctx];
}

function dcmdfr_output_fallback_page($post_id, $markdown, $code, $mode) {
    ob_start();
    get_header();

    [$page, $ctx] = dcmdfr_render_page($post_id, $markdown, $code, $mode);
    echo $page;

    get_footer();
    $html = ob_get_clean();

    echo dcmdfr_clean_full_fallback_html($html, $ctx);
}

function dcmdfr_maybe_render() {
    if (is_admin() || !is_singular('dry_recipe')) return;

    $mode = '';
    if (dcmdfr_has_valid_preview_token()) $mode = 'preview';
    elseif (dcmdfr_public_enabled()) $mode = 'public';

    if ($mode === '') return;

    $post_id = (int)get_queried_object_id();
    if ($post_id <= 0) return;

    $code = dcmdfr_recipe_code($post_id);

    if (dcmdfr_has_valid_dcv5_profile($post_id, $code)) return;

    $markdown = dcmdfr_source_markdown($post_id);
    $plain = trim(wp_strip_all_tags($markdown));
    if (strlen($plain) < 300) return;

    status_header(200);
    nocache_headers();

    dcmdfr_output_fallback_page($post_id, $markdown, $code, $mode);
    exit;
}
