# DRYCURED_RETRIEVAL_INDEX_OODA_LOG_v1

Status: retrieval index OODA log v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## Observe

- Extraction sample već nosi source_file, chapter, section, object_type, source_text, labels, refs i visual/table veze.
- Nedostajala su retrieval polja: searchable_text, concise_summary, tagovi, query mode signali i retrieval priority.
- Source discipline je već zaključana kroz main.tex input graph.

## Orient

- Future Problem mode i Theory mode trebaju različite retrieval signale.
- Warning i process-sensitive blokovi moraju dobiti jači rang od perifernog teorijskog teksta kad query nosi operativni signal.
- Visual i table objekti moraju ostati povezani, ali ne smiju sami preuzeti primat bez tekstualnog anchor-a.

## Decide

- V1 index ostaje lagani transform extraction outputa.
- Tagging, ranking i query modes ostaju heuristički, ali eksplicitni i auditabilni.
- Semantic retrieval ostaje planiran signal, ne implementirana jezgra.

## Act

- Napravljen je schema JSON za retrieval-ready model.
- Napravljen je sample retrieval index iz extraction samplea.
- Dokumentirani su query model i ranking rules za budući answer composition sloj.

## Preporučeni sljedeći korak

- AI answer composition prototype v1
