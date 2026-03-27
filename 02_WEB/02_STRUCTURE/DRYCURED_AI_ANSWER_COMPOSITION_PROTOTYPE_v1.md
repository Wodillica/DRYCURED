# DRYCURED_AI_ANSWER_COMPOSITION_PROTOTYPE_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Što prototype radi

Prototype sastavlja kontrolirani odgovor iz retrieval-ready indexa bez slobodnog generativnog zaključivanja. Odgovor se slaže iz tri odvojena kanala:

- problem evidence channel
- theory evidence channel
- visual support channel

Svaki output zadržava strogo odvajanje između:

- `confirmed_from_book`
- `inferred_from_book_context`
- `insufficient_evidence`

---

## 2. Iz kojih input signala sklapa odgovor

Composer koristi samo retrieval izlaz:

- object_type
- chapter i section kontekst
- concise summary i searchable tekst
- confidence basis
- warning, problem, theory i process signale
- tablice i infografike kao sekundarnu potporu

Ne koristi slobodni chat reasoning izvan retrieval paketa.

---

## 3. Što sada već omogućuje

- kontrolirani Problem answer mode
- kontrolirani Theory answer mode
- vidljivo odvajanje potvrđenih tvrdnji od inferencija
- evidence gating prije nego što se claim smije pojaviti u odgovoru
- uključivanje tablica i infografika kao support sloja bez lažnog autoriteta

---

## 4. Što još ne omogućuje

- automatski retrieval run-time za proizvoljne upite
- puni answer engine nad cijelim corpusom
- konflikt resolution među više jednakih evidence paketa
- finalni web prikaz

---

## 5. Kako se ponaša na slabim dokazima

Ako retrieval paket nema dovoljno jak problem ili theory anchor:

- odgovor mora spustiti claim na `inferred_from_book_context`
- ili vratiti `insufficient_evidence = true`

Visual support nikad ne smije postati glavni dokaz bez tekstualnog anchor-a.

---

## 6. Status helper datoteke

- Datoteka: `retrieval_ready_knowledge_index_prototype_v1.js`
- Status: local-only helper/prototype artefakt; nije commitana u repo i zasad treba ostati izvan repoa dok se ne zaključa reusable tooling boundary

---

## 7. Konačni status

PASS WITH WARNING

Warning:

- Prototype je dokazno discipliniran, ali još ovisi o ručno kontroliranom evidence packu i heurističkom retrieval tagiranju. Prije lokalnog QA sandboxa treba zatvoriti automatski mode routing i retrieval execution boundary.
