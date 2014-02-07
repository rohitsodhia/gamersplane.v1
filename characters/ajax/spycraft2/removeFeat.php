<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$featID = intval($_POST['featID']);
			$removeFeat = $mysql->query("DELETE FROM spycraft2_feats WHERE characterID = $characterID AND featID = $featID");
			if ($removeFeat->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>