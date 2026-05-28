# Drycured MU-plugins .bak Public Access Block v0.0.2 — 2026-05-28

Status: PASS

Purpose:
Block public access to stale MU-plugin backup/source files such as .php.bak-* and .css.bak-*.

Finding:
Public exposure retest showed multiple MU-plugin backup files returned HTTP 200 OK before mitigation.

Initial v0.0.1 result:
- Some .bak-* files were blocked.
- Several .php.bak-* files still returned 200 OK.
- Reason: older/global regex covered files ending in .bak but not filenames containing .bak-*.

Final action:
- Replaced/strengthened the MU-plugin backup/source Nginx block with v0.0.2.
- v0.0.2 blocks:
  - /wp-content/mu-plugins/.*.bak*
  - /wp-content/mu-plugins/.*.(old|orig|save)-style variants

Validation:
- Nginx config test passed.
- All representative MU-plugin .bak-* URLs returned 403 Forbidden.
- Rejected staging ZIP files remained 403 Forbidden.
- Home, sitemap.xml, Recepti, Alati and wp-json remained 200 OK.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from the archived backup file and reload Nginx.

Next recommended phase:
- Quarantine stale MU-plugin .bak-* files and rejected ZIP files from web root into root-only quarantine.
- Do not delete files directly; move with manifest and rollback paths.
