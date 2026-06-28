<?php
if (!defined('ABSPATH')) exit;

/*
 * DRYCURED SINGLE TEMPLATE — CLEAN REBUILD
 * Web prikaz = modularno sučelje.
 * Print prikaz = dokument samo u browser print previewu.
 */

add_filter('the_content', 'drycured_render_public_recipe_content', 20);

function drycured_render_public_recipe_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    wp_enqueue_style('drycured-recipes');

    $post_id = get_the_ID();

    /*
     * DRYCURED_LEGACY_RENDERER_HTTP500_GUARD_v010
     *
     * Ova dva javna MD-only zapisa ruše stari public renderer kroz
     * memory exhaustion u WordPress formatting.php. Ne diramo njihove
     * podatke i ne povećavamo memoriju; samo ih izuzimamo iz starog
     * renderera dok ne dobiju kontrolirani V5/V6 prikaz za cijele komade.
     */
    if (in_array((int) $post_id, [3064, 3068], true)) {
        return $content;
    }

    $data_raw = get_post_meta($post_id, '_dry_recipe_data', true);
    $data = $data_raw ? json_decode($data_raw, true) : [];
    if (!is_array($data)) $data = [];

    $public = (!empty($data['public_recipe']) && is_array($data['public_recipe'])) ? $data['public_recipe'] : [];

    $title = get_the_title($post_id);
    $intro = $public['intro'] ?? ($data['short_description'] ?? '');
    // NOTE: get_the_excerpt() is INTENTIONALLY not used here because it calls
    // wp_trim_excerpt() which calls apply_filters('the_content') causing infinite recursion
    // when this function is itself a the_content filter. Use raw post_excerpt instead.
    if ($intro === '') {
        $raw_excerpt = get_post_field('post_excerpt', $post_id);
        $intro = $raw_excerpt !== '' ? esc_html($raw_excerpt) : '';
    }

    $region = $data['region'] ?? drycured_recipe_term_name($post_id, 'dry_region');
    $microregion = $data['microregion'] ?? drycured_recipe_term_name($post_id, 'dry_microregion');
    $category = $data['category'] ?? drycured_recipe_term_name($post_id, 'dry_product_category');
    $product_type = $data['product_type'] ?? drycured_recipe_term_name($post_id, 'dry_product_type');

    $meat_types = !empty($data['meat_types']) && is_array($data['meat_types'])
        ? implode(', ', $data['meat_types'])
        : drycured_recipe_terms_join($post_id, 'dry_meat_type');

    $processes = !empty($data['processes']) && is_array($data['processes'])
        ? implode(', ', $data['processes'])
        : drycured_recipe_terms_join($post_id, 'dry_process_type');

    $quick = $public['quick_facts'] ?? [];
    $meat_rows = $public['meat_composition'] ?? ($data['meat_composition'] ?? []);
    $ingredient_rows = $public['ingredients'] ?? ($data['ingredients'] ?? []);
    $casing = $public['casings'] ?? ($data['casings'] ?? []);
    $phases = $public['process_phases'] ?? ($public['preparation'] ?? []);
    $mistakes = $public['mistakes'] ?? ($data['problems'] ?? []);

    $full_markdown = get_post_meta($post_id, '_dry_recipe_full_markdown', true);
    if (!$full_markdown && !empty($data['full_markdown'])) $full_markdown = $data['full_markdown'];
    if (!$full_markdown && !empty($data['source_markdown'])) $full_markdown = $data['source_markdown'];

    $md_cards = $full_markdown
        ? drycured_render_md_cards_clean($full_markdown, !empty($ingredient_rows), !empty($phases), !empty($mistakes))
        : '';

    $calculator = $data['calculator'] ?? [];
    $calculator_enabled = !empty($calculator['enabled']) || !empty($data['calculator_ready']) || get_post_meta($post_id, '_dry_calculator_ready', true);
    $calculator_url = drycured_recipe_calculator_link($post_id, $data);

    ob_start();
    ?>
    <article class="dry-recipe-page dry-recipe-clean-template">

        <section class="dry-recipe-card dry-recipe-header">
            <div class="dry-recipe-topline">
                <span>Recept</span>
                <?php if ($category): ?><span><?php echo esc_html($category); ?></span><?php endif; ?>
                <?php if ($region): ?><span><?php echo esc_html($region); ?></span><?php endif; ?>
            </div>

            <div class="dry-recipe-hero-layout">
                <div class="dry-recipe-hero-copy">
                    <h1><?php echo esc_html($title); ?></h1>

                    <?php if ($intro): ?>
                        <p class="dry-recipe-intro"><?php echo esc_html($intro); ?></p>
                    <?php endif; ?>

                    <div class="dry-recipe-tags">
                        <?php echo drycured_recipe_tag($microregion); ?>
                        <?php echo drycured_recipe_tag($product_type); ?>
                        <?php echo drycured_recipe_tag($meat_types); ?>
                        <?php echo drycured_recipe_tag($processes); ?>
                    </div>
                </div>

                <?php if (has_post_thumbnail($post_id)): ?>
                    <figure class="dry-recipe-image">
                        <?php echo get_the_post_thumbnail($post_id, 'large'); ?>
                    </figure>
                <?php endif; ?>

                <div class="dry-recipe-actions dry-recipe-actions-under-image">
                    <?php if ($calculator_enabled): ?>
                        <a class="dry-btn dry-btn-primary" href="<?php echo esc_url($calculator_url); ?>">Izračunaj sastojke</a>
                    <?php else: ?>
                        <span class="dry-btn dry-btn-disabled">Kalkulator u pripremi</span>
                    <?php endif; ?>

                    <button class="dry-btn dry-btn-secondary" onclick="window.print(); return false;">Pregled za ispis</button>
                </div>
            </div>
        </section>

        <section class="dry-recipe-card dry-recipe-facts">
            <h2>Osnovni podaci</h2>
            <div class="dry-facts-grid">
                <?php
                if (!empty($quick) && is_array($quick)) {
                    foreach ($quick as $label => $value) echo drycured_fact_item($label, $value);
                } else {
                    echo drycured_fact_item('Osnovna šarža', !empty($data['batch_weight_kg']) ? $data['batch_weight_kg'] . ' kg' : 'prema receptu');
                    echo drycured_fact_item('Regija', $region ?: 'nije određeno');
                    echo drycured_fact_item('Vrsta proizvoda', $category ?: 'nije određeno');
                    echo drycured_fact_item('Vrsta mesa', $meat_types ?: 'nije određeno');
                    echo drycured_fact_item('Postupak', $processes ?: 'nije određeno');
                    echo drycured_fact_item('Kalkulator', $calculator_enabled ? 'dostupan' : 'u pripremi');
                }
                ?>
            </div>
        </section>

        <?php if (!empty($data['protected_product_warning'])): ?>
            <section class="dry-protected-note">
                Ovaj zapis je edukativni profil. Ne predstavlja službenu specifikaciju za certificiranu proizvodnju zaštićenog proizvoda.
            </section>
        <?php endif; ?>

        <section class="dry-recipe-main">

            <div class="dry-recipe-left">
                <?php if (!empty($meat_rows)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading"><span>01</span><h2>Mesni sastav</h2></div>
                        <p class="dry-section-hint">Preporučeni dijelovi mesa za osnovnu šaržu.</p>
                        <?php echo drycured_render_recipe_rows($meat_rows, 'meat'); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($ingredient_rows)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading"><span>02</span><h2>Sastojci i začini</h2></div>
                        <p class="dry-section-hint">Količine su navedene za osnovnu šaržu recepta.</p>
                        <?php echo drycured_render_recipe_rows($ingredient_rows, 'ingredient'); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($casing)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading"><span>03</span><h2>Crijeva / omotači</h2></div>
                        <?php echo drycured_render_casing_block($casing); ?>
                    </section>
                <?php endif; ?>
            </div>

            <div class="dry-recipe-right">
                <?php if (!empty($phases)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading"><span>04</span><h2>Tehnološki postupak</h2></div>
                        <p class="dry-section-hint">Postupak je prikazan po fazama: što radiš, zašto je važno, kako provjeravaš i što učiniti ako krene loše.</p>
                        <?php echo drycured_render_process_phases($phases); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($md_cards)): ?>
                    <?php echo $md_cards; ?>
                <?php endif; ?>

                <?php if (!empty($mistakes)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading"><span>05</span><h2>Najčešće greške i rješenja</h2></div>
                        <?php echo drycured_render_mistakes($mistakes); ?>
                    </section>
                <?php endif; ?>
            </div>

        </section>

        <?php if (!empty($full_markdown)): ?>
            <section class="dry-recipe-print-document" aria-hidden="true" style="display:none;">
                <h1><?php echo esc_html($title); ?></h1>
                <?php echo drycured_render_full_recipe_markdown($full_markdown); ?>
            </section>
        <?php endif; ?>

    </article>
    <?php
    return ob_get_clean();
}

function drycured_recipe_calculator_link($post_id, $data) {
    $recipe_id = get_post_meta($post_id, '_dry_recipe_id', true);
    $calculator = $data['calculator'] ?? [];

    $args = [
        'recipe_id' => $recipe_id,
        'mode' => $calculator['calculator_mode'] ?? 'sastojci',
    ];

    if (!empty($calculator['calculator_key'])) $args['product'] = $calculator['calculator_key'];
    return add_query_arg($args, home_url('/kalkulator/'));
}

function drycured_recipe_term_name($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) return '';
    return $terms[0]->name;
}

function drycured_recipe_terms_join($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);
    if (!$terms || is_wp_error($terms)) return '';
    return implode(', ', wp_list_pluck($terms, 'name'));
}

function drycured_recipe_tag($value) {
    if (!$value) return '';
    return '<span>' . esc_html($value) . '</span>';
}

function drycured_fact_item($label, $value) {
    if (!$value) return '';
    return '<article><span>' . esc_html($label) . '</span><strong>' . esc_html($value) . '</strong></article>';
}

function drycured_render_recipe_rows($rows, $type = 'ingredient') {
    if (!is_array($rows) || empty($rows)) return '';

    $html = '<div class="dry-recipe-rows">';
    foreach ($rows as $row) {
        if (!is_array($row)) continue;

        $name = $row['name'] ?? $row['sastojak'] ?? $row['dio_mesa'] ?? $row['dio'] ?? 'Stavka';
        $amount = drycured_format_amount($row);
        $note = $row['why'] ?? $row['role'] ?? $row['uloga'] ?? $row['note'] ?? $row['napomena'] ?? '';

        $html .= '<article class="dry-recipe-row"><div>';
        $html .= '<strong>' . esc_html($name) . '</strong>';
        if ($note) $html .= '<p>' . esc_html($note) . '</p>';
        $html .= '</div>';
        if ($amount) $html .= '<span>' . esc_html($amount) . '</span>';
        $html .= '</article>';
    }
    $html .= '</div>';

    return $html;
}

function drycured_render_casing_block($casing) {
    if (!is_array($casing)) return '';
    $html = '<div class="dry-casing-block">';
    if (!empty($casing['type'])) $html .= '<p><strong>Vrsta:</strong> ' . esc_html($casing['type']) . '</p>';
    if (!empty($casing['diameter_mm'])) $html .= '<p><strong>Promjer:</strong> ' . esc_html($casing['diameter_mm']) . ' mm</p>';
    if (!empty($casing['soaking'])) $html .= '<p><strong>Priprema:</strong> ' . esc_html($casing['soaking']) . '</p>';
    $html .= '</div>';
    return $html;
}

function drycured_render_process_phases($phases) {
    if (!is_array($phases) || empty($phases)) return '';

    $html = '<div class="dry-process-phases">';
    foreach ($phases as $phase) {
        if (!is_array($phase)) continue;

        $title = $phase['title'] ?? 'Faza postupka';
        $html .= '<article class="dry-process-phase">';
        $html .= '<h3>' . esc_html($title) . '</h3>';

        if (!empty($phase['what'])) $html .= '<div class="dry-phase-block"><strong>Što radiš</strong><p>' . esc_html($phase['what']) . '</p></div>';
        if (!empty($phase['why'])) $html .= '<div class="dry-phase-block"><strong>Zašto je važno</strong><p>' . esc_html($phase['why']) . '</p></div>';
        if (!empty($phase['control'])) $html .= '<div class="dry-phase-block"><strong>Kontrolni znak</strong><p>' . esc_html($phase['control']) . '</p></div>';

        if (!empty($phase['risk']) || !empty($phase['fix'])) {
            $html .= '<div class="dry-phase-warning">';
            if (!empty($phase['risk'])) $html .= '<p><strong>Rizik:</strong> ' . esc_html($phase['risk']) . '</p>';
            if (!empty($phase['fix'])) $html .= '<p><strong>Rješenje:</strong> ' . esc_html($phase['fix']) . '</p>';
            $html .= '</div>';
        }

        if (!empty($phase['parameters']) && is_array($phase['parameters'])) {
            $html .= '<dl class="dry-phase-params">';
            foreach ($phase['parameters'] as $param) {
                if (!is_array($param)) continue;
                $label = $param['label'] ?? '';
                $value = $param['value'] ?? '';
                if (!$label && !$value) continue;
                $html .= '<div>';
                if ($label) $html .= '<dt>' . esc_html($label) . '</dt>';
                if ($value) $html .= '<dd>' . esc_html($value) . '</dd>';
                $html .= '</div>';
            }
            $html .= '</dl>';
        }

        $html .= '</article>';
    }
    $html .= '</div>';

    return $html;
}

function drycured_render_mistakes($mistakes) {
    if (!is_array($mistakes) || empty($mistakes)) return '';

    $html = '<div class="dry-mistakes">';
    foreach ($mistakes as $mistake) {
        if (!is_array($mistake)) continue;

        $problem = $mistake['problem'] ?? 'Problem';
        $cause = $mistake['cause'] ?? ($mistake['uzrok'] ?? '');
        $solution = $mistake['solution'] ?? ($mistake['rjesenje'] ?? '');

        $html .= '<article class="dry-mistake">';
        $html .= '<h3>' . esc_html($problem) . '</h3>';
        if ($cause) $html .= '<p><strong>Uzrok:</strong> ' . esc_html($cause) . '</p>';
        if ($solution) $html .= '<p><strong>Rješenje:</strong> ' . esc_html($solution) . '</p>';
        $html .= '</article>';
    }
    $html .= '</div>';

    return $html;
}

function drycured_format_amount($row) {
    if (!empty($row['amount'])) return $row['amount'];
    if (!empty($row['amount_note'])) return $row['amount_note'];
    if (isset($row['amount_kg'])) return drycured_number($row['amount_kg']) . ' kg';
    if (isset($row['amount_g'])) return drycured_number($row['amount_g']) . ' g';
    if (isset($row['amount_l'])) return drycured_number($row['amount_l']) . ' L';
    if (isset($row['amount_pieces'])) return drycured_number($row['amount_pieces']) . ' kom';
    return '';
}

function drycured_number($value) {
    if (!is_numeric($value)) return $value;
    $value = floatval($value);
    if (floor($value) == $value) return (string)intval($value);
    return rtrim(rtrim(number_format($value, 3, ',', ''), '0'), ',');
}

/* ---------- MD → native interface kartice ---------- */

function drycured_render_md_cards_clean($markdown, $skip_ingredients = true, $has_structured_process = false, $has_structured_mistakes = false) {
    $sections = drycured_md_extract_sections_clean($markdown);
    if (empty($sections)) return '';

    $order = ['process', 'notes', 'region', 'storage', 'mistakes', 'ingredients', 'general'];
    $sorted = [];

    foreach ($order as $kind) {
        foreach ($sections as $section) {
            if (drycured_md_kind_clean($section['title']) === $kind) $sorted[] = $section;
        }
    }

    $html = '';
    $card_no = $has_structured_process ? 6 : 4;

    foreach ($sorted as $section) {
        $title = $section['title'];
        $items = $section['items'];
        $kind = drycured_md_kind_clean($title);

        if ($kind === 'ingredients' && $skip_ingredients) continue;
        if ($kind === 'process' && $has_structured_process) continue;
        if ($kind === 'mistakes' && $has_structured_mistakes) continue;
        if (empty($items)) continue;

        $display_title = ($kind === 'process') ? 'Tehnološki postupak' : trim($title, " \t\n\r\0\x0B:");

        $html .= '<section class="dry-recipe-card dry-md-clean-card dry-md-clean-card--' . esc_attr($kind) . '">';
        $html .= '<div class="dry-panel-heading"><span>' . esc_html(str_pad((string)$card_no, 2, '0', STR_PAD_LEFT)) . '</span><h2>' . esc_html($display_title) . '</h2></div>';

        if ($kind === 'process') {
            $html .= '<p class="dry-section-hint">Postupak je prikazan po fazama, kao radni vodič za izradu proizvoda.</p>';
            $html .= drycured_md_render_process_clean($items);
        } elseif ($kind === 'ingredients') {
            $html .= drycured_md_render_ingredients_clean($items);
        } else {
            $html .= drycured_md_render_plain_clean($items);
        }

        $html .= '</section>';
        $card_no++;
    }

    return $html;
}

function drycured_md_extract_sections_clean($markdown) {
    $markdown = trim((string)$markdown);
    if ($markdown === '') return [];

    $markdown = preg_replace('/<!--.*?-->/s', '', $markdown);
    $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
    $lines = explode("\n", $markdown);

    $sections = [];
    $current = null;
    $first_title_skipped = false;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line === '---') continue;

        if (preg_match('/^(#{1,6})\s+(.+)$/u', $line, $m)) {
            $level = strlen($m[1]);
            $title = trim($m[2]);

            if (!$first_title_skipped && $level <= 2) {
                $first_title_skipped = true;
                continue;
            }

            if ($current) $sections[] = $current;
            $current = ['title' => trim($title, " \t\n\r\0\x0B:"), 'items' => []];
            continue;
        }

        if (!$current) continue;

        if (preg_match('/^\*\*(.+?)\*\*$/u', $line, $m)) {
            $current['items'][] = ['type' => 'subtitle', 'text' => trim($m[1], " \t\n\r\0\x0B:")];
            continue;
        }

        if (preg_match('/^[-•]\s+(.+)$/u', $line, $m)) {
            $current['items'][] = ['type' => 'bullet', 'text' => trim($m[1])];
            continue;
        }

        if (preg_match('/^(\d+)[\.\)]\s+(.+)$/u', $line, $m)) {
            $current['items'][] = ['type' => 'step', 'num' => trim($m[1]), 'text' => trim($m[2])];
            continue;
        }

        $current['items'][] = ['type' => 'note', 'text' => $line];
    }

    if ($current) $sections[] = $current;
    return $sections;
}

function drycured_md_kind_clean($title) {
    $h = drycured_lc_ascii($title);

    if (str_contains($h, 'sastoj')) return 'ingredients';
    if (str_contains($h, 'postup') || str_contains($h, 'priprem') || str_contains($h, 'tehnoloski')) return 'process';
    if (str_contains($h, 'tehnik') || str_contains($h, 'napomen')) return 'notes';
    if (str_contains($h, 'regional')) return 'region';
    if (str_contains($h, 'cuvanje') || str_contains($h, 'trajanja')) return 'storage';
    if (str_contains($h, 'greske')) return 'mistakes';
    return 'general';
}

function drycured_lc_ascii($text) {
    $text = remove_accents((string)$text);
    return function_exists('mb_strtolower') ? mb_strtolower($text) : strtolower($text);
}

function drycured_md_render_process_clean($items) {
    $html = '<div class="dry-process-phases dry-md-clean-process">';
    $open = false;

    foreach ($items as $item) {
        $type = $item['type'] ?? '';
        $text = $item['text'] ?? '';

        if ($type === 'subtitle') {
            if ($open) $html .= '</div></article>';
            $html .= '<article class="dry-process-phase dry-md-clean-phase"><h3>' . esc_html($text) . '</h3><div class="dry-md-clean-steps">';
            $open = true;
            continue;
        }

        if (!$open) {
            $html .= '<article class="dry-process-phase dry-md-clean-phase"><h3>Radni koraci</h3><div class="dry-md-clean-steps">';
            $open = true;
        }

        if ($type === 'step') {
            $html .= '<div class="dry-md-clean-step"><span>' . esc_html($item['num'] ?? '') . '</span><p>' . esc_html($text) . '</p></div>';
        } elseif ($type === 'bullet') {
            $html .= '<div class="dry-md-clean-step dry-md-clean-step--bullet"><span>•</span><p>' . esc_html($text) . '</p></div>';
        } else {
            $html .= '<div class="dry-phase-block"><p>' . esc_html($text) . '</p></div>';
        }
    }

    if ($open) $html .= '</div></article>';
    $html .= '</div>';

    return $html;
}

function drycured_md_render_plain_clean($items) {
    $html = '<div class="dry-md-clean-list">';

    foreach ($items as $item) {
        $type = $item['type'] ?? '';
        $text = $item['text'] ?? '';

        if ($type === 'subtitle') {
            $html .= '<article class="dry-md-clean-subtitle"><strong>' . esc_html($text) . '</strong></article>';
        } elseif ($type === 'step') {
            $html .= '<article class="dry-md-clean-item"><span>' . esc_html($item['num'] ?? '') . '</span><p>' . esc_html($text) . '</p></article>';
        } else {
            $html .= '<article class="dry-md-clean-item"><span>•</span><p>' . esc_html($text) . '</p></article>';
        }
    }

    $html .= '</div>';
    return $html;
}

function drycured_md_render_ingredients_clean($items) {
    $html = '<div class="dry-recipe-rows">';

    foreach ($items as $item) {
        if (($item['type'] ?? '') !== 'bullet') continue;

        $text = $item['text'] ?? '';
        $amount = '';
        $name = $text;

        if (preg_match('/^((?:\d+(?:[,.]\d+)?|\d+\/\d+)\s*(?:kg|g|ml|l|kom|komada|cm|mm|%))\s+(.+)$/iu', $text, $m)) {
            $amount = trim($m[1]);
            $name = trim($m[2]);
        }

        $html .= '<article class="dry-recipe-row"><div><strong>' . esc_html($name) . '</strong></div>';
        if ($amount) $html .= '<span>' . esc_html($amount) . '</span>';
        $html .= '</article>';
    }

    $html .= '</div>';
    return $html;
}

/* ---------- Print markdown ---------- */

function drycured_render_full_recipe_markdown($markdown) {
    $markdown = trim((string)$markdown);
    if ($markdown === '') return '';

    $markdown = str_replace(["\r\n", "\r"], "\n", $markdown);
    $lines = explode("\n", $markdown);
    $html = '';
    $list = '';

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            drycured_print_close_list($html, $list);
            continue;
        }

        if (preg_match('/^#{1,6}\s+(.+)$/u', $line, $m)) {
            drycured_print_close_list($html, $list);
            $html .= '<h2>' . esc_html($m[1]) . '</h2>';
            continue;
        }

        if (preg_match('/^\*\*(.+?)\*\*$/u', $line, $m)) {
            drycured_print_close_list($html, $list);
            $html .= '<h3>' . esc_html($m[1]) . '</h3>';
            continue;
        }

        if (preg_match('/^[-•]\s+(.+)$/u', $line, $m)) {
            if ($list !== 'ul') {
                drycured_print_close_list($html, $list);
                $html .= '<ul>';
                $list = 'ul';
            }
            $html .= '<li>' . esc_html($m[1]) . '</li>';
            continue;
        }

        if (preg_match('/^\d+[\.\)]\s+(.+)$/u', $line, $m)) {
            if ($list !== 'ol') {
                drycured_print_close_list($html, $list);
                $html .= '<ol>';
                $list = 'ol';
            }
            $html .= '<li>' . esc_html($m[1]) . '</li>';
            continue;
        }

        drycured_print_close_list($html, $list);
        $html .= '<p>' . esc_html($line) . '</p>';
    }

    drycured_print_close_list($html, $list);
    return '<div class="dry-full-markdown">' . $html . '</div>';
}

function drycured_print_close_list(&$html, &$list) {
    if ($list === 'ul') $html .= '</ul>';
    if ($list === 'ol') $html .= '</ol>';
    $list = '';
}
