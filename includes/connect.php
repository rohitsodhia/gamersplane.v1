<?php
	$dbHostname = getenv('MYSQL_HOST');
	$dbUsername = getenv('MYSQL_USERNAME');
	$dbPassword = getenv('MYSQL_PASSWORD');
	$dbName     = getenv('MYSQL_DATABASE');
	$dbPort     = getenv('MYSQL_PORT');

	$mysql = new PDO("mysql:host=$dbHostname;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUsername, $dbPassword);
	$mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$mysql->query('SET time_zone="GMT"');
	$mysql->query("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");

	class DB
	{
		public static $connections = [];

		public static function addConnection($label, $connection)
		{
			self::$connections[$label] = $connection;
		}

		public static function conn($label)
		{
			return self::$connections[$label];
		}
	}

	DB::addConnection('mysql', $mysql);
?>
