# Drycured Health Report v0.0.1 — 2026-05-28

Status: PASS — read-only report

Purpose:
Create a single read-only operational health report for drycured.com after the first technical-security and backup hardening phase.

Checks included:
- Host uptime
- Disk space
- Memory
- Git status
- Last commits
- Daily DB/config backup systemd timer
- Backup monitor
- Latest daily DB/config backups
- Latest weekly uploads backup
- Backup storage usage
- Nginx config test
- PHP version
- WordPress version
- Active plugins
- Live URL health
- Protected route checks
- Security header sample
- Mail error log check

Validated:
- Git status clean.
- Nginx config test passed.
- Daily DB/config backup timer enabled and active.
- Backup monitor returned PASS.
- Latest daily DB/config backup passed SHA256 verification.
- Weekly uploads backup exists.
- Home, sitemap.xml, robots.txt, Recepti, Alati, wp-json and wp-login.php returned 200 OK.
- XML-RPC and exposed backup/archive/plugin backup routes returned 403 Forbidden.
- Basic security headers and CSP Report-Only are present.

Notes:
- HSTS remains in pilot mode with max-age=300.
- CSP remains Report-Only and should not be tightened without browser/report evidence.
- Mail error log contains older SMTP authentication errors from 2026-05-06. SMTP remains a separate follow-up item.

Next recommended phase:
- Health Report v0.0.2 can later add machine-readable PASS/WARNING/FAIL summary.
- Next practical task: either SMTP fix, file integrity/malware audit, or Core Web Vitals baseline.
