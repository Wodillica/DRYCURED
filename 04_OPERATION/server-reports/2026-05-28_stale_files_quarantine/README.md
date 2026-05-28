# Drycured Stale Files Quarantine — 2026-05-28

Status: PASS

Purpose:
Move stale backup/source files out of the public WordPress web root into root-only quarantine after public access protections were confirmed.

Source categories:
- MU-plugin .bak-* files from:
  /var/www/html/wp-content/mu-plugins
- Rejected infographics staging ZIP files from:
  /var/www/html/wp-content/uploads/drycured/infografike-greske-staging

Quarantine location:
- /root/drycured_quarantine/stale_files_2026-05-28_17-53-45

Action:
- Files were moved, not deleted.
- A manifest was created with original source path and destination path for rollback.
- Post-quarantine check confirmed no remaining MU-plugin .bak-* files in web root.
- Post-quarantine check confirmed no remaining rejected ZIP files in the staging web root.

Validation:
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.
- Representative old MU-plugin .bak URL returned 403 Forbidden.
- Representative old rejected ZIP URL returned 403 Forbidden.

Rollback:
Use quarantine_manifest.txt to move files back from the quarantine location to their original paths if needed.

Security note:
This cleanup reduces public web root clutter and removes stale backup/source files from the served directory. The files are retained in root-only quarantine for audit and rollback.

Next recommended phase:
- Re-run File Integrity / Malware Audit v0.0.2 to confirm the web root is clean after quarantine.
- Later review WPCode / Insert Headers and Footers snippets because the plugin contains eval-based snippet execution.
