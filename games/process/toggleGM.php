<?php
	if (isset($_POST['toggle'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);

		$getGame = $mysql->query("SELECT playerCheck.isGM, games.forumID FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID INNER JOIN players playerCheck ON gamers.gameID = playerCheck.gameID WHERE games.gameID = {$gameID} AND gmCheck.userID = {$currentUser->userID} AND gmCheck.isGM = 1 AND playerCheck.userID = {$playerID} LIMIT 1");

		if (!$gmCheck->rowCount()) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'errors' => ['invalidRequest']]);
			} else {
				header("Location: /games/{$gameID}/");
			}
		} else {
			list($isGM, $forumID) = $getGame->fetch(PDO::FETCH_NUM);
			$mysql->query("UPDATE players SET isGM = NOT isGM WHERE gameID = {$gameID} AND playerID = {$playerID} LIMIT 1");
			if ($isGM) {
				$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID = {$forumID}");
			} else {
				$mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES ({$playerID}, {$forumID})");
			}

			if (isset($_POST['modal'])) {
				displayJSON(['success' => true, 'userID' => $playerID]);
			} else {
				header("Location: /games/{$gameID}/?gmAdded=1");
			}
		}
	} else {
		if (isset($_POST['modal'])) {
			displayJSON(['failed' => true]);
		} else {
			header('Location: /games/');
		}
	}
?>
