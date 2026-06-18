# DRYCURED Recipe Type Router — stabilized audit v1.1

Ovaj izvještaj je read-only. Ne mijenja WordPress i služi kao stabilizirana radna lista prije pojedinačne obrade recepata.

## Izvori

- Audit v1 CSV: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/recipe_type_readonly_audit_v1.csv`
- Calibration v1.1 CSV: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_calibration_v1_1_2026-06-18_17-54-00/recipe_type_router_calibration_v1_1.csv`

## Sažetak

- Ukupno recepata: 937
- Promijenjeno u odnosu na audit v1: 167
- Javno objavljeni PASS kandidati: 30
- Javno objavljeni FAIL/blokirani: 382

## Finalni broj po tipu

- FISH_OR_SEAFOOD: 24
- GROUND_MEAT_OR_CASING: 446
- NEEDS_CLASSIFICATION: 173
- THERMAL_PROCESSED: 75
- WHOLE_CUT: 219

## Gate rezultat

- FAIL: 840
- PASS: 97

## Type source rezultat

- BLOCK: 34
- KEEP: 752
- RECLASSIFY: 151

## Prvih 60 promjena tipa u odnosu na v1

| Post ID | Status | V1 tip | V1.1 tip | Gate | Prioritet | Naslov | Razlog |
|---:|---|---|---|---|---|---|---|
| 1981 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | TRADICIJSKIH TALIJANSKIH RECEPATA ZA SUHOMESNATE PROIZVODE | NOISE_OR_COLLECTION:tradicijskih |
| 1986 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | LARDO DI COLONNATA | WHOLE_CUT_TITLE:lardo |
| 1987 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: MORTADELLA BOLOGNA | THERMAL_TITLE:mortadella |
| 1991 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | GUANCIALE ROMANO | WHOLE_CUT_TITLE:guanciale |
| 1994 | draft | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Islandska Hangikjöt Kobasica | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:hangikjot |
| 1995 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: Posavska Krvavica sa Šumskim Začinima | THERMAL_TITLE:krvavica |
| 1997 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | Etnografska studija kulinarskog naslijeđa | NOISE_OR_COLLECTION:etnografska studija |
| 2001 | draft | WHOLE_CUT | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Kholodets (Холодець) &#8211; Hladetina od mesa | THERMAL_TITLE:hladetina |
| 2004 | draft | WHOLE_CUT | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Pashtetna (Паштетна) &#8211; Pečena mesna pašteta | THERMAL_TITLE:pasteta |
| 2005 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | UJEDINJENO KRALJEVSTVO &#8211; Tradicionalni recepti | NOISE_OR_COLLECTION:tradicionalni recepti,ujedinjeno kraljevstvo |
| 2011 | draft | WHOLE_CUT | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | NOISE_OR_COLLECTION:kompleksna zbirka |
| 2012 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | FRANCUSKA &#8211; Tradicionalni recepti za saucisson | NOISE_OR_COLLECTION:tradicionalni recepti,recepti za,francuska |
| 2014 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | NOISE_OR_COLLECTION:kompleksna zbirka |
| 2015 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | ČEŠKA &#8211; Tradicionalni recepti | NOISE_OR_COLLECTION:tradicionalni recepti,ceska |
| 2017 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Drisheen (Tradicionalna irska krvavica od ovčje krvi) | THERMAL_TITLE:krvavica,drisheen |
| 2022 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | Recepti za salame i suhomesnate proizvode iz europskih zemalja | NOISE_OR_COLLECTION:recepti za |
| 2025 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | Recepti za salame i suhomesnate proizvode iz evropskih zemalja | NOISE_OR_COLLECTION:recepti za |
| 2027 | draft | NEEDS_CLASSIFICATION | GROUND_MEAT_OR_CASING | FAIL | C_NONPUBLIC_BLOCKED | Nemački recept za Mettwurst | GROUND_TITLE:mettwurst |
| 2032 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa (Kapula) | WHOLE_CUT_TITLE:coppa |
| 2035 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Tradicionalna irska krvavica (Black Pudding) | THERMAL_TITLE:krvavica,black pudding |
| 2036 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Irska bijela krvavica (White Pudding) | THERMAL_TITLE:krvavica,white pudding |
| 2037 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Hangikjöt (Dimljeni janjeti but) | WHOLE_CUT_TITLE:dimljeni janjeti but |
| 2044 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: NÜRNBERGER BRATWURST (NIRNBERŠKA KOBASICA ZA PEČENJE) | THERMAL_TITLE:bratwurst,kobasica za pecenje |
| 2045 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: Salata sa Schinkenspeck kobasicama | WHOLE_CUT_TITLE:speck,schinken |
| 2047 | publish | NEEDS_CLASSIFICATION | THERMAL_PROCESSED | FAIL | A_PUBLIC_BLOCKED | Domaći Bratwurst | THERMAL_TITLE:bratwurst |
| 2048 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Domaći Leberwurst (Pašteta od jetre) | THERMAL_TITLE:leberwurst,pasteta |
| 2055 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa Piacentina &#8211; Piacentinska Coppa | WHOLE_CUT_TITLE:coppa |
| 2058 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Guanciale Romano &#8211; Rimski Guanciale | WHOLE_CUT_TITLE:guanciale |
| 2064 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | COPPA (CAPOCOLLO) &#8211; TALIJANSKA VRATINA | WHOLE_CUT_TITLE:coppa,capocollo,vratina |
| 2070 | draft | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | BOUDIN BLANC DE BRUXELLES (BIJELA KRVAVICA IZ BRUXELLESA) | THERMAL_TITLE:krvavica |
| 2073 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Kistanje Začinjena Panceta (Specifični sušeni vrat) | WHOLE_CUT_TITLE:panceta,suseni vrat |
| 2074 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Petrovo Polje Začinska Slanina (Specifični začinjeni komad) | WHOLE_CUT_TITLE:slanina |
| 2079 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | COPPA CORSE | WHOLE_CUT_TITLE:coppa |
| 2081 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: PÂTÉ DE CAMPAGNE BRETON | THERMAL_TITLE:pate |
| 2083 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Gračac Začinjena Panceta (Specifični sušeni vrat) | WHOLE_CUT_TITLE:panceta,suseni vrat |
| 2084 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Plitvice Začinska Slanina (Specifični začinjeni komad) | WHOLE_CUT_TITLE:slanina |
| 2087 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Podravska Krvavica | THERMAL_TITLE:krvavica |
| 2090 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: KASZANKA (POLJSKA KRVAVICA) | THERMAL_TITLE:krvavica,kaszanka |
| 2091 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: Morcela de Assar (Krvavica za Pečenje) | THERMAL_TITLE:krvavica,morcela |
| 2114 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: ST. GALLER OLMA-BRATWURST (TELEĆA KOBASICA ZA PEČENJE) | THERMAL_TITLE:bratwurst,kobasica za pecenje |
| 2116 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | COPPA PIACENTINA | WHOLE_CUT_TITLE:coppa |
| 2117 | draft | NEEDS_CLASSIFICATION | GROUND_MEAT_OR_CASING | FAIL | C_NONPUBLIC_BLOCKED | Finocchiona Toscana IGP — edukativni profil | GROUND_TITLE:finocchiona |
| 2353 | publish | NEEDS_CLASSIFICATION | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Pastërma (Albansko suho meso) | WHOLE_CUT_TITLE:suho meso |
| 2415 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | ?? NJEMAČKA &#8211; Tradicionalni recepti za Wurst | NOISE_OR_COLLECTION:tradicionalni recepti,recepti za,njemacka |
| 2416 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FAIL | C_NONPUBLIC_BLOCKED | ?? ITALIJA &#8211; Tradicionalni recepti za salame | NOISE_OR_COLLECTION:tradicionalni recepti,recepti za,italija |
| 2435 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Crnogorska stelja | WHOLE_CUT_TITLE:stelja |
| 2437 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Drniški Začinjeni Vrat s Planine (Specifični mountainski naresak) | WHOLE_CUT_TITLE:zacinjeni vrat |
| 2449 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Ribbenssteg (Danska dimljena svinjska rebra) | WHOLE_CUT_TITLE:svinjska rebra |
| 2451 | draft | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Blodpølse (Danska krvavica) | THERMAL_TITLE:krvavica,blodpolse |
| 2459 | private | WHOLE_CUT | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Boiled Ham (Tradicionalna irska kuhana šunka) | THERMAL_TITLE:kuhana sunka,boiled ham,kuhana |
| 2460 | pending | NEEDS_CLASSIFICATION | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Westfälischer Schinken s krumpirom u ljusci i hrenom | WHOLE_CUT_TITLE:schinken |
| 2463 | pending | NEEDS_CLASSIFICATION | FISH_OR_SEAFOOD | FAIL | C_NONPUBLIC_BLOCKED | Rogatički Planinsko-Morski Raritet &#8220;Vjetar i More&#8221; | FISH_TITLE:morski |
| 2467 | pending | FISH_OR_SEAFOOD | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | Istarski Morski Pršut &#8220;Neptunov Dar&#8221; | FISH_TITLE:morski;CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:prsut |
| 2469 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | FAIL | C_NONPUBLIC_BLOCKED | Privatno: Haslet (tradicionalna engleska pašteta od svinjskih iznutrica) | THERMAL_TITLE:pasteta,haslet |
| 2479 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | PASS | D_NONPUBLIC_REVIEW_READY | Privatno: Verikäkk (Krvavica s ječmom) | THERMAL_TITLE:krvavica,verikakk |
| 2489 | draft | NEEDS_CLASSIFICATION | GROUND_MEAT_OR_CASING | FAIL | C_NONPUBLIC_BLOCKED | ?? Slovački recept &#8211; Turistická Saláma | GROUND_TITLE:salama |
| 2496 | draft | NEEDS_CLASSIFICATION | GROUND_MEAT_OR_CASING | FAIL | C_NONPUBLIC_BLOCKED | ?? Tradicionalni recept za saucisson sec | GROUND_TITLE:saucisson |
| 2502 | draft | THERMAL_PROCESSED | WHOLE_CUT | FAIL | C_NONPUBLIC_BLOCKED | JOULUKINKKU (BOŽIĆNA ŠUNKA) | WHOLE_CUT_TITLE:sunka |
| 2504 | publish | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | FAIL | A_PUBLIC_BLOCKED | SAVUSIIKA (DIMLJENI SIIKA &#8211; BIJELA RIBA) | FISH_TITLE:riba,savusiika |
| 2509 | pending | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | FAIL | C_NONPUBLIC_BLOCKED | KYLMÄSAVULOHI (HLADNO DIMLJENI LOSOS) | FISH_TITLE:losos,kylmasavulohi |

## Prvih 60 javno blokiranih

| Post ID | Status | V1 tip | V1.1 tip | Gate | Prioritet | Naslov | Razlog |
|---:|---|---|---|---|---|---|---|
| 1988 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SOPPRESSA VICENTINA | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 1991 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | GUANCIALE ROMANO | WHOLE_CUT_TITLE:guanciale |
| 2018 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Irska salama (Spiced Irish Salami) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2021 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Cumberland kobasica | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2023 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ?? Grčki tradicionalni recept &#8211; Loukaniko | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2026 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ?? Španjolski recept za chorizo | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2028 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Češki Špekáček (Špek Kobasica) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2029 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | JAMBON DE BAYONNE | WHOLE_CUT_TITLE:jambon |
| 2032 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa (Kapula) | WHOLE_CUT_TITLE:coppa |
| 2033 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Andouille de Guémené | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2039 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Ukrajinska Domashnia Kovbasa (Domaća Kobasica) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2042 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | BALMOȘ CU SLANINĂ (PALENTA SA SLANINOM) | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2043 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | PFÄLZER SAUMAGEN (FALAČKA SVINJSKA ŽELUDAC-KOBASICA) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2047 | publish | NEEDS_CLASSIFICATION | THERMAL_PROCESSED | FAIL | A_PUBLIC_BLOCKED | Domaći Bratwurst | THERMAL_TITLE:bratwurst |
| 2049 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Hausmacher Mettwurst (Domaća dimljena kobasica za mazanje) | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2051 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ŠKOTSKI LORNE SAUSAGE (ČETVRTASTI KOBASIČASTI ODREZAK) | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2052 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Jamón Ibérico de Bellota &#8211; Iberijski Pršut od Žira | WHOLE_CUT_TITLE:prsut |
| 2053 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Chorizo de Pamplona &#8211; Čorizo iz Pamplone | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2055 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Coppa Piacentina &#8211; Piacentinska Coppa | WHOLE_CUT_TITLE:coppa |
| 2056 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Nduja Calabrese &#8211; Kalabrijska Nduja | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2057 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Bresaola Valtellinese &#8211; Valtelinska Bresaola | WHOLE_CUT_TITLE:bresaola |
| 2058 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Guanciale Romano &#8211; Rimski Guanciale | WHOLE_CUT_TITLE:guanciale |
| 2059 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SALAME MILANO &#8211; MILANSKA SALAMA | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2060 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SOPPRESSATA CALABRESE &#8211; KALABRIJSKA SOPPRESSATA | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2061 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | PROSCIUTTO DI PARMA &#8211; PARMSKA ŠUNKA | WHOLE_CUT_TITLE:prosciutto,parma,sunka |
| 2062 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | PANCETTA ARROTOLATA &#8211; ROLANA PANCETA | WHOLE_CUT_TITLE:pancetta,panceta |
| 2063 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | FINOCCHIONA TOSCANA &#8211; TOSKANSKA KOBASICA S KOMORAČEM | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2072 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Njeguški pršut | WHOLE_CUT_TITLE:prsut |
| 2075 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Vrgorački kulen iz zaboravljenih recepata | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2076 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Senjska Morska Panceta &#8220;Bura Intenzitet&#8221; | WHOLE_CUT_TITLE:panceta |
| 2078 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | ANDOUILLE DE VIRE | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2079 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | COPPA CORSE | WHOLE_CUT_TITLE:coppa |
| 2080 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | SAUCISSE DE MORTEAU | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2086 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Prekomurska Slaninska Rolada &#8220;Trnčova&#8221; | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2088 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Koprivnička Kulen | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2089 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | KABANOSY (TANKE SUHE KOBASICE) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2092 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Crikvenička Panceta | WHOLE_CUT_TITLE:panceta |
| 2093 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Stara Koprivnička Salama &#8220;Šokačka Priča&#8221; | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2094 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Pitomački &#8220;Divlji Kulen&#8221; | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2096 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | SLĂNINĂ AFUMATĂ (Dimljena slanina) | WHOLE_CUT_TITLE:slanina |
| 2097 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | CHIȘCĂ (Puhana želuca s krvlju) | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2100 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Miholjačka Začinska Salama | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2101 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Vinkovački Slavonski Kulen &#8211; Stara obiteljska receptura | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2102 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Slovačka slanina (Slovenská slanina) | WHOLE_CUT_TITLE:slanina |
| 2103 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Kranjska klobasa (Kranjska kobasica) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2104 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Kraški pršut | WHOLE_CUT_TITLE:prsut |
| 2106 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Gorenjska zaseka | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2107 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Vipavska panceta | WHOLE_CUT_TITLE:panceta |
| 2108 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Tolminska salama | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2110 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Crnogorska Slanina (Durmitorska panćeta) | WHOLE_CUT_TITLE:panceta,slanina |
| 2111 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | LANDJÄGER (TRADICIONALNE DIMLJENE KOBASICE) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2113 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | VACHERIN FRIBOURGEOIS METTWURST (DIMLJENA KOBASICA S ALPSKIM SIROM) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2115 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | BRESAOLA DELLA VALTELLINA | WHOLE_CUT_TITLE:bresaola |
| 2116 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | COPPA PIACENTINA | WHOLE_CUT_TITLE:coppa |
| 2353 | publish | NEEDS_CLASSIFICATION | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Pastërma (Albansko suho meso) | WHOLE_CUT_TITLE:suho meso |
| 2355 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Proshuta Shqiptare – albanska pršuta | WHOLE_CUT_TITLE:prsut |
| 2359 | publish | NEEDS_CLASSIFICATION | NEEDS_CLASSIFICATION | FAIL | A_PUBLIC_BLOCKED | Vorarlberger Mostbröckle – vorarlberška dimljena govedina | NEEDS_CLASSIFICATION;NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2398 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Traditionele Droge Worst (Klasična suha kobasica) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2399 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | FAIL | A_PUBLIC_BLOCKED | Rookworst (Dimljena kobasica) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2428 | publish | WHOLE_CUT | WHOLE_CUT | FAIL | A_PUBLIC_BLOCKED | Durmitorska kastradina | PUBLIC_OR_META_INTERNAL_TEXT_HIT |

## Prvih 60 javnih PASS kandidata

| Post ID | Status | V1 tip | V1.1 tip | Gate | Prioritet | Naslov | Razlog |
|---:|---|---|---|---|---|---|---|
| 1982 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | FINOCCHIONA TOSCANA |  |
| 1983 | publish | WHOLE_CUT | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | PROSCIUTTO DI SAN DANIELE | WHOLE_CUT_TITLE:prosciutto,san daniele |
| 1984 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | NDUJA CALABRESE |  |
| 1986 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | LARDO DI COLONNATA | WHOLE_CUT_TITLE:lardo |
| 1990 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | SALAME DI FELINO |  |
| 2037 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Hangikjöt (Dimljeni janjeti but) | WHOLE_CUT_TITLE:dimljeni janjeti but |
| 2064 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | COPPA (CAPOCOLLO) &#8211; TALIJANSKA VRATINA | WHOLE_CUT_TITLE:coppa,capocollo,vratina |
| 2552 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Otočac Začinski Vrat s Planine (Mountainski naresak) | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2604 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Osiječki Ljuti Začinski Vrat s Divljim Travkama | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2606 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Požeški Aromatični Sušeni Vrat s Divljim Travkama | WHOLE_CUT_TITLE:suseni vrat |
| 2694 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Požeški Pikantni Vrat | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2696 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Nasički Fermentirani Vrat | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2703 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Erdutski Fermentirani Vrat s Podunavskim Začinima | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2705 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Vukovarski Fermentirani Vrat s Podunavskim Začinima | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2780 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Slavonski pikantni vrat | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2781 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Esencijalni slavonski sušeni vrat s divljim začinima | WHOLE_CUT_TITLE:suseni vrat |
| 3042 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Jésus de Lyon – debela suha kobasica |  |
| 3083 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | ЕЛЕНСКИ БУТ (Elenski but) | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:but |
| 3094 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Elena filet (Еленски филе) |  |
| 3105 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Apohti (Απόχτι) &#8211; Dimljeni svinjski file |  |
| 3106 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Pastourma (Παστουρμάς) &#8211; Ciparski začinjeni sušeni goveđi file |  |
| 3135 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Pastourmas (Παστουρμάς) |  |
| 3142 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Heilhornað Hangikjöt (Sušena janjeća rebra) | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:rebra,hangikjot |
| 3175 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | PINNEKJØTT (SUŠENA REBRA) | WHOLE_CUT_TITLE:pinnekjott,susena rebra |
| 3188 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Lountza Kafteri (Λούντζα Καυτερή) &#8211; Ljuta dimljena svinjska vratina | WHOLE_CUT_TITLE:vratina,lountza |
| 3205 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | PASTRAMĂ DE OAIE (Sušena ovčetina) |  |
| 3206 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Basturma (Бастурма &#8211; Začinjeno sušeno goveđe meso) |  |
| 3208 | publish | GROUND_MEAT_OR_CASING | GROUND_MEAT_OR_CASING | PASS | B_PUBLIC_REVIEW_READY | Svinaya Vyrezka (Свиная вырезка &#8211; Sušeni svinjski file) |  |
| 3212 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | Lomo Embuchado &#8211; Začinjena Suha Svinjska Leđa | WHOLE_CUT_TITLE:lomo |
| 3216 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | PASS | B_PUBLIC_REVIEW_READY | BÜNDNERFLEISCH (GRAUBÜNDENSKI SUŠENI GOVEĐI BUT) | CALIBRATION_ACCEPTED:TITLE_WHOLE_CUT_SIGNAL:but |

## Sljedeća urednička odluka

Ne raditi javni update. Sljedeći korak je odabrati mali pilot batch po jednom tehnološkom tipu i za svaki recept otvoriti pojedinačni izvorni dosje, recipe.yml i QA izvještaj.
