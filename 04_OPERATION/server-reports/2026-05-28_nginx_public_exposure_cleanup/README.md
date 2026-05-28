# Drycured Nginx public exposure cleanup — 2026-05-28

Status: PASS

Purpose:
Clean up duplicated public exposure protection after confirmed mitigation.

Action:
- Removed misplaced include of drycured-public-exposure-blocks.conf from the non-HTTPS/redirect server block.
- Kept the working direct v0.0.2 location block inside the active drycured HTTPS server block.

Final live check:
- Backup/archive/source/staging URLs return 403 Forbidden.
- Home, sitemap.xml, Recepti and Alati return 200 OK.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from drycured.before_cleanup_fix.bak.
