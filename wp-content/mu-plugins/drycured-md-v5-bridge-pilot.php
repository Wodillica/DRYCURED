<?php
/**
 * Plugin Name: Drycured MD → V5 Bridge Pilot
 * Description: Pretvara odabrane MD-import dry_recipe zapise u V5 profil koji koristi postojeći Drycured V5 renderer.
 * Version: 0.1.6
 */

if (!defined('ABSPATH')) exit;

function drycured_mdv5_enabled() {
    return get_option('drycured_md_v5_bridge_enabled', '0') === '1';
}

function drycured_mdv5_pilot_ids() {
    $raw = get_option('drycured_md_v5_bridge_pilot_ids', '3042');
    return array_filter(array_map('absint', preg_split('/\s*,\s*/', (string)$raw)));
}

function drycured_mdv5_get_markdown($post_id) {
    $md = get_post_meta($post_id, '_dry_recipe_full_markdown', true);

    if (!$md) {
        $sections = get_post_meta($post_id, '_dry_recipe_sections', true);
        $decoded = json_decode((string)$sections, true);
        if (is_array($decoded) && !empty($decoded['full_markdown'])) {
            $md = $decoded['full_markdown'];
        }
    }

    if (!$md) {
        $raw = get_post_meta($post_id, 'drycured_input_raw_json', true);
        $decoded = json_decode((string)$raw, true);
        if (is_array($decoded) && !empty($decoded['full_markdown'])) {
            $md = $decoded['full_markdown'];
        }
    }

    if (!$md) {
        $p = get_post($post_id);
        $md = $p ? $p->post_content : '';
    }

    return (string)$md;
}

function drycured_mdv5_plain($text) {
    $text = wp_strip_all_tags((string)$text);
    $text = preg_replace('/[#*_`]+/', '', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

function drycured_mdv5_section($markdown, $wanted) {
    $markdown = str_replace(["\r\n", "\r"], "\n", (string)$markdown);
    $lines = explode("\n", $markdown);

    $capture = false;
    $out = [];

    foreach ($lines as $line) {
        $trim = trim($line);

        if (preg_match('/^#{1,4}\s+(.+)$/u', $trim, $m)) {
            $heading = mb_strtolower(drycured_mdv5_plain($m[1]));
            $wanted_l = mb_strtolower($wanted);

            if ($capture) break;
            if (mb_stripos($heading, $wanted_l) !== false) {
                $capture = true;
            }
            continue;
        }

        if ($capture) $out[] = $line;
    }

    return trim(implode("\n", $out));
}

function drycured_mdv5_lines($text) {
    $text = str_replace(["\r\n", "\r"], "\n", (string)$text);
    $out = [];

    foreach (explode("\n", $text) as $line) {
        $line = trim($line);
        $line = preg_replace('/^[-*]\s+/u', '', $line);
        $line = preg_replace('/^\d+\.\s+/u', '', $line);
        $line = drycured_mdv5_plain($line);
        if ($line !== '') $out[] = $line;
    }

    return $out;
}

function drycured_mdv5_parse_amount_line($line) {
    $line = drycured_mdv5_plain($line);

    if (!preg_match('/^([0-9]+(?:[,.][0-9]+)?)\s*(kg|g|ml|l|L)\s+(.+)$/u', $line, $m)) {
        return [
            'raw' => $line,
            'value' => null,
            'unit' => '',
            'name' => $line,
        ];
    }

    return [
        'raw' => $line,
        'value' => (float)str_replace(',', '.', $m[1]),
        'unit' => strtolower($m[2]) === 'l' ? 'l' : strtolower($m[2]),
        'name' => trim($m[3]),
    ];
}

function drycured_mdv5_to_grams($value, $unit) {
    if ($value === null) return null;
    if ($unit === 'kg') return $value * 1000.0;
    if ($unit === 'g') return $value;
    return null;
}

function drycured_mdv5_to_ml($value, $unit) {
    if ($value === null) return null;
    if ($unit === 'l') return $value * 1000.0;
    if ($unit === 'ml') return $value;
    return null;
}

function drycured_mdv5_fmt_num($n, $dec = 0) {
    $n = (float)$n;
    if ($dec === 0) {
        return number_format(round($n), 0, ',', '.');
    }
    return rtrim(rtrim(number_format($n, $dec, ',', '.'), '0'), ',');
}

function drycured_mdv5_fmt_kg($grams) {
    return drycured_mdv5_fmt_num($grams / 1000.0, 3) . ' kg';
}

function drycured_mdv5_fmt_g($grams) {
    if ($grams >= 1000) {
        return drycured_mdv5_fmt_kg($grams);
    }
    return drycured_mdv5_fmt_num($grams, 0) . ' g';
}

function drycured_mdv5_fmt_l($ml) {
    if ($ml >= 1000) {
        return drycured_mdv5_fmt_num($ml / 1000.0, 3) . ' L';
    }
    return drycured_mdv5_fmt_num($ml, 0) . ' ml';
}

function drycured_mdv5_is_material_name($name) {
    $l = mb_strtolower($name);
    return (bool)preg_match('/svinj|lopatic|pleć|potrbu|slanina|masnoć|leđn|salo|but|vrat|plecka|plećka/u', $l);
}

function drycured_mdv5_is_liquid_name($name, $unit) {
    $l = mb_strtolower($name);
    if ($unit === 'ml' || $unit === 'l') return true;
    return (bool)preg_match('/voda|vino|konjak|rakij|tekuć/u', $l);
}

function drycured_mdv5_is_starter_name($name) {
    return (bool)preg_match('/starter|kultura|bactoferm|ferment/u', mb_strtolower($name));
}


function drycured_mdv5_is_generic_material_name($name) {
    $l = trim(mb_strtolower($name));
    $l = preg_replace('/\s+/', ' ', $l);

    return in_array($l, [
        'svinjetina',
        'meso',
        'mesna sirovina',
        'svinjsko meso',
    ], true);
}

function drycured_mdv5_ingredients($markdown) {
    $section = drycured_mdv5_section($markdown, 'sastojci');
    $lines = drycured_mdv5_lines($section);

    $parsed = [];

    foreach ($lines as $line) {
        $row = drycured_mdv5_parse_amount_line($line);
        $row['cat'] = 'spice';
        $row['note_extra'] = '';

        /*
         * Redoslijed je bitan:
         * - voda, vino, konjak i slične tekućine idu u liquids čak i kad se spominje starter
         * - starter kultura ide u spices kao tehnološki dodatak
         * - meso, potrbušina i slanina idu u materials
         */
        if (drycured_mdv5_is_liquid_name($row['name'], $row['unit'])) {
            $row['cat'] = 'liquid';
            if (preg_match('/starter/u', mb_strtolower($row['name']))) {
                $row['note_extra'] = 'Tekućina za razrjeđivanje ili aktivaciju starter kulture. Preračunato na 10 kg mesne sirovine.';
            }
        } elseif (drycured_mdv5_is_starter_name($row['name'])) {
            $row['cat'] = 'spice';
            $row['note_extra'] = 'Tehnološki dodatak / starter kultura. Doziranje je preračunato iz izvornog MD zapisa na 10 kg mesne sirovine.';
        } elseif (drycured_mdv5_is_material_name($row['name'])) {
            $row['cat'] = 'material';
        } else {
            $row['cat'] = 'spice';
        }

        $parsed[] = $row;
    }

    /*
     * Ako izvor ima generičku stavku "Svinjetina" i uz nju detaljne stavke
     * poput lopatice, potrbušine i leđne slanine, generičku stavku izbacujemo.
     * Inače bi se 10 kg svinjetine zbrajalo s detaljnim gramima i uništilo skaliranje.
     */
    $specific_material_count = 0;
    foreach ($parsed as $row) {
        if ($row['cat'] === 'material' && !drycured_mdv5_is_generic_material_name($row['name'])) {
            $specific_material_count++;
        }
    }

    if ($specific_material_count > 0) {
        $parsed = array_values(array_filter($parsed, function($row) {
            if (($row['cat'] ?? '') !== 'material') return true;
            return !drycured_mdv5_is_generic_material_name($row['name']);
        }));
    }

    $source_material_g = 0.0;
    foreach ($parsed as $row) {
        if ($row['cat'] === 'material') {
            $g = drycured_mdv5_to_grams($row['value'], $row['unit']);
            if ($g !== null) $source_material_g += $g;
        }
    }

    $target_material_g = 10000.0;
    $factor = ($source_material_g > 0) ? ($target_material_g / $source_material_g) : 1.0;

    $materials = [];
    $spices = [];
    $liquids = [];

    foreach ($parsed as $row) {
        $name = $row['name'];
        $unit = $row['unit'];
        $value = $row['value'];
        $cat = $row['cat'];

        if ($cat === 'material') {
            $g = drycured_mdv5_to_grams($value, $unit);
            if ($g === null) continue;

            $scaled_g = $g * $factor;
            $pct = $scaled_g / $target_material_g * 100.0;

            $material_name = ucfirst($name);
            $material_note = 'Preračunato na 10 kg mesne sirovine iz izvornog MD omjera.';

            if (preg_match('/potrbu.*kož/ui', $material_name)) {
                $material_name = 'Svinjska potrbušina bez kože';
                $material_note = 'Za proizvodni prikaz koristi se potrbušina bez kože. Kožu obavezno ukloniti prije mljevenja; u nadjev ulazi samo mesno-masni dio.';
            }

            $materials[] = [
                'name' => $material_name,
                'amount' => drycured_mdv5_fmt_kg($scaled_g),
                'percent' => drycured_mdv5_fmt_num($pct, 1) . ' %',
                'rate' => '',
                'note' => $material_note,
            ];
            continue;
        }

        if ($cat === 'liquid') {
            $ml = drycured_mdv5_to_ml($value, $unit);
            if ($ml === null) continue;

            $scaled_ml = $ml * $factor;

            $liquids[] = [
                'name' => ucfirst($name),
                'amount' => drycured_mdv5_fmt_l($scaled_ml),
                'percent' => '',
                'rate' => drycured_mdv5_fmt_num($scaled_ml / 10.0, 1) . ' ml/kg',
                'note' => $row['note_extra'] ?: 'Preračunato na 10 kg mesne sirovine iz izvornog MD zapisa.',
            ];
            continue;
        }

        $g = drycured_mdv5_to_grams($value, $unit);
        $amount = '';
        $rate = '';
        $percent = '';

        if ($g !== null) {
            $scaled_g = $g * $factor;
            $amount = drycured_mdv5_fmt_g($scaled_g);
            $rate = drycured_mdv5_fmt_num($scaled_g / 10.0, 1) . ' g/kg';
            $percent = drycured_mdv5_fmt_num($scaled_g / 10000.0 * 100.0, 2) . ' %';
        }

        $spices[] = [
            'name' => ucfirst($name),
            'amount' => $amount,
            'percent' => $percent,
            'rate' => $rate,
            'note' => $row['note_extra'] ?: 'Preračunato na 10 kg mesne sirovine iz izvornog MD zapisa.',
        ];
    }

    if (!$materials) {
        $materials[] = [
            'name' => 'Mesna sirovina',
            'amount' => '10 kg',
            'percent' => '100 %',
            'rate' => '',
            'note' => 'Izvor ne daje dovoljno razdvojenu mesnu strukturu.',
        ];
    }

    return [$materials, $spices, $liquids, $factor, $source_material_g];
}


function drycured_mdv5_timeline($markdown) {
    $section = drycured_mdv5_section($markdown, 'postupak');
    $lines = drycured_mdv5_lines($section);

    if (!$lines) {
        $lines = [
            'Sirovinu dobro ohladiti i pripremiti za rezanje ili mljevenje.',
            'Pomiješati meso, sol, začine i predviđene dodatke.',
            'Napuniti pripremljeni omotač bez zračnih džepova.',
            'Provesti fermentaciju, dimljenje ili sušenje prema tipu proizvoda.',
            'Zreti do stabilne teksture, čistog mirisa i sigurnog presjeka.',
        ];
    }

    $items = [];
    $i = 1;

    foreach ($lines as $line) {
        $items[] = [
            'day' => $i <= 3 ? 'Dan 1' : 'Proces',
            'title' => mb_substr($line, 0, 54),
            'text' => $line,
            'duration' => $i === 1 ? 'priprema' : 'prema fazi',
            'temperature' => $i <= 2 ? '0–4 °C' : 'kontrolirano',
            'control' => $i <= 2 ? 'raditi s hladnom sirovinom' : 'pratiti miris, površinu i teksturu',
            'goal' => $line,
            'critical' => 'Ako se pojavi neugodan miris, sluzava površina ili sumnjiva boja, proizvod ne smatrati sigurnim.',
        ];
        $i++;
        if ($i > 8) break;
    }

    return $items;
}

function drycured_mdv5_profile($markdown) {
    return [
        ['name' => 'Začinjenost', 'score' => 5],
        ['name' => 'Masnoća', 'score' => 6],
        ['name' => 'Dim', 'score' => stripos($markdown, 'dim') !== false ? 4 : 1],
        ['name' => 'Zrenje', 'score' => 6],
    ];
}

function drycured_mdv5_bridge_build_profile($post_id, $code = '') {
    $post = get_post($post_id);
    if (!$post || $post->post_type !== 'dry_recipe') return null;

    $code = $code ?: get_post_meta($post_id, '_dry_recipe_id', true);
    if (!$code) return null;

    $markdown = drycured_mdv5_get_markdown($post_id);
    if (strlen(trim($markdown)) < 300) return null;

    [$materials, $spices, $liquids, $factor, $source_material_g] = drycured_mdv5_ingredients($markdown);

    $lead = drycured_mdv5_plain($markdown);
    $lead = mb_substr($lead, 0, 260) . (mb_strlen($lead) > 260 ? '...' : '');

    $casing = drycured_mdv5_section($markdown, 'crijeva');
    $casing_text = $casing ? mb_substr(drycured_mdv5_plain($casing), 0, 80) : 'prema receptu';

    $duration = 'prema receptu';
    if (preg_match('/([0-9]{1,3}\s*[–-]\s*[0-9]{1,3}\s*dana)/u', $markdown, $m)) {
        $duration = $m[1];
    }

    return [
        'code' => $code,
        'title' => get_the_title($post_id),
        'region' => 'Lyon, Francuska',
        'type' => 'Suha kobasica',
        'lead' => $lead,
        'quick' => [
            ['label' => 'Šarža', 'value' => '10 kg mesne sirovine'],
            ['label' => 'Trajanje', 'value' => $duration],
            ['label' => 'Dimljenje', 'value' => stripos($markdown, 'dim') !== false ? 'hladni dim / prema receptu' : 'nije naglašeno'],
            ['label' => 'Crijeva', 'value' => $casing_text],
            ['label' => 'Skaliranje', 'value' => 'MD izvor → 10 kg'],
        ],
        'materials' => $materials,
        'spices' => $spices,
        'liquids' => $liquids,
        'profile' => drycured_mdv5_profile($markdown),
        'climate' => [
            [
                'title' => 'Fermentacijska mikroklima',
                'text' => '20–24 °C · 85–90 % relativne vlage · oko 72 sata. Prostor mora biti miran, bez jakog propuha, s dovoljno vlage da se površina ne osuši prije nego fermentacija odradi početnu stabilizaciju.',
            ],
            [
                'title' => 'Prva klima sušenja',
                'text' => '14–16 °C · 75–80 % relativne vlage · približno 14 dana. Strujanje zraka treba biti blago i stalno, ne agresivno. Cilj je postupno izvlačenje vlage bez zatvaranja površinske kore.',
            ],
            [
                'title' => 'Druga klima sušenja',
                'text' => '14–16 °C · 65–70 % relativne vlage · do približno 30–35 % gubitka početne mase. Vlažnost se spušta tek nakon prve faze, kada je površina stabilna i nema znakova sluzi ili ljepljivosti.',
            ],
            [
                'title' => 'Rizik tvrde kore',
                'text' => 'Ako je zrak presuh ili je propuh prejak, debela kobasica može izvana otvrdnuti, a u jezgri zadržati vlagu. Rješenje: smanjiti strujanje zraka, povisiti relativnu vlagu i produljiti zrenje.',
            ],
            [
                'title' => 'Završna klima čuvanja',
                'text' => '10–14 °C · suh, taman i prozračan prostor. Gotov proizvod čuvati bez kondenzacije i bez zatvorenog vlažnog zraka; nakon rezanja zaštititi presjek od pretjeranog isušivanja.',
            ],
        ],
        'timeline' => drycured_mdv5_timeline($markdown),
        'errors' => [
            [
                'problem' => 'Razmazana masnoća u presjeku',
                'phase' => 'Mljevenje / miješanje',
                'severity' => 'Srednji rizik',
                'level' => 'warning',
                'cause' => 'Sirovina ili slanina nisu bile dovoljno hladne.',
                'solution' => 'Prekinuti obradu, ohladiti sirovinu i nastaviti tek kada je masa ponovno čvrsta.',
            ],
            [
                'problem' => 'Zračni džepovi',
                'phase' => 'Punjenje',
                'severity' => 'Srednji rizik',
                'level' => 'warning',
                'cause' => 'Neravnomjerno punjenje ili loše odzračivanje omotača.',
                'solution' => 'Puniti čvrsto, ali bez pucanja omotača; zračne džepove probosti sterilnom iglom.',
            ],
            [
                'problem' => 'Neugodan miris',
                'phase' => 'Fermentacija / zrenje',
                'severity' => 'Visok rizik',
                'level' => 'danger',
                'cause' => 'Previsoka temperatura, kvarenje ili presporo sušenje.',
                'solution' => 'Ne prikrivati začinima. Ako postoji sumnja u zdravstvenu ispravnost, proizvod odbaciti.',
            ],
        ],
        'done_when' => [
            ['title' => 'Miris je čist', 'text' => 'Nema truležnih, kiselih ni užeglih nota.'],
            ['title' => 'Površina je stabilna', 'text' => 'Nema sluzi, ljepljivosti ni mokrih mjesta.'],
            ['title' => 'Presjek je siguran', 'text' => 'Nema vlažne jezgre, sivih zona ni razmazane masnoće.'],
            ['title' => 'Tekstura je zrela', 'text' => 'Kobasica je čvrsta, ali ne presušena.'],
        ],
        'safety' => [
            ['level' => 'green', 'title' => 'Zeleno — normalno', 'text' => 'Čist miris, stabilna površina i očekivan tijek sušenja.'],
            ['level' => 'yellow', 'title' => 'Žuto — oprez', 'text' => 'Blaga ljepljivost, presporo sušenje ili pretvrda površina. Korigirati uvjete i pratiti.'],
            ['level' => 'red', 'title' => 'Crveno — odbaci', 'text' => 'Truležan, kiseo ili užegao miris, sluzava površina ili sumnjive promjene u presjeku.'],
        ],
        'serving' => [
            ['title' => 'Rezanje', 'text' => 'Rezati tanko, čistim nožem, nakon kratkog odmora na sobnoj temperaturi.'],
            ['title' => 'Posluživanje', 'text' => 'Poslužiti uz kruh, sir, ukiseljeno povrće i jednostavnu zakusku.'],
            ['title' => 'Čuvanje', 'text' => 'Čuvati na hladnom, suhom i tamnom mjestu uz umjereno strujanje zraka.'],
            ['title' => 'Kada odbaciti', 'text' => 'Ne konzumirati ako se pojavi neugodan miris, sluzava površina ili sumnjiva promjena boje.'],
        ],
    ];
}


/*
 * DRYCURED_MD_TO_DCV5_PREVIEW_BRIDGE_v03
 * Preview-only adapter for selected MD-import recipes.
 * No public takeover: requires option-enabled preview token and selected preview IDs.
 */
if (!function_exists('drycured_mdv5_preview_enabled_v03')) {
    function drycured_mdv5_preview_enabled_v03() {
        return get_option('drycured_md_v5_preview_bridge_enabled', '0') === '1';
    }
}

if (!function_exists('drycured_mdv5_preview_ids_v03')) {
    function drycured_mdv5_preview_ids_v03() {
        $raw = get_option('drycured_md_v5_preview_bridge_ids', '');
        return array_filter(array_map('absint', preg_split('/\s*,\s*/', (string)$raw)));
    }
}

if (!function_exists('drycured_mdv5_preview_token_ok_v03')) {
    function drycured_mdv5_preview_token_ok_v03() {
        if (!drycured_mdv5_preview_enabled_v03()) return false;
        $stored = (string)get_option('drycured_md_v5_preview_bridge_token', '');
        $given = isset($_GET['mdv5_preview']) ? (string)$_GET['mdv5_preview'] : '';
        return $stored !== '' && $given !== '' && hash_equals($stored, $given);
    }
}

if (!function_exists('drycured_mdv5_clean_text_v03')) {
    function drycured_mdv5_clean_text_v03($text) {
        $text = wp_strip_all_tags((string)$text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace('??', ' ', $text);
        $text = preg_replace('/#+\s*/u', '', $text);
        $text = preg_replace('/radni zapis iz europske baze\.?/iu', '', $text);
        $text = preg_replace('/Status autentifikacije:\s*approved\.?/iu', '', $text);
        $text = preg_replace('/\bapproved\b/iu', '', $text);
        $text = preg_replace('/svinjetina\/svinjski proizvodi,\s*riba\/morski proizvodi/iu', 'svinjetina', $text);
        $text = preg_replace('/riba\/morski proizvodi/iu', 'riba', $text);
        $text = preg_replace('/sušeni zreli sir|suseni zreli sir/iu', 'suhomesnati proizvod', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return trim($text);
    }
}

if (!function_exists('drycured_mdv5_md_sections_v03')) {
    function drycured_mdv5_md_sections_v03($md) {
        $md = str_replace(["\r\n", "\r"], "\n", (string)$md);
        $sections = [];
        if (!preg_match_all('/^###\s+(.+?)\s*$/mu', $md, $m, PREG_OFFSET_CAPTURE)) {
            return $sections;
        }

        $count = count($m[0]);
        for ($i = 0; $i < $count; $i++) {
            $title = mb_strtolower(trim($m[1][$i][0]), 'UTF-8');
            $start = $m[0][$i][1] + strlen($m[0][$i][0]);
            $end = ($i + 1 < $count) ? $m[0][$i + 1][1] : strlen($md);
            $sections[$title] = trim(substr($md, $start, $end - $start));
        }
        return $sections;
    }
}

if (!function_exists('drycured_mdv5_title_v03')) {
    function drycured_mdv5_title_v03($post_id, $data, $md) {
        if (is_array($data) && !empty($data['title'])) {
            return drycured_mdv5_clean_text_v03($data['title']);
        }

        if (preg_match('/^##\s+(.+?)\s*$/mu', (string)$md, $m)) {
            return drycured_mdv5_clean_text_v03($m[1]);
        }

        return drycured_mdv5_clean_text_v03(get_the_title($post_id));
    }
}

if (!function_exists('drycured_mdv5_family_v03')) {
    function drycured_mdv5_family_v03($title, $data = null) {
        $extra = '';
        if (is_array($data)) {
            $extra = ($data['category'] ?? '') . ' ' . ($data['product_type'] ?? '');
        }
        $text = mb_strtolower($title . ' ' . $extra, 'UTF-8');

        if (preg_match('/kobas|salama|salame|salami|saucisson|wurst|chorizo|loukaniko|kulen|mettwurst|salsiccia|carnati|cârnați|nduja|andouille|landjäger|landjager|droge worst|finocchiona|soppressa|soppressata|felino|milano|kabanosy/iu', $text)) return 'SAUSAGE_SALAMI';
        if (preg_match('/šunka|sunka|rohschinken|prosciutto|pršut|prsut|jamón|jamon|jambon/iu', $text)) return 'HAM_PROSCIUTTO';
        if (preg_match('/pancet|slanina|lardo|bacon|guanciale|leđna mast|ledna mast/iu', $text)) return 'BACON_FAT';
        if (preg_match('/bresaola|coppa|lonza|lonzo|vrat|but|rebra|kastradina|pastirma|pastrama/iu', $text)) return 'WHOLE_CUT';

        return 'OTHER';
    }
}

if (!function_exists('drycured_mdv5_type_v03')) {
    function drycured_mdv5_type_v03($family) {
        if ($family === 'SAUSAGE_SALAMI') return 'Kobasica / salama';
        if ($family === 'HAM_PROSCIUTTO') return 'Cijeli komad — šunka / pršut';
        if ($family === 'BACON_FAT') return 'Slanina / panceta / masno tkivo';
        if ($family === 'WHOLE_CUT') return 'Cijeli komad mesa';
        return 'Suhomesnati proizvod';
    }
}

if (!function_exists('drycured_mdv5_public_lead_v03')) {
    function drycured_mdv5_public_lead_v03($title, $family, $region) {
        $title = trim(drycured_mdv5_clean_text_v03($title), " —-");
        $region = drycured_mdv5_clean_text_v03($region);
        $where = $region ? ' područja ' . $region : '';

        if ($family === 'SAUSAGE_SALAMI') {
            return $title . ' je suha kobasica ili salama' . $where . ', prikazana kao proizvodni vodič s omjerima sirovine, začinima, punjenjem, sušenjem i kontrolnim točkama.';
        }
        if ($family === 'HAM_PROSCIUTTO') {
            return $title . ' je sušeni cijeli komad iz skupine šunki i pršuta' . $where . ', prikazan kroz soljenje, odmor, sušenje, zrenje i sigurnosne kontrole.';
        }
        if ($family === 'BACON_FAT') {
            return $title . ' je proizvod od slanine, pancete ili masnog tkiva' . $where . ', prikazan kroz soljenje, začinski sloj, sušenje, zrenje i kontrolu površine.';
        }
        if ($family === 'WHOLE_CUT') {
            return $title . ' je sušeni cijeli komad mesa' . $where . ', prikazan kroz pripremu komada, pac ili soljenje, sušenje, zrenje i provjeru gotovosti.';
        }
        return $title . ' je suhomesnati proizvod' . $where . ', prikazan kao proizvodni vodič s jasnim omjerima, postupkom i kontrolnim točkama.';
    }
}

if (!function_exists('drycured_mdv5_split_amount_v03')) {
    function drycured_mdv5_split_amount_v03($line) {
        $line = drycured_mdv5_clean_text_v03($line);
        $line = preg_replace('/^\-\s*/u', '', $line);
        $line = preg_replace('/^\*\s*/u', '', $line);
        $line = trim($line);

        if (preg_match('/^([0-9]+(?:[,.][0-9]+)?)\s*(kg|g|ml|l|L)\s+(.+)$/u', $line, $m)) {
            return [trim($m[3]), str_replace('.', ',', $m[1]) . ' ' . $m[2]];
        }

        if (preg_match('/^([0-9]+(?:[,.][0-9]+)?)\s*kg\s*[–-]\s*([0-9]+(?:[,.][0-9]+)?)\s*kg\s+(.+)$/u', $line, $m)) {
            return [trim($m[3]), str_replace('.', ',', $m[1]) . '–' . str_replace('.', ',', $m[2]) . ' kg'];
        }

        return [$line, ''];
    }
}

if (!function_exists('drycured_mdv5_percent_rate_v03')) {
    function drycured_mdv5_percent_rate_v03($amount) {
        $amount = trim((string)$amount);

        if (preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*kg/iu', $amount, $m)) {
            $kg = (float)str_replace(',', '.', $m[1]);
            return [number_format(($kg / 10.0) * 100, 1, ',', '') . ' %', ''];
        }

        if (preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*g/iu', $amount, $m)) {
            $g = (float)str_replace(',', '.', $m[1]);
            return [number_format(($g / 1000.0 / 10.0) * 100, 2, ',', '') . ' %', number_format($g / 10.0, 1, ',', '') . ' g/kg'];
        }

        if (preg_match('/([0-9]+(?:[,.][0-9]+)?)\s*ml/iu', $amount, $m)) {
            $ml = (float)str_replace(',', '.', $m[1]);
            return ['', number_format($ml / 10.0, 1, ',', '') . ' ml/kg'];
        }

        return ['', ''];
    }
}

if (!function_exists('drycured_mdv5_item_kind_v03')) {
    function drycured_mdv5_item_kind_v03($name, $amount) {
        $lower = mb_strtolower($name . ' ' . $amount, 'UTF-8');

        if (preg_match('/crijev|omotač|omotac|kolagen|mrežic|mrezic|špaga|spaga/iu', $lower)) return 'skip';
        if (preg_match('/vino|voda|ml\b|češnjak|cesnjak|bijeli luk/iu', $lower)) return 'liquid';

        if (preg_match('/meso|svinj|but|lopatica|plećka|plecka|slanina|masnoć|masnoc|mast|potrbušina|potrbusina|goved|kožom|kozom|prsa|vrat|leđ|led|file/iu', $lower)) {
            if (preg_match('/sol|papar|češnjak|cesnjak|paprika|komorač|komorac|čilij|cilij|šećer|secer|vino|brašno|brasno/iu', $lower)) {
                return 'spice';
            }
            return 'material';
        }

        return 'spice';
    }
}

if (!function_exists('drycured_mdv5_ingredients_v03')) {
    function drycured_mdv5_ingredients_v03($md) {
        $sections = drycured_mdv5_md_sections_v03($md);
        $body = '';

        foreach ($sections as $key => $value) {
            if (mb_stripos($key, 'sastoj') !== false) {
                $body = $value;
                break;
            }
        }

        $materials = [];
        $spices = [];
        $liquids = [];

        foreach (preg_split('/\n/u', $body) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!preg_match('/^\s*[-*]/u', $line)) continue;

            [$name, $amount] = drycured_mdv5_split_amount_v03($line);
            if ($name === '') continue;

            [$percent, $rate] = drycured_mdv5_percent_rate_v03($amount);

            $item = [
                'name' => drycured_mdv5_clean_text_v03($name),
                'amount' => drycured_mdv5_clean_text_v03($amount),
                'percent' => $percent,
                'rate' => $rate,
                'note' => '',
            ];

            $kind = drycured_mdv5_item_kind_v03($item['name'], $item['amount']);

            if ($kind === 'skip') continue;
            if ($kind === 'material') $materials[] = $item;
            elseif ($kind === 'liquid') $liquids[] = $item;
            else $spices[] = $item;
        }

        if (!$materials) {
            $materials[] = ['name'=>'Osnovna sirovina','amount'=>'10 kg','percent'=>'100 %','rate'=>'','note'=>'Potrebna završna provjera anatomskog sastava prije DCV5 migracije.'];
        }

        if (!$spices) {
            $spices[] = ['name'=>'Začini','amount'=>'prema receptu','percent'=>'','rate'=>'','note'=>'Potrebna završna provjera začina.'];
        }

        return [$materials, $spices, $liquids];
    }
}

if (!function_exists('drycured_mdv5_timeline_v03')) {
    function drycured_mdv5_timeline_v03($md, $family) {
        $sections = drycured_mdv5_md_sections_v03($md);
        $body = '';

        foreach ($sections as $key => $value) {
            if (mb_stripos($key, 'postup') !== false) {
                $body = $value;
                break;
            }
        }

        $timeline = [];
        $i = 1;

        foreach (preg_split('/\n/u', $body) as $line) {
            $line = trim($line);
            if (!preg_match('/^\-\s*(.+)$/u', $line, $m)) continue;

            $item = trim($m[1]);
            $parts = explode(':', $item, 2);
            $title = count($parts) === 2 ? trim($parts[0]) : ('Faza ' . $i);
            $text = count($parts) === 2 ? trim($parts[1]) : $item;

            $timeline[] = [
                'day' => 'Faza ' . $i,
                'title' => drycured_mdv5_clean_text_v03($title),
                'text' => drycured_mdv5_clean_text_v03($text),
                'critical' => 'Kontrolirati temperaturu, miris, površinu i tempo sušenja. Ako se pojavi odstupanje, usporiti proces i korigirati uvjete.',
            ];

            $i++;
            if ($i > 10) break;
        }

        if ($timeline) return $timeline;

        if ($family === 'SAUSAGE_SALAMI') {
            return [
                ['day'=>'Dan 1','title'=>'Priprema sirovine','text'=>'Meso i masnoću očistiti, narezati i ohladiti.','critical'=>'Raditi hladno; temperatura smjese ne smije rasti.'],
                ['day'=>'Dan 1','title'=>'Mljevenje i miješanje','text'=>'Meso mljeti prema receptu, dodati sol, začine i tekućine.','critical'=>'Masa se ne smije razmazati niti zagrijati.'],
                ['day'=>'Dan 1–2','title'=>'Punjenje','text'=>'Puniti u pripremljene omotače bez zračnih džepova.','critical'=>'Zrak probosti sterilnom iglom.'],
                ['day'=>'Dalje','title'=>'Sušenje i zrenje','text'=>'Voditi postupno sušenje i zrenje prema kalibru.','critical'=>'Izbjegavati tvrdu koru i vlažnu jezgru.'],
            ];
        }

        return [
            ['day'=>'Dan 1','title'=>'Priprema komada','text'=>'Komad očistiti, oblikovati i ohladiti.','critical'=>'Provjeriti miris, površinu i debljinu komada.'],
            ['day'=>'Soljenje','title'=>'Suhi pac / soljenje','text'=>'Sol i začine ravnomjerno utrljati u površinu.','critical'=>'Sol mora ravnomjerno prodirati kroz komad.'],
            ['day'=>'Dalje','title'=>'Sušenje i zrenje','text'=>'Sušiti i zreti postupno u stabilnim uvjetima.','critical'=>'Prebrzo sušenje zatvara površinu.'],
        ];
    }
}

if (!function_exists('drycured_mdv5_default_blocks_v03')) {
    function drycured_mdv5_default_blocks_v03($family) {
        $profile = ($family === 'SAUSAGE_SALAMI')
            ? [
                ['name'=>'Začini','score'=>7],
                ['name'=>'Dim','score'=>5],
                ['name'=>'Ljutina','score'=>3],
                ['name'=>'Slanoća','score'=>6],
                ['name'=>'Masnoća','score'=>5],
                ['name'=>'Tekstura','score'=>6],
            ]
            : [
                ['name'=>'Sol','score'=>7],
                ['name'=>'Dim','score'=>4],
                ['name'=>'Zrenje','score'=>8],
                ['name'=>'Slanoća','score'=>6],
                ['name'=>'Masnoća','score'=>5],
                ['name'=>'Tekstura','score'=>7],
            ];

        return [
            'profile' => $profile,
            'climate' => [
                ['title'=>'Izrada','text'=>'Raditi hladno, uredno i s dobro ohlađenom sirovinom.'],
                ['title'=>'Soljenje','text'=>'Sol mora biti ravnomjerno raspoređena i prilagođena tipu proizvoda.'],
                ['title'=>'Sušenje','text'=>'Sušenje mora biti postupno, bez naglog zatvaranja površine.'],
                ['title'=>'Zrenje','text'=>'Zrenje završava tek kad su miris, tekstura i presjek stabilni.'],
            ],
            'errors' => [
                ['problem'=>'Pretvrda površina','phase'=>'Sušenje','severity'=>'Oprez','level'=>'warning','cause'=>'Prebrz protok zraka ili preniska vlaga.','solution'=>'Smanjiti propuh, stabilizirati vlagu i produljiti zrenje.'],
                ['problem'=>'Razmazana mast','phase'=>'Mljevenje / miješanje','severity'=>'Oprez','level'=>'warning','cause'=>'Sirovina ili masnoća bile su pretopla.','solution'=>'Raditi hladno i po potrebi prekinuti proces radi hlađenja.'],
                ['problem'=>'Neugodan miris','phase'=>'Zrenje / čuvanje','severity'=>'Visok rizik','level'=>'danger','cause'=>'Kvarenje, previsoka temperatura ili higijenski problem.','solution'=>'Ne koristiti proizvod ako postoji sumnja u zdravstvenu ispravnost.'],
            ],
            'done_when' => [
                ['title'=>'Miris je čist','text'=>'Nema truležnih, kiselih ni užeglih nota.'],
                ['title'=>'Tekstura je stabilna','text'=>'Presjek ili komad djeluju povezano, bez vlažne jezgre.'],
                ['title'=>'Površina je pravilna','text'=>'Površina je suha i stabilna, bez sluzi i sumnjivih promjena.'],
            ],
            'safety' => [
                ['level'=>'green','title'=>'Zeleno — normalno','text'=>'Čist miris, stabilna površina i očekivan tijek sušenja.'],
                ['level'=>'yellow','title'=>'Žuto — oprez','text'=>'Pretvrda površina, blaga ljepljivost ili sporo sušenje. Korigirati uvjete.'],
                ['level'=>'red','title'=>'Crveno — odbaci','text'=>'Truležan, kiseo ili užegao miris, sluzava površina ili sumnjiv presjek.'],
            ],
            'serving' => [
                ['title'=>'Rezanje','text'=>'Rezati tanko ili prema tipu proizvoda, čistim nožem.'],
                ['title'=>'Čuvanje','text'=>'Čuvati hladno, tamno i prozračno; vakuumirati tek kad je proizvod stabilan.'],
            ],
        ];
    }
}

if (!function_exists('drycured_mdv5_bridge_build_profile_v03')) {
    function drycured_mdv5_bridge_build_profile_v03($post_id, $code = '') {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'dry_recipe') return null;

        $code = $code ?: get_post_meta($post_id, '_dry_recipe_id', true);
        if (!$code) return null;

        $md = drycured_mdv5_get_markdown($post_id);
        if (strlen(trim($md)) < 300) return null;

        $raw_data = get_post_meta($post_id, '_dry_recipe_data', true);
        $data = json_decode((string)$raw_data, true);
        if (!is_array($data)) $data = null;

        $title = drycured_mdv5_title_v03($post_id, $data, $md);
        $family = drycured_mdv5_family_v03($title, $data);

        $country = is_array($data) ? drycured_mdv5_clean_text_v03($data['country_hr'] ?? '') : '';
        $region = is_array($data) ? drycured_mdv5_clean_text_v03($data['region'] ?? '') : '';
        $region_line = trim($country . (($country && $region && $country !== $region) ? ' · ' . $region : ($region ?: '')));

        [$materials, $spices, $liquids] = drycured_mdv5_ingredients_v03($md);
        $blocks = drycured_mdv5_default_blocks_v03($family);

        return [
            'code' => $code,
            'title' => $title,
            'region' => $region_line,
            'type' => drycured_mdv5_type_v03($family),
            'lead' => drycured_mdv5_public_lead_v03($title, $family, $region_line),
            'quick' => ($family === 'SAUSAGE_SALAMI')
                ? [
                    ['label'=>'Šarža','value'=>'10 kg'],
                    ['label'=>'Trajanje','value'=>'prema kalibru i zrenju'],
                    ['label'=>'Dimljenje','value'=>'hladni dim gdje je primjenjivo'],
                    ['label'=>'Omotač','value'=>'prirodni ili jestivi omotač'],
                    ['label'=>'Gubitak mase','value'=>'30–40 %'],
                ]
                : [
                    ['label'=>'Šarža','value'=>'10 kg / cijeli komad'],
                    ['label'=>'Trajanje','value'=>'prema debljini komada'],
                    ['label'=>'Dimljenje','value'=>'po receptu'],
                    ['label'=>'Omotač','value'=>'bez crijeva'],
                    ['label'=>'Gubitak mase','value'=>'postupno sušenje'],
                ],
            'materials' => $materials,
            'spices' => $spices,
            'liquids' => $liquids,
            'profile' => $blocks['profile'],
            'climate' => $blocks['climate'],
            'timeline' => drycured_mdv5_timeline_v03($md, $family),
            'errors' => $blocks['errors'],
            'done_when' => $blocks['done_when'],
            'safety' => $blocks['safety'],
            'serving' => $blocks['serving'],
            '_mdv5_preview_bridge_v03' => true,
            '_mdv5_preview_family_v03' => $family,
        ];
    }
}


function drycured_md_v5_bridge_profile($post_id, $code = '') {
    if (function_exists('drycured_b01_wholecut_v5_profile')) {
        $wholecut_profile = drycured_b01_wholecut_v5_profile($post_id, $code);
        if ($wholecut_profile) {
            return $wholecut_profile;
        }
    }

    if (
        function_exists('drycured_mdv5_preview_token_ok_v03') &&
        drycured_mdv5_preview_token_ok_v03()
    ) {
        $preview_ids = function_exists('drycured_mdv5_preview_ids_v03') ? drycured_mdv5_preview_ids_v03() : [];
        if ($preview_ids && in_array((int)$post_id, $preview_ids, true)) {
            $preview_profile = drycured_mdv5_bridge_build_profile_v03($post_id, $code);
            if ($preview_profile) {
                return $preview_profile;
            }
        }
    }

    if (!drycured_mdv5_enabled()) return null;

    $pilot_ids = drycured_mdv5_pilot_ids();
    if ($pilot_ids && !in_array((int)$post_id, $pilot_ids, true)) return null;

    return drycured_mdv5_bridge_build_profile($post_id, $code);
}
