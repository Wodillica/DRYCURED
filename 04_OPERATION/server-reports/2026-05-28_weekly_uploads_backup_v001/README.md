# Drycured Weekly Uploads Backup v0.0.1 — 2026-05-28

Status: PASS

Purpose:
Create and validate a manual weekly backup script for drycured.com wp-content/uploads.

Script:
- /root/drycured_ops/drycured_weekly_uploads_backup.sh

Backup output:
- /root/drycured_production_backups/weekly_uploads_2026-05-28_17-28-17

Source:
- /var/www/html/wp-content/uploads

Source size:
- Approximately 1.1G

Backup archive:
- uploads_2026-05-28_17-28-17.tar.zst

Archive size:
- Approximately 972 MB

Included:
- Full wp-content/uploads archive
- MANIFEST.txt
- SHA256SUMS.txt
- backup.log

Not included:
- Database
- Nginx config
- wp-config.php
- Full WordPress root
- Offsite backup

Validation:
- Script syntax: PASS.
- Archive creation completed.
- Archive listing test: PASS.
- SHA256 validation: PASS.
- Home, sitemap.xml, Recepti, Alati and wp-json returned 200 OK after backup.

Note:
Initial validation command lost REPORT_DIR/BACKUP_DIR shell variables and produced empty-path errors. The backup itself was present and was validated afterward through recovered validation.

Retention:
- weekly_uploads_* directories older than 35 days are removed by the script.

Security note:
Actual backup files are not committed to Git because they contain uploaded media and project material. Git archives only the report and the backup script.

Next recommended phase:
- Run one more manual weekly uploads backup verification later, not immediately.
- Add weekly systemd timer after second successful manual run.
- Later add offsite backup.
