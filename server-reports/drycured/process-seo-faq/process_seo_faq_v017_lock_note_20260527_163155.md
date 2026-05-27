# Drycured.com — Process SEO FAQ v0.1.7 Lock Note

Datum: 2026-05-27 16:31:57
Server: swab-production

## Što je dodano

Dodan je admin-only SEO + FAQ map plugin:

`wp-content/mu-plugins/drycured-process-seo-faq.php`

## Svrha

Plugin prikazuje SEO title, meta description i FAQ prijedloge za 12 procesnih stranica u WordPress adminu.

Admin lokacija:

`Alati -> Drycured SEO FAQ`

## Važno

Ova verzija ne mijenja javni frontend.

Opcija javne primjene ostaje isključena:

`drycured_process_seo_faq_public_enabled=0`

## Potvrđeno

- PHP lint: PASS
- admin funkcije: PASS
- item_count=12
- svaka procesna stranica ima 4 FAQ pitanja
- public marker count=0
- FAQPage public marker count=0
- drycured_process_seo_faq public marker count=0

## Ne dira

- procesne stranice
- Elementor sadržaj
- Process Hub
- Home Rail Adapter
- alate
- meni
- javne SEO meta podatke
- FAQPage schema markup

## Urednička napomena

Neki SEO titleovi su u draftu malo duži. Prije javnog uključivanja treba napraviti kratku SEO copy reviziju titleova i meta opisa.

## Rollback

```bash
rm -f /var/www/html/wp-content/mu-plugins/drycured-process-seo-faq.php
wp option delete drycured_process_seo_faq_public_enabled --allow-root
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeće smije biti samo:

1. admin pregled sadržaja,
2. SEO copy fine-tune,
3. tek nakon toga isključeni schema/meta test.

Ne uključivati javni SEO/FAQ output bez zasebnog plana i rollbacka.
