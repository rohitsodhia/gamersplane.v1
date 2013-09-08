<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$forumID = intval($_POST['forumID']);
	
	$adminForums = $mysql->query('SELECT forumID FROM forumAdmins WHERE userID = '.$userID);
	$temp = array();
	foreach ($adminForums as $aForumID) $temp[] = $aForumID['forumID'];
	$adminForums = $temp;
	$forumInfo = $mysql->query('SELECT parentID, heritage FROM forums WHERE forumID = '.$forumID);
	list($parentID, $heritage) = $forumInfo->fetch();
	$oHeritage = $heritage;
	$heritage = explode('-', $heritage);
	foreach ($heritage as $key => $hForumID) $heritage[$key] = intval($hForumID);
	
	if (!(in_array(0, $adminForums) || array_intersect($adminForums, $heritage))) { header('Location: '.SITEROOT.'/forums/'); exit; }
	
	if (isset($_POST['delete'])) {
		$forums = $mysql->query('SELECT children.forumID FROM forums AS parent, forums AS children WHERE parent.forumID = '.$forumID.' AND children.heritage LIKE CONCAT(parent.heritage, "%")');
		$temp = array();
		while (list($cForumID) = $forums->fetchColumn()) $temp[] = $cForumID;
		$forums = $temp
		
		header('Location: '.SITEROOT.'/forums/'.$parentID);
	} else header('Location: '.SITEROOT.'/forums/');
?>