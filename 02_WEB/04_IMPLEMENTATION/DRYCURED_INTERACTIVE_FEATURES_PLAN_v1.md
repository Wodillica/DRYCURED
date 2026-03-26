# DRYCURED_INTERACTIVE_FEATURES_PLAN_v1

Status: radna kanonska verzija v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira plan interaktivnih značajki koje drycured.com trebaju pretvoriti iz statičnog sadržajnog sajta u operativni sustav.

Interaktivni sloj je ključna razlika između drycured.com i većine sličnih sajtova.

---

## 2. Temeljna odluka

Interaktivnost se ne gradi radi efekta, nego radi korisnosti.

Svaka interaktivna značajka mora pomoći korisniku da:

- nešto izračuna
- nešto razumije
- nešto prati
- nešto ispravi
- donese bolju odluku

---

## 3. Glavni interaktivni moduli

### 3.1. Kalkulator recepata
Status:
- prvi modul već postoji

Uloga:
- skaliranje recepta po količini mesa
- točne količine sastojaka
- veza između recepta i formule

Zašto je važan:
- to je prvi stvarni alatni sloj sajta
- odmah razlikuje drycured od običnog recept portala

### 3.2. Kalkulator soli
Uloga:
- izračun potrebne količine soli po tipu recepta ili procesa

Vrijednost:
- visok praktični učinak
- jednostavan za korisnika
- dobar ulaz u alatni sloj

### 3.3. Kalkulator procesa / helper
Uloga:
- povezivanje temperature, vlage i procesa
- osnovni warning / preporuka sloj

Vrijednost:
- vrlo jaka diferencijacija
- povezuje sadržaj i praksu

### 3.4. Troubleshooting engine
Uloga:
- korisnik bira simptom
- sustav vraća mogući uzrok i korekciju

Vrijednost:
- jedan od najvrjednijih modula za stvarne korisnike
- prirodno odgovara filozofiji knjige

### 3.5. Povezani sadržajni engine
Uloga:
- članak vodi na:
  - srodne članke
  - alat
  - problem
  - recept
  - proces

Vrijednost:
- smanjuje statičnost
- povećava vrijeme na sajtu
- stvara sustav, ne zbirku tekstova

---

## 4. Prioritet interaktivnih značajki

### PRIORITET 1
- Kalkulator recepata
- povezivanje alata s Home i člancima

### PRIORITET 2
- Kalkulator soli
- blok “Najčešći problemi”
- povezani sadržaji

### PRIORITET 3
- procesni helper
- mikroklimatski pomoćnik
- troubleshooting engine v1

### PRIORITET 4
- napredniji process simulator
- dublje povezivanje recipe enginea i buduće aplikacije

---

## 5. Kako interaktivnost mora biti ugrađena u sajt

### 5.1. Na Home stranici
Home mora prikazivati:
- istaknute alate
- najnovije objave
- problem-based ulaze
- najvažnije sekcije sustava

### 5.2. Na podstranicama
Svaka važna podstranica treba imati barem jedan od ovih elemenata:
- povezani alat
- povezani problem
- povezani recept
- povezani proces
- povezane teme

### 5.3. U receptnom sustavu
Recepti ne smiju biti običan tekst, nego:
- strukturirani
- skalabilni
- povezani s kalkulatorom

---

## 6. Dizajnerska pravila za interaktivne module

Interaktivni moduli moraju biti:
- moderni
- čisti
- jasni
- laki za korištenje
- vizualno usklađeni s Astrom i ostatkom sajta

Ne smiju biti:
- ružni plugin box
- odvojeni widget koji izgleda strano
- dizajnerski neskladan element

---

## 7. Najbolje prakse koje treba usvojiti

Na temelju sličnih recipe, BBQ, curing i tool sajtova, za drycured se usvaja:

- precizan input / output model
- jednostavni i brzi kalkulatori
- sigurnost i jasnoća izračuna
- korak-po-korak praktičnost
- navigacija prema problemu, ne samo prema temi
- alat kao sastavni dio sadržaja, ne odvojeni dodatak

---

## 8. Što treba izbjegavati

- gomilanje pluginova koji rade svatko svoju stvar
- vizualno različite alate bez jedinstvenog dizajnerskog jezika
- “fancy” interaktivnost bez stvarne koristi
- odvojene alate koji nisu povezani s člancima i receptima

---

## 9. Redoslijed implementacije

1. stabilizirati i gitati prvi kalkulator recepata  
2. definirati kako se prikazuje na sajtu  
3. povezati ga s Home i barem jednom podstranicom  
4. pripremiti plan za kalkulator soli  
5. pripremiti troubleshooting v1 logiku  
6. širiti interaktivni sloj tek nakon što prvi alatni blok radi stabilno

---

## 10. Kriterij uspjeha

Interaktivni plan je uspješan ako:

- korisnik brzo dolazi do korisnog alata
- alat je vizualno dio sajta
- alat je sadržajno povezan s člancima
- receptni sustav nije samo tekst nego operativni engine
- sajt djeluje kao sustav, ne kao arhiva

---

## 11. Zaključak

Interaktivni sloj je jedna od glavnih budućih konkurentskih prednosti drycured.com.

Najvažnije je:
- graditi ga disciplinirano
- povezivati ga sa sadržajem
- ne razbijati ga u nepovezane plugin eksperimente

Prvi korak tog sloja već postoji kroz kalkulator recepata.  
Sada ga treba pretvoriti iz izoliranog modula u vidljivi dio sustava.