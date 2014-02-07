<?
	if (checkLogin(0)) {
		includeSystemInfo('spycraft2');
		
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$focusID = intval($_POST['focusID']);
			$removeFocus = $mysql->query("DELETE FROM spycraft2_focuses WHERE characterID = $characterID AND focusID = $focusID");
			if ($removeFocus->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>