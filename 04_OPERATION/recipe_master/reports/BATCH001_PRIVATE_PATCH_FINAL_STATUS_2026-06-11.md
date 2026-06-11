# Batch001 private patch — finalni status

Datum: 2026-06-11  
Alat: `tools/global_recipe_database/drycured_batch001_private_post_patch_v2.php`  
Status: ZAKLJUČANO / SIGURNO / BEZ WORDPRESS UPISA

## Sažetak

Batch001 private patch alat je očvrsnut tako da ne može slučajno degradirati postojeće recepte.

Završni DRY RUN pokazuje:

- `would_patch_existing_private: 0`
- `patched_existing_private: 0`
- `wordpress_write_performed: NO`
- `DRY_RUN_ROWS: 0`
- `SOURCE_GATE_FAILED_ROWS: 0`

## Što je popravljeno

1. Meta-first matching:
   - recepti se više ne sparuju samo po slugu;
   - smanjen rizik krivog spajanja zapisa.

2. Source gate:
   - blokira sirove, prekratke, kontaminirane ili pogrešno strukturirane izvore.

3. Country taxonomy:
   - popravljeni `dry_country` redci u postojećem `BATCH_001_TAXONOMY_MAPPING.csv`;
   - usklađeni `term_name`, `term_slug` i `existing_term_id` prema postojećim WP terminima.

4. Source-gate false positives:
   - uklonjena pogrešna klasifikacija proizvoda kao “Cijeli komad” ondje gdje WP već ima točniju kategoriju;
   - sužen whole-cut regex da ne hvata izraze poput “cijeli komadići”.

5. Content regression gate:
   - blokira upis ako bi source markdown skratio postojeći sadržaj.

6. Web structure regression gate:
   - blokira zamjenu Drycured strukturiranog sadržaja sirovim source excerptom.

## Završna odluka za blokirane recepte

### 32 javna recepta

Svi javni recepti ostaju netaknuti.

Razlog:
- postojeći WP sadržaj ima Drycured strukturu;
- source excerpt je siroviji ili slabiji;
- private patch alat ne smije mijenjati javno objavljene recepte.

Odluka:
`KEEP_PUBLIC_WP_CONTENT__SOURCE_RAW_OR_WEAKER`

### 18 privatnih recepata

Svi privatni blokirani recepti ostaju netaknuti u ovom batch alatu.

Razlog:
- u 16 slučajeva postojeći WP sadržaj ima bolju Drycured strukturu od source excerpta;
- u 2 slučaja source excerpt je kraći od postojećeg sadržaja.

Odluke:
- `KEEP_EXISTING_WP_CONTENT__SOURCE_IS_RAW_OR_WEAKER`
- `KEEP_EXISTING_WP_CONTENT__SOURCE_SHORTER`

## Zaključak

Batch001 private patch više nije alat za masovni upis ovih zapisa, nego sigurnosni validator koji sprječava degradaciju postojećeg sadržaja.

Za daljnji rad ne treba izvršavati ovaj batch importer nad postojećim javnim ili boljim privatnim receptima.

Sljedeći radni smjer:
- javne recepte obrađivati samo kroz zaseban publish-safe audit;
- nove ili loše privatne recepte uređivati ručno/source-safe, po tehnološkoj skupini;
- ne koristiti sirovi source excerpt za prepisivanje kvalitetnijeg Drycured prikaza.
