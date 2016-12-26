<?
	$dbHostname = 'localhost';
	$dbUsername = 'gamersplane';
	$dbPassword = 'Ep2NXZ0Atv6MThNtsa2h';
	$dbName     = 'gamersplane';

	$mysql = new PDO("mysql:host=$dbHostname;dbname=$dbName", $dbUsername, $dbPassword);
	$mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$mysql->query('SET time_zone="GMT"');

	$mongo = new MongoDB\Client('mongodb://localhost:27017');
	$mongo = $mongo->gamersplane;

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
	DB::addConnection('mongo', $mongo);
?>
