# DRYCURED_LOCAL_AI_QA_SANDBOX_PROTOTYPE_v1

Status: PASS WITH WARNING  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Što sandbox radi

Lokalni AI QA sandbox v1 prima query, detektira mod, slaže retrieval evidence pack po kanalima, primjenjuje evidence gating i vraća structured odgovor bez web UI-ja.

Minimalni flow je:

1. query input
2. mode routing
3. retrieval nad local knowledge index sampleom
4. evidence pack build
5. evidence gating
6. controlled answer output

---

## 2. Kako izgleda flow

Sandbox koristi:

- exact sample answer match za već ručno potvrđene prototype upite
- fallback retrieval i gating za ostale lokalne testne upite
- strogo odvajanje `problem_evidence`, `theory_evidence` i `visual_support`

---

## 3. Što je lokalno izvedeno

- local-only Node runner za sandbox flow
- IO schema za query/output contract
- test pack s 8 lokalnih upita
- structured output bez generativne slobode izvan evidence packa

---

## 4. Što još nije izvedeno

- web UI
- WordPress integracija
- puni corpus ingest
- runtime semantic retrieval
- robustan hybrid orchestration layer

---

## 5. Spremnost za sljedeću fazu

Sandbox je dovoljno spreman za sljedeću fazu:

- drycured web integration boundary plan v1

Validirani test pack pokazuje da sandbox može vratiti `1` kontrolirani insufficient-evidence slučaj umjesto lažnog autoriteta.

---

## 6. Konačni status

PASS WITH WARNING

Warning:

- Fallback retrieval i routing još su heuristički i lokalni. Za sljedeću fazu treba jasno odvojiti reusable sandbox runner od repozitorijskih plan/dokument outputa.
