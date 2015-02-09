<?
	addPackage('forum');
	$threadID = intval($_POST['threadID']);
	$forumID = $mysql->query("SELECT forumID FROM threads WHERE threadID = {$threadID}")->fetchColumn();
	$permissions = $permissions = ForumPermissions::getPermissions($currentUser->userID, $forumID, 'write');

	if ($permissions[$forumID]['write']) {
		try {
			$poll = new ForumPoll($threadID);
		} catch (Exception $e) { header("Location: /forums/thread/{$threadID}"); exit; }
		if (sizeof($poll->getVotesCast()) == 0 || $poll->getAllowRevoting()) {
			$validVotes = array();
			if (is_array($_POST['votes']) && sizeof($_POST['votes']) <= $poll->getOptionsPerUser()) {
				foreach ($_POST['votes'] as $vote) {
					if (in_array($vote, $poll->getOptions())) $validVotes[] = $vote;
				}
			} elseif (intval($_POST['votes']) && $poll->getOptions(intval($_POST['votes']))) $validVotes[] = $_POST['votes'];

			if (sizeof($validVotes)) $poll->addVotes($validVotes);
		}
	}
	
	header("Location: /forums/thread/{$threadID}/");
?>