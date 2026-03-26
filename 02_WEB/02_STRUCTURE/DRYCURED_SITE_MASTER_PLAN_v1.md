# DRYCURED_SITE_MASTER_PLAN_v1

Status: radna kanonska verzija v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira glavni plan razvoja drycured.com kao modernog, interaktivnog i sadržajno-stručnog sustava.

Njegova svrha nije opisati još jedan generički WordPress projekt, nego zaključati logiku po kojoj drycured.com treba prerasti iz statične stranice u:

- edukativni portal
- operativni sustav za učenje i praktičan rad
- bazu znanja povezanu s alatima
- most između knjige, web sadržaja, kalkulatora i buduće aplikacije

---

## 2. Temeljna urednička i proizvodna odluka

Drycured.com se ne razvija kao:

- običan blog
- običan recept portal
- obična WordPress prezentacija
- statična enciklopedija bez alata

Drycured.com se razvija kao:

- digitalna enciklopedija suhomesnatih proizvoda
- interaktivni edukativni sustav
- praktični alat za donošenje odluka
- baza za budući recipe engine, kalkulatore i aplikaciju

Glavno pravilo glasi:

> Sadržaj, alati i navigacija moraju biti povezani u jedan operativni sustav.

---

## 3. Zaključana tehnološka odluka

### 3.1. Tema
- Astra ostaje aktivna i zadržana tema

### 3.2. Builder
- Elementor se koristi za layout i kontrolirane vizualne sekcije
- Elementor se ne koristi kao glavni sustav logike

### 3.3. WordPress uloga
WordPress ostaje glavni CMS sloj za:
- objave
- stranice
- strukturu sadržaja
- osnovnu prezentaciju

### 3.4. Interaktivna logika
Napredni alati i interaktivni moduli trebaju se postupno oslanjati na:
- custom module
- shortcode ili widget logiku
- vanjski backend / SWAB sloj gdje je potrebno

---

## 4. Osnovna arhitektura sajta

Drycured.com treba biti organiziran kroz 5 glavnih funkcionalnih slojeva:

### 4.1. Znanje
- enciklopedijski članci
- definicije
- procesi
- sirovine
- mikroklima
- greške i rješenja
- regionalne i tradicijske teme

### 4.2. Praksa
- how-to sadržaj
- vodiči
- praktični koraci
- priprema prostora i opreme

### 4.3. Recepti
- strukturirani recepti
- regionalne receptne skupine
- povezivanje s kalkulatorima

### 4.4. Alati
- kalkulatori
- recipe engine
- process helperi
- budući simulacijski moduli

### 4.5. Problemi i rješenja
- simptom → uzrok → korekcija
- dijagnostički sadržaj
- warning zone

---

## 5. Glavne sekcije sajta

Predlaže se sljedeća visoka navigacijska logika:

- Home
- Enciklopedija / Znanje
- Praksa
- Recepti
- Alati
- Problemi

---

## 6. Ključna UX odluka

Cijeli sajt ne smije biti statičan.

To znači:

- Home mora nuditi jasne akcije
- podstranice moraju voditi prema povezanim sadržajima i alatima
- korisnik ne smije ostati zarobljen u jednom tekstu bez sljedećeg koraka
- alati moraju biti vidljivi i logično povezani s člancima

Osnovno UX pravilo glasi:

> Svaka važna stranica mora pomoći korisniku da ili nauči, ili nešto izračuna, ili riješi problem.

---

## 7. Što se preuzima iz najboljih sličnih pristupa

### 7.1. Iz recipe i curing kalkulatora
Preuzima se:
- precizno skaliranje
- jasni ulazi i izlazi
- povezivanje recepta i formule
- osjećaj sigurnosti i ponovljivosti

### 7.2. Iz BBQ / smoking how-to sajtova
Preuzima se:
- praktičnost
- korak-po-korak logika
- blizina stvarnim problemima korisnika

### 7.3. Iz tehničkih i industrijskih sajtova
Preuzima se:
- struktura
- preglednost
- procesna logika

### 7.4. Iz modernih tool sajtova
Preuzima se:
- interaktivnost
- alatni blokovi
- sustav kao proizvod, a ne samo skup članaka

---

## 8. Što drycured.com ne smije postati

- preopterećen pluginima
- vizualno šaren i neujednačen
- statičan i pasivan
- kaotična mješavina bloga, trgovine i enciklopedije
- skup nepovezanih članaka bez navigacijske logike

---

## 9. Redoslijed rada

### Faza 1
- stabilizacija strukture i tehničke osnove
- kopija / staging logika
- definiranje novog Home modela

### Faza 2
- Home redesign plan
- model podstranica
- navigacijska logika

### Faza 3
- integracija prvog interaktivnog modula
- povezivanje objava, članaka i alatnog sloja

### Faza 4
- recipe engine širenje
- troubleshooting modul
- dodatni kalkulatori

### Faza 5
- prijenos odobrene verzije na live
- kontrolirana migracija i QA

---

## 10. Zaključane operativne odluke

- veće izmjene rade se na kopiji, ne na live verziji
- Astra ostaje temeljna tema
- Elementor se koristi kontrolirano i isključivo za layout
- interaktivni moduli ne smiju se graditi kao hrpa nepovezanih pluginova
- receptni modul i kalkulatori tretiraju se kao jezgra buduće vrijednosti sajta
- Home mora postati ulaz u sustav, a ne samo naslovna slika i nekoliko linkova

---

## 11. Zaključak

Drycured.com mora se razvijati kao interaktivni sustav za znanje, praksu i alate.

Njegova snaga nije u tome da bude još jedan lijep WordPress sajt, nego u tome da spoji:

- enciklopedijsku dubinu
- praktične procese
- recepte
- kalkulatore
- logiku problema i rješenja