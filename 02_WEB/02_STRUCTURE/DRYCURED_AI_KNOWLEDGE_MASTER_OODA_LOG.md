# DRYCURED_AI_KNOWLEDGE_MASTER_OODA_LOG

Status: master OODA log v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## Observe

Stvarni corpus nije PDF nego LaTeX book projekt.

Potvrđeno je:
- `main.tex` orkestrira cijelu knjigu
- postoji chapter split i backmatter/frontmatter sloj
- postoje figure, tables i custom `infografika` tip
- postoje `caption`, `label`, `ref` i zasebni popisi vizuala
- `style.tex` definira custom semantička okruženja
- sadržaj već nosi dijagnostičke uzorke korisne za AI problem mode

## Orient

Iz toga slijedi da DRYCURED treba source-aware knowledge pipeline, a ne generički PDF RAG.

Najveća vrijednost knjige je upravo u tome što je strukturirana:
- hijerarhijski
- semantički
- vizualno
- referencijski

## Decide

Zaključen je jedinstveni model:
- LaTeX source layer
- extraction/parsing layer
- knowledge object layer
- retrieval/index layer
- answer generation layer
- web integration layer

I dva radna moda:
- Problem mode
- Theory mode

## Act

Upisani su master plan dokumenti za:
- ukupnu arhitekturu
- LaTeX pipeline
- knowledge object schema
- retrieval/ranking
- QA response model
- anti-hallucination rules
- v1 scope i implementation order

## Zaključak

Ovaj zadatak sada postaje kanonski master plan za DRYCURED AI knowledge layer. Raniji odvojeni planovi mogu služiti kao kontekst, ali ne više kao glavni izvor odluke.
