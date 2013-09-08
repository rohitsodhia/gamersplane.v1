<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$featID = intval($_POST['featID']);
			$removeFeat = $mysql->query("DELETE FROM spycraft_feats WHERE characterID = $characterID AND featID = $featID");
			if ($removeFeat->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>