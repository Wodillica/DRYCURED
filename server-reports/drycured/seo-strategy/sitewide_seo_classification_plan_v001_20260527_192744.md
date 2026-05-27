# Drycured.com — site-wide SEO klasifikacija v0.0.1

## A — ručno optimizirati odmah

Ovo su glavne stranice koje nose identitet projekta i trebaju ručno pisane SEO title/meta description podatke:

- Home
- Proces izrade
- Recepti / Recepti baza
- Savjeti
- Infografike
- Alati
- Kalkulator sušenja
- Planer dimljenja
- Praćenje pH fermentacije
- Starter kulture
- Knjiga — Preview
- Drycured podcast
- Atlas stilova Europe
- Greške i rješenja

## B — ručno optimizirati nakon A skupine

Procesne stranice:

- Sirovina
- Rezanje
- Soljenje
- Mljevenje
- Miješanje
- Odležavanje smjese
- Punjenje
- Fermentacija
- Dimljenje
- Sušenje
- Zrenje
- Pakiranje

Stranice problema:

- Razmazana mast u presjeku
- Tvrda kora, mekana jezgra
- Kiseli miris jezgre
- Zračni džepovi u kobasici
- Kondenzacija na proizvodu
- Neželjena plijesan
- Gorčina od dima
- Užeglost masti

## C — template SEO

Ove skupine ne treba ručno obrađivati jednu po jednu u prvoj fazi. Treba im dati kvalitetne AIOSEO predloške:

### dry_recipe

Predložak title:
[Naziv recepta] — recept za suhomesnati proizvod | drycured.com

Predložak description:
Praktičan recept za [naziv recepta] s postupkom izrade, sastojcima i smjernicama za domaću pripremu suhomesnatih proizvoda.

### tip_pusnice

Predložak title:
[Naziv savjeta] — savjet iz pušnice | drycured.com

Predložak description:
Praktičan savjet za domaću izradu suhomesnatih proizvoda: uzrok problema, posljedice i konkretna rješenja u pušnici ili komori.

### infografika

Predložak title:
[Naziv infografike] — vizualni vodič | drycured.com

Predložak description:
Edukativna infografika o mesu, soljenju, dimljenju, sušenju, zrenju i sigurnoj izradi suhomesnatih proizvoda.

## D — provjeriti prije indeksiranja

Ovo treba dodatni audit kvalitete prije odluke:

- forum
- topic
- reply
- post_tag arhive
- prazne ili tanke taxonomy arhive
- stare test stranice
- Elementor predlošci
- tehnički custom post typeovi

## E — vjerojatno noindex / ne optimizirati za SEO

Ove stranice uglavnom nisu SEO landing stranice:

- Cart
- Checkout
- My account / Korisnički račun
- Registracija
- Postavke računa
- Premium pristup, ako je zatvorena/prodajna interna stranica
- test stranice
- tehničke stranice i shortcode testovi

## Operativni redoslijed

1. Zaključati A skupinu.
2. Zaključati B skupinu.
3. Uvesti predloške za C skupinu.
4. Napraviti noindex audit za D/E skupinu.
5. Tek nakon toga raditi fine korekcije pojedinačnih recepata i članaka.

## Važno

Ne raditi masovni upis bez:
- backupa AIOSEO tablice,
- live meta provjere prije/poslije,
- provjere sitemap statusa,
- Git arhiviranja izvještaja.
