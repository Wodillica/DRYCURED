<?php
if (!defined('ABSPATH')) exit;

add_shortcode('drycured_recipes', 'drycured_recipes_shortcode');

function drycured_recipes_shortcode($atts = []) {
    wp_enqueue_style('drycured-recipes'); wp_enqueue_script('drycured-recipes');

    $view     = sanitize_text_field($_GET['dry_view'] ?? 'atlas');
    $search   = sanitize_text_field($_GET['dry_search'] ?? '');
    $country  = sanitize_text_field($_GET['dry_country'] ?? '');
    $region   = sanitize_text_field($_GET['dry_region'] ?? '');
    $category = sanitize_text_field($_GET['dry_category'] ?? '');
    $meat     = sanitize_text_field($_GET['dry_meat'] ?? '');
    $process  = sanitize_text_field($_GET['dry_process'] ?? '');

    if (!in_array($view, ['atlas', 'list', 'cards'], true)) {
        $view = 'atlas';
    }

    $query_args = [
        'post_type'      => 'dry_recipe',
        'post_status'    => 'publish',
        'posts_per_page' => 1000,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];

    if ($search !== '') {
        $query_args['s'] = $search;
    }

    $tax_query = ['relation' => 'AND'];

    $tax_map = [
        'dry_country'          => $country,
        'dry_region'           => $region,
        'dry_product_category' => $category,
        'dry_meat_type'        => $meat,
        'dry_process_type'     => $process,
    ];

    foreach ($tax_map as $taxonomy => $slug) {
        if ($slug !== '') {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $slug,
            ];
        }
    }

    if (count($tax_query) > 1) {
        $query_args['tax_query'] = $tax_query;
    }

    $q = new WP_Query($query_args);

    ob_start();
    ?>
    <section class="drycured-recipes-app drycured-recipes-php">

        <div class="drycured-recipes-head">
            <p class="drycured-small-label">Recepti</p>
            <p class="drycured-subtitle">Pretraži po zemlji, regiji, proizvodu, mesu ili postupku.</p>
        </div>

        <form class="drycured-recipe-form" method="get" action="">
            <input
                type="search"
                name="dry_search"
                value="<?php echo esc_attr($search); ?>"
                class="drycured-recipe-search"
                placeholder="Pretraži recept, regiju, proizvod ili sastojak..."
            >

            <div class="drycured-filter-row">
                <?php drycured_render_filter_select('dry_country', 'Sve zemlje', 'dry_country', $country); ?>
                <?php drycured_render_filter_select('dry_region', 'Sve regije', 'dry_region', $region); ?>
                <?php drycured_render_filter_select('dry_category', 'Sve vrste proizvoda', 'dry_product_category', $category); ?>
                <?php drycured_render_filter_select('dry_meat', 'Sve vrste mesa', 'dry_meat_type', $meat); ?>
                <?php drycured_render_filter_select('dry_process', 'Svi postupci', 'dry_process_type', $process); ?>
            </div>

            <div class="drycured-form-actions">
                <button type="submit" class="drycured-primary-filter">Pretraži</button>
                <a class="drycured-reset-filter" href="<?php echo esc_url(get_permalink()); ?>">Poništi filtere</a>
            </div>

            <input type="hidden" name="dry_view" value="<?php echo esc_attr($view); ?>">
        </form>

        <div class="drycured-view-row">
            <?php echo drycured_view_link('atlas', 'Atlas', $view); ?>
            <?php echo drycured_view_link('list', 'Lista', $view); ?>
            <?php echo drycured_view_link('cards', 'Kartice', $view); ?>
        </div>

        <div class="drycured-recipes-count">
            <?php echo esc_html($q->found_posts); ?> recepata
        </div>

        <?php
        if (!$q->have_posts()) {
            echo '<p class="drycured-empty">Nema recepata za odabrane filtere.</p>';
        } elseif ($view === 'atlas') {
            drycured_render_atlas_view($q->posts);
        } elseif ($view === 'list') {
            drycured_render_list_view($q->posts);
        } else {
            drycured_render_cards_view($q->posts);
        }
        ?>

    </section>
    <?php
    wp_reset_postdata();
    return ob_get_clean();
}

function drycured_render_filter_select($name, $label, $taxonomy, $selected) {
    $extra_args = [];
    if($taxonomy === 'dry_region' && !empty($_GET['dry_country'])) {
        $country_slug = sanitize_text_field($_GET['dry_country']);
        $country_term = get_term_by('slug', $country_slug, 'dry_country');
        if($country_term) {
            $posts = get_posts(['post_type'=>'dry_recipe','post_status'=>'publish','posts_per_page'=>-1,
                'tax_query'=>[['taxonomy'=>'dry_country','field'=>'slug','terms'=>$country_slug]]]);
            $region_ids = [];
            foreach($posts as $p) {
                $rterms = get_the_terms($p->ID, 'dry_region');
                if($rterms) foreach($rterms as $rt) $region_ids[$rt->term_id] = true;
            }
            $extra_args['include'] = array_keys($region_ids);
            if(empty($extra_args['include'])) $extra_args['include'] = [0];
        }
    }
    $terms = get_terms(array_merge([
        'taxonomy'   => $taxonomy,
        'hide_empty' => true,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ], $extra_args));

    $filter_key = str_replace(['dry_country','dry_region','dry_category','dry_meat','dry_process'], ['country','region','category','meat','process'], $name);
    $onchange = ($name === 'dry_country') ? ' onchange="this.form.submit()"' : '';
    echo '<select name="' . esc_attr($name) . '" data-filter="' . esc_attr($filter_key) . '" class="drycured-filter"' . $onchange . '>';
    echo '<option value="">' . esc_html($label) . '</option>';

    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            echo '<option value="' . esc_attr($term->slug) . '" ' . selected($selected, $term->slug, false) . '>';
            echo esc_html($term->name . ' (' . $term->count . ')');
            echo '</option>';
        }
    }

    echo '</select>';
}

function drycured_view_link($target_view, $label, $current_view) {
    $args = $_GET;
    $args['dry_view'] = $target_view;

    $url = esc_url(add_query_arg($args, get_permalink()));
    $class = $target_view === $current_view ? 'drycured-view-btn is-active' : 'drycured-view-btn';

    return '<a class="' . esc_attr($class) . '" href="' . $url . '">' . esc_html($label) . '</a>';
}

function drycured_render_atlas_view($posts) {
    $atlas = [];

    foreach ($posts as $post) {
        $country  = drycured_first_term_or_default($post->ID, 'dry_country', 'Hrvatska');
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', 'Neodređena regija');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', 'Ostalo');

        if (!isset($atlas[$country])) {
            $atlas[$country] = [
                'count' => 0,
                'regions' => [],
            ];
        }

        if (!isset($atlas[$country]['regions'][$region])) {
            $atlas[$country]['regions'][$region] = [
                'count' => 0,
                'categories' => [],
            ];
        }

        if (!isset($atlas[$country]['regions'][$region]['categories'][$category])) {
            $atlas[$country]['regions'][$region]['categories'][$category] = 0;
        }

        $atlas[$country]['count']++;
        $atlas[$country]['regions'][$region]['count']++;
        $atlas[$country]['regions'][$region]['categories'][$category]++;
    }

    echo '<div class="drycured-atlas">';

    foreach ($atlas as $country => $country_data) {
        echo '<section class="drycured-atlas-country">';
        echo '<div class="drycured-atlas-title"><span>' . esc_html($country) . '</span><strong>' . intval($country_data['count']) . '</strong></div>';

        foreach ($country_data['regions'] as $region => $region_data) {
            echo '<div class="drycured-atlas-region">';
            echo '<div class="drycured-atlas-region-title">' . esc_html($region) . ' <span>' . intval($region_data['count']) . '</span></div>';
            echo '<div class="drycured-atlas-cats">';

            foreach ($region_data['categories'] as $category => $count) {
                $url = add_query_arg([
                    'dry_region'   => sanitize_title(remove_accents($region)),
                    'dry_category' => sanitize_title(remove_accents($category)),
                    'dry_view'     => 'list',
                ], get_permalink());

                echo '<a href="' . esc_url($url) . '">' . esc_html($category . ' (' . $count . ')') . '</a>';
            }

            echo '</div>';
            echo '</div>';
        }

        echo '</section>';
    }

    echo '</div>';
}

function drycured_render_list_view($posts) {
    echo '<div class="drycured-list-wrap">';
    echo '<table class="drycured-list-table">';
    echo '<thead><tr>';
    echo '<th>Naziv</th>';
    echo '<th>Zemlja</th>';
    echo '<th>Regija</th>';
    echo '<th>Vrsta</th>';
    echo '<th>Meso</th>';
    echo '<th>Postupak</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody>';

    foreach ($posts as $post) {
        $country  = drycured_first_term_or_default($post->ID, 'dry_country', '');
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', '');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', '');
        $meat     = drycured_terms_join($post->ID, 'dry_meat_type');
        $process  = drycured_terms_join($post->ID, 'dry_process_type');

        echo '<tr>';
        echo '<td><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a></td>';
        echo '<td>' . esc_html($country) . '</td>';
        echo '<td>' . esc_html($region) . '</td>';
        echo '<td>' . esc_html($category) . '</td>';
        echo '<td>' . esc_html($meat) . '</td>';
        echo '<td>' . esc_html($process) . '</td>';
        echo '<td>';

        if (get_post_meta($post->ID, '_dry_calculator_ready', true)) {
            echo '<a class="drycured-mini-action" href="' . esc_url(home_url('/kalkulator/?recipe_id=' . rawurlencode(get_post_meta($post->ID, '_dry_recipe_id', true)))) . '">Izračunaj</a>';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
}

function drycured_render_cards_view($posts) {
    echo '<div class="drycured-card-grid">';

    foreach ($posts as $post) {
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', '');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', '');
        $meat     = drycured_terms_join($post->ID, 'dry_meat_type');

        echo '<article class="drycured-modern-card">';
        echo '<h3><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a></h3>';
        echo '<div class="drycured-card-meta">' . esc_html(implode(' · ', array_filter([$region, $category, $meat]))) . '</div>';
        echo '<p>' . esc_html(get_the_excerpt($post->ID)) . '</p>';
        echo '<div class="drycured-card-actions">';
        echo '<a href="' . esc_url(get_permalink($post->ID)) . '">Otvori</a>';

        if (get_post_meta($post->ID, '_dry_calculator_ready', true)) {
            echo '<a href="' . esc_url(home_url('/kalkulator/?recipe_id=' . rawurlencode(get_post_meta($post->ID, '_dry_recipe_id', true)))) . '">Izračunaj</a>';
        }

        echo '</div>';
        echo '</article>';
    }

    echo '</div>';
}

function drycured_first_term_or_default($post_id, $taxonomy, $default = '') {
    $terms = get_the_terms($post_id, $taxonomy);

    if (!$terms || is_wp_error($terms)) {
        return $default;
    }

    return $terms[0]->name;
}

function drycured_terms_join($post_id, $taxonomy) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (!$terms || is_wp_error($terms)) {
        return '';
    }

    return implode(', ', wp_list_pluck($terms, 'name'));
}
