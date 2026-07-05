#!/usr/bin/env python3
"""
Popravak za Pavičić batch 1 (9 postova):
1. HR-DA-001 (3312): krivi post_content (sadrži "## " markdown + "ne dimi") → zamjena čistim tekstom
2. Svih 9 postova: dodati quick_overrides (Dimljenje + Trajanje) iz Pavičić handoffa
"""
import subprocess, json

WP = 'wp --path=/var/www/html --allow-root'

def wp(cmd):
    r = subprocess.run(f'{WP} {cmd}', shell=True, capture_output=True, text=True)
    return r.stdout.strip(), r.returncode

def set_meta(pid, key, value):
    escaped = value.replace("'", "'\"'\"'")
    _, rc = wp(f"post meta update {pid} {key} '{escaped}'")
    return rc == 0

# --- 1. Popravak post_content za HR-DA-001 (3312) ---
# Ukloniti krivi tekst o "luganiga / ne dimi" koji ne odgovara Pavičić str. 101
# Istina: hladno dimljenje do 8 dana + zrenje do 60 dana
DA001_CONTENT = (
    "Sinjska kobasica iz Cetinske krajine priprema se od ručno rezanog svinjskog mesa "
    "i slanine (kockice 10×10 mm) s mediteranskim začinima: ribana limunova korica, "
    "mljeveni klinčić i muškatni oraščić. Hladno dimljenje do 8 dana, zrenje do 60 dana. "
    "Prema Pavičić, str. 101."
)

ok_content = set_meta(3312, 'post_content', DA001_CONTENT)
# WP-CLI post meta ne radi za post_content — koristiti post update
escaped = DA001_CONTENT.replace("'", "'\"'\"'")
_, rc = wp(f"post update 3312 --post_content='{escaped}'")
print(f"HR-DA-001 post_content fix: {'OK' if rc == 0 else 'ERR'}")
verify, _ = wp('post get 3312 --field=post_content')
print(f"  Verify: {verify[:80]}...")

# --- 2. quick_overrides za svih 9 postova ---
# Vrijednosti doslovno iz Pavičić handoffa (broj dana iz opisa postupka)
QUICK_OVERRIDES = {
    3312: {  # HR-DA-001 Sinjska kobasica str. 101
        'Dimljenje': 'hladno dimljenje do 8 dana',
        'Trajanje': '3–12 dana (zrenje do 60 dana)',
        'Omotač': 'tanka svinjska crijeva, 15–20 cm, prstenast oblik',
    },
    3567: {  # HR-TU-002 Turopoljske kobasice str. 92
        'Dimljenje': 'hladno dimljenje 4–5 dana',
        'Trajanje': '2–7 dana (opcionalno zrenje do 60 dana)',
        'Omotač': 'tanka svinjska crijeva, parovi 20 cm',
    },
    3568: {  # HR-TU-003 Velikogoričke kobasice str. 89
        'Dimljenje': 'hladno dimljenje 5–7 dana',
        'Trajanje': '2–9 dana (opcionalno zrenje do 60 dana)',
        'Omotač': 'tanka svinjska crijeva, parovi 12 cm',
    },
    3569: {  # HR-PO-002 Podravske kobasice str. 102
        'Dimljenje': 'hladno dimljenje 5–7 dana',
        'Trajanje': '3–9 dana (opcionalno zrenje do 60 dana)',
        'Omotač': 'tanka svinjska crijeva, parovi 20 cm',
    },
    3570: {  # HR-KU-001 Pokupske kobasice str. 108
        'Dimljenje': 'hladno dimljenje 7 dana',
        'Trajanje': '8–9 dana',
        'Omotač': 'tanka svinjska crijeva, parovi 18 cm',
    },
    3571: {  # HR-PO-003 Slatinske kobasice str. 112
        'Dimljenje': 'hladno dimljenje 2–5 dana',
        'Trajanje': '3–7 dana (zrenje 40–60 dana)',
        'Omotač': 'tanka svinjska crijeva, parovi 25 cm',
    },
    3572: {  # HR-ZA-001 Zagorske kobasice str. 117
        'Dimljenje': 'hladno dimljenje 5 dana (boja kestena = kraj)',
        'Trajanje': '6–7 dana (opcionalno zrenje > 60 dana)',
        'Omotač': 'tanka svinjska crijeva, parovi 15 cm',
    },
    3573: {  # HR-ZA-002 Zagorske krvavice str. 139
        'Dimljenje': 'bez dimljenja — kuhana kobasica',
        'Trajanje': 'kuhanje 50 min + hlađenje; konzumirati svježe',
        'Omotač': 'tanka svinjska crijeva, vijenac 30 cm',
    },
    3574: {  # HR-TU-004 Turopoljske devenice str. 146
        'Dimljenje': 'bez dimljenja — kuhana kobasica',
        'Trajanje': 'kuhanje 50 min + hlađenje; konzumirati svježe',
        'Omotač': 'tanka svinjska crijeva, vijenac 30 cm',
    },
}

print('\nDodavanje quick_overrides na svih 9 postova:')
for pid, qov in QUICK_OVERRIDES.items():
    code, _ = wp(f'post meta get {pid} _dry_recipe_id')

    raw, _ = wp(f'post meta get {pid} _dry_recipe_overrides')
    try:
        ov = json.loads(raw)
        if not isinstance(ov, dict):
            ov = {}
    except Exception:
        ov = {}
        print(f'  {pid} {code}: WARN could not parse overrides, starting fresh')

    ov['quick_overrides'] = qov

    j = json.dumps(ov, ensure_ascii=False)
    escaped = j.replace("'", "'\"'\"'")
    _, rc = wp(f"post meta update {pid} _dry_recipe_overrides '{escaped}'")

    # Verify
    raw2, _ = wp(f'post meta get {pid} _dry_recipe_overrides')
    ov2 = json.loads(raw2)
    qov2 = ov2.get('quick_overrides', {})
    ok = 'OK' if rc == 0 and 'Dimljenje' in qov2 and 'Trajanje' in qov2 else 'ERR'
    dim = qov2.get('Dimljenje', '?')
    traj = qov2.get('Trajanje', '?')
    print(f'  {pid} {code}: {ok}  Dimljenje="{dim}"|Trajanje="{traj}"')

print('\nDone.')
