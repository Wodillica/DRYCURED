#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Zbirka A — popunjavanje 5 recepata kojima nedostaje stvaran tekst iz PDF-a.

Izvor: 224336073-Proizvodi-Od-Mesa-a5.pdf
Stranice: FR-001 str.15, RO-001 str.19-20, PL-002 str.30-31,
          CZ-001 str.48, RS-VO-001 str.60

Tekst preveden na standardni hrvatski (ijekavica + hrvatski vokabular).
NIKAKAV izmišljeni podatak — sve iz citiranog PDF teksta.
"""

import subprocess, json, sys

DRY_RUN = '--dry-run' in sys.argv
WP = ['wp', '--path=/var/www/html', '--allow-root']


def wp(*args):
    r = subprocess.run(WP + list(args), capture_output=True, text=True)
    return r.stdout.strip()


def log(msg): print(msg, flush=True)


RECIPES = {

# ─── FR-001 — Francuska jetrena kobasica ─────────────────────────────────────
# PDF: str. 15, poglavlje "Jetrene kobasice"
# NAPOMENA u izvoru: količina karanfilića je nejasna — navedena kao "? g"
'FR-001': {
    'post_id': 3652,
    'is_smoked': True,
    'materials': [
        {'name': 'svinjska jetra (sirova)', 'amount': '2,5 kg', 'percent': '—', 'rate': '—', 'note': 'isjeći na manje komade'},
        {'name': 'barena slanina (kockice, čvrsta)', 'amount': '2,5 kg', 'percent': '—', 'rate': '—', 'note': 'isjeći na kocke manje veličine, kratko bariti'},
    ],
    'spices': [
        {'name': 'sol', 'amount': 'po izračunu (per kg)', 'percent': '—', 'rate': '20 g/kg', 'note': ''},
        {'name': 'biber', 'amount': 'po izračunu (per kg)', 'percent': '—', 'rate': '1 g/kg', 'note': ''},
        {'name': 'karanfilić', 'amount': 'Nije specificirano u izvoru', 'percent': '—', 'rate': '? g/kg', 'note': 'u originalu navedeno kao "? g" — količina nije čitljiva'},
        {'name': 'sirovi crni luk', 'amount': 'po izračunu (per kg)', 'percent': '—', 'rate': '10 g/kg', 'note': 'mljeveno zajedno s jetrom'},
    ],
    'casing_note': 'goveđe debelo crijevo ili krajnji dio debelog crijeva svinje, kalibar 55–60 mm',
    'grinding_note': 'jetra i luk: najfiniji otvori ploče; slanina: kockice (ne mljevena)',
    'quick_overrides': {
        'Šarža': '5 kg ukupno (2,5 kg jetre + 2,5 kg slanine)',
        'Barenje': '80 °C, 1 sat',
        'Dimljenje': 'hladan dim — nakon sušenja',
        'Crijevo': 'goveđe debelo / krajnji dio debeloga crijeva svinje, kalibar 55–60 mm',
        'Napomena': 'Alternativni naziv: tamna jetrena kobasica (zbog veće količine jetre)',
    },
    'timeline': [
        {'day': 'Dan 1', 'title': 'Priprema jetre i luka',
         'text': 'Sirovu jetru isjeći na manje komade. Zajedno s lukom propustiti kroz najfinije otvore ploče na stroju za mljevenje mesa.',
         'critical': 'Jetra se koristi sirova — ne bariti unaprijed.'},
        {'day': 'Dan 1', 'title': 'Priprema slanine',
         'text': 'Tvrdu barenu slaninu isjeći na kocke manje veličine. Kratko bariti.',
         'critical': ''},
        {'day': 'Dan 1', 'title': 'Miješanje i punjenje',
         'text': 'Barenu slaninu pomiješati s mljevenom jetrom i lukom. Dodati sol i začine, dobro izmiješati. Puniti u goveđe debelo crijevo ili krajnji dio debelog crijeva svinje, kalibar oko 55–60 mm.',
         'critical': ''},
        {'day': 'Dan 1', 'title': 'Barenje',
         'text': 'Bariti na 80 °C jedan sat.',
         'critical': 'Temperatura vode: 80 °C, trajanje: točno 1 sat.'},
        {'day': 'Dan 1', 'title': 'Hlađenje i sušenje',
         'text': 'Nakon barenja hladiti u hladnoj vodi. Objesiti na štap radi sušenja.',
         'critical': ''},
        {'day': 'Dan 1+', 'title': 'Dimljenje',
         'text': 'Nakon sušenja dimiti na hladnom dimu.',
         'critical': 'Isključivo hladan dim.'},
    ],
},

# ─── RO-001 — Transilvanijska džigernjača ─────────────────────────────────────
# PDF: str. 19–20, poglavlje "Jetrene kobasice / Hurke"
# NAPOMENA: "gronik" = izraz iz izvornog teksta (50 dkg); nije pronađena
#            standardna hrvatska inačica — moguće vrsta svinjskog mesnog
#            prerađevina karakteristična za transilvanijsku tradiciju.
'RO-001': {
    'post_id': 3651,
    'is_smoked': False,
    'materials': [
        {'name': 'svinjska jetra (sirova, mljevena)', 'amount': 'Nije specificirano u kg — 1 set iznutrica', 'percent': '—', 'rate': '—', 'note': 'mljevena sirova'},
        {'name': 'pluća, srce, bubreg (kuhano, mljeveno)', 'amount': 'Nije specificirano u kg — 1 set iznutrica', 'percent': '—', 'rate': '—', 'note': 'skuhati u slanoj vodi, ocijediti, samljeti'},
        {'name': 'riža', 'amount': '20 dkg', 'percent': '—', 'rate': '—', 'note': 'bariti, isprati hladnom vodom'},
        {'name': 'crni luk', 'amount': '20 dkg', 'percent': '—', 'rate': '—', 'note': 'sitno isjeći ili izribati, dinstati na masnoći do staklastog'},
        {'name': 'gronik', 'amount': '50 dkg', 'percent': '—', 'rate': '—', 'note': 'izraz iz izvornog teksta — vrsta mesnog prerađevina u transilvanijskoj tradiciji; hrvatska inačica naziva nije potvrđena'},
        {'name': 'suho grožđe', 'amount': '10 dkg', 'percent': '—', 'rate': '—', 'note': ''},
        {'name': 'kruh bez kore', 'amount': '1 kriška', 'percent': '—', 'rate': '—', 'note': 'isjeći na sitne kocke, dodati masi'},
        {'name': 'kiselkaste jabuke srednje veličine', 'amount': '6 komada', 'percent': '—', 'rate': '—', 'note': 'oguliti i izribati'},
    ],
    'spices': [
        {'name': 'sol', 'amount': '6–8 dkg', 'percent': '—', 'rate': '—', 'note': 'po ukusu'},
        {'name': 'grubo mljeveni crni biber', 'amount': '1 žličica', 'percent': '—', 'rate': '—', 'note': ''},
        {'name': 'slatka mljevena paprika (aleva)', 'amount': '1 žličica', 'percent': '—', 'rate': '—', 'note': ''},
    ],
    'casing_note': 'crijeva za hurke — mekano puniti, čvrsto vezati',
    'grinding_note': 'kuhano meso: samljeveno; jetra: sirova, samljevena',
    'quick_overrides': {
        'Barenje': '20–25 minuta',
        'Dimljenje': 'Nije specificirano u izvoru — recept završava barenjem i hlađenjem',
        'Čuvanje': 'hladno mjesto, prosušeno na dasci',
        'Posebnost recepta': 'Transilvanijska tradicija: jabuke, suho grožđe i riža u nadevu',
        'Napomena o sastojku': '"Gronik" (50 dkg) — izraz iz izvornog teksta; hrvatska inačica nije potvrđena',
    },
    'timeline': [
        {'day': 'Dan 1', 'title': 'Kuhanje mesa',
         'text': 'Svo meso (pluća, srce, bubreg) skuhati u slanoj vodi. Ocijediti i samljeti. Jetru samljeti sirovu.',
         'critical': 'Jetra se mlje sirova, ostalo kuhano.'},
        {'day': 'Dan 1', 'title': 'Priprema ostalih sastojaka',
         'text': 'Kruh isjeći na sitne kocke i dodati masi. Jabuke oguliti i izribati. Luk očistiti, sitno isjeći ili izribati i dinstati na masnoći dok ne postane staklast. Opranu rižu bariti i isprati hladnom vodom.',
         'critical': ''},
        {'day': 'Dan 1', 'title': 'Miješanje i punjenje',
         'text': 'Sve pomiješati sa začinima i dodati toliko juhe (čorbe) da masa bude mekana. Mekano puniti u crijeva za hurke i čvrsto vezati.',
         'critical': 'Masa mora biti mekana — regulirati gustoću juhom.'},
        {'day': 'Dan 1', 'title': 'Barenje i hlađenje',
         'text': 'Bariti 20–25 minuta. Ohladiti u hladnoj vodi. Na dasci prosušiti i čuvati na hladnom mjestu.',
         'critical': 'Dimljenje NIJE navedeno u izvoru.'},
    ],
},

# ─── CZ-001 — Praška šunka ───────────────────────────────────────────────────
# PDF: str. 48, poglavlje "Polutrajni proizvodi od mesa"
'CZ-001': {
    'post_id': 3653,
    'is_smoked': True,
    'materials': [
        {'name': 'šunka od ošurene svinje', 'amount': '4–5 kg', 'percent': '—', 'rate': '—', 'note': 'oblikovana šunka; isključivo od ošurene svinje'},
    ],
    'spices': [
        {'name': 'sol', 'amount': '20 dkg', 'percent': '—', 'rate': '—', 'note': ''},
        {'name': 'kristal šećer', 'amount': '5 dkg', 'percent': '—', 'rate': '—', 'note': 'utrljavati u šunku na početku — zadržava svjetlu boju'},
        {'name': 'biber u zrnu', 'amount': '1,5 dkg', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'korijander', 'amount': '1,5 dkg', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'borove sjemenke', 'amount': '1,5 dkg', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'lovorov list', 'amount': '3 lista', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'piment (najkvirc)', 'amount': '5 g', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'đumbir', 'amount': '5 g', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'bijeli luk', 'amount': '4 režnja', 'percent': '—', 'rate': '—', 'note': 'izgnječiti, u salamuri'},
        {'name': 'crni luk', 'amount': '1 mala glavica', 'percent': '—', 'rate': '—', 'note': 'očistiti, isjeći na kolutove, u salamuri'},
    ],
    'liquids': [
        {'name': 'ocat (sirće)', 'amount': '2,5 dl', 'percent': '—', 'rate': '—', 'note': 'u salamuri'},
        {'name': 'voda (za salamuru)', 'amount': '2,5 l', 'percent': '—', 'rate': '—', 'note': 'prokuhati sa začinima i octom, ohladiti'},
    ],
    'casing_note': 'Nije specificirano u izvoru — šunka se ne puni u crijevo',
    'grinding_note': 'Nije specificirano u izvoru — šunka se ne melje',
    'quick_overrides': {
        'Šarža': '1 šunka 4–5 kg (isključivo od ošurene svinje)',
        'Faza 1 — šećer': '24 sata, hladno',
        'Faza 2 — soljenje': '2 tjedna, svakodnevno okretanje',
        'Faza 3 — salamura': '4 tjedna, svakodnevno okretanje',
        'Dimljenje': 'slab, hladan dim — samo dok boja ne potamni (kratko)',
        'Napomena': 'Šunka mora biti od ošurene svinje — to je uvjet recepta',
    },
    'timeline': [
        {'day': 'Dan 1', 'title': 'Trljanje šećerom',
         'text': 'U oblikovanu šunku utrljati kristal šećer. Čuvati na hladnom 24 sata. Šećer zadržava svjetlu boju mesa.',
         'critical': 'Šunka mora biti od ošurene svinje.'},
        {'day': 'Dan 2', 'title': 'Trljanje solju',
         'text': 'Utrljati sol. Svakodnevno okretati u nastaloj tekućini kroz 2 tjedna.',
         'critical': 'Okretati svaki dan bez iznimke.'},
        {'day': 'Dan 16', 'title': 'Priprema salamure',
         'text': 'U 2,5 l vode staviti sve začine, crni luk (kolutovi), izgnječeni bijeli luk i ocat. Prokuhati i ohladiti. Ohlađenom salamurom preliti šunku. Ako salamura ne prekrije šunku, dopuniti prokuhanom i ohlađenom vodom.',
         'critical': 'Salamura mora biti potpuno hladna pri prelijevanju.'},
        {'day': 'Dan 16–44', 'title': 'Salamurenje',
         'text': 'Šunka stoji u salamuri 4 tjedna. Okretati svaki dan.',
         'critical': 'Šunka mora biti potpuno uronjena u salamuru.'},
        {'day': 'Dan 44', 'title': 'Ocijeđivanje i sušenje',
         'text': 'Šunku izvaditi iz salamure, ocijediti i prosušiti.',
         'critical': ''},
        {'day': 'Dan 44+', 'title': 'Dimljenje',
         'text': 'Staviti na slab i hladan dim samo dok boja ne potamni. Dimljenje je kratko — cilj je boja, ne konzervacija.',
         'critical': 'Kratko dimljenje — samo da boja potamni. Hladan dim.'},
    ],
},

# ─── RS-VO-001 — Ratarske kobasice (vojvođanske) ─────────────────────────────
# PDF: str. 60, poglavlje "Kobasice"
# NAPOMENA: Recept u izvoru upućuje na tehniku sremskih kobasica —
#           detaljan postupak opisan u RS-SM-001 (Sremske kobasice, post 3635)
'RS-VO-001': {
    'post_id': 3656,
    'is_smoked': True,
    'materials': [
        {'name': 'svinjski but i masnije meso (oko rebara, vrata i sl.)', 'amount': '8,5 kg', 'percent': '—', 'rate': '—', 'note': ''},
        {'name': 'svježa slanina bez kore', 'amount': '1,5 kg', 'percent': '—', 'rate': '—', 'note': 'sirova slanina'},
    ],
    'spices': [
        {'name': 'mljeveni crni biber', 'amount': '25 g', 'percent': '—', 'rate': '2,5 g/kg', 'note': ''},
        {'name': 'sol', 'amount': '200 g', 'percent': '—', 'rate': '20 g/kg', 'note': ''},
        {'name': 'slatka aleva paprika', 'amount': '100 g', 'percent': '—', 'rate': '10 g/kg', 'note': ''},
        {'name': 'bijeli luk', 'amount': '1 glavica', 'percent': '—', 'rate': '—', 'note': ''},
    ],
    'casing_note': 'tanka svinjska crijeva',
    'grinding_note': 'Nije specificirano u izvoru — primijeniti tehniku sremskih kobasica (Vidi: RS-SM-001)',
    'quick_overrides': {
        'Šarža': '10 kg (8,5 kg mesa + 1,5 kg slanine)',
        'Tehnika': 'Postupak istovjetan sremskim kobasicama (dimljenje + sušenje)',
        'Napomena': 'Izvor ne navodi detaljan opis koraka — upućuje na "postupak sremskih kobasica" (RS-SM-001)',
        'Upotreba': 'Dobre i kuhane u raznim jelima; izvrsne kao međuobrok pri radu ili izletu',
    },
    'timeline': [
        {'day': 'Dan 1', 'title': 'Priprema i mljevenje',
         'text': 'Svinjski but i masnije meso (oko rebara, vrata) te sirovu slaninu bez kore samljeviti. Dodati sol, papriku, biber i bijeli luk. Puniti u tanka svinjska crijeva.',
         'critical': 'Postupak priprema istovjetan sremskim kobasicama (RS-SM-001) — detalje vidjeti tamo.'},
        {'day': 'Dan 1+', 'title': 'Dimljenje i sušenje',
         'text': 'Dimiti i sušiti prema tehnici sremskih kobasica.',
         'critical': 'Izvor ne specificira trajanje dimljenja i sušenja za ovaj recept posebno.'},
    ],
},

# ─── PL-002 — Poljska specijal kobasica za pečenje ───────────────────────────
# PDF: str. 30–31, poglavlje "Kobasice za pečenje"
'PL-002': {
    'post_id': 3649,
    'is_smoked': True,
    'materials': [
        {'name': 'posno svinjsko meso', 'amount': '2 kg', 'percent': '40 %', 'rate': '—', 'note': 'mljeveno kroz 8 mm ploče'},
        {'name': 'masno svinjsko meso', 'amount': '3 kg', 'percent': '60 %', 'rate': '—', 'note': 'mljeveno kroz 5 mm ploče'},
    ],
    'spices': [
        {'name': 'kuhinjska sol', 'amount': 'po izračunu', 'percent': '—', 'rate': '22 g/kg', 'note': 'dio se dodaje pri soljenju, ostatak pri miješanju'},
        {'name': 'šećer', 'amount': 'po izračunu', 'percent': '—', 'rate': '2 g/kg', 'note': ''},
        {'name': 'crni biber', 'amount': 'po izračunu', 'percent': '—', 'rate': '1,5 g/kg', 'note': ''},
        {'name': 'muskatni oraščić', 'amount': 'po izračunu', 'percent': '—', 'rate': '0,5 g/kg', 'note': ''},
        {'name': 'kim', 'amount': 'po izračunu', 'percent': '—', 'rate': '0,5 g/kg', 'note': ''},
    ],
    'casing_note': 'svinjska ili ovčja tanka crijeva (sajtling), kalibar oko 25 mm; duljina: 60–70 cm po komadu (oblikovano uvrtanjem)',
    'grinding_note': 'posno meso: ploče 8 mm; masno meso: ploče 5 mm',
    'quick_overrides': {
        'Šarža': '5 kg (2 kg posnog + 3 kg masnog svinjskog)',
        'Rešetka (posno)': '8 mm',
        'Rešetka (masno)': '5 mm',
        'Odmaranje': '30–60 minuta na sobnoj temperaturi, zatim 2–6 °C',
        'Dimljenje': 'vruć dim, 60 °C — 50–60 minuta',
        'Pečenje': '75–90 °C — 20 minuta',
        'Dozrijevanje': 'tjedan dana, suha prostorija 12–18 °C',
        'Boja gotovog proizvoda': 'tamnosmeđa',
    },
    'timeline': [
        {'day': 'Dan 1', 'title': 'Soljenje mesa',
         'text': 'Meso posoliti i ostaviti da se usuoli (prema istom postupku kao i prethodna kobasica za pečenje u izvoru).',
         'critical': ''},
        {'day': 'Dan 1', 'title': 'Mljevenje',
         'text': 'Mršavo meso samljeviti kroz ploče od 8 mm, masno meso kroz ploče od 5 mm.',
         'critical': 'Dva različita promjera ploče — ne miješati.'},
        {'day': 'Dan 1', 'title': 'Miješanje i punjenje',
         'text': 'Ostatak soli i začine izmiješati s mljevenim mesom. Sve puniti u svinjska ili ovčja tanka crijeva (sajtling), kalibar oko 25 mm. Duljinu formirati na 60–70 cm uvrtanjem.',
         'critical': ''},
        {'day': 'Dan 1', 'title': 'Odmaranje i hlađenje',
         'text': 'Kobasice ostaviti 30–60 minuta da vise na sobnoj temperaturi, potom čuvati na 2–6 °C.',
         'critical': ''},
        {'day': 'Dan 1–2', 'title': 'Dimljenje i pečenje',
         'text': 'Dimiti na vrućem dimu na 60 °C prvih 50–60 minuta. Zatim peći na 75–90 °C 20 minuta. Kobasica dobiva tamnosmeđu boju.',
         'critical': 'Temperatura dimljenja: 60 °C (vruć dim, ne hladan).'},
        {'day': 'Dan 2–9', 'title': 'Dozrijevanje',
         'text': 'Čuvati u suhoj prostoriji na 12–18 °C tjedan dana.',
         'critical': ''},
    ],
},

}  # end RECIPES


# ─── Build and update _dry_recipe_overrides ───────────────────────────────────
log(f"\n{'='*60}")
log("Zbirka A — fill missing 5 recipes (real PDF data)")
log(f"{'='*60}\n")

ok = 0
skip = 0

for code, data in RECIPES.items():
    post_id = data['post_id']

    overrides = {}
    if data.get('is_smoked') is not None:
        overrides['is_smoked'] = data['is_smoked']
    if data.get('materials'):
        overrides['materials'] = data['materials']
    if data.get('spices'):
        overrides['spices'] = data['spices']
    if data.get('liquids'):
        overrides['liquids'] = data['liquids']
    if data.get('casing_note'):
        overrides['casing_note'] = data['casing_note']
    if data.get('grinding_note'):
        overrides['grinding_note'] = data['grinding_note']
    if data.get('quick_overrides'):
        overrides['quick_overrides'] = data['quick_overrides']
    if data.get('timeline'):
        overrides['timeline'] = data['timeline']

    json_str = json.dumps(overrides, ensure_ascii=False)

    if DRY_RUN:
        log(f"[DRY] {code} ({post_id}) — {len(overrides)} keys: {list(overrides.keys())}")
        log(f"      JSON size: {len(json_str)} chars")
        ok += 1
        continue

    result = subprocess.run(
        WP + ['post', 'meta', 'update', str(post_id), '_dry_recipe_overrides', json_str],
        capture_output=True, text=True
    )
    if result.returncode in (0, 1) or 'Updated' in result.stdout or 'updated' in result.stdout:
        log(f"[OK] {code} ({post_id}) — {list(overrides.keys())}")
        ok += 1
    else:
        log(f"[ERR] {code} ({post_id}) — {result.stderr[:200]}")
        skip += 1

log(f"\nDone. Updated: {ok}, Errors: {skip}")
log("\nNakon pokretanja, verificirati na:")
for code, data in RECIPES.items():
    pid = data['post_id']
    log(f"  {code}: https://drycured.com/?post_type=dry_recipe&p={pid}&preview=true")
