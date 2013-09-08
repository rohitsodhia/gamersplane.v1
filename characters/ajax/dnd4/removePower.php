<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$power = sanatizeString($_POST['power']);
			$removePower = $mysql->query("DELETE FROM dnd4_powers WHERE characterID = $characterID AND name = '$power'");
			if ($removePower->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>