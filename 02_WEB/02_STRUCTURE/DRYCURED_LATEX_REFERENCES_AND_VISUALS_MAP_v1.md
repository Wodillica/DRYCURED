# DRYCURED_LATEX_REFERENCES_AND_VISUALS_MAP_v1

Status: references and visuals map v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Temeljni nalaz

Corpus koristi standardni LaTeX reference model s proširenjem za infografike.

Potvrđeno:
- `\label{...}` postoji široko kroz chaptere
- `\caption{...}` prati tablice, slike i infografike
- `\ref{...}` postoji, ali u trenutnom presjeku nije masovno korišten
- `\listoffigures`, `\listoftables` i `\listof{infografika}` postoje u `main.tex`

---

## 2. Kako su mapirane tablice

Tipični obrazac:
- `\begin{table}`
- `\caption{...}`
- `\label{tab:...}`
- okolni explanatory tekst
- ponekad `tablenotes`

Pravilo za retrieval:
- tablica mora ostati zajedno sa:
  - captionom
  - labelom
  - najbližim explanatory tekstom prije ili poslije tablice

Tablica nije samo grid podataka nego knowledge object s kontekstom.

---

## 3. Kako su mapirane slike

Tipični obrazac:
- `\begin{figure}`
- `\includegraphics{...}`
- `\caption{...}`
- `\label{fig:...}`

Pravilo za retrieval:
- slika ulazi kao visual object
- caption i asset path su obavezni metadata sloj
- retrieval je smije vraćati samo ako podupire odgovor, ne samo zato što postoji u istoj sekciji

---

## 4. Kako su mapirane infografike

Infografika se ne označava posebnim environmentom nego kroz:
- `\begin{figure}`
- `\captionsetup{type=infografika}`
- `\caption{...}`
- `\label{inf:...}`

Pravilo:
- parser mora razlikovati `infografika` od obične figure
- `infografika` je first-class knowledge object jer često nosi sažetak procesa, matricu ili dijagnostičku logiku

---

## 5. Kako su povezani label/ref/caption

Minimum koji retrieval mora čuvati zajedno:
- `label`
- `caption`
- object type
- source file
- source section path
- surrounding text koji referencira objekt

Bez toga vizual gubi značenje i postaje samo slika bez evidence vrijednosti.

---

## 6. Što retrieval sloj mora čuvati zajedno

Obavezne cjeline:
- naslov sekcije + pripadni objašnjavajući odlomak
- warning blok + okolni section context
- infografika + caption + label + referentni tekst
- tablica + caption + label + explanatory paragraph / tablenotes
- problem -> uzrok -> korekcija opis ako se nalazi uz tablicu ili vizual

---

## 7. Posebni warning za parser

Ne smije se raditi linearni regex koji samo skuplja sve `caption` i `label` retke bez konteksta, jer:
- caption i label mogu biti odvojeni po linijama
- `label` može označavati i sekciju, ne samo vizual
- `infografika` se otkriva kroz prethodni `\captionsetup{type=infografika}` signal

---

## 8. Zaključak

Tablice i infografike u DRYCURED knjizi moraju se tretirati kao dokazni objekti prve klase, ali samo uz očuvan caption/label/context paket.
