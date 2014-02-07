<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$featID = intval($_POST['featID']);
			$notes = sanitizeString($_POST['notes']);
			$updateFeat = $mysql->prepare("UPDATE spycraft_feats SET notes = :notes WHERE characterID = $characterID AND featID = $featID");
			$updateFeat->execute(array(':notes' => $notes));
			if ($updateFeat->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>