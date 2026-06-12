<?php
/**
 * Plugin Name: Drycured Recipe MD Fallback Renderer
 * Description: Fallback renderer for dry_recipe posts without a valid DCV5 profile. Public mode is controlled by option.
 * Version: 0.2.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', 'dcmdfr_maybe_render', 1);

function dcmdfr_public_enabled() {
    return (string) get_option('drycured_md_fallback_public_enabled', '0') === '1';
}

function dcmdfr_has_valid_preview_token() {
    $expected = (string) get_option('drycured_md_fallback_preview_token', '');
    $provided = isset($_GET['dc_md_fallback_preview'])
        ? (string) wp_unslash($_GET['dc_md_fallback_preview'])
        : '';

    return $expected !== '' && $provided !== '' && hash_equals($expected, $provided);
}

function dcmdfr_recipe_code($post_id) {
    $keys = [
        '_dry_recipe_id',
        'dry_recipe_id',
        '_recipe_id',
        'recipe_id',
        'dry_recipe_code',
        'drycured_source_recipe_id',
    ];

    foreach ($keys as $key) {
        $value = trim((string) get_post_meta($post_id, $key, true));
        if ($value !== '') {
            return $value;
        }
    }

    return '';
}

function dcmdfr_has_valid_dcv5_profile($post_id, $code) {
    if (!function_exists('dcv5_get_recipe_profile')) {
        return false;
    }

    $profile = dcv5_get_recipe_profile($post_id, $code);

    if (!is_array($profile) || empty($profile)) {
        return false;
    }

    $title = trim((string)($profile['title'] ?? $profile['name'] ?? ''));
    $profile_code = trim((string)($profile['code'] ?? $profile['recipe_code'] ?? ''));

    return $title !== '' || $profile_code !== '';
}

function dcmdfr_source_markdown($post_id) {
    $full = trim((string) get_post_meta($post_id, '_dry_recipe_full_markdown', true));
    if ($full !== '') {
        return $full;
    }

    $post = get_post($post_id);
    if ($post && trim((string) $post->post_content) !== '') {
        return (string) $post->post_content;
    }

    return '';
}

function dcmdfr_terms_line($post_id, $taxonomy) {
    $terms = wp_get_object_terms($post_id, $taxonomy, ['fields' => 'names']);
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }

    return implode(' · ', array_map('sanitize_text_field', $terms));
}

function dcmdfr_inline_format($text) {
    $text = esc_html($text);
    $text = preg_replace('/\*\*(.*?)\*\*/u', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/u', '<em>$1</em>', $text);
    return $text;
}

function dcmdfr_markdown_to_html($markdown) {
    $markdown = str_replace(["\r\n", "\r"], "\n", trim((string) $markdown));
    $lines = explode("\n", $markdown);

    $html = '';
    $in_ul = false;
    $in_ol = false;

    foreach ($lines as $line) {
        $trim = trim($line);

        if ($trim === '') {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }
            continue;
        }

        if (preg_match('/^(#{1,4})\s+(.+)$/u', $trim, $m)) {
            if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
            if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }

            $level = min(4, max(2, strlen($m[1]) + 1));
            $html .= '<h' . $level . '>' . dcmdfr_inline_format($m[2]) . '</h' . $level . ">\n";
            continue;
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

        if ($in_ul) { $html .= "</ul>\n"; $in_ul = false; }
        if ($in_ol) { $html .= "</ol>\n"; $in_ol = false; }

        $html .= '<p>' . dcmdfr_inline_format($trim) . "</p>\n";
    }

    if ($in_ul) { $html .= "</ul>\n"; }
    if ($in_ol) { $html .= "</ol>\n"; }

    return $html;
}

function dcmdfr_render_page($post_id, $markdown, $code, $mode) {
    $country = dcmdfr_terms_line($post_id, 'dry_country');
    $category = dcmdfr_terms_line($post_id, 'dry_product_category');
    $process = dcmdfr_terms_line($post_id, 'dry_process_type');

    ob_start();
    ?>
    <style>
        body.single-dry_recipe .site-content { background: #f8f0de !important; }

        .dc-md-fallback-recipe {
            box-sizing: border-box;
            width: min(1180px, calc(100vw - 42px));
            margin: 34px auto 72px;
            color: #111b33;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .dc-md-fallback-banner {
            margin: 0 0 16px;
            padding: 10px 14px;
            border: 1px solid #d8a63f;
            border-radius: 14px;
            background: #fff8e3;
            color: #4b3512;
            font-weight: 800;
            font-size: 13px;
        }

        .dc-md-fallback-hero,
        .dc-md-fallback-body,
        .dc-md-fallback-meta {
            background: #fffaf0;
            border: 1px solid #e2c98e;
            border-radius: 22px;
            box-shadow: 0 14px 34px rgba(25, 32, 48, .08);
        }

        .dc-md-fallback-hero {
            padding: 28px 32px;
            margin-bottom: 18px;
        }

        .dc-md-fallback-hero h1 {
            margin: 0 0 12px;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.08;
            color: #111b33;
        }

        .dc-md-fallback-kicker {
            margin: 0 0 8px;
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #8a6320;
        }

        .dc-md-fallback-meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            padding: 14px;
            margin-bottom: 18px;
        }

        .dc-md-fallback-meta div {
            padding: 12px 14px;
            border-radius: 16px;
            background: #fffdf7;
            border: 1px solid #ecd9aa;
        }

        .dc-md-fallback-meta span {
            display: block;
            margin-bottom: 4px;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #8a6320;
        }

        .dc-md-fallback-meta strong {
            font-size: 15px;
            color: #111b33;
        }

        .dc-md-fallback-body {
            padding: 30px 34px;
            font-size: 17px;
            line-height: 1.72;
        }

        .dc-md-fallback-body h2,
        .dc-md-fallback-body h3,
        .dc-md-fallback-body h4 {
            margin: 28px 0 12px;
            color: #111b33;
            line-height: 1.22;
        }

        .dc-md-fallback-body p { margin: 0 0 14px; }

        .dc-md-fallback-body ul,
        .dc-md-fallback-body ol {
            margin: 0 0 18px 1.2rem;
            padding: 0;
        }

        .dc-md-fallback-body li { margin: 6px 0; }

        @media (max-width: 780px) {
            .dc-md-fallback-recipe {
                width: min(100%, calc(100vw - 24px));
                margin-top: 18px;
            }

            .dc-md-fallback-hero,
            .dc-md-fallback-body {
                padding: 22px 18px;
            }

            .dc-md-fallback-meta {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <article class="dc-md-fallback-recipe" id="dc-md-fallback-recipe-<?php echo esc_attr($post_id); ?>">
        <?php if ($mode === 'preview') : ?>
            <div class="dc-md-fallback-banner">
                PREVIEW FALLBACK PRIKAZ — vidljivo samo s privatnim tokenom.
            </div>
        <?php endif; ?>

        <header class="dc-md-fallback-hero">
            <p class="dc-md-fallback-kicker">Drycured recept</p>
            <h1><?php echo esc_html(get_the_title($post_id)); ?></h1>
        </header>

        <section class="dc-md-fallback-meta" aria-label="Osnovni podaci recepta">
            <div><span>Šifra</span><strong><?php echo esc_html($code ?: 'nije navedeno'); ?></strong></div>
            <div><span>Zemlja</span><strong><?php echo esc_html($country ?: 'nije navedeno'); ?></strong></div>
            <div><span>Kategorija</span><strong><?php echo esc_html($category ?: 'nije navedeno'); ?></strong></div>
            <div><span>Proces</span><strong><?php echo esc_html($process ?: 'nije navedeno'); ?></strong></div>
        </section>

        <main class="dc-md-fallback-body">
            <?php echo dcmdfr_markdown_to_html($markdown); ?>
        </main>
    </article>
    <?php
    return ob_get_clean();
}

function dcmdfr_maybe_render() {
    if (is_admin() || !is_singular('dry_recipe')) {
        return;
    }

    $mode = '';
    if (dcmdfr_has_valid_preview_token()) {
        $mode = 'preview';
    } elseif (dcmdfr_public_enabled()) {
        $mode = 'public';
    }

    if ($mode === '') {
        return;
    }

    $post_id = (int) get_queried_object_id();
    if ($post_id <= 0) {
        return;
    }

    $code = dcmdfr_recipe_code($post_id);

    if (dcmdfr_has_valid_dcv5_profile($post_id, $code)) {
        return;
    }

    $markdown = dcmdfr_source_markdown($post_id);
    $plain = trim(wp_strip_all_tags($markdown));

    if (strlen($plain) < 300) {
        return;
    }

    status_header(200);
    nocache_headers();

    get_header();
    echo dcmdfr_render_page($post_id, $markdown, $code, $mode);
    get_footer();
    exit;
}
