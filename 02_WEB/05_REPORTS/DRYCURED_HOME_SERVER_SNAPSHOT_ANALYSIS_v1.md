# DRYCURED_HOME_SERVER_SNAPSHOT_ANALYSIS_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Datum analize: 2026-03-31  
Izvor: `E:\SWAB_V2\website\drycured_local\imports\server_home\drycured_home_server_snapshot_2026-03-31.html`

---

## 1. Što je stvarno zatečeno

Snapshot je read-only HTML izlaz aktivne live naslovnice. Ne sadrži WordPress bazu, Elementor JSON ni administrativne postavke, ali vrlo jasno otkriva:

- da live Home renderira Astra + Elementor
- da je aktivni live Home vrlo vjerojatno vezan uz WordPress page `101`
- da postoji stvarni Elementor Pro latest posts blok
- da header već sadrži GTranslate integraciju kao poseban slot u meniju

Ključni tehnički tragovi:

- `body` sadrži `page-id-101` i `elementor-page-101`
- učitava se `elementor-post-101.css`
- navigacija sadrži `current_page_item page-item-101`
- učitani su Astra `4.8.13`, Elementor `3.27.6`, Elementor Pro widget CSS i GTranslate widget skripte

---

## 2. Glavne sekcije live Home snapshota

### 2.1. Header / menu

Desktop i mobile header dijele istu osnovnu navigaciju:

- Home
- Recipes
- Blog
- Shop
- My account
- Knjiga — Preview
- GTranslate placeholder / wrapper

To znači da live Home već ima spoj:

- sadržaja
- trgovine / računa
- knjige
- jezičnog switchera

ali bez dodatne smart-header logike za brze ulaze u znanje, probleme i kalkulator.

### 2.2. Hero

Hero je uredan i jasan:

- H1: `Enciklopedija suhomesnatih proizvoda — europska digitalna pušnica`
- kratki opis o tradiciji, znanju i preciznim recepturama
- primarni CTA: `ISTRAŽI →`

Ovo je aktivni live sadržaj, ne samo prezentacijski wrapper.

### 2.3. Sekcija “Učimo zajedno: Tehnike, povijest i savjeti”

Sekcija sadrži:

- naslov
- CTA `PREGLEDAJ SVE`
- tri kartice:
  - Enciklopedija znanja
  - Recepture
  - Atlas stilova Europe

Ovo je trenutno glavni editorijalni gateway prema sadržaju.

### 2.4. Sekcija “Najnoviji članci”

Ovo je stvarni dinamički latest posts blok. Snapshot pokazuje:

- naslov sekcije `Najnoviji članci`
- barem tri objave
- naslov, datum, excerpt i CTA logiku

To je najvažniji trag da Home već sadrži aktivni sadržajni ritam, a ne samo statične marketinške blokove.

### 2.5. Trust / knjiga blok

Sekcija `Tradicija zapisana, znanje dostupno` referencira knjigu `Enciklopedija suhomesnatih proizvoda (Davor Savicki, 2025)` i gradi trust sloj između knjige i platforme.

### 2.6. Audience / value blok

Tri kartice:

- Za hobiste i kućne proizvođače
- Za iskusne mesare i majstore
- Za istraživače tradicije

Ovaj dio već dobro komunicira kome je platforma namijenjena.

### 2.7. “Zašto je drycured.com jedinstven?”

Sekcija s icon-box logikom i argumentima tipa:

- Preciznost
- Tradicija + znanost
- Za sve razine

### 2.8. Završni CTA

Sekcija `Spoj tradicije i digitalnog doba` završava Home tok i vodi na `POČNI ISTRAŽIVATI`.

---

## 3. Što je aktivni sadržaj, a što wrapper

### Očito aktivni sadržaj

- hero copy
- navigacija
- tri knowledge kartice
- latest posts blok
- knjiga / trust copy
- audience kartice
- uniqueness argumenti
- završni CTA

### Očito prezentacijski wrapper

- Astra header/footer builder shell
- Elementor section/container markup
- globalni CSS slojevi
- GTranslate render wrapperi

---

## 4. Što vrijedi zadržati

- prepoznatljiv hero i enciklopedijski positioning
- logiku sekcije `Učimo zajedno`
- latest posts kao znak da sajt živi
- knjiga/trust sloj
- ozbiljan ton i relativno čist ritam stranice

---

## 5. Kandidati za interaktivnu nadogradnju

- klasični menu bar treba prerasti u smart header
- Home trenutno nema dovoljno jasno odvojene truth-layer zone
- vanjski sadržaj i community sadržaj nisu urednički odvojeni jer na Home još nisu eksplicitno modelirani
- kalkulator, problemi i knowledge entrypoints nisu dovoljno istaknuti kao operativni ulazi
- GTranslate je tehnički prisutan, ali više kao widget slot nego dio zrele header logike

---

## 6. Koliko je snapshot koristan kao baza

Snapshot je vrlo koristan za:

- rekonstrukciju stvarnog redoslijeda sekcija
- identifikaciju aktivnih CTA zona
- očitanje trenutne navigacije
- potvrdu da Home već ima živi latest posts sloj
- planiranje interaction copy nadogradnje bez totalnog rebuilda

Snapshot nije dovoljan za:

- vjernu Elementor duplikaciju
- izvlačenje postavki svakog widgeta
- sigurnu rekonstrukciju svih dinamičkih query parametara

---

## 7. Ključni zaključak

Najvažniji nalaz je da live Home nije kaotična landing stranica nego već formiran editorial/brand tok u Asti + Elementoru. Zato sljedeća interaction copy faza treba biti:

- nadogradnja interakcije
- nadogradnja header logike
- uvođenje truth-layer zona

a ne totalni rebuild cijele naslovnice.

---

## 8. Warning

Iako je snapshot dovoljno dobar za specifikaciju, on nije potpuna WordPress/Elementor kopija. To znači da svaka kasnija lokalna izvedba mora ostati svjesna da se radi o rekonstrukciji na temelju renderiranog outputa, a ne o 1:1 bazičnoj duplikaciji live page builder stanja.
