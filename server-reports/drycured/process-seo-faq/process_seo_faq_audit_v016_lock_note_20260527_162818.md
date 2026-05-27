# Drycured.com — Process SEO + FAQ Audit v0.1.6 Lock Note

Datum: 2026-05-27 16:28:18
Server: swab-production

## Što je napravljeno

Izrađen je SEO + FAQ audit za 12 procesnih stranica drycured.com.

## Važno

Ovaj korak nije mijenjao produkciju.

Nije dirano:

- procesne stranice
- Elementor sadržaj
- Process Hub
- Home Rail Adapter
- alati
- meni
- SEO meta podaci
- schema markup

## Izrađeno

- HTTP snapshot 12 procesnih stranica
- WordPress page/meta snapshot
- HTML SEO structure analiza
- prijedlog SEO title/meta description/FAQ pitanja za 12 procesa

## Preporučeni nastavak

Sljedeći korak smije biti samo isključeni/admin-only SEO + FAQ map plugin:

`wp-content/mu-plugins/drycured-process-seo-faq.php`

Prva verzija ne smije javno mijenjati meta podatke ni schema markup.

## Pravilo za buduću implementaciju

SEO + FAQ se uključuje postupno, prvo kroz admin-only pregled, zatim po jednoj stranici, uz rollback opciju.

