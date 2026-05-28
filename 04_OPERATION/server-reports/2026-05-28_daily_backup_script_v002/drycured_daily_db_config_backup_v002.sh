#!/usr/bin/env bash
set -euo pipefail

TS=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_ROOT="/root/drycured_production_backups"
BACKUP_DIR="$BACKUP_ROOT/daily_db_config_${TS}"
WP_ROOT="/var/www/html"
LOG_FILE="$BACKUP_DIR/logs/backup.log"
RETENTION_DAYS=7

mkdir -p "$BACKUP_DIR"/{db,config,manifests,logs}
chmod 700 "$BACKUP_ROOT" "$BACKUP_DIR"

log() {
  echo "[$(date -Is)] $*" | tee -a "$LOG_FILE"
}

log "DRYCURED daily DB + config backup v0.0.2 started"
log "Backup dir: $BACKUP_DIR"

cd "$WP_ROOT"

DB_OUT="$BACKUP_DIR/db/drycured_db_${TS}.sql.gz"
log "Exporting database..."
wp db export - --allow-root | gzip -9 > "$DB_OUT"

log "Testing database gzip..."
gzip -t "$DB_OUT"

log "Archiving Nginx config..."
tar -czf "$BACKUP_DIR/config/nginx_config_${TS}.tar.gz" /etc/nginx 2>> "$LOG_FILE"

log "Copying protected wp-config.php..."
cp "$WP_ROOT/wp-config.php" "$BACKUP_DIR/config/wp-config_${TS}.php.bak"
chmod 600 "$BACKUP_DIR/config/wp-config_${TS}.php.bak"

cat > "$BACKUP_DIR/manifests/MANIFEST.txt" <<MANIFEST
DRYCURED DAILY DB + CONFIG BACKUP v0.0.2
Timestamp: $TS
Server: $(hostname)
WordPress root: $WP_ROOT
Backup dir: $BACKUP_DIR

Included:
- Database dump: db/drycured_db_${TS}.sql.gz
- Nginx config archive: config/nginx_config_${TS}.tar.gz
- Protected wp-config copy: config/wp-config_${TS}.php.bak

Not included:
- wp-content/uploads
- full /var/www/html
- offsite backup
- automated restore

Retention:
- Intended local retention: ${RETENTION_DAYS} days for daily_db_config_* directories.
MANIFEST

cd "$BACKUP_DIR"
{
  find ./db ./config -type f -print
  printf '%s\n' ./manifests/MANIFEST.txt
} | sort | xargs sha256sum > manifests/SHA256SUMS.txt

log "Validating SHA256 checksums..."
sha256sum -c manifests/SHA256SUMS.txt >> "$LOG_FILE"

log "Applying retention: deleting daily_db_config_* older than ${RETENTION_DAYS} days"
find "$BACKUP_ROOT" -maxdepth 1 -type d -name "daily_db_config_*" -mtime +"$RETENTION_DAYS" -print -exec rm -rf {} \; >> "$LOG_FILE" 2>&1 || true

log "Final backup files:"
find "$BACKUP_DIR" -type f -printf "%TY-%Tm-%Td %TH:%TM %s %p\n" | sort >> "$LOG_FILE"

log "DRYCURED daily DB + config backup v0.0.2 finished: PASS"

echo "BACKUP_DIR=$BACKUP_DIR"
echo "LOG_FILE=$LOG_FILE"
