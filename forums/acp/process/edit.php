<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	$forumInfo = $mysql->query('SELECT title, description, heritage FROM forums WHERE forumID = '.$forumID);
	list($title, $description, $heritage) = $forumInfo->fetch();
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	$toDo = '';
	$actionKey = '';
	if (isset($_POST['update'])) {
		$newTitle = isset($_POST['title'])?sanatizeString($_POST['title']):$title;
		$newDesc = sanatizeString($_POST['description']);
		
		if ($newTitle != $title || $newDesc != $description) $mysql->query('UPDATE forums SET title = "'.$newTitle.'", description = "'.$newDesc.'" WHERE forumID = '.$forumID);
		
		header('Location: '.SITEROOT.'/forums/acp/'.$forumID);
	} else header('Location: '.SITEROOT.'/forums/');
?>