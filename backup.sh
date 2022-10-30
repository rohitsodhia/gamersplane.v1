export $(grep -v '^#' .env | xargs -d '\n')
filename=$(date +'%Y%m%d_%H%M%S')

docker-compose exec -T mysql bash -c "mysqldump --user=$MYSQL_USERNAME --password='$MYSQL_PASSWORD' --ignore-table=$MYSQL_DATABASE.forums_readData_forums_c --ignore-table=$MYSQL_DATABASE.forums_readData_newPosts $MYSQL_DATABASE | gzip > /tmp/$filename.gz"
mysql_container=$(docker ps | grep -E 'mysql' | awk '{ print $1 }')
docker cp $mysql_container:/tmp/$filename.gz $BACKUP_DIR/mysql/
docker-compose exec -T mysql rm /tmp/$filename.gz
find $BACKUP_DIR/mysql/* -mtime +30 -exec rm {} \;

docker-compose exec -T mongo mongodump --db gamersplane --out /tmp/$filename
mongo_container=$(docker ps | grep -E 'mongo' | awk '{ print $1 }')
docker cp $mongo_container:/tmp/$filename $BACKUP_DIR/mongo/
docker-compose exec -T mongo rm -r /tmp/$filename
tar -czvf $BACKUP_DIR/mongo/$filename.tar.gz $BACKUP_DIR/mongo/$filename
rm -rf $BACKUP_DIR/mongo/$filename
find $BACKUP_DIR/mongo/* -mtime +30 -exec rm -r {} \;
