<?php
/**
 * Plugin Name: Drycured Canonical Recipe Tuning
 * Description: Dodatna dorada javnog prikaza kanonskih recepata.
 * Version: 0.1.0
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
        @media (min-width: 641px) {
            .single-dry_recipe .dc-canon-table-wrap {
                overflow-x: visible !important;
            }

            .single-dry_recipe .dc-canon-table {
                min-width: 0 !important;
                width: 100% !important;
                table-layout: fixed !important;
            }

            .single-dry_recipe .dc-canon-table th,
            .single-dry_recipe .dc-canon-table td {
                white-space: normal !important;
                overflow-wrap: break-word !important;
                word-break: normal !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(1),
            .single-dry_recipe .dc-canon-table td:nth-child(1) {
                width: 36% !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(2),
            .single-dry_recipe .dc-canon-table td:nth-child(2) {
                width: 22% !important;
            }

            .single-dry_recipe .dc-canon-table th:nth-child(3),
            .single-dry_recipe .dc-canon-table td:nth-child(3) {
                width: 42% !important;
            }
        }

        .single-dry_recipe .dc-canon-admin {
            display: none !important;
        }

        .single-dry_recipe .dc-canon-section p,
        .single-dry_recipe .dc-canon-list li,
        .single-dry_recipe .dc-canon-table td {
            font-size: 16px !important;
            line-height: 1.65 !important;
        }

        .single-dry_recipe .dc-canon-section {
            margin-bottom: 22px !important;
        }
    </style>
    <?php
}, 999);
