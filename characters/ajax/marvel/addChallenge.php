<?
	if (checkLogin(0)) {
		includeSystemInfo('marvel');

		$userID = intval($_SESSION['userID']);
		$characterID = intval($_POST['characterID']);
		$charCheck = $mysql->query("SELECT characterID FROM characters WHERE characterID = $characterID AND userID = $userID");
		if ($charCheck->rowCount()) {
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