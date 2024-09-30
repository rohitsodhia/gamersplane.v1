<?
	if (isset($_GET['forumID']) || isset($_GET['threadID'])) {
		$type = isset($_GET['forumID'])?'f':'t';
		$id = isset($_GET['forumID'])?intval($_GET['forumID']):intval($_GET['threadID']);
		if ($id != 0) {
			$checkSub = $mysql->query("SELECT userID FROM forumSubs WHERE userID = {$currentUser->userID} AND `type` = '{$type}' AND ID = {$id}");
			if ($checkSub->rowCount())
				$mysql->query("DELETE FROM forumSubs WHERE userID = {$currentUser->userID} AND `type` = '{$type}' AND ID = {$id}");
			else
				$mysql->query("INSERT INTO forumSubs SET userID = {$currentUser->userID}, `type` = '{$type}', ID = {$id}");
		}

		if ($type == 'f')
			header("Location: /forums/{$id}/");
		else
			header("Location: /forums/thread/{$id}/");
	} else
		header('Location: /forums/')
?>
