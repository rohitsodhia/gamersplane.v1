<?
	$userID = intval($_SESSION['userID']);
	$threadID = intval($_POST['threadID']);
	
	$forumID = $mysql->query('SELECT forumID FROM threads WHERE threadID = '.$threadID);
	$forumID = $forumID->fetchColumn();
	
	$permissions = retrievePermissions($userID, $forumID, 'read', TRUE);
	
	if ($permissions['read']) {
		$pollInfo = $mysql->query("SELECT optionsPerUser, allowRevoting FROM forums_polls WHERE threadID = $threadID");
		if ($pollInfo->rowCount()) {
			$pollInfo = $pollInfo->fetch();
			$checkVoted = $mysql->query("SELECT pv.pollOptionID FROM forums_pollVotes pv, forums_pollOptions po WHERE po.threadID = $threadID AND po.pollOptionID = pv.pollOptionID AND pv.userID = $userID");
			if ($checkVoted->rowCount() > 0 && !$pollInfo['allowRevoting']) {
			} elseif ($checkVoted->rowCount() == 0 || $pollInfo['allowRevoting']) {
				$pollOptions = $mysql->query("SELECT pollOptionID FROM forums_pollOptions WHERE threadID = $threadID");
				$validOptions = array();
				foreach ($pollOptions as $optionInfo) $validOptions[] = $optionInfo['pollOptionID'];

				$validVotes = '';
				if (is_array($_POST['votes']) && sizeof($_POST['votes']) <= $pollInfo['optionsPerUser']) {
					foreach ($_POST['votes'] as $vote) if (in_array($vote, $validOptions)) $validVotes .= "($userID, $vote, NOW()), ";
				} elseif (intval($_POST['votes']) && in_array($_POST['votes'], $validOptions)) $validVotes .= "($userID, {$_POST['votes']}, NOW()), ";

				if (strlen($validVotes)) {
					if ($pollInfo['allowRevoting']) $mysql->query("DELETE FROM forums_pollVotes WHERE userID = $userID AND pollOptionID IN (".implode(', ', $validOptions).")");
					$mysql->query('INSERT INTO forums_pollVotes (userID, pollOptionID, votedOn) VALUES '.substr($validVotes, 0, -2));
				}
			}
		}
	}
	
	header('Location: /forums/thread/'.$threadID);
?>