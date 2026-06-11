# WHOLE-CUT SOURCE-SAFE POLICY

Ovaj dokument zaključava pravilo za sve buduće skripte koje rade s receptima cijelih komada u drycured.com sustavu.

## Aktivni master

Aktivni podatkovni izvor za batch cijelih komada je:

`04_OPERATION/recipe_master/drycured_recipes_master_v1_1_cijeli_komadi_25_source_safe.json`

## Zabranjeni aktivni izvori za render/import cijelih komada

Sljedeće datoteke ne smiju biti aktivni render-source ni import-source za cijele komade:

- `04_OPERATION/recipe_master/drycured_recipes_master_v1_pilot_cijeli_komadi_25.json`
- `04_OPERATION/recipe_master/fill_candidates/batch01_cijeli_komadi_25/block_fill_candidates.json`
- `04_OPERATION/recipe_master/evidence/batch01_cijeli_komadi_25/editorial_evidence_candidates.json`
- `server-reports/recipes/mass-pipeline-v2/source_input/drycured_recipes_clean_rebuild_v1_2.json`

Te datoteke mogu služiti samo kao forenzički trag, evidence ili pomoćni urednički materijal, ali ne kao javni izvor istine.

## Obvezni gate

Svaka buduća skripta koja generira, osvježava, renderira ili importira cijele komade mora prije rada pozvati:

`python3 04_OPERATION/recipe_master/tools/whole_cut_source_safe_gate.py 04_OPERATION/recipe_master/drycured_recipes_master_v1_1_cijeli_komadi_25_source_safe.json`

Ako gate vrati `FAIL`, skripta mora stati prije bilo kakvog WordPress upisa.

## Razlog

Kod cijelih komada ne smiju se automatski prenositi kobasičarski blokovi poput mljevenja, nadjeva, punjenja, crijeva i generičkih režima dimljenja/sušenja/zrenja. Konkretni parametri procesa smiju se dodati tek nakon vanjske validacije izvora.
