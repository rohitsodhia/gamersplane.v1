<?
	include(FILEROOT.'/includes/forums/ForumPermissions.class.php');

	$threadID = intval($_POST['threadID']);
	
	$forumID = $mysql->query('SELECT forumID FROM threads WHERE threadID = '.$threadID);
	$forumID = $forumID->fetchColumn();
	
	$permissions = ForumPermissions::getPermissions($currentUser->userID, $forumID, 'moderate');

	if ($permissions[$forumID]['moderate']) {
		$action = '';
		if (isset($_POST['lock'])) $action = 'lock';
		elseif (isset($_POST['sticky'])) $action = 'sticky';
		elseif (isset($_POST['action']) && in_array($_POST['action'], array('lock', 'sticky', 'move'))) $action = $_POST['action'];
		
		if ($action == 'lock') $mysql->query('UPDATE threads SET locked = locked ^ 1 WHERE threadID = '.$threadID);
		elseif ($action == 'sticky') $mysql->query('UPDATE threads SET sticky = sticky ^ 1 WHERE threadID = '.$threadID);
		elseif ($action == 'move') { header("Location: /forums/moveThread/{$threadID}/"); exit; }
	}
	
	header("Location: /forums/thread/{$threadID}/");
?>