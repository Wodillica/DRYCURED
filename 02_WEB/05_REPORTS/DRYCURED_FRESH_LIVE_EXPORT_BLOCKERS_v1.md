# DRYCURED_FRESH_LIVE_EXPORT_BLOCKERS_v1

Status: blockers mapa v1  
Projekt: drycured.com  
Datum: 2026-03-31  
Jezik rada: hrvatski

---

## 1. Export blockers

Za sam fresh export trenutno nema aktivnog blockera.

Svježi SQL dump i svježi `wp-content` payload postoje lokalno.

---

## 2. Preostali operativni warningi

### Warning 1

- Tema: lokalni restore još nije ponovljen
- Utjecaj: live-content sloj još nije vraćen u lokalnu bazu i runtime
- Status: otvoreno
- Sljedeći korak: retry content importa u lokalni WordPress runtime

### Warning 2

- Tema: export je validiran kao datotečni set, ali ne još kao potpuno restaurirana instanca
- Utjecaj: Home/page `101` je potvrđen na live originu, ali još nije potvrđen u lokalnoj bazi nakon novog importa
- Status: otvoreno
- Sljedeći korak: import SQL dumpa + sync `wp-content` + URL replace + runtime validacija

---

## 3. Najmanji sljedeći korak

Najmanji smisleni sljedeći korak je:

- retry live-content importa u lokalni runtime koristeći upravo izvezene artefakte

