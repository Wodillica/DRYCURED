# DRYCURED_RETRIEVAL_READY_KNOWLEDGE_INDEX_PROTOTYPE_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Što je prototype index

Ovo je retrieval-ready prototip knowledge indexa građen iz već postojećeg extraction outputa, ne iz raw LaTeX crawl-a. Ulaz je ostao strogo vezan uz kanonski graph od `D:\drycured.com_prijevod\01_IZVOR\main.tex`, koji trenutno ima `37` aktivnih čvorova u manifestu.

Index transformira extraction objekte u retrieval jedinice koje imaju:

- stabilni object identity
- searchable tekst
- kratki retrieval summary
- problem/theory/process/warning tagove
- source discipline metadata
- retrieval priority i confidence basis

---

## 2. Kako je građen iz extraction outputa

Ulazi:

- `DRYCURED_LATEX_INPUT_GRAPH_MANIFEST_v1.json`
- `DRYCURED_LATEX_KNOWLEDGE_OBJECTS_SAMPLE_v1.json`
- extraction rules i edge cases dokumenti

Transform radi:

- zadržavanje source discipline iz extraction sloja
- dodavanje retrieval polja bez promjene izvornog sadržaja
- heurističko tagiranje za problem, teoriju, proces i warning logiku
- dodavanje query mode signala za budući Problem mode i Theory mode

---

## 3. Što sada već omogućuje

- razlikovanje problem-oriented i theory-oriented retrieval logike
- boostanje warning i process-sensitive blokova
- povezivanje tekstualnih blokova s tablicama i infografikama
- keyword, tag-based i retrieval-priority signale za budući ranking
- pripremu za semantic retrieval sloj bez rušenja source discipline

Sample index trenutno obuhvaća `164` retrieval jedinica:

- cause: 7
- correction: 7
- figure_reference: 15
- infographic: 22
- problem: 35
- process_phase: 15
- table: 5
- theory_block: 57
- warning: 1

---

## 4. Što još ne omogućuje

- pravi embedding ili semantic similarity scoring
- conflict resolution među više konkurentnih izvora
- answer composition
- full-book ingestion
- kvalitetno spajanje svih vizuala s okolnim tekstom kad nema eksplicitnih referenci

---

## 5. Spremnost za sljedeću fazu

Prototype je dovoljno spreman za sljedeću fazu:

- AI answer composition prototype v1

To vrijedi pod uvjetom da answer layer koristi index kao retrieval bazu, a ne da ponovno improvizira izvan source discipline.

---

## 6. Konačni status

PASS WITH WARNING

Warning:

- Retrieval index je strukturno spreman, ali semantičko tagiranje i ranking još su heuristički i moraju ostati kontrolirani dok se ne uvede retrieval evaluation nad širim corpusom.
