# DRYCURED_AI_EVIDENCE_GATING_RULES_v1

Status: evidence gating rules v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Kada je tvrdnja confirmed_from_book

Tvrdnja smije biti potvrđena iz knjige samo ako postoji barem jedan od ovih paketa:

- problem/cause/correction object koji eksplicitno pokriva upit
- theory_block / definition / process_phase koji izravno objašnjava traženi koncept
- table ili infographic uz relevantni tekstualni anchor, ne sama za sebe

---

## 2. Kada je dopuštena samo inferencija

Inferencija je dopuštena samo kad:

- retrieval ima relevantan teorijski ili procesni kontekst
- ali nema dovoljno direktan problem object za puni zaključak
- i inference ne prelazi izvan onoga što retrieval paket razumno podržava

Takve tvrdnje moraju ostati u `inferred_from_book_context`.

---

## 3. Kada mora vratiti insufficient_evidence

Sustav mora vratiti `insufficient_evidence = true` kad:

- nema dovoljno jak tekstualni anchor
- postoji samo periferni theory blok bez direktne veze s upitom
- postoji samo visual bez dovoljno jakog textual supporta
- retrieval kandidati međusobno ne daju jasan operativni zaključak

---

## 4. Kako se sprječava lažni problem autoritet

- generički theory_block ne smije automatski postati problem dokaz
- problem answer mode mora tražiti `problem`, `cause`, `correction` ili `warning` anchor
- process/theory blokovi mogu biti samo supporting evidence ako ne nose eksplicitni simptomatski signal

---

## 5. Uloga visual supporta

- tablice i infografike smiju pojačati odgovor
- ne smiju glumiti glavni dokaz bez tekstualnog anchor-a
- caption sam po sebi nije dovoljan za jaki zaključak ako nema relevantnog prose bloka ili dijagnostičke tablice
