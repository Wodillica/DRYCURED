# SCRAPE POLICY v1

## Svrha

Ovaj dokument definira osnovna pravila za tehnički i pravno razuman scraping recepata i povezanih tehnoloških zapisa.

## Temeljna načela

1. scraping se vodi disciplinirano i dokumentirano
2. ne radi se agresivni crawl bez testnog batcha
3. sirovi podaci se čuvaju odvojeno od obrađenih podataka
4. svaki izvor mora imati zapis o domeni, datumu dohvaćanja i korištenom parseru
5. javni web izvori nisu automatski jednako vrijedni

## Operativna pravila

### 1. Mali početni batch
- prvo koristiti mali skup domena
- ne širiti opseg dok se ne provjere rezultati

### 2. Rate limiting
- koristiti razuman broj zahtjeva po domeni
- izbjegavati nagle burstove prometa
- koristiti kašnjenje i retry logiku

### 3. Robots i uvjeti korištenja
- prije širenja scrapinga provjeriti robots.txt i opće uvjete korištenja gdje je to potrebno
- ako izvor jasno zabranjuje scraping ili automatizirano preuzimanje, taj izvor ide u poseban review status

### 4. Evidencija dohvaćanja
Za svaki dohvat zabilježiti:
- izvorni URL
- domenu
- datum i vrijeme dohvaćanja
- status odgovora
- parser koji je korišten
- lokaciju sirovog zapisa

### 5. Hijerarhija ekstrakcije
Redoslijed obrade:
1. schema.org / JSON-LD
2. recipe-scrapers
3. trafilatura
4. vlastiti parser i ručna provjera

### 6. Ograničenja sadržaja
- ne kopirati bespotrebno velike količine sadržaja u repozitorij
- repozitorij služi za pravila, sheme i kontrolne dokumente
- sirovi scrape output ostaje izvan repozitorija

### 7. Procjena izvora
Izvore treba ocjenjivati prema tipu:
- stručni / tradicijski / referentni
- receptni portal
- proizvođač
- forum
- komentar / agregator / slabi izvor

### 8. Deduplikacija
- isti URL i isti sadržaj tretirati kao egzaktan duplikat
- vrlo sličan naslov i vrlo slični sastojci tretirati kao fuzzy kandidat

### 9. Sigurnost i preglednost
- bez automatskog masovnog raspakiravanja arhiva
- bez nekontroliranog OCR-a
- bez miješanja raw i normalized sloja

## Statusi izvora
- allowed_test
- allowed_review
- blocked_review
- low_value
- high_value

## Napomena

Ovo je operativna politika prve faze. Nakon testnog crawla dokument se nadograđuje u v2.