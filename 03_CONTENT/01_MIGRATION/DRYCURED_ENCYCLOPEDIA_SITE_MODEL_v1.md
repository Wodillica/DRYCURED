# DRYCURED_ENCYCLOPEDIA_SITE_MODEL_v1

Status: radna kanonska verzija v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira prvi stvarni sadržajni model za enciklopedijski sloj web stranice **drycured.com**.

Njegova svrha nije stvaranje još jednog planskog papira, nego zaključavanje osnovne logike po kojoj se sadržaj iz knjige **Enciklopedija suhomesnatih proizvoda** pretvara u web-prirodne cjeline.

Ovaj dokument definira:

- koje enciklopedijske sekcije postoje na webu
- kako su hijerarhijski složene
- koje tipove stranica koristimo
- kako se sadržaj iz knjige reže na web cjeline
- što je prioritet za prvi sadržajni val

Važno:

- **Home stranica se ne dira**
- ovaj model vrijedi samo za sadržaj do kojeg se dolazi preko Home
- model je napravljen za **TOM 1**, koji je pretežno enciklopedijsko-teorijski
- recepti u ovoj fazi nisu glavni modul i ne tretiraju se kao prvi sadržajni fokus

---

## 2. Osnovna urednička odluka

drycured.com se u ovoj fazi ne gradi kao:

- klasični blog
- klasični recept portal
- klasična trgovina

nego kao:

- **digitalna enciklopedija suhomesnatih proizvoda**
- **edukativni portal**
- **baza znanja za buduću aplikaciju i alatne module**

Iz toga slijedi glavno pravilo:

> Prvi web sadržaj mora graditi autoritet, jasnoću i navigacijsku logiku, a ne gomilati nepovezane članke.

---

## 3. Glavne enciklopedijske sekcije na webu

Prvi sadržajni model drycured.com treba biti organiziran u ovih 6 glavnih sekcija.

### 3.1. Uvod u svijet suhomesnatih proizvoda

Svrha:
- uvodi korisnika u temu
- objašnjava što drycured.com jest
- postavlja osnovni kontekst za daljnje čitanje

Tipični sadržaji:
- što su suhomesnati proizvodi
- osnovne podjele proizvoda
- tradicionalna i suvremena proizvodnja
- razlika između kućne i industrijske logike

### 3.2. Sirovine i osnove

Svrha:
- objašnjava temeljne građevne elemente proizvoda
- stvara bazu za razumijevanje kasnijih tehnoloških poglavlja

Tipični sadržaji:
- meso i masno tkivo
- sol i nitritna sol
- začini i aromatika
- voda, vino, češnjak i pomoćne komponente
- crijeva i ovoji

### 3.3. Tehnološki procesi

Svrha:
- objašnjava glavne proizvodne korake
- služi kao most između enciklopedije i budućih praktičnih modula

Tipični sadržaji:
- soljenje
- salamurenje
- usitnjavanje i miješanje
- punjenje u crijeva
- dimljenje
- sušenje
- zrenje
- skladištenje

### 3.4. Mikroklima, kontrola i sigurnost

Svrha:
- okuplja znanje koje sprječava greške i gubitke
- daje korisniku osjećaj kontrole nad procesom

Tipični sadržaji:
- temperatura
- relativna vlaga
- strujanje zraka
- higijena
- mikrobiološki rizici
- sigurnost proizvoda

### 3.5. Greške, problemi i rješenja

Svrha:
- vrlo praktična sekcija
- prati filozofiju knjige: simptom → uzrok → korekcija

Tipični sadržaji:
- presušenost
- mekana sredina
- kiseli mirisi
- nepravilan presjek
- plijesni
- problemi pri dimljenju
- problemi pri fermentaciji ili zrenju

### 3.6. Tradicije, regije i proizvodne kulture

Svrha:
- povezuje znanje s identitetom proizvoda
- daje enciklopediji širinu i kulturnu vrijednost

Tipični sadržaji:
- regionalne škole suhomesnatih proizvoda
- razlike među državama i regijama
- tradicijski pristupi
- nazivlje i posebnosti

---

## 4. Hijerarhijski model web strukture

Enciklopedijski sloj ne smije biti jedna ravna lista članaka. Mora imati jasan model hijerarhije.

Predlaže se model od 3 razine.

### RAZINA 1 — glavne sekcije

To su glavni ulazi u enciklopedijski dio:

- Uvod
- Sirovine i osnove
- Tehnološki procesi
- Mikroklima, kontrola i sigurnost
- Greške, problemi i rješenja
- Tradicije, regije i proizvodne kulture

### RAZINA 2 — pillar stranice

Svaka glavna sekcija treba imati jednu ili više čvrstih preglednih stranica koje služe kao glavna ulazna točka.

Primjeri:

- `Tehnološki procesi`
- `Sirovine i osnove`
- `Greške i rješenja`

Te stranice nisu obični članci, nego pregledne stranice koje:

- daju sažetak teme
- objašnjavaju logiku sekcije
- vode prema podstranicama
- povezuju srodne teme

### RAZINA 3 — podstranice / tematski članci

To su konkretne stranice kao što su:

- `Soljenje mesa`
- `Dimljenje i vrste dima`
- `Kontrola vlage tijekom sušenja`
- `Zašto kobasica ostaje mekana u sredini`
- `Uloga svinjskog masnog tkiva`

To je osnovna radna jedinica web enciklopedije.

---

## 5. Tipovi stranica koje koristimo

Za enciklopedijski dio trebamo samo nekoliko jasnih tipova stranica.

### 5.1. Sekcijska ulazna stranica

Funkcija:
- uvod u veliku temu
- pregled podtema
- navigacija prema pillar i podstranicama

Primjeri:
- `Tehnološki procesi`
- `Sirovine i osnove`

### 5.2. Pillar stranica

Funkcija:
- široki pregled veće teme
- okupljanje povezanih podtema
- stvaranje autoriteta i SEO jezgre

Primjeri:
- `Dimljenje suhomesnatih proizvoda`
- `Sušenje i zrenje`
- `Greške pri izradi kobasica`

### 5.3. Tematski članak

Funkcija:
- obrada jednog uskog pitanja ili jednog jasnog procesa

Primjeri:
- `Hladno i toplo dimljenje`
- `Kako vlaga utječe na površinsko sušenje`
- `Zašto se javlja tvrda kora`

### 5.4. Troubleshooting članak

Funkcija:
- rješavanje konkretnih problema
- uvijek prati formulu:
  - simptom
  - mogući uzroci
  - korekcije

Ovo je važan posebni tip stranice jer odgovara filozofiji knjige.

### 5.5. Regionalni enciklopedijski članak

Funkcija:
- prikazuje neku državu, regiju ili proizvodnu tradiciju
- veže znanje uz geografiju i kulturu

Primjeri:
- `Slavonska tradicija trajnih kobasica`
- `Dalmatinski pristup pršutu`
- `Srednjoeuropske škole dimljenja`

---

## 6. Kako rezati sadržaj knjige u web-prirodne cjeline

Knjiga i web ne smiju imati isti ritam.

Knjiga može nositi duže blokove i linearnu logiku. Web traži jasnije i kraće jedinice.

Zato vrijede ova pravila.

### Pravilo 1
Jedno veliko poglavlje iz knjige ne postaje automatski jedna web stranica.

### Pravilo 2
Jedna web stranica mora imati jednu dominantnu temu i jasan razlog postojanja.

### Pravilo 3
Dugi teorijski blokovi iz knjige režu se na:
- preglednu pillar stranicu
- više uskih tematskih članaka
- eventualno troubleshooting članak ako tema ima praktične probleme

### Pravilo 4
Tablice, matrice i vrlo gusti tehnički dijelovi ne lijepe se sirovo na web, nego se:
- sažimaju
- interpretiraju
- pretvaraju u čitljive sekcije
- po potrebi kasnije pretvaraju u interaktivne module

### Pravilo 5
Ako neki dio knjige nema dovoljno web-prirodan oblik, ne objavljuje se odmah, nego čeka drugu fazu.

---

## 7. Minimalni model jedne enciklopedijske stranice

Svaka enciklopedijska stranica na drycured.com trebala bi slijediti isti osnovni obrazac.

### Obavezni dijelovi

1. **Naslov stranice**  
2. **Kratki uvodni sažetak**  
3. **Glavni sadržaj podijeljen u logične podnaslove**  
4. **Praktični zaključak ili sažetak za čitatelja**  
5. **Povezane teme**  
6. **Ako je primjenjivo: problemi i rješenja**  
7. **Ako je primjenjivo: poveznica prema budućim receptima ili alatima**

### Dodatni dijelovi po potrebi

- infografika
- tablica
- regionalna napomena
- stručna napomena
- kratki FAQ blok

---

## 8. Navigacijska logika

Navigacija enciklopedijskog sloja mora biti jednostavna i prirodna.

Korisnik mora moći ući u sadržaj barem na 4 načina:

### 8.1. Po velikim temama
Primjer:
- Sirovine
- Procesi
- Mikroklima
- Greške

### 8.2. Po razini znanja
Primjer:
- Početnici
- Napredni kućni proizvođači
- Stručni čitatelji

### 8.3. Po problemu koji korisnik želi riješiti
Primjer:
- presušivanje
- loš presjek
- nepravilan miris
- plijesan

### 8.4. Po regiji ili tradiciji
Primjer:
- Hrvatska
- Balkan
- Srednja Europa
- Mediteran

Važno:

U prvom krugu implementacije ne treba odjednom graditi sve navigacijske ulaze, ali model mora biti pripremljen tako da ih može prihvatiti.

---

## 9. Što nije prioritet u prvom krugu

U ovoj fazi nije prioritet:

- puni receptni sustav
- kalkulator sastojaka
- duboki alatni moduli
- masivna baza svih regija i svih proizvoda
- sirovi prijenos tablica iz knjige bez obrade

To dolazi kasnije.

Prvi krug treba zaključati:

- enciklopedijsku jezgru
- pregledne ulazne sekcije
- prve kvalitetne pillar stranice
- nekoliko jakih tematskih članaka

---

## 10. Prioritet za prvi sadržajni val

Za prvi val predlaže se da se ne širi na sve teme odjednom, nego da se krene s jezgrenim blokovima koji imaju najveću vrijednost za korisnika i najbolji web potencijal.

### PRIORITET A — temeljne edukativne cjeline

- što su suhomesnati proizvodi
- osnovna podjela proizvoda
- razlika između svježih, polusuhih i trajnih proizvoda
- osnovna logika kućne proizvodnje

### PRIORITET B — sirovine i osnove

- meso i masno tkivo
- sol i funkcija soli
- češnjak, vino, voda i pomoćne tekućine
- crijeva i ovoji

### PRIORITET C — procesi

- soljenje
- salamurenje
- dimljenje
- sušenje
- zrenje

### PRIORITET D — problemi i rješenja

- najčešće greške pri sušenju
- problemi s presjekom kobasice
- prebrzo sušenje površine
- mirisi i mikrobiološki warning znakovi

---

## 11. Operativna pravila za daljnji rad

### Pravilo A
Nakon ovog dokumenta ne radimo beskonačno nove planove, nego prelazimo na stvarni sadržaj.

### Pravilo B
Svaki novi sadržajni korak mora direktno voditi prema:
- novoj web sekciji
- novom modelu stranice
- ili stvarnom web tekstu

### Pravilo C
Recepti ostaju poseban budući modul i ne miješaju se sada s enciklopedijskom jezgrom.

### Pravilo D
Ako neki sadržaj traži kalkulator, strukturu podataka ili tehnološki engine, označava se kao **kasnija faza**, a ne gura naslijepo u prvi val.

---

## 12. Zaključak

Ovaj dokument zaključava osnovni model prvog pravog sadržajnog sloja za drycured.com.

Prvi fokus weba nije receptni katalog, nego **enciklopedijska jezgra** koja:

- prenosi znanje iz knjige u web-prirodan oblik
- gradi autoritet stranice
- otvara jasan navigacijski sustav
- priprema teren za kasnije module: tehnologiju, savjete, recepte i kalkulator

Sljedeći operativni korak nakon ovog dokumenta je:

**BOOK_TO_WEB_MAPPING_ENCYCLOPEDIA_v1**

odnosno konkretno mapiranje sadržaja knjige u ove web sekcije i tipove stranica.
