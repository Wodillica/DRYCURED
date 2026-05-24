# Scraping recepata

Ova mapa sadrži početni operativni paket za prikupljanje, klasifikaciju i obradu recepata suhomesnatih proizvoda.

## Svrha

Ovdje se pripremaju:
- seed liste domena i početnih URL-ova
- višejezični skup ključnih riječi
- pravila pristojnog i reproducibilnog web scrapinga
- strategija parsera i fallback logike
- pravila deduplikacije
- početna web-orijentirana JSON schema recepta
- zadaci za Codex vezani uz scraping i OneDrive scan

## Pravilo odvajanja

Ova dokumentacija pripada repozitoriju DRYCURED kao projektna i operativna baza.
Radni scrape podaci, lokalni scan manifesti i eksperimentalni outputi ostaju izvan ovog repozitorija, u zasebnom workspaceu na disku D.

## Predloženi redoslijed rada

1. potvrditi seed domene i jezike
2. zaključati scrape policy
3. zaključati parser strategy
4. pokrenuti mali testni crawl po domenama niskog rizika
5. tek nakon toga širiti opseg na veći broj izvora

## Napomena

Ova mapa nije spremište sirovih scrape rezultata, nego kontrolni sloj projekta.