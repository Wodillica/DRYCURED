# DRYCURED_AI_RETRIEVAL_AND_RANKING_PLAN_v1

Status: retrieval plan v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira retrieval i ranking logiku za DRYCURED AI knowledge sloj.

Cilj nije samo naći sličan tekst, nego složiti dokazivi odgovor iz pravih knowledge objekata.

---

## 2. Retrieval principi

- retrieval radi nad knowledge objektima i njihovim chunkovima
- retrieval mora biti mode-aware
- ranking mora favorizirati source precision nad općom sličnosti
- tablice i infografike ulaze u ranking kao zasebni objekti

---

## 3. Dva retrieval moda

### Problem mode ranking prioritet
1. problem objekti
2. warning objekti
3. cause objekti
4. correction objekti
5. process phase objekti
6. povezane tablice
7. povezane infografike
8. theory blokovi kao potporni kontekst

### Theory mode ranking prioritet
1. theory blokovi
2. definicije
3. process phase objekti
4. warning objekti gdje su relevantni
5. tablice
6. infografike
7. povezani alati i recepti

---

## 4. Retrieval pipeline

### Stage A: Query classification
Sustav prvo klasificira upit kao:
- problem
- theory
- mixed
- insufficiently clear

### Stage B: Candidate retrieval
Paralelno dohvatiti:
- lexical kandidate
- semantic kandidate
- section-level kandidate
- table / infographic kandidate

### Stage C: Object graph expansion
Na temelju najboljih kandidata dohvatiti relacije:
- problem -> cause -> correction
- section -> table -> infographic
- theory -> process phase -> warning

### Stage D: Ranking
Signalne skupine:
- query-to-object relevance
- object type priority po modu
- source specificity
- graph proximity
- chapter / section authority
- evidencijski status

### Stage E: Evidence pack assembly
Finalni answer pack mora sadržavati:
- primarne objekte
- sekundarne objekte
- vizualne objekte
- source trail

---

## 5. Ranking pravila

- `confirmed_from_book` objekti imaju prednost nad inferiranima
- točno pogođena problem sekcija ima prednost nad općim teorijskim objašnjenjem
- tablica ili infografika se vraća samo ako pojačava odgovor
- generički visoko-slični chunk bez jasnog section konteksta ne smije pobijediti precizan object match

---

## 6. Posebno pravilo za tablice i infografike

Tablica ili infografika se uključuje samo ako zadovolji barem jedno:
- izravno je referencirana u dohvaćenom teorijskom ili problem objektu
- caption semantički odgovara queryju
- pripada istoj sekciji kao vodeći odgovor i nosi sažetak parametara / matrice / dijagnostike

---

## 7. V1 preporuka za tehničku izvedbu

Retrieval sloj u v1 treba imati:
- object metadata index
- lexical index
- embedding index
- relation graph index
- mode-aware reranker

---

## 8. Zaključak

DRYCURED retrieval mora biti evidence-first.

Najbolji odgovor nije onaj koji zvuči najpametnije, nego onaj koji je najpreciznije vezan uz prave objekte knjige.
