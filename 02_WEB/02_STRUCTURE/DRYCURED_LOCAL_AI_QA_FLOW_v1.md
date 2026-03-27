# DRYCURED_LOCAL_AI_QA_FLOW_v1

Status: local flow v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Ulaz

- query
- optional expected_mode
- optional notes

---

## 2. Mode routing

- query se normalizira
- prepoznaju se problem i theory signali
- ako simptomatski signal dominira, ide problem mode
- ako ne dominira, default ostaje theory mode

---

## 3. Retrieval

- sandbox pretražuje local knowledge index sample
- score kombinira lexical overlap, object_type relevance i retrieval_priority
- exact sample queries imaju prioritetni shortcut na već potvrđeni answer sample

---

## 4. Evidence pack build

Sandbox slaže:

- `problem_evidence`
- `theory_evidence`
- `visual_support`

Visual objekti ostaju sekundarni support i ne preuzimaju ulogu glavnog anchor-a.

---

## 5. Answer composition

- primjenjuje se evidence gating
- ako anchor nije dovoljno jak, vraća se insufficient_evidence
- ako anchor postoji, odgovor se sastavlja u structured output sa sažetim answer poljima

---

## 6. Izlaz

Izlaz vraća:

- detected_mode
- evidence_pack po kanalima
- answer_output
- insufficient_evidence
- confidence_basis
- related_sources
