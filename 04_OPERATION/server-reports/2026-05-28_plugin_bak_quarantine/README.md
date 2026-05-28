# Drycured Plugin .bak Quarantine — 2026-05-28

Status: PASS

Purpose:
Move stale plugin backup/source files out of the public WordPress plugin directory into root-only quarantine after public access protections were confirmed.

Source:
- /var/www/html/wp-content/plugins

Quarantine location:
- /root/drycured_quarantine/plugin_bak_files_2026-05-28_18-20-20

File categories moved:
- *.bak*
- *.old*
- *.orig*
- *.save*

Action:
- Files were moved, not deleted.
- Directory structure was preserved under quarantine.
- A manifest was created with original source path and destination path for rollback.

Validation:
- Post-quarantine plugin check confirmed no remaining .bak/.old/.orig/.save files in wp-content/plugins.
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.
- Representative old plugin .bak URLs returned 403 Forbidden.

Rollback:
Use quarantine_manifest.txt to move files back from quarantine to their original paths if needed.

Security note:
This cleanup removes stale plugin backup/source files from the served web root while keeping them available in root-only quarantine for audit and rollback.

Next recommended phase:
- Run File Integrity / Malware Audit v0.0.3 after plugin quarantine.
- Confirm no remaining .bak/.old/.orig/.save files in plugins or mu-plugins.
- Later review WPCode / Insert Headers and Footers snippets.
