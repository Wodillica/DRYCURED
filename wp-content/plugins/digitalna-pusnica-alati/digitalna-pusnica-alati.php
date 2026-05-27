<?php
/**
 * Plugin Name: Digitalna Pusnica - Alati
 * Version: 1.3.2
 */
defined('ABSPATH') || exit;
define('DP_ALATI_VERSION', '1.1.3');
define('DP_ALATI_URL', plugin_dir_url(__FILE__));
define('DP_ALATI_PATH', plugin_dir_path(__FILE__));

// CSS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('dp-alati-style', DP_ALATI_URL . 'dp-alati-style.css', [], DP_ALATI_VERSION);
    wp_enqueue_script('dp-alat-11', DP_ALATI_URL . 'dist/tool-11-ph-tracker.js', [], DP_ALATI_VERSION, true);
    wp_enqueue_script('dp-alat-12', DP_ALATI_URL . 'dist/tool-12-kalkulator-susenja.js', [], DP_ALATI_VERSION, true);
});

// type=module filter za SVE dp- skripte
add_filter('script_loader_tag', function($tag, $handle, $src) {
    if (strpos($handle, 'dp-') === 0) {
        return '<script type="module" src="' . esc_url($src) . '" id="' . esc_attr($handle) . '-js"></script>' . "\n";
    }
    return $tag;
}, 10, 3);

// Shortcodi
$dp_alati = [
    'dp_alat_dijagnostika' => ['file' => 'tool-07-dijagnostika.js', 'id' => 'dp-alat-07'],
    'dp_alat_weight'       => ['file' => 'tool-02-weight.js',       'id' => 'dp-alat-02'],
    'dp_alat_dnevnik'      => ['file' => 'tool-03-dnevnik.js',      'id' => 'dp-alat-03'],
    'dp_alat_dimljenje'    => ['file' => 'tool-04-dimljenje.js',    'id' => 'dp-alat-04'],
    'dp_alat_monitor'      => ['file' => 'tool-05-monitor.js',      'id' => 'dp-alat-05'],
    'dp_alat_haccp'        => ['file' => 'tool-06-haccp.js',        'id' => 'dp-alat-06'],
    'dp_alat_inventar'     => ['file' => 'tool-08-inventar.js',     'id' => 'dp-alat-08'],
    'dp_alat_sol'          => ['file' => 'tool-09-kalkulator-soli.js', 'id' => 'dp-alat-09'],
    'dp_alat_10' => ['file' => 'tool-10-generator-etiketa.js', 'id' => 'dp-alat-10'],
    'dp_alat_11' => ['file' => 'tool-11-ph-tracker.js', 'id' => 'dp-alat-11'],
    'dp_alat_12' => ['file' => 'tool-12-kalkulator-susenja.js', 'id' => 'dp-alat-12'],
];

foreach ($dp_alati as $shortcode => $alat) {
    add_shortcode($shortcode, function($atts) use ($alat) {
        wp_enqueue_script(
            $alat['id'],
            DP_ALATI_URL . 'dist/' . $alat['file'],
            [],
            DP_ALATI_VERSION,
            true
        );
        wp_localize_script($alat['id'], 'dpAlatiConfig', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('dp_alati_nonce'),
            'userId'     => get_current_user_id(),
            'isLoggedIn' => is_user_logged_in(),
            'userName'   => wp_get_current_user()->display_name ?? '',
        ]);
        $mount_id = $alat['id'] . '-mount';
        return '<div id="' . esc_attr($mount_id) . '" class="dp-alat-wrapper"></div>';
    });
}

add_action('wp_footer', function() {
    if (!is_singular()) return;
    global $post;
    if (!$post) return;
    $shortcodes = ['dp_alat_weight','dp_alat_dnevnik','dp_alat_dimljenje','dp_alat_monitor','dp_alat_haccp','dp_alat_inventar','dp_alat_sol','dp_alat_10'];
    $found = false;
    foreach ($shortcodes as $sc) {
        if (has_shortcode($post->post_content, $sc)) { $found = true; break; }
    }
    if (!$found) return;
    echo '<script>' . file_get_contents(DP_ALATI_PATH . 'style_patcher.js') . '</script>';
}, 99);

add_action('send_headers', function() {
    if (!is_singular() || !is_page()) {
        return;
    }

    $dp_alati_paths = [
        '/alati/',
        '/kalkulator-soli/',
        '/pracenje-ph/',
        '/kalkulator-susenja/',
        '/planer-dimljenja/',
    ];

    $request_path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    $request_path = trailingslashit($request_path ?: '/');

    $is_tool_page = in_array($request_path, $dp_alati_paths, true);

    if (!$is_tool_page) {
        global $post;

        $tool_shortcodes = [
            'dp_alat_weight',
            'dp_alat_dnevnik',
            'dp_alat_dimljenje',
            'dp_alat_monitor',
            'dp_alat_haccp',
            'dp_alat_inventar',
            'dp_alat_sol',
            'dp_alat_10',
            'dp_alat_11',
            'dp_alat_12',
        ];

        if ($post instanceof WP_Post) {
            foreach ($tool_shortcodes as $shortcode) {
                if (has_shortcode((string) $post->post_content, $shortcode)) {
                    $is_tool_page = true;
                    break;
                }
            }
        }
    }

    if (!$is_tool_page) {
        return;
    }

    header('Cache-Control: no-cache, no-store, must-revalidate');
});
