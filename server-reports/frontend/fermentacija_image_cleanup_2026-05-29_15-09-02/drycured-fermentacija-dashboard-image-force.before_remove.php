<?php
/**
 * Plugin Name: Drycured Fermentacija Dashboard Image Force
 * Description: Forces persistent dashboard background image on the modern fermentacija process page.
 * Version: 0.0.1
 * Author: drycured.com
 */

defined('ABSPATH') || exit;

function dcfif_is_fermentacija_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/fermentacija';
}

function dcfif_force_dashboard_image(): void {
    if (!dcfif_is_fermentacija_page()) {
        return;
    }
    ?>
    <style id="dcfif-force-dashboard-image">
        body .dcpf-dashboard,
        body .dcpf-dashboard.dcpfs-live {
            background-image:
                linear-gradient(rgba(10,16,26,.58), rgba(10,16,26,.70)),
                url("https://drycured.com/wp-content/uploads/drycured/home-process/process-07-fermentacija.webp") !important;
            background-size: cover !important;
            background-position: center center !important;
            background-repeat: no-repeat !important;
            position: relative !important;
        }

        body .dcpf-dashboard::before {
            display: none !important;
            content: none !important;
        }

        body .dcpf-dashboard > * {
            position: relative !important;
            z-index: 2 !important;
        }
    </style>

    <script id="dcfif-force-dashboard-image-js">
        (function(){
            function forceFermentationDashboardImage(){
                var dash = document.querySelector('.dcpf-dashboard');
                if (!dash) return;

                dash.style.setProperty(
                    'background-image',
                    'linear-gradient(rgba(10,16,26,.58), rgba(10,16,26,.70)), url("https://drycured.com/wp-content/uploads/drycured/home-process/process-07-fermentacija.webp")',
                    'important'
                );
                dash.style.setProperty('background-size', 'cover', 'important');
                dash.style.setProperty('background-position', 'center center', 'important');
                dash.style.setProperty('background-repeat', 'no-repeat', 'important');
            }

            forceFermentationDashboardImage();
            document.addEventListener('DOMContentLoaded', forceFermentationDashboardImage);
            window.addEventListener('load', forceFermentationDashboardImage);
            setTimeout(forceFermentationDashboardImage, 400);
            setTimeout(forceFermentationDashboardImage, 1200);
        })();
    </script>
    <?php
}
add_action('wp_footer', 'dcfif_force_dashboard_image', 9999);
