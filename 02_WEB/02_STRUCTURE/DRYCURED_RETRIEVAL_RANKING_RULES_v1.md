# DRYCURED_RETRIEVAL_RANKING_RULES_v1

Status: retrieval ranking rules v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Osnovno rangiranje

Rezultat se rangira kombinacijom:

- exact tag match
- lexical overlap
- object_type relevance
- chapter priority
- extraction confidence
- visual/table proximity

---

## 2. Boost pravila

Boost dobivaju:

- `warning` objekti za problem-oriented upite
- `problem`, `cause`, `correction` za simptomatske upite
- `process_phase` za procesne i teorijske upite o fazama
- `table` i `infographic` samo kad imaju blizak tekstualni anchor
- objekti iz poglavlja o dimljenju, sušenju, mikroklimi i kontroli kvalitete kad query to sugerira

---

## 3. Penalty pravila

Penalty dobivaju:

- periferni tekst bez odgovarajućih tagova
- vizuali bez jasnog caption ili bez veze na relevantan prose blok
- generički zaključci poglavlja kad postoje precizniji section-level kandidati
- objekti s nižom extraction confidence razinom kad postoji jači kandidat

---

## 4. Kako spriječiti da periferni tekst pobijedi glavni blok

- section-level relevantnost ima prednost nad chapter-level općenitošću
- `warning`, `problem`, `correction` i `process_phase` imaju viši prioritet od generičkog `theory_block` kad query nosi operativni signal
- visual objekti ne smiju pobijediti prose blok ako sami ne nose dovoljan odgovor

---

## 5. Povezivanje tekst + tablica + infografika

- prvo se bira glavni tekstualni kandidat
- zatim se dodaju povezani `related_table_ids` i `related_visual_ids`
- ako nema eksplicitne veze, visual ostaje sekundarni kandidat i ne ulazi automatski u top rezultat

---

## 6. Slabi ili konfliktni dokazi

Kad su dokazi slabi ili konfliktni:

- sustav vraća više kandidata niže sigurnosti
- confidence basis mora ostati vidljiv
- retrieval sloj ne smije fabricirati jedinstveni “točan” odgovor bez jačeg source signala

---

## 7. Semantic similarity placeholder

V1 još nema embedding scoring, ali model ostavlja mjesto za budući semantic layer:

- semantic similarity dolazi tek kao dodatni signal
- ne smije nadjačati source discipline, warning logiku ni jasne exact tag matcheve
