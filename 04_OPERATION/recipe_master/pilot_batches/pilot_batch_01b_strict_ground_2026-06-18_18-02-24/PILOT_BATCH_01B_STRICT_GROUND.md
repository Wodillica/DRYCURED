# DRYCURED PILOT_BATCH_01B_STRICT_GROUND

Ovaj dokument ne mijenja WordPress. Služi za strogo čišćenje prvog pilot batcha.

## Pravilo

U prvi pilot smiju ući samo proizvodi koji su stvarno mljeveno/usitnjeno meso u omotaču. Komadi mesa, fileti, pastirme, pastrame, basturme, pancete, vratovi, slanine i riba se isključuju i šalju u vlastite tehnološke modele.

## Sažetak

- Izvorni redovi: 10
- Prihvaćeno kao STRICT_GROUND: 4
- Isključeno za type review: 6

## Prihvaćeni kandidati za prvi pojedinačni dosje

| Post ID | Naslov | Tip | Gate | URL | Odluka | Razlog |
|---:|---|---|---|---|---|---|
| 1982 | FINOCCHIONA TOSCANA | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/finocchiona-toscana/ | STRICT_GROUND_ACCEPT | GROUND_SIGNAL:finocchiona |
| 1984 | NDUJA CALABRESE | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/nduja-calabrese/ | STRICT_GROUND_ACCEPT | GROUND_SIGNAL:nduja |
| 1990 | SALAME DI FELINO | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/salame-di-felino/ | STRICT_GROUND_ACCEPT | GROUND_SIGNAL:salame,salame di felino |
| 3042 | Jésus de Lyon – debela suha kobasica | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/jesus-de-lyon-debela-suha-kobasica/ | STRICT_GROUND_ACCEPT | GROUND_SIGNAL:kobasica,jesus de lyon |

## Isključeni kandidati — prebaciti u WHOLE_CUT/THERMAL review

| Post ID | Naslov | Tip | Gate | URL | Odluka | Razlog |
|---:|---|---|---|---|---|---|
| 3094 | Elena filet (Еленски филе) | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/elena-filet/ | EXCLUDE | WHOLE_CUT_SIGNAL:filet,file |
| 3105 | Apohti (Απόχτι) &#8211; Dimljeni svinjski file | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/apohti-dimljeni-svinjski-file/ | EXCLUDE | WHOLE_CUT_SIGNAL:file,apohti,svinjski file |
| 3106 | Pastourma (Παστουρμάς) &#8211; Ciparski začinjeni sušeni goveđi file | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/pastourma-ciparski-zacinjeni-suseni-govei-file/ | EXCLUDE | WHOLE_CUT_SIGNAL:file,pastourma |
| 3135 | Pastourmas (Παστουρμάς) | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/pastourmas/ | EXCLUDE | WHOLE_CUT_SIGNAL:pastourma,pastourmas |
| 3205 | PASTRAMĂ DE OAIE (Sušena ovčetina) | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/pastrama-de-oaie-susena-ovcetina/ | EXCLUDE | WHOLE_CUT_SIGNAL:ovcetina |
| 3206 | Basturma (Бастурма &#8211; Začinjeno sušeno goveđe meso) | GROUND_MEAT_OR_CASING | PASS | https://drycured.com/recepti-baza/basturma-zacinjeno-suseno-govee-meso/ | EXCLUDE | WHOLE_CUT_SIGNAL:basturma |

## Sljedeći korak

Za prihvaćene kandidate otvoriti pojedinačne dosjee: sources.yml, recipe.yml, qa_report.md i wordpress_import_log.md. Javni update se i dalje ne radi.
