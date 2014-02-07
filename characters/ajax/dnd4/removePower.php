<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$powerID = intval($_POST['powerID']);
			$removePower = $mysql->query("DELETE FROM dnd4_powers WHERE characterID = $characterID AND powerID = $powerID");
			if ($removePower->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>