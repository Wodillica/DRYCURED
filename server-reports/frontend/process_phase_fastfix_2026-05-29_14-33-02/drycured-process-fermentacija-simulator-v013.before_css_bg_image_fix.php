<?php
/**
 * Plugin Name: Drycured Process Fermentacija Simulator
 * Description: Pretvara statični parametarski panel na stranici Fermentacija u interaktivni simulator.
 * Version: 0.1.3
 * Author: drycured.com
 */

if (!defined('ABSPATH')) {
    exit;
}

function dcpfs_enabled(): bool {
    return (bool) get_option('drycured_process_fermentacija_simulator_enabled', 1);
}

function dcpfs_is_page(): bool {
    $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    return $path === 'proces-izrade/fermentacija';
}

function dcpfs_assets() {
    if (!dcpfs_is_page() || !dcpfs_enabled()) {
        return;
    }
    ?>
    <style>
        .dcpfs-dashboard-image {
            margin: 0 0 20px;
            border-radius: 24px;
            overflow: hidden;
            background: rgba(255,255,255,.06);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.08);
        }

        .dcpfs-dashboard-image img {
            display: block;
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .dcpf-dashboard.dcpfs-live {
            padding: 26px !important;
        }

        .dcpfs-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .dcpfs-head span {
            display: inline-flex;
            width: max-content;
            min-height: 28px;
            align-items: center;
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(241,216,137,.16);
            color: #f1d889;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .dcpfs-head strong {
            color: rgba(255,255,255,.72);
            font-size: 12px;
            font-weight: 800;
            text-align: right;
            line-height: 1.35;
        }

        .dcpfs-title {
            margin: 0 0 18px !important;
            color: #fff !important;
            font-size: 28px !important;
            line-height: 1.05 !important;
            letter-spacing: -.03em !important;
        }

        .dcpfs-controls {
            display: grid;
            gap: 11px;
        }

        .dcpfs-control label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 5px;
            color: rgba(255,255,255,.84);
            font-size: 12px;
            font-weight: 900;
        }

        .dcpfs-control label b {
            color: #f1d889;
            font-variant-numeric: tabular-nums;
        }

        .dcpfs-control input[type="range"] {
            --pos: 50%;
            -webkit-appearance: none;
            appearance: none;
            width: 100%;
            height: 10px;
            margin: 6px 0 2px;
            padding: 0;
            border: 0;
            background: transparent;
            outline: none;
            cursor: pointer;
        }

        .dcpfs-control input[type="range"]::-webkit-slider-runnable-track {
            height: 10px;
            border-radius: 999px;
            background:
                linear-gradient(90deg, #f1d889 0%, #78d3ff var(--pos), rgba(255,255,255,.14) var(--pos), rgba(255,255,255,.14) 100%);
            box-shadow: inset 0 1px 2px rgba(0,0,0,.2);
        }

        .dcpfs-control input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            margin-top: -6px;
            border-radius: 50%;
            border: 3px solid #101722;
            background: #f1d889;
            box-shadow: 0 6px 18px rgba(0,0,0,.35), 0 0 0 4px rgba(241,216,137,.16);
        }

        .dcpfs-control input[type="range"]::-moz-range-track {
            height: 10px;
            border-radius: 999px;
            background: rgba(255,255,255,.14);
        }

        .dcpfs-control input[type="range"]::-moz-range-progress {
            height: 10px;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
        }

        .dcpfs-control input[type="range"]::-moz-range-thumb {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 3px solid #101722;
            background: #f1d889;
        }

        .dcpfs-result {
            margin-top: 16px;
            padding: 15px;
            border-radius: 20px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
        }

        .dcpfs-result h3 {
            margin: 0 0 7px !important;
            color: #fff !important;
            font-size: 18px !important;
            line-height: 1.2 !important;
        }

        .dcpfs-result p {
            margin: 0 !important;
            color: rgba(255,255,255,.74) !important;
            font-size: 13px !important;
            line-height: 1.48 !important;
        }

        .dcpfs-bars {
            display: grid;
            gap: 8px;
            margin-top: 13px;
        }

        .dcpfs-bar label {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 5px;
            color: rgba(255,255,255,.70);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .dcpfs-track {
            height: 8px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            overflow: hidden;
        }

        .dcpfs-fill {
            display: block;
            width: 40%;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f1d889, #78d3ff);
            transition: width .2s ease;
        }

        .dcpfs-warning .dcpfs-fill {
            background: linear-gradient(90deg, #f1d889, #ff9a76);
        }

        .dcpfs-note {
            margin-top: 12px;
            color: rgba(255,255,255,.52);
            font-size: 11px;
            line-height: 1.4;
        }

        @media (max-width: 680px) {
            .dcpfs-title {
                font-size: 24px !important;
            }

            .dcpfs-head {
                flex-direction: column;
                gap: 8px;
            }

            .dcpfs-head strong {
                text-align: left;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dash = document.querySelector('.dcpf-dashboard');
            if (!dash || dash.classList.contains('dcpfs-live')) return;

            dash.classList.add('dcpfs-live');

            dash.innerHTML = `
                <figure class="dcpf-dashboard-image dcpfs-dashboard-image">
                    <img src="https://drycured.com/wp-content/uploads/drycured/home-process/process-07-fermentacija.webp" alt="Fermentacija trajnih kobasica u kontroliranoj mikroklimi" loading="eager" decoding="async">
                </figure>
                <div class="dcpfs-head">
                    <span>aktivni simulator</span>
                    <strong>edukativna procjena<br>ne zamjenjuje mjerenje</strong>
                </div>

                <h2 class="dcpfs-title">Uvjeti fermentacije</h2>

                <div class="dcpfs-controls">
                    <div class="dcpfs-control">
                        <label>Temperatura <b id="dcpfs-temp-val">22 °C</b></label>
                        <input id="dcpfs-temp" type="range" min="14" max="28" value="22" step="1">
                    </div>

                    <div class="dcpfs-control">
                        <label>Relativna vlaga <b id="dcpfs-rh-val">92 %</b></label>
                        <input id="dcpfs-rh" type="range" min="75" max="98" value="92" step="1">
                    </div>

                    <div class="dcpfs-control">
                        <label>Šećer u smjesi <b id="dcpfs-sugar-val">2.5 g/kg</b></label>
                        <input id="dcpfs-sugar" type="range" min="0" max="6" value="2.5" step="0.5">
                    </div>

                    <div class="dcpfs-control">
                        <label>Promjer proizvoda <b id="dcpfs-diam-val">45 mm</b></label>
                        <input id="dcpfs-diam" type="range" min="24" max="90" value="45" step="1">
                    </div>

                    <div class="dcpfs-control">
                        <label>Ciljani pH <b id="dcpfs-ph-val">5.2</b></label>
                        <input id="dcpfs-ph" type="range" min="4.7" max="5.6" value="5.2" step="0.1">
                    </div>
                </div>

                <div class="dcpfs-result">
                    <h3 id="dcpfs-status-title">Uravnotežena fermentacija</h3>
                    <p id="dcpfs-status-text">Uvjeti su blizu tipične edukativne zone za početak fermentacije.</p>

                    <div class="dcpfs-bars">
                        <div class="dcpfs-bar">
                            <label>Brzina fermentacije <span id="dcpfs-speed-num">0/100</span></label>
                            <div class="dcpfs-track"><i class="dcpfs-fill" id="dcpfs-speed"></i></div>
                        </div>

                        <div class="dcpfs-bar">
                            <label>Rizik presporog starta <span id="dcpfs-slow-num">0/100</span></label>
                            <div class="dcpfs-track"><i class="dcpfs-fill" id="dcpfs-slow"></i></div>
                        </div>

                        <div class="dcpfs-bar">
                            <label>Rizik prekiseljavanja <span id="dcpfs-acid-num">0/100</span></label>
                            <div class="dcpfs-track"><i class="dcpfs-fill" id="dcpfs-acid"></i></div>
                        </div>

                        <div class="dcpfs-bar">
                            <label>Rizik tvrde kore <span id="dcpfs-dry-num">0/100</span></label>
                            <div class="dcpfs-track"><i class="dcpfs-fill" id="dcpfs-dry"></i></div>
                        </div>
                    </div>
                </div>

                <div class="dcpfs-note">
                    Simulator služi za razumijevanje odnosa uvjeta. Stvarnu proizvodnju uvijek treba voditi prema receptu, mjerenju temperature, vlage i pH-a.
                </div>
            `;

            function clamp(v, min, max) {
                return Math.max(min, Math.min(max, v));
            }

            function n(id) {
                return parseFloat(document.getElementById(id).value);
            }

            function updateRangeFill(input) {
                const min = parseFloat(input.min || 0);
                const max = parseFloat(input.max || 100);
                const val = parseFloat(input.value || 0);
                const pct = ((val - min) / (max - min)) * 100;
                input.style.setProperty('--pos', clamp(pct, 0, 100) + '%');
            }

            function setBar(id, numId, value, warning) {
                const val = clamp(Math.round(value), 0, 100);
                const el = document.getElementById(id);
                el.style.width = val + '%';
                document.getElementById(numId).textContent = val + '/100';

                const wrap = el.closest('.dcpfs-bar');
                if (warning && val > 62) {
                    wrap.classList.add('dcpfs-warning');
                } else {
                    wrap.classList.remove('dcpfs-warning');
                }
            }

            function evaluate() {
                const temp = n('dcpfs-temp');
                const rh = n('dcpfs-rh');
                const sugar = n('dcpfs-sugar');
                const diam = n('dcpfs-diam');
                const ph = n('dcpfs-ph');

                document.getElementById('dcpfs-temp-val').textContent = temp.toFixed(0) + ' °C';
                document.getElementById('dcpfs-rh-val').textContent = rh.toFixed(0) + ' %';
                document.getElementById('dcpfs-sugar-val').textContent = sugar.toFixed(1) + ' g/kg';
                document.getElementById('dcpfs-diam-val').textContent = diam.toFixed(0) + ' mm';
                document.getElementById('dcpfs-ph-val').textContent = ph.toFixed(1);

                document.querySelectorAll('.dcpfs-control input[type="range"]').forEach(updateRangeFill);

                let speed = 38 + ((temp - 18) * 5.1) + (sugar * 7.0) - Math.max(0, diam - 55) * 0.38;
                speed = clamp(speed, 5, 100);

                let slow = 20 + Math.max(0, 18 - temp) * 9 + Math.max(0, 1.5 - sugar) * 18 + Math.max(0, diam - 60) * 0.78;
                slow = clamp(slow, 0, 100);

                let acid = 14 + Math.max(0, sugar - 2.5) * 15 + Math.max(0, temp - 23) * 8 + Math.max(0, 5.1 - ph) * 62;
                acid = clamp(acid, 0, 100);

                let dry = 10 + Math.max(0, 88 - rh) * 5 + Math.max(0, temp - 24) * 5 + Math.max(0, diam - 55) * 0.55;
                dry = clamp(dry, 0, 100);

                setBar('dcpfs-speed', 'dcpfs-speed-num', speed, false);
                setBar('dcpfs-slow', 'dcpfs-slow-num', slow, true);
                setBar('dcpfs-acid', 'dcpfs-acid-num', acid, true);
                setBar('dcpfs-dry', 'dcpfs-dry-num', dry, true);

                let title = 'Uravnotežena fermentacija';
                let text = 'Uvjeti su blizu tipične edukativne zone za početak fermentacije. Prati miris, površinu, temperaturu, vlagu i pH ako ga mjeriš.';

                if (slow > 62) {
                    title = 'Fermentacija može krenuti presporo';
                    text = 'Preniska temperatura, premalo dostupnog šećera ili veći promjer mogu usporiti početak fermentacije.';
                } else if (acid > 65) {
                    title = 'Rizik previše kiselog proizvoda';
                    text = 'Viša temperatura, više šećera i niži ciljani pH mogu ubrzati zakiseljavanje. Brže nije uvijek bolje.';
                } else if (dry > 65) {
                    title = 'Rizik tvrde kore';
                    text = 'Niska relativna vlaga ili prebrz prijelaz prema sušenju mogu zatvoriti površinu prije jezgre.';
                } else if (speed > 80) {
                    title = 'Brza fermentacija — prati pH';
                    text = 'Proces bi mogao ići brzo. To može biti korisno, ali traži pažljiviju kontrolu kiselosti i temperature.';
                }

                document.getElementById('dcpfs-status-title').textContent = title;
                document.getElementById('dcpfs-status-text').textContent = text;
            }

            ['dcpfs-temp', 'dcpfs-rh', 'dcpfs-sugar', 'dcpfs-diam', 'dcpfs-ph'].forEach(function (id) {
                document.getElementById(id).addEventListener('input', evaluate);
            });

            evaluate();
        });
    </script>
    <?php
}
add_action('wp_head', 'dcpfs_assets', 190);
