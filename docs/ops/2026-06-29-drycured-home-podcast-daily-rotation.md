# Drycured ops zapis — dnevna rotacija podcast kartice na početnoj

Datum: 2026-06-29  
Područje: drycured.com / početna stranica  
Komponenta: Najnoviji sadržaj / podcast kartica  
Datoteka: `wp-content/plugins/drycured-home-core/includes/home-latest-unified.php`

## Razlog izmjene

Na početnoj stranici u bloku “Najnoviji sadržaj” podcast kartica je dulje vrijeme prikazivala istu epizodu:

- EP02: Soljenje, crijeva i češnjak

Utvrđeno je da je epizoda bila tvrdo upisana u PHP datoteci, pa se nije automatski mijenjala.

## Izvršena izmjena

Uvedena je dnevna rotacija podcast kartice.

Rotacija koristi dostupne epizode:

- EP01: Osnove dobrog suhomesnatog proizvoda
- EP02: Soljenje, crijeva i češnjak
- EP03: Fermentacija, dim i sušenje
- EP04: Najčešće greške i kako ih izbjeći
- EP05: Kontrola, sigurnost i dnevnik šarže

Svaki dan prikazuje se jedna epizoda, zatim ciklus kreće ispočetka.

## Sigurnosni postupak

Prije izmjene napravljena je sigurnosna kopija aktivne datoteke na serveru u:

- `/root/drycured_backup_home_latest_*`

## Provjera

Nakon izmjene treba provjeriti početnu stranicu:

- `https://drycured.com/?v=podcast-daily-rotation`

Ako se sljedeći dan epizoda ne promijeni, potrebno je provjeriti Cloudflare cache i sadržaj datoteke `home-latest-unified.php`.

## Napomena

U repozitorij se ne spremaju privatni tokeni, API ključevi, IP logovi, Cloudflare podaci, backup arhive ni drugi osjetljivi podaci.

## Sigurnosna napomena

U repozitorij se ne spremaju privatni tokeni, API ključevi, IP logovi, Cloudflare podaci, backup arhive ni drugi osjetljivi podaci.
