# Drycured ops zapis — HR recipe SEO Batch 02

Datum: 2026-06-29
Područje: drycured.com / SEO / recepti
Komponenta: All in One SEO, tablica `wp_aioseo_posts`

## Izvršeno

Popunjeni su SEO title i meta description za drugi batch hrvatskih recepata:

- Pazinska kobasica
- Lička kobasica
- Vrgorački kulen
- Žlomprt / istarski ombolo
- Meso z tiblice
- Lička kastradina
- Istarska kosnica
- Iločka domaća kobasica
- Daruvarski domaći špek
- Primorska domaća kobasica

## Način rada

Batch 02 je izveden sigurnim PHP/WP-CLI postupkom, bez `INSERT ... ON DUPLICATE KEY UPDATE`.

Za svaki recept:
- provjeren je postojeći AIOSEO red
- ako je postojao jedan red, ažuriran je taj red
- ako red nije postojao, otvoren je novi red
- ako bi postojalo više redova, upis bi bio blokiran

## Konačna provjera baze

Provjera baze pokazala je:

- 10/10 recepata ima SEO title
- 10/10 recepata ima meta description
- 10/10 recepata ima `robots_noindex = 0`
- 10/10 recepata ima točno jedan AIOSEO red

## Napomena za nastavak

Prvi korak u sljedećem nastavku rada:

- napraviti javni HTML check za Batch 02
- zatim spremiti/zaključiti Batch 02 kao potpuno provjeren
- nakon toga izvaditi Batch 03 hrvatskih recepata

