# 3042 Jésus de Lyon — recipe.yml internal QA v1

Status: **BLOCKED_FOR_PUBLIC_UPDATE**

Ovaj QA ne mijenja WordPress. Provjerava samo radni `recipe.yml` u dosjeu.

## Sažetak

- Ukupno provjera: 25
- PASS: 25
- FAIL: 0
- Zbroj sirovina: 10.0 kg
- Privatni preview/adapter tehnički dopušten: `true`
- Javni update dopušten: `false`

## Aktivne poznate blokade

- kanonski izvor za točne količine nije potvrđen
- količina starter kulture zahtijeva tehničku provjeru
- dimljenje je označeno kao needs_confirmation
- javni tekst još sadrži interne tragove prema intake izvještaju
- potrebno je završiti qa_report.md prije bilo kakvog WordPress updatea

## QA tablica

| Provjera | Status | Težina | Napomena |
|---|---|---|---|
| Javni update zabranjen | PASS | BLOCKER | Mora ostati false do završnog QA-a. |
| Status radnog nacrta | PASS | BLOCKER | Recept mora ostati radni nacrt. |
| Nije označen kao public verified | PASS | BLOCKER | Ne smije biti public_verified dok izvor i QA nisu završeni. |
| Tip recepta GROUND_MEAT_OR_CASING | PASS | BLOCKER | Za ovaj pilot koristi se samo model mljevenog/usitnjenog mesa u omotaču. |
| Status izvora upisan | PASS | BLOCKER | Proizvod je potvrđen, recept još nije kanonski potvrđen. |
| Ne tvrdi zaštićeni status | PASS | BLOCKER | Ne smije se tvrditi aktualni IGP/ZOI status. |
| Šarža 10 kg | PASS | MAJOR | Svi recepti u sustavu moraju biti standardizirani na 10 kg. |
| Sirovine u kg postoje | PASS | MAJOR | Meso i masnoća moraju biti u kg. |
| Zbroj sirovina približno 10 kg | PASS | MAJOR | Zbroj pronađenih amount_kg vrijednosti je 10.0 kg. |
| Začini i dodaci u g postoje | PASS | MAJOR | Začini moraju biti u g. |
| Sol 200 g / 20 g/kg | PASS | MAJOR | Sol je u razumnom osnovnom rasponu za suhi proizvod. |
| Starter kultura označena za provjeru | PASS | BLOCKER | Starter ne smije u javni recept bez provjere deklaracije proizvođača. |
| Tekućine upisane | PASS | MINOR | Tekućine su vidljive, ali voda za starter ostaje vezana uz provjeru startera. |
| Češnjak jasno definiran | PASS | MAJOR | U ovom nacrtu nema procijeđene tekućine od češnjaka. |
| Granulacija i hladna obrada | PASS | MAJOR | Za mljeveni proizvod mora postojati rešetka u mm i kontrola temperature. |
| Obrada masnoće opisana | PASS | MAJOR | Masnoća mora ostati hladna i čvrsta. |
| Crijeva i namakanje | PASS | MAJOR | Crijeva imaju tip, promjer, tekućinu, vrijeme i temperaturu namakanja. |
| Procesni blok postoji | PASS | MAJOR | Proces mora biti strukturiran po fazama. |
| Dimljenje označeno kao needs_confirmation | PASS | BLOCKER | Dimljenje ne smije biti javno prikazano kao obvezno bez dodatnog izvora. |
| Sušenje/zrenje ima parametre | PASS | MAJOR | Sušenje/zrenje ima radne parametre. |
| Nitritna sol nije navedena | PASS | MAJOR | Ako se kasnije doda nitrit, obvezna je sigurnosna napomena. |
| Gotovo je kad blok postoji | PASS | MAJOR | Recept mora imati kriterije gotovosti. |
| Problemi imaju rješenja | PASS | MAJOR | Svaki problem mora imati konkretno rješenje. |
| Posluživanje i čuvanje postoje | PASS | MINOR | Treba biti vidljivo za javni prikaz. |
| Blokade prije javnog updatea postoje | PASS | BLOCKER | Javni update mora ostati blokiran. |

## Zaključak

`recipe.yml` je tehnički uredan kao radni nacrt i smije ići u privatni preview/adapter, ali samo s vidljivim internim statusom `NOT_PUBLIC` u dosjeu, ne u javnom prikazu.

Javni WordPress update nije dopušten jer recept još ima aktivne blokade: izvor količina, starter kultura, dimljenje i javni interni tragovi.
