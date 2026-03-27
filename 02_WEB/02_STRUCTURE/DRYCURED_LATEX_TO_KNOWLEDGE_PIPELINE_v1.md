# DRYCURED_LATEX_TO_KNOWLEDGE_PIPELINE_v1

Status: pipeline plan v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira kako LaTeX knjiga tehnički postaje strukturirani knowledge sloj za DRYCURED AI.

Cilj nije još puni parser, nego ozbiljan plan extraction pipelinea koji ne gubi značenje knjige.

---

## 2. Polazni nalaz

LaTeX projekt sadrži:
- glavni entrypoint `main.tex`
- chapter split u više `.tex` datoteka
- `frontmatter`, `chapters`, `backmatter`
- `config/style.tex` s custom okruženjima
- slike i infografike preko `graphicspath`
- `\caption`, `\label`, `\ref` i custom `infografika` tip

To znači da pipeline mora raditi nad source grafom, ne nad jednim izvoznim PDF-om.

---

## 3. Pipeline faze

### Faza 1: Source inventory
Koraci:
- očitati `main.tex`
- izgraditi listu svih uključenih `.tex` datoteka
- razdvojiti frontmatter, chapters i backmatter
- očitati `config/style.tex`
- popisati custom makroe i custom environments

Izlaz:
- source manifest
- file dependency graph

### Faza 2: Include resolution
Koraci:
- razmotati `\input` i `\include` pozive
- podržati `SafeInput` / `SafeInputFile` fallback logiku
- canonicalizirati putanje s razmacima, dijakritikom i zagradama

Izlaz:
- linearizirani source stream s očuvanim file granicama

### Faza 3: Structural parsing
Koraci:
- parsirati `\chapter`, `\section`, `\subsection`, `\subsubsection`
- parsirati `\section*` i `\addcontentsline` za numerički i nenumerički sadržaj
- vezati svaki segment na chapter/section kontekst

Izlaz:
- structural tree
- section objects

### Faza 4: Semantic block extraction
Koraci:
- izdvojiti obične odlomke
- izdvojiti custom blokove
- prepoznati quote, description, enumerate, itemize i blokove s dijagnostičkom logikom
- prepoznati patterne tipa:
  - simptom -> uzrok -> korekcija
  - warning prag
  - orijentacijska matrica
  - faze procesa

Izlaz:
- semantički chunkovi s tipom i granicama

### Faza 5: Table extraction
Koraci:
- parsirati `table`, `longtable`, `tabularx`, `threeparttable` i srodne strukture
- vezati `caption`, `label`, `tablenotes` i chapter kontekst
- spremiti raw LaTeX i normalized table metadata

Izlaz:
- table objects
- table summaries

### Faza 6: Figure / infografika extraction
Koraci:
- parsirati `figure`
- detektirati `\captionsetup{type=infografika}`
- izdvojiti asset path, caption, label i visual type
- razlikovati:
  - standardnu sliku
  - infografiku

Izlaz:
- visual objects
- infografika objects

### Faza 7: Reference graph extraction
Koraci:
- parsirati `\label{...}`
- parsirati `\ref{...}` i srodne reference
- povezati tekstualne chunkove s tablicama i vizualima

Izlaz:
- graph relacija objekt -> objekt
- graph relacija sekcija -> tablica / infografika / slika

### Faza 8: Knowledge object mapping
Koraci:
- pretvoriti structural i semantic segmente u knowledge objekte
- dodijeliti object type, tags i source evidence
- očuvati source granice do file/section/label razine

Izlaz:
- canonical knowledge object collection

### Faza 9: Chunking for retrieval
Pravila:
- chunking ne smije razdvajati caption od tablice/slike
- chunking ne smije rezati problem->uzrok->korekcija lanac
- chunking ne smije razdvajati warning blok od njegovog konteksta
- section-level i object-level chunking moraju koegzistirati

Izlaz:
- retrieval chunks
- object-aware embeddings / lexical index units

---

## 4. Što pipeline mora eksplicitno podržati

- `\input` hijerarhiju
- chapter i section kontekst
- custom environmente iz `style.tex`
- `figure`, `table`, `infografika`
- `caption`, `label`, `ref`
- assets putanje
- tablenotes i footnote-like kontekst tablica
- nenumerirane sekcije koje su dio znanja

---

## 5. Što pipeline ne smije raditi

- flattenati sve u jedan plain-text dump
- izgubiti vezu između teksta i vizuala
- izgubiti chapter / section porijeklo
- tretirati infografiku kao običnu sliku bez semantic weighta
- rezati knowledge chunkove samo po duljini tokena bez semantike

---

## 6. Zaključak

DRYCURED LaTeX pipeline mora biti source-aware i object-aware.

Drugim riječima:
- prvo se parsira knjiga kao dokumentni sustav
- tek onda se radi retrieval sloj

To je temelj da AI odgovori ostanu vezani uz stvarni sadržaj knjige.
