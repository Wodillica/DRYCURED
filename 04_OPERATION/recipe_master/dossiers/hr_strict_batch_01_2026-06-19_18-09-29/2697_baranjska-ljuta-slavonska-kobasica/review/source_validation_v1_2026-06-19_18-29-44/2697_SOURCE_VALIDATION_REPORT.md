# 2697 Baranjska Ljuta Slavonska Kobasica source validation v1

Status: **CONFIRMED_RECIPE_PUBLIC_SOURCES_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

Ovaj korak ne mijenja WordPress. Potvrđuje da recept ima javne izvore dovoljne za radni `recipe.yml`, ali ne dopušta javni update.

## Zaključak

Baranjska kobasica je potvrđena kao stvarni regionalni hrvatski proizvod iz skupine mljevenog mesa u ovitku. Javni izvori potvrđuju tehnološki okvir, začinski profil i jedan konkretan receptni zapis. Budući da nema službeni IGP/PGI disciplinar za ovaj konkretni recept, javni update ostaje blokiran dok se ne izradi `recipe.yml`, provede internal QA i napravi privatni preview.

## Statusi

- Canonical project status: `CONFIRMED_RECIPE`
- Recipe type router: `GROUND_MEAT_OR_CASING`
- Recommended title: `Baranjska kobasica – ljuta varijanta`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Recipe.yml next allowed: `true`
- Title review required: `true`
- Exact WP quantities confirmed: `false`

## Izvorno zaključane činjenice

| Element | Izvorno potvrđeno |
|---|---|
| Tip proizvoda | mljevena suha/dimljena kobasica u tankom svinjskom crijevu |
| Regija | Baranja / Slavonija |
| Glavni začinski potpis | slatka i ljuta paprika, češnjak, papar/biber, sol |
| Mljevenje | rešetka 6 mm u javnom receptnom zapisu |
| Crijeva | tanka svinjska crijeva, 35–40 cm |
| Dimljenje | 3–4 dima po 6 sati tijekom tjedan dana u receptnom zapisu; stručni opis navodi 5–6 dimova svaki drugi dan |
| Zrenje | 25–30 dana ili oko 30 dana prema javnim izvorima |

## Izvorna formula prije Drycured skaliranja

| Sastojak | Izvorna količina |
|---|---:|
| svinjsko meso | 10 kg |
| tvrda slanina | 1 kg |
| sol | 200 g |
| šećer | 30 g |
| biber/papar | 50 g |
| slatka paprika | 150 g |
| ljuta paprika | 100 g |
| pastozni češnjak | 50 g |

## Drycured odluka prije `recipe.yml`

Drycured standard traži 10 kg ukupne mesne smjese. Budući da javni recept ima 10 kg mesa + 1 kg slanine, omjer treba skalirati faktorom `10/11 = 0,90909` ili urednički oblikovati kao 10 kg ukupne smjese uz očuvanje omjera mesa i tvrde slanine.

## Otvorene blokade prije javnog updatea

- Naziv javnog posta `Baranjska Ljuta Slavonska Kobasica` treba urednički uskladiti; kanonski naziv za radni dosje je `Baranjska kobasica – ljuta varijanta`.
- Izvori potvrđuju proizvod i radnu recepturu, ali ne daju službeni zaštićeni disciplinar kao kod IGP proizvoda.
- Prije javnog updatea treba izraditi `recipe.yml` na 10 kg ukupne smjese prema Drycured standardu.
- Treba jasno riješiti skaliranje jer javni recept daje 10 kg mesa + 1 kg tvrde slanine, dok Drycured standard traži 10 kg ukupne mesne smjese.
- Treba definirati češnjak: pastozni češnjak, macerat ili procijeđena tekućina.
- Treba definirati crijeva: tanka svinjska crijeva 35-40 cm, namakanje, voda, temperatura, vrijeme i neprokuhavanje.
- Treba definirati faze dimljenja i zrenja s radnim parametrima.

## Izvori

- `SRC-2697-001` — Agroklub / stručni opis regionalnih kobasica, citiran Mastanjević — high_for_technology_medium_for_exact_formula
- `SRC-2697-002` — HRT / izjava predsjednika udruge proizvođača baranjskog kulena — medium_high_for_identity
- `SRC-2697-003` — Recepisi / javno objavljen recept za Baranjsku kobasicu — medium_for_exact_formula_low_for_official_status

## Sljedeći korak

Izraditi `recipe.yml` na 10 kg ukupne smjese, uz jasno definiranje rešetke 6 mm, tvrdog masnog tkiva, tankih svinjskih crijeva, češnjaka i ciklusa dimljenja/zrenja. Javni update ostaje zabranjen.

## Repair napomena

Ova verzija ispravlja dokumentacijski problem iz prethodnog generiranja izvještaja, gdje je shell pogrešno interpretirao Markdown backtickove. WordPress nije mijenjan.
