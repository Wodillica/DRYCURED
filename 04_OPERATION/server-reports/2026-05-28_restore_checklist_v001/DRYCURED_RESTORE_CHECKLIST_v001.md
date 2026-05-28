# DRYCURED RESTORE CHECKLIST v0.0.1

Status: PROCEDURE ONLY — NO RESTORE EXECUTED

Date: 2026-05-28_16-55-25

Latest detected backup:
```
/root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58
```

## 1. Purpose

This checklist defines the controlled restore procedure for drycured.com database and configuration backups.

This document does not execute restore. It is a safe operational guide.

## 2. What this backup set contains

Expected files:

```
db/*.sql.gz
config/nginx_config_*.tar.gz
config/wp-config_*.php.bak
manifests/MANIFEST.txt
manifests/SHA256SUMS.txt
logs/backup.log
```

## 3. Before any restore

Do not restore directly unless there is a confirmed incident.

Before restore:

1. Confirm current incident.
2. Record current Git commit.
3. Record current live URL status.
4. Create emergency backup of current state.
5. Verify selected backup checksums.
6. Prefer staging restore first.
7. If production restore is unavoidable, put site in maintenance mode if needed.

## 4. Pre-restore commands

### 4.1 Record current Git state

```bash
cd /root/DRYCURED_GITHUB
git status --short
git log --oneline -n 10
```

### 4.2 Record current live status

```bash
for URL in \
"https://drycured.com/" \
"https://drycured.com/sitemap.xml" \
"https://drycured.com/recepti/" \
"https://drycured.com/alati/" \
"https://drycured.com/wp-json/"
do
  echo "---- $URL ----"
  curl -sS -o /dev/null -D - "$URL" | sed -n '1,18p'
  echo
done
```

### 4.3 Create emergency pre-restore backup

```bash
TS=$(date +"%Y-%m-%d_%H-%M-%S")
EMERGENCY_DIR="/root/drycured_production_backups/pre_restore_emergency_$TS"
mkdir -p "$EMERGENCY_DIR"/{db,config,logs}
chmod 700 "$EMERGENCY_DIR"

cd /var/www/html
wp db export - --allow-root | gzip -9 > "$EMERGENCY_DIR/db/drycured_db_pre_restore_$TS.sql.gz"

tar -czf "$EMERGENCY_DIR/config/nginx_config_pre_restore_$TS.tar.gz" /etc/nginx
cp /var/www/html/wp-config.php "$EMERGENCY_DIR/config/wp-config_pre_restore_$TS.php.bak"
chmod 600 "$EMERGENCY_DIR/config/wp-config_pre_restore_$TS.php.bak"

find "$EMERGENCY_DIR" -type f -print0 | sort -z | xargs -0 sha256sum > "$EMERGENCY_DIR/SHA256SUMS.txt"
```

## 5. Verify chosen backup before restore

Replace BACKUP_DIR with the selected backup path.

```bash
BACKUP_DIR="/root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58"

cd "$BACKUP_DIR"
sha256sum -c manifests/SHA256SUMS.txt

gzip -t db/*.sql.gz
tar -tzf config/nginx_config_*.tar.gz >/dev/null
```

Expected:

```
OK for all checksum entries
gzip test passes
tar test passes
```

## 6. Database restore procedure

WARNING: This overwrites the current production database.

Only execute after emergency backup and approval.

```bash
BACKUP_DIR="/root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58"
DB_FILE=$(ls "$BACKUP_DIR"/db/*.sql.gz | head -n 1)

cd /var/www/html

gzip -dc "$DB_FILE" | wp db import - --allow-root
wp cache flush --allow-root
```

## 7. Nginx config restore procedure

WARNING: Only restore Nginx config if the current Nginx configuration is broken or corrupted.

Recommended safer method:
extract to temporary directory, inspect, then copy selected files manually.

```bash
BACKUP_DIR="/root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58"
TMP_RESTORE="/root/drycured_restore_tmp/nginx_$(date +"%Y-%m-%d_%H-%M-%S")"
mkdir -p "$TMP_RESTORE"

tar -xzf "$BACKUP_DIR"/config/nginx_config_*.tar.gz -C "$TMP_RESTORE"

find "$TMP_RESTORE" -type f | sort
```

After inspection, copy only required files.

Then test:

```bash
nginx -t
systemctl reload nginx
```

## 8. wp-config restore procedure

WARNING: Only restore wp-config.php if current file is damaged.

```bash
BACKUP_DIR="/root/drycured_production_backups/daily_db_config_2026-05-28_16-51-58"
cp /var/www/html/wp-config.php /root/wp-config.before_restore_$(date +"%Y-%m-%d_%H-%M-%S").php.bak

cp "$BACKUP_DIR"/config/wp-config_*.php.bak /var/www/html/wp-config.php
chmod 600 /var/www/html/wp-config.php
```

Then test:

```bash
cd /var/www/html
wp core version --allow-root
```

## 9. Post-restore live checks

```bash
for URL in \
"https://drycured.com/" \
"https://drycured.com/sitemap.xml" \
"https://drycured.com/recepti/" \
"https://drycured.com/alati/" \
"https://drycured.com/wp-json/"
do
  echo "---- $URL ----"
  curl -sS -o /dev/null -D - "$URL" | sed -n '1,18p'
  echo
done
```

Expected:

```
Home: 200 OK
sitemap.xml: 200 OK
Recepti: 200 OK
Alati: 200 OK
wp-json: 200 OK
```

## 10. Rollback if restore fails

Use the emergency pre-restore backup created in section 4.3.

Restore database from emergency backup:

```bash
EMERGENCY_DIR="/root/drycured_production_backups/pre_restore_emergency_YYYY-MM-DD_HH-MM-SS"
gzip -dc "$EMERGENCY_DIR"/db/*.sql.gz | wp db import - --allow-root
wp cache flush --allow-root
```

Restore wp-config if needed:

```bash
cp "$EMERGENCY_DIR"/config/wp-config_*.php.bak /var/www/html/wp-config.php
chmod 600 /var/www/html/wp-config.php
```

Nginx rollback:
extract emergency Nginx config and restore only after inspection.

## 11. Current limitations

This restore checklist covers:

- database
- Nginx config
- wp-config.php

It does not yet cover:

- wp-content/uploads restore
- full WordPress root restore
- offsite restore
- staging restore test

## 12. Next recommended improvements

1. Create staging restore test.
2. Add weekly uploads backup.
3. Add offsite backup.
4. Add monthly restore drill.
5. Add automated daily backup after manual script has proven stable.
