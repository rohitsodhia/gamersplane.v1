export $(grep -v '^#' .env | xargs -d '\n')
filename=$(date +'%Y%m%d_%H%M%S')

docker-compose exec -T mysql bash -c "mysqldump --user=root --password='$MYSQL_ROOT_PASSWORD' --default-character-set=utf8mb4 --ignore-table=$MYSQL_DATABASE.forums_readData_forums_c --ignore-table=$MYSQL_DATABASE.forums_readData_newPosts $MYSQL_DATABASE > /tmp/$filename.sql"
mysql_container=$(docker ps | grep -E 'mysql' | awk '{ print $1 }')
docker cp $mysql_container:/tmp/$filename.sql $BACKUP_DIR/mysql/
zstd $BACKUP_DIR/mysql/$filename.sql
rm $BACKUP_DIR/mysql/$filename.sql
docker-compose exec -T mysql rm /tmp/$filename.sql
find $BACKUP_DIR/mysql/* -mtime +30 -exec rm {} \;
