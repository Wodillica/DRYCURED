# DRYCURED_HOME_HEADER_REWORK_PLAN_v1

Status: radni plan v1  
Projekt: drycured.com  
Temelj: live Home snapshot + interaction copy logika

---

## 1. Svrha dokumenta

Ovaj dokument definira kako postojeći live menu bar prelazi u smart header bez totalnog rebuilda cijelog sajta.

---

## 2. Što postoji danas

Trenutni header sadrži:

- logo
- glavnu navigaciju
- GTranslate slot
- desktop i mobile varijantu kroz Astra header builder

Navigacija trenutno vodi na:

- Home
- Recipes
- Blog
- Shop
- My account
- Knjiga — Preview

---

## 3. Što ostaje

- logo lijevo
- glavna navigacijska osnova
- Astra header builder kao tehnički okvir
- language switcher prostor

---

## 4. Što se dodaje

Smart header mora dodati brze ulaze koji predstavljaju stvarnu logiku sustava:

- Znanje
- Problemi
- Kalkulator
- Aktualno

Ovi ulazi ne moraju nužno zamijeniti glavnu navigaciju. Mogu postojati kao:

- sekundarni quick access red
- utility nav
- istaknuti header chips / links

---

## 5. Ciljna logika zaglavlja

### Primarni sloj

- logo
- glavna navigacija
- language switcher

### Sekundarni operativni sloj

- Znanje
- Problemi
- Kalkulator
- Aktualno

Funkcija sekundarnog sloja je da korisniku odmah da operativni ulaz, bez potrebe da dekodira cijelu strukturu stranice.

---

## 6. Učinak na Home UX

Smart header mora postići:

- brže usmjeravanje korisnika
- manje oslanjanje na skrol kao jedini način otkrivanja sadržaja
- jače razlikovanje knowledge, tools i current content logike
- bolju podlogu za kasniji language switcher i truth-layer razvoj

---

## 7. Pravila izvedbe

- ne raditi totalni redesign headera
- ne uvoditi plugin kaos
- zadržati Astra kompatibilnost
- language switcher tretirati kao dio header sustava, ne kao strani widget
- quick entry logiku izvesti tako da radi i na desktopu i na mobitelu

---

## 8. Preporučeni v1 model

### Desktop

- lijevo: logo
- sredina: glavna navigacija
- desno: language switcher
- ispod ili uz navigaciju: quick entry links

### Mobile

- logo
- hamburger
- unutar mobile header logike:
  - language switcher
  - quick entries na vrhu drawer menija

---

## 9. Što ide kasnije

- finalni vizualni polish
- behavior detalji za scroll state headera
- personalizirani current content ulazi
- dublja integracija AI knowledge ulaza

---

## 10. Zaključak

Header rework nije kozmetika. To je prvi korak kojim Home prestaje biti samo klasična WordPress naslovnica i počinje raditi kao ulaz u:

- znanje
- probleme
- kalkulator
- aktualni sadržaj
