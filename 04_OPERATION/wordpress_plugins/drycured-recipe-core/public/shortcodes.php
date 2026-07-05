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

    // Special case: 'neodredena-regija' filters posts with NO dry_region taxonomy term
    if ($region === 'neodredena-regija') {
        $tax_query = [['taxonomy' => 'dry_region', 'operator' => 'NOT EXISTS']];
        $region = '';
    } else {
        $tax_query = ['relation' => 'AND'];
    }

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

    // Apply tax_query if any filters active (including NOT EXISTS for neodredena-regija)
    $has_tax_filter = count($tax_query) > 1 || (count($tax_query) === 1 && isset($tax_query[0]['operator']));
    if ($has_tax_filter) {
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


/**
 * TRAJNA, STATIČKA lista kanonskih regija po državi.
 *
 * NE MIJENJATI ad-hoc — svaka izmjena zahtijeva eksplicitnu potvrdu Davora.
 * Ova lista određuje što se prikazuje u Atlas prikazu (/recepti/?dry_view=atlas),
 * neovisno o tome koje taxonomy terme trenutno imaju postovi u bazi.
 *
 * Kanonske HR regije prema HNK/tradicionalnoj regionalnoj podjeli (14 regija).
 * Ostale države: navedene su najprepoznatljivije administrativne/kulinarsko-tradicijske
 * regije. Proširiti samo na Davorov zahtjev.
 *
 * @return array  Associative: country_name => [region_name, ...]
 */
function drycured_canonical_regions_by_country() {
    return [
        // --- HRVATSKA (14 kanonskih regija) ---
        'Hrvatska' => [
            'Slavonija', 'Baranja', 'Srijem', 'Posavina', 'Podravina',
            'Lika', 'Kvarner', 'Istra', 'Dalmacija', 'Banija',
            'Gorski kotar', 'Zagorje', 'Međimurje', 'Središnja Hrvatska', 'Turopolje', 'Pokuplje',
        ],
        // --- ITALIJA ---
        'Italija' => [
            'Emilia-Romagna', 'Alto Adige', 'Toscana', 'Calabria', 'Marche', 'Piemonte',
            'Lombardia', 'Veneto', 'Sardegna', 'Sicilia', 'Lazio', 'Umbria',
            'Trentino', 'Friuli', 'Campania', 'Puglia', 'Italija',
        ],
        // --- GRČKA ---
        'Grčka' => [
            'Makedonia', 'Peloponez', 'Kreta', 'Epir', 'Egejski otoci',
            'Jonski otoci', 'Trakija', 'Tesalija', 'Atika', 'Cipar', 'Grčka',
        ],
        // --- NORVEŠKA ---
        'Norveška' => [
            'Vestlandet', 'Østlandet', 'Sørlandet', 'Trøndelag', 'Nord-Norge', 'Norveška',
        ],
        // --- BUGARSKA ---
        'Bugarska' => [
            'Stara Planina', 'Trakija', 'Rodopi', 'Strandža', 'Bugarska',
        ],
        // --- NJEMAČKA ---
        'Njemačka' => [
            'Bavarska', 'Turingija', 'Vestfalija', 'Schleswig-Holstein',
            'Baden-Württemberg', 'Porajnje', 'Saksonija', 'Njemačka',
        ],
        // --- AUSTRIJA ---
        'Austrija' => [
            'Tirol', 'Vorarlberg', 'Štajerska', 'Koruška', 'Salzburg',
            'Donja Austrija', 'Gornja Austrija', 'Burgenland', 'Austrija',
        ],
        // --- ŠVICARSKA ---
        'Švicarska' => [
            'Graubünden', 'Valais', 'Appenzell', 'Bern', 'Zürich', 'Ticino', 'Švicarska',
        ],
        // --- FRANCUSKA ---
        'Francuska' => [
            'Bourgogne-Franche-Comté', 'Nouvelle-Aquitaine', 'Pays Basque', 'Bayonne',
            'Normandie', 'Bretagne', 'Alsace', 'Auvergne-Rhône-Alpes', 'Provence', 'Korzika', 'Francuska',
        ],
        // --- UJEDINJENO KRALJEVSTVO ---
        'Ujedinjeno Kraljevstvo' => [
            'Engleska', 'Škotska', 'Wales', 'Sjeverna Irska',
        ],
        // --- BELGIJA ---
        'Belgija' => [
            'Flandrija', 'Valonija', 'Ardeni', 'Antwerpen', 'Belgija',
        ],
        // --- ŠPANJOLSKA ---
        'Španjolska' => [
            'Extremadura', 'Castilla y León', 'Katalonija', 'Baskija',
            'Navarra', 'Andaluzija', 'Aragonija', 'Španjolska',
        ],
        // --- LITVA ---
        'Litva' => [
            'Suvalkija', 'Žemaitija', 'Aukštaitija', 'Dzūkija', 'Litva',
        ],
        // --- SLOVENIJA ---
        'Slovenija' => [
            'Gornja Savinjska dolina', 'Prekmurje', 'Prlekija', 'Gorenjska', 'Primorska', 'Dolenjska', 'Slovenija',
        ],
        // --- RUMUNJSKA ---
        'Rumunjska' => [
            'Transilvanija', 'Muntenija', 'Oltenija', 'Moldavija (RO)', 'Rumunjska',
        ],
        // --- IRSKA ---
        'Irska' => [
            'Munster', 'Leinster', 'Connacht', 'Ulster', 'Irska',
        ],
        // --- FINSKA ---
        'Finska' => [
            'Lapland', 'Karelija', 'Savonia', 'Häme', 'Finska',
        ],
        // --- PORTUGAL ---
        'Portugal' => [
            'Alentejo', 'Minho', 'Trás-os-Montes', 'Algarve', 'Portugal',
        ],
        // --- ŠVEDSKA ---
        'Švedska' => [
            'Götaland', 'Svealand', 'Norrland', 'Smöland', 'Švedska',
        ],
        // --- BOSNA I HERCEGOVINA ---
        'Češka' => ['Bohemija', 'Moravija', 'Šlezija'],
        'Latvija' => ['Vidzeme', 'Kurzeme', 'Zemgale', 'Latgale'],
        'Bjelorusija' => ['Minsk', 'Grodno', 'Brest', 'Vitebsk'],
        'Srbija' => [
            'Vojvodina', 'Šumadija', 'Zapadna Srbija', 'Istočna Srbija',
            'Južna i Istočna Srbija', 'Kosovo i Metohija', 'Beograd',
        ],
        'Sjeverna Makedonija' => [
            'Vardar', 'Istočna Makedonija', 'Jugozapadna Makedonija',
            'Pelagonia', 'Polog', 'Skoplje', 'Jugoistočna Makedonija', 'Severoistočna Makedonija',
        ],
        'Bosna i Hercegovina' => [
            'Hercegovina', 'Bosna', 'Bosna i Hercegovina',
        ],
        // --- CRNA GORA ---
        'Crna Gora' => [
            'Primorska Crna Gora', 'Sjeverna Crna Gora', 'Crna Gora',
        ],
        // --- POLSKA ---
        'Poljska' => [
            'Maloposka', 'Šleska', 'Mazovija', 'Pomerania', 'Poljska',
        ],
        // --- Jednočlane države (manji broj recepata) ---
        'Albanija'    => ['Albanija'],
        'Danska'      => ['Danska'],
        'Estonija'    => ['Estonija'],
        'Island'      => ['Island'],
        'Kosovo'      => ['Kosovo'],
        'Mađarska'    => ['Mađarska'],
        'Malta'       => ['Malta'],
        'Moldavija'   => ['Moldavija'],
        'Nizozemska'  => ['Nizozemska'],
        'Rusija'      => ['Rusija'],
        'Slovačka'    => ['Slovačka'],
        'Turska'      => ['Sjeverni Cipar', 'Turska'],
        'Ukrajna'     => ['Ukrajna'],
    ];
}

function drycured_render_atlas_view($posts) {
    $atlas = [];

    foreach ($posts as $post) {
        $country  = drycured_first_term_or_default($post->ID, 'dry_country', 'Hrvatska');
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', 'Neodređena regija');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', 'Ostalo');

        if (!isset($atlas[$country])) {
            $atlas[$country] = ['count' => 0, 'regions' => []];
        }
        if (!isset($atlas[$country]['regions'][$region])) {
            $atlas[$country]['regions'][$region] = ['count' => 0, 'categories' => []];
        }
        if (!isset($atlas[$country]['regions'][$region]['categories'][$category])) {
            $atlas[$country]['regions'][$region]['categories'][$category] = 0;
        }
        $atlas[$country]['count']++;
        $atlas[$country]['regions'][$region]['count']++;
        $atlas[$country]['regions'][$region]['categories'][$category]++;
    }

    // Dopuni Hrvatsku svim kanonskim regijama (prikazati i one s 0 recepata)
    $canonical = drycured_canonical_regions_by_country();
    foreach ($canonical as $can_country => $can_regions) {
        if (!isset($atlas[$can_country])) {
            $atlas[$can_country] = ['count' => 0, 'regions' => []];
        }
        foreach ($can_regions as $can_region) {
            if (!isset($atlas[$can_country]['regions'][$can_region])) {
                $atlas[$can_country]['regions'][$can_region] = ['count' => 0, 'categories' => []];
            }
        }
    }

    // Sortiraj po broju recepata
    uasort($atlas, fn($a,$b) => $b['count'] - $a['count']);

    $current_country = sanitize_text_field($_GET['dry_country'] ?? '');
    $current_region  = sanitize_text_field($_GET['dry_region']  ?? '');

    echo '<div class="drycured-atlas">';

    foreach ($atlas as $country => $country_data) {
        $country_slug = sanitize_title(remove_accents($country));
        // Otvori zemlju SAMO ako je eksplicitno odabrana
        $is_open = ($current_country === $country_slug);
        // Ako je filter aktivan, prikaži samo odabranu zemlju
        if ($current_country !== '' && $current_country !== $country_slug) continue;

        echo '<section class="drycured-atlas-country' . ($is_open ? ' is-open' : '') . '" data-country="' . esc_attr($country_slug) . '">';

        // Naslov zemlje — klikabilni toggle
        $country_url = add_query_arg([
            'dry_country' => $country_slug,
            'dry_view'    => 'atlas',
        ], get_permalink());

        echo '<div class="drycured-atlas-title drycured-country-toggle" role="button" tabindex="0">';
        echo '<span>' . esc_html($country) . '</span>';
        echo '<strong>' . intval($country_data['count']) . '</strong>';
        echo '<span class="drycured-toggle-icon">›</span>';
        echo '</div>';

        // Regije — skrivene dok zemlja nije otvorena
        echo '<div class="drycured-atlas-regions">';

        // Sort: non-zero count regions first (by count DESC), then zero-count regions in canonical order
        $canonical_order = $canonical[$country] ?? [];
        uksort($country_data['regions'], function($a, $b) use ($country_data, $canonical_order) {
            $ca = $country_data['regions'][$a]['count'] ?? 0;
            $cb = $country_data['regions'][$b]['count'] ?? 0;
            if ($ca !== $cb) return $cb - $ca;
            // same count: preserve canonical order for 0-count regions
            $ia = array_search($a, $canonical_order);
            $ib = array_search($b, $canonical_order);
            if ($ia === false) $ia = 999;
            if ($ib === false) $ib = 999;
            return $ia - $ib;
        });

        foreach ($country_data['regions'] as $region => $region_data) {
            // Preskoči besmislene regije
            if (strlen($region) > 60 || strpos($region, '*') !== false) continue;

            $region_slug = sanitize_title(remove_accents($region));
            $reg_is_open = ($current_region === $region_slug);

            echo '<div class="drycured-atlas-region' . ($reg_is_open ? ' is-open' : '') . '" data-region="' . esc_attr($region_slug) . '">';

            // Naslov regije — klikabilni toggle
            echo '<div class="drycured-atlas-region-title drycured-region-toggle" role="button" tabindex="0">';
            echo '<span>' . esc_html($region) . '</span>';
            echo '<em>' . intval($region_data['count']) . '</em>';
            echo '<span class="drycured-toggle-icon">›</span>';
            echo '</div>';

            // Kategorije — skrivene dok regija nije otvorena
            echo '<div class="drycured-atlas-cats">';

            arsort($region_data['categories']);
            foreach ($region_data['categories'] as $category => $count) {
                $url = add_query_arg([
                    'dry_country'  => $country_slug,
                    'dry_region'   => $region_slug,
                    'dry_category' => sanitize_title(remove_accents($category)),
                    'dry_view'     => 'list',
                ], get_permalink());

                echo '<a href="' . esc_url($url) . '" class="drycured-cat-link">';
                echo esc_html($category) . ' <span>(' . $count . ')</span>';
                echo '</a>';
            }

            // Link "Prikaži sve recepte iz regije"
            $all_url = add_query_arg([
                'dry_country' => $country_slug,
                'dry_region'  => $region_slug,
                'dry_view'    => 'list',
            ], get_permalink());
            echo '<a href="' . esc_url($all_url) . '" class="drycured-cat-link drycured-cat-all">';
            echo 'Svi recepti <span>(' . intval($region_data['count']) . ')</span>';
            echo '</a>';

            echo '</div>'; // .drycured-atlas-cats
            echo '</div>'; // .drycured-atlas-region
        }

        echo '</div>'; // .drycured-atlas-regions
        echo '</section>'; // .drycured-atlas-country
    }

    echo '</div>'; // .drycured-atlas

    // Inline JS za toggle
    echo '<script>
    document.querySelectorAll(".drycured-country-toggle").forEach(function(btn){
        btn.addEventListener("click", function(){
            var section = btn.closest(".drycured-atlas-country");
            section.classList.toggle("is-open");
        });
    });
    document.querySelectorAll(".drycured-region-toggle").forEach(function(btn){
        btn.addEventListener("click", function(e){
            e.stopPropagation();
            var region = btn.closest(".drycured-atlas-region");
            region.classList.toggle("is-open");
        });
    });
    </script>';
}

function drycured_render_list_view($posts) {
    $per_page = 30;
    $current_page = max(1, intval($_GET['dry_page'] ?? 1));
    $total = count($posts);
    $total_pages = ceil($total / $per_page);
    $offset = ($current_page - 1) * $per_page;
    $paged = array_slice($posts, $offset, $per_page);

    echo '<div class="drycured-list-wrap">';

    // Paginacija gore
    if ($total_pages > 1) {
        drycured_render_pagination($current_page, $total_pages, $total);
    }

    echo '<table class="drycured-list-table">';
    echo '<thead><tr>';
    echo '<th>Naziv</th><th>Zemlja</th><th>Regija</th><th>Vrsta</th><th></th>';
    echo '</tr></thead><tbody>';

    foreach ($paged as $post) {
        $country  = drycured_first_term_or_default($post->ID, 'dry_country', '—');
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', '—');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', '—');

        echo '<tr>';
        echo '<td><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a></td>';
        echo '<td>' . esc_html($country) . '</td>';
        echo '<td>' . esc_html($region) . '</td>';
        echo '<td><span class="drycured-cat-badge">' . esc_html($category) . '</span></td>';
        echo '<td><a class="drycured-btn-sm" href="' . esc_url(get_permalink($post->ID)) . '">Otvori →</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    // Paginacija dolje
    if ($total_pages > 1) {
        drycured_render_pagination($current_page, $total_pages, $total);
    }

    echo '</div>';
}

function drycured_render_cards_view($posts) {
    $per_page = 24;
    $current_page = max(1, intval($_GET['dry_page'] ?? 1));
    $total = count($posts);
    $total_pages = ceil($total / $per_page);
    $offset = ($current_page - 1) * $per_page;
    $paged = array_slice($posts, $offset, $per_page);

    if ($total_pages > 1) {
        drycured_render_pagination($current_page, $total_pages, $total);
    }

    echo '<div class="drycured-card-grid">';

    foreach ($paged as $post) {
        $country  = drycured_first_term_or_default($post->ID, 'dry_country', '');
        $region   = drycured_first_term_or_default($post->ID, 'dry_region', '');
        $category = drycured_first_term_or_default($post->ID, 'dry_product_category', '');
        $thumb    = get_the_post_thumbnail_url($post->ID, 'medium');

        echo '<article class="drycured-modern-card">';

        if ($thumb) {
            echo '<div class="drycured-card-thumb"><img src="' . esc_url($thumb) . '" alt="" loading="lazy"></div>';
        } else {
            echo '<div class="drycured-card-thumb drycured-card-thumb--empty"><span>🥩</span></div>';
        }

        echo '<div class="drycured-card-body">';
        echo '<div class="drycured-card-meta">';
        if ($country) echo '<span>' . esc_html($country) . '</span>';
        if ($region && $region !== $country) echo '<span>' . esc_html($region) . '</span>';
        echo '</div>';
        echo '<h3><a href="' . esc_url(get_permalink($post->ID)) . '">' . esc_html(get_the_title($post->ID)) . '</a></h3>';
        if ($category) echo '<span class="drycured-cat-badge">' . esc_html($category) . '</span>';
        echo '<div class="drycured-card-actions">';
        echo '<a class="drycured-btn-sm" href="' . esc_url(get_permalink($post->ID)) . '">Otvori recept</a>';
        echo '</div>';
        echo '</div>';
        echo '</article>';
    }

    echo '</div>';

    if ($total_pages > 1) {
        drycured_render_pagination($current_page, $total_pages, $total);
    }
}


function drycured_render_pagination($current, $total_pages, $total_items) {
    $args = $_GET;
    echo '<div class="drycured-pagination">';
    echo '<span class="drycured-pagination__info">' . intval($total_items) . ' recepata</span>';
    for ($i = 1; $i <= $total_pages; $i++) {
        $args['dry_page'] = $i;
        $url = esc_url(add_query_arg($args, get_permalink()));
        $cls = $i === $current ? 'drycured-page-btn is-active' : 'drycured-page-btn';
        // Prikaži samo +/- 2 stranice oko trenutne
        if ($i === 1 || $i === $total_pages || abs($i - $current) <= 2) {
            echo '<a class="' . $cls . '" href="' . $url . '">' . $i . '</a>';
        } elseif (abs($i - $current) === 3) {
            echo '<span class="drycured-page-ellipsis">…</span>';
        }
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
