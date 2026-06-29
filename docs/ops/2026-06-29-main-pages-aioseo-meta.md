# Drycured ops zapis — AIOSEO meta podaci za glavne stranice

Datum: 2026-06-29
Područje: drycured.com / SEO
Komponenta: All in One SEO, tablica `wp_aioseo_posts`

## Razlog izmjene

SEO audit je pokazao da dio važnih stranica nema ručno postavljen SEO naslov i meta opis.

## Izvršeno

Popunjeni su AIOSEO title i description za:

- `/vodici/`
- `/sezonski-kalendar/`

Stranice koje su već imale SEO podatke nisu mijenjane:

- početna stranica
- `/recepti/`
- `/infografike/`
- `/podcast/`
- `/greske/`

## Konačna provjera

Za `/vodici/` javni HTML prikazuje:

- `<title>`
- meta description
- canonical
- OG title
- OG description
- schema.org CollectionPage

Za `/sezonski-kalendar/` javni HTML prikazuje:

- `<title>`
- meta description
- canonical
- OG title
- OG description
- schema.org WebPage

Za sve glavne stranice `robots_noindex = 0`.

## Backup

Prije izmjene napravljen je backup AIOSEO redova za postove 103 i 3540.
