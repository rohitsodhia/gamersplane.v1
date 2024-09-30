<?php
	if (isset($_POST['pendingAction'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		$pendingAction = $_POST['pendingAction'] == 'approve' ? 'approve' : 'reject';

		$getGame = $mysql->query("SELECT games.groupID FROM games INNER JOIN players gmCheck ON games.gameID = gmCheck.gameID INNER JOIN players playerCheck ON games.gameID = playerCheck.gameID WHERE games.gameID = {$gameID} AND gmCheck.userID = {$currentUser->userID} AND gmCheck.isGM = 1 AND playerCheck.userID = {$playerID} LIMIT 1");

		if (!$getGame->rowCount()) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'errors' => ['noPlayer']]);
			} else {
				header("Location: /games/{$gameID}/?approveError=1");
			}
		} else {
			$groupID = $getGame->fetchColumn();
			if ($pendingAction == 'approve') {
				$mysql->query("UPDATE players SET approved = 1 WHERE gameID = {$gameID} AND userID = {$playerID} LIMIT 1");
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$playerID}");
			} else {
				$mysql->query("DELETE FROM players WHERE gameID = {$gameID} AND userID = {$playerID} LIMIT 1");
			}

			if (isset($_POST['modal'])) {
				displayJSON(['success' => true, 'action' => $pendingAction, 'userID' => $playerID]);
			} else {
				header("Location: /games/{$gameID}/");
			}
		}
	} else {
		if (isset($_POST['modal'])) {
				displayJSON(['failed' => true]);
		} else {
			header('Location: /games/' . ($gameID ? $gameID . '/' : ''));
		}
	}
?>
