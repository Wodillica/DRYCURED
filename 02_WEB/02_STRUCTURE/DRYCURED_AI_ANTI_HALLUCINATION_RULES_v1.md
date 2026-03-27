# DRYCURED_AI_ANTI_HALLUCINATION_RULES_v1

Status: safety rules v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira pravila kojima DRYCURED AI izbjegava halucinacije i lažni autoritet.

---

## 2. Glavno pravilo

AI ne smije govoriti kao da knjiga nešto tvrdi ako retrieval nije pronašao odgovarajući dokazni objekt iz knjige.

---

## 3. Obavezna pravila

- odgovor mora biti vezan uz retrieval sloj
- tvrdnja iz knjige mora imati source evidence
- inferencija mora biti označena kao inferencija
- nedostatak dokaza mora biti eksplicitno priznat
- tablice i infografike smiju se vezati samo kad su stvarno relevantne
- odgovor ne smije izmišljati chapter, section, label ili vizual

---

## 4. Zabranjena ponašanja

- slobodno dopunjavanje procesa “iz općeg znanja” kao da je iz knjige
- izmišljanje uzroka ili korekcije bez odgovarajućih objekata
- spajanje nepovezanih tablica u jedan lažni zaključak
- prikazivanje generičkog sigurnosnog savjeta kao citata knjige ako nije dohvaćen
- pretvaranje vizuala u dokaz ako su samo vizualno srodni, ali ne i sadržajno relevantni

---

## 5. Pravilo za problem odgovore

Kod problem odgovora sustav mora provjeriti postoji li barem jedan od sljedećih dokaznih paketa:
- problem + uzrok + korekcija
- warning + process context
- simptom + tablica/infografika + section objašnjenje

Ako toga nema, odgovor mora biti ograničen i označen kao nedovoljno potkrijepljen.

---

## 6. Pravilo za teorijske odgovore

Kod theory moda sustav mora provjeriti:
- postoji li definicija ili theory blok
- postoji li process context
- postoje li relevantni supporting objekti

Ako postoji samo djelomičan support, odgovor to mora reći.

---

## 7. Evidence priority

Redoslijed povjerenja:
1. confirmed theory/problem object
2. directly related table
3. directly related infographic
4. section-level supporting text
5. inferred context
6. insufficient evidence

---

## 8. Zaključak

DRYCURED AI mora raditi kao source-disciplined knowledge system.

Bolje je reći “nemam dovoljno dokaza iz knjige” nego proizvesti uvjerljiv, ali lažan odgovor.
