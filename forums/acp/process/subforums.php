<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$isAdmin = $mysql->query("SELECT f.forumID, p.forumID, fa.forumID FROM forums f, forums p, forumAdmins fa WHERE fa.userID = 1 AND fa.forumID = p.forumID AND f.heritage LIKE CONCAT(p.heritage, '%') AND f.forumID = $forumID");
	if (!$isAdmin->rowCount()) { header('Location: /forums/'); exit; }
	
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
		$forumInfo = $mysql->query('SELECT heritage, gameID FROM forums WHERE forumID = '.$pForumID);
		list($baseHeritage, $gameID) = $forumInfo->fetch(PDO::FETCH_NUM);
		if (!$gameID) $gameID = 'NULL';
		$numForums = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$pForumID);
		$numForums = $numForums->fetchColumn();
		$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, `order`, gameID) VALUES (:title, $pForumID, :order, $gameID)");
		$addForum->bindValue(':title', sanitizeString($_POST['newForum']));
		$addForum->bindValue(':order', intval($numForums + 1));
		$addForum->execute();
		$forumID = $mysql->lastInsertId();
		$mysql->query('UPDATE forums SET heritage = "'.$baseHeritage.'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
		$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
		
		header('Location: /forums/acp/'.$pForumID.'/subforums/');
	} else header('Location: /forums/');
?>