# DRYCURED_HOME_INTERACTION_COPY_SPEC_v1

Status: radna specifikacija v1  
Projekt: drycured.com  
Temelj: live Home snapshot analiza 2026-03-31

---

## 1. Svrha dokumenta

Ovaj dokument definira kako postojeća live Home logika prelazi u novu interaction copy verziju bez totalnog rebuilda layouta.

Osnovna odluka glasi:

- Home ostaje prepoznatljiva
- ne ruši se postojeći tok stranice
- mijenja se urednička i interakcijska logika
- uvodi se truth-layer razdvajanje sadržaja

---

## 2. Temeljna promjena

Nova interaction copy verzija Home ne gradi još novi identitet cijelog sajta, nego radi ovih 5 promjena:

1. klasični header prelazi u smart header  
2. ritam Home stranice postaje interaktivniji  
3. verified sadržaj se jasno odvaja od vanjskih izvora  
4. video i community sloj dobivaju vlastite zone  
5. alatni i knowledge ulazi postaju jasniji

---

## 3. Ciljna struktura Home interaction copy verzije

### 3.1. SMART HEADER

Sadržaj:

- logo
- glavna navigacija
- prostor za language switcher
- brzi ulazi:
  - Znanje
  - Problemi
  - Kalkulator
  - Aktualno

Funkcija:

- pretvoriti običan menu bar u operativno zaglavlje
- odmah korisniku pokazati glavne ulaze u sustav

### 3.2. HERO

Ostaje prepoznatljiv aktualni enciklopedijski positioning, ali CTA logika postaje jača:

- primarni CTA prema znanju / verified sloju
- sekundarni CTA prema problemima ili kalkulatoru

Funkcija:

- zadržati brand kontinuitet
- brže usmjeriti korisnika prema stvarnoj akciji

### 3.3. DRYCURED VERIFIED

Sadržaj:

- drycured članci
- potvrđeni sažeci
- verified analize
- latest posts iz internog sadržaja

Funkcija:

- jasno označiti što je naš potvrđeni sloj
- zadržati Home kao živi kanal, ali bez miješanja s vanjskim izvorima

### 3.4. VANJSKI IZVORI / AKTUALNO

Sadržaj:

- vanjske vijesti
- stručni članci
- relevantni noviji tekstovi

Obavezni metapodaci:

- izvor
- datum
- tip sadržaja

Funkcija:

- dovesti vanjski signal na Home
- bez lažnog predstavljanja kao drycured verified

### 3.5. VIDEO / MEDIA

V1 prioritet:

- YouTube
- X

Kasnije:

- drugi embed i media izvori po tehničkoj i pravnoj procjeni

Funkcija:

- Home dobiva živ medijski sloj
- ali ostaje jasno označeno da je riječ o video / embed sadržaju

### 3.6. ŠTO DRUGI KAŽU / LIVE DISKUSIJA

Sadržaj:

- mišljenja
- iskustva
- rasprave
- community signal

Funkcija:

- dati socijalni i terenski kontekst
- bez prikazivanja community mišljenja kao činjenice

### 3.7. DRYCURED TOOLS + KNOWLEDGE

Sadržaj:

- kalkulator
- problemi i rješenja
- knjiga
- tablice
- infografike
- kasnije AI knowledge ulaz

Funkcija:

- Home jasno komunicira da je drycured i alatni sustav, ne samo sadržajni portal

### 3.8. TRUST / KNJIGA

Aktualni trust blok o knjizi ostaje, ali se urednički jasnije pozicionira kao:

- kanonski izvor znanja
- temelj za verified sloj

---

## 4. Preporučeni redoslijed zona

1. Smart Header  
2. Hero  
3. Drycured Verified  
4. Vanjski izvori / Aktualno  
5. Video / Media  
6. Što drugi kažu / Live diskusija  
7. Drycured Tools + Knowledge  
8. Trust / Knjiga  
9. Završni CTA

---

## 5. Što ostaje isto

- enciklopedijski ton
- ozbiljan ritam Home stranice
- hero positioning
- knjiga kao trust temelj
- latest posts logika kao znak živog sajta

---

## 6. Što se mijenja

- menu bar postaje smart header
- verified i vanjski sadržaj više nisu u istom uredničkom sloju
- community i video dobivaju zasebne zone
- kalkulator i problemi dobivaju jači status operativnih ulaza
- uvodi se truth-layer označavanje svakog feed tipa

---

## 7. Granice ove specifikacije

Ova specifikacija ne radi:

- totalni layout rebuild
- finalni UI dizajn
- live deploy
- automatizaciju svih vanjskih feedova

Ona definira logiku po kojoj će se kasnije graditi stvarna lokalna WordPress interaction copy verzija.
