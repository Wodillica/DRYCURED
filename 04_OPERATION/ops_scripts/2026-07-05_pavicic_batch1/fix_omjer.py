#!/usr/bin/env python3
"""
Fix 1: Omjer smjese — zamjena hardkodiranog 70/30 s izračunom iz stvarnih kg materijala.
Fix 2: HR-ZA-002 i HR-TU-004 — bolje quick kartice (Čuvanje, Toplinska obrada).
"""
import subprocess, json, re

WP = 'wp --path=/var/www/html --allow-root'
PHP_FILE = '/var/www/html/wp-content/mu-plugins/drycured-recipe-view-v1.php'

def wp(cmd):
    r = subprocess.run(f'{WP} {cmd}', shell=True, capture_output=True, text=True)
    return r.stdout.strip(), r.returncode

# ── Fix 1: PHP patch za omjer smjese ──────────────────────────────────────────

OLD_OMJER = (
    '                    <div class="dcv5-composition">\n'
    '                        <div class="dcv5-comp-bar">70 % meso</div>\n'
    '                        <div class="dcv5-comp-bar">30 % slanina</div>\n'
    '                    </div>'
)

NEW_OMJER = (
    '                    <?php\n'
    '                    // Izračun omjera iz stvarnih kg materijala\n'
    '                    $__total = 0; $__fat = 0;\n'
    '                    foreach (($recipe[\'materials\'] ?? []) as $__m) {\n'
    '                        $__amt = str_replace(\',\', \'.\', $__m[\'amount\'] ?? \'\');\n'
    '                        if (preg_match(\'/(\d+(?:\.\d+)?)\s*kg/i\', $__amt, $__mm)) {\n'
    '                            $__kg = (float)$__mm[1];\n'
    '                            $__total += $__kg;\n'
    '                            $__n = mb_strtolower($__m[\'name\'] ?? \'\', \'UTF-8\');\n'
    '                            if (strpos($__n, \'slanin\') !== false || strpos($__n, \'masno\') !== false) {\n'
    '                                $__fat += $__kg;\n'
    '                            }\n'
    '                        }\n'
    '                    }\n'
    '                    if ($__total > 0 && $__fat > 0) {\n'
    '                        $__fp = round($__fat / $__total * 100);\n'
    '                        $__mp = 100 - $__fp;\n'
    '                        echo \'<div class="dcv5-composition">\';\n'
    '                        echo \'<div class="dcv5-comp-bar">\' . $__mp . \' % meso</div>\';\n'
    '                        echo \'<div class="dcv5-comp-bar">\' . $__fp . \' % slanina</div>\';\n'
    '                        echo \'</div>\';\n'
    '                    }\n'
    '                    ?>'
)

with open(PHP_FILE, 'r', encoding='utf-8') as fh:
    content = fh.read()

if OLD_OMJER not in content:
    print('ERR: anchor not found in PHP file — check whitespace')
    exit(1)

new_content = content.replace(OLD_OMJER, NEW_OMJER, 1)

with open(PHP_FILE, 'w', encoding='utf-8') as fh:
    fh.write(new_content)

print('OK: PHP omjer smjese patched')
print(f'  NEW_OMJER present: {"YES" if NEW_OMJER[:50] in new_content else "NO"}')

# Verify PHP syntax
r = subprocess.run('php -l ' + PHP_FILE, shell=True, capture_output=True, text=True)
if 'No syntax errors' in r.stdout or 'No syntax errors' in r.stderr:
    print('  PHP syntax: OK')
else:
    print(f'  PHP syntax ERR: {r.stdout} {r.stderr}')

# ── Fix 2: Quick kartice za HR-ZA-002 (krvavice) i HR-TU-004 (devenice) ──────

IZNUTRICE_POSTS = {
    3573: 'HR-ZA-002',  # Zagorske krvavice
    3574: 'HR-TU-004',  # Turopoljske devenice
}

print('\nDodavanje quick kartice za krvavice/devenice:')
for pid, code in IZNUTRICE_POSTS.items():
    raw, _ = wp(f'post meta get {pid} _dry_recipe_overrides')
    try:
        ov = json.loads(raw)
    except Exception:
        ov = {}

    ov['quick_overrides'] = ov.get('quick_overrides', {})
    ov['quick_overrides']['Toplinska obrada'] = 'kuhanje 50 min na laganoj vatri'
    ov['quick_overrides']['Čuvanje'] = 'hladno, suho, luftirano; konzumirati svježe (kraći rok od dimljenih)'
    # krvavice/devenice nemaju Dimljenje label — safe to leave quick_overrides['Dimljenje'] in place

    j = json.dumps(ov, ensure_ascii=False)
    escaped = j.replace("'", "'\"'\"'")
    _, rc = wp(f"post meta update {pid} _dry_recipe_overrides '{escaped}'")

    raw2, _ = wp(f'post meta get {pid} _dry_recipe_overrides')
    ov2 = json.loads(raw2)
    qov = ov2.get('quick_overrides', {})
    ok = 'OK' if rc == 0 and 'Toplinska obrada' in qov else 'ERR'
    print(f'  {pid} {code}: {ok}  Toplinska="{qov.get("Toplinska obrada","?")}"')

# ── Verify omjer on HR-TU-002 (no slanina → should be hidden) ────────────────
print('\nVerify omjer on HR-TU-002 (nema slanine → section treba biti skrivena):')
page = subprocess.run(
    'curl -Ls -H "Host: swab.hr" "http://localhost/recepti-baza/hr-tu-002-turopoljske-kobasice/"',
    shell=True, capture_output=True, text=True
).stdout
comp_count = page.count('dcv5-comp-bar')
print(f'  dcv5-comp-bar count: {comp_count}  (expected: 0)')

print('\nVerify omjer on HR-KU-001 (50/50 → treba biti 50% meso / 50% slanina):')
page2 = subprocess.run(
    'curl -Ls -H "Host: swab.hr" "http://localhost/recepti-baza/hr-ku-001-pokupske-kobasice/"',
    shell=True, capture_output=True, text=True
).stdout
import re as _re
bars = _re.findall(r'dcv5-comp-bar">(.*?)</div>', page2)
print(f'  Bars: {bars}')

print('\nDone.')
