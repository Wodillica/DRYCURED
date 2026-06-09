<?php
/**
 * Plugin Name: Drycured Batch001 DCV5 Profile Bridge
 * Description: Makes private batch_001 v2.0.2 dry_recipe posts use the existing DCV5/DCV6 recipe renderer. No DB writes.
 */

if (!defined('ABSPATH')) { exit; }

add_filter('dcv5_supported_recipe_codes', 'drycured_b001_supported_recipe_codes', 9999);
add_filter('dcv5_recipe_profile', 'drycured_b001_recipe_profile', 9999, 3);

function drycured_b001_supported_recipe_codes($codes) {
    if (!is_array($codes)) {
        $codes = [];
    }

    $ids = get_posts([
        'post_type'      => 'dry_recipe',
        'post_status'    => 'private',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => [
            [
                'key'   => 'drycured_mass_pipeline_version',
                'value' => 'v2.0.2',
            ],
            [
                'key'   => 'drycured_mass_pipeline_batch',
                'value' => 'batch_001',
            ],
        ],
    ]);

    foreach ($ids as $id) {
        $code = (string) get_post_meta($id, '_dry_recipe_id', true);
        if ($code !== '' && !in_array($code, $codes, true)) {
            $codes[] = $code;
        }
    }

    return $codes;
}

function drycured_b001_recipe_profile($profile, $post_id, $code) {
    if (!$post_id || get_post_type($post_id) !== 'dry_recipe') {
        return $profile;
    }

    if (get_post_status($post_id) !== 'private') {
        return $profile;
    }

    if (get_post_meta($post_id, 'drycured_mass_pipeline_version', true) !== 'v2.0.2') {
        return $profile;
    }

    if (get_post_meta($post_id, 'drycured_mass_pipeline_batch', true) !== 'batch_001') {
        return $profile;
    }

    if (!defined('WP_CLI') && !current_user_can('edit_post', $post_id)) {
        return $profile;
    }

    $real_code = (string) get_post_meta($post_id, '_dry_recipe_id', true);
    if ($real_code === '') {
        $real_code = $code ?: ('BATCH001-' . $post_id);
    }

    return drycured_b001_build_profile($post_id, $real_code);
}

function drycured_b001_build_profile($post_id, $code) {
    $title = get_the_title($post_id);

    $markdown = (string) get_post_meta($post_id, '_dry_recipe_full_markdown', true);
    if (trim($markdown) === '') {
        $markdown = (string) get_post_field('post_content', $post_id);
    }

    $country  = drycured_b001_terms_or_meta($post_id, 'dry_country', '_dry_country', 'Potrebna provjera');
    $region   = drycured_b001_terms_or_meta($post_id, 'dry_region', '_dry_region', 'Potrebna provjera');
    $type     = drycured_b001_terms_or_meta($post_id, 'dry_product_type', '_dry_product_type', '');
    $category = drycured_b001_terms_or_meta($post_id, 'dry_product_category', '_dry_category', '');

    if ($type === '') {
        $type = $category !== '' ? $category : 'Suhomesnati proizvod';
    }

    $parsed = drycured_b001_parse_markdown($markdown);

    $materials = $parsed['materials'];
    $spices    = $parsed['spices'];
    $liquids   = $parsed['liquids'];
    $timeline  = $parsed['timeline'];

    if (empty($materials)) {
        $materials[] = drycured_b001_item('Glavna sirovina', 'provjeriti', '', '', 'Izvorni zapis ne sadrži dovoljno strukturiran opis sirovine. Potrebna je urednička provjera.');
    }

    if (empty($spices)) {
        $spices[] = drycured_b001_item('Začini', 'provjeriti', '', '', 'Začine treba urednički potvrditi iz izvornog zapisa.');
    }

    if (empty($liquids)) {
        $liquids[] = drycured_b001_item('Tekućine / češnjak', 'nije navedeno', '', '', 'Ako se koristi češnjak ili tekućina, detalje treba dopuniti prije javne objave.');
    }

    if (empty($timeline)) {
        $timeline = drycured_b001_default_timeline();
    }

    $lead = drycured_b001_lead($title, $country, $region, $type, $markdown);

    return [
        'code'      => $code,
        'title'     => $title,
        'region'    => $region,
        'type'      => $type,
        'lead'      => $lead,

        'quick' => [
            ['label' => 'Status', 'value' => 'privatni radni recept'],
            ['label' => 'Država / regija', 'value' => trim($country . ' / ' . $region, ' /')],
            ['label' => 'Tip', 'value' => $type],
            ['label' => 'Šifra', 'value' => $code],
            ['label' => 'Objava', 'value' => 'nije dopuštena'],
        ],

        'materials' => $materials,
        'spices'    => $spices,
        'liquids'   => $liquids,

        'profile' => [
            ['name' => 'Dim', 'score' => 6],
            ['name' => 'Slanoća', 'score' => 7],
            ['name' => 'Začinjenost', 'score' => 6],
            ['name' => 'Suhoća', 'score' => 7],
            ['name' => 'Zrenje', 'score' => 6],
            ['name' => 'Tekstura', 'score' => 7],
        ],

        'climate' => [
            ['title' => 'Radni status', 'text' => 'Recept je u privatnoj uredničkoj fazi i koristi postojeći Drycured prikaz za provjeru strukture, sadržaja i tehnoloških parametara.'],
            ['title' => 'Kontrola izvora', 'text' => 'Prije javne objave potrebno je potvrditi izvor, zemlju, regiju, naziv proizvoda i tehnološki postupak.'],
            ['title' => 'Tehnološka provjera', 'text' => 'Posebno provjeriti trajanje faza, temperaturu, vlagu, dimljenje, omotač i sigurnosne napomene.'],
            ['title' => 'Urednička obrada', 'text' => 'Svi problematični ili nepotpuni dijelovi moraju imati konkretno rješenje prije objave.'],
        ],

        'timeline' => $timeline,

        'errors' => [
            [
                'problem'  => 'Nepotpuni izvorni zapis',
                'phase'    => 'Urednička provjera',
                'severity' => 'Oprez',
                'level'    => 'warning',
                'cause'    => 'Recept dolazi iz masovnog uvoza i može imati nepotpune podatke.',
                'solution' => 'Provjeriti izvor, sastojke, količine, faze procesa i sigurnosne napomene prije javne objave.',
            ],
            [
                'problem'  => 'Pogrešna zemlja ili regija',
                'phase'    => 'Klasifikacija',
                'severity' => 'Oprez',
                'level'    => 'warning',
                'cause'    => 'Automatski uvoz može pogrešno mapirati taxonomije.',
                'solution' => 'Ručno potvrditi zemlju, regiju i kategoriju proizvoda prije objave.',
            ],
        ],

        'done_when' => [
            ['title' => 'Izvor je potvrđen', 'text' => 'Naziv, zemlja, regija i tehnološki opis moraju biti provjereni prije objave.'],
            ['title' => 'Sastojci su usklađeni', 'text' => 'Količine moraju biti razumljive i prilagođene standardu receptne baze.'],
            ['title' => 'Postupak je jasan', 'text' => 'Svaka faza mora imati cilj, uvjete i kritičnu kontrolu.'],
            ['title' => 'Sigurnost je dopunjena', 'text' => 'Rizične situacije moraju imati jasnu odluku i konkretno rješenje.'],
        ],

        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — privatni pregled', 'text' => 'Recept je vidljiv samo administratoru i nije javno objavljen.'],
            ['level' => 'yellow', 'title' => 'Žuto — potrebna provjera', 'text' => 'Prije objave treba provjeriti izvor, klasifikaciju, sastojke i postupak.'],
            ['level' => 'red', 'title' => 'Crveno — ne objaviti', 'text' => 'Ne objavljivati dok recept nema potvrđen izvor i urednički pregled.'],
        ],

        'serving' => [
            ['title' => 'Posluživanje', 'text' => 'Način posluživanja treba dopuniti prema tipu proizvoda nakon uredničke provjere.'],
            ['title' => 'Čuvanje', 'text' => 'Uvjeti čuvanja moraju biti potvrđeni prema stvarnom proizvodu i stupnju osušenosti.'],
        ],
    ];
}

function drycured_b001_parse_markdown($markdown) {
    $out = [
        'materials' => [],
        'spices'    => [],
        'liquids'   => [],
        'timeline'  => [],
    ];

    $lines = preg_split('/\r\n|\r|\n/', (string) $markdown);
    $section = '';

    foreach ($lines as $line) {
        $t = trim($line);
        if ($t === '') { continue; }

        if (preg_match('/^#{1,4}\s*(.+)$/u', $t, $m)) {
            $h = mb_strtolower($m[1], 'UTF-8');
            if (strpos($h, 'sastoj') !== false) {
                $section = 'ingredients';
            } elseif (strpos($h, 'postup') !== false || strpos($h, 'priprem') !== false || strpos($h, 'tehnik') !== false) {
                $section = 'process';
            } else {
                $section = '';
            }
            continue;
        }

        if ($section === 'ingredients' && preg_match('/^-\s*(.+)$/u', $t, $m)) {
            $item = drycured_b001_parse_ingredient_line($m[1]);
            if (!$item) { continue; }

            $bucket = drycured_b001_bucket_for_item($item['name'], $item['amount']);
            $out[$bucket][] = $item;
            continue;
        }

        if ($section === 'process' && preg_match('/^-\s*(.+)$/u', $t, $m)) {
            $text = trim($m[1]);
            if ($text !== '') {
                $out['timeline'][] = [
                    'day'      => 'Faza',
                    'title'    => drycured_b001_short_title($text),
                    'text'     => $text,
                    'critical' => 'Provjeriti trajanje, temperaturu, vlagu i sigurnosnu kontrolu prije javne objave.',
                ];
            }
        }
    }

    if (count($out['timeline']) > 8) {
        $out['timeline'] = array_slice($out['timeline'], 0, 8);
    }

    return $out;
}

function drycured_b001_parse_ingredient_line($line) {
    $line = trim($line);

    if (preg_match('/^([0-9]+(?:[,.][0-9]+)?)\s*(kg|g|l|ml)\s+(.+)$/iu', $line, $m)) {
        $num  = str_replace('.', ',', $m[1]);
        $unit = mb_strtolower($m[2], 'UTF-8');
        $name = trim($m[3]);

        return drycured_b001_item($name, $num . ' ' . $unit, '', '', 'Preuzeto iz izvornog radnog zapisa; prije objave provjeriti količinu i tehnološku ulogu.');
    }

    return drycured_b001_item($line, 'provjeriti', '', '', 'Stavka nije strukturirana u standardnom obliku količina + jedinica + naziv.');
}

function drycured_b001_bucket_for_item($name, $amount) {
    $n = mb_strtolower($name . ' ' . $amount, 'UTF-8');

    if (preg_match('/\b(voda|vino|sok|tekuć|tecuc|juha|ekstrakt)\b/u', $n)) {
        return 'liquids';
    }

    if (preg_match('/\b(meso|but|pleć|plec|vrat|slanina|panceta|potrbušina|goved|svinj|jelen|koza|ovca)\b/u', $n)) {
        return 'materials';
    }

    if (preg_match('/kg/u', $n)) {
        return 'materials';
    }

    return 'spices';
}

function drycured_b001_item($name, $amount, $percent, $rate, $note) {
    return [
        'name'    => $name,
        'amount'  => $amount,
        'percent' => $percent,
        'rate'    => $rate,
        'note'    => $note,
    ];
}

function drycured_b001_default_timeline() {
    return [
        ['day' => 'Faza 1', 'title' => 'Priprema sirovine', 'text' => 'Provjeriti sirovinu, očistiti nepotrebne dijelove i pripremiti meso prema tipu proizvoda.', 'critical' => 'Sirovina mora biti hladna, čista i tehnološki ispravna.'],
        ['day' => 'Faza 2', 'title' => 'Soljenje i začinjavanje', 'text' => 'Ravnomjerno rasporediti sol i začine prema receptu.', 'critical' => 'Neravnomjerna sol stvara rizik kvarenja i neujednačen okus.'],
        ['day' => 'Faza 3', 'title' => 'Dimljenje / sušenje', 'text' => 'Voditi proizvod kroz predviđeni režim dima, zraka i vlage.', 'critical' => 'Prebrzo sušenje zatvara površinu i ostavlja vlažnu jezgru.'],
        ['day' => 'Faza 4', 'title' => 'Zrenje i čuvanje', 'text' => 'Proizvod čuvati do stabilnog presjeka, mirisa i gubitka mase.', 'critical' => 'Ne objavljivati recept bez potvrđenih završnih kriterija.'],
    ];
}

function drycured_b001_short_title($text) {
    $text = wp_strip_all_tags($text);
    $text = preg_replace('/[:.].*$/u', '', $text);
    $text = trim($text);
    if ($text === '') {
        return 'Procesna faza';
    }
    if (mb_strlen($text, 'UTF-8') > 46) {
        $text = mb_substr($text, 0, 43, 'UTF-8') . '...';
    }
    return $text;
}

function drycured_b001_lead($title, $country, $region, $type, $markdown) {
    $plain = trim(wp_strip_all_tags($markdown));
    $plain = preg_replace('/\s+/u', ' ', $plain);

    $base = $title . ' je privatni radni recept iz masovnog unosa. Prikazuje se u postojećem Drycured dizajnu radi uredničke provjere prije javne objave.';

    if ($country || $region || $type) {
        $base .= ' Klasifikacija: ' . trim($country . ', ' . $region . ', ' . $type, ' ,') . '.';
    }

    if ($plain !== '') {
        $base .= ' Izvorni zapis je učitan i treba ga završno uskladiti.';
    }

    return $base;
}

function drycured_b001_terms_or_meta($post_id, $taxonomy, $meta_key, $fallback = '') {
    if ($taxonomy) {
        $terms = wp_get_post_terms($post_id, $taxonomy, ['fields' => 'names']);
        if (!is_wp_error($terms) && !empty($terms)) {
            return implode(', ', array_map('sanitize_text_field', $terms));
        }
    }

    if ($meta_key) {
        $v = (string) get_post_meta($post_id, $meta_key, true);
        if ($v !== '') {
            return $v;
        }
    }

    return $fallback;
}
