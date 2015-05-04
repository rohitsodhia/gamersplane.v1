<?
	if (isset($_POST['add'])) {
		addPackage('forum');
		$threadID = intval($_POST['threadID']);
		if (!$threadID) { header('Location: /forums'); exit; }
		
		$threadManager = new ThreadManager($threadID);
		if ($threadManager->getPermissions('admin') == false) { header('Location: /403'); exit; }

		$destinationID = intval($_POST['forumID']);
		$mysql->query("UPDATE threads SET forumID = $destinationID WHERE threadID = $threadID");
		
		header("Location: /forums/thread/{$threadID}/");
	} else 
		header('Location: /forums/');
?>