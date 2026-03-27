# DRYCURED_RETRIEVAL_QUERY_MODEL_v1

Status: retrieval query model v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Problem query

Sustav pokušava pronaći:

- simptom
- najvjerojatnije uzroke
- korekcije
- warning blokove
- povezane tablice i infografike

Glavni signali:

- exact problem tag match
- warning tag match
- chapter/section relevance iz poglavlja o greškama, mikroklimi, sušenju i dimljenju
- lexical overlap na simptomskim riječima
- proximity do tablica s dijagnozom ili matricama grešaka

Retrieval sloju treba vratiti:

- primarne problem/cause/correction/warning blokove
- sekundarne process/theory blokove kao objašnjenje
- povezane tablice i infografike

---

## 2. Theory query

Sustav pokušava pronaći:

- definiciju ili objašnjenje teme
- procesni kontekst
- povezane warninge ako teorija nosi sigurnosni prag
- relevantne tablice i infografike

Glavni signali:

- theory tag match
- section relevance
- chapter priority
- lexical overlap s teorijskim pojmovima
- figure/table proximity kad vizual pojašnjava teorijski blok

Retrieval sloju treba vratiti:

- primarne theory_block / definition / process_phase objekte
- povezane warning objekte samo ako su stvarno relevantni
- povezane vizuale i tablice

---

## 3. Process query

Status:

- rezervirano / optional v1

Sustav pokušava pronaći:

- fazu procesa
- operativne parametre
- warning pragove
- tablice i infografike koje sažimaju režim

Glavni signali:

- process tag match
- object_type = process_phase
- chapter priority za dimljenje, sušenje, mikroklimu i kontrolu kvalitete
- lexical overlap na faznim terminima

Retrieval sloju treba vratiti:

- process_phase objekte
- supporting theory blokove
- povezane warninge
- tablice i infografike s operativnim signalom
