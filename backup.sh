#!/usr/bin/env bash
set -euo pipefail

export $(grep -v '^#' .env | xargs -d '\n')
filename=$(date +'%Y%m%d_%H%M%S')

mysqldump \
	--host="$MYSQL_HOST" \
	--port="$MYSQL_PORT" \
	--user="$MYSQL_USERNAME" \
	--password="$MYSQL_PASSWORD" \
	--ssl-mode=REQUIRED \
	--single-transaction \
	--set-gtid-purged=OFF \
	--no-tablespaces \
	--default-character-set=utf8mb4 \
	--ignore-table="$MYSQL_DATABASE.forums_readData_forums_c" \
	--ignore-table="$MYSQL_DATABASE.forums_readData_newPosts" \
	"$MYSQL_DATABASE" \
	| sed 's/\sDEFINER=`[^`]*`@`[^`]*`//g' \
	| zstd -q -o "$BACKUP_DIR/mysql/$filename.sql.zst"

# rclone copy "$BACKUP_DIR/mysql/$filename.sql.zst" b2:your-bucket/mysql/

find "$BACKUP_DIR/mysql" -name '*.sql.zst' -mtime +30 -delete
