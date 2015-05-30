<?
	addPackage('forum');
	$forumID = intval($_POST['forumID']);
	$forumManager = new ForumManager($forumID, ForumManager::NO_NEWPOSTS|ForumManager::NO_CHILDREN|ForumManager::ADMIN_FORUMS);
	$forum = $forumManager->forums[$forumID];
	if (!$forum->getPermissions('admin')) { header('Location: /forums/'); exit; }
	
	$toDo = '';
	$actionKey = '';
	$forumNames = array();
	foreach ($_POST as $key => $value) {
		$parts = explode('_', $key);
		if ($parts[0] == 'moveUp') {
			$toDo = 'move';
			$direction = 'up';
			$actionKey = $parts[1];
			break;
		} elseif ($parts[0] == 'moveDown') {
			$toDo = 'move';
			$direction = 'down';
			$actionKey = $parts[1];
			break;
		} elseif ($key == 'addForum') {
			$toDo = 'new';
		}
	}
	
	if ($toDo == 'move') {
		$forumCount = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$forumID);
		$forumCount = $forumCount->fetchColumn();
		$order = $mysql->query('SELECT `order` FROM forums WHERE forumID = '.$actionKey);
		$order = $order->fetchColumn();
		if ($direction == 'up' && $order != 1) {
			$oldPosition = $order;
			$newPosition = $order - 1;
		} elseif ($direction == 'down' && $order != $forumCount) {
			$oldPosition = $order;
			$newPosition = $order + 1;
		}
		$mysql->query("UPDATE forums SET `order` = IF(`order` = $oldPosition, $newPosition, $oldPosition) WHERE `order` IN ($oldPosition, $newPosition) AND parentID = $forumID");
		
		header("Location: /forums/acp/{$forumID}/subforums/");
	} elseif ($toDo == 'new') {
		$pForumID = $forumID;
		$gameID = $forum->isGameForum()?$forum->getGameID():'NULL';
		$numForums = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$pForumID)->fetchColumn();
		$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, heritage, `order`, gameID) VALUES (:title, $pForumID, :heritage, :order, $gameID)");
		$addForum->bindValue(':title', sanitizeString($_POST['newForum']));
		$addForum->bindValue(':heritage', $forum->getHeritage(true).'-');
		$addForum->bindValue(':order', intval($numForums + 1));
		$addForum->execute();
		$forumID = $mysql->lastInsertId();
		$mysql->query('UPDATE forums SET heritage = "'.$forum->getHeritage(true).'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
		$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
		if ($gameID != 'NULL') {
			$groupID = $mysql->query("SELECT groupID FROM games WHERE gameID = {$gameID}")->fetchColumn();
			$mysql->query("INSERT INTO forums_permissions_groups (groupID, forumID) VALUES ({$groupID}, {$forumID})");
		}
		
		header('Location: /forums/acp/'.$pForumID.'/subforums/');
	} else header('Location: /forums/');
?>