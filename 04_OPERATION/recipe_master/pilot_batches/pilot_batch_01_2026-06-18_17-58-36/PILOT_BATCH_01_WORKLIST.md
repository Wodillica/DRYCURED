# DRYCURED PILOT_BATCH_01 — radna lista iz stabiliziranog audita v1.1

Ovaj dokument ne mijenja WordPress. Služi samo za izbor prvog malog batcha za pojedinačnu obradu.

## Izvor

- Stabilized audit CSV: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_stabilized_audit_v1_1_2026-06-18_17-56-31/recipe_type_stabilized_audit_v1_1.csv`

## Sažetak

- Ukupno redova: 937
- Javno objavljeni PASS kandidati: 30
- Javno objavljeni FAIL/blokirani: 382
- Svi PASS kandidati, uključujući private/draft/pending: 97

## Javni PASS po tipu

- GROUND_MEAT_OR_CASING: 11
- WHOLE_CUT: 19

## Javni FAIL po tipu

- FISH_OR_SEAFOOD: 15
- GROUND_MEAT_OR_CASING: 246
- NEEDS_CLASSIFICATION: 22
- THERMAL_PROCESSED: 3
- WHOLE_CUT: 96

## Preporuka

Prvi pilot batch treba biti samo `GROUND_MEAT_OR_CASING`, jer za taj tip već postoji potvrđeni referentni javni model: Slavonska domaća kobasica HR-SL-005.

Ne raditi javni update izravno iz ove liste. Za svaki recept treba otvoriti pojedinačni izvorni dosje, recipe.yml i QA izvještaj.

## PILOT_01_RECOMMENDED — mljeveno/usitnjeno meso u omotaču

| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |
|---:|---|---|---|---|---|---|
| 1982 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | FINOCCHIONA TOSCANA | https://drycured.com/recepti-baza/finocchiona-toscana/ |  |
| 1984 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | NDUJA CALABRESE | https://drycured.com/recepti-baza/nduja-calabrese/ |  |
| 1990 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | SALAME DI FELINO | https://drycured.com/recepti-baza/salame-di-felino/ |  |
| 3042 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Jésus de Lyon – debela suha kobasica | https://drycured.com/recepti-baza/jesus-de-lyon-debela-suha-kobasica/ |  |
| 3094 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Elena filet (Еленски филе) | https://drycured.com/recepti-baza/elena-filet/ |  |
| 3105 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Apohti (Απόχτι) &#8211; Dimljeni svinjski file | https://drycured.com/recepti-baza/apohti-dimljeni-svinjski-file/ |  |
| 3106 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Pastourma (Παστουρμάς) &#8211; Ciparski začinjeni sušeni goveđi file | https://drycured.com/recepti-baza/pastourma-ciparski-zacinjeni-suseni-govei-file/ |  |
| 3135 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Pastourmas (Παστουρμάς) | https://drycured.com/recepti-baza/pastourmas/ |  |
| 3205 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | PASTRAMĂ DE OAIE (Sušena ovčetina) | https://drycured.com/recepti-baza/pastrama-de-oaie-susena-ovcetina/ |  |
| 3206 | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Basturma (Бастурма &#8211; Začinjeno sušeno goveđe meso) | https://drycured.com/recepti-baza/basturma-zacinjeno-suseno-govee-meso/ |  |

## Javni PASS — WHOLE_CUT kandidati za kasniji zaseban model

| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |
|---:|---|---|---|---|---|---|
| 1983 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | PROSCIUTTO DI SAN DANIELE | https://drycured.com/recepti-baza/prosciutto-di-san-daniele/ | WHOLE_CUT_TITLE:prosciutto,san daniele |
| 1986 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | LARDO DI COLONNATA | https://drycured.com/recepti-baza/lardo-di-colonnata/ | WHOLE_CUT_TITLE:lardo |
| 2037 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Hangikjöt (Dimljeni janjeti but) | https://drycured.com/recepti-baza/hangikjot-dimljeni-janjeti-but/ | WHOLE_CUT_TITLE:dimljeni janjeti but |
| 2064 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | COPPA (CAPOCOLLO) &#8211; TALIJANSKA VRATINA | https://drycured.com/recepti-baza/coppa-capocollo-talijanska-vratina/ | WHOLE_CUT_TITLE:coppa,capocollo,vratina |
| 2552 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Otočac Začinski Vrat s Planine (Mountainski naresak) | https://drycured.com/recepti-baza/otocac-zacinski-vrat-s-planine-mountainski-naresak/ | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2604 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Osiječki Ljuti Začinski Vrat s Divljim Travkama | https://drycured.com/recepti-baza/osijecki-ljuti-zacinski-vrat-s-divljim-travkama/ | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2606 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Požeški Aromatični Sušeni Vrat s Divljim Travkama | https://drycured.com/recepti-baza/pozeski-aromaticni-suseni-vrat-s-divljim-travkama/ | WHOLE_CUT_TITLE:suseni vrat |
| 2694 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Požeški Pikantni Vrat | https://drycured.com/recepti-baza/pozeski-pikantni-vrat/ | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2696 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Nasički Fermentirani Vrat | https://drycured.com/recepti-baza/nasicki-fermentirani-vrat/ | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2703 | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Erdutski Fermentirani Vrat s Podunavskim Začinima | https://drycured.com/recepti-baza/erdutski-fermentirani-vrat-s-podunavskim-zacinima/ | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |

## Javni PASS — THERMAL_PROCESSED kandidati za kasniji zaseban model

| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |
|---:|---|---|---|---|---|---|

## Javni PASS — FISH_OR_SEAFOOD kandidati za kasniji zaseban model

| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |
|---:|---|---|---|---|---|---|

## Prvih 40 javno blokiranih

| Post ID | Tip | Gate | Prioritet | Naslov | URL | Razlog/blokada |
|---:|---|---|---|---|---|---|
| 1988 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SOPPRESSA VICENTINA | https://drycured.com/recepti-baza/soppressa-vicentina/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 1991 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | GUANCIALE ROMANO | https://drycured.com/recepti-baza/guanciale-romano/ | WHOLE_CUT_TITLE:guanciale |
| 2018 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Irska salama (Spiced Irish Salami) | https://drycured.com/recepti-baza/irska-salama-spiced-irish-salami/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2021 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Cumberland kobasica | https://drycured.com/recepti-baza/cumberland-kobasica/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2023 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ?? Grčki tradicionalni recept &#8211; Loukaniko | https://drycured.com/recepti-baza/grcki-tradicionalni-recept-loukaniko/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2026 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ?? Španjolski recept za chorizo | https://drycured.com/recepti-baza/spanjolski-recept-za-chorizo/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2028 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Češki Špekáček (Špek Kobasica) | https://drycured.com/recepti-baza/ceski-spekacek-spek-kobasica/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2029 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | JAMBON DE BAYONNE | https://drycured.com/recepti-baza/jambon-de-bayonne/ | WHOLE_CUT_TITLE:jambon |
| 2032 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa (Kapula) | https://drycured.com/recepti-baza/coppa-kapula/ | WHOLE_CUT_TITLE:coppa |
| 2033 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Andouille de Guémené | https://drycured.com/recepti-baza/andouille-de-guemene/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2039 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Ukrajinska Domashnia Kovbasa (Domaća Kobasica) | https://drycured.com/recepti-baza/ukrajinska-domashnia-kovbasa-domaca-kobasica/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2042 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | BALMOȘ CU SLANINĂ (PALENTA SA SLANINOM) | https://drycured.com/recepti-baza/balmos-cu-slanina-palenta-sa-slaninom/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2043 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | PFÄLZER SAUMAGEN (FALAČKA SVINJSKA ŽELUDAC-KOBASICA) | https://drycured.com/recepti-baza/pfalzer-saumagen-falacka-svinjska-zeludac-kobasica/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2047 | THERMAL_PROCESSED | FAIL | A_PUBLIC_BLOCKED | Domaći Bratwurst | https://drycured.com/recepti-baza/domaci-bratwurst/ | THERMAL_TITLE:bratwurst |
| 2049 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Hausmacher Mettwurst (Domaća dimljena kobasica za mazanje) | https://drycured.com/recepti-baza/hausmacher-mettwurst-domaca-dimljena-kobasica-za-mazanje/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2051 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ŠKOTSKI LORNE SAUSAGE (ČETVRTASTI KOBASIČASTI ODREZAK) | https://drycured.com/recepti-baza/skotski-lorne-sausage-cetvrtasti-kobasicasti-odrezak/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2052 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Jamón Ibérico de Bellota &#8211; Iberijski Pršut od Žira | https://drycured.com/recepti-baza/jamon-iberico-de-bellota-iberijski-prsut-od-zira/ | WHOLE_CUT_TITLE:prsut |
| 2053 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Chorizo de Pamplona &#8211; Čorizo iz Pamplone | https://drycured.com/recepti-baza/chorizo-de-pamplona-corizo-iz-pamplone/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2055 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa Piacentina &#8211; Piacentinska Coppa | https://drycured.com/recepti-baza/coppa-piacentina-piacentinska-coppa/ | WHOLE_CUT_TITLE:coppa |
| 2056 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Nduja Calabrese &#8211; Kalabrijska Nduja | https://drycured.com/recepti-baza/nduja-calabrese-kalabrijska-nduja/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2057 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Bresaola Valtellinese &#8211; Valtelinska Bresaola | https://drycured.com/recepti-baza/bresaola-valtellinese-valtelinska-bresaola/ | WHOLE_CUT_TITLE:bresaola |
| 2058 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Guanciale Romano &#8211; Rimski Guanciale | https://drycured.com/recepti-baza/guanciale-romano-rimski-guanciale/ | WHOLE_CUT_TITLE:guanciale |
| 2059 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SALAME MILANO &#8211; MILANSKA SALAMA | https://drycured.com/recepti-baza/salame-milano-milanska-salama/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2060 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SOPPRESSATA CALABRESE &#8211; KALABRIJSKA SOPPRESSATA | https://drycured.com/recepti-baza/soppressata-calabrese-kalabrijska-soppressata/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2061 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | PROSCIUTTO DI PARMA &#8211; PARMSKA ŠUNKA | https://drycured.com/recepti-baza/prosciutto-di-parma-parmska-sunka/ | WHOLE_CUT_TITLE:prosciutto,parma,sunka |
| 2062 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | PANCETTA ARROTOLATA &#8211; ROLANA PANCETA | https://drycured.com/recepti-baza/pancetta-arrotolata-rolana-panceta/ | WHOLE_CUT_TITLE:pancetta,panceta |
| 2063 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | FINOCCHIONA TOSCANA &#8211; TOSKANSKA KOBASICA S KOMORAČEM | https://drycured.com/recepti-baza/finocchiona-toscana-toskanska-kobasica-s-komoracem/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2072 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Njeguški pršut | https://drycured.com/recepti-baza/njeguski-prsut/ | WHOLE_CUT_TITLE:prsut |
| 2075 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Vrgorački kulen iz zaboravljenih recepata | https://drycured.com/recepti-baza/vrgoracki-kulen-iz-zaboravljenih-recepata/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2076 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Senjska Morska Panceta &#8220;Bura Intenzitet&#8221; | https://drycured.com/recepti-baza/senjska-morska-panceta-bura-intenzitet/ | WHOLE_CUT_TITLE:panceta |
| 2078 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ANDOUILLE DE VIRE | https://drycured.com/recepti-baza/andouille-de-vire/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2079 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | COPPA CORSE | https://drycured.com/recepti-baza/coppa-corse/ | WHOLE_CUT_TITLE:coppa |
| 2080 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SAUCISSE DE MORTEAU | https://drycured.com/recepti-baza/saucisse-de-morteau/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2086 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Prekomurska Slaninska Rolada &#8220;Trnčova&#8221; | https://drycured.com/recepti-baza/prekomurska-slaninska-rolada-trncova/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2088 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Koprivnička Kulen | https://drycured.com/recepti-baza/koprivnicka-kulen/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2089 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | KABANOSY (TANKE SUHE KOBASICE) | https://drycured.com/recepti-baza/kabanosy-tanke-suhe-kobasice/ | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2092 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Crikvenička Panceta | https://drycured.com/recepti-baza/crikvenicka-panceta/ | WHOLE_CUT_TITLE:panceta |
| 2093 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Stara Koprivnička Salama &#8220;Šokačka Priča&#8221; | https://drycured.com/recepti-baza/stara-koprivnicka-salama-sokacka-prica/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2094 | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Pitomački &#8220;Divlji Kulen&#8221; | https://drycured.com/recepti-baza/pitomacki-divlji-kulen/ | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2096 | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | SLĂNINĂ AFUMATĂ (Dimljena slanina) | https://drycured.com/recepti-baza/slanina-afumata-dimljena-slanina/ | WHOLE_CUT_TITLE:slanina |

## Izlazne datoteke

- `pilot_01_recommended_ground_public_pass.csv`
- `public_pass_all_types.csv`
- `public_fail_blocked.csv`
- `public_pass_ground.csv`
- `public_pass_whole_cut.csv`
- `public_pass_thermal.csv`
- `public_pass_fish.csv`
- `pilot_batch_01_summary.json`
