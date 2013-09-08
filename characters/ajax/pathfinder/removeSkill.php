<?
	if (checkLogin(0)) {
		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
			$skillID = intval($_POST['skillID']);
			$removeSkill = $mysql->query("DELETE FROM pathfinder_skills WHERE characterID = $characterID AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>