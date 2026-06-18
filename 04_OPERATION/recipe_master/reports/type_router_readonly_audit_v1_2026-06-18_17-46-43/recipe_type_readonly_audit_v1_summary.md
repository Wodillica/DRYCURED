# DRYCURED Recipe Type Router — read-only audit v1

Datum: 2026-06-18T17:47:47+00:00

## Važna napomena

Ovaj audit je read-only. Nije mijenjao WordPress postove, meta podatke, statuse, slugove, URL-ove ni renderer.

## Sažetak

- Ukupno dry_recipe zapisa: 937
- Objavljenih zapisa: 412
- Ukupno blokiranih za javno ažuriranje: 834
- Objavljenih blokiranih za javno ažuriranje: 382
- Interni/fallback tekst hitovi: 408
- Nitrit bez sigurnosne napomene: 180
- Public fetch enabled: yes
- Public fetch errors: 0

## Broj po tehnološkom tipu

- FISH_OR_SEAFOOD: 5
- GROUND_MEAT_OR_CASING: 565
- NEEDS_CLASSIFICATION: 185
- THERMAL_PROCESSED: 39
- WHOLE_CUT: 143

## Broj po statusu objave

- draft: 288
- pending: 92
- private: 145
- publish: 412

## Broj po confidence razini

- high: 556
- low: 93
- medium: 139
- none: 149

## Prvih 40 blokiranih primjera

| Post ID | Status | Tip | Confidence | Naslov | Razlog blokade |
|---:|---|---|---|---|---|
| 1976 | draft | GROUND_MEAT_OR_CASING | high | Primorska kobasica | GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1977 | draft | WHOLE_CUT | medium | Creski udić — pršut od ovce | PHASE_MISSING_TIME_OR_PARAMS |
| 1978 | draft | GROUND_MEAT_OR_CASING | high | Creska janjeća roštilj kobasica | GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1979 | draft | NEEDS_CLASSIFICATION | low | Testni recept iz kalkulatora | NEEDS_CLASSIFICATION;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1980 | draft | NEEDS_CLASSIFICATION | low | Davorova kobasa | NEEDS_CLASSIFICATION |
| 1981 | draft | GROUND_MEAT_OR_CASING | medium | TRADICIJSKIH TALIJANSKIH RECEPATA ZA SUHOMESNATE PROIZVODE | NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1985 | draft | WHOLE_CUT | medium | SPECK ALTO ADIGE | NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1987 | private | GROUND_MEAT_OR_CASING | high | Privatno: MORTADELLA BOLOGNA | NITRITE_WITHOUT_SAFETY_NOTE |
| 1988 | publish | GROUND_MEAT_OR_CASING | high | SOPPRESSA VICENTINA | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 1991 | publish | GROUND_MEAT_OR_CASING | medium | GUANCIALE ROMANO | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 1992 | draft | GROUND_MEAT_OR_CASING | medium | Engleska Cumberland Kobasica | PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1993 | draft | GROUND_MEAT_OR_CASING | medium | Škotska Lorne Kobasica | GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1994 | draft | GROUND_MEAT_OR_CASING | medium | Islandska Hangikjöt Kobasica | GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1996 | draft | NEEDS_CLASSIFICATION | none | Tradicionalni suhomesnati proizvodi Slavonije | NEEDS_CLASSIFICATION;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1997 | draft | GROUND_MEAT_OR_CASING | low | Etnografska studija kulinarskog naslijeđa | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1998 | draft | GROUND_MEAT_OR_CASING | low | Tradicionalni suhomesnati proizvodi | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 1999 | draft | WHOLE_CUT | high | Salo (Сало) &#8211; Ukrajinska slanina | PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2000 | draft | GROUND_MEAT_OR_CASING | high | Domašnja Kovbasa (Домашня ковбаса) &#8211; Domaća kobasica | PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2001 | draft | WHOLE_CUT | low | Kholodets (Холодець) &#8211; Hladetina od mesa | WHOLE_CUT_MISSING_CURE_OR_BRINE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2002 | draft | WHOLE_CUT | low | Solonina (Солонина) &#8211; Salamurena govedina | PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2003 | draft | GROUND_MEAT_OR_CASING | medium | Shukhevychivska Kovbasa (Шухевичівська ковбаса) &#8211; Sušena kobasica s paprom | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2004 | draft | WHOLE_CUT | medium | Pashtetna (Паштетна) &#8211; Pečena mesna pašteta | WHOLE_CUT_MISSING_CURE_OR_BRINE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2005 | draft | GROUND_MEAT_OR_CASING | low | UJEDINJENO KRALJEVSTVO &#8211; Tradicionalni recepti | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2006 | draft | GROUND_MEAT_OR_CASING | low | Cumberland Sausage | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2007 | draft | NEEDS_CLASSIFICATION | low | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | NEEDS_CLASSIFICATION;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2008 | draft | NEEDS_CLASSIFICATION | low | GRČKA &#8211; Tradicionalni recepti za Loukaniko | NEEDS_CLASSIFICATION;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2009 | draft | NEEDS_CLASSIFICATION | low | Tradicionalni Loukaniko sa Krete | NEEDS_CLASSIFICATION;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2010 | draft | GROUND_MEAT_OR_CASING | low | Makedončki Loukaniko sa Planinom | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2011 | draft | WHOLE_CUT | low | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | WHOLE_CUT_MISSING_CURE_OR_BRINE;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2012 | draft | GROUND_MEAT_OR_CASING | low | FRANCUSKA &#8211; Tradicionalni recepti za saucisson | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2013 | draft | GROUND_MEAT_OR_CASING | low | Saucisson sec iz Lyona | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2014 | draft | GROUND_MEAT_OR_CASING | low | Kompleksna zbirka tradicionalnih suhomesnatih recepata iz Europe | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2015 | draft | GROUND_MEAT_OR_CASING | low | ČEŠKA &#8211; Tradicionalni recepti | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2016 | draft | GROUND_MEAT_OR_CASING | low | Lovecká Saláma (Lovačka salama) | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;NITRITE_WITHOUT_SAFETY_NOTE;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2018 | publish | GROUND_MEAT_OR_CASING | high | Irska salama (Spiced Irish Salami) | NITRITE_WITHOUT_SAFETY_NOTE;PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2021 | publish | GROUND_MEAT_OR_CASING | high | Cumberland kobasica | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2022 | draft | GROUND_MEAT_OR_CASING | medium | Recepti za salame i suhomesnate proizvode iz europskih zemalja | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2023 | publish | GROUND_MEAT_OR_CASING | high | ?? Grčki tradicionalni recept &#8211; Loukaniko | PUBLIC_OR_META_INTERNAL_TEXT_HIT |
| 2024 | draft | GROUND_MEAT_OR_CASING | medium | Slovački recept &#8211; Turistická Saláma | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PHASE_MISSING_TIME_OR_PARAMS;PROBLEM_WITHOUT_SOLUTION_SIGNAL |
| 2025 | draft | GROUND_MEAT_OR_CASING | medium | Recepti za salame i suhomesnate proizvode iz evropskih zemalja | GROUND_MISSING_GRANULATION;GROUND_MISSING_FAT_HANDLING;PROBLEM_WITHOUT_SOLUTION_SIGNAL |

## Prvih 40 kandidata bez blokade

| Post ID | Status | Tip | Confidence | Naslov |
|---:|---|---|---|---|
| 1975 | draft | GROUND_MEAT_OR_CASING | high | Istarska kobasica |
| 1982 | publish | GROUND_MEAT_OR_CASING | high | FINOCCHIONA TOSCANA |
| 1983 | publish | WHOLE_CUT | high | PROSCIUTTO DI SAN DANIELE |
| 1984 | publish | GROUND_MEAT_OR_CASING | high | NDUJA CALABRESE |
| 1986 | publish | GROUND_MEAT_OR_CASING | high | LARDO DI COLONNATA |
| 1989 | private | GROUND_MEAT_OR_CASING | high | Privatno: PORCHETTA ROMANA |
| 1990 | publish | GROUND_MEAT_OR_CASING | high | SALAME DI FELINO |
| 1995 | private | GROUND_MEAT_OR_CASING | high | Privatno: Posavska Krvavica sa Šumskim Začinima |
| 2017 | private | GROUND_MEAT_OR_CASING | high | Privatno: Drisheen (Tradicionalna irska krvavica od ovčje krvi) |
| 2019 | private | GROUND_MEAT_OR_CASING | high | Privatno: Landjäger kobasice s bavarskom salatom od krumpira |
| 2020 | private | GROUND_MEAT_OR_CASING | high | Privatno: Mettwurst s toplom salatom od krumpira i zelenog graha |
| 2035 | private | GROUND_MEAT_OR_CASING | high | Privatno: Tradicionalna irska krvavica (Black Pudding) |
| 2036 | private | GROUND_MEAT_OR_CASING | high | Privatno: Irska bijela krvavica (White Pudding) |
| 2037 | publish | GROUND_MEAT_OR_CASING | medium | Hangikjöt (Dimljeni janjeti but) |
| 2044 | private | GROUND_MEAT_OR_CASING | high | Privatno: NÜRNBERGER BRATWURST (NIRNBERŠKA KOBASICA ZA PEČENJE) |
| 2045 | private | GROUND_MEAT_OR_CASING | high | Privatno: Salata sa Schinkenspeck kobasicama |
| 2046 | private | GROUND_MEAT_OR_CASING | high | Privatno: Rinderbeiried sendvič |
| 2064 | publish | GROUND_MEAT_OR_CASING | medium | COPPA (CAPOCOLLO) &#8211; TALIJANSKA VRATINA |
| 2087 | private | GROUND_MEAT_OR_CASING | high | Privatno: Podravska Krvavica |
| 2090 | private | GROUND_MEAT_OR_CASING | high | Privatno: KASZANKA (POLJSKA KRVAVICA) |
| 2091 | private | GROUND_MEAT_OR_CASING | high | Privatno: Morcela de Assar (Krvavica za Pečenje) |
| 2187 | draft | GROUND_MEAT_OR_CASING | high | Slavonska kobasica — ZOZP |
| 2188 | draft | GROUND_MEAT_OR_CASING | high | Slavonska cajna kobasica — tradicijska |
| 2224 | draft | WHOLE_CUT | medium | Domaći pršut |
| 2225 | draft | WHOLE_CUT | medium | Suha šunka |
| 2228 | draft | WHOLE_CUT | low | Domaća slanina / panceta |
| 2229 | draft | WHOLE_CUT | medium | Suha vratina |
| 2358 | draft | WHOLE_CUT | high | Steirischer Rohschinken (Štajerska sirova šunka) |
| 2377 | draft | THERMAL_PROCESSED | medium | PÂTÉ GAUMAIS (GAUME PAŠTETA) |
| 2402 | draft | WHOLE_CUT | low | Lountza (Λούντζα) |
| 2405 | pending | WHOLE_CUT | medium | Posyrti (Ποσυρτή) |
| 2406 | pending | WHOLE_CUT | low | Apohtin (Αποχτίν) |
| 2433 | private | THERMAL_PROCESSED | high | Privatno: Crnogorska pečenica |
| 2438 | private | GROUND_MEAT_OR_CASING | high | Privatno: Komiški Brudet Narezak |
| 2447 | private | THERMAL_PROCESSED | high | Privatno: Leverpostej (Danska pašteta od svinjske jetre) |
| 2453 | draft | THERMAL_PROCESSED | medium | Flæskesteg (Danska pečena svinjetina s hrskavom kožom) |
| 2455 | private | THERMAL_PROCESSED | high | Privatno: Sylte (Danska hladetina od svinjske glave) |
| 2457 | draft | WHOLE_CUT | medium | Irski spiced beef (Začinjeno sušeno goveđe meso) |
| 2472 | private | THERMAL_PROCESSED | high | Privatno: Oxford Brawn (hladetina od svinjske glave) |
| 2473 | pending | THERMAL_PROCESSED | medium | Tradicionalni engleski Black Pudding (krvavica) |

## Izlazne datoteke

- CSV: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/recipe_type_readonly_audit_v1.csv`
- JSON: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/recipe_type_readonly_audit_v1.json`
- Summary: `/root/DRYCURED_GITHUB/04_OPERATION/recipe_master/reports/type_router_readonly_audit_v1_2026-06-18_17-46-43/recipe_type_readonly_audit_v1_summary.md`
