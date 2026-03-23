# SOURCE SCORING v1

## Svrha

Ovaj dokument definira početna pravila za ocjenu korisnosti i pouzdanosti web izvora u receptnom sustavu.

## Glavne osi ocjenjivanja

Svaki izvor treba ocijeniti u najmanje pet dimenzija:

1. relevantnost za suhomesnate proizvode
2. kvaliteta strukture sadržaja
3. tehnološka vrijednost
4. stabilnost dohvaćanja
5. razina pravnog i operativnog rizika

## Predložena skala

Svaka dimenzija ocjenjuje se od 1 do 5.
Ukupni zbroj služi za početnu klasifikaciju izvora.

## Dimenzije

### 1. Relevantnost
- 5 = izvor često sadrži suhomesnate recepte ili tradicijske opise s visokom preciznošću
- 3 = izvor povremeno ima korisne recepte ili članke
- 1 = rijetko ili usputno spominje temu

### 2. Struktura sadržaja
- 5 = jasan naslov, sastojci, koraci, dobar markup
- 3 = djelomično strukturirano, ali uz šum
- 1 = kaotičan ili slab sadržaj

### 3. Tehnološka vrijednost
- 5 = sadrži procesne detalje, omjere, trajanje, dimljenje, sušenje, zrenje
- 3 = sadrži djelomične procesne informacije
- 1 = gotovo bez tehnološke vrijednosti

### 4. Stabilnost dohvaćanja
- 5 = lako dohvatljivo, malo prepreka, dobar HTML
- 3 = djelomično stabilno, potreban fallback
- 1 = nestabilno, teško dohvatljivo ili previše dinamično

### 5. Rizik / ograničenja
- 5 = nizak rizik, razuman javni sadržaj, dobar kandidat za testni crawl
- 3 = potrebno dodatno razmatranje
- 1 = visok rizik ili neprikladno za scraping bez posebne provjere

## Početne klase izvora

### Klasa A
- 21 do 25 bodova
- visoka vrijednost
- pogodno za batch 2 i širenje

### Klasa B
- 15 do 20 bodova
- dobro za oprezni nastavak
- traži dodatnu evaluaciju

### Klasa C
- 9 do 14 bodova
- ograničena vrijednost
- zadržati samo ako daje specifične regionalne rezultate

### Klasa D
- 5 do 8 bodova
- slaba vrijednost ili previsok rizik
- prebaciti u review ili blokadu

## Obvezne napomene uz score
Uz brojčanu ocjenu treba bilježiti i kratke napomene:
- što je dobro
- što nedostaje
- treba li review
- treba li parser fallback

## Napomena

Score nije konačna istina, nego operativni alat za odluku što dalje testirati, a što ne.