<?php
/**
 * Plugin Name: Drycured Process Hub
 * Description: Read-only registry for Drycured process pages, tools, images and navigation metadata.
 * Version: 0.1.0
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Returns the canonical process registry.
 *
 * This function must remain read-only.
 * It must not modify posts, menus, options, shortcodes, page content or frontend output.
 */
function drycured_process_hub_get_processes(): array {
    $processes = [
        'sirovina' => [
            'order' => 1,
            'title' => 'Sirovina',
            'url' => home_url('/proces-izrade/sirovina/'),
            'image' => content_url('/uploads/drycured/procesi/sirovina/sirovina-hero-v01.jpg'),
            'tool' => null,
            'prev' => null,
            'next' => 'rezanje',
            'status' => 'active',
            'summary' => 'Odabir i procjena sirovine prije početka proizvodnje.',
        ],
        'rezanje' => [
            'order' => 2,
            'title' => 'Rezanje',
            'url' => home_url('/proces-izrade/rezanje/'),
            'image' => content_url('/uploads/drycured/procesi/rezanje/rezanje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'sirovina',
            'next' => 'soljenje',
            'status' => 'active',
            'summary' => 'Priprema komada mesa pravilnim rezanjem prije soljenja i daljnje obrade.',
        ],
        'soljenje' => [
            'order' => 3,
            'title' => 'Soljenje',
            'url' => home_url('/proces-izrade/soljenje/'),
            'image' => content_url('/uploads/drycured/procesi/soljenje/soljenje-hero-v01.jpg'),
            'tool' => [
                'label' => 'Kalkulator soli',
                'url' => home_url('/kalkulator-soli/'),
                'option' => null,
            ],
            'prev' => 'rezanje',
            'next' => 'mljevenje',
            'status' => 'active',
            'summary' => 'Kontrola soli, začina i vremena soljenja.',
        ],
        'mljevenje' => [
            'order' => 4,
            'title' => 'Mljevenje',
            'url' => home_url('/proces-izrade/mljevenje/'),
            'image' => content_url('/uploads/drycured/procesi/mljevenje/mljevenje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'soljenje',
            'next' => 'mijesanje',
            'status' => 'active',
            'summary' => 'Kontrola granulacije, temperature i rizika razmazivanja masti.',
        ],
        'mijesanje' => [
            'order' => 5,
            'title' => 'Miješanje',
            'url' => home_url('/proces-izrade/mijesanje/'),
            'image' => content_url('/uploads/drycured/procesi/mijesanje/mijesanje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'mljevenje',
            'next' => 'odlezavanje-smjese',
            'status' => 'active',
            'summary' => 'Razvoj vezivnosti, ravnomjerne raspodjele začina i pravilne strukture smjese.',
        ],
        'odlezavanje-smjese' => [
            'order' => 6,
            'title' => 'Odležavanje smjese',
            'url' => home_url('/proces-izrade/odlezavanje-smjese/'),
            'image' => content_url('/uploads/drycured/procesi/odlezavanje/odlezavanje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'mijesanje',
            'next' => 'punjenje',
            'status' => 'active',
            'summary' => 'Mirna faza stabilizacije smjese prije punjenja.',
        ],
        'punjenje' => [
            'order' => 7,
            'title' => 'Punjenje',
            'url' => home_url('/proces-izrade/punjenje/'),
            'image' => content_url('/uploads/drycured/procesi/punjenje/punjenje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'odlezavanje-smjese',
            'next' => 'fermentacija',
            'status' => 'active',
            'summary' => 'Kontrola pritiska punjenja, zraka, crijeva i oblika proizvoda.',
        ],
        'fermentacija' => [
            'order' => 8,
            'title' => 'Fermentacija',
            'url' => home_url('/proces-izrade/fermentacija/'),
            'image' => '',
            'tool' => [
                'label' => 'Praćenje pH',
                'url' => home_url('/pracenje-ph/'),
                'option' => null,
            ],
            'prev' => 'punjenje',
            'next' => 'dimljenje',
            'status' => 'active',
            'summary' => 'Kontrola pH, temperature, vlage i sigurnog razvoja fermentacije.',
        ],
        'dimljenje' => [
            'order' => 9,
            'title' => 'Dimljenje',
            'url' => home_url('/proces-izrade/dimljenje/'),
            'image' => content_url('/uploads/drycured/procesi/dimljenje/dimljenje-hero-v01.jpg'),
            'tool' => [
                'label' => 'Planer dimljenja',
                'url' => drycured_process_hub_get_option_url('drycured_smoking_planner_url', home_url('/planer-dimljenja/')),
                'option' => 'drycured_smoking_planner_url',
            ],
            'prev' => 'fermentacija',
            'next' => 'susenje',
            'status' => 'active',
            'summary' => 'Kontrola dima, temperature, odmora i rizika gorkog ili teškog dima.',
        ],
        'susenje' => [
            'order' => 10,
            'title' => 'Sušenje',
            'url' => home_url('/proces-izrade/susenje/'),
            'image' => content_url('/uploads/drycured/procesi/susenje/susenje-hero-v01.jpg'),
            'tool' => [
                'label' => 'Kalkulator sušenja',
                'url' => drycured_process_hub_get_option_url('drycured_drying_calculator_url', home_url('/kalkulator-susenja/')),
                'option' => 'drycured_drying_calculator_url',
            ],
            'prev' => 'dimljenje',
            'next' => 'zrenje',
            'status' => 'active',
            'summary' => 'Praćenje gubitka mase, površinske kore, vlage i tempa sušenja.',
        ],
        'zrenje' => [
            'order' => 11,
            'title' => 'Zrenje',
            'url' => home_url('/proces-izrade/zrenje/'),
            'image' => content_url('/uploads/drycured/procesi/zrenje/zrenje-hero-v02.jpg'),
            'tool' => null,
            'prev' => 'susenje',
            'next' => 'pakiranje',
            'status' => 'active',
            'summary' => 'Završno izjednačavanje arome, vlage, površine i teksture.',
        ],
        'pakiranje' => [
            'order' => 12,
            'title' => 'Pakiranje',
            'url' => home_url('/proces-izrade/pakiranje/'),
            'image' => content_url('/uploads/drycured/procesi/pakiranje/pakiranje-hero-v01.jpg'),
            'tool' => null,
            'prev' => 'zrenje',
            'next' => null,
            'status' => 'active',
            'summary' => 'Završna zaštita proizvoda, čuvanje, označavanje i kontrola ambalaže.',
        ],
    ];

    uasort($processes, static function ($a, $b) {
        return (int) $a['order'] <=> (int) $b['order'];
    });

    return $processes;
}

function drycured_process_hub_get_option_url(string $option_name, string $fallback): string {
    $value = trim((string) get_option($option_name, ''));

    if ($value !== '') {
        return esc_url_raw($value);
    }

    return esc_url_raw($fallback);
}

function drycured_process_hub_get_process(string $slug): ?array {
    $processes = drycured_process_hub_get_processes();
    return $processes[$slug] ?? null;
}

function drycured_process_hub_get_prev_next(string $slug): array {
    $process = drycured_process_hub_get_process($slug);

    if (!$process) {
        return [
            'prev' => null,
            'next' => null,
        ];
    }

    return [
        'prev' => $process['prev'] ? drycured_process_hub_get_process((string) $process['prev']) : null,
        'next' => $process['next'] ? drycured_process_hub_get_process((string) $process['next']) : null,
    ];
}

function drycured_process_hub_get_tool(string $slug): ?array {
    $process = drycured_process_hub_get_process($slug);

    if (!$process || empty($process['tool']) || !is_array($process['tool'])) {
        return null;
    }

    return $process['tool'];
}

/**
 * Admin-only debug shortcode.
 *
 * It renders only if:
 * - user can manage_options
 * - query parameter drycured_debug_process_hub=1 is present
 *
 * This keeps the registry invisible on the public frontend.
 */
function drycured_process_hub_debug_shortcode(): string {
    if (!current_user_can('manage_options')) {
        return '';
    }

    if (($_GET['drycured_debug_process_hub'] ?? '') !== '1') {
        return '';
    }

    $processes = drycured_process_hub_get_processes();

    ob_start();
    ?>
    <div class="drycured-process-hub-debug" style="padding:20px;border:2px solid #101722;background:#fff;margin:20px 0;font-family:system-ui,sans-serif;">
        <h2>Drycured Process Hub Debug</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;border-bottom:1px solid #ccc;padding:8px;">Red</th>
                    <th style="text-align:left;border-bottom:1px solid #ccc;padding:8px;">Proces</th>
                    <th style="text-align:left;border-bottom:1px solid #ccc;padding:8px;">URL</th>
                    <th style="text-align:left;border-bottom:1px solid #ccc;padding:8px;">Alat</th>
                    <th style="text-align:left;border-bottom:1px solid #ccc;padding:8px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processes as $slug => $process): ?>
                    <tr>
                        <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo esc_html((string) $process['order']); ?></td>
                        <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo esc_html($process['title']); ?> <small>(<?php echo esc_html($slug); ?>)</small></td>
                        <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo esc_url($process['url']); ?></td>
                        <td style="border-bottom:1px solid #eee;padding:8px;">
                            <?php
                            if (!empty($process['tool']['label'])) {
                                echo esc_html($process['tool']['label']) . ' — ' . esc_url($process['tool']['url']);
                            } else {
                                echo '—';
                            }
                            ?>
                        </td>
                        <td style="border-bottom:1px solid #eee;padding:8px;"><?php echo esc_html($process['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php

    return (string) ob_get_clean();
}

add_shortcode('drycured_process_hub_debug', 'drycured_process_hub_debug_shortcode');
