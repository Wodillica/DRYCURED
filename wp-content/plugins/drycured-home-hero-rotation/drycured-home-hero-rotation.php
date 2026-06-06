<?php
/**
 * Plugin Name: Drycured Home Hero Rotation
 * Description: Dnevna rotacija glavne fotografske hero slike na početnoj stranici drycured.com.
 * Version: 1.0.0
 * Author: Drycured
 */

defined('ABSPATH') || exit;

const DCHHR_VERSION              = '1.0.0';
const DCHHR_FRONT_PAGE_ID        = 101;
const DCHHR_ELEMENTOR_TARGET     = 'doydxyp';
const DCHHR_OPTION_ENABLED       = 'drycured_home_hero_rotation_enabled';
const DCHHR_OPTION_CYCLE         = 'drycured_home_hero_rotation_cycle';
const DCHHR_OPTION_PREVIEW_TOKEN = 'drycured_home_hero_rotation_preview_token';
const DCHHR_MAX_SLOTS            = 14;
const DCHHR_TIMEZONE             = 'Europe/Zagreb';


/**
 * Vraća mapu i javni URL rotacijskih slika.
 */
function dchhr_storage(): array {
    $uploads = wp_upload_dir();

    return [
        'dir' => trailingslashit($uploads['basedir']) . 'drycured/home-hero',
        'url' => trailingslashit($uploads['baseurl']) . 'drycured/home-hero',
    ];
}


/**
 * Osigurava osnovne postavke i mapu.
 */
function dchhr_ensure_defaults(): void {
    $storage = dchhr_storage();

    if (!is_dir($storage['dir'])) {
        wp_mkdir_p($storage['dir']);
    }

    if (get_option(DCHHR_OPTION_ENABLED, null) === null) {
        add_option(DCHHR_OPTION_ENABLED, 0, '', false);
    }

    if (get_option(DCHHR_OPTION_CYCLE, null) === null) {
        add_option(DCHHR_OPTION_CYCLE, 14, '', false);
    }

    if (!get_option(DCHHR_OPTION_PREVIEW_TOKEN)) {
        add_option(
            DCHHR_OPTION_PREVIEW_TOKEN,
            wp_generate_password(32, false, false),
            '',
            false
        );
    }
}

register_activation_hook(__FILE__, 'dchhr_ensure_defaults');
add_action('plugins_loaded', 'dchhr_ensure_defaults');


/**
 * Ciklus može biti 7, 8 ili 14 dana.
 */
function dchhr_cycle_days(): int {
    $cycle = (int) get_option(DCHHR_OPTION_CYCLE, 14);

    return in_array($cycle, [7, 8, 14], true) ? $cycle : 14;
}


/**
 * Pronalazi datoteku za određeni slot.
 *
 * Podržani nazivi:
 * hero-01.webp
 * hero-01.jpg
 * hero-01.jpeg
 * hero-01.png
 */
function dchhr_slot_data(int $slot): array {
    $storage = dchhr_storage();
    $base    = sprintf('hero-%02d', $slot);

    foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
        $filename = $base . '.' . $extension;
        $path     = trailingslashit($storage['dir']) . $filename;

        if (!is_file($path)) {
            continue;
        }

        $url = trailingslashit($storage['url']) . rawurlencode($filename);

        return [
            'slot'     => $slot,
            'exists'   => true,
            'filename' => $filename,
            'path'     => $path,
            'url'      => add_query_arg('v', (string) filemtime($path), $url),
            'size'     => filesize($path),
        ];
    }

    return [
        'slot'     => $slot,
        'exists'   => false,
        'filename' => '',
        'path'     => '',
        'url'      => '',
        'size'     => 0,
    ];
}


/**
 * Lista svih 14 slotova bez pomicanja redoslijeda kada slika nedostaje.
 */
function dchhr_all_slots(): array {
    $slots = [];

    for ($slot = 1; $slot <= DCHHR_MAX_SLOTS; $slot++) {
        $slots[] = dchhr_slot_data($slot);
    }

    return $slots;
}


/**
 * Provjerava valjani privremeni preview parametar.
 */
function dchhr_preview_day(): int {
    $provided_token = isset($_GET['dc_hero_token'])
        ? sanitize_text_field(wp_unslash($_GET['dc_hero_token']))
        : '';

    $stored_token = (string) get_option(DCHHR_OPTION_PREVIEW_TOKEN, '');

    if (
        $provided_token === ''
        || $stored_token === ''
        || !hash_equals($stored_token, $provided_token)
    ) {
        return 0;
    }

    $day = isset($_GET['dc_hero_day'])
        ? absint($_GET['dc_hero_day'])
        : 0;

    return ($day >= 1 && $day <= DCHHR_MAX_SLOTS) ? $day : 0;
}


/**
 * Plugin radi samo na stvarnoj početnoj stranici ID 101.
 */
function dchhr_is_target_page(): bool {
    return is_front_page() && get_queried_object_id() === DCHHR_FRONT_PAGE_ID;
}


/**
 * Ispisuje runtime skriptu.
 *
 * Dan se računa u pregledniku prema zoni Europe/Zagreb.
 * Zbog toga daily rotacija radi i kada je HTML početne stranice cacheiran.
 */
function dchhr_output_runtime_rotation(): void {
    if (!dchhr_is_target_page()) {
        return;
    }

    $preview_day = dchhr_preview_day();
    $enabled     = (bool) get_option(DCHHR_OPTION_ENABLED, 0);

    if (!$enabled && $preview_day === 0) {
        return;
    }

    $slot_urls = [];

    foreach (dchhr_all_slots() as $slot) {
        $slot_urls[] = $slot['exists'] ? $slot['url'] : null;
    }

    $config = [
        'selector'   => '.elementor-101 .elementor-element.elementor-element-' . DCHHR_ELEMENTOR_TARGET,
        'cycle'      => dchhr_cycle_days(),
        'slots'      => $slot_urls,
        'previewDay' => $preview_day,
        'timeZone'   => DCHHR_TIMEZONE,
    ];
    ?>
    <script id="drycured-home-hero-rotation-runtime">
    (function (config) {
        'use strict';

        function calculateDailySlot() {
            if (Number(config.previewDay) > 0) {
                return Number(config.previewDay);
            }

            try {
                var parts = new Intl.DateTimeFormat('en-CA', {
                    timeZone: config.timeZone,
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                }).formatToParts(new Date());

                var values = {};

                parts.forEach(function (part) {
                    values[part.type] = part.value;
                });

                var year = Number(values.year);
                var month = Number(values.month);
                var day = Number(values.day);

                var ordinal = Math.floor(
                    Date.UTC(year, month - 1, day) / 86400000
                );

                return ((ordinal % config.cycle) + config.cycle) % config.cycle + 1;
            } catch (error) {
                var fallback = new Date();
                var fallbackOrdinal = Math.floor(
                    Date.UTC(
                        fallback.getFullYear(),
                        fallback.getMonth(),
                        fallback.getDate()
                    ) / 86400000
                );

                return (
                    ((fallbackOrdinal % config.cycle) + config.cycle)
                    % config.cycle
                ) + 1;
            }
        }

        var selectedSlot = calculateDailySlot();
        var imageUrl = config.slots[selectedSlot - 1] || '';

        document.documentElement.setAttribute(
            'data-dc-hero-slot',
            String(selectedSlot)
        );

        if (!imageUrl) {
            document.documentElement.setAttribute(
                'data-dc-hero-status',
                'fallback'
            );
            return;
        }

        var selector = config.selector;

        var cssRule =
            selector
            + ':not(.elementor-motion-effects-element-type-background), '
            + selector
            + ' > .elementor-motion-effects-container'
            + ' > .elementor-motion-effects-layer'
            + '{background-image:url('
            + JSON.stringify(imageUrl)
            + ') !important;}';

        var style = document.createElement('style');

        style.id = 'drycured-home-hero-rotation-dynamic';
        style.textContent = cssRule;

        document.head.appendChild(style);

        document.documentElement.setAttribute(
            'data-dc-hero-status',
            'rotated'
        );
    })(<?php echo wp_json_encode($config, JSON_UNESCAPED_SLASHES); ?>);
    </script>
    <?php
}

add_action('wp_head', 'dchhr_output_runtime_rotation', 2);


/**
 * Preview poveznice ne smiju završiti u cacheu ili tražilicama.
 */
function dchhr_preview_headers(): void {
    if (!dchhr_is_target_page() || dchhr_preview_day() === 0) {
        return;
    }

    nocache_headers();
    header('X-Robots-Tag: noindex, nofollow, noarchive', true);
}

add_action('send_headers', 'dchhr_preview_headers');


function dchhr_preview_robots(array $robots): array {
    if (dchhr_preview_day() > 0) {
        $robots['noindex']   = true;
        $robots['nofollow']  = true;
        $robots['noarchive'] = true;
    }

    return $robots;
}

add_filter('wp_robots', 'dchhr_preview_robots');


/**
 * Izračun trenutačnog slota za administratorski prikaz.
 */
function dchhr_today_slot(): int {
    $timezone = new DateTimeZone(DCHHR_TIMEZONE);
    $today    = new DateTimeImmutable('now', $timezone);

    $utc_date = new DateTimeImmutable(
        $today->format('Y-m-d') . ' 00:00:00',
        new DateTimeZone('UTC')
    );

    $ordinal = (int) floor($utc_date->getTimestamp() / DAY_IN_SECONDS);
    $cycle   = dchhr_cycle_days();

    return (($ordinal % $cycle) + $cycle) % $cycle + 1;
}


/**
 * Administratorska stranica.
 */
function dchhr_admin_menu(): void {
    add_options_page(
        'Drycured hero rotacija',
        'Drycured hero rotacija',
        'manage_options',
        'drycured-home-hero-rotation',
        'dchhr_admin_page'
    );
}

add_action('admin_menu', 'dchhr_admin_menu');


function dchhr_admin_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    $enabled = (bool) get_option(DCHHR_OPTION_ENABLED, 0);
    $cycle   = dchhr_cycle_days();
    $token   = (string) get_option(DCHHR_OPTION_PREVIEW_TOKEN, '');
    $storage = dchhr_storage();
    $today   = dchhr_today_slot();
    $slots   = dchhr_all_slots();

    ?>
    <div class="wrap">
        <h1>Drycured — dnevna rotacija hero slike</h1>

        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Postavke hero rotacije su spremljene.</p>
            </div>
        <?php endif; ?>

        <p>
            Ciljani Elementor element:
            <code><?php echo esc_html(DCHHR_ELEMENTOR_TARGET); ?></code>
        </p>

        <p>
            Mapa slika:
            <code><?php echo esc_html($storage['dir']); ?></code>
        </p>

        <p>
            Današnji izračunati slot:
            <strong><?php echo esc_html((string) $today); ?></strong>
        </p>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="dchhr_save">
            <?php wp_nonce_field('dchhr_save_settings'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Javna rotacija</th>
                    <td>
                        <label>
                            <input
                                type="checkbox"
                                name="enabled"
                                value="1"
                                <?php checked($enabled); ?>
                            >
                            Uključi dnevnu rotaciju na javnoj početnoj stranici
                        </label>
                    </td>
                </tr>

                <tr>
                    <th scope="row">Trajanje ciklusa</th>
                    <td>
                        <select name="cycle">
                            <option value="7" <?php selected($cycle, 7); ?>>
                                7 dana
                            </option>
                            <option value="8" <?php selected($cycle, 8); ?>>
                                8 dana
                            </option>
                            <option value="14" <?php selected($cycle, 14); ?>>
                                14 dana
                            </option>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button('Spremi postavke'); ?>
        </form>

        <hr>

        <h2>Slike i testne poveznice</h2>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Slot</th>
                    <th>Datoteka</th>
                    <th>Status</th>
                    <th>Veličina</th>
                    <th>Privremeni pregled</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slots as $slot) : ?>
                    <?php
                    $preview_url = add_query_arg(
                        [
                            'dc_hero_token' => $token,
                            'dc_hero_day'   => $slot['slot'],
                            'v'             => time(),
                        ],
                        home_url('/')
                    );
                    ?>
                    <tr>
                        <td>
                            <strong>
                                <?php echo esc_html(sprintf('%02d', $slot['slot'])); ?>
                            </strong>
                        </td>
                        <td>
                            <?php echo esc_html($slot['filename'] ?: 'hero-' . sprintf('%02d', $slot['slot']) . '.webp'); ?>
                        </td>
                        <td>
                            <?php echo $slot['exists'] ? 'Dostupna' : 'Nedostaje — koristi fallback'; ?>
                        </td>
                        <td>
                            <?php echo $slot['exists'] ? esc_html(size_format($slot['size'])) : '—'; ?>
                        </td>
                        <td>
                            <a
                                href="<?php echo esc_url($preview_url); ?>"
                                target="_blank"
                                rel="noopener"
                            >
                                Otvori test
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}


/**
 * Sprema postavke rotacije.
 */
function dchhr_save_settings(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Nemate dopuštenje za ovu radnju.');
    }

    check_admin_referer('dchhr_save_settings');

    $enabled = isset($_POST['enabled']) ? 1 : 0;
    $cycle   = isset($_POST['cycle']) ? absint($_POST['cycle']) : 14;

    if (!in_array($cycle, [7, 8, 14], true)) {
        $cycle = 14;
    }

    update_option(DCHHR_OPTION_ENABLED, $enabled, false);
    update_option(DCHHR_OPTION_CYCLE, $cycle, false);

    wp_cache_flush();

    if (
        class_exists('\Elementor\Plugin')
        && isset(\Elementor\Plugin::$instance->files_manager)
    ) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    wp_safe_redirect(
        add_query_arg(
            'updated',
            '1',
            admin_url('options-general.php?page=drycured-home-hero-rotation')
        )
    );

    exit;
}

add_action('admin_post_dchhr_save', 'dchhr_save_settings');
