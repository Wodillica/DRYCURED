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
    if (is_admin() || wp_doing_ajax()) {
        return null;
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    $prefix = dclc_parent_path() . '/';

    if (!str_starts_with($path, $prefix)) {
        return null;
    }

    $slug = trim(substr($path, strlen($prefix)), '/');
    $slug = trim($slug, '/');

    if ($slug === '') {
        return null;
    }

    $topics = dclc_topics();

    return isset($topics[$slug]) ? $slug : null;
}



function dclc_prep_guides(): array {
    return [
        'but-prsut-sunka' => [
            ['title' => 'Pregled oko kosti i zgloba', 'text' => 'Provjeri miris, boju i vlažnost oko kosti, zgloba i najdebljeg dijela. Sumnjiv miris uz kost je znak za izdvajanje, ne za jače dimljenje.'],
            ['title' => 'Uklanjanje krvnih podljeva', 'text' => 'Izreži krvne podljeve i oštećene rubove jer zadržavaju vlagu i stvaraju loše početne točke za kvarenje.'],
            ['title' => 'Oblikovanje ruba', 'text' => 'Poravnaj viseće dijelove mesa i masnoće. Cilj je komad koji se suši ravnomjerno, bez džepova i tankih repova.'],
            ['title' => 'Očuvanje zaštitne masnoće', 'text' => 'Ne skidaj svu masnoću. Kod velikog komada masnoća usporava isušivanje i štiti teksturu tijekom dugog zrenja.'],
            ['title' => 'Procjena debljine', 'text' => 'Najdeblji dio određuje trajanje soljenja i odmora. Deblji but traži sporiji i strpljiviji pristup.'],
            ['title' => 'Vaganje i zapis', 'text' => 'Zapiši početnu masu prije soljenja. Kasnije će gubitak mase pokazati ide li sušenje pravilnim ritmom.'],
        ],

        'plecka' => [
            ['title' => 'Pregled nepravilnih dijelova', 'text' => 'Plećka ima više neravnina i veziva. Posebno provjeri dijelove uz lopaticu, rubove i mjesta gdje se zadržava vlaga.'],
            ['title' => 'Uklanjanje grubih opni', 'text' => 'Skini grube opne i žilave dijelove koji bi kasnije smetali rezanju i ravnomjernom sušenju.'],
            ['title' => 'Poravnavanje rubova', 'text' => 'Odstrani viseće dijelove koji bi se presušili prije ostatka komada. Površina treba biti uredna, ali ne previše ogoljena.'],
            ['title' => 'Kontrola masnoće', 'text' => 'Ostavi korisnu zaštitnu masnoću, ali ukloni mekane, poderane ili sumnjive masne rubove.'],
            ['title' => 'Procjena debljine i oblika', 'text' => 'Ako je komad jako nepravilan, soljenje i sušenje treba voditi prema najdebljem dijelu.'],
            ['title' => 'Priprema za ravnomjerno soljenje', 'text' => 'Komad prije soljenja mora biti suh na površini, hladan i bez džepova krvi ili slobodne vlage.'],
        ],

        'vratina-coppa-capocollo' => [
            ['title' => 'Pregled prošaranosti', 'text' => 'Masnoća mora biti čvrsta, svijetla i neutralnog mirisa. Žuta, mekana ili užegla mast nije dobar početak.'],
            ['title' => 'Uklanjanje opni', 'text' => 'Skini grube opne i tvrde rubove koji sprječavaju ravnomjeran dodir soli s površinom.'],
            ['title' => 'Cilindrično oblikovanje', 'text' => 'Vratinu oblikuj u što pravilniji cilindar. To pomaže jednakom sušenju i ljepšem presjeku.'],
            ['title' => 'Priprema za vezanje', 'text' => 'Ako se komad veže, prvo poravnaj rubove i namjesti oblik. Vezanje ne smije skrivati džepove ni nabore.'],
            ['title' => 'Provjera masnih džepova', 'text' => 'Veći masni džepovi mogu usporiti prodor soli. Ne uklanjaj ih sve, ali komad mora ostati kompaktan.'],
            ['title' => 'Površina spremna za sol', 'text' => 'Površina treba biti hladna, čista i lagano suha. Vlažna površina razrjeđuje sol i stvara neujednačen početak.'],
        ],

        'pecenica-lonza-lungic' => [
            ['title' => 'Odabir pravilnog komada', 'text' => 'Biraj čist, dug i kompaktan komad bez dubokih rezova. Mršavi komadi nemaju masnoću koja oprašta greške.'],
            ['title' => 'Skidanje srebrne opne', 'text' => 'Srebrnasta opna se uklanja jer otežava soljenje, sušenje i kasnije rezanje.'],
            ['title' => 'Uklanjanje tankih repova', 'text' => 'Vrlo tanki krajevi presuše prije ostatka komada. Poravnaj ih ili ih odvoji za drugu namjenu.'],
            ['title' => 'Minimalno oblikovanje', 'text' => 'Ne reži previše. Kod pečenice i lonze cilj je zadržati kompaktnost i što manje otvorenih površina.'],
            ['title' => 'Procjena najtanjeg dijela', 'text' => 'Kod mršavih komada najtanji dio često određuje rizik presušivanja. Sušenje kasnije mora početi blago.'],
            ['title' => 'Priprema za kratko soljenje', 'text' => 'Komad prije soljenja mora biti suh, hladan i pravilno oblikovan jer brzo prima sol.'],
        ],

        'panceta-potrbusina' => [
            ['title' => 'Pregled slojeva mesa i masti', 'text' => 'Provjeri jesu li mesni i masni slojevi čisti, svježi i bez neugodnog mirisa. Mast mora biti bijela do kremasta.'],
            ['title' => 'Poravnavanje plohe', 'text' => 'Panceta se mora oblikovati u pravilnu plohu. Neravni rubovi se presuše ili zadržavaju vlagu.'],
            ['title' => 'Uklanjanje krvnih točaka', 'text' => 'Krvne točke i oštećeni dijelovi uklanjaju se prije soljenja jer ubrzavaju kvarenje površine.'],
            ['title' => 'Odluka: ravna ili rolana', 'text' => 'Ako se panceta rola, već sada treba planirati kako će se istisnuti zrak i izbjeći unutarnji džepovi.'],
            ['title' => 'Kontrola debljine', 'text' => 'Deblji dijelovi trebaju više vremena za sol i sušenje. Pretanke dijelove treba zaštititi od presušivanja.'],
            ['title' => 'Suha površina prije soli', 'text' => 'Površinu obriši i stabiliziraj. Sol se mora hvatati ravnomjerno, a ne plivati u površinskoj vlazi.'],
        ],

        'obrazina-guanciale' => [
            ['title' => 'Pregled mirisa masnoće', 'text' => 'Obrazina je masna i aromatična, ali loša mast brzo kvari sve. Miris mora biti čist, bez težine i užeglosti.'],
            ['title' => 'Uklanjanje žlijezda i nečistoća', 'text' => 'Pažljivo ukloni žlijezde, krvne dijelove i tvrde nečistoće. To je najvažnija radnja prije soljenja obrazine.'],
            ['title' => 'Oblikovanje nepravilnog komada', 'text' => 'Obrazina je prirodno nepravilna. Ukloni viseće rubove, ali ne skidaj korisnu masnoću.'],
            ['title' => 'Provjera tankih dijelova', 'text' => 'Tanki dijelovi brzo primaju sol i brzo se suše. Kod soljenja treba paziti da ne postanu preslani.'],
            ['title' => 'Začinski smjer', 'text' => 'Prije soljenja odluči ide li komad prema papru, lovoru, ružmarinu ili drugoj regionalnoj aromi.'],
            ['title' => 'Spremnost za soljenje', 'text' => 'Komad mora biti hladan, suh i očišćen. Ako miris nije potpuno čist, ne ide dalje u proces.'],
        ],

        'govedi-komadi-bresaola' => [
            ['title' => 'Odabir čistog mišića', 'text' => 'Biraj kompaktan goveđi mišić bez jakog veziva, dubokih rezova i oštećenja. Boja može biti tamnija, ali miris mora biti svjež.'],
            ['title' => 'Skidanje opni i veziva', 'text' => 'Govedina traži posebno uredno čišćenje. Opne i tvrdo vezivo otežavaju soljenje i daju žilavu teksturu.'],
            ['title' => 'Oblikovanje pravilnog presjeka', 'text' => 'Komad treba imati što pravilniji oblik. Neujednačen oblik znači neujednačeno sušenje.'],
            ['title' => 'Zaštita od presušivanja', 'text' => 'Budući da nema zaštitne masnoće, već u pripremi treba izbjeći tanke rubove i previše otvorenih površina.'],
            ['title' => 'Procjena debljine', 'text' => 'Debljina određuje soljenje i kasnije sušenje. Kod govedine je bolje sporije i stabilnije nego brzo i tvrdo.'],
            ['title' => 'Zapis početne mase', 'text' => 'Početna masa je važna jer će gubitak mase kasnije biti glavni orijentir napretka sušenja.'],
        ],
    ];
}

function dclc_render_prep_guide(string $slug): string {
    $guides = dclc_prep_guides();

    if (empty($guides[$slug])) {
        return '';
    }

    ob_start();
    ?>
    <section class="dclc-block dclc-prep-guide">
        <span class="dclc-kicker">Vizualni vodič prije soljenja</span>
        <h2>Što napraviti prije nego komad dođe u sol</h2>
        <p class="dclc-prep-guide__lead">
            Ove radnje odlučuju hoće li soljenje biti ravnomjerno, a sušenje sigurno. Ako se pogreška ostavi u komadu prije soljenja,
            kasnije je dim, začin ili vrijeme neće pouzdano popraviti.
        </p>

        <div class="dclc-prep-guide__grid">
            <?php foreach ($guides[$slug] as $index => $step) : ?>
                <article>
                    <div class="dclc-prep-guide__num"><?php echo esc_html(str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT)); ?></div>
                    <div>
                        <h3><?php echo esc_html($step['title']); ?></h3>
                        <p><?php echo esc_html($step['text']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}



function dclc_hero_visual_data(string $slug): array {
    $map = [
        'but-prsut-sunka' => [
            'shape' => 'but',
            'label' => 'But / pršut / šunka',
            'focus' => ['kost i zglob', 'zaštitna masnoća', 'najdeblji dio', 'zapis mase'],
        ],
        'plecka' => [
            'shape' => 'plecka',
            'label' => 'Plećka',
            'focus' => ['neravnine', 'grube opne', 'rubovi', 'vlaga'],
        ],
        'vratina-coppa-capocollo' => [
            'shape' => 'vratina',
            'label' => 'Vratina / coppa',
            'focus' => ['mramoriranost', 'cilindrični oblik', 'vezanje', 'masni džepovi'],
        ],
        'pecenica-lonza-lungic' => [
            'shape' => 'pecenica',
            'label' => 'Pečenica / lonza',
            'focus' => ['srebrna opna', 'tanki repovi', 'presušivanje', 'kratko soljenje'],
        ],
        'panceta-potrbusina' => [
            'shape' => 'panceta',
            'label' => 'Panceta / potrbušina',
            'focus' => ['slojevi mesa i masti', 'ravna ploha', 'krvne točke', 'rolanje'],
        ],
        'obrazina-guanciale' => [
            'shape' => 'obrazina',
            'label' => 'Obrazina / guanciale',
            'focus' => ['žlijezde', 'miris masnoće', 'tanki dijelovi', 'začinski smjer'],
        ],
        'govedi-komadi-bresaola' => [
            'shape' => 'govedina',
            'label' => 'Goveđi komadi / bresaola',
            'focus' => ['opne i vezivo', 'pravilan presjek', 'zaštita od presušivanja', 'početna masa'],
        ],
    ];

    return $map[$slug] ?? [
        'shape' => 'default',
        'label' => 'Veći komad',
        'focus' => ['pregled', 'čišćenje', 'oblikovanje', 'soljenje'],
    ];
}

function dclc_render_hero_visual(string $slug): string {
    $data = dclc_hero_visual_data($slug);

    ob_start();
    ?>
    <aside class="dclc-hero__image dclc-hero__image--dark-panel" aria-label="Sažetak pripreme komada">
        <div class="dclc-dark-panel">
            <span class="dclc-dark-panel__label">Fokus pripreme</span>
            <strong><?php echo esc_html($data['label']); ?></strong>

            <div class="dclc-dark-panel__items">
                <?php foreach ($data['focus'] as $item) : ?>
                    <span><?php echo esc_html($item); ?></span>
                <?php endforeach; ?>
            </div>

            <p>
                Prije soljenja provjeri ključne točke komada: oblik, masnoću, rubove i najdeblji dio.
                Dobra priprema smanjuje rizik presušivanja, neujednačene slanosti i kvarenja.
            </p>
        </div>
    </aside>
    <?php
    return ob_get_clean();
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

            <?php echo dclc_render_hero_visual($slug); ?>
        </section>

        <?php echo dclc_render_prep_guide($slug); ?>

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

        <?php echo dclc_html_visual_extra_blocks($slug); ?>

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


        .dclc-hero__image--dark-panel {
            background:
                radial-gradient(circle at 84% 18%, rgba(202,164,111,.22), transparent 34%),
                linear-gradient(135deg, #101722, #1b2532);
            display: flex;
            align-items: stretch;
            justify-content: stretch;
        }

        .dclc-dark-panel {
            width: 100%;
            height: 100%;
            padding: 34px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #fff;
        }

        .dclc-dark-panel__label {
            display: inline-flex;
            width: fit-content;
            margin-bottom: 16px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(202,164,111,.18);
            color: #caa46f;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .dclc-dark-panel strong {
            color: #fff;
            font-size: clamp(28px, 3.5vw, 44px);
            line-height: 1.05;
            letter-spacing: -.035em;
        }

        .dclc-dark-panel__items {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 24px;
        }

        .dclc-dark-panel__items span {
            min-height: 38px;
            display: inline-flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            color: rgba(255,255,255,.88);
            font-size: 13px;
            font-weight: 800;
            line-height: 1.2;
        }

        .dclc-dark-panel p {
            margin: 24px 0 0;
            color: rgba(255,255,255,.70);
            font-size: 14px;
            line-height: 1.65;
        }


        .dclc-hero__image--diagram {
            background:
                radial-gradient(circle at 82% 16%, rgba(202,164,111,.18), transparent 34%),
                linear-gradient(135deg, #101722, #1d2734);
            display: flex;
            align-items: stretch;
            justify-content: stretch;
        }

        .dclc-cut-visual {
            width: 100%;
            height: 100%;
            padding: 26px;
            box-sizing: border-box;
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            gap: 18px;
        }

        .dclc-cut-visual__board {
            position: relative;
            min-height: 230px;
            border-radius: 26px;
            background:
                radial-gradient(circle at 50% 45%, rgba(255,255,255,.13), transparent 38%),
                rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dclc-cut-shape {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 58%;
            height: 44%;
            transform: translate(-50%, -50%);
            background:
                radial-gradient(circle at 38% 42%, rgba(255,255,255,.28), transparent 15%),
                radial-gradient(circle at 68% 58%, rgba(90,18,22,.72), transparent 22%),
                linear-gradient(135deg, #b56a55, #7c2630 46%, #d1a07a);
            border: 5px solid rgba(245,226,202,.52);
            box-shadow: 0 20px 38px rgba(0,0,0,.34);
        }

        .dclc-cut-shape--but {
            width: 64%;
            height: 48%;
            border-radius: 58% 42% 46% 54% / 50% 42% 58% 50%;
        }

        .dclc-cut-shape--but::after {
            content: "";
            position: absolute;
            right: -22%;
            top: 34%;
            width: 34%;
            height: 28%;
            border-radius: 999px;
            background: linear-gradient(135deg, #d6ad87, #8f3a32);
            border: 4px solid rgba(245,226,202,.48);
        }

        .dclc-cut-shape--plecka {
            width: 62%;
            height: 48%;
            border-radius: 38% 54% 42% 50% / 44% 42% 56% 52%;
        }

        .dclc-cut-shape--vratina {
            width: 58%;
            height: 42%;
            border-radius: 999px;
            background:
                repeating-linear-gradient(90deg, rgba(245,226,202,.42) 0 10px, transparent 10px 38px),
                linear-gradient(135deg, #8f2b32, #c98667);
        }

        .dclc-cut-shape--pecenica {
            width: 66%;
            height: 30%;
            border-radius: 999px;
            background: linear-gradient(135deg, #bd6c68, #8f2b32);
        }

        .dclc-cut-shape--panceta {
            width: 68%;
            height: 34%;
            border-radius: 18px;
            background:
                repeating-linear-gradient(0deg, #c98f75 0 18px, #f0d4b8 18px 34px, #8f2b32 34px 48px);
            border-color: rgba(245,226,202,.62);
        }

        .dclc-cut-shape--obrazina {
            width: 54%;
            height: 42%;
            border-radius: 62% 38% 54% 46% / 48% 58% 42% 52%;
            background: linear-gradient(135deg, #e4b893, #a94a3c 52%, #f0d4b8);
        }

        .dclc-cut-shape--govedina {
            width: 58%;
            height: 40%;
            border-radius: 28% 38% 32% 44% / 36% 32% 44% 40%;
            background:
                radial-gradient(circle at 35% 45%, rgba(255,255,255,.16), transparent 18%),
                linear-gradient(135deg, #6b1720, #3e0f14);
            border-color: rgba(220,190,170,.42);
        }

        .dclc-marker {
            position: absolute;
            width: 14px;
            height: 14px;
            border-radius: 999px;
            background: #caa46f;
            box-shadow: 0 0 0 8px rgba(202,164,111,.18);
        }

        .dclc-marker--a { left: 28%; top: 38%; }
        .dclc-marker--b { left: 52%; top: 55%; }
        .dclc-marker--c { right: 24%; top: 34%; }

        .dclc-cut-visual__legend {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px;
        }

        .dclc-cut-visual__legend strong {
            grid-column: 1 / -1;
            color: #fff;
            font-size: 18px;
            line-height: 1.2;
        }

        .dclc-cut-visual__legend span {
            display: inline-flex;
            align-items: center;
            min-height: 34px;
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            color: rgba(255,255,255,.86);
            font-size: 12px;
            font-weight: 780;
            line-height: 1.2;
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

        .dclc-prep-guide {
            background:
                radial-gradient(circle at 88% 12%, rgba(202,164,111,.16), transparent 30%),
                rgba(255,255,255,.94);
        }

        .dclc-prep-guide__lead {
            max-width: 860px;
            margin-bottom: 0;
        }

        .dclc-prep-guide__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 26px;
        }

        .dclc-prep-guide__grid article {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr);
            gap: 16px;
            align-items: flex-start;
            min-height: 150px;
            padding: 22px;
            border-radius: 24px;
            background: #fff;
            border: 1px solid rgba(139,111,71,.14);
            box-shadow: 0 10px 26px rgba(60,40,20,.06);
        }

        .dclc-prep-guide__num {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #101722;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .08em;
        }

        .dclc-prep-guide__grid h3 {
            margin: 0 0 8px;
            color: #101722;
            font-size: 20px;
            line-height: 1.16;
        }

        .dclc-prep-guide__grid p {
            margin: 0;
            color: #4e5a68;
            font-size: 15px;
            line-height: 1.65;
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


        .dclc-rk-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .dclc-rk-card {
            padding: 24px;
            border-radius: 26px;
            background: #fff;
            border: 1px solid rgba(139,111,71,.14);
            box-shadow: 0 10px 26px rgba(60,40,20,.06);
        }

        .dclc-rk-card h3 {
            margin: 0 0 14px;
            font-size: 22px;
            color: #101722;
        }

        .dclc-rk-card ul {
            margin: 0;
            padding-left: 20px;
        }

        .dclc-rk-card li {
            margin: 8px 0;
            color: #4e5a68;
            line-height: 1.55;
        }

        .dclc-rk-card--remove {
            border-color: rgba(145,42,42,.22);
            background: linear-gradient(135deg, #fff, rgba(145,42,42,.045));
        }

        .dclc-rk-card--keep {
            border-color: rgba(42,105,66,.24);
            background: linear-gradient(135deg, #fff, rgba(42,105,66,.045));
        }

        .dclc-risk-note {
            margin-top: 18px;
            padding: 18px 20px;
            border-radius: 22px;
            background: rgba(139,111,71,.10);
            color: #3d2b18;
            line-height: 1.65;
        }

        .dclc-risk-bars {
            display: grid;
            gap: 14px;
            margin: 24px 0 18px;
        }

        .dclc-risk-row {
            display: grid;
            grid-template-columns: 150px minmax(0, 1fr) 230px;
            gap: 14px;
            align-items: center;
        }

        .dclc-risk-row span {
            font-weight: 850;
            color: #101722;
        }

        .dclc-risk-row div {
            height: 14px;
            border-radius: 999px;
            background: rgba(139,111,71,.12);
            overflow: hidden;
        }

        .dclc-risk-row i {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #8B6F47, #101722);
        }

        .dclc-risk-row strong {
            font-size: 13px;
            color: #4e5a68;
            line-height: 1.35;
        }

        .dclc-check-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 24px;
        }

        .dclc-check-grid div {
            padding: 16px 18px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(139,111,71,.14);
            color: #101722;
            font-weight: 800;
            line-height: 1.45;
        }

        .dclc-check-grid div::before {
            content: "✓";
            display: inline-flex;
            margin-right: 8px;
            color: #2a6942;
            font-weight: 900;
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

        
        .dclc-cut-visual,
        .dclc-cut-shape,
        .dclc-marker {
            display: none !important;
        }

        @media (max-width: 900px) {
            .dclc-hero,
            .dclc-final {
                grid-template-columns: 1fr;
                padding: 30px 22px;
            }

            .dclc-section-grid,
            .dclc-problem-grid,
            .dclc-prep-guide__grid,
            .dclc-rk-grid,
            .dclc-check-grid {
                grid-template-columns: 1fr;
            }

            .dclc-risk-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .dclc-hero__image {
                height: 330px;
            }

            .dclc-dark-panel__items {
                grid-template-columns: 1fr;
            }

            .dclc-dark-panel {
                padding: 26px 22px;
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
 * Parent page card enhancer: adds reliable links to child topic cards on /obrada-vecijih-komada/.
 */
function dclc_parent_enhancer(): void {
    if (is_admin() || wp_doing_ajax() || !is_page()) {
        return;
    }

    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');

    if ($path !== dclc_parent_path()) {
        return;
    }

    $topics = [
        [
            'match' => ['but', 'pršut', 'sunka', 'šunka'],
            'url' => dclc_base_url() . 'but-prsut-sunka/',
            'label' => 'Otvori vodič za but, pršut i šunku',
        ],
        [
            'match' => ['plećka', 'plecka'],
            'url' => dclc_base_url() . 'plecka/',
            'label' => 'Otvori vodič za plećku',
        ],
        [
            'match' => ['vratina', 'coppa', 'capocollo'],
            'url' => dclc_base_url() . 'vratina-coppa-capocollo/',
            'label' => 'Otvori vodič za vratinu',
        ],
        [
            'match' => ['pečenica', 'pecenica', 'lonza', 'lungić', 'lungic'],
            'url' => dclc_base_url() . 'pecenica-lonza-lungic/',
            'label' => 'Otvori vodič za pečenicu i lonzu',
        ],
        [
            'match' => ['panceta', 'potrbušina', 'potrbusina'],
            'url' => dclc_base_url() . 'panceta-potrbusina/',
            'label' => 'Otvori vodič za pancetu',
        ],
        [
            'match' => ['obrazina', 'guanciale'],
            'url' => dclc_base_url() . 'obrazina-guanciale/',
            'label' => 'Otvori vodič za obrazinu',
        ],
        [
            'match' => ['goveđi', 'govedi', 'bresaola'],
            'url' => dclc_base_url() . 'govedi-komadi-bresaola/',
            'label' => 'Otvori vodič za goveđe komade',
        ],
    ];
    ?>
    <style id="drycured-large-cuts-parent-enhancer-css">
        .dcpovk-grid article {
            position: relative;
        }

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
            line-height: 1.15;
            text-align: center;
            text-decoration: none !important;
            box-shadow: 0 8px 18px rgba(139,111,71,.18);
        }

        .dcpovk-grid article .dclc-topic-link:hover,
        .dcpovk-grid article .dclc-topic-link:focus {
            background: #765c39;
            color: #fff !important;
            text-decoration: none !important;
        }

        .dcpovk-large-cuts-directory {
            margin-top: 28px;
            padding: 28px;
            border-radius: 28px;
            background: #101722;
            color: #fff;
            box-shadow: 0 18px 48px rgba(16,23,34,.16);
        }

        .dcpovk-large-cuts-directory h2 {
            margin: 0 0 10px;
            color: #fff;
            font-size: clamp(24px, 3vw, 38px);
            line-height: 1.08;
        }

        .dcpovk-large-cuts-directory p {
            margin: 0 0 18px;
            color: rgba(255,255,255,.78);
            line-height: 1.7;
        }

        .dcpovk-large-cuts-directory__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .dcpovk-large-cuts-directory__grid a {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.20);
            color: #fff !important;
            font-size: 13px;
            font-weight: 850;
            text-align: center;
            text-decoration: none !important;
        }

        .dcpovk-large-cuts-directory__grid a:hover,
        .dcpovk-large-cuts-directory__grid a:focus {
            background: rgba(255,255,255,.18);
        }

        @media (max-width: 720px) {
            .dcpovk-large-cuts-directory__grid,
            .dcpovk-choice-guide__grid {
                grid-template-columns: 1fr;
            }

            .dcpovk-choice-guide {
                padding: 26px 20px;
            }
        }
    </style>

    <script id="drycured-large-cuts-parent-enhancer-js">
        document.addEventListener('DOMContentLoaded', function () {
            const topics = <?php echo wp_json_encode($topics); ?>;

            function norm(value) {
                return (value || '')
                    .toLowerCase()
                    .replaceAll('č', 'c')
                    .replaceAll('ć', 'c')
                    .replaceAll('š', 's')
                    .replaceAll('đ', 'd')
                    .replaceAll('ž', 'z')
                    .trim();
            }

            const cards = Array.from(document.querySelectorAll('.dcpovk-grid article'));

            cards.forEach(function(card) {
                if (card.querySelector('.dclc-topic-link')) return;

                const title = card.querySelector('h3');
                const sourceText = norm(title ? title.textContent : card.textContent);

                const topic = topics.find(function(item) {
                    return item.match.some(function(token) {
                        return sourceText.includes(norm(token));
                    });
                });

                if (!topic) return;

                const a = document.createElement('a');
                a.href = topic.url;
                a.className = 'dclc-topic-link';
                a.textContent = 'Otvori detaljni vodič';
                a.setAttribute('aria-label', topic.label);

                card.appendChild(a);
            });

            const mainPage = document.querySelector('.dcpovk-page');
            const typesBlock = document.getElementById('vrste-komada') || document.querySelector('.dcpovk-block .dcpovk-grid')?.closest('.dcpovk-block');

            if (mainPage && typesBlock && !document.querySelector('.dcpovk-choice-guide')) {
                const choice = document.createElement('section');
                choice.className = 'dcpovk-choice-guide';
                choice.innerHTML = `
                    <span class="dcpovk-choice-guide__kicker">Brzi odabir</span>
                    <h2>Kako odabrati pravi vodič</h2>
                    <p>Ne kreće svaki veći komad istim putem. Odaberi vodič prema obliku, masnoći i namjeni komada koji stvarno imaš na stolu.</p>

                    <div class="dcpovk-choice-guide__grid">
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/but-prsut-sunka/')}">
                            <strong>Radim but, pršut ili šunku</strong>
                            <span>Velik komad, kost, koža, dugo soljenje i dugo zrenje.</span>
                        </a>
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/panceta-potrbusina/')}">
                            <strong>Radim pancetu ili potrbušinu</strong>
                            <span>Plošnati komad, slojevi mesa i masti, ravno ili rolano.</span>
                        </a>
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/pecenica-lonza-lungic/')}">
                            <strong>Radim pečenicu, lonzu ili lungić</strong>
                            <span>Mršav i dug komad, brz rizik presušivanja.</span>
                        </a>
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/vratina-coppa-capocollo/')}">
                            <strong>Radim vratinu, buđolu ili coppu</strong>
                            <span>Masniji cilindrični komad, oblikovanje i vezanje su važni.</span>
                        </a>
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/plecka/')}">
                            <strong>Radim plećku</strong>
                            <span>Nepravilniji komad, više rubova, opni i mjesta koja zadržavaju vlagu.</span>
                        </a>
                        <a href="${absoluteUrl('/proces-izrade/obrada-vecijih-komada/govedi-komadi-bresaola/')}">
                            <strong>Radim goveđi komad ili bresaola tip</strong>
                            <span>Mršavo crveno meso, bez zaštitne masnoće, traži sporiji ritam.</span>
                        </a>
                    </div>
                `;

                typesBlock.insertAdjacentElement('beforebegin', choice);
            }

            if (mainPage && typesBlock && !document.querySelector('.dcpovk-large-cuts-directory')) {
                const directory = document.createElement('section');
                directory.className = 'dcpovk-large-cuts-directory';
                directory.innerHTML = `
                    <h2>Detaljni vodiči za pojedine komade</h2>
                    <p>Svaki veći komad ima drukčiju debljinu, masnoću, površinu i ritam sušenja. Otvori vodič za komad koji pripremaš.</p>
                    <div class="dcpovk-large-cuts-directory__grid">
                        ${topics.map(function(topic) {
                            return `<a href="${topic.url}">${topic.label.replace('Otvori vodič za ', '')}</a>`;
                        }).join('')}
                    </div>
                `;

                typesBlock.insertAdjacentElement('afterend', directory);
            }
        });
    </script>
    <?php
}
add_action('wp_footer', 'dclc_parent_enhancer', 9999);

/**
 * Virtual fallback renderer.
 * Ako WordPress ne riješi child page za veće komade, ovaj sloj prikazuje sadržaj
 * iz centralne mape dclc_topics() prema URL slugu.
 */
function dclc_virtual_topic_fallback(): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }

    $slug = dclc_current_slug();

    if (!$slug) {
        return;
    }

    global $wp_query, $post;

    if ($wp_query) {
        $wp_query->is_404 = false;
        $wp_query->is_page = true;
        $wp_query->is_singular = true;
    }

    status_header(200);
    nocache_headers();

    get_header();
    echo dclc_render_topic($slug);
    get_footer();
    exit;
}
add_action('template_redirect', 'dclc_virtual_topic_fallback', 0);


/**
 * HTML visual guide extension for large cuts.
 * Adds non-photo visual guidance blocks: remove/keep, thickness risk, final checklist.
 */
function dclc_html_visual_extra_blocks(string $slug): string {
    $remove_keep = [
        'but-prsut-sunka' => [
            'remove' => ['krvne podljeve oko kosti i zgloba', 'oštećeno ili nagnječeno meso', 'meke viseće naslage masnoće', 'nečistoće, žlijezde i poderane rubove'],
            'keep' => ['čvrstu zaštitnu masnoću', 'prirodan oblik buta', 'kožu ako štiti vanjsku stranu', 'kompaktno zdravo meso'],
            'risk' => 'Najveći rizik je preslabo posoljena jezgra i prebrzo zatvaranje površine.',
        ],
        'plecka' => [
            'remove' => ['grube opne', 'viseće rubove', 'krvne podljeve', 'suvišnu mekanu masnoću'],
            'keep' => ['kompaktne mišićne dijelove', 'korisnu površinsku masnoću', 'prirodan oblik bez pretjeranog rezanja', 'ravne površine za soljenje'],
            'risk' => 'Najveći rizik je neravnomjerno soljenje zbog nepravilnog oblika.',
        ],
        'vratina-coppa-capocollo' => [
            'remove' => ['grube opne', 'tvrde rubove', 'meke masne džepove', 'krvne dijelove'],
            'keep' => ['mramoriranost', 'kompaktnost komada', 'zaštitnu masnoću', 'cilindričan oblik za vezanje'],
            'risk' => 'Najveći rizik je užeglost masnoće i loše oblikovan komad.',
        ],
        'pecenica-lonza-lungic' => [
            'remove' => ['srebrnu opnu', 'tanke repove', 'žilave dijelove', 'oštećene rubove'],
            'keep' => ['kompaktnu sredinu', 'pravilnu duljinu', 'što manje otvorenih reznih površina', 'ujednačen oblik'],
            'risk' => 'Najveći rizik je presušivanje jer komad ima malo zaštitne masnoće.',
        ],
        'panceta-potrbusina' => [
            'remove' => ['krvne točke', 'poderane rubove', 'meku staru mast', 'nepravilne tanke repove'],
            'keep' => ['jasne slojeve mesa i masti', 'ravnu plohu', 'čvrstu bijelu masnoću', 'prirodnu debljinu komada'],
            'risk' => 'Najveći rizik je neujednačeno sušenje slojeva mesa i masti.',
        ],
        'obrazina-guanciale' => [
            'remove' => ['žlijezde', 'krvne dijelove', 'nečistoće', 'meke ili sumnjive masne rubove'],
            'keep' => ['čvrstu masnoću', 'kompaktni oblik', 'korisnu debljinu', 'čistu mesnu površinu'],
            'risk' => 'Najveći rizik je neugodan miris masnoće i loše očišćene žlijezde.',
        ],
        'govedi-komadi-bresaola' => [
            'remove' => ['opne', 'tvrdo vezivo', 'nepravilne rubove', 'duboke zareze'],
            'keep' => ['čist kompaktan mišić', 'pravilni presjek', 'ujednačenu debljinu', 'glatku površinu'],
            'risk' => 'Najveći rizik je presušivanje površine jer govedina nema zaštitnu masnoću.',
        ],
    ];

    if (empty($remove_keep[$slug])) {
        return '';
    }

    $data = $remove_keep[$slug];

    ob_start();
    ?>
    <section class="dclc-block dclc-remove-keep">
        <span class="dclc-kicker">Što ukloniti, što ostaviti</span>
        <h2>Priprema komada prije soljenja</h2>

        <div class="dclc-rk-grid">
            <article class="dclc-rk-card dclc-rk-card--remove">
                <h3>Ukloniti</h3>
                <ul>
                    <?php foreach ($data['remove'] as $item) : ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>

            <article class="dclc-rk-card dclc-rk-card--keep">
                <h3>Ostaviti</h3>
                <ul>
                    <?php foreach ($data['keep'] as $item) : ?>
                        <li><?php echo esc_html($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        </div>

        <div class="dclc-risk-note">
            <strong>Glavni rizik:</strong> <?php echo esc_html($data['risk']); ?>
        </div>
    </section>

    <section class="dclc-block dclc-thickness-risk">
        <span class="dclc-kicker">Debljina komada</span>
        <h2>Što je komad deblji, proces mora biti sporiji</h2>

        <div class="dclc-risk-bars" aria-label="Rizik prema debljini komada">
            <div class="dclc-risk-row">
                <span>Tanji komad</span>
                <div><i style="width:38%"></i></div>
                <strong>brže soljenje, veći rizik presušivanja</strong>
            </div>
            <div class="dclc-risk-row">
                <span>Srednji komad</span>
                <div><i style="width:62%"></i></div>
                <strong>traži uravnotežen ritam</strong>
            </div>
            <div class="dclc-risk-row">
                <span>Debeli komad</span>
                <div><i style="width:88%"></i></div>
                <strong>sporije soljenje, veći rizik slabe jezgre</strong>
            </div>
        </div>

        <p>
            Debljina ne određuje samo koliko će komad biti u soli, nego i koliko mu treba odmora prije sušenja.
            Kod debljih komada ne smije se žuriti s jakim strujanjem zraka jer se površina može zatvoriti prije jezgre.
        </p>
    </section>

    <section class="dclc-block dclc-final-checklist">
        <span class="dclc-kicker">Kontrolni popis</span>
        <h2>Komad je spreman za soljenje kada…</h2>

        <div class="dclc-check-grid">
            <div>površina je čista i hladna</div>
            <div>uklonjeni su krvni podljevi i nečistoće</div>
            <div>rubovi su uredni i ne vise</div>
            <div>zaštitna masnoća nije nepotrebno skinuta</div>
            <div>najdeblji dio je procijenjen</div>
            <div>početna masa je zapisana</div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
