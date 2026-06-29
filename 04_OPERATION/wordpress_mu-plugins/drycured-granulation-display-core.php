<?php
/**
 * DRYCURED — Granulation Display Core SAFE v1.2
 *
 * Prikazuje obaveznu granulaciju iz _dry_verified_process.mljevenje.
 * Ne mijenja bazu.
 * Ne mijenja recepte.
 * Ne dira URL-ove.
 *
 * v1.2: dcg11_insert_box() sada preskace JSON-LD <script> blokove pri pretrazi
 *       pozicije za 'Mljevenje' kako bi izbjegao lazni match u schema.org markupu.
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcg11_enabled() {
    return get_option('drycured_granulation_display_core_enabled', '1') === '1';
}

function dcg11_get_data($post_id) {
    $raw = get_post_meta($post_id, '_dry_verified_process', true);
    $data = json_decode($raw, true);

    if (!is_array($data) || empty($data['mljevenje']) || !is_array($data['mljevenje'])) {
        return null;
    }

    $m = $data['mljevenje'];

    $meat = isset($m['krto_meso_resetka_mm']) ? trim((string) $m['krto_meso_resetka_mm']) : '';
    if ($meat === '' && isset($m['meso_resetka_mm'])) {
        $meat = trim((string) $m['meso_resetka_mm']);
    }
    if ($meat === '' && isset($m['resetka_mm'])) {
        $meat = trim((string) $m['resetka_mm']);
    }

    $fat = isset($m['slanina_rezanje']) ? trim((string) $m['slanina_rezanje']) : '';
    if ($fat === '' && isset($m['masnoca_rezanje'])) {
        $fat = trim((string) $m['masnoca_rezanje']);
    }

    $pre = isset($m['slanina_preduvjet']) ? trim((string) $m['slanina_preduvjet']) : '';
    if ($pre === '' && isset($m['masnoca_preduvjet'])) {
        $pre = trim((string) $m['masnoca_preduvjet']);
    }

    $profile = isset($m['granulacija_profil']) ? trim((string) $m['granulacija_profil']) : '';
    $control = isset($m['kontrola']) ? trim((string) $m['kontrola']) : '';

    if ($meat === '' && $fat === '') {
        return null;
    }

    return array(
        'meat' => $meat,
        'fat' => $fat,
        'pre' => $pre,
        'profile' => $profile,
        'control' => $control,
    );
}

function dcg11_mm_text($v) {
    $v = trim((string) $v);
    if ($v === '') {
        return 'nije upisano';
    }
    if (preg_match('/mm/iu', $v)) {
        return $v;
    }
    return $v . ' mm';
}

function dcg11_box($g) {
    $meat = $g['meat'] !== '' ? 'mljeti na rešetku ' . esc_html(dcg11_mm_text($g['meat'])) : 'nije upisano';
    $fat = $g['fat'] !== '' ? esc_html($g['fat']) : 'nije upisano';
    $pre = $g['pre'] !== '' ? esc_html($g['pre']) : 'vrlo hladna';
    $profile = $g['profile'] !== '' ? esc_html($g['profile']) : 'jasan presjek bez razmazane masti';
    $control = $g['control'] !== '' ? esc_html($g['control']) : 'ako se masa počne lijepiti ili mast ostavlja film po posudi, obradu treba prekinuti i sirovinu vratiti na hlađenje';

    return '<div class="drycured-granulation-display-core" style="margin:14px 0 18px;padding:16px 18px;border:1px solid #e0b45f;border-radius:16px;background:#fff6df;box-shadow:0 6px 18px rgba(90,60,20,.08);">' .
        '<div style="font-size:12px;text-transform:uppercase;letter-spacing:.07em;color:#8b5b18;font-weight:800;margin-bottom:8px;">Obavezna granulacija</div>' .
        '<h3 style="margin:0 0 10px;font-size:21px;line-height:1.25;">Mljevenje i obrada tvrde slanine</h3>' .
        '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:10px;">' .
        '<div style="background:#fff;border:1px solid #efd9aa;border-radius:12px;padding:10px 12px;"><strong>Meso</strong><br>' . $meat . '</div>' .
        '<div style="background:#fff;border:1px solid #efd9aa;border-radius:12px;padding:10px 12px;"><strong>Tvrda leđna slanina / masnoća</strong><br>' . $fat . '</div>' .
        '<div style="background:#fff;border:1px solid #efd9aa;border-radius:12px;padding:10px 12px;"><strong>Preduvjet</strong><br>' . $pre . '</div>' .
        '</div>' .
        '<p style="margin:12px 0 0;"><strong>Cilj:</strong> ' . $profile . '</p>' .
        '<p style="margin:6px 0 0;"><strong>Kontrola:</strong> ' . $control . '</p>' .
        '</div>';
}

function dcg11_insert_box($html, $box) {
    if (strpos($html, 'drycured-granulation-display-core') !== false) {
        return $html;
    }

    $count = 0;

    $html2 = preg_replace(
        '/(<h[1-5][^>]*>\s*Mljevenje[^<]*<\/h[1-5]>)/iu',
        '$1' . $box,
        $html,
        1,
        $count
    );

    if ($count > 0) {
        return $html2;
    }

    // Fallback: strip ALL <script> blocks before searching for 'Mljevenje'.
    // Skipping only JSON-LD was insufficient: regular <script> blocks containing
    // JS data (phaseSpecs objects) also contain 'Mljevenje i rezanje' as a key,
    // and injecting the box into JS string literals breaks the entire page.
    // Split HTML into alternating non-script / script chunks; search only non-script.
    $parts = preg_split('/(<script[^>]*>.*?<\/script>)/su', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $prefix_len = 0;
    for ($pi = 0; $pi < count($parts); $pi++) {
        $part = $parts[$pi];
        if ($pi % 2 === 1) {
            // script block — skip entirely
            $prefix_len += strlen($part);
            continue;
        }
        // non-script chunk — safe to search
        $pos = mb_stripos($part, 'Mljevenje', 0, 'UTF-8');
        if ($pos !== false) {
            $local_byte = strlen(mb_substr($part, 0, $pos, 'UTF-8'));
            $abs = $prefix_len + $local_byte;
            $insert_pos = strpos($html, '</', $abs);
            if ($insert_pos !== false) {
                $close_pos = strpos($html, '>', $insert_pos);
                if ($close_pos !== false) {
                    return substr($html, 0, $close_pos + 1) . $box . substr($html, $close_pos + 1);
                }
            }
        }
        $prefix_len += strlen($part);
    }

    return str_replace('</body>', $box . '</body>', $html);
}

function dcg11_replace_resetka_value($html, $g) {
    $replacement = 'meso ' . esc_html(dcg11_mm_text($g['meat']));
    if ($g['fat'] !== '') {
        $replacement .= '; slanina ' . esc_html($g['fat']);
    }

    $label_pos = mb_stripos($html, 'REŠETKA', 0, 'UTF-8');
    if ($label_pos === false) {
        $label_pos = mb_stripos($html, 'RESETKA', 0, 'UTF-8');
    }

    if ($label_pos === false) {
        return $html;
    }

    $window = mb_substr($html, $label_pos, 1500, 'UTF-8');
    $old_pos = mb_stripos($window, 'prema receptu', 0, 'UTF-8');

    if ($old_pos === false) {
        return $html;
    }

    $absolute = $label_pos + $old_pos;

    return mb_substr($html, 0, $absolute, 'UTF-8') .
        $replacement .
        mb_substr($html, $absolute + mb_strlen('prema receptu', 'UTF-8'), null, 'UTF-8');
}

add_action('template_redirect', function () {
    if (!dcg11_enabled()) {
        return;
    }

    if (!is_singular('dry_recipe')) {
        return;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) {
        return;
    }

    $g = dcg11_get_data($post_id);
    if (!$g) {
        return;
    }

    ob_start(function ($html) use ($g) {
        $box = dcg11_box($g);
        $html = dcg11_insert_box($html, $box);
        $html = dcg11_replace_resetka_value($html, $g);
        $html = str_replace('</body>', '<!-- drycured-granulation-display-core-safe-v11-active --></body>', $html);
        return $html;
    });
}, 0);
