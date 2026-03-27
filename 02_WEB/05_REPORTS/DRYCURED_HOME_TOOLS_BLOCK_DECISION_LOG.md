# DRYCURED_HOME_TOOLS_BLOCK_DECISION_LOG

Datum: 2026-03-27  
Projekt: drycured.com  
Metoda: OODA zapis za alatni blok na Home staging stranici

---

## Observe

Stvarno nađeno stanje:

- staging Home stranica ID `1458` postoji i latest posts blok već radi
- aktivni front page nije diran
- `WP Recipe Maker` plugin je aktivan
- nema nijednog `wprm_recipe` zapisa
- nije pronađen postojeći javni kalkulator recepata kao zasebna alatna stranica
- nije pronađen postojeći shortcode ili gotov frontend kalkulator koji bi se mogao samo povezati
- stranica `Recepti` postoji i radi, ali trenutno je sadržajni listing objava
- ostali alati (`Kalkulator soli`, `Procesni helper`) još ne postoje kao funkcionalni frontend moduli

---

## Orient

Moguće opcije bile su:

### Opcija A

- ostaviti stari vizualni placeholder i samo preimenovati CTA

Procjena:

- premalo korisno
- ne komunicira alatni sustav dovoljno jasno

### Opcija B

- izgraditi potpuno novi pravi recipe calculator u ovom koraku

Procjena:

- preširok zahvat za trenutni zadatak
- veći rizik i nepotrebno širenje scopea

### Opcija C

- napraviti jasan alatni blok s jednim stvarnim privremenim targetom i dva future entryja

Procjena:

- najbolji omjer korisnosti i rizika
- održava Home logiku čistom
- poštuje kanonski prioritet da `Kalkulator recepata` bude prvi alat

---

## Decide

Odabran je model:

- 1 glavni alatni ulaz: `Kalkulator recepata`
- 2 future entry kartice:
  - `Kalkulator soli`
  - `Procesni helper`

Ključna odluka:

- budući da stvarni calculator frontend nije pronađen u lokalnoj kopiji, `Kalkulator recepata` je povezan na postojeći `Recepti` URL kao kontrolirani privremeni target

Zašto je taj model odabran:

- stvarna korisnost: Home sada jasno pokazuje da postoji alatni sloj
- najmanji rizik: nema novih pluginova ni velikog refactora
- čisto održavanje: mali lokalni shortcode i uredan Elementor shortcode widget
- vizualna usklađenost: kartice koriste isti ozbiljan, miran ritam kao latest posts blok i ostatak staging skeleta

---

## Act

Točno napravljeno:

1. napravljen je lokalni shortcode za tools grid  
2. shortcode je učitan kroz lokalni Astra `functions.php`  
3. sekcija `Alati` na staging stranici `1458` zamijenjena je novim headingom, uvodom i shortcode widgetom  
4. `Kalkulator recepata` kartica spojena je na `http://localhost:8085/recepti/` kao siguran privremeni target  
5. `Kalkulator soli` i `Procesni helper` ostavljeni su kao kontrolirani `Uskoro` moduli  
6. odrađen je cache reset i dodatni Elementor refresh dok frontend nije povukao novi render  
7. potvrđeno je da se novi alatni blok prikazuje na staging frontend stranici bez placeholder sadržaja

Napomena:

- pokušaj kreiranja zasebne lokalne page mete za `Kalkulator recepata` nije uspio zbog DB insert greške u ovoj importanoj kopiji
- zbog toga je zadržan manji rizik i usvojen postojeći `Recepti` URL kao privremeni target

---

## Preporuka za sljedeći korak

`problem blok`

Razlog:

- problem-based ulaz je sljedeći najlogičniji funkcionalni sloj nakon latest posts i alata
- dobro zatvara Home logiku: znanje + alati + problemi
- language switcher je važan, ali ne nosi istu operativnu vrijednost kao problem blok
