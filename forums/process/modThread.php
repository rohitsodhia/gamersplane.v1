<?
	$loggedIn = checkLogin();
	
	$userID = intval($_SESSION['userID']);
	$threadID = intval($_POST['threadID']);
	
	$forumID = $mysql->query('SELECT forumID FROM threads WHERE threadID = '.$threadID);
	$forumID = $forumID->fetchColumn();
	
	$permissions = retrievePermissions($userID, $forumID, 'moderate', TRUE);
	
	if ($permissions['moderate']) {
		$action = '';
		if (isset($_POST['lock'])) $action = 'lock';
		elseif (isset($_POST['sticky'])) $action = 'sticky';
		elseif (isset($_POST['action']) && in_array($_POST['action'], array('lock', 'sticky', 'move'))) $action = $_POST['action'];
		
		if ($action == 'lock') $mysql->query('UPDATE threads SET locked = locked ^ 1 WHERE threadID = '.$threadID);
		elseif ($action == 'sticky') $mysql->query('UPDATE threads SET sticky = sticky ^ 1 WHERE threadID = '.$threadID);
		elseif ($action == 'move') { header('Location: '.SITEROOT.'/forums/moveThread/'.$threadID); exit; }
	}
	
	header('Location: '.SITEROOT.'/forums/thread/'.$threadID);
?>