<?
	$threadID = intval($_GET['id']);
	
	$forumID = $mysql->query('SELECT forumID FROM threads WHERE threadID = '.$threadID);
	$forumID = $forumID->fetchColumn();
	
	$permissions = retrievePermissions($currentUser->userID, $forumID, 'moderate', TRUE);
	
	if ($permissions['moderate']) $mysql->query('UPDATE threads SET sticky = sticky ^ 1 WHERE threadID = '.$threadID);
	
	header('Location: /forums/thread/'.$threadID);
?>