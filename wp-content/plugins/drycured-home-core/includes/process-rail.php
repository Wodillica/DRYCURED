<?php
defined('ABSPATH') || exit;

function drycured_home_core_process_rail_shortcode($atts = []) {
    drycured_home_core_enqueue_assets();

    $base = home_url('/wp-content/uploads/drycured/home-process/');

    $phases = [
        [
            'n' => '01',
            't' => 'Sirovina',
            'full' => 'Sirovina',
            'd' => 'Kvaliteta mesa određuje boju, teksturu, stabilnost i konačan okus proizvoda.',
            'r' => 'Loša sirovina ne može se spasiti začinima ni dimom.',
            's' => 'Birati svježe, pravilno ohlađeno i tehnološki prikladno meso.',
            'bg' => $base . 'process-01-sirovina.png',
            'url' => home_url('/proces-izrade/sirovina/'),
        ],
        [
            'n' => '02',
            't' => 'Soljenje',
            'full' => 'Soljenje',
            'd' => 'Sol pokreće sigurnost, vezanje vode i osnovni okus proizvoda.',
            'r' => 'Premalo soli smanjuje sigurnost, a previše soli narušava balans.',
            's' => 'Koristiti provjerene omjere i voditi zapis svake šarže.',
            'bg' => $base . 'process-02-soljenje.png',
            'url' => home_url('/proces-izrade/soljenje/'),
        ],
        [
            'n' => '03',
            't' => 'Rezanje',
            'full' => 'Rezanje',
            'd' => 'Pravilno rezanje čuva strukturu mesa i pomaže ravnomjernom presjeku.',
            'r' => 'Toplo meso i tupi noževi razmazuju mast i kvare teksturu.',
            's' => 'Raditi hladno, oštrim nožem i s jasnom dimenzijom reza.',
            'bg' => $base . 'process-03-rezanje.png',
            'url' => home_url('/proces-izrade/rezanje/'),
        ],
        [
            'n' => '04',
            't' => 'Mljevenje',
            'full' => 'Mljevenje',
            'd' => 'Granulacija mesa određuje zagriz, vezanje mase i izgled presjeka.',
            'r' => 'Pregrijano mljevenje razmazuje mast i slabi strukturu.',
            's' => 'Meso i opremu držati hladnima te koristiti odgovarajuću rešetku.',
            'bg' => $base . 'process-04-mljevenje.png',
            'url' => home_url('/proces-izrade/mljevenje/'),
        ],
        [
            'n' => '05',
            't' => 'Miješanje',
            'full' => 'Miješanje',
            'd' => 'Miješanjem se povezuju meso, mast, sol i začini u stabilnu masu.',
            'r' => 'Premalo miješanja daje rastresitu masu, a predugo miješanje je zagrijava.',
            's' => 'Miješati kontrolirano, hladno i samo dok masa ne postane povezana.',
            'bg' => $base . 'process-05-mijesanje.png',
            'url' => home_url('/proces-izrade/mijesanje/'),
        ],
        [
            'n' => '06',
            't' => 'Odležavanje',
            'full' => 'Odležavanje smjese',
            'd' => 'Smjesa kratko miruje kako bi se sol, začini i proteini ravnomjernije povezali.',
            'r' => 'Preskakanje mirovanja može dati slabije vezanje i neujednačen okus.',
            's' => 'Smjesu pokriti i držati hladno, bez nepotrebnog zagrijavanja ili isušivanja.',
            'bg' => $base . 'process-05a-odlezavanje-smjese.png',
            'url' => home_url('/proces-izrade/odlezavanje-smjese/'),
        ],
        [
            'n' => '07',
            't' => 'Punjenje',
            'full' => 'Punjenje',
            'd' => 'Punjenje oblikuje proizvod i određuje ravnomjernost presjeka.',
            'r' => 'Zračni džepovi stvaraju šupljine i moguće kritične točke kvarenja.',
            's' => 'Puniti ravnomjerno, bez prekida, i po potrebi izbosti zarobljeni zrak.',
            'bg' => $base . 'process-06-punjenje.png',
            'url' => home_url('/proces-izrade/punjenje/'),
        ],
        [
            'n' => '08',
            't' => 'Fermentacija',
            'full' => 'Fermentacija',
            'd' => 'Fermentacija razvija kiselost, aromu i mikrobiološku stabilnost kobasica.',
            'r' => 'Previsoka temperatura može dati pretjerano kiselu jezgru.',
            's' => 'Pratiti temperaturu, vlagu, vrijeme i po potrebi pH vrijednost.',
            'bg' => $base . 'process-07-fermentacija.png',
            'url' => home_url('/proces-izrade/fermentacija/'),
        ],
        [
            'n' => '09',
            't' => 'Dimljenje',
            'full' => 'Dimljenje',
            'd' => 'Dim daje boju, miris i dodatnu površinsku zaštitu proizvoda.',
            'r' => 'Prejak ili prljav dim daje gorčinu i tamnu površinu.',
            's' => 'Koristiti suh, čist i blag dim uz dobru ventilaciju.',
            'bg' => $base . 'process-08-dimljenje.png',
            'url' => home_url('/proces-izrade/dimljenje/'),
        ],
        [
            'n' => '10',
            't' => 'Sušenje',
            'full' => 'Sušenje',
            'd' => 'Sušenje postupno uklanja vodu i gradi stabilnost proizvoda.',
            'r' => 'Prebrzo sušenje stvara tvrdu koru i vlažnu jezgru.',
            's' => 'Uskladiti vlagu, temperaturu i blago strujanje zraka.',
            'bg' => $base . 'process-09-susenje.webp',
            'url' => home_url('/proces-izrade/susenje/'),
        ],
        [
            'n' => '11',
            't' => 'Zrenje',
            'full' => 'Zrenje',
            'd' => 'Zrenje razvija dubinu okusa, mirisa i teksture koju ne može zamijeniti brzina.',
            'r' => 'Nestabilna mikroklima daje neujednačen proizvod.',
            's' => 'Redovito pratiti masu, miris, površinu i opći razvoj proizvoda.',
            'bg' => $base . 'process-10-zrenje.png',
            'url' => home_url('/proces-izrade/zrenje/'),
        ],
        [
            'n' => '12',
            't' => 'Pakiranje',
            'full' => 'Pakiranje',
            'd' => 'Pakiranje čuva stabilan proizvod i zatvara ciklus proizvodnje.',
            'r' => 'Prerano zatvaranje može zarobiti vlagu i narušiti kvalitetu.',
            's' => 'Pakirati tek kada je proizvod suh, stabilan i senzorski uredan.',
            'bg' => $base . 'process-11-pakiranje.png',
            'url' => home_url('/proces-izrade/pakiranje/'),
        ],
    ];

    ob_start();
    ?>
    <section class="dc-process-rail" aria-label="Put od mesa do zrelog proizvoda">
        <div class="dc-process-rail__inner">
            <div class="dc-process-rail__top">
                <span>Interaktivni vodič procesa</span>
                <strong>Put od mesa do zrelog proizvoda</strong>
            </div>

            <div class="dc-process-rail__track">
                <?php foreach ($phases as $p): ?>
                    <article class="dc-rail-card" tabindex="0" style="--dc-card-bg:url('<?php echo esc_url($p['bg']); ?>')">
                        <a class="dc-rail-card__link" href="<?php echo esc_url($p['url']); ?>" aria-label="<?php echo esc_attr('Otvori fazu: ' . $p['full']); ?>">
                            <div class="dc-rail-card__mini">
                                <span><?php echo esc_html($p['n']); ?></span>
                                <strong><?php echo esc_html($p['t']); ?></strong>
                            </div>
                        </a>
                        <div class="dc-rail-card__panel">
                            <div class="dc-rail-card__num"><?php echo esc_html($p['n']); ?></div>
                            <h3><?php echo esc_html($p['full']); ?></h3>
                            <p><?php echo esc_html($p['d']); ?></p>
                            <div class="dc-rail-card__note">
                                <b>Rizik:</b> <?php echo esc_html($p['r']); ?><br>
                                <b>Rješenje:</b> <?php echo esc_html($p['s']); ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('drycured_home_process_rail', 'drycured_home_core_process_rail_shortcode');
