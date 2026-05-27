# Drycured.com — Process Hub / Home Process Rail Migration Plan v0.1.4

## Status dokumenta

Ovo je migracijski plan.

Ovaj dokument ne mijenja WordPress produkciju, ne dodaje novi kod i ne dira postojeći home process rail.

## Trenutno stanje

Sustav trenutno ima:

- aktivne procesne stranice za 12 faza
- Process Hub read-only registar
- administratorski pregled Process Huba
- postojeći home process rail
- potvrđeno poklapanje svih 12 URL-ova između Process Huba i home vodiča

## Cilj migracije

Cilj nije promijeniti izgled home vodiča.

Cilj je da postojeći home process rail u budućnosti čita redoslijed, nazive, URL-ove i povezane podatke iz Process Hub registra.

To omogućuje:

- jedno centralno mjesto istine
- manje ručnih korekcija
- lakše povezivanje procesa s alatima
- lakše povezivanje procesa s člancima Enciklopedije znanja
- lakše povezivanje procesa s receptima

## Strogo pravilo

Migracija ne smije promijeniti javni izgled home stranice.

Ako se nakon migracije promijeni raspored, link, tekst ili vizualni izgled bez namjere, migracija se vraća natrag.

## Što se ne smije dirati u prvoj migraciji

Ne smije se dirati:

- postojeće procesne stranice
- alatne stranice
- meni
- postojeći alati
- postojeći shortcodovi alata
- CSS stranica procesa
- Process Hub registar, osim ako audit pokaže netočan podatak
- Elementor sadržaj home stranice

## Predložena fazna migracija

### Faza 1 — Audit postojećeg home vodiča

Već odrađeno.

Rezultat:
- home vodič ima svih 12 procesnih URL-ova
- svi URL-ovi iz Process Hub registra pronađeni su na home stranici
- nisu rađene izmjene

### Faza 2 — Snapshot postojećeg rendera

Prije bilo kakvog koda treba spremiti:

- home HTML prije promjene
- popis procesnih linkova
- broj marker elemenata
- veličinu stranice
- HTTP status
- vizualni screenshot ako se radi ručno iz preglednika

### Faza 3 — Adapter, ne zamjena

Ne mijenjati odmah postojeći home process rail.

Dodati mali adapter koji može pročitati Process Hub registar, ali je prema zadanim postavkama isključen.

Primjer opcije:

`drycured_home_process_rail_use_hub=0`

Dok je opcija 0, prikaz ostaje stari.

### Faza 4 — Testni način

Uvesti testni način samo za administratora ili query parametar.

Primjer:

`?drycured_test_hub_rail=1`

U tom načinu se može usporediti:

- stari redoslijed
- novi redoslijed iz Process Huba
- linkovi
- nazivi faza

Testni način ne smije biti javno aktivan.

### Faza 5 — Paralelna usporedba

Napraviti skriptu koja uspoređuje stari i novi izvor podataka:

- broj faza mora biti 12
- slugovi moraju biti isti
- URL-ovi moraju biti isti
- redoslijed mora biti isti
- nazivi moraju biti isti ili urednički odobreni
- povezani alati ostaju samo dodatni podatak, ne mijenjaju osnovni prikaz

### Faza 6 — Kontrolirano uključivanje

Tek kad je usporedba potpuno čista, opcija se može promijeniti:

`drycured_home_process_rail_use_hub=1`

Nakon uključivanja odmah provjeriti:

- home HTTP 200
- svih 12 linkova prisutno
- vizualni prikaz bez promjene
- mobilni prikaz
- desktop prikaz
- povratak na staro preko opcije

### Faza 7 — Rollback

Rollback mora biti jedna naredba:

`wp option update drycured_home_process_rail_use_hub 0 --allow-root && wp cache flush --allow-root`

Ako adapter stvara bilo kakav problem:

`rm -f wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php`

zatim:

`wp cache flush --allow-root`

## Minimalni budući plugin

Ako se odobri kodni korak, predloženi plugin bio bi:

`wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php`

U prvoj verziji smije raditi samo:

- čitati Process Hub registar
- izvesti usporedni audit
- ne mijenjati javni prikaz dok opcija nije uključena

## Uvjeti za kodni korak

Prije pisanja koda mora postojati:

- server backup
- GitHub checkpoint
- audit postojećeg home vodiča
- definirana rollback opcija
- potvrda da se ide na adapter, ne na zamjenu

## Zaključak

Home process rail se ne spaja odmah na Process Hub.

Prvo se izrađuje adapter u isključenom stanju, zatim audit, zatim testni prikaz, a tek nakon potvrde može se uključiti čitanje iz Process Hub registra.

