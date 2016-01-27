<?
	if (isset($_POST['toggle'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);

		$game = $mongo->games->findOne(array('gameID' => $gameID), array('forumID' => true, 'players' => true));
		$gmCheck = false;
		$playerCheck = false;
		foreach ($game['players'] as $player) {
			if ($player['user']['userID'] == $playerID && $player['isGM']) 
				$playerCheck = true;
			elseif ($player['user']['userID'] == $playerID) 
				$playerCheck = false;
			elseif ($player['user']['userID'] == $currentUser->userID && $player['isGM']) 
				$gmCheck = true;
		}
		if (!$gmCheck && $playerCheck == null) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => array('invalidPost')));
			else 
				header("Location: /games/{$gameID}/");
		} else {
			$isGM = $playerCheck;
			$mongo->games->update(array('gameID' => $gameID, 'players.user.userID' => $playerID), array('$set' => array('players.$.isGM' => !$isGM)));
			$forumID = $game['forumID'];
			if ($isGM) 
				$mysql->query("DELETE FROM forumAdmins WHERE userID = {$playerID} AND forumID = {$forumID}");
			else 
				$mysql->query("INSERT INTO forumAdmins (userID, forumID) VALUES ({$playerID}, {$forumID})");

#			$hl_toggleGM = new HistoryLogger($isGM?'gmRemoved':'gmAdded');
#			$hl_toggleGM->addUser($playerID)->addGame($gameID)->addUser($currentUser->userID, 'gm')->save();
			
			if (isset($_POST['modal'])) 
				displayJSON(array('success' => true, 'userID' => $playerID));
			else 
				header("Location: /games/{$gameID}/?gmAdded=1");
		}
	} else {
		if (isset($_POST['modal'])) 
			displayJSON(array('failed' => true));
		else 
			header('Location: /games/');
	}
?>