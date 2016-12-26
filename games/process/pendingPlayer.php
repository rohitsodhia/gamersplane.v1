<?php
	if (isset($_POST['pendingAction'])) {
		$gameID = intval($_POST['gameID']);
		$playerID = intval($_POST['playerID']);
		$pendingAction = $_POST['pendingAction'] == 'approve' ? 'approve' : 'reject';

		$groupID = $mongo->games->findOne(
			[
				'gameID' => $gameID,
				'players' => ['$elemMatch' => [
					'user.userID' => $currentUser->userID,
					'isGM' => true
				]]
			],
			['projection' => ['groupID' => true]]
		);

		if (!$groupID) {
			if (isset($_POST['modal'])) {
				displayJSON(['failed' => true, 'errors' => ['noPlayer']]);
			} else {
				header("Location: /games/{$gameID}/?approveError=1");
			}
		} else {
			$groupID = $groupID['groupID'];
			if ($pendingAction == 'approve') {
				$mongo->games->updateOne(
					[
						'gameID' => $gameID,
						'players' => ['$elemMatch' => [
							'user.userID' => $playerID
						]]
					],
					['$set' => ['players.$.approved' => true]]
				);
				$mysql->query("INSERT INTO forums_groupMemberships SET groupID = {$groupID}, userID = {$playerID}");
			} else {
				$mongo->games->updateOne(
					['gameID' => $gameID],
					['$pull' => ['players' => ['user.userID' => $playerID]]]
				);
			}
#			$hl_playerApplied = new HistoryLogger($pendingAction == 'approve'?'playerApproved':'playerRejected');
#			$hl_playerApplied->addUser($playerID)->addUser($currentUser->userID, 'gm')->addGame($gameID)->save();

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
