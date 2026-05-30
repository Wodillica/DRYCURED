<?php
/**
 * Plugin Name: Drycured Recipe View v0.5 Pilot
 * Description: Pilot prikaz recepta kao proizvodni vodič za HR-SL-007.
 * Version: 0.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('the_content', 'dcv5_recipe_view_pilot_content', 1200);

function dcv5_recipe_view_pilot_content($content) {
    if (!is_singular('dry_recipe') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $post_id = get_the_ID();
    $code = get_post_meta($post_id, '_dry_recipe_id', true);

    if ($code !== 'HR-SL-007') {
        return $content;
    }

    $recipe = dcv5_ratarske_kobasice_profile($post_id);

    $image_url = get_the_post_thumbnail_url($post_id, 'large');
    if (!$image_url) {
        $image_url = get_post_meta($post_id, '_dry_recipe_image_url', true);
    }

    $calculator_url = add_query_arg(
        ['recipe' => $recipe['code']],
        home_url('/kalkulator/')
    );

    ob_start();
    dcv5_render_recipe_schema($recipe);
    ?>
    <style>
        .single-dry_recipe .entry-header,
        .single-dry_recipe .entry-meta {
            display: none !important;
        }

        .single-dry_recipe .ast-container,
        .single-dry_recipe .site-content .ast-container,
        .single-dry_recipe .content-area,
        .single-dry_recipe main.site-main {
            max-width: 1340px !important;
            width: 100% !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .dcv5-recipe {
            max-width: 1220px;
            margin: 0 auto;
            padding: 34px 18px 64px;
            color: #111b33;
        }

        .dcv5-hero,
        .dcv5-panel,
        .dcv5-side-panel {
            background: #fffaf0;
            border: 1px solid #e2c98e;
            border-radius: 22px;
            box-shadow: 0 14px 34px rgba(25, 32, 48, .08);
        }

        .dcv5-hero {
            padding: 30px 34px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
        }

        .dcv5-hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 26px;
            align-items: center;
        }

        .dcv5-hero-media {
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2c98e;
            background: #f5e7c5;
            box-shadow: 0 12px 26px rgba(25, 32, 48, .10);
        }

        .dcv5-hero-media img {
            display: block;
            width: 100%;
            height: 280px;
            object-fit: cover;
        }

        .dcv5-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .dcv5-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 15px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 900;
            border: 1px solid #d5b46b;
            transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
        }

        .dcv5-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(25, 32, 48, .12);
        }

        .dcv5-btn-primary {
            background: #111b33;
            color: #fffaf0;
            border-color: #111b33;
        }

        .dcv5-btn-secondary {
            background: #f1dfb6;
            color: #111b33;
        }

        .dcv5-hero:before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top right, rgba(216,166,63,.18), transparent 36%);
            pointer-events: none;
        }

        .dcv5-hero-inner {
            position: relative;
            z-index: 1;
        }

        .dcv5-kicker {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .dcv5-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 11px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            color: #10182d;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .dcv5-hero h1 {
            margin: 0 0 12px;
            font-size: clamp(34px, 4.5vw, 56px);
            line-height: 1.04;
            color: #0d172d;
        }

        .dcv5-lead {
            max-width: 880px;
            margin: 0;
            color: #3e4a63;
            font-size: 17px;
            line-height: 1.7;
        }

        .dcv5-quick-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin: 18px 0 24px;
        }

        .dcv5-quick-card {
            background: #fffdf7;
            border: 1px solid #e6cf97;
            border-radius: 16px;
            padding: 13px 14px;
        }

        .dcv5-quick-card span,
        .dcv5-field-label {
            display: block;
            margin-bottom: 5px;
            color: #7d6a3c;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .dcv5-quick-card strong {
            display: block;
            color: #111b33;
            font-size: 15px;
            line-height: 1.35;
        }

        .dcv5-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 300px;
            gap: 22px;
            align-items: start;
        }

        .dcv5-panel {
            padding: 24px 28px;
            margin-bottom: 20px;
        }

        .dcv5-panel h2 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            font-size: 24px;
            line-height: 1.2;
            color: #111b33;
        }

        .dcv5-panel h2 span {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #d8a63f;
            color: #10182c;
            font-size: 14px;
            font-weight: 900;
        }

        .dcv5-section-note {
            margin: -4px 0 18px;
            color: #4a566e;
            font-size: 15px;
            line-height: 1.6;
        }

        .dcv5-composition {
            display: grid;
            grid-template-columns: 7fr 3fr;
            gap: 8px;
            margin: 12px 0 4px;
        }

        .dcv5-comp-bar {
            min-height: 46px;
            border-radius: 14px;
            padding: 12px 14px;
            background: #d8a63f;
            color: #10182c;
            font-weight: 900;
        }

        .dcv5-comp-bar:nth-child(2) {
            background: #f0d99a;
        }

        .dcv5-card-grid {
            display: grid;
            gap: 12px;
        }

        .dcv5-card-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dcv5-card-grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dcv5-ingredient-card,
        .dcv5-process-card,
        .dcv5-error-card,
        .dcv5-serving-card,
        .dcv5-profile-row,
        .dcv5-climate-card {
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-ingredient-card h3,
        .dcv5-process-card h3,
        .dcv5-error-card h3,
        .dcv5-serving-card h3,
        .dcv5-climate-card h3 {
            margin: 0 0 9px;
            color: #111b33;
            font-size: 17px;
            line-height: 1.3;
        }

        .dcv5-amount-line {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-bottom: 10px;
        }

        .dcv5-amount {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            background: #111b33;
            color: #fffaf0;
            font-weight: 900;
            font-size: 14px;
        }

        .dcv5-percent,
        .dcv5-rate {
            display: inline-flex;
            padding: 7px 10px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            color: #111b33;
            font-weight: 800;
            font-size: 13px;
        }

        .dcv5-ingredient-card p,
        .dcv5-process-card p,
        .dcv5-error-card p,
        .dcv5-serving-card p,
        .dcv5-climate-card p {
            margin: 0;
            color: #3b4861;
            font-size: 15.5px;
            line-height: 1.65;
        }

        .dcv5-profile-row {
            display: grid;
            grid-template-columns: 125px minmax(0, 1fr) 44px;
            gap: 10px;
            align-items: center;
            margin-bottom: 9px;
        }

        .dcv5-profile-name {
            font-weight: 800;
            color: #111b33;
            font-size: 14px;
        }

        .dcv5-profile-track {
            height: 10px;
            border-radius: 999px;
            background: #efe2c3;
            overflow: hidden;
        }

        .dcv5-profile-fill {
            height: 100%;
            border-radius: 999px;
            background: #d8a63f;
        }

        .dcv5-profile-score {
            font-size: 13px;
            font-weight: 900;
            color: #111b33;
        }

        .dcv5-timeline {
            position: relative;
            display: grid;
            gap: 12px;
        }

        .dcv5-timeline-item {
            display: grid;
            grid-template-columns: 104px minmax(0, 1fr);
            gap: 14px;
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-day {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 14px;
            background: #d8a63f;
            color: #10182c;
            font-weight: 900;
            text-align: center;
            padding: 8px;
        }

        .dcv5-critical {
            margin-top: 10px;
            padding: 10px 12px;
            border-left: 4px solid #d8a63f;
            background: #fff6dd;
            border-radius: 10px;
            color: #3a455d;
            font-size: 14px;
            line-height: 1.55;
        }

        .dcv5-error-card {
            border-left: 5px solid #d8a63f;
        }

        .dcv5-error-card.danger {
            border-left-color: #9f2f2f;
        }

        .dcv5-error-card.warning {
            border-left-color: #d8a63f;
        }

        .dcv5-error-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 8px 0 10px;
        }

        .dcv5-small-pill {
            display: inline-flex;
            padding: 5px 8px;
            border-radius: 999px;
            background: #f1dfb6;
            border: 1px solid #d5b46b;
            font-size: 12px;
            font-weight: 800;
            color: #111b33;
        }

        .dcv5-side-panel {
            position: sticky;
            top: 96px;
            padding: 16px;
        }

        .dcv5-side-panel h3 {
            margin: 0 0 12px;
            color: #111b33;
            font-size: 15px;
        }

        .dcv5-side-panel a {
            display: block;
            padding: 8px 10px;
            border-radius: 10px;
            color: #26385c;
            text-decoration: none;
            font-size: 14px;
        }

        .dcv5-side-panel a:hover {
            background: #f3e3bd;
        }

        .dcv5-print-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .dcv5-check-grid,
        .dcv5-safety-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .dcv5-check-card,
        .dcv5-safety-card {
            background: #fffdf7;
            border: 1px solid #ecd9aa;
            border-radius: 16px;
            padding: 15px;
        }

        .dcv5-check-card h3,
        .dcv5-safety-card h3 {
            margin: 0 0 8px;
            font-size: 16px;
            color: #111b33;
            line-height: 1.3;
        }

        .dcv5-check-card p,
        .dcv5-safety-card p {
            margin: 0;
            color: #3b4861;
            font-size: 15px;
            line-height: 1.6;
        }

        .dcv5-safety-card.green {
            border-left: 6px solid #4b8f5a;
        }

        .dcv5-safety-card.yellow {
            border-left: 6px solid #d8a63f;
        }

        .dcv5-safety-card.red {
            border-left: 6px solid #a83a3a;
        }

        .dcv5-print-button-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px 0 0;
        }

        @media print {
            header.site-header,
            footer.site-footer,
            .dcv5-side-panel,
            .dcv5-hero-media,
            .dcv5-actions,
            #profil,
            #klima {
                display: none !important;
            }

            .dcv5-recipe {
                max-width: none !important;
                padding: 0 !important;
            }

            .dcv5-layout {
                display: block !important;
            }

            .dcv5-panel,
            .dcv5-hero,
            .dcv5-quick-card {
                box-shadow: none !important;
                break-inside: avoid;
            }

            body {
                background: #fff !important;
            }
        }

        .dcv5-print-box {
            min-height: 64px;
            border: 1px dashed #d5b46b;
            border-radius: 14px;
            padding: 10px;
            background: #fffdf7;
        }

        @media (max-width: 980px) {
            .dcv5-hero-grid {
                grid-template-columns: 1fr;
            }

            .dcv5-hero-media img {
                height: 240px;
            }

            .dcv5-layout {
                grid-template-columns: 1fr;
            }

            .dcv5-side-panel {
                position: static;
            }

            .dcv5-quick-strip,
            .dcv5-card-grid.three,
            .dcv5-card-grid.two,
            .dcv5-print-strip,
            .dcv5-check-grid,
            .dcv5-safety-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .dcv5-recipe {
                padding: 24px 12px 42px;
            }

            .dcv5-hero,
            .dcv5-panel {
                padding: 20px 18px;
                border-radius: 16px;
            }

            .dcv5-quick-strip,
            .dcv5-card-grid.three,
            .dcv5-card-grid.two,
            .dcv5-print-strip,
            .dcv5-check-grid,
            .dcv5-safety-grid {
                grid-template-columns: 1fr;
            }

            .dcv5-timeline-item {
                grid-template-columns: 1fr;
            }

            .dcv5-profile-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="dcv5-recipe">
        <header class="dcv5-hero" id="vrh">
            <div class="dcv5-hero-grid">
                <div class="dcv5-hero-inner">
                    <div class="dcv5-kicker">
                        <span class="dcv5-badge">DIGITALNA PUŠNICA</span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['code']); ?></span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['region']); ?></span>
                        <span class="dcv5-badge"><?php echo esc_html($recipe['type']); ?></span>
                    </div>

                    <h1><?php echo esc_html($recipe['title']); ?></h1>
                    <p class="dcv5-lead"><?php echo esc_html($recipe['lead']); ?></p>

                    <div class="dcv5-actions">
                        <a class="dcv5-btn dcv5-btn-primary" href="<?php echo esc_url($calculator_url); ?>">
                            Otvori kalkulator za ovaj recept
                        </a>
                        <a class="dcv5-btn dcv5-btn-secondary" href="#dnevnik">
                            Dnevnik šarže
                        </a>
                        <a class="dcv5-btn dcv5-btn-secondary" href="javascript:window.print()">
                            Ispiši radnu verziju
                        </a>
                    </div>
                </div>

                <?php if ($image_url) : ?>
                    <figure class="dcv5-hero-media">
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($recipe['title']); ?>">
                    </figure>
                <?php endif; ?>
            </div>
        </header>

        <section class="dcv5-quick-strip" aria-label="Brzi proizvodni sažetak">
            <?php foreach ($recipe['quick'] as $item) : ?>
                <article class="dcv5-quick-card">
                    <span><?php echo esc_html($item['label']); ?></span>
                    <strong><?php echo esc_html($item['value']); ?></strong>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="dcv5-layout">
            <main>
                <section class="dcv5-panel" id="omjer">
                    <h2><span>1</span>Omjer smjese</h2>
                    <p class="dcv5-section-note">Brzi pregled odnosa glavnih sirovina u šarži od 10 kg. Ovaj omjer čuva sočnost, ali i omogućuje stabilno sušenje.</p>

                    <div class="dcv5-composition">
                        <div class="dcv5-comp-bar">70 % meso</div>
                        <div class="dcv5-comp-bar">30 % slanina</div>
                    </div>
                </section>

                <section class="dcv5-panel" id="sirovine">
                    <h2><span>2</span>Glavne sirovine</h2>
                    <p class="dcv5-section-note">Meso i slanina prikazuju se u kilogramima jer čine osnovu smjese.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['materials'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="zacini">
                    <h2><span>3</span>Začini i dodaci</h2>
                    <p class="dcv5-section-note">Začini se prikazuju u gramima, uz postotak i g/kg gdje je korisno. Time korisnik dobiva i radnu vrijednost i tehnološki omjer.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['spices'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="tekucine">
                    <h2><span>4</span>Tekućine i češnjak</h2>
                    <p class="dcv5-section-note">Tekućine se prikazuju u litrama. Češnjak se ne dodaje kao komadić, nego kao procijeđena aromatična tekućina.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['liquids'] as $item) : dcv5_card($item); endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="profil">
                    <h2><span>5</span>Profil proizvoda</h2>
                    <p class="dcv5-section-note">Senzorni profil pomaže korisniku da odmah razumije karakter proizvoda prije nego krene u izradu.</p>

                    <?php foreach ($recipe['profile'] as $item) : ?>
                        <div class="dcv5-profile-row">
                            <div class="dcv5-profile-name"><?php echo esc_html($item['name']); ?></div>
                            <div class="dcv5-profile-track"><div class="dcv5-profile-fill" style="width: <?php echo esc_attr($item['score'] * 10); ?>%"></div></div>
                            <div class="dcv5-profile-score"><?php echo esc_html($item['score']); ?>/10</div>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="dcv5-panel" id="klima">
                    <h2><span>6</span>Klimatski i tehnološki potpis</h2>
                    <p class="dcv5-section-note">Uvjeti prostora važni su koliko i začini. Dobra kobasica nastaje iz ritma hladnoće, vlage, dima i vremena.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['climate'] as $item) : ?>
                            <article class="dcv5-climate-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="kronologija">
                    <h2><span>7</span>Procesna kronologija</h2>
                    <p class="dcv5-section-note">Ovo je radni vodič od sirovine do stola. Svaka faza ima cilj i kritičnu točku kontrole.</p>

                    <div class="dcv5-timeline">
                        <?php foreach ($recipe['timeline'] as $step) : ?>
                            <article class="dcv5-timeline-item">
                                <div class="dcv5-day"><?php echo esc_html($step['day']); ?></div>
                                <div>
                                    <h3><?php echo esc_html($step['title']); ?></h3>
                                    <p><?php echo esc_html($step['text']); ?></p>
                                    <div class="dcv5-critical"><strong>Kritično:</strong> <?php echo esc_html($step['critical']); ?></div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="greske">
                    <h2><span>8</span>Anatomija greške</h2>
                    <p class="dcv5-section-note">Svaki problem mora imati konkretno rješenje. Ovo je zaštitni dio recepta.</p>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['errors'] as $error) : ?>
                            <article class="dcv5-error-card <?php echo esc_attr($error['level']); ?>">
                                <h3><?php echo esc_html($error['problem']); ?></h3>
                                <div class="dcv5-error-meta">
                                    <span class="dcv5-small-pill"><?php echo esc_html($error['phase']); ?></span>
                                    <span class="dcv5-small-pill"><?php echo esc_html($error['severity']); ?></span>
                                </div>
                                <p><strong>Uzrok:</strong> <?php echo esc_html($error['cause']); ?></p>
                                <p><strong>Rješenje:</strong> <?php echo esc_html($error['solution']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="gotovo">
                    <h2><span>9</span>Gotovo je kad…</h2>
                    <p class="dcv5-section-note">Ovaj blok pomaže korisniku procijeniti je li proizvod tehnološki stabilan za rezanje, čuvanje i posluživanje.</p>

                    <div class="dcv5-check-grid">
                        <?php foreach ($recipe['done_when'] as $item) : ?>
                            <article class="dcv5-check-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="sigurnost">
                    <h2><span>10</span>Sigurnosni semafor</h2>
                    <p class="dcv5-section-note">Kod suhomesnatih proizvoda svaka sumnja mora imati praktičnu odluku. Bolje odbaciti rizičan proizvod nego spašavati nešto što se ne smije spašavati.</p>

                    <div class="dcv5-safety-grid">
                        <?php foreach ($recipe['safety'] as $item) : ?>
                            <article class="dcv5-safety-card <?php echo esc_attr($item['level']); ?>">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="posluzivanje">
                    <h2><span>11</span>Posluživanje i čuvanje</h2>

                    <div class="dcv5-card-grid two">
                        <?php foreach ($recipe['serving'] as $item) : ?>
                            <article class="dcv5-serving-card">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="dcv5-panel" id="dnevnik">
                    <h2><span>12</span>Dnevnik šarže</h2>
                    <p class="dcv5-section-note">Ovaj blok je priprema za budući print i digitalni dnevnik. Za ozbiljnu proizvodnju bilješke vrijede zlata.</p>

                    <div class="dcv5-print-strip">
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Datum početka</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Masa šarže</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Kalibar crijeva</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">T/RH prostora</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Broj dimljenja</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Gubitak mase</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Datum rezanja</span></div>
                        <div class="dcv5-print-box"><span class="dcv5-field-label">Ocjena 1–10</span></div>
                    </div>
                </section>
            </main>

            <aside>
                <nav class="dcv5-side-panel" aria-label="Sadržaj recepta">
                    <h3>Sadržaj recepta</h3>
                    <a href="#omjer">Omjer smjese</a>
                    <a href="#sirovine">Glavne sirovine</a>
                    <a href="#zacini">Začini i dodaci</a>
                    <a href="#tekucine">Tekućine i češnjak</a>
                    <a href="#profil">Profil proizvoda</a>
                    <a href="#klima">Tehnološki potpis</a>
                    <a href="#kronologija">Procesna kronologija</a>
                    <a href="#greske">Anatomija greške</a>
                    <a href="#gotovo">Gotovo je kad…</a>
                    <a href="#sigurnost">Sigurnosni semafor</a>
                    <a href="#posluzivanje">Posluživanje</a>
                    <a href="#dnevnik">Dnevnik šarže</a>
                </nav>
            </aside>
        </div>
    </div>
    <?php

    return ob_get_clean();
}

function dcv5_card($item) {
    ?>
    <article class="dcv5-ingredient-card">
        <h3><?php echo esc_html($item['name']); ?></h3>
        <div class="dcv5-amount-line">
            <span class="dcv5-amount"><?php echo esc_html($item['amount']); ?></span>
            <?php if (!empty($item['percent'])) : ?><span class="dcv5-percent"><?php echo esc_html($item['percent']); ?></span><?php endif; ?>
            <?php if (!empty($item['rate'])) : ?><span class="dcv5-rate"><?php echo esc_html($item['rate']); ?></span><?php endif; ?>
        </div>
        <p><?php echo esc_html($item['note']); ?></p>
    </article>
    <?php
}

function dcv5_render_recipe_schema($recipe) {
    $ingredients = [];

    foreach (['materials', 'spices', 'liquids'] as $group) {
        foreach ($recipe[$group] as $item) {
            $ingredients[] = $item['amount'] . ' ' . $item['name'];
        }
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Recipe',
        'name' => $recipe['title'],
        'description' => $recipe['lead'],
        'recipeCategory' => 'Suhomesnati proizvod',
        'recipeCuisine' => 'Hrvatska',
        'recipeYield' => '10 kg mesne mase',
        'totalTime' => 'P60D',
        'recipeIngredient' => $ingredients,
        'recipeInstructions' => array_map(function ($step) {
            return [
                '@type' => 'HowToStep',
                'name' => $step['title'],
                'text' => $step['text'] . ' Kritično: ' . $step['critical'],
            ];
        }, $recipe['timeline']),
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function dcv5_ratarske_kobasice_profile($post_id) {
    return [
        'code' => 'HR-SL-007',
        'title' => 'Ratarske kobasice',
        'region' => 'Slavonija',
        'type' => 'Kobasica',
        'lead' => 'Ratarske kobasice su slavonske trajne kobasice za šaržu od 10 kg mesne mase, s jasnim omjerom mesa i masnoće, začinima u gramima, hladnim dimljenjem i postupnim sušenjem.',
        'quick' => [
            ['label' => 'Šarža', 'value' => '10 kg mesne mase'],
            ['label' => 'Trajanje', 'value' => '30–60 dana'],
            ['label' => 'Dimljenje', 'value' => 'hladni dim'],
            ['label' => 'Crijeva', 'value' => '32–42 mm'],
            ['label' => 'Gubitak mase', 'value' => '25–30 %'],
        ],
        'materials' => [
            ['name' => 'Mješovito svinjsko meso', 'amount' => '7,000 kg', 'percent' => '70 %', 'rate' => '', 'note' => 'Vrat, obresci rebara, plećka i čisti dijelovi glave. Osnova okusa, strukture i vezanja nadjeva.'],
            ['name' => 'Tvrđa svinjska slanina ili masniji obresci', 'amount' => '3,000 kg', 'percent' => '30 %', 'rate' => '', 'note' => 'Daje sočnost, mekoću presjeka i tradicionalni seoski karakter.'],
        ],
        'spices' => [
            ['name' => 'Kuhinjska sol', 'amount' => '220 g', 'percent' => '2,20 %', 'rate' => '22 g/kg', 'note' => 'Radna vrijednost za ovu recepturu. Sol mora biti ravnomjerno raspoređena po cijeloj smjesi.'],
            ['name' => 'Slatka mljevena paprika', 'amount' => '115 g', 'percent' => '1,15 %', 'rate' => '11,5 g/kg', 'note' => 'Slavonski tip paprike; mora biti svježa, mirisna i bez gorčine.'],
            ['name' => 'Ljuta mljevena paprika', 'amount' => '30 g', 'percent' => '0,30 %', 'rate' => '3 g/kg', 'note' => 'Daje umjerenu pikantnost. Ne povećavati bez probne šarže.'],
            ['name' => 'Crni papar', 'amount' => '12 g', 'percent' => '0,12 %', 'rate' => '1,2 g/kg', 'note' => 'Najbolje grubo mljeven neposredno prije rada.'],
            ['name' => 'Kim', 'amount' => '7 g', 'percent' => '0,07 %', 'rate' => '0,7 g/kg', 'note' => 'Prepoznatljiv dodatak ratarskom stilu; ne smije preuzeti okus kobasice.'],
        ],
        'liquids' => [
            ['name' => 'Svježi češnjak za ekstrakciju', 'amount' => '30 g', 'percent' => '0,30 %', 'rate' => '3 g/kg', 'note' => 'Češnjak se zgnječi, namače i procijedi. U nadjev se ne dodaju vlakna češnjaka.'],
            ['name' => 'Prokuhana i ohlađena voda', 'amount' => '0,060 L', 'percent' => '0,60 %', 'rate' => '6 ml/kg', 'note' => 'Koristi se za ekstrakciju češnjaka. U nadjev ide samo procijeđena tekućina.'],
        ],
        'profile' => [
            ['name' => 'Paprika', 'score' => 7],
            ['name' => 'Dim', 'score' => 6],
            ['name' => 'Ljutina', 'score' => 4],
            ['name' => 'Slanoća', 'score' => 6],
            ['name' => 'Masnoća', 'score' => 6],
            ['name' => 'Tekstura', 'score' => 6],
        ],
        'climate' => [
            ['title' => 'Izrada', 'text' => 'Meso i masnoća trebaju ostati hladni, idealno 0–4 °C. Hladna sirovina daje čitljiv presjek i sprječava razmazivanje masnoće. Ako se masa počne lijepiti ili mazati, rad treba prekinuti i sirovinu vratiti na hlađenje.'],
            ['title' => 'Početna fermentacija', 'text' => 'Kod ove tradicionalne kobasice fermentacija je blaga i prirodna, bez posebno vođene starter kulture. Počinje tijekom odležavanja nadjeva i nastavlja se prvih 12–24 sata nakon punjenja. Cilj je stabilizacija mirisa, boje i vezanja nadjeva, bez naglog kiseljenja.'],
            ['title' => 'Predsušenje', 'text' => 'Nakon početne stabilizacije kobasice trebaju 24–48 sati mirnog predsušenja na 8–12 °C i relativnoj vlazi oko 80–85 %. Površina mora postati suha na dodir, ali ne tvrda. Mokra površina ne prima dim pravilno.'],
            ['title' => 'Dimljenje', 'text' => 'Dimljenje je aromatska i površinska faza, a ne način brzog sušenja. Koristiti tanak hladni dim od bukve, graba ili hrasta. Temperatura dima ne bi trebala prelaziti 20–22 °C, uz pauze između ciklusa.'],
            ['title' => 'Sušenje', 'text' => 'Sušenje je faza kontroliranog gubitka vode i mase. Nakon dimljenja kobasice trebaju 10–15 °C, relativnu vlagu 70–80 % i blago strujanje zraka. Cilj je postupno smanjivanje mase bez tvrde kore.'],
            ['title' => 'Zrenje', 'text' => 'Zrenje počinje kada je proizvod površinski stabilan i kada se gubitak vlage uspori. U ovoj fazi razvijaju se aroma, boja presjeka i tekstura. Miris mora ostati čist, paprikast i dimljen, bez kiselih ili truležnih nota.'],
        ],
        'timeline' => [
            ['day' => 'Dan 1', 'title' => 'Priprema mesa', 'text' => 'Očistiti meso, ukloniti žilave dijelove i raditi s dobro ohlađenom sirovinom.', 'critical' => 'Temperatura sirovine mora ostati niska da se masnoća ne razmaže.'],
            ['day' => 'Dan 1', 'title' => 'Mljevenje', 'text' => 'Meso i masnije dijelove mljeti kroz rešetku 6–8 mm, bez ponavljanja mljevenja.', 'critical' => 'Ako se masa lijepi i maže, treba prekinuti rad i ohladiti sirovinu.'],
            ['day' => 'Dan 1', 'title' => 'Miješanje', 'text' => 'Dodati sol, papriku, papar, kim i procijeđenu češnjakovu tekućinu. Miješati 8–12 minuta.', 'critical' => 'Začini moraju biti ravnomjerni, ali nadjev se ne smije pregrijati.'],
            ['day' => 'Dan 1–2', 'title' => 'Odležavanje nadjeva', 'text' => 'Nadjev držati 12 sati na 2–6 °C, pokriven i zaštićen od isušivanja.', 'critical' => 'Miris mora ostati čist, paprikast i bez kiselih nota.'],
            ['day' => 'Dan 2', 'title' => 'Punjenje', 'text' => 'Puniti u svinjska crijeva 32–42 mm, čvrsto, ali bez pucanja.', 'critical' => 'Zračne džepove odmah probosti čistom iglom.'],
            ['day' => 'Dan 2–3', 'title' => 'Početna fermentacija / stabilizacija', 'text' => 'Nakon punjenja kobasice miruju u hladnom i prozračnom prostoru. Nadjev se stabilizira, sol i začini se dodatno povezuju s masom, a površina se priprema za predsušenje.', 'critical' => 'Fermentacija ne smije krenuti naglo. Ako se pojavi izražen kiseo miris, sluzavost ili napuhavanje crijeva, postupak treba zaustaviti i proizvod ne koristiti bez sigurne procjene.'],
            ['day' => 'Dan 3–5', 'title' => 'Predsušenje', 'text' => 'Kobasice objesiti tako da se ne dodiruju. Površina se mora prosušiti prije prvog dima.', 'critical' => 'Ne dimiti dok je površina mokra, sjajna ili ljepljiva.'],
            ['day' => 'Dan 5–15', 'title' => 'Dimljenje', 'text' => 'Dimiti hladnim dimom u 3–6 blagih ciklusa, uz pauze između dimljenja.', 'critical' => 'Prejak dim daje gorčinu i pretamnu površinu. Dim mora biti tanak, suh i mirisan.'],
            ['day' => 'Dan 15–35', 'title' => 'Sušenje', 'text' => 'Nakon dimljenja kobasice sušiti na 10–15 °C i 70–80 % relativne vlage, uz blago strujanje zraka.', 'critical' => 'Pretvrda površina i mekana sredina znače prebrzo sušenje ili prenizku vlagu.'],
            ['day' => 'Dan 35–60', 'title' => 'Zrenje', 'text' => 'U završnoj fazi proizvod se stabilizira, okus se zaokružuje, a presjek postaje povezan i mirisan.', 'critical' => 'Ako se pojavi neugodan kiseo, truležan ili užegao miris, proizvod ne treba spašavati začinima.'],
            ['day' => 'Dan 60+', 'title' => 'Pakiranje i čuvanje', 'text' => 'Gotove kobasice pakirati tek kada su stabilne, površinski suhe i mirisno čiste. Za kratko čuvanje prikladan je papir ili prozračna ambalaža, a vakuumiranje se koristi samo kada proizvod više ne otpušta vlagu.', 'critical' => 'Ne vakuumirati prerano. Ako se u pakiranju pojavi vlaga, neugodan miris ili ljepljiva površina, proizvod izvaditi, provjeriti i ne konzumirati ako postoji sumnja u kvarenje.'],
        ],
        'errors' => [
            ['problem' => 'Razmazana mast', 'phase' => 'Mljevenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Sirovina je bila pretopla ili je masnoća previše obrađivana.', 'solution' => 'Raditi s ohlađenom sirovinom, mljeti samo jednom i napraviti pauzu ako se masnoća počne mazati.'],
            ['problem' => 'Zračni džepovi', 'phase' => 'Punjenje', 'severity' => 'Rizik kvarenja', 'level' => 'danger', 'cause' => 'Labavo punjenje ili neprobušeni mjehurići zraka.', 'solution' => 'Puniti sporije, kontrolirati pritisak i svaki mjehurić odmah probosti čistom iglom.'],
            ['problem' => 'Gorka aroma dima', 'phase' => 'Dimljenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Previše dima, vlažno drvo ili premale pauze.', 'solution' => 'Koristiti suho tvrdo drvo, skratiti cikluse i produljiti pauze između dimljenja.'],
            ['problem' => 'Sluzava površina', 'phase' => 'Predsušenje / zrenje', 'severity' => 'Visok rizik', 'level' => 'danger', 'cause' => 'Previsoka vlaga bez dovoljno zraka ili loše predsušenje.', 'solution' => 'Premjestiti kobasice u prozračniji prostor, osušiti površinu i ne nastavljati dimljenje dok površina nije stabilna.'],
        ],
        'done_when' => [
            ['title' => 'Površina je stabilna', 'text' => 'Kobasica je suha na dodir, bez sluzi, bez ljepljivosti i bez mokrih mjesta oko vezova.'],
            ['title' => 'Presjek je povezan', 'text' => 'Meso i masnoća drže cjelinu, nema velikih šupljina, a sredina nije mekana ni vlažna.'],
            ['title' => 'Miris je čist', 'text' => 'Miris je paprikast, blag dimljen i mesnat, bez truležnih, kiselih ili užeglih nota.'],
            ['title' => 'Gubitak mase je postignut', 'text' => 'Ciljani gubitak za ovaj tip kobasice je približno 25–30 %, ovisno o kalibru i željenoj suhoći.'],
            ['title' => 'Kora nije pretvrda', 'text' => 'Površina ne smije biti kameno tvrda dok je sredina mekana; to je znak prebrzog sušenja.'],
            ['title' => 'Okus je zaokružen', 'text' => 'Dim, paprika, sol i masnoća trebaju biti uravnoteženi, bez gorčine i bez agresivne kiselosti.'],
        ],
        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Suha površina, čist miris, blaga bijela plemenita plijesan, ujednačena boja i postupno smanjenje mase.'],
            ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Pretvrda površina, neujednačena boja, slab protok zraka, blaga ljepljivost ili sumnja na presporo sušenje. Poboljšati uvjete i pratiti proizvod.'],
            ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Truležan, kiseo ili užegao miris, sluzava površina, napuhano crijevo, zelene/crne promjene u presjeku ili mekana sredina neugodnog mirisa.'],
        ],
        'serving' => [
            ['title' => 'Rezanje', 'text' => 'Kobasicu rezati tanko, nakon kratkog odmora na sobnoj temperaturi. Tako se aroma otvara, a presjek postaje ugodniji.'],
            ['title' => 'Uz što poslužiti', 'text' => 'Odgovara uz kruh, sir, luk, kiselo povrće, jednostavnu slavonsku zakusku i laganija crna ili svježija bijela vina.'],
            ['title' => 'Čuvanje cijelog proizvoda', 'text' => 'Čuvati na hladnom, suhom i tamnom mjestu, uz umjereno strujanje zraka. Izbjegavati zatvaranje u plastiku ako proizvod još otpušta vlagu.'],
            ['title' => 'Nakon rezanja', 'text' => 'Zamotati u papir ili prozračnu krpu i potrošiti u roku od 7–14 dana. Ako se pojavi neugodan miris ili sluzava površina, proizvod ne koristiti.'],
            ['title' => 'Pečenje', 'text' => 'Za pečenje koristiti samo svježu ili najviše 1–2 dana prosušenu kobasicu. Potpuno suha kobasica nije za pečenje.'],
            ['title' => 'Kada odbaciti', 'text' => 'Ne konzumirati ako se pojavi truležan, kiseli ili užegao miris, zelene/crne promjene u presjeku ili mekana sredina neugodnog mirisa.'],
        ],
    ];
}
