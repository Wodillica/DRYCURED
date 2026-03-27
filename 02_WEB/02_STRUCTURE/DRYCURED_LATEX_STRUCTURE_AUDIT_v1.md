# DRYCURED_LATEX_STRUCTURE_AUDIT_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Corpus root: `D:\drycured.com_prijevod\01_IZVOR`  
Jezik rada: hrvatski

---

## 1. Sažetak nalaza

LaTeX corpus je dovoljno strukturiran da postane ozbiljan knowledge source za DRYCURED AI, ali nije “čist” u smislu jedne savršeno uređene datotečne hijerarhije.

Najvažnije je:
- stvarni entrypoint je `main.tex`
- stvarna logika knjige ide kroz `\input` uključivanja
- znanje je već organizirano po chapter/section/subsection razinama
- tablice, figure i infografike imaju caption/label sloj
- `style.tex` definira custom semantička okruženja korisna za extraction
- u filesystemu postoje i duplicirane/nested `.tex` varijante koje nisu primarni izvor i koje parser ne smije slijepo ingestati

---

## 2. Kako je corpus organiziran

Glavni entrypoint:
- `D:\drycured.com_prijevod\01_IZVOR\main.tex`

Glavni moduli:
- `frontmatter`
- `chapters`
- `backmatter`
- `config/style.tex`

Stvarno referencirane datoteke iz `main.tex`:
- 25 chapter datoteka
- više frontmatter i backmatter datoteka
- `config/style.tex`

Potvrđeno iz `main.tex`:
- `\listoffigures`
- `\listoftables`
- `\listof{infografika}{Popis infografika}`

---

## 3. Strukturirani signal koji parser može koristiti

Corpus već daje:
- `\chapter`
- `\section`
- `\subsection`
- `\subsubsection`
- `\section*` + `\addcontentsline`
- `\label`
- `\ref`
- `table`
- `figure`
- `\captionsetup{type=infografika}`
- custom semantic environments iz `style.tex`

To je dovoljno da se knowledge extraction radi iz source semantike, a ne iz plain text dumpa.

---

## 4. Ključni parsing rizici

### 4.1. Nested duplicate datoteke
U corpusu postoje duplicirane i duboko ugniježđene `.tex` datoteke, uključujući stare ili parcijalne verzije chaptera.

Rizik:
- rekurzivni parser može dvostruko ingestati sadržaj
- inventory preko samog filesystem walka daje prenapuhane brojke

Zaključak:
- kanonski source graph mora se graditi isključivo iz `main.tex` include relacija

### 4.2. Nazivna nekonzistentnost datoteka
Postoje chapter datoteke s:
- razmacima
- dijakritikom
- zagradama
- alternativnim starijim nazivima

Rizik:
- parser i asset resolver mogu puknuti na file-name canonicalizationu

### 4.3. Caption/label razdvojenost po linijama
Kod figura, tablica i infografika `caption` i `label` nisu uvijek na istoj liniji.

Rizik:
- sirovi regex extractor može izgubiti vezu caption <-> label

### 4.4. Infografika nije zasebno environment okruženje
Infografika se označava kroz:
- `figure`
- `\captionsetup{type=infografika}`

Rizik:
- ako parser gleda samo `figure`, izgubit će razliku između slike i infografike

### 4.5. Custom okruženja nisu samo stil, nego semantika
`upozorenjeBlok`, `prakticniSavjet`, `majstorskaNapomena` i slični blokovi nose stvarnu meaning logiku.

Rizik:
- ako se flattenaju u paragraf, gubi se znanje tipa warning / savjet / majstorska praksa

---

## 5. Preporučeni extraction pristup

Preporuka v1:
- parsing mora krenuti od `main.tex`
- parser mora razmotati samo stvarno referencirane `\input` datoteke
- `style.tex` se mora parsirati prije chapter extractiona
- object extraction mora biti section-aware i visual-aware
- tablica + caption + label + okolni explanatory tekst moraju ostati povezani
- infografika + caption + label + referentni tekst moraju ostati povezani
- warning/savjet blokovi moraju postati knowledge objekti prve klase

---

## 6. Minimalna ispravna jedinica chunkanja

Minimalna ispravna jedinica chunkanja u v1 nije token window nego semantički blok.

To znači:
- naslov + pripadni odlomci ostaju zajedno
- warning blok ostaje uz najbliži surrounding section context
- tablica ostaje s captionom, labelom i najbližim objašnjenjem
- infografika ostaje s captionom, labelom i referentnim tekstom
- problem -> uzrok -> korekcija lanac ne smije biti razrezan na nepovezane chunkove

---

## 7. Operativni zaključak

Corpus je spreman za knowledge extraction prototip, ali samo uz disciplinu:
- entrypoint-driven parsing
- object-aware extraction
- strict handling of duplicate file noise

Konačni status:
- `PASS WITH WARNING`

Warning:
- filesystem corpus sadrži duplicate/nested i nazivno nekonzistentne datoteke koje moraju biti isključene iz kanonskog extraction flowa osim ako su stvarno referencirane iz `main.tex`.
