<?php
/**
 * Plugin Name: Drycured Process SEO FAQ
 * Description: Admin-only SEO and FAQ map for Drycured process pages. Public output is disabled by default.
 * Version: 0.1.7
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function drycured_process_seo_faq_v017_public_enabled(): bool {
    return ((string) get_option('drycured_process_seo_faq_public_enabled', '0')) === '1';
}

function drycured_process_seo_faq_v017_items(): array {
    return [
        'sirovina' => [
            'order' => 1,
            'title' => 'Sirovina',
            'url' => home_url('/proces-izrade/sirovina/'),
            'seo_title' => 'Sirovina za suhomesnate proizvode — odabir mesa, masnoće i svježine',
            'meta_description' => 'Praktičan vodič za odabir sirovine za suhomesnate proizvode: svježina mesa, odnos mesa i masnoće, temperatura, higijena i najčešće greške.',
            'faq' => [
                ['q' => 'Kako prepoznati dobru sirovinu za suhomesnate proizvode?', 'a' => 'Dobra sirovina mora imati čist miris, urednu boju, čvrstu strukturu i pravilno ohlađeno meso. Ako miris odstupa, sirovinu treba izdvojiti i ne koristiti dok se ne provjeri uzrok.'],
                ['q' => 'Zašto temperatura mesa prije obrade mora biti niska?', 'a' => 'Niska temperatura usporava kvarenje i sprječava razmazivanje masnoće u kasnijim fazama. Ako se meso grije, treba ga vratiti na hlađenje prije nastavka obrade.'],
                ['q' => 'Što napraviti ako meso ima sumnjiv miris?', 'a' => 'Takvo meso ne treba spašavati začinima. Treba ga odvojiti, provjeriti uvjete čuvanja i po potrebi odbaciti.'],
                ['q' => 'Kada masnoća nije prikladna za kobasice i salame?', 'a' => 'Masnoća nije prikladna ako je mekana, užegla, neugodnog mirisa ili pretopla. Za dobar presjek potrebna je čvrsta i hladna masnoća.'],
            ],
        ],
        'rezanje' => [
            'order' => 2,
            'title' => 'Rezanje',
            'url' => home_url('/proces-izrade/rezanje/'),
            'seo_title' => 'Rezanje mesa za suhomesnate proizvode — pravilni komadi i priprema',
            'meta_description' => 'Objašnjenje rezanja mesa za suhomesnate proizvode: veličina komada, uklanjanje žilica, odvajanje masnoće i priprema za soljenje ili mljevenje.',
            'faq' => [
                ['q' => 'Koliko veliki trebaju biti komadi mesa prije soljenja?', 'a' => 'Komadi trebaju biti dovoljno ujednačeni da se sol ravnomjerno rasporedi. Prevelike komade treba dodatno razrezati, a pretople komade vratiti na hlađenje.'],
                ['q' => 'Treba li ukloniti sve žilice i opne?', 'a' => 'Tvrde žilice i grube opne treba ukloniti jer kvare strukturu i presjek. Manje vezivo se uklanja prema vrsti proizvoda.'],
                ['q' => 'Kako rezanje utječe na kasnije mljevenje?', 'a' => 'Nepravilno rezani i preveliki komadi opterećuju stroj i griju smjesu. Rješenje je ujednačeno rezanje i rad s hladnim mesom.'],
                ['q' => 'Što napraviti ako se meso počne grijati tijekom rezanja?', 'a' => 'Prekinuti rad, vratiti meso na hlađenje i nastaviti tek kada se temperatura stabilizira.'],
            ],
        ],
        'soljenje' => [
            'order' => 3,
            'title' => 'Soljenje',
            'url' => home_url('/proces-izrade/soljenje/'),
            'seo_title' => 'Soljenje suhomesnatih proizvoda — sol, vrijeme i sigurnost',
            'meta_description' => 'Vodič kroz soljenje mesa za suhomesnate proizvode: količina soli, suhi i mokri postupak, vrijeme soljenja, greške i veza s Kalkulatorom soli.',
            'faq' => [
                ['q' => 'Koliko soli ide na 10 kg mesa?', 'a' => 'Količina ovisi o proizvodu i postupku. Za točan izračun treba koristiti Kalkulator soli i ne računati napamet.'],
                ['q' => 'Što napraviti ako je proizvod preslan?', 'a' => 'Kod cijelih komada može pomoći kontrolirano odsoljavanje ili kraće daljnje soljenje u idućoj šarži. Kod mljevenih smjesa ispravak je ograničen i treba bilježiti pogrešku.'],
                ['q' => 'Što napraviti ako se sumnja da je soli premalo?', 'a' => 'Ne nastavljati proces bez provjere. Treba provjeriti recept, masu mesa i korišteni kalkulator te procijeniti može li se sigurno korigirati.'],
                ['q' => 'Kada koristiti Kalkulator soli?', 'a' => 'Uvijek kada se mijenja količina mesa, tip proizvoda ili postupak soljenja. Kalkulator smanjuje rizik pogrešnog preračuna.'],
            ],
        ],
        'mljevenje' => [
            'order' => 4,
            'title' => 'Mljevenje',
            'url' => home_url('/proces-izrade/mljevenje/'),
            'seo_title' => 'Mljevenje mesa za kobasice i salame — rešetke, temperatura i mast',
            'meta_description' => 'Praktičan vodič za mljevenje mesa: izbor rešetke, kontrola temperature, sprječavanje razmazivanja masti i najčešće greške u domaćoj proizvodnji.',
            'faq' => [
                ['q' => 'Koju rešetku koristiti za kobasice?', 'a' => 'Rešetka se bira prema tipu proizvoda i željenoj granulaciji. Ako recept traži grublji presjek, ne treba ići na presitnu rešetku.'],
                ['q' => 'Zašto se mast razmazuje tijekom mljevenja?', 'a' => 'Najčešći uzrok je previsoka temperatura mesa, tupa šajba ili loš nož. Rješenje je hlađenje mesa i provjera opreme.'],
                ['q' => 'Treba li meso hladiti prije mljevenja?', 'a' => 'Da. Hladno meso i masnoća daju čistiji rez i bolju strukturu.'],
                ['q' => 'Što napraviti ako smjesa postane ljepljiva i masna?', 'a' => 'Zaustaviti mljevenje, ohladiti smjesu i provjeriti nož, rešetku i temperaturu.'],
            ],
        ],
        'mijesanje' => [
            'order' => 5,
            'title' => 'Miješanje',
            'url' => home_url('/proces-izrade/mijesanje/'),
            'seo_title' => 'Miješanje smjese za kobasice — vezivnost, začini i struktura',
            'meta_description' => 'Kako pravilno miješati smjesu za kobasice i salame: razvoj vezivnosti, ravnomjerna raspodjela začina, temperatura i greške koje kvare presjek.',
            'faq' => [
                ['q' => 'Kako znati da je smjesa dovoljno izmiješana?', 'a' => 'Smjesa treba postati povezana, ujednačena i sposobna držati strukturu. Ako se raspada, miješanje ili vezivnost nisu dovoljni.'],
                ['q' => 'Zašto smjesa postaje previše ljepljiva?', 'a' => 'Može biti predugo miješana ili pretopla. Treba kontrolirati temperaturu i ne forsirati miješanje nakon postignute vezivnosti.'],
                ['q' => 'Kada se dodaju začini?', 'a' => 'Začine treba dodati tako da se ravnomjerno rasporede, najčešće prije glavnog miješanja ili prema receptu.'],
                ['q' => 'Što napraviti ako se začini neravnomjerno rasporede?', 'a' => 'Smjesu treba pažljivo premiješati, ali bez pregrijavanja. Za iduću šaržu začine treba ravnomjernije pripremiti prije dodavanja.'],
            ],
        ],
        'odlezavanje-smjese' => [
            'order' => 6,
            'title' => 'Odležavanje smjese',
            'url' => home_url('/proces-izrade/odlezavanje-smjese/'),
            'seo_title' => 'Odležavanje smjese za kobasice — stabilizacija prije punjenja',
            'meta_description' => 'Objašnjenje odležavanja smjese prije punjenja: temperatura, trajanje, razvoj okusa, sigurnost i greške koje nastaju ako se smjesa ne stabilizira.',
            'faq' => [
                ['q' => 'Zašto smjesa treba odležati prije punjenja?', 'a' => 'Odležavanje pomaže stabilizaciji začina, soli i vezivnosti. Ako se preskoči, punjenje i struktura mogu biti lošiji.'],
                ['q' => 'Koliko dugo smjesa može odležavati?', 'a' => 'Trajanje ovisi o receptu i uvjetima, ali uvijek mora biti u hladnom i kontroliranom prostoru.'],
                ['q' => 'Na kojoj temperaturi se smjesa čuva?', 'a' => 'Smjesa se čuva hladno, bez nepotrebnog zagrijavanja. Ako temperatura raste, proces treba zaustaviti i smjesu ohladiti.'],
                ['q' => 'Što napraviti ako smjesa promijeni miris?', 'a' => 'Smjesu treba odvojiti i provjeriti. Sumnjiv miris ne treba prikrivati dimom ili začinima.'],
            ],
        ],
        'punjenje' => [
            'order' => 7,
            'title' => 'Punjenje',
            'url' => home_url('/proces-izrade/punjenje/'),
            'seo_title' => 'Punjenje kobasica i salama — crijeva, zrak i pritisak punjenja',
            'meta_description' => 'Vodič za punjenje kobasica i salama: priprema crijeva, kontrola zraka, pravilan pritisak, pucanje crijeva i najčešće greške.',
            'faq' => [
                ['q' => 'Kako pripremiti crijeva prije punjenja?', 'a' => 'Crijeva treba pravilno namočiti, isprati i držati podatnima prema vrsti crijeva. Suho ili kruto crijevo lakše puca.'],
                ['q' => 'Što napraviti ako u kobasici ostane zrak?', 'a' => 'Zrak treba izbaciti ubadanjem sterilnom iglom i pravilnim vezanjem. U idućoj šarži treba smanjiti prekide i paziti na pritisak punjenja.'],
                ['q' => 'Zašto crijevo puca tijekom punjenja?', 'a' => 'Uzrok može biti prejak pritisak, loše pripremljeno crijevo ili prečvrsta smjesa. Treba smanjiti pritisak i provjeriti pripremu crijeva.'],
                ['q' => 'Kako znati da proizvod nije prepunjen?', 'a' => 'Crijevo mora biti puno, ali ne napeto do pucanja. Proizvod mora zadržati oblik i podnijeti vezanje.'],
            ],
        ],
        'fermentacija' => [
            'order' => 8,
            'title' => 'Fermentacija',
            'url' => home_url('/proces-izrade/fermentacija/'),
            'seo_title' => 'Fermentacija suhomesnatih proizvoda — pH, temperatura i sigurnost',
            'meta_description' => 'Praktičan vodič kroz fermentaciju kobasica i salama: praćenje pH vrijednosti, temperatura, vlaga, starter kulture i rješenja za najčešće probleme.',
            'faq' => [
                ['q' => 'Zašto je pH važan u fermentaciji?', 'a' => 'pH pokazuje tijek zakiseljavanja i sigurnost procesa. Ako pH ne pada očekivano, treba provjeriti temperaturu, starter kulturu i recepturu.'],
                ['q' => 'Što napraviti ako pH pada presporo?', 'a' => 'Treba provjeriti temperaturu fermentacije, aktivnost startera, količinu šećera i uvjete vlage. Proces se ne smije samo produžiti bez kontrole.'],
                ['q' => 'Kada koristiti starter kulture?', 'a' => 'Starter kulture se koriste kada se želi kontroliranija i sigurnija fermentacija, osobito kod salama i osjetljivih proizvoda.'],
                ['q' => 'Kako prepoznati lošu fermentaciju?', 'a' => 'Znakovi mogu biti neugodan miris, nepravilan pH, ljepljiva površina ili neobična boja. Proizvod treba odvojiti i provjeriti uzrok.'],
            ],
        ],
        'dimljenje' => [
            'order' => 9,
            'title' => 'Dimljenje',
            'url' => home_url('/proces-izrade/dimljenje/'),
            'seo_title' => 'Dimljenje suhomesnatih proizvoda — hladni dim, temperatura i odmor',
            'meta_description' => 'Vodič za dimljenje suhomesnatih proizvoda: hladno dimljenje, temperatura dima, odmor između dimljenja, greške i veza s Planerom dimljenja.',
            'faq' => [
                ['q' => 'Kada dim mora biti hladan?', 'a' => 'Kod suhomesnatih proizvoda koji se suše i zriju dim mora biti kontroliran i hladan prema tipu proizvoda. Previsoka temperatura može oštetiti strukturu.'],
                ['q' => 'Zašto proizvod nakon dimljenja treba odmor?', 'a' => 'Odmor omogućuje smirivanje površine i ravnomjernije razvijanje arome dima. Bez odmora dim može biti grub i težak.'],
                ['q' => 'Što napraviti ako je dim prejak?', 'a' => 'Treba smanjiti intenzitet dima, produžiti odmor i ne nastavljati dimljenje dok se površina ne stabilizira.'],
                ['q' => 'Kada koristiti Planer dimljenja?', 'a' => 'Planer dimljenja treba koristiti kod planiranja ciklusa dima i odmora, osobito kada se radi više proizvoda ili dulji proces.'],
            ],
        ],
        'susenje' => [
            'order' => 10,
            'title' => 'Sušenje',
            'url' => home_url('/proces-izrade/susenje/'),
            'seo_title' => 'Sušenje suhomesnatih proizvoda — vlaga, masa i površinska kora',
            'meta_description' => 'Objašnjenje sušenja kobasica, salama i mesa: gubitak mase, vlaga, strujanje zraka, površinska kora, vlažna jezgra i Kalkulator sušenja.',
            'faq' => [
                ['q' => 'Kako znati da se proizvod pravilno suši?', 'a' => 'Pravilno sušenje prati se gubitkom mase, mirisom, površinom i presjekom. Sam izgled izvana nije dovoljan.'],
                ['q' => 'Što je površinska kora?', 'a' => 'Površinska kora nastaje kada se vanjski sloj osuši prebrzo, a jezgra ostane vlažna. Rješenje je usporiti sušenje i stabilizirati vlagu.'],
                ['q' => 'Što napraviti ako je proizvod tvrd izvana, a mekan iznutra?', 'a' => 'Treba smanjiti agresivno sušenje, provjeriti vlagu i strujanje zraka te pratiti masu kroz dulji period.'],
                ['q' => 'Kada koristiti Kalkulator sušenja?', 'a' => 'Kalkulator sušenja treba koristiti za praćenje gubitka mase i procjenu tempa sušenja.'],
            ],
        ],
        'zrenje' => [
            'order' => 11,
            'title' => 'Zrenje',
            'url' => home_url('/proces-izrade/zrenje/'),
            'seo_title' => 'Zrenje suhomesnatih proizvoda — aroma, tekstura i stabilnost',
            'meta_description' => 'Vodič kroz zrenje suhomesnatih proizvoda: izjednačavanje vlage, razvoj arome, površina, plijesan, tekstura i završna procjena.',
            'faq' => [
                ['q' => 'Koja je razlika između sušenja i zrenja?', 'a' => 'Sušenje primarno smanjuje vlagu, a zrenje izjednačava aromu, teksturu i stabilnost proizvoda.'],
                ['q' => 'Kako se prepoznaje dobro zrenje?', 'a' => 'Dobar proizvod ima uredan miris, stabilnu površinu, ujednačen presjek i teksturu primjerenu proizvodu.'],
                ['q' => 'Što napraviti ako se pojavi neželjena plijesan?', 'a' => 'Problematičan komad treba odvojiti, provjeriti vlagu, cirkulaciju i higijenu prostora.'],
                ['q' => 'Kada proizvod može prijeći na pakiranje?', 'a' => 'Kada su miris, presjek, tekstura i površina stabilni te nema znakova vlažne jezgre ili nepoželjne plijesni.'],
            ],
        ],
        'pakiranje' => [
            'order' => 12,
            'title' => 'Pakiranje',
            'url' => home_url('/proces-izrade/pakiranje/'),
            'seo_title' => 'Pakiranje suhomesnatih proizvoda — vakuum, omot i čuvanje',
            'meta_description' => 'Kako pravilno pakirati i čuvati suhomesnate proizvode: vakuum, papir, posude, kondenzacija, plijesan, označavanje i očuvanje arome.',
            'faq' => [
                ['q' => 'Kada je vakuum dobro rješenje?', 'a' => 'Vakuum je dobar samo za stabilan, suh i uredan proizvod. Ne smije se koristiti za prikrivanje vlažne površine ili sumnjivog mirisa.'],
                ['q' => 'Zašto se javlja kondenzacija u pakiranju?', 'a' => 'Kondenzacija nastaje zbog vlage, toplog proizvoda ili promjene temperature. Proizvod treba stabilizirati prije zatvaranja.'],
                ['q' => 'Kako čuvati rezane komade?', 'a' => 'Rezani komadi su osjetljiviji i treba ih čuvati hladno, čisto i potrošiti prije cijelih komada.'],
                ['q' => 'Što treba pisati na oznaci proizvoda?', 'a' => 'Oznaka treba sadržavati naziv proizvoda, datum, šaržu i napomenu o načinu čuvanja.'],
            ],
        ],
    ];
}

function drycured_process_seo_faq_v017_admin_menu(): void {
    add_management_page(
        'Drycured SEO FAQ',
        'Drycured SEO FAQ',
        'manage_options',
        'drycured-process-seo-faq',
        'drycured_process_seo_faq_v017_admin_page'
    );
}
add_action('admin_menu', 'drycured_process_seo_faq_v017_admin_menu');

function drycured_process_seo_faq_v017_admin_page(): void {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Nemate dopuštenje za pregled ove stranice.', 'drycured'));
    }

    $items = drycured_process_seo_faq_v017_items();
    ?>
    <div class="wrap">
        <h1>Drycured SEO FAQ</h1>

        <p>
            Ovo je administratorska mapa SEO title, meta description i FAQ prijedloga za procesne stranice.
            Javna SEO/schema primjena je isključena.
        </p>

        <div style="margin:16px 0;padding:14px 16px;border-left:4px solid #2271b1;background:#fff;">
            <strong>drycured_process_seo_faq_public_enabled:</strong>
            <?php echo drycured_process_seo_faq_v017_public_enabled() ? '1 — uključeno' : '0 — isključeno'; ?>
        </div>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th style="width:60px;">Red</th>
                    <th>Proces</th>
                    <th>SEO title</th>
                    <th>Meta description</th>
                    <th>FAQ pitanja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $slug => $item): ?>
                    <tr>
                        <td><?php echo esc_html((string) ($item['order'] ?? '')); ?></td>
                        <td>
                            <strong><?php echo esc_html((string) ($item['title'] ?? '')); ?></strong><br>
                            <code><?php echo esc_html((string) $slug); ?></code><br>
                            <a href="<?php echo esc_url((string) ($item['url'] ?? '')); ?>" target="_blank" rel="noopener">
                                otvori stranicu
                            </a>
                        </td>
                        <td>
                            <?php echo esc_html((string) ($item['seo_title'] ?? '')); ?>
                            <br><small><?php echo esc_html((string) strlen((string) ($item['seo_title'] ?? ''))); ?> znakova</small>
                        </td>
                        <td>
                            <?php echo esc_html((string) ($item['meta_description'] ?? '')); ?>
                            <br><small><?php echo esc_html((string) strlen((string) ($item['meta_description'] ?? ''))); ?> znakova</small>
                        </td>
                        <td>
                            <ol>
                                <?php foreach (($item['faq'] ?? []) as $faq): ?>
                                    <li>
                                        <strong><?php echo esc_html((string) ($faq['q'] ?? '')); ?></strong><br>
                                        <small><?php echo esc_html((string) ($faq['a'] ?? '')); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top:28px;">Sigurnosna pravila</h2>
        <ul style="list-style:disc;margin-left:22px;">
            <li>Ova verzija ne dodaje javni FAQ blok.</li>
            <li>Ova verzija ne dodaje FAQPage schema markup.</li>
            <li>Ova verzija ne mijenja title/meta podatke.</li>
            <li>Ova verzija služi samo za administratorski pregled.</li>
        </ul>
    </div>
    <?php
}
