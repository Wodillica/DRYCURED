# DRYCURED_LATEX_EXTRACTION_EDGE_CASES_v1

Status: audit-based edge cases v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Nested include problemi

- Traversal mora pratiti samo stvarne `\input` i `\include` relacije iz `main.tex`.
- Filesystem sadrži stare i duplicirane `.tex` datoteke izvan aktivnog build grafa.
- Raw recursive crawl bi ingestirao pogrešne kopije i dvostruko brojao sadržaj.

---

## 2. Duplicate filesystem files izvan input grafa

- Duplicirane ili arhivske chapter datoteke postoje na disku, ali nisu kanonski dio builda.
- Takve datoteke moraju ostati izvan extraction scopea ako nisu aktivno referencirane.

---

## 3. Custom caption tip za infografike

- Infografike nisu zaseban environment nego caption type definiran preko `\DeclareCaptionType`.
- Prototype ih prepoznaje preko `\captionsetup{type=infografika}` unutar `figure` environmenta.
- Parser koji gleda samo `figure` bez caption type logike izgubio bi taj signal.

---

## 4. Prazni ili djelomični captioni

- Neke figure ili tablice mogu imati slab caption ili caption koji bez okolnog teksta nije dovoljan.
- Takvi visual objects ne smiju ostati retrieval-sami bez veze na surrounding section prose.

---

## 5. Objekti koji imaju smisao samo uz okolni tekst

- Warning blok bez prethodnog objašnjenja može biti prenagao.
- Tablica bez objašnjenja može izgubiti operativno značenje.
- Infografika bez okolnog teksta može izgubiti teorijski kontekst.

---

## 6. Macro noise

- Corpus koristi stilističke i pomoćne makroe koji nisu knowledge sami po sebi.
- Naivni strip svih makroa može oštetiti značenje ili unicode signal.
- Potreban je selektivni normalization pass, ne slijepo uklanjanje svega.

---

## 7. Reference koje ne resolveaju uredno

- `label/ref` parovi mogu upućivati na objekte izvan trenutnog sample scopea.
- Takve reference treba zadržati kao metadata, ali ne fabricirati lažne veze.

---

## 8. Retrieval rizik

- Ako section chunk postane preširok, retrieval će vraćati mutne rezultate.
- Ako se chunk previše usitni, warning, tablica i objašnjenje razdvajaju se i gube odgovorivost.
- Zato je v1 granularity: heading block + odvojeni custom/visual objects + reference bridge metadata.
