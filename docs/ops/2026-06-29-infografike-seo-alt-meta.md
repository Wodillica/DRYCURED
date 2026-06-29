# Drycured ops zapis — SEO popravak infografika

Datum: 2026-06-29
Područje: drycured.com / infografike
Komponenta: MU plugin `drycured-infografike.php`

## Razlog izmjene

SEO audit je pokazao da dio slika nema alt tekst. Među prvim slikama bez alt teksta bile su infografike iz kataloga infografika.

## Izvršeno

Dodan je alt tekst za 12 infografika u WordPress medijskoj biblioteci.

Provjeren je SEO head za:
- `/infografike/`
- `/infografike/tehnoloska-namjena-dijelova-mesa/`

Utvrđeno je da stranice već imaju vlastiti `<title>` iz aktivnog SEO/theming sustava. Privremeno dodani ručni `<title>` u MU pluginu uklonjen je kako ne bi postojala dva title taga.

## Konačna provjera

Arhiva infografika:
- title count: 1
- meta description: aktivan
- canonical: aktivan
- OG title/description: aktivni

Pojedinačna infografika:
- title count: 1
- meta description: aktivan
- canonical: aktivan
- OG title/description/image: aktivni

## Napomena

Ova izmjena ne mijenja sadržaj infografika ni njihov vizualni prikaz. Radi se o SEO i pristupačnosti poboljšanju.
