# Drycured Basic Security Headers — 2026-05-28

Status: PASS

Purpose:
Add conservative baseline HTTP security headers to drycured.com without introducing CSP yet.

Headers added:
- X-Content-Type-Options: nosniff
- Referrer-Policy: strict-origin-when-cross-origin
- X-Frame-Options: SAMEORIGIN
- Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), bluetooth=()
- Strict-Transport-Security: max-age=300

Notes:
- CSP was intentionally not added in this phase.
- HSTS is in pilot mode only: max-age=300, no includeSubDomains, no preload.
- Key public pages remained 200 OK.
- sitemap.xml remained 200 OK.
- Some WordPress endpoints may show duplicate non-conflicting headers; this is noted for later cleanup.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from drycured.before_security_headers_basic.bak and remove /etc/nginx/snippets/drycured-security-headers-basic.conf if needed.
