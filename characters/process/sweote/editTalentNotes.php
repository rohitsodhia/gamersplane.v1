<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$talentID = intval($_POST['talentID']);
			$notes = sanitizeString($_POST['notes']);
			$updateFeat = $mysql->prepare("UPDATE sweote_talents SET notes = :notes WHERE characterID = $characterID AND talentID = $talentID");
			$updateFeat->execute(array(':notes' => $notes));
			if ($updateFeat->rowCount()) echo 1;
			else echo -1;
		} else echo 0;
	}
?>