# DRYCURED_LATEX_EXTRACTION_RULES_v1

Status: radni prototype rules v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Heading razine

- `\chapter` definira chapter kontekst za sve objekte u datoteci.
- `\section`, `\subsection` i `\subsubsection` definiraju minimalne chunk granice.
- Heading i pripadni prozni blok ostaju zajedno kao minimalna sigurna extraction jedinica.
- Zvjezdaste varijante headinga (`\section*`, `\subsection*`) tretiraju se jednako za knowledge extraction, ali bez oslanjanja na numeraciju.

---

## 2. Odlomci

- Prozni sadržaj se hvata između heading granica.
- Pražnjenja redaka razdvajaju odlomke.
- LaTeX prezentacijske naredbe se čiste, ali tekstualni sadržaj ostaje.
- Odlomak ostaje u istom knowledge objectu dok god ne ulazi u zaseban custom environment ili visual block.

---

## 3. Custom okruženja

- `upozorenjeBlok` postaje zaseban `warning` object prve klase.
- `legendaBlok` postaje `definition` object prve klase.
- `prakticniSavjet`, `majstorskaNapomena`, `zanimljivost`, `ekonomskaAnaliza`, `napomenaBlok`, `zakljucnaMisao` ostaju zasebni objects s baznim tipom `theory_block`.
- Custom environment se ne utapa u surrounding prose ako nosi urednički signal.

---

## 4. Tablice

- `table` environment postaje zaseban `table` object prve klase.
- `caption`, `label` i lokalni `ref` metadata sloj moraju ostati uz tablicu.
- Najbliži section prose blok ostaje odvojeni knowledge object, ali zadržava `related_table_ids` vezu ako postoji eksplicitna referenca ili lokalna bliskost.

---

## 5. Figure i infografike

- `figure` environment je bazni visual block.
- Ako unutar figure postoji `\captionsetup{type=infografika}`, object tip je `infographic`.
- Inače se koristi `figure_reference`.
- `caption`, `label`, `ref` i najbliži tekstualni kontekst moraju ostati povezani.

---

## 6. Label / ref veze

- `\label` i `\ref` prikupljaju se na object razini.
- Reference se koriste za spajanje prose blokova s tablicama i vizualima.
- Ako referenca ne resolvea na poznati visual/table object, čuva se kao sirovi metadata signal, ne odbacuje se.

---

## 7. Objects prve klase

U prototype v1 kao objects prve klase ulaze:

- `theory_block`
- `definition`
- `warning`
- `problem`
- `cause`
- `correction`
- `process_phase`
- `table`
- `infographic`
- `figure_reference`

---

## 8. Metadata sloj

Kao metadata ostaje:

- chapter
- section
- source_file
- labels
- refs
- related_visual_ids
- related_table_ids
- extraction_confidence
- extraction notes
