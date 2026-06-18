# DRYCURED Recipe Type Router — calibration v1.1

Ovaj izvještaj ne mijenja WordPress. Služi samo za kalibraciju klasifikatora prije bilo kakvog batch uređivanja.

## Sažetak

- Izvorni CSV: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/recipe_type_readonly_audit_v1.csv`
- Ukupno redova: 937
- Prijedloga promjene tipa: 162
- Reclassify prijedloga: 146
- Blokada zbog kolekcije/testa/nerecepta: 16

## Trenutni broj po tipu

- FISH_OR_SEAFOOD: 5
- GROUND_MEAT_OR_CASING: 565
- NEEDS_CLASSIFICATION: 185
- THERMAL_PROCESSED: 39
- WHOLE_CUT: 143

## Predloženi broj po tipu nakon kalibracije

- FISH_OR_SEAFOOD: 24
- GROUND_MEAT_OR_CASING: 437
- NEEDS_CLASSIFICATION: 179
- THERMAL_PROCESSED: 78
- WHOLE_CUT: 219

## Detektirani problemi

- changed_proposal: 162
- ground_probably_thermal: 34
- ground_probably_whole_cut: 64

## Prvih 80 prijedloga promjene

| Post ID | Status | Trenutni tip | Predloženi tip | Naslov | Razlog |
|---:|---|---|---|---|---|
| 1981 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | TRADICIJSKIH TALIJANSKIH RECEPATA ZA SUHOMESNATE PROIZVODE | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicijskih |
| 1986 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | LARDO DI COLONNATA | TITLE_WHOLE_CUT_SIGNAL:lardo |
| 1987 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: MORTADELLA BOLOGNA | TITLE_THERMAL_SIGNAL:mortadella |
| 1991 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | GUANCIALE ROMANO | TITLE_WHOLE_CUT_SIGNAL:guanciale |
| 1994 | draft | GROUND_MEAT_OR_CASING | WHOLE_CUT | Islandska Hangikjöt Kobasica | TITLE_WHOLE_CUT_SIGNAL:hangikjot |
| 1995 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Posavska Krvavica sa Šumskim Začinima | TITLE_THERMAL_SIGNAL:krvavica |
| 1997 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | Etnografska studija kulinarskog naslijeđa | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:etnografska studija |
| 2001 | draft | WHOLE_CUT | THERMAL_PROCESSED | Kholodets (Холодець) &#8211; Hladetina od mesa | TITLE_THERMAL_SIGNAL:hladetina |
| 2004 | draft | WHOLE_CUT | THERMAL_PROCESSED | Pashtetna (Паштетна) &#8211; Pečena mesna pašteta | TITLE_THERMAL_SIGNAL:pasteta,pecena |
| 2005 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | UJEDINJENO KRALJEVSTVO &#8211; Tradicionalni recepti | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicionalni recepti,ujedinjeno kraljevstvo |
| 2011 | draft | WHOLE_CUT | NEEDS_CLASSIFICATION | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:kompleksna zbirka |
| 2012 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | FRANCUSKA &#8211; Tradicionalni recepti za saucisson | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicionalni recepti,recepti za,francuska |
| 2014 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:kompleksna zbirka |
| 2015 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | ČEŠKA &#8211; Tradicionalni recepti | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicionalni recepti,ceska |
| 2017 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Drisheen (Tradicionalna irska krvavica od ovčje krvi) | TITLE_THERMAL_SIGNAL:krvavica,drisheen |
| 2022 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | Recepti za salame i suhomesnate proizvode iz europskih zemalja | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:recepti za |
| 2025 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | Recepti za salame i suhomesnate proizvode iz evropskih zemalja | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:recepti za |
| 2032 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Coppa (Kapula) | TITLE_WHOLE_CUT_SIGNAL:coppa |
| 2035 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Tradicionalna irska krvavica (Black Pudding) | TITLE_THERMAL_SIGNAL:krvavica,black pudding |
| 2036 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Irska bijela krvavica (White Pudding) | TITLE_THERMAL_SIGNAL:krvavica,white pudding |
| 2037 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Hangikjöt (Dimljeni janjeti but) | TITLE_WHOLE_CUT_SIGNAL:but,dimljeni janjeti but,hangikjot |
| 2044 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: NÜRNBERGER BRATWURST (NIRNBERŠKA KOBASICA ZA PEČENJE) | TITLE_THERMAL_SIGNAL:bratwurst,kobasica za pecenje |
| 2045 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Salata sa Schinkenspeck kobasicama | TITLE_WHOLE_CUT_SIGNAL:speck,schinken |
| 2047 | publish | NEEDS_CLASSIFICATION | THERMAL_PROCESSED | Domaći Bratwurst | TITLE_THERMAL_SIGNAL:bratwurst |
| 2048 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Domaći Leberwurst (Pašteta od jetre) | TITLE_THERMAL_SIGNAL:pasteta |
| 2055 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Coppa Piacentina &#8211; Piacentinska Coppa | TITLE_WHOLE_CUT_SIGNAL:coppa |
| 2058 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Guanciale Romano &#8211; Rimski Guanciale | TITLE_WHOLE_CUT_SIGNAL:guanciale |
| 2064 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | COPPA (CAPOCOLLO) &#8211; TALIJANSKA VRATINA | TITLE_WHOLE_CUT_SIGNAL:coppa,capocollo,vratina,vrat |
| 2070 | draft | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | BOUDIN BLANC DE BRUXELLES (BIJELA KRVAVICA IZ BRUXELLESA) | TITLE_THERMAL_SIGNAL:krvavica |
| 2071 | draft | GROUND_MEAT_OR_CASING | WHOLE_CUT | Butifarra Blanca &#8211; Tradicionalna Bijela Katalonska Kobasica | TITLE_WHOLE_CUT_SIGNAL:but |
| 2073 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Kistanje Začinjena Panceta (Specifični sušeni vrat) | TITLE_WHOLE_CUT_SIGNAL:panceta,vrat |
| 2074 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Petrovo Polje Začinska Slanina (Specifični začinjeni komad) | TITLE_WHOLE_CUT_SIGNAL:slanina |
| 2079 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | COPPA CORSE | TITLE_WHOLE_CUT_SIGNAL:coppa |
| 2081 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: PÂTÉ DE CAMPAGNE BRETON | TITLE_THERMAL_SIGNAL:pate |
| 2083 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Gračac Začinjena Panceta (Specifični sušeni vrat) | TITLE_WHOLE_CUT_SIGNAL:panceta,vrat |
| 2084 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Plitvice Začinska Slanina (Specifični začinjeni komad) | TITLE_WHOLE_CUT_SIGNAL:slanina |
| 2087 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Podravska Krvavica | TITLE_THERMAL_SIGNAL:krvavica |
| 2090 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: KASZANKA (POLJSKA KRVAVICA) | TITLE_THERMAL_SIGNAL:krvavica,kaszanka |
| 2091 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Morcela de Assar (Krvavica za Pečenje) | TITLE_THERMAL_SIGNAL:krvavica,morcela |
| 2114 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: ST. GALLER OLMA-BRATWURST (TELEĆA KOBASICA ZA PEČENJE) | TITLE_THERMAL_SIGNAL:bratwurst,kobasica za pecenje |
| 2116 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | COPPA PIACENTINA | TITLE_WHOLE_CUT_SIGNAL:coppa |
| 2230 | draft | WHOLE_CUT | THERMAL_PROCESSED | Suhi kare / pečenica | TITLE_THERMAL_SIGNAL:pecenica |
| 2353 | publish | NEEDS_CLASSIFICATION | WHOLE_CUT | Pastërma (Albansko suho meso) | TITLE_WHOLE_CUT_SIGNAL:suho meso |
| 2415 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | ?? NJEMAČKA &#8211; Tradicionalni recepti za Wurst | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicionalni recepti,recepti za |
| 2416 | draft | GROUND_MEAT_OR_CASING | NEEDS_CLASSIFICATION | ?? ITALIJA &#8211; Tradicionalni recepti za salame | TITLE_LOOKS_LIKE_COLLECTION_OR_TEST:tradicionalni recepti,recepti za |
| 2435 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Crnogorska stelja | TITLE_WHOLE_CUT_SIGNAL:stelja |
| 2437 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Drniški Začinjeni Vrat s Planine (Specifični mountainski naresak) | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2449 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Ribbenssteg (Danska dimljena svinjska rebra) | TITLE_WHOLE_CUT_SIGNAL:rebra |
| 2451 | draft | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Blodpølse (Danska krvavica) | TITLE_THERMAL_SIGNAL:krvavica |
| 2459 | private | WHOLE_CUT | THERMAL_PROCESSED | Privatno: Boiled Ham (Tradicionalna irska kuhana šunka) | TITLE_THERMAL_SIGNAL:kuhana |
| 2460 | pending | NEEDS_CLASSIFICATION | WHOLE_CUT | Westfälischer Schinken s krumpirom u ljusci i hrenom | TITLE_WHOLE_CUT_SIGNAL:schinken |
| 2463 | pending | NEEDS_CLASSIFICATION | FISH_OR_SEAFOOD | Rogatički Planinsko-Morski Raritet &#8220;Vjetar i More&#8221; | TITLE_FISH_SIGNAL:morski |
| 2467 | pending | FISH_OR_SEAFOOD | WHOLE_CUT | Istarski Morski Pršut &#8220;Neptunov Dar&#8221; | TITLE_WHOLE_CUT_SIGNAL:prsut |
| 2469 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Haslet (tradicionalna engleska pašteta od svinjskih iznutrica) | TITLE_THERMAL_SIGNAL:pasteta |
| 2479 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Verikäkk (Krvavica s ječmom) | TITLE_THERMAL_SIGNAL:krvavica |
| 2502 | draft | THERMAL_PROCESSED | WHOLE_CUT | JOULUKINKKU (BOŽIĆNA ŠUNKA) | TITLE_WHOLE_CUT_SIGNAL:sunka |
| 2504 | publish | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | SAVUSIIKA (DIMLJENI SIIKA &#8211; BIJELA RIBA) | TITLE_FISH_SIGNAL:riba |
| 2509 | pending | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | KYLMÄSAVULOHI (HLADNO DIMLJENI LOSOS) | TITLE_FISH_SIGNAL:losos |
| 2510 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | ILMAKUIVATTU PORONPAISTI (ZRAČNO-SUŠENI SOBOV BUT) | TITLE_WHOLE_CUT_SIGNAL:but |
| 2511 | publish | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | GRAAVILOHI (GRAVLAX &#8211; FINSKI MARINIRANI LOSOS) | TITLE_FISH_SIGNAL:losos |
| 2531 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Slátur (Islandska krvavica) | TITLE_THERMAL_SIGNAL:krvavica |
| 2551 | private | GROUND_MEAT_OR_CASING | WHOLE_CUT | Privatno: Senj Začinski Vrat (Specifični nasoljavani vrat) | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2552 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Otočac Začinski Vrat s Planine (Mountainski naresak) | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2557 | publish | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | Rūkyti lašišos (dimljen losos na litavski način) | TITLE_FISH_SIGNAL:losos |
| 2578 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: Leverworst (Jetrena pašteta) | TITLE_THERMAL_SIGNAL:pasteta |
| 2582 | pending | NEEDS_CLASSIFICATION | WHOLE_CUT | HOLSTEINER KATENSCHINKEN (HOLŠTAJNSKA DIMLJENA ŠUNKA) | TITLE_WHOLE_CUT_SIGNAL:schinken,sunka |
| 2583 | pending | NEEDS_CLASSIFICATION | WHOLE_CUT | WESTFÄLISCHER KNOCHENSCHINKEN (VESTFALSKA ŠUNKA S KOSTI) | TITLE_WHOLE_CUT_SIGNAL:schinken,sunka |
| 2585 | pending | NEEDS_CLASSIFICATION | WHOLE_CUT | Leichter Karreeschinken s povrćem u eintopfu | TITLE_WHOLE_CUT_SIGNAL:schinken |
| 2593 | draft | NEEDS_CLASSIFICATION | WHOLE_CUT | ELGKJØTT SPEKEMAT (SUHO MESO LOSA) | TITLE_WHOLE_CUT_SIGNAL:suho meso |
| 2594 | publish | GROUND_MEAT_OR_CASING | FISH_OR_SEAFOOD | GRAVET LAKS (GRAVLAKS &#8211; MARINIRANI LOSOS) | TITLE_FISH_SIGNAL:losos |
| 2597 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | FENALÅR (SUŠENI BUT JANJETA) | TITLE_WHOLE_CUT_SIGNAL:but |
| 2599 | draft | NEEDS_CLASSIFICATION | WHOLE_CUT | PINNEKJØTT (SUŠENA REBRA) | TITLE_WHOLE_CUT_SIGNAL:rebra |
| 2602 | draft | NEEDS_CLASSIFICATION | WHOLE_CUT | Đakovački Specijalni Fermentirani Vrat | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2604 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Osiječki Ljuti Začinski Vrat s Divljim Travkama | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2606 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Požeški Aromatični Sušeni Vrat s Divljim Travkama | TITLE_WHOLE_CUT_SIGNAL:vrat |
| 2608 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | Kuru Et (Suho meso sa smrekom) | TITLE_WHOLE_CUT_SIGNAL:suho meso |
| 2616 | publish | GROUND_MEAT_OR_CASING | WHOLE_CUT | BALERON (POLJSKA DIMLJENA VRATINA) | TITLE_WHOLE_CUT_SIGNAL:vratina,vrat |
| 2619 | private | GROUND_MEAT_OR_CASING | THERMAL_PROCESSED | Privatno: POLĘDWICA SOPOCKA (SUŠENA SVINJSKA PEČENICA) | TITLE_THERMAL_SIGNAL:pecenica |
| 2639 | draft | NEEDS_CLASSIFICATION | THERMAL_PROCESSED | Zalatina (Ζαλατίνα) &#8211; Ciparski hladetinasti svinjski narezak | TITLE_THERMAL_SIGNAL:hladetina |
| 2641 | pending | GROUND_MEAT_OR_CASING | WHOLE_CUT | Posyrti (Ποσυρτή) &#8211; Ciparska suha kobasica s divljim biljem | TITLE_WHOLE_CUT_SIGNAL:posyrti |

## Zaključak

Ako se potvrdi da su prijedlozi kalibracije logični, sljedeći korak je izrada audit alata v1.1 s jačim pravilima prioriteta za WHOLE_CUT, THERMAL_PROCESSED i FISH_OR_SEAFOOD.
Tek nakon toga smije se odabrati prvi mali batch za ručnu/kanonsku obradu.
