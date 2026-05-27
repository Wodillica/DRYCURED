# Drycured.com — završni zapisnik rada

Datum: 2026-05-27 17:08:19
Server: swab-production
WordPress root: /var/www/html

## Sažetak

Procesni, alatni i SEO/FAQ pripremni sloj drycured.com danas su dovedeni u stabilno i dokumentirano stanje.

Nisu uključene nove javne SEO/FAQ funkcije.
Nije mijenjan javni prikaz home stranice.
Nije mijenjan Elementor sadržaj.
Nisu dirani alati, meni ni procesne stranice nakon zaključanih koraka.

## Zaključano stanje

### Procesne stranice

Aktivno i potvrđeno:

1. Sirovina
2. Rezanje
3. Soljenje
4. Mljevenje
5. Miješanje
6. Odležavanje smjese
7. Punjenje
8. Fermentacija
9. Dimljenje
10. Sušenje
11. Zrenje
12. Pakiranje

Sve faze su povezane kroz postojeći home process rail i potvrđene kroz Process Hub registar.

### Alati

Potvrđeni povezani alati:

- Kalkulator soli
- Praćenje pH
- Planer dimljenja
- Kalkulator sušenja

### Process Hub

Postoji centralni read-only registar procesa:

- wp-content/mu-plugins/drycured-process-hub.php

Potvrđeno:

- registar ima 12 procesa
- prev/next logika radi
- sve slike i URL-ovi su provjereni
- postoji admin pregled u WordPressu

Admin lokacija:

- Alati -> Drycured Process Hub

### Home Rail Adapter

Postoji isključeni adapter:

- wp-content/mu-plugins/drycured-home-process-rail-hub-adapter.php

Stanje:

- drycured_home_process_rail_use_hub=0
- adapter ne mijenja javni prikaz
- služi za usporedbu postojećeg home vodiča s Process Hub registrom

Admin lokacija:

- Alati -> Drycured Rail Adapter

### SEO/FAQ sloj

Postoji admin-only SEO/FAQ mapa:

- wp-content/mu-plugins/drycured-process-seo-faq.php

Admin lokacije:

- Alati -> Drycured SEO FAQ
- Alati -> Drycured SEO FAQ Schema

Stanje opcija:

- drycured_process_seo_faq_public_enabled=0
- drycured_process_seo_faq_schema_enabled=0
- drycured_process_seo_faq_visible_block_enabled=0
- drycured_process_seo_faq_test_slug=susenje

Potvrđeno:

- SEO/FAQ admin mapa radi
- schema preview radi
- FAQPage JSON-LD se ne prikazuje javno
- vidljivi FAQ blok nije uključen
- javni frontend nije promijenjen

## GitHub checkpointovi

Zadnji relevantni commitovi:

- 3e2856a — Add disabled drycured home rail hub adapter
- 37deb47 — Add drycured process SEO FAQ admin map
- 2aecdf9 — Refine drycured process SEO FAQ copy
- 106ff96 — Document drycured process SEO FAQ activation plan
- fa3a400 — Add drycured process SEO FAQ schema preview

## Važna napomena

Na serveru je prikazana poruka:

System restart required

Restart ne raditi usputno tijekom razvoja.
Planirati ga zasebno, nakon provjere backupa i u mirnom terminu.

## Preporučeni sljedeći korak

Ne uključivati javni FAQ/schema odmah.

Sljedeći sigurni korak može biti:

1. kontrolirani schema-only test na jednoj stranici: /proces-izrade/susenje/
2. ili dodatni audit SEO plugina koji trenutno upravlja title/meta podacima

Prije javne aktivacije obavezno:

- napraviti backup
- napraviti HTML snapshot prije i poslije
- uključiti samo jednu testnu stranicu
- provjeriti da nema duplog FAQPage schema markupa
- imati rollback jednom opcijom

## Zaključak

Sustav je trenutno u stabilnom pripremnom stanju.

Sve važne komponente su postavljene, auditirane i arhivirane, ali javna stranica nije nepotrebno promijenjena.
