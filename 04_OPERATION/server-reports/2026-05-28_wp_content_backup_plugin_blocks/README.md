# Drycured wp-content Backup Plugin Public Access Block — 2026-05-28

Status: PASS

Purpose:
Block public access to legacy WordPress backup plugin directories discovered during uploads backup audit.

Findings before mitigation:
- /wp-content/ai1wm-backups/ returned 200 OK.
- /wp-content/backups-dup-lite/ returned 200 OK.
- /wp-content/litespeed/auto-backup/ was already protected with 403.
- /wp-content/uploads/drycured/backups/ was already protected with 403.

Action:
- Added Nginx location blocks for:
  - /wp-content/ai1wm-backups/
  - /wp-content/backups-dup-lite/

Validation:
- Nginx config test passed.
- After reload, both target routes returned 403 Forbidden.
- Existing protected backup paths remained 403.
- Home, sitemap.xml, Recepti, Alati and wp-json remained available.

Notes:
The first live check still showed 200 OK for the new blocks, but after explicit Nginx reload retest both routes returned 403 Forbidden. Final state is PASS.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from the archived backup file and reload Nginx.
