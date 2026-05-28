# Drycured wp-config DISALLOW_FILE_EDIT — 2026-05-28

Status: PASS

Purpose:
Harden WordPress by disabling theme/plugin file editing from the WordPress admin interface.

Action:
- Added DISALLOW_FILE_EDIT to wp-config.php.
- No plugin, theme or content changes were made.

Final live check:
- Home returned 200 OK.
- wp-login.php returned 200 OK.
- wp-json returned 200 OK.
- sitemap.xml returned 200 OK.

Rollback:
Restore /var/www/html/wp-config.php from wp-config.before_disallow_file_edit.bak.
