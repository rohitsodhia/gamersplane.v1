<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft2');
		
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$focusID = intval($_POST['focusID']);
			$removeFocus = $mysql->query("DELETE FROM spycraft2_focuses WHERE characterID = $characterID AND focusID = $focusID");
			if ($removeFocus->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>