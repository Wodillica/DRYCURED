# DRYCURED_LATEX_CORPUS_INVENTORY_v1

Status: inventory v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Kanonski entrypoint

- `D:\drycured.com_prijevod\01_IZVOR\main.tex`

Uloga:
- glavni source manifest knjige
- određuje koje su datoteke stvarno dio builda
- određuje popise slika, tablica i infografika

---

## 2. Pomoćna ključna datoteka

- `D:\drycured.com_prijevod\01_IZVOR\config\style.tex`

Uloga:
- definira tipografiju i layout
- definira custom semantic environments
- definira custom `infografika` caption type
- daje ključnu semantičku informaciju za extraction pipeline

---

## 3. Glavni moduli corpusa

### Frontmatter
Datoteke:
- `frontmatter/cover_front.tex`
- `frontmatter/titlepage.tex`
- `frontmatter/predgovor.tex`
- `frontmatter/rijec_citatelju.tex`

Uloga:
- uvodni i prezentacijski sloj
- niži prioritet za AI knowledge v1

### Chapters
Stvarno referencirani chapter sloj iz `main.tex`:
- `chapters/chap01_uvod.tex`
- `chapters/chap02_povijest.tex`
- `chapters/chap03_meso_i_osnovni_pojmovi.tex`
- `chapters/chap04_sirovine_i_pomocni_sastojci.tex`
- `chapters/chap05_pasmine_i_meso_masnotost.tex`
- `chapters/chap06_tehnoloski_procesi.tex`
- `chapters/chap07_fermentacija_i_mikrobiologija.tex`
- `chapters/chap08_dimljenje_susenje_zrenje.tex`
- `chapters/chap09_tradicionalne_i_suvremene_tehnike.tex`
- `chapters/chap10_klimatski_i_mikrolokalni_uvjeti.tex`
- `chapters/chap11_zdravstveni aspek_suhomesnatih_proizvoda.tex`
- `chapters/chap12_kultura_tradicija_i_identitet_suhomesnatih_proizvoda.tex`
- `chapters/chap13_oprema_alati_i_suvremene_diy_varijante.tex`
- `chapters/chap14_kontrola_kvalitete_i_sigurnost.tex`
- `chapters/chap15_cuvanje_pakiraje_i_logistika.tex`
- `chapters/chap16_prezentacija_distribucija_trziste.tex`
- `chapters/chap17_standardizirane_recepture_i_sheme.tex`
- `chapters/chap18_kulturni_aspekti_i_turizam.tex`
- `chapters/chap19_Standardi_i_oznake_izvornosti_(PDO, PGI, TSG).tex`
- `chapters/chap20_regionalni_stilovi.tex`
- `chapters/chap21_male_tajne_velikih_majstora.tex`
- `chapters/chap22_sigurnost_hrane_i_higijena.tex`
- `chapters/chap23_ekonomski_i_trzisni_aspekti.tex`
- `chapters/chap24_digitalno_doba_tradicija.tex`
- `chapters/chap25_zaključak_budućnost_tradicije_i_čovijek_u_središtu_procesa.tex`

### Backmatter
Datoteke:
- `backmatter/rjecnik_pojmova.tex`
- `backmatter/bibliografija.tex`
- `backmatter/o_autoru.tex`
- `backmatter/kolofon.tex`
- `backmatter/zavrsna_stranica.tex`
- `backmatter/cover_back.tex`

Uloga:
- reference i završni sloj
- korisno za glossary i bibliografiju, ali sekundarni prioritet za AI v1

---

## 4. Corpus statistika za stvarno referencirane build datoteke

Sažetak nad kanonski referenciranim `.tex` datotekama:
- 25 chapter datoteka
- 38 ukupnih `\input` referenci u `main.tex` ako se uključe frontmatter/backmatter i helper artefakti iz macro definicija
- stvarno postojeće build datoteke treba računati kroz eksplicitne chapter/front/back reference, ne kroz raw regex hvatanje macro placeholdera

Jaki chapterovi po strukturi:
- `chap06_tehnoloski_procesi.tex`
- `chap08_dimljenje_susenje_zrenje.tex`
- `chap11_zdravstveni aspek_suhomesnatih_proizvoda.tex`
- `chap17_standardizirane_recepture_i_sheme.tex`
- `chap21_male_tajne_velikih_majstora.tex`

Najbogatiji vizualni chapterovi među stvarno referenciranima:
- `chap06_tehnoloski_procesi.tex`
- `chap13_oprema_alati_i_suvremene_diy_varijante.tex`
- `chap19_Standardi_i_oznake_izvornosti_(PDO, PGI, TSG).tex`
- `chap20_regionalni_stilovi.tex`

---

## 5. Posebni slučajevi koje inventory mora označiti

- u filesystemu postoje duplicate/nested stare datoteke u `chapters/chapters/...`
- postoje alternativne ili stare chapter varijante koje nisu nužno kanonske
- postoje file-name varijante s razmacima i zagradama
- inventory za parser mora razlikovati:
  - kanonski referencirane datoteke
  - prisutne ali ne-referencirane datoteke

---

## 6. Zaključak

Kanonski corpus inventory mora uvijek polaziti od `main.tex`.

Sve ostalo u stablu tretira se kao sekundarni ili potencijalno arhivski materijal dok se izričito ne potvrdi da je dio aktivnog builda.
