# Drycured Local Backup Baseline v0.0.1 — 2026-05-28

Status: PASS

Purpose:
Create a manual local production backup baseline without introducing cron automation yet.

Backup location:
- /root/drycured_production_backups/local_baseline_2026-05-28_15-58-04

Included in local backup:
- Database dump: drycured_db_2026-05-28_15-58-04.sql.gz
- Nginx config archive: nginx_config_2026-05-28_15-58-04.tar.gz
- Protected wp-config copy: wp-config_2026-05-28_15-58-04.php.bak
- MANIFEST.txt
- SHA256SUMS.txt

Not included:
- wp-content/uploads
- full /var/www/html
- offsite backup
- automated cron
- restore execution

Validation:
- DB gzip validation: PASS.
- Nginx tar validation: PASS.
- SHA256 validation: PASS.
- Home, sitemap.xml, Recepti, Alati and wp-json returned 200 OK after backup.

Security note:
The actual backup files are not committed to Git because they contain database and wp-config material. Git contains only this report.

Next recommended phase:
- Add controlled daily DB/config backup script.
- Add retention policy.
- Add restore checklist.
- Later add weekly uploads backup and offsite backup.
