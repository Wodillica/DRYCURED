<?php
/**
 * Plugin Name: Drycured Recipe v0.6.0 Feature Pack
 * Description: Dodatni feature pack za kanonski web prikaz recepta HR-SL-007.
 * Version: 0.6.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dcv6_feature_pack_content', 1250);

function dcv6_feature_pack_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);

    if ($code !== 'HR-SL-007' || strpos($content, 'dcv5-recipe') === false) {
        return $content;
    }

    $content = dcv6_add_nav_links($content);
    $content = dcv6_insert_before_layout($content, dcv6_render_work_summary());
    $content = dcv6_insert_after_section($content, 'klima', dcv6_render_micro_variations());
    $content = dcv6_insert_after_section($content, 'posluzivanje', dcv6_render_related_recipes($post_id));
    $content = dcv6_insert_after_section($content, 'posluzivanje', dcv6_render_compare_block());
    $content = dcv6_insert_before_section_close($content, 'dnevnik', dcv6_render_digital_batch_diary());
    $content = dcv6_insert_before_main_close($content, dcv6_render_admin_block($post_id));
    $content .= dcv6_render_enhanced_schema($post_id);

    return $content;
}

function dcv6_add_nav_links($content) {
    $content = str_replace(
        '<h3>Sadržaj recepta</h3>',
        '<h3>Sadržaj recepta</h3><a href="#radni-sazetak">Radni sažetak</a>',
        $content
    );

    $content = str_replace(
        '<a href="#kronologija">Procesna kronologija</a>',
        '<a href="#kronologija">Procesna kronologija</a><a href="#varijacije">Varijacije</a>',
        $content
    );

    $content = str_replace(
        '<a href="#dnevnik">Dnevnik šarže</a>',
        '<a href="#povezani">Povezani recepti</a><a href="#usporedba">Usporedba</a><a href="#dnevnik">Dnevnik šarže</a>',
        $content
    );

    return $content;
}

function dcv6_insert_before_layout($content, $html) {
    if (!$html) {
        return $content;
    }

    return str_replace('<div class="dcv5-layout">', $html . "\n" . '<div class="dcv5-layout">', $content);
}

function dcv6_insert_after_section($content, $section_id, $html) {
    if (!$html) {
        return $content;
    }

    $pattern = '/(<section class="dcv5-panel" id="' . preg_quote($section_id, '/') . '">.*?<\/section>)/s';

    return preg_replace($pattern, '$1' . "\n" . $html, $content, 1);
}

function dcv6_insert_before_section_close($content, $section_id, $html) {
    if (!$html) {
        return $content;
    }

    $pattern = '/(<section class="dcv5-panel" id="' . preg_quote($section_id, '/') . '">.*?)(<\/section>)/s';

    return preg_replace($pattern, '$1' . "\n" . $html . "\n" . '$2', $content, 1);
}

function dcv6_insert_before_main_close($content, $html) {
    if (!$html) {
        return $content;
    }

    return str_replace('</main>', $html . "\n" . '</main>', $content);
}

function dcv6_render_work_summary() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-work-summary" id="radni-sazetak">
        <h2><span>✓</span>Radni sažetak prije izrade</h2>
        <p class="dcv5-section-note">
            Ovaj blok služi kao brza provjera prije rada. Ako korisnik pročita samo jedan dio prije početka,
            neka pročita ovaj.
        </p>

        <div class="dcv6-summary-grid">
            <article class="dcv6-summary-card">
                <h3>Za koga je recept</h3>
                <p>Domaća proizvodnja, mala šarža i tradicionalni slavonski stil. Recept je pisan za 10 kg mesne mase i za korisnika koji želi kontroliran, ponovljiv postupak.</p>
            </article>

            <article class="dcv6-summary-card">
                <h3>Najvažnije kontrole</h3>
                <p>Hladna sirovina, ravnomjerno miješanje, punjenje bez zraka, mirno predsušenje, blagi hladni dim i sporo sušenje bez tvrde kore.</p>
            </article>

            <article class="dcv6-summary-card">
                <h3>Ne preskači</h3>
                <p>Ne preskakati predsušenje prije dima, kontrolu mirisa, provjeru ljepljivosti površine i bilježenje gubitka mase tijekom sušenja.</p>
            </article>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_micro_variations() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-variations" id="varijacije">
        <h2><span>V</span>Mikroregionalne varijacije</h2>
        <p class="dcv5-section-note">
            Varijacije ne mijenjaju osnovnu sigurnosnu logiku recepta. One služe za razumijevanje lokalnog stila, začinskog naglaska i ritma dimljenja.
        </p>

        <div class="dcv6-card-grid-three">
            <article class="dcv6-info-card">
                <h3>Slavonska kućna varijanta</h3>
                <p>Uravnotežen odnos paprike, dima i masnoće. Naglasak je na čistom mirisu, stabilnom presjeku i umjerenom dimljenju.</p>
            </article>

            <article class="dcv6-info-card">
                <h3>Baranjski naglasak</h3>
                <p>Može imati puniji začinski dojam i izraženiju papriku, ali bez pretjerivanja s ljutinom. Sušenje mora ostati postupno.</p>
            </article>

            <article class="dcv6-info-card">
                <h3>Srijemski stil</h3>
                <p>Često ide prema nešto izraženijem dimnom karakteru i duljem čuvanju, uz isti oprez: dim mora biti tanak, suh i mirisan.</p>
            </article>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_related_recipes($current_post_id) {
    $related = [
        'HR-SL-001' => 'Slavonski kulen',
        'HR-SL-005' => 'Slavonska domaća kobasica',
        'HR-SL-006' => 'Srijemska kobasica',
        'HR-SL-030' => 'Brodska kobasica',
        'HR-SL-031' => 'Osječka kobasica',
        'HR-SL-037' => 'Vinkovačka kobasica',
        'HR-SL-020' => 'Vinkovačka šunka',
    ];

    $found = [];

    foreach ($related as $code => $fallback_title) {
        $q = new WP_Query([
            'post_type' => 'dry_recipe',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'meta_query' => [
                [
                    'key' => '_dry_recipe_id',
                    'value' => $code,
                    'compare' => '=',
                ],
            ],
        ]);

        if ($q->have_posts()) {
            $q->the_post();
            if (get_the_ID() !== $current_post_id) {
                $found[] = [
                    'title' => get_the_title(),
                    'url' => get_permalink(),
                    'code' => $code,
                ];
            }
        }
        wp_reset_postdata();
    }

    if (!$found) {
        return '';
    }

    ob_start();
    ?>
    <section class="dcv5-panel dcv6-related" id="povezani">
        <h2><span>R</span>Povezani recepti</h2>
        <p class="dcv5-section-note">
            Povezani recepti pomažu usporediti stil, začine, dimljenje i trajanje izrade unutar iste regionalne obitelji.
        </p>

        <div class="dcv6-related-list">
            <?php foreach ($found as $item) : ?>
                <a class="dcv6-related-link" href="<?php echo esc_url($item['url']); ?>">
                    <span><?php echo esc_html($item['code']); ?></span>
                    <strong><?php echo esc_html($item['title']); ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_compare_block() {
    ob_start();
    ?>
    <section class="dcv5-panel dcv6-compare" id="usporedba">
        <h2><span>≠</span>Usporedba recepta</h2>
        <p class="dcv5-section-note">
            Ovaj recept može se spremiti za buduću usporedbu s drugim receptima. Usporedba će koristiti omjer mesa i masnoće, začinski profil, dimljenje, trajanje, kalibar crijeva i gubitak mase.
        </p>

        <div class="dcv6-compare-box">
            <button type="button" class="dcv6-compare-button" data-recipe-code="HR-SL-007" data-recipe-title="Ratarske kobasice">
                Dodaj Ratarske kobasice u usporedbu
            </button>
            <p class="dcv6-compare-status" aria-live="polite"></p>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_digital_batch_diary() {
    ob_start();
    ?>
    <div class="dcv6-digital-diary">
        <h3>Digitalni dnevnik šarže</h3>
        <p>Unesi početnu i trenutnu masu. Sustav izračunava gubitak mase i pomaže pratiti sušenje.</p>

        <div class="dcv6-diary-grid">
            <label>
                Datum početka
                <input type="date" class="dcv6-diary-input" id="dcv6-start-date">
            </label>

            <label>
                Početna masa (kg)
                <input type="number" step="0.001" min="0" class="dcv6-diary-input" id="dcv6-start-mass" placeholder="10.000">
            </label>

            <label>
                Trenutna masa (kg)
                <input type="number" step="0.001" min="0" class="dcv6-diary-input" id="dcv6-current-mass" placeholder="7.300">
            </label>

            <label>
                Datum mjerenja
                <input type="date" class="dcv6-diary-input" id="dcv6-check-date">
            </label>
        </div>

        <div class="dcv6-diary-result" id="dcv6-diary-result">
            Gubitak mase pojavit će se nakon unosa vrijednosti.
        </div>

        <textarea class="dcv6-diary-notes" id="dcv6-diary-notes" rows="4" placeholder="Bilješke: miris, površina, boja, vlaga, dimljenje, korekcije uvjeta..."></textarea>

        <div class="dcv6-diary-actions">
            <button type="button" class="dcv6-diary-save">Spremi bilješke u pregledniku</button>
            <button type="button" class="dcv6-diary-clear">Očisti dnevnik</button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function dcv6_render_admin_block($post_id) {
    if (!current_user_can('manage_options')) {
        return '';
    }

    $code = get_post_meta($post_id, '_dry_recipe_id', true);
    $thumb_id = get_post_thumbnail_id($post_id);
    $status = get_post_status($post_id);

    ob_start();
    ?>
    <section class="dcv5-panel dcv6-admin-block" id="admin-status">
        <h2><span>A</span>Urednički status</h2>
        <p class="dcv5-section-note">
            Ovaj blok vide samo administratori. Ne prikazuje se javnim korisnicima.
        </p>

        <div class="dcv6-admin-grid">
            <div><strong>WP ID</strong><span><?php echo esc_html($post_id); ?></span></div>
            <div><strong>Recept ID</strong><span><?php echo esc_html($code); ?></span></div>
            <div><strong>Status objave</strong><span><?php echo esc_html($status); ?></span></div>
            <div><strong>Featured image</strong><span><?php echo $thumb_id ? esc_html($thumb_id) : 'nije postavljena'; ?></span></div>
            <div><strong>View standard</strong><span>Drycured Recipe View v0.6.0</span></div>
            <div><strong>Javni urednički podaci</strong><span>sakriveni</span></div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function dcv6_render_enhanced_schema($post_id) {
    $image = get_the_post_thumbnail_url($post_id, 'large');

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        '@id' => get_permalink($post_id) . '#drycured-recipe-v060',
        'name' => 'Ratarske kobasice',
        'description' => 'Standardizirani drycured recept za ratarske kobasice, šarža 10 kg, hladno dimljenje, sušenje, zrenje i pakiranje.',
        'image' => $image ? [$image] : [],
        'recipeYield' => '10 kg mesne mase',
        'recipeCuisine' => 'Hrvatska',
        'recipeCategory' => 'Suhomesnati proizvod',
        'keywords' => 'ratarske kobasice, slavonska kobasica, suha kobasica, hladno dimljenje, sušenje, zrenje',
        'totalTime' => 'P60D',
        'recipeIngredient' => [
            '7,000 kg mješovitog svinjskog mesa',
            '3,000 kg tvrđe svinjske slanine ili masnijih obrezaka',
            '220 g kuhinjske soli',
            '115 g slatke mljevene paprike',
            '30 g ljute mljevene paprike',
            '12 g crnog papra',
            '7 g kima',
            '30 g svježeg češnjaka za ekstrakciju',
            '0,060 L prokuhane i ohlađene vode',
        ],
        'recipeInstructions' => [
            ['@type' => 'HowToStep', 'name' => 'Priprema mesa', 'text' => 'Raditi s hladnom sirovinom i ukloniti žilave dijelove.'],
            ['@type' => 'HowToStep', 'name' => 'Mljevenje', 'text' => 'Meso i masnije dijelove mljeti kroz rešetku 6–8 mm.'],
            ['@type' => 'HowToStep', 'name' => 'Miješanje', 'text' => 'Dodati sol, papriku, papar, kim i procijeđenu češnjakovu tekućinu.'],
            ['@type' => 'HowToStep', 'name' => 'Punjenje', 'text' => 'Puniti u svinjska crijeva 32–42 mm bez zračnih džepova.'],
            ['@type' => 'HowToStep', 'name' => 'Početna fermentacija i stabilizacija', 'text' => 'Nakon punjenja proizvod kratko stabilizirati u hladnom i prozračnom prostoru.'],
            ['@type' => 'HowToStep', 'name' => 'Predsušenje', 'text' => 'Površina se mora prosušiti prije prvog dima.'],
            ['@type' => 'HowToStep', 'name' => 'Dimljenje', 'text' => 'Dimiti hladnim, tankim i suhim dimom u blagim ciklusima.'],
            ['@type' => 'HowToStep', 'name' => 'Sušenje', 'text' => 'Sušiti postupno do stabilnog gubitka mase.'],
            ['@type' => 'HowToStep', 'name' => 'Zrenje', 'text' => 'Zreti dok presjek ne postane povezan i miris čist.'],
            ['@type' => 'HowToStep', 'name' => 'Pakiranje', 'text' => 'Pakirati tek kada proizvod više ne otpušta vlagu.'],
        ],
    ];

    return '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

add_action('wp_footer', 'dcv6_feature_pack_footer', 100000);

function dcv6_feature_pack_footer() {
    ?>
    <style>
        body.single-dry_recipe .dcv6-summary-grid,
        body.single-dry_recipe .dcv6-card-grid-three {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 14px !important;
        }

        body.single-dry_recipe .dcv6-summary-card,
        body.single-dry_recipe .dcv6-info-card,
        body.single-dry_recipe .dcv6-compare-box,
        body.single-dry_recipe .dcv6-digital-diary,
        body.single-dry_recipe .dcv6-admin-block .dcv6-admin-grid > div {
            background: #fffdf7 !important;
            border: 1px solid #e6cf98 !important;
            border-radius: 18px !important;
            padding: 18px 19px !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe .dcv6-summary-card h3,
        body.single-dry_recipe .dcv6-info-card h3,
        body.single-dry_recipe .dcv6-digital-diary h3 {
            margin: 0 0 10px !important;
            color: #10182d !important;
            font-size: 17px !important;
            font-weight: 900 !important;
            line-height: 1.32 !important;
        }

        body.single-dry_recipe .dcv6-summary-card p,
        body.single-dry_recipe .dcv6-info-card p,
        body.single-dry_recipe .dcv6-digital-diary p {
            margin: 0 !important;
            color: #43506a !important;
            font-size: 15.3px !important;
            line-height: 1.68 !important;
        }

        body.single-dry_recipe .dcv6-related-list {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        body.single-dry_recipe .dcv6-related-link {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 15px 17px !important;
            border-radius: 16px !important;
            border: 1px solid #e6cf98 !important;
            background: #fffdf7 !important;
            text-decoration: none !important;
            color: #10182d !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .03) !important;
        }

        body.single-dry_recipe .dcv6-related-link span {
            display: inline-flex !important;
            min-width: 76px !important;
            justify-content: center !important;
            padding: 7px 9px !important;
            border-radius: 999px !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            font-size: 12px !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv6-related-link strong {
            font-size: 15.5px !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe .dcv6-compare-button,
        body.single-dry_recipe .dcv6-diary-save,
        body.single-dry_recipe .dcv6-diary-clear {
            border: 1px solid #111b33 !important;
            background: #111b33 !important;
            color: #fffaf0 !important;
            border-radius: 999px !important;
            padding: 11px 16px !important;
            font-weight: 900 !important;
            cursor: pointer !important;
        }

        body.single-dry_recipe .dcv6-diary-clear {
            background: #f1dfb6 !important;
            border-color: #d5b46b !important;
            color: #111b33 !important;
        }

        body.single-dry_recipe .dcv6-compare-status {
            margin: 12px 0 0 !important;
            color: #43506a !important;
            font-weight: 800 !important;
        }

        body.single-dry_recipe .dcv6-diary-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 12px !important;
            margin: 16px 0 !important;
        }

        body.single-dry_recipe .dcv6-diary-grid label {
            color: #10182d !important;
            font-size: 13px !important;
            font-weight: 900 !important;
            line-height: 1.35 !important;
        }

        body.single-dry_recipe .dcv6-diary-input,
        body.single-dry_recipe .dcv6-diary-notes {
            width: 100% !important;
            margin-top: 7px !important;
            border: 1px solid #e2c98e !important;
            border-radius: 12px !important;
            padding: 10px 11px !important;
            background: #fffaf0 !important;
            color: #10182d !important;
        }

        body.single-dry_recipe .dcv6-diary-result {
            margin: 12px 0 !important;
            padding: 14px 15px !important;
            border-radius: 14px !important;
            border: 1px solid #e2c98e !important;
            background: #fff8e8 !important;
            color: #10182d !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe .dcv6-diary-actions {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            margin-top: 12px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid strong {
            display: block !important;
            color: #8a733c !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: .06em !important;
            margin-bottom: 5px !important;
        }

        body.single-dry_recipe .dcv6-admin-grid span {
            color: #10182d !important;
            font-weight: 900 !important;
        }

        @media print {
            body.single-dry_recipe .site-header,
            body.single-dry_recipe .site-footer,
            body.single-dry_recipe .dcv5-side-panel,
            body.single-dry_recipe .dcv5-hero-media,
            body.single-dry_recipe .dcv5-actions,
            body.single-dry_recipe #profil,
            body.single-dry_recipe #varijacije,
            body.single-dry_recipe #povezani,
            body.single-dry_recipe #usporedba,
            body.single-dry_recipe .dcv6-admin-block,
            body.single-dry_recipe #wpadminbar,
            body.single-dry_recipe .dc-floating-compare,
            body.single-dry_recipe .dcv5-btn {
                display: none !important;
            }

            body.single-dry_recipe,
            body.single-dry_recipe .site-content {
                background: #fff !important;
            }

            body.single-dry_recipe .dcv5-recipe {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
            }

            body.single-dry_recipe .dcv5-panel,
            body.single-dry_recipe .dcv5-hero,
            body.single-dry_recipe .dcv5-quick-card {
                box-shadow: none !important;
                break-inside: avoid !important;
            }
        }

        @media (max-width: 980px) {
            body.single-dry_recipe .dcv6-summary-grid,
            body.single-dry_recipe .dcv6-card-grid-three,
            body.single-dry_recipe .dcv6-related-list,
            body.single-dry_recipe .dcv6-diary-grid,
            body.single-dry_recipe .dcv6-admin-grid {
                grid-template-columns: 1fr !important;
            }
        }
    </style>

    <script>
        (function () {
            const profile = {
                code: 'HR-SL-007',
                title: 'Ratarske kobasice',
                baseKg: 10,
                ingredients: {
                    meatKg: 7,
                    fatKg: 3,
                    saltG: 220,
                    sweetPaprikaG: 115,
                    hotPaprikaG: 30,
                    pepperG: 12,
                    cuminG: 7,
                    garlicG: 30,
                    garlicWaterL: 0.060
                }
            };

            window.drycuredRecipeProfile = profile;
            try {
                localStorage.setItem('drycured_recipe_profile_HR-SL-007', JSON.stringify(profile));
            } catch (e) {}

            document.addEventListener('DOMContentLoaded', function () {
                const compareButton = document.querySelector('.dcv6-compare-button');
                if (compareButton) {
                    compareButton.addEventListener('click', function () {
                        let list = [];
                        try {
                            list = JSON.parse(localStorage.getItem('drycured_compare_recipes') || '[]');
                        } catch (e) {
                            list = [];
                        }

                        const item = {
                            code: compareButton.dataset.recipeCode,
                            title: compareButton.dataset.recipeTitle,
                            url: window.location.href
                        };

                        if (!list.some(x => x.code === item.code)) {
                            list.push(item);
                        }

                        localStorage.setItem('drycured_compare_recipes', JSON.stringify(list));

                        const status = document.querySelector('.dcv6-compare-status');
                        if (status) {
                            status.textContent = 'Recept je dodan u pripremu za usporedbu.';
                        }
                    });
                }

                const startMass = document.getElementById('dcv6-start-mass');
                const currentMass = document.getElementById('dcv6-current-mass');
                const result = document.getElementById('dcv6-diary-result');
                const notes = document.getElementById('dcv6-diary-notes');
                const startDate = document.getElementById('dcv6-start-date');
                const checkDate = document.getElementById('dcv6-check-date');

                function updateLoss() {
                    if (!startMass || !currentMass || !result) return;

                    const start = parseFloat(String(startMass.value).replace(',', '.'));
                    const current = parseFloat(String(currentMass.value).replace(',', '.'));

                    if (!start || !current || current > start) {
                        result.textContent = 'Gubitak mase pojavit će se nakon ispravnog unosa vrijednosti.';
                        return;
                    }

                    const lossKg = start - current;
                    const lossPct = (lossKg / start) * 100;

                    let status = 'u tijeku';
                    if (lossPct >= 25 && lossPct <= 30) {
                        status = 'ciljani raspon za ovaj recept';
                    } else if (lossPct > 30) {
                        status = 'iznad ciljanog raspona; provjeri tvrdoću i presjek';
                    }

                    result.textContent = 'Gubitak mase: ' + lossKg.toFixed(3).replace('.', ',') + ' kg · ' + lossPct.toFixed(1).replace('.', ',') + ' % · ' + status;
                }

                [startMass, currentMass].forEach(function (el) {
                    if (el) el.addEventListener('input', updateLoss);
                });

                const key = 'drycured_batch_diary_HR-SL-007';

                function loadDiary() {
                    try {
                        const data = JSON.parse(localStorage.getItem(key) || '{}');
                        if (startDate && data.startDate) startDate.value = data.startDate;
                        if (checkDate && data.checkDate) checkDate.value = data.checkDate;
                        if (startMass && data.startMass) startMass.value = data.startMass;
                        if (currentMass && data.currentMass) currentMass.value = data.currentMass;
                        if (notes && data.notes) notes.value = data.notes;
                        updateLoss();
                    } catch (e) {}
                }

                function saveDiary() {
                    const data = {
                        startDate: startDate ? startDate.value : '',
                        checkDate: checkDate ? checkDate.value : '',
                        startMass: startMass ? startMass.value : '',
                        currentMass: currentMass ? currentMass.value : '',
                        notes: notes ? notes.value : ''
                    };
                    localStorage.setItem(key, JSON.stringify(data));
                    if (result) result.textContent = 'Dnevnik je spremljen u ovom pregledniku.';
                }

                function clearDiary() {
                    localStorage.removeItem(key);
                    if (startDate) startDate.value = '';
                    if (checkDate) checkDate.value = '';
                    if (startMass) startMass.value = '';
                    if (currentMass) currentMass.value = '';
                    if (notes) notes.value = '';
                    if (result) result.textContent = 'Dnevnik je očišćen.';
                }

                const saveBtn = document.querySelector('.dcv6-diary-save');
                const clearBtn = document.querySelector('.dcv6-diary-clear');

                if (saveBtn) saveBtn.addEventListener('click', saveDiary);
                if (clearBtn) clearBtn.addEventListener('click', clearDiary);

                loadDiary();

                if (new URLSearchParams(window.location.search).get('recipe') === 'HR-SL-007') {
                    try {
                        localStorage.setItem('drycured_calculator_requested_recipe', 'HR-SL-007');
                    } catch (e) {}
                }
            });
        })();
    </script>
    <?php
}
