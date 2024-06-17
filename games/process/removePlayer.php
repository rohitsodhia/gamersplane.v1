<?php
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);

		try {
			$forumID = $mysql->query("SELECT games.forumID, players.isGM FROM games INNER JOIN players ON games.gameID = players.gameID WHERE games.gameID = {$gameID} AND players.userID = {$currentUser->userID} LIMIT 1")->fetchColumn();
		} catch (Exception $e) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true]);
			} else {
				header('Location: /games/');
			}
		}
		try {
			$mysql->query("SELECT userID FROM players WHERE userID = {$playerID} AND gameID = {$gameID} LIMIT 1");
		} catch (Exception $e) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'errors' => ['noPlayer']]);
			} else {
				header('Location: /403');
			}
		}

		$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "' . sql_forumIDPad(2) . '-' . sql_forumIDPad($forumID) . '%"');
		$forumIDs = [];
		foreach ($forums as $info) {
			$forumIDs[] = $info['forumID'];
		}
		$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID IN (" . implode(', ', $forumIDs) . ")");
		$mysql->query("DELETE FROM forums_permissions_users WHERE userID = {$playerID} AND forumID IN (" . implode(', ', $forumIDs) . ")");
		$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = {$playerID} AND gm.groupID = p.groupID AND p.forumID IN (" . implode(', ', $forumIDs) . ")");
		// $hl_removePlayer = new HistoryLogger(isset($_POST['remove'])?'playerRemove':'playerLeft');
		// $hl_removePlayer->addUser($playerID)->addGame($gameID)->addUser($currentUser->userID, 'gm')->addForCharacters($chars)->save();
		$mysql->query("DELETE deckPermissions FROM decks INNER JOIN deckPermissions ON decks.deckID = deckPermissions.deckID WHERE deckPermissions.userID = {$playerID}")->fetchAll();
		$mysql->query("DELETE FROM players WHERE gameID = {$gameID} AND playerID = {$playerID} LIMIT 1");
		$mysql->query("UPDATE characters SET gameID = NULL WHERE userID = {$playerID} AND gameID = {$gameID}");

		if (isset($_POST['remove'])) {
			if (isset($_POST['modal'])) {
				displayJSON(['success' => true, 'action' => 'playerRemoved', 'userID' => $playerID ]);
			} else {
				header('Location: /games/'.$gameID.'/?removed=1');
			}
		} elseif (isset($_POST['leave'])) {
			if (isset($_POST['modal'])) {
				displayJSON(['success' => true, 'action' => 'left', 'userID' => $playerID]);
			} else {
				header('Location: /games/my/');
			}
		}
	} else {
		if (isset($_POST['modal'])) {
			displayJSON(['failed' => true, 'errors' => ['invalidPlayer']]);
		} else {
			header("Location: /games/{$gameID}/?notInGame=1");
		}
	}
?>
