<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$featID = intval($_POST['featID']);
			$notes = sanitizeString($_POST['notes']);
			$updateFeat = $mysql->prepare("UPDATE pathfinder_feats SET notes = :notes WHERE characterID = $characterID AND featID = $featID");
			$updateFeat->execute(array(':notes' => $notes));
			if ($updateFeat->rowCount()) echo 1;
			else echo -1;
		} else echo 0;
	}
?>