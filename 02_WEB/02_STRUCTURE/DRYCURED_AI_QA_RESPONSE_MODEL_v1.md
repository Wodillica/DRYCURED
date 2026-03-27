# DRYCURED_AI_QA_RESPONSE_MODEL_v1

Status: response model v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira model AI odgovora za DRYCURED knowledge layer.

Odgovor mora biti strukturiran, dokaziv i spreman za web reuse.

---

## 2. Obavezna response polja

Svaki odgovor mora podržavati:
- `short_answer`
- `detailed_explanation`
- `possible_causes`
- `recommended_corrections`
- `related_book_sections`
- `related_tables`
- `related_infographics`
- `related_tools`
- `confidence_basis`
- `source_evidence`
- `insufficient_evidence`

---

## 3. Dodatna tehnička polja

Preporučena dodatna polja:
- `mode`
- `answer_status`
- `confirmed_claims`
- `inferred_claims`
- `excluded_claims`
- `related_recipes`
- `process_context`
- `warnings`

---

## 4. Claim status model

Svaki odgovor mora jasno odvajati:
- `confirmed_from_book`
- `inferred_from_book_context`
- `insufficient_evidence`

Pravilo:
- `short_answer` smije koristiti samo confirmed tvrdnje ili eksplicitno označenu inferenciju
- `detailed_explanation` mora pokazati iz čega je izveden odgovor

---

## 5. Problem mode response oblik

Minimalni sastav:
- `short_answer`
- `possible_causes`
- `recommended_corrections`
- `related_book_sections`
- `related_tables`
- `related_infographics`
- `confidence_basis`
- `source_evidence`
- `insufficient_evidence`

---

## 6. Theory mode response oblik

Minimalni sastav:
- `short_answer`
- `detailed_explanation`
- `process_context`
- `related_book_sections`
- `related_tables`
- `related_infographics`
- `confidence_basis`
- `source_evidence`
- `insufficient_evidence`

---

## 7. Confidence basis

`confidence_basis` ne smije biti generički score bez objašnjenja.

Mora sadržavati:
- broj i tip primarnih objekata
- jesu li uključene tablice / infografike
- je li odgovor čist confirmed ili djelomično inferred
- postoje li kontradikcije ili rupe u evidence packu

---

## 8. Source evidence

`source_evidence` mora moći prikazati:
- chapter
- section
- object type
- label ako postoji
- caption ako je riječ o tablici ili infografici

---

## 9. Insufficient evidence flag

Ako retrieval ne daje dovoljno dokaznog materijala:
- `insufficient_evidence = true`
- odgovor mora to reći jasno i kratko
- ne smije se glumiti zaključak iz knjige koji knjiga nije podržala

---

## 10. Zaključak

DRYCURED AI response model nije samo tekstualni izlaz nego evidence paket koji web i buduća aplikacija mogu prikazati transparentno.
