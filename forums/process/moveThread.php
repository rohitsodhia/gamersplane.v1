<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$threadID = intval($_POST['threadID']);
	
	if (isset($_POST['add'])) {
		$forumInfo = $mysql->query('SELECT forums.forumID, forums.heritage FROM threads, forums WHERE forums.forumID = threads.forumID AND threads.threadID = '.$threadID);
		list($forumID, $heritage) = $forumInfo->fetch(PDO::FETCH_NUM);
		$heritage = explode('-', $heritage);
		foreach ($heritage as $key => $value) $heritage[$key] = intval($value);
		$adminCheck = $mysql->query("SELECT forumID FROM forumAdmins WHERE userID = $userID AND forumID IN (0, 2, {$heritage[1]})");
		if (!$adminCheck->rowCount() || !in_array(2, $heritage)) { header('Location: '.SITEROOT.'/forums'); exit; }
		
		$destinationID = $_POST['destinationID'];
		$mysql->query("UPDATE threads SET forumID = $destinationID WHERE threadID = $threadID");
		$mysql->query("UPDATE forums_readData SET threadData = REPLACE(threadData, 'i:{$threadID};a:3:{s:7:\"forumID\";s:2:\"{$forumID}\";', 'i:{$threadID};a:3:{s:7:\"forumID\";s:2:\"{$destinationID}\";') WHERE threadData LIKE '%i:{$threadID};a:3:{s:7:\"forumID\";s:2:\"{$forumID}\";%'");
		
		header('Location: '.SITEROOT.'/forums/thread/'.$threadID);
	} else header('Location: '.SITEROOT.'/forums');
?>