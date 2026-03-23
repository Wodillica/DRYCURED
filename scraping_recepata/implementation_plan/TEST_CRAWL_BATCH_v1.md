# TEST CRAWL BATCH v1

## Cilj

Pokrenuti mali, kontrolirani testni batch kako bi se provjerilo:
- može li se sadržaj stabilno dohvatiti
- daje li parser upotrebljive rezultate
- postoje li tehnička ili pravna ograničenja
- koje domene vrijedi zadržati za sljedeći krug

## Opseg prve probe

Prvi batch mora biti mali i pregledan.
Preporuka je testirati 8 do 12 domena iz seed liste.

## Predložene početne domene

### Visoki prioritet
- coolinarika.com
- chefkoch.de
- slowfood.com

### Srednji prioritet
- finirecepti.net
- agroklub.com
- stvarukusa.mondo.rs
- okusno.je
- moirecepti.mk
- giallozafferano.it
- directoalpaladar.com
- mindmegette.hu
- kwestiasmaku.com

## Tipovi izvora u batchu
Batch treba sadržavati mješavinu:
- velikih receptnih portala
- tradicijskih / referentnih izvora
- izvora s mogućim tehnološkim člancima

## Što mjeriti

Za svaku domenu i svaki testni URL bilježiti:
- HTTP status
- je li dohvat uspio
- je li otkriven schema.org Recipe ili drugi strukturirani zapis
- je li recipe-scrapers dao rezultat
- je li Trafilatura bila potrebna
- procjenu kvalitete rezultata
- količinu šuma
- procjenu pravne i tehničke prikladnosti

## Preporučeni ishod po domeni
- keep_for_batch_2
- keep_with_review
- low_value
- blocked_review

## Što ne raditi
- ne širiti batch prije evaluacije
- ne scrapati tisuće URL-ova u prvom krugu
- ne uključivati društvene mreže kao primarni izvor

## Minimalni izlaz nakon batcha
- TEST_CRAWL_RESULTS_v1.csv
- TEST_CRAWL_REVIEW_v1.md
- popis domena za drugi krug
- popis domena za review ili blokadu
