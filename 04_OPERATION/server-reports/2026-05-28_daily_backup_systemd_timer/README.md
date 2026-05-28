# Drycured Daily DB + Config Backup systemd Timer — 2026-05-28

Status: PASS

Purpose:
Enable controlled daily systemd timer execution for drycured.com database and configuration backups.

Service:
- /etc/systemd/system/drycured-daily-db-config-backup.service

Timer:
- /etc/systemd/system/drycured-daily-db-config-backup.timer

Schedule:
- Daily at 03:20 UTC
- Persistent=true
- RandomizedDelaySec=10m

Script executed:
- /root/drycured_ops/drycured_daily_db_config_backup.sh

Retention:
- Daily DB/config backups are retained for 7 days.
- Older daily_db_config_* directories are removed by the backup script.

Manual systemd test:
- systemctl start drycured-daily-db-config-backup.service executed successfully.
- Service exited with status=0/SUCCESS.
- New backup was created.
- SHA256 validation passed.
- Home, sitemap.xml, Recepti, Alati and wp-json returned 200 OK after service run.

Latest tested backup:
- /root/drycured_production_backups/daily_db_config_2026-05-28_17-00-13

Security note:
Actual backup files are not committed to Git because they contain database and wp-config material. Git archives only the report, unit files and validation output.

Next recommended phase:
- Create backup monitoring/check script.
- Add weekly uploads backup separately.
- Later add offsite backup and monthly restore drill.
