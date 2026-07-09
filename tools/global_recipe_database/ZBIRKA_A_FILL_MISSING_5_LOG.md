# Zbirka A — Popunjavanje 5 recepata s pravim PDF tekstom

**Datum:** 2026-07-09
**Skript:** `zbirka_a_fill_missing_5.py`
**Izvor:** `224336073-Proizvodi-Od-Mesa-a5.pdf`

## Što je promijenjeno

Pet recepata koji su prethodno imali samo placeholder (`Napomena: Puni tekst
recepta nije dostupan`) ažurirani su stvarnim podacima iz izvornog PDF-a.

| Kôd | Post ID | PDF str. | is_smoked | Napomena |
|-----|---------|----------|-----------|----------|
| FR-001 | 3652 | 15 | true | Karanfilić: `? g` u izvoru — količina nije čitljiva |
| RO-001 | 3651 | 19–20 | false | "gronik" (50 dkg) — hr. inačica nepoznata |
| CZ-001 | 3653 | 48 | true | 6 tjedana ukupno (šećer→sol→salamura→dim) |
| RS-VO-001 | 3656 | 60 | true | Upućuje na RS-SM-001 za detaljan postupak |
| PL-002 | 3649 | 30–31 | true | Vruć dim 60°C + pečenje 75–90°C |

## Provjere

- `_dry_recipe_overrides` ažuriran WP-CLI meta update
- Placeholder "Detaljan postupak nije dostupan" uklonjen (grep potvrđen)
- Typo "Ohhlađenom" ispravljen u CZ-001 timeline (Dan 16)

## Zašto commit dolazi nakon primjene

Skript je pokrenut iz `/tmp/` direktno na serveru (brza primjena jer je
SSH pristup uspostavljen tek unutar iste radne sesije, a baza je ažurirana
odmah). Dokumentacija i commit dodan naknadno — ovo je procesna greška
koja se ispravlja ovim commitom.

