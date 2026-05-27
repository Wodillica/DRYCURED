# Drycured.com — Process SEO FAQ v0.2.0 Lock Note

Datum: 2026-05-27 17:04:21
Server: swab-production

## Što je dodano

Dodan je admin-only FAQPage JSON-LD schema preview sloj u:

`wp-content/mu-plugins/drycured-process-seo-faq.php`

## Važno

Ova verzija ne dodaje javni schema output.

Opcije ostaju isključene:

`drycured_process_seo_faq_public_enabled=0`

`drycured_process_seo_faq_schema_enabled=0`

`drycured_process_seo_faq_visible_block_enabled=0`

Test slug:

`drycured_process_seo_faq_test_slug=susenje`

## Potvrđeno

- PHP lint: PASS
- schema JSON valid: PASS
- schema_question_count=4
- FAQPage public count=0
- drycured-process-faq-schema public count=0

## Ne dira

- javni frontend
- Elementor sadržaj
- Process Hub
- Home Rail Adapter
- alate
- meni
- vidljivi FAQ blok

## Sljedeći dopušteni korak

Samo audit/admin pregled schema previewa.

Ne uključivati javni schema output bez zasebnog odobrenja.
