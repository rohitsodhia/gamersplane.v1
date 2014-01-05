<?
	if (checkLogin(0)) {
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = {$_SESSION['userID']}");
		if ($charCheck->rowCount()) {
			$talentID = intval($_POST['talentID']);
			$removeTalent = $mysql->query("DELETE FROM sweote_talents WHERE characterID = $characterID AND talentID = $talentID");
			if ($removeTalent->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>