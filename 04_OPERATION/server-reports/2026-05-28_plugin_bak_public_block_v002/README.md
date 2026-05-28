# Drycured Plugin .bak Public Access Block v0.0.2 — 2026-05-28

Status: PASS

Purpose:
Block public access to stale plugin backup/source files inside wp-content/plugins.

Finding:
Public exposure retest showed multiple plugin backup/source files returned HTTP 200 OK before mitigation, including .php.bak.*, .css.bak.*, and other .bak-* variants inside drycured plugin directories.

Initial v0.0.1 result:
- Some plugin .bak files were blocked.
- Two representative files still returned 200 OK:
  - drycured-recipe-core/includes/importer.php.bak.20260507_172814
  - drycured-recipe-core/assets/css/drycured-recipes.css.bak.20260509_091412

Final action:
- Strengthened the Nginx plugin backup/source block to v0.0.2.
- v0.0.2 blocks:
  - /wp-content/plugins/...(.bak|.old|.orig|.save)(.|-|_|$)...
  - /wp-content/plugins/... archive/source file variants such as .sql, .tar, .zip, .7z, .rar with suffix variants

Validation:
- Nginx config test passed.
- All 8 representative plugin .bak URLs returned 403 Forbidden.
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from the archived backup file and reload Nginx.

Next recommended phase:
- Quarantine stale plugin .bak-* files from wp-content/plugins into root-only quarantine.
- Do not delete files directly; move with manifest and rollback paths.
