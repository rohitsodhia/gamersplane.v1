<?
	if (isset($_POST['pendingAction'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		$pendingAction = $_POST['pendingAction'] == 'approve'?'approve':'reject';

		$groupID = $mongo->games->findOne(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $currentUser->userID, 'isGM' => true))), array('groupID' => true));
		
		if (!$groupID) {
			if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true, 'errors' => ['noPlayer']));
			else 
				header("Location: /games/{$gameID}/?approveError=1");
		} else {
			$groupID = $groupID['groupID'];
			if ($pendingAction == 'approve') {
				$mongo->games->update(array('gameID' => $gameID, 'players' => array('$elemMatch' => array('user.userID' => $playerID))), array('$set' => array('players.$.approved' => true)));
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$playerID}");
			} else {
				$mongo->games->update(array('gameID' => $gameID), array('$pull' => array('players' => array('user.userID' => $playerID))));
			}
#			$hl_playerApplied = new HistoryLogger($pendingAction == 'approve'?'playerApproved':'playerRejected');
#			$hl_playerApplied->addUser($playerID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();
			
			if (isset($_POST['modal'])) 
				displayJSON(array('success' => true, 'action' => $pendingAction, 'userID' => $playerID));
			else 
				header("Location: /games/{$gameID}/");
		}
	} else {
		if (isset($_POST['modal'])) 
				displayJSON(array('failed' => true));
		else 
			header('Location: /games/'.($gameID?$gameID.'/':''));
	}
?>