<?php
/**
 * Plugin Name: Drycured Batch01 Whole-Cut MD Public Body
 * Description: Uski most koji za 25 cijelih komada popunjava prazni main HTML sadržajem iz _dry_recipe_full_markdown. Ne dira ostale recepte.
 */

if (!defined('ABSPATH')) {
    exit;
}

function drycured_b01_whole_cut_md_ids(): array {
    return [
        3094, 3105, 3142, 3175, 3188, 3205, 3212,
        2037, 2064, 2552, 2604, 2606, 2694, 2696,
        2703, 2705, 2780, 2781, 3027, 3083, 3106,
        3135, 3206, 3208, 3216,
    ];
}

function drycured_b01_whole_cut_md_target_id(): int {
    if (!is_singular('dry_recipe')) {
        return 0;
    }

    $post_id = (int) get_queried_object_id();

    if (!in_array($post_id, drycured_b01_whole_cut_md_ids(), true)) {
        return 0;
    }

    return $post_id;
}

add_action('template_redirect', function (): void {
    $post_id = drycured_b01_whole_cut_md_target_id();

    if ($post_id <= 0) {
        return;
    }

    $md = (string) get_post_meta($post_id, '_dry_recipe_full_markdown', true);

    if (trim($md) === '') {
        return;
    }

    ob_start(function (string $html) use ($post_id, $md): string {
        return drycured_b01_whole_cut_md_fill_main($html, $post_id, $md);
    });
}, 1);

function drycured_b01_whole_cut_md_fill_main(string $html, int $post_id, string $md): string {
    if (strpos($html, 'data-drycured-b01-md-public="1"') !== false) {
        return $html;
    }

    $article = drycured_b01_whole_cut_md_render_article($post_id, $md);

    $replacement = '<main id="main" class="site-main">' . $article . '</main><!-- #main -->';

    $pattern_with_comment = '~<main\s+id=["\']main["\']\s+class=["\']site-main["\']>\s*</main>\s*<!--\s*#main\s*-->~iu';
    $new_html = preg_replace($pattern_with_comment, $replacement, $html, 1, $count);

    if ($count > 0 && is_string($new_html)) {
        return $new_html;
    }

    $pattern_plain = '~<main\s+id=["\']main["\']\s+class=["\']site-main["\']>\s*</main>~iu';
    $new_html = preg_replace($pattern_plain, '<main id="main" class="site-main">' . $article . '</main>', $html, 1, $count);

    if ($count > 0 && is_string($new_html)) {
        return $new_html;
    }

    return $html;
}

function drycured_b01_whole_cut_md_render_article(int $post_id, string $md): string {
    $title = get_the_title($post_id);
    $recipe_id = (string) get_post_meta($post_id, '_dry_recipe_id', true);

    $html  = '<article class="dcv5-recipe dcv5-wholecut-md-public" data-drycured-b01-md-public="1">';
    $html .= '<section class="dcv5-hero">';
    $html .= '<div class="dcv5-hero-grid">';
    $html .= '<div>';
    $html .= '<div class="dcv5-badge-row">';
    $html .= '<span class="dcv5-badge">Cijeli komad</span>';
    $html .= '<span class="dcv5-badge">Šarža 10 kg</span>';
    if ($recipe_id !== '') {
        $html .= '<span class="dcv5-badge">' . esc_html($recipe_id) . '</span>';
    }
    $html .= '</div>';
    $html .= '<h1>' . esc_html($title) . '</h1>';
    $html .= '<p>Proizvodni vodič za izradu suhomesnatog proizvoda od cijelog komada mesa, prilagođen prikazu na drycured.com.</p>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</section>';

    $html .= '<section class="dcv5-panel dcv5-wholecut-md-panel">';
    $html .= drycured_b01_whole_cut_md_to_html($md);
    $html .= '</section>';
    $html .= '</article>';

    return $html;
}

function drycured_b01_whole_cut_md_inline(string $text): string {
    $text = esc_html($text);
    $text = preg_replace('/\*\*(.*?)\*\*/u', '<strong>$1</strong>', $text);
    return $text;
}

function drycured_b01_whole_cut_md_forbidden_line(string $line): bool {
    return (bool) preg_match('/(Mljevenj|granulacij|Rešetk|šajb|nadjev|Punjenj|Crijev|omotač|ovojnic|puniti u|Narezati na kocke|meso i masnoću očistiti|razmazuje u presjeku)/iu', $line);
}

function drycured_b01_whole_cut_md_forbidden_section_title(string $line): bool {
    return (bool) preg_match('/^(##|###)\s*(Mljevenj|Odmor nadjeva|Odležavanje nadjeva|Punjenj|Priprema nadjeva|Crijev|Omotač|Crijeva\s*\/\s*omotač)/iu', $line);
}

function drycured_b01_whole_cut_md_to_html(string $md): string {
    $lines = preg_split('/\R/u', $md);
    $html = '';
    $in_list = false;
    $skip_section = false;

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '') {
            if ($in_list) {
                $html .= '</ul>';
                $in_list = false;
            }
            continue;
        }

        if (preg_match('/^(##|###)\s+(.+)$/u', $line, $hm)) {
            if ($in_list) {
                $html .= '</ul>';
                $in_list = false;
            }

            if (drycured_b01_whole_cut_md_forbidden_section_title($line)) {
                $skip_section = true;
                continue;
            }

            $skip_section = false;

            if ($hm[1] === '###') {
                $html .= '<h3>' . drycured_b01_whole_cut_md_inline($hm[2]) . '</h3>';
            } else {
                $html .= '<h2>' . drycured_b01_whole_cut_md_inline($hm[2]) . '</h2>';
            }

            continue;
        }

        if ($skip_section) {
            continue;
        }

        if (drycured_b01_whole_cut_md_forbidden_line($line)) {
            continue;
        }

        if (preg_match('/^-\s+(.+)$/u', $line, $m)) {
            if (!$in_list) {
                $html .= '<ul class="dcv5-md-list">';
                $in_list = true;
            }
            $html .= '<li>' . drycured_b01_whole_cut_md_inline($m[1]) . '</li>';
            continue;
        }

        if ($in_list) {
            $html .= '</ul>';
            $in_list = false;
        }

        $html .= '<p>' . drycured_b01_whole_cut_md_inline($line) . '</p>';
    }

    if ($in_list) {
        $html .= '</ul>';
    }

    return $html;
}
