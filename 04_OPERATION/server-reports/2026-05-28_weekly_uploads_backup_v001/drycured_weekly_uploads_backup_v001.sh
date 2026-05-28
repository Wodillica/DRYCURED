#!/usr/bin/env bash
set -euo pipefail

TS=$(date +"%Y-%m-%d_%H-%M-%S")
BACKUP_ROOT="/root/drycured_production_backups"
BACKUP_DIR="$BACKUP_ROOT/weekly_uploads_${TS}"
UPLOADS="/var/www/html/wp-content/uploads"
LOG_FILE="$BACKUP_DIR/logs/backup.log"
RETENTION_DAYS=35

mkdir -p "$BACKUP_DIR"/{uploads,manifests,logs}
chmod 700 "$BACKUP_ROOT" "$BACKUP_DIR"

log() {
  echo "[$(date -Is)] $*" | tee -a "$LOG_FILE"
}

log "DRYCURED weekly uploads backup v0.0.1 started"
log "Backup dir: $BACKUP_DIR"
log "Uploads source: $UPLOADS"

if [ ! -d "$UPLOADS" ]; then
  log "FAIL: uploads directory not found."
  exit 2
fi

log "Measuring uploads size..."
du -sh "$UPLOADS" | tee -a "$LOG_FILE"

ARCHIVE="$BACKUP_DIR/uploads/uploads_${TS}.tar.zst"

log "Creating uploads archive with tar + zstd..."
tar --warning=no-file-changed -I 'zstd -19 -T0' -cf "$ARCHIVE" -C /var/www/html/wp-content uploads 2>> "$LOG_FILE" || {
  STATUS=$?
  if [ "$STATUS" -eq 1 ]; then
    log "WARNING: tar returned 1, likely file changed during archive. Continuing with validation."
  else
    log "FAIL: tar returned $STATUS."
    exit "$STATUS"
  fi
}

log "Testing archive listing..."
tar -I zstd -tf "$ARCHIVE" >/dev/null

cat > "$BACKUP_DIR/manifests/MANIFEST.txt" <<MANIFEST
DRYCURED WEEKLY UPLOADS BACKUP v0.0.1
Timestamp: $TS
Server: $(hostname)
Source: $UPLOADS
Backup dir: $BACKUP_DIR

Included:
- Full wp-content/uploads archive

Not included:
- Database
- Nginx config
- wp-config.php
- Full WordPress root
- Offsite backup

Retention:
- Intended local retention: ${RETENTION_DAYS} days for weekly_uploads_* directories.

Notes:
- This is a local weekly uploads backup.
- DB/config backups are handled separately by daily_db_config backup.
MANIFEST

cd "$BACKUP_DIR"
{
  find ./uploads -type f -print
  printf '%s\n' ./manifests/MANIFEST.txt
} | sort | xargs sha256sum > manifests/SHA256SUMS.txt

log "Validating SHA256 checksums..."
sha256sum -c manifests/SHA256SUMS.txt >> "$LOG_FILE"

log "Applying retention: deleting weekly_uploads_* older than ${RETENTION_DAYS} days"
find "$BACKUP_ROOT" -maxdepth 1 -type d -name "weekly_uploads_*" -mtime +"$RETENTION_DAYS" -print -exec rm -rf {} \; >> "$LOG_FILE" 2>&1 || true

log "Final backup files:"
find "$BACKUP_DIR" -type f -printf "%TY-%Tm-%Td %TH:%TM %s %p\n" | sort >> "$LOG_FILE"

log "DRYCURED weekly uploads backup v0.0.1 finished: PASS"

echo "BACKUP_DIR=$BACKUP_DIR"
echo "LOG_FILE=$LOG_FILE"
