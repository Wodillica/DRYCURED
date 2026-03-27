# DRYCURED_KNOWLEDGE_OBJECT_SCHEMA_v1

Status: schema plan v1  
Projekt: drycured.com  
Jezik rada: hrvatski

---

## 1. Svrha dokumenta

Ovaj dokument definira knowledge object model za DRYCURED AI knowledge core.

Knowledge object je osnovna jedinica retrievala, dokazivanja i odgovora.

---

## 2. Zajednička obavezna polja za sve objekte

Svaki knowledge object mora imati:
- `object_id`
- `object_type`
- `title`
- `body`
- `source_file`
- `source_chapter`
- `source_section_path`
- `source_labels`
- `source_anchor_type`
- `evidence_level`
- `tags`
- `related_object_ids`
- `retrieval_text`

---

## 3. Zajednička opcionalna polja

- `subtitle`
- `summary`
- `chapter_number`
- `section_number`
- `subsection_number`
- `visual_asset_path`
- `caption`
- `table_schema`
- `warning_severity`
- `problem_keywords`
- `process_phase_key`
- `related_tool_keys`
- `related_recipe_keys`
- `notes`

---

## 4. Tipovi objekata

### 4.1. Teorijski blok
Obavezno:
- `object_type = theory_block`
- `body`
- `source_section_path`

Opcionalno:
- `summary`
- `process_phase_key`
- `related_table_ids`
- `related_visual_ids`

### 4.2. Definicija
Obavezno:
- `object_type = definition`
- `title`
- `body`

Opcionalno:
- `aliases`
- `related_theory_ids`

### 4.3. Problem
Obavezno:
- `object_type = problem`
- `title`
- `body`
- `problem_keywords`

Opcionalno:
- `symptom_aliases`
- `related_cause_ids`
- `related_correction_ids`

### 4.4. Uzrok
Obavezno:
- `object_type = cause`
- `body`

Opcionalno:
- `related_problem_ids`
- `related_warning_ids`

### 4.5. Korekcija
Obavezno:
- `object_type = correction`
- `body`

Opcionalno:
- `related_problem_ids`
- `related_tool_keys`

### 4.6. Warning
Obavezno:
- `object_type = warning`
- `body`
- `warning_severity`

Opcionalno:
- `trigger_conditions`
- `related_problem_ids`

### 4.7. Procesna faza
Obavezno:
- `object_type = process_phase`
- `title`
- `body`
- `process_phase_key`

Opcionalno:
- `phase_inputs`
- `phase_outputs`
- `critical_parameters`

### 4.8. Tablica
Obavezno:
- `object_type = table`
- `caption`
- `source_labels`
- `table_schema`

Opcionalno:
- `table_summary`
- `row_headers`
- `column_headers`

### 4.9. Infografika
Obavezno:
- `object_type = infographic`
- `caption`
- `visual_asset_path`
- `source_labels`

Opcionalno:
- `visual_summary`
- `related_section_ids`

### 4.10. Referenca na vizual
Obavezno:
- `object_type = visual_reference`
- `body`
- `related_object_ids`

### 4.11. Povezani alat
Obavezno:
- `object_type = related_tool`
- `title`
- `related_tool_keys`

### 4.12. Povezani recept
Obavezno:
- `object_type = related_recipe`
- `title`
- `related_recipe_keys`

---

## 5. Evidence level

Dopuštene vrijednosti:
- `confirmed_from_book`
- `inferred_from_book_context`
- `insufficient_evidence`

Pravilo:
- samo `confirmed_from_book` može biti citiran kao činjenica iz knjige
- `inferred_from_book_context` mora biti tako označen
- `insufficient_evidence` ne smije glumiti potvrđeni odgovor

---

## 6. Tagiranje

Minimalne tag skupine:
- tema
- proces
- simptom
- parametar
- rizik
- proizvodni tip
- mikroklima
- sigurnost
- dimljenje
- sušenje
- fermentacija

---

## 7. Source reference model

Svaki objekt mora nositi:
- putanju do `.tex` datoteke
- chapter id
- section path
- label ako postoji
- line range ili parser anchor kad bude dostupan

Bez source referencea objekt nije validan knowledge objekt.

---

## 8. Zaključak

Knowledge object schema mora biti dovoljno stroga da osigura dokazivost, a dovoljno fleksibilna da poveže tekst, tablice, infografike, alate i receptni sloj.
