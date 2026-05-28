# Drycured XML-RPC block — 2026-05-28

Status: PASS

Purpose:
Block WordPress XML-RPC endpoint because it was active and receiving repeated automated POST traffic.

Findings before mitigation:
- XML-RPC POST system.listMethods returned HTTP 200 OK.
- XML-RPC exposed WordPress methods including system.multicall, pingback.ping, metaWeblog.*, blogger.* and wp.* methods.
- Nginx access logs showed repeated automated POST requests to //xmlrpc.php from external IPs.
- No confirmed legitimate dependency on XML-RPC was found for current drycured.com workflows.

Action:
- Added Nginx location block for /xmlrpc.php and //xmlrpc.php.
- REST API remains available.
- Login remains available.
- Public pages remain available.

Final live check:
- /xmlrpc.php returned 403 Forbidden.
- POST /xmlrpc.php returned 403 Forbidden.
- POST //xmlrpc.php returned 403 Forbidden.
- Home, wp-json, wp-login.php, sitemap.xml, Recepti and Alati returned 200 OK.

Rollback:
Restore /etc/nginx/sites-enabled/drycured from drycured.before_xmlrpc_block.bak and reload Nginx.
