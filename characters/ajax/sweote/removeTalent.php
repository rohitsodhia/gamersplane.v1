<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$talentID = intval($_POST['talentID']);
			$removeTalent = $mysql->query("DELETE FROM sweote_talents WHERE characterID = $characterID AND talentID = $talentID");
			if ($removeTalent->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>