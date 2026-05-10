<?php
if (!defined('ABSPATH')) exit;

if (defined('WP_CLI') && WP_CLI) {

    class Drycured_Recipe_CLI {

        public function import_md($args, $assoc_args) {
            $file = $args[0] ?? '';

            if (!$file || !file_exists($file)) {
                WP_CLI::error('MD datoteka ne postoji: ' . $file);
            }

            $result = drycured_import_md_text(file_get_contents($file), basename($file));

            WP_CLI::success('MD uvoz završen.');
            WP_CLI::log('Detected: ' . ($result['detected'] ?? 0));
            WP_CLI::log('Created: ' . ($result['created'] ?? 0));
            WP_CLI::log('Updated: ' . ($result['updated'] ?? 0));
        }

        public function import_md_dir($args, $assoc_args) {
            $dir = $args[0] ?? '';

            if (!$dir || !is_dir($dir)) {
                WP_CLI::error('Mapa ne postoji: ' . $dir);
            }

            $files = glob(rtrim($dir, '/') . '/*.md');

            if (!$files) {
                WP_CLI::warning('Nema .md datoteka u mapi.');
                return;
            }

            $created = 0;
            $updated = 0;
            $detected = 0;

            foreach ($files as $file) {
                WP_CLI::log('Uvoz MD: ' . basename($file));
                $result = drycured_import_md_text(file_get_contents($file), basename($file));

                $created += intval($result['created'] ?? 0);
                $updated += intval($result['updated'] ?? 0);
                $detected += intval($result['detected'] ?? 0);
            }

            WP_CLI::success('MD uvoz mape završen.');
            WP_CLI::log('Detected: ' . $detected);
            WP_CLI::log('Created: ' . $created);
            WP_CLI::log('Updated: ' . $updated);
        }

        public function import_json($args, $assoc_args) {
            $file = $args[0] ?? '';

            if (!$file || !file_exists($file)) {
                WP_CLI::error('JSON datoteka ne postoji: ' . $file);
            }

            if (!function_exists('drycured_import_json_file')) {
                WP_CLI::error('Funkcija drycured_import_json_file nije dostupna. Provjeri json-importer.php.');
            }

            try {
                $result = drycured_import_json_file($file);
            } catch (Exception $e) {
                WP_CLI::error($e->getMessage());
            }

            WP_CLI::success('JSON uvoz završen.');
            WP_CLI::log('Paket: ' . ($result['pack'] ?? basename($file)));
            WP_CLI::log('Detected: ' . ($result['detected'] ?? 0));
            WP_CLI::log('Created: ' . ($result['created'] ?? 0));
            WP_CLI::log('Updated: ' . ($result['updated'] ?? 0));
            WP_CLI::log('Skipped: ' . ($result['skipped'] ?? 0));

            if (!empty($result['errors'])) {
                WP_CLI::warning('Greške:');
                foreach ($result['errors'] as $error) {
                    WP_CLI::log('- ' . $error);
                }
            }
        }

        public function import_json_dir($args, $assoc_args) {
            $dir = $args[0] ?? '';

            if (!$dir || !is_dir($dir)) {
                WP_CLI::error('Mapa ne postoji: ' . $dir);
            }

            if (!function_exists('drycured_import_json_file')) {
                WP_CLI::error('Funkcija drycured_import_json_file nije dostupna. Provjeri json-importer.php.');
            }

            $files = glob(rtrim($dir, '/') . '/*.json');

            if (!$files) {
                WP_CLI::warning('Nema .json datoteka u mapi.');
                return;
            }

            $total = [
                'detected' => 0,
                'created'  => 0,
                'updated'  => 0,
                'skipped'  => 0,
                'errors'   => [],
            ];

            foreach ($files as $file) {
                WP_CLI::log('Uvoz JSON: ' . basename($file));

                try {
                    $result = drycured_import_json_file($file);
                } catch (Exception $e) {
                    $total['errors'][] = basename($file) . ': ' . $e->getMessage();
                    continue;
                }

                foreach (['detected', 'created', 'updated', 'skipped'] as $key) {
                    $total[$key] += intval($result[$key] ?? 0);
                }

                $total['errors'] = array_merge($total['errors'], $result['errors'] ?? []);
            }

            WP_CLI::success('JSON uvoz mape završen.');
            WP_CLI::log('Detected: ' . $total['detected']);
            WP_CLI::log('Created: ' . $total['created']);
            WP_CLI::log('Updated: ' . $total['updated']);
            WP_CLI::log('Skipped: ' . $total['skipped']);

            if (!empty($total['errors'])) {
                WP_CLI::warning('Greške:');
                foreach ($total['errors'] as $error) {
                    WP_CLI::log('- ' . $error);
                }
            }
        }

        public function purge_recipes($args, $assoc_args) {
            $ids = get_posts([
                'post_type'   => 'dry_recipe',
                'post_status' => 'any',
                'numberposts' => -1,
                'fields'      => 'ids',
            ]);

            foreach ($ids as $id) {
                wp_delete_post($id, true);
            }

            WP_CLI::success('Obrisano dry_recipe zapisa: ' . count($ids));
        }
    }

    WP_CLI::add_command('drycured', 'Drycured_Recipe_CLI');
}
