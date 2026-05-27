<?php
/**
 * Plugin Name: Drycured Archive SEO Bridge
 * Description: Narrow SEO title/meta bridge for selected drycured.com CPT archive routes.
 * Version: 0.0.1
 */

if (!defined('ABSPATH')) {
    exit;
}

function drycured_archive_seo_bridge_map(): array {
    return [
        '/savjeti/' => [
            'title' => 'Savjeti iz pušnice — greške, rješenja i praktična kontrola procesa',
            'description' => 'Praktični savjeti za domaću izradu suhomesnatih proizvoda: najčešće greške, uzroci problema i konkretna rješenja u pušnici i komori.',
            'canonical' => home_url('/savjeti/'),
        ],
        '/infografike/' => [
            'title' => 'Infografike o suhomesnatim proizvodima — vizualni vodiči',
            'description' => 'Katalog edukativnih infografika o mesu, soljenju, dimljenju, sušenju, zrenju, sigurnosti i tradicionalnoj izradi suhomesnatih proizvoda.',
            'canonical' => home_url('/infografike/'),
        ],
        '/recepti-baza/' => [
            'title' => 'Baza recepata za suhomesnate proizvode — države, regije i vrste',
            'description' => 'Pretraživa baza recepata za suhomesnate proizvode po državama, regijama, vrsti proizvoda, postupku izrade i sastavu mesa.',
            'canonical' => home_url('/recepti-baza/'),
        ],
    ];
}

function drycured_archive_seo_bridge_current(): ?array {
    if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || is_feed() || is_robots()) {
        return null;
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $path = '/' . trim((string) $path, '/') . '/';

    $map = drycured_archive_seo_bridge_map();

    return $map[$path] ?? null;
}

function drycured_archive_seo_bridge_filter_title($title) {
    $seo = drycured_archive_seo_bridge_current();
    return $seo ? $seo['title'] : $title;
}

function drycured_archive_seo_bridge_filter_description($description) {
    $seo = drycured_archive_seo_bridge_current();
    return $seo ? $seo['description'] : $description;
}

add_filter('aioseo_title', 'drycured_archive_seo_bridge_filter_title', 100000, 1);
add_filter('aioseo_description', 'drycured_archive_seo_bridge_filter_description', 100000, 1);
add_filter('pre_get_document_title', 'drycured_archive_seo_bridge_filter_title', 100000, 1);

function drycured_archive_seo_bridge_upsert_tag(string $head, string $pattern, string $tag): string {
    if (preg_match($pattern, $head)) {
        return preg_replace($pattern, $tag, $head, 1);
    }

    if (stripos($head, '<link rel="canonical"') !== false) {
        return preg_replace('/<link\s+rel=["\']canonical["\'][^>]*>/i', $tag . "\n$0", $head, 1);
    }

    return $head . "\n" . $tag;
}

function drycured_archive_seo_bridge_rewrite_head(string $html): string {
    $seo = drycured_archive_seo_bridge_current();

    if (!$seo || stripos($html, '<head') === false || stripos($html, '</head>') === false) {
        return $html;
    }

    $title = esc_attr($seo['title']);
    $desc = esc_attr($seo['description']);
    $canonical = esc_url($seo['canonical']);

    return preg_replace_callback('/<head\b[^>]*>(.*?)<\/head>/is', function ($m) use ($title, $desc, $canonical) {
        $head = $m[1];

        $head = preg_replace('/<title>.*?<\/title>/is', '<title>' . $title . '</title>', $head, 1);

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<meta\s+name=["\']description["\'][^>]*>/i',
            '<meta name="description" content="' . $desc . '" />'
        );

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<meta\s+property=["\']og:title["\'][^>]*>/i',
            '<meta property="og:title" content="' . $title . '" />'
        );

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<meta\s+property=["\']og:description["\'][^>]*>/i',
            '<meta property="og:description" content="' . $desc . '" />'
        );

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<meta\s+name=["\']twitter:title["\'][^>]*>/i',
            '<meta name="twitter:title" content="' . $title . '" />'
        );

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<meta\s+name=["\']twitter:description["\'][^>]*>/i',
            '<meta name="twitter:description" content="' . $desc . '" />'
        );

        $head = drycured_archive_seo_bridge_upsert_tag(
            $head,
            '/<link\s+rel=["\']canonical["\'][^>]*>/i',
            '<link rel="canonical" href="' . $canonical . '" />'
        );

        if (stripos($head, 'Drycured Archive SEO Bridge') === false) {
            $head .= "\n<!-- Drycured Archive SEO Bridge v0.0.1 -->\n";
        }

        return '<head>' . $head . '</head>';
    }, $html, 1);
}

add_action('template_redirect', function () {
    if (drycured_archive_seo_bridge_current()) {
        ob_start('drycured_archive_seo_bridge_rewrite_head');
    }
}, 0);
