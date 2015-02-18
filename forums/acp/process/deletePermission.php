<?
	addPackage('forum');
	$forumID = intval($_POST['forumID']);
	$forumManager = new ForumManager($forumID, ForumManager::NO_NEWPOSTS|ForumManager::NO_CHILDREN|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin') || $forum->isGameForum() || (!$pType && !isset($_POST['save']))) { header('Location: /forums/'); exit; }

	$pType = in_array($_POST['pType'], array('group', 'user'))?$_POST['pType']:NULL;
	$typeID = intval($_POST['typeID']);
	
	if (isset($_POST['delete'])) {
		if ($pType == 'group') $mysql->query("DELETE FROM forums_permissions_groups WHERE groupID = $typeID");
		else $typeInfo = $mysql->query("DELETE FROM forums_permissions_users WHERE userID = $typeID");
		echo 1;
	} else echo 0;
?>