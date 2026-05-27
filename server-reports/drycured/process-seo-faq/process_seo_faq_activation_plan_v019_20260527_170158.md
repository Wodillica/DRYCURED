# Drycured.com — Process SEO FAQ Activation Plan v0.1.9

Status: plan only. No production changes.

Current state:
- Admin-only SEO FAQ plugin exists.
- Public output is disabled.
- drycured_process_seo_faq_public_enabled=0.
- 12 process pages have SEO title, meta description and 4 FAQ items in admin map.

Activation rule:
Do not enable all 12 pages at once.

Required future options:
- drycured_process_seo_faq_public_enabled=0
- drycured_process_seo_faq_schema_enabled=0
- drycured_process_seo_faq_visible_block_enabled=0
- drycured_process_seo_faq_test_slug=susenje

Phase 1:
Add schema preview only, admin-only, no public output.

Phase 2:
Enable FAQPage JSON-LD only on /proces-izrade/susenje/ after separate approval.

Phase 3:
If test passes, expand schema-only to Soljenje, Fermentacija and Sušenje.

Phase 4:
Only later plan a visible FAQ block, first on Sušenje.

Do not touch:
- Elementor content
- Process Hub
- Home Rail Adapter
- menus
- tools
- process page layout
- public title/meta control before SEO plugin audit

Rollback for future public schema:
wp option update drycured_process_seo_faq_public_enabled 0 --allow-root
wp option update drycured_process_seo_faq_schema_enabled 0 --allow-root
wp cache flush --allow-root

Next allowed code step:
drycured-process-seo-faq.php v0.2.0 with admin-only schema preview and all public options disabled.
