#!/bin/bash
set -euo pipefail
IFS=$'\n\t'

# Load environment
set -a
source .env
set +a

# Constants
filename=$(date +'%Y%m%d_%H%M%S')
volume_name=gpv1_db_backup
backup_container_name=pxb
db_container=gpv1-mysql

# Check required commands
for cmd in docker jq rsync tar xargs grep; do
    command -v "$cmd" >/dev/null 2>&1 || { echo >&2 "Error: $cmd is not installed."; exit 1; }
done

# Logging
log() {
    echo "[$(date +'%F %T')] $*"
}

# Cleanup
cleanup() {
    log "Cleaning up..."
    docker volume rm "$volume_name" > /dev/null 2>&1 || true
    rm -rf "$BACKUP_DIR/tmp"
}
trap cleanup EXIT

log "Removing old volume (if exists)..."
docker volume rm "$volume_name" > /dev/null 2>&1 || true

log "Starting XtraBackup container..."
docker run --rm --name "$backup_container_name" \
    --volumes-from "$db_container" \
    -v "$volume_name":/backup_84 \
    --network=container:"$db_container" \
    -it --user root \
    percona/percona-xtrabackup:8.4 \
    /bin/bash -c "
        set -e
        xtrabackup --backup --datadir=/var/lib/mysql \
                   --target-dir=/backup_84 --host=$db_container \
                   --port=3306 --user=root --password=$MYSQL_ROOT_PASSWORD
        xtrabackup --prepare --target-dir=/backup_84
    "

log "Inspecting Docker volume..."
backup_path=$(docker volume inspect "$volume_name" | jq -r ".[0].Mountpoint")

log "Syncing backup data..."
mkdir -p "$BACKUP_DIR/tmp"
rsync -a "$backup_path/" "$BACKUP_DIR/tmp/"

log "Creating compressed archive..."
tar --zstd -C "$BACKUP_DIR/tmp" -cf "$filename.tar.zst" .

log "Moving archive to backup directory..."
mv "$filename.tar.zst" "$BACKUP_DIR"

log "Removing backups older than 30 days..."
find "$BACKUP_DIR" -type f -name '*.tar.zst' -mtime +30 -exec rm -f {} \;

log "Backup completed successfully."
