<?php
/**
 * Plugin Name: Drycured Registry Extension 01-B
 * Description: Proširuje dcv12_batch01_recipe_registry s receptima klastera 01-B
 *              (Istarsko-Dalmatinski). Bez diranja source-a recipe-view-v1.php.
 *              Wraps original funkciju kroz function_exists check.
 * Version: 1.0
 */

if (!defined('ABSPATH')) { exit; }

// Naša registracija recepata klastera 01-B
function drycured_01B_registry_entries() {
    return [
        'HR-IS-001' => ['order' => 101, 'title' => 'Istarska kobasica', 'family' => 'kobasica', 'slug' => 'hr-is-001-istarska-kobasica', 'region' => 'Istra'],
        'HR-IS-002' => ['order' => 102, 'title' => 'Rovinjska kobasica', 'family' => 'kobasica', 'slug' => 'hr-is-002-rovinjska-kobasica', 'region' => 'Istra'],
        'HR-IS-003' => ['order' => 103, 'title' => 'Pazinska kobasica', 'family' => 'kobasica', 'slug' => 'hr-is-003-pazinska-kobasica', 'region' => 'Istra'],
        'HR-IS-004' => ['order' => 104, 'title' => 'Žlomprt (istarski ombolo)', 'family' => 'cijeli_komad', 'slug' => 'hr-is-004-zlomprt-istarski-ombolo', 'region' => 'Istra'],
        'HR-MM-001' => ['order' => 201, 'title' => "Meso 'z tiblice", 'family' => 'cijeli_komad', 'slug' => 'hr-mm-001-meso-z-tiblice', 'region' => 'Međimurje'],
        'HR-LI-001' => ['order' => 110, 'title' => 'Lička kobasica', 'family' => 'kobasica', 'slug' => 'hr-li-001-licka-kobasica', 'region' => 'Lika'],
        'HR-DA-001' => ['order' => 120, 'title' => 'Sinjska kobasica', 'family' => 'kobasica', 'slug' => 'hr-da-001-sinjska-kobasica', 'region' => 'Dalmacija (Cetinska krajina)'],
        'HR-DA-002' => ['order' => 121, 'title' => 'Korčulanska kobasica', 'family' => 'kobasica', 'slug' => 'hr-da-002-korculanska-kobasica', 'region' => 'Dalmacija (otok Korčula)'],
        'HR-DA-003' => ['order' => 122, 'title' => 'Hvarska prstena kobasica', 'family' => 'kobasica', 'slug' => 'hr-da-003-hvarska-prstena-kobasica', 'region' => 'Dalmacija (otok Hvar)'],
        'HR-DA-004' => ['order' => 123, 'title' => 'Vrgorački kulen', 'family' => 'kulen', 'slug' => 'hr-da-004-vrgoracki-kulen', 'region' => 'Dalmacija (Vrgorac, Zagora)'],
    ];
}

// Pristup 1: Hookatii na dcv5_supported_recipe_codes filter (adapter v1.2 patten)
add_filter('dcv5_supported_recipe_codes', function($codes) {
    if (!is_array($codes)) $codes = [];
    foreach (array_keys(drycured_01B_registry_entries()) as $code) {
        if (!in_array($code, $codes, true)) $codes[] = $code;
    }
    return $codes;
}, 100, 1);

// Pristup 2: Wrap dcv12_batch01_recipe_registry — definira našu verziju PRIJE
// nego što recipe-view-v1.php dođe do svoje fallback definicije (function_exists guard).
// Ovaj plugin učitava se prije mu-plugins glavnog jer ima alphabetic prioritet:
// "drycured-registry-01B.php" < "drycured-recipe-view-v1.php"
if (!function_exists('dcv12_batch01_recipe_registry')) {
    function dcv12_batch01_recipe_registry() {
        // Originalna 01-A registry definicija (kopirana iz recipe-view-v1.php linija 4622-4658)
        $original = [
            'HR-SL-001' => ['order' => 1, 'title' => 'Slavonski kulen', 'family' => 'kulen', 'slug' => 'hr-sl-001-slavonski-kulen-pdo-eu', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-002' => ['order' => 2, 'title' => 'Kulenova seka', 'family' => 'kulen', 'slug' => 'hr-sl-002-kulenova-seka', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-003' => ['order' => 3, 'title' => 'Baranjski kulen', 'family' => 'kulen', 'slug' => 'hr-sl-003-baranjski-kulen-zozp-eu', 'region' => 'Baranja'],
            'HR-SL-004' => ['order' => 4, 'title' => 'Đakovački kulen', 'family' => 'kulen', 'slug' => 'hr-sl-004-dakovacki-kulen', 'region' => 'Đakovština'],
            'HR-SL-005' => ['order' => 5, 'title' => 'Slavonska domaća kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-005-slavonska-domaca-kobasica', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-006' => ['order' => 6, 'title' => 'Srijemska kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-006-srijemska-kobasica-ogp', 'region' => 'Srijem'],
            'HR-SL-007' => ['order' => 7, 'title' => 'Ratarske kobasice', 'family' => 'kobasica', 'slug' => 'hr-sl-007-ratarske-kobasice', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-008' => ['order' => 8, 'title' => 'Baranjska kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-008-baranjska-kobasica', 'region' => 'Baranja'],
            'HR-SL-009' => ['order' => 9, 'title' => 'Baranjska salama', 'family' => 'salama', 'slug' => 'hr-sl-009-baranjska-salama', 'region' => 'Baranja'],
            'HR-SL-010' => ['order' => 10, 'title' => 'Slavonska kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-010-slavonska-kobasica-zoi-eu-2023', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-012' => ['order' => 11, 'title' => 'Slavonska dimljena slanina', 'family' => 'slanina_panceta', 'slug' => 'hr-sl-012-slavonska-dimljena-slanina', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-014' => ['order' => 12, 'title' => 'Slavonska dimljena šunka', 'family' => 'sunka', 'slug' => 'hr-sl-014-slavonska-dimljena-sunka', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-015' => ['order' => 13, 'title' => 'Suha plećka slavonska', 'family' => 'cijeli_komad', 'slug' => 'hr-sl-015-suha-plecka-slavonska', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-016' => ['order' => 14, 'title' => 'Suha rebra slavonska', 'family' => 'cijeli_komad', 'slug' => 'hr-sl-016-suha-rebra-slavonska', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-017' => ['order' => 15, 'title' => 'Slavonska jetrena kobasica', 'family' => 'iznutrice', 'slug' => 'hr-sl-017-slavonska-jetrena-kobasica', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-018' => ['order' => 16, 'title' => 'Krvavica slavonska', 'family' => 'krvavica', 'slug' => 'hr-sl-018-krvavica-slavonska', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-019' => ['order' => 17, 'title' => 'Tlačenica slavonska (švargl)', 'family' => 'tlacenica_svargl', 'slug' => 'hr-sl-019-tlacenica-slavonska-svargl', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-020' => ['order' => 18, 'title' => 'Vinkovačka šunka — suho soljena varijanta', 'family' => 'sunka', 'slug' => 'hr-sl-020-vinkovacka-sunka-suho-soljena-varijanta', 'region' => 'Vinkovci i Srijem'],
            'HR-SL-021' => ['order' => 19, 'title' => 'Slavonski lovački kulen (divljač + svinjetina)', 'family' => 'kulen', 'slug' => 'hr-sl-021-slavonski-lovacki-kulen-divljac-svinjetina', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-022' => ['order' => 20, 'title' => 'Konjska salama Ploštine (talijanska manjina, Pakrac)', 'family' => 'salama', 'slug' => 'hr-sl-022-konjska-salama-plostine-talijanska-manjina-pakrac', 'region' => 'Pakrac i Ploštine'],
            'HR-SL-024' => ['order' => 21, 'title' => 'Slavonski dimljeni svinjski vrat', 'family' => 'cijeli_komad', 'slug' => 'hr-sl-024-slavonski-dimljeni-svinjski-vrat', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-027' => ['order' => 22, 'title' => 'Slavonski suhi vrat (ovratina)', 'family' => 'cijeli_komad', 'slug' => 'hr-sl-027-slavonski-suhi-vrat-ovratina', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-028' => ['order' => 23, 'title' => 'Baranjska šunka', 'family' => 'sunka', 'slug' => 'hr-sl-028-baranjska-sunka', 'region' => 'Baranja'],
            'HR-SL-029' => ['order' => 24, 'title' => 'Đakovačka šunka', 'family' => 'sunka', 'slug' => 'hr-sl-029-dakovacka-sunka', 'region' => 'Đakovština'],
            'HR-SL-030' => ['order' => 25, 'title' => 'Brodska kobasica (Slavonski Brod)', 'family' => 'kobasica', 'slug' => 'hr-sl-030-brodska-kobasica-slavonski-brod', 'region' => 'Slavonski Brod'],
            'HR-SL-031' => ['order' => 26, 'title' => 'Osječka kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-031-osjecka-kobasica', 'region' => 'Osijek'],
            'HR-SL-032' => ['order' => 27, 'title' => 'Slavonska krvavica od prosa (bijela)', 'family' => 'krvavica', 'slug' => 'hr-sl-032-slavonska-krvavica-od-prosa-bijela', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-033' => ['order' => 28, 'title' => 'Slavonska šunka od crne slavonske svinje', 'family' => 'sunka', 'slug' => 'hr-sl-033-slavonska-sunka-od-crne-slavonske-svinje', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-034' => ['order' => 29, 'title' => 'Slavonska domaća salama', 'family' => 'salama', 'slug' => 'hr-sl-034-slavonska-domaca-salama', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-036' => ['order' => 30, 'title' => 'Slavonska čvarkovača (kobasica od čvaraka)', 'family' => 'kobasica', 'slug' => 'hr-sl-036-slavonska-cvarkovaca-kobasica-od-cvaraka', 'region' => 'Slavonija, Baranja i Srijem'],
            'HR-SL-037' => ['order' => 31, 'title' => 'Vinkovačka kobasica', 'family' => 'kobasica', 'slug' => 'hr-sl-037-vinkovacka-kobasica', 'region' => 'Vinkovci i Srijem'],
            'HR-SL-038' => ['order' => 32, 'title' => 'Slavonska lovačka kobasica (divljač + svinjetina)', 'family' => 'kobasica', 'slug' => 'hr-sl-038-slavonska-lovacka-kobasica-divljac-svinjetina', 'region' => 'Slavonija, Baranja i Srijem'],
        ];
        return array_merge($original, drycured_01B_registry_entries());
    }
}


/**
 * Dynamic navigation registry: generates nav entries for ALL published dry_recipe posts
 * that are NOT already in the static registry (dcv12_batch01_recipe_registry).
 * Used exclusively by dcv62_recipe_nav_registry() — does NOT affect ingredient/profile
 * rendering which relies on dcv12_batch01_recipe_registry() separately.
 *
 * Results cached in WP object cache for 5 minutes to avoid per-request DB queries.
 */
function drycured_dynamic_nav_entries() {
    $cache_key = 'drycured_dynamic_nav_entries_v1';
    $cached = wp_cache_get($cache_key, 'drycured_nav');
    if ($cached !== false) {
        return $cached;
    }

    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT p.ID, p.post_name, p.post_title, pm.meta_value AS code
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} pm
             ON pm.post_id = p.ID AND pm.meta_key = '_dry_recipe_id'
         WHERE p.post_status = 'publish'
           AND p.post_type = 'dry_recipe'
         ORDER BY p.ID ASC"
    );

    $entries = [];
    $order   = 1000;
    foreach ($rows as $row) {
        if (empty($row->code)) {
            continue;
        }
        $entries[ $row->code ] = [
            'order'  => $order++,
            'title'  => $row->post_title,
            'slug'   => $row->post_name,
            'family' => 'world',
            'region' => '',
        ];
    }

    wp_cache_set($cache_key, $entries, 'drycured_nav', 300);
    return $entries;
}

