# Today renderer fix status

## Cilj

Danas se problem prikaza recepata rješava sustavno, ne ručnim krpanjem jednog recepta.

## Trenutno stanje

- Najnoviji normalizer v2: 04_OPERATION/recipe_master/reports/contract_normalizer_v2_2026-06-15_18-15-01
- Pilot contract: 2976 Slavonska domaća kobasica
- Staging mapa: 04_OPERATION/recipe_master/staged_contracts_v2

## Odluka

Renderer adapter smije se testirati samo na receptu 2976 dok HTML QA ne prođe.

Nakon toga adapter se može uključiti za sve javne recepte koji imaju status `RENDER_ADAPTER_CANDIDATE`.

## Zabranjeno

- ručno HTML krpanje pojedinačnih recepata
- prikaz `prema receptu`
- prikaz `hladna masa`
- javno miješanje 2976 i 2981
- masovni upis u WordPress prije pilot QA

## Sljedeći korak

Izraditi renderer adapter pilot za 2976 koji čita staged contract v2 i prikazuje standardizirane blokove:
- sastojci
- granulacija
- crijeva
- češnjak
- procesna kronologija
- greške i rješenja
