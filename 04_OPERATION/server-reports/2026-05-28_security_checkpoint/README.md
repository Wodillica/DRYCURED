# Drycured Security Checkpoint — 2026-05-28

Status: PASS

Purpose:
Final checkpoint audit for the first drycured.com technical-security hardening series.

Verified controls:
- Public backup/archive/source/staging exposure returns 403 Forbidden.
- XML-RPC GET/POST and double-slash variant return 403 Forbidden.
- DISALLOW_FILE_EDIT is enabled in wp-config.php.
- Basic security headers are present.
- CSP Report-Only header is present; no blocking CSP was introduced.
- Login rate limit is active.
- REST API remains available.
- admin-ajax remains available.
- Home, sitemap.xml, Recepti, Alati and Registracija remain available.

Notes:
- wp-login.php may return 503 immediately after intentional burst testing because rate limiting is active. After cooldown, normal single access should return 200 OK.
- HSTS remains in pilot mode with max-age=300.
- CSP remains Report-Only and should not be tightened without browser/report evidence.

Rollback references:
Use the individual archived rollback files from each previous security step:
- public exposure quarantine
- Nginx public exposure cleanup
- basic security headers
- CSP Report-Only baseline
- wp-config DISALLOW_FILE_EDIT
- XML-RPC block
- login rate limit
