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

function drycured_md_v5_bridge_profile($post_id, $code = '') {
    if (function_exists('drycured_b01_wholecut_v5_profile')) {
        $wholecut_profile = drycured_b01_wholecut_v5_profile($post_id, $code);
        if ($wholecut_profile) {
            return $wholecut_profile;
        }
    }

    if (!drycured_mdv5_enabled()) return null;

    $pilot_ids = drycured_mdv5_pilot_ids();
    if ($pilot_ids && !in_array((int)$post_id, $pilot_ids, true)) return null;

    return drycured_mdv5_bridge_build_profile($post_id, $code);
}
