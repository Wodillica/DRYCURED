<?php
if (!defined('ABSPATH')) exit;

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
     * Privremeni sigurnosni guard za dva javna MD-only zapisa koji su
     * rušili stari public renderer kroz memory exhaustion u WordPress
     * formatting.php. Podaci se ne mijenjaju; zapisi se samo izuzimaju
     * iz starog renderera dok ne dobiju kontrolirani V5/V6 prikaz.
     */
    if (in_array((int) $post_id, [3064, 3068], true)) {
        return $content;
    }

    $data_raw = get_post_meta($post_id, '_dry_recipe_data', true);
    $data = $data_raw ? json_decode($data_raw, true) : [];

    if (!is_array($data)) {
        $data = [];
    }

    $public = (!empty($data['public_recipe']) && is_array($data['public_recipe']))
        ? $data['public_recipe']
        : [];

    $title = get_the_title($post_id);
    $intro = $public['intro'] ?? ($data['short_description'] ?? get_the_excerpt($post_id));

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

    $calculator = $data['calculator'] ?? [];
    $calculator_enabled = !empty($calculator['enabled']) || !empty($data['calculator_ready']) || get_post_meta($post_id, '_dry_calculator_ready', true);
    $calculator_url = drycured_recipe_calculator_link($post_id, $data);

    ob_start();
    ?>
    <article class="dry-recipe-page">

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
                        <a class="dry-btn dry-btn-primary" href="<?php echo esc_url($calculator_url); ?>">
                            Izračunaj sastojke
                        </a>
                    <?php else: ?>
                        <span class="dry-btn dry-btn-disabled">Kalkulator u pripremi</span>
                    <?php endif; ?>

                    <button class="dry-btn dry-btn-secondary" onclick="window.print(); return false;">
                        Ispiši recept
                    </button>
                </div>
            </div>
        </section>

        <section class="dry-recipe-card dry-recipe-facts">
            <h2>Osnovni podaci</h2>
            <div class="dry-facts-grid">
                <?php
                if (!empty($quick) && is_array($quick)) {
                    foreach ($quick as $label => $value) {
                        echo drycured_fact_item($label, $value);
                    }
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
                        <div class="dry-panel-heading">
                            <span>01</span>
                            <h2>Mesni sastav</h2>
                        </div>
                        <p class="dry-section-hint">Preporučeni dijelovi mesa za osnovnu šaržu.</p>
                        <?php echo drycured_render_recipe_rows($meat_rows, 'meat'); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($ingredient_rows)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading">
                            <span>02</span>
                            <h2>Sastojci i začini</h2>
                        </div>
                        <p class="dry-section-hint">Količine su navedene za osnovnu šaržu recepta.</p>
                        <?php echo drycured_render_recipe_rows($ingredient_rows, 'ingredient'); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($casing)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading">
                            <span>03</span>
                            <h2>Crijeva / omotači</h2>
                        </div>
                        <?php echo drycured_render_casing_block($casing); ?>
                    </section>
                <?php endif; ?>
            </div>

            <div class="dry-recipe-right">
                <?php if (!empty($phases)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading">
                            <span>04</span>
                            <h2>Tehnološki postupak</h2>
                        </div>
                        <p class="dry-section-hint">Postupak je prikazan po fazama: što radiš, zašto je važno, kako provjeravaš i što učiniti ako krene loše.</p>
                        <?php echo drycured_render_process_phases($phases); ?>
                    </section>
                <?php endif; ?>

                <?php if (!empty($mistakes)): ?>
                    <section class="dry-recipe-card">
                        <div class="dry-panel-heading">
                            <span>05</span>
                            <h2>Najčešće greške i rješenja</h2>
                        </div>
                        <?php echo drycured_render_mistakes($mistakes); ?>
                    </section>
                <?php endif; ?>
            </div>

        </section>
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

    if (!empty($calculator['calculator_key'])) {
        $args['product'] = $calculator['calculator_key'];
    }

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

        $html .= '<article class="dry-recipe-row">';
        $html .= '<div>';
        $html .= '<strong>' . esc_html($name) . '</strong>';
        if ($note) {
            $html .= '<p>' . esc_html($note) . '</p>';
        }
        $html .= '</div>';
        if ($amount) {
            $html .= '<span>' . esc_html($amount) . '</span>';
        }
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

        if (!empty($phase['what'])) {
            $html .= '<div class="dry-phase-block"><strong>Što radiš</strong><p>' . esc_html($phase['what']) . '</p></div>';
        }

        if (!empty($phase['why'])) {
            $html .= '<div class="dry-phase-block"><strong>Zašto je važno</strong><p>' . esc_html($phase['why']) . '</p></div>';
        }

        if (!empty($phase['control'])) {
            $html .= '<div class="dry-phase-block"><strong>Kontrolni znak</strong><p>' . esc_html($phase['control']) . '</p></div>';
        }

        if (!empty($phase['risk']) || !empty($phase['fix'])) {
            $html .= '<div class="dry-phase-warning">';
            if (!empty($phase['risk'])) {
                $html .= '<p><strong>Rizik:</strong> ' . esc_html($phase['risk']) . '</p>';
            }
            if (!empty($phase['fix'])) {
                $html .= '<p><strong>Rješenje:</strong> ' . esc_html($phase['fix']) . '</p>';
            }
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

    if (isset($row['amount_kg_min']) && isset($row['amount_kg_max'])) {
        return drycured_number($row['amount_kg_min']) . '–' . drycured_number($row['amount_kg_max']) . ' kg';
    }
    if (isset($row['amount_g_min']) && isset($row['amount_g_max'])) {
        return drycured_number($row['amount_g_min']) . '–' . drycured_number($row['amount_g_max']) . ' g';
    }
    if (isset($row['amount_l_min']) && isset($row['amount_l_max'])) {
        return drycured_number($row['amount_l_min']) . '–' . drycured_number($row['amount_l_max']) . ' L';
    }
    if (isset($row['amount_pieces'])) return drycured_number($row['amount_pieces']) . ' kom';

    return '';
}

function drycured_number($value) {
    if (!is_numeric($value)) return $value;
    $value = floatval($value);
    if (floor($value) == $value) return (string)intval($value);
    return rtrim(rtrim(number_format($value, 3, ',', ''), '0'), ',');
}
