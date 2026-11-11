#!/usr/bin/env bash
# Prunes cronolog output under logs/proxy/gamersplane/YYYY/MM/DD/.
# access.log is kept for 1 month, error.log for 3 months.
# Usage: prune-logs.sh [--dry-run]
set -euo pipefail

LOG_ROOT=${LOG_ROOT:-"$(cd "$(dirname "$0")" && pwd)/logs/proxy/gamersplane"}
ACCESS_CUTOFF=$(date -d '1 month ago' +%Y%m%d)
ERROR_CUTOFF=$(date -d '3 months ago' +%Y%m%d)
DRY_RUN=0
[ "${1:-}" = "--dry-run" ] && DRY_RUN=1

remove() {
  echo "removing $1"
  [ "$DRY_RUN" -eq 1 ] || rm -f -- "$1"
}

for day in "$LOG_ROOT"/*/*/*/; do
  [ -d "$day" ] || continue
  day=${day%/}
  d=${day#"$LOG_ROOT"/}   # YYYY/MM/DD
  stamp=${d//\//}
  [[ "$stamp" =~ ^[0-9]{8}$ ]] || continue

  [[ "$stamp" < "$ACCESS_CUTOFF" ]] && [ -f "$day/access.log" ] && remove "$day/access.log"
  [[ "$stamp" < "$ERROR_CUTOFF" ]] && [ -f "$day/error.log" ] && remove "$day/error.log"
done

# Drop day/month/year directories left empty
[ "$DRY_RUN" -eq 1 ] || find "$LOG_ROOT" -mindepth 1 -type d -empty -delete
