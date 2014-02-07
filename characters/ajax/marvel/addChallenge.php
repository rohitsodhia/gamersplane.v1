<?
	if (checkLogin(0)) {
		includeSystemInfo('marvel');

		$userID = $_SESSION['userID'];
		$characterID = intval($_POST['characterID']);
		if (allowCharEdit($characterID, $userID)) {
			$name = preg_replace('/\s+/', ' ', $_POST['challengeName']);
			$stones = intval($_POST['stones']);
			$count = $mysql->query('SELECT COUNT(challengeID) FROM marvel_challenges WHERE characterID = '.$characterID);
			$count = $count->fetchColumn();
			$count++;
			$addChallenge = $mysql->prepare("INSERT INTO marvel_challenges (characterID, challengeID, challenge, stones) VALUES (:characterID, :challengeID, :name, :stones)");
			$addChallenge->execute(array(':characterID' => $characterID, ':challengeID' => $count, ':name' => $name, ':stones' => $stones));
			$challengeInfo = array('challengeID' => $mysql->lastInsertId, 'challenge' => $name, 'stones' => $stones);
			if ($addChallenge->rowCount()) challengeFormFormat($challengeInfo);
		}
	}
?>