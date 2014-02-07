<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	$heritage = $mysql->query('SELECT heritage FROM forums WHERE forumID = '.$forumID);
	$heritage = $heritage->fetchColumn();
	$oHeritage = $heritage;
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	if (isset($_POST['addForum'])) {
		$numRows = $mysql->query('SELECT COUNT(forumID) FROM forums WHERE parentID = '.$forumID);
		$numRows = $numRows->fetchColumn();
		$mysql->query('INSERT INTO forums (title, parentID, `order`) VALUES ("'.sanitizeString($_POST['newForum']).'", '.$forumID.', '.intval($numRows + 1).')');
		$forumID = $mysql->lastInsertId();
		$mysql->query('UPDATE forums SET heritage = "'.$oHeritage.'-'.sql_forumIDPad($forumID).'" WHERE forumID = '.$forumID);
		$mysql->query('INSERT INTO forums_permissions_general (forumID) VALUES ('.$forumID.')');
		
		header('Location: '.SITEROOT.'/forums/acp/'.$forumID);
	} else header('Location: '.SITEROOT.'/forums/');
?>