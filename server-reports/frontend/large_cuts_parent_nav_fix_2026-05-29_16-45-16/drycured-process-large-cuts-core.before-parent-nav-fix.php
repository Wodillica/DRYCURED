<?php
/**
 * Plugin Name: Drycured Process Large Cuts Core
 * Description: Centralni sustav podstranica za obradu većih komada mesa za sušenje.
 * Version: 0.0.1
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

function dclc_parent_path(): string {
    return 'proces-izrade/obrada-vecijih-komada';
}

function dclc_base_url(): string {
    return home_url('/' . dclc_parent_path() . '/');
}

function dclc_img_base(): string {
    return home_url('/wp-content/uploads/drycured/home-process/');
}

function dclc_topics(): array {
    return [
        'but-prsut-sunka' => [
            'title' => 'But, pršut i šunka',
            'eyebrow' => 'Najveći komadi',
            'lead' => 'But je najzahtjevniji veći komad jer spaja debljinu, kost, kožu, masnoću i dugo vrijeme obrade. Kod pršuta i šunki uspjeh se ne dobiva brzinom, nego pravilnim oblikovanjem, soljenjem, odmorom i strpljivim sušenjem.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['pršut', 'suha šunka', 'dugo zrenje', 'proizvodi s kostima ili bez kosti'],
            'sections' => [
                [
                    'h' => 'Kako odabrati but',
                    'p' => 'Biraj čvrst, svjež i dobro ohlađen but stabilne boje, bez neugodnog mirisa, dubokih zareza, krvnih podljeva i ljepljive površine. Kod komada s kožom provjeri da koža nije oštećena, a kod komada s kosti da oko zgloba nema sumnjivog mirisa.',
                ],
                [
                    'h' => 'Oblikovanje prije soljenja',
                    'p' => 'Uklanjaju se poderani rubovi, viseći komadi tkiva, krvni podljevi i površine koje bi stvarale džepove vlage. Ne smije se agresivno skidati sva masnoća jer ona kod velikog komada usporava isušivanje i štiti teksturu.',
                ],
                [
                    'h' => 'Soljenje i izjednačavanje',
                    'p' => 'But traži najviše strpljenja. Sol mora imati vremena prodrijeti kroz deblje mišiće, a nakon soljenja slijedi hladno izjednačavanje kako bi se slanost smirila i rasporedila. Prekratko soljenje daje rizičnu jezgru; preoštro sušenje zatvara površinu.',
                ],
                [
                    'h' => 'Sušenje i zrenje',
                    'p' => 'Početak mora biti blag, s umjerenim strujanjem zraka. Veliki komad ne smije naglo gubiti vlagu. Kod dimljenih regionalnih varijanti dim je dodatni sloj identiteta, ali ne zamjena za pravilno soljenje i kontrolu vlage.',
                ],
            ],
            'problems' => [
                ['problem' => 'Tvrda površina, mekana jezgra', 'cause' => 'Prebrz početak sušenja ili prejako strujanje zraka.', 'solution' => 'Smanjiti ventilaciju, povisiti vlagu i produljiti fazu izjednačavanja.'],
                ['problem' => 'Sumnjiv miris uz kost', 'cause' => 'Nedovoljno hlađenje, loše soljenje ili početno kvarenje.', 'solution' => 'Komad izdvojiti; ne pokušavati ga spašavati dimom ili začinima.'],
                ['problem' => 'Preslan rub, blaga sredina', 'cause' => 'Sol ostaje na površini, a izjednačavanje je prekratko.', 'solution' => 'Planirati dulji hladni odmor nakon soljenja i sporiji ulazak u sušenje.'],
            ],
        ],

        'plecka' => [
            'title' => 'Plećka',
            'eyebrow' => 'Praktičan veći komad',
            'lead' => 'Plećka je manja i često zahvalnija od buta, ali ima više veziva, nepravilniji oblik i drukčiji raspored masnoće. Dobra je za kućne i male proizvodne uvjete jer dopušta kraći ciklus, ali traži pažljivo oblikovanje.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['suha plećka', 'manji sušeni komadi', 'dimljeni komadi', 'regionalne šunke kraćeg ciklusa'],
            'sections' => [
                [
                    'h' => 'Odabir plećke',
                    'p' => 'Plećka treba biti kompaktna, hladna i bez dubljih zareza. Zbog više veziva i neravnina, posebno treba paziti na dijelove uz lopaticu i rubove koji zadržavaju vlagu.',
                ],
                [
                    'h' => 'Čišćenje i oblikovanje',
                    'p' => 'Uklanjaju se grube opne, krvni podljevi i dijelovi koji vise. Cilj nije napraviti savršeno gladak komad, nego smanjiti mjesta na kojima se mogu zadržati sol, vlaga ili nečistoća.',
                ],
                [
                    'h' => 'Soljenje',
                    'p' => 'Plećka se soli kraće od velikog buta, ali dulje od tankih komada. Zbog nepravilnog oblika važno je ravnomjerno utrljavanje soli i dobra faza odmora.',
                ],
                [
                    'h' => 'Dimljenje i sušenje',
                    'p' => 'U kontinentalnim stilovima plećka dobro prima hladni dim. U mediteranskim pristupima može ići bez dima, ali tada početno sušenje mora biti osobito čisto i stabilno.',
                ],
            ],
            'problems' => [
                ['problem' => 'Ljepljivi rubovi', 'cause' => 'Neravnine zadržavaju vlagu.', 'solution' => 'Prije soljenja oblikovati rubove i osigurati blago strujanje zraka.'],
                ['problem' => 'Žilava tekstura', 'cause' => 'Previše veziva ili neujednačeno zrenje.', 'solution' => 'Ukloniti grube opne i planirati dulje zrenje za čvršće komade.'],
                ['problem' => 'Neujednačena slanost', 'cause' => 'Nepravilan oblik i nedovoljan odmor.', 'solution' => 'Nakon soljenja obavezno provesti hladno izjednačavanje.'],
            ],
        ],

        'vratina-coppa-capocollo' => [
            'title' => 'Vratina, coppa i capocollo',
            'eyebrow' => 'Masniji aromatični komadi',
            'lead' => 'Vratina je zahvalna za sušenje jer ima dobar omjer mesa i masnoće. Masnoća čuva sočnost i aromu, ali traži urednu higijenu, stabilnu temperaturu i zaštitu od užeglosti.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['buđola', 'coppa', 'capocollo', 'vezani sušeni komadi'],
            'sections' => [
                [
                    'h' => 'Odabir vratine',
                    'p' => 'Dobra vratina je prošarana, ali ne smije biti mekana, žućkasta ili ljepljiva. Masnoća treba biti bijela do kremasta, neutralnog mirisa i čvrsta na dodir.',
                ],
                [
                    'h' => 'Oblikovanje i vezanje',
                    'p' => 'Komad se oblikuje u pravilniji cilindar, uklanjaju se viseći rubovi i grube opne. Kod coppa/capocollo stila često se koristi vezanje kako bi komad zadržao oblik i ravnomjernije se sušio.',
                ],
                [
                    'h' => 'Sol i začini',
                    'p' => 'Vratina dobro podnosi papar, lovor, ružmarin, češnjakovu tekućinu ili regionalne mješavine. Začini ne smiju prikrivati lošu sirovinu; oni samo podižu aromu.',
                ],
                [
                    'h' => 'Sušenje',
                    'p' => 'Zbog masnoće vratina ne smije biti u pretoplom prostoru. Umjerena temperatura i dobar protok zraka sprječavaju ljepljivost i neželjene mirise.',
                ],
            ],
            'problems' => [
                ['problem' => 'Užegla masnoća', 'cause' => 'Toplina, svjetlo, kisik ili stara mast.', 'solution' => 'Koristiti svježu sirovinu, držati nižu temperaturu i zaštititi komad od previše zraka i svjetla.'],
                ['problem' => 'Ljepljiva površina', 'cause' => 'Previše vlage i premalo zraka.', 'solution' => 'Kratko pojačati ventilaciju, provjeriti temperaturu i higijenu površine.'],
                ['problem' => 'Nepravilan oblik', 'cause' => 'Nedovoljno oblikovanje ili loše vezanje.', 'solution' => 'Prije soljenja uredno oblikovati i po potrebi vezati komad.'],
            ],
        ],

        'pecenica-lonza-lungic' => [
            'title' => 'Pečenica, lonza i lungić',
            'eyebrow' => 'Mršavi dugi komadi',
            'lead' => 'Pečenica, lonza i lungić daju elegantne sušene proizvode, ali su osjetljivi jer imaju malo zaštitne masnoće. Najveći rizik nije sporo sušenje, nego presušivanje.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['suha pečenica', 'lonza', 'filetirani sušeni komadi', 'kraći ciklusi'],
            'sections' => [
                [
                    'h' => 'Odabir komada',
                    'p' => 'Biraju se čisti, uredni i kompaktni komadi bez dubokih zareza. Mršavi komadi moraju imati stabilnu boju i svjež miris jer nemaju masnoću koja bi ublažila pogreške u procesu.',
                ],
                [
                    'h' => 'Čišćenje',
                    'p' => 'Uklanjaju se srebrnaste opne i žilavi dijelovi. Površina mora ostati glatka jer svaki zarez može postati mjesto prebrzog sušenja.',
                ],
                [
                    'h' => 'Soljenje',
                    'p' => 'Soljenje je kraće nego kod buta ili plećke, ali mora biti ravnomjerno. Pretjerana sol brzo dominira jer je komad tanji i mršaviji.',
                ],
                [
                    'h' => 'Sušenje',
                    'p' => 'Početak sušenja mora biti blag, uz višu početnu relativnu vlagu i slabije strujanje zraka. Cilj je da se komad steže polako, bez tvrde kore.',
                ],
            ],
            'problems' => [
                ['problem' => 'Pretvrda i suha pečenica', 'cause' => 'Preagresivno sušenje i premalo zaštitne masnoće.', 'solution' => 'Usporiti početak sušenja i smanjiti strujanje zraka.'],
                ['problem' => 'Preslan okus', 'cause' => 'Predugo soljenje tankog komada.', 'solution' => 'Skraćivati soljenje prema debljini i provesti kratko izjednačavanje.'],
                ['problem' => 'Siva ili suha površina', 'cause' => 'Previše zraka ili starija sirovina.', 'solution' => 'Koristiti svjež komad i stabilizirati vlagu u početnoj fazi.'],
            ],
        ],

        'panceta-potrbusina' => [
            'title' => 'Panceta i potrbušina',
            'eyebrow' => 'Slojeviti komadi',
            'lead' => 'Panceta je plošnati komad u kojem se izmjenjuju meso i masnoća. Upravo ta slojevitost daje okus, ali i traži pažnju jer se mesni i masni dijelovi ne suše istom brzinom.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['panceta', 'slanina s mesom', 'dimljena potrbušina', 'rolana panceta'],
            'sections' => [
                [
                    'h' => 'Odabir pancete',
                    'p' => 'Dobra panceta ima čist miris, bijelu masnoću i jasne mesne slojeve. Pretanka panceta se brzo suši, a predebela traži sporiji i pažljiviji proces.',
                ],
                [
                    'h' => 'Oblikovanje',
                    'p' => 'Rubovi se poravnavaju, odstranjuju se krvni podljevi i nepravilni komadi. Kod rolane pancete posebno je važno spriječiti zračne džepove u unutrašnjosti.',
                ],
                [
                    'h' => 'Soljenje i začini',
                    'p' => 'Sol i začini moraju ravnomjerno pokriti i mesne i masne dijelove. Češnjakova tekućina, papar, lovor i ružmarin česti su regionalni pratitelji, ali se koriste odmjereno.',
                ],
                [
                    'h' => 'Dimljenje i sušenje',
                    'p' => 'Kontinentalne pancete često idu kroz hladni dim, dok mediteranske više ovise o zraku i soli. U oba slučaja treba izbjeći toplinu koja ubrzava užeglost masti.',
                ],
            ],
            'problems' => [
                ['problem' => 'Užegla masnoća', 'cause' => 'Toplina, starija mast ili previše kisika.', 'solution' => 'Koristiti svježu potrbušinu, držati hladniji proces i izbjegavati svjetlo.'],
                ['problem' => 'Neujednačeno sušenje', 'cause' => 'Različita debljina mesa i masti.', 'solution' => 'Poravnati rubove i omogućiti ravnomjeran protok zraka.'],
                ['problem' => 'Zračni džep u rolanoj panceti', 'cause' => 'Loše rolanje ili nedovoljno vezanje.', 'solution' => 'Čvrsto rolati, istisnuti zrak i pravilno vezati prije sušenja.'],
            ],
        ],

        'obrazina-guanciale' => [
            'title' => 'Obrazina i guanciale',
            'eyebrow' => 'Masni aromatični specijalitet',
            'lead' => 'Obrazina je manji, ali vrlo aromatičan komad bogat masnoćom i vezivom. U guanciale stilu traži čistu sirovinu, dobro začinjavanje i strpljivo, ali ne pretoplo sušenje.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['guanciale', 'sušena obrazina', 'manji masni komadi', 'regionalni specijaliteti'],
            'sections' => [
                [
                    'h' => 'Odabir obrazine',
                    'p' => 'Biraju se čisti komadi bez neugodnog mirisa, krvnih podljeva i oštećenih rubova. Masnoća mora biti čvrsta i neutralna, bez žutila i užeglosti.',
                ],
                [
                    'h' => 'Čišćenje',
                    'p' => 'Obrazina se oblikuje pažljivo jer je manja i nepravilna. Uklanjaju se žlijezde, krvni dijelovi i grube opne koje mogu stvarati neugodan miris.',
                ],
                [
                    'h' => 'Soljenje i začini',
                    'p' => 'Dobro podnosi papar, lovor, ružmarin i druge aromatične začine. Soljenje ne smije biti grubo i nasumično jer tanji dijelovi brzo primaju sol.',
                ],
                [
                    'h' => 'Sušenje',
                    'p' => 'Zbog visokog udjela masti prostor mora biti hladan, čist i bez previše svjetla. Previše topline brzo narušava aromu masnoće.',
                ],
            ],
            'problems' => [
                ['problem' => 'Težak miris masnoće', 'cause' => 'Loša sirovina, žlijezde ili toplina.', 'solution' => 'Pažljivo očistiti komad i odbaciti svaku sumnjivu sirovinu.'],
                ['problem' => 'Preslan rub', 'cause' => 'Neujednačena debljina i predugo soljenje.', 'solution' => 'Prilagoditi soljenje tanjim dijelovima i provesti izjednačavanje.'],
                ['problem' => 'Ljepljiva površina', 'cause' => 'Previše vlage i nedovoljno zraka.', 'solution' => 'Kratko stabilizirati vlagu, pojačati higijenu i provjeriti temperaturu.'],
            ],
        ],

        'govedi-komadi-bresaola' => [
            'title' => 'Goveđi komadi i bresaola',
            'eyebrow' => 'Mršavo crveno meso',
            'lead' => 'Goveđi sušeni komadi traže posebnu pažnju jer su mršavi, tamniji i imaju manje zaštitne masnoće. Bresaola tip proizvoda oslanja se na uredno oblikovanje, kontrolirano soljenje i vrlo pažljivo sušenje.',
            'image' => 'process-extra-obrada-vecijih-komada.webp',
            'best_for' => ['bresaola', 'sušena govedina', 'goveđi but', 'pastirma slični pristupi'],
            'sections' => [
                [
                    'h' => 'Odabir goveđeg komada',
                    'p' => 'Biraju se kompaktni mišići iz buta ili slični čisti komadi, bez jakog veziva i površinskih oštećenja. Boja treba biti tamnocrvena, ali miris mora ostati čist i svjež.',
                ],
                [
                    'h' => 'Oblikovanje',
                    'p' => 'Uklanjaju se opne, višak veziva i nepravilni rubovi. Mršavi komad treba što pravilniji oblik kako bi se sušio ravnomjerno.',
                ],
                [
                    'h' => 'Soljenje',
                    'p' => 'Govedina brzo pokazuje pogreške. Premalo soli povećava rizik, a previše soli lako preuzme okus. Zato je važno uredno utrljavanje i mirno izjednačavanje.',
                ],
                [
                    'h' => 'Sušenje i zrenje',
                    'p' => 'Proces mora biti sporiji i stabilan jer nema masnoće koja bi štitila površinu. Prejak zrak dovodi do tvrde kore i suhe, tamne površine.',
                ],
            ],
            'problems' => [
                ['problem' => 'Presušena površina', 'cause' => 'Govedina je mršava i brzo gubi vlagu.', 'solution' => 'Smanjiti strujanje zraka i koristiti višu početnu vlagu.'],
                ['problem' => 'Pretamna, tvrda vanjština', 'cause' => 'Prebrzo sušenje ili oksidacija površine.', 'solution' => 'Usporiti proces i zaštititi komad od previše zraka.'],
                ['problem' => 'Neujednačeno soljenje', 'cause' => 'Nepravilan oblik ili premalo izjednačavanja.', 'solution' => 'Oblikovati pravilniji komad i produljiti hladni odmor.'],
            ],
        ],
    ];
}

function dclc_current_slug(): ?string {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return null;
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $prefix = dclc_parent_path() . '/';

    if (!str_starts_with($path, $prefix)) {
        return null;
    }

    $slug = trim(substr($path, strlen($prefix)), '/');
    $topics = dclc_topics();

    return isset($topics[$slug]) ? $slug : null;
}

function dclc_render_topic(string $slug): string {
    $topics = dclc_topics();
    $topic = $topics[$slug];

    $img = dclc_img_base() . $topic['image'];

    ob_start();
    ?>
    <main class="dclc-page" aria-label="<?php echo esc_attr($topic['title']); ?>">
        <section class="dclc-hero">
            <div class="dclc-hero__copy">
                <span class="dclc-kicker"><?php echo esc_html($topic['eyebrow']); ?></span>
                <h1><?php echo esc_html($topic['title']); ?></h1>
                <p><?php echo esc_html($topic['lead']); ?></p>

                <div class="dclc-best">
                    <?php foreach ($topic['best_for'] as $item) : ?>
                        <span><?php echo esc_html($item); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <figure class="dclc-hero__image">
                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($topic['title']); ?>" loading="eager" decoding="async">
            </figure>
        </section>

        <section class="dclc-block">
            <span class="dclc-kicker">Praktični vodič</span>
            <h2>Kako pripremiti ovaj komad za daljnji proces</h2>

            <div class="dclc-section-grid">
                <?php foreach ($topic['sections'] as $section) : ?>
                    <article>
                        <h3><?php echo esc_html($section['h']); ?></h3>
                        <p><?php echo esc_html($section['p']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dclc-block dclc-flow">
            <span class="dclc-kicker">Redoslijed rada</span>
            <h2>Od sirovine do spremnog komada</h2>

            <ol>
                <li><strong>Pregledaj sirovinu:</strong> boja, miris, čvrstoća, mast, rubovi i eventualni krvni podljevi.</li>
                <li><strong>Oblikuj komad:</strong> ukloni viseće rubove, grube opne, oštećenja i mjesta koja zadržavaju vlagu.</li>
                <li><strong>Procijeni debljinu:</strong> debljina određuje soljenje, izjednačavanje i brzinu sušenja.</li>
                <li><strong>Posoli promišljeno:</strong> sol mora doći do unutrašnjosti, a ne samo ostati na površini.</li>
                <li><strong>Odmori u hladnom:</strong> izjednačavanje smanjuje rizik preslanog ruba i preslabe jezgre.</li>
                <li><strong>Uvedi u sušenje polako:</strong> početak ne smije biti agresivan jer se površina može zatvoriti.</li>
            </ol>
        </section>

        <section class="dclc-block">
            <span class="dclc-kicker">Problem → rješenje</span>
            <h2>Najčešće greške i brze korekcije</h2>

            <div class="dclc-problem-grid">
                <?php foreach ($topic['problems'] as $row) : ?>
                    <article>
                        <h3><?php echo esc_html($row['problem']); ?></h3>
                        <p><strong>Uzrok:</strong> <?php echo esc_html($row['cause']); ?></p>
                        <p><strong>Rješenje:</strong> <?php echo esc_html($row['solution']); ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="dclc-block dclc-warning">
            <span class="dclc-kicker">Sigurnosna napomena</span>
            <h2>Dim i začini ne popravljaju lošu sirovinu</h2>
            <p>
                Ako komad ima neugodan miris, duboku ljepljivost, sivu ili zelenkastu jezgru, sumnjiv iscjedak ili znakove kvarenja,
                ne pokušava se spašavati dimom, pranjem ili jačim začinima. Takav komad se izdvaja i ne koristi za konzumaciju.
            </p>
        </section>

        <section class="dclc-final">
            <div>
                <span class="dclc-kicker">Navigacija</span>
                <h2>Vrati se na pregled ili nastavi prema soljenju</h2>
                <p>Veći komadi traže miran redoslijed: prvo dobra sirovina i oblikovanje, zatim soljenje, izjednačavanje i tek onda sušenje.</p>
            </div>
            <nav class="dclc-final__buttons" aria-label="Navigacija većih komada">
                <a href="<?php echo esc_url(dclc_base_url()); ?>">← Obrada većih komada</a>
                <a href="<?php echo esc_url(home_url('/proces-izrade/')); ?>">Svi procesi</a>
                <a href="<?php echo esc_url(home_url('/proces-izrade/soljenje/')); ?>">Nastavi na Soljenje →</a>
            </nav>
        </section>
    </main>
    <?php
    return ob_get_clean();
}

function dclc_filter_content($content) {
    $slug = dclc_current_slug();

    if (!$slug || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    return dclc_render_topic($slug);
}
add_filter('the_content', 'dclc_filter_content', 60);

function dclc_assets(): void {
    $slug = dclc_current_slug();

    if (!$slug) {
        return;
    }
    ?>
    <style id="drycured-large-cuts-core-css">
        .dclc-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 24px 22px 48px;
            color: #101722;
        }

        .dclc-hero,
        .dclc-block,
        .dclc-final {
            border-radius: 32px;
            box-sizing: border-box;
        }

        .dclc-hero {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(360px, .95fr);
            gap: 34px;
            align-items: center;
            padding: 42px;
            background: linear-gradient(135deg, rgba(255,255,255,.95), rgba(248,244,235,.98));
            border: 1px solid rgba(139,111,71,.16);
            box-shadow: 0 18px 52px rgba(60,40,20,.10);
        }

        .dclc-kicker {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 16px;
            padding: 9px 15px;
            border-radius: 999px;
            background: rgba(139,111,71,.12);
            color: #8B6F47;
            font-size: 12px;
            font-weight: 850;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .dclc-hero h1,
        .dclc-block h2,
        .dclc-final h2 {
            margin: 0 0 14px;
            color: #101722;
            letter-spacing: -.035em;
            line-height: 1.06;
        }

        .dclc-hero h1 {
            font-size: clamp(38px, 5vw, 66px);
        }

        .dclc-block h2,
        .dclc-final h2 {
            font-size: clamp(28px, 3.6vw, 48px);
        }

        .dclc-hero p,
        .dclc-block p,
        .dclc-flow li,
        .dclc-final p {
            color: #4e5a68;
            font-size: 16px;
            line-height: 1.75;
        }

        .dclc-hero__image {
            margin: 0;
            height: 430px;
            border-radius: 30px;
            overflow: hidden;
            background: #101722;
            box-shadow: 0 20px 50px rgba(16,23,34,.24);
        }

        .dclc-hero__image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dclc-best {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin-top: 22px;
        }

        .dclc-best span {
            display: inline-flex;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(139,111,71,.12);
            color: #5b4328;
            font-size: 12px;
            font-weight: 800;
        }

        .dclc-block {
            margin-top: 28px;
            padding: 38px;
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(139,111,71,.14);
            box-shadow: 0 14px 36px rgba(60,40,20,.07);
        }

        .dclc-section-grid,
        .dclc-problem-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .dclc-section-grid article,
        .dclc-problem-grid article {
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(139,111,71,.14);
            box-shadow: 0 10px 26px rgba(60,40,20,.06);
        }

        .dclc-section-grid h3,
        .dclc-problem-grid h3 {
            margin: 0 0 10px;
            color: #101722;
            font-size: 20px;
        }

        .dclc-section-grid p,
        .dclc-problem-grid p {
            margin: 0;
        }

        .dclc-problem-grid p + p {
            margin-top: 8px;
        }

        .dclc-flow ol {
            margin: 24px 0 0;
            padding-left: 22px;
        }

        .dclc-flow li + li {
            margin-top: 12px;
        }

        .dclc-warning {
            background: linear-gradient(135deg, rgba(255,255,255,.95), rgba(248,244,235,.98));
        }

        .dclc-final {
            margin-top: 34px;
            padding: 44px 46px;
            background: #101722;
            color: #fff;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(260px, 320px);
            gap: 34px;
            align-items: center;
            box-shadow: 0 18px 48px rgba(16,23,34,.16);
        }

        .dclc-final h2 {
            color: #fff;
        }

        .dclc-final p {
            color: rgba(255,255,255,.82);
        }

        .dclc-final .dclc-kicker {
            background: rgba(139,111,71,.20);
            color: #caa46f;
        }

        .dclc-final__buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .dclc-final__buttons a {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 850;
            text-align: center;
            text-decoration: none !important;
        }

        .dclc-final__buttons a:nth-child(2) {
            background: #fff;
            color: #101722 !important;
        }

        .dclc-final__buttons a:not(:nth-child(2)) {
            background: rgba(255,255,255,.10);
            color: #fff !important;
            border: 1px solid rgba(255,255,255,.20);
        }

        @media (max-width: 900px) {
            .dclc-hero,
            .dclc-final {
                grid-template-columns: 1fr;
                padding: 30px 22px;
            }

            .dclc-section-grid,
            .dclc-problem-grid {
                grid-template-columns: 1fr;
            }

            .dclc-hero__image {
                height: 330px;
            }
        }

        @media (max-width: 620px) {
            .dclc-page {
                padding: 18px 14px 40px;
            }

            .dclc-block {
                padding: 28px 20px;
            }

            .dclc-hero__image {
                height: 290px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'dclc_assets', 45);

/**
 * Parent page card enhancer: adds links to child topic cards on /obrada-vecijih-komada/.
 */
function dclc_parent_enhancer(): void {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return;
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    if ($path !== dclc_parent_path()) {
        return;
    }

    $links = [
        'but, pršut i šunka' => dclc_base_url() . 'but-prsut-sunka/',
        'plećka' => dclc_base_url() . 'plecka/',
        'vratina, coppa i capocollo' => dclc_base_url() . 'vratina-coppa-capocollo/',
        'pečenica, lonza i lungić' => dclc_base_url() . 'pecenica-lonza-lungic/',
        'panceta i potrbušina' => dclc_base_url() . 'panceta-potrbusina/',
        'obrazina i guanciale' => dclc_base_url() . 'obrazina-guanciale/',
        'goveđi komadi i bresaola' => dclc_base_url() . 'govedi-komadi-bresaola/',
    ];
    ?>
    <style id="drycured-large-cuts-parent-enhancer-css">
        .dcpovk-grid article .dclc-topic-link {
            margin-top: 14px;
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 9px 14px;
            border-radius: 999px;
            background: #8B6F47;
            color: #fff !important;
            font-size: 13px;
            font-weight: 850;
            text-decoration: none !important;
        }
    </style>
    <script id="drycured-large-cuts-parent-enhancer-js">
        document.addEventListener('DOMContentLoaded', function () {
            const links = <?php echo wp_json_encode($links); ?>;
            const cards = Array.from(document.querySelectorAll('.dcpovk-grid article'));

            cards.forEach(function(card) {
                const h3 = card.querySelector('h3');
                if (!h3) return;

                const key = (h3.textContent || '').trim().toLowerCase();
                const url = links[key];

                if (!url || card.querySelector('.dclc-topic-link')) return;

                const a = document.createElement('a');
                a.href = url;
                a.className = 'dclc-topic-link';
                a.textContent = 'Otvori detaljni vodič';

                card.appendChild(a);
            });
        });
    </script>
    <?php
}
add_action('wp_footer', 'dclc_parent_enhancer', 9999);
