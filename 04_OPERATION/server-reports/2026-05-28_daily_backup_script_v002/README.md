# Drycured Daily DB + Config Backup Script v0.0.2 — 2026-05-28

Status: PASS

Purpose:
Create and validate a reusable script for local daily drycured.com database and configuration backups.

Script:
- /root/drycured_ops/drycured_daily_db_config_backup.sh

Backup output:
- /root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58

Included:
- Database dump compressed as .sql.gz
- Nginx config archive
- Protected wp-config.php copy
- MANIFEST.txt
- SHA256SUMS.txt
- backup.log

Validation:
- Script syntax: PASS.
- Database gzip test: PASS.
- Nginx tar validation: PASS.
- SHA256 validation: PASS.
- Home, sitemap.xml, Recepti, Alati and wp-json returned 200 OK after manual script run.

Fix from v0.0.1:
- logs/backup.log was excluded from SHA256SUMS because the log continues to change after checksum generation.
- SHA256SUMS now covers only db, config and MANIFEST.txt.

Security note:
Actual backup files are not committed to Git because they contain database and wp-config material. Git archives only the report and a copy of the backup script.

Next recommended phase:
- Create restore checklist without executing restore.
- Then add cron/systemd timer for daily execution.
- Later add weekly uploads backup and offsite backup.
