<?
	if ($_SERVER['SERVER_NAME'] == 'localhost') $dbHostname = 'localhost';
	else $dbHostname = 'mysql.gamersplane.com';
	$dbUsername = 'gamersplane';
	$dbPassword = 'Ep2NXZ0Atv6MThNtsa2h';
	$dbName     = 'gamersplane';
	
	$mysql = new PDO("mysql:host=$dbHostname;dbname=$dbName", $dbUsername, $dbPassword);
	$mysql->query('SET time_zone="GMT"');
?>