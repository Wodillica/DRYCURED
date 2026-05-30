<?php
/**
 * Plugin Name: Drycured Recipe View v0.5.14 Safety Marker
 * Description: Dodaje stvarne HTML semaforske oznake u kartice sigurnosnog semafora.
 * Version: 0.5.14
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {
    if (!is_singular('dry_recipe')) {
        return;
    }
    ?>
    <style>
        body.single-dry_recipe #sigurnost .dcv5-safety-card {
            position: relative !important;
            padding-bottom: 64px !important;
            overflow: visible !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-marker {
            position: absolute !important;
            left: 18px !important;
            bottom: 18px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 9px !important;
            z-index: 999 !important;
            pointer-events: none !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-dot-real {
            width: 28px !important;
            height: 28px !important;
            border-radius: 999px !important;
            border: 4px solid #fffaf0 !important;
            box-shadow: 0 5px 14px rgba(17, 27, 51, .28) !important;
            flex: 0 0 auto !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-label-real {
            font-size: 12px !important;
            line-height: 1 !important;
            font-weight: 900 !important;
            letter-spacing: .045em !important;
            text-transform: uppercase !important;
            color: #6b5c38 !important;
            background: rgba(255, 250, 240, .9) !important;
            border: 1px solid #ead6a5 !important;
            border-radius: 999px !important;
            padding: 6px 9px !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.green .dcv5-safety-dot-real {
            background: #2e9b57 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.yellow .dcv5-safety-dot-real {
            background: #f2c230 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.red .dcv5-safety-dot-real {
            background: #c93636 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.green {
            border-left: 6px solid #2e9b57 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.yellow {
            border-left: 6px solid #f2c230 !important;
        }

        body.single-dry_recipe #sigurnost .dcv5-safety-card.red {
            border-left: 6px solid #c93636 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = document.querySelectorAll('#sigurnost .dcv5-safety-card');

            cards.forEach(function (card) {
                if (card.querySelector('.dcv5-safety-marker')) {
                    return;
                }

                let label = '';
                if (card.classList.contains('green')) {
                    label = 'normalno';
                } else if (card.classList.contains('yellow')) {
                    label = 'oprez';
                } else if (card.classList.contains('red')) {
                    label = 'odbaci';
                }

                if (!label) {
                    return;
                }

                const marker = document.createElement('div');
                marker.className = 'dcv5-safety-marker';

                const dot = document.createElement('span');
                dot.className = 'dcv5-safety-dot-real';

                const text = document.createElement('span');
                text.className = 'dcv5-safety-label-real';
                text.textContent = label;

                marker.appendChild(dot);
                marker.appendChild(text);
                card.appendChild(marker);
            });
        });
    </script>
    <?php
}, 99999);
