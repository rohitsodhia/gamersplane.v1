<?
	if (isset($_POST['add'])) {
		addPackage('forum');
		$threadID = intval($_POST['threadID']);
		if (!$threadID) { header('Location: /forums'); exit; }
		
		$threadManager = new ThreadManager($threadID);
		if ($threadManager->getPermissions('admin') == false) { header('Location: /403'); exit; }

		$cForumID = $mysql->query("SELECT forumID FROM threads WHERE threadID = {$threadID}");
		$destinationID = intval($_POST['forumID']);
		$mysql->query("UPDATE threads SET forumID = $destinationID WHERE threadID = {$threadID}");
		$mysql->query("UPDATE forums SET threadCount = threadCount - 1 WHERE forumID = {$cForumID}");
		$mysql->query("UPDATE forums SET threadCount = threadCount + 1 WHERE forumID = {$destinationID}");

		header("Location: /forums/thread/{$threadID}/");
	} else 
		header('Location: /forums/');
?>