<?
	addPackage('forum');
	$threadID = intval($_POST['threadID']);
	$forumID = $mysql->query("SELECT forumID FROM threads WHERE threadID = {$threadID}");
	$forumID = $forumID->fetchColumn();
	$permissions = $permissions = ForumPermissions::getPermissions($currentUser->userID, $forumID, 'write');

	if ($permissions[$forumID]['write']) {
		try {
			$poll = new ForumPoll($threadID);
		} catch (Exception $e) { header("Location: /forums/thread/{$threadID}"); exit; }
		$threadManager->getPoll();
		if ($pollInfo->rowCount()) {
			$pollInfo = $pollInfo->fetch();
			$checkVoted = $mysql->query("SELECT pv.pollOptionID FROM forums_pollVotes pv, forums_pollOptions po WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID AND pv.userID = {$currentUser->userID}");
			if ($checkVoted->rowCount() > 0 && !$pollInfo['allowRevoting']) {
			} elseif ($checkVoted->rowCount() == 0 || $pollInfo['allowRevoting']) {
				$pollOptions = $mysql->query("SELECT pollOptionID FROM forums_pollOptions WHERE threadID = $threadID");
				$validOptions = array();
				foreach ($pollOptions as $optionInfo) $validOptions[] = $optionInfo['pollOptionID'];

				$validVotes = '';
				if (is_array($_POST['votes']) && sizeof($_POST['votes']) <= $pollInfo['optionsPerUser']) {
					foreach ($_POST['votes'] as $vote) if (in_array($vote, $validOptions)) $validVotes .= "({$currentUser->userID}, $vote, NOW()), ";
				} elseif (intval($_POST['votes']) && in_array($_POST['votes'], $validOptions)) $validVotes .= "({$currentUser->userID}, {$_POST['votes']}, NOW()), ";

				if (strlen($validVotes)) {
					if ($pollInfo['allowRevoting']) $mysql->query("DELETE FROM forums_pollVotes WHERE userID = {$currentUser->userID} AND pollOptionID IN (".implode(', ', $validOptions).")");
					$mysql->query('INSERT INTO forums_pollVotes (userID, pollOptionID, votedOn) VALUES '.substr($validVotes, 0, -2));
				}
			}
		}
	}
	
	header("Location: /forums/thread/{$threadID}");
?>