# DRYCURED_HOME_LATEST_POSTS_DECISION_LOG

Datum: 2026-03-27  
Projekt: drycured.com  
Metoda: OODA zapis za latest posts blok na Home staging stranici

---

## Observe

Zatečeno stanje:

- staging Home stranica već postoji kao Elementor stranica
- ciljna stranica je ID `1458`
- aktivni front page nije smio biti diran
- Elementor i Elementor Pro su aktivni u lokalnom stacku
- objave postoje
- ukupno je potvrđeno 8 objava
- najnovije objave postoje i imaju:
  - naslov
  - datum
  - excerpt
- najnovije objave trenutno nemaju featured image
- postojeći blok "Novo na sajtu" bio je samo placeholder unutar staging skeleta

---

## Orient

Dostupne opcije bez novih pluginova:

### Opcija A

- Elementor native dynamic posts / loop rješenje

Procjena:

- tehnički moguće jer je Elementor Pro aktivan
- ali je veći rizik ručno i programski slagati Pro loop strukturu u postojeći Elementor JSON bez ulaska u editor i bez dodatnih eksperimenata

### Opcija B

- postojeći WordPress shortcode ili query blok

Procjena:

- nije pronađeno uredno postojeće rješenje koje već sada daje traženi dizajnerski rezultat unutar Elementora

### Opcija C

- lagani lokalni shortcode fallback bez novih pluginova

Procjena:

- najmanji rizik
- najčišća kontrola nad markupom
- najbolje sjeda u postojeći staging skelet

---

## Decide

Odabrana je opcija C:

- lokalni shortcode `[drycured_latest_posts count="3"]`
- render preko Elementor shortcode widgeta unutar bloka `Novo na sajtu`

Zašto je to najbolja opcija u ovom koraku:

- stabilnost: jednostavan WP query i kontroliran output
- održavanje: mali, čitljiv lokalni dodatak bez plugin ovisnosti
- vizualna usklađenost: markup i stil su podešeni da izgledaju kao dio postojećeg Home skeleta
- najmanji rizik: nije trebalo uvoditi nove pluginove ni agresivno rekonstruirati Elementor Pro loop strukturu

Dodatna svjesna odluka:

- featured images su izostavljene jer najnovije objave nemaju thumbnail i bez njih blok izgleda čišće i ozbiljnije

---

## Act

Točno napravljeno:

1. dodan je lokalni shortcode file u Astra temu  
2. shortcode je učitan kroz `functions.php` lokalne teme  
3. napravljen je helper script koji mijenja samo sekciju `Novo na sajtu` na staging stranici ID `1458`  
4. placeholder sadržaj zamijenjen je headingom, kratkim uvodom i shortcode widgetom  
5. flushani su transijenti i cache sloj  
6. osvježen je `post_modified` staging stranice kako bi frontend render definitivno povukao novi sadržaj  
7. potvrđeno je da frontend prikazuje 3 dinamičke objave i da placeholder više ne postoji

---

## Sljedeći preporučeni korak

`alatni blok`

Razlog:

- logički je sljedeći po prioritetu iz kanonskih planova
- lakše ga je povezati nakon što je latest posts sloj već postao stvarno dinamičan
- nakon alatnog bloka može slijediti problem blok, a zatim language switcher sloj
