<?php
/**
 * Drycured pre-publish validacijski skript
 * Pokretanje: wp --allow-root --path=/var/www/html eval-file validate_post.php [-- --pid=XXXX]
 *
 * Provjere:
 *   V1. is_smoked konzistentnost — Dim bar, Dimljenje kartica
 *   V2. Omjer smjese — prikazuje se SAMO za kobasica family
 *   V3. Liquids — nema "ne dodaje" zajedno sa stvarnim stavkama
 *   V4. Naslovi — registry title == post_title (dijakritike)
 *   V5. Timeline — svi koraci imaju day/title/text ključeve
 *   V6. is_smoked mora biti eksplicitno postavljeno
 *
 * Izlaz: PASS / WARN / FAIL po provjeri, sažetak na kraju
 */

// Čita --pid= iz $argv ili provjerava sve published
$target_pid = null;
foreach ($argv ?? [] as $arg) {
    if (preg_match('/^--pid=(\d+)$/', $arg, $m)) {
        $target_pid = (int)$m[1];
    }
}

$reg = function_exists('dcv12_batch01_recipe_registry') ? dcv12_batch01_recipe_registry() : [];

if ($target_pid) {
    $pids = [$target_pid];
} else {
    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT pm.post_id FROM {$wpdb->postmeta} pm
         JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE pm.meta_key = '_dry_recipe_id'
         AND pm.meta_value LIKE 'HR-%'
         AND p.post_status = 'publish'",
        ARRAY_A
    );
    $pids = array_column($rows, 'post_id');
}

$total_fail = 0;
$total_warn = 0;
$total_pass = 0;

foreach ($pids as $pid) {
    $code = get_post_meta($pid, '_dry_recipe_id', true);
    $reg_entry = $reg[$code] ?? null;
    if (!$reg_entry) continue;

    $family  = $reg_entry['family'] ?? '';
    $reg_ttl = $reg_entry['title'] ?? '';
    $post_ttl = get_the_title($pid);

    $ov_raw = get_post_meta($pid, '_dry_recipe_overrides', true);
    $ov = $ov_raw ? json_decode($ov_raw, true) : [];

    $issues = [];  // ['FAIL'|'WARN'|'PASS', 'Vxx', 'message']

    // V1: is_smoked konzistentnost
    $is_smoked = isset($ov['is_smoked']) ? (bool)$ov['is_smoked'] : null;

    if ($is_smoked === null) {
        $issues[] = ['WARN', 'V6', 'is_smoked nije postavljeno — dodati eksplicitno u overrides'];
    }

    if ($is_smoked === false) {
        // Provjeri da Dim bar u finalnom profilu == 0
        $profile = dcv5_get_recipe_profile($pid, $code);
        $dim_score = null;
        foreach ($profile['profile'] ?? [] as $p) {
            if ($p['name'] === 'Dim') { $dim_score = $p['score']; break; }
        }
        if ($dim_score !== 0 && $dim_score !== null) {
            $issues[] = ['FAIL', 'V1', "is_smoked=false ali Dim score=$dim_score (mora biti 0)"];
        } else {
            $issues[] = ['PASS', 'V1', "Dim=0 uz is_smoked=false OK"];
        }

        // Provjeri da Dimljenje kartica nije "hladno dimljenje"
        foreach ($profile['quick'] ?? [] as $q) {
            if ($q['label'] === 'Dimljenje') {
                $val = strtolower($q['value'] ?? '');
                // "bez dimljenja" je ispravan izlaz za is_smoked=false — ne failati
                $is_wrong_smoke = (strpos($val, 'hladno') !== false || strpos($val, 'dim') !== false)
                                  && strpos($val, 'bez dimljenja') === false;
                if ($is_wrong_smoke) {
                    $issues[] = ['FAIL', 'V1', "is_smoked=false ali Dimljenje kartica='{$q['value']}'"];
                } else {
                    $issues[] = ['PASS', 'V1', "Dimljenje kartica OK: '{$q['value']}'"];
                }
            }
        }
    } elseif ($is_smoked === true) {
        $issues[] = ['PASS', 'V6', 'is_smoked=true postavljeno'];
    }

    // V2: Omjer smjese — samo kobasica
    if ($family !== 'kobasica') {
        $ov_mats = $ov['materials'] ?? [];
        $fat = 0; $total = 0;
        foreach ($ov_mats as $m) {
            $amt = str_replace(',', '.', $m['amount'] ?? '');
            if (preg_match('/(\d+(?:\.\d+)?)\s*kg/i', $amt, $mm)) {
                $kg = (float)$mm[1]; $total += $kg;
                $n = mb_strtolower($m['name'] ?? '', 'UTF-8');
                if (strpos($n, 'slanin') !== false || strpos($n, 'masno') !== false) $fat += $kg;
            }
        }
        if ($total > 0 && $fat > 0) {
            // Provjeri ima li guard — od sada bi trebao biti OK zbog family=kobasica guarda u template
            $issues[] = ['WARN', 'V2', "family=$family ima slanina/masno u materijalima ($fat/$total kg) — template guard sprječava prikaz"];
        } else {
            $issues[] = ['PASS', 'V2', "Omjer OK (family=$family, nema fat keyword)"];
        }
    } else {
        $issues[] = ['PASS', 'V2', "Omjer OK (family=kobasica, prikazuje se)"];
    }

    // V3: Liquids — nema ne-dodaje zajedno sa stvarnim stavkama
    $profile_v3 = dcv5_get_recipe_profile($pid, $code);
    $liq = $profile_v3['liquids'] ?? [];
    $real_liq = array_filter($liq, fn($l) => strpos(mb_strtolower($l['name'] ?? '', 'UTF-8'), 'ne dodaje') === false && strpos(mb_strtolower($l['name'] ?? '', 'UTF-8'), 'nije naveden') === false);
    $fake_liq = array_filter($liq, fn($l) => strpos(mb_strtolower($l['name'] ?? '', 'UTF-8'), 'ne dodaje') !== false || strpos(mb_strtolower($l['name'] ?? '', 'UTF-8'), 'nije naveden') !== false);
    if (!empty($real_liq) && !empty($fake_liq)) {
        $issues[] = ['FAIL', 'V3', 'Liquids kontradikcija: stvarne stavke + ne-dodaje redovi zajedno'];
    } else {
        $issues[] = ['PASS', 'V3', 'Liquids konzistentni'];
    }

    // V4: Naslovi — registry title vs post_title (ASCII nedostajuća dijakritika)
    // Jednostavan check: trebaju biti isti
    if ($reg_ttl !== $post_ttl) {
        // Ako se razlikuju samo u dijakritikama (registry nema, post ima) — FAIL
        $reg_ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $reg_ttl);
        $post_ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $post_ttl);
        if ($reg_ascii === $post_ascii && $reg_ttl !== $post_ttl) {
            $issues[] = ['FAIL', 'V4', "Registry title '{$reg_ttl}' != post_title '{$post_ttl}' (dijakritike)"];
        } elseif ($reg_ttl !== $post_ttl) {
            $issues[] = ['WARN', 'V4', "Registry title '{$reg_ttl}' != post_title '{$post_ttl}'"];
        }
    } else {
        $issues[] = ['PASS', 'V4', "Naslov OK: '{$reg_ttl}'"];
    }

    // V5: Timeline ključevi
    $timeline = $ov['timeline'] ?? [];
    $bad_steps = [];
    foreach ($timeline as $i => $step) {
        if (!isset($step['day']) || !isset($step['title']) || !isset($step['text'])) {
            $bad_steps[] = "korak $i: " . implode(',', array_keys($step));
        }
    }
    if ($bad_steps) {
        $issues[] = ['FAIL', 'V5', 'Timeline koraci imaju krive ključeve: ' . implode('; ', $bad_steps)];
    } elseif (!empty($timeline)) {
        $issues[] = ['PASS', 'V5', 'Timeline ključevi OK (' . count($timeline) . ' koraka)'];
    } else {
        $issues[] = ['WARN', 'V5', 'Nema timeline u overrides (koristi family default)'];
    }

    // ── Ispis za ovaj post ──
    $post_fails = count(array_filter($issues, fn($i) => $i[0] === 'FAIL'));
    $post_warns = count(array_filter($issues, fn($i) => $i[0] === 'WARN'));
    $post_status = $post_fails > 0 ? 'FAIL' : ($post_warns > 0 ? 'WARN' : 'PASS');

    $total_fail += $post_fails;
    $total_warn += $post_warns;
    if ($post_fails === 0 && $post_warns === 0) $total_pass++;

    $marker = $post_status === 'FAIL' ? '✗' : ($post_status === 'WARN' ? '△' : '✓');
    echo "\n[$post_status] {$marker} {$code} — {$post_ttl} (pid={$pid}, family={$family})\n";
    foreach ($issues as [$lvl, $vcode, $msg]) {
        if ($lvl === 'PASS') continue;  // ne prikazuj PASS detalje
        $sym = $lvl === 'FAIL' ? '  ✗' : '  △';
        echo "  {$sym} [{$vcode}] {$msg}\n";
    }
}

echo "\n════════════════════════════════\n";
echo "SAŽETAK: FAIL={$total_fail} WARN={$total_warn} clean_posts={$total_pass}\n";
if ($total_fail === 0 && $total_warn === 0) {
    echo "SVE PROVJERE PROŠLE ✓\n";
} elseif ($total_fail === 0) {
    echo "Nema kritičnih grešaka, ima upozorenja △\n";
} else {
    echo "IMA KRITIČNIH GREŠAKA — fix prije objave! ✗\n";
}
