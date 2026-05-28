# Drycured login rate limit — 2026-05-28

Status: PASS

Purpose:
Add conservative Nginx rate limiting to wp-login.php after audit showed repeated bot login attempts.

Findings before mitigation:
- wp-login.php received repeated GET + POST attempts from many external IPs.
- admin-ajax.php has legitimate WordPress/admin usage and was not rate-limited in this phase.
- wp-json remains available and was not restricted in this phase.

Action:
- Added Nginx limit_req_zone: drycured_login, 5 requests/minute.
- Added wp-login.php location block with burst=10 nodelay.
- Only wp-login.php was rate-limited.

Final live check:
- wp-login.php remained accessible for normal request.
- wp-admin redirected normally.
- wp-json remained 200 OK.
- admin-ajax.php remained available.
- Home, sitemap.xml, Recepti and Alati remained 200 OK.
- Burst test: first requests returned 200; later rapid requests returned 503, confirming rate limit is active.

Notes:
- 503 is Nginx default limit response. Future cleanup may switch this to 429 Too Many Requests.
- Do not rate-limit admin-ajax.php or REST without separate deeper audit.

Rollback:
Restore /etc/nginx/nginx.conf from nginx.conf.before_login_rate_limit.bak.
Restore /etc/nginx/sites-enabled/drycured from drycured.before_login_rate_limit.bak.
Run nginx -t and reload Nginx.
