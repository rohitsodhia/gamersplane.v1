<?
	if (checkLogin(0)) {
		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$skillID = intval($_POST['skillID']);
			$removeSkill = $mysql->query("DELETE FROM sweote_skills WHERE characterID = $characterID AND skillID = $skillID");
			if ($removeSkill->rowCount()) echo 1;
			else echo 0;
		} else echo 0;
	}
?>