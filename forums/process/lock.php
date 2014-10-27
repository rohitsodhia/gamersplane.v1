<?
	$userID = intval($_SESSION['userID']);
	$threadID = intval($_GET['id']);
	
	$forumID = $mysql->query('SELECT forumID FROM threads WHERE threadID = '.$threadID);
	$forumID = $forumID->fetchColumn();
	
	$permissions = retrievePermissions($userID, $forumID, 'moderate', TRUE);
	
	if ($permissions['moderate']) $mysql->query('UPDATE threads SET locked = locked ^ 1 WHERE threadID = '.$threadID);
	
	header('Location: /forums/thread/'.$threadID);
?>