# DRYCURED_AI_KNOWLEDGE_MASTER_ARCHITECTURE_v1

Status: master architecture v1  
Projekt: drycured.com  
Knowledge source: LaTeX knjiga kao kanonski izvor  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira jedinstvenu tehničku arhitekturu kojom LaTeX knjiga postaje DRYCURED AI KNOWLEDGE CORE.

Cilj nije generički chatbot niti slobodni RAG sloj bez discipline, nego sustav koji:
- odgovara na pitanja iz knjige
- pomaže u dijagnostici problema
- prikazuje teoriju i procesni kontekst
- veže tekst, tablice, slike i infografike u jedan dokazivi odgovor

---

## 2. Temeljna odluka

Knjiga se ne tretira kao PDF blob.

Knjiga se tretira kao strukturirani LaTeX knowledge source koji već sadrži:
- chapter i section hijerarhiju
- subsection i subsubsection granularnost
- label/reference sustav
- tablice s captionima i labelama
- slike s captionima i labelama
- poseban infografika tip s vlastitim popisom
- custom blok okruženja za upozorenja, savjete, napomene i slične semantičke elemente

Glavno pravilo glasi:

> AI sloj mora odgovarati iz strukturiranih knowledge objekata izvučenih iz LaTeX izvora, a ne iz nestrukturiranog teksta bez porijekla.

---

## 3. Stvarni LaTeX nalaz koji određuje arhitekturu

Na temelju pregleda `D:\drycured.com_prijevod\01_IZVOR\main.tex` i `config/style.tex` potvrđeno je:

- knjiga je složena kao `book` projekt s `\input` hijerarhijom
- `main.tex` uključuje frontmatter, 25 chapter datoteka, backmatter i zasebne popise
- postoje `\listoffigures`, `\listoftables` i `\listof{infografika}{Popis infografika}`
- definiran je custom caption type `infografika`
- slike i infografike imaju stabilne asset putanje i caption sloj
- chapter datoteke imaju `\chapter`, `\section`, `\subsection`, `\label`, `\ref`
- style sloj definira custom blok okruženja:
  - `legendaBlok`
  - `prakticniSavjet`
  - `majstorskaNapomena`
  - `zanimljivost`
  - `upozorenjeBlok`
  - `ekonomskaAnaliza`
  - `napomenaBlok`
  - `zakljucnaMisao`
- u sadržaju se već pojavljuju problem logike tipa:
  - simptom -> uzrok -> korekcija
  - warning pragovi
  - orijentacijske tablice
  - mikroklimatske i procesne matrice

Iz toga slijedi da AI sloj mora podržavati i tekstualne i vizualne knowledge objekte.

---

## 4. Logički slojevi sustava

### 4.1. LaTeX source layer
Ulazi:
- `main.tex`
- chapter datoteke
- frontmatter/backmatter
- `config/style.tex`
- asset reference sloj za slike i infografike

Uloga:
- kanonski source of truth
- jedini dopušteni izvor za `confirmed_from_book` tvrdnje

### 4.2. Extraction / parsing layer
Uloga:
- razmotati `\input` i `\include` logiku
- očitati strukturu knjige
- izdvojiti semantičke blokove
- mapirati vizuale, captione, labele i reference

### 4.3. Knowledge object layer
Uloga:
- pretvoriti LaTeX segmente u stabilne knowledge objekte
- svakom objektu dati tip, source reference i relacije

### 4.4. Retrieval / index layer
Uloga:
- indeksirati knowledge objekte
- podržati problem-based i theory-based retrieval
- kombinirati tekst, tablice i infografike po relevantnosti

### 4.5. Answer generation layer
Uloga:
- generirati odgovor isključivo iz dohvaćenih objekata
- jasno razdvojiti potvrđeno, inferirano i nedovoljno dokazano

### 4.6. Drycured web integration layer
Uloga:
- izložiti AI odgovor webu i kasnijoj aplikaciji
- povezati odgovor sa sekcijama knjige, člancima, alatima i receptima

---

## 5. Dva osnovna moda rada

### 5.1. Problem mode
Ulaz:
- simptom
- defekt
- pitanje tipa “zašto se događa”
- pitanje tipa “kako ispraviti”

Izlaz mora sadržavati:
- kratki odgovor
- moguće uzroke
- preporučenu korekciju
- relevantne dijelove knjige
- povezane tablice
- povezane infografike
- upozorenje ako nema dovoljno izvora

### 5.2. Theory mode
Ulaz:
- teorijsko pitanje
- definicija
- objašnjenje procesa
- pitanje o mikroklimi, fermentaciji, sušenju, dimljenju, sigurnosti

Izlaz mora sadržavati:
- kratki odgovor
- detaljno objašnjenje
- procesni kontekst
- relevantne dijelove knjige
- tablice i infografike ako postoje
- jasno označen stupanj dokazivosti

---

## 6. Glavna arhitekturna pravila

- LaTeX knjiga je knowledge core, ne samo referenca
- retrieval radi nad knowledge objektima, ne samo nad raw chunkovima
- answer layer ne smije glumiti autoritet izvan retrieval dokaza
- tablice i infografike moraju biti first-class objekti
- problem mode i theory mode dijele isti core, ali različite retrieval prioritete
- web sloj mora moći prikazati evidence trail

---

## 7. Operativni zaključak

DRYCURED AI knowledge sustav mora biti građen kao disciplinirani pipeline:

LaTeX source -> extraction -> knowledge objects -> retrieval/ranking -> evidence-bound answer -> web integration

To je jedini održivi put da drycured.com dobije AI sloj koji je stručan, provjerljiv i stvarno vezan uz knjigu.
