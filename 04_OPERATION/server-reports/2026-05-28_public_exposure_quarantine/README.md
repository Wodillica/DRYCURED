# Drycured public exposure quarantine — 2026-05-28

Status: PASS

Purpose:
Block and remove public exposure of backup/archive/source/staging files from drycured.com web root.

Findings:
Several files returned public HTTP 200 before mitigation:
- /projekti/projekt_drycured.com_struktura.zip
- /swabtest/swabtools-homepage-techtemplate.zip
- /wp-content/plugins/drycured-recipe-core/includes/importer.php.bak
- /wp-content/uploads/drycured/infografike-greske-staging/...zip

Mitigation:
- Added Nginx blocking rules for public backup/archive/staging paths.
- Moved exposed ZIP and .bak files to root-only quarantine.
- No public content pages were changed.

Final live check:
- Exposed backup/archive/source URLs return 403 Forbidden.
- Home, sitemap.xml, Recepti and Alati return 200 OK.

Rollback:
Use the quarantine manifest and Nginx config backups stored in this report folder.
