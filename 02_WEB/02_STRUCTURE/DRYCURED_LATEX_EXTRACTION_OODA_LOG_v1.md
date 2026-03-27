# DRYCURED_LATEX_EXTRACTION_OODA_LOG_v1

Status: prototype OODA log v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## Observe

- Kanonski entrypoint je `D:\drycured.com_prijevod\01_IZVOR\main.tex`.
- Filesystem sadrži stare ili duplicirane `.tex` datoteke koje nisu sve dio build grafa.
- `style.tex` definira custom okruženja i custom caption type za infografike.
- Reprezentativni sample chapters nose procese, dimljenje/sušenje, mikroklimu i dijagnostiku grešaka.

## Orient

- Najmanji siguran extraction model nije paragraph-only nego heading-centered chunking.
- Warning, tablica i infografika moraju ostati first-class objects.
- `label/ref` metadata je potreban most između surrounding texta i visual objekata.

## Decide

- Traversal ide isključivo po aktivnom input graphu.
- Sample scope ostaje ograničen na 4 reprezentativna poglavlja.
- Output schema ostaje dovoljno mala za prototype, ali s dovoljno metadata za retrieval-ready fazu.

## Act

- Generiran je input graph manifest iz `main.tex`.
- Generiran je sample JSON s `164` knowledge objekata.
- Zaključana su prototype extraction rules i edge case dokumentacija.

## Preporučeni sljedeći korak

- retrieval-ready knowledge index prototype v1
