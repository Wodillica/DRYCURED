# DRYCURED_AI_MODE_ROUTING_RULES_v1

Status: mode routing rules v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Problem upit

Problem upit sustav prepoznaje kad query sadrži signal poput:

- zašto se događa problem
- što uzrokuje simptom
- kako ispraviti grešku
- opis mirisa, plijesni, presušenja, ljepljive površine, pukotina ili druge greške

Primarni retrieval signali:

- problem tags
- warning tags
- symptom lexical overlap
- problem/cause/correction object types

---

## 2. Theory upit

Theory upit sustav prepoznaje kad query traži:

- objašnjenje pojma ili procesa
- razliku između procesa
- ulogu nekog faktora
- širi kontekst faze, mikroklime ili zrenja

Primarni retrieval signali:

- theory tags
- process tags
- chapter/section relevance
- process_phase i theory_block object types

---

## 3. Granični slučajevi

Na graničnim slučajevima:

- ako query nosi i simptom i proces, problem mode ima prednost
- theory blokovi mogu se dodati samo kao supporting context
- warning blok dobiva prednost samo kad je stvarno vezan uz simptom ili sigurnosni prag

---

## 4. Hybrid odgovor

Hybrid odgovor nije default.

Dopušten je samo kad:

- postoji jak problem anchor
- i postoji nužan process/theory context za razumljivo objašnjenje

Ako toga nema, sustav vraća čisti problem ili čisti theory mode.
