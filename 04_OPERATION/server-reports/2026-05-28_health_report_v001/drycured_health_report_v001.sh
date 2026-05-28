#!/usr/bin/env bash
set -euo pipefail

echo "=== DRYCURED HEALTH REPORT v0.0.1 — READ ONLY ==="
date -Is
echo

echo "=== HOST ==="
hostname
uptime
echo

echo "=== DISK SPACE ==="
df -h
echo

echo "=== MEMORY ==="
free -h
echo

echo "=== GIT STATUS ==="
cd /root/DRYCURED_GITHUB
git status --short
echo

echo "=== LAST COMMITS ==="
git log --oneline -n 12
echo

echo "=== BACKUP TIMER ==="
systemctl is-enabled drycured-daily-db-config-backup.timer || true
systemctl is-active drycured-daily-db-config-backup.timer || true
systemctl list-timers --all --no-pager | grep -E "NEXT|drycured-daily-db-config-backup" || true
echo

echo "=== BACKUP MONITOR ==="
/root/drycured_ops/drycured_backup_monitor.sh || true
echo

echo "=== LATEST DAILY DB CONFIG BACKUPS ==="
find /root/drycured_production_backups -maxdepth 1 -type d -name "daily_db_config_*" -printf "%TY-%Tm-%Td %TH:%TM %p\n" 2>/dev/null | sort | tail -n 10
echo

echo "=== LATEST WEEKLY UPLOADS BACKUPS ==="
find /root/drycured_production_backups -maxdepth 1 -type d -name "weekly_uploads_*" -printf "%TY-%Tm-%Td %TH:%TM %p\n" 2>/dev/null | sort | tail -n 10
echo

echo "=== BACKUP ROOT SIZE ==="
du -sh /root/drycured_production_backups 2>/dev/null || true
du -h --max-depth=1 /root/drycured_production_backups 2>/dev/null | sort -h | tail -n 30 || true
echo

echo "=== NGINX CONFIG TEST ==="
nginx -t
echo

echo "=== PHP / WORDPRESS ==="
php -v | head -n 3 || true
cd /var/www/html
wp core version --allow-root || true
wp plugin list --status=active --fields=name,version,status --allow-root || true
echo

echo "=== LIVE URL HEALTH ==="
for URL in \
"https://drycured.com/" \
"https://drycured.com/sitemap.xml" \
"https://drycured.com/robots.txt" \
"https://drycured.com/recepti/" \
"https://drycured.com/alati/" \
"https://drycured.com/wp-json/" \
"https://drycured.com/wp-login.php"
do
  echo "---- $URL ----"
  curl -sS -o /dev/null -D - "$URL" | sed -n '1,22p'
  echo
done

echo "=== PROTECTED ROUTES CHECK ==="
for URL in \
"https://drycured.com/xmlrpc.php" \
"https://drycured.com//xmlrpc.php" \
"https://drycured.com/wp-content/ai1wm-backups/" \
"https://drycured.com/wp-content/backups-dup-lite/" \
"https://drycured.com/wp-content/litespeed/auto-backup/" \
"https://drycured.com/wp-content/uploads/drycured/backups/" \
"https://drycured.com/projekti/projekt_drycured.com_struktura.zip" \
"https://drycured.com/swabtest/swabtools-homepage-techtemplate.zip"
do
  echo "---- $URL ----"
  curl -sS -o /dev/null -D - "$URL" | sed -n '1,18p'
  echo
done

echo "=== SECURITY HEADER SAMPLE — HOME ==="
curl -sS -o /dev/null -D - https://drycured.com/ | grep -iE "strict-transport-security|content-security-policy|x-content-type-options|referrer-policy|x-frame-options|permissions-policy" || true
echo

echo "=== MAIL ERROR LOG CHECK ==="
MAIL_LOG="/var/www/html/wp-content/uploads/drycured/mail/mail-errors.log"
if [ -f "$MAIL_LOG" ]; then
  ls -lh "$MAIL_LOG"
  tail -n 40 "$MAIL_LOG"
else
  echo "mail error log not found"
fi
echo

echo "=== FINAL HEALTH REPORT COMPLETE ==="
