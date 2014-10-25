<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	$gameInfo = $mysql->query('SELECT heritage, gameID FROM forums WHERE forumID = '.$forumID);
	list($heritage, $gameID) = $gameInfo->fetch();
	$oHeritage = $heritage;
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: /forums/'); exit; }
	
	if (isset($_POST['addForum'])) {
		$numRows = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$forumID);
		$numRows = $numRows->fetchColumn();
		$addForum = $mysql->prepare("INSERT INTO forums (title, parentID, `order`) VALUES (:title, {$forumID}, :order)");
		$addForum->bindValue(':title', sanitizeString($_POST['newForum']));
		$addForum->bindValue(':order', intval($numRows + 1));
		$addForum->execute();
		$forumID = $mysql->lastInsertId();
		$mysql->query('UPDATE forums SET heritage = "'.$oHeritage.'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
		$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
		
		header('Location: /forums/acp/'.$forumID);
	} else header('Location: /forums/');
?>