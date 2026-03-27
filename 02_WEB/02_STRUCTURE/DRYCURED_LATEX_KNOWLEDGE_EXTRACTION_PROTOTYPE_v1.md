# DRYCURED_LATEX_KNOWLEDGE_EXTRACTION_PROTOTYPE_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Što je prototip stvarno izvukao

Prototip je izgradio aktivni input graph isključivo od `D:\drycured.com_prijevod\01_IZVOR\main.tex` i njegovih stvarno referenciranih `\input` relacija. Na toj osnovi je iz ograničenog v1 sample scopea izvezao strukturirane knowledge objekte u JSON.

Aktivni graph obuhvaća `37` stvarno uključenih datoteka. Sample extraction je rađen samo nad reprezentativnim poglavljima:

- `chapters/chap08_dimljenje_susenje_zrenje.tex`, `chapters/chap09_tradicionalne_i_suvremene_tehnike.tex`, `chapters/chap10_klimatski_i_mikrolokalni_uvjeti.tex`, `chapters/chap14_kontrola_kvalitete_i_sigurnost.tex`

---

## 2. Što je izvezeno u sample JSON

Izvezeni su object tipovi kad su bili stvarno prisutni i prepoznatljivi u sampleu:

- cause: 7
- correction: 7
- figure_reference: 15
- infographic: 22
- problem: 35
- process_phase: 15
- table: 5
- theory_block: 57
- warning: 1

Minimalna jedinica extractiona je:

- heading razina + pripadni prozni blok
- custom warning/environment blok kao zaseban object
- tablica kao zaseban object s caption/label metadata slojem
- infografika kao zaseban object preko `captionsetup{type=infografika}`
- figure reference kao zaseban visual object kad je prisutna figura s relevantnim captionom

---

## 3. Što radi dobro

- Parser ne radi raw recursive crawl po filesystemu.
- Manifest prati samo kanonski build graph iz `main.tex`.
- Chapter i section kontekst ostaju sačuvani na object razini.
- `label/ref` veze se čuvaju i koriste za spajanje section teksta s tablicama i vizualima.
- Custom okruženja iz `style.tex`, posebno `upozorenjeBlok`, ostaju zasebni knowledge objekti.
- Infografike se razlikuju od običnih figura preko custom caption tipa.

---

## 4. Što još ne radi dovoljno dobro

- Semantička klasifikacija `problem / cause / correction` još je heuristička, ne kanonski parsirana.
- Nisu svi visual blokovi povezani s najbližim objašnjenjem ako u tekstu nema eksplicitnog `\ref`.
- Prototip još ne normalizira složene makroe i sve tablične podtipove do retrieval-ready kvalitete.
- Nema punog parsera za definicije iz svih mogućih stilskih obrazaca.

---

## 5. Spremnost za sljedeću fazu

Parser je dovoljno spreman za sljedeću fazu:

- retrieval-ready knowledge index prototype v1

To vrijedi pod uvjetom da sljedeći korak zadrži ista ograničenja:

- traversal samo preko `main.tex`
- chunking bez odvajanja warning/tablica/infografika od okolnog konteksta
- bez masovnog importa cijele knjige dok se ne zaključa retrieval index schema

---

## 6. Konačni status

PASS WITH WARNING

Warning:

- Prototip pouzdano čuva strukturu i vizualne veze za sample corpus, ali još nije dovoljno robustan za puni corpus ingest bez dodatnog extraction pass-a za makroe, reference edge caseove i finiju semantičku klasifikaciju.
