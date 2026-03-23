# DEDUPE RULES v1

## Svrha

Ovaj dokument definira početna pravila za prepoznavanje duplikata i skoro-duplikata u receptnom sustavu.

## Vrste duplikata

### 1. Egzaktni duplikat
Datoteke ili zapisi smatraju se egzaktno jednakima ako imaju:
- isti hash sadržaja
- isti URL i isti dohvaćeni sadržaj
- isti lokalni dokument bez sadržajnih razlika

Status:
- duplicate_exact

### 2. Fuzzy duplikat
Zapisi se smatraju sumnjivo sličnima ako imaju kombinaciju sljedećih obilježja:
- vrlo sličan ili isti naslov
- isti ili vrlo sličan skup sastojaka
- vrlo sličan redoslijed procesa
- isti proizvod, ista regija i isti tip izvora

Status:
- duplicate_fuzzy

## Polja za usporedbu
Uspoređivati primarno:
- normalized_name
- original_name
- country_guess
- region_guess
- ingredient signature
- process signature
- source_domain
- source_type

## Potpis recepta
Za fuzzy deduplikaciju treba generirati više potpisa:
- title_signature
- ingredient_signature
- process_signature
- recipe_signature_combined

## Pravila opreza
- regionalne varijante istog proizvoda nisu automatski duplikati
- prije spajanja provjeriti razlikuju li se:
  - omjeri soli
  - vrsta mesa
  - vrsta crijeva
  - dimljenje / bez dimljenja
  - trajanje zrenja
  - primjena češnjaka ili tekućine od češnjaka

## Preporučeni ishodi
- keep_primary
- keep_both_regional_variant
- merge_after_review
- manual_review_required

## Napomena

Deduplikacija ne smije uništiti mikroregionalne razlike. U ovom projektu sličnost nije uvijek isto što i višak.