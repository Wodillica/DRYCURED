# Drycured.com — Process SEO FAQ v0.1.8 Lock Note

Datum: 2026-05-27 16:38:57
Server: swab-production

## Što je napravljeno

Uređena je admin-only SEO + FAQ mapa za 12 procesnih stranica.

Plugin:

`wp-content/mu-plugins/drycured-process-seo-faq.php`

## Promjene

- skraćeni i ujednačeni SEO titleovi
- dotjerani meta opisi
- FAQ sadržaj ostaje u admin-only mapi
- javna primjena ostaje isključena

## Važno

Ova verzija ne mijenja javni frontend.

Opcija ostaje:

`drycured_process_seo_faq_public_enabled=0`

## Potvrđeno

- PHP lint: PASS
- item_count=12
- svaka procesna stranica ima 4 FAQ pitanja
- SEO titleovi: 49–58 znakova
- meta opisi: 109–130 znakova
- public marker count=0
- FAQPage public marker count=0

## Ne dira

- procesne stranice
- Elementor sadržaj
- Process Hub
- Home Rail Adapter
- alate
- meni
- javne SEO meta podatke
- FAQPage schema markup

## Rollback

```bash
cp "/root/drycured_reports/process_seo_faq_finetune_v018_20260527_163613/drycured-process-seo-faq.before-v018.20260527_163613.php" /var/www/html/wp-content/mu-plugins/drycured-process-seo-faq.php
wp cache flush --allow-root
```

## Sljedeći dopušteni korak

Sljedeće smije biti samo plan javne aktivacije ili isključeni schema/meta test.
Ne uključivati javni SEO/FAQ output bez zasebnog plana, audita i rollbacka.
