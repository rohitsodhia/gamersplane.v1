<?php
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);

		$game = $mongo->games->findOne(
			['gameID' => $gameID],
			['projection' => ['forumID' => true, 'players' => true, 'decks' => true]]
		);
		$gmCheck = false;
		$playerCheck = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $playerID) {
				$playerCheck = true;
			} elseif ($player['user']['userID'] == $currentUser->userID && $player['isGM']) {
				$gmCheck = true;
			}
		}

		if (!$playerCheck) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'errors' => ['noPlayer']]);
			} else {
				header('Location: /403');
			}
		} elseif ($gmCheck || $playerCheck) {
			$forumID = $game['forumID'];
			$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "' . sql_forumIDPad(2) . '-' . sql_forumIDPad($forumID) . '%"');
			$forumIDs = [];
			foreach ($forums as $info) {
				$forumIDs[] = $info['forumID'];
			}
			$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID IN (" . implode(', ', $forumIDs) . ")");
			$mysql->query("DELETE FROM forums_permissions_users WHERE userID = {$playerID} AND forumID IN (" . implode(', ', $forumIDs) . ")");
			$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = {$playerID} AND gm.groupID = p.groupID AND p.forumID IN (" . implode(', ', $forumIDs) . ")");
#			$hl_removePlayer = new HistoryLogger(isset($_POST['remove'])?'playerRemove':'playerLeft');
#			$hl_removePlayer->addUser($playerID)->addGame($gameID)->addUser($currentUser->userID, 'gm')->addForCharacters($chars)->save();
			$decks = $game['decks'];
			foreach ($decks as &$deck) {
				if (in_array($playerID, $deck['permissions'])) {
					unset($deck['permissions'][array_search($playerID, $deck['permissions'])]);
				}
			}
			$mongo->games->updateOne(
				['gameID' => $gameID],
				[
					'$pull' => ['players' => ['user.userID' => $playerID]],
					'$set' => ['decks' => $decks]
				]
			);
			$mongo->characters->updateOne(
				['user.userID' => $playerID, 'game.gameID' => $gameID],
				['$set' => ['game' => null]]
			);

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
	} else {
		if (isset($_POST['modal'])) {
			displayJSON(['failed' => true]);
		} else {
			header('Location: /games/');
		}
	}
?>
