<?php
/**
 * Plugin Name: Drycured Recipe MD Fallback Renderer
 * Description: Public fallback renderer for dry_recipe posts without a valid DCV5 profile.
 * Version: 0.3.1
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

function dcmdfr_inline_format($text) {
    $text = dcmdfr_clean_public_text_line($text);
    $text = esc_html($text);
    $text = preg_replace('/\*\*(.*?)\*\*/u', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/u', '<em>$1</em>', $text);
    return $text;
}

function dcmdfr_markdown_to_html($markdown, $display_title = '') {
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
        $trim = dcmdfr_clean_public_text_line($line);

        if ($trim === '') {
            $close_lists();
            continue;
        }

        if (preg_match('/^(#{1,4})\s+(.+)$/u', $trim, $m)) {
            $heading_text = dcmdfr_clean_display_title($m[2]);

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
            $html .= '<section class="dc-md-section"><h2>' . dcmdfr_inline_format($heading) . "</h2>\n";
            continue;
        }

        if (!$section_open) {
            $html .= '<section class="dc-md-section dc-md-intro">' . "\n";
            $section_open = true;
        }

        if (preg_match('/^[-*•]\s+(.+)$/u', $trim, $m)) {
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            if (!$in_ul) { $html .= "<ul>\n"; $in_ul = true; }
            $html .= '<li>' . dcmdfr_inline_format($m[1]) . "</li>\n";
            continue;
        }

        if (preg_match('/^\d+\.\s+(.+)$/u', $trim, $m)) {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if (!$in_ol) { $html .= "<ol>\n"; $in_ol = true; }
            $html .= '<li>' . dcmdfr_inline_format($m[1]) . "</li>\n";
            continue;
        }

        $close_lists();
        $html .= '<p>' . dcmdfr_inline_format($trim) . "</p>\n";
    }

    $close_lists();
    if ($section_open) $html .= "</section>\n";

    return $html;
}

function dcmdfr_render_page($post_id, $markdown, $code, $mode) {
    $title = dcmdfr_clean_display_title(get_the_title($post_id));
    $country = dcmdfr_terms_line($post_id, 'dry_country');
    $category = dcmdfr_terms_line($post_id, 'dry_product_category');
    $process = dcmdfr_terms_line($post_id, 'dry_process_type');
    $html_body = dcmdfr_markdown_to_html($markdown, $title);

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

        .dc-md-body {
            padding:26px;
        }

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
            .dc-md-meta {
                grid-template-columns:1fr;
            }
            .dc-md-hero h1 {
                font-size:34px;
            }
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
            <div><span>Kategorija</span><strong><?php echo esc_html($category ?: 'nije navedeno'); ?></strong></div>
            <div><span>Proces</span><strong><?php echo esc_html($process ?: 'nije navedeno'); ?></strong></div>
        </section>

        <main class="dc-md-body">
            <?php echo $html_body; ?>
        </main>
    </article>
    <?php
    return ob_get_clean();
}


function dcmdfr_clean_full_fallback_html($html) {
    // Clean only fallback-rendered output. This removes imported dirty title markers
    // such as "??" from document title, SEO fragments, breadcrumbs and visible text.
    if (stripos($html, 'dc-md-fallback-recipe') === false) {
        return $html;
    }

    $html = preg_replace('/\?{2,}\s*/u', '', $html);
    $html = preg_replace('/\s{2,}/u', ' ', $html);

    return $html;
}

function dcmdfr_output_fallback_page($post_id, $markdown, $code, $mode) {
    ob_start();
    get_header();
    echo dcmdfr_render_page($post_id, $markdown, $code, $mode);
    get_footer();

    $html = ob_get_clean();
    echo dcmdfr_clean_full_fallback_html($html);
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
