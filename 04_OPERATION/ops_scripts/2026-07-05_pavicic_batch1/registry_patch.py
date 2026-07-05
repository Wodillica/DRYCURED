#!/usr/bin/env python3
import subprocess, json, re

f = '/var/www/html/wp-content/mu-plugins/aaa-drycured-registry-01B.php'
with open(f, 'r', encoding='utf-8') as fh:
    content = fh.read()

new_entries = (
    "        'HR-TU-002' => ['order' => 131, 'title' => 'Turopoljske kobasice', 'family' => 'kobasica', 'slug' => 'hr-tu-002-turopoljske-kobasice', 'region' => 'Turopolje'],\n"
    "        'HR-TU-003' => ['order' => 132, 'title' => 'Velikogoricke kobasice', 'family' => 'kobasica', 'slug' => 'hr-tu-003-velikogoricke-kobasice', 'region' => 'Turopolje'],\n"
    "        'HR-TU-004' => ['order' => 133, 'title' => 'Turopoljske devenice', 'family' => 'kobasica', 'slug' => 'hr-tu-004-turopoljske-devenice', 'region' => 'Turopolje'],\n"
    "        'HR-PO-002' => ['order' => 141, 'title' => 'Podravske kobasice', 'family' => 'kobasica', 'slug' => 'hr-po-002-podravske-kobasice', 'region' => 'Podravina'],\n"
    "        'HR-PO-003' => ['order' => 142, 'title' => 'Slatinske kobasice', 'family' => 'kobasica', 'slug' => 'hr-po-003-slatinske-kobasice', 'region' => 'Podravina (Slatina)'],\n"
    "        'HR-KU-001' => ['order' => 151, 'title' => 'Pokupske kobasice', 'family' => 'kobasica', 'slug' => 'hr-ku-001-pokupske-kobasice', 'region' => 'Pokuplje'],\n"
    "        'HR-ZA-001' => ['order' => 161, 'title' => 'Zagorske kobasice', 'family' => 'kobasica', 'slug' => 'hr-za-001-zagorske-kobasice', 'region' => 'Zagorje'],\n"
    "        'HR-ZA-002' => ['order' => 162, 'title' => 'Zagorske krvavice', 'family' => 'krvavica', 'slug' => 'hr-za-002-zagorske-krvavice', 'region' => 'Zagorje'],\n"
)

anchor = "        'HR-MM-001'"
if anchor not in content:
    print('ERR: anchor HR-MM-001 not found in file')
    exit(1)

new_content = content.replace(anchor, new_entries + anchor, 1)

with open(f, 'w', encoding='utf-8') as fh:
    fh.write(new_content)

print('OK: registry patched')
codes = ['HR-TU-002','HR-TU-003','HR-TU-004','HR-PO-002','HR-PO-003','HR-KU-001','HR-ZA-001','HR-ZA-002']
for c in codes:
    print(f'  {c}: {"FOUND" if c in new_content else "MISSING"}')

# --- Now set _dry_recipe_id and drycured_public_master_version on all 9 posts ---
WP = 'wp --path=/var/www/html --allow-root'

def wp(cmd):
    r = subprocess.run(f'{WP} {cmd}', shell=True, capture_output=True, text=True)
    return r.stdout.strip(), r.returncode

posts = {
    3312: 'HR-DA-001',
    3567: 'HR-TU-002',
    3568: 'HR-TU-003',
    3569: 'HR-PO-002',
    3570: 'HR-KU-001',
    3571: 'HR-PO-003',
    3572: 'HR-ZA-001',
    3573: 'HR-ZA-002',
    3574: 'HR-TU-004',
}

print('\nSetting meta on posts:')
for pid, code in posts.items():
    _, rc1 = wp(f"post meta update {pid} _dry_recipe_id '{code}'")
    _, rc2 = wp(f"post meta update {pid} drycured_public_master_version 'v1.1_products_only'")
    rid, _ = wp(f'post meta get {pid} _dry_recipe_id')
    ver, _ = wp(f'post meta get {pid} drycured_public_master_version')
    ok = 'OK' if rc1 == 0 and rc2 == 0 and rid == code and ver == 'v1.1_products_only' else 'ERR'
    print(f'  {pid} {code}: {ok}  id={rid}  ver={ver}')

# Verify registry via WP-CLI PHP eval
print('\nVerify registry includes new codes:')
php = "echo implode(',', array_keys(dcv12_batch01_recipe_registry()));"
out, _ = wp(f"eval '{php}'")
for c in ['HR-ZA-001', 'HR-TU-002', 'HR-PO-002', 'HR-KU-001']:
    print(f'  {c}: {"FOUND" if c in out else "MISSING"}')
