<?php
/**
 * Plugin Name: Drycured Batch01 Whole-Cut DCV5 Profile Adapter
 * Description: Clean pilot adapter for whole-cut MD recipes. Produces DCV5 profile only; no markdown HTML fallback.
 * Version: 0.2.0-pilot3
 */

if (!defined('ABSPATH')) { exit; }

function drycured_b01_wholecut_profile_pilot_ids(): array {
    return [
        3094, 3105, 3142, 3175, 3188, 3205, 3212,
        2037, 2064, 2552, 2604, 2606, 2694, 2696,
        2703, 2705, 2780, 2781, 3027, 3083, 3106,
        3135, 3206, 3208, 3216
    ];
}

function drycured_b01_wholecut_region_map(): array {
    return [
        3094 => 'Elena, Bugarska',
        3105 => 'Cipar / istočno Sredozemlje',
        3142 => 'Island',
        3175 => 'Norveška',
        3188 => 'Cipar / istočno Sredozemlje',
        3205 => 'Rumunjska',
        3212 => 'Španjolska',
        2037 => 'Island',
        2064 => 'Italija',
        2552 => 'Lika, Hrvatska',
        2604 => 'Osijek, Slavonija',
        2606 => 'Požega, Slavonija',
        2694 => 'Požega, Slavonija',
        2696 => 'Našice, Slavonija',
        2703 => 'Erdut / Podunavlje',
        2705 => 'Vukovar / Podunavlje',
        2780 => 'Slavonija',
        2781 => 'Slavonija',
        3027 => 'Engadin / Graubünden, Švicarska',
        3083 => 'Elena, Bugarska',
        3106 => 'Cipar / istočno Sredozemlje',
        3135 => 'Cipar / istočno Sredozemlje',
        3206 => 'Kavkaz / istočna Europa',
        3208 => 'Rusija / istočna Europa',
        3216 => 'Graubünden, Švicarska',
    ];
}

function drycured_b01_wholecut_v5_profile($post_id, $code = '') {
    $post_id = (int) $post_id;

    if (!in_array($post_id, drycured_b01_wholecut_profile_pilot_ids(), true)) {
        return null;
    }

    if (!function_exists('drycured_mdv5_bridge_build_profile')) {
        return null;
    }

    $profile = drycured_mdv5_bridge_build_profile($post_id, $code);
    if (!is_array($profile)) {
        return null;
    }

    return drycured_b01_wholecut_sanitize_profile($profile, $post_id, $code);
}

function drycured_b01_wholecut_sanitize_profile(array $profile, int $post_id, string $code): array {
    $title = html_entity_decode(get_the_title($post_id), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $regions = drycured_b01_wholecut_region_map();

    $profile['code']   = $code !== '' ? $code : (string) get_post_meta($post_id, '_dry_recipe_id', true);
    $profile['title']  = $title;
    $profile['type']   = 'Cijeli komad';
    $profile['region'] = $regions[$post_id] ?? 'Europa';

    $profile['lead'] = $title . ' je suhomesnati proizvod od cijelog komada mesa. Prikaz je usklađen za radnu šaržu od 10 kg osnovne sirovine, s naglaskom na suhi pac ili začinski premaz, stabilizaciju površine, sušenje, zrenje i sigurnu procjenu gotovosti.';

    $profile['quick'] = [
        ['label' => 'Šarža', 'value' => '10 kg osnovne sirovine'],
        ['label' => 'Tip', 'value' => 'cijeli komad'],
        ['label' => 'Pac / premaz', 'value' => 'suhi začinski nanos'],
        ['label' => 'Obrada', 'value' => 'soljenje, sušenje i zrenje'],
        ['label' => 'Gotovost', 'value' => 'stabilan presjek i gubitak mase'],
    ];

    $profile['materials'] = drycured_b01_wholecut_clean_items($profile['materials'] ?? [], 'materials');
    $profile['spices']    = drycured_b01_wholecut_clean_items(array_merge($profile['spices'] ?? [], $profile['liquids'] ?? []), 'spices');
    $profile['liquids']   = [];

    $profile['profile'] = [
        ['name' => 'Soljenje', 'score' => 8],
        ['name' => 'Začinski sloj', 'score' => 7],
        ['name' => 'Dim', 'score' => 5],
        ['name' => 'Suhoća', 'score' => 8],
        ['name' => 'Zrenje', 'score' => 8],
        ['name' => 'Tekstura', 'score' => 8],
    ];

    $profile['climate'] = [
        ['title' => 'Priprema komada', 'text' => 'Komad mora biti očišćen, pravilno oblikovan i dobro ohlađen prije nanošenja soli i začina.'],
        ['title' => 'Suhi pac i začinski premaz', 'text' => 'Sol, češnjak u prahu ako je naveden i ostali začini utrljavaju se po površini komada. Češnjak u prahu, ako je naveden, ostaje dio suhog začinskog premaza.'],
        ['title' => 'Stabilizacija površine', 'text' => 'Površina se mora smiriti i osušiti bez naglog stvaranja tvrde kore.'],
        ['title' => 'Dimljenje prema potvrđenom režimu', 'text' => 'Ako recept uključuje dim, koristi se hladan, tanak i čist dim u blagim ciklusima.'],
        ['title' => 'Sušenje', 'text' => 'Sušenje mora biti postupno; prejak propuh i preniska vlaga stvaraju tvrdu površinu i vlažnu jezgru.'],
        ['title' => 'Zrenje', 'text' => 'Zrenje završava tek kada su miris, tekstura, presjek i gubitak mase stabilni.'],
    ];

    $profile['timeline'] = [
        ['day' => 'Faza 1', 'title' => 'Priprema komada', 'text' => 'Komad očistiti, po potrebi oblikovati i dobro ohladiti prije soljenja.', 'critical' => 'Ne obrađivati topao ili površinski neuredan komad.'],
        ['day' => 'Faza 2', 'title' => 'Suhi pac i začinski premaz', 'text' => 'Sol i začine ravnomjerno nanijeti po cijeloj površini. Kod debljih komada fazu voditi sporije i kontrolirano.', 'critical' => 'Neravnomjerno soljenje stvara rizik kvarenja u jezgri.'],
        ['day' => 'Faza 3', 'title' => 'Okretanje i kontrola', 'text' => 'Tijekom soljenja komad redovito okretati i pratiti miris, površinu i ispuštenu tekućinu.', 'critical' => 'Sluzava površina ili neugodan miris zahtijevaju prekid i procjenu sigurnosti.'],
        ['day' => 'Faza 4', 'title' => 'Predsušenje', 'text' => 'Komad objesiti u hladan i prozračan prostor dok površina ne postane suha na dodir.', 'critical' => 'Ne izlagati jakom propuhu odmah nakon soljenja.'],
        ['day' => 'Faza 5', 'title' => 'Dimljenje prema potvrđenom režimu', 'text' => 'Ako je proizvod stvarno dimljeni tip, režim dimljenja mora biti potvrđen iz izvora prije konačne objave.', 'critical' => 'Ne koristiti neprovjeren režim dimljenja; dim ne smije prekriti miris kvarenja niti zatvoriti površinu.'],
        ['day' => 'Faza 6', 'title' => 'Sušenje', 'text' => 'Sušenje voditi postupno, prema debljini komada i stvarnom gubitku mase.', 'critical' => 'Pretvrda površina uz mekanu jezgru znak je prebrzog sušenja.'],
        ['day' => 'Faza 7', 'title' => 'Zrenje', 'text' => 'Zrenje završava tek kada su miris, presjek, površina i tekstura stabilni.', 'critical' => 'Ne rezati prerano; cijeli komadi traže vrijeme.'],
        ['day' => 'Faza 8', 'title' => 'Pakiranje i čuvanje', 'text' => 'Pakirati tek kada je proizvod stabilan i više ne otpušta površinsku vlagu.', 'critical' => 'Prerano vakuumiranje može zarobiti vlagu.'],
    ];

    $profile['errors'] = [
        ['problem' => 'Pretvrda površina, mekana sredina', 'phase' => 'Sušenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Prejak propuh, preniska vlaga ili prebrz početak sušenja.', 'solution' => 'Smanjiti protok zraka, povisiti relativnu vlagu i produljiti ravnomjerno zrenje.'],
        ['problem' => 'Neugodan ili sluzav miris', 'phase' => 'Soljenje / zrenje', 'severity' => 'Visok rizik', 'level' => 'danger', 'cause' => 'Nečistoća, previsoka temperatura ili presporo stabiliziranje površine.', 'solution' => 'Proizvod ne koristiti dok se ne procijeni sigurnost; kod sumnje odbaciti.'],
        ['problem' => 'Presjek je vlažan u jezgri', 'phase' => 'Zrenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Komad nije dovoljno dugo sušen ili je površina prerano zatvorena.', 'solution' => 'Produžiti zrenje u mirnijim uvjetima i pratiti gubitak mase.'],
        ['problem' => 'Površinska plijesan nepoznatog tipa', 'phase' => 'Zrenje', 'severity' => 'Oprez', 'level' => 'warning', 'cause' => 'Neprikladna vlaga, slaba higijena ili loš protok zraka.', 'solution' => 'Razlikovati suhu bijelu plijesan od obojenih i sluzavih promjena; sumnjive proizvode odbaciti.'],
    ];

    $profile['done_when'] = [
        ['title' => 'Miris je čist', 'text' => 'Nema truležnih, kiselih, amonijačnih ni užeglih nota.'],
        ['title' => 'Površina je stabilna', 'text' => 'Površina je suha ili plemenito zrela, bez sluzi i mokrih mjesta.'],
        ['title' => 'Presjek je siguran', 'text' => 'Nema vlažne jezgre, sivih zona ni sumnjivih promjena boje.'],
        ['title' => 'Gubitak mase je stabilan', 'text' => 'Komad je izgubio dovoljno vlage za sigurnu teksturu i rezanje.'],
    ];

    $profile['safety'] = [
        ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Čist miris, stabilna površina, postupno sušenje i ujednačen presjek.'],
        ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Pretvrda površina, sporo sušenje, blaga ljepljivost ili sumnjiva površinska promjena. Korigirati uvjete i pratiti.'],
        ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Truležan, kiseo ili užegao miris, sluzava površina, neobične obojene promjene ili vlažna jezgra neugodnog mirisa.'],
    ];

    $profile['serving'] = [
        ['title' => 'Rezanje', 'text' => 'Rezati tanko, čistim i oštrim nožem, nakon kratkog odmora na sobnoj temperaturi.'],
        ['title' => 'Posluživanje', 'text' => 'Poslužiti kao tanko rezani suhomesnati proizvod uz kruh, sir, kiselo povrće ili jednostavnu platu.'],
        ['title' => 'Čuvanje', 'text' => 'Čuvati na hladnom, suhom i tamnom mjestu, bez zatvaranja dok proizvod još otpušta vlagu.'],
        ['title' => 'Nakon rezanja', 'text' => 'Za kraće čuvanje koristiti papir ili prozračan omot; vakuumirati samo stabilan proizvod.'],
    ];

    return drycured_b01_wholecut_apply_source_safe_process($profile, $post_id);
}

function drycured_b01_wholecut_apply_source_safe_process(array $profile, int $post_id): array {
    $title = html_entity_decode(get_the_title($post_id), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $smoked = drycured_b01_wholecut_title_suggests_smoke($title);

    $profile['climate'] = [
        [
            'title' => 'Priprema komada',
            'text' => 'Komad se obrađuje kao cijela mesna cjelina. Površina mora biti čista, uredna i dovoljno hladna prije nanošenja soli i začina.',
        ],
        [
            'title' => 'Suhi pac i začinski premaz',
            'text' => 'Sol i začini iz popisa sastojaka nanose se po površini komada. Češnjak u prahu ili sušeni češnjak, ako je naveden, ostaje dio suhog premaza.',
        ],
        [
            'title' => 'Odležavanje u pacu',
            'text' => 'Izvorni MD zapis ne daje pouzdano proizvodno trajanje za ovu fazu. Fazu treba voditi prema debljini komada, količini soli i stvarnom tipu proizvoda.',
        ],
        [
            'title' => 'Površinska stabilizacija',
            'text' => 'Prije duljeg sušenja površina mora biti stabilna, bez sluzi, neugodnog mirisa i naglog zatvaranja kore.',
        ],
        [
            'title' => $smoked ? 'Dimljenje' : 'Dimljenje nije osnovna potvrđena faza',
            'text' => $smoked
                ? 'Naziv proizvoda upućuje na dimljeni tip, ali izvorni MD zapis ne daje pouzdan broj ciklusa, temperaturu ni trajanje. Te parametre treba potvrditi prije konačne javne objave recepta.'
                : 'Za ovaj zapis dimljenje se ne smije prikazivati kao obvezna faza bez potvrđenog izvora. Ako se lokalno koristi dim, režim mora biti posebno potvrđen.',
        ],
        [
            'title' => 'Sušenje',
            'text' => 'Izvor ne daje dovoljno pouzdan pojedinačni režim sušenja. Sušenje se mora voditi postupno, bez naglog isušivanja površine i uz praćenje stvarnog gubitka mase.',
        ],
        [
            'title' => 'Zrenje',
            'text' => 'Zrenje završava tek kada su miris, presjek, površina i tekstura stabilni. Ne smije se prikazivati fiksno trajanje ako nije potvrđeno za konkretan proizvod.',
        ],
    ];

    $timeline = [
        [
            'day' => 'Faza 1',
            'title' => 'Priprema cijelog komada',
            'text' => 'Komad pregledati, očistiti od neurednih rubova i površinski pripremiti za soljenje ili začinski premaz.',
            'critical' => 'Ne koristiti komad s neugodnim mirisom, sluzavom površinom ili sumnjivim promjenama boje.',
        ],
        [
            'day' => 'Faza 2',
            'title' => 'Suhi pac i začinski premaz',
            'text' => 'Sol i začine iz recepta ravnomjerno utrljati po površini. Suhi češnjak, ako postoji u sastojcima, tretira se kao začin, a ne kao tekućina.',
            'critical' => 'Neravnomjerno soljenje može ostaviti preslabo zaštićene dijelove komada.',
        ],
        [
            'day' => 'Faza 3',
            'title' => 'Odležavanje u pacu',
            'text' => 'Komad držati u kontroliranim hladnim uvjetima i redovito ga okretati. Izvorni MD zapis ne daje pouzdano trajanje ove faze.',
            'critical' => 'Ako se pojavi sluz, neugodan miris ili neuobičajena tekućina, postupak treba prekinuti i proizvod procijeniti kao rizičan.',
        ],
        [
            'day' => 'Faza 4',
            'title' => 'Predsušenje i površinska stabilizacija',
            'text' => 'Nakon paca površinu treba stabilizirati prije duljeg sušenja. Cilj je suha i mirna površina, bez zatvaranja tvrde kore.',
            'critical' => 'Prejak propuh na početku može zatvoriti površinu i ostaviti vlažnu jezgru.',
        ],
    ];

    if ($smoked) {
        $timeline[] = [
            'day' => 'Faza 5',
            'title' => 'Dimljenje prema potvrđenom režimu',
            'text' => 'Proizvod je označen kao dimljeni tip, ali izvorni MD zapis ne daje pouzdane parametre dimljenja. Broj ciklusa, temperatura dima i pauze moraju se potvrditi prije konačne objave.',
            'critical' => 'Ne prikazivati izmišljeni režim dimljenja. Dim ne smije prekriti miris kvarenja niti zatvoriti površinu komada.',
        ];
    }

    $timeline[] = [
        'day' => 'Faza ' . (count($timeline) + 1),
        'title' => 'Sušenje',
        'text' => 'Sušenje se vodi postupno, prema debljini komada, površini i stvarnom gubitku mase. Izvorni MD zapis ne daje pouzdan pojedinačni režim.',
        'critical' => 'Pretvrda površina uz mekanu sredinu znak je prebrzog sušenja.',
    ];

    $timeline[] = [
        'day' => 'Faza ' . (count($timeline) + 1),
        'title' => 'Zrenje',
        'text' => 'Zrenje traje do stabilnog mirisa, teksture i presjeka. Fiksno trajanje ne smije se navoditi bez potvrđenog izvora.',
        'critical' => 'Ne rezati i ne pakirati proizvod dok površina i jezgra nisu stabilne.',
    ];

    $timeline[] = [
        'day' => 'Faza ' . (count($timeline) + 1),
        'title' => 'Pakiranje i čuvanje',
        'text' => 'Pakirati tek kada je proizvod stabilan i više ne otpušta površinsku vlagu. Za kraće čuvanje prednost ima prozračan omot.',
        'critical' => 'Prerano zatvaranje može zarobiti vlagu i potaknuti kvarenje.',
    ];

    $profile['timeline'] = $timeline;

    return $profile;
}

function drycured_b01_wholecut_title_suggests_smoke(string $title): bool {
    return (bool) preg_match('/dimljen|dimljeni|hangikj|lountza|apohti/iu', $title);
}

function drycured_b01_wholecut_clean_items(array $items, string $group): array {
    $out = [];

    foreach ($items as $item) {
        $name = html_entity_decode((string) ($item['name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $note = html_entity_decode((string) ($item['note'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        if ($name === '') {
            continue;
        }

        // Generičke opaske tipa "češnjak se ne koristi/ne dodaje" nisu sastojak.
        if (preg_match('/češnj|cesn|garlic|knoblauch|чесън|σκόρδ/iu', $name . ' ' . $note)
            && preg_match('/ne\s+(koristi|dodaje)|nije\s+(naveden|obvezan)|ako\s+se\s+dodaje/iu', $name . ' ' . $note)) {
            continue;
        }

        if (preg_match('/crijev|omotač|ovojnic|mrežic|špag|punjenj|nadjev|mljevenj|rešetk|šajb|starter/iu', $name . ' ' . $note)) {
            continue;
        }

        $item['name'] = drycured_b01_wholecut_clean_text($name);

        if (preg_match('/češnj|cesn|garlic|knoblauch|чесън|σκόρδ/iu', $name)) {
            if (preg_match('/prah|prahu|granul|sušen|susen|dry|powder/iu', $name)) {
                $item['note'] = 'Suhi sastojak začinskog premaza. Utrljava se po površini komada zajedno sa solju i ostalim začinima.';
                $out[] = $item;
            } else {
                $item['note'] = 'Češnjak se koristi samo ako je izvorno naveden kao sastojak. Kod cijelog komada vodi se kao dio začinskog premaza ili paca.';
                $out[] = $item;
            }
            continue;
        }

        $item['note'] = drycured_b01_wholecut_clean_text($note);

        if ($group === 'spices' && preg_match('/\b(voda|vino|rakij|konjak|water|wine|ml|litra|litara)\b/iu', $name)) {
            continue;
        }

        $out[] = $item;
    }

    return drycured_b01_wholecut_dedupe($out);
}

function drycured_b01_wholecut_clean_text(string $text): string {
    $repl = [
        'nadjev' => 'površina komada',
        'Nadjev' => 'Površina komada',
        'punjenje' => 'oblikovanje',
        'Punjenje' => 'Oblikovanje',
        'crijeva' => 'vezanje',
        'Crijeva' => 'Vezanje',
        'omotač' => 'površina',
        'Omotač' => 'Površina',
        'kobasica' => 'cijeli komad',
        'Kobasica' => 'Cijeli komad',
    ];

    $text = strtr($text, $repl);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function drycured_b01_wholecut_dedupe(array $items): array {
    $seen = [];
    $out = [];

    foreach ($items as $item) {
        $key = mb_strtolower(trim(($item['name'] ?? '') . '|' . ($item['amount'] ?? '')), 'UTF-8');
        if ($key === '|' || isset($seen[$key])) {
            continue;
        }
        $seen[$key] = true;
        $out[] = $item;
    }

    return $out;
}

function drycured_b01_wholecut_is_target(): bool {
    if (!is_singular('dry_recipe')) {
        return false;
    }
    $post_id = (int) get_queried_object_id();
    return in_array($post_id, drycured_b01_wholecut_profile_pilot_ids(), true);
}

add_filter('the_content', function($content) {
    if (!drycured_b01_wholecut_is_target()) {
        return $content;
    }

    $content = preg_replace('/<section\b[^>]*id=["\']tekucine["\'][^>]*>.*?<\/section>/isu', '', $content, 1);
    $content = preg_replace('/<a\b[^>]*href=["\']#tekucine["\'][^>]*>\s*Tekućine i češnjak\s*<\/a>/iu', '', $content);

    $replacements = [
        'Omjer smjese' => 'Omjer komada i paca',
        'Brzi pregled odnosa glavnih sirovina u šarži od 10 kg. Ovaj omjer čuva sočnost, ali i omogućuje stabilno sušenje.' => 'Brzi pregled osnovnog komada i površinskog paca. Kod cijelog komada cilj je ravnomjerno soljenje i postupno sušenje bez zatvaranja površine.',
        'Meso i slanina prikazuju se u kilogramima jer čine osnovu smjese.' => 'Osnovni komad prikazuje se u kilogramima jer određuje trajanje soljenja, sušenja i zrenja.',
        'Začini i dodaci' => 'Suhi pac i začinski premaz',
        'Začini se prikazuju u gramima, uz postotak i g/kg gdje je korisno. Time korisnik dobiva i radnu vrijednost i tehnološki omjer.' => 'Sastojci suhog paca i začinskog premaza prikazuju se u gramima i g/kg. Utrljavaju se po površini komada, bez prikazivanja češnjakove tekućine ako izvor ne navodi mokri pac.',
        'Uvjeti prostora važni su koliko i začini. Dobra kobasica nastaje iz ritma hladnoće, vlage, dima i vremena.' => 'Uvjeti prostora važni su koliko i pac. Dobar cijeli komad nastaje iz ritma hladnoće, vlage, dima i vremena.',
        'Kalibar crijeva' => 'Debljina komada',
        '70 % meso' => '100 % osnovni komad',
        '30 % slanina' => 'suhi pac / premaz',
    ];

    $content = str_replace(array_keys($replacements), array_values($replacements), $content);

    $content = str_replace(
        "summary.innerHTML = '<strong>Senzorski potpis:</strong> paprikasto-dimljena kobasica srednje masnoće, blage do umjerene ljutine i čvrstog, ali ne presuhog presjeka.';",
        "summary.innerHTML = '<strong>Senzorski potpis:</strong> soljeno-zreli cijeli komad čistog mirisa, stabilne površine i čvrstog, ali ne presušenog presjeka.';",
        $content
    );

    $content = drycured_b01_wholecut_renumber_sections($content);

    return $content;
}, 99999);

function drycured_b01_wholecut_renumber_sections(string $content): string {
    $n = 0;
    return preg_replace_callback(
        '/(<section\b[^>]*class=["\'][^"\']*dcv5-panel[^"\']*["\'][^>]*>\s*<h2[^>]*>\s*<span[^>]*>)(\d+)(<\/span>)/isu',
        function($m) use (&$n) {
            $n++;
            return $m[1] . $n . $m[3];
        },
        $content
    );
}


add_action('template_redirect', function() {
    if (!drycured_b01_wholecut_is_target()) {
        return;
    }

    ob_start('drycured_b01_wholecut_final_html_cleanup');
}, -9999);

function drycured_b01_wholecut_final_html_cleanup(string $html): string {
    if ($html === '') {
        return $html;
    }

    // Ukloni cijelu generičku sekciju "Tekućine i češnjak" iz DCV5 rendera.
    $html = preg_replace(
        '/\s*<section\b[^>]*id=["\']tekucine["\'][^>]*>.*?<\/section>/isu',
        '',
        $html,
        1
    );

    // Ukloni link iz bočne navigacije.
    $html = preg_replace(
        '/<a\b[^>]*href=["\']#tekucine["\'][^>]*>\s*Tekućine i češnjak\s*<\/a>/iu',
        '',
        $html
    );

    $replacements = [
        'Tekućine i češnjak' => '',
        'Tekućine se prikazuju u litrama. Češnjak se ne dodaje kao komadić, nego kao procijeđena aromatična tekućina.' => '',
        'Češnjak se ne dodaje kao komadić, nego kao procijeđena aromatična tekućina.' => '',
        'procijeđena aromatična tekućina' => 'aromatski začinski nanos',
        'češnjakova tekućina' => 'češnjak u prahu',
        'Dobra kobasica nastaje iz ritma hladnoće, vlage, dima i vremena.' => 'Dobar cijeli komad nastaje iz ritma hladnoće, vlage, dima i vremena.',
        'paprikasto-dimljena kobasica srednje masnoće, blage do umjerene ljutine i čvrstog, ali ne presuhog presjeka' => 'soljeno-zreli cijeli komad čistog mirisa, stabilne površine i čvrstog, ali ne presušenog presjeka',
        'Kalibar crijeva' => 'Debljina komada',
        'Omjer smjese' => 'Omjer komada i paca',
        'Začini i dodaci' => 'Suhi pac i začinski premaz',
        'Brzi pregled odnosa glavnih sirovina u šarži od 10 kg. Ovaj omjer čuva sočnost, ali i omogućuje stabilno sušenje.' => 'Brzi pregled osnovnog komada i površinskog paca. Kod cijelog komada cilj je ravnomjerno soljenje i postupno sušenje bez zatvaranja površine.',
        'Meso i slanina prikazuju se u kilogramima jer čine osnovu smjese.' => 'Osnovni komad prikazuje se u kilogramima jer određuje trajanje soljenja, sušenja i zrenja.',
        'Začini se prikazuju u gramima, uz postotak i g/kg gdje je korisno. Time korisnik dobiva i radnu vrijednost i tehnološki omjer.' => 'Sastojci suhog paca i začinskog premaza prikazuju se u gramima i g/kg. Utrljavaju se po površini komada.',
        '70 % meso' => '100 % osnovni komad',
        '30 % slanina' => 'suhi pac / premaz',
    ];

    $html = str_replace(array_keys($replacements), array_values($replacements), $html);

    // Očisti eventualne dvostruke praznine nastale uklanjanjem navigacijskog linka.
    $html = preg_replace('/(<nav\b[^>]*class=["\'][^"\']*dcv5-side-panel[^"\']*["\'][^>]*>.*?)\s{2,}(.*?<\/nav>)/isu', '$1 $2', $html);

    return $html;
}


add_action('wp_footer', function() {
    if (!drycured_b01_wholecut_is_target()) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const replacements = [
            ['paprikasto-dimljena kobasica srednje masnoće, blage do umjerene ljutine i čvrstog, ali ne presuhog presjeka', 'soljeno-zreli cijeli komad čistog mirisa, stabilne površine i čvrstog, ali ne presušenog presjeka'],
            ['Dobra kobasica nastaje iz ritma hladnoće, vlage, dima i vremena.', 'Dobar cijeli komad nastaje iz ritma hladnoće, vlage, dima i vremena.'],
            ['Tekućine i češnjak', ''],
            ['Kalibar crijeva', 'Debljina komada']
        ];

        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
        const nodes = [];
        while (walker.nextNode()) nodes.push(walker.currentNode);

        nodes.forEach(function (node) {
            let text = node.nodeValue;
            replacements.forEach(function (pair) {
                text = text.split(pair[0]).join(pair[1]);
            });
            node.nodeValue = text;
        });
    });
    </script>
    <?php
}, 99999);
