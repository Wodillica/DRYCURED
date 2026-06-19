# 1982 Finocchiona Toscana source validation v1

Status: **CONFIRMED_RECIPE_OFFICIAL_SPEC_AVAILABLE_PUBLIC_UPDATE_BLOCKED**

Ovaj korak ne mijenja WordPress. Potvrđuje izvorni status recepta i definira smjer za `recipe.yml`.

## Zaključak

Finocchiona Toscana je službeno potvrđen IGP/PGI proizvod s dostupnim službenim disciplinarom. Disciplinar daje dovoljno jak okvir za izradu Drycured radnog `recipe.yml` zapisa, ali postojeći WP recept se ne smije javno ažurirati dok se ne napravi i QA-provjeri strukturirani `recipe.yml`.

## Statusi

- Canonical project status: `CONFIRMED_RECIPE`
- Public update allowed: `false`
- Public publish allowed: `false`
- Source post write allowed: `false`
- Recipe.yml next allowed: `true`
- Exact WP quantities confirmed: `false`

## Službeno zaključane činjenice za 10 kg

| Element | Raspon / status |
|---|---|
| Sol | 250–350 g / 10 kg |
| Mljeveni papar | 5–10 g / 10 kg |
| Papar u zrnu/lomljeni | 15–40 g / 10 kg |
| Češnjak ili ekvivalent suhog češnjaka | 5–10 g / 10 kg |
| Sjeme/cvijet komorača | 20–50 g / 10 kg |
| Vino | do 0,1 L / 10 kg, opcionalno |
| Šećeri | do 100 g / 10 kg, opcionalno |
| Starter kulture | dopuštene, ali zahtijevaju tehnološku napomenu |
| Nitriti/nitrati | dopušteni u specifikaciji, ali u Drycured receptu traže sigurnosnu napomenu ako se koriste |
| Sušenje | 12–25 °C |
| Zrenje | 11–18 °C, 65–90 % RH |
| Minimalno trajanje | 15 / 21 / 45 dana prema težini pri punjenju |

## Otvorene blokade prije javnog updatea

- Postojeći WP recept nema još zaključan recipe.yml s provjerom svih obveznih polja.
- Nedostaju _dry_recipe_sections i _dry_verified_process za strukturirani radni prikaz.
- Potrebno je uskladiti recept na 10 kg prema službenim rasponima iz disciplinara.
- Ako se koriste nitriti/nitrati ili starter kulture, moraju biti jasno označeni kao opcionalni/dopušteni i tehnološki objašnjeni.
- Crijeva, granulacija i proces moraju biti popunjeni u Drycured standardu prije javnog updatea.

## Izvori

- `SRC-1982-001` — Ministero delle politiche agricole alimentari e forestali / Finocchiona IGP disciplinary specification — high
- `SRC-1982-002` — European Union / Commission Implementing Regulation (EU) 2015/629 — high
- `SRC-1982-003` — Consorzio di tutela della Finocchiona IGP — high
- `SRC-1982-004` — Regione Toscana product information — medium_high

## Sljedeći korak

Izraditi `recipe.yml` iz službenog disciplinara i postojećeg WP sadržaja. Javni update ostaje zabranjen.

## Repair napomena

Ova verzija ispravlja dokumentacijski problem iz prethodnog generiranja izvještaja, gdje je shell pogrešno interpretirao Markdown backtickove. WordPress nije mijenjan.
