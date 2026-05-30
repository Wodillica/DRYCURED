<?php
/**
 * Plugin Name: Drycured Recipe View v0.5.15 Sensory Polish
 * Description: Poboljšava prikaz senzorskog profila proizvoda u web receptu.
 * Version: 0.5.15
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
        /*
         * v0.5.15 — Senzorski profil kao čitljiva karta proizvoda.
         */

        body.single-dry_recipe #profil .dcv5-sensory-summary {
            margin: 0 0 18px !important;
            padding: 15px 17px !important;
            border-radius: 16px !important;
            border: 1px solid #e4c98c !important;
            background: #fff8e8 !important;
            color: #334059 !important;
            font-size: 15.5px !important;
            line-height: 1.65 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-summary strong {
            color: #10182d !important;
            font-weight: 900 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 {
            position: relative !important;
            display: grid !important;
            grid-template-columns: 120px minmax(260px, 1fr) 72px minmax(230px, .9fr) !important;
            gap: 14px !important;
            align-items: center !important;
            padding: 15px 16px !important;
            margin-bottom: 10px !important;
            border-radius: 17px !important;
            border: 1px solid #e6cf98 !important;
            background: #fffdf7 !important;
            box-shadow: 0 6px 14px rgba(25, 32, 48, .035) !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 > * {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 > *:first-child {
            color: #10182d !important;
            font-size: 15.5px !important;
            font-weight: 900 !important;
            line-height: 1.25 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-row-v2 .dcv5-sensory-meaning {
            display: flex !important;
            flex-direction: column !important;
            gap: 3px !important;
            padding-left: 14px !important;
            border-left: 1px solid #ead6a5 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-meaning strong {
            color: #111b33 !important;
            font-size: 13px !important;
            line-height: 1.2 !important;
            font-weight: 900 !important;
            letter-spacing: .035em !important;
            text-transform: uppercase !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-meaning span {
            color: #46536b !important;
            font-size: 14px !important;
            line-height: 1.45 !important;
            font-weight: 500 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale {
            grid-column: 2 / 4 !important;
            display: grid !important;
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 6px !important;
            margin-top: 6px !important;
            color: #8b7440 !important;
            font-size: 10.5px !important;
            font-weight: 800 !important;
            letter-spacing: .035em !important;
            text-transform: uppercase !important;
            opacity: .9 !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(1) {
            text-align: left !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(2) {
            text-align: center !important;
        }

        body.single-dry_recipe #profil .dcv5-sensory-scale span:nth-child(3) {
            text-align: right !important;
        }

        @media (max-width: 980px) {
            body.single-dry_recipe #profil .dcv5-sensory-row-v2 {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }

            body.single-dry_recipe #profil .dcv5-sensory-meaning {
                padding-left: 0 !important;
                border-left: 0 !important;
            }

            body.single-dry_recipe #profil .dcv5-sensory-scale {
                grid-column: 1 !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const profile = document.querySelector('#profil');
            if (!profile) {
                return;
            }

            if (!profile.querySelector('.dcv5-sensory-summary')) {
                const note = profile.querySelector('.dcv5-section-note');
                const summary = document.createElement('div');
                summary.className = 'dcv5-sensory-summary';
                summary.innerHTML = '<strong>Senzorski potpis:</strong> paprikasto-dimljena kobasica srednje masnoće, blage do umjerene ljutine i čvrstog, ali ne presuhog presjeka.';
                if (note && note.parentNode) {
                    note.insertAdjacentElement('afterend', summary);
                } else {
                    profile.insertBefore(summary, profile.firstChild);
                }
            }

            const meanings = {
                'Paprika': {
                    level: 'izražena',
                    text: 'Paprika je vodeća aroma i nosi regionalni karakter proizvoda.'
                },
                'Dim': {
                    level: 'srednje izražen',
                    text: 'Dim je prisutan, ali ne smije prekriti meso i papriku.'
                },
                'Ljutina': {
                    level: 'blaga do umjerena',
                    text: 'Ljutina daje živost, ali ne dominira zalogajem.'
                },
                'Slanoća': {
                    level: 'uravnotežena',
                    text: 'Slanost treba čuvati proizvod bez grubog slanog dojma.'
                },
                'Masnoća': {
                    level: 'srednja',
                    text: 'Masnoća daje sočnost i mekši presjek bez masnog dojma.'
                },
                'Tekstura': {
                    level: 'kompaktna',
                    text: 'Presjek treba biti povezan, rezan čistim rubom i bez šupljina.'
                }
            };

            const candidates = Array.from(profile.querySelectorAll('div, article, li'))
                .filter(function (el) {
                    return /\b\d{1,2}\/10\b/.test(el.textContent || '') &&
                           !el.classList.contains('dcv5-sensory-row-v2') &&
                           !el.closest('.dcv5-sensory-summary');
                });

            candidates.forEach(function (row) {
                const txt = row.textContent || '';
                const key = Object.keys(meanings).find(function (name) {
                    return txt.toLowerCase().includes(name.toLowerCase());
                });

                if (!key) {
                    return;
                }

                row.classList.add('dcv5-sensory-row-v2');

                if (!row.querySelector('.dcv5-sensory-meaning')) {
                    const meaning = document.createElement('div');
                    meaning.className = 'dcv5-sensory-meaning';
                    meaning.innerHTML = '<strong>' + meanings[key].level + '</strong><span>' + meanings[key].text + '</span>';
                    row.appendChild(meaning);
                }

                if (!row.querySelector('.dcv5-sensory-scale')) {
                    const scale = document.createElement('div');
                    scale.className = 'dcv5-sensory-scale';
                    scale.innerHTML = '<span>blago</span><span>srednje</span><span>izraženo</span>';
                    row.appendChild(scale);
                }
            });
        });
    </script>
    <?php
}, 99999);
