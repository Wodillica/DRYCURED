# DRYCURED_LATEX_CUSTOM_ENVIRONMENTS_MAP_v1

Status: custom environment map v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Kanonski custom environmenti iz `style.tex`

Potvrđeni custom environmenti:
- `legendaBlok`
- `prakticniSavjet`
- `majstorskaNapomena`
- `zanimljivost`
- `upozorenjeBlok`
- `ekonomskaAnaliza`
- `napomenaBlok`
- `zakljucnaMisao`

Ovi blokovi nisu samo stil nego signal znanja.

---

## 2. Značenje i extraction tretman

### `legendaBlok`
Značenje:
- legenda / pojašnjenje simbola ili pojmova

Extraction tretman:
- knowledge object tip `definition` ili `supporting_context`
- zadržati uz objekt koji objašnjava

Stvarna uporaba:
- `chapters/chap03_meso_i_osnovni_pojmovi.tex`
- `chapters/chap04_sirovine_i_pomocni_sastojci.tex`

### `prakticniSavjet`
Značenje:
- izravni praktični naputak

Extraction tretman:
- knowledge object tip `correction` ili `practical_tip`
- visoki prioritet u problem mode retrievalu

Stvarna uporaba:
- `chapters/chap01_uvod.tex`
- `chapters/chap03_meso_i_osnovni_pojmovi.tex`
- `chapters/chap04_sirovine_i_pomocni_sastojci.tex`
- `chapters/chap09_tradicionalne_i_suvremene_tehnike.tex`

### `majstorskaNapomena`
Značenje:
- iskustveni, craft i heuristic sloj

Extraction tretman:
- knowledge object tip `expert_note`
- u theory modu kao supporting context
- u problem modu samo kao secondary evidence uz confirmed teorijski sloj

Stvarna uporaba je jaka, posebno u:
- `chapters/chap08_dimljenje_susenje_zrenje.tex`
- `chapters/chap18_kulturni_aspekti_i_turizam.tex`
- `chapters/chap21_male_tajne_velikih_majstora.tex`

### `zanimljivost`
Značenje:
- kuriozitet ili dodatni kontekst

Extraction tretman:
- low-priority knowledge object
- ne smije dominirati odgovorom osim ako korisnik traži širi kontekst

### `upozorenjeBlok`
Značenje:
- warning / safety caution

Extraction tretman:
- knowledge object prve klase tipa `warning`
- visok prioritet u problem mode retrievalu

Stvarna uporaba potvrđena u:
- `chapters/chap01_uvod.tex`
- `chapters/chap09_tradicionalne_i_suvremene_tehnike.tex`

### `ekonomskaAnaliza`
Značenje:
- ekonomski i operativni komentar

Extraction tretman:
- knowledge object tip `economic_analysis`
- sekundarni prioritet za AI v1

### `napomenaBlok`
Značenje:
- važna napomena ili kontekstualna ograda

Extraction tretman:
- knowledge object tip `note`
- može pojačati answer caveat sloj

### `zakljucnaMisao`
Značenje:
- završni interpretativni sažetak

Extraction tretman:
- summary object
- koristan za chapter-level synthesis, ali ne kao primarni dokaz za problem odgovor

---

## 3. Najvažniji zaključak za pipeline

Custom environmenti moraju se parsirati kao first-class semantic blocks.

Ne smiju biti:
- stopljeni s običnim paragrafom
- rezani po token windowsima
- tretirani samo kao vizualni stil

---

## 4. Zaključak

Najvrjedniji environmenti za AI v1 su:
- `upozorenjeBlok`
- `prakticniSavjet`
- `majstorskaNapomena`
- `napomenaBlok`

To su blokovi koji najviše pomažu u problem/theory response modelu i trebaju biti eksplicitno mapirani u extraction pipelineu.
