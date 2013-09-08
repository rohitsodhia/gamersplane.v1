<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$featID = intval($_POST['featID']);
			$notes = sanatizeString($_POST['notes']);
			$updateFeat = $mysql->query("UPDATE dnd4_feats SET notes = '$notes' WHERE characterID = $characterID AND featID = $featID");
			if ($updateFeat->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>