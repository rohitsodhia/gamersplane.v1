<?
	addPackage('forum');
	$forumID = intval($_POST['forumID']);
	$forumManager = new ForumManager($forumID, ForumManager::NO_NEWPOSTS|ForumManager::NO_CHILDREN|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin') || $forum->getParentID() == 2) { header('Location: /forums/'); exit; }
	
	$toDo = '';
	$actionKey = '';
	if (isset($_POST['update'])) {
		$newTitle = isset($_POST['title'])?sanitizeString($_POST['title']):$title;
		$newDesc = sanitizeString($_POST['description']);
		
		if ($newTitle != $title || $newDesc != $description) {
			$updateForum = $mysql->prepare("UPDATE forums SET title = :newTitle, description = :newDesc WHERE forumID = $forumID");
			$updateForum->bindValue(':newTitle', $newTitle);
			$updateForum->bindValue(':newDesc', $newDesc);
			$updateForum->execute();
		}
		
		header('Location: /forums/acp/'.$forumID.'/');
	} else header('Location: /forums/');
?>