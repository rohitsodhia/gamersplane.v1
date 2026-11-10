#!/usr/bin/env bash
# Rotates the postfix log (via postfix itself) and deletes rotated logs older than 1 year.
# Run daily from cron. Rotated files land next to the log as postfix.log.<timestamp>.
# Usage: rotate-postfix-logs.sh [--dry-run]
set -euo pipefail

LOG_ROOT=${LOG_ROOT:-"$(cd "$(dirname "$0")" && pwd)/logs/postfix"}
KEEP_DAYS=${KEEP_DAYS:-365}
DRY_RUN=0
[ "${1:-}" = "--dry-run" ] && DRY_RUN=1

if [ "$DRY_RUN" -eq 1 ]; then
  echo "would run: docker exec gpv1-postfix postfix logrotate"
  find "$LOG_ROOT" -name 'postfix.log.*' -mtime +"$KEEP_DAYS" -print | sed 's/^/would remove /'
  exit 0
fi

docker exec gpv1-postfix postfix logrotate
find "$LOG_ROOT" -name 'postfix.log.*' -mtime +"$KEEP_DAYS" -print -delete
