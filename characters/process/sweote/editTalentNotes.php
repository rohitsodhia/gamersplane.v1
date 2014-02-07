<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$talentID = intval($_POST['talentID']);
			$notes = sanitizeString($_POST['notes']);
			$updateFeat = $mysql->prepare("UPDATE sweote_talents SET notes = :notes WHERE characterID = $characterID AND talentID = $talentID");
			$updateFeat->execute(array(':notes' => $notes));
			if ($updateFeat->rowCount()) echo 1;
			else echo -1;
		} else echo 0;
	}
?>