<?php
/**
 * Plugin Name: Drycured Home Process Rail Hub Adapter
 * Description: Disabled-by-default adapter for comparing the existing home process rail with the Drycured Process Hub registry. Does not alter frontend rendering.
 * Version: 0.1.5
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Adapter enable flag.
 *
 * v0.1.5 is intentionally non-rendering. Even with the option enabled,
 * this version does not replace the public home process rail.
 */
function drycured_home_rail_hub_adapter_v015_use_hub(): bool {
    return ((string) get_option('drycured_home_process_rail_use_hub', '0')) === '1';
}

/**
 * Returns Process Hub items in a compact shape for comparison.
 */
function drycured_home_rail_hub_adapter_v015_get_hub_items(): array {
    if (!function_exists('drycured_process_hub_get_processes')) {
        return [];
    }

    $processes = drycured_process_hub_get_processes();
    $items = [];

    foreach ($processes as $slug => $process) {
        $items[] = [
            'order' => (int) ($process['order'] ?? 0),
            'slug' => (string) $slug,
            'title' => (string) ($process['title'] ?? ''),
            'url' => (string) ($process['url'] ?? ''),
            'path' => (string) parse_url((string) ($process['url'] ?? ''), PHP_URL_PATH),
            'image' => (string) ($process['image'] ?? ''),
            'tool_label' => !empty($process['tool']['label']) ? (string) $process['tool']['label'] : '',
            'tool_url' => !empty($process['tool']['url']) ? (string) $process['tool']['url'] : '',
            'prev' => (string) ($process['prev'] ?? ''),
            'next' => (string) ($process['next'] ?? ''),
            'status' => (string) ($process['status'] ?? ''),
        ];
    }

    usort($items, static function ($a, $b) {
        return (int) $a['order'] <=> (int) $b['order'];
    });

    return $items;
}

/**
 * Compares the existing home HTML with Process Hub paths.
 *
 * This function only reads the home page. It does not edit anything.
 */
function drycured_home_rail_hub_adapter_v015_compare_home(?string $html = null): array {
    $items = drycured_home_rail_hub_adapter_v015_get_hub_items();

    if ($html === null) {
        $response = wp_remote_get(home_url('/'), [
            'timeout' => 20,
            'redirection' => 5,
        ]);

        if (is_wp_error($response)) {
            return [
                'ok' => false,
                'error' => $response->get_error_message(),
                'items' => [],
                'missing' => [],
                'marker_count' => 0,
            ];
        }

        $html = (string) wp_remote_retrieve_body($response);
    }

    $marker_count = substr_count($html, 'dc-process-rail');
    $missing = [];
    $checked = [];

    foreach ($items as $item) {
        $path = (string) ($item['path'] ?? '');
        $count = $path !== '' ? substr_count($html, $path) : 0;

        $row = $item;
        $row['home_count'] = $count;
        $row['home_found'] = $count > 0;

        if (!$row['home_found']) {
            $missing[] = $item['slug'];
        }

        $checked[] = $row;
    }

    return [
        'ok' => count($missing) === 0 && count($items) === 12,
        'error' => '',
        'items' => $checked,
        'missing' => $missing,
        'marker_count' => $marker_count,
        'process_count' => count($items),
    ];
}

/**
 * Admin-only menu page.
 *
 * This does not affect public frontend rendering.
 */
function drycured_home_rail_hub_adapter_v015_admin_menu(): void {
    add_management_page(
        'Drycured Rail Adapter',
        'Drycured Rail Adapter',
        'manage_options',
        'drycured-home-rail-hub-adapter',
        'drycured_home_rail_hub_adapter_v015_admin_page'
    );
}
add_action('admin_menu', 'drycured_home_rail_hub_adapter_v015_admin_menu');

function drycured_home_rail_hub_adapter_v015_admin_page(): void {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Nemate dopuštenje za pregled ove stranice.', 'drycured'));
    }

    $use_hub = drycured_home_rail_hub_adapter_v015_use_hub();
    $compare = drycured_home_rail_hub_adapter_v015_compare_home();

    ?>
    <div class="wrap">
        <h1>Drycured Home Rail Hub Adapter</h1>

        <p>
            Ovo je administratorski pregled adaptera između postojećeg home process raila i Process Hub registra.
            Verzija v0.1.5 ne mijenja javni prikaz.
        </p>

        <div style="margin:16px 0;padding:14px 16px;border-left:4px solid <?php echo $use_hub ? '#d63638' : '#2271b1'; ?>;background:#fff;">
            <strong>Opcija drycured_home_process_rail_use_hub:</strong>
            <?php echo $use_hub ? '1 — uključeno u opciji, ali v0.1.5 još ne preuzima frontend render' : '0 — isključeno'; ?>
        </div>

        <div style="margin:16px 0;padding:14px 16px;border-left:4px solid <?php echo !empty($compare['ok']) ? '#00a32a' : '#d63638'; ?>;background:#fff;">
            <strong>Usporedba home vodiča:</strong>
            <?php echo !empty($compare['ok']) ? 'PASS — svi procesni URL-ovi iz Huba pronađeni su na home stranici.' : 'WARNING — postoje odstupanja.'; ?>
            <br>
            <strong>dc-process-rail markeri:</strong>
            <?php echo esc_html((string) ($compare['marker_count'] ?? 0)); ?>
        </div>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Red</th>
                    <th>Slug</th>
                    <th>Proces</th>
                    <th>Hub URL</th>
                    <th>Nađen na home</th>
                    <th>Broj pojavljivanja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($compare['items'] ?? []) as $item): ?>
                    <tr>
                        <td><?php echo esc_html((string) ($item['order'] ?? '')); ?></td>
                        <td><code><?php echo esc_html((string) ($item['slug'] ?? '')); ?></code></td>
                        <td><?php echo esc_html((string) ($item['title'] ?? '')); ?></td>
                        <td>
                            <?php if (!empty($item['url'])): ?>
                                <a href="<?php echo esc_url((string) $item['url']); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html((string) $item['path']); ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td><?php echo !empty($item['home_found']) ? 'DA' : 'NE'; ?></td>
                        <td><?php echo esc_html((string) ($item['home_count'] ?? 0)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2 style="margin-top:28px;">Sigurnosna pravila</h2>
        <ul style="list-style:disc;margin-left:22px;">
            <li>Adapter v0.1.5 ne preuzima javni prikaz home vodiča.</li>
            <li>Adapter ne mijenja Elementor sadržaj.</li>
            <li>Adapter ne mijenja meni, alate ni procesne stranice.</li>
            <li>Opcija za buduće uključivanje ostaje postavljena na 0.</li>
        </ul>
    </div>
    <?php
}
