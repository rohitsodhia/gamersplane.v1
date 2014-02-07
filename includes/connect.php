<?
	require_once('database/Database.php');
	use Database\Database;

	if ($_SERVER['SERVER_NAME'] == 'gamersplane.local') $dbHostname = 'localhost';
	else $dbHostname = 'mysql.gamersplane.com';
	$dbUsername = 'gamersplane';
	$dbPassword = 'Ep2NXZ0Atv6MThNtsa2h';
	$dbName     = 'gamersplane';
	
	$mysql = new Database("mysql:host=$dbHostname;dbname=$dbName", $dbUsername, $dbPassword);
	$mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//	$mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$mysql->query('SET time_zone="GMT"');
?>