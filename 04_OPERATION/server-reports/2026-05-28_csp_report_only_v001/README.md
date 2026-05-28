# Drycured CSP Report-Only v0.0.1 — 2026-05-28

Status: PASS

Purpose:
Introduce a conservative Content-Security-Policy-Report-Only header for monitoring only, without blocking resources.

Policy:
- Report-Only mode only.
- No blocking CSP was added.
- Allows self, HTTPS resources, data/blob images and WordPress/Elementor inline behavior for the first monitoring phase.
- Allows GTranslate CDN and Google Fonts domains observed during audit.

Final live check:
- Home, Proces izrade, Recepti, Alati, Kalkulator sušenja, Starter kulture, sitemap.xml, wp-json and wp-login.php returned 200 OK.
- Home header summary confirms Content-Security-Policy-Report-Only is present.
- No Content-Security-Policy blocking header was added.

Notes:
This is a monitoring baseline. Future tightening should be based on browser console/CSP reports and not done blindly.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from drycured.before_csp_report_only_v001.bak and remove /etc/nginx/snippets/drycured-csp-report-only.conf if needed.
