<?
	addPackage('forum');
	$forumID = intval($_POST['forumID']);
	$forumManager = new ForumManager($forumID, ForumManager::NO_NEWPOSTS|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin')) { header('Location: /forums/'); exit; }
	
	if (isset($_POST['addForum'])) {
		$numRows = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$forumID);
		$numRows = $numRows->fetchColumn();
		$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, `order`) VALUES (:title, {$forumID}, :order)");
		$addForum->bindValue(':title', sanitizeString($_POST['newForum']));
		$addForum->bindValue(':order', intval($numRows + 1));
		$addForum->execute();
		$forumID = $mysql->lastInsertId();
		$mysql->query('UPDATE forums SET heritage = "'.$forum->getHeritage(true).'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
		$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
		
		header('Location: /forums/acp/'.$forumID.'/');
	} else header('Location: /forums/');
?>