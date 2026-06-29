# Drycured ops zapis — HR recipe SEO Batch 01

Datum: 2026-06-29
Područje: drycured.com / SEO / recepti
Komponenta: All in One SEO, tablica `wp_aioseo_posts`

## Izvršeno

Popunjeni su SEO title i meta description za prvi batch hrvatskih recepata:

- Slavonski kulen
- Kulenova seka
- Baranjski kulen
- Đakovački kulen
- Slavonska domaća kobasica
- Srijemska kobasica
- Baranjska kobasica
- Slavonska kobasica
- Krvavica slavonska
- Tlačenica slavonska
- Brodska kobasica
- Osječka kobasica
- Slavonska domaća salama
- Slavonska čvarkovača
- Vinkovačka kobasica
- Slavonska lovačka kobasica
- Istarska kobasica
- Rovinjska kobasica
- Pakračko-lipička češnjovka
- Samoborska češnjovka

## Važna napomena

Prvi SQL upis stvorio je duple AIOSEO redove jer `post_id` nije jedinstveni ključ u tablici `wp_aioseo_posts`.

Nakon toga je izvedeno čišćenje duplikata:
- ostavljen je popunjeni AIOSEO red
- uklonjeni su prazni NULL/NULL redovi
- javni HTML je ponovno provjeren

## Konačna provjera

Javni HTML za ključne recepte prikazuje:

- novi `<title>`
- novi meta description
- canonical
- OG title
- OG description
- schema.org WebPage

Provjereni uzorci:

- `/recepti-baza/hr-sl-001-slavonski-kulen-pdo-eu/`
- `/recepti-baza/hr-sl-005-slavonska-domaca-kobasica/`
- `/recepti-baza/hr-sl-010-slavonska-kobasica-zoi-eu-2023/`
- `/recepti-baza/hr-sl-002-kulenova-seka/`
- `/recepti-baza/hr-ce-001-samoborska-cesnjovka/`

## Pravilo za sljedeće batcheve

Za AIOSEO recepte više ne koristiti `INSERT ... ON DUPLICATE KEY UPDATE`.

Koristiti samo siguran postupak:
1. backup
2. provjera broja redova po `post_id`
3. update postojećeg reda ako postoji
4. insert samo ako red ne postoji
5. blokirati upis ako postoji više redova
