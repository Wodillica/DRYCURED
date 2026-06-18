<?php


if (!defined('ABSPATH')) {
    fwrite(STDERR, "FAIL: Ova skripta se mora pokrenuti kroz WP-CLI eval-file.\n");
    exit(1);
}

$out_dir = getenv('DC_AUDIT_OUT');
if (!$out_dir) {
    fwrite(STDERR, "FAIL: DC_AUDIT_OUT nije postavljen.\n");
    exit(1);
}

$fetch_public = getenv('DC_AUDIT_FETCH_PUBLIC');
$fetch_public = ($fetch_public === false || $fetch_public === '') ? '1' : $fetch_public;
$fetch_public = in_array(strtolower($fetch_public), ['1', 'yes', 'true', 'on'], true);

$public_scan_max = getenv('DC_AUDIT_PUBLIC_SCAN_MAX');
$public_scan_max = ($public_scan_max === false || $public_scan_max === '') ? 0 : (int)$public_scan_max;

if (!is_dir($out_dir)) {
    mkdir($out_dir, 0775, true);
}

$csv_path = rtrim($out_dir, '/') . '/recipe_type_readonly_audit_v1.csv';
$json_path = rtrim($out_dir, '/') . '/recipe_type_readonly_audit_v1.json';
$summary_path = rtrim($out_dir, '/') . '/recipe_type_readonly_audit_v1_summary.md';

function dc_audit_lower($text) {
    $text = (string)$text;
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($text, 'UTF-8');
    }
    return strtolower($text);
}

function dc_audit_norm($text) {
    $text = wp_strip_all_tags((string)$text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = dc_audit_lower($text);
    $text = preg_replace('/\s+/u', ' ', $text);
    return trim($text);
}

function dc_audit_has_any($text, array $patterns) {
    foreach ($patterns as $pattern) {
        if (@preg_match($pattern, $text)) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        } else {
            if (strpos($text, $pattern) !== false) {
                return true;
            }
        }
    }
    return false;
}

function dc_audit_count_hits($text, array $patterns) {
    $count = 0;
    foreach ($patterns as $pattern) {
        if (@preg_match($pattern, $text)) {
            if (preg_match($pattern, $text)) {
                $count++;
            }
        } else {
            if (strpos($text, $pattern) !== false) {
                $count++;
            }
        }
    }
    return $count;
}

function dc_audit_flatten_meta($post_id) {
    $meta = get_post_meta($post_id);
    $parts = [];
    foreach ($meta as $key => $values) {
        $parts[] = (string)$key;
        foreach ((array)$values as $value) {
            if (is_scalar($value)) {
                $parts[] = (string)$value;
            } else {
                $parts[] = wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }
    }
    return implode("\n", $parts);
}

function dc_audit_fetch_public_html($url) {
    if (!$url) {
        return [
            'ok' => false,
            'status' => '',
            'body' => '',
            'error' => 'empty_url',
        ];
    }

    $response = wp_remote_get($url, [
        'timeout' => 4,
        'redirection' => 2,
        'sslverify' => false,
        'headers' => [
            'User-Agent' => 'DrycuredRecipeTypeAudit/1.0 read-only',
        ],
    ]);

    if (is_wp_error($response)) {
        return [
            'ok' => false,
            'status' => '',
            'body' => '',
            'error' => $response->get_error_message(),
        ];
    }

    $code = (int)wp_remote_retrieve_response_code($response);
    $body = (string)wp_remote_retrieve_body($response);

    return [
        'ok' => ($code >= 200 && $code < 400),
        'status' => (string)$code,
        'body' => $body,
        'error' => '',
    ];
}

$patterns = [
    'ground' => [
        '/\bkobasic/u',
        '/\bsalam/u',
        '/\bkulen/u',
        '/\bkulin/u',
        '/\bsudžuk/u',
        '/\bsudzuk/u',
        '/\bnadjev/u',
        '/\bnadev/u',
        '/puniti u/u',
        '/puni se u/u',
        '/crijev/u',
        '/crev/u',
        '/omotač/u',
        '/omotac/u',
        '/mljeven/u',
        '/mleven/u',
        '/samelj/u',
        '/samlet/u',
        '/rešetk/u',
        '/resetk/u',
        '/šajb/u',
        '/sajb/u',
        '/punilic/u',
    ],
    'whole' => [
        '/\bpršut/u',
        '/\bprsut/u',
        '/\bšunk/u',
        '/\bsunk/u',
        '/\bvrat/u',
        '/\bplećk/u',
        '/\bpleck/u',
        '/\bpancet/u',
        '/\bslanina/u',
        '/\brebr/u',
        '/\bkare/u',
        '/\blonza/u',
        '/\bbresaol/u',
        '/\blardo/u',
        '/\bpastirm/u',
        '/\bpastrm/u',
        '/suhi pac/u',
        '/mokri pac/u',
        '/salamur/u',
        '/utrlj/u',
        '/potop/u',
        '/preslag/u',
        '/okret/u',
        '/komad/u',
    ],
    'thermal' => [
        '/\bbarit/u',
        '/\bbari se/u',
        '/\bobaren/u',
        '/\bkuhat/u',
        '/\bkuhan/u',
        '/\bkuva/u',
        '/\bparit/u',
        '/\bparen/u',
        '/\bpeći/u',
        '/\bpeci/u',
        '/\bpečen/u',
        '/\bpecen/u',
        '/toplins/u',
        '/termičk/u',
        '/termick/u',
        '/pasteriz/u',
        '/temperatura jezgre/u',
        '/temperaturu jezgre/u',
        '/\bkrvavic/u',
        '/\bjetrenja/u',
        '/\bdžigern/u',
        '/\bdzigern/u',
        '/\btlačen/u',
        '/\btlacen/u',
        '/\bšvarg/u',
        '/\bsvarg/u',
        '/\bhrenov/u',
        '/\bviršl/u',
        '/\bvirsl/u',
        '/\bmortadel/u',
        '/\bzampone/u',
        '/\bcotechino/u',
        '/\bbujon/u',
        '/\b80\s*°?\s*c/u',
        '/\b75\s*°?\s*c/u',
        '/\b72\s*°?\s*c/u',
    ],
    'fish' => [
        '/\brib/u',
        '/\bribl/u',
        '/\bmorsk/u',
        '/\bpastrv/u',
        '/\blosos/u',
        '/\btuna/u',
        '/\bbakalar/u',
        '/\bskuš/u',
        '/\bskus/u',
        '/\bsardin/u',
        '/\binćun/u',
        '/\bincun/u',
        '/\bbrancin/u',
        '/\borad/u',
        '/\boslić/u',
        '/\boslic/u',
        '/hladni lanac/u',
    ],
];

$field_patterns = [
    'granulation' => [
        '/rešetk[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/resetk[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/šajb[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/sajb[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/otvor[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/ploč[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/ploc[^.]{0,80}\b[0-9]{1,2}\s*mm/u',
        '/\b[0-9]{1,2}\s*mm[^.]{0,80}(rešetk|resetk|šajb|sajb|mljeven|mleven)/u',
    ],
    'fat_handling' => [
        '/slanina[^.]{0,120}(kock|rezan|sjeck|seck|nož|noz|smrz|zamrz|leđ|ledn|tvrda)/u',
        '/masnoć[^.]{0,120}(kock|rezan|sjeck|seck|nož|noz|smrz|zamrz|leđ|ledn|tvrda)/u',
        '/masno tkivo[^.]{0,120}(kock|rezan|sjeck|seck|nož|noz|smrz|zamrz|leđ|ledn|tvrda)/u',
    ],
    'casing' => [
        '/crijev/u',
        '/crev/u',
        '/omotač/u',
        '/omotac/u',
        '/želudac/u',
        '/zeludac/u',
        '/mjehur/u',
        '/bešik/u',
        '/besik/u',
    ],
    'brine_or_cure' => [
        '/salamur/u',
        '/\bpac/u',
        '/suho soljen/u',
        '/mokro soljen/u',
        '/soljenj/u',
        '/utrlj/u',
        '/potop/u',
        '/naliti/u',
        '/preslag/u',
        '/okret/u',
    ],
    'thermal_process' => [
        '/\bbarit/u',
        '/\bbari se/u',
        '/\bobaren/u',
        '/\bkuhat/u',
        '/\bkuhan/u',
        '/\bkuva/u',
        '/\bparit/u',
        '/\bparen/u',
        '/\bpeći/u',
        '/\bpeci/u',
        '/\bpečen/u',
        '/\bpecen/u',
        '/toplins/u',
        '/termičk/u',
        '/termick/u',
        '/pasteriz/u',
    ],
    'thermal_params' => [
        '/\b[0-9]{2,3}\s*°?\s*c/u',
        '/\b[0-9]{1,3}\s*(minuta|min|sati|h)\b/u',
        '/temperatura jezgre/u',
        '/temperaturu jezgre/u',
    ],
    'cold_chain' => [
        '/hladni lanac/u',
        '/svjež/u',
        '/svjez/u',
        '/rashlađ/u',
        '/rashlad/u',
        '/ohlađ/u',
        '/ohlad/u',
        '/\b0\s*-\s*4\s*°?\s*c/u',
        '/\b4\s*°?\s*c/u',
    ],
    'smoking' => [
        '/dimljen/u',
        '/dimiti/u',
        '/dimi se/u',
        '/dimom/u',
        '/pušnic/u',
        '/pusnic/u',
        '/hladni dim/u',
        '/topli dim/u',
        '/vrući dim/u',
        '/vruci dim/u',
    ],
    'drying' => [
        '/sušen/u',
        '/susen/u',
        '/sušiti/u',
        '/susiti/u',
        '/suši se/u',
        '/susi se/u',
        '/prosuš/u',
        '/prosus/u',
        '/prozrač/u',
        '/prozrac/u',
        '/propuh/u',
    ],
    'aging' => [
        '/zrenj/u',
        '/zrije/u',
        '/zri/u',
        '/sazrij/u',
        '/dozrij/u',
        '/dozrevan/u',
    ],
    'phase_time_or_params' => [
        '/\b[0-9]{1,3}\s*(dan|dana|sat|sata|h|min|minuta|tjedan|tjedna|mjesec|mjeseca|nedjel|nedelj)/u',
        '/\b[0-9]{1,2}\s*-\s*[0-9]{1,3}\s*(dan|dana|sat|sata|h|min|tjedan|tjedna|mjesec|mjeseca|nedjel|nedelj)/u',
        '/\b[0-9]{1,3}\s*°?\s*c/u',
        '/\b[0-9]{1,3}\s*%/u',
        '/relativn[aeu] vlag/u',
        '/\brh\b/u',
        '/ciklus/u',
        '/pauz/u',
    ],
    'nitrite' => [
        '/nitrit/u',
        '/nitritna sol/u',
        '/nitritne soli/u',
        '/nitritnom/u',
        '/\be250\b/u',
    ],
    'nitrite_note' => [
        '/nitritnu sol vagati precizno/u',
        '/ne prekoračiti navedenu količinu/u',
        '/ne prekoraciti navedenu kolicinu/u',
        '/ne dodavati je od oka/u',
        '/ne kombinirati s drugim nitritnim/u',
        '/varijanta bez nitrita/u',
    ],
    'fallback_internal' => [
        '/\bpreview\b/u',
        '/\bfallback\b/u',
        '/source-lock/u',
        '/radni recept/u',
        '/\baudit\b/u',
        '/\badapter\b/u',
        '/\bclone\b/u',
        '/\bdebug\b/u',
        '/\binternal\b/u',
        '/testni prikaz/u',
        '/privremeni tekst/u',
        '/fotografija će biti dodana/u',
        '/fotografija ce biti dodana/u',
        '/sadržaj će biti dopunjen/u',
        '/sadrzaj ce biti dopunjen/u',
        '/čeka provjeru/u',
        '/ceka provjeru/u',
    ],
    'problem_words' => [
        '/grešk/u',
        '/gresk/u',
        '/problem/u',
        '/uzrok/u',
        '/kvar/u',
        '/neisprav/u',
        '/pukn/u',
        '/plijesan/u',
        '/plijesni/u',
        '/sluz/u',
        '/užeg/u',
        '/uzeg/u',
        '/kisel/u',
    ],
    'solution_words' => [
        '/rješen/u',
        '/rjesen/u',
        '/korekc/u',
        '/poprav/u',
        '/spriječ/u',
        '/sprijec/u',
        '/smanj/u',
        '/poveć/u',
        '/povec/u',
        '/odbac/u',
        '/ne koristiti/u',
    ],
];

$ids = get_posts([
    'post_type' => 'dry_recipe',
    'post_status' => ['publish', 'private', 'draft', 'pending', 'future'],
    'posts_per_page' => -1,
    'fields' => 'ids',
    'orderby' => 'ID',
    'order' => 'ASC',
    'no_found_rows' => true,
]);

$rows = [];
$summary = [
    'total' => 0,
    'by_type' => [],
    'by_status' => [],
    'by_confidence' => [],
    'public_blocked' => 0,
    'published_total' => 0,
    'published_blocked' => 0,
    'fallback_internal_hits' => 0,
    'nitrite_without_note' => 0,
    'public_fetch_enabled' => $fetch_public ? 'yes' : 'no',
    'public_fetch_errors' => 0,
];

$public_scanned = 0;

foreach ($ids as $post_id) {
    $post = get_post($post_id);
    if (!$post) {
        continue;
    }

    $title = get_the_title($post_id);
    $status = get_post_status($post_id);
    $url = get_permalink($post_id);

    $meta_text = dc_audit_flatten_meta($post_id);
    $base_text = implode("\n", [
        $title,
        $post->post_name,
        $post->post_excerpt,
        $post->post_content,
        $meta_text,
    ]);

    $public_fetch_status = '';
    $public_fetch_error = '';
    $public_html = '';

    if ($fetch_public && $status === 'publish' && $url && ($public_scan_max <= 0 || $public_scanned < $public_scan_max)) {
        $public_scanned++;
        $fetch = dc_audit_fetch_public_html($url);
        $public_fetch_status = $fetch['status'];
        $public_fetch_error = $fetch['error'];
        if (!$fetch['ok']) {
            $summary['public_fetch_errors']++;
        }
        $public_html = $fetch['body'];
    }

    $text = dc_audit_norm($base_text . "\n" . $public_html);

    $score_ground = dc_audit_count_hits($text, $patterns['ground']);
    $score_whole = dc_audit_count_hits($text, $patterns['whole']);
    $score_thermal = dc_audit_count_hits($text, $patterns['thermal']);
    $score_fish = dc_audit_count_hits($text, $patterns['fish']);

    $scores = [
        'GROUND_MEAT_OR_CASING' => $score_ground,
        'WHOLE_CUT' => $score_whole,
        'THERMAL_PROCESSED' => $score_thermal,
        'FISH_OR_SEAFOOD' => $score_fish,
    ];

    arsort($scores);
    $score_keys = array_keys($scores);
    $top_type = $score_keys[0];
    $top_score = (int)$scores[$top_type];
    $second_score = (int)$scores[$score_keys[1]];

    $type = $top_type;
    $confidence = 'low';

    if ($top_score < 2) {
        $type = 'NEEDS_CLASSIFICATION';
        $confidence = 'none';
    } elseif ($top_score >= 4 && ($top_score - $second_score) >= 2) {
        $confidence = 'high';
    } elseif ($top_score >= 3 && ($top_score - $second_score) >= 1) {
        $confidence = 'medium';
    } else {
        $confidence = 'low';
    }

    if ($confidence === 'low' && $top_score >= 2 && ($top_score - $second_score) <= 0) {
        $type = 'NEEDS_CLASSIFICATION';
    }

    
    if ($score_fish >= 2 && $score_fish >= ($score_ground + 1)) {
        $type = 'FISH_OR_SEAFOOD';
        $confidence = $score_fish >= 4 ? 'high' : 'medium';
    } elseif ($score_thermal >= 3 && $score_thermal >= ($score_ground - 1)) {
        $type = 'THERMAL_PROCESSED';
        $confidence = $score_thermal >= 5 ? 'high' : 'medium';
    } elseif ($score_ground >= 3 && $score_ground > $score_whole && $score_thermal < 3 && $score_fish < 2) {
        $type = 'GROUND_MEAT_OR_CASING';
        $confidence = $score_ground >= 5 ? 'high' : 'medium';
    } elseif ($score_whole >= 3 && $score_whole > $score_ground && $score_thermal < 3 && $score_fish < 2) {
        $type = 'WHOLE_CUT';
        $confidence = $score_whole >= 5 ? 'high' : 'medium';
    }

    $has_granulation = dc_audit_has_any($text, $field_patterns['granulation']);
    $has_fat_handling = dc_audit_has_any($text, $field_patterns['fat_handling']);
    $has_casing = dc_audit_has_any($text, $field_patterns['casing']);
    $has_brine_or_cure = dc_audit_has_any($text, $field_patterns['brine_or_cure']);
    $has_thermal_process = dc_audit_has_any($text, $field_patterns['thermal_process']);
    $has_thermal_params = dc_audit_has_any($text, $field_patterns['thermal_params']);
    $has_cold_chain = dc_audit_has_any($text, $field_patterns['cold_chain']);
    $has_smoking = dc_audit_has_any($text, $field_patterns['smoking']);
    $has_drying = dc_audit_has_any($text, $field_patterns['drying']);
    $has_aging = dc_audit_has_any($text, $field_patterns['aging']);
    $has_phase_time_or_params = dc_audit_has_any($text, $field_patterns['phase_time_or_params']);
    $has_nitrite = dc_audit_has_any($text, $field_patterns['nitrite']);
    $has_nitrite_note = dc_audit_has_any($text, $field_patterns['nitrite_note']);
    $has_fallback_internal = dc_audit_has_any($text, $field_patterns['fallback_internal']);
    $has_problem_words = dc_audit_has_any($text, $field_patterns['problem_words']);
    $has_solution_words = dc_audit_has_any($text, $field_patterns['solution_words']);

    $block_reasons = [];

    if ($type === 'NEEDS_CLASSIFICATION') {
        $block_reasons[] = 'NEEDS_CLASSIFICATION';
    }

    if ($type === 'GROUND_MEAT_OR_CASING') {
        if (!$has_granulation) {
            $block_reasons[] = 'GROUND_MISSING_GRANULATION';
        }
        if (!$has_casing) {
            $block_reasons[] = 'GROUND_MISSING_CASING';
        }
        if (!$has_fat_handling) {
            $block_reasons[] = 'GROUND_MISSING_FAT_HANDLING';
        }
    }

    if ($type === 'WHOLE_CUT') {
        if (!$has_brine_or_cure) {
            $block_reasons[] = 'WHOLE_CUT_MISSING_CURE_OR_BRINE';
        }
        if ($has_casing && $score_ground > $score_whole) {
            $block_reasons[] = 'WHOLE_CUT_POSSIBLE_WRONG_CASING_MODEL';
        }
    }

    if ($type === 'THERMAL_PROCESSED') {
        if (!$has_thermal_process) {
            $block_reasons[] = 'THERMAL_MISSING_PROCESS';
        }
        if (!$has_thermal_params) {
            $block_reasons[] = 'THERMAL_MISSING_TEMP_OR_DURATION';
        }
    }

    if ($type === 'FISH_OR_SEAFOOD') {
        if (!$has_cold_chain) {
            $block_reasons[] = 'FISH_MISSING_COLD_CHAIN';
        }
        if (!$has_brine_or_cure) {
            $block_reasons[] = 'FISH_MISSING_SALTING_OR_BRINE';
        }
    }

    if (($has_smoking || $has_drying || $has_aging) && !$has_phase_time_or_params) {
        $block_reasons[] = 'PHASE_MISSING_TIME_OR_PARAMS';
    }

    if ($has_nitrite && !$has_nitrite_note) {
        $block_reasons[] = 'NITRITE_WITHOUT_SAFETY_NOTE';
    }

    if ($has_fallback_internal) {
        $block_reasons[] = 'PUBLIC_OR_META_INTERNAL_TEXT_HIT';
    }

    if ($has_problem_words && !$has_solution_words) {
        $block_reasons[] = 'PROBLEM_WITHOUT_SOLUTION_SIGNAL';
    }

    $public_blocked = count($block_reasons) > 0 ? 'FAIL' : 'PASS';

    $audit_status = 'TYPE_PASS_READY_FOR_SOURCE_DOSSIER';
    if ($type === 'NEEDS_CLASSIFICATION') {
        $audit_status = 'NEEDS_CLASSIFICATION';
    } elseif ($has_fallback_internal) {
        $audit_status = 'TYPE_PASS_PUBLIC_TEXT_BLOCKED';
    } elseif ($public_blocked === 'FAIL') {
        $audit_status = 'TYPE_PASS_MISSING_FIELDS';
    }

    if ($confidence === 'low' && $type !== 'NEEDS_CLASSIFICATION') {
        $audit_status = 'TYPE_CONFLICT_NEEDS_REVIEW';
    }

    $row = [
        'post_id' => $post_id,
        'title' => $title,
        'url' => $url,
        'post_status' => $status,
        'detected_type' => $type,
        'confidence' => $confidence,
        'score_ground' => $score_ground,
        'score_whole' => $score_whole,
        'score_thermal' => $score_thermal,
        'score_fish' => $score_fish,
        'has_granulation' => $has_granulation ? '1' : '0',
        'has_fat_handling' => $has_fat_handling ? '1' : '0',
        'has_casing' => $has_casing ? '1' : '0',
        'has_brine_or_cure' => $has_brine_or_cure ? '1' : '0',
        'has_thermal_process' => $has_thermal_process ? '1' : '0',
        'has_thermal_params' => $has_thermal_params ? '1' : '0',
        'has_cold_chain' => $has_cold_chain ? '1' : '0',
        'has_smoking' => $has_smoking ? '1' : '0',
        'has_drying' => $has_drying ? '1' : '0',
        'has_aging' => $has_aging ? '1' : '0',
        'has_phase_time_or_params' => $has_phase_time_or_params ? '1' : '0',
        'has_nitrite' => $has_nitrite ? '1' : '0',
        'has_nitrite_note' => $has_nitrite_note ? '1' : '0',
        'has_fallback_internal' => $has_fallback_internal ? '1' : '0',
        'has_problem_signal' => $has_problem_words ? '1' : '0',
        'has_solution_signal' => $has_solution_words ? '1' : '0',
        'public_fetch_status' => $public_fetch_status,
        'public_fetch_error' => $public_fetch_error,
        'audit_status' => $audit_status,
        'public_update_gate' => $public_blocked,
        'block_reasons' => implode(';', $block_reasons),
    ];

    $rows[] = $row;

    $summary['total']++;
    $summary['by_type'][$type] = ($summary['by_type'][$type] ?? 0) + 1;
    $summary['by_status'][$status] = ($summary['by_status'][$status] ?? 0) + 1;
    $summary['by_confidence'][$confidence] = ($summary['by_confidence'][$confidence] ?? 0) + 1;

    if ($status === 'publish') {
        $summary['published_total']++;
    }

    if ($public_blocked === 'FAIL') {
        $summary['public_blocked']++;
        if ($status === 'publish') {
            $summary['published_blocked']++;
        }
    }

    if ($has_fallback_internal) {
        $summary['fallback_internal_hits']++;
    }

    if ($has_nitrite && !$has_nitrite_note) {
        $summary['nitrite_without_note']++;
    }
}

$csv = fopen($csv_path, 'w');
if (!$csv) {
    fwrite(STDERR, "FAIL: Ne mogu otvoriti CSV za pisanje: $csv_path\n");
    exit(1);
}

$headers = [
    'post_id',
    'title',
    'url',
    'post_status',
    'detected_type',
    'confidence',
    'score_ground',
    'score_whole',
    'score_thermal',
    'score_fish',
    'has_granulation',
    'has_fat_handling',
    'has_casing',
    'has_brine_or_cure',
    'has_thermal_process',
    'has_thermal_params',
    'has_cold_chain',
    'has_smoking',
    'has_drying',
    'has_aging',
    'has_phase_time_or_params',
    'has_nitrite',
    'has_nitrite_note',
    'has_fallback_internal',
    'has_problem_signal',
    'has_solution_signal',
    'public_fetch_status',
    'public_fetch_error',
    'audit_status',
    'public_update_gate',
    'block_reasons',
];

fputcsv($csv, $headers);

foreach ($rows as $row) {
    $line = [];
    foreach ($headers as $header) {
        $line[] = $row[$header] ?? '';
    }
    fputcsv($csv, $line);
}
fclose($csv);

file_put_contents(
    $json_path,
    wp_json_encode([
        'generated_at' => date('c'),
        'summary' => $summary,
        'rows' => $rows,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

ksort($summary['by_type']);
ksort($summary['by_status']);
ksort($summary['by_confidence']);

$blocked_examples = array_values(array_filter($rows, function($row) {
    return ($row['public_update_gate'] ?? '') === 'FAIL';
}));

$ready_examples = array_values(array_filter($rows, function($row) {
    return ($row['public_update_gate'] ?? '') === 'PASS';
}));

$md = [];
$md[] = '# DRYCURED Recipe Type Router — read-only audit v1';
$md[] = '';
$md[] = 'Datum: ' . date('c');
$md[] = '';
$md[] = '## Važna napomena';
$md[] = '';
$md[] = 'Ovaj audit je read-only. Nije mijenjao WordPress postove, meta podatke, statuse, slugove, URL-ove ni renderer.';
$md[] = '';
$md[] = '## Sažetak';
$md[] = '';
$md[] = '- Ukupno dry_recipe zapisa: ' . $summary['total'];
$md[] = '- Objavljenih zapisa: ' . $summary['published_total'];
$md[] = '- Ukupno blokiranih za javno ažuriranje: ' . $summary['public_blocked'];
$md[] = '- Objavljenih blokiranih za javno ažuriranje: ' . $summary['published_blocked'];
$md[] = '- Interni/fallback tekst hitovi: ' . $summary['fallback_internal_hits'];
$md[] = '- Nitrit bez sigurnosne napomene: ' . $summary['nitrite_without_note'];
$md[] = '- Public fetch enabled: ' . $summary['public_fetch_enabled'];
$md[] = '- Public fetch errors: ' . $summary['public_fetch_errors'];
$md[] = '';
$md[] = '## Broj po tehnološkom tipu';
$md[] = '';
foreach ($summary['by_type'] as $type => $count) {
    $md[] = '- ' . $type . ': ' . $count;
}
$md[] = '';
$md[] = '## Broj po statusu objave';
$md[] = '';
foreach ($summary['by_status'] as $st => $count) {
    $md[] = '- ' . $st . ': ' . $count;
}
$md[] = '';
$md[] = '## Broj po confidence razini';
$md[] = '';
foreach ($summary['by_confidence'] as $cf => $count) {
    $md[] = '- ' . $cf . ': ' . $count;
}
$md[] = '';
$md[] = '## Prvih 40 blokiranih primjera';
$md[] = '';
$md[] = '| Post ID | Status | Tip | Confidence | Naslov | Razlog blokade |';
$md[] = '|---:|---|---|---|---|---|';
foreach (array_slice($blocked_examples, 0, 40) as $row) {
    $md[] = '| ' . $row['post_id'] . ' | ' . $row['post_status'] . ' | ' . $row['detected_type'] . ' | ' . $row['confidence'] . ' | ' . str_replace('|', '/', $row['title']) . ' | ' . str_replace('|', '/', $row['block_reasons']) . ' |';
}
$md[] = '';
$md[] = '## Prvih 40 kandidata bez blokade';
$md[] = '';
$md[] = '| Post ID | Status | Tip | Confidence | Naslov |';
$md[] = '|---:|---|---|---|---|';
foreach (array_slice($ready_examples, 0, 40) as $row) {
    $md[] = '| ' . $row['post_id'] . ' | ' . $row['post_status'] . ' | ' . $row['detected_type'] . ' | ' . $row['confidence'] . ' | ' . str_replace('|', '/', $row['title']) . ' |';
}
$md[] = '';
$md[] = '## Izlazne datoteke';
$md[] = '';
$md[] = '- CSV: `' . $csv_path . '`';
$md[] = '- JSON: `' . $json_path . '`';
$md[] = '- Summary: `' . $summary_path . '`';
$md[] = '';

file_put_contents($summary_path, implode("\n", $md));

echo "=== READ-ONLY AUDIT COMPLETE ===\n";
echo "TOTAL=" . $summary['total'] . "\n";
echo "PUBLISHED_TOTAL=" . $summary['published_total'] . "\n";
echo "PUBLIC_BLOCKED=" . $summary['public_blocked'] . "\n";
echo "PUBLISHED_BLOCKED=" . $summary['published_blocked'] . "\n";
echo "FALLBACK_INTERNAL_HITS=" . $summary['fallback_internal_hits'] . "\n";
echo "NITRITE_WITHOUT_NOTE=" . $summary['nitrite_without_note'] . "\n";
echo "PUBLIC_FETCH_ENABLED=" . $summary['public_fetch_enabled'] . "\n";
echo "PUBLIC_FETCH_ERRORS=" . $summary['public_fetch_errors'] . "\n";
echo "CSV=$csv_path\n";
echo "JSON=$json_path\n";
echo "SUMMARY=$summary_path\n";

echo "TYPE_COUNTS:\n";
foreach ($summary['by_type'] as $type => $count) {
    echo $type . "=" . $count . "\n";
}
