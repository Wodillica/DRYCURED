#!/usr/bin/env bash
set -euo pipefail

BACKUP_ROOT="/root/drycured_production_backups"
TIMER="drycured-daily-db-config-backup.timer"
SERVICE="drycured-daily-db-config-backup.service"
MAX_AGE_HOURS=36

STATUS="PASS"

warn() {
  echo "WARNING: $*"
  if [ "$STATUS" != "FAIL" ]; then
    STATUS="WARNING"
  fi
}

fail() {
  echo "FAIL: $*"
  STATUS="FAIL"
}

echo "=== DRYCURED BACKUP MONITOR v0.0.2 ==="
date -Is
echo

echo "=== SYSTEMD TIMER STATUS ==="
TIMER_ENABLED=$(systemctl is-enabled "$TIMER" 2>/dev/null || true)
TIMER_ACTIVE=$(systemctl is-active "$TIMER" 2>/dev/null || true)

echo "timer_enabled=$TIMER_ENABLED"
echo "timer_active=$TIMER_ACTIVE"

if [ "$TIMER_ENABLED" != "enabled" ]; then
  fail "Timer is not enabled."
fi

if [ "$TIMER_ACTIVE" != "active" ]; then
  fail "Timer is not active."
fi

echo
echo "=== NEXT TIMER RUN ==="
systemctl list-timers --all --no-pager | grep -E "NEXT|$TIMER" || warn "Timer not visible in list-timers."

echo
echo "=== LAST SERVICE RESULT ==="
SERVICE_ACTIVE_STATE=$(systemctl show "$SERVICE" -p ActiveState --value 2>/dev/null || true)
SERVICE_SUB_STATE=$(systemctl show "$SERVICE" -p SubState --value 2>/dev/null || true)
SERVICE_RESULT=$(systemctl show "$SERVICE" -p Result --value 2>/dev/null || true)
SERVICE_EXEC_STATUS=$(systemctl show "$SERVICE" -p ExecMainStatus --value 2>/dev/null || true)

echo "service_active_state=$SERVICE_ACTIVE_STATE"
echo "service_sub_state=$SERVICE_SUB_STATE"
echo "service_result=$SERVICE_RESULT"
echo "service_exec_main_status=$SERVICE_EXEC_STATUS"

# For oneshot services, inactive/dead after successful run is normal.
if [ "$SERVICE_RESULT" != "success" ] && [ "$SERVICE_RESULT" != "" ]; then
  fail "Last service result is not success."
fi

if [ "$SERVICE_EXEC_STATUS" != "0" ] && [ "$SERVICE_EXEC_STATUS" != "" ]; then
  fail "Last service ExecMainStatus is not 0."
fi

echo
echo "=== SERVICE STATUS PREVIEW ==="
systemctl status "$SERVICE" --no-pager | sed -n '1,80p' || true

echo
echo "=== LATEST BACKUP ==="
LATEST_BACKUP=$(find "$BACKUP_ROOT" -maxdepth 1 -type d -name "daily_db_config_*" | sort | tail -n 1 || true)

if [ -z "$LATEST_BACKUP" ]; then
  fail "No daily_db_config backup directory found."
else
  echo "latest_backup=$LATEST_BACKUP"
fi

echo
echo "=== LATEST BACKUP AGE ==="
if [ -n "${LATEST_BACKUP:-}" ]; then
  NOW_EPOCH=$(date +%s)
  BACKUP_EPOCH=$(stat -c %Y "$LATEST_BACKUP")
  AGE_SECONDS=$((NOW_EPOCH - BACKUP_EPOCH))
  AGE_HOURS=$((AGE_SECONDS / 3600))
  echo "age_hours=$AGE_HOURS"
  echo "max_age_hours=$MAX_AGE_HOURS"

  if [ "$AGE_HOURS" -gt "$MAX_AGE_HOURS" ]; then
    fail "Latest backup is older than ${MAX_AGE_HOURS} hours."
  fi
fi

echo
echo "=== LATEST BACKUP FILES ==="
if [ -n "${LATEST_BACKUP:-}" ]; then
  find "$LATEST_BACKUP" -type f -printf "%TY-%Tm-%Td %TH:%TM %s %p\n" | sort
fi

echo
echo "=== SHA256 VERIFY ==="
if [ -n "${LATEST_BACKUP:-}" ] && [ -f "$LATEST_BACKUP/manifests/SHA256SUMS.txt" ]; then
  (
    cd "$LATEST_BACKUP"
    sha256sum -c manifests/SHA256SUMS.txt
  ) || fail "SHA256 verification failed."
else
  fail "SHA256SUMS.txt not found."
fi

echo
echo "=== BACKUP STORAGE USAGE ==="
du -sh "$BACKUP_ROOT" 2>/dev/null || warn "Could not measure backup root."
du -sh "$BACKUP_ROOT"/daily_db_config_* 2>/dev/null | sort -h | tail -n 20 || true

echo
echo "=== DAILY BACKUP COUNT ==="
COUNT=$(find "$BACKUP_ROOT" -maxdepth 1 -type d -name "daily_db_config_*" | wc -l)
echo "daily_backup_count=$COUNT"

if [ "$COUNT" -gt 10 ]; then
  warn "More than 10 daily backup directories found. Retention may need review."
fi

echo
echo "=== FINAL STATUS ==="
echo "$STATUS"

if [ "$STATUS" = "FAIL" ]; then
  exit 2
elif [ "$STATUS" = "WARNING" ]; then
  exit 1
else
  exit 0
fi
