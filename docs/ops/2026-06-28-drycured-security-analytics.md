# Drycured security and analytics update — 2026-06-28

## Summary

Performed immediate security hardening and analytics/log cleanup for drycured.com after observing high bot/scanner noise in Nginx access logs.

## Analytics and logging

- Installed and activated Independent Analytics in WordPress.
- Confirmed that Independent Analytics starts measuring visits from installation time onward.
- Created a dedicated Nginx access log for drycured.com:
  - `/var/log/nginx/drycured.access.log`
  - `/var/log/nginx/drycured.error.log`
- Removed the public GoAccess report from the web root:
  - `/var/www/html/goaccess-report.html`
- Generated private GoAccess reports under:
  - `/root/goaccess-reports/`
- GoAccess HTML/log reports were intentionally not committed because they may contain IP addresses and security-sensitive request data.

## Security hardening

Confirmed and/or applied the following protections:

- Blocked XML-RPC endpoint:
  - `/xmlrpc.php`
- Blocked common environment/config probes:
  - `/env`
  - `/aws.env`
  - `/.dotenv`
  - `/secrets.env`
- Blocked user enumeration probes:
  - `/?get_users=...`
- Blocked public multisite signup endpoint:
  - `/wp-signup.php`
- Confirmed public WordPress registration is disabled:
  - `users_can_register = 0`
  - network registration option remains disabled
- Confirmed normal public pages remain available:
  - `/` returns 200
  - `/recepti/` returns 200
- Changed blocked responses from Nginx `444` to `403` to avoid Cloudflare 520 noise in analytics and reports.

## Validation

Validated after Nginx reload:

- `nginx -t` passed successfully.
- `systemctl reload nginx` completed successfully.
- Dangerous/scanner URLs return 403.
- Public pages return 200.

## Notes

This was an immediate hardening pass. Further hardening should be done only after a fresh backup and successful `nginx -t` validation.

Do not commit GoAccess reports, server logs, IP data, private backup files or security-sensitive request traces.
