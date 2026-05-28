# Drycured Backup / Restore Audit — 2026-05-28

Status: PASS as audit / FAIL as complete production backup strategy

Purpose:
Inventory existing drycured.com backup, restore and disaster recovery state without making changes.

Findings:
- WordPress root is approximately 1.4G.
- wp-content is approximately 1.3G.
- uploads is approximately 1.1G.
- database is approximately 49M.
- Many historical/manual backup directories exist under /root.
- Public backup exposure remains blocked with 403 Forbidden.
- No confirmed systematic drycured daily database backup job was found.
- No confirmed systematic uploads backup job was found.
- No confirmed offsite backup tool/config was found.
- No confirmed restore test was found.

Available tools:
- rsync
- mysqldump
- tar
- gzip
- zstd

Not confirmed:
- rclone
- restic
- borg
- aws/s3cmd

Live health:
- Home returned 200 OK.
- sitemap.xml returned 200 OK.
- Recepti returned 200 OK.
- Alati returned 200 OK.
- wp-json returned 200 OK.

Recommended next phase:
Create a controlled local backup baseline:
- daily database backup
- weekly wp-content/uploads backup
- nginx config backup
- wp-config protected copy
- manifest and checksum
- retention policy
- restore checklist
