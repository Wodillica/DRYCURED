# Drycured Restore Checklist v0.0.1 — 2026-05-28

Status: PASS — procedure only, no restore executed

Purpose:
Create a controlled restore checklist for drycured.com database and configuration backups.

Scope:
- Database restore procedure
- Nginx config inspection/restore procedure
- wp-config.php restore procedure
- Emergency pre-restore backup procedure
- Post-restore live checks
- Rollback procedure if restore fails

Important:
No restore was executed in this phase.
This is documentation only.

Latest backup referenced:
- /root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58

Covered:
- DB backup verification
- gzip test
- SHA256 verification
- Nginx tar inspection
- emergency pre-restore backup
- post-restore health checks

Not yet covered:
- wp-content/uploads restore
- full WordPress root restore
- offsite restore
- staging restore drill

Next recommended phase:
- Add systemd timer or cron for daily DB/config backup after one more manual verification.
- Then add weekly uploads backup.
- Later add offsite backup and monthly restore drill.
