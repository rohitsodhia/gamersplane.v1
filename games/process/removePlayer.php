<?
	if (isset($_POST['remove']) || isset($_POST['leave'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);

		$game = $mongo->games->findOne(array('gameID' => $gameID), array('forumID' => true, 'players' => true));
		$gmCheck = false;
		$playerCheck = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $playerID) 
				$playerCheck = true;
			elseif ($player['user']['userID'] == $currentUser->userID && $player['isGM']) 
				$gmCheck = true;
		}

		if ($playerCheck->rowCount() == 0) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => array('noPlayer')));
			else 
				header('Location: /403');
		} elseif ($gmCheck || $playerCheck) {
			$forumID = $game['forumID'];
			$forums = $mysql->query('SELECT forumID FROM forums WHERE heritage LIKE "'.sql_forumIDPad(2).'-'.sql_forumIDPad($forumID).'%"');
			$forumIDs = array();
			foreach ($forums as $info) 
				$forumIDs[] = $info['forumID'];
			$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM forums_permissions_users WHERE userID = {$playerID} AND forumID IN (".implode(', ', $forumIDs).")");
			$mysql->query("DELETE FROM gm USING forums_groupMemberships gm INNER JOIN forums_permissions_groups p WHERE gm.userID = {$playerID} AND gm.groupID = p.groupID AND p.forumID IN (".implode(', ', $forumIDs).")");
			$chars = $mysql->query("SELECT characterID FROM characters WHERE gameID = {$gameID} AND userID = {$playerID}")->fetchAll(PDO::FETCH_COLUMN);
#			$hl_removePlayer = new HistoryLogger(isset($_POST['remove'])?'playerRemove':'playerLeft');
#			$hl_removePlayer->addUser($playerID)->addGame($gameID)->addUser($currentUser->userID, 'gm')->addForCharacters($chars)->save();
			$mysql->query("UPDATE characters SET gameID = NULL, approved = 0 WHERE gameID = {$gameID} AND userID = {$playerID}");
			$mysql->query("DELETE FROM dp USING deckPermissions dp INNER JOIN decks d WHERE d.deckID = dp.deckID AND gameID = {$gameID} AND dp.userID = {$playerID}");
			$mongo->games->update(array('gameID' => $gameID), array('$pull' => array('player.user.userID' => $playerID)));
			
			if (isset($_POST['remove'])) {
				if (isset($_POST['modal'])) 
					displayJSON(array('success' => true, 'action' => 'removed', 'userID' => $playerID ));
				else 
					header('Location: /games/'.$gameID.'/?removed=1');
			} elseif (isset($_POST['leave'])) {
				if (isset($_POST['modal'])) 
					displayJSON(array('success' => true, 'action' => 'left', 'userID' => $playerID));
				else 
					header('Location: /games/my/');
			}
		} else {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => array('invalidPlayer')));
			else 
				header("Location: /games/{$gameID}/?notInGame=1");
		}
	} else {
		if (isset($_POST['modal'])) 
			displayJSON(array('failed' => true));
		else 
			header('Location: /games/');
	}
?>