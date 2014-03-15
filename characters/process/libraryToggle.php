<?
	checkLogin(0);
	
	$userID = intval($_SESSION['userID']);
	$characterID = intval($_POST['characterID']);
	$charAllowed = $mysql->query("SELECT userID FROM characters WHERE characterID = $characterID AND userID = $userID");
	if ($charAllowed->rowCount()) {
		$mysql->query("INSERT INTO characterLibrary SET characterID = $characterID ON DUPLICATE KEY UPDATE inLibrary = inLibrary ^ 1");
		echo 1;
	} else echo 0;
?>