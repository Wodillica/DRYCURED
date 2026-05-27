# Drycured.com — Process Hub Architecture v0.1

## Status dokumenta

Ovo je arhitektonski nacrt.

Ovaj dokument ne mijenja produkciju, ne dodaje novi plugin i ne dira postojeće procesne stranice.

## Cilj

Centralni `drycured-process-hub` sloj u budućnosti treba objediniti podatke o procesnim fazama:

- redoslijed procesa
- URL svake faze
- naziv faze
- kratki opis
- hero sliku
- povezani alat, ako postoji
- prethodnu i sljedeću fazu
- status faze
- vezane članke Enciklopedije znanja
- vezane recepte

Cilj prve faze nije zamijeniti postojeće MU-pluginove, nego napraviti siguran centralni registar podataka.

## Važno pravilo

Postojeći procesni MU-pluginovi ostaju aktivni i netaknuti.

Process Hub u prvoj fazi smije biti samo čitač i registar podataka, bez preuzimanja renderiranja stranica.

Ne smije mijenjati sadržaj procesnih stranica, meni, alate, shortcodeove, CTA linkove ni postojeći HTML izlaz.

## Trenutni procesni niz

| Red | Proces | URL | Povezani alat | Status |
|---:|---|---|---|---|
| 01 | Sirovina | /proces-izrade/sirovina/ | — | aktivno |
| 02 | Rezanje | /proces-izrade/rezanje/ | — | aktivno |
| 03 | Soljenje | /proces-izrade/soljenje/ | Kalkulator soli | aktivno |
| 04 | Mljevenje | /proces-izrade/mljevenje/ | — | aktivno |
| 05 | Miješanje | /proces-izrade/mijesanje/ | — | aktivno |
| 06 | Odležavanje smjese | /proces-izrade/odlezavanje-smjese/ | — | aktivno |
| 07 | Punjenje | /proces-izrade/punjenje/ | — | aktivno |
| 08 | Fermentacija | /proces-izrade/fermentacija/ | Praćenje pH | aktivno |
| 09 | Dimljenje | /proces-izrade/dimljenje/ | Planer dimljenja | aktivno |
| 10 | Sušenje | /proces-izrade/susenje/ | Kalkulator sušenja | aktivno |
| 11 | Zrenje | /proces-izrade/zrenje/ | — | aktivno |
| 12 | Pakiranje | /proces-izrade/pakiranje/ | — | aktivno |

## Aktivni alati

| Alat | URL | Povezan s procesom | Napomena |
|---|---|---|---|
| Kalkulator soli | /kalkulator-soli/ | Soljenje | postojeći alat |
| Praćenje pH | /pracenje-ph/ | Fermentacija | postojeći alat |
| Planer dimljenja | /planer-dimljenja/ | Dimljenje | postojeći alat |
| Kalkulator sušenja | /kalkulator-susenja/ | Sušenje | postojeći alat |

## Predložena fazna implementacija

### Faza 1 — Registry only

Dodati novi MU-plugin:

`wp-content/mu-plugins/drycured-process-hub.php`

U ovoj fazi plugin samo registrira podatke o procesima.

Dopuštene funkcije:

- dohvat svih procesa
- dohvat jednog procesa po slugu
- dohvat prethodne i sljedeće faze
- dohvat povezanog alata za pojedinu fazu

Zabranjeno u ovoj fazi:

- mijenjanje `the_content`
- dodavanje CSS-a
- dodavanje JavaScripta
- izmjena menija
- izmjena postojećih procesnih stranica
- izmjena postojećih alata

### Faza 2 — Debug prikaz samo za administratora

Dodati interni debug prikaz procesa, ali samo ako je korisnik administrator i ako se ručno pozove debug parametar.

Debug prikaz služi za provjeru redoslijeda procesa, URL-ova, povezanih alata i hero slika.

Ovaj prikaz ne smije biti javno vidljiv.

### Faza 3 — Home process rail povezivanje

Tek nakon provjere registra, postojeći home procesni vodič može u budućnosti čitati redoslijed iz Process Huba.

Postojeći home process order pluginovi ne smiju se ukloniti dok se ne potvrdi potpuno isti prikaz.

### Faza 4 — Procesne stranice

Kasnije, pojedine procesne stranice mogu koristiti centralni registar za prethodnu fazu, sljedeću fazu, povezani alat, povezane članke i povezane recepte.

Postojeći sadržaj stranica ostaje u postojećim procesnim pluginovima dok se ne napravi poseban migracijski plan.

### Faza 5 — Enciklopedija znanja i recepti

Process Hub kasnije može postati poveznica između procesnih stranica, alata, recepata, članaka Enciklopedije znanja i edukativnih kartica na home stranici.

## Podaci koje registar mora sadržavati

Za svaki proces registar mora imati:

- slug procesa
- redni broj
- naziv
- URL stranice
- putanju do hero slike
- kratki opis
- prethodnu fazu
- sljedeću fazu
- povezani alat, ako postoji
- status procesa

## Sigurnosna pravila

1. Ne presretati globalno linkove.
2. Ne mijenjati postojeće stranice u prvoj fazi.
3. Ne mijenjati meni.
4. Ne stvarati nove alatne stranice.
5. Ne uklanjati postojeće procesne MU-pluginove.
6. Ne spajati više promjena u jedan nepregledan korak.
7. Svaka promjena mora imati backup, lint, HTTP check, marker check, rollback i Git commit.

## Rollback filozofija

Faza 1 mora imati minimalan rollback.

Ako se doda samo read-only plugin, rollback je:

`rm -f wp-content/mu-plugins/drycured-process-hub.php`

zatim:

`wp cache flush --allow-root`

Budući da Faza 1 ne dira postojeće stranice, rollback ne smije utjecati na postojeći procesni sustav.

## Preporučeni prvi kodni korak

Nakon prihvaćanja ovog plana izraditi samo read-only `drycured-process-hub.php` registry plugin.

Plugin mora proći PHP lint, ne smije utjecati na frontend, ne smije mijenjati postojeće HTML izlaze, mora imati samo administratorski/debug dohvat, koristiti postojeće URL-ove alata i postojeće hero slike, te biti lako uklonjiv jednim rollbackom.

## Status

Plan je spreman za pregled prije izrade prvog read-only registry plugina.
