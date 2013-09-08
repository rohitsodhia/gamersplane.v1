<!DOCTYPE html>
<html>
<head>
</head>

<body>
<?php
	require_once('includes/requires.php');
	
$result = mysql_query("SELECT DISTINCT table_name, constraint_name FROM information_schema.key_column_usage WHERE constraint_schema = 'gamersplane' AND referenced_table_name IS NOT NULL");
while($row = mysql_fetch_assoc($result)) {
  mysql_query("ALTER TABLE `$row[table_name]` DROP FOREIGN KEY `$row[constraint_name]`");
}

	$mysql->query('SHOW TABLES', 'tables');
	while ($info = $mysql->getRow('tables')) {
		$table = $info[0];
		if (in_array(strtolower($table), array('forums_depths', 'forums_depths_old', 'forums_permissions', 'threads_relposts'))) continue;
		echo "$table<br>";
		$mysql->query("ALTER TABLE $table ENGINE = MyISAM");
		echo "<br><br>";
//		echo("ALTER TABLE $table ENGINE = MyISAM<br><br>");
	}
?>
</body></html>